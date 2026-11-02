<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] }} — Document Viewer — {{ $institution?->school_acronym ?: 'SNSU' }}</title>

    <!-- Bootstrap 5 & Material Design Icons -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @php
        $faviconUrl = $institution?->logo_variants['favicon']['url'] ?? ($institution?->logo_path ? asset($institution->logo_path) : null);
        $primaryColor = $theme?->primary_color ?: '#2563eb';
    @endphp
    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    @endif

    <style>
        :root {
            --primary: {{ $primaryColor }};
            --primary-dark: #1d4ed8;
            --bg-body: #f8fafc;
            --bg-stage: #e2e8f0;
            --bg-surface: #ffffff;
            --border-color: #cbd5e1;
            --border-subtle: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: var(--bg-body);
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* ─── Top Header Bar ─────────────────────────────── */
        .viewer-header {
            height: 58px;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .doc-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .doc-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 480px;
            line-height: 1.2;
        }
        .doc-meta-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-ref {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
        }
        .badge-qr {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        /* ─── Header Action Buttons ──────────────────────── */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            white-space: nowrap;
        }
        .btn-action:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .btn-action.btn-primary-action {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 1px 2px rgba(37,99,235,0.2);
        }
        .btn-action.btn-primary-action:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #ffffff;
        }

        .v-divider {
            width: 1px;
            height: 22px;
            background: var(--border-subtle);
            margin: 0 4px;
        }

        /* ─── Workspace (Sidebar + Canvas) ───────────────── */
        .viewer-workspace {
            display: flex;
            height: calc(100vh - 58px);
            width: 100vw;
            overflow: hidden;
            background: var(--bg-stage);
        }

        /* Sidebar */
        .viewer-sidebar {
            width: 290px;
            min-width: 290px;
            background: var(--bg-surface);
            border-right: 1px solid var(--border-subtle);
            overflow-y: auto;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: margin-left 0.25s ease;
        }
        .viewer-sidebar.collapsed {
            margin-left: -290px;
        }

        .sb-card {
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .sb-card-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .sb-meta-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 7px;
        }
        .sb-meta-label {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }
        .sb-meta-val {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
            margin-top: 1px;
            word-break: break-word;
        }
        .sb-meta-val.mono { font-family: monospace; }

        .ctrl-select {
            width: 100%;
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12px;
            margin-bottom: 8px;
        }
        .ctrl-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37,99,235,0.15);
        }

        .btn-sb-apply {
            width: 100%;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 7px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.15s;
        }
        .btn-sb-apply:hover {
            background: var(--primary-dark);
        }

        /* ─── PDF Canvas Stage (100% Full-Width) ─────────── */
        .viewer-stage {
            flex: 1;
            height: 100%;
            width: 100%;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        .pdf-frame-wrapper {
            width: 100%;
            height: 100%;
            flex: 1;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 16px rgba(15,23,42,0.06);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .pdf-window-header {
            height: 34px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-subtle);
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .window-dots {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .window-dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .window-dots .d-red { background: #ef4444; }
        .window-dots .d-yel { background: #f59e0b; }
        .window-dots .d-grn { background: #22c55e; }

        .window-label {
            font-size: 11px;
            font-family: monospace;
            font-weight: 600;
            color: #64748b;
        }

        #docPdfFrame {
            width: 100%;
            flex: 1;
            height: calc(100% - 34px);
            border: none;
            display: block;
            background: #ffffff;
        }

        /* ─── Loading Overlay ────────────────────────────── */
        .pdf-loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.92);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            z-index: 20;
            backdrop-filter: blur(4px);
        }
        .spinner-ring {
            width: 44px;
            height: 44px;
            border: 3.5px solid rgba(37,99,235,0.15);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text {
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        /* ─── Comments Drawer ───────────────────────────── */
        .comments-drawer {
            width: 380px;
            height: 100%;
            background: #ffffff;
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            box-shadow: -2px 0 12px rgba(15,23,42,0.04);
            z-index: 50;
            overflow: hidden;
            transition: all 0.2s ease-in-out;
        }
        .comments-drawer.collapsed {
            display: none;
        }
        .comments-header {
            height: 52px;
            padding: 0 16px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            flex-shrink: 0;
        }
        .comments-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background-color: #fafbfc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .comments-footer {
            padding: 12px 16px 16px 16px;
            background: #ffffff;
            border-top: 1px solid var(--border-subtle);
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    {{-- ─── Independent Top Header ────────────────────────── --}}
    <header class="viewer-header">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <button class="btn-action" onclick="goBack()" title="Back">
                <i class="mdi mdi-arrow-left"></i>
            </button>

            <div class="doc-icon-box">
                <i class="mdi {{ $meta['icon'] ?? 'mdi-file-document' }}"></i>
            </div>

            <div class="doc-info">
                <div class="doc-title" title="{{ $meta['title'] }}">{{ $meta['title'] }}</div>
                <div class="doc-meta-row">
                    <span>{{ $meta['subtitle'] }}</span>
                    @if(!empty($meta['date']))
                        <span>&bull; {{ $meta['date'] }}</span>
                    @endif
                    @if(!empty($meta['reference']))
                        <span class="badge-ref">{{ $meta['reference'] }}</span>
                    @endif
                    @if(!empty($meta['tracking']))
                        <span class="badge-qr" title="DTS Tracking Code">
                            <i class="mdi mdi-barcode me-1"></i>{{ $meta['tracking'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="header-actions">
            <button class="btn-action" onclick="toggleSidebar()" id="btnToggleSidebar" title="Toggle Options Sidebar">
                <i class="mdi mdi-page-layout-sidebar-left"></i>
                <span id="sbBtnLabel">Options</span>
            </button>

            <button class="btn-action" onclick="reloadPdf()" title="Refresh Document">
                <i class="mdi mdi-refresh"></i>
            </button>

            <button class="btn-action" onclick="printFrame()" title="Print Document">
                <i class="mdi mdi-printer"></i>
                <span>Print</span>
            </button>

            {{-- Electronic Sign / Endorsement Action Button --}}
            @if(!empty($myPendingSignatory))
                <button type="button" class="btn-action shadow-sm" onclick="openSignatureModal()" id="btnAffixSignature"
                        style="background: #ef4444; border-color: #dc2626; color: #ffffff !important; font-weight: 600; padding: 6px 14px; gap: 6px;"
                        title="Affix Electronic Signature & Endorsement">
                    <i class="mdi mdi-draw fs-5"></i>
                    <span>Sign as {{ $myPendingSignatory->role_label }}</span>
                </button>
            @elseif(!empty($mySignedSignatory))
                <button type="button" class="btn-action bg-success-subtle border-success-subtle text-success fw-semibold" onclick="showSignatureDetails()" id="btnSignedBadge"
                        style="font-size: 12px; padding: 6px 12px; gap: 6px;"
                        title="You have electronically signed this document ({{ $mySignedSignatory->signature_hash }})">
                    <i class="mdi mdi-check-decagram fs-5 text-success"></i>
                    <span>Signed ({{ $mySignedSignatory->role_label }})</span>
                </button>
            @endif

            {{-- Download Split Dropdown --}}
            <div class="btn-group" id="downloadGroup" style="display:inline-flex;">
                <a href="{{ $downloadUrl }}" id="btnDownload"
                   class="btn-action btn-primary-action"
                   title="Download PDF with full SNSU letterhead template">
                    <i class="mdi mdi-download"></i>
                    <span>Download</span>
                </a>
                <button type="button"
                        class="btn-action btn-primary-action dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        style="padding: 6px 8px; border-left: 1px solid rgba(255,255,255,0.25);"
                        title="More download options">
                    <i class="mdi mdi-chevron-down" style="font-size:14px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:220px; font-size:13px;">
                    <li>
                        <a class="dropdown-item py-2" href="{{ $downloadUrl }}">
                            <i class="mdi mdi-file-document-outline me-2 text-primary"></i>
                            <strong>With Template</strong><br>
                            <small class="text-muted ms-4">Full SNSU letterhead + content</small>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ $barcodeStampUrl }}">
                            <i class="mdi mdi-barcode me-2 text-success"></i>
                            <strong>Barcode Stamp Only</strong><br>
                            <small class="text-muted ms-4">Tracking code PDF for physical docs</small>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Toggle Comments Drawer Button --}}
            <button class="btn-action" onclick="toggleCommentsDrawer()" id="btnToggleComments" title="View Audit Comments & Revision Feedback">
                <i class="mdi mdi-comment-text-multiple-outline text-primary"></i>
                <span>Comments (<strong id="commentsCountBadge">{{ $commentsCount ?? 0 }}</strong>)</span>
            </button>

            {{-- Toggle Attachments Modal Button --}}
            <button class="btn-action {{ ($physicallySignedCount ?? 0) > 0 ? 'text-danger fw-bold border-danger' : '' }}" onclick="toggleAttachmentsModal()" id="btnToggleAttachments" title="View Attached Files & Physically Signed Scans">
                <i class="mdi {{ ($physicallySignedCount ?? 0) > 0 ? 'mdi-draw-pen text-danger' : 'mdi-paperclip text-primary' }}"></i>
                <span>Attachments (<strong>{{ $attachmentsCount ?? 0 }}</strong>)</span>
                @if(($physicallySignedCount ?? 0) > 0)
                    <span class="badge bg-danger text-white extra-small" style="font-size: 9px; padding: 2px 5px;">Wet-Signed</span>
                @endif
            </button>

            <button class="btn-action" onclick="toggleFullscreen()" title="Fullscreen Mode">
                <i class="mdi mdi-fullscreen"></i>
            </button>

            {{-- Direct link to record in DSS --}}
            @if($type === 'policy')
                <a href="{{ route('dss.policies.show', $id) }}" class="btn-action" title="View Policy in DSS">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            @elseif($type === 'memo')
                <a href="{{ route('dss.memos.show', $id) }}" class="btn-action" title="View Memorandum in DSS">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            @elseif($type === 'dts' && Route::has('dss.dts.show'))
                <a href="{{ route('dss.dts.show', $id) }}" class="btn-action" title="View Routing Slip in DTS">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            @elseif(in_array($type, ['extension_proposal', 'proposal']))
                <a href="{{ route('dss.rie.proposals.show', $id) }}" class="btn-action" title="View Proposal Workbench in RIE">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            @elseif($type === 'extension_plan')
                <a href="{{ route('dss.rie.plans.show', $id) }}" class="btn-action" title="View Plan Workbench in RIE">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            @endif
        </div>
    </header>

    {{-- ─── Workspace (Sidebar + Canvas) ─────────────────── --}}
    <div class="viewer-workspace">

        {{-- Collapsible Options Sidebar --}}
        <aside class="viewer-sidebar" id="viewerSidebar">
            {{-- Document Meta Card --}}
            <div class="sb-card">
                <div class="sb-card-title">
                    <i class="mdi mdi-information-outline"></i> Document Info
                </div>
                <div class="sb-meta-item">
                    <span class="sb-meta-label">Type</span>
                    <span class="sb-meta-val">{{ $meta['subtitle'] }}</span>
                </div>
                @if(!empty($meta['reference']))
                <div class="sb-meta-item">
                    <span class="sb-meta-label">Reference No.</span>
                    <span class="sb-meta-val mono">{{ $meta['reference'] }}</span>
                </div>
                @endif
                @if(!empty($meta['tracking']))
                <div class="sb-meta-item">
                    <span class="sb-meta-label">DTS Tracking ID</span>
                    <span class="sb-meta-val mono">{{ $meta['tracking'] }}</span>
                </div>
                @endif
                @if(!empty($meta['date']))
                <div class="sb-meta-item">
                    <span class="sb-meta-label">Date</span>
                    <span class="sb-meta-val">{{ $meta['date'] }}</span>
                </div>
                @endif
                @if(!empty($meta['status']))
                <div class="sb-meta-item">
                    <span class="sb-meta-label">Status</span>
                    <span class="sb-meta-val">
                        <span class="badge bg-{{ $meta['status_color'] ?? 'primary' }}">{{ $meta['status'] }}</span>
                    </span>
                </div>
                @endif
            </div>

            {{-- Signatory Workflow Card --}}
            @if(isset($signatories) && $signatories->isNotEmpty())
            <div class="sb-card">
                <div class="sb-card-title d-flex align-items-center justify-content-between">
                    <span><i class="mdi mdi-account-check-outline"></i> Signatory Workflow</span>
                    <span class="badge bg-light text-secondary border font-mono" style="font-size: 10px;">
                        {{ $signatories->where('status', 'signed')->count() }}/{{ $signatories->count() }}
                    </span>
                </div>
                <div class="d-flex flex-column gap-2 mt-2">
                    @foreach($signatories as $sig)
                        @php $isSigSigned = $sig->status === 'signed'; @endphp
                        <div class="p-2 rounded-2 border {{ $isSigSigned ? 'bg-success-subtle border-success-subtle' : 'bg-light border-subtle' }}" style="font-size: 11px;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark text-truncate" style="max-width: 140px;">
                                    {{ $sig->role_label }}
                                </span>
                                @if($isSigSigned)
                                    <span class="badge bg-success text-white font-mono" style="font-size: 9px;">
                                        <i class="mdi mdi-check"></i> Signed
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark font-mono" style="font-size: 9px;">
                                        <i class="mdi mdi-clock-outline"></i> Pending
                                    </span>
                                @endif
                            </div>
                            <div class="text-dark fw-semibold text-truncate">
                                {{ $sig->signatory_name ?: ($sig->employee?->full_name ?: ($sig->user?->name ?: 'Authorized Signatory')) }}
                            </div>
                            <div class="text-muted extra-small text-truncate">
                                {{ $sig->designation ?: 'Staff' }}
                            </div>
                            @if($isSigSigned && $sig->signed_at)
                                <div class="text-success extra-small font-mono mt-1" style="font-size: 9.5px;">
                                    {{ $sig->signed_at->format('M d, Y h:i A') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Attachments & Physically Signed Copies Card --}}
            <div class="sb-card">
                <div class="sb-card-title d-flex justify-content-between align-items-center">
                    <span><i class="mdi mdi-paperclip text-primary"></i> Attachments</span>
                    <span class="badge {{ ($attachmentsCount ?? 0) > 0 ? 'bg-primary text-white' : 'bg-light text-muted border' }} font-mono extra-small">{{ $attachmentsCount ?? 0 }}</span>
                </div>
                @if(($physicallySignedCount ?? 0) > 0)
                    <div class="p-2 rounded bg-danger-subtle border border-danger-subtle mb-2">
                        <div class="extra-small fw-bold text-danger d-flex align-items-center gap-1">
                            <i class="mdi mdi-draw-pen"></i> Wet-Signed Copy Attached
                        </div>
                    </div>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-semibold extra-small py-1 rounded-pill" onclick="toggleAttachmentsModal()">
                    <i class="mdi mdi-folder-open-outline me-1"></i> View Attached Files
                </button>
            </div>

            {{-- Template Options Card --}}
            <div class="sb-card">
                <div class="sb-card-title">
                    <i class="mdi mdi-tune"></i> Template Options
                </div>

                <label class="sb-meta-label mb-1">Paper Size</label>
                <select class="ctrl-select" id="ctrlPaper">
                    <option value="A4" {{ request('paper','A4') === 'A4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                    <option value="8.5x13" {{ request('paper') === '8.5x13' ? 'selected' : '' }}>Folio / Long (8.5″ × 13″)</option>
                    <option value="letter" {{ request('paper') === 'letter' ? 'selected' : '' }}>US Letter (8.5″ × 11″)</option>
                </select>

                <label class="sb-meta-label mb-1">Template Mode</label>
                <select class="ctrl-select" id="ctrlTemplate">
                    <option value="executive" {{ request('template','executive') === 'executive' ? 'selected' : '' }}>Generic (University Letterhead)</option>
                    <option value="iso_form" {{ request('template') === 'iso_form' ? 'selected' : '' }}>ISO Template (With Top-Right ISO)</option>
                </select>

                <button class="btn-sb-apply" onclick="applyControls()">
                    <i class="mdi mdi-refresh"></i> Apply &amp; Reload
                </button>
            </div>

            {{-- Template Studio Link --}}
            <div class="sb-card">
                <div class="sb-card-title">
                    <i class="mdi mdi-palette-outline"></i> Template Studio
                </div>
                <p style="font-size: 11px; color: #64748b; margin-bottom: 10px; line-height: 1.4;">
                    Logo placements, header/footer layouts, barcodes, and signatories follow your institutional settings.
                </p>
                <a href="{{ route('dss.settings.institution.template_studio') }}" target="_blank"
                   class="btn-action w-100 justify-content-center">
                    <i class="mdi mdi-open-in-new"></i> Open Template Studio
                </a>
            </div>

            {{-- Footer Notice --}}
            <div style="font-size: 10px; color: #94a3b8; text-align: center; margin-top: auto;">
                <i class="mdi mdi-shield-check-outline text-success me-1"></i>
                SNSU Institutional Document Engine
            </div>
        </aside>

        {{-- ─── Full-width PDF Stage ─────────────────────── --}}
        <main class="viewer-stage" id="viewerStage">
            <div class="pdf-frame-wrapper" id="pdfWrapper">

                {{-- Window Title Bar --}}
                <div class="pdf-window-header">
                    <div class="window-dots">
                        <span class="d-red"></span>
                        <span class="d-yel"></span>
                        <span class="d-grn"></span>
                    </div>
                    <div class="window-label" id="pdfFrameLabel">
                        {{ $meta['reference'] ?? 'Document Preview' }}
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-light border-0 py-0 px-1 text-muted" onclick="printFrame()" title="Print">
                            <i class="mdi mdi-printer fs-6"></i>
                        </button>
                        <button class="btn btn-sm btn-light border-0 py-0 px-1 text-muted" onclick="toggleFullscreen()" title="Fullscreen">
                            <i class="mdi mdi-fullscreen fs-6"></i>
                        </button>
                    </div>
                </div>

                {{-- Loading Overlay --}}
                <div class="pdf-loading-overlay" id="pdfLoadingOverlay">
                    <div class="spinner-ring"></div>
                    <div class="loading-text">Generating Dynamic PDF…</div>
                </div>

                {{-- Embedded PDF Iframe --}}
                <iframe
                    id="docPdfFrame"
                    src="{{ $pdfUrl }}"
                    title="Document PDF Viewer"
                    onload="onPdfLoaded()"
                    onerror="onPdfError()"
                ></iframe>
            </div>
        </main>

        {{-- ─── Comments & Revision Feedback Drawer ─────── --}}
        <aside class="comments-drawer collapsed" id="commentsDrawer">
            <div class="comments-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-comment-text-multiple-outline text-primary fs-5"></i>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 13px;">Document Feedback</div>
                        <div class="text-muted extra-small">Notes &amp; Revision Trail</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary font-mono" id="val_comments_count" style="font-size: 11px;">0 notes</span>
                    <button class="btn btn-sm btn-light border-0 p-1 text-muted" onclick="toggleCommentsDrawer()" title="Close Comments">
                        <i class="mdi mdi-close fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Comments Feed -->
            <div class="comments-body" id="val_comments_list">
                <div class="text-center py-5 text-muted extra-small">
                    <i class="mdi mdi-loading mdi-spin fs-4 d-block mb-2 text-primary"></i>
                    Loading document notes...
                </div>
            </div>

            <!-- Input Footer -->
            <div class="comments-footer">
                <div class="mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="val_remarks_input" class="text-muted extra-small fw-bold text-uppercase">
                            Add Note / Feedback
                        </label>
                        <select id="val_comment_type" class="form-select form-select-sm py-0 px-2 font-mono text-muted" style="width: auto; font-size: 11px; height: 24px;">
                            <option value="comment">General Note</option>
                            <option value="revision_requested">Revision Request</option>
                            <option value="endorsed">Endorsement Note</option>
                        </select>
                    </div>
                    <textarea id="val_remarks_input"
                              class="form-control"
                              rows="3"
                              style="font-size: 12.5px; resize: none;"
                              placeholder="Write a comment, feedback note, or revision instruction..."></textarea>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="extra-small text-muted font-mono" style="font-size: 10px;">
                        <i class="mdi mdi-lock-outline"></i> Institutional audit record
                    </span>
                    <button type="button" class="btn btn-sm btn-primary fw-semibold px-3 d-inline-flex align-items-center gap-1" id="btnSubmitComment" onclick="submitDocumentComment()">
                        <i class="mdi mdi-send fs-6"></i>
                        <span>Post Note</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    {{-- ─── Client Scripts ───────────────────────────────── --}}
    <script>
        const BASE_PDF_URL = @json(route('dss.documents.pdf', ['type' => $type, 'id' => $id]));
        const COMMENTS_INDEX_URL = @json(route('dss.documents.comments.index', ['type' => $type, 'id' => $id]));
        const COMMENTS_STORE_URL = @json(route('dss.documents.comments.store', ['type' => $type, 'id' => $id]));
        const SIGN_DOCUMENT_URL = @json(route('dss.documents.sign', ['type' => $type, 'id' => $id]));
        const REQUEST_REVISION_URL = @json(route('dss.documents.request_revision', ['type' => $type, 'id' => $id]));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const CURRENT_TIER_ROLE = @json($myPendingSignatory?->tier_role ?? '');
        const CURRENT_ROLE_LABEL = @json($myPendingSignatory?->role_label ?? '');

        function buildPdfUrl(paper, template, download) {
            const params = new URLSearchParams({
                paper: paper || 'A4',
                template: template || 'executive',
                download: download ? '1' : '0',
            });
            return BASE_PDF_URL + '?' + params.toString();
        }

        function onPdfLoaded() {
            const overlay = document.getElementById('pdfLoadingOverlay');
            if (overlay) overlay.style.display = 'none';
        }

        function onPdfError() {
            const overlay = document.getElementById('pdfLoadingOverlay');
            if (!overlay) return;
            overlay.innerHTML = `
                <div style="font-size: 36px; color: #ef4444;"><i class="mdi mdi-alert-circle-outline"></i></div>
                <div class="loading-text" style="color: #b91c1c;">Failed to load PDF. Check server connection.</div>
                <button onclick="reloadPdf()" style="margin-top: 8px; padding: 6px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">Retry</button>
            `;
        }

        function reloadPdf() {
            const overlay = document.getElementById('pdfLoadingOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.innerHTML = `
                    <div class="spinner-ring"></div>
                    <div class="loading-text">Regenerating PDF…</div>
                `;
            }
            const frame = document.getElementById('docPdfFrame');
            const paper = document.getElementById('ctrlPaper')?.value || 'A4';
            const template = document.getElementById('ctrlTemplate')?.value || 'executive';
            const url = frame.src.split('?')[0] + '?' + new URLSearchParams({
                paper: paper,
                template: template,
                download: '0',
                _t: Date.now(),
            });
            frame.src = url;
        }

        function applyControls() {
            const paper = document.getElementById('ctrlPaper')?.value || 'A4';
            const template = document.getElementById('ctrlTemplate')?.value || 'executive';
            const newUrl = buildPdfUrl(paper, template, false) + '&_t=' + Date.now();
            const downloadUrl = buildPdfUrl(paper, template, true);

            const overlay = document.getElementById('pdfLoadingOverlay');
            if (overlay) overlay.style.display = 'flex';

            document.getElementById('docPdfFrame').src = newUrl;
            document.getElementById('btnDownload').href = downloadUrl;
            document.getElementById('pdfFrameLabel').textContent =
                (paper === '8.5x13' ? 'Folio / Long' : paper.toUpperCase()) + ' · ' +
                (template === 'iso_form' ? 'ISO Template' : 'Generic');
        }

        function toggleSidebar() {
            const sb = document.getElementById('viewerSidebar');
            if (!sb) return;
            sb.classList.toggle('collapsed');
        }

        function toggleFullscreen() {
            const wrapper = document.getElementById('pdfWrapper');
            if (!document.fullscreenElement) {
                wrapper.requestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        }

        function printFrame() {
            const frame = document.getElementById('docPdfFrame');
            try {
                frame.contentWindow.print();
            } catch (e) {
                window.open(frame.src.replace('download=0', 'download=0'), '_blank');
            }
        }

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "{{ route('dss.dashboard') }}";
            }
        }

        /* ─── Comments Drawer & Live Feedback Logic ────────── */
        let commentsLoaded = false;

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function toggleCommentsDrawer(forceOpen = null) {
            const drawer = document.getElementById('commentsDrawer');
            if (!drawer) return;

            if (forceOpen === true) {
                drawer.classList.remove('collapsed');
            } else if (forceOpen === false) {
                drawer.classList.add('collapsed');
            } else {
                drawer.classList.toggle('collapsed');
            }

            if (!drawer.classList.contains('collapsed')) {
                loadComments();
            }
        }

        function loadComments() {
            const list = document.getElementById('val_comments_list');
            if (!commentsLoaded && list) {
                list.innerHTML = `
                    <div class="text-center py-5 text-muted extra-small">
                        <i class="mdi mdi-loading mdi-spin fs-4 d-block mb-2 text-primary"></i>
                        Loading document notes...
                    </div>
                `;
            }

            fetch(COMMENTS_INDEX_URL, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                commentsLoaded = true;
                if (data.status === 'success') {
                    renderCommentsFeed(data.comments || []);
                }
            })
            .catch(err => {
                console.error('Failed to load comments:', err);
                if (list) {
                    list.innerHTML = `
                        <div class="text-center py-4 text-danger extra-small">
                            <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-1"></i>
                            Failed to load comments.
                        </div>
                    `;
                }
            });
        }

        function renderCommentsFeed(comments) {
            const list = document.getElementById('val_comments_list');
            const countBadge = document.getElementById('val_comments_count');
            const headerBadge = document.getElementById('commentsCountBadge');

            const countText = comments.length === 1 ? '1 note' : `${comments.length} notes`;
            if (countBadge) countBadge.innerText = countText;
            if (headerBadge) headerBadge.innerText = comments.length;

            if (!comments || comments.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-5 text-muted extra-small">
                        <i class="mdi mdi-comment-outline fs-3 d-block mb-1 text-slate-300"></i>
                        <span class="fw-bold text-dark">No comments or notes yet</span>
                        <p class="mt-1 mb-0 text-muted" style="font-size: 11px;">Revision instructions, feedback, and endorsement notes will appear here.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            comments.forEach(c => {
                const initial = escapeHtml(c.initial || (c.user_name || 'U').charAt(0).toUpperCase());
                const isSelf = !!c.is_self;

                if (c.action_type === 'revision_requested') {
                    if (isSelf) {
                        html += `
                            <div class="d-flex flex-column align-items-end mb-1 ms-auto" style="max-width: 88%;">
                                <div class="shadow-sm font-sans text-break text-dark" style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 16px; border-bottom-right-radius: 4px !important; padding: 10px 14px;">
                                    <div class="d-flex align-items-center justify-content-end gap-1 mb-2">
                                        <span class="badge bg-danger text-white font-mono extra-small py-1 px-2 rounded-pill shadow-xs" style="font-size: 9.5px; font-weight: 600;">
                                            <i class="mdi mdi-undo-variant me-1"></i> Revision Requested
                                        </span>
                                    </div>
                                    <div class="text-dark font-sans" style="font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                </div>
                                <span class="extra-small text-muted font-mono mt-1 pe-1" style="font-size: 10px;">${c.created_at_human} &bull; You</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="d-flex align-items-start gap-2 mb-1 me-auto" style="max-width: 90%;">
                                <div class="rounded-circle bg-danger text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-xs" style="width: 30px; height: 30px; font-size: 11px;">
                                    ${initial}
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-1 mb-1 ps-1">
                                        <span class="fw-bold text-dark extra-small" style="font-size: 11.5px;">${escapeHtml(c.user_name || 'Signatory')}</span>
                                        <span class="extra-small text-muted font-mono" style="font-size: 10px;">&bull; ${escapeHtml(c.user_role || 'Staff')}</span>
                                    </div>
                                    <div class="shadow-sm font-sans text-break text-dark" style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 16px; border-bottom-left-radius: 4px !important; padding: 10px 14px;">
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <span class="badge bg-danger text-white font-mono extra-small py-1 px-2 rounded-pill shadow-xs" style="font-size: 9.5px; font-weight: 600;">
                                                <i class="mdi mdi-undo-variant me-1"></i> Revision Requested
                                            </span>
                                        </div>
                                        <div class="text-dark font-sans" style="font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                    </div>
                                    <span class="extra-small text-muted font-mono mt-1 ps-1" style="font-size: 10px;">${c.created_at_human}</span>
                                </div>
                            </div>
                        `;
                    }
                } else if (c.action_type === 'endorsed') {
                    if (isSelf) {
                        html += `
                            <div class="d-flex flex-column align-items-end mb-1 ms-auto" style="max-width: 88%;">
                                <div class="shadow-sm font-sans text-break text-dark" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; border-bottom-right-radius: 4px !important; padding: 10px 14px;">
                                    <div class="d-flex align-items-center justify-content-end gap-1 mb-2">
                                        <span class="badge bg-success text-white font-mono extra-small py-1 px-2 rounded-pill shadow-xs" style="font-size: 9.5px; font-weight: 600;">
                                            <i class="mdi mdi-check-decagram-outline me-1"></i> Endorsed
                                        </span>
                                    </div>
                                    <div class="text-dark font-sans" style="font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                </div>
                                <span class="extra-small text-muted font-mono mt-1 pe-1" style="font-size: 10px;">${c.created_at_human} &bull; You</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="d-flex align-items-start gap-2 mb-1 me-auto" style="max-width: 90%;">
                                <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-xs" style="width: 30px; height: 30px; font-size: 11px;">
                                    ${initial}
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-1 mb-1 ps-1">
                                        <span class="fw-bold text-dark extra-small" style="font-size: 11.5px;">${escapeHtml(c.user_name || 'Signatory')}</span>
                                        <span class="extra-small text-muted font-mono" style="font-size: 10px;">&bull; ${escapeHtml(c.user_role || 'Staff')}</span>
                                    </div>
                                    <div class="shadow-sm font-sans text-break text-dark" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; border-bottom-left-radius: 4px !important; padding: 10px 14px;">
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <span class="badge bg-success text-white font-mono extra-small py-1 px-2 rounded-pill shadow-xs" style="font-size: 9.5px; font-weight: 600;">
                                                <i class="mdi mdi-check-decagram-outline me-1"></i> Endorsed
                                            </span>
                                        </div>
                                        <div class="text-dark font-sans" style="font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                    </div>
                                    <span class="extra-small text-muted font-mono mt-1 ps-1" style="font-size: 10px;">${c.created_at_human}</span>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    if (isSelf) {
                        html += `
                            <div class="d-flex flex-column align-items-end mb-1 ms-auto" style="max-width: 85%;">
                                <div class="text-white shadow-sm font-sans text-break" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 16px; border-bottom-right-radius: 4px !important; padding: 10px 14px; font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                <span class="extra-small text-muted font-mono mt-1 pe-1" style="font-size: 10px;">${c.created_at_human} &bull; You</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="d-flex align-items-start gap-2 mb-1 me-auto" style="max-width: 90%;">
                                <div class="rounded-circle bg-light border text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-xs" style="width: 30px; height: 30px; font-size: 11px;">
                                    ${initial}
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-1 mb-1 ps-1">
                                        <span class="fw-bold text-dark extra-small" style="font-size: 11.5px;">${escapeHtml(c.user_name || 'User')}</span>
                                        <span class="extra-small text-muted font-mono" style="font-size: 10px;">&bull; ${escapeHtml(c.user_role || 'Staff')}</span>
                                    </div>
                                    <div class="shadow-sm font-sans text-break text-dark" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; border-bottom-left-radius: 4px !important; padding: 10px 14px; font-size: 12.5px; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(c.comment)}</div>
                                    <span class="extra-small text-muted font-mono mt-1 ps-1" style="font-size: 10px;">${c.created_at_human}</span>
                                </div>
                            </div>
                        `;
                    }
                }
            });

            list.innerHTML = html;
            list.scrollTop = list.scrollHeight;
        }

        function submitDocumentComment() {
            const commentInput = document.getElementById('val_remarks_input');
            const typeSelect = document.getElementById('val_comment_type');
            const btn = document.getElementById('btnSubmitComment');
            const comment = commentInput?.value.trim();
            const actionType = typeSelect?.value || 'comment';

            if (!comment) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Comment',
                        text: 'Please write a comment or feedback note before posting.',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    alert('Please write a comment or feedback note before posting.');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i> Posting...`;

            fetch(COMMENTS_STORE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    comment: comment,
                    action_type: actionType
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-send fs-6"></i><span>Post Note</span>`;

                if (data.status === 'success') {
                    commentInput.value = '';
                    loadComments();
                    if (window.Swal) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Note posted successfully'
                        });
                    }
                } else {
                    alert(data.message || 'Failed to post comment.');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-send fs-6"></i><span>Post Note</span>`;
                alert('An error occurred while posting note. Please try again.');
            });
        }

        // Auto-open comments drawer if hash is #comments or URL param comments=1
        document.addEventListener('DOMContentLoaded', () => {
            if (window.location.hash === '#comments' || new URLSearchParams(window.location.search).get('comments') === '1') {
                toggleCommentsDrawer(true);
            }
        });

        /* ─── Digital Signature Canvas & Approval Handlers ─── */
        let sigModalInstance = null;
        let sigCanvas = null;
        let sigCtx = null;
        let isDrawing = false;
        let hasDrawn = false;
        let activeSigTab = 'draw';

        function openSignatureModal() {
            const modalEl = document.getElementById('signatureModal');
            if (!modalEl) return;

            if (!sigModalInstance) {
                sigModalInstance = new bootstrap.Modal(modalEl);
            }
            sigModalInstance.show();

            // Initialize canvas once modal is shown
            setTimeout(initSignatureCanvas, 250);
        }

        function showSignatureDetails() {
            if (!window.Swal) return;
            const hash = @json($mySignedSignatory?->signature_hash ?? 'N/A');
            const role = @json($mySignedSignatory?->role_label ?? 'Signatory');
            const date = @json($mySignedSignatory?->signed_at?->format('F d, Y h:i A') ?? 'Verified');

            Swal.fire({
                icon: 'success',
                title: 'Digitally Signed & Sealed',
                html: `
                    <div class="text-start py-2 extra-small" style="font-size: 13px;">
                        <p class="mb-2">You have verified and affixed your cryptographic digital endorsement for this document.</p>
                        <div class="bg-light p-3 rounded border mb-2 font-mono">
                            <div><strong>Role:</strong> ${escapeHtml(role)}</div>
                            <div><strong>Audit Hash:</strong> <span class="text-primary">${escapeHtml(hash)}</span></div>
                            <div><strong>Date Sealed:</strong> ${escapeHtml(date)}</div>
                        </div>
                        <div class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-shield-check text-success"></i> Tamper-evident hash logged in institutional document audit registry.
                        </div>
                    </div>
                `,
                confirmButtonText: 'Close',
                confirmButtonColor: '#2563eb'
            });
        }

        function initSignatureCanvas() {
            sigCanvas = document.getElementById('sigCanvas');
            if (!sigCanvas) return;

            sigCtx = sigCanvas.getContext('2d');
            const rect = sigCanvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;

            sigCanvas.width = rect.width * dpr;
            sigCanvas.height = rect.height * dpr;
            sigCtx.scale(dpr, dpr);

            sigCtx.strokeStyle = '#0f172a';
            sigCtx.lineWidth = 2.5;
            sigCtx.lineCap = 'round';
            sigCtx.lineJoin = 'round';

            // Clear events before attaching
            sigCanvas.onpointerdown = startDrawing;
            sigCanvas.onpointermove = drawStroke;
            sigCanvas.onpointerup = stopDrawing;
            sigCanvas.onpointercancel = stopDrawing;

            // Track active tab
            document.querySelectorAll('#sigNavTabs button').forEach(btn => {
                btn.addEventListener('shown.bs.tab', (e) => {
                    const target = e.target.getAttribute('data-bs-target');
                    activeSigTab = target === '#tabType' ? 'type' : (target === '#tabSaved' ? 'saved' : 'draw');
                    if (activeSigTab === 'draw') {
                        setTimeout(initSignatureCanvas, 100);
                    }
                });
            });
        }

        function startDrawing(e) {
            isDrawing = true;
            hasDrawn = true;
            const rect = sigCanvas.getBoundingClientRect();
            sigCtx.beginPath();
            sigCtx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function drawStroke(e) {
            if (!isDrawing) return;
            const rect = sigCanvas.getBoundingClientRect();
            sigCtx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            sigCtx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function clearSignatureCanvas() {
            if (!sigCanvas || !sigCtx) return;
            const rect = sigCanvas.getBoundingClientRect();
            sigCtx.clearRect(0, 0, rect.width, rect.height);
            hasDrawn = false;
        }

        function renderTypedSignaturePreview() {
            const input = document.getElementById('typedSignatureInput');
            const preview = document.getElementById('typedSignaturePreview');
            if (preview && input) {
                preview.innerText = input.value || 'Your Signature';
            }
        }

        function generateTypedSignatureBase64(name) {
            const c = document.createElement('canvas');
            c.width = 600;
            c.height = 160;
            const ctx = c.getContext('2d');
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.font = '52px "Brush Script MT", "Dancing Script", cursive';
            ctx.fillStyle = '#1e3a8a';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(name || 'Signatory', c.width / 2, c.height / 2);
            return c.toDataURL('image/png');
        }

        function submitDigitalSignature() {
            const btn = document.getElementById('btnConfirmSign');
            const remarks = document.getElementById('sigRemarksInput')?.value || '';
            let sigImg = null;
            let sigType = activeSigTab;

            if (activeSigTab === 'draw') {
                if (!hasDrawn) {
                    if (window.Swal) {
                        Swal.fire('Signature Missing', 'Please draw your digital signature on the pad before confirming.', 'warning');
                    } else {
                        alert('Please draw your digital signature on the pad before confirming.');
                    }
                    return;
                }
                sigImg = sigCanvas.toDataURL('image/png');
            } else if (activeSigTab === 'type') {
                const typedName = document.getElementById('typedSignatureInput')?.value.trim();
                if (!typedName) {
                    alert('Please enter your printed name.');
                    return;
                }
                sigImg = generateTypedSignatureBase64(typedName);
            } else if (activeSigTab === 'saved') {
                sigImg = document.getElementById('savedSigImg')?.src;
                if (!sigImg) {
                    alert('Saved signature not found.');
                    return;
                }
            }

            btn.disabled = true;
            btn.innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i> Sealing Document...`;

            fetch(SIGN_DOCUMENT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    tier_role: CURRENT_TIER_ROLE,
                    signature_img: sigImg,
                    signature_type: sigType,
                    remarks: remarks
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-check-decagram fs-6"></i><span>Affix Signature &amp; Seal</span>`;

                if (data.status === 'success') {
                    if (sigModalInstance) {
                        sigModalInstance.hide();
                    }

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Document Signed & Sealed!',
                            html: `
                                <div class="text-center py-2" style="font-size: 13px;">
                                    <p class="mb-2 text-dark font-semibold">${escapeHtml(data.message)}</p>
                                    <span class="badge bg-light text-primary border font-mono px-3 py-1" style="font-size: 11px;">
                                        Audit Hash: ${escapeHtml(data.signature_hash)}
                                    </span>
                                </div>
                            `,
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'Signing failed.');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `<i class="mdi mdi-check-decagram fs-6"></i><span>Affix Signature &amp; Seal</span>`;
                alert('An error occurred while signing. Please try again.');
            });
        }

        function openRevisionPrompt() {
            if (sigModalInstance) {
                sigModalInstance.hide();
            }

            if (window.Swal) {
                Swal.fire({
                    title: 'Request Document Revisions',
                    text: 'State the required corrections or revision instructions for the document author:',
                    input: 'textarea',
                    inputPlaceholder: 'Type specific revision items or instructions here...',
                    inputAttributes: {
                        rows: 4
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit Revision Request',
                    confirmButtonColor: '#ef4444',
                    cancelButtonText: 'Back',
                    showLoaderOnConfirm: true,
                    preConfirm: (remarks) => {
                        if (!remarks || !remarks.trim()) {
                            Swal.showValidationMessage('Please write the revision instructions before submitting.');
                            return false;
                        }

                        return fetch(REQUEST_REVISION_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({ remarks: remarks.trim() })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error(response.statusText);
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value?.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Revisions Requested',
                            text: 'The document author has been notified of your revision instructions.',
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            loadComments();
                            toggleCommentsDrawer(true);
                        });
                    }
                });
            } else {
                const remarks = prompt('Please enter revision instructions for the document author:');
                if (remarks && remarks.trim()) {
                    fetch(REQUEST_REVISION_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({ remarks: remarks.trim() })
                    })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message || 'Revision request logged.');
                        window.location.reload();
                    });
                }
            }
        }
    </script>

    <!-- ─── Digital Signature Modal ───────────────────────── -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <!-- Modal Header -->
                <div class="modal-header bg-white border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="mdi mdi-draw fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="signatureModalLabel" style="font-size: 16px;">
                                Affix Electronic Signature &amp; Endorsement
                            </h5>
                            <div class="text-muted extra-small">
                                {{ $meta['title'] }} &bull; <span class="font-mono text-dark fw-semibold">{{ $meta['reference'] ?? 'Ref: N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4 bg-light">
                    <!-- Signer Identity Card -->
                    <div class="bg-white border rounded-3 p-3 mb-3 shadow-xs d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase extra-small text-muted fw-bold">Signatory Role Assignment</span>
                            <div class="fw-bold text-dark fs-6 mt-1">
                                {{ $myPendingSignatory?->role_label ?? 'Authorized Signatory' }}
                            </div>
                            <div class="text-muted extra-small">
                                {{ auth()->user()?->employee?->full_name ?: (auth()->user()?->name ?? 'Signatory') }} &bull;
                                <span class="text-dark">{{ $myPendingSignatory?->designation ?: (auth()->user()?->employee?->designation ?: 'Authorized Signatory') }}</span>
                            </div>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 font-mono rounded-pill extra-small">
                            Tier: {{ strtoupper($myPendingSignatory?->tier_role ?? 'approval') }}
                        </span>
                    </div>

                    <div class="bg-white border rounded-3 p-3 shadow-xs">
                        <ul class="nav nav-pills gap-2 mb-3" id="sigNavTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1 px-3 extra-small fw-bold rounded-pill" id="tabDrawBtn" data-bs-toggle="pill" data-bs-target="#tabDraw" type="button" role="tab">
                                    <i class="mdi mdi-gesture-tap me-1"></i> Draw Signature
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-3 extra-small fw-bold rounded-pill" id="tabTypeBtn" data-bs-toggle="pill" data-bs-target="#tabType" type="button" role="tab">
                                    <i class="mdi mdi-format-text me-1"></i> Type Name
                                </button>
                            </li>
                            @if(!empty($savedSignature))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-3 extra-small fw-bold rounded-pill" id="tabSavedBtn" data-bs-toggle="pill" data-bs-target="#tabSaved" type="button" role="tab">
                                    <i class="mdi mdi-history me-1"></i> Use Saved Signature
                                </button>
                            </li>
                            @endif
                        </ul>

                        <div class="tab-content" id="sigTabContent">
                            <div class="tab-pane fade show active" id="tabDraw" role="tabpanel">
                                <div class="border rounded-2 position-relative" style="background: #ffffff; height: 180px; touch-action: none;">
                                    <canvas id="sigCanvas" width="680" height="180" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
                                    <div class="position-absolute bottom-0 start-0 w-100 px-3 py-1 border-top d-flex align-items-center justify-content-between bg-light" style="font-size: 11px;">
                                        <span class="text-muted"><i class="mdi mdi-pen"></i> Sign on line above using mouse, stylus, or touch</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 text-muted" onclick="clearSignatureCanvas()">
                                            <i class="mdi mdi-eraser"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tabType" role="tabpanel">
                                <div class="mb-2">
                                    <label class="form-label extra-small text-muted fw-bold">Signer Printed Name</label>
                                    <input type="text" class="form-control" id="typedSignatureInput"
                                           value="{{ auth()->user()?->employee?->full_name ?: (auth()->user()?->name ?? '') }}"
                                           placeholder="Enter full name" oninput="renderTypedSignaturePreview()">
                                </div>
                                <div class="border rounded-2 p-3 text-center bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <div id="typedSignaturePreview" style="font-family: 'Brush Script MT', 'Dancing Script', cursive; font-size: 38px; color: #1e3a8a; user-select: none;">
                                        {{ auth()->user()?->employee?->full_name ?: (auth()->user()?->name ?? '') }}
                                    </div>
                                </div>
                            </div>

                            @if(!empty($savedSignature))
                            <div class="tab-pane fade" id="tabSaved" role="tabpanel">
                                <div class="border rounded-2 p-3 text-center bg-light d-flex flex-column align-items-center justify-content-center" style="height: 180px;">
                                    <img src="{{ $savedSignature }}" id="savedSigImg" alt="Saved Signature" style="max-height: 110px; max-width: 90%; object-fit: contain;">
                                    <span class="badge bg-success-subtle text-success mt-2">
                                        <i class="mdi mdi-shield-check"></i> Previously Verified Signature Stamp
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Remarks / Notation -->
                    <div class="mt-3">
                        <label for="sigRemarksInput" class="form-label extra-small text-muted fw-bold text-uppercase">
                            Signatory Notation / Remarks (Optional)
                        </label>
                        <input type="text" class="form-control form-control-sm" id="sigRemarksInput"
                               placeholder="e.g. Verified and approved compliant with university standards.">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-white border-top px-4 py-3 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" onclick="openRevisionPrompt()">
                        <i class="mdi mdi-undo-variant"></i>
                        <span>Request Revisions</span>
                    </button>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-success fw-bold px-4 d-flex align-items-center gap-1" id="btnConfirmSign" onclick="submitDigitalSignature()">
                            <i class="mdi mdi-check-decagram fs-6"></i>
                            <span>Affix Signature &amp; Seal</span>
                        </button>
                    </div>
                </div>
    <!-- Attachments & Physically Signed Documents Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom px-4 py-3 bg-light">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="attachmentsModalLabel">
                        <i class="mdi mdi-paperclip text-primary"></i>
                        Attached Files &amp; Physically Signed Copies
                        <span class="badge bg-primary text-white font-mono extra-small">{{ $attachmentsCount ?? 0 }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if(!empty($uploadedDocs) && $uploadedDocs->isNotEmpty())
                        <div class="table-responsive mb-4">
                            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light extra-small text-uppercase text-muted">
                                    <tr>
                                        <th>File &amp; Name</th>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Uploaded By / Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($uploadedDocs as $doc)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-2 d-inline-flex align-items-center justify-content-center bg-light border p-2" style="width: 36px; height: 36px;">
                                                        <i class="mdi {{ $doc->file_icon }} fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                                        <div class="extra-small text-muted font-mono">{{ $doc->original_filename }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($doc->is_physically_signed)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small px-2 py-1 font-mono mb-1 d-inline-block">
                                                        <i class="mdi mdi-draw-pen me-1"></i> Wet-Signed Copy
                                                    </span>
                                                @endif
                                                <div><span class="badge bg-light text-secondary border extra-small">{{ $doc->category_label }}</span></div>
                                            </td>
                                            <td class="font-mono extra-small">{{ $doc->formatted_file_size }}</td>
                                            <td>
                                                <div class="extra-small text-dark fw-semibold">{{ $doc->uploader_name ?: ($doc->uploader?->name ?: 'User') }}</div>
                                                <div class="extra-small text-muted font-mono">{{ $doc->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    @if($doc->is_previewable)
                                                        <a href="{{ $doc->preview_url }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1 text-primary" title="Preview file">
                                                            <i class="mdi mdi-eye-outline fs-6"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ $doc->download_url }}" class="btn btn-sm btn-light border px-2 py-1 text-dark" title="Download file">
                                                        <i class="mdi mdi-download fs-6"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted small mb-4">
                            <i class="mdi mdi-folder-open-outline fs-1 text-secondary opacity-50 d-block mb-1"></i>
                            No files attached to this document yet.
                        </div>
                    @endif

                    <!-- Upload zone inside viewer modal -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold text-dark extra-small text-uppercase mb-2">Upload New Attachment / Wet-Signed Copy</h6>
                        <x-dss.document-uploader :model="$document" :type="$type" category="physically_signed_plan" title="Attach Document" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleAttachmentsModal() {
        const modalEl = document.getElementById('attachmentsModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }
    </script>
</body>
</html>
