@extends('layouts.dss.app')

@section('title', 'Edit Extension Plan: ' . $plan->plan_code . ' — RIE System')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .date-mode-btn {
        font-size: 11px !important;
        padding: 3px 9px !important;
        font-weight: 600;
    }
    .btn-check:checked + .date-mode-btn {
        background-color: var(--dss-primary, #047857) !important;
        color: #ffffff !important;
        border-color: var(--dss-primary, #047857) !important;
    }
    .flatpickr-calendar {
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Breadcrumb & Header Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 text-muted extra-small mb-1 font-mono">
                <a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">DSS</a>
                <span>/</span>
                <span class="text-primary fw-semibold">RIE System</span>
                <span>/</span>
                <a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">Extension Plans</a>
                <span>/</span>
                <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="text-decoration-none text-muted">{{ $plan->plan_code }}</a>
                <span>/</span>
                <span class="text-dark fw-bold">Edit Details</span>
            </div>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-pencil-box text-warning"></i>
                <span>Edit Extension Plan: {{ $plan->plan_code }}</span>
                <span class="badge rounded-pill bg-light text-dark border extra-small px-2 py-1 font-mono">{{ $plan->status_badge['label'] ?? strtoupper($plan->status) }}</span>
            </h4>
            <div class="text-muted extra-small mt-1">
                Update timeline milestones, beneficiaries, proposed budget, and institutional signatories for this extension work plan.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Plan Workbench
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-2xs mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-alert-circle fs-5 flex-shrink-0 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-1">Please correct the following errors:</h6>
                    <ul class="mb-0 extra-small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('dss.rie.plans.update', $plan->getRouteKey()) }}" method="POST" id="extensionPlanEditForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="action_type" id="actionTypeInput" value="draft">

        <div class="row g-4">
            <!-- Left Column: Main Plan Inputs -->
            <div class="col-lg-8">
                <!-- 1. Institutional Document Card -->
                <div class="card border rounded-3 shadow-2xs mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 12px;">1</span>
                            <h6 class="fw-bold m-0 text-dark">Program &amp; Activity Identification</h6>
                        </div>
                        <span class="badge bg-light text-muted border font-mono extra-small">{{ $plan->plan_code }}</span>
                    </div>
                    <div class="card-body p-4">
                        <!-- Academic Program Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Academic Program <span class="text-danger">*</span>
                            </label>
                            <select name="academic_program_id" id="academicProgramSelect" class="form-select select2-ajax"
                                    data-placeholder="Search or Select Degree Program (e.g. BTVTED, BSIT)...">
                                <option value=""></option>
                                @foreach($academicPrograms as $prog)
                                    @php
                                        $campusName = $prog->campus?->campus_name ?: 'Main Campus';
                                        $label = "{$campusName} — {$prog->program_code} ({$prog->program_name})";
                                        $isSelected = old('academic_program_id', $plan->academic_program_id) == $prog->id;
                                    @endphp
                                    <option value="{{ $prog->id }}"
                                            data-program-name="{{ $label }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="program_name" id="programNameInput" value="{{ old('program_name', $plan->program_name) }}">
                            <div class="form-text extra-small text-muted mt-1">
                                Exact Program format on document: <span class="fw-semibold text-dark" id="programDisplayPreview">{{ old('program_name', $plan->program_name) }}</span>
                            </div>
                        </div>

                        <!-- 3rd-Party Tracking Number (Optional) -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase mb-0">
                                    <i class="mdi mdi-barcode-scan text-primary me-1"></i> 3rd-Party DTS Tracking Number <span class="badge bg-light text-muted border font-mono ms-1" style="font-size: 10px;">Optional</span>
                                </label>
                                <span class="extra-small text-muted">Connected to 3rd party DTS</span>
                            </div>
                            <input type="text" name="tracking_number" class="form-control font-mono"
                                   placeholder="e.g. SNSU-2026-00123 (or leave blank to link later)"
                                   value="{{ old('tracking_number', $plan->tracking_number) }}">
                            <div class="form-text extra-small text-muted">
                                Tracking number from 3rd-party tracking system. You can update this anytime before official PDF printing.
                            </div>
                        </div>

                        <!-- Extension Activities / Project Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Extension Activities (Project Title) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="extension_activity" class="form-control fw-bold text-dark fs-6"
                                   placeholder="e.g. PROJECT KAON"
                                   value="{{ old('extension_activity', $plan->extension_activity) }}" required>
                            <div class="form-text extra-small text-muted">
                                Name of the extension initiative, project banner, or community service program.
                            </div>
                        </div>

                        <!-- Objectives -->
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Objectives <span class="text-danger">*</span>
                            </label>
                            <textarea name="objectives" rows="4" class="form-control"
                                      placeholder="State the specific objectives of the extension project..." required>{{ old('objectives', $plan->objectives) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Schedule of Activities Timeline Card -->
                <div class="card border rounded-3 shadow-2xs mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 12px;">2</span>
                            <h6 class="fw-bold m-0 text-dark">Schedule of Activities (Milestones)</h6>
                        </div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small">Institutional Calendar</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Submission of Proposal -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    Submission of Extension Project Proposal
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-calendar"></i></span>
                                    <input type="date" name="sub_extension_proposal_date" class="form-control"
                                           value="{{ old('sub_extension_proposal_date', $plan->sub_extension_proposal_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- Submission of Activity Design -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    Submission of Activity Design
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-calendar"></i></span>
                                    <input type="date" name="sub_activity_design_date" class="form-control"
                                           value="{{ old('sub_activity_design_date', $plan->sub_activity_design_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- Submission of Purchase Request (Optional) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small d-flex justify-content-between">
                                    <span>Submission of Purchase Request</span>
                                    <span class="text-muted font-italic">(Optional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-cart-outline"></i></span>
                                    <input type="date" name="sub_purchase_request_date" class="form-control"
                                           value="{{ old('sub_purchase_request_date', $plan->sub_purchase_request_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- MOA Signing -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    MOA Signing
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-pen"></i></span>
                                    <input type="date" name="moa_signing_date" class="form-control"
                                           value="{{ old('moa_signing_date', $plan->moa_signing_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- Arrival of Materials -->
                            @php
                                $matMode = old('materials_arrival_mode', $plan->materials_arrival_mode ?: 'range');
                                $matDatesArr = old('materials_arrival_dates', $plan->materials_arrival_dates);
                                if (is_array($matDatesArr)) {
                                    $matDatesStr = implode(', ', $matDatesArr);
                                } else {
                                    $matDatesStr = (string) $matDatesArr;
                                }
                            @endphp
                            <div class="col-12">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase mb-0 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-truck-delivery-outline text-warning"></i>
                                            <span>Arrival of Materials</span>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1 extra-small">RIE Calendar Event</span>
                                        </label>

                                        <!-- Mode Switcher -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeRange" value="range" {{ $matMode === 'range' ? 'checked' : '' }} onchange="toggleDateMode('materials', 'range')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeRange"><i class="mdi mdi-calendar-range me-1"></i>Date Range</label>

                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeMultiple" value="multiple" {{ $matMode === 'multiple' ? 'checked' : '' }} onchange="toggleDateMode('materials', 'multiple')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeMultiple"><i class="mdi mdi-calendar-multiselect me-1"></i>Selected Dates</label>

                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeSingle" value="single" {{ $matMode === 'single' ? 'checked' : '' }} onchange="toggleDateMode('materials', 'single')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeSingle"><i class="mdi mdi-calendar me-1"></i>Single Date</label>
                                        </div>
                                    </div>

                                    <!-- Range Container -->
                                    <div id="materialsRangeContainer" class="row g-2 align-items-center {{ $matMode === 'range' ? '' : 'd-none' }}">
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">Start Date (Arrival Begins):</label>
                                            <input type="date" name="materials_arrival_start" id="materialsArrivalStart" class="form-control form-control-sm"
                                                   value="{{ old('materials_arrival_start', $plan->materials_arrival_start?->format('Y-m-d')) }}" onchange="updateDatePreview('materials')">
                                        </div>
                                        <div class="col-md-2 text-center pt-md-3">
                                            <span class="text-muted extra-small fw-bold">to</span>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">End Date (All Materials Arrived):</label>
                                            <input type="date" name="materials_arrival_end" id="materialsArrivalEnd" class="form-control form-control-sm"
                                                   value="{{ old('materials_arrival_end', $plan->materials_arrival_end?->format('Y-m-d')) }}" onchange="updateDatePreview('materials')">
                                        </div>
                                    </div>

                                    <!-- Multiple / Selected Dates Container -->
                                    <div id="materialsMultipleContainer" class="{{ $matMode === 'multiple' ? '' : 'd-none' }}">
                                        <label class="extra-small text-muted mb-1">Click to select specific delivery dates on calendar:</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white"><i class="mdi mdi-calendar-multiselect text-primary"></i></span>
                                            <input type="text" id="materialsMultiPicker" class="form-control form-control-sm bg-white" placeholder="Click to select multiple delivery dates..." readonly value="{{ $matDatesStr }}">
                                        </div>
                                        <input type="hidden" name="materials_arrival_dates" id="materialsDatesHidden" value="{{ $matDatesStr }}">
                                    </div>

                                    <!-- Single Date Container -->
                                    <div id="materialsSingleContainer" class="{{ $matMode === 'single' ? '' : 'd-none' }}">
                                        <label class="extra-small text-muted mb-1">Target Arrival Date:</label>
                                        <input type="date" id="materialsSingleDate" class="form-control form-control-sm"
                                               value="{{ old('materials_arrival_start', $plan->materials_arrival_start?->format('Y-m-d')) }}"
                                               onchange="syncSingleToRange('materials', this.value)">
                                    </div>

                                    <!-- Computed Format & Calendar Badge -->
                                    <input type="hidden" name="materials_arrival_date" id="materialsDateFormatted" value="{{ old('materials_arrival_date', $plan->materials_arrival_date) }}">
                                    <div class="mt-2 d-flex align-items-center justify-content-between extra-small">
                                        <span class="text-muted">Document Text: <strong class="text-dark" id="materialsDisplayPreview">{{ old('materials_arrival_date', $plan->materials_arrival_date ?: 'Not specified') }}</strong></span>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border" id="materialsCalendarBadge"><i class="mdi mdi-calendar-clock me-1"></i>RIE Calendar Synced</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Conduct of Extension Activity -->
                            @php
                                $condMode = old('conduct_activity_mode', $plan->conduct_activity_mode ?: 'range');
                                $condDatesArr = old('conduct_activity_dates', $plan->conduct_activity_dates);
                                if (is_array($condDatesArr)) {
                                    $condDatesStr = implode(', ', $condDatesArr);
                                } else {
                                    $condDatesStr = (string) $condDatesArr;
                                }
                            @endphp
                            <div class="col-12">
                                <div class="p-3 border rounded-3 bg-light-subtle">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase mb-0 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-account-group text-success"></i>
                                            <span>Conduct of Extension Activity</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 extra-small">Primary RIE Event</span>
                                        </label>

                                        <!-- Mode Switcher -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeRange" value="range" {{ $condMode === 'range' ? 'checked' : '' }} onchange="toggleDateMode('conduct', 'range')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeRange"><i class="mdi mdi-calendar-range me-1"></i>Date Range</label>

                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeMultiple" value="multiple" {{ $condMode === 'multiple' ? 'checked' : '' }} onchange="toggleDateMode('conduct', 'multiple')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeMultiple"><i class="mdi mdi-calendar-multiselect me-1"></i>Selected Dates</label>

                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeSingle" value="single" {{ $condMode === 'single' ? 'checked' : '' }} onchange="toggleDateMode('conduct', 'single')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeSingle"><i class="mdi mdi-calendar me-1"></i>Single Date</label>
                                        </div>
                                    </div>

                                    <!-- Range Container -->
                                    <div id="conductRangeContainer" class="row g-2 align-items-center {{ $condMode === 'range' ? '' : 'd-none' }}">
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">Start Date (Activity Launch):</label>
                                            <input type="date" name="conduct_activity_start" id="conductActivityStart" class="form-control form-control-sm"
                                                   value="{{ old('conduct_activity_start', $plan->conduct_activity_start?->format('Y-m-d')) }}" onchange="updateDatePreview('conduct')">
                                        </div>
                                        <div class="col-md-2 text-center pt-md-3">
                                            <span class="text-muted extra-small fw-bold">to</span>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">End Date (Activity Culmination):</label>
                                            <input type="date" name="conduct_activity_end" id="conductActivityEnd" class="form-control form-control-sm"
                                                   value="{{ old('conduct_activity_end', $plan->conduct_activity_end?->format('Y-m-d')) }}" onchange="updateDatePreview('conduct')">
                                        </div>
                                    </div>

                                    <!-- Multiple Container -->
                                    <div id="conductMultipleContainer" class="{{ $condMode === 'multiple' ? '' : 'd-none' }}">
                                        <label class="extra-small text-muted mb-1">Click to select specific extension execution dates on calendar:</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white"><i class="mdi mdi-calendar-multiselect text-success"></i></span>
                                            <input type="text" id="conductMultiPicker" class="form-control form-control-sm bg-white" placeholder="Click to select multiple execution dates..." readonly value="{{ $condDatesStr }}">
                                        </div>
                                        <input type="hidden" name="conduct_activity_dates" id="conductDatesHidden" value="{{ $condDatesStr }}">
                                    </div>

                                    <!-- Single Container -->
                                    <div id="conductSingleContainer" class="{{ $condMode === 'single' ? '' : 'd-none' }}">
                                        <label class="extra-small text-muted mb-1">Execution Date:</label>
                                        <input type="date" id="conductSingleDate" class="form-control form-control-sm"
                                               value="{{ old('conduct_activity_start', $plan->conduct_activity_start?->format('Y-m-d')) }}"
                                               onchange="syncSingleToRange('conduct', this.value)">
                                    </div>

                                    <!-- Computed Format & Calendar Badge -->
                                    <input type="hidden" name="conduct_activity_date" id="conductDateFormatted" value="{{ old('conduct_activity_date', $plan->conduct_activity_date) }}">
                                    <div class="mt-2 d-flex align-items-center justify-content-between extra-small">
                                        <span class="text-muted">Document Text: <strong class="text-dark" id="conductDisplayPreview">{{ old('conduct_activity_date', $plan->conduct_activity_date ?: 'Not specified') }}</strong></span>
                                        <span class="badge bg-success-subtle text-success border" id="conductCalendarBadge"><i class="mdi mdi-calendar-check me-1"></i>RIE Calendar Synced</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submission of Narrative Reports -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    Submission of Narrative Reports, Attendance &amp; Evaluation
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-file-check-outline"></i></span>
                                    <input type="date" name="sub_narrative_report_date" class="form-control"
                                           value="{{ old('sub_narrative_report_date', $plan->sub_narrative_report_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- Conduct of Monitoring/Feedback -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    Conduct of Monitoring/Feedback
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-clipboard-pulse-outline"></i></span>
                                    <input type="date" name="monitoring_feedback_date" class="form-control"
                                           value="{{ old('monitoring_feedback_date', $plan->monitoring_feedback_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Beneficiaries, Budget & Extensionists Card -->
                <div class="card border rounded-3 shadow-2xs mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 12px;">3</span>
                            <h6 class="fw-bold m-0 text-dark">Beneficiaries &amp; Budget Allocation</h6>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">Scope &amp; Impact</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Beneficiaries Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="beneficiaries" rows="2" class="form-control"
                                      placeholder="e.g. Daycare Worker and BHW of Surigao City" required>{{ old('beneficiaries', $plan->beneficiaries) }}</textarea>
                            <div class="form-text extra-small text-muted">
                                Target community sector, partner agency, LGU, or barangay stakeholders.
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Target Beneficiaries <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-multiple"></i></span>
                                    <input type="number" name="target_beneficiaries_count" class="form-control" min="1"
                                           placeholder="e.g. 50" value="{{ old('target_beneficiaries_count', $plan->target_beneficiaries_count) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Proposed Budget <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">₱</span>
                                    <input type="number" step="0.01" name="proposed_budget" class="form-control font-mono fw-bold"
                                           placeholder="e.g. 80000.00" value="{{ old('proposed_budget', $plan->proposed_budget) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Number of Extensionists <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-tie"></i></span>
                                    <input type="number" name="extensionists_count" class="form-control" min="1"
                                           placeholder="e.g. 15" value="{{ old('extensionists_count', $plan->extensionists_count) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Signatories & Actions -->
            <div class="col-lg-4">
                <!-- 4-Tier Dynamic Institutional Signatories Box -->
                <div class="card border rounded-3 shadow-2xs mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 12px;">4</span>
                                <h6 class="fw-bold m-0 text-dark">Institutional Signatories</h6>
                            </div>
                            <span class="badge bg-primary-subtle text-primary extra-small px-2 py-1" id="collegeIndicatorBadge">
                                {{ $plan->academicProgram?->department?->unit_name ?? 'Academic College' }}
                            </span>
                        </div>
                        <div class="extra-small text-muted mt-1">
                            Dynamic 4-tier signatory chain automatically resolved by Academic College / Department.
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <!-- Hidden Signatory Form Fields -->
                        <input type="hidden" name="reviewed_by_employee_id" id="inputReviewedEmpId" value="{{ $plan->reviewed_by_employee_id }}">
                        <input type="hidden" name="reviewed_by_name" id="inputReviewedName" value="{{ $plan->reviewed_by_name }}">
                        <input type="hidden" name="reviewed_by_designation" id="inputReviewedDesig" value="{{ $plan->reviewed_by_designation }}">

                        <input type="hidden" name="noted_by_employee_id" id="inputNotedEmpId" value="{{ $plan->noted_by_employee_id }}">
                        <input type="hidden" name="noted_by_name" id="inputNotedName" value="{{ $plan->noted_by_name }}">
                        <input type="hidden" name="noted_by_designation" id="inputNotedDesig" value="{{ $plan->noted_by_designation }}">

                        <input type="hidden" name="approved_by_employee_id" id="inputApprovedEmpId" value="{{ $plan->approved_by_employee_id ?: $plan->noted_by_employee_id }}">
                        <input type="hidden" name="approved_by_name" id="inputApprovedName" value="{{ $plan->approved_by_name ?: $plan->noted_by_name }}">
                        <input type="hidden" name="approved_by_designation" id="inputApprovedDesig" value="{{ $plan->approved_by_designation ?: $plan->noted_by_designation }}">

                        <input type="hidden" name="concurred_by_employee_id" id="inputConcurredEmpId" value="{{ $plan->concurred_by_employee_id }}">
                        <input type="hidden" name="concurred_by_name" id="inputConcurredName" value="{{ $plan->concurred_by_name }}">
                        <input type="hidden" name="concurred_by_designation" id="inputConcurredDesig" value="{{ $plan->concurred_by_designation }}">

                        <input type="hidden" name="concurred_by_2_employee_id" id="inputConcurred2EmpId" value="{{ $plan->concurred_by_2_employee_id }}">
                        <input type="hidden" name="concurred_by_2_name" id="inputConcurred2Name" value="{{ $plan->concurred_by_2_name }}">
                        <input type="hidden" name="concurred_by_2_designation" id="inputConcurred2Desig" value="{{ $plan->concurred_by_2_designation }}">

                        <!-- Tier 1: Prepared By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-primary text-white extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-account-star me-1"></i> 1. Prepared by
                                </span>
                                <span class="text-muted extra-small">Proponent</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate">
                                {{ $plan->prepared_by_name ?: ($plan->preparedByUser?->name ?? 'Proponent') }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;">
                                {{ $plan->prepared_by_designation ?: 'Project Leader' }}
                            </div>
                        </div>

                        <!-- Tier 2: Reviewed By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-clipboard-check me-1"></i> 2. Reviewed by
                                </span>
                                <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Dynamic</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="reviewedDisplayName">
                                {{ $plan->reviewed_by_name ?: 'College Head for Extension' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="reviewedDisplayDesig">
                                {{ $plan->reviewed_by_designation ?: 'College Head for Extension' }}
                            </div>
                        </div>

                        <!-- Tier 3: Noted By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-shield-check me-1"></i> 3. Noted by
                                </span>
                                <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Dynamic</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="notedDisplayName">
                                {{ $plan->noted_by_name ?: 'College Dean' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="notedDisplayDesig">
                                {{ $plan->noted_by_designation ?: 'College Dean' }}
                            </div>
                        </div>

                        <!-- Tier 4: Concurred By -->
                        <div class="p-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-certificate me-1"></i> 4. Concurred by
                                </span>
                                <span class="text-muted extra-small" style="font-size: 9.5px;">University</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="concurred1DisplayName">
                                {{ $plan->concurred_by_name ?: 'EVER JOY G. RABADAN, MAEM, MSc.' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="concurred1DisplayDesig">
                                {{ $plan->concurred_by_designation ?: 'University Extension Specialist V' }}
                            </div>
                            <div class="border-top my-1 pt-1">
                                <div class="fw-bold text-dark extra-small text-truncate" id="concurred2DisplayName">
                                    {{ $plan->concurred_by_2_name ?: 'ALVIE ELMA C. PLAZA, MA' }}
                                </div>
                                <div class="text-muted" style="font-size: 10.5px;" id="concurred2DisplayDesig">
                                    {{ $plan->concurred_by_2_designation ?: 'University Extension Director' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border rounded-3 shadow-2xs">
                    <div class="card-body p-3 d-flex flex-column gap-2">
                        <button type="button" class="btn btn-warning btn-lg fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm text-dark"
                                onclick="submitEditPlan('submit')">
                            <i class="mdi mdi-send-check fs-5"></i>
                            <span>Save &amp; Submit for Notation</span>
                        </button>

                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                onclick="submitEditPlan('draft')">
                            <i class="mdi mdi-content-save-outline"></i>
                            <span>Update &amp; Keep as Draft</span>
                        </button>

                        <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 mt-1">
                            <i class="mdi mdi-close"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function submitEditPlan(actionType) {
        document.getElementById('actionTypeInput').value = actionType;
        document.getElementById('extensionPlanEditForm').submit();
    }

    function toggleDateMode(type, mode) {
        var rangeCont = document.getElementById(type + 'RangeContainer');
        var multiCont = document.getElementById(type + 'MultipleContainer');
        var singleCont = document.getElementById(type + 'SingleContainer');

        rangeCont.classList.add('d-none');
        multiCont.classList.add('d-none');
        singleCont.classList.add('d-none');

        if (mode === 'range') {
            rangeCont.classList.remove('d-none');
            updateDatePreview(type);
        } else if (mode === 'multiple') {
            multiCont.classList.remove('d-none');
            updateMultiDatePreview(type);
        } else if (mode === 'single') {
            singleCont.classList.remove('d-none');
            var startVal = document.getElementById(type === 'materials' ? 'materialsArrivalStart' : 'conductActivityStart').value;
            if (startVal) {
                document.getElementById(type === 'materials' ? 'materialsSingleDate' : 'conductSingleDate').value = startVal;
                syncSingleToRange(type, startVal);
            }
        }
    }

    function syncSingleToRange(type, val) {
        if (!val) return;
        if (type === 'materials') {
            document.getElementById('materialsArrivalStart').value = val;
            document.getElementById('materialsArrivalEnd').value = val;
        } else {
            document.getElementById('conductActivityStart').value = val;
            document.getElementById('conductActivityEnd').value = val;
        }
        updateDatePreview(type);
    }

    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        try {
            var parts = dateStr.split('-');
            if (parts.length === 3) {
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                var m = months[parseInt(parts[1], 10) - 1];
                return m + ' ' + parseInt(parts[2], 10) + ', ' + parts[0];
            }
        } catch (e) {}
        return dateStr;
    }

    function updateDatePreview(type) {
        var start = document.getElementById(type === 'materials' ? 'materialsArrivalStart' : 'conductActivityStart').value;
        var end = document.getElementById(type === 'materials' ? 'materialsArrivalEnd' : 'conductActivityEnd').value;
        var preview = document.getElementById(type === 'materials' ? 'materialsDisplayPreview' : 'conductDisplayPreview');
        var hiddenInput = document.getElementById(type === 'materials' ? 'materialsDateFormatted' : 'conductDateFormatted');

        if (start && end && start !== end) {
            var sFormatted = formatDateDisplay(start);
            var eFormatted = formatDateDisplay(end);
            var text = sFormatted + ' – ' + eFormatted;
            preview.textContent = text;
            hiddenInput.value = text;
        } else if (start) {
            var text = formatDateDisplay(start);
            preview.textContent = text;
            hiddenInput.value = text;
        } else {
            preview.textContent = 'Not specified';
            hiddenInput.value = '';
        }
    }

    function updateMultiDatePreview(type) {
        var hidden = document.getElementById(type === 'materials' ? 'materialsDatesHidden' : 'conductDatesHidden');
        var preview = document.getElementById(type === 'materials' ? 'materialsDisplayPreview' : 'conductDisplayPreview');
        var hiddenInput = document.getElementById(type === 'materials' ? 'materialsDateFormatted' : 'conductDateFormatted');

        if (hidden && hidden.value) {
            preview.textContent = hidden.value;
            hiddenInput.value = hidden.value;
        } else {
            preview.textContent = 'Click calendar to select dates';
            hiddenInput.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Init Flatpickr for Multiple Date Pickers
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#materialsMultiPicker', {
                mode: 'multiple',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                conjunction: ', ',
                defaultDate: "{{ $matDatesStr }}".split(', ').filter(Boolean),
                onChange: function (selectedDates, dateStr) {
                    var formatted = selectedDates.map(function (d) {
                        return d.toISOString().split('T')[0];
                    }).join(', ');
                    document.getElementById('materialsDatesHidden').value = formatted;
                    updateMultiDatePreview('materials');
                }
            });

            flatpickr('#conductMultiPicker', {
                mode: 'multiple',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                conjunction: ', ',
                defaultDate: "{{ $condDatesStr }}".split(', ').filter(Boolean),
                onChange: function (selectedDates, dateStr) {
                    var formatted = selectedDates.map(function (d) {
                        return d.toISOString().split('T')[0];
                    }).join(', ');
                    document.getElementById('conductDatesHidden').value = formatted;
                    updateMultiDatePreview('conduct');
                }
            });
        }

        // Academic Program select2 handler
        $('#academicProgramSelect').on('change', function () {
            var selectedText = $(this).find('option:selected').data('program-name') || $(this).find('option:selected').text();
            if (selectedText) {
                $('#programNameInput').val(selectedText);
                $('#programDisplayPreview').text(selectedText);
            }
        });

        // Select2 for Employee Search Ajax
        $('.select2-employee-ajax').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '{{ route("dss.select2.employees") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, page: params.page };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results || data, function (item) {
                            return {
                                id: item.id,
                                text: item.text || (item.name + (item.designation ? ' (' + item.designation + ')' : '')),
                                designation: item.designation || ''
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });
</script>
@endpush
