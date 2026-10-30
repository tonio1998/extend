@php
    $rolesList = ($roles ?? \Spatie\Permission\Models\Role::all())
        ->filter(fn($r) => $r->name !== 'SuperAdmin')
        ->sortBy('name');
@endphp

<!-- GENERATE USER ACCESS / RESET PASSWORD MODAL (CRM ENTERPRISE EDITION) -->
<div class="modal fade no-print" id="generateUserModal" tabindex="-1" aria-labelledby="generateUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            
            <!-- VIEW 1: GENERATE / PROVISION FORM -->
            <div id="genUserStepForm">
                <div class="modal-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-2xs" 
                             style="background: #eff6ff; color: #2563eb; width: 44px; height: 44px; min-width: 44px; border: 1px solid #dbeafe;">
                            <i class="mdi mdi-shield-key-outline fs-4" id="genModalIcon"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="generateUserModalLabel">Generate System User Access</h5>
                            <div class="text-muted extra-small" id="genModalSubTitle">Provision sovereign login credentials &amp; Google SSO</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="generateUserForm" method="POST" action="" onsubmit="handleGenerateUserSubmit(event)">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Inline Error Alert Banner -->
                        <div id="genUserErrorAlert" class="alert alert-danger py-2 px-3 mb-3 d-none align-items-center gap-2" style="font-size: 12.5px; border-radius: 8px;">
                            <i class="mdi mdi-alert-circle-outline fs-5 flex-shrink-0"></i>
                            <span id="genUserErrorText">Error message</span>
                        </div>

                        <!-- Target Personnel Entity Card -->
                        <div class="p-3 rounded-3 border mb-3" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                            <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Target Personnel Entity</div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-bold text-dark fs-6" id="genEmpName">—</div>
                                <span class="badge bg-white text-dark border font-mono px-2 py-1 shadow-2xs" id="genEmpNoBadge">—</span>
                            </div>
                            <div class="text-muted extra-small mt-1 d-flex align-items-center gap-2" id="genEmpDesigText">
                                <i class="mdi mdi-briefcase-outline text-secondary"></i>
                                <span>—</span>
                            </div>
                        </div>

                        <!-- Google Workspace SSO Callout -->
                        <div class="p-2 rounded-3 mb-3 d-flex align-items-center gap-2" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <div class="rounded-circle p-1 bg-white border d-flex align-items-center justify-content-center shadow-2xs flex-shrink-0" style="width: 30px; height: 30px;">
                                <svg width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                            </div>
                            <div style="font-size: 12px; line-height: 1.4;" class="text-success-emphasis">
                                <strong>Google Workspace SSO:</strong> Employee can authenticate seamlessly via 1-Click <strong>"Sign in with Google"</strong> using their institutional email.
                            </div>
                        </div>

                        <!-- Institutional Email Field -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Institutional Email (Username) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-email-outline"></i></span>
                                <input type="email" name="user_email" id="genUserEmail" class="form-control border-start-0 font-mono" required placeholder="e.g. employee@snsu.edu.ph" style="font-size: 13.5px;">
                            </div>
                            <div class="form-text extra-small text-muted">Primary identifier for portal login and Google SSO authentication.</div>
                        </div>

                        <!-- System Role Field -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Assigned System Role <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-shield-account-outline"></i></span>
                                <select name="role" id="genUserRole" class="form-select border-start-0" required data-no-select2="true" style="font-size: 13.5px;">
                                    @foreach($rolesList as $role)
                                        <option value="{{ $role->name }}" {{ $role->name === 'Staff' ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Custom Password Field (Optional) -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-dark mb-0" id="genPassLabel">Custom Password (Optional)</label>
                                <span class="extra-small text-muted font-mono">Min 6 characters</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-lock-outline"></i></span>
                                <input type="password" name="password" id="genUserPassword" class="form-control border-start-0 border-end-0" placeholder="Leave empty for auto-generated password" style="font-size: 13.5px;">
                                <button class="btn btn-light border border-start-0 text-muted px-2" type="button" id="togglePasswordBtn" onclick="toggleGenPassword()">
                                    <i class="mdi mdi-eye-outline" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            <div class="form-text extra-small text-muted mt-1" id="genPassHelp">If blank, system will generate a secure temporary key that you can view and print.</div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light py-2 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-white border px-3 fw-semibold text-dark shadow-2xs" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="genUserSubmitBtn" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                            <span class="spinner-border spinner-border-sm d-none" id="genUserSpinner" role="status" aria-hidden="true"></span>
                            <i class="mdi mdi-account-key" id="genUserBtnIcon"></i>
                            <span id="genUserBtnText">Generate &amp; Enable Access</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- VIEW 2: SUCCESS CREDENTIAL SLIP (PRINT & VIEW) -->
            <div id="genUserStepSuccess" class="d-none">
                <div class="modal-header border-bottom px-4 py-3" style="background:#f0fdf4; border-left:4px solid #16a34a;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-2xs" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-check-bold fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0 fs-6" id="slipHeaderTitle">User Access Credentials Issued</h5>
                            <div class="text-white-50 extra-small" id="slipHeaderSub">Account is active and ready for employee handover</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" onclick="finishAndReloadModal()" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Credential Voucher Box -->
                    <div id="credentialVoucherCard" class="p-3 rounded-3 border bg-white shadow-2xs mb-3" style="border: 1.5px dashed #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase font-mono" style="font-size: 10px;">Official Handover Voucher</span>
                                <div class="fw-bold text-dark fs-6 mt-1" id="slipEmpName">—</div>
                                <div class="text-muted extra-small" id="slipEmpMeta">—</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success border border-success-subtle" id="slipRoleBadge">Role: Admin</span>
                                <div class="text-muted font-mono extra-small mt-1" id="slipIssuedAt" style="font-size: 10px;">—</div>
                            </div>
                        </div>

                        <!-- Credentials Grid -->
                        <div class="d-flex flex-column gap-2">
                            <!-- Login URL -->
                            <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between gap-2">
                                <div class="overflow-hidden">
                                    <div class="text-muted extra-small text-uppercase fw-bold" style="font-size: 10px;">Portal Web Address</div>
                                    <div class="font-mono text-dark fw-bold text-truncate" id="slipLoginUrl" style="font-size: 12.5px;">—</div>
                                </div>
                                <button type="button" class="btn btn-xs btn-white border shadow-2xs text-muted flex-shrink-0 px-2 py-1" onclick="copySlipField('slipLoginUrl', 'Portal URL')">
                                    <i class="mdi mdi-content-copy me-1"></i>Copy
                                </button>
                            </div>

                            <!-- Username / Email -->
                            <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between gap-2">
                                <div class="overflow-hidden">
                                    <div class="text-muted extra-small text-uppercase fw-bold" style="font-size: 10px;">Username / Institutional Email</div>
                                    <div class="font-mono text-dark fw-bold text-truncate" id="slipEmail" style="font-size: 13px;">—</div>
                                </div>
                                <button type="button" class="btn btn-xs btn-white border shadow-2xs text-muted flex-shrink-0 px-2 py-1" onclick="copySlipField('slipEmail', 'Username / Email')">
                                    <i class="mdi mdi-content-copy me-1"></i>Copy
                                </button>
                            </div>

                            <!-- Temporary Password -->
                            <div class="p-2 rounded border d-flex align-items-center justify-content-between gap-2" style="background: #fffbeb; border-color: #fde68a !important;">
                                <div class="overflow-hidden">
                                    <div class="text-amber-800 extra-small text-uppercase fw-bold d-flex align-items-center gap-1" style="font-size: 10px;">
                                        <i class="mdi mdi-key-variant"></i> Temporary Access Password
                                    </div>
                                    <div class="font-mono text-dark fw-bold" id="slipPassword" style="font-size: 15px; letter-spacing: 0.5px;">—</div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-white border shadow-2xs text-muted px-2 py-1" onclick="toggleSlipPasswordMask()">
                                        <i class="mdi mdi-eye-outline" id="slipPasswordEye"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-warning border text-dark fw-semibold shadow-2xs px-2 py-1" onclick="copySlipField('slipPassword', 'Password')">
                                        <i class="mdi mdi-content-copy me-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Handover Notice -->
                        <div class="mt-3 p-2 rounded bg-light text-muted extra-small d-flex align-items-start gap-2" style="font-size: 11px; line-height: 1.4;">
                            <i class="mdi mdi-information-outline text-primary mt-1 fs-6"></i>
                            <div>
                                <strong>Employee Instructions:</strong> Hand over this voucher or print a physical copy. The user can also use <strong>1-Click Google SSO</strong> with this email. Instruct employee to update password on first login.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="modal-footer bg-light py-2 px-4 border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="copyAllSlipCredentials()">
                        <i class="mdi mdi-clipboard-text-multiple-outline me-1"></i>Copy All Text
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3 shadow-sm d-flex align-items-center gap-2" onclick="printCredentialSlip()">
                            <i class="mdi mdi-printer"></i>Print Credential Slip
                        </button>
                        <button type="button" class="btn btn-sm btn-success fw-bold px-3" onclick="finishAndReloadModal()">
                            <i class="mdi mdi-check me-1"></i>Done
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- HIDDEN PRINTABLE SLIP TEMPLATE (DOM-BASED PRINTING) -->
<div id="printableSlipArea" style="display: none;">
    <div style="max-width: 620px; margin: 0 auto; border: 2px solid #0f172a; border-radius: 8px; padding: 28px 32px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; background: #fff;">
        <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 14px; margin-bottom: 18px;">
            <div id="printSlipInst" style="font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b;">Surigao del Norte State University — Extension System</div>
            <div style="font-size: 19px; font-weight: 900; color: #0f172a; margin-top: 4px; letter-spacing: 0.5px;">OFFICIAL SYSTEM USER CREDENTIAL SLIP</div>
            <div style="display: inline-block; margin-top: 6px; padding: 3px 10px; background: #f1f5f9; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #475569;">Confidential Handover Voucher</div>
        </div>

        <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px;">Personnel Master Record</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 18px;">
            <tr>
                <td style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; width: 32%; font-weight: 600; color: #64748b;">Personnel Name:</td>
                <td id="printSlipName" style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a;">—</td>
            </tr>
            <tr>
                <td style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; width: 32%; font-weight: 600; color: #64748b;">Employee Number:</td>
                <td id="printSlipNo" style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; font-family: monospace;">—</td>
            </tr>
            <tr>
                <td style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; width: 32%; font-weight: 600; color: #64748b;">Designation / Role:</td>
                <td id="printSlipRole" style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a;">—</td>
            </tr>
            <tr>
                <td style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; width: 32%; font-weight: 600; color: #64748b;">Campus &amp; Department:</td>
                <td id="printSlipDept" style="padding: 6px 8px; font-size: 13px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a;">—</td>
            </tr>
        </table>

        <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 8px;">Authorized Access Credentials</div>
        <div style="background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 6px; padding: 16px; margin: 18px 0;">
            <div style="margin-bottom: 10px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">Portal Web Address</div>
                <div id="printSlipUrl" style="font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px;">—</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">Institutional Username / Login Email</div>
                <div id="printSlipEmail" style="font-family: 'Courier New', Courier, monospace; font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 2px;">—</div>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">Initial Access Password</div>
                <div style="margin-top: 4px;">
                    <span id="printSlipPassword" style="font-family: 'Courier New', Courier, monospace; font-size: 19px; font-weight: 900; color: #097969; background: #e6fffa; border: 1px solid #99f6e4; display: inline-block; padding: 4px 14px; border-radius: 4px; letter-spacing: 1.5px;">—</span>
                </div>
            </div>
        </div>

        <div style="background: #fffbeb; border: 1px solid #fef08a; padding: 12px; border-radius: 6px; font-size: 11.5px; color: #713f12; line-height: 1.5; margin-bottom: 24px;">
            <strong>Security &amp; Login Instructions:</strong>
            <ul style="margin: 4px 0 0 0; padding-left: 18px;">
                <li><strong>Google Workspace 1-Click SSO:</strong> You may also click <em>"Sign in with Google"</em> on the portal login page using your institutional email.</li>
                <li><strong>First-Time Login:</strong> Please change your initial password immediately upon first access.</li>
                <li><strong>Confidentiality:</strong> Do not disclose or share these credentials with anyone.</li>
            </ul>
        </div>

        <div style="display: table; width: 100%; margin-top: 30px;">
            <div style="display: table-cell; width: 50%; padding-right: 20px;">
                <div style="border-bottom: 1px solid #0f172a; height: 38px; margin-bottom: 6px;"></div>
                <div style="font-size: 11px; color: #475569; font-weight: 600; text-transform: uppercase;">Issued By: <strong id="printSlipIssuedBy">System Admin</strong></div>
                <div id="printSlipIssuedAt" style="font-size: 10px; color: #64748b;">—</div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div style="border-bottom: 1px solid #0f172a; height: 38px; margin-bottom: 6px;"></div>
                <div style="font-size: 11px; color: #475569; font-weight: 600; text-transform: uppercase;">Received &amp; Acknowledged By: (Employee Signature)</div>
                <div style="font-size: 10px; color: #64748b;">Date Received: ____________________</div>
            </div>
        </div>

        <div style="border-top: 1.5px dashed #94a3b8; margin-top: 36px; padding-top: 8px; font-size: 10.5px; color: #94a3b8; text-align: center; font-family: monospace;">
            &#9986; ----------------- DETACH &amp; SECURELY HAND OVER TO EMPLOYEE -----------------
        </div>
    </div>
</div>

<!-- SCRIPTS FOR GENERATE USER ACCESS & PRINT HANDOVER SLIP -->
<script>
    let activeCredentials = null;
    let rawPasswordValue = '';
    let isPasswordMasked = false;

    function openGenerateUserModal(empId, empName, empNo, empEmail, empDesig, hasUser = false, currentRole = null) {
        // Reset steps
        document.getElementById('genUserStepForm').classList.remove('d-none');
        document.getElementById('genUserStepSuccess').classList.add('d-none');
        
        // Mode styling & texts (Generate Access vs Reset Password)
        const modalTitle = document.getElementById('generateUserModalLabel');
        const modalSub = document.getElementById('genModalSubTitle');
        const modalIcon = document.getElementById('genModalIcon');
        const btnText = document.getElementById('genUserBtnText');
        const btnIcon = document.getElementById('genUserBtnIcon');
        const passLabel = document.getElementById('genPassLabel');
        const passHelp = document.getElementById('genPassHelp');

        if (hasUser) {
            if (modalTitle) modalTitle.textContent = 'Reset Password & Issue Credential Slip';
            if (modalSub) modalSub.textContent = 'Reset employee portal password and generate printable handover voucher';
            if (modalIcon) modalIcon.className = 'mdi mdi-lock-reset fs-4';
            if (btnText) btnText.textContent = 'Reset Password & Generate Slip';
            if (btnIcon) btnIcon.className = 'mdi mdi-lock-reset';
            if (passLabel) passLabel.textContent = 'New Temporary Password (Optional)';
            if (passHelp) passHelp.textContent = 'Leave empty to auto-generate a fresh secure key for the employee.';
        } else {
            if (modalTitle) modalTitle.textContent = 'Generate System User Access';
            if (modalSub) modalSub.textContent = 'Provision sovereign login credentials & Google SSO';
            if (modalIcon) modalIcon.className = 'mdi mdi-shield-key-outline fs-4';
            if (btnText) btnText.textContent = 'Generate & Enable Access';
            if (btnIcon) btnIcon.className = 'mdi mdi-account-key';
            if (passLabel) passLabel.textContent = 'Custom Password (Optional)';
            if (passHelp) passHelp.textContent = 'If blank, system will generate a secure temporary key that you can view and print.';
        }

        // Populate Target info
        document.getElementById('genEmpName').textContent = empName || '—';
        document.getElementById('genEmpNoBadge').textContent = empNo || 'EMP-NO';
        
        const desigContainer = document.getElementById('genEmpDesigText');
        if (desigContainer) {
            desigContainer.innerHTML = '<i class="mdi mdi-briefcase-outline text-secondary"></i> <span>' + (empDesig || 'Staff Member') + '</span>';
        }
        
        // Populate fields
        document.getElementById('genUserEmail').value = empEmail || '';
        document.getElementById('genUserPassword').value = '';

        if (currentRole) {
            const roleSelect = document.getElementById('genUserRole');
            if (roleSelect) roleSelect.value = currentRole;
        }
        
        // Reset error alert
        const errorAlert = document.getElementById('genUserErrorAlert');
        if (errorAlert) {
            errorAlert.classList.add('d-none');
            errorAlert.classList.remove('d-flex');
        }

        // Set form action
        document.getElementById('generateUserForm').action = '/dss/settings/employees/' + empId + '/generate-user';

        // Show modal
        const modalEl = document.getElementById('generateUserModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    function toggleGenPassword() {
        const input = document.getElementById('genUserPassword');
        const icon = document.getElementById('togglePasswordIcon');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
        } else {
            input.type = 'password';
            icon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
        }
    }

    function toggleSlipPasswordMask() {
        const passEl = document.getElementById('slipPassword');
        const eyeIcon = document.getElementById('slipPasswordEye');
        if (!passEl) return;
        
        if (isPasswordMasked) {
            passEl.textContent = rawPasswordValue;
            eyeIcon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
            isPasswordMasked = false;
        } else {
            passEl.textContent = '••••••••••••';
            eyeIcon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
            isPasswordMasked = true;
        }
    }

    function populateCredentialSlipView(cred) {
        activeCredentials = cred;
        rawPasswordValue = cred.password;
        isPasswordMasked = false;

        // Populate Modal Success View
        document.getElementById('slipEmpName').textContent = cred.name;
        document.getElementById('slipEmpMeta').textContent = cred.employee_no + ' • ' + cred.designation + ' (' + cred.campus + ')';
        document.getElementById('slipRoleBadge').textContent = 'Role: ' + cred.role;
        document.getElementById('slipIssuedAt').textContent = 'Issued: ' + cred.issued_at;
        document.getElementById('slipLoginUrl').textContent = cred.login_url;
        document.getElementById('slipEmail').textContent = cred.email;
        document.getElementById('slipPassword').textContent = cred.password;

        // Populate Hidden Print Template
        const pInst = document.getElementById('printSlipInst');
        if (pInst) pInst.textContent = cred.institution_name || 'Surigao del Norte State University';
        const pName = document.getElementById('printSlipName');
        if (pName) pName.textContent = cred.name;
        const pNo = document.getElementById('printSlipNo');
        if (pNo) pNo.textContent = cred.employee_no;
        const pRole = document.getElementById('printSlipRole');
        if (pRole) pRole.textContent = cred.designation + ' • ' + cred.role;
        const pDept = document.getElementById('printSlipDept');
        if (pDept) pDept.textContent = cred.campus + ' — ' + cred.department;
        const pUrl = document.getElementById('printSlipUrl');
        if (pUrl) pUrl.textContent = cred.login_url;
        const pEmail = document.getElementById('printSlipEmail');
        if (pEmail) pEmail.textContent = cred.email;
        const pPass = document.getElementById('printSlipPassword');
        if (pPass) pPass.textContent = cred.password;
        const pIssuedBy = document.getElementById('printSlipIssuedBy');
        if (pIssuedBy) pIssuedBy.textContent = cred.issued_by;
        const pIssuedAt = document.getElementById('printSlipIssuedAt');
        if (pIssuedAt) pIssuedAt.textContent = 'Issued: ' + cred.issued_at;
    }

    function handleGenerateUserSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('generateUserForm');
        const submitBtn = document.getElementById('genUserSubmitBtn');
        const spinner = document.getElementById('genUserSpinner');
        const btnIcon = document.getElementById('genUserBtnIcon');
        const btnText = document.getElementById('genUserBtnText');
        const errorAlert = document.getElementById('genUserErrorAlert');
        const errorText = document.getElementById('genUserErrorText');

        // Reset error
        errorAlert.classList.add('d-none');
        errorAlert.classList.remove('d-flex');

        // Loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnIcon.classList.add('d-none');
        btnText.textContent = 'Processing Credentials...';

        const formData = new FormData(form);
        const payload = {
            user_email: formData.get('user_email'),
            role: formData.get('role'),
            password: formData.get('password') || null
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || form.querySelector('input[name="_token"]')?.value;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to provision user access.');
            }
            return data;
        })
        .then(data => {
            if (data.result && data.credentials) {
                populateCredentialSlipView(data.credentials);

                // Flip view
                document.getElementById('genUserStepForm').classList.add('d-none');
                document.getElementById('genUserStepSuccess').classList.remove('d-none');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Credentials generated! Ready to print or copy.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } else {
                throw new Error(data.message || 'Failed to generate credentials.');
            }
        })
        .catch(err => {
            console.error(err);
            errorText.textContent = err.message || 'An error occurred while provisioning user access.';
            errorAlert.classList.remove('d-none');
            errorAlert.classList.add('d-flex');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnIcon.classList.remove('d-none');
            btnText.textContent = 'Generate & Enable Access';
        });
    }

    function copySlipField(elementId, fieldName) {
        const text = elementId === 'slipPassword' ? rawPasswordValue : document.getElementById(elementId)?.textContent;
        if (!text) return;
        
        if (typeof window.dssCopyToClipboard === 'function') {
            window.dssCopyToClipboard(text, fieldName + ' copied to clipboard!');
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            alert(fieldName + ' copied!');
        }
    }

    function copyAllSlipCredentials() {
        if (!activeCredentials) return;
        const text = `=====================================================
SNSU EXTENSION SYSTEM — OFFICIAL PERSONNEL ACCESS CREDENTIALS
=====================================================
Employee:      ${activeCredentials.name} (${activeCredentials.employee_no})
Designation:   ${activeCredentials.designation}
Campus/Dept:   ${activeCredentials.campus} / ${activeCredentials.department}
Assigned Role: ${activeCredentials.role}

Login URL:     ${activeCredentials.login_url}
Username:      ${activeCredentials.email}
Password:      ${rawPasswordValue}

Google SSO:    1-Click "Sign in with Google" enabled
Issued Date:   ${activeCredentials.issued_at}
Issued By:     ${activeCredentials.issued_by}
=====================================================
CONFIDENTIAL: Hand over directly to employee. Update password upon first login.`;

        if (typeof window.dssCopyToClipboard === 'function') {
            window.dssCopyToClipboard(text, 'Full credentials text copied!');
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            alert('Full credentials copied to clipboard!');
        }
    }

    function printCredentialSlip() {
        if (!activeCredentials) return;
        
        // Ensure values are populated in printable slip area
        populateCredentialSlipView(activeCredentials);

        const slipContent = document.getElementById('printableSlipArea').innerHTML;
        
        let printIframe = document.getElementById('printSlipIframe');
        if (!printIframe) {
            printIframe = document.createElement('iframe');
            printIframe.id = 'printSlipIframe';
            printIframe.style.position = 'fixed';
            printIframe.style.right = '0';
            printIframe.style.bottom = '0';
            printIframe.style.width = '0';
            printIframe.style.height = '0';
            printIframe.style.border = '0';
            document.body.appendChild(printIframe);
        }

        const iframeDoc = printIframe.contentDocument || printIframe.contentWindow.document;
        iframeDoc.body.innerHTML = slipContent;

        const printStyle = iframeDoc.createElement('style');
        printStyle.textContent = '@page { size: A4 portrait; margin: 15mm; } body { margin: 0; padding: 20px; background: #fff; }';
        iframeDoc.head.appendChild(printStyle);

        setTimeout(function() {
            printIframe.contentWindow.focus();
            printIframe.contentWindow.print();
        }, 300);
    }

    function finishAndReloadModal() {
        const modalEl = document.getElementById('generateUserModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
        window.location.reload();
    }

    @if(session('credentials'))
    document.addEventListener('DOMContentLoaded', function() {
        const sessionCred = @json(session('credentials'));
        if (sessionCred) {
            populateCredentialSlipView(sessionCred);

            document.getElementById('genUserStepForm').classList.add('d-none');
            document.getElementById('genUserStepSuccess').classList.remove('d-none');

            const modalEl = document.getElementById('generateUserModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
    @endif
</script>
