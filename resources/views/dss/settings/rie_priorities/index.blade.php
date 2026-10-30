@extends('layouts.dss.app')

@section('title', 'Extension Agendas & Banner Programs — SNSU DSS')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    .priority-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .priority-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -4px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .priority-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3.5px;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .priority-stat-card.accent-emerald::before { background-color: #059669; }
    .priority-stat-card.accent-blue::before { background-color: #2563eb; }
    .priority-stat-card.accent-purple::before { background-color: #7c3aed; }
    .priority-stat-card.accent-amber::before { background-color: #d97706; }

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
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Extension Agendas &amp; Banner Programs</h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 font-mono" style="font-size: 11px;">
                    <i class="mdi mdi-target-variant me-1"></i>RIE Institutional Priorities
                </span>
            </div>
            <p class="text-muted small mb-0">
                Institutional framework definitions for <strong>{{ $tenant->school_name ?? ($institution->school_name ?? 'SNSU') }}</strong>. These taxonomies govern proposal thematic alignment, SDG cross-referencing, and performance accreditation metrics.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-2 text-dark d-flex align-items-center gap-1.5">
                <i class="mdi mdi-file-document-outline text-muted"></i>
                <span>Proposals Directory</span>
            </a>
            <button type="button" class="btn btn-sm btn-success px-3.5 py-2 fw-semibold shadow-2xs d-flex align-items-center gap-1.5" id="btnAddNewRecord">
                <i class="mdi mdi-plus-circle fs-6"></i>
                <span id="btnAddNewRecordLabel">Add Banner Program</span>
            </button>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-emerald">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Banner Programs</div>
                        <div class="fs-3 fw-bold text-dark mb-0 font-mono">{{ $totalBannerPrograms }}</div>
                        <div class="extra-small text-success mt-1 fw-semibold">
                            <i class="mdi mdi-check-circle me-0.5"></i>{{ $activeBannerPrograms }} Active Flagships
                        </div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-3 text-success">
                        <i class="mdi mdi-flag-variant-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Extension Agendas</div>
                        <div class="fs-3 fw-bold text-dark mb-0 font-mono">{{ $totalAgendas }}</div>
                        <div class="extra-small text-primary mt-1 fw-semibold">
                            <i class="mdi mdi-check-circle me-0.5"></i>{{ $activeAgendas }} Active Thrusts
                        </div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-3 text-primary">
                        <i class="mdi mdi-compass-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Active Priorities</div>
                        <div class="fs-3 fw-bold text-dark mb-0 font-mono">{{ $activeBannerPrograms + $activeAgendas }}</div>
                        <div class="extra-small text-muted mt-1">Available in proposal forms</div>
                    </div>
                    <div class="rounded-3 p-3 text-purple" style="background: #f3e8ff; color: #7c3aed;">
                        <i class="mdi mdi-checkbox-marked-circle-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="priority-stat-card accent-amber">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Inactive / Archived</div>
                        <div class="fs-3 fw-bold text-dark mb-0 font-mono">{{ $inactiveBannerPrograms + $inactiveAgendas }}</div>
                        <div class="extra-small text-muted mt-1">Hidden from selection</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-3 text-warning">
                        <i class="mdi mdi-archive-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="priorityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'banner_programs' ? 'active' : '' }}" id="banner-programs-tab" data-bs-toggle="tab" data-bs-target="#banner-programs" type="button" role="tab" aria-controls="banner-programs" aria-selected="{{ $activeTab === 'banner_programs' ? 'true' : 'false' }}">
                        <i class="mdi mdi-flag-variant me-1.5"></i>
                        <span>Banner Programs</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $totalBannerPrograms }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'agendas' ? 'active' : '' }}" id="agendas-tab" data-bs-toggle="tab" data-bs-target="#agendas" type="button" role="tab" aria-controls="agendas" aria-selected="{{ $activeTab === 'agendas' ? 'true' : 'false' }}">
                        <i class="mdi mdi-compass me-1.5"></i>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Institutional Flagship Banner Programs</h6>
                            <p class="text-muted extra-small mb-0">High-impact university umbrella programs under which individual extension projects are grouped.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0" id="bannerProgramsTable">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    <th style="width: 70px;">Order</th>
                                    <th style="width: 130px;">Code</th>
                                    <th>Program Title &amp; Scope</th>
                                    <th>Description / Focal Area</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bannerPrograms as $program)
                                    <tr id="banner-row-{{ $program->id }}">
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
                                                {{ $program->description ? \Illuminate\Support\Str::limit($program->description, 90) : '—' }}
                                            </span>
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
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border py-1 px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 font-sm">
                                                    <li>
                                                        <a class="dropdown-item py-1.5 edit-banner-btn" href="javascript:void(0)"
                                                           data-id="{{ $program->id }}"
                                                           data-code="{{ $program->code }}"
                                                           data-name="{{ $program->name }}"
                                                           data-description="{{ $program->description }}"
                                                           data-sort="{{ $program->sort_order }}"
                                                           data-active="{{ $program->is_active ? 1 : 0 }}">
                                                            <i class="mdi mdi-pencil-outline me-2 text-primary"></i>Edit Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1.5 toggle-banner-btn" href="javascript:void(0)"
                                                           data-id="{{ $program->id }}"
                                                           data-name="{{ $program->name }}"
                                                           data-active="{{ $program->is_active ? 1 : 0 }}">
                                                            <i class="mdi {{ $program->is_active ? 'mdi-pause text-warning' : 'mdi-play text-success' }} me-2"></i>
                                                            {{ $program->is_active ? 'Set Inactive' : 'Activate' }}
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <a class="dropdown-item py-1.5 text-danger delete-banner-btn" href="javascript:void(0)"
                                                           data-id="{{ $program->id }}"
                                                           data-name="{{ $program->name }}">
                                                            <i class="mdi mdi-trash-can-outline me-2"></i>Delete Program
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Institutional Extension Agendas &amp; Thrusts</h6>
                            <p class="text-muted extra-small mb-0">Strategic research &amp; extension priorities aligned with regional development goals and CHED mandates.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0" id="agendasTable">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    <th style="width: 70px;">Order</th>
                                    <th style="width: 130px;">Code</th>
                                    <th>Agenda / Thrust Title</th>
                                    <th>Strategic Objective / Scope</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendas as $agenda)
                                    <tr id="agenda-row-{{ $agenda->id }}">
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
                                                {{ $agenda->description ? \Illuminate\Support\Str::limit($agenda->description, 90) : '—' }}
                                            </span>
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
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border py-1 px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 font-sm">
                                                    <li>
                                                        <a class="dropdown-item py-1.5 edit-agenda-btn" href="javascript:void(0)"
                                                           data-id="{{ $agenda->id }}"
                                                           data-code="{{ $agenda->code }}"
                                                           data-name="{{ $agenda->name }}"
                                                           data-description="{{ $agenda->description }}"
                                                           data-sort="{{ $agenda->sort_order }}"
                                                           data-active="{{ $agenda->is_active ? 1 : 0 }}">
                                                            <i class="mdi mdi-pencil-outline me-2 text-primary"></i>Edit Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1.5 toggle-agenda-btn" href="javascript:void(0)"
                                                           data-id="{{ $agenda->id }}"
                                                           data-name="{{ $agenda->name }}"
                                                           data-active="{{ $agenda->is_active ? 1 : 0 }}">
                                                            <i class="mdi {{ $agenda->is_active ? 'mdi-pause text-warning' : 'mdi-play text-success' }} me-2"></i>
                                                            {{ $agenda->is_active ? 'Set Inactive' : 'Activate' }}
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <a class="dropdown-item py-1.5 text-danger delete-agenda-btn" href="javascript:void(0)"
                                                           data-id="{{ $agenda->id }}"
                                                           data-name="{{ $agenda->name }}">
                                                            <i class="mdi mdi-trash-can-outline me-2"></i>Delete Agenda
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
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
                        <i class="mdi mdi-flag-variant text-success me-1.5"></i>Add Flagship Banner Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono" placeholder="e.g. BP-ICT, LIKHA, KABALIKAT" required>
                        <div class="form-text extra-small">Short identifier or acronym used in proposals and tracking.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Name / Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Information Technology Empowerment for Communities" required>
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
                    <button type="submit" class="btn btn-sm btn-success px-3.5 fw-semibold shadow-2xs">
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
                        <i class="mdi mdi-pencil text-primary me-1.5"></i>Edit Banner Program
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
                    <button type="submit" class="btn btn-sm btn-primary px-3.5 fw-semibold shadow-2xs">
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
                        <i class="mdi mdi-compass text-info me-1.5"></i>Add Extension Agenda / Thrust
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agenda Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-mono" placeholder="e.g. AG-01, ENVI, FOOD-SEC" required>
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
                    <button type="submit" class="btn btn-sm btn-info text-white px-3.5 fw-semibold shadow-2xs">
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
                        <i class="mdi mdi-pencil text-primary me-1.5"></i>Edit Extension Agenda
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
                    <button type="submit" class="btn btn-sm btn-primary px-3.5 fw-semibold shadow-2xs">
                        <i class="mdi mdi-check me-1"></i>Update Extension Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Track active tab and update "Add New" button
    const btnAddNew = document.getElementById('btnAddNewRecord');
    const btnLabel = document.getElementById('btnAddNewRecordLabel');

    function updateAddButton() {
        const activeTabEl = document.querySelector('#priorityTabs .nav-link.active');
        if (activeTabEl && activeTabEl.id === 'agendas-tab') {
            btnLabel.textContent = 'Add Extension Agenda';
            btnAddNew.onclick = function () {
                new bootstrap.Modal(document.getElementById('addAgendaModal')).show();
            };
        } else {
            btnLabel.textContent = 'Add Banner Program';
            btnAddNew.onclick = function () {
                new bootstrap.Modal(document.getElementById('addBannerProgramModal')).show();
            };
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

            Swal.fire({
                title: 'Delete Banner Program?',
                text: `Are you sure you want to remove '${name}'? Existing proposals with this banner will keep their text records.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
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
                            }).then(() => location.reload());
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

            Swal.fire({
                title: 'Delete Extension Agenda?',
                text: `Are you sure you want to remove '${name}'?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
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
                                const url = new URL(window.location.href);
                                url.searchParams.set('tab', 'agendas');
                                window.location.href = url.toString();
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
