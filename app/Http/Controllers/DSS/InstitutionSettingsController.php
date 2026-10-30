<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\DocumentType;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\PolicySignatory;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\SaaS\IsoDocumentControl;
use App\Models\SaaS\SignatoryMatrix;
use App\Models\User;
use App\Services\DSS\TenantContext;
use App\Services\PDF\PolicyGenerate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InstitutionSettingsController extends Controller
{
    public function index()
    {
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        return view('dss.settings.institution', compact('institution', 'theme'));
    }

    public function campuses()
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        $campuses = Campus::where('tenant_id', $tenant->id)->with('departments')->get();

        return view('dss.settings.school.campuses', compact('institution', 'theme', 'campuses', 'tenant'));
    }

    public function isoControlsPage()
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        $departments = Department::where('tenant_id', $tenant->id)->with('campus')->get();
        $isoControls = IsoDocumentControl::where('tenant_id', $tenant->id)
            ->with('department')
            ->orderBy('department_id')
            ->orderBy('document_type')
            ->get();

        return view('dss.settings.school.iso_controls', compact(
            'institution',
            'theme',
            'departments',
            'isoControls',
            'tenant'
        ));
    }

    public function docParams(): RedirectResponse
    {
        return redirect()->route('dss.dts.settings.doc_types', ['tab' => 'formatting']);
    }

    public function updateProfile(Request $request)
    {
        $institution = TenantContext::getInstitution();

        $validated = $request->validate([
            'school_name' => 'required|string|max:191',
            'school_acronym' => 'required|string|max:50',
            'school_type' => 'required|string|max:50',
            'address' => 'nullable|string',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'website_url' => 'nullable|string|max:191',
            'current_fy' => 'required|string|max:10',
            'charter_basis' => 'nullable|string|max:191',
            'vision_statement' => 'nullable|string',
            'mission_statement' => 'nullable|string',
        ]);

        $institution->update($validated);

        return response()->json([
            'result' => true,
            'message' => 'Institutional profile updated successfully!',
        ]);
    }

    public function storeCampus(Request $request)
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'campus_code' => 'required|string|max:50',
            'campus_name' => 'required|string|max:191',
            'campus_type' => 'required|string|max:50',
            'location' => 'nullable|string|max:191',
            'head_name' => 'nullable|string|max:100',
            'contact_no' => 'nullable|string|max:50',
        ]);

        $campus = Campus::create(array_merge($validated, ['tenant_id' => $tenant->id]));

        return response()->json([
            'result' => true,
            'message' => 'Campus unit added successfully!',
            'campus' => $campus,
        ]);
    }

    public function storeDepartment(Request $request)
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'dept_code' => 'required|string|max:50',
            'dept_name' => 'required|string|max:191',
            'division' => 'nullable|string|max:100',
            'head_name' => 'nullable|string|max:100',
        ]);

        $dept = Department::create(array_merge($validated, ['tenant_id' => $tenant->id]));

        return response()->json([
            'result' => true,
            'message' => 'Department/Office added successfully!',
            'department' => $dept,
        ]);
    }

    public function storeSignatory(Request $request)
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'process_type' => 'required|string|max:50',
            'step_order' => 'required|integer|min:1',
            'role_title' => 'required|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'employee_name' => 'nullable|string|max:100',
            'designation' => 'required|string|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);

        if (! empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
            if ($user) {
                $validated['employee_name'] = $user->name;
            }
        }

        if (empty($validated['employee_name'])) {
            return response()->json(['result' => false, 'message' => 'Please select a registered user.'], 422);
        }

        $signatory = SignatoryMatrix::create([
            'tenant_id' => $tenant->id,
            'process_type' => $validated['process_type'],
            'step_order' => $validated['step_order'],
            'role_title' => $validated['role_title'],
            'employee_name' => $validated['employee_name'],
            'designation' => $validated['designation'],
            'min_amount' => $validated['min_amount'] ?? 0.00,
            'max_amount' => $validated['max_amount'] ?? null,
            'is_mandatory' => true,
        ]);

        return response()->json([
            'result' => true,
            'message' => 'Approval signatory rule recorded!',
            'signatory' => $signatory,
        ]);
    }

    /** Save per-document-type template settings (greeting, closing, core values, column count). */
    public function updateDocumentSettings(Request $request): JsonResponse
    {
        $institution = TenantContext::getInstitution();

        $validated = $request->validate([
            'document_type' => 'required|string|max:100',
            'logo_layout' => 'nullable|string|in:centered,split_dual,dual_left,left,right,iso_box',
            'logo_size' => 'nullable|numeric|min:12.0|max:45.0',
            'logo_top_margin' => 'nullable|numeric|min:4.0|max:30.0',
            'show_secondary_logo' => 'nullable|boolean',
            'divider_style' => 'nullable|string|in:single_black,double_rule,theme_accent,none',
            'footer_logo_position' => 'nullable|string|in:left,center,right,split_dual,dual_left,dual_left_and_right,dual_right,none',
            'footer_left_slot_1' => 'nullable|string|max:100',
            'footer_left_slot_2' => 'nullable|string|max:100',
            'footer_right_slot_1' => 'nullable|string|max:100',
            'footer_right_slot_2' => 'nullable|string|max:100',
            'header_left_slot_1' => 'nullable|string|max:100',
            'header_left_slot_2' => 'nullable|string|max:100',
            'header_right_slot_1' => 'nullable|string|max:100',
            'show_footer_logo' => 'nullable|boolean',
            // Footer stamp placement (independent per element)
            'barcode_position' => 'nullable|string|in:above_left,above_center,above_right,footer_left,footer_center,footer_right,none',
            'qr_position' => 'nullable|string|in:above_left,above_center,above_right,footer_left,footer_center,footer_right,none',
            'show_footer_address' => 'nullable|boolean',
            'show_footer_contact' => 'nullable|boolean',
            'show_footer_website' => 'nullable|boolean',
            'show_core_values' => 'nullable|boolean',
            'show_distribution' => 'nullable|boolean',
            'distribution_text' => 'nullable|string|max:1000',
            'greeting' => 'nullable|string',
            'closing' => 'nullable|string',
            'core_values' => 'nullable|string|max:255',
            'office_name' => 'nullable|string|max:191',
            'header_school_name' => 'nullable|string|max:191',
            'header_address' => 'nullable|string|max:255',
            'font_family' => 'nullable|string|in:Arial,Times,Helvetica,Courier',
            'line_height' => 'nullable|numeric|min:1.0|max:3.0',
            'paragraph_spacing' => 'nullable|numeric|min:1.0|max:10.0',
            'signature_gap' => 'nullable|numeric|min:4.0|max:40.0',
            'signatory_columns' => 'nullable|integer|min:1|max:3',
            'signatory_layout' => 'nullable|string',
        ]);

        $settings = $institution->document_settings ?? [];
        $existing = $settings[$validated['document_type']] ?? [];

        $settings[$validated['document_type']] = [
            'logo_layout' => $validated['logo_layout'] ?? ($existing['logo_layout'] ?? 'centered'),
            'logo_size' => isset($validated['logo_size']) ? (float) $validated['logo_size'] : ($existing['logo_size'] ?? 24.0),
            'logo_top_margin' => isset($validated['logo_top_margin']) ? (float) $validated['logo_top_margin'] : ($existing['logo_top_margin'] ?? 10.0),
            'show_secondary_logo' => isset($validated['show_secondary_logo']) ? (bool) $validated['show_secondary_logo'] : ($existing['show_secondary_logo'] ?? false),
            'divider_style' => $validated['divider_style'] ?? ($existing['divider_style'] ?? 'single_black'),
            'footer_logo_position' => $validated['footer_logo_position'] ?? ($existing['footer_logo_position'] ?? 'left'),
            'footer_left_slot_1' => array_key_exists('footer_left_slot_1', $validated) ? $validated['footer_left_slot_1'] : ($existing['footer_left_slot_1'] ?? null),
            'footer_left_slot_2' => array_key_exists('footer_left_slot_2', $validated) ? $validated['footer_left_slot_2'] : ($existing['footer_left_slot_2'] ?? null),
            'footer_right_slot_1' => array_key_exists('footer_right_slot_1', $validated) ? $validated['footer_right_slot_1'] : ($existing['footer_right_slot_1'] ?? null),
            'footer_right_slot_2' => array_key_exists('footer_right_slot_2', $validated) ? $validated['footer_right_slot_2'] : ($existing['footer_right_slot_2'] ?? null),
            'header_left_slot_1' => array_key_exists('header_left_slot_1', $validated) ? $validated['header_left_slot_1'] : ($existing['header_left_slot_1'] ?? null),
            'header_left_slot_2' => array_key_exists('header_left_slot_2', $validated) ? $validated['header_left_slot_2'] : ($existing['header_left_slot_2'] ?? null),
            'header_right_slot_1' => array_key_exists('header_right_slot_1', $validated) ? $validated['header_right_slot_1'] : ($existing['header_right_slot_1'] ?? null),
            'show_footer_logo' => isset($validated['show_footer_logo']) ? (bool) $validated['show_footer_logo'] : ($existing['show_footer_logo'] ?? true),
            // Footer stamp placement (independent per element)
            'barcode_position' => $validated['barcode_position'] ?? ($existing['barcode_position'] ?? 'above_right'),
            'qr_position' => $validated['qr_position'] ?? ($existing['qr_position'] ?? 'above_right'),
            'show_footer_address' => isset($validated['show_footer_address']) ? (bool) $validated['show_footer_address'] : ($existing['show_footer_address'] ?? true),
            'show_footer_contact' => isset($validated['show_footer_contact']) ? (bool) $validated['show_footer_contact'] : ($existing['show_footer_contact'] ?? true),
            'show_footer_website' => isset($validated['show_footer_website']) ? (bool) $validated['show_footer_website'] : ($existing['show_footer_website'] ?? true),
            'show_core_values' => isset($validated['show_core_values']) ? (bool) $validated['show_core_values'] : ($existing['show_core_values'] ?? true),
            'show_distribution' => isset($validated['show_distribution']) ? (bool) $validated['show_distribution'] : ($existing['show_distribution'] ?? true),
            'distribution_text' => array_key_exists('distribution_text', $validated) ? $validated['distribution_text'] : ($existing['distribution_text'] ?? null),
            'office_name' => array_key_exists('office_name', $validated) ? $validated['office_name'] : ($existing['office_name'] ?? null),
            'header_school_name' => array_key_exists('header_school_name', $validated) ? $validated['header_school_name'] : ($existing['header_school_name'] ?? null),
            'header_address' => array_key_exists('header_address', $validated) ? $validated['header_address'] : ($existing['header_address'] ?? null),
            'greeting' => array_key_exists('greeting', $validated) ? $validated['greeting'] : ($existing['greeting'] ?? null),
            'closing' => array_key_exists('closing', $validated) ? $validated['closing'] : ($existing['closing'] ?? null),
            'core_values' => array_key_exists('core_values', $validated) ? $validated['core_values'] : ($existing['core_values'] ?? null),
            'font_family' => $validated['font_family'] ?? ($existing['font_family'] ?? 'Arial'),
            'line_height' => isset($validated['line_height']) ? (float) $validated['line_height'] : ($existing['line_height'] ?? 1.5),
            'paragraph_spacing' => isset($validated['paragraph_spacing']) ? (float) $validated['paragraph_spacing'] : ($existing['paragraph_spacing'] ?? 3.0),
            'signature_gap' => isset($validated['signature_gap']) ? (float) $validated['signature_gap'] : ($existing['signature_gap'] ?? 9.0),
            'signatory_columns' => isset($validated['signatory_columns']) ? (int) $validated['signatory_columns'] : ($existing['signatory_columns'] ?? 2),
            'signatory_layout' => $validated['signatory_layout'] ?? ($existing['signatory_layout'] ?? 'spread'),
        ];

        $institution->update(['document_settings' => $settings]);

        return response()->json([
            'result' => true,
            'message' => 'Document template settings saved for '.$validated['document_type'].'!',
        ]);
    }

    /** Register a new ISO Document Control configuration. */
    public function storeIsoControl(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'department_id' => 'nullable|exists:units,id',
            'office_code' => 'nullable|string|max:50',
            'office_name' => 'nullable|string|max:191',
            'document_type' => 'required|string|max:100',
            'doc_no' => 'required|string|max:50',
            'rev_no' => 'required|string|max:20',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $departmentId = $validated['department_id'] ?? null;
        if (! $departmentId && ! empty($validated['office_code'])) {
            $dept = Department::firstOrCreate(
                ['tenant_id' => $tenant->id, 'unit_code' => strtoupper($validated['office_code'])],
                [
                    'unit_name' => $validated['office_name'] ?: strtoupper($validated['office_code']),
                    'short_name' => strtoupper($validated['office_code']),
                    'is_active' => true,
                ]
            );
            $departmentId = $dept->id;
        }

        $control = IsoDocumentControl::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'department_id' => $departmentId,
                'document_type' => $validated['document_type'],
            ],
            [
                'tenant_id' => $tenant->id,
                'department_id' => $departmentId,
                'document_type' => $validated['document_type'],
                'doc_no' => $validated['doc_no'],
                'rev_no' => $validated['rev_no'],
                'effective_date' => $validated['effective_date'] ?? now()->format('Y-m-d'),
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]
        );

        $control->load('department');

        return response()->json([
            'result' => true,
            'message' => "ISO Document Control for {$control->office_code} ({$control->document_type}) saved successfully!",
            'control' => $control,
        ]);
    }

    /** Update an existing ISO Document Control rule. */
    public function updateIsoControl(Request $request, int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $control = IsoDocumentControl::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'nullable|exists:units,id',
            'document_type' => 'required|string|max:100',
            'doc_no' => 'required|string|max:50',
            'rev_no' => 'required|string|max:20',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $control->update([
            'department_id' => $validated['department_id'] ?? $control->department_id,
            'document_type' => $validated['document_type'],
            'doc_no' => $validated['doc_no'],
            'rev_no' => $validated['rev_no'],
            'effective_date' => $validated['effective_date'] ?? $control->effective_date,
            'notes' => $validated['notes'] ?? $control->notes,
        ]);

        $control->load('department');

        return response()->json([
            'result' => true,
            'message' => 'ISO Document Control updated successfully!',
            'control' => $control,
        ]);
    }

    /** Increment revision number and set new effective date for an ISO Document Control. */
    public function incrementIsoRevision(Request $request, int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $control = IsoDocumentControl::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'effective_date' => 'required|date',
            'new_rev_no' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:255',
        ]);

        if (! empty($validated['new_rev_no'])) {
            $control->rev_no = $validated['new_rev_no'];
            $control->effective_date = $validated['effective_date'];
            if (! empty($validated['notes'])) {
                $control->notes = $validated['notes'];
            }
            $control->save();
        } else {
            $control->incrementRevision(
                $validated['effective_date'],
                $validated['notes'] ?? null
            );
        }

        return response()->json([
            'result' => true,
            'message' => "Revision updated to Rev {$control->rev_no} (Effective: {$control->effective_date?->format('m/d/Y')})!",
            'control' => $control,
        ]);
    }

    /** Delete an ISO Document Control. */
    public function destroyIsoControl(int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $control = IsoDocumentControl::where('tenant_id', $tenant->id)->findOrFail($id);
        $control->delete();

        return response()->json([
            'result' => true,
            'message' => 'ISO Document Control removed.',
        ]);
    }

    /** Quick Ajax lookup for office and document type ISO parameters. */
    public function lookupIsoControl(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $officeCode = $request->query('office');
        $docType = $request->query('document_type');

        $control = IsoDocumentControl::resolveControl($tenant->id, $officeCode, $docType, fallbackToFirst: false);

        if (! $control) {
            return response()->json([
                'result' => false,
                'has_custom_control' => false,
                'control' => [
                    'doc_no' => 'F-ISO-001',
                    'rev_no' => '0',
                    'effective_date' => date('m/d/Y'),
                    'page_no' => '1 of 1',
                    'office_code' => $officeCode,
                    'document_type' => $docType,
                ],
            ]);
        }

        return response()->json([
            'result' => true,
            'has_custom_control' => true,
            'control' => [
                'doc_no' => $control->doc_no,
                'rev_no' => $control->rev_no,
                'effective_date' => $control->effective_date ? $control->effective_date->format('m/d/Y') : date('m/d/Y'),
                'page_no' => '1 of 1',
                'office_code' => $control->office_code,
                'office_name' => $control->office_name,
                'department_id' => $control->department_id,
                'document_type' => $control->document_type,
            ],
        ]);
    }

    /** Add a reusable institution-level signatory template. */
    public function storeSignatoryTemplate(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'document_type' => 'nullable|string|max:100',
            'user_id' => 'required|exists:users,id',
            'role_label' => 'required|string|max:100',
            'employee_name' => 'nullable|string|max:150',
            'designation' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'col_span' => 'nullable|integer|min:1|max:12',
            'position_column' => 'nullable|integer|min:1|max:12',
            'alignment' => 'nullable|string|in:left,center,right',
            'sort_order' => 'required|integer|min:1',
            'show_signature_line' => 'boolean',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $validated['employee_name'] = $user->name;
        $validated['col_span'] = (int) ($validated['col_span'] ?? 6);
        $validated['position_column'] = (int) ($validated['position_column'] ?? 1);
        $validated['alignment'] = $validated['alignment'] ?? 'left';

        $signatory = PolicySignatory::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'policy_id' => null,
            'is_template' => true,
        ]));

        return response()->json([
            'result' => true,
            'message' => 'Signatory template added!',
            'signatory' => $signatory->load('user'),
        ]);
    }

    /** Update an institution-level signatory template (e.g. col_span, alignment, role_label, etc.). */
    public function updateSignatoryTemplate(Request $request, int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $signatory = PolicySignatory::where('tenant_id', $tenant->id)
            ->where('is_template', true)
            ->findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'col_span' => 'nullable|integer|min:1|max:12',
            'position_column' => 'nullable|integer|min:1|max:12',
            'alignment' => 'nullable|string|in:left,center,right',
            'sort_order' => 'nullable|integer|min:1',
            'role_label' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:150',
            'office' => 'nullable|string|max:150',
            'show_signature_line' => 'nullable|boolean',
        ]);

        if (! empty($validated['user_id'])) {
            $user = User::findOrFail($validated['user_id']);
            $validated['employee_name'] = $user->name;
        }

        $signatory->update(array_filter($validated, fn ($val) => ! is_null($val)));

        return response()->json([
            'result' => true,
            'message' => 'Signatory template updated!',
            'signatory' => $signatory->fresh()->load('user'),
        ]);
    }

    /** Batch reorder signatory templates. */
    public function reorderSignatories(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $validated = $request->validate([
            'signatories' => 'required|array',
            'signatories.*.id' => 'required|integer',
            'signatories.*.sort_order' => 'required|integer|min:1',
            'signatories.*.col_span' => 'nullable|integer|min:1|max:12',
        ]);

        DB::transaction(function () use ($tenant, $validated) {
            foreach ($validated['signatories'] as $item) {
                $updateData = ['sort_order' => $item['sort_order']];
                if (isset($item['col_span'])) {
                    $updateData['col_span'] = $item['col_span'];
                }
                PolicySignatory::where('tenant_id', $tenant->id)
                    ->where('is_template', true)
                    ->where('id', $item['id'])
                    ->update($updateData);
            }
        });

        return response()->json([
            'result' => true,
            'message' => 'Signatories order and layout updated successfully!',
        ]);
    }

    /** Remove an institution-level signatory template. */
    public function destroySignatoryTemplate(int $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $signatory = PolicySignatory::where('tenant_id', $tenant->id)
            ->where('is_template', true)
            ->findOrFail($id);

        $signatory->delete();

        return response()->json(['result' => true, 'message' => 'Signatory template removed.']);
    }

    /** Generate live PDF preview for a document type template. */
    public function previewPdf(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $docType = $request->query('document_type', 'University Memorandum');
        $template = $request->query('template', 'executive');
        $paper = $request->query('paper', '8.5x13');

        $samplePolicy = new InstitutionalPolicy;
        $samplePolicy->tenant_id = $tenant->id;
        $samplePolicy->tracking_number = 'DTS-LIVE-'.date('Ymd').'-001';
        $samplePolicy->policy_number = 'MEMO-'.date('Y').'-PREVIEW';
        $samplePolicy->title = 'OPERATING PROCEDURES AND STANDARDIZED TEMPLATE GUIDELINES FOR INSTITUTIONAL ISSUANCES';
        $samplePolicy->document_type = $docType;
        $samplePolicy->template_type = $template;
        $samplePolicy->paper_size = $paper;
        $samplePolicy->doc_control_no = 'F-POL-001';
        $samplePolicy->doc_rev_no = '1';
        $samplePolicy->doc_effective_date = now();
        $samplePolicy->approving_authority = 'University President';
        $samplePolicy->legal_basis = $institution->charter_basis ?: 'Republic Act 8292; CHED CMO No. 46 s. 2012; ISO 9001:2015 Standards';
        $samplePolicy->rationale = 'This official template preview validates the formatting, margins, typography, greeting, closing salutation, QR security stamping, and multi-column signatory distribution for all university issuances.';
        $samplePolicy->scope = 'all academic departments, administrative directorates, campus units, faculty, and administrative personnel.';
        $samplePolicy->provisions = [
            [
                'article' => 'ARTICLE I',
                'title' => 'PURPOSE AND QUALITY COMPLIANCE',
                'clauses' => [
                    'Section 1. Institutional Standards: All administrative offices and campuses shall maintain consistent styling, official headers, and verified signatories for all promulgated issuances.',
                    'Section 2. Audit Trail: Every official document issued under this template shall carry a tamper-evident DTS QR tracking code for instant authentication.',
                ],
            ],
            [
                'article' => 'ARTICLE II',
                'title' => 'IMPLEMENTATION AND EXECUTION',
                'clauses' => [
                    'Section 1. Dissemination: Official copies shall be distributed in accordance with the registered distribution matrix upon executive promulgation.',
                ],
            ],
        ];
        $samplePolicy->sunset_review_years = 3;
        $samplePolicy->effectivity_date = now();

        $pdf = new PolicyGenerate($samplePolicy, $institution, $paper, $template);
        $prefix = $institution?->school_acronym ?: 'INST';
        $docSlug = str_replace(' ', '_', $docType);
        $fileName = "Preview_{$prefix}_{$docSlug}_{$template}_{$paper}.pdf";

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /** Visual Document Template Studio Screen (Dedicated full page). */
    public function templateStudio(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $tenantId = $tenant?->id ?? 1;

        // 1. Standard institutional document types across SUCs
        $standardTypes = [
            'Generic (Default)',
            'University Memorandum',
            'Administrative Order',
            'Board Resolution',
            'Special Order',
            'Office Circular',
            'Travel Authority',
            'Implementing Rules & Regulations',
            'Institutional Policy Code',
        ];

        // 2. Tenant's registered DocumentType classifications from Process Catalog
        $dbTypes = DocumentType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $docTypes = array_values(array_unique(array_merge($standardTypes, $dbTypes)));

        $currentDocType = $request->query('document_type', 'Generic (Default)');
        if (! in_array($currentDocType, $docTypes)) {
            $currentDocType = 'Generic (Default)';
        }

        $usersQuery = User::with([
            'department',
            'employee.department',
            'employeeDesignations.department',
            'employeeDesignations.designationModel',
            'employee.designations.department',
            'employee.designations.designationModel',
        ]);

        if ($tenantId) {
            $usersQuery->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            });
        }

        $users = $usersQuery->orderBy('name')->get();
        if ($users->isEmpty()) {
            $users = User::orderBy('name')->get();
        }

        $users->each(function ($u) {
            $list = [];

            // 1. Direct user designation
            if (! empty($u->designation)) {
                $list[] = [
                    'designation' => $u->designation,
                    'office' => $u->department->unit_name ?? ($u->department->name ?? ''),
                    'source' => 'Primary Designation',
                    'is_primary' => true,
                ];
            }

            // 2. Employee designations on user
            if ($u->employeeDesignations && $u->employeeDesignations->isNotEmpty()) {
                foreach ($u->employeeDesignations as $ed) {
                    $desigName = $ed->designation ?: ($ed->designationModel->name ?? '');
                    $officeName = $ed->department->unit_name ?? ($ed->department->name ?? '');
                    if (! empty($desigName)) {
                        $exists = false;
                        foreach ($list as $idx => $item) {
                            if (strcasecmp($item['designation'], $desigName) === 0) {
                                if (empty($item['office']) && ! empty($officeName)) {
                                    $list[$idx]['office'] = $officeName;
                                }
                                $exists = true;
                                break;
                            }
                        }
                        if (! $exists) {
                            $list[] = [
                                'designation' => $desigName,
                                'office' => $officeName,
                                'source' => 'Institutional Designation',
                                'is_primary' => (bool) $ed->is_primary,
                            ];
                        }
                    }
                }
            }

            // 3. Employee on user
            if ($u->employee) {
                if (! empty($u->employee->designation)) {
                    $empDesig = $u->employee->designation;
                    $empOffice = $u->employee->department->unit_name ?? ($u->employee->department->name ?? '');
                    $exists = false;
                    foreach ($list as $idx => $item) {
                        if (strcasecmp($item['designation'], $empDesig) === 0) {
                            if (empty($item['office']) && ! empty($empOffice)) {
                                $list[$idx]['office'] = $empOffice;
                            }
                            $exists = true;
                            break;
                        }
                    }
                    if (! $exists) {
                        $list[] = [
                            'designation' => $empDesig,
                            'office' => $empOffice,
                            'source' => 'Employee Record',
                            'is_primary' => false,
                        ];
                    }
                }

                if ($u->employee->designations && $u->employee->designations->isNotEmpty()) {
                    foreach ($u->employee->designations as $ed) {
                        $desigName = $ed->designation ?: ($ed->designationModel->name ?? '');
                        $officeName = $ed->department->unit_name ?? ($ed->department->name ?? '');
                        if (! empty($desigName)) {
                            $exists = false;
                            foreach ($list as $idx => $item) {
                                if (strcasecmp($item['designation'], $desigName) === 0) {
                                    if (empty($item['office']) && ! empty($officeName)) {
                                        $list[$idx]['office'] = $officeName;
                                    }
                                    $exists = true;
                                    break;
                                }
                            }
                            if (! $exists) {
                                $list[] = [
                                    'designation' => $desigName,
                                    'office' => $officeName,
                                    'source' => 'Employee Designation',
                                    'is_primary' => (bool) $ed->is_primary,
                                ];
                            }
                        }
                    }
                }
            }

            // Fallback if empty
            if (empty($list)) {
                $list[] = [
                    'designation' => $u->role ?: 'Institutional Official',
                    'office' => $u->department->unit_name ?? ($u->department->name ?? 'Office of the University President'),
                    'source' => 'Default Role',
                    'is_primary' => true,
                ];
            }

            usort($list, function ($a, $b) {
                return ($b['is_primary'] ? 1 : 0) <=> ($a['is_primary'] ? 1 : 0);
            });

            $u->setAttribute('designations_list', $list);
        });
        $policySignatories = PolicySignatory::where('tenant_id', $tenantId)
            ->where('is_template', true)
            ->with('user')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $docSettings = $institution->document_settings ?? [];
        $genericSettings = $docSettings['Generic (Default)'] ?? ($docSettings['Generic'] ?? []);

        // Fallback cascade: specific document type -> Generic defaults -> system defaults
        $currentSettings = $docSettings[$currentDocType] ?? [];
        $currentSettings = array_merge([
            'logo_layout' => 'centered',
            'logo_size' => 24.0,
            'logo_top_margin' => 10.0,
            'show_secondary_logo' => 0,
            'divider_style' => 'single_black',
            'footer_logo_position' => 'left',
            'footer_left_slot_1' => 'seal',
            'footer_left_slot_2' => null,
            'footer_right_slot_1' => null,
            'footer_right_slot_2' => null,
            'header_left_slot_1' => 'seal',
            'header_left_slot_2' => null,
            'header_right_slot_1' => null,
            'show_footer_logo' => 1,
            'greeting' => 'Madujow nu aedow! Greetings from '.($institution->school_acronym ?: 'the University').'!',
            'closing' => 'For strict compliance and immediate dissemination.',
            'core_values' => $institution->core_values ?? 'Accountability | Responsiveness | Innovativeness | Selflessness | Excellence',
            'font_family' => 'Arial',
            'line_height' => 1.5,
            'paragraph_spacing' => 3.0,
            'signature_gap' => 9.0,
            'signatory_columns' => 2,
            'signatory_layout' => 'spread',
        ], $genericSettings, $currentSettings);

        // Sample policy matching actual template structure for the live sheet
        $samplePolicy = new InstitutionalPolicy;
        $samplePolicy->tenant_id = $tenantId;
        $samplePolicy->tracking_number = 'DTS-'.date('Y').'-STD89';
        $samplePolicy->policy_number = 'MEMO-'.date('Y').'-018';
        $samplePolicy->title = strtoupper($currentDocType === 'Generic (Default)' ? 'Official Institutional Standard' : $currentDocType).' COMPLIANCE & OPERATIONAL FRAMEWORK';
        $samplePolicy->document_type = $currentDocType;
        $samplePolicy->approving_authority = 'University President';
        $samplePolicy->legal_basis = $institution->charter_basis ?: 'Republic Act 8292; CHED CMO No. 46 s. 2012; ISO 9001:2015 Standards';
        $samplePolicy->rationale = 'This operational instrument establishes the standard institutional operating procedures, accountability framework, and governance compliance protocols across all campuses and departments of '.$institution->school_name.'.';
        $samplePolicy->scope = 'all university campuses, academic colleges, non-teaching units, faculty, administrative staff, and designated officials.';
        $samplePolicy->provisions = [
            [
                'article' => 'ARTICLE I',
                'title' => 'GENERAL PROVISIONS & MANDATE',
                'clauses' => [
                    'Section 1. Institutional Framework: Pursuant to official institutional mandates and statutory powers under Republic Act 8292, this document sets forth the comprehensive guidelines governing all operations within '.$institution->school_name.'.',
                    'Section 2. Mandatory Adherence: All academic and administrative units are enjoined to maintain strict compliance and align local operating manuals with the statutory benchmarks defined herein.',
                ],
            ],
            [
                'article' => 'ARTICLE II',
                'title' => 'IMPLEMENTATION & MONITORING PROTOCOLS',
                'clauses' => [
                    'Section 1. Executive Oversight: The Office of the University President, together with the Vice Presidents and Campus Directors, shall oversee periodic compliance audits and quarterly performance reviews.',
                    'Section 2. Corrective Adjudication: Any non-compliance or procedural deviation shall be submitted to the University Legal Counsel and Quality Assurance Directorate for corrective action.',
                ],
            ],
        ];
        $samplePolicy->sunset_review_years = 3;
        $samplePolicy->effectivity_date = now();

        $sealUrl = null;
        if (! empty($institution->seal_path) && file_exists(public_path($institution->seal_path))) {
            $sealUrl = asset($institution->seal_path);
        } elseif (file_exists(public_path("storage/tenants/{$tenantId}/branding/print-seal-1024x1024.png"))) {
            $sealUrl = asset("storage/tenants/{$tenantId}/branding/print-seal-1024x1024.png");
        } elseif (file_exists(public_path("storage/tenants/{$tenantId}/branding/master-512x512.png"))) {
            $sealUrl = asset("storage/tenants/{$tenantId}/branding/master-512x512.png");
        }

        // Secondary Logo (e.g. Bagong Pilipinas or auxiliary seal)
        $secondaryLogoUrl = null;
        if (file_exists(public_path("storage/tenants/{$tenantId}/branding/secondary-logo.png"))) {
            $secondaryLogoUrl = asset("storage/tenants/{$tenantId}/branding/secondary-logo.png");
        } elseif (file_exists(public_path('images/bagong_pilipinas.png'))) {
            $secondaryLogoUrl = asset('images/bagong_pilipinas.png');
        } elseif (file_exists(public_path('images/bagong-pilipinas-logo.png'))) {
            $secondaryLogoUrl = asset('images/bagong-pilipinas-logo.png');
        } elseif ($sealUrl) {
            $secondaryLogoUrl = $sealUrl; // fallback mirror for preview
        }

        // Quality / ISO Certification Logo (e.g. SOCOTEC ISO 9001)
        $isoLogoUrl = null;
        if (file_exists(public_path("storage/tenants/{$tenantId}/branding/iso-logo.png"))) {
            $isoLogoUrl = asset("storage/tenants/{$tenantId}/branding/iso-logo.png");
        } elseif (file_exists(public_path('images/socotec_iso9001.png'))) {
            $isoLogoUrl = asset('images/socotec_iso9001.png');
        }

        // Aggregate All Available Logos for Template Placement (Built-in + Custom Uploaded)
        $availableLogos = [];
        if ($sealUrl) {
            $availableLogos[] = [
                'id' => 'seal',
                'label' => ($institution->school_acronym ?: 'University').' Official Seal',
                'url' => $sealUrl,
                'category' => 'seal',
            ];
        }
        if ($secondaryLogoUrl) {
            $availableLogos[] = [
                'id' => 'secondary',
                'label' => 'Bagong Pilipinas / Auxiliary',
                'url' => $secondaryLogoUrl,
                'category' => 'national',
            ];
        }
        if ($isoLogoUrl) {
            $availableLogos[] = [
                'id' => 'iso',
                'label' => 'ISO 9001:2015 Quality Certified',
                'url' => $isoLogoUrl,
                'category' => 'certification',
            ];
        }
        if (file_exists(public_path('images/pab_accredited.jpg'))) {
            $availableLogos[] = [
                'id' => 'pab',
                'label' => 'PAB Accredited Certification',
                'url' => asset('images/pab_accredited.jpg'),
                'category' => 'certification',
            ];
        }
        $customLogos = $institution->logo_variants['custom_logos'] ?? [];
        foreach ($customLogos as $cl) {
            $availableLogos[] = [
                'id' => $cl['id'],
                'label' => $cl['label'] ?? 'Custom Logo',
                'url' => asset($cl['relative_path']),
                'category' => $cl['category'] ?? 'custom',
            ];
        }

        $schoolName = $institution->school_name ?: 'INSTITUTIONAL NAME';
        $schoolAddress = $institution->address ?: '';

        return view('dss.settings.template_studio', compact(
            'tenant',
            'institution',
            'theme',
            'docTypes',
            'currentDocType',
            'users',
            'policySignatories',
            'docSettings',
            'currentSettings',
            'samplePolicy',
            'sealUrl',
            'secondaryLogoUrl',
            'isoLogoUrl',
            'availableLogos',
            'schoolName',
            'schoolAddress'
        ));
    }

    /**
     * Upload custom institutional logo to the template studio library.
     */
    public function uploadTemplateLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:10240',
            'label' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:50',
        ]);

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $tenantId = $tenant->id;

        $targetDir = public_path("storage/tenants/{$tenantId}/branding/custom_logos");
        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();
        $filename = 'logo_'.time().'_'.uniqid().'.'.$extension;
        $file->move($targetDir, $filename);

        $relativePath = "storage/tenants/{$tenantId}/branding/custom_logos/{$filename}";
        $label = $request->input('label') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $category = $request->input('category') ?: 'custom';

        $variants = $institution->logo_variants ?? [];
        $customLogos = $variants['custom_logos'] ?? [];

        $newLogo = [
            'id' => 'custom_'.uniqid(),
            'label' => $label,
            'category' => $category,
            'relative_path' => $relativePath,
            'url' => asset($relativePath),
            'uploaded_at' => now()->toIso8601String(),
        ];

        $customLogos[] = $newLogo;
        $variants['custom_logos'] = $customLogos;
        $institution->update(['logo_variants' => $variants]);

        return response()->json([
            'result' => true,
            'message' => 'Logo uploaded successfully and added to library!',
            'logo' => $newLogo,
        ]);
    }
}
