<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | {{ config('app.name', 'Institutional Repository') }}</title>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --repo-primary: #00674f;
            --repo-primary-dark: #004d3b;
            --repo-border: #e2e8f0;
        }

        html, body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 10% 15%, rgba(0, 103, 79, 0.05) 0px, transparent 50%),
                radial-gradient(at 90% 85%, rgba(100, 116, 139, 0.05) 0px, transparent 50%);
            color: #0f172a;
            padding: 16px;
        }

        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }

        .compact-error-card {
            background: #ffffff;
            border: 1px solid var(--repo-border);
            border-top: 4px solid var(--repo-primary);
            border-radius: 14px;
            max-width: 480px;
            width: 100%;
            padding: 24px 28px;
            box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.08), 0 2px 8px rgba(0, 0, 0, 0.02);
            text-align: center;
        }

        .btn-repo-primary {
            background: var(--repo-primary);
            color: #ffffff;
            border: 1px solid var(--repo-primary);
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .btn-repo-primary:hover {
            background: var(--repo-primary-dark);
            border-color: var(--repo-primary-dark);
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="compact-error-card">
    <!-- Top Identity Tag -->
    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
        <span class="badge bg-light text-secondary border font-mono" style="font-size: 10px;">
            <i class="mdi mdi-shield-check text-success me-1"></i> INSTITUTIONAL PORTAL
        </span>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono" style="font-size: 10px;">
            HTTP 404 &bull; NOT FOUND
        </span>
    </div>

    <!-- Error Icon -->
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger mb-3" style="width: 52px; height: 52px; font-size: 26px; border: 1px solid #fecaca;">
        <i class="mdi mdi-file-question-outline"></i>
    </div>

    <!-- Title & Description -->
    <h5 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Page Not Found</h5>
    <p class="text-secondary extra-small mb-3" style="line-height: 1.5;">
        The page or resource you are looking for does not exist or may have been relocated.
    </p>

    <!-- Navigation Actions -->
    <div class="d-flex align-items-center justify-content-center gap-2 pt-2 border-top">
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold extra-small" onclick="window.history.back()">
            <i class="mdi mdi-arrow-left me-1"></i>Back
        </button>
        @auth
            <a href="{{ route('dss.documents.repository') }}" class="btn btn-sm btn-repo-primary rounded-pill px-3 py-1 extra-small">
                <i class="mdi mdi-folder-multiple-outline me-1"></i>Repository
            </a>
            <a href="{{ route('dss.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold extra-small">
                Dashboard
            </a>
        @else
            <a href="{{ url('/') }}" class="btn btn-sm btn-repo-primary rounded-pill px-3 py-1 extra-small">
                <i class="mdi mdi-home-outline me-1"></i>Return Home
            </a>
        @endauth
    </div>
</div>

</body>
</html>
