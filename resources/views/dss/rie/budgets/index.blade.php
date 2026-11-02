@extends('layouts.dss.app')

@section('title', 'Annual Extension Budget & Fund Utilization — SNSU RIE')

@section('content')
<div class="container-fluid p-0">
    {{-- Breadcrumb & Quick Action Toolbar --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Annual Extension Budgets</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.budgets.export', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1 extra-small fw-semibold shadow-2xs d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-tray-arrow-down text-primary"></i>
                <span>Export Report (CSV)</span>
            </a>
            <a href="{{ route('dss.rie.budgets.create', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-plus-circle-outline"></i>
                <span>Add Annual Budget</span>
            </a>
        </div>
    </div>

    {{-- Executive Financial Hero Overview Card --}}
    <div class="card border rounded-3 p-4 mb-3 shadow-2xs bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-finance me-1"></i>Financial Planning &amp; Monitoring
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        Institutional Fund Governance
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    Extension Annual Budget Monitoring &bull; FY {{ $selectedYear }}
                </h4>
                <p class="text-muted extra-small mb-3" style="max-width: 680px; line-height: 1.6;">
                    Track institutional appropriations, campus fund shares, approved obligations, and real-time remaining liquidity for community extension programs across Surigao del Norte State University.
                </p>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="extra-small font-mono fw-bold text-muted text-uppercase me-1">Fiscal Year:</span>
                    @foreach($availableYears as $year)
                        <a href="{{ route('dss.rie.budgets.index', array_merge(request()->query(), ['fy' => $year])) }}"
                           class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold border {{ $selectedYear == $year ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                            FY {{ $year }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 250px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">FY {{ $selectedYear }} Overall Burn Rate</div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="fs-2 fw-bold text-dark font-mono">{{ $macroBurnRate }}%</span>
                        <span class="badge {{ $macroBurnRate > 90 ? 'bg-danger' : ($macroBurnRate > 70 ? 'bg-warning text-dark' : 'bg-success') }} font-mono extra-small">
                            {{ $macroBurnRate > 90 ? 'Depleting' : ($macroBurnRate > 70 ? 'Active' : 'Liquid') }}
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                        <div class="progress-bar {{ $macroBurnRate > 90 ? 'bg-danger' : ($macroBurnRate > 70 ? 'bg-warning' : 'bg-success') }}"
                             role="progressbar" style="width: {{ min(100, $macroBurnRate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono">
                        Total Encumbered: <strong>{{ $macroCommitmentRate }}%</strong> (with pending proposals)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 Executive Bento Metric Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Annual Budget</div>
                        <div class="fs-4 fw-bold text-dark font-mono mb-0">₱{{ number_format($totalApprovedBudget, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-2 text-success">
                        <i class="mdi mdi-bank-check fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Effective Total</span>
                    <span class="badge bg-light text-dark border font-mono">{{ $budgets->count() }} Registries</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Approved Utilized</div>
                        <div class="fs-4 fw-bold text-primary font-mono mb-0">₱{{ number_format($totalUtilized, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-2 text-primary">
                        <i class="mdi mdi-check-decagram-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Active Obligations</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">{{ $macroBurnRate }}% utilized</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">In-Review Committed</div>
                        <div class="fs-4 fw-bold text-warning font-mono mb-0">₱{{ number_format($totalCommitted, 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-2 text-warning">
                        <i class="mdi mdi-clock-time-four-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Pending Endorsement</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono">In Review</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold font-mono tracking-wider mb-1">Available Remaining</div>
                        <div class="fs-4 fw-bold font-mono mb-0 {{ $totalRemainingBalance < 0 ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format($totalRemainingBalance, 2) }}
                        </div>
                    </div>
                    <div class="rounded-3 bg-info-subtle p-2 text-info">
                        <i class="mdi mdi-wallet-outline fs-4"></i>
                    </div>
                </div>
                <div class="extra-small text-muted d-flex align-items-center justify-content-between pt-2 border-top">
                    <span>Projected Liquidity</span>
                    <span class="fw-semibold font-mono {{ $totalProjectedBalance < 0 ? 'text-danger' : 'text-dark' }}">
                        ₱{{ number_format($totalProjectedBalance, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Segmented View Switcher Toolbar --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-inline-flex align-items-center gap-1 p-1 bg-white rounded-pill border shadow-2xs">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 extra-small fw-bold btn-dark text-white budget-view-btn active" id="btnViewTable" onclick="switchBudgetView('table', this)">
                <i class="mdi mdi-table-large me-1"></i>Allocations Table ({{ $budgets->count() }})
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 extra-small fw-semibold text-secondary budget-view-btn" id="btnViewCampuses" onclick="switchBudgetView('campuses', this)">
                <i class="mdi mdi-school-outline me-1"></i>Campus Distribution ({{ $campuses->count() + 1 }})
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 extra-small fw-semibold text-secondary budget-view-btn" id="btnViewClusters" onclick="switchBudgetView('clusters', this)">
                <i class="mdi mdi-chart-pie-outline me-1"></i>Fund Clusters Matrix
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-muted border font-mono extra-small">
                Active: {{ $statusCounts['active'] ?? 0 }} &bull; Closed: {{ $statusCounts['closed'] ?? 0 }}
            </span>
        </div>
    </div>

    {{-- PANE 1: ALLOCATIONS TABLE VIEW --}}
    <div id="viewPaneTable" class="budget-view-pane">
        <div class="card border rounded-3 bg-white shadow-2xs mb-4 overflow-hidden">
            {{-- Filter Bar --}}
            <div class="p-3 border-bottom bg-light">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-xl-4">
                        <h6 class="fw-bold text-dark mb-0">Extension Fiscal Year Budget Allocations</h6>
                        <div class="extra-small text-muted">Breakdown of approved appropriations per campus jurisdiction and fund cluster.</div>
                    </div>

                    <div class="col-12 col-xl-8">
                        <div class="d-flex align-items-center justify-content-xl-end gap-2 flex-wrap flex-md-nowrap">
                            {{-- Quick Live Search with Input Group (Prevents icon & text overlap) --}}
                            <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" style="min-width: 220px; max-width: 320px;">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 8px 0 0 8px;">
                                    <i class="mdi mdi-magnify" style="font-size: 15px;"></i>
                                </span>
                                <input type="text" id="budgetSearchInput" class="form-control form-control-sm border-start-0" placeholder="Filter code, campus, cluster..." autocomplete="off" style="border-radius: 0 8px 8px 0; padding-left: 4px !important; font-size: 12px;">
                            </div>

                            {{-- Campus Filter --}}
                            <select id="budgetCampusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 12px; min-width: 160px; max-width: 200px; border-radius: 8px;" onchange="window.filterTableRows()">
                                <option value="">All Campuses</option>
                                <option value="University-Wide">University-Wide (Main)</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->campus_name }}">{{ $campus->campus_name }}</option>
                                @endforeach
                            </select>

                            {{-- Status Filter --}}
                            <select id="budgetStatusFilter" class="form-select form-select-sm flex-grow-1 flex-md-grow-0" style="font-size: 12px; min-width: 120px; max-width: 140px; border-radius: 8px;" onchange="window.filterTableRows()">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                                <option value="draft">Draft</option>
                            </select>

                            {{-- Reset Filter Button --}}
                            <button type="button" class="btn btn-sm btn-light border p-1 rounded-2 text-muted flex-shrink-0" title="Reset Filters" onclick="resetBudgetFilters()" style="width: 31px; height: 31px;">
                                <i class="mdi mdi-refresh" style="font-size: 14px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table View --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 12px;" id="budgetDataTable">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th class="ps-3" style="min-width: 130px;">Actions</th>
                            <th style="min-width: 240px;">Budget Code &amp; Title</th>
                            <th style="min-width: 150px;">Campus</th>
                            <th style="min-width: 150px;">Fund Cluster</th>
                            <th style="min-width: 140px;">Approved Allocation</th>
                            <th style="min-width: 140px;">Utilized / Obligated</th>
                            <th style="min-width: 140px;">Remaining Balance</th>
                            <th style="min-width: 130px;">Burn Rate</th>
                            <th class="text-end pe-3" style="min-width: 90px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $budget)
                            @php
                                $health = $budget->health_status;
                                $rem = $budget->remaining_balance;
                            @endphp
                            <tr class="budget-row-item"
                                data-campus="{{ $budget->campus?->campus_name ?? 'University-Wide' }}"
                                data-status="{{ $budget->status }}"
                                data-search="{{ strtolower($budget->budget_code . ' ' . $budget->title . ' ' . ($budget->campus?->campus_name ?? 'University-Wide') . ' ' . $budget->fund_cluster) }}">
                                {{-- 1ST COLUMN: ACTIONS --}}
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 extra-small fw-bold">
                                            <i class="mdi mdi-eye-outline me-1"></i>Monitor
                                        </a>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-xs btn-light border rounded-circle p-1" data-bs-toggle="dropdown" aria-expanded="false" style="width: 26px; height: 26px;">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-start shadow-sm border extra-small">
                                                <li>
                                                    <a class="dropdown-item py-1" href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}">
                                                        <i class="mdi mdi-view-dashboard-outline text-primary me-2"></i>Open Full Workbench
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item py-1" onclick="openRealignModal('{{ $budget->getRouteKey() }}', '{{ $budget->budget_code }}', '{{ number_format($budget->effective_total, 2) }}')">
                                                        <i class="mdi mdi-swap-horizontal text-warning me-2"></i>Realign / Augment Funds
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-1" href="{{ route('dss.rie.budgets.edit', $budget->getRouteKey()) }}">
                                                        <i class="mdi mdi-pencil-outline text-info me-2"></i>Edit Parameters
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('dss.rie.budgets.destroy', $budget->getRouteKey()) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this budget appropriation?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-1 text-danger">
                                                            <i class="mdi mdi-trash-can-outline me-2"></i>Delete Budget
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2ND COLUMN: BUDGET CODE & TITLE --}}
                                <td>
                                    <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="fw-bold font-mono text-dark text-decoration-none">
                                        {{ $budget->budget_code }}
                                    </a>
                                    <div class="fw-semibold text-dark text-truncate mt-1" style="max-width: 250px;" title="{{ $budget->title }}">{{ $budget->title }}</div>
                                    <div class="extra-small text-muted font-mono mt-1">
                                        Resolution: {{ $budget->board_resolution_no ?: ($budget->approved_by ?: 'Institutional Resolution') }}
                                    </div>
                                </td>

                                {{-- 3RD COLUMN: CAMPUS --}}
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

                                {{-- 4TH COLUMN: FUND CLUSTER --}}
                                <td>
                                    <div class="extra-small font-mono text-dark fw-bold">{{ $budget->fund_cluster }}</div>
                                    <div class="extra-small text-muted font-mono">
                                        GAA: ₱{{ number_format($budget->allocated_gaa, 2) }} &bull; STF: ₱{{ number_format($budget->allocated_stf, 2) }}
                                    </div>
                                </td>

                                {{-- 5TH COLUMN: APPROVED ALLOCATION --}}
                                <td class="font-mono fw-bold text-dark">
                                    ₱{{ number_format($budget->effective_total, 2) }}
                                    @if($budget->realigned_amount != 0)
                                        <div class="extra-small {{ $budget->realigned_amount > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $budget->realigned_amount > 0 ? '+' : '' }}₱{{ number_format($budget->realigned_amount, 2) }} realigned
                                        </div>
                                    @endif
                                </td>

                                {{-- 6TH COLUMN: UTILIZED / OBLIGATED --}}
                                <td>
                                    <div class="font-mono fw-bold text-primary">₱{{ number_format($budget->utilized_amount, 2) }}</div>
                                    <div class="extra-small text-muted font-mono mt-1">
                                        ₱{{ number_format($budget->committed_amount, 2) }} in-review
                                    </div>
                                </td>

                                {{-- 7TH COLUMN: REMAINING BALANCE --}}
                                <td>
                                    <div class="font-mono fw-bold {{ $rem < 0 ? 'text-danger' : 'text-success' }}">
                                        ₱{{ number_format($rem, 2) }}
                                    </div>
                                    <div class="extra-small text-muted font-mono">
                                        Proj: ₱{{ number_format($budget->projected_balance, 2) }}
                                    </div>
                                </td>

                                {{-- 8TH COLUMN: BURN RATE --}}
                                <td>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="font-mono extra-small fw-bold">{{ $budget->burn_rate }}%</span>
                                        <span class="extra-small text-muted font-mono">{{ $budget->commitment_rate }}% enc.</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background: #e2e8f0;">
                                        <div class="progress-bar {{ $health['progress_class'] }}" role="progressbar" style="width: {{ min(100, $budget->burn_rate) }}%;"></div>
                                    </div>
                                </td>

                                {{-- 9TH COLUMN: STATUS --}}
                                <td class="text-end pe-3">
                                    <span class="badge rounded-pill {{ $budget->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }} px-2 py-1 font-mono extra-small">
                                        {{ strtoupper($budget->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-cash-remove fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    <div class="fw-semibold">No extension budget appropriations found for FY {{ $selectedYear }}.</div>
                                    <div class="extra-small mt-1 mb-3">Initialize your annual fiscal year budget to start tracking proposal obligations and balances.</div>
                                    <a href="{{ route('dss.rie.budgets.create', ['fy' => $selectedYear]) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold">
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

    {{-- PANE 2: CAMPUS DISTRIBUTION GRID --}}
    <div id="viewPaneCampuses" class="budget-view-pane d-none">
        <div class="card border rounded-3 bg-white shadow-2xs mb-3 p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0">Campus Extension Fund Allocation &amp; Utilization</h6>
                    <div class="extra-small text-muted">Territorial appropriation distribution and active fund burn across university campuses.</div>
                </div>
                <span class="badge bg-light text-secondary border font-mono extra-small">
                    {{ $campuses->count() + 1 }} Jurisdiction Units
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- University-Wide / Main Operations Unit --}}
            @php
                $mainBudgets = $budgets->whereNull('campus_id');
                $mainAlloc = $mainBudgets->sum(fn($b) => $b->effective_total);
                $mainUtil = $mainBudgets->sum(fn($b) => $b->utilized_amount);
                $mainComm = $mainBudgets->sum(fn($b) => $b->committed_amount);
                $mainRem = $mainAlloc - $mainUtil;
                $mainBurn = $mainAlloc > 0 ? round(($mainUtil / $mainAlloc) * 100, 1) : 0;
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small mb-1">
                                    <i class="mdi mdi-domain me-1"></i>CENTRAL
                                </span>
                                <h6 class="fw-bold text-dark mb-0">University-Wide (Main)</h6>
                            </div>
                            <span class="badge bg-light text-dark border font-mono extra-small">{{ $mainBudgets->count() }} registry</span>
                        </div>

                        <div class="row g-2 mt-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded-2 border">
                                    <div class="extra-small text-muted font-mono text-uppercase">Allocation</div>
                                    <div class="fw-bold font-mono text-dark extra-small">₱{{ number_format($mainAlloc, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded-2 border">
                                    <div class="extra-small text-muted font-mono text-uppercase">Utilized</div>
                                    <div class="fw-bold font-mono text-primary extra-small">₱{{ number_format($mainUtil, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center extra-small font-mono mb-1">
                                <span class="text-muted">Remaining Balance:</span>
                                <span class="fw-bold {{ $mainRem < 0 ? 'text-danger' : 'text-success' }}">₱{{ number_format($mainRem, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center extra-small font-mono mb-1">
                                <span class="text-muted">Burn Rate:</span>
                                <span class="fw-bold text-dark">{{ $mainBurn }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; background: #e2e8f0;">
                                <div class="progress-bar {{ $mainBurn > 90 ? 'bg-danger' : ($mainBurn > 70 ? 'bg-warning' : 'bg-success') }}"
                                     role="progressbar" style="width: {{ min(100, $mainBurn) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="extra-small text-muted font-mono">₱{{ number_format($mainComm, 2) }} committed</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small" onclick="filterByCampus('University-Wide')">
                            <i class="mdi mdi-filter-outline me-1"></i>Filter Table
                        </button>
                    </div>
                </div>
            </div>

            {{-- Campus Cards --}}
            @foreach($campuses as $campus)
                @php
                    $cBudgets = $budgets->where('campus_id', $campus->id);
                    $cAlloc = $cBudgets->sum(fn($b) => $b->effective_total);
                    $cUtil = $cBudgets->sum(fn($b) => $b->utilized_amount);
                    $cComm = $cBudgets->sum(fn($b) => $b->committed_amount);
                    $cRem = $cAlloc - $cUtil;
                    $cBurn = $cAlloc > 0 ? round(($cUtil / $cAlloc) * 100, 1) : 0;
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-light text-secondary border font-mono extra-small mb-1">
                                        <i class="mdi mdi-school-outline me-1"></i>CAMPUS
                                    </span>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 220px;" title="{{ $campus->campus_name }}">
                                        {{ $campus->campus_name }}
                                    </h6>
                                </div>
                                <span class="badge bg-light text-dark border font-mono extra-small">{{ $cBudgets->count() }} registries</span>
                            </div>

                            <div class="row g-2 mt-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded-2 border">
                                        <div class="extra-small text-muted font-mono text-uppercase">Allocation</div>
                                        <div class="fw-bold font-mono text-dark extra-small">₱{{ number_format($cAlloc, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded-2 border">
                                        <div class="extra-small text-muted font-mono text-uppercase">Utilized</div>
                                        <div class="fw-bold font-mono text-primary extra-small">₱{{ number_format($cUtil, 2) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center extra-small font-mono mb-1">
                                    <span class="text-muted">Remaining Balance:</span>
                                    <span class="fw-bold {{ $cRem < 0 ? 'text-danger' : 'text-success' }}">₱{{ number_format($cRem, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center extra-small font-mono mb-1">
                                    <span class="text-muted">Burn Rate:</span>
                                    <span class="fw-bold text-dark">{{ $cBurn }}%</span>
                                </div>
                                <div class="progress" style="height: 6px; background: #e2e8f0;">
                                    <div class="progress-bar {{ $cBurn > 90 ? 'bg-danger' : ($cBurn > 70 ? 'bg-warning' : 'bg-success') }}"
                                         role="progressbar" style="width: {{ min(100, $cBurn) }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="extra-small text-muted font-mono">₱{{ number_format($cComm, 2) }} committed</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small" onclick="filterByCampus('{{ addslashes($campus->campus_name) }}')">
                                <i class="mdi mdi-filter-outline me-1"></i>Filter Table
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PANE 3: FUND CLUSTERS MATRIX --}}
    <div id="viewPaneClusters" class="budget-view-pane d-none">
        <div class="card border rounded-3 bg-white shadow-2xs mb-3 p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0">Fund Clusters Breakdown Matrix (DBM &amp; CHED Standard)</h6>
                    <div class="extra-small text-muted">Analysis of appropriations divided into General Appropriations Act, Special Trust Funds, and External Grants.</div>
                </div>
                <span class="badge bg-light text-secondary border font-mono extra-small">
                    Philippine SUC Financial Standards
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- Fund Cluster 1: GAA --}}
            <div class="col-12 col-lg-4">
                <div class="card border rounded-3 p-4 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 bg-primary-subtle p-2 text-primary">
                                <i class="mdi mdi-bank fs-4"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">FUND CLUSTER 01</span>
                                <h6 class="fw-bold text-dark mb-0">General Appropriations Act (GAA)</h6>
                            </div>
                        </div>

                        <p class="text-muted extra-small mb-3" style="line-height: 1.5;">
                            Mandated National Government allotment supporting core extension services, outreach operations, and community capability-building mandates across all campuses.
                        </p>

                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="extra-small text-muted font-mono text-uppercase mb-1">Total GAA Allocated</div>
                            <div class="fs-4 fw-bold text-dark font-mono">₱{{ number_format($totalGaa, 2) }}</div>
                            <div class="extra-small text-muted font-mono mt-1">
                                Share of Total: <strong>{{ $totalApprovedBudget > 0 ? round(($totalGaa / $totalApprovedBudget) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div class="extra-small text-muted pt-2 border-top d-flex align-items-center gap-1 font-mono">
                        <i class="mdi mdi-shield-check-outline text-success"></i>
                        <span>DBM NEP / GAA Regular Allotment</span>
                    </div>
                </div>
            </div>

            {{-- Fund Cluster 2: STF --}}
            <div class="col-12 col-lg-4">
                <div class="card border rounded-3 p-4 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 bg-success-subtle p-2 text-success">
                                <i class="mdi mdi-cash-register fs-4"></i>
                            </div>
                            <div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">FUND CLUSTER 05</span>
                                <h6 class="fw-bold text-dark mb-0">Special Trust Fund (STF)</h6>
                            </div>
                        </div>

                        <p class="text-muted extra-small mb-3" style="line-height: 1.5;">
                            Internally Generated Income (IGI), tuition shares, training fees, and revolving fund revenues allocated for university-wide extension activities pursuant to RA 8292.
                        </p>

                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="extra-small text-muted font-mono text-uppercase mb-1">Total STF Allocated</div>
                            <div class="fs-4 fw-bold text-success font-mono">₱{{ number_format($totalStf, 2) }}</div>
                            <div class="extra-small text-muted font-mono mt-1">
                                Share of Total: <strong>{{ $totalApprovedBudget > 0 ? round(($totalStf / $totalApprovedBudget) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div class="extra-small text-muted pt-2 border-top d-flex align-items-center gap-1 font-mono">
                        <i class="mdi mdi-shield-check-outline text-success"></i>
                        <span>Fiduciary &amp; Institutional Reserves</span>
                    </div>
                </div>
            </div>

            {{-- Fund Cluster 3: External Grants & Consortia --}}
            <div class="col-12 col-lg-4">
                <div class="card border rounded-3 p-4 bg-white shadow-2xs h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 bg-warning-subtle p-2 text-warning">
                                <i class="mdi mdi-handshake fs-4"></i>
                            </div>
                            <div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono extra-small">FUND CLUSTER 07</span>
                                <h6 class="fw-bold text-dark mb-0">External Grants &amp; Consortia</h6>
                            </div>
                        </div>

                        <p class="text-muted extra-small mb-3" style="line-height: 1.5;">
                            Special counterpart funds from national agencies (DOST, CHED, DA-BAR), Local Government Units (LGUs), non-governmental partners, and international grants.
                        </p>

                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="extra-small text-muted font-mono text-uppercase mb-1">Total External Allocated</div>
                            <div class="fs-4 fw-bold text-warning font-mono">₱{{ number_format($totalExternal, 2) }}</div>
                            <div class="extra-small text-muted font-mono mt-1">
                                Share of Total: <strong>{{ $totalApprovedBudget > 0 ? round(($totalExternal / $totalApprovedBudget) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div class="extra-small text-muted pt-2 border-top d-flex align-items-center gap-1 font-mono">
                        <i class="mdi mdi-shield-check-outline text-success"></i>
                        <span>Inter-Agency Grants-in-Aid</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fund Realignment & BOR Resolutions Summary Banner --}}
        <div class="card border rounded-3 p-3 bg-white shadow-2xs mb-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-light text-dark border font-mono extra-small">BOR &amp; Executive Approvals</span>
                        <h6 class="fw-bold text-dark mb-0">Fund Realignment &amp; Augmentation Governance</h6>
                    </div>
                    <div class="extra-small text-muted" style="line-height: 1.6;">
                        Total net adjustments recorded in FY {{ $selectedYear }}:
                        <strong class="font-mono {{ $totalRealigned >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $totalRealigned >= 0 ? '+' : '' }}₱{{ number_format($totalRealigned, 2) }}
                        </strong>.
                        Every augmentation and realignment requires official documentation and Board of Regents (BOR) resolution tracking.
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 extra-small fw-bold" onclick="switchBudgetView('table', document.getElementById('btnViewTable'))">
                        <i class="mdi mdi-table-eye me-1"></i>Inspect All Allocations
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: REALIGNMENT / AUGMENTATION --}}
<div class="modal fade" id="realignModal" tabindex="-1" aria-labelledby="realignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <form id="realignForm" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom bg-light py-3 px-4">
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
                <div class="modal-footer border-top bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 py-1 fw-bold">
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
    function switchBudgetView(mode, btn) {
        document.querySelectorAll('.budget-view-pane').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.budget-view-btn').forEach(b => {
            b.classList.remove('active', 'btn-dark', 'text-white');
            b.classList.add('text-secondary');
        });

        if (btn) {
            btn.classList.add('active', 'btn-dark', 'text-white');
            btn.classList.remove('text-secondary');
        }

        if (mode === 'table') {
            document.getElementById('viewPaneTable')?.classList.remove('d-none');
        } else if (mode === 'campuses') {
            document.getElementById('viewPaneCampuses')?.classList.remove('d-none');
        } else if (mode === 'clusters') {
            document.getElementById('viewPaneClusters')?.classList.remove('d-none');
        }
    }

    function filterByCampus(campusName) {
        const campusSelect = document.getElementById('budgetCampusFilter');
        if (campusSelect) {
            campusSelect.value = campusName;
        }
        switchBudgetView('table', document.getElementById('btnViewTable'));
        window.filterTableRows();
    }

    function resetBudgetFilters() {
        const searchInput = document.getElementById('budgetSearchInput');
        const campusSelect = document.getElementById('budgetCampusFilter');
        const statusSelect = document.getElementById('budgetStatusFilter');
        if (searchInput) searchInput.value = '';
        if (campusSelect) campusSelect.value = '';
        if (statusSelect) statusSelect.value = '';
        window.filterTableRows();
    }

    function openRealignModal(budgetKey, code, effectiveTotal) {
        document.getElementById('modalBudgetCode').innerText = 'Target: ' + code;
        document.getElementById('modalCurrentAllocation').innerText = '₱' + effectiveTotal;
        document.getElementById('realignForm').action = "{{ url('dss/rie/budgets') }}/" + budgetKey + "/realign";
        var modal = new bootstrap.Modal(document.getElementById('realignModal'));
        modal.show();
    }

    window.filterTableRows = function () {
        const q = (document.getElementById('budgetSearchInput')?.value || '').toLowerCase().trim();
        const campus = (document.getElementById('budgetCampusFilter')?.value || '').trim();
        const status = (document.getElementById('budgetStatusFilter')?.value || '').trim();

        document.querySelectorAll('.budget-row-item').forEach(row => {
            const rowCampus = row.getAttribute('data-campus') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesCampus = campus === '' || rowCampus === campus;
            const matchesStatus = status === '' || rowStatus === status;
            const matchesSearch = q === '' || rowSearch.includes(q);

            row.style.display = (matchesCampus && matchesStatus && matchesSearch) ? '' : 'none';
        });
    };

    document.getElementById('budgetSearchInput')?.addEventListener('input', window.filterTableRows);
</script>
@endsection
