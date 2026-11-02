@extends('layouts.dss.app')

@section('title', 'Edit Extension Plan: ' . $plan->plan_code . ' — RIE System')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .plan-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .plan-card-header {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .milestone-row {
        transition: background 0.15s ease;
    }
    .milestone-row:hover {
        background: #f8fafc;
    }
    .milestone-num-badge {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        background: #eff6ff;
        color: #2563eb;
    }
    .flatpickr-calendar {
        box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Header Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb m-0 extra-small">
                    <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">Extension Projects</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="text-decoration-none text-muted">{{ $plan->plan_code }}</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Edit Details</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-file-document-edit-outline text-primary"></i>
                <span>Edit Extension Plan: {{ $plan->plan_code }}</span>
                <span class="badge rounded-pill bg-light text-dark border extra-small px-2 py-1 font-mono">{{ $plan->status_badge['label'] ?? strtoupper($plan->status) }}</span>
            </h4>
            <div class="text-muted small mt-0.5">
                Update project activities, objectives, schedule milestones, and beneficiaries.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Plan Workbench
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-2xs mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-alert-circle fs-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="d-block mb-1">Please review the following errors:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('dss.rie.plans.update', $plan->getRouteKey()) }}" method="POST" id="extensionPlanForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="action_type" id="actionTypeInput" value="draft">

        <div class="row g-4">
            <!-- Left Column: Core Document Inputs -->
            <div class="col-lg-8">
                <!-- 1. Project Information -->
                <div class="plan-card">
                    <div class="plan-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-information-outline text-primary fs-5"></i>
                            <h6 class="fw-bold m-0 text-dark">Project Information</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Academic Program Selection -->
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                Academic Program <span class="text-danger">*</span>
                            </label>
                            <select name="academic_program_id" id="academicProgramSelect" class="form-select select2-ajax" required>
                                <option value="">-- Select Degree Program --</option>
                                @foreach($academicPrograms as $prog)
                                    @php
                                        $campusName = $prog->campus?->campus_name ?: 'Main Campus';
                                        $label = "{$campusName} — {$prog->program_code} ({$prog->program_name})";
                                    @endphp
                                    <option value="{{ $prog->id }}"
                                            data-program-name="{{ $label }}"
                                            {{ (string) old('academic_program_id', $plan->academic_program_id) === (string) $prog->id ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="program_name" id="programNameInput" value="{{ old('program_name', $plan->program_name) }}">
                        </div>

                        <!-- Extension Activities / Project Title -->
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                Extension Activity / Project Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="extension_activity" class="form-control form-control-lg fw-bold text-dark fs-6"
                                   placeholder="e.g. PROJECT KAON: Livelihood &amp; Nutrition Literacy Campaign"
                                   value="{{ old('extension_activity', $plan->extension_activity) }}" required>
                        </div>

                        <!-- Objectives -->
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                Objectives <span class="text-danger">*</span>
                            </label>
                            <textarea name="objectives" rows="4" class="form-control"
                                      placeholder="List the specific objectives of the extension plan..." required>{{ old('objectives', $plan->objectives) }}</textarea>
                        </div>

                        <!-- Optional DTS Tracking Number -->
                        <div>
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                DTS Tracking Number <span class="text-muted fw-normal lowercase">(Optional — can be linked later)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="mdi mdi-barcode-scan"></i></span>
                                <input type="text" name="tracking_number" class="form-control font-mono"
                                       placeholder="e.g. SNSU-2026-00123"
                                       value="{{ old('tracking_number', $plan->tracking_number) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Beneficiaries & Budget Allocation -->
                <div class="plan-card">
                    <div class="plan-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-account-group-outline text-primary fs-5"></i>
                            <h6 class="fw-bold m-0 text-dark">Beneficiaries &amp; Budget Allocation</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Beneficiaries Description -->
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                Beneficiaries Description <span class="text-danger">*</span>
                            </label>
                            <textarea name="beneficiaries" rows="2" class="form-control"
                                      placeholder="e.g. Daycare Workers and Barangay Health Workers of Surigao City" required>{{ old('beneficiaries', $plan->beneficiaries) }}</textarea>
                        </div>

                        <div class="row g-3">
                            <!-- Target Number of Beneficiaries -->
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                    Target Beneficiaries <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-multiple"></i></span>
                                    <input type="number" name="target_beneficiaries_count" class="form-control font-semibold" min="1"
                                           placeholder="e.g. 50" value="{{ old('target_beneficiaries_count', $plan->target_beneficiaries_count) }}" required>
                                </div>
                            </div>

                            <!-- Proposed Budget -->
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                    Proposed Budget <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-dark">₱</span>
                                    <input type="number" step="0.01" name="proposed_budget" class="form-control font-mono fw-bold"
                                           placeholder="e.g. 80000.00" value="{{ old('proposed_budget', $plan->proposed_budget) }}" required>
                                </div>
                            </div>

                            <!-- Number of Extensionists -->
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase">
                                    Extensionists Count <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-tie"></i></span>
                                    <input type="number" name="extensionists_count" class="form-control font-semibold" min="1"
                                           placeholder="e.g. 15" value="{{ old('extensionists_count', $plan->extensionists_count) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Schedule of Activities (Dynamic Milestones) -->
                <div class="plan-card">
                    <div class="plan-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-calendar-clock text-primary fs-5"></i>
                            <div>
                                <h6 class="fw-bold m-0 text-dark">Schedule of Activities &amp; Milestones</h6>
                                <div class="text-muted extra-small">Customize the project milestone sequence, dates, and execution timeline.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-2xs" onclick="addMilestoneRow()">
                                <i class="mdi mdi-plus me-1"></i> Add Milestone
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 extra-small" onclick="resetToDefaultMilestones()" title="Reset to standard milestones">
                                Reset Defaults
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="milestonesTable">
                                <thead class="table-light extra-small text-uppercase text-muted">
                                    <tr>
                                        <th style="width: 44px;" class="text-center">#</th>
                                        <th style="min-width: 260px;">Milestone Stage / Description</th>
                                        <th style="width: 320px;">Target Period / Date</th>
                                        <th style="width: 80px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="milestonesTbody">
                                    <!-- Dynamic rows injected via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top bg-light-subtle d-flex align-items-center justify-content-between">
                            <span class="extra-small text-muted" id="milestoneCountText">0 milestones configured</span>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5" onclick="addMilestoneRow()">
                                <i class="mdi mdi-plus me-1"></i> Add Another Row
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Submission Actions & Information -->
            <div class="col-lg-4">
                <!-- Action Submission Card -->
                <div class="plan-card sticky-top" style="top: 20px;">
                    <div class="plan-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-send-check-outline text-primary fs-5"></i>
                            <h6 class="fw-bold m-0 text-dark">Plan Actions</h6>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column gap-2">
                        <button type="button" class="btn btn-warning btn-lg fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm text-dark rounded-pill"
                                onclick="submitPlan('submit')">
                            <i class="mdi mdi-send-check fs-5"></i>
                            <span>Save &amp; Submit for Notation</span>
                        </button>

                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 rounded-pill"
                                onclick="submitPlan('draft')">
                            <i class="mdi mdi-content-save-outline"></i>
                            <span>Update &amp; Keep as Draft</span>
                        </button>

                        <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 rounded-pill">
                            <i class="mdi mdi-close"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </div>

                <!-- Guidance Information Card -->
                <div class="plan-card">
                    <div class="plan-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-information-outline text-muted fs-5"></i>
                            <h6 class="fw-semibold m-0 text-secondary extra-small text-uppercase">Workflow Information</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <ul class="extra-small text-muted ps-3 mb-0 d-flex flex-column gap-2">
                            <li><strong>Dynamic Milestones:</strong> All configured milestone activities and target dates are automatically saved into the database and displayed in the official PDF print.</li>
                            <li><strong>Automated Routing:</strong> Official institutional signatories and endorsement tiers are automatically resolved via the Document Signatory Matrix.</li>
                        </ul>
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
    // Standard default institutional milestones
    const defaultMilestones = [
        { title: 'Submission of Extension Project Proposal', target_date: 'March 15, 2026' },
        { title: 'Submission of Activity Design', target_date: 'April 05, 2026' },
        { title: 'Submission of Purchase Request (Optional)', target_date: 'N/A' },
        { title: 'MOA Signing', target_date: 'April 20, 2026' },
        { title: 'Arrival of Materials', target_date: 'May 05, 2026' },
        { title: 'Conduct of Extension Activity', target_date: 'May 10 – May 12, 2026' },
        { title: 'Submission of Narrative Reports, Attendance & Evaluation', target_date: 'May 20, 2026' },
        { title: 'Conduct of Monitoring/Feedback', target_date: 'June 05, 2026' }
    ];

    // Load existing milestones from plan or old input
    const initialMilestones = @json(old('milestones', $plan->milestones_list));
    let currentMilestones = (initialMilestones && initialMilestones.length > 0)
        ? JSON.parse(JSON.stringify(initialMilestones))
        : JSON.parse(JSON.stringify(defaultMilestones));

    const fpMap = {};

    function getDateIcon(val) {
        val = (val || '').trim();
        if (!val) return 'mdi-calendar text-muted';
        if (val.toUpperCase() === 'N/A') return 'mdi-cancel text-secondary';
        if (val.includes('–') || val.includes(' - ') || val.includes(' to ')) return 'mdi-calendar-range text-success';
        return 'mdi-calendar-check text-primary';
    }

    function updateDateIcon(idx) {
        const input = document.getElementById(`milestone_date_${idx}`);
        const icon = document.getElementById(`date_mode_icon_${idx}`);
        if (input && icon) {
            icon.className = `mdi ${getDateIcon(input.value)}`;
        }
    }

    function setRowDateMode(idx, mode) {
        const input = document.getElementById(`milestone_date_${idx}`);
        if (!input) return;

        if (fpMap[idx]) {
            fpMap[idx].destroy();
            delete fpMap[idx];
        }

        if (mode === 'custom') {
            updateDateIcon(idx);
            input.focus();
            return;
        }

        fpMap[idx] = flatpickr(input, {
            mode: mode,
            dateFormat: 'F d, Y',
            conjunction: ' – ',
            allowInput: true,
            onChange: function(selectedDates) {
                if (mode === 'range' && selectedDates.length === 2) {
                    const d1 = selectedDates[0];
                    const d2 = selectedDates[1];
                    const optMonth = { month: 'long', day: 'numeric', year: 'numeric' };
                    input.value = `${d1.toLocaleDateString('en-US', optMonth)} – ${d2.toLocaleDateString('en-US', optMonth)}`;
                }
                updateDateIcon(idx);
            },
            onClose: function() {
                updateDateIcon(idx);
            }
        });

        fpMap[idx].open();
    }

    function openDatePicker(idx) {
        const input = document.getElementById(`milestone_date_${idx}`);
        if (!input) return;

        if (!fpMap[idx]) {
            const val = input.value.trim();
            const mode = (val.includes('–') || val.includes(' - ') || val.includes(' to ')) ? 'range' : 'single';
            setRowDateMode(idx, mode);
        } else {
            fpMap[idx].open();
        }
    }

    function applyPreset(idx, presetVal) {
        const input = document.getElementById(`milestone_date_${idx}`);
        if (input) {
            input.value = presetVal;
            updateDateIcon(idx);
            if (fpMap[idx]) {
                fpMap[idx].destroy();
                delete fpMap[idx];
            }
        }
    }

    function quickSetNa(idx) {
        const input = document.getElementById(`milestone_date_${idx}`);
        if (input) {
            input.value = (input.value.trim().toUpperCase() === 'N/A') ? '' : 'N/A';
            updateDateIcon(idx);
            if (fpMap[idx]) {
                fpMap[idx].destroy();
                delete fpMap[idx];
            }
        }
    }

    function renderMilestones() {
        // Clear previous Flatpickr instances
        Object.keys(fpMap).forEach(key => {
            if (fpMap[key]) {
                fpMap[key].destroy();
                delete fpMap[key];
            }
        });

        const tbody = document.getElementById('milestonesTbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        currentMilestones.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.className = 'milestone-row';
            tr.innerHTML = `
                <td class="text-center">
                    <span class="milestone-num-badge">${index + 1}</span>
                </td>
                <td>
                    <input type="text"
                           name="milestones[${index}][title]"
                           class="form-control form-control-sm fw-semibold text-dark"
                           placeholder="Enter milestone title / phase..."
                           value="${escapeHtml(item.title || '')}"
                           required>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <button class="btn btn-light border dropdown-toggle px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Format: Specific Date, Date Range, or Custom Period">
                            <i class="mdi ${getDateIcon(item.target_date)}" id="date_mode_icon_${index}"></i>
                        </button>
                        <ul class="dropdown-menu shadow-sm extra-small">
                            <li class="dropdown-header text-muted extra-small py-1">Choose Date Format</li>
                            <li><a class="dropdown-item py-1.5 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setRowDateMode(${index}, 'single')"><i class="mdi mdi-calendar text-primary"></i><span>Specific Single Date</span></a></li>
                            <li><a class="dropdown-item py-1.5 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setRowDateMode(${index}, 'range')"><i class="mdi mdi-calendar-range text-success"></i><span>Date Range (Start – End)</span></a></li>
                            <li><a class="dropdown-item py-1.5 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="setRowDateMode(${index}, 'custom')"><i class="mdi mdi-form-textbox text-info"></i><span>Custom Period / Quarter</span></a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li class="dropdown-header text-muted extra-small py-1">Quick Presets</li>
                            <li><a class="dropdown-item py-1 text-secondary" href="javascript:void(0)" onclick="applyPreset(${index}, 'N/A')"><i class="mdi mdi-cancel me-1.5"></i> Mark as N/A</a></li>
                            <li><a class="dropdown-item py-1 text-secondary" href="javascript:void(0)" onclick="applyPreset(${index}, '1st Quarter ' + new Date().getFullYear())"><i class="mdi mdi-calendar-clock me-1.5"></i> 1st Quarter</a></li>
                            <li><a class="dropdown-item py-1 text-secondary" href="javascript:void(0)" onclick="applyPreset(${index}, '2nd Quarter ' + new Date().getFullYear())"><i class="mdi mdi-calendar-clock me-1.5"></i> 2nd Quarter</a></li>
                            <li><a class="dropdown-item py-1 text-secondary" href="javascript:void(0)" onclick="applyPreset(${index}, 'To be scheduled')"><i class="mdi mdi-clock-outline me-1.5"></i> To be scheduled</a></li>
                        </ul>
                        <input type="text"
                               name="milestones[${index}][target_date]"
                               id="milestone_date_${index}"
                               class="form-control form-control-sm milestone-date-input font-mono bg-white"
                               placeholder="Pick date, range, or type custom..."
                               value="${escapeHtml(item.target_date || '')}"
                               oninput="updateDateIcon(${index})">
                        <button type="button" class="btn btn-light border text-primary px-2" onclick="openDatePicker(${index})" title="Open Calendar">
                            <i class="mdi mdi-calendar-month-outline"></i>
                        </button>
                        <button type="button" class="btn btn-light border extra-small px-2 text-muted fw-bold" onclick="quickSetNa(${index})" title="Toggle N/A">
                            N/A
                        </button>
                    </div>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light btn-sm text-secondary px-1" onclick="moveMilestone(${index}, -1)" ${index === 0 ? 'disabled' : ''} title="Move Up">
                            <i class="mdi mdi-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-light btn-sm text-secondary px-1" onclick="moveMilestone(${index}, 1)" ${index === currentMilestones.length - 1 ? 'disabled' : ''} title="Move Down">
                            <i class="mdi mdi-arrow-down"></i>
                        </button>
                        <button type="button" class="btn btn-light btn-sm text-danger px-1" onclick="removeMilestone(${index})" title="Remove">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Initialize flatpickr on date inputs
        currentMilestones.forEach((item, index) => {
            const val = (item.target_date || '').trim();
            if (val && val.toUpperCase() !== 'N/A' && !val.toLowerCase().includes('quarter') && !val.toLowerCase().includes('scheduled')) {
                const mode = (val.includes('–') || val.includes(' - ') || val.includes(' to ')) ? 'range' : 'single';
                const input = document.getElementById(`milestone_date_${index}`);
                if (input && window.flatpickr) {
                    fpMap[index] = flatpickr(input, {
                        mode: mode,
                        dateFormat: 'F d, Y',
                        conjunction: ' – ',
                        allowInput: true,
                        onChange: function(selectedDates) {
                            if (mode === 'range' && selectedDates.length === 2) {
                                const d1 = selectedDates[0];
                                const d2 = selectedDates[1];
                                const optMonth = { month: 'long', day: 'numeric', year: 'numeric' };
                                input.value = `${d1.toLocaleDateString('en-US', optMonth)} – ${d2.toLocaleDateString('en-US', optMonth)}`;
                            }
                            updateDateIcon(index);
                        },
                        onClose: function() {
                            updateDateIcon(index);
                        }
                    });
                }
            }
        });

        // Update count text
        const countText = document.getElementById('milestoneCountText');
        if (countText) {
            countText.innerText = `${currentMilestones.length} milestone${currentMilestones.length === 1 ? '' : 's'} configured`;
        }
    }

    function addMilestoneRow() {
        syncCurrentFromDom();
        currentMilestones.push({
            title: '',
            target_date: ''
        });
        renderMilestones();
        // Focus the newly added row title input
        const inputs = document.querySelectorAll('#milestonesTbody input[name$="[title]"]');
        if (inputs.length) {
            inputs[inputs.length - 1].focus();
        }
    }

    function removeMilestone(idx) {
        syncCurrentFromDom();
        if (currentMilestones.length <= 1) {
            alert('At least one milestone is required.');
            return;
        }
        currentMilestones.splice(idx, 1);
        renderMilestones();
    }

    function moveMilestone(idx, direction) {
        syncCurrentFromDom();
        const targetIdx = idx + direction;
        if (targetIdx < 0 || targetIdx >= currentMilestones.length) return;

        const temp = currentMilestones[idx];
        currentMilestones[idx] = currentMilestones[targetIdx];
        currentMilestones[targetIdx] = temp;
        renderMilestones();
    }

    function resetToDefaultMilestones() {
        if (confirm('Reset milestones to standard institutional defaults? Any custom items will be replaced.')) {
            currentMilestones = JSON.parse(JSON.stringify(defaultMilestones));
            renderMilestones();
        }
    }

    function syncCurrentFromDom() {
        const rows = document.querySelectorAll('#milestonesTbody tr');
        currentMilestones = [];
        rows.forEach(r => {
            const titleInput = r.querySelector('input[name$="[title]"]');
            const dateInput = r.querySelector('input[name$="[target_date]"]');
            if (titleInput) {
                currentMilestones.push({
                    title: titleInput.value,
                    target_date: dateInput ? dateInput.value : ''
                });
            }
        });
    }

    function escapeHtml(str) {
        return (str || '').replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }

    function submitPlan(actionType) {
        syncCurrentFromDom();
        document.getElementById('actionTypeInput').value = actionType;
        document.getElementById('extensionPlanForm').submit();
    }

    $(document).ready(function() {
        renderMilestones();

        // Select2 for Program
        if ($('#academicProgramSelect').length) {
            $('#academicProgramSelect').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Select Degree Program --',
                allowClear: false
            }).on('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    const progName = opt.getAttribute('data-program-name') || opt.text;
                    $('#programNameInput').val(progName);
                }
            });
        }
    });
</script>
@endpush
