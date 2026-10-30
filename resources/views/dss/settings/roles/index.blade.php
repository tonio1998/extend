@extends('layouts.dss.app')

@section('title', 'Institutional Roles — User Management — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Institutional Roles Catalog</h3>
                @if($isSuperAdmin)
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 font-mono" style="font-size: 11px;">
                        <i class="mdi mdi-shield-crown me-1 text-warning"></i>SuperAdmin Mode (Full Access)
                    </span>
                @else
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-mono" style="font-size: 11px;">
                        <i class="mdi mdi-shield-account me-1 text-primary"></i>Tenant Admin Mode (View Only)
                    </span>
                @endif
            </div>
            <p class="text-muted small mb-0">
                Sovereign institutional roles and permission matrices for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($isSuperAdmin)
                <button class="btn btn-dss-primary btn-sm px-3.5 shadow-xs d-flex align-items-center gap-1.5" onclick="openNewRoleModal()">
                    <i class="mdi mdi-shield-plus-outline"></i>
                    <span>Create Institutional Role</span>
                </button>
            @else
                <div class="alert alert-info py-1.5 px-3 mb-0 d-flex align-items-center gap-2 extra-small border">
                    <i class="mdi mdi-information-outline fs-6 text-primary"></i>
                    <span>Only the SuperAdmin can create new roles and modify permission matrices. Institutional Admins can assign existing roles and direct permissions in <a href="{{ route('dss.settings.users.index') }}" class="fw-bold text-primary text-decoration-underline">User Management</a>.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Roles DataTable Card -->
    <div class="dss-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Roles &amp; Capability Matrices</h6>
                <div class="extra-small text-muted">
                    Roles encapsulate institutional authorities. Faculty and personnel inherit these capabilities when assigned to a role.
                </div>
            </div>
            @if($isSuperAdmin)
                <button class="btn btn-sm btn-outline-primary shadow-xs d-flex align-items-center gap-1" onclick="openNewRoleModal()">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    <span>New Role</span>
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="rolesTable" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 155px;">Action</th>
                        <th>Role Title</th>
                        <th>Institutional Scope</th>
                        <th>Personnel Count</th>
                        <th>Granted Capabilities</th>
                        <th>Created / Modified</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>
                                @if($isSuperAdmin)
                                    <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="btn btn-sm btn-white border shadow-2xs text-dark fw-bold font-mono px-2.5 py-1.5 d-flex align-items-center gap-1">
                                        <i class="mdi mdi-cog-outline text-primary"></i>
                                        <span>Configure</span>
                                    </a>
                                @else
                                    <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="btn btn-sm btn-light border text-muted fw-bold font-mono px-2.5 py-1.5 d-flex align-items-center gap-1">
                                        <i class="mdi mdi-eye-outline text-primary"></i>
                                        <span>View Capabilities</span>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-2 p-1.5 bg-light border text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-shield-check-outline fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $role->name }}</div>
                                        <div class="extra-small text-muted font-mono">Guard: {{ $role->guard_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono">
                                    {{ $tenant->school_acronym ?: $tenant->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 font-mono">
                                    <i class="mdi mdi-account-multiple me-1"></i>{{ $role->users_count }} Personnel
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-mono">
                                        {{ $role->permissions->count() }} Capabilities
                                    </span>
                                    @if($isSuperAdmin)
                                        <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="btn btn-link btn-sm p-0 extra-small text-decoration-none">
                                            Configure Matrix
                                        </a>
                                    @else
                                        <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="btn btn-link btn-sm p-0 extra-small text-decoration-none text-muted">
                                            View Capabilities
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-muted extra-small font-mono">{{ $role->updated_at ? $role->updated_at->format('M d, Y') : 'System Default' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CONFIGURE ROLE PERMISSIONS MATRIX (SUPERADMIN EDIT / TA VIEW)      -->
<!-- ========================================================================= -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-labelledby="editPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="edit_role_title">Configure Permissions Matrix</h5>
                    <div class="extra-small text-muted" id="edit_role_subtitle">Update institutional capabilities for this role</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_role_id">

                <div id="edit_role_notice" class="alert alert-warning py-2 px-3 mb-3 d-none border">
                    <i class="mdi mdi-shield-lock-outline me-1"></i>
                    <strong>Read-Only Mode:</strong> Only the SuperAdmin can reconfigure permissions assigned to institutional roles.
                </div>

                <div class="d-flex flex-column gap-3">
                    @foreach($permissionCatalog as $domainName => $domainPermissions)
                        <div class="perm-card-domain">
                            <div class="d-flex justify-content-between align-items-center mb-2.5 pb-2 border-bottom">
                                <span class="fw-bold text-dark extra-small text-uppercase" style="letter-spacing: 0.5px;">
                                    <i class="mdi mdi-shield-key-outline me-1 text-primary"></i>{{ $domainName }}
                                </span>
                                <div class="edit-matrix-controls d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-link text-primary extra-small p-0 text-decoration-none" onclick="toggleDomainChecks('edit_', '{{ Str::slug($domainName) }}', true)">Select All</button>
                                    <span class="text-muted extra-small">|</span>
                                    <button type="button" class="btn btn-sm btn-link text-muted extra-small p-0 text-decoration-none" onclick="toggleDomainChecks('edit_', '{{ Str::slug($domainName) }}', false)">Deselect</button>
                                </div>
                            </div>
                            <div class="row g-2">
                                @foreach($domainPermissions as $permKey => $permDesc)
                                    <div class="col-md-6">
                                        <div class="form-check extra-small p-2 rounded bg-white border">
                                            <input class="form-check-input edit-perm-check edit_domain-{{ Str::slug($domainName) }}"
                                                   type="checkbox"
                                                   value="{{ $permKey }}"
                                                   id="edit_{{ Str::slug($permKey) }}">
                                            <label class="form-check-label w-100 cursor-pointer" for="edit_{{ Str::slug($permKey) }}">
                                                <strong class="font-mono text-dark" style="font-size: 11.5px;">{{ $permKey }}</strong>
                                                <div class="text-muted" style="font-size: 11px;">{{ $permDesc }}</div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-dss-primary px-4 fw-bold shadow-xs" id="btnSubmitRoleMatrix" onclick="submitEditPermissions()">
                    <i class="mdi mdi-content-save-check-outline me-1"></i>Update Matrix
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CREATE INSTITUTIONAL ROLE (SUPERADMIN ONLY)                        -->
<!-- ========================================================================= -->
@if($isSuperAdmin)
<div class="modal fade" id="newRoleModal" tabindex="-1" aria-labelledby="newRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="mdi mdi-shield-plus-outline fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="newRoleModalLabel">Create Institutional Role</h5>
                        <div class="extra-small text-muted">Define the position title. Capabilities and personnel can be configured in the Studio.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Role Title Input -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-0">Role Title / Position Name <span class="text-danger">*</span></label>
                        <span class="badge bg-light text-dark border font-mono extra-small">{{ $tenant->school_acronym ?? 'Tenant' }} (Team #{{ $tenant->id }})</span>
                    </div>
                    <div class="input-group shadow-2xs">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-shield-account-outline fs-5"></i></span>
                        <input type="text" class="form-control border-start-0 ps-1" id="new_role_name" placeholder="e.g. Quality Assurance Director / Head of Internal Audit" autofocus>
                    </div>
                    <div class="form-text extra-small text-muted mt-1">
                        Give this institutional role an official, recognizable title.
                    </div>
                </div>

                <!-- Quick Capability Presets -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-0">Quick Capability Preset</label>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small" id="presetBadge">0 capabilities</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="role-preset-option active d-flex align-items-center gap-2" onclick="selectRolePreset('blank', this)">
                                <i class="mdi mdi-radiobox-marked text-primary fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Blank (Custom)</div>
                                    <div class="text-muted" style="font-size: 11px;">Configure in Studio</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-preset-option d-flex align-items-center gap-2" onclick="selectRolePreset('coordinator', this)">
                                <i class="mdi mdi-radiobox-blank text-muted fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Extension Coordinator</div>
                                    <div class="text-muted" style="font-size: 11px;">Plans, Review &amp; M&amp;E</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-preset-option d-flex align-items-center gap-2" onclick="selectRolePreset('tracking', this)">
                                <i class="mdi mdi-radiobox-blank text-muted fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Tracking &amp; Records</div>
                                    <div class="text-muted" style="font-size: 11px;">3rd-Party DTS &amp; Archive</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-preset-option d-flex align-items-center gap-2" onclick="selectRolePreset('budget', this)">
                                <i class="mdi mdi-radiobox-blank text-muted fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Budget Officer</div>
                                    <div class="text-muted" style="font-size: 11px;">Allocation &amp; Burn Rate</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-preset-option d-flex align-items-center gap-2" onclick="selectRolePreset('proponent', this)">
                                <i class="mdi mdi-radiobox-blank text-muted fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Project Leader</div>
                                    <div class="text-muted" style="font-size: 11px;">Author Plans &amp; Proposals</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-preset-option d-flex align-items-center gap-2" onclick="selectRolePreset('all', this)">
                                <i class="mdi mdi-radiobox-blank text-muted fs-5 preset-radio"></i>
                                <div>
                                    <div class="fw-bold text-dark extra-small">Full RIE Authority</div>
                                    <div class="text-muted" style="font-size: 11px;">All Extension capabilities</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Manual Capability Picker (Collapsed by default) -->
                <div class="border-top pt-2 mt-3">
                    <a class="d-flex align-items-center justify-content-between text-decoration-none text-muted extra-small fw-semibold py-1" data-bs-toggle="collapse" href="#advancedPermissionsCollapse" role="button" aria-expanded="false">
                        <span><i class="mdi mdi-tune-variant me-1 text-primary"></i>Advanced: Hand-pick individual capabilities (<span id="manualSelectedCount">0</span>)</span>
                        <i class="mdi mdi-chevron-down"></i>
                    </a>
                    <div class="collapse mt-2" id="advancedPermissionsCollapse">
                        <div class="p-2.5 rounded bg-light border" style="max-height: 220px; overflow-y: auto;">
                            @foreach($permissionCatalog as $domainName => $domainPermissions)
                                <div class="mb-2 pb-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark" style="font-size: 11px;">{{ $domainName }}</span>
                                        <button type="button" class="btn btn-sm btn-link extra-small p-0 text-decoration-none" onclick="toggleDomainChecks('new_', '{{ Str::slug($domainName) }}', true); updateManualCount();">Select All</button>
                                    </div>
                                    <div class="row g-1">
                                        @foreach($domainPermissions as $permKey => $permDesc)
                                            <div class="col-md-6">
                                                <div class="form-check extra-small py-1 px-2 rounded bg-white border" style="font-size: 11px;">
                                                    <input class="form-check-input new-perm-check new_domain-{{ Str::slug($domainName) }}"
                                                           type="checkbox"
                                                           value="{{ $permKey }}"
                                                           id="new_{{ Str::slug($permKey) }}"
                                                           onchange="onManualPermChange()">
                                                    <label class="form-check-label w-100 cursor-pointer" for="new_{{ Str::slug($permKey) }}" title="{{ $permDesc }}">
                                                        <span class="font-mono text-dark fw-bold" style="font-size: 10.5px;">{{ $permKey }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-white border shadow-2xs btn-sm px-3 text-dark fw-semibold" onclick="submitNewRole(false)" id="btnSaveOnly">
                        <i class="mdi mdi-content-save-outline me-1"></i>Save Role
                    </button>
                    <button type="button" class="btn btn-dss-primary btn-sm px-3.5 fw-bold shadow-xs d-flex align-items-center gap-1.5" onclick="submitNewRole(true)" id="btnSaveAndConfigure">
                        <span>Create &amp; Open Studio</span>
                        <i class="mdi mdi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/responsive.bootstrap5.min.js') }}"></script>

<script>
    const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
    let rolesDt;

    $(document).ready(function() {
        rolesDt = $('#rolesTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search roles...",
                lengthMenu: "Show _MENU_ roles"
            },
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0] }
            ]
        });
    });

    function toggleDomainChecks(prefix, domainSlug, checkState) {
        document.querySelectorAll(`.${prefix}domain-${domainSlug}`).forEach(chk => {
            if (!chk.disabled) chk.checked = checkState;
        });
    }

    function openEditPermissionsModal(roleId, roleName, activePermissions, canEdit) {
        document.getElementById('edit_role_id').value = roleId;
        document.getElementById('edit_role_title').innerText = canEdit ? `Configure Matrix: ${roleName}` : `Role Matrix: ${roleName}`;
        document.getElementById('edit_role_subtitle').innerText = canEdit
            ? 'Grant or revoke fine-grained institutional capabilities for this role'
            : 'Viewing institutional capabilities assigned to this role';

        const notice = document.getElementById('edit_role_notice');
        const submitBtn = document.getElementById('btnSubmitRoleMatrix');
        const controls = document.querySelectorAll('.edit-matrix-controls');

        if (!canEdit) {
            notice.classList.remove('d-none');
            submitBtn.classList.add('d-none');
            controls.forEach(c => c.classList.add('d-none'));
        } else {
            notice.classList.add('d-none');
            submitBtn.classList.remove('d-none');
            controls.forEach(c => c.classList.remove('d-none'));
        }

        document.querySelectorAll('.edit-perm-check').forEach(chk => {
            chk.checked = activePermissions.includes(chk.value);
            chk.disabled = !canEdit;
        });

        new bootstrap.Modal(document.getElementById('editPermissionsModal')).show();
    }

    function submitEditPermissions() {
        if (!isSuperAdmin) {
            Swal.fire('Unauthorized', 'Only SuperAdmin can configure role permission matrices.', 'warning');
            return;
        }

        const roleId = document.getElementById('edit_role_id').value;
        const permissions = Array.from(document.querySelectorAll('.edit-perm-check:checked')).map(c => c.value);

        fetch(`/dss/settings/roles/${roleId}/permissions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                permissions: permissions
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Role Matrix Updated!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to update matrix.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Unable to update role permissions matrix.', 'error');
        });
    }

    const rolePresets = {
        'blank': [],
        'coordinator': [
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.manage',
            'rie.proposals.view',
            'rie.proposals.review',
            'rie.proposals.evaluate',
            'rie.signatures.sign',
            'rie.implementation.manage',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.reports.accomplishment',
            'rie.impact.evaluate',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.tracking.view'
        ],
        'tracking': [
            'dashboard.view',
            'rie.tracking.view',
            'rie.tracking.manage',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.documents.view',
            'rie.documents.manage'
        ],
        'budget': [
            'dashboard.view',
            'rie.budgets.view',
            'rie.budgets.manage',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.documents.view',
            'rie.documents.manage'
        ],
        'proponent': [
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.create',
            'rie.proposals.view',
            'rie.proposals.create',
            'rie.implementation.manage',
            'rie.activities.manage',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.reports.accomplishment',
            'rie.signatures.sign',
            'rie.documents.view',
            'rie.documents.manage'
        ],
        'all': [
            @foreach($permissionCatalog as $domain => $perms)
                @foreach($perms as $pKey => $pDesc)
                    '{{ $pKey }}',
                @endforeach
            @endforeach
        ]
    };

    function selectRolePreset(presetKey, element) {
        document.querySelectorAll('.role-preset-option').forEach(el => {
            el.classList.remove('active');
            const icon = el.querySelector('.preset-radio');
            if (icon) {
                icon.classList.remove('mdi-radiobox-marked', 'text-primary');
                icon.classList.add('mdi-radiobox-blank', 'text-muted');
            }
        });

        if (element) {
            element.classList.add('active');
            const icon = element.querySelector('.preset-radio');
            if (icon) {
                icon.classList.remove('mdi-radiobox-blank', 'text-muted');
                icon.classList.add('mdi-radiobox-marked', 'text-primary');
            }
        }

        const selectedKeys = rolePresets[presetKey] || [];
        document.querySelectorAll('.new-perm-check').forEach(chk => {
            chk.checked = selectedKeys.includes(chk.value);
        });

        updateManualCount();
    }

    function onManualPermChange() {
        document.querySelectorAll('.role-preset-option').forEach(el => {
            el.classList.remove('active');
            const icon = el.querySelector('.preset-radio');
            if (icon) {
                icon.classList.remove('mdi-radiobox-marked', 'text-primary');
                icon.classList.add('mdi-radiobox-blank', 'text-muted');
            }
        });
        updateManualCount();
    }

    function updateManualCount() {
        const checkedCount = document.querySelectorAll('.new-perm-check:checked').length;
        const badge = document.getElementById('presetBadge');
        const manualCount = document.getElementById('manualSelectedCount');
        if (badge) badge.innerText = `${checkedCount} capabilities`;
        if (manualCount) manualCount.innerText = checkedCount;
    }

    function openNewRoleModal() {
        if (!isSuperAdmin) {
            Swal.fire('Restricted', 'Only the SuperAdmin is authorized to create institutional roles.', 'warning');
            return;
        }
        document.getElementById('new_role_name').value = '';
        selectRolePreset('blank', document.querySelector('.role-preset-option'));
        const modal = new bootstrap.Modal(document.getElementById('newRoleModal'));
        modal.show();
        setTimeout(() => {
            const input = document.getElementById('new_role_name');
            if (input) input.focus();
        }, 300);
    }

    function submitNewRole(openStudio = true) {
        if (!isSuperAdmin) {
            Swal.fire('Unauthorized', 'Only SuperAdmin can create new institutional roles.', 'warning');
            return;
        }

        const roleName = document.getElementById('new_role_name').value.trim();
        if (!roleName) {
            Swal.fire('Required', 'Please enter a role position name.', 'warning');
            const input = document.getElementById('new_role_name');
            if (input) input.focus();
            return;
        }

        const permissions = Array.from(document.querySelectorAll('.new-perm-check:checked')).map(c => c.value);
        const btn = openStudio ? document.getElementById('btnSaveAndConfigure') : document.getElementById('btnSaveOnly');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';
        }

        fetch("{{ route('dss.settings.roles.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: roleName,
                permissions: permissions
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                if (openStudio && data.redirect_url) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Role Created!',
                        text: 'Opening Role Configuration Studio...',
                        showConfirmButton: false,
                        timer: 900
                    }).then(() => {
                        window.location.href = data.redirect_url;
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Role Created!',
                        text: data.message,
                        confirmButtonColor: 'var(--dss-primary)'
                    }).then(() => location.reload());
                }
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                Swal.fire('Error', data.message || 'Failed to create role.', 'error');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
            Swal.fire('Error', 'An error occurred while creating the role.', 'error');
        });
    }
</script>
@endsection
