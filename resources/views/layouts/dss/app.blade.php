<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Surigao del Norte State University — RIE Extension Management System')</title>

    <!-- Local Bootstrap 5 CSS & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Dynamic Favicon & PWA Icons from White-label Suite -->
    @php
        $appInst = \App\Services\DSS\TenantContext::getInstitution();
        $faviconUrl = $appInst->logo_variants['favicon']['url'] ?? ($appInst->logo_path ? asset($appInst->logo_path) : null);
        $appIconUrl = $appInst->logo_variants['app_icon']['url'] ?? ($appInst->logo_path ? asset($appInst->logo_path) : null);
    @endphp
    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    @endif
    @if($appIconUrl)
        <link rel="apple-touch-icon" sizes="192x192" href="{{ $appIconUrl }}">
    @endif

    <!-- Dynamic SaaS Theme Engine -->
    @include('layouts.dss.theme-styles')

    <!-- Local Select2 CSS -->
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">

    <!-- Local Core jQuery & Select2 JS (Loaded in HEAD to support inline view scripts) -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>

    @yield('styles')
    @stack('styles')

    @php
        $currentThemeObj = \App\Services\DSS\TenantContext::getTheme();
        $activeUiTemplate = $currentThemeObj->ui_template ?? 'flup_minimal';
        $rawTitle = trim($__env->yieldContent('title', 'Dashboard'));
        $cleanPageTitle = html_entity_decode(trim(explode('—', $rawTitle)[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($cleanPageTitle === '' || str_contains(strtolower($cleanPageTitle), 'decision intelligence')) {
            $cleanPageTitle = 'Dashboard';
        }
    @endphp
</head>
<body data-ui-template="{{ $activeUiTemplate }}">
    @if($activeUiTemplate === 'material_able')
        <!-- Material Able Fixed Full-Width Topbar -->
        @include('layouts.dss.navbar')
    @endif

    <div class="dss-app-container">
        <!-- Dynamic Multi-Template Sidebar -->
        @include('layouts.dss.sidebar')

        <!-- Main Content Shell -->
        <div class="dss-main-content">
            @if($activeUiTemplate === 'material_able')
                <!-- Material Able Geometric Page Header Banner -->
                <div class="ma-page-header">
                    <div>
                        <h4 class="ma-page-header-title text-white mb-1">{{ $cleanPageTitle }}</h4>
                        <p class="ma-page-header-subtitle mb-0" style="color: rgba(255,255,255,0.85);">Welcome to {{ $appInst->school_acronym ?: 'SNSU' }} Extension Management System</p>
                    </div>
                    <ul class="d-flex align-items-center gap-2 list-unstyled m-0 p-0 text-white small">
                        <li><a href="{{ route('dss.dashboard') }}" class="text-white text-decoration-none"><i class="mdi mdi-home"></i></a></li>
                        <li><i class="mdi mdi-chevron-right"></i></li>
                        <li>{{ $cleanPageTitle }}</li>
                    </ul>
                </div>
            @else
                <!-- Flup Minimal / Print Manzil Inline or Detached Topbar -->
                @include('layouts.dss.navbar')
            @endif

            <!-- Page Body Content -->
            <main class="dss-page-content flex-grow-1">
                @if(session('status') || session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs mb-4 d-flex align-items-center gap-3 p-3 rounded-3" style="background-color: #ecfdf5; border: 1px solid #a7f3d0 !important; color: #065f46;" role="alert">
                        <div class="rounded-circle p-1 bg-success text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; min-width: 24px;">
                            <i class="mdi mdi-check fs-6"></i>
                        </div>
                        <div class="fw-semibold small flex-grow-1">
                            {{ session('status') ?: session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs mb-4 d-flex align-items-center gap-3 p-3 rounded-3" style="background-color: #fef2f2; border: 1px solid #fecaca !important; color: #991b1b;" role="alert">
                        <div class="rounded-circle p-1 bg-danger text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; min-width: 24px;">
                            <i class="mdi mdi-alert-circle fs-6"></i>
                        </div>
                        <div class="fw-semibold small flex-grow-1">{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-xs mb-4 d-flex align-items-center gap-3 p-3 rounded-3" style="background-color: #fffbeb; border: 1px solid #fde68a !important; color: #92400e;" role="alert">
                        <div class="rounded-circle p-1 bg-warning text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; min-width: 24px;">
                            <i class="mdi mdi-alert fs-6"></i>
                        </div>
                        <div class="fw-semibold small flex-grow-1">{{ session('warning') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Local Core Scripts -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chartjs/chart.umd.js') }}"></script>

    <script>
        // Setup CSRF header for all fetch/ajax calls
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // Material Able / Flup / Print Manzil Sidebar & Fullscreen Toggles
        window.toggleMaSidebar = function() {
            if (window.innerWidth < 992) {
                document.body.classList.toggle('ma-sidebar-mobile-open');
            } else {
                document.body.classList.toggle('ma-sidebar-collapsed');
            }
        };

        // 1-Click Database-Backed UI Layout Template Switcher
        window.switchGlobalUiTemplate = function(templateKey) {
            fetch('{{ route('dss.settings.theme.ui_template') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ ui_template: templateKey })
            })
            .then(res => res.json())
            .then(() => {
                window.location.reload();
            })
            .catch(() => {
                window.location.reload();
            });
        };

        window.toggleMaFullscreen = function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        };

        window.filterMaSidebar = function(query) {
            const q = String(query || '').toLowerCase().trim();
            const container = document.getElementById('maSidebarNavContainer');
            if (!container) return;
            container.querySelectorAll('.dss-nav-link, .dss-subnav-link').forEach(link => {
                const text = (link.textContent || '').toLowerCase();
                link.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        };

        // Global Reusable customConfirm (matching uisfix standard)
        window.customConfirm = function(options) {
            options = options || {};
            const isDanger = options.type === 'danger' || options.isDanger;
            return Swal.fire({
                title: options.title || 'Are you sure?',
                text: options.text || options.message || 'Please confirm to proceed.',
                icon: options.icon || (isDanger ? 'warning' : 'question'),
                showCancelButton: true,
                confirmButtonText: options.confirmButtonText || options.confirmText || (isDanger ? 'Yes, Proceed' : 'Confirm'),
                cancelButtonText: options.cancelButtonText || options.cancelText || 'Cancel',
                confirmButtonColor: options.confirmButtonColor || (isDanger ? '#ef4444' : 'var(--dss-primary, #059669)'),
                cancelButtonColor: options.cancelButtonColor || '#64748b',
                customClass: {
                    confirmButton: isDanger ? 'btn-danger' : ''
                }
            }).then(result => {
                if (result.isConfirmed) {
                    if (typeof options.onConfirm === 'function') {
                        return options.onConfirm();
                    }
                    return true;
                }
                return false;
            });
        };

        // Global Executive Stacking Toast Manager (Native Top-Right Stack)
        window.dssToast = {
            show: function(opts) {
                opts = opts || {};
                let container = document.getElementById('dss-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'dss-toast-container';
                    container.className = 'dss-toast-container';
                    document.body.appendChild(container);
                }

                const type = opts.icon || 'info';
                const colors = {
                    success: 'var(--dss-primary, #059669)',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#0284c7'
                };
                const icons = {
                    success: 'mdi-check-circle',
                    error: 'mdi-alert-circle',
                    warning: 'mdi-alert',
                    info: 'mdi-information'
                };
                const accent = colors[type] || colors.info;
                const iconClass = icons[type] || icons.info;

                const toast = document.createElement('div');
                toast.className = 'dss-stacked-toast';
                toast.style.setProperty('--toast-accent', accent);

                const duration = opts.timer || opts.duration || 3200;

                toast.innerHTML = `
                    <div class="dss-toast-icon-badge">
                        <i class="mdi ${iconClass}"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="dss-toast-title">${opts.title || ''}</div>
                        ${opts.text ? `<div class="dss-toast-desc">${opts.text}</div>` : ''}
                    </div>
                    <button type="button" class="dss-toast-close" title="Dismiss">&times;</button>
                    <div class="dss-toast-bar" style="transition: width ${duration}ms linear;"></div>
                `;

                const closeBtn = toast.querySelector('.dss-toast-close');
                const progressBar = toast.querySelector('.dss-toast-bar');

                const dismiss = () => {
                    toast.classList.add('dss-toast-hiding');
                    setTimeout(() => {
                        if (toast.parentNode) toast.parentNode.removeChild(toast);
                    }, 240);
                };

                closeBtn.onclick = dismiss;
                container.appendChild(toast);

                requestAnimationFrame(() => {
                    if (progressBar) progressBar.style.width = '0%';
                });

                if (duration > 0) {
                    setTimeout(dismiss, duration);
                }

                return toast;
            },
            success: function(title, text, duration) { return this.show({ icon: 'success', title: title, text: text, timer: duration }); },
            error: function(title, text, duration) { return this.show({ icon: 'error', title: title, text: text, timer: duration }); },
            warning: function(title, text, duration) { return this.show({ icon: 'warning', title: title, text: text, timer: duration }); },
            info: function(title, text, duration) { return this.show({ icon: 'info', title: title, text: text, timer: duration }); }
        };

        window.appToast = window.dssToast;

        // Universal Safe Clipboard Copy (Works on HTTP & HTTPS, never throws TypeError)
        window.dssCopyToClipboard = function(text, successMsg) {
            if (!text && text !== 0) return Promise.resolve(false);
            text = String(text);
            successMsg = successMsg || 'Copied to clipboard.';

            const fallbackCopy = function(str, msg) {
                try {
                    const ta = document.createElement('textarea');
                    ta.value = str;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    ta.style.top = '-9999px';
                    ta.setAttribute('readonly', '');
                    document.body.appendChild(ta);
                    ta.focus();
                    ta.select();
                    const successful = document.execCommand('copy');
                    document.body.removeChild(ta);
                    if (successful) {
                        window.dssToast.success('Copied', msg);
                        return Promise.resolve(true);
                    }
                } catch (err) {
                    console.error('Fallback copy failed: ', err);
                }
                window.dssToast.warning('Copy Notice', 'Please copy manually.');
                return Promise.resolve(false);
            };

            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text).then(function() {
                    window.dssToast.success('Copied', successMsg);
                    return true;
                }).catch(function() {
                    return fallbackCopy(text, successMsg);
                });
            }
            return fallbackCopy(text, successMsg);
        };

        // Intercept any Swal.fire({ toast: true }) so ALL toasts throughout the system automatically stack cleanly!
        if (window.Swal) {
            const origSwalFire = window.Swal.fire;
            window.Swal.fire = function(...args) {
                if (args[0] && typeof args[0] === 'object' && args[0].toast === true) {
                    return Promise.resolve(window.dssToast.show(args[0]));
                }
                return origSwalFire.apply(this, args);
            };
        }

        // Universal Auto-Select2 Initializer across IIPM-DSS
        window.initSelect2 = function(context) {
            context = context || document;
            if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
                return;
            }

            $(context).find('select').each(function() {
                const $el = $(this);
                // Skip if already initialized, opted out, or part of navbar, datatables, or input-group
                if ($el.hasClass('select2-hidden-accessible') || 
                    $el.data('no-select2') || 
                    $el.is('#dss-fy-select') ||
                    $el.closest('.dss-navbar').length > 0 || 
                    $el.closest('.dataTables_wrapper').length > 0 ||
                    $el.closest('.input-group').length > 0) {
                    return;
                }

                const $modal = $el.closest('.modal');
                const hasEmptyOpt = $el.find('option[value=""]').length > 0;
                const placeholder = $el.attr('placeholder') || $el.data('placeholder') || (hasEmptyOpt ? $el.find('option[value=""]').text() : null);

                const opts = {
                    theme: 'bootstrap-5',
                    width: $el.data('width') || ($el.hasClass('w-100') ? '100%' : '100%'),
                    placeholder: placeholder,
                    allowClear: Boolean($el.data('allow-clear') && hasEmptyOpt),
                    dropdownAutoWidth: true,
                };

                if ($modal.length > 0) {
                    opts.dropdownParent = $modal;
                }

                $el.select2(opts);
            });
        };

        $(document).ready(function() {
            window.initSelect2(document);
        });

        // Auto-rebind Select2 whenever any Bootstrap modal is shown
        $(document).on('shown.bs.modal', function(e) {
            window.initSelect2(e.target);
        });

        // Global DataTables Dropdown Popout Handler: ensures table containers never clip dropdown menus across all pages
        $(document).on('show.bs.dropdown', '.table-responsive .dropdown, table .dropdown', function () {
            $(this).closest('.table-responsive').addClass('dropdown-open').css('overflow', 'visible');
            $(this).closest('.dss-card, .card').css('overflow', 'visible');
        });
        $(document).on('hidden.bs.dropdown', '.table-responsive .dropdown, table .dropdown', function () {
            var $container = $(this).closest('.table-responsive');
            if ($container.find('.dropdown-menu.show').length === 0) {
                $container.removeClass('dropdown-open').css('overflow', 'auto');
            }
        });
    </script>
    <style>
        /* Custom Select2 Bootstrap-5 Theme Polish for DSS Executive Standard */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem;
            font-family: inherit;
            border-color: #cbd5e1;
            border-radius: 0.375rem;
            min-height: calc(1.5em + 0.75rem + 2px);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--dss-primary, #0f172a);
            box-shadow: 0 0 0 0.2rem rgba(15, 23, 42, 0.12);
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            z-index: 1065;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: var(--dss-primary, #0f172a);
            color: #ffffff;
        }
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #f1f5f9;
            color: var(--dss-primary, #0f172a);
            font-weight: 600;
        }
    </style>
    <script src="{{ asset('js/dss/live_notifications.js') }}"></script>

    @yield('scripts')
    @stack('scripts')

    <!-- Official System Provenance & Authorship Seal -->
    @include('layouts.dss.partials.author_seal')
</body>
</html>
