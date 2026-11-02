@php
    $currentTheme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $primary = $currentTheme->primary_color ?? '#047857';
    $secondary = $currentTheme->secondary_color ?? '#0284c7';
    $accent = $currentTheme->accent_color ?? '#f59e0b';
    $radius = $currentTheme->border_radius ?? '10px';
    $sidebarTheme = $currentTheme->sidebar_theme ?? 'light';

    // Granular sidebar tokens with fallback
    $sidebarBg = $currentTheme->sidebar_bg ?: ($sidebarTheme === 'dark' ? '#0f172a' : '#ffffff');
    $sidebarText = $currentTheme->sidebar_text ?: ($sidebarTheme === 'dark' ? '#cbd5e1' : '#334155');
    $sidebarActiveBg = $currentTheme->sidebar_active_bg ?: ($sidebarTheme === 'dark' ? 'rgba(5, 150, 105, 0.22)' : 'color-mix(in srgb, ' . $primary . ' 12%, #ffffff)');
    $sidebarActiveText = $currentTheme->sidebar_active_text ?: ($sidebarTheme === 'dark' ? '#34d399' : $primary);
    $sidebarBorder = $currentTheme->sidebar_border ?: ($sidebarTheme === 'dark' ? '#1e293b' : '#e2e8f0');

    // Granular topbar tokens
    $topbarBg = $currentTheme->topbar_bg ?: '#ffffff';
    $topbarText = $currentTheme->topbar_text ?: '#0f172a';
    $topbarBorder = $currentTheme->topbar_border ?: '#e2e8f0';

    // Granular button tokens
    $btnPrimaryBg = $currentTheme->btn_primary_bg ?: $primary;
    $btnPrimaryText = $currentTheme->btn_primary_text ?: '#ffffff';
    $btnSecondaryBg = $currentTheme->btn_secondary_bg ?: $secondary;
    $btnSecondaryText = $currentTheme->btn_secondary_text ?: '#ffffff';
    $btnRadius = $currentTheme->btn_radius ?: '8px';
    $btnElevation = $currentTheme->btn_elevation ?: 'subtle';

    // Granular card tokens
    $cardBg = $currentTheme->card_bg ?: '#ffffff';
    $cardBorder = $currentTheme->card_border ?: '#e2e8f0';
    $cardRadius = $currentTheme->card_radius ?: ($currentTheme->border_radius ?: '12px');
@endphp

<!-- Preconnect & Direct Google Fonts Loading for Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --dss-primary: {{ $primary }};
        --dss-primary-hover: color-mix(in srgb, {{ $primary }} 85%, black);
        --dss-primary-soft: color-mix(in srgb, {{ $primary }} 10%, #ffffff);
        --dss-primary-border: color-mix(in srgb, {{ $primary }} 25%, #ffffff);

        --dss-secondary: {{ $secondary }};
        --dss-secondary-hover: color-mix(in srgb, {{ $secondary }} 85%, black);
        --dss-secondary-soft: color-mix(in srgb, {{ $secondary }} 10%, #ffffff);
        --dss-secondary-border: color-mix(in srgb, {{ $secondary }} 25%, #ffffff);

        --dss-accent: {{ $accent }};
        --dss-accent-soft: color-mix(in srgb, {{ $accent }} 12%, #ffffff);
        --dss-accent-border: color-mix(in srgb, {{ $accent }} 30%, #ffffff);

        --dss-radius: {{ $radius }};
        --dss-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;

        /* Detailed Sidebar Tokens */
        --dss-sidebar-bg: {{ $sidebarBg }};
        --dss-sidebar-text: {{ $sidebarText }};
        --dss-sidebar-muted: color-mix(in srgb, {{ $sidebarText }} 60%, transparent);
        --dss-sidebar-hover: color-mix(in srgb, {{ $sidebarText }} 8%, transparent);
        --dss-sidebar-active: {{ $sidebarActiveBg }};
        --dss-sidebar-active-text: {{ $sidebarActiveText }};
        --dss-sidebar-border: {{ $sidebarBorder }};

        /* Detailed Topbar Tokens */
        --dss-topbar-bg: {{ $topbarBg }};
        --dss-topbar-text: {{ $topbarText }};
        --dss-topbar-border: {{ $topbarBorder }};

        /* Detailed Button Tokens */
        --dss-btn-primary-bg: {{ $btnPrimaryBg }};
        --dss-btn-primary-text: {{ $btnPrimaryText }};
        --dss-btn-secondary-bg: {{ $btnSecondaryBg }};
        --dss-btn-secondary-text: {{ $btnSecondaryText }};
        --dss-btn-radius: {{ $btnRadius }};
        --dss-btn-elevation: {{ $btnElevation }};

        /* Detailed Card Tokens */
        --dss-card-bg: {{ $cardBg }};
        --dss-card-border: {{ $cardBorder }};
        --dss-card-radius: {{ $cardRadius }};
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: var(--dss-font);
        background-color: #f8fafc;
        color: #0f172a;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        letter-spacing: -0.011em;
        line-height: 1.5;
    }

    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        font-family: var(--dss-font);
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.025em;
        line-height: 1.25;
        margin-top: 0;
    }

    p {
        margin-top: 0;
        margin-bottom: 0.5rem;
    }

    *, html, body, input, select, textarea, button, span, p, h1, h2, h3, h4, h5, h6, table, th, td, .badge, .btn, .font-mono {
        font-family: var(--dss-font) !important;
    }

    .font-mono {
        font-family: var(--dss-font) !important;
    }

    .dss-app-container {
        display: flex;
        min-height: 100vh;
        width: 100%;
        background-color: #f8fafc;
    }

    /* Clean & Steady Canva-Style Sidebar */
    .dss-sidebar {
        width: 280px;
        min-width: 280px;
        background: var(--dss-sidebar-bg, #ffffff) !important;
        color: var(--dss-sidebar-text, #0f172a) !important;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--dss-sidebar-border, #f1f5f9) !important;
        position: sticky;
        top: 0;
        height: 100vh;
        z-index: 1000;
        transition: all 0.2s ease;
    }

    .dss-sidebar-header {
        height: 64px;
        min-height: 64px;
        padding: 0 1.15rem;
        border-bottom: 1px solid var(--dss-sidebar-border, #f1f5f9) !important;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: var(--dss-sidebar-bg, #ffffff) !important;
    }

    .dss-sidebar-header .text-dark {
        color: var(--dss-sidebar-text, #0f172a) !important;
    }

    .dss-sidebar-body {
        flex: 1;
        padding: 0.85rem 0.65rem;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .dss-sidebar-body::-webkit-scrollbar {
        width: 4px;
    }
    .dss-sidebar-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .dss-sidebar-body::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .dss-sidebar-footer {
        height: 64px;
        min-height: 64px;
        padding: 0 1.15rem;
        border-top: 1px solid var(--dss-sidebar-border, #f1f5f9) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--dss-sidebar-bg, #ffffff) !important;
        color: var(--dss-sidebar-text, #0f172a) !important;
    }

    .dss-sidebar-footer .text-dark {
        color: var(--dss-sidebar-text, #0f172a) !important;
    }

    .dss-sidebar-section-title {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--dss-sidebar-muted, #94a3b8) !important;
        padding: 0.75rem 0.65rem 0.25rem 0.65rem;
        margin-top: 0.35rem;
        margin-bottom: 0.1rem;
    }

    .dss-sidebar-section-title:first-child {
        margin-top: 0;
        padding-top: 0.15rem;
    }

    .dss-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 100vh;
        background-color: #f8fafc;
    }

    /* Page Content & Footer Spacing — single source of truth */
    .dss-page-content {
        padding: .75rem;
    }

    .dss-page-footer {
        padding: 0.75rem 1.75rem;
    }

    @media (max-width: 767.98px) {
        .dss-page-content {
            padding: 1.25rem 1rem;
        }
        .dss-page-footer {
            padding: 0.65rem 1rem;
        }
    }

    /* Dynamic Topbar / Navbar Theming */
    .dss-navbar {
        height: 64px;
        min-height: 64px;
        background: var(--dss-topbar-bg) !important;
        border-bottom: 1px solid var(--dss-topbar-border) !important;
        color: var(--dss-topbar-text) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.75rem;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
    }

    .dss-navbar .text-dark {
        color: var(--dss-topbar-text) !important;
    }

    /* Flup Minimal Fixed / Sticky Topbar */
    body[data-ui-template="flup_minimal"] .flup-topbar {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px 24px;
        margin-left: -24px;
        margin-right: -24px;
        margin-top: 0;
        margin-bottom: 24px;
        border-bottom: 1px solid #e8eeec;
        box-shadow: 0 1px 4px rgba(15, 41, 30, 0.03);
    }

    body[data-ui-template="flup_minimal"] .dss-main-content {
        padding-top: 0 !important;
    }

    @media (max-width: 991.98px) {
        body[data-ui-template="flup_minimal"] .flup-topbar {
            padding: 12px 16px;
            margin-left: -16px;
            margin-right: -16px;
            margin-bottom: 16px;
        }
    }

    /* Canva-Style Steady Nav Links on Sidebar (Strictly 1 Line) */
    .dss-nav-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.525rem 0.75rem;
        border-radius: 10px;
        color: var(--dss-sidebar-text, #334155) !important;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.84rem;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        margin-bottom: 0.2rem;
        line-height: 1.35;
        border: 1px solid transparent;
        position: relative;
        white-space: nowrap !important;
    }

    .dss-nav-link span,
    .dss-nav-link div {
        white-space: nowrap !important;
    }

    .dss-nav-link i {
        font-size: 1.2rem;
        color: var(--dss-sidebar-muted, #64748b);
        width: 22px;
        text-align: center;
        transition: color 0.15s ease, transform 0.15s ease;
        flex-shrink: 0;
    }

    .dss-nav-link:hover {
        background-color: var(--dss-sidebar-hover, #f8fafc) !important;
        color: var(--dss-sidebar-text, #0f172a) !important;
        transform: translateX(2px);
    }

    .dss-nav-link:hover i {
        color: var(--dss-sidebar-active-text, var(--dss-primary, #047857)) !important;
    }

    /* Modern Clean Sidebar Active Nav Item (Clean Soft Tint, No Bulky Pills) */
    .dss-nav-link.active {
        background-color: var(--dss-sidebar-active, var(--dss-primary-soft, #ecfdf5)) !important;
        color: var(--dss-sidebar-active-text, var(--dss-primary, #047857)) !important;
        font-weight: 600;
        border: 1px solid transparent !important;
        border-radius: 8px;
        box-shadow: none !important;
    }

    .dss-nav-link.active i {
        color: var(--dss-sidebar-active-text, var(--dss-primary, #047857)) !important;
    }

    /* Subnav Links in Sidebar Dropdowns (Clean Text Accent, No Nested Pills) */
    .dss-subnav-link {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.42rem 0.65rem;
        border-radius: 6px;
        color: var(--dss-sidebar-muted, #475569) !important;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.81rem;
        transition: all 0.15s ease;
        margin-bottom: 0.15rem;
        line-height: 1.35;
        white-space: nowrap !important;
    }

    .dss-subnav-link span {
        white-space: nowrap !important;
    }

    .dss-subnav-link:hover {
        background-color: var(--dss-sidebar-hover, #f8fafc) !important;
        color: var(--dss-sidebar-active-text, var(--dss-primary, #047857)) !important;
        transform: translateX(2px);
    }

    .dss-subnav-link.active {
        color: var(--dss-sidebar-active-text, var(--dss-primary, #047857)) !important;
        font-weight: 700;
        background-color: rgba(4, 120, 87, 0.08) !important;
    }

    .dss-subnav-link i {
        font-size: 15px;
        width: 18px;
        text-align: center;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .dss-subnav-link:hover i,
    .dss-subnav-link.active i {
        color: var(--dss-primary, #7d2ae8) !important;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .dss-sidebar-dropdown a .pm-nav-arrow,
    .dss-sidebar-dropdown a .dss-dropdown-caret {
        transition: transform 0.2s ease;
    }

    .dss-sidebar-dropdown a[aria-expanded="true"] .pm-nav-arrow,
    .dss-sidebar-dropdown a[aria-expanded="true"] .dss-dropdown-caret {
        transform: rotate(180deg);
    }

    /* Modern Card Hierarchy & Spacing */
    .dss-card {
        background: var(--dss-card-bg) !important;
        border-radius: var(--dss-card-radius, 12px) !important;
        border: 1px solid var(--dss-card-border) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .dss-card:hover {
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.06);
        border-color: color-mix(in srgb, var(--dss-card-border) 80%, black) !important;
    }

    .dss-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--dss-card-border) !important;
        background: var(--dss-card-bg) !important;
        border-top-left-radius: var(--dss-card-radius, 12px) !important;
        border-top-right-radius: var(--dss-card-radius, 12px) !important;
    }

    .dss-card-body {
        padding: 1.5rem;
    }

    /* Tables & Spacing */
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f8fafc !important;
        color: #475569;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 0.875rem;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Primary & Accent Buttons */
    .btn {
        font-weight: 600;
        border-radius: var(--dss-btn-radius, 8px);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.15s ease;
        line-height: 1.4;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
        border-radius: var(--dss-btn-radius, 6px);
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: calc(var(--dss-btn-radius, 8px) + 2px);
    }

    .btn-dss-primary {
        background-color: var(--dss-btn-primary-bg) !important;
        border-color: var(--dss-btn-primary-bg) !important;
        color: var(--dss-btn-primary-text) !important;
        border-radius: var(--dss-btn-radius, 8px) !important;
    }

    .btn-dss-primary:hover {
        background-color: color-mix(in srgb, var(--dss-btn-primary-bg) 85%, black) !important;
        border-color: color-mix(in srgb, var(--dss-btn-primary-bg) 85%, black) !important;
        color: var(--dss-btn-primary-text) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px color-mix(in srgb, var(--dss-btn-primary-bg) 35%, transparent) !important;
    }

    .btn-dss-secondary {
        background-color: var(--dss-btn-secondary-bg) !important;
        border-color: var(--dss-btn-secondary-bg) !important;
        color: var(--dss-btn-secondary-text) !important;
        border-radius: var(--dss-btn-radius, 8px) !important;
    }

    .btn-dss-secondary:hover {
        background-color: color-mix(in srgb, var(--dss-btn-secondary-bg) 85%, black) !important;
        border-color: color-mix(in srgb, var(--dss-btn-secondary-bg) 85%, black) !important;
        color: var(--dss-btn-secondary-text) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px color-mix(in srgb, var(--dss-btn-secondary-bg) 35%, transparent) !important;
    }

    /* Badges & Pills */
    .badge {
        font-weight: 600;
        padding: 0.35rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        line-height: 1.25;
        letter-spacing: 0.01em;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-dss-primary {
        background-color: var(--dss-primary-soft);
        color: var(--dss-primary);
        border: 1px solid var(--dss-primary-border);
    }

    .badge-dss-secondary {
        background-color: var(--dss-secondary-soft);
        color: var(--dss-secondary);
        border: 1px solid var(--dss-secondary-border);
    }

    .bg-soft-success {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .bg-soft-warning {
        background-color: #fffbeb !important;
        color: #d97706 !important;
        border: 1px solid #fde68a !important;
    }

    .bg-soft-danger {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
    }

    .bg-soft-info {
        background-color: #f0f9ff !important;
        color: #0284c7 !important;
        border: 1px solid #bae6fd !important;
    }

    .text-dss-primary {
        color: var(--dss-primary) !important;
    }

    .text-dss-secondary {
        color: var(--dss-secondary) !important;
    }

    .bg-dss-primary-soft {
        background-color: var(--dss-primary-soft) !important;
    }

    .bg-dss-secondary-soft {
        background-color: var(--dss-secondary-soft) !important;
    }

    /* Form Controls & Inputs */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        color: #0f172a;
        transition: all 0.15s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--dss-primary);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--dss-primary) 15%, transparent);
    }

    .form-control-sm, .form-select-sm {
        padding: 0.35rem 0.65rem;
        font-size: 0.8125rem;
        border-radius: 6px;
    }

    .input-group .input-group-text {
        border-color: #cbd5e1;
        background-color: #ffffff;
        color: #64748b;
    }

    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: var(--dss-primary);
    }

    /* Toggle Switches - Pill Track & Smooth Thumb */
    .form-switch {
        padding-left: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        vertical-align: middle !important;
        min-height: 24px !important;
        margin: 0 !important;
    }

    .form-switch .form-check-input,
    .form-check-input[role="switch"],
    input[type="checkbox"].perm-switch,
    input[type="checkbox"].user-switch,
    .form-check-input.dss-switch {
        width: 38px !important;
        min-width: 38px !important;
        max-width: 38px !important;
        height: 22px !important;
        min-height: 22px !important;
        max-height: 22px !important;
        margin: 0 !important;
        float: none !important;
        border-radius: 20px !important;
        background-color: #cbd5e1 !important;
        border: 1.5px solid #94a3b8 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23ffffff'/%3e%3c/svg%3e") !important;
        background-position: 2px center !important;
        background-repeat: no-repeat !important;
        background-size: 16px 16px !important;
        cursor: pointer !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        transition: background-position 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        outline: none !important;
    }

    .form-switch .form-check-input:hover,
    .form-check-input[role="switch"]:hover,
    input[type="checkbox"].perm-switch:hover,
    input[type="checkbox"].user-switch:hover,
    .form-check-input.dss-switch:hover {
        border-color: var(--dss-primary) !important;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--dss-primary) 15%, transparent), inset 0 1px 2px rgba(0, 0, 0, 0.08) !important;
    }

    .form-switch .form-check-input:focus,
    .form-check-input[role="switch"]:focus,
    input[type="checkbox"].perm-switch:focus,
    input[type="checkbox"].user-switch:focus,
    .form-check-input.dss-switch:focus {
        border-color: var(--dss-primary) !important;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--dss-primary) 22%, transparent) !important;
    }

    .form-switch .form-check-input:checked,
    .form-check-input[role="switch"]:checked,
    input[type="checkbox"].perm-switch:checked,
    input[type="checkbox"].user-switch:checked,
    .form-check-input.dss-switch:checked {
        background-color: var(--dss-primary) !important;
        border-color: var(--dss-primary) !important;
        background-position: 18px center !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23ffffff'/%3e%3c/svg%3e") !important;
        box-shadow: 0 1px 2px rgba(0, 103, 79, 0.2) !important;
    }

    .form-switch .form-check-label {
        margin-left: 8px !important;
        cursor: pointer !important;
        user-select: none !important;
    }

    .form-switch .form-check-input:disabled,
    .form-check-input[role="switch"]:disabled,
    input[type="checkbox"].perm-switch:disabled,
    input[type="checkbox"].user-switch:disabled {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
    }

    /* Survey Areas Tab Navigation System */
    .dss-area-tab-btn {
        width: 100%;
        text-align: left;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #1e293b;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .dss-area-tab-btn:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }

    .dss-area-tab-btn.active {
        background-color: var(--dss-primary-soft) !important;
        border-color: var(--dss-primary) !important;
        color: var(--dss-primary) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .dss-area-tab-btn.active .badge {
        background-color: var(--dss-primary) !important;
        color: #ffffff !important;
        border-color: var(--dss-primary) !important;
    }

    .btn-white {
        background-color: #ffffff !important;
        color: #0f172a !important;
        border-color: #e2e8f0 !important;
    }

    .btn-white:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: var(--dss-primary) !important;
    }

    .shadow-2xs {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
    }

    .shadow-xs {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.08);
    }

    .extra-small {
        font-size: 0.75rem;
    }

    /* ════════════════════════════════════════════════════════════════════════════
       EXECUTIVE SWEETALERT2 STYLING (MATCHING UISFIX EMERALD & INSTITUTIONAL THEME)
       ════════════════════════════════════════════════════════════════════════════ */

    /* ════════════════════════════════════════════════════════════════════════════
       EXECUTIVE SWEETALERT2 STYLING (MODALS & TOASTS PERFECTLY SCOPED)
       ════════════════════════════════════════════════════════════════════════════ */

    /* Frosted Blur Backdrop for Standard Modals Only */
    .swal2-container.swal2-backdrop-show:not(.swal2-top-end):not(.swal2-top-start):not(.swal2-bottom-end):not(.swal2-bottom-start):not(.swal2-top):not(.swal2-bottom) {
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
        background: rgba(15, 23, 42, 0.5) !important;
    }

    /* Toast Container Floating Position & Padding */
    .swal2-container.swal2-top-end {
        top: 20px !important;
        right: 20px !important;
        bottom: auto !important;
        left: auto !important;
        padding: 0 !important;
        z-index: 100050 !important;
    }

    .swal2-container.swal2-top-start {
        top: 20px !important;
        left: 20px !important;
        bottom: auto !important;
        right: auto !important;
        padding: 0 !important;
        z-index: 100050 !important;
    }

    .swal2-container.swal2-top-end,
    .swal2-container.swal2-top-start,
    .swal2-container.swal2-bottom-end,
    .swal2-container.swal2-bottom-start,
    .swal2-container:has(.swal2-toast) {
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        background: transparent !important;
        pointer-events: none !important;
    }

    /* Executive Modal Popup Shell (MODALS ONLY — will not affect Toasts) */
    .swal2-popup:not(.swal2-toast) {
        font-family: var(--dss-font, 'Inter', -apple-system, BlinkMacSystemFont, sans-serif) !important;
        border-radius: 22px !important;
        padding: 2.25rem 2rem 2rem !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        border-top: 4px solid var(--dss-primary, #059669) !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
        background: #ffffff !important;
        width: 440px !important;
        max-width: calc(100vw - 32px) !important;
    }

    /* Modal Type-dependent Accent Top Border */
    .swal2-popup:not(.swal2-toast).swal2-icon-warning { border-top-color: #f59e0b !important; }
    .swal2-popup:not(.swal2-toast).swal2-icon-error   { border-top-color: #ef4444 !important; }
    .swal2-popup:not(.swal2-toast).swal2-icon-info    { border-top-color: #0284c7 !important; }
    .swal2-popup:not(.swal2-toast).swal2-icon-success { border-top-color: var(--dss-primary, #059669) !important; }
    .swal2-popup:not(.swal2-toast).swal2-icon-question { border-top-color: #6366f1 !important; }

    /* Modal Icons Only */
    .swal2-popup:not(.swal2-toast) .swal2-icon {
        margin: 0.5rem auto 1.25rem !important;
        border-width: 3px !important;
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-success {
        border-color: var(--dss-primary, #059669) !important;
        color: var(--dss-primary, #059669) !important;
        background: color-mix(in srgb, var(--dss-primary, #059669) 8%, #ffffff) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-success [class^='swal2-success-line'] {
        background-color: var(--dss-primary, #059669) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-success .swal2-success-ring {
        border-color: color-mix(in srgb, var(--dss-primary, #059669) 25%, transparent) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-warning {
        border-color: #f59e0b !important;
        color: #f59e0b !important;
        background: #fffbe6 !important;
        box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.2) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-error {
        border-color: #ef4444 !important;
        color: #ef4444 !important;
        background: #fef2f2 !important;
        box-shadow: 0 8px 20px -4px rgba(239, 68, 68, 0.2) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
        background-color: #ef4444 !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-info {
        border-color: #0284c7 !important;
        color: #0284c7 !important;
        background: #eff6ff !important;
        box-shadow: 0 8px 20px -4px rgba(2, 132, 199, 0.2) !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-icon.swal2-question {
        border-color: #6366f1 !important;
        color: #6366f1 !important;
        background: #eef2ff !important;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.2) !important;
    }

    /* Modal Typography Only */
    .swal2-popup:not(.swal2-toast) .swal2-title {
        color: #0f172a !important;
        font-size: 1.28rem !important;
        font-weight: 800 !important;
        padding: 0 0 0.5rem !important;
        letter-spacing: -0.3px !important;
        line-height: 1.3 !important;
    }

    .swal2-popup:not(.swal2-toast) .swal2-html-container {
        color: #475569 !important;
        font-size: 0.92rem !important;
        font-weight: 500 !important;
        line-height: 1.6 !important;
        margin: 0 0 1.5rem !important;
        padding: 0 8px !important;
    }

    /* Modal Actions Container */
    .swal2-popup:not(.swal2-toast) .swal2-actions {
        margin: 1.25rem 0 0 !important;
        gap: 0.85rem !important;
        width: 100% !important;
        justify-content: center !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Modal Pill Action Buttons */
    .swal2-actions button {
        border-radius: 50px !important;
        padding: 11px 24px !important;
        font-weight: 700 !important;
        font-size: 0.92rem !important;
        transition: all 0.2s ease !important;
        outline: none !important;
    }

    /* Primary Confirm Button */
    .swal2-confirm.btn,
    .swal2-styled.swal2-confirm {
        background: var(--dss-primary, #059669) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 14px color-mix(in srgb, var(--dss-primary, #059669) 35%, transparent) !important;
    }

    .swal2-confirm.btn:hover,
    .swal2-styled.swal2-confirm:hover {
        filter: brightness(0.92) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px color-mix(in srgb, var(--dss-primary, #059669) 45%, transparent) !important;
    }

    /* Destructive / Danger Confirm Button */
    .swal2-confirm.btn-danger,
    .swal2-styled.swal2-confirm.btn-danger,
    .swal2-confirm[style*="rgb(220, 38, 38)"],
    .swal2-confirm[style*="rgb(221, 51, 51)"],
    .swal2-confirm[style*="#dc2626"],
    .swal2-confirm[style*="#ef4444"],
    .swal2-confirm[style*="#d33"],
    .swal2-confirm[style*="rgb(239, 68, 68)"] {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35) !important;
    }

    .swal2-confirm.btn-danger:hover,
    .swal2-styled.swal2-confirm.btn-danger:hover,
    .swal2-confirm[style*="rgb(220, 38, 38)"]:hover,
    .swal2-confirm[style*="rgb(221, 51, 51)"]:hover,
    .swal2-confirm[style*="#dc2626"]:hover,
    .swal2-confirm[style*="#ef4444"]:hover,
    .swal2-confirm[style*="#d33"]:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        box-shadow: 0 8px 22px rgba(220, 38, 38, 0.45) !important;
        transform: translateY(-1px) !important;
    }

    /* Cancel Button (Soft Slate Pill) */
    .swal2-cancel.btn,
    .swal2-styled.swal2-cancel {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
    }

    .swal2-cancel.btn:hover,
    .swal2-styled.swal2-cancel:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }

    /* Deny Button */
    .swal2-deny.btn,
    .swal2-styled.swal2-deny {
        background: #64748b !important;
        color: #ffffff !important;
        border: none !important;
    }

    .swal2-deny.btn:hover,
    .swal2-styled.swal2-deny:hover {
        background: #475569 !important;
    }

    /* ════════════════════════════════════════════════════════════════════════════
       SWEETALERT2 LUXURY FLOATING TOAST STYLING (PIXEL-PERFECT & UNCLIPPED)
       ════════════════════════════════════════════════════════════════════════════ */
    .swal2-popup.swal2-toast {
        pointer-events: auto !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        width: 380px !important;
        max-width: calc(100vw - 40px) !important;
        border-radius: 14px !important;
        padding: 12px 16px !important;
        border: 1px solid #cbd5e1 !important;
        border-left: 4px solid var(--dss-primary, #059669) !important;
        box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.16), 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        background: #ffffff !important;
        gap: 12px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }

    .swal2-popup.swal2-toast.swal2-icon-warning { border-left-color: #f59e0b !important; }
    .swal2-popup.swal2-toast.swal2-icon-error   { border-left-color: #ef4444 !important; }
    .swal2-popup.swal2-toast.swal2-icon-info    { border-left-color: #0284c7 !important; }
    .swal2-popup.swal2-toast.swal2-icon-success { border-left-color: var(--dss-primary, #059669) !important; }
    .swal2-popup.swal2-toast.swal2-icon-question { border-left-color: #6366f1 !important; }

    .swal2-popup.swal2-toast .swal2-icon {
        width: 26px !important;
        height: 26px !important;
        min-width: 26px !important;
        max-width: 26px !important;
        margin: 0 !important;
        border-width: 2px !important;
        flex-shrink: 0 !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content {
        font-size: 15px !important;
        font-weight: 700 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .swal2-popup.swal2-toast .swal2-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        text-align: left !important;
    }

    .swal2-popup.swal2-toast .swal2-html-container {
        font-size: 11.5px !important;
        margin: 2px 0 0 !important;
        padding: 0 !important;
        color: #475569 !important;
        line-height: 1.35 !important;
        text-align: left !important;
    }

    /* ════════════════════════════════════════════════════════════════════════════
       DSS EXECUTIVE STACKING TOAST NOTIFICATIONS (TOP-RIGHT STACK)
       ════════════════════════════════════════════════════════════════════════════ */
    .dss-toast-container {
        position: fixed !important;
        top: 76px !important; /* Floating below sticky 64px navbar */
        right: 24px !important;
        z-index: 100050 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
        pointer-events: none !important;
        max-width: 380px !important;
        width: calc(100vw - 48px) !important;
    }

    .dss-stacked-toast {
        pointer-events: auto !important;
        background: #ffffff !important;
        border-radius: 12px !important;
        border: 1px solid #cbd5e1 !important;
        border-left: 4.5px solid var(--toast-accent, var(--dss-primary, #059669)) !important;
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.14), 0 4px 10px -2px rgba(15, 23, 42, 0.06) !important;
        padding: 12px 14px 14px !important;
        display: flex !important;
        align-items: flex-start !important;
        gap: 10px !important;
        transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1) !important;
        animation: dssToastSlideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        position: relative !important;
        overflow: hidden !important;
    }

    @keyframes dssToastSlideDown {
        from {
            opacity: 0;
            transform: translateY(-16px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dss-stacked-toast.dss-toast-hiding {
        opacity: 0 !important;
        transform: translateX(40px) scale(0.9) !important;
        margin-bottom: -45px !important;
    }

    .dss-toast-icon-badge {
        width: 28px;
        height: 28px;
        min-width: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        background: color-mix(in srgb, var(--toast-accent, var(--dss-primary, #059669)) 12%, #ffffff);
        color: var(--toast-accent, var(--dss-primary, #059669));
    }

    .dss-toast-title {
        font-weight: 700;
        font-size: 13px;
        color: #0f172a;
        line-height: 1.3;
    }

    .dss-toast-desc {
        font-size: 11.5px;
        color: #475569;
        margin-top: 2px;
        line-height: 1.35;
    }

    .dss-toast-close {
        background: none;
        border: none;
        padding: 0;
        margin-left: auto;
        color: #94a3b8;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.15s ease;
    }

    .dss-toast-close:hover {
        opacity: 1;
        color: #0f172a;
    }

    .dss-toast-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2.5px;
        background: var(--toast-accent, var(--dss-primary, #059669));
        width: 100%;
        opacity: 0.85;
    }

    /* ════════════════════════════════════════════════════════════════════════════
       CANVA-INSPIRED STATIC DESIGN SYSTEM & BENTO HELPERS (STILL GRADIENTS ONLY)
       ════════════════════════════════════════════════════════════════════════════ */
    .canva-gradient-purple { background: linear-gradient(135deg, #7d2ae8 0%, #00c4cc 100%) !important; color: #fff !important; }
    .canva-gradient-indigo { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important; color: #fff !important; }
    .canva-gradient-dark   { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%) !important; color: #fff !important; }
    .canva-gradient-subtle { background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important; }
    .canva-gradient-mesh-still {
        background: radial-gradient(circle at 10% 20%, rgba(125, 42, 232, 0.06) 0%, rgba(248, 250, 252, 1) 60%, rgba(0, 196, 204, 0.05) 100%) !important;
    }

    .canva-card {
        background: var(--dss-card-bg, #ffffff);
        border-radius: var(--dss-card-radius, 14px);
        border: 1px solid var(--dss-card-border, #e2e8f0);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .canva-card:hover {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.07), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
        border-color: color-mix(in srgb, var(--dss-card-border, #e2e8f0) 70%, black);
    }

    /* Unified Clean White Canva Page Header with Dynamic Theme Integration */
    .canva-hero-header,
    .canva-dashboard-hero,
    .canva-hero-box {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-top: 3.5px solid var(--dss-primary) !important;
        border-radius: 18px !important;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02) !important;
    }
    .canva-hero-header h1, .canva-hero-header h2, .canva-hero-header h3, .canva-hero-header h4,
    .canva-dashboard-hero h1, .canva-dashboard-hero h2, .canva-dashboard-hero h3, .canva-dashboard-hero h4,
    .canva-hero-box h1, .canva-hero-box h2, .canva-hero-box h3, .canva-hero-box h4 {
        color: #0f172a !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
    }
    .canva-hero-header p,
    .canva-dashboard-hero p,
    .canva-hero-box p {
        color: #64748b !important;
    }

    /* Dynamic Theme Eyebrow Pill for Canva Headers */
    .canva-theme-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        background: var(--dss-primary-soft) !important;
        color: var(--dss-primary) !important;
        border: 1px solid var(--dss-primary-border) !important;
    }

    .border-accent-purple  { border-top: 3px solid #7d2ae8 !important; }
    .border-accent-emerald { border-top: 3px solid #10b981 !important; }
    .border-accent-cyan    { border-top: 3px solid #06b6d4 !important; }
    .border-accent-amber   { border-top: 3px solid #f59e0b !important; }
    .border-accent-rose    { border-top: 3px solid #f43f5e !important; }

    .canva-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    /* ════════════════════════════════════════════════════════════════════════════
       STABLE DECIMAL SPACING UTILITY FALLBACKS (FIXES BOOTSTRAP 5 LIMITATIONS)
       ════════════════════════════════════════════════════════════════════════════ */
    .gap-0\.5 { gap: 0.25rem !important; }
    .gap-1\.5 { gap: 0.375rem !important; }
    .gap-2\.5 { gap: 0.625rem !important; }
    .gap-3\.5 { gap: 0.875rem !important; }
    .py-0\.5 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
    .py-1\.5 { padding-top: 0.375rem !important; padding-bottom: 0.375rem !important; }
    .py-2\.5 { padding-top: 0.625rem !important; padding-bottom: 0.625rem !important; }
    .py-3\.5 { padding-top: 0.875rem !important; padding-bottom: 0.875rem !important; }
    .px-0\.5 { padding-left: 0.25rem !important; padding-right: 0.25rem !important; }
    .px-1\.5 { padding-left: 0.375rem !important; padding-right: 0.375rem !important; }
    .px-2\.5 { padding-left: 0.625rem !important; padding-right: 0.625rem !important; }
    .px-3\.5 { padding-left: 0.875rem !important; padding-right: 0.875rem !important; }
    .p-0\.5 { padding: 0.25rem !important; }
    .p-1\.5 { padding: 0.375rem !important; }
    .p-2\.5 { padding: 0.625rem !important; }
    .me-0\.5 { margin-right: 0.25rem !important; }
    .me-1\.5 { margin-right: 0.375rem !important; }
    .me-2\.5 { margin-right: 0.625rem !important; }
    .ms-0\.5 { margin-left: 0.25rem !important; }
    .ms-1\.5 { margin-left: 0.375rem !important; }
    .ms-2\.5 { margin-left: 0.625rem !important; }
    .mb-0\.5 { margin-bottom: 0.25rem !important; }
    .mb-1\.5 { margin-bottom: 0.375rem !important; }
    .mb-2\.5 { margin-bottom: 0.625rem !important; }
    .mb-3\.5 { margin-bottom: 0.875rem !important; }
    .mt-0\.5 { margin-top: 0.25rem !important; }
    .mt-1\.5 { margin-top: 0.375rem !important; }
    .mt-2\.5 { margin-top: 0.625rem !important; }

    /* ════════════════════════════════════════════════════════════════════════════
       CENTRALIZED DSS DATATABLES DESIGN SYSTEM (ENTERPRISE STANDARD)
       ════════════════════════════════════════════════════════════════════════════ */
    /* Table Typography & Core Layout */
    .dataTables_wrapper .table,
    table.dataTable,
    .dss-datatable {
        font-size: 12.5px !important;
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    /* Column Headers */
    .dataTables_wrapper .table thead th,
    table.dataTable thead th,
    .dss-datatable thead th {
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        color: #64748b !important;
        background-color: #f8fafc !important;
        border-top: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 10px 12px !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Body Cells */
    .dataTables_wrapper .table tbody td,
    table.dataTable tbody td,
    .dss-datatable tbody td {
        font-size: 12.5px !important;
        vertical-align: middle !important;
        padding: 10px 12px !important;
        color: #1e293b !important;
        border-bottom: 1px solid #f1f5f9 !important;
        background-color: #ffffff;
    }

    /* Hover State */
    .dataTables_wrapper .table tbody tr:hover td,
    table.dataTable tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    /* Responsive Table Container & Dropdown Popout Support */
    .table-responsive {
        min-height: 280px;
    }
    .table-responsive:has(.dropdown-menu.show),
    .table-responsive.dropdown-open {
        overflow: visible !important;
    }

    /* Dropdown Menus in Tables */
    table.dataTable .dropdown-menu,
    .dataTables_wrapper .dropdown-menu {
        z-index: 1065 !important;
        font-size: 12px !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08) !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* DataTables Info, Pagination & Processing Reset */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 2px !important;
        border: none !important;
        background: transparent !important;
    }
    .dataTables_wrapper .dataTables_paginate .pagination {
        margin-bottom: 0 !important;
        gap: 4px !important;
    }
    .dataTables_wrapper .dataTables_paginate .page-link {
        font-size: 12px !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
        background-color: var(--dss-primary, #0f172a) !important;
        border-color: var(--dss-primary, #0f172a) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 12px !important;
        color: #64748b !important;
        padding: 0 !important;
    }
    .dataTables_wrapper .dataTables_processing {
        z-index: 1050 !important;
        background: rgba(255, 255, 255, 0.9) !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: var(--dss-primary, #0f172a) !important;
    }

    /* Global Laravel & Bootstrap Pagination Theming (Emerald DSS) */
    .pagination {
        gap: 4px !important;
        margin-bottom: 0 !important;
    }
    .pagination .page-item {
        list-style: none;
    }
    .pagination .page-item .page-link {
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
        line-height: 1;
    }
    .pagination .page-item .page-link:hover {
        background: #f0fdf4;
        color: #00674f;
        border-color: #a7f3d0;
    }
    .pagination .page-item.active .page-link {
        background: #00674f !important;
        border-color: #00674f !important;
        color: #ffffff !important;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 103, 79, 0.25);
    }
    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    /* Custom CSS Overrides from DB */
    {!! $currentTheme->custom_css ?? '' !!}
</style>
<!-- Material Able Global Design System CSS -->
<link href="{{ asset('css/material-able.css') }}?v={{ @filemtime(public_path('css/material-able.css')) ?: '1.4' }}" rel="stylesheet">
