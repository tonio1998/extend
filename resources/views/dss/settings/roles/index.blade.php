@extends('layouts.dss.app')

@section('title', 'Institutional Roles & Capability Matrices — User Management — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Institutional Roles Management</h3>
                @if($isSuperAdmin)
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 font-mono extra-small">
                        <i class="mdi mdi-shield-crown me-1 text-warning"></i>SuperAdmin Mode (Full CRUD)
                    </span>
                @else
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-mono extra-small">
                        <i class="mdi mdi-shield-account me-1 text-primary"></i>Tenant Admin Mode (View Only)
                    </span>
                @endif
            </div>
            <p class="text-muted small mb-0">
                Configure role designations, manage personnel assignments, and grant fine-grained system capabilities for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.settings.permissions.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-secondary">
                <i class="mdi mdi-key-variant"></i>
                <span>Permissions Registry</span>
            </a>
            @if($isSuperAdmin)
                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1" onclick="openNewRoleModal()">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    <span>New Institutional Role</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Roles DataTable Card -->
    <div class="card border rounded-3 p-4 bg-white shadow-2xs mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutional Roles &amp; Capabilities</h6>
                <div class="extra-small text-muted">
                    Roles encapsulate institutional authorities. Faculty and personnel inherit capabilities when assigned to a role.
                </div>
            </div>
            <span class="badge bg-light text-dark border font-mono px-3 py-1">
                {{ $roles->count() }} Configured Roles
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="rolesTable" style="font-size: 13px;">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th style="width: 175px;">Actions</th>
                        <th style="width: 220px;">Role Title</th>
                        <th>Scope &amp; Description</th>
                        <th style="width: 140px;">Assigned Personnel</th>
                        <th style="width: 160px;">Granted Capabilities</th>
                        <th class="pe-3 text-end" style="width: 120px;">Last Modified</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr id="role-row-{{ $role->id }}">
                            <td>
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 extra-small fw-semibold" title="Configure Capabilities & Personnel">
                                        <i class="mdi mdi-tune-vertical me-1"></i>Configure
                                    </a>
                                    @if($isSuperAdmin && $role->name !== 'SuperAdmin')
                                        <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 extra-small text-dark fw-semibold"
                                                onclick="openEditRoleModal({{ $role->id }}, '{{ addslashes($role->name) }}', '{{ addslashes($role->description ?? '') }}')"
                                                title="Rename / Edit Role">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-1 extra-small text-danger fw-semibold"
                                                onclick="deleteRole({{ $role->id }}, '{{ addslashes($role->name) }}', {{ $role->users_count }})"
                                                title="Delete Role">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-2 p-1 bg-light border text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                        <i class="mdi mdi-shield-check-outline fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark font-mono" id="role-title-{{ $role->id }}">{{ $role->name }}</div>
                                        <div class="extra-small text-muted font-mono">Guard: {{ $role->guard_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark extra-small" id="role-desc-{{ $role->id }}">
                                    {{ $role->description ?: 'Standard institutional access role and privileges for ' . ($tenant->school_acronym ?: $tenant->name) . '.' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 font-mono extra-small">
                                    <i class="mdi mdi-account-multiple me-1"></i>{{ $role->users_count }} Personnel
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dss.settings.roles.configure', $role->id) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-mono text-decoration-none extra-small">
                                    <i class="mdi mdi-shield-key-outline me-1"></i>{{ $role->permissions->count() }} Capabilities
                                </a>
                            </td>
                            <td class="pe-3 text-end">
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
<!-- MODAL: CREATE INSTITUTIONAL ROLE (SUPERADMIN ONLY)                        -->
<!-- ========================================================================= -->
@if($isSuperAdmin)
<div class="modal fade" id="newRoleModal" tabindex="-1" aria-labelledby="newRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2">
                        <i class="mdi mdi-shield-plus-outline fs-4"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="newRoleModalLabel">Create Institutional Role</h6>
                        <div class="extra-small text-muted">Add a new role to the institution catalog.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Role Title / Position Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono" id="new_role_name" placeholder="e.g. Auditor, Extension Coordinator, Dean" autofocus>
                    <div class="extra-small text-muted mt-1">Official title recognized within {{ $tenant->school_acronym ?: $tenant->name }}.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Role Description &amp; Scope</label>
                    <textarea class="form-control" id="new_role_description" rows="3" placeholder="Describe the responsibilities and institutional scope of this role..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary" onclick="submitNewRole(false)" id="btnSaveRoleOnly">
                        <i class="mdi mdi-content-save-outline me-1"></i>Save Role
                    </button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-2xs" onclick="submitNewRole(true)" id="btnSaveRoleStudio">
                        <span>Save &amp; Configure</span>
                        <i class="mdi mdi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT ROLE (SUPERADMIN ONLY)                                        -->
<!-- ========================================================================= -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2">
                        <i class="mdi mdi-pencil-outline fs-4"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="editRoleModalLabel">Edit Institutional Role</h6>
                        <div class="extra-small text-muted">Rename role or update scope description.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_role_id">

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Role Title / Position Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono" id="edit_role_name">
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Role Description &amp; Scope</label>
                    <textarea class="form-control" id="edit_role_description" rows="3" placeholder="Describe the responsibilities and institutional scope..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" onclick="submitEditRole()">
                    <i class="mdi mdi-check-circle-outline me-1"></i>Save Changes
                </button>
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    let rolesDt;

    $(document).ready(function() {
        rolesDt = $('#rolesTable').DataTable({
            responsive: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50],
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

    function openNewRoleModal() {
        document.getElementById('new_role_name').value = '';
        document.getElementById('new_role_description').value = '';
        new bootstrap.Modal(document.getElementById('newRoleModal')).show();
    }

    function submitNewRole(openStudio) {
        const roleName = document.getElementById('new_role_name').value.trim();
        const roleDesc = document.getElementById('new_role_description').value.trim();

        if (!roleName) {
            Swal.fire('Required', 'Please enter a role title (e.g. Auditor, Project Leader).', 'warning');
            return;
        }

        const btnSave = document.getElementById('btnSaveRoleOnly');
        const btnStudio = document.getElementById('btnSaveRoleStudio');
        if (btnSave) btnSave.disabled = true;
        if (btnStudio) btnStudio.disabled = true;

        fetch("{{ route('dss.settings.roles.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: roleName,
                description: roleDesc
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btnSave) btnSave.disabled = false;
            if (btnStudio) btnStudio.disabled = false;

            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Role Created!',
                    text: data.message,
                    confirmButtonColor: '#00674f',
                    timer: 1500
                }).then(() => {
                    if (openStudio && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to create role.', 'error');
            }
        })
        .catch(err => {
            if (btnSave) btnSave.disabled = false;
            if (btnStudio) btnStudio.disabled = false;
            Swal.fire('Error', 'An error occurred while creating the role.', 'error');
        });
    }

    function openEditRoleModal(id, name, desc) {
        document.getElementById('edit_role_id').value = id;
        document.getElementById('edit_role_name').value = name;
        document.getElementById('edit_role_description').value = desc;
        new bootstrap.Modal(document.getElementById('editRoleModal')).show();
    }

    function submitEditRole() {
        const id = document.getElementById('edit_role_id').value;
        const name = document.getElementById('edit_role_name').value.trim();
        const desc = document.getElementById('edit_role_description').value.trim();

        if (!name) {
            Swal.fire('Required', 'Role title cannot be empty.', 'warning');
            return;
        }

        fetch("{{ url('dss/settings/roles') }}/" + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                description: desc
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Role Updated!',
                    text: data.message,
                    confirmButtonColor: '#00674f'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to update role.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'An error occurred while updating the role.', 'error');
        });
    }

    function deleteRole(id, name, usersCount) {
        if (usersCount > 0) {
            Swal.fire({
                title: 'Cannot Delete Role',
                text: `Role "${name}" currently has ${usersCount} assigned personnel. Please reassign the personnel before deleting this role.`,
                icon: 'error',
                confirmButtonColor: '#00674f'
            });
            return;
        }

        Swal.fire({
            title: 'Delete Role?',
            text: `Are you sure you want to permanently delete role "${name}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                fetch("{{ url('dss/settings/roles') }}/" + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Role Deleted!',
                            text: data.message,
                            confirmButtonColor: '#00674f'
                        }).then(() => {
                            const row = document.getElementById('role-row-' + id);
                            if (rolesDt && row) {
                                rolesDt.row(row).remove().draw();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to delete role.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'An error occurred while deleting the role.', 'error');
                });
            }
        });
    }
</script>
@endsection
