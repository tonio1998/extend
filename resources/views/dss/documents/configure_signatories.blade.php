@extends('layouts.dss.app')

@section('title', 'Configure Signatories — ' . ($meta['code'] ?? 'Document'))

@push('styles')
<style>
    .signatory-card {
        transition: all 0.2s ease-in-out;
        border: 2px solid #475569 !important;
        background: #ffffff;
        border-radius: 14px !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
        margin-bottom: 1.5rem !important;
        overflow: hidden;
    }
    .signatory-card:hover,
    .signatory-card:focus-within {
        border-color: #0284c7 !important;
        box-shadow: 0 6px 20px rgba(2, 132, 199, 0.15) !important;
    }
    .signatory-card-header {
        background-color: #f1f5f9;
        border-bottom: 2px solid #475569 !important;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease-in-out;
    }
    .signatory-card:hover .signatory-card-header,
    .signatory-card:focus-within .signatory-card-header {
        background-color: #f0f9ff;
        border-bottom-color: #0284c7 !important;
    }
    .drag-handle {
        cursor: grab;
        user-select: none;
        padding: 3px 6px;
        border-radius: 6px;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        transition: all 0.15s ease;
    }
    .drag-handle:hover {
        background: #e2e8f0;
        color: #0f172a !important;
        border-color: #475569;
    }
    .drag-handle:active {
        cursor: grabbing !important;
        background: #cbd5e1;
    }
    .sortable-ghost {
        opacity: 0.45 !important;
        background: #f0f9ff !important;
        border: 2px dashed #0284c7 !important;
    }
    .sortable-chosen {
        cursor: grabbing !important;
        background: #f8fafc !important;
    }
    .sortable-drag {
        box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18) !important;
        transform: scale(1.01);
    }
    .timeline-node {
        position: relative;
        padding-left: 28px;
    }
    .timeline-node::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 24px;
        bottom: -12px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-node:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        font-family: monospace;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px !important;
        color: #1e293b !important;
        font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    {{-- ==================================================================== --}}
    {{-- TOP ACTION TOOLBAR & BREADCRUMBS                                     --}}
    {{-- ==================================================================== --}}
    <div class="card border-0 shadow-2xs rounded-4 bg-white mb-4 p-3.5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb extra-small font-mono m-0 text-muted">
                        <li class="breadcrumb-item"><a href="{{ route('dss.documents.view', ['type' => $meta['type_key'], 'id' => $document->getKey()]) }}" class="text-decoration-none text-muted">{{ $meta['type_label'] }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $meta['back_url'] }}" class="text-decoration-none text-muted">{{ $meta['code'] }}</a></li>
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Configure Signatories</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-inline-flex align-items-center justify-content-center p-2 rounded-3 bg-primary-subtle text-primary">
                        <i class="mdi mdi-account-cog-outline fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0 fs-5">Configure {{ $meta['type_label'] }} Signatories</h4>
                        <p class="text-muted extra-small mb-0">Modify and customize the sequential approval workflow and designated officers for this specific document.</p>
                    </div>
                </div>
            </div>

            <!-- Top Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ $meta['back_url'] }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Back to {{ $meta['type_label'] }}</span>
                </a>

                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1 shadow-2xs" id="btnTopSuggest">
                    <i class="mdi mdi-auto-fix"></i>
                    <span>Suggest Official Sequence</span>
                </button>

                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 fw-bold d-flex align-items-center gap-1.5 shadow-xs" id="btnTopSave">
                    <i class="mdi mdi-check-circle"></i>
                    <span>Save Signatories Workflow</span>
                </button>
            </div>
        </div>
    </div>

    @php
        $sigCollection = collect($signatories ?? []);
        $prepSig = $sigCollection->firstWhere('tier_role', 'prepared');
        $approvalSigs = $sigCollection->filter(fn($s) => ($s->tier_role ?? '') !== 'prepared')->values();
        $docLeaderName = $meta['leader_name'] ?: ($prepSig?->signatory_name ?: 'Project Leader');
        $docLeaderTitle = $meta['leader_title'] ?: ($prepSig?->designation ?: 'Project Leader');
        $docLeaderEmpId = $meta['leader_employee_id'] ?: ($prepSig?->employee_id ?? null);
    @endphp

    {{-- ==================================================================== --}}
    {{-- MAIN 2-COLUMN BENTO GRID                                             --}}
    {{-- ==================================================================== --}}
    <div class="row g-4">

        {{-- ---------------------------------------------------------------- --}}
        {{-- LEFT COLUMN: DOCUMENT CONTEXT & LIVE HIERARCHY TIMELINE          --}}
        {{-- ---------------------------------------------------------------- --}}
        <div class="col-lg-4 col-xl-3.5">
            <div class="vstack gap-3 sticky-top" style="top: 85px; z-index: 10;">

                <!-- Document Context Bento Card -->
                <div class="card border-0 shadow-2xs rounded-4 bg-white p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-dark text-white font-mono extra-small">{{ $meta['code'] }}</span>
                        <span class="badge {{ strtolower($meta['status']) === 'approved' ? 'bg-success' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} font-mono extra-small text-uppercase">
                            {{ $meta['status'] }}
                        </span>
                    </div>

                    <h6 class="fw-bold text-dark fs-6 mb-2 line-clamp-2" title="{{ $meta['title'] }}">{{ $meta['title'] }}</h6>

                    <div class="extra-small text-muted mb-3">
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            <i class="mdi mdi-domain text-secondary"></i>
                            <span class="text-truncate">{{ $meta['unit'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            <i class="mdi mdi-account-tie-outline text-secondary"></i>
                            <span>{{ $docLeaderName }} ({{ $docLeaderTitle }})</span>
                        </div>
                        @if(!empty($meta['budget']))
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="mdi mdi-cash-multiple text-success"></i>
                                <span class="fw-bold text-dark font-mono">{{ $meta['budget'] }}</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($meta['parent_ref']) && !empty($meta['parent_url']))
                        <div class="p-2.5 rounded-3 bg-light border extra-small">
                            <span class="text-muted d-block mb-1">Parent Hierarchy Reference:</span>
                            <a href="{{ $meta['parent_url'] }}" class="fw-semibold text-primary text-decoration-none font-mono">
                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $meta['parent_ref'] }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Live Signatory Sequence Visualizer -->
                <div class="card border-0 shadow-2xs rounded-4 bg-white p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark extra-small text-uppercase m-0">Live Signatory Chain</h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small" id="timelineCountBadge">
                            {{ $approvalSigs->count() + 1 }} Steps
                        </span>
                    </div>

                    <div class="vstack gap-2" id="liveTimelineContainer">
                        <!-- Step 0: Prepared by -->
                        <div class="timeline-node">
                            <div class="timeline-dot bg-secondary-subtle text-secondary border">0</div>
                            <div class="extra-small fw-bold text-dark">{{ $docLeaderName }}</div>
                            <div class="extra-small text-muted">Prepared by &bull; Proponent</div>
                        </div>

                        <!-- Dynamic Steps 1..N generated via JS from repeater rows -->
                    </div>
                </div>

                <!-- Centralized Institutional Workflow Note -->
                <div class="card border-0 shadow-2xs rounded-4 bg-info-subtle border-info-subtle p-3 extra-small text-info-emphasis">
                    <div class="d-flex align-items-start gap-2">
                        <i class="mdi mdi-information-outline fs-5 text-info mt-0.5"></i>
                        <div>
                            <div class="fw-bold mb-1">Document Signatory Customizer:</div>
                            <p class="m-0 mb-1">You are configuring signatories specifically for this <strong>{{ $meta['type_label'] }} [{{ $meta['code'] }}]</strong>.</p>
                            <span class="text-muted">Use <em>Suggest Official Sequence</em> to auto-fill the standard university approval chain, or adjust any tier as personnel changes.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ---------------------------------------------------------------- --}}
        {{-- RIGHT COLUMN: DEDICATED SIGNATORIES WORKFLOW REPEATER            --}}
        {{-- ---------------------------------------------------------------- --}}
        <div class="col-lg-8 col-xl-8.5">
            <form id="configureSignatoriesForm" onsubmit="return false;">
                @csrf

                {{-- TIER 0: PREPARED BY (PINNED PROPONENT) --}}
                <div class="card shadow-2xs rounded-4 mb-4 overflow-hidden" style="border: 2px solid #059669 !important;">
                    <div class="bg-success-subtle px-3.5 py-2.5 d-flex align-items-center justify-content-between" style="border-bottom: 2px solid #059669 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success text-white font-mono extra-small rounded-pill px-2.5 py-1">Tier 0 &bull; Proponent</span>
                            <span class="fw-bold text-dark extra-small text-uppercase">Prepared by</span>
                        </div>
                        <span class="text-success extra-small fw-medium d-flex align-items-center gap-1">
                            <i class="mdi mdi-pin"></i> Fixed Document Proponent
                        </span>
                    </div>

                    <div class="card-body p-3.5 bg-white">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label extra-small text-muted mb-1 fw-semibold">Proponent Full Name</label>
                                <div class="fw-bold text-dark fs-6">{{ $docLeaderName }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label extra-small text-muted mb-1 fw-semibold">Designation / Role</label>
                                <div class="text-dark extra-small fw-medium">{{ $docLeaderTitle }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label extra-small text-muted mb-1 fw-semibold">Proponent Unit</label>
                                <div class="text-muted extra-small">{{ $meta['unit'] }}</div>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Tier 0 -->
                        <input type="hidden" name="signatories[0][id]" value="{{ $prepSig->id ?? '' }}">
                        <input type="hidden" name="signatories[0][role_label]" value="Prepared by:">
                        <input type="hidden" name="signatories[0][tier_role]" value="prepared">
                        <input type="hidden" name="signatories[0][employee_id]" value="{{ $docLeaderEmpId }}">
                        <input type="hidden" name="signatories[0][name]" value="{{ $docLeaderName }}">
                        <input type="hidden" name="signatories[0][designation]" value="{{ $docLeaderTitle }}">
                        <input type="hidden" name="signatories[0][office]" value="{{ $meta['unit'] }}">
                        <input type="hidden" name="signatories[0][alignment]" value="left">
                        <input type="hidden" name="signatories[0][position_column]" value="1">
                    </div>
                </div>

                {{-- REPEATER HEADER & CONTROLS --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="fw-bold text-dark fs-6 m-0">Sequential Approval Chain</h6>
                        <span class="badge bg-white text-muted border font-mono extra-small" id="signatoriesCountBadge">
                            {{ $approvalSigs->count() }} {{ Str::plural('Signatory', $approvalSigs->count()) }}
                        </span>
                        <span class="badge bg-light text-secondary border font-sans extra-small fw-normal d-inline-flex align-items-center gap-1">
                            <i class="mdi mdi-cursor-move text-primary"></i>
                            <span>Drag handle (⠿) to reorder</span>
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1 shadow-2xs" id="btnSuggestSignatories">
                            <i class="mdi mdi-auto-fix"></i>
                            <span>Suggest Official Sequence</span>
                        </button>

                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-bold d-flex align-items-center gap-1.5 shadow-2xs" id="btnAddSignatoryRow">
                            <i class="mdi mdi-account-plus-outline"></i>
                            <span>Add Signatory</span>
                        </button>
                    </div>
                </div>

                {{-- DYNAMIC REPEATER LIST WITH VISIBLE BORDERS & DRAG-AND-DROP --}}
                <div id="signatoriesRepeaterList" class="vstack mb-2">
                    @foreach($approvalSigs as $idx => $sig)
                        @php
                            $stepNum = $idx + 1;
                            $arrayIdx = $idx + 1;
                            $sigEmp = $sig->employee ?? null;
                        @endphp
                        <div class="signatory-card card rounded-4 signatory-row" data-index="{{ $arrayIdx }}">
                            <div class="signatory-card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="drag-handle" title="Click & Drag to reorder sequence">
                                        <i class="mdi mdi-drag-vertical fs-5"></i>
                                    </span>
                                    <span class="badge bg-primary text-white font-mono rounded-pill px-2.5 py-1 extra-small step-badge">Tier {{ $stepNum }}</span>
                                    <span class="extra-small text-dark tier-summary-text fw-semibold">
                                        {{ $sig->role_label ? rtrim($sig->role_label, ':') : 'Signatory' }}: {{ $sig->signatory_name ?: 'Not Assigned' }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-up" title="Move Up in sequence" {{ $idx === 0 ? 'disabled' : '' }}>
                                        <i class="mdi mdi-arrow-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-down" title="Move Down in sequence" {{ $idx === $approvalSigs->count() - 1 ? 'disabled' : '' }}>
                                        <i class="mdi mdi-arrow-down"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 btn-remove-signatory" title="Remove this signatory tier">
                                        <i class="mdi mdi-trash-can-outline me-0.5"></i> Remove
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-3.5 bg-white">
                                <input type="hidden" name="signatories[{{ $arrayIdx }}][id]" class="sig-id" value="{{ $sig->id ?? '' }}">
                                <input type="hidden" name="signatories[{{ $arrayIdx }}][tier_role]" class="sig-tier-role" value="{{ $sig->tier_role ?? 'reviewed' }}">

                                <!-- Form Fields: Row 1 -->
                                <div class="row g-2.5 align-items-center mb-2.5">
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Role / Action Label</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="signatories[{{ $arrayIdx }}][role_label]" class="form-control form-control-sm font-sans fw-bold sig-role-label" value="{{ $sig->role_label ?? '' }}" placeholder="e.g. Reviewed by: (or blank)">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Preset Actions"></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm extra-small">
                                                <li><h6 class="dropdown-header text-uppercase extra-small text-primary fw-bold">Collegiate Levels</h6></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Reviewed by:" data-tier="reviewed" data-align="left" data-col="2">Reviewed by: (Extension Head)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Noted by:" data-tier="noted" data-align="left" data-col="3">Noted by: (College Dean)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Concurred by:" data-tier="concurred" data-align="left" data-col="4">Concurred by: (Extension Director)</a></li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li><h6 class="dropdown-header text-uppercase extra-small text-success fw-bold">Institutional Levels (Official Matrix)</h6></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommending_approval" data-align="center" data-col="2">Recommending Approval: (VP for RIE)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of allotment:" data-tier="certified_allotment" data-align="left" data-col="1">Certified as to allotment: (Budget Officer) [Left]</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of funds:" data-tier="certified_funds" data-align="right" data-col="2">Certified as to funds: (Accountant) [Right]</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="" data-tier="cao_designate" data-align="center" data-col="2">CAO-Designate (No header)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Approved by:" data-tier="approved" data-align="center" data-col="2">Approved by: (University President)</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Select Employee from Directory <span class="text-danger">*</span></label>
                                        <select name="signatories[{{ $arrayIdx }}][employee_id]" class="form-select form-select-sm signatory-employee-select2" data-placeholder="Search employee by name, title, or office..." required>
                                            @if(!empty($sig->employee_id) && $sigEmp)
                                                <option value="{{ $sig->employee_id }}" selected>{{ $sigEmp->full_name }} ({{ $sigEmp->designation }})</option>
                                            @elseif(!empty($sig->signatory_name))
                                                <option value="{{ $sig->employee_id ?? '' }}" selected>{{ $sig->signatory_name }}{{ $sig->designation ? ' ('.$sig->designation.')' : '' }}</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Layout Alignment</label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <select name="signatories[{{ $arrayIdx }}][alignment]" class="form-select form-select-sm sig-alignment">
                                                    <option value="left" {{ ($sig->alignment ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ ($sig->alignment ?? '') === 'center' ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ ($sig->alignment ?? '') === 'right' ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <select name="signatories[{{ $arrayIdx }}][position_column]" class="form-select form-select-sm sig-col">
                                                    <option value="1" {{ ($sig->position_column ?? 1) == 1 ? 'selected' : '' }}>Col 1 (Left)</option>
                                                    <option value="2" {{ ($sig->position_column ?? 1) == 2 ? 'selected' : '' }}>Col 2 (Right/Center)</option>
                                                    <option value="3" {{ ($sig->position_column ?? 1) == 3 ? 'selected' : '' }}>Col 3</option>
                                                    <option value="4" {{ ($sig->position_column ?? 1) == 4 ? 'selected' : '' }}>Col 4</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Fields: Row 2 (Name, Designation, Office) -->
                                <div class="row g-2.5">
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Signatory Full Name</label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][name]" class="form-control form-control-sm font-sans fw-semibold sig-name" value="{{ $sig->signatory_name ?? '' }}" placeholder="Signatory Full Name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Designation / Official Title</label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][designation]" class="form-control form-control-sm font-sans sig-designation" value="{{ $sig->designation ?? '' }}" placeholder="Designation / Official Title">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Office / Department</label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][office]" class="form-control form-control-sm font-sans sig-office" value="{{ $sig->office ?? '' }}" placeholder="Office / Department">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- CLEAN DOCKED FORM FOOTER (NO OVERLAPPING STICKY BAR) --}}
                <div class="card border rounded-4 bg-white p-3.5 shadow-2xs mt-3" style="border: 1.5px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <a href="{{ $meta['back_url'] }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold">
                            <i class="mdi mdi-arrow-left me-1"></i> Cancel &amp; Back
                        </a>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold" id="btnBottomSuggest">
                                <i class="mdi mdi-auto-fix me-1"></i> Suggest Official Sequence
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-1.5 fw-bold" id="btnBottomAdd">
                                <i class="mdi mdi-account-plus-outline me-1"></i> Add Signatory
                            </button>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 py-1.5 fw-bold shadow-xs" id="btnSaveSignatoriesWorkflow">
                                <i class="mdi mdi-check-circle me-1"></i> Save Signatories Workflow
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>

{{-- ==================================================================== --}}
{{-- TEMPLATE ROW FOR ADDING SIGNATORIES                                  --}}
{{-- ==================================================================== --}}
<template id="signatoryRowTemplate">
    <div class="signatory-card card rounded-4 signatory-row" data-index="__INDEX__">
        <div class="signatory-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="drag-handle" title="Click & Drag to reorder sequence">
                    <i class="mdi mdi-drag-vertical fs-5"></i>
                </span>
                <span class="badge bg-primary text-white font-mono rounded-pill px-2.5 py-1 extra-small step-badge">Tier __STEP__</span>
                <span class="extra-small text-dark tier-summary-text fw-semibold">
                    Reviewed by: Not Assigned
                </span>
            </div>

            <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-up" title="Move Up">
                    <i class="mdi mdi-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-down" title="Move Down">
                    <i class="mdi mdi-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-1 btn-remove-signatory" title="Remove this signatory">
                    <i class="mdi mdi-trash-can-outline me-0.5"></i> Remove
                </button>
            </div>
        </div>

        <div class="card-body p-3.5 bg-white">
            <input type="hidden" name="signatories[__INDEX__][id]" class="sig-id" value="">
            <input type="hidden" name="signatories[__INDEX__][tier_role]" class="sig-tier-role" value="reviewed">

            <!-- Form Fields: Row 1 -->
            <div class="row g-2.5 align-items-center mb-2.5">
                <div class="col-md-4">
                    <label class="form-label extra-small text-muted mb-1 fw-semibold">Role / Action Label</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="signatories[__INDEX__][role_label]" class="form-control form-control-sm font-sans fw-bold sig-role-label" value="Reviewed by:" placeholder="e.g. Reviewed by:">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Preset Actions"></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm extra-small">
                            <li><h6 class="dropdown-header text-uppercase extra-small text-primary fw-bold">Collegiate Levels</h6></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Reviewed by:" data-tier="reviewed" data-align="left" data-col="2">Reviewed by: (Extension Head)</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Noted by:" data-tier="noted" data-align="left" data-col="3">Noted by: (College Dean)</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Concurred by:" data-tier="concurred" data-align="left" data-col="4">Concurred by: (Extension Director)</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><h6 class="dropdown-header text-uppercase extra-small text-success fw-bold">Institutional Levels</h6></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommending_approval" data-align="center" data-col="2">Recommending Approval: (VP for RIE)</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of allotment:" data-tier="certified_allotment" data-align="left" data-col="1">Certified as to allotment: (Budget Officer) [Left]</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of funds:" data-tier="certified_funds" data-align="right" data-col="2">Certified as to funds: (Accountant) [Right]</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="" data-tier="cao_designate" data-align="center" data-col="2">CAO-Designate (No header)</a></li>
                            <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Approved by:" data-tier="approved" data-align="center" data-col="2">Approved by: (University President)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label extra-small text-muted mb-1 fw-semibold">Select Employee from Directory <span class="text-danger">*</span></label>
                    <select name="signatories[__INDEX__][employee_id]" class="form-select form-select-sm signatory-employee-select2" data-placeholder="Search employee by name, title, or office..." required>
                        <option value="">Search employee by name, title, or office...</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label extra-small text-muted mb-1 fw-semibold">Layout Alignment</label>
                    <div class="row g-1">
                        <div class="col-6">
                            <select name="signatories[__INDEX__][alignment]" class="form-select form-select-sm sig-alignment">
                                <option value="left" selected>Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="signatories[__INDEX__][position_column]" class="form-select form-select-sm sig-col">
                                <option value="1" selected>Col 1</option>
                                <option value="2">Col 2</option>
                                <option value="3">Col 3</option>
                                <option value="4">Col 4</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Fields: Row 2 -->
            <div class="row g-2.5">
                <div class="col-md-4">
                    <label class="form-label extra-small text-muted mb-1">Signatory Full Name</label>
                    <input type="text" name="signatories[__INDEX__][name]" class="form-control form-control-sm font-sans fw-semibold sig-name" value="" placeholder="Signatory Full Name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small text-muted mb-1">Designation / Official Title</label>
                    <input type="text" name="signatories[__INDEX__][designation]" class="form-control form-control-sm font-sans sig-designation" value="" placeholder="Designation / Official Title">
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small text-muted mb-1">Office / Department</label>
                    <input type="text" name="signatories[__INDEX__][office]" class="form-control form-control-sm font-sans sig-office" value="" placeholder="Office / Department">
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script src="{{ asset('assets/libs/sortable.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        let sortableInstance = null;
        function initSortable() {
            const el = document.getElementById('signatoriesRepeaterList');
            if (!el || typeof Sortable === 'undefined') return;
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            sortableInstance = new Sortable(el, {
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function () {
                    reindexSignatoryRows();
                }
            });
        }

        function initSelect2($select) {
            if (!$select.length || !$.fn.select2) return;
            $select.select2({
                width: '100%',
                allowClear: true,
                placeholder: $select.data('placeholder') || 'Search employee...',
                ajax: {
                    url: "{{ route('dss.select2.employees') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        const items = (data.results || data.data || []).map(emp => ({
                            id: emp.id,
                            text: emp.text || `${emp.full_name || emp.name} (${emp.designation || 'Institutional Officer'})`,
                            full_name: emp.full_name || emp.name,
                            designation: emp.designation || '',
                            department: emp.department?.unit_name || emp.department_name || emp.office || ''
                        }));
                        return {
                            results: items,
                            pagination: {
                                more: (data.pagination && data.pagination.more) || false
                            }
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
                const item = e.params.data;
                const $row = $(this).closest('.signatory-row');
                if (item.full_name) $row.find('.sig-name').val(item.full_name);
                if (item.designation) $row.find('.sig-designation').val(item.designation);
                if (item.department) $row.find('.sig-office').val(item.department);
                updateCardSummary($row);
                updateTimelinePreview();
            });
        }

        function updateCardSummary($row) {
            const roleLabel = $row.find('.sig-role-label').val() || 'Signatory';
            const name = $row.find('.sig-name').val() || 'Not Assigned';
            $row.find('.tier-summary-text').text(`${roleLabel.replace(/:$/, '')}: ${name}`);
        }

        // Live Timeline Preview
        function updateTimelinePreview() {
            const $container = $('#liveTimelineContainer');
            // Keep Step 0
            $container.find('.timeline-node:not(:first)').remove();

            const $rows = $('#signatoriesRepeaterList .signatory-row');
            $('#timelineCountBadge').text(($rows.length + 1) + ' Steps');

            $rows.each(function (idx) {
                const stepNum = idx + 1;
                const $row = $(this);
                const role = $row.find('.sig-role-label').val() || 'Signatory';
                const name = $row.find('.sig-name').val() || 'Pending Designation';
                const designation = $row.find('.sig-designation').val() || '';

                const nodeHtml = `
                    <div class="timeline-node">
                        <div class="timeline-dot bg-primary-subtle text-primary border border-primary-subtle">${stepNum}</div>
                        <div class="extra-small fw-bold text-dark">${name}</div>
                        <div class="extra-small text-muted">${role.replace(/:$/, '')}${designation ? ' &bull; ' + designation : ''}</div>
                    </div>
                `;
                $container.append(nodeHtml);
            });
        }

        // Initialize Select2 on existing rows
        $('#signatoriesRepeaterList .signatory-employee-select2').each(function () {
            initSelect2($(this));
        });

        // Initialize drag-and-drop Sortable
        initSortable();

        // Initialize timeline
        updateTimelinePreview();

        // Listen for live input changes
        $(document).on('input change', '.sig-role-label, .sig-name, .sig-designation', function () {
            const $row = $(this).closest('.signatory-row');
            updateCardSummary($row);
            updateTimelinePreview();
        });

        // Re-index rows
        function reindexSignatoryRows() {
            const $rows = $('#signatoriesRepeaterList .signatory-row');
            $('#signatoriesCountBadge').text($rows.length + ($rows.length === 1 ? ' Signatory' : ' Signatories'));

            $rows.each(function (idx) {
                const stepNum = idx + 1;
                const arrayIdx = idx + 1;
                const $row = $(this);

                $row.attr('data-index', arrayIdx);
                $row.find('.step-badge').text('Tier ' + stepNum);

                $row.find('.btn-move-signatory-up').prop('disabled', idx === 0);
                $row.find('.btn-move-signatory-down').prop('disabled', idx === $rows.length - 1);

                $row.find('.sig-id').attr('name', `signatories[${arrayIdx}][id]`);
                $row.find('.sig-tier-role').attr('name', `signatories[${arrayIdx}][tier_role]`);
                $row.find('.sig-role-label').attr('name', `signatories[${arrayIdx}][role_label]`);
                $row.find('.signatory-employee-select2').attr('name', `signatories[${arrayIdx}][employee_id]`);
                $row.find('.sig-alignment').attr('name', `signatories[${arrayIdx}][alignment]`);
                $row.find('.sig-col').attr('name', `signatories[${arrayIdx}][position_column]`);
                $row.find('.sig-name').attr('name', `signatories[${arrayIdx}][name]`);
                $row.find('.sig-designation').attr('name', `signatories[${arrayIdx}][designation]`);
                $row.find('.sig-office').attr('name', `signatories[${arrayIdx}][office]`);

                updateCardSummary($row);
            });

            updateTimelinePreview();
        }

        // Add row
        function addSignatoryRow() {
            const count = $('#signatoriesRepeaterList .signatory-row').length;
            const nextIdx = count + 1;
            const tpl = $('#signatoryRowTemplate').html();
            const rendered = tpl.replace(/__INDEX__/g, nextIdx).replace(/__STEP__/g, nextIdx);
            const $newRow = $(rendered);

            $('#signatoriesRepeaterList').append($newRow);
            initSelect2($newRow.find('.signatory-employee-select2'));
            reindexSignatoryRows();
            initSortable();

            $('html, body').animate({
                scrollTop: $newRow.offset().top - 120
            }, 300);
        }

        $('#btnAddSignatoryRow, #btnBottomAdd').on('click', addSignatoryRow);

        // Remove row
        $(document).on('click', '.btn-remove-signatory', function () {
            const $row = $(this).closest('.signatory-row');
            $row.fadeOut(200, function () {
                $row.remove();
                reindexSignatoryRows();
            });
        });

        // Move row up
        $(document).on('click', '.btn-move-signatory-up', function () {
            const $row = $(this).closest('.signatory-row');
            const $prev = $row.prev('.signatory-row');
            if ($prev.length) {
                $row.insertBefore($prev);
                reindexSignatoryRows();
            }
        });

        // Move row down
        $(document).on('click', '.btn-move-signatory-down', function () {
            const $row = $(this).closest('.signatory-row');
            const $next = $row.next('.signatory-row');
            if ($next.length) {
                $row.insertAfter($next);
                reindexSignatoryRows();
            }
        });

        // Preset action dropdown item selected
        $(document).on('click', '.preset-role-option', function (e) {
            e.preventDefault();
            const $this = $(this);
            const $row = $this.closest('.signatory-row');
            const label = $this.data('label');
            const tier = $this.data('tier');
            const align = $this.data('align');
            const col = $this.data('col');

            $row.find('.sig-role-label').val(label);
            if (tier) $row.find('.sig-tier-role').val(tier);
            if (align) $row.find('.sig-alignment').val(align);
            if (col) $row.find('.sig-col').val(col);

            updateCardSummary($row);
            updateTimelinePreview();
        });

        // Suggest official signatories matrix
        function suggestSignatories() {
            const $btns = $('#btnSuggestSignatories, #btnTopSuggest, #btnBottomSuggest');
            $btns.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Loading...');

            $.ajax({
                url: "{{ $meta['suggest_url'] }}",
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    $btns.prop('disabled', false).html('<i class="mdi mdi-auto-fix"></i> <span>Suggest Official Sequence</span>');
                    if (res && res.success && Array.isArray(res.signatories)) {
                        const approvalSigs = res.signatories.filter(s => s.tier_role !== 'prepared');
                        $('#signatoriesRepeaterList').empty();

                        approvalSigs.forEach((sig, idx) => {
                            const arrayIdx = idx + 1;
                            const tpl = $('#signatoryRowTemplate').html();
                            const rendered = tpl.replace(/__INDEX__/g, arrayIdx).replace(/__STEP__/g, arrayIdx);
                            const $newRow = $(rendered);

                            $newRow.find('.sig-role-label').val(sig.role_label ?? '');
                            $newRow.find('.sig-tier-role').val(sig.tier_role ?? 'reviewed');
                            $newRow.find('.sig-name').val(sig.name ?? '');
                            $newRow.find('.sig-designation').val(sig.designation ?? '');
                            $newRow.find('.sig-office').val(sig.office ?? '');
                            if (sig.alignment) $newRow.find('.sig-alignment').val(sig.alignment);
                            if (sig.position_column) $newRow.find('.sig-col').val(sig.position_column);

                            if (sig.employee_id) {
                                const opt = new Option(sig.name + (sig.designation ? ' (' + sig.designation + ')' : ''), sig.employee_id, true, true);
                                $newRow.find('.signatory-employee-select2').append(opt);
                            }

                            $('#signatoriesRepeaterList').append($newRow);
                            initSelect2($newRow.find('.signatory-employee-select2'));
                        });

                        reindexSignatoryRows();
                        initSortable();

                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Official Sequence Suggested',
                                text: 'The official approval sequence for this document was loaded. You can adjust, reorder, or customize any field before saving.',
                                timer: 2500,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function () {
                    $btns.prop('disabled', false).html('<i class="mdi mdi-auto-fix"></i> <span>Suggest Official Sequence</span>');
                    alert('Could not load official signatories suggestion.');
                }
            });
        }

        $('#btnSuggestSignatories, #btnTopSuggest, #btnBottomSuggest').on('click', suggestSignatories);

        // Save Signatories Workflow
        function saveWorkflow() {
            const $btns = $('#btnSaveSignatoriesWorkflow, #btnTopSave');
            $btns.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Saving Workflow...');

            const payload = {
                _token: '{{ csrf_token() }}',
                signatories: []
            };

            // Pinned proponent (Index 0)
            payload.signatories.push({
                id: $('input[name="signatories[0][id]"]').val(),
                role_label: $('input[name="signatories[0][role_label]"]').val(),
                tier_role: 'prepared',
                employee_id: $('input[name="signatories[0][employee_id]"]').val(),
                name: $('input[name="signatories[0][name]"]').val(),
                designation: $('input[name="signatories[0][designation]"]').val(),
                office: $('input[name="signatories[0][office]"]').val(),
                alignment: 'left',
                position_column: 1
            });

            // Repeater rows
            $('#signatoriesRepeaterList .signatory-row').each(function () {
                const $r = $(this);
                payload.signatories.push({
                    id: $r.find('.sig-id').val(),
                    role_label: $r.find('.sig-role-label').val(),
                    tier_role: $r.find('.sig-tier-role').val(),
                    employee_id: $r.find('.signatory-employee-select2').val(),
                    name: $r.find('.sig-name').val(),
                    designation: $r.find('.sig-designation').val(),
                    office: $r.find('.sig-office').val(),
                    alignment: $r.find('.sig-alignment').val(),
                    position_column: $r.find('.sig-col').val()
                });
            });

            $.ajax({
                url: "{{ $meta['update_url'] }}",
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function (res) {
                    $btns.prop('disabled', false).html('<i class="mdi mdi-check-circle me-1"></i> Saved!');

                    const redirectUrl = res.redirect_url || "{{ $meta['back_url'] }}";

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Signatories Workflow Saved',
                            text: res.message || 'Signatories have been updated successfully. Returning to document...',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = redirectUrl;
                        });
                    } else {
                        window.location.href = redirectUrl;
                    }
                },
                error: function (xhr) {
                    $btns.prop('disabled', false).html('<i class="mdi mdi-check-circle me-1"></i> Save Signatories Workflow');
                    const msg = xhr.responseJSON?.message || 'Error updating signatories workflow. Please check required fields.';
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Save',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        }

        $('#btnSaveSignatoriesWorkflow, #btnTopSave').on('click', saveWorkflow);
    });
</script>
@endpush
@endsection

