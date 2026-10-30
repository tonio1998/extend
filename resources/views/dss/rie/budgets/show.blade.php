@extends('layouts.dss.app')

@section('title', "{$budget->budget_code} — Extension Budget Monitoring Workbench")

@section('styles')
<style>
    .workbench-hero {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-top: 4px solid #059669;
        border-radius: 18px;
    }
    .metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .metric-card.border-emerald::before { background: #059669; }
    .metric-card.border-indigo::before { background: #4f46e5; }
    .metric-card.border-amber::before { background: #d97706; }
    .metric-card.border-cyan::before { background: #0284c7; }

    .nav-tabs-custom .nav-link {
        font-weight: 600;
        font-size: 13px;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 18px;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #0f172a;
        border-bottom-color: #cbd5e1;
    }
    .nav-tabs-custom .nav-link.active {
        color: #059669;
        border-bottom-color: #059669;
        background: transparent;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.budgets.index', ['fy' => $budget->fiscal_year]) }}" class="text-decoration-none text-muted">Annual Budgets</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $budget->budget_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1.5" onclick="openRealignModal()">
                <i class="mdi mdi-swap-horizontal"></i>
                <span>Realign / Augment Funds</span>
            </button>
            <a href="{{ route('dss.rie.budgets.edit', $budget->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1.5">
                <i class="mdi mdi-pencil-outline"></i>
                <span>Edit Parameters</span>
            </a>
            <a href="{{ route('dss.rie.budgets.index', ['fy' => $budget->fiscal_year]) }}" class="btn btn-sm btn-light border">
                <i class="mdi mdi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show extra-small" role="alert">
            <i class="mdi mdi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="workbench-hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1" style="font-size: 11px;">
                        FY {{ $budget->fiscal_year }} Appropriation
                    </span>
                    <span class="badge bg-light text-dark border font-mono px-2 py-1" style="font-size: 11px;">
                        {{ $budget->fund_cluster }}
                    </span>
                    <span class="badge rounded-pill {{ $budget->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2.5 py-1 font-mono" style="font-size: 10px;">
                        {{ strtoupper($budget->status) }}
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">
                    {{ $budget->title }}
                </h3>
                <div class="d-flex align-items-center gap-3 text-muted extra-small flex-wrap">
                    <span><i class="mdi mdi-barcode font-mono text-success me-1"></i><strong>{{ $budget->budget_code }}</strong></span>
                    <span><i class="mdi mdi-school-outline me-1"></i>{{ $budget->campus?->campus_name ?? 'University-Wide (Main Campus)' }}</span>
                    @if($budget->board_resolution_no)
                        <span><i class="mdi mdi-file-certificate-outline me-1"></i>{{ $budget->board_resolution_no }}</span>
                    @endif
                    @if($budget->approved_by)
                        <span><i class="mdi mdi-account-check me-1"></i>{{ $budget->approved_by }}</span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                @php $health = $budget->health_status; @endphp
                <div class="p-3 bg-light rounded-4 border text-start d-inline-block shadow-2xs" style="min-width: 260px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="extra-small text-uppercase fw-bold text-muted font-mono">Burn Rate / Obligated</span>
                        <span class="badge {{ $health['badge'] }} font-mono extra-small">{{ $health['label'] }}</span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="fs-2 fw-bold text-dark font-mono">{{ $budget->burn_rate }}%</span>
                        <span class="extra-small text-muted font-mono">({{ $budget->commitment_rate }}% committed)</span>
                    </div>
                    <div class="progress mb-2" style="height: 7px;">
                        <div class="progress-bar {{ $health['progress_class'] }}" role="progressbar" style="width: {{ min(100, $budget->burn_rate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono d-flex justify-content-between">
                        <span>₱{{ number_format($budget->utilized_amount, 2) }}</span>
                        <span>of ₱{{ number_format($budget->effective_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card border-emerald">
                <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Appropriation</div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">₱{{ number_format($budget->effective_total, 2) }}</div>
                <div class="extra-small text-muted mt-2 pt-2 border-top">
                    <div>GAA: <strong class="text-dark font-mono">₱{{ number_format($budget->allocated_gaa, 2) }}</strong></div>
                    <div>STF: <strong class="text-dark font-mono">₱{{ number_format($budget->allocated_stf, 2) }}</strong></div>
                    @if($budget->realigned_amount != 0)
                        <div class="{{ $budget->realigned_amount > 0 ? 'text-success' : 'text-danger' }}">
                            Realign: <strong>{{ $budget->realigned_amount > 0 ? '+' : '' }}₱{{ number_format($budget->realigned_amount, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card border-indigo">
                <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Utilized</div>
                <div class="fs-4 fw-bold text-primary font-mono mb-1">₱{{ number_format($budget->utilized_amount, 2) }}</div>
                <div class="extra-small text-muted mt-2 pt-2 border-top d-flex justify-content-between">
                    <span>Active Obligations</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                        {{ $approvedProposals->count() }} Approved Proposals
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card border-amber">
                <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">In-Review Committed</div>
                <div class="fs-4 fw-bold text-warning font-mono mb-1">₱{{ number_format($budget->committed_amount, 2) }}</div>
                <div class="extra-small text-muted mt-2 pt-2 border-top d-flex justify-content-between">
                    <span>Pending Endorsement</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">
                        {{ $inReviewProposals->count() }} In-Review
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card border-cyan">
                <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Remaining Liquid Balance</div>
                @php $rem = $budget->remaining_balance; @endphp
                <div class="fs-4 fw-bold font-mono mb-1 {{ $rem < 0 ? 'text-danger' : 'text-success' }}">
                    ₱{{ number_format($rem, 2) }}
                </div>
                <div class="extra-small text-muted mt-2 pt-2 border-top d-flex justify-content-between">
                    <span>Projected Balance</span>
                    <span class="font-mono fw-bold {{ $budget->projected_balance < 0 ? 'text-danger' : 'text-dark' }}">
                        ₱{{ number_format($budget->projected_balance, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="dss-card p-0 mb-4 overflow-hidden">
        <ul class="nav nav-tabs nav-tabs-custom px-4 pt-2 border-bottom bg-light" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabProposals" type="button" role="tab">
                    <i class="mdi mdi-file-document-multiple-outline me-1"></i>Charged Extension Proposals ({{ $proposals->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCategories" type="button" role="tab">
                    <i class="mdi mdi-format-list-checks me-1"></i>Line-Item Categories ({{ $budget->allocations->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabBanner" type="button" role="tab">
                    <i class="mdi mdi-flag-checkered me-1"></i>Banner Program Breakdown
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAudit" type="button" role="tab">
                    <i class="mdi mdi-history me-1"></i>Augmentations &amp; Adjustments ({{ $budget->transactions->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content p-4">
            <div class="tab-pane fade show active" id="tabProposals" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Community Extension Projects Charging Against FY {{ $budget->fiscal_year }}</h6>
                        <div class="extra-small text-muted">Real-time proposals deducting from this budget allocation upon presidential approval.</div>
                    </div>
                    <a href="{{ route('dss.rie.proposals.create') }}" class="btn btn-xs btn-dss-primary">
                        <i class="mdi mdi-plus me-1"></i>Submit New Proposal
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Proposal Code &amp; Title</th>
                                <th>Project Leader</th>
                                <th>Campus / Dept</th>
                                <th>Allocated Cash (₱)</th>
                                <th>Obligation Impact</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $prop)
                                @php
                                    $isApproved = in_array(strtolower((string) $prop->status), ['approved'], true);
                                    $isInReview = in_array(strtolower((string) $prop->status), ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'in-review', 'in_review'], true);
                                    $badge = $prop->status_badge;
                                @endphp
                                <tr>
                                    <td class="ps-3" style="max-width: 300px;">
                                        <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="fw-bold font-mono text-dss-primary text-decoration-none">
                                            {{ $prop->proposal_code }}
                                        </a>
                                        <div class="fw-semibold text-dark text-truncate mt-0.5" title="{{ $prop->title }}">{{ $prop->title }}</div>
                                        <div class="extra-small text-muted">{{ $prop->banner_program ?: 'General Community Extension' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $prop->project_leader_name ?: ($prop->projectLeaderUser?->name ?: 'N/A') }}</div>
                                        <div class="extra-small text-muted">{{ $prop->project_leader_title ?: 'Proponent' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-mono">
                                            {{ $prop->campus?->campus_name ?? 'Main' }}
                                        </span>
                                    </td>
                                    <td class="font-mono fw-bold text-dark">
                                        ₱{{ number_format((float) ($prop->budget_cash_amount ?? 0), 2) }}
                                    </td>
                                    <td>
                                        @if($isApproved)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                                                <i class="mdi mdi-check-circle me-1"></i>Utilized / Deducted
                                            </span>
                                        @elseif($isInReview)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">
                                                <i class="mdi mdi-clock-outline me-1"></i>Committed Reserve
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border font-mono">
                                                Pipeline Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $badge['class'] ?? 'bg-secondary' }} rounded-pill px-2 py-0.5" style="font-size: 10px;">
                                            {{ $badge['label'] ?? $prop->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="btn btn-xs btn-outline-secondary">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No extension proposals currently charging against FY {{ $budget->fiscal_year }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabCategories" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Sub-Allocations by Category</h6>
                        <div class="extra-small text-muted">Planned spending limits across activity types.</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Expense Category</th>
                                <th>Campus</th>
                                <th>Allocated Amount</th>
                                <th>Share of Budget</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($budget->allocations as $alloc)
                                @php
                                    $pct = $budget->effective_total > 0 ? round(($alloc->allocated_amount / $budget->effective_total) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $alloc->category_name }}</td>
                                    <td>{{ $alloc->campus?->campus_name ?? 'Consolidated' }}</td>
                                    <td class="font-mono fw-bold text-dark">₱{{ number_format($alloc->allocated_amount, 2) }}</td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $pct }}%;"></div>
                                            </div>
                                            <span class="font-mono extra-small">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-muted extra-small">{{ $alloc->remarks ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No specific category sub-allocations configured.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tabBanner" role="tabpanel">
                <h6 class="fw-bold text-dark mb-1">Fund Utilization Across Extension Banner Programs</h6>
                <div class="extra-small text-muted mb-3">Total funds committed and approved per flagship institutional program.</div>

                <div class="row g-3">
                    @forelse($bannerBreakdown as $b)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="fw-bold text-dark text-truncate mb-1">{{ $b['name'] }}</div>
                                <div class="fs-4 fw-bold text-success font-mono mb-1">₱{{ number_format($b['total_amount'], 2) }}</div>
                                <div class="extra-small text-muted">{{ $b['count'] }} Approved project proposals</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            No approved projects categorized by banner programs yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="tabAudit" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Appropriation History &amp; Realignments</h6>
                        <div class="extra-small text-muted">Audit trail of fund initializations, augmentations, and reappropriations.</div>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-warning" onclick="openRealignModal()">
                        <i class="mdi mdi-plus me-1"></i>New Realignment
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Transaction Type</th>
                                <th>Amount (₱)</th>
                                <th>Reference / BOR Res</th>
                                <th>Justification / Reason</th>
                                <th>Authorized By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($budget->transactions as $trx)
                                <tr>
                                    <td class="ps-3 font-mono extra-small text-muted">{{ $trx->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge {{ $trx->amount >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} font-mono extra-small">
                                            {{ strtoupper($trx->transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="font-mono fw-bold {{ $trx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $trx->amount >= 0 ? '+' : '' }}₱{{ number_format($trx->amount, 2) }}
                                    </td>
                                    <td class="font-mono extra-small">{{ $trx->reference_no ?: '—' }}</td>
                                    <td class="extra-small text-dark">{{ $trx->reason }}</td>
                                    <td class="extra-small">{{ $trx->authorized_by ?: ($trx->createdByUser?->name ?? 'System Admin') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No adjustment transactions recorded yet.
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

<div class="modal fade" id="realignModal" tabindex="-1" aria-labelledby="realignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form method="POST" action="{{ route('dss.rie.budgets.realign', $budget->getRouteKey()) }}">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <div>
                        <h6 class="modal-title fw-bold text-dark" id="realignModalLabel">
                            <i class="mdi mdi-swap-horizontal text-warning me-1"></i>Budget Augmentation / Realignment
                        </h6>
                        <div class="extra-small text-muted font-mono">{{ $budget->budget_code }} (FY {{ $budget->fiscal_year }})</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="extra-small text-muted font-mono text-uppercase">Current Effective Total</div>
                        <div class="fs-4 fw-bold text-dark font-mono">₱{{ number_format($budget->effective_total, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold extra-small text-uppercase">Transaction Type <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select form-select-sm" required>
                            <option value="augmentation">Augmentation (Additional Funds +)</option>
                            <option value="realignment">Realignment (Reappropriation ~)</option>
                            <option value="deduction">Deduction / Deficit Reduction (-)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold extra-small text-uppercase">Amount (PHP ₱) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-mono" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold extra-small text-uppercase">Reference Document / BOR Resolution</label>
                        <input type="text" name="reference_no" class="form-control form-control-sm" placeholder="e.g. BOR Res. No. 18-2026 / Office Order">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold extra-small text-uppercase">Reason &amp; Legal Basis <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="State justification for fund realignment or augmentation..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold extra-small text-uppercase">Authorized Official</label>
                        <input type="text" name="authorized_by" class="form-control form-control-sm" placeholder="e.g. VP for RIE / University President">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dss-primary px-3">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Apply Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openRealignModal() {
        var modal = new bootstrap.Modal(document.getElementById('realignModal'));
        modal.show();
    }
</script>
@endsection
