<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RIE-EXTEND — Research, Innovation & Extension Management System</title>
    <meta name="description" content="Sovereign University Extension Management Platform for planning, proposing, and tracking community extension projects aligned with the 17 UN Sustainable Development Goals.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Dynamic Theme Customizer Styles -->
    @include('layouts.dss.theme-styles')

    <style>
        :root {
            --rie-green:   #16a34a;
            --rie-emerald: #059669;
            --rie-teal:    #0d9488;
            --rie-amber:   #d97706;
            --rie-blue:    #2563eb;
            --rie-purple:  #7c3aed;
            --landing-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        *, html, body, input, select, textarea, button, span, p, h1, h2, h3, h4, h5, h6 {
            font-family: var(--landing-font) !important;
        }

        body {
            font-family: var(--landing-font);
            color: #0f172a;
            background-color: #ffffff;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Hero ─────────────────────────────────────────────── */
        .rie-hero {
            position: relative;
            background: radial-gradient(ellipse at 60% 0%, #f0fdf4 0%, #ffffff 70%);
            padding: 5rem 0 4rem;
            border-bottom: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .rie-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(22,163,74,.07) 0%, transparent 50%),
                radial-gradient(circle at 85% 80%, rgba(5,150,105,.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .rie-gradient-text {
            background: linear-gradient(135deg, var(--rie-green) 0%, var(--rie-teal) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        /* ── Navbar ───────────────────────────────────────────── */
        .rie-nav-link {
            font-size: .9rem;
            font-weight: 600;
            color: #475569 !important;
            padding: .4rem .85rem !important;
            border-radius: 8px;
            transition: all .2s;
        }
        .rie-nav-link:hover { color: var(--rie-green) !important; background: #f0fdf4; }

        .btn-rie-primary {
            background: linear-gradient(135deg, var(--rie-green) 0%, var(--rie-teal) 100%);
            color: #fff !important;
            border: none;
            border-radius: 50rem;
            font-weight: 700;
            transition: all .25s;
            box-shadow: 0 4px 14px rgba(22,163,74,.28);
        }
        .btn-rie-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22,163,74,.38);
            filter: brightness(1.06);
        }

        /* ── Eyebrow Pill Badge ───────────────────────────────── */
        .rie-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem 1rem;
            border-radius: 50rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: var(--rie-green);
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .3px;
            margin-bottom: 1.25rem;
        }

        /* ── Feature Chips (hero row) ─────────────────────────── */
        .rie-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .4rem 1rem;
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: .87rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            box-shadow: 0 2px 6px rgba(15,23,42,.04);
        }
        .rie-chip:hover {
            border-color: var(--rie-green);
            color: var(--rie-green);
            background: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(22,163,74,.12);
        }

        /* ── SDG Goals Carousel Strip ─────────────────────────── */
        .sdg-strip {
            overflow-x: auto;
            display: flex;
            gap: .6rem;
            padding: .5rem 0;
            scrollbar-width: none;
        }
        .sdg-strip::-webkit-scrollbar { display: none; }
        .sdg-pill {
            white-space: nowrap;
            padding: .3rem .85rem;
            border-radius: 50rem;
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid transparent;
            flex-shrink: 0;
        }

        /* ── Module Cards ─────────────────────────────────────── */
        .rie-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            overflow: hidden;
            transition: all .28s cubic-bezier(.16,1,.3,1);
            height: 100%;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 18px rgba(15,23,42,.04);
        }
        .rie-card:hover {
            transform: translateY(-6px);
            border-color: #cbd5e1;
            box-shadow: 0 16px 36px rgba(15,23,42,.09);
            color: inherit;
        }
        .rie-card-banner {
            height: 116px;
            padding: 1rem;
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        /* ── Stats Counter Strip ──────────────────────────────── */
        .stat-value {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.03em;
        }
        .stat-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: .65;
            margin-top: .35rem;
        }

        /* ── Feature Sections ─────────────────────────────────── */
        .feature-icon-box {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        /* ── Process Step ─────────────────────────────────────── */
        .step-node {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ── Developer Footer Card ────────────────────────────── */
        .developer-card {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15,23,42,.04);
        }
        .developer-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--rie-green), var(--rie-teal), var(--rie-blue));
        }

        /* ── Utility ──────────────────────────────────────────── */
        .extra-small { font-size: 12px; }
        .fw-extrabold { font-weight: 800 !important; }
    </style>
</head>
<body>

    <!-- ── Gradient Top Accent Bar ──────────────────────────────── -->
    <div style="height: 4px; background: linear-gradient(90deg, var(--rie-green) 0%, var(--rie-teal) 50%, var(--rie-blue) 100%);"></div>

    <!-- ── Sticky Navbar ─────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3 shadow-sm" style="box-shadow: 0 1px 6px rgba(15,23,42,.06) !important;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none fw-bold" href="{{ url('/') }}">
                @php
                    $navbarBanner = $institution->logo_variants['navbar']['url'] ?? null;
                    $sealLogo     = $institution->logo_variants['sidebar']['url'] ?? ($institution->logo_variants['app_icon']['url'] ?? ($institution->logo_path ? asset($institution->logo_path) : null));
                @endphp
                @if($navbarBanner)
                    <img src="{{ $navbarBanner }}" alt="{{ $institution->school_acronym ?: 'RIE-EXTEND' }}" style="height: 38px; width: auto; max-width: 220px; object-fit: contain;">
                @elseif($sealLogo)
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $sealLogo }}" alt="{{ $institution->school_acronym ?: 'RIE-EXTEND' }}" style="height: 38px; width: 38px; object-fit: contain;">
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-extrabold" style="font-size: 1.05rem; letter-spacing: -.02em; line-height: 1;">{{ $institution->school_acronym ?: 'RIE-EXTEND' }}</span>
                            <span class="text-muted" style="font-size: 9.5px; font-weight: 700; letter-spacing: .5px;">EXTENSION SYSTEM</span>
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, var(--rie-green) 0%, var(--rie-teal) 100%); box-shadow: 0 4px 12px rgba(22,163,74,.3);">
                            <i class="mdi mdi-handshake-outline fs-5"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-extrabold" style="font-size: 1.05rem; letter-spacing: -.02em; line-height: 1;">RIE-EXTEND</span>
                            <span class="text-muted" style="font-size: 9.5px; font-weight: 700; letter-spacing: .5px;">EXTENSION SYSTEM</span>
                        </div>
                    </div>
                @endif
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#rieNav">
                <i class="mdi mdi-menu fs-3 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="rieNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link rie-nav-link" href="#modules">Modules</a></li>
                    <li class="nav-item"><a class="nav-link rie-nav-link" href="#workflow">Workflow</a></li>
                    <li class="nav-item"><a class="nav-link rie-nav-link" href="#sdg">SDG Alignment</a></li>
                    <li class="nav-item"><a class="nav-link rie-nav-link" href="#mcda">MCDA Engine</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dss.dashboard') }}" class="btn btn-rie-primary px-4 py-2 d-flex align-items-center gap-2" style="font-size: 13px;">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-rie-primary px-4 py-2 d-flex align-items-center gap-2" style="font-size: 13px;">
                            <i class="mdi mdi-login-variant"></i>
                            <span>Portal Sign In</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Hero Section ──────────────────────────────────────────── -->
    <section class="rie-hero text-center">
        <div class="container position-relative" style="z-index: 1;">
            <!-- Eyebrow -->
            <div class="rie-eyebrow">
                <i class="mdi mdi-handshake-outline"></i>
                Research, Innovation & Extension (RIE) — Higher Education Extension Portal
            </div>

            <!-- Headline -->
            <h1 class="fw-extrabold text-dark mb-3" style="font-size: clamp(2.2rem, 4.5vw, 3rem); line-height: 1.18; letter-spacing: -.03em; max-width: 780px; margin: 0 auto .75rem;">
                Plan, Propose & Track <span class="rie-gradient-text">University Extension</span> Projects
            </h1>

            <p class="text-secondary mx-auto mb-4" style="font-size: 1.05rem; line-height: 1.7; max-width: 640px;">
                The sovereign digital platform for managing multi-year extension plans, community proposals, SDG alignments, and MCDA-based project prioritization — built for Philippine SUCs.
            </p>

            <!-- Quick Feature Chips -->
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-5">
                <a href="#modules" class="rie-chip">
                    <i class="mdi mdi-file-document-multiple-outline text-success"></i>
                    <span>Extension Plans</span>
                </a>
                <a href="#modules" class="rie-chip">
                    <i class="mdi mdi-lightbulb-on-outline" style="color: var(--rie-amber);"></i>
                    <span>Proposals & Projects</span>
                </a>
                <a href="#sdg" class="rie-chip">
                    <i class="mdi mdi-globe-model" style="color: var(--rie-blue);"></i>
                    <span>17 SDG Alignment</span>
                </a>
                <a href="#mcda" class="rie-chip">
                    <i class="mdi mdi-chart-timeline-variant-shimmer" style="color: var(--rie-purple);"></i>
                    <span>MCDA Prioritization</span>
                </a>
                <a href="{{ route('login') }}" class="rie-chip">
                    <i class="mdi mdi-draw-pen text-danger"></i>
                    <span>Digital Signatures</span>
                </a>
            </div>

            <!-- CTA Buttons -->
            <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                @auth
                    <a href="{{ route('dss.rie.proposals.create') }}" class="btn btn-rie-primary px-5 py-2 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="mdi mdi-plus-circle-outline fs-5"></i>
                        <span>Create New Proposal</span>
                    </a>
                    <a href="{{ route('dss.rie.plans.index') }}" class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold" style="font-size: 14px;">
                        View All Plans →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-rie-primary px-5 py-2 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="mdi mdi-login-variant fs-5"></i>
                        <span>Access Extension Portal</span>
                    </a>
                    <a href="#modules" class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold" style="font-size: 14px;">
                        Explore Modules →
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- ── Statistics Counter Strip ──────────────────────────────── -->
    <section class="py-4 border-bottom" style="background: #f8fafc;">
        <div class="container">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="stat-value" style="color: var(--rie-green);">{{ number_format($stats['extension_plans']) }}+</div>
                    <div class="stat-label text-muted">Extension Plans</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-value text-dark">{{ number_format($stats['extension_proposals']) }}+</div>
                    <div class="stat-label text-muted">Project Proposals</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-value" style="color: var(--rie-blue);">{{ $stats['sdg_goals'] }}</div>
                    <div class="stat-label text-muted">UN SDG Goals Tracked</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-value" style="color: var(--rie-teal);">{{ $stats['community_satisfaction'] }}</div>
                    <div class="stat-label text-muted">Community Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Module Cards ───────────────────────────────────────────── -->
    <section class="py-5 bg-white border-bottom" id="modules">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                <div>
                    <span class="badge rounded-pill px-3 py-1 mb-1" style="background: #f0fdf4; color: var(--rie-green); border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700;">Core Modules</span>
                    <h2 class="fw-extrabold text-dark mb-0 fs-3">Extension Management Modules</h2>
                </div>
                <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill btn-sm px-3 fw-bold">
                    Access All Modules →
                </a>
            </div>

            <div class="row g-3">
                <!-- Card 1: Extension Plans -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, #16a34a 0%, #059669 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10px;">Multi-Year Roadmap</span>
                            <i class="mdi mdi-file-document-multiple-outline fs-3"></i>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="badge rounded-pill" style="background: #f0fdf4; color: var(--rie-green); border: 1px solid #bbf7d0; font-size: 10px;">Extension Plans</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 fs-6">Annual & Multi-Year Extension Plan</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1" style="line-height: 1.55;">
                                Create institutional extension roadmaps with banner programs, agendas, target beneficiaries, and budget allocation frameworks.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="extra-small text-muted" style="font-size: 11px;">{{ $stats['extension_plans'] }} Active Plans</span>
                                <span class="fw-bold extra-small" style="color: var(--rie-green); font-size: 12px;">Create Plan →</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 2: Extension Proposals -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10px;">Project Proposals</span>
                            <i class="mdi mdi-lightbulb-on-outline fs-3"></i>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="badge rounded-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 10px;">Proposals</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 fs-6">Extension Project Proposal System</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1" style="line-height: 1.55;">
                                Draft, submit, and track extension project proposals with SDG tagging, multi-signatory digital endorsements, and AI-assisted narrative generation.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="extra-small text-muted" style="font-size: 11px;">{{ $stats['extension_proposals'] }} Proposals</span>
                                <span class="fw-bold extra-small" style="color: #d97706; font-size: 12px;">Submit Proposal →</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 3: SDG Alignment -->
                <div class="col-md-6 col-lg-4">
                    <a href="#sdg" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10px;">UN Agenda 2030</span>
                            <i class="mdi mdi-globe-model fs-3"></i>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="badge rounded-pill" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 10px;">SDG Tracker</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 fs-6">17 UN Sustainable Development Goals</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1" style="line-height: 1.55;">
                                Automatically tag and track extension proposals against all 17 UN SDGs with AI-generated narrative summaries and impact reports.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="extra-small text-muted" style="font-size: 11px;">All 17 Goals Tracked</span>
                                <span class="fw-bold extra-small" style="color: #2563eb; font-size: 12px;">View SDGs →</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 4: MCDA Decision Engine -->
                <div class="col-md-6 col-lg-6">
                    <a href="#mcda" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10px;">AHP & TOPSIS</span>
                            <i class="mdi mdi-chart-timeline-variant-shimmer fs-3"></i>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="badge rounded-pill" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ede9fe; font-size: 10px;">MCDA Engine</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 fs-6">Multi-Criteria Decision Analysis for Project Prioritization</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1" style="line-height: 1.55;">
                                Objective project ranking using Saaty's AHP pairwise comparisons and TOPSIS closeness coefficients — ensures fairness in allocating limited extension resources.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="extra-small text-muted" style="font-size: 11px;">CR &lt; 0.10 Validated</span>
                                <span class="fw-bold extra-small" style="color: #7c3aed; font-size: 12px;">Run MCDA →</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 5: Digital Signatures & Document Workflow -->
                <div class="col-md-6 col-lg-6">
                    <a href="{{ route('login') }}" class="rie-card">
                        <div class="rie-card-banner text-white" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%);">
                            <span class="badge rounded-pill bg-white bg-opacity-25 font-mono" style="font-size: 10px;">E-Signature Workflow</span>
                            <i class="mdi mdi-draw-pen fs-3"></i>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="mb-1">
                                <span class="badge rounded-pill" style="background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4; font-size: 10px;">Document Routing</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 fs-6">Multi-Signatory Digital Endorsement Workflow</h5>
                            <p class="text-muted extra-small mb-3 flex-grow-1" style="line-height: 1.55;">
                                Configurable multi-level signatory chain for extension proposals — from proponent to campus director, VP for Extension, and University President.
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="extra-small text-muted" style="font-size: 11px;">Full Audit Trail</span>
                                <span class="fw-bold extra-small" style="color: #0d9488; font-size: 12px;">Access Portal →</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Extension Proposal Workflow ───────────────────────────── -->
    <section class="py-5 bg-white border-bottom" id="workflow">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge rounded-pill px-3 py-1 mb-2" style="background: #f0fdf4; color: var(--rie-green); border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700;">Proposal Lifecycle</span>
                <h2 class="fw-extrabold text-dark mb-2 fs-3">Extension Project Workflow — End-to-End</h2>
                <p class="text-muted mx-auto" style="max-width: 560px; line-height: 1.65;">From initial ideation to signed approval, the system guides extension proponents through each stage seamlessly.</p>
            </div>

            <div class="row g-3">
                @foreach([
                    ['step' => '01', 'icon' => 'mdi-file-document-outline', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'title' => 'Create Extension Plan', 'desc' => 'Institutional coordinator sets up a multi-year or annual extension plan with banner programs, agendas, and target communities.'],
                    ['step' => '02', 'icon' => 'mdi-lightbulb-on-outline', 'color' => '#d97706', 'bg' => '#fffbeb', 'title' => 'Draft Proposal', 'desc' => 'Faculty proponent creates a project proposal under an approved plan — with SDG tags, budget, target beneficiaries, and AI-generated narratives.'],
                    ['step' => '03', 'icon' => 'mdi-scale-balance', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'title' => 'MCDA Prioritization', 'desc' => 'Admin runs AHP/TOPSIS analysis to objectively rank proposals across competing resource constraints.'],
                    ['step' => '04', 'icon' => 'mdi-draw-pen', 'color' => '#0d9488', 'bg' => '#f0fdfa', 'title' => 'Multi-Signatory Approval', 'desc' => 'Proposal routes through configured signatories — each receives a digital endorsement request with PDF preview and audit logging.'],
                    ['step' => '05', 'icon' => 'mdi-file-pdf-box', 'color' => '#e11d48', 'bg' => '#fff1f2', 'title' => 'Generate Official PDF', 'desc' => 'System generates a letterhead PDF with official signatory block, QR barcode, and institutional header for archiving.'],
                    ['step' => '06', 'icon' => 'mdi-check-decagram-outline', 'color' => '#2563eb', 'bg' => '#eff6ff', 'title' => 'Archive & Monitor', 'desc' => 'Approved proposals are tracked for implementation progress, community impact data, and SDG reporting compliance.'],
                ] as $w)
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-4 border h-100 d-flex gap-3 align-items-start" style="background: #fff; box-shadow: 0 2px 10px rgba(15,23,42,.03);">
                        <div class="step-node" style="background: {{ $w['bg'] }}; color: {{ $w['color'] }}; border: 1px solid {{ $w['color'] }}40;">
                            {{ $w['step'] }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="mdi {{ $w['icon'] }}" style="color: {{ $w['color'] }}; font-size: 1.1rem;"></i>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 13.5px;">{{ $w['title'] }}</h6>
                            </div>
                            <p class="text-muted mb-0 extra-small" style="line-height: 1.55;">{{ $w['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── SDG Alignment Section ──────────────────────────────────── -->
    <section class="py-5 border-bottom" id="sdg" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <span class="badge rounded-pill px-3 py-1 mb-2" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 700;">UN Agenda 2030</span>
                    <h2 class="fw-extrabold text-dark mb-3">Aligned to 17 UN Sustainable Development Goals</h2>
                    <p class="text-secondary mb-4" style="line-height: 1.7;">
                        Every extension proposal is mapped to the relevant SDGs. The system auto-generates impact narrative summaries using AI and produces SDG compliance reports for CHED and BOR submissions.
                    </p>
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach([
                            ['icon' => 'mdi-tag-multiple-outline', 'color' => '#2563eb', 'bg' => '#eff6ff', 'title' => 'Multi-SDG Tagging', 'desc' => 'Tag each proposal to one or more of the 17 SDGs with priority weighting.'],
                            ['icon' => 'mdi-brain', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'title' => 'AI Narrative Generator', 'desc' => 'Auto-generate SDG-aligned impact narratives from proposal data using AI.'],
                            ['icon' => 'mdi-chart-bar', 'color' => '#059669', 'bg' => '#f0fdf4', 'title' => 'Impact Dashboard', 'desc' => 'Track cumulative SDG contributions across all approved extension projects.'],
                        ] as $f)
                        <div class="d-flex align-items-start gap-3">
                            <div class="feature-icon-box" style="background: {{ $f['bg'] }}; color: {{ $f['color'] }}; border: 1px solid {{ $f['color'] }}30;">
                                <i class="mdi {{ $f['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $f['title'] }}</h6>
                                <p class="text-muted extra-small mb-0" style="line-height: 1.5;">{{ $f['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-rie-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                        <i class="mdi mdi-globe-model"></i>
                        <span>View SDG Dashboard</span>
                    </a>
                </div>

                <div class="col-lg-7">
                    <!-- SDG Goal Tiles Grid -->
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
                        <div class="col-4 col-sm-3 col-md-2" style="max-width: 14.28%; flex: 0 0 14.28%;">
                            <div class="rounded-3 text-white text-center p-2 h-100 d-flex flex-column align-items-center justify-content-center" style="background: {{ $sdg['color'] }}; min-height: 70px; cursor: default; transition: transform .2s; box-shadow: 0 2px 8px {{ $sdg['color'] }}50;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                                <div class="fw-extrabold" style="font-size: 16px; line-height: 1;">{{ $sdg['no'] }}</div>
                                <div style="font-size: 7.5px; font-weight: 700; line-height: 1.2; margin-top: 3px; opacity: .92;">{{ $sdg['label'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-muted text-center mt-3 extra-small" style="font-size: 11px;">
                        <i class="mdi mdi-information-outline me-1"></i>All 17 UN Sustainable Development Goals tracked per extension proposal
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── MCDA Decision Engine ───────────────────────────────────── -->
    <section class="py-5 bg-white border-bottom" id="mcda">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2">
                    <span class="badge rounded-pill px-3 py-1 mb-2" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ede9fe; font-size: 11px; font-weight: 700;">Mathematical Decision Intelligence</span>
                    <h2 class="fw-extrabold text-dark mb-3">Project Prioritization via AHP & TOPSIS</h2>
                    <p class="text-secondary mb-4" style="line-height: 1.7;">
                        When multiple extension proposals compete for limited funds, the built-in MCDA engine eliminates bias by computing objective rankings using eigenvector weights and Euclidean distance to ideal solutions.
                    </p>
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach([
                            ['icon' => 'mdi-matrix', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'title' => "Saaty's Pairwise Comparison Matrix", 'desc' => 'Faculty panels input pairwise criteria judgments; system validates CR < 0.10.'],
                            ['icon' => 'mdi-vector-triangle', 'color' => '#2563eb', 'bg' => '#eff6ff', 'title' => 'TOPSIS Ideal Solution Ranking', 'desc' => 'Calculates Euclidean distance from positive and negative ideals for each project.'],
                            ['icon' => 'mdi-trophy-award', 'color' => '#d97706', 'bg' => '#fffbeb', 'title' => 'Closeness Coefficient (Ci*) Ranking', 'desc' => 'Outputs 0–100% relative scores for transparent, defensible resource allocation.'],
                        ] as $f)
                        <div class="d-flex align-items-start gap-3">
                            <div class="feature-icon-box" style="background: {{ $f['bg'] }}; color: {{ $f['color'] }}; border: 1px solid {{ $f['color'] }}30;">
                                <i class="mdi {{ $f['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $f['title'] }}</h6>
                                <p class="text-muted extra-small mb-0" style="line-height: 1.5;">{{ $f['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13.5px; border-color: #7c3aed; color: #7c3aed;">
                        <i class="mdi mdi-calculator-variant"></i>
                        <span>Launch MCDA Studio</span>
                    </a>
                </div>

                <!-- MCDA Mockup Card -->
                <div class="col-lg-6 order-lg-1">
                    <div class="p-4 rounded-4 border bg-light shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-dark font-mono" style="font-size: 10px;">MCDA EVALUATION #RIE-2026-12</span>
                                <h6 class="fw-bold text-dark mt-1 mb-0">Extension Projects Prioritization</h6>
                            </div>
                            <span class="badge bg-success font-mono" style="font-size: 10px;">CR: 0.038 ✓</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach([
                                ['rank'=>1,'title'=>'Livelihood Skills Training — Brgy. San Juan','score'=>92.4,'color'=>'#16a34a'],
                                ['rank'=>2,'title'=>'Environmental Awareness Campaign — Coastal','score'=>78.1,'color'=>'#2563eb'],
                                ['rank'=>3,'title'=>'Digital Literacy for Senior Citizens','score'=>64.7,'color'=>'#d97706'],
                            ] as $p)
                            <div class="p-2 rounded-3 bg-white border d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:26px;height:26px;background:{{ $p['color'] }}20;color:{{ $p['color'] }};font-size:11px;flex-shrink:0;">#{{ $p['rank'] }}</span>
                                    <div class="text-truncate extra-small fw-semibold text-dark">{{ $p['title'] }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge fw-bold font-mono" style="background:{{ $p['color'] }}15;color:{{ $p['color'] }};border:1px solid {{ $p['color'] }}30;font-size:10px;">{{ $p['score'] }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-muted extra-small mt-2 mb-0 text-center" style="font-size: 10.5px;">Ci* Relative Closeness Coefficient Rankings</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Stats & Scale Strip ────────────────────────────────────── -->
    <section class="py-4 border-bottom" style="background: linear-gradient(135deg, #16a34a 0%, #0d9488 100%);">
        <div class="container">
            <div class="row g-3 text-center text-white">
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold">{{ number_format($stats['banner_programs']) }}+</div>
                    <div class="extra-small text-white fw-semibold mt-1" style="opacity:.8; font-size:10px; text-transform:uppercase; letter-spacing:.5px;">Banner Programs</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold">{{ number_format($stats['extensionists']) }}+</div>
                    <div class="extra-small text-white fw-semibold mt-1" style="opacity:.8; font-size:10px; text-transform:uppercase; letter-spacing:.5px;">Extension Personnel</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold">{{ number_format($stats['agenda_priorities']) }}</div>
                    <div class="extra-small text-white fw-semibold mt-1" style="opacity:.8; font-size:10px; text-transform:uppercase; letter-spacing:.5px;">Priority Agendas</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fs-3 fw-extrabold">{{ $stats['campuses'] }}</div>
                    <div class="extra-small text-white fw-semibold mt-1" style="opacity:.8; font-size:10px; text-transform:uppercase; letter-spacing:.5px;">Campus Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Footer ─────────────────────────────────────────────────── -->
    <footer class="py-4 bg-white border-top">
        <div class="container">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;background:linear-gradient(135deg,var(--rie-green) 0%,var(--rie-teal) 100%);">
                            <i class="mdi mdi-handshake-outline" style="font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-extrabold text-dark" style="font-size:.95rem;letter-spacing:-.02em;">RIE-EXTEND</div>
                            <div class="text-muted" style="font-size:9.5px;font-weight:700;letter-spacing:.5px;">RESEARCH, INNOVATION & EXTENSION SYSTEM</div>
                        </div>
                    </div>
                    <p class="text-muted mb-0 extra-small">
                        {{ $institution->school_name ?? 'State University & College' }} — Extension Management Platform
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="text-muted text-decoration-none extra-small fw-semibold">Portal Sign In</a>
                        <a href="#modules" class="text-muted text-decoration-none extra-small fw-semibold">Modules</a>
                        <a href="#sdg" class="text-muted text-decoration-none extra-small fw-semibold">SDG Alignment</a>
                    </div>
                    <div class="text-muted mt-2" style="font-size:11px;">&copy; {{ date('Y') }} RIE-EXTEND Platform. Built for Philippine Higher Education.</div>
                </div>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
