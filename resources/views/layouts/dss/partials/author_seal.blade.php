{{-- ============================================================================
     OFFICIAL SYSTEM PROVENANCE & AUTHORSHIP CERTIFICATE (TAMPER-EVIDENT SEAL)
     Lead Systems Architect & Full-Stack Engineer: Antonio Piloton Jr.
     Protected by Philippine Intellectual Property Code (RA 8293)
     ============================================================================ --}}
<div class="modal fade" id="systemProvenanceModal" tabindex="-1" aria-hidden="true" style="z-index: 10999;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: #ffffff;">
            {{-- Top Gold/Emerald Ornamental Accent Bar --}}
            <div style="height: 6px; background: linear-gradient(90deg, #059669 0%, #10b981 35%, #d97706 70%, #f59e0b 100%);"></div>

            <div class="modal-body p-4 p-md-5">
                {{-- Header with Guilloche / Crest Style Badge --}}
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 p-1"
                         style="width: 76px; height: 76px; background: linear-gradient(135deg, #ecfdf5 0%, #fef3c7 100%); border: 2px solid #bbf7d0; box-shadow: 0 8px 24px rgba(5, 150, 105, 0.18);">
                        <i class="mdi mdi-certificate-outline fs-1 text-success"></i>
                    </div>
                    <div class="extra-small text-uppercase fw-bold text-success font-mono" style="letter-spacing: 0.12em;">
                        Certificate of System Provenance &amp; Authorship
                    </div>
                    <h3 class="fw-extrabold text-dark mt-1 mb-1" style="letter-spacing: -0.02em;">
                        RIE-EXTEND PLATFORM
                    </h3>
                    <p class="text-muted extra-small mb-0">
                        Research, Innovation &amp; Extension Institutional Management Engine
                    </p>
                </div>

                {{-- Author Information Box --}}
                <div class="p-3.5 rounded-3 mb-3.5 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="extra-small text-muted text-uppercase fw-bold">Lead Systems Architect &amp; Developer</span>
                        <span class="badge bg-success text-white font-mono" style="font-size: 9.5px; padding: 2px 6px;">
                            <i class="mdi mdi-check-decagram me-0.5"></i>VERIFIED AUTHOR
                        </span>
                    </div>
                    <div class="fs-4 fw-extrabold text-dark mb-0.5" style="letter-spacing: -0.02em;">
                        Antonio Piloton Jr.
                    </div>
                    <div class="text-secondary extra-small fw-semibold">
                        Lead Systems Architect &bull; Full-Stack Software Engineer
                    </div>
                </div>

                {{-- Cryptographic & Licensing Matrix --}}
                <div class="p-3 rounded-3 border bg-white mb-3.5" style="font-size: 11.5px;">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Initial Release Year:</span>
                        <span class="fw-bold font-mono text-dark">2026</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Engine Core:</span>
                        <span class="fw-bold font-mono text-dark">RIE-EXTEND v2.6-LTS</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Authorship Protection:</span>
                        <span class="fw-bold text-dark">Republic Act No. 8293 (IP Code of the Philippines)</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted fw-semibold">System Fingerprint:</span>
                        <span class="fw-bold font-mono text-success" style="font-size: 10px;">SHA256-APJ-RIE-EXTEND-2026</span>
                    </div>
                </div>

                <div class="p-3 rounded-3 border bg-light text-muted extra-small mb-4" style="line-height: 1.5; font-size: 11px;">
                    <strong>Institutional Grant Notice:</strong> This software platform and its underlying codebase are turned over for sovereign institutional deployment. In accordance with moral and intellectual property rights, original authorship credits and architectural attribution remain the perpetual intellectual property of <strong>Antonio Piloton Jr.</strong>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Close Certificate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        // 1. Permanent Browser DevTools Console Watermark
        const titleStyle = 'background: #047857; color: #ffffff; font-size: 14px; font-weight: 800; padding: 4px 10px; border-radius: 4px;';
        const authorStyle = 'color: #059669; font-size: 12px; font-weight: 700;';
        const subStyle = 'color: #64748b; font-size: 11px;';
        console.log('%c RIE-EXTEND PLATFORM ', titleStyle);
        console.log('%c Lead Systems Architect & Developer: Antonio Piloton Jr. ', authorStyle);
        console.log('%c Provenance Fingerprint: SHA256-APJ-RIE-EXTEND-SECURE-2026 \u2022 Protected by Intellectual Property Code of the Philippines (RA 8293) ', subStyle);

        // 2. Secret Key Combination: Ctrl + Shift + Alt + A (or Cmd + Shift + Alt + A on Mac)
        window.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.altKey && (e.key === 'A' || e.key === 'a')) {
                e.preventDefault();
                window.showSystemProvenanceModal();
            }
        });

        // 3. Multi-Click Easter Egg: 5 rapid clicks on any navbar logo or brand title
        let clickCount = 0;
        let clickTimer = null;
        document.addEventListener('click', function (e) {
            const target = e.target.closest('.auth-logo-wrap, .brand-title, .dss-sidebar-header, .navbar-brand');
            if (target) {
                clickCount++;
                clearTimeout(clickTimer);
                if (clickCount >= 5) {
                    clickCount = 0;
                    window.showSystemProvenanceModal();
                } else {
                    clickTimer = setTimeout(() => { clickCount = 0; }, 1600);
                }
            }
        });

        window.showSystemProvenanceModal = function () {
            const modalEl = document.getElementById('systemProvenanceModal');
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        };
    })();
</script>

