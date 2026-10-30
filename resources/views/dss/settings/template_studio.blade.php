@extends('layouts.dss.app')

@section('title', 'Document Template Studio — ' . $currentDocType)

@section('styles')
<style>
    /* Full Studio Layout */
    .studio-container {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        margin-top: 15px;
    }

    .studio-sidebar {
        flex: 0 0 380px;
        max-width: 380px;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .studio-canvas-area {
        flex: 1;
        min-width: 0;
        background: #334155;
        border-radius: 12px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow-x: auto;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.25);
    }

    /* Authentic Policy Memorandum Sheet */
    .memorandum-sheet {
        background: #ffffff;
        width: 215.9mm;
        min-height: 279.4mm;
        padding: 22mm 20mm;
        box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        font-family: Arial, Helvetica, sans-serif;
        color: #000;
        font-size: 13px;
        line-height: 1.5;
        position: relative;
        border-radius: 2px;
    }

    .header-univ-name {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #0f172a;
    }

    .meta-table td {
        padding: 3px 6px;
        vertical-align: top;
        font-size: 13px;
        line-height: 1.35;
    }

    .meta-table td.label-col {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 700;
        width: 130px;
        text-transform: uppercase;
        color: #000;
    }

    .article-title {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12.5px;
        margin-top: 1rem;
        margin-bottom: 0.4rem;
        text-align: center;
    }

    .section-clause {
        text-align: justify;
        text-indent: 2rem;
        margin-bottom: 0.65rem;
        line-height: 1.5;
        font-size: 13px;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
    }

    .qr-stamp-box {
        border: 1px dashed #64748b;
        padding: 6px;
        background: #ffffff;
        display: inline-block;
        text-align: center;
    }

    /* 12-Column Grid & Drag-Drop Signatory Canvas */
    .studio-sig-col {
        transition: transform 0.2s ease, opacity 0.2s ease;
        position: relative;
    }

    .studio-sig-col.is-dragging {
        opacity: 0.35 !important;
        transform: scale(0.97);
    }

    .studio-sig-col.drag-over-active .studio-sig-card {
        border: 2px dashed #2563eb !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15) !important;
        transform: translateY(-2px);
    }

    .studio-sig-card {
        transition: all 0.2s ease;
        cursor: grab;
        background: #ffffff;
    }

    .studio-sig-card:hover {
        border-color: #93c5fd !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .studio-sig-card:active {
        cursor: grabbing;
    }

    .grid12-overlay-col {
        flex: 1;
        border-right: 1px dashed rgba(37, 99, 235, 0.22);
        background: rgba(37, 99, 235, 0.02);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 4px;
        font-family: monospace;
        font-size: 8px;
        font-weight: 700;
        color: rgba(37, 99, 235, 0.55);
    }

    .grid12-overlay-col:first-child {
        border-left: 1px dashed rgba(37, 99, 235, 0.22);
    }

    /* Interactive On-Sheet Header & Footer Placement Bars */
    .template-placement-toolbar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 8px;
        user-select: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .template-placement-btn-group .btn {
        font-size: 11px;
        padding: 2px 8px;
        font-weight: 600;
    }

    .template-placement-btn-group .btn.active-pos {
        background-color: var(--dss-primary, #059669) !important;
        color: #ffffff !important;
        border-color: var(--dss-primary, #059669) !important;
    }

    .logo-drop-slot {
        border: 1.5px dashed #cbd5e1;
        border-radius: 6px;
        background: rgba(248, 250, 252, 0.6);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 48px;
        padding: 4px;
    }

    .logo-drop-slot.slot-active {
        border-color: #3b82f6 !important;
        background: rgba(239, 246, 255, 0.8) !important;
    }

    .logo-drop-slot.drag-over-slot {
        border-color: #2563eb !important;
        background: #dbeafe !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
        transform: scale(1.02);
    }

    .draggable-logo-box {
        cursor: grab;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
        padding: 2px;
    }

    .draggable-logo-box:hover {
        transform: scale(1.03);
        outline: 2px dashed #2563eb;
        outline-offset: 3px;
        border-radius: 4px;
        background: rgba(37, 99, 235, 0.04);
    }

    .draggable-logo-box:active {
        cursor: grabbing;
    }

    .draggable-logo-box .drag-indicator-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: #2563eb;
        color: #fff;
        font-size: 8px;
        border-radius: 3px;
        padding: 1px 4px;
        display: none;
        pointer-events: none;
        z-index: 10;
        font-family: var(--dss-font-mono, monospace);
    }

    .draggable-logo-box:hover .drag-indicator-badge {
        display: block;
    }

    /* Institutional Logo Library Tray & Draggable Chips */
    .logo-library-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        width: 100%;
    }
    .logo-chips-tray {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-height: 44px;
    }
    .logo-library-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        cursor: grab;
        transition: all 0.15s ease;
        user-select: none;
    }
    .logo-library-chip:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        box-shadow: 0 2px 6px rgba(59,130,246,0.15);
        transform: translateY(-1px);
    }
    .logo-library-chip:active {
        cursor: grabbing;
    }
    .logo-library-chip img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        border-radius: 3px;
        background: #fff;
    }
    .slot-label-tag {
        font-size: 8px;
        font-family: var(--dss-font-mono, monospace);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .slot-action-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .logo-drop-slot:hover .slot-action-btn {
        display: flex;
    }
    .slot-pick-btn {
        position: absolute;
        bottom: -6px;
        right: -6px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .logo-drop-slot:hover .slot-pick-btn {
        display: flex;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .memorandum-sheet, .memorandum-sheet * {
            visibility: visible;
        }
        .memorandum-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 15mm !important;
            box-shadow: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Top Navigation Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center bg-white p-3 rounded-3 border shadow-2xs mb-3 gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dss.settings.institution') }}#doctpl-pane" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Settings
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="live-dot"></span>
                    <h5 class="fw-bold text-dark mb-0 font-mono tracking-tight">Policy Document Template Studio</h5>
                    <span class="badge bg-primary-subtle text-primary border font-mono">Live Next Screen</span>
                </div>
                <small class="text-muted">Real-time Layout & Multi-Column Signatories Designer for Official University Documents</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnToggleGrid" onclick="toggle12ColGuide()" title="Toggle visual 12-column guide on sheet">
                <i class="mdi mdi-view-column-outline me-1"></i><span id="gridGuideText">Show 12-Col Grid</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i>Print Official Sheet
            </button>
            <a href="{{ route('dss.settings.institution.preview_pdf') }}?document_type={{ urlencode($currentDocType) }}" target="_blank" class="btn btn-sm btn-danger">
                <i class="mdi mdi-file-pdf-box me-1"></i>Official PDF Preview
            </a>
            <button type="button" class="btn btn-sm btn-dss-primary fw-bold" onclick="saveTemplateSettings()">
                <i class="mdi mdi-content-save me-1"></i>Save Template Settings
            </button>
        </div>
    </div>

    <!-- Document Types Switcher Tabs -->
    <div class="dss-card p-3 mb-3 bg-light-subtle">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="extra-small fw-bold text-muted text-uppercase me-2">
                <i class="mdi mdi-file-document-multiple-outline me-1"></i>Select Template Target:
            </span>
            @foreach($docTypes as $dt)
                <a href="{{ route('dss.settings.institution.template_studio', ['document_type' => $dt]) }}"
                   class="btn btn-sm {{ $currentDocType === $dt ? 'btn-dark text-white fw-bold shadow-xs' : 'btn-white text-secondary border' }}">
                   @if($currentDocType === $dt) <i class="mdi mdi-check-circle-outline me-1 text-success"></i> @endif
                   @if($dt === 'Generic (Default)')
                       <span class="badge font-mono me-1" style="background:#064e3b;color:#a7f3d0;font-size:9px;">GLOBAL DEFAULT</span>
                   @endif
                   {{ $dt }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Studio Split Screen -->
    <div class="studio-container">

        <!-- Left Column: Studio Controls -->
        <div class="studio-sidebar">

            <!-- Card 0: Institutional Header & Logo Layout (FPDF) -->
            <div class="dss-card p-3 mb-3 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="mdi mdi-image-filter-center-focus me-1.5 text-primary"></i>Header &amp; Logo Layout
                        </h6>
                        <div class="extra-small text-muted">FPDF Drawing Placement &amp; Official Letterhead</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border font-mono extra-small">FPDF Engine</span>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Logo Placement Layout
                    </label>
                    <select class="form-select form-select-sm" id="ctl_logo_layout" onchange="syncSheetHeader()">
                        <option value="centered" {{ ($currentSettings['logo_layout'] ?? 'centered') === 'centered' ? 'selected' : '' }}>
                            Executive Centered (Single Seal at Top)
                        </option>
                        <option value="dual_left" {{ ($currentSettings['logo_layout'] ?? '') === 'dual_left' ? 'selected' : '' }}>
                            Dual Left - Magkatabi (Two Seals Side-by-Side on Left)
                        </option>
                        <option value="split_dual" {{ ($currentSettings['logo_layout'] ?? '') === 'split_dual' ? 'selected' : '' }}>
                            Split Dual-Logos (Left: Univ Seal &bull; Right: Auxiliary Seal)
                        </option>
                        <option value="left" {{ ($currentSettings['logo_layout'] ?? '') === 'left' ? 'selected' : '' }}>
                            Modern Left-Aligned (Seal on Left Margin)
                        </option>
                        <option value="right" {{ ($currentSettings['logo_layout'] ?? '') === 'right' ? 'selected' : '' }}>
                            Executive Right-Aligned (Seal on Right Margin)
                        </option>
                        <option value="iso_box" {{ ($currentSettings['logo_layout'] ?? '') === 'iso_box' ? 'selected' : '' }}>
                            ISO QMS Form (Left Seal &bull; Right Doc Control Box)
                        </option>
                    </select>
                    <div class="extra-small text-muted mt-0.5">Controls the FPDF vector coordinates and visual letterhead placement.</div>
                </div>

                <div class="row g-2 mb-2.5">
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Logo Width / Scale
                        </label>
                        <select class="form-select form-select-sm font-mono" id="ctl_logo_size" onchange="syncSheetHeader()">
                            <option value="18" {{ ($currentSettings['logo_size'] ?? 24) == 18 ? 'selected' : '' }}>18 mm (Compact)</option>
                            <option value="20" {{ ($currentSettings['logo_size'] ?? 24) == 20 ? 'selected' : '' }}>20 mm (Medium)</option>
                            <option value="24" {{ ($currentSettings['logo_size'] ?? 24) == 24 ? 'selected' : '' }}>24 mm (Standard SUC)</option>
                            <option value="28" {{ ($currentSettings['logo_size'] ?? 24) == 28 ? 'selected' : '' }}>28 mm (Prominent)</option>
                            <option value="32" {{ ($currentSettings['logo_size'] ?? 24) == 32 ? 'selected' : '' }}>32 mm (Grand Seal)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Top Y-Offset Margin
                        </label>
                        <select class="form-select form-select-sm font-mono" id="ctl_logo_top_margin" onchange="syncSheetHeader()">
                            <option value="6" {{ ($currentSettings['logo_top_margin'] ?? 10) == 6 ? 'selected' : '' }}>6 mm (Tight Top)</option>
                            <option value="8" {{ ($currentSettings['logo_top_margin'] ?? 10) == 8 ? 'selected' : '' }}>8 mm (Moderate)</option>
                            <option value="10" {{ ($currentSettings['logo_top_margin'] ?? 10) == 10 ? 'selected' : '' }}>10 mm (Standard)</option>
                            <option value="12" {{ ($currentSettings['logo_top_margin'] ?? 10) == 12 ? 'selected' : '' }}>12 mm (Spacious)</option>
                            <option value="15" {{ ($currentSettings['logo_top_margin'] ?? 10) == 15 ? 'selected' : '' }}>15 mm (Formal Deep)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Dividing Line / Rule Style
                    </label>
                    <select class="form-select form-select-sm" id="ctl_divider_style" onchange="syncSheetHeader()">
                        <option value="single_black" {{ ($currentSettings['divider_style'] ?? 'single_black') === 'single_black' ? 'selected' : '' }}>
                            Single Solid Black Rule (0.4mm Standard)
                        </option>
                        <option value="double_rule" {{ ($currentSettings['divider_style'] ?? '') === 'double_rule' ? 'selected' : '' }}>
                            Formal Executive Double Line
                        </option>
                        <option value="theme_accent" {{ ($currentSettings['divider_style'] ?? '') === 'theme_accent' ? 'selected' : '' }}>
                            Primary Theme Accent Color
                        </option>
                        <option value="none" {{ ($currentSettings['divider_style'] ?? '') === 'none' ? 'selected' : '' }}>
                            No Dividing Line (Clean Spacing)
                        </option>
                    </select>
                </div>

                <div class="form-check form-switch extra-small mb-2.5">
                    <input class="form-check-input" type="checkbox" id="ctl_show_secondary_logo" {{ !empty($currentSettings['show_secondary_logo']) ? 'checked' : '' }} onchange="syncSheetHeader()">
                    <label class="form-check-label text-dark fw-semibold" for="ctl_show_secondary_logo">
                        Render Auxiliary Seal (Bagong Pilipinas / ISO Seal)
                    </label>
                </div>

                <!-- Footer Logo & Seal Placement Controls -->
                <div class="pt-2 border-top">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Footer Logo &amp; Seal Placement
                    </label>
                    <select class="form-select form-select-sm" id="ctl_footer_logo_position" onchange="syncSheetFooter()">
                        <option value="dual_left" {{ ($currentSettings['footer_logo_position'] ?? 'left') === 'dual_left' ? 'selected' : '' }}>
                            Dual Left - Magkatabi (Two Logos Side-by-Side on Left)
                        </option>
                        <option value="dual_left_and_right" {{ ($currentSettings['footer_logo_position'] ?? '') === 'dual_left_and_right' ? 'selected' : '' }}>
                            Dual Left + Right (Two on Left &bull; Certification on Right)
                        </option>
                        <option value="left" {{ ($currentSettings['footer_logo_position'] ?? 'left') === 'left' ? 'selected' : '' }}>
                            Footer Left (Single Auxiliary/Partner Seal)
                        </option>
                        <option value="center" {{ ($currentSettings['footer_logo_position'] ?? '') === 'center' ? 'selected' : '' }}>
                            Footer Center (Centered Quality Seal)
                        </option>
                        <option value="right" {{ ($currentSettings['footer_logo_position'] ?? '') === 'right' ? 'selected' : '' }}>
                            Footer Right (Right Margin Seal)
                        </option>
                        <option value="split_dual" {{ ($currentSettings['footer_logo_position'] ?? '') === 'split_dual' ? 'selected' : '' }}>
                            Split Dual (Both Left &amp; Right Seals)
                        </option>
                        <option value="none" {{ ($currentSettings['footer_logo_position'] ?? '') === 'none' ? 'selected' : '' }}>
                            No Footer Logos (Clean Text Only)
                        </option>
                    </select>
                    <div class="form-check form-switch extra-small mt-1.5">
                        <input class="form-check-input" type="checkbox" id="ctl_show_footer_logo" {{ !empty($currentSettings['show_footer_logo']) ? 'checked' : '' }} onchange="syncSheetFooter()">
                        <label class="form-check-label text-dark fw-semibold" for="ctl_show_footer_logo">
                            Enable Footer Seals &amp; Certification Badges
                        </label>
                    </div>
                </div>
            </div>

            <!-- Card 1: Salutation & Format Settings -->
            <div class="dss-card p-3 mb-3 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="mdi mdi-tune me-1.5 text-primary"></i>Document Salutations
                    </h6>
                    <span class="badge bg-light text-muted border font-mono small">Live Sync</span>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Opening Greeting / Salutation
                    </label>
                    <textarea class="form-control form-control-sm" id="ctl_greeting" rows="2"
                              oninput="syncSheetField('greeting', this.value)">{{ $currentSettings['greeting'] ?? ($institution->school_acronym ? 'Official Greetings from ' . $institution->school_acronym . '!' : 'Official Greetings!') }}</textarea>
                    <div class="extra-small text-muted mt-0.5">Appears immediately after the metadata table.</div>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Closing Salutation
                    </label>
                    <textarea class="form-control form-control-sm" id="ctl_closing" rows="2"
                              oninput="syncSheetField('closing', this.value)">{{ $currentSettings['closing'] ?? 'For strict compliance and immediate dissemination.' }}</textarea>
                    <div class="extra-small text-muted mt-0.5">Appears right before the signatories.</div>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Core Values Banner (Footer)
                    </label>
                    <input type="text" class="form-control form-control-sm" id="ctl_corevals"
                           value="{{ $currentSettings['core_values'] ?? 'Accountability | Responsiveness | Excellence' }}"
                           oninput="syncSheetField('core_values', this.value)">
                </div>

                <!-- Document Typography & Spacing -->
                <div class="row g-2 mb-2.5">
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Official Document Font
                        </label>
                        <select class="form-select form-select-sm" id="ctl_font" onchange="syncSheetTypography()">
                            <option value="Arial" {{ ($currentSettings['font_family'] ?? 'Arial') === 'Arial' ? 'selected' : '' }}>Arial (Modern Official Sans-Serif)</option>
                            <option value="Times" {{ ($currentSettings['font_family'] ?? '') === 'Times' ? 'selected' : '' }}>Times New Roman (Formal Legal Serif)</option>
                            <option value="Helvetica" {{ ($currentSettings['font_family'] ?? '') === 'Helvetica' ? 'selected' : '' }}>Helvetica (Clean Corporate Sans)</option>
                            <option value="Courier" {{ ($currentSettings['font_family'] ?? '') === 'Courier' ? 'selected' : '' }}>Courier (Official Typewriter / Monospace)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Line Height
                        </label>
                        <select class="form-select form-select-sm" id="ctl_lineheight" onchange="syncSheetTypography()">
                            <option value="1.15" {{ ($currentSettings['line_height'] ?? 1.5) == 1.15 ? 'selected' : '' }}>1.15 (Compact)</option>
                            <option value="1.25" {{ ($currentSettings['line_height'] ?? 1.5) == 1.25 ? 'selected' : '' }}>1.25 (Standard)</option>
                            <option value="1.5" {{ ($currentSettings['line_height'] ?? 1.5) == 1.5 ? 'selected' : '' }}>1.5 (Official Standard)</option>
                            <option value="1.75" {{ ($currentSettings['line_height'] ?? 1.5) == 1.75 ? 'selected' : '' }}>1.75 (Spacious)</option>
                            <option value="2.0" {{ ($currentSettings['line_height'] ?? 1.5) == 2.0 ? 'selected' : '' }}>2.0 (Double Space)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Spacing Below Lines
                        </label>
                        <select class="form-select form-select-sm" id="ctl_paraspacing" onchange="syncSheetTypography()">
                            <option value="2.0" {{ ($currentSettings['paragraph_spacing'] ?? 3.0) == 2.0 ? 'selected' : '' }}>2.0 mm (Tight)</option>
                            <option value="3.0" {{ ($currentSettings['paragraph_spacing'] ?? 3.0) == 3.0 ? 'selected' : '' }}>3.0 mm (Standard)</option>
                            <option value="4.5" {{ ($currentSettings['paragraph_spacing'] ?? 3.0) == 4.5 ? 'selected' : '' }}>4.5 mm (Moderate)</option>
                            <option value="6.0" {{ ($currentSettings['paragraph_spacing'] ?? 3.0) == 6.0 ? 'selected' : '' }}>6.0 mm (Spacious)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2.5">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                        Signature Gap (Below Role Label)
                    </label>
                    <select class="form-select form-select-sm" id="ctl_siggap" onchange="renderSignatoriesLive()">
                        <option value="5" {{ ($currentSettings['signature_gap'] ?? 9.0) == 5 ? 'selected' : '' }}>5 mm (Compact)</option>
                        <option value="7" {{ ($currentSettings['signature_gap'] ?? 9.0) == 7 ? 'selected' : '' }}>7 mm (Balanced)</option>
                        <option value="9" {{ ($currentSettings['signature_gap'] ?? 9.0) == 9 ? 'selected' : '' }}>9 mm (Standard Recommended)</option>
                        <option value="12" {{ ($currentSettings['signature_gap'] ?? 9.0) == 12 ? 'selected' : '' }}>12 mm (Spacious)</option>
                        <option value="16" {{ ($currentSettings['signature_gap'] ?? 9.0) == 16 ? 'selected' : '' }}>16 mm (Tall Signature)</option>
                    </select>
                    <div class="extra-small text-muted mt-0.5">Vertical room reserved between Role Label and Name for signature or DTS security stamp.</div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Signatory Columns
                        </label>
                        <select class="form-select form-select-sm" id="ctl_sigcols" onchange="renderSignatoriesLive()">
                            <option value="1" {{ ($currentSettings['signatory_columns'] ?? 2) == 1 ? 'selected' : '' }}>1 Column (Stacked)</option>
                            <option value="2" {{ ($currentSettings['signatory_columns'] ?? 2) == 2 ? 'selected' : '' }}>2 Columns (Standard)</option>
                            <option value="3" {{ ($currentSettings['signatory_columns'] ?? 2) == 3 ? 'selected' : '' }}>3 Columns (Wide)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Alignment Layout
                        </label>
                        <select class="form-select form-select-sm" id="ctl_siglayout" onchange="renderSignatoriesLive()">
                            <option value="spread" {{ ($currentSettings['signatory_layout'] ?? 'spread') === 'spread' ? 'selected' : '' }}>Spread (Even)</option>
                            <option value="left" {{ ($currentSettings['signatory_layout'] ?? '') === 'left' ? 'selected' : '' }}>Left Aligned</option>
                            <option value="right" {{ ($currentSettings['signatory_layout'] ?? '') === 'right' ? 'selected' : '' }}>Right Aligned</option>
                            <option value="center" {{ ($currentSettings['signatory_layout'] ?? '') === 'center' ? 'selected' : '' }}>Centered</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 1b: Footer Content & Tracking Stamp -->
            <div class="dss-card p-3 mb-3 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="mdi mdi-page-layout-footer me-1.5 text-secondary"></i>Footer Content & Tracking
                        </h6>
                        <div class="extra-small text-muted">Address, contact, core values, barcode/QR, distribution</div>
                    </div>
                    <span class="badge bg-secondary-subtle text-dark border font-mono extra-small">Live Sync</span>
                </div>

                <!-- Footer Center Text Toggles -->
                <div class="mb-2.5">
                    <div class="extra-small fw-bold text-muted text-uppercase mb-1">Footer Center Content</div>
                    <div class="d-flex flex-column gap-1">
                        <div class="form-check form-switch extra-small">
                            <input class="form-check-input" type="checkbox" id="ctl_show_footer_address"
                                {{ ($currentSettings['show_footer_address'] ?? true) ? 'checked' : '' }}
                                onchange="syncSheetFooter()">
                            <label class="form-check-label text-dark" for="ctl_show_footer_address">
                                Show Address
                            </label>
                        </div>
                        <div class="form-check form-switch extra-small">
                            <input class="form-check-input" type="checkbox" id="ctl_show_footer_contact"
                                {{ ($currentSettings['show_footer_contact'] ?? true) ? 'checked' : '' }}
                                onchange="syncSheetFooter()">
                            <label class="form-check-label text-dark" for="ctl_show_footer_contact">
                                Show Tel / Email Contact
                            </label>
                        </div>
                        <div class="form-check form-switch extra-small">
                            <input class="form-check-input" type="checkbox" id="ctl_show_footer_website"
                                {{ ($currentSettings['show_footer_website'] ?? true) ? 'checked' : '' }}
                                onchange="syncSheetFooter()">
                            <label class="form-check-label text-dark" for="ctl_show_footer_website">
                                Show Website URL
                            </label>
                        </div>
                        <div class="form-check form-switch extra-small">
                            <input class="form-check-input" type="checkbox" id="ctl_show_core_values"
                                {{ ($currentSettings['show_core_values'] ?? true) ? 'checked' : '' }}
                                onchange="syncSheetFooter()">
                            <label class="form-check-label text-dark" for="ctl_show_core_values">
                                Show Core Values Banner
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tracking Stamp (Barcode + QR) — Independent Position Control -->
                <div class="mb-2.5 pt-2 border-top">
                    <div class="extra-small fw-bold text-muted text-uppercase mb-2">DTS Tracking Stamp Placement</div>

                    <div class="mb-2">
                        <label class="form-label extra-small text-muted mb-1">
                            <i class="mdi mdi-barcode me-1 text-dark"></i><strong>Barcode</strong> Position
                        </label>
                        <select class="form-select form-select-sm" id="ctl_barcode_position" onchange="syncSheetFooter()">
                            <option value="above_right" {{ ($currentSettings['barcode_position'] ?? 'above_right') === 'above_right' ? 'selected' : '' }}>Above Footer Rule — Right</option>
                            <option value="above_left" {{ ($currentSettings['barcode_position'] ?? '') === 'above_left' ? 'selected' : '' }}>Above Footer Rule — Left</option>
                            <option value="above_center" {{ ($currentSettings['barcode_position'] ?? '') === 'above_center' ? 'selected' : '' }}>Above Footer Rule — Center</option>
                            <option value="footer_left" {{ ($currentSettings['barcode_position'] ?? '') === 'footer_left' ? 'selected' : '' }}>In Footer — Left (beside logos)</option>
                            <option value="footer_center" {{ ($currentSettings['barcode_position'] ?? '') === 'footer_center' ? 'selected' : '' }}>In Footer — Center</option>
                            <option value="footer_right" {{ ($currentSettings['barcode_position'] ?? '') === 'footer_right' ? 'selected' : '' }}>In Footer — Right</option>
                            <option value="none" {{ ($currentSettings['barcode_position'] ?? '') === 'none' ? 'selected' : '' }}>Hidden (No Barcode)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label extra-small text-muted mb-1">
                            <i class="mdi mdi-qrcode me-1 text-dark"></i><strong>QR Code</strong> Position
                        </label>
                        <select class="form-select form-select-sm" id="ctl_qr_position" onchange="syncSheetFooter()">
                            <option value="above_right" {{ ($currentSettings['qr_position'] ?? 'above_right') === 'above_right' ? 'selected' : '' }}>Above Footer Rule — Right (beside barcode)</option>
                            <option value="above_left" {{ ($currentSettings['qr_position'] ?? '') === 'above_left' ? 'selected' : '' }}>Above Footer Rule — Left</option>
                            <option value="above_center" {{ ($currentSettings['qr_position'] ?? '') === 'above_center' ? 'selected' : '' }}>Above Footer Rule — Center</option>
                            <option value="footer_left" {{ ($currentSettings['qr_position'] ?? '') === 'footer_left' ? 'selected' : '' }}>In Footer — Left</option>
                            <option value="footer_center" {{ ($currentSettings['qr_position'] ?? '') === 'footer_center' ? 'selected' : '' }}>In Footer — Center</option>
                            <option value="footer_right" {{ ($currentSettings['qr_position'] ?? '') === 'footer_right' ? 'selected' : '' }}>In Footer — Right</option>
                            <option value="none" {{ ($currentSettings['qr_position'] ?? '') === 'none' ? 'selected' : '' }}>Hidden (No QR)</option>
                        </select>
                    </div>
                </div>

                <!-- Distribution List -->
                <div class="pt-2 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="extra-small fw-bold text-muted text-uppercase">Distribution / Copy Furnished</div>
                        <div class="form-check form-switch extra-small mb-0">
                            <input class="form-check-input" type="checkbox" id="ctl_show_distribution"
                                {{ ($currentSettings['show_distribution'] ?? true) ? 'checked' : '' }}
                                onchange="syncSheetFooter()">
                            <label class="form-check-label text-dark fw-semibold" for="ctl_show_distribution">Show</label>
                        </div>
                    </div>
                    <textarea class="form-control form-control-sm" id="ctl_distribution_text" rows="3"
                              style="font-size: 11px; resize: vertical;"
                              oninput="syncSheetFooter()">{{ $currentSettings['distribution_text'] ?? 'Office of the University President; Office of the VPAA; Office of the VPAF; Office of the VPRE; University Legal Counsel; QA Directorate; All College Deans; All Campus Directors; University Records Management Office; Official University Portal.' }}</textarea>
                    <div class="extra-small text-muted mt-0.5">Shown at the bottom of every page.</div>
                </div>
            </div>

            <!-- Card 2: Template Signatories Manager -->
            <div class="dss-card p-3 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="mdi mdi-draw-pen me-1.5 text-primary"></i>Signatories for {{ $currentDocType }}
                        </h6>
                        <div class="extra-small text-muted">Inherited automatically by new policies</div>
                    </div>
                    <span class="badge bg-primary text-white font-mono" id="sigsCountBadge">0 Signatories</span>
                </div>

                <!-- Current Signatories List -->
                <div id="studioSigsList" class="mb-3 d-flex flex-column gap-2">
                    <!-- Populated dynamically -->
                </div>

                <!-- Add Signatory Form (Strictly uses user.id) -->
                <div class="border rounded p-3 bg-light shadow-2xs">
                    <p class="extra-small fw-bold text-dark text-uppercase mb-2">
                        <i class="mdi mdi-account-plus me-1 text-primary"></i>Add Signatory Rule
                    </p>

                    <div class="mb-2">
                        <label class="extra-small text-muted mb-0">Signatory Official (user.id) <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="ctl_user_id" onchange="onSignatoryUserSelected(this.value)">
                            <option value="">-- Select Registered System User --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" data-name="{{ $u->name }}" data-email="{{ $u->email }}">
                                    {{ $u->name }} [UID #{{ $u->id }} - {{ $u->role ?? 'Staff' }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Multiple Institutional Designations Picker (Auto-triggered when official has >1 designation) -->
                    <div id="ctl_multiple_designations_wrapper" style="display: none;" class="mb-2 p-2 bg-light-subtle rounded border border-info-subtle">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="extra-small text-primary fw-bold mb-0">
                                <i class="mdi mdi-badge-account-horizontal me-1"></i>Multiple Designations Detected:
                            </label>
                            <span class="badge bg-info-subtle text-info border font-mono" id="ctl_desig_count_badge" style="font-size: 8.5px;"></span>
                        </div>
                        <select class="form-select form-select-xs font-mono" id="ctl_designation_picker" onchange="onDesignationPicked(this.value)">
                        </select>
                        <div class="extra-small text-muted mt-1" style="font-size: 9.5px;">
                            Auto-selected primary. Pick from the list above to switch title and office.
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="extra-small text-muted mb-0">Role Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="ctl_role_label" placeholder="e.g. Prepared by: or Approved by:">
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="extra-small text-muted mb-0">Designation / Title</label>
                            <input type="text" class="form-control form-control-sm" id="ctl_designation" placeholder="e.g. SUC President III">
                        </div>
                        <div class="col-6">
                            <label class="extra-small text-muted mb-0">Office / Unit</label>
                            <input type="text" class="form-control form-control-sm" id="ctl_office" placeholder="e.g. Office of the President">
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="extra-small text-muted mb-0">Width (12-Col Grid)</label>
                            <select class="form-select form-select-sm font-mono fw-semibold text-primary" id="ctl_colspan">
                                <option value="6" selected>6 Cols (Half - 50%)</option>
                                <option value="12">12 Cols (Full - 100%)</option>
                                <option value="4">4 Cols (1/3 Width)</option>
                                <option value="3">3 Cols (1/4 Width)</option>
                                <option value="8">8 Cols (2/3 Width)</option>
                                <option value="2">2 Cols (1/6 Width)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="extra-small text-muted mb-0">Text Alignment</label>
                            <select class="form-select form-select-sm" id="ctl_align">
                                <option value="left" selected>Left Aligned</option>
                                <option value="center">Centered</option>
                                <option value="right">Right Aligned</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="extra-small text-muted mb-0">Sort Order</label>
                        <input type="number" class="form-control form-control-sm font-mono" id="ctl_sort" value="1" min="1">
                    </div>

                    <button type="button" class="btn btn-dss-primary btn-sm w-100 fw-bold shadow-2xs" onclick="addSignatoryRule()">
                        <i class="mdi mdi-plus-circle me-1"></i>Add to {{ $currentDocType }}
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Column: Canvas Area -->
        <div class="studio-canvas-area">

            <!-- Institutional Logo Library & Asset Drawer (full-width above the sheet) -->
            <div class="logo-library-card p-3 mb-3" id="studioLogoLibraryCard" style="width: 215.9mm; max-width: 100%;">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge bg-primary text-white font-mono px-2 py-1" style="font-size: 11px;">
                            <i class="mdi mdi-image-multiple me-1"></i>Logo Library
                        </span>
                        <span class="text-muted" style="font-size: 11px; white-space: nowrap;">
                            Drag onto slots below or click to assign
                        </span>
                    </div>
                    <button type="button" class="btn btn-xs btn-primary fw-bold py-1 px-2 flex-shrink-0" onclick="openUploadLogoModal()">
                        <i class="mdi mdi-cloud-upload me-1"></i>Upload Logo
                    </button>
                </div>

                <!-- Draggable Logo Chips (horizontally scrollable) -->
                <div class="logo-chips-tray" id="logoLibraryChipsContainer">
                    <!-- Populated dynamically by renderLogoLibraryTray() -->
                </div>
            </div>

            <div class="memorandum-sheet" id="actualPolicySheet">

                <!-- Dynamic Official Letterhead Header -->
                <div id="sheetOfficialHeaderWrapper" class="mb-2">
                    <!-- Populated live by syncSheetHeader() -->
                </div>

                <div id="sheetDividerLine" class="mb-3">
                    <hr style="border-top: 2px solid #000; opacity: 1; margin: 0 0 16px 0;">
                </div>

                <!-- Memorandum Header Metadata Table -->
                <table class="w-100 meta-table mb-3">
                    <tr>
                        <td class="label-col">{{ strtoupper($currentDocType) }}:</td>
                        <td class="fw-bold fs-6 font-mono text-dark">{{ $samplePolicy->policy_number }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">TO:</td>
                        <td class="fw-semibold">
                            Vice Presidents, Campus Directors, College Deans, Academic Directors, Department Chairs, Unit Heads, Faculty, Administrative Personnel, and All Concerned
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">FROM:</td>
                        <td class="fw-bold">
                            THE UNIVERSITY PRESIDENT
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">SUBJECT:</td>
                        <td class="fw-bold text-uppercase" style="font-family: Arial, Helvetica, sans-serif; color: #0f172a;">
                            {{ $samplePolicy->title }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">DATE:</td>
                        <td>{{ date('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">LEGAL BASIS:</td>
                        <td class="text-muted" style="font-size: 12px;">
                            {{ $samplePolicy->legal_basis }}
                        </td>
                    </tr>
                </table>

                <hr style="border-top: 1px solid #64748b; opacity: 0.7; margin: 0 0 16px 0;">

                <!-- Cultural Greeting (Live-synced) -->
                <div id="sheet_greeting" class="mb-3 fst-italic text-secondary" style="font-size: 13.5px;">
                    {{ $currentSettings['greeting'] ?? ($institution->school_acronym ? 'Official Greetings from ' . $institution->school_acronym . '!' : 'Official Greetings!') }}
                </div>

                <!-- I. Rationale & Policy Statement (Actual Policy Body with 1.5 Line Height) -->
                <div class="mb-3">
                    <div class="fw-bold text-uppercase mb-1" style="font-family: Arial, sans-serif; font-size: 11.5px;">I. Rationale & Policy Statement</div>
                    <div class="section-clause">
                        {{ $samplePolicy->rationale }}
                    </div>
                </div>

                <!-- II. Scope & Coverage -->
                <div class="mb-3">
                    <div class="fw-bold text-uppercase mb-1" style="font-family: Arial, sans-serif; font-size: 11.5px;">II. Scope & Coverage</div>
                    <div class="section-clause">
                        This institutional instrument shall strictly apply to {{ strtolower($samplePolicy->scope) }}
                    </div>
                </div>

                <!-- III. Operating Provisions & Guidelines (Structured Clauses) -->
                <div class="mb-3">
                    <div class="fw-bold text-uppercase mb-1" style="font-family: Arial, sans-serif; font-size: 11.5px;">III. Operating Provisions & Guidelines</div>
                    @foreach($samplePolicy->provisions as $art)
                        <div class="article-title">
                            {{ $art['article'] }}: {{ $art['title'] }}
                        </div>
                        @foreach($art['clauses'] as $clause)
                            <div class="section-clause">
                                {{ $clause }}
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <!-- Effectivity & Implementation Clause -->
                <div class="mb-4">
                    <div class="article-title">EFFECTIVITY & IMPLEMENTATION CLAUSE</div>
                    <div class="section-clause">
                        This {{ $currentDocType }} is formally issued pursuant to {{ $samplePolicy->legal_basis }}, having undergone multi-sectoral stakeholder consultation, Legal Counsel compliance vetting, QA audit conformance, and council deliberation.
                    </div>
                    <div class="section-clause">
                        This issuance shall take effect on <strong>{{ date('F d, Y') }}</strong> and shall remain in force subject to mandatory evaluation every {{ $samplePolicy->sunset_review_years }} years. All previous memoranda, office orders, or circulars contrary to or inconsistent with the provisions of this issuance are hereby repealed, amended, or modified accordingly.
                    </div>
                </div>

                <!-- Closing Salutation (Live-synced) -->
                <div id="sheet_closing" class="mb-4 fst-italic" style="font-size: 13px;">
                    {{ $currentSettings['closing'] ?? 'For strict compliance and immediate dissemination.' }}
                </div>

                <!-- LIVE MULTI-COLUMN SIGNATORIES SECTION -->
                <div id="sheet_signatories_container" style="margin-top: 1.5rem; page-break-inside: avoid;">
                    <!-- Populated dynamically by renderSignatoriesLive() -->
                </div>

                <!-- DTS Tracking Barcode & QR Stamp Box -->
                @php
                    $studioBarcode = null;
                    try {
                        $studioBarcode = \DNS1D::getBarcodePNG($samplePolicy->tracking_number, 'C128', 1.3, 26);
                    } catch (\Throwable $e) {
                        $studioBarcode = null;
                    }
                @endphp
                <!-- Dynamic Official Footer Section (Live-synced with drag-and-drop and footer placement) -->
                <div id="sheetOfficialFooterWrapper" class="mt-4 pt-2">
                    <!-- Populated live by syncSheetFooter() -->
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    var pageToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    var currentDocType = '{{ addslashes($currentDocType) }}';
    var allSignatories = @json($policySignatories ?? []);
    var allUsers = @json($users ?? []);
    var draggedSigIndex = null;
    var showGridGuide = false;

    window.sealUrl = @json($sealUrl);
    window.secondaryLogoUrl = @json($secondaryLogoUrl);
    window.isoLogoUrl = @json($isoLogoUrl);
    window.schoolName = @json($schoolName);
    window.address = @json($schoolAddress);
    window.availableLogos = @json($availableLogos ?? []);
    window.footerLeftSlot1 = @json($currentSettings['footer_left_slot_1'] ?? 'seal');
    window.footerLeftSlot2 = @json($currentSettings['footer_left_slot_2'] ?? null);
    window.footerRightSlot1 = @json($currentSettings['footer_right_slot_1'] ?? null);
    window.footerRightSlot2 = @json($currentSettings['footer_right_slot_2'] ?? null);
    window.headerLeftSlot1 = @json($currentSettings['header_left_slot_1'] ?? 'seal');
    window.headerLeftSlot2 = @json($currentSettings['header_left_slot_2'] ?? null);
    window.headerRightSlot1 = @json($currentSettings['header_right_slot_1'] ?? null);
    window.stampPosition = @json($currentSettings['stamp_position'] ?? 'top_right');
    window.showBarcode = @json($currentSettings['show_barcode'] ?? true);
    window.showQr = @json($currentSettings['show_qr'] ?? true);
    window.barcodePosition = @json($currentSettings['barcode_position'] ?? 'above_right');
    window.qrPosition     = @json($currentSettings['qr_position']     ?? 'above_right');
    window.showFooterAddress = @json($currentSettings['show_footer_address'] ?? true);
    window.showFooterContact = @json($currentSettings['show_footer_contact'] ?? true);
    window.showFooterWebsite = @json($currentSettings['show_footer_website'] ?? true);
    window.showCoreValues = @json($currentSettings['show_core_values'] ?? true);
    window.showDistribution = @json($currentSettings['show_distribution'] ?? true);
    window.distributionText = @json($currentSettings['distribution_text'] ?? 'Office of the University President; Office of the VPAA; Office of the VPAF; Office of the VPRE; University Legal Counsel; QA Directorate; All College Deans; All Campus Directors; University Records Management Office; Official University Portal.');

    function getLogoObj(keyOrId) {
        if (!keyOrId || keyOrId === 'none') return null;
        const found = (window.availableLogos || []).find(l => l.id === keyOrId);
        if (found) return found;
        if (keyOrId === 'seal' && window.sealUrl) {
            return { id: 'seal', label: (window.schoolName || 'University') + ' Seal', url: window.sealUrl, category: 'seal' };
        }
        if (keyOrId === 'secondary' && window.secondaryLogoUrl) {
            return { id: 'secondary', label: 'Bagong Pilipinas / Auxiliary', url: window.secondaryLogoUrl, category: 'national' };
        }
        if (keyOrId === 'iso' && window.isoLogoUrl) {
            return { id: 'iso', label: 'ISO 9001:2015 Certified', url: window.isoLogoUrl, category: 'certification' };
        }
        return null;
    }
    @php
        $contactStr = '';
        if (!empty($institution->contact_phone)) { $contactStr .= 'Tel: ' . $institution->contact_phone; }
        if (!empty($institution->contact_email)) { $contactStr .= ($contactStr ? ' | ' : '') . 'Email: ' . $institution->contact_email; }
        $webStr = !empty($institution->website_url) ? 'Website: ' . $institution->website_url : '';
        $sampleTrackNumber = $samplePolicy->tracking_number ?? 'DTS-2026-POL01';
        $sampleTrackUrl = url('/track/' . $sampleTrackNumber);
        $sampleBarcodeBase64 = $studioBarcode ?? '';
    @endphp
    window.contactInfo = @json($contactStr);
    window.websiteInfo = @json($webStr);
    window.sampleBarcodeBase64 = @json($sampleBarcodeBase64);
    window.sampleTrackingNumber = @json($sampleTrackNumber);
    window.sampleTrackUrl = @json($sampleTrackUrl);

    // 1. Live Typing Sync to the Sheet
    function syncSheetField(field, val) {
        if (field === 'greeting') {
            const el = document.getElementById('sheet_greeting');
            if (el) el.textContent = val || 'Official Greetings from {{ $institution->school_acronym ?: "" }}!';
        } else if (field === 'closing') {
            const el = document.getElementById('sheet_closing');
            if (el) el.textContent = val || 'For strict compliance and immediate dissemination.';
        } else if (field === 'core_values') {
            const el = document.getElementById('sheet_core_values');
            if (el) el.textContent = val || 'Accountability | Responsiveness | Excellence';
        }
    }

    // Toggle 12-Column Visual Grid Guide
    function toggle12ColGuide() {
        showGridGuide = !showGridGuide;
        const btn = document.getElementById('btnToggleGrid');
        const text = document.getElementById('gridGuideText');
        const overlay = document.getElementById('grid12Overlay');

        if (showGridGuide) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary');
            text.textContent = 'Hide 12-Col Grid';
            if (overlay) overlay.style.display = 'flex';
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-secondary');
            text.textContent = 'Show 12-Col Grid';
            if (overlay) overlay.style.display = 'none';
        }
    }

    // Helper: Get filtered & sorted signatories for current document type
    function getCurrentDocSignatories() {
        return allSignatories.filter(s => {
            if (!s.document_type) return true;
            return s.document_type === currentDocType;
        }).sort((a, b) => (a.sort_order || 1) - (b.sort_order || 1));
    }

    // 2. Render Signatories Live onto the Sheet & Sidebar in 12-Column Grid
    function renderSignatoriesLive() {
        const sigs = getCurrentDocSignatories();
        const sigGapMm = parseFloat(document.getElementById('ctl_siggap')?.value || '9');
        const sigGapPx = Math.max(18, Math.round(sigGapMm * 3.2));

        // Update badge count
        const countBadge = document.getElementById('sigsCountBadge');
        if (countBadge) {
            countBadge.textContent = sigs.length + (sigs.length === 1 ? ' Signatory' : ' Signatories');
        }

        // Populate Sidebar List
        const listEl = document.getElementById('studioSigsList');
        if (listEl) {
            if (sigs.length === 0) {
                listEl.innerHTML = `<div class="p-3 border border-dashed rounded text-center text-muted extra-small fst-italic">No signatories assigned yet for ${escapeHtml(currentDocType)}.</div>`;
            } else {
                let listHtml = '';
                sigs.forEach((s, idx) => {
                    const uName = (s.user && s.user.name) ? s.user.name : (s.employee_name || 'Official');
                    const uidStr = s.user_id ? `<span class="badge bg-primary-subtle text-primary border font-mono ms-1" style="font-size: 8px;">UID: #${s.user_id}</span>` : '';
                    const colSpan = parseInt(s.col_span, 10) || 6;
                    listHtml += `
                        <div class="p-2 rounded border bg-white shadow-2xs d-flex justify-content-between align-items-center">
                            <div>
                                <div class="extra-small fw-semibold text-muted text-uppercase">${escapeHtml(s.role_label || 'Approved by:')}</div>
                                <div class="fw-bold text-dark extra-small">${escapeHtml(uName)} ${uidStr}</div>
                                <div class="text-muted" style="font-size: 10px;">
                                    ${escapeHtml(s.designation || '')} &bull;
                                    <span class="badge bg-secondary-subtle text-dark border font-mono">${colSpan}/12 cols</span> &bull;
                                    <span class="text-secondary font-mono">#${idx + 1}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-xs btn-outline-danger py-1 px-1.5" onclick="removeSignatoryRule(${s.id})" title="Remove Signatory">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                listEl.innerHTML = listHtml;
            }
        }

        // Render onto the Actual Sheet in a standard 12-Column Grid
        const container = document.getElementById('sheet_signatories_container');
        if (!container) return;

        // Build 12-column visual grid guides overlay
        let gridOverlayHtml = `<div class="grid12-overlay" id="grid12Overlay" style="display: ${showGridGuide ? 'flex' : 'none'}; position: absolute; inset: 0; pointer-events: none; z-index: 1; min-height: 120px;">`;
        for (let col = 1; col <= 12; col++) {
            gridOverlayHtml += `<div class="grid12-overlay-col">col ${col}</div>`;
        }
        gridOverlayHtml += `</div>`;

        if (sigs.length === 0) {
            container.innerHTML = `
                <div class="position-relative">
                    ${gridOverlayHtml}
                    <div class="p-4 border border-dashed rounded text-center text-muted position-relative" style="z-index: 2; background: rgba(248, 250, 252, 0.7);">
                        <i class="mdi mdi-draw-pen fs-3 text-secondary mb-1 d-block"></i>
                        <div class="fw-bold text-dark small">No Official Signatories Placed Yet</div>
                        <div class="extra-small text-muted">Use the form on the left to add signatories. They will appear here in the 12-column grid and can be dragged directly on the sheet.</div>
                    </div>
                </div>
            `;
            return;
        }

        // Build 12-Column Row with Draggable Signatory Cards
        let rowHtml = `<div class="position-relative" style="min-height: 120px;">
            ${gridOverlayHtml}
            <div class="row g-4 position-relative" id="sheet_grid_row" style="z-index: 2;">
        `;

        sigs.forEach((s, idx) => {
            const uName = (s.user && s.user.name) ? s.user.name : (s.employee_name || 'Designated Official');
            const uidStr = s.user_id ? `<span class="badge bg-primary-subtle text-primary border font-mono ms-1" style="font-size: 8px;">UID: #${s.user_id}</span>` : '';
            const colSpan = parseInt(s.col_span, 10) || 6;
            const isCenter = (s.alignment === 'center');
            const isRight = (s.alignment === 'right');

            rowHtml += `
                <div class="col-${colSpan} studio-sig-col"
                     id="sig_col_${s.id}"
                     data-id="${s.id}"
                     data-index="${idx}"
                     draggable="true"
                     ondragstart="onSigDragStart(event, ${idx})"
                     ondragover="onSigDragOver(event, ${idx})"
                     ondragleave="onSigDragLeave(event)"
                     ondrop="onSigDrop(event, ${idx})"
                     ondragend="onSigDragEnd(event)">

                    <div class="studio-sig-card p-3 rounded border bg-white shadow-2xs position-relative ${isRight ? 'text-end' : (isCenter ? 'text-center' : 'text-start')}"
                         style="border-left: 3.5px solid #2563eb !important;">

                        <!-- Canvas Quick Control Bar (Hidden on print) -->
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom no-print text-start">
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-primary cursor-move" title="Drag to reposition on canvas" style="cursor: grab;">
                                    <i class="mdi mdi-drag fs-5"></i>
                                </span>
                                <span class="badge bg-primary-subtle text-primary font-mono" style="font-size: 9px;">#${idx + 1}</span>
                                ${s.user_id ? `<span class="badge bg-light text-secondary border font-mono" style="font-size: 8.5px;">UID: #${s.user_id}</span>` : ''}
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                <!-- Text Alignment Toggle (Left / Center / Right) -->
                                <div class="btn-group btn-group-xs me-0.5" role="group" title="Text alignment">
                                    <button type="button"
                                            class="btn btn-xs ${(!isCenter && !isRight) ? 'btn-dark' : 'btn-outline-secondary'} py-0 px-1.5"
                                            style="height: 22px;"
                                            onclick="updateSigAlignment(${s.id}, 'left', event)"
                                            title="Left Align">
                                        <i class="mdi mdi-format-align-left" style="font-size: 11px;"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-xs ${isCenter ? 'btn-dark' : 'btn-outline-secondary'} py-0 px-1.5"
                                            style="height: 22px;"
                                            onclick="updateSigAlignment(${s.id}, 'center', event)"
                                            title="Center Align">
                                        <i class="mdi mdi-format-align-center" style="font-size: 11px;"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-xs ${isRight ? 'btn-dark' : 'btn-outline-secondary'} py-0 px-1.5"
                                            style="height: 22px;"
                                            onclick="updateSigAlignment(${s.id}, 'right', event)"
                                            title="Right Align">
                                        <i class="mdi mdi-format-align-right" style="font-size: 11px;"></i>
                                    </button>
                                </div>

                                <!-- 12-Column Span Selector -->
                                <div class="d-flex align-items-center gap-1" title="Columns covered out of 12">
                                    <select class="form-select form-select-xs py-0 px-1 font-mono fw-bold text-primary"
                                            style="font-size: 10px; width: 92px; height: 22px;"
                                            onchange="updateSigSpan(${s.id}, this.value, event)"
                                            onclick="event.stopPropagation()">
                                        <option value="12" ${colSpan === 12 ? 'selected' : ''}>12 cols (100%)</option>
                                        <option value="8" ${colSpan === 8 ? 'selected' : ''}>8 cols (66%)</option>
                                        <option value="6" ${colSpan === 6 ? 'selected' : ''}>6 cols (50%)</option>
                                        <option value="4" ${colSpan === 4 ? 'selected' : ''}>4 cols (33%)</option>
                                        <option value="3" ${colSpan === 3 ? 'selected' : ''}>3 cols (25%)</option>
                                        <option value="2" ${colSpan === 2 ? 'selected' : ''}>2 cols (16%)</option>
                                    </select>
                                </div>

                                <!-- Edit Official & Info Button -->
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1.5"
                                        style="height: 22px; line-height: 20px;"
                                        onclick="openEditSigModal(${s.id}); event.stopPropagation();"
                                        title="Edit Official Name, User, Role & Details">
                                    <i class="mdi mdi-pencil" style="font-size: 11px;"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1"
                                        style="height: 22px; line-height: 20px;"
                                        onclick="removeSignatoryRule(${s.id}); event.stopPropagation();"
                                        title="Remove Signatory">
                                    <i class="mdi mdi-close" style="font-size: 11px;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Official Memorandum Signatory Format -->
                        <div class="small fw-semibold text-muted text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                            ${escapeHtml(s.role_label || 'Approved by:')}
                        </div>

                        <div style="width: 85%; border-bottom: 1.5px solid #000; margin-bottom: 4px; height: ${sigGapPx}px; position: relative; ${isRight ? 'margin-left: auto; margin-right: 0;' : (isCenter ? 'margin-left: auto; margin-right: auto;' : '')}">
                            <div style="font-family: Arial, sans-serif; font-size: 8px; color: #166534; position: absolute; bottom: 2px; ${isRight ? 'right: 0;' : (isCenter ? 'left: 50%; transform: translateX(-50%);' : 'left: 0;')}">
                                <i class="mdi mdi-shield-check text-success"></i>
                                <strong>DTS VERIFIED</strong>
                            </div>
                        </div>

                        <div class="fw-bold text-uppercase fs-6" style="font-family: Arial, sans-serif; letter-spacing: 0.5px; color: #0f172a;">
                            ${escapeHtml(uName)}
                        </div>
                        ${s.designation ? `<div style="font-size: 12px; color: #334155;">${escapeHtml(s.designation)}</div>` : ''}
                        ${s.office ? `<div style="font-size: 11px; color: #64748b;">${escapeHtml(s.office)}</div>` : ''}

                    </div>
                </div>
            `;
        });

        rowHtml += `</div></div>`;
        container.innerHTML = rowHtml;
    }

    // ==========================================
    // DRAG AND DROP ENGINE FOR 12-COLUMN GRID
    // ==========================================

    function onSigDragStart(e, idx) {
        draggedSigIndex = idx;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', idx);
        const col = e.currentTarget;
        setTimeout(() => {
            col.classList.add('is-dragging');
        }, 0);
    }

    function onSigDragOver(e, idx) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const col = e.currentTarget;
        if (draggedSigIndex !== idx) {
            col.classList.add('drag-over-active');
        }
    }

    function onSigDragLeave(e) {
        e.currentTarget.classList.remove('drag-over-active');
    }

    function onSigDrop(e, targetIdx) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over-active');

        if (draggedSigIndex === null || draggedSigIndex === targetIdx) return;

        const sigs = getCurrentDocSignatories();
        if (draggedSigIndex >= sigs.length || targetIdx >= sigs.length) return;

        // Splice dragged item and insert at target position
        const [movedItem] = sigs.splice(draggedSigIndex, 1);
        sigs.splice(targetIdx, 0, movedItem);

        // Update local sort_order
        sigs.forEach((s, i) => {
            s.sort_order = i + 1;
        });

        // Re-render canvas immediately
        renderSignatoriesLive();

        // Persist order to backend
        persistSignatoriesOrder(sigs);
    }

    function onSigDragEnd(e) {
        e.currentTarget.classList.remove('is-dragging');
        document.querySelectorAll('.studio-sig-col').forEach(el => {
            el.classList.remove('drag-over-active', 'is-dragging');
        });
        draggedSigIndex = null;
    }

    function persistSignatoriesOrder(sigs) {
        fetch("{{ route('dss.settings.institution.signatory_template.reorder') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': pageToken },
            body: JSON.stringify({
                signatories: sigs.map((s, i) => ({
                    id: s.id,
                    sort_order: i + 1,
                    col_span: parseInt(s.col_span, 10) || 6
                }))
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: false
                });
                toast.fire({
                    icon: 'success',
                    title: 'Canvas order re-arranged and saved!'
                });
            }
        });
    }

    // ==========================================
    // PER-SIGNATORY 12-COLUMN SPAN CONTROLS
    // ==========================================

    function updateSigSpan(id, newSpan, e) {
        if (e) e.stopPropagation();
        const spanVal = parseInt(newSpan, 10);
        const sig = allSignatories.find(s => s.id === id);
        if (!sig) return;

        sig.col_span = spanVal;
        renderSignatoriesLive();

        fetch(`/dss/settings/institution/signatory-template/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': pageToken },
            body: JSON.stringify({ col_span: spanVal })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1400,
                    timerProgressBar: false
                });
                toast.fire({
                    icon: 'success',
                    title: `Column width updated to ${spanVal} / 12 cols!`
                });
            }
        });
    }

    function updateSigAlignment(id, newAlign, e) {
        if (e) e.stopPropagation();
        const sig = allSignatories.find(s => s.id === id);
        if (!sig) return;

        sig.alignment = newAlign;
        renderSignatoriesLive();

        fetch(`/dss/settings/institution/signatory-template/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': pageToken },
            body: JSON.stringify({ alignment: newAlign })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1300,
                    timerProgressBar: false
                });
                const alignLabel = newAlign === 'right' ? 'Right Aligned' : (newAlign === 'center' ? 'Centered' : 'Left Aligned');
                toast.fire({
                    icon: 'success',
                    title: `Alignment set to ${alignLabel}!`
                });
            }
        });
    }

    // ==========================================
    // EDIT SIGNATORY DETAILS MODAL (Select User & Infos)
    // ==========================================

    function openEditSigModal(id) {
        const sig = allSignatories.find(s => s.id === id);
        if (!sig) return;

        let userOptionsHtml = '<option value="">-- Select Registered User --</option>';
        allUsers.forEach(u => {
            const isSel = (sig.user_id && sig.user_id == u.id) ? 'selected' : '';
            userOptionsHtml += `<option value="${u.id}" data-name="${escapeHtml(u.name)}" ${isSel}>${escapeHtml(u.name)} [UID #${u.id} - ${escapeHtml(u.role || 'Staff')}]</option>`;
        });

        Swal.fire({
            title: '<div class="fs-5 fw-bold text-dark"><i class="mdi mdi-account-edit me-1 text-primary"></i>Edit Signatory Official & Details</div>',
            html: `
                <div class="text-start" style="font-family: var(--dss-font-sans, inherit);">
                    <div class="mb-2.5">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Select Official (user.id) <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="swal_edit_user_id">
                            ${userOptionsHtml}
                        </select>
                    </div>

                    <!-- Multiple Institutional Designations Picker for Modal -->
                    <div id="swal_edit_desig_wrapper" style="display: none;" class="mb-2.5 p-2 bg-light-subtle rounded border border-info-subtle">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label extra-small fw-bold text-primary text-uppercase mb-0">
                                <i class="mdi mdi-badge-account-horizontal me-1"></i>Multiple Designations Detected:
                            </label>
                            <span class="badge bg-info-subtle text-info border font-mono" id="swal_desig_count_badge" style="font-size: 8px;"></span>
                        </div>
                        <select class="form-select form-select-xs font-mono" id="swal_edit_desig_picker"></select>
                        <div class="extra-small text-muted mt-1" style="font-size: 9px;">
                            Pick from official's registered designations to switch title and office.
                        </div>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Role Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="swal_edit_role_label" value="${escapeHtml(sig.role_label || '')}" placeholder="e.g. Prepared by: or Approved by:">
                    </div>
                    <div class="row g-2 mb-2.5">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Designation / Title</label>
                            <input type="text" class="form-control form-control-sm" id="swal_edit_designation" value="${escapeHtml(sig.designation || '')}" placeholder="e.g. SUC President III">
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Office / Unit</label>
                            <input type="text" class="form-control form-control-sm" id="swal_edit_office" value="${escapeHtml(sig.office || '')}" placeholder="e.g. Office of the President">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">12-Col Width Span</label>
                            <select class="form-select form-select-sm font-mono fw-semibold text-primary" id="swal_edit_col_span">
                                <option value="12" ${sig.col_span == 12 ? 'selected' : ''}>12 cols (100% Full)</option>
                                <option value="8" ${sig.col_span == 8 ? 'selected' : ''}>8 cols (66% Two-Thirds)</option>
                                <option value="6" ${(!sig.col_span || sig.col_span == 6) ? 'selected' : ''}>6 cols (50% Half)</option>
                                <option value="4" ${sig.col_span == 4 ? 'selected' : ''}>4 cols (33% One-Third)</option>
                                <option value="3" ${sig.col_span == 3 ? 'selected' : ''}>3 cols (25% One-Fourth)</option>
                                <option value="2" ${sig.col_span == 2 ? 'selected' : ''}>2 cols (16% One-Sixth)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Text Alignment</label>
                            <select class="form-select form-select-sm" id="swal_edit_alignment">
                                <option value="left" ${(!sig.alignment || sig.alignment === 'left') ? 'selected' : ''}>Left Aligned</option>
                                <option value="center" ${sig.alignment === 'center' ? 'selected' : ''}>Centered</option>
                                <option value="right" ${sig.alignment === 'right' ? 'selected' : ''}>Right Aligned</option>
                            </select>
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="mdi mdi-content-save me-1"></i>Save Changes',
            confirmButtonColor: 'var(--dss-primary)',
            cancelButtonColor: '#64748b',
            width: 520,
            didOpen: () => {
                const userSel = document.getElementById('swal_edit_user_id');
                const desigWrapper = document.getElementById('swal_edit_desig_wrapper');
                const desigPicker = document.getElementById('swal_edit_desig_picker');
                const countBadge = document.getElementById('swal_desig_count_badge');
                const desigInput = document.getElementById('swal_edit_designation');
                const officeInput = document.getElementById('swal_edit_office');

                function syncSwalUser(userId, keepCurrentText) {
                    if (!userId) {
                        if (desigWrapper) desigWrapper.style.display = 'none';
                        return;
                    }
                    const user = allUsers.find(u => String(u.id) === String(userId));
                    if (!user) return;
                    const list = user.designations_list || [];

                    if (!keepCurrentText && list.length > 0) {
                        if (desigInput) desigInput.value = list[0].designation || '';
                        if (officeInput) officeInput.value = list[0].office || '';
                    }

                    if (list.length > 1) {
                        if (desigWrapper) desigWrapper.style.display = 'block';
                        if (countBadge) countBadge.textContent = `${list.length} Titles`;
                        if (desigPicker) {
                            let opts = '';
                            let matchIdx = 0;
                            list.forEach((item, idx) => {
                                const isCur = (desigInput && desigInput.value && desigInput.value.toLowerCase() === (item.designation || '').toLowerCase());
                                if (isCur) matchIdx = idx;
                                const label = `${item.designation}${item.office ? ' (' + item.office + ')' : ''}${item.is_primary ? ' [Primary]' : ''}`;
                                opts += `<option value="${idx}" ${isCur ? 'selected' : ''}>${escapeHtml(label)}</option>`;
                            });
                            desigPicker.innerHTML = opts;
                            desigPicker.selectedIndex = matchIdx;
                            desigPicker.onchange = function() {
                                const selItem = list[parseInt(this.value, 10)];
                                if (selItem) {
                                    if (desigInput) desigInput.value = selItem.designation || '';
                                    if (officeInput) officeInput.value = selItem.office || '';
                                }
                            };
                        }
                    } else {
                        if (desigWrapper) desigWrapper.style.display = 'none';
                    }
                }

                if (userSel) {
                    userSel.addEventListener('change', function() {
                        syncSwalUser(this.value, false);
                    });
                    syncSwalUser(userSel.value, true);
                }
            },
            preConfirm: () => {
                const userSel = document.getElementById('swal_edit_user_id');
                const userId = userSel ? userSel.value : '';
                const roleLabel = document.getElementById('swal_edit_role_label').value.trim();

                if (!userId) {
                    Swal.showValidationMessage('Please select a registered system user (user.id).');
                    return false;
                }
                if (!roleLabel) {
                    Swal.showValidationMessage('Role Label is required.');
                    return false;
                }

                return {
                    user_id: userId,
                    role_label: roleLabel,
                    designation: document.getElementById('swal_edit_designation').value.trim(),
                    office: document.getElementById('swal_edit_office').value.trim(),
                    col_span: parseInt(document.getElementById('swal_edit_col_span').value, 10) || 6,
                    alignment: document.getElementById('swal_edit_alignment').value || 'left'
                };
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const data = result.value;

            fetch(`/dss/settings/institution/signatory-template/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': pageToken
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(res => {
                if (res.result && res.signatory) {
                    const idx = allSignatories.findIndex(s => s.id === id);
                    if (idx !== -1) {
                        allSignatories[idx] = res.signatory;
                    }
                    renderSignatoriesLive();

                    const toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1600,
                        timerProgressBar: false
                    });
                    toast.fire({
                        icon: 'success',
                        title: 'Signatory information updated!'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update signatory.' });
                }
            });
        });
    }

    // ==========================================
    // TEMPLATE SETTINGS (Greeting, Closing, Banner)
    // ==========================================

    function saveTemplateSettings() {
        const payload = {
            document_type:     currentDocType,
            greeting:          document.getElementById('ctl_greeting').value,
            closing:           document.getElementById('ctl_closing').value,
            core_values:       document.getElementById('ctl_corevals').value,
            signatory_columns: 2,
            signatory_layout:  'grid12',
        };

        fetch("{{ route('dss.settings.institution.document_settings') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': pageToken },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Template Configuration Saved!',
                    text: d.message || 'Settings successfully saved for ' + currentDocType,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 1800,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save settings.' });
            }
        });
    }

    // ==========================================
    // AUTO-SELECT DESIGNATION & MULTIPLE DESIGNATIONS PICKER
    // ==========================================

    function onSignatoryUserSelected(userId) {
        const wrapper = document.getElementById('ctl_multiple_designations_wrapper');
        const picker = document.getElementById('ctl_designation_picker');
        const countBadge = document.getElementById('ctl_desig_count_badge');
        const desigInput = document.getElementById('ctl_designation');
        const officeInput = document.getElementById('ctl_office');
        const roleInput = document.getElementById('ctl_role_label');

        if (!userId) {
            if (wrapper) wrapper.style.display = 'none';
            return;
        }

        const user = allUsers.find(u => String(u.id) === String(userId));
        if (!user) return;

        const list = user.designations_list || [];

        // Auto-fill role label suggestion if empty
        if (roleInput && !roleInput.value.trim()) {
            const roleLower = (user.role || '').toLowerCase();
            if (roleLower.includes('president')) {
                roleInput.value = 'Approved by:';
            } else if (roleLower.includes('director') || roleLower.includes('admin') || roleLower.includes('dean')) {
                roleInput.value = 'Recommending Approval:';
            } else {
                roleInput.value = 'Prepared by:';
            }
        }

        if (list.length > 0) {
            const primary = list[0];
            if (desigInput) desigInput.value = primary.designation || '';
            if (officeInput) officeInput.value = primary.office || '';

            if (list.length > 1) {
                if (wrapper) wrapper.style.display = 'block';
                if (countBadge) countBadge.textContent = `${list.length} Titles`;
                if (picker) {
                    let opts = '';
                    list.forEach((item, idx) => {
                        const label = `${item.designation}${item.office ? ' (' + item.office + ')' : ''}${item.is_primary ? ' [Primary]' : ''}`;
                        opts += `<option value="${idx}">${escapeHtml(label)}</option>`;
                    });
                    picker.innerHTML = opts;
                    picker.selectedIndex = 0;
                }
            } else {
                if (wrapper) wrapper.style.display = 'none';
            }
        } else {
            if (desigInput) desigInput.value = user.designation || user.role || '';
            if (officeInput) officeInput.value = (user.department && user.department.unit_name) ? user.department.unit_name : '';
            if (wrapper) wrapper.style.display = 'none';
        }
    }

    function onDesignationPicked(index) {
        const userId = document.getElementById('ctl_user_id')?.value;
        if (!userId) return;

        const user = allUsers.find(u => String(u.id) === String(userId));
        if (!user || !user.designations_list) return;

        const selected = user.designations_list[parseInt(index, 10)];
        if (!selected) return;

        const desigInput = document.getElementById('ctl_designation');
        const officeInput = document.getElementById('ctl_office');

        if (desigInput) desigInput.value = selected.designation || '';
        if (officeInput) officeInput.value = selected.office || '';
    }

    // ==========================================
    // ADD SIGNATORY RULE (Strictly user.id + col_span + alignment)
    // ==========================================

    function addSignatoryRule() {
        const userSel = document.getElementById('ctl_user_id');
        const userId = userSel ? userSel.value : '';
        const opt = userSel ? userSel.options[userSel.selectedIndex] : null;
        const employeeName = (opt && opt.dataset.name) ? opt.dataset.name : '';
        const roleLabel = document.getElementById('ctl_role_label').value.trim();
        const colSpan = parseInt(document.getElementById('ctl_colspan').value, 10) || 6;
        const alignment = document.getElementById('ctl_align') ? document.getElementById('ctl_align').value : 'left';
        const sortOrder = parseInt(document.getElementById('ctl_sort').value, 10) || (getCurrentDocSignatories().length + 1);

        if (!userId) {
            Swal.fire({
                icon: 'warning',
                title: 'User Selection Required',
                text: 'Please select a registered system user (user.id) for this signatory rule.',
                confirmButtonColor: 'var(--dss-primary)'
            });
            return;
        }

        if (!roleLabel) {
            Swal.fire({
                icon: 'warning',
                title: 'Role Label Required',
                text: 'Please specify the role label (e.g., Prepared by:, Recommending Approval:, or Approved by:).',
                confirmButtonColor: 'var(--dss-primary)'
            });
            return;
        }

        const payload = {
            document_type:       currentDocType,
            user_id:             userId,
            role_label:          roleLabel,
            employee_name:       employeeName,
            designation:         document.getElementById('ctl_designation').value,
            office:              document.getElementById('ctl_office').value,
            col_span:            colSpan,
            alignment:           alignment,
            position_column:     1,
            sort_order:          sortOrder,
            show_signature_line: true,
        };

        fetch("{{ route('dss.settings.institution.signatory_template') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': pageToken },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(d => {
            if (d.result && d.signatory) {
                allSignatories.push(d.signatory);
                renderSignatoriesLive();

                // Clear input fields
                document.getElementById('ctl_user_id').value = '';
                document.getElementById('ctl_role_label').value = '';
                document.getElementById('ctl_designation').value = '';
                document.getElementById('ctl_office').value = '';
                document.getElementById('ctl_sort').value = getCurrentDocSignatories().length + 1;
                const desigWrapper = document.getElementById('ctl_multiple_designations_wrapper');
                if (desigWrapper) desigWrapper.style.display = 'none';

                Swal.fire({
                    icon: 'success',
                    title: 'Signatory Appended to Canvas!',
                    text: `Signatory placed with width ${colSpan}/12 cols on ${currentDocType}`,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to add signatory rule.' });
            }
        });
    }

    // ==========================================
    // REMOVE SIGNATORY RULE
    // ==========================================

    function removeSignatoryRule(id) {
        Swal.fire({
            title: 'Remove this signatory rule?',
            text: 'This signatory will no longer appear on ' + currentDocType + ' documents.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, remove'
        })
        .then(r => {
            if (!r.isConfirmed) return;
            fetch(`/dss/settings/institution/signatory-template/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': pageToken }
            })
            .then(r => r.json())
            .then(d => {
                if (d.result) {
                    allSignatories = allSignatories.filter(s => s.id !== id);
                    renderSignatoriesLive();
                }
            });
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ==========================================
    // SYNC TYPOGRAPHY, LINE HEIGHT & SPACING
    // ==========================================

    function syncSheetTypography() {
        const fontVal = document.getElementById('ctl_font')?.value || 'Arial';
        const lineHeightVal = parseFloat(document.getElementById('ctl_lineheight')?.value || '1.5');
        const paraSpacingVal = parseFloat(document.getElementById('ctl_paraspacing')?.value || '3.0');

        const sheet = document.getElementById('actualPolicySheet');
        if (sheet) {
            if (fontVal === 'Times') {
                sheet.style.fontFamily = '"Times New Roman", Times, Georgia, serif';
            } else if (fontVal === 'Courier') {
                sheet.style.fontFamily = '"Courier New", Courier, monospace';
            } else if (fontVal === 'Helvetica') {
                sheet.style.fontFamily = '"Helvetica Neue", Helvetica, Arial, sans-serif';
            } else {
                sheet.style.fontFamily = 'Arial, Helvetica, sans-serif';
            }
            sheet.style.lineHeight = lineHeightVal;

            const clauses = sheet.querySelectorAll('.section-clause');
            clauses.forEach(cl => {
                cl.style.lineHeight = lineHeightVal;
                cl.style.marginBottom = `${paraSpacingVal}mm`;
            });
        }

        renderSignatoriesLive();
    }

    // ==========================================
    // INSTITUTIONAL LOGO LIBRARY & MULTI-SLOT DRAG-AND-DROP ENGINE
    // ==========================================

    var activeDraggedLogoId = null;

    function renderLogoLibraryTray() {
        const container = document.getElementById('logoLibraryChipsContainer');
        if (!container) return;

        if (!window.availableLogos || window.availableLogos.length === 0) {
            container.innerHTML = `<span class="text-muted extra-small fst-italic">No logos registered yet. Click "Upload Logo" to upload institution seals or certification badges.</span>`;
            return;
        }

        let html = '';
        window.availableLogos.forEach(logo => {
            const catColors = {
                seal: 'bg-primary-subtle text-primary border-primary-subtle',
                national: 'bg-danger-subtle text-danger border-danger-subtle',
                certification: 'bg-info-subtle text-info border-info-subtle',
                academic: 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                custom: 'bg-success-subtle text-success border-success-subtle'
            };
            const badgeClass = catColors[logo.category] || 'bg-secondary-subtle text-dark border-secondary-subtle';

            html += `
                <div class="logo-library-chip" draggable="true"
                     ondragstart="onLogoChipDragStart(event, '${escapeHtml(logo.id)}')"
                     ondragend="onLogoChipDragEnd(event)"
                     onclick="openQuickAssignModal('${escapeHtml(logo.id)}')"
                     title="Drag this logo onto any Header or Footer slot, or Click to Assign">
                    <img src="${logo.url}" alt="${escapeHtml(logo.label)}">
                    <div class="d-flex flex-column text-start">
                        <span class="fw-bold text-dark font-sans" style="font-size: 11px; line-height: 1.2;">
                            ${escapeHtml(logo.label)}
                        </span>
                        <span class="badge ${badgeClass} border font-mono" style="font-size: 8px; width: fit-content;">
                            ${escapeHtml(logo.category || 'custom')}
                        </span>
                    </div>
                    <i class="mdi mdi-drag text-muted ms-1" style="font-size: 14px;"></i>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function onLogoChipDragStart(e, logoId) {
        activeDraggedLogoId = logoId;
        if (e.dataTransfer) {
            e.dataTransfer.setData('text/plain', logoId);
            e.dataTransfer.effectAllowed = 'copyMove';
        }
        document.querySelectorAll('.logo-drop-slot').forEach(s => s.classList.add('slot-active'));
    }

    function onLogoChipDragEnd(e) {
        activeDraggedLogoId = null;
        document.querySelectorAll('.logo-drop-slot').forEach(s => {
            s.classList.remove('slot-active', 'drag-over-slot');
        });
    }

    function onSlotDragOver(e) {
        e.preventDefault();
        if (e.dataTransfer) {
            e.dataTransfer.dropEffect = 'copy';
        }
        const target = e.currentTarget;
        if (target && !target.classList.contains('drag-over-slot')) {
            target.classList.add('drag-over-slot');
        }
    }

    function onSlotDragLeave(e) {
        const target = e.currentTarget;
        if (target) {
            target.classList.remove('drag-over-slot');
        }
    }

    function onSlotDrop(e, slotName) {
        e.preventDefault();
        onLogoChipDragEnd(e);

        const logoId = e.dataTransfer ? e.dataTransfer.getData('text/plain') : activeDraggedLogoId;
        if (!logoId) return;

        setSlotLogo(slotName, logoId);
    }

    function toCamel(s) {
        return s.replace(/_([a-z0-9])/g, g => g[1].toUpperCase());
    }

    function setSlotLogo(slotName, logoId) {
        const prop = toCamel(slotName);
        window[prop] = logoId;

        const logo = getLogoObj(logoId);
        const label = logo ? logo.label : logoId;

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: false
        });
        toast.fire({
            icon: 'success',
            title: `Assigned: ${label}`
        });

        if (slotName.startsWith('header')) {
            syncSheetHeader();
        } else {
            syncSheetFooter();
        }
    }

    function clearSlot(slotName, e) {
        if (e) e.stopPropagation();
        const prop = toCamel(slotName);
        window[prop] = null;

        if (slotName.startsWith('header')) {
            syncSheetHeader();
        } else {
            syncSheetFooter();
        }

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: false
        });
        toast.fire({
            icon: 'info',
            title: 'Slot cleared'
        });
    }

    // ==========================================
    // UPLOAD LOGO MODAL (Multipart Direct Upload)
    // ==========================================

    function openUploadLogoModal(targetSlot = null) {
        Swal.fire({
            title: '<div class="fs-5 fw-bold text-dark"><i class="mdi mdi-cloud-upload me-1 text-primary"></i>Upload Institutional Logo</div>',
            html: `
                <div class="text-start" style="font-family: var(--dss-font-sans, inherit);">
                    <div class="mb-2.5">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Logo Image File (PNG, JPG, WEBP, SVG) <span class="text-danger">*</span>
                        </label>
                        <input type="file" id="swal_logo_file" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        <div class="extra-small text-muted mt-0.5">High-resolution PNG or SVG is recommended for sharp print output.</div>
                    </div>
                    <div class="mb-2.5">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Logo Title / Label <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="swal_logo_label" class="form-control form-control-sm" placeholder="e.g. College of Nursing, Bagong Pilipinas, ISO 9001:2015">
                    </div>
                    <div class="mb-2.5">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Classification Category
                        </label>
                        <select id="swal_logo_category" class="form-select form-select-sm">
                            <option value="custom" selected>Institutional / Custom Badge</option>
                            <option value="academic">College / Department Unit</option>
                            <option value="certification">Quality &amp; Accreditation (ISO / AACCUP / PAB)</option>
                            <option value="national">National Agency / Flagship Program</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="mdi mdi-check me-1"></i>Upload and Add to Library',
            confirmButtonColor: 'var(--dss-primary)',
            focusConfirm: false,
            preConfirm: () => {
                const fileInput = document.getElementById('swal_logo_file');
                const labelInput = document.getElementById('swal_logo_label');
                const catInput = document.getElementById('swal_logo_category');

                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    Swal.showValidationMessage('Please select an image file to upload.');
                    return false;
                }
                const label = labelInput ? labelInput.value.trim() : '';
                if (!label) {
                    Swal.showValidationMessage('Please provide a title or label for this logo.');
                    return false;
                }
                return {
                    file: fileInput.files[0],
                    label: label,
                    category: catInput ? catInput.value : 'custom'
                };
            }
        }).then(res => {
            if (!res.isConfirmed || !res.value) return;

            const fd = new FormData();
            fd.append('logo', res.value.file);
            fd.append('label', res.value.label);
            fd.append('category', res.value.category);

            Swal.fire({
                title: 'Uploading Logo...',
                text: 'Storing file into institutional branding storage and registering asset.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("{{ route('dss.settings.institution.upload_template_logo') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': pageToken
                },
                body: fd
            })
            .then(r => r.json())
            .then(d => {
                if (d.result && d.logo) {
                    window.availableLogos.push(d.logo);
                    renderLogoLibraryTray();

                    if (targetSlot) {
                        setSlotLogo(targetSlot, d.logo.id);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Logo Added to Library!',
                        text: d.message || 'Logo successfully uploaded and registered into your institutional library.',
                        confirmButtonColor: 'var(--dss-primary)',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Upload Failed', text: d.message || 'Could not upload logo.' });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
            });
        });
    }

    // ==========================================
    // SLOT PICKER MODAL (Click to Assign)
    // ==========================================

    function openSlotPickerModal(slotName) {
        const slotLabels = {
            footer_left_slot_1: 'Footer Left Slot 1 (Primary Seal)',
            footer_left_slot_2: 'Footer Left Slot 2 (Magkatabi / Auxiliary Seal)',
            footer_right_slot_1: 'Footer Right Slot 1 (ISO / Quality Badge)',
            footer_right_slot_2: 'Footer Right Slot 2 (PAB / Accreditation Badge)',
            header_left_slot_1: 'Header Left Slot 1',
            header_left_slot_2: 'Header Left Slot 2 (Magkatabi)',
            header_right_slot_1: 'Header Right Slot 1'
        };

        const currentVal = window[toCamel(slotName)] || null;

        let logosHtml = '<div class="row g-2">';
        (window.availableLogos || []).forEach(logo => {
            const isSelected = (currentVal === logo.id);
            logosHtml += `
                <div class="col-6">
                    <div class="p-2 border rounded d-flex align-items-center gap-2 cursor-pointer transition-all ${isSelected ? 'border-primary bg-primary-subtle' : 'bg-white hover-shadow'}"
                         onclick="setSlotLogo('${slotName}', '${escapeHtml(logo.id)}'); Swal.close();"
                         style="cursor: pointer;">
                        <img src="${logo.url}" alt="${escapeHtml(logo.label)}" style="width: 32px; height: 32px; object-fit: contain; background: #fff; border-radius: 4px; border: 1px solid #e2e8f0;">
                        <div class="d-flex flex-column text-start overflow-hidden">
                            <span class="fw-bold text-dark text-truncate" style="font-size: 11px;">${escapeHtml(logo.label)}</span>
                            <span class="badge bg-light text-muted border font-mono" style="font-size: 8px; width: fit-content;">${escapeHtml(logo.category || 'logo')}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        logosHtml += '</div>';

        Swal.fire({
            title: `<div class="fs-5 fw-bold text-dark"><i class="mdi mdi-image-search me-1 text-primary"></i>Choose Logo for ${slotLabels[slotName] || slotName}</div>`,
            html: `
                <div class="text-start" style="font-family: var(--dss-font-sans, inherit);">
                    <p class="extra-small text-muted mb-2.5">Select a logo or certification badge from your library to place into this slot:</p>
                    ${logosHtml}
                    <div class="mt-3 pt-2.5 border-top d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="clearSlot('${slotName}'); Swal.close();">
                            <i class="mdi mdi-close-circle me-1"></i>Clear this Slot
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="Swal.close(); openUploadLogoModal('${slotName}');">
                            <i class="mdi mdi-cloud-upload me-1"></i>+ Upload New Logo
                        </button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: 580
        });
    }

    function openQuickAssignModal(logoId) {
        const logo = getLogoObj(logoId);
        if (!logo) return;

        Swal.fire({
            title: `<div class="fs-6 fw-bold text-dark"><i class="mdi mdi-target me-1 text-primary"></i>Place Logo: ${escapeHtml(logo.label)}</div>`,
            html: `
                <div class="text-start extra-small" style="font-family: var(--dss-font-sans, inherit);">
                    <p class="text-muted mb-2">Select where on the document you want to position this logo:</p>
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('footer_left_slot_1', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-footer me-1.5 text-primary"></i><strong>Footer Left Slot 1</strong> (Primary Seal)</span>
                            <span class="badge bg-light text-dark border">Left 1</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('footer_left_slot_2', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-footer me-1.5 text-success"></i><strong>Footer Left Slot 2</strong> (Magkatabi / Side-by-Side)</span>
                            <span class="badge bg-success-subtle text-success border">Left 2</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('footer_right_slot_1', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-footer me-1.5 text-info"></i><strong>Footer Right Slot 1</strong> (ISO / Certification)</span>
                            <span class="badge bg-info-subtle text-info border">Right 1</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('footer_right_slot_2', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-footer me-1.5 text-warning"></i><strong>Footer Right Slot 2</strong> (PAB / Accreditation)</span>
                            <span class="badge bg-warning-subtle text-dark border">Right 2</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('header_left_slot_1', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-header me-1.5 text-primary"></i><strong>Header Left Slot 1</strong> (Primary Header)</span>
                            <span class="badge bg-light text-dark border">Header L1</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('header_left_slot_2', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-header me-1.5 text-success"></i><strong>Header Left Slot 2</strong> (Dual Header Magkatabi)</span>
                            <span class="badge bg-success-subtle text-success border">Header L2</span>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2" onclick="setSlotLogo('header_right_slot_1', '${logoId}'); Swal.close();">
                            <span><i class="mdi mdi-page-layout-header me-1.5 text-secondary"></i><strong>Header Right Slot 1</strong> (Dual Split Header)</span>
                            <span class="badge bg-light text-dark border">Header R1</span>
                        </button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: 480
        });
    }

    // Helper: Build a visual slot HTML element for on-sheet rendering
    function buildVisualSlotHtml(slotName, labelTag, slotWidthPx, slotHeightPx) {
        const logoId = window[toCamel(slotName)] || null;
        const logo = getLogoObj(logoId);

        let content = '';
        if (logo) {
            content = `
                <span class="slot-label-tag">${labelTag}</span>
                <img src="${logo.url}" alt="${escapeHtml(logo.label)}"
                     style="max-width: ${slotWidthPx - 10}px; max-height: ${slotHeightPx - 16}px; object-fit: contain;">
                <button type="button" class="slot-action-btn" onclick="clearSlot('${slotName}', event)" title="Clear this slot">&times;</button>
                <button type="button" class="slot-pick-btn" onclick="openSlotPickerModal('${slotName}')" title="Change logo"><i class="mdi mdi-pencil" style="font-size: 9px;"></i></button>
            `;
        } else {
            content = `
                <span class="slot-label-tag">${labelTag}</span>
                <span class="text-muted extra-small" style="font-size: 8.5px; opacity: 0.65;"><i class="mdi mdi-plus"></i> Drop/Click</span>
            `;
        }

        return `
            <div class="logo-drop-slot ${slotName.startsWith('header') ? 'header-drop-slot' : 'footer-drop-slot'} d-flex flex-column align-items-center justify-content-center"
                 style="width: ${slotWidthPx}px; min-height: ${slotHeightPx}px; cursor: pointer;"
                 ondragover="onSlotDragOver(event)" ondragleave="onSlotDragLeave(event)" ondrop="onSlotDrop(event, '${slotName}')"
                 onclick="openSlotPickerModal('${slotName}')"
                 title="${labelTag} (Drag a logo here or click to pick)">
                ${content}
            </div>
        `;
    }

    function setTemplateLogoLayout(layout) {
        const ctl = document.getElementById('ctl_logo_layout');
        if (ctl) ctl.value = layout;
        syncSheetHeader();

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1400,
            timerProgressBar: false
        });
        const names = {
            centered: 'Centered Seal',
            dual_left: 'Dual Left (Magkatabi)',
            left: 'Left-Aligned Seal',
            right: 'Right-Aligned Seal',
            split_dual: 'Split Dual Logos',
            iso_box: 'ISO Form Quality Box'
        };
        toast.fire({
            icon: 'success',
            title: `Header: ${names[layout] || layout}`
        });
    }

    function setTemplateFooterLogoPosition(pos) {
        const ctl = document.getElementById('ctl_footer_logo_position');
        const showCtl = document.getElementById('ctl_show_footer_logo');
        if (ctl) ctl.value = pos;
        if (showCtl) showCtl.checked = (pos !== 'none');

        // Auto-configure slot presets if activating dual_left or dual_left_and_right
        if (pos === 'dual_left') {
            if (!window.footerLeftSlot1) window.footerLeftSlot1 = 'seal';
            if (!window.footerLeftSlot2) window.footerLeftSlot2 = (window.secondaryLogoUrl ? 'secondary' : (window.availableLogos[1]?.id || 'iso'));
        } else if (pos === 'dual_left_and_right') {
            if (!window.footerLeftSlot1) window.footerLeftSlot1 = 'seal';
            if (!window.footerLeftSlot2) window.footerLeftSlot2 = (window.secondaryLogoUrl ? 'secondary' : (window.availableLogos[1]?.id || 'iso'));
            if (!window.footerRightSlot1) window.footerRightSlot1 = (window.isoLogoUrl ? 'iso' : (window.availableLogos[2]?.id || 'pab'));
        }

        syncSheetFooter();

        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1400,
            timerProgressBar: false
        });
        const names = {
            dual_left: 'Dual Left - Magkatabi',
            dual_left_and_right: 'Dual Left + Right',
            left: 'Footer Left Seal',
            center: 'Footer Centered Seal',
            right: 'Footer Right Seal',
            split_dual: 'Split Dual Seals',
            none: 'Clean Footer (Text Only)'
        };
        toast.fire({
            icon: 'success',
            title: `Footer: ${names[pos] || pos}`
        });
    }

    // ==========================================
    // SYNC HEADER & LOGO PLACEMENT (FPDF SIMULATOR)
    // ==========================================

    function syncSheetHeader() {
        const layout = document.getElementById('ctl_logo_layout')?.value || 'centered';
        const logoSize = parseInt(document.getElementById('ctl_logo_size')?.value || '24', 10);
        const topMargin = parseInt(document.getElementById('ctl_logo_top_margin')?.value || '10', 10);
        const divider = document.getElementById('ctl_divider_style')?.value || 'single_black';

        const wrapper = document.getElementById('sheetOfficialHeaderWrapper');
        const dividerEl = document.getElementById('sheetDividerLine');
        if (!wrapper) return;

        const pxSize = Math.round(logoSize * 3.78);
        const pxTop = Math.round(topMargin * 2.5);

        // 1. On-Sheet Header Placement Toolbar
        let toolbarHtml = `
            <div class="template-placement-toolbar">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary border font-mono" style="font-size: 9.5px;">
                        <i class="mdi mdi-page-layout-header me-1"></i>Header Layout
                    </span>
                    <div class="template-placement-btn-group btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary ${layout === 'dual_left' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('dual_left')" title="Dual Logos Side-by-Side on Left (Magkatabi)">
                            <i class="mdi mdi-view-parallel me-0.5"></i>Dual Left (Magkatabi)
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${layout === 'left' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('left')">
                            <i class="mdi mdi-format-align-left me-0.5"></i>Left
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${layout === 'centered' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('centered')">
                            <i class="mdi mdi-format-align-center me-0.5"></i>Center
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${layout === 'right' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('right')">
                            <i class="mdi mdi-format-align-right me-0.5"></i>Right
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${layout === 'split_dual' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('split_dual')">
                            <i class="mdi mdi-view-column me-0.5"></i>Split Dual
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${layout === 'iso_box' ? 'active-pos' : ''}" onclick="setTemplateLogoLayout('iso_box')">
                            <i class="mdi mdi-certificate-outline me-0.5"></i>ISO Box
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 fw-semibold" onclick="saveTemplateSettings()">
                        <i class="mdi mdi-content-save me-1"></i>Save Placement
                    </button>
                </div>
            </div>
        `;

        let headerBody = '';

        if (layout === 'dual_left') {
            // Dual logos side-by-side on the left (magkatabi)
            headerBody = `
                <div style="display: flex; align-items: center; gap: 12px; margin-top: ${pxTop}px;">
                    <div class="d-flex align-items-center gap-2">
                        ${buildVisualSlotHtml('header_left_slot_1', 'Logo 1', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                        ${buildVisualSlotHtml('header_left_slot_2', 'Logo 2 (Magkatabi)', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                    </div>
                    <div style="flex: 1; text-align: left;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <div class="header-univ-name text-uppercase fs-5" style="line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                        <div style="font-size: 11px; color: #475569;">${escapeHtml(window.address)}</div>
                        <div style="font-family: Arial, Helvetica, sans-serif; font-weight: 700; text-transform: uppercase; margin-top: 4px; font-size: 11.5px; color: #1e3a8a;">
                            Office of the University President
                        </div>
                    </div>
                </div>
            `;
        } else if (layout === 'split_dual') {
            headerBody = `
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: ${pxTop}px;">
                    ${buildVisualSlotHtml('header_left_slot_1', 'Left Logo', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <div class="header-univ-name text-uppercase fs-5" style="line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                        <div style="font-size: 11px; color: #475569;">${escapeHtml(window.address)}</div>
                        <div style="font-family: Arial, Helvetica, sans-serif; font-weight: 700; text-transform: uppercase; margin-top: 4px; font-size: 11.5px; color: #1e3a8a;">
                            Office of the University President
                        </div>
                    </div>
                    ${buildVisualSlotHtml('header_right_slot_1', 'Right Logo', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                </div>
            `;
        } else if (layout === 'left') {
            headerBody = `
                <div style="display: flex; align-items: center; gap: 15px; margin-top: ${pxTop}px;">
                    ${buildVisualSlotHtml('header_left_slot_1', 'Left Logo', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                    <div style="flex: 1; text-align: left;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <div class="header-univ-name text-uppercase fs-5" style="line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                        <div style="font-size: 11px; color: #475569;">${escapeHtml(window.address)}</div>
                        <div style="font-family: Arial, Helvetica, sans-serif; font-weight: 700; text-transform: uppercase; margin-top: 4px; font-size: 11.5px; color: #1e3a8a;">
                            Office of the University President
                        </div>
                    </div>
                </div>
            `;
        } else if (layout === 'right') {
            headerBody = `
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: ${pxTop}px;">
                    <div style="flex: 1; text-align: left;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <div class="header-univ-name text-uppercase fs-5" style="line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                        <div style="font-size: 11px; color: #475569;">${escapeHtml(window.address)}</div>
                        <div style="font-family: Arial, Helvetica, sans-serif; font-weight: 700; text-transform: uppercase; margin-top: 4px; font-size: 11.5px; color: #1e3a8a;">
                            Office of the University President
                        </div>
                    </div>
                    ${buildVisualSlotHtml('header_left_slot_1', 'Right Logo', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                </div>
            `;
        } else if (layout === 'iso_box') {
            headerBody = `
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: ${pxTop}px;">
                    ${buildVisualSlotHtml('header_left_slot_1', 'Left Logo', Math.max(pxSize, 56), Math.max(pxSize, 56))}
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <div class="header-univ-name text-uppercase" style="font-size: 15px; line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                        <div style="font-size: 10px; color: #475569;">${escapeHtml(window.address)}</div>
                    </div>
                    <div style="flex: 0 0 170px; border: 1px solid #000; font-size: 10px; text-align: left; background: #fff;">
                        <div style="padding: 2px 5px; border-bottom: 1px solid #000; display: flex; justify-content: space-between;"><strong>Doc No:</strong> <span>FM-SUC-OP-01</span></div>
                        <div style="padding: 2px 5px; border-bottom: 1px solid #000; display: flex; justify-content: space-between;"><strong>Effective:</strong> <span>Oct 14, 2026</span></div>
                        <div style="padding: 2px 5px; display: flex; justify-content: space-between;"><strong>Rev No:</strong> <span>1</span></div>
                    </div>
                </div>
            `;
        } else {
            // Centered
            headerBody = `
                <div style="text-align: center; margin-top: ${pxTop}px;">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        ${buildVisualSlotHtml('header_left_slot_1', 'Centered Seal', Math.max(pxSize, 60), Math.max(pxSize, 60))}
                    </div>
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                    <div class="header-univ-name text-uppercase fs-5" style="line-height: 1.2;">${escapeHtml(window.schoolName)}</div>
                    <div style="font-size: 11px; color: #475569;">${escapeHtml(window.address)}</div>
                    <div style="font-family: Arial, Helvetica, sans-serif; font-weight: 700; text-transform: uppercase; margin-top: 5px; font-size: 11.5px; letter-spacing: 0.5px; color: #1e3a8a;">
                        Office of the University President
                    </div>
                </div>
            `;
        }

        wrapper.innerHTML = toolbarHtml + headerBody;

        // Divider Line
        if (dividerEl) {
            if (divider === 'double_rule') {
                dividerEl.innerHTML = '<div style="border-top: 2px solid #000; margin-bottom: 2px;"></div><div style="border-top: 1px solid #000; margin-bottom: 14px;"></div>';
            } else if (divider === 'theme_accent') {
                dividerEl.innerHTML = '<div style="border-top: 3px solid var(--dss-primary, #059669); margin-bottom: 14px;"></div>';
            } else if (divider === 'none') {
                dividerEl.innerHTML = '<div style="margin-bottom: 14px;"></div>';
            } else {
                dividerEl.innerHTML = '<hr style="border-top: 2px solid #000; opacity: 1; margin: 0 0 14px 0;">';
            }
        }
    }

    // ==========================================
    // SYNC FOOTER & MULTI-SLOT PLACEMENT (FPDF SIMULATOR)
    // ==========================================

    function syncSheetFooter() {
        const footerPos = document.getElementById('ctl_footer_logo_position')?.value || 'left';
        const showFoot = document.getElementById('ctl_show_footer_logo')?.checked ?? true;
        const coreVals = document.getElementById('ctl_corevals')?.value || 'Accountability | Responsiveness | Excellence';

        const wrapper = document.getElementById('sheetOfficialFooterWrapper');
        if (!wrapper) return;

        // 1. On-Sheet Footer Placement Toolbar
        let toolbarHtml = `
            <div class="template-placement-toolbar mb-3">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="badge bg-secondary-subtle text-dark border font-mono" style="font-size: 9.5px;">
                        <i class="mdi mdi-page-layout-footer me-1"></i>Footer Placement
                    </span>
                    <div class="template-placement-btn-group btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'dual_left' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('dual_left')" title="Two logos side-by-side on the left margin (as used in SUC templates)">
                            <i class="mdi mdi-view-parallel me-0.5"></i>Dual Left (Magkatabi)
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'dual_left_and_right' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('dual_left_and_right')" title="Two on Left, One on Right">
                            <i class="mdi mdi-page-layout-footer me-0.5"></i>Dual Left + Right
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'split_dual' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('split_dual')">
                            <i class="mdi mdi-view-column me-0.5"></i>Split Dual
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'left' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('left')">
                            <i class="mdi mdi-format-align-left me-0.5"></i>Left Seal
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'center' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('center')">
                            <i class="mdi mdi-format-align-center me-0.5"></i>Center Seal
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'right' && showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('right')">
                            <i class="mdi mdi-format-align-right me-0.5"></i>Right Seal
                        </button>
                        <button type="button" class="btn btn-outline-secondary ${footerPos === 'none' || !showFoot ? 'active-pos' : ''}" onclick="setTemplateFooterLogoPosition('none')">
                            <i class="mdi mdi-cancel me-0.5"></i>Text Only
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 fw-semibold" onclick="saveTemplateSettings()">
                        <i class="mdi mdi-content-save me-1"></i>Save Placement
                    </button>
                </div>
            </div>
        `;

        // Read admin footer config — independent position per element
        const barcodePos = document.getElementById('ctl_barcode_position')?.value || window.barcodePosition || 'above_right';
        const qrPos      = document.getElementById('ctl_qr_position')?.value      || window.qrPosition     || 'above_right';
        const showAddr   = document.getElementById('ctl_show_footer_address')?.checked  ?? (window.showFooterAddress !== false);
        const showCont   = document.getElementById('ctl_show_footer_contact')?.checked  ?? (window.showFooterContact !== false);
        const showWeb    = document.getElementById('ctl_show_footer_website')?.checked   ?? (window.showFooterWebsite !== false);
        const showCV     = document.getElementById('ctl_show_core_values')?.checked      ?? (window.showCoreValues !== false);
        const showDist   = document.getElementById('ctl_show_distribution')?.checked     ?? (window.showDistribution !== false);
        const distText   = document.getElementById('ctl_distribution_text')?.value       || window.distributionText || '';

        // Helper: renders a compact barcode/QR chip for placing inline in footer zones
        const barcodeChipHtml = (barcodePos !== 'none') ? `
            <div class="text-center" style="display:inline-block;">
                ${window.sampleBarcodeBase64 ? `<img src="data:image/png;base64,${window.sampleBarcodeBase64}" alt="Barcode" style="height:22px;max-width:120px;display:block;margin:0 auto 1px auto;">` : '<div style="height:22px;width:110px;background:#e2e8f0;border-radius:2px;"></div>'}
                <div class="font-mono fw-bold" style="font-size:7px;letter-spacing:0.5px;">${escapeHtml(window.sampleTrackingNumber||'DTS-SAMPLE')}</div>
                <div style="font-size:6.5px;color:#64748b;">Barcode</div>
            </div>` : '';

        const qrChipHtml = (qrPos !== 'none') ? `
            <div class="qr-stamp-box" style="display:inline-block;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=${encodeURIComponent(window.sampleTrackUrl||'')}" alt="QR" width="44" height="44">
                <div style="font-size:6.5px;color:#64748b;margin-top:1px;">QR</div>
            </div>` : '';

        // Build ABOVE-RULE row: collect elements that want above_left / above_center / above_right
        const aboveLeft   = [barcodePos==='above_left'?barcodeChipHtml:'', qrPos==='above_left'?qrChipHtml:''].filter(Boolean).join(' ');
        const aboveCenter = [barcodePos==='above_center'?barcodeChipHtml:'', qrPos==='above_center'?qrChipHtml:''].filter(Boolean).join(' ');
        const aboveRight  = [barcodePos==='above_right'?barcodeChipHtml:'', qrPos==='above_right'?qrChipHtml:''].filter(Boolean).join(' ');

        let barcodeRowHtml = '';
        if (aboveLeft || aboveCenter || aboveRight) {
            barcodeRowHtml = `
                <div class="d-flex align-items-end justify-content-between mb-2" style="min-height:52px;">
                    <div class="d-flex align-items-end gap-2">${aboveLeft}</div>
                    <div class="d-flex align-items-end gap-2">${aboveCenter}</div>
                    <div class="d-flex align-items-end gap-2">${aboveRight}</div>
                </div>
            `;
        }

        // Build FOOTER INLINE elements (go into left/center/right footer zones)
        const footerLeftStamp   = [barcodePos==='footer_left'?barcodeChipHtml:'', qrPos==='footer_left'?qrChipHtml:''].filter(Boolean).join(' ');
        const footerCenterStamp = [barcodePos==='footer_center'?barcodeChipHtml:'', qrPos==='footer_center'?qrChipHtml:''].filter(Boolean).join(' ');
        const footerRightStamp  = [barcodePos==='footer_right'?barcodeChipHtml:'', qrPos==='footer_right'?qrChipHtml:''].filter(Boolean).join(' ');

        // 3. Footer Divider Rule
        let ruleHtml = `<hr style="border-top: 1.5px solid #000; opacity: 1; margin: 4px 0 8px 0;">`;

        // Footer slots: Left, Center, Right — with inline stamp zones injected
        const isNone   = (footerPos === 'none' || !showFoot);
        const isCenter = (footerPos === 'center' && showFoot);

        let leftSlotsHtml = '';
        let rightSlotsHtml = '';

        if (!isNone && !isCenter) {
            leftSlotsHtml = `
                <div class="d-flex align-items-center gap-2" style="flex: 0 0 auto;">
                    ${buildVisualSlotHtml('footer_left_slot_1', 'Left Slot 1', 52, 52)}
                    ${(footerPos === 'dual_left' || footerPos === 'dual_left_and_right' || window.footerLeftSlot2) ? buildVisualSlotHtml('footer_left_slot_2', 'Left Slot 2 (Magkatabi)', 52, 52) : ''}
                    ${footerLeftStamp ? `<div class="d-flex align-items-center gap-1">${footerLeftStamp}</div>` : ''}
                </div>
            `;
            if (footerPos === 'right' || footerPos === 'split_dual' || footerPos === 'dual_left_and_right' || footerPos === 'dual_right' || window.footerRightSlot1 || window.footerRightSlot2 || footerRightStamp) {
                rightSlotsHtml = `
                    <div class="d-flex align-items-center gap-2" style="flex: 0 0 auto;">
                        ${footerRightStamp ? `<div class="d-flex align-items-center gap-1">${footerRightStamp}</div>` : ''}
                        ${buildVisualSlotHtml('footer_right_slot_1', 'Right Slot 1', 52, 52)}
                        ${(window.footerRightSlot2 || footerPos === 'dual_right') ? buildVisualSlotHtml('footer_right_slot_2', 'Right Slot 2', 52, 52) : ''}
                    </div>
                `;
            }
        }

        // Center info block — each line individually togglable + optional center stamp
        let centerContentHtml = `
            <div class="text-center" style="flex: 1; padding: 0 8px;">
                ${isCenter ? `<div class="mb-1">${buildVisualSlotHtml('footer_left_slot_1', 'Center Seal', 52, 52)}</div>` : ''}
                ${footerCenterStamp ? `<div class="mb-1 d-flex align-items-center justify-content-center gap-2">${footerCenterStamp}</div>` : ''}
                ${(showAddr && window.address)    ? `<div style="font-size: 9px; color: #1e293b; font-weight: 500;">${escapeHtml(window.address)}</div>` : ''}
                ${(showCont && window.contactInfo) ? `<div style="font-size: 8.5px; color: #475569;">${escapeHtml(window.contactInfo)}</div>` : ''}
                ${(showWeb  && window.websiteInfo) ? `<div style="font-size: 8.5px; color: #475569;">${escapeHtml(window.websiteInfo)}</div>` : ''}
                ${showCV ? `
                    <div id="sheet_core_values" class="text-uppercase fw-bold text-dark mt-1" style="font-family: Arial, sans-serif; font-size: 8px; letter-spacing: 1.2px;">
                        ${escapeHtml(coreVals)}
                    </div>` : '<div id="sheet_core_values"></div>'}
            </div>
        `;

        let footerSlotsHtml = `
            <div class="d-flex align-items-center justify-content-between gap-2" style="min-height: 56px;">
                ${leftSlotsHtml}
                ${centerContentHtml}
                ${rightSlotsHtml}
            </div>
        `;

        // Distribution list — admin-editable & togglable
        let distributionHtml = '';
        if (showDist && distText.trim()) {
            distributionHtml = `
                <div style="margin-top: 10px; font-size: 9.5px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 5px;">
                    <strong>Distribution / Copy Furnished:</strong> ${escapeHtml(distText)}
                </div>
            `;
        }

        wrapper.innerHTML = toolbarHtml + barcodeRowHtml + ruleHtml + footerSlotsHtml + distributionHtml;
    }

    // ==========================================
    // SAVE TEMPLATE SETTINGS (TYPOGRAPHY, LOGO SLOTS & SALUTATIONS)
    // ==========================================

    function saveTemplateSettings() {
        const fontEl = document.getElementById('ctl_font');
        const lineHeightEl = document.getElementById('ctl_lineheight');
        const paraSpacingEl = document.getElementById('ctl_paraspacing');
        const sigGapEl = document.getElementById('ctl_siggap');
        const greetingEl = document.getElementById('ctl_greeting');
        const closingEl = document.getElementById('ctl_closing');
        const coreValsEl = document.getElementById('ctl_corevals');
        const sigColsEl = document.getElementById('ctl_sigcols');
        const sigLayoutEl = document.getElementById('ctl_siglayout');

        const payload = {
            document_type:          currentDocType,
            logo_layout:            document.getElementById('ctl_logo_layout')?.value || 'centered',
            logo_size:              parseFloat(document.getElementById('ctl_logo_size')?.value || '24'),
            logo_top_margin:        parseFloat(document.getElementById('ctl_logo_top_margin')?.value || '10'),
            show_secondary_logo:    document.getElementById('ctl_show_secondary_logo')?.checked ? 1 : 0,
            divider_style:          document.getElementById('ctl_divider_style')?.value || 'single_black',
            footer_logo_position:   document.getElementById('ctl_footer_logo_position')?.value || 'left',
            show_footer_logo:       document.getElementById('ctl_show_footer_logo')?.checked ? 1 : 0,
            footer_left_slot_1:     window.footerLeftSlot1 || null,
            footer_left_slot_2:     window.footerLeftSlot2 || null,
            footer_right_slot_1:    window.footerRightSlot1 || null,
            footer_right_slot_2:    window.footerRightSlot2 || null,
            header_left_slot_1:     window.headerLeftSlot1 || null,
            header_left_slot_2:     window.headerLeftSlot2 || null,
            header_right_slot_1:    window.headerRightSlot1 || null,
            // Footer content toggles
            barcode_position:       document.getElementById('ctl_barcode_position')?.value || 'above_right',
            qr_position:            document.getElementById('ctl_qr_position')?.value      || 'above_right',
            show_footer_address:    document.getElementById('ctl_show_footer_address')?.checked ? 1 : 0,
            show_footer_contact:    document.getElementById('ctl_show_footer_contact')?.checked ? 1 : 0,
            show_footer_website:    document.getElementById('ctl_show_footer_website')?.checked ? 1 : 0,
            show_core_values:       document.getElementById('ctl_show_core_values')?.checked ? 1 : 0,
            show_distribution:      document.getElementById('ctl_show_distribution')?.checked ? 1 : 0,
            distribution_text:      document.getElementById('ctl_distribution_text')?.value || null,
            greeting:               greetingEl ? greetingEl.value : null,
            closing:                closingEl ? closingEl.value : null,
            core_values:            coreValsEl ? coreValsEl.value : null,
            font_family:            fontEl ? fontEl.value : 'Arial',
            line_height:            lineHeightEl ? parseFloat(lineHeightEl.value) : 1.5,
            paragraph_spacing:      paraSpacingEl ? parseFloat(paraSpacingEl.value) : 3.0,
            signature_gap:          sigGapEl ? parseFloat(sigGapEl.value) : 9.0,
            signatory_columns:      sigColsEl ? parseInt(sigColsEl.value, 10) : 2,
            signatory_layout:       sigLayoutEl ? sigLayoutEl.value : 'spread'
        };

        fetch("{{ route('dss.settings.institution.document_settings') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': pageToken
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Template Settings Saved!',
                    text: `${currentDocType} paper layout, multi-slot logo placements, and typography saved successfully.`,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 2000
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Save Failed', text: d.message || 'Could not save settings.' });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
        });
    }

    // Initialize live sheet
    document.addEventListener('DOMContentLoaded', function () {
        renderLogoLibraryTray();
        syncSheetHeader();
        syncSheetTypography();
        syncSheetFooter();
        renderSignatoriesLive();
    });
</script>
@endsection
