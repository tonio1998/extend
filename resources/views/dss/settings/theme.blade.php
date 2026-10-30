@extends('layouts.dss.app')

@section('title', 'Live Theme & Campus Customizer — SNSU')

@section('styles')
<style>
    .checkered-bg {
        background-color: #ffffff;
        background-image: 
            linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
            linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
            linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
    }

    .logo-dropzone {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 12px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .logo-dropzone:hover, .logo-dropzone.dragover {
        border-color: var(--dss-primary) !important;
        background-color: #f0fdf4 !important;
    }

    .variant-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
    }

    .variant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }

    /* Preset Selection Card */
    .preset-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .preset-card:hover {
        border-color: var(--dss-primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.07);
    }

    .preset-card.active-preset {
        border-color: var(--dss-primary) !important;
        background: color-mix(in srgb, var(--dss-primary) 3%, #ffffff);
        box-shadow: 0 0 0 2px color-mix(in srgb, var(--dss-primary) 30%, transparent), 0 8px 20px rgba(0,0,0,0.06);
    }

    .palette-swatch-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-block;
        border: 1.5px solid #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.18);
        flex-shrink: 0;
    }

    /* Customizer Tabs Navigation */
    .studio-nav-tabs .nav-link {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        padding: 0.65rem 0.95rem;
        border-radius: 8px;
        transition: all 0.15s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .studio-nav-tabs .nav-link:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }

    .studio-nav-tabs .nav-link.active {
        color: var(--dss-primary) !important;
        background-color: var(--dss-primary-soft) !important;
    }

    /* Mock Window Frame for Dynamic Preview */
    .preview-window-frame {
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.12);
        transition: all 0.2s ease;
    }

    .preview-window-header {
        background: #f1f5f9;
        padding: 8px 14px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .window-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .preview-sidebar {
        width: 185px;
        min-width: 185px;
        padding: 12px 8px;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .preview-navbar {
        height: 44px;
        padding: 0 14px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .preview-nav-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .color-input-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Top Header & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="extra-small text-muted text-uppercase fw-bold">SaaS Governance &bull; White-Labeling Engine</div>
            <h3 class="fw-bold text-dark mb-1">Live Theme & Brand Customizer</h3>
            <p class="text-muted small mb-0">Fine-grained white-label styling studio. Configure institutional color presets, granular sidebar &amp; topbar tones, button aesthetics, and assets.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-flex align-items-center gap-1.5" onclick="applyPreset('snsu_emerald')">
                <i class="mdi mdi-restore"></i>
                <span>Reset to SNSU Default</span>
            </button>
            <button type="button" id="btnSaveTheme" class="btn btn-dss-primary btn-sm px-3.5 shadow-xs d-flex align-items-center gap-1.5" onclick="saveThemeSettings()">
                <i class="mdi mdi-content-save"></i>
                <span id="btnSaveText">Save &amp; Apply Theme</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 0: PRE-DEFINED UI LAYOUT TEMPLATES (SAVED IN DATABASE)            -->
    <!-- ========================================================================= -->
    @php
        $uiTemplatesList = $uiTemplates ?? \App\Services\DSS\ThemePresets::uiTemplates();
        $activeUiTpl = $theme->ui_template ?? 'flup_minimal';
    @endphp
    <div class="dss-card p-4 mb-4 shadow-xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2.5">
            <div class="d-flex align-items-center gap-2">
                <div class="p-1.5 rounded-2 bg-dss-primary-soft text-dss-primary">
                    <i class="mdi mdi-layers-triple-outline fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Pre-Defined UI Layout Templates</h5>
                    <div class="extra-small text-muted">Choose your preferred global UI architecture (Sidebar, Topbar, Cards &amp; Dashboard layout). Saved directly in the database.</div>
                </div>
            </div>
            <span class="badge bg-primary text-white extra-small">
                Active UI: {{ $uiTemplatesList[$activeUiTpl]['name'] ?? 'Flup — Clean SaaS Minimalist' }}
            </span>
        </div>

        <div class="row g-3">
            @foreach($uiTemplatesList as $tplKey => $tpl)
                @php
                    $isCurrentTpl = $activeUiTpl === $tplKey;
                @endphp
                <div class="col-xl-4 col-md-6">
                    <div class="preset-card h-100 {{ $isCurrentTpl ? 'active-preset' : '' }}"
                         onclick="window.switchGlobalUiTemplate('{{ $tplKey }}')"
                         style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge extra-small fw-bold" style="background: color-mix(in srgb, {{ $tpl['accent_preview'] }} 14%, #ffffff); color: {{ $tpl['accent_preview'] }};">
                                <i class="mdi {{ $tpl['icon'] }} me-1"></i>{{ $tpl['tag'] }}
                            </span>
                            @if($isCurrentTpl)
                                <span class="badge bg-success-subtle text-success extra-small fw-bold">
                                    <i class="mdi mdi-check-circle me-0.5"></i> Active in DB
                                </span>
                            @endif
                        </div>

                        <div class="fw-bold text-dark mb-1" style="font-size: 14.5px;">{{ $tpl['name'] }}</div>
                        <p class="text-muted extra-small mb-3" style="font-size: 11.5px; line-height: 1.45;">
                            {{ $tpl['desc'] }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="extra-small text-muted fw-semibold">Solid Opaque &bull; Dynamic Theme Color</span>
                            <span class="extra-small fw-bold text-primary">
                                {{ $isCurrentTpl ? 'Currently Active' : 'Switch & Save UI' }} <i class="mdi mdi-arrow-right ms-0.5"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: READY-MADE CURATED INSTITUTIONAL DESIGN PRESETS -->
    <!-- ========================================================================= -->
    <div class="dss-card p-4 mb-4 shadow-xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2.5">
            <div class="d-flex align-items-center gap-2">
                <div class="p-1.5 rounded-2 bg-dss-primary-soft text-dss-primary">
                    <i class="mdi mdi-palette-swatch-outline fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Curated Theme Presets</h5>
                    <div class="extra-small text-muted">Select an established colorway or customize granular tokens below to match your institutional branding.</div>
                </div>
            </div>
            <span class="badge bg-soft-primary text-primary font-mono extra-small">
                {{ count($presets) }} Presets Available
            </span>
        </div>

        <div class="row g-3" id="presetsGrid">
            @foreach($presets as $key => $p)
                @php
                    $isCurrentPreset = ($theme->preset_key ?? 'adssu_emerald') === $key;
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="preset-card h-100 {{ $isCurrentPreset ? 'active-preset' : '' }}" 
                         id="preset_card_{{ $key }}"
                         onclick="applyPreset('{{ $key }}')">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge extra-small font-mono fw-bold" style="background: color-mix(in srgb, {{ $p['primary_color'] }} 12%, #ffffff); color: {{ $p['primary_color'] }}; border: 1px solid color-mix(in srgb, {{ $p['primary_color'] }} 25%, transparent);">
                                {{ $p['tag'] }}
                            </span>
                            <span class="badge bg-success-subtle text-success extra-small fw-bold {{ $isCurrentPreset ? '' : 'd-none' }}" id="active_badge_{{ $key }}">
                                <i class="mdi mdi-check-circle me-0.5"></i> Active
                            </span>
                        </div>

                        <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $p['name'] }}</div>
                        <p class="text-muted extra-small mb-3" style="font-size: 11px; min-height: 32px; line-height: 1.35;">
                            {{ $p['desc'] }}
                        </p>

                        <!-- Visual Color Swatches -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="d-flex align-items-center" style="margin-left: 4px;">
                                <span class="palette-swatch-dot" style="background-color: {{ $p['primary_color'] }}; z-index: 5;" title="Primary: {{ $p['primary_color'] }}"></span>
                                <span class="palette-swatch-dot" style="background-color: {{ $p['secondary_color'] }}; margin-left: -6px; z-index: 4;" title="Secondary: {{ $p['secondary_color'] }}"></span>
                                <span class="palette-swatch-dot" style="background-color: {{ $p['accent_color'] }}; margin-left: -6px; z-index: 3;" title="Accent: {{ $p['accent_color'] }}"></span>
                                <span class="palette-swatch-dot" style="background-color: {{ $p['sidebar_bg'] }}; margin-left: -6px; z-index: 2;" title="Sidebar: {{ $p['sidebar_bg'] }}"></span>
                                <span class="palette-swatch-dot" style="background-color: {{ $p['topbar_bg'] }}; margin-left: -6px; z-index: 1;" title="Topbar: {{ $p['topbar_bg'] }}"></span>
                            </div>
                            <span class="extra-small fw-bold text-dss-primary" style="font-size: 11px;">
                                Use Preset <i class="mdi mdi-arrow-right ms-0.5"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 2: DETAILED COMPONENT STYLING & REAL-TIME INTERACTIVE PREVIEW -->
    <!-- ========================================================================= -->
    <div class="row g-4 mb-4">
        <!-- Detailed Styling Studio Controls (Left 6-Cols) -->
        <div class="col-xl-6 col-lg-6">
            <div class="dss-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Granular Component Studio</h6>
                        <div class="extra-small text-muted">Fine-tune individual colors, sidebar, navbar, and button attributes.</div>
                    </div>
                    <span class="badge bg-light text-muted border font-mono extra-small">Live Sync Active</span>
                </div>

                <!-- Studio Tab Nav -->
                <ul class="nav studio-nav-tabs bg-light p-1 rounded-3 mb-3" id="studioTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-brand-link" data-bs-toggle="tab" data-bs-target="#tab-brand" type="button" role="tab">
                            <i class="mdi mdi-format-color-fill"></i> Brand
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-sidebar-link" data-bs-toggle="tab" data-bs-target="#tab-sidebar" type="button" role="tab">
                            <i class="mdi mdi-page-layout-sidebar-left"></i> Sidebar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-topbar-link" data-bs-toggle="tab" data-bs-target="#tab-topbar" type="button" role="tab">
                            <i class="mdi mdi-page-layout-header"></i> Topbar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-buttons-link" data-bs-toggle="tab" data-bs-target="#tab-buttons" type="button" role="tab">
                            <i class="mdi mdi-gesture-tap-button"></i> Buttons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-cards-link" data-bs-toggle="tab" data-bs-target="#tab-cards" type="button" role="tab">
                            <i class="mdi mdi-card-outline"></i> Cards
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-css-link" data-bs-toggle="tab" data-bs-target="#tab-css" type="button" role="tab">
                            <i class="mdi mdi-code-tags"></i> CSS
                        </button>
                    </li>
                </ul>

                <form id="themeCustomizerForm">
                    <input type="hidden" id="preset_key" name="preset_key" value="{{ $theme->preset_key ?? 'snsu_emerald' }}">
                    <input type="hidden" id="ui_template" name="ui_template" value="{{ $theme->ui_template ?? 'flup_minimal' }}">
                    <input type="hidden" id="sidebar_theme" name="sidebar_theme" value="{{ $theme->sidebar_theme ?? 'light' }}">
                    <input type="hidden" id="navbar_theme" name="navbar_theme" value="{{ $theme->navbar_theme ?? 'light' }}">

                    <div class="tab-content" id="studioTabContent">
                        <!-- TAB 1: BRAND PALETTE -->
                        <div class="tab-pane fade show active" id="tab-brand" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    Primary Brand Color (Key Metrics, Nav Highlights, Buttons)
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border" id="primary_color" name="primary_color" value="{{ $theme->primary_color ?? '#047857' }}" oninput="syncColorInput('primary_color'); updateLivePreview();">
                                    <input type="text" class="form-control font-mono color-input-badge" id="primary_color_text" value="{{ $theme->primary_color ?? '#047857' }}" oninput="syncTextInput('primary_color'); updateLivePreview();">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    Secondary Brand Color (Analytics Charts, Badges, Highlights)
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border" id="secondary_color" name="secondary_color" value="{{ $theme->secondary_color ?? '#0284c7' }}" oninput="syncColorInput('secondary_color'); updateLivePreview();">
                                    <input type="text" class="form-control font-mono color-input-badge" id="secondary_color_text" value="{{ $theme->secondary_color ?? '#0284c7' }}" oninput="syncTextInput('secondary_color'); updateLivePreview();">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    Accent Gold / Alert Color (Milestones, Badges, Attention)
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border" id="accent_color" name="accent_color" value="{{ $theme->accent_color ?? '#f59e0b' }}" oninput="syncColorInput('accent_color'); updateLivePreview();">
                                    <input type="text" class="form-control font-mono color-input-badge" id="accent_color_text" value="{{ $theme->accent_color ?? '#f59e0b' }}" oninput="syncTextInput('accent_color'); updateLivePreview();">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                                    Global Layout Border Radius Tokens
                                </label>
                                <select class="form-select" id="border_radius" name="border_radius" onchange="updateLivePreview()">
                                    <option value="6px" {{ ($theme->border_radius ?? '') === '6px' ? 'selected' : '' }}>Compact Sharp (6px)</option>
                                    <option value="8px" {{ ($theme->border_radius ?? '') === '8px' ? 'selected' : '' }}>Clean Standard (8px)</option>
                                    <option value="10px" {{ ($theme->border_radius ?? '10px') === '10px' ? 'selected' : '' }}>Modern Squircle (10px - Recommended)</option>
                                    <option value="12px" {{ ($theme->border_radius ?? '') === '12px' ? 'selected' : '' }}>Rounded Soft (12px)</option>
                                    <option value="16px" {{ ($theme->border_radius ?? '') === '16px' ? 'selected' : '' }}>Curved Fluid (16px)</option>
                                </select>
                            </div>
                        </div>

                        <!-- TAB 2: SIDEBAR STYLING -->
                        <div class="tab-pane fade" id="tab-sidebar" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Sidebar Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="sidebar_bg" name="sidebar_bg" value="{{ $theme->sidebar_bg ?? '#ffffff' }}" oninput="syncColorInput('sidebar_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="sidebar_bg_text" value="{{ $theme->sidebar_bg ?? '#ffffff' }}" oninput="syncTextInput('sidebar_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Sidebar Text Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="sidebar_text" name="sidebar_text" value="{{ $theme->sidebar_text ?? '#1e293b' }}" oninput="syncColorInput('sidebar_text'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="sidebar_text_text" value="{{ $theme->sidebar_text ?? '#1e293b' }}" oninput="syncTextInput('sidebar_text'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Active Item Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="sidebar_active_bg" name="sidebar_active_bg" value="{{ $theme->sidebar_active_bg ?? '#ecfdf5' }}" oninput="syncColorInput('sidebar_active_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="sidebar_active_bg_text" value="{{ $theme->sidebar_active_bg ?? '#ecfdf5' }}" oninput="syncTextInput('sidebar_active_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Active Text &amp; Icon Accent</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="sidebar_active_text" name="sidebar_active_text" value="{{ $theme->sidebar_active_text ?? '#047857' }}" oninput="syncColorInput('sidebar_active_text'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="sidebar_active_text_text" value="{{ $theme->sidebar_active_text ?? '#047857' }}" oninput="syncTextInput('sidebar_active_text'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Sidebar Right Border Divider</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border" id="sidebar_border" name="sidebar_border" value="{{ $theme->sidebar_border ?? '#e2e8f0' }}" oninput="syncColorInput('sidebar_border'); updateLivePreview();">
                                    <input type="text" class="form-control font-mono color-input-badge" id="sidebar_border_text" value="{{ $theme->sidebar_border ?? '#e2e8f0' }}" oninput="syncTextInput('sidebar_border'); updateLivePreview();">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: TOPBAR STYLING -->
                        <div class="tab-pane fade" id="tab-topbar" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Topbar Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="topbar_bg" name="topbar_bg" value="{{ $theme->topbar_bg ?? '#ffffff' }}" oninput="syncColorInput('topbar_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="topbar_bg_text" value="{{ $theme->topbar_bg ?? '#ffffff' }}" oninput="syncTextInput('topbar_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Topbar Text &amp; Icons</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="topbar_text" name="topbar_text" value="{{ $theme->topbar_text ?? '#0f172a' }}" oninput="syncColorInput('topbar_text'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="topbar_text_text" value="{{ $theme->topbar_text ?? '#0f172a' }}" oninput="syncTextInput('topbar_text'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Topbar Bottom Border</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color border" id="topbar_border" name="topbar_border" value="{{ $theme->topbar_border ?? '#e2e8f0' }}" oninput="syncColorInput('topbar_border'); updateLivePreview();">
                                    <input type="text" class="form-control font-mono color-input-badge" id="topbar_border_text" value="{{ $theme->topbar_border ?? '#e2e8f0' }}" oninput="syncTextInput('topbar_border'); updateLivePreview();">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: BUTTONS & INTERACTIONS -->
                        <div class="tab-pane fade" id="tab-buttons" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Primary Button Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="btn_primary_bg" name="btn_primary_bg" value="{{ $theme->btn_primary_bg ?? ($theme->primary_color ?? '#047857') }}" oninput="syncColorInput('btn_primary_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="btn_primary_bg_text" value="{{ $theme->btn_primary_bg ?? ($theme->primary_color ?? '#047857') }}" oninput="syncTextInput('btn_primary_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Primary Button Text</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="btn_primary_text" name="btn_primary_text" value="{{ $theme->btn_primary_text ?? '#ffffff' }}" oninput="syncColorInput('btn_primary_text'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="btn_primary_text_text" value="{{ $theme->btn_primary_text ?? '#ffffff' }}" oninput="syncTextInput('btn_primary_text'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Secondary Button Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="btn_secondary_bg" name="btn_secondary_bg" value="{{ $theme->btn_secondary_bg ?? ($theme->secondary_color ?? '#0284c7') }}" oninput="syncColorInput('btn_secondary_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="btn_secondary_bg_text" value="{{ $theme->btn_secondary_bg ?? ($theme->secondary_color ?? '#0284c7') }}" oninput="syncTextInput('btn_secondary_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Secondary Button Text</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="btn_secondary_text" name="btn_secondary_text" value="{{ $theme->btn_secondary_text ?? '#ffffff' }}" oninput="syncColorInput('btn_secondary_text'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="btn_secondary_text_text" value="{{ $theme->btn_secondary_text ?? '#ffffff' }}" oninput="syncTextInput('btn_secondary_text'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Button Corner Shape</label>
                                    <select class="form-select" id="btn_radius" name="btn_radius" onchange="updateLivePreview()">
                                        <option value="6px" {{ ($theme->btn_radius ?? '') === '6px' ? 'selected' : '' }}>Compact (6px)</option>
                                        <option value="8px" {{ ($theme->btn_radius ?? '8px') === '8px' ? 'selected' : '' }}>Standard Modern (8px)</option>
                                        <option value="12px" {{ ($theme->btn_radius ?? '') === '12px' ? 'selected' : '' }}>Smooth Rounded (12px)</option>
                                        <option value="50px" {{ ($theme->btn_radius ?? '') === '50px' ? 'selected' : '' }}>Executive Full Pill (50px)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Button Elevation &amp; Shadow</label>
                                    <select class="form-select" id="btn_elevation" name="btn_elevation" onchange="updateLivePreview()">
                                        <option value="none" {{ ($theme->btn_elevation ?? '') === 'none' ? 'selected' : '' }}>Flat Minimalist</option>
                                        <option value="subtle" {{ ($theme->btn_elevation ?? 'subtle') === 'subtle' ? 'selected' : '' }}>Subtle Shadow (Default)</option>
                                        <option value="glow" {{ ($theme->btn_elevation ?? '') === 'glow' ? 'selected' : '' }}>Brand Accent Glow</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: CARDS & CONTAINERS -->
                        <div class="tab-pane fade" id="tab-cards" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Card Background</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="card_bg" name="card_bg" value="{{ $theme->card_bg ?? '#ffffff' }}" oninput="syncColorInput('card_bg'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="card_bg_text" value="{{ $theme->card_bg ?? '#ffffff' }}" oninput="syncTextInput('card_bg'); updateLivePreview();">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Card Border Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color border" id="card_border" name="card_border" value="{{ $theme->card_border ?? '#e2e8f0' }}" oninput="syncColorInput('card_border'); updateLivePreview();">
                                        <input type="text" class="form-control font-mono color-input-badge" id="card_border_text" value="{{ $theme->card_border ?? '#e2e8f0' }}" oninput="syncTextInput('card_border'); updateLivePreview();">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Card Corner Radius</label>
                                <select class="form-select" id="card_radius" name="card_radius" onchange="updateLivePreview()">
                                    <option value="8px" {{ ($theme->card_radius ?? '') === '8px' ? 'selected' : '' }}>Compact (8px)</option>
                                    <option value="10px" {{ ($theme->card_radius ?? '') === '10px' ? 'selected' : '' }}>Standard (10px)</option>
                                    <option value="12px" {{ ($theme->card_radius ?? '12px') === '12px' ? 'selected' : '' }}>Modern Soft (12px - Recommended)</option>
                                    <option value="16px" {{ ($theme->card_radius ?? '') === '16px' ? 'selected' : '' }}>Fluid Curved (16px)</option>
                                </select>
                            </div>
                        </div>

                        <!-- TAB 6: CUSTOM CSS INJECTION -->
                        <div class="tab-pane fade" id="tab-css" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Direct Institutional CSS Injection</label>
                                <textarea class="form-control font-mono" id="custom_css" name="custom_css" rows="6" placeholder="/* Custom CSS overrides e.g. .dss-navbar { backdrop-filter: blur(10px); } */">{{ $theme->custom_css }}</textarea>
                                <div class="extra-small text-muted mt-1">
                                    <i class="mdi mdi-information-outline me-1"></i>CSS injected here compiles directly into the institutional suite runtime header.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Real-Time Interactive Live Application Preview (Right 6-Cols) -->
        <div class="col-xl-6 col-lg-6">
            <div class="dss-card p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Live Application Mockup</h6>
                        <div class="extra-small text-muted">Updates in real time as presets or tokens are modified.</div>
                    </div>
                    <span class="badge badge-dss-primary" id="previewBadge">Instant Feedback</span>
                </div>

                <!-- Realistic Mockup Window -->
                <div class="preview-window-frame flex-grow-1 d-flex flex-column mb-3" id="livePreviewContainer">
                    <!-- Browser Window Header Bar -->
                    <div class="preview-window-header">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="window-dot bg-danger opacity-75"></span>
                            <span class="window-dot bg-warning opacity-75"></span>
                            <span class="window-dot bg-success opacity-75"></span>
                        </div>
                        <span class="extra-small font-mono text-muted text-truncate" style="font-size: 10px; max-width: 250px;">
                            http://extend.dev.com &bull; {{ $institution->school_acronym ?: 'SNSU' }}
                        </span>
                        <span class="badge bg-light text-muted border font-mono" style="font-size: 9px;">PREVIEW</span>
                    </div>

                    <!-- Main App Shell -->
                    <div class="d-flex flex-grow-1" style="min-height: 380px;">
                        <!-- Mini Interactive Sidebar -->
                        <div class="preview-sidebar" id="mockSidebar">
                            <!-- Sidebar Brand -->
                            <div class="d-flex align-items-center gap-2 pb-2 mb-2 border-bottom" id="mockSidebarBrandBorder" style="border-color: rgba(226, 232, 240, 0.5);">
                                <div class="p-1 rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" id="mockSidebarLogoBox" style="width: 26px; height: 26px; background: var(--dss-primary); color: #fff;">
                                    @if(!empty($institution->logo_path))
                                        <img src="{{ asset($institution->logo_path) }}" alt="Logo" style="max-height: 20px; max-width: 20px; object-fit: contain;">
                                    @else
                                        <i class="mdi mdi-school" style="font-size: 14px;"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-bold extra-small text-truncate lh-1" id="mockSidebarTitle" style="font-size: 11px;">{{ $institution->school_acronym ?: 'SNSU' }}</div>
                                    <div class="text-muted font-mono" style="font-size: 8.5px;">Extension System (RIE)</div>
                                </div>
                            </div>

                            <div class="extra-small fw-bold text-uppercase px-1 mb-1" id="mockSidebarSectionTitle" style="font-size: 9px; letter-spacing: 0.05em; opacity: 0.65;">
                                Decision Intelligence
                            </div>

                            <!-- Active Nav Link -->
                            <a href="javascript:void(0)" class="preview-nav-item" id="mockNavActive">
                                <i class="mdi mdi-view-dashboard-outline fs-6" id="mockNavActiveIcon"></i>
                                <span class="fw-bold">Dashboard</span>
                            </a>

                            <!-- Other Nav Links -->
                            <a href="javascript:void(0)" class="preview-nav-item" id="mockNavInactive1">
                                <i class="mdi mdi-barcode-scan fs-6 opacity-75"></i>
                                <span>Doc Tracking</span>
                                <span class="badge ms-auto font-mono" style="font-size: 8px; background: rgba(0,0,0,0.06);">FLOW</span>
                            </a>

                            <a href="javascript:void(0)" class="preview-nav-item" id="mockNavInactive2">
                                <i class="mdi mdi-certificate-outline fs-6 opacity-75"></i>
                                <span>Accreditation</span>
                            </a>

                            <div class="mt-auto pt-2 border-top extra-small text-truncate px-1" id="mockSidebarFooter" style="font-size: 9px; opacity: 0.7;">
                                <i class="mdi mdi-shield-check text-success me-1"></i>Certified SaaS
                            </div>
                        </div>

                        <!-- Mini Content Column -->
                        <div class="flex-grow-1 d-flex flex-column bg-light" id="mockMainContent">
                            <!-- Mini Topbar -->
                            <div class="preview-navbar" id="mockNavbar">
                                <div class="d-flex align-items-center gap-1.5">
                                    <div class="px-2 py-0.5 rounded-2 d-flex align-items-center gap-1 extra-small fw-semibold border" id="mockNavbarInstitutionBadge">
                                        <i class="mdi mdi-office-building"></i>
                                        <span class="text-truncate" style="max-width: 140px; font-size: 10px;">{{ $institution->school_name }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="badge bg-light text-muted border font-mono" style="font-size: 9px;">FY: 2026</span>
                                    <span class="p-1 rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 11px;">
                                        <i class="mdi mdi-bell-outline"></i>
                                    </span>
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold font-mono" id="mockUserAvatar" style="width: 22px; height: 22px; font-size: 9.5px; background: var(--dss-primary);">
                                        AD
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Workspace Body -->
                            <div class="p-3 d-flex flex-column gap-3 overflow-auto">
                                <!-- Sample Action Card -->
                                <div class="p-3 rounded-3 border" id="mockCard">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <div class="fw-bold extra-small text-dark" id="mockCardTitle" style="font-size: 12px;">
                                            Sample Decision Metric &bull; AHP / TOPSIS
                                        </div>
                                        <span class="badge font-mono extra-small" id="mockAccentPill" style="font-size: 9px;">
                                            SLA 99.4%
                                        </span>
                                    </div>
                                    <p class="text-muted extra-small mb-2.5" style="font-size: 10.5px; line-height: 1.35;">
                                        Automated ranking completed for campus equipment suppliers with multi-attribute decision metrics.
                                    </p>

                                    <!-- Action Buttons Preview -->
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm extra-small px-2.5 py-1 fw-bold" id="mockBtnPrimary" style="font-size: 11px;">
                                            Primary Action
                                        </button>
                                        <button type="button" class="btn btn-sm extra-small px-2.5 py-1 fw-bold" id="mockBtnSecondary" style="font-size: 11px;">
                                            Secondary
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light border extra-small px-2 py-1 text-muted" style="font-size: 10.5px;">
                                            Dismiss
                                        </button>
                                    </div>
                                </div>

                                <!-- Soft Tokens Row -->
                                <div class="d-flex gap-2 flex-wrap align-items-center">
                                    <span class="badge rounded-2 extra-small px-2 py-1" id="mockTokenPrimary">
                                        Primary Soft Pill
                                    </span>
                                    <span class="badge rounded-2 extra-small px-2 py-1" id="mockTokenSecondary">
                                        Secondary Soft Pill
                                    </span>
                                    <span class="badge bg-soft-success text-success border border-success-subtle extra-small px-2 py-1">
                                        <i class="mdi mdi-check-circle-outline me-0.5"></i> Operational
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="extra-small text-muted text-center mt-auto">
                    <i class="mdi mdi-information-outline me-1"></i>Standardized Inter system typography remains consistent across all modules.
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 3: INSTITUTIONAL LOGO & 7 MULTI-RESOLUTION WHITE-LABEL SUITE -->
    <!-- ========================================================================= -->
    <div class="dss-card p-4 mb-4 shadow-xs">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3 border-bottom pb-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 rounded-2 bg-dss-primary-soft text-dss-primary">
                        <i class="mdi mdi-image-multiple-outline fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Institutional Brand Mark &amp; 7-Size Asset Generator</h5>
                </div>
                <p class="text-muted extra-small mt-1 mb-0">
                    Upload your high-resolution university seal or institutional logo. The system automatically renders, optimizes, and generates <strong>7 standardized multi-resolution variants</strong> for web, mobile PWA, SSR PDF printing, and security watermarking.
                </p>
            </div>

            @if(!empty($institution->logo_path))
            <button class="btn btn-outline-danger btn-sm extra-small fw-bold d-flex align-items-center gap-1 shadow-2xs" onclick="confirmRemoveLogo()">
                <i class="mdi mdi-trash-can-outline"></i>
                <span>Reset to Default Logo</span>
            </button>
            @endif
        </div>

        <div class="row g-4 align-items-start">
            <!-- Left: Interactive Upload Box -->
            <div class="col-xl-4 col-lg-5">
                <form id="logoUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="logo-dropzone p-4 text-center" id="logoDropzone" onclick="document.getElementById('logoFileInput').click()">
                        <input type="file" id="logoFileInput" name="logo" class="d-none" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" onchange="handleLogoSelect(this)">
                        
                        <div id="dropzoneEmptyState">
                            <div class="mb-2">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-xs p-3 border">
                                    <i class="mdi mdi-cloud-upload-outline fs-3 text-dss-primary"></i>
                                </div>
                            </div>
                            <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Click to Browse or Drag Logo Here</div>
                            <div class="text-muted extra-small mb-2">Supports high-res PNG, JPG, WEBP, or SVG (Up to 10MB)</div>
                            <span class="badge bg-light text-secondary border font-mono extra-small">Alpha Transparency Recommended</span>
                        </div>

                        <!-- Staged Preview State -->
                        <div id="dropzoneStagedState" class="d-none">
                            <div class="checkered-bg p-2 rounded-2 border d-inline-block mb-2 shadow-2xs">
                                <img id="stagedImagePreview" src="" alt="Preview" style="max-height: 100px; max-width: 160px; object-fit: contain;">
                            </div>
                            <div class="fw-bold text-dark extra-small text-truncate" id="stagedFileName">logo.png</div>
                            <div class="text-muted extra-small font-mono" id="stagedFileSize">0 KB</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="btnUploadLogo" class="btn btn-dss-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-xs" onclick="submitLogoUpload()" disabled>
                            <i class="mdi mdi-lightning-bolt"></i>
                            <span>Generate 7 Multi-Resolution Versions</span>
                        </button>
                    </div>
                </form>

                <!-- Current Active Master Summary -->
                <div class="p-3 bg-light rounded-3 border mt-3">
                    <div class="extra-small text-muted text-uppercase fw-bold mb-2">Active Brand Status</div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="checkered-bg p-1.5 rounded-2 border flex-shrink-0" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                            @if(!empty($institution->logo_path))
                                <img src="{{ asset($institution->logo_path) }}" alt="Logo" style="max-height: 36px; max-width: 36px; object-fit: contain;">
                            @else
                                <i class="mdi mdi-school fs-4 text-dss-primary"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold text-dark extra-small text-truncate">{{ $institution->school_name }}</div>
                            <div class="text-muted extra-small font-mono" style="font-size: 11px;">
                                @if(!empty($institution->logo_path))
                                    <span class="text-success fw-bold"><i class="mdi mdi-check-circle me-0.5"></i> Custom Logo Active</span>
                                @else
                                    <span class="text-secondary"><i class="mdi mdi-information-outline me-0.5"></i> Default State Seal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: 7-Version Output Showcase Gallery -->
            <div class="col-xl-8 col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <span class="extra-small fw-bold text-uppercase text-muted">Generated Multi-Resolution Suite (7 Versions)</span>
                    <span class="badge bg-soft-primary text-primary border border-primary-subtle font-mono">
                        {{ !empty($logoVariants) ? count($logoVariants) . ' Assets Ready' : 'Awaiting Upload' }}
                    </span>
                </div>

                @php
                    $variantKeys = [
                        'favicon' => [
                            'title' => '1. Browser Favicon',
                            'badge' => '32 × 32 px',
                            'desc' => 'Browser tab icon & bookmark shortcut',
                            'icon' => 'mdi-tab',
                            'preview_height' => 32,
                            'preview_width' => 32,
                        ],
                        'app_icon' => [
                            'title' => '2. Mobile / PWA App Icon',
                            'badge' => '192 × 192 px',
                            'desc' => 'Apple Touch & Android launcher icon',
                            'icon' => 'mdi-cellphone',
                            'preview_height' => 48,
                            'preview_width' => 48,
                        ],
                        'sidebar' => [
                            'title' => '3. Compact Sidebar Mark',
                            'badge' => '120 × 120 px',
                            'desc' => 'Collapsed sidebar badge & mobile UI',
                            'icon' => 'mdi-page-layout-sidebar-left',
                            'preview_height' => 44,
                            'preview_width' => 44,
                        ],
                        'navbar' => [
                            'title' => '4. Horizontal Navbar Header',
                            'badge' => '280 × 80 px',
                            'desc' => 'Primary top navigation bar brand mark',
                            'icon' => 'mdi-page-layout-header',
                            'preview_height' => 32,
                            'preview_width' => 'auto',
                        ],
                        'master' => [
                            'title' => '5. Master High-Res Logo',
                            'badge' => '512 × 512 px',
                            'desc' => 'Login portal & institutional credentials',
                            'icon' => 'mdi-shield-crown-outline',
                            'preview_height' => 54,
                            'preview_width' => 54,
                        ],
                        'print_seal' => [
                            'title' => '6. Official Print Seal (300 DPI)',
                            'badge' => '1024 × 1024 px',
                            'desc' => 'Ultra-crisp A4 & Long SSR binder covers',
                            'icon' => 'mdi-printer-outline',
                            'preview_height' => 54,
                            'preview_width' => 54,
                        ],
                        'watermark' => [
                            'title' => '7. Document Watermark (Translucent)',
                            'badge' => '600 × 600 px &bull; 22% Alpha',
                            'desc' => 'Background security stamp for official PDFs',
                            'icon' => 'mdi-watermark',
                            'preview_height' => 50,
                            'preview_width' => 50,
                        ],
                    ];
                @endphp

                <div class="row g-2.5" id="logoVariantsContainer">
                    @foreach($variantKeys as $key => $meta)
                        @php
                            $vData = $logoVariants[$key] ?? null;
                            $hasAsset = !empty($vData['url']);
                            $assetUrl = $hasAsset ? $vData['url'] : null;
                        @endphp
                        <div class="col-md-6 col-sm-12">
                            <div class="variant-card p-3 d-flex align-items-center justify-content-between gap-3 h-100">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <!-- Preview Canvas -->
                                    <div class="checkered-bg rounded-2 border flex-shrink-0 d-flex align-items-center justify-content-center p-1" style="width: 58px; height: 58px;">
                                        @if($hasAsset)
                                            <img src="{{ $assetUrl }}" alt="{{ $meta['title'] }}" style="max-height: {{ is_numeric($meta['preview_height']) ? $meta['preview_height'].'px' : '48px' }}; max-width: 50px; object-fit: contain;">
                                        @else
                                            <i class="mdi {{ $meta['icon'] }} fs-4 text-secondary opacity-50"></i>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="fw-bold text-dark extra-small text-truncate" style="font-size: 11.5px;">{{ $meta['title'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 my-0.5">
                                            <span class="badge bg-light text-dark border font-mono px-1.5 py-0.5" style="font-size: 9.5px;">
                                                {!! $meta['badge'] !!}
                                            </span>
                                            @if($hasAsset && !empty($vData['size_formatted']))
                                                <span class="badge bg-soft-secondary text-secondary font-mono" style="font-size: 9px;">{{ $vData['size_formatted'] }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted extra-small text-truncate" style="font-size: 10.5px;" title="{{ $meta['desc'] }}">
                                            {{ $meta['desc'] }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex flex-column gap-1 flex-shrink-0">
                                    @if($hasAsset)
                                        <a href="{{ $assetUrl }}" target="_blank" download="{{ $vData['file_name'] ?? 'logo.png' }}" class="btn btn-xs btn-light border p-1 d-flex align-items-center justify-content-center shadow-2xs" style="width: 26px; height: 26px;" title="Download Asset">
                                            <i class="mdi mdi-download text-primary"></i>
                                        </a>
                                        <button type="button" class="btn btn-xs btn-light border p-1 d-flex align-items-center justify-content-center shadow-2xs" style="width: 26px; height: 26px;" onclick="copyAssetUrl('{{ $assetUrl }}')" title="Copy URL">
                                            <i class="mdi mdi-content-copy text-secondary"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-light text-muted border font-mono" style="font-size: 9px;">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Injected presets dictionary from backend
    const PRESETS_DATA = @json($presets);

    // Synchronize color picker and hex text inputs
    function syncColorInput(id) {
        const val = document.getElementById(id).value;
        const textElem = document.getElementById(id + '_text');
        if (textElem) {
            textElem.value = val;
        }
    }

    function syncTextInput(id) {
        const textVal = document.getElementById(id + '_text').value;
        if (/^#[0-9A-Fa-f]{6}$/.test(textVal)) {
            const picker = document.getElementById(id);
            if (picker) {
                picker.value = textVal;
            }
        }
    }

    // Apply a preset directly
    function applyPreset(presetKey) {
        const p = PRESETS_DATA[presetKey];
        if (!p) return;

        // Set form fields
        document.getElementById('preset_key').value = presetKey;
        
        // Colors
        setFieldAndSync('primary_color', p.primary_color);
        setFieldAndSync('secondary_color', p.secondary_color);
        setFieldAndSync('accent_color', p.accent_color);

        // Sidebar
        document.getElementById('sidebar_theme').value = p.sidebar_theme || 'light';
        setFieldAndSync('sidebar_bg', p.sidebar_bg);
        setFieldAndSync('sidebar_text', p.sidebar_text);
        setFieldAndSync('sidebar_active_bg', p.sidebar_active_bg);
        setFieldAndSync('sidebar_active_text', p.sidebar_active_text);
        setFieldAndSync('sidebar_border', p.sidebar_border);

        // Topbar
        document.getElementById('navbar_theme').value = p.navbar_theme || 'light';
        setFieldAndSync('topbar_bg', p.topbar_bg);
        setFieldAndSync('topbar_text', p.topbar_text);
        setFieldAndSync('topbar_border', p.topbar_border);

        // Buttons & Cards
        setFieldAndSync('btn_primary_bg', p.btn_primary_bg);
        setFieldAndSync('btn_primary_text', p.btn_primary_text);
        setFieldAndSync('btn_secondary_bg', p.btn_secondary_bg);
        setFieldAndSync('btn_secondary_text', p.btn_secondary_text);
        
        if (document.getElementById('btn_radius')) document.getElementById('btn_radius').value = p.btn_radius || '8px';
        if (document.getElementById('btn_elevation')) document.getElementById('btn_elevation').value = p.btn_elevation || 'subtle';
        if (document.getElementById('border_radius')) document.getElementById('border_radius').value = p.border_radius || '10px';
        
        setFieldAndSync('card_bg', p.card_bg || '#ffffff');
        setFieldAndSync('card_border', p.card_border || '#e2e8f0');
        if (document.getElementById('card_radius')) document.getElementById('card_radius').value = p.card_radius || '12px';

        // Update active UI indicator in presets grid
        document.querySelectorAll('.preset-card').forEach(card => card.classList.remove('active-preset'));
        const activeCard = document.getElementById('preset_card_' + presetKey);
        if (activeCard) activeCard.classList.add('active-preset');

        document.querySelectorAll('[id^="active_badge_"]').forEach(b => b.classList.add('d-none'));
        const activeBadge = document.getElementById('active_badge_' + presetKey);
        if (activeBadge) activeBadge.classList.remove('d-none');

        // Trigger Live Preview
        updateLivePreview();

        // Trigger Stacking Toast Notification
        dssToast.success(p.name + ' Preset Loaded', 'Live preview updated. Click "Save & Apply Theme" to persist.', 2500);
    }

    function setFieldAndSync(id, val) {
        const elem = document.getElementById(id);
        const textElem = document.getElementById(id + '_text');
        if (elem) elem.value = val;
        if (textElem) textElem.value = val;
    }

    // Dynamic Live Preview renderer
    function updateLivePreview() {
        const primary = document.getElementById('primary_color').value;
        const secondary = document.getElementById('secondary_color').value;
        const accent = document.getElementById('accent_color').value;
        const borderRadius = document.getElementById('border_radius').value;

        const sidebarBg = document.getElementById('sidebar_bg').value;
        const sidebarText = document.getElementById('sidebar_text').value;
        const sidebarActiveBg = document.getElementById('sidebar_active_bg').value;
        const sidebarActiveText = document.getElementById('sidebar_active_text').value;
        const sidebarBorder = document.getElementById('sidebar_border').value;

        const topbarBg = document.getElementById('topbar_bg').value;
        const topbarText = document.getElementById('topbar_text').value;
        const topbarBorder = document.getElementById('topbar_border').value;

        const btnPrimaryBg = document.getElementById('btn_primary_bg').value;
        const btnPrimaryText = document.getElementById('btn_primary_text').value;
        const btnSecondaryBg = document.getElementById('btn_secondary_bg').value;
        const btnSecondaryText = document.getElementById('btn_secondary_text').value;
        const btnRadius = document.getElementById('btn_radius').value;
        const btnElevation = document.getElementById('btn_elevation').value;

        const cardBg = document.getElementById('card_bg').value;
        const cardBorder = document.getElementById('card_border').value;
        const cardRadius = document.getElementById('card_radius').value;

        // 1. Mock Sidebar styling
        const mockSidebar = document.getElementById('mockSidebar');
        if (mockSidebar) {
            mockSidebar.style.backgroundColor = sidebarBg;
            mockSidebar.style.borderColor = sidebarBorder;
            mockSidebar.style.color = sidebarText;
        }

        const mockSidebarTitle = document.getElementById('mockSidebarTitle');
        if (mockSidebarTitle) mockSidebarTitle.style.color = sidebarText;

        const mockSidebarSection = document.getElementById('mockSidebarSectionTitle');
        if (mockSidebarSection) mockSidebarSection.style.color = sidebarText;

        const mockNavActive = document.getElementById('mockNavActive');
        if (mockNavActive) {
            mockNavActive.style.backgroundColor = sidebarActiveBg;
            mockNavActive.style.color = sidebarActiveText;
            mockNavActive.style.borderRadius = btnRadius;
            mockNavActive.style.border = `1px solid ${sidebarActiveText}40`;
        }

        const mockNavActiveIcon = document.getElementById('mockNavActiveIcon');
        if (mockNavActiveIcon) mockNavActiveIcon.style.color = sidebarActiveText;

        ['mockNavInactive1', 'mockNavInactive2'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.color = sidebarText;
        });

        // 2. Mock Navbar styling
        const mockNavbar = document.getElementById('mockNavbar');
        if (mockNavbar) {
            mockNavbar.style.backgroundColor = topbarBg;
            mockNavbar.style.borderColor = topbarBorder;
            mockNavbar.style.color = topbarText;
        }

        const instBadge = document.getElementById('mockNavbarInstitutionBadge');
        if (instBadge) {
            instBadge.style.backgroundColor = primary + '14';
            instBadge.style.borderColor = primary + '35';
            instBadge.style.color = primary;
        }

        const userAvatar = document.getElementById('mockUserAvatar');
        if (userAvatar) {
            userAvatar.style.backgroundColor = primary;
        }

        // 3. Mock Card styling
        const mockCard = document.getElementById('mockCard');
        if (mockCard) {
            mockCard.style.backgroundColor = cardBg;
            mockCard.style.borderColor = cardBorder;
            mockCard.style.borderRadius = cardRadius;
        }

        const mockAccentPill = document.getElementById('mockAccentPill');
        if (mockAccentPill) {
            mockAccentPill.style.backgroundColor = accent + '20';
            mockAccentPill.style.color = accent;
            mockAccentPill.style.border = `1px solid ${accent}45`;
        }

        // 4. Mock Buttons
        const mockBtnPrimary = document.getElementById('mockBtnPrimary');
        if (mockBtnPrimary) {
            mockBtnPrimary.style.backgroundColor = btnPrimaryBg;
            mockBtnPrimary.style.borderColor = btnPrimaryBg;
            mockBtnPrimary.style.color = btnPrimaryText;
            mockBtnPrimary.style.borderRadius = btnRadius;
            if (btnElevation === 'glow') {
                mockBtnPrimary.style.boxShadow = `0 4px 14px ${btnPrimaryBg}50`;
            } else if (btnElevation === 'none') {
                mockBtnPrimary.style.boxShadow = 'none';
            } else {
                mockBtnPrimary.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
            }
        }

        const mockBtnSecondary = document.getElementById('mockBtnSecondary');
        if (mockBtnSecondary) {
            mockBtnSecondary.style.backgroundColor = btnSecondaryBg;
            mockBtnSecondary.style.borderColor = btnSecondaryBg;
            mockBtnSecondary.style.color = btnSecondaryText;
            mockBtnSecondary.style.borderRadius = btnRadius;
        }

        // 5. Mock Soft Tokens
        const mockTokenPrimary = document.getElementById('mockTokenPrimary');
        if (mockTokenPrimary) {
            mockTokenPrimary.style.backgroundColor = primary + '18';
            mockTokenPrimary.style.color = primary;
            mockTokenPrimary.style.border = `1px solid ${primary}35`;
        }

        const mockTokenSecondary = document.getElementById('mockTokenSecondary');
        if (mockTokenSecondary) {
            mockTokenSecondary.style.backgroundColor = secondary + '18';
            mockTokenSecondary.style.color = secondary;
            mockTokenSecondary.style.border = `1px solid ${secondary}35`;
        }

        // Also live update CSS variables on Document Root for instant feeling
        document.documentElement.style.setProperty('--dss-primary', primary);
        document.documentElement.style.setProperty('--ma-primary', primary);
        document.documentElement.style.setProperty('--ma-primary-dark', `color-mix(in srgb, ${primary} 85%, #000000)`);
        document.documentElement.style.setProperty('--ma-primary-deeper', `color-mix(in srgb, ${primary} 75%, #0f172a)`);
        document.documentElement.style.setProperty('--ma-primary-soft', `color-mix(in srgb, ${primary} 12%, #ffffff)`);
        document.documentElement.style.setProperty('--dss-secondary', secondary);
        document.documentElement.style.setProperty('--dss-accent', accent);
        document.documentElement.style.setProperty('--dss-radius', borderRadius);
        document.documentElement.style.setProperty('--dss-sidebar-bg', sidebarBg);
        document.documentElement.style.setProperty('--dss-sidebar-text', sidebarText);
        document.documentElement.style.setProperty('--dss-sidebar-active', sidebarActiveBg);
        document.documentElement.style.setProperty('--dss-sidebar-active-text', sidebarActiveText);
        document.documentElement.style.setProperty('--dss-sidebar-border', sidebarBorder);
        document.documentElement.style.setProperty('--dss-topbar-bg', topbarBg);
        document.documentElement.style.setProperty('--dss-topbar-text', topbarText);
        document.documentElement.style.setProperty('--dss-topbar-border', topbarBorder);
        document.documentElement.style.setProperty('--dss-btn-primary-bg', btnPrimaryBg);
        document.documentElement.style.setProperty('--dss-btn-primary-text', btnPrimaryText);
        document.documentElement.style.setProperty('--dss-btn-secondary-bg', btnSecondaryBg);
        document.documentElement.style.setProperty('--dss-btn-secondary-text', btnSecondaryText);
        document.documentElement.style.setProperty('--dss-btn-radius', btnRadius);
        document.documentElement.style.setProperty('--dss-card-bg', cardBg);
        document.documentElement.style.setProperty('--dss-card-border', cardBorder);
        document.documentElement.style.setProperty('--dss-card-radius', cardRadius);
    }

    // Save Theme Settings to Backend
    function saveThemeSettings() {
        const btn = document.getElementById('btnSaveTheme');
        const btnText = document.getElementById('btnSaveText');
        const originalText = btnText.innerHTML;

        btn.setAttribute('disabled', 'disabled');
        btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';

        const payload = {
            primary_color: document.getElementById('primary_color').value,
            secondary_color: document.getElementById('secondary_color').value,
            accent_color: document.getElementById('accent_color').value,
            preset_key: document.getElementById('preset_key').value,
            sidebar_theme: document.getElementById('sidebar_theme').value || 'light',
            sidebar_bg: document.getElementById('sidebar_bg').value,
            sidebar_text: document.getElementById('sidebar_text').value,
            sidebar_active_bg: document.getElementById('sidebar_active_bg').value,
            sidebar_active_text: document.getElementById('sidebar_active_text').value,
            sidebar_border: document.getElementById('sidebar_border').value,
            navbar_theme: document.getElementById('navbar_theme').value || 'light',
            topbar_bg: document.getElementById('topbar_bg').value,
            topbar_text: document.getElementById('topbar_text').value,
            topbar_border: document.getElementById('topbar_border').value,
            border_radius: document.getElementById('border_radius').value,
            btn_primary_bg: document.getElementById('btn_primary_bg').value,
            btn_primary_text: document.getElementById('btn_primary_text').value,
            btn_secondary_bg: document.getElementById('btn_secondary_bg').value,
            btn_secondary_text: document.getElementById('btn_secondary_text').value,
            btn_radius: document.getElementById('btn_radius').value,
            btn_elevation: document.getElementById('btn_elevation').value,
            card_bg: document.getElementById('card_bg').value,
            card_border: document.getElementById('card_border').value,
            card_radius: document.getElementById('card_radius').value,
            custom_css: document.getElementById('custom_css').value
        };

        fetch("{{ route('dss.settings.theme.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Institutional Theme Saved!',
                    text: data.message || 'Theme changes persisted and applied across all modules.',
                    confirmButtonColor: 'var(--dss-primary)'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: data.message || 'Unable to update theme settings.'
                });
                btn.removeAttribute('disabled');
                btnText.innerHTML = originalText;
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network or server error while updating theme settings.'
            });
            btn.removeAttribute('disabled');
            btnText.innerHTML = originalText;
        });
    }

    // Drag and drop logo handlers
    const dropzone = document.getElementById('logoDropzone');
    const fileInput = document.getElementById('logoFileInput');
    const btnUpload = document.getElementById('btnUploadLogo');

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleLogoSelect(fileInput);
        }
    });

    function handleLogoSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];

            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Too Large',
                    text: 'Maximum allowed image size is 10MB.'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('stagedImagePreview').src = e.target.result;
                document.getElementById('stagedFileName').textContent = file.name;
                document.getElementById('stagedFileSize').textContent = formatBytes(file.size);

                document.getElementById('dropzoneEmptyState').classList.add('d-none');
                document.getElementById('dropzoneStagedState').classList.remove('d-none');
                btnUpload.removeAttribute('disabled');
            };
            reader.readAsDataURL(file);
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function submitLogoUpload() {
        if (!fileInput.files || !fileInput.files[0]) return;

        const formData = new FormData();
        formData.append('logo', fileInput.files[0]);

        btnUpload.setAttribute('disabled', 'disabled');
        btnUpload.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Generating 7 Multi-Resolution Versions...';

        fetch("{{ route('dss.settings.theme.upload_logo') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: '7 Logo Versions Generated!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: data.message || 'Unable to process logo image.'
                });
                btnUpload.removeAttribute('disabled');
                btnUpload.innerHTML = '<i class="mdi mdi-lightning-bolt"></i><span>Generate 7 Multi-Resolution Versions</span>';
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network or server error while generating logo variants.'
            });
            btnUpload.removeAttribute('disabled');
            btnUpload.innerHTML = '<i class="mdi mdi-lightning-bolt"></i><span>Generate 7 Multi-Resolution Versions</span>';
        });
    }

    function confirmRemoveLogo() {
        Swal.fire({
            title: 'Reset to Default Logo?',
            text: 'This will remove the uploaded logo and restore standard institutional default marks across the application.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Reset'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('dss.settings.theme.remove_logo') }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Logo Reset',
                            text: data.message,
                            confirmButtonColor: 'var(--dss-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    function copyAssetUrl(url) {
        if (typeof window.dssCopyToClipboard === 'function') {
            window.dssCopyToClipboard(url, 'Direct asset link copied to clipboard.');
        } else {
            const ta = document.createElement('textarea');
            ta.value = url;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            dssToast.success('Asset URL Copied', 'Direct asset link copied to clipboard.', 2200);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateLivePreview();
    });
</script>
@endsection
