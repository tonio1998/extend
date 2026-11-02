@php
    $currentTheme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $primary = $currentTheme->primary_color ?? '#00674f';
    $secondary = $currentTheme->secondary_color ?? '#64748b';
    $sidebarTheme = $currentTheme->sidebar_theme ?? 'light';

    $sidebarBg = $currentTheme->sidebar_bg ?: ($sidebarTheme === 'dark' ? '#0f172a' : '#f8fafc');
    $sidebarText = $currentTheme->sidebar_text ?: ($sidebarTheme === 'dark' ? '#cbd5e1' : '#334155');
    $sidebarActiveBg = $currentTheme->sidebar_active_bg ?: ($sidebarTheme === 'dark' ? 'rgba(4, 120, 87, 0.22)' : 'color-mix(in srgb, ' . $primary . ' 12%, #ffffff)');
    $sidebarActiveText = $currentTheme->sidebar_active_text ?: ($sidebarTheme === 'dark' ? '#34d399' : $primary);
    $sidebarBorder = $currentTheme->sidebar_border ?: ($sidebarTheme === 'dark' ? '#1e293b' : '#e2e8f0');

    $monthNamesShort = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Extension Calendar - Duration & Range Monitoring — {{ $inst?->school_name ?: 'RIE System' }}</title>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

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

    <style>
        :root {
            --cal-primary: {{ $primary }};
            --cal-primary-hover: color-mix(in srgb, {{ $primary }} 85%, black);
            --cal-primary-soft: color-mix(in srgb, {{ $primary }} 10%, #ffffff);
            --cal-sidebar-bg: {{ $sidebarBg }};
            --cal-sidebar-text: {{ $sidebarText }};
            --cal-sidebar-border: {{ $sidebarBorder }};
            --cal-sidebar-active: {{ $sidebarActiveBg }};
            --cal-sidebar-active-text: {{ $sidebarActiveText }};
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

        .cal-crm-shell {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #f8fafc;
        }

        /* ─── 1. TOP COMMAND BAR ────────────────────────────────────────── */
        .cal-topbar {
            height: 64px;
            min-height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            gap: 12px;
        }

        .cal-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            flex-shrink: 0;
        }

        .cal-brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--cal-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            box-shadow: 0 2px 4px color-mix(in srgb, var(--cal-primary) 30%, transparent);
            flex-shrink: 0;
        }

        .cal-brand-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.015em;
        }

        .cal-brand-subtitle {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }

        /* Segmented View Switcher */
        .cal-segmented-switch {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 8px;
            display: inline-flex;
            gap: 2px;
            border: 1px solid #e2e8f0;
        }

        .cal-segmented-btn {
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
            cursor: pointer;
            white-space: nowrap;
        }

        .cal-segmented-btn:hover {
            color: #0f172a;
        }

        .cal-segmented-btn.active {
            background: #ffffff;
            color: var(--cal-primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .cal-search-input-group {
            width: 240px;
            position: relative;
        }

        .cal-search-input-group input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding-left: 32px;
            padding-right: 48px;
            font-size: 12px;
            height: 34px;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .cal-search-input-group input:focus {
            background: #ffffff;
            border-color: var(--cal-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--cal-primary) 12%, transparent);
            outline: none;
        }

        .cal-search-input-group .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .cal-search-kbd {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 4px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            pointer-events: none;
        }

        /* ─── 2. WORKSPACE LAYOUT ───────────────────────────────────────── */
        .cal-workspace {
            display: flex;
            flex: 1;
            height: calc(100vh - 64px);
            overflow: hidden;
            position: relative;
        }

        /* ─── DEDICATED CALENDAR SIDEBAR ─────────────────────────────────── */
        .cal-sidebar {
            width: 280px;
            min-width: 280px;
            background: var(--cal-sidebar-bg);
            color: var(--cal-sidebar-text);
            border-right: 1px solid var(--cal-sidebar-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px 14px;
            overflow-y: auto;
            z-index: 1020;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cal-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .cal-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .cal-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 64px 0 0 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1015;
            backdrop-filter: blur(2px);
        }

        @media (max-width: 991px) {
            .cal-sidebar {
                position: fixed;
                top: 64px;
                bottom: 0;
                left: 0;
                transform: translateX(-100%);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            }
            .cal-sidebar.is-open {
                transform: translateX(0);
            }
            .cal-sidebar-backdrop.is-open {
                display: block;
            }
        }

        .cal-sidebar-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            padding: 6px 6px 3px 6px;
        }

        .cal-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border-radius: 8px;
            color: var(--cal-sidebar-text);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }

        .cal-nav-item:hover {
            background: rgba(0, 0, 0, 0.04);
            color: #0f172a;
        }

        .cal-nav-item.active {
            background: var(--cal-sidebar-active);
            color: var(--cal-sidebar-active-text);
            font-weight: 600;
        }

        .cal-nav-item.active .badge {
            background: var(--cal-primary) !important;
            color: #ffffff !important;
        }

        /* Mini Interactive Sidebar Calendar */
        .cal-mini-picker {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            margin-bottom: 8px;
        }

        .cal-mini-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .cal-mini-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            text-align: center;
        }

        .cal-mini-day-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            padding: 2px 0;
        }

        .cal-mini-cell {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            transition: all 0.12s ease;
        }

        .cal-mini-cell:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .cal-mini-cell.is-outside {
            color: #cbd5e1;
            font-weight: 400;
        }

        .cal-mini-cell.is-today {
            background: var(--cal-primary);
            color: #ffffff;
            font-weight: 700;
        }

        .cal-mini-cell.has-events::after {
            content: '';
            position: absolute;
            bottom: 2px;
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: #2563eb;
        }

        .cal-mini-cell.is-today.has-events::after {
            background: #ffffff;
        }

        /* Extension Monitoring Stats Card */
        .cal-stats-widget {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        /* ─── 3. MAIN CALENDAR CANVAS ───────────────────────────────────── */
        .cal-canvas {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            padding: 16px 20px;
            background: #f8fafc;
        }

        .cal-canvas::-webkit-scrollbar {
            width: 6px;
        }
        .cal-canvas::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Horizontal Scroll Wrapper for smaller viewports */
        .ext-cal-grid-scroll-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .ext-cal-grid-inner {
            min-width: 720px;
        }

        /* Range-Style Calendar Grid */
        .ext-cal-grid-header {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            background: #0f172a;
            color: #f8fafc;
            text-align: center;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 0;
            border-radius: 10px 10px 0 0;
        }

        .ext-cal-weeks-container {
            display: flex;
            flex-direction: column;
            border-left: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            border-radius: 0 0 10px 10px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .ext-cal-week-row {
            position: relative;
            min-height: 136px;
            border-top: 1px solid #cbd5e1;
            display: flex;
            flex-direction: column;
        }

        .ext-cal-week-bg {
            position: absolute;
            inset: 0;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            pointer-events: none;
            z-index: 1;
        }

        .ext-cal-bg-cell {
            border-right: 1px solid #e2e8f0;
            background: #ffffff;
            transition: background-color 0.2s ease;
        }
        .ext-cal-bg-cell:last-child {
            border-right: none;
        }
        .ext-cal-bg-cell.is-outside {
            background: #f8fafc;
        }
        .ext-cal-bg-cell.is-weekend {
            background: #fafaf9;
        }
        .ext-cal-bg-cell.is-today {
            background: rgba(0, 103, 79, 0.06);
            box-shadow: inset 0 0 0 2px rgba(0, 103, 79, 0.35);
        }

        @keyframes calDayPulse {
            0% { background-color: rgba(0, 103, 79, 0.25); }
            50% { background-color: rgba(0, 103, 79, 0.10); }
            100% { background-color: transparent; }
        }

        .ext-cal-bg-cell.is-highlighted {
            animation: calDayPulse 1.6s ease-out;
            background: rgba(0, 103, 79, 0.12) !important;
        }

        .ext-cal-week-day-headers {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            padding: 6px 0 4px;
        }

        .ext-cal-day-header-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8px;
            min-width: 0;
            cursor: pointer;
            border-radius: 4px;
        }

        .ext-cal-day-header-cell:hover .ext-cal-day-num {
            background: #e2e8f0;
        }

        .ext-cal-day-header-cell.is-outside {
            opacity: 0.45;
        }

        .ext-cal-day-num {
            font-size: 12px;
            font-weight: 800;
            color: #1e293b;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            transition: all 0.15s ease;
        }

        .ext-cal-day-num.is-today {
            background: var(--cal-primary);
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 103, 79, 0.35);
        }

        .ext-cal-week-lanes {
            position: relative;
            z-index: 3;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            row-gap: 5px;
            column-gap: 0;
            padding: 2px 4px 12px;
            flex-grow: 1;
            align-content: start;
        }

        .ext-cal-range-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            height: 26px;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
            padding: 0 8px;
            margin: 0 3px;
            border-radius: 6px;
            color: #ffffff !important;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            overflow: hidden;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.16);
            transition: transform 0.12s ease, filter 0.12s ease, box-shadow 0.12s ease;
        }

        .ext-cal-range-bar:hover,
        .ext-cal-range-bar.is-linked-hover {
            transform: translateY(-1px);
            filter: brightness(1.12);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.32);
            z-index: 5;
        }

        .ext-cal-range-bar.continues-left {
            margin-left: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: 3px dashed rgba(255, 255, 255, 0.7);
        }

        .ext-cal-range-bar.continues-right {
            margin-right: 0;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: 3px dashed rgba(255, 255, 255, 0.7);
        }

        .ext-cal-range-bar-main {
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
        }

        .ext-cal-range-code {
            background: rgba(15, 23, 42, 0.28);
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 9px;
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }

        .ext-cal-range-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1 1 auto;
            min-width: 0;
        }

        .ext-cal-range-pill {
            background: rgba(15, 23, 42, 0.22);
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            flex-shrink: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Gantt Cells */
        .gantt-month-cell {
            width: 38px;
            min-width: 38px;
            text-align: center;
            padding: 4px 2px;
            position: relative;
        }
        .gantt-bar-segment {
            height: 18px;
            border-radius: 4px;
            background: #cbd5e1;
            transition: all 0.15s ease;
        }
        .gantt-bar-segment.is-active-ongoing {
            background: linear-gradient(90deg, #059669, #10b981);
            box-shadow: 0 1px 4px rgba(16, 185, 129, 0.35);
        }
        .gantt-bar-segment.is-active-upcoming {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.3);
        }
        .gantt-bar-segment.is-active-completed {
            background: linear-gradient(90deg, #475569, #64748b);
        }

        /* Print Mode */
        @media print {
            .cal-topbar, .cal-sidebar, .cal-sidebar-backdrop, .cal-segmented-switch, #topFilterRibbon, .btn {
                display: none !important;
            }
            .cal-crm-shell, .cal-workspace, .cal-canvas {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
            }
            .ext-cal-weeks-container {
                box-shadow: none !important;
                border: 1px solid #000000 !important;
            }
        }
    </style>
</head>
<body>

<div class="cal-crm-shell">
    {{-- ====================================================================
         1. DEDICATED CALENDAR TOPBAR
         ==================================================================== --}}
    <header class="cal-topbar">
        {{-- Brand & Mobile Drawer Toggle --}}
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light border d-inline-flex d-lg-none" id="btnToggleSidebar" onclick="window.toggleCalSidebar()" title="Toggle Calendar Controls">
                <i class="mdi mdi-menu fs-5"></i>
            </button>

            <a href="{{ route('dss.rie.calendar.index') }}" class="cal-brand-link">
                <div class="cal-brand-icon">
                    <i class="mdi mdi-calendar-range"></i>
                </div>
                <div class="d-none d-sm-block">
                    <div class="cal-brand-title">Extension Calendar</div>
                    <div class="cal-brand-subtitle">Duration & Range Monitoring &bull; RIE System</div>
                </div>
            </a>

            <span class="badge bg-light text-secondary border font-mono d-none d-xl-inline-block" style="font-size: 10px;">
                <i class="mdi mdi-shield-check text-success me-1"></i>SNSU RIE
            </span>
        </div>

        {{-- Center: Month Navigator & Segmented View Switcher --}}
        <div class="d-flex align-items-center gap-2">
            {{-- Month Navigator --}}
            <div class="d-inline-flex align-items-center border rounded-3 bg-light p-1" id="calMonthNavGroup">
                <button type="button" class="btn btn-sm btn-light border-0 rounded-2 px-2 py-1" onclick="window.navigateCalendarMonth(-1)" title="Previous Month (Arrow Left)">
                    <i class="mdi mdi-chevron-left fs-5"></i>
                </button>
                <h6 class="fw-bold text-dark m-0 px-2 font-sans" id="calendarCurrentMonthLabel" style="min-width: 135px; text-align: center; font-size: 13.5px;">
                    October 2026
                </h6>
                <button type="button" class="btn btn-sm btn-light border-0 rounded-2 px-2 py-1" onclick="window.navigateCalendarMonth(1)" title="Next Month (Arrow Right)">
                    <i class="mdi mdi-chevron-right fs-5"></i>
                </button>
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 fw-bold extra-small" style="height: 34px;" onclick="window.jumpCalendarToToday()" title="Jump to Today (Key: T)">
                Today
            </button>

            {{-- Segmented View Switcher --}}
            <div class="cal-segmented-switch d-none d-md-inline-flex">
                <button type="button" class="cal-segmented-btn active" id="btnViewCalendar" onclick="window.switchCalendarViewMode('calendar', this)">
                    <i class="mdi mdi-calendar-range"></i>
                    <span>Calendar</span>
                </button>
                <button type="button" class="cal-segmented-btn" id="btnViewGantt" onclick="window.switchCalendarViewMode('gantt', this)">
                    <i class="mdi mdi-chart-gantt"></i>
                    <span>Roadmap (<span id="btnGanttCount">{{ count($durationRoadmapItems) }}</span>)</span>
                </button>
                <button type="button" class="cal-segmented-btn" id="btnViewList" onclick="window.switchCalendarViewMode('list', this)">
                    <i class="mdi mdi-format-list-bulleted-square"></i>
                    <span>Ledger (<span id="btnLedgerCount">{{ count($calendarEvents) }}</span>)</span>
                </button>
            </div>
        </div>

        {{-- Right: Live Search, Export & Navigation --}}
        <div class="d-flex align-items-center gap-2">
            {{-- Live Search --}}
            <div class="cal-search-input-group d-none d-lg-block">
                <i class="mdi mdi-magnify search-icon"></i>
                <input type="text" id="calSearchInput" class="form-control form-control-sm" placeholder="Search title, leader..." autocomplete="off">
                <span class="cal-search-kbd">Ctrl+K</span>
            </div>

            {{-- Year Scope Filter --}}
            <div class="d-none d-sm-block">
                <select id="calScopePeriodSelect" class="form-select form-select-sm font-mono extra-small" style="width: 130px; height: 34px;" onchange="window.onScopePeriodChange(this.value)">
                    <option value="all" {{ $selectedPeriod === 'all' ? 'selected' : '' }}>Scope: All-Time</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (string) $selectedPeriod === (string) $yr ? 'selected' : '' }}>FY {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Export / Print Dropdown --}}
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 extra-small fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false" title="Export or Print Calendar">
                    <i class="mdi mdi-tray-arrow-down text-primary"></i>
                    <span class="d-none d-xl-inline">Export</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border extra-small">
                    <li>
                        <button type="button" class="dropdown-item py-1" onclick="window.printCalendarView()">
                            <i class="mdi mdi-printer me-2 text-primary"></i>Print Calendar View
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item py-1" onclick="window.exportEventsToCSV()">
                            <i class="mdi mdi-file-delimited-outline me-2 text-success"></i>Export Schedule (CSV)
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-1 text-muted" href="{{ route('dss.documents.repository') }}">
                            <i class="mdi mdi-folder-multiple-outline me-2"></i>Document Repository
                        </a>
                    </li>
                </ul>
            </div>

            <a href="{{ route('dss.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-view-dashboard-outline"></i>
                <span class="d-none d-sm-inline">Dashboard</span>
            </a>
        </div>
    </header>

    {{-- ====================================================================
         2. WORKSPACE: DEDICATED SIDEBAR + CENTRAL CANVAS
         ==================================================================== --}}
    <div class="cal-workspace">
        {{-- Mobile Backdrop --}}
        <div class="cal-sidebar-backdrop" id="calSidebarBackdrop" onclick="window.toggleCalSidebar(false)"></div>

        {{-- DEDICATED SIDEBAR --}}
        <aside class="cal-sidebar" id="calSidebar">
            <div class="d-flex flex-column gap-3">
                {{-- Mobile Close Header --}}
                <div class="d-flex align-items-center justify-content-between d-lg-none pb-2 border-bottom">
                    <span class="fw-bold text-dark font-sans extra-small text-uppercase">Calendar Controls</span>
                    <button type="button" class="btn btn-sm btn-light border-0 text-muted p-1" onclick="window.toggleCalSidebar(false)">
                        <i class="mdi mdi-close fs-5"></i>
                    </button>
                </div>

                {{-- Campus Selector (Instant Client-Side Filtering) --}}
                <div>
                    <label class="cal-sidebar-heading d-block p-0 mb-1">Campus Filter</label>
                    <select id="calCampusSelect" class="form-select form-select-sm font-sans" onchange="window.filterCalendarByCampus(this.value)">
                        <option value="all">All Campuses</option>
                        @foreach($campuses as $camp)
                            <option value="{{ $camp->campus_name }}">{{ $camp->campus_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Interactive Mini-Month Calendar --}}
                <div class="cal-mini-picker">
                    <div class="cal-mini-header">
                        <span class="fw-bold text-dark extra-small font-mono" id="calMiniLabel">Oct 2026</span>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-light border-0 p-0 text-muted" onclick="window.navigateCalendarMonth(-1)" title="Prev Month">
                                <i class="mdi mdi-chevron-left fs-6"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-light border-0 p-0 text-muted" onclick="window.navigateCalendarMonth(1)" title="Next Month">
                                <i class="mdi mdi-chevron-right fs-6"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cal-mini-grid" id="calMiniGrid">
                        <div class="cal-mini-day-label">S</div>
                        <div class="cal-mini-day-label">M</div>
                        <div class="cal-mini-day-label">T</div>
                        <div class="cal-mini-day-label">W</div>
                        <div class="cal-mini-day-label">T</div>
                        <div class="cal-mini-day-label">F</div>
                        <div class="cal-mini-day-label">S</div>
                    </div>
                </div>

                {{-- Milestone Categories Filter Navigation (Instant Filtering) --}}
                <div>
                    <div class="cal-sidebar-heading">Milestone Categories</div>
                    <nav class="d-flex flex-column" id="calCategoryNav">
                        <div class="cal-nav-item active" data-cat="all" onclick="window.filterCalendarByCategory('all', this)">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-calendar-blank-multiple text-muted"></i>
                                <span>All Event Ranges</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_all">{{ $categoryCounts['all'] ?? $totalScheduledEventsCount }}</span>
                        </div>
                        <div class="cal-nav-item" data-cat="proposal_duration" onclick="window.filterCalendarByCategory('proposal_duration', this)">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background: #2563eb;"></span>
                                <span class="text-truncate">Proposal Spans</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_proposal_duration">{{ $categoryCounts['proposal_duration'] ?? 0 }}</span>
                        </div>
                        <div class="cal-nav-item" data-cat="conduct_activity" onclick="window.filterCalendarByCategory('conduct_activity', this)">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background: #059669;"></span>
                                <span class="text-truncate">Conduct of Activity</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_conduct_activity">{{ $categoryCounts['conduct_activity'] ?? 0 }}</span>
                        </div>
                        <div class="cal-nav-item" data-cat="materials_procurement" onclick="window.filterCalendarByCategory('materials_procurement', this)">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background: #d97706;"></span>
                                <span class="text-truncate">Materials & Logistics</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_materials_procurement">{{ $categoryCounts['materials_procurement'] ?? 0 }}</span>
                        </div>
                        <div class="cal-nav-item" data-cat="moa_signing" onclick="window.filterCalendarByCategory('moa_signing', this)">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background: #7c3aed;"></span>
                                <span class="text-truncate">MOA / MOU Signing</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_moa_signing">{{ $categoryCounts['moa_signing'] ?? 0 }}</span>
                        </div>
                        <div class="cal-nav-item" data-cat="monitoring_report" onclick="window.filterCalendarByCategory('monitoring_report', this)">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background: #db2777;"></span>
                                <span class="text-truncate">Monitoring & Eval</span>
                            </div>
                            <span class="badge bg-light text-secondary border font-mono" id="catCount_monitoring_report">{{ $categoryCounts['monitoring_report'] ?? 0 }}</span>
                        </div>
                    </nav>
                </div>

                {{-- Status Pills (Instant Client-Side Filtering) --}}
                <div>
                    <div class="cal-sidebar-heading">Timeline Status</div>
                    <div class="d-flex flex-wrap gap-1" id="calStatusNav">
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 extra-small fw-semibold border btn-dark text-white cal-status-btn active" data-status="all" onclick="window.filterCalendarByStatus('all', this)">
                            All
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 extra-small fw-semibold border btn-light text-secondary cal-status-btn" data-status="ongoing" onclick="window.filterCalendarByStatus('ongoing', this)">
                            <i class="mdi mdi-play-circle text-success me-1"></i>Ongoing
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 extra-small fw-semibold border btn-light text-secondary cal-status-btn" data-status="upcoming" onclick="window.filterCalendarByStatus('upcoming', this)">
                            <i class="mdi mdi-clock-start text-primary me-1"></i>Upcoming
                        </button>
                        <button type="button" class="btn btn-xs rounded-pill px-2 py-1 extra-small fw-semibold border btn-light text-secondary cal-status-btn" data-status="completed" onclick="window.filterCalendarByStatus('completed', this)">
                            <i class="mdi mdi-check-all text-muted me-1"></i>Completed
                        </button>
                    </div>
                </div>
            </div>

            {{-- FOOTER STATS: Timeline Health & Monitoring Footprint --}}
            <div class="cal-stats-widget mt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="extra-small fw-bold text-dark text-uppercase font-mono">TIMELINE FOOTPRINT</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 9px;" id="sidebarSyncStatus">LIVE</span>
                </div>

                <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                    <div class="progress-bar bg-success" id="sidebarProgressBar" style="width: 50%;"></div>
                </div>

                <div class="row g-1 extra-small font-mono mb-2">
                    <div class="col-6">
                        <span class="text-muted">Ongoing:</span>
                        <strong class="text-success ms-1" id="statOngoing">{{ $ongoingExtensionsCount }}</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-muted">Upcoming:</span>
                        <strong class="text-primary ms-1" id="statUpcoming">{{ $upcomingCommencementsCount }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Completed:</span>
                        <strong class="text-dark ms-1" id="statCompleted">{{ $completedExtensionsCount }}</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-muted">Total:</span>
                        <strong class="text-dark ms-1" id="statTotal">{{ $totalScheduledEventsCount }}</strong>
                    </div>
                </div>

                <div class="border-top pt-2 text-center text-muted extra-small font-mono">
                    {{ $inst?->school_name ?: 'Surigao del Norte State University' }}
                </div>
            </div>
        </aside>

        {{-- CENTRAL CALENDAR WORKSPACE CANVAS --}}
        <main class="cal-canvas">
            {{-- Top KPI Quick Ribbon & Active Filters --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" id="topFilterRibbon">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-white text-dark border font-mono px-3 py-1 extra-small shadow-2xs">
                        <i class="mdi mdi-play-circle text-success me-1"></i>Ongoing: <strong class="text-success ms-1" id="topKpiOngoing">{{ $ongoingExtensionsCount }}</strong>
                    </span>
                    <span class="badge bg-white text-dark border font-mono px-3 py-1 extra-small shadow-2xs">
                        <i class="mdi mdi-clock-start text-primary me-1"></i>Upcoming: <strong class="text-primary ms-1" id="topKpiUpcoming">{{ $upcomingCommencementsCount }}</strong>
                    </span>
                    <span class="badge bg-white text-dark border font-mono px-3 py-1 extra-small shadow-2xs">
                        <i class="mdi mdi-check-all text-muted me-1"></i>Completed: <strong class="text-dark ms-1" id="topKpiCompleted">{{ $completedExtensionsCount }}</strong>
                    </span>
                    <span class="badge bg-white text-dark border font-mono px-3 py-1 extra-small shadow-2xs">
                        <i class="mdi mdi-calendar-check text-warning me-1"></i>Total Ranges: <strong class="text-dark ms-1" id="topKpiTotal">{{ $totalScheduledEventsCount }}</strong>
                    </span>

                    {{-- Dynamic Active Filter Badges --}}
                    <span id="activeFilterPillsContainer" class="d-flex align-items-center gap-1 flex-wrap"></span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold d-inline-flex align-items-center gap-1" id="btnResetAllFilters" style="display: none;" onclick="window.resetAllCalendarFilters()">
                        <i class="mdi mdi-refresh text-danger"></i>Reset Filters
                    </button>
                    <div class="extra-small text-muted font-mono d-none d-xl-block">
                        <i class="mdi mdi-information-outline text-primary me-1"></i>Hover bar to highlight linked multi-week span
                    </div>
                </div>
            </div>

            {{-- Mobile Swipe Hint --}}
            <div class="alert alert-light border py-1 px-2 d-md-none extra-small text-muted d-flex align-items-center gap-1 mb-2">
                <i class="mdi mdi-gesture-swipe-horizontal text-primary fs-6"></i>
                <span>Swipe horizontally to view all calendar columns</span>
            </div>

            {{-- ================================================================
                 VIEW 1: MONTHLY CONTINUOUS RANGE CALENDAR
                 ================================================================ --}}
            <div id="viewPaneCalendar">
                <div class="ext-cal-grid-scroll-wrap">
                    <div class="ext-cal-grid-inner">
                        <div class="ext-cal-grid-header">
                            <div>Sunday</div>
                            <div>Monday</div>
                            <div>Tuesday</div>
                            <div>Wednesday</div>
                            <div>Thursday</div>
                            <div>Friday</div>
                            <div>Saturday</div>
                        </div>
                        <div class="ext-cal-weeks-container" id="extCalendarGridBody"></div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 VIEW 2: DURATION ROADMAP GANTT MATRIX (JAN - DEC)
                 =============================================================== --}}
            <div id="viewPaneGantt" style="display: none;">
                <div class="card border rounded-3 bg-white shadow-2xs mb-3">
                    <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="extra-small text-muted fw-semibold">
                            <i class="mdi mdi-chart-gantt text-primary me-1"></i>12-Month Commencement &amp; Duration Roadmap Matrix (Start Date &rarr; End Date)
                        </div>
                        <div class="d-flex align-items-center gap-3 extra-small fw-bold">
                            <span class="d-inline-flex align-items-center gap-1"><span class="rounded-1 d-inline-block" style="width: 10px; height: 10px; background: #10b981;"></span>Ongoing</span>
                            <span class="d-inline-flex align-items-center gap-1"><span class="rounded-1 d-inline-block" style="width: 10px; height: 10px; background: #3b82f6;"></span>Upcoming</span>
                            <span class="d-inline-flex align-items-center gap-1"><span class="rounded-1 d-inline-block" style="width: 10px; height: 10px; background: #64748b;"></span>Completed</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
                            <thead class="table-light extra-small text-uppercase text-muted">
                                <tr>
                                    <th style="min-width: 260px;">Extension Proposal / Activity</th>
                                    <th style="min-width: 180px;">Commencement &rarr; Completion</th>
                                    <th style="min-width: 120px;">Duration</th>
                                    <th style="min-width: 130px;">Timeline Status</th>
                                    @foreach($monthNamesShort as $mNum => $mLabel)
                                        <th class="gantt-month-cell">{{ $mLabel }}</th>
                                    @endforeach
                                    <th class="text-end pe-3" style="min-width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ganttTableBody">
                                @forelse($durationRoadmapItems as $item)
                                    @php
                                        $tState = $item['timeline_state'] ?? 'upcoming';
                                        $segClass = $tState === 'ongoing' ? 'is-active-ongoing' : ($tState === 'completed' ? 'is-active-completed' : 'is-active-upcoming');
                                    @endphp
                                    <tr class="cal-gantt-row"
                                        data-search="{{ strtolower($item['title'] . ' ' . $item['code'] . ' ' . $item['leader'] . ' ' . $item['campus']) }}"
                                        data-category="{{ strtolower($item['type'] === 'Proposal' ? 'proposal_duration' : 'conduct_activity') }}"
                                        data-timeline="{{ $tState }}"
                                        data-campus="{{ $item['campus'] }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">{{ $item['code'] }}</span>
                                                <span class="badge {{ $item['status_class'] }} font-mono" style="font-size: 9px;">{{ $item['status_label'] }}</span>
                                            </div>
                                            <a href="{{ $item['url'] }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 270px;" title="{{ $item['title'] }}">
                                                {{ $item['title'] }}
                                            </a>
                                            <div class="text-muted extra-small text-truncate" style="max-width: 270px;">
                                                {{ $item['campus'] }} &bull; {{ $item['leader'] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2 bg-light border font-mono extra-small fw-bold text-dark">
                                                <span class="text-success">{{ $item['start_formatted'] }}</span>
                                                <i class="mdi mdi-arrow-right text-muted"></i>
                                                <span class="text-danger">{{ $item['end_formatted'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono extra-small px-2 py-1">
                                                <i class="mdi mdi-timer-outline text-primary me-1"></i>{{ $item['duration_label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $item['timeline_badge']['class'] ?? 'bg-secondary' }} font-mono extra-small d-inline-block mb-1">
                                                {{ $item['timeline_badge']['label'] ?? 'SCHEDULED' }}
                                            </span>
                                            <div class="text-muted" style="font-size: 10px;">
                                                {{ $item['timeline_badge']['days_note'] ?? '' }}
                                            </div>
                                        </td>
                                        @foreach($monthNamesShort as $mNum => $mLabel)
                                            @php
                                                $isActiveMonth = in_array($mNum, $item['active_months'] ?? [], true);
                                            @endphp
                                            <td class="gantt-month-cell">
                                                @if($isActiveMonth)
                                                    <div class="gantt-bar-segment {{ $segClass }}" title="{{ $item['title'] }} ({{ $item['start_formatted'] }} → {{ $item['end_formatted'] }})"></div>
                                                @else
                                                    <div style="height: 18px; border-radius: 4px; background: #f1f5f9;"></div>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-end pe-3">
                                            <a href="{{ $item['url'] }}" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 extra-small fw-bold">
                                                <i class="mdi mdi-eye-outline me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center py-5 text-muted extra-small">
                                            No extension commencement or duration records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 VIEW 3: CHRONOLOGICAL SCHEDULE & MILESTONES LEDGER
                 ================================================================ --}}
            <div id="viewPaneList" style="display: none;">
                <div class="card border rounded-3 bg-white shadow-2xs mb-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="milestoneLedgerTable" style="font-size: 12.5px;">
                            <thead class="table-light extra-small text-uppercase text-muted">
                                <tr>
                                    <th style="width: 220px;">Date Range</th>
                                    <th style="width: 190px;">Category</th>
                                    <th>Activity / Proposal Title</th>
                                    <th style="width: 140px;">Duration Span</th>
                                    <th style="width: 160px;">Campus &amp; Leader</th>
                                    <th style="width: 140px;">Status</th>
                                    <th class="text-end pe-3" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerTableBody">
                                @forelse($calendarEvents as $evIdx => $ev)
                                    <tr class="cal-ledger-row"
                                        data-category="{{ $ev['category'] }}"
                                        data-timeline="{{ $ev['timeline_state'] }}"
                                        data-campus="{{ $ev['campus'] }}"
                                        data-search="{{ strtolower($ev['short_title'] . ' ' . $ev['code'] . ' ' . $ev['leader'] . ' ' . $ev['campus']) }}">
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-2 bg-light border font-mono extra-small fw-bold text-dark">
                                                <span>{{ $ev['range_label'] ?? $ev['start_formatted'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge text-white extra-small px-2 py-1 d-inline-flex align-items-center gap-1" style="background-color: {{ $ev['color'] }};">
                                                <i class="mdi {{ $ev['icon'] }}"></i>
                                                <span>{{ $ev['category_label'] }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-light text-dark border font-mono extra-small">{{ $ev['code'] }}</span>
                                                <span class="text-muted extra-small">{{ $ev['doc_type'] }}</span>
                                            </div>
                                            <a href="{{ $ev['url'] }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 320px;" title="{{ $ev['short_title'] }}">
                                                {{ $ev['short_title'] }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono extra-small">{{ $ev['duration_label'] }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark extra-small text-truncate" style="max-width: 155px;">{{ $ev['campus'] }}</div>
                                            <div class="text-muted extra-small text-truncate" style="max-width: 155px;">{{ $ev['leader'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ev['status_class'] }} font-mono extra-small">{{ $ev['status_label'] }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 extra-small fw-bold"
                                                    onclick="window.openCalendarEventModal({{ $evIdx }})">
                                                <i class="mdi mdi-information-outline me-1"></i>Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted extra-small">
                                            No scheduled events or commencement dates found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- ====================================================================
     MODAL: EVENT DETAILS INSPECTION
     ==================================================================== --}}
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 text-white" id="calModalHeader" style="background-color: var(--cal-primary);">
                <div>
                    <div class="extra-small text-uppercase fw-bold opacity-85" id="calModalCategory">PROPOSAL COMMENCEMENT & DURATION</div>
                    <h5 class="modal-title fw-bold text-white m-0 mt-1" id="calModalTitle">Extension Title</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small px-2 py-1" id="calModalCode">PROP-0001</span>
                    <span class="badge bg-success text-white font-mono extra-small px-2 py-1" id="calModalStatus">APPROVED</span>
                </div>

                <div class="p-3 rounded-3 border mb-3 bg-light">
                    <div class="extra-small text-muted text-uppercase fw-bold mb-1">Scheduled Date Range (Commencement &rarr; Completion)</div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="fw-bold text-dark font-mono" style="font-size: 13px;">
                            <span id="calModalStart">—</span>
                            <i class="mdi mdi-arrow-right mx-1 text-primary"></i>
                            <span id="calModalEnd">—</span>
                        </div>
                        <span class="badge bg-primary text-white font-mono extra-small px-2 py-1" id="calModalDuration">—</span>
                    </div>
                </div>

                <div class="p-3 rounded-3 border bg-white mb-3 extra-small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Campus:</span>
                        <span class="fw-bold text-dark" id="calModalCampus">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Project Leader:</span>
                        <span class="fw-bold text-dark" id="calModalLeader">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted fw-semibold">Budget Valuation:</span>
                        <span class="fw-bold text-dark font-mono" id="calModalBudget">—</span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="#" target="_blank" id="calModalPdfBtn" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-bold text-danger extra-small">
                        <i class="mdi mdi-file-pdf-box me-1"></i>View PDF
                    </a>
                    <a href="#" id="calModalOpenBtn" class="btn btn-sm btn-primary rounded-pill px-4 py-1 fw-bold extra-small">
                        <i class="mdi mdi-open-in-new me-1"></i>Open Workbench
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====================================================================
     MODAL: DAY AGENDA INSPECTOR (Click any date on calendar)
     ==================================================================== --}}
<div class="modal fade" id="calendarDayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div>
                    <div class="extra-small text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">DATE AGENDA INSPECTION</div>
                    <h5 class="modal-title fw-bold text-white m-0 mt-1" id="calDayModalTitle">Date Title</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" id="calDayModalBody">
                {{-- Day events injected dynamically --}}
            </div>
        </div>
    </div>
</div>

<script>
    window.EXT_CALENDAR_EVENTS = @json($calendarEvents);
    window.EXT_CAL_ACTIVE_CAT = 'all';
    window.EXT_CAL_ACTIVE_STATUS = 'all';
    window.EXT_CAL_ACTIVE_CAMPUS = 'all';
    window.EXT_CAL_YEAR = {{ (int) $defaultYear }};
    window.EXT_CAL_MONTH = {{ (int) $defaultMonth - 1 }};

    // Smart Month initial calculation
    (function initSmartMonth() {
        const evs = window.EXT_CALENDAR_EVENTS || [];
        if (evs.length > 0) {
            const ongoingOrUpcoming = evs.find(e => e.timeline_state === 'ongoing' || e.timeline_state === 'upcoming') || evs[0];
            if (ongoingOrUpcoming && ongoingOrUpcoming.start) {
                const parts = ongoingOrUpcoming.start.split('-');
                if (parts.length === 3) {
                    window.EXT_CAL_YEAR = parseInt(parts[0], 10);
                    window.EXT_CAL_MONTH = parseInt(parts[1], 10) - 1;
                }
            }
        }
    })();

    // Sidebar drawer toggle for mobile
    window.toggleCalSidebar = function (forceState) {
        const sidebar = document.getElementById('calSidebar');
        const backdrop = document.getElementById('calSidebarBackdrop');
        if (!sidebar) return;

        const isOpen = forceState !== undefined ? forceState : !sidebar.classList.contains('is-open');
        sidebar.classList.toggle('is-open', isOpen);
        if (backdrop) backdrop.classList.toggle('is-open', isOpen);
    };

    /**
     * Unified Filter Predicate across all views.
     */
    function matchesCurrentFilters(ev, searchQuery) {
        if (window.EXT_CAL_ACTIVE_CAT !== 'all' && ev.category !== window.EXT_CAL_ACTIVE_CAT) {
            return false;
        }
        if (window.EXT_CAL_ACTIVE_STATUS !== 'all' && ev.timeline_state !== window.EXT_CAL_ACTIVE_STATUS) {
            return false;
        }
        if (window.EXT_CAL_ACTIVE_CAMPUS !== 'all' && ev.campus !== window.EXT_CAL_ACTIVE_CAMPUS) {
            return false;
        }
        if (searchQuery !== '') {
            const haystack = (ev.title + ' ' + (ev.short_title || '') + ' ' + (ev.code || '') + ' ' + (ev.leader || '') + ' ' + (ev.campus || '')).toLowerCase();
            return haystack.includes(searchQuery);
        }
        return true;
    }

    /**
     * Recalculates and updates live KPI counters, progress bars, and active badges.
     */
    window.updateCalendarStats = function (filteredEvents) {
        const ongoing = filteredEvents.filter(e => e.timeline_state === 'ongoing').length;
        const upcoming = filteredEvents.filter(e => e.timeline_state === 'upcoming').length;
        const completed = filteredEvents.filter(e => e.timeline_state === 'completed').length;
        const total = filteredEvents.length;

        // Top Ribbon
        const topOngoing = document.getElementById('topKpiOngoing');
        const topUpcoming = document.getElementById('topKpiUpcoming');
        const topCompleted = document.getElementById('topKpiCompleted');
        const topTotal = document.getElementById('topKpiTotal');
        if (topOngoing) topOngoing.textContent = ongoing;
        if (topUpcoming) topUpcoming.textContent = upcoming;
        if (topCompleted) topCompleted.textContent = completed;
        if (topTotal) topTotal.textContent = total;

        // Sidebar
        const statOngoing = document.getElementById('statOngoing');
        const statUpcoming = document.getElementById('statUpcoming');
        const statCompleted = document.getElementById('statCompleted');
        const statTotal = document.getElementById('statTotal');
        if (statOngoing) statOngoing.textContent = ongoing;
        if (statUpcoming) statUpcoming.textContent = upcoming;
        if (statCompleted) statCompleted.textContent = completed;
        if (statTotal) statTotal.textContent = total;

        // Progress bar
        const pb = document.getElementById('sidebarProgressBar');
        if (pb) {
            const pct = total > 0 ? Math.round(((ongoing + completed) / total) * 100) : 0;
            pb.style.width = pct + '%';
        }

        // Active Filter Badges
        const badgesContainer = document.getElementById('activeFilterPillsContainer');
        const resetBtn = document.getElementById('btnResetAllFilters');
        const searchInput = document.getElementById('calSearchInput');
        const searchVal = (searchInput?.value || '').trim();

        const hasActiveFilters = window.EXT_CAL_ACTIVE_CAT !== 'all' ||
                                 window.EXT_CAL_ACTIVE_STATUS !== 'all' ||
                                 window.EXT_CAL_ACTIVE_CAMPUS !== 'all' ||
                                 searchVal !== '';

        if (resetBtn) resetBtn.style.display = hasActiveFilters ? 'inline-flex' : 'none';

        if (badgesContainer) {
            let pillsHtml = '';
            if (window.EXT_CAL_ACTIVE_CAT !== 'all') {
                pillsHtml += `<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 extra-small">
                    Cat: ${window.EXT_CAL_ACTIVE_CAT.replace('_', ' ')}
                    <i class="mdi mdi-close cursor-pointer ms-1 text-danger" onclick="window.filterCalendarByCategory('all', null)"></i>
                </span>`;
            }
            if (window.EXT_CAL_ACTIVE_STATUS !== 'all') {
                pillsHtml += `<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 extra-small">
                    Status: ${window.EXT_CAL_ACTIVE_STATUS}
                    <i class="mdi mdi-close cursor-pointer ms-1 text-danger" onclick="window.filterCalendarByStatus('all', null)"></i>
                </span>`;
            }
            if (window.EXT_CAL_ACTIVE_CAMPUS !== 'all') {
                pillsHtml += `<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 extra-small">
                    Campus: ${window.EXT_CAL_ACTIVE_CAMPUS}
                    <i class="mdi mdi-close cursor-pointer ms-1 text-danger" onclick="window.filterCalendarByCampus('all')"></i>
                </span>`;
            }
            if (searchVal !== '') {
                pillsHtml += `<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 extra-small">
                    Search: "${searchVal}"
                    <i class="mdi mdi-close cursor-pointer ms-1 text-danger" onclick="window.clearCalendarSearch()"></i>
                </span>`;
            }
            badgesContainer.innerHTML = pillsHtml;
        }
    };

    /**
     * Render the Month Calendar Grid with continuous range bars across columns.
     */
    window.renderExtensionCalendarGrid = function () {
        const gridBody = document.getElementById('extCalendarGridBody');
        const labelEl = document.getElementById('calendarCurrentMonthLabel');
        const miniLabel = document.getElementById('calMiniLabel');
        if (!gridBody) return;

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const monthShortNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        if (labelEl) {
            labelEl.textContent = monthNames[window.EXT_CAL_MONTH] + ' ' + window.EXT_CAL_YEAR;
        }
        if (miniLabel) {
            miniLabel.textContent = monthShortNames[window.EXT_CAL_MONTH] + ' ' + window.EXT_CAL_YEAR;
        }

        const firstDay = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH, 1);
        const startWeekday = firstDay.getDay();
        const daysInMonth = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH + 1, 0).getDate();
        const prevMonthDays = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH, 0).getDate();

        const todayObj = new Date();
        const todayStr = todayObj.getFullYear() + '-' + String(todayObj.getMonth() + 1).padStart(2, '0') + '-' + String(todayObj.getDate()).padStart(2, '0');

        const searchVal = (document.getElementById('calSearchInput')?.value || '').toLowerCase().trim();

        const filteredEvents = (window.EXT_CALENDAR_EVENTS || [])
            .map((ev, idx) => Object.assign({}, ev, { _idx: idx }))
            .filter(ev => matchesCurrentFilters(ev, searchVal));

        // Update live stats
        window.updateCalendarStats(filteredEvents);

        const totalCells = Math.ceil((startWeekday + daysInMonth) / 7) * 7;
        const totalWeeks = totalCells / 7;

        const cells = [];
        for (let i = 0; i < totalCells; i++) {
            let cellYear = window.EXT_CAL_YEAR;
            let cellMonth = window.EXT_CAL_MONTH;
            let dayNum = 1;
            let isOutside = false;

            if (i < startWeekday) {
                isOutside = true;
                dayNum = prevMonthDays - startWeekday + i + 1;
                cellMonth = window.EXT_CAL_MONTH - 1;
                if (cellMonth < 0) {
                    cellMonth = 11;
                    cellYear--;
                }
            } else if (i >= startWeekday + daysInMonth) {
                isOutside = true;
                dayNum = i - (startWeekday + daysInMonth) + 1;
                cellMonth = window.EXT_CAL_MONTH + 1;
                if (cellMonth > 11) {
                    cellMonth = 0;
                    cellYear++;
                }
            } else {
                dayNum = i - startWeekday + 1;
            }

            const dateStr = cellYear + '-' + String(cellMonth + 1).padStart(2, '0') + '-' + String(dayNum).padStart(2, '0');
            cells.push({
                dayNum: dayNum,
                dateStr: dateStr,
                isOutside: isOutside,
                isToday: dateStr === todayStr,
                isWeekend: (i % 7 === 0) || (i % 7 === 6),
                monthShort: monthNames[cellMonth].slice(0, 3)
            });
        }

        // Render Sidebar Mini Calendar
        window.renderSidebarMiniCalendar(cells);

        let html = '';
        for (let w = 0; w < totalWeeks; w++) {
            const weekCells = cells.slice(w * 7, w * 7 + 7);
            const weekStartStr = weekCells[0].dateStr;
            const weekEndStr = weekCells[6].dateStr;

            const weekSegments = [];
            filteredEvents.forEach(ev => {
                if (ev.end < weekStartStr || ev.start > weekEndStr) {
                    return;
                }
                const clampedStart = ev.start < weekStartStr ? weekStartStr : ev.start;
                const clampedEnd = ev.end > weekEndStr ? weekEndStr : ev.end;

                const startColIdx = weekCells.findIndex(c => c.dateStr === clampedStart);
                const endColIdx = weekCells.findIndex(c => c.dateStr === clampedEnd);
                if (startColIdx === -1 || endColIdx === -1) return;

                const startCol = startColIdx + 1;
                const spanCols = Math.max(1, endColIdx - startColIdx + 1);

                weekSegments.push({
                    ev: ev,
                    startCol: startCol,
                    endCol: endColIdx + 1,
                    spanCols: spanCols,
                    continuesLeft: ev.start < weekStartStr,
                    continuesRight: ev.end > weekEndStr
                });
            });

            weekSegments.sort((a, b) => {
                if (a.startCol !== b.startCol) return a.startCol - b.startCol;
                if (b.spanCols !== a.spanCols) return b.spanCols - a.spanCols;
                return String(a.ev.start).localeCompare(String(b.ev.start));
            });

            const lanes = [];
            weekSegments.forEach(seg => {
                let placedLane = -1;
                for (let l = 0; l < lanes.length; l++) {
                    const lastInLane = lanes[l][lanes[l].length - 1];
                    if (lastInLane.endCol < seg.startCol) {
                        placedLane = l;
                        break;
                    }
                }
                if (placedLane === -1) {
                    placedLane = lanes.length;
                    lanes.push([]);
                }
                seg.lane = placedLane + 1;
                lanes[placedLane].push(seg);
            });

            html += '<div class="ext-cal-week-row">';

            // Background Grid
            html += '<div class="ext-cal-week-bg">';
            weekCells.forEach(c => {
                const bgClasses = [
                    'ext-cal-bg-cell',
                    c.isOutside ? 'is-outside' : '',
                    c.isWeekend ? 'is-weekend' : '',
                    c.isToday ? 'is-today' : ''
                ].filter(Boolean).join(' ');
                html += `<div class="${bgClasses}" id="bgCell_${c.dateStr}"></div>`;
            });
            html += '</div>';

            // Day Numbers & Clickable Header
            html += '<div class="ext-cal-week-day-headers">';
            weekCells.forEach(c => {
                const activeOnDay = filteredEvents.filter(ev => c.dateStr >= ev.start && c.dateStr <= ev.end).length;
                html += `<div class="ext-cal-day-header-cell ${c.isOutside ? 'is-outside' : ''}" onclick="window.openDayAgendaModal('${c.dateStr}')" title="Click to inspect agenda for ${c.dateStr}">`;
                html += '<div class="d-flex align-items-center gap-1">';
                html += `<span class="ext-cal-day-num ${c.isToday ? 'is-today' : ''}">${c.dayNum}</span>`;
                if (c.dayNum === 1 || (w === 0 && c === weekCells[0])) {
                    html += `<span class="text-muted extra-small fw-bold">${c.monthShort}</span>`;
                }
                if (c.isToday) {
                    html += '<span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 8px; padding: 1px 4px;">TODAY</span>';
                }
                html += '</div>';
                if (activeOnDay > 0) {
                    html += `<span class="badge bg-light text-secondary border font-mono" style="font-size: 9px; padding: 1px 5px;" title="${activeOnDay} active ranges on this date">${activeOnDay}</span>`;
                }
                html += '</div>';
            });
            html += '</div>';

            // Foreground Range Bars with Synchronized Multi-Segment Hover
            html += '<div class="ext-cal-week-lanes">';
            weekSegments.forEach(seg => {
                const ev = seg.ev;
                const barClasses = [
                    'ext-cal-range-bar',
                    seg.continuesLeft ? 'continues-left' : '',
                    seg.continuesRight ? 'continues-right' : ''
                ].filter(Boolean).join(' ');

                const rangeText = ev.range_label || (ev.start_formatted + ' – ' + ev.end_formatted);
                const tooltipText = `[${ev.code}] ${ev.short_title} • ${rangeText} (${ev.duration_label})`;

                html += `<div class="${barClasses}" `
                    + `data-event-id="${ev.id}" `
                    + `style="grid-column: ${seg.startCol} / span ${seg.spanCols}; grid-row: ${seg.lane}; background-color: ${ev.color};" `
                    + `onmouseenter="window.onRangeBarHover('${ev.id}', true)" `
                    + `onmouseleave="window.onRangeBarHover('${ev.id}', false)" `
                    + `onclick="window.openCalendarEventModal(${ev._idx})" `
                    + `title="${tooltipText.replace(/"/g, '&quot;')}">`;

                html += '<div class="ext-cal-range-bar-main">';
                if (seg.continuesLeft) {
                    html += '<i class="mdi mdi-chevron-double-left opacity-85 flex-shrink-0"></i>';
                } else {
                    html += `<i class="mdi ${ev.icon || 'mdi-calendar-range'} flex-shrink-0"></i>`;
                }
                if (seg.spanCols >= 2) {
                    html += `<span class="ext-cal-range-code">${ev.code}</span>`;
                }
                html += `<span class="ext-cal-range-title">${ev.short_title}</span>`;
                html += '</div>';

                html += '<div class="d-flex align-items-center gap-1 flex-shrink-0">';
                if (seg.spanCols >= 3) {
                    html += `<span class="ext-cal-range-pill">${rangeText}</span>`;
                }
                if (seg.continuesRight) {
                    html += '<i class="mdi mdi-chevron-double-right opacity-85"></i>';
                }
                html += '</div>';

                html += '</div>';
            });
            html += '</div>';

            html += '</div>';
        }

        gridBody.innerHTML = html;
    };

    /**
     * Linked Multi-Segment Hover: Highlights all bars belonging to the same event.
     */
    window.onRangeBarHover = function (evId, isHovered) {
        if (!evId) return;
        document.querySelectorAll(`.ext-cal-range-bar[data-event-id="${evId}"]`).forEach(el => {
            el.classList.toggle('is-linked-hover', isHovered);
        });
    };

    /**
     * Render the compact mini calendar in the sidebar.
     */
    window.renderSidebarMiniCalendar = function (cells) {
        const miniGrid = document.getElementById('calMiniGrid');
        if (!miniGrid) return;

        let miniHtml = `
            <div class="cal-mini-day-label">S</div>
            <div class="cal-mini-day-label">M</div>
            <div class="cal-mini-day-label">T</div>
            <div class="cal-mini-day-label">W</div>
            <div class="cal-mini-day-label">T</div>
            <div class="cal-mini-day-label">F</div>
            <div class="cal-mini-day-label">S</div>
        `;

        cells.forEach(c => {
            const hasEv = (window.EXT_CALENDAR_EVENTS || []).some(ev => c.dateStr >= ev.start && c.dateStr <= ev.end);
            const classes = [
                'cal-mini-cell',
                c.isOutside ? 'is-outside' : '',
                c.isToday ? 'is-today' : '',
                hasEv ? 'has-events' : ''
            ].filter(Boolean).join(' ');

            miniHtml += `<div class="${classes}" onclick="window.jumpCalendarToDate('${c.dateStr}')" title="${c.dateStr} (Click to jump)">${c.dayNum}</div>`;
        });

        miniGrid.innerHTML = miniHtml;
    };

    window.jumpCalendarToDate = function (dateStr) {
        if (!dateStr) return;
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            window.EXT_CAL_YEAR = parseInt(parts[0], 10);
            window.EXT_CAL_MONTH = parseInt(parts[1], 10) - 1;
            window.renderExtensionCalendarGrid();

            // Highlight that date's background cell with pulse effect
            setTimeout(() => {
                const cell = document.getElementById(`bgCell_${dateStr}`);
                if (cell) {
                    cell.classList.add('is-highlighted');
                    setTimeout(() => cell.classList.remove('is-highlighted'), 2000);
                }
            }, 100);
        }
    };

    window.navigateCalendarMonth = function (delta) {
        window.EXT_CAL_MONTH += delta;
        if (window.EXT_CAL_MONTH < 0) {
            window.EXT_CAL_MONTH = 11;
            window.EXT_CAL_YEAR--;
        } else if (window.EXT_CAL_MONTH > 11) {
            window.EXT_CAL_MONTH = 0;
            window.EXT_CAL_YEAR++;
        }
        window.applyAllFilters();
    };

    window.jumpCalendarToToday = function () {
        const now = new Date();
        window.EXT_CAL_YEAR = now.getFullYear();
        window.EXT_CAL_MONTH = now.getMonth();
        window.applyAllFilters();
    };

    /**
     * Client-Side Category Filtering
     */
    window.filterCalendarByCategory = function (cat, el) {
        window.EXT_CAL_ACTIVE_CAT = cat;
        document.querySelectorAll('#calCategoryNav .cal-nav-item').forEach(item => {
            item.classList.toggle('active', item.getAttribute('data-cat') === cat);
        });
        window.applyAllFilters();
    };

    /**
     * Client-Side Timeline Status Filtering
     */
    window.filterCalendarByStatus = function (status, el) {
        window.EXT_CAL_ACTIVE_STATUS = status;
        document.querySelectorAll('#calStatusNav .cal-status-btn').forEach(btn => {
            const isActive = btn.getAttribute('data-status') === status;
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('btn-dark', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('btn-light', !isActive);
            btn.classList.toggle('text-secondary', !isActive);
        });
        window.applyAllFilters();
    };

    /**
     * Client-Side Campus Filtering
     */
    window.filterCalendarByCampus = function (campus) {
        window.EXT_CAL_ACTIVE_CAMPUS = campus;
        window.applyAllFilters();
    };

    /**
     * Central Coordinator: Applies all active filters to Calendar, Gantt, and Ledger.
     */
    window.applyAllFilters = function () {
        const q = (document.getElementById('calSearchInput')?.value || '').toLowerCase().trim();

        // 1. Re-render month grid
        window.renderExtensionCalendarGrid();

        // 2. Filter Gantt Rows
        let ganttVisibleCount = 0;
        document.querySelectorAll('.cal-gantt-row').forEach(row => {
            const rowCampus = row.getAttribute('data-campus') || '';
            const rowCat = row.getAttribute('data-category') || '';
            const rowTimeline = row.getAttribute('data-timeline') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesCampus = window.EXT_CAL_ACTIVE_CAMPUS === 'all' || rowCampus === window.EXT_CAL_ACTIVE_CAMPUS;
            const matchesCat = window.EXT_CAL_ACTIVE_CAT === 'all' || rowCat === window.EXT_CAL_ACTIVE_CAT;
            const matchesStatus = window.EXT_CAL_ACTIVE_STATUS === 'all' || rowTimeline === window.EXT_CAL_ACTIVE_STATUS;
            const matchesSearch = q === '' || rowSearch.includes(q);

            const isVisible = matchesCampus && matchesCat && matchesStatus && matchesSearch;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) ganttVisibleCount++;
        });
        const ganttCountEl = document.getElementById('btnGanttCount');
        if (ganttCountEl) ganttCountEl.textContent = ganttVisibleCount;

        // 3. Filter Ledger Rows
        let ledgerVisibleCount = 0;
        document.querySelectorAll('.cal-ledger-row').forEach(row => {
            const rowCampus = row.getAttribute('data-campus') || '';
            const rowCat = row.getAttribute('data-category') || '';
            const rowTimeline = row.getAttribute('data-timeline') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesCampus = window.EXT_CAL_ACTIVE_CAMPUS === 'all' || rowCampus === window.EXT_CAL_ACTIVE_CAMPUS;
            const matchesCat = window.EXT_CAL_ACTIVE_CAT === 'all' || rowCat === window.EXT_CAL_ACTIVE_CAT;
            const matchesStatus = window.EXT_CAL_ACTIVE_STATUS === 'all' || rowTimeline === window.EXT_CAL_ACTIVE_STATUS;
            const matchesSearch = q === '' || rowSearch.includes(q);

            const isVisible = matchesCampus && matchesCat && matchesStatus && matchesSearch;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) ledgerVisibleCount++;
        });
        const ledgerCountEl = document.getElementById('btnLedgerCount');
        if (ledgerCountEl) ledgerCountEl.textContent = ledgerVisibleCount;
    };

    window.resetAllCalendarFilters = function () {
        window.EXT_CAL_ACTIVE_CAT = 'all';
        window.EXT_CAL_ACTIVE_STATUS = 'all';
        window.EXT_CAL_ACTIVE_CAMPUS = 'all';

        const searchInput = document.getElementById('calSearchInput');
        if (searchInput) searchInput.value = '';

        const campusSelect = document.getElementById('calCampusSelect');
        if (campusSelect) campusSelect.value = 'all';

        document.querySelectorAll('#calCategoryNav .cal-nav-item').forEach(item => {
            item.classList.toggle('active', item.getAttribute('data-cat') === 'all');
        });

        document.querySelectorAll('#calStatusNav .cal-status-btn').forEach(btn => {
            const isActive = btn.getAttribute('data-status') === 'all';
            btn.classList.toggle('active', isActive);
            btn.classList.toggle('btn-dark', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('btn-light', !isActive);
            btn.classList.toggle('text-secondary', !isActive);
        });

        window.applyAllFilters();
    };

    window.clearCalendarSearch = function () {
        const searchInput = document.getElementById('calSearchInput');
        if (searchInput) {
            searchInput.value = '';
            window.applyAllFilters();
        }
    };

    window.switchCalendarViewMode = function (mode, btn) {
        if (btn && btn.parentElement) {
            btn.parentElement.querySelectorAll('.cal-segmented-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        const paneCal = document.getElementById('viewPaneCalendar');
        const paneGantt = document.getElementById('viewPaneGantt');
        const paneList = document.getElementById('viewPaneList');
        const monthNav = document.getElementById('calMonthNavGroup');

        if (paneCal) paneCal.style.display = mode === 'calendar' ? '' : 'none';
        if (paneGantt) paneGantt.style.display = mode === 'gantt' ? '' : 'none';
        if (paneList) paneList.style.display = mode === 'list' ? '' : 'none';

        if (monthNav) {
            monthNav.style.opacity = mode === 'calendar' ? '1' : '0.5';
        }
    };

    window.openCalendarEventModal = function (idx) {
        const ev = (window.EXT_CALENDAR_EVENTS || [])[idx];
        if (!ev) return;

        document.getElementById('calModalHeader').style.backgroundColor = ev.color || 'var(--cal-primary)';
        document.getElementById('calModalCategory').textContent = (ev.category_label || 'EXTENSION MILESTONE').toUpperCase();
        document.getElementById('calModalTitle').textContent = ev.short_title || ev.title;
        document.getElementById('calModalCode').textContent = ev.code + ' • ' + ev.doc_type;
        document.getElementById('calModalStatus').textContent = ev.status_label;
        document.getElementById('calModalStart').textContent = ev.start_formatted;
        document.getElementById('calModalEnd').textContent = ev.end_formatted;
        document.getElementById('calModalDuration').textContent = ev.duration_label;
        document.getElementById('calModalBudget').textContent = ev.budget_formatted;
        document.getElementById('calModalCampus').textContent = ev.campus;
        document.getElementById('calModalLeader').textContent = ev.leader;
        document.getElementById('calModalOpenBtn').href = ev.url;
        document.getElementById('calModalPdfBtn').href = ev.pdf_url;

        const modalEl = document.getElementById('calendarEventModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    };

    /**
     * Day Agenda Modal: Shows all milestones active on the clicked date.
     */
    window.openDayAgendaModal = function (dateStr) {
        const evs = (window.EXT_CALENDAR_EVENTS || []).filter(e => dateStr >= e.start && dateStr <= e.end);
        const titleEl = document.getElementById('calDayModalTitle');
        const bodyEl = document.getElementById('calDayModalBody');
        if (!titleEl || !bodyEl) return;

        const dateObj = new Date(dateStr + 'T00:00:00');
        const dateFormatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        titleEl.textContent = dateFormatted;

        if (evs.length === 0) {
            bodyEl.innerHTML = `
                <div class="text-center py-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-muted mb-2" style="width: 48px; height: 48px;">
                        <i class="mdi mdi-calendar-blank-outline fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Active Milestones</h6>
                    <p class="text-muted extra-small m-0">There are no extension commencements, activity conducts, or deadlines scheduled on this date.</p>
                </div>
            `;
        } else {
            let html = '<div class="d-flex flex-column gap-2">';
            evs.forEach(ev => {
                const rangeLabel = ev.range_label || (ev.start_formatted + ' – ' + ev.end_formatted);
                html += `
                    <div class="p-3 rounded-3 border bg-white shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge text-white extra-small px-2 py-1" style="background-color: ${ev.color};">
                                <i class="mdi ${ev.icon} me-1"></i>${ev.category_label}
                            </span>
                            <span class="badge ${ev.status_class} font-mono extra-small">${ev.status_label}</span>
                        </div>
                        <h6 class="fw-bold text-dark extra-small mb-1">${ev.short_title || ev.title}</h6>
                        <div class="extra-small text-muted font-mono mb-2">
                            <span>${rangeLabel}</span> &bull; <span>${ev.duration_label}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="extra-small text-muted font-mono text-truncate" style="max-width: 220px;">
                                ${ev.campus} &bull; ${ev.leader}
                            </span>
                            <div class="d-flex align-items-center gap-1">
                                <a href="${ev.pdf_url}" target="_blank" class="btn btn-xs btn-light border rounded-pill px-2 py-1 extra-small text-danger fw-bold">
                                    <i class="mdi mdi-file-pdf-box me-1"></i>PDF
                                </a>
                                <a href="${ev.url}" class="btn btn-xs btn-primary rounded-pill px-3 py-1 extra-small fw-bold">
                                    Open
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            bodyEl.innerHTML = html;
        }

        const modalEl = document.getElementById('calendarDayModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    };

    /**
     * Export visible schedule to CSV.
     */
    window.exportEventsToCSV = function () {
        const evs = (window.EXT_CALENDAR_EVENTS || []);
        if (evs.length === 0) {
            alert('No extension milestones available to export.');
            return;
        }

        const headers = ['Code', 'Type', 'Title', 'Category', 'Start Date', 'End Date', 'Duration', 'Campus', 'Project Leader', 'Status', 'Budget'];
        const rows = evs.map(e => [
            `"${e.code || ''}"`,
            `"${e.doc_type || ''}"`,
            `"${(e.title || '').replace(/"/g, '""')}"`,
            `"${e.category_label || ''}"`,
            `"${e.start || ''}"`,
            `"${e.end || ''}"`,
            `"${e.duration_label || ''}"`,
            `"${e.campus || ''}"`,
            `"${e.leader || ''}"`,
            `"${e.status_label || ''}"`,
            `"${e.budget_formatted || ''}"`
        ]);

        const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `extension_schedule_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.printCalendarView = function () {
        window.print();
    };

    window.onScopePeriodChange = function (val) {
        const url = new URL(window.location.href);
        url.searchParams.set('period', val);
        window.location.href = url.toString();
    };

    // Live search listener
    document.getElementById('calSearchInput')?.addEventListener('input', function () {
        window.applyAllFilters();
    });

    // Keyboard Shortcuts
    window.addEventListener('keydown', function (e) {
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        const isTyping = activeTag === 'input' || activeTag === 'select' || activeTag === 'textarea';

        // Ctrl + K for search
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const input = document.getElementById('calSearchInput');
            if (input) {
                input.focus();
                input.select();
            }
            return;
        }

        // Escape to clear search or close offcanvas
        if (e.key === 'Escape') {
            if (isTyping) {
                document.activeElement.blur();
                window.clearCalendarSearch();
            }
            window.toggleCalSidebar(false);
            return;
        }

        // View Mode Shortcuts (1, 2, 3) when not typing
        if (!isTyping) {
            if (e.key === '1') {
                window.switchCalendarViewMode('calendar', document.getElementById('btnViewCalendar'));
            } else if (e.key === '2') {
                window.switchCalendarViewMode('gantt', document.getElementById('btnViewGantt'));
            } else if (e.key === '3') {
                window.switchCalendarViewMode('list', document.getElementById('btnViewList'));
            } else if (e.key.toLowerCase() === 't') {
                window.jumpCalendarToToday();
            } else if (e.key === 'ArrowLeft') {
                window.navigateCalendarMonth(-1);
            } else if (e.key === 'ArrowRight') {
                window.navigateCalendarMonth(1);
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        window.applyAllFilters();
    });
</script>

</body>
</html>
