@extends('layouts.dss.app')

@section('title', ($isMyProposals ? 'My Extension Proposals' : 'Extension Proposals Masterlist') . ' — RIE System')

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
                    {{ $isMyProposals ? 'My Extension Proposals' : 'Extension Proposals Masterlist' }}
                </h3>
                <p class="text-secondary small mb-0">
                    Comprehensive university project and activity proposals based on official approved Extension Plans (ISO 9001 Format).
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @can('rie.proposals.create')
                    @if($approvedPlansReadyCount > 0)
                        <button type="button" class="btn btn-dss-primary rounded-pill px-4 py-2 shadow-xs d-flex align-items-center gap-1 fw-bold" style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#selectApprovedPlanModal">
                            <i class="mdi mdi-file-document-plus-outline"></i>
                            <span>New Extension Proposal</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1 font-mono">{{ $approvedPlansReadyCount }} Ready</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 shadow-xs d-flex align-items-center gap-1 fw-semibold opacity-75" style="font-size: 13px;" title="Proposal creation requires an approved Extension Plan." onclick="alert('Notice: To create an Extension Proposal, you must first have an officially APPROVED Extension Plan. Currently, all approved plans already have proposals, or none are approved yet.');">
                            <i class="mdi mdi-lock-outline"></i>
                            <span>New Extension Proposal</span>
                        </button>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link btn-sm rounded-pill px-3 py-1 extra-small fw-bold {{ $isMyProposals ? 'active' : 'bg-light text-secondary border' }}"
                   href="{{ route('dss.rie.proposals.my') }}">
                    <i class="mdi mdi-folder-account-outline me-1"></i>
                    My Proposals
                    @if(isset($myProposalsCount))
                        <span class="badge rounded-pill {{ $isMyProposals ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 font-mono">{{ $myProposalsCount }}</span>
                    @endif
                </a>
            </li>
            @canany(['rie.proposals.manage', 'rie.proposals.review', 'rie.proposals.evaluate'])
            <li class="nav-item">
                <a class="nav-link btn-sm rounded-pill px-3 py-1 extra-small fw-bold {{ !$isMyProposals ? 'active' : 'bg-light text-secondary border' }}"
                   href="{{ route('dss.rie.proposals.index') }}">
                    <i class="mdi mdi-format-list-bulleted me-1"></i>
                    Masterlist (RIE Oversight)
                    @if(isset($totalMasterCount))
                        <span class="badge rounded-pill {{ !$isMyProposals ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 font-mono">{{ $totalMasterCount }}</span>
                    @endif
                </a>
            </li>
            @endcanany
        </ul>
        <div class="extra-small text-muted font-mono d-none d-md-block">
            <i class="mdi mdi-shield-check-outline text-success"></i> Strict Gatekeeping: Only Approved Plans Can Proceed
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="canva-card p-3 border shadow-2xs h-100 d-flex align-items-center gap-3">
                <div class="canva-avatar canva-avatar-primary">
                    <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                </div>
                <div>
                    <div class="extra-small text-muted text-uppercase fw-semibold">Total Proposals</div>
                    <div class="fs-4 fw-extrabold text-dark font-mono">{{ $totalMasterCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="canva-card p-3 border shadow-2xs h-100 d-flex align-items-center gap-3">
                <div class="canva-avatar canva-avatar-warning">
                    <i class="mdi mdi-progress-clock fs-4"></i>
                </div>
                <div>
                    <div class="extra-small text-muted text-uppercase fw-semibold">In Routing Review</div>
                    <div class="fs-4 fw-extrabold text-dark font-mono">{{ $inRouteCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="canva-card p-3 border shadow-2xs h-100 d-flex align-items-center gap-3">
                <div class="canva-avatar canva-avatar-success">
                    <i class="mdi mdi-check-decagram fs-4"></i>
                </div>
                <div>
                    <div class="extra-small text-muted text-uppercase fw-semibold">Approved Proposals</div>
                    <div class="fs-4 fw-extrabold text-dark font-mono">{{ $approvedCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="canva-card p-3 border shadow-2xs h-100 d-flex align-items-center gap-3" style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
                <div class="canva-avatar text-success bg-white border border-success-subtle">
                    <i class="mdi mdi-clipboard-check-outline fs-4"></i>
                </div>
                <div>
                    <div class="extra-small text-success-emphasis text-uppercase fw-bold">Approved Plans Ready</div>
                    <div class="fs-4 fw-extrabold text-success font-mono">{{ $approvedPlansReadyCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposals Masterlist Table --}}
    <div class="canva-card p-0 border shadow-2xs overflow-hidden">
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3 bg-light bg-opacity-40">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold text-dark m-0 fs-6">Registered Proposals</h5>
                <span class="badge bg-light text-secondary border font-mono extra-small">Live Synchronized</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="sdgFilter" class="form-select form-select-sm rounded-pill extra-small border-secondary-subtle" style="width: 220px;">
                    <option value="all" {{ ($selectedSdg ?? 'all') === 'all' ? 'selected' : '' }}>All UN SDGs (1–17)</option>
                    @foreach($allSdgs ?? [] as $sdgItem)
                        <option value="{{ $sdgItem->goal_number }}" {{ (string) ($selectedSdg ?? '') === (string) $sdgItem->goal_number ? 'selected' : '' }}>
                            SDG {{ $sdgItem->goal_number }}: {{ $sdgItem->name }}
                        </option>
                    @endforeach
                </select>
                <select id="statusFilter" class="form-select form-select-sm rounded-pill extra-small border-secondary-subtle" style="width: 170px;">
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
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="proposalsTable" style="width:100%; font-size: 13px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th style="width: 130px;">Proposal Code</th>
                            <th style="width: 140px;">Parent Plan</th>
                            <th>Program / Activity Title</th>
                            <th style="width: 170px;">Project Leader</th>
                            <th style="width: 130px;">Budget</th>
                            <th style="width: 180px;">Status</th>
                            <th class="text-end pe-3" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Select Approved Plan to Proceed to Proposal --}}
<div class="modal fade" id="selectApprovedPlanModal" tabindex="-1" aria-labelledby="selectApprovedPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-inline-flex">
                        <i class="mdi mdi-clipboard-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6" id="selectApprovedPlanModalLabel">Select Approved Extension Plan</h5>
                        <p class="text-muted extra-small m-0">Only officially Approved Extension Plans are authorized to proceed to Proposal stage.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($approvedPlansList->isEmpty())
                    <div class="text-center py-4 text-muted extra-small">
                        <i class="mdi mdi-alert-circle-outline fs-2 text-warning d-block mb-1"></i>
                        No approved extension plans found ready for proposal creation.
                    </div>
                @else
                    <p class="small text-secondary mb-3">
                        Select an approved plan below to automatically import its activity details, objectives, budget, and leader into the official 7-page proposal template:
                    </p>
                    <div class="list-group">
                        @foreach($approvedPlansList as $readyPlan)
                            <div class="list-group-item list-group-item-action p-3 rounded-3 mb-2 border d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div style="max-width: 520px;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                            <i class="mdi mdi-check-decagram me-1"></i> APPROVED PLAN
                                        </span>
                                        <strong class="font-mono text-dark">{{ $readyPlan->plan_code }}</strong>
                                        <span class="text-muted extra-small">&bull; {{ $readyPlan->program_name }}</span>
                                    </div>
                                    <div class="fw-bold text-dark mb-1">{{ $readyPlan->extension_activity }}</div>
                                    <div class="extra-small text-muted">
                                        <span>Leader: <strong>{{ $readyPlan->prepared_by_name }}</strong></span>
                                        <span class="ms-2">Beneficiaries: {{ Str::limit($readyPlan->beneficiaries, 40) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('dss.rie.proposals.create', ['plan_id' => $readyPlan->getRouteKey()]) }}" class="btn btn-sm btn-dss-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1">
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

@push('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function () {
    const table = $('#proposalsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dss.rie.proposals.data', ['my_proposals' => $isMyProposals ? 1 : 0]) }}",
            data: function (d) {
                d.status = $('#statusFilter').val();
                d.sdg = $('#sdgFilter').val();
            }
        },
        columns: [
            { data: 'code_link', name: 'proposal_code' },
            { data: 'plan_reference', name: 'extensionPlan.plan_code' },
            { data: 'title_display', name: 'title' },
            { data: 'leader_display', name: 'project_leader_name' },
            { data: 'budget_display', name: 'budget_cash_amount' },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        language: {
            emptyTable: "No extension proposals registered yet. Start by selecting an approved Extension Plan.",
            search: "_INPUT_",
            searchPlaceholder: "Search proposals, codes, or leaders..."
        }
    });

    $('#statusFilter, #sdgFilter').on('change', function () {
        table.draw();
    });
});
</script>
@endpush
