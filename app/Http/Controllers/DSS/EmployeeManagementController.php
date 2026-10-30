<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\SaaS\Campus;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class EmployeeManagementController extends Controller
{
    /**
     * Display Employee Directory (Master Institutional Entity List)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = false;

        // Metrics Summary
        $totalEmployees = Employee::where('tenant_id', $tenant->id)->count();
        $withUserCount = Employee::where('tenant_id', $tenant->id)->whereHas('user')->count();
        $withoutUserCount = Employee::where('tenant_id', $tenant->id)->whereDoesntHave('user')->count();
        $activeEmployeesCount = Employee::where('tenant_id', $tenant->id)->where('employment_status', 'Active')->count();
        $inactiveEmployeesCount = Employee::where('tenant_id', $tenant->id)->where('employment_status', '!=', 'Active')->count();

        // Dropdowns for Modals & Filter
        $campuses = Campus::where('tenant_id', $tenant->id)->get();
        $departments = Unit::where('tenant_id', $tenant->id)->orderBy('unit_name')->get();
        $designations = Designation::where('tenant_id', $tenant->id)->where('status', 'active')->orderBy('name')->get();

        $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name')->get();

        // Suggested next employee number
        $nextSeq = $totalEmployees + 1;
        $suggestedEmpNo = 'EMP-'.date('Y').'-'.str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        return view('dss.settings.employees.index', compact(
            'tenant',
            'institution',
            'theme',
            'totalEmployees',
            'withUserCount',
            'withoutUserCount',
            'activeEmployeesCount',
            'inactiveEmployeesCount',
            'campuses',
            'departments',
            'designations',
            'roles',
            'suggestedEmpNo',
            'isSuperAdmin'
        ));
    }

    /**
     * Pure Yajra DataTables server-side feed for Master Employee Entity Directory.
     */
    public function data(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $isSuperAdmin = false;

        // Base query with tenant scope and relations
        $query = Employee::where('tenant_id', $tenant->id)
            ->with([
                'user.roles',
                'campus',
                'department',
                'designations.campus',
                'designations.department',
            ]);

        // Custom filters from DataTables payload
        $campusId = $request->filled('campus_id') ? (int) $request->input('campus_id') : null;
        $deptId = $request->filled('department_id') ? (int) $request->input('department_id') : null;
        $empType = trim((string) $request->input('employment_type', ''));
        $empStatus = trim((string) $request->input('employment_status', ''));
        $accountStatus = trim((string) $request->input('account_status', ''));
        $preset = trim((string) ($request->input('filter') ?? ''));

        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        if ($deptId) {
            $query->where(function ($q) use ($deptId) {
                $q->where('department_id', $deptId)
                    ->orWhereHas('designations', fn ($dq) => $dq->where('department_id', $deptId));
            });
        }

        if ($empType !== '') {
            $query->where('employment_type', $empType);
        }

        if ($empStatus !== '') {
            $query->where('employment_status', $empStatus);
        }

        if ($accountStatus === 'with_user') {
            $query->whereHas('user');
        } elseif ($accountStatus === 'without_user') {
            $query->whereDoesntHave('user');
        }

        if ($preset === 'with_user') {
            $query->whereHas('user');
        } elseif ($preset === 'without_user') {
            $query->whereDoesntHave('user');
        } elseif ($preset === 'active') {
            $query->where('employment_status', 'Active');
        } elseif ($preset === 'on_leave') {
            $query->where('employment_status', 'On Leave');
        } elseif ($preset === 'inactive') {
            $query->whereIn('employment_status', ['Inactive', 'Retired', 'Separated']);
        } elseif ($preset === 'permanent') {
            $query->where('employment_type', 'Permanent');
        }

        $avatarColors = ['#0284c7', '#059669', '#d97706', '#7c3aed', '#dc2626', '#475569'];

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                $search = trim((string) ($request->input('search.value') ?? $request->input('search') ?? ''));
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('employee_no', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('designation', 'like', "%{$search}%")
                            ->orWhereHas('designations', function ($dq) use ($search) {
                                $dq->where('designation', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->orderColumn('name_info', function ($query, $order) {
                $query->orderBy('last_name', $order)->orderBy('first_name', $order);
            })
            ->orderColumn('designation_status', function ($query, $order) {
                $query->orderBy('designation', $order);
            })
            ->orderColumn('campus_unit', function ($query, $order) {
                $query->orderBy('department_id', $order);
            })
            ->orderColumn('contact', function ($query, $order) {
                $query->orderBy('email', $order);
            })
            ->addColumn('actions', function ($emp) use ($isSuperAdmin) {
                $encryptedId = $emp->encrypted_id;
                $fullName = $emp->full_name;
                $escapedJsName = addslashes($fullName);
                $hasUser = (bool) $emp->user;
                $userEmail = $hasUser ? $emp->user->email : $emp->email;
                $escapedJsDesig = addslashes($emp->designation ?? '');

                $showUrl = route('dss.settings.employees.show', $encryptedId);
                $editUrl = route('dss.settings.employees.edit', $encryptedId);

                $html = '<div class="d-flex align-items-center gap-1">';
                $html .= '<a href="'.$showUrl.'" class="btn btn-sm btn-light border shadow-2xs px-2 py-1" title="View 360° Dossier Profile"><i class="mdi mdi-card-account-details-outline text-primary"></i></a>';

                if (! $isSuperAdmin) {
                    $html .= '<a href="'.$editUrl.'" class="btn btn-sm btn-white border shadow-2xs text-dark px-2 py-1" title="Edit Employee Profile"><i class="mdi mdi-pencil-outline text-dark"></i></a>';

                    if (! $hasUser) {
                        $html .= '<button class="btn btn-sm btn-primary-subtle text-primary border border-primary-subtle shadow-2xs fw-bold px-2 py-1 font-mono d-flex align-items-center gap-1" style="font-size: 11px;" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->email).'\', \''.$escapedJsDesig.'\')" title="Generate User Account for this Employee"><i class="mdi mdi-key-plus"></i><span class="d-none d-xl-inline">Gen</span></button>';
                    } else {
                        $roleName = e($emp->user->role ?? '');
                        $html .= '<button class="btn btn-sm btn-light border shadow-2xs text-primary px-2 py-1" style="font-size: 11px;" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->user->email).'\', \''.$escapedJsDesig.'\', true, \''.$roleName.'\')" title="Reset Password & Issue Slip ('.e($emp->user->email).')"><i class="mdi mdi-lock-reset"></i></button>';
                    }

                    $html .= '<div class="dropdown">';
                    $html .= '<button class="btn btn-sm btn-light border p-1 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Operations"><i class="mdi mdi-dots-vertical"></i></button>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 12.5px;">';
                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2" href="'.$showUrl.'"><i class="mdi mdi-eye-outline text-primary"></i><span>View 360° Dossier</span></a></li>';
                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2" href="'.$editUrl.'"><i class="mdi mdi-pencil-outline text-secondary"></i><span>Edit Personnel Profile</span></a></li>';
                    $html .= '<li><hr class="dropdown-divider"></li>';
                    $html .= '<li class="dropdown-header text-uppercase extra-small fw-bold">Quick Status Switch</li>';

                    $activeCls = $emp->employment_status === 'Active' ? 'active' : '';
                    $leaveCls = $emp->employment_status === 'On Leave' ? 'active' : '';
                    $inactiveCls = $emp->employment_status === 'Inactive' ? 'active' : '';

                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 '.$activeCls.'" href="javascript:void(0)" onclick="quickToggleStatus(\''.$encryptedId.'\', \'Active\')"><span class="status-dot status-dot-active"></span><span>Set Active</span></a></li>';
                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 '.$leaveCls.'" href="javascript:void(0)" onclick="quickToggleStatus(\''.$encryptedId.'\', \'On Leave\')"><span class="status-dot status-dot-leave"></span><span>Set On Leave</span></a></li>';
                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 '.$inactiveCls.'" href="javascript:void(0)" onclick="quickToggleStatus(\''.$encryptedId.'\', \'Inactive\')"><span class="status-dot status-dot-inactive"></span><span>Set Inactive</span></a></li>';
                    $html .= '<li><hr class="dropdown-divider"></li>';

                    if ($hasUser) {
                        $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 text-primary" href="javascript:void(0)" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->user->email).'\', \''.$escapedJsDesig.'\')"><i class="mdi mdi-key-change"></i><span>Re-issue Credentials &amp; Slip</span></a></li>';
                        $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 text-warning" href="javascript:void(0)" onclick="revokeUserAccess(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->user->email).'\')"><i class="mdi mdi-link-variant-off"></i><span>Unlink / Revoke User Access</span></a></li>';
                    } else {
                        $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 text-primary" href="javascript:void(0)" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->email).'\', \''.$escapedJsDesig.'\')"><i class="mdi mdi-key-plus"></i><span>Generate User Credentials</span></a></li>';
                    }

                    $html .= '<li><hr class="dropdown-divider"></li>';
                    $hasUserJs = $hasUser ? 'true' : 'false';
                    $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="confirmDeleteEmployee(\''.$encryptedId.'\', \''.$escapedJsName.'\', '.$hasUserJs.')"><i class="mdi mdi-archive-arrow-down-outline"></i><span>Archive Personnel Record</span></a></li>';
                    $html .= '</ul></div>';
                }
                $html .= '</div>';

                return $html;
            })
            ->addColumn('name_info', function ($emp) use ($avatarColors) {
                $encryptedId = $emp->encrypted_id;
                $safeFullName = e($emp->full_name);
                $showUrl = route('dss.settings.employees.show', $encryptedId);
                $initials = strtoupper(substr($emp->first_name ?? '', 0, 1).substr($emp->last_name ?? '', 0, 1));
                $avatarBg = $avatarColors[$emp->id % count($avatarColors)];
                $hireDateStr = $emp->date_hired ? '&bull; Hired: '.Carbon::parse($emp->date_hired)->format('M Y') : '';
                $genderStr = $emp->gender ? '<span>'.e($emp->gender).'</span>' : '';

                $html = '<div class="d-flex align-items-center gap-2.5">';
                $html .= '<div class="emp-avatar text-white shadow-2xs" style="background-color: '.$avatarBg.';">'.$initials.'</div>';
                $html .= '<div class="min-w-0">';
                $html .= '<a href="'.$showUrl.'" class="fw-bold text-dark text-decoration-none text-truncate d-block" title="View Dossier: '.$safeFullName.'">'.$safeFullName.'</a>';
                $html .= '<div class="font-mono text-muted extra-small d-flex align-items-center gap-1.5 flex-wrap mt-0.5">';
                $html .= '<span class="badge bg-light text-dark border font-mono px-1.5 py-0.5" style="font-size: 10px;">'.e($emp->employee_no).'</span>';
                $html .= $genderStr;
                $html .= $hireDateStr ? '<span>'.$hireDateStr.'</span>' : '';
                $html .= '</div></div></div>';

                return $html;
            })
            ->addColumn('designation_status', function ($emp) {
                $primaryDesig = $emp->designations->firstWhere('is_primary', true) ?? $emp->designations->first();
                $concurrentDesigs = $emp->designations->where('id', '!=', $primaryDesig?->id);

                $html = '';
                if ($primaryDesig) {
                    $html .= '<div class="fw-bold text-dark d-flex align-items-center gap-1.5 flex-wrap"><span>'.e($primaryDesig->designation).'</span><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1 py-0.2" style="font-size: 8.5px;">PRIMARY</span></div>';
                } else {
                    $html .= '<div class="fw-semibold text-dark">'.e($emp->designation ?: 'Staff Member').'</div>';
                }

                if ($concurrentDesigs->isNotEmpty()) {
                    $html .= '<div class="mt-1 pt-1 border-top border-dashed d-flex flex-column gap-1">';
                    foreach ($concurrentDesigs as $cd) {
                        $html .= '<div class="d-flex align-items-center gap-1 extra-small" style="font-size: 10.5px;"><span class="badge bg-secondary-subtle text-secondary border px-1 py-0.2 flex-shrink-0" style="font-size: 8px;">CONCURRENT</span><span class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="'.e($cd->designation).'">'.e($cd->designation).'</span></div>';
                    }
                    $html .= '</div>';
                }

                $typeBadge = match ($emp->employment_type) {
                    'Permanent' => 'bg-success-subtle text-success border-success-subtle',
                    'Contractual' => 'bg-info-subtle text-info border-info-subtle',
                    'Part-time' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                    'Casual', 'Job Order' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                    default => 'bg-light text-dark border',
                };
                $statusDotClass = match ($emp->employment_status) {
                    'Active' => 'status-dot-active',
                    'On Leave' => 'status-dot-leave',
                    default => 'status-dot-inactive',
                };

                $html .= '<div class="d-flex align-items-center gap-1.5 mt-2">';
                $html .= '<span class="badge '.$typeBadge.' border px-1.5 py-0.5" style="font-size: 10px;">'.e($emp->employment_type ?? 'Permanent').'</span>';
                $html .= '<span class="badge bg-light text-dark border px-1.5 py-0.5 d-inline-flex align-items-center gap-1" style="font-size: 9.5px;"><span class="status-dot '.$statusDotClass.'"></span><span id="emp-status-badge-'.$emp->id.'">'.e($emp->employment_status ?? 'Active').'</span></span>';
                $html .= '</div>';

                return $html;
            })
            ->addColumn('campus_unit', function ($emp) {
                $primaryDesig = $emp->designations->firstWhere('is_primary', true) ?? $emp->designations->first();
                $concurrentDesigs = $emp->designations->where('id', '!=', $primaryDesig?->id);

                $html = '';
                if ($primaryDesig && $primaryDesig->department) {
                    $html .= '<div class="fw-semibold text-dark text-truncate" style="max-width: 210px;" title="'.e($primaryDesig->department->unit_name).'"><i class="mdi mdi-domain text-primary me-1"></i>'.e($primaryDesig->department->unit_name).'</div>';
                    $html .= '<div class="text-muted extra-small text-truncate" style="max-width: 210px;"><i class="mdi mdi-map-marker-outline text-muted me-0.5"></i>'.e($primaryDesig->campus?->campus_name ?? 'Main Campus').'</div>';
                } elseif ($emp->department) {
                    $html .= '<div class="fw-semibold text-dark text-truncate" style="max-width: 210px;" title="'.e($emp->department->unit_name).'"><i class="mdi mdi-domain text-primary me-1"></i>'.e($emp->department->unit_name).'</div>';
                    $html .= '<div class="text-muted extra-small text-truncate" style="max-width: 210px;"><i class="mdi mdi-map-marker-outline text-muted me-0.5"></i>'.e($emp->campus?->campus_name ?? 'Main Campus').'</div>';
                } else {
                    $html .= '<div class="text-muted extra-small font-italic">Unassigned Unit</div>';
                }

                if ($concurrentDesigs->isNotEmpty()) {
                    $otherDepts = $concurrentDesigs->map(fn ($c) => $c->department)->filter()->unique('id');
                    if ($otherDepts->isNotEmpty()) {
                        $html .= '<div class="mt-1 pt-1 border-top border-dashed d-flex flex-column gap-0.5">';
                        foreach ($otherDepts as $od) {
                            $html .= '<div class="extra-small text-muted text-truncate" style="font-size: 10.5px; max-width: 210px;" title="'.e($od->unit_name).'"><i class="mdi mdi-office-building-outline text-secondary me-0.5"></i>'.e($od->unit_name).'</div>';
                        }
                        $html .= '</div>';
                    }
                }

                return $html;
            })
            ->addColumn('contact', function ($emp) {
                $html = '<div class="font-mono text-dark text-truncate d-flex align-items-center gap-1" style="max-width: 200px;"><i class="mdi mdi-email-outline text-muted"></i><a href="mailto:'.e($emp->email).'" class="text-dark text-decoration-none text-truncate" title="'.e($emp->email).'">'.e($emp->email).'</a></div>';
                if ($emp->phone_number) {
                    $html .= '<div class="font-mono text-muted extra-small mt-0.5"><i class="mdi mdi-phone-outline text-muted me-1"></i>'.e($emp->phone_number).'</div>';
                }

                return $html;
            })
            ->addColumn('account', function ($emp) use ($isSuperAdmin) {
                $encryptedId = $emp->encrypted_id;
                $fullName = $emp->full_name;
                $escapedJsName = addslashes($fullName);
                $hasUser = (bool) $emp->user;
                $escapedJsDesig = addslashes($emp->designation ?? '');

                $html = '';
                if ($hasUser) {
                    $html .= '<div class="d-flex flex-column gap-1">';
                    $html .= '<div class="d-flex align-items-center gap-1.5"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-semibold" style="font-size: 11px;"><i class="mdi mdi-check-decagram me-1"></i>User Linked</span><span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">'.e($emp->user->role ?? 'User').'</span></div>';
                    $html .= '<div class="font-mono text-muted extra-small text-truncate" style="max-width: 210px;" title="'.e($emp->user->email).'"><i class="mdi mdi-account-key-outline me-1"></i>'.e($emp->user->email).'</div>';
                    $html .= '<div class="d-flex align-items-center justify-content-between flex-wrap gap-1"><div class="extra-small text-muted d-flex align-items-center gap-1" style="font-size: 10px;"><i class="mdi mdi-google text-danger"></i><span>Google SSO</span></div>';
                    if (! $isSuperAdmin) {
                        $html .= '<a href="javascript:void(0)" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->user->email).'\', \''.$escapedJsDesig.'\', true, \''.e($emp->user->role ?? '').'\')" class="extra-small text-primary fw-semibold text-decoration-none d-inline-flex align-items-center gap-0.5" style="font-size: 10.5px;"><i class="mdi mdi-lock-reset"></i>Reset Pass</a>';
                    }
                    $html .= '</div></div>';
                } else {
                    $html .= '<div class="d-flex flex-column gap-1">';
                    $html .= '<span class="badge bg-light text-secondary border px-2 py-1 font-mono fw-normal" style="font-size: 11px; width: fit-content;"><i class="mdi mdi-account-cancel-outline me-1"></i>No User Account</span>';
                    $html .= '<span class="text-muted extra-small" style="font-size: 10.5px;">Institutional entity only</span>';
                    if (! $isSuperAdmin) {
                        $html .= '<a href="javascript:void(0)" onclick="openGenerateUserModal(\''.$encryptedId.'\', \''.$escapedJsName.'\', \''.e($emp->employee_no).'\', \''.e($emp->email).'\', \''.$escapedJsDesig.'\')" class="extra-small text-primary fw-semibold text-decoration-none" style="font-size: 11px;"><i class="mdi mdi-plus-circle-outline me-0.5"></i>Generate access</a>';
                    }
                    $html .= '</div>';
                }

                return $html;
            })
            ->setRowId(fn ($emp) => 'emp-row-'.$emp->id)
            ->rawColumns(['actions', 'name_info', 'designation_status', 'campus_unit', 'contact', 'account'])
            ->toJson();
    }

    /**
     * Show the dedicated form for creating a new Employee (Separate Page)
     */
    public function create(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = false;

        $totalEmployees = Employee::where('tenant_id', $tenant->id)->count();
        $suggestedEmpNo = 'EMP-'.date('Y').'-'.str_pad($totalEmployees + 1, 4, '0', STR_PAD_LEFT);

        $campuses = Campus::where('tenant_id', $tenant->id)->get();
        $departments = Unit::where('tenant_id', $tenant->id)->orderBy('unit_name')->get();
        $designations = Designation::where('tenant_id', $tenant->id)->where('status', 'active')->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name')->get();

        return view('dss.settings.employees.create', compact(
            'tenant',
            'institution',
            'theme',
            'campuses',
            'departments',
            'designations',
            'roles',
            'suggestedEmpNo',
            'isSuperAdmin'
        ));
    }

    /**
     * Display a dedicated 360° Employee CRM Profile & Dossier (Separate Page)
     */
    public function show(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = false;

        $employee = Employee::where('tenant_id', $tenant->id)
            ->with(['user.roles', 'campus', 'department', 'designations.campus', 'designations.department', 'designations.designationModel'])
            ->findOrFail($id);

        $primaryDesig = $employee->designations->firstWhere('is_primary', true) ?? $employee->designations->first();
        $concurrentDesigs = $employee->designations->where('id', '!=', $primaryDesig?->id);

        $tenure = null;
        if ($employee->date_hired) {
            $diff = now()->diff($employee->date_hired);
            $years = $diff->y;
            $months = $diff->m;
            $parts = [];
            if ($years > 0) {
                $parts[] = $years.' '.Str::plural('yr', $years);
            }
            if ($months > 0) {
                $parts[] = $months.' '.Str::plural('mo', $months);
            }
            $tenure = ! empty($parts) ? implode(' ', $parts) : 'Less than a month';
        }

        $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name')->get();

        return view('dss.settings.employees.show', compact(
            'tenant',
            'institution',
            'theme',
            'employee',
            'primaryDesig',
            'concurrentDesigs',
            'tenure',
            'roles',
            'isSuperAdmin'
        ));
    }

    /**
     * Show the dedicated form for editing an Employee (Separate Page)
     */
    public function edit(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = false;

        $employee = Employee::where('tenant_id', $tenant->id)
            ->with(['user.roles', 'campus', 'department', 'designations.campus', 'designations.department', 'designations.designationModel'])
            ->findOrFail($id);

        $campuses = Campus::where('tenant_id', $tenant->id)->get();
        $departments = Unit::where('tenant_id', $tenant->id)->orderBy('unit_name')->get();
        $designations = Designation::where('tenant_id', $tenant->id)->where('status', 'active')->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name')->get();

        return view('dss.settings.employees.edit', compact(
            'tenant',
            'institution',
            'theme',
            'employee',
            'campuses',
            'departments',
            'designations',
            'roles',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created Employee (Tenant Admin only)
     */
    public function store(Request $request)
    {

        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'employee_no' => 'required|string|max:50|unique:employees,employee_no,NULL,id,tenant_id,'.$tenant->id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'suffix' => 'nullable|string|max:20',
            'email' => 'required|email|max:150',
            'phone_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Male,Female,Other',
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'department_id' => 'nullable|exists:units,id',
            'designation' => 'required|string|max:150',
            'employment_type' => 'required|string|in:Permanent,Contractual,Temporary,Casual,Job Order,Part-time',
            'employment_status' => 'required|string|in:Active,On Leave,Inactive',
            'date_hired' => 'nullable|date',
            // Multiple Designations
            'designations' => 'nullable|array',
            'designations.*.campus_id' => 'nullable',
            'designations.*.department_id' => 'nullable',
            'designations.*.designation' => 'nullable|string|max:150',
            'designations.*.is_primary' => 'nullable',
            // Optional immediate user account creation
            'create_user_account' => 'nullable|boolean',
            'user_email' => 'nullable|required_if:create_user_account,1|email|unique:users,email',
            'user_role' => 'nullable|required_if:create_user_account,1|string',
            'user_password' => 'nullable|string|min:6',
        ]);

        $employee = Employee::create([
            'tenant_id' => $tenant->id,
            'employee_no' => trim($validated['employee_no']),
            'first_name' => trim($validated['first_name']),
            'middle_name' => trim($validated['middle_name'] ?? ''),
            'last_name' => trim($validated['last_name']),
            'suffix' => trim($validated['suffix'] ?? ''),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => trim($validated['phone_number'] ?? ''),
            'gender' => $validated['gender'] ?? 'Other',
            'campus_id' => $validated['campus_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'designation' => trim($validated['designation']),
            'employment_type' => $validated['employment_type'],
            'employment_status' => $validated['employment_status'],
            'date_hired' => $validated['date_hired'] ?? now()->format('Y-m-d'),
        ]);

        // Process User Account if requested
        $user = null;
        $accountCreatedMsg = '';
        if (! empty($validated['create_user_account']) && ! empty($validated['user_email'])) {
            $userEmail = strtolower(trim($validated['user_email']));
            $password = ! empty($validated['user_password']) ? $validated['user_password'] : Str::random(16);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $employee->id,
                'campus_id' => $employee->campus_id,
                'dept_id' => $employee->department_id,
                'name' => $employee->full_name,
                'email' => $userEmail,
                'password' => Hash::make($password),
                'role' => $validated['user_role'] ?? 'Staff',
                'designation' => $employee->designation,
                'phone_number' => $employee->phone_number,
                'status' => 'active',
            ]);

            $role = Role::where('name', $validated['user_role'] ?? 'Staff')->first();
            if ($role) {
                $user->assignRole($role);
            }

            $accountCreatedMsg = " System user account also generated ({$userEmail}) ready for Google SSO or password login.";
        }

        // Process Multiple Designations & Units (Linked to Master Designations Repository)
        $rawDesignations = $request->input('designations', []);
        $processedDesignations = [];
        $primaryDesigModel = null;
        $primaryDesigTitle = null;

        if (! empty($rawDesignations) && is_array($rawDesignations)) {
            $hasPrimary = false;
            foreach ($rawDesignations as $d) {
                $desigTitle = trim($d['designation'] ?? '');
                $desigId = ! empty($d['designation_id']) ? (int) $d['designation_id'] : null;

                $desigModel = null;
                if ($desigId) {
                    $desigModel = Designation::where('tenant_id', $tenant->id)->find($desigId);
                    if ($desigModel && empty($desigTitle)) {
                        $desigTitle = $desigModel->name;
                    }
                } elseif (! empty($desigTitle)) {
                    $desigModel = Designation::firstOrCreate(
                        ['tenant_id' => $tenant->id, 'name' => $desigTitle],
                        ['category' => 'Administrative', 'status' => 'active']
                    );
                }

                if (empty($desigTitle) && $desigModel) {
                    $desigTitle = $desigModel->name;
                }

                if (empty($desigTitle)) {
                    continue;
                }

                $isPri = (! empty($d['is_primary']) || ! $hasPrimary);
                if ($isPri && ! $hasPrimary) {
                    $hasPrimary = true;
                    $primaryDesigModel = $desigModel;
                    $primaryDesigTitle = $desigTitle;
                } else {
                    $isPri = false;
                }

                $processedDesignations[] = [
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'user_id' => $user?->id,
                    'campus_id' => ! empty($d['campus_id']) ? (int) $d['campus_id'] : $employee->campus_id,
                    'department_id' => ! empty($d['department_id']) ? (int) $d['department_id'] : $employee->department_id,
                    'designation_id' => $desigModel?->id,
                    'designation' => $desigTitle,
                    'is_primary' => $isPri,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Default primary if no designations provided
        if (empty($processedDesignations)) {
            $defaultModel = Designation::where('tenant_id', $tenant->id)->firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $employee->designation ?: 'Staff Member'],
                ['category' => 'Administrative', 'status' => 'active']
            );
            $processedDesignations[] = [
                'tenant_id' => $tenant->id,
                'employee_id' => $employee->id,
                'user_id' => $user?->id,
                'campus_id' => $employee->campus_id,
                'department_id' => $employee->department_id,
                'designation_id' => $defaultModel->id,
                'designation' => $defaultModel->name,
                'is_primary' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $primaryDesigModel = $defaultModel;
            $primaryDesigTitle = $defaultModel->name;
        }

        EmployeeDesignation::insert($processedDesignations);

        if ($primaryDesigModel || $primaryDesigTitle) {
            $employee->update([
                'designation_id' => $primaryDesigModel?->id,
                'designation' => $primaryDesigTitle ?? $primaryDesigModel?->name,
            ]);
        }

        return redirect()->route('dss.settings.employees.index')
            ->with('success', "Employee [{$employee->employee_no}] {$employee->full_name} registered successfully.{$accountCreatedMsg}");
    }

    /**
     * Update an existing Employee (Tenant Admin only)
     */
    public function update(Request $request, $id)
    {

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'employee_no' => 'required|string|max:50|unique:employees,employee_no,'.$employee->id.',id,tenant_id,'.$tenant->id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'suffix' => 'nullable|string|max:20',
            'email' => 'required|email|max:150',
            'phone_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Male,Female,Other',
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'department_id' => 'nullable|exists:units,id',
            'designation' => 'required|string|max:150',
            'employment_type' => 'required|string|in:Permanent,Contractual,Temporary,Casual,Job Order,Part-time',
            'employment_status' => 'required|string|in:Active,On Leave,Inactive',
            'date_hired' => 'nullable|date',
            // Multiple Designations
            'designations' => 'nullable|array',
            'designations.*.campus_id' => 'nullable',
            'designations.*.department_id' => 'nullable',
            'designations.*.designation' => 'nullable|string|max:150',
            'designations.*.is_primary' => 'nullable',
        ]);

        $employee->update([
            'employee_no' => trim($validated['employee_no']),
            'first_name' => trim($validated['first_name']),
            'middle_name' => trim($validated['middle_name'] ?? ''),
            'last_name' => trim($validated['last_name']),
            'suffix' => trim($validated['suffix'] ?? ''),
            'email' => strtolower(trim($validated['email'])),
            'phone_number' => trim($validated['phone_number'] ?? ''),
            'gender' => $validated['gender'] ?? 'Other',
            'campus_id' => $validated['campus_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'designation' => trim($validated['designation']),
            'employment_type' => $validated['employment_type'],
            'employment_status' => $validated['employment_status'],
            'date_hired' => $validated['date_hired'] ?? $employee->date_hired,
        ]);

        // Sync Multiple Designations & Offices (Linked to Master Designations Repository)
        $rawDesignations = $request->input('designations', []);
        if (! empty($rawDesignations) && is_array($rawDesignations)) {
            EmployeeDesignation::where('employee_id', $employee->id)->delete();
            $newRows = [];
            $hasPrimary = false;
            $primaryRecord = null;
            $primaryDesigModel = null;

            foreach ($rawDesignations as $d) {
                $desigTitle = trim($d['designation'] ?? '');
                $desigId = ! empty($d['designation_id']) ? (int) $d['designation_id'] : null;

                $desigModel = null;
                if ($desigId) {
                    $desigModel = Designation::where('tenant_id', $tenant->id)->find($desigId);
                    if ($desigModel && empty($desigTitle)) {
                        $desigTitle = $desigModel->name;
                    }
                } elseif (! empty($desigTitle)) {
                    $desigModel = Designation::firstOrCreate(
                        ['tenant_id' => $tenant->id, 'name' => $desigTitle],
                        ['category' => 'Administrative', 'status' => 'active']
                    );
                }

                if (empty($desigTitle) && $desigModel) {
                    $desigTitle = $desigModel->name;
                }

                if (empty($desigTitle)) {
                    continue;
                }

                $isPri = (! empty($d['is_primary']) || ! $hasPrimary);
                if ($isPri && ! $hasPrimary) {
                    $hasPrimary = true;
                    $primaryRecord = $d;
                    $primaryDesigModel = $desigModel;
                } else {
                    $isPri = false;
                }

                $newRows[] = [
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'user_id' => $employee->user?->id,
                    'campus_id' => ! empty($d['campus_id']) ? (int) $d['campus_id'] : $employee->campus_id,
                    'department_id' => ! empty($d['department_id']) ? (int) $d['department_id'] : $employee->department_id,
                    'designation_id' => $desigModel?->id,
                    'designation' => $desigTitle,
                    'is_primary' => $isPri,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($newRows)) {
                EmployeeDesignation::insert($newRows);

                if ($primaryRecord) {
                    $employee->update([
                        'designation_id' => $primaryDesigModel?->id,
                        'designation' => $primaryDesigModel?->name ?? trim($primaryRecord['designation']),
                        'campus_id' => ! empty($primaryRecord['campus_id']) ? (int) $primaryRecord['campus_id'] : $employee->campus_id,
                        'department_id' => ! empty($primaryRecord['department_id']) ? (int) $primaryRecord['department_id'] : $employee->department_id,
                    ]);
                }
            }
        } else {
            // Ensure at least primary exists
            $defaultModel = Designation::where('tenant_id', $tenant->id)->firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $employee->designation ?: 'Staff Member'],
                ['category' => 'Administrative', 'status' => 'active']
            );
            EmployeeDesignation::updateOrCreate(
                ['employee_id' => $employee->id, 'is_primary' => true],
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $employee->user?->id,
                    'campus_id' => $employee->campus_id,
                    'department_id' => $employee->department_id,
                    'designation_id' => $defaultModel->id,
                    'designation' => $defaultModel->name,
                    'status' => 'active',
                ]
            );
            $employee->update(['designation_id' => $defaultModel->id]);
        }

        // Sync linked user profile details if user exists
        if ($employee->user) {
            $employee->user->update([
                'name' => $employee->full_name,
                'campus_id' => $employee->campus_id,
                'dept_id' => $employee->department_id,
                'designation' => $employee->designation,
                'phone_number' => $employee->phone_number,
            ]);

            // Sync user_id on all employee designations
            EmployeeDesignation::where('employee_id', $employee->id)
                ->whereNull('user_id')
                ->update(['user_id' => $employee->user->id]);
        }

        return redirect()->route('dss.settings.employees.index')
            ->with('success', "Employee record [{$employee->employee_no}] updated successfully.");
    }

    /**
     * Generate User Access for an Existing Employee (Tenant Admin only)
     * Allows custom institutional email so employee can sign in with Google Workspace OAuth
     */
    public function generateUserAccess(Request $request, $id)
    {

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)
            ->with(['campus', 'department', 'user'])
            ->findOrFail($id);

        $validated = $request->validate([
            'user_email' => 'required|email|max:150',
            'role' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);

        if (strtolower($validated['role']) === 'superadmin') {
            $msg = 'The SuperAdmin role is exclusively restricted to the sovereign platform owner (apiloton1@snsu.edu.ph).';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['result' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('error', $msg);
        }

        $customEmail = strtolower(trim($validated['user_email']));
        // Generate secure temporary password if not provided
        $clearPassword = ! empty($validated['password'])
            ? $validated['password']
            : rand(1000, 9999).Str::upper(Str::random(3));

        // Check if employee already has a linked user account
        $linkedUser = $employee->user;

        if ($linkedUser) {
            // Check if changing email to another employee's existing account
            $emailCollision = User::where('email', $customEmail)
                ->where('id', '!=', $linkedUser->id)
                ->first();

            if ($emailCollision) {
                $otherEmp = $emailCollision->employee_id ? Employee::find($emailCollision->employee_id) : null;
                $msg = "The institutional email [{$customEmail}] is already assigned to another account".($otherEmp ? " ({$otherEmp->full_name} - {$otherEmp->employee_no})" : '').'.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['result' => false, 'message' => $msg], 422);
                }

                return redirect()->back()->with('error', $msg);
            }

            // Update existing user credentials and role
            $linkedUser->update([
                'name' => $employee->full_name,
                'email' => $customEmail,
                'role' => $validated['role'],
                'password' => Hash::make($clearPassword),
                'status' => 'active',
                'campus_id' => $employee->campus_id,
                'dept_id' => $employee->department_id,
                'designation' => $employee->designation,
                'phone_number' => $employee->phone_number,
            ]);

            $user = $linkedUser;
        } else {
            // Employee does not yet have a linked user account
            $existingUser = User::where('email', $customEmail)->first();

            if ($existingUser) {
                // Check if already linked to a different employee
                if ($existingUser->employee_id && $existingUser->employee_id !== $employee->id) {
                    $otherEmp = Employee::find($existingUser->employee_id);
                    $msg = "The institutional email [{$customEmail}] is already assigned to another employee: {$otherEmp?->full_name} ({$otherEmp?->employee_no}).";
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['result' => false, 'message' => $msg], 422);
                    }

                    return redirect()->back()->with('error', $msg);
                }

                // Re-link and activate existing unlinked user account for this employee entity
                $existingUser->update([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'campus_id' => $employee->campus_id,
                    'dept_id' => $employee->department_id,
                    'name' => $employee->full_name,
                    'role' => $validated['role'],
                    'designation' => $employee->designation,
                    'phone_number' => $employee->phone_number,
                    'status' => 'active',
                    'password' => Hash::make($clearPassword),
                ]);

                $user = $existingUser;
            } else {
                // Create new sovereign user account
                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'campus_id' => $employee->campus_id,
                    'dept_id' => $employee->department_id,
                    'name' => $employee->full_name,
                    'email' => $customEmail,
                    'password' => Hash::make($clearPassword),
                    'role' => $validated['role'],
                    'designation' => $employee->designation,
                    'phone_number' => $employee->phone_number,
                    'status' => 'active',
                ]);
            }
        }

        // Sync Spatie role
        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->syncRoles([$role]);
        }

        // Link all designations to this user account
        EmployeeDesignation::where('employee_id', $employee->id)
            ->update(['user_id' => $user->id]);

        $successMsg = "User account successfully provisioned for {$employee->full_name} ({$user->email}) with role [{$user->role}]. Google Workspace SSO is ready.";

        $credentialsPayload = [
            'name' => $employee->full_name,
            'employee_no' => $employee->employee_no,
            'designation' => $employee->designation ?: 'Staff Member',
            'campus' => $employee->campus?->campus_name ?? 'Main Campus',
            'department' => $employee->department?->unit_name ?? 'General Administration',
            'email' => $user->email,
            'password' => $clearPassword,
            'role' => $user->role,
            'login_url' => url('/login'),
            'google_sso' => true,
            'issued_at' => now()->format('M d, Y h:i A'),
            'issued_by' => auth()->user()?->name ?? 'System Administrator',
            'institution_name' => $tenant->name ?? config('app.name', 'IIPM Decision Support System'),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => $successMsg,
                'credentials' => $credentialsPayload,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                ],
            ]);
        }

        return redirect()->back()
            ->with('success', $successMsg)
            ->with('credentials', $credentialsPayload);
    }

    /**
     * Export Employee Directory to CSV (CRM Feature)
     */
    public function exportCsv(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $query = Employee::where('tenant_id', $tenant->id)
            ->with(['user.roles', 'campus', 'department', 'designations.campus', 'designations.department']);

        // Apply any active filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }
        if ($campusId = $request->input('campus_id')) {
            $query->where('campus_id', $campusId);
        }
        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }
        if ($empType = $request->input('employment_type')) {
            $query->where('employment_type', $empType);
        }
        if ($empStatus = $request->input('employment_status')) {
            $query->where('employment_status', $empStatus);
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->get();

        $filename = 'employees_export_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($employees) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, [
                'Employee Number',
                'Full Name',
                'Last Name',
                'First Name',
                'Middle Name',
                'Suffix',
                'Gender',
                'Institutional Email',
                'Phone Number',
                'Campus',
                'Primary Office / Department',
                'Primary Designation',
                'Concurrent Designations',
                'Employment Type',
                'Employment Status',
                'Date Hired',
                'Linked User Account',
                'User Role',
            ]);

            foreach ($employees as $emp) {
                $primary = $emp->designations->firstWhere('is_primary', true) ?? $emp->designations->first();
                $concurrent = $emp->designations->where('id', '!=', $primary?->id)->pluck('designation')->implode('; ');

                fputcsv($handle, [
                    $emp->employee_no,
                    $emp->full_name,
                    $emp->last_name,
                    $emp->first_name,
                    $emp->middle_name,
                    $emp->suffix,
                    $emp->gender,
                    $emp->email,
                    $emp->phone_number,
                    $primary?->campus?->campus_name ?? $emp->campus?->campus_name ?? 'Main Campus',
                    $primary?->department?->unit_name ?? $emp->department?->unit_name ?? 'Unassigned',
                    $primary?->designation ?? $emp->designation,
                    $concurrent,
                    $emp->employment_type,
                    $emp->employment_status,
                    $emp->date_hired ? $emp->date_hired->format('Y-m-d') : '',
                    $emp->user ? $emp->user->email : 'No Account',
                    $emp->user ? $emp->user->role : 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get single employee JSON with rich 360 CRM profile details for AJAX modals
     */
    public function showJson($id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)
            ->with(['user.roles', 'campus', 'department', 'designations.campus', 'designations.department'])
            ->findOrFail($id);

        $primaryDesig = $employee->designations->firstWhere('is_primary', true) ?? $employee->designations->first();
        $concurrent = $employee->designations->where('id', '!=', $primaryDesig?->id)->values();

        $tenure = null;
        if ($employee->date_hired) {
            $diff = now()->diff($employee->date_hired);
            $years = $diff->y;
            $months = $diff->m;
            $parts = [];
            if ($years > 0) {
                $parts[] = $years.' '.Str::plural('yr', $years);
            }
            if ($months > 0) {
                $parts[] = $months.' '.Str::plural('mo', $months);
            }
            $tenure = ! empty($parts) ? implode(' ', $parts) : 'Less than a month';
        }

        return response()->json([
            'result' => true,
            'employee' => $employee,
            'primary_designation' => $primaryDesig,
            'concurrent_designations' => $concurrent,
            'designations' => $employee->designations,
            'campus_name' => $primaryDesig?->campus?->campus_name ?? $employee->campus?->campus_name ?? 'Main Campus',
            'department_name' => $primaryDesig?->department?->unit_name ?? $employee->department?->unit_name ?? 'Unassigned Office',
            'date_hired_formatted' => $employee->date_hired ? $employee->date_hired->format('M d, Y') : null,
            'tenure' => $tenure,
            'has_user' => (bool) $employee->user,
            'user' => $employee->user ? [
                'id' => $employee->user->id,
                'name' => $employee->user->name,
                'email' => $employee->user->email,
                'role' => $employee->user->role,
                'status' => $employee->user->status ?? 'active',
                'created_at' => $employee->user->created_at?->format('M d, Y h:i A'),
            ] : null,
            'user_email' => $employee->user?->email,
            'user_role' => $employee->user?->role,
        ]);
    }

    /**
     * Soft delete / Archive Employee record (Tenant Admin only)
     */
    public function destroy(Request $request, $id)
    {

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)->findOrFail($id);

        // If employee has user account
        if ($employee->user()->exists()) {
            if ($request->boolean('force_detach_user')) {
                // Safely detach user account before deleting employee
                $user = $employee->user;
                $user->update(['employee_id' => null, 'status' => 'inactive']);
                EmployeeDesignation::where('employee_id', $employee->id)->update(['user_id' => null]);
            } else {
                $msg = "Cannot delete employee {$employee->full_name} because they have an active linked user account ({$employee->user->email}). Unlink the user account first or confirm detachment.";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['result' => false, 'message' => $msg, 'has_user' => true], 422);
                }

                return redirect()->back()->with('error', $msg);
            }
        }

        $empName = $employee->full_name;
        $empNo = $employee->employee_no;
        $employee->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => "Employee record [{$empNo}] {$empName} archived successfully.",
            ]);
        }

        return redirect()->route('dss.settings.employees.index')
            ->with('success', "Employee record [{$empNo}] {$empName} archived successfully.");
    }

    /**
     * Revoke / Unlink User Account from Employee Entity (CRM Feature)
     */
    public function revokeUserAccess(Request $request, $id)
    {

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)->with('user')->findOrFail($id);

        if (! $employee->user) {
            $msg = "Employee {$employee->full_name} does not have a linked user account.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['result' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('warning', $msg);
        }

        $user = $employee->user;
        $userEmail = $user->email;

        // Unlink employee from user and set user to inactive
        $user->update(['employee_id' => null, 'status' => 'inactive']);
        EmployeeDesignation::where('employee_id', $employee->id)->update(['user_id' => null]);

        $msg = "User account ({$userEmail}) unlinked and deactivated for employee [{$employee->employee_no}] {$employee->full_name}.";
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['result' => true, 'message' => $msg]);
        }

        return redirect()->route('dss.settings.employees.index')->with('success', $msg);
    }

    /**
     * Quick Toggle or Update Employment Status (Active, On Leave, Inactive, etc.)
     */
    public function toggleStatus(Request $request, $id)
    {

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $employee = Employee::where('tenant_id', $tenant->id)->findOrFail($id);

        $status = $request->input('status');
        if (! in_array($status, ['Active', 'On Leave', 'Inactive', 'Retired', 'Separated'])) {
            return response()->json(['result' => false, 'message' => 'Invalid status supplied.'], 422);
        }

        $employee->update(['employment_status' => $status]);

        return response()->json([
            'result' => true,
            'status' => $status,
            'message' => "Employment status for {$employee->full_name} updated to {$status}.",
        ]);
    }
}
