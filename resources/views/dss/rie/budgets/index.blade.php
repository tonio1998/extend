@extends('layouts.dss.app')

@section('title', 'Annual Extension Budget & Fund Utilization — SNSU RIE')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Annual Extension Budgets</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.budgets.export', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-2xs">
                <i class="mdi mdi-download-outline"></i>
                <span>Export Report (CSV)</span>
            </a>
            <a href="{{ route('dss.rie.budgets.create', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-dss-primary d-flex align-items-center gap-1 shadow-2xs">
                <i class="mdi mdi-plus-circle-outline"></i>
                <span>Add Annual Budget</span>
            </a>
        </div>
    </div>

    <div class="budget-hero-card p-4 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1" style="font-size: 11px;">
                        <i class="mdi mdi-finance me-1"></i>Financial Planning &amp; Monitoring
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1" style="font-size: 11px;">
                        Institutional Fund Governance
                    </span>
                </div>
                <h2 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">
                    Extension Annual Budget Monitoring — FY {{ $selectedYear }}
                </h2>
                <p class="text-muted mb-3" style="font-size: 14px; max-width: 720px; line-height: 1.6;">
                    Track institutional appropriations, campus fund shares, approved obligations, and real-time remaining liquidity for community extension programs across Surigao del Norte State University.
                </p>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="extra-small font-mono fw-bold text-muted text-uppercase me-1">Fiscal Year:</span>
                    @foreach($availableYears as $year)
                        <a href="{{ route('dss.rie.budgets.index', array_merge(request()->query(), ['fy' => $year])) }}"
                           class="fy-pill-btn {{ $selectedYear == $year ? 'active' : '' }}">
                            FY {{ $year }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-4 border text-start d-inline-block shadow-2xs" style="min-width: 250px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">FY {{ $selectedYear }} Overall Burn Rate</div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="fs-2 fw-bold text-dark font-mono">{{ $macroBurnRate }}%</span>
                        <span class="badge {{ $macroBurnRate > 90 ? 'bg-danger' : ($macroBurnRate > 70 ? 'bg-warning text-dark' : 'bg-success') }} font-mono extra-small">
                            {{ $macroBurnRate > 90 ? 'Depleting' : ($macroBurnRate > 70 ? 'Active' : 'Liquid') }}
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar {{ $macroBurnRate > 90 ? 'bg-danger' : ($macroBurnRate > 70 ? 'bg-warning' : 'bg-success') }}"
                             role="progressbar" style="width: {{ min(100, $macroBurnRate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted">
                        Total Commitment: <strong>{{ $macroCommitmentRate }}%</strong> (with in-review proposals)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="budget-bento-card border-emerald">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Annual Budget</div>
                        <div class="fs-4 fw-bold text-dark font-mono mb-0">₱{{ number_format($totalApprovedBudget, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-2.5 text-success">
                        <i class="mdi mdi-bank-check fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                    <span>Effective Total</span>
                    <span class="badge bg-light text-dark border font-mono">{{ $budgets->count() }} Registries</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="budget-bento-card border-indigo">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Utilized</div>
                        <div class="fs-4 fw-bold text-primary font-mono mb-0">₱{{ number_format($totalUtilized, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-2.5 text-primary">
                        <i class="mdi mdi-check-decagram-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                    <span>Active Obligations</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">{{ $macroBurnRate }}% utilized</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="budget-bento-card border-amber">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">In-Review Committed</div>
                        <div class="fs-4 fw-bold text-warning font-mono mb-0">₱{{ number_format($totalCommitted, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-2.5 text-warning">
                        <i class="mdi mdi-clock-time-four-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                    <span>Pending Endorsement</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">In Review</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="budget-bento-card border-cyan">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Available Remaining</div>
                        <div class="fs-4 fw-bold font-mono mb-0 {{ $totalRemainingBalance < 0 ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format($totalRemainingBalance, 2) }}
                        </div>
                    </div>
                    <div class="rounded-3 bg-info-subtle p-2.5 text-info">
                        <i class="mdi mdi-wallet-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                    <span>Projected Liquidity</span>
                    <span class="fw-semibold font-mono {{ $totalProjectedBalance < 0 ? 'text-danger' : 'text-dark' }}">
                        ₱{{ number_format($totalProjectedBalance, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="dss-card p-0 mb-4 overflow-hidden">
        <div class="p-3 px-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Extension Fiscal Year Budget Allocations</h6>
                <div class="extra-small text-muted">Breakdown of approved appropriations per campus jurisdiction and fund cluster.</div>
            </div>

            <form method="GET" action="{{ route('dss.rie.budgets.index') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="fy" value="{{ $selectedYear }}">

                <select name="campus_id" class="form-select form-select-sm" style="font-size: 12px; width: 180px;" onchange="this.form.submit()">
                    <option value="">All Campuses</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ $selectedCampusId == $campus->id ? 'selected' : '' }}>
                            {{ $campus->campus_name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-sm" style="font-size: 12px; width: 130px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ $selectedStatus === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                @if($selectedCampusId || $selectedStatus)
                    <a href="{{ route('dss.rie.budgets.index', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Budget Code &amp; Title</th>
                        <th>Campus</th>
                        <th>Fund Cluster</th>
                        <th>Approved Allocation</th>
                        <th>Utilized / Obligated</th>
                        <th>Remaining Balance</th>
                        <th>Burn Rate</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            $health = $budget->health_status;
                            $rem = $budget->remaining_balance;
                        @endphp
                        <tr>
                            <td class="ps-4" style="max-width: 280px;">
                                <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="fw-bold font-mono text-dss-primary text-decoration-none">
                                    {{ $budget->budget_code }}
                                </a>
                                <div class="fw-semibold text-dark text-truncate mt-0.5" title="{{ $budget->title }}">{{ $budget->title }}</div>
                                <div class="extra-small text-muted mt-0.5">
                                    Approved: {{ $budget->board_resolution_no ?: ($budget->approved_by ?: 'Institutional Resolution') }}
                                </div>
                            </td>
                            <td>
                                @if($budget->campus)
                                    <span class="badge bg-light text-dark border font-mono">
                                        <i class="mdi mdi-school-outline me-1"></i>{{ $budget->campus->campus_name }}
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                                        University-Wide (Main)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="extra-small font-mono text-dark fw-medium">{{ $budget->fund_cluster }}</div>
                                <div class="extra-small text-muted">
                                    GAA: ₱{{ number_format($budget->allocated_gaa, 2) }} • STF: ₱{{ number_format($budget->allocated_stf, 2) }}
                                </div>
                            </td>
                            <td class="font-mono fw-bold text-dark">
                                ₱{{ number_format($budget->effective_total, 2) }}
                                @if($budget->realigned_amount != 0)
                                    <div class="extra-small {{ $budget->realigned_amount > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $budget->realigned_amount > 0 ? '+' : '' }}₱{{ number_format($budget->realigned_amount, 2) }} realigned
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-mono fw-semibold text-primary">₱{{ number_format($budget->utilized_amount, 2) }}</div>
                                <div class="extra-small text-muted mt-0.5">
                                    ₱{{ number_format($budget->committed_amount, 2) }} in-review
                                </div>
                            </td>
                            <td>
                                <div class="font-mono fw-bold {{ $rem < 0 ? 'text-danger' : 'text-success' }}">
                                    ₱{{ number_format($rem, 2) }}
                                </div>
                                <div class="extra-small text-muted">
                                    Proj: ₱{{ number_format($budget->projected_balance, 2) }}
                                </div>
                            </td>
                            <td style="min-width: 140px;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="font-mono extra-small fw-bold">{{ $budget->burn_rate }}%</span>
                                    <span class="extra-small text-muted font-mono">{{ $budget->commitment_rate }}% total</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $health['progress_class'] }}" role="progressbar" style="width: {{ min(100, $budget->burn_rate) }}%;"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $budget->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }} px-2 py-0.5 font-mono" style="font-size: 10px;">
                                    {{ strtoupper($budget->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown d-inline-block">
                                    <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="btn btn-xs btn-dss-primary" title="Open Budget Workbench">
                                        Monitor
                                    </a>
                                    <button type="button" class="btn btn-xs btn-light border dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px;">
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}">
                                                <i class="mdi mdi-eye-outline text-primary me-2"></i>View Workbench
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1.5" href="javascript:void(0)" onclick="openRealignModal('{{ $budget->getRouteKey() }}', '{{ $budget->budget_code }}', '{{ number_format($budget->effective_total, 2) }}')">
                                                <i class="mdi mdi-swap-horizontal text-warning me-2"></i>Realign / Augment
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.budgets.edit', $budget->getRouteKey()) }}">
                                                <i class="mdi mdi-pencil-outline text-info me-2"></i>Edit Parameters
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('dss.rie.budgets.destroy', $budget->getRouteKey()) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this budget registry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1.5 text-danger">
                                                    <i class="mdi mdi-trash-can-outline me-2"></i>Delete Budget
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-cash-remove fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <div class="fw-semibold">No extension budget appropriations found for FY {{ $selectedYear }}.</div>
                                <div class="extra-small mt-1 mb-3">Initialize your annual fiscal year budget to start tracking proposal obligations and balances.</div>
                                <a href="{{ route('dss.rie.budgets.create', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-dss-primary">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i>Add Annual Budget for FY {{ $selectedYear }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="realignModal" tabindex="-1" aria-labelledby="realignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form id="realignForm" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <div>
                        <h6 class="modal-title fw-bold text-dark" id="realignModalLabel">
                            <i class="mdi mdi-swap-horizontal text-warning me-1"></i>Budget Augmentation / Realignment
                        </h6>
                        <div class="extra-small text-muted font-mono" id="modalBudgetCode">Code</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="extra-small text-muted font-mono text-uppercase">Current Effective Allocation</div>
                        <div class="fs-4 fw-bold text-dark font-mono" id="modalCurrentAllocation">₱0.00</div>
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
    function openRealignModal(budgetKey, code, effectiveTotal) {
        document.getElementById('modalBudgetCode').innerText = 'Target: ' + code;
        document.getElementById('modalCurrentAllocation').innerText = '₱' + effectiveTotal;
        document.getElementById('realignForm').action = "{{ url('dss/rie/budgets') }}/" + budgetKey + "/realign";
        var modal = new bootstrap.Modal(document.getElementById('realignModal'));
        modal.show();
    }
</script>
@endsection
