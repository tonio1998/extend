@extends('layouts.dss.app')

@section('title', ($isMyProposals ? 'My Extension Plans' : 'Extension Plans Masterlist') . ' — RIE System')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="canva-hero-header p-4 mb-4 shadow-2xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="canva-theme-badge">
                        <i class="mdi mdi-lightbulb-on-outline"></i> RIE SYSTEM &bull; EXTENSION SERVICES
                    </span>
                    <span class="text-secondary extra-small">Office of Research, Innovation &amp; Extension</span>
                </div>
                <h3 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.02em;">
                    {{ $isMyProposals ? 'My Extension Plans' : 'Extension Plans Masterlist' }}
                </h3>
                <p class="text-secondary small mb-0">
                    {{ $isMyProposals ? 'Review and track university extension project proposals and plans you authored as Project Leader.' : 'Directory of university extension project plans across all academic programs & campuses (RIE In-Charge Oversight).' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @can('rie.tracking.view')
                <a href="{{ route('dss.rie.tracking.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 shadow-xs d-flex align-items-center gap-1 fw-bold" style="font-size: 13px;">
                    <i class="mdi mdi-barcode-scan text-primary"></i>
                    <span>Connect DTS Tracking</span>
                </a>
                @endcan
                @can('rie.plans.create')
                <a href="{{ route('dss.rie.plans.create') }}" class="btn btn-dss-primary rounded-pill px-4 py-2 shadow-xs d-flex align-items-center gap-1 fw-bold" style="font-size: 13px;">
                    <i class="mdi mdi-plus-circle"></i>
                    <span>New Extension Plan</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link btn-sm rounded-pill px-3 py-1 extra-small fw-bold {{ $isMyProposals ? 'active' : 'bg-light text-secondary border' }}"
                   href="{{ route('dss.rie.plans.my') }}">
                    <i class="mdi mdi-folder-account-outline me-1"></i>
                    My Extension Plans
                    @if(isset($myPlansCount))
                        <span class="badge rounded-pill {{ $isMyProposals ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 font-mono">{{ $myPlansCount }}</span>
                    @endif
                </a>
            </li>
            @can('viewAny', \App\Models\RIE\ExtensionPlan::class)
            <li class="nav-item">
                <a class="nav-link btn-sm rounded-pill px-3 py-1 extra-small fw-bold {{ !$isMyProposals ? 'active' : 'bg-light text-secondary border' }}"
                   href="{{ route('dss.rie.plans.index') }}">
                    <i class="mdi mdi-format-list-bulleted me-1"></i>
                    Masterlist (RIE Oversight)
                    @if(isset($totalMasterCount))
                        <span class="badge rounded-pill {{ !$isMyProposals ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 font-mono">{{ $totalMasterCount }}</span>
                    @endif
                </a>
            </li>
            @endcan
        </ul>
        <div class="extra-small text-muted font-mono d-none d-md-block">
            <i class="mdi mdi-shield-check-outline text-success me-1"></i>
            {{ $isMyProposals ? 'Author Mode: Proponent' : 'Oversight Mode: RIE Directorate' }}
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

    <!-- 4 KPI Metric Bento Cards -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: Total Plans -->
        <div class="col-xl-3 col-md-6">
            <div class="canva-card p-3 h-100 border-accent-purple">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">{{ $isMyProposals ? 'My Proposals' : 'Total Plans' }}</span>
                    <span class="rounded-circle p-1 bg-primary bg-opacity-10 text-primary d-inline-flex">
                        <i class="mdi mdi-folder-account-outline fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ $activePlansCount ?? 0 }}</div>
                <div class="extra-small text-muted">{{ $isMyProposals ? 'Authored Extension Proposals' : 'Institutional Registry' }}</div>
            </div>
        </div>

        <!-- Metric 2: Submitted / In Routing -->
        <div class="col-xl-3 col-md-6">
            <div class="canva-card p-3 h-100 border-accent-cyan">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Submitted</span>
                    <span class="rounded-circle p-1 bg-info bg-opacity-10 text-info d-inline-flex">
                        <i class="mdi mdi-send-check fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ $submittedCount ?? 0 }}</div>
                <div class="extra-small text-muted">In Routing &amp; Verification</div>
            </div>
        </div>

        <!-- Metric 3: Noted by Extension Head -->
        <div class="col-xl-3 col-md-6">
            <div class="canva-card p-3 h-100 border-accent-amber">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Endorsed</span>
                    <span class="rounded-circle p-1 bg-warning bg-opacity-10 text-warning d-inline-flex">
                        <i class="mdi mdi-check fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ $notedCount ?? 0 }}</div>
                <div class="extra-small text-muted">Noted by Extension Head</div>
            </div>
        </div>

        <!-- Metric 4: Approved by Dean -->
        <div class="col-xl-3 col-md-6">
            <div class="canva-card p-3 h-100 border-accent-green">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Approved</span>
                    <span class="rounded-circle p-1 bg-success bg-opacity-10 text-success d-inline-flex">
                        <i class="mdi mdi-check-decagram fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ $approvedCount ?? 0 }}</div>
                <div class="extra-small text-muted">Approved &amp; Ready for Execution</div>
            </div>
        </div>
    </div>

    <!-- Filters Strip -->
    <div class="dss-card p-3 mb-4">
        <form id="plansFilterForm" class="row g-2 align-items-center" onsubmit="return false;">
            <div class="col-md-5 col-lg-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="plansSearchInput" name="search" class="form-control form-control-sm border-start-0"
                           placeholder="Search plan code, activity title, program, proponent...">
                </div>
            </div>

            <div class="col-md-4 col-lg-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft Plan</option>
                    <option value="submitted">Submitted (In Route)</option>
                    <option value="noted">Noted by Extension Head</option>
                    <option value="approved">Approved by Dean</option>
                </select>
            </div>

            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dss-primary w-100 fw-bold">
                    Filter
                </button>
                <button type="button" id="resetPlansFilterBtn" class="btn btn-sm btn-light border text-muted px-2" title="Reset Filters">
                    <i class="mdi mdi-refresh fs-6"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Extension Plans Table Card -->
    <div class="dss-card p-0 shadow-sm mb-4" style="overflow: visible;">
        <div class="p-3 border-bottom bg-light bg-opacity-50 d-flex justify-content-between align-items-center flex-wrap gap-2 rounded-top-3">
            <div>
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-format-list-bulleted text-primary"></i>
                    <span>{{ $isMyProposals ? 'My Authored Extension Plan Proposals' : 'University Extension Plan Registry' }}</span>
                </h6>
                <div class="extra-small text-muted">Real-time indexed repository with official signing matrix &amp; DTS integration</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white border text-dark font-mono"><i class="mdi mdi-check-circle text-success me-1"></i>Verified RIE Archive</span>
            </div>
        </div>

        <div class="table-responsive" style="overflow: visible;">
            <table class="table table-hover align-middle mb-0" id="plansTable" style="width: 100%;">
                <thead class="bg-light text-muted extra-small text-uppercase fw-bold font-mono">
                    <tr>
                        <th class="ps-3" style="width: 95px;">Actions</th>
                        <th style="width: 175px;">Plan Code</th>
                        <th>Extension Activity &amp; Program</th>
                        <th style="width: 180px;">Proponent (Project Leader)</th>
                        <th style="width: 155px;">Budget &amp; Target</th>
                        <th style="width: 160px;">DTS Tracking</th>
                        <th class="pe-3" style="width: 140px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    const table = $('#plansTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        ajax: {
            url: "{{ route('dss.rie.plans.data', ['my_plans' => $isMyProposals ? 1 : 0]) }}",
            data: function (d) {
                d.status = $('select[name="status"]').val();
                d.search = $('#plansSearchInput').val();
            }
        },
        columns: [
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'ps-3' },
            { data: 'plan_code', name: 'plan_code' },
            { data: 'activity_program', name: 'extension_activity' },
            { data: 'proponent', name: 'prepared_by_name' },
            { data: 'budget_target', name: 'target_beneficiaries_count' },
            { data: 'dts_tracking', name: 'dts_record_id', orderable: false },
            { data: 'status_badge', name: 'status', className: 'pe-3' }
        ],
        order: [[1, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="ms-2">Loading extension plans...</span>',
            emptyTable: 'No Extension Plans found.',
            zeroRecords: 'No matching extension plans found.'
        },
        dom: "<'row'<'col-sm-12'tr>><'row align-items-center p-3'<'col-sm-6 text-muted extra-small'i><'col-sm-6 d-flex justify-content-end'p>>"
    });

    $('#plansFilterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    let searchDebounce;
    $('#plansSearchInput').on('keyup', function() {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function() {
            table.ajax.reload();
        }, 300);
    });

    $('select[name="status"]').on('change', function() {
        table.ajax.reload();
    });

    $('#resetPlansFilterBtn').on('click', function(e) {
        e.preventDefault();
        $('#plansSearchInput').val('');
        $('select[name="status"]').val('');
        table.ajax.reload();
    });

    $(document).on('click', '.delete-plan-btn', function(e) {
        e.preventDefault();
        const planId = $(this).data('id');
        const planCode = $(this).data('code');

        if (typeof window.customConfirm === 'function') {
            window.customConfirm({
                title: 'Delete Draft Plan?',
                message: `Are you sure you want to permanently delete draft Extension Plan "${planCode}"?`,
                isDanger: true,
                confirmText: 'Yes, Delete Plan',
                onConfirm: function() {
                    $.ajax({
                        url: `/dss/rie/plans/${planId}`,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': window.csrfToken },
                        success: function(res) {
                            if (res.success) {
                                window.dssToast.success('Deleted', res.message || 'Plan deleted successfully.');
                                table.ajax.reload(null, false);
                            } else {
                                window.dssToast.error('Delete Failed', res.message || 'Unable to delete plan.');
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.message || 'Failed to delete plan.';
                            window.dssToast.error('Error', msg);
                        }
                    });
                }
            });
        }
    });
});
</script>
@endsection
