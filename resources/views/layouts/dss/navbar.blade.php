@php
    $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $currentTheme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $uiTemplate = $currentTheme->ui_template ?? 'flup_minimal';
    $fy = $fy ?? ($inst->current_fy ?: date('Y'));
    $user = auth()->user();
    $navLogo = $inst->logo_variants['sidebar']['url'] ?? ($inst->logo_variants['app_icon']['url'] ?? ($inst->logo_path ? asset($inst->logo_path) : null));
    $pendingSigCount = 0;
    if (auth()->check()) {
        $pendingSigCount = \App\Models\SaaS\DocumentSignatory::pending()
            ->forSignatoryUser($user)
            ->where('tenant_id', $inst->id ?? ($user->tenant_id ?? 1))
            ->count();
    }
    $uiTemplateLabels = [
        'flup_minimal' => 'Flup Minimal',
        'print_manzil' => 'Print Manzil',
        'material_able' => 'Material Able',
    ];
@endphp

@if($uiTemplate === 'flup_minimal')
    {{-- ====================================================================
         1. FLUP CLEAN SAAS HEADER (Inline Title + Time Period Pill)
         ==================================================================== --}}
    <header class="flup-topbar">
        <div class="d-flex align-items-center gap-2.5">
            <button type="button" class="pm-icon-square-btn d-lg-none" onclick="window.toggleMaSidebar()" title="Menu">
                <i class="mdi mdi-menu fs-5"></i>
            </button>
            <h1 class="flup-page-title">{{ $cleanPageTitle ?? 'Dashboard' }}</h1>
        </div>

        <div class="d-flex align-items-center gap-2.5 flex-wrap">
            <!-- Quick DB-Backed UI Template Switcher -->
            <div class="dropdown">
                <button class="flup-period-pill bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <i class="mdi mdi-layers-outline text-primary" style="font-size: 16px;"></i>
                    <span>UI: <strong class="text-dark">{{ $uiTemplateLabels[$uiTemplate] ?? 'Flup Minimal' }}</strong></span>
                    <i class="mdi mdi-chevron-down text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" style="border-radius: 12px; font-size: 12.5px; min-width: 225px;">
                    <li class="px-3 py-1.5 text-muted extra-small fw-bold text-uppercase">Select UI Layout (Saved in DB)</li>
                    <li>
                        <button type="button" class="dropdown-item py-2 d-flex align-items-center justify-content-between {{ $uiTemplate === 'flup_minimal' ? 'fw-bold text-primary bg-light' : '' }}" onclick="window.switchGlobalUiTemplate('flup_minimal')">
                            <span><i class="mdi mdi-view-quilt-outline me-2"></i>Flup — Clean SaaS</span>
                            @if($uiTemplate === 'flup_minimal') <i class="mdi mdi-check text-primary"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-2 d-flex align-items-center justify-content-between {{ $uiTemplate === 'print_manzil' ? 'fw-bold text-primary bg-light' : '' }}" onclick="window.switchGlobalUiTemplate('print_manzil')">
                            <span><i class="mdi mdi-view-dashboard-variant-outline me-2"></i>Print Manzil — Bento</span>
                            @if($uiTemplate === 'print_manzil') <i class="mdi mdi-check text-primary"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-2 d-flex align-items-center justify-content-between {{ $uiTemplate === 'material_able' ? 'fw-bold text-primary bg-light' : '' }}" onclick="window.switchGlobalUiTemplate('material_able')">
                            <span><i class="mdi mdi-page-layout-header me-2"></i>Material Able — Classic</span>
                            @if($uiTemplate === 'material_able') <i class="mdi mdi-check text-primary"></i> @endif
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-1.5 text-primary small fw-semibold" href="{{ route('dss.settings.theme') }}">
                            <i class="mdi mdi-palette-outline me-1"></i>Open Full Theme Studio
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Flup Time Period Pill -->
            <div class="flup-period-pill">
                <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size: 15px;"></i>
                <span>Time period:</span>
                <select id="dss-fy-select" data-no-select2="true" onchange="window.location.search = '?fy=' + this.value"
                        class="bg-transparent border-0 fw-bold text-dark p-0" style="font-size: 12.5px; outline: none; cursor: pointer;">
                    @for($y = date('Y') + 1; $y >= 2022; $y--)
                        <option value="{{ $y }}" class="text-dark" {{ $fy == $y ? 'selected' : '' }}>FY {{ $y }} ({{ $y }}.01 - {{ $y }}.12)</option>
                    @endfor
                </select>
            </div>

            <!-- Notifications Bell -->
            <div class="dropdown">
                <button class="pm-icon-square-btn bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                    <i class="mdi mdi-bell-outline" style="font-size: 18px;"></i>
                    <span class="position-absolute badge rounded-circle bg-danger dss-notification-badge d-none" style="top: -4px; right: -4px; width: 8px; height: 8px; padding: 0; font-size: 0;">0</span>
                    @if($pendingSigCount > 0)
                        <span class="pm-notif-count-badge">{{ $pendingSigCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 mt-2" style="width: 330px; border-radius: 14px; overflow: hidden;">
                    <div class="px-3 py-2.5 bg-white border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small">Notifications (<span class="dss-notification-count">{{ $pendingSigCount }}</span>)</span>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted extra-small" onclick="window.markAllNotificationsRead()">Clear all</button>
                    </div>
                    <div class="dss-desktop-notification-prompt d-none px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <span class="extra-small text-muted">Enable desktop alerts</span>
                        <button type="button" class="btn btn-primary btn-sm py-0.5 px-2 extra-small" onclick="window.requestDesktopNotificationPermission()">Enable</button>
                    </div>
                    <div class="dss-notification-list overflow-auto" style="max-height: 300px;">
                        <div class="text-center py-4 px-3 text-muted extra-small">No unread notifications</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

@elseif($uiTemplate === 'material_able')
    {{-- ====================================================================
         2. MATERIAL ABLE FULL-WIDTH COLORED TOPBAR
         ==================================================================== --}}
    <header class="ma-topbar">
        <div class="ma-topbar-brand">
            <a href="{{ route('dss.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                @if($navLogo)
                    <img src="{{ $navLogo }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain; background: #fff; border-radius: 4px; padding: 2px;">
                @else
                    <i class="mdi mdi-image-filter-hdr fs-4 text-white"></i>
                @endif
                <span class="fw-bold text-truncate" style="font-size: 16px;">
                    {{ $inst->school_acronym ?: 'SNSU' }} Extension
                </span>
            </a>
            <button type="button" class="ma-topbar-icon-btn ms-auto" onclick="window.toggleMaSidebar()" title="Toggle Navigation">
                <i class="mdi mdi-menu fs-5"></i>
            </button>
        </div>

        <div class="ma-topbar-main">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="ma-topbar-icon-btn d-none d-sm-inline-flex" onclick="window.toggleMaFullscreen()" title="Toggle Fullscreen">
                    <i class="mdi mdi-arrow-expand-all" style="font-size: 17px;"></i>
                </button>
                <span class="d-none d-lg-inline-block text-white text-opacity-75 small ms-1">
                    {{ $inst->school_name ?? 'Surigao del Norte State University' }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Quick DB-Backed UI Template Switcher -->
                <div class="dropdown">
                    <button class="btn btn-sm border-0 text-white d-inline-flex align-items-center gap-1" style="background: rgba(255,255,255,0.16);" type="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-layers-outline"></i>
                        <span class="d-none d-md-inline">UI: {{ $uiTemplateLabels[$uiTemplate] ?? 'Material Able' }}</span>
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="font-size: 12.5px; min-width: 220px;">
                        <li class="px-3 py-1.5 text-muted extra-small fw-bold text-uppercase">Select UI Layout (Saved in DB)</li>
                        <li><button type="button" class="dropdown-item py-2" onclick="window.switchGlobalUiTemplate('flup_minimal')"><i class="mdi mdi-view-quilt-outline me-2"></i>Flup — Clean SaaS</button></li>
                        <li><button type="button" class="dropdown-item py-2" onclick="window.switchGlobalUiTemplate('print_manzil')"><i class="mdi mdi-view-dashboard-variant-outline me-2"></i>Print Manzil — Bento</button></li>
                        <li><button type="button" class="dropdown-item py-2 fw-bold text-primary bg-light" onclick="window.switchGlobalUiTemplate('material_able')"><i class="mdi mdi-page-layout-header me-2"></i>Material Able — Classic</button></li>
                    </ul>
                </div>

                <!-- Fiscal Year Switcher -->
                <div class="d-none d-md-flex align-items-center gap-1 px-2.5 py-1 rounded" style="background: rgba(255,255,255,0.15);">
                    <span class="text-white text-opacity-75" style="font-size: 11.5px;">FY:</span>
                    <select id="dss-fy-select" data-no-select2="true" onchange="window.location.search = '?fy=' + this.value"
                            class="bg-transparent border-0 text-white fw-semibold" style="font-size: 12.5px; outline: none; cursor: pointer;">
                        @for($y = date('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" class="text-dark" {{ $fy == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn border-0 d-flex align-items-center gap-2 text-white p-0" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background: rgba(255,255,255,0.22); border: 2px solid #fff; font-size: 11.5px;">
                            {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                        </div>
                        <span class="d-none d-sm-inline fw-normal" style="font-size: 13px;">{{ $user->name ?? 'Administrator' }}</span>
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="font-size: 13px; min-width: 200px;">
                        <li><a class="dropdown-item py-2" href="{{ route('dss.settings.theme') }}"><i class="mdi mdi-palette-outline me-2"></i>Theme &amp; UI Studio</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('dss.settings.institution') }}"><i class="mdi mdi-domain me-2"></i>University Profile</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="mdi mdi-logout me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

@else
    {{-- ====================================================================
         3. PRINT MANZIL DETACHED SOLID WHITE TOPBAR
         ==================================================================== --}}
    <header class="pm-topbar">
        <div class="d-flex align-items-center gap-2.5 min-w-0">
            <button type="button" class="pm-icon-square-btn" onclick="window.toggleMaSidebar()" title="Toggle Sidebar">
                <i class="mdi mdi-menu fs-5"></i>
            </button>

            <div class="dropdown">
                <a href="#" class="pm-shop-selector" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="pm-shop-icon">
                        @if($navLogo)
                            <img src="{{ $navLogo }}" alt="Logo" style="width: 22px; height: 22px; object-fit: contain;">
                        @else
                            <i class="mdi mdi-school-outline"></i>
                        @endif
                    </div>
                    <span class="text-truncate d-none d-sm-inline" style="max-width: 210px;">
                        {{ $inst->school_name ?? 'Surigao del Norte State University' }}
                    </span>
                    <i class="mdi mdi-chevron-down text-muted" style="font-size: 16px;"></i>
                </a>
                <ul class="dropdown-menu shadow border-0 mt-2" style="border-radius: 14px; font-size: 13px; min-width: 230px;">
                    <li class="px-3 py-2 border-bottom bg-light">
                        <div class="fw-bold text-dark">{{ $inst->school_acronym ?: 'SNSU' }} RIE Division</div>
                        <div class="extra-small text-muted">{{ $cleanPageTitle ?? 'Dashboard' }}</div>
                    </li>
                    <li><a class="dropdown-item py-2" href="{{ route('dss.dashboard') }}"><i class="mdi mdi-view-dashboard-outline me-2 text-primary"></i>Dashboard</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('dss.settings.theme') }}"><i class="mdi mdi-palette-outline me-2 text-muted"></i>Theme &amp; UI Studio</a></li>
                </ul>
            </div>
        </div>

        <div class="d-none d-lg-flex align-items-center gap-2.5">
            <a href="{{ route('dss.rie.budgets.index') }}" class="pm-cta-pill">
                <span class="pm-cta-pill-icon">
                    <i class="mdi mdi-hand-pointing-up"></i>
                </span>
                <span>Check Budget Status</span>
            </a>

            <div class="pm-date-pill">
                <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size: 16px;"></i>
                <span>{{ now()->format('l, d M. Y') }}</span>
                <span class="text-muted mx-1">|</span>
                <span class="text-muted" style="font-size: 11.5px;">FY:</span>
                <select id="dss-fy-select" data-no-select2="true" onchange="window.location.search = '?fy=' + this.value"
                        class="bg-transparent border-0 fw-bold text-primary p-0" style="font-size: 12.5px; outline: none; cursor: pointer;">
                    @for($y = date('Y') + 1; $y >= 2022; $y--)
                        <option value="{{ $y }}" class="text-dark" {{ $fy == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Quick DB-Backed UI Template Switcher -->
            <div class="dropdown">
                <button class="pm-date-pill bg-white" type="button" data-bs-toggle="dropdown" style="cursor: pointer;">
                    <i class="mdi mdi-layers-outline text-primary"></i>
                    <span>{{ $uiTemplateLabels[$uiTemplate] ?? 'Print Manzil' }}</span>
                    <i class="mdi mdi-chevron-down text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; font-size: 12.5px; min-width: 225px;">
                    <li class="px-3 py-1.5 text-muted extra-small fw-bold text-uppercase">Select UI Layout (Saved in DB)</li>
                    <li><button type="button" class="dropdown-item py-2" onclick="window.switchGlobalUiTemplate('flup_minimal')"><i class="mdi mdi-view-quilt-outline me-2"></i>Flup — Clean SaaS</button></li>
                    <li><button type="button" class="dropdown-item py-2 fw-bold text-primary bg-light" onclick="window.switchGlobalUiTemplate('print_manzil')"><i class="mdi mdi-view-dashboard-variant-outline me-2"></i>Print Manzil — Bento</button></li>
                    <li><button type="button" class="dropdown-item py-2" onclick="window.switchGlobalUiTemplate('material_able')"><i class="mdi mdi-page-layout-header me-2"></i>Material Able — Classic</button></li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2.5">
            <div class="dropdown">
                <button class="pm-icon-square-btn" type="button" data-bs-toggle="dropdown" title="Notifications">
                    <i class="mdi mdi-bell-outline" style="font-size: 19px;"></i>
                    <span class="position-absolute badge rounded-circle bg-danger dss-notification-badge d-none" style="top: -4px; right: -4px; width: 8px; height: 8px; padding: 0; font-size: 0;">0</span>
                    <span class="pm-notif-count-badge">{{ $pendingSigCount > 0 ? $pendingSigCount : 2 }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 mt-2" style="width: 330px; border-radius: 16px; overflow: hidden;">
                    <div class="px-3 py-2.5 bg-white border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small">Notifications (<span class="dss-notification-count">{{ $pendingSigCount }}</span>)</span>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted extra-small" onclick="window.markAllNotificationsRead()">Clear all</button>
                    </div>
                    <div class="dss-desktop-notification-prompt d-none px-3 py-2 bg-light border-bottom d-flex align-items-center justify-content-between">
                        <span class="extra-small text-muted">Enable desktop alerts</span>
                        <button type="button" class="btn btn-primary btn-sm py-0.5 px-2 extra-small" onclick="window.requestDesktopNotificationPermission()">Enable</button>
                    </div>
                    <div class="dss-notification-list overflow-auto" style="max-height: 300px;">
                        <div class="text-center py-4 px-3 text-muted extra-small">No unread notifications</div>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn border-0 d-flex align-items-center gap-2 p-0" type="button" data-bs-toggle="dropdown">
                    <div class="pm-topbar-avatar">
                        {{ strtoupper(substr($user->name ?? 'JD', 0, 2)) }}
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="font-size: 13px; min-width: 210px; border-radius: 14px;">
                    <li class="px-3 py-2.5 border-bottom bg-light">
                        <div class="fw-bold text-dark">{{ $user->name ?? 'Administrator' }}</div>
                        <div class="extra-small text-muted">{{ $user->role ?? 'Extension Staff' }}</div>
                    </li>
                    <li><a class="dropdown-item py-2" href="{{ route('dss.settings.theme') }}"><i class="mdi mdi-palette-outline me-2 text-primary"></i>Theme &amp; UI Studio</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('dss.settings.institution') }}"><i class="mdi mdi-domain me-2 text-muted"></i>Institution Profile</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="mdi mdi-logout me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>
@endif
