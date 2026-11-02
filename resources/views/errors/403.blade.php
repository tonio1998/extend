<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Restricted | Institutional Asset Vault</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --repo-primary: #00674f;
            --repo-primary-dark: #004d3b;
            --repo-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 15% 15%, rgba(220, 38, 38, 0.05) 0px, transparent 55%),
                radial-gradient(at 85% 85%, rgba(0, 103, 79, 0.05) 0px, transparent 55%);
            color: #0f172a;
            margin: 0;
            padding: 24px;
        }

        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }

        .error-card {
            background: #ffffff;
            border: 1px solid var(--repo-border);
            border-top: 4px solid #dc2626;
            border-radius: 16px;
            max-width: 580px;
            width: 100%;
            padding: 36px 32px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03);
            text-align: center;
            position: relative;
        }

        .error-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef2f2;
            border: 2px solid #fecaca;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 20px;
            box-shadow: 0 8px 16px rgba(220, 38, 38, 0.12);
        }

        .error-code-badge {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            padding: 4px 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            margin-bottom: 16px;
        }

        .diagnostic-box {
            background: #f8fafc;
            border: 1px solid var(--repo-border);
            border-radius: 10px;
            padding: 14px 16px;
            text-align: left;
            margin-top: 20px;
            margin-bottom: 24px;
            font-size: 12px;
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
            box-shadow: 0 4px 12px rgba(0, 103, 79, 0.2);
        }
    </style>
</head>
<body>

<div class="error-card">
    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
        <span class="badge bg-light text-secondary border font-mono" style="font-size: 10px;">
            <i class="mdi mdi-shield-lock-outline me-1"></i> ACCESS GOVERNANCE
        </span>
        <span class="text-muted extra-small">&bull;</span>
        <span class="extra-small text-muted fw-semibold">Surigao del Norte State University</span>
    </div>

    <div class="error-icon-wrap">
        <i class="mdi mdi-shield-alert-outline"></i>
    </div>

    <div>
        <span class="error-code-badge font-mono">
            <i class="mdi mdi-lock-outline"></i>
            <span>HTTP 403 &bull; RESTRICTED PERMISSION</span>
        </span>
    </div>

    <h4 class="fw-bold text-dark mb-2" style="letter-spacing: -0.02em;">
        Access Restricted or Unauthorized
    </h4>

    <p class="text-secondary small mb-3" style="line-height: 1.6;">
        {{ $exception->getMessage() ?: 'You do not have the required institutional authorization or governance role to view or download this digital asset.' }}
    </p>

    <div class="diagnostic-box">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="text-muted font-mono extra-small text-uppercase fw-bold">Governance Check</span>
            <span class="badge bg-white text-muted border font-mono" style="font-size: 10px;">Security Policy</span>
        </div>
        <div class="text-truncate font-mono text-dark mb-1" style="font-size: 11px;">
            <span class="text-muted">Target Resource:</span> {{ request()->path() }}
        </div>
        <div class="d-flex align-items-center justify-content-between font-mono text-muted extra-small">
            <span>User: <strong class="text-dark">{{ Auth::user()?->name ?: 'Unauthenticated' }}</strong></span>
            <span>Timestamp: <strong class="text-dark">{{ now()->format('M d, Y h:i A') }}</strong></span>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" onclick="window.history.back()">
            <i class="mdi mdi-arrow-left"></i>
            <span>Go Back</span>
        </button>

        <a href="{{ route('dss.documents.repository') }}" class="btn btn-sm btn-repo-primary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
            <i class="mdi mdi-folder-multiple-outline"></i>
            <span>Document Repository</span>
        </a>

        <a href="{{ route('dss.dashboard') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span>DSS Dashboard</span>
        </a>
    </div>
</div>

</body>
</html>

