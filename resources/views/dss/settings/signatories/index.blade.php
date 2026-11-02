@extends('layouts.dss.app')

@section('title', 'Document Signatory Matrix — System Configuration')

@push('styles')
<style>
    .matrix-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .matrix-card-header {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sig-tier-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
    }
    .sig-tier-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }
    .sig-tier-badge {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 20px;
    }
    .live-preview-box {
        background: #fafafa;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
    }
    /* Visual 12-Column Bar */
    .grid-bar-container {
        display: flex;
        gap: 3px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 8px;
    }
    .grid-bar-segment {
        flex: 1;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        font-family: monospace;
        border-radius: 4px;
        cursor: pointer;
        background: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
        user-select: none;
        transition: all 0.15s ease;
    }
    .grid-bar-segment:hover {
        background: #e2e8f0;
    }
    .grid-bar-segment.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Breadcrumb & Header Title -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb m-0 extra-small">
                    <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">System Configuration</li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Signatory Matrix</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-draw text-primary"></i>
                <span>Document Signatory Matrix</span>
            </h4>
            <div class="text-muted small mt-0.5">
                Standardize authorized signatories, 12-column layout placement, and sequential approval routing.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold shadow-2xs" data-bs-toggle="modal" data-bs-target="#newDocTypeModal">
                <i class="mdi mdi-plus-circle-outline me-1"></i> New Document Type
            </button>
            @if($activeDocType)
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-2xs" data-bs-toggle="modal" data-bs-target="#addSignatoryModal">
                    <i class="mdi mdi-plus me-1"></i> Add Signatory Step
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-2xs mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Selector & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="row g-3 align-items-center">
            <!-- Document Type Dropdown -->
            <div class="col-md-6 col-lg-5">
                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                    <i class="mdi mdi-file-document-outline me-1"></i> Document Type
                </label>
                <select id="docTypeSelector" class="form-select form-select-sm fw-semibold" onchange="switchDocType(this.value)">
                    @foreach($docTypes as $doc)
                        <option value="{{ $doc->id }}" {{ $selectedDocTypeId === $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }} {{ $doc->code ? '('.$doc->code.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Campus Scope -->
            <div class="col-md-6 col-lg-4">
                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                    <i class="mdi mdi-domain me-1"></i> Campus Scope
                </label>
                <select id="campusSelector" class="form-select form-select-sm" onchange="switchCampus(this.value)">
                    <option value="" {{ is_null($selectedCampusId) ? 'selected' : '' }}>All Campuses (Global Default)</option>
                    @foreach($campuses as $camp)
                        <option value="{{ $camp->id }}" {{ $selectedCampusId === $camp->id ? 'selected' : '' }}>
                            {{ $camp->campus_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Summary Status -->
            <div class="col-12 col-lg-3 text-lg-end mt-lg-4">
                @if($activeDocType)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 small fw-semibold">
                        <i class="mdi mdi-shield-check me-1"></i> {{ $matrices->count() }} Signatory Steps Active
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Signatory Flow Matrix for Selected Document Type -->
    @if($activeDocType)
        <div class="matrix-card">
            <div class="matrix-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb;">
                        <i class="mdi {{ $activeDocType->icon ?: 'mdi-file-document-outline' }} fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0">{{ $activeDocType->name }}</h6>
                        <div class="text-muted extra-small">
                            12-Column CSS Grid Placement &bull; Sequential Order &bull; {{ $selectedCampusId ? ($campuses->firstWhere('id', $selectedCampusId)?->campus_name ?? 'Campus') : 'All Campuses (Global Default)' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($matrices->isEmpty())
                        <form action="{{ route('dss.settings.signatories.seed_defaults') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                            <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 extra-small fw-bold">
                                <i class="mdi mdi-auto-fix me-1"></i> Pre-populate Standard Signatories
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="p-4">
                @if($matrices->isNotEmpty())
                    <div class="row g-3 mb-4" id="signatoryCardsContainer">
                        @foreach($matrices as $matrix)
                            @php
                                $tierColors = [
                                    'prepared' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
                                    'reviewed' => ['bg' => '#fefce8', 'text' => '#a16207', 'border' => '#fef08a'],
                                    'noted' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
                                    'concurred' => ['bg' => '#f0fdf4', 'text' => '#047857', 'border' => '#a7f3d0'],
                                    'concurred_2' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0'],
                                    'recommending_approval' => ['bg' => '#faf5ff', 'text' => '#7e22ce', 'border' => '#e9d5ff'],
                                    'approved' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
                                ];
                                $tColor = $tierColors[$matrix->tier_role] ?? ['bg' => '#f8fafc', 'text' => '#334155', 'border' => '#e2e8f0'];
                                $colSpan = $matrix->col_span ?: 6;
                                $alignment = $matrix->alignment ?: 'left';
                            @endphp
                            <div class="col-md-6 col-xl-4 matrix-item-card" data-id="{{ $matrix->id }}" data-sort="{{ $matrix->sort_order }}" data-span="{{ $colSpan }}" data-align="{{ $alignment }}">
                                <div class="sig-tier-card h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <!-- Header: Step & Move Controls -->
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <span class="sig-tier-badge" style="background: {{ $tColor['bg'] }}; color: {{ $tColor['text'] }}; border: 1px solid {{ $tColor['border'] }};">
                                                    Step {{ $matrix->sort_order }}: {{ strtoupper(str_replace('_', ' ', $matrix->tier_role)) }}
                                                </span>
                                            </div>

                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-xs btn-light border py-0 px-1.5" title="Move Step Up" onclick="moveStepOrder({{ $matrix->id }}, 'up')">
                                                    <i class="mdi mdi-arrow-up-bold"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-light border py-0 px-1.5" title="Move Step Down" onclick="moveStepOrder({{ $matrix->id }}, 'down')">
                                                    <i class="mdi mdi-arrow-down-bold"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Signatory Info -->
                                        <div class="extra-small fw-bold text-muted text-uppercase mb-1">{{ $matrix->role_label }}</div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $matrix->resolved_name }}</h6>
                                        <div class="small text-muted mb-2">{{ $matrix->resolved_designation }}</div>

                                        @if($matrix->assignment_type === 'dynamic_extension_head')
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill extra-small">
                                                <i class="mdi mdi-source-branch me-1"></i> Dynamic: College Extension Head
                                            </span>
                                        @elseif($matrix->assignment_type === 'dynamic_college_dean')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill extra-small">
                                                <i class="mdi mdi-school me-1"></i> Dynamic: College Dean / Director
                                            </span>
                                        @elseif($matrix->assignment_type === 'dynamic_author')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small">
                                                <i class="mdi mdi-account-star me-1"></i> Dynamic: Document Author / Leader
                                            </span>
                                        @elseif($matrix->assignment_type === 'role_based')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small">
                                                <i class="mdi mdi-certificate me-1"></i> Institutional Officer
                                            </span>
                                        @endif

                                        <!-- Interactive 1-Click Grid & Alignment Toolbar -->
                                        <div class="mt-3 p-2 rounded-2 bg-light border border-slate-200">
                                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                                <span class="extra-small fw-bold text-muted text-uppercase" style="font-size: 10px;">Grid Width:</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-xs {{ $colSpan == 12 ? 'btn-primary' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetSpan({{ $matrix->id }}, 12)" title="100% Full Width (Entire Row)">12 cols</button>
                                                    <button type="button" class="btn btn-xs {{ $colSpan == 6 ? 'btn-primary' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetSpan({{ $matrix->id }}, 6)" title="50% Half Width (2 across)">6 cols</button>
                                                    <button type="button" class="btn btn-xs {{ $colSpan == 4 ? 'btn-primary' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetSpan({{ $matrix->id }}, 4)" title="33% One-Third (3 across)">4 cols</button>
                                                    <button type="button" class="btn btn-xs {{ $colSpan == 3 ? 'btn-primary' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetSpan({{ $matrix->id }}, 3)" title="25% One-Fourth (4 across)">3 cols</button>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="extra-small fw-bold text-muted text-uppercase" style="font-size: 10px;">Align:</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-xs {{ $alignment === 'left' ? 'btn-dark' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetAlign({{ $matrix->id }}, 'left')">
                                                        <i class="mdi mdi-format-align-left"></i> Left
                                                    </button>
                                                    <button type="button" class="btn btn-xs {{ $alignment === 'center' ? 'btn-dark' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetAlign({{ $matrix->id }}, 'center')">
                                                        <i class="mdi mdi-format-align-center"></i> Center
                                                    </button>
                                                    <button type="button" class="btn btn-xs {{ $alignment === 'right' ? 'btn-dark' : 'btn-white border' }} py-0 px-2 extra-small" onclick="quickSetAlign({{ $matrix->id }}, 'right')">
                                                        <i class="mdi mdi-format-align-right"></i> Right
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Actions -->
                                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-1">
                                            @if($matrix->requires_digital_signature)
                                                <span class="badge bg-light text-dark border extra-small" title="Digital signature required">
                                                    <i class="mdi mdi-draw"></i> E-Sign
                                                </span>
                                            @endif
                                            @if($matrix->is_mandatory)
                                                <span class="badge bg-danger-subtle text-danger extra-small">
                                                    Required
                                                </span>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-sm btn-light border text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold" title="Edit Step" data-bs-toggle="modal" data-bs-target="#editSignatoryModal{{ $matrix->id }}">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </button>
                                            <form action="{{ route('dss.settings.signatories.destroy', $matrix->id) }}" method="POST" onsubmit="return confirm('Remove this signatory rule?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete Step">
                                                    <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal: Edit Signatory Rule {{ $matrix->id }} -->
                            <div class="modal fade" id="editSignatoryModal{{ $matrix->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header bg-primary text-white py-3 px-4">
                                            <h6 class="modal-title fw-bold">
                                                <i class="mdi mdi-pencil me-1"></i> Edit Signatory Step {{ $matrix->sort_order }} — {{ $matrix->role_label }}
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('dss.settings.signatories.update', $matrix->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Tier Action Role</label>
                                                        <select name="tier_role" class="form-select form-select-sm">
                                                            <option value="prepared" {{ $matrix->tier_role === 'prepared' ? 'selected' : '' }}>Prepared by</option>
                                                            <option value="reviewed" {{ $matrix->tier_role === 'reviewed' ? 'selected' : '' }}>Reviewed by</option>
                                                            <option value="noted" {{ $matrix->tier_role === 'noted' ? 'selected' : '' }}>Noted by</option>
                                                            <option value="concurred" {{ $matrix->tier_role === 'concurred' ? 'selected' : '' }}>Concurred by</option>
                                                            <option value="recommending_approval" {{ $matrix->tier_role === 'recommending_approval' ? 'selected' : '' }}>Recommending Approval</option>
                                                            <option value="approved" {{ $matrix->tier_role === 'approved' ? 'selected' : '' }}>Approved by</option>
                                                            <option value="certified" {{ $matrix->tier_role === 'certified' ? 'selected' : '' }}>Certified Correct</option>
                                                            <option value="custom" {{ $matrix->tier_role === 'custom' ? 'selected' : '' }}>Custom</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Display Label</label>
                                                        <input type="text" name="role_label" class="form-control form-control-sm font-semibold" value="{{ $matrix->role_label }}" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Assignee Source</label>
                                                    <select name="assignment_type" class="form-select form-select-sm" required>
                                                        <optgroup label="Dynamic by Proponent Unit">
                                                            <option value="dynamic_author" {{ $matrix->assignment_type === 'dynamic_author' ? 'selected' : '' }}>Document Author / Project Leader</option>
                                                            <option value="dynamic_extension_head" {{ $matrix->assignment_type === 'dynamic_extension_head' ? 'selected' : '' }}>College Extension Head (Proponent Unit)</option>
                                                            <option value="dynamic_college_dean" {{ $matrix->assignment_type === 'dynamic_college_dean' ? 'selected' : '' }}>College Dean / Campus Director (Proponent Unit)</option>
                                                            <option value="dynamic_dept_head" {{ $matrix->assignment_type === 'dynamic_dept_head' ? 'selected' : '' }}>Department / Unit Head</option>
                                                        </optgroup>
                                                        <optgroup label="Institutional &amp; Fixed Assignees">
                                                            <option value="role_based" {{ $matrix->assignment_type === 'role_based' ? 'selected' : '' }}>Institutional Role / Designation (Specialist V, Director, VP)</option>
                                                            <option value="fixed_employee" {{ $matrix->assignment_type === 'fixed_employee' ? 'selected' : '' }}>Specific Personnel / Employee</option>
                                                            <option value="manual_signatory" {{ $matrix->assignment_type === 'manual_signatory' ? 'selected' : '' }}>Manual (Type Name &amp; Title Directly)</option>
                                                        </optgroup>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Name (If fixed or manual)</label>
                                                    <input type="text" name="signatory_name" class="form-control form-control-sm" value="{{ $matrix->signatory_name }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Title / Designation</label>
                                                    <input type="text" name="designation" class="form-control form-control-sm" value="{{ $matrix->designation }}">
                                                </div>

                                                <!-- 12-Column CSS Grid Selector -->
                                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <label class="form-label extra-small fw-bold text-dark text-uppercase m-0">
                                                            <i class="mdi mdi-view-column-outline text-primary me-1"></i> Grid Column Width (1 to 12 Columns)
                                                        </label>
                                                        <span class="badge bg-primary extra-small font-mono" id="colSpanBadge{{ $matrix->id }}">
                                                            {{ $matrix->col_span ?: 6 }} of 12 cols
                                                        </span>
                                                    </div>

                                                    <input type="hidden" name="col_span" id="colSpanInput{{ $matrix->id }}" value="{{ $matrix->col_span ?: 6 }}">

                                                    <!-- Interactive 12-Segment Bar -->
                                                    <div class="grid-bar-container mb-2">
                                                        @for($i = 1; $i <= 12; $i++)
                                                            <div class="grid-bar-segment {{ $i <= ($matrix->col_span ?: 6) ? 'active' : '' }}"
                                                                 onclick="selectGridSpan({{ $matrix->id }}, {{ $i }})"
                                                                 title="{{ $i }} of 12 columns">
                                                                {{ $i }}
                                                            </div>
                                                        @endfor
                                                    </div>

                                                    <!-- Quick Presets -->
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectGridSpan({{ $matrix->id }}, 12)">Full Row (12 cols - 100%)</button>
                                                        <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectGridSpan({{ $matrix->id }}, 6)">Half Row (6 cols - 50%)</button>
                                                        <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectGridSpan({{ $matrix->id }}, 4)">One-Third (4 cols - 33%)</button>
                                                        <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectGridSpan({{ $matrix->id }}, 3)">One-Fourth (3 cols - 25%)</button>
                                                    </div>
                                                </div>

                                                <!-- Text & Signature Alignment -->
                                                <div class="row g-3 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Alignment</label>
                                                        <select name="alignment" class="form-select form-select-sm">
                                                            <option value="left" {{ ($matrix->alignment ?: 'left') === 'left' ? 'selected' : '' }}>Left Align</option>
                                                            <option value="center" {{ $matrix->alignment === 'center' ? 'selected' : '' }}>Center Align</option>
                                                            <option value="right" {{ $matrix->alignment === 'right' ? 'selected' : '' }}>Right Align</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Signing Order (Step)</label>
                                                        <input type="number" name="sort_order" class="form-control form-control-sm font-bold" value="{{ $matrix->sort_order }}" min="1" required>
                                                    </div>
                                                </div>

                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="requires_digital_signature" value="1" id="digitalSigCheck{{ $matrix->id }}" {{ $matrix->requires_digital_signature ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-semibold text-dark" for="digitalSigCheck{{ $matrix->id }}">
                                                        Requires Electronic / Digital Signature
                                                    </label>
                                                </div>

                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="mandatoryCheck{{ $matrix->id }}" {{ $matrix->is_mandatory ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-semibold text-dark" for="mandatoryCheck{{ $matrix->id }}">
                                                        Mandatory Step (Required before final approval)
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light py-2.5 px-4">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Visual Signature Block Preview (Dynamic 12-Column Layout Engine) -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-eye-outline text-primary fs-5"></i>
                                <span class="extra-small fw-bold text-uppercase text-dark" style="letter-spacing: 0.5px;">Live 12-Column Document Signature Block Preview</span>
                            </div>
                            <span class="badge bg-light border text-muted extra-small font-mono">
                                Exact PDF & Printout Output
                            </span>
                        </div>
                        <div class="live-preview-box">
                            <div class="row g-4" id="liveBlockPreview">
                                @foreach($matrices as $m)
                                    @php
                                        $mSpan = $m->col_span ?: 6;
                                        $mAlign = $m->alignment ?: 'left';
                                        $lineMargin = ($mAlign === 'center') ? 'margin: 0 auto;' : (($mAlign === 'right') ? 'margin-left: auto; margin-right: 0;' : 'margin-right: auto; margin-left: 0;');
                                    @endphp
                                    <div class="col-12 col-md-{{ $mSpan }} text-{{ $mAlign }} preview-col-item" data-id="{{ $m->id }}">
                                        @if($m->role_label && $m->role_label !== '.')
                                            <div class="extra-small fw-bold text-uppercase text-muted mb-3">{{ $m->role_label }}</div>
                                        @else
                                            <div class="extra-small fw-bold text-uppercase text-muted mb-3">&nbsp;</div>
                                        @endif
                                        <div style="height: 32px; margin-bottom: 6px;"></div>
                                        <div class="fw-bold text-dark text-uppercase" style="font-size: 13px;">{{ $m->resolved_name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $m->resolved_designation }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 52px; height: 52px; background: #eff6ff; color: #2563eb;">
                            <i class="mdi mdi-draw-pen fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Signatory Steps Defined</h6>
                        <p class="text-muted small mb-4" style="max-width: 420px; margin: 0 auto;">
                            Set up the authorized approval workflow for {{ $activeDocType->name }}. You can pre-populate standard institutional signatories with one click.
                        </p>
                        <form action="{{ route('dss.settings.signatories.seed_defaults') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                            <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-2xs fw-semibold">
                                <i class="mdi mdi-auto-fix me-1"></i> Pre-populate Standard Signatories
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal: Add Signatory Rule -->
@if($activeDocType)
<div class="modal fade" id="addSignatoryModal" tabindex="-1" aria-labelledby="addSignatoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h6 class="modal-title fw-bold" id="addSignatoryModalLabel">
                    <i class="mdi mdi-draw me-1"></i> Add Signatory Step — {{ $activeDocType->name }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dss.settings.signatories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="doc_type_id" value="{{ $activeDocType->id }}">
                <input type="hidden" name="campus_id" value="{{ $selectedCampusId }}">

                <div class="modal-body p-4">
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Tier Action Role</label>
                            <select name="tier_role" id="tierRoleSelect" class="form-select form-select-sm" onchange="syncTierToDisplayLabel(this.value)">
                                <option value="prepared">Prepared by</option>
                                <option value="reviewed">Reviewed by</option>
                                <option value="noted" selected>Noted by</option>
                                <option value="concurred">Concurred by</option>
                                <option value="recommending_approval">Recommending Approval</option>
                                <option value="approved">Approved by</option>
                                <option value="certified">Certified Correct</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Display Label</label>
                            <input type="text" name="role_label" id="roleLabelInput" class="form-control form-control-sm font-semibold" placeholder="e.g. Noted by:" value="Noted by:" required>
                        </div>
                    </div>

                    <!-- Presets -->
                    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-3 p-2 rounded-2 bg-light border">
                        <span class="extra-small text-muted me-1">Presets:</span>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small" onclick="setQuickLabel('prepared', 'Prepared by:')">Prepared by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small" onclick="setQuickLabel('reviewed', 'Reviewed by:')">Reviewed by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small" onclick="setQuickLabel('noted', 'Noted by:')">Noted by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small" onclick="setQuickLabel('concurred', 'Concurred by:')">Concurred by:</button>
                        <button type="button" class="btn btn-xs btn-white border shadow-2xs py-0.5 px-2 extra-small" onclick="setQuickLabel('approved', 'Approved by:')">Approved by:</button>
                    </div>

                    <!-- Assignment Type -->
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Assignee Source</label>
                        <select name="assignment_type" id="assignmentTypeSelect" class="form-select form-select-sm" required onchange="toggleAssigneeFields()">
                            <optgroup label="Dynamic by Proponent Unit">
                                <option value="dynamic_author">Document Author / Project Leader</option>
                                <option value="dynamic_extension_head">College Extension Head (Proponent Unit)</option>
                                <option value="dynamic_college_dean">College Dean / Campus Director (Proponent Unit)</option>
                                <option value="dynamic_dept_head">Department / Unit Head</option>
                            </optgroup>
                            <optgroup label="Institutional &amp; Fixed Assignees">
                                <option value="role_based">Institutional Role / Designation (Specialist V, Director, VP)</option>
                                <option value="fixed_employee" selected>Specific Personnel / Employee</option>
                                <option value="manual_signatory">Manual (Type Name &amp; Title Directly)</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Specific Employee Select (Server-Side Select2) -->
                    <div class="mb-3" id="employeeSelectWrap">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Select Personnel</label>
                        <select name="employee_id" id="employeeSelect" class="form-select form-select-sm" style="width: 100%;">
                            <option value="">-- Type to search employee --</option>
                        </select>
                    </div>

                    <!-- Manual Signatory Name -->
                    <div class="mb-3" id="manualSignatoryWrap" style="display: none;">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Signatory Full Name</label>
                        <input type="text" name="signatory_name" id="manualSignatoryNameInput" class="form-control form-control-sm font-semibold" placeholder="e.g. Dr. Juan Dela Cruz, Ph.D.">
                    </div>

                    <!-- Title / Designation -->
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Title / Designation</label>
                        <input type="text" name="designation" id="designationInput" class="form-control form-control-sm" placeholder="e.g. Campus Director / Extension Head">
                    </div>

                    <!-- 12-Column CSS Grid Selector -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label extra-small fw-bold text-dark text-uppercase m-0">
                                <i class="mdi mdi-view-column-outline text-primary me-1"></i> Grid Column Width (1 to 12 Columns)
                            </label>
                            <span class="badge bg-primary extra-small font-mono" id="addColSpanBadge">
                                6 of 12 cols
                            </span>
                        </div>

                        <input type="hidden" name="col_span" id="addColSpanInput" value="6">

                        <!-- Interactive 12-Segment Bar -->
                        <div class="grid-bar-container mb-2">
                            @for($i = 1; $i <= 12; $i++)
                                <div class="grid-bar-segment {{ $i <= 6 ? 'active' : '' }}"
                                     onclick="selectAddGridSpan({{ $i }})"
                                     title="{{ $i }} of 12 columns">
                                    {{ $i }}
                                </div>
                            @endfor
                        </div>

                        <!-- Quick Presets -->
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectAddGridSpan(12)">Full Row (12 cols - 100%)</button>
                            <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectAddGridSpan(6)">Half Row (6 cols - 50%)</button>
                            <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectAddGridSpan(4)">One-Third (4 cols - 33%)</button>
                            <button type="button" class="btn btn-xs btn-white border extra-small" onclick="selectAddGridSpan(3)">One-Fourth (3 cols - 25%)</button>
                        </div>
                    </div>

                    <!-- Layout & Order -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Alignment</label>
                            <select name="alignment" class="form-select form-select-sm">
                                <option value="left" selected>Left Align</option>
                                <option value="center">Center Align</option>
                                <option value="right">Right Align</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Signing Order (Step)</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm font-bold" value="{{ $matrices->count() + 1 }}" min="1" required>
                        </div>
                    </div>

                    <!-- Switches -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="requires_digital_signature" value="1" id="digitalSigCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="digitalSigCheck">
                            Requires Electronic / Digital Signature
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="mandatoryCheck" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="mandatoryCheck">
                            Mandatory Step (Required before final approval)
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Save Signatory Step</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal: New Document Type -->
<div class="modal fade" id="newDocTypeModal" tabindex="-1" aria-labelledby="newDocTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold" id="newDocTypeModalLabel">
                    <i class="mdi mdi-file-plus-outline me-1"></i> Register New Document Type
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dss.settings.signatories.document_type.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Document Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm font-semibold" placeholder="e.g. Extension Plan, Activity Design, Terminal Report" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Document Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control form-control-sm font-mono text-uppercase" placeholder="e.g. EXT-PLAN, ACT-DES" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase">Category</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="RIE & Extension" selected>RIE & Extension</option>
                                <option value="Academic Affairs">Academic Affairs</option>
                                <option value="Administration & Finance">Administration & Finance</option>
                                <option value="Student Services">Student Services</option>
                                <option value="General Institution">General Institution</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">Description / Purpose</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Briefly describe what this document is used for..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">
                        <i class="mdi mdi-check me-1"></i> Create Document Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchDocType(docTypeId) {
        const campusId = '{{ $selectedCampusId ?? "" }}';
        let url = '{{ route("dss.settings.signatories.index") }}?doc_type_id=' + docTypeId;
        if (campusId) {
            url += '&campus_id=' + campusId;
        }
        window.location.href = url;
    }

    function switchCampus(campusId) {
        const docTypeId = '{{ $selectedDocTypeId ?? "" }}';
        let url = '{{ route("dss.settings.signatories.index") }}?';
        if (docTypeId) {
            url += 'doc_type_id=' + docTypeId + '&';
        }
        url += 'campus_id=' + campusId;
        window.location.href = url;
    }

    // Interactive 12-Segment Bar Selection for Edit Modal
    function selectGridSpan(matrixId, span) {
        const input = document.getElementById('colSpanInput' + matrixId);
        const badge = document.getElementById('colSpanBadge' + matrixId);
        if (input) input.value = span;
        if (badge) badge.textContent = span + ' of 12 cols';

        const modal = document.getElementById('editSignatoryModal' + matrixId);
        if (modal) {
            const segments = modal.querySelectorAll('.grid-bar-segment');
            segments.forEach((seg, idx) => {
                if (idx < span) {
                    seg.classList.add('active');
                } else {
                    seg.classList.remove('active');
                }
            });
        }
    }

    // Interactive 12-Segment Bar Selection for Add Modal
    function selectAddGridSpan(span) {
        const input = document.getElementById('addColSpanInput');
        const badge = document.getElementById('addColSpanBadge');
        if (input) input.value = span;
        if (badge) badge.textContent = span + ' of 12 cols';

        const modal = document.getElementById('addSignatoryModal');
        if (modal) {
            const segments = modal.querySelectorAll('.grid-bar-segment');
            segments.forEach((seg, idx) => {
                if (idx < span) {
                    seg.classList.add('active');
                } else {
                    seg.classList.remove('active');
                }
            });
        }
    }

    // Quick 1-Click Width Change on Matrix Card
    function quickSetSpan(matrixId, span) {
        fetch('{{ url("dss/settings/signatories/quick-update") }}/' + matrixId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ col_span: span })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update live preview column class
                const previewCol = document.querySelector('.preview-col-item[data-id="' + matrixId + '"]');
                if (previewCol) {
                    previewCol.className = previewCol.className.replace(/col-md-\d+/, 'col-md-' + span);
                }
                // Reload smoothly
                window.location.reload();
            }
        });
    }

    // Quick 1-Click Alignment Change on Matrix Card
    function quickSetAlign(matrixId, align) {
        fetch('{{ url("dss/settings/signatories/quick-update") }}/' + matrixId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ alignment: align })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }

    // Quick Step Order Up / Down Reorder
    function moveStepOrder(matrixId, direction) {
        const cards = Array.from(document.querySelectorAll('.matrix-item-card'));
        const currentIndex = cards.findIndex(c => c.getAttribute('data-id') == matrixId);
        if (currentIndex === -1) return;

        let targetIndex = (direction === 'up') ? currentIndex - 1 : currentIndex + 1;
        if (targetIndex < 0 || targetIndex >= cards.length) return;

        // Swap elements in array
        const temp = cards[currentIndex];
        cards[currentIndex] = cards[targetIndex];
        cards[targetIndex] = temp;

        // Reassign sort_order sequentially
        const payload = cards.map((card, idx) => ({
            id: parseInt(card.getAttribute('data-id')),
            sort_order: idx + 1,
            col_span: parseInt(card.getAttribute('data-span') || 6),
            alignment: card.getAttribute('data-align') || 'left'
        }));

        fetch('{{ route("dss.settings.signatories.update_layout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: payload })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }

    $(document).ready(function () {
        if ($('#employeeSelect').length) {
            $('#employeeSelect').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addSignatoryModal'),
                width: '100%',
                placeholder: 'Search employee by name or designation...',
                allowClear: true,
                ajax: {
                    url: '{{ route("dss.select2.employees") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            campus_id: '{{ $selectedCampusId ?? "" }}'
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: (data.results || []).map(function (emp) {
                                return {
                                    id: emp.id,
                                    text: emp.text || emp.full_name,
                                    name: emp.full_name || emp.name,
                                    designation: emp.designation
                                };
                            }),
                            pagination: data.pagination
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
                const data = e.params.data;
                if (data && data.designation) {
                    $('#designationInput').val(data.designation);
                }
            });
        }
    });

    function toggleAssigneeFields() {
        const type = document.getElementById('assignmentTypeSelect').value;
        const employeeWrap = document.getElementById('employeeSelectWrap');
        const manualWrap = document.getElementById('manualSignatoryWrap');

        if (type === 'fixed_employee') {
            if (employeeWrap) employeeWrap.style.display = 'block';
            if (manualWrap) manualWrap.style.display = 'none';
        } else if (type === 'manual_signatory') {
            if (employeeWrap) employeeWrap.style.display = 'none';
            if (manualWrap) manualWrap.style.display = 'block';
        } else {
            if (employeeWrap) employeeWrap.style.display = 'none';
            if (manualWrap) manualWrap.style.display = 'none';
        }
    }

    function syncTierToDisplayLabel(tier) {
        const labelMap = {
            'prepared': 'Prepared by:',
            'reviewed': 'Reviewed by:',
            'noted': 'Noted by:',
            'concurred': 'Concurred by:',
            'recommending_approval': 'Recommending Approval:',
            'approved': 'Approved by:',
            'certified': 'Certified Correct:',
            'custom': ''
        };
        const input = document.getElementById('roleLabelInput');
        if (input && labelMap[tier] !== undefined) {
            input.value = labelMap[tier];
            input.focus();
        }
    }

    function setQuickLabel(tier, label) {
        const tierSelect = document.getElementById('tierRoleSelect');
        const input = document.getElementById('roleLabelInput');
        if (tierSelect) {
            tierSelect.value = tier;
        }
        if (input) {
            input.value = label;
            input.focus();
        }
    }
</script>
@endpush
@endsection
