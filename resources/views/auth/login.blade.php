<!DOCTYPE html>
@php
    $institution = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $theme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $activeUiTemplate = $theme->ui_template ?? 'flup_minimal';
    $uiTemplatesMeta = \App\Services\DSS\ThemePresets::uiTemplates();
    $activeUiMeta = $uiTemplatesMeta[$activeUiTemplate] ?? $uiTemplatesMeta['flup_minimal'];

    $faviconUrl = $institution->logo_variants['favicon']['url'] ?? ($institution->logo_path ? asset($institution->logo_path) : null);
    $sealLogo = $institution->logo_variants['sidebar']['url'] ?? ($institution->logo_variants['app_icon']['url'] ?? ($institution->logo_path ? asset($institution->logo_path) : null));
    $schoolAcronym = $institution->school_acronym ?: 'RIE-EXTEND';
    $schoolName = $institution->school_name ?: 'Research, Innovation & Extension System';
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — {{ $schoolAcronym }} Extension Management System</title>
    <meta name="description" content="Secure portal sign-in for the {{ $schoolAcronym }} Research, Innovation & Extension System.">

    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    @endif

    <!-- Local Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <!-- Dynamic SaaS Theme Engine (--dss-primary, --dss-secondary, --dss-accent, UI template CSS) -->
    @include('layouts.dss.theme-styles')

    <style>
        :root {
            --auth-primary: var(--dss-primary, #047857);
            --auth-primary-dark: color-mix(in srgb, var(--dss-primary, #047857) 80%, #0f172a);
            --auth-primary-soft: color-mix(in srgb, var(--dss-primary, #047857) 10%, #ffffff);
            --auth-primary-border: color-mix(in srgb, var(--dss-primary, #047857) 24%, #ffffff);
            --auth-secondary: var(--dss-secondary, #0284c7);
            --auth-accent: var(--dss-accent, #f59e0b);
            --auth-card-radius: var(--dss-card-radius, 16px);
            --auth-btn-radius: var(--dss-btn-radius, 10px);
        }

        body {
            min-height: 100vh;
            background: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
        }

        .auth-split-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
            background: #f8fafc;
        }

        /* ====================================================================
           LEFT SHOWCASE PANEL — ADAPTS TO SELECTED UI TEMPLATE & THEME
           ==================================================================== */
        /* 1. Default / Flup Minimal — Clean SaaS Bordered Surface */
        .auth-showcase {
            flex: 1.15;
            padding: 3.5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .auth-showcase::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 12% 18%, color-mix(in srgb, var(--auth-primary) 8%, transparent) 0%, transparent 45%),
                radial-gradient(circle at 88% 82%, color-mix(in srgb, var(--auth-secondary) 7%, transparent) 0%, transparent 45%);
            pointer-events: none;
        }

        /* 2. Print Manzil — Soft Bento Showcase */
        body[data-ui-template="print_manzil"] .auth-showcase {
            background: linear-gradient(145deg, var(--auth-primary-soft) 0%, #f8fafc 60%, color-mix(in srgb, var(--auth-secondary) 8%, #ffffff) 100%);
            border-right: 1px solid var(--auth-primary-border);
        }

        /* 3. Material Able — Classic Enterprise Colored Showcase */
        body[data-ui-template="material_able"] .auth-showcase {
            background: linear-gradient(145deg, var(--auth-primary) 0%, var(--auth-primary-dark) 100%);
            color: #ffffff;
            border-right: none;
        }
        body[data-ui-template="material_able"] .auth-showcase .brand-title,
        body[data-ui-template="material_able"] .auth-showcase .auth-headline,
        body[data-ui-template="material_able"] .auth-showcase .bento-title {
            color: #ffffff !important;
        }
        body[data-ui-template="material_able"] .auth-showcase .brand-sub,
        body[data-ui-template="material_able"] .auth-showcase .auth-hero-desc,
        body[data-ui-template="material_able"] .auth-showcase .bento-sub,
        body[data-ui-template="material_able"] .auth-showcase .auth-showcase-footer {
            color: rgba(255, 255, 255, 0.82) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }
        body[data-ui-template="material_able"] .auth-showcase .auth-gradient-text {
            background: none !important;
            -webkit-text-fill-color: var(--auth-accent) !important;
            color: var(--auth-accent) !important;
        }
        body[data-ui-template="material_able"] .auth-showcase .auth-pill-badge {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.28);
            color: #ffffff;
        }
        body[data-ui-template="material_able"] .auth-showcase .auth-bento-card {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .auth-logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            position: relative;
            z-index: 2;
        }
        .auth-logo-badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--auth-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px color-mix(in srgb, var(--auth-primary) 28%, transparent);
            flex-shrink: 0;
        }
        .brand-title {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.1;
        }
        .brand-sub {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }

        .auth-hero-content {
            margin: auto 0;
            padding: 2rem 0;
            max-width: 560px;
            position: relative;
            z-index: 2;
        }

        .auth-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: var(--auth-primary-soft);
            border: 1px solid var(--auth-primary-border);
            color: var(--auth-primary);
            border-radius: 999px;
            padding: 0.35rem 0.95rem;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .auth-headline {
            font-size: clamp(2rem, 3.2vw, 2.75rem);
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.03em;
            color: #0f172a;
            margin-bottom: 1.15rem;
        }

        .auth-gradient-text {
            background: linear-gradient(135deg, var(--auth-primary) 0%, var(--auth-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-hero-desc {
            font-size: 0.96rem;
            color: #475569;
            line-height: 1.68;
            margin-bottom: 2rem;
        }

        .auth-bento-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem;
        }
        .auth-bento-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--auth-card-radius, 14px);
            padding: 1.1rem 1rem;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        body[data-ui-template="print_manzil"] .auth-bento-card {
            border-radius: 20px;
        }
        .auth-bento-card:hover {
            transform: translateY(-2px);
            border-color: var(--auth-primary-border);
        }
        .auth-bento-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 0.65rem;
            background: var(--auth-primary-soft);
            color: var(--auth-primary);
            border: 1px solid var(--auth-primary-border);
        }
        .bento-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.15rem;
        }
        .bento-sub {
            font-size: 11px;
            color: #64748b;
            line-height: 1.35;
        }

        .auth-showcase-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.25rem;
            border-top: 1px solid #e2e8f0;
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* ====================================================================
           RIGHT SIGN-IN FORM PANEL — ADAPTS TO SELECTED UI TEMPLATE & THEME
           ==================================================================== */
        .auth-form-panel {
            width: 490px;
            min-width: 440px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 3rem;
            position: relative;
            z-index: 10;
            background: #f8fafc;
        }

        .auth-card-box {
            background: var(--dss-card-bg, #ffffff);
            border: 1px solid var(--dss-card-border, #e2e8f0);
            border-radius: var(--auth-card-radius, 16px);
            padding: 2.5rem 2.25rem;
            box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.06);
            position: relative;
            overflow: hidden;
        }
        body[data-ui-template="print_manzil"] .auth-card-box {
            border-radius: 24px;
            box-shadow: 0 20px 45px -12px rgba(15, 23, 42, 0.08);
        }
        body[data-ui-template="material_able"] .auth-card-box {
            border-radius: 12px;
            border-top: 4px solid var(--auth-primary);
        }

        .auth-card-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--auth-primary) 0%, var(--auth-secondary) 60%, var(--auth-accent) 100%);
        }
        body[data-ui-template="material_able"] .auth-card-box::before {
            display: none;
        }

        .auth-nav-back {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 1.35rem;
            transition: all 0.18s ease;
        }
        .auth-nav-back:hover {
            color: var(--auth-primary);
            transform: translateX(-2px);
        }

        .auth-card-head h2 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #0f172a;
            margin-bottom: 0.3rem;
        }
        .auth-card-head p {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .auth-alert {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.825rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }
        .auth-alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .auth-alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .auth-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .auth-input-group {
            position: relative;
            margin-bottom: 1.15rem;
        }
        .auth-input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.18s ease;
        }
        .auth-input {
            width: 100%;
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 0.72rem 1rem 0.72rem 2.65rem !important;
            font-size: 0.9rem !important;
            color: #0f172a !important;
            outline: none !important;
            transition: all 0.2s ease !important;
        }
        .auth-input:focus {
            border-color: var(--auth-primary) !important;
            box-shadow: 0 0 0 3px var(--auth-primary-soft) !important;
        }
        .auth-input-group:focus-within .input-icon {
            color: var(--auth-primary);
        }

        .auth-pwd-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
        }
        .auth-pwd-toggle:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .auth-forgot-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--auth-primary);
            text-decoration: none;
        }
        .auth-forgot-link:hover {
            text-decoration: underline;
        }

        .auth-btn-primary {
            width: 100%;
            padding: 0.8rem 1.25rem;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--dss-btn-primary-text, #ffffff);
            background: var(--dss-btn-primary-bg, var(--auth-primary));
            border: 1px solid var(--dss-btn-primary-bg, var(--auth-primary));
            border-radius: var(--auth-btn-radius, 10px);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px color-mix(in srgb, var(--auth-primary) 26%, transparent);
        }
        .auth-btn-primary:hover {
            background: var(--auth-primary-dark);
            border-color: var(--auth-primary-dark);
            transform: translateY(-1px);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            font-size: 10.5px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 1.35rem 0;
            font-weight: 700;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .auth-btn-google {
            width: 100%;
            padding: 0.72rem 1rem;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--auth-btn-radius, 10px);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            text-decoration: none;
        }
        .auth-btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .auth-card-footer {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 1.5rem;
        }

        @media (max-width: 992px) {
            .auth-showcase {
                display: none;
            }
            .auth-form-panel {
                width: 100%;
                min-width: unset;
                max-width: 480px;
                margin: 0 auto;
                padding: 2rem 1.25rem;
            }
        }
    </style>
</head>
<body data-ui-template="{{ $activeUiTemplate }}">

<div class="auth-split-wrapper">
    {{-- LEFT SHOWCASE PANEL (Matches Active UI Template & Theme Colors) --}}
    <div class="auth-showcase">
        <div class="auth-logo-wrap">
            @if($sealLogo)
                <img src="{{ $sealLogo }}" alt="{{ $schoolAcronym }}" style="width: 46px; height: 46px; object-fit: contain; border-radius: 12px; background: #ffffff; padding: 4px; border: 1px solid #e2e8f0;">
            @else
                <div class="auth-logo-badge">
                    <i class="mdi mdi-handshake-outline fs-4"></i>
                </div>
            @endif
            <div>
                <div class="brand-title">{{ $schoolAcronym }}</div>
                <div class="brand-sub">{{ $schoolName }}</div>
            </div>
        </div>

        <div class="auth-hero-content">
            <div class="auth-pill-badge">
                <i class="mdi {{ $activeUiMeta['icon'] ?? 'mdi-handshake-outline' }}"></i>
                <span>Research, Innovation &amp; Extension (RIE) Portal</span>
            </div>

            <h1 class="auth-headline">
                Advancing Communities through <span class="auth-gradient-text">Extension &amp; Innovation.</span>
            </h1>

            <p class="auth-hero-desc">
                Centralized university operating system for Extension Plans, Project Proposals, Commencement &amp; Duration Range Monitoring, 17 UN Sustainable Development Goals alignment, and Multi-Signatory Endorsement.
            </p>

            <div class="auth-bento-grid">
                <div class="auth-bento-card">
                    <div class="auth-bento-icon">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                    </div>
                    <div class="bento-title">Extension Plans</div>
                    <div class="bento-sub">Annual &amp; Multi-Year Roadmaps</div>
                </div>

                <div class="auth-bento-card">
                    <div class="auth-bento-icon">
                        <i class="mdi mdi-calendar-range"></i>
                    </div>
                    <div class="bento-title">Range Calendar</div>
                    <div class="bento-sub">Commencement &amp; Durations</div>
                </div>

                <div class="auth-bento-card">
                    <div class="auth-bento-icon">
                        <i class="mdi mdi-earth"></i>
                    </div>
                    <div class="bento-title">17 UN SDGs</div>
                    <div class="bento-sub">Direct &amp; Indirect Impact</div>
                </div>
            </div>
        </div>

        <div class="auth-showcase-footer">
            <span>{{ $schoolName }}</span>
            <span>&copy; {{ date('Y') }} {{ $schoolAcronym }} RIE System</span>
        </div>
    </div>

    {{-- RIGHT SIGN-IN FORM PANEL --}}
    <div class="auth-form-panel">
        <div class="auth-card-box">
            <a href="{{ url('/') }}" class="auth-nav-back">
                <i class="mdi mdi-arrow-left"></i> Back to Public Portal
            </a>

            <div class="auth-card-head">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <span class="badge rounded-pill px-2.5 py-1 font-mono" style="background: var(--auth-primary-soft); color: var(--auth-primary); border: 1px solid var(--auth-primary-border); font-size: 10.5px;">
                        <i class="mdi mdi-shield-check-outline me-1"></i>SECURE GATEWAY
                    </span>
                    <span class="text-muted extra-small fw-semibold" style="font-size: 11px;">
                        <i class="mdi {{ $activeUiMeta['icon'] ?? 'mdi-layers-outline' }} text-primary me-0.5"></i>{{ $activeUiMeta['id'] === 'print_manzil' ? 'Print Manzil UI' : ($activeUiMeta['id'] === 'material_able' ? 'Material Able UI' : 'Flup Minimal UI') }}
                    </span>
                </div>
                <h2>Portal Sign In</h2>
                <p>Enter your institutional credentials or authenticate via Google Workspace SSO.</p>
            </div>

            @if(session('status'))
                <div class="auth-alert auth-alert-success">
                    <i class="mdi mdi-check-circle fs-5 flex-shrink-0"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="auth-alert auth-alert-danger">
                    <i class="mdi mdi-alert-circle fs-5 flex-shrink-0"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="auth-label" for="email">Institutional Email</label>
                    <div class="auth-input-group">
                        <i class="mdi mdi-email-outline input-icon"></i>
                        <input
                            type="email"
                            class="auth-input @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="username@institution.edu.ph"
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="auth-label mb-0" for="password">Password</label>
                        <a href="{{ route('password.request') }}" class="auth-forgot-link">Forgot password?</a>
                    </div>
                    <div class="auth-input-group">
                        <i class="mdi mdi-lock-outline input-icon"></i>
                        <input
                            type="password"
                            class="auth-input"
                            id="password"
                            name="password"
                            value=""
                            required
                            placeholder="Enter your account password"
                            autocomplete="current-password"
                        >
                        <button type="button" class="auth-pwd-toggle" onclick="togglePassword()" id="togglePwdBtn" aria-label="Toggle password visibility">
                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3.5">
                    <div class="form-check m-0">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" checked>
                        <label class="form-check-label small text-secondary fw-semibold" for="remember">Keep me signed in</label>
                    </div>
                </div>

                <button type="submit" class="auth-btn-primary" id="signinBtn">
                    <i class="mdi mdi-login fs-5"></i>
                    <span>Sign In to Workspace</span>
                </button>

                @if(config('services.google.client_id'))
                    <div class="auth-divider">or continue with sso</div>

                    <a href="{{ route('auth.google') }}" class="auth-btn-google" id="googleSsoBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Sign in with Google Workspace</span>
                    </a>
                @endif

                <div class="auth-card-footer">
                    Protected by <strong>{{ $schoolAcronym }} Multi-Tenant Sovereign Isolation</strong> &bull; RIE Extension System
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
        } else {
            pwd.type = 'password';
            icon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('signinBtn');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin fs-5"></i><span>Authenticating…</span>';
        btn.disabled = true;
    });
</script>
</body>
</html>
