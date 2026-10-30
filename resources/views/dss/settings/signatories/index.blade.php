@extends('layouts.dss.app')

@section('title', 'Centralized Document Signatories Matrix — System Configuration')

@push('styles')
<style>
    .matrix-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .matrix-card-header {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sig-tier-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
    }
    .sig-tier-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
        transform: translateY(-2px);
    }
    .sig-tier-badge {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 20px;
    }
    .live-preview-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Breadcrumb & Header Title -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb m-0 extra-small font-mono">
                    <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">DSS</a></li>
                    <li class="breadcrumb-item"><span class="text-primary fw-semibold">System Configuration</span></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Signatory Matrix</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-draw text-primary"></i>
                <span>Centralized Document Signatories Matrix</span>
                <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle extra-small px-2 py-1">REUSABLE HUB</span>
            </h4>
            <div class="text-muted extra-small mt-0.5">
                Configure, standardize, and govern authorized document signatories and digital signing rules per school, campus, and process catalog.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($activeDocType)
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-2xs fw-semibold" data-bs-toggle="modal" data-bs-target="#addSignatoryModal">
                    <i class="mdi mdi-plus me-1"></i> Add Signatory Rule
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-2xs mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. Campus Scope Switcher -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="mdi mdi-office-building-marker text-muted"></i>
            <span class="extra-small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Institutional Campus Scope</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dss.settings.signatories.index', ['campus_id' => '', 'doc_type_id' => $selectedDocTypeId]) }}"
               class="btn btn-sm rounded-pill px-3 fw-semibold {{ is_null($selectedCampusId) ? 'btn-primary' : 'btn-light border text-secondary' }}">
                <i class="mdi mdi-earth me-1"></i> All Campuses (Global Default)
            </a>

            @foreach($campuses as $camp)
                <a href="{{ route('dss.settings.signatories.index', ['campus_id' => $camp->id, 'doc_type_id' => $selectedDocTypeId]) }}"
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ $selectedCampusId === $camp->id ? 'btn-primary' : 'btn-light border text-secondary' }}">
                    <i class="mdi mdi-domain me-1"></i> {{ $camp->campus_name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- 2. Document Types Catalog (Server-Side Select2 from dts_document_types) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                    <i class="mdi mdi-file-tree-outline"></i>
                </div>
                <div>
                    <span class="extra-small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Document Process Catalog (from DTS)</span>
                    <div class="extra-small text-muted">Select or search document classification via Server-Side AJAX</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($activeDocType)
                    <span class="badge bg-dark rounded-pill px-3 py-1.5 extra-small font-mono">
                        <i class="mdi {{ $activeDocType->icon ?: 'mdi-file-document-outline' }} text-warning me-1"></i>
                        {{ $activeDocType->name }}
                        @if($activeDocType->code)
                            <span class="text-white-50 ms-1">({{ $activeDocType->code }})</span>
                        @endif
                    </span>
                @endif
                @if(Route::has('dss.dts.settings.doc_types'))
                    <a href="{{ route('dss.dts.settings.doc_types') }}" class="extra-small text-decoration-none text-primary fw-semibold" title="Manage master document classifications">
                        Manage Catalog &rarr;
                    </a>
                @endif
            </div>
        </div>

        <div class="row align-items-center g-2 mt-1">
            <div class="col-lg-8 col-md-7">
                <select id="docTypeSelect2" class="form-select form-select-sm" style="width: 100%;">
                    @if($activeDocType)
                        <option value="{{ $activeDocType->id }}" selected>
                            {{ $activeDocType->name }} {{ $activeDocType->code ? '('.$activeDocType->code.')' : '' }}
                        </option>
                    @else
                        <option value="">-- Search &amp; Select Document Process --</option>
                    @endif
                </select>
            </div>
            <div class="col-lg-4 col-md-5 d-flex align-items-center justify-content-md-end gap-2">
                @if($activeDocType)
                    <div class="extra-small font-mono text-muted d-flex align-items-center gap-1">
                        <i class="mdi mdi-shield-check text-success"></i>
                        <span>{{ $matrices->count() }} Matrix Signatories Active</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Signatory Matrix Rules for Selected Document Type -->
    @if($activeDocType)
        <div class="matrix-card">
            <div class="matrix-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(13, 148, 136, 0.1); color: #0d9488;">
                        <i class="mdi {{ $activeDocType->icon ?: 'mdi-draw' }} fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0">{{ $activeDocType->name }} — Signatory Flow</h6>
                        <div class="text-muted extra-small">
                            Scope: {{ $selectedCampusId ? ($campuses->firstWhere('id', $selectedCampusId)?->campus_name ?? 'Campus') : 'All Campuses (Global Institution Rule)' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($matrices->isEmpty())
                        <form action="{{ route('dss.settings.signatories.seed_defaults') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                            <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 extra-small fw-bold">
                                <i class="mdi mdi-auto-fix me-1"></i> Pre-populate 3-Tier Route
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-4">
                @if($matrices->isNotEmpty())
                    <div class="row g-3 mb-4">
                        @foreach($matrices as $matrix)
                            @php
                                $tierColors = [
                                    'prepared' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
                                    'reviewed' => ['bg' => '#fefce8', 'text' => '#a16207', 'border' => '#fef08a'],
                                    'noted' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
                                    'recommending_approval' => ['bg' => '#faf5ff', 'text' => '#7e22ce', 'border' => '#e9d5ff'],
                                    'approved' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
                                ];
                                $tColor = $tierColors[$matrix->tier_role] ?? ['bg' => '#f8fafc', 'text' => '#334155', 'border' => '#e2e8f0'];
                            @endphp
                            <div class="col-md-4">
                                <div class="sig-tier-card h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="sig-tier-badge" style="background: {{ $tColor['bg'] }}; color: {{ $tColor['text'] }}; border: 1px solid {{ $tColor['border'] }};">
                                                Tier {{ $matrix->sort_order }}: {{ strtoupper($matrix->tier_role) }}
                                            </span>
                                            <span class="extra-small text-muted font-mono">Col {{ $matrix->position_column }}</span>
                                        </div>

                                        <div class="extra-small fw-bold text-muted text-uppercase mb-1">{{ $matrix->role_label }}</div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $matrix->resolved_name }}</h6>
                                        <div class="small text-muted mb-2">{{ $matrix->resolved_designation }}</div>

                                        @if($matrix->assignment_type !== 'fixed_employee')
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill extra-small">
                                                <i class="mdi mdi-lightning-bolt me-1"></i> Dynamic: {{ str_replace('_', ' ', $matrix->assignment_type) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-1">
                                            @if($matrix->requires_digital_signature)
                                                <span class="badge bg-success-subtle text-success extra-small" title="Digital signature required">
                                                    <i class="mdi mdi-draw"></i> E-Sign
                                                </span>
                                            @endif
                                            @if($matrix->is_mandatory)
                                                <span class="badge bg-danger-subtle text-danger extra-small" title="Mandatory signatory">
                                                    Required
                                                </span>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <form action="{{ route('dss.settings.signatories.destroy', $matrix->id) }}" method="POST" onsubmit="return confirm('Remove this signatory rule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete">
                                                    <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Live Visual Signature Block Preview -->
                    <div class="mt-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-eye-outline text-muted"></i>
                            <span class="extra-small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Live Document Signature Block Preview</span>
                        </div>
                        <div class="live-preview-box">
                            <div class="row g-4 text-center">
                                @foreach($matrices as $m)
                                    <div class="col-md-{{ 12 / min(max($matrices->count(), 1), 3) }}">
                                        <div class="extra-small fw-bold text-uppercase text-muted mb-4">{{ $m->role_label }}</div>
                                        <div style="height: 40px; border-bottom: 1.5px solid #0f172a; margin: 0 auto 6px auto; max-width: 240px;"></div>
                                        <div class="fw-bold text-dark text-uppercase small">{{ $m->resolved_name }}</div>
                                        <div class="extra-small text-muted">{{ $m->resolved_designation }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background: #f1f5f9; color: #64748b;">
                            <i class="mdi mdi-draw-pen fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Signatory Matrix Defined Yet</h6>
                        <p class="text-muted extra-small mb-4" style="max-width: 450px; margin: 0 auto;">
                            Configure the official signatories for {{ $activeDocType->name }}. You can pre-populate the standard 3-tier university route (Prepared by, Noted by, Approved by) with one click.
                        </p>
                        <form action="{{ route('dss.settings.signatories.seed_defaults') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                            <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-2xs fw-bold">
                                <i class="mdi mdi-auto-fix me-1"></i> Pre-populate Standard 3-Tier Route
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal: Add Signatory Rule -->
@if($activeDocType)
<div class="modal fade" id="addSignatoryModal" tabindex="-1" aria-labelledby="addSignatoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h6 class="modal-title fw-bold" id="addSignatoryModalLabel">
                    <i class="mdi mdi-draw me-1"></i> Add Signatory Rule — {{ $activeDocType->name }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dss.settings.signatories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">

                <div class="modal-body p-4">
                    <!-- 1. Tier Role & Display Label (Manual Input with Presets & Sync) -->
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Tier Action Role</label>
                            <select name="tier_role" id="tierRoleSelect" class="form-select form-select-sm" onchange="syncTierToDisplayLabel(this.value)">
                                <option value="prepared">Prepared by</option>
                                <option value="reviewed">Reviewed by</option>
                                <option value="noted" selected>Noted by</option>
                                <option value="recommending_approval">Recommending Approval</option>
                                <option value="approved">Approved by</option>
                                <option value="certified">Certified Correct</option>
                                <option value="custom">Custom (Manual)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase d-flex justify-content-between">
                                <span>Display Label (Manual)</span>
                                <span class="text-primary extra-small font-sans fw-normal">Free text</span>
                            </label>
                            <input type="text" name="role_label" id="roleLabelInput" class="form-control form-control-sm font-semibold" placeholder="Type label (e.g. Reviewed by:)" value="Noted by:" required>
                        </div>
                    </div>

                    <!-- Quick Preset Badges -->
                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-3 p-2 rounded-2 bg-light border border-slate-200">
                        <span class="extra-small text-muted font-mono me-1">Quick presets:</span>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('prepared', 'Prepared by:')">Prepared by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('reviewed', 'Reviewed by:')">Reviewed by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('noted', 'Noted by:')">Noted by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('recommending_approval', 'Recommending Approval:')">Recommending Approval:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('approved', 'Approved by:')">Approved by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small quick-label-btn" onclick="setQuickLabel('certified', 'Certified Correct:')">Certified Correct:</button>
                    </div>

                    <!-- 2. Assignment Type -->
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Assignee Source</label>
                        <select name="assignment_type" id="assignmentTypeSelect" class="form-select form-select-sm" required onchange="toggleAssigneeFields()">
                            <option value="fixed_employee" selected>Specific Personnel / Employee (From Directory)</option>
                            <option value="creator_selected">Chosen by Document Creator on Form (e.g. Dean, Extension Head)</option>
                            <option value="manual_signatory">Manual Signatory (Type Name &amp; Title Directly)</option>
                            <option value="dynamic_author">Dynamic: Document Author / Proponent</option>
                            <option value="dynamic_dept_head">Dynamic: Originating Department / Unit Head</option>
                            <option value="dynamic_campus_director">Dynamic: Campus Executive Director</option>
                        </select>
                    </div>

                    <!-- 3A. Specific Employee Select (Server-Side Select2) -->
                    <div class="mb-3" id="employeeSelectWrap">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Select Designated Personnel</label>
                        <select name="employee_id" id="employeeSelect" class="form-select form-select-sm" style="width: 100%;">
                            <option value="">-- Type to search employee... --</option>
                        </select>
                    </div>

                    <!-- 3B. Manual Signatory Name (If manual_signatory chosen) -->
                    <div class="mb-3" id="manualSignatoryWrap" style="display: none;">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Full Name (Manual)</label>
                        <input type="text" name="signatory_name" id="manualSignatoryNameInput" class="form-control form-control-sm font-semibold" placeholder="e.g. Dr. Juan Dela Cruz, Ph.D.">
                    </div>

                    <!-- 4. Override Designation -->
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Title / Position Designation</label>
                        <input type="text" name="designation" id="designationInput" class="form-control form-control-sm" placeholder="e.g. Campus Director / Extension Head">
                    </div>

                    <!-- 5. Layout & Order -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Column Position (1 - 3)</label>
                            <select name="position_column" class="form-select form-select-sm">
                                <option value="1">Column 1 (Left)</option>
                                <option value="2" selected>Column 2 (Center/Right)</option>
                                <option value="3">Column 3 (Right)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Sequence Order</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $matrices->count() + 1 }}" min="1">
                        </div>
                    </div>

                    <!-- 6. Digital Signature Requirements -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_digital_signature" value="1" id="digitalSigCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="digitalSigCheck">
                            Requires Digital Signature &amp; Cryptographic Hash
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="mandatoryCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="mandatoryCheck">
                            Mandatory Signatory (Required before final approval)
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Save Signatory Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    $(document).ready(function () {
        // 1. Server-Side Select2 for Document Process Types
        $('#docTypeSelect2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search document classification (e.g. Extension Plan, Purchase Request, Travel Order)...',
            allowClear: false,
            ajax: {
                url: '{{ route("dss.select2.doc_types") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: (data.results || []).map(function (item) {
                            return {
                                id: item.id,
                                text: item.name + (item.code ? ' (' + item.code + ')' : '')
                            };
                        }),
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            const selectedDocTypeId = e.params.data.id;
            const campusId = '{{ $selectedCampusId ?? "" }}';
            let targetUrl = '{{ route("dss.settings.signatories.index") }}?doc_type_id=' + selectedDocTypeId;
            if (campusId) {
                targetUrl += '&campus_id=' + campusId;
            }
            window.location.href = targetUrl;
        });

        // 2. Server-Side Select2 for Employee Search in Modal
        $('#employeeSelect').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addSignatoryModal'),
            width: '100%',
            placeholder: 'Search employee by name, designation, or office...',
            allowClear: true,
            ajax: {
                url: '{{ route("dss.select2.employees") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1,
                        campus_id: '{{ $selectedCampusId ?? "" }}'
                    };
                },
                processResults: function (data) {
                    return {
                        results: (data.results || []).map(function (emp) {
                            return {
                                id: emp.id,
                                text: emp.text || emp.full_name,
                                name: emp.full_name || emp.name,
                                designation: emp.designation
                            };
                        }),
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            const data = e.params.data;
            if (data && data.designation) {
                $('#designationInput').val(data.designation);
            }
        });
    });

    function toggleAssigneeFields() {
        const type = document.getElementById('assignmentTypeSelect').value;
        const employeeWrap = document.getElementById('employeeSelectWrap');
        const manualWrap = document.getElementById('manualSignatoryWrap');

        if (type === 'fixed_employee') {
            if (employeeWrap) employeeWrap.style.display = 'block';
            if (manualWrap) manualWrap.style.display = 'none';
        } else if (type === 'manual_signatory') {
            if (employeeWrap) employeeWrap.style.display = 'none';
            if (manualWrap) manualWrap.style.display = 'block';
        } else {
            if (employeeWrap) employeeWrap.style.display = 'none';
            if (manualWrap) manualWrap.style.display = 'none';
        }
    }

    function syncTierToDisplayLabel(tier) {
        const labelMap = {
            'prepared': 'Prepared by:',
            'reviewed': 'Reviewed by:',
            'noted': 'Noted by:',
            'recommending_approval': 'Recommending Approval:',
            'approved': 'Approved by:',
            'certified': 'Certified Correct:',
            'custom': ''
        };
        const input = document.getElementById('roleLabelInput');
        if (input && labelMap[tier] !== undefined) {
            input.value = labelMap[tier];
            input.focus();
        }
    }

    function setQuickLabel(tier, label) {
        const tierSelect = document.getElementById('tierRoleSelect');
        const input = document.getElementById('roleLabelInput');
        if (tierSelect) {
            tierSelect.value = tier;
        }
        if (input) {
            input.value = label;
            input.focus();
        }
    }
</script>
@endpush
@endsection
