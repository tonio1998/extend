@extends('layouts.dss.app')

@section('title', 'Extension Agendas & Banner Programs — SNSU DSS')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    /* Compact Priority Stat Cards */
    .priority-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .priority-stat-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }
    .priority-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    .priority-stat-card.accent-emerald::before { background-color: #059669; }
    .priority-stat-card.accent-blue::before { background-color: #2563eb; }
    .priority-stat-card.accent-purple::before { background-color: #7c3aed; }
    .priority-stat-card.accent-amber::before { background-color: #d97706; }

    .stat-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .nav-tabs-custom .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 18px;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link.active {
        color: #047857;
        background: transparent;
        border-bottom: 2px solid #047857;
    }
    .nav-tabs-custom .nav-link:hover:not(.active) {
        color: #1e293b;
        border-bottom: 2px solid #cbd5e1;
    }

    /* DataTables Custom Controls */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">Programs &amp; Projects</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size: 12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">RIE Priorities</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-target-variant text-success fs-5"></i>
                Extension Agendas &amp; Banner Programs
            </h3>
            <p class="text-muted small mb-0">
                Institutional framework definitions for <strong>{{ $tenant->school_name ?? ($institution->school_name ?? 'SNSU') }}</strong> governing thematic alignment, SDG linkages, and involved extension initiatives.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-file-document-outline text-muted"></i>
                <span>Proposals Directory</span>
            </a>
            <button type="button" class="btn btn-sm btn-success px-3 py-1 fw-semibold shadow-2xs d-flex align-items-center gap-1" id="btnAddNewRecord">
                <i class="mdi mdi-plus-circle fs-6"></i>
                <span id="btnAddNewRecordLabel">Add Banner Program</span>
            </button>
        </div>
    </div>

    <!-- Sleek Compact KPI Metric Row -->
    <div class="row g-2 mb-3">
        {{-- Banner Programs Stat --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-emerald">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-0">Banner Programs</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $totalBannerPrograms }}</div>
                        <div class="extra-small mt-0.5">
                            <span class="text-success fw-semibold"><i class="mdi mdi-check-circle me-0.5"></i>{{ $activeBannerPrograms }} Active</span>
                            <span class="text-muted">· {{ $inactiveBannerPrograms }} Inactive</span>
                        </div>
                    </div>
                    <div class="stat-icon-box bg-success-subtle text-success">
                        <i class="mdi mdi-flag-variant"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Extension Agendas Stat --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-0">Extension Agendas</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $totalAgendas }}</div>
                        <div class="extra-small mt-0.5">
                            <span class="text-primary fw-semibold"><i class="mdi mdi-check-circle me-0.5"></i>{{ $activeAgendas }} Active</span>
                            <span class="text-muted">· {{ $inactiveAgendas }} Inactive</span>
                        </div>
                    </div>
                    <div class="stat-icon-box bg-primary-subtle text-primary">
                        <i class="mdi mdi-compass"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Involved Extensions Stat --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-0">Involved Extensions</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $totalInvolvedProposals }}</div>
                        <div class="extra-small mt-0.5 text-truncate" style="color: #7c3aed;">
                            <span class="fw-semibold"><i class="mdi mdi-file-document-check-outline me-0.5"></i>{{ $totalInvolvedProposals }} Aligned Proposals</span>
                        </div>
                    </div>
                    <div class="stat-icon-box" style="background: #f3e8ff; color: #7c3aed;">
                        <i class="mdi mdi-file-tree-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Priorities Stat --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-amber">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-0">Active Coverage</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $activePriorities }}</div>
                        <div class="extra-small mt-0.5 text-muted">
                            <span class="fw-semibold text-warning-emphasis"><i class="mdi mdi-shield-check me-0.5"></i>{{ $totalPriorities > 0 ? round(($activePriorities / $totalPriorities) * 100) : 0 }}% active</span>
                            <span>({{ $activePriorities }} of {{ $totalPriorities }})</span>
                        </div>
                    </div>
                    <div class="stat-icon-box bg-warning-subtle text-warning">
                        <i class="mdi mdi-target-variant"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="priorityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'banner_programs' ? 'active' : '' }}" id="banner-programs-tab" data-bs-toggle="tab" data-bs-target="#banner-programs" type="button" role="tab" aria-controls="banner-programs" aria-selected="{{ $activeTab === 'banner_programs' ? 'true' : 'false' }}">
                        <i class="mdi mdi-flag-variant me-1"></i>
                        <span>Banner Programs</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $totalBannerPrograms }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'agendas' ? 'active' : '' }}" id="agendas-tab" data-bs-toggle="tab" data-bs-target="#agendas" type="button" role="tab" aria-controls="agendas" aria-selected="{{ $activeTab === 'agendas' ? 'true' : 'false' }}">
                        <i class="mdi mdi-compass me-1"></i>
                        <span>Extension Agendas &amp; Thrusts</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $totalAgendas }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="priorityTabsContent">
                <!-- TAB 1: BANNER PROGRAMS -->
                <div class="tab-pane fade {{ $activeTab === 'banner_programs' ? 'show active' : '' }}" id="banner-programs" role="tabpanel" aria-labelledby="banner-programs-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Institutional Flagship Banner Programs</h6>
                            <p class="text-muted extra-small mb-0">High-impact university umbrella programs under which individual extension projects are grouped.</p>
                        </div>
                        <span class="badge bg-light text-dark border font-mono px-3 py-1">
                            {{ $totalBannerPrograms }} Registered Banner Programs
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="bannerProgramsTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    <th style="width: 100px;">Actions</th>
                                    <th style="width: 60px;">Order</th>
                                    <th style="width: 120px;">Code</th>
                                    <th style="min-width: 200px;">Program Title</th>
                                    <th style="min-width: 220px;">Scope &amp; Description</th>
                                    <th style="width: 150px;">Involved Extensions</th>
                                    <th style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bannerPrograms as $program)
                                    @php
                                        $propCount = $bannerProposalCounts[$program->id] ?? 0;
                                        $planCount = $bannerPlanCounts[$program->id] ?? 0;
                                    @endphp
                                    <tr id="banner-row-{{ $program->id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 edit-banner-btn"
                                                    title="Edit Program"
                                                    data-id="{{ $program->id }}"
                                                    data-code="{{ $program->code }}"
                                                    data-name="{{ $program->name }}"
                                                    data-description="{{ $program->description }}"
                                                    data-sort="{{ $program->sort_order }}"
                                                    data-active="{{ $program->is_active ? 1 : 0 }}">
                                                    <i class="mdi mdi-pencil text-primary"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 toggle-banner-btn"
                                                    title="{{ $program->is_active ? 'Set Inactive' : 'Activate' }}"
                                                    data-id="{{ $program->id }}"
                                                    data-name="{{ $program->name }}"
                                                    data-active="{{ $program->is_active ? 1 : 0 }}">
                                                    <i class="mdi {{ $program->is_active ? 'mdi-pause text-warning' : 'mdi-play text-success' }}"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 delete-banner-btn"
                                                    title="Delete Program"
                                                    data-id="{{ $program->id }}"
                                                    data-name="{{ $program->name }}"
                                                    data-proposals="{{ $propCount }}">
                                                    <i class="mdi mdi-trash-can-outline text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono">#{{ $program->sort_order ?? $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1">
                                                {{ $program->code ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $program->name }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $program->description ? \Illuminate\Support\Str::limit($program->description, 85) : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($propCount > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1 d-inline-flex align-items-center gap-1" style="width: fit-content;">
                                                        <i class="mdi mdi-file-document-outline"></i>
                                                        <span>{{ $propCount }} {{ Str::plural('Proposal', $propCount) }}</span>
                                                    </span>
                                                    @if($planCount > 0)
                                                        <span class="extra-small text-muted font-mono">
                                                            <i class="mdi mdi-clipboard-text-outline me-0.5"></i>{{ $planCount }} {{ Str::plural('Plan', $planCount) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border font-mono px-2 py-1">
                                                    0 Proposals
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($program->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="mdi mdi-check-circle me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                    <i class="mdi mdi-pause-circle me-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-flag-variant-outline fs-2 d-block mb-2 text-muted opacity-50"></i>
                                            No banner programs defined yet. Click "Add Banner Program" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: EXTENSION AGENDAS & THRUSTS -->
                <div class="tab-pane fade {{ $activeTab === 'agendas' ? 'show active' : '' }}" id="agendas" role="tabpanel" aria-labelledby="agendas-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Institutional Extension Agendas &amp; Thrusts</h6>
                            <p class="text-muted extra-small mb-0">Strategic research &amp; extension priorities aligned with regional development goals and CHED mandates.</p>
                        </div>
                        <span class="badge bg-light text-dark border font-mono px-3 py-1">
                            {{ $totalAgendas }} Registered Agendas
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="agendasTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    <th style="width: 100px;">Actions</th>
                                    <th style="width: 60px;">Order</th>
                                    <th style="width: 120px;">Code</th>
                                    <th style="min-width: 200px;">Agenda Title</th>
                                    <th style="min-width: 220px;">Strategic Objective / Scope</th>
                                    <th style="width: 150px;">Involved Extensions</th>
                                    <th style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendas as $agenda)
                                    @php
                                        $agendaPropCount = $agendaProposalCounts[$agenda->id] ?? 0;
                                        $agendaPnCount = $agendaPlanCounts[$agenda->id] ?? 0;
                                    @endphp
                                    <tr id="agenda-row-{{ $agenda->id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 edit-agenda-btn"
                                                    title="Edit Agenda"
                                                    data-id="{{ $agenda->id }}"
                                                    data-code="{{ $agenda->code }}"
                                                    data-name="{{ $agenda->name }}"
                                                    data-description="{{ $agenda->description }}"
                                                    data-sort="{{ $agenda->sort_order }}"
                                                    data-active="{{ $agenda->is_active ? 1 : 0 }}">
                                                    <i class="mdi mdi-pencil text-primary"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 toggle-agenda-btn"
                                                    title="{{ $agenda->is_active ? 'Set Inactive' : 'Activate' }}"
                                                    data-id="{{ $agenda->id }}"
                                                    data-name="{{ $agenda->name }}"
                                                    data-active="{{ $agenda->is_active ? 1 : 0 }}">
                                                    <i class="mdi {{ $agenda->is_active ? 'mdi-pause text-warning' : 'mdi-play text-success' }}"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1 delete-agenda-btn"
                                                    title="Delete Agenda"
                                                    data-id="{{ $agenda->id }}"
                                                    data-name="{{ $agenda->name }}"
                                                    data-proposals="{{ $agendaPropCount }}">
                                                    <i class="mdi mdi-trash-can-outline text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono">#{{ $agenda->sort_order ?? $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-mono px-2 py-1">
                                                {{ $agenda->code ?: 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $agenda->name }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $agenda->description ? \Illuminate\Support\Str::limit($agenda->description, 85) : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($agendaPropCount > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-mono px-2 py-1 d-inline-flex align-items-center gap-1" style="width: fit-content;">
                                                        <i class="mdi mdi-compass-outline"></i>
                                                        <span>{{ $agendaPropCount }} {{ Str::plural('Proposal', $agendaPropCount) }}</span>
                                                    </span>
                                                    @if($agendaPnCount > 0)
                                                        <span class="extra-small text-muted font-mono">
                                                            <i class="mdi mdi-clipboard-text-outline me-0.5"></i>{{ $agendaPnCount }} {{ Str::plural('Plan', $agendaPnCount) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border font-mono px-2 py-1">
                                                    0 Proposals
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($agenda->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="mdi mdi-check-circle me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                    <i class="mdi mdi-pause-circle me-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-compass-outline fs-2 d-block mb-2 text-muted opacity-50"></i>
                                            No extension agendas defined yet. Click "Add Extension Agenda" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADD BANNER PROGRAM -->
<div class="modal fade" id="addBannerProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('dss.settings.rie_priorities.banner_programs.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-flag-variant text-success me-1"></i>Add Flagship Banner Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono" placeholder="e.g. TECHASSIST, STEAMUP, FOODPRO" required>
                        <div class="form-text extra-small">Short identifier or acronym used in proposals and tracking.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Name / Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Technology Transfer &amp; Community Empowerment" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Scope &amp; Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Focal objective, community target group, and thematic scope..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Display Sort Order</label>
                            <input type="number" name="sort_order" class="form-control font-mono" value="{{ $totalBannerPrograms + 1 }}" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addBannerIsActive" checked>
                                <label class="form-check-label small fw-semibold" for="addBannerIsActive">Active &amp; Selectable</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success px-4 fw-semibold shadow-2xs">
                        <i class="mdi mdi-check me-1"></i>Save Banner Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT BANNER PROGRAM -->
<div class="modal fade" id="editBannerProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="editBannerProgramForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-pencil text-primary me-1"></i>Edit Banner Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="editBannerCode" class="form-control font-mono" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Name / Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editBannerName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Scope &amp; Description</label>
                        <textarea name="description" id="editBannerDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Display Sort Order</label>
                            <input type="number" name="sort_order" id="editBannerSort" class="form-control font-mono" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editBannerIsActive">
                                <label class="form-check-label small fw-semibold" for="editBannerIsActive">Active &amp; Selectable</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-2xs">
                        <i class="mdi mdi-check me-1"></i>Update Banner Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: ADD AGENDA -->
<div class="modal fade" id="addAgendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('dss.settings.rie_priorities.agendas.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-compass text-info me-1"></i>Add Extension Agenda / Thrust
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agenda Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono" placeholder="e.g. EA-1, AG-01, ENVI, FOOD-SEC" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agenda / Thrust Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sustainable Agriculture, Food Security &amp; Nutrition" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Scope &amp; Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Strategic priority details and intended developmental impacts..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Display Sort Order</label>
                            <input type="number" name="sort_order" class="form-control font-mono" value="{{ $totalAgendas + 1 }}" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addAgendaIsActive" checked>
                                <label class="form-check-label small fw-semibold" for="addAgendaIsActive">Active &amp; Selectable</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-info text-white px-4 fw-semibold shadow-2xs">
                        <i class="mdi mdi-check me-1"></i>Save Extension Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT AGENDA -->
<div class="modal fade" id="editAgendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="editAgendaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-pencil text-primary me-1"></i>Edit Extension Agenda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agenda Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="editAgendaCode" class="form-control font-mono" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agenda / Thrust Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editAgendaName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Scope &amp; Description</label>
                        <textarea name="description" id="editAgendaDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Display Sort Order</label>
                            <input type="number" name="sort_order" id="editAgendaSort" class="form-control font-mono" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editAgendaIsActive">
                                <label class="form-check-label small fw-semibold" for="editAgendaIsActive">Active &amp; Selectable</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-2xs">
                        <i class="mdi mdi-check me-1"></i>Update Extension Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/responsive.bootstrap5.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Initialize DataTables
    let bannerDt = null;
    let agendasDt = null;

    if ($.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable('#bannerProgramsTable')) {
            bannerDt = $('#bannerProgramsTable').DataTable({
                responsive: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0] }
                ],
                language: {
                    search: "Search Banner Programs:",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        }

        if (!$.fn.DataTable.isDataTable('#agendasTable')) {
            agendasDt = $('#agendasTable').DataTable({
                responsive: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0] }
                ],
                language: {
                    search: "Search Agendas:",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        }
    }

    // Track active tab and update "Add New" button + adjust DataTable columns
    const btnAddNew = document.getElementById('btnAddNewRecord');
    const btnLabel = document.getElementById('btnAddNewRecordLabel');

    function updateAddButton() {
        const activeTabEl = document.querySelector('#priorityTabs .nav-link.active');
        if (activeTabEl && activeTabEl.id === 'agendas-tab') {
            btnLabel.textContent = 'Add Extension Agenda';
            btnAddNew.onclick = function () {
                new bootstrap.Modal(document.getElementById('addAgendaModal')).show();
            };
            if (agendasDt) {
                agendasDt.columns.adjust().draw(false);
            }
        } else {
            btnLabel.textContent = 'Add Banner Program';
            btnAddNew.onclick = function () {
                new bootstrap.Modal(document.getElementById('addBannerProgramModal')).show();
            };
            if (bannerDt) {
                bannerDt.columns.adjust().draw(false);
            }
        }
    }

    // Attach tab click listener
    document.querySelectorAll('#priorityTabs button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function () {
            updateAddButton();
        });
    });

    updateAddButton();

    // Edit Banner Program
    document.querySelectorAll('.edit-banner-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-description');
            const sort = this.getAttribute('data-sort');
            const active = this.getAttribute('data-active') === '1';

            document.getElementById('editBannerProgramForm').action = "{{ url('dss/settings/rie-priorities/banner-programs') }}/" + id;
            document.getElementById('editBannerCode').value = code || '';
            document.getElementById('editBannerName').value = name || '';
            document.getElementById('editBannerDescription').value = desc || '';
            document.getElementById('editBannerSort').value = sort || '';
            document.getElementById('editBannerIsActive').checked = active;

            new bootstrap.Modal(document.getElementById('editBannerProgramModal')).show();
        });
    });

    // Toggle Banner Program Status
    document.querySelectorAll('.toggle-banner-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const currentActive = this.getAttribute('data-active') === '1';
            const actionText = currentActive ? 'deactivate' : 'activate';

            Swal.fire({
                title: 'Change Status?',
                text: `Are you sure you want to ${actionText} '${name}'?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#047857',
                cancelButtonColor: '#64748b',
                confirmButtonText: `Yes, ${actionText}`
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ url('dss/settings/rie-priorities/banner-programs') }}/" + id + "/toggle-status", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Failed to update status', 'error'));
                }
            });
        });
    });

    // Delete Banner Program
    document.querySelectorAll('.delete-banner-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const proposals = parseInt(this.getAttribute('data-proposals') || '0', 10);

            let warningText = `Are you sure you want to remove '${name}'?`;
            if (proposals > 0) {
                warningText += ` There are ${proposals} proposal(s) currently linked to this banner program.`;
            }

            Swal.fire({
                title: 'Delete Banner Program?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ url('dss/settings/rie-priorities/banner-programs') }}/" + id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                const row = document.getElementById('banner-row-' + id);
                                if (bannerDt && row) {
                                    bannerDt.row(row).remove().draw();
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Failed to delete banner program', 'error'));
                }
            });
        });
    });

    // Edit Agenda
    document.querySelectorAll('.edit-agenda-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-description');
            const sort = this.getAttribute('data-sort');
            const active = this.getAttribute('data-active') === '1';

            document.getElementById('editAgendaForm').action = "{{ url('dss/settings/rie-priorities/agendas') }}/" + id;
            document.getElementById('editAgendaCode').value = code || '';
            document.getElementById('editAgendaName').value = name || '';
            document.getElementById('editAgendaDescription').value = desc || '';
            document.getElementById('editAgendaSort').value = sort || '';
            document.getElementById('editAgendaIsActive').checked = active;

            new bootstrap.Modal(document.getElementById('editAgendaModal')).show();
        });
    });

    // Toggle Agenda Status
    document.querySelectorAll('.toggle-agenda-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const currentActive = this.getAttribute('data-active') === '1';
            const actionText = currentActive ? 'deactivate' : 'activate';

            Swal.fire({
                title: 'Change Status?',
                text: `Are you sure you want to ${actionText} '${name}'?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#047857',
                cancelButtonColor: '#64748b',
                confirmButtonText: `Yes, ${actionText}`
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ url('dss/settings/rie-priorities/agendas') }}/" + id + "/toggle-status", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                const url = new URL(window.location.href);
                                url.searchParams.set('tab', 'agendas');
                                window.location.href = url.toString();
                            });
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Failed to update status', 'error'));
                }
            });
        });
    });

    // Delete Agenda
    document.querySelectorAll('.delete-agenda-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const proposals = parseInt(this.getAttribute('data-proposals') || '0', 10);

            let warningText = `Are you sure you want to remove '${name}'?`;
            if (proposals > 0) {
                warningText += ` There are ${proposals} proposal(s) currently linked to this agenda.`;
            }

            Swal.fire({
                title: 'Delete Extension Agenda?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ url('dss/settings/rie-priorities/agendas') }}/" + id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                const row = document.getElementById('agenda-row-' + id);
                                if (agendasDt && row) {
                                    agendasDt.row(row).remove().draw();
                                } else {
                                    const url = new URL(window.location.href);
                                    url.searchParams.set('tab', 'agendas');
                                    window.location.href = url.toString();
                                }
                            });
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Failed to delete agenda', 'error'));
                }
            });
        });
    });
});
</script>
@endsection
