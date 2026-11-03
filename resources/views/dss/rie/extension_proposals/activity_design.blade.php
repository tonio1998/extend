@extends('layouts.dss.app')

@section('title', "Activity Design: {$proposal->proposal_code} — RIE System")

@push('styles')
<style>
    .activity-design-sheet {
        max-width: 900px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
    }
    .sheet-section-title {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #0f172a;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 4px;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .design-meta-table th {
        width: 220px;
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.8125rem;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
    }
    .design-meta-table td {
        font-size: 0.8125rem;
        color: #1e293b;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
    }
    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
        }
        .no-print, .dss-navbar, .dss-sidebar, .dss-footer {
            display: none !important;
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
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Top Action Bar (Screen Only) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb extra-small font-mono m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.proposals.index') }}" class="text-decoration-none text-muted">Proposals</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.proposals.show', $proposal->getRouteKey()) }}" class="text-decoration-none text-muted">{{ $proposal->proposal_code }}</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold">Activity Design</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white font-mono extra-small">STEP 3 OF 3</span>
                <h4 class="fw-bold text-dark m-0">Activity Design</h4>
                <span class="text-muted small">&bull; Derived from Proposal &amp; Extension Plan</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.rie.proposals.show', $proposal->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Proposal
            </a>
            @if($plan)
                <a href="{{ route('dss.rie.plans.show', $plan->getRouteKey()) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="mdi mdi-clipboard-text-outline me-1"></i> View Extension Plan
                </a>
            @endif
            @if($wetSignedDoc)
                <a href="{{ $wetSignedDoc->download_url }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold shadow-2xs">
                    <i class="mdi mdi-file-check-outline me-1"></i> Wet-Signed Copy
                </a>
            @endif
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-2xs" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> Print Activity Design
            </button>
        </div>
    </div>

    <!-- Official Activity Design Document Sheet -->
    <div class="activity-design-sheet p-4 p-md-5 rounded-3 mb-5">

        <!-- University Header & Letterhead -->
        <div class="text-center pb-3 border-bottom mb-4">
            <div class="text-uppercase extra-small text-muted font-mono" style="letter-spacing: 1.5px;">Republic of the Philippines</div>
            <h5 class="fw-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.5px;">Surigao del Norte State University</h5>
            <div class="text-muted extra-small">Surigao City, Philippines &bull; www.snsu.edu.ph</div>
            <div class="fw-semibold text-secondary extra-small mt-1 text-uppercase">Office of Research, Innovation, and Extension Services</div>
            <div class="mt-3">
                <span class="badge bg-dark text-white font-mono px-3 py-1.5 text-uppercase fw-bold" style="letter-spacing: 2px; font-size: 13px;">
                    ACTIVITY DESIGN
                </span>
            </div>
        </div>

        <!-- Document Control Reference Block -->
        <div class="d-flex justify-content-between align-items-center extra-small font-mono text-muted mb-3 pb-2 border-bottom">
            <div>Doc Ref: <strong>SNSU-RIE-AD-{{ date('Y') }}</strong></div>
            <div>Proposal Code: <strong>{{ $proposal->proposal_code }}</strong></div>
            @if($plan)
                <div>Plan Code: <strong>{{ $plan->plan_code }}</strong></div>
            @endif
            <div>Status: <strong class="text-success text-uppercase">{{ $proposal->status ?: 'Active' }}</strong></div>
        </div>

        <!-- Section I: Basic Activity Information -->
        <div class="sheet-section-title">I. General Information</div>
        <table class="table design-meta-table mb-4">
            <tbody>
                <tr>
                    <th>Activity Title</th>
                    <td class="fw-bold text-dark fs-6">{{ $proposal->title }}</td>
                </tr>
                <tr>
                    <th>Program / Banner Project</th>
                    <td>
                        {{ $proposal->banner_program ?: ($plan?->program_name ?: 'Institutional Extension Program') }}
                        @if($proposal->academicProgram)
                            <span class="text-muted">({{ $proposal->academicProgram->program_name }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Implementing Unit / College</th>
                    <td>{{ $proposal->unit?->unit_name ?: ($proposal->implementing_agency ?: 'College Extension Unit') }}</td>
                </tr>
                <tr>
                    <th>Partner / Collaborating Agencies</th>
                    <td>{{ $proposal->collaborating_agencies ?: 'Community Stakeholders / LGU' }}</td>
                </tr>
                <tr>
                    <th>Target Beneficiaries</th>
                    <td>
                        <strong>{{ $proposal->beneficiaries ?: ($plan?->beneficiaries ?: 'Community Members') }}</strong>
                        @if($plan && $plan->target_beneficiaries_count)
                            <span class="badge bg-light text-dark border ms-1 font-mono">({{ number_format((int)$plan->target_beneficiaries_count) }} Pax)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Implementation Period / Venue</th>
                    <td>
                        <strong>{{ $proposal->duration_text ?: ($plan?->formatted_conduct_activity ?: 'Scheduled Implementation') }}</strong>
                        @if($proposal->project_location)
                            <div class="text-muted extra-small mt-0.5"><i class="mdi mdi-map-marker me-1"></i>{{ $proposal->project_location }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Project Leader</th>
                    <td>
                        <strong>{{ $proposal->project_leader_name }}</strong>
                        <span class="text-muted">({{ $proposal->project_leader_title ?: 'Proponent / Lead' }})</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Section II: Rationale & Objectives -->
        <div class="sheet-section-title">II. Context, Rationale &amp; Objectives</div>
        <div class="mb-3">
            <h6 class="fw-bold text-dark extra-small text-uppercase mb-1">A. Context &amp; Significance</h6>
            <p class="small text-secondary mb-3" style="line-height: 1.6; text-align: justify;">
                {{ $proposal->introduction ?: ($proposal->relevance_narrative ?: 'This extension activity is undertaken as part of the university mandate to translate technical and academic expertise into meaningful community capacity building.') }}
            </p>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark extra-small text-uppercase mb-1">B. Activity Objectives</h6>
            @if(!empty($proposal->general_objective))
                <div class="small text-dark fw-semibold mb-2">General Objective: {{ $proposal->general_objective }}</div>
            @endif
            @if(!empty($proposal->specific_objectives) && is_array($proposal->specific_objectives))
                <ul class="small text-secondary ps-3 mb-0">
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
            <table class="table table-bordered schedule-table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45px;" class="text-center font-mono">#</th>
                        <th>Milestone / Activity Component</th>
                        <th style="min-width: 170px;" class="text-center font-mono">Target Date / Period</th>
                        <th style="width: 130px;" class="text-center">Verification</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                        <tr>
                            <td class="text-center font-mono fw-bold text-muted">{{ $act['num'] }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $act['title'] }}</div>
                                @if(!empty($act['remarks']))
                                    <div class="text-muted extra-small">{{ $act['remarks'] }}</div>
                                @endif
                            </td>
                            <td class="text-center font-mono extra-small">
                                {{ $act['target_date'] ?: 'To be scheduled' }}
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
                                No scheduled activities defined.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Section IV: Budgetary Allocation -->
        <div class="sheet-section-title">IV. Budgetary Requirements &amp; Allocation</div>
        <table class="table design-meta-table mb-4">
            <tbody>
                <tr>
                    <th>Total Proposed Budget</th>
                    <td class="fw-bold font-mono text-dark fs-6">{{ $proposal->budget_total_display ?: ($plan?->formatted_budget ?: '₱0.00') }}</td>
                </tr>
                <tr>
                    <th>SNSU Institutional Share</th>
                    <td class="font-mono">{{ $proposal->budget_institution_share ?: ($proposal->budget_snsu_share ?: ($plan?->formatted_budget ?: '—')) }}</td>
                </tr>
                <tr>
                    <th>Partner / Beneficiary Counterpart</th>
                    <td class="font-mono">{{ $proposal->budget_partner_share ?: '₱0.00 (In-kind / Venue)' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Section V: Signatories Panel -->
        <div class="sheet-section-title">V. Review &amp; Approval Matrix</div>

        @php
            $sigCollection = isset($signatories) ? collect($signatories) : collect();

            // Tier 1: Up to Concurred
            $prepSig = $sigCollection->firstWhere('tier_role', 'prepared')
                ?: (object)[
                    'role_label' => 'Prepared by:',
                    'signatory_name' => $proposal->project_leader_name,
                    'designation' => $proposal->project_leader_title ?: 'Project Leader',
                ];

            $revSig = $sigCollection->firstWhere('tier_role', 'reviewed')
                ?: (object)[
                    'role_label' => 'Reviewed by:',
                    'signatory_name' => $plan?->reviewed_by_name ?: ($proposal->signatory_reviewed_by ?: 'College Head for Extension'),
                    'designation' => $plan?->reviewed_by_designation ?: 'Head, College Extension Unit',
                ];

            $notedSig = $sigCollection->firstWhere('tier_role', 'noted')
                ?: (object)[
                    'role_label' => 'Noted by:',
                    'signatory_name' => $plan?->noted_by_name ?: ($proposal->signatory_noted_by ?: 'College Dean'),
                    'designation' => $plan?->noted_by_designation ?: 'College Dean',
                ];

            $concSig = $sigCollection->first(fn($s) => str_starts_with($s->tier_role ?? '', 'concurred') || str_contains(strtolower($s->role_label ?? ''), 'concurred'))
                ?: (object)[
                    'role_label' => 'Concurred by:',
                    'signatory_name' => $plan?->concurred_by_2_name ?: ($plan?->concurred_by_name ?: ($proposal->signatory_approved_by ?: 'ALVIE ELMA C. PLAZA, MA')),
                    'designation' => $plan?->concurred_by_2_designation ?: ($plan?->concurred_by_designation ?: 'Director, Extension Services'),
                ];

            // Tier 2: Post-Concurred Signatories
            $recVpeSig = $sigCollection->first(fn($s) => $s->tier_role === 'recommending_approval' || (str_contains(strtolower($s->role_label ?? ''), 'recommending') && str_contains(strtolower(($s->designation ?? '').' '.($s->signatory_name ?? '')), 'sunico')))
                ?: $sigCollection->first(fn($s) => str_contains(strtolower($s->role_label ?? ''), 'recommending'))
                ?: (object)[
                    'role_label' => 'Recommending Approval:',
                    'signatory_name' => 'ROMY JUN A. SUNICO, DTE',
                    'designation' => 'Vice President for RIE',
                ];

            $budgetSig = $sigCollection->first(fn($s) => $s->tier_role === 'certified_allotment' || str_contains(strtolower(($s->role_label ?? '').' '.($s->tier_role ?? '')), 'allotment'))
                ?: (object)[
                    'role_label' => 'Certified as to availability of allotment:',
                    'signatory_name' => 'MARY ANN M. NARCISO, MBA',
                    'designation' => 'Budget Officer III',
                ];

            $acctSig = $sigCollection->first(fn($s) => $s->tier_role === 'certified_funds' || str_contains(strtolower(($s->role_label ?? '').' '.($s->tier_role ?? '')), 'funds'))
                ?: (object)[
                    'role_label' => 'Certified as to availability of funds:',
                    'signatory_name' => 'IVY JEAN J. TURNO',
                    'designation' => 'Accountant III',
                ];

            $caoSig = $sigCollection->first(fn($s) => $s->tier_role === 'cao_designate' || str_contains(strtolower(($s->designation ?? '').' '.($s->signatory_name ?? '')), 'ecleo') || str_contains(strtolower($s->designation ?? ''), 'cao'))
                ?: (object)[
                    'role_label' => '',
                    'signatory_name' => 'FELICITY MAE M. ECLEO',
                    'designation' => 'CAO-Designate',
                ];

            $presSig = $sigCollection->first(fn($s) => $s->tier_role === 'approved' || str_contains(strtolower($s->role_label ?? ''), 'approved') || str_contains(strtolower($s->designation ?? ''), 'president'))
                ?: (object)[
                    'role_label' => 'Approved by:',
                    'signatory_name' => 'ROWENA A. PLANDO, Ph.D.',
                    'designation' => 'University President III',
                ];
        @endphp

        <!-- 1. TIER 1 SIGNATORIES (4-COLUMN ROW: PREPARED, REVIEWED, NOTED, CONCURRED) -->
        <div class="row g-4 pt-2 mb-4">
            <!-- Prepared by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $prepSig->role_label ?: 'Prepared by:' }}</div>
                <div class="border-top pt-2">
                    <div class="fw-bold text-dark small text-uppercase">{{ $prepSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $prepSig->designation ?: 'Project Leader' }}</div>
                </div>
            </div>

            <!-- Reviewed by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $revSig->role_label ?: 'Reviewed by:' }}</div>
                <div class="border-top pt-2">
                    <div class="fw-bold text-dark small text-uppercase">{{ $revSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $revSig->designation ?: 'Head, College Extension Unit' }}</div>
                </div>
            </div>

            <!-- Noted by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $notedSig->role_label ?: 'Noted by:' }}</div>
                <div class="border-top pt-2">
                    <div class="fw-bold text-dark small text-uppercase">{{ $notedSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $notedSig->designation ?: 'College Dean' }}</div>
                </div>
            </div>

            <!-- Concurred by -->
            <div class="col-6 col-md-3">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $concSig->role_label ?: 'Concurred by:' }}</div>
                <div class="border-top pt-2">
                    <div class="fw-bold text-dark small text-uppercase">{{ $concSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $concSig->designation ?: 'Director, Extension Services' }}</div>
                </div>
            </div>
        </div>

        <!-- 2. POST-CONCURRED TIER: RECOMMENDING APPROVAL (CENTERED) -->
        <div class="row justify-content-center mb-4 pt-2">
            <div class="col-12 col-md-6 text-center">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $recVpeSig->role_label ?: 'Recommending Approval:' }}</div>
                <div class="border-top pt-2 mx-auto" style="max-width: 320px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $recVpeSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $recVpeSig->designation ?: 'Vice President for RIE' }}</div>
                </div>
            </div>
        </div>

        <!-- 3. POST-CONCURRED TIER: 2-COLUMN FINANCIAL CERTIFICATION (ALLOTMENT & FUNDS) -->
        <div class="row g-4 mb-4 pt-2">
            <!-- Left: Certified as to availability of allotment -->
            <div class="col-6 text-start">
                <div class="extra-small text-dark fw-bold fst-italic mb-3">{{ $budgetSig->role_label ?: 'Certified as to availability of allotment:' }}</div>
                <div class="border-top pt-2 me-auto" style="max-width: 300px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $budgetSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $budgetSig->designation ?: 'Budget Officer III' }}</div>
                </div>
            </div>

            <!-- Right: Certified as to availability of funds -->
            <div class="col-6 text-end">
                <div class="extra-small text-dark fw-bold fst-italic mb-3">{{ $acctSig->role_label ?: 'Certified as to availability of funds:' }}</div>
                <div class="border-top pt-2 ms-auto" style="max-width: 300px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $acctSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $acctSig->designation ?: 'Accountant III' }}</div>
                </div>
            </div>
        </div>

        <!-- 4. POST-CONCURRED TIER: CAO-DESIGNATE (CENTERED) -->
        <div class="row justify-content-center mb-4 pt-2">
            <div class="col-12 col-md-6 text-center">
                @if(!empty($caoSig->role_label))
                    <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $caoSig->role_label }}</div>
                @else
                    <div class="mb-3" style="height: 18px;"></div>
                @endif
                <div class="border-top pt-2 mx-auto" style="max-width: 320px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $caoSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $caoSig->designation ?: 'CAO-Designate' }}</div>
                </div>
            </div>
        </div>

        <!-- 5. POST-CONCURRED TIER: APPROVED BY (UNIVERSITY PRESIDENT - CENTERED) -->
        <div class="row justify-content-center pt-2">
            <div class="col-12 col-md-6 text-center">
                <div class="extra-small text-muted text-uppercase fw-bold mb-3">{{ $presSig->role_label ?: 'Approved by:' }}</div>
                <div class="border-top pt-2 mx-auto" style="max-width: 320px;">
                    <div class="fw-bold text-dark small text-uppercase">{{ $presSig->signatory_name }}</div>
                    <div class="text-muted extra-small">{{ $presSig->designation ?: 'University President III' }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

