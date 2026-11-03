@extends('layouts.dss.app')

@section('title', 'Activity Designs Registry — SNSU RIE Suite')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb extra-small font-mono m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.plans.index') }}" class="text-decoration-none text-muted">RIE Extension</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold">Activity Designs</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white font-mono extra-small">MODULE 3</span>
                <h4 class="fw-bold text-dark m-0">Activity Designs Registry</h4>
                <span class="text-muted small">&bull; Implementation &amp; Financial Certifications</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="mdi mdi-arrow-left me-1"></i> Proposals Registry
            </a>
            <a href="{{ route('dss.rie.activity_designs.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-2xs">
                <i class="mdi mdi-plus-circle me-1"></i> New Activity Design
            </a>
        </div>
    </div>

    <!-- Metric Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border rounded-3 shadow-2xs h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold">Total Designs</div>
                        <div class="fs-4 fw-bold text-dark font-mono mt-1">{{ number_format($totalCount) }}</div>
                    </div>
                    <div class="p-2.5 rounded-3 bg-primary-subtle text-primary">
                        <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border rounded-3 shadow-2xs h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold">Approved</div>
                        <div class="fs-4 fw-bold text-success font-mono mt-1">{{ number_format($approvedCount) }}</div>
                    </div>
                    <div class="p-2.5 rounded-3 bg-success-subtle text-success">
                        <i class="mdi mdi-check-decagram-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border rounded-3 shadow-2xs h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold">Pending Review</div>
                        <div class="fs-4 fw-bold text-warning font-mono mt-1">{{ number_format($underReviewCount) }}</div>
                    </div>
                    <div class="p-2.5 rounded-3 bg-warning-subtle text-warning">
                        <i class="mdi mdi-clock-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border rounded-3 shadow-2xs h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold">Drafts</div>
                        <div class="fs-4 fw-bold text-secondary font-mono mt-1">{{ number_format($draftCount) }}</div>
                    </div>
                    <div class="p-2.5 rounded-3 bg-secondary-subtle text-secondary">
                        <i class="mdi mdi-pencil-box-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border rounded-3 shadow-2xs mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('dss.rie.activity_designs.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by design code, title, or project leader..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="unit_id" class="form-select form-select-sm">
                        <option value="">All Academic Units / Colleges</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ $unitId == $u->id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 flex-grow-1">Filter</button>
                    @if($search || $status || $unitId)
                        <a href="{{ route('dss.rie.activity_designs.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Reset Filters">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Designs Table (Rule #8: Actions as 1st Column) -->
    <div class="card border rounded-3 shadow-2xs overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light extra-small text-uppercase font-mono">
                    <tr>
                        <th style="width: 140px;" class="ps-3 text-center">Actions</th>
                        <th>Activity Design Code</th>
                        <th>Activity Title &amp; Unit</th>
                        <th>Project Leader</th>
                        <th>Budget &amp; Schedule</th>
                        <th class="pe-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($designs as $d)
                        <tr>
                            <!-- 1st Column: Actions (Rule #8) -->
                            <td class="ps-3 text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dss.rie.activity_designs.show', $d->getRouteKey()) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 fw-bold" title="View Official Activity Design Sheet">
                                        <i class="mdi mdi-eye-outline me-0.5"></i> View
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end extra-small shadow-sm">
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.activity_designs.show', $d->getRouteKey()) }}">
                                                <i class="mdi mdi-file-document-outline me-1.5 text-primary"></i> Open Activity Sheet
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.activity_designs.signatories.edit', $d->getRouteKey()) }}">
                                                <i class="mdi mdi-account-cog-outline me-1.5 text-info"></i> Manage Signatories
                                            </a>
                                        </li>
                                        @if($d->extensionProposal)
                                            <li>
                                                <a class="dropdown-item py-1.5" href="{{ route('dss.rie.proposals.show', $d->extensionProposal->getRouteKey()) }}">
                                                    <i class="mdi mdi-file-table-outline me-1.5 text-secondary"></i> View Proposal
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.activity_designs.pdf', $d->getRouteKey()) }}" target="_blank">
                                                <i class="mdi mdi-file-pdf-box me-1.5 text-danger"></i> FPDF Document
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item py-1.5" href="{{ route('dss.rie.activity_designs.show', $d->getRouteKey()) }}" onclick="window.print(); return false;">
                                                <i class="mdi mdi-printer me-1.5 text-dark"></i> Print Document
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <!-- 2nd Column: Design Code -->
                            <td>
                                <div class="fw-bold font-mono text-dark">{{ $d->design_code }}</div>
                                @if($d->extensionProposal)
                                    <div class="extra-small text-muted font-mono">
                                        Prop: <a href="{{ route('dss.rie.proposals.show', $d->extensionProposal->getRouteKey()) }}" class="text-decoration-none text-muted">{{ $d->extensionProposal->proposal_code }}</a>
                                    </div>
                                @endif
                            </td>

                            <!-- 3rd Column: Title & Unit -->
                            <td>
                                <a href="{{ route('dss.rie.activity_designs.show', $d->getRouteKey()) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $d->title }}
                                </a>
                                <div class="extra-small text-muted mt-0.5">
                                    <i class="mdi mdi-domain me-1"></i>{{ $d->unit?->unit_name ?: 'Higher Education Institution' }}
                                </div>
                            </td>

                            <!-- 4th Column: Leader -->
                            <td>
                                <div class="fw-medium text-dark">{{ $d->project_leader_name ?: 'Project Leader' }}</div>
                                <div class="extra-small text-muted">{{ $d->project_leader_title ?: 'Project Leader' }}</div>
                            </td>

                            <!-- 5th Column: Budget & Schedule -->
                            <td>
                                <div class="fw-bold font-mono text-dark">{{ $d->formatted_budget_total }}</div>
                                <div class="extra-small text-muted">
                                    <i class="mdi mdi-calendar-range me-0.5"></i> {{ $d->duration_text ?: 'Scheduled Period' }}
                                </div>
                            </td>

                            <!-- 6th Column: Status -->
                            <td class="pe-3 text-center">
                                @if($d->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">Approved</span>
                                @elseif($d->status === 'under_review' || $d->status === 'submitted')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono extra-small">Under Review</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border font-mono extra-small">Draft</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="p-3">
                                    <i class="mdi mdi-file-document-outline fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                    <h6>No Activity Designs Found</h6>
                                    <p class="extra-small text-muted mb-3">Activity Designs are created following Extension Proposals to authorize event budgets and institutional endorsements.</p>
                                    <a href="{{ route('dss.rie.activity_designs.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="mdi mdi-plus-circle me-1"></i> Create First Activity Design
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($designs->hasPages())
            <div class="card-footer bg-white border-top py-2 px-3">
                {{ $designs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

