@extends('layouts.dss.app')

@section('title', ($isMyProposals ? 'My Extension Proposals' : 'Extension Proposals Masterlist') . ' — RIE System')

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
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Extension Proposals</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            @can('rie.proposals.create')
                @if($approvedPlansReadyCount > 0)
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#selectApprovedPlanModal">
                        <i class="mdi mdi-plus-circle-outline"></i>
                        <span>New Extension Proposal</span>
                        <span class="badge bg-white text-dark rounded-pill ms-1 font-mono">{{ $approvedPlansReadyCount }} Ready</span>
                    </button>
                @else
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 py-1 extra-small fw-semibold opacity-75 shadow-2xs d-inline-flex align-items-center gap-1" title="Proposal creation requires an approved Extension Plan." onclick="alert('Notice: To create an Extension Proposal, you must first have an officially APPROVED Extension Plan. Currently, all approved plans already have proposals, or none are approved yet.');">
                        <i class="mdi mdi-lock-outline"></i>
                        <span>New Extension Proposal</span>
                    </button>
                @endif
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
                        ISO 9001:2015 Approved Formats
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    {{ $isMyProposals ? 'My Extension Proposals' : 'Extension Proposals Masterlist' }}
                </h4>
                <p class="text-muted extra-small mb-3" style="max-width: 720px; line-height: 1.6;">
                    Comprehensive university project and activity proposals based on official approved Extension Plans. Features automated UN SDG alignment, line-item budgeting, activity design, and multi-tier institutional endorsements.
                </p>

                {{-- Segmented View Switcher (My Proposals vs Masterlist) --}}
                <div class="d-inline-flex align-items-center gap-1 p-1 bg-light rounded-pill border shadow-2xs">
                    <a href="{{ route('dss.rie.proposals.my') }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ $isMyProposals ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-folder-account-outline me-1"></i>My Proposals ({{ $myProposalsCount }})
                    </a>
                    @canany(['rie.proposals.manage', 'rie.proposals.review', 'rie.proposals.evaluate'])
                    <a href="{{ route('dss.rie.proposals.index') }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ !$isMyProposals ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-format-list-bulleted me-1"></i>Masterlist Oversight ({{ $totalMasterCount }})
                    </a>
                    @endcanany
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 250px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">Gatekeeper Pipeline</div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fs-4 fw-bold text-dark font-mono">{{ $approvedPlansReadyCount }}</span>
                        <span class="badge {{ $approvedPlansReadyCount > 0 ? 'bg-success' : 'bg-secondary' }} font-mono extra-small">
                            {{ $approvedPlansReadyCount > 0 ? 'Approved Plans Ready' : 'Pipeline Cleared' }}
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                        @php
                            $propCompletion = $totalMasterCount > 0 ? round(($approvedCount / $totalMasterCount) * 100, 1) : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $propCompletion) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono">
                        {{ $propCompletion }}% official President approved
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
                            {{ $isMyProposals ? 'My Authored Proposals' : 'Total Proposals' }}
                        </div>
                        <div class="fs-4 fw-bold text-dark font-mono mb-0">{{ $isMyProposals ? $myProposalsCount : $totalMasterCount }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-2 text-primary">
                        <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Registry Scope</span>
                    <span class="badge bg-light text-dark border font-mono">{{ $isMyProposals ? 'Proponent Mode' : 'University-Wide' }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">In Routing Review</div>
                        <div class="fs-4 fw-bold text-warning font-mono mb-0">{{ $inRouteCount }}</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-2 text-warning">
                        <i class="mdi mdi-progress-clock fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Drafts Pending</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">{{ $draftCount }} Drafts</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Proposals</div>
                        <div class="fs-4 fw-bold text-success font-mono mb-0">{{ $approvedCount }}</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-2 text-success">
                        <i class="mdi mdi-check-decagram fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Full Executive Concurrence</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">Approved</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Plans Ready</div>
                        <div class="fs-4 fw-bold font-mono mb-0 {{ $approvedPlansReadyCount > 0 ? 'text-success' : 'text-muted' }}">
                            {{ $approvedPlansReadyCount }}
                        </div>
                    </div>
                    <div class="rounded-3 {{ $approvedPlansReadyCount > 0 ? 'bg-success-subtle text-success' : 'bg-light text-secondary' }} p-2">
                        <i class="mdi mdi-clipboard-check-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Gatekeeper Pipeline</span>
                    <span class="badge {{ $approvedPlansReadyCount > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }} font-mono">
                        {{ $approvedPlansReadyCount > 0 ? 'Ready to Author' : 'Zero Backlog' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposals Masterlist Table Card --}}
    <div class="card border rounded-3 bg-white shadow-2xs mb-4 overflow-hidden">
        {{-- Unified Filter & Action Header --}}
        <div class="p-3 border-bottom bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-xl-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-file-document-multiple-outline text-primary"></i>
                        <span>{{ $isMyProposals ? 'My Registered Extension Proposals' : 'University Extension Proposals Registry' }}</span>
                    </h6>
                    <div class="extra-small text-muted">Synchronized proposals repository with UN SDG alignments &amp; multi-tier status.</div>
                </div>

                <div class="col-12 col-xl-8">
                    <div class="d-flex align-items-center justify-content-xl-end gap-2 flex-wrap flex-md-nowrap">
                        {{-- Quick Search with Input Group (No overlap) --}}
                        <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" style="min-width: 200px; max-width: 280px;">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 8px 0 0 8px;">
                                <i class="mdi mdi-magnify" style="font-size: 15px;"></i>
                            </span>
                            <input type="text" id="proposalsSearchInput" class="form-control form-control-sm border-start-0" placeholder="Search proposal, leader, program..." autocomplete="off" style="border-radius: 0 8px 8px 0; padding-left: 4px !important; font-size: 12px;">
                        </div>

                        {{-- UN SDG Filter --}}
                        <select id="sdgFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 12px; min-width: 170px; max-width: 220px; border-radius: 8px;">
                            <option value="all" {{ ($selectedSdg ?? 'all') === 'all' ? 'selected' : '' }}>All UN SDGs (1–17)</option>
                            @foreach($allSdgs ?? [] as $sdgItem)
                                <option value="{{ $sdgItem->goal_number }}" {{ (string) ($selectedSdg ?? '') === (string) $sdgItem->goal_number ? 'selected' : '' }}>
                                    SDG {{ $sdgItem->goal_number }}: {{ $sdgItem->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Status Filter --}}
                        <select id="statusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 12px; min-width: 140px; max-width: 170px; border-radius: 8px;">
                            <option value="all">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed (Head)</option>
                            <option value="noted">Noted (Dean)</option>
                            <option value="concurred">Concurred (Director)</option>
                            <option value="recommended">Recommended (VP RIE)</option>
                            <option value="approved">Approved (President)</option>
                            <option value="returned">Returned</option>
                        </select>

                        {{-- Reset Filter Button --}}
                        <button type="button" id="resetProposalsFilterBtn" class="btn btn-sm btn-light border p-1 rounded-2 text-muted flex-shrink-0" title="Reset Filters" style="width: 31px; height: 31px;">
                            <i class="mdi mdi-refresh" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="proposalsTable" style="width: 100%; font-size: 12px;">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3" style="width: 95px;">Actions</th>
                        <th style="width: 145px;">Proposal Code</th>
                        <th style="width: 140px;">Parent Plan</th>
                        <th>Program / Activity Title</th>
                        <th style="width: 170px;">Project Leader</th>
                        <th style="width: 130px;">Budget</th>
                        <th class="pe-3 text-end" style="width: 170px;">Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: Select Approved Plan to Proceed to Proposal --}}
<div class="modal fade" id="selectApprovedPlanModal" tabindex="-1" aria-labelledby="selectApprovedPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-success-subtle text-success p-2">
                        <i class="mdi mdi-clipboard-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="selectApprovedPlanModalLabel">Select Approved Extension Plan</h6>
                        <div class="text-muted extra-small">Only officially Approved Extension Plans are authorized to proceed to Proposal stage.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($approvedPlansList->isEmpty())
                    <div class="text-center py-5 text-muted extra-small">
                        <i class="mdi mdi-alert-circle-outline fs-2 text-warning d-block mb-2"></i>
                        <div class="fw-semibold">No approved extension plans found ready for proposal creation.</div>
                        <div class="mt-1">Plans must be fully approved by the Dean before advancing to proposal stage.</div>
                    </div>
                @else
                    <p class="small text-secondary mb-3">
                        Select an approved plan below to automatically import its activity details, objectives, budget, and leader into the official 7-page proposal template:
                    </p>
                    <div class="list-group gap-2">
                        @foreach($approvedPlansList as $readyPlan)
                            <div class="list-group-item list-group-item-action p-3 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div style="max-width: 520px;">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                            <i class="mdi mdi-check-decagram me-1"></i> APPROVED PLAN
                                        </span>
                                        <strong class="font-mono text-dark">{{ $readyPlan->plan_code }}</strong>
                                        <span class="text-muted extra-small">&bull; {{ $readyPlan->program_name }}</span>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">{{ $readyPlan->extension_activity }}</div>
                                    <div class="extra-small text-muted font-mono">
                                        <span>Leader: <strong>{{ $readyPlan->prepared_by_name }}</strong></span>
                                        <span class="ms-2">&bull; Beneficiaries: {{ Str::limit($readyPlan->beneficiaries, 40) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('dss.rie.proposals.create', ['plan_id' => $readyPlan->getRouteKey()]) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold d-inline-flex align-items-center gap-1 shadow-2xs">
                                        <span>Proceed with Proposal</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function () {
    const table = $('#proposalsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        ajax: {
            url: "{{ route('dss.rie.proposals.data', ['my_proposals' => $isMyProposals ? 1 : 0]) }}",
            data: function (d) {
                d.status = $('#statusFilter').val();
                d.sdg = $('#sdgFilter').val();
                d.search = { value: $('#proposalsSearchInput').val() };
            }
        },
        columns: [
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'ps-3' },
            { data: 'code_link', name: 'proposal_code' },
            { data: 'plan_reference', name: 'extensionPlan.plan_code' },
            { data: 'title_display', name: 'title' },
            { data: 'leader_display', name: 'project_leader_name' },
            { data: 'budget_display', name: 'budget_cash_amount' },
            { data: 'status_badge', name: 'status', className: 'pe-3 text-end' }
        ],
        order: [[1, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="ms-2 font-mono extra-small">Loading extension proposals...</span>',
            emptyTable: 'No extension proposals registered yet. Start by selecting an approved Extension Plan.',
            zeroRecords: 'No matching extension proposals found.'
        },
        dom: "<'row'<'col-sm-12'tr>><'p-3 border-top bg-light d-flex justify-content-between align-items-center flex-wrap gap-2'<'text-muted extra-small font-mono'i><'d-flex justify-content-end'p>>"
    });

    let searchDebounce;
    $('#proposalsSearchInput').on('keyup', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            table.draw();
        }, 250);
    });

    $('#statusFilter, #sdgFilter').on('change', function () {
        table.draw();
    });

    $('#resetProposalsFilterBtn').on('click', function () {
        $('#proposalsSearchInput').val('');
        $('#statusFilter').val('all');
        $('#sdgFilter').val('all');
        table.draw();
    });
});
</script>
@endsection
