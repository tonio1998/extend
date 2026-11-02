@extends('layouts.dss.app')

@section('title', 'Configure Role: ' . $role->name . ' — Institutional Roles — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
@php
    $domainIcons = [
        'Extension Program Management' => 'mdi-lightbulb-on-outline',
        'Proposal Submission & Evaluation' => 'mdi-file-document-edit-outline',
        'Approval Workflow & Endorsements' => 'mdi-draw-pen',
        'Implementation, Activities & Monitoring' => 'mdi-calendar-check-outline',
        'Accomplishment, Impact & Analytics' => 'mdi-chart-areaspline',
        'Document Repository & Tracking' => 'mdi-folder-file-outline',
        'Budget & Financial Monitoring' => 'mdi-cash-multiple',
        'Governance & Audit Trail' => 'mdi-shield-lock-outline',
        'General System Permissions' => 'mdi-cog-outline',
    ];

    // Build flat array of all permissions with their domain and grant status
    $flatPermissions = [];
    $accountedPerms = [];

    foreach ($permissionCatalog as $catTitle => $catItems) {
        foreach ($catItems as $pKey => $pDesc) {
            $permModel = $allPermissions->firstWhere('name', $pKey);
            if ($permModel) {
                $flatPermissions[] = [
                    'id' => $permModel->id,
                    'key' => $pKey,
                    'description' => $pDesc,
                    'domain' => $catTitle,
                    'is_granted' => in_array($pKey, $rolePermissionNames),
                ];
                $accountedPerms[] = $pKey;
            }
        }
    }

    $otherPerms = $allPermissions->whereNotIn('name', $accountedPerms);
    foreach ($otherPerms as $op) {
        $flatPermissions[] = [
            'id' => $op->id,
            'key' => $op->name,
            'description' => $op->name,
            'domain' => 'General System Permissions',
            'is_granted' => in_array($op->name, $rolePermissionNames),
        ];
    }

    $totalGrantedPerms = collect($flatPermissions)->where('is_granted', true)->count();
    $totalPermsCount = count($flatPermissions);

    $assignedUserIdsList = $assignedUsers->pluck('id')->values()->all();
@endphp

<div class="container-fluid p-0">
    {{-- Breadcrumb & Top Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 extra-small font-mono">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">DSS Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.settings.roles.index') }}" class="text-decoration-none text-muted">Roles Catalog</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $role->name }}</li>
            </ol>
        </nav>

        <a href="{{ route('dss.settings.roles.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-secondary">
            <i class="mdi mdi-arrow-left"></i>
            <span>Back to Roles Catalog</span>
        </a>
    </div>

    {{-- Compact Executive Role Hero Header Card --}}
    <div class="card border rounded-3 p-3 mb-3 shadow-2xs bg-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-shield-account-outline me-1"></i>INSTITUTIONAL ROLE
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        Guard: {{ $role->guard_name }}
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        {{ $tenant->school_acronym ?: $tenant->name }}
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-0 font-mono" style="letter-spacing: -0.02em;">
                    {{ $role->name }}
                </h5>
                <div class="text-muted extra-small mt-1">
                    Toggle switches in the last column to grant or revoke capabilities and assign institutional personnel.
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Segmented View Switcher --}}
                <div class="d-inline-flex align-items-center gap-1 p-1 bg-light rounded-pill border shadow-2xs">
                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold role-tab-btn active btn-dark text-white" id="btnTabPerms" onclick="switchRoleStudioTab('permissions', this)">
                        <i class="mdi mdi-shield-key-outline me-1"></i>Capabilities Matrix (<span id="tabPermsCount">{{ $totalGrantedPerms }}</span>)
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-semibold text-secondary role-tab-btn" id="btnTabUsers" onclick="switchRoleStudioTab('personnel', this)">
                        <i class="mdi mdi-account-group-outline me-1"></i>Assigned Personnel (<span id="tabUsersCount">{{ $assignedUsers->count() }}</span>)
                    </button>
                </div>

                {{-- Compact Metric Badge --}}
                <div class="px-3 py-1 bg-light rounded-3 border text-center shadow-2xs font-mono extra-small">
                    <span class="text-muted">Granted: </span>
                    <strong class="text-primary" id="heroPermsGranted">{{ $totalGrantedPerms }}</strong>
                    <span class="text-muted">/ {{ $totalPermsCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- PANE 1: CAPABILITIES MATRIX DATATABLE (SWITCH IN LAST COLUMN)         --}}
    {{-- ===================================================================== --}}
    <div id="panePermissions" class="role-studio-pane">
        <div class="card border rounded-3 bg-white shadow-2xs mb-4 overflow-hidden">
            {{-- Toolbar Header --}}
            <div class="p-2 px-3 border-bottom bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-xl-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $isSuperAdmin ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} font-mono extra-small">
                                <i class="mdi {{ $isSuperAdmin ? 'mdi-shield-crown' : 'mdi-shield-lock-outline' }} me-1"></i>
                                {{ $isSuperAdmin ? 'SuperAdmin Mode' : 'Read-Only Mode' }}
                            </span>
                            <span id="perms-dirty-indicator" class="badge bg-warning text-dark font-mono d-none extra-small">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>Unsaved Changes
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-xl-8">
                        <div class="d-flex align-items-center justify-content-xl-end gap-2 flex-wrap flex-md-nowrap">
                            {{-- Search Filter --}}
                            <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" style="min-width: 180px; max-width: 240px;">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-2 pe-1" style="border-radius: 8px 0 0 8px;">
                                    <i class="mdi mdi-magnify" style="font-size: 14px;"></i>
                                </span>
                                <input type="text" id="permSearchInput" class="form-control form-control-sm border-start-0 ps-1" placeholder="Search permissions..." autocomplete="off" style="border-radius: 0 8px 8px 0; font-size: 11.5px;">
                            </div>

                            {{-- Domain / Module Filter --}}
                            <select id="permDomainFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 11.5px; min-width: 160px; max-width: 210px; border-radius: 8px;">
                                <option value="">All Functional Modules</option>
                                @foreach($permissionCatalog as $dTitle => $dItems)
                                    <option value="{{ $dTitle }}">{{ $dTitle }}</option>
                                @endforeach
                            </select>

                            {{-- Status Filter --}}
                            <select id="permStatusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 11.5px; min-width: 115px; max-width: 135px; border-radius: 8px;">
                                <option value="">All Statuses</option>
                                <option value="GRANTED">Granted Only</option>
                                <option value="REVOKED">Revoked Only</option>
                            </select>

                            {{-- Save Matrix Button --}}
                            @if($isSuperAdmin)
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1 flex-shrink-0" id="btn-save-perms" onclick="savePermissionsMatrix()">
                                    <i class="mdi mdi-content-save-outline"></i>
                                    <span id="btn-save-perms-text">Save Matrix</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bulk Actions (SuperAdmin) --}}
            @if($isSuperAdmin)
                <div class="px-3 py-1 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 extra-small">
                    <span class="text-muted font-mono" style="font-size: 11px;">Quick Actions for Visible Capabilities:</span>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 extra-small fw-semibold" onclick="bulkToggleVisiblePermissions(true)">
                            <i class="mdi mdi-check-all me-1"></i>Grant All Filtered
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small fw-semibold" onclick="bulkToggleVisiblePermissions(false)">
                            <i class="mdi mdi-close me-1"></i>Revoke All Filtered
                        </button>
                    </div>
                </div>
            @endif

            {{-- Capabilities DataTable --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" id="permissionsTable" style="width: 100%; font-size: 12px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="width: 220px;">Functional Module</th>
                            <th style="width: 220px;">Permission Key</th>
                            <th>Description / Authority Scope</th>
                            <th style="width: 100px;">Status</th>
                            <th class="pe-3 text-end" style="width: 90px;">Grant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flatPermissions as $p)
                            @php
                                $isGranted = $p['is_granted'];
                                $slug = Str::slug($p['key']);
                            @endphp
                            <tr class="perm-table-row {{ $isGranted ? 'bg-success-subtle bg-opacity-10' : '' }}" id="row-perm-{{ $slug }}">
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border font-mono extra-small">
                                        <i class="mdi {{ $domainIcons[$p['domain']] ?? 'mdi-shield-check-outline' }} text-primary me-1"></i>
                                        {{ $p['domain'] }}
                                    </span>
                                </td>
                                <td>
                                    <code class="fw-bold font-mono text-dark" style="font-size: 11.5px;">{{ $p['key'] }}</code>
                                </td>
                                <td>
                                    <span class="text-muted" style="line-height: 1.4;">{{ $p['description'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $isGranted ? 'bg-success text-white' : 'bg-light text-muted border' }} font-mono perm-badge-label" id="badge-perm-{{ $slug }}" style="font-size: 9.5px; padding: 2px 6px;">
                                        {{ $isGranted ? 'GRANTED' : 'REVOKED' }}
                                    </span>
                                </td>
                                <td class="pe-3 text-end">
                                    <div class="form-check form-switch m-0 d-inline-flex justify-content-end align-items-center">
                                        <input class="form-check-input perm-switch dss-switch"
                                               type="checkbox"
                                               role="switch"
                                               id="switch-{{ $slug }}"
                                               value="{{ $p['key'] }}"
                                               {{ $isGranted ? 'checked' : '' }}
                                               {{ !$isSuperAdmin ? 'disabled' : '' }}
                                               onchange="onPermSwitchToggle(this)"
                                               title="{{ $isGranted ? 'Revoke Permission' : 'Grant Permission' }}">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- PANE 2: PERSONNEL ASSIGNMENT DATATABLE (SWITCH IN LAST COLUMN)        --}}
    {{-- ===================================================================== --}}
    <div id="panePersonnel" class="role-studio-pane d-none">
        <div class="card border rounded-3 bg-white shadow-2xs mb-4 overflow-hidden">
            {{-- Toolbar Header --}}
            <div class="p-2 px-3 border-bottom bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-xl-4">
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-dark" style="font-size: 12.5px;">Institutional Personnel Assignment</strong>
                            <span id="personnel-dirty-indicator" class="badge bg-warning text-dark font-mono d-none extra-small">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>Unsaved Changes
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-xl-8">
                        <div class="d-flex align-items-center justify-content-xl-end gap-2 flex-wrap flex-md-nowrap">
                            {{-- Search Filter --}}
                            <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" style="min-width: 180px; max-width: 240px;">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-2 pe-1" style="border-radius: 8px 0 0 8px;">
                                    <i class="mdi mdi-magnify" style="font-size: 14px;"></i>
                                </span>
                                <input type="text" id="userSearchInput" class="form-control form-control-sm border-start-0 ps-1" placeholder="Search personnel..." autocomplete="off" style="border-radius: 0 8px 8px 0; font-size: 11.5px;">
                            </div>

                            {{-- Campus Filter --}}
                            <select id="userCampusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 11.5px; min-width: 150px; max-width: 190px; border-radius: 8px;">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->campus_name }}">{{ $campus->campus_name }}</option>
                                @endforeach
                            </select>

                            {{-- Role Assignment Status Filter --}}
                            <select id="userStatusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 11.5px; min-width: 130px; max-width: 155px; border-radius: 8px;">
                                <option value="">All Personnel</option>
                                <option value="ASSIGNED">Assigned Only</option>
                                <option value="UNASSIGNED">Unassigned Only</option>
                            </select>

                            {{-- Save Personnel Button --}}
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1 flex-shrink-0" id="btn-save-personnel" onclick="savePersonnelAssignments()">
                                <i class="mdi mdi-content-save-outline"></i>
                                <span id="btn-save-personnel-text">Save Personnel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bulk Actions (Personnel) --}}
            <div class="px-3 py-1 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 extra-small">
                <span class="text-muted font-mono" style="font-size: 11px;">Quick Actions for Visible Personnel:</span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 extra-small fw-semibold" onclick="bulkToggleVisiblePersonnel(true)">
                        <i class="mdi mdi-check-all me-1"></i>Assign All Filtered
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small fw-semibold" onclick="bulkToggleVisiblePersonnel(false)">
                        <i class="mdi mdi-close me-1"></i>Unassign All Filtered
                    </button>
                </div>
            </div>

            {{-- Personnel DataTable --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" id="personnelTable" style="width: 100%; font-size: 12px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="width: 220px;">Personnel Name</th>
                            <th style="width: 200px;">Email / User ID</th>
                            <th style="width: 160px;">Campus</th>
                            <th>Department / Unit</th>
                            <th style="width: 110px;">Status</th>
                            <th class="pe-3 text-end" style="width: 90px;">Assign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTenantUsers as $user)
                            @php
                                $isAssigned = in_array($user->id, $assignedUserIdsList);
                                $initials = collect(explode(' ', trim($user->name)))
                                    ->filter()
                                    ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                                $uCampusName = $user->campus?->campus_name ?? $user->campus?->name ?? 'University-Wide';
                                $uDeptName = $user->department?->unit_name ?: ($user->department?->dept_name ?? $user->department?->name ?? 'Unassigned Unit');
                            @endphp
                            <tr class="user-table-row {{ $isAssigned ? 'bg-success-subtle bg-opacity-10' : '' }}" id="row-user-{{ $user->id }}">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success fw-bold font-mono shadow-2xs flex-shrink-0" style="width: 26px; height: 26px; background: #dcfce7; font-size: 10px;">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $user->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-dark">{{ $user->email }}</span>
                                    <span class="text-muted extra-small font-mono">#{{ $user->id }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono extra-small">
                                        {{ $uCampusName }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border font-mono text-truncate extra-small" style="max-width: 180px;">
                                        {{ $uDeptName }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $isAssigned ? 'bg-success text-white' : 'bg-light text-muted border' }} font-mono user-badge-label" id="badge-user-{{ $user->id }}" style="font-size: 9.5px; padding: 2px 6px;">
                                        {{ $isAssigned ? 'ASSIGNED' : 'UNASSIGNED' }}
                                    </span>
                                </td>
                                <td class="pe-3 text-end">
                                    <div class="form-check form-switch m-0 d-inline-flex justify-content-end align-items-center">
                                        <input class="form-check-input user-switch dss-switch"
                                               type="checkbox"
                                               role="switch"
                                               id="switch-user-{{ $user->id }}"
                                               value="{{ $user->id }}"
                                               {{ $isAssigned ? 'checked' : '' }}
                                               onchange="onUserSwitchToggle(this)"
                                               title="{{ $isAssigned ? 'Revoke Assignment' : 'Assign to Role' }}">
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
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    const roleId = {{ $role->id }};
    const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // In-memory state of granted permissions & assigned users
    let rolePermissions = new Set(@json($rolePermissionNames));
    let roleUserIds = new Set(@json($assignedUserIdsList));

    let permsTable;
    let usersTable;

    function switchRoleStudioTab(tab, btn) {
        document.querySelectorAll('.role-studio-pane').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.role-tab-btn').forEach(b => {
            b.classList.remove('active', 'btn-dark', 'text-white');
            b.classList.add('text-secondary');
        });

        if (btn) {
            btn.classList.add('active', 'btn-dark', 'text-white');
            btn.classList.remove('text-secondary');
        }

        if (tab === 'permissions') {
            document.getElementById('panePermissions')?.classList.remove('d-none');
            permsTable?.columns.adjust().draw(false);
        } else {
            document.getElementById('panePersonnel')?.classList.remove('d-none');
            usersTable?.columns.adjust().draw(false);
        }
    }

    $(document).ready(function() {
        // 1. Initialize Capabilities DataTable
        permsTable = $('#permissionsTable').DataTable({
            pageLength: 25,
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
            order: [[0, 'asc'], [1, 'asc']],
            autoWidth: false,
            language: {
                search: "",
                emptyTable: "No capabilities found.",
                zeroRecords: "No matching capabilities found."
            },
            dom: "<'row'<'col-sm-12'tr>><'p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2'<'text-muted extra-small font-mono'i><'d-flex justify-content-end'p>>"
        });

        $('#permSearchInput').on('keyup', function() {
            permsTable.search(this.value).draw();
        });

        $('#permDomainFilter').on('change', function() {
            permsTable.column(0).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
        });

        $('#permStatusFilter').on('change', function() {
            permsTable.column(3).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
        });

        // 2. Initialize Personnel DataTable
        usersTable = $('#personnelTable').DataTable({
            pageLength: 25,
            lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
            order: [[4, 'asc'], [0, 'asc']],
            autoWidth: false,
            language: {
                search: "",
                emptyTable: "No institutional personnel found in registry.",
                zeroRecords: "No matching personnel found."
            },
            dom: "<'row'<'col-sm-12'tr>><'p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2'<'text-muted extra-small font-mono'i><'d-flex justify-content-end'p>>"
        });

        $('#userSearchInput').on('keyup', function() {
            usersTable.search(this.value).draw();
        });

        $('#userCampusFilter').on('change', function() {
            usersTable.column(2).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
        });

        $('#userStatusFilter').on('change', function() {
            usersTable.column(4).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
        });
    });

    /* ========================================================================= */
    /* 1. CAPABILITIES TOGGLE LOGIC                                              */
    /* ========================================================================= */
    function onPermSwitchToggle(switchEl) {
        if (!isSuperAdmin) return;
        const isChecked = switchEl.checked;
        const permKey = switchEl.value;
        const slug = slugify(permKey);
        const row = document.getElementById('row-perm-' + slug);
        const badge = document.getElementById('badge-perm-' + slug);

        if (isChecked) {
            rolePermissions.add(permKey);
            if (row) row.classList.add('bg-success-subtle', 'bg-opacity-10');
            if (badge) {
                badge.className = 'badge bg-success text-white font-mono perm-badge-label';
                badge.textContent = 'GRANTED';
            }
        } else {
            rolePermissions.delete(permKey);
            if (row) row.classList.remove('bg-success-subtle', 'bg-opacity-10');
            if (badge) {
                badge.className = 'badge bg-light text-muted border font-mono perm-badge-label';
                badge.textContent = 'REVOKED';
            }
        }

        // Update row data in DataTable cache for search/filtering
        if (permsTable && row) {
            permsTable.row(row).invalidate();
        }

        updateOverallPermCounters();
        document.getElementById('perms-dirty-indicator')?.classList.remove('d-none');
    }

    function bulkToggleVisiblePermissions(grant) {
        if (!isSuperAdmin) return;
        $('#permissionsTable tbody tr:visible').each(function() {
            const sw = $(this).find('.perm-switch')[0];
            if (sw && sw.checked !== grant) {
                sw.checked = grant;
                onPermSwitchToggle(sw);
            }
        });
    }

    function updateOverallPermCounters() {
        const total = rolePermissions.size;
        const heroCounter = document.getElementById('heroPermsGranted');
        const tabCounter = document.getElementById('tabPermsCount');
        if (heroCounter) heroCounter.textContent = total;
        if (tabCounter) tabCounter.textContent = total;
    }

    function savePermissionsMatrix() {
        if (!isSuperAdmin) {
            if (window.Swal) Swal.fire('Unauthorized', 'Only the SuperAdmin can modify the permissions capability matrix.', 'warning');
            return;
        }

        const permNames = Array.from(rolePermissions);
        const btn = document.getElementById('btn-save-perms');
        const btnText = document.getElementById('btn-save-perms-text');
        const origText = btnText ? btnText.textContent : 'Save Matrix';
        if (btnText) btnText.textContent = 'Saving Matrix...';
        if (btn) btn.disabled = true;

        fetch("{{ route('dss.settings.roles.permissions', $role->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ permissions: permNames })
        })
        .then(res => res.json())
        .then(data => {
            if (btnText) btnText.textContent = origText;
            if (btn) btn.disabled = false;
            if (data.result) {
                document.getElementById('perms-dirty-indicator')?.classList.add('d-none');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Capabilities Saved!',
                        text: data.message,
                        confirmButtonColor: '#00674f',
                        timer: 2000
                    });
                }
            } else {
                if (window.Swal) Swal.fire('Error', data.message || 'Failed to update capabilities matrix.', 'error');
            }
        })
        .catch(err => {
            if (btnText) btnText.textContent = origText;
            if (btn) btn.disabled = false;
            if (window.Swal) Swal.fire('Error', 'An error occurred while saving the capabilities matrix.', 'error');
        });
    }

    /* ========================================================================= */
    /* 2. PERSONNEL TOGGLE LOGIC                                                 */
    /* ========================================================================= */
    function onUserSwitchToggle(switchEl) {
        const isChecked = switchEl.checked;
        const userId = parseInt(switchEl.value);
        const row = document.getElementById('row-user-' + userId);
        const badge = document.getElementById('badge-user-' + userId);

        if (isChecked) {
            roleUserIds.add(userId);
            if (row) row.classList.add('bg-success-subtle', 'bg-opacity-10');
            if (badge) {
                badge.className = 'badge bg-success text-white font-mono user-badge-label';
                badge.textContent = 'ASSIGNED';
            }
        } else {
            roleUserIds.delete(userId);
            if (row) row.classList.remove('bg-success-subtle', 'bg-opacity-10');
            if (badge) {
                badge.className = 'badge bg-light text-muted border font-mono user-badge-label';
                badge.textContent = 'UNASSIGNED';
            }
        }

        if (usersTable && row) {
            usersTable.row(row).invalidate();
        }

        updateOverallUserCounters();
        document.getElementById('personnel-dirty-indicator')?.classList.remove('d-none');
    }

    function bulkToggleVisiblePersonnel(assign) {
        $('#personnelTable tbody tr:visible').each(function() {
            const sw = $(this).find('.user-switch')[0];
            if (sw && sw.checked !== assign) {
                sw.checked = assign;
                onUserSwitchToggle(sw);
            }
        });
    }

    function updateOverallUserCounters() {
        const total = roleUserIds.size;
        const tabUsers = document.getElementById('tabUsersCount');
        if (tabUsers) tabUsers.textContent = total;
    }

    function savePersonnelAssignments() {
        const userIds = Array.from(roleUserIds);
        const btn = document.getElementById('btn-save-personnel');
        const btnText = document.getElementById('btn-save-personnel-text');
        const origText = btnText ? btnText.textContent : 'Save Personnel';
        if (btnText) btnText.textContent = 'Saving...';
        if (btn) btn.disabled = true;

        fetch("{{ route('dss.settings.roles.personnel', $role->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(res => res.json())
        .then(data => {
            if (btnText) btnText.textContent = origText;
            if (btn) btn.disabled = false;
            if (data.result) {
                document.getElementById('personnel-dirty-indicator')?.classList.add('d-none');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Personnel Updated!',
                        text: data.message,
                        confirmButtonColor: '#00674f',
                        timer: 2000
                    });
                }
            } else {
                if (window.Swal) Swal.fire('Error', data.message || 'Failed to update personnel assignment.', 'error');
            }
        })
        .catch(err => {
            if (btnText) btnText.textContent = origText;
            if (btn) btn.disabled = false;
            if (window.Swal) Swal.fire('Error', 'An error occurred while saving personnel assignments.', 'error');
        });
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
</script>
@endsection
