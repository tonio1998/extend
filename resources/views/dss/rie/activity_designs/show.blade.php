@extends('layouts.dss.app')

@section('title', "Activity Design: {$design->design_code} — SNSU RIE Suite")

@push('styles')
<style>
    /* Executive Institutional Sheet Styling */
    .activity-design-canvas-wrapper {
        background-color: #f1f5f9;
        padding-top: 1.5rem;
        padding-bottom: 3.5rem;
    }
    .activity-design-sheet {
        max-width: 940px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        padding: 48px 56px;
    }
    .sheet-section-title {
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #0f172a;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 6px;
        margin-top: 2.25rem;
        margin-bottom: 1.25rem;
    }
    .design-meta-table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1.5rem;
    }
    .design-meta-table th {
        width: 230px;
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.8125rem;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .design-meta-table td {
        font-size: 0.8125rem;
        color: #1e293b;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .sig-signature-box {
        padding: 10px 12px;
    }
    .sig-signature-line {
        border-top: 1px solid #0f172a;
        padding-top: 6px;
        margin-top: 36px;
    }
    .sig-stamp {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 4px;
    }
    .sig-stamp-signed {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .no-print, .dss-navbar, .dss-sidebar, .dss-footer {
            display: none !important;
        }
        .activity-design-canvas-wrapper {
            background: transparent !important;
            padding: 0 !important;
        }
        .activity-design-sheet {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 activity-design-canvas-wrapper">

    <!-- Top Action Toolbar (Clean, Executive, Uncrowded) -->
    <div class="max-w-940 mx-auto mb-3 no-print" style="max-width: 940px;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white p-3 rounded-3 border shadow-2xs">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb extra-small font-mono m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dss.rie.activity_designs.index') }}" class="text-decoration-none text-muted">Activity Designs</a></li>
                        <li class="breadcrumb-item active text-dark fw-bold">{{ $design->design_code }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-dark text-white font-mono extra-small">{{ $design->design_code }}</span>
                    @if($proposal)
                        <a href="{{ route('dss.rie.proposals.show', $proposal->getRouteKey()) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none extra-small font-mono" title="Linked Extension Proposal">
                            <i class="mdi mdi-link-variant me-0.5"></i> Proposal: {{ $proposal->proposal_code }}
                        </a>
                    @endif
                    <span class="badge {{ $design->status === 'approved' ? 'bg-success' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }} font-mono extra-small text-uppercase">
                        {{ $design->status ?: 'Draft' }}
                    </span>
                </div>
            </div>

            <!-- Sleek Action Group: 1 Primary Button + Dropdown Actions Menu -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('dss.rie.activity_designs.pdf', $design->getRouteKey()) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1.5" title="Generate Official FPDF Document">
                    <i class="mdi mdi-file-pdf-box fs-6"></i>
                    <span>Download PDF</span>
                </a>

                <div class="dropdown" style="position: relative;">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="More Document Options">
                        <span style="pointer-events: none;">Actions</span>
                        <i class="mdi mdi-chevron-down text-muted ms-0.5" style="pointer-events: none;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" style="border-radius: 12px; font-size: 12.5px; min-width: 240px; z-index: 1055 !important;">
                        <li class="px-3 py-1 text-muted extra-small fw-bold text-uppercase">Document Operations</li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.documents.signatories.edit', ['type' => 'activity_design', 'id' => $design->getRouteKey()]) }}">
                                <i class="mdi mdi-account-cog-outline text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold">Configure Signatories</div>
                                    <div class="extra-small text-muted">Approval Chain &amp; Signers</div>
                                </div>
                            </a>
                        </li>
                        @if($wetSignedDoc)
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-success fw-semibold" href="{{ $wetSignedDoc->download_url }}" target="_blank">
                                    <i class="mdi mdi-file-check-outline fs-5"></i>
                                    <div>
                                        <div>Wet-Signed Copy</div>
                                        <div class="extra-small text-muted">Archived signed file</div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>
                        <li class="px-3 py-1 text-muted extra-small fw-bold text-uppercase">Lifecycle Cross-Links</li>
                        @if($proposal)
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.rie.proposals.show', $proposal->getRouteKey()) }}">
                                    <i class="mdi mdi-file-table-outline text-primary fs-5"></i>
                                    <div>
                                        <div>View Source Proposal</div>
                                        <div class="extra-small text-muted font-mono">{{ $proposal->proposal_code }}</div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($plan)
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}">
                                    <i class="mdi mdi-clipboard-text-outline text-info fs-5"></i>
                                    <div>
                                        <div>View Extension Plan</div>
                                        <div class="extra-small text-muted font-mono">{{ $plan->plan_code }}</div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-1.5 d-flex align-items-center gap-2" href="#" onclick="window.print(); return false;">
                                <i class="mdi mdi-printer text-dark fs-5"></i>
                                <span>Print Document Sheet</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-1.5 d-flex align-items-center gap-2" href="{{ route('dss.rie.activity_designs.index') }}">
                                <i class="mdi mdi-arrow-left text-secondary fs-5"></i>
                                <span>Back to Activity Registry</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Institutional Activity Design Document Sheet -->
    <div class="activity-design-sheet">

        <!-- University Header & Letterhead -->
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-uppercase extra-small text-muted font-mono" style="letter-spacing: 1.5px;">Republic of the Philippines</div>
            <h5 class="fw-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.5px;">Surigao del Norte State University</h5>
            <div class="text-muted extra-small">Surigao City, Philippines &bull; www.snsu.edu.ph</div>
            <div class="fw-semibold text-secondary extra-small mt-1 text-uppercase">Office of Research, Innovation, and Extension Services</div>
            <div class="mt-3">
                <span class="badge bg-dark text-white font-mono px-4 py-1.5 text-uppercase fw-bold" style="letter-spacing: 2px; font-size: 13px;">
                    ACTIVITY DESIGN
                </span>
            </div>
        </div>

        <!-- Document Control Reference Block -->
        <div class="d-flex flex-wrap justify-content-between align-items-center extra-small font-mono text-muted mb-4 pb-2 border-bottom">
            <div>Doc Ref: <strong>SNSU-RIE-AD-{{ date('Y') }}</strong></div>
            <div>Design Code: <strong>{{ $design->design_code }}</strong></div>
            @if($proposal)
                <div>Source Proposal: <strong>{{ $proposal->proposal_code }}</strong></div>
            @endif
            <div>Status: <strong class="text-success text-uppercase">{{ $design->status ?: 'Draft' }}</strong></div>
        </div>

        <!-- Section I: Basic Activity Information -->
        <div class="sheet-section-title">I. General Information</div>
        <table class="design-meta-table">
            <tbody>
                <tr>
                    <th>Activity Title</th>
                    <td class="fw-bold text-dark fs-6">{{ $design->title }}</td>
                </tr>
                <tr>
                    <th>Program / Banner Project</th>
                    <td>
                        {{ $design->banner_program ?: ($proposal?->banner_program ?: ($plan?->program_name ?: 'Institutional Extension Program')) }}
                        @if($design->academicProgram)
                            <span class="text-muted">({{ $design->academicProgram->program_name }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Implementing Unit / College</th>
                    <td>{{ $design->unit?->unit_name ?: ($proposal?->unit?->unit_name ?: 'College Extension Unit') }}</td>
                </tr>
                <tr>
                    <th>Partner / Collaborating Agencies</th>
                    <td>{{ $design->collaborating_agencies ?: 'Community Stakeholders / Local Government Unit' }}</td>
                </tr>
                <tr>
                    <th>Target Beneficiaries</th>
                    <td>
                        <strong>{{ $design->target_beneficiaries ?: ($proposal?->beneficiaries ?: 'Community Members') }}</strong>
                        @if($design->target_beneficiaries_count || ($plan && $plan->target_beneficiaries_count))
                            <span class="badge bg-light text-dark border ms-1 font-mono">({{ number_format((int)($design->target_beneficiaries_count ?: $plan->target_beneficiaries_count)) }} Pax)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Implementation Period / Venue</th>
                    <td>
                        <strong>{{ $design->duration_text ?: ($proposal?->duration_text ?: 'Scheduled Implementation') }}</strong>
                        @if($design->project_location)
                            <div class="text-muted extra-small mt-0.5"><i class="mdi mdi-map-marker me-1"></i>{{ $design->project_location }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Project Leader</th>
                    <td>
                        <strong>{{ $design->project_leader_name }}</strong>
                        <span class="text-muted">({{ $design->project_leader_title ?: 'Project Leader' }})</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Section II: Rationale & Objectives -->
        <div class="sheet-section-title">II. Context, Rationale &amp; Objectives</div>
        <div class="mb-3">
            <h6 class="fw-bold text-dark extra-small text-uppercase mb-1">A. Context &amp; Significance</h6>
            <p class="small text-secondary mb-3" style="line-height: 1.65; text-align: justify;">
                {{ $design->context_rationale ?: ($proposal?->introduction ?: ($proposal?->relevance_narrative ?: 'This extension activity is undertaken as part of the university mandate to translate technical and academic expertise into meaningful community capacity building.')) }}
            </p>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark extra-small text-uppercase mb-1">B. Activity Objectives</h6>
            @if(!empty($design->general_objective))
                <div class="p-2.5 bg-light rounded-2 border-start border-3 border-primary small text-dark fw-semibold mb-2.5">
                    <strong>General Objective:</strong> {{ $design->general_objective }}
                </div>
            @endif
            @if(!empty($design->specific_objectives) && is_array($design->specific_objectives))
                <ul class="small text-secondary ps-3 mb-0" style="line-height: 1.6;">
                    @foreach($design->specific_objectives as $obj)
                        <li class="mb-1">{{ is_array($obj) ? ($obj['text'] ?? implode(' ', $obj)) : $obj }}</li>
                    @endforeach
                </ul>
            @elseif($proposal && !empty($proposal->specific_objectives) && is_array($proposal->specific_objectives))
                <ul class="small text-secondary ps-3 mb-0" style="line-height: 1.6;">
                    @foreach($proposal->specific_objectives as $obj)
                        <li class="mb-1">{{ is_array($obj) ? ($obj['text'] ?? implode(' ', $obj)) : $obj }}</li>
                    @endforeach
                </ul>
            @elseif($plan && !empty($plan->objectives))
                <div class="small text-secondary" style="white-space: pre-line; line-height: 1.6;">{{ $plan->objectives }}</div>
            @endif
        </div>

        <!-- Section III: Schedule / Program Matrix of Activities -->
        <div class="sheet-section-title">III. Program Matrix &amp; Schedule of Milestones</div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered mb-0 align-middle">
                <thead class="table-light extra-small text-uppercase font-mono">
                    <tr>
                        <th style="width: 45px;" class="text-center">#</th>
                        <th>Milestone / Activity Component</th>
                        <th style="min-width: 170px;" class="text-center">Target Date / Period</th>
                        <th style="width: 130px;" class="text-center">Verification</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($activities as $act)
                        <tr>
                            <td class="text-center font-mono fw-bold text-muted">{{ $act['num'] ?? ($loop->iteration) }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $act['title'] ?? 'Scheduled Milestone' }}</div>
                                @if(!empty($act['remarks']))
                                    <div class="text-muted extra-small">{{ $act['remarks'] }}</div>
                                @endif
                            </td>
                            <td class="text-center font-mono extra-small">
                                {{ $act['target_date'] ?? 'To be scheduled' }}
                            </td>
                            <td class="text-center">
                                @if(!empty($act['is_na']))
                                    <span class="badge bg-light text-muted border extra-small">Exempted</span>
                                @elseif(!empty($act['is_completed']))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">
                                        <i class="mdi mdi-check-circle me-0.5"></i> Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small">
                                        Pending Proof
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted extra-small py-3">
                                No scheduled activities defined in the parent Extension Plan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Section IV: Budgetary Allocation -->
        <div class="sheet-section-title">IV. Budgetary Requirements &amp; Allocation</div>
        <table class="design-meta-table mb-4">
            <tbody>
                <tr>
                    <th>Total Proposed Budget</th>
                    <td class="fw-bold font-mono text-dark fs-6">{{ $design->formatted_budget_total ?: ($design->budget_total_display ?: '₱0.00') }}</td>
                </tr>
                <tr>
                    <th>SNSU Institutional Share</th>
                    <td class="font-mono text-secondary">{{ $design->formatted_snsu_share ?: '₱0.00' }}</td>
                </tr>
                <tr>
                    <th>Partner / External Share</th>
                    <td class="font-mono text-secondary">{{ $design->formatted_partner_share ?: '₱0.00' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ========================================================================= -->
        <!-- SECTION V: REVIEW & APPROVAL MATRIX (OFFICIAL SIGNATORIES PANEL)          -->
        <!-- Layout strictly matching the institutional matrix:                        -->
        <!-- 1. Prepared by (Project Leader)                                           -->
        <!-- 2. Reviewed by (College Extension Head)                                   -->
        <!-- 3. Noted by (College Dean)                                                -->
        <!-- 4. Concurred by (Extension Director)                                      -->
        <!-- Followed by Post-Concurred Tiers:                                         -->
        <!-- 5. Recommending Approval: ROMY JUN A. SUNICO, DTE (VP for RIE) [Centered]  -->
        <!-- 6. Two-Column Certification:                                              -->
        <!--    Left: Certified as to allotment: MARY ANN M. NARCISO, MBA (Budget Off) -->
        <!--    Right: Certified as to funds: IVY JEAN J. TURNO (Accountant III)       -->
        <!-- 7. FELICITY MAE M. ECLEO (CAO-Designate) [Centered]                       -->
        <!-- 8. Approved by: ROWENA A. PLANDO, Ph.D. (University President) [Centered] -->
        <!-- ========================================================================= -->
        <div class="sheet-section-title">V. Review &amp; Approval Matrix</div>

        @php
            $sigCollection = isset($signatories) ? collect($signatories) : collect();

            // Tier 1: Up to Concurred
            $prepSig = $sigCollection->firstWhere('tier_role', 'prepared')
                ?: (object)[
                    'role_label' => 'Prepared by:',
                    'signatory_name' => $design->project_leader_name,
                    'designation' => $design->project_leader_title ?: 'Project Leader',
                    'status' => 'pending'
                ];

            $revSig = $sigCollection->firstWhere('tier_role', 'reviewed')
                ?: (object)[
                    'role_label' => 'Reviewed by:',
                    'signatory_name' => $plan?->reviewed_by_name ?: ($design->unit?->extension_head_name ?: 'College Head for Extension'),
                    'designation' => $plan?->reviewed_by_designation ?: ($design->unit?->extension_head_title ?: 'Head, College Extension Unit'),
                    'status' => 'pending'
                ];

            $notedSig = $sigCollection->firstWhere('tier_role', 'noted')
                ?: (object)[
                    'role_label' => 'Noted by:',
                    'signatory_name' => $plan?->noted_by_name ?: ($design->unit?->head_name ?: 'College Dean'),
                    'designation' => $plan?->noted_by_designation ?: ($design->unit?->head_title ?: 'College Dean'),
                    'status' => 'pending'
                ];

            $concSig = $sigCollection->first(fn($s) => str_starts_with($s->tier_role ?? '', 'concurred') || str_contains(strtolower($s->role_label ?? ''), 'concurred'))
                ?: (object)[
                    'role_label' => 'Concurred by:',
                    'signatory_name' => $plan?->concurred_by_2_name ?: ($plan?->concurred_by_name ?: 'ALVIE ELMA C. PLAZA, MA'),
                    'designation' => $plan?->concurred_by_2_designation ?: ($plan?->concurred_by_designation ?: 'Director, Extension Services'),
                    'status' => 'pending'
                ];

            // Tier 2: Post-Concurred Signatories
            $recVpeSig = $sigCollection->first(fn($s) => $s->tier_role === 'recommending_approval' || (str_contains(strtolower($s->role_label ?? ''), 'recommending') && str_contains(strtolower(($s->designation ?? '').' '.($s->signatory_name ?? '')), 'sunico')))
                ?: $sigCollection->first(fn($s) => str_contains(strtolower($s->role_label ?? ''), 'recommending'))
                ?: (object)[
                    'role_label' => 'Recommending Approval:',
                    'signatory_name' => 'ROMY JUN A. SUNICO, DTE',
                    'designation' => 'Vice President for RIE',
                    'status' => 'pending'
                ];

            $budgetSig = $sigCollection->first(fn($s) => $s->tier_role === 'certified_allotment' || str_contains(strtolower(($s->role_label ?? '').' '.($s->tier_role ?? '')), 'allotment'))
                ?: (object)[
                    'role_label' => 'Certified as to availability of allotment:',
                    'signatory_name' => 'MARY ANN M. NARCISO, MBA',
                    'designation' => 'Budget Officer III',
                    'status' => 'pending'
                ];

            $acctSig = $sigCollection->first(fn($s) => $s->tier_role === 'certified_funds' || str_contains(strtolower(($s->role_label ?? '').' '.($s->tier_role ?? '')), 'funds'))
                ?: (object)[
                    'role_label' => 'Certified as to availability of funds:',
                    'signatory_name' => 'IVY JEAN J. TURNO',
                    'designation' => 'Accountant III',
                    'status' => 'pending'
                ];

            $caoSig = $sigCollection->first(fn($s) => $s->tier_role === 'cao_designate' || str_contains(strtolower(($s->designation ?? '').' '.($s->signatory_name ?? '')), 'ecleo') || str_contains(strtolower($s->designation ?? ''), 'cao'))
                ?: (object)[
                    'role_label' => '',
                    'signatory_name' => 'FELICITY MAE M. ECLEO',
                    'designation' => 'CAO-Designate',
                    'status' => 'pending'
                ];

            $presSig = $sigCollection->first(fn($s) => $s->tier_role === 'approved' || str_contains(strtolower($s->role_label ?? ''), 'approved') || str_contains(strtolower($s->designation ?? ''), 'president'))
                ?: (object)[
                    'role_label' => 'Approved by:',
                    'signatory_name' => 'ROWENA A. PLANDO, Ph.D.',
                    'designation' => 'University President III',
                    'status' => 'pending'
                ];
        @endphp

        <!-- 1. TIER 1 SIGNATORIES (4-COLUMN BALANCED ROW) -->
        <div class="row g-4 pt-2 mb-4">
            <!-- Prepared by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $prepSig->role_label ?: 'Prepared by:' }}</div>
                <div class="sig-signature-line">
                    <div class="fw-bold text-dark small text-uppercase">{{ $prepSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $prepSig->designation ?: 'Project Leader' }}</div>
                    @if(($prepSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>

            <!-- Reviewed by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $revSig->role_label ?: 'Reviewed by:' }}</div>
                <div class="sig-signature-line">
                    <div class="fw-bold text-dark small text-uppercase">{{ $revSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $revSig->designation ?: 'Head, College Extension Unit' }}</div>
                    @if(($revSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>

            <!-- Noted by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $notedSig->role_label ?: 'Noted by:' }}</div>
                <div class="sig-signature-line">
                    <div class="fw-bold text-dark small text-uppercase">{{ $notedSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $notedSig->designation ?: 'College Dean' }}</div>
                    @if(($notedSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>

            <!-- Concurred by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $concSig->role_label ?: 'Concurred by:' }}</div>
                <div class="sig-signature-line">
                    <div class="fw-bold text-dark small text-uppercase">{{ $concSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $concSig->designation ?: 'Director, Extension Services' }}</div>
                    @if(($concSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. POST-CONCURRED TIER: RECOMMENDING APPROVAL (CENTERED) -->
        <div class="row justify-content-center mb-4 pt-3">
            <div class="col-12 col-md-6 text-center">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $recVpeSig->role_label ?: 'Recommending Approval:' }}</div>
                <div class="sig-signature-line mx-auto" style="max-width: 340px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $recVpeSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $recVpeSig->designation ?: 'Vice President for RIE' }}</div>
                    @if(($recVpeSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. POST-CONCURRED TIER: 2-COLUMN FINANCIAL CERTIFICATION (ALLOTMENT & FUNDS) -->
        <div class="row g-4 mb-4 pt-3">
            <!-- Left: Certified as to availability of allotment -->
            <div class="col-6 text-start">
                <div class="extra-small text-dark fw-bold fst-italic mb-3">{{ $budgetSig->role_label ?: 'Certified as to availability of allotment:' }}</div>
                <div class="sig-signature-line me-auto" style="max-width: 320px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $budgetSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $budgetSig->designation ?: 'Budget Officer III' }}</div>
                    @if(($budgetSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>

            <!-- Right: Certified as to availability of funds -->
            <div class="col-6 text-end">
                <div class="extra-small text-dark fw-bold fst-italic mb-3">{{ $acctSig->role_label ?: 'Certified as to availability of funds:' }}</div>
                <div class="sig-signature-line ms-auto" style="max-width: 320px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $acctSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $acctSig->designation ?: 'Accountant III' }}</div>
                    @if(($acctSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. POST-CONCURRED TIER: CAO-DESIGNATE (CENTERED) -->
        <div class="row justify-content-center mb-4 pt-3">
            <div class="col-12 col-md-6 text-center">
                @if(!empty($caoSig->role_label))
                    <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $caoSig->role_label }}</div>
                @else
                    <div class="mb-3" style="height: 18px;"></div>
                @endif
                <div class="sig-signature-line mx-auto" style="max-width: 340px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $caoSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $caoSig->designation ?: 'CAO-Designate' }}</div>
                    @if(($caoSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 5. POST-CONCURRED TIER: APPROVED BY (UNIVERSITY PRESIDENT - CENTERED) -->
        <div class="row justify-content-center pt-3">
            <div class="col-12 col-md-6 text-center">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $presSig->role_label ?: 'Approved by:' }}</div>
                <div class="sig-signature-line mx-auto" style="max-width: 340px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $presSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $presSig->designation ?: 'University President III' }}</div>
                    @if(($presSig->status ?? '') === 'signed')
                        <span class="sig-stamp sig-stamp-signed"><i class="mdi mdi-check"></i> Signed</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
