<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DSS\CentralizedSignatoryController;
use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\RecordRoute;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionAgenda;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\RIE\SustainableDevelopmentGoal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Services\DSS\DssAiAssistantService;
use App\Services\DSS\TenantContext;
use App\Services\DTS\DtsRoutingService;
use App\Services\DTS\SnsuTrackingService;
use App\Services\PDF\DocumentPdfGenerator;
use App\Services\Signatories\CentralizedSignatoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExtensionProposalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        $baseQuery = ExtensionProposal::where('tenant_id', $tenantId);
        $totalMasterCount = (clone $baseQuery)->count();
        $myProposalsCount = (clone $baseQuery)->where('project_leader_user_id', $user?->id)->count();

        $draftCount = (clone $baseQuery)->where('status', 'draft')->count();
        $inRouteCount = (clone $baseQuery)->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended'])->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();

        $approvedPlansQuery = ExtensionPlan::where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->doesntHave('extensionProposal');
        $approvedPlansReadyCount = (clone $approvedPlansQuery)->count();
        $approvedPlansList = $approvedPlansQuery->latest()->get();
        $allSdgs = SustainableDevelopmentGoal::ordered()->get();
        $selectedSdg = $request->query('sdg', 'all');

        $isMyProposals = false;

        return view('dss.rie.extension_proposals.index', compact(
            'isMyProposals',
            'totalMasterCount',
            'myProposalsCount',
            'draftCount',
            'inRouteCount',
            'approvedCount',
            'approvedPlansReadyCount',
            'approvedPlansList',
            'allSdgs',
            'selectedSdg'
        ));
    }

    public function myProposals(Request $request)
    {
        if ($request->ajax()) {
            $request->merge(['my_proposals' => 1]);

            return $this->data($request);
        }

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $baseQuery = ExtensionProposal::where('tenant_id', $tenantId);
        $totalMasterCount = (clone $baseQuery)->count();
        $myProposalsCount = (clone $baseQuery)->where('project_leader_user_id', $user?->id)->count();

        $draftCount = (clone $baseQuery)->where('project_leader_user_id', $user?->id)->where('status', 'draft')->count();
        $inRouteCount = (clone $baseQuery)->where('project_leader_user_id', $user?->id)->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended'])->count();
        $approvedCount = (clone $baseQuery)->where('project_leader_user_id', $user?->id)->where('status', 'approved')->count();

        $approvedPlansList = ExtensionPlan::where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where('prepared_by_user_id', $user?->id)
            ->doesntHave('extensionProposal')
            ->latest()
            ->get();
        $approvedPlansReadyCount = $approvedPlansList->count();
        $allSdgs = SustainableDevelopmentGoal::ordered()->get();
        $selectedSdg = $request->query('sdg', 'all');

        $isMyProposals = true;

        return view('dss.rie.extension_proposals.index', compact(
            'isMyProposals',
            'totalMasterCount',
            'myProposalsCount',
            'draftCount',
            'inRouteCount',
            'approvedCount',
            'approvedPlansReadyCount',
            'approvedPlansList',
            'allSdgs',
            'selectedSdg'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        $query = ExtensionProposal::with([
            'extensionPlan',
            'academicProgram',
            'campus',
            'projectLeaderUser',
            'sdgs',
        ])->where('tenant_id', $tenantId);

        if ($request->boolean('my_proposals') && $user) {
            $query->where('project_leader_user_id', $user->id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('sdg') && $request->sdg !== 'all') {
            $sdgNum = (int) $request->sdg;
            if ($sdgNum >= 1 && $sdgNum <= 17) {
                $query->where(function ($q) use ($sdgNum) {
                    $q->whereHas('sdgs', function ($sq) use ($sdgNum) {
                        $sq->where('goal_number', $sdgNum);
                    })
                        ->orWhereJsonContains('sdg_alignments->selected', $sdgNum)
                        ->orWhereJsonContains('sdg_alignments->selected', (string) $sdgNum)
                        ->orWhereJsonContains('sdg_alignments->indirect', $sdgNum)
                        ->orWhereJsonContains('sdg_alignments->indirect', (string) $sdgNum)
                        ->orWhereJsonContains('sdg_alignments', $sdgNum)
                        ->orWhereJsonContains('sdg_alignments', (string) $sdgNum);
                });
            }
        }

        $sdgColors = [
            1 => '#E5243B', 2 => '#DDA63A', 3 => '#4C9F38', 4 => '#C5192D',
            5 => '#FF3A21', 6 => '#26BDE2', 7 => '#FCC30B', 8 => '#A21942',
            9 => '#FD6925', 10 => '#DD1367', 11 => '#FD9D24', 12 => '#BF8B2E',
            13 => '#3F7E44', 14 => '#0A97D9', 15 => '#56C02B', 16 => '#00689D',
            17 => '#19486A',
        ];

        return DataTables::of($query)
            ->addColumn('code_link', function (ExtensionProposal $proposal) {
                $code = $proposal->proposal_code ?: 'PROP-'.str_pad($proposal->id, 4, '0', STR_PAD_LEFT);
                $url = route('dss.rie.proposals.show', $proposal->getRouteKey());
                $trackHtml = '';
                if ($proposal->tracking_number) {
                    $trackUrl = $proposal->tracking_url;
                    $trackHtml = '<a href="'.$trackUrl.'" target="_blank" class="extra-small font-mono text-muted text-decoration-none mt-1 d-inline-flex align-items-center gap-1 hover-text-primary" title="Track in DTS">'
                        .'<i class="mdi mdi-qrcode-scan text-primary"></i>'.$proposal->tracking_number
                        .'</a>';
                }

                return '<div>'
                    .'<a href="'.$url.'" class="fw-bold text-dark font-mono text-decoration-none d-inline-flex align-items-center gap-1 hover-text-primary">'
                    .'<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1">'.$code.'</span>'
                    .'</a>'
                    .$trackHtml
                    .'</div>';
            })
            ->addColumn('plan_reference', function (ExtensionProposal $proposal) {
                if (! $proposal->extensionPlan) {
                    return '<span class="text-muted extra-small">None</span>';
                }
                $plan = $proposal->extensionPlan;
                $url = route('dss.rie.plans.show', $plan->getRouteKey());

                return '<a href="'.$url.'" class="badge bg-success-subtle text-success border border-success-subtle font-mono text-decoration-none px-2 py-0.5" title="Parent Approved Plan">'
                    .'<i class="mdi mdi-clipboard-check-outline me-1"></i>'.$plan->plan_code
                    .'</a>';
            })
            ->addColumn('title_display', function (ExtensionProposal $proposal) use ($sdgColors) {
                $url = route('dss.rie.proposals.show', $proposal->getRouteKey());
                $college = $proposal->academicProgram?->name ?? 'General Extension';
                $resolvedSdgs = $proposal->resolved_sdg_alignments;
                $directNums = array_map('intval', (array) ($resolvedSdgs['selected'] ?? []));
                $indirectNums = array_map('intval', (array) ($resolvedSdgs['indirect'] ?? []));
                $allNums = array_values(array_unique(array_merge($directNums, $indirectNums)));
                sort($allNums);

                $sdgHtml = '';
                if (! empty($allNums)) {
                    $sdgHtml = '<div class="d-flex flex-wrap align-items-center gap-1 mt-1">';
                    foreach ($allNums as $num) {
                        $color = $sdgColors[$num] ?? '#19486A';
                        $isIndirect = in_array($num, $indirectNums, true) && ! in_array($num, $directNums, true);
                        $borderStyle = $isIndirect ? 'border: 1px dashed #ffffff; opacity: 0.85;' : '';
                        $label = 'SDG '.$num.($isIndirect ? ' (Ind)' : '');
                        $sdgHtml .= '<span class="badge text-white font-mono" style="background-color: '.$color.'; font-size: 9.5px; padding: 2px 6px; '.$borderStyle.'">'.$label.'</span>';
                    }
                    $sdgHtml .= '</div>';
                }

                return '<div>'
                    .'<a href="'.$url.'" class="fw-bold text-dark text-decoration-none hover-text-primary d-block text-truncate" style="max-width: 320px;" title="'.e($proposal->title).'">'
                    .e($proposal->title)
                    .'</a>'
                    .'<span class="extra-small text-muted d-block text-truncate" style="max-width: 320px;">'
                    .e($college)
                    .'</span>'
                    .$sdgHtml
                    .'</div>';
            })
            ->addColumn('leader_display', function (ExtensionProposal $proposal) {
                return '<div class="extra-small">'
                    .'<div class="fw-semibold text-dark text-truncate" style="max-width: 170px;">'.e($proposal->project_leader_name).'</div>'
                    .'<div class="text-muted extra-small">'.e($proposal->project_leader_title).'</div>'
                    .'</div>';
            })
            ->addColumn('budget_display', function (ExtensionProposal $proposal) {
                if ($proposal->budget_cash_amount > 0) {
                    return '<span class="font-mono fw-bold text-dark">₱'.number_format($proposal->budget_cash_amount, 2).'</span>';
                }

                return '<span class="badge bg-light text-secondary border extra-small">In-Kind / Non-Cash</span>';
            })
            ->addColumn('status_badge', function (ExtensionProposal $proposal) {
                $badge = $proposal->status_badge;

                return '<span class="badge '.$badge['class'].' extra-small font-mono px-2 py-1 d-inline-flex align-items-center gap-1">'
                    .'<i class="mdi '.$badge['icon'].'"></i>'
                    .'<span>'.$badge['label'].'</span>'
                    .'</span>';
            })
            ->addColumn('actions', function (ExtensionProposal $proposal) {
                $key = $proposal->getRouteKey();
                $viewUrl = route('dss.rie.proposals.show', $key);
                $editUrl = route('dss.rie.proposals.edit', $key);
                $pdfUrl = route('dss.rie.proposals.pdf', $key);
                $accomplishmentUrl = route('dss.rie.proposals.accomplishment.report', $key);

                $dtsItem = '';
                if ($proposal->tracking_number) {
                    $trackUrl = route('document.track', $proposal->tracking_number);
                    $dtsItem = '<li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-primary" href="'.$trackUrl.'" target="_blank"><i class="mdi mdi-radar fs-6"></i><span>Track in DTS ('.e($proposal->tracking_number).')</span></a></li>';
                }

                $user = Auth::user();
                $canEdit = $proposal->status === 'draft' || ($user && $user->can('rie.proposals.manage'));

                return '
                    <div class="d-flex align-items-center gap-1">
                        <a href="'.$viewUrl.'" class="btn btn-sm btn-dss-primary px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1" title="Open Proposal Workbench">
                            <i class="mdi mdi-eye-outline"></i>
                        </a>
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-light border text-dark px-2 py-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport" data-bs-popper-config=\'{"strategy":"fixed"}\' title="Operations & Documents">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-start shadow-lg border extra-small p-1" style="min-width: 220px; z-index: 1065;">
                                <li class="dropdown-header extra-small text-uppercase fw-bold text-muted py-1 font-mono">Proposal Operations</li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$viewUrl.'"><i class="mdi mdi-view-dashboard-outline text-primary fs-6"></i><span>Proposal Workbench</span></a></li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-primary" href="'.route('dss.documents.signatories.edit', ['type' => 'extension_proposal', 'id' => $key]).'"><i class="mdi mdi-account-cog-outline fs-6"></i><span>Configure Signatories</span></a></li>
                                '.($canEdit ? '<li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1 text-warning-emphasis" href="'.$editUrl.'"><i class="mdi mdi-pencil-outline fs-6"></i><span>Edit Proposal</span></a></li>' : '').'
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$accomplishmentUrl.'"><i class="mdi mdi-trophy-outline text-success fs-6"></i><span>Terminal / Accomplishment</span></a></li>
                                '.$dtsItem.'
                                <li><hr class="dropdown-divider my-1"></li>
                                <li class="dropdown-header extra-small text-uppercase fw-bold text-muted py-1 font-mono">Official Documents</li>
                                <li><a class="dropdown-item py-1 d-flex align-items-center gap-2 rounded-1" href="'.$pdfUrl.'" target="_blank"><i class="mdi mdi-file-pdf-box text-danger fs-6"></i><span>Official 7-Page PDF</span></a></li>
                            </ul>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['code_link', 'plan_reference', 'title_display', 'leader_display', 'budget_display', 'status_badge', 'actions'])
            ->make(true);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $rawPlanId = $request->query('plan_id');
        $resolvedPlanId = null;

        if ($rawPlanId) {
            $resolvedPlanId = is_numeric($rawPlanId) ? (int) $rawPlanId : (function_exists('dss_decrypt_id') ? dss_decrypt_id($rawPlanId) : null);
        }

        if (! $resolvedPlanId) {
            $approvedPlans = ExtensionPlan::where('tenant_id', $tenantId)
                ->where('status', 'approved')
                ->doesntHave('extensionProposal')
                ->latest()
                ->get();

            if ($approvedPlans->isEmpty()) {
                return redirect()->route('dss.rie.plans.index')
                    ->with('warning', 'Notice: An Extension Proposal requires an approved Extension Plan. There are currently no approved Extension Plans available without a proposal.');
            }

            if ($approvedPlans->count() === 1) {
                $resolvedPlanId = $approvedPlans->first()->id;
            } else {
                return view('dss.rie.extension_proposals.select_plan', compact('approvedPlans'));
            }
        }

        $plan = ExtensionPlan::with([
            'academicProgram.campus',
            'campus',
            'preparedByUser',
            'extensionProposal',
        ])->where('tenant_id', $tenantId)->findOrFail($resolvedPlanId);

        if (! $plan->canCreateProposal()) {
            return redirect()->route('dss.rie.plans.show', $plan->getRouteKey())
                ->with('error', "Access Denied: Extension Proposal can only be initiated for an APPROVED Extension Plan. This plan is currently: '".strtoupper($plan->status)."'.");
        }

        if ($plan->extensionProposal) {
            return redirect()->route('dss.rie.proposals.show', $plan->extensionProposal->getRouteKey())
                ->with('info', "An Extension Proposal ({$plan->extensionProposal->proposal_code}) already exists for this Extension Plan.");
        }

        $campuses = Campus::where('tenant_id', $tenantId)->get();
        $academicPrograms = AcademicProgram::where('tenant_id', $tenantId)->get();
        $colleges = Unit::where('tenant_id', $tenantId)->orderBy('unit_name', 'asc')->get();
        $allSdgs = SustainableDevelopmentGoal::ordered()->get();

        $bannerPrograms = ExtensionBannerProgram::active()->ordered()->get();
        $extensionAgendas = ExtensionAgenda::active()->ordered()->get();

        $leadEmployee = $plan->prepared_by_employee_id
            ? Employee::find($plan->prepared_by_employee_id)
            : (Auth::user()?->employee_id ? Employee::find(Auth::user()->employee_id) : null);
        $defaultGender = $leadEmployee?->gender ?: '';

        $durationMonths = 1;
        if ($plan->conduct_activity_start && $plan->conduct_activity_end) {
            $m = $plan->conduct_activity_start->diffInMonths($plan->conduct_activity_end);
            $durationMonths = max(1, (int) round($m));
        }

        $suggestedData = self::resolveSuggestedSignatoriesForPlan($plan);
        $suggestedSignatories = $suggestedData['signatories'] ?? [];
        $suggestedSource = $suggestedData['source'] ?? 'template';
        $suggestedPastCode = $suggestedData['past_proposal_code'] ?? null;

        return view('dss.rie.extension_proposals.create', compact(
            'plan',
            'campuses',
            'academicPrograms',
            'colleges',
            'bannerPrograms',
            'extensionAgendas',
            'defaultGender',
            'durationMonths',
            'allSdgs',
            'suggestedSignatories',
            'suggestedSource',
            'suggestedPastCode'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        $planId = $request->input('extension_plan_id');
        $resolvedPlanId = is_numeric($planId) ? (int) $planId : (function_exists('dss_decrypt_id') ? dss_decrypt_id($planId) : null);

        $plan = ExtensionPlan::where('tenant_id', $tenantId)->findOrFail($resolvedPlanId);

        if (! $plan->canCreateProposal()) {
            return back()->withInput()->with('error', "Validation Failed: Cannot create Extension Proposal for unapproved Extension Plan '{$plan->plan_code}'.");
        }

        if ($plan->extensionProposal) {
            return redirect()->route('dss.rie.proposals.show', $plan->extensionProposal->getRouteKey())
                ->with('info', 'An Extension Proposal already exists for this plan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'banner_program' => 'nullable|string|max:100',
            'banner_program_other' => 'nullable|string|max:255',
            'agenda_priorities' => 'nullable|array',
            'beneficiaries' => 'nullable|string',
            'target_beneficiaries_data' => 'nullable',
            'implementing_agency' => 'nullable|string|max:255',
            'implementing_colleges' => 'nullable',
            'lead_agency' => 'nullable|string|max:255',
            'collaborating_agencies' => 'nullable|string',
            'collaborating_agencies_data' => 'nullable',
            'duration_start' => 'nullable|date',
            'duration_end' => 'nullable|date',
            'duration_text' => 'nullable|string|max:150',
            'duration_months' => 'nullable|integer',
            'project_location' => 'nullable|string|max:255',
            'budget_institution_share' => 'nullable|string',
            'budget_snsu_share' => 'nullable|string',
            'budget_partner_share' => 'nullable|string',
            'fund_source_internal' => 'nullable|string',
            'fund_source_external' => 'nullable|string',
            'budget_total_display' => 'nullable|string|max:100',
            'budget_cash_amount' => 'nullable|numeric|min:0',

            'project_leader_name' => 'nullable|string|max:191',
            'project_leader_title' => 'nullable|string|max:191',
            'project_leader_gender' => 'nullable|string|max:20',
            'asst_project_leader_name' => 'nullable|string|max:191',
            'asst_project_leader_title' => 'nullable|string|max:191',
            'proponents' => 'nullable|array',

            'introduction' => 'nullable|string',
            'general_objective' => 'nullable|string',
            'specific_objectives' => 'nullable',
            'objectives' => 'nullable|array',
            'expected_outputs' => 'nullable',
            'relevance_narrative' => 'nullable|string',
            'sdg_alignments' => 'nullable',
            'development_gap' => 'nullable|string',
            'pdp_responsiveness' => 'nullable|string',
            'schedule_of_activities' => 'nullable',
            'people_involved' => 'nullable',
            'org_structure' => 'nullable|array',
            'risk_management' => 'nullable',
            'sustainability_measures' => 'nullable|string',

            'action_type' => 'nullable|string|in:draft,submit',
        ]);

        $proposalCode = ExtensionProposal::generateCode($tenantId);

        $status = 'draft';

        $rawProponents = $request->input('proponents', []);
        $cleanProponents = [];
        if (is_array($rawProponents)) {
            foreach ($rawProponents as $p) {
                $name = trim($p['name'] ?? '');
                $role = trim($p['role'] ?? '');
                $empId = ! empty($p['employee_id']) ? (int) $p['employee_id'] : null;
                $type = $p['type'] ?? ($empId ? 'faculty' : 'manual');
                if ($name !== '') {
                    $cleanProponents[] = [
                        'name' => $name,
                        'role' => $role ?: 'Faculty Extensionist',
                        'employee_id' => $empId,
                        'type' => $type,
                    ];
                }
            }
        }

        $leaderProp = collect($cleanProponents)->first(function ($p) {
            $role = strtolower($p['role'] ?? '');

            return str_contains($role, 'leader') && ! str_contains($role, 'asst') && ! str_contains($role, 'assistant');
        }) ?? ($cleanProponents[0] ?? null);

        $asstLeaderProp = collect($cleanProponents)->first(function ($p) {
            $role = strtolower($p['role'] ?? '');

            return str_contains($role, 'asst') || str_contains($role, 'assistant');
        });

        $projectLeaderName = $leaderProp['name'] ?? ($request->input('project_leader_name') ?: ($plan->prepared_by_name ?: ($user?->name ?: 'Project Leader')));
        $projectLeaderTitle = $leaderProp['role'] ?? ($request->input('project_leader_title') ?: 'Project Leader');
        $projectLeaderEmployeeId = $leaderProp['employee_id'] ?? ($user?->employee_id ?? $plan->prepared_by_employee_id);

        $asstProjectLeaderName = ! empty($asstLeaderProp['name']) ? $asstLeaderProp['name'] : null;
        $asstProjectLeaderTitle = ! empty($asstLeaderProp['role']) ? $asstLeaderProp['role'] : 'Assistant Project Leader';

        $institution = TenantContext::getInstitution();
        $defaultAgency = $plan->campus?->campus_name ?: ($institution?->school_name ?: 'Higher Education Institution');
        $budgetInstitutionShare = $validated['budget_institution_share'] ?? ($validated['budget_snsu_share'] ?? ($validated['fund_source_internal'] ?? 'In-Kind (University Service Vehicle & Faculty Expertise)'));

        $proponentEmp = $projectLeaderEmployeeId
            ? Employee::with('department')->find($projectLeaderEmployeeId)
            : ($user?->employee_id ? Employee::with('department')->find($user->employee_id) : null);
        $resolvedUnitId = $plan->academicProgram?->department_id
            ?: ($plan->extensionPlan?->academicProgram?->department_id
            ?: ($proponentEmp?->department_id));

        $targetBeneficiaries = $request->input('target_beneficiaries_data');
        if (is_string($targetBeneficiaries)) {
            $decoded = json_decode($targetBeneficiaries, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $targetBeneficiaries = $decoded;
            }
        }

        $collaboratingAgenciesData = $request->input('collaborating_agencies_data');
        if (is_string($collaboratingAgenciesData)) {
            $decoded = json_decode($collaboratingAgenciesData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $collaboratingAgenciesData = $decoded;
            }
        }

        $implementingColleges = $request->input('implementing_colleges');
        if (is_string($implementingColleges)) {
            $decoded = json_decode($implementingColleges, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $implementingColleges = $decoded;
            } else {
                $implementingColleges = array_filter(array_map('trim', explode(',', $implementingColleges)));
            }
        }

        $durationStart = ! empty($validated['duration_start']) ? Carbon::parse($validated['duration_start']) : $plan->conduct_activity_start;
        $durationEnd = ! empty($validated['duration_end']) ? Carbon::parse($validated['duration_end']) : $plan->conduct_activity_end;
        $durationMonths = $validated['duration_months'] ?? null;
        if (! $durationMonths && $durationStart && $durationEnd) {
            $durationMonths = max(1, (int) round($durationStart->diffInMonths($durationEnd)));
        }

        $proposal = ExtensionProposal::create([
            'tenant_id' => $tenantId,
            'campus_id' => $plan->campus_id,
            'unit_id' => $resolvedUnitId,
            'academic_program_id' => $plan->academic_program_id,
            'extension_plan_id' => $plan->id,
            'proposal_code' => $proposalCode,

            'title' => $validated['title'],
            'banner_program' => $validated['banner_program'] ?? null,
            'banner_program_other' => $validated['banner_program_other'] ?? null,
            'agenda_priorities' => $validated['agenda_priorities'] ?? [],
            'beneficiaries' => $validated['beneficiaries'] ?? $plan->beneficiaries,
            'target_beneficiaries_data' => $targetBeneficiaries ?: null,
            'implementing_agency' => $validated['implementing_agency'] ?? $defaultAgency,
            'implementing_colleges' => $implementingColleges ?: null,
            'lead_agency' => $validated['lead_agency'] ?? $defaultAgency,
            'collaborating_agencies' => $validated['collaborating_agencies'] ?? null,
            'collaborating_agencies_data' => $collaboratingAgenciesData ?: null,
            'duration_start' => $durationStart,
            'duration_end' => $durationEnd,
            'duration_text' => $validated['duration_text'] ?? null,
            'duration_months' => $durationMonths,
            'project_location' => $validated['project_location'] ?? null,

            'budget_institution_share' => $budgetInstitutionShare,
            'budget_partner_share' => $validated['budget_partner_share'] ?? null,
            'fund_source_internal' => $validated['fund_source_internal'] ?? $budgetInstitutionShare,
            'fund_source_external' => $validated['fund_source_external'] ?? ($validated['budget_partner_share'] ?? null),
            'budget_total_display' => ! empty($validated['budget_total_display']) ? $validated['budget_total_display'] : 'Grand Total: No Cash Requirement',
            'budget_cash_amount' => isset($validated['budget_cash_amount']) && $validated['budget_cash_amount'] !== '' ? (float) $validated['budget_cash_amount'] : (float) ($plan->proposed_budget ?: 0),

            'project_leader_user_id' => $user?->id,
            'project_leader_employee_id' => $projectLeaderEmployeeId,
            'project_leader_name' => $projectLeaderName ?: 'Project Leader',
            'project_leader_title' => $projectLeaderTitle ?: 'Project Leader',
            'project_leader_gender' => $validated['project_leader_gender'] ?? ($proponentEmp?->gender ?: null),
            'asst_project_leader_name' => $asstProjectLeaderName,
            'asst_project_leader_title' => $asstProjectLeaderTitle ?: 'Assistant Project Leader',
            'proponents' => $cleanProponents,

            'introduction' => $validated['introduction'] ?? null,
            'general_objective' => $validated['general_objective'] ?? null,
            'specific_objectives' => $validated['specific_objectives'] ?? null,
            'objectives' => ! empty($validated['objectives']) ? $validated['objectives'] : ($plan->objectives ? array_filter(array_map('trim', explode("\n", $plan->objectives))) : []),
            'expected_outputs' => $validated['expected_outputs'] ?? null,
            'relevance_narrative' => $validated['relevance_narrative'] ?? null,
            'sdg_alignments' => $validated['sdg_alignments'] ?? [],
            'development_gap' => $validated['development_gap'] ?? null,
            'pdp_responsiveness' => $validated['pdp_responsiveness'] ?? null,
            'schedule_of_activities' => $validated['schedule_of_activities'] ?? [],
            'people_involved' => $validated['people_involved'] ?? [],
            'org_structure' => $validated['org_structure'] ?? [],
            'risk_management' => $validated['risk_management'] ?? null,
            'sustainability_measures' => $validated['sustainability_measures'] ?? null,

            'prepared_by_user_id' => $user?->id,
            'prepared_by_employee_id' => $user?->employee_id ?? $plan->prepared_by_employee_id,
            'prepared_by_name' => $projectLeaderName,
            'prepared_by_designation' => $projectLeaderTitle ?: 'Project Leader',
            'prepared_at' => null,

            'status' => $status,
        ]);

        if (! empty($validated['sdg_alignments'])) {
            self::syncProposalSdgs($proposal, $validated['sdg_alignments']);
        }

        DtsRoutingService::registerExtensionProposal($proposal, $user?->id);
        $proposal->refresh();

        self::syncSignatoriesAndRoutes($proposal);

        return redirect()->route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'technical'])
            ->with('success', 'Part I saved as Draft. You may now craft Part II (Technical Description) and configure signatories before submitting for review.');
    }

    public static function syncProposalSdgs(ExtensionProposal $proposal, mixed $sdgAlignments): void
    {
        if (! is_array($sdgAlignments)) {
            return;
        }

        $selectedGoals = [];
        $indirectGoals = [];

        if (isset($sdgAlignments['selected']) || isset($sdgAlignments['indirect'])) {
            $selectedGoals = array_map('intval', (array) ($sdgAlignments['selected'] ?? []));
            $indirectGoals = array_map('intval', (array) ($sdgAlignments['indirect'] ?? []));
        } else {
            $selectedGoals = array_map('intval', array_filter($sdgAlignments, fn ($v) => is_numeric($v) || is_string($v)));
        }

        $allGoalNumbers = array_values(array_unique(array_merge($selectedGoals, $indirectGoals)));
        if (empty($allGoalNumbers)) {
            $proposal->sdgs()->sync([]);

            return;
        }

        $sdgMap = SustainableDevelopmentGoal::whereIn('goal_number', $allGoalNumbers)
            ->pluck('id', 'goal_number');

        $syncPayload = [];
        foreach ($selectedGoals as $goalNum) {
            if (isset($sdgMap[$goalNum])) {
                $syncPayload[$sdgMap[$goalNum]] = ['is_primary' => true];
            }
        }
        foreach ($indirectGoals as $goalNum) {
            if (isset($sdgMap[$goalNum]) && ! isset($syncPayload[$sdgMap[$goalNum]])) {
                $syncPayload[$sdgMap[$goalNum]] = ['is_primary' => false];
            }
        }

        $proposal->sdgs()->sync($syncPayload);
    }

    public static function resolveSignatoryPlacement(?string $roleLabel, ?string $tierRole, int $idx, ?int $userCol = null, ?string $userAlign = null): array
    {
        $combined = strtolower(trim(($roleLabel ?? '').' '.($tierRole ?? '')));

        if (str_contains($combined, 'funds')) {
            return ['col' => 2, 'align' => 'right'];
        }

        if (str_contains($combined, 'allotment')) {
            return ['col' => 1, 'align' => 'left'];
        }

        if (! empty($userAlign) && in_array(strtolower($userAlign), ['left', 'center', 'right'], true)) {
            $align = strtolower($userAlign);
            $col = $userCol ?: ($align === 'right' ? 2 : ($align === 'center' ? 2 : 1));

            return ['col' => $col, 'align' => $align];
        }

        if (str_contains($combined, 'noted') || str_contains($combined, 'concurred') || str_contains($combined, 'prepared') || str_contains($combined, 'reviewed') || str_contains($combined, 'endorsed')) {
            return ['col' => 1, 'align' => 'left'];
        }

        if (str_contains($combined, 'recommend') || str_contains($combined, 'approve')) {
            return ['col' => 2, 'align' => 'center'];
        }

        return ['col' => 1, 'align' => 'left'];
    }

    public static function syncSignatoriesAndRoutes(ExtensionProposal $proposal, array $signatoryInputs = []): void
    {
        $tenantId = $proposal->tenant_id ?: 1;

        if (Schema::hasTable('dts_records') && empty($proposal->dts_record_id)) {
            DtsRoutingService::registerExtensionProposal($proposal, $proposal->project_leader_user_id);
            $proposal->refresh();
        }

        $submittedSignatories = $signatoryInputs['signatories'] ?? null;
        if (is_array($submittedSignatories)) {
            $submittedSignatories = array_values($submittedSignatories);
        }

        if (is_array($submittedSignatories) && count($submittedSignatories) > 0) {
            $existingSigs = DocumentSignatory::where('document_type', $proposal->getMorphClass())
                ->where('document_id', $proposal->id)
                ->get()
                ->keyBy('id');

            $processedIds = [];
            $routingSteps = [];

            foreach ($submittedSignatories as $idx => $item) {
                $sortOrder = $idx + 1;
                $rawRoleLabel = isset($item['role_label']) ? trim((string) $item['role_label']) : null;
                $roleLabel = ($idx === 0) ? ($rawRoleLabel ?: 'Prepared by') : ($rawRoleLabel !== '' ? $rawRoleLabel : null);
                $tierRole = ! empty($item['tier_role']) ? trim($item['tier_role']) : (! empty($roleLabel) ? Str::slug($roleLabel.'_'.$sortOrder, '_') : 'step_'.$sortOrder);
                $empId = ! empty($item['employee_id']) ? (int) $item['employee_id'] : null;

                $emp = null;
                if ($empId) {
                    $emp = Employee::with('department')->find($empId);
                }

                $name = trim($item['name'] ?? ($emp ? ($emp->full_name ?: trim("{$emp->first_name} {$emp->last_name}")) : ''));
                $designation = trim($item['designation'] ?? ($emp?->designation ?: 'Authorized Signatory'));
                $office = trim($item['office'] ?? ($emp?->department?->unit_name ?: 'Higher Education Institution'));

                if (empty($name) && $idx === 0) {
                    $name = $proposal->project_leader_name;
                    $designation = $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader');
                }

                if ($idx > 0 && empty($empId) && empty($name)) {
                    continue;
                }

                if (empty($name) && $emp) {
                    $name = $emp->full_name ?: trim("{$emp->first_name} {$emp->last_name}");
                }

                if (empty($name)) {
                    continue;
                }

                $userAlign = $item['alignment'] ?? null;
                $userCol = isset($item['position_column']) ? (int) $item['position_column'] : null;
                $placement = self::resolveSignatoryPlacement($roleLabel, $tierRole, $idx, $userCol, $userAlign);

                $existingId = ! empty($item['id']) ? (int) $item['id'] : null;
                $existing = $existingId ? ($existingSigs[$existingId] ?? null) : null;

                $status = $existing?->status ?? 'pending';
                if ($idx === 0 || $tierRole === 'prepared') {
                    $status = ($proposal->status !== 'draft') ? 'signed' : 'pending';
                }

                if ($existing) {
                    $existing->update([
                        'role_label' => $roleLabel,
                        'tier_role' => $tierRole,
                        'assignment_type' => 'specific_employee',
                        'employee_id' => $empId,
                        'signatory_name' => $name,
                        'designation' => $designation,
                        'office' => $office,
                        'sort_order' => $sortOrder,
                        'position_column' => $placement['col'],
                        'alignment' => $placement['align'],
                        'status' => $status,
                    ]);
                    $sigRecord = $existing;
                } else {
                    $sigRecord = DocumentSignatory::create([
                        'document_type' => $proposal->getMorphClass(),
                        'document_id' => $proposal->id,
                        'tenant_id' => $tenantId,
                        'campus_id' => $proposal->campus_id,
                        'dts_record_id' => $proposal->dts_record_id,
                        'role_label' => $roleLabel,
                        'tier_role' => $tierRole,
                        'assignment_type' => 'specific_employee',
                        'employee_id' => $empId,
                        'signatory_name' => $name,
                        'designation' => $designation,
                        'office' => $office,
                        'sort_order' => $sortOrder,
                        'position_column' => $placement['col'],
                        'col_span' => 1,
                        'alignment' => $placement['align'],
                        'status' => $status,
                        'requires_digital_signature' => true,
                        'is_mandatory' => true,
                    ]);
                }

                $processedIds[] = $sigRecord->id;

                if ($idx > 0 && $tierRole !== 'prepared') {
                    $actionText = ! empty($roleLabel)
                        ? "For {$roleLabel} & Administrative Endorsement"
                        : 'For Review & Administrative Endorsement';

                    $routingSteps[] = [
                        'role_label' => $roleLabel,
                        'office' => $office,
                        'action' => $actionText,
                        'unit_id' => $emp?->department_id,
                    ];
                }
            }

            DocumentSignatory::where('document_type', $proposal->getMorphClass())
                ->where('document_id', $proposal->id)
                ->whereNotIn('id', $processedIds)
                ->where('status', '!=', 'signed')
                ->delete();

            if ($proposal->dts_record_id && Schema::hasTable('dts_record_routes')) {

                RecordRoute::where('record_id', $proposal->dts_record_id)
                    ->where('step_number', '>', count($routingSteps))
                    ->delete();

                foreach ($routingSteps as $sIdx => $rStep) {
                    $stepNum = $sIdx + 1;
                    $existingRoute = RecordRoute::where('record_id', $proposal->dts_record_id)
                        ->where('step_number', $stepNum)
                        ->first();

                    $routeStatus = $existingRoute?->status ?? 'pending';
                    if ($proposal->status === 'draft') {
                        $routeStatus = 'pending';
                    } elseif ($stepNum === 1 && $routeStatus === 'pending') {
                        $routeStatus = 'in_transit';
                    }

                    RecordRoute::updateOrCreate(
                        [
                            'record_id' => $proposal->dts_record_id,
                            'step_number' => $stepNum,
                        ],
                        [
                            'tenant_id' => $tenantId,
                            'unit_id' => $rStep['unit_id'] ?: $existingRoute?->unit_id,
                            'office_name' => $rStep['office'],
                            'for_action' => $rStep['action'],
                            'sla_hours' => 24,
                            'is_mandatory' => 1,
                            'status' => $routeStatus,
                        ]
                    );
                }
            }

            return;
        }

        $existingCount = DocumentSignatory::where('document_type', $proposal->getMorphClass())
            ->where('document_id', $proposal->id)
            ->count();

        if ($existingCount > 0) {
            return;
        }

        $resolved = self::resolveSuggestedSignatories($proposal);
        self::syncSignatoriesAndRoutes($proposal, ['signatories' => $resolved['signatories']]);
    }

    public static function resolveSuggestedSignatories(ExtensionProposal $proposal): array
    {
        $tenantId = $proposal->tenant_id ?: 1;

        $proponentEmp = $proposal->projectLeaderEmployee
            ?? ($proposal->project_leader_employee_id ? Employee::with('department')->find($proposal->project_leader_employee_id) : null);
        $currentUnitId = $proposal->unit_id
            ?: ($proposal->academicProgram?->department_id
            ?: ($proposal->extensionPlan?->academicProgram?->department_id
            ?: ($proponentEmp?->department_id)));

        if (empty($proposal->unit_id) && $currentUnitId && $proposal->exists) {
            $proposal->update(['unit_id' => $currentUnitId]);
        }

        $pastProposal = ExtensionProposal::where('tenant_id', $tenantId)
            ->where('id', '!=', $proposal->id ?? 0)
            ->whereHas('documentSignatories', function ($q) {
                $q->where('tier_role', '!=', 'prepared');
            })
            ->latest('id')
            ->first();

        if ($pastProposal) {
            $pastSigs = DocumentSignatory::where('document_type', $pastProposal->getMorphClass())
                ->where('document_id', $pastProposal->id)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($pastSigs->isNotEmpty()) {
                $suggested = [];

                $suggested[] = [
                    'role_label' => 'Prepared by:',
                    'tier_role' => 'prepared',
                    'employee_id' => $proposal->project_leader_employee_id,
                    'name' => $proposal->project_leader_name,
                    'designation' => $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader'),
                    'office' => $proponentEmp?->department?->unit_name ?: 'Project Proponent / Extension Team',
                    'alignment' => 'left',
                    'position_column' => 1,
                    'employee_text' => $proposal->project_leader_name.' ('.($proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader')).')',
                ];

                $pastProponentEmp = $pastProposal->projectLeaderEmployee;
                $pastUnitId = $pastProposal->unit_id
                    ?: ($pastProposal->academicProgram?->department_id
                    ?: ($pastProposal->extensionPlan?->academicProgram?->department_id
                    ?: ($pastProponentEmp?->department_id)));

                $isSameUnit = ($currentUnitId && $pastUnitId && (int) $currentUnitId === (int) $pastUnitId);
                $pastNotedSig = $pastSigs->first(fn ($s) => $s->tier_role === 'noted' || str_contains(strtolower($s->role_label ?? ''), 'noted'));

                if ($isSameUnit && $pastNotedSig) {
                    $suggested[] = [
                        'role_label' => $pastNotedSig->role_label ?: 'Noted by:',
                        'tier_role' => 'noted',
                        'employee_id' => $pastNotedSig->employee_id,
                        'name' => $pastNotedSig->signatory_name,
                        'designation' => $pastNotedSig->designation,
                        'office' => $pastNotedSig->office,
                        'alignment' => $pastNotedSig->alignment ?: 'left',
                        'position_column' => $pastNotedSig->position_column ?: 1,
                        'employee_text' => $pastNotedSig->signatory_name.($pastNotedSig->designation ? ' ('.$pastNotedSig->designation.')' : ''),
                    ];
                } else {

                    $unitDean = null;
                    $unitModel = $currentUnitId ? Unit::find($currentUnitId) : null;
                    if ($unitModel && $unitModel->head_employee_id) {
                        $unitDean = Employee::with('department')->find($unitModel->head_employee_id);
                    }

                    if (! $unitDean && $currentUnitId) {
                        $unitDean = Employee::with('department')->where('tenant_id', $tenantId)
                            ->where('department_id', $currentUnitId)
                            ->where(function ($q) {
                                $q->where('designation', 'like', '%Dean%')
                                    ->orWhere('designation', 'like', '%College Head%')
                                    ->orWhere('designation', 'like', '%Head%')
                                    ->orWhere('designation', 'like', '%Director%');
                            })
                            ->first();
                    }

                    if (! $unitDean && $currentUnitId) {
                        $empDesig = EmployeeDesignation::where('department_id', $currentUnitId)
                            ->where(function ($q) {
                                $q->where('designation', 'like', '%Dean%')
                                    ->orWhere('designation', 'like', '%Head%');
                            })
                            ->first();
                        if ($empDesig && $empDesig->employee_id) {
                            $unitDean = Employee::with('department')->find($empDesig->employee_id);
                        }
                    }

                    if ($unitDean) {
                        $suggested[] = [
                            'role_label' => 'Noted by:',
                            'tier_role' => 'noted',
                            'employee_id' => $unitDean->id,
                            'name' => $unitDean->full_name,
                            'designation' => $unitDean->designation ?: 'Dean',
                            'office' => $unitDean->department?->unit_name ?: 'Office of the Dean',
                            'alignment' => 'left',
                            'position_column' => 1,
                            'employee_text' => $unitDean->full_name.' ('.($unitDean->designation ?: 'Dean').')',
                        ];
                    } elseif ($unitModel && ! empty($unitModel->head_name)) {
                        $suggested[] = [
                            'role_label' => 'Noted by:',
                            'tier_role' => 'noted',
                            'employee_id' => null,
                            'name' => $unitModel->head_name,
                            'designation' => $unitModel->head_title ?: 'Dean',
                            'office' => $unitModel->unit_name,
                            'alignment' => 'left',
                            'position_column' => 1,
                            'employee_text' => $unitModel->head_name.' ('.($unitModel->head_title ?: 'Dean').')',
                        ];
                    } else {
                        $targetUnitName = $proposal->academicProgram?->department?->unit_name ?: ($proponentEmp?->department?->unit_name ?: 'Office of the College Dean');
                        $suggested[] = [
                            'role_label' => 'Noted by:',
                            'tier_role' => 'noted',
                            'employee_id' => null,
                            'name' => '',
                            'designation' => 'Dean',
                            'office' => $targetUnitName,
                            'alignment' => 'left',
                            'position_column' => 1,
                            'employee_text' => '',
                        ];
                    }
                }

                foreach ($pastSigs as $pastSig) {
                    if ($pastSig->tier_role === 'prepared' || $pastSig->tier_role === 'noted' || str_contains(strtolower($pastSig->role_label ?? ''), 'prepared') || str_contains(strtolower($pastSig->role_label ?? ''), 'noted')) {
                        continue;
                    }

                    $suggested[] = [
                        'role_label' => $pastSig->role_label,
                        'tier_role' => $pastSig->tier_role,
                        'employee_id' => $pastSig->employee_id,
                        'name' => $pastSig->signatory_name,
                        'designation' => $pastSig->designation,
                        'office' => $pastSig->office,
                        'alignment' => $pastSig->alignment ?: 'left',
                        'position_column' => $pastSig->position_column ?: 1,
                        'employee_text' => $pastSig->signatory_name.($pastSig->designation ? ' ('.$pastSig->designation.')' : ''),
                    ];
                }

                return [
                    'source' => 'past_proposal',
                    'past_proposal_id' => $pastProposal->id,
                    'past_proposal_code' => $pastProposal->proposal_code,
                    'past_proposal_title' => $pastProposal->title,
                    'current_unit_id' => $currentUnitId,
                    'signatories' => $suggested,
                ];
            }
        }

        $matrices = DocumentSignatoryMatrix::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('process_type', 'EXTENSION PROPOSAL')
                    ->orWhere('doc_type_id', 43);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($matrices->isNotEmpty()) {
            $matrixSignatories = [];
            foreach ($matrices as $m) {
                if ($m->tier_role === 'prepared' || $m->assignment_type === 'dynamic_author') {
                    $matrixSignatories[] = [
                        'role_label' => $m->role_label ?: 'Prepared by:',
                        'tier_role' => 'prepared',
                        'employee_id' => $proposal->project_leader_employee_id,
                        'name' => $proposal->project_leader_name,
                        'designation' => $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader'),
                        'office' => $proponentEmp?->department?->unit_name ?: 'Project Proponent / Extension Team',
                        'alignment' => $m->alignment ?: 'left',
                        'position_column' => $m->position_column ?: 1,
                        'employee_text' => $proposal->project_leader_name.' ('.($proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader')).')',
                    ];
                } elseif ($m->tier_role === 'noted' || $m->assignment_type === 'dynamic_college_dean') {

                    $unitDean = null;
                    $unitModel = $currentUnitId ? Unit::find($currentUnitId) : null;
                    if ($unitModel && $unitModel->head_employee_id) {
                        $unitDean = Employee::with('department')->find($unitModel->head_employee_id);
                    }
                    if (! $unitDean && $currentUnitId) {
                        $unitDean = Employee::with('department')->where('tenant_id', $tenantId)
                            ->where('department_id', $currentUnitId)
                            ->where(function ($q) {
                                $q->where('designation', 'like', '%Dean%')
                                    ->orWhere('designation', 'like', '%College Head%')
                                    ->orWhere('designation', 'like', '%Head%')
                                    ->orWhere('designation', 'like', '%Director%');
                            })
                            ->first();
                    }
                    if (! $unitDean && $currentUnitId) {
                        $empDesig = EmployeeDesignation::where('department_id', $currentUnitId)
                            ->where(function ($q) {
                                $q->where('designation', 'like', '%Dean%')
                                    ->orWhere('designation', 'like', '%Head%');
                            })
                            ->first();
                        if ($empDesig && $empDesig->employee_id) {
                            $unitDean = Employee::with('department')->find($empDesig->employee_id);
                        }
                    }

                    $matrixSignatories[] = [
                        'role_label' => $m->role_label ?: 'Noted by:',
                        'tier_role' => 'noted',
                        'employee_id' => $unitDean?->id,
                        'name' => $unitDean ? $unitDean->full_name : ($unitModel?->head_name ?: ''),
                        'designation' => $unitDean ? ($unitDean->designation ?: 'Dean') : ($unitModel?->head_title ?: 'Dean'),
                        'office' => $unitDean?->department?->unit_name ?: ($unitModel?->unit_name ?: ($proponentEmp?->department?->unit_name ?: 'Office of the College Dean')),
                        'alignment' => $m->alignment ?: 'left',
                        'position_column' => $m->position_column ?: 1,
                        'employee_text' => $unitDean ? ($unitDean->full_name.' ('.($unitDean->designation ?: 'Dean').')') : '',
                    ];
                } else {
                    $matrixSignatories[] = [
                        'role_label' => $m->role_label,
                        'tier_role' => $m->tier_role,
                        'employee_id' => $m->employee_id,
                        'name' => $m->signatory_name,
                        'designation' => $m->designation,
                        'office' => $m->office,
                        'alignment' => $m->alignment ?: 'left',
                        'position_column' => $m->position_column ?: 1,
                        'employee_text' => $m->signatory_name.($m->designation ? ' ('.$m->designation.')' : ''),
                    ];
                }
            }

            return [
                'source' => 'matrix',
                'past_proposal_id' => null,
                'past_proposal_code' => null,
                'current_unit_id' => $currentUnitId,
                'signatories' => $matrixSignatories,
            ];
        }

        $proponentUnitName = $proponentEmp?->department?->unit_name
            ?: ($proposal->academicProgram?->department?->unit_name ?: 'College Extension Team');
        $defaultSignatories = [
            [
                'role_label' => 'Prepared by:',
                'tier_role' => 'prepared',
                'employee_id' => $proposal->project_leader_employee_id,
                'name' => $proposal->project_leader_name,
                'designation' => $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader'),
                'office' => 'Project Proponent / '.$proponentUnitName,
                'alignment' => 'left',
                'position_column' => 1,
                'employee_text' => $proposal->project_leader_name.' ('.($proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader')).')',
            ],
            [
                'role_label' => 'Reviewed by:',
                'tier_role' => 'reviewed_focal',
                'employee_id' => null,
                'name' => '',
                'designation' => 'Focal Person',
                'office' => 'Extension Unit',
                'alignment' => 'left',
                'position_column' => 1,
                'employee_text' => '',
            ],
            [
                'role_label' => 'Reviewed by:',
                'tier_role' => 'reviewed_coordinator',
                'employee_id' => null,
                'name' => '',
                'designation' => 'College/Campus Research/Extension Coordinator',
                'office' => 'College Research and Extension Unit',
                'alignment' => 'right',
                'position_column' => 2,
                'employee_text' => '',
            ],
            [
                'role_label' => 'Noted:',
                'tier_role' => 'noted_dean',
                'employee_id' => $unitDean?->id,
                'name' => $unitDean ? $unitDean->full_name : ($unitModel?->head_name ?: ''),
                'designation' => $unitDean ? ($unitDean->designation ?: 'College Dean/Program Chair') : ($unitModel?->head_title ?: 'College Dean/Program Chair'),
                'office' => $unitDean?->department?->unit_name ?: ($unitModel?->unit_name ?: 'Office of the College Dean'),
                'alignment' => 'left',
                'position_column' => 1,
                'employee_text' => $unitDean ? ($unitDean->full_name.' ('.($unitDean->designation ?: 'Dean').')') : '',
            ],
            [
                'role_label' => 'Noted:',
                'tier_role' => 'noted_director',
                'employee_id' => null,
                'name' => '',
                'designation' => 'Campus Director',
                'office' => 'Office of the Campus Director',
                'alignment' => 'right',
                'position_column' => 2,
                'employee_text' => '',
            ],
            [
                'role_label' => 'Recommending Approval:',
                'tier_role' => 'recommended_director',
                'employee_id' => null,
                'name' => 'ALVIE ELMA C. PLAZA, MA',
                'designation' => 'Extension Director',
                'office' => 'University Extension Directorate',
                'alignment' => 'center',
                'position_column' => 2,
                'employee_text' => 'ALVIE ELMA C. PLAZA, MA (Extension Director)',
            ],
            [
                'role_label' => 'Approved:',
                'tier_role' => 'approved_vpie',
                'employee_id' => null,
                'name' => 'ROMY JUN A. SUNICO, DTE',
                'designation' => 'Vice President for Research, Innovation and Extension',
                'office' => 'Office of the Vice President for RIE',
                'alignment' => 'center',
                'position_column' => 2,
                'employee_text' => 'ROMY JUN A. SUNICO, DTE (Vice President for RIE)',
            ],
        ];

        return [
            'source' => 'default_canonical',
            'past_proposal_id' => null,
            'past_proposal_code' => null,
            'current_unit_id' => $currentUnitId,
            'signatories' => $defaultSignatories,
        ];
    }

    public static function resolveSuggestedSignatoriesForPlan(ExtensionPlan $plan): array
    {
        $dummyProposal = new ExtensionProposal([
            'tenant_id' => $plan->tenant_id,
            'campus_id' => $plan->campus_id,
            'unit_id' => $plan->academicProgram?->department_id,
            'academic_program_id' => $plan->academic_program_id,
            'extension_plan_id' => $plan->id,
            'project_leader_employee_id' => $plan->prepared_by_employee_id ?: Auth::user()?->employee_id,
            'project_leader_name' => $plan->prepared_by_name ?: (Auth::user()?->name ?: 'Project Leader'),
            'project_leader_title' => 'Project Leader',
        ]);
        $dummyProposal->setRelation('academicProgram', $plan->academicProgram);
        $dummyProposal->setRelation('extensionPlan', $plan);

        return self::resolveSuggestedSignatories($dummyProposal);
    }

    public function getSuggestedSignatories(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with(['academicProgram', 'extensionPlan', 'projectLeaderEmployee'])->findOrFail($proposalId);

        $suggested = self::resolveSuggestedSignatories($proposal);

        return response()->json([
            'success' => true,
            'source' => $suggested['source'],
            'past_proposal_code' => $suggested['past_proposal_code'] ?? null,
            'past_proposal_title' => $suggested['past_proposal_title'] ?? null,
            'signatories' => $suggested['signatories'],
        ]);
    }

    /**
     * Show dedicated full page for configuring Extension Proposal signatories workflow (Centralized Modifier).
     */
    public function configureSignatories(Request $request, string $id): View
    {
        return app(CentralizedSignatoryController::class)
            ->editDocumentSignatories($request, 'extension_proposal', $id);
    }

    public function updateSignatories(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        if (strtolower((string) $proposal->status) === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Signatories and order cannot be updated because the proposal has already been approved.',
            ], 403);
        }

        $inputData = $request->all();
        if (is_string($request->input('signatories'))) {
            $decoded = json_decode($request->input('signatories'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $inputData['signatories'] = $decoded;
            }
        }

        self::syncSignatoriesAndRoutes($proposal, $inputData);

        if (method_exists($proposal, 'recordAuditTrail')) {
            $proposal->recordAuditTrail('Signatories Configured', 'Updated dynamic approval signatories and DTS routing workflow.', Auth::user());
        }

        $signatories = DocumentSignatory::where('document_type', $proposal->getMorphClass())
            ->where('document_id', $proposal->id)
            ->orderBy('sort_order', 'asc')
            ->get();
        $routes = ($proposal->dts_record_id && Schema::hasTable('dts_record_routes'))
            ? RecordRoute::where('record_id', $proposal->dts_record_id)->orderBy('step_number')->get()
            : collect();

        return response()->json([
            'success' => true,
            'message' => 'Signatories and DTS routing workflow updated successfully.',
            'signatories' => $signatories,
            'routes' => $routes,
        ]);
    }

    public function submitForReview(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with(['dtsRecord'])->findOrFail($proposalId);

        if ($proposal->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft proposals can be submitted for review.',
            ], 422);
        }

        self::syncSignatoriesAndRoutes($proposal);

        $proposal->status = 'submitted';
        $proposal->prepared_at = now();
        $proposal->save();

        DocumentSignatory::where('document_type', $proposal->getMorphClass())
            ->where('document_id', $proposal->id)
            ->where('tier_role', 'prepared')
            ->update([
                'status' => 'signed',
                'signed_at' => now(),
                'signed_by_user_id' => Auth::id() ?? $proposal->project_leader_user_id,
                'remarks' => 'Proposal officially submitted by Project Leader.',
            ]);

        if ($proposal->dtsRecord) {
            $step1Route = RecordRoute::where('record_id', $proposal->dts_record_id)
                ->where('step_number', 1)
                ->first();

            $proposal->dtsRecord->update([
                'TrailStatus' => 'In-Transit',
                'location' => 'Project Proponent',
                'Destination' => $step1Route?->unit_id,
            ]);

            if ($step1Route) {
                $step1Route->update([
                    'status' => 'in_transit',
                ]);
            }

            DtsRoutingService::recordTrailLog(
                $proposal->dtsRecord,
                'Submitted for Review & College Endorsement (Step 1)',
                'In-Transit',
                $request->input('remarks', 'Project proposal submitted for formal technical review and collegiate endorsement.'),
                Auth::id()
            );
        }

        if (method_exists($proposal, 'recordAuditTrail')) {
            $proposal->recordAuditTrail('Proposal Submitted', 'Submitted project proposal for collegiate evaluation and administrative review.', Auth::user());
        }

        return response()->json([
            'success' => true,
            'message' => 'Extension Proposal submitted for review successfully! Document routing has commenced.',
            'status' => 'submitted',
            'status_badge' => $proposal->status_badge,
            'redirect_url' => route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'routing']),
        ]);
    }

    public function show(Request $request, string $id): View
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;

        $relations = [
            'extensionPlan.academicProgram.campus',
            'extensionPlan.campus',
            'extensionPlan.activities.uploadedByUser',
            'extensionPlan.activities.uploadedDocument',
            'academicProgram',
            'campus',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'uploadedDocuments',
            'physicallySignedDocument',
            'documentAuditTrails.user',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
            'sdgs',
        ];
        if (Schema::hasTable('dts_records')) {
            $relations[] = 'dtsRecord.routes';
            $relations[] = 'dtsRecord.logs';
        }

        $proposal = ExtensionProposal::with($relations)->findOrFail($proposalId);

        if (Schema::hasTable('dts_records') && (empty($proposal->tracking_number) || empty($proposal->dts_record_id))) {
            DtsRoutingService::registerExtensionProposal($proposal);
            $proposal->refresh();
        }

        $existingSigCount = DocumentSignatory::where('document_type', $proposal->getMorphClass())
            ->where('document_id', $proposal->id)
            ->count();
        if ($existingSigCount === 0) {
            self::syncSignatoriesAndRoutes($proposal);
            $proposal->refresh();
        }

        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($proposal);
        $recordRoutes = (Schema::hasTable('dts_record_routes') && $proposal->dts_record_id)
            ? RecordRoute::where('record_id', $proposal->dts_record_id)->orderBy('step_number', 'asc')->get()
            : collect();

        $activeTab = $request->query('tab', 'overview');
        $validTabs = ['overview', 'activities', 'technical', 'routing', 'pdf', 'attachments', 'accomplishment'];
        if (! in_array($activeTab, $validTabs, true)) {
            $activeTab = 'overview';
        }

        $auditTrails = $proposal->documentAuditTrails()->with(['user', 'uploadedDocument'])->latest()->get();
        $institution = TenantContext::getInstitution();
        $allSdgs = SustainableDevelopmentGoal::ordered()->get();
        $bannerPrograms = ExtensionBannerProgram::active()->ordered()->get();
        $extensionAgendas = ExtensionAgenda::active()->ordered()->get();

        // Fetch live SNSU Central DTS tracking status & routing trail
        $snsuTracking = SnsuTrackingService::fetchTracking($proposal->tracking_number);

        return view('dss.rie.extension_proposals.show', compact('proposal', 'activeTab', 'auditTrails', 'signatories', 'recordRoutes', 'institution', 'allSdgs', 'bannerPrograms', 'extensionAgendas', 'snsuTracking'));
    }

    /**
     * Connect or update official SNSU Central DTS Tracking Number.
     */
    public function connectTracking(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:60',
        ]);

        $res = SnsuTrackingService::attachTrackingNumber($proposal, $validated['tracking_number']);

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    /**
     * Live fetch document tracking status & history from SNSU DTS API.
     */
    public function fetchLiveTracking(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $data = SnsuTrackingService::fetchTracking($proposal->tracking_number);

        return response()->json([
            'success' => $data !== null,
            'data' => $data,
        ]);
    }

    public function sign(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);
        $user = Auth::user();

        $validated = $request->validate([
            'tier_role' => 'required|string|in:prepared,reviewed,noted,concurred,recommended,approved',
            'signature_img' => 'nullable|string',
            'signature_type' => 'nullable|string|in:draw,drawn,type,font,upload,typed',
            'remarks' => 'nullable|string|max:500',
        ]);

        $tierRole = $validated['tier_role'];

        $roleLabels = [
            'prepared' => 'Prepared by (Project Leader)',
            'reviewed' => 'Reviewed by (Extension Head)',
            'noted' => 'Noted by (Dean)',
            'concurred' => 'Concurred by (Extension Director)',
            'recommended' => 'Recommending Approval (VP RIE)',
            'approved' => 'Approved by (University President)',
        ];

        $sigRecord = CentralizedSignatoryService::recordDigitalSignature(
            $proposal,
            $tierRole,
            $user,
            [
                'role_label' => $roleLabels[$tierRole] ?? ucfirst($tierRole).' by',
                'signature_img' => $validated['signature_img'] ?? null,
                'signature_type' => $validated['signature_type'] ?? 'drawn',
                'remarks' => $validated['remarks'] ?? 'Verified and digitally signed.',
                'designation' => $proposal->prepared_by_designation,
            ]
        );

        if ($tierRole === 'reviewed' && in_array($proposal->status, ['submitted', 'draft'])) {
            $proposal->status = 'reviewed';
        } elseif ($tierRole === 'noted') {
            $proposal->status = 'noted';
        } elseif ($tierRole === 'concurred') {
            $proposal->status = 'concurred';
        } elseif ($tierRole === 'recommended') {
            $proposal->status = 'recommended';
        } elseif ($tierRole === 'approved') {
            $proposal->status = 'approved';
        }

        $proposal->save();

        if ($proposal->dtsRecord) {
            $forAction = match ($tierRole) {
                'prepared' => 'Proposal Prepared & Submitted for Technical Review',
                'reviewed' => 'Reviewed by Extension Head - Endorsed for Dean Note',
                'noted' => 'Noted by Dean - Endorsed for Extension Director Concurrence',
                'concurred' => 'Concurred by Extension Director - Endorsed for VP RIE Recommendation',
                'recommended' => 'Recommending Approval by VP RIE - Endorsed for Presidential Approval',
                'approved' => 'Officially Approved by University President',
                default => 'Digitally Signed Tier',
            };
            $trailStatus = ($tierRole === 'approved') ? 'Done' : 'In-Transit';
            DtsRoutingService::recordTrailLog(
                $proposal->dtsRecord,
                $forAction,
                $trailStatus,
                $validated['remarks'] ?? "Digitally signed as {$roleLabels[$tierRole]}.",
                $user?->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Proposal digitally signed successfully as '{$roleLabels[$tierRole]}'.",
            'signature' => $sigRecord,
            'status' => $proposal->status,
            'status_badge' => $proposal->status_badge,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        if (! $proposal->is_editable) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This proposal has already been submitted and cannot be modified.'], 403);
            }

            return back()->with('error', 'This proposal has already been submitted and cannot be modified.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'banner_program' => 'nullable|string|max:100',
            'banner_program_other' => 'nullable|string|max:255',
            'agenda_priorities' => 'nullable|array',
            'beneficiaries' => 'nullable|string',
            'target_beneficiaries_data' => 'nullable',
            'implementing_agency' => 'nullable|string|max:255',
            'implementing_colleges' => 'nullable',
            'lead_agency' => 'nullable|string|max:255',
            'collaborating_agencies' => 'nullable|string',
            'collaborating_agencies_data' => 'nullable',
            'duration_start' => 'nullable|date',
            'duration_end' => 'nullable|date',
            'duration_text' => 'nullable|string|max:150',
            'duration_months' => 'nullable|integer',
            'project_location' => 'nullable|string|max:255',
            'budget_institution_share' => 'nullable|string',
            'budget_snsu_share' => 'nullable|string',
            'budget_partner_share' => 'nullable|string',
            'fund_source_internal' => 'nullable|string',
            'fund_source_external' => 'nullable|string',
            'budget_total_display' => 'nullable|string|max:100',
            'budget_cash_amount' => 'nullable|numeric|min:0',
            'project_leader_name' => 'nullable|string|max:191',
            'project_leader_title' => 'nullable|string|max:191',
            'project_leader_gender' => 'nullable|string|max:20',
            'sdg_alignments' => 'nullable|array',
        ]);

        $targetBeneficiaries = $request->input('target_beneficiaries_data');
        if (is_string($targetBeneficiaries)) {
            $decoded = json_decode($targetBeneficiaries, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $targetBeneficiaries = $decoded;
            }
        }

        $collaboratingAgenciesData = $request->input('collaborating_agencies_data');
        if (is_string($collaboratingAgenciesData)) {
            $decoded = json_decode($collaboratingAgenciesData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $collaboratingAgenciesData = $decoded;
            }
        }

        $implementingColleges = $request->input('implementing_colleges');
        if (is_string($implementingColleges)) {
            $decoded = json_decode($implementingColleges, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $implementingColleges = $decoded;
            } else {
                $implementingColleges = array_filter(array_map('trim', explode(',', $implementingColleges)));
            }
        }

        $proposal->fill([
            'title' => $validated['title'],
            'banner_program' => $validated['banner_program'] ?? $proposal->banner_program,
            'banner_program_other' => $validated['banner_program_other'] ?? $proposal->banner_program_other,
            'agenda_priorities' => $validated['agenda_priorities'] ?? $proposal->agenda_priorities,
            'sdg_alignments' => $validated['sdg_alignments'] ?? $proposal->sdg_alignments,
            'beneficiaries' => $validated['beneficiaries'] ?? $proposal->beneficiaries,
            'target_beneficiaries_data' => $targetBeneficiaries ?: $proposal->target_beneficiaries_data,
            'implementing_agency' => $validated['implementing_agency'] ?? $proposal->implementing_agency,
            'implementing_colleges' => $implementingColleges ?: $proposal->implementing_colleges,
            'lead_agency' => $validated['lead_agency'] ?? $proposal->lead_agency,
            'collaborating_agencies' => $validated['collaborating_agencies'] ?? $proposal->collaborating_agencies,
            'collaborating_agencies_data' => $collaboratingAgenciesData ?: $proposal->collaborating_agencies_data,
            'duration_start' => $validated['duration_start'] ?? $proposal->duration_start,
            'duration_end' => $validated['duration_end'] ?? $proposal->duration_end,
            'duration_text' => $validated['duration_text'] ?? $proposal->duration_text,
            'duration_months' => $validated['duration_months'] ?? $proposal->duration_months,
            'project_location' => $validated['project_location'] ?? $proposal->project_location,
            'fund_source_internal' => $validated['fund_source_internal'] ?? ($validated['budget_institution_share'] ?? $proposal->fund_source_internal),
            'fund_source_external' => $validated['fund_source_external'] ?? ($validated['budget_partner_share'] ?? $proposal->fund_source_external),
            'budget_institution_share' => $validated['budget_institution_share'] ?? $proposal->budget_institution_share,
            'budget_partner_share' => $validated['budget_partner_share'] ?? $proposal->budget_partner_share,
            'budget_total_display' => ! empty($validated['budget_total_display']) ? $validated['budget_total_display'] : ($proposal->budget_total_display ?: 'Grand Total: No Cash Requirement'),
            'budget_cash_amount' => isset($validated['budget_cash_amount']) && $validated['budget_cash_amount'] !== '' ? (float) $validated['budget_cash_amount'] : (float) ($proposal->budget_cash_amount ?? 0),
            'project_leader_name' => ! empty($validated['project_leader_name']) ? $validated['project_leader_name'] : ($proposal->project_leader_name ?: 'Project Leader'),
            'project_leader_title' => ! empty($validated['project_leader_title']) ? $validated['project_leader_title'] : ($proposal->project_leader_title ?: 'Project Leader'),
            'project_leader_gender' => $validated['project_leader_gender'] ?? $proposal->project_leader_gender,
            'asst_project_leader_title' => $proposal->asst_project_leader_title ?: 'Assistant Project Leader',
        ]);

        $proposal->save();

        if (! empty($validated['sdg_alignments'])) {
            self::syncProposalSdgs($proposal, $validated['sdg_alignments']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Basic Information updated successfully.',
                'proposal' => $proposal,
            ]);
        }

        return redirect()->route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'overview'])
            ->with('success', 'Basic Information updated successfully.');
    }

    public function updateTechnical(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        if (! $proposal->is_editable) {
            return response()->json([
                'success' => false,
                'message' => 'This proposal has already been submitted and cannot be modified.',
            ], 403);
        }

        if (is_string($request->input('people_involved'))) {
            $decoded = json_decode($request->input('people_involved'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['people_involved' => $decoded]);
            }
        }

        if (is_string($request->input('schedule_of_activities'))) {
            $decoded = json_decode($request->input('schedule_of_activities'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['schedule_of_activities' => $decoded]);
            }
        }

        if (is_string($request->input('sdg_alignments'))) {
            $decoded = json_decode($request->input('sdg_alignments'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['sdg_alignments' => $decoded]);
            }
        }

        if (is_string($request->input('budgetary_requirements'))) {
            $decoded = json_decode($request->input('budgetary_requirements'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['budgetary_requirements' => $decoded]);
            }
        }

        if (is_string($request->input('specific_objectives'))) {
            $decoded = json_decode($request->input('specific_objectives'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['specific_objectives' => $decoded]);
            }
        }

        if (is_string($request->input('expected_outputs'))) {
            $decoded = json_decode($request->input('expected_outputs'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['expected_outputs' => $decoded]);
            }
        }

        if (is_string($request->input('risk_management'))) {
            $decoded = json_decode($request->input('risk_management'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['risk_management' => $decoded]);
            }
        }

        if (is_string($request->input('org_structure'))) {
            $decoded = json_decode($request->input('org_structure'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $request->merge(['org_structure' => $decoded]);
            }
        }

        if (! is_array($request->input('org_structure'))) {
            $orgStructureInput = [
                'mode' => $request->input('org_mode', 'dynamic'),
                'dean_title' => $request->input('org_dean_title'),
                'dean_name' => $request->input('org_dean_name'),
                'coord_title' => $request->input('org_coord_title'),
                'coord_name' => $request->input('org_coord_name'),
                'focal_title' => $request->input('org_focal_title'),
                'focal_name' => $request->input('org_focal_name'),
                'beneficiaries_title' => $request->input('org_beneficiaries_title'),
                'beneficiaries_name' => $request->input('org_beneficiaries_name'),
            ];
            $request->merge(['org_structure' => $orgStructureInput]);
        }

        $validated = $request->validate([
            'introduction' => 'nullable|string',
            'general_objective' => 'nullable|string',
            'specific_objectives' => 'nullable',
            'objectives' => 'nullable',
            'objectives_text' => 'nullable|string',
            'expected_outputs' => 'nullable',
            'relevance_narrative' => 'nullable|string',
            'sdg_alignments' => 'nullable|array',
            'development_gap' => 'nullable|string',
            'pdp_responsiveness' => 'nullable|string',
            'schedule_of_activities' => 'nullable|array',
            'sustainability_measures' => 'nullable|string',
            'people_involved' => 'nullable|array',
            'org_structure' => 'nullable|array',
            'risk_management' => 'nullable',
            'budgetary_requirements' => 'nullable|array',
            'org_chart_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'remove_org_chart' => 'nullable|string',
        ]);

        if ($request->has('introduction')) {
            $proposal->introduction = $this->sanitizeRichHtml($validated['introduction']);
        }

        if ($request->has('general_objective')) {
            $proposal->general_objective = $this->sanitizeRichHtml($validated['general_objective']);
        }

        if ($request->has('specific_objectives')) {
            $proposal->specific_objectives = $validated['specific_objectives'];
        }

        if ($request->has('expected_outputs')) {
            $proposal->expected_outputs = $validated['expected_outputs'];
        }

        if ($request->has('risk_management')) {
            $proposal->risk_management = $validated['risk_management'];
        }

        if ($request->has('objectives_text') || $request->has('objectives')) {
            $rawObj = $request->input('objectives_text') ?? $request->input('objectives');
            if (is_string($rawObj)) {
                $cleaned = $this->sanitizeRichHtml($rawObj);
                $proposal->objectives = [
                    'html' => $cleaned,
                    'raw' => strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ["\n", "\n", "\n", "\n\n"], $cleaned)),
                ];
            } elseif (is_array($rawObj)) {
                $proposal->objectives = $rawObj;
            }
        }

        if ($request->has('relevance_narrative')) {
            $proposal->relevance_narrative = $this->sanitizeRichHtml($validated['relevance_narrative']);
        }
        if ($request->has('sdg_alignments')) {
            $proposal->sdg_alignments = $validated['sdg_alignments'];
            self::syncProposalSdgs($proposal, $validated['sdg_alignments']);
        }
        if ($request->has('development_gap')) {
            $proposal->development_gap = $this->sanitizeRichHtml($validated['development_gap']);
        }
        if ($request->has('pdp_responsiveness')) {
            $proposal->pdp_responsiveness = $this->sanitizeRichHtml($validated['pdp_responsiveness']);
        }
        if ($request->has('sustainability_measures')) {
            $proposal->sustainability_measures = $this->sanitizeRichHtml($validated['sustainability_measures']);
        }

        if ($request->has('schedule_of_activities')) {
            $proposal->schedule_of_activities = $validated['schedule_of_activities'];
        }

        if ($request->has('people_involved')) {
            $proposal->people_involved = $validated['people_involved'];

            if (isset($validated['people_involved']['rows']) && is_array($validated['people_involved']['rows'])) {
                $syncedProponents = [];
                foreach ($validated['people_involved']['rows'] as $pRow) {
                    $pName = trim($pRow['name'] ?? '');
                    if ($pName !== '') {
                        $syncedProponents[] = [
                            'name' => $pName,
                            'role' => trim($pRow['role'] ?? 'Member'),
                            'employee_id' => $pRow['employee_id'] ?? null,
                            'type' => $pRow['type'] ?? (str_contains(strtolower($pName), 'student') || str_contains(strtolower($pName), 'volunteer') ? 'manual' : 'faculty'),
                        ];
                    }
                }
                if (! empty($syncedProponents)) {
                    $proposal->proponents = $syncedProponents;
                }
            }
        }

        if ($request->has('budgetary_requirements')) {
            $proposal->budgetary_requirements = $validated['budgetary_requirements'];

            $grandTotal = 0.0;
            if (isset($validated['budgetary_requirements']['sections']) && is_array($validated['budgetary_requirements']['sections'])) {
                foreach ($validated['budgetary_requirements']['sections'] as $sec) {
                    if (isset($sec['items']) && is_array($sec['items'])) {
                        foreach ($sec['items'] as $item) {
                            $grandTotal += (float) ($item['amount'] ?? (((float) ($item['qty'] ?? 0)) * ((float) ($item['cost'] ?? 0))));
                        }
                    } elseif (isset($sec['subtotal'])) {
                        $grandTotal += (float) $sec['subtotal'];
                    }
                }
            } elseif (isset($validated['budgetary_requirements']['items']) && is_array($validated['budgetary_requirements']['items'])) {
                foreach ($validated['budgetary_requirements']['items'] as $item) {
                    if (($item['type'] ?? '') === 'row') {
                        $grandTotal += (float) ($item['amount'] ?? (((float) ($item['qty'] ?? 0)) * ((float) ($item['cost'] ?? 0))));
                    }
                }
            } elseif (isset($validated['budgetary_requirements']['grand_total'])) {
                $grandTotal = (float) $validated['budgetary_requirements']['grand_total'];
            }

            if ($grandTotal > 0) {
                $proposal->budget_cash_amount = $grandTotal;
                $proposal->budget_total_display = 'Grand Total: P'.number_format($grandTotal, 2);
            }
        }

        $orgStructure = $proposal->org_structure ?? [];
        if ($request->has('org_structure') && is_array($validated['org_structure'])) {
            $orgStructure = array_merge($orgStructure, $validated['org_structure']);
        }

        if ($request->hasFile('org_chart_file')) {
            $file = $request->file('org_chart_file');
            $tenantId = $proposal->tenant_id ?: (TenantContext::getTenantId() ?: 1);
            $dir = "tenants/{$tenantId}/proposals/org_charts";
            $filename = 'org_chart_'.$proposal->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $storedPath = $file->storeAs($dir, $filename, 'public');

            $orgStructure['mode'] = 'custom';
            $orgStructure['custom_image'] = $storedPath;
        } elseif ($request->input('remove_org_chart') === '1') {
            $orgStructure['mode'] = 'dynamic';
            $orgStructure['custom_image'] = null;
        }

        $proposal->org_structure = $orgStructure;
        $proposal->save();

        return response()->json([
            'success' => true,
            'message' => 'Technical Description updated successfully.',
            'org_chart_url' => $proposal->org_chart_url,
            'org_structure' => $proposal->resolved_org_structure,
            'people_involved' => $proposal->resolved_people_involved,
            'schedule_of_activities' => $proposal->resolved_schedule_of_activities,
            'expected_outputs' => $proposal->resolved_expected_outputs,
            'risk_management' => $proposal->resolved_risk_management,
            'budgetary_requirements' => $proposal->resolved_budgetary_requirements,
            'budget_total_display' => $proposal->budget_total_display,
            'budget_cash_amount' => $proposal->budget_cash_amount,
        ]);
    }

    protected function sanitizeRichHtml(?string $html): string
    {
        if (! $html) {
            return '';
        }

        for ($i = 0; $i < 5; $i++) {
            if (! str_contains($html, '&amp;') && ! str_contains($html, '&lt;')) {
                break;
            }
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $html = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $html);

        return trim($html);
    }

    public function pdf(Request $request, string $id): Response
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with([
            'extensionPlan.academicProgram.campus',
            'extensionPlan.campus',
            'academicProgram',
            'campus',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
            'dtsRecord.routes',
        ])->findOrFail($proposalId);

        $institution = TenantContext::getInstitution();
        $paper = $request->query('paper', 'A4');
        $template = $request->query('template', 'executive');
        $download = $request->boolean('download');

        if ($request->query('format') === 'html' || $request->query('view') === 'html') {
            $bannerPrograms = ExtensionBannerProgram::active()->ordered()->get();
            $extensionAgendas = ExtensionAgenda::active()->ordered()->get();

            return response()->view('dss.rie.extension_proposals.pdf', compact('proposal', 'institution', 'bannerPrograms', 'extensionAgendas'));
        }

        $pdf = DocumentPdfGenerator::fromExtensionProposal($proposal, $institution, $paper, $template);
        $prefix = $institution?->school_acronym ?: 'SNSU';
        $fileName = "{$prefix}_Extension_Proposal_{$proposal->proposal_code}.pdf";

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline')."; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function generateSdgNarrative(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $validated = $request->validate([
            'selected_sdgs' => 'nullable|array',
            'selected_sdgs.*' => 'integer|min:1|max:17',
            'indirect_sdgs' => 'nullable|array',
            'indirect_sdgs.*' => 'integer|min:1|max:17',
        ]);

        $selectedNums = array_map('intval', (array) ($validated['selected_sdgs'] ?? []));
        $indirectNums = array_map('intval', (array) ($validated['indirect_sdgs'] ?? []));

        if (empty($selectedNums)) {
            $selectedNums = array_map('intval', (array) ($proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 16]));
        }
        if (empty($indirectNums)) {
            $indirectNums = array_map('intval', (array) ($proposal->resolved_sdg_alignments['indirect'] ?? []));
        }

        $allSdgs = [
            1 => ['title' => 'No Poverty',                           'focus' => 'socioeconomic upliftment and digital inclusion for vulnerable community sectors'],
            2 => ['title' => 'Zero Hunger',                          'focus' => 'community nutrition monitoring, sustainable agricultural distribution, and food security mechanisms'],
            3 => ['title' => 'Good Health & Well-being',             'focus' => 'campus health protocols, preventative safety systems, and rapid emergency reporting'],
            4 => ['title' => 'Quality Education',                    'focus' => 'equitable, inclusive digital learning environments, automated learner tracking, and institutional capability-building'],
            5 => ['title' => 'Gender Equality',                      'focus' => 'safe, inclusive, and gender-equitable access to technology and educational opportunities'],
            6 => ['title' => 'Clean Water & Sanitation',             'focus' => 'institutional hygiene awareness, resource monitoring, and sanitation management protocols'],
            7 => ['title' => 'Affordable & Clean Energy',            'focus' => 'energy-efficient digital workflows, low-power computational systems, and sustainability'],
            8 => ['title' => 'Decent Work & Economic Growth',        'focus' => 'technological skills acquisition, workforce digital readiness, and institutional efficiency'],
            9 => ['title' => 'Industry, Innovation & Infrastructure', 'focus' => 'technological innovation, modern digital infrastructure, and reliable cloud-based service delivery'],
            10 => ['title' => 'Reduced Inequalities',                 'focus' => 'bridging the digital divide between urban and rural partner schools through accessible digital platforms'],
            11 => ['title' => 'Sustainable Cities & Communities',     'focus' => 'cultivating safer, smarter, and resilient campus communities through proactive digital monitoring'],
            12 => ['title' => 'Responsible Consumption & Production', 'focus' => 'paperless administrative workflows, resource conservation, and sustainable institutional practices'],
            13 => ['title' => 'Climate Action',                       'focus' => 'minimizing environmental footprint through digital transformation and paperless communications'],
            14 => ['title' => 'Life Below Water',                     'focus' => 'marine biodiversity conservation awareness and aquatic environmental tracking'],
            15 => ['title' => 'Life on Land',                         'focus' => 'terrestrial environmental protection, green campus initiatives, and digital environmental stewardship'],
            16 => ['title' => 'Peace, Justice & Strong Institutions', 'focus' => 'institutional transparency, student safety protection, and verifiable governance records'],
            17 => ['title' => 'Partnerships for the Goals',           'focus' => 'collaborative multistakeholder partnerships between the university, local government units, and partner schools'],
        ];

        $directTitles = [];
        $directDetails = [];
        foreach ($selectedNums as $num) {
            if (isset($allSdgs[$num])) {
                $directTitles[] = "SDG {$num} – {$allSdgs[$num]['title']}";
                $directDetails[] = "<strong>SDG {$num} ({$allSdgs[$num]['title']})</strong> through {$allSdgs[$num]['focus']}";
            }
        }

        $indirectTitles = [];
        foreach ($indirectNums as $num) {
            if (isset($allSdgs[$num])) {
                $indirectTitles[] = "SDG {$num} – {$allSdgs[$num]['title']}";
            }
        }

        $directTitlesList = implode(', ', $directTitles);
        $indirectTitlesList = implode(', ', $indirectTitles);

        $institution = TenantContext::getInstitution();
        $schoolName = $institution?->institution_name ?: 'Surigao del Norte State University';
        $schoolAcronym = $institution?->school_acronym ?: 'SNSU';

        $stripHtml = static fn (string $html): string => trim(strip_tags(html_entity_decode($html)));
        $projectTitle = $proposal->title ?? 'the extension project';
        $introText = $stripHtml($proposal->introduction ?? '');
        $objectivesText = $stripHtml($proposal->objectives_text ?? '');

        $groqKey = config('services.groq.key') ?: env('GROQ_API_KEY');
        $geminiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        if (! empty($groqKey) || ! empty($geminiKey)) {
            try {
                $aiService = app(DssAiAssistantService::class);

                $indirectSection = ! empty($indirectTitlesList)
                    ? "Indirect / Tangential SDG Contributions (not primary focus): {$indirectTitlesList}\n"
                    : '';

                $prompt =
                    "Write an official, high-impact SDG Relevance Narrative (2–3 clear academic paragraphs) for a University Extension Project Proposal at {$schoolName} ({$schoolAcronym}).\n\n".
                    "Project Title: {$projectTitle}\n".
                    "Introduction / Background: {$introText}\n".
                    "Project Objectives: {$objectivesText}\n".
                    "Primary (Direct) SDG Alignments: {$directTitlesList}\n".
                    $indirectSection.
                    "\nGuidelines:\n".
                    "- Paragraph 1: Directly reference the project title. Explain how the project’s introduction and objectives align with the primary SDGs.\n".
                    "- Paragraph 2: Highlight concrete institutional impacts derived from the project objectives, using formal language suitable for CHED evaluators.\n".
                    (! empty($indirectTitlesList)
                        ? "- Paragraph 3: Briefly acknowledge indirect SDG contributions, noting they are not primary objectives but natural downstream effects.\n"
                        : '').
                    "- Maintain a formal, scholarly, and administrative tone throughout.\n".
                    "- Output plain narrative text only. Do NOT include markdown headers, bullet points, or conversational filler.\n".
                    '- Reference the actual project title naturally within the narrative.';

                $aiResult = $aiService->respond($prompt, []);
                $modelName = (string) ($aiResult['model'] ?? '');
                $isActualLlm = str_contains($modelName, 'Groq') || str_contains($modelName, 'Gemini');

                if ($isActualLlm && ! empty($aiResult['reply']) && strlen(trim($aiResult['reply'])) > 80 && ! str_contains($aiResult['reply'], 'Institutional Document Tracking')) {
                    $cleaned = trim(str_replace(['```html', '```', '**', '##', '#'], '', $aiResult['reply']));
                    $paras = array_filter(explode("\n", str_replace(["\r\n", "\r"], "\n", $cleaned)));
                    $formatted = implode('<br><br>', array_map('trim', $paras));

                    return response()->json([
                        'success' => true,
                        'narrative' => $formatted,
                        'source' => $modelName,
                        'selected_count' => count($selectedNums),
                        'indirect_count' => count($indirectNums),
                    ]);
                }
            } catch (\Throwable) {

            }
        }

        $detailsList = implode('; (b) ', $directDetails);

        $p1 = "“{$projectTitle}” directly advances {$schoolName}’s institutional mandate to champion the United Nations Sustainable Development Goals (SDGs), with primary alignment on {$directTitlesList}. By operationalizing faculty expertise, cutting-edge technology, and purposeful community engagement, the project addresses critical developmental gaps and delivers lasting value to its partner beneficiaries.";

        $p2 = ! empty($directDetails)
            ? "Specifically, “{$projectTitle}” creates measurable socio-technical impact by: (a) {$detailsList}. Through this collaborative and evidence-based framework, {$schoolAcronym} reinforces its mandate of delivering inclusive, high-impact extension services that bridge digital disparities and foster enduring community resilience."
            : '';

        $p3 = ! empty($indirectTitles)
            ? "Furthermore, while not among its primary objectives, “{$projectTitle}” is also expected to contribute indirectly to {$indirectTitlesList}. These downstream effects reflect the interconnected nature of sustainable development, where targeted educational and digital interventions inevitably catalyze broader socio-economic and environmental benefits across the community ecosystem."
            : '';

        return response()->json([
            'success' => true,
            'narrative' => implode('<br><br>', array_filter([$p1, $p2, $p3])),
            'source' => 'DSS Institutional Semantic Engine',
            'selected_count' => count($selectedNums),
            'indirect_count' => count($indirectNums),
        ]);
    }

    /**
     * Update post-implementation accomplishment and terminal narrative report
     */
    public function updateAccomplishment(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $validated = $request->validate([
            'implementation_status' => 'nullable|string|in:Not Started,Ongoing,Completed,Delayed,Terminated',
            'actual_beneficiaries_total' => 'nullable|integer|min:0',
            'actual_beneficiaries_male' => 'nullable|integer|min:0',
            'actual_beneficiaries_female' => 'nullable|integer|min:0',
            'actual_beneficiaries_sectors' => 'nullable|string|max:255',
            'actual_budget_spent' => 'nullable|numeric|min:0',
            'executive_narrative' => 'nullable|string',
            'activity_narrative' => 'nullable|string',
            'summary_accomplishments' => 'nullable|string',
            'problems_encountered' => 'nullable|string',
            'solutions_adopted' => 'nullable|string',
            'impact_feedback' => 'nullable|string',
            'date_completed' => 'nullable|date',
        ]);

        $metadata = $proposal->metadata ?? [];
        $metadata['accomplishment_report'] = array_merge($metadata['accomplishment_report'] ?? [], $validated, [
            'updated_at' => now()->toIso8601String(),
            'updated_by_user_id' => Auth::id(),
            'updated_by_name' => Auth::user()?->name ?? 'Proponent',
        ]);

        $proposal->metadata = $metadata;
        $proposal->save();

        if (method_exists($proposal, 'recordAuditTrail')) {
            $proposal->recordAuditTrail(
                'Accomplishment Report Updated',
                'Recorded actual community beneficiaries, fund liquidation, and narrative report findings.',
                Auth::user()
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Post-implementation accomplishment report updated successfully!',
            'accomplishment' => $metadata['accomplishment_report'],
        ]);
    }

    /**
     * Render the official Generated Accomplishment & Terminal Narrative Report document.
     */
    public function accomplishmentReport(Request $request, string $id): View
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with([
            'campus',
            'academicProgram.campus',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'uploadedDocuments',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
        ])->findOrFail($proposalId);

        $institution = TenantContext::getInstitution();
        $acc = $proposal->metadata['accomplishment_report'] ?? [];
        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($proposal);

        $plannedBudget = (float) ($proposal->budget_cash_amount ?? 0);
        $actualSpent = (float) ($acc['actual_budget_spent'] ?? 0);
        $remainingBudget = max(0, $plannedBudget - $actualSpent);
        $utilizationRate = $plannedBudget > 0 ? min(100, round(($actualSpent / $plannedBudget) * 100, 1)) : 0;

        return view('dss.rie.extension_proposals.accomplishment_report', compact(
            'proposal',
            'institution',
            'acc',
            'signatories',
            'plannedBudget',
            'actualSpent',
            'remainingBudget',
            'utilizationRate'
        ));
    }

    /**
     * Generate the official Terminal Narrative Report directly as a downloadable / streamable binary PDF.
     */
    public function accomplishmentPdf(Request $request, string $id): Response
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with([
            'campus',
            'academicProgram.campus',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'uploadedDocuments',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
        ])->findOrFail($proposalId);

        $institution = TenantContext::getInstitution();
        $acc = $proposal->metadata['accomplishment_report'] ?? [];
        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($proposal);

        $plannedBudget = (float) ($proposal->budget_cash_amount ?? 0);
        $actualSpent = (float) ($acc['actual_budget_spent'] ?? 0);
        $remainingBudget = max(0, $plannedBudget - $actualSpent);
        $utilizationRate = $plannedBudget > 0 ? min(100, round(($actualSpent / $plannedBudget) * 100, 1)) : 0;

        // Resolve logo base64 for reliable Dompdf rendering
        $logoBase64 = null;
        $logoPath = null;
        if (! empty($institution?->logo_path) && file_exists(public_path($institution->logo_path))) {
            $logoPath = public_path($institution->logo_path);
        } elseif (file_exists(public_path('assets/images/snsu_logo.png'))) {
            $logoPath = public_path('assets/images/snsu_logo.png');
        }
        if ($logoPath && file_exists($logoPath)) {
            $logoData = @file_get_contents($logoPath);
            if ($logoData) {
                $logoMime = @mime_content_type($logoPath) ?: 'image/png';
                $logoBase64 = 'data:'.$logoMime.';base64,'.base64_encode($logoData);
            }
        }

        // Resolve uploaded activity photos into base64 data URIs for interleaved narrative
        $evidencePhotos = [];
        if ($proposal->uploadedDocuments) {
            foreach ($proposal->uploadedDocuments as $doc) {
                $ext = strtolower(pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $disk = $doc->disk ?? 'public';
                    $fullPath = null;
                    if (Storage::disk($disk)->exists($doc->file_path)) {
                        $fullPath = Storage::disk($disk)->path($doc->file_path);
                    } elseif (file_exists(public_path('storage/'.$doc->file_path))) {
                        $fullPath = public_path('storage/'.$doc->file_path);
                    }
                    if ($fullPath && file_exists($fullPath)) {
                        $raw = @file_get_contents($fullPath);
                        if ($raw) {
                            $mime = @mime_content_type($fullPath) ?: 'image/jpeg';
                            $evidencePhotos[] = [
                                'title' => $doc->title ?: $doc->original_filename ?: 'Activity Documentation',
                                'category' => $doc->category ?: 'Field Evidence',
                                'data_uri' => 'data:'.$mime.';base64,'.base64_encode($raw),
                            ];
                        }
                    }
                }
            }
        }

        $pdf = Pdf::loadView('dss.rie.extension_proposals.narrative_terminal_pdf', compact(
            'proposal',
            'institution',
            'acc',
            'signatories',
            'plannedBudget',
            'actualSpent',
            'remainingBudget',
            'utilizationRate',
            'logoBase64',
            'evidencePhotos'
        ))->setPaper('a4', 'portrait');

        $prefix = $institution?->school_acronym ?: 'SNSU';
        $fileName = "{$prefix}_Terminal_Narrative_Report_{$proposal->proposal_code}.pdf";

        if ($request->boolean('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    /**
     * AI-assisted automatic narrative generator for extension accomplishment reports.
     */
    public function generateAccomplishmentNarrative(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $institution = TenantContext::getInstitution();
        $schoolName = $institution?->institution_name ?: 'Surigao del Norte State University';
        $schoolAcronym = $institution?->school_acronym ?: 'SNSU';

        $title = $proposal->title;
        $genObj = $proposal->general_objective ?: ($proposal->objectives_text ?: 'To deliver impactful community capacity-building and technical assistance.');
        $outputs = is_array($proposal->expected_outputs) ? implode('; ', $proposal->expected_outputs) : ($proposal->expected_outputs ?: 'Skills training, technology transfer, and community adoption.');
        $beneficiaries = $proposal->beneficiaries ?: 'Partner community members and marginalized sectors';

        $groqKey = config('services.groq.key') ?: env('GROQ_API_KEY');
        $geminiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        if (! empty($groqKey) || ! empty($geminiKey)) {
            try {
                $aiService = app(DssAiAssistantService::class);
                $prompt = "You are an official academic evaluator drafting the formal Post-Implementation Accomplishment Report for a university extension project at {$schoolName} ({$schoolAcronym}).\n\n".
                    "Project Title: {$title}\n".
                    "Objectives: {$genObj}\n".
                    "Target Outputs: {$outputs}\n".
                    "Target Beneficiaries: {$beneficiaries}\n\n".
                    "Generate a JSON response with exactly 6 narrative fields:\n".
                    "1. 'executive_narrative': Executive overview (2 paragraphs) on project rationale and community context.\n".
                    "2. 'activity_narrative': Detailed narrative on activity execution, training delivery, participant participation, and workshops.\n".
                    "3. 'summary_accomplishments': Detailed summary of physical deliverables, modules completed, and skills transferred.\n".
                    "4. 'problems_encountered': Typical field challenges faced (weather, scheduling, logistics).\n".
                    "5. 'solutions_adopted': Specific remedial and contingency measures taken.\n".
                    "6. 'impact_feedback': Qualitative impact narrative highlighting community empowerment and quotes.\n\n".
                    'Respond with ONLY valid JSON without markdown fences.';

                $aiResult = $aiService->respond($prompt, []);
                $reply = trim($aiResult['reply'] ?? '');

                // Parse json
                $cleaned = preg_replace('/^```json\s*|\s*```$/i', '', $reply);
                $decoded = json_decode($cleaned, true);

                if (is_array($decoded) && ! empty($decoded['summary_accomplishments'])) {
                    return response()->json([
                        'success' => true,
                        'source' => $aiResult['model'] ?? 'AI Extension Assistant',
                        'data' => $decoded,
                    ]);
                }
            } catch (\Throwable) {
                // fall back to template engine
            }
        }

        // Canonical High-Impact Fallback Template
        $fallback = [
            'executive_narrative' => "The extension project entitled “{$title}” was conducted by {$schoolAcronym} to advance community resilience and inclusive socio-economic growth across Caraga. The intervention addressed critical capacity gaps identified among partner community members, specifically targeting: {$beneficiaries}.\n\nGuided by institutional priorities and the Sustainable Development Goals, the project team mobilized university faculty and student facilitators to deliver structured technical coaching and practical assistance.",
            'activity_narrative' => "Project activities commenced with collaborative stakeholder consultations and formal coordinating conferences with local barangay leaders. The training regimen was delivered through contextualized modular lectures, hands-on laboratory demonstrations, and small-group mentoring sessions.\n\nParticipants actively engaged in real-world application exercises, translating theoretical concepts into concrete workplace practices.",
            'summary_accomplishments' => "All programmed milestones were achieved in full accordance with the approved work plan. The project team successfully turned over key deliverables including {$outputs} to community stakeholders, fully realizing the objective: {$genObj}\n\nParticipants demonstrated measurable post-training competence, ensuring sustained capability retention within the host community.",
            'problems_encountered' => "1. Unpredictable weather conditions and transportation logistics across coastal access routes during field training dates.\n2. Competing seasonal livelihood schedules of primary household earners during scheduled instructional hours.\n3. Initial disparities in technological literacy and baseline familiarity with introduced tools among adult participants.",
            'solutions_adopted' => "1. Instituted flexible weekend and late-afternoon modular sessions aligned with participants' non-working hours.\n2. Mobilized student-faculty peer facilitators to provide one-on-one guided coaching during practical laboratory and demonstration segments.\n3. Partnered with Barangay Officials to establish local communication hotlines and secure community halls equipped with backup lighting and venue facilities.",
            'impact_feedback' => "Community leaders and participating beneficiaries expressed profound appreciation for {$schoolAcronym}'s dedicated extension intervention. Participants reported marked improvements in practical knowledge and immediate applicability to their daily household and livelihood activities. Local barangay resolutions and testimonials confirmed that the project addressed long-standing development gaps and fostered stronger institutional trust with the university.",
        ];

        return response()->json([
            'success' => true,
            'source' => 'DSS Institutional Semantic Engine',
            'data' => $fallback,
        ]);
    }

    /**
     * Render the official Activity Design for this extension proposal.
     */
    public function activityDesign(Request $request, string $id): RedirectResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::with([
            'extensionPlan',
            'academicProgram',
            'campus',
            'projectLeaderUser',
            'projectLeaderEmployee',
        ])->findOrFail($proposalId);

        $design = ActivityDesign::firstOrCreate(
            [
                'extension_proposal_id' => $proposal->id,
            ],
            [
                'tenant_id' => $proposal->tenant_id ?: 1,
                'campus_id' => $proposal->campus_id,
                'extension_plan_id' => $proposal->extension_plan_id,
                'design_code' => ActivityDesign::generateCode($proposal->tenant_id ?: 1),
                'title' => $proposal->title,
                'banner_program' => $proposal->banner_program,
                'academic_program_id' => $proposal->academic_program_id,
                'unit_id' => $proposal->unit_id,
                'collaborating_agencies' => $proposal->collaborating_agencies,
                'target_beneficiaries' => $proposal->beneficiaries,
                'target_beneficiaries_count' => 0,
                'duration_text' => $proposal->duration_text,
                'project_location' => $proposal->project_location,
                'project_leader_user_id' => $proposal->project_leader_user_id,
                'project_leader_employee_id' => $proposal->project_leader_employee_id,
                'project_leader_name' => $proposal->project_leader_name,
                'project_leader_title' => $proposal->project_leader_title ?: 'Project Leader',
                'context_rationale' => $proposal->introduction ?: $proposal->relevance_narrative,
                'general_objective' => $proposal->general_objective,
                'specific_objectives' => $proposal->specific_objectives,
                'program_matrix' => $proposal->extensionPlan?->milestones_list ?? [],
                'budget_total' => $proposal->budget_cash_amount ?: 0,
                'budget_snsu_share' => $proposal->budget_cash_amount ?: 0,
                'budget_partner_share' => 0,
                'budget_total_display' => $proposal->budget_total_display,
                'status' => 'draft',
            ]
        );

        return redirect()->route('dss.rie.activity_designs.show', $design->getRouteKey());
    }
}
