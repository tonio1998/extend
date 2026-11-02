@extends('layouts.dss.app')

@section('title', 'Permissions Registry — User Management — SNSU')

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
                <h3 class="fw-bold text-dark mb-0">System Permissions Master Registry</h3>
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
                Master catalog of system authorization keys and capabilities across SUC operational state machines for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($isSuperAdmin)
                <button class="btn btn-primary btn-sm px-3 rounded-pill shadow-xs d-flex align-items-center gap-1 fw-bold" onclick="openNewPermissionModal()">
                    <i class="mdi mdi-key-plus"></i>
                    <span>Register New Permission</span>
                </button>
            @else
                <div class="alert alert-info py-1 px-3 mb-0 d-flex align-items-center gap-2 extra-small border">
                    <i class="mdi mdi-information-outline fs-6"></i>
                    <span>System permissions are globally governed. Only the SuperAdmin can manage permission keys.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Permissions DataTable Card -->
    <div class="card border rounded-3 p-4 bg-white shadow-2xs mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Capabilities &amp; Authority Keys</h6>
                <div class="extra-small text-muted">
                    Catalog of fine-grained permissions used by roles and direct user grants across the institution.
                </div>
            </div>
            <span class="badge bg-light text-dark border font-mono px-3 py-1">
                {{ $permissions->count() }} Total System Keys
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="permissionsTable" style="font-size: 13px;">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th style="width: 280px;">Permission Key</th>
                        <th>Capability Description</th>
                        <th style="width: 140px;">Direct Users</th>
                        <th class="text-end pe-3" style="width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $perm)
                        @php
                            $matchedDesc = $perm->description ?: ucwords(str_replace(['.', '_', '-'], ' ', $perm->name));
                            $directCount = $directPermissionCounts[$perm->name] ?? 0;
                            $rolesCount = $perm->roles->count();
                        @endphp
                        <tr id="perm-row-{{ $perm->id }}">
                            <td>
                                <code class="fw-bold text-dark px-2 py-1 rounded bg-light border font-mono" style="font-size: 11.5px;">
                                    {{ $perm->name }}
                                </code>
                            </td>
                            <td>
                                <span class="text-dark fw-medium">{{ $matchedDesc }}</span>
                            </td>
                            <td>
                                @if($directCount > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1">
                                        {{ $directCount }} {{ Str::plural('user', $directCount) }}
                                    </span>
                                @else
                                    <span class="text-muted extra-small font-mono">0 users</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if($isSuperAdmin)
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 extra-small fw-semibold"
                                                onclick="openEditPermissionModal({{ $perm->id }}, '{{ addslashes($perm->name) }}', '{{ addslashes($matchedDesc) }}')"
                                                title="Edit Permission">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-light border text-danger rounded-pill px-2 py-1 extra-small fw-semibold"
                                                onclick="deletePermission({{ $perm->id }}, '{{ addslashes($perm->name) }}', {{ $rolesCount }})"
                                                title="Delete Permission">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border font-mono extra-small">Locked</span>
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
<!-- MODAL: REGISTER NEW PERMISSION (SUPERADMIN ONLY)                          -->
<!-- ========================================================================= -->
@if($isSuperAdmin)
<div class="modal fade" id="newPermissionModal" tabindex="-1" aria-labelledby="newPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="newPermissionModalLabel">Register New Permission</h6>
                    <div class="extra-small text-muted">Add a new authorization key to the system registry.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Permission Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono" id="new_permission_name" placeholder="e.g. audit.reports.export_confidential">
                    <div class="extra-small text-muted mt-1">Use lower-case dot notation: <code>module.resource.action</code></div>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Capability Description</label>
                    <textarea class="form-control" id="new_permission_desc" rows="2" placeholder="Describe the authority granted by this key..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" onclick="submitNewPermission()">
                    <i class="mdi mdi-key-plus me-1"></i>Register Permission
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT PERMISSION (SUPERADMIN ONLY)                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editPermissionModalLabel">Edit System Permission</h6>
                    <div class="extra-small text-muted">Update capability description and authority scope.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_permission_id">

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Permission Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono" id="edit_permission_name">
                    <div class="extra-small text-muted mt-1">Caution: Changing keys will affect <code>@@can</code> directives referencing this name.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Capability Description</label>
                    <textarea class="form-control" id="edit_permission_desc" rows="3" placeholder="Describe the authority granted by this key..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" onclick="submitEditPermission()">
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
    let permsDt;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    $(document).ready(function() {
        permsDt = $('#permissionsTable').DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search permissions or keys...",
                lengthMenu: "Show _MENU_ permissions"
            },
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: 3 }
            ]
        });
    });

    function openNewPermissionModal() {
        document.getElementById('new_permission_name').value = '';
        document.getElementById('new_permission_desc').value = '';
        new bootstrap.Modal(document.getElementById('newPermissionModal')).show();
    }

    function submitNewPermission() {
        const permName = document.getElementById('new_permission_name').value.trim();
        if (!permName) {
            Swal.fire('Required', 'Please enter a permission key (e.g. audit.reports.export).', 'warning');
            return;
        }

        const permDesc = document.getElementById('new_permission_desc').value.trim();

        fetch("{{ route('dss.settings.permissions.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: permName,
                description: permDesc
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Permission Registered!',
                    text: data.message,
                    confirmButtonColor: '#00674f'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to register permission.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'An error occurred while registering the permission.', 'error');
        });
    }

    function openEditPermissionModal(id, name, desc) {
        document.getElementById('edit_permission_id').value = id;
        document.getElementById('edit_permission_name').value = name;
        document.getElementById('edit_permission_desc').value = desc;
        new bootstrap.Modal(document.getElementById('editPermissionModal')).show();
    }

    function submitEditPermission() {
        const id = document.getElementById('edit_permission_id').value;
        const name = document.getElementById('edit_permission_name').value.trim();
        const desc = document.getElementById('edit_permission_desc').value.trim();

        if (!name) {
            Swal.fire('Required', 'Permission name cannot be empty.', 'warning');
            return;
        }

        fetch("{{ url('dss/settings/permissions') }}/" + id, {
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
                    title: 'Permission Updated!',
                    text: data.message,
                    confirmButtonColor: '#00674f'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to update permission.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'An error occurred while updating the permission.', 'error');
        });
    }

    function deletePermission(id, name, rolesCount) {
        let warningText = `Are you sure you want to permanently delete permission "${name}"?`;
        if (rolesCount > 0) {
            warningText += ` It is currently attached to ${rolesCount} role(s) and will be unlinked automatically.`;
        }

        Swal.fire({
            title: 'Delete Permission?',
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                fetch("{{ url('dss/settings/permissions') }}/" + id, {
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
                            title: 'Deleted!',
                            text: data.message,
                            confirmButtonColor: '#00674f'
                        }).then(() => {
                            const row = document.getElementById('perm-row-' + id);
                            if (permsDt && row) {
                                permsDt.row(row).remove().draw();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to delete permission.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'An error occurred while deleting the permission.', 'error');
                });
            }
        });
    }
</script>
@endsection
