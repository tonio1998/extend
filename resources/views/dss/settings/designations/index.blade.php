@extends('layouts.dss.app')

@section('title', 'Master Designations & Positions — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    .desig-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .desig-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -4px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .desig-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3.5px;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .desig-stat-card.accent-primary::before { background-color: #2563eb; }
    .desig-stat-card.accent-success::before { background-color: #10b981; }
    .desig-stat-card.accent-info::before { background-color: #06b6d4; }
    .desig-stat-card.accent-warning::before { background-color: #f59e0b; }
    .desig-stat-card.accent-secondary::before { background-color: #64748b; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Master Designations &amp; Institutional Positions</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-mono" style="font-size: 11px;">
                    <i class="mdi mdi-briefcase-outline me-1"></i>Position Repository
                </span>
            </div>
            <p class="text-muted small mb-0">
                Central repository of standardized institutional designations for <strong>{{ $tenant->school_name ?? $tenant->name }}</strong>. Multiple employees can hold each designation concurrently or primarily (e.g. <em>Programmer</em>, <em>Faculty</em>).
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.employees.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-2 text-dark d-flex align-items-center gap-1.5">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Employee Directory</span>
            </a>
            <button type="button" class="btn btn-sm btn-success px-3.5 py-2 fw-semibold shadow-2xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addDesignationModal">
                <i class="mdi mdi-plus-circle fs-6"></i>
                <span>Add New Designation</span>
            </button>
        </div>
    </div>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl">
            <div class="desig-stat-card accent-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Total Positions</div>
                        <div class="fs-3 fw-bold text-dark mb-0 font-mono">{{ $totalDesignations }}</div>
                        <div class="extra-small text-muted mt-1">Master defined positions</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-3 text-primary">
                        <i class="mdi mdi-briefcase-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="desig-stat-card accent-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Active Positions</div>
                        <div class="fs-3 fw-bold text-success mb-0 font-mono">{{ $activeDesignations }}</div>
                        <div class="extra-small text-muted mt-1">Available for assignment</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-3 text-success">
                        <i class="mdi mdi-check-circle-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="desig-stat-card accent-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Technical</div>
                        <div class="fs-3 fw-bold text-info mb-0 font-mono">{{ $technicalCount }}</div>
                        <div class="extra-small text-muted mt-1">IT, systems, technical</div>
                    </div>
                    <div class="rounded-3 bg-info-subtle p-3 text-info">
                        <i class="mdi mdi-code-tags fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="desig-stat-card accent-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Academic</div>
                        <div class="fs-3 fw-bold text-warning-emphasis mb-0 font-mono">{{ $academicCount }}</div>
                        <div class="extra-small text-muted mt-1">Teaching, faculty, research</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-3 text-warning">
                        <i class="mdi mdi-school-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="desig-stat-card accent-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Administrative</div>
                        <div class="fs-3 fw-bold text-secondary mb-0 font-mono">{{ $adminCount }}</div>
                        <div class="extra-small text-muted mt-1">Offices, support, aides</div>
                    </div>
                    <div class="rounded-3 bg-secondary-subtle p-3 text-secondary">
                        <i class="mdi mdi-office-building-cog-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="dss-card p-3 mb-4">
        <form method="GET" action="{{ route('dss.settings.designations.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search designation title, code, description..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="category" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Filter</button>
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('dss.settings.designations.index') }}" class="btn btn-sm btn-light border" title="Reset Filters"><i class="mdi mdi-refresh"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Master Designations Table Card -->
    <div class="dss-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutional Designations Directory</h6>
                <div class="extra-small text-muted">Selectable in Employee profile creation &amp; concurrent appointments via Select2.</div>
            </div>
            <span class="badge bg-light text-dark border font-mono px-2 py-1">
                {{ $designations->count() }} position{{ $designations->count() === 1 ? '' : 's' }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="designationsTable" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Actions</th>
                        <th>Position / Title</th>
                        <th>Category</th>
                        <th>Description / Scope</th>
                        <th style="width: 180px;">Active Personnel Holders</th>
                        <th style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($designations as $d)
                        @php
                            $holders = $holderCounts[$d->id] ?? 0;
                            $catBadge = match($d->category) {
                                'Academic' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                'Technical' => 'bg-info-subtle text-info border-info-subtle',
                                'Executive' => 'bg-danger-subtle text-danger border-danger-subtle',
                                default => 'bg-primary-subtle text-primary border-primary-subtle',
                            };
                        @endphp
                        <tr id="desig-row-{{ $d->id }}">
                            <!-- Actions -->
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <button class="btn btn-sm btn-white border shadow-2xs text-dark px-2 py-1" onclick="openEditDesignationModal({{ $d->id }}, '{{ addslashes($d->name) }}', '{{ $d->code }}', '{{ $d->category }}', '{{ addslashes($d->description ?? '') }}', '{{ $d->status }}')" title="Edit Designation">
                                        <i class="mdi mdi-pencil-outline text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-white border shadow-2xs text-danger px-2 py-1" onclick="confirmDeleteDesignation({{ $d->id }}, '{{ addslashes($d->name) }}', {{ $holders }})" title="Archive Designation">
                                        <i class="mdi mdi-archive-arrow-down-outline"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Title & Code -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-dark fw-bold" style="width: 34px; height: 34px; min-width: 34px; font-size: 12px;">
                                        <i class="mdi mdi-briefcase-outline text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $d->name }}</div>
                                        @if($d->code)
                                            <span class="badge bg-light text-dark border font-mono extra-small" style="font-size: 10px;">{{ $d->code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge {{ $catBadge }} border px-2 py-1" style="font-size: 11px;">
                                    {{ $d->category }}
                                </span>
                            </td>

                            <!-- Description -->
                            <td>
                                <div class="text-muted extra-small" style="max-width: 350px;">
                                    {{ $d->description ?: 'No specific description provided.' }}
                                </div>
                            </td>

                            <!-- Active Personnel Holders -->
                            <td>
                                @if($holders > 0)
                                    <a href="{{ route('dss.settings.employees.index', ['search' => $d->name]) }}" class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 text-decoration-none fw-bold d-inline-flex align-items-center gap-1" title="View all employees holding {{ $d->name }}">
                                        <i class="mdi mdi-account-group"></i>
                                        <span>{{ $holders }} Personnel</span>
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-0.5 extra-small">
                                        0 Assigned
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="badge {{ $d->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2 py-0.5" style="font-size: 10px;">
                                    {{ ucfirst($d->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: ADD NEW DESIGNATION
========================================================= -->
<div class="modal fade" id="addDesignationModal" tabindex="-1" aria-labelledby="addDesignationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3" style="background:#f0fdf4; border-left:4px solid #16a34a;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="addDesignationModalLabel">
                    <i class="mdi mdi-plus-circle"></i>
                    <span>Register New Institutional Designation</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('dss.settings.designations.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Designation Title / Position Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Programmer, Faculty, Project Coordinator">
                        <div class="form-text extra-small">This title can be selected across multiple employee records.</div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Position Code</label>
                            <input type="text" name="code" class="form-control font-mono" placeholder="e.g. PROG, FAC, PC">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required data-no-select2="true">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $cat === 'Administrative' ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Scope / Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of duties or institutional mandate..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Status</label>
                        <select name="status" class="form-select form-select-sm" data-no-select2="true">
                            <option value="active">Active (Available for employee assignment)</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">
                        <i class="mdi mdi-check-circle me-1"></i>Save Designation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: EDIT DESIGNATION
========================================================= -->
<div class="modal fade" id="editDesignationModal" tabindex="-1" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3" style="background:#f8fafc; border-left:4px solid #1e293b;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editDesignationModalLabel">
                    <i class="mdi mdi-pencil-outline"></i>
                    <span>Edit Designation Record</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editDesignationForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Designation Title / Position Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editDesigName" class="form-control" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Position Code</label>
                            <input type="text" name="code" id="editDesigCode" class="form-control font-mono">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Category <span class="text-danger">*</span></label>
                            <select name="category" id="editDesigCategory" class="form-select" required data-no-select2="true">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Scope / Description</label>
                        <textarea name="description" id="editDesigDescription" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Status <span class="text-danger">*</span></label>
                        <select name="status" id="editDesigStatus" class="form-select form-select-sm" required data-no-select2="true">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dark px-4 fw-bold">
                        <i class="mdi mdi-content-save me-1"></i>Save Changes
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
    let designationsDataTable = null;

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#designationsTable')) {
            designationsDataTable = $('#designationsTable').DataTable({
                pageLength: 20,
                responsive: true,
                order: [[1, 'asc']],
                language: {
                    search: "Quick Search:",
                    searchPlaceholder: "Search positions...",
                    lengthMenu: "Show _MENU_ positions",
                    info: "Showing _START_ to _END_ of _TOTAL_ positions",
                    emptyTable: "No designations registered yet."
                }
            });
        }
    });

    function openEditDesignationModal(id, name, code, category, desc, status) {
        document.getElementById('editDesigName').value = name;
        document.getElementById('editDesigCode').value = code || '';
        document.getElementById('editDesigCategory').value = category || 'Administrative';
        document.getElementById('editDesigDescription').value = desc || '';
        document.getElementById('editDesigStatus').value = status || 'active';

        document.getElementById('editDesignationForm').action = '/dss/settings/designations/' + id;
        const modal = new bootstrap.Modal(document.getElementById('editDesignationModal'));
        modal.show();
    }

    function confirmDeleteDesignation(id, name, holders) {
        let warningText = `Are you sure you want to archive the designation <strong>${name}</strong>?`;
        if (holders > 0) {
            warningText = `<strong>Warning:</strong> ${holders} personnel currently hold the designation <strong>${name}</strong>. Archiving this designation may affect their active title.`;
        }

        Swal.fire({
            title: 'Archive Designation?',
            html: warningText,
            icon: holders > 0 ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/dss/settings/designations/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ force_delete: holders > 0 ? 1 : 0 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Archived',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error deleting designation.' });
                });
            }
        });
    }
</script>
@endsection
