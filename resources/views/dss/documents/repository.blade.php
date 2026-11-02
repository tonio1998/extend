@php
    $currentTheme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $primary = $currentTheme->primary_color ?? '#00674f';
    $secondary = $currentTheme->secondary_color ?? '#64748b';
    $accent = $currentTheme->accent_color ?? '#f59e0b';
    $radius = $currentTheme->border_radius ?? '12px';
    $sidebarTheme = $currentTheme->sidebar_theme ?? 'light';

    $sidebarBg = $currentTheme->sidebar_bg ?: ($sidebarTheme === 'dark' ? '#0f172a' : '#f8fafc');
    $sidebarText = $currentTheme->sidebar_text ?: ($sidebarTheme === 'dark' ? '#cbd5e1' : '#334155');
    $sidebarActiveBg = $currentTheme->sidebar_active_bg ?: ($sidebarTheme === 'dark' ? 'rgba(4, 120, 87, 0.22)' : 'color-mix(in srgb, ' . $primary . ' 12%, #ffffff)');
    $sidebarActiveText = $currentTheme->sidebar_active_text ?: ($sidebarTheme === 'dark' ? '#34d399' : $primary);
    $sidebarBorder = $currentTheme->sidebar_border ?: ($sidebarTheme === 'dark' ? '#1e293b' : '#e2e8f0');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Institutional Document Repository — {{ $inst?->school_name ?: 'SNSU' }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @if(!empty($inst?->favicon_url))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $inst->favicon_url }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- jQuery & Bootstrap 5 JS -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <!-- Master DSS Institutional Theme Styles -->
    @include('layouts.dss.theme-styles')

    @stack('styles')

    <style>
        :root {
            --repo-primary: {{ $primary }};
            --repo-primary-hover: color-mix(in srgb, {{ $primary }} 85%, black);
            --repo-primary-soft: color-mix(in srgb, {{ $primary }} 10%, #ffffff);
            --repo-sidebar-bg: {{ $sidebarBg }};
            --repo-sidebar-text: {{ $sidebarText }};
            --repo-sidebar-border: {{ $sidebarBorder }};
            --repo-sidebar-active: {{ $sidebarActiveBg }};
            --repo-sidebar-active-text: {{ $sidebarActiveText }};
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
            color: #0f172a;
            font-family: var(--dss-font, 'Inter', sans-serif) !important;
            -webkit-font-smoothing: antialiased;
        }

        .repo-crm-shell {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
        }

        /* ─── 1. TOP COMMAND BAR ────────────────────────────────────────── */
        .repo-topbar {
            height: 64px;
            min-height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .repo-brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .repo-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--repo-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 2px 4px color-mix(in srgb, var(--repo-primary) 30%, transparent);
            flex-shrink: 0;
        }

        .repo-brand-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.015em;
        }

        .repo-brand-subtitle {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }

        .repo-search-input-group {
            width: 420px;
            position: relative;
        }

        .repo-search-input-group input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding-left: 36px;
            padding-right: 58px;
            font-size: 12.5px;
            height: 36px;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .repo-search-input-group input:focus {
            background: #ffffff;
            border-color: var(--repo-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--repo-primary) 12%, transparent);
            outline: none;
        }

        .repo-search-input-group .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }

        .repo-search-kbd {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-family: inherit;
            padding: 2px 6px;
            border-radius: 4px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            pointer-events: none;
        }

        /* Segmented View Switcher */
        .repo-segmented-switch {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 8px;
            display: inline-flex;
            gap: 2px;
            border: 1px solid #e2e8f0;
        }

        .repo-segmented-btn {
            border: none;
            background: transparent;
            color: #64748b;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .repo-segmented-btn:hover {
            color: #0f172a;
        }

        .repo-segmented-btn.active {
            background: #ffffff;
            color: var(--repo-primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* ─── 2. WORKSPACE LAYOUT ───────────────────────────────────────── */
        .repo-workspace {
            display: flex;
            flex: 1;
            height: calc(100vh - 64px);
            overflow: hidden;
        }

        /* ─── DEDICATED SIDEBAR ─────────────────────────────────────────── */
        .repo-sidebar {
            width: 270px;
            min-width: 270px;
            background: var(--repo-sidebar-bg);
            color: var(--repo-sidebar-text);
            border-right: 1px solid var(--repo-sidebar-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.15rem 0.85rem;
            overflow-y: auto;
        }

        .repo-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .repo-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .repo-upload-cta {
            background: var(--repo-primary);
            color: #ffffff;
            border: none;
            border-radius: 9px;
            padding: 9px 14px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 5px color-mix(in srgb, var(--repo-primary) 25%, transparent);
        }

        .repo-upload-cta:hover {
            background: var(--repo-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px color-mix(in srgb, var(--repo-primary) 35%, transparent);
        }

        .repo-sidebar-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            padding: 0.65rem 0.65rem 0.25rem 0.65rem;
        }

        .repo-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border-radius: 8px;
            color: var(--repo-sidebar-text);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.15s ease;
        }

        .repo-nav-item:hover {
            background: rgba(0, 0, 0, 0.04);
            color: #0f172a;
        }

        .repo-nav-item.active {
            background: var(--repo-sidebar-active);
            color: var(--repo-sidebar-active-text);
            font-weight: 600;
        }

        .repo-nav-item.active .badge {
            background: var(--repo-primary) !important;
            color: #ffffff !important;
        }

        .repo-pill-filter {
            font-size: 11px;
            font-weight: 500;
            border-radius: 6px;
            padding: 3px 8px;
            text-decoration: none;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .repo-storage-widget {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        /* ─── MAIN CONTENT CANVAS ───────────────────────────────────────── */
        .repo-canvas {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            padding: 1.5rem 1.75rem;
            background: #f8fafc;
        }

        .repo-canvas::-webkit-scrollbar {
            width: 6px;
        }
        .repo-canvas::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* ─── 3. METRIC KPI CARDS ───────────────────────────────────────── */
        .repo-metric-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .repo-metric-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .repo-metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            flex-shrink: 0;
        }

        .repo-metric-num {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.025em;
        }

        /* ─── 4. ASSET CARDS (GRID VIEW) ───────────────────────────────── */
        .repo-doc-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .repo-doc-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .repo-doc-card-thumb {
            height: 60px;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .repo-hash-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 11px;
            color: #475569;
            padding: 2px 7px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .repo-hash-badge:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── 8. REPOSITORY PAGINATION ──────────────────────────────────── */
        .repo-pagination-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .repo-pagination {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .repo-pagination .page-item {
            list-style: none;
            display: inline-flex;
        }

        .repo-pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            line-height: 1;
        }

        .repo-pagination .page-item .page-link:hover {
            background: #f1f5f9;
            color: var(--repo-primary);
            border-color: #cbd5e1;
        }

        .repo-pagination .page-item.active .page-link {
            background: var(--repo-primary) !important;
            border-color: var(--repo-primary) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 103, 79, 0.25);
            cursor: default;
        }

        .repo-pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
            pointer-events: none;
        }

        .repo-pagination .page-item .page-link.dots {
            border: none;
            background: transparent;
            color: #94a3b8;
            min-width: 20px;
            padding: 0 4px;
            cursor: default;
        }

        /* Fallback for native Bootstrap 5 pagination if rendered inside repo-canvas */
        .repo-canvas .pagination {
            margin-bottom: 0;
            gap: 4px;
            display: inline-flex;
            align-items: center;
        }

        .repo-canvas .pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px !important;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .repo-canvas .pagination .page-item .page-link:hover {
            background: #f1f5f9;
            color: var(--repo-primary);
            border-color: #cbd5e1;
        }

        .repo-canvas .pagination .page-item.active .page-link {
            background: var(--repo-primary) !important;
            border-color: var(--repo-primary) !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 103, 79, 0.25);
        }

        .repo-canvas .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .repo-canvas nav > div.d-none.flex-sm-fill.d-sm-flex > div:first-child {
            display: none !important;
        }

        .repo-canvas nav > div.d-flex.justify-content-between.flex-fill.d-sm-none {
            display: none !important;
        }
    </style>
</head>
<body>

<div class="repo-crm-shell">

    <!-- ====================================================================
         1. DEDICATED REPOSITORY COMMAND BAR
         ==================================================================== -->
    <header class="repo-topbar">
        <!-- Brand & Institutional Identity -->
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dss.documents.repository') }}" class="repo-brand-link">
                @if(!empty($inst?->logo_url))
                    <img src="{{ $inst->logo_url }}" alt="School Seal" style="width: 38px; height: 38px; object-fit: contain;" class="rounded-2">
                @else
                    <div class="repo-brand-icon">
                        <i class="mdi mdi-shield-folder-outline"></i>
                    </div>
                @endif
                <div>
                    <div class="repo-brand-title">{{ $inst?->school_name ?: 'Surigao del Norte State University' }}</div>
                    <div class="repo-brand-subtitle d-flex align-items-center gap-1">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-mono" style="font-size: 10px; font-weight: 600;">DIGITAL ASSET VAULT</span>
                        <span>Institutional Document Repository</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Global Search Bar -->
        <form action="{{ route('dss.documents.repository') }}" method="GET" class="repo-search-input-group d-none d-md-block" id="topbarSearchForm" onsubmit="event.preventDefault(); triggerRepoSearch(this.search.value);">
            <i class="mdi mdi-magnify search-icon"></i>
            <input type="text" name="search" id="topbarSearchInput" class="form-control" placeholder="Search repository by title, filename, or checksum..." value="{{ $search }}" autocomplete="off">
            <span class="repo-search-kbd">Ctrl K</span>
        </form>

        <!-- Right Controls -->
        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Switcher -->
            <div class="repo-segmented-switch me-1">
                <a href="javascript:void(0)" id="btnViewGrid" onclick="switchRepoView('grid')"
                   class="repo-segmented-btn {{ $viewMode === 'grid' ? 'active' : '' }}" title="Grid Cards View">
                    <i class="mdi mdi-view-grid-outline"></i>
                    <span>Grid</span>
                </a>
                <a href="javascript:void(0)" id="btnViewTable" onclick="switchRepoView('table')"
                   class="repo-segmented-btn {{ $viewMode === 'table' ? 'active' : '' }}" title="Table Explorer View">
                    <i class="mdi mdi-view-list-outline"></i>
                    <span>Table</span>
                </a>
            </div>

            <!-- Audit Trail Modal Trigger -->
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#vaultAuditTrailModal" title="Audit Trail">
                <i class="mdi mdi-history text-secondary"></i>
                <span class="d-none d-sm-inline">Audit Log</span>
            </button>

            <!-- Exit to Main DSS Portal -->
            <a href="{{ route('dss.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium d-flex align-items-center gap-1" title="Return to Main DSS System">
                <i class="mdi mdi-arrow-left text-secondary"></i>
                <span class="d-none d-sm-inline">Exit to DSS</span>
            </a>

            <!-- User Avatar Bubble -->
            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold ms-1" style="width: 34px; height: 34px; font-size: 13px; background: var(--repo-primary);" title="{{ Auth::user()?->name }}">
                {{ strtoupper(substr(Auth::user()?->name ?: 'U', 0, 1)) }}
            </div>
        </div>
    </header>

    <!-- ====================================================================
         2. WORKSPACE BODY: DEDICATED SIDEBAR + CONTENT CANVAS
         ==================================================================== -->
    <div class="repo-workspace">
        <aside class="repo-sidebar">
            <div>
                <div class="dropdown mb-3">
                    <button type="button" class="repo-upload-cta dropdown-toggle w-100 justify-content-between" data-bs-toggle="dropdown" aria-expanded="false" id="btnRepoNewDropdown">
                        <span class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-plus-circle-outline fs-5"></i>
                            <span>+ New Upload</span>
                        </span>
                        <i class="mdi mdi-chevron-down extra-small"></i>
                    </button>
                    <ul class="dropdown-menu shadow border-0 p-1 rounded-3 w-100" style="font-size: 13px;" aria-labelledby="btnRepoNewDropdown">
                        <li>
                            <a class="dropdown-item rounded-2 py-2 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="openUploadModalWithMode('pdf')">
                                <div class="rounded-circle bg-danger-subtle text-danger p-1 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-file-pdf-box fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">Upload PDF File(s)</div>
                                    <div class="text-muted extra-small">Local files with SHA-256 seal</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="openUploadModalWithMode('gdrive')">
                                <div class="rounded-circle bg-success-subtle text-success p-1 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-google-drive fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">Google Drive Link(s)</div>
                                    <div class="text-muted extra-small">Public shareable cloud links</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Document Categories -->
                <div class="repo-sidebar-heading">Vault Repositories</div>
                <nav class="mb-3" id="sidebarCategoryNav">
                    <a href="javascript:void(0)" onclick="setSidebarCategory('all')" class="repo-nav-item {{ empty($moduleFilter) && empty($categoryFilter) && empty($physicallySignedOnly) && empty($storageFilter) ? 'active' : '' }}" data-repo-filter="all">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-folder-multiple-outline fs-5 text-secondary"></i>
                            <span>All Documents</span>
                        </div>
                        <span class="badge bg-light text-secondary border font-mono extra-small" id="badgeTotalCount">{{ $totalCount }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="setSidebarCategory('signed')" class="repo-nav-item {{ $physicallySignedOnly ? 'active' : '' }}" data-repo-filter="signed">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-draw-pen fs-5 text-danger"></i>
                            <span>Wet-Signed Evidence</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small" id="badgeSignedCount">{{ $signedCount }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="setSidebarCategory('rie')" class="repo-nav-item {{ $moduleFilter === 'rie' ? 'active' : '' }}" data-repo-filter="rie">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-rocket-launch-outline fs-5 text-primary"></i>
                            <span>RIE Extension Projects</span>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" id="badgeRieCount">{{ $rieCount }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="setSidebarCategory('gdrive')" class="repo-nav-item {{ $storageFilter === 'gdrive' ? 'active' : '' }}" data-repo-filter="gdrive">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-google-drive fs-5 text-success"></i>
                            <span>Google Drive Files</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small" id="badgeGdriveCount">{{ $gdriveCount }}</span>
                    </a>
                </nav>

                <!-- Campus Filter (if available) -->
                @if($campuses->isNotEmpty())
                    <div class="repo-sidebar-heading">Campus Jurisdiction</div>
                    <div class="px-1 mb-3">
                        <select class="form-select form-select-sm border" style="font-size: 12px;" id="sidebarCampusSelect" onchange="setSidebarCampus(this.value)">
                            <option value="">All Campuses</option>
                            @foreach($campuses as $camp)
                                <option value="{{ $camp->id }}" {{ (string)$campusId === (string)$camp->id ? 'selected' : '' }}>
                                    {{ $camp->campus_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Actual System & Server Storage Monitor Widget -->
            <div>
                <div class="repo-storage-widget">
                    <!-- 1. Header with Live Status Badge -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark extra-small d-flex align-items-center gap-1">
                            <i class="mdi mdi-server-network text-primary"></i> SYSTEM STORAGE
                        </span>
                        @php
                            $diskPct = $systemStorage['percent'] ?? 0;
                            $diskStatus = $systemStorage['status'] ?? 'success';
                            $diskBadgeClass = match($diskStatus) {
                                'danger' => 'bg-danger-subtle text-danger border-danger-subtle',
                                'warning' => 'bg-warning-subtle text-warning border-warning-subtle',
                                default => 'bg-success-subtle text-success border-success-subtle',
                            };
                            $diskBarClass = match($diskStatus) {
                                'danger' => 'bg-danger',
                                'warning' => 'bg-warning',
                                default => 'bg-success',
                            };
                        @endphp
                        <span class="badge border font-mono {{ $diskBadgeClass }}" id="sidebarDiskBadge" style="font-size: 9px;">
                            {{ $systemStorage['status_label'] ?? 'HEALTHY' }}
                        </span>
                    </div>

                    <!-- 2. Server Disk Capacity & Usage -->
                    <div class="d-flex align-items-center justify-content-between text-secondary extra-small mb-1">
                        <span>Server Disk (Host)</span>
                        <span class="font-mono fw-bold text-dark" id="sidebarDiskPercent">{{ $diskPct }}%</span>
                    </div>

                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0; border-radius: 999px;">
                        <div class="progress-bar {{ $diskBarClass }}" id="sidebarDiskProgressBar" style="width: {{ $diskPct }}%; border-radius: 999px;"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between extra-small text-muted font-mono mb-2">
                        <span>Used: <strong class="text-dark" id="sidebarDiskUsed">{{ $systemStorage['used_formatted'] ?? '—' }}</strong></span>
                        <span>Total: <strong class="text-dark" id="sidebarDiskTotal">{{ $systemStorage['total_formatted'] ?? '—' }}</strong></span>
                    </div>

                    <div class="extra-small text-muted font-mono mb-3 p-1 rounded-2 bg-light border text-center">
                        <i class="mdi mdi-harddisk-plus text-secondary me-1"></i>
                        Available: <strong class="text-success" id="sidebarDiskFree">{{ $systemStorage['free_formatted'] ?? '—' }}</strong>
                    </div>

                    <!-- 3. Repository Vault Footprint -->
                    <div class="pt-2 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="extra-small fw-bold text-muted font-mono">VAULT FOOTPRINT</span>
                            <span class="badge bg-light text-secondary border font-mono" style="font-size: 9px;">
                                <i class="mdi mdi-shield-check text-success"></i> SHA-256
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between extra-small text-secondary">
                            <span>Uploaded Files:</span>
                            <strong class="text-dark font-mono" id="sidebarVaultFootprint">{{ $systemStorage['vault_formatted'] ?? '—' }}</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between extra-small text-secondary">
                            <span>Indexed Records:</span>
                            <span class="font-mono text-dark"><strong id="sidebarVaultFileCount">{{ $totalCount }}</strong> files &bull; <strong id="sidebarGdriveCount">{{ $gdriveCount }}</strong> links</span>
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted extra-small d-inline-flex align-items-center gap-1 hover-primary">
                        <i class="mdi mdi-keyboard-return"></i> Return to Main DSS Portal
                    </a>
                </div>
            </div>
        </aside>

        <!-- MAIN REPOSITORY WORKSPACE CANVAS -->
        <!-- MAIN REPOSITORY WORKSPACE CANVAS -->
        <main class="repo-canvas position-relative" id="repoCanvas">
            <!-- Loading Overlay for Pure AJAX -->
            <div id="repoLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none d-flex align-items-start justify-content-center pt-5" style="background: rgba(248, 250, 252, 0.7); z-index: 20; backdrop-filter: blur(1px);">
                <div class="bg-white border rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status" style="width: 16px; height: 16px; border-width: 2px;"></div>
                    <span class="extra-small fw-semibold text-dark">Updating repository...</span>
                </div>
            </div>

            <!-- Dynamic Content Container -->
            <div id="repoCanvasContent">
                @include('dss.documents.partials.repository-canvas')
            </div>
        </main>
    </div>
</div>

<!-- ====================================================================
     MODALS
     ==================================================================== -->

<!-- 1. QUICK UPLOAD MODAL -->
<div class="modal fade" id="quickUploadModal" tabindex="-1" aria-labelledby="quickUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-inline-flex">
                        <i class="mdi mdi-folder-upload fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 m-0" id="quickUploadModalLabel">Upload Documents to Repository</h5>
                        <p class="text-muted extra-small m-0">Upload verified PDF documents or attach public Google Drive links.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <x-dss.document-uploader :allowPhysicalSign="true" category="general" title="Upload Document" />
            </div>
        </div>
    </div>
</div>

<!-- 2. ASSET DETAILS INSPECTOR MODAL -->
<div class="modal fade" id="assetDetailsModal" tabindex="-1" aria-labelledby="assetDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-2 d-inline-flex">
                        <i class="mdi mdi-file-certificate-outline fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 m-0" id="assetDetailsModalLabel">Document Details &amp; Checksum</h5>
                        <p class="text-muted extra-small m-0">Document metadata and cryptographic verification.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 p-3 rounded-2 bg-light border">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono d-block mb-1">Document Title</span>
                    <div class="fw-bold text-dark fs-6" id="inspectDocTitle">—</div>
                    <div class="extra-small text-muted font-mono mt-1" id="inspectDocFilename">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small text-uppercase fw-bold text-muted font-mono mb-1">SHA-256 Checksum</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control font-mono extra-small" id="inspectDocHash" readonly>
                        <button type="button" class="btn btn-outline-primary" id="btnInspectCopyHash" title="Copy Checksum">
                            <i class="mdi mdi-content-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light border">
                            <span class="extra-small text-muted font-mono d-block">Category</span>
                            <strong class="text-dark extra-small" id="inspectDocCategory">—</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light border">
                            <span class="extra-small text-muted font-mono d-block">File Size</span>
                            <strong class="text-dark extra-small" id="inspectDocSize">—</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light border">
                            <span class="extra-small text-muted font-mono d-block">Wet-Signed Status</span>
                            <strong class="text-dark extra-small" id="inspectDocSigned">—</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light border">
                            <span class="extra-small text-muted font-mono d-block">Upload Date</span>
                            <strong class="text-dark extra-small" id="inspectDocDate">—</strong>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono d-block mb-1">Uploaded By</span>
                    <div class="fw-semibold text-dark extra-small" id="inspectDocUploader">—</div>
                </div>

                <div id="inspectNotesWrapper" class="d-none mb-3">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono d-block mb-1">Remarks</span>
                    <div class="p-2 rounded-2 bg-light border extra-small fst-italic text-secondary" id="inspectDocNotes"></div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <a href="#" id="inspectPreviewBtn" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                    <i class="mdi mdi-eye me-1"></i> Preview
                </a>
                <a href="#" id="inspectDownloadBtn" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                    <i class="mdi mdi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 3. REPOSITORY AUDIT TRAIL MODAL -->
<div class="modal fade" id="vaultAuditTrailModal" tabindex="-1" aria-labelledby="vaultAuditTrailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-2 d-inline-flex">
                        <i class="mdi mdi-history fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 m-0" id="vaultAuditTrailModalLabel">Institutional Repository Audit History</h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono">
                            <i class="mdi mdi-shield-lock-outline me-1"></i> Tamper-Evident Access &amp; Upload Logging
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($recentAuditTrails->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        No audit trail entries recorded yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
                            <thead class="table-light extra-small text-uppercase text-muted font-mono">
                                <tr>
                                    <th style="width: 170px;">Timestamp</th>
                                    <th style="width: 140px;">Action</th>
                                    <th>Summary</th>
                                    <th style="width: 180px;">Actor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAuditTrails as $at)
                                    <tr>
                                        <td class="font-mono text-muted extra-small">
                                            <div class="fw-semibold text-dark">{{ $at->created_at->format('M d, Y') }}</div>
                                            <div>{{ $at->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border rounded-pill extra-small px-2 py-1 font-mono">
                                                {{ strtoupper($at->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $at->summary }}</div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $at->user?->name ?: 'System' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// ─── PURE AJAX REPOSITORY COORDINATOR ─────────────────────────────────────────
const repoState = {
    view: @json($viewMode ?: 'table'),
    module: @json($moduleFilter ?: ''),
    category: @json($categoryFilter ?: ''),
    sort: @json($sort ?: 'latest'),
    search: @json($search ?: ''),
    format: @json($formatFilter ?: ''),
    storage: @json($storageFilter ?: ''),
    campus_id: @json($campusId ? (string)$campusId : ''),
    physically_signed: @json($physicallySignedOnly ? '1' : ''),
    page: @json($documents->currentPage() ?: 1),
    baseUrl: @json(route('dss.documents.repository'))
};

let searchDebounceTimer = null;

function showRepoLoading() {
    const overlay = document.getElementById('repoLoadingOverlay');
    if (overlay) overlay.classList.remove('d-none');
}

function hideRepoLoading() {
    const overlay = document.getElementById('repoLoadingOverlay');
    if (overlay) overlay.classList.add('d-none');
}

function updateCount(id, val) {
    const el = document.getElementById(id);
    if (el && val !== undefined && val !== null) {
        el.textContent = val;
    }
}

function syncSearchInputs(val) {
    const top = document.getElementById('topbarSearchInput');
    const can = document.getElementById('canvasSearchInput');
    if (top && top.value !== val) top.value = val;
    if (can && can.value !== val) can.value = val;
}

function updateViewButtons(view) {
    const btnGrid = document.getElementById('btnViewGrid');
    const btnTable = document.getElementById('btnViewTable');
    if (btnGrid) btnGrid.classList.toggle('active', view === 'grid');
    if (btnTable) btnTable.classList.toggle('active', view !== 'grid');
}

function updateSidebarNavActive() {
    const items = document.querySelectorAll('#sidebarCategoryNav .repo-nav-item');
    items.forEach(el => {
        const filter = el.dataset.repoFilter;
        let isActive = false;
        if (filter === 'all') {
            isActive = !repoState.module && !repoState.category && !repoState.physically_signed && !repoState.storage;
        } else if (filter === 'signed') {
            isActive = repoState.physically_signed === '1';
        } else if (filter === 'rie') {
            isActive = repoState.module === 'rie';
        } else if (filter === 'gdrive') {
            isActive = repoState.storage === 'gdrive';
        } else if (filter === 'policy') {
            isActive = repoState.module === 'policy';
        }
        el.classList.toggle('active', isActive);
    });
}

function updateFormatPillsActive() {
    const pills = document.querySelectorAll('#sidebarFormatPills .repo-pill-filter');
    pills.forEach(el => {
        const fmt = el.dataset.format;
        const isActive = (repoState.format === fmt);
        el.classList.toggle('active', isActive);

        // Styling based on format type
        const colors = {
            pdf: 'bg-danger text-white',
            gdrive: 'bg-success text-white',
            doc: 'bg-primary text-white',
            sheet: 'bg-success text-white',
            image: 'bg-info text-white',
            zip: 'bg-dark text-white'
        };
        const activeClass = colors[fmt] || 'bg-primary text-white';

        if (isActive) {
            el.className = 'repo-pill-filter active ' + activeClass;
        } else {
            el.className = 'repo-pill-filter bg-white text-secondary border';
        }
    });
}

/**
 * Main Pure AJAX Fetch
 */
function fetchRepo(targetUrlOrParams, options = {}) {
    showRepoLoading();

    let queryParams = new URLSearchParams();

    if (typeof targetUrlOrParams === 'number') {
        repoState.page = targetUrlOrParams;
    } else if (typeof targetUrlOrParams === 'string') {
        try {
            const u = new URL(targetUrlOrParams, window.location.origin);
            const p = u.searchParams.get('page');
            if (p) {
                repoState.page = parseInt(p, 10);
            }
            ['view', 'module', 'category', 'sort', 'search', 'format', 'storage', 'campus_id', 'physically_signed'].forEach(k => {
                if (u.searchParams.has(k)) {
                    repoState[k] = u.searchParams.get(k);
                }
            });
        } catch (err) {
            console.warn('URL parse warning:', err);
        }
    } else if (typeof targetUrlOrParams === 'object' && targetUrlOrParams !== null) {
        Object.assign(repoState, targetUrlOrParams);
    }

    if (repoState.view) queryParams.set('view', repoState.view);
    if (repoState.module) queryParams.set('module', repoState.module);
    if (repoState.category) queryParams.set('category', repoState.category);
    if (repoState.sort && repoState.sort !== 'latest') queryParams.set('sort', repoState.sort);
    if (repoState.search) queryParams.set('search', repoState.search);
    if (repoState.format) queryParams.set('format', repoState.format);
    if (repoState.storage) queryParams.set('storage', repoState.storage);
    if (repoState.campus_id) queryParams.set('campus_id', repoState.campus_id);
    if (repoState.physically_signed) queryParams.set('physically_signed', '1');
    if (repoState.page && Number(repoState.page) > 1) queryParams.set('page', repoState.page);

    // Always fetch relative to current origin to avoid host/port mismatch
    const baseObj = new URL(repoState.baseUrl, window.location.origin);
    const fetchPath = baseObj.pathname;
    const fullUrl = fetchPath + (queryParams.toString() ? '?' + queryParams.toString() : '');

    fetch(fullUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Repository error: ' + res.status);
        return res.json();
    })
    .then(data => {
        hideRepoLoading();
        if (data.success && data.html) {
            const container = document.getElementById('repoCanvasContent');
            if (container) {
                container.innerHTML = data.html;
            }

            // Update browser URL
            if (options.pushState !== false) {
                window.history.pushState(repoState, '', fullUrl);
            }

            // Sync counts
            if (data.counts) {
                updateCount('badgeTotalCount', data.counts.total);
                updateCount('badgeSignedCount', data.counts.signed);
                updateCount('badgeRieCount', data.counts.rie);
                updateCount('badgeGdriveCount', data.counts.gdrive);
                updateCount('badgePolicyCount', data.counts.policy);
                updateCount('sidebarVaultFileCount', data.counts.total);

                if (data.counts.system_storage) {
                    const sys = data.counts.system_storage;
                    const elUsed = document.getElementById('sidebarDiskUsed');
                    const elTotal = document.getElementById('sidebarDiskTotal');
                    const elFree = document.getElementById('sidebarDiskFree');
                    const elPct = document.getElementById('sidebarDiskPercent');
                    const elBar = document.getElementById('sidebarDiskProgressBar');
                    const elBadge = document.getElementById('sidebarDiskBadge');
                    const elVault = document.getElementById('sidebarVaultFootprint');

                    if (elUsed) elUsed.textContent = sys.used_formatted;
                    if (elTotal) elTotal.textContent = sys.total_formatted;
                    if (elFree) elFree.textContent = sys.free_formatted;
                    if (elPct) elPct.textContent = sys.percent + '%';
                    if (elVault) elVault.textContent = sys.vault_formatted;

                    if (elBar) {
                        elBar.style.width = sys.percent + '%';
                        elBar.className = 'progress-bar ' + (sys.status === 'danger' ? 'bg-danger' : (sys.status === 'warning' ? 'bg-warning' : 'bg-success'));
                    }

                    if (elBadge) {
                        elBadge.textContent = sys.status_label;
                        elBadge.className = 'badge border font-mono ' + (sys.status === 'danger' ? 'bg-danger-subtle text-danger border-danger-subtle' : (sys.status === 'warning' ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-success-subtle text-success border-success-subtle'));
                    }
                }

                if (data.counts.gdrive_count !== undefined) {
                    const el = document.getElementById('sidebarGdriveCount');
                    if (el) el.textContent = data.counts.gdrive_count;
                }
            }

            // Sync views & navigation
            updateViewButtons(data.viewMode || repoState.view);
            updateSidebarNavActive();
            updateFormatPillsActive();
            syncSearchInputs(repoState.search || '');

            const campusSelect = document.getElementById('sidebarCampusSelect');
            if (campusSelect) campusSelect.value = repoState.campus_id || '';

            // Re-bind dynamic events in canvas
            bindCanvasEvents();

            if (options.scrollToTop) {
                const canvas = document.getElementById('repoCanvas');
                if (canvas) canvas.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    })
    .catch(err => {
        hideRepoLoading();
        console.error('AJAX Repository Error:', err);
    });
}

function switchRepoView(mode) {
    repoState.view = mode;
    fetchRepo({ view: mode });
}

function setSidebarCategory(cat) {
    if (cat === 'all') {
        repoState.module = '';
        repoState.physically_signed = '';
        repoState.storage = '';
        repoState.category = '';
    } else if (cat === 'signed') {
        repoState.physically_signed = '1';
        repoState.module = '';
        repoState.storage = '';
        repoState.category = '';
    } else if (cat === 'rie') {
        repoState.module = 'rie';
        repoState.physically_signed = '';
        repoState.storage = '';
        repoState.category = '';
    } else if (cat === 'gdrive') {
        repoState.storage = 'gdrive';
        repoState.module = '';
        repoState.physically_signed = '';
        repoState.category = '';
    } else if (cat === 'policy') {
        repoState.module = 'policy';
        repoState.physically_signed = '';
        repoState.storage = '';
        repoState.category = '';
    }
    fetchRepo({ page: 1 });
}

function toggleFormatFilter(fmt) {
    if (repoState.format === fmt) {
        repoState.format = '';
    } else {
        repoState.format = fmt;
    }
    fetchRepo({ page: 1 });
}

function setSidebarCampus(campusId) {
    repoState.campus_id = campusId;
    fetchRepo({ page: 1 });
}

function removeRepoFilter(key) {
    if (key === 'search') {
        repoState.search = '';
    } else if (key === 'module') {
        repoState.module = '';
    } else if (key === 'category') {
        repoState.category = '';
    } else if (key === 'physically_signed') {
        repoState.physically_signed = '';
    } else if (key === 'format') {
        repoState.format = '';
    } else if (key === 'storage') {
        repoState.storage = '';
    } else if (key === 'sort') {
        repoState.sort = 'latest';
    }
    fetchRepo({ page: 1 });
}

function resetRepoFilters() {
    repoState.module = '';
    repoState.category = '';
    repoState.search = '';
    repoState.format = '';
    repoState.storage = '';
    repoState.campus_id = '';
    repoState.physically_signed = '';
    repoState.sort = 'latest';
    fetchRepo({ page: 1 });
}

function triggerRepoSearch(query) {
    repoState.search = query.trim();
    fetchRepo({ page: 1 });
}

function copyRepoHash(hash) {
    if (!hash) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(hash).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'SHA-256 Checksum copied', timer: 1500, showConfirmButton: false });
            } else {
                alert('Checksum copied: ' + hash);
            }
        });
    } else {
        const ta = document.createElement('textarea');
        ta.value = hash;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'SHA-256 Checksum copied', timer: 1500, showConfirmButton: false });
        } else {
            alert('Checksum copied: ' + hash);
        }
    }
}

function openUploadModalWithMode(mode) {
    const modalEl = document.getElementById('quickUploadModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        setTimeout(() => {
            if (typeof window.setGlobalUploadMode === 'function') {
                window.setGlobalUploadMode(mode);
            }
        }, 150);
    }
}

/**
 * Handle AJAX Document Deletion with confirmation
 */
function handleAjaxDelete(btn) {
    const docId = btn.dataset.docId;
    const filename = btn.dataset.filename || 'this document';
    const deleteUrl = btn.dataset.deleteUrl;

    if (!deleteUrl) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Archive Document?',
            text: `Are you sure you want to remove "${filename}" from the digital asset vault?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                executeDeleteRequest(docId, deleteUrl);
            }
        });
    } else {
        if (confirm(`Are you sure you want to remove "${filename}"?`)) {
            executeDeleteRequest(docId, deleteUrl);
        }
    }
}

function executeDeleteRequest(docId, deleteUrl) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Document archived successfully',
                    showConfirmButton: false,
                    timer: 2000
                });
            }

            const item = document.getElementById('docRow_' + docId) || document.getElementById('docCard_' + docId);
            if (item) {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    item.remove();
                    // If no items left on page, refresh to load new page/empty state
                    const remaining = document.querySelectorAll('#repoCanvasContent tbody tr, #repoCanvasContent .repo-doc-card');
                    if (remaining.length === 0) {
                        fetchRepo();
                    }
                }, 300);
            } else {
                fetchRepo();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', data.message || 'Could not archive document', 'error');
            } else {
                alert(data.message || 'Could not archive document');
            }
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'An error occurred while deleting document', 'error');
        }
    });
}

/**
 * Bind interactive elements within the dynamic canvas
 */
function bindCanvasEvents() {
    // 1. Canvas Filter Form Submission
    const filterForm = document.getElementById('repoCanvasFilterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            repoState.module = document.getElementById('canvasModuleSelect')?.value || '';
            repoState.category = document.getElementById('canvasCategorySelect')?.value || '';
            repoState.sort = document.getElementById('canvasSortSelect')?.value || 'latest';
            repoState.search = document.getElementById('canvasSearchInput')?.value.trim() || '';
            fetchRepo({ page: 1 });
        });
    }

    // 2. Select Dropdowns auto-trigger
    const modSelect = document.getElementById('canvasModuleSelect');
    if (modSelect) {
        modSelect.addEventListener('change', function() {
            repoState.module = this.value;
            fetchRepo({ page: 1 });
        });
    }

    const catSelect = document.getElementById('canvasCategorySelect');
    if (catSelect) {
        catSelect.addEventListener('change', function() {
            repoState.category = this.value;
            fetchRepo({ page: 1 });
        });
    }

    const sortSelect = document.getElementById('canvasSortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            repoState.sort = this.value;
            fetchRepo({ page: 1 });
        });
    }

    // 3. Canvas Live Search with Debounce
    const canvasSearch = document.getElementById('canvasSearchInput');
    if (canvasSearch) {
        canvasSearch.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            const val = this.value;
            syncSearchInputs(val);
            searchDebounceTimer = setTimeout(() => {
                repoState.search = val.trim();
                fetchRepo({ page: 1 });
            }, 300);
        });
    }

    // 4. Clear Search Button
    const btnClear = document.getElementById('btnClearCanvasSearch');
    if (btnClear) {
        btnClear.addEventListener('click', function() {
            repoState.search = '';
            syncSearchInputs('');
            fetchRepo({ page: 1 });
        });
    }

    // 5. Reset Filters Button
    const btnReset = document.getElementById('btnResetRepoFilters');
    if (btnReset) {
        btnReset.addEventListener('click', resetRepoFilters);
    }

    // 6. Pagination Links Interception
    const pageLinks = document.querySelectorAll('#repoCanvasContent .repo-pagination a, #repoCanvasContent .pagination a, #repoCanvasContent .repo-page-btn');
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let targetPage = this.dataset.page;
            if (!targetPage) {
                try {
                    const u = new URL(this.href || this.getAttribute('href'), window.location.origin);
                    targetPage = u.searchParams.get('page');
                } catch(err) {}
            }
            if (targetPage) {
                fetchRepo({ page: parseInt(targetPage, 10) }, { scrollToTop: true });
            }
        });
    });

    // Delegated click fallback on repoCanvas so pagination ALWAYS triggers
    const canvasEl = document.getElementById('repoCanvas');
    if (canvasEl && !canvasEl.dataset.paginationBound) {
        canvasEl.dataset.paginationBound = 'true';
        canvasEl.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.repo-pagination a, .pagination a, .repo-page-btn');
            if (pageLink) {
                e.preventDefault();
                let targetPage = pageLink.dataset.page;
                if (!targetPage) {
                    try {
                        const u = new URL(pageLink.href || pageLink.getAttribute('href'), window.location.origin);
                        targetPage = u.searchParams.get('page');
                    } catch (err) {}
                }
                if (targetPage) {
                    fetchRepo({ page: parseInt(targetPage, 10) }, { scrollToTop: true });
                }
            }
        });
    }

    // 7. Asset Inspector Modal Trigger
    document.querySelectorAll('#repoCanvasContent .btn-inspect-asset').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            document.getElementById('inspectDocTitle').textContent = d.title || '—';
            document.getElementById('inspectDocFilename').textContent = d.filename || '—';
            document.getElementById('inspectDocHash').value = d.hash || '';
            document.getElementById('inspectDocCategory').textContent = d.category || '—';
            document.getElementById('inspectDocSize').textContent = d.size || '—';
            document.getElementById('inspectDocSigned').textContent = d.signed || '—';
            document.getElementById('inspectDocDate').textContent = d.date || '—';
            document.getElementById('inspectDocUploader').textContent = d.uploader || '—';

            const notesWrapper = document.getElementById('inspectNotesWrapper');
            if (d.notes && d.notes.trim() !== '') {
                notesWrapper.classList.remove('d-none');
                document.getElementById('inspectDocNotes').textContent = d.notes;
            } else {
                notesWrapper.classList.add('d-none');
            }

            document.getElementById('inspectPreviewBtn').href = d.preview || '#';
            document.getElementById('inspectDownloadBtn').href = d.download || '#';

            const modalEl = document.getElementById('assetDetailsModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });
    });

    // 8. Delete Buttons with AJAX
    document.querySelectorAll('#repoCanvasContent .btn-delete-document').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            handleAjaxDelete(this);
        });
    });
}

// Global hook for modal uploader to refresh canvas
window.refreshRepoAjax = function() {
    fetchRepo(window.location.href, { pushState: false });
};

document.addEventListener('DOMContentLoaded', function() {
    // Quick search shortcut (Ctrl + K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('topbarSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Topbar Search live input with debounce
    const topSearch = document.getElementById('topbarSearchInput');
    if (topSearch) {
        topSearch.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            const val = this.value;
            syncSearchInputs(val);
            searchDebounceTimer = setTimeout(() => {
                repoState.search = val.trim();
                fetchRepo({ page: 1 });
            }, 300);
        });
    }

    // Copy hash button in inspector modal
    const btnInspectCopy = document.getElementById('btnInspectCopyHash');
    if (btnInspectCopy) {
        btnInspectCopy.addEventListener('click', function() {
            const hash = document.getElementById('inspectDocHash').value;
            copyRepoHash(hash);
        });
    }

    // Initial binding
    bindCanvasEvents();

    // Browser back/forward navigation support
    window.addEventListener('popstate', function() {
        fetchRepo(window.location.href, { pushState: false });
    });
});
</script>

@stack('scripts')

</body>
</html>
