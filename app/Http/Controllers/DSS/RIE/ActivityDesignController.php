<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DSS\CentralizedSignatoryController;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\DocumentSignatory;
use App\Models\User;
use App\Services\DSS\TenantContext;
use App\Services\PDF\DocumentPdfGenerator;
use App\Services\Signatories\CentralizedSignatoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityDesignController extends Controller
{
    /**
     * Display a listing of Activity Designs (Module 3).
     */
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenant()?->id ?? 1;
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $unitId = $request->filled('unit_id') ? (int) $request->query('unit_id') : null;

        $query = ActivityDesign::with([
            'extensionProposal',
            'extensionPlan',
            'unit',
            'projectLeaderEmployee',
            'projectLeaderUser',
            'documentSignatories',
        ])
            ->where('tenant_id', $tenantId)
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('design_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('project_leader_name', 'like', "%{$search}%")
                    ->orWhereHas('extensionProposal', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%")
                            ->orWhere('proposal_code', 'like', "%{$search}%")
                            ->orWhere('project_leader_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unit', function ($uq) use ($search) {
                        $uq->where('unit_name', 'like', "%{$search}%")
                            ->orWhere('unit_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $designs = $query->paginate(15)->withQueryString();

        // Metric Statistics
        $totalCount = ActivityDesign::where('tenant_id', $tenantId)->count();
        $approvedCount = ActivityDesign::where('tenant_id', $tenantId)->where('status', 'approved')->count();
        $underReviewCount = ActivityDesign::where('tenant_id', $tenantId)->whereIn('status', ['submitted', 'under_review'])->count();
        $draftCount = ActivityDesign::where('tenant_id', $tenantId)->where('status', 'draft')->count();

        $units = Unit::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('unit_name')->get();

        return view('dss.rie.activity_designs.index', compact(
            'designs',
            'totalCount',
            'approvedCount',
            'underReviewCount',
            'draftCount',
            'units',
            'search',
            'status',
            'unitId'
        ));
    }

    /**
     * Show the form for creating a new Activity Design.
     */
    public function create(Request $request): View
    {
        $tenantId = TenantContext::getTenant()?->id ?? 1;
        $selectedProposalId = $request->filled('proposal_id')
            ? (function_exists('dss_decrypt_id') ? dss_decrypt_id($request->query('proposal_id')) : (int) $request->query('proposal_id'))
            : null;

        $selectedProposal = $selectedProposalId ? ExtensionProposal::with(['extensionPlan', 'unit', 'projectLeaderEmployee'])->find($selectedProposalId) : null;

        $proposals = ExtensionProposal::where('tenant_id', $tenantId)
            ->with(['extensionPlan', 'unit', 'projectLeaderEmployee'])
            ->orderBy('id', 'desc')
            ->get();

        // Map existing Activity Designs by extension_proposal_id
        $existingDesignMap = ActivityDesign::where('tenant_id', $tenantId)
            ->whereNotNull('extension_proposal_id')
            ->get(['id', 'design_code', 'extension_proposal_id'])
            ->keyBy('extension_proposal_id');

        $nextCode = ActivityDesign::generateCode($tenantId);

        return view('dss.rie.activity_designs.create', compact('selectedProposal', 'proposals', 'existingDesignMap', 'nextCode'));
    }

    /**
     * Store a newly created Activity Design in storage by fetching from an Extension Proposal.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::getTenant()?->id ?? 1;

        $validated = $request->validate([
            'extension_proposal_id' => 'required|exists:extension_proposals,id',
            'title' => 'nullable|string|max:255',
            'project_leader_name' => 'nullable|string|max:191',
            'project_leader_title' => 'nullable|string|max:191',
            'budget_total' => 'nullable|numeric|min:0',
            'budget_snsu_share' => 'nullable|numeric|min:0',
            'budget_partner_share' => 'nullable|numeric|min:0',
        ]);

        $proposal = ExtensionProposal::with(['extensionPlan', 'unit', 'projectLeaderEmployee'])->findOrFail($validated['extension_proposal_id']);

        // Check if an Activity Design already exists for this proposal
        $design = ActivityDesign::where('extension_proposal_id', $proposal->id)->first();

        if (! $design) {
            $design = new ActivityDesign;
            $design->tenant_id = $tenantId;
            $design->design_code = ActivityDesign::generateCode($tenantId);
            $design->status = 'draft';
            $design->populateFromProposal($proposal);

            // Optional explicit overrides if provided
            if (! empty($validated['title'])) {
                $design->title = $validated['title'];
            }
            if (! empty($validated['project_leader_name'])) {
                $design->project_leader_name = $validated['project_leader_name'];
            }
            if (! empty($validated['project_leader_title'])) {
                $design->project_leader_title = $validated['project_leader_title'];
            }
            if (isset($validated['budget_total']) && $validated['budget_total'] !== null) {
                $design->budget_total = (float) $validated['budget_total'];
            }
            if (isset($validated['budget_snsu_share']) && $validated['budget_snsu_share'] !== null) {
                $design->budget_snsu_share = (float) $validated['budget_snsu_share'];
            }
            if (isset($validated['budget_partner_share']) && $validated['budget_partner_share'] !== null) {
                $design->budget_partner_share = (float) $validated['budget_partner_share'];
            }

            $design->save();

            // Auto-seed runtime document signatories for this Activity Design
            self::syncSignatoriesAndRoutes($design);

            $message = "Activity Design [{$design->design_code}] successfully created and fetched from Extension Proposal.";
        } else {
            // Re-sync directly from proposal
            $design->populateFromProposal($proposal);

            if (! empty($validated['title'])) {
                $design->title = $validated['title'];
            }
            if (! empty($validated['project_leader_name'])) {
                $design->project_leader_name = $validated['project_leader_name'];
            }
            if (! empty($validated['project_leader_title'])) {
                $design->project_leader_title = $validated['project_leader_title'];
            }
            if (isset($validated['budget_total']) && $validated['budget_total'] !== null) {
                $design->budget_total = (float) $validated['budget_total'];
            }
            if (isset($validated['budget_snsu_share']) && $validated['budget_snsu_share'] !== null) {
                $design->budget_snsu_share = (float) $validated['budget_snsu_share'];
            }
            if (isset($validated['budget_partner_share']) && $validated['budget_partner_share'] !== null) {
                $design->budget_partner_share = (float) $validated['budget_partner_share'];
            }

            $design->save();

            $message = "Activity Design [{$design->design_code}] updated with fresh data from Extension Proposal.";
        }

        return redirect()->route('dss.rie.activity_designs.show', $design->getRouteKey())
            ->with('success', $message);
    }

    /**
     * Re-fetch and synchronize all data directly from the parent Extension Proposal.
     */
    public function sync(Request $request, string $id): RedirectResponse
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::with('extensionProposal.extensionPlan')->findOrFail($designId);

        if (! $design->extensionProposal) {
            return back()->with('error', 'This Activity Design is not linked to any source Extension Proposal.');
        }

        $design->populateFromProposal($design->extensionProposal);
        $design->save();

        return redirect()->route('dss.rie.activity_designs.show', $design->getRouteKey())
            ->with('success', "Activity Design successfully re-fetched and synchronized from Extension Proposal [{$design->extensionProposal->proposal_code}].");
    }

    /**
     * Display the specified Activity Design sheet.
     */
    public function show(Request $request, string $id): View
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::with([
            'extensionProposal.extensionPlan',
            'extensionPlan',
            'academicProgram',
            'unit',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
        ])->findOrFail($designId);

        $proposal = $design->extensionProposal;
        $plan = $design->extensionPlan ?: $proposal?->extensionPlan;
        $institution = TenantContext::getInstitution();
        $activities = $design->program_matrix ?: ($plan?->milestones_list ?? []);

        // Check if signatories exist in sys_document_signatories
        $signatoriesCount = DocumentSignatory::where('document_type', $design->getMorphClass())
            ->where('document_id', $design->id)
            ->count();

        if ($signatoriesCount === 0) {
            self::syncSignatoriesAndRoutes($design);
            $design->refresh();
        }

        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($design);

        // Check if an uploaded wet-signed Activity Design exists
        $wetSignedDoc = $design->physicallySignedDocument
            ?: ($proposal?->uploadedDocuments()->where('category', 'activity_design')->first()
            ?: $plan?->activities()->where('title', 'like', '%Activity Design%')->first()?->uploadedDocument);

        return view('dss.rie.activity_designs.show', compact('design', 'proposal', 'plan', 'institution', 'activities', 'signatories', 'wetSignedDoc'));
    }

    /**
     * Show dedicated full page for configuring Activity Design signatories workflow (Centralized Modifier).
     */
    public function configureSignatories(Request $request, string $id): View
    {
        return app(CentralizedSignatoryController::class)
            ->editDocumentSignatories($request, 'activity_design', $id);
    }

    /**
     * Generate official Activity Design PDF using FPDF (matching Extension Proposal format).
     */
    public function pdf(Request $request, string $id): Response
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::with([
            'extensionProposal.extensionPlan',
            'extensionPlan',
            'academicProgram',
            'unit',
            'projectLeaderUser',
            'projectLeaderEmployee',
            'documentSignatories.employee',
            'documentSignatories.signedByUser',
        ])->findOrFail($designId);

        $institution = TenantContext::getInstitution();
        $paper = $request->query('paper', 'A4');
        $template = $request->query('template', 'executive');
        $download = $request->boolean('download');

        $pdf = DocumentPdfGenerator::fromActivityDesign($design, $institution, $paper, $template);
        $prefix = $institution?->school_acronym ?: 'SNSU';
        $fileName = "{$prefix}_Activity_Design_{$design->design_code}.pdf";

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline')."; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Update signatories for this Activity Design (AJAX repeater endpoint).
     */
    public function updateSignatories(Request $request, string $id): JsonResponse
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::findOrFail($designId);

        $payload = $request->all();
        self::syncSignatoriesAndRoutes($design, $payload);

        return response()->json([
            'success' => true,
            'message' => 'Activity Design signatories updated successfully.',
            'count' => $design->documentSignatories()->count(),
        ]);
    }

    /**
     * Auto-suggest the 9-tier Activity Design signatories matrix.
     */
    public function getSuggestedSignatories(Request $request, string $id): JsonResponse
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::findOrFail($designId);

        $resolved = self::resolveSuggestedSignatories($design);

        return response()->json([
            'success' => true,
            'source' => $resolved['source'] ?? 'canonical',
            'signatories' => $resolved['signatories'] ?? [],
        ]);
    }

    /**
     * Record a verified digital signature on an Activity Design tier.
     */
    public function sign(Request $request, string $id): JsonResponse
    {
        $designId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $design = ActivityDesign::findOrFail($designId);

        $validated = $request->validate([
            'tier_role' => 'required|string',
            'role_label' => 'nullable|string',
            'signature_img' => 'nullable|string',
            'signature_type' => 'nullable|string|in:drawn,font,upload',
            'remarks' => 'nullable|string|max:500',
        ]);

        $res = CentralizedSignatoryService::recordDigitalSignature(
            $design,
            $validated['tier_role'],
            Auth::user(),
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Activity Design signed successfully.',
            'data' => $res,
        ]);
    }

    /**
     * Resolve suggested signatories specifically for Activity Design (9 Tiers per user uploaded image).
     *
     * @return array<string, mixed>
     */
    public static function resolveSuggestedSignatories(ActivityDesign $design): array
    {
        $tenantId = $design->tenant_id ?: 1;
        $unitId = $design->unit_id;

        // 1. Prepared by (Project Leader)
        $prepName = $design->project_leader_name ?: 'Project Leader';
        $prepDesig = $design->project_leader_title ?: 'Project Leader';
        $prepEmpId = $design->project_leader_employee_id;

        // 2. Unit Officers: Dean and Extension Head
        $unit = $unitId ? Unit::find($unitId) : null;
        $extHeadName = $unit?->extension_head_name ?: 'College Head for Extension';
        $extHeadTitle = $unit?->extension_head_title ?: 'Head, College Extension Unit';
        $extHeadEmpId = $unit?->extension_head_employee_id;

        $deanName = $unit?->head_name ?: 'College Dean';
        $deanTitle = $unit?->head_title ?: 'College Dean';
        $deanEmpId = $unit?->head_employee_id;

        // 3. Concurred by: University Extension Director
        $directorEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%Extension Director%')
                    ->orWhere('last_name', 'like', '%Plaza%');
            })->first();

        $directorName = $directorEmp?->full_name ?: 'ALVIE ELMA C. PLAZA, MA';
        $directorTitle = $directorEmp?->designation ?: 'Director, Extension Services';
        $directorEmpId = $directorEmp?->id;

        // 4. Recommending Approval: ROMY JUN A. SUNICO, DTE
        $vpeEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('last_name', 'like', '%Sunico%')
                    ->orWhere('designation', 'like', '%Vice President for RIE%')
                    ->orWhere('designation', 'like', '%VP for RIE%');
            })->first();

        $vpeName = $vpeEmp?->full_name ?: 'ROMY JUN A. SUNICO, DTE';
        $vpeTitle = $vpeEmp?->designation ?: 'Vice President for RIE';
        $vpeEmpId = $vpeEmp?->id;

        // 5. Certified as to allotment: MARY ANN M. NARCISO, MBA
        $budgetEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('last_name', 'like', '%Narciso%')
                    ->orWhere('designation', 'like', '%Budget Officer%');
            })->first();

        $budgetName = $budgetEmp?->full_name ?: 'MARY ANN M. NARCISO, MBA';
        $budgetTitle = $budgetEmp?->designation ?: 'Budget Officer III';
        $budgetEmpId = $budgetEmp?->id;

        // 6. Certified as to funds: IVY JEAN J. TURNO
        $acctEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('last_name', 'like', '%Turno%')
                    ->orWhere('designation', 'like', '%Accountant%');
            })->first();

        $acctName = $acctEmp?->full_name ?: 'IVY JEAN J. TURNO';
        $acctTitle = $acctEmp?->designation ?: 'Accountant III';
        $acctEmpId = $acctEmp?->id;

        // 7. FELICITY MAE M. ECLEO: CAO-Designate
        $caoEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('last_name', 'like', '%Ecleo%')
                    ->orWhere('designation', 'like', '%CAO%');
            })->first();

        $caoName = $caoEmp?->full_name ?: 'FELICITY MAE M. ECLEO';
        $caoTitle = $caoEmp?->designation ?: 'CAO-Designate';
        $caoEmpId = $caoEmp?->id;

        // 8. Approved by: ROWENA A. PLANDO, Ph.D.
        $presEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('last_name', 'like', '%Plando%')
                    ->orWhere('designation', 'like', '%University President%');
            })->first();

        $presName = $presEmp?->full_name ?: 'ROWENA A. PLANDO, Ph.D.';
        $presTitle = $presEmp?->designation ?: 'University President III';
        $presEmpId = $presEmp?->id;

        $signatories = [
            [
                'role_label' => 'Prepared by:',
                'tier_role' => 'prepared',
                'employee_id' => $prepEmpId,
                'name' => $prepName,
                'designation' => $prepDesig,
                'office' => $unit?->unit_name ?: 'Proponent Unit',
                'alignment' => 'left',
                'position_column' => 1,
            ],
            [
                'role_label' => 'Reviewed by:',
                'tier_role' => 'reviewed',
                'employee_id' => $extHeadEmpId,
                'name' => $extHeadName,
                'designation' => $extHeadTitle,
                'office' => $unit?->unit_name ?: 'College Extension Unit',
                'alignment' => 'left',
                'position_column' => 2,
            ],
            [
                'role_label' => 'Noted by:',
                'tier_role' => 'noted',
                'employee_id' => $deanEmpId,
                'name' => $deanName,
                'designation' => $deanTitle,
                'office' => $unit?->unit_name ?: 'Office of the Dean',
                'alignment' => 'left',
                'position_column' => 3,
            ],
            [
                'role_label' => 'Concurred by:',
                'tier_role' => 'concurred',
                'employee_id' => $directorEmpId,
                'name' => $directorName,
                'designation' => $directorTitle,
                'office' => 'University Extension Services Office',
                'alignment' => 'left',
                'position_column' => 4,
            ],
            [
                'role_label' => 'Recommending Approval:',
                'tier_role' => 'recommending_approval',
                'employee_id' => $vpeEmpId,
                'name' => $vpeName,
                'designation' => $vpeTitle,
                'office' => 'Office of the Vice President for RIE',
                'alignment' => 'center',
                'position_column' => 2,
            ],
            [
                'role_label' => 'Certified as to availability of allotment:',
                'tier_role' => 'certified_allotment',
                'employee_id' => $budgetEmpId,
                'name' => $budgetName,
                'designation' => $budgetTitle,
                'office' => 'Budget Management Office',
                'alignment' => 'left',
                'position_column' => 1,
            ],
            [
                'role_label' => 'Certified as to availability of funds:',
                'tier_role' => 'certified_funds',
                'employee_id' => $acctEmpId,
                'name' => $acctName,
                'designation' => $acctTitle,
                'office' => 'Accounting Office',
                'alignment' => 'right',
                'position_column' => 2,
            ],
            [
                'role_label' => '',
                'tier_role' => 'cao_designate',
                'employee_id' => $caoEmpId,
                'name' => $caoName,
                'designation' => $caoTitle,
                'office' => 'Office of the Chief Administrative Officer',
                'alignment' => 'center',
                'position_column' => 2,
            ],
            [
                'role_label' => 'Approved by:',
                'tier_role' => 'approved',
                'employee_id' => $presEmpId,
                'name' => $presName,
                'designation' => $presTitle,
                'office' => 'Office of the University President',
                'alignment' => 'center',
                'position_column' => 2,
            ],
        ];

        return [
            'source' => 'activity_design_canonical',
            'signatories' => $signatories,
        ];
    }

    /**
     * Synchronize Activity Design signatories to sys_document_signatories.
     *
     * @param  array<string, mixed>  $inputs
     */
    public static function syncSignatoriesAndRoutes(ActivityDesign $design, array $inputs = []): void
    {
        $tenantId = $design->tenant_id ?: 1;
        $submitted = $inputs['signatories'] ?? null;

        if (is_array($submitted)) {
            $submitted = array_values($submitted);
        }

        if (empty($submitted)) {
            $suggested = self::resolveSuggestedSignatories($design)['signatories'] ?? [];
            $submitted = $suggested;
        }

        $existingSigs = DocumentSignatory::where('document_type', $design->getMorphClass())
            ->where('document_id', $design->id)
            ->get()
            ->keyBy('id');

        $processedIds = [];

        foreach ($submitted as $idx => $item) {
            $sortOrder = $idx + 1;
            $rawRoleLabel = isset($item['role_label']) ? trim((string) $item['role_label']) : '';
            $roleLabel = ($idx === 0) ? ($rawRoleLabel ?: 'Prepared by:') : $rawRoleLabel;
            $tierRole = ! empty($item['tier_role']) ? trim($item['tier_role']) : 'step_'.$sortOrder;
            $empId = ! empty($item['employee_id']) ? (int) $item['employee_id'] : null;

            $emp = $empId ? Employee::with('department')->find($empId) : null;
            $name = trim($item['name'] ?? ($emp ? $emp->full_name : ''));
            $designation = trim($item['designation'] ?? ($emp?->designation ?: 'Authorized Signatory'));
            $office = trim($item['office'] ?? ($emp?->department?->unit_name ?: 'Surigao del Norte State University'));

            if (empty($name) && $idx === 0) {
                $name = $design->project_leader_name ?: 'Project Leader';
                $designation = $design->project_leader_title ?: 'Project Leader';
            }

            if (empty($name)) {
                continue;
            }

            $userAlign = $item['alignment'] ?? null;
            $userCol = isset($item['position_column']) ? (int) $item['position_column'] : null;
            $placement = ExtensionProposalController::resolveSignatoryPlacement($roleLabel, $tierRole, $idx, $userCol, $userAlign);

            $existingId = ! empty($item['id']) ? (int) $item['id'] : null;
            $existing = $existingId ? ($existingSigs[$existingId] ?? null) : null;

            $status = $existing?->status ?? 'pending';
            if ($idx === 0 || $tierRole === 'prepared') {
                $status = ($design->status !== 'draft') ? 'signed' : 'pending';
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
                    'document_type' => $design->getMorphClass(),
                    'document_id' => $design->id,
                    'tenant_id' => $tenantId,
                    'campus_id' => $design->campus_id,
                    'dts_record_id' => $design->dts_record_id,
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
        }

        DocumentSignatory::where('document_type', $design->getMorphClass())
            ->where('document_id', $design->id)
            ->whereNotIn('id', $processedIds)
            ->where('status', '!=', 'signed')
            ->delete();
    }
}
