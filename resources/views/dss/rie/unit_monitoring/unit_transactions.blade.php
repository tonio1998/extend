@extends('layouts.dss.app')

@section('title', $unit->unit_name . ' — Extension Projects, Plans & Transactions | SNSU RIE System')

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
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $unit->unit_name }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.rie.unit_monitoring.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Back to All Units</span>
            </a>
            <a href="{{ route('dss.documents.repository', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-shield-folder-outline text-primary"></i>
                <span>Unit Documents Vault</span>
            </a>
            <a href="{{ route('dss.rie.calendar.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1">
                <i class="mdi mdi-calendar-range text-success"></i>
                <span>Extension Calendar</span>
            </a>
        </div>
    </div>

    {{-- Executive Unit Hero Header --}}
    <div class="card border rounded-3 p-4 mb-3 shadow-2xs bg-white">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-domain me-1"></i>ACADEMIC UNIT &bull; {{ $unit->unit_code ?: 'UNIT #'.$unit->id }}
                    </span>
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-map-marker text-success me-0.5"></i>{{ $unit->campus?->campus_name ?: 'Main Campus' }}
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1 extra-small">
                        <i class="mdi mdi-account-tie me-0.5"></i>Head: {{ $headName }}
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    {{ $unit->unit_name }}
                </h3>
                <p class="text-muted extra-small mb-3" style="max-width: 780px; line-height: 1.6;">
                    Dedicated institutional ledger for <strong>{{ $unit->unit_name }}</strong> tracking community extension proposals, operational plans, repository documents, and mandatory 3-year post-commencement impact evaluations.
                </p>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi mdi-account-group text-primary me-1"></i>{{ $projectLeadersCount }} Active Project Proponent(s)
                    </span>
                    <span class="badge bg-light text-dark border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi mdi-file-document-outline text-success me-1"></i>{{ $documents->count() }} Centralized Files Logged
                    </span>
                    <span class="badge {{ $impactDueCount > 0 ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-success-subtle text-success border-success-subtle' }} border font-mono px-2.5 py-1 extra-small">
                        <i class="mdi {{ $impactDueCount > 0 ? 'mdi-alert-circle' : 'mdi-check-circle' }} me-1"></i>
                        {{ $impactDueCount > 0 ? $impactDueCount.' 3-Yr Impact Survey Due' : 'All 3-Yr Assessments Up to Date' }}
                    </span>
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="p-3 bg-light rounded-3 border text-start d-inline-block shadow-2xs" style="min-width: 270px;">
                    <div class="extra-small text-uppercase fw-bold text-muted font-mono mb-1">Cumulative Budget Allocation</div>
                    <div class="fs-3 fw-bold text-dark font-mono mb-2">
                        ₱{{ number_format($totalBudget, 2) }}
                    </div>
                    <div class="progress mb-2" style="height: 6px; background: #e2e8f0;">
                        @php
                            $totalProps = $proposalsWithImpact->count();
                            $completionRate = $totalProps > 0 ? round(($completedCount / $totalProps) * 100, 1) : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $completionRate) }}%;"></div>
                    </div>
                    <div class="extra-small text-muted font-mono">
                        {{ $completionRate }}% completed initiatives &bull; {{ $activeCount }} active
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Executive 4-Col Stat Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Total Proposals</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">
                        <i class="mdi mdi-lightbulb-on-outline"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">{{ $proposalsWithImpact->count() }}</div>
                <div class="extra-small text-muted">
                    <span class="text-primary fw-semibold">{{ $activeCount }} ongoing</span> &bull; <span>{{ $completedCount }} terminal</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Approved Budget</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle font-mono extra-small">
                        <i class="mdi mdi-cash-multiple"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">₱{{ number_format($totalBudget, 2) }}</div>
                <div class="extra-small text-muted">
                    Institutional &amp; external funding committed
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">Operational Plans</span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-mono extra-small">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark font-mono mb-1">{{ $plans->count() }}</div>
                <div class="extra-small text-muted">
                    Approved extension program blueprints
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-2xs h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="extra-small text-muted text-uppercase fw-bold font-mono">3-Yr Impact Due</span>
                    <span class="badge {{ $impactDueCount > 0 ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-success-subtle text-success border-success-subtle' }} border font-mono extra-small">
                        <i class="mdi mdi-clock-alert-outline"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold {{ $impactDueCount > 0 ? 'text-danger' : 'text-dark' }} font-mono mb-1">
                    {{ $impactDueCount }}
                </div>
                <div class="extra-small">
                    @if($impactDueCount > 0)
                        <span class="text-danger fw-bold"><i class="mdi mdi-alert-circle me-0.5"></i>Requires 3-Year Post-Impact Survey</span>
                    @else
                        <span class="text-success fw-semibold"><i class="mdi mdi-check-circle me-0.5"></i>{{ $impactAssessedCount }} Evaluated / Up to date</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Tabs Container: Proposals, Plans, Repository Documents, Impact Ledger --}}
    <div class="card border rounded-3 bg-white shadow-2xs mb-4">
        {{-- Nav Tabs Navigation --}}
        <div class="px-3 pt-2 border-bottom bg-light bg-opacity-25">
            <ul class="nav nav-tabs nav-tabs-custom border-0" id="unitTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="proposals-tab" data-bs-toggle="tab" data-bs-target="#proposals-pane" type="button" role="tab" aria-controls="proposals-pane" aria-selected="true">
                        <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                        <span>Extension Proposals &amp; Projects</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $proposalsWithImpact->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="plans-tab" data-bs-toggle="tab" data-bs-target="#plans-pane" type="button" role="tab" aria-controls="plans-pane" aria-selected="false">
                        <i class="mdi mdi-clipboard-text-outline me-1"></i>
                        <span>Extension Plans</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $plans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab" aria-controls="docs-pane" aria-selected="false">
                        <i class="mdi mdi-file-document-multiple-outline me-1"></i>
                        <span>Repository Documents &amp; Evidence</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1 font-mono">{{ $documents->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="impact-tab" data-bs-toggle="tab" data-bs-target="#impact-pane" type="button" role="tab" aria-controls="impact-pane" aria-selected="false">
                        <i class="mdi mdi-chart-timeline-variant me-1"></i>
                        <span>3-Year Post-Commencement Impact Pipeline</span>
                        @if($impactDueCount > 0)
                            <span class="badge rounded-pill bg-danger text-white ms-1 font-mono">{{ $impactDueCount }} Due</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="unitTabsContent">

                {{-- ====================================================================
                     TAB 1: EXTENSION PROPOSALS & PROJECTS MASTERLIST
                     ==================================================================== --}}
                <div class="tab-pane fade show active" id="proposals-pane" role="tabpanel" aria-labelledby="proposals-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Extension Proposals &amp; Active Projects</h6>
                            <p class="text-muted extra-small mb-0">Full catalog of projects submitted or executed under {{ $unit->unit_name }}.</p>
                        </div>
                        <span class="badge bg-light text-muted border font-mono px-3 py-1 extra-small">
                            Showing {{ $proposalsWithImpact->count() }} Proposal(s)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="unitProposalsTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 140px;" class="text-center">Action</th>
                                    <th style="min-width: 240px;">Proposal Title &amp; Code</th>
                                    <th style="width: 170px;">Project Leader</th>
                                    <th style="width: 130px;">Commencement</th>
                                    <th style="width: 110px;">Elapsed</th>
                                    <th style="width: 140px;">Approved Budget</th>
                                    <th style="width: 140px;" class="text-center">3-Yr Impact Status</th>
                                    <th style="width: 110px;" class="text-center">Workflow</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proposalsWithImpact as $item)
                                    @php
                                        $prop = $item['proposal'];
                                        $badge = $item['impact_badge'];
                                    @endphp
                                    <tr class="{{ $item['impact_status'] === 'due' ? 'table-danger bg-opacity-10' : '' }}">
                                        {{-- 1st Column: Actions --}}
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View Full Proposal Document">
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

                                        {{-- 2nd Column: Proposal Title & Code --}}
                                        <td>
                                            <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 380px;">
                                                {{ $prop->title }}
                                            </a>
                                            <div class="d-flex align-items-center gap-1 extra-small font-mono text-muted mt-0.5">
                                                <span class="badge bg-dark text-white">{{ $prop->proposal_code }}</span>
                                                @if($prop->extensionPlan)
                                                    <span>&bull; Plan: {{ $prop->extensionPlan->plan_code }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 3rd Column: Project Leader --}}
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $prop->project_leader_name ?: 'Unassigned Proponent' }}</div>
                                            <div class="extra-small text-muted font-mono">{{ $prop->lead_implementer_type ?: 'Faculty Proponent' }}</div>
                                        </td>

                                        {{-- 4th Column: Commencement --}}
                                        <td>
                                            @if($item['commencement_date'])
                                                <span class="font-mono extra-small text-dark">{{ $item['commencement_date']->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted extra-small">TBD</span>
                                            @endif
                                        </td>

                                        {{-- 5th Column: Elapsed --}}
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono extra-small">
                                                {{ $item['elapsed_years'] }} yrs
                                            </span>
                                        </td>

                                        {{-- 6th Column: Approved Budget --}}
                                        <td>
                                            <span class="font-mono fw-bold text-dark">
                                                ₱{{ number_format((float) $prop->approved_budget, 2) }}
                                            </span>
                                        </td>

                                        {{-- 7th Column: 3-Yr Impact Status --}}
                                        <td class="text-center">
                                            <span class="badge {{ $badge['class'] }} font-mono extra-small">
                                                <i class="mdi {{ $badge['icon'] }} me-0.5"></i>
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>

                                        {{-- 8th Column: Workflow Status --}}
                                        <td class="text-center">
                                            @php
                                                $statusColor = match($prop->status) {
                                                    'approved' => 'bg-success text-white',
                                                    'submitted', 'reviewed', 'noted', 'concurred', 'recommended' => 'bg-primary text-white',
                                                    'completed', 'terminal' => 'bg-info text-white',
                                                    'draft' => 'bg-secondary text-white',
                                                    default => 'bg-light text-dark border',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusColor }} font-mono text-uppercase extra-small">
                                                {{ $prop->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted extra-small">
                                            <i class="mdi mdi-information-outline fs-5 d-block mb-1 text-secondary"></i>
                                            No extension proposals found for this unit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ====================================================================
                     TAB 2: OPERATIONAL PLANS & BLUEPRINTS
                     ==================================================================== --}}
                <div class="tab-pane fade" id="plans-pane" role="tabpanel" aria-labelledby="plans-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Extension Plans &amp; Program Frameworks</h6>
                            <p class="text-muted extra-small mb-0">Pre-proposal institutional master plans prepared for or under {{ $unit->unit_name }}.</p>
                        </div>
                        <span class="badge bg-light text-muted border font-mono px-3 py-1 extra-small">
                            {{ $plans->count() }} Plan(s)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="unitPlansTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 100px;" class="text-center">Action</th>
                                    <th style="width: 140px;">Plan Code</th>
                                    <th style="min-width: 220px;">Program Name</th>
                                    <th style="width: 180px;">Prepared By</th>
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
                                        <td>
                                            <span class="badge bg-dark text-white font-mono">{{ $p->plan_code }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('dss.rie.plans.show', $p->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $p->program_name }}
                                            </a>
                                            @if($p->academicProgram)
                                                <div class="extra-small text-muted font-mono">{{ $p->academicProgram->program_name }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $p->prepared_by_name ?: 'Extension Coordinator' }}</div>
                                            <div class="extra-small text-muted font-mono">{{ $p->prepared_by_title ?: 'Proponent' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border font-mono">
                                                {{ $p->activities->count() }} Activities
                                            </span>
                                        </td>
                                        <td class="text-center font-mono extra-small">
                                            FY {{ $p->fiscal_year ?: ($p->created_at ? $p->created_at->format('Y') : date('Y')) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $p->status === 'approved' ? 'bg-success text-white' : 'bg-primary-subtle text-primary border border-primary-subtle' }} font-mono text-uppercase extra-small">
                                                {{ $p->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted extra-small">
                                            <i class="mdi mdi-information-outline fs-5 d-block mb-1 text-secondary"></i>
                                            No extension plans recorded under this unit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ====================================================================
                     TAB 3: REPOSITORY DOCUMENTS & EVIDENCE VAULT
                     ==================================================================== --}}
                <div class="tab-pane fade" id="docs-pane" role="tabpanel" aria-labelledby="docs-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Centralized Repository Documents &amp; Evidence</h6>
                            <p class="text-muted extra-small mb-0">Attached milestone evidence, signed scans, and compliance documents for {{ $unit->unit_name }}.</p>
                        </div>
                        <a href="{{ route('dss.documents.repository', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 extra-small fw-semibold">
                            <i class="mdi mdi-open-in-new me-1"></i>Open Full Vault Explorer
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="unitDocsTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 100px;" class="text-center">Action</th>
                                    <th style="min-width: 240px;">Document Title &amp; Filename</th>
                                    <th style="width: 140px;">Category</th>
                                    <th style="width: 120px;">SHA-256 Hash</th>
                                    <th style="width: 110px;">Size</th>
                                    <th style="width: 160px;">Uploaded By</th>
                                    <th style="width: 120px;">Date Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $doc)
                                    <tr>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('dss.documents.upload.preview', $doc->id) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="View Document in Viewer">
                                                    <i class="mdi mdi-eye-outline text-primary"></i>
                                                </a>
                                                <a href="{{ route('dss.documents.upload.download', $doc->id) }}" class="btn btn-sm btn-light border px-2 py-1 extra-small" title="Download Original">
                                                    <i class="mdi mdi-download text-success"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('dss.documents.upload.preview', $doc->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 320px;">
                                                {{ $doc->title }}
                                            </a>
                                            <div class="extra-small text-muted font-mono text-truncate" style="max-width: 320px;">
                                                {{ $doc->original_filename }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-mono extra-small">
                                                {{ str_replace('_', ' ', $doc->category) }}
                                            </span>
                                            @if($doc->is_physically_signed)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small ms-1">Wet-Signed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->sha256_hash)
                                                <span class="badge bg-light text-secondary border font-mono extra-small" title="{{ $doc->sha256_hash }}">
                                                    {{ substr($doc->sha256_hash, 0, 8) }}...
                                                </span>
                                            @else
                                                <span class="text-muted extra-small font-mono">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-mono extra-small text-muted">
                                                {{ $doc->formatted_file_size }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark extra-small">{{ $doc->uploader_name ?: ($doc->uploader?->name ?: 'System User') }}</span>
                                        </td>
                                        <td>
                                            <span class="font-mono extra-small text-muted">
                                                {{ $doc->created_at ? $doc->created_at->format('M d, Y') : '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted extra-small">
                                            <i class="mdi mdi-shield-folder-outline fs-5 d-block mb-1 text-secondary"></i>
                                            No repository documents found for this unit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ====================================================================
                     TAB 4: 3-YEAR POST-COMMENCEMENT IMPACT EVALUATION LEDGER
                     ==================================================================== --}}
                <div class="tab-pane fade" id="impact-pane" role="tabpanel" aria-labelledby="impact-tab">
                    <div class="alert alert-success border-success-subtle bg-success-subtle bg-opacity-25 rounded-3 p-3 mb-3 d-flex align-items-start gap-2.5">
                        <i class="mdi mdi-information-outline fs-5 text-success flex-shrink-0 mt-0.5"></i>
                        <div class="extra-small text-dark">
                            <strong>Mandatory Institutional Policy:</strong> Impact assessments are conducted <strong>3 years (36 months) after the commencement of the project</strong>. Projects meeting or exceeding this threshold require documented field verification, community adoption evaluations, and sustainability audits.
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0 w-100" id="unitImpactTable" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr class="text-uppercase extra-small text-muted fw-bold">
                                    {{-- RULE #8: Action column is ALWAYS the 1st column --}}
                                    <th style="width: 140px;" class="text-center">Action</th>
                                    <th style="min-width: 220px;">Initiative / Proposal</th>
                                    <th style="width: 140px;">Project Leader</th>
                                    <th style="width: 130px;">Commencement</th>
                                    <th style="width: 140px;">3-Yr Impact Due</th>
                                    <th style="width: 150px;" class="text-center">Status</th>
                                    <th style="min-width: 180px;">Evaluator &amp; Findings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proposalsWithImpact as $item)
                                    @php
                                        $prop = $item['proposal'];
                                        $badge = $item['impact_badge'];
                                        $meta = $item['assessment_meta'];
                                    @endphp
                                    <tr class="{{ $item['impact_status'] === 'due' ? 'table-danger bg-opacity-10' : '' }}">
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm {{ $item['impact_status'] === 'due' ? 'btn-danger text-white' : ($item['is_assessed'] ? 'btn-outline-success' : 'btn-light border') }} px-2.5 py-1 extra-small fw-semibold shadow-2xs"
                                                    onclick="openRecordImpactModal({{ $prop->id }}, {{ json_encode($prop->proposal_code . ' — ' . $prop->title) }}, {{ json_encode($meta) }})">
                                                <i class="mdi {{ $item['is_assessed'] ? 'mdi-check-decagram' : 'mdi-clipboard-edit-outline' }} me-0.5"></i>
                                                <span>{{ $item['is_assessed'] ? 'Update' : 'Record Survey' }}</span>
                                            </button>
                                        </td>
                                        <td>
                                            <a href="{{ route('dss.rie.proposals.show', $prop->getRouteKey()) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $prop->title }}
                                            </a>
                                            <div class="extra-small font-mono text-muted mt-0.5">{{ $prop->proposal_code }}</div>
                                        </td>
                                        <td>
                                            <span class="text-dark extra-small fw-semibold">{{ $prop->project_leader_name }}</span>
                                        </td>
                                        <td class="font-mono extra-small">
                                            {{ $item['commencement_date'] ? $item['commencement_date']->format('M d, Y') : 'TBD' }}
                                        </td>
                                        <td class="font-mono extra-small">
                                            {{ $item['impact_due_date'] ? $item['impact_due_date']->format('M d, Y') : 'TBD' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badge['class'] }} font-mono extra-small">
                                                <i class="mdi {{ $badge['icon'] }} me-0.5"></i>
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item['is_assessed'] && !empty($meta))
                                                <div class="fw-semibold text-dark extra-small">{{ $meta['evaluator_name'] ?? 'Official Auditor' }}</div>
                                                <div class="extra-small text-muted font-mono">
                                                    {{ $meta['conducted_at'] ?? 'Conducted' }} &bull; Rating: <strong class="text-success">{{ $meta['sustainability_rating'] ?? 'Satisfactory' }}</strong>
                                                </div>
                                            @else
                                                <span class="text-muted extra-small">No assessment recorded yet</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted extra-small">
                                            <i class="mdi mdi-information-outline fs-5 d-block mb-1 text-secondary"></i>
                                            No impact pipeline entries for this unit.
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

{{-- Record Impact Assessment Modal (Reused for quick assessment recording) --}}
<div class="modal fade" id="recordImpactModal" tabindex="-1" aria-labelledby="recordImpactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom py-3 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-clipboard-check-outline fs-6"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="recordImpactModalLabel">Record 3-Year Post-Commencement Impact Assessment</h5>
                        <p class="text-muted extra-small mb-0">Official institutional impact evaluation pursuant to RIE QA guidelines.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recordImpactForm" onsubmit="submitImpactAssessment(event)">
                <input type="hidden" id="impactProposalId" name="proposal_id">
                <div class="modal-body p-4">
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <div class="extra-small text-muted font-mono text-uppercase fw-bold">Extension Project Under Evaluation:</div>
                        <div class="fw-bold text-dark fs-6" id="modalProposalTitle">—</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Assessment / Survey Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="assessmentDateInput" name="assessment_date" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Evaluator / Auditor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="evaluatorNameInput" name="evaluator_name" placeholder="Name of Evaluator / Auditor" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Sustainability Rating</label>
                            <select class="form-select form-select-sm" id="sustainabilityRatingSelect" name="sustainability_rating">
                                <option value="Highly Sustainable">Highly Sustainable (Exemplary Outcomes)</option>
                                <option value="Satisfactory" selected>Satisfactory (Targets Sustained)</option>
                                <option value="Partially Sustained">Partially Sustained (Needs Intervention)</option>
                                <option value="Non-Sustained">Non-Sustained (Terminal Dropoff)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Community Adoption Level</label>
                            <select class="form-select form-select-sm" id="adoptionLevelSelect" name="community_adoption_level">
                                <option value="High Adoption" selected>High Adoption (Full Community Integration)</option>
                                <option value="Moderate Adoption">Moderate Adoption (Continuing Practice)</option>
                                <option value="Low Adoption">Low Adoption (Limited Continuation)</option>
                                <option value="Discontinued">Discontinued by Beneficiaries</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Evaluation Findings &amp; Socio-Economic Outcomes</label>
                            <textarea class="form-control form-control-sm" id="findingsSummaryText" name="findings_summary" rows="3" placeholder="Summary of impact survey observations, beneficiary livelihoods, skills retained, etc."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Recommendations &amp; Continuance Plan</label>
                            <textarea class="form-control form-control-sm" id="recommendationsText" name="recommendations" rows="2" placeholder="Policy recommendations, institutional support, scale-up proposals."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Official Impact Assessment Report / Document Link</label>
                            <input type="url" class="form-control form-control-sm" id="reportUrlInput" name="report_url" placeholder="https://drive.google.com/... or cloud document link">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success px-3 shadow-2xs fw-bold" id="submitImpactBtn">
                        <i class="mdi mdi-check-circle me-1"></i>Save Impact Assessment Status
                    </button>
                </div>
            </form>
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
        $('#unitProposalsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[3, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search unit proposals..."
            }
        });

        $('#unitPlansTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search unit plans..."
            }
        });

        $('#unitDocsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[6, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search repository documents..."
            }
        });

        $('#unitImpactTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[4, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search impact ledger..."
            }
        });
    });

    function openRecordImpactModal(proposalId, proposalTitle, meta) {
        document.getElementById('impactProposalId').value = proposalId;
        document.getElementById('modalProposalTitle').textContent = proposalTitle;

        if (meta) {
            document.getElementById('assessmentDateInput').value = meta.conducted_at || '{{ date('Y-m-d') }}';
            document.getElementById('evaluatorNameInput').value = meta.evaluator_name || '';
            document.getElementById('sustainabilityRatingSelect').value = meta.sustainability_rating || 'Satisfactory';
            document.getElementById('adoptionLevelSelect').value = meta.community_adoption_level || 'High Adoption';
            document.getElementById('findingsSummaryText').value = meta.findings_summary || '';
            document.getElementById('recommendationsText').value = meta.recommendations || '';
            document.getElementById('reportUrlInput').value = meta.report_url || '';
        } else {
            document.getElementById('recordImpactForm').reset();
            document.getElementById('impactProposalId').value = proposalId;
            document.getElementById('modalProposalTitle').textContent = proposalTitle;
            document.getElementById('assessmentDateInput').value = '{{ date('Y-m-d') }}';
            document.getElementById('evaluatorNameInput').value = '{{ auth()->user()->name ?? '' }}';
        }

        const modal = new bootstrap.Modal(document.getElementById('recordImpactModal'));
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
