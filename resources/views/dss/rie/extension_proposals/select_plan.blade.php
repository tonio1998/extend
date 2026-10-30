@extends('layouts.dss.app')

@section('title', 'Select Approved Extension Plan — RIE System')

@section('content')
<div class="container-fluid p-0">
    <div class="canva-hero-header p-4 mb-4 shadow-2xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="canva-theme-badge">
                        <i class="mdi mdi-lightbulb-on-outline"></i> RIE EXTENSION SERVICES
                    </span>
                    <span class="text-secondary extra-small">Official Step 1 of Proposal Initiation</span>
                </div>
                <h3 class="fw-extrabold text-dark mb-1">Select Approved Extension Plan</h3>
                <p class="text-secondary small mb-0">
                    By institutional policy, an Extension Proposal can only be generated from an officially approved Extension Plan.
                </p>
            </div>
            <div>
                <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Proposals
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($approvedPlans as $plan)
            <div class="col-md-6 col-lg-4">
                <div class="canva-card p-4 border shadow-2xs h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                <i class="mdi mdi-check-decagram me-1"></i> APPROVED PLAN
                            </span>
                            <span class="font-mono extra-small text-muted">{{ $plan->plan_code }}</span>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">{{ $plan->extension_activity }}</h5>
                        <p class="text-muted extra-small mb-3">
                            <i class="mdi mdi-domain me-1"></i> {{ $plan->program_name }}
                        </p>
                        <div class="bg-light p-3 rounded-2 extra-small mb-3">
                            <div class="text-muted mb-1">Beneficiaries: <strong class="text-dark">{{ Str::limit($plan->beneficiaries, 60) }}</strong></div>
                            <div class="text-muted">Leader: <strong class="text-dark">{{ $plan->prepared_by_name }}</strong></div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('dss.rie.proposals.create', ['plan_id' => $plan->getRouteKey()]) }}" class="btn btn-sm btn-dss-primary rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-1">
                            <span>Proceed with Proposal</span>
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="canva-card p-5 text-center border shadow-2xs">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-inline-flex mb-3">
                        <i class="mdi mdi-alert-circle-outline fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Approved Extension Plans Available</h5>
                    <p class="text-muted extra-small mb-4" style="max-width: 500px; margin: 0 auto;">
                        There are currently no approved Extension Plans ready for proposal creation. Please ensure an Extension Plan is first submitted and approved by the Dean.
                    </p>
                    <a href="{{ route('dss.rie.plans.index') }}" class="btn btn-sm btn-dss-primary rounded-pill px-4 fw-bold">
                        <i class="mdi mdi-clipboard-text-outline me-1"></i> View Extension Plans
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
