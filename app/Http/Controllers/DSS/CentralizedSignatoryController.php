<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DSS\RIE\ActivityDesignController;
use App\Http\Controllers\DSS\RIE\ExtensionProposalController;
use App\Models\Dts\DocumentType;
use App\Models\Dts\RecordRoute;
use App\Models\Employee;
use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Models\User;
use App\Services\DSS\TenantContext;
use App\Services\Signatories\CentralizedSignatoryService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CentralizedSignatoryController extends Controller
{
    /**
     * Display the Centralized Signatory Matrix workbench.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $tenantId = $tenant?->id ?? 1;

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();
        $docTypes = DocumentType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCampusId = $request->filled('campus_id') ? (int) $request->query('campus_id') : null;
        $selectedDocTypeId = $request->filled('doc_type_id') ? (int) $request->query('doc_type_id') : null;

        // Default to Extension Plan doc_type if none selected
        if (! $selectedDocTypeId && $docTypes->isNotEmpty()) {
            $defaultDoc = $docTypes->firstWhere('name', 'Extension Plan') ?: $docTypes->first();
            $selectedDocTypeId = $defaultDoc?->id;
        }

        $activeDocType = $selectedDocTypeId ? $docTypes->firstWhere('id', $selectedDocTypeId) : null;

        // Query matrices
        $matricesQuery = DocumentSignatoryMatrix::with(['campus', 'documentType', 'employee', 'user'])
            ->where('tenant_id', $tenantId);

        if ($selectedDocTypeId) {
            $matricesQuery->where(function ($q) use ($selectedDocTypeId, $activeDocType) {
                $q->where('doc_type_id', $selectedDocTypeId);
                if ($activeDocType) {
                    $q->orWhere('process_type', $activeDocType->name);
                }
            });
        }

        if ($selectedCampusId) {
            $matricesQuery->where('campus_id', $selectedCampusId);
        } else {
            $matricesQuery->whereNull('campus_id');
        }

        $matrices = $matricesQuery->orderBy('sort_order', 'asc')
            ->orderBy('position_column', 'asc')
            ->get();

        // Employees are loaded on-demand via Server-Side Select2 AJAX (dss.select2.employees)
        $employees = collect();

        return view('dss.settings.signatories.index', compact(
            'tenant',
            'institution',
            'campuses',
            'docTypes',
            'selectedCampusId',
            'selectedDocTypeId',
            'activeDocType',
            'matrices',
            'employees'
        ));
    }

    /**
     * Create a new document classification / process type.
     */
    public function storeDocumentType(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
        ]);

        $docType = DocumentType::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'code' => strtoupper(trim($validated['code'])),
            'category' => $validated['category'] ?: 'RIE & Extension',
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?: 'mdi-file-document-outline',
            'is_active' => true,
        ]);

        return redirect()->route('dss.settings.signatories.index', [
            'doc_type_id' => $docType->id,
        ])->with('success', "New Document Process Type '{$docType->name}' created successfully. You can now define its signatories.");
    }

    /**
     * Store a new signatory matrix rule.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $validated = $request->validate([
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'doc_type_id' => 'required|exists:dts_document_types,id',
            'tier_role' => 'nullable|string|max:50',
            'role_label' => 'required|string|max:100',
            'assignment_type' => 'required|string|in:fixed_employee,dynamic_author,dynamic_dept_head,dynamic_campus_director,manual_signatory,creator_selected,dynamic_extension_head,dynamic_college_dean,role_based',
            'employee_id' => 'nullable|required_if:assignment_type,fixed_employee|exists:employees,id',
            'signatory_name' => 'nullable|string|max:191',
            'designation' => 'nullable|string|max:191',
            'office' => 'nullable|string|max:191',
            'position_column' => 'nullable|integer|min:1|max:12',
            'col_span' => 'nullable|integer|min:1|max:12',
            'alignment' => 'nullable|string|in:left,center,right',
            'sort_order' => 'required|integer|min:1',
            'requires_digital_signature' => 'nullable|boolean',
            'is_mandatory' => 'nullable|boolean',
        ]);

        $docType = DocumentType::findOrFail($validated['doc_type_id']);

        if (empty($validated['tier_role']) || $validated['tier_role'] === 'custom') {
            $labelLower = strtolower($validated['role_label']);
            if (str_contains($labelLower, 'prepar')) {
                $validated['tier_role'] = 'prepared';
            } elseif (str_contains($labelLower, 'review')) {
                $validated['tier_role'] = 'reviewed';
            } elseif (str_contains($labelLower, 'note')) {
                $validated['tier_role'] = 'noted';
            } elseif (str_contains($labelLower, 'recommend')) {
                $validated['tier_role'] = 'recommending_approval';
            } elseif (str_contains($labelLower, 'approv')) {
                $validated['tier_role'] = 'approved';
            } else {
                $validated['tier_role'] = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $validated['role_label']), '_')) ?: 'signatory';
            }
        }

        $userId = null;
        if (! empty($validated['employee_id'])) {
            $emp = Employee::find($validated['employee_id']);
            $validated['signatory_name'] = $emp?->full_name;
            if (empty($validated['designation'])) {
                $validated['designation'] = $emp?->designation;
            }
            $userId = User::where('employee_id', $validated['employee_id'])->value('id');
        }

        DocumentSignatoryMatrix::create([
            'tenant_id' => $tenantId,
            'campus_id' => $validated['campus_id'] ?? null,
            'doc_type_id' => $docType->id,
            'process_type' => $docType->name,
            'tier_role' => $validated['tier_role'],
            'role_label' => $validated['role_label'],
            'assignment_type' => $validated['assignment_type'],
            'employee_id' => $validated['employee_id'] ?? null,
            'user_id' => $userId,
            'signatory_name' => $validated['signatory_name'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'office' => $validated['office'] ?? null,
            'position_column' => $validated['position_column'] ?? 1,
            'col_span' => (int) ($validated['col_span'] ?? 6),
            'alignment' => $validated['alignment'] ?? 'left',
            'sort_order' => $validated['sort_order'],
            'requires_digital_signature' => $request->boolean('requires_digital_signature', true),
            'is_mandatory' => $request->boolean('is_mandatory', true),
            'is_active' => true,
        ]);

        return redirect()->route('dss.settings.signatories.index', [
            'campus_id' => $validated['campus_id'] ?? null,
            'doc_type_id' => $docType->id,
        ])->with('success', "Signatory '{$validated['role_label']}' added to {$docType->name} matrix.");
    }

    /**
     * Update an existing signatory rule.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $matrix = DocumentSignatoryMatrix::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'role_label' => 'required|string|max:100',
            'tier_role' => 'nullable|string|max:50',
            'assignment_type' => 'required|string|in:fixed_employee,dynamic_author,dynamic_dept_head,dynamic_campus_director,manual_signatory,creator_selected,dynamic_extension_head,dynamic_college_dean,role_based',
            'employee_id' => 'nullable|required_if:assignment_type,fixed_employee|exists:employees,id',
            'signatory_name' => 'nullable|string|max:191',
            'designation' => 'nullable|string|max:191',
            'office' => 'nullable|string|max:191',
            'position_column' => 'nullable|integer|min:1|max:12',
            'col_span' => 'nullable|integer|min:1|max:12',
            'alignment' => 'nullable|string|in:left,center,right',
            'sort_order' => 'required|integer|min:1',
            'requires_digital_signature' => 'nullable|boolean',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if (empty($validated['tier_role']) || $validated['tier_role'] === 'custom') {
            $labelLower = strtolower($validated['role_label']);
            if (str_contains($labelLower, 'prepar')) {
                $validated['tier_role'] = 'prepared';
            } elseif (str_contains($labelLower, 'review')) {
                $validated['tier_role'] = 'reviewed';
            } elseif (str_contains($labelLower, 'note')) {
                $validated['tier_role'] = 'noted';
            } elseif (str_contains($labelLower, 'recommend')) {
                $validated['tier_role'] = 'recommending_approval';
            } elseif (str_contains($labelLower, 'approv')) {
                $validated['tier_role'] = 'approved';
            } else {
                $validated['tier_role'] = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $validated['role_label']), '_')) ?: 'signatory';
            }
        }

        if (! empty($validated['employee_id'])) {
            $emp = Employee::find($validated['employee_id']);
            $validated['signatory_name'] = $emp?->full_name;
            if (empty($validated['designation'])) {
                $validated['designation'] = $emp?->designation;
            }
            $validated['user_id'] = User::where('employee_id', $validated['employee_id'])->value('id');
        } else {
            $validated['user_id'] = null;
        }

        $validated['requires_digital_signature'] = $request->boolean('requires_digital_signature', true);
        $validated['is_mandatory'] = $request->boolean('is_mandatory', true);

        $matrix->update($validated);

        // Sync pending document instances
        CentralizedSignatoryService::syncMatrixToPendingDocuments($tenant->id, $matrix->doc_type_id);

        return redirect()->route('dss.settings.signatories.index', [
            'campus_id' => $matrix->campus_id,
            'doc_type_id' => $matrix->doc_type_id,
        ])->with('success', 'Signatory rule updated successfully.');
    }

    /**
     * Delete a signatory rule.
     */
    public function destroy(int $id): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $matrix = DocumentSignatoryMatrix::where('tenant_id', $tenant->id)->findOrFail($id);
        $campusId = $matrix->campus_id;
        $docTypeId = $matrix->doc_type_id;

        $matrix->delete();

        CentralizedSignatoryService::syncMatrixToPendingDocuments($tenant->id, $docTypeId);

        return redirect()->route('dss.settings.signatories.index', [
            'campus_id' => $campusId,
            'doc_type_id' => $docTypeId,
        ])->with('success', 'Signatory rule removed.');
    }

    /**
     * Batch update layout (drag-and-drop sort_order, col_span, alignment).
     */
    public function updateLayout(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:sys_document_signatory_matrices,id',
            'items.*.sort_order' => 'required|integer|min:1',
            'items.*.col_span' => 'required|integer|min:1|max:12',
            'items.*.alignment' => 'required|string|in:left,center,right',
        ]);

        $affectedDocTypeId = null;
        foreach ($validated['items'] as $itemData) {
            $matrix = DocumentSignatoryMatrix::where('tenant_id', $tenant->id)
                ->where('id', $itemData['id'])
                ->first();

            if ($matrix) {
                $affectedDocTypeId = $matrix->doc_type_id;
                $matrix->update([
                    'sort_order' => $itemData['sort_order'],
                    'col_span' => $itemData['col_span'],
                    'alignment' => $itemData['alignment'],
                    'position_column' => min(4, (int) ceil($itemData['col_span'] / 3)),
                ]);
            }
        }

        if ($affectedDocTypeId) {
            CentralizedSignatoryService::syncMatrixToPendingDocuments($tenant->id, $affectedDocTypeId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Signatory layout and sequence updated successfully.',
        ]);
    }

    /**
     * Quick AJAX update for a single signatory rule (e.g. 1-click width or alignment pill).
     */
    public function updateItemQuick(Request $request, int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $matrix = DocumentSignatoryMatrix::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'col_span' => 'nullable|integer|min:1|max:12',
            'alignment' => 'nullable|string|in:left,center,right',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        if (isset($validated['col_span'])) {
            $validated['position_column'] = min(4, (int) ceil($validated['col_span'] / 3));
        }

        $matrix->update(array_filter($validated, fn ($val) => ! is_null($val)));

        CentralizedSignatoryService::syncMatrixToPendingDocuments($tenant->id, $matrix->doc_type_id);

        return response()->json([
            'success' => true,
            'message' => 'Signatory updated.',
            'matrix' => $matrix,
        ]);
    }

    /**
     * Quick-seed standard university defaults for the active document type.
     */
    public function seedDefaults(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $docTypeId = (int) $request->input('doc_type_id');
        $campusId = $request->filled('campus_id') ? (int) $request->input('campus_id') : null;

        $docType = DocumentType::findOrFail($docTypeId);

        // Check if this is Extension Plan, Proposal, or Activity Design
        $isExtension = str_contains(strtolower($docType->name), 'extension')
            || str_contains(strtolower($docType->name), 'activity');

        if ($isExtension) {
            // 1. Prepared by (Project Leader)
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
                'process_type' => $docType->name,
                'tier_role' => 'prepared',
                'role_label' => 'Prepared by:',
                'assignment_type' => 'dynamic_author',
                'signatory_name' => 'Project Leader / Proponent',
                'designation' => 'Faculty / Project Leader',
                'position_column' => 1,
                'col_span' => 12,
                'alignment' => 'left',
                'sort_order' => 1,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
                'is_active' => true,
            ]);

            // 2. Reviewed by (College / Campus Head for Extension)
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
                'process_type' => $docType->name,
                'tier_role' => 'reviewed',
                'role_label' => 'Reviewed by:',
                'assignment_type' => 'dynamic_extension_head',
                'signatory_name' => 'College Head for Extension',
                'designation' => 'Head for Extension (Proponent Unit)',
                'office' => 'College Extension Unit',
                'position_column' => 2,
                'col_span' => 6,
                'alignment' => 'left',
                'sort_order' => 2,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
                'is_active' => true,
            ]);

            // 3. Noted by (College Dean / Campus Director)
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
                'process_type' => $docType->name,
                'tier_role' => 'noted',
                'role_label' => 'Noted by:',
                'assignment_type' => 'dynamic_college_dean',
                'signatory_name' => 'College Dean / Campus Director',
                'designation' => 'Dean / Campus Director (Proponent Unit)',
                'office' => 'Office of the Dean',
                'position_column' => 3,
                'col_span' => 6,
                'alignment' => 'left',
                'sort_order' => 3,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
                'is_active' => true,
            ]);

            // 4. Concurred by (Extension Specialist V)
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
                'process_type' => $docType->name,
                'tier_role' => 'concurred',
                'role_label' => 'Concurred by:',
                'assignment_type' => 'role_based',
                'signatory_name' => 'EVER JOY G. RABADAN, MAEM, MSc.',
                'designation' => 'University Extension Specialist V',
                'office' => 'University Extension Services Office',
                'position_column' => 4,
                'col_span' => 6,
                'alignment' => 'left',
                'sort_order' => 4,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
                'is_active' => true,
            ]);

            // 5. Concurred by (University Extension Director)
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
                'process_type' => $docType->name,
                'tier_role' => 'concurred_2',
                'role_label' => 'Concurred by:',
                'assignment_type' => 'role_based',
                'signatory_name' => 'ALVIE ELMA C. PLAZA, MA',
                'designation' => 'University Extension Director',
                'office' => 'University Extension Services Office',
                'position_column' => 4,
                'col_span' => 6,
                'alignment' => 'left',
                'sort_order' => 5,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
                'is_active' => true,
            ]);

            return redirect()->route('dss.settings.signatories.index', [
                'campus_id' => $campusId,
                'doc_type_id' => $docType->id,
            ])->with('success', "Standard 5-step institutional signatories seeded for {$docType->name}.");
        }

        // Find key executive employees for general documents
        $president = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%President%')
                    ->orWhere('designation', 'like', '%Director%');
            })->first();

        $extHead = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%Extension%')
                    ->orWhere('designation', 'like', '%Head%');
            })->first();

        // 1. Prepared by (Dynamic Author / Project Leader)
        DocumentSignatoryMatrix::create([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'doc_type_id' => $docType->id,
            'process_type' => $docType->name,
            'tier_role' => 'prepared',
            'role_label' => 'Prepared by:',
            'assignment_type' => 'dynamic_author',
            'signatory_name' => '[Project Leader / Author]',
            'designation' => 'Project Leader',
            'position_column' => 1,
            'sort_order' => 1,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
            'is_active' => true,
        ]);

        // 2. Noted by: Extension Plan = College Head for Extension; General = Dept/Extension Head
        DocumentSignatoryMatrix::create([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'doc_type_id' => $docType->id,
            'process_type' => $docType->name,
            'tier_role' => 'noted',
            'role_label' => 'Noted by:',
            'assignment_type' => $isExtensionPlan ? 'creator_selected' : ($extHead ? 'fixed_employee' : 'dynamic_dept_head'),
            'employee_id' => $isExtensionPlan ? null : $extHead?->id,
            'signatory_name' => $isExtensionPlan ? null : ($extHead?->full_name ?: 'Reynaldo L. Alonzo EnP'),
            'designation' => $isExtensionPlan ? 'College Head for Extension' : ($extHead?->designation ?: 'Extension Head'),
            'office' => $isExtensionPlan ? 'College Extension Services' : 'Extension Services Office',
            'position_column' => 2,
            'sort_order' => 2,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
            'is_active' => true,
        ]);

        // 3. Approved by: Extension Plan = College Dean; General = President/Director
        DocumentSignatoryMatrix::create([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'doc_type_id' => $docType->id,
            'process_type' => $docType->name,
            'tier_role' => 'approved',
            'role_label' => 'Approved by:',
            'assignment_type' => $isExtensionPlan ? 'creator_selected' : ($president ? 'fixed_employee' : 'dynamic_campus_director'),
            'employee_id' => $isExtensionPlan ? null : $president?->id,
            'signatory_name' => $isExtensionPlan ? null : ($president?->full_name ?: 'Dr. Joy C. Capistrano'),
            'designation' => $isExtensionPlan ? 'College Dean' : ($president?->designation ?: 'University President'),
            'office' => $isExtensionPlan ? 'Office of the College Dean' : 'Office of the University President',
            'position_column' => 3,
            'sort_order' => 3,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
            'is_active' => true,
        ]);

        return redirect()->route('dss.settings.signatories.index', [
            'campus_id' => $campusId,
            'doc_type_id' => $docType->id,
        ])->with('success', "Standard 3-tier signatories seeded for {$docType->name}.");
    }

    /**
     * Update runtime document signatories in sys_document_signatories for a specific document instance.
     * Allows adjusting signers when personnel changes, is on leave, or requires reassignment.
     */
    public function updateDocumentSignatories(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'document_id' => 'required',
            'signatories' => 'required|array',
            'signatories.*.id' => 'required|integer|exists:sys_document_signatories,id',
            'signatories.*.employee_id' => 'nullable|integer|exists:employees,id',
            'signatories.*.signatory_name' => 'nullable|string|max:191',
            'signatories.*.designation' => 'nullable|string|max:191',
            'signatories.*.office' => 'nullable|string|max:191',
            'signatories.*.role_label' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $docClass = $validated['document_type'];
        if (! class_exists($docClass)) {
            $morphMap = Relation::$morphMap;
            if (isset($morphMap[$docClass])) {
                $docClass = $morphMap[$docClass];
            }
        }

        $document = $docClass::findOrFail($validated['document_id']);

        // Authorization check: Admin, creator, or designated leader
        $canManage = $user->isSuperAdmin() || $user->isTenantAdmin()
            || ($document->prepared_by_user_id && $document->prepared_by_user_id === $user->id)
            || ($document->user_id && $document->user_id === $user->id)
            || ($document->created_by && $document->created_by === $user->id);

        if (! $canManage) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to modify document signatories.'], 403);
            }

            return back()->with('error', 'Unauthorized to modify document signatories.');
        }

        DB::beginTransaction();
        try {
            foreach ($validated['signatories'] as $sigInput) {
                $sig = DocumentSignatory::where('document_type', $document->getMorphClass())
                    ->where('document_id', $document->getKey())
                    ->where('id', $sigInput['id'])
                    ->first();

                if (! $sig) {
                    continue;
                }

                // If already signed, don't allow changing personnel unless superadmin/tenantadmin
                if ($sig->isSigned() && ! ($user->isSuperAdmin() || $user->isTenantAdmin())) {
                    continue;
                }

                $empId = $sigInput['employee_id'] ?? null;
                $emp = $empId ? Employee::with('department')->find($empId) : null;
                $userId = $empId ? User::where('employee_id', $empId)->value('id') : null;

                $name = $emp ? $emp->full_name : ($sigInput['signatory_name'] ?? $sig->signatory_name);
                $desig = $sigInput['designation'] ?? ($emp?->designation ?: $sig->designation);
                $office = $sigInput['office'] ?? ($emp?->department?->unit_name ?: $sig->office);
                $roleLabel = $sigInput['role_label'] ?? $sig->role_label;

                $sig->update([
                    'employee_id' => $empId,
                    'user_id' => $userId,
                    'signatory_name' => $name,
                    'designation' => $desig,
                    'office' => $office,
                    'role_label' => $roleLabel,
                    'assignment_type' => 'creator_selected',
                ]);

                // Sync with document columns if present (e.g. ExtensionPlan)
                $tier = $sig->tier_role;
                if ($tier === 'noted' && in_array('noted_by_employee_id', $document->getFillable())) {
                    $document->update([
                        'noted_by_employee_id' => $empId,
                        'noted_by_name' => $name,
                        'noted_by_designation' => $desig,
                    ]);
                } elseif ($tier === 'approved' && in_array('approved_by_employee_id', $document->getFillable())) {
                    $document->update([
                        'approved_by_employee_id' => $empId,
                        'approved_by_name' => $name,
                        'approved_by_designation' => $desig,
                    ]);
                } elseif ($tier === 'prepared' && in_array('prepared_by_employee_id', $document->getFillable())) {
                    $document->update([
                        'prepared_by_employee_id' => $empId,
                        'prepared_by_name' => $name,
                        'prepared_by_designation' => $desig,
                        'prepared_by_user_id' => $userId ?? $document->prepared_by_user_id,
                    ]);
                }

                // If linked to DTS routes, sync the office_name / courier remarks
                if ($document->dts_record_id) {
                    $stepNum = match ($tier) {
                        'noted', 'reviewed' => 1,
                        'approved' => 2,
                        default => null,
                    };
                    if ($stepNum) {
                        RecordRoute::where('record_id', $document->dts_record_id)
                            ->where('step_number', $stepNum)
                            ->where('status', '!=', 'completed')
                            ->update([
                                'unit_id' => $emp?->department_id,
                                'dept_id' => $emp?->department_id,
                                'office_name' => "{$name} ({$desig})",
                                'remarks' => "Signatory reassigned to: {$name} | Role: {$desig}",
                            ]);
                    }
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document signatories updated successfully.',
                    'signatories' => CentralizedSignatoryService::getSignatoriesForDocument($document),
                ]);
            }

            return back()->with('success', 'Document signatories updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update signatories: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to update signatories: '.$e->getMessage());
        }
    }

    /**
     * Resolve document model and meta for a given type alias and ID.
     *
     * @return array{document: mixed, meta: array<string, mixed>, class: string}
     */
    public static function resolveDocumentInstance(string $type, int|string $id): array
    {
        $id = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $type = strtolower(str_replace(['-', ' '], '_', $type));

        return match ($type) {
            'activity_design', 'activity_designs', 'activitydesign' => [
                'document' => ActivityDesign::with([
                    'extensionProposal.extensionPlan',
                    'extensionPlan',
                    'unit',
                    'campus',
                    'projectLeaderUser',
                    'projectLeaderEmployee',
                    'documentSignatories.employee',
                ])->findOrFail($id),
                'meta' => function (ActivityDesign $doc) {
                    return [
                        'type_key' => 'activity_design',
                        'type_label' => 'Activity Design',
                        'code' => $doc->design_code,
                        'title' => $doc->title,
                        'unit' => $doc->unit?->unit_name ?: 'Academic Unit',
                        'leader_name' => $doc->project_leader_name,
                        'leader_title' => $doc->project_leader_title ?: 'Project Leader',
                        'leader_employee_id' => $doc->project_leader_employee_id,
                        'budget' => $doc->formatted_budget_total,
                        'status' => $doc->status ?: 'Draft',
                        'parent_ref' => $doc->extensionProposal ? ('Proposal: '.$doc->extensionProposal->proposal_code) : null,
                        'parent_url' => $doc->extensionProposal ? route('dss.rie.proposals.show', $doc->extensionProposal->getRouteKey()) : null,
                        'back_url' => route('dss.rie.activity_designs.show', $doc->getRouteKey()),
                        'update_url' => route('dss.documents.signatories.update', ['type' => 'activity_design', 'id' => $doc->getRouteKey()]),
                        'suggest_url' => route('dss.documents.signatories.suggest', ['type' => 'activity_design', 'id' => $doc->getRouteKey()]),
                    ];
                },
                'class' => ActivityDesign::class,
            ],
            'extension_proposal', 'proposal', 'proposals', 'extensionproposal' => [
                'document' => ExtensionProposal::with([
                    'extensionPlan',
                    'unit',
                    'campus',
                    'projectLeaderEmployee',
                    'documentSignatories.employee',
                ])->findOrFail($id),
                'meta' => function (ExtensionProposal $doc) {
                    return [
                        'type_key' => 'extension_proposal',
                        'type_label' => 'Extension Proposal',
                        'code' => $doc->proposal_code,
                        'title' => $doc->title,
                        'unit' => $doc->unit?->unit_name ?: ($doc->department?->unit_name ?: 'Academic Unit'),
                        'leader_name' => $doc->project_leader_name ?: ($doc->projectLeaderEmployee?->full_name ?: 'Project Leader'),
                        'leader_title' => $doc->project_leader_title ?: 'Project Leader',
                        'leader_employee_id' => $doc->project_leader_employee_id,
                        'budget' => $doc->formatted_budget_total ?? (isset($doc->total_budget) ? ('₱'.number_format($doc->total_budget, 2)) : null),
                        'status' => $doc->status ?: 'Draft',
                        'parent_ref' => $doc->extensionPlan ? ('Plan: '.$doc->extensionPlan->plan_code) : null,
                        'parent_url' => $doc->extensionPlan ? route('dss.rie.plans.show', $doc->extensionPlan->getRouteKey()) : null,
                        'back_url' => route('dss.rie.proposals.show', $doc->getRouteKey()),
                        'update_url' => route('dss.documents.signatories.update', ['type' => 'extension_proposal', 'id' => $doc->getRouteKey()]),
                        'suggest_url' => route('dss.documents.signatories.suggest', ['type' => 'extension_proposal', 'id' => $doc->getRouteKey()]),
                    ];
                },
                'class' => ExtensionProposal::class,
            ],
            'extension_plan', 'plan', 'plans', 'extensionplan' => [
                'document' => ExtensionPlan::with([
                    'campus',
                    'academicProgram',
                    'documentSignatories.employee',
                ])->findOrFail($id),
                'meta' => function (ExtensionPlan $doc) {
                    return [
                        'type_key' => 'extension_plan',
                        'type_label' => 'Extension Plan',
                        'code' => $doc->plan_code,
                        'title' => $doc->extension_activity ?: ($doc->program_name ?: 'Extension Plan'),
                        'unit' => $doc->academicProgram?->program_name ?: ($doc->campus?->campus_name ?: 'Extension Unit'),
                        'leader_name' => $doc->prepared_by_name ?: ($doc->preparedByEmployee?->full_name ?: 'Proponent'),
                        'leader_title' => $doc->prepared_by_designation ?: 'Extension Coordinator',
                        'leader_employee_id' => $doc->prepared_by_employee_id,
                        'budget' => isset($doc->estimated_budget) ? ('₱'.number_format($doc->estimated_budget, 2)) : null,
                        'status' => $doc->status ?: 'Draft',
                        'parent_ref' => null,
                        'parent_url' => null,
                        'back_url' => route('dss.rie.plans.show', $doc->getRouteKey()),
                        'update_url' => route('dss.documents.signatories.update', ['type' => 'extension_plan', 'id' => $doc->getRouteKey()]),
                        'suggest_url' => route('dss.documents.signatories.suggest', ['type' => 'extension_plan', 'id' => $doc->getRouteKey()]),
                    ];
                },
                'class' => ExtensionPlan::class,
            ],
            default => abort(404, "Unsupported document type [{$type}] for signatory modification."),
        };
    }

    /**
     * Show centralized full page for configuring signatories workflow on any specific document.
     */
    public function editDocumentSignatories(Request $request, string $type, int|string $id): View
    {
        $resolved = self::resolveDocumentInstance($type, $id);
        $document = $resolved['document'];
        $meta = is_callable($resolved['meta']) ? $resolved['meta']($document) : $resolved['meta'];

        // Ensure signatories exist in sys_document_signatories
        $signatoriesCount = DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->count();

        if ($signatoriesCount === 0) {
            if ($document instanceof ActivityDesign) {
                ActivityDesignController::syncSignatoriesAndRoutes($document);
            } elseif ($document instanceof ExtensionProposal) {
                ExtensionProposalController::syncSignatoriesAndRoutes($document);
            } else {
                CentralizedSignatoryService::instantiateForDocument($document);
            }
            $document->refresh();
        }

        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($document);
        $institution = TenantContext::getInstitution();

        return view('dss.documents.configure_signatories', compact('document', 'meta', 'signatories', 'institution', 'type', 'id'));
    }

    /**
     * Centralized update endpoint for saving sequential signatories workflow on any document.
     */
    public function updateDocumentSignatoriesWorkflow(Request $request, string $type, int|string $id): JsonResponse
    {
        $resolved = self::resolveDocumentInstance($type, $id);
        $document = $resolved['document'];
        $meta = is_callable($resolved['meta']) ? $resolved['meta']($document) : $resolved['meta'];

        if (strtolower((string) $document->status) === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Signatories cannot be updated because the document is already approved.',
            ], 403);
        }

        $payload = $request->all();

        if ($document instanceof ActivityDesign) {
            ActivityDesignController::syncSignatoriesAndRoutes($document, $payload);
        } elseif ($document instanceof ExtensionProposal) {
            ExtensionProposalController::syncSignatoriesAndRoutes($document, $payload);
        } else {
            self::syncGenericDocumentSignatories($document, $payload);
        }

        return response()->json([
            'success' => true,
            'message' => "Signatories workflow for [{$meta['code']}] updated successfully.",
            'redirect_url' => $meta['back_url'],
            'count' => $document->documentSignatories()->count(),
        ]);
    }

    /**
     * Auto-suggest official signatories matrix for any document.
     */
    public function suggestDocumentSignatories(Request $request, string $type, int|string $id): JsonResponse
    {
        $resolved = self::resolveDocumentInstance($type, $id);
        $document = $resolved['document'];

        if ($document instanceof ActivityDesign) {
            $suggested = ActivityDesignController::resolveSuggestedSignatories($document)['signatories'] ?? [];
        } elseif ($document instanceof ExtensionProposal) {
            $suggested = ExtensionProposalController::resolveSuggestedSignatories($document)['signatories'] ?? [];
        } else {
            $suggested = CentralizedSignatoryService::getSignatoriesFor($document->getMorphClass(), $document->tenant_id ?: 1);
        }

        return response()->json([
            'success' => true,
            'signatories' => $suggested,
        ]);
    }

    /**
     * Generic sync for runtime document signatories in sys_document_signatories.
     *
     * @param  array<string, mixed>  $inputs
     */
    public static function syncGenericDocumentSignatories(mixed $document, array $inputs = []): void
    {
        $tenantId = $document->tenant_id ?: 1;
        $submitted = $inputs['signatories'] ?? null;
        if (is_array($submitted)) {
            $submitted = array_values($submitted);
        }

        if (empty($submitted)) {
            return;
        }

        $existingSigs = DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
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

            if (empty($name)) {
                continue;
            }

            $alignment = $item['alignment'] ?? 'left';
            $col = isset($item['position_column']) ? (int) $item['position_column'] : 1;

            $existingId = ! empty($item['id']) ? (int) $item['id'] : null;
            $existing = $existingId ? ($existingSigs[$existingId] ?? null) : null;

            $status = $existing?->status ?? 'pending';
            if ($idx === 0 || $tierRole === 'prepared') {
                $status = ($document->status !== 'draft') ? 'signed' : 'pending';
            }

            if ($existing) {
                $existing->update([
                    'role_label' => $roleLabel,
                    'tier_role' => $tierRole,
                    'assignment_type' => 'creator_selected',
                    'employee_id' => $empId,
                    'signatory_name' => $name,
                    'designation' => $designation,
                    'office' => $office,
                    'sort_order' => $sortOrder,
                    'position_column' => $col,
                    'alignment' => $alignment,
                    'status' => $status,
                ]);
                $processedIds[] = $existing->id;
            } else {
                $created = DocumentSignatory::create([
                    'document_type' => $document->getMorphClass(),
                    'document_id' => $document->getKey(),
                    'tenant_id' => $tenantId,
                    'campus_id' => $document->campus_id ?? null,
                    'dts_record_id' => $document->dts_record_id ?? null,
                    'role_label' => $roleLabel,
                    'tier_role' => $tierRole,
                    'assignment_type' => 'creator_selected',
                    'employee_id' => $empId,
                    'signatory_name' => $name,
                    'designation' => $designation,
                    'office' => $office,
                    'sort_order' => $sortOrder,
                    'position_column' => $col,
                    'alignment' => $alignment,
                    'status' => $status,
                    'requires_digital_signature' => true,
                    'is_mandatory' => true,
                ]);
                $processedIds[] = $created->id;
            }
        }

        // Delete removed non-signed signatories
        DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->whereNotIn('id', $processedIds)
            ->where('status', '!=', 'signed')
            ->delete();
    }
}
