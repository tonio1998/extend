@extends('layouts.dss.app')

@section('title', "Extension Proposal: {$proposal->proposal_code} — RIE System")

@push('styles')
<style>
    /* Executive Proposal Hero Banner */
    .proposal-hero-banner {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    .proposal-hero-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2563eb, #3b82f6, #0ea5e9, #10b981);
    }
    .proposal-icon-box {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    }

    /* Executive Segmented Nav Tabs */
    .proposal-tabs-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 6px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .proposal-tabs-nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .proposal-tabs-nav .nav-item {
        flex-shrink: 0;
    }
    .proposal-tabs-nav .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }
    .proposal-tabs-nav .nav-link:hover {
        color: #0f172a;
        background: #ffffff;
        border-color: #e2e8f0;
    }
    .proposal-tabs-nav .nav-link.active {
        color: #0f172a;
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
    }
    .proposal-tabs-nav .nav-link .tab-icon {
        font-size: 1.15rem;
        line-height: 1;
        transition: transform 0.2s ease;
    }
    .proposal-tabs-nav .nav-link.active .tab-icon {
        transform: scale(1.12);
    }

    /* Content Card Container */
    .proposal-workbench-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    /* Stat Highlight Cards */
    .metric-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .metric-stat-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }
    .metric-stat-card .metric-icon-pill {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Info Grid Styling */
    .info-tile {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        transition: all 0.15s ease;
    }
    .info-tile:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }
    .info-tile-label {
        font-size: 0.725rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
    }
    .info-tile-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
    }
    .pulse-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        background-color: currentColor;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    {{-- Executive Proposal Hero Banner --}}
    <div class="proposal-hero-banner p-4 mb-4 shadow-sm">
        <div class="d-flex flex-column gap-3">
            <!-- Top Row: Breadcrumb & Academic Campus Badge -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb extra-small mb-0 align-items-center">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-home-outline"></i> RIE System
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dss.rie.proposals.index') }}" class="text-decoration-none text-muted">Extension Proposals</a>
                        </li>
                        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $proposal->proposal_code }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center gap-2">
                    @if($proposal->unit || $proposal->academicProgram)
                        <span class="badge bg-light text-secondary border font-sans px-2 py-1 extra-small rounded-pill d-inline-flex align-items-center gap-1">
                            <i class="mdi mdi-domain text-primary"></i>
                            {{ $proposal->unit?->unit_name ?: ($proposal->academicProgram?->name ?: 'Extension Office') }}
                        </span>
                    @endif
                    <span class="badge bg-light text-muted border font-mono px-2 py-1 extra-small rounded-pill">
                        Created {{ $proposal->created_at ? $proposal->created_at->format('M d, Y') : 'Recent' }}
                    </span>
                </div>
            </div>

            <!-- Center Row: Emblem, Code, Badges, Title & Key Details -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="d-flex align-items-start gap-3 flex-grow-1" style="max-width: 860px;">
                    <div class="proposal-icon-box d-none d-sm-flex flex-shrink-0">
                        <i class="mdi mdi-file-document-outline fs-3"></i>
                    </div>
                    <div>
                        <!-- Status & System Badges Row -->
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2 py-1 extra-small rounded-pill fw-bold">
                                <i class="mdi mdi-pound me-0.5"></i>{{ $proposal->proposal_code }}
                            </span>

                            @if($proposal->tracking_number)
                                <a href="{{ $proposal->tracking_url }}" target="_blank" class="badge bg-light text-dark border font-mono px-2 py-1 extra-small rounded-pill text-decoration-none shadow-2xs hover-border-primary d-inline-flex align-items-center gap-1" title="Track in Centralized DTS">
                                    <i class="mdi mdi-qrcode-scan text-primary"></i> DTS: <span class="fw-bold">{{ $proposal->tracking_number }}</span>
                                </a>
                            @endif

                            @php $badge = $proposal->status_badge; @endphp
                            <span class="badge {{ $badge['class'] }} rounded-pill extra-small font-mono px-2 py-1 d-inline-flex align-items-center gap-1">
                                <span class="pulse-indicator"></span>
                                <span>{{ $badge['label'] }}</span>
                            </span>

                            @if($proposal->extensionPlan)
                                <a href="{{ route('dss.rie.plans.show', $proposal->extensionPlan->getRouteKey()) }}" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small font-mono text-decoration-none px-2 py-1 d-inline-flex align-items-center gap-1" title="View Parent Approved Plan">
                                    <i class="mdi mdi-link-variant"></i> Plan: {{ $proposal->extensionPlan->plan_code }}
                                </a>
                            @endif
                        </div>

                        <!-- Main Proposal Title -->
                        <h2 class="fw-extrabold text-dark mb-2" style="font-size: 1.375rem; letter-spacing: -0.02em; line-height: 1.35;">
                            {{ $proposal->title }}
                        </h2>

                        <!-- Key Information Chips Row -->
                        <div class="d-flex align-items-center gap-2 flex-wrap text-secondary extra-small">
                            <div class="d-inline-flex align-items-center gap-1 bg-light px-2 py-1 rounded-pill border">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 18px; height: 18px; font-size: 10px;">
                                    {{ substr($proposal->project_leader_name, 0, 1) }}
                                </div>
                                <span class="text-muted">Lead Proponent:</span>
                                <strong class="text-dark">{{ $proposal->project_leader_name }}</strong>
                                <span class="text-muted">({{ $proposal->project_leader_title ?: 'Project Leader' }})</span>
                            </div>

                            @if($proposal->budget_total_display)
                                <div class="d-inline-flex align-items-center gap-1 bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1 rounded-pill font-mono fw-bold">
                                    <i class="mdi mdi-cash-multiple"></i>
                                    <span>Total: {{ $proposal->budget_total_display }}</span>
                                </div>
                            @endif

                            @if($proposal->duration_text || $proposal->duration_start)
                                <div class="d-inline-flex align-items-center gap-1 bg-light px-2 py-1 rounded-pill border text-muted">
                                    <i class="mdi mdi-calendar-range text-primary"></i>
                                    <span>{{ $proposal->duration_text ?: ($proposal->duration_start?->format('M Y') . ' – ' . $proposal->duration_end?->format('M Y')) }}</span>
                                </div>
                            @endif

                            @if($proposal->beneficiaries)
                                <div class="d-inline-flex align-items-center gap-1 text-muted">
                                    <i class="mdi mdi-account-multiple-outline text-secondary"></i>
                                    <span>Beneficiaries: <strong class="text-dark">{{ Str::limit($proposal->beneficiaries, 40) }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Area -->
                <div class="d-flex align-items-center gap-2 flex-wrap flex-shrink-0">
                    @if($proposal->extensionPlan)
                        <a href="{{ route('dss.rie.plans.show', $proposal->extensionPlan->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1 shadow-2xs" title="Step 1: View Extension Plan">
                            <i class="mdi mdi-clipboard-text-outline text-primary fs-6"></i>
                            <span>View Plan</span>
                        </a>
                    @endif

                    <a href="{{ route('dss.rie.proposals.activity_design', $proposal->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1 shadow-2xs" title="Step 3: View Activity Design">
                        <i class="mdi mdi-calendar-text-outline text-info fs-6"></i>
                        <span>View Activity Design</span>
                    </a>

                    <a href="{{ route('dss.rie.proposals.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1 shadow-2xs">
                        <i class="mdi mdi-file-pdf-box text-danger fs-6"></i>
                        <span>Official PDF</span>
                    </a>

                    <a href="{{ route('dss.rie.proposals.accomplishment.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-2xs" title="Open Official Terminal Narrative PDF">
                        <i class="mdi mdi-file-pdf-box fs-6 text-danger"></i>
                        <span>Narrative PDF</span>
                    </a>

                    @if($proposal->is_editable)
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#configureSignatoriesModal">
                            <i class="mdi mdi-account-cog-outline fs-6"></i>
                            <span>Set Signatories</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-4 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm btn-trigger-submit-review">
                            <i class="mdi mdi-send-check fs-6"></i>
                            <span>Submit for Review</span>
                        </button>
                    @else
                        @php
                            $user = Auth::user();
                            $hasPendingForUser = $user && \App\Models\SaaS\DocumentSignatory::where('document_type', $proposal->getMorphClass())
                                ->where('document_id', $proposal->id)
                                ->where('status', 'pending')
                                ->forSignatoryUser($user)
                                ->exists();
                        @endphp
                        @if($hasPendingForUser)
                            <a href="{{ route('dss.signatures.index') }}" class="btn btn-sm btn-danger rounded-pill px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm" title="Open your centralized signature inbox">
                                <i class="mdi mdi-draw-pen fs-6"></i>
                                <span>Sign in Centralized Inbox</span>
                            </a>
                        @else
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 font-mono extra-small d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-clock-check-outline text-primary"></i>
                                <span>Submitted / In DTS Routing</span>
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Institutional 3-Stage Lifecycle Stepper --}}
    <div class="card border rounded-3 p-3 mb-4 bg-white shadow-2xs">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white font-mono extra-small">RIE WORKFLOW</span>
                <span class="fw-bold text-dark extra-small text-uppercase font-mono" style="letter-spacing: 0.5px;">Institutional Extension Lifecycle Pipeline</span>
            </div>
            @if($proposal->extensionPlan)
                <span class="extra-small font-mono text-muted">
                    Project Lifecycle Progress: <strong class="text-{{ $proposal->extensionPlan->progress_color }}">{{ $proposal->extensionPlan->progress_percentage }}%</strong> ({{ $proposal->extensionPlan->completed_milestones_count }}/{{ $proposal->extensionPlan->active_milestones_count }} Milestones Verified)
                </span>
            @endif
        </div>
        <div class="row g-2 align-items-stretch">
            <!-- Stage 1: Extension Plan -->
            <div class="col-md-4">
                <div class="p-2.5 rounded-2 border h-100 {{ $proposal->extensionPlan ? 'bg-light' : 'bg-light text-muted' }} d-flex justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <span class="badge rounded-circle {{ $proposal->extensionPlan ? 'bg-success text-white' : 'bg-secondary text-white' }} fw-bold font-mono extra-small d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">1</span>
                        <div class="overflow-hidden">
                            <div class="extra-small text-muted text-uppercase fw-bold font-mono">Stage 1: Extension Plan</div>
                            <div class="fw-bold text-dark text-truncate small font-mono">
                                {{ $proposal->extensionPlan ? $proposal->extensionPlan->plan_code : 'Independent Plan' }}
                            </div>
                        </div>
                    </div>
                    @if($proposal->extensionPlan)
                        <a href="{{ route('dss.rie.plans.show', $proposal->extensionPlan->getRouteKey()) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 extra-small flex-shrink-0" title="Open Extension Plan">
                            <span>View Plan</span> <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary extra-small">Direct</span>
                    @endif
                </div>
            </div>

            <!-- Stage 2: Extension Proposal (Active) -->
            <div class="col-md-4">
                <div class="p-2.5 rounded-2 border h-100 bg-primary-subtle border-primary d-flex justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <span class="badge rounded-circle bg-primary text-white fw-bold font-mono extra-small d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">2</span>
                        <div class="overflow-hidden">
                            <div class="extra-small text-primary text-uppercase fw-bold font-mono">Stage 2: Extension Proposal</div>
                            <div class="fw-bold text-dark text-truncate small font-mono">{{ $proposal->proposal_code }}</div>
                        </div>
                    </div>
                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 extra-small">Active Hub</span>
                </div>
            </div>

            <!-- Stage 3: Activity Design -->
            <div class="col-md-4">
                <div class="p-2.5 rounded-2 border h-100 bg-light d-flex justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <span class="badge rounded-circle bg-info text-white fw-bold font-mono extra-small d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">3</span>
                        <div class="overflow-hidden">
                            <div class="extra-small text-muted text-uppercase fw-bold font-mono">Stage 3: Activity Design</div>
                            <div class="fw-bold text-dark text-truncate small">Action Matrix &amp; Budget</div>
                        </div>
                    </div>
                    <a href="{{ route('dss.rie.proposals.activity_design', $proposal->getRouteKey()) }}" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 extra-small flex-shrink-0" title="Open Activity Design">
                        <span>View Design</span> <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Segmented Tab Navigation Bar --}}
    <div class="proposal-tabs-wrapper">
        <ul class="proposal-tabs-nav font-sans" id="proposalWorkbenchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'overview']) }}">
                    <i class="mdi mdi-view-dashboard-outline text-primary tab-icon"></i>
                    <span>Overview &amp; Part I</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'activities' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'activities']) }}">
                    <i class="mdi mdi-calendar-check-outline text-info tab-icon"></i>
                    <span>Schedule &amp; Milestones</span>
                    @if($proposal->extensionPlan)
                        <span class="badge bg-{{ $proposal->extensionPlan->progress_color }} text-white rounded-pill ms-1 font-mono extra-small">{{ $proposal->extensionPlan->progress_percentage }}%</span>
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'technical' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'technical']) }}">
                    <i class="mdi mdi-file-document-edit-outline text-indigo tab-icon"></i>
                    <span>Part II Technical Description</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'routing' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'routing']) }}">
                    <i class="mdi mdi-account-multiple-check-outline text-success tab-icon"></i>
                    <span>Part III Approval Routing &amp; Signatories</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'pdf' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'pdf']) }}">
                    <i class="mdi mdi-file-pdf-box text-danger tab-icon"></i>
                    <span>Official PDF Document</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'attachments' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'attachments']) }}">
                    <i class="mdi mdi-paperclip text-warning tab-icon"></i>
                    <span>Attachments &amp; Scans</span>
                    @if($proposal->uploadedDocuments->count() > 0)
                        <span class="badge bg-primary text-white rounded-pill ms-1 font-mono extra-small">{{ $proposal->uploadedDocuments->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab === 'accomplishment' ? 'active' : '' }}" href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'accomplishment']) }}">
                    <i class="mdi mdi-certificate-outline text-success tab-icon"></i>
                    <span>Part IV Accomplishment &amp; Terminal Report</span>
                    @php $accStatus = $proposal->metadata['accomplishment_report']['implementation_status'] ?? null; @endphp
                    @if($accStatus)
                        <span class="badge bg-success-subtle text-success rounded-pill ms-1 font-mono extra-small">{{ $accStatus }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    {{-- Active Tab Content Card Shell --}}
    <div class="proposal-workbench-card p-0 mb-4">
        <div class="card-body p-4">
            <div class="tab-content">
                <!-- TAB 1: OVERVIEW & PART I -->
                <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : 'd-none' }}" id="overview">
                    <!-- Top Metric Highlight Cards Row -->
                    <div class="row g-3 mb-4">
                        <!-- Stat 1: Total Proposed Budget -->
                        <div class="col-sm-6 col-xl">
                            <div class="metric-stat-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="info-tile-label m-0">Total Proposed Budget</span>
                                    <div class="metric-icon-pill bg-success-subtle text-success border border-success-subtle">
                                        <i class="mdi mdi-cash-multiple"></i>
                                    </div>
                                </div>
                                <div class="fw-bold text-dark fs-5 font-mono mb-1">
                                    {{ $proposal->budget_total_display ?: '₱0.00' }}
                                </div>
                                <div class="extra-small text-muted text-truncate" title="Institution: {{ $proposal->budget_institution_share ?: ($proposal->budget_snsu_share ?: '—') }} | Partner: {{ $proposal->budget_partner_share ?: '—' }}">
                                    SNSU: <strong class="text-dark">{{ $proposal->budget_institution_share ?: ($proposal->budget_snsu_share ?: '—') }}</strong> &bull; Partner: <strong class="text-dark">{{ $proposal->budget_partner_share ?: '—' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 2: Implementation Duration -->
                        <div class="col-sm-6 col-xl">
                            <div class="metric-stat-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="info-tile-label m-0">Implementation Period</span>
                                    <div class="metric-icon-pill bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="mdi mdi-calendar-clock"></i>
                                    </div>
                                </div>
                                <div class="fw-bold text-dark fs-5 font-mono mb-1 text-truncate" title="{{ $proposal->duration_text ?: 'Scheduled Period' }}">
                                    {{ $proposal->duration_text ?: ($proposal->duration_start?->format('M Y') . ' – ' . $proposal->duration_end?->format('M Y')) }}
                                </div>
                                <div class="extra-small text-muted">
                                    Commencement: <strong class="text-dark">{{ $proposal->duration_start ? $proposal->duration_start->format('M d, Y') : 'To Be Scheduled' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 3: Proponents & Extensionists -->
                        <div class="col-sm-6 col-xl">
                            <div class="metric-stat-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="info-tile-label m-0">Extension Team</span>
                                    <div class="metric-icon-pill bg-info-subtle text-info-emphasis border border-info-subtle">
                                        <i class="mdi mdi-account-group-outline"></i>
                                    </div>
                                </div>
                                <div class="fw-bold text-dark fs-5 mb-1">
                                    {{ count($proposal->proponents ?? []) ?: ($proposal->asst_project_leader_name ? 2 : 1) }} Member(s)
                                </div>
                                <div class="extra-small text-muted text-truncate" title="Lead: {{ $proposal->project_leader_name }}">
                                    Lead: <strong class="text-dark">{{ $proposal->project_leader_name }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 4: Centralized DTS Tracking -->
                        <div class="col-sm-6 col-xl">
                            <div class="metric-stat-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="info-tile-label m-0">DTS Central Track #</span>
                                    <div class="metric-icon-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                        <i class="mdi mdi-qrcode-scan"></i>
                                    </div>
                                </div>
                                <div class="fw-bold text-primary fs-5 font-mono mb-1 text-truncate">
                                    @if($proposal->tracking_number)
                                        <a href="{{ $proposal->tracking_url }}" target="_blank" class="text-primary text-decoration-none hover-underline">
                                            {{ $proposal->tracking_number }}
                                        </a>
                                    @else
                                        <span class="text-muted font-sans fs-6">Generating...</span>
                                    @endif
                                </div>
                                <div class="extra-small text-muted">
                                    Current Trail: <strong class="text-dark">{{ $proposal->dtsRecord->TrailStatus ?? 'Draft / In-Preparation' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 5: Project Lifecycle Progress -->
                        @if($proposal->extensionPlan)
                        <div class="col-sm-6 col-xl">
                            <div class="metric-stat-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="info-tile-label m-0">Lifecycle Progress</span>
                                    <div class="metric-icon-pill bg-{{ $proposal->extensionPlan->progress_color }}-subtle text-{{ $proposal->extensionPlan->progress_color }} border border-{{ $proposal->extensionPlan->progress_color }}-subtle">
                                        <i class="mdi mdi-progress-check"></i>
                                    </div>
                                </div>
                                <div class="fw-bold font-mono fs-5 mb-1 text-{{ $proposal->extensionPlan->progress_color }}">
                                    {{ $proposal->extensionPlan->progress_percentage }}%
                                </div>
                                <div class="extra-small text-muted font-mono">
                                    <strong class="text-dark">{{ $proposal->extensionPlan->completed_milestones_count }}</strong> of {{ $proposal->extensionPlan->active_milestones_count }} verified
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Main Grid: Detailed Specifications (8-Col) & Team/Quick Links (4-Col) -->
                    <div class="row g-4">
                        <!-- Left Column (8-Col): Project Specifications & Priorities -->
                        <div class="col-lg-8">
                            <!-- Official Part I. Basic Information Details -->
                            <div class="border rounded-4 p-4 mb-4 bg-white shadow-2xs">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <h6 class="fw-bold text-dark extra-small text-uppercase m-0 d-flex align-items-center gap-1">
                                        <i class="mdi mdi-card-bulleted-settings-outline text-success"></i>
                                        <span>Part I. Basic Information (Official SNSU Activity Proposal)</span>
                                    </h6>
                                    @if($proposal->is_editable)
                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editPartIModal">
                                            <i class="mdi mdi-pencil me-1"></i> Edit Basic Information
                                        </button>
                                    @endif
                                </div>

                                <div class="row g-3">
                                    {{-- Banner Program --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100 border-start border-success border-3">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-flag-outline text-success"></i> B. Banner Program
                                            </div>
                                            <div class="info-tile-value">
                                                @if($proposal->banner_program)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono px-2 py-1">
                                                        {{ $proposal->banner_program }}
                                                    </span>
                                                    @if($proposal->banner_program === 'Others' && $proposal->banner_program_other)
                                                        <div class="extra-small text-muted mt-1">{{ $proposal->banner_program_other }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-muted extra-small">Not designated</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Extension Agenda --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-bullseye-arrow text-primary"></i> C. Extension Agenda
                                            </div>
                                            <div class="info-tile-value d-flex flex-wrap gap-1">
                                                @forelse($proposal->agenda_priorities ?? [] as $agenda)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small px-2 py-0.5">
                                                        {{ $agenda }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted extra-small">None selected</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    {{-- D. SDG Addressed (Inline Official UN SDG Tiles) --}}
                                    <div class="col-12">
                                        <div class="info-tile">
                                            <div class="info-tile-label d-flex align-items-center gap-1 mb-2">
                                                <i class="mdi mdi-earth text-success"></i> D. SDG Addressed (UN Sustainable Development Goals)
                                            </div>
                                            @include('dss.rie.extension_proposals.partials.sdg_inline_tiles', [
                                                'mode' => 'readonly',
                                                'selectedSdgs' => $proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 17],
                                                'indirectSdgs' => $proposal->resolved_sdg_alignments['indirect'] ?? [],
                                            ])
                                        </div>
                                    </div>

                                    {{-- Project Leader & Gender --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-account-star-outline text-indigo"></i> E. Project Leader
                                            </div>
                                            <div class="info-tile-value d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $proposal->project_leader_name }}</div>
                                                    <div class="text-muted extra-small">{{ $proposal->project_leader_title ?: 'Project Leader' }}</div>
                                                </div>
                                                @if($proposal->project_leader_gender)
                                                    <span class="badge {{ strtolower($proposal->project_leader_gender) === 'female' ? 'bg-pink-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} rounded-pill px-2 py-0.5 extra-small">
                                                        <i class="mdi {{ strtolower($proposal->project_leader_gender) === 'female' ? 'mdi-gender-female' : 'mdi-gender-male' }} me-0.5"></i>
                                                        {{ ucfirst($proposal->project_leader_gender) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Implementing College(s) --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-school-outline text-warning-emphasis"></i> F. Implementing College(s)
                                            </div>
                                            <div class="info-tile-value d-flex flex-wrap gap-1">
                                                @php
                                                    $colleges = is_array($proposal->implementing_colleges) ? $proposal->implementing_colleges : array_filter([$proposal->implementing_colleges]);
                                                @endphp
                                                @forelse($colleges as $col)
                                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill extra-small px-2 py-0.5">
                                                        {{ $col }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted extra-small">{{ $proposal->academicProgram?->name ?? 'General Extension' }}</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Collaborating Agencies --}}
                                    <div class="col-12">
                                        <div class="info-tile">
                                            <div class="info-tile-label d-flex align-items-center gap-1 mb-2">
                                                <i class="mdi mdi-handshake-outline text-success"></i> G. Collaborating Agency(ies)
                                            </div>
                                            @php $collabRows = $proposal->resolved_collaborating_agencies; @endphp
                                            @if(!empty($collabRows) && count($collabRows) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered bg-white mb-0" style="font-size: 12px;">
                                                        <thead class="table-light extra-small text-uppercase text-muted">
                                                            <tr>
                                                                <th style="width: 50%;">Agency / Partner Name</th>
                                                                <th style="width: 50%;">Role / Contribution</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($collabRows as $collab)
                                                                <tr>
                                                                    <td class="fw-semibold text-dark">{{ $collab['agency'] ?? '—' }}</td>
                                                                    <td class="text-secondary">{{ $collab['role'] ?? 'Collaborating Partner' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted extra-small fst-italic">No collaborating partner agencies indicated.</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Target Beneficiaries --}}
                                    <div class="col-12">
                                        <div class="info-tile">
                                            <div class="info-tile-label d-flex align-items-center gap-1 mb-2">
                                                <i class="mdi mdi-account-multiple-check-outline text-primary"></i> H. Target Beneficiaries Breakdown
                                            </div>
                                            @php
                                                $benefRows = $proposal->resolved_target_beneficiaries;
                                                $sumM = 0; $sumF = 0; $sumT = 0;
                                                foreach($benefRows as $b) {
                                                    $sumM += (int) ($b['male'] ?? 0);
                                                    $sumF += (int) ($b['female'] ?? 0);
                                                    $sumT += (int) ($b['total'] ?? 0);
                                                }
                                            @endphp
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white mb-0" style="font-size: 12px;">
                                                    <thead class="table-light extra-small text-uppercase text-muted">
                                                        <tr>
                                                            <th>Target Beneficiary Group / Community</th>
                                                            <th style="width: 90px;" class="text-center">Male</th>
                                                            <th style="width: 90px;" class="text-center">Female</th>
                                                            <th style="width: 100px;" class="text-center">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($benefRows as $b)
                                                            <tr>
                                                                <td class="fw-semibold text-dark">{{ $b['name'] ?? 'General Beneficiaries' }}</td>
                                                                <td class="text-center font-mono">{{ number_format($b['male'] ?? 0) }}</td>
                                                                <td class="text-center font-mono">{{ number_format($b['female'] ?? 0) }}</td>
                                                                <td class="text-center font-mono fw-bold text-primary">{{ number_format($b['total'] ?? 0) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted extra-small">No beneficiaries listed.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    @if(count($benefRows) > 0)
                                                        <tfoot class="table-light fw-bold font-mono extra-small">
                                                            <tr>
                                                                <td class="text-end text-uppercase">Total:</td>
                                                                <td class="text-center">{{ number_format($sumM) }}</td>
                                                                <td class="text-center">{{ number_format($sumF) }}</td>
                                                                <td class="text-center text-primary">{{ number_format($sumT) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Estimated Budget Requirement --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-cash-multiple text-success"></i> I. Estimated Budget Requirement
                                            </div>
                                            <div class="info-tile-value font-mono">
                                                <div class="d-flex justify-content-between text-muted extra-small mb-1">
                                                    <span>SNSU / Internal:</span>
                                                    <strong class="text-dark">{{ $proposal->fund_source_internal ?: ($proposal->budget_institution_share ?: '₱0.00') }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted extra-small mb-1">
                                                    <span>External / Partner:</span>
                                                    <strong class="text-dark">{{ $proposal->fund_source_external ?: ($proposal->budget_partner_share ?: '₱0.00') }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between border-top pt-1 text-success fw-bold">
                                                    <span>Grand Total:</span>
                                                    <span>{{ $proposal->budget_total_display ?: '₱0.00' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Duration in Months --}}
                                    <div class="col-md-6">
                                        <div class="info-tile h-100">
                                            <div class="info-tile-label d-flex align-items-center gap-1">
                                                <i class="mdi mdi-calendar-clock text-primary"></i> J. Implementation Duration
                                            </div>
                                            <div class="info-tile-value font-mono">
                                                <div class="fw-bold text-dark fs-6 mb-1">
                                                    {{ $proposal->duration_months ? $proposal->duration_months . ' Month' . ($proposal->duration_months > 1 ? 's' : '') : ($proposal->duration_text ?: 'Scheduled Window') }}
                                                </div>
                                                <div class="extra-small text-muted">
                                                    Period: <strong>{{ $proposal->duration_start ? $proposal->duration_start->format('M d, Y') : 'Start Date' }}</strong> &ndash; <strong>{{ $proposal->duration_end ? $proposal->duration_end->format('M d, Y') : 'End Date' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linked Parent Approved Extension Plan -->
                            @if($proposal->extensionPlan)
                                <div class="border border-success-subtle bg-success-subtle bg-opacity-25 rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-success text-white p-3 d-inline-flex flex-shrink-0">
                                            <i class="mdi mdi-clipboard-check-outline fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-success text-white font-mono extra-small rounded-pill mb-1">Approved Parent Plan</span>
                                            <div class="fw-bold text-dark">{{ $proposal->extensionPlan->title }}</div>
                                            <div class="text-muted extra-small font-mono">Code: {{ $proposal->extensionPlan->plan_code }} &bull; Target Budget: {{ $proposal->extensionPlan->formatted_budget ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('dss.rie.plans.show', $proposal->extensionPlan->getRouteKey()) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold shadow-2xs">
                                        <i class="mdi mdi-open-in-new me-1"></i> View Parent Plan
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column (4-Col): Team Roster & Fast Actions -->
                        <div class="col-lg-4">
                            <!-- Proponents & Extensionists Team Roster -->
                            <div class="border rounded-4 p-3 mb-4 bg-white shadow-2xs">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-dark extra-small text-uppercase m-0 d-flex align-items-center gap-1">
                                        <i class="mdi mdi-account-group text-primary"></i>
                                        <span>Proponents &amp; Extensionists</span>
                                    </h6>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">
                                        {{ count($proposal->proponents ?? []) ?: ($proposal->asst_project_leader_name ? 2 : 1) }} Total
                                    </span>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @if(!empty($proposal->proponents) && is_array($proposal->proponents))
                                        @foreach($proposal->proponents as $prop)
                                            @php
                                                $pRole = strtolower($prop['role'] ?? '');
                                                $isLeader = str_contains($pRole, 'leader') && !str_contains($pRole, 'asst') && !str_contains($pRole, 'assistant');
                                                $isAsst = str_contains($pRole, 'asst') || str_contains($pRole, 'assistant');
                                            @endphp
                                            <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle {{ $isLeader ? 'bg-primary' : ($isAsst ? 'bg-warning text-dark' : 'bg-secondary') }} text-white d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">
                                                            {{ substr($prop['name'] ?? 'P', 0, 1) }}
                                                        </div>
                                                        <span class="fw-bold text-dark extra-small">{{ $prop['name'] ?? '' }}</span>
                                                    </div>
                                                    @if($isLeader)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small px-2 py-1">Project Leader</span>
                                                    @elseif($isAsst)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small px-2 py-1">Asst. Leader</span>
                                                    @elseif(($prop['type'] ?? 'faculty') === 'manual')
                                                        <span class="badge bg-secondary-subtle text-secondary border extra-small px-2 py-1">Custom Member</span>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small px-2 py-1">Faculty</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted extra-small ps-4">{{ $prop['role'] ?? 'Extensionist' }}</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Project Leader Fallback Card -->
                                        <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">
                                                        {{ substr($proposal->project_leader_name, 0, 1) }}
                                                    </div>
                                                    <span class="fw-bold text-dark extra-small">{{ $proposal->project_leader_name }}</span>
                                                </div>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle extra-small px-2 py-1">Project Leader</span>
                                            </div>
                                            <div class="text-muted extra-small ps-4">{{ $proposal->project_leader_title ?: 'Project Leader' }}</div>
                                        </div>
                                        @if($proposal->asst_project_leader_name)
                                            <div class="p-3 rounded-3 border bg-light bg-opacity-50">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">
                                                            {{ substr($proposal->asst_project_leader_name, 0, 1) }}
                                                        </div>
                                                        <span class="fw-bold text-dark extra-small">{{ $proposal->asst_project_leader_name }}</span>
                                                    </div>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small px-2 py-1">Asst. Leader</span>
                                                </div>
                                                <div class="text-muted extra-small ps-4">{{ $proposal->asst_project_leader_title ?: 'Assistant Project Leader' }}</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Fast Navigation Shortcuts -->
                            <div class="border rounded-4 p-3 bg-light bg-opacity-40">
                                <h6 class="fw-bold text-dark extra-small text-uppercase mb-2 d-flex align-items-center gap-1">
                                    <i class="mdi mdi-arrow-decision-auto-outline text-primary"></i>
                                    <span>Quick Workflow Jump</span>
                                </h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'technical']) }}" class="btn btn-sm btn-white border rounded-3 p-3 text-start d-flex align-items-center justify-content-between text-decoration-none shadow-2xs hover-border-primary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="mdi mdi-file-document-edit-outline text-indigo fs-5"></i>
                                            <div>
                                                <div class="fw-bold text-dark extra-small">Part II Technical Workbench</div>
                                                <div class="text-muted" style="font-size: 11px;">Edit narratives, objectives, &amp; timeline</div>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right text-muted"></i>
                                    </a>
                                    <a href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'routing']) }}" class="btn btn-sm btn-white border rounded-3 p-3 text-start d-flex align-items-center justify-content-between text-decoration-none shadow-2xs hover-border-primary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="mdi mdi-account-multiple-check-outline text-success fs-5"></i>
                                            <div>
                                                <div class="fw-bold text-dark extra-small">Signatories &amp; DTS Transit</div>
                                                <div class="text-muted" style="font-size: 11px;">Inspect institutional approval chain</div>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right text-muted"></i>
                                    </a>
                                    <a href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'pdf']) }}" class="btn btn-sm btn-white border rounded-3 p-3 text-start d-flex align-items-center justify-content-between text-decoration-none shadow-2xs hover-border-primary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="mdi mdi-file-pdf-box text-danger fs-5"></i>
                                            <div>
                                                <div class="fw-bold text-dark extra-small">Official PDF Preview</div>
                                                <div class="text-muted" style="font-size: 11px;">7-Page ISO 9001:2015 layout</div>
                                            </div>
                                        </div>
                                        <i class="mdi mdi-chevron-right text-muted"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: SCHEDULE OF ACTIVITIES & MILESTONES (CROSS-DOCUMENT PORTFOLIO) -->
                <div class="tab-pane fade {{ $activeTab === 'activities' ? 'show active' : 'd-none' }}" id="activities">
                    <div class="p-4 p-md-4 mb-4 rounded-3 border bg-light">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-7">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary text-white font-mono extra-small">STEP 1 &bull; 2 &bull; 3 SYNCHRONIZED</span>
                                    @if($proposal->extensionPlan)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                            Plan: {{ $proposal->extensionPlan->plan_code }}
                                        </span>
                                    @endif
                                </div>
                                <h5 class="fw-bold text-dark mb-1">
                                    Schedule of Activities &amp; Milestone Proof Compilation
                                </h5>
                                <p class="text-secondary small mb-0">
                                    This activity pipeline connects Step 1 (Extension Plan), Step 2 (Extension Proposal), and Step 3 (Activity Design). Each milestone functions as a project tracking checkpoint verified through uploaded wet-signed documentation or public Google Drive links saved in the Institutional Repository.
                                </p>
                            </div>
                            <div class="col-lg-5">
                                @if($proposal->extensionPlan)
                                    <div class="p-3 rounded-3 bg-white border shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="extra-small text-uppercase fw-bold text-muted font-mono">Completion Rate</span>
                                            <span class="fs-5 fw-bold font-mono text-{{ $proposal->extensionPlan->progress_color }}">{{ $proposal->extensionPlan->progress_percentage }}%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 10px; border-radius: 999px; background-color: #e2e8f0;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $proposal->extensionPlan->progress_color }}" role="progressbar" style="width: {{ $proposal->extensionPlan->progress_percentage }}%;" aria-valuenow="{{ $proposal->extensionPlan->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted extra-small font-mono">
                                            <span><i class="mdi mdi-check-circle-outline text-success me-1"></i><strong class="text-dark">{{ $proposal->extensionPlan->completed_milestones_count }}</strong> verified</span>
                                            <span><i class="mdi mdi-clock-outline text-warning me-1"></i><strong class="text-dark">{{ max(0, $proposal->extensionPlan->active_milestones_count - $proposal->extensionPlan->completed_milestones_count) }}</strong> pending</span>
                                            <span><i class="mdi mdi-flag-checkered text-primary me-1"></i><strong class="text-dark">{{ $proposal->extensionPlan->active_milestones_count }}</strong> active stages</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 rounded-3 bg-white border text-center text-muted small">
                                        Independent proposal with no parent extension plan linked.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Schedule of Activities Table -->
                    @php
                        $milestones = $proposal->extensionPlan?->milestones_list ?? [];
                    @endphp

                    <div class="table-responsive rounded-3 border mb-4">
                        <table class="table schedule-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="min-width: 250px;">Milestone Stage Description</th>
                                    <th style="min-width: 170px;" class="text-center">Target Period / Date</th>
                                    <th style="min-width: 320px;">Wet-Signed Evidence (Repository Linked)</th>
                                    <th style="width: 140px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($milestones as $m)
                                <tr>
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
                                                    'preview_url' => $m['preview_url'] ?? $m['file_url'],
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
                                                            @if($proposal->extensionPlan && !empty($m['id']))
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

                                                @if($proposal->extensionPlan && !empty($m['id']))
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
                                            @if($proposal->extensionPlan && !empty($m['id']))
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-trigger-upload d-inline-flex align-items-center gap-1"
                                                        data-id="{{ $m['id'] }}"
                                                        data-title="{{ $m['title'] }}">
                                                    <i class="mdi mdi-upload-outline"></i>
                                                    <span>Upload Evidence</span>
                                                </button>
                                            @else
                                                <span class="text-muted extra-small fst-italic"><i class="mdi mdi-clock-outline me-1"></i>Awaiting signed evidence</span>
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
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted extra-small py-4">
                                        No milestones linked to this proposal yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Linked Documents Cross-Navigation Bar -->
                    <div class="p-3 rounded-2 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-link-variant text-primary fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark extra-small text-uppercase font-mono">Connected Extension Documentation</div>
                                <div class="text-muted extra-small">Access synchronized records across Step 1 (Plan), Step 2 (Proposal), and Step 3 (Activity Design).</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($proposal->extensionPlan)
                                <a href="{{ route('dss.rie.plans.show', $proposal->extensionPlan->getRouteKey()) }}" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold">
                                    <i class="mdi mdi-clipboard-text-outline me-1"></i> Step 1: Open Extension Plan
                                </a>
                            @endif
                            <a href="{{ route('dss.rie.proposals.activity_design', $proposal->getRouteKey()) }}" class="btn btn-xs btn-outline-dark rounded-pill px-3 py-1.5 fw-semibold">
                                <i class="mdi mdi-calendar-text-outline me-1"></i> Step 3: View Activity Design
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: TECHNICAL DESCRIPTION (MS WORD-STYLE DOCUMENT WORKBENCH) -->
                @php $isEditable = (bool) ($proposal->is_editable ?? true); @endphp
                <div class="tab-pane fade {{ $activeTab === 'technical' ? 'show active' : 'd-none' }}" id="technical">
                    <form id="technicalDescriptionForm" enctype="multipart/form-data">
                        @csrf
                        <div class="word-workbench-container shadow-sm mb-4">
                            {{-- Word App Header --}}
                            <div class="word-app-header">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-white text-primary rounded-1 px-2 py-1 font-mono fw-bold">
                                        <i class="mdi mdi-file-word-outline fs-6 align-middle me-1"></i> WORD
                                    </span>
                                    <div>
                                        <span class="fw-bold fs-6 text-white">{{ $proposal->proposal_code }} — Part II. Technical Description</span>
                                        @if($isEditable)
                                            <span class="badge bg-white bg-opacity-20 text-dark ms-2 font-mono extra-small" id="saveStatusIndicator">
                                                <i class="mdi mdi-cloud-check-outline me-1 text-success"></i> Auto-Save Ready
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark ms-2 font-mono extra-small" id="saveStatusIndicator" title="Proposal has been submitted. Editing is locked.">
                                                <i class="mdi mdi-lock-outline me-1"></i> Read-Only (Submitted)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="btn btn-xs btn-outline-light px-2 py-1 rounded-pill dropdown-toggle" data-bs-toggle="dropdown" title="Jump to section">
                                            <i class="mdi mdi-bookmark-outline me-1"></i> Go to Section
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-7">
                                            <li><h6 class="dropdown-header extra-small py-1">Document Sections</h6></li>
                                            <li><a class="dropdown-item py-1" href="#sec_rationale">A. Rationale (max 500 words)</a></li>
                                            <li><a class="dropdown-item py-1" href="#sec_objectives">B. Objectives (General &amp; Specific)</a></li>
                                            <li><a class="dropdown-item py-1" href="#sec_outputs">C. Expected Output (7 Categories)</a></li>
                                            <li><a class="dropdown-item py-1" href="#sec_sdg">D. Relevance of Activity (SDGs)</a></li>
                                            <li><a class="dropdown-item py-1" href="#scheduleActivitiesSection">E. Schedule of Activities (Jan–Dec)</a></li>
                                            <li><a class="dropdown-item py-1" href="#peopleInvolvedSection">F. Project Proponents</a></li>
                                            <li><a class="dropdown-item py-1" href="#sec_risk">G. Risk Management</a></li>
                                            <li><a class="dropdown-item py-1" href="#budgetaryRequirementsSection">Part II. Budgetary Requirements (PS, MOOE, CO)</a></li>
                                        </ul>
                                    </div>
                                    @if($isEditable)
                                        <button type="button" class="btn btn-xs btn-light text-primary fw-bold px-3 py-1 shadow-2xs rounded-pill" id="btnSaveWordDocTop">
                                            <i class="mdi mdi-content-save-outline me-1"></i> Save Changes (Ctrl+S)
                                        </button>
                                    @endif
                                    <a href="{{ route('dss.rie.proposals.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-xs btn-outline-light px-2 py-1 rounded-pill" title="Preview PDF Output">
                                        <i class="mdi mdi-file-pdf-box me-1"></i> Preview PDF
                                    </a>
                                </div>
                            </div>

                            {{-- Word Document Canvas (A4 Paper Sheet View) --}}
                            <div class="word-canvas" id="wordCanvas">
                                <div class="word-page shadow-sm" id="wordPage">
                                    {{-- Academic Letterhead Watermark / Header --}}
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 text-muted extra-small">
                                        <div>
                                            <div class="fw-bold text-dark text-uppercase letter-spacing-wide">{{ ($institution ?? null)?->school_name ?: 'SURIGAO DEL NORTE STATE UNIVERSITY' }}</div>
                                            <div class="extra-small opacity-75">OFFICE OF RESEARCH, INNOVATION AND EXTENSION (RIE)</div>
                                        </div>
                                        <div class="text-end font-mono">
                                            <div>FM-{{ ($institution ?? null)?->school_acronym ?: 'SNSU' }}-PROP-01</div>
                                            @if($proposal->tracking_number)
                                                <div class="extra-small opacity-75">DTS Track: {{ $proposal->tracking_number }}</div>
                                            @endif
                                            <div class="extra-small opacity-75">Rev. 00 | Effectivity: {{ date('M d, Y') }}</div>
                                        </div>
                                    </div>

                                    {{-- Document Header Title --}}
                                    <div class="text-center mb-4">
                                        <div class="text-uppercase fw-bold text-success extra-small">Surigao Del Norte State University (SNSU)</div>
                                        <h4 class="fw-extrabold text-dark m-0" style="letter-spacing: -0.01em;">ACTIVITY PROPOSAL</h4>
                                        <div class="text-secondary fw-bold extra-small text-uppercase">Part II. TECHNICAL DESCRIPTION</div>
                                        <div class="extra-small text-muted font-mono mt-1">
                                            <span>{{ $proposal->proposal_code }}</span>
                                            @if($proposal->tracking_number)
                                                <span class="mx-2">&bull;</span>
                                                <a href="{{ $proposal->tracking_url }}" target="_blank" class="text-decoration-none text-muted hover-text-primary" title="Track in DTS">
                                                    <i class="mdi mdi-qrcode-scan text-primary me-0.5"></i> DTS # {{ $proposal->tracking_number }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!$isEditable)
                                        <div class="alert alert-warning border-0 shadow-2xs d-flex align-items-center gap-2 mb-4 py-2 px-3 rounded-2">
                                            <i class="mdi mdi-lock-alert-outline fs-5 text-warning-emphasis"></i>
                                            <div class="extra-small text-dark">
                                                <strong>Proposal Submitted &amp; Locked:</strong> This proposal is currently undergoing formal review (Status: <strong>{{ $proposal->status_badge['label'] }}</strong>). Technical Description editing and auto-save are disabled.
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Section A: Rationale --}}
                                    <div class="word-section mb-4" id="sec_rationale">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="word-section-title m-0">
                                                <span>A. RATIONALE</span>
                                            </h6>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small" id="rationaleWordCountBadge">
                                                0 / 500 words
                                            </span>
                                        </div>
                                        <p class="text-muted extra-small mb-2">State the background, community context, and technological rationale. <strong class="text-dark">(Must not exceed 500 words)</strong>.</p>
                                        <div class="word-editor-box shadow-xs mb-3 {{ !$isEditable ? 'border-light bg-light bg-opacity-25' : '' }}">
                                            @if($isEditable)
                                                @include('dss.rie.extension_proposals.partials.word_toolbar')
                                            @endif
                                            <div class="word-editable-content p-3 bg-white" id="editor_introduction" contenteditable="{{ $isEditable ? 'true' : 'false' }}" style="min-height: 140px; line-height: 1.75; {{ !$isEditable ? 'cursor: default;' : '' }}">{!! $proposal->introduction_html ?: '' !!}</div>
                                        </div>
                                    </div>

                                    {{-- Section B: Objectives --}}
                                    <div class="word-section mb-4" id="sec_objectives">
                                        <h6 class="word-section-title mb-2">
                                            <span>B. OBJECTIVES</span>
                                        </h6>

                                        {{-- General Objective --}}
                                        <div class="mb-3">
                                            <label class="form-label extra-small fw-bold text-uppercase text-secondary mb-1">
                                                1. General Objective
                                            </label>
                                            <div class="word-editor-box shadow-xs {{ !$isEditable ? 'border-light bg-light bg-opacity-25' : '' }}">
                                                <div class="word-editable-content p-3 bg-white" id="editor_general_objective" contenteditable="{{ $isEditable ? 'true' : 'false' }}" style="min-height: 70px; line-height: 1.6; {{ !$isEditable ? 'cursor: default;' : '' }}">{!! $proposal->general_objective ?: '' !!}</div>
                                            </div>
                                        </div>

                                        {{-- Specific Objectives Repeater --}}
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label extra-small fw-bold text-uppercase text-secondary m-0">
                                                    2. Specific Objectives
                                                </label>
                                                @if($isEditable)
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5 fw-bold" id="btnAddSpecificObjective">
                                                        <i class="mdi mdi-plus me-1"></i> + Add Specific Objective
                                                    </button>
                                                @endif
                                            </div>
                                            <div id="specificObjectivesList" class="vstack gap-2">
                                                @php
                                                    $specificObjs = is_array($proposal->specific_objectives) ? $proposal->specific_objectives : [];
                                                @endphp
                                                @forelse($specificObjs as $idx => $sObj)
                                                    <div class="specific-obj-row d-flex align-items-start gap-2 bg-light p-2 rounded-2 border">
                                                        <span class="badge bg-primary text-white font-mono rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 24px; height: 24px; font-size: 11px;">
                                                            {{ $idx + 1 }}
                                                        </span>
                                                        <textarea class="form-control form-control-sm specific-obj-input bg-white" rows="2" placeholder="Specific Objective #{{ $idx + 1 }}..." {{ !$isEditable ? 'readonly disabled' : '' }}>{{ $sObj }}</textarea>
                                                        @if($isEditable)
                                                            <button type="button" class="btn btn-sm text-danger p-1 btn-remove-spec-obj flex-shrink-0" title="Delete objective">
                                                                <i class="mdi mdi-close-circle-outline fs-5"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <div class="specific-obj-row d-flex align-items-start gap-2 bg-light p-2 rounded-2 border">
                                                        <span class="badge bg-primary text-white font-mono rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 24px; height: 24px; font-size: 11px;">1</span>
                                                        <textarea class="form-control form-control-sm specific-obj-input bg-white" rows="2" placeholder="Specific Objective #1..." {{ !$isEditable ? 'readonly disabled' : '' }}></textarea>
                                                        @if($isEditable)
                                                            <button type="button" class="btn btn-sm text-danger p-1 btn-remove-spec-obj flex-shrink-0" title="Delete objective">
                                                                <i class="mdi mdi-close-circle-outline fs-5"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section C: Expected Output (7 Standard Categories) --}}
                                    <div class="word-section mb-4" id="sec_outputs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="word-section-title m-0">
                                                <span>C. EXPECTED OUTPUT</span>
                                            </h6>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                                7 Institutional Output Indicators
                                            </span>
                                        </div>
                                        <p class="text-muted extra-small mb-2">Define measurable targets and quantitative descriptions for the 7 official extension output categories.</p>

                                        @php
                                            $expectedOutputsList = $proposal->resolved_expected_outputs;
                                        @endphp
                                        <div class="table-responsive border rounded-3 bg-white shadow-2xs mb-3">
                                            <table class="table table-sm table-bordered align-middle mb-0" id="expectedOutputsTable" style="font-size: 12.5px;">
                                                <thead class="table-light extra-small text-uppercase text-muted">
                                                    <tr>
                                                        <th style="width: 140px;">Output Category</th>
                                                        <th style="width: 110px;" class="text-center">Target Qty</th>
                                                        <th>Expected Output Description / Key Indicators</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($expectedOutputsList as $out)
                                                        @php
                                                            $cat = $out['category'];
                                                            $catBadgeColor = match($cat) {
                                                                'Product' => 'bg-primary-subtle text-primary border-primary-subtle',
                                                                'People' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                                                'Service' => 'bg-success-subtle text-success border-success-subtle',
                                                                'Partnership' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                                                'Patent/IP' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                                'Publication' => 'bg-purple-subtle text-purple border-purple-subtle',
                                                                'Policy' => 'bg-dark-subtle text-dark border-dark-subtle',
                                                                default => 'bg-secondary-subtle text-secondary border',
                                                            };
                                                        @endphp
                                                        <tr class="expected-output-row" data-category="{{ $cat }}">
                                                            <td class="fw-bold text-dark">
                                                                <span class="badge {{ $catBadgeColor }} border rounded-pill px-2 py-1 font-sans">
                                                                    {{ $cat }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control form-control-sm text-center font-mono output-target-input" value="{{ $out['target'] ?? 0 }}" min="0" placeholder="0" {{ !$isEditable ? 'readonly disabled' : '' }}>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control form-control-sm output-description-input" rows="2" placeholder="Specific quantitative and qualitative target for {{ $cat }}..." {{ !$isEditable ? 'readonly disabled' : '' }}>{{ $out['description'] ?? '' }}</textarea>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Section D: Relevance Narrative & 17 UN SDGs --}}
                                    @php
                                        $sdgCatalog = [];
                                        if (isset($allSdgs) && $allSdgs instanceof \Illuminate\Support\Collection && $allSdgs->isNotEmpty()) {
                                            foreach ($allSdgs as $sdgModel) {
                                                $sdgCatalog[$sdgModel->goal_number] = [
                                                    'id' => $sdgModel->id,
                                                    'title' => $sdgModel->name,
                                                    'color' => $sdgModel->color_hex,
                                                    'icon' => $sdgModel->icon ?: 'mdi-star-outline',
                                                ];
                                            }
                                        } else {
                                            $sdgCatalog = [
                                                1 => ['title' => 'No Poverty', 'color' => '#E5243B', 'icon' => 'mdi-home-heart'],
                                                2 => ['title' => 'Zero Hunger', 'color' => '#DDA63A', 'icon' => 'mdi-food-apple-outline'],
                                                3 => ['title' => 'Good Health & Well-being', 'color' => '#4C9F38', 'icon' => 'mdi-heart-pulse'],
                                                4 => ['title' => 'Quality Education', 'color' => '#C5192D', 'icon' => 'mdi-school-outline'],
                                                5 => ['title' => 'Gender Equality', 'color' => '#FF3A21', 'icon' => 'mdi-gender-male-female'],
                                                6 => ['title' => 'Clean Water & Sanitation', 'color' => '#26BDE2', 'icon' => 'mdi-water-outline'],
                                                7 => ['title' => 'Affordable & Clean Energy', 'color' => '#FCC30B', 'icon' => 'mdi-solar-power-variant-outline'],
                                                8 => ['title' => 'Decent Work & Economic Growth', 'color' => '#A21942', 'icon' => 'mdi-chart-line'],
                                                9 => ['title' => 'Industry, Innovation & Infrastructure', 'color' => '#FD6925', 'icon' => 'mdi-lightbulb-on-outline'],
                                                10 => ['title' => 'Reduced Inequalities', 'color' => '#DD1367', 'icon' => 'mdi-scale-balance'],
                                                11 => ['title' => 'Sustainable Cities & Communities', 'color' => '#FD9D24', 'icon' => 'mdi-city-variant-outline'],
                                                12 => ['title' => 'Responsible Consumption & Production', 'color' => '#BF8B2E', 'icon' => 'mdi-recycle'],
                                                13 => ['title' => 'Climate Action', 'color' => '#3F7E44', 'icon' => 'mdi-weather-partly-cloudy'],
                                                14 => ['title' => 'Life Below Water', 'color' => '#0A97D9', 'icon' => 'mdi-fish'],
                                                15 => ['title' => 'Life on Land', 'color' => '#56C02B', 'icon' => 'mdi-tree-outline'],
                                                16 => ['title' => 'Peace, Justice & Strong Institutions', 'color' => '#00689D', 'icon' => 'mdi-shield-check-outline'],
                                                17 => ['title' => 'Partnerships for the Goals', 'color' => '#19486A', 'icon' => 'mdi-handshake-outline'],
                                            ];
                                        }
                                        $selectedSdgs = array_map('intval', (array) ($proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 16]));
                                        $indirectSdgsInit = array_map('intval', (array) ($proposal->resolved_sdg_alignments['indirect'] ?? []));
                                    @endphp
                                    <div class="word-section mb-4" id="sec_sdg">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="word-section-title m-0">
                                                <span>D. RELEVANCE OF PROGRAM/PROJECTS/ACTIVITY</span>
                                            </h6>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small px-2 py-1 font-mono" id="sdgCounterBadge">
                                                {{ count($selectedSdgs) }} Direct{{ count($indirectSdgsInit) > 0 ? ', ' . count($indirectSdgsInit) . ' Indirect' : '' }}
                                            </span>
                                        </div>
                                        <p class="text-muted extra-small mb-2"><i class="mdi mdi-cursor-default-click-outline me-1 text-primary"></i><strong>Left-click</strong> an SDG to mark as <strong>Direct</strong> alignment. <i class="mdi mdi-mouse me-1 text-warning"></i><strong>Right-click</strong> to mark as <strong>Indirect / Tangential</strong> <span class="text-warning">⚠</span>.</p>

                                        {{-- 17 SDGs Interactive Inline Tiles --}}
                                        <div class="mb-3">
                                            @include('dss.rie.extension_proposals.partials.sdg_inline_tiles', [
                                                'mode' => 'workbench',
                                                'isEditable' => $isEditable,
                                                'selectedSdgs' => $selectedSdgs,
                                                'indirectSdgs' => $indirectSdgsInit,
                                            ])
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label text-muted extra-small fw-bold text-uppercase m-0">SDG Relevance Narrative</label>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small font-sans py-1 px-2" id="sdgNarrativeSyncBadge">
                                                    <i class="mdi mdi-auto-fix me-1"></i> Auto-synced with SDGs
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                @if($isEditable)
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 shadow-2xs fw-semibold" id="btnAiGenerateNarrative" title="Generate comprehensive academic narrative using AI / Decision Support Engine">
                                                        <i class="mdi mdi-robot-excited-outline text-primary me-1"></i> AI Auto-Generate Narrative
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="word-editor-box shadow-xs mb-3 {{ !$isEditable ? 'border-light bg-light bg-opacity-25' : '' }}">
                                            @if($isEditable)
                                                @include('dss.rie.extension_proposals.partials.word_toolbar')
                                            @endif
                                            <div class="word-editable-content p-3 bg-white mb-0" id="editor_relevance_narrative" contenteditable="{{ $isEditable ? 'true' : 'false' }}" style="min-height: 90px; line-height: 1.7; {{ !$isEditable ? 'cursor: default;' : '' }}">{!! $proposal->relevance_narrative_html ?: '' !!}</div>
                                        </div>
                                    </div>

                                    {{-- Section E: Schedule of Activities (Jan - Dec) --}}
                                    <div class="word-section mb-4" id="scheduleActivitiesSection">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                            <div>
                                                <h6 class="word-section-title m-0">
                                                    <span>E. SCHEDULE OF ACTIVITIES</span>
                                                </h6>
                                                <p class="text-muted extra-small m-0">Timeline matrix mapping major activities against calendar months (Jan–Dec).</p>
                                            </div>
                                            @if($isEditable)
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 shadow-2xs fw-semibold" id="btnAddScheduleRow">
                                                        <i class="mdi mdi-plus me-1"></i> + Add Activity
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="table-responsive border rounded-2 mb-2">
                                            <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="scheduleTable">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width: 35px;">No.</th>
                                                        <th style="width: 35%; text-align: left;">Major Activities</th>
                                                        <th style="width: 22%; text-align: left;">Agency / Person Involved</th>
                                                        <th style="width: 28px;">Jan</th>
                                                        <th style="width: 28px;">Feb</th>
                                                        <th style="width: 28px;">Mar</th>
                                                        <th style="width: 28px;">Apr</th>
                                                        <th style="width: 28px;">May</th>
                                                        <th style="width: 28px;">Jun</th>
                                                        <th style="width: 28px;">Jul</th>
                                                        <th style="width: 28px;">Aug</th>
                                                        <th style="width: 28px;">Sep</th>
                                                        <th style="width: 28px;">Oct</th>
                                                        <th style="width: 28px;">Nov</th>
                                                        <th style="width: 28px;">Dec</th>
                                                        @if($isEditable)<th style="width: 40px;"></th>@endif
                                                    </tr>
                                                </thead>
                                                <tbody id="scheduleTableBody">
                                                    @php
                                                        $schedRows = $proposal->resolved_schedule_of_activities['rows'] ?? [];
                                                        if (empty($schedRows)) {
                                                            $schedRows = [
                                                                ['no' => 1, 'activity' => 'Coordination with Partner Community & LGU', 'agency' => 'Project Leader / Team', 'months' => ['Sep' => 1]],
                                                                ['no' => 2, 'activity' => 'Preparation of Training Modules and Instructional Materials', 'agency' => 'Extension Faculty', 'months' => ['Sep' => 1, 'Oct' => 1]],
                                                                ['no' => 3, 'activity' => 'Conduct of Capability Training Workshops', 'agency' => 'Project Team & Partners', 'months' => ['Oct' => 1]],
                                                                ['no' => 4, 'activity' => 'Post-Activity Evaluation & Terminal Reporting', 'agency' => 'Project Proponents', 'months' => ['Nov' => 1]],
                                                            ];
                                                        }
                                                        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                    @endphp
                                                    @foreach($schedRows as $idx => $sRow)
                                                        @php
                                                            $rMonths = $sRow['months'] ?? [];
                                                        @endphp
                                                        <tr class="schedule-data-row" data-index="{{ $idx }}">
                                                            <td class="text-center fw-bold sched-row-num">{{ $sRow['no'] ?? ($idx + 1) }}</td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 sched-activity-input" value="{{ $sRow['activity'] ?? ($sRow['name'] ?? '') }}" placeholder="Enter major activity..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 sched-agency-input" value="{{ $sRow['agency'] ?? ($sRow['person'] ?? 'Extension Team') }}" placeholder="Agency or person..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            @foreach($allMonths as $m)
                                                                @php
                                                                    $isChecked = !empty($rMonths[$m]) || (!empty($sRow['active_months']) && in_array($m, (array)$sRow['active_months']));
                                                                @endphp
                                                                <td class="text-center p-0">
                                                                    <div class="d-flex align-items-center justify-content-center h-100 py-1">
                                                                        <input type="checkbox" class="form-check-input m-0 sched-month-checkbox" data-month="{{ $m }}" {{ $isChecked ? 'checked' : '' }} {{ !$isEditable ? 'disabled' : '' }}>
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                            @if($isEditable)
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-sched-row" title="Remove activity">
                                                                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="extra-small text-muted d-flex justify-content-between align-items-center">
                                            <span><i class="mdi mdi-calendar-check text-primary me-1"></i> Check the months during which each major activity is scheduled to occur.</span>
                                            <span class="font-mono text-muted" id="scheduleRowCountBadge">{{ count($schedRows) }} Activities</span>
                                        </div>
                                    </div>

                                    {{-- Section F: Project Proponents --}}
                                    <div class="word-section mb-4" id="peopleInvolvedSection">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                            <div>
                                                <h6 class="word-section-title m-0">
                                                    <span>F. PROJECT PROPONENTS</span>
                                                </h6>
                                                <p class="text-muted extra-small m-0">Faculty and staff proponents, project designations, and assigned responsibilities.</p>
                                            </div>
                                            @if($isEditable)
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 shadow-2xs fw-semibold" id="btnAddProponentRow">
                                                        <i class="mdi mdi-account-plus-outline me-1"></i> + Add Proponent
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2 shadow-2xs" id="btnSyncFromProponents" title="Sync initial proponents from Part I">
                                                        <i class="mdi mdi-sync me-1"></i> Sync Proponents
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="table-responsive border rounded-2 mb-2">
                                            <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="proponentsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 30%;">Name</th>
                                                        <th style="width: 25%;">Project Role Title</th>
                                                        <th>Duties &amp; Responsibilities</th>
                                                        @if($isEditable)<th style="width: 40px;" class="text-center"></th>@endif
                                                    </tr>
                                                </thead>
                                                <tbody id="proponentsTableBody">
                                                    @php
                                                        $propList = $proposal->resolved_people_involved['rows'] ?? [];
                                                        if (empty($propList) && !empty($proposal->proponents)) {
                                                            $propList = $proposal->proponents;
                                                        }
                                                        if (empty($propList)) {
                                                            $propList = [
                                                                [
                                                                    'name' => $proposal->project_leader_name,
                                                                    'role' => 'Project Leader',
                                                                    'responsibilities' => 'Overall project management, institutional coordination, and reporting.',
                                                                ]
                                                            ];
                                                        }
                                                    @endphp
                                                    @foreach($propList as $pIdx => $prop)
                                                        <tr class="proponent-data-row" data-index="{{ $pIdx }}">
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 fw-semibold prop-name-input" value="{{ $prop['name'] ?? '' }}" placeholder="Faculty proponent name..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 prop-role-input" value="{{ $prop['role'] ?? 'Member' }}" placeholder="Role title..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 prop-duties-input" value="{{ $prop['responsibilities'] ?? ($prop['duties'] ?? 'Activity facilitation, technical modules, and logistics.') }}" placeholder="Assigned duties..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            @if($isEditable)
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-prop-row" title="Remove proponent">
                                                                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Section G: Risk Management --}}
                                    <div class="word-section mb-4" id="sec_risk">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                            <div>
                                                <h6 class="word-section-title m-0">
                                                    <span>G. RISK MANAGEMENT</span>
                                                </h6>
                                                <p class="text-muted extra-small m-0">Identified project risks, severity levels, and preventative mitigation strategies.</p>
                                            </div>
                                            @if($isEditable)
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 shadow-2xs fw-semibold" id="btnAddRiskRow">
                                                    <i class="mdi mdi-shield-plus-outline me-1"></i> + Add Risk Factor
                                                </button>
                                            @endif
                                        </div>

                                        <div class="table-responsive border rounded-2 mb-2">
                                            <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="riskManagementTable">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width: 65px;">Obj No.</th>
                                                        <th style="width: 65px;">Act No.</th>
                                                        <th style="text-align: left; width: 30%;">Risk</th>
                                                        <th style="width: 110px;">Risk Level</th>
                                                        <th style="text-align: left;">Prevention / Mitigation Plan</th>
                                                        @if($isEditable)<th style="width: 40px;"></th>@endif
                                                    </tr>
                                                </thead>
                                                <tbody id="riskManagementTableBody">
                                                    @php
                                                        $riskItems = $proposal->resolved_risk_management;
                                                        if (empty($riskItems)) {
                                                            $riskItems = [
                                                                ['objective_no' => '1', 'activity_no' => '1', 'risk' => 'Inclement weather or typhoon disruption', 'risk_level' => 'Low', 'prevention' => 'Coordinate backup indoor venue and monitor weather advisories.'],
                                                                ['objective_no' => '2', 'activity_no' => '3', 'risk' => 'Intermittent internet connectivity during practical demo', 'risk_level' => 'Medium', 'prevention' => 'Pre-download offline presentation modules and localized toolkits.'],
                                                                ['objective_no' => '3', 'activity_no' => '4', 'risk' => 'Low participant turnout', 'risk_level' => 'High', 'prevention' => 'Coordinate actively with community LGU leaders and provide certificates of completion.'],
                                                            ];
                                                        }
                                                    @endphp
                                                    @foreach($riskItems as $rIdx => $rk)
                                                        @php
                                                            $rLevel = ucfirst(strtolower($rk['risk_level'] ?? 'Low'));
                                                        @endphp
                                                        <tr class="risk-data-row" data-index="{{ $rIdx }}">
                                                            <td class="text-center">
                                                                <input type="text" class="form-control form-control-sm text-center border-0 risk-obj-input" value="{{ $rk['objective_no'] ?? 1 }}" style="max-width: 55px;" {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="text" class="form-control form-control-sm text-center border-0 risk-act-input" value="{{ $rk['activity_no'] ?? 1 }}" style="max-width: 55px;" {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 risk-desc-input" value="{{ $rk['risk'] ?? '' }}" placeholder="Identify risk..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            <td class="text-center">
                                                                <select class="form-select form-select-sm risk-level-select font-mono extra-small {{ $rLevel === 'High' ? 'text-danger fw-bold' : ($rLevel === 'Medium' ? 'text-warning-emphasis fw-semibold' : 'text-success fw-semibold') }}" {{ !$isEditable ? 'disabled' : '' }}>
                                                                    <option value="Low" {{ $rLevel === 'Low' ? 'selected' : '' }}>Low</option>
                                                                    <option value="Medium" {{ $rLevel === 'Medium' ? 'selected' : '' }}>Medium</option>
                                                                    <option value="High" {{ $rLevel === 'High' ? 'selected' : '' }}>High</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm border-0 risk-prev-input" value="{{ $rk['prevention'] ?? '' }}" placeholder="Mitigation plan..." {{ !$isEditable ? 'readonly' : '' }}>
                                                            </td>
                                                            @if($isEditable)
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-risk-row" title="Remove risk">
                                                                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Part II. Budgetary Requirements (Line - Item Budget: Sections A to I) --}}
                                    <div class="word-section mb-4" id="budgetaryRequirementsSection">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                            <div>
                                                <h6 class="word-section-title m-0">
                                                    <span>PART II. BUDGETARY REQUIREMENTS</span>
                                                </h6>
                                                <p class="text-muted extra-small m-0">
                                                    <em>(Put N/A in cells if no cash requirement; each section allows dynamic line item insertion with real-time auto-calculation)</em>
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono fs-7 py-1 px-2" id="budgetCashBadge">
                                                    Grand Total: &#8369;{{ number_format($proposal->budget_cash_amount ?: 0, 2) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="fw-bold text-dark extra-small text-uppercase mb-2">Line - Item Budget</div>

                                        @php
                                             $budgetSectionsCatalog = [
                                                 'A' => [
                                                     'letter' => 'A.',
                                                     'title' => 'Personal Services',
                                                     'is_main' => true,
                                                     'default' => [
                                                         ['particulars' => 'Salary', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
                                                         ['particulars' => 'Honoraria', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A']
                                                     ]
                                                 ],
                                                 'B' => [
                                                     'letter' => 'B.',
                                                     'title' => 'Maintenance & Other Operating Expenses (MOOE)',
                                                     'is_main' => true,
                                                     'is_header_only' => true,
                                                     'default' => []
                                                 ],
                                                 'B_COMM' => [
                                                     'letter' => '',
                                                     'title' => 'Communication',
                                                     'parent' => 'B',
                                                     'default' => []
                                                 ],
                                                 'B_TRANS' => [
                                                     'letter' => '',
                                                     'title' => 'Transportation & Travel',
                                                     'parent' => 'B',
                                                     'default' => []
                                                 ],
                                                 'B_SUPP' => [
                                                     'letter' => '',
                                                     'title' => 'Supplies and Materials',
                                                     'parent' => 'B',
                                                     'default' => []
                                                 ],
                                                 'B_TRAIN' => [
                                                     'letter' => '',
                                                     'title' => 'Trainings & Seminars',
                                                     'parent' => 'B',
                                                     'default' => []
                                                 ],
                                                 'B_OTHER' => [
                                                     'letter' => '',
                                                     'title' => 'Other Services',
                                                     'parent' => 'B',
                                                     'default' => []
                                                 ],
                                                 'C' => [
                                                     'letter' => 'C.',
                                                     'title' => 'Capital Outlay (CO)',
                                                     'is_main' => true,
                                                     'default' => []
                                                 ],
                                                 'D' => [
                                                     'letter' => 'D.',
                                                     'title' => 'Contingency or Miscellaneous',
                                                     'is_main' => true,
                                                     'default' => []
                                                 ],
                                             ];
                                             $resolvedBudget = $proposal->resolved_line_item_budget;
                                        @endphp

                                        <div class="table-responsive border rounded-2 mb-2">
                                            <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="lineItemBudgetTable">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width: 45px; text-align: center;"></th>
                                                        <th style="text-align: left; width: 42%;">Particulars / Item Description</th>
                                                        <th style="width: 10%;">Qty</th>
                                                        <th style="width: 11%;">Unit</th>
                                                        <th style="width: 15%; text-align: right;">Unit Cost (&#8369;)</th>
                                                        <th style="width: 15%; text-align: right;">Total Cost (&#8369;)</th>
                                                        @if($isEditable)<th style="width: 40px;"></th>@endif
                                                    </tr>
                                                </thead>
                                                <tbody id="lineItemBudgetTableBody">
                                                    @foreach($budgetSectionsCatalog as $secKey => $secMeta)
                                                        @php
                                                            $isHeaderOnly = !empty($secMeta['is_header_only']);
                                                            $isSubcategory = !empty($secMeta['parent']);
                                                            $secItems = $resolvedBudget[$secKey]['items'] ?? $secMeta['default'];
                                                            if (!$isHeaderOnly && empty($secItems)) {
                                                                $secItems = [['particulars' => 'N/A', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A']];
                                                            }
                                                        @endphp

                                                        @if($isHeaderOnly)
                                                            {{-- Main Parent Header (e.g. B. MOOE) --}}
                                                            <tr class="table-secondary fw-bold section-header-row" data-section="{{ $secKey }}">
                                                                <td class="text-center font-mono fw-bold fs-7">{{ $secMeta['letter'] }}</td>
                                                                <td colspan="{{ $isEditable ? 4 : 3 }}" class="text-dark py-2">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="fs-7">{{ $secMeta['title'] }}</span>
                                                                        <span class="font-mono text-muted extra-small section-subtotal-display" data-section="{{ $secKey }}">MOOE Total: &#8369;0.00</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end font-mono fw-bold section-subtotal-val" data-section="{{ $secKey }}">&#8369;0.00</td>
                                                                @if($isEditable)<td></td>@endif
                                                            </tr>
                                                        @elseif($isSubcategory)
                                                            {{-- Sub-category under B (Communication, Transportation, etc.) --}}
                                                            <tr class="bg-light fw-bold section-header-row" data-section="{{ $secKey }}" style="border-top: 2px solid #cbd5e1;">
                                                                <td class="text-center font-mono text-muted"></td>
                                                                <td colspan="{{ $isEditable ? 4 : 3 }}" class="text-dark ps-3 py-1.5">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="text-dark fw-bold"><i class="mdi mdi-subdirectory-arrow-right me-1 text-primary"></i>{{ $secMeta['title'] }}</span>
                                                                        <span class="font-mono text-muted extra-small section-subtotal-display" data-section="{{ $secKey }}">Subtotal: &#8369;0.00</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end font-mono fw-semibold section-subtotal-val" data-section="{{ $secKey }}">&#8369;0.00</td>
                                                                @if($isEditable)
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-add-budget-row" data-section="{{ $secKey }}" title="Add line item to {{ $secMeta['title'] }}">
                                                                            <i class="mdi mdi-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            @foreach($secItems as $itemIdx => $bItem)
                                                                <tr class="budget-item-row" data-section="{{ $secKey }}">
                                                                    <td class="text-center text-muted font-mono"></td>
                                                                    <td class="ps-4">
                                                                        <input type="text" class="form-control form-control-sm border-0 b-part-input" value="{{ $bItem['particulars'] ?? '' }}" placeholder="Item description / particulars..." {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input type="text" class="form-control form-control-sm text-center border-0 b-qty-input" value="{{ $bItem['qty'] ?? 'N/A' }}" placeholder="Qty" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input type="text" class="form-control form-control-sm text-center border-0 b-unit-input" value="{{ $bItem['unit'] ?? 'N/A' }}" placeholder="Unit" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <input type="text" class="form-control form-control-sm text-end border-0 b-cost-input" value="{{ $bItem['cost'] ?? 'N/A' }}" placeholder="0.00" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <input type="text" class="form-control form-control-sm text-end border-0 fw-semibold b-amount-input" value="{{ $bItem['amount'] ?? 'N/A' }}" placeholder="0.00" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    @if($isEditable)
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-budget-row" title="Remove line item">
                                                                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                                            </button>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            {{-- Standard Main Section (A, C, D) --}}
                                                            <tr class="table-secondary fw-bold section-header-row" data-section="{{ $secKey }}">
                                                                <td class="text-center font-mono fw-bold fs-7">{{ $secMeta['letter'] }}</td>
                                                                <td colspan="{{ $isEditable ? 4 : 3 }}" class="text-dark py-2">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="fs-7">{{ $secMeta['title'] }}</span>
                                                                        <span class="font-mono text-muted extra-small section-subtotal-display" data-section="{{ $secKey }}">Subtotal: &#8369;0.00</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end font-mono fw-bold section-subtotal-val" data-section="{{ $secKey }}">&#8369;0.00</td>
                                                                @if($isEditable)
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 btn-add-budget-row" data-section="{{ $secKey }}" title="Add line item to this section">
                                                                            <i class="mdi mdi-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                @endif
                                                            </tr>
                                                            @foreach($secItems as $itemIdx => $bItem)
                                                                <tr class="budget-item-row" data-section="{{ $secKey }}">
                                                                    <td class="text-center text-muted font-mono"></td>
                                                                    <td class="ps-3">
                                                                        <input type="text" class="form-control form-control-sm border-0 b-part-input" value="{{ $bItem['particulars'] ?? '' }}" placeholder="Item description / particulars..." {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input type="text" class="form-control form-control-sm text-center border-0 b-qty-input" value="{{ $bItem['qty'] ?? 'N/A' }}" placeholder="Qty" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input type="text" class="form-control form-control-sm text-center border-0 b-unit-input" value="{{ $bItem['unit'] ?? 'N/A' }}" placeholder="Unit" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <input type="text" class="form-control form-control-sm text-end border-0 b-cost-input" value="{{ $bItem['cost'] ?? 'N/A' }}" placeholder="0.00" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <input type="text" class="form-control form-control-sm text-end border-0 fw-semibold b-amount-input" value="{{ $bItem['amount'] ?? 'N/A' }}" placeholder="0.00" {{ !$isEditable ? 'readonly' : '' }}>
                                                                    </td>
                                                                    @if($isEditable)
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-budget-row" title="Remove line item">
                                                                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                                            </button>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-dark fw-bold">
                                                        <td colspan="5" class="text-end text-uppercase py-2">Grand Total:</td>
                                                        <td class="text-end font-mono fs-6 text-white py-2" id="budgetGrandTotalDisplay">
                                                            &#8369;{{ number_format($proposal->budget_cash_amount ?: 0, 2) }}
                                                        </td>
                                                        @if($isEditable)<td></td>@endif
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Document Footer --}}
                                    <div class="border-top pt-3 text-muted extra-small d-flex justify-content-between align-items-center">
                                        <span>Page 2 of 6 &bull; Activity Proposal &bull; Part II. Technical Description</span>
                                        <span class="font-mono">Official RIE System Document</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Sticky Save Bar --}}
                            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                @if($isEditable)
                                    <div class="extra-small text-muted">
                                        <i class="mdi mdi-cloud-check-outline text-primary me-1"></i> Auto-save is active while drafting. Configure signatories before formal submission.
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-xs" id="btnSaveWordDocBottom">
                                            <i class="mdi mdi-content-save-check me-1"></i> Save Draft
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#configureSignatoriesModal">
                                            <i class="mdi mdi-account-cog-outline me-1"></i> Set Signatories
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-xs btn-trigger-submit-review">
                                            <i class="mdi mdi-send-check me-1"></i> Submit Proposal for Review
                                        </button>
                                    </div>
                                @else
                                    <div class="extra-small text-muted">
                                        <i class="mdi mdi-lock-outline text-warning me-1"></i> Proposal has been submitted for review. Editing is locked.
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('dss.rie.proposals.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold shadow-xs">
                                            <i class="mdi mdi-file-pdf-box me-1"></i> View Official 7-Page PDF
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: APPROVAL ROUTING & SIGNATURES -->
                <div class="tab-pane fade {{ $activeTab === 'routing' ? 'show active' : 'd-none' }}" id="routing">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 bg-light rounded-3 border mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-inline-flex">
                                <i class="mdi mdi-qrcode-scan fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark extra-small text-uppercase">Centralized Document Tracking System (DTS)</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="font-mono fs-5 fw-bold text-primary">{{ $proposal->tracking_number ?: $proposal->proposal_code }}</span>
                                    @if($proposal->dtsRecord)
                                        <span class="badge bg-secondary-subtle text-secondary font-mono extra-small">
                                            DTS Trail: {{ $proposal->dtsRecord->TrailStatus ?? 'Start' }}
                                        </span>
                                    @endif
                                    @php $badge = $proposal->status_badge; @endphp
                                    <span class="badge {{ $badge['class'] }} rounded-pill extra-small font-mono px-2 py-1">
                                        <i class="mdi {{ $badge['icon'] }} me-0.5"></i> {{ $badge['label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-2xs fw-semibold" data-bs-toggle="modal" data-bs-target="#connectTrackingModal">
                                <i class="mdi mdi-link-variant me-1"></i> Connect SNSU Tracking
                            </button>
                            <a href="{{ $proposal->tracking_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-2xs fw-semibold">
                                <i class="mdi mdi-open-in-new me-1"></i> Public Tracker
                            </a>
                            @if($proposal->is_editable)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-2xs" data-bs-toggle="modal" data-bs-target="#configureSignatoriesModal">
                                    <i class="mdi mdi-account-cog-outline me-1"></i> Configure Signatories
                                </button>
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-2xs btn-trigger-submit-review">
                                    <i class="mdi mdi-send-check me-1"></i> Submit Proposal for Review
                                </button>
                            @else
                                <a href="{{ route('dss.signatures.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-2xs d-inline-flex align-items-center gap-1" title="Open centralized signature inbox">
                                    <i class="mdi mdi-draw-pen"></i>
                                    <span>For Signature Module</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- SNSU CENTRAL DTS LIVE TRACKING INTEGRATION -->
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
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" id="btnRefreshLiveTracking" title="Refresh live status from SNSU DTS">
                                    <i class="mdi mdi-refresh me-1"></i> Refresh Trail
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            @if(!empty($snsuTracking))
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-3">
                                        <div class="p-2.5 rounded-2 bg-light border">
                                            <div class="text-muted extra-small text-uppercase fw-semibold">Tracking Number / QR</div>
                                            <div class="font-mono fw-bold text-primary fs-6">{{ $snsuTracking['tracking_number'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="p-2.5 rounded-2 bg-light border">
                                            <div class="text-muted extra-small text-uppercase fw-semibold">Current Custody / Office</div>
                                            <div class="fw-bold text-dark text-truncate fs-6" title="{{ $snsuTracking['current_location'] }}">{{ $snsuTracking['current_location'] ?: 'Office of RIE' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="p-2.5 rounded-2 bg-light border">
                                            <div class="text-muted extra-small text-uppercase fw-semibold">Destination Office</div>
                                            <div class="fw-bold text-dark text-truncate fs-6">{{ $snsuTracking['destination'] ?: 'Under Review' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="p-2.5 rounded-2 bg-light border">
                                            <div class="text-muted extra-small text-uppercase fw-semibold">Trail Status</div>
                                            <div class="fw-bold text-dark fs-6 font-mono text-uppercase">{{ $snsuTracking['trail_status'] ?: 'Start' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Trail Table -->
                                <div class="table-responsive rounded-2 border">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 13px;">
                                        <thead class="table-light extra-small text-uppercase text-muted">
                                            <tr>
                                                <th class="ps-3" style="width: 50px;">#</th>
                                                <th style="min-width: 170px;">Date &amp; Time</th>
                                                <th style="min-width: 130px;">Status</th>
                                                <th style="min-width: 220px;">Office / Station</th>
                                                <th style="min-width: 200px;">Action / Purpose</th>
                                                <th style="min-width: 160px;">Courier / Custodian</th>
                                                <th class="pe-3" style="min-width: 200px;">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="snsuTrailTableBody">
                                            @forelse($snsuTracking['trail'] ?? [] as $tIdx => $log)
                                                <tr>
                                                    <td class="ps-3 font-mono text-muted">{{ $tIdx + 1 }}</td>
                                                    <td class="font-mono text-dark fw-semibold extra-small">{{ $log['timestamp'] ?: '—' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">
                                                            {{ $log['status'] }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold text-dark">{{ $log['office'] ?: '—' }}</td>
                                                    <td class="text-secondary extra-small">{{ $log['action'] ?: 'Routing' }}</td>
                                                    <td class="text-dark extra-small">{{ $log['courier'] ?: '—' }}</td>
                                                    <td class="pe-3 text-muted extra-small">{{ $log['comments'] ?: '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-3 text-muted extra-small">
                                                        No transit logs recorded yet in SNSU DTS.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 px-3 bg-light rounded-3 border">
                                    <div class="rounded-circle bg-white text-muted p-3 d-inline-flex border mb-2 shadow-2xs">
                                        <i class="mdi mdi-link-variant-off fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No SNSU Central DTS Record Connected Yet</h6>
                                    <p class="text-muted extra-small mx-auto mb-3" style="max-width: 540px;">
                                        Once this document is encoded or filed in the university-wide Document Tracking System (<code>uis.dev.com</code>), connect its 12-digit Tracking Number here to stream live custody and transmittal logs.
                                    </p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" data-bs-toggle="modal" data-bs-target="#connectTrackingModal">
                                        <i class="mdi mdi-link-variant me-1"></i> Connect SNSU Tracking Number
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- DTS RECORD ROUTING STEPS (dts_record_routes) -->
                    <div class="card border rounded-3 shadow-2xs overflow-hidden mb-4">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">dts_record_routes</span>
                                <h6 class="fw-bold text-dark extra-small text-uppercase m-0">Sequential Institutional Transit Flow (5 Routing Steps)</h6>
                            </div>
                            <span class="extra-small text-muted font-mono">Standard Turnaround: 24 hrs SLA per Office</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light extra-small text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-3" style="width: 70px;">Step #</th>
                                        <th style="min-width: 220px;">Routing Destination / Office</th>
                                        <th style="min-width: 280px;">Action / Endorsement Purpose</th>
                                        <th style="width: 100px;">SLA</th>
                                        <th style="width: 140px;">Route Status</th>
                                        <th class="pe-3" style="min-width: 160px;">Date &amp; Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recordRoutes ?? [] as $route)
                                        @php
                                            $st = strtolower($route->status ?? 'pending');
                                            $badgeClass = match($st) {
                                                'in_transit' => 'bg-primary text-white',
                                                'in_progress' => 'bg-warning text-dark',
                                                'completed' => 'bg-success text-white',
                                                default => 'bg-secondary-subtle text-secondary border',
                                            };
                                            $badgeIcon = match($st) {
                                                'in_transit' => 'mdi-truck-fast-outline',
                                                'in_progress' => 'mdi-clock-outline',
                                                'completed' => 'mdi-check-bold',
                                                default => 'mdi-timer-sand',
                                            };
                                        @endphp
                                        <tr class="{{ $st === 'in_transit' ? 'table-primary bg-opacity-25' : '' }}">
                                            <td class="ps-3 font-mono fw-bold">
                                                <span class="badge bg-light text-dark border font-mono rounded-pill px-2">Step {{ $route->step_number }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $route->office_name }}</div>
                                                <div class="text-muted extra-small font-mono">{{ $route->department?->unit_code ?: 'Department/Unit' }}</div>
                                            </td>
                                            <td>
                                                <div class="text-secondary extra-small">{{ $route->for_action ?: 'For institutional evaluation and endorsement' }}</div>
                                            </td>
                                            <td class="font-mono extra-small text-muted">{{ $route->sla_hours ?? 24 }} Hours</td>
                                            <td>
                                                <span class="badge {{ $badgeClass }} rounded-pill extra-small px-2 py-1 font-mono">
                                                    <i class="mdi {{ $badgeIcon }} me-0.5"></i> {{ ucfirst(str_replace('_', ' ', $st)) }}
                                                </span>
                                            </td>
                                            <td class="pe-3 extra-small font-mono text-muted">
                                                @if($route->completed_at)
                                                    <span class="text-success fw-bold">{{ \Carbon\Carbon::parse($route->completed_at)->format('M d, Y h:i A') }}</span>
                                                @elseif($route->received_at)
                                                    <span class="text-primary">Received: {{ \Carbon\Carbon::parse($route->received_at)->format('M d, Y') }}</span>
                                                @else
                                                    <span>Pending transit</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-3 text-muted extra-small">
                                                No DTS record routes initialized yet. Click <strong>Configure Signatories</strong> to synchronize routes.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SIGNATORIES MATRIX (sys_document_signatories) -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="fw-bold text-dark extra-small text-uppercase mb-0">Part III. Institutional Approval Signatories Matrix ({{ isset($signatories) && $signatories->isNotEmpty() ? $signatories->count() . ' Tiers' : 'Dynamic Approval Chain' }})</h6>
                            <p class="text-muted extra-small m-0">Canonical signatory records stored in sys_document_signatories.</p>
                        </div>
                        @if(strtolower((string) $proposal->status) !== 'approved')
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small fw-bold" id="btnQuickSuggestSignatories" title="Auto-suggest signatories from past proposal">
                                    <i class="mdi mdi-auto-fix me-1 text-primary"></i> Suggest Past Signatories
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 extra-small fw-bold" data-bs-toggle="modal" data-bs-target="#configureSignatoriesModal">
                                    <i class="mdi mdi-pencil me-1"></i> Edit Signatories
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">
                        @if(isset($signatories) && $signatories->isNotEmpty())
                            @foreach($signatories as $sig)
                                @php
                                    $isSigned = in_array(strtolower($sig->status ?? ''), ['signed', 'approved', 'completed']) || !empty($sig->signed_at);
                                @endphp
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-white shadow-2xs {{ $isSigned ? 'border-success-subtle bg-success-subtle bg-opacity-25' : '' }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="extra-small text-uppercase fw-bold text-muted">{{ !empty($sig->role_label) ? $sig->role_label : 'Signatory' }}</span>
                                            @if($isSigned)
                                                <span class="badge bg-success text-white font-mono extra-small">
                                                    <i class="mdi mdi-check-decagram me-0.5"></i> Signed
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border font-mono extra-small">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                        <div class="fw-bold text-dark fs-6 mb-1">{{ $sig->signatory_name ?: ($sig->employee?->formal_name ?? '—') }}</div>
                                        <div class="text-muted extra-small mb-2">{{ $sig->designation ?: ($sig->employee?->designation ?? 'Authorized Signatory') }}</div>
                                        <div class="border-top pt-2 mt-2 extra-small font-mono">
                                            @if($isSigned && $sig->signed_at)
                                                <div class="text-success fw-semibold">
                                                    <i class="mdi mdi-clock-check-outline me-1"></i> {{ \Carbon\Carbon::parse($sig->signed_at)->format('M d, Y h:i A') }}
                                                </div>
                                                @if($sig->signature_hash)
                                                    <div class="text-muted text-truncate mt-1" style="max-width: 100%; font-size: 11px;">
                                                        <i class="mdi mdi-shield-check text-success me-0.5"></i> {{ $sig->signature_hash }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted">
                                                    <i class="mdi mdi-clock-outline me-1"></i> Awaiting formal signature
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @php
                                $suggestedFallback = \App\Http\Controllers\DSS\RIE\ExtensionProposalController::resolveSuggestedSignatories($proposal)['signatories'] ?? [];
                            @endphp
                            @foreach($suggestedFallback as $idx => $tier)
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="extra-small text-uppercase fw-bold text-muted">{{ $tier['role_label'] ?? ($idx === 0 ? 'Prepared by:' : 'Signatory') }}</span>
                                            <span class="badge bg-secondary text-white font-mono extra-small">Suggested</span>
                                        </div>
                                        <div class="fw-bold text-dark fs-6">{{ $tier['name'] ?: '— Awaiting Selection —' }}</div>
                                        <div class="text-muted extra-small mb-1">{{ $tier['designation'] }}</div>
                                        <div class="extra-small text-muted font-mono">{{ $tier['office'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- TAB 4: PDF PREVIEW -->
                <div class="tab-pane fade {{ $activeTab === 'pdf' ? 'show active' : 'd-none' }}" id="pdf">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 border flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-inline-flex">
                                <i class="mdi mdi-file-pdf-box fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark extra-small text-uppercase">Official Surigao Del Norte State University Format</div>
                                <div class="text-muted extra-small">ISO 9001:2015 Compliant Institutional 7-Page Extension Proposal</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('dss.rie.proposals.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 shadow-2xs fw-semibold d-flex align-items-center gap-1">
                                <i class="mdi mdi-fullscreen fs-6"></i>
                                <span>Fullscreen PDF</span>
                            </a>
                        </div>
                    </div>
                    <div class="border rounded-4 overflow-hidden shadow-2xs bg-light">
                        <iframe src="{{ route('dss.rie.proposals.pdf', $proposal->getRouteKey()) }}" style="width: 100%; height: 900px; border: none;"></iframe>
                    </div>
                </div>

                <!-- TAB 5: CENTRALIZED ATTACHMENTS & SCANS (1-LINER COMPONENT) -->
                <div class="tab-pane fade {{ $activeTab === 'attachments' ? 'show active' : 'd-none' }}" id="attachments">
                    <x-dss.document-attachments :model="$proposal" :auditTrails="$auditTrails" />
                </div>

                <!-- TAB 6: PART IV ACCOMPLISHMENT & TERMINAL REPORT -->
                @php
                    $acc = $proposal->metadata['accomplishment_report'] ?? [];
                    $implStatus = $acc['implementation_status'] ?? 'Not Started';
                    $plannedBudget = (float) ($proposal->budget_cash_amount ?? 0);
                    $actualSpent = (float) ($acc['actual_budget_spent'] ?? 0);
                    $spentPct = $plannedBudget > 0 ? min(100, round(($actualSpent / $plannedBudget) * 100, 1)) : 0;
                @endphp
                <div class="tab-pane fade {{ $activeTab === 'accomplishment' ? 'show active' : 'd-none' }}" id="accomplishment">
                    <!-- Accomplishment Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pb-3 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold text-dark mb-0">Part IV: Post-Implementation Accomplishment &amp; Terminal Report</h5>
                                <span class="badge {{ $implStatus === 'Completed' ? 'bg-success' : ($implStatus === 'Ongoing' ? 'bg-primary' : 'bg-secondary') }} rounded-pill px-2.5 py-1">
                                    <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>{{ $implStatus }}
                                </span>
                            </div>
                            <p class="text-muted extra-small mb-0">
                                Institutional documentation of actual community reach, fund liquidation, and tangible outcomes achieved for <strong>{{ $proposal->title }}</strong>.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('dss.rie.proposals.accomplishment.pdf', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-danger px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5 shadow-2xs">
                                <i class="mdi mdi-file-pdf-box fs-6"></i>
                                <span>Generate Narrative PDF</span>
                            </a>
                            <a href="{{ route('dss.rie.proposals.accomplishment.pdf', ['id' => $proposal->getRouteKey(), 'download' => 1]) }}" class="btn btn-sm btn-outline-secondary px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-2xs">
                                <i class="mdi mdi-download fs-6"></i>
                                <span>Download PDF</span>
                            </a>
                            <a href="{{ route('dss.rie.proposals.accomplishment.report', $proposal->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-outline-dark px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-2xs">
                                <i class="mdi mdi-printer fs-6 text-muted"></i>
                                <span>HTML Print</span>
                            </a>
                            @if(!empty($acc['updated_at']))
                                <span class="text-muted extra-small font-mono">
                                    <i class="mdi mdi-clock-outline me-1"></i>Last recorded: {{ \Carbon\Carbon::parse($acc['updated_at'])->format('M d, Y h:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Metric Overview Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="metric-stat-card border-emerald">
                                <div>
                                    <div class="text-muted extra-small text-uppercase fw-bold mb-1">Actual Beneficiaries Reached</div>
                                    <div class="fs-3 fw-bold text-dark mb-0 font-mono">
                                        {{ number_format((int) ($acc['actual_beneficiaries_total'] ?? 0)) }}
                                    </div>
                                    <div class="extra-small text-muted mt-1">
                                        Target planned: <span class="fw-semibold text-dark">{{ $proposal->beneficiaries ? Str::limit($proposal->beneficiaries, 25) : 'General Public' }}</span>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between text-muted extra-small">
                                    <span><i class="mdi mdi-gender-male text-primary"></i> Male: {{ (int) ($acc['actual_beneficiaries_male'] ?? 0) }}</span>
                                    <span><i class="mdi mdi-gender-female text-danger"></i> Female: {{ (int) ($acc['actual_beneficiaries_female'] ?? 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="metric-stat-card">
                                <div>
                                    <div class="text-muted extra-small text-uppercase fw-bold mb-1">Fund Liquidation / Disbursed</div>
                                    <div class="fs-3 fw-bold text-dark mb-0 font-mono">
                                        ₱{{ number_format($actualSpent, 2) }}
                                    </div>
                                    <div class="extra-small text-muted mt-1">
                                        Approved allocation: <span class="fw-semibold text-dark">₱{{ number_format($plannedBudget, 2) }}</span>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <div class="d-flex justify-content-between extra-small mb-1">
                                        <span class="text-muted">Utilization Rate</span>
                                        <span class="fw-bold font-mono text-dark">{{ $spentPct }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $spentPct }}%;" aria-valuenow="{{ $spentPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="metric-stat-card">
                                <div>
                                    <div class="text-muted extra-small text-uppercase fw-bold mb-1">Terminal Evidence &amp; Attachments</div>
                                    <div class="fs-3 fw-bold text-dark mb-0 font-mono">
                                        {{ $proposal->uploadedDocuments->count() }} <span class="fs-6 fw-normal text-muted">files</span>
                                    </div>
                                    <div class="extra-small text-muted mt-1">
                                        Photos, attendance sheets &amp; reports
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <a href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'attachments']) }}" class="btn btn-sm btn-light border w-100 extra-small fw-semibold py-1">
                                        <i class="mdi mdi-upload me-1 text-primary"></i>Upload Scans in Attachments Tab
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accomplishment Form -->
                    <form id="accomplishmentReportForm" class="bg-light p-4 rounded-3 border">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Implementation Status <span class="text-danger">*</span></label>
                                <select name="implementation_status" class="form-select form-select-sm">
                                    <option value="Not Started" {{ $implStatus === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="Ongoing" {{ $implStatus === 'Ongoing' ? 'selected' : '' }}>Ongoing (In Progress)</option>
                                    <option value="Completed" {{ $implStatus === 'Completed' ? 'selected' : '' }}>Completed (Fully Executed)</option>
                                    <option value="Delayed" {{ $implStatus === 'Delayed' ? 'selected' : '' }}>Delayed / Deferred</option>
                                    <option value="Terminated" {{ $implStatus === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Date of Project Completion</label>
                                <input type="date" name="date_completed" class="form-control form-control-sm font-mono" value="{{ $acc['date_completed'] ?? '' }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Total Beneficiaries Reached</label>
                                <input type="number" name="actual_beneficiaries_total" class="form-control form-control-sm font-mono" value="{{ $acc['actual_beneficiaries_total'] ?? '' }}" placeholder="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Male Count</label>
                                <input type="number" name="actual_beneficiaries_male" class="form-control form-control-sm font-mono" value="{{ $acc['actual_beneficiaries_male'] ?? '' }}" placeholder="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Female Count</label>
                                <input type="number" name="actual_beneficiaries_female" class="form-control form-control-sm font-mono" value="{{ $acc['actual_beneficiaries_female'] ?? '' }}" placeholder="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-dark">Total Budget Liquidated (₱)</label>
                                <input type="number" step="0.01" name="actual_budget_spent" class="form-control form-control-sm font-mono" value="{{ $acc['actual_budget_spent'] ?? '' }}" placeholder="0.00" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Specific Community Sectors / Demographics</label>
                            <input type="text" name="actual_beneficiaries_sectors" class="form-control form-control-sm" value="{{ $acc['actual_beneficiaries_sectors'] ?? '' }}" placeholder="e.g. Fisherfolk, Women Micro-entrepreneurs, Senior Citizens, 4Ps Beneficiaries">
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2.5 mb-3 bg-white border rounded-3">
                            <div>
                                <span class="fw-bold text-dark small"><i class="mdi mdi-file-document-edit text-primary me-1"></i>Comprehensive Narrative Documentation Sections</span>
                                <div class="text-muted extra-small">Interleaved narrative report interleaving text, field outputs, and photographic documentation.</div>
                            </div>
                            <button type="button" id="btnAutoDraftAccomplishment" class="btn btn-xs btn-outline-primary px-3 py-1.5 fw-bold rounded-pill shadow-2xs d-flex align-items-center gap-1">
                                <i class="mdi mdi-auto-fix"></i>
                                <span>Auto-Draft All Narratives (AI)</span>
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark mb-1">1. Executive Overview &amp; Background Narrative</label>
                            <textarea name="executive_narrative" id="txtExecutiveNarrative" rows="3" class="form-control form-control-sm" placeholder="Introductory narrative describing project rationale, host community context, and developmental challenges addressed...">{{ $acc['executive_narrative'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark mb-1">2. Activity Execution &amp; Training Delivery Narrative</label>
                            <textarea name="activity_narrative" id="txtActivityNarrative" rows="3" class="form-control form-control-sm" placeholder="Chronological narrative of stakeholder consultations, modular lectures, practical laboratory exercises, and participant engagement...">{{ $acc['activity_narrative'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark mb-1">3. Major Accomplishments &amp; Technology Transfer Narrative</label>
                            <textarea name="summary_accomplishments" id="txtSummaryAccomplishments" rows="4" class="form-control form-control-sm" placeholder="Detailed narrative of tangible deliverables produced, modules completed, competencies developed, and turned-over assets...">{{ $acc['summary_accomplishments'] ?? '' }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Problems / Constraints Encountered</label>
                                <textarea name="problems_encountered" id="txtProblemsEncountered" rows="3" class="form-control form-control-sm" placeholder="Weather interruptions, logistical constraints, community availability issues...">{{ $acc['problems_encountered'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Solutions &amp; Remedial Measures Adopted</label>
                                <textarea name="solutions_adopted" id="txtSolutionsAdopted" rows="3" class="form-control form-control-sm" placeholder="Adjustments made, contingency scheduling, additional stakeholder coordination...">{{ $acc['solutions_adopted'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-dark">Community Impact &amp; Beneficiary Testimonials</label>
                            <textarea name="impact_feedback" id="txtImpactFeedback" rows="3" class="form-control form-control-sm" placeholder="Excerpts of community feedback, changes observed in partner practices, sustained benefits, and partner endorsements...">{{ $acc['impact_feedback'] ?? '' }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <button type="button" id="btnSaveAccomplishment" class="btn btn-sm btn-success px-4 py-2 fw-bold shadow-2xs d-flex align-items-center gap-1.5">
                                <i class="mdi mdi-content-save-check fs-6"></i>
                                <span>Save Accomplishment Report</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONFIGURE SIGNATORIES & DTS ROUTING MODAL -->
<div class="modal fade" id="configureSignatoriesModal" tabindex="-1" aria-labelledby="configureSignatoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-height: 92vh;">
        <form id="configureSignatoriesForm" class="modal-content border-0 shadow-lg rounded-3 overflow-hidden" style="max-height: 90vh;">
            @csrf
            <div class="modal-header border-bottom py-3 px-4 bg-light flex-shrink-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-inline-flex">
                        <i class="mdi mdi-account-cog-outline fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6" id="configureSignatoriesModalLabel">Configure Approval Signatories &amp; DTS Routing</h5>
                        <p class="text-muted extra-small m-0">Dynamic approval workflow: Add, remove, or re-order signatories. Steps 1 to N automatically map to DTS transit routes.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="overflow-y: auto; max-height: calc(90vh - 135px);">
                    <div class="alert alert-info py-2 px-3 extra-small mb-3 d-flex align-items-center gap-2 rounded-3 border-0 bg-info-subtle text-info-emphasis">
                        <i class="mdi mdi-information-outline fs-5"></i>
                        <div>
                            Selecting an employee from the institutional directory automatically synchronizes their full name, formal designation, and department office to both the <strong>Document Signatories Matrix</strong> and <strong>DTS Transit Route</strong>.<br>
                            <span class="d-inline-block mt-1 text-dark"><i class="mdi mdi-lightbulb-on-outline text-warning me-1"></i><strong>Hierarchy Convention:</strong> Tiers from Proponent up to <em>College Dean</em> are college-specific. Levels after the Dean (<em>Extension Directorate &bull; VP RIE &bull; University President</em>) are institutional defaults true to all proposals across the university.</span>
                        </div>
                    </div>

                    @php
                        $sigPrepared = isset($signatories) ? $signatories->firstWhere('tier_role', 'prepared') : null;
                        $approvalSigs = isset($signatories) ? $signatories->filter(fn($s) => $s->tier_role !== 'prepared')->values() : collect();

                        if ($approvalSigs->isEmpty()) {
                            $resolvedSuggestions = \App\Http\Controllers\DSS\RIE\ExtensionProposalController::resolveSuggestedSignatories($proposal)['signatories'] ?? [];
                            $approvalSigs = collect($resolvedSuggestions)->filter(fn($s) => ($s['tier_role'] ?? '') !== 'prepared')->map(function ($s) {
                                return (object) [
                                    'id' => null,
                                    'role_label' => $s['role_label'] ?? '',
                                    'tier_role' => $s['tier_role'] ?? '',
                                    'employee_id' => $s['employee_id'] ?? null,
                                    'employee' => !empty($s['employee_id']) ? \App\Models\Employee::find($s['employee_id']) : null,
                                    'signatory_name' => $s['name'] ?? '',
                                    'designation' => $s['designation'] ?? '',
                                    'office' => $s['office'] ?? '',
                                    'alignment' => $s['alignment'] ?? 'left',
                                    'position_column' => $s['position_column'] ?? 1,
                                ];
                            });
                        }

                        $proponentOffice = $proposal->unit?->unit_name 
                            ?: ($proposal->academicProgram?->department?->unit_name 
                            ?: ($proposal->projectLeaderEmployee?->department?->unit_name ?: 'Project Proponent / Extension Team'));
                    @endphp

                    <!-- TIER 0: PREPARED BY (PINNED PROPONENT) -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-secondary-subtle text-secondary border font-mono extra-small">Proponent</span>
                            <span class="text-muted extra-small">Project Leader (Auto-assigned &amp; Pinned)</span>
                        </div>
                        <div class="fw-bold text-dark fs-6">{{ $proposal->project_leader_name }}</div>
                        <div class="text-muted extra-small">{{ $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader') }} &bull; {{ $proponentOffice }}</div>

                        <input type="hidden" name="signatories[0][id]" value="{{ $sigPrepared?->id }}">
                        <input type="hidden" name="signatories[0][role_label]" value="Prepared by">
                        <input type="hidden" name="signatories[0][tier_role]" value="prepared">
                        <input type="hidden" name="signatories[0][employee_id]" value="{{ $proposal->project_leader_employee_id }}">
                        <input type="hidden" name="signatories[0][name]" value="{{ $proposal->project_leader_name }}">
                        <input type="hidden" name="signatories[0][designation]" value="{{ $proposal->prepared_by_designation ?: ($proposal->project_leader_title ?: 'Project Leader') }}">
                        <input type="hidden" name="signatories[0][office]" value="{{ $proponentOffice }}">
                    </div>

                    <!-- DYNAMIC APPROVAL SIGNATORIES REPEATER LIST -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold text-dark extra-small text-uppercase m-0">Sequential Approval Chain (DTS Transit Steps)</h6>
                            <span class="badge bg-light text-muted border font-mono extra-small" id="signatoriesCountBadge">{{ $approvalSigs->count() }} Approval Tiers</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 fw-bold d-flex align-items-center gap-1 shadow-2xs" id="btnSuggestPastSignatories" title="Auto-suggest signatories from past proposal or official matrix">
                                <i class="mdi mdi-auto-fix"></i>
                                <span>Suggest Past Signatories</span>
                            </button>
                            <button type="button" class="btn btn-xs btn-primary rounded-pill px-2 py-1 fw-bold d-flex align-items-center gap-1 shadow-2xs btn-add-signatory-trigger">
                                <i class="mdi mdi-account-plus-outline"></i>
                                <span>Add Signatory</span>
                            </button>
                        </div>
                    </div>

                    <div id="signatoriesRepeaterList" class="vstack gap-3">
                        @foreach($approvalSigs as $idx => $sig)
                            @php
                                $stepNum = $idx + 1;
                                $arrayIdx = $idx + 1;
                                $sigEmp = $sig->employee ?? null;
                            @endphp
                            <div class="signatory-row border rounded-3 p-3 bg-white shadow-2xs position-relative" data-index="{{ $arrayIdx }}">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="drag-handle me-0.5" title="Drag to reorder step (drag-and-drop)">
                                            <i class="mdi mdi-drag-vertical fs-5"></i>
                                        </span>
                                        <span class="badge bg-primary text-white font-mono rounded-pill px-2 py-1 extra-small step-badge">Step {{ $stepNum }}</span>
                                        <span class="text-muted extra-small font-mono step-transit-label">DTS Transit Step {{ $stepNum }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-up" title="Move Step Up" {{ $idx === 0 ? 'disabled' : '' }}>
                                            <i class="mdi mdi-arrow-up"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-down" title="Move Step Down" {{ $idx === $approvalSigs->count() - 1 ? 'disabled' : '' }}>
                                            <i class="mdi mdi-arrow-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 btn-remove-signatory" title="Remove this signatory">
                                            <i class="mdi mdi-trash-can-outline me-0.5"></i> Remove
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="signatories[{{ $arrayIdx }}][id]" class="sig-id" value="{{ $sig->id ?? '' }}">
                                <input type="hidden" name="signatories[{{ $arrayIdx }}][tier_role]" class="sig-tier-role" value="{{ $sig->tier_role ?? 'reviewed' }}">

                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Role / Action Label <span class="text-muted fw-normal">(Optional)</span></label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="signatories[{{ $arrayIdx }}][role_label]" class="form-control form-control-sm font-sans fw-bold sig-role-label" value="{{ $sig->role_label ?? '' }}" placeholder="e.g. Reviewed by (or leave blank)">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Preset Actions"></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm extra-small">
                                                <li><h6 class="dropdown-header text-uppercase extra-small text-primary fw-bold">Collegiate Levels (Up to Dean)</h6></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Reviewed by" data-tier="reviewed" data-align="left" data-col="1">Reviewed by (Extension Head / Coordinator)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Noted by:" data-tier="noted" data-align="left" data-col="1">Noted by: (College Dean)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Endorsed by" data-tier="endorsed" data-align="left" data-col="1">Endorsed by</a></li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li><h6 class="dropdown-header text-uppercase extra-small text-success fw-bold">Institutional Levels (Official Matrix)</h6></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Concurred:" data-tier="concurred" data-align="left" data-col="1">Concurred: (Extension Directorate)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommended" data-align="center" data-col="2">Recommending Approval: (VP RIE)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of allotment:" data-tier="certified_allotment" data-align="left" data-col="1">Certified as to allotment: (Budget Officer) [Left]</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of funds:" data-tier="certified_funds" data-align="right" data-col="2">Certified as to funds: (Accountant) [Right]</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommended" data-align="center" data-col="2">Recommending Approval: (CAO)</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Approved:" data-tier="approved" data-align="center" data-col="2">Approved: (University President)</a></li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified Correct" data-tier="certified" data-align="left" data-col="1">Certified Correct</a></li>
                                                <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Accepted by" data-tier="accepted" data-align="left" data-col="1">Accepted by</a></li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li><a class="dropdown-item py-1 preset-role-option text-danger" href="#" data-label="" data-tier="reviewed" data-align="left" data-col="1"><i class="mdi mdi-close-circle-outline me-1"></i> No Label (Leave Blank)</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Select Employee from Directory <span class="text-danger">*</span></label>
                                        <select name="signatories[{{ $arrayIdx }}][employee_id]" class="form-select form-select-sm signatory-employee-select2" data-placeholder="Search employee by name, title, or office..." data-no-select2="true" required>
                                            @if(!empty($sig->employee_id) && $sigEmp)
                                                <option value="{{ $sig->employee_id }}" selected>{{ $sigEmp->full_name }} ({{ $sigEmp->designation }})</option>
                                            @elseif(!empty($sig->signatory_name))
                                                <option value="{{ $sig->employee_id ?? '' }}" selected>{{ $sig->signatory_name }}{{ $sig->designation ? ' ('.$sig->designation.')' : '' }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label extra-small text-muted mb-1 fw-semibold">Layout Position <span class="text-muted fw-normal">(PDF)</span></label>
                                        @php
                                            $currentAlign = $sig->alignment ?? 'left';
                                            $labelLower = strtolower($sig->role_label ?? '');
                                            if (str_contains($labelLower, 'funds')) {
                                                $currentAlign = 'right';
                                            } elseif (str_contains($labelLower, 'allotment')) {
                                                $currentAlign = 'left';
                                            } elseif (str_contains($labelLower, 'recommend') || str_contains($labelLower, 'approve')) {
                                                $currentAlign = 'center';
                                            }
                                        @endphp
                                        <select name="signatories[{{ $arrayIdx }}][alignment]" class="form-select form-select-sm font-sans sig-alignment">
                                            <option value="left" {{ $currentAlign === 'left' ? 'selected' : '' }}>Left Column</option>
                                            <option value="center" {{ $currentAlign === 'center' ? 'selected' : '' }}>Centered (Full Width)</option>
                                            <option value="right" {{ $currentAlign === 'right' ? 'selected' : '' }}>Right Column (Pair)</option>
                                        </select>
                                        <input type="hidden" name="signatories[{{ $arrayIdx }}][position_column]" class="sig-col" value="{{ $currentAlign === 'right' ? 2 : ($currentAlign === 'center' ? 2 : 1) }}">
                                    </div>
                                </div>

                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Formal Name <span class="text-muted fw-normal extra-small">(Auto-filled)</span></label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][name]" class="form-control form-control-sm font-sans fw-semibold sig-name" value="{{ $sig->signatory_name ?? '' }}" placeholder="Auto-populated upon selecting employee">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Official Designation / Title</label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][designation]" class="form-control form-control-sm font-sans sig-designation" value="{{ $sig->designation ?? '' }}" placeholder="Auto-populated from employee (editable)">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small text-muted mb-1">Office / Department (DTS Route Step)</label>
                                        <input type="text" name="signatories[{{ $arrayIdx }}][office]" class="form-control form-control-sm font-sans sig-office" value="{{ $sig->office ?? '' }}" placeholder="Auto-populated from employee (editable)">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- REPEATER CONTROLS: ADD BUTTON -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mt-3">
                        <div class="extra-small text-muted">
                            <i class="mdi mdi-plus-circle-outline me-1 text-primary"></i> Need extra review, endorsement, or approval steps? Click <strong>Add Signatory</strong>.
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-2xs btn-add-signatory-trigger" id="btnAddSignatoryRow">
                            <i class="mdi mdi-account-plus-outline fs-6"></i>
                            <span>Add Signatory</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer py-2 px-4 bg-light border-top flex-shrink-0 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-dss-primary rounded-pill px-4 fw-bold shadow-xs d-flex align-items-center gap-1" id="btnSaveSignatories">
                        <i class="mdi mdi-check-bold fs-6"></i>
                        <span>Save Signatories &amp; Routing Steps</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT PART I. BASIC INFORMATION -->
<div class="modal fade" id="editPartIModal" tabindex="-1" aria-labelledby="editPartIModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-2.5 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 d-inline-flex">
                        <i class="mdi mdi-clipboard-edit-outline fs-5 text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="editPartIModalLabel">Edit Part I. Basic Information</h6>
                        <span class="extra-small opacity-75 font-mono">ACTIVITY PROPOSAL &bull; SNSU Official Form</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dss.rie.proposals.update', $proposal->getRouteKey()) }}" method="POST" id="editPartIForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- A. Title of Activity -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark extra-small">A. Title of the Activity <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm fw-bold text-uppercase" value="{{ $proposal->title }}" required>
                        </div>

                        <!-- B. Banner Program -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark extra-small d-block mb-1">B. Banner Program <span class="text-danger">*</span></label>
                            <div class="p-3 bg-light rounded-3 border">
                                @php
                                    $pList = isset($bannerPrograms) && $bannerPrograms instanceof \Illuminate\Support\Collection
                                        ? $bannerPrograms->pluck('name')->toArray()
                                        : (is_array($bannerPrograms ?? null) ? $bannerPrograms : \App\Models\RIE\ExtensionBannerProgram::active()->ordered()->pluck('name')->toArray());
                                    if (empty($pList)) {
                                        $pList = ['TECHASSIST', 'FOODPRO', 'AGRIYAMAN', 'MODFARM 360', 'GOVeRN', 'SUSTINAHAN', 'RENEWTECH', 'ReStat-QUIIP', 'STEAMUP', 'TOURGABAY', 'MSMEntri', 'KATUTUBO', 'ECOREVIVE', 'GADLIGHT', 'WELLNESS', 'TUBIG-SIGURADO'];
                                    }
                                    $halfP = (int) ceil(count($pList) / 2);
                                    $col1P = array_slice($pList, 0, $halfP);
                                    $col2P = array_slice($pList, $halfP);
                                    $curBanner = $proposal->banner_program;
                                    $isOtherBanner = $curBanner === 'Others' || (!in_array($curBanner, $pList) && !empty($curBanner));
                                @endphp
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        @foreach($col1P as $bp)
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="banner_program" value="{{ $bp }}" id="modal_bp_{{ \Illuminate\Support\Str::slug($bp) }}" {{ $curBanner === $bp ? 'checked' : '' }}>
                                                <label class="form-check-label extra-small text-dark fw-medium" for="modal_bp_{{ \Illuminate\Support\Str::slug($bp) }}">
                                                    {{ $bp }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6">
                                        @foreach($col2P as $bp)
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="banner_program" value="{{ $bp }}" id="modal_bp_{{ \Illuminate\Support\Str::slug($bp) }}" {{ $curBanner === $bp ? 'checked' : '' }}>
                                                <label class="form-check-label extra-small text-dark fw-medium" for="modal_bp_{{ \Illuminate\Support\Str::slug($bp) }}">
                                                    {{ $bp }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="banner_program" value="Others" id="modal_bp_others" {{ $isOtherBanner ? 'checked' : '' }}>
                                        <label class="form-check-label extra-small text-dark fw-semibold" for="modal_bp_others">
                                            Others:
                                        </label>
                                    </div>
                                    <input type="text" name="banner_program_other" class="form-control form-control-sm" placeholder="Specify other banner program..." value="{{ $proposal->banner_program_other ?: ($isOtherBanner ? $curBanner : '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- C. Extension Agenda -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark extra-small d-block mb-1">C. Extension Agenda <span class="text-muted fw-normal">(Check all that apply)</span></label>
                            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">
                                @php
                                    $agendaOptions = isset($extensionAgendas) && $extensionAgendas instanceof \Illuminate\Support\Collection
                                        ? $extensionAgendas->pluck('name')->toArray()
                                        : (is_array($extensionAgendas ?? null) ? $extensionAgendas : \App\Models\RIE\ExtensionAgenda::active()->ordered()->pluck('name')->toArray());
                                    if (empty($agendaOptions)) {
                                        $agendaOptions = [
                                            'Transformative Learning',
                                            'Livelihood Generation',
                                            'Biodiversity Conservation',
                                            'Climate Change Adoption & Risk Management',
                                            'Good Governance'
                                        ];
                                    }
                                    $curAgendas = $proposal->agenda_priorities ?? [];
                                @endphp
                                @foreach($agendaOptions as $aOpt)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agenda_priorities[]" value="{{ $aOpt }}" id="modal_agenda_{{ $loop->index }}" {{ in_array($aOpt, $curAgendas) ? 'checked' : '' }}>
                                        <label class="form-check-label extra-small text-dark fw-medium" for="modal_agenda_{{ $loop->index }}">
                                            {{ $aOpt }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- D. SDG Addressed -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark extra-small d-block mb-1">
                                D. SDG Addressed <span class="text-muted fw-normal">(Click inline UN Sustainable Development Goal tiles to select/deselect)</span>
                            </label>
                            <div class="p-3 bg-light rounded-3 border">
                                @include('dss.rie.extension_proposals.partials.sdg_inline_tiles', [
                                    'mode' => 'checkbox',
                                    'inputName' => 'sdg_alignments[selected][]',
                                    'idPrefix' => 'modal_sdg',
                                    'selectedSdgs' => $proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 17],
                                ])
                            </div>
                        </div>

                        <!-- E. Project Leader & Gender -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-dark extra-small">E. Project Leader <span class="text-danger">*</span></label>
                            <input type="text" name="project_leader_name" class="form-control form-control-sm fw-semibold" value="{{ $proposal->project_leader_name }}" required>
                            <input type="hidden" name="project_leader_title" value="{{ $proposal->project_leader_title ?: 'Project Leader' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark extra-small">Gender <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small ms-1">Auto fetch / profile</span></label>
                            <input type="text" name="project_leader_gender" class="form-control form-control-sm" value="{{ $proposal->project_leader_gender ?: 'Female' }}" placeholder="e.g. Female / Male">
                        </div>

                        <!-- F. Implementing Colleges -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark extra-small">F. Implementing Colleges <span class="text-muted fw-normal">(Comma or semicolon separated)</span></label>
                            @php
                                $collegesVal = is_array($proposal->implementing_colleges) ? implode('; ', $proposal->implementing_colleges) : ($proposal->implementing_colleges ?: 'College of Computing and Information Sciences');
                            @endphp
                            <input type="text" name="implementing_colleges" class="form-control form-control-sm" value="{{ $collegesVal }}">
                        </div>

                        <!-- G. Collaborating Agencies -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark extra-small m-0">G. Collaborating Agencies</label>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2" id="btnModalAddAgency">
                                    <i class="mdi mdi-plus me-1"></i> Add Agency
                                </button>
                            </div>
                            <div class="table-responsive border rounded-2">
                                <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="modalAgenciesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%;">Collaborating Agency Name</th>
                                            <th>Role Description</th>
                                            <th style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalAgenciesTableBody">
                                        @php
                                            $collabList = $proposal->resolved_collaborating_agencies;
                                            if (empty($collabList) && !empty($proposal->collaborating_agencies)) {
                                                $collabList = [['agency' => $proposal->collaborating_agencies, 'role' => 'Partner Agency']];
                                            }
                                            if (empty($collabList)) {
                                                $collabList = [['agency' => '', 'role' => '']];
                                            }
                                        @endphp
                                        @foreach($collabList as $agIdx => $ag)
                                            <tr>
                                                <td><input type="text" name="collaborating_agencies_data[{{ $agIdx }}][agency]" class="form-control form-control-sm border-0" value="{{ $ag['agency'] ?? '' }}" placeholder="Agency name..."></td>
                                                <td><input type="text" name="collaborating_agencies_data[{{ $agIdx }}][role]" class="form-control form-control-sm border-0" value="{{ $ag['role'] ?? '' }}" placeholder="Partner role..."></td>
                                                <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-modal-agency"><i class="mdi mdi-trash-can-outline fs-6"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- H. Target Beneficiaries Breakdown -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark extra-small m-0">H. Target Beneficiaries Breakdown</label>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2" id="btnModalAddBeneficiary">
                                    <i class="mdi mdi-plus me-1"></i> Add Group
                                </button>
                            </div>
                            <div class="table-responsive border rounded-2">
                                <table class="table table-bordered table-sm align-middle mb-0 extra-small" id="modalBeneficiariesTable">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="text-align: left;">Beneficiary Group / Community</th>
                                            <th style="width: 100px;">Male</th>
                                            <th style="width: 100px;">Female</th>
                                            <th style="width: 100px;">Total</th>
                                            <th style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalBeneficiariesTableBody">
                                        @php
                                            $bens = $proposal->resolved_target_beneficiaries;
                                            if (empty($bens)) {
                                                $bens = [['name' => $proposal->beneficiaries ?: 'Partner Beneficiaries', 'male' => 30, 'female' => 20, 'total' => 50]];
                                            }
                                        @endphp
                                        @foreach($bens as $bIdx => $bn)
                                            <tr class="modal-ben-row">
                                                <td><input type="text" name="target_beneficiaries_data[{{ $bIdx }}][name]" class="form-control form-control-sm border-0" value="{{ $bn['name'] ?? '' }}" placeholder="Beneficiary name..."></td>
                                                <td><input type="number" name="target_beneficiaries_data[{{ $bIdx }}][male]" class="form-control form-control-sm border-0 text-center modal-ben-m" value="{{ $bn['male'] ?? 0 }}" min="0"></td>
                                                <td><input type="number" name="target_beneficiaries_data[{{ $bIdx }}][female]" class="form-control form-control-sm border-0 text-center modal-ben-f" value="{{ $bn['female'] ?? 0 }}" min="0"></td>
                                                <td><input type="number" name="target_beneficiaries_data[{{ $bIdx }}][total]" class="form-control form-control-sm border-0 text-center fw-bold modal-ben-t" value="{{ $bn['total'] ?? 0 }}" readonly></td>
                                                <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-modal-ben"><i class="mdi mdi-trash-can-outline fs-6"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- I. Estimated Budget Requirement -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark extra-small">I. Internal Fund Source (Institution Share)</label>
                            <input type="text" name="fund_source_internal" class="form-control form-control-sm" value="{{ $proposal->fund_source_internal ?: ($proposal->budget_institution_share ?: 'In-Kind (University Service Vehicle & Faculty Expertise)') }}" placeholder="e.g. In-Kind / Amount">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark extra-small">External Fund Source (Partner Share)</label>
                            <input type="text" name="fund_source_external" class="form-control form-control-sm" value="{{ $proposal->fund_source_external ?: ($proposal->budget_partner_share ?: 'In-Kind (Partner Venue & Provisions)') }}" placeholder="e.g. In-Kind / Amount">
                        </div>

                        <!-- J. Duration -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark extra-small">J. Start Date</label>
                            <input type="date" name="duration_start" id="modal_duration_start" class="form-control form-control-sm" value="{{ $proposal->duration_start ? $proposal->duration_start->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark extra-small">End Date</label>
                            <input type="date" name="duration_end" id="modal_duration_end" class="form-control form-control-sm" value="{{ $proposal->duration_end ? $proposal->duration_end->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark extra-small">No. of Months <span class="text-muted fw-normal">(Integer only)</span></label>
                            <input type="number" name="duration_months" id="modal_duration_months" class="form-control form-control-sm fw-bold" value="{{ $proposal->duration_months ?: 1 }}" min="1" step="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-4 bg-light border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-xs">
                        <i class="mdi mdi-content-save-check me-1"></i> Save Basic Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CLIENT TEMPLATE FOR NEW SIGNATORY ROW -->
<template id="signatoryRowTemplate">
    <div class="signatory-row border rounded-3 p-3 bg-white shadow-2xs position-relative" data-index="__INDEX__">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-1">
                <span class="drag-handle me-0.5" title="Drag to reorder step (drag-and-drop)">
                    <i class="mdi mdi-drag-vertical fs-5"></i>
                </span>
                <span class="badge bg-primary text-white font-mono rounded-pill px-2 py-1 extra-small step-badge">Step __STEP__</span>
                <span class="text-muted extra-small font-mono step-transit-label">DTS Transit Step __STEP__</span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-up" title="Move Step Up">
                    <i class="mdi mdi-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 btn-move-signatory-down" title="Move Step Down">
                    <i class="mdi mdi-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 btn-remove-signatory" title="Remove this signatory">
                    <i class="mdi mdi-trash-can-outline me-0.5"></i> Remove
                </button>
            </div>
        </div>

        <input type="hidden" name="signatories[__INDEX__][id]" class="sig-id" value="">
        <input type="hidden" name="signatories[__INDEX__][tier_role]" class="sig-tier-role" value="reviewed">

        <div class="row g-2 align-items-center mb-2">
            <div class="col-md-4">
                <label class="form-label extra-small text-muted mb-1 fw-semibold">Role / Action Label <span class="text-muted fw-normal">(Optional)</span></label>
                <div class="input-group input-group-sm">
                    <input type="text" name="signatories[__INDEX__][role_label]" class="form-control form-control-sm font-sans fw-bold sig-role-label" value="" placeholder="e.g. Reviewed by (or leave blank)">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Preset Actions"></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm extra-small">
                        <li><h6 class="dropdown-header text-uppercase extra-small text-primary fw-bold">Collegiate Levels (Up to Dean)</h6></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Reviewed by" data-tier="reviewed" data-align="left" data-col="1">Reviewed by (Extension Head / Coordinator)</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Noted by:" data-tier="noted" data-align="left" data-col="1">Noted by: (College Dean)</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Endorsed by" data-tier="endorsed" data-align="left" data-col="1">Endorsed by</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><h6 class="dropdown-header text-uppercase extra-small text-success fw-bold">Institutional Levels (Official Matrix)</h6></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Concurred:" data-tier="concurred" data-align="left" data-col="1">Concurred: (Extension Directorate)</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommended" data-align="center" data-col="2">Recommending Approval: (VP RIE)</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of allotment:" data-tier="certified_allotment" data-align="left" data-col="1">Certified as to allotment: (Budget Officer) [Left]</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified as to availability of funds:" data-tier="certified_funds" data-align="right" data-col="2">Certified as to funds: (Accountant) [Right]</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Recommending Approval:" data-tier="recommended" data-align="center" data-col="2">Recommending Approval: (CAO)</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Approved:" data-tier="approved" data-align="center" data-col="2">Approved: (University President)</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Certified Correct" data-tier="certified" data-align="left" data-col="1">Certified Correct</a></li>
                        <li><a class="dropdown-item py-1 preset-role-option" href="#" data-label="Accepted by" data-tier="accepted" data-align="left" data-col="1">Accepted by</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-1 preset-role-option text-danger" href="#" data-label="" data-tier="reviewed" data-align="left" data-col="1"><i class="mdi mdi-close-circle-outline me-1"></i> No Label (Leave Blank)</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-5">
                <label class="form-label extra-small text-muted mb-1 fw-semibold">Select Employee from Directory <span class="text-danger">*</span></label>
                <select name="signatories[__INDEX__][employee_id]" class="form-select form-select-sm signatory-employee-select2" data-placeholder="Search employee by name, title, or office..." data-no-select2="true" required>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small text-muted mb-1 fw-semibold">Layout Position <span class="text-muted fw-normal">(PDF)</span></label>
                <select name="signatories[__INDEX__][alignment]" class="form-select form-select-sm font-sans sig-alignment">
                    <option value="left" selected>Left Column</option>
                    <option value="center">Centered (Full Width)</option>
                    <option value="right">Right Column (Pair)</option>
                </select>
                <input type="hidden" name="signatories[__INDEX__][position_column]" class="sig-col" value="1">
            </div>
        </div>

        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label extra-small text-muted mb-1">Formal Name <span class="text-muted fw-normal extra-small">(Auto-filled)</span></label>
                <input type="text" name="signatories[__INDEX__][name]" class="form-control form-control-sm font-sans fw-semibold sig-name" value="" placeholder="Auto-populated upon selecting employee">
            </div>
            <div class="col-md-4">
                <label class="form-label extra-small text-muted mb-1">Official Designation / Title</label>
                <input type="text" name="signatories[__INDEX__][designation]" class="form-control form-control-sm font-sans sig-designation" value="" placeholder="Auto-populated from employee (editable)">
            </div>
            <div class="col-md-4">
                <label class="form-label extra-small text-muted mb-1">Office / Department (DTS Route Step)</label>
                <input type="text" name="signatories[__INDEX__][office]" class="form-control form-control-sm font-sans sig-office" value="" placeholder="Auto-populated from employee (editable)">
            </div>
        </div>
    </div>
</template>
@endsection

@push('styles')
<style>
/* Word Workbench Outer Container */
.word-workbench-container {
    background-color: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08);
}

/* Word App Header (Authentic Office 365 Blue) */
.word-app-header {
    background: linear-gradient(135deg, #185abd 0%, #0c3c82 100%);
    color: #ffffff;
    padding: 10px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 9px;
    border-top-right-radius: 9px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

/* Word Editor Box (Per-Section Integrated Word Toolbar & Editor) */
.word-editor-box {
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    background: #ffffff;
    transition: all 0.2s ease-in-out;
    overflow: hidden;
    margin-bottom: 12px;
}

.word-editor-box:hover {
    border-color: #94a3b8;
}

.word-editor-box:focus-within {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}

.word-section-toolbar {
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 3px 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

.word-editor-box:focus-within .word-section-toolbar {
    background-color: #eff6ff !important;
    border-bottom-color: #bfdbfe !important;
}

.word-section {
    scroll-margin-top: 80px;
}

.word-ribbon-group {
    display: flex;
    align-items: center;
    gap: 2px;
    padding-right: 6px;
    border-right: 1px solid #e2e8f0;
}

.word-editor-box:focus-within .word-ribbon-group {
    border-right-color: #dbeafe;
}

.word-btn {
    border: 1px solid transparent;
    background: transparent;
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 12.5px;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 26px;
    transition: all 0.12s ease-in-out;
}

.word-btn:hover {
    background-color: #e2e8f0;
    color: #0f172a;
    border-color: #cbd5e1;
}

.word-btn:active, .word-btn.active {
    background-color: #cbd5e1;
    color: #1e3a8a;
    border-color: #94a3b8;
}

/* Word Document Canvas (A4 Sheet Simulation) */
.word-canvas {
    background-color: #cbd5e1;
    padding: 36px 16px;
    display: flex;
    justify-content: center;
    overflow-x: auto;
    min-height: 750px;
}

.word-page {
    background-color: #ffffff;
    width: 100%;
    min-height: 1120px;
    padding: 56px 64px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    color: #1e293b;
    font-family: 'Aptos', 'Segoe UI', Calibri, Arial, sans-serif;
    font-size: 14.5px;
    line-height: 1.65;
    position: relative;
}

.word-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e40af;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border-bottom: 1.5px solid #dbeafe;
    padding-bottom: 5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.word-editable-content {
    border: none !important;
    border-radius: 0 0 5px 5px !important;
    box-shadow: none !important;
    font-size: 14.5px;
    outline: none !important;
    background-color: #ffffff !important;
}

/* Dynamic Resizable Table */
.dynamic-people-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #ffffff;
}

.dynamic-people-table th {
    position: relative;
    background-color: #f1f5f9;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
    user-select: none;
}

.dynamic-people-table td {
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    vertical-align: middle;
    background-color: #ffffff;
}

.dynamic-people-table tr:hover td {
    background-color: #f8fafc;
}

.col-resizer {
    position: absolute;
    top: 0;
    right: -3px;
    width: 7px;
    bottom: 0;
    cursor: col-resize;
    user-select: none;
    z-index: 10;
}

.col-resizer:hover, .col-resizer.resizing {
    background-color: #2563eb;
}

.table-cell-input {
    width: 100%;
    border: 1px solid transparent;
    background: transparent;
    font-size: 12px;
    padding: 4px 6px;
    border-radius: 3px;
    color: #1e293b;
    line-height: 1.45;
    transition: all 0.15s ease;
    resize: vertical;
    vertical-align: middle;
}

.table-cell-input:hover {
    border-color: #cbd5e1;
    background-color: #f8fafc;
}

.table-cell-input:focus {
    border-color: #2563eb;
    background-color: #ffffff;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.15);
    outline: none;
}

.name-cell-wrapper {
    width: 100%;
    position: relative;
}

.name-cell-wrapper .select2-container--bootstrap-5 {
    width: 100% !important;
}

.name-cell-wrapper .select2-container--bootstrap-5 .select2-selection--single {
    min-height: 29px !important;
    height: 29px !important;
    padding: 2px 8px !important;
    font-size: 12px !important;
    border-radius: 4px !important;
    border-color: #cbd5e1;
    display: flex;
    align-items: center;
    background-color: #ffffff;
}

.name-cell-wrapper .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 23px !important;
    padding-left: 0 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
}

.name-cell-wrapper .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.15) !important;
}

.name-cell-wrapper {
    width: 100%;
    position: relative;
}

.name-cell-wrapper .select2-container--bootstrap-5 .select2-selection--single {
    min-height: 29px !important;
    height: 29px !important;
    padding: 2px 8px !important;
    font-size: 12px !important;
    border-radius: 4px !important;
    border-color: #cbd5e1;
    display: flex;
    align-items: center;
    background-color: #ffffff;
}

.name-cell-wrapper .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 23px !important;
    padding-left: 0 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
}

.name-cell-wrapper .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.15) !important;
}

.btn-toggle-person-mode {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    border-color: #cbd5e1;
    color: #64748b;
    background-color: #ffffff;
}

.btn-toggle-person-mode:hover {
    background-color: #f1f5f9;
    color: #2563eb;
    border-color: #94a3b8;
}

/* 17 UN SDGs Interactive Grid — Styled globally via .un-sdg-tile in public/css/material-able.css */

/* Dynamic Schedule Table */
.dynamic-schedule-table, .dynamic-budget-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #ffffff;
    font-size: 12px;
}

.dynamic-schedule-table th, .dynamic-budget-table th {
    position: relative;
    background-color: #f1f5f9;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}

.dynamic-schedule-table td, .dynamic-budget-table td {
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    vertical-align: middle;
    background-color: #ffffff;
}

/* Dynamic Gantt Chart Specific Styles */
.dynamic-gantt-table {
    table-layout: auto !important;
}
.dynamic-gantt-table thead tr:first-child th {
    background-color: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    text-align: center;
}
.dynamic-gantt-table thead tr:nth-child(2) th {
    background-color: #f1f5f9;
    font-weight: 600;
    text-align: center;
    padding: 4px 6px;
}
.dynamic-gantt-table .gantt-cell {
    position: relative;
    padding: 3px !important;
    text-align: center;
    vertical-align: middle !important;
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease;
}
.dynamic-gantt-table .gantt-cell.gantt-active {
    background-color: #f3e8ff !important;
}
.dynamic-gantt-table .gantt-cell.gantt-inactive:hover {
    background-color: #f8fafc;
}
.dynamic-gantt-table .gantt-cell.gantt-inactive:hover .gantt-bar-empty {
    opacity: 1;
}
.dynamic-gantt-table .gantt-bar-fill {
    background: linear-gradient(135deg, #c084fc, #a855f7);
    border: 1px solid #9333ea;
    border-radius: 4px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 13px;
    box-shadow: 0 1px 2px rgba(147, 51, 234, 0.25);
    transition: transform 0.1s ease;
}
.dynamic-gantt-table .gantt-cell:hover .gantt-bar-fill {
    transform: scale(0.97);
}
.dynamic-gantt-table .gantt-bar-empty {
    height: 28px;
    border: 1.5px dashed #cbd5e1;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.15s ease;
}
.dynamic-gantt-table .period-label-text {
    border-bottom: 1px dashed #94a3b8;
    padding: 1px 4px;
    cursor: text;
    border-radius: 2px;
}
.dynamic-gantt-table .period-label-text:focus {
    outline: none;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.25);
    border-bottom-color: #a855f7;
}

.dynamic-budget-table .budget-header-row td {
    background-color: #f8fafc !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    font-style: italic;
    border-top: 1.5px solid #94a3b8;
    border-bottom: 1.5px solid #94a3b8;
}

.dynamic-budget-table .budget-subtotal-row td {
    background-color: #f1f5f9 !important;
    font-weight: 700 !important;
    color: #1e293b !important;
}

.dynamic-budget-table .budget-grand-total-row td {
    background-color: #eff6ff !important;
    font-weight: 800 !important;
    color: #1e40af !important;
    font-size: 13px !important;
    border-top: 2px solid #2563eb !important;
    border-bottom: 2px solid #2563eb !important;
}

.dynamic-budget-table tr.active-budget-row td {
    background-color: #f0f7ff !important;
    box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.2);
}

/* Dynamic Org Chart Node Tree */
.org-tree-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.org-tree-node {
    border-radius: 6px;
    padding: 8px 14px;
    min-width: 260px;
    max-width: 380px;
    text-align: center;
    position: relative;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: all 0.15s ease;
}

.org-tree-node input {
    background: transparent;
    border: none;
    text-align: center;
    color: inherit;
    outline: none;
}

.org-tree-node input:hover, .org-tree-node input:focus {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

#orgDropzone.dragover {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
}

/* Signatory Drag & Drop Styling */
.drag-handle {
    cursor: grab;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: color 0.15s ease, transform 0.15s ease;
    user-select: none;
    padding: 2px 4px;
    border-radius: 4px;
}
.drag-handle:hover {
    color: #2563eb;
    background-color: #eff6ff;
}
.drag-handle:active {
    cursor: grabbing;
}
.signatory-row.sortable-ghost {
    opacity: 0.35;
    background-color: #eff6ff !important;
    border: 2px dashed #3b82f6 !important;
}
.signatory-row.sortable-chosen {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    border-color: #2563eb !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isProposalEditable = {{ $isEditable ? 'true' : 'false' }};
    let isDirty = false;
    let autoSaveTimer = null;
    let isSaving = false;
    const AUTOSAVE_DELAY_MS = 2000;

    function markDirtyAndTriggerAutoSave(delay = AUTOSAVE_DELAY_MS) {
        if (!isProposalEditable) return;

        isDirty = true;
        const indicator = document.getElementById('saveStatusIndicator');
        if (indicator && !isSaving) {
            indicator.className = 'badge bg-warning-subtle text-warning border border-warning-subtle ms-2 font-mono extra-small';
            indicator.innerHTML = '<i class="mdi mdi-circle-edit-outline me-1"></i> Unsaved changes...';
        }

        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            if (isDirty && !isSaving) {
                saveTechnicalDescription({ isAutoSave: true });
            }
        }, delay);
    }

    if (!isProposalEditable) {
        document.querySelectorAll('.word-editable-content').forEach(el => {
            el.setAttribute('contenteditable', 'false');
            el.style.cursor = 'default';
        });
    }

    // ------------------------------------------------------------------
    // 1. DIGITAL SIGNATURE SUBMIT
    // ------------------------------------------------------------------
    const signForm = document.getElementById('signProposalForm');
    if (signForm) {
        signForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(signForm);

            fetch("{{ route('dss.rie.proposals.sign', $proposal->getRouteKey()) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Signed Successfully',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Signing Failed',
                        text: data.message || 'Unknown error'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to complete signature request.'
                });
            });
        });
    }

    // ------------------------------------------------------------------
    // 2. DYNAMIC PEOPLE INVOLVED TABLE DATA MODEL & RENDERING
    // ------------------------------------------------------------------
    let peopleTableData = @json($proposal->resolved_people_involved);
    if (!peopleTableData || !Array.isArray(peopleTableData.columns) || !Array.isArray(peopleTableData.rows)) {
        peopleTableData = {
            columns: [
                { id: 'name', label: 'Person / Faculty Member', width: 30 },
                { id: 'role', label: 'Project Role / Title', width: 25 },
                { id: 'responsibilities', label: 'Assigned Duties & Responsibilities', width: 45 }
            ],
            rows: []
        };
    }
    @php $initProponents = $proposal->proponents ?: []; @endphp
    const initialProponents = @json($initProponents);

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeColumnWidths() {
        const total = peopleTableData.columns.reduce((sum, col) => sum + (parseFloat(col.width) || 0), 0);
        if (total > 0 && Math.abs(total - 100) > 0.5) {
            peopleTableData.columns.forEach(col => {
                col.width = Math.round((col.width / total) * 100 * 10) / 10;
            });
        }
    }

    function syncCellInputsToData() {
        const inputs = document.querySelectorAll('.table-cell-input');
        inputs.forEach(input => {
            const rowId = input.dataset.rowId;
            const colId = input.dataset.colId;
            const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
            if (row && colId) {
                row[colId] = input.value;
            }
        });

        // Also sync Select2 inputs for rows where col is 'name' and type is 'faculty'
        $('.table-cell-select2').each(function () {
            const rowId = $(this).data('rowId');
            const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
            if (row) {
                const val = $(this).val();
                if (val !== null && val !== undefined && val !== '') {
                    row.name = val;
                }
            }
        });
    }

    function initSelect2OnRowElement($select) {
        if (!$select.length) return;
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        $select.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search employee from university directory...',
            allowClear: true,
            ajax: {
                url: '{{ route("dss.select2.employees") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: (data.results || []).map(function (emp) {
                            return {
                                id: emp.full_name || emp.name,
                                employee_id: emp.id,
                                text: emp.text || emp.full_name,
                                name: emp.full_name || emp.name,
                                designation: emp.designation
                            };
                        }),
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            const data = e.params.data;
            const rowId = $(this).data('rowId');
            const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
            if (row) {
                row.name = data.name || data.text || '';
                row.employee_id = data.employee_id || '';
                row.type = 'faculty';

                // Auto fill role if blank or default Member / Team Member
                if ((!row.role || row.role === 'Member' || row.role === 'Extensionist / Team Member') && data.designation) {
                    row.role = data.designation;
                    const roleInput = document.querySelector(`input[data-row-id="${row.id}"][data-col-id="role"]`);
                    if (roleInput) {
                        roleInput.value = data.designation;
                    }
                }
            }
            markDirtyAndTriggerAutoSave();
        }).on('select2:clear', function () {
            const rowId = $(this).data('rowId');
            const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
            if (row) {
                row.name = '';
                row.employee_id = '';
            }
            markDirtyAndTriggerAutoSave();
        });
    }

    function renderPeopleTable() {
        const table = document.getElementById('peopleInvolvedTable');
        if (!table) return;

        // Proportional colgroup
        let colgroup = table.querySelector('colgroup');
        if (!colgroup) {
            colgroup = document.createElement('colgroup');
            table.insertBefore(colgroup, table.firstChild);
        }
        colgroup.innerHTML = '';
        peopleTableData.columns.forEach(col => {
            const colEl = document.createElement('col');
            colEl.style.width = col.width + '%';
            colgroup.appendChild(colEl);
        });
        const actionCol = document.createElement('col');
        actionCol.style.width = '38px';
        colgroup.appendChild(actionCol);

        // Header Row
        const headerRow = document.getElementById('peopleTableHeaderRow');
        headerRow.innerHTML = '';
        peopleTableData.columns.forEach((col, idx) => {
            const th = document.createElement('th');
            th.style.position = 'relative';
            th.style.width = col.width + '%';

            const isCoreCol = ['name', 'role', 'responsibilities'].includes(col.id);
            let headerHtml = `<div class="d-flex align-items-center justify-content-between">
                <span class="col-label text-truncate" title="${escapeHtml(col.label)}">${escapeHtml(col.label)}</span>`;
            if (!isCoreCol) {
                headerHtml += `<button type="button" class="btn btn-link text-danger p-0 ms-1 btn-delete-col" data-col-id="${col.id}" title="Delete Column">
                    <i class="mdi mdi-close-circle fs-6"></i>
                </button>`;
            }
            headerHtml += `</div>`;
            th.innerHTML = headerHtml;

            // Resizer handle (between columns)
            if (idx < peopleTableData.columns.length - 1) {
                const resizer = document.createElement('div');
                resizer.className = 'col-resizer';
                resizer.dataset.colIndex = idx;
                th.appendChild(resizer);
            }

            headerRow.appendChild(th);
        });

        // Action Header
        const actionTh = document.createElement('th');
        actionTh.className = 'text-center';
        actionTh.style.width = '38px';
        actionTh.innerHTML = '<i class="mdi mdi-cog-outline text-muted"></i>';
        headerRow.appendChild(actionTh);

        // Body Rows
        const tbody = document.getElementById('peopleTableBody');
        tbody.innerHTML = '';

        if (peopleTableData.rows.length === 0) {
            const emptyTr = document.createElement('tr');
            emptyTr.innerHTML = `<td colspan="${peopleTableData.columns.length + 1}" class="text-center py-4 text-muted extra-small">
                <i class="mdi mdi-account-group-outline fs-3 d-block mb-1 opacity-50"></i>
                No team members listed yet. Click <strong>+ Add Faculty</strong> or <strong>+ Add Custom / Student</strong> to add members.
            </td>`;
            tbody.appendChild(emptyTr);
        } else {
            peopleTableData.rows.forEach(row => {
                const tr = document.createElement('tr');
                tr.dataset.rowId = row.id;

                peopleTableData.columns.forEach(col => {
                    const td = document.createElement('td');
                    const val = row[col.id] || '';
                    if (col.id === 'responsibilities') {
                        td.innerHTML = `<textarea class="table-cell-input" rows="2" data-row-id="${row.id}" data-col-id="${col.id}" placeholder="Enter specific duties and responsibilities...">${escapeHtml(val)}</textarea>`;
                    } else if (col.id === 'name') {
                        // Check if row is manual or faculty
                        const isManual = (row.type === 'manual') || (val && (val.toLowerCase().includes('student') || val.toLowerCase().includes('volunteer')));
                        if (row.type === undefined) {
                            row.type = isManual ? 'manual' : 'faculty';
                        }

                        let nameControlHtml = '';
                        if (row.type === 'manual') {
                            nameControlHtml = `<input type="text" class="table-cell-input manual-name-input font-sans fw-semibold" data-row-id="${row.id}" data-col-id="name" value="${escapeHtml(val)}" placeholder="e.g. Selected BSIT Students, Student Volunteers...">`;
                        } else {
                            nameControlHtml = `
                                <select class="form-select form-select-sm table-cell-select2 proponent-select2" data-row-id="${row.id}" style="width: 100%;">
                                    ${val ? `<option value="${escapeHtml(val)}" selected>${escapeHtml(val)}</option>` : '<option value="">Search faculty member from directory...</option>'}
                                </select>
                            `;
                        }

                        td.innerHTML = `
                            <div class="d-flex align-items-center gap-1 name-cell-wrapper">
                                <div class="flex-grow-1 overflow-hidden">
                                    ${nameControlHtml}
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-toggle-person-mode" data-row-id="${row.id}" title="${row.type === 'manual' ? 'Switch to Faculty Directory (Select2)' : 'Switch to Custom Text (Students/Groups)'}">
                                    <i class="mdi mdi-swap-horizontal"></i>
                                </button>
                            </div>
                        `;
                    } else {
                        td.innerHTML = `<input type="text" class="table-cell-input" data-row-id="${row.id}" data-col-id="${col.id}" value="${escapeHtml(val)}" placeholder="${escapeHtml(col.label)}...">`;
                    }
                    tr.appendChild(td);
                });

                // Action TD
                const actionTd = document.createElement('td');
                actionTd.className = 'text-center align-middle';
                actionTd.innerHTML = `<button type="button" class="btn btn-link text-danger p-0 delete-row-btn" data-row-id="${row.id}" title="Remove Person">
                    <i class="mdi mdi-trash-can-outline fs-6"></i>
                </button>`;
                tr.appendChild(actionTd);

                tbody.appendChild(tr);
            });

            // Initialize Select2 on all newly rendered select controls
            $(tbody).find('.table-cell-select2').each(function () {
                initSelect2OnRowElement($(this));
            });
        }

        // Count Badge
        const badge = document.getElementById('peopleRowCountBadge');
        if (badge) {
            badge.textContent = `${peopleTableData.rows.length} ${peopleTableData.rows.length === 1 ? 'Person' : 'Persons'}`;
        }

        // Initialize drag resizers and row delete buttons
        initResizers();
        attachRowActionListeners();
    }

    function initResizers() {
        const resizers = document.querySelectorAll('.col-resizer');
        const table = document.getElementById('peopleInvolvedTable');
        if (!table) return;

        resizers.forEach(resizer => {
            resizer.addEventListener('mousedown', function (e) {
                e.preventDefault();
                syncCellInputsToData();

                const colIndex = parseInt(this.dataset.colIndex, 10);
                const leftCol = peopleTableData.columns[colIndex];
                const rightCol = peopleTableData.columns[colIndex + 1];
                if (!leftCol || !rightCol) return;

                const startX = e.pageX;
                const startLeftWidth = leftCol.width;
                const startRightWidth = rightCol.width;
                const tableWidth = table.offsetWidth;

                resizer.classList.add('resizing');
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none';

                function onMouseMove(moveEvent) {
                    const deltaX = moveEvent.pageX - startX;
                    const deltaPercent = (deltaX / tableWidth) * 100;

                    const minWidth = 10;
                    let newLeft = startLeftWidth + deltaPercent;
                    let newRight = startRightWidth - deltaPercent;

                    if (newLeft >= minWidth && newRight >= minWidth) {
                        leftCol.width = Math.round(newLeft * 10) / 10;
                        rightCol.width = Math.round(newRight * 10) / 10;

                        // Live DOM width updates
                        const colElements = table.querySelectorAll('colgroup col');
                        if (colElements[colIndex]) colElements[colIndex].style.width = leftCol.width + '%';
                        if (colElements[colIndex + 1]) colElements[colIndex + 1].style.width = rightCol.width + '%';

                        const thElements = table.querySelectorAll('thead th');
                        if (thElements[colIndex]) thElements[colIndex].style.width = leftCol.width + '%';
                        if (thElements[colIndex + 1]) thElements[colIndex + 1].style.width = rightCol.width + '%';
                    }
                }

                function onMouseUp() {
                    resizer.classList.remove('resizing');
                    document.body.style.cursor = '';
                    document.body.style.userSelect = '';
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);

                    normalizeColumnWidths();
                    renderPeopleTable();
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });
    }

    function attachRowActionListeners() {
        // Toggle person mode (Select2 vs Manual Text)
        document.querySelectorAll('.btn-toggle-person-mode').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                syncCellInputsToData();
                const rowId = this.dataset.rowId;
                const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
                if (row) {
                    row.type = (row.type === 'manual' ? 'faculty' : 'manual');
                    renderPeopleTable();
                    markDirtyAndTriggerAutoSave();
                }
            });
        });

        // Delete row
        document.querySelectorAll('.delete-row-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                syncCellInputsToData();
                const rowId = this.dataset.rowId;
                peopleTableData.rows = peopleTableData.rows.filter(r => String(r.id) !== String(rowId));
                renderPeopleTable();
                markDirtyAndTriggerAutoSave();
            });
        });

        // Delete custom column
        document.querySelectorAll('.btn-delete-col').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                syncCellInputsToData();
                const colId = this.dataset.colId;
                const col = peopleTableData.columns.find(c => c.id === colId);
                if (!col) return;

                Swal.fire({
                    title: 'Remove Column?',
                    text: `Delete column "${col.label}" and its cell data?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((res) => {
                    if (res.isConfirmed) {
                        peopleTableData.columns = peopleTableData.columns.filter(c => c.id !== colId);
                        peopleTableData.rows.forEach(r => {
                            delete r[colId];
                        });
                        normalizeColumnWidths();
                        renderPeopleTable();
                        markDirtyAndTriggerAutoSave();
                    }
                });
            });
        });

        // Sync inputs on blur/change
        document.querySelectorAll('.table-cell-input').forEach(input => {
            input.addEventListener('input', function () {
                const rowId = this.dataset.rowId;
                const colId = this.dataset.colId;
                const row = peopleTableData.rows.find(r => String(r.id) === String(rowId));
                if (row) {
                    row[colId] = this.value;
                }
                markDirtyAndTriggerAutoSave();
            });
        });
    }

    function addFacultyRow() {
        syncCellInputsToData();
        const nextId = 'row_' + Date.now();
        const newRow = {
            id: nextId,
            name: '',
            role: 'Member',
            type: 'faculty',
            responsibilities: 'Participates in planning, execution, monitoring, and institutional documentation.'
        };
        peopleTableData.columns.forEach(col => {
            if (!newRow.hasOwnProperty(col.id)) {
                newRow[col.id] = '';
            }
        });
        peopleTableData.rows.push(newRow);
        renderPeopleTable();
        markDirtyAndTriggerAutoSave();

        setTimeout(() => {
            const $select = $(`select[data-row-id="${nextId}"]`);
            if ($select.length) {
                $select.select2('open');
            }
        }, 120);
    }

    function addManualRow() {
        syncCellInputsToData();
        const nextId = 'row_' + Date.now();
        const newRow = {
            id: nextId,
            name: 'Selected BSIT Students',
            role: 'Member',
            type: 'manual',
            responsibilities: 'Technical assistance, logistics support, attendance tracking, and user assistance.'
        };
        peopleTableData.columns.forEach(col => {
            if (!newRow.hasOwnProperty(col.id)) {
                newRow[col.id] = '';
            }
        });
        peopleTableData.rows.push(newRow);
        renderPeopleTable();
        markDirtyAndTriggerAutoSave();

        setTimeout(() => {
            const input = document.querySelector(`input[data-row-id="${nextId}"][data-col-id="name"]`);
            if (input) {
                input.focus();
                input.select();
            }
        }, 50);
    }

    function addPersonColumn() {
        syncCellInputsToData();
        Swal.fire({
            title: 'Add New Column',
            text: 'Enter column name (e.g. Campus / Dept, Contact No, Deliverables):',
            input: 'text',
            inputPlaceholder: 'Column label...',
            showCancelButton: true,
            confirmButtonColor: '#185abd',
            confirmButtonText: 'Add Column',
            cancelButtonText: 'Cancel',
            inputValidator: (val) => {
                if (!val || !val.trim()) {
                    return 'Please enter a valid column name.';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const colLabel = result.value.trim();
                const colId = 'col_' + Date.now();
                const newColWidth = 20;

                const scale = (100 - newColWidth) / 100;
                peopleTableData.columns.forEach(col => {
                    col.width = Math.max(10, Math.round(col.width * scale * 10) / 10);
                });

                peopleTableData.columns.push({
                    id: colId,
                    label: colLabel,
                    width: newColWidth
                });

                peopleTableData.rows.forEach(r => {
                    r[colId] = '';
                });

                normalizeColumnWidths();
                renderPeopleTable();
                markDirtyAndTriggerAutoSave();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Column "${colLabel}" added. Drag borders to resize!`,
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }

    function syncProponents() {
        Swal.fire({
            title: 'Re-sync Proponents from Part I?',
            text: 'This will reset the People Involved table rows using the proponents from Part I.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#185abd',
            confirmButtonText: 'Sync Proponents',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const newRows = [];
                if (Array.isArray(initialProponents) && initialProponents.length > 0) {
                    initialProponents.forEach((prop, idx) => {
                        const role = prop.role || 'Member';
                        let duty = 'Participates in planning, execution, monitoring, and institutional documentation.';
                        if (role.toLowerCase().includes('leader') && !role.toLowerCase().includes('asst')) {
                            duty = 'Overall project management, cloud infrastructure setup, stakeholder liaison, and final reporting.';
                        } else if (role.toLowerCase().includes('asst') || role.toLowerCase().includes('assistant')) {
                            duty = 'Operational coordination, scheduling, stakeholder communication, and field activity supervision.';
                        }
                        const pName = prop.name || '';
                        const pType = prop.type || (pName.toLowerCase().includes('student') || pName.toLowerCase().includes('volunteer') ? 'manual' : 'faculty');
                        newRows.push({
                            id: 'row_' + (idx + 1),
                            name: pName,
                            role: role,
                            type: pType,
                            employee_id: prop.employee_id || null,
                            responsibilities: duty
                        });
                    });
                } else {
                    newRows.push({
                        id: 'row_1',
                        name: '{{ addslashes($proposal->project_leader_name) }}',
                        role: '{{ addslashes($proposal->project_leader_title ?: "Project Leader") }}',
                        type: 'faculty',
                        employee_id: '{{ $proposal->project_leader_employee_id }}',
                        responsibilities: 'Overall project management, cloud infrastructure setup, stakeholder liaison, and project governance.'
                    });
                    @if($proposal->asst_project_leader_name)
                    newRows.push({
                        id: 'row_2',
                        name: '{{ addslashes($proposal->asst_project_leader_name) }}',
                        role: '{{ addslashes($proposal->asst_project_leader_title ?: "Assistant Project Leader") }}',
                        type: 'faculty',
                        responsibilities: 'Operational coordination, scheduling, stakeholder communication, and field activity supervision.'
                    });
                    @endif
                }
                peopleTableData.rows = newRows;
                renderPeopleTable();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Proponents synchronized successfully!',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }

    // Button event listeners
    const btnAddFaculty = document.getElementById('btnDynamicAddFaculty');
    const btnAddManual = document.getElementById('btnDynamicAddManual');
    if (btnAddFaculty) btnAddFaculty.addEventListener('click', addFacultyRow);
    if (btnAddManual) btnAddManual.addEventListener('click', addManualRow);

    const btnAddCol = document.getElementById('btnDynamicAddCol');
    if (btnAddCol) btnAddCol.addEventListener('click', addPersonColumn);

    const btnSync = document.getElementById('btnSyncFromProponents');
    if (btnSync) btnSync.addEventListener('click', syncProponents);

    // Initial render of People table
    renderPeopleTable();

    // ------------------------------------------------------------------
    // 3. SDG SELECTION & INTERACTIVE CARDS WITH AUTO-GENERATIVE NARRATIVE
    // ------------------------------------------------------------------
    @php
        $initSelectedSdgs  = array_map('intval', (array) ($proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 16]));
        $initIndirectSdgs  = array_map('intval', (array) ($proposal->resolved_sdg_alignments['indirect'] ?? []));
    @endphp
    let selectedSdgs  = @json($initSelectedSdgs);   // Direct / Primary SDGs
    let indirectSdgs  = @json($initIndirectSdgs);   // Indirect / Tangential SDGs
    const proposalTitle = @json($proposal->title ?? '');
    const schoolName    = @json(($institution ?? null)?->institution_name ?? 'Surigao del Norte State University');
    const schoolAcronym = @json(($institution ?? null)?->school_acronym   ?? 'SNSU');

    const sdgKnowledgeBase = {
        1: { title: "No Poverty", focus: "socioeconomic upliftment and digital inclusion for vulnerable community sectors" },
        2: { title: "Zero Hunger", focus: "community nutrition monitoring, sustainable agricultural distribution, and food security mechanisms" },
        3: { title: "Good Health & Well-being", focus: "campus health protocols, preventative safety systems, and rapid emergency reporting" },
        4: { title: "Quality Education", focus: "equitable, inclusive digital learning environments, automated learner tracking, and institutional capability-building" },
        5: { title: "Gender Equality", focus: "safe, inclusive, and gender-equitable access to technology and educational opportunities" },
        6: { title: "Clean Water & Sanitation", focus: "institutional hygiene awareness, resource monitoring, and sanitation management protocols" },
        7: { title: "Affordable & Clean Energy", focus: "energy-efficient digital workflows, low-power computational systems, and sustainability" },
        8: { title: "Decent Work & Economic Growth", focus: "technological skills acquisition, workforce digital readiness, and institutional efficiency" },
        9: { title: "Industry, Innovation & Infrastructure", focus: "technological innovation, modern digital infrastructure, and reliable cloud-based service delivery" },
        10: { title: "Reduced Inequalities", focus: "bridging the digital divide between urban and rural partner schools through accessible digital platforms" },
        11: { title: "Sustainable Cities & Communities", focus: "cultivating safer, smarter, and resilient campus communities through proactive digital monitoring" },
        12: { title: "Responsible Consumption & Production", focus: "paperless administrative workflows, resource conservation, and sustainable institutional practices" },
        13: { title: "Climate Action", focus: "minimizing environmental footprint through digital transformation and paperless communications" },
        14: { title: "Life Below Water", focus: "marine biodiversity conservation awareness and aquatic environmental tracking" },
        15: { title: "Life on Land", focus: "terrestrial environmental protection, green campus initiatives, and digital environmental stewardship" },
        16: { title: "Peace, Justice & Strong Institutions", focus: "institutional transparency, student safety protection, and verifiable governance records" },
        17: { title: "Partnerships for the Goals", focus: "collaborative multistakeholder partnerships between the university, local government units, and partner schools" }
    };

    const editorNarrative = document.getElementById('editor_relevance_narrative');
    const syncBadge = document.getElementById('sdgNarrativeSyncBadge');
    const btnAiNarrative = document.getElementById('btnAiGenerateNarrative');
    let userHasCustomizedNarrative = false;

    if (editorNarrative) {
        editorNarrative.addEventListener('input', function () {
            userHasCustomizedNarrative = true;
            if (syncBadge) {
                syncBadge.innerHTML = '<i class="mdi mdi-pencil me-1"></i> User Revised (Manual Edit)';
                syncBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill extra-small font-sans py-1 px-2';
            }
        });
    }

    function generateLocalNarrative(directArray, indirectArray) {
        if ((!directArray || directArray.length === 0) && (!indirectArray || indirectArray.length === 0)) {
            return '<p class="text-muted fst-italic">Please select at least one UN Sustainable Development Goal above to generate the relevance narrative.</p>';
        }

        // Read live editor values for context-aware generation
        const introEl   = document.getElementById('editor_introduction');
        const objEl     = document.getElementById('editor_objectives');
        const introText = introEl ? (introEl.innerText || '').trim() : '';
        const objText   = objEl   ? (objEl.innerText   || '').trim() : '';
        const ctx       = [proposalTitle, introText, objText].filter(Boolean).join(' ');

        // Context keyword detectors
        const hasTech      = /technolog|digital|system|software|app|automat|platform|iot|sensor|rfid|network|data|comput/i.test(ctx);
        const hasEducation = /educat|learn|student|school|academ|train|workshop|curricul|faculty|teach|classroom/i.test(ctx);
        const hasHealth    = /health|medical|wellness|safety|hygiene|sanit|emerg|clinic|patient|hospital/i.test(ctx);
        const hasEnviron   = /environ|climate|ecolog|green|sustainab|forest|biodiversit|ocean|marine|wildlife/i.test(ctx);
        const hasCommunity = /communit|barangay|municipal|partner|beneficiar|stakeholder|local government|province/i.test(ctx);

        // Build direct SDG references
        const directTitles  = [];
        const directDetails = [];
        (directArray || []).forEach(num => {
            if (sdgKnowledgeBase[num]) {
                directTitles.push(`SDG ${num} \u2013 ${sdgKnowledgeBase[num].title}`);
                directDetails.push(`<strong>SDG ${num} (${sdgKnowledgeBase[num].title})</strong> through ${sdgKnowledgeBase[num].focus}`);
            }
        });

        // Build indirect SDG references
        const indirectTitles = [];
        (indirectArray || []).forEach(num => {
            if (sdgKnowledgeBase[num]) {
                indirectTitles.push(`SDG ${num} \u2013 ${sdgKnowledgeBase[num].title}`);
            }
        });

        const titleRef   = proposalTitle ? `<em>${proposalTitle}</em>` : 'this extension project';
        const directList = directTitles.join(', ') || '(none specified)';

        // --- Paragraph 1: Project-title-driven direct SDG alignment ---
        let contextClause = '';
        if (hasTech && hasEducation) {
            contextClause = ' By leveraging cutting-edge digital technologies to strengthen educational systems and enhance the operational capacity of partner schools, the project delivers a holistic, innovation-driven approach to community development.';
        } else if (hasTech && hasHealth) {
            contextClause = ' Through the deployment of smart monitoring and digital safety systems, the project creates a data-driven environment that safeguards the well-being of community beneficiaries and institutional stakeholders alike.';
        } else if (hasTech) {
            contextClause = ' Through the deployment of purposeful digital platforms and technology-enabled workflows, the project modernizes institutional processes and creates lasting socio-technical impact within its target communities.';
        } else if (hasEducation) {
            contextClause = ' The project strengthens educational systems, promotes inclusive and equitable learning environments, and builds the technical and pedagogical capacity of faculty, students, and community partners.';
        } else if (hasEnviron) {
            contextClause = ' The project promotes environmentally responsible practices and strengthens ecological stewardship within partner communities, contributing to long-term sustainable development outcomes.';
        } else if (hasCommunity) {
            contextClause = ' Through purposeful engagement with local government units, partner communities, and key stakeholders, the project bridges critical development gaps and delivers sustainable, community-driven institutional value.';
        } else {
            contextClause = ' By operationalizing university faculty expertise and purposeful community engagement, the project addresses critical developmental gaps and fosters enduring institutional resilience among its beneficiaries.';
        }

        const p1 = `${titleRef} directly advances ${schoolName}\u2019s institutional mandate to champion the United Nations Sustainable Development Goals (SDGs), with primary alignment on ${directList}.${contextClause}`;

        // --- Paragraph 2: Detailed SDG impact ---
        let p2 = '';
        if (directDetails.length > 0) {
            const detailsList = '(a) ' + directDetails.join('; (b) ');
            p2 = `Specifically, ${titleRef} creates measurable socio-technical impact by: ${detailsList}. Through this collaborative and evidence-based framework, ${schoolAcronym} reinforces its mandate of delivering inclusive, high-impact extension services that bridge digital disparities and foster enduring community resilience.`;
        }

        // --- Paragraph 3: Indirect / Tangential SDGs ---
        let p3 = '';
        if (indirectTitles.length > 0) {
            p3 = `Furthermore, while not among its primary objectives, ${titleRef} is also expected to contribute indirectly to ${indirectTitles.join(', ')}. These downstream effects reflect the interconnected nature of sustainable development, where targeted digital and educational interventions inevitably catalyze broader socio-economic and environmental benefits across the community ecosystem.`;
        }

        return [p1, p2, p3].filter(Boolean).join('<br><br>');
    }

    function autoUpdateSdgNarrative(flash = true) {
        if (!editorNarrative) return;
        const html = generateLocalNarrative(selectedSdgs, indirectSdgs);
        editorNarrative.innerHTML = html;
        userHasCustomizedNarrative = false;

        if (syncBadge) {
            syncBadge.innerHTML = `<i class="mdi mdi-auto-fix me-1"></i> Auto-synced (${selectedSdgs.length} SDGs)`;
            syncBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small font-sans py-1 px-2';
        }

        if (flash) {
            editorNarrative.style.transition = 'background-color 0.3s ease, border-color 0.3s ease';
            editorNarrative.style.backgroundColor = '#eff6ff';
            editorNarrative.style.borderColor = '#3b82f6';
            setTimeout(() => {
                editorNarrative.style.backgroundColor = '#ffffff';
                editorNarrative.style.borderColor = '';
            }, 600);
        }

        markDirtyAndTriggerAutoSave();
    }

    function updateSdgUi() {
        const badge = document.getElementById('sdgCounterBadge');
        if (badge) {
            let txt = `${selectedSdgs.length} Direct`;
            if (indirectSdgs.length > 0) txt += `, ${indirectSdgs.length} Indirect`;
            badge.textContent = txt;
        }
        document.querySelectorAll('.sdg-card-item').forEach(card => {
            const num   = parseInt(card.dataset.sdg, 10);
            const color = card.dataset.color || '#185abd';

            card.style.setProperty('--sdg-color', color);
            card.style.backgroundColor = color;

            if (selectedSdgs.includes(num)) {
                card.classList.add('selected', 'is-selected');
                card.classList.remove('indirect', 'is-indirect');
            } else if (indirectSdgs.includes(num)) {
                card.classList.remove('selected', 'is-selected');
                card.classList.add('indirect', 'is-indirect');
            } else {
                card.classList.remove('selected', 'is-selected', 'indirect', 'is-indirect');
            }
        });
    }

    document.querySelectorAll('.sdg-card-item').forEach(card => {

        // Left-click: toggle Primary (Direct)
        card.addEventListener('click', function () {
            const num = parseInt(this.dataset.sdg, 10);
            if (selectedSdgs.includes(num)) {
                // Primary → Unselected
                selectedSdgs = selectedSdgs.filter(n => n !== num);
            } else if (indirectSdgs.includes(num)) {
                // Indirect → switch to Primary
                indirectSdgs = indirectSdgs.filter(n => n !== num);
                selectedSdgs.push(num);
                selectedSdgs.sort((a, b) => a - b);
            } else {
                // Unselected → Primary
                selectedSdgs.push(num);
                selectedSdgs.sort((a, b) => a - b);
            }
            updateSdgUi();
            autoUpdateSdgNarrative(true);
        });

        // Right-click: toggle Indirect (Tangential)
        card.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            const num = parseInt(this.dataset.sdg, 10);
            if (indirectSdgs.includes(num)) {
                // Indirect → Unselected
                indirectSdgs = indirectSdgs.filter(n => n !== num);
            } else if (selectedSdgs.includes(num)) {
                // Primary → switch to Indirect
                selectedSdgs = selectedSdgs.filter(n => n !== num);
                indirectSdgs.push(num);
                indirectSdgs.sort((a, b) => a - b);
            } else {
                // Unselected → Indirect
                indirectSdgs.push(num);
                indirectSdgs.sort((a, b) => a - b);
            }
            updateSdgUi();
            autoUpdateSdgNarrative(true);
        });
    });
    updateSdgUi();

    // AI Auto-Generate Narrative Button (Full LLM / Institutional AI Engine)
    if (btnAiNarrative) {
        btnAiNarrative.addEventListener('click', function () {
            if (selectedSdgs.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Select SDGs First',
                    text: 'Please select at least one UN Sustainable Development Goal above before generating.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            const originalBtnHtml = btnAiNarrative.innerHTML;
            btnAiNarrative.disabled = true;
            btnAiNarrative.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> AI Generating...';

            fetch("{{ route('dss.rie.proposals.generate_sdg_narrative', $proposal->getRouteKey()) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    selected_sdgs: selectedSdgs,
                    indirect_sdgs: indirectSdgs
                })
            })
            .then(res => res.json())
            .then(data => {
                btnAiNarrative.disabled = false;
                btnAiNarrative.innerHTML = originalBtnHtml;

                if (data.success && data.narrative) {
                    editorNarrative.innerHTML = data.narrative;
                    userHasCustomizedNarrative = false;

                    if (syncBadge) {
                        syncBadge.innerHTML = `<i class="mdi mdi-robot-excited me-1"></i> AI Generated (${data.source || 'DSS AI'})`;
                        syncBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small font-sans py-1 px-2';
                    }

                    editorNarrative.style.transition = 'background-color 0.4s ease, border-color 0.4s ease';
                    editorNarrative.style.backgroundColor = '#ecfdf5';
                    editorNarrative.style.borderColor = '#10b981';
                    setTimeout(() => {
                        editorNarrative.style.backgroundColor = '#ffffff';
                        editorNarrative.style.borderColor = '';
                    }, 800);

                    markDirtyAndTriggerAutoSave();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `SDG Narrative generated via ${data.source || 'AI Engine'}!`,
                        text: 'You can freely edit or revise the text anytime.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    autoUpdateSdgNarrative(true);
                }
            })
            .catch(err => {
                btnAiNarrative.disabled = false;
                btnAiNarrative.innerHTML = originalBtnHtml;
                autoUpdateSdgNarrative(true);
            });
        });
    }

    // ------------------------------------------------------------------
    // 4. DYNAMIC SCHEDULE OF ACTIVITIES GANTT CHART MATRIX
    // ------------------------------------------------------------------
    let scheduleTableData = @json($proposal->resolved_schedule_of_activities);

    // Normalize scheduleTableData into Gantt structure if in old format or empty
    if (!scheduleTableData || !Array.isArray(scheduleTableData.periods) || !Array.isArray(scheduleTableData.rows)) {
        scheduleTableData = {
            type: 'gantt',
            periods: [],
            rows: []
        };
    }

    function syncScheduleInputsToData() {
        const actInputs = document.querySelectorAll('.schedule-activity-input');
        actInputs.forEach(inp => {
            const rowId = inp.dataset.rowId;
            const row = scheduleTableData.rows.find(r => String(r.id) === String(rowId));
            if (row) {
                row.activity = inp.value;
            }
        });

        const labelSpans = document.querySelectorAll('.period-label-text');
        labelSpans.forEach(sp => {
            const periodId = sp.dataset.periodId;
            const period = scheduleTableData.periods.find(p => String(p.id) === String(periodId));
            if (period) {
                period.label = sp.textContent.trim();
            }
        });
    }

    function renderScheduleTable() {
        const table = document.getElementById('scheduleTable');
        if (!table) return;

        const thead = document.getElementById('scheduleTableHead');
        const tbody = document.getElementById('scheduleTableBody');
        if (!thead || !tbody) return;

        const periods = scheduleTableData.periods || [];
        const rows = scheduleTableData.rows || [];

        // Build 2-Tier Gantt Header
        let tier1Html = `<tr>
            <th rowspan="2" style="width: 46px;" class="text-center">No.</th>
            <th rowspan="2" style="min-width: 260px;" class="text-start">Major Activities</th>`;
        periods.forEach(p => {
            tier1Html += `<th class="text-center text-primary fw-bold" style="min-width: 80px; font-size: 13px;">${p.num}</th>`;
        });
        tier1Html += `<th rowspan="2" style="width: 42px;" class="text-center"><i class="mdi mdi-cog-outline text-muted"></i></th></tr>`;

        let tier2Html = `<tr>`;
        periods.forEach(p => {
            tier2Html += `<th class="text-center" style="min-width: 80px; font-size: 11px;">
                <div class="d-flex align-items-center justify-content-center gap-1">
                    <span class="period-label-text" contenteditable="true" data-period-id="${p.id}" title="Click to edit month label">${escapeHtml(p.label || ('Month ' + p.num))}</span>
                    ${periods.length > 1 ? `<button type="button" class="btn btn-link text-danger p-0 btn-delete-sched-period" data-period-id="${p.id}" title="Delete Column"><i class="mdi mdi-close"></i></button>` : ''}
                </div>
            </th>`;
        });
        tier2Html += `</tr>`;

        thead.innerHTML = tier1Html + tier2Html;

        // Build Body Rows
        tbody.innerHTML = '';
        if (rows.length === 0) {
            const emptyTr = document.createElement('tr');
            emptyTr.innerHTML = `<td colspan="${periods.length + 3}" class="text-center py-4 text-muted extra-small">
                <i class="mdi mdi-chart-gantt fs-3 d-block mb-1 opacity-50"></i>
                No activities plotted yet. Click <strong>+ Add Activity</strong> to add rows.
            </td>`;
            tbody.appendChild(emptyTr);
        } else {
            rows.forEach((row, idx) => {
                const tr = document.createElement('tr');
                tr.dataset.rowId = row.id;

                const activeList = Array.isArray(row.active_periods) ? row.active_periods : [];

                let rowHtml = `
                    <td class="text-center fw-bold text-muted align-middle" style="width: 46px;">${idx + 1}</td>
                    <td>
                        <textarea class="table-cell-input schedule-activity-input fw-semibold" rows="2" data-row-id="${row.id}" placeholder="Activity description...">${escapeHtml(row.activity || '')}</textarea>
                    </td>`;

                periods.forEach(p => {
                    const isActive = activeList.includes(p.id);
                    if (isActive) {
                        rowHtml += `
                            <td class="gantt-cell gantt-active" data-row-id="${row.id}" data-period-id="${p.id}" title="Active Period - Click to toggle off">
                                <div class="gantt-bar-fill">
                                    <i class="mdi mdi-check-bold"></i>
                                </div>
                            </td>`;
                    } else {
                        rowHtml += `
                            <td class="gantt-cell gantt-inactive" data-row-id="${row.id}" data-period-id="${p.id}" title="Click to plot Gantt bar">
                                <div class="gantt-bar-empty">
                                    <i class="mdi mdi-plus text-primary"></i>
                                </div>
                            </td>`;
                    }
                });

                rowHtml += `
                    <td class="text-center align-middle" style="width: 42px;">
                        <button type="button" class="btn btn-link text-danger p-0 delete-sched-row-btn" data-row-id="${row.id}" title="Remove Activity">
                            <i class="mdi mdi-trash-can-outline fs-6"></i>
                        </button>
                    </td>`;

                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        const badge = document.getElementById('scheduleRowCountBadge');
        if (badge) {
            badge.textContent = `${rows.length} ${rows.length === 1 ? 'Activity' : 'Activities'}`;
        }

        attachScheduleListeners();
    }

    function attachScheduleListeners() {
        // Toggle Gantt Active Block
        document.querySelectorAll('.gantt-cell').forEach(cell => {
            cell.addEventListener('click', function () {
                const rowId = this.dataset.rowId;
                const periodId = this.dataset.periodId;
                const row = scheduleTableData.rows.find(r => String(r.id) === String(rowId));
                if (!row) return;

                if (!Array.isArray(row.active_periods)) {
                    row.active_periods = [];
                }

                const exists = row.active_periods.includes(periodId);
                if (exists) {
                    row.active_periods = row.active_periods.filter(pid => pid !== periodId);
                    this.className = 'gantt-cell gantt-inactive';
                    this.title = 'Click to plot Gantt bar';
                    this.innerHTML = '<div class="gantt-bar-empty"><i class="mdi mdi-plus text-primary"></i></div>';
                } else {
                    row.active_periods.push(periodId);
                    this.className = 'gantt-cell gantt-active';
                    this.title = 'Active Period - Click to toggle off';
                    this.innerHTML = '<div class="gantt-bar-fill"><i class="mdi mdi-check-bold"></i></div>';
                }
                markDirtyAndTriggerAutoSave();
            });
        });

        // Delete Row
        document.querySelectorAll('.delete-sched-row-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                syncScheduleInputsToData();
                const rowId = this.dataset.rowId;
                scheduleTableData.rows = scheduleTableData.rows.filter(r => String(r.id) !== String(rowId));
                scheduleTableData.rows.forEach((r, i) => r.no = i + 1);
                renderScheduleTable();
                markDirtyAndTriggerAutoSave();
            });
        });

        // Delete Period Column
        document.querySelectorAll('.btn-delete-sched-period').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                syncScheduleInputsToData();
                const periodId = this.dataset.periodId;
                scheduleTableData.periods = scheduleTableData.periods.filter(p => p.id !== periodId);
                scheduleTableData.periods.forEach((p, i) => p.num = i + 1);
                scheduleTableData.rows.forEach(r => {
                    if (Array.isArray(r.active_periods)) {
                        r.active_periods = r.active_periods.filter(pid => pid !== periodId);
                    }
                });
                renderScheduleTable();
                markDirtyAndTriggerAutoSave();
            });
        });

        // Activity Inputs
        document.querySelectorAll('.schedule-activity-input').forEach(inp => {
            inp.addEventListener('input', function () {
                const rowId = this.dataset.rowId;
                const row = scheduleTableData.rows.find(r => String(r.id) === String(rowId));
                if (row) row.activity = this.value;
                markDirtyAndTriggerAutoSave();
            });
        });

        // Period Label Contenteditable
        document.querySelectorAll('.period-label-text').forEach(sp => {
            sp.addEventListener('blur', function () {
                const periodId = this.dataset.periodId;
                const period = scheduleTableData.periods.find(p => String(p.id) === String(periodId));
                if (period) {
                    period.label = this.textContent.trim();
                    markDirtyAndTriggerAutoSave();
                }
            });
        });
    }

    function addScheduleRow() {
        syncScheduleInputsToData();
        const nextNo = scheduleTableData.rows.length + 1;
        const nextId = 'act_' + Date.now();
        scheduleTableData.rows.push({
            id: nextId,
            no: nextNo,
            activity: '',
            active_periods: []
        });
        renderScheduleTable();
        markDirtyAndTriggerAutoSave();
    }

    function addScheduleColumn() {
        syncScheduleInputsToData();
        const nextNum = scheduleTableData.periods.length + 1;
        const defaultLabel = 'Month ' + nextNum;

        Swal.fire({
            title: 'Add Month / Timeline Column',
            text: 'Enter label for column #' + nextNum + ' (e.g. Jul 2026, Week 1, Month ' + nextNum + '):',
            input: 'text',
            inputValue: defaultLabel,
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'Add Month Column',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.trim()) {
                const label = result.value.trim();
                const periodId = 'p_' + Date.now();
                scheduleTableData.periods.push({
                    id: periodId,
                    num: nextNum,
                    label: label
                });
                renderScheduleTable();
                markDirtyAndTriggerAutoSave();
            }
        });
    }

    const btnAddSchedRow = document.getElementById('btnAddScheduleRow');
    const btnAddSchedCol = document.getElementById('btnAddScheduleCol');
    if (btnAddSchedRow) btnAddSchedRow.addEventListener('click', addScheduleRow);
    if (btnAddSchedCol) btnAddSchedCol.addEventListener('click', addScheduleColumn);

    renderScheduleTable();

    // ------------------------------------------------------------------
    // 5. DYNAMIC HIERARCHICAL ORGANIZATIONAL STRUCTURE
    // ------------------------------------------------------------------
    let orgStructureData = @json($proposal->resolved_org_structure);
    if (!orgStructureData || !Array.isArray(orgStructureData.nodes)) {
        orgStructureData = {
            mode: 'dynamic',
            nodes: []
        };
    }

    const orgModeDynamic = document.getElementById('orgModeDynamic');
    const orgModeCustom = document.getElementById('orgModeCustom');
    const orgDynamicWrapper = document.getElementById('orgDynamicWrapper');
    const orgCustomWrapper = document.getElementById('orgCustomWrapper');

    function toggleOrgMode() {
        if (orgModeCustom && orgModeCustom.checked) {
            if (orgCustomWrapper) orgCustomWrapper.classList.remove('d-none');
            if (orgDynamicWrapper) orgDynamicWrapper.classList.add('d-none');
            orgStructureData.mode = 'custom';
        } else {
            if (orgDynamicWrapper) orgDynamicWrapper.classList.remove('d-none');
            if (orgCustomWrapper) orgCustomWrapper.classList.add('d-none');
            orgStructureData.mode = 'dynamic';
        }
        markDirtyAndTriggerAutoSave();
    }

    if (orgModeDynamic) orgModeDynamic.addEventListener('change', toggleOrgMode);
    if (orgModeCustom) orgModeCustom.addEventListener('change', toggleOrgMode);

    function syncOrgNodesToData() {
        const titles = document.querySelectorAll('.org-node-title-input');
        titles.forEach(inp => {
            const id = inp.dataset.nodeId;
            const node = orgStructureData.nodes.find(n => String(n.id) === String(id));
            if (node) node.title = inp.value;
        });
        const names = document.querySelectorAll('.org-node-name-input');
        names.forEach(inp => {
            const id = inp.dataset.nodeId;
            const node = orgStructureData.nodes.find(n => String(n.id) === String(id));
            if (node) node.name = inp.value;
        });
    }

    function renderOrgTree() {
        const container = document.getElementById('orgTreeContainer');
        if (!container) return;
        container.innerHTML = '';

        if (!orgStructureData.nodes || orgStructureData.nodes.length === 0) {
            container.innerHTML = `<div class="text-center text-muted extra-small py-4">No positions listed. Click <strong>+ Add Position / Role</strong> to build structure.</div>`;
            return;
        }

        orgStructureData.nodes.forEach((node, idx) => {
            if (idx > 0) {
                const connector = document.createElement('div');
                connector.className = 'd-flex flex-column align-items-center my-1';
                connector.innerHTML = `
                    <div style="width: 2px; height: 16px; background: #94a3b8;"></div>
                    <i class="mdi mdi-chevron-down text-secondary" style="margin-top: -6px; font-size: 16px;"></i>
                `;
                container.appendChild(connector);
            }

            const card = document.createElement('div');
            card.className = 'org-tree-node border bg-white shadow-2xs rounded-3 p-3 position-relative';
            card.style.width = '340px';

            card.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-primary-subtle text-primary font-mono extra-small px-2 py-1 rounded-pill">Tier ${idx + 1}</span>
                    ${orgStructureData.nodes.length > 1 ? `
                        <button type="button" class="btn btn-link text-danger p-0 btn-delete-org-node" data-node-id="${node.id}" title="Remove Position">
                            <i class="mdi mdi-close-circle-outline"></i>
                        </button>
                    ` : ''}
                </div>
                <input type="text" class="form-control form-control-sm text-center fw-bold text-dark border-0 bg-light p-1 mb-1 org-node-title-input" data-node-id="${node.id}" value="${escapeHtml(node.title)}" placeholder="Position / Designation Title...">
                <input type="text" class="form-control form-control-sm text-center text-secondary border-0 bg-transparent p-1 org-node-name-input" data-node-id="${node.id}" value="${escapeHtml(node.name)}" placeholder="Officer / Person / Entity Name...">
            `;
            container.appendChild(card);
        });

        container.querySelectorAll('.btn-delete-org-node').forEach(btn => {
            btn.addEventListener('click', function () {
                syncOrgNodesToData();
                const id = this.dataset.nodeId;
                orgStructureData.nodes = orgStructureData.nodes.filter(n => String(n.id) !== String(id));
                renderOrgTree();
                markDirtyAndTriggerAutoSave();
            });
        });

        container.querySelectorAll('.org-node-title-input, .org-node-name-input').forEach(inp => {
            inp.addEventListener('input', function () {
                const id = this.dataset.nodeId;
                const node = orgStructureData.nodes.find(n => String(n.id) === String(id));
                if (node) {
                    if (this.classList.contains('org-node-title-input')) node.title = this.value;
                    if (this.classList.contains('org-node-name-input')) node.name = this.value;
                }
                markDirtyAndTriggerAutoSave();
            });
        });
    }

    const btnAddOrgNode = document.getElementById('btnAddOrgNode');
    if (btnAddOrgNode) {
        btnAddOrgNode.addEventListener('click', function () {
            syncOrgNodesToData();
            const nextId = 'org_' + Date.now();
            orgStructureData.nodes.push({
                id: nextId,
                title: 'New Position / Committee',
                name: 'Designated Member',
                level: orgStructureData.nodes.length + 1
            });
            renderOrgTree();
            markDirtyAndTriggerAutoSave();
        });
    }
    renderOrgTree();

    // Image Upload & Preview logic for Custom Org Chart
    const orgFileInput = document.getElementById('orgChartFileInput');
    const btnSelectOrgFile = document.getElementById('btnSelectOrgFile');
    const btnReplaceOrgImage = document.getElementById('btnReplaceOrgImage');
    const btnRemoveOrgImage = document.getElementById('btnRemoveOrgImage');
    const orgDropzone = document.getElementById('orgDropzone');
    const orgImagePreviewContainer = document.getElementById('orgImagePreviewContainer');
    const orgChartImgPreview = document.getElementById('orgChartImgPreview');
    const orgEmptyUploadPrompt = document.getElementById('orgEmptyUploadPrompt');
    const removeOrgChartFlag = document.getElementById('removeOrgChartFlag');

    function handleSelectedFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: 'Please select a valid image file (PNG, JPG, WebP, SVG).'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            if (orgChartImgPreview) orgChartImgPreview.src = e.target.result;
            if (orgImagePreviewContainer) orgImagePreviewContainer.classList.remove('d-none');
            if (orgEmptyUploadPrompt) orgEmptyUploadPrompt.classList.add('d-none');
            if (removeOrgChartFlag) removeOrgChartFlag.value = '0';
            markDirtyAndTriggerAutoSave();
        };
        reader.readAsDataURL(file);
    }

    if (btnSelectOrgFile && orgFileInput) {
        btnSelectOrgFile.addEventListener('click', () => orgFileInput.click());
    }
    if (btnReplaceOrgImage && orgFileInput) {
        btnReplaceOrgImage.addEventListener('click', () => orgFileInput.click());
    }
    if (orgFileInput) {
        orgFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handleSelectedFile(this.files[0]);
            }
        });
    }

    if (orgDropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            orgDropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                orgDropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            orgDropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                orgDropzone.classList.remove('dragover');
            }, false);
        });

        orgDropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files[0] && orgFileInput) {
                orgFileInput.files = files;
                handleSelectedFile(files[0]);
            }
        }, false);
    }

    if (btnRemoveOrgImage) {
        btnRemoveOrgImage.addEventListener('click', function () {
            if (removeOrgChartFlag) removeOrgChartFlag.value = '1';
            if (orgFileInput) orgFileInput.value = '';
            if (orgImagePreviewContainer) orgImagePreviewContainer.classList.add('d-none');
            if (orgEmptyUploadPrompt) orgEmptyUploadPrompt.classList.remove('d-none');

            if (orgModeDynamic) {
                orgModeDynamic.checked = true;
                toggleOrgMode();
            }
            markDirtyAndTriggerAutoSave();
        });
    }

    // ------------------------------------------------------------------
    // 6. DYNAMIC BUDGETARY REQUIREMENTS SPREADSHEET TABLE
    // ------------------------------------------------------------------
    let budgetTableData = @json($proposal->resolved_budgetary_requirements);
    if (!budgetTableData || !Array.isArray(budgetTableData.items)) {
        budgetTableData = {
            category_title: 'A. Direct Activity Expenses & Requirements',
            items: [],
            grand_total: 0
        };
    }

    function formatCurrency(num) {
        const val = parseFloat(num) || 0;
        return '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalculateBudget() {
        let grandTotal = 0;
        let currentSectionSum = 0;

        budgetTableData.items.forEach(item => {
            if (item.type === 'row') {
                const qty = parseFloat(item.qty) || 0;
                const cost = parseFloat(item.cost) || 0;
                item.amount = Math.round(qty * cost * 100) / 100;
                currentSectionSum += item.amount;
                grandTotal += item.amount;
            } else if (item.type === 'subtotal') {
                item.amount = currentSectionSum;
                currentSectionSum = 0;
            }
        });

        budgetTableData.grand_total = grandTotal;

        // Update DOM displays
        const grandTotalDisplay = document.getElementById('budgetGrandTotalDisplay');
        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = formatCurrency(grandTotal);
        }

        document.querySelectorAll('.budget-subtotal-cell').forEach(cell => {
            const id = cell.dataset.itemId;
            const item = budgetTableData.items.find(i => String(i.id) === String(id));
            if (item) {
                cell.textContent = formatCurrency(item.amount);
            }
        });

        document.querySelectorAll('.budget-amount-cell').forEach(cell => {
            const id = cell.dataset.itemId;
            const item = budgetTableData.items.find(i => String(i.id) === String(id));
            if (item) {
                cell.textContent = formatCurrency(item.amount);
            }
        });
    }

    function syncBudgetInputsToData() {
        const categoryInput = document.getElementById('budgetCategoryTitle');
        if (categoryInput) {
            budgetTableData.category_title = categoryInput.value;
        }

        document.querySelectorAll('.budget-cell-input').forEach(inp => {
            const id = inp.dataset.itemId;
            const field = inp.dataset.field;
            const item = budgetTableData.items.find(i => String(i.id) === String(id));
            if (item && field) {
                if (field === 'qty' || field === 'cost') {
                    item[field] = parseFloat(inp.value) || 0;
                } else {
                    item[field] = inp.value;
                }
            }
        });
    }

    let activeBudgetItemId = null;

    function insertBudgetItemBefore(refItemId) {
        syncBudgetInputsToData();
        const nextId = 'b_' + Date.now();
        const newItem = {
            id: nextId,
            type: 'row',
            description: '',
            qty: 1,
            unit: 'Lot',
            cost: 0,
            amount: 0
        };

        let insertIdx = -1;
        if (refItemId === 'top') {
            insertIdx = (budgetTableData.items.length > 0 && budgetTableData.items[0].type === 'header') ? 1 : 0;
        } else if (refItemId) {
            const idx = budgetTableData.items.findIndex(i => String(i.id) === String(refItemId));
            if (idx !== -1) {
                insertIdx = idx; // Insert right before current row
            }
        }

        if (insertIdx === -1) {
            // Default: if first item is header, insert right under it at top
            insertIdx = (budgetTableData.items.length > 0 && budgetTableData.items[0].type === 'header') ? 1 : 0;
        }

        budgetTableData.items.splice(insertIdx, 0, newItem);
        activeBudgetItemId = nextId;
        renderBudgetTable();
        markDirtyAndTriggerAutoSave();

        setTimeout(() => {
            const newInp = document.querySelector(`input[data-item-id="${nextId}"][data-field="description"]`);
            if (newInp) {
                newInp.focus();
                newInp.select();
            }
        }, 60);
    }

    function insertBudgetItemAfter(refItemId) {
        syncBudgetInputsToData();
        const nextId = 'b_' + Date.now();
        const newItem = {
            id: nextId,
            type: 'row',
            description: '',
            qty: 1,
            unit: 'Lot',
            cost: 0,
            amount: 0
        };

        let insertIdx = -1;
        if (refItemId === 'top') {
            // Under first header if exists, or top
            insertIdx = (budgetTableData.items.length > 0 && budgetTableData.items[0].type === 'header') ? 1 : 0;
        } else if (refItemId) {
            const idx = budgetTableData.items.findIndex(i => String(i.id) === String(refItemId));
            if (idx !== -1) {
                insertIdx = idx + 1; // Insert right after current row
            }
        }

        if (insertIdx === -1) {
            // If no active row is selected, default to top under first section header
            if (budgetTableData.items.length > 0 && budgetTableData.items[0].type === 'header') {
                insertIdx = 1;
            } else {
                const firstSubtotalIdx = budgetTableData.items.findIndex(i => i.type === 'subtotal');
                insertIdx = (firstSubtotalIdx !== -1) ? firstSubtotalIdx : budgetTableData.items.length;
            }
        }

        budgetTableData.items.splice(insertIdx, 0, newItem);
        activeBudgetItemId = nextId;
        renderBudgetTable();
        markDirtyAndTriggerAutoSave();

        setTimeout(() => {
            const newInp = document.querySelector(`input[data-item-id="${nextId}"][data-field="description"]`);
            if (newInp) {
                newInp.focus();
                newInp.select();
            }
        }, 60);
    }

    function insertBudgetItemAtTop() {
        syncBudgetInputsToData();
        insertBudgetItemBefore('top');
    }

    function insertBudgetItemAtBottom() {
        syncBudgetInputsToData();
        let insertIdx = budgetTableData.items.length;
        for (let i = budgetTableData.items.length - 1; i >= 0; i--) {
            if (budgetTableData.items[i].type === 'subtotal') {
                insertIdx = i;
                break;
            }
        }
        const nextId = 'b_' + Date.now();
        const newItem = {
            id: nextId,
            type: 'row',
            description: '',
            qty: 1,
            unit: 'Lot',
            cost: 0,
            amount: 0
        };
        budgetTableData.items.splice(insertIdx, 0, newItem);
        activeBudgetItemId = nextId;
        renderBudgetTable();
        markDirtyAndTriggerAutoSave();
        setTimeout(() => {
            const newInp = document.querySelector(`input[data-item-id="${nextId}"][data-field="description"]`);
            if (newInp) {
                newInp.focus();
                newInp.select();
            }
        }, 60);
    }

    function renderBudgetTable() {
        const tbody = document.getElementById('budgetTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (!budgetTableData.items || budgetTableData.items.length === 0) {
            tbody.innerHTML = `<tr>
                <td colspan="6" class="text-center py-4 text-muted extra-small">
                    <i class="mdi mdi-cash-multiple fs-3 d-block mb-1 opacity-50"></i>
                    No budget items added yet. Click <strong>+ Add Item</strong> or <strong>+ Add Section Header</strong> to start.
                </td>
            </tr>`;
            return;
        }

        budgetTableData.items.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.dataset.itemId = item.id;
            if (String(item.id) === String(activeBudgetItemId)) {
                tr.classList.add('active-budget-row');
            }

            if (item.type === 'header') {
                tr.className = 'budget-header-row' + (String(item.id) === String(activeBudgetItemId) ? ' active-budget-row' : '');
                tr.innerHTML = `
                    <td colspan="5" class="py-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-chevron-right text-primary"></i>
                            <input type="text" class="table-cell-input budget-cell-input fw-bold text-dark font-sans" data-item-id="${item.id}" data-field="title" value="${escapeHtml(item.title)}" placeholder="Section Header Title...">
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn btn-link text-primary p-0 insert-budget-item-below-btn" data-item-id="${item.id}" title="Insert Row Below This Header">
                                <i class="mdi mdi-plus-circle-outline fs-6"></i>
                            </button>
                            <button type="button" class="btn btn-link text-danger p-0 delete-budget-item-btn" data-item-id="${item.id}" title="Remove Section Header">
                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                            </button>
                        </div>
                    </td>
                `;
            } else if (item.type === 'subtotal') {
                tr.className = 'budget-subtotal-row';
                tr.innerHTML = `
                    <td colspan="4" class="text-end fw-bold text-uppercase py-2 pe-3">Subtotal:</td>
                    <td class="text-end fw-bold font-mono budget-subtotal-cell py-2" data-item-id="${item.id}">${formatCurrency(item.amount || 0)}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-link text-danger p-0 delete-budget-item-btn" data-item-id="${item.id}" title="Remove Subtotal">
                            <i class="mdi mdi-close fs-6"></i>
                        </button>
                    </td>
                `;
            } else {
                // Line item row
                tr.innerHTML = `
                    <td>
                        <input type="text" class="table-cell-input budget-cell-input" data-item-id="${item.id}" data-field="description" value="${escapeHtml(item.description)}" placeholder="Description of item, supplies, travel...">
                    </td>
                    <td class="text-center">
                        <input type="number" step="any" class="table-cell-input budget-cell-input text-center font-mono" data-item-id="${item.id}" data-field="qty" value="${item.qty ?? 1}" placeholder="1">
                    </td>
                    <td class="text-center">
                        <input type="text" class="table-cell-input budget-cell-input text-center" data-item-id="${item.id}" data-field="unit" value="${escapeHtml(item.unit || 'Lot')}" placeholder="Unit">
                    </td>
                    <td class="text-end">
                        <input type="number" step="0.01" class="table-cell-input budget-cell-input text-end font-mono" data-item-id="${item.id}" data-field="cost" value="${item.cost ?? 0}" placeholder="0.00">
                    </td>
                    <td class="text-end font-mono fw-semibold text-dark budget-amount-cell" data-item-id="${item.id}">
                        ${formatCurrency(item.amount || 0)}
                    </td>
                    <td class="text-center align-middle">
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn btn-link text-secondary p-0 insert-budget-item-above-btn" data-item-id="${item.id}" title="Insert Row Above This (Shift+Enter)">
                                <i class="mdi mdi-table-row-plus-before fs-6"></i>
                            </button>
                            <button type="button" class="btn btn-link text-primary p-0 insert-budget-item-below-btn" data-item-id="${item.id}" title="Insert Row Below This (Enter)">
                                <i class="mdi mdi-table-row-plus-after fs-6"></i>
                            </button>
                            <button type="button" class="btn btn-link text-danger p-0 delete-budget-item-btn" data-item-id="${item.id}" title="Remove Item">
                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                            </button>
                        </div>
                    </td>
                `;
            }

            // Track active row on click
            tr.addEventListener('click', function () {
                activeBudgetItemId = item.id;
                tbody.querySelectorAll('tr').forEach(r => r.classList.remove('active-budget-row'));
                tr.classList.add('active-budget-row');
            });

            // Track active row on focus and handle Enter / Shift+Enter shortcuts
            tr.querySelectorAll('.budget-cell-input').forEach(inp => {
                inp.addEventListener('focus', function () {
                    activeBudgetItemId = item.id;
                    tbody.querySelectorAll('tr').forEach(r => r.classList.remove('active-budget-row'));
                    tr.classList.add('active-budget-row');
                });

                inp.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (e.shiftKey) {
                            insertBudgetItemBefore(item.id);
                        } else {
                            insertBudgetItemAfter(item.id);
                        }
                    }
                });
            });

            tbody.appendChild(tr);
        });

        // Attach listeners for insert above buttons
        tbody.querySelectorAll('.insert-budget-item-above-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = this.dataset.itemId;
                insertBudgetItemBefore(id);
            });
        });

        // Attach listeners for insert below buttons
        tbody.querySelectorAll('.insert-budget-item-below-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = this.dataset.itemId;
                insertBudgetItemAfter(id);
            });
        });

        // Attach listeners to delete buttons
        tbody.querySelectorAll('.delete-budget-item-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                syncBudgetInputsToData();
                const id = this.dataset.itemId;
                budgetTableData.items = budgetTableData.items.filter(i => String(i.id) !== String(id));
                if (String(activeBudgetItemId) === String(id)) {
                    activeBudgetItemId = null;
                }
                recalculateBudget();
                renderBudgetTable();
                markDirtyAndTriggerAutoSave();
            });
        });

        tbody.querySelectorAll('.budget-cell-input').forEach(inp => {
            inp.addEventListener('input', function () {
                syncBudgetInputsToData();
                recalculateBudget();
                markDirtyAndTriggerAutoSave();
            });
        });

        recalculateBudget();
    }

    // Category title focus sets active insertion to the top
    const budgetCategoryTitleInp = document.getElementById('budgetCategoryTitle');
    if (budgetCategoryTitleInp) {
        budgetCategoryTitleInp.addEventListener('focus', function () {
            activeBudgetItemId = 'top';
            const tbody = document.getElementById('budgetTableBody');
            if (tbody) {
                tbody.querySelectorAll('tr').forEach(r => r.classList.remove('active-budget-row'));
            }
        });
    }

    const btnAddBudgetItem = document.getElementById('btnAddBudgetItem');
    const btnDropInsertBelow = document.getElementById('btnDropInsertBelow');
    const btnDropInsertAbove = document.getElementById('btnDropInsertAbove');
    const btnDropInsertTop = document.getElementById('btnDropInsertTop');
    const btnDropInsertBottom = document.getElementById('btnDropInsertBottom');
    const btnAddBudgetSection = document.getElementById('btnAddBudgetSection');

    if (btnAddBudgetItem) {
        btnAddBudgetItem.addEventListener('click', function () {
            insertBudgetItemAfter(activeBudgetItemId);
        });
    }
    if (btnDropInsertBelow) {
        btnDropInsertBelow.addEventListener('click', function () {
            insertBudgetItemAfter(activeBudgetItemId);
        });
    }
    if (btnDropInsertAbove) {
        btnDropInsertAbove.addEventListener('click', function () {
            insertBudgetItemBefore(activeBudgetItemId);
        });
    }
    if (btnDropInsertTop) {
        btnDropInsertTop.addEventListener('click', function () {
            insertBudgetItemAtTop();
        });
    }
    if (btnDropInsertBottom) {
        btnDropInsertBottom.addEventListener('click', function () {
            insertBudgetItemAtBottom();
        });
    }

    if (btnAddBudgetSection) {
        btnAddBudgetSection.addEventListener('click', function () {
            syncBudgetInputsToData();
            Swal.fire({
                title: 'Add Section Header',
                text: 'Enter sub-category title (e.g. Supplies and Materials, Honorarium, Travel):',
                input: 'text',
                inputPlaceholder: 'Section header...',
                showCancelButton: true,
                confirmButtonColor: '#185abd',
                confirmButtonText: 'Add Section',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && result.value && result.value.trim()) {
                    const title = result.value.trim();
                    budgetTableData.items.push({
                        id: 'b_sec_' + Date.now(),
                        type: 'header',
                        title: title
                    });
                    budgetTableData.items.push({
                        id: 'b_row_' + Date.now(),
                        type: 'row',
                        description: '',
                        qty: 1,
                        unit: 'Pax',
                        cost: 0,
                        amount: 0
                    });
                    budgetTableData.items.push({
                        id: 'b_sub_' + Date.now(),
                        type: 'subtotal',
                        title: 'Subtotal',
                        amount: 0
                    });
                    renderBudgetTable();
                    markDirtyAndTriggerAutoSave();
                }
            });
        });
    }

    renderBudgetTable();

    // ------------------------------------------------------------------
    // 7. GENERIC WORD RIBBON TOOLBAR COMMANDS
    // ------------------------------------------------------------------
    document.querySelectorAll('.word-btn[data-cmd]').forEach(btn => {
        // Prevent losing text selection in active contenteditable area
        btn.addEventListener('mousedown', function (e) {
            e.preventDefault();
        });
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const cmd = this.dataset.cmd;
            const val = this.dataset.value || null;
            document.execCommand(cmd, false, val);
            markDirtyAndTriggerAutoSave();
        });
    });

    document.querySelectorAll('.word-font-family, #wordFontFamily').forEach(select => {
        select.addEventListener('change', function () {
            document.execCommand('fontName', false, this.value);
            const wordPage = document.getElementById('wordPage');
            if (wordPage) wordPage.style.fontFamily = this.value;
            markDirtyAndTriggerAutoSave();
        });
    });

    document.querySelectorAll('.word-font-size, #wordFontSize').forEach(select => {
        select.addEventListener('change', function () {
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
                const map = { '12px': '2', '14px': '3', '16px': '4', '18px': '5', '22px': '6' };
                document.execCommand('fontSize', false, map[this.value] || '3');
                markDirtyAndTriggerAutoSave();
            }
        });
    });

    const wordBlockFormat = document.getElementById('wordBlockFormat');
    if (wordBlockFormat) {
        wordBlockFormat.addEventListener('change', function () {
            const tag = this.value;
            document.execCommand('formatBlock', false, tag);
            markDirtyAndTriggerAutoSave();
        });
    }

    // Text Color Palette
    document.querySelectorAll('.set-text-color-btn').forEach(btn => {
        btn.addEventListener('mousedown', e => e.preventDefault());
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const color = this.dataset.color;
            document.execCommand('foreColor', false, color);
            markDirtyAndTriggerAutoSave();
        });
    });

    // Highlight Color Palette
    document.querySelectorAll('.set-highlight-color-btn').forEach(btn => {
        btn.addEventListener('mousedown', e => e.preventDefault());
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const color = this.dataset.color;
            document.execCommand('hiliteColor', false, color);
            markDirtyAndTriggerAutoSave();
        });
    });

    // Auto-Save Keystroke & Blur Listeners on all narrative contenteditable areas
    document.querySelectorAll('.word-editable-content').forEach(editor => {
        editor.addEventListener('input', function () {
            markDirtyAndTriggerAutoSave();
        });
        editor.addEventListener('blur', function () {
            if (isDirty) {
                clearTimeout(autoSaveTimer);
                saveTechnicalDescription({ isAutoSave: true });
            }
        });
    });

    // Unsaved Changes BeforeUnload Guard
    window.addEventListener('beforeunload', function (e) {
        if (isDirty && isProposalEditable) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes in Part II Technical Description.';
            return e.returnValue;
        }
    });

    // ------------------------------------------------------------------
    // RATIONALE LIVE WORD COUNTER (MAX 500 WORDS)
    // ------------------------------------------------------------------
    const introEl = document.getElementById('editor_introduction');
    const wordBadge = document.getElementById('rationaleWordCountBadge');
    function updateRationaleWordCount() {
        if (!introEl || !wordBadge) return;
        const text = introEl.innerText.trim();
        const words = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
        wordBadge.textContent = `${words} / 500 words`;
        if (words > 500) {
            wordBadge.className = 'badge bg-danger text-white border border-danger font-mono extra-small';
        } else if (words >= 400) {
            wordBadge.className = 'badge bg-warning text-dark border border-warning font-mono extra-small';
        } else {
            wordBadge.className = 'badge bg-success-subtle text-success border border-success-subtle font-mono extra-small';
        }
    }
    if (introEl) {
        introEl.addEventListener('input', updateRationaleWordCount);
        updateRationaleWordCount();
    }

    // ------------------------------------------------------------------
    // SPECIFIC OBJECTIVES REPEATER
    // ------------------------------------------------------------------
    const btnAddSpecificObj = document.getElementById('btnAddSpecificObjective');
    const specificObjList = document.getElementById('specificObjectivesList');
    if (btnAddSpecificObj && specificObjList) {
        btnAddSpecificObj.addEventListener('click', function () {
            const count = specificObjList.querySelectorAll('.specific-obj-row').length + 1;
            const row = document.createElement('div');
            row.className = 'specific-obj-row d-flex align-items-start gap-2 bg-light p-2 rounded-2 border';
            row.innerHTML = `
                <span class="badge bg-primary text-white font-mono rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 24px; height: 24px; font-size: 11px;">${count}</span>
                <textarea class="form-control form-control-sm specific-obj-input bg-white" rows="2" placeholder="Specific Objective #${count}..."></textarea>
                <button type="button" class="btn btn-sm text-danger p-1 btn-remove-spec-obj flex-shrink-0" title="Delete objective">
                    <i class="mdi mdi-close-circle-outline fs-5"></i>
                </button>
            `;
            specificObjList.appendChild(row);
            markDirtyAndTriggerAutoSave();
        });

        specificObjList.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-spec-obj');
            if (btn) {
                const row = btn.closest('.specific-obj-row');
                if (row) {
                    row.remove();
                    specificObjList.querySelectorAll('.specific-obj-row').forEach((r, idx) => {
                        const badge = r.querySelector('.badge');
                        if (badge) badge.textContent = idx + 1;
                        const ta = r.querySelector('.specific-obj-input');
                        if (ta) ta.placeholder = `Specific Objective #${idx + 1}...`;
                    });
                    markDirtyAndTriggerAutoSave();
                }
            }
        });

        specificObjList.addEventListener('input', function (e) {
            if (e.target.matches('.specific-obj-input')) {
                markDirtyAndTriggerAutoSave();
            }
        });
    }

    // ------------------------------------------------------------------
    // EXPECTED OUTPUTS CHANGE LISTENER
    // ------------------------------------------------------------------
    const expOutputsTable = document.getElementById('expectedOutputsTable');
    if (expOutputsTable) {
        expOutputsTable.addEventListener('input', function (e) {
            if (e.target.matches('.output-target-input') || e.target.matches('.output-description-input')) {
                markDirtyAndTriggerAutoSave();
            }
        });
    }

    // ------------------------------------------------------------------
    // SCHEDULE OF ACTIVITIES TABLE
    // ------------------------------------------------------------------
    const btnAddSched = document.getElementById('btnAddScheduleRow');
    const schedBody = document.getElementById('scheduleTableBody');
    if (btnAddSched && schedBody) {
        btnAddSched.addEventListener('click', function () {
            const count = schedBody.querySelectorAll('.schedule-data-row').length + 1;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            let mHtml = '';
            months.forEach(m => {
                mHtml += `<td class="text-center p-0"><div class="d-flex align-items-center justify-content-center h-100 py-1"><input type="checkbox" class="form-check-input m-0 sched-month-checkbox" data-month="${m}"></div></td>`;
            });
            const tr = document.createElement('tr');
            tr.className = 'schedule-data-row';
            tr.dataset.index = count - 1;
            tr.innerHTML = `
                <td class="text-center fw-bold sched-row-num">${count}</td>
                <td><input type="text" class="form-control form-control-sm border-0 sched-activity-input" placeholder="Enter major activity..."></td>
                <td><input type="text" class="form-control form-control-sm border-0 sched-agency-input" placeholder="Agency or person..." value="Extension Team"></td>
                ${mHtml}
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-sched-row" title="Remove activity">
                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                    </button>
                </td>
            `;
            schedBody.appendChild(tr);
            updateSchedCount();
            markDirtyAndTriggerAutoSave();
        });

        schedBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-sched-row');
            if (btn) {
                const tr = btn.closest('.schedule-data-row');
                if (tr) {
                    tr.remove();
                    schedBody.querySelectorAll('.schedule-data-row').forEach((r, idx) => {
                        const num = r.querySelector('.sched-row-num');
                        if (num) num.textContent = idx + 1;
                        r.dataset.index = idx;
                    });
                    updateSchedCount();
                    markDirtyAndTriggerAutoSave();
                }
            }
        });

        schedBody.addEventListener('change', function (e) {
            if (e.target.matches('.sched-month-checkbox') || e.target.matches('input')) {
                markDirtyAndTriggerAutoSave();
            }
        });

        function updateSchedCount() {
            const badge = document.getElementById('scheduleRowCountBadge');
            if (badge) {
                const cnt = schedBody.querySelectorAll('.schedule-data-row').length;
                badge.textContent = `${cnt} Activities`;
            }
        }
    }

    // ------------------------------------------------------------------
    // PROJECT PROPONENTS TABLE
    // ------------------------------------------------------------------
    const btnAddProp = document.getElementById('btnAddProponentRow');
    const propBody = document.getElementById('proponentsTableBody');
    if (btnAddProp && propBody) {
        btnAddProp.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'proponent-data-row';
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm border-0 fw-semibold prop-name-input" placeholder="Faculty proponent name..."></td>
                <td><input type="text" class="form-control form-control-sm border-0 prop-role-input" value="Member" placeholder="Role title..."></td>
                <td><input type="text" class="form-control form-control-sm border-0 prop-duties-input" placeholder="Assigned duties..."></td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-prop-row" title="Remove proponent">
                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                    </button>
                </td>
            `;
            propBody.appendChild(tr);
            markDirtyAndTriggerAutoSave();
        });

        propBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-prop-row');
            if (btn) {
                const tr = btn.closest('.proponent-data-row');
                if (tr) {
                    tr.remove();
                    markDirtyAndTriggerAutoSave();
                }
            }
        });

        propBody.addEventListener('input', function (e) {
            if (e.target.matches('input')) {
                markDirtyAndTriggerAutoSave();
            }
        });
    }

    const btnSyncProp = document.getElementById('btnSyncFromProponents');
    if (btnSyncProp && propBody) {
        btnSyncProp.addEventListener('click', function () {
            if (!initialProponents || !initialProponents.length) {
                Swal.fire({ icon: 'info', title: 'No Proponents', text: 'No additional proponents listed in Part I.' });
                return;
            }
            propBody.innerHTML = '';
            initialProponents.forEach(p => {
                const tr = document.createElement('tr');
                tr.className = 'proponent-data-row';
                tr.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm border-0 fw-semibold prop-name-input" value="${escapeHtml(p.name || '')}"></td>
                    <td><input type="text" class="form-control form-control-sm border-0 prop-role-input" value="${escapeHtml(p.role || 'Member')}"></td>
                    <td><input type="text" class="form-control form-control-sm border-0 prop-duties-input" value="${escapeHtml(p.responsibilities || p.duties || 'Activity facilitation, technical modules, and logistics.')}"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-prop-row" title="Remove proponent">
                            <i class="mdi mdi-trash-can-outline fs-6"></i>
                        </button>
                    </td>
                `;
                propBody.appendChild(tr);
            });
            markDirtyAndTriggerAutoSave();
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Proponents synchronized', timer: 2000, showConfirmButton: false });
        });
    }

    // ------------------------------------------------------------------
    // RISK MANAGEMENT TABLE
    // ------------------------------------------------------------------
    const btnAddRisk = document.getElementById('btnAddRiskRow');
    const riskBody = document.getElementById('riskManagementTableBody');
    if (btnAddRisk && riskBody) {
        btnAddRisk.addEventListener('click', function () {
            const count = riskBody.querySelectorAll('.risk-data-row').length + 1;
            const tr = document.createElement('tr');
            tr.className = 'risk-data-row';
            tr.innerHTML = `
                <td class="text-center"><input type="text" class="form-control form-control-sm text-center border-0 risk-obj-input" value="${count}" style="max-width: 55px;"></td>
                <td class="text-center"><input type="text" class="form-control form-control-sm text-center border-0 risk-act-input" value="${count}" style="max-width: 55px;"></td>
                <td><input type="text" class="form-control form-control-sm border-0 risk-desc-input" placeholder="Identify risk..."></td>
                <td class="text-center">
                    <select class="form-select form-select-sm risk-level-select font-mono extra-small text-success fw-semibold">
                        <option value="Low" selected>Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </td>
                <td><input type="text" class="form-control form-control-sm border-0 risk-prev-input" placeholder="Mitigation plan..."></td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-risk-row" title="Remove risk">
                        <i class="mdi mdi-trash-can-outline fs-6"></i>
                    </button>
                </td>
            `;
            riskBody.appendChild(tr);
            markDirtyAndTriggerAutoSave();
        });

        riskBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-risk-row');
            if (btn) {
                const tr = btn.closest('.risk-data-row');
                if (tr) {
                    tr.remove();
                    markDirtyAndTriggerAutoSave();
                }
            }
        });

        riskBody.addEventListener('change', function (e) {
            if (e.target.matches('.risk-level-select')) {
                const val = e.target.value;
                if (val === 'High') {
                    e.target.className = 'form-select form-select-sm risk-level-select font-mono extra-small text-danger fw-bold';
                } else if (val === 'Medium') {
                    e.target.className = 'form-select form-select-sm risk-level-select font-mono extra-small text-warning-emphasis fw-semibold';
                } else {
                    e.target.className = 'form-select form-select-sm risk-level-select font-mono extra-small text-success fw-semibold';
                }
                markDirtyAndTriggerAutoSave();
            } else if (e.target.matches('input')) {
                markDirtyAndTriggerAutoSave();
            }
        });
    }

    // ------------------------------------------------------------------
    // LINE-ITEM BUDGET (SECTIONS A TO I)
    // ------------------------------------------------------------------
    const budgetBody = document.getElementById('lineItemBudgetTableBody');
    if (budgetBody) {
        $(document).on('click', '.btn-add-budget-row', function (e) {
            e.preventDefault();
            const sec = $(this).data('section');
            const headerRow = budgetBody.querySelector(`.section-header-row[data-section="${sec}"]`);
            if (headerRow) {
                let lastRow = headerRow;
                let next = headerRow.nextElementSibling;
                while (next && next.classList.contains('budget-item-row') && next.dataset.section === sec) {
                    lastRow = next;
                    next = next.nextElementSibling;
                }
                const tr = document.createElement('tr');
                tr.className = 'budget-item-row';
                tr.dataset.section = sec;
                tr.innerHTML = `
                    <td class="ps-3"><input type="text" class="form-control form-control-sm border-0 b-part-input" placeholder="Item description / particulars..."></td>
                    <td class="text-center"><input type="text" class="form-control form-control-sm text-center border-0 b-qty-input" value="1" placeholder="Qty"></td>
                    <td class="text-center"><input type="text" class="form-control form-control-sm text-center border-0 b-unit-input" value="unit" placeholder="Unit"></td>
                    <td class="text-end"><input type="text" class="form-control form-control-sm text-end border-0 b-cost-input" value="0.00" placeholder="0.00"></td>
                    <td class="text-end"><input type="text" class="form-control form-control-sm text-end border-0 fw-semibold b-amount-input" value="0.00" placeholder="0.00"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-budget-row" title="Remove line item">
                            <i class="mdi mdi-trash-can-outline fs-6"></i>
                        </button>
                    </td>
                `;
                lastRow.after(tr);
                calculateLineItemBudget();
                markDirtyAndTriggerAutoSave();
            }
        });

        budgetBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-budget-row');
            if (btn) {
                const tr = btn.closest('.budget-item-row');
                if (tr) {
                    tr.remove();
                    calculateLineItemBudget();
                    markDirtyAndTriggerAutoSave();
                }
            }
        });

        budgetBody.addEventListener('input', function (e) {
            if (e.target.matches('.b-qty-input') || e.target.matches('.b-cost-input')) {
                const tr = e.target.closest('.budget-item-row');
                if (tr) {
                    const qtyStr = tr.querySelector('.b-qty-input')?.value?.trim();
                    const costStr = tr.querySelector('.b-cost-input')?.value?.trim();
                    const amtInput = tr.querySelector('.b-amount-input');
                    const qty = parseFloat(qtyStr);
                    const cost = parseFloat(costStr?.replace(/,/g, ''));
                    if (!isNaN(qty) && !isNaN(cost)) {
                        const total = qty * cost;
                        if (amtInput) amtInput.value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
                calculateLineItemBudget();
                markDirtyAndTriggerAutoSave();
            } else if (e.target.matches('.b-part-input') || e.target.matches('.b-unit-input') || e.target.matches('.b-amount-input')) {
                calculateLineItemBudget();
                markDirtyAndTriggerAutoSave();
            }
        });

        function calculateLineItemBudget() {
            let grandTotal = 0;
            const itemSections = ['A', 'B_COMM', 'B_TRANS', 'B_SUPP', 'B_TRAIN', 'B_OTHER', 'C', 'D'];
            const mooeKeys = ['B_COMM', 'B_TRANS', 'B_SUPP', 'B_TRAIN', 'B_OTHER'];
            let mooeTotal = 0;

            itemSections.forEach(sec => {
                let secSubtotal = 0;
                document.querySelectorAll(`.budget-item-row[data-section="${sec}"]`).forEach(row => {
                    const amtStr = row.querySelector('.b-amount-input')?.value?.replace(/,/g, '')?.trim();
                    const amt = parseFloat(amtStr);
                    if (!isNaN(amt)) {
                        secSubtotal += amt;
                    }
                });
                const subDisp = document.querySelector(`.section-subtotal-display[data-section="${sec}"]`);
                if (subDisp) {
                    subDisp.textContent = `Subtotal: ₱${secSubtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
                const subVal = document.querySelector(`.section-subtotal-val[data-section="${sec}"]`);
                if (subVal) {
                    subVal.textContent = `₱${secSubtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }

                if (mooeKeys.includes(sec)) {
                    mooeTotal += secSubtotal;
                } else {
                    grandTotal += secSubtotal;
                }
            });

            // Update parent B (MOOE) display
            const mooeDisp = document.querySelector(`.section-subtotal-display[data-section="B"]`);
            if (mooeDisp) {
                mooeDisp.textContent = `MOOE Total: ₱${mooeTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
            const mooeVal = document.querySelector(`.section-subtotal-val[data-section="B"]`);
            if (mooeVal) {
                mooeVal.textContent = `₱${mooeTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            grandTotal += mooeTotal;

            const grandDisp = document.getElementById('budgetGrandTotalDisplay');
            if (grandDisp) {
                grandDisp.textContent = `₱${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
            const badge = document.getElementById('budgetCashBadge');
            if (badge) {
                badge.textContent = `Grand Total: ₱${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
        }
        calculateLineItemBudget();
    }

    // ------------------------------------------------------------------
    // PART I MODAL DYNAMIC HELPERS
    // ------------------------------------------------------------------
    $('#btnModalAddAgency').on('click', function () {
        const idx = $('#modalAgenciesTableBody tr').length;
        const tr = `<tr>
            <td><input type="text" name="collaborating_agencies_data[${idx}][agency]" class="form-control form-control-sm border-0" placeholder="Agency name..."></td>
            <td><input type="text" name="collaborating_agencies_data[${idx}][role]" class="form-control form-control-sm border-0" placeholder="Partner role..."></td>
            <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-modal-agency"><i class="mdi mdi-trash-can-outline fs-6"></i></button></td>
        </tr>`;
        $('#modalAgenciesTableBody').append(tr);
    });
    $(document).on('click', '.btn-remove-modal-agency', function () {
        $(this).closest('tr').remove();
    });

    $('#btnModalAddBeneficiary').on('click', function () {
        const idx = $('#modalBeneficiariesTableBody tr').length;
        const tr = `<tr class="modal-ben-row">
            <td><input type="text" name="target_beneficiaries_data[${idx}][name]" class="form-control form-control-sm border-0" placeholder="Beneficiary name..."></td>
            <td><input type="number" name="target_beneficiaries_data[${idx}][male]" class="form-control form-control-sm border-0 text-center modal-ben-m" value="0" min="0"></td>
            <td><input type="number" name="target_beneficiaries_data[${idx}][female]" class="form-control form-control-sm border-0 text-center modal-ben-f" value="0" min="0"></td>
            <td><input type="number" name="target_beneficiaries_data[${idx}][total]" class="form-control form-control-sm border-0 text-center fw-bold modal-ben-t" value="0" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-modal-ben"><i class="mdi mdi-trash-can-outline fs-6"></i></button></td>
        </tr>`;
        $('#modalBeneficiariesTableBody').append(tr);
    });
    $(document).on('click', '.btn-remove-modal-ben', function () {
        $(this).closest('tr').remove();
    });
    $(document).on('input', '.modal-ben-m, .modal-ben-f', function () {
        const row = $(this).closest('tr');
        const m = parseInt(row.find('.modal-ben-m').val()) || 0;
        const f = parseInt(row.find('.modal-ben-f').val()) || 0;
        row.find('.modal-ben-t').val(m + f);
    });

    function calcModalDuration() {
        const startVal = $('#modal_duration_start').val();
        const endVal = $('#modal_duration_end').val();
        if (startVal && endVal) {
            const d1 = new Date(startVal);
            const d2 = new Date(endVal);
            if (!isNaN(d1) && !isNaN(d2) && d2 >= d1) {
                let months = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
                if (d2.getDate() >= d1.getDate()) {
                    months += 1;
                }
                $('#modal_duration_months').val(Math.max(1, Math.round(months)));
            }
        }
    }
    $('#modal_duration_start, #modal_duration_end').on('change', calcModalDuration);

    // ------------------------------------------------------------------
    // 8. SAVE TECHNICAL DESCRIPTION (AJAX AUTO-SAVE + MANUAL + CTRL+S)
    // ------------------------------------------------------------------
    function saveTechnicalDescription(options = {}) {
        if (!isProposalEditable) return;
        if (isSaving) return;

        const isAuto = options && options.isAutoSave === true;
        isSaving = true;
        clearTimeout(autoSaveTimer);

        const indicator = document.getElementById('saveStatusIndicator');
        if (indicator) {
            indicator.className = 'badge bg-primary-subtle text-primary border border-primary-subtle ms-2 font-mono extra-small';
            indicator.innerHTML = isAuto
                ? '<i class="mdi mdi-cloud-upload-outline mdi-spin me-1"></i> Auto-saving...'
                : '<i class="mdi mdi-loading mdi-spin me-1"></i> Saving...';
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        // Narrative contenteditable texts
        const introEl = document.getElementById('editor_introduction');
        if (introEl) formData.append('introduction', introEl.innerHTML.trim());

        const genObjEl = document.getElementById('editor_general_objective');
        if (genObjEl) formData.append('general_objective', genObjEl.innerHTML.trim());

        // Specific objectives
        const specObjs = [];
        document.querySelectorAll('.specific-obj-input').forEach(inp => {
            const val = inp.value.trim();
            if (val) specObjs.push(val);
        });
        formData.append('specific_objectives', JSON.stringify(specObjs));

        // Expected Outputs (7 categories)
        const outputs = [];
        document.querySelectorAll('.expected-output-row').forEach(row => {
            outputs.push({
                category: row.dataset.category || row.querySelector('td')?.innerText?.trim(),
                target: row.querySelector('.output-target-input')?.value?.trim() || '0',
                description: row.querySelector('.output-description-input')?.value?.trim() || ''
            });
        });
        formData.append('expected_outputs', JSON.stringify(outputs));

        const relevEl = document.getElementById('editor_relevance_narrative');
        const sdgPayload = {
            selected: selectedSdgs,
            indirect: indirectSdgs,
            narrative: relevEl ? relevEl.innerHTML.trim() : ''
        };
        formData.append('sdg_alignments', JSON.stringify(sdgPayload));
        if (relevEl) formData.append('relevance_narrative', relevEl.innerHTML.trim());

        // Schedule of Activities (Jan - Dec)
        const schedRows = [];
        document.querySelectorAll('.schedule-data-row').forEach(row => {
            const months = {};
            row.querySelectorAll('.sched-month-checkbox').forEach(cb => {
                if (cb.checked) {
                    months[cb.dataset.month] = 1;
                }
            });
            schedRows.push({
                no: row.querySelector('.sched-row-num')?.innerText?.trim() || 1,
                activity: row.querySelector('.sched-activity-input')?.value?.trim() || '',
                agency: row.querySelector('.sched-agency-input')?.value?.trim() || '',
                months: months
            });
        });
        formData.append('schedule_of_activities', JSON.stringify({ rows: schedRows }));

        // Proponents
        const propRows = [];
        document.querySelectorAll('.proponent-data-row').forEach(row => {
            propRows.push({
                name: row.querySelector('.prop-name-input')?.value?.trim() || '',
                role: row.querySelector('.prop-role-input')?.value?.trim() || 'Member',
                responsibilities: row.querySelector('.prop-duties-input')?.value?.trim() || ''
            });
        });
        formData.append('people_involved', JSON.stringify({ rows: propRows }));

        // Risk Management
        const riskRows = [];
        document.querySelectorAll('.risk-data-row').forEach(row => {
            riskRows.push({
                objective_no: row.querySelector('.risk-obj-input')?.value?.trim() || 1,
                activity_no: row.querySelector('.risk-act-input')?.value?.trim() || 1,
                risk: row.querySelector('.risk-desc-input')?.value?.trim() || '',
                risk_level: row.querySelector('.risk-level-select')?.value?.trim() || 'Low',
                prevention: row.querySelector('.risk-prev-input')?.value?.trim() || ''
            });
        });
        formData.append('risk_management', JSON.stringify(riskRows));

        // Line-Item Budget (Sections A, B with sub-categories, C, D)
        const budgetData = {};
        const sectionKeys = ['A', 'B_COMM', 'B_TRANS', 'B_SUPP', 'B_TRAIN', 'B_OTHER', 'C', 'D'];
        sectionKeys.forEach(key => {
            budgetData[key] = { items: [] };
        });
        document.querySelectorAll('.budget-item-row').forEach(row => {
            const sec = row.dataset.section;
            if (sec && budgetData[sec]) {
                budgetData[sec].items.push({
                    particulars: row.querySelector('.b-part-input')?.value?.trim() || '',
                    qty: row.querySelector('.b-qty-input')?.value?.trim() || 'N/A',
                    unit: row.querySelector('.b-unit-input')?.value?.trim() || 'N/A',
                    cost: row.querySelector('.b-cost-input')?.value?.trim() || 'N/A',
                    amount: row.querySelector('.b-amount-input')?.value?.trim() || 'N/A'
                });
            }
        });
        formData.append('budgetary_requirements', JSON.stringify(budgetData));

        fetch("{{ route('dss.rie.proposals.technical.update', $proposal->getRouteKey()) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.success) {
                isDirty = false;
                const now = new Date();
                const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                if (indicator) {
                    indicator.className = 'badge bg-success-subtle text-success border border-success-subtle ms-2 font-mono extra-small';
                    indicator.innerHTML = `<i class="mdi mdi-cloud-check-outline me-1"></i> ${isAuto ? 'Auto-saved' : 'Saved'} at ${timeStr}`;
                }

                if (data.org_chart_url && orgChartImgPreview) {
                    orgChartImgPreview.src = data.org_chart_url;
                }

                if (!isAuto) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Part II Technical Description saved successfully!',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } else {
                if (indicator) {
                    indicator.className = 'badge bg-danger-subtle text-danger border border-danger-subtle ms-2 font-mono extra-small';
                    indicator.innerHTML = '<i class="mdi mdi-alert-circle-outline me-1"></i> Save Failed';
                }
                if (!isAuto) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: data.message || 'Unable to save technical description.'
                    });
                }
            }
        })
        .catch(err => {
            isSaving = false;
            console.error(err);
            if (indicator) {
                indicator.className = 'badge bg-danger-subtle text-danger border border-danger-subtle ms-2 font-mono extra-small';
                indicator.innerHTML = '<i class="mdi mdi-alert-circle-outline me-1"></i> Network Error';
            }
            if (!isAuto) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not connect to server to save changes.'
                });
            }
        });
    }

    const btnSaveTop = document.getElementById('btnSaveWordDocTop');
    const btnSaveBottom = document.getElementById('btnSaveWordDocBottom');
    if (btnSaveTop) btnSaveTop.addEventListener('click', () => saveTechnicalDescription({ isAutoSave: false }));
    if (btnSaveBottom) btnSaveBottom.addEventListener('click', () => saveTechnicalDescription({ isAutoSave: false }));

    // -------------------------------------------------------------
    // CONFIGURE SIGNATORIES & DTS ROUTING MODAL HANDLERS (DYNAMIC)
    // -------------------------------------------------------------
    const configSigModalEl = document.getElementById('configureSignatoriesModal');
    if (configSigModalEl) {
        function initSignatorySelect2($select) {
            if (!$select || !$select.length) {
                return;
            }
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#configureSignatoriesModal'),
                width: '100%',
                placeholder: $select.data('placeholder') || 'Search employee directory...',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('dss.select2.employees') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            tenant_id: '{{ $proposal->tenant_id }}'
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: (data.results || []).map(function (emp) {
                                return {
                                    id: emp.id,
                                    text: emp.text || ((emp.full_name || emp.name) + (emp.designation ? ' — ' + emp.designation : '') + (emp.office ? ' (' + emp.office + ')' : '')),
                                    name: emp.full_name || emp.name,
                                    designation: emp.designation || '',
                                    office: emp.office || (emp.department ? emp.department.unit_name : '')
                                };
                            }),
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
                const data = e.params.data;
                const $row = $(this).closest('.signatory-row');
                if (data.name) {
                    $row.find('.sig-name').val(data.name);
                }
                if (data.designation) {
                    $row.find('.sig-designation').val(data.designation);
                }
                if (data.office) {
                    $row.find('.sig-office').val(data.office);
                }
                $row.find('.select2-selection').removeClass('border border-danger');
            }).on('select2:unselect select2:clear', function (e) {
                const $row = $(this).closest('.signatory-row');
                $row.find('.sig-name').val('');
                $row.find('.sig-designation').val('');
                $row.find('.sig-office').val('');
            });
        }

        function reindexSignatoryRows() {
            const $rows = $('#signatoriesRepeaterList .signatory-row');
            const count = $rows.length;
            $('#signatoriesCountBadge').text(count + (count === 1 ? ' Approval Tier' : ' Approval Tiers'));

            $rows.each(function (index) {
                const stepNum = index + 1;
                const arrayIdx = index + 1; // 0 is prepared_by
                const $row = $(this);

                $row.attr('data-index', arrayIdx);
                $row.find('.step-badge').text('Step ' + stepNum);
                $row.find('.step-transit-label').text('DTS Transit Step ' + stepNum);

                // Update input name attributes
                $row.find('.sig-id').attr('name', `signatories[${arrayIdx}][id]`);
                $row.find('.sig-tier-role').attr('name', `signatories[${arrayIdx}][tier_role]`);
                $row.find('.sig-role-label').attr('name', `signatories[${arrayIdx}][role_label]`);
                $row.find('.signatory-employee-select2').attr('name', `signatories[${arrayIdx}][employee_id]`);
                $row.find('.sig-alignment').attr('name', `signatories[${arrayIdx}][alignment]`);
                $row.find('.sig-col').attr('name', `signatories[${arrayIdx}][position_column]`);
                $row.find('.sig-name').attr('name', `signatories[${arrayIdx}][name]`);
                $row.find('.sig-designation').attr('name', `signatories[${arrayIdx}][designation]`);
                $row.find('.sig-office').attr('name', `signatories[${arrayIdx}][office]`);

                // Update up/down button states
                $row.find('.btn-move-signatory-up').prop('disabled', index === 0);
                $row.find('.btn-move-signatory-down').prop('disabled', index === count - 1);
            });
        }

        let signatorySortable = null;
        function initSignatorySortable() {
            const listEl = document.getElementById('signatoriesRepeaterList');
            if (!listEl || typeof Sortable === 'undefined') return;

            if (signatorySortable) {
                signatorySortable.destroy();
            }

            signatorySortable = new Sortable(listEl, {
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function () {
                    reindexSignatoryRows();
                }
            });
        }

        function bindAllSignatorySelect2() {
            $('#signatoriesRepeaterList .signatory-employee-select2').each(function () {
                initSignatorySelect2($(this));
            });
            reindexSignatoryRows();
            initSignatorySortable();
        }

        // Initialize select2 and sortable on modal open
        if (configSigModalEl) {
            configSigModalEl.addEventListener('show.bs.modal', bindAllSignatorySelect2);
            configSigModalEl.addEventListener('shown.bs.modal', bindAllSignatorySelect2);
        }
        // Trigger quick suggest from Tab 4 button
        $(document).on('click', '#btnQuickSuggestSignatories', function (e) {
            e.preventDefault();
            $('#configureSignatoriesModal').modal('show');
            setTimeout(function () {
                $('#btnSuggestPastSignatories').trigger('click');
            }, 350);
        });

        // Auto-suggest past signatories ajax handler
        $(document).on('click', '#btnSuggestPastSignatories', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const originalHtml = $btn.html();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Suggesting...');

            $.ajax({
                url: "{{ route('dss.rie.proposals.signatories.suggest', $proposal->getRouteKey()) }}",
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(originalHtml);
                    if (res && res.success && Array.isArray(res.signatories)) {
                        const approvalSigs = res.signatories.filter(s => s.tier_role !== 'prepared');
                        if (approvalSigs.length === 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Signatories',
                                text: 'No past approval signatories found to suggest.'
                            });
                            return;
                        }

                        // Destroy existing Select2 instances and clear list
                        $('#signatoriesRepeaterList .signatory-row').each(function () {
                            const $sel = $(this).find('.signatory-employee-select2');
                            if ($sel.hasClass('select2-hidden-accessible')) {
                                $sel.select2('destroy');
                            }
                            $(this).remove();
                        });

                        const templateHtml = document.getElementById('signatoryRowTemplate').innerHTML;
                        approvalSigs.forEach(function (sig) {
                            const $newRow = $(templateHtml);
                            $newRow.find('.sig-id').val('');
                            $newRow.find('.sig-tier-role').val(sig.tier_role || 'reviewed');
                            $newRow.find('.sig-role-label').val(sig.role_label || '');
                            $newRow.find('.sig-name').val(sig.name || '');
                            $newRow.find('.sig-designation').val(sig.designation || '');
                            $newRow.find('.sig-office').val(sig.office || '');
                            $newRow.find('.sig-alignment').val(sig.alignment || 'left');
                            $newRow.find('.sig-col').val(sig.position_column || 1);

                            const $select = $newRow.find('.signatory-employee-select2');
                            if (sig.employee_id) {
                                const optionText = sig.employee_text || (sig.name + (sig.designation ? ' (' + sig.designation + ')' : ''));
                                const newOption = new Option(optionText, sig.employee_id, true, true);
                                $select.append(newOption);
                            }

                            $('#signatoriesRepeaterList').append($newRow);
                            initSignatorySelect2($select);
                        });

                        reindexSignatoryRows();
                        initSignatorySortable();

                        const sourceMsg = res.source === 'past_proposal'
                            ? `Signatories successfully suggested from past proposal (${res.past_proposal_code || 'Previous Proposal'}). Project leader & Dean have been aligned with your Unit.`
                            : 'Signatories loaded from official institutional matrix template.';

                        Swal.fire({
                            icon: 'success',
                            title: 'Signatories Suggested',
                            text: sourceMsg,
                            timer: 2800,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: res.message || 'Could not load suggested signatories.'
                        });
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to connect to server to load suggested signatories.'
                    });
                }
            });
        });

        // Add signatory button handler (triggers from top header or bottom toolbar)
        $(document).on('click', '.btn-add-signatory-trigger, #btnAddSignatoryRow', function (e) {
            e.preventDefault();
            const templateHtml = document.getElementById('signatoryRowTemplate').innerHTML;
            const $newRow = $(templateHtml);
            $('#signatoriesRepeaterList').append($newRow);
            initSignatorySelect2($newRow.find('.signatory-employee-select2'));
            reindexSignatoryRows();
            initSignatorySortable();

            // Smoothly scroll modal-body to the newly added row
            const modalBody = document.querySelector('#configureSignatoriesModal .modal-body');
            if (modalBody) {
                modalBody.scrollTo({
                    top: modalBody.scrollHeight,
                    behavior: 'smooth'
                });
            }

            setTimeout(() => {
                $newRow.find('.signatory-employee-select2').select2('open');
            }, 300);
        });

        // Preset role selection
        $(document).on('click', '.preset-role-option', function (e) {
            e.preventDefault();
            const label = $(this).data('label');
            const tier = $(this).data('tier');
            const align = $(this).data('align');
            const col = $(this).data('col');
            const $row = $(this).closest('.signatory-row');
            $row.find('.sig-role-label').val(label);
            $row.find('.sig-tier-role').val(tier);
            if (align) {
                $row.find('.sig-alignment').val(align);
                $row.find('.sig-col').val(col || (align === 'right' ? 2 : 1));
            }
        });

        // Alignment change handler
        $(document).on('change', '.sig-alignment', function () {
            const align = $(this).val();
            const $row = $(this).closest('.signatory-row');
            $row.find('.sig-col').val(align === 'right' ? 2 : (align === 'center' ? 2 : 1));
        });

        // Move row up
        $(document).on('click', '.btn-move-signatory-up', function () {
            const $row = $(this).closest('.signatory-row');
            const $prev = $row.prev('.signatory-row');
            if ($prev.length) {
                $row.insertBefore($prev);
                reindexSignatoryRows();
            }
        });

        // Move row down
        $(document).on('click', '.btn-move-signatory-down', function () {
            const $row = $(this).closest('.signatory-row');
            const $next = $row.next('.signatory-row');
            if ($next.length) {
                $row.insertAfter($next);
                reindexSignatoryRows();
            }
        });

        // Remove row
        $(document).on('click', '.btn-remove-signatory', function () {
            const $rows = $('#signatoriesRepeaterList .signatory-row');
            if ($rows.length <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum Required',
                    text: 'At least one approval signatory is required for document routing.'
                });
                return;
            }
            const $row = $(this).closest('.signatory-row');
            const $select = $row.find('.signatory-employee-select2');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $row.remove();
            reindexSignatoryRows();
            initSignatorySortable();
        });

        // Submit form
        $('#configureSignatoriesForm').on('submit', function (e) {
            e.preventDefault();

            // Validate that every sequential approval tier has an employee chosen from directory
            let hasMissing = false;
            let missingStepNum = 1;
            let $firstMissingSelect = null;

            $('#signatoriesRepeaterList .signatory-row').each(function (idx) {
                const $row = $(this);
                const empId = $row.find('.signatory-employee-select2').val();
                if (!empId) {
                    hasMissing = true;
                    missingStepNum = idx + 1;
                    $firstMissingSelect = $row.find('.signatory-employee-select2');
                    $row.find('.select2-selection').addClass('border border-danger');
                    return false; // Break out of loop
                } else {
                    $row.find('.select2-selection').removeClass('border border-danger');
                }
            });

            if (hasMissing) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Employee Selection Required',
                    text: `Please select an employee from the directory for Step ${missingStepNum}.`
                }).then(() => {
                    if ($firstMissingSelect) {
                        $firstMissingSelect.select2('open');
                    }
                });
                return false;
            }

            const $btn = $('#btnSaveSignatories');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: "{{ route('dss.rie.proposals.signatories.update', $proposal->getRouteKey()) }}",
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (res) {
                    bootstrap.Modal.getInstance(configSigModalEl)?.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Signatories Updated',
                        text: res.message || 'Approval signatories and DTS routes saved successfully.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Save',
                        text: xhr.responseJSON?.message || 'Unable to update signatories. Please check all fields.'
                    });
                }
            });
        });
    }

    // -------------------------------------------------------------
    // SUBMIT PROPOSAL FOR REVIEW (FROM HERO, TOOLBAR, OR TAB 4)
    // -------------------------------------------------------------
    $(document).on('click', '.btn-trigger-submit-review', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Submit Proposal for Formal Review?',
            html: `<div class="text-start extra-small text-muted">
                <p class="mb-2">Are you ready to submit <strong>{{ addslashes($proposal->title) }}</strong> for official collegiate evaluation and administrative review?</p>
                <div class="p-3 bg-light border rounded-3 mb-2 vstack gap-1">
                    <div><i class="mdi mdi-lock-outline text-warning me-1"></i> Part I &amp; Part II will be locked from further edits.</div>
                    <div><i class="mdi mdi-truck-fast-outline text-primary me-1"></i> Centralized DTS routing will advance to <strong>Step 1: Initial Collegiate Review</strong>.</div>
                    <div><i class="mdi mdi-account-check text-success me-1"></i> Project Leader submission signature will be sealed in the audit trail.</div>
                </div>
            </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="mdi mdi-send-check me-1"></i> Yes, Submit for Review',
            cancelButtonText: 'Keep as Draft',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Submitting Proposal...',
                    text: 'Activating transit routes and locking document...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('dss.rie.proposals.submit_for_review', $proposal->getRouteKey()) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remarks: 'Project Proposal submitted by Project Leader for Step 1 Technical Review.'
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Proposal Submitted!',
                            text: res.message || 'Routing has commenced.',
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = res.redirect_url || "{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'routing']) }}";
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            text: xhr.responseJSON?.message || 'Could not submit proposal. Please ensure all sections are ready.'
                        });
                    }
                });
            }
        });
    });
    // Connect SNSU DTS Tracking Number Form
    $('#formConnectTracking').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#btnSubmitConnectTracking');
        const tracking = $('#inputTrackingNumber').val().trim();
        const feedback = $('#connectTrackingFeedback');

        if (!tracking) return;

        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Connecting...');
        feedback.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: "{{ route('dss.rie.proposals.connect_tracking', $proposal->id) }}",
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

    // Refresh Live Tracking Trail
    $('#btnRefreshLiveTracking').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Refreshing...');

        $.ajax({
            url: "{{ route('dss.rie.proposals.fetch_tracking', $proposal->id) }}",
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
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to reach SNSU Central DTS API. Please ensure uis.dev.com is running.'
                });
            }
        });
    });

    // Save Accomplishment Report AJAX
    $('#btnSaveAccomplishment').on('click', function (e) {
        e.preventDefault();
        const btn = $(this);
        const form = $('#accomplishmentReportForm');
        const originalHtml = btn.html();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('dss.rie.proposals.accomplishment.update', $proposal->getRouteKey()) }}",
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                btn.prop('disabled', false).html(originalHtml);
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Accomplishment Report Saved!',
                        text: res.message || 'Updated successfully.',
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(originalHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: xhr.responseJSON?.message || 'Could not save accomplishment report. Please check input values.'
                });
            }
        });
    });

    // Auto-Draft Accomplishment Narrative via AI / Semantic Engine
    $('#btnAutoDraftAccomplishment').on('click', function (e) {
        e.preventDefault();
        const btn = $(this);
        const originalHtml = btn.html();

        Swal.fire({
            title: 'Auto-Draft Accomplishment Narrative?',
            text: 'Synthesize project objectives, target outputs, and community beneficiaries into formal academic accomplishment narrative?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: '<i class="mdi mdi-auto-fix me-1"></i> Generate Draft',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Synthesizing...');

                $.ajax({
                    url: "{{ route('dss.rie.proposals.accomplishment.generate_narrative', $proposal->getRouteKey()) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (res.success && res.data) {
                            if (res.data.executive_narrative) {
                                $('#txtExecutiveNarrative').val(res.data.executive_narrative);
                            }
                            if (res.data.activity_narrative) {
                                $('#txtActivityNarrative').val(res.data.activity_narrative);
                            }
                            if (res.data.summary_accomplishments) {
                                $('#txtSummaryAccomplishments').val(res.data.summary_accomplishments);
                            }
                            if (res.data.problems_encountered) {
                                $('#txtProblemsEncountered').val(res.data.problems_encountered);
                            }
                            if (res.data.solutions_adopted) {
                                $('#txtSolutionsAdopted').val(res.data.solutions_adopted);
                            }
                            if (res.data.impact_feedback) {
                                $('#txtImpactFeedback').val(res.data.impact_feedback);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Accomplishment Narrative Drafted!',
                                text: `Synthesized successfully using ${res.source || 'DSS Semantic Engine'}. Review the generated sections below and click Save.`,
                                timer: 2200,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false).html(originalHtml);
                        Swal.fire('Error', 'Unable to draft narrative at this time.', 'error');
                    }
                });
            }
        });
    });

    @if($proposal->extensionPlan)
    // Dynamic GDrive input row addition in proposal modal
    $('#btnPropAddGdriveInput').on('click', function() {
        const rowHtml = `
            <div class="input-group input-group-sm prop-gdrive-entry-row">
                <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                <input type="url" class="form-control prop-gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                <button type="button" class="btn btn-outline-danger btn-remove-prop-gdrive-row" title="Remove this link">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>`;
        $('#propGdriveInputsWrapper').append(rowHtml);
    });

    $(document).on('click', '.btn-remove-prop-gdrive-row', function() {
        $(this).closest('.prop-gdrive-entry-row').remove();
    });

    // Dropzone click trigger
    $('#propProofDropzone').on('click', function(e) {
        if (!$(e.target).is('#propProofFileInput')) {
            $('#propProofFileInput').trigger('click');
        }
    });

    // File selection badges preview
    $('#propProofFileInput').on('change', function() {
        const files = this.files;
        const badgesContainer = $('#propProofSelectedFilesBadges');
        badgesContainer.empty();
        if (files && files.length > 0) {
            badgesContainer.removeClass('d-none');
            Array.from(files).forEach((f, idx) => {
                const mb = (f.size / (1024 * 1024)).toFixed(2);
                const badge = $(`
                    <span class="badge bg-white text-dark border extra-small px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-2xs">
                        <i class="mdi mdi-file-document-outline text-primary fs-6"></i>
                        <span class="fw-medium text-truncate" style="max-width: 180px;">${f.name}</span>
                        <span class="text-muted">(${mb} MB)</span>
                    </span>
                `);
                badgesContainer.append(badge);
            });
        } else {
            badgesContainer.addClass('d-none');
        }
    });

    $(document).on('click', '.btn-trigger-upload', function() {
        const actId = $(this).data('id');
        const actTitle = $(this).data('title');
        $('#uploadMilestoneActivityId').val(actId);
        $('#uploadMilestoneStageTitle').text(actTitle);
        $('#propProofFileInput').val('');
        $('#propProofSelectedFilesBadges').empty().addClass('d-none');
        $('#propProofRemarksInput').val('');
        $('#propUploadFeedbackAlert').addClass('d-none').removeClass('alert-success alert-danger').text('');
        
        // Reset GDrive rows to 1 single blank row
        $('#propGdriveInputsWrapper').html(`
            <div class="input-group input-group-sm prop-gdrive-entry-row">
                <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                <input type="url" class="form-control prop-gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
            </div>
        `);

        const modalEl = document.getElementById('modalUploadMilestoneProof');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    $('#formUploadMilestoneProof').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const actId = $('#uploadMilestoneActivityId').val();
        const btn = $('#btnSubmitPropMilestoneProof');
        const feedback = $('#propUploadFeedbackAlert');

        if (!actId) return;

        // Check if at least one file OR at least one non-empty Google Drive URL is provided
        const filesCount = $('#propProofFileInput')[0]?.files?.length || 0;
        let gdriveCount = 0;
        $('.prop-gdrive-url-field').each(function() {
            if ($(this).val().trim() !== '') {
                gdriveCount++;
            }
        });

        if (filesCount === 0 && gdriveCount === 0) {
            feedback.removeClass('d-none alert-success').addClass('alert-danger')
                .html('<i class="mdi mdi-alert-circle me-1"></i> Please select at least one document file to upload OR provide a valid Google Drive link.');
            return;
        }

        const formData = new FormData(form);
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Uploading...');
        feedback.addClass('d-none');

        let uploadUrl = "{{ route('dss.rie.plans.activity.upload_document', ['id' => $proposal->extensionPlan->getRouteKey(), 'activityId' => ':actId']) }}";
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

    $(document).on('click', '.btn-delete-proof', function() {
        const actId = $(this).data('id');
        const docId = $(this).data('doc-id') || '';
        const actTitle = $(this).data('title');
        if (!actId) return;

        const doDelete = () => {
            let deleteUrl = "{{ route('dss.rie.plans.activity.delete_document', ['id' => $proposal->extensionPlan->getRouteKey(), 'activityId' => ':actId']) }}";
            deleteUrl = deleteUrl.replace(':actId', actId);
            if (docId) {
                deleteUrl += '/' + docId;
            }

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.success) {
                        if (window.dssToast) {
                            window.dssToast.success('Proof Removed', res.message);
                        }
                        setTimeout(() => { window.location.reload(); }, 600);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to delete proof.';
                    if (window.dssToast) window.dssToast.error('Error', msg);
                    else alert(msg);
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Remove Document Attachment?',
                text: `Are you sure you want to remove '${actTitle}' from this milestone?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove file'
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else if (confirm(`Remove document proof for '${actTitle}'?`)) {
            doDelete();
        }
    });
    @endif
});
</script>

<!-- CONNECT SNSU TRACKING NUMBER MODAL -->
<div class="modal fade" id="connectTrackingModal" tabindex="-1" aria-labelledby="connectTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header bg-dark text-white py-2.5 px-3">
                <h6 class="modal-title fw-bold fs-6 m-0" id="connectTrackingModalLabel">
                    <i class="mdi mdi-qrcode-scan me-1.5 text-primary"></i> Connect SNSU DTS Tracking Number
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formConnectTracking">
                @csrf
                <div class="modal-body p-3">
                    <p class="text-muted extra-small mb-3">
                        Enter the official Document Tracking Number / QR code issued from the central SNSU Tracking System (<code>uis.dev.com</code>).
                    </p>
                    <div class="mb-3">
                        <label for="inputTrackingNumber" class="form-label extra-small fw-bold text-uppercase text-dark">
                            Official SNSU Tracking # / QR Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm font-mono fs-6" id="inputTrackingNumber" name="tracking_number"
                               value="{{ $proposal->tracking_number }}" placeholder="e.g. 202610240001 or 000000022120" required>
                        <div class="form-text extra-small text-muted">
                            Accepts 12-digit QR codes or DTS Document IDs.
                        </div>
                    </div>
                    <div id="connectTrackingFeedback" class="alert d-none extra-small py-2 px-3 mb-0"></div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" id="btnSubmitConnectTracking">
                        <i class="mdi mdi-link-variant me-1"></i> Verify &amp; Connect
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($proposal->extensionPlan)
<!-- Upload Wet-Signed Milestone Document Modal (Same Form as Institutional Repository) -->
<!-- Upload Milestone Evidence Modal -->
<div class="modal fade" id="modalUploadMilestoneProof" tabindex="-1" aria-labelledby="modalUploadMilestoneProofLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 align-items-center">
                <div>
                    <h6 class="modal-title fw-semibold text-dark m-0" id="modalUploadMilestoneProofLabel">Upload Milestone Evidence</h6>
                    <div class="text-muted" style="font-size: 12px;">Attach verified documents or cloud links to complete this activity milestone.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formUploadMilestoneProof" enctype="multipart/form-data">
                <input type="hidden" id="uploadMilestoneActivityId" name="activity_id" value="">
                <div class="modal-body p-4">
                    <!-- Milestone Stage Indicator -->
                    <div class="p-3 rounded-2 bg-light border border-light-subtle d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-uppercase text-muted fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Milestone Stage</div>
                            <div class="fw-semibold text-dark" id="uploadMilestoneStageTitle" style="font-size: 14px;">—</div>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-normal" style="font-size: 11px;">Document Evidence</span>
                    </div>

                    <!-- 1. Physical Files Upload -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
                            Document Files <span class="text-muted fw-normal">(PDF, Scanned Copies, Office Files)</span>
                        </label>
                        <div class="milestone-dropzone p-3 text-center rounded-2 border border-2 border-dashed"
                             id="propProofDropzone"
                             style="background: #fbfcfe; border-color: #cbd5e1; cursor: pointer; transition: all 0.2s ease;">
                            <input class="d-none" type="file" id="propProofFileInput" name="files[]" multiple accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx,.zip">
                            <i class="mdi mdi-cloud-upload-outline text-secondary fs-3 d-block mb-1"></i>
                            <div class="text-dark fw-medium" style="font-size: 13px;">
                                <span class="text-primary text-decoration-underline">Click to browse</span> or drag files here
                            </div>
                            <div class="text-muted mt-0.5" style="font-size: 11.5px;">
                                Accepts PDF, Word, Excel, scanned images, or ZIP up to 50MB
                            </div>
                        </div>
                        <div id="propProofSelectedFilesBadges" class="d-none mt-2 d-flex flex-wrap gap-1.5"></div>
                    </div>

                    <!-- 2. Google Drive Links -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold text-dark m-0" style="font-size: 13px;">
                                Google Drive / Cloud Links
                            </label>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-medium" id="btnPropAddGdriveInput" style="font-size: 12px;">
                                <i class="mdi mdi-plus me-0.5"></i> Add Link
                            </button>
                        </div>
                        <div id="propGdriveInputsWrapper" class="d-flex flex-column gap-2">
                            <div class="input-group input-group-sm prop-gdrive-entry-row">
                                <span class="input-group-text bg-white border-end-0 text-success"><i class="mdi mdi-google-drive"></i></span>
                                <input type="url" class="form-control border-start-0 prop-gdrive-url-field" name="gdrive_urls[]" placeholder="https://drive.google.com/file/d/...">
                            </div>
                        </div>
                        <div class="form-text text-muted mt-1" style="font-size: 11.5px;">
                            <i class="mdi mdi-information-outline me-1"></i>Please ensure Google Drive links are set to <strong>Anyone with the link can view</strong> for reviewer access.
                        </div>
                    </div>

                    <!-- 3. Notes / Remarks -->
                    <div class="mb-2">
                        <label for="propProofRemarksInput" class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
                            Notes / Remarks <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea class="form-control form-control-sm" id="propProofRemarksInput" name="remarks" rows="2" placeholder="e.g., Signed MOA with partner agency, Approved Activity Design, or participant attendance sheets..."></textarea>
                    </div>

                    <div id="propUploadFeedbackAlert" class="alert d-none extra-small mb-0 mt-3" role="alert"></div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light border-top px-4 py-2.5 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 11.5px;">
                        <i class="mdi mdi-shield-check-outline text-success me-0.5"></i> Auto-hashed (SHA-256) for audit integrity
                    </span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnSubmitPropMilestoneProof" class="btn btn-sm btn-primary px-4 fw-medium">
                            <i class="mdi mdi-upload me-1"></i> Upload Evidence
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endpush
