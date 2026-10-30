<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — IIPM-DSS Platform</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <!-- Dynamic Theme Styles -->
    @include('layouts.dss.theme-styles')

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, rgba(125, 42, 232, 0.06) 0%, #f8fafc 60%, rgba(0, 196, 204, 0.04) 100%);
            padding: 2rem 1rem;
            font-family: var(--dss-font);
        }

        .auth-box {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            border-top: 3px solid var(--dss-primary, #7d2ae8);
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.06), 0 4px 12px -2px rgba(0,0,0,0.03);
            max-width: 460px;
            width: 100%;
            padding: 2.75rem 2.25rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="text-center mb-4">
                <div class="rounded-3 p-2 bg-dss-primary-soft text-dss-primary border border-dss-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
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

                <button type="submit" class="btn btn-dss-primary w-100 py-2 mb-3 shadow-xs fw-bold">
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
