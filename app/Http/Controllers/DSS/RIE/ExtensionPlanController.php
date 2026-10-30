<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Http\Requests\RIE\StoreExtensionPlanRequest;
use App\Http\Requests\RIE\UpdateExtensionPlanRequest;
use App\Models\Accreditation\AcademicProgram;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\SaaS\DocumentSignatory;
use App\Services\DSS\TenantContext;
use App\Services\DTS\SnsuTrackingService;
use App\Services\PDF\DocumentPdfGenerator;
use App\Services\RIE\ExtensionSignatoryResolver;
use App\Services\Signatories\CentralizedSignatoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExtensionPlanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        $baseQuery = ExtensionPlan::where('tenant_id', $tenantId);
        $totalMasterCount = (clone $baseQuery)->count();
        $myPlansCount = (clone $baseQuery)->where('prepared_by_user_id', $user?->id)->count();

        $activeQuery = clone $baseQuery;
        $activePlansCount = (clone $activeQuery)->count();
        $draftCount = (clone $activeQuery)->where('status', 'draft')->count();
        $submittedCount = (clone $activeQuery)->where('status', 'submitted')->count();
        $notedCount = (clone $activeQuery)->where('status', 'noted')->count();
        $approvedCount = (clone $activeQuery)->where('status', 'approved')->count();
        $isMyProposals = false;

        return view('dss.rie.extension_plans.index', compact(
            'isMyProposals',
            'totalMasterCount',
            'myPlansCount',
            'activePlansCount',
            'draftCount',
            'submittedCount',
            'notedCount',
            'approvedCount'
        ));
    }

    public function myPlans(Request $request)
    {
        if ($request->ajax()) {
            $request->merge(['my_plans' => 1]);

            return $this->data($request);
        }

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $baseQuery = ExtensionPlan::where('tenant_id', $tenantId);
        $totalMasterCount = (clone $baseQuery)->count();
        $myPlansCount = (clone $baseQuery)->where('prepared_by_user_id', $user?->id)->count();

        $activeQuery = (clone $baseQuery)->where('prepared_by_user_id', $user?->id);
        $activePlansCount = (clone $activeQuery)->count();
        $draftCount = (clone $activeQuery)->where('status', 'draft')->count();
        $submittedCount = (clone $activeQuery)->where('status', 'submitted')->count();
        $notedCount = (clone $activeQuery)->where('status', 'noted')->count();
        $approvedCount = (clone $activeQuery)->where('status', 'approved')->count();
        $isMyProposals = true;

        return view('dss.rie.extension_plans.index', compact(
            'isMyProposals',
            'totalMasterCount',
            'myPlansCount',
            'activePlansCount',
            'draftCount',
            'submittedCount',
            'notedCount',
            'approvedCount'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $query = ExtensionPlan::with(['campus', 'preparedByUser'])
            ->where('tenant_id', $tenantId);

        if ($request->filled('my_plans') && $request->get('my_plans') == 1) {
            $user = Auth::user();
            $query->where('prepared_by_user_id', $user?->id);
        }

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                if ($request->filled('status')) {
                    $query->where('status', $request->query('status'));
                }

                $search = $request->input('search.value') ?? $request->input('search');
                if (! empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('plan_code', 'like', "%{$search}%")
                            ->orWhere('extension_activity', 'like', "%{$search}%")
                            ->orWhere('program_name', 'like', "%{$search}%")
                            ->orWhere('prepared_by_name', 'like', "%{$search}%");
                    });
                }
            })
            ->addColumn('plan_code', function (ExtensionPlan $plan) {
                $key = $plan->getRouteKey();
                $url = route('dss.rie.plans.show', $key);
                $code = e($plan->plan_code);
                $date = $plan->created_at ? $plan->created_at->format('M d, Y') : '';

                return '
                    <a href="'.$url.'" class="fw-bold font-mono text-decoration-none text-primary" style="font-size: 12.5px; white-space: nowrap;">
                        '.$code.'
                    </a>
                    <div class="text-muted font-mono mt-0.5" style="font-size: 11px;">
                        '.$date.'
                    </div>
                ';
            })
            ->addColumn('activity_program', function (ExtensionPlan $plan) {
                $act = e($plan->extension_activity);
                $prog = e($plan->program_name);

                return '
                    <div class="fw-semibold text-dark" style="font-size: 13px; line-height: 1.35;">'.$act.'</div>
                    <div class="text-muted d-flex align-items-center gap-1 mt-0.5" style="font-size: 11.5px;">
                        <i class="mdi mdi-school-outline"></i>
                        <span>'.$prog.'</span>
                    </div>
                ';
            })
            ->addColumn('proponent', function (ExtensionPlan $plan) {
                $name = e($plan->prepared_by_name);

                return '
                    <div class="fw-semibold text-dark" style="font-size: 12px;">'.$name.'</div>
                    <div class="text-muted mt-1">
                        <span class="badge bg-light text-muted border" style="font-size: 10px; padding: 2px 6px;">Project Leader</span>
                    </div>
                ';
            })
            ->addColumn('budget_target', function (ExtensionPlan $plan) {
                $budget = e($plan->formatted_budget);
                $ben = (int) $plan->target_beneficiaries_count;
                $ext = (int) $plan->extensionists_count;

                return '
                    <div class="fw-bold text-success font-mono" style="font-size: 12px;">'.$budget.'</div>
                    <div class="text-muted mt-1" style="font-size: 11px;">
                        '.$ben.' Beneficiaries • '.$ext.' Extensionists
                    </div>
                ';
            })
            ->addColumn('dts_tracking', function (ExtensionPlan $plan) {
                if ($plan->tracking_number) {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1" style="font-size: 11px;" title="3rd-Party DTS Tracking Number"><i class="mdi mdi-barcode-scan me-1"></i>'.e($plan->tracking_number).'</span>';
                }

                return '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono px-2 py-1" style="font-size: 11px;" title="Tracking number needed before official PDF printing"><i class="mdi mdi-link-variant-off me-1"></i>No Tracking #</span>';
            })
            ->addColumn('status_badge', function (ExtensionPlan $plan) {
                $badge = $plan->status_badge;

                return '
                    <span class="badge '.($badge['class'] ?? 'bg-secondary').' rounded-pill px-2 py-1" style="font-size: 10px; letter-spacing: 0.3px;">
                        '.e($badge['label'] ?? $plan->status).'
                    </span>
                ';
            })
            ->addColumn('actions', function (ExtensionPlan $plan) {
                $key = $plan->getRouteKey();
                $url = route('dss.rie.plans.show', $key);
                $pdfLongExec = route('dss.rie.plans.pdf', ['id' => $key, 'paper' => '8.5x13', 'template' => 'executive']);
                $pdfLongIso = route('dss.rie.plans.pdf', ['id' => $key, 'paper' => '8.5x13', 'template' => 'iso_form']);
                $pdfA4Exec = route('dss.rie.plans.pdf', ['id' => $key, 'paper' => 'A4', 'template' => 'executive']);
                $pdfA4Iso = route('dss.rie.plans.pdf', ['id' => $key, 'paper' => 'A4', 'template' => 'iso_form']);

                $dtsItems = '';
                if ($plan->tracking_number) {
                    $trackUrl = route('document.track', $plan->tracking_number);
                    $dtsItems = '
                        <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-primary" href="'.$trackUrl.'" target="_blank"><i class="mdi mdi-radar fs-6"></i><span>Track in DTS ('.e($plan->tracking_number).')</span></a></li>
                    ';
                }

                return '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.$url.'" class="btn btn-sm btn-dss-primary px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1" title="Open Plan Workbench">
                            <i class="mdi mdi-eye-outline"></i>
                        </a>
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-light border text-dark px-2 py-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport" data-bs-popper-config=\'{"strategy":"fixed"}\' title="Operations & PDF Print">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-lg border extra-small p-1" style="min-width: 225px; z-index: 1065;">
                                <li class="dropdown-header extra-small text-uppercase fw-bold text-muted py-1 font-mono">Plan Operations</li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$url.'"><i class="mdi mdi-file-document-outline text-primary fs-6"></i><span>Plan Workbench</span></a></li>
                                '.($plan->canBeEditedBy(Auth::user()) ? '<li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-warning-emphasis" href="'.route('dss.rie.plans.edit', $key).'"><i class="mdi mdi-pencil-outline fs-6"></i><span>Edit Plan Details</span></a></li>' : '').'
                                '.($plan->canBeDeletedBy(Auth::user()) ? '<li><button type="button" class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-danger delete-plan-btn" data-id="'.$key.'" data-code="'.e($plan->plan_code).'"><i class="mdi mdi-trash-can-outline fs-6"></i><span>Delete Draft</span></button></li>' : '').'
                                '.$dtsItems.'
                                <li><hr class="dropdown-divider my-1"></li>
                                <li class="dropdown-header extra-small text-uppercase fw-bold text-muted py-1 font-mono">Official PDF Print</li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$pdfLongExec.'" target="_blank"><i class="mdi mdi-file-pdf-box text-danger fs-6"></i><span>Folio 8.5×13" Exec</span></a></li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$pdfLongIso.'" target="_blank"><i class="mdi mdi-clipboard-text-outline text-success fs-6"></i><span>Folio 8.5×13" ISO</span></a></li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$pdfA4Exec.'" target="_blank"><i class="mdi mdi-file-pdf-box text-danger fs-6"></i><span>A4 Executive</span></a></li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$pdfA4Iso.'" target="_blank"><i class="mdi mdi-clipboard-text-outline text-success fs-6"></i><span>A4 ISO / QMS</span></a></li>
                            </ul>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['plan_code', 'activity_program', 'proponent', 'budget_target', 'dts_tracking', 'status_badge', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();

        $projectLeaderName = $user->name;
        $projectLeaderDesignation = 'Project Leader';
        $projectLeaderEmployeeId = $user->employee_id;

        if ($user->employee) {
            $emp = $user->employee;
            $projectLeaderName = $emp->full_name;
        }

        $academicPrograms = AcademicProgram::with(['campus'])
            ->where('is_active', true)
            ->where(function ($q) use ($tenant) {
                if ($tenant) {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', $tenant->id);
                }
            })
            ->orderBy('program_name', 'asc')
            ->get();

        $defaultSignatories = ExtensionSignatoryResolver::resolveForPlan(null, null, $user);

        return view('dss.rie.extension_plans.create', compact(
            'institution',
            'user',
            'projectLeaderName',
            'projectLeaderDesignation',
            'projectLeaderEmployeeId',
            'academicPrograms',
            'defaultSignatories'
        ));
    }

    public function getSignatoriesByProgram(Request $request): JsonResponse
    {
        $programId = $request->query('program_id');
        $unitId = $request->query('unit_id');
        $resolved = ExtensionSignatoryResolver::resolveForPlan(
            $programId ? (int) $programId : null,
            $unitId ? (int) $unitId : null,
            Auth::user()
        );

        return response()->json([
            'success' => true,
            'college_name' => $resolved['college_name'],
            'college_code' => $resolved['college_code'],
            'signatories' => $resolved,
        ]);
    }

    public function store(StoreExtensionPlanRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $campusId = $user->campus_id ?? $tenant?->campuses()->first()?->id;

        $progId = ! empty($validated['academic_program_id']) ? (int) $validated['academic_program_id'] : null;
        $resolvedSignatories = ExtensionSignatoryResolver::resolveForPlan($progId, null, $user);

        $reviewedEmployeeId = $validated['reviewed_by_employee_id'] ?? $resolvedSignatories['reviewed']['employee_id'];
        $reviewedByName = $validated['reviewed_by_name'] ?? $resolvedSignatories['reviewed']['name'];
        $reviewedByDesignation = $validated['reviewed_by_designation'] ?? $resolvedSignatories['reviewed']['designation'];

        $notedEmployeeId = $validated['noted_by_employee_id'] ?? $resolvedSignatories['noted']['employee_id'];
        $notedByName = $validated['noted_by_name'] ?? $resolvedSignatories['noted']['name'];
        $notedByDesignation = $validated['noted_by_designation'] ?? $resolvedSignatories['noted']['designation'];

        $approvedEmployeeId = $validated['approved_by_employee_id'] ?? $notedEmployeeId;
        $approvedByName = $validated['approved_by_name'] ?? $notedByName;
        $approvedByDesignation = $validated['approved_by_designation'] ?? $notedByDesignation;

        $concurredEmployeeId = $validated['concurred_by_employee_id'] ?? $resolvedSignatories['concurred_1']['employee_id'];
        $concurredByName = $validated['concurred_by_name'] ?? $resolvedSignatories['concurred_1']['name'];
        $concurredByDesignation = $validated['concurred_by_designation'] ?? $resolvedSignatories['concurred_1']['designation'];

        $concurred2EmployeeId = $validated['concurred_by_2_employee_id'] ?? $resolvedSignatories['concurred_2']['employee_id'];
        $concurred2ByName = $validated['concurred_by_2_name'] ?? $resolvedSignatories['concurred_2']['name'];
        $concurred2ByDesignation = $validated['concurred_by_2_designation'] ?? $resolvedSignatories['concurred_2']['designation'];

        $notedEmployee = $notedEmployeeId ? Employee::with(['department'])->find($notedEmployeeId) : null;
        $approvedEmployee = $approvedEmployeeId ? Employee::with(['department'])->find($approvedEmployeeId) : null;
        $reviewedEmployee = $reviewedEmployeeId ? Employee::with(['department'])->find($reviewedEmployeeId) : null;

        $preparedByName = $user->name;
        $preparedByEmployeeId = $user->employee_id;
        if ($user->employee) {
            $preparedByName = $user->employee->full_name;
        }
        $preparedByDesignation = 'Project Leader';

        $actionType = $request->input('action_type', 'submit');
        $planStatus = $actionType === 'draft' ? 'draft' : 'submitted';

        $institution = TenantContext::getInstitution();
        $acronym = strtoupper(trim($institution?->school_acronym ?: 'DTS'));
        $year = (string) ($institution?->current_fy ?: date('Y'));

        DB::beginTransaction();
        try {

            $countThisYear = ExtensionPlan::where('tenant_id', $tenantId)
                ->whereYear('created_at', date('Y'))
                ->count() + 1;
            $planCode = sprintf('%s-EXT-%s-%04d', $acronym, $year, $countThisYear);

            $trackingNumber = ! empty($validated['tracking_number']) ? trim((string) $validated['tracking_number']) : null;

            $matMode = $validated['materials_arrival_mode'] ?? 'range';
            $matStart = $validated['materials_arrival_start'] ?? null;
            $matEnd = $validated['materials_arrival_end'] ?? $matStart;
            $matDates = null;
            if (! empty($validated['materials_arrival_dates'])) {
                $matDates = is_array($validated['materials_arrival_dates'])
                    ? $validated['materials_arrival_dates']
                    : array_filter(array_map('trim', explode(',', $validated['materials_arrival_dates'])));
            }

            $matText = $validated['materials_arrival_date'] ?? null;
            if (! $matText && $matStart) {
                if ($matMode === 'range' && $matEnd && $matEnd !== $matStart) {
                    $matText = date('F d, Y', strtotime($matStart)).' – '.date('F d, Y', strtotime($matEnd));
                } elseif ($matMode === 'multiple' && ! empty($matDates)) {
                    $matText = implode(', ', array_map(fn ($d) => date('M d, Y', strtotime($d)), $matDates));
                } else {
                    $matText = date('F d, Y', strtotime($matStart));
                }
            }

            $conductMode = $validated['conduct_activity_mode'] ?? 'range';
            $conductStart = $validated['conduct_activity_start'] ?? null;
            $conductEnd = $validated['conduct_activity_end'] ?? $conductStart;
            $conductDates = null;
            if (! empty($validated['conduct_activity_dates'])) {
                $conductDates = is_array($validated['conduct_activity_dates'])
                    ? $validated['conduct_activity_dates']
                    : array_filter(array_map('trim', explode(',', $validated['conduct_activity_dates'])));
            }

            $conductText = $validated['conduct_activity_date'] ?? null;
            if (! $conductText && $conductStart) {
                if ($conductMode === 'range' && $conductEnd && $conductEnd !== $conductStart) {
                    $conductText = date('F d, Y', strtotime($conductStart)).' – '.date('F d, Y', strtotime($conductEnd));
                } elseif ($conductMode === 'multiple' && ! empty($conductDates)) {
                    $conductText = implode(', ', array_map(fn ($d) => date('M d, Y', strtotime($d)), $conductDates));
                } else {
                    $conductText = date('F d, Y', strtotime($conductStart));
                }
            }

            $plan = ExtensionPlan::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'academic_program_id' => $validated['academic_program_id'] ?? null,
                'dts_record_id' => null,
                'plan_code' => $planCode,
                'tracking_number' => $trackingNumber,
                'program_name' => $validated['program_name'],
                'extension_activity' => $validated['extension_activity'],
                'objectives' => $validated['objectives'],
                'sub_extension_proposal_date' => $validated['sub_extension_proposal_date'] ?? null,
                'sub_activity_design_date' => $validated['sub_activity_design_date'] ?? null,
                'sub_purchase_request_date' => $validated['sub_purchase_request_date'] ?? null,
                'moa_signing_date' => $validated['moa_signing_date'] ?? null,
                'materials_arrival_mode' => $matMode,
                'materials_arrival_start' => $matStart,
                'materials_arrival_end' => $matEnd,
                'materials_arrival_dates' => $matDates,
                'materials_arrival_date' => $matText,
                'conduct_activity_mode' => $conductMode,
                'conduct_activity_start' => $conductStart,
                'conduct_activity_end' => $conductEnd,
                'conduct_activity_dates' => $conductDates,
                'conduct_activity_date' => $conductText,
                'sub_narrative_report_date' => $validated['sub_narrative_report_date'] ?? null,
                'monitoring_feedback_date' => $validated['monitoring_feedback_date'] ?? null,
                'monitoring_feedback_end_date' => $validated['monitoring_feedback_end_date'] ?? null,
                'beneficiaries' => $validated['beneficiaries'],
                'target_beneficiaries_count' => $validated['target_beneficiaries_count'],
                'proposed_budget' => $validated['proposed_budget'],
                'extensionists_count' => $validated['extensionists_count'],
                'prepared_by_user_id' => $user->id,
                'prepared_by_employee_id' => $preparedByEmployeeId,
                'prepared_by_name' => $preparedByName,
                'prepared_by_designation' => $preparedByDesignation,
                'prepared_at' => now(),
                'reviewed_by_user_id' => $resolvedSignatories['reviewed']['user_id'] ?? null,
                'reviewed_by_employee_id' => $reviewedEmployeeId,
                'reviewed_by_name' => $reviewedByName,
                'reviewed_by_designation' => $reviewedByDesignation,
                'noted_by_employee_id' => $notedEmployeeId,
                'noted_by_name' => $notedByName,
                'noted_by_designation' => $notedByDesignation,
                'approved_by_employee_id' => $approvedEmployeeId,
                'approved_by_name' => $approvedByName,
                'approved_by_designation' => $approvedByDesignation,
                'concurred_by_employee_id' => $concurredEmployeeId,
                'concurred_by_name' => $concurredByName,
                'concurred_by_designation' => $concurredByDesignation,
                'concurred_by_2_employee_id' => $concurred2EmployeeId,
                'concurred_by_2_name' => $concurred2ByName,
                'concurred_by_2_designation' => $concurred2ByDesignation,
                'status' => $planStatus,
            ]);

            try {
                CentralizedSignatoryService::instantiateForDocument(
                    $plan,
                    'Extension Plan',
                    $tenantId,
                    $campusId,
                    $user,
                    [],
                    true
                );
            } catch (\Throwable $sigEx) {
                report($sigEx);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Extension Plan created successfully.',
                    'plan_id' => $plan->id,
                    'redirect_url' => route('dss.rie.plans.show', $plan->getRouteKey()),
                ]);
            }

            return redirect()->route('dss.rie.plans.show', $plan->getRouteKey())
                ->with('success', "Extension Plan [{$plan->plan_code}] registered successfully. Document routed to Extension Head via DTS.");
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save Extension Plan: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error creating Extension Plan: '.$e->getMessage());
        }
    }

    public function show(Request $request, string $id): View
    {
        $planId = dss_decrypt_id($id);
        $activeTab = $request->query('tab', 'overview');
        $validTabs = ['overview', 'objectives', 'milestones', 'routing', 'pdf', 'attachments'];
        if (! in_array($activeTab, $validTabs)) {
            $activeTab = 'overview';
        }

        $relations = [
            'academicProgram.campus',
            'campus',
            'preparedByUser',
            'preparedByEmployee',
            'reviewedByEmployee',
            'notedByEmployee',
            'approvedByEmployee',
            'concurredByEmployee',
            'concurredBy2Employee',
            'uploadedDocuments.uploader',
            'uploadedDocuments.auditTrails',
            'physicallySignedDocument',
            'documentAuditTrails.user',
        ];
        if (Schema::hasTable('dts_records') && Schema::hasTable('dts_record_routes')) {
            $relations[] = 'dtsRecord.routes.unit';
        }

        $plan = ExtensionPlan::with($relations)->findOrFail($planId);

        if (empty($plan->reviewed_by_name) || empty($plan->concurred_by_name)) {
            $resolved = ExtensionSignatoryResolver::resolveForPlan($plan->academic_program_id, null, $plan->preparedByUser);
            $plan->update([
                'reviewed_by_employee_id' => $plan->reviewed_by_employee_id ?: $resolved['reviewed']['employee_id'],
                'reviewed_by_name' => $plan->reviewed_by_name ?: $resolved['reviewed']['name'],
                'reviewed_by_designation' => $plan->reviewed_by_designation ?: $resolved['reviewed']['designation'],
                'noted_by_employee_id' => $plan->noted_by_employee_id ?: $resolved['noted']['employee_id'],
                'noted_by_name' => $plan->noted_by_name ?: $resolved['noted']['name'],
                'noted_by_designation' => $plan->noted_by_designation ?: $resolved['noted']['designation'],
                'concurred_by_employee_id' => $plan->concurred_by_employee_id ?: $resolved['concurred_1']['employee_id'],
                'concurred_by_name' => $plan->concurred_by_name ?: $resolved['concurred_1']['name'],
                'concurred_by_designation' => $plan->concurred_by_designation ?: $resolved['concurred_1']['designation'],
                'concurred_by_2_employee_id' => $plan->concurred_by_2_employee_id ?: $resolved['concurred_2']['employee_id'],
                'concurred_by_2_name' => $plan->concurred_by_2_name ?: $resolved['concurred_2']['name'],
                'concurred_by_2_designation' => $plan->concurred_by_2_designation ?: $resolved['concurred_2']['designation'],
            ]);
            $plan->refresh();
        }

        $resolvedSignatories = ExtensionSignatoryResolver::resolveForPlan($plan->academic_program_id, null, $plan->preparedByUser);

        $institution = TenantContext::getInstitution();
        $auditTrails = $plan->documentAuditTrails()->with(['user', 'uploadedDocument'])->latest()->get();

        $trackingNumber = $plan->tracking_number ?? ($plan->plan_code ?? null);
        $snsuTracking = SnsuTrackingService::fetchTracking($trackingNumber);

        return view('dss.rie.extension_plans.show', compact('plan', 'institution', 'auditTrails', 'activeTab', 'snsuTracking', 'resolvedSignatories'));
    }

    public function pdf(Request $request, string $id): Response
    {
        $planId = dss_decrypt_id($id);
        $relations = [
            'academicProgram.campus',
            'campus',
            'preparedByUser',
            'preparedByEmployee',
            'notedByEmployee',
            'approvedByEmployee',
        ];
        if (Schema::hasTable('dts_records') && Schema::hasTable('dts_record_routes')) {
            $relations[] = 'dtsRecord.routes.unit';
        }

        $plan = ExtensionPlan::with($relations)->findOrFail($planId);

        $institution = TenantContext::getInstitution();
        $paper = $request->query('paper', 'A4');
        $template = $request->query('template', 'executive');
        $download = $request->boolean('download');

        $pdf = DocumentPdfGenerator::fromExtensionPlan($plan, $institution, $paper, $template);
        $prefix = $institution?->school_acronym ?: 'RIE';
        $fileName = "{$prefix}_Extension_Plan_{$plan->plan_code}.pdf";

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline')."; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function sign(Request $request, string $id): JsonResponse
    {
        $planId = dss_decrypt_id($id);
        $plan = ExtensionPlan::findOrFail($planId);
        $user = Auth::user();

        $validated = $request->validate([
            'tier_role' => 'required|string|max:50',
            'signature_img' => 'nullable|string',
            'signature_type' => 'nullable|string|in:draw,drawn,type,font,upload,typed',
            'remarks' => 'nullable|string|max:500',
        ]);

        $tierRole = $validated['tier_role'];

        $runtimeSig = DocumentSignatory::where('document_type', $plan->getMorphClass())
            ->where('document_id', $plan->id)
            ->where('tier_role', $tierRole)
            ->first();

        $canSign = false;
        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            $canSign = true;
        } elseif ($runtimeSig) {
            $canSign = $runtimeSig->canBeSignedBy($user);
        } else {
            $canSign = match ($tierRole) {
                'prepared' => ($plan->prepared_by_user_id === $user->id),
                'noted' => ($user->employee_id && $plan->noted_by_employee_id === $user->employee_id),
                'approved' => ($user->employee_id && $plan->approved_by_employee_id === $user->employee_id),
                default => false,
            };
        }

        if (! $canSign) {
            return response()->json([
                'success' => false,
                'message' => "You are not authorized to sign for the '{$tierRole}' tier of this Extension Plan.",
            ], 403);
        }

        $roleLabels = [
            'prepared' => 'Prepared by',
            'noted' => 'Noted by',
            'approved' => 'Approved by',
        ];

        $sigRecord = CentralizedSignatoryService::recordDigitalSignature(
            $plan,
            $tierRole,
            $user,
            [
                'role_label' => $roleLabels[$tierRole] ?? ucfirst($tierRole).' by',
                'signature_img' => $validated['signature_img'] ?? null,
                'signature_type' => $validated['signature_type'] ?? 'drawn',
                'remarks' => $validated['remarks'] ?? 'Verified and digitally signed.',
                'designation' => match ($tierRole) {
                    'prepared' => $plan->prepared_by_designation,
                    'noted' => $plan->noted_by_designation,
                    'approved' => $plan->approved_by_designation,
                    default => null,
                },
            ]
        );

        if ($tierRole === 'noted' && $plan->status === 'submitted') {
            $plan->status = 'noted';
            $plan->save();
        } elseif ($tierRole === 'approved') {
            $plan->status = 'approved';
            $plan->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Extension Plan digitally signed successfully as '{$roleLabels[$tierRole]}'.",
            'signature' => $sigRecord,
            'status' => $plan->status,
            'status_badge' => $plan->status_badge,
        ]);
    }

    public function edit(string $id): View
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::with(['academicProgram', 'preparedByUser', 'preparedByEmployee', 'notedByEmployee', 'approvedByEmployee'])
            ->findOrFail($planId);

        $user = Auth::user();
        if (! $plan->canBeEditedBy($user)) {
            abort(403, 'You are not authorized to edit this Extension Plan, or it has already progressed past the editable stage.');
        }

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();

        $academicPrograms = AcademicProgram::with(['campus'])
            ->where('is_active', true)
            ->where(function ($q) use ($tenant) {
                if ($tenant) {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', $tenant->id);
                }
            })
            ->orderBy('program_name', 'asc')
            ->get();

        $employees = Employee::where('is_active', true)
            ->where(function ($q) use ($tenant) {
                if ($tenant) {
                    $q->whereNull('tenant_id')->orWhere('tenant_id', $tenant->id);
                }
            })
            ->orderBy('last_name', 'asc')
            ->get();

        return view('dss.rie.extension_plans.edit', compact(
            'plan',
            'institution',
            'user',
            'academicPrograms',
            'employees'
        ));
    }

    public function update(UpdateExtensionPlanRequest $request, string $id): RedirectResponse|JsonResponse
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::findOrFail($planId);

        $user = Auth::user();
        if (! $plan->canBeEditedBy($user)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized or plan is locked from editing.'], 403);
            }

            return back()->with('error', 'You are not authorized to edit this Extension Plan.');
        }

        $validated = $request->validated();
        $notedEmployee = Employee::with(['department'])->findOrFail($validated['noted_by_employee_id']);
        $approvedEmployee = Employee::with(['department'])->findOrFail($validated['approved_by_employee_id']);

        $actionType = $request->input('action_type', 'draft');
        $planStatus = $actionType === 'submit' ? 'submitted' : ($plan->status === 'submitted' ? 'submitted' : 'draft');

        $materialsDates = ! empty($validated['materials_arrival_dates']) ? (array) $validated['materials_arrival_dates'] : null;
        $conductDates = ! empty($validated['conduct_activity_dates']) ? (array) $validated['conduct_activity_dates'] : null;

        $plan->update([
            'program_name' => $validated['program_name'],
            'academic_program_id' => $validated['academic_program_id'] ?? null,
            'extension_activity' => $validated['extension_activity'],
            'objectives' => $validated['objectives'],
            'sub_extension_proposal_date' => $validated['sub_extension_proposal_date'] ?? null,
            'sub_activity_design_date' => $validated['sub_activity_design_date'] ?? null,
            'sub_purchase_request_date' => $validated['sub_purchase_request_date'] ?? null,
            'moa_signing_date' => $validated['moa_signing_date'] ?? null,
            'materials_arrival_mode' => $validated['materials_arrival_mode'] ?? 'single',
            'materials_arrival_start' => $validated['materials_arrival_start'] ?? null,
            'materials_arrival_end' => $validated['materials_arrival_end'] ?? null,
            'materials_arrival_dates' => $materialsDates,
            'materials_arrival_date' => $validated['materials_arrival_date'] ?? null,
            'conduct_activity_mode' => $validated['conduct_activity_mode'] ?? 'single',
            'conduct_activity_start' => $validated['conduct_activity_start'] ?? null,
            'conduct_activity_end' => $validated['conduct_activity_end'] ?? null,
            'conduct_activity_dates' => $conductDates,
            'conduct_activity_date' => $validated['conduct_activity_date'] ?? null,
            'sub_narrative_report_date' => $validated['sub_narrative_report_date'] ?? null,
            'monitoring_feedback_date' => $validated['monitoring_feedback_date'] ?? null,
            'monitoring_feedback_end_date' => $validated['monitoring_feedback_end_date'] ?? null,
            'beneficiaries' => $validated['beneficiaries'],
            'target_beneficiaries_count' => (int) $validated['target_beneficiaries_count'],
            'proposed_budget' => (float) $validated['proposed_budget'],
            'extensionists_count' => (int) $validated['extensionists_count'],
            'noted_by_employee_id' => $notedEmployee->id,
            'noted_by_name' => $notedEmployee->full_name,
            'noted_by_designation' => $validated['noted_by_designation'] ?: ($notedEmployee->designation ?? 'Head, Extension Services'),
            'approved_by_employee_id' => $approvedEmployee->id,
            'approved_by_name' => $approvedEmployee->full_name,
            'approved_by_designation' => $validated['approved_by_designation'] ?: ($approvedEmployee->designation ?? 'College Dean'),
            'tracking_number' => array_key_exists('tracking_number', $validated) ? (! empty($validated['tracking_number']) ? trim((string) $validated['tracking_number']) : null) : $plan->tracking_number,
            'status' => $planStatus,
        ]);

        if (method_exists($plan, 'recordAuditTrail')) {
            $plan->recordAuditTrail(
                'Plan Updated',
                "Extension Plan {$plan->plan_code} details updated by {$user->name}. Action: ".($actionType === 'submit' ? 'Submitted for notation' : 'Saved as draft'),
                $user
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Extension Plan updated successfully.',
                'redirect' => route('dss.rie.plans.show', $plan->getRouteKey()),
            ]);
        }

        return redirect()->route('dss.rie.plans.show', $plan->getRouteKey())
            ->with('success', "Extension Plan '{$plan->plan_code}' updated successfully.");
    }

    public function destroy(string $id): JsonResponse
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::findOrFail($planId);
        $user = Auth::user();

        if (! $plan->canBeDeletedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete this plan. Only draft plans can be deleted by their creator or administrator.',
            ], 403);
        }

        $code = $plan->plan_code;
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => "Draft Extension Plan '{$code}' has been successfully deleted.",
        ]);
    }

    public function submitForReview(Request $request, string $id): JsonResponse
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::findOrFail($planId);
        $user = Auth::user();

        if ($plan->status !== 'draft') {
            return response()->json(['success' => false, 'message' => "Plan is currently '{$plan->status}' and cannot be resubmitted."], 422);
        }

        if ($plan->prepared_by_user_id !== $user->id && ! $user->isSuperAdmin() && ! $user->isTenantAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only the plan proponent can submit this plan for review.'], 403);
        }

        $plan->status = 'submitted';
        $plan->save();

        if (method_exists($plan, 'recordAuditTrail')) {
            $plan->recordAuditTrail('Submitted for Review', "Extension Plan {$plan->plan_code} submitted for notation and institutional routing by {$user->name}.", $user);
        }

        return response()->json([
            'success' => true,
            'message' => "Extension Plan '{$plan->plan_code}' successfully submitted for notation.",
            'status' => $plan->status,
            'status_badge' => $plan->status_badge,
        ]);
    }

    public function connectTracking(Request $request, string $id): JsonResponse
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::findOrFail($planId);

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:60',
        ]);

        $clean = trim($validated['tracking_number']);
        $plan->update(['tracking_number' => $clean]);

        $res = SnsuTrackingService::attachTrackingNumber($plan, $clean);

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    public function fetchLiveTracking(string $id): JsonResponse
    {
        $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $plan = ExtensionPlan::findOrFail($planId);

        $trackingNumber = $plan->tracking_number ?? ($plan->plan_code ?? null);
        $liveData = SnsuTrackingService::fetchTracking($trackingNumber);

        if (! $liveData) {
            return response()->json([
                'success' => false,
                'message' => "No live document tracking records found in Central DTS for '{$trackingNumber}'.",
                'tracking_number' => $trackingNumber,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $liveData,
        ]);
    }
}
