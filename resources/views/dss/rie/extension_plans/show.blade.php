@extends('layouts.dss.app')

@section('title', 'Extension Plan: ' . $plan->plan_code . ' — RIE System')

@push('styles')
<style>
    /* Canva Header Card */
    .canva-hero-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
    }

    /* Tabs with Links Styling */
    .rie-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 6px;
    }
    .rie-nav-tabs .nav-link {
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        padding: 10px 16px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .rie-nav-tabs .nav-link:hover {
        color: var(--dss-primary, #002b49);
        border-bottom-color: #cbd5e1;
        background: transparent;
    }
    .rie-nav-tabs .nav-link.active {
        color: var(--dss-primary, #002b49);
        border-bottom: 2px solid var(--dss-primary, #002b49);
        background: transparent;
        font-weight: 700;
    }

    /* Milestone Schedule Table */
    .schedule-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    .schedule-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .schedule-table tr:last-child td {
        border-bottom: none;
    }
    .schedule-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Vertical DTS Timeline */
    .timeline-track {
        position: relative;
        padding-left: 28px;
    }
    .timeline-track::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-step-item {
        position: relative;
        padding-bottom: 18px;
    }
    .timeline-step-item:last-child {
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -28px;
        top: 2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        color: #64748b;
    }
    .timeline-step-item.completed .timeline-dot {
        border-color: #22c55e;
        background: #22c55e;
        color: #ffffff;
    }
    .timeline-step-item.in_transit .timeline-dot,
    .timeline-step-item.in_progress .timeline-dot {
        border-color: #f59e0b;
        background: #fef3c7;
        color: #b45309;
    }

    /* Print Overrides */
    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .no-print, .dss-navbar, .dss-sidebar, .dss-footer {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- 1. Executive Top Header & Toolbar -->
    <div class="canva-hero-box mb-4 no-print">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb m-0 extra-small font-mono">
                        <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">DSS</a></li>
                        <li class="breadcrumb-item"><span class="text-primary fw-semibold">RIE System</span></li>
                        <li class="breadcrumb-item"><a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">Extension Plans</a></li>
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $plan->plan_code }}</li>
                    </ol>
                </nav>

                <!-- Document Title & Status Pill -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="fw-bold text-dark m-0 fs-5">{{ $plan->extension_activity }}</h4>
                    <span class="badge {{ $plan->status_badge['class'] }} rounded-pill extra-small px-2 py-1">
                        <i class="mdi {{ $plan->status_badge['icon'] }} me-1"></i>
                        {{ $plan->status_badge['label'] }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill extra-small font-mono">
                        {{ $plan->plan_code }}
                    </span>
                </div>
            </div>

                <!-- Action Toolbar Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if($plan->canBeEditedBy(auth()->user()))
                        <a href="{{ route('dss.rie.plans.edit', $plan->getRouteKey()) }}"
                           class="btn btn-sm btn-outline-warning rounded-pill px-3 shadow-2xs fw-bold text-dark d-inline-flex align-items-center gap-1"
                           title="Edit Plan Details">
                            <i class="mdi mdi-pencil-outline"></i>
                            <span>Edit Plan</span>
                        </a>
                    @endif

                    @if($plan->status === 'draft' && ($plan->prepared_by_user_id === auth()->id() || auth()->user()->isSuperAdmin() || auth()->user()->isTenantAdmin()))
                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 shadow-2xs fw-bold text-dark d-inline-flex align-items-center gap-1"
                                id="btnSubmitPlanForReview" onclick="submitPlanForReview()">
                            <i class="mdi mdi-send-check"></i>
                            <span>Submit for Notation</span>
                        </button>
                    @endif

                    @if($plan->canCreateProposal())
                        @if($plan->extensionProposal)
                            <a href="{{ route('dss.rie.proposals.show', $plan->extensionProposal->getRouteKey()) }}"
                               class="btn btn-sm btn-success rounded-pill px-3 shadow-2xs fw-bold d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-file-document-check-outline"></i>
                                <span>View Extension Proposal</span>
                            </a>
                        @else
                            @can('rie.proposals.create')
                            <a href="{{ route('dss.rie.proposals.create', ['plan_id' => $plan->getRouteKey()]) }}"
                               class="btn btn-sm btn-dss-primary rounded-pill px-3 shadow-2xs fw-bold d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-plus-circle-outline"></i>
                                <span>Create Extension Proposal</span>
                            </a>
                            @endcan
                        @endif
                    @else
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 text-muted extra-small"
                                onclick="alert('Notice: Creating an Extension Proposal requires an officially APPROVED Extension Plan. This plan is currently: {{ strtoupper($plan->status) }}.');"
                                title="Locked: Requires Dean\'s Official Approval">
                            <i class="mdi mdi-lock-outline me-1"></i> Proposal Locked
                        </button>
                    @endif

                    @if($plan->tracking_number)
                        @can('rie.tracking.manage')
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-2xs font-mono fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#modalConnectPlanTracking"
                                title="3rd-Party DTS Tracking Connected (Click to update)">
                            <i class="mdi mdi-barcode-scan me-1"></i> DTS: {{ $plan->tracking_number }}
                        </button>
                        @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 font-mono fs-7 d-inline-flex align-items-center gap-1">
                            <i class="mdi mdi-barcode-scan"></i> DTS: {{ $plan->tracking_number }}
                        </span>
                        @endcan
                        <a href="{{ route('dss.rie.plans.pdf', ['id' => $plan->getRouteKey(), 'download' => 1]) }}"
                           class="btn btn-sm btn-primary rounded-pill px-3 shadow-2xs fw-semibold"
                           title="Download Official PDF">
                            <i class="mdi mdi-download me-1"></i> Download PDF
                        </a>
                    @else
                        @can('rie.tracking.manage')
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 shadow-2xs fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#modalConnectPlanTracking"
                                title="Connect 3rd-Party Tracking Number">
                            <i class="mdi mdi-link-variant-plus me-1"></i> Connect Tracking #
                        </button>
                        @endcan
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-2xs fw-semibold"
                                onclick="confirmPrintWithoutTracking('{{ route('dss.rie.plans.pdf', ['id' => $plan->getRouteKey(), 'download' => 1]) }}')"
                                title="Download PDF">
                            <i class="mdi mdi-download me-1"></i> Download PDF
                        </button>
                    @endif

                <div class="dropdown">
                    <button class="btn btn-sm btn-light border rounded-pill px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Options">
                        <i class="mdi mdi-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm extra-small">
                        <li>
                            <a class="dropdown-item py-1" href="{{ route('dss.documents.view', ['type' => 'extension_plan', 'id' => $plan->id]) }}" target="_blank">
                                <i class="mdi mdi-open-in-new me-2 text-primary"></i> Fullscreen Viewer
                            </a>
                        </li>
                        <li>
                            <button class="dropdown-item py-1" type="button" onclick="window.print()">
                                <i class="mdi mdi-printer me-2 text-secondary"></i> Print Page
                            </button>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-1" href="{{ route('dss.rie.plans.index') }}">
                                <i class="mdi mdi-arrow-left me-2 text-muted"></i> Back to Masterlist
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Canva-Style KPI Metric Bento Cards (Top of Page) -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: Proposed Budget -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="canva-card p-3 h-100 border-accent-emerald">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Proposed Budget</span>
                    <span class="rounded-circle p-1 bg-success bg-opacity-10 text-success d-inline-flex">
                        <i class="mdi mdi-cash-multiple fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-success font-mono">{{ $plan->formatted_budget }}</div>
                <div class="extra-small text-muted">Estimated Project Allocation</div>
            </div>
        </div>

        <!-- Metric 2: Target Beneficiaries -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="canva-card p-3 h-100 border-accent-cyan">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Target Beneficiaries</span>
                    <span class="rounded-circle p-1 bg-info bg-opacity-10 text-info d-inline-flex">
                        <i class="mdi mdi-account-group fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-primary font-mono">{{ number_format((int)$plan->target_beneficiaries_count) }}</div>
                <div class="extra-small text-muted">Community &amp; Stakeholder Members</div>
            </div>
        </div>

        <!-- Metric 3: Extensionists -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="canva-card p-3 h-100 border-accent-purple">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Extensionists</span>
                    <span class="rounded-circle p-1 bg-primary bg-opacity-10 text-primary d-inline-flex">
                        <i class="mdi mdi-account-tie fs-6"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold font-mono" style="color: #7d2ae8;">{{ $plan->extensionists_count }}</div>
                <div class="extra-small text-muted">Faculty &amp; Project Personnel</div>
            </div>
        </div>

        <!-- Metric 4: Project Leader -->
        <div class="col-xl col-md-6 col-sm-6">
            <div class="canva-card p-3 h-100 border-accent-amber">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Project Leader</span>
                    <span class="rounded-circle p-1 bg-warning bg-opacity-10 text-warning d-inline-flex">
                        <i class="mdi mdi-school fs-6"></i>
                    </span>
                </div>
                <div class="fs-5 fw-bold text-dark text-truncate" title="{{ $plan->prepared_by_name }}">{{ $plan->prepared_by_name }}</div>
                <div class="extra-small text-muted text-truncate">{{ $plan->academicProgram?->program_code ?? 'Project Lead' }}</div>
            </div>
        </div>

        <!-- Metric 5: Extension Lifecycle Progress -->
        <div class="col-xl col-md-6 col-sm-12">
            <div class="canva-card p-3 h-100 border-accent-purple position-relative">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">Project Progress</span>
                    <span class="badge rounded-pill bg-{{ $plan->progress_color }}-subtle text-{{ $plan->progress_color }} extra-small fw-bold px-2 py-0.5">
                        {{ $plan->progress_status }}
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-4 fw-bold font-mono text-{{ $plan->progress_color }}">{{ $plan->progress_percentage }}%</span>
                    <span class="extra-small text-muted font-mono">({{ $plan->completed_milestones_count }}/{{ $plan->active_milestones_count }} Done)</span>
                </div>
                <div class="progress mt-2" style="height: 6px; border-radius: 999px; background-color: #f1f5f9;">
                    <div class="progress-bar bg-{{ $plan->progress_color }}" role="progressbar" style="width: {{ $plan->progress_percentage }}%;" aria-valuenow="{{ $plan->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Navigation Links (Real Links, Not JS Pseudo-Tabs) -->
    <div class="canva-card p-0 mb-4 shadow-sm overflow-hidden">
        <div class="bg-light bg-opacity-50 px-3 pt-2 border-bottom">
            <ul class="nav rie-nav-tabs" id="planTabLinks">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'overview']) }}">
                        <i class="mdi mdi-text-box-search-outline me-1"></i> Scope &amp; Details
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'objectives' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'objectives']) }}">
                        <i class="mdi mdi-target-variant me-1"></i> Objectives &amp; Outcomes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'milestones' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'milestones']) }}">
                        <i class="mdi mdi-calendar-clock me-1"></i> Activities &amp; Milestones
                        <span class="badge {{ $plan->progress_percentage >= 100 ? 'bg-success text-white' : 'bg-primary text-white' }} ms-1 font-mono">{{ $plan->completed_milestones_count }}/{{ $plan->active_milestones_count }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'routing' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'routing']) }}">
                        <i class="mdi mdi-routes me-1"></i> DTS Routing &amp; Audit
                        @if($plan->dtsRecord && $plan->dtsRecord->routes)
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 font-mono">{{ $plan->dtsRecord->routes->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'pdf' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'pdf']) }}">
                        <i class="mdi mdi-file-pdf-box me-1 text-danger"></i> Official PDF Document
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'attachments' ? 'active' : '' }}" href="{{ route('dss.rie.plans.show', ['id' => $plan->getRouteKey(), 'tab' => 'attachments']) }}">
                        <i class="mdi mdi-paperclip me-1 text-primary"></i> Attachments &amp; Signed Copies
                        <span class="badge {{ $plan->uploadedDocuments->isNotEmpty() ? 'bg-primary text-white' : 'bg-secondary-subtle text-secondary' }} ms-1 font-mono attachments-count-badge">{{ $plan->uploadedDocuments->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tab Content Panes -->
        <div class="tab-content p-4" id="planTabContent">

            <!-- Tab 1: Scope & Details -->
            <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : 'd-none' }}" id="overview">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="text-uppercase extra-small fw-bold text-muted mb-1" style="letter-spacing: 1px;">Academic Degree Program</div>
                        <div class="fw-bold text-dark fs-6">{{ $plan->program_name }}</div>
                        <div class="text-muted small mt-1">
                            <span class="badge bg-light text-muted border font-mono">{{ $plan->academicProgram?->program_code ?? 'RIE Program' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-uppercase extra-small fw-bold text-muted mb-1" style="letter-spacing: 1px;">Campus / Institution</div>
                        <div class="fw-bold text-dark fs-6">{{ $plan->campus?->campus_name ?? 'Main Campus' }}</div>
                        <div class="text-muted small mt-1">
                            <span>Created: <strong>{{ $plan->created_at->format('M d, Y') }}</strong></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-uppercase extra-small fw-bold text-muted mb-1" style="letter-spacing: 1px;">Extension Activity Title</div>
                        <div class="text-dark fw-bold fs-5">{{ $plan->extension_activity }}</div>
                    </div>
                    @if($plan->beneficiaries)
                        <div class="col-12">
                            <div class="p-3 rounded-2 bg-light border">
                                <span class="badge bg-primary-subtle text-primary extra-small fw-bold mb-1 d-inline-block">Beneficiary Profile</span>
                                <div class="text-dark small">{{ $plan->beneficiaries }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Dynamic 4-Tier College Signatory Showcase -->
                    <div class="col-12 mt-4">
                        @include('dss.rie.partials.signatories_panel', ['signatories' => $resolvedSignatories])
                    </div>
                </div>
            </div>

            <!-- Tab 2: Objectives & Outcomes -->
            <div class="tab-pane fade {{ $activeTab === 'objectives' ? 'show active' : 'd-none' }}" id="objectives">
                @php
                    $rawObjectives = trim($plan->objectives ?? '');
                    $objectiveLines = preg_split('/\r\n|\r|\n/', $rawObjectives);
                    $parsedObjectives = [];
                    $introText = null;

                    foreach ($objectiveLines as $line) {
                        $trimmed = trim($line);
                        if ($trimmed === '') continue;
                        if (preg_match('/^(\d+)[\.\)]\s*(.+)$/', $trimmed, $m)) {
                            $parsedObjectives[] = ['num' => $m[1], 'text' => $m[2]];
                        } elseif (empty($parsedObjectives)) {
                            $introText = ($introText ? $introText . ' ' : '') . $trimmed;
                        } else {
                            $parsedObjectives[] = ['num' => count($parsedObjectives) + 1, 'text' => $trimmed];
                        }
                    }
                @endphp

                @if($introText)
                    <div class="text-secondary small mb-3 ps-1">
                        {{ $introText }}
                    </div>
                @endif

                @if(count($parsedObjectives) > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($parsedObjectives as $obj)
                            <div class="d-flex align-items-start gap-3 p-3 rounded-2 border bg-white shadow-2xs">
                                <span class="badge rounded-circle bg-primary-subtle text-primary fw-bold extra-small d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 24px; height: 24px; margin-top: 1px;">
                                    {{ $obj['num'] }}
                                </span>
                                <div class="text-dark small fw-medium" style="line-height: 1.5;">
                                    {{ $obj['text'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-secondary small ps-1" style="white-space: pre-line; line-height: 1.6;">
                        {{ $plan->objectives }}
                    </div>
                @endif
            </div>

            <!-- Tab 3: Activities & Milestones -->
            <div class="tab-pane fade {{ $activeTab === 'milestones' ? 'show active' : 'd-none' }}" id="milestones">
                <!-- Milestone Execution & Progress Portfolio Banner -->
                <div class="card border rounded-3 bg-white shadow-2xs mb-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-7">
                                <div class="d-flex align-items-center gap-2 mb-1.5">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle extra-small fw-medium">
                                        Progress Tracker
                                    </span>
                                    <span class="badge rounded-pill bg-{{ $plan->progress_color }}-subtle text-{{ $plan->progress_color }} border border-{{ $plan->progress_color }}-subtle extra-small fw-semibold px-2 py-0.5" id="progressStatusBadge">
                                        {{ $plan->progress_status }}
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-5">
                                    Schedule of Activities &amp; Milestones
                                </h6>
                                <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                    Track stage progress and compile supporting wet-signed documents (e.g. approved Activity Design, partner MOA/MOU, attendance sheets, and terminal narrative reports) for institutional compliance.
                                </p>
                            </div>
                            <div class="col-lg-5">
                                <div class="p-3 rounded-2 bg-light border border-light-subtle">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                                        <span class="text-muted small fw-medium">Milestone Completion</span>
                                        <span class="fs-6 fw-bold text-{{ $plan->progress_color }}" id="progressPercentageText">{{ $plan->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px; border-radius: 999px; background-color: #e2e8f0;">
                                        <div class="progress-bar bg-{{ $plan->progress_color }}" id="progressBarTrack" role="progressbar" style="width: {{ $plan->progress_percentage }}%;" aria-valuenow="{{ $plan->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted extra-small">
                                        <span><i class="mdi mdi-check-circle-outline text-success me-1"></i><strong class="text-dark" id="progressVerifiedCount">{{ $plan->completed_milestones_count }}</strong> verified</span>
                                        <span><i class="mdi mdi-clock-outline text-warning me-1"></i><strong class="text-dark" id="progressPendingCount">{{ max(0, $plan->active_milestones_count - $plan->completed_milestones_count) }}</strong> pending</span>
                                        <span><strong class="text-dark">{{ $plan->active_milestones_count }}</strong> total milestones</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule of Activities Table -->
                <div class="table-responsive rounded-3 border">
                    <table class="table schedule-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center">#</th>
                                <th style="min-width: 250px;">Milestone Stage Description</th>
                                <th style="min-width: 170px;" class="text-center">Target Period / Date</th>
                                <th style="min-width: 280px;">Attached Evidence &amp; Documents</th>
                                <th style="width: 140px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="milestonesTableBody">
                            @php
                                $milestones = $plan->milestones_list;
                            @endphp

                            @foreach($milestones as $m)
                            <tr id="activity-row-{{ $m['id'] ?? $loop->iteration }}" class="{{ !empty($m['is_completed']) ? 'table-light-success' : '' }}">
                                <td class="text-center fw-bold text-muted">{{ $m['num'] }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $m['title'] }}</div>
                                    @if(!empty($m['remarks']))
                                        <div class="text-muted extra-small mt-0.5"><i class="mdi mdi-information-outline me-1"></i>{{ $m['remarks'] }}</div>
                                    @endif
                                </td>
                                <td class="text-center font-mono extra-small">
                                    @if(!empty($m['target_date']) && strtolower(trim($m['target_date'])) !== 'n/a')
                                        <div class="d-inline-flex align-items-center gap-1 px-2.5 py-1 rounded-pill bg-light border text-dark fw-bold">
                                            @if(str_contains($m['target_date'], '–') || str_contains($m['target_date'], '-') || str_contains($m['target_date'], 'to'))
                                                <i class="mdi mdi-calendar-range text-primary"></i>
                                            @else
                                                <i class="mdi mdi-calendar-check text-success"></i>
                                            @endif
                                            <span>{{ $m['target_date'] }}</span>
                                        </div>
                                    @elseif(!empty($m['is_na']) || strtolower(trim($m['target_date'] ?? '')) === 'n/a')
                                        <span class="badge bg-light text-muted border px-2 py-1"><i class="mdi mdi-minus-circle-outline me-1"></i>N/A</span>
                                    @else
                                        <span class="text-muted fst-italic">To be scheduled</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $docsList = $m['documents'] ?? [];
                                        if (empty($docsList) && (!empty($m['file_url']) || !empty($m['is_completed']))) {
                                            $docsList = [[
                                                'id' => 0,
                                                'file_name' => $m['file_name'] ?? 'Wet-Signed Document Proof',
                                                'file_url' => $m['file_url'],
                                                'preview_url' => $m['preview_url'],
                                                'formatted_file_size' => $m['formatted_file_size'] ?? '',
                                                'file_icon' => $m['file_icon'] ?? 'mdi-file-pdf-box text-danger',
                                                'uploaded_at' => $m['uploaded_at'] ?? '',
                                                'uploaded_by' => $m['uploaded_by'] ?? '',
                                            ]];
                                        }
                                    @endphp

                                    @if(!empty($m['is_na']))
                                        <span class="text-muted extra-small fst-italic"><i class="mdi mdi-cancel me-1"></i>Not applicable to this project</span>
                                    @elseif(count($docsList) > 0)
                                        <div class="d-flex flex-column gap-1.5">
                                            @foreach($docsList as $docItem)
                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border bg-white shadow-2xs">
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                                        <i class="mdi {{ $docItem['file_icon'] ?? 'mdi-file-pdf-box text-danger' }} fs-5 flex-shrink-0"></i>
                                                        <div class="overflow-hidden">
                                                            <a href="{{ $docItem['file_url'] }}" target="_blank" class="fw-semibold text-dark text-truncate d-block extra-small text-decoration-none" title="{{ $docItem['file_name'] }}">
                                                                {{ $docItem['file_name'] }}
                                                            </a>
                                                            <div class="text-muted extra-small font-mono d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 10px;">
                                                                @if(!empty($docItem['formatted_file_size']))
                                                                    <span>{{ $docItem['formatted_file_size'] }}</span>
                                                                @endif
                                                                @if(!empty($docItem['uploaded_at']))
                                                                    <span>&bull;</span>
                                                                    <span>{{ $docItem['uploaded_at'] }}</span>
                                                                @endif
                                                                @if(!empty($docItem['uploaded_by']))
                                                                    <span>&bull;</span>
                                                                    <span>by {{ $docItem['uploaded_by'] }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                        @if(!empty($docItem['file_url']))
                                                            <a href="{{ $docItem['file_url'] }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" title="Preview Document">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
                                                        @endif
                                                        @if($m['id'])
                                                            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-1.5 py-0.5 btn-delete-proof"
                                                                    data-id="{{ $m['id'] }}"
                                                                    data-doc-id="{{ $docItem['id'] }}"
                                                                    data-title="{{ $docItem['file_name'] }}"
                                                                    title="Remove this attachment">
                                                                <i class="mdi mdi-trash-can-outline"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                            @if($m['id'])
                                                <div class="mt-0.5">
                                                    <button type="button" class="btn btn-xs btn-light border text-primary rounded-pill px-2.5 py-1 extra-small fw-semibold btn-trigger-upload d-inline-flex align-items-center gap-1"
                                                            data-id="{{ $m['id'] }}"
                                                            data-title="{{ $m['title'] }}">
                                                        <i class="mdi mdi-plus-circle-outline"></i>
                                                        <span>Attach More Files / Links</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        @if($m['id'])
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-trigger-upload d-inline-flex align-items-center gap-1"
                                                    data-id="{{ $m['id'] }}"
                                                    data-title="{{ $m['title'] }}">
                                                <i class="mdi mdi-upload-outline"></i>
                                                <span>Upload Evidence</span>
                                            </button>
                                        @else
                                            <span class="text-muted extra-small fst-italic">Save plan to upload proof</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!empty($m['is_na']) || strtolower(trim($m['target_date'] ?? '')) === 'n/a')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary extra-small px-2 py-1">
                                            Exempted
                                        </span>
                                    @elseif(!empty($m['is_completed']))
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle extra-small px-2 py-1">
                                            <i class="mdi mdi-check-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small px-2 py-1">
                                            <i class="mdi mdi-clock-outline me-1"></i> Pending Proof
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 4: DTS Routing & Audit -->
            <div class="tab-pane fade {{ $activeTab === 'routing' ? 'show active' : 'd-none' }}" id="routing">
                <!-- SNSU CENTRAL DTS LIVE TRACKING INTEGRATION CARD -->
                <div class="card border rounded-3 shadow-2xs overflow-hidden mb-4 border-primary border-opacity-25">
                    <div class="card-header bg-primary bg-opacity-10 py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white font-mono extra-small"><i class="mdi mdi-cloud-sync me-1"></i>SNSU CENTRAL DTS (LIVE)</span>
                            <h6 class="fw-bold text-dark extra-small text-uppercase m-0">Campus-Wide Document Custody &amp; Routing Trail</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if(!empty($snsuTracking))
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                    <i class="mdi mdi-check-circle me-1"></i>Status: {{ $snsuTracking['trail_status'] ?? 'Active' }}
                                </span>
                            @endif
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" id="btnRefreshPlanLiveTracking" title="Refresh live status from SNSU DTS">
                                <i class="mdi mdi-refresh me-1"></i> Refresh Trail
                            </button>
                            @can('rie.tracking.manage')
                            <button type="button" class="btn btn-xs btn-primary rounded-pill px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#modalConnectPlanTracking">
                                <i class="mdi mdi-link-variant me-1"></i> {{ !empty($plan->tracking_number) ? 'Update Tracking #' : 'Connect Tracking #' }}
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if(!empty($snsuTracking))
                            <!-- Connected DTS Metadata Bar -->
                            <div class="row g-2 mb-3 pb-3 border-bottom">
                                <div class="col-md-3">
                                    <span class="text-muted extra-small d-block text-uppercase font-mono">Official Tracking Number</span>
                                    <span class="fw-bold font-mono text-primary fs-6">{{ $snsuTracking['tracking_number'] ?? $plan->tracking_number }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted extra-small d-block text-uppercase font-mono">Current Custodian / Office</span>
                                    <span class="fw-semibold text-dark fs-6">{{ $snsuTracking['current_location'] ?? 'Office of Origin' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted extra-small d-block text-uppercase font-mono">Intended Destination</span>
                                    <span class="fw-semibold text-dark fs-6">{{ $snsuTracking['destination'] ?? 'Reviewing Authority' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted extra-small d-block text-uppercase font-mono">System Document State</span>
                                    <span class="badge bg-light text-dark border font-mono extra-small">{{ strtoupper($snsuTracking['doc_status'] ?? 'Active') }}</span>
                                </div>
                            </div>

                            <!-- Live Chronological Trail Steps from UIS DTS -->
                            @if(!empty($snsuTracking['trail']) && count($snsuTracking['trail']) > 0)
                                <div class="text-uppercase extra-small fw-bold text-muted mb-2 font-mono" style="letter-spacing: 0.5px;">Live Central Routing History</div>
                                <div class="timeline-track">
                                    @foreach($snsuTracking['trail'] as $tStep)
                                        <div class="timeline-step-item completed">
                                            <div class="timeline-dot"><i class="mdi mdi-check"></i></div>
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-bold text-dark fs-6">
                                                    {{ $tStep['office'] ?? 'Office' }}
                                                </span>
                                                <span class="badge rounded-pill extra-small font-mono bg-success text-white">
                                                    {{ strtoupper($tStep['action'] ?? 'ROUTED') }}
                                                </span>
                                            </div>
                                            @if(!empty($tStep['remarks']))
                                                <div class="small text-muted mb-1">
                                                    Remarks: <em>{{ $tStep['remarks'] }}</em>
                                                </div>
                                            @endif
                                            <div class="extra-small text-muted font-mono d-flex align-items-center justify-content-between">
                                                <span>Transacted by: <strong>{{ $tStep['actor'] ?? 'System / Personnel' }}</strong></span>
                                                @if(!empty($tStep['timestamp']))
                                                    <span class="text-success"><i class="mdi mdi-clock-check-outline me-1"></i>{{ $tStep['timestamp'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info py-2 px-3 extra-small mb-0 rounded-2">
                                    <i class="mdi mdi-information-outline me-1"></i> Tracking record exists in SNSU Central DTS. Routing hops will appear as the document moves across offices.
                                </div>
                            @endif
                        @else
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-2">
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1">
                                        <i class="mdi mdi-link-variant-off text-muted me-1"></i> No Live SNSU Central DTS Record Connected
                                    </div>
                                    <div class="extra-small text-muted">
                                        Link this Extension Plan with an official SNSU Central Tracking Number (e.g. <code>SNSU-2026-00123</code>) to monitor live inter-office routing, notation custody, and physical document trail.
                                    </div>
                                </div>
                                @can('rie.tracking.manage')
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalConnectPlanTracking">
                                    <i class="mdi mdi-link-variant me-1"></i> Connect Tracking Number
                                </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Local DTS Records (If available in local DB) -->
                @if($plan->dtsRecord && Route::has('dss.dts.show'))
                    <div class="p-3 rounded-2 bg-light border mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold font-mono d-block mb-1">Local DTS Record</span>
                            <span class="fw-bold font-mono text-primary fs-5">{{ $plan->dtsRecord->DocumentID }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('dss.dts.show', dss_encrypt_id($plan->dtsRecord->id)) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                <i class="mdi mdi-radar me-1"></i> Open Local DTS View
                            </a>
                        </div>
                    </div>

                    @if($plan->dtsRecord->routes && $plan->dtsRecord->routes->isNotEmpty())
                        <div class="text-uppercase extra-small fw-bold text-muted mb-3" style="letter-spacing: 1px;">Local Route Steps</div>
                        <div class="timeline-track">
                            @foreach($plan->dtsRecord->routes->sortBy('step_number') as $route)
                                <div class="timeline-step-item {{ $route->status }}">
                                    <div class="timeline-dot">
                                        @if($route->status === 'completed')
                                            <i class="mdi mdi-check"></i>
                                        @else
                                            {{ $route->step_number }}
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-6">{{ $route->office_name }}</span>
                                        <span class="badge rounded-pill extra-small font-mono {{ $route->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ strtoupper(str_replace('_', ' ', $route->status)) }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mb-1">Action: <strong class="text-secondary">{{ $route->for_action }}</strong></div>
                                    <div class="extra-small text-muted font-mono d-flex align-items-center justify-content-between">
                                        <span>SLA: <strong>{{ $route->processing_time }}</strong></span>
                                        @if($route->completed_at)
                                            <span class="text-success"><i class="mdi mdi-check-all me-1"></i>Completed: {{ $route->completed_at->format('M d, Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <!-- Tab 5: Official PDF Document -->
            <div class="tab-pane fade {{ $activeTab === 'pdf' ? 'show active' : 'd-none' }}" id="pdf">
                <div class="border rounded-3 overflow-hidden" style="min-height: 850px;">
                    <div class="bg-dark text-white py-2 px-3 d-flex align-items-center justify-content-between">
                        <div class="extra-small font-mono d-flex align-items-center gap-2">
                            <i class="mdi mdi-file-pdf-box text-danger fs-5"></i>
                            <span>{{ $plan->plan_code }}.pdf — Official Layout</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('dss.rie.plans.pdf', ['id' => $plan->getRouteKey(), 'download' => 1]) }}" class="btn btn-sm btn-primary px-3 py-1 extra-small">
                                <i class="mdi mdi-download me-1"></i> Download
                            </a>
                            <a href="{{ route('dss.documents.view', ['type' => 'extension_plan', 'id' => $plan->id]) }}" class="btn btn-sm btn-outline-light px-3 py-1 extra-small" target="_blank">
                                <i class="mdi mdi-open-in-new me-1"></i> Fullscreen
                            </a>
                        </div>
                    </div>
                    <iframe id="pdfPreviewIframe" src="{{ $activeTab === 'pdf' ? route('dss.rie.plans.pdf', $plan->getRouteKey()) : '' }}" data-src="{{ route('dss.rie.plans.pdf', $plan->getRouteKey()) }}" style="width: 100%; height: 900px; border: none;"></iframe>
                </div>
            </div>

            <!-- Tab 6: Attachments & Physically Signed Copies (Centralized Reusable Component) -->
            <div class="tab-pane fade {{ $activeTab === 'attachments' ? 'show active' : 'd-none' }}" id="attachments">
                <x-dss.document-attachments :model="$plan" :auditTrails="$auditTrails" />
            </div>


        </div>
    </div>
</div>

@can('rie.tracking.manage')
<div class="modal fade" id="modalConnectPlanTracking" tabindex="-1" aria-labelledby="modalConnectPlanTrackingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white py-3">
                <h6 class="modal-title fw-bold" id="modalConnectPlanTrackingLabel">
                    <i class="mdi mdi-barcode-scan me-1"></i> Connect SNSU Central DTS Tracking Number
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="extra-small text-muted mb-3">
                    Input the official Document Tracking Number issued by the University Records / Central DTS Office for this Extension Plan:
                </p>
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-uppercase text-dark">Tracking Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono fw-bold fs-6" id="inputPlanTrackingNumber"
                           placeholder="e.g. SNSU-2026-00123"
                           value="{{ $plan->tracking_number ?? $plan->plan_code }}">
                </div>
                <div id="planTrackingFeedback" class="alert d-none extra-small py-2 mb-0"></div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnConnectPlanTrackingSubmit">
                    <i class="mdi mdi-link-variant me-1"></i> Verify &amp; Connect
                </button>
            </div>
        </div>
    </div>
</div>
@endcan

<!-- Upload Milestone Evidence Modal -->
<div class="modal fade" id="modalUploadMilestoneProof" tabindex="-1" aria-labelledby="modalUploadMilestoneProofLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <!-- Modal Header matching DSS Theme -->
            <div class="modal-header border-bottom py-3 px-4 bg-light align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-inline-flex">
                        <i class="mdi mdi-cloud-upload-outline fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 m-0" id="modalUploadMilestoneProofLabel">Upload Milestone Evidence</h5>
                        <p class="text-muted extra-small m-0">Attach supporting files or Google Drive links for milestone verification.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formUploadMilestoneProof" enctype="multipart/form-data">
                <input type="hidden" id="uploadMilestoneActivityId" name="activity_id" value="">
                <div class="modal-body p-4">
                    <!-- Milestone Stage Indicator -->
                    <div class="p-2.5 px-3 mb-3 rounded-3 d-flex align-items-center justify-content-between gap-2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-1.5 d-inline-flex text-white">
                                <i class="mdi mdi-flag-checkered" style="font-size: 11px;"></i>
                            </span>
                            <div class="extra-small">
                                <span class="fw-bold text-success-emphasis">Target Milestone:</span>
                                <strong class="text-dark font-mono ms-1" id="uploadMilestoneStageTitle">—</strong>
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono extra-small px-2 py-0.5">
                            Evidence Verification
                        </span>
                    </div>

                    <!-- 1. Physical Files Upload Dropzone -->
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-dark font-mono mb-1">
                            <i class="mdi mdi-file-pdf-box text-danger me-1"></i> Wet-Signed Evidence PDF (Multiple Supported)
                        </label>
                        <div class="milestone-dropzone p-3 text-center rounded-3 border"
                             id="proofDropzone"
                             style="background: #ffffff; border: 2px dashed #94a3b8 !important; cursor: pointer; transition: all 0.2s ease;">
                            <input class="d-none" type="file" id="proofFileInput" name="files[]" multiple accept=".pdf,application/pdf">
                            <div class="rounded-circle bg-white p-2 shadow-2xs mb-1.5 text-danger d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="mdi mdi-file-pdf-box fs-3"></i>
                            </div>
                            <div class="fw-bold text-dark extra-small mb-0.5">Click to browse or drag &amp; drop PDF files</div>
                            <div class="text-muted extra-small">
                                <strong class="text-danger">PDF format only (.pdf)</strong> &bull; Max 50MB per file
                            </div>
                        </div>
                        <div id="proofSelectedFilesBadges" class="d-none mt-2 d-flex flex-wrap gap-1"></div>
                    </div>

                    <!-- 2. Google Drive Links -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <label class="form-label extra-small fw-bold text-dark font-mono m-0">
                                <i class="mdi mdi-google-drive text-success me-1"></i> Google Drive Shareable Link(s)
                            </label>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-0.5 extra-small fw-bold" id="btnAddGdriveInput">
                                <i class="mdi mdi-plus me-0.5"></i> Add Link
                            </button>
                        </div>
                        <div id="gdriveInputsWrapper" class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm gdrive-entry-row">
                                <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                                <input type="url" class="form-control gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                            </div>
                        </div>
                        <div class="p-2 rounded-2 mt-2 extra-small text-muted d-flex align-items-center gap-1.5" style="background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.2);">
                            <i class="mdi mdi-information-outline text-primary fs-6"></i>
                            <span>Ensure Google Drive links are set to <strong>"Anyone with the link can view"</strong> for reviewer access.</span>
                        </div>
                    </div>

                    <!-- 3. Notes / Remarks -->
                    <div class="mb-2">
                        <label for="proofRemarksInput" class="form-label extra-small fw-bold text-muted font-mono mb-1">
                            Notes / Remarks <span class="text-muted fw-normal font-sans">(Optional)</span>
                        </label>
                        <textarea class="form-control form-control-sm" id="proofRemarksInput" name="remarks" rows="2" placeholder="e.g. Signed MOA with Barangay Officials, Approved Activity Design..."></textarea>
                    </div>

                    <div id="uploadFeedbackAlert" class="alert d-none extra-small mb-0 mt-3" role="alert"></div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <span class="text-muted extra-small">
                        <i class="mdi mdi-shield-check text-success me-0.5"></i> Auto-hashed (SHA-256) for audit integrity
                    </span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnSubmitMilestoneProof" class="btn btn-sm btn-dss-primary rounded-pill px-4 fw-bold shadow-xs">
                            <i class="mdi mdi-upload me-1"></i> Upload &amp; Verify Milestone
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dynamic GDrive input row addition
    $('#btnAddGdriveInput').on('click', function() {
        const rowHtml = `
            <div class="input-group input-group-sm gdrive-entry-row">
                <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                <input type="url" class="form-control gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                <button type="button" class="btn btn-outline-danger btn-remove-gdrive-row" title="Remove link row"><i class="mdi mdi-close"></i></button>
            </div>`;
        $('#gdriveInputsWrapper').append(rowHtml);
    });

    $(document).on('click', '.btn-remove-gdrive-row', function() {
        $(this).closest('.gdrive-entry-row').remove();
    });

    // Dropzone click trigger
    $('#proofDropzone').on('click', function(e) {
        if (!$(e.target).is('#proofFileInput')) {
            $('#proofFileInput').trigger('click');
        }
    });

    // Display selected file badges
    $('#proofFileInput').on('change', function() {
        const container = $('#proofSelectedFilesBadges');
        container.empty();
        if (this.files && this.files.length > 0) {
            const filesArr = Array.from(this.files);
            const nonPdf = filesArr.filter(f => !f.name.toLowerCase().endsWith('.pdf') && f.type !== 'application/pdf');
            if (nonPdf.length > 0) {
                alert('Only PDF files (.pdf) are accepted.');
                $(this).val('');
                container.addClass('d-none');
                return;
            }

            container.removeClass('d-none');
            filesArr.forEach(f => {
                const mb = (f.size / (1024 * 1024)).toFixed(2);
                container.append(`
                    <span class="badge bg-white text-dark border extra-small px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs">
                        <i class="mdi mdi-file-pdf-box text-danger fs-6"></i>
                        <span class="fw-medium text-truncate" style="max-width: 180px;">${f.name}</span>
                        <span class="text-muted">(${mb} MB)</span>
                    </span>`);
            });
        } else {
            container.addClass('d-none');
        }
    });

    // Milestone Document Upload Trigger
    $(document).on('click', '.btn-trigger-upload', function() {
        const actId = $(this).data('id');
        const actTitle = $(this).data('title');
        $('#uploadMilestoneActivityId').val(actId);
        $('#uploadMilestoneStageTitle').text(actTitle);
        $('#proofFileInput').val('');
        $('#proofSelectedFilesBadges').empty().addClass('d-none');
        $('#gdriveInputsWrapper').html(`
            <div class="input-group input-group-sm gdrive-entry-row">
                <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                <input type="url" class="form-control gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
            </div>`);
        $('#proofRemarksInput').val('');
        $('#uploadFeedbackAlert').addClass('d-none').removeClass('alert-success alert-danger').text('');
        const modal = new bootstrap.Modal(document.getElementById('modalUploadMilestoneProof'));
        modal.show();
    });

    $('#formUploadMilestoneProof').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const actId = $('#uploadMilestoneActivityId').val();
        const btn = $('#btnSubmitMilestoneProof');
        const feedback = $('#uploadFeedbackAlert');

        if (!actId) return;

        let hasFiles = $('#proofFileInput')[0].files && $('#proofFileInput')[0].files.length > 0;
        let hasGdrive = false;
        $('.gdrive-url-field').each(function() {
            if ($(this).val().trim() !== '') {
                hasGdrive = true;
            }
        });

        if (!hasFiles && !hasGdrive) {
            feedback.removeClass('d-none alert-success').addClass('alert-danger').text('Please select at least one document file or provide a valid Google Drive link.');
            return;
        }

        const formData = new FormData(form);
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Uploading Assets...');

        let uploadUrl = "{{ route('dss.rie.plans.activity.upload_document', ['id' => $plan->getRouteKey(), 'activityId' => ':actId']) }}";
        uploadUrl = uploadUrl.replace(':actId', actId);

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(res) {
                btn.prop('disabled', false).html('<i class="mdi mdi-upload me-1"></i> Upload &amp; Verify Milestone');
                if (res.success) {
                    feedback.removeClass('d-none alert-danger').addClass('alert-success').text(res.message);
                    if (window.dssToast) {
                        window.dssToast.success('Verified', res.message);
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    feedback.removeClass('d-none alert-success').addClass('alert-danger').text(res.message || 'Upload failed.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="mdi mdi-upload me-1"></i> Upload &amp; Verify Milestone');
                const msg = xhr.responseJSON?.message || 'Failed to upload document proof.';
                feedback.removeClass('d-none alert-success').addClass('alert-danger').text(msg);
            }
        });
    });

    // Delete proof handler (Supports deleting single document or all)
    $(document).on('click', '.btn-delete-proof', function() {
        const actId = $(this).data('id');
        const docId = $(this).data('doc-id');
        const docTitle = $(this).data('title') || 'this document';
        if (!actId) return;

        const doDelete = () => {
            let deleteUrl = "{{ route('dss.rie.plans.activity.delete_document', ['id' => $plan->getRouteKey(), 'activityId' => ':actId', 'docId' => ':docId']) }}";
            deleteUrl = deleteUrl.replace(':actId', actId).replace(':docId', docId || '');

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: { document_id: docId },
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.success) {
                        if (window.dssToast) {
                            window.dssToast.success('Removed', res.message);
                        }
                        setTimeout(() => { window.location.reload(); }, 600);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to delete attachment.';
                    if (window.dssToast) window.dssToast.error('Error', msg);
                    else alert(msg);
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Remove Attachment?',
                text: `Are you sure you want to remove '${docTitle}' from this milestone?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove it'
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else if (confirm(`Remove '${docTitle}' from this milestone?`)) {
            doDelete();
        }
    });

    // 2. Activate tab from URL hash if present
    const hash = window.location.hash;
    if (hash) {
        const targetTabLink = document.querySelector(`a[data-bs-toggle="tab"][href="${hash}"]`);
        if (targetTabLink) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(targetTabLink);
            tabInstance.show();
            if (hash === '#pdf') {
                const iframe = document.getElementById('pdfPreviewIframe');
                if (iframe && !iframe.getAttribute('src') && iframe.dataset.src) {
                    iframe.setAttribute('src', iframe.dataset.src);
                }
            }
        }
    }

    // 2. Handle tab switching with links, update browser URL hash
    const tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabLinks.forEach(function (link) {
        link.addEventListener('shown.bs.tab', function (e) {
            const href = e.target.getAttribute('href');
            if (href && href.startsWith('#')) {
                history.replaceState(null, null, href);
            }

            // Lazy-load PDF iframe when PDF tab is opened
            if (href === '#pdf') {
                const iframe = document.getElementById('pdfPreviewIframe');
                if (iframe && !iframe.getAttribute('src') && iframe.dataset.src) {
                    iframe.setAttribute('src', iframe.dataset.src);
                }
            }
        });
    });

    window.confirmPrintWithoutTracking = function(pdfUrl) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '3rd-Party Tracking Number Missing',
                text: 'Official documents require a 3rd-party DTS tracking number for compliance. Would you like to connect a tracking number first, or download anyway?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-link-variant me-1"></i> Connect Tracking #',
                cancelButtonText: 'Download Anyway',
                confirmButtonColor: '#047857',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = new bootstrap.Modal(document.getElementById('modalConnectPlanTracking'));
                    modal.show();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = pdfUrl;
                }
            });
        } else {
            if (confirm('Official documents require a 3rd-party DTS tracking number. Connect tracking number now?')) {
                const modal = new bootstrap.Modal(document.getElementById('modalConnectPlanTracking'));
                modal.show();
            } else {
                window.location.href = pdfUrl;
            }
        }
    };

    // 3. Connect Tracking Handler
    $('#btnConnectPlanTrackingSubmit').on('click', function () {
        const btn = $(this);
        const tracking = $('#inputPlanTrackingNumber').val().trim();
        const feedback = $('#planTrackingFeedback');

        if (!tracking) return;

        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Connecting...');
        feedback.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: "{{ route('dss.rie.plans.connect_tracking', $plan->getRouteKey()) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                tracking_number: tracking
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="mdi mdi-link-variant me-1"></i> Verify &amp; Connect');
                feedback.removeClass('d-none').addClass('alert-success').text(res.message || 'Tracking number successfully connected.');
                setTimeout(() => {
                    window.location.reload();
                }, 900);
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="mdi mdi-link-variant me-1"></i> Verify &amp; Connect');
                const msg = xhr.responseJSON?.message || 'Failed to connect tracking number. Please verify the tracking number in SNSU DTS.';
                feedback.removeClass('d-none').addClass('alert-danger').text(msg);
            }
        });
    });

    // 4. Refresh Live Tracking Trail
    $('#btnRefreshPlanLiveTracking').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Refreshing...');

        $.ajax({
            url: "{{ route('dss.rie.plans.fetch_tracking', $plan->getRouteKey()) }}",
            type: 'GET',
            success: function (res) {
                btn.prop('disabled', false).html('<i class="mdi mdi-refresh me-1"></i> Refresh Trail');
                if (res.success && res.data) {
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tracking Status',
                        text: 'No updates found for this tracking number in SNSU DTS.'
                    });
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="mdi mdi-refresh me-1"></i> Refresh Trail');
                Swal.fire({
                    icon: 'warning',
                    title: 'Live Gateway Notice',
                    text: 'Unable to reach SNSU Central DTS Gateway. Please ensure tracking number is active.'
                });
            }
        });
    });
});

function submitPlanForReview() {
    if (typeof window.customConfirm === 'function') {
        window.customConfirm({
            title: 'Submit Extension Plan for Notation?',
            message: 'This will lock the plan from proponent edits and route it to the College Head for Extension notation.',
            confirmText: 'Yes, Submit for Notation',
            onConfirm: function() {
                $.ajax({
                    url: "{{ route('dss.rie.plans.submit_for_review', $plan->getRouteKey()) }}",
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.success) {
                            window.dssToast.success('Submitted', res.message);
                            setTimeout(() => { window.location.reload(); }, 800);
                        } else {
                            window.dssToast.error('Submission Failed', res.message);
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to submit plan.';
                        window.dssToast.error('Error', msg);
                    }
                });
            }
        });
    }
}

function copyRepoHash(hash) {
    if (!hash) return;
    if (typeof window.dssCopyToClipboard === 'function') {
        window.dssCopyToClipboard(hash, 'SHA-256 hash copied to clipboard.');
    } else if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(hash).then(() => {
            if (window.dssToast) {
                window.dssToast.show({ title: 'Checksum Copied', message: 'SHA-256 hash copied to clipboard.', icon: 'success' });
            } else {
                alert('SHA-256 Hash copied: ' + hash);
            }
        });
    } else {
        const ta = document.createElement('textarea');
        ta.value = hash;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        if (window.dssToast) {
            window.dssToast.show({ title: 'Checksum Copied', message: 'SHA-256 hash copied to clipboard.', icon: 'success' });
        } else {
            alert('SHA-256 Hash copied: ' + hash);
        }
    }
}
</script>
@endpush
