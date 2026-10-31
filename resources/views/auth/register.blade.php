<!DOCTYPE html>
@php
    $institution = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
    $theme = $theme ?? \App\Services\DSS\TenantContext::getTheme();
    $activeUiTemplate = $theme->ui_template ?? 'flup_minimal';
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institutional Tenant Registration — RIE System</title>

    <!-- Local Bootstrap 5 CSS & Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Dynamic Theme Styles -->
    @include('layouts.dss.theme-styles')

    <style>
        .register-container {
            min-height: 100vh;
            background: radial-gradient(circle at 12% 20%, color-mix(in srgb, var(--dss-primary, #047857) 8%, transparent) 0%, #f8fafc 60%, color-mix(in srgb, var(--dss-secondary, #0284c7) 6%, transparent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }
        body[data-ui-template="material_able"] .register-container {
            background: linear-gradient(145deg, var(--dss-primary, #047857) 0%, color-mix(in srgb, var(--dss-primary, #047857) 65%, #0f172a) 100%);
        }

        .register-card {
            background: var(--dss-card-bg, #ffffff);
            border-radius: var(--dss-card-radius, 16px);
            border: 1px solid var(--dss-card-border, #e2e8f0);
            border-top: 4px solid var(--dss-primary, #047857);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.06), 0 8px 12px -4px rgba(0, 0, 0, 0.03);
            width: 100%;
            max-width: 820px;
            overflow: hidden;
        }
    </style>
</head>
<body data-ui-template="{{ $activeUiTemplate }}">
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="p-4 bg-light bg-opacity-50 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-dss-primary-soft text-dss-primary border border-dss-primary-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="mdi mdi-school fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="letter-spacing: -0.02em;">Register Institutional Tenant</h5>
                        <div class="extra-small text-muted fw-semibold">IIPM-DSS Multi-Tenant Framework</div>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                    <i class="mdi mdi-arrow-left me-1"></i>Back to Sign In
                </a>
            </div>

            <!-- Form Body -->
            <div class="p-4 p-md-5">
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-xs mb-4 py-2 px-3 small">
                        <i class="mdi mdi-alert-circle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="mdi mdi-office-building text-dss-primary me-2"></i>1. Institution Profile & Identity
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="institution_name">Official University / College Name</label>
                            <input type="text" class="form-control" id="institution_name" name="institution_name" value="{{ old('institution_name', 'Agusan del Sur State College of Agriculture and Technology') }}" required placeholder="e.g. State University / College">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="school_acronym">School Acronym</label>
                            <input type="text" class="form-control text-uppercase" id="school_acronym" name="school_acronym" value="{{ old('school_acronym', 'ASSCAT') }}" required placeholder="e.g. ASSCAT">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="school_type">Institution Classification</label>
                            <select class="form-select" id="school_type" name="school_type">
                                <option value="State University and College (SUC)" selected>State University and College (SUC)</option>
                                <option value="Local University and College (LUC)">Local University and College (LUC)</option>
                                <option value="Private Higher Education Institution (PHEI)">Private Higher Education Institution (PHEI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="campus_name">Main / Initial Campus Name</label>
                            <input type="text" class="form-control" id="campus_name" name="campus_name" value="{{ old('campus_name', 'Main Campus (Bunawan)') }}" required placeholder="e.g. Main Campus">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="mdi mdi-account-tie text-dss-primary me-2"></i>2. Executive Super Administrator
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', 'Dr. Roberto M. Reyes') }}" required placeholder="e.g. Dr. Jane Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="email">Institutional Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', 'admin@asscat.edu.ph') }}" required placeholder="name@school.edu.ph">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="password">System Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="extra-small text-muted">
                            By registering, you initialize a sovereign multi-tenant database sandbox with default AHP & TOPSIS matrices.
                        </div>
                        <button type="submit" class="btn btn-dss-primary px-4 py-2 fw-bold">
                            <i class="mdi mdi-check-decagram me-1"></i>Complete Registration & Enter Workspace
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
