@extends('layouts.dss.app')

@section('title', ($isMyProposals ? 'My Extension Plans' : 'Extension Plans Masterlist') . ' — RIE System')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    {{-- Breadcrumb & Quick Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Extension Plans</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            @can('rie.tracking.view')
            <a href="{{ route('dss.rie.tracking.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold shadow-2xs d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-barcode-scan text-primary"></i>
                <span>Connect DTS Tracking</span>
            </a>
            @endcan
            @can('rie.plans.create')
            <a href="{{ route('dss.rie.plans.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-plus-circle-outline"></i>
                <span>New Extension Plan</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- Executive Hero Overview Card --}}
    <div class="card border rounded-3 p-4 mb-3 shadow-2xs bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-lightbulb-on-outline me-1"></i>RIE SYSTEM &bull; EXTENSION SERVICES
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        ISO 9001:2015 Operational Framework
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    {{ $isMyProposals ? 'My Extension Plans' : 'Extension Plans Masterlist' }}
                </h4>
                <p class="text-muted extra-small mb-3" style="max-width: 720px; line-height: 1.6;">
                    {{ $isMyProposals
                        ? 'Review and manage community extension operational plans you authored as Project Leader. Plans must achieve official Dean approval before advancing to 7-page Extension Proposals.'
                        : 'Institutional registry of extension project plans across all academic colleges, institutes, and campuses of Surigao del Norte State University.' }}
                </p>

                {{-- Segmented View Switcher (My Plans vs Masterlist) --}}
                <div class="d-inline-flex align-items-center gap-1 p-1 bg-light rounded-pill border shadow-2xs">
                    <a href="{{ route('dss.rie.plans.my') }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ $isMyProposals ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-folder-account-outline me-1"></i>My Extension Plans ({{ $myPlansCount }})
                    </a>
                    @can('viewAny', \App\Models\RIE\ExtensionPlan::class)
                    <a href="{{ route('dss.rie.plans.index') }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ !$isMyProposals ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-format-list-bulleted me-1"></i>Masterlist Oversight ({{ $totalMasterCount }})
                    </a>
                    @endcan
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 250px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">Gatekeeper Protocol</div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fs-4 fw-bold text-dark font-mono">{{ $approvedCount }}</span>
                        <span class="badge bg-success font-mono extra-small">Approved &bull; Executable</span>
                    </div>
                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                        @php
                            $execRate = $activePlansCount > 0 ? round(($approvedCount / $activePlansCount) * 100, 1) : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $execRate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono">
                        {{ $execRate }}% of registered plans fully endorsed
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-2xs mb-3 p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-check-circle fs-5 text-success"></i>
                <div class="extra-small fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 4 Executive Bento Metric Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">
                            {{ $isMyProposals ? 'My Authored Plans' : 'Total Registered Plans' }}
                        </div>
                        <div class="fs-4 fw-bold text-dark font-mono mb-0">{{ $activePlansCount }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-2 text-primary">
                        <i class="mdi mdi-folder-account-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Registry Scope</span>
                    <span class="badge bg-light text-dark border font-mono">{{ $isMyProposals ? 'Author Mode' : 'Campus-Wide' }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">In Routing &amp; Review</div>
                        <div class="fs-4 fw-bold text-info font-mono mb-0">{{ $submittedCount }}</div>
                    </div>
                    <div class="rounded-3 bg-info-subtle p-2 text-info">
                        <i class="mdi mdi-send-check fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Drafts Pending Submission</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle font-mono">{{ $draftCount }} Drafts</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Endorsed by Head</div>
                        <div class="fs-4 fw-bold text-warning font-mono mb-0">{{ $notedCount }}</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-2 text-warning">
                        <i class="mdi mdi-check-all fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Awaiting Dean Approval</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">Endorsed</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved by Dean</div>
                        <div class="fs-4 fw-bold text-success font-mono mb-0">{{ $approvedCount }}</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-2 text-success">
                        <i class="mdi mdi-check-decagram fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Ready for Proposal</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">Authorized</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Plans Masterlist Table Card --}}
    <div class="card border rounded-3 bg-white shadow-2xs mb-4 overflow-hidden">
        {{-- Unified Filter & Action Header --}}
        <div class="p-3 border-bottom bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-xl-5">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-format-list-bulleted text-primary"></i>
                        <span>{{ $isMyProposals ? 'My Authored Extension Plan Proposals' : 'University Extension Plan Registry' }}</span>
                    </h6>
                    <div class="extra-small text-muted">Indexed repository with signing matrix status &amp; DTS barcode tracking.</div>
                </div>

                <div class="col-12 col-xl-7">
                    <form id="plansFilterForm" class="d-flex align-items-center justify-content-xl-end gap-2 flex-wrap flex-md-nowrap" onsubmit="return false;">
                        {{-- Search Input with Input Group (No overlap) --}}
                        <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" style="min-width: 220px; max-width: 320px;">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 8px 0 0 8px;">
                                <i class="mdi mdi-magnify" style="font-size: 15px;"></i>
                            </span>
                            <input type="text" id="plansSearchInput" name="search" class="form-control form-control-sm border-start-0" placeholder="Search plan code, activity, program..." autocomplete="off" style="border-radius: 0 8px 8px 0; padding-left: 4px !important; font-size: 12px;">
                        </div>

                        {{-- Status Filter --}}
                        <select id="plansStatusFilter" name="status" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 12px; min-width: 160px; max-width: 200px; border-radius: 8px;">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft Plan</option>
                            <option value="submitted">Submitted (In Route)</option>
                            <option value="noted">Noted by Extension Head</option>
                            <option value="approved">Approved by Dean</option>
                        </select>

                        {{-- Reset Filter Button --}}
                        <button type="button" id="resetPlansFilterBtn" class="btn btn-sm btn-light border p-1 rounded-2 text-muted flex-shrink-0" title="Reset Filters" style="width: 31px; height: 31px;">
                            <i class="mdi mdi-refresh" style="font-size: 14px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="plansTable" style="width: 100%; font-size: 12px;">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3" style="width: 95px;">Actions</th>
                        <th style="width: 175px;">Plan Code</th>
                        <th>Extension Activity &amp; Program</th>
                        <th style="width: 180px;">Project Leader</th>
                        <th style="width: 155px;">Budget &amp; Target</th>
                        <th style="width: 160px;">DTS Tracking</th>
                        <th class="pe-3 text-end" style="width: 140px;">Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
                d.status = $('#plansStatusFilter').val();
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
            { data: 'status_badge', name: 'status', className: 'pe-3 text-end' }
        ],
        order: [[1, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="ms-2 font-mono extra-small">Loading extension plans...</span>',
            emptyTable: 'No Extension Plans found in this registry.',
            zeroRecords: 'No matching extension plans found.'
        },
        dom: "<'row'<'col-sm-12'tr>><'p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2'<'text-muted extra-small font-mono'i><'d-flex justify-content-end'p>>"
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
        }, 250);
    });

    $('#plansStatusFilter').on('change', function() {
        table.ajax.reload();
    });

    $('#resetPlansFilterBtn').on('click', function(e) {
        e.preventDefault();
        $('#plansSearchInput').val('');
        $('#plansStatusFilter').val('');
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
                                if (window.dssToast) {
                                    window.dssToast.success('Deleted', res.message || 'Plan deleted successfully.');
                                }
                                table.ajax.reload(null, false);
                            } else {
                                if (window.dssToast) {
                                    window.dssToast.error('Delete Failed', res.message || 'Unable to delete plan.');
                                }
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.message || 'Failed to delete plan.';
                            if (window.dssToast) {
                                window.dssToast.error('Error', msg);
                            }
                        }
                    });
                }
            });
        } else if (confirm(`Are you sure you want to delete draft Extension Plan "${planCode}"?`)) {
            $.ajax({
                url: `/dss/rie/plans/${planId}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.csrfToken },
                success: function(res) {
                    table.ajax.reload(null, false);
                }
            });
        }
    });
});
</script>
@endsection
