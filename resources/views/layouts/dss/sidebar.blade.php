@php
    $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $currentTheme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $uiTemplate = $currentTheme->ui_template ?? 'flup_minimal';
    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
    $user = auth()->user();
    $navLogo = $inst->logo_variants['sidebar']['url'] ?? ($inst->logo_variants['app_icon']['url'] ?? ($inst->logo_path ? asset($inst->logo_path) : null));

    $isPersonnelActive = $currentRoute && (
        str_starts_with($currentRoute, 'dss.settings.employees') ||
        str_starts_with($currentRoute, 'dss.settings.designations') ||
        str_starts_with($currentRoute, 'dss.settings.users') ||
        str_starts_with($currentRoute, 'dss.settings.roles') ||
        str_starts_with($currentRoute, 'dss.settings.permissions')
    );

    $isSystemConfigActive = $currentRoute && (
        in_array($currentRoute, ['dss.settings.institution', 'dss.settings.institution.campuses'], true) ||
        str_starts_with($currentRoute, 'dss.settings.campuses') ||
        str_starts_with($currentRoute, 'dss.settings.rie_priorities') ||
        str_starts_with($currentRoute, 'dss.settings.signatories') ||
        $currentRoute === 'dss.settings.theme'
    );

    $isForSignatureActive = $currentRoute && str_starts_with($currentRoute, 'dss.signatures');

    $pendingSigCount = 0;
    if (auth()->check()) {
        $u = auth()->user();
        $tId = $inst->id ?? ($u->tenant_id ?? 1);
        $pendingSigCount = \App\Models\SaaS\DocumentSignatory::pending()
            ->forSignatoryUser($u)
            ->where('tenant_id', $tId)
            ->count();
    }
@endphp

<aside class="dss-sidebar" id="maSidebar">
    {{-- ====================================================================
         SIDEBAR HEADER (ADAPTS TO SELECTED UI TEMPLATE)
         ==================================================================== --}}
    @if($uiTemplate === 'flup_minimal')
        <!-- FLUP HEADER: Brand + Collapsible Rail Toggle (< / >) -->
        <div class="flup-sidebar-header">
            <a href="{{ route('dss.dashboard') }}" class="flup-brand-link min-w-0">
                <div class="flup-brand-icon">
                    @if($navLogo)
                        <img src="{{ $navLogo }}" alt="Logo" style="width: 22px; height: 22px; object-fit: contain;">
                    @else
                        <i class="mdi mdi-sprout"></i>
                    @endif
                </div>
                <span class="flup-brand-text text-truncate">{{ $inst->school_acronym ?: 'SNSU' }}</span>
            </a>
            <button type="button" class="flup-rail-toggle" onclick="window.toggleMaSidebar()" title="Collapse / Expand Rail">
                <i class="mdi mdi-chevron-left" style="font-size: 16px;"></i>
            </button>
        </div>
    @elseif($uiTemplate === 'material_able')
        <!-- MATERIAL ABLE HEADER: Mountain Profile Banner + Search -->
        <div class="ma-sidebar-user-banner">
            <div class="ma-sidebar-user-avatar">
                {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
            </div>
            <a class="text-white fw-semibold text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 13.5px;" data-bs-toggle="collapse" href="#maSidebarUserMenu" role="button" aria-expanded="false">
                <span>{{ $user->name ?? 'Administrator' }}</span>
                <i class="mdi mdi-menu-down" style="font-size: 16px;"></i>
            </a>
            <div style="font-size: 11px; color: rgba(255,255,255,0.78); margin-top: 1px;">
                {{ $user->role ?? 'Extension Staff' }}
            </div>
        </div>
        <div class="collapse bg-light border-bottom px-3 py-2" id="maSidebarUserMenu">
            <a href="{{ route('dss.settings.institution') }}" class="d-flex align-items-center gap-2 py-1 text-dark text-decoration-none small">
                <i class="mdi mdi-account-outline"></i><span>University Profile</span>
            </a>
            <a href="{{ route('dss.settings.theme') }}" class="d-flex align-items-center gap-2 py-1 text-dark text-decoration-none small">
                <i class="mdi mdi-palette-outline"></i><span>Theme &amp; UI Studio</span>
            </a>
        </div>
        <div class="px-3 pt-2.5 pb-1">
            <div class="d-flex align-items-center border-bottom pb-1">
                <i class="mdi mdi-magnify text-muted me-2"></i>
                <input type="text" id="maSidebarSearchInput" class="border-0 bg-transparent w-100 small" style="outline: none;" placeholder="Search Navigation" oninput="window.filterMaSidebar(this.value)">
            </div>
        </div>
    @else
        <!-- PRINT MANZIL HEADER: Brand Box + Subtitle -->
        <a href="{{ route('dss.dashboard') }}" class="pm-sidebar-brand">
            <div class="pm-brand-logo-box">
                @if($navLogo)
                    <img src="{{ $navLogo }}" alt="Logo" style="width: 26px; height: 26px; object-fit: contain;">
                @else
                    <span>{{ substr($inst->school_acronym ?: 'S', 0, 1) }}.</span>
                @endif
            </div>
            <div class="min-w-0">
                <span class="pm-brand-title text-truncate">{{ $inst->school_acronym ?: 'SNSU' }} Extend.</span>
                <span class="pm-brand-subtitle text-truncate">Elegant Extension Dashboard</span>
            </div>
        </a>
    @endif

    {{-- ====================================================================
         SIDEBAR NAVIGATION LINKS
         ==================================================================== --}}
    <div class="dss-sidebar-body" id="maSidebarNavContainer">
        <div class="dss-sidebar-section-title">
            <span class="flup-sec-full">Extension Workflow</span>
            <span class="flup-sec-short">W</span>
        </div>

        <a href="{{ route('dss.dashboard') }}" class="dss-nav-link {{ $currentRoute === 'dss.dashboard' ? 'active' : '' }}" title="Dashboard">
            <div class="pm-nav-left">
                <i class="mdi mdi-view-dashboard-outline pm-nav-icon"></i>
                <span class="text-truncate">Dashboard</span>
            </div>
            @if($uiTemplate === 'print_manzil')
                <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
            @endif
        </a>

        @can('rie.plans.view')
            <a href="{{ route('dss.rie.plans.index') }}" class="dss-nav-link {{ str_starts_with($currentRoute, 'dss.rie.plans') ? 'active' : '' }}" title="Extension Plans">
                <div class="pm-nav-left">
                    <i class="mdi mdi-calendar-multiselect-outline pm-nav-icon"></i>
                    @hasanyrole(['Project Leader', 'Faculty', 'Employee'])
                        <span class="text-truncate">My Extension Plans</span>
                    @else
                        <span class="text-truncate">Extension Plans</span>
                    @endhasanyrole
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcan

        @can('rie.proposals.view')
            <a href="{{ route('dss.rie.proposals.index') }}" class="dss-nav-link {{ str_starts_with($currentRoute, 'dss.rie.proposals') ? 'active' : '' }}" title="Proposals">
                <div class="pm-nav-left">
                    <i class="mdi mdi-file-document-edit-outline pm-nav-icon"></i>
                    @hasanyrole(['Project Leader', 'Faculty', 'Employee'])
                        <span class="text-truncate">My Proposals</span>
                    @else
                        <span class="text-truncate">Proposals</span>
                    @endhasanyrole
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcan

        @canany(['rie.proposals.view', 'rie.plans.view'])
            <a href="{{ route('dss.rie.calendar.index') }}" class="dss-nav-link {{ str_starts_with($currentRoute, 'dss.rie.calendar') ? 'active' : '' }}" title="Extension Calendar & Duration Monitoring">
                <div class="pm-nav-left">
                    <i class="mdi mdi-calendar-clock-outline pm-nav-icon"></i>
                    <span class="text-truncate">Extension Calendar</span>
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcanany

        @can('rie.budgets.view')
            <a href="{{ route('dss.rie.budgets.index') }}" class="dss-nav-link {{ str_starts_with($currentRoute, 'dss.rie.budgets') ? 'active' : '' }}" title="Extension Budgets">
                <div class="pm-nav-left">
                    <i class="mdi mdi-chart-box-outline pm-nav-icon"></i>
                    <span class="text-truncate">Extension Budgets</span>
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcan

        @can('rie.tracking.view')
            <a href="{{ route('dss.rie.tracking.index') }}" class="dss-nav-link {{ str_starts_with($currentRoute, 'dss.rie.tracking') ? 'active' : '' }}" title="Connect Tracking">
                <div class="pm-nav-left">
                    <i class="mdi mdi-map-outline pm-nav-icon"></i>
                    <span class="text-truncate">Tracking</span>
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcan

        <div class="dss-sidebar-section-title">
            <span class="flup-sec-full">Approvals &amp; Ledger</span>
            <span class="flup-sec-short">A</span>
        </div>

        @can('rie.signatures.sign')
            <a href="{{ route('dss.signatures.index') }}" class="dss-nav-link {{ $isForSignatureActive ? 'active' : '' }}" title="Sign Documents">
                <div class="pm-nav-left">
                    <i class="mdi mdi-draw-pen pm-nav-icon"></i>
                    <span class="text-truncate">Sign Documents</span>
                </div>
                @if($pendingSigCount > 0)
                    <span class="pm-feed-badge ms-auto">{{ $pendingSigCount }}</span>
                @elseif($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcan

        @canany(['rie.documents.view', 'rie.documents.manage'])
            <a href="{{ route('dss.documents.repository') }}" class="dss-nav-link {{ $currentRoute && str_starts_with($currentRoute, 'dss.document') ? 'active' : '' }}" title="Document Repository">
                <div class="pm-nav-left">
                    <i class="mdi mdi-folder-outline pm-nav-icon"></i>
                    <span class="text-truncate">Repository</span>
                </div>
                @if($uiTemplate === 'print_manzil')
                    <i class="mdi mdi-chevron-right pm-nav-arrow"></i>
                @endif
            </a>
        @endcanany

        @hasanyrole(['SuperAdmin', 'Admin'])
            <div class="dss-sidebar-section-title">
                <span class="flup-sec-full">System</span>
                <span class="flup-sec-short">S</span>
            </div>

            <div class="dss-sidebar-dropdown">
                <a href="#personnelSubmenu" class="dss-nav-link {{ $isPersonnelActive ? 'active' : '' }}"
                   data-bs-toggle="collapse" role="button" aria-expanded="{{ $isPersonnelActive ? 'true' : 'false' }}" title="Personnel & Access">
                    <div class="pm-nav-left">
                        <i class="mdi mdi-account-group-outline pm-nav-icon"></i>
                        <span class="text-truncate">Personnel &amp; Access</span>
                    </div>
                    <i class="mdi mdi-chevron-down pm-nav-arrow" style="font-size: 14px;"></i>
                </a>
                <div class="collapse ps-3 {{ $isPersonnelActive ? 'show' : '' }}" id="personnelSubmenu">
                    @can('system.users.assign')
                        <a href="{{ route('dss.settings.employees.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.employees') ? 'active' : '' }}">
                            <i class="mdi mdi-circle-small"></i>
                            <span>Manage Staff</span>
                        </a>
                    @endcan
                    @can('system.hierarchy.manage')
                        <a href="{{ route('dss.settings.designations.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.designations') ? 'active' : '' }}">
                            <i class="mdi mdi-circle-small"></i>
                            <span>Manage Positions</span>
                        </a>
                    @endcan
                    @can('system.users.assign')
                        <a href="{{ route('dss.settings.users.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.users') ? 'active' : '' }}">
                            <i class="mdi mdi-circle-small"></i>
                            <span>Manage Users</span>
                        </a>
                    @endcan
                    @can('system.roles.manage')
                        <a href="{{ route('dss.settings.roles.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.roles') || str_starts_with($currentRoute, 'dss.settings.permissions') ? 'active' : '' }}">
                            <i class="mdi mdi-circle-small"></i>
                            <span>Roles &amp; Permissions</span>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="dss-sidebar-dropdown">
                <a href="#systemSetupSubmenu" class="dss-nav-link {{ $isSystemConfigActive ? 'active' : '' }}"
                   data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSystemConfigActive ? 'true' : 'false' }}" title="System Settings">
                    <div class="pm-nav-left">
                        <i class="mdi mdi-cog-outline pm-nav-icon"></i>
                        <span class="text-truncate">Settings</span>
                    </div>
                    <i class="mdi mdi-chevron-down pm-nav-arrow" style="font-size: 14px;"></i>
                </a>
                <div class="collapse ps-3 {{ $isSystemConfigActive ? 'show' : '' }}" id="systemSetupSubmenu">
                    <a href="{{ route('dss.settings.institution') }}" class="dss-subnav-link {{ $currentRoute === 'dss.settings.institution' ? 'active' : '' }}">
                        <i class="mdi mdi-circle-small"></i>
                        <span>Configure Profile</span>
                    </a>
                    <a href="{{ route('dss.settings.institution.campuses') }}" class="dss-subnav-link {{ $currentRoute === 'dss.settings.institution.campuses' ? 'active' : '' }}">
                        <i class="mdi mdi-circle-small"></i>
                        <span>Manage Campuses</span>
                    </a>
                    <a href="{{ route('dss.settings.rie_priorities.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.rie_priorities') ? 'active' : '' }}">
                        <i class="mdi mdi-circle-small"></i>
                        <span>RIE Priorities</span>
                    </a>
                    <a href="{{ route('dss.settings.signatories.index') }}" class="dss-subnav-link {{ str_starts_with($currentRoute, 'dss.settings.signatories') ? 'active' : '' }}">
                        <i class="mdi mdi-circle-small"></i>
                        <span>Define Signatories</span>
                    </a>
                    <a href="{{ route('dss.settings.theme') }}" class="dss-subnav-link {{ $currentRoute === 'dss.settings.theme' ? 'active' : '' }}">
                        <i class="mdi mdi-circle-small"></i>
                        <span>Theme &amp; UI Studio</span>
                    </a>
                </div>
            </div>
        @endhasanyrole
    </div>

    {{-- ====================================================================
         SIDEBAR FOOTER (ADAPTS TO SELECTED UI TEMPLATE)
         ==================================================================== --}}
    @if($uiTemplate === 'flup_minimal')
        <!-- FLUP FOOTER: Harper Nelson Profile Row + Log out Button -->
        <div class="flup-sidebar-footer">
            <div class="flup-footer-user">
                <div class="user-avatar-circle" style="width: 36px; height: 36px;">
                    {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                </div>
                <div class="flup-user-info min-w-0">
                    <div class="fw-bold text-dark text-truncate" style="font-size: 12.5px; line-height: 1.2;">
                        {{ $user->name ?? 'Administrator' }}
                    </div>
                    <div class="text-muted text-truncate" style="font-size: 11px;">
                        {{ $user->role ?? 'Admin Manager' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="flup-logout-btn" title="Log out">
                    <i class="mdi mdi-logout" style="font-size: 18px;"></i>
                    <span class="flup-logout-text">Log out</span>
                </button>
            </form>
        </div>
    @elseif($uiTemplate === 'print_manzil')
        <!-- PRINT MANZIL FOOTER: Jordan Nico Contact Card + Workspace Mini Bar -->
        <div class="pm-sidebar-footer">
            <div class="pm-user-detail-card">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <div class="pm-user-detail-avatar">
                        {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 13px;">
                            {{ $user->name ?? 'Administrator' }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 11px;">
                            {{ $user->role ?? 'Extension Officer' }}
                        </div>
                        <div class="fw-semibold text-dark text-truncate" style="font-size: 11px;">
                            {{ $inst->school_acronym ?: 'SNSU' }} RIE Division
                        </div>
                    </div>
                </div>

                <div class="pm-user-contact-row">
                    <div class="pm-user-contact-icon">
                        <i class="mdi mdi-phone"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="text-muted" style="font-size: 10px; line-height: 1.1;">Campus Line</div>
                        <div class="fw-semibold text-dark text-truncate" style="font-size: 11px;">
                            {{ $inst->contact_phone ?: '(086) 826-1738' }}
                        </div>
                    </div>
                    <i class="mdi mdi-chevron-right text-muted"></i>
                </div>

                <div class="pm-user-contact-row">
                    <div class="pm-user-contact-icon">
                        <i class="mdi mdi-email"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="text-muted" style="font-size: 10px; line-height: 1.1;">Email</div>
                        <div class="fw-semibold text-dark text-truncate" style="font-size: 11px;">
                            {{ $user->email ?? ($inst->contact_email ?: 'rie@snsu.edu.ph') }}
                        </div>
                    </div>
                    <i class="mdi mdi-chevron-right text-muted"></i>
                </div>
            </div>

            <div class="pm-user-mini-bar dropdown">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="user-avatar-circle" style="width: 32px; height: 32px; font-size: 11px;">
                        {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 12px; line-height: 1.2;">
                            {{ $inst->school_acronym ?: 'SNSU' }} Workspace
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 10.5px;">
                            FY {{ $inst->current_fy ?: date('Y') }} Active
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm p-1 border-0 text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-dots-horizontal fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 12.5px;">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.settings.theme') }}">
                            <i class="mdi mdi-palette-outline text-primary"></i>
                            <span>Theme &amp; UI Studio</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                <i class="mdi mdi-logout"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    @endif
</aside>
