@extends('layouts.dss.app')

@section('title', 'Personnel Directory — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Personnel Directory</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-mono" style="font-size: 11px;">
                    <i class="mdi mdi-shield-check me-1 text-primary"></i>SNSU Directory Administration
                </span>
            </div>
            <p class="text-muted small mb-0">
                Institutional personnel directory, organizational role assignments, and access permissions for <strong>{{ $tenant->school_name ?? $tenant->name ?? 'Surigao del Norte State University' }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border font-mono px-3 py-2">
                <i class="mdi mdi-account-group text-primary me-1"></i>{{ $users->count() }} Registered Personnel
            </span>
        </div>
    </div>

    <!-- Users DataTable Card -->
    <div class="dss-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutional Personnel &amp; Access Controls</h6>
                <div class="extra-small text-muted">Manage roles, direct capabilities, password resets, and account lifecycle status.</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="usersTable" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th style="width: 140px; white-space: nowrap;">Action</th>
                        <th>Personnel Profile</th>
                        <th>Assigned Role</th>
                        <th>Campus &amp; Department</th>
                        <th>Designation</th>
                        <th>Direct Permissions</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td style="white-space: nowrap;">
                                <div class="d-flex align-items-center gap-1">
                                    <!-- Quick Assign Button (Links to Dedicated Assignment Page) -->
                                    <a href="{{ route('dss.settings.users.assign_page', $user->id) }}"
                                       class="btn btn-sm btn-white border shadow-2xs text-dark fw-bold font-mono px-2 py-1 d-flex align-items-center gap-1"
                                       title="Assign Roles & Permissions">
                                        <i class="mdi mdi-shield-account text-primary"></i>
                                        <span>Assign</span>
                                    </a>

                                    <!-- Actions Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border px-2 py-1 text-muted shadow-2xs"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                title="More Operations">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start shadow-sm" style="font-size: 13px; min-width: 220px;">
                                            <li class="dropdown-header text-uppercase extra-small fw-bold text-muted">Governance &amp; Privileges</li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dss.settings.users.assign_page', [$user->id, 'tab' => 'roles']) }}">
                                                    <i class="mdi mdi-shield-account text-primary fs-6"></i>
                                                    <span>Assign Roles</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dss.settings.users.assign_page', [$user->id, 'tab' => 'permissions']) }}">
                                                    <i class="mdi mdi-shield-key-outline text-success fs-6"></i>
                                                    <span>Assign Permission</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="dropdown-header text-uppercase extra-small fw-bold text-muted">Security &amp; Credentials</li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2 text-primary" href="javascript:void(0)"
                                                   onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ addslashes($user->role ?: 'Unassigned') }}')">
                                                    <i class="mdi mdi-lock-reset fs-6"></i>
                                                    <span>Reset Password</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="dropdown-header text-uppercase extra-small fw-bold text-muted">Account Lifecycle</li>
                                            @php
                                                $normalizedStatus = strtolower($user->status ?? 'active');
                                            @endphp
                                            @if($normalizedStatus === 'active')
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-warning" href="javascript:void(0)"
                                                       onclick="confirmToggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', 'Inactive')">
                                                        <i class="mdi mdi-account-off-outline fs-6"></i>
                                                        <span>Set Inactive</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                       onclick="confirmToggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', 'Archived')">
                                                        <i class="mdi mdi-archive-arrow-down-outline fs-6"></i>
                                                        <span>Archived</span>
                                                    </a>
                                                </li>
                                            @elseif($normalizedStatus === 'inactive')
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-success" href="javascript:void(0)"
                                                       onclick="confirmToggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', 'Active')">
                                                        <i class="mdi mdi-account-check-outline fs-6"></i>
                                                        <span>Set Active</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                       onclick="confirmToggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', 'Archived')">
                                                        <i class="mdi mdi-archive-arrow-down-outline fs-6"></i>
                                                        <span>Archived</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-success" href="javascript:void(0)"
                                                       onclick="confirmToggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', 'Active')">
                                                        <i class="mdi mdi-account-check-outline fs-6"></i>
                                                        <span>Restore / Set Active</span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if($user->employee)
                                                <li><hr class="dropdown-divider"></li>
                                                <li class="dropdown-header text-uppercase extra-small fw-bold text-muted">Affiliation</li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dss.settings.employees.show', $user->employee->encrypted_id ?? $user->employee->id) }}">
                                                        <i class="mdi mdi-badge-account-horizontal-outline text-info fs-6"></i>
                                                        <span>View 360° Dossier</span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-dark border d-flex align-items-center justify-content-center fw-bold shadow-2xs" style="width: 38px; height: 38px; min-width: 38px; font-size: 13px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate d-flex align-items-center gap-1">
                                            <span>{{ $user->name }}</span>
                                            @if($user->employee)
                                                <span class="badge bg-light text-dark border font-mono" style="font-size: 10px;" title="Linked Institutional Employee Record">
                                                    {{ $user->employee->employee_no }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="extra-small text-muted font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleBadgeClass = match($user->role) {
                                        'SuperAdmin' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        'President', 'Director' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'Admin', 'Extension Staff', 'Extension Tracking Officer', 'Extension Budget Officer', 'Extension M&E Officer', 'Extension Document Custodian' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                        'Extension Coordinator', 'College Extension Head', 'Dean', 'UnitHead' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'Project Leader', 'Faculty' => 'bg-success-subtle text-success border border-success-subtle',
                                        'Evaluator', 'Auditor' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                        default => 'bg-secondary-subtle text-secondary border border-secondary-subtle'
                                    };
                                @endphp
                                <span class="badge {{ $roleBadgeClass }} font-mono px-2 py-1">
                                    <i class="mdi mdi-shield-account me-1"></i>{{ $user->role ?: 'Unassigned' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $user->campus?->campus_name ?? 'Main Campus' }}</div>
                                <div class="extra-small text-muted">{{ $user->department?->unit_name ?: ($user->department?->dept_name ?? 'General Administration') }}</div>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold">{{ $user->designation ?: 'Faculty / Staff Member' }}</span>
                            </td>
                            <td>
                                @php
                                    $directPermsCount = $user->permissions->count();
                                @endphp
                                @if($directPermsCount > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="{{ $user->permissions->pluck('name')->implode(', ') }}">
                                        <i class="mdi mdi-key-plus me-1"></i>{{ $directPermsCount }} Direct
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 font-mono">
                                        None (Role Only)
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusNorm = strtolower($user->status ?? 'active');
                                @endphp
                                @if($statusNorm === 'active')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 border border-success-subtle font-mono">Active</span>
                                @elseif($statusNorm === 'archived')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 border border-danger-subtle font-mono">Archived</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted px-2 py-1 border font-mono">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: RESET PASSWORD & OFFICIAL CREDENTIALS VOUCHER                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <!-- VIEW 1: Form View -->
            <div id="reset_password_view_form">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-lock-reset fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Reset User Password</h6>
                            <div class="extra-small text-muted">Generate temporary credentials and handover slip</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="reset_pwd_user_id">

                    <!-- Target User Info Banner -->
                    <div class="p-3 rounded bg-light border mb-3 d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <div class="fw-bold text-dark fs-6" id="reset_pwd_user_name">---</div>
                            <div class="extra-small text-muted font-mono" id="reset_pwd_user_email">---</div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono" id="reset_pwd_user_role">Role</span>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted mb-0">New Password</label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none extra-small fw-bold text-primary" onclick="generateAutoPassword()">
                                <i class="mdi mdi-autorenew me-1"></i>Auto Generate
                            </button>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="mdi mdi-key-outline text-muted"></i></span>
                            <input type="text" class="form-control font-mono" id="reset_pwd_input" placeholder="Leave empty or click Auto Generate">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyInputPassword('reset_pwd_input')" title="Copy to clipboard">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                        </div>
                        <div class="extra-small text-muted mt-1">
                            Click <strong>Auto Generate</strong> to create a secure temporary password (e.g. <code>7382KLM</code>).
                        </div>
                    </div>

                    <div class="alert alert-warning border py-2 px-3 mb-0 extra-small d-flex align-items-center gap-2">
                        <i class="mdi mdi-shield-alert-outline fs-5 text-warning flex-shrink-0"></i>
                        <div>
                            An official handover voucher will be generated immediately so you can print or provide credentials to the employee.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-xs" id="btn_submit_reset_pwd" onclick="submitResetPassword()">
                        <i class="mdi mdi-check me-1"></i>Confirm Reset &amp; Create Slip
                    </button>
                </div>
            </div>

            <!-- VIEW 2: Credentials Handover Slip Voucher -->
            <div id="reset_password_view_voucher" style="display: none;">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-check-decagram fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Password Reset Completed</h6>
                            <div class="extra-small text-muted">Credentials voucher issued &amp; ready for handover</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetPasswordModalClosed()"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Voucher Slip Visual Card -->
                    <div class="voucher-card-crm p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <div>
                                <span class="fw-bold text-dark" style="font-size: 13px;" id="voucher_institution_name">{{ $tenant->school_name ?? $tenant->name }}</span>
                                <div class="extra-small text-muted">CONFIDENTIAL SYSTEM CREDENTIALS VOUCHER</div>
                            </div>
                            <span class="badge bg-success text-white font-mono" style="font-size: 10px;">TEMPORARY ACCESS</span>
                        </div>

                        <div class="row g-2 mb-3" style="font-size: 12px;">
                            <div class="col-4 text-muted">Account Holder:</div>
                            <div class="col-8 fw-bold text-dark" id="voucher_name">---</div>

                            <div class="col-4 text-muted">Username / Email:</div>
                            <div class="col-8 font-mono fw-bold text-primary" id="voucher_username">---</div>

                            <div class="col-4 text-muted">Temporary Password:</div>
                            <div class="col-8 d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-warning font-mono fs-6 px-2 py-1" id="voucher_password">---</span>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" onclick="copyVoucherPassword()" title="Copy password">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                            </div>

                            <div class="col-4 text-muted">Assigned Role:</div>
                            <div class="col-8 font-mono" id="voucher_role">---</div>

                            <div class="col-4 text-muted">Issuance Timestamp:</div>
                            <div class="col-8 text-muted" id="voucher_date">---</div>
                        </div>

                        <div class="p-2 rounded bg-light border extra-small text-muted">
                            <i class="mdi mdi-shield-lock-outline me-1 text-secondary"></i>
                            <strong>Handover Notice:</strong> Please advise the employee to change their password upon initial logon via their Profile Settings.
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-50" onclick="copyAllVoucherDetails()">
                            <i class="mdi mdi-content-copy me-1"></i>Copy Details
                        </button>
                        <button type="button" class="btn btn-sm btn-success w-50 fw-bold" onclick="printVoucherSlip()">
                            <i class="mdi mdi-printer me-1"></i>Print Slip
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal" onclick="resetPasswordModalClosed()">
                        <i class="mdi mdi-check me-1"></i>Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>

<script>
    let usersDt;
    let currentVoucherData = null;

    $(document).ready(function() {
        // DataTables configured WITHOUT responsive collapse (responsive: false per specification)
        usersDt = $('#usersTable').DataTable({
            responsive: false,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search personnel...",
                lengthMenu: "Show _MENU_ records"
            },
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 5] }
            ]
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    /**
     * Password Reset & Voucher Functionality
     */
    function openResetPasswordModal(userId, userName, userEmail, userRole) {
        document.getElementById('reset_pwd_user_id').value = userId;
        document.getElementById('reset_pwd_user_name').innerText = userName;
        document.getElementById('reset_pwd_user_email').innerText = userEmail;
        document.getElementById('reset_pwd_user_role').innerText = userRole;

        // Auto-generate a suggested strong password
        generateAutoPassword();

        // Ensure form view is visible
        document.getElementById('reset_password_view_form').style.display = 'block';
        document.getElementById('reset_password_view_voucher').style.display = 'none';

        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }

    function generateAutoPassword() {
        const letters = "ABCDEFGHJKLMNPQRSTUVWXYZ";
        const digits = Math.floor(1000 + Math.random() * 9000);
        let randStr = "";
        for (let i = 0; i < 3; i++) {
            randStr += letters.charAt(Math.floor(Math.random() * letters.length));
        }
        document.getElementById('reset_pwd_input').value = `${digits}${randStr}`;
    }

    function copyInputPassword(inputId) {
        const val = document.getElementById(inputId).value;
        if (val) {
            if (typeof window.dssCopyToClipboard === 'function') {
                window.dssCopyToClipboard(val, 'Password copied to clipboard');
            } else {
                const ta = document.createElement('textarea');
                ta.value = val;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Password copied to clipboard',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    }

    function submitResetPassword() {
        const userId = document.getElementById('reset_pwd_user_id').value;
        const password = document.getElementById('reset_pwd_input').value.trim();
        const btn = document.getElementById('btn_submit_reset_pwd');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Resetting...';

        fetch(`/dss/settings/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                password: password
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-check me-1"></i>Confirm Reset & Create Slip';

            if (data.result && data.credentials) {
                currentVoucherData = data.credentials;

                // Populate Voucher Slip View
                document.getElementById('voucher_name').innerText = data.credentials.name;
                document.getElementById('voucher_username').innerText = data.credentials.username;
                document.getElementById('voucher_password').innerText = data.credentials.password;
                document.getElementById('voucher_role').innerText = data.credentials.role;
                document.getElementById('voucher_date').innerText = data.credentials.issued_at;
                if (data.credentials.institution) {
                    document.getElementById('voucher_institution_name').innerText = data.credentials.institution;
                }

                // Switch to Voucher view
                document.getElementById('reset_password_view_form').style.display = 'none';
                document.getElementById('reset_password_view_voucher').style.display = 'block';
            } else {
                Swal.fire('Reset Failed', data.message || 'Unable to reset password.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-check me-1"></i>Confirm Reset & Create Slip';
            Swal.fire('Error', 'An unexpected error occurred while resetting password.', 'error');
        });
    }

    function resetPasswordModalClosed() {
        // Reload page to reflect updated state if necessary
        location.reload();
    }

    function copyVoucherPassword() {
        if (currentVoucherData && currentVoucherData.password) {
            if (typeof window.dssCopyToClipboard === 'function') {
                window.dssCopyToClipboard(currentVoucherData.password, 'Temporary password copied');
            } else {
                const ta = document.createElement('textarea');
                ta.value = currentVoucherData.password;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Temporary password copied',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }
    }

    function copyAllVoucherDetails() {
        if (!currentVoucherData) return;
        const text = `--- SNSU EXTENSION SYSTEM CREDENTIALS HANDOVER SLIP ---\n` +
                     `Institution: ${currentVoucherData.institution || 'Surigao del Norte State University'}\n` +
                     `Personnel Name: ${currentVoucherData.name}\n` +
                     `Login Email/Username: ${currentVoucherData.username}\n` +
                     `Temporary Password: ${currentVoucherData.password}\n` +
                     `Assigned Role: ${currentVoucherData.role}\n` +
                     `Issued Date: ${currentVoucherData.issued_at}\n` +
                     `Note: Please change your password upon first login.`;
        if (typeof window.dssCopyToClipboard === 'function') {
            window.dssCopyToClipboard(text, 'Full credentials copied');
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Full credentials copied',
                showConfirmButton: false,
                timer: 1800
            });
        }
    }

    function printVoucherSlip() {
        if (!currentVoucherData) return;

        let printFrame = document.getElementById('hidden_print_voucher_frame');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'hidden_print_voucher_frame';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            document.body.appendChild(printFrame);
        }

        const iframeDoc = printFrame.contentDocument || printFrame.contentWindow.document;

        const voucherHtml = `
            <div class="voucher-box">
                <div class="voucher-header">
                    <div class="inst-title">${currentVoucherData.institution || 'State University'}</div>
                    <div class="inst-sub">INTEGRATED INSTITUTIONAL PERFORMANCE MANAGEMENT & DECISION SUPPORT SYSTEM</div>
                    <div class="voucher-badge">CONFIDENTIAL CREDENTIALS HANDOVER SLIP</div>
                </div>
                <hr style="border: 0; border-top: 2px solid #059669; margin: 15px 0;">
                <table class="voucher-table">
                    <tr>
                        <td class="lbl">Personnel Name:</td>
                        <td class="val"><strong>${currentVoucherData.name}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Login Email / Username:</td>
                        <td class="val font-mono">${currentVoucherData.username}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Assigned Role:</td>
                        <td class="val">${currentVoucherData.role}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Temporary Password:</td>
                        <td class="val"><span class="pwd-box">${currentVoucherData.password}</span></td>
                    </tr>
                    <tr>
                        <td class="lbl">Issued Timestamp:</td>
                        <td class="val">${currentVoucherData.issued_at}</td>
                    </tr>
                </table>
                <div class="voucher-notice">
                    <strong>SECURITY ADVISORY:</strong> This credential voucher contains sensitive temporary access credentials generated by the System Administrator. Please navigate to the portal, log in immediately, and update your password via Account Settings.
                </div>
                <div class="sig-row">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Administrator / Issuing Officer</div>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Employee Signature / Date Received</div>
                    </div>
                </div>
            </div>
        `;

        iframeDoc.body.innerHTML = voucherHtml;

        const styleEl = iframeDoc.createElement('style');
        styleEl.textContent = `
            @page { size: A5 landscape; margin: 12mm; }
            body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; margin: 0; padding: 10px; }
            .voucher-box { border: 2px dashed #059669; border-radius: 8px; padding: 20px; max-width: 650px; margin: 0 auto; }
            .voucher-header { text-align: center; }
            .inst-title { font-size: 16px; font-weight: bold; color: #047857; text-transform: uppercase; }
            .inst-sub { font-size: 10px; color: #64748b; letter-spacing: 0.5px; margin-top: 3px; }
            .voucher-badge { display: inline-block; background: #059669; color: #fff; font-size: 10px; font-weight: bold; padding: 3px 10px; border-radius: 20px; margin-top: 8px; }
            .voucher-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .voucher-table td { padding: 6px 4px; font-size: 12px; }
            .voucher-table td.lbl { width: 35%; color: #475569; }
            .voucher-table td.val { width: 65%; color: #0f172a; }
            .font-mono { font-family: monospace; }
            .pwd-box { display: inline-block; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 4px 10px; font-family: monospace; font-size: 14px; font-weight: bold; color: #047857; border-radius: 4px; }
            .voucher-notice { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 8px 12px; font-size: 10px; color: #92400e; margin-top: 15px; }
            .sig-row { display: flex; justify-content: space-between; margin-top: 30px; }
            .sig-box { width: 45%; text-align: center; }
            .sig-line { border-bottom: 1px solid #94a3b8; height: 35px; margin-bottom: 5px; }
            .sig-lbl { font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; }
        `;
        iframeDoc.head.appendChild(styleEl);

        setTimeout(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 300);
    }

    /**
     * Account Lifecycle (Archive / Inactive / Active)
     */
    function confirmToggleUserStatus(userId, userName, targetStatus) {
        let title = `Set to ${targetStatus}?`;
        let text = `Change status of ${userName} to ${targetStatus}?`;
        let icon = 'question';
        let confirmColor = '#047857';

        if (targetStatus === 'Archived') {
            title = `Archive User Account?`;
            text = `User "${userName}" will be placed into the archive and barred from logging in until restored.`;
            icon = 'warning';
            confirmColor = '#dc2626';
        } else if (targetStatus === 'Inactive') {
            title = `Deactivate User Account?`;
            text = `User "${userName}" will be deactivated temporarily.`;
            icon = 'warning';
            confirmColor = '#d97706';
        } else if (targetStatus === 'Active') {
            title = `Restore User Account?`;
            text = `User "${userName}" will be granted active system access.`;
            icon = 'question';
            confirmColor = '#059669';
        }

        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: `Yes, Set to ${targetStatus}`,
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/dss/settings/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        status: targetStatus
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated!',
                            text: data.message,
                            confirmButtonColor: 'var(--dss-primary, #047857)'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Action Denied', data.message || 'Failed to update user status.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'An error occurred while updating user status.', 'error');
                });
            }
        });
    }
</script>
@endsection
