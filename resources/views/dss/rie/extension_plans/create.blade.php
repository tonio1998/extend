@extends('layouts.dss.app')

@section('title', 'Step 1: Extension Plan Form — RIE System')

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
                <a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">Extension Project</a>
                <span>/</span>
                <span class="text-dark fw-bold">New Extension Plan</span>
            </div>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-file-document-edit text-warning"></i>
                <span>Step 1: Extension Plan Form</span>
                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small px-2 py-1">Step 1 of 2</span>
            </h4>
            <div class="text-muted extra-small mt-1">
                Formulate, schedule, and route your university extension project proposal through institutional review &amp; DTS compliance.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.plans.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Masterlist
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-2xs mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-alert-circle fs-5 flex-shrink-0 mt-1"></i>
                <div>
                    <strong class="d-block mb-1">Please review the following errors:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('dss.rie.plans.store') }}" method="POST" id="extensionPlanForm">
        @csrf
        <input type="hidden" name="action_type" id="actionTypeInput" value="submit">

        <div class="row g-4">
            <!-- Left Column: Main Plan Inputs (Document Flow) -->
            <div class="col-lg-8">
                <!-- 1. Institutional Document Card -->
                <div class="card border rounded-3 shadow-2xs mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 12px;">1</span>
                            <h6 class="fw-bold m-0 text-dark">Program &amp; Activity Identification</h6>
                        </div>
                        <span class="badge bg-light text-muted border font-mono extra-small">FORM-EXT-01</span>
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
                                    @endphp
                                    <option value="{{ $prog->id }}"
                                            data-program-name="{{ $label }}"
                                            {{ old('academic_program_id') == $prog->id ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="program_name" id="programNameInput" value="{{ old('program_name', 'SNSU CITY CAMPUS - BTVTED') }}">
                            <div class="form-text extra-small text-muted mt-1">
                                Exact Program format on document: <span class="fw-semibold text-dark" id="programDisplayPreview">{{ old('program_name', 'SNSU CITY CAMPUS - BTVTED') }}</span>
                            </div>
                        </div>

                        <!-- 3rd-Party Tracking Number (Optional) -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase mb-0">
                                    <i class="mdi mdi-barcode-scan text-primary me-1"></i> 3rd-Party DTS Tracking Number <span class="badge bg-light text-muted border font-mono ms-1" style="font-size: 10px;">Optional</span>
                                </label>
                                <span class="extra-small text-muted">Can be connected later</span>
                            </div>
                            <input type="text" name="tracking_number" class="form-control font-mono"
                                   placeholder="e.g. SNSU-2026-00123 (or leave blank for now)"
                                   value="{{ old('tracking_number') }}">
                            <div class="form-text extra-small text-muted">
                                You can save this record now and connect or update the tracking number later before printing official documents.
                            </div>
                        </div>

                        <!-- Extension Activities / Project Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Extension Activities (Project Title) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="extension_activity" class="form-control fw-bold text-dark fs-6"
                                   placeholder="e.g. PROJECT KAON"
                                   value="{{ old('extension_activity') }}" required>
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
                                      placeholder="State the specific objectives of the extension project (e.g. 1. Conduct livelihood capacity building, 2. Promote nutrition literacy)..." required>{{ old('objectives') }}</textarea>
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
                                           value="{{ old('sub_extension_proposal_date') }}">
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
                                           value="{{ old('sub_activity_design_date') }}">
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
                                           value="{{ old('sub_purchase_request_date') }}">
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
                                           value="{{ old('moa_signing_date') }}">
                                </div>
                            </div>

                            <!-- Arrival of Materials (Date Range or Selected Dates) -->
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
                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeRange" value="range" checked onchange="toggleDateMode('materials', 'range')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeRange"><i class="mdi mdi-calendar-range me-1"></i>Date Range</label>

                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeMultiple" value="multiple" onchange="toggleDateMode('materials', 'multiple')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeMultiple"><i class="mdi mdi-calendar-multiselect me-1"></i>Selected Dates</label>

                                            <input type="radio" class="btn-check" name="materials_arrival_mode" id="matModeSingle" value="single" onchange="toggleDateMode('materials', 'single')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="matModeSingle"><i class="mdi mdi-calendar me-1"></i>Single Date</label>
                                        </div>
                                    </div>

                                    <!-- Range Container -->
                                    <div id="materialsRangeContainer" class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">Start Date (Arrival Begins):</label>
                                            <input type="date" name="materials_arrival_start" id="materialsArrivalStart" class="form-control form-control-sm"
                                                   value="{{ old('materials_arrival_start') }}" onchange="updateDatePreview('materials')">
                                        </div>
                                        <div class="col-md-2 text-center pt-md-3">
                                            <span class="text-muted extra-small fw-bold">to</span>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">End Date (All Materials Arrived):</label>
                                            <input type="date" name="materials_arrival_end" id="materialsArrivalEnd" class="form-control form-control-sm"
                                                   value="{{ old('materials_arrival_end') }}" onchange="updateDatePreview('materials')">
                                        </div>
                                    </div>

                                    <!-- Multiple / Selected Dates Container -->
                                    <div id="materialsMultipleContainer" class="d-none">
                                        <label class="extra-small text-muted mb-1">Click to select specific delivery dates on calendar:</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white"><i class="mdi mdi-calendar-multiselect text-primary"></i></span>
                                            <input type="text" id="materialsMultiPicker" class="form-control form-control-sm bg-white" placeholder="Click to select multiple delivery dates..." readonly>
                                        </div>
                                        <input type="hidden" name="materials_arrival_dates" id="materialsDatesHidden" value="{{ old('materials_arrival_dates') }}">
                                    </div>

                                    <!-- Single Date Container -->
                                    <div id="materialsSingleContainer" class="d-none">
                                        <label class="extra-small text-muted mb-1">Target Arrival Date:</label>
                                        <input type="date" id="materialsSingleDate" class="form-control form-control-sm" onchange="syncSingleToRange('materials', this.value)">
                                    </div>

                                    <!-- Computed Format & Calendar Badge -->
                                    <input type="hidden" name="materials_arrival_date" id="materialsDateFormatted" value="{{ old('materials_arrival_date') }}">
                                    <div class="mt-2 d-flex align-items-center justify-content-between extra-small">
                                        <span class="text-muted">Document Text: <strong class="text-dark" id="materialsDisplayPreview">{{ old('materials_arrival_date', 'Not specified') }}</strong></span>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border" id="materialsCalendarBadge"><i class="mdi mdi-calendar-clock me-1"></i>RIE Calendar Synced</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Conduct of Extension Activity (Date Range or Selected Dates) -->
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
                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeRange" value="range" checked onchange="toggleDateMode('conduct', 'range')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeRange"><i class="mdi mdi-calendar-range me-1"></i>Date Range</label>

                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeMultiple" value="multiple" onchange="toggleDateMode('conduct', 'multiple')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeMultiple"><i class="mdi mdi-calendar-multiselect me-1"></i>Selected Dates</label>

                                            <input type="radio" class="btn-check" name="conduct_activity_mode" id="conductModeSingle" value="single" onchange="toggleDateMode('conduct', 'single')">
                                            <label class="btn btn-outline-secondary date-mode-btn" for="conductModeSingle"><i class="mdi mdi-calendar me-1"></i>Single Date</label>
                                        </div>
                                    </div>

                                    <!-- Range Container -->
                                    <div id="conductRangeContainer" class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">Start Date (Activity Launch):</label>
                                            <input type="date" name="conduct_activity_start" id="conductActivityStart" class="form-control form-control-sm"
                                                   value="{{ old('conduct_activity_start') }}" onchange="updateDatePreview('conduct')">
                                        </div>
                                        <div class="col-md-2 text-center pt-md-3">
                                            <span class="text-muted extra-small fw-bold">to</span>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="extra-small text-muted mb-1">End Date (Activity Culmination):</label>
                                            <input type="date" name="conduct_activity_end" id="conductActivityEnd" class="form-control form-control-sm"
                                                   value="{{ old('conduct_activity_end') }}" onchange="updateDatePreview('conduct')">
                                        </div>
                                    </div>

                                    <!-- Multiple / Selected Dates Container -->
                                    <div id="conductMultipleContainer" class="d-none">
                                        <label class="extra-small text-muted mb-1">Click to select specific extension execution dates on calendar:</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white"><i class="mdi mdi-calendar-multiselect text-success"></i></span>
                                            <input type="text" id="conductMultiPicker" class="form-control form-control-sm bg-white" placeholder="Click to select multiple execution dates..." readonly>
                                        </div>
                                        <input type="hidden" name="conduct_activity_dates" id="conductDatesHidden" value="{{ old('conduct_activity_dates') }}">
                                    </div>

                                    <!-- Single Date Container -->
                                    <div id="conductSingleContainer" class="d-none">
                                        <label class="extra-small text-muted mb-1">Execution Date:</label>
                                        <input type="date" id="conductSingleDate" class="form-control form-control-sm" onchange="syncSingleToRange('conduct', this.value)">
                                    </div>

                                    <!-- Computed Format & Calendar Badge -->
                                    <input type="hidden" name="conduct_activity_date" id="conductDateFormatted" value="{{ old('conduct_activity_date') }}">
                                    <div class="mt-2 d-flex align-items-center justify-content-between extra-small">
                                        <span class="text-muted">Document Text: <strong class="text-dark" id="conductDisplayPreview">{{ old('conduct_activity_date', 'Not specified') }}</strong></span>
                                        <span class="badge bg-success-subtle text-success border" id="conductCalendarBadge"><i class="mdi mdi-calendar-check me-1"></i>RIE Calendar Synced</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submission of Narrative Reports, Attendance, Evaluation & Cert. Commendation -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark extra-small">
                                    Submission of Narrative Reports, Attendance, Evaluation &amp; Cert. Commendation
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-file-check-outline"></i></span>
                                    <input type="date" name="sub_narrative_report_date" class="form-control"
                                           value="{{ old('sub_narrative_report_date') }}">
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
                                           value="{{ old('monitoring_feedback_date') }}">
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
                        <!-- Beneficiaries Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                Beneficiaries Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="beneficiaries" rows="2" class="form-control"
                                      placeholder="e.g. Daycare Worker and BHW of Surigao City" required>{{ old('beneficiaries') }}</textarea>
                            <div class="form-text extra-small text-muted">
                                Target community sector, partner agency, LGU, or barangay stakeholders.
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Target Number of Beneficiaries -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Target Beneficiaries <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-multiple"></i></span>
                                    <input type="number" name="target_beneficiaries_count" class="form-control" min="1"
                                           placeholder="e.g. 50" value="{{ old('target_beneficiaries_count', 50) }}" required>
                                </div>
                            </div>

                            <!-- Proposed Budget -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Proposed Budget <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">₱</span>
                                    <input type="number" step="0.01" name="proposed_budget" class="form-control font-mono fw-bold"
                                           placeholder="e.g. 80000.00" value="{{ old('proposed_budget', '80000.00') }}" required>
                                </div>
                            </div>

                            <!-- Number of Extensionists -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark extra-small text-uppercase">
                                    Number of Extensionists <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-tie"></i></span>
                                    <input type="number" name="extensionists_count" class="form-control" min="1"
                                           placeholder="e.g. 15" value="{{ old('extensionists_count', 15) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Signatories & Routing Workflow -->
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
                                {{ $defaultSignatories['college_name'] ?? 'College of Computer and Information Sciences' }}
                            </span>
                        </div>
                        <div class="extra-small text-muted mt-1">
                            Dynamic 4-tier signatory chain automatically resolved by Academic College / Department.
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <!-- Hidden Signatory Form Fields -->
                        <input type="hidden" name="reviewed_by_employee_id" id="inputReviewedEmpId" value="{{ $defaultSignatories['reviewed']['employee_id'] ?? '' }}">
                        <input type="hidden" name="reviewed_by_name" id="inputReviewedName" value="{{ $defaultSignatories['reviewed']['name'] ?? '' }}">
                        <input type="hidden" name="reviewed_by_designation" id="inputReviewedDesig" value="{{ $defaultSignatories['reviewed']['designation'] ?? '' }}">

                        <input type="hidden" name="noted_by_employee_id" id="inputNotedEmpId" value="{{ $defaultSignatories['noted']['employee_id'] ?? '' }}">
                        <input type="hidden" name="noted_by_name" id="inputNotedName" value="{{ $defaultSignatories['noted']['name'] ?? '' }}">
                        <input type="hidden" name="noted_by_designation" id="inputNotedDesig" value="{{ $defaultSignatories['noted']['designation'] ?? '' }}">

                        <input type="hidden" name="approved_by_employee_id" id="inputApprovedEmpId" value="{{ $defaultSignatories['noted']['employee_id'] ?? '' }}">
                        <input type="hidden" name="approved_by_name" id="inputApprovedName" value="{{ $defaultSignatories['noted']['name'] ?? '' }}">
                        <input type="hidden" name="approved_by_designation" id="inputApprovedDesig" value="{{ $defaultSignatories['noted']['designation'] ?? '' }}">

                        <input type="hidden" name="concurred_by_employee_id" id="inputConcurredEmpId" value="{{ $defaultSignatories['concurred_1']['employee_id'] ?? '' }}">
                        <input type="hidden" name="concurred_by_name" id="inputConcurredName" value="{{ $defaultSignatories['concurred_1']['name'] ?? '' }}">
                        <input type="hidden" name="concurred_by_designation" id="inputConcurredDesig" value="{{ $defaultSignatories['concurred_1']['designation'] ?? '' }}">

                        <input type="hidden" name="concurred_by_2_employee_id" id="inputConcurred2EmpId" value="{{ $defaultSignatories['concurred_2']['employee_id'] ?? '' }}">
                        <input type="hidden" name="concurred_by_2_name" id="inputConcurred2Name" value="{{ $defaultSignatories['concurred_2']['name'] ?? '' }}">
                        <input type="hidden" name="concurred_by_2_designation" id="inputConcurred2Desig" value="{{ $defaultSignatories['concurred_2']['designation'] ?? '' }}">

                        <!-- Tier 1: Prepared By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-primary text-white extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-account-star me-1"></i> 1. Prepared by:
                                </span>
                                <span class="text-muted extra-small">Project Leader</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="projectLeaderDisplayName">
                                {{ $projectLeaderName }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;">
                                {{ $projectLeaderDesignation }}
                            </div>
                        </div>

                        <!-- Tier 2: Reviewed By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-clipboard-check me-1"></i> 2. Reviewed by:
                                </span>
                                <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Auto-Fetched</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="reviewedDisplayName">
                                {{ $defaultSignatories['reviewed']['name'] ?? 'College Head for Extension' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="reviewedDisplayDesig">
                                {{ $defaultSignatories['reviewed']['designation'] ?? 'College Head for Extension' }}
                            </div>
                        </div>

                        <!-- Tier 3: Noted By -->
                        <div class="p-2 mb-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-shield-check me-1"></i> 3. Noted by:
                                </span>
                                <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Auto-Fetched</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="notedDisplayName">
                                {{ $defaultSignatories['noted']['name'] ?? 'College Dean' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="notedDisplayDesig">
                                {{ $defaultSignatories['noted']['designation'] ?? 'College Dean' }}
                            </div>
                        </div>

                        <!-- Tier 4: Concurred By -->
                        <div class="p-2 rounded-2 border bg-light">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle extra-small" style="font-size: 10px;">
                                    <i class="mdi mdi-certificate me-1"></i> 4. Concurred by:
                                </span>
                                <span class="text-muted extra-small" style="font-size: 9.5px;">University Level</span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="concurred1DisplayName">
                                {{ $defaultSignatories['concurred_1']['name'] ?? 'EVER JOY G. RABADAN, MAEM, MSc.' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;" id="concurred1DisplayDesig">
                                {{ $defaultSignatories['concurred_1']['designation'] ?? 'University Extension Specialist V' }}
                            </div>
                            <div class="border-top my-1 pt-1">
                                <div class="fw-bold text-dark extra-small text-truncate" id="concurred2DisplayName">
                                    {{ $defaultSignatories['concurred_2']['name'] ?? 'ALVIE ELMA C. PLAZA, MA' }}
                                </div>
                                <div class="text-muted" style="font-size: 10.5px;" id="concurred2DisplayDesig">
                                    {{ $defaultSignatories['concurred_2']['designation'] ?? 'University Extension Director' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3rd-Party DTS Tracking Integration Note -->
                <div class="card border rounded-3 shadow-2xs mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-color: #e2e8f0 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="mdi mdi-barcode-scan text-primary fs-4 flex-shrink-0 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-dark m-0" style="font-size: 13.5px;">
                                    3rd-Party DTS Tracking Integration
                                </h6>
                                <p class="extra-small text-muted mb-0 mt-1" style="font-size: 11.5px; line-height: 1.4;">
                                    You can save and submit this Extension Plan now. Official 3rd-party DTS tracking numbers can be connected anytime before printing documents.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Submission Buttons -->
                <div class="card border rounded-3 shadow-2xs">
                    <div class="card-body p-3 d-flex flex-column gap-2">
                        <button type="button" class="btn btn-primary btn-lg fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                onclick="submitPlan('submit')">
                            <i class="mdi mdi-send-check fs-5"></i>
                            <span>Submit Extension Plan</span>
                        </button>

                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2"
                                onclick="submitPlan('draft')">
                            <i class="mdi mdi-content-save-outline"></i>
                            <span>Save as Draft</span>
                        </button>
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
    function submitPlan(actionType) {
        document.getElementById('actionTypeInput').value = actionType;
        document.getElementById('extensionPlanForm').submit();
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
        var startInput = document.getElementById(type === 'materials' ? 'materialsArrivalStart' : 'conductActivityStart');
        var endInput = document.getElementById(type === 'materials' ? 'materialsArrivalEnd' : 'conductActivityEnd');
        startInput.value = val;
        endInput.value = val;
        updateDatePreview(type);
    }

    function formatDateStr(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        if (parts.length === 3) {
            var date = new Date(parts[0], parts[1] - 1, parts[2]);
            return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        }
        return dateStr;
    }

    function updateDatePreview(type) {
        var isMat = (type === 'materials');
        var startVal = document.getElementById(isMat ? 'materialsArrivalStart' : 'conductActivityStart').value;
        var endVal = document.getElementById(isMat ? 'materialsArrivalEnd' : 'conductActivityEnd').value;
        var previewElem = document.getElementById(isMat ? 'materialsDisplayPreview' : 'conductDisplayPreview');
        var hiddenInput = document.getElementById(isMat ? 'materialsDateFormatted' : 'conductDateFormatted');

        if (!startVal) {
            previewElem.innerText = 'Not specified';
            hiddenInput.value = '';
            return;
        }

        var text = '';
        if (startVal && endVal && startVal !== endVal) {
            var sText = formatDateStr(startVal);
            var eText = formatDateStr(endVal);
            var startDate = new Date(startVal);
            var endDate = new Date(endVal);
            var diffTime = Math.abs(endDate - startDate);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            text = sText + ' – ' + eText + ' (' + diffDays + ' Days)';
        } else {
            text = formatDateStr(startVal);
        }

        previewElem.innerText = text;
        hiddenInput.value = text;
    }

    function updateMultiDatePreview(type) {
        var isMat = (type === 'materials');
        var hiddenDates = document.getElementById(isMat ? 'materialsDatesHidden' : 'conductDatesHidden').value;
        var previewElem = document.getElementById(isMat ? 'materialsDisplayPreview' : 'conductDisplayPreview');
        var hiddenInput = document.getElementById(isMat ? 'materialsDateFormatted' : 'conductDateFormatted');

        if (!hiddenDates) {
            previewElem.innerText = 'No dates selected';
            hiddenInput.value = '';
            return;
        }

        var datesArr = hiddenDates.split(',').map(function(d) { return d.trim(); }).filter(Boolean);
        var formatted = datesArr.map(function(d) {
            var parts = d.split('-');
            if (parts.length === 3) {
                var dt = new Date(parts[0], parts[1] - 1, parts[2]);
                return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
            return d;
        });

        var text = formatted.join(', ') + ' (' + datesArr.length + ' Dates)';
        previewElem.innerText = text;
        hiddenInput.value = text;

        if (datesArr.length > 0) {
            datesArr.sort();
            var startInput = document.getElementById(isMat ? 'materialsArrivalStart' : 'conductActivityStart');
            var endInput = document.getElementById(isMat ? 'materialsArrivalEnd' : 'conductActivityEnd');
            startInput.value = datesArr[0];
            endInput.value = datesArr[datesArr.length - 1];
        }
    }

    $(document).ready(function() {
        // Initialize Flatpickr Multi-Date Pickers
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#materialsMultiPicker', {
                mode: 'multiple',
                dateFormat: 'Y-m-d',
                conjunction: ', ',
                onChange: function(selectedDates, dateStr) {
                    $('#materialsDatesHidden').val(dateStr);
                    updateMultiDatePreview('materials');
                }
            });

            flatpickr('#conductMultiPicker', {
                mode: 'multiple',
                dateFormat: 'Y-m-d',
                conjunction: ', ',
                onChange: function(selectedDates, dateStr) {
                    $('#conductDatesHidden').val(dateStr);
                    updateMultiDatePreview('conduct');
                }
            });
        }

        // Initialize preview on page load if prefilled
        updateDatePreview('materials');
        updateDatePreview('conduct');

        // Sync Academic Program Name input & Dynamic College Signatories
        $('#academicProgramSelect').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var progId = $(this).val();
            var progName = selectedOption.data('program-name') || selectedOption.text().trim();
            if (progName) {
                $('#programNameInput').val(progName);
                $('#programDisplayPreview').text(progName);
            }
            if (progId) {
                $.ajax({
                    url: '{{ route('dss.rie.plans.signatories_by_program') }}',
                    type: 'GET',
                    data: { program_id: progId },
                    success: function(res) {
                        if (res.success && res.signatories) {
                            var s = res.signatories;
                            $('#collegeIndicatorBadge').text(res.college_name || 'Academic Unit');
                            // Reviewed by (College Extension Head)
                            $('#reviewedDisplayName').text(s.reviewed.name);
                            $('#reviewedDisplayDesig').text(s.reviewed.designation);
                            $('#inputReviewedEmpId').val(s.reviewed.employee_id);
                            $('#inputReviewedName').val(s.reviewed.name);
                            $('#inputReviewedDesig').val(s.reviewed.designation);
                            // Noted by (College Dean / UnitHead)
                            $('#notedDisplayName').text(s.noted.name);
                            $('#notedDisplayDesig').text(s.noted.designation);
                            $('#inputNotedEmpId').val(s.noted.employee_id);
                            $('#inputNotedName').val(s.noted.name);
                            $('#inputNotedDesig').val(s.noted.designation);
                            $('#inputApprovedEmpId').val(s.noted.employee_id);
                            $('#inputApprovedName').val(s.noted.name);
                            $('#inputApprovedDesig').val(s.noted.designation);
                            // Concurred by
                            $('#concurred1DisplayName').text(s.concurred_1.name);
                            $('#concurred1DisplayDesig').text(s.concurred_1.designation);
                            $('#concurred2DisplayName').text(s.concurred_2.name);
                            $('#concurred2DisplayDesig').text(s.concurred_2.designation);
                        }
                    }
                });
            }
        });

        // Initialize Server-side Select2 for Noted By Employee
        $('#notedBySelect').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '{{ route('dss.select2.employees') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1,
                        names_only: 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            placeholder: 'Type employee name (e.g. Madi Lim)...',
            allowClear: true
        });

        // Initialize Server-side Select2 for Approved By Employee
        $('#approvedBySelect').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '{{ route('dss.select2.employees') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1,
                        names_only: 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            placeholder: 'Type dean name (e.g. Fax Lika)...',
            allowClear: true
        });
    });
</script>
@endpush
