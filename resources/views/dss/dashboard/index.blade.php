@extends('layouts.dss.app')

@section('title', 'Dashboard')

@section('content')
@php
    $currentTheme = \App\Services\DSS\TenantContext::getTheme();
    $uiTemplate = $currentTheme->ui_template ?? 'flup_minimal';
    $totalPendingSigs = $pendingDirectorSignaturesCount + $pendingDeanSignaturesCount + $pendingCollegeSignaturesCount;
    $approvalRate = $totalProposals > 0 ? round(($approvedCount / $totalProposals) * 100, 1) : 100;
    $ongoingRate = $totalProposals > 0 ? round(($inReviewCount / $totalProposals) * 100, 1) : 0;

    $listRecords = match($activeRoleView) {
        'dean', 'college_head' => $collegePlans->isNotEmpty() ? $collegePlans : $recentPlans,
        'project_leader' => $myPlans->isNotEmpty() ? $myPlans : $recentPlans,
        default => $recentPlans,
    };

    $avatarColors = ['var(--ma-primary)', '#8b5cf6', '#10b981', '#f59e0b', '#0ea5e9'];
@endphp

<div class="container-fluid p-0">
    @if($canSwitchView)
        <div class="pm-card px-3.5 py-2.5 mb-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white">Perspective</span>
                <span class="fw-bold text-dark small">Role Dashboard View</span>
            </div>
            <div class="pm-segment-group">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'director']) }}" class="pm-segment-pill {{ $activeRoleView === 'director' ? 'active' : '' }}">Extension Director</a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'dean']) }}" class="pm-segment-pill {{ $activeRoleView === 'dean' ? 'active' : '' }}">Dean</a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'college_head']) }}" class="pm-segment-pill {{ $activeRoleView === 'college_head' ? 'active' : '' }}">College Head</a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'project_leader']) }}" class="pm-segment-pill {{ $activeRoleView === 'project_leader' ? 'active' : '' }}">Project Leader</a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'extension_office']) }}" class="pm-segment-pill {{ $activeRoleView === 'extension_office' ? 'active' : '' }}">Extension Office</a>
            </div>
        </div>
    @endif

    {{-- ========================================================================
         UI TEMPLATE 1: FLUP — CLEAN SAAS MINIMALIST
         ======================================================================== --}}
    @if($uiTemplate === 'flup_minimal')
        <!-- ROW 1: 4 Bordered KPI Cards + Dashed "Add data" Card -->
        <div class="row g-3 mb-4">
            <div class="col-xl col-md-6">
                <div class="flup-kpi-card">
                    <div class="flup-kpi-top">
                        <i class="mdi mdi-account-multiple-outline"></i>
                        <span>Total beneficiaries</span>
                    </div>
                    <div class="flup-kpi-bottom">
                        <span class="flup-kpi-num">{{ number_format($totalTargetBeneficiaries) }}</span>
                        <span class="flup-kpi-trend text-success">
                            <i class="mdi mdi-trending-up"></i> 2.5%
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="flup-kpi-card">
                    <div class="flup-kpi-top">
                        <i class="mdi mdi-alpha-p-circle-outline"></i>
                        <span>Proposed budget</span>
                    </div>
                    <div class="flup-kpi-bottom">
                        <span class="flup-kpi-num">₱{{ number_format($totalProposedPlanBudget, 0) }}</span>
                        <span class="flup-kpi-trend text-success">
                            <i class="mdi mdi-trending-up"></i> {{ $budgetBurnRate }}%
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="flup-kpi-card">
                    <div class="flup-kpi-top">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                        <span>Extension plans</span>
                    </div>
                    <div class="flup-kpi-bottom">
                        <span class="flup-kpi-num">{{ number_format($activeRoleView === 'project_leader' ? $myPlansCount : $totalPlans) }}</span>
                        <span class="flup-kpi-trend text-danger">
                            <i class="mdi mdi-trending-down"></i> {{ $untrackedPlansCount }} untracked
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="flup-kpi-card">
                    <div class="flup-kpi-top">
                        <i class="mdi mdi-file-check-outline"></i>
                        <span>Approved proposals</span>
                    </div>
                    <div class="flup-kpi-bottom">
                        <span class="flup-kpi-num">{{ number_format($approvedCount) }}</span>
                        <span class="flup-kpi-trend text-success">
                            <i class="mdi mdi-trending-up"></i> {{ $approvalRate }}%
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-12">
                <a href="{{ route('dss.rie.plans.create') }}" class="flup-add-data-card">
                    <i class="mdi mdi-plus-box-outline fs-4 text-muted"></i>
                    <span>Add data</span>
                </a>
            </div>
        </div>

        <!-- ROW 2: Full-Width Grouped Bar Chart ("Product sales" -> "Extension budget & execution") -->
        <div class="flup-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold text-dark m-0" style="font-size: 16px;">Extension budget &amp; execution</h5>
                <div class="d-flex align-items-center gap-4 small fw-semibold">
                    <span class="d-inline-flex align-items-center gap-1.5 text-dark">
                        <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #4f6ef7;"></span>
                        Proposed budget
                    </span>
                    <span class="d-inline-flex align-items-center gap-1.5 text-dark">
                        <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #f59e0b;"></span>
                        Approved allocation
                    </span>
                </div>
            </div>

            <div style="height: 255px; position: relative;">
                <canvas id="flupBarChart"></canvas>
            </div>
        </div>

        <!-- ROW 3: Bottom 2-Column Split (Left: Category Donut + Pills, Right: Campus Reach & Programs) -->
        <div class="row g-4">
            <!-- Left: Programs by extension category (Pills + Donut Chart) -->
            <div class="col-xl-6">
                <div class="flup-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3.5" style="font-size: 16px;">Programs by extension sector</h5>
                    <div class="row align-items-center g-3">
                        <div class="col-sm-7">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-laptop" style="color: #8b5cf6;"></i>
                                        <span class="text-truncate">ICT Literacy – <strong>25%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-sprout" style="color: #3b82f6;"></i>
                                        <span class="text-truncate">Agri-Aqua – <strong>17%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-shield-alert-outline" style="color: #6366f1;"></i>
                                        <span class="text-truncate">DRRM Safety – <strong>13%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-school-outline" style="color: #38bdf8;"></i>
                                        <span class="text-truncate">Education – <strong>12%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-leaf" style="color: #d946ef;"></i>
                                        <span class="text-truncate">Eco-Marine – <strong>9%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-Storefront-outline" style="color: #ec4899;"></i>
                                        <span class="text-truncate">Livelihood – <strong>8%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-solar-power" style="color: #f43f5e;"></i>
                                        <span class="text-truncate">Clean Tech – <strong>6%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-heart-pulse" style="color: #f59e0b;"></i>
                                        <span class="text-truncate">Health Care – <strong>5%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-domain" style="color: #10b981;"></i>
                                        <span class="text-truncate">Governance – <strong>3%</strong></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="flup-cat-pill">
                                        <i class="mdi mdi-hammer-wrench" style="color: #22c55e;"></i>
                                        <span class="text-truncate">Engineering – <strong>2%</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 d-flex justify-content-center">
                            <div style="width: 190px; height: 190px; position: relative;">
                                <canvas id="flupDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Extension distribution by campus & active programs -->
            <div class="col-xl-6">
                <div class="flup-card p-4 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark m-0" style="font-size: 16px;">Extension by campuses &amp; programs</h5>
                        <a href="{{ route('dss.rie.plans.index') }}" class="text-primary small fw-bold text-decoration-none">View all</a>
                    </div>

                    <div class="row align-items-center g-3 flex-grow-1">
                        <div class="col-sm-5">
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: var(--ma-primary);"></span>Surigao Main</span>
                                <strong>42%</strong>
                            </div>
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: var(--ma-primary);"></span>Del Carmen</span>
                                <strong>24%</strong>
                            </div>
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: var(--ma-primary);"></span>Malimono</span>
                                <strong>18%</strong>
                            </div>
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: var(--ma-primary);"></span>Mainit Campus</span>
                                <strong>16%</strong>
                            </div>
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: #94a3b8;"></span>Claver Extension</span>
                                <strong>11%</strong>
                            </div>
                            <div class="flup-country-row">
                                <span class="d-flex align-items-center gap-2"><span class="rounded-circle d-inline-block" style="width: 7px; height: 7px; background: #94a3b8;"></span>DTS Tracked</span>
                                <strong>{{ $trackingCoverageRate }}%</strong>
                            </div>
                        </div>

                        <div class="col-sm-7 border-start ps-sm-3 min-w-0 overflow-hidden">
                            <div class="text-muted extra-small fw-bold text-uppercase mb-2">Recent Extension Programs</div>
                            @forelse($listRecords->take(4) as $idx => $plan)
                                <div class="ma-perf-item py-2">
                                    <div class="min-w-0 flex-grow-1 overflow-hidden">
                                        <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate small" title="{{ $plan->extension_activity ?: $plan->program_name }}">
                                            {{ $plan->extension_activity ?: $plan->program_name }}
                                        </a>
                                        <div class="text-muted extra-small text-truncate">
                                            {{ $plan->campus?->campus_name ?? 'Main Campus' }} &bull; {{ ucfirst($plan->status ?? 'draft') }}
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="fw-bold text-dark extra-small">₱{{ number_format((float) $plan->proposed_budget, 0) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small py-3 text-center">No extension plans yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ========================================================================
         UI TEMPLATE 2: MATERIAL ABLE — ENTERPRISE CLASSIC
         ======================================================================== --}}
    @elseif($uiTemplate === 'material_able')
        <!-- ROW 1: Material Able 2x2 Quad Grids & Split Highlight Banners -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="ma-quad-card">
                    <div class="ma-quad-grid">
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-account-outline text-primary ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ number_format($totalTargetBeneficiaries) }}</div>
                                <div class="ma-quad-label">Beneficiaries</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-volume-high text-success ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $trackingCoverageRate }}%</div>
                                <div class="ma-quad-label">DTS Volume</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-file-document-outline text-danger ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $activeRoleView === 'project_leader' ? $myPlansCount : $totalPlans }}</div>
                                <div class="ma-quad-label">Extension Plans</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-email-outline text-primary ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $activeRoleView === 'project_leader' ? $myProposalsCount : $totalProposals }}</div>
                                <div class="ma-quad-label">Proposals</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="ma-quad-card">
                    <div class="ma-quad-grid">
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-share-variant text-primary ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $campusCount }}</div>
                                <div class="ma-quad-label">Campuses</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-sitemap text-success ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $deptCount }}</div>
                                <div class="ma-quad-label">Network Units</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-signal-cellular-3 text-danger ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $inReviewCount }}</div>
                                <div class="ma-quad-label">In Routing</div>
                            </div>
                        </div>
                        <div class="ma-quad-cell">
                            <i class="mdi mdi-wifi text-info ma-quad-icon"></i>
                            <div class="ma-quad-info">
                                <div class="ma-quad-val">{{ $approvedCount }}</div>
                                <div class="ma-quad-label">Approved</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-12">
                <div class="d-flex flex-column justify-content-between h-100 gap-3">
                    <a href="{{ route('dss.rie.budgets.index') }}" class="ma-split-banner ma-bg-green text-decoration-none">
                        <div class="ma-split-banner-icon"><i class="mdi mdi-star"></i></div>
                        <div class="ma-split-banner-body">
                            <div class="ma-split-banner-val">₱{{ number_format($totalProposedPlanBudget, 0) }}</div>
                            <div class="ma-split-banner-label">Proposed Extension Budget</div>
                        </div>
                    </a>
                    <a href="{{ route('dss.signatures.index') }}" class="ma-split-banner ma-bg-blue text-decoration-none">
                        <div class="ma-split-banner-icon"><i class="mdi mdi-trophy"></i></div>
                        <div class="ma-split-banner-body">
                            <div class="ma-split-banner-val">{{ $activeRoleView === 'project_leader' ? $pendingMySignaturesCount : $totalPendingSigs }}</div>
                            <div class="ma-split-banner-label">Pending Signatory Actions</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- ROW 2: Left Performance List & Right 2x3 Metric Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div class="ma-card h-100 d-flex flex-column">
                    <div class="ma-card-header">
                        <h5 class="ma-card-title">Extension Program Performance</h5>
                        <a href="{{ route('dss.rie.plans.index') }}" class="text-muted"><i class="mdi mdi-wrench-outline"></i></a>
                    </div>
                    <div class="p-4 pt-2 flex-grow-1 d-flex flex-column justify-content-between overflow-hidden">
                        <div class="min-w-0 overflow-hidden">
                            @forelse($listRecords as $idx => $plan)
                                @php
                                    $isApprovedOrNoted = in_array(strtolower($plan->status ?? ''), ['approved', 'noted', 'concurred']);
                                    $avatarBg = $avatarColors[$idx % count($avatarColors)];
                                    $leaderName = $plan->prepared_by_name ?: ($plan->program_name ?: 'Extension Proponent');
                                    $initials = strtoupper(substr(trim($leaderName), 0, 2));
                                @endphp
                                <div class="ma-perf-item">
                                    <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1 overflow-hidden">
                                        <div class="ma-perf-avatar" style="background: {{ $avatarBg }};">{{ $initials }}</div>
                                        <div class="min-w-0 flex-grow-1 overflow-hidden">
                                            <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" title="{{ $plan->extension_activity ?: $plan->program_name }}">
                                                {{ $plan->extension_activity ?: $plan->program_name }}
                                            </a>
                                            <div class="text-muted extra-small text-truncate">
                                                {{ $plan->campus?->campus_name ?? 'Main Campus' }} &bull; {{ ucfirst($plan->status ?? 'draft') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="fw-semibold text-dark">₱{{ number_format((float) $plan->proposed_budget, 0) }}</span>
                                        <i class="mdi {{ $isApprovedOrNoted ? 'mdi-arrow-up text-success' : 'mdi-arrow-down text-danger' }} ms-1"></i>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">No extension plans recorded yet.</div>
                            @endforelse
                        </div>
                        <div class="pt-3 mt-2 border-top d-flex align-items-center justify-content-between">
                            <a href="{{ route('dss.rie.plans.create') }}" class="btn btn-sm btn-primary"><i class="mdi mdi-plus me-1"></i>New Extension Plan</a>
                            <a href="{{ route('dss.rie.plans.index') }}" class="text-primary small text-decoration-none fw-semibold">View All Plans <i class="mdi mdi-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="row g-4 h-100">
                    <div class="col-sm-6">
                        <div class="ma-metric-card">
                            <div class="ma-metric-title">Annual Extension Plans</div>
                            <div class="ma-metric-val"><i class="mdi mdi-arrow-up text-success"></i><span>{{ $totalPlans }}</span></div>
                            <div class="ma-metric-sub">{{ $totalTrackedPlans }} Linked to Central DTS</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ma-metric-card">
                            <div class="ma-metric-title">Proposal Status</div>
                            <div class="ma-metric-val"><i class="mdi mdi-arrow-up text-success"></i><span>{{ $totalProposals }}</span></div>
                            <div class="ma-metric-sub">{{ $approvalRate }}% Approved in FY {{ $fy }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('dss.rie.tracking.index') }}" class="ma-solid-stat-card ma-solid-red text-decoration-none">
                            <span class="ma-solid-stat-badge">{{ max(0, 100 - $trackingCoverageRate) }}%</span>
                            <div class="ma-solid-stat-val">{{ $untrackedPlansCount }}</div>
                            <div class="ma-solid-stat-label">Untracked Documents</div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('dss.rie.budgets.index') }}" class="ma-solid-stat-card ma-solid-green text-decoration-none">
                            <span class="ma-solid-stat-badge">{{ $budgetBurnRate }}%</span>
                            <div class="ma-solid-stat-val">₱{{ number_format($totalApprovedBudget ?: $totalProposedPlanBudget, 0) }}</div>
                            <div class="ma-solid-stat-label">Annual Budget Status</div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <div class="ma-metric-card">
                            <div class="ma-metric-title">DTS Tracked Records</div>
                            <div class="ma-metric-val"><i class="mdi mdi-arrow-down text-danger"></i><span>{{ $totalTrackedPlans }}</span></div>
                            <div class="ma-metric-sub">{{ $trackingCoverageRate }}% Tracking Coverage</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ma-metric-card">
                            <div class="ma-metric-title">Registered Personnel</div>
                            <div class="ma-metric-val"><i class="mdi mdi-arrow-up text-success"></i><span>{{ $usersCount }}</span></div>
                            <div class="ma-metric-sub">Across {{ $campusCount }} University Campuses</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 3: Bottom Full-Width 4-Column Progress Slider Strip -->
        <div class="ma-card p-4">
            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="text-muted small mb-1">Published Plans</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fs-5 fw-bold text-dark">{{ number_format($totalPlans) }}</span>
                        <span class="text-success fw-semibold small">+100%</span>
                    </div>
                    <div class="ma-slider-track">
                        <div class="ma-slider-fill" style="width: {{ min(100, max(20, $totalPlans * 15)) }}%; background: #ff5252; color: #ff5252;"><span class="ma-slider-thumb"></span></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="text-muted small mb-1">Approved Proposals</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fs-5 fw-bold text-dark">{{ number_format($approvedCount) }}</span>
                        <span class="text-danger fw-semibold small">{{ $approvalRate }}%</span>
                    </div>
                    <div class="ma-slider-track">
                        <div class="ma-slider-fill" style="width: {{ min(100, max(15, $approvalRate)) }}%; background: var(--ma-primary); color: var(--ma-primary);"><span class="ma-slider-thumb"></span></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="text-muted small mb-1">DTS Tracking Sync</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fs-5 fw-bold text-dark">{{ $trackingCoverageRate }}%</span>
                        <span class="text-success fw-semibold small">+{{ $totalTrackedPlans }} linked</span>
                    </div>
                    <div class="ma-slider-track">
                        <div class="ma-slider-fill" style="width: {{ min(100, max(18, $trackingCoverageRate)) }}%; background: #11c15b; color: #11c15b;"><span class="ma-slider-thumb"></span></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="text-muted small mb-1">Ongoing Proposals</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fs-5 fw-bold text-dark">{{ number_format($inReviewCount) }}</span>
                        <span class="text-success fw-semibold small">+{{ $ongoingRate }}%</span>
                    </div>
                    <div class="ma-slider-track">
                        <div class="ma-slider-fill" style="width: {{ min(100, max(22, $ongoingRate)) }}%; background: #ffba57; color: #ffba57;"><span class="ma-slider-thumb"></span></div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ========================================================================
         UI TEMPLATE 3: PRINT MANZIL — ELEGANT BENTO SOFT
         ======================================================================== --}}
    @else
        <!-- ROW 1: 4 Print Manzil KPI Cards with Soft Icon Box & Smooth SVG Sparkline -->
        <div class="row g-3 mb-3.5">
            <div class="col-xl-3 col-md-6">
                <div class="pm-kpi-card">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="pm-kpi-label">Total Proposed Budget</div>
                            <div class="pm-kpi-value text-truncate">₱{{ number_format($totalProposedPlanBudget, 0) }}</div>
                        </div>
                        <div class="pm-kpi-icon-box pm-kpi-icon-primary"><i class="mdi mdi-chart-bar"></i></div>
                    </div>
                    <div class="pm-kpi-footer">
                        <div><span class="pm-kpi-delta text-primary">+{{ $budgetBurnRate }}%</span><span class="pm-kpi-sub ms-1">allocated in FY {{ $fy }}</span></div>
                        <svg width="78" height="28" viewBox="0 0 78 28" fill="none"><path d="M2 24C12 22 18 14 26 17C34 20 42 8 52 12C62 16 68 4 76 6" stroke="var(--ma-primary)" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="pm-kpi-card">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="pm-kpi-label">Total Extension Plans</div>
                            <div class="pm-kpi-value">{{ number_format($activeRoleView === 'project_leader' ? $myPlansCount : $totalPlans) }}</div>
                        </div>
                        <div class="pm-kpi-icon-box pm-kpi-icon-purple"><i class="mdi mdi-trending-up"></i></div>
                    </div>
                    <div class="pm-kpi-footer">
                        <div><span class="pm-kpi-delta" style="color: #8b5cf6;">+{{ $totalTrackedPlans }}</span><span class="pm-kpi-sub ms-1">linked to DTS</span></div>
                        <svg width="78" height="28" viewBox="0 0 78 28" fill="none"><path d="M2 22C10 22 16 12 24 18C32 24 40 6 48 14C56 22 64 4 76 10" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="pm-kpi-card">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="pm-kpi-label">Extension Proposals</div>
                            <div class="pm-kpi-value">{{ number_format($activeRoleView === 'project_leader' ? $myProposalsCount : $totalProposals) }}</div>
                        </div>
                        <div class="pm-kpi-icon-box pm-kpi-icon-amber"><i class="mdi mdi-package-variant-closed"></i></div>
                    </div>
                    <div class="pm-kpi-footer">
                        <div><span class="pm-kpi-delta text-warning">{{ $approvalRate }}%</span><span class="pm-kpi-sub ms-1">approval rate</span></div>
                        <svg width="78" height="28" viewBox="0 0 78 28" fill="none"><path d="M2 20C14 18 20 24 30 14C40 4 48 22 58 12C68 2 72 16 76 8" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="pm-kpi-card">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="pm-kpi-label">Target Beneficiaries</div>
                            <div class="pm-kpi-value">{{ number_format($totalTargetBeneficiaries) }}</div>
                        </div>
                        <div class="pm-kpi-icon-box pm-kpi-icon-emerald"><i class="mdi mdi-cube-send"></i></div>
                    </div>
                    <div class="pm-kpi-footer">
                        <div><span class="pm-kpi-delta text-success">+{{ $campusCount }}</span><span class="pm-kpi-sub ms-1">active campuses</span></div>
                        <svg width="78" height="28" viewBox="0 0 78 28" fill="none"><path d="M2 24C14 20 22 16 32 18C42 20 50 8 60 11C68 13 72 4 76 5" stroke="#10b981" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: Left Mini Card + Center Workflow Analytics Strip + Right DTS Server Status Card -->
        <div class="row g-3 mb-3.5">
            <div class="col-xl-2 col-lg-4">
                <div class="pm-card p-3.5 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white" style="width: 28px; height: 28px; min-width: 28px;"><i class="mdi mdi-shield-check-outline" style="font-size: 15px;"></i></span>
                        <span class="fw-bold text-dark text-truncate" style="font-size: 13px;">Allocation</span>
                    </div>
                    <div class="my-2">
                        <svg width="100%" height="36" viewBox="0 0 120 36" fill="none" preserveAspectRatio="none"><path d="M2 24C14 8 24 30 36 16C48 2 60 28 74 14C88 0 100 26 118 8" stroke="var(--ma-primary)" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 16px;">₱{{ number_format($totalApprovedBudget ?: $totalProposedPlanBudget, 0) }}</div>
                        <div class="fw-bold text-primary" style="font-size: 11.5px;">+{{ $budgetBurnRate }}% Utilized</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-8">
                <div class="pm-card p-3.5 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="d-inline-flex align-items-center justify-content-center border rounded-3 text-dark" style="width: 32px; height: 32px;"><i class="mdi mdi-compass-rose" style="font-size: 17px;"></i></span>
                            <span class="fw-bold text-dark" style="font-size: 14.5px;">Extension Workflow Analytics</span>
                        </div>
                        <a href="{{ route('dss.rie.plans.index') }}" class="text-muted small text-decoration-none fw-semibold">FY {{ $fy }} Pipeline</a>
                    </div>

                    <div class="d-flex align-items-center gap-2.5 flex-wrap">
                        <a href="{{ route('dss.rie.plans.index') }}" class="pm-analytics-pill">
                            <div class="d-flex align-items-center gap-2 min-w-0"><span class="pm-analytics-dot" style="background: #f1f5f9; color: #475569;"><i class="mdi mdi-file-edit-outline"></i></span><span class="fw-semibold text-truncate" style="font-size: 12px;">Draft</span></div>
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $draftCount }}</span>
                        </a>
                        <a href="{{ route('dss.rie.proposals.index') }}" class="pm-analytics-pill">
                            <div class="d-flex align-items-center gap-2 min-w-0"><span class="pm-analytics-dot" style="background: #dcfce7; color: #10b981;"><i class="mdi mdi-check-circle-outline"></i></span><span class="fw-semibold text-truncate" style="font-size: 12px;">Approved</span></div>
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $approvedCount }}</span>
                        </a>
                        <a href="{{ route('dss.rie.proposals.index') }}" class="pm-analytics-pill">
                            <div class="d-flex align-items-center gap-2 min-w-0"><span class="pm-analytics-dot" style="background: #ede9fe; color: #8b5cf6;"><i class="mdi mdi-sync"></i></span><span class="fw-semibold text-truncate" style="font-size: 12px;">In Routing</span></div>
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $inReviewCount }}</span>
                        </a>
                        <a href="{{ route('dss.signatures.index') }}" class="pm-analytics-pill">
                            <div class="d-flex align-items-center gap-2 min-w-0"><span class="pm-analytics-dot" style="background: #fef3c7; color: #f59e0b;"><i class="mdi mdi-draw-pen"></i></span><span class="fw-semibold text-truncate" style="font-size: 12px;">For Sign</span></div>
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $totalPendingSigs }}</span>
                        </a>
                        <a href="{{ route('dss.rie.tracking.index') }}" class="pm-analytics-pill">
                            <div class="d-flex align-items-center gap-2 min-w-0"><span class="pm-analytics-dot" style="background: #dcfce7; color: #059669;"><i class="mdi mdi-barcode-scan"></i></span><span class="fw-semibold text-truncate" style="font-size: 12px;">Tracked</span></div>
                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $totalTrackedPlans }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-12">
                <div class="pm-card p-3.5 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="fw-bold text-dark" style="font-size: 14px;">DTS Sync Status</span>
                        <a href="{{ route('dss.rie.tracking.index') }}" class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-3 border text-decoration-none" style="background: #f8fafc;">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-dark text-white" style="width: 20px; height: 20px; font-size: 11px;"><i class="mdi mdi-file-document-outline"></i></span>
                            <span class="fw-bold text-dark" style="font-size: 11.5px;">{{ $untrackedPlansCount }}</span>
                            <span class="text-danger fw-bold" style="font-size: 10.5px;">Untracked</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 my-2">
                        <svg width="115" height="34" viewBox="0 0 115 34" fill="none"><path d="M2 24C12 14 20 26 30 16C40 6 48 24 58 14C68 4 78 24 88 12C98 2 106 18 113 8" stroke="var(--ma-primary)" stroke-width="2" stroke-linecap="round"/></svg>
                        <div class="text-end"><div class="fw-bold text-primary" style="font-size: 21px; line-height: 1.1;">{{ $trackingCoverageRate > 0 ? $trackingCoverageRate : '99.9' }}%</div></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div><div class="text-muted" style="font-size: 11px;">Overall Performance</div><div class="fw-bold text-success" style="font-size: 12px;">Excellent</div></div>
                        <span class="text-muted extra-small">All Campuses &bull; FY {{ $fy }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 3: Left Overview Smooth Area Chart & Activity Rate + Right Extension Programs Feed & Personnel -->
        <div class="row g-3">
            <div class="col-xl-8">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="pm-card p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="pm-card-title">Overview</h5>
                            <span class="px-3 py-1 rounded-3 border bg-light fw-semibold text-dark" style="font-size: 12px;">Extension Budget <i class="mdi mdi-chevron-down ms-1"></i></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="pm-segment-group">
                                <button type="button" class="pm-segment-pill" onclick="window.switchOverviewDataset('q1', this)">Q1</button>
                                <button type="button" class="pm-segment-pill" onclick="window.switchOverviewDataset('q2', this)">Q2</button>
                                <button type="button" class="pm-segment-pill active" onclick="window.switchOverviewDataset('month', this)">Month</button>
                                <button type="button" class="pm-segment-pill" onclick="window.switchOverviewDataset('year', this)">Year</button>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <a href="{{ route('dss.rie.plans.create') }}" class="btn btn-sm btn-primary"><i class="mdi mdi-plus"></i> New Plan</a>
                                <a href="{{ route('dss.rie.proposals.create') }}" class="btn btn-sm btn-light border text-dark"><i class="mdi mdi-file-plus-outline"></i> New Proposal</a>
                            </div>
                        </div>
                        <div style="height: 235px; position: relative;">
                            <canvas id="pmOverviewChart"></canvas>
                        </div>
                    </div>

                    <div class="pm-card p-4 flex-grow-1 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <h6 class="pm-card-title" style="font-size: 14px !important;">Beneficiary &amp; Campus Reach</h6>
                            <span class="text-muted fw-semibold" style="font-size: 12px;">FY {{ $fy }} <i class="mdi mdi-chevron-down"></i></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 my-1">
                            <div class="d-flex align-items-center gap-2.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 border bg-light text-dark" style="width: 36px; height: 36px;"><i class="mdi mdi-account-group-outline fs-5"></i></span>
                                <div><div class="text-muted" style="font-size: 11px;">Total Beneficiaries</div><div class="fw-bold text-dark" style="font-size: 15px;">{{ number_format($totalTargetBeneficiaries) }}</div></div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="px-3 py-1.5 rounded-3 border bg-light">
                                    <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 10.5px;"><span class="rounded-circle d-inline-block" style="width: 6px; height: 6px; background: #f59e0b;"></span>Active Plans</div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 13px;">{{ $totalPlans }}</div>
                                </div>
                                <div class="px-3 py-1.5 rounded-3 border bg-light">
                                    <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 10.5px;"><span class="rounded-circle d-inline-block" style="width: 6px; height: 6px; background: #8b5cf6;"></span>Proposals</div>
                                    <div class="fw-bold text-dark text-center" style="font-size: 13px;">{{ $totalProposals }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative pt-2">
                            <svg width="100%" height="54" viewBox="0 0 600 54" fill="none" preserveAspectRatio="none">
                                <path d="M0 44 Q 60 28, 120 38 T 240 18 T 360 36 T 480 20 T 600 32" stroke="#f59e0b" stroke-width="2.2" fill="none"/>
                                <path d="M0 48 Q 60 36, 120 42 T 240 28 T 360 40 T 480 30 T 600 38" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="4 4" fill="none"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="pm-card p-4 flex-grow-1 d-flex flex-column justify-content-between overflow-hidden">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="pm-card-title">Extension Programs</h5>
                                <a href="{{ route('dss.rie.plans.index') }}" class="text-muted small text-decoration-none fw-semibold">View All</a>
                            </div>
                            <div class="min-w-0 overflow-hidden">
                                @forelse($listRecords->take(4) as $idx => $plan)
                                    @php
                                        $avatarBg = $avatarColors[$idx % count($avatarColors)];
                                        $leaderName = $plan->prepared_by_name ?: ($plan->program_name ?: 'Extension Proponent');
                                        $initials = strtoupper(substr(trim($leaderName), 0, 2));
                                    @endphp
                                    <div class="pm-feed-item">
                                        <div class="d-flex align-items-center gap-2.5 min-w-0 flex-grow-1 overflow-hidden">
                                            <div class="pm-feed-avatar" style="background: {{ $avatarBg }};">{{ $initials }}</div>
                                            <div class="min-w-0 flex-grow-1 overflow-hidden">
                                                <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 13px;" title="{{ $plan->extension_activity ?: $plan->program_name }}">
                                                    {{ $plan->extension_activity ?: $plan->program_name }}
                                                </a>
                                                <div class="text-muted text-truncate" style="font-size: 11.5px;">{{ $plan->campus?->campus_name ?? 'Main Campus' }} &bull; {{ ucfirst($plan->status ?? 'draft') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end flex-shrink-0 ms-2">
                                            <div class="text-muted fw-semibold" style="font-size: 11px;">₱{{ number_format((float) $plan->proposed_budget, 0) }}</div>
                                            <div class="mt-1"><span class="pm-feed-badge">{{ $idx + 1 }}</span></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted small">No extension programs recorded yet.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="pt-3 mt-2 border-top d-flex align-items-center justify-content-between">
                            <a href="{{ route('dss.rie.plans.create') }}" class="btn btn-sm btn-primary"><i class="mdi mdi-plus me-1"></i>New Plan</a>
                            <a href="{{ route('dss.signatures.index') }}" class="text-primary small fw-bold text-decoration-none">{{ $totalPendingSigs }} Pending Signatures <i class="mdi mdi-chevron-right"></i></a>
                        </div>
                    </div>

                    <div class="pm-card p-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="pm-card-title" style="font-size: 14px !important;">Extension Personnel</h6>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('dss.settings.employees.index') }}" class="text-muted small text-decoration-none fw-semibold">View All</a>
                                <a href="{{ route('dss.settings.users.index') }}" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white text-decoration-none" style="width: 28px; height: 28px;"><i class="mdi mdi-plus" style="font-size: 16px;"></i></a>
                            </div>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 11.5px;">You have <strong>{{ $usersCount }}</strong> registered personnel across {{ $campusCount }} campuses</p>
                        <div class="pm-avatar-stack">
                            @foreach(['AP', 'MA', 'RO', 'JD', 'CL', 'ES'] as $sIdx => $init)
                                <a href="{{ route('dss.settings.employees.index') }}" class="pm-contact-avatar" style="background: {{ ['#e2e8f0', '#dbeafe', '#fce7f3', '#dcfce7', '#fef3c7', '#ede9fe'][$sIdx % 6] }};">{{ $init }}</a>
                            @endforeach
                            <a href="{{ route('dss.settings.employees.index') }}" class="pm-contact-avatar fw-bold" style="background: #f8fafc; border: 1.5px solid #cbd5e1;">+{{ max(1, $usersCount - 6) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const rootStyles = getComputedStyle(document.documentElement);
    const primaryColor = (rootStyles.getPropertyValue('--dss-primary') || '#047857').trim();

    function hexToRgba(hex, alpha) {
        let c = hex.replace('#', '');
        if (c.length === 3) c = c.split('').map(x => x + x).join('');
        const num = parseInt(c, 16);
        if (isNaN(num)) return `rgba(4, 120, 87, ${alpha})`;
        return `rgba(${(num >> 16) & 255}, ${(num >> 8) & 255}, ${num & 255}, ${alpha})`;
    }

    // 1. FLUP GROUPED BAR CHART & DONUT CHART
    const flupBarEl = document.getElementById('flupBarChart');
    if (flupBarEl) {
        new Chart(flupBarEl, {
            type: 'bar',
            data: {
                labels: ['1 Jul', '2 Jul', '3 Jul', '4 Jul', '5 Jul', '6 Jul', '7 Jul', '8 Jul', '9 Jul', '10 Jul', '11 Jul', '12 Jul'],
                datasets: [
                    {
                        label: 'Proposed budget (₱K)',
                        data: [28, 32, 22, 34, 52, 50, 16, 35, 26, 44, 34, 58],
                        backgroundColor: '#4f6ef7',
                        borderRadius: 4,
                        barPercentage: 0.55,
                        categoryPercentage: 0.48
                    },
                    {
                        label: 'Approved allocation (₱K)',
                        data: [38, 45, 56, 43, 44, 58, 38, 42, 32, 48, 45, 54],
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                        barPercentage: 0.55,
                        categoryPercentage: 0.48
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#0f172a',
                        bodyColor: '#0f172a',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11 },
                            callback: value => value + ' K'
                        }
                    }
                }
            }
        });
    }

    const flupDonutEl = document.getElementById('flupDonutChart');
    if (flupDonutEl) {
        new Chart(flupDonutEl, {
            type: 'doughnut',
            data: {
                labels: ['ICT Literacy', 'Agri-Aqua', 'DRRM Safety', 'Education', 'Eco-Marine', 'Livelihood', 'Clean Tech', 'Health Care', 'Governance', 'Engineering'],
                datasets: [{
                    data: [25, 17, 13, 12, 9, 8, 6, 5, 3, 2],
                    backgroundColor: ['#8b5cf6', '#6366f1', '#a855f7', '#38bdf8', '#34d399', '#ec4899', '#3b82f6', '#ef4444', '#f59e0b', '#10b981'],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 2. PRINT MANZIL SMOOTH AREA CHART
    const pmCtx = document.getElementById('pmOverviewChart');
    if (pmCtx) {
        const chartCtx = pmCtx.getContext('2d');
        const gradient = chartCtx.createLinearGradient(0, 0, 0, 230);
        gradient.addColorStop(0, hexToRgba(primaryColor, 0.20));
        gradient.addColorStop(1, hexToRgba(primaryColor, 0.0));

        const datasetsByMode = {
            month: [15, 28, 32, 30, 48, 55, 62, 46, 58, 42, 68, 82],
            q1:    [20, 35, 45, 40, 52, 60, 58, 65, 70, 62, 75, 85],
            q2:    [18, 24, 38, 46, 42, 58, 64, 52, 68, 72, 78, 90],
            year:  [25, 32, 40, 48, 55, 60, 68, 72, 76, 80, 86, 95]
        };

        const overviewChart = new Chart(pmCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Extension Budget & Activity Index',
                    data: datasetsByMode.month,
                    borderColor: primaryColor,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    tension: 0.42,
                    fill: true,
                    pointRadius: [0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0],
                    pointHoverRadius: 6,
                    pointBackgroundColor: primaryColor,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: primaryColor, titleColor: '#ffffff', bodyColor: '#ffffff', padding: 10, cornerRadius: 10, displayColors: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                    y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9', borderDash: [4, 4] }, ticks: { color: '#94a3b8', font: { size: 11 }, maxTicksLimit: 5 } }
                }
            }
        });

        window.switchOverviewDataset = function(mode, btn) {
            document.querySelectorAll('.pm-segment-group .pm-segment-pill').forEach(el => {
                if (el.tagName === 'BUTTON') el.classList.remove('active');
            });
            if (btn) btn.classList.add('active');
            if (datasetsByMode[mode]) {
                overviewChart.data.datasets[0].data = datasetsByMode[mode];
                overviewChart.update();
            }
        };
    }
});
</script>
@endsection
