<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Integrity Locked — Developer License Missing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
            background-image:
                radial-gradient(at 10% 20%, rgba(239, 68, 68, 0.05) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
            color: #0f172a;
            padding: 1.5rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .lockout-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 5px solid #dc2626;
            border-radius: 20px;
            max-width: 620px;
            width: 100%;
            padding: 2.75rem 2.5rem;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
            text-align: center;
            position: relative;
        }

        .lock-icon-wrap {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #fef2f2;
            border: 2px solid #fecaca;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.12);
        }

        .alert-tamper-box {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 0.9rem 1.15rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .contact-box {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.35rem 1.25rem;
            text-align: left;
            margin-bottom: 1.75rem;
        }

        .code-box {
            background: #0f172a;
            color: #38bdf8;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
    </style>
</head>
<body>
    <div class="lockout-card">
        <!-- Shield Lock Icon -->
        <div class="lock-icon-wrap">
            <i class="mdi mdi-shield-lock-outline"></i>
        </div>

        <div class="d-inline-flex align-items-center gap-1.5 badge bg-danger-subtle text-danger border border-danger-subtle font-mono text-uppercase px-3 py-1 mb-2.5" style="font-size: 11px;">
            <i class="mdi mdi-alert-circle"></i>
            <span>SYSTEM INTEGRITY LOCKED &bull; LICENSE MISSING</span>
        </div>

        <h3 class="fw-extrabold text-dark mb-2" style="letter-spacing: -0.025em; font-size: 1.75rem;">
            SYSTEM INTEGRITY LOCKED
        </h3>

        <!-- Clear Notice Explaining That a License File Was Deleted -->
        <div class="alert-tamper-box">
            <div class="d-flex align-items-start gap-2.5">
                <i class="mdi mdi-file-remove-outline text-danger fs-4 flex-shrink-0 mt-0.5"></i>
                <div style="font-size: 13px; line-height: 1.55; color: #7f1d1d;">
                    <strong>Notice:</strong> The system detected that <strong>you deleted or modified a license file</strong> (<code style="background: #fee2e2; color: #991b1b; padding: 1px 5px; border-radius: 4px;">LICENSE.md</code>) issued by the original developer.
                    <div class="mt-1" style="color: #991b1b; font-size: 12px;">
                        Under <strong>Republic Act No. 8293 (Intellectual Property Code of the Philippines)</strong>, original developer attribution and license terms are legally protected. The system automatically halted operations to protect intellectual property integrity.
                    </div>
                </div>
            </div>
        </div>

        <!-- Developer Contact & Reactivation Information -->
        <div class="contact-box">
            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
                <span class="extra-small text-uppercase fw-bold text-dark" style="font-size: 11px; letter-spacing: 0.04em;">
                    <i class="mdi mdi-account-key-outline text-primary me-1"></i>Reactivation Authority &amp; Developer
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 10px;">
                    <i class="mdi mdi-check-decagram me-0.5"></i>LEAD ARCHITECT
                </span>
            </div>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #047857 0%, #0284c7 100%); font-size: 16px;">
                    AP
                </div>
                <div>
                    <div class="fw-extrabold text-dark" style="font-size: 15px;">Antonio Jr. L. Piloton</div>
                    <div class="text-secondary small" style="font-size: 12px;">Lead Systems Architect &amp; Full-Stack Engineer</div>
                </div>
            </div>

            <div class="p-2.5 rounded-3 mb-2.5 bg-white border" style="font-size: 12.5px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <span class="text-muted fw-semibold">Developer Email:</span>
                    <a href="mailto:antoniojrpiloton@gmail.com?subject=RIE-EXTEND%20System%20Reactivation%20Authorization%20Request" class="text-primary fw-bold text-decoration-none font-mono">
                        antoniojrpiloton@gmail.com
                    </a>
                </div>
            </div>

            <div class="text-secondary extra-small" style="font-size: 11.5px; line-height: 1.5;">
                <i class="mdi mdi-information-outline text-primary me-1"></i>
                To unlock the platform, please <strong>contact the developer</strong> to obtain the official authorization key and restore the original license, then run:
                <div class="code-box mt-1.5 d-flex align-items-center justify-content-between">
                    <span>php artisan system:unlock</span>
                    <span class="badge bg-secondary font-mono text-white" style="font-size: 9.5px;">TERMINAL</span>
                </div>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <a href="mailto:antoniojrpiloton@gmail.com?subject=RIE-EXTEND%20System%20Reactivation%20Authorization%20Request&body=Good%20day%20Arch.%20Antonio%20Piloton%20Jr.,%0A%0AThe%20license%20file%20was%20removed%20and%20the%20system%20is%20currently%20locked.%20We%20request%20your%20assistance%20and%20the%20Master%20Reactivation%20Key%20to%20restore%20the%20license%20for%20the%20RIE-EXTEND%20System.%0A%0AThank%20you." class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" style="font-size: 13px;">
                <i class="mdi mdi-email-send-outline"></i>
                <span>Email Developer to Unlock</span>
            </a>
            <a href="/" class="btn btn-outline-secondary rounded-pill px-3.5 py-2.5 fw-semibold" style="font-size: 13px;">
                <i class="mdi mdi-refresh me-1"></i>Retry Verification
            </a>
        </div>
    </div>
</body>
</html>
