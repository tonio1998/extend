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
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
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
                Master catalog of system authorization keys and capabilities across SUC operational state machines for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($isSuperAdmin)
                <button class="btn btn-dss-primary btn-sm px-3.5 shadow-xs d-flex align-items-center gap-1.5" onclick="openNewPermissionModal()">
                    <i class="mdi mdi-key-plus"></i>
                    <span>Register New Permission</span>
                </button>
            @else
                <div class="alert alert-info py-1.5 px-3 mb-0 d-flex align-items-center gap-2 extra-small border">
                    <i class="mdi mdi-information-outline fs-6"></i>
                    <span>System permissions are globally governed. Only the SuperAdmin can register new permission keys.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Permissions DataTable Card -->
    <div class="dss-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Capabilities &amp; Authority Keys</h6>
                <div class="extra-small text-muted">
                    Catalog of fine-grained permissions used by roles and direct user grants across the institution.
                </div>
            </div>
            <span class="badge bg-light text-dark border font-mono px-3 py-1.5">
                {{ $permissions->count() }} Total System Keys
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="permissionsTable" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Permission Key</th>
                        <th>Domain / Module</th>
                        <th>Capability Description</th>
                        <th>Roles Assigned</th>
                        <th>Direct Personnel</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $perm)
                        @php
                            $matchedDomain = 'Institutional Governance & Administration';
                            $matchedDesc = ucwords(str_replace(['.', '_', '-'], ' ', $perm->name));
                            foreach($permissionCatalog as $dom => $permsList) {
                                if (isset($permsList[$perm->name])) {
                                    $matchedDomain = $dom;
                                    $matchedDesc = $permsList[$perm->name];
                                    break;
                                }
                            }

                            $domainBadgeClass = match(true) {
                                str_contains($matchedDomain, 'Program') || str_contains($matchedDomain, 'Plan') => 'bg-primary-subtle text-primary border-primary-subtle',
                                str_contains($matchedDomain, 'Proposal') || str_contains($matchedDomain, 'Evaluation') => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                str_contains($matchedDomain, 'Approval') || str_contains($matchedDomain, 'Endorsement') => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                str_contains($matchedDomain, 'Implementation') || str_contains($matchedDomain, 'Monitoring') => 'bg-success-subtle text-success border-success-subtle',
                                str_contains($matchedDomain, 'Accomplishment') || str_contains($matchedDomain, 'Impact') => 'bg-primary-subtle text-primary border-primary-subtle',
                                str_contains($matchedDomain, 'Document') || str_contains($matchedDomain, 'Tracking') => 'bg-info-subtle text-info border-info-subtle',
                                str_contains($matchedDomain, 'Budget') || str_contains($matchedDomain, 'Financial') => 'bg-danger-subtle text-danger border-danger-subtle',
                                default => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                            };

                            $directCount = $directPermissionCounts[$perm->name] ?? 0;
                        @endphp
                        <tr>
                            <td>
                                <code class="fw-bold text-dark px-2 py-1 rounded bg-light border font-mono" style="font-size: 12px;">
                                    {{ $perm->name }}
                                </code>
                            </td>
                            <td>
                                <span class="badge {{ $domainBadgeClass }} border px-2 py-1 extra-small">
                                    {{ $matchedDomain }}
                                </span>
                            </td>
                            <td>
                                <span class="text-dark fw-medium">{{ $matchedDesc }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($perm->roles as $r)
                                        <span class="badge bg-light text-dark border extra-small" style="font-size: 10.5px;">
                                            {{ $r->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted extra-small font-mono">None</span>
                                    @endforelse
                                </div>
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
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="newPermissionModalLabel">Register New Permission</h5>
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
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Domain / Module</label>
                    <select class="form-select" id="new_permission_domain">
                        @foreach(array_keys($permissionCatalog) as $dName)
                            <option value="{{ $dName }}">{{ $dName }}</option>
                        @endforeach
                        <option value="Custom Institutional Capabilities">Other / Custom</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Capability Description</label>
                    <textarea class="form-control" id="new_permission_desc" rows="2" placeholder="Describe the authority granted by this key..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted">Automatically Attach to Roles (Optional)</label>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        @foreach($roles as $r)
                            <div class="form-check extra-small border rounded px-2.5 py-1 bg-light">
                                <input class="form-check-input new-perm-role-target" type="checkbox" value="{{ $r->name }}" id="perm_role_{{ $r->id }}">
                                <label class="form-check-label cursor-pointer" for="perm_role_{{ $r->id }}">
                                    {{ $r->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dss-primary px-4 fw-bold shadow-xs" onclick="submitNewPermission()">
                    <i class="mdi mdi-key-plus me-1"></i>Register Permission
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

    $(document).ready(function() {
        permsDt = $('#permissionsTable').DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search capabilities...",
                lengthMenu: "Show _MENU_ permissions"
            },
            order: [[1, 'asc'], [0, 'asc']]
        });
    });

    function openNewPermissionModal() {
        document.getElementById('new_permission_name').value = '';
        document.getElementById('new_permission_desc').value = '';
        document.querySelectorAll('.new-perm-role-target').forEach(c => c.checked = false);
        new bootstrap.Modal(document.getElementById('newPermissionModal')).show();
    }

    function submitNewPermission() {
        const permName = document.getElementById('new_permission_name').value.trim();
        if (!permName) {
            Swal.fire('Required', 'Please enter a permission key (e.g. audit.reports.export).', 'warning');
            return;
        }

        const permDesc = document.getElementById('new_permission_desc').value.trim();
        const permDomain = document.getElementById('new_permission_domain').value;
        const targetRoles = Array.from(document.querySelectorAll('.new-perm-role-target:checked')).map(c => c.value);

        fetch("{{ route('dss.settings.permissions.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: permName,
                description: permDesc,
                domain: permDomain,
                roles: targetRoles
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Permission Registered!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to register permission.', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'An error occurred while registering the permission.', 'error');
        });
    }
</script>
@endsection
