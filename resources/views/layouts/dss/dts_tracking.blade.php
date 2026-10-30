<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Document Tracking Console — ' . ($institution->school_acronym ?: 'DTS'))</title>

    {{-- Local Material Design Icons, Bootstrap 5.3 & SweetAlert2 --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}">
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

    {{-- Local Select2 & BS5 Theme for server-side searchable dropdowns --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css') }}">

    {{-- Leaflet.js CSS for Campus Coordinates Mapping --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    {{-- School Dynamic Theme Styles --}}
    @include('layouts.dss.theme-styles')

    {{-- Dynamic Favicon from School Branding --}}
    @php
        $inst = $institution ?? \App\Services\DSS\TenantContext::getInstitution();
        $themeSetting = $theme ?? \App\Services\DSS\TenantContext::getTheme();
        $faviconUrl = $inst->logo_variants['favicon']['url'] ?? ($inst->logo_path ? asset($inst->logo_path) : null);
        $navbarLogo = $inst->logo_variants['navbar']['url'] ?? ($inst->logo_path ? asset($inst->logo_path) : null);
    @endphp
    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    @endif

    <style>
        /* Strictly ONE single font everywhere from the school theme */
        *, html, body, input, select, textarea, button, span, p, h1, h2, h3, h4, h5, h6, pre, code, .badge, .btn {
            font-family: var(--dss-font, 'Inter', -apple-system, sans-serif) !important;
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* School-Themed Dedicated DTS Topbar */
        .dts-school-topbar {
            background: linear-gradient(135deg, color-mix(in srgb, var(--dss-primary) 70%, black) 0%, var(--dss-primary) 100%) !important;
            border-bottom: 3px solid var(--dss-accent, #f59e0b) !important;
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1040;
            padding: 10px 24px;
        }

        .dts-search-container {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: var(--dss-radius, 8px);
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .dts-search-container:focus-within {
            background: #ffffff;
            border-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.35);
        }
        .dts-search-container input {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 14px;
        }
        .dts-search-container:focus-within input {
            color: #0f172a;
        }
        .dts-search-container input::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }
        .dts-search-container:focus-within input::placeholder {
            color: #94a3b8;
        }

        .btn-theme-accent {
            background: var(--dss-accent, #f59e0b) !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 700 !important;
            transition: all 0.2s ease;
        }
        .btn-theme-accent:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-theme-primary {
            background: var(--dss-primary) !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }
        .btn-theme-primary:hover {
            background: var(--dss-primary-hover) !important;
            color: #ffffff !important;
        }

        /* DTS School Card */
        .dts-theme-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--dss-radius, 10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .stat-box-theme {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--dss-radius, 8px);
            padding: 12px 10px;
            text-align: center;
            transition: all 0.18s ease;
        }
        .stat-box-theme:hover {
            background: #ffffff;
            border-color: var(--dss-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Pulsing Telemetry Dot */
        .telemetry-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--dss-accent, #f59e0b);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
        }

        .extra-small {
            font-size: 0.76rem;
        }

        /* ── Select2 DTS Theme Override ──────────────────────────── */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 10px;
            font-size: .875rem;
            border: 1px solid #ced4da;
            border-radius: var(--dss-radius, 8px);
            display: flex;
            align-items: center;
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--dss-primary);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--dss-primary) 25%, transparent);
            outline: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #0f172a;
            font-weight: 600;
            font-size: .875rem;
            padding-left: 2px;
            padding-right: 30px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 8px;
            width: 20px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 4px;
            font-size: 1rem;
            line-height: 1;
            color: #94a3b8;
        }
        .select2-dropdown {
            border: 1px solid var(--dss-primary);
            border-radius: var(--dss-radius, 8px);
            box-shadow: 0 8px 24px -4px rgba(0,0,0,.15);
            font-size: .875rem;
            overflow: hidden;
        }
        .select2-container--default .select2-search--dropdown {
            padding: 8px 10px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0;
            border-radius: calc(var(--dss-radius, 8px) - 2px);
            padding: 6px 10px;
            font-size: .8125rem;
            font-weight: 500;
            height: 34px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: var(--dss-primary);
            color: #fff;
        }
        .select2-container--default .select2-results__option--selected {
            background: color-mix(in srgb, var(--dss-primary) 12%, white);
            color: var(--dss-primary);
            font-weight: 700;
        }
        .select2-results__option {
            padding: 8px 14px;
            font-size: .875rem;
        }
        .select2-results__message {
            padding: 8px 14px;
            color: #94a3b8;
            font-size: .8125rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- School-Themed DTS Topbar --}}
    <header class="dts-school-topbar text-white">
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                {{-- Left: School Brand & Logo --}}
                <a href="{{ route('dss.dts.index') }}" class="d-flex align-items-center gap-3 text-white text-decoration-none flex-shrink-0">
                    @if($navbarLogo)
                        <img src="{{ $navbarLogo }}" alt="{{ $inst->school_acronym }}" style="height:38px; max-width:180px; object-fit:contain;">
                    @else
                        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold shadow-xs"
                             style="width:40px; height:40px; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.4); font-size:16px;">
                            {{ substr($inst->school_acronym ?: 'DTS', 0, 4) }}
                        </div>
                    @endif

                    <div class="d-none d-sm-block">
                        <div class="fw-extrabold text-white d-flex align-items-center gap-1" style="font-size:1.05rem; line-height:1.2;">
                            <span>{{ $inst->school_acronym ?: 'DTS' }} Document Tracking System</span>
                            <span class="telemetry-dot ms-1"></span>
                        </div>
                        <div class="extra-small" style="color:rgba(255,255,255,0.78); font-weight:500;">
                            {{ $inst->school_name ?: 'Integrated Institutional Process Management & Decision Support System' }}
                        </div>
                    </div>
                </a>

                {{-- Center: Live Search Bar with Instant Autocomplete --}}
                <div class="flex-grow-1 mx-lg-3 mx-0 position-relative" style="max-width: 540px;" id="dtsTopSearchWrapper">
                    <form method="GET" action="{{ route('dss.dts.searchQR') }}" class="m-0" id="dtsTopSearchForm">
                        <div class="input-group dts-search-container">
                            <span class="input-group-text bg-transparent border-0 text-white ps-3 pe-2">
                                <i class="mdi mdi-qrcode-scan fs-5"></i>
                            </span>
                            <input autocomplete="off"
                                   placeholder="Search phrase, tracking #, office, directive, file..."
                                   value="{{ request()->query('searchQR', request()->query('q', '')) }}"
                                   type="search"
                                   name="searchQR"
                                   id="dtsTopSearchInput"
                                   class="form-control">
                            <button type="submit" class="btn btn-theme-accent px-4 d-flex align-items-center gap-1">
                                <i class="mdi mdi-magnify"></i> Track
                            </button>
                        </div>
                    </form>

                    {{-- Live Autocomplete Suggestions Floating Dropdown --}}
                    <div id="dtsLiveSuggestions"
                         class="d-none position-absolute w-100 bg-white rounded-3 shadow-lg border mt-1 text-dark"
                         style="z-index:9999; top:100%; left:0; max-height:420px; overflow-y:auto;">
                    </div>
                </div>

                {{-- Right: Quick Navigation Actions --}}
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ route('dss.dts.index') }}"
                       class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 fw-semibold px-3 py-1"
                       style="border-radius:var(--dss-radius, 8px); border-color:rgba(255,255,255,0.3); font-size:13px;">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        <span>Tracking Register</span>
                    </a>

                    <a href="{{ route('dss.dts.create') }}"
                       class="btn btn-sm btn-theme-accent fw-bold px-3 py-1 d-flex align-items-center gap-1 shadow-xs"
                       style="border-radius:var(--dss-radius, 8px); font-size:13px;">
                        <i class="mdi mdi-plus-circle"></i>
                        <span>New Record</span>
                    </a>

                    <a href="{{ route('dss.dashboard') }}"
                       class="btn btn-sm text-white px-2 py-1 d-flex align-items-center shadow-xs"
                       style="background:rgba(255,255,255,0.15); border-radius:var(--dss-radius, 8px);"
                       title="Return to IIPM-DSS Core Hub">
                        <i class="mdi mdi-view-dashboard-outline fs-6"></i>
                    </a>
                </div>

            </div>
        </div>
    </header>

    {{-- Main Container Content with Fullwidth Fluid Layout (Non-crowded) --}}
    <main class="container-fluid px-3 px-md-4 px-xl-5 py-4">
        @yield('content')
    </main>

    {{-- Local Core Scripts --}}
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- Live Autocomplete Search Handler --}}
    <script>
        $(document).ready(function () {
            const $input = $('#dtsTopSearchInput');
            const $dropdown = $('#dtsLiveSuggestions');
            const $wrapper = $('#dtsTopSearchWrapper');
            let searchTimeout = null;

            $input.on('input', function () {
                const query = $(this).val().trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    $dropdown.addClass('d-none').empty();
                    return;
                }

                searchTimeout = setTimeout(function () {
                    $.ajax({
                        url: '{{ route("dss.dts.searchSuggestions") }}',
                        data: { q: query },
                        dataType: 'json',
                        success: function (res) {
                            if (!res.results || !res.results.length) {
                                $dropdown.html(`
                                    <div class="p-3 text-center text-muted extra-small">
                                        <i class="mdi mdi-file-search-outline fs-4 d-block mb-1 text-secondary"></i>
                                        No quick match found for "<strong>${$('<div>').text(query).html()}</strong>".
                                        <div class="mt-1">Press <strong>Enter</strong> for full advanced search.</div>
                                    </div>
                                `).removeClass('d-none');
                                return;
                            }

                            let html = `
                                <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between bg-light">
                                    <span class="extra-small fw-bold text-uppercase text-muted" style="font-size:10px; letter-spacing:1px;">
                                        Quick Match Suggestions (${res.count})
                                    </span>
                                    <span class="badge bg-secondary extra-small">Live</span>
                                </div>
                                <div class="list-group list-group-flush">
                            `;

                            res.results.forEach(function (item) {
                                html += `
                                    <a href="${item.url}" class="list-group-item list-group-item-action p-3 px-3 border-0 border-bottom d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex flex-column text-truncate">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="fw-bold text-dark extra-small">${item.document_id}</span>
                                                <span class="badge bg-light text-secondary border extra-small py-1 px-2">${item.status}</span>
                                                <span class="badge extra-small py-1 px-2" style="background:#f0fdf4; color:#047857; font-size:10px;">${item.doc_type}</span>
                                            </div>
                                            <div class="fw-semibold text-dark text-truncate" style="font-size:13px;">
                                                ${$('<div>').text(item.title).html()}
                                            </div>
                                            <div class="extra-small text-muted text-truncate mt-1">
                                                <i class="mdi mdi-office-building me-1"></i>${item.office} &bull; <span class="text-primary fw-semibold">${item.matched_part}</span>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right text-muted fs-5"></i>
                                    </a>
                                `;
                            });

                            html += `
                                </div>
                                <a href="${res.search_url}" class="d-block p-3 text-center bg-light text-primary fw-bold extra-small text-decoration-none border-top hover-bg-primary-subtle">
                                    <i class="mdi mdi-magnify me-1"></i> View all results &amp; related suggestions for "${$('<div>').text(query).html()}" &rarr;
                                </a>
                            `;

                            $dropdown.html(html).removeClass('d-none');
                        }
                    });
                }, 250);
            });

            // Close on click outside or ESC
            $(document).on('click', function (e) {
                if (!$wrapper.is(e.target) && $wrapper.has(e.target).length === 0) {
                    $dropdown.addClass('d-none');
                }
            });

            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $dropdown.addClass('d-none');
                }
            });
        });
    </script>

    <!-- SweetAlert2 Core Script (Loaded via assets/libs above) -->
    <script>
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

        if (window.Swal) {
            const origSwalFire = window.Swal.fire;
            window.Swal.fire = function(...args) {
                if (args[0] && typeof args[0] === 'object' && args[0].toast === true) {
                    return Promise.resolve(window.dssToast.show(args[0]));
                }
                return origSwalFire.apply(this, args);
            };
        }
    </script>

    @stack('scripts')
</body>
</html>
