@extends('layouts.dss.app')

@section('title', 'Register Institutional Employee — SNSU')

@section('styles')
<style>
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .form-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .designation-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        transition: all 0.2s ease;
    }
    .designation-row:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04);
    }
    .designation-row.is-primary-row {
        border-color: #93c5fd;
        background-color: #f0fdf4;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 12px;">
                    <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dss.settings.employees.index') }}" class="text-decoration-none text-muted">Employee Directory</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Register New Employee</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Register New Institutional Personnel</h3>
            <div class="text-muted extra-small mt-0.5">Creating master personnel record for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>.</div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.employees.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-2 text-dark d-flex align-items-center gap-1.5">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Back to Directory</span>
            </a>
        </div>
    </div>

    <!-- Main Registration Form -->
    <form method="POST" action="{{ route('dss.settings.employees.store') }}" id="createEmployeeForm">
        @csrf

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <!-- 1. Identity & Institutional Identification -->
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="mdi mdi-badge-account-horizontal text-primary fs-5"></i>
                        <span>1. Institutional Identification &amp; Contact</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark">Employee Number <span class="text-danger">*</span></label>
                            <input type="text" name="employee_no" class="form-control font-mono" value="{{ old('employee_no', $suggestedEmpNo) }}" required placeholder="e.g. EMP-2026-0001">
                            <div class="form-text extra-small">Unique institutional identifier.</div>
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-dark">Institutional / Official Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="officialEmailInput" class="form-control" value="{{ old('email') }}" required placeholder="e.g. employee@<?= $tenant->domain ?? 'adssu.edu.ph' ?>">
                            <div class="form-text extra-small">Used for official communications and default Google Workspace OAuth SSO.</div>
                        </div>

                        <!-- Personal Names -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="First name">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-dark">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="Middle name">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-dark">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="Last name">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-bold text-dark">Suffix</label>
                            <input type="text" name="suffix" class="form-control" value="{{ old('suffix') }}" placeholder="Jr., III, Ph.D.">
                        </div>

                        <!-- Gender, Phone, Date Hired -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark">Gender</label>
                            <select name="gender" class="form-select" data-no-select2="true">
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other / Prefer not to say</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark">Contact Phone Number</label>
                            <input type="text" name="phone_number" class="form-control font-mono" value="{{ old('phone_number') }}" placeholder="e.g. 09171234567">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark">Date of Assumption / Hired</label>
                            <input type="date" name="date_hired" class="form-control" value="{{ old('date_hired', date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <!-- 2. Institutional Offices & Multiple Designations -->
                <div class="form-card">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div class="form-card-title mb-0 border-bottom-0 pb-0">
                            <i class="mdi mdi-office-building text-primary fs-5"></i>
                            <span>2. Institutional Offices &amp; Designations</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold d-flex align-items-center gap-1.5" onclick="addDesignationRow()">
                            <i class="mdi mdi-plus-circle"></i>
                            <span>Add Concurrent Position</span>
                        </button>
                    </div>

                    <div class="alert alert-info border-info-subtle py-2 px-3 mb-3 d-flex align-items-start gap-2 extra-small">
                        <i class="mdi mdi-information text-info fs-6 flex-shrink-0 mt-0.5"></i>
                        <div>
                            Select from the <strong>Master Designations Repository</strong> using Select2. An employee can have multiple positions across different campuses and offices (e.g. <em>Programmer in ICT</em>, <em>Faculty in CCIS</em>). Toggle <strong>Primary</strong> for their mother unit.
                        </div>
                    </div>

                    <!-- Designations Rows Container -->
                    <div id="designationsContainer" class="d-flex flex-column gap-3">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Hidden fields for backward compatibility -->
                    <input type="hidden" name="campus_id" id="hiddenCampusId" value="">
                    <input type="hidden" name="department_id" id="hiddenDeptId" value="">
                    <input type="hidden" name="designation_id" id="hiddenDesigId" value="">
                    <input type="hidden" name="designation" id="hiddenDesignation" value="Staff Member">
                </div>
            </div>

            <!-- Right Column: Employment Classification & System Account -->
            <div class="col-12 col-lg-4">
                <!-- Employment Profile Card -->
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="mdi mdi-briefcase-check text-success fs-5"></i>
                        <span>3. Employment Classification</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Employment Type <span class="text-danger">*</span></label>
                        <select name="employment_type" class="form-select" required data-no-select2="true">
                            <option value="Permanent" {{ old('employment_type') === 'Permanent' ? 'selected' : '' }}>Permanent (Regular / Plantilla)</option>
                            <option value="Contractual" {{ old('employment_type') === 'Contractual' ? 'selected' : '' }}>Contractual (COS)</option>
                            <option value="Temporary" {{ old('employment_type') === 'Temporary' ? 'selected' : '' }}>Temporary</option>
                            <option value="Casual" {{ old('employment_type') === 'Casual' ? 'selected' : '' }}>Casual</option>
                            <option value="Job Order" {{ old('employment_type') === 'Job Order' ? 'selected' : '' }}>Job Order (JO)</option>
                            <option value="Part-time" {{ old('employment_type') === 'Part-time' ? 'selected' : '' }}>Part-time / Visiting Lecturer</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Employment Status <span class="text-danger">*</span></label>
                        <select name="employment_status" class="form-select" required data-no-select2="true">
                            <option value="Active" {{ old('employment_status', 'Active') === 'Active' ? 'selected' : '' }}>Active (In Active Service)</option>
                            <option value="On Leave" {{ old('employment_status') === 'On Leave' ? 'selected' : '' }}>On Leave (Study/Sabbatical/Sick)</option>
                            <option value="Inactive" {{ old('employment_status') === 'Inactive' ? 'selected' : '' }}>Inactive / Separated</option>
                        </select>
                    </div>
                </div>

                <!-- System User Login Account Card -->
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="mdi mdi-account-key text-primary fs-5"></i>
                        <span>4. System User Account (0..1)</span>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="createUserSwitch" name="create_user_account" value="1" {{ old('create_user_account') ? 'checked' : '' }} onchange="toggleUserAccountFields(this.checked)">
                        <label class="form-check-label fw-bold text-dark small" for="createUserSwitch">
                            Provision System Login Account Immediately
                        </label>
                    </div>

                    <div class="extra-small text-muted mb-3" style="line-height: 1.4;">
                        Institutional personnel do not require a login account to exist. If enabled, this employee will be granted system access ready for <strong>Google Workspace 1-Click SSO</strong>.
                    </div>

                    <div id="userAccountSection" style="display: {{ old('create_user_account') ? 'block' : 'none' }};" class="p-3 bg-light rounded-3 border">
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-dark">Google Workspace Login Email <span class="text-danger">*</span></label>
                            <input type="email" name="user_email" id="userEmailInput" class="form-control form-control-sm font-mono" value="{{ old('user_email') }}" placeholder="e.g. employee@adssu.edu.ph">
                            <div class="form-text extra-small">Must match their institutional Google email.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-dark">Initial System Role <span class="text-danger">*</span></label>
                            <select name="user_role" class="form-select form-select-sm" data-no-select2="true">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('user_role', 'Staff') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label extra-small fw-bold text-dark">Initial Password (Optional)</label>
                            <input type="password" name="user_password" class="form-control form-control-sm" placeholder="Leave blank to generate random key">
                        </div>
                    </div>
                </div>

                <!-- Submit / Cancel Card -->
                <div class="form-card bg-light border">
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-success fw-bold py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="mdi mdi-check-circle-outline fs-5"></i>
                            <span>Register Institutional Employee</span>
                        </button>
                        <a href="{{ route('dss.settings.employees.index') }}" class="btn btn-white border fw-semibold py-2 text-dark text-center">
                            Cancel &amp; Return
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const campusesData = @json($campuses);
    const departmentsData = @json($departments);
    const designationsData = @json($designations);

    $(document).ready(function() {
        // Initialize with 1 default primary designation row
        addDesignationRow();

        // Auto-sync email input if official email is typed
        $('#officialEmailInput').on('input', function() {
            const val = $(this).val();
            const userEmail = $('#userEmailInput');
            if (userEmail.length && !userEmail.data('customized')) {
                userEmail.val(val);
            }
        });

        $('#userEmailInput').on('input', function() {
            $(this).data('customized', true);
        });
    });

    function toggleUserAccountFields(checked) {
        const sec = document.getElementById('userAccountSection');
        if (checked) {
            sec.style.display = 'block';
            const official = document.getElementById('officialEmailInput').value;
            const uInput = document.getElementById('userEmailInput');
            if (official && !uInput.value) {
                uInput.value = official;
            }
        } else {
            sec.style.display = 'none';
        }
    }

    // ========================================================
    // DYNAMIC DESIGNATION & OFFICE ROWS (WITH SELECT2)
    // ========================================================
    function addDesignationRow(data = null) {
        const container = document.getElementById('designationsContainer');
        if (!container) return;

        const index = container.querySelectorAll('.designation-row').length;
        const isFirst = index === 0;
        const isPri = data ? Boolean(data.is_primary) : isFirst;

        let campusOptions = '<option value="">Select Campus...</option>';
        campusesData.forEach(c => {
            const sel = (data && data.campus_id == c.id) ? 'selected' : '';
            campusOptions += `<option value="${c.id}" ${sel}>${c.campus_name}</option>`;
        });

        let deptOptions = '<option value="">Select Office / Unit...</option>';
        departmentsData.forEach(d => {
            const sel = (data && data.department_id == d.id) ? 'selected' : '';
            deptOptions += `<option value="${d.id}" ${sel}>${d.unit_name}</option>`;
        });

        let desigOptions = '<option value="">Select Position / Designation...</option>';
        designationsData.forEach(des => {
            const sel = (data && (data.designation_id == des.id || data.designation == des.name)) ? 'selected' : '';
            desigOptions += `<option value="${des.id}" data-name="${des.name}" ${sel}>${des.name} [${des.category}]</option>`;
        });

        const row = document.createElement('div');
        row.className = `designation-row ${isPri ? 'is-primary-row' : ''} position-relative`;
        row.id = `desig-row-item-${index}`;

        row.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${isPri ? 'bg-primary' : 'bg-secondary'} px-2 py-0.5 font-mono primary-status-badge" style="font-size: 10px;">
                        ${isPri ? 'MOTHER UNIT / PRIMARY' : 'CONCURRENT APPOINTMENT'}
                    </span>
                    <span class="extra-small text-muted">Position #${index + 1}</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input desig-primary-radio" type="radio" name="primary_designation_radio" id="pri_radio_${index}" value="${index}" ${isPri ? 'checked' : ''} onchange="onPrimaryRadioChanged(${index})">
                        <label class="form-check-label extra-small fw-bold text-dark cursor-pointer" for="pri_radio_${index}">Set as Primary</label>
                    </div>
                    <input type="hidden" name="designations[${index}][is_primary]" value="${isPri ? '1' : '0'}" class="is-primary-hidden">

                    ${!isFirst ? `
                        <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2 text-danger" onclick="removeDesignationRow(this)" title="Remove this concurrent appointment">
                            <i class="mdi mdi-close"></i> Remove
                        </button>
                    ` : ''}
                </div>
            </div>

            <div class="row g-2">
                <!-- Designation Title with Select2 -->
                <div class="col-12 col-md-5">
                    <label class="form-label extra-small fw-bold text-dark mb-1">Designation Title / Position <span class="text-danger">*</span></label>
                    <select name="designations[${index}][designation_id]" class="form-select form-select-sm desig-select2" required>
                        ${desigOptions}
                    </select>
                    <input type="hidden" name="designations[${index}][designation]" class="desig-name-hidden" value="${data ? (data.designation || '') : ''}">
                </div>

                <!-- Office / Department -->
                <div class="col-12 col-md-4">
                    <label class="form-label extra-small fw-bold text-dark mb-1">Office / Department / Unit <span class="text-danger">*</span></label>
                    <select name="designations[${index}][department_id]" class="form-select form-select-sm desig-dept-select" required data-no-select2="true">
                        ${deptOptions}
                    </select>
                </div>

                <!-- Campus -->
                <div class="col-12 col-md-3">
                    <label class="form-label extra-small fw-bold text-dark mb-1">Campus</label>
                    <select name="designations[${index}][campus_id]" class="form-select form-select-sm desig-campus-select" data-no-select2="true">
                        ${campusOptions}
                    </select>
                </div>
            </div>
        `;

        container.appendChild(row);

        // Initialize Select2 on the designation dropdown with tagging support
        const $select2El = $(row).find('.desig-select2');
        $select2El.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Choose or type designation...',
            tags: true, // Allows typing custom position name if not in master list!
            allowClear: false
        }).on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            const selectedVal = $(this).val();
            const cleanText = $(this).find('option:selected').data('name') || selectedText.split(' [')[0];
            $(row).find('.desig-name-hidden').val(cleanText);
            syncPrimaryHiddenFields();
        });

        syncPrimaryHiddenFields();
    }

    function removeDesignationRow(btn) {
        const row = btn.closest('.designation-row');
        if (row) {
            row.remove();
            reindexDesignationRows();
            syncPrimaryHiddenFields();
        }
    }

    function onPrimaryRadioChanged(checkedIndex) {
        const container = document.getElementById('designationsContainer');
        const rows = container.querySelectorAll('.designation-row');
        rows.forEach((r, idx) => {
            const isChecked = (idx === checkedIndex);
            r.classList.toggle('is-primary-row', isChecked);
            const badge = r.querySelector('.primary-status-badge');
            if (badge) {
                badge.className = `badge ${isChecked ? 'bg-primary' : 'bg-secondary'} px-2 py-0.5 font-mono primary-status-badge`;
                badge.textContent = isChecked ? 'MOTHER UNIT / PRIMARY' : 'CONCURRENT APPOINTMENT';
            }
            const hidden = r.querySelector('.is-primary-hidden');
            if (hidden) hidden.value = isChecked ? '1' : '0';
        });

        syncPrimaryHiddenFields();
    }

    function reindexDesignationRows() {
        const container = document.getElementById('designationsContainer');
        const rows = container.querySelectorAll('.designation-row');
        let hasChecked = false;

        rows.forEach((r, idx) => {
            r.id = `desig-row-item-${idx}`;
            r.querySelector('.desig-select2').setAttribute('name', `designations[${idx}][designation_id]`);
            r.querySelector('.desig-name-hidden').setAttribute('name', `designations[${idx}][designation]`);
            r.querySelector('.desig-dept-select').setAttribute('name', `designations[${idx}][department_id]`);
            r.querySelector('.desig-campus-select').setAttribute('name', `designations[${idx}][campus_id]`);
            
            const radio = r.querySelector('.desig-primary-radio');
            radio.value = idx;
            radio.id = `pri_radio_${idx}`;
            r.querySelector('label[for^="pri_radio_"]').setAttribute('for', `pri_radio_${idx}`);
            radio.setAttribute('onchange', `onPrimaryRadioChanged(${idx})`);

            const hidden = r.querySelector('.is-primary-hidden');
            hidden.setAttribute('name', `designations[${idx}][is_primary]`);

            if (radio.checked) hasChecked = true;
        });

        if (!hasChecked && rows.length > 0) {
            const firstRadio = rows[0].querySelector('.desig-primary-radio');
            if (firstRadio) {
                firstRadio.checked = true;
                onPrimaryRadioChanged(0);
            }
        }
    }

    function syncPrimaryHiddenFields() {
        const container = document.getElementById('designationsContainer');
        if (!container) return;

        const rows = container.querySelectorAll('.designation-row');
        let priRow = null;

        rows.forEach(r => {
            const radio = r.querySelector('.desig-primary-radio');
            if (radio && radio.checked) {
                priRow = r;
            }
        });

        if (!priRow && rows.length > 0) {
            priRow = rows[0];
        }

        if (priRow) {
            const desigId = $(priRow).find('.desig-select2').val() || '';
            const desigName = $(priRow).find('.desig-name-hidden').val() || $(priRow).find('.desig-select2 option:selected').text() || 'Staff Member';
            const deptId = $(priRow).find('.desig-dept-select').val() || '';
            const campusId = $(priRow).find('.desig-campus-select').val() || '';

            $('#hiddenDesigId').val(desigId);
            $('#hiddenDesignation').val(desigName.split(' [')[0]);
            $('#hiddenDeptId').val(deptId);
            $('#hiddenCampusId').val(campusId);
        }
    }
</script>
@endsection
