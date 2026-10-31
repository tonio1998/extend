<!DOCTYPE html>
@php
    $institution = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $theme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $activeUiTemplate = $theme->ui_template ?? 'flup_minimal';
    $schoolAcronym = $institution->school_acronym ?: 'RIE-EXTEND';
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — {{ $schoolAcronym }} RIE System</title>

    <!-- Local Bootstrap 5 CSS & Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <!-- Dynamic Theme Styles -->
    @include('layouts.dss.theme-styles')

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 12% 20%, color-mix(in srgb, var(--dss-primary, #047857) 8%, transparent) 0%, #f8fafc 60%, color-mix(in srgb, var(--dss-secondary, #0284c7) 6%, transparent) 100%);
            padding: 2rem 1rem;
        }
        body[data-ui-template="material_able"] .auth-container {
            background: linear-gradient(145deg, var(--dss-primary, #047857) 0%, color-mix(in srgb, var(--dss-primary, #047857) 65%, #0f172a) 100%);
        }

        .auth-box {
            background: var(--dss-card-bg, #ffffff);
            border-radius: var(--dss-card-radius, 16px);
            border: 1px solid var(--dss-card-border, #e2e8f0);
            border-top: 4px solid var(--dss-primary, #047857);
            box-shadow: 0 12px 32px -6px rgba(15, 23, 42, 0.08);
            max-width: 460px;
            width: 100%;
            padding: 2.5rem 2.25rem;
        }
        body[data-ui-template="print_manzil"] .auth-box {
            border-radius: 24px;
        }
    </style>
</head>
<body data-ui-template="{{ $activeUiTemplate }}">
    <div class="auth-container">
        <div class="auth-box">
            <div class="text-center mb-4">
                <div class="rounded-3 p-2 bg-dss-primary-soft text-dss-primary border d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; border-color: var(--dss-primary-border) !important;">
                    <i class="mdi mdi-lock-reset fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Reset Password</h4>
                <p class="text-muted small">Enter your verified institutional email address to receive password recovery instructions.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted" for="email">Institutional Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted"><i class="mdi mdi-email-outline"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@institution.edu.ph">
                    </div>
                </div>

                <button type="submit" class="btn btn-dss-primary w-100 py-2 mb-3 shadow-xs fw-bold" style="border-radius: var(--dss-btn-radius, 10px);">
                    <i class="mdi mdi-send-outline me-1"></i>Send Password Reset Link
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="small fw-semibold text-decoration-none text-muted">
                        <i class="mdi mdi-arrow-left me-1"></i>Back to Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
