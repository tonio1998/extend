@extends('layouts.dss.app')

@section('title', $leaderName . ' — Extension Projects & Transactions | SNSU RIE System')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
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

    {{-- Breadcrumb & Quick Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.unit_monitoring.index') }}" class="text-decoration-none text-muted">Unit Monitoring &amp; Impact</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $leaderName }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.rie.unit_monitoring.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Back to Monitoring</span>
            </a>
            <a href="{{ route('dss.documents.repository', ['project_leader' => $leaderName]) }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-shield-folder-outline text-primary"></i>
                <span>Leader Documents Vault</span>
            </a>
            <a href="{{ route('dss.rie.calendar.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-calendar-range text-success"></i>
                <span>Extension Calendar</span>
            </a>
        </div>
    </div>

    {{-- Leader Profile Hero Card --}}
    <div class="card border rounded-3 p-4 mb-3 shadow-2xs bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-account-star me-1"></i>PROJECT LEADER &bull; EXTENSION PROPONENT
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        {{ $unitAffiliation }}
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    {{ $leaderName }}
                </h3>
                <p class="text-muted extra-small mb-3" style="max-width: 780px; line-height: 1.6;">
                    Comprehensive portfolio for Project Leader <strong>{{ $leaderName }}</strong> showing authored community extension proposals, operational plans, repository files, and 3-year impact survey timelines.
                </p>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi mdi-folder-multiple-outline text-primary me-1"></i>{{ $proposalsWithImpact->count() }} Proposals Authored
                    </span>
                    <span class="badge bg-light text-dark border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi mdi-clipboard-text-outline text-warning me-1"></i>{{ $plans->count() }} Operational Plans
                    </span>
                    <span class="badge bg-light text-dark border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi mdi-file-document-outline text-success me-1"></i>{{ $documents->count() }} Repository Files
                    </span>
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 270px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">Cumulative Budget Managed</div>
                    <div class="fs-3 fw-bold text-dark font-mono mb-2">
                        ₱{{ number_format($totalBudget, 2) }}
                    </div>
                    <div class="extra-small text-muted font-mono">
                        Across {{ $proposalsWithImpact->count() }} approved extension proposal(s)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Tabs Container: Proposals, Plans, Repository Documents --}}
    <div class="card border rounded-3 bg-white shadow-2xs mb-4">
        <div class="px-3 pt-2 border-bottom bg-light bg-opacity-25">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="leaderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="lproposals-tab" data-bs-toggle="tab" data-bs-target="#lproposals-pane" type="button" role="tab">
                        <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                        <span>Extension Proposals ({{ $proposalsWithImpact->count() }})</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lplans-tab" data-bs-toggle="tab" data-bs-target="#lplans-pane" type="button" role="tab">
                        <i class="mdi mdi-clipboard-text-outline me-1"></i>
                        <span>Operational Plans ({{ $plans->count() }})</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ldocs-tab" data-bs-toggle="tab" data-bs-target="#ldocs-pane" type="button" role="tab">
                        <i class="mdi mdi-file-document-multiple-outline me-1"></i>
                        <span>Repository Evidence Vault ({{ $documents->count() }})</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="leaderTabsContent">

                {{-- TAB 1: Proposals --}}
                <div class="tab-pane fade show active" id="lproposals-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="leaderProposalsTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 100px;" class="text-center">Action</th>
                                    <th style="min-width: 240px;">Proposal Title &amp; Code</th>
                                    <th style="width: 180px;">Academic Unit</th>
                                    <th style="width: 130px;">Commencement</th>
                                    <th style="width: 140px;">Approved Budget</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proposalsWithImpact as $item)
                                    @php $prop = $item['proposal']; @endphp
                                    <tr>
                                        <td class="text-center">
                                            <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View Proposal">
                                                <i class="mdi mdi-eye-outline text-primary"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $prop->title }}
                                            </a>
                                            <div class="extra-small font-mono text-muted mt-0.5"><span class="badge bg-dark text-white">{{ $prop->proposal_code }}</span></div>
                                        </td>
                                        <td>
                                            <div class="text-dark extra-small fw-semibold">{{ $prop->unit?->unit_name ?: 'Academic Unit' }}</div>
                                            <div class="text-muted extra-small font-mono">{{ $prop->campus?->campus_name ?: 'Main Campus' }}</div>
                                        </td>
                                        <td class="font-mono extra-small">
                                            {{ $item['commencement_date'] ? $item['commencement_date']->format('M d, Y') : 'TBD' }}
                                        </td>
                                        <td class="font-mono fw-bold text-dark">
                                            ₱{{ number_format((float) $prop->approved_budget, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono text-uppercase extra-small">
                                                {{ $prop->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted extra-small">
                                            No proposals found for this leader.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: Plans --}}
                <div class="tab-pane fade" id="lplans-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="leaderPlansTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 100px;" class="text-center">Action</th>
                                    <th style="width: 140px;">Plan Code</th>
                                    <th style="min-width: 220px;">Program Name</th>
                                    <th style="width: 110px;" class="text-center">Activities</th>
                                    <th style="width: 120px;" class="text-center">Year / FY</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $p)
                                    <tr>
                                        <td class="text-center">
                                            <a href="{{ route('dss.rie.plans.show', $p->getRouteKey()) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View Extension Plan">
                                                <i class="mdi mdi-eye-outline text-primary"></i>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-dark text-white font-mono">{{ $p->plan_code }}</span></td>
                                        <td>
                                            <a href="{{ route('dss.rie.plans.show', $p->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $p->program_name }}
                                            </a>
                                        </td>
                                        <td class="text-center"><span class="badge bg-light text-dark border font-mono">{{ $p->activities->count() }}</span></td>
                                        <td class="text-center font-mono extra-small">FY {{ $p->fiscal_year ?: date('Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono text-uppercase extra-small">{{ $p->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted extra-small">
                                            No plans prepared by this leader.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: Documents --}}
                <div class="tab-pane fade" id="ldocs-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="leaderDocsTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 100px;" class="text-center">Action</th>
                                    <th style="min-width: 240px;">Document Title &amp; Filename</th>
                                    <th style="width: 140px;">Category</th>
                                    <th style="width: 110px;">Size</th>
                                    <th style="width: 120px;">Date Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $doc)
                                    <tr>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('dss.documents.upload.preview', $doc->id) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View in Viewer">
                                                    <i class="mdi mdi-eye-outline text-primary"></i>
                                                </a>
                                                <a href="{{ route('dss.documents.upload.download', $doc->id) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="Download">
                                                    <i class="mdi mdi-download text-success"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('dss.documents.upload.preview', $doc->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 320px;">
                                                {{ $doc->title }}
                                            </a>
                                            <div class="extra-small text-muted font-mono text-truncate">{{ $doc->original_filename }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono extra-small">{{ str_replace('_', ' ', $doc->category) }}</span>
                                        </td>
                                        <td><span class="font-mono extra-small text-muted">{{ $doc->formatted_file_size }}</span></td>
                                        <td><span class="font-mono extra-small text-muted">{{ $doc->created_at ? $doc->created_at->format('M d, Y') : '—' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted extra-small">
                                            No repository documents found for this leader.
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

@push('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/responsive.bootstrap5.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#leaderProposalsTable').DataTable({ responsive: true, pageLength: 10 });
        $('#leaderPlansTable').DataTable({ responsive: true, pageLength: 10 });
        $('#leaderDocsTable').DataTable({ responsive: true, pageLength: 10 });
    });
</script>
@endpush
@endsection
