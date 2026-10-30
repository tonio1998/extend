<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — RIE-EXTEND Extension Management System</title>
    <meta name="description" content="Secure portal sign-in for the RIE-EXTEND Research, Innovation & Extension System. Access your extension workspace.">

    <!-- Google Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* ── Canva Light Theme Tokens ───────────────────────── */
        :root {
            --canva-purple: #7d2ae8;
            --canva-indigo: #4f46e5;
            --canva-blue: #0076f5;
            --canva-cyan: #00c4cc;
            --canva-pink: #ff5757;
            --canva-bg: #f8fafc;
            --canva-card-bg: #ffffff;
            --canva-text-main: #0f172a;
            --canva-text-muted: #64748b;
            --canva-border: #e2e8f0;
            --canva-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --canva-display: 'Space Grotesk', var(--canva-font);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: var(--canva-bg);
            font-family: var(--canva-font);
            color: var(--canva-text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Canva Light Page Wrapper ───────────────────────── */
        .canva-light-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
            background:
                radial-gradient(circle at 10% 15%, rgba(125, 42, 232, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 25%, rgba(0, 196, 204, 0.09) 0%, transparent 45%),
                radial-gradient(circle at 50% 90%, rgba(0, 118, 245, 0.06) 0%, transparent 50%),
                #f8fafc;
        }

        /* Clean subtle grid texture */
        .canva-light-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.025) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Ambient Pastel Orbs */
        .canva-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
        }
        .orb-1 {
            width: 480px; height: 480px;
            top: -100px; left: -80px;
            background: radial-gradient(circle, rgba(125, 42, 232, 0.12) 0%, rgba(216, 180, 254, 0.05) 70%, transparent 80%);
        }
        .orb-2 {
            width: 440px; height: 440px;
            bottom: -60px; right: 5%;
            background: radial-gradient(circle, rgba(0, 196, 204, 0.1) 0%, rgba(125, 42, 232, 0.05) 70%, transparent 80%);
        }

        /* ── LEFT SHOWCASE PANEL (Light Canva Creative Studio) ─ */
        .canva-showcase {
            flex: 1.18;
            padding: 4rem 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        /* Universal Platform Logo */
        .canva-logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .canva-logo-badge {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #7d2ae8 0%, #0076f5 50%, #00c4cc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(125, 42, 232, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.5);
            flex-shrink: 0;
        }

        .canva-brand-title {
            font-family: var(--canva-display);
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #0f172a;
            line-height: 1.1;
        }
        .canva-brand-title span {
            background: linear-gradient(135deg, #7d2ae8 0%, #0076f5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .canva-brand-sub {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 2px;
        }

        /* Hero Content */
        .canva-hero-content {
            margin: auto 0;
            padding: 2rem 0;
            max-width: 540px;
        }

        .canva-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.5rem;
        }
        .canva-pill-badge i {
            color: #7d2ae8;
            font-size: 1.05rem;
        }

        .canva-headline {
            font-family: var(--canva-display);
            font-size: clamp(2.1rem, 3.4vw, 2.85rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: #0f172a;
            margin-bottom: 1.25rem;
        }

        .canva-gradient-text {
            background: linear-gradient(135deg, #7d2ae8 0%, #0076f5 60%, #00c4cc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .canva-hero-desc {
            font-size: 0.96rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 2.25rem;
        }

        /* 3-Chip Bento Features (Clean White Cards) */
        .canva-bento-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem;
        }

        .canva-bento-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.15rem 1rem;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }
        .canva-bento-card:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 12px 24px rgba(125, 42, 232, 0.08);
        }

        .canva-bento-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 0.75rem;
        }
        .icon-purple {
            background: #f5f3ff;
            color: #7d2ae8;
            border: 1px solid #ede9fe;
        }
        .icon-cyan {
            background: #ecfeff;
            color: #0891b2;
            border: 1px solid #cffafe;
        }
        .icon-blue {
            background: #eff6ff;
            color: #0076f5;
            border: 1px solid #dbeafe;
        }

        .canva-bento-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.2rem;
            line-height: 1.3;
        }
        .canva-bento-sub {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Showcase Footer */
        .canva-showcase-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
        }

        /* ── RIGHT SIGN-IN FORM PANEL (Crisp White Canva Card) ─ */
        .canva-form-panel {
            width: 480px;
            min-width: 440px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3rem;
            position: relative;
            z-index: 10;
        }

        .canva-card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 2.75rem 2.25rem;
            box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        /* Canva vibrant gradient top bar */
        .canva-card-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #7d2ae8 0%, #0076f5 50%, #00c4cc 100%);
        }

        /* Top Back Link */
        .canva-nav-back {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }
        .canva-nav-back:hover {
            color: #7d2ae8;
            transform: translateX(-2px);
        }

        /* Header in Card */
        .canva-card-head h2 {
            font-family: var(--canva-display);
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }
        .canva-card-head p {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        /* Alerts */
        .canva-alert {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.825rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
        }
        .canva-alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .canva-alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        /* Form Labels & Inputs */
        .canva-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 0.45rem;
        }

        .canva-input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .canva-input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .canva-input {
            width: 100%;
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem 0.75rem 2.75rem !important;
            font-size: 0.885rem !important;
            color: #0f172a !important;
            outline: none !important;
            transition: all 0.25s ease !important;
            font-family: inherit !important;
        }
        .canva-input::placeholder {
            color: #94a3b8 !important;
        }
        .canva-input:focus {
            background: #ffffff !important;
            border-color: #7d2ae8 !important;
            box-shadow: 0 0 0 3.5px rgba(125, 42, 232, 0.12) !important;
        }
        .canva-input:focus + .input-icon,
        .canva-input-group:focus-within .input-icon {
            color: #7d2ae8;
        }

        .canva-pwd-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.2s;
            line-height: 1;
        }
        .canva-pwd-toggle:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        /* Remember & Forgot Row */
        .canva-form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .canva-checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .canva-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #7d2ae8;
            cursor: pointer;
            border-radius: 4px;
        }
        .canva-checkbox-label {
            font-size: 0.82rem;
            color: #475569;
            cursor: pointer;
            font-weight: 500;
        }

        .canva-forgot-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: #7d2ae8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .canva-forgot-link:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        /* Canva Signature Gradient Primary Button */
        .canva-btn-primary {
            width: 100%;
            padding: 0.85rem 1.25rem;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            color: #ffffff;
            background: linear-gradient(135deg, #7d2ae8 0%, #0076f5 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 6px 20px rgba(125, 42, 232, 0.28);
        }
        .canva-btn-primary:hover {
            transform: translateY(-2px);
            filter: brightness(1.06);
            box-shadow: 0 10px 25px rgba(125, 42, 232, 0.38);
        }
        .canva-btn-primary:active {
            transform: translateY(0);
        }

        /* Divider */
        .canva-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            font-size: 10.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 1.5rem 0;
            font-weight: 700;
        }
        .canva-divider::before,
        .canva-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* Google SSO Button */
        .canva-btn-google {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.885rem;
            font-weight: 600;
            color: #334155;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .canva-btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        /* Card Footnote */
        .canva-card-footer {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 1.75rem;
        }
        .canva-card-footer strong {
            color: #334155;
        }

        /* ── Responsive Behavior ────────────────────────────── */
        @media (max-width: 992px) {
            .canva-showcase {
                display: none;
            }
            .canva-form-panel {
                width: 100%;
                min-width: unset;
                max-width: 480px;
                margin: 0 auto;
                padding: 2.5rem 1.25rem;
            }
        }
        @media (max-width: 480px) {
            .canva-card-box {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

<div class="canva-light-wrapper">
    <!-- Ambient Pastel Glow Orbs -->
    <div class="canva-orb orb-1"></div>
    <div class="canva-orb orb-2"></div>

    {{-- ══ LEFT SHOWCASE (Canva Clean Platform Vibe) ══ --}}
    <div class="canva-showcase">
        {{-- Universal Platform Logo (NOT school-specific) --}}
        <div class="canva-logo-wrap">
            <div class="canva-logo-badge">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="#ffffff"/>
                    <path d="M2 17L12 22L22 17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.85"/>
                </svg>
            </div>
            <div>
                <div class="canva-brand-title">
                    RIE<span>-EXTEND</span>
                </div>
                <div class="canva-brand-sub">
                    Research, Innovation &amp; Extension System
                </div>
            </div>
        </div>

        {{-- Hero Content --}}
        <div class="canva-hero-content">
            <div class="canva-pill-badge">
                <i class="mdi mdi-handshake-outline text-warning"></i>
                Research, Innovation &amp; Extension (RIE)
            </div>

            <h1 class="canva-headline">
                Advancing Communities through <span class="canva-gradient-text">Extension &amp; Innovation.</span>
            </h1>

            <p class="canva-hero-desc">
                The sovereign digital operating system for University Extension Projects, participatory community planning, 17 UN Sustainable Development Goals alignment, and objective decision prioritization.
            </p>

            {{-- 3-Chip Canva Bento Feature Cards --}}
            <div class="canva-bento-grid">
                <div class="canva-bento-card">
                    <div class="canva-bento-icon icon-cyan">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                    </div>
                    <div class="canva-bento-title">Extension Plans</div>
                    <div class="canva-bento-sub">Multi-Year &amp; Annual Roadmaps</div>
                </div>

                <div class="canva-bento-card">
                    <div class="canva-bento-icon icon-purple">
                        <i class="mdi mdi-globe-model"></i>
                    </div>
                    <div class="canva-bento-title">SDG Alignment</div>
                    <div class="canva-bento-sub">17 UN Goals Impact Tracking</div>
                </div>

                <div class="canva-bento-card">
                    <div class="canva-bento-icon icon-blue">
                        <i class="mdi mdi-chart-timeline-variant-shimmer"></i>
                    </div>
                    <div class="canva-bento-title">MCDA Matrix</div>
                    <div class="canva-bento-sub">AHP &amp; TOPSIS Evaluation</div>
                </div>
            </div>
        </div>

        {{-- Showcase Footer --}}
        <div class="canva-showcase-footer">
            <span>Sovereign SUC Extension Portal</span>
            <span>&copy; {{ date('Y') }} RIE System Platform</span>
        </div>
    </div>

    {{-- ══ RIGHT FORM PANEL (Clean Canva White Card) ══ --}}
    <div class="canva-form-panel">
        <div class="canva-card-box">
            {{-- Navigation Back to Public Portal --}}
            <a href="{{ url('/') }}" class="canva-nav-back">
                <i class="mdi mdi-arrow-left"></i> Back to Public Portal
            </a>

            {{-- Card Header --}}
            <div class="canva-card-head">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-2 py-1 font-mono" style="background: #f5f3ff; color: #7d2ae8; border: 1px solid #ede9fe; font-size: 10.5px;">
                        <i class="mdi mdi-shield-check-outline me-1"></i>SECURE GATEWAY
                    </span>
                </div>
                <h2>Portal Sign In</h2>
                <p>Enter your institutional credentials or authenticate via Google Workspace SSO.</p>
            </div>

            {{-- Flash Session Alerts --}}
            @if(session('status'))
                <div class="canva-alert canva-alert-success">
                    <i class="mdi mdi-check-circle fs-5 flex-shrink-0"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="canva-alert canva-alert-danger">
                    <i class="mdi mdi-alert-circle fs-5 flex-shrink-0"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            {{-- Clean Login Form --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Email Input --}}
                <div class="mb-3">
                    <label class="canva-label" for="email">Institutional Email</label>
                    <div class="canva-input-group">
                        <i class="mdi mdi-email-outline input-icon"></i>
                        <input
                            type="email"
                            class="canva-input @error('email') is-invalid @enderror"
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

                {{-- Password Input --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="canva-label mb-0" for="password">Password</label>
                        <a href="{{ route('password.request') }}" class="canva-forgot-link">Forgot password?</a>
                    </div>
                    <div class="canva-input-group">
                        <i class="mdi mdi-lock-outline input-icon"></i>
                        <input
                            type="password"
                            class="canva-input"
                            id="password"
                            name="password"
                            value=""
                            required
                            placeholder="Enter your account password"
                            autocomplete="current-password"
                        >
                        <button type="button" class="canva-pwd-toggle" onclick="togglePassword()" id="togglePwdBtn" aria-label="Toggle password visibility">
                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="canva-form-meta">
                    <div class="canva-checkbox-wrap">
                        <input type="checkbox" class="canva-checkbox" id="remember" name="remember" checked>
                        <label class="canva-checkbox-label" for="remember">Keep me signed in</label>
                    </div>
                </div>

                {{-- Primary Sign In CTA Button --}}
                <button type="submit" class="canva-btn-primary" id="signinBtn">
                    <i class="mdi mdi-login fs-5"></i>
                    <span>Sign In to Workspace</span>
                </button>

                @if(config('services.google.client_id'))
                    {{-- Divider --}}
                    <div class="canva-divider">or continue with sso</div>

                    {{-- Google Workspace SSO (only when configured) --}}
                    <a href="{{ route('auth.google') }}" class="canva-btn-google" id="googleSsoBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Sign in with Google Workspace</span>
                    </a>
                @else
                    {{-- SSO Not Configured Notice --}}
                    <div class="canva-divider">or</div>
                    <div class="d-flex align-items-start gap-2 px-1 py-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <i class="mdi mdi-information-outline mt-1 flex-shrink-0" style="color: #94a3b8; font-size: 1rem;"></i>
                        <p class="mb-0" style="font-size: 11.5px; color: #64748b; line-height: 1.5;">
                            Google Workspace SSO is not configured in <code style="font-size: 10.5px; background: #f1f5f9; padding: 1px 4px; border-radius: 4px;">.env</code> yet.
                            Please sign in with your <strong>institutional email and password</strong> above.
                        </p>
                    </div>
                @endif

                {{-- Security Compliance Note --}}
                <div class="canva-card-footer">
                    Protected by <strong>Multi-Tenant Sovereign Data Isolation</strong> &bull; RIE Extension System
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    // Interactive button loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('signinBtn');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin fs-5"></i><span>Authenticating…</span>';
        btn.disabled = true;
    });
</script>
</body>
</html>
