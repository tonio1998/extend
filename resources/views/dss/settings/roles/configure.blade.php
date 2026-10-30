@extends('layouts.dss.app')

@section('title', 'Configure Role: ' . $role->name . ' — Institutional Roles — SNSU')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 extra-small font-mono">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">DSS Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.settings.roles.index') }}" class="text-decoration-none text-muted">Roles Catalog</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">{{ $role->name }}</li>
            </ol>
        </nav>

        <a href="{{ route('dss.settings.roles.index') }}" class="btn btn-sm btn-white border shadow-2xs text-muted fw-bold font-mono px-3 d-flex align-items-center gap-1.5">
            <i class="mdi mdi-arrow-left"></i>
            <span>Back to Roles Catalog</span>
        </a>
    </div>

    <!-- Role Studio Header -->
    <div class="role-studio-header mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="role-icon-box">
                    <i class="mdi mdi-shield-account-outline fs-2"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold text-dark mb-0 font-mono">{{ $role->name }}</h4>
                        <span class="badge bg-light text-dark border font-mono px-2 py-0.5" style="font-size: 11px;">
                            Guard: {{ $role->guard_name }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border font-mono px-2 py-0.5" style="font-size: 11px;">
                            Tenant Team #{{ $tenant->id }} ({{ $tenant->school_acronym ?: $tenant->name }})
                        </span>
                    </div>
                    <p class="text-muted small mb-0">
                        Interactive Drag &amp; Drop Studio for assigning institutional personnel and configuring system authority matrices.
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="bg-white border rounded-3 px-3 py-2 text-center shadow-2xs">
                    <div class="extra-small text-muted font-mono text-uppercase">Assigned Personnel</div>
                    <div class="h5 fw-bold text-dark mb-0 font-mono" id="hero-assigned-users-count">{{ $assignedUsers->count() }}</div>
                </div>
                <div class="bg-white border rounded-3 px-3 py-2 text-center shadow-2xs">
                    <div class="extra-small text-muted font-mono text-uppercase">Granted Capabilities</div>
                    <div class="h5 fw-bold text-primary mb-0 font-mono" id="hero-assigned-perms-count">{{ $assignedPermissions->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Studio Tabs Navigation -->
    <ul class="nav nav-tabs mb-3 border-bottom" id="roleStudioTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-2" id="tab-personnel-btn" data-bs-toggle="tab" data-bs-target="#tab-personnel" type="button" role="tab">
                <i class="mdi mdi-account-group-outline fs-5"></i>
                <span>Institutional Personnel (Drag &amp; Drop)</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill font-mono" id="tab-personnel-badge">{{ $assignedUsers->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2" id="tab-permissions-btn" data-bs-toggle="tab" data-bs-target="#tab-permissions" type="button" role="tab">
                <i class="mdi mdi-shield-key-outline fs-5"></i>
                <span>Capabilities Matrix (Drag &amp; Drop)</span>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill font-mono" id="tab-perms-badge">{{ $assignedPermissions->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="roleStudioTabsContent">
        <!-- ========================================================================= -->
        <!-- TAB 1: INSTITUTIONAL PERSONNEL ASSIGNMENT (DRAG & DROP)                    -->
        <!-- ========================================================================= -->
        <div class="tab-pane fade show active" id="tab-personnel" role="tabpanel">
            <div class="alert alert-info py-2 px-3 mb-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 small">
                    <i class="mdi mdi-gesture-swipe fs-5 text-info"></i>
                    <span>
                        <strong>Drag &amp; Drop:</strong> Move institutional users between the <strong>Available</strong> and <strong>Assigned</strong> columns. You can also click the quick action buttons on each card.
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="personnel-dirty-indicator" class="badge bg-warning text-dark font-mono d-none">
                        <i class="mdi mdi-circle-medium"></i> Unsaved Changes
                    </span>
                    <button type="button" class="btn btn-sm btn-dss-primary px-3 shadow-xs font-mono fw-bold d-flex align-items-center gap-1.5" onclick="savePersonnelAssignments()">
                        <i class="mdi mdi-content-save-outline"></i>
                        <span id="btn-save-personnel-text">Save Personnel Assignments</span>
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <!-- Column 1: Available Personnel -->
                <div class="col-lg-6">
                    <div class="drag-drop-card">
                        <div class="drag-drop-card-header">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-account-multiple-outline text-muted fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Available Personnel</h6>
                                </div>
                                <span class="badge bg-light text-dark border font-mono" id="badge-avail-users-count">
                                    {{ $availableUsers->count() }} Users
                                </span>
                            </div>

                            <!-- User Search Bar -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" id="search-avail-users" class="form-control border-start-0" placeholder="Search by name, email, department..." oninput="filterUserList('available')">
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="clearUserSearch('available')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Drop Container: Available Users -->
                        <div class="drag-drop-list-container" id="available-users-container">
                            <div class="empty-drop-placeholder" id="placeholder-avail-users">
                                <i class="mdi mdi-check-all fs-1 text-success opacity-50 mb-2"></i>
                                <h6 class="text-dark fw-bold mb-1">All Personnel Assigned</h6>
                                <p class="small text-muted mb-0">Every registered institutional user has been allocated to this role.</p>
                            </div>

                            @foreach($availableUsers as $user)
                                @php
                                    $initials = collect(explode(' ', trim($user->name)))
                                        ->filter()
                                        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                    $uCampusName = $user->campus?->campus_name ?? $user->campus?->name ?? '';
                                    $uDeptName = $user->department?->unit_name ?: ($user->department?->dept_name ?? $user->department?->name ?? '');
                                @endphp
                                <div class="dnd-item-card user-card-item" data-user-id="{{ $user->id }}" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-dept="{{ strtolower($uDeptName) }}" data-campus="{{ strtolower($uCampusName) }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2.5 flex-grow-1 overflow-hidden">
                                            <i class="mdi mdi-drag drag-handle" title="Drag to assign"></i>
                                            <div class="user-avatar-circle">
                                                {{ $initials ?: 'U' }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate small" title="{{ $user->name }}">{{ $user->name }}</div>
                                                <div class="extra-small text-muted font-mono text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                    @if($uCampusName)
                                                        <span class="badge bg-light text-muted border extra-small font-mono py-0">{{ $uCampusName }}</span>
                                                    @endif
                                                    @if($uDeptName)
                                                        <span class="badge bg-light text-muted border extra-small font-mono py-0">{{ $uDeptName }}</span>
                                                    @endif
                                                    <span class="badge bg-secondary-subtle text-secondary extra-small font-mono py-0">Current: {{ $user->role ?: 'Unassigned' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 font-mono flex-shrink-0" onclick="moveUserCard({{ $user->id }}, 'assigned')">
                                            <span>Assign</span>
                                            <i class="mdi mdi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Column 2: Assigned Personnel to this Role -->
                <div class="col-lg-6">
                    <div class="drag-drop-card" style="border-color: #a7f3d0;">
                        <div class="drag-drop-card-header" style="background: #f0fdf4; border-bottom-color: #dcfce7;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-account-check-outline text-success fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Assigned to {{ $role->name }}</h6>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono" id="badge-assigned-users-count">
                                    {{ $assignedUsers->count() }} Assigned
                                </span>
                            </div>

                            <!-- Assigned User Search Bar -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" id="search-assigned-users" class="form-control border-start-0" placeholder="Filter assigned personnel..." oninput="filterUserList('assigned')">
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="clearUserSearch('assigned')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Drop Container: Assigned Users -->
                        <div class="drag-drop-list-container" id="assigned-users-container" style="background: #f0fdf4;">
                            <div class="empty-drop-placeholder" id="placeholder-assigned-users">
                                <i class="mdi mdi-account-arrow-right-outline fs-1 text-muted opacity-50 mb-2"></i>
                                <h6 class="text-dark fw-bold mb-1">No Personnel Assigned Yet</h6>
                                <p class="small text-muted mb-0">Drag user cards from the left column here to assign them to {{ $role->name }}.</p>
                            </div>

                            @foreach($assignedUsers as $user)
                                @php
                                    $initials = collect(explode(' ', trim($user->name)))
                                        ->filter()
                                        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                    $uCampusName = $user->campus?->campus_name ?? $user->campus?->name ?? '';
                                    $uDeptName = $user->department?->unit_name ?: ($user->department?->dept_name ?? $user->department?->name ?? '');
                                @endphp
                                <div class="dnd-item-card user-card-item" data-user-id="{{ $user->id }}" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-dept="{{ strtolower($uDeptName) }}" data-campus="{{ strtolower($uCampusName) }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2.5 flex-grow-1 overflow-hidden">
                                            <i class="mdi mdi-drag drag-handle" title="Drag to remove or reorder"></i>
                                            <div class="user-avatar-circle" style="background: #dcfce7; color: #166534;">
                                                {{ $initials ?: 'U' }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate small" title="{{ $user->name }}">{{ $user->name }}</div>
                                                <div class="extra-small text-muted font-mono text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                    @if($uCampusName)
                                                        <span class="badge bg-light text-muted border extra-small font-mono py-0">{{ $uCampusName }}</span>
                                                    @endif
                                                    @if($uDeptName)
                                                        <span class="badge bg-light text-muted border extra-small font-mono py-0">{{ $uDeptName }}</span>
                                                    @endif
                                                    <span class="badge bg-success-subtle text-success extra-small font-mono py-0">Role: {{ $role->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 font-mono flex-shrink-0" onclick="moveUserCard({{ $user->id }}, 'available')">
                                            <i class="mdi mdi-close me-1"></i>
                                            <span>Remove</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: CAPABILITIES MATRIX (DRAG & DROP)                                  -->
        <!-- ========================================================================= -->
        <div class="tab-pane fade" id="tab-permissions" role="tabpanel">
            <div class="alert @if($isSuperAdmin) alert-info @else alert-warning @endif py-2 px-3 mb-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 small">
                    <i class="mdi @if($isSuperAdmin) mdi-shield-crown text-warning @else mdi-shield-lock-outline text-warning @endif fs-5"></i>
                    <span>
                        @if($isSuperAdmin)
                            <strong>SuperAdmin Authority:</strong> Drag system capabilities between <strong>Available</strong> and <strong>Granted</strong> columns to configure role authorization.
                        @else
                            <strong>Read-Only Inspection:</strong> Only the SuperAdmin can alter the capabilities matrix of global roles. Institutional Admins can assign existing roles and grant direct permissions to users in <a href="{{ route('dss.settings.users.index') }}" class="fw-bold text-dark text-decoration-underline">User Management</a>.
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="perms-dirty-indicator" class="badge bg-warning text-dark font-mono d-none">
                        <i class="mdi mdi-circle-medium"></i> Unsaved Changes
                    </span>
                    @if($isSuperAdmin)
                        <button type="button" class="btn btn-sm btn-dss-primary px-3 shadow-xs font-mono fw-bold d-flex align-items-center gap-1.5" onclick="savePermissionsMatrix()">
                            <i class="mdi mdi-content-save-outline"></i>
                            <span id="btn-save-perms-text">Save Permissions Matrix</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Domain Category Filter Pills -->
            <div class="d-flex align-items-center gap-1.5 mb-3 flex-wrap">
                <span class="extra-small text-muted font-mono me-1">Filter Domain:</span>
                <span class="filter-tab-pill active" data-domain="all" onclick="filterByPermDomain('all')">All Domains</span>
                @foreach($permissionCatalog as $domain => $domainPerms)
                    <span class="filter-tab-pill" data-domain="{{ Str::slug($domain) }}" onclick="filterByPermDomain('{{ Str::slug($domain) }}')">
                        {{ $domain }}
                    </span>
                @endforeach
            </div>

            <div class="row g-3">
                <!-- Column 1: Available Capabilities -->
                <div class="col-lg-6">
                    <div class="drag-drop-card">
                        <div class="drag-drop-card-header">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-key-outline text-muted fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Available Capabilities</h6>
                                </div>
                                <span class="badge bg-light text-dark border font-mono" id="badge-avail-perms-count">
                                    {{ $availablePermissions->count() }} Items
                                </span>
                            </div>

                            <!-- Perm Search -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" id="search-avail-perms" class="form-control border-start-0" placeholder="Search capabilities by key or description..." oninput="filterPermList('available')">
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="clearPermSearch('available')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Drop Container: Available Perms -->
                        <div class="drag-drop-list-container" id="available-perms-container">
                            <div class="empty-drop-placeholder" id="placeholder-avail-perms">
                                <i class="mdi mdi-check-all fs-1 text-success opacity-50 mb-2"></i>
                                <h6 class="text-dark fw-bold mb-1">All Capabilities Granted</h6>
                                <p class="small text-muted mb-0">Every available system permission has been assigned to this role.</p>
                            </div>

                            @foreach($availablePermissions as $perm)
                                @php
                                    $domainTitle = 'Institutional Governance & Administration';
                                    $desc = $perm->name;
                                    foreach($permissionCatalog as $dTitle => $dPerms) {
                                        if (isset($dPerms[$perm->name])) {
                                            $domainTitle = $dTitle;
                                            $desc = $dPerms[$perm->name];
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="dnd-item-card perm-card-item" data-perm-name="{{ $perm->name }}" data-domain="{{ Str::slug($domainTitle) }}" data-text="{{ strtolower($perm->name . ' ' . $desc . ' ' . $domainTitle) }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-start gap-2 flex-grow-1 overflow-hidden">
                                            @if($isSuperAdmin)
                                                <i class="mdi mdi-drag drag-handle mt-1" title="Drag to grant"></i>
                                            @endif
                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                                    <span class="badge bg-secondary-subtle text-secondary extra-small font-mono py-0">{{ $domainTitle }}</span>
                                                    <code class="fw-bold text-dark font-mono small">{{ $perm->name }}</code>
                                                </div>
                                                <div class="small text-muted">{{ $desc }}</div>
                                            </div>
                                        </div>
                                        @if($isSuperAdmin)
                                            <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 font-mono flex-shrink-0" onclick="movePermCard('{{ $perm->name }}', 'assigned')">
                                                <span>Grant</span>
                                                <i class="mdi mdi-arrow-right ms-1"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Column 2: Granted Capabilities to Role -->
                <div class="col-lg-6">
                    <div class="drag-drop-card" style="border-color: #bfdbfe;">
                        <div class="drag-drop-card-header" style="background: #eff6ff; border-bottom-color: #dbeafe;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-shield-check text-primary fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Granted to {{ $role->name }}</h6>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono" id="badge-assigned-perms-count">
                                    {{ $assignedPermissions->count() }} Capabilities
                                </span>
                            </div>

                            <!-- Assigned Perm Search -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" id="search-assigned-perms" class="form-control border-start-0" placeholder="Filter granted capabilities..." oninput="filterPermList('assigned')">
                                <button class="btn btn-outline-secondary border-start-0" type="button" onclick="clearPermSearch('assigned')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Drop Container: Assigned Perms -->
                        <div class="drag-drop-list-container" id="assigned-perms-container" style="background: #eff6ff;">
                            <div class="empty-drop-placeholder" id="placeholder-assigned-perms">
                                <i class="mdi mdi-shield-alert-outline fs-1 text-muted opacity-50 mb-2"></i>
                                <h6 class="text-dark fw-bold mb-1">No Capabilities Granted</h6>
                                <p class="small text-muted mb-0">Drag capabilities from the left column here to authorize {{ $role->name }}.</p>
                            </div>

                            @foreach($assignedPermissions as $perm)
                                @php
                                    $domainTitle = 'Institutional Governance & Administration';
                                    $desc = $perm->name;
                                    foreach($permissionCatalog as $dTitle => $dPerms) {
                                        if (isset($dPerms[$perm->name])) {
                                            $domainTitle = $dTitle;
                                            $desc = $dPerms[$perm->name];
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="dnd-item-card perm-card-item" data-perm-name="{{ $perm->name }}" data-domain="{{ Str::slug($domainTitle) }}" data-text="{{ strtolower($perm->name . ' ' . $desc . ' ' . $domainTitle) }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-start gap-2 flex-grow-1 overflow-hidden">
                                            @if($isSuperAdmin)
                                                <i class="mdi mdi-drag drag-handle mt-1" title="Drag to remove"></i>
                                            @endif
                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                                    <span class="badge bg-primary-subtle text-primary extra-small font-mono py-0">{{ $domainTitle }}</span>
                                                    <code class="fw-bold text-dark font-mono small">{{ $perm->name }}</code>
                                                </div>
                                                <div class="small text-muted">{{ $desc }}</div>
                                            </div>
                                        </div>
                                        @if($isSuperAdmin)
                                            <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 font-mono flex-shrink-0" onclick="movePermCard('{{ $perm->name }}', 'available')">
                                                <i class="mdi mdi-close me-1"></i>
                                                <span>Revoke</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
    const roleId = {{ $role->id }};
    const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentPermDomainFilter = 'all';

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Sortable for Personnel Assignment
        initPersonnelSortable();

        // Initialize Sortable for Capabilities Matrix
        initPermissionsSortable();

        // Update initial counters and empty placeholders
        updatePersonnelCounters();
        updatePermCounters();
    });

    /* ========================================================================= */
    /* 1. PERSONNEL ASSIGNMENT DRAG & DROP LOGIC                                */
    /* ========================================================================= */
    function initPersonnelSortable() {
        const availEl = document.getElementById('available-users-container');
        const assignedEl = document.getElementById('assigned-users-container');

        if (!availEl || !assignedEl) return;

        Sortable.create(availEl, {
            group: 'personnel-transfer',
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle, .user-card-item',
            onEnd: function(evt) {
                if (evt.from !== evt.to) {
                    toggleUserCardBtn(evt.item, evt.to.id === 'assigned-users-container' ? 'assigned' : 'available');
                }
                updatePersonnelCounters();
                markPersonnelDirty();
            }
        });

        Sortable.create(assignedEl, {
            group: 'personnel-transfer',
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle, .user-card-item',
            onEnd: function(evt) {
                if (evt.from !== evt.to) {
                    toggleUserCardBtn(evt.item, evt.to.id === 'assigned-users-container' ? 'assigned' : 'available');
                }
                updatePersonnelCounters();
                markPersonnelDirty();
            }
        });
    }

    function toggleUserCardBtn(card, targetContainer) {
        const btn = card.querySelector('button');
        const avatar = card.querySelector('.user-avatar-circle');
        const userId = card.getAttribute('data-user-id');

        if (targetContainer === 'assigned') {
            if (btn) {
                btn.className = 'btn btn-xs btn-outline-danger py-1 px-2 font-mono flex-shrink-0';
                btn.setAttribute('onclick', `moveUserCard(${userId}, 'available')`);
                btn.innerHTML = '<i class="mdi mdi-close me-1"></i><span>Remove</span>';
            }
            if (avatar) {
                avatar.style.background = '#dcfce7';
                avatar.style.color = '#166534';
            }
        } else {
            if (btn) {
                btn.className = 'btn btn-xs btn-outline-primary py-1 px-2 font-mono flex-shrink-0';
                btn.setAttribute('onclick', `moveUserCard(${userId}, 'assigned')`);
                btn.innerHTML = '<span>Assign</span><i class="mdi mdi-arrow-right ms-1"></i>';
            }
            if (avatar) {
                avatar.style.background = '#e2e8f0';
                avatar.style.color = '#334155';
            }
        }
    }

    function moveUserCard(userId, target) {
        const card = document.querySelector(`.user-card-item[data-user-id="${userId}"]`);
        if (!card) return;

        const targetContainer = document.getElementById(target === 'assigned' ? 'assigned-users-container' : 'available-users-container');
        if (!targetContainer) return;

        targetContainer.appendChild(card);
        toggleUserCardBtn(card, target);
        updatePersonnelCounters();
        markPersonnelDirty();
    }

    function updatePersonnelCounters() {
        const availCards = document.querySelectorAll('#available-users-container .user-card-item');
        const assignedCards = document.querySelectorAll('#assigned-users-container .user-card-item');

        const availCount = availCards.length;
        const assignedCount = assignedCards.length;

        document.getElementById('badge-avail-users-count').textContent = `${availCount} Users`;
        document.getElementById('badge-assigned-users-count').textContent = `${assignedCount} Assigned`;
        document.getElementById('hero-assigned-users-count').textContent = assignedCount;
        document.getElementById('tab-personnel-badge').textContent = assignedCount;

        // Toggle placeholders
        const availPlaceholder = document.getElementById('placeholder-avail-users');
        const assignedPlaceholder = document.getElementById('placeholder-assigned-users');

        if (availPlaceholder) availPlaceholder.style.display = availCount === 0 ? 'block' : 'none';
        if (assignedPlaceholder) assignedPlaceholder.style.display = assignedCount === 0 ? 'block' : 'none';
    }

    function markPersonnelDirty() {
        const indicator = document.getElementById('personnel-dirty-indicator');
        if (indicator) indicator.classList.remove('d-none');
    }

    function filterUserList(col) {
        const query = document.getElementById(col === 'assigned' ? 'search-assigned-users' : 'search-avail-users').value.toLowerCase().trim();
        const containerId = col === 'assigned' ? 'assigned-users-container' : 'available-users-container';
        const cards = document.querySelectorAll(`#${containerId} .user-card-item`);

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const email = card.getAttribute('data-email') || '';
            const dept = card.getAttribute('data-dept') || '';
            const campus = card.getAttribute('data-campus') || '';

            const matches = !query || name.includes(query) || email.includes(query) || dept.includes(query) || campus.includes(query);
            card.style.display = matches ? 'block' : 'none';
        });
    }

    function clearUserSearch(col) {
        const input = document.getElementById(col === 'assigned' ? 'search-assigned-users' : 'search-avail-users');
        if (input) {
            input.value = '';
            filterUserList(col);
        }
    }

    function savePersonnelAssignments() {
        const assignedCards = document.querySelectorAll('#assigned-users-container .user-card-item');
        const userIds = Array.from(assignedCards).map(card => parseInt(card.getAttribute('data-user-id'))).filter(id => !isNaN(id));

        const btnText = document.getElementById('btn-save-personnel-text');
        const origText = btnText.textContent;
        btnText.textContent = 'Saving...';

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
            btnText.textContent = origText;
            if (data.result) {
                document.getElementById('personnel-dirty-indicator').classList.add('d-none');
                Swal.fire({
                    icon: 'success',
                    title: 'Personnel Updated!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 2000
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to update personnel assignment.', 'error');
            }
        })
        .catch(err => {
            btnText.textContent = origText;
            Swal.fire('Error', 'An error occurred while saving personnel assignments.', 'error');
        });
    }

    /* ========================================================================= */
    /* 2. CAPABILITIES MATRIX DRAG & DROP LOGIC                                  */
    /* ========================================================================= */
    function initPermissionsSortable() {
        if (!isSuperAdmin) return;

        const availEl = document.getElementById('available-perms-container');
        const assignedEl = document.getElementById('assigned-perms-container');

        if (!availEl || !assignedEl) return;

        Sortable.create(availEl, {
            group: 'perms-transfer',
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle, .perm-card-item',
            onEnd: function(evt) {
                if (evt.from !== evt.to) {
                    togglePermCardBtn(evt.item, evt.to.id === 'assigned-perms-container' ? 'assigned' : 'available');
                }
                updatePermCounters();
                markPermDirty();
            }
        });

        Sortable.create(assignedEl, {
            group: 'perms-transfer',
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle, .perm-card-item',
            onEnd: function(evt) {
                if (evt.from !== evt.to) {
                    togglePermCardBtn(evt.item, evt.to.id === 'assigned-perms-container' ? 'assigned' : 'available');
                }
                updatePermCounters();
                markPermDirty();
            }
        });
    }

    function togglePermCardBtn(card, targetContainer) {
        if (!isSuperAdmin) return;
        const btn = card.querySelector('button');
        const permName = card.getAttribute('data-perm-name');

        if (targetContainer === 'assigned') {
            if (btn) {
                btn.className = 'btn btn-xs btn-outline-danger py-1 px-2 font-mono flex-shrink-0';
                btn.setAttribute('onclick', `movePermCard('${permName}', 'available')`);
                btn.innerHTML = '<i class="mdi mdi-close me-1"></i><span>Revoke</span>';
            }
        } else {
            if (btn) {
                btn.className = 'btn btn-xs btn-outline-primary py-1 px-2 font-mono flex-shrink-0';
                btn.setAttribute('onclick', `movePermCard('${permName}', 'assigned')`);
                btn.innerHTML = '<span>Grant</span><i class="mdi mdi-arrow-right ms-1"></i>';
            }
        }
    }

    function movePermCard(permName, target) {
        if (!isSuperAdmin) return;
        const card = document.querySelector(`.perm-card-item[data-perm-name="${permName}"]`);
        if (!card) return;

        const targetContainer = document.getElementById(target === 'assigned' ? 'assigned-perms-container' : 'available-perms-container');
        if (!targetContainer) return;

        targetContainer.appendChild(card);
        togglePermCardBtn(card, target);
        updatePermCounters();
        markPermDirty();
    }

    function updatePermCounters() {
        const availCards = document.querySelectorAll('#available-perms-container .perm-card-item');
        const assignedCards = document.querySelectorAll('#assigned-perms-container .perm-card-item');

        const availCount = availCards.length;
        const assignedCount = assignedCards.length;

        document.getElementById('badge-avail-perms-count').textContent = `${availCount} Items`;
        document.getElementById('badge-assigned-perms-count').textContent = `${assignedCount} Capabilities`;
        document.getElementById('hero-assigned-perms-count').textContent = assignedCount;
        document.getElementById('tab-perms-badge').textContent = assignedCount;

        const availPlaceholder = document.getElementById('placeholder-avail-perms');
        const assignedPlaceholder = document.getElementById('placeholder-assigned-perms');

        if (availPlaceholder) availPlaceholder.style.display = availCount === 0 ? 'block' : 'none';
        if (assignedPlaceholder) assignedPlaceholder.style.display = assignedCount === 0 ? 'block' : 'none';
    }

    function markPermDirty() {
        const indicator = document.getElementById('perms-dirty-indicator');
        if (indicator) indicator.classList.remove('d-none');
    }

    function filterByPermDomain(domainSlug) {
        currentPermDomainFilter = domainSlug;

        document.querySelectorAll('.filter-tab-pill').forEach(pill => {
            if (pill.getAttribute('data-domain') === domainSlug) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        });

        filterPermList('available');
        filterPermList('assigned');
    }

    function filterPermList(col) {
        const query = document.getElementById(col === 'assigned' ? 'search-assigned-perms' : 'search-avail-perms').value.toLowerCase().trim();
        const containerId = col === 'assigned' ? 'assigned-perms-container' : 'available-perms-container';
        const cards = document.querySelectorAll(`#${containerId} .perm-card-item`);

        cards.forEach(card => {
            const domain = card.getAttribute('data-domain') || '';
            const text = card.getAttribute('data-text') || '';

            const matchesDomain = (currentPermDomainFilter === 'all') || (domain === currentPermDomainFilter);
            const matchesQuery = !query || text.includes(query);

            card.style.display = (matchesDomain && matchesQuery) ? 'block' : 'none';
        });
    }

    function clearPermSearch(col) {
        const input = document.getElementById(col === 'assigned' ? 'search-assigned-perms' : 'search-avail-perms');
        if (input) {
            input.value = '';
            filterPermList(col);
        }
    }

    function savePermissionsMatrix() {
        if (!isSuperAdmin) {
            Swal.fire('Unauthorized', 'Only the SuperAdmin can modify the permissions capability matrix.', 'warning');
            return;
        }

        const assignedCards = document.querySelectorAll('#assigned-perms-container .perm-card-item');
        const permNames = Array.from(assignedCards).map(card => card.getAttribute('data-perm-name')).filter(Boolean);

        const btnText = document.getElementById('btn-save-perms-text');
        const origText = btnText.textContent;
        btnText.textContent = 'Saving Matrix...';

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
            btnText.textContent = origText;
            if (data.result) {
                document.getElementById('perms-dirty-indicator').classList.add('d-none');
                Swal.fire({
                    icon: 'success',
                    title: 'Permissions Matrix Saved!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 2000
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to update permissions matrix.', 'error');
            }
        })
        .catch(err => {
            btnText.textContent = origText;
            Swal.fire('Error', 'An error occurred while saving the permissions matrix.', 'error');
        });
    }
</script>
@endsection
