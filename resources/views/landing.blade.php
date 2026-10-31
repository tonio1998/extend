<!DOCTYPE html>
@php
    $institution = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $theme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $activeUiTemplate = $theme->ui_template ?? 'flup_minimal';
    $uiTemplatesMeta = \App\Services\DSS\ThemePresets::uiTemplates();
    $activeUiMeta = $uiTemplatesMeta[$activeUiTemplate] ?? $uiTemplatesMeta['flup_minimal'];

    $faviconUrl = $institution->logo_variants['favicon']['url'] ?? ($institution->logo_path ? asset($institution->logo_path) : null);
    $navbarBanner = $institution->logo_variants['navbar']['url'] ?? null;
    $sealLogo = $institution->logo_variants['sidebar']['url'] ?? ($institution->logo_variants['app_icon']['url'] ?? ($institution->logo_path ? asset($institution->logo_path) : null));
    $schoolAcronym = $institution->school_acronym ?: 'RIE-EXTEND';
    $schoolName = $institution->school_name ?: 'State University & College';
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $schoolAcronym }} — Research, Innovation &amp; Extension Management System</title>
    <meta name="description" content="Sovereign University Extension Management Platform for planning, proposing, and tracking community extension projects aligned with the 17 UN Sustainable Development Goals.">

    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    @endif

    <!-- Local Bootstrap 5 & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <!-- Dynamic SaaS Theme Engine (--dss-primary, --dss-secondary, --dss-accent, UI template CSS) -->
    @include('layouts.dss.theme-styles')

    <style>
        :root {
            --rie-primary: var(--dss-primary, #047857);
            --rie-primary-dark: color-mix(in srgb, var(--dss-primary, #047857) 82%, #0f172a);
            --rie-primary-soft: color-mix(in srgb, var(--dss-primary, #047857) 10%, #ffffff);
            --rie-primary-border: color-mix(in srgb, var(--dss-primary, #047857) 24%, #ffffff);
            --rie-secondary: var(--dss-secondary, #0284c7);
            --rie-accent: var(--dss-accent, #f59e0b);
            --rie-card-radius: var(--dss-card-radius, 14px);
            --rie-btn-radius: var(--dss-btn-radius, 10px);
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
        }

        /* ====================================================================
           DYNAMIC NAVBAR ADAPTATION PER UI TEMPLATE
           ==================================================================== */
        .landing-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 0;
            transition: all 0.2s ease;
        }
        .landing-nav-link {
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569 !important;
            padding: 0.45rem 0.9rem !important;
            border-radius: 8px;
            transition: all 0.18s ease;
        }
        .landing-nav-link:hover {
            color: var(--rie-primary) !important;
            background: var(--rie-primary-soft);
        }

        /* Template 2: Print Manzil — Detached Floating Bento Navbar */
        body[data-ui-template="print_manzil"] .landing-navbar-wrap {
            padding: 12px 16px 0;
            background: transparent;
        }
        body[data-ui-template="print_manzil"] .landing-navbar {
            max-width: 1320px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 8px 24px -6px rgba(15, 23, 42, 0.06);
            padding: 0.75rem 1.25rem;
        }

        /* Template 3: Material Able — Full-Width Colored Primary Topbar */
        body[data-ui-template="material_able"] .landing-navbar {
            background: var(--rie-primary) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
        }
        body[data-ui-template="material_able"] .landing-navbar .brand-title,
        body[data-ui-template="material_able"] .landing-navbar .brand-sub,
        body[data-ui-template="material_able"] .landing-nav-link {
            color: #ffffff !important;
        }
        body[data-ui-template="material_able"] .landing-nav-link:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff !important;
        }

        /* ====================================================================
           DYNAMIC HERO SECTION PER UI TEMPLATE
           ==================================================================== */
        /* Default / Template 1: Flup Minimal — Clean Crisp SaaS Surface */
        .landing-hero {
            position: relative;
            background: #ffffff;
            padding: 4.5rem 0 4rem;
            border-bottom: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .landing-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, color-mix(in srgb, var(--rie-primary) 7%, transparent) 0%, transparent 50%),
                radial-gradient(circle at 85% 80%, color-mix(in srgb, var(--rie-secondary) 6%, transparent) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Template 2: Print Manzil — Soft Bento Hero Box */
        body[data-ui-template="print_manzil"] .landing-hero {
            background: #f8fafc;
            border-bottom: none;
            padding: 2rem 0 2.5rem;
        }
        body[data-ui-template="print_manzil"] .landing-hero-inner {
            background: linear-gradient(135deg, var(--rie-primary-soft) 0%, #ffffff 65%, color-mix(in srgb, var(--rie-secondary) 8%, #ffffff) 100%);
            border: 1px solid var(--rie-primary-border);
            border-radius: 26px;
            padding: 3.5rem 2rem;
            box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.05);
        }

        /* Template 3: Material Able — Geometric Enterprise Banner */
        body[data-ui-template="material_able"] .landing-hero {
            background: linear-gradient(135deg, var(--rie-primary) 0%, var(--rie-primary-dark) 100%);
            color: #ffffff;
            border-bottom: 4px solid var(--rie-accent);
            padding: 4.75rem 0 4.25rem;
        }
        body[data-ui-template="material_able"] .landing-hero h1,
        body[data-ui-template="material_able"] .landing-hero .hero-lead {
            color: #ffffff !important;
        }
        body[data-ui-template="material_able"] .landing-hero .rie-gradient-text {
            background: none !important;
            -webkit-text-fill-color: var(--rie-accent) !important;
            color: var(--rie-accent) !important;
        }
        body[data-ui-template="material_able"] .landing-hero .rie-eyebrow {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.28);
            color: #ffffff;
        }
        body[data-ui-template="material_able"] .landing-hero .rie-chip {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.24);
            color: #ffffff;
        }
        body[data-ui-template="material_able"] .landing-hero .rie-chip:hover {
            background: #ffffff;
            color: var(--rie-primary);
        }

        /* Theme Gradient Text & Buttons */
        .rie-gradient-text {
            background: linear-gradient(135deg, var(--rie-primary) 0%, var(--rie-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .btn-rie-primary {
            background: var(--rie-primary);
            color: #ffffff !important;
            border: 1px solid var(--rie-primary);
            border-radius: var(--rie-btn-radius, 10px);
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--rie-primary) 26%, transparent);
        }
        .btn-rie-primary:hover {
            background: var(--rie-primary-dark);
            border-color: var(--rie-primary-dark);
            transform: translateY(-1px);
            color: #ffffff !important;
        }

        .btn-rie-outline {
            background: #ffffff;
            color: var(--rie-primary) !important;
            border: 1.5px solid var(--rie-primary-border);
            border-radius: var(--rie-btn-radius, 10px);
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-rie-outline:hover {
            background: var(--rie-primary-soft);
            border-color: var(--rie-primary);
            transform: translateY(-1px);
        }

        .rie-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 1rem;
            border-radius: 999px;
            background: var(--rie-primary-soft);
            border: 1px solid var(--rie-primary-border);
            color: var(--rie-primary);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .rie-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.42rem 1rem;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .rie-chip:hover {
            border-color: var(--rie-primary);
            color: var(--rie-primary);
            background: var(--rie-primary-soft);
            transform: translateY(-2px);
        }

        /* ====================================================================
           DYNAMIC CARDS PER UI TEMPLATE
           ==================================================================== */
        .rie-card {
            border: 1px solid var(--dss-card-border, #e2e8f0);
            border-radius: var(--rie-card-radius, 14px);
            background: var(--dss-card-bg, #ffffff);
            overflow: hidden;
            transition: all 0.24s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .rie-card:hover {
            transform: translateY(-4px);
            border-color: var(--rie-primary-border);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            color: inherit;
        }
        body[data-ui-template="print_manzil"] .rie-card {
            border-radius: 22px;
            box-shadow: 0 6px 22px -4px rgba(15, 23, 42, 0.05);
        }
        body[data-ui-template="material_able"] .rie-card {
            border-radius: 10px;
            border-top: 3.5px solid var(--rie-primary);
        }

        .rie-card-banner {
            height: 108px;
            padding: 1rem 1.25rem;
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .landing-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--rie-card-radius, 14px);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        body[data-ui-template="print_manzil"] .landing-kpi-card {
            border-radius: 20px;
            padding-bottom: 2rem;
        }
        body[data-ui-template="material_able"] .landing-kpi-card {
            border-radius: 10px;
            border-left: 4px solid var(--rie-primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-top: 0.35rem;
        }

        .feature-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .step-node {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }
    </style>
</head>
<body data-ui-template="{{ $activeUiTemplate }}">

    <!-- Top Theme Accent Bar (Flup & Print Manzil) -->
    @if($activeUiTemplate !== 'material_able')
        <div style="height: 4px; background: linear-gradient(90deg, var(--rie-primary) 0%, var(--rie-secondary) 55%, var(--rie-accent) 100%);"></div>
    @endif

    <!-- Sticky Template-Aware Navbar -->
    <div class="landing-navbar-wrap sticky-top">
        <nav class="navbar navbar-expand-lg landing-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2.5 text-decoration-none fw-bold" href="{{ url('/') }}">
                    @if($navbarBanner)
                        <img src="{{ $navbarBanner }}" alt="{{ $schoolAcronym }}" style="height: 38px; width: auto; max-width: 220px; object-fit: contain;">
                    @elseif($sealLogo)
                        <div class="d-flex align-items-center gap-2.5">
                            <img src="{{ $sealLogo }}" alt="{{ $schoolAcronym }}" style="height: 38px; width: 38px; object-fit: contain; border-radius: 8px; background: #fff; padding: 2px;">
                            <div class="d-flex flex-column">
                                <span class="brand-title text-dark fw-extrabold" style="font-size: 1.05rem; letter-spacing: -0.02em; line-height: 1.1;">{{ $schoolAcronym }}</span>
                                <span class="brand-sub text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.05em;">RIE EXTENSION SYSTEM</span>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: var(--rie-primary);">
                                <i class="mdi mdi-handshake-outline fs-5"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="brand-title text-dark fw-extrabold" style="font-size: 1.05rem; letter-spacing: -0.02em; line-height: 1.1;">{{ $schoolAcronym }}</span>
                                <span class="brand-sub text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.05em;">RIE EXTENSION SYSTEM</span>
                            </div>
                        </div>
                    @endif
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#rieNav">
                    <i class="mdi mdi-menu fs-3 {{ $activeUiTemplate === 'material_able' ? 'text-white' : 'text-dark' }}"></i>
                </button>

                <div class="collapse navbar-collapse" id="rieNav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                        <li class="nav-item"><a class="nav-link landing-nav-link" href="#modules">Modules</a></li>
                        <li class="nav-item"><a class="nav-link landing-nav-link" href="#workflow">Workflow</a></li>
                        <li class="nav-item"><a class="nav-link landing-nav-link" href="#sdg">17 UN SDGs</a></li>
                        <li class="nav-item"><a class="nav-link landing-nav-link" href="{{ route('document.track') }}">Track Document</a></li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
                        @auth
                            <a href="{{ route('dss.dashboard') }}" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-light text-dark fw-bold' : 'btn-rie-primary' }} px-4 py-2 d-flex align-items-center gap-2" style="font-size: 13px; border-radius: var(--rie-btn-radius);">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                <span>Open Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-light text-dark fw-bold' : 'btn-rie-primary' }} px-4 py-2 d-flex align-items-center gap-2" style="font-size: 13px; border-radius: var(--rie-btn-radius);">
                                <i class="mdi mdi-login-variant"></i>
                                <span>Portal Sign In</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Template-Aware Hero Section -->
    <section class="landing-hero text-center">
        <div class="container position-relative" style="z-index: 1;">
            <div class="landing-hero-inner">
                <div class="rie-eyebrow">
                    <i class="mdi {{ $activeUiMeta['icon'] ?? 'mdi-handshake-outline' }}"></i>
                    <span>{{ $schoolName }} &bull; Research, Innovation &amp; Extension (RIE)</span>
                </div>

                <h1 class="fw-extrabold text-dark mb-3" style="font-size: clamp(2.1rem, 4.2vw, 3rem); line-height: 1.16; letter-spacing: -0.03em; max-width: 800px; margin: 0 auto 0.75rem;">
                    Plan, Propose &amp; Monitor <span class="rie-gradient-text">University Extension</span> Programs
                </h1>

                <p class="hero-lead text-secondary mx-auto mb-4" style="font-size: 1.02rem; line-height: 1.68; max-width: 650px; {{ $activeUiTemplate === 'material_able' ? 'opacity: 0.9;' : '' }}">
                    Centralized institutional platform for managing multi-year extension plans, community proposals, commencement &amp; duration calendars, 17 UN SDG alignments, and digital signatory routing.
                </p>

                <!-- Quick Feature Chips -->
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-4">
                    <a href="#modules" class="rie-chip">
                        <i class="mdi mdi-file-document-multiple-outline" style="color: {{ $activeUiTemplate === 'material_able' ? 'var(--rie-accent)' : 'var(--rie-primary)' }};"></i>
                        <span>Extension Plans</span>
                    </a>
                    <a href="#modules" class="rie-chip">
                        <i class="mdi mdi-lightbulb-on-outline" style="color: var(--rie-accent);"></i>
                        <span>Proposals &amp; Projects</span>
                    </a>
                    <a href="#sdg" class="rie-chip">
                        <i class="mdi mdi-globe-model" style="color: {{ $activeUiTemplate === 'material_able' ? '#93c5fd' : 'var(--rie-secondary)' }};"></i>
                        <span>17 UN SDG Monitoring</span>
                    </a>
                    <a href="#workflow" class="rie-chip">
                        <i class="mdi mdi-calendar-range" style="color: {{ $activeUiTemplate === 'material_able' ? '#6ee7b7' : 'var(--rie-primary)' }};"></i>
                        <span>Extension Calendar &amp; Ranges</span>
                    </a>
                    <a href="{{ route('document.track') }}" class="rie-chip">
                        <i class="mdi mdi-Draw-pen" style="color: {{ $activeUiTemplate === 'material_able' ? '#fda4af' : '#e11d48' }};"></i>
                        <span>Digital Signatures</span>
                    </a>
                </div>

                <!-- CTA Buttons -->
                <div class="d-flex align-items-center justify-content-center gap-2.5 flex-wrap">
                    @auth
                        <a href="{{ route('dss.dashboard') }}" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-light text-dark fw-bold' : 'btn-rie-primary' }} px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 14.5px; border-radius: var(--rie-btn-radius);">
                            <i class="mdi mdi-view-dashboard-outline fs-5"></i>
                            <span>Go to Executive Dashboard</span>
                        </a>
                        <a href="{{ route('dss.rie.calendar.index') }}" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-outline-light' : 'btn-rie-outline' }} px-4 py-2.5 fw-bold" style="font-size: 14px; border-radius: var(--rie-btn-radius);">
                            <i class="mdi mdi-calendar-range me-1"></i>Extension Calendar
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-light text-dark fw-bold' : 'btn-rie-primary' }} px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 14.5px; border-radius: var(--rie-btn-radius);">
                            <i class="mdi mdi-login-variant fs-5"></i>
                            <span>Access Extension Portal</span>
                        </a>
                        <a href="#modules" class="btn {{ $activeUiTemplate === 'material_able' ? 'btn-outline-light' : 'btn-rie-outline' }} px-4 py-2.5 fw-bold" style="font-size: 14px; border-radius: var(--rie-btn-radius);">
                            Explore Modules &rarr;
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Counter Strip (Adapts to Flup / Print Manzil Sparklines / Material Able) -->
    <section class="py-4 border-bottom" style="background: #f8fafc;">
        <div class="container">
            <div class="row g-3">
                @foreach([
                    ['val' => number_format($stats['extension_plans']).'+', 'label' => 'Extension Plans', 'icon' => 'mdi-calendar-multiselect-outline', 'color' => 'var(--rie-primary)'],
                    ['val' => number_format($stats['extension_proposals']).'+', 'label' => 'Project Proposals', 'icon' => 'mdi-file-document-edit-outline', 'color' => 'var(--rie-secondary)'],
                    ['val' => $stats['sdg_goals'], 'label' => 'UN SDG Goals Tracked', 'icon' => 'mdi-earth', 'color' => 'var(--rie-accent)'],
                    ['val' => $stats['community_satisfaction'], 'label' => 'Community Satisfaction', 'icon' => 'mdi-heart-pulse', 'color' => 'var(--rie-primary)'],
                ] as $kpi)
                    <div class="col-6 col-md-3">
                        <div class="landing-kpi-card d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-value" style="color: {{ $kpi['color'] }};">{{ $kpi['val'] }}</div>
                                <div class="stat-label">{{ $kpi['label'] }}</div>
                            </div>
                            <div class="feature-icon-box" style="background: var(--rie-primary-soft); color: var(--rie-primary);">
                                <i class="mdi {{ $kpi['icon'] }}"></i>
                            </div>
                            @if($activeUiTemplate === 'print_manzil')
                                <svg viewBox="0 0 200 28" preserveAspectRatio="none" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 22px; opacity: 0.18;">
                                    <path d="M0,20 Q50,4 100,16 T200,10 L200,28 L0,28 Z" fill="var(--rie-primary)"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Core Extension Modules -->
    <section class="py-5 bg-white border-bottom" id="modules">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                <div>
                    <span class="rie-eyebrow mb-2">Core Ecosystem</span>
                    <h2 class="fw-extrabold text-dark mb-0 fs-3">Extension Management Modules</h2>
                </div>
                <a href="{{ route('login') }}" class="btn btn-rie-outline btn-sm px-3.5 py-2">
                    Access All Modules &rarr;
                </a>
            </div>

            <div class="row g-3.5">
                <!-- Card 1: Extension Plans -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, var(--rie-primary) 0%, var(--rie-primary-dark) 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10.5px;">Multi-Year Roadmap</span>
                            <i class="mdi mdi-file-document-multiple-outline fs-3"></i>
                        </div>
                        <div class="p-3.5 d-flex flex-column flex-grow-1">
                            <div class="mb-1.5">
                                <span class="badge rounded-pill" style="background: var(--rie-primary-soft); color: var(--rie-primary); border: 1px solid var(--rie-primary-border); font-size: 10.5px;">Extension Plans</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1.5 fs-6">Annual &amp; Multi-Year Extension Plan</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1">
                                Create institutional extension roadmaps with banner programs, agendas, target beneficiaries, and milestone schedules.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2.5 border-top">
                                <span class="extra-small text-muted">{{ $stats['extension_plans'] }} Active Plans</span>
                                <span class="fw-bold extra-small" style="color: var(--rie-primary);">Open Module &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 2: Extension Proposals -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, var(--rie-secondary) 0%, color-mix(in srgb, var(--rie-secondary) 75%, #0f172a) 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10.5px;">Project Proposals</span>
                            <i class="mdi mdi-lightbulb-on-outline fs-3"></i>
                        </div>
                        <div class="p-3.5 d-flex flex-column flex-grow-1">
                            <div class="mb-1.5">
                                <span class="badge rounded-pill" style="background: color-mix(in srgb, var(--rie-secondary) 12%, #fff); color: var(--rie-secondary); font-size: 10.5px;">Proposals</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1.5 fs-6">Extension Project Proposal Workbench</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1">
                                Draft, submit, and monitor extension proposals with 6Ps deliverables, GAD beneficiaries, SDG tagging, and budget breakdowns.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2.5 border-top">
                                <span class="extra-small text-muted">{{ $stats['extension_proposals'] }} Proposals</span>
                                <span class="fw-bold extra-small" style="color: var(--rie-secondary);">Submit Proposal &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 3: Extension Calendar & Range Monitoring -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, var(--rie-accent) 0%, color-mix(in srgb, var(--rie-accent) 75%, #78350f) 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10.5px;">Timeline &amp; Duration</span>
                            <i class="mdi mdi-calendar-range fs-3"></i>
                        </div>
                        <div class="p-3.5 d-flex flex-column flex-grow-1">
                            <div class="mb-1.5">
                                <span class="badge rounded-pill" style="background: color-mix(in srgb, var(--rie-accent) 14%, #fff); color: color-mix(in srgb, var(--rie-accent) 80%, #000); font-size: 10.5px;">Calendar Monitor</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1.5 fs-6">Commencement &amp; Duration Range Calendar</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1">
                                Continuous multi-day range calendar monitoring commencement dates, project durations, MOA signings, materials arrivals, and evaluations.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2.5 border-top">
                                <span class="extra-small text-muted">Full Range View</span>
                                <span class="fw-bold extra-small" style="color: var(--rie-primary);">View Calendar &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Extension Proposal Workflow -->
    <section class="py-5 border-bottom" id="workflow" style="background: #f8fafc;">
        <div class="container">
            <div class="text-center mb-4">
                <span class="rie-eyebrow mb-2">End-to-End Governance</span>
                <h2 class="fw-extrabold text-dark mb-2 fs-3">Extension Project Lifecycle</h2>
                <p class="text-muted mx-auto" style="max-width: 560px;">From annual planning to digital signatory approval and impact monitoring.</p>
            </div>

            <div class="row g-3">
                @foreach([
                    ['step' => '01', 'icon' => 'mdi-file-document-outline', 'title' => 'Create Extension Plan', 'desc' => 'Set up institutional multi-year or annual extension roadmaps with banner programs and activity schedules.'],
                    ['step' => '02', 'icon' => 'mdi-lightbulb-on-outline', 'title' => 'Draft Project Proposal', 'desc' => 'Define objectives, commencement & completion dates, 6Ps expected outputs, GAD beneficiaries, and SDG alignments.'],
                    ['step' => '03', 'icon' => 'mdi-draw-pen', 'title' => 'Multi-Signatory Endorsement', 'desc' => 'Route documents through configured campus and university signatories with barcode verification and audit logs.'],
                    ['step' => '04', 'icon' => 'mdi-calendar-range', 'title' => 'Calendar & SDG Monitoring', 'desc' => 'Track ongoing project durations, activity milestones, budget utilization, and 17 UN SDG coverage in real time.'],
                ] as $w)
                    <div class="col-md-6 col-lg-3">
                        <div class="rie-card p-3.5 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <div class="step-node" style="background: var(--rie-primary-soft); color: var(--rie-primary); border: 1px solid var(--rie-primary-border);">
                                    {{ $w['step'] }}
                                </div>
                                <i class="mdi {{ $w['icon'] }} fs-4" style="color: var(--rie-primary);"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1.5">{{ $w['title'] }}</h6>
                            <p class="text-muted mb-0 extra-small">{{ $w['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 17 UN SDG Alignment Section -->
    <section class="py-5 bg-white border-bottom" id="sdg">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-5">
                    <span class="rie-eyebrow mb-2">UN Agenda 2030</span>
                    <h2 class="fw-extrabold text-dark mb-2.5 fs-3">17 UN Sustainable Development Goals Monitoring</h2>
                    <p class="text-secondary mb-3.5">
                        Every extension proposal is tagged to its direct and indirect UN Sustainable Development Goals, giving university leadership real-time visibility on institutional SDG coverage.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-rie-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2">
                        <i class="mdi mdi-earth"></i>
                        <span>Open SDG Monitoring Dashboard</span>
                    </a>
                </div>

                <div class="col-lg-7">
                    <div class="row g-2">
                        @php
                            $sdgs = [
                                ['no'=>1,'label'=>'No Poverty','color'=>'#E5243B'],
                                ['no'=>2,'label'=>'Zero Hunger','color'=>'#DDA63A'],
                                ['no'=>3,'label'=>'Good Health','color'=>'#4C9F38'],
                                ['no'=>4,'label'=>'Quality Education','color'=>'#C5192D'],
                                ['no'=>5,'label'=>'Gender Equality','color'=>'#FF3A21'],
                                ['no'=>6,'label'=>'Clean Water','color'=>'#26BDE2'],
                                ['no'=>7,'label'=>'Clean Energy','color'=>'#FCC30B'],
                                ['no'=>8,'label'=>'Decent Work','color'=>'#A21942'],
                                ['no'=>9,'label'=>'Industry & Innovation','color'=>'#FD6925'],
                                ['no'=>10,'label'=>'Reduced Inequalities','color'=>'#DD1367'],
                                ['no'=>11,'label'=>'Sustainable Cities','color'=>'#FD9D24'],
                                ['no'=>12,'label'=>'Responsible Consumption','color'=>'#BF8B2E'],
                                ['no'=>13,'label'=>'Climate Action','color'=>'#3F7E44'],
                                ['no'=>14,'label'=>'Life Below Water','color'=>'#0A97D9'],
                                ['no'=>15,'label'=>'Life on Land','color'=>'#56C02B'],
                                ['no'=>16,'label'=>'Peace & Justice','color'=>'#00689D'],
                                ['no'=>17,'label'=>'Partnerships','color'=>'#19486A'],
                            ];
                        @endphp
                        @foreach($sdgs as $sdg)
                            <div class="col-4 col-sm-3 col-md-2">
                                <div class="rounded-3 text-white text-center p-2 h-100 d-flex flex-column align-items-center justify-content-center" style="background: {{ $sdg['color'] }}; min-height: 68px; box-shadow: 0 2px 6px {{ $sdg['color'] }}40;">
                                    <div class="fw-extrabold" style="font-size: 16px; line-height: 1;">{{ $sdg['no'] }}</div>
                                    <div style="font-size: 8.5px; font-weight: 700; line-height: 1.15; margin-top: 3px;">{{ $sdg['label'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Theme Footer Strip -->
    <section class="py-4 text-white" style="background: linear-gradient(135deg, var(--rie-primary) 0%, var(--rie-primary-dark) 100%);">
        <div class="container">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold text-white">{{ number_format($stats['banner_programs']) }}+</div>
                    <div class="extra-small text-white opacity-85 text-uppercase fw-bold">Banner Programs</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold text-white">{{ number_format($stats['extensionists']) }}+</div>
                    <div class="extra-small text-white opacity-85 text-uppercase fw-bold">Extension Personnel</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold text-white">{{ number_format($stats['agenda_priorities']) }}</div>
                    <div class="extra-small text-white opacity-85 text-uppercase fw-bold">Priority Agendas</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold text-white">{{ $stats['campuses'] }}</div>
                    <div class="extra-small text-white opacity-85 text-uppercase fw-bold">Campus Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-white border-top">
        <div class="container">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if($sealLogo)
                            <img src="{{ $sealLogo }}" alt="{{ $schoolAcronym }}" style="width: 28px; height: 28px; object-fit: contain;">
                        @else
                            <div class="rounded-2 d-flex align-items-center justify-content-center text-white" style="width: 28px; height: 28px; background: var(--rie-primary);">
                                <i class="mdi mdi-handshake-outline"></i>
                            </div>
                        @endif
                        <span class="fw-extrabold text-dark">{{ $schoolAcronym }} &bull; RIE Extension Management System</span>
                    </div>
                    <p class="text-muted mb-0 extra-small">{{ $schoolName }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="text-muted text-decoration-none extra-small fw-semibold">Portal Sign In</a>
                        <a href="{{ route('document.track') }}" class="text-muted text-decoration-none extra-small fw-semibold">Track Document</a>
                        <a href="#modules" class="text-muted text-decoration-none extra-small fw-semibold">Modules</a>
                        <a href="#sdg" class="text-muted text-decoration-none extra-small fw-semibold">SDG Alignment</a>
                    </div>
                    <div class="text-muted mt-1 extra-small">&copy; {{ date('Y') }} {{ $schoolAcronym }} RIE Platform.</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
