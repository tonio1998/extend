@extends('layouts.dss.app')

@section('title', 'Unit Extension Monitoring & 3-Year Impact Assessment — SNSU RIE System')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    /* DataTables Custom Controls */
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

    {{-- Breadcrumb & Quick Actions (Standard Template) --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">RIE Operations</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Unit Extension Monitoring &amp; Impact</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-download text-primary"></i>
                    <span>Export CSV</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" style="border-radius: 12px; font-size: 12.5px; min-width: 240px;">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.rie.unit_monitoring.export', ['type' => 'units', 'campus' => $selectedCampus, 'search' => $searchQuery]) }}">
                            <i class="mdi mdi-table-large text-success"></i>
                            <span>Export Units Summary Table</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.rie.unit_monitoring.export', ['type' => 'impact', 'campus' => $selectedCampus, 'search' => $searchQuery]) }}">
                            <i class="mdi mdi-chart-timeline-variant text-danger"></i>
                            <span>Export 3-Year Impact Pipeline</span>
                        </a>
                    </li>
                </ul>
            </div>

            <a href="{{ route('dss.rie.calendar.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-calendar-range text-success"></i>
                <span>Extension Calendar</span>
            </a>
        </div>
    </div>

    {{-- Executive Hero Overview Card (Matching Proposals & Plans Standard Template) --}}
    <div class="card border rounded-3 p-4 mb-3 shadow-2xs bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-domain me-1"></i>RIE SYSTEM &bull; UNIT MONITORING
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        Mandatory 3-Year Post-Commencement Impact Assessment
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    Unit Extension Monitoring &amp; 3-Year Impact Assessment
                </h4>
                <p class="text-muted extra-small mb-3" style="max-width: 760px; line-height: 1.6;">
                    Comprehensive institutional monitoring across university academic colleges, departments, and satellite campuses. Tracks cumulative budget allocation, project volume, and automatically computes 3-year post-commencement impact assessment eligibility.
                </p>

                {{-- Segmented View Switcher (Units Overview vs 3-Year Impact Pipeline) --}}
                <div class="d-inline-flex align-items-center gap-1 p-1 bg-light rounded-pill border shadow-2xs">
                    <a href="{{ route('dss.rie.unit_monitoring.index', array_merge(request()->query(), ['tab' => 'units'])) }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ $activeTab === 'units' ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-domain me-1"></i>Units Extension Overview ({{ $kpis['units_with_extensions'] }}/{{ $kpis['total_units'] }})
                    </a>
                    <a href="{{ route('dss.rie.unit_monitoring.index', array_merge(request()->query(), ['tab' => 'impact'])) }}"
                       class="btn btn-xs rounded-pill px-3 py-1 extra-small fw-bold text-decoration-none {{ $activeTab === 'impact' ? 'btn-dark text-white' : 'btn-light text-secondary' }}">
                        <i class="mdi mdi-chart-timeline-variant me-1"></i>3-Year Impact Assessment Pipeline
                        @if($kpis['impact_due_count'] > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-1 font-mono">{{ $kpis['impact_due_count'] }} Due</span>
                        @endif
                    </a>
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 250px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">Impact Evaluation Progress</div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fs-4 fw-bold text-dark font-mono">{{ $kpis['impact_completed_count'] }}</span>
                        <span class="badge {{ $kpis['impact_due_count'] > 0 ? 'bg-danger text-white' : 'bg-success text-white' }} font-mono extra-small">
                            {{ $kpis['impact_due_count'] > 0 ? $kpis['impact_due_count'] . ' Due for Survey' : 'Pipeline Up-to-Date' }}
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                        @php
                            $impactTotal = $kpis['impact_completed_count'] + $kpis['impact_due_count'] + $kpis['impact_upcoming_count'];
                            $impactRate = $impactTotal > 0 ? round(($kpis['impact_completed_count'] / $impactTotal) * 100, 1) : 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $impactRate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono">
                        {{ $impactRate }}% evaluated &bull; ₱{{ number_format($kpis['total_budget'], 2) }} funds tracked
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Executive 4-Col Stat Cards (Matching Proposals & Plans Masterlist) --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Total Extensions</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">
                        <i class="mdi mdi-lightbulb-on-outline"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">{{ $kpis['total_extensions'] }}</div>
                <div class="extra-small text-muted">
                    <span class="text-primary fw-semibold">{{ $kpis['active_extensions'] }} active</span> &bull; <span>{{ $kpis['completed_extensions'] }} completed</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Cumulative Budget</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle font-mono extra-small">
                        <i class="mdi mdi-cash-multiple"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">₱{{ number_format($kpis['total_budget'], 2) }}</div>
                <div class="extra-small text-muted">
                    Internal &amp; external funding committed
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Monitored Units</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                        <i class="mdi mdi-domain"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">
                    {{ $kpis['units_with_extensions'] }} <span class="fs-6 fw-normal text-muted font-sans">/ {{ $kpis['total_units'] }}</span>
                </div>
                <div class="extra-small text-muted">
                    <span class="text-success fw-semibold"><i class="mdi mdi-check-circle me-0.5"></i>Active Colleges &amp; Departments</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">3-Yr Impact Due</span>
                    <span class="badge {{ $kpis['impact_due_count'] > 0 ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-success-subtle text-success border-success-subtle' }} border font-mono extra-small">
                        <i class="mdi mdi-clock-alert-outline"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold {{ $kpis['impact_due_count'] > 0 ? 'text-danger' : 'text-dark' }} font-mono mb-1">
                    {{ $kpis['impact_due_count'] }}
                </div>
                <div class="extra-small">
                    @if($kpis['impact_due_count'] > 0)
                        <span class="text-danger fw-bold"><i class="mdi mdi-alert-circle me-0.5"></i>Requires 3-Year Post Impact Survey</span>
                    @else
                        <span class="text-success fw-semibold"><i class="mdi mdi-check-circle me-0.5"></i>All 3-Yr Assessments Completed</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Monitoring Card with Standard Tabs & Filter Bar --}}
    <div class="card border rounded-3 bg-white shadow-2xs mb-4">
        {{-- Card Header: Filter Toolbar --}}
        <div class="p-3 border-bottom bg-light bg-opacity-50">
            <form method="GET" action="{{ route('dss.rie.unit_monitoring.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">

                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="mdi mdi-magnify"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by unit name, code, title, or leader..." value="{{ $searchQuery }}">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="mdi mdi-map-marker-outline"></i>
                        </span>
                        <select name="campus" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="all" {{ $selectedCampus === 'all' ? 'selected' : '' }}>All Campuses (Entire University)</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ (string)$selectedCampus === (string)$campus->id ? 'selected' : '' }}>
                                    {{ $campus->campus_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill fw-semibold d-flex align-items-center gap-1 shadow-2xs">
                        <i class="mdi mdi-filter-variant"></i>
                        <span>Apply Filter</span>
                    </button>
                    @if($searchQuery || $selectedCampus !== 'all')
                        <a href="{{ route('dss.rie.unit_monitoring.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light border px-2.5 rounded-pill text-muted" title="Reset Filters">
                            <i class="mdi mdi-close"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Nav Tabs Navigation (Standard DSS nav-tabs-custom) --}}
        <div class="px-3 pt-2 border-bottom">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="monitoringTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'units' ? 'active' : '' }}" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-pane" type="button" role="tab" aria-controls="units-pane" aria-selected="{{ $activeTab === 'units' ? 'true' : 'false' }}" onclick="document.getElementById('activeTabInput').value='units'">
                        <i class="mdi mdi-domain me-1"></i>
                        <span>Monitored Units &amp; Departments</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ count($unitsSummary) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'impact' ? 'active' : '' }}" id="impact-tab" data-bs-toggle="tab" data-bs-target="#impact-pane" type="button" role="tab" aria-controls="impact-pane" aria-selected="{{ $activeTab === 'impact' ? 'true' : 'false' }}" onclick="document.getElementById('activeTabInput').value='impact'">
                        <i class="mdi mdi-chart-timeline-variant me-1"></i>
                        <span>3-Year Post-Commencement Impact Assessment Pipeline</span>
                        <span class="badge rounded-pill {{ $kpis['impact_due_count'] > 0 ? 'bg-danger text-white' : 'bg-light text-dark border' }} ms-1 font-mono">
                            {{ count($proposalsList) }}
                            @if($kpis['impact_due_count'] > 0)
                                &bull; {{ $kpis['impact_due_count'] }} Due
                            @endif
                        </span>
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab Content Bodies --}}
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="monitoringTabsContent">

                {{-- ==========================================================
                     TAB 1: MONITORED UNITS & DEPARTMENTS SUMMARY
                     ========================================================== --}}
                <div class="tab-pane fade {{ $activeTab === 'units' ? 'show active' : '' }}" id="units-pane" role="tabpanel" aria-labelledby="units-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Unit / Department Summary Table</h6>
                            <p class="text-muted extra-small mb-0">Extension service activity volume, cumulative budget, and impact survey compliance grouped per academic unit.</p>
                        </div>
                        <span class="badge bg-light text-muted border font-mono px-3 py-1 extra-small">
                            Showing {{ count($unitsSummary) }} Monitored Unit(s)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="unitsSummaryTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 130px;" class="text-center">Action</th>
                                    <th style="min-width: 220px;">Unit / Department</th>
                                    <th style="width: 160px;">Campus</th>
                                    <th style="min-width: 180px;">Unit Head / Extension Coordinator</th>
                                    <th style="width: 120px;" class="text-center">Total Extensions</th>
                                    <th style="width: 80px;" class="text-center">Active</th>
                                    <th style="width: 80px;" class="text-center">Completed</th>
                                    <th style="width: 150px;" class="text-end">Cumulative Budget</th>
                                    <th style="width: 130px;" class="text-center">3-Yr Impact Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unitsSummary as $u)
                                    <tr>
                                        {{-- 1st Column: Table Action --}}
                                        <td class="text-center">
                                            <a href="{{ route('dss.rie.unit_monitoring.unit_transactions', $u['unit_id']) }}"
                                               class="btn btn-sm btn-light border px-2.5 py-1 extra-small fw-semibold shadow-2xs d-inline-flex align-items-center gap-1 text-decoration-none"
                                               title="View Dedicated Unit Projects &amp; Transactions Page">
                                                <i class="mdi mdi-open-in-new text-primary"></i>
                                                <span>Projects &amp; Transactions ({{ $u['total_extensions'] }})</span>
                                            </a>
                                        </td>

                                        {{-- Column 2: Unit / Department --}}
                                        <td>
                                            <a href="{{ route('dss.rie.unit_monitoring.unit_transactions', $u['unit_id']) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $u['unit_name'] }}
                                            </a>
                                            @if($u['unit_code'])
                                                <span class="badge bg-light text-secondary border font-mono extra-small">{{ $u['unit_code'] }}</span>
                                            @endif
                                        </td>

                                        {{-- Column 3: Campus --}}
                                        <td>
                                            <span class="text-muted extra-small d-inline-flex align-items-center gap-1">
                                                <i class="mdi mdi-map-marker text-success"></i>
                                                {{ $u['campus_name'] }}
                                            </span>
                                        </td>

                                        {{-- Column 4: Unit Head / Coordinator --}}
                                        <td>
                                            <div class="fw-semibold text-dark extra-small">{{ $u['unit_head'] }}</div>
                                            <div class="text-muted" style="font-size: 11px;">Lead Coordinator</div>
                                        </td>

                                        {{-- Column 5: Total Extensions --}}
                                        <td class="text-center">
                                            <span class="badge {{ $u['total_extensions'] > 0 ? 'bg-success text-white' : 'bg-light text-muted border' }} font-mono px-2.5 py-1">
                                                {{ $u['total_extensions'] }}
                                            </span>
                                        </td>

                                        {{-- Column 6: Active --}}
                                        <td class="text-center font-mono extra-small text-dark fw-semibold">
                                            {{ $u['active_extensions'] }}
                                        </td>

                                        {{-- Column 7: Completed --}}
                                        <td class="text-center font-mono extra-small text-muted">
                                            {{ $u['completed_extensions'] }}
                                        </td>

                                        {{-- Column 8: Cumulative Budget --}}
                                        <td class="text-end font-mono extra-small fw-bold text-dark">
                                            ₱{{ number_format($u['total_budget'], 2) }}
                                        </td>

                                        {{-- Column 9: 3-Year Impact Assessment Due --}}
                                        <td class="text-center">
                                            @if($u['impact_due_count'] > 0)
                                                <span class="badge bg-danger text-white rounded-pill font-mono extra-small px-2 py-1">
                                                    <i class="mdi mdi-alert-circle me-0.5"></i>{{ $u['impact_due_count'] }} Due
                                                </span>
                                            @elseif($u['impact_completed_count'] > 0)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono extra-small px-2 py-1">
                                                    <i class="mdi mdi-check me-0.5"></i>{{ $u['impact_completed_count'] }} Assessed
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border font-mono extra-small">
                                                    Maturing
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-domain-off fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                            <div class="fw-semibold">No monitored university units found</div>
                                            <div class="extra-small text-muted">Try adjusting your campus filter or search query.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ==========================================================
                     TAB 2: 3-YEAR POST-COMMENCEMENT IMPACT ASSESSMENT PIPELINE
                     ========================================================== --}}
                <div class="tab-pane fade {{ $activeTab === 'impact' ? 'show active' : '' }}" id="impact-pane" role="tabpanel" aria-labelledby="impact-tab">
                    {{-- Impact Policy Notice Box --}}
                    <div class="alert alert-success border-success-subtle bg-success-subtle bg-opacity-25 rounded-3 p-3 mb-3 d-flex align-items-start gap-2.5">
                        <i class="mdi mdi-information-outline fs-5 text-success flex-shrink-0 mt-0.5"></i>
                        <div class="extra-small text-dark">
                            <strong>Mandatory Institutional Policy:</strong> Impact assessments are officially conducted <strong>3 years (36 months) after the commencement of the extension project</strong> to rigorously evaluate beneficiary adoption, sustainability, and socio-economic outcomes. Initiatives reaching or exceeding the 3-year threshold require formal survey deployment.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">3-Year Extension Initiatives Impact Evaluation Ledger</h6>
                            <p class="text-muted extra-small mb-0">Tracking commencement timestamps, elapsed lifecycle, and audit status.</p>
                        </div>
                        <span class="badge bg-light text-muted border font-mono px-3 py-1 extra-small">
                            {{ count($proposalsList) }} Extension Projects Audited
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="impactPipelineTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 140px;" class="text-center">Action</th>
                                    <th style="min-width: 240px;">Extension Proposal</th>
                                    <th style="width: 180px;">Unit &amp; Campus</th>
                                    <th style="width: 160px;">Project Leader</th>
                                    <th style="width: 130px;">Commencement</th>
                                    <th style="width: 110px;">Elapsed</th>
                                    <th style="width: 140px;">3-Yr Impact Due Date</th>
                                    <th style="min-width: 180px;">Impact Assessment Status</th>
                                    <th style="width: 120px;" class="text-end">Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proposalsList as $item)
                                    @php
                                        $prop = $item['proposal'];
                                        $badge = $item['impact_status_badge'];
                                    @endphp
                                    <tr class="{{ $item['impact_status'] === 'due' ? 'table-danger bg-opacity-10' : '' }}">
                                        {{-- 1st Column: Actions --}}
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View Full Proposal">
                                                    <i class="mdi mdi-eye-outline text-primary"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm {{ $item['impact_status'] === 'due' ? 'btn-danger text-white' : ($item['is_assessed'] ? 'btn-outline-success' : 'btn-light border') }} px-2 py-1 extra-small fw-semibold shadow-2xs"
                                                        onclick="openRecordImpactModal({{ $prop->id }}, {{ json_encode($prop->proposal_code . ' — ' . $prop->title) }}, {{ json_encode($item['assessment_meta']) }})"
                                                        title="{{ $item['is_assessed'] ? 'Update Assessment Details' : 'Record Official Impact Assessment' }}">
                                                    <i class="mdi {{ $item['is_assessed'] ? 'mdi-check-decagram' : 'mdi-clipboard-edit-outline' }}"></i>
                                                    <span>{{ $item['is_assessed'] ? 'Edit' : 'Assess' }}</span>
                                                </button>
                                            </div>
                                        </td>

                                        {{-- Column 2: Extension Title & Code --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5 mb-1 flex-wrap">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                                    {{ $prop->proposal_code }}
                                                </span>
                                                @php $pBadge = $prop->status_badge; @endphp
                                                <span class="badge {{ $pBadge['class'] }} rounded-pill extra-small font-mono">
                                                    {{ $pBadge['label'] }}
                                                </span>
                                            </div>
                                            <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none hover-underline" style="font-size: 13px;">
                                                {{ $prop->title }}
                                            </a>
                                        </td>

                                        {{-- Column 3: Implementing Unit --}}
                                        <td>
                                            <div class="fw-semibold text-dark extra-small">{{ $item['unit_name'] }}</div>
                                            <div class="extra-small text-muted"><i class="mdi mdi-domain me-0.5"></i>{{ $item['campus_name'] }}</div>
                                        </td>

                                        {{-- Column 4: Project Leader --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5">
                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold font-mono" style="width: 20px; height: 20px; font-size: 9.5px;">
                                                    {{ strtoupper(substr($prop->project_leader_name, 0, 1)) }}
                                                </div>
                                                <div class="extra-small text-dark fw-semibold text-truncate" style="max-width: 140px;" title="{{ $prop->project_leader_name }}">
                                                    {{ $prop->project_leader_name }}
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Column 5: Commencement Date --}}
                                        <td class="font-mono extra-small">
                                            @if($item['commencement_date'])
                                                <div class="fw-bold text-dark"><i class="mdi mdi-play-circle-outline text-success me-0.5"></i>{{ $item['commencement_date']->format('M d, Y') }}</div>
                                            @else
                                                <span class="text-muted">To Be Scheduled</span>
                                            @endif
                                        </td>

                                        {{-- Column 6: Elapsed Time --}}
                                        <td class="font-mono extra-small">
                                            <span class="badge bg-light text-dark border rounded-pill">
                                                {{ $item['elapsed_text'] }} ago
                                            </span>
                                        </td>

                                        {{-- Column 7: 3-Year Due Date --}}
                                        <td class="font-mono extra-small">
                                            @if($item['impact_due_date'])
                                                <div class="fw-bold text-{{ $item['impact_status'] === 'due' ? 'danger' : 'dark' }}">
                                                    <i class="mdi mdi-flag-checkered text-{{ $item['impact_status'] === 'due' ? 'danger' : 'primary' }} me-0.5"></i>
                                                    {{ $item['impact_due_date']->format('M d, Y') }}
                                                </div>
                                                <div class="text-muted" style="font-size: 11px;">(36 Mos Post-Commence)</div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Column 8: Impact Assessment Status --}}
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge {{ $badge['class'] }} rounded-pill extra-small px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                                    <i class="mdi {{ $badge['icon'] }}"></i>
                                                    <span>{{ $badge['label'] }}</span>
                                                </span>
                                                <span class="extra-small text-muted" style="font-size: 11px;">
                                                    {{ $badge['description'] }}
                                                </span>
                                                @if($item['is_assessed'] && !empty($item['assessment_meta']['evaluator_name']))
                                                    <span class="extra-small text-success font-mono" style="font-size: 10.5px;">
                                                        Evaluator: {{ $item['assessment_meta']['evaluator_name'] }} ({{ $item['assessment_meta']['conducted_at'] ?? '' }})
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Column 9: Budget --}}
                                        <td class="text-end font-mono extra-small fw-semibold text-dark">
                                            {{ $prop->budget_total_display ?: '₱0.00' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-clipboard-text-search-outline fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                            <div class="fw-semibold">No extension projects found in impact pipeline</div>
                                            <div class="extra-small text-muted">Try clearing campus filters or search parameters.</div>
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

{{-- MODAL 1: Quick View of Unit Extension Initiatives --}}
<div class="modal fade" id="unitExtensionsModal" tabindex="-1" aria-labelledby="unitExtensionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="unitModalTitle">Unit Extension Initiatives</h6>
                    <div class="extra-small text-muted" id="unitModalCampus">Surigao City Main Campus</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="table-light">
                            <tr class="text-uppercase extra-small text-muted fw-bold">
                                <th style="width: 140px;">Code</th>
                                <th>Project Title</th>
                                <th style="width: 140px;">Leader</th>
                                <th style="width: 110px;">Commenced</th>
                                <th style="width: 110px;" class="text-end">Budget</th>
                            </tr>
                        </thead>
                        <tbody id="unitModalTableBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: Record / Update Impact Assessment Survey Data --}}
<div class="modal fade" id="recordImpactModal" tabindex="-1" aria-labelledby="recordImpactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <div>
                    <h6 class="modal-title fw-bold mb-0 text-white">Record 3-Year Post-Commencement Impact Assessment</h6>
                    <div class="extra-small text-white text-opacity-75">Document official evaluation findings &amp; sustainability rating</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recordImpactForm" onsubmit="submitImpactAssessment(event)">
                @csrf
                <input type="hidden" id="impactProposalId" name="proposal_id">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Extension Project</label>
                        <div class="p-2.5 bg-light rounded border extra-small fw-bold text-dark" id="impactModalProposalTitle">
                            SNSU-PROP-2026-0001
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="assessmentDateInput">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm font-mono" id="assessmentDateInput" name="assessment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="evaluatorNameInput">Lead Evaluator <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="evaluatorNameInput" name="evaluator_name" placeholder="Name of Evaluator / Auditor" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted" for="sustainabilityRatingSelect">Sustainability &amp; Viability Rating <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="sustainabilityRatingSelect" name="sustainability_rating" required>
                            <option value="Outstanding / Highly Sustainable">Outstanding / Highly Sustainable (Active community adoption)</option>
                            <option value="Satisfactory / Sustainable with Support">Satisfactory / Sustainable with Support (Occasional university oversight)</option>
                            <option value="Moderate / Maturing">Moderate / Maturing (Transitioning to independent ops)</option>
                            <option value="Low / Needs Institutional Interventions">Low / Needs Institutional Interventions (High risk of attrition)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted" for="adoptionLevelSelect">Community Technology / Knowledge Adoption Level <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="adoptionLevelSelect" name="community_adoption_level" required>
                            <option value="High Adoption (Institutionalized in Community)">High Adoption (Institutionalized in Community / LGU adopted)</option>
                            <option value="Moderate Adoption (Regularly Utilized)">Moderate Adoption (Regularly Utilized by Beneficiary Groups)</option>
                            <option value="Limited Adoption (Partial Utilization)">Limited Adoption (Partial Utilization / Pilot stage)</option>
                            <option value="No Long-Term Adoption Detected">No Long-Term Adoption Detected</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted" for="findingsSummaryText">Summary Findings &amp; Impact Evidence <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm" id="findingsSummaryText" name="findings_summary" rows="3" placeholder="Describe key behavioral, economic, or environmental improvements observed in the community..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted" for="recommendationsText">Next-Step Recommendations</label>
                        <textarea class="form-control form-control-sm" id="recommendationsText" name="recommendations" rows="2" placeholder="Policy recommendations, extension scale-up plans, or termination notice..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted" for="reportUrlInput">Impact Assessment Report / Repository Link</label>
                        <input type="url" class="form-control form-control-sm" id="reportUrlInput" name="report_url" placeholder="https://drive.google.com/... or official institutional document URL">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-2xs" id="submitImpactBtn">
                        <i class="mdi mdi-check-circle me-1"></i>
                        <span>Save Impact Assessment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openUnitModal(unitName, campusName, proposals) {
        document.getElementById('unitModalTitle').textContent = unitName;
        document.getElementById('unitModalCampus').textContent = campusName;

        const tbody = document.getElementById('unitModalTableBody');
        tbody.innerHTML = '';

        if (!proposals || proposals.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No extension initiatives recorded for this unit.</td></tr>';
        } else {
            proposals.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="font-mono extra-small fw-bold text-success">${p.code || '—'}</td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 320px;">${p.title || 'Untitled'}</div>
                        <span class="badge bg-light text-muted border font-mono extra-small">${p.status || 'Active'}</span>
                    </td>
                    <td class="extra-small text-muted">${p.leader || '—'}</td>
                    <td class="font-mono extra-small">${p.commencement || '—'}</td>
                    <td class="font-mono extra-small text-end fw-bold text-dark">${p.budget || '₱0.00'}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('unitExtensionsModal'));
        modal.show();
    }

    function openRecordImpactModal(proposalId, proposalTitle, existingMeta) {
        document.getElementById('impactProposalId').value = proposalId;
        document.getElementById('impactModalProposalTitle').textContent = proposalTitle;

        if (existingMeta) {
            document.getElementById('assessmentDateInput').value = existingMeta.conducted_at || '{{ date('Y-m-d') }}';
            document.getElementById('evaluatorNameInput').value = existingMeta.evaluator_name || '';
            document.getElementById('sustainabilityRatingSelect').value = existingMeta.sustainability_rating || 'Outstanding / Highly Sustainable';
            document.getElementById('adoptionLevelSelect').value = existingMeta.community_adoption_level || 'High Adoption (Institutionalized in Community)';
            document.getElementById('findingsSummaryText').value = existingMeta.findings_summary || '';
            document.getElementById('recommendationsText').value = existingMeta.recommendations || '';
            document.getElementById('reportUrlInput').value = existingMeta.report_url || '';
        } else {
            document.getElementById('recordImpactForm').reset();
            document.getElementById('impactProposalId').value = proposalId;
            document.getElementById('impactModalProposalTitle').textContent = proposalTitle;
            document.getElementById('assessmentDateInput').value = '{{ date('Y-m-d') }}';
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('recordImpactModal'));
        modal.show();
    }

    async function submitImpactAssessment(event) {
        event.preventDefault();
        const btn = document.getElementById('submitImpactBtn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

        const proposalId = document.getElementById('impactProposalId').value;
        const payload = {
            assessment_date: document.getElementById('assessmentDateInput').value,
            evaluator_name: document.getElementById('evaluatorNameInput').value,
            sustainability_rating: document.getElementById('sustainabilityRatingSelect').value,
            community_adoption_level: document.getElementById('adoptionLevelSelect').value,
            findings_summary: document.getElementById('findingsSummaryText').value,
            recommendations: document.getElementById('recommendationsText').value,
            report_url: document.getElementById('reportUrlInput').value,
        };

        try {
            const url = `{{ url('/dss/rie/unit-monitoring/proposals') }}/${proposalId}/impact-assessment`;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Impact Assessment Recorded',
                    text: data.message || 'Official 3-year impact survey successfully documented.',
                    confirmButtonColor: '#00674f'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to save impact assessment.');
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message || 'Unable to record impact assessment.',
                confirmButtonColor: '#dc2626'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
</script>
@endpush
@endsection
