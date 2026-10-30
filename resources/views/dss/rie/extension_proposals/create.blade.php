@extends('layouts.dss.app')

@section('title', 'New Activity Proposal — RIE System')

@section('content')
<div class="container-fluid p-0">
    {{-- Hero Banner --}}
    <div class="canva-hero-header p-4 mb-4 shadow-2xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="canva-theme-badge">
                        <i class="mdi mdi-lightbulb-on-outline"></i> RIE ACTIVITY PROPOSAL
                    </span>
                    <span class="text-secondary extra-small">Official SNSU 6-Page Proposal Format</span>
                </div>
                <h3 class="fw-extrabold text-dark mb-1">New Activity Proposal</h3>
                <p class="text-secondary small mb-0">
                    Official university format for extension project and activity proposals.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="mdi mdi-arrow-left me-1"></i> Cancel
                </a>
            </div>
        </div>
    </div>

    <!-- Verified Approved Parent Plan Card -->
    <div class="p-3 mb-4 rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background-color: #f0fdf4; border: 1.5px solid #bbf7d0;">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success rounded-circle p-2 d-inline-flex text-white shadow-2xs">
                <i class="mdi mdi-check-bold fs-6"></i>
            </span>
            <div>
                <div class="extra-small text-success-emphasis fw-bold text-uppercase font-mono">
                    <i class="mdi mdi-shield-check me-1"></i> Authorized From Approved Extension Plan
                </div>
                <div class="fw-bold text-dark fs-6">{{ $plan->extension_activity }}</div>
                <div class="extra-small text-muted font-mono">
                    Code: <strong>{{ $plan->plan_code }}</strong> &bull; Program: {{ $plan->program_name }} &bull; Approved by: {{ $plan->approved_by_name ?: 'Dean' }}
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-3 py-1 extra-small fw-semibold">
                <i class="mdi mdi-open-in-new me-1"></i> View Plan Details
            </a>
        </div>
    </div>

    <!-- Proposal Creation Form -->
    <form action="{{ route('dss.rie.proposals.store') }}" method="POST" id="proposalForm">
        @csrf
        <input type="hidden" name="extension_plan_id" value="{{ $plan->id }}">

        <!-- PART I: BASIC INFORMATION -->
        <div class="canva-card p-4 border shadow-2xs mb-4">
            <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-inline-flex">
                    <i class="mdi mdi-information-outline fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark fs-6 m-0">PART I. BASIC INFORMATION</h5>
                    <p class="text-muted extra-small m-0">Activity title, banner program, extension agenda, proponents, beneficiaries, and budget.</p>
                </div>
            </div>

            <div class="row g-3">
                <!-- A. Title of the Activity -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark extra-small">A. Title of the Activity <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-sm font-sans fw-semibold" value="{{ old('title', $plan->extension_activity) }}" placeholder="Enter full activity title..." required>
                </div>

                <!-- B. Banner Program -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark extra-small d-block mb-2">B. Banner Program <span class="text-muted fw-normal">(Select applicable banner program)</span></label>
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="row g-2 mb-2">
                            @php
                                $programsList = isset($bannerPrograms) && $bannerPrograms instanceof \Illuminate\Support\Collection
                                    ? $bannerPrograms->pluck('name')->toArray()
                                    : (is_array($bannerPrograms ?? null) ? $bannerPrograms : \App\Models\RIE\ExtensionBannerProgram::active()->ordered()->pluck('name')->toArray());
                                if (empty($programsList)) {
                                    $programsList = ['TECHASSIST', 'FOODPRO', 'AGRIYAMAN', 'MODFARM 360', 'GOVeRN', 'SUSTINAHAN', 'RENEWTECH', 'ReStat-QUIIP', 'STEAMUP', 'TOURGABAY', 'MSMEntri', 'KATUTUBO', 'ECOREVIVE', 'GADLIGHT', 'WELLNESS', 'TUBIG-SIGURADO'];
                                }
                                $half = (int) ceil(count($programsList) / 2);
                                $col1Programs = array_slice($programsList, 0, $half);
                                $col2Programs = array_slice($programsList, $half);
                                $selectedBanner = old('banner_program', '');
                            @endphp
                            <div class="col-md-6">
                                @foreach($col1Programs as $prog)
                                    <div class="form-check mb-1.5">
                                        <input class="form-check-input banner-prog-radio" type="radio" name="banner_program" value="{{ $prog }}" id="bp_{{ \Illuminate\Support\Str::slug($prog) }}" {{ $selectedBanner === $prog ? 'checked' : '' }}>
                                        <label class="form-check-label extra-small text-dark fw-medium" for="bp_{{ \Illuminate\Support\Str::slug($prog) }}">
                                            {{ $prog }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                @foreach($col2Programs as $prog)
                                    <div class="form-check mb-1.5">
                                        <input class="form-check-input banner-prog-radio" type="radio" name="banner_program" value="{{ $prog }}" id="bp_{{ \Illuminate\Support\Str::slug($prog) }}" {{ $selectedBanner === $prog ? 'checked' : '' }}>
                                        <label class="form-check-label extra-small text-dark fw-medium" for="bp_{{ \Illuminate\Support\Str::slug($prog) }}">
                                            {{ $prog }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 pt-2 border-top">
                            <div class="form-check m-0">
                                <input class="form-check-input banner-prog-radio" type="radio" name="banner_program" value="Others" id="bp_others" {{ $selectedBanner === 'Others' ? 'checked' : '' }}>
                                <label class="form-check-label extra-small text-dark fw-semibold" for="bp_others">
                                    Others:
                                </label>
                            </div>
                            <input type="text" name="banner_program_other" id="bannerProgramOtherInput" class="form-control form-control-sm" placeholder="Please specify if other banner program..." value="{{ old('banner_program_other') }}" style="max-width: 450px;">
                        </div>
                    </div>
                </div>

                <!-- C. Extension Agenda -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark extra-small d-block mb-2">C. Extension Agenda <span class="text-muted fw-normal">(Check all that apply)</span></label>
                    <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">
                        @php
                            $agendasList = isset($extensionAgendas) && $extensionAgendas instanceof \Illuminate\Support\Collection
                                ? $extensionAgendas->pluck('name')->toArray()
                                : (is_array($extensionAgendas ?? null) ? $extensionAgendas : \App\Models\RIE\ExtensionAgenda::active()->ordered()->pluck('name')->toArray());
                            if (empty($agendasList)) {
                                $agendasList = [
                                    'Transformative Learning',
                                    'Livelihood Generation',
                                    'Biodiversity Conservation',
                                    'Climate Change Adoption & Risk Management',
                                    'Good Governance'
                                ];
                            }
                        @endphp
                        @foreach($agendasList as $agenda)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agenda_priorities[]" value="{{ $agenda }}" id="agenda_{{ $loop->index }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label extra-small text-dark fw-medium" for="agenda_{{ $loop->index }}">
                                    {{ $agenda }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- D. SDG Addressed -->
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <label class="form-label fw-bold text-dark extra-small m-0">
                            D. SDG Addressed <span class="text-muted fw-normal">(Click inline UN Sustainable Development Goal tiles to select/deselect)</span>
                        </label>
                    </div>
                    <div class="p-3 bg-light rounded-3 border">
                        @include('dss.rie.extension_proposals.partials.sdg_inline_tiles', [
                            'mode' => 'checkbox',
                            'inputName' => 'sdg_alignments[selected][]',
                            'idPrefix' => 'create_sdg',
                            'selectedSdgs' => old('sdg_alignments.selected', [4, 9, 17]),
                        ])
                    </div>
                </div>

                <!-- E. Project Leader & Gender -->
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark extra-small">E. Project Leader <span class="text-danger">*</span></label>
                    <select id="projectLeaderSelect" class="form-select form-select-sm proponent-select2" style="width: 100%;">
                        <option value="{{ $plan->prepared_by_name }}" selected>{{ $plan->prepared_by_name }}</option>
                    </select>
                    <input type="hidden" name="project_leader_name" id="project_leader_name" value="{{ old('project_leader_name', $plan->prepared_by_name) }}">
                    <input type="hidden" name="project_leader_title" id="project_leader_title" value="{{ old('project_leader_title', 'Project Leader') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark extra-small">
                        Gender
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Auto fetch from employee profile</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-gender-male-female"></i></span>
                        <input type="text" name="project_leader_gender" id="project_leader_gender" class="form-control form-control-sm bg-light fw-bold" value="{{ old('project_leader_gender', $defaultGender) }}" placeholder="e.g. Female / Male">
                    </div>
                </div>

                <!-- F. Implementing Colleges -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark extra-small">
                        F. Implementing Colleges
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Allow multiple Colleges</span>
                    </label>
                    <select name="implementing_colleges[]" id="implementingCollegesSelect" class="form-select form-select-sm" multiple="multiple" style="width: 100%;">
                        @foreach(($colleges ?? []) as $col)
                            @php
                                $isCurrent = ($plan->academicProgram?->department_id == $col->id) || ($plan->campus_id == $col->campus_id && str_contains(strtolower($col->unit_name), 'computing'));
                            @endphp
                            <option value="{{ $col->unit_name }}" {{ $isCurrent ? 'selected' : '' }}>
                                {{ $col->unit_name }} ({{ $col->unit_code ?: 'College' }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text extra-small text-muted">Select all participating colleges/departments.</div>
                </div>

                <!-- G. Collaborating Agencies (Repeater) -->
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold text-dark extra-small m-0">G. Collaborating Agencies</label>
                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" id="btnAddAgency">
                            <i class="mdi mdi-plus me-1"></i> Add Agency
                        </button>
                    </div>
                    <div class="border rounded-3 p-2 bg-light">
                        <table class="table table-sm table-hover align-middle mb-0" id="collaboratingAgenciesTable">
                            <thead class="extra-small text-muted text-uppercase">
                                <tr>
                                    <th style="width: 50%;">Agency Name</th>
                                    <th>Role / Contribution</th>
                                    <th style="width: 60px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="agenciesContainer">
                                <tr class="agency-row" data-index="0">
                                    <td>
                                        <input type="text" name="collaborating_agencies_data[0][agency]" class="form-control form-control-sm" placeholder="e.g. LGU - Tubajon, DepEd Surigao, etc." value="{{ old('collaborating_agencies_data.0.agency') }}">
                                    </td>
                                    <td>
                                        <input type="text" name="collaborating_agencies_data[0][role]" class="form-control form-control-sm" placeholder="e.g. Venue partner & community mobilization" value="{{ old('collaborating_agencies_data.0.role') }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger btn-remove-agency" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- H. Target Beneficiaries (Table with Male/Female/Total auto math) -->
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <label class="form-label fw-bold text-dark extra-small m-0">H. Target Beneficiaries</label>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">And more depende</span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-1" id="btnAddBeneficiary">
                            <i class="mdi mdi-account-multiple-plus me-1"></i> Add Beneficiary Group
                        </button>
                    </div>
                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-sm table-bordered align-middle mb-0" id="targetBeneficiariesTable">
                            <thead class="table-light extra-small text-uppercase">
                                <tr>
                                    <th>Name (e.g. LGU – Tubajon, Out-of-School Youth)</th>
                                    <th style="width: 140px;" class="text-center">Male</th>
                                    <th style="width: 140px;" class="text-center">Female</th>
                                    <th style="width: 140px;" class="text-center">Total</th>
                                    <th style="width: 60px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="beneficiariesContainer">
                                <tr class="beneficiary-row" data-index="0">
                                    <td>
                                        <input type="text" name="target_beneficiaries_data[0][name]" class="form-control form-control-sm" placeholder="e.g. LGU – Tubajon" value="{{ old('target_beneficiaries_data.0.name', $plan->beneficiaries) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="target_beneficiaries_data[0][male]" class="form-control form-control-sm text-center ben-male" min="0" value="{{ old('target_beneficiaries_data.0.male', 30) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="target_beneficiaries_data[0][female]" class="form-control form-control-sm text-center ben-female" min="0" value="{{ old('target_beneficiaries_data.0.female', 20) }}">
                                    </td>
                                    <td>
                                        <input type="number" name="target_beneficiaries_data[0][total]" class="form-control form-control-sm text-center fw-bold bg-light ben-total" readonly value="{{ old('target_beneficiaries_data.0.total', 50) }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger btn-remove-ben" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light extra-small fw-bold">
                                <tr>
                                    <td class="text-end text-uppercase">Grand Total Beneficiaries:</td>
                                    <td class="text-center text-primary" id="totalMaleCount">30</td>
                                    <td class="text-center text-primary" id="totalFemaleCount">20</td>
                                    <td class="text-center text-success fs-6" id="grandTotalBenCount">50</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- I. Estimated Budget Requirement -->
                <div class="col-12 mt-3">
                    <h6 class="fw-bold text-dark extra-small text-uppercase mb-2 border-top pt-3">I. Estimated Budget Requirement</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark extra-small">
                        Internal Fund Source (Institution Share)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Amount only or text (expertise)</span>
                    </label>
                    <textarea name="fund_source_internal" class="form-control form-control-sm font-sans" rows="2" placeholder="e.g. P15,000.00 / In-kind expertise and University Service Vehicle">{{ old('fund_source_internal', old('budget_institution_share', 'In-Kind (University Service Vehicle & Faculty Expertise)')) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark extra-small">
                        External Fund Source (Partner Share)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Amount only or text</span>
                    </label>
                    <textarea name="fund_source_external" class="form-control form-control-sm font-sans" rows="2" placeholder="e.g. P10,000.00 / In-kind venue and meal provisions">{{ old('fund_source_external', old('budget_partner_share', 'In-Kind (Venue & Community Accommodations)')) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark extra-small">Estimated Total Budget Display</label>
                    <input type="text" name="budget_total_display" class="form-control form-control-sm" value="{{ old('budget_total_display', 'Grand Total: No Cash Requirement') }}" placeholder="e.g. Grand Total: P25,000.00 or No Cash Requirement">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark extra-small">Estimated Cash Amount (PHP)</label>
                    <input type="number" step="0.01" name="budget_cash_amount" class="form-control form-control-sm" value="{{ old('budget_cash_amount', $plan->proposed_budget ?: 0) }}">
                </div>

                <!-- J. Duration -->
                <div class="col-12 mt-3">
                    <h6 class="fw-bold text-dark extra-small text-uppercase mb-2 border-top pt-3">J. Duration</h6>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark extra-small">Started Date <span class="text-muted fw-normal">(mm/dd/yyyy)</span></label>
                    <input type="date" name="duration_start" id="durationStartInput" class="form-control form-control-sm" value="{{ old('duration_start', $plan->conduct_activity_start?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark extra-small">End Date <span class="text-muted fw-normal">(mm/dd/yyyy)</span></label>
                    <input type="date" name="duration_end" id="durationEndInput" class="form-control form-control-sm" value="{{ old('duration_end', $plan->conduct_activity_end?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark extra-small">
                        No of Months
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Auto calculate.. no decimal points</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="number" name="duration_months" id="durationMonthsInput" class="form-control form-control-sm bg-light fw-bold text-center" value="{{ old('duration_months', $durationMonths) }}" readonly>
                        <span class="input-group-text bg-light text-muted">Month(s)</span>
                    </div>
                </div>

                <!-- H. Proponents & Leadership -->
                <div class="col-12 mt-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3 mb-2">
                        <div>
                            <h6 class="fw-bold text-dark extra-small text-uppercase m-0">Project Proponents &amp; Leadership</h6>
                            <p class="text-muted extra-small m-0">Project Leader (required), optional Assistant Leader, and member extensionists.</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-3 shadow-2xs fw-semibold" id="btnAddAssistantLeaderRow">
                                <i class="mdi mdi-account-star-outline me-1"></i> + Add Assistant Leader
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 shadow-2xs fw-semibold" id="btnAddFacultyRow">
                                <i class="mdi mdi-account-plus-outline me-1"></i> + Add Faculty Member
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 shadow-2xs fw-semibold" id="btnAddManualRow">
                                <i class="mdi mdi-account-group-outline me-1"></i> + Add Custom Member
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3 overflow-visible mb-2">
                        <table class="table table-sm table-hover align-middle mb-0" id="proponentsTable">
                            <thead class="table-light extra-small text-uppercase">
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="width: 160px;">Role Type</th>
                                    <th>Extensionist / Proponent Name <span class="text-danger">*</span></th>
                                    <th style="width: 280px;">Role / Designation <span class="text-danger">*</span></th>
                                    <th style="width: 90px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="proponentsContainer">
                                {{-- Row 0: Project Leader --}}
                                <tr class="proponent-row" data-index="0" data-mode="faculty" data-role-type="leader">
                                    <td class="text-center font-mono text-muted extra-small row-number">1</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2 py-1 mode-badge">
                                            <i class="mdi mdi-account-star me-0.5"></i> Project Leader
                                        </span>
                                    </td>
                                    <td>
                                        <div class="name-cell-wrap">
                                            <select class="form-select form-select-sm proponent-select2" style="width: 100%;">
                                                <option value="{{ $plan->prepared_by_name }}" selected>{{ $plan->prepared_by_name }}</option>
                                            </select>
                                            <input type="hidden" name="proponents[0][name]" class="prop-name" value="{{ old('project_leader_name', $plan->prepared_by_name) }}">
                                            <input type="hidden" name="proponents[0][employee_id]" class="prop-emp-id" value="{{ $plan->prepared_by_employee_id }}">
                                            <input type="hidden" name="proponents[0][type]" class="prop-type" value="faculty">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="proponents[0][role]" class="form-control form-control-sm font-sans fw-semibold prop-role bg-light" value="Project Leader" readonly required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-secondary" disabled title="Project Leader is required">
                                            <i class="mdi mdi-lock-outline"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PART III: SUGGESTED APPROVAL SIGNATORIES -->
        <div class="canva-card p-4 border shadow-2xs mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                        <i class="mdi mdi-account-multiple-check text-primary fs-5"></i>
                        <span>Approval Signatories Chain (Official SNSU Review Flow)</span>
                    </h6>
                    <p class="text-muted extra-small m-0">
                        Signatories match the new official SNSU Activity Proposal format:
                        <strong>Prepared by</strong> (Project Leader) &rarr;
                        <strong>Reviewed by</strong> (Focal Person &amp; Coordinator) &rarr;
                        <strong>Noted</strong> (Dean &amp; Campus Director) &rarr;
                        <strong>Recommending Approval</strong> (Extension Director) &rarr;
                        <strong>Approved</strong> (VP RIE).
                    </p>
                </div>
                <span class="badge bg-light text-muted border font-mono extra-small">
                    {{ count($suggestedSignatories ?? []) }} Signatories
                </span>
            </div>

            <div class="row g-2">
                @foreach(($suggestedSignatories ?? []) as $idx => $sig)
                    <div class="col-md-3 col-sm-6">
                        <div class="p-3 rounded-3 border bg-light h-100 shadow-2xs">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="extra-small font-mono fw-bold text-primary">
                                    {{ $sig['role_label'] ?? 'Signatory' }}
                                </span>
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" title="{{ $sig['name'] }}">
                                {{ $sig['name'] ?: '— To Be Selected / Assigned —' }}
                            </div>
                            <div class="text-muted extra-small text-truncate" style="font-size: 11px;" title="{{ $sig['designation'] }}">
                                {{ $sig['designation'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FORM ACTIONS -->
        <div class="canva-card p-3 border shadow-2xs d-flex flex-wrap align-items-center justify-content-between gap-3 bg-light">
            <div class="extra-small text-muted">
                <i class="mdi mdi-information-outline text-primary me-1"></i> Saving Part I creates a <strong>Draft Proposal</strong>. You will automatically proceed to <strong>Part II (Technical Description)</strong> to complete the Rationale, Objectives, Expected Outputs, Risk Matrix, and Line-Item Budget.
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" name="action_type" value="draft" class="btn btn-sm btn-dss-primary rounded-pill px-4 fw-bold shadow-xs d-flex align-items-center gap-1">
                    <span>Save &amp; Proceed to Part II (Technical Description)</span>
                    <i class="mdi mdi-arrow-right-circle fs-6"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Banner Program 'Others' field toggle
    function toggleBannerOthers() {
        if ($('#bp_others').is(':checked')) {
            $('#bannerProgramOtherInput').prop('disabled', false).focus();
        } else {
            $('#bannerProgramOtherInput').prop('disabled', true);
        }
    }
    $('.banner-prog-radio').on('change', toggleBannerOthers);
    toggleBannerOthers();

    // 2. Select2 for SDGs & Implementing Colleges
    if ($.fn.select2) {
        $('#sdgSelect').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select aligned Sustainable Development Goals...',
            allowClear: true
        });
        $('#implementingCollegesSelect').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select one or more implementing colleges...',
            allowClear: true
        });
    }

    // 3. Auto-calculate duration in months (no decimal points)
    function calcDurationMonths() {
        const startVal = $('#durationStartInput').val();
        const endVal = $('#durationEndInput').val();
        if (!startVal || !endVal) return;

        const d1 = new Date(startVal);
        const d2 = new Date(endVal);
        if (isNaN(d1) || isNaN(d2) || d2 < d1) {
            $('#durationMonthsInput').val(1);
            return;
        }

        let months = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
        if (d2.getDate() >= d1.getDate()) {
            months += 1;
        }
        months = Math.max(1, Math.round(months));
        $('#durationMonthsInput').val(months);
    }
    $('#durationStartInput, #durationEndInput').on('change', calcDurationMonths);

    // 4. Target Beneficiaries Auto-Math
    function updateBeneficiaryTotals() {
        let totalM = 0;
        let totalF = 0;
        let grandTotal = 0;

        $('.beneficiary-row').each(function() {
            const m = parseInt($(this).find('.ben-male').val()) || 0;
            const f = parseInt($(this).find('.ben-female').val()) || 0;
            const rowTotal = m + f;
            $(this).find('.ben-total').val(rowTotal);
            totalM += m;
            totalF += f;
            grandTotal += rowTotal;
        });

        $('#totalMaleCount').text(totalM);
        $('#totalFemaleCount').text(totalF);
        $('#grandTotalBenCount').text(grandTotal);
    }
    $(document).on('input', '.ben-male, .ben-female', updateBeneficiaryTotals);
    updateBeneficiaryTotals();

    // Add beneficiary row
    let benIndex = 1;
    $('#btnAddBeneficiary').on('click', function() {
        const idx = benIndex++;
        const row = `
            <tr class="beneficiary-row" data-index="${idx}">
                <td>
                    <input type="text" name="target_beneficiaries_data[${idx}][name]" class="form-control form-control-sm" placeholder="e.g. Target Community / Beneficiary Group">
                </td>
                <td>
                    <input type="number" name="target_beneficiaries_data[${idx}][male]" class="form-control form-control-sm text-center ben-male" min="0" value="0">
                </td>
                <td>
                    <input type="number" name="target_beneficiaries_data[${idx}][female]" class="form-control form-control-sm text-center ben-female" min="0" value="0">
                </td>
                <td>
                    <input type="number" name="target_beneficiaries_data[${idx}][total]" class="form-control form-control-sm text-center fw-bold bg-light ben-total" readonly value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-ben" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>
        `;
        $('#beneficiariesContainer').append(row);
        updateBeneficiaryTotals();
    });

    $(document).on('click', '.btn-remove-ben', function() {
        if ($('.beneficiary-row').length > 1) {
            $(this).closest('.beneficiary-row').remove();
            updateBeneficiaryTotals();
        }
    });

    // 5. Collaborating Agencies Repeater
    let agencyIndex = 1;
    $('#btnAddAgency').on('click', function() {
        const idx = agencyIndex++;
        const row = `
            <tr class="agency-row" data-index="${idx}">
                <td>
                    <input type="text" name="collaborating_agencies_data[${idx}][agency]" class="form-control form-control-sm" placeholder="e.g. Partner Agency Name">
                </td>
                <td>
                    <input type="text" name="collaborating_agencies_data[${idx}][role]" class="form-control form-control-sm" placeholder="e.g. Technical collaboration & facility use">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-agency" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>
        `;
        $('#agenciesContainer').append(row);
    });

    $(document).on('click', '.btn-remove-agency', function() {
        if ($('.agency-row').length > 1) {
            $(this).closest('.agency-row').remove();
        }
    });

    // 6. Proponents Select2 and Gender Auto-Fetch
    let nextIndex = 2;
    const container = $('#proponentsContainer');

    function initSelect2OnRow($row) {
        const $select = $row.find('.proponent-select2');
        if (!$select.length) return;

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search employee from university directory...',
            allowClear: true,
            ajax: {
                url: '{{ route("dss.select2.employees") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: (data.results || []).map(function(emp) {
                            return {
                                id: emp.full_name || emp.name,
                                employee_id: emp.id,
                                text: emp.text || emp.full_name,
                                name: emp.full_name || emp.name,
                                designation: emp.designation,
                                gender: emp.gender || ''
                            };
                        }),
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            const row = $(this).closest('.proponent-row');
            row.find('.prop-name').val(data.name || data.text || '');
            row.find('.prop-emp-id').val(data.employee_id || '');

            // If Project Leader row, sync gender to the gender input!
            if (row.data('role-type') === 'leader' || $(this).attr('id') === 'projectLeaderSelect') {
                $('#project_leader_name').val(data.name || data.text || '');
                if (data.gender) {
                    $('#project_leader_gender').val(data.gender);
                }
            }
        });
    }

    // Top Project Leader Select2
    $('#projectLeaderSelect').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Search project leader from university directory...',
        allowClear: true,
        ajax: {
            url: '{{ route("dss.select2.employees") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data) {
                return {
                    results: (data.results || []).map(function(emp) {
                        return {
                            id: emp.full_name || emp.name,
                            employee_id: emp.id,
                            text: emp.text || emp.full_name,
                            name: emp.full_name || emp.name,
                            designation: emp.designation,
                            gender: emp.gender || ''
                        };
                    }),
                    pagination: data.pagination
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        $('#project_leader_name').val(data.name || data.text || '');
        if (data.gender) {
            $('#project_leader_gender').val(data.gender);
        }
        // Sync row 0 name
        container.find('.proponent-row[data-role-type="leader"] .prop-name').val(data.name || data.text || '');
        container.find('.proponent-row[data-role-type="leader"] .prop-emp-id').val(data.employee_id || '');
    });

    container.find('.proponent-row').each(function() {
        initSelect2OnRow($(this));
    });

    // Add Proponent buttons
    $('#btnAddAssistantLeaderRow').on('click', function() {
        const idx = nextIndex++;
        const rowHtml = `
            <tr class="proponent-row" data-index="${idx}" data-mode="faculty" data-role-type="assistant">
                <td class="text-center font-mono text-muted extra-small row-number"></td>
                <td>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill extra-small px-2 py-1 mode-badge">
                        <i class="mdi mdi-account-tie me-0.5"></i> Asst. Leader
                    </span>
                </td>
                <td>
                    <div class="name-cell-wrap">
                        <select class="form-select form-select-sm proponent-select2" style="width: 100%;">
                            <option value="">Search assistant leader...</option>
                        </select>
                        <input type="hidden" name="proponents[${idx}][name]" class="prop-name" value="">
                        <input type="hidden" name="proponents[${idx}][employee_id]" class="prop-emp-id" value="">
                        <input type="hidden" name="proponents[${idx}][type]" class="prop-type" value="faculty">
                    </div>
                </td>
                <td>
                    <input type="text" name="proponents[${idx}][role]" class="form-control form-control-sm font-sans fw-medium prop-role" value="Assistant Project Leader" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-row" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>
        `;
        const $row = $(rowHtml);
        container.append($row);
        initSelect2OnRow($row);
    });

    $('#btnAddFacultyRow').on('click', function() {
        const idx = nextIndex++;
        const rowHtml = `
            <tr class="proponent-row" data-index="${idx}" data-mode="faculty" data-role-type="member">
                <td class="text-center font-mono text-muted extra-small row-number"></td>
                <td>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small px-2 py-1 mode-badge">
                        <i class="mdi mdi-account-outline me-0.5"></i> Faculty
                    </span>
                </td>
                <td>
                    <div class="name-cell-wrap">
                        <select class="form-select form-select-sm proponent-select2" style="width: 100%;">
                            <option value="">Search faculty member...</option>
                        </select>
                        <input type="hidden" name="proponents[${idx}][name]" class="prop-name" value="">
                        <input type="hidden" name="proponents[${idx}][employee_id]" class="prop-emp-id" value="">
                        <input type="hidden" name="proponents[${idx}][type]" class="prop-type" value="faculty">
                    </div>
                </td>
                <td>
                    <input type="text" name="proponents[${idx}][role]" class="form-control form-control-sm font-sans fw-medium prop-role" value="Member" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-row" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>
        `;
        const $row = $(rowHtml);
        container.append($row);
        initSelect2OnRow($row);
    });

    $('#btnAddManualRow').on('click', function() {
        const idx = nextIndex++;
        const rowHtml = `
            <tr class="proponent-row" data-index="${idx}" data-mode="manual" data-role-type="member">
                <td class="text-center font-mono text-muted extra-small row-number"></td>
                <td>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill extra-small px-2 py-1 mode-badge">
                        <i class="mdi mdi-account-group-outline me-0.5"></i> Custom
                    </span>
                </td>
                <td>
                    <input type="text" name="proponents[${idx}][name]" class="form-control form-control-sm font-sans fw-medium prop-name" placeholder="Enter proponent or student volunteer name..." required>
                    <input type="hidden" name="proponents[${idx}][employee_id]" class="prop-emp-id" value="">
                    <input type="hidden" name="proponents[${idx}][type]" class="prop-type" value="manual">
                </td>
                <td>
                    <input type="text" name="proponents[${idx}][role]" class="form-control form-control-sm font-sans fw-medium prop-role" value="Member" placeholder="e.g. Student Volunteer / Facilitator" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-row" title="Remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            </tr>
        `;
        const $row = $(rowHtml);
        container.append($row);
    });

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('.proponent-row').remove();
    });
});
</script>
@endpush
