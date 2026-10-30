@extends('layouts.dss.app')

@section('title', 'Assign Roles & Permissions — ' . $user->name . ' — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Top Action Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.users.index') }}" class="btn btn-sm btn-white border shadow-2xs text-dark fw-bold d-flex align-items-center gap-1">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Back to Personnel Directory</span>
            </a>
            <span class="text-muted small">/</span>
            <span class="fw-semibold text-dark small">Assign Roles &amp; Permissions</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border font-mono px-3 py-2">
                <i class="mdi mdi-shield-account-outline text-primary me-1"></i>AJAX Instant Synced
            </span>
        </div>
    </div>

    <!-- User Header Dossier Card -->
    <div class="dss-card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-dark border d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 52px; height: 52px; min-width: 52px; font-size: 18px;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0">{{ $user->name }}</h4>
                        @if($user->employee)
                            <span class="badge bg-light text-dark border font-mono" style="font-size: 11px;">
                                <i class="mdi mdi-badge-account-horizontal-outline me-1 text-muted"></i>{{ $user->employee->employee_no }}
                            </span>
                        @endif
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono" id="user_primary_role_badge">
                            {{ $user->role ?: 'Unassigned' }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap text-muted small">
                        <span><i class="mdi mdi-email-outline me-1"></i>{{ $user->email }}</span>
                        <span>&bull;</span>
                        <span><i class="mdi mdi-bank-outline me-1"></i>{{ $user->campus?->campus_name ?? 'Main Campus' }}</span>
                        <span>&bull;</span>
                        <span><i class="mdi mdi-domain me-1"></i>{{ $user->department?->unit_name ?: ($user->department?->dept_name ?? 'General Administration') }}</span>
                    </div>
                </div>
            </div>

            <!-- Real-time Privilege Counter Badges -->
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded bg-light border text-center" style="min-width: 120px;">
                    <div class="extra-small text-muted text-uppercase fw-bold">Active Roles</div>
                    <div class="fs-5 fw-bold text-dark font-mono" id="roles_count_badge">{{ count($userRoleNames) }}</div>
                </div>
                <div class="p-2 rounded bg-light border text-center" style="min-width: 140px;">
                    <div class="extra-small text-muted text-uppercase fw-bold">Direct Permissions</div>
                    <div class="fs-5 fw-bold text-success font-mono" id="perms_count_badge">{{ count($userDirectPermNames) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Assignment Container -->
    <div class="dss-card p-4">
        <!-- Navigation Tabs: Roles vs Permissions -->
        <ul class="nav nav-tabs access-tabs mb-4" id="accessTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab') !== 'permissions' ? 'active' : '' }} d-flex align-items-center gap-2" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-pane" type="button" role="tab">
                    <i class="mdi mdi-shield-account text-primary"></i>
                    <span>Roles Assignment</span>
                    <span class="badge bg-light text-dark border font-mono ms-1" id="tab_roles_count">{{ count($userRoleNames) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab') === 'permissions' ? 'active' : '' }} d-flex align-items-center gap-2" id="perms-tab" data-bs-toggle="tab" data-bs-target="#perms-pane" type="button" role="tab">
                    <i class="mdi mdi-shield-key-outline text-success"></i>
                    <span>Direct Permissions</span>
                    <span class="badge bg-light text-dark border font-mono ms-1" id="tab_perms_count">{{ count($userDirectPermNames) }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="accessTabsContent">
            <!-- ============================================================= -->
            <!-- TAB 1: ROLES ASSIGNMENT TABLE (MATCHING SCREENSHOT 1)        -->
            <!-- ============================================================= -->
            <div class="tab-pane fade {{ request('tab') !== 'permissions' ? 'show active' : '' }}" id="roles-pane" role="tabpanel">
                <div class="alert alert-light border py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 extra-small text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-information-outline text-primary fs-6"></i>
                        <span>
                            Toggle any switch to instantly assign or revoke an institutional role for <strong>{{ $user->name }}</strong> via AJAX.
                        </span>
                    </div>
                    <span class="font-mono text-dark fw-bold">Live Synced &bull; No Page Reload</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100 table-clean-header" id="rolesTable" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">#</th>
                                <th style="width: 200px;">NAME</th>
                                <th>DETAILS</th>
                                <th style="width: 120px; text-align: center;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $index => $role)
                                @php
                                    $isAssigned = in_array($role->name, $userRoleNames);
                                    $desc = $roleDescriptions[$role->name] ?? 'Standard institutional access role and privileges.';
                                @endphp
                                <tr id="role_row_{{ $role->id }}">
                                    <td class="text-center font-mono text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $role->name }}</div>
                                    </td>
                                    <td class="text-secondary">
                                        {{ $desc }}
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center m-0">
                                            <input class="form-check-input role-toggle-switch"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="role_switch_{{ $role->id }}"
                                                   data-role-name="{{ $role->name }}"
                                                   {{ $isAssigned ? 'checked' : '' }}
                                                   onchange="handleRoleToggle(this, '{{ addslashes($role->name) }}')">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- TAB 2: PERMISSIONS ASSIGNMENT TABLE (MATCHING SCREENSHOT 2)  -->
            <!-- ============================================================= -->
            <div class="tab-pane fade {{ request('tab') === 'permissions' ? 'show active' : '' }}" id="perms-pane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="permissionDomainFilter" style="width: 280px;" onchange="filterPermissionsByDomain(this.value)">
                            <option value="">All Domains ({{ $allPermissions->count() }})</option>
                            @foreach($permissionCatalog as $domain => $perms)
                                <option value="{{ $domain }}">{{ $domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2 extra-small text-muted">
                        <i class="mdi mdi-shield-check text-success fs-6"></i>
                        <span>Direct permissions apply immediately to this account.</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100 table-clean-header" id="permissionsTable" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">#</th>
                                <th style="width: 260px;">NAME</th>
                                <th>DETAILS</th>
                                <th style="width: 120px; text-align: center;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allPermissions as $pIndex => $perm)
                                @php
                                    $isGranted = in_array($perm['name'], $userDirectPermNames);
                                @endphp
                                <tr id="perm_row_{{ $perm['id'] }}" data-domain="{{ $perm['domain'] }}">
                                    <td class="text-center font-mono text-muted">{{ $pIndex + 1 }}</td>
                                    <td>
                                        <div class="font-mono fw-bold text-dark" style="font-size: 12px;">{{ $perm['name'] }}</div>
                                        <div class="extra-small text-muted">{{ $perm['domain'] }}</div>
                                    </td>
                                    <td class="text-secondary">
                                        {{ $perm['details'] }}
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center m-0">
                                            <input class="form-check-input perm-toggle-switch"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="perm_switch_{{ $perm['id'] }}"
                                                   data-perm-name="{{ $perm['name'] }}"
                                                   {{ $isGranted ? 'checked' : '' }}
                                                   onchange="handlePermissionToggle(this, '{{ addslashes($perm['name']) }}')">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    const userId = {{ $user->id }};
    let rolesDataTable;
    let permissionsDataTable;

    $(document).ready(function() {
        // DataTables for Roles (responsive: false per specification, clean integer layout)
        rolesDataTable = $('#rolesTable').DataTable({
            responsive: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search roles...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [3] }
            ]
        });

        // DataTables for Permissions (responsive: false per specification)
        permissionsDataTable = $('#permissionsTable').DataTable({
            responsive: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search permissions...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [3] }
            ]
        });
    });

    /**
     * Filter Permissions DataTable by Domain dropdown
     */
    function filterPermissionsByDomain(domain) {
        if (!permissionsDataTable) return;
        if (!domain) {
            permissionsDataTable.column(1).search('').draw();
        } else {
            // Filter column 1 where domain name is shown
            permissionsDataTable.column(1).search(domain).draw();
        }
    }

    /**
     * Instant AJAX Toggle for Roles
     */
    function handleRoleToggle(toggleEl, roleName) {
        const isChecked = toggleEl.checked;
        toggleEl.disabled = true;

        fetch(`/dss/settings/users/${userId}/toggle-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                role_name: roleName,
                state: isChecked
            })
        })
        .then(res => res.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.result) {
                // Update live counters
                document.getElementById('roles_count_badge').innerText = data.roles_count;
                document.getElementById('tab_roles_count').innerText = data.roles_count;
                if (data.primary_role) {
                    document.getElementById('user_primary_role_badge').innerText = data.primary_role;
                }

                // Toast notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: isChecked ? 'success' : 'info',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                // Revert toggle state on failure
                toggleEl.checked = !isChecked;
                Swal.fire('Operation Prohibited', data.message || 'Unable to update role.', 'error');
            }
        })
        .catch(err => {
            toggleEl.disabled = false;
            toggleEl.checked = !isChecked;
            Swal.fire('Network Error', 'An error occurred while communicating with the server.', 'error');
        });
    }

    /**
     * Instant AJAX Toggle for Permissions
     */
    function handlePermissionToggle(toggleEl, permName) {
        const isChecked = toggleEl.checked;
        toggleEl.disabled = true;

        fetch(`/dss/settings/users/${userId}/toggle-permission`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                permission_name: permName,
                state: isChecked
            })
        })
        .then(res => res.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.result) {
                // Update live counters
                document.getElementById('perms_count_badge').innerText = data.permissions_count;
                document.getElementById('tab_perms_count').innerText = data.permissions_count;

                // Toast notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: isChecked ? 'success' : 'info',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true
                });
            } else {
                // Revert toggle state on failure
                toggleEl.checked = !isChecked;
                Swal.fire('Operation Prohibited', data.message || 'Unable to update permission.', 'error');
            }
        })
        .catch(err => {
            toggleEl.disabled = false;
            toggleEl.checked = !isChecked;
            Swal.fire('Network Error', 'An error occurred while communicating with the server.', 'error');
        });
    }
</script>
@endsection
