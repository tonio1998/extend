@extends('layouts.dss.app')

@section('title', 'For Signature — Electronic Endorsement Inbox')

@section('content')
<div class="container-fluid px-4 py-3">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0 extra-small">
            <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dss.rie.proposals.index') }}" class="text-decoration-none text-muted">RIE Extensions</a></li>
            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">For Signature &amp; Endorsement</li>
        </ol>
    </nav>

    <!-- Unified Canva Hero Header -->
    <div class="canva-hero-header p-4 mb-4 shadow-2xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="canva-theme-badge">
                        <i class="mdi mdi-draw-pen me-1"></i> ELECTRONIC ENDORSEMENTS &bull; SIGNING ENGINE
                    </span>
                    <span class="text-secondary extra-small">Enterprise Institutional Governance</span>
                </div>
                <h2 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2" style="letter-spacing: -0.02em;">
                    For Signature &amp; Endorsement Inbox
                    @if($pendingCount > 0)
                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle fs-6 px-3 py-1 font-mono">
                            {{ $pendingCount }} Action Required
                        </span>
                    @else
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle extra-small px-2 py-1">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Inbox Up to Date
                        </span>
                    @endif
                </h2>
                <p class="text-secondary small mb-0" style="max-width: 780px;">
                    Official university issuances, policies, and action designs routed for your electronic verification, endorsement notes, and digital signing under institutional matrix rules.
                </p>
            </div>

            <!-- User Signatory Identity Card -->
            <div class="sig-hero-chip px-3 py-2 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary fw-bold" style="width: 44px; height: 44px; min-width: 44px;">
                    @if($user->avatar ?? false)
                        <img src="{{ asset($user->avatar) }}" alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover">
                    @else
                        <i class="mdi mdi-account-tie fs-4"></i>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="fw-bold text-dark text-truncate" style="max-width: 200px; font-size: 13.5px;">
                            {{ $user->employee?->full_name ?: $user->name }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2 py-1" title="Verified University Signatory">
                            <i class="mdi mdi-shield-check"></i> Verified
                        </span>
                    </div>
                    <div class="text-muted extra-small text-truncate" style="max-width: 210px;">
                        {{ $user->employee?->designation ?: ($user->designation ?: 'Authorized Signatory') }}
                    </div>
                </div>
                @if(Route::has('dss.signatories.index'))
                    <a href="{{ route('dss.signatories.index') }}" class="btn btn-sm btn-light border p-1 text-muted ms-1" title="Configure Signatory Matrix Rules">
                        <i class="mdi mdi-cog-outline fs-6"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Executive Metrics Bento Strip -->
    <div class="row g-3 mb-4">
        <!-- 1. Awaiting Signature -->
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('dss.signatures.index', ['tab' => 'pending']) }}"
               class="sig-bento-card h-100 text-dark {{ $activeTab === 'pending' ? 'active-card' : '' }}"
               style="border-top: 3.5px solid #ef4444; {{ $activeTab === 'pending' ? 'background: #fffcfc; border-color: #ef4444;' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Awaiting Signature</span>
                        <div class="fs-2 fw-extrabold font-mono text-dark mt-1 lh-1">{{ $pendingCount }}</div>
                    </div>
                    <div class="sig-icon-bubble bg-danger-subtle text-danger">
                        <i class="mdi mdi-fountain-pen-tip"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                    <span class="extra-small {{ $pendingCount > 0 ? 'text-danger fw-bold' : 'text-muted' }} d-flex align-items-center gap-1">
                        @if($pendingCount > 0)
                            <span class="pulse-dot me-1"></span> Action required
                        @else
                            <i class="mdi mdi-check-all text-success"></i> All caught up
                        @endif
                    </span>
                    @if($activeTab === 'pending')
                        <span class="badge bg-danger text-white extra-small px-2 py-1 font-mono" style="font-size: 10px;">Active</span>
                    @else
                        <i class="mdi mdi-arrow-right extra-small text-muted"></i>
                    @endif
                </div>
            </a>
        </div>

        <!-- 2. Signed History -->
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('dss.signatures.index', ['tab' => 'signed']) }}"
               class="sig-bento-card h-100 text-dark {{ $activeTab === 'signed' ? 'active-card' : '' }}"
               style="border-top: 3.5px solid #10b981; {{ $activeTab === 'signed' ? 'background: #fcfdfc; border-color: #10b981;' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Signed History</span>
                        <div class="fs-2 fw-extrabold font-mono text-dark mt-1 lh-1">{{ $signedCount }}</div>
                    </div>
                    <div class="sig-icon-bubble bg-success-subtle text-success">
                        <i class="mdi mdi-check-decagram-outline"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                    <span class="extra-small text-success fw-semibold d-flex align-items-center gap-1">
                        <i class="mdi mdi-shield-lock-outline"></i> Digitally sealed
                    </span>
                    @if($activeTab === 'signed')
                        <span class="badge bg-success text-white extra-small px-2 py-1 font-mono" style="font-size: 10px;">Active</span>
                    @else
                        <i class="mdi mdi-arrow-right extra-small text-muted"></i>
                    @endif
                </div>
            </a>
        </div>

        <!-- 3. Lifetime Queue -->
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('dss.signatures.index', ['tab' => 'all']) }}"
               class="sig-bento-card h-100 text-dark {{ $activeTab === 'all' ? 'active-card' : '' }}"
               style="border-top: 3.5px solid #6366f1; {{ $activeTab === 'all' ? 'background: #fafbff; border-color: #6366f1;' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Lifetime Inbox</span>
                        <div class="fs-2 fw-extrabold font-mono text-dark mt-1 lh-1">{{ $pendingCount + $signedCount }}</div>
                    </div>
                    <div class="sig-icon-bubble text-primary" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                        <i class="mdi mdi-tray-full"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                    <span class="extra-small text-secondary fw-semibold d-flex align-items-center gap-1">
                        <i class="mdi mdi-file-document-multiple-outline"></i> Assigned total
                    </span>
                    @if($activeTab === 'all')
                        <span class="badge bg-dark text-white extra-small px-2 py-1 font-mono" style="font-size: 10px;">Active</span>
                    @else
                        <i class="mdi mdi-arrow-right extra-small text-muted"></i>
                    @endif
                </div>
            </a>
        </div>

        <!-- 4. Signatory Matrix Status -->
        <div class="col-sm-6 col-xl-3">
            <div class="sig-bento-card h-100 text-dark" style="border-top: 3.5px solid #06b6d4;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Signatory Matrix</span>
                        <div class="fs-5 fw-extrabold font-mono text-dark mt-1 lh-1">Active &bull; Enforced</div>
                    </div>
                    <div class="sig-icon-bubble bg-info-subtle text-info">
                        <i class="mdi mdi-cog-sync-outline"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                    <span class="extra-small text-muted fw-medium">
                        Multi-tier routing rulebase
                    </span>
                    @if(Route::has('dss.signatories.index'))
                        <a href="{{ route('dss.signatories.index') }}" class="extra-small text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                            <span>Rules</span> <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Unified Navigation Tabs & Filter Bar -->
    <div class="sig-filter-toolbar mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Navigation Tabs -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Tab: Pending -->
                <a href="{{ route('dss.signatures.index', array_merge(request()->query(), ['tab' => 'pending'])) }}"
                   class="sig-nav-pill {{ $activeTab === 'pending' ? 'active-pending' : '' }}">
                    <i class="mdi mdi-fountain-pen-tip"></i>
                    <span>Pending Signature</span>
                    <span class="badge rounded-pill {{ $activeTab === 'pending' ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' }} font-mono ms-1 px-2 py-1" style="font-size: 11px;">
                        {{ $pendingCount }}
                    </span>
                </a>

                <!-- Tab: Signed -->
                <a href="{{ route('dss.signatures.index', array_merge(request()->query(), ['tab' => 'signed'])) }}"
                   class="sig-nav-pill {{ $activeTab === 'signed' ? 'active-signed' : '' }}">
                    <i class="mdi mdi-check-all"></i>
                    <span>Signed History</span>
                    <span class="badge rounded-pill {{ $activeTab === 'signed' ? 'bg-white text-success' : 'bg-success-subtle text-success' }} font-mono ms-1 px-2 py-1" style="font-size: 11px;">
                        {{ $signedCount }}
                    </span>
                </a>

                <!-- Tab: All -->
                <a href="{{ route('dss.signatures.index', array_merge(request()->query(), ['tab' => 'all'])) }}"
                   class="sig-nav-pill {{ $activeTab === 'all' ? 'active' : '' }}">
                    <i class="mdi mdi-format-list-bulleted"></i>
                    <span>All Records</span>
                    <span class="badge rounded-pill {{ $activeTab === 'all' ? 'bg-white text-dark' : 'bg-light text-secondary border' }} font-mono ms-1 px-2 py-1" style="font-size: 11px;">
                        {{ $pendingCount + $signedCount }}
                    </span>
                </a>
            </div>

            <!-- Filter Controls Form -->
            <form action="{{ route('dss.signatures.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <!-- Campus Filter -->
                <div class="input-group input-group-sm" style="width: 175px;">
                    <span class="input-group-text bg-light border-end-0 text-muted px-2"><i class="mdi mdi-map-marker-outline"></i></span>
                    <select name="campus_id" class="form-select form-select-sm border-start-0" onchange="this.form.submit()">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $camp)
                            <option value="{{ $camp->id }}" {{ (string)$campusId === (string)$camp->id ? 'selected' : '' }}>
                                {{ $camp->campus_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Document Type Filter -->
                <div class="input-group input-group-sm" style="width: 215px;">
                    <span class="input-group-text bg-light border-end-0 text-muted px-2"><i class="mdi mdi-file-document-outline"></i></span>
                    <select name="doc_filter" class="form-select form-select-sm border-start-0" onchange="this.form.submit()">
                        <option value="">All Document Types</option>
                        <option value="ExtensionProposal" {{ $docFilter === 'ExtensionProposal' ? 'selected' : '' }}>Extension Proposals</option>
                        <option value="ExtensionPlan" {{ $docFilter === 'ExtensionPlan' ? 'selected' : '' }}>Extension Plans</option>
                        <option value="UniversityMemorandum" {{ $docFilter === 'UniversityMemorandum' ? 'selected' : '' }}>Memorandums</option>
                        <option value="InstitutionalPolicy" {{ $docFilter === 'InstitutionalPolicy' ? 'selected' : '' }}>Institutional Policies</option>
                    </select>
                </div>

                <!-- Search Input with Clear Button -->
                <div class="input-group input-group-sm" style="width: 260px;">
                    <span class="input-group-text bg-white border-end-0 text-muted px-2"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 border-end-0" placeholder="Search title, code, DTS ID..." value="{{ $search }}">
                    <button class="btn btn-light border border-start-0 text-muted" type="submit">
                        <i class="mdi mdi-arrow-right"></i>
                    </button>
                </div>

                <!-- Reset Filters Button -->
                @if($search || $campusId || $docFilter)
                    <a href="{{ route('dss.signatures.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light border text-danger px-2" title="Reset all filters">
                        <i class="mdi mdi-filter-remove-outline me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Documents Bento Container -->
    <div class="canva-card p-0 mb-4 shadow-sm overflow-hidden">
        @if($items->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-5 px-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light mb-3 shadow-2xs" style="width: 80px; height: 80px;">
                    @if($activeTab === 'pending')
                        <i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size: 40px;"></i>
                    @else
                        <i class="mdi mdi-folder-open-outline text-muted" style="font-size: 40px;"></i>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">
                    @if($activeTab === 'pending')
                        All Caught Up! Zero Pending Endorsements
                    @elseif($search || $campusId || $docFilter)
                        No Documents Match Your Filter
                    @else
                        No Signed Documents in Record
                    @endif
                </h4>
                <p class="text-muted small mb-4" style="max-width: 480px; margin-left: auto; margin-right: auto;">
                    @if($activeTab === 'pending')
                        Great job! There are currently no university documents awaiting your electronic verification, notation, or digital signature.
                    @elseif($search || $campusId || $docFilter)
                        We couldn't find any documents matching your current search query or active filter settings. Try clearing your filters or using different keywords.
                    @else
                        You haven't signed or sealed any documents yet. When you review and endorse documents, your cryptographic signature trail will appear here.
                    @endif
                </p>

                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    @if($activeTab === 'pending')
                        <a href="{{ route('dss.signatures.index', ['tab' => 'signed']) }}" class="btn btn-sm btn-light border fw-semibold px-3 py-2">
                            <i class="mdi mdi-check-all me-1 text-success"></i> View Signed History
                        </a>
                        <a href="{{ route('document.track') }}" class="btn btn-sm btn-dss-primary fw-semibold px-3 py-2">
                            <i class="mdi mdi-qrcode-scan me-1"></i> Open DTS Tracker
                        </a>
                    @elseif($search || $campusId || $docFilter)
                        <a href="{{ route('dss.signatures.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light border fw-semibold px-3 py-2">
                            <i class="mdi mdi-filter-remove-outline me-1"></i> Clear All Filters
                        </a>
                    @endif
                </div>
            </div>
        @else
            <!-- Documents Table -->
            <div class="table-responsive">
                <table class="table sig-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 170px;">Doc Category</th>
                            <th style="min-width: 280px;">Document Details &amp; Code</th>
                            <th style="width: 190px;">Your Signatory Role</th>
                            <th style="width: 170px;">DTS Tracking</th>
                            <th style="width: 190px;">Endorsement Status</th>
                            <th class="pe-4 text-end" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $isPending = $item->status === 'pending';
                                $docUrl = $item->document_url;
                                $docTitle = $item->document_title;
                                $docCode = $item->document_code;
                                $docBadge = $item->document_type_badge;

                                // Card/Badge Theme Mapping
                                $typeConfig = match($docBadge) {
                                    'Extension Proposal' => [
                                        'icon' => 'mdi-file-document-edit-outline',
                                        'pill_class' => 'bg-info-subtle text-info border border-info-subtle',
                                        'avatar_bg' => 'rgba(14, 165, 233, 0.12)',
                                        'avatar_color' => '#0ea5e9',
                                    ],
                                    'Extension Plan' => [
                                        'icon' => 'mdi-lightbulb-on-outline',
                                        'pill_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'avatar_bg' => 'rgba(2, 132, 199, 0.12)',
                                        'avatar_color' => '#0284c7',
                                    ],
                                    'University Memorandum' => [
                                        'icon' => 'mdi-file-document-edit-outline',
                                        'pill_class' => 'bg-success-subtle text-success border border-success-subtle',
                                        'avatar_bg' => 'rgba(5, 150, 105, 0.12)',
                                        'avatar_color' => '#059669',
                                    ],
                                    'Institutional Policy' => [
                                        'icon' => 'mdi-shield-check-outline',
                                        'pill_class' => 'bg-purple-subtle text-purple border border-purple-subtle',
                                        'avatar_bg' => 'rgba(124, 58, 237, 0.12)',
                                        'avatar_color' => '#7c3aed',
                                    ],
                                    default => [
                                        'icon' => 'mdi-file-document-outline',
                                        'pill_class' => 'bg-light text-secondary border',
                                        'avatar_bg' => '#f1f5f9',
                                        'avatar_color' => '#475569',
                                    ],
                                };

                                // Tier Role Styling
                                $tierBadge = match(strtolower($item->tier_role ?: '')) {
                                    'prepared' => 'bg-info-subtle text-info border border-info-subtle',
                                    'reviewed' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    'endorsed' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                    'approved' => 'bg-success-subtle text-success border border-success-subtle',
                                    default => 'bg-light text-muted border',
                                };

                                // Tracking Number resolution
                                $trackingNo = $item->dtsRecord?->DocumentID ?? ($item->document?->tracking_number ?? null);
                                $qrCode = $item->dtsRecord?->QRCODE ?? null;
                            @endphp
                            <tr>
                                <!-- 1. Document Category & Campus -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-inline-flex align-items-center justify-content-center"
                                             style="width: 38px; height: 38px; min-width: 38px; background: {{ $typeConfig['avatar_bg'] }}; color: {{ $typeConfig['avatar_color'] }};">
                                            <i class="mdi {{ $typeConfig['icon'] }} fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="badge rounded-pill extra-small fw-bold px-2 py-1 {{ $typeConfig['pill_class'] }}">
                                                {{ $docBadge }}
                                            </span>
                                            @if($item->campus)
                                                <div class="extra-small text-muted text-truncate mt-1" style="max-width: 180px;" title="{{ $item->campus->campus_name }}">
                                                    <i class="mdi mdi-map-marker-outline text-secondary"></i> {{ $item->campus->campus_name }}
                                                </div>
                                            @else
                                                <div class="extra-small text-muted mt-1 fst-italic">
                                                    University-wide
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Document Details & Code -->
                                <td>
                                    <div class="fw-bold text-dark fs-6 mb-1">
                                        @if($docUrl)
                                            <a href="{{ $docUrl }}" class="text-dark text-decoration-none hover-primary d-inline-block" title="Open institutional document viewer">
                                                {{ $docTitle }}
                                            </a>
                                        @else
                                            <span>{{ $docTitle }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-2 extra-small text-muted">
                                        <span class="badge bg-light text-secondary border font-mono px-2 py-1">
                                            {{ $docCode }}
                                        </span>
                                        <span class="text-secondary">&bull;</span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-calendar-outline"></i> {{ $item->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 3. Signatory Role & Governance Tier -->
                                <td>
                                    <div class="fw-bold text-dark extra-small mb-1">
                                        {{ $item->role_label }}
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span class="badge rounded-pill extra-small font-mono text-uppercase px-2 py-1 {{ $tierBadge }}">
                                            Tier: {{ $item->tier_role ?: 'Signatory' }}
                                        </span>
                                        @if($item->is_mandatory)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small px-2 py-1 font-mono" title="Mandatory signatory before document finalization">
                                                Mandatory
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 4. DTS Tracking Code -->
                                <td>
                                    @if($trackingNo)
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="font-mono text-dark fw-bold extra-small" title="{{ $trackingNo }}">
                                                {{ $trackingNo }}
                                            </span>
                                            <button type="button" class="copy-chip-btn" onclick="copySignatoryText('{{ $trackingNo }}', 'Tracking Code')" title="Copy tracking ID">
                                                <i class="mdi mdi-content-copy" style="font-size: 13px;"></i>
                                            </button>
                                        </div>
                                        @if($qrCode)
                                            <div class="extra-small text-muted font-mono d-flex align-items-center gap-1">
                                                <i class="mdi mdi-qrcode"></i> QR: {{ $qrCode }}
                                            </div>
                                        @endif
                                        <div class="mt-1">
                                            <a href="{{ route('document.track', ['tracking_number' => $trackingNo]) }}" class="extra-small text-primary text-decoration-none d-inline-flex align-items-center gap-1" target="_blank" title="Track document routing in DTS">
                                                <span>Track in DTS</span> <i class="mdi mdi-open-in-new" style="font-size: 10px;"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border extra-small">Direct Internal</span>
                                    @endif
                                </td>

                                <!-- 5. Status & Cryptographic Stamp -->
                                <td>
                                    @if($isPending)
                                        <div class="d-inline-flex align-items-center gap-1 badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 extra-small fw-semibold">
                                            <span class="pulse-dot"></span>
                                            <span>Awaiting Signature</span>
                                        </div>
                                        <div class="extra-small text-muted mt-1">
                                            Action required
                                        </div>
                                    @else
                                        <div class="d-inline-flex align-items-center gap-1 badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 extra-small fw-semibold">
                                            <i class="mdi mdi-check-circle"></i>
                                            <span>Signed &amp; Sealed</span>
                                        </div>
                                        @if($item->signed_at)
                                            <div class="extra-small text-muted font-mono mt-1" style="font-size: 11px;">
                                                <i class="mdi mdi-clock-check-outline"></i> {{ $item->signed_at->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                        @if($item->signature_hash)
                                            <div class="mt-1">
                                                <span class="hash-badge" onclick="copySignatoryText('{{ $item->signature_hash }}', 'Signature Hash')" title="Click to copy SHA-256 digital signature seal: {{ $item->signature_hash }}">
                                                    <i class="mdi mdi-shield-key-outline text-primary"></i>
                                                    <span class="text-truncate">{{ Str::limit($item->signature_hash, 16) }}</span>
                                                    <i class="mdi mdi-content-copy text-muted ms-auto"></i>
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <!-- 6. Actions -->
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($docUrl)
                                            @if($isPending)
                                                <a href="{{ $docUrl }}" class="btn btn-sm btn-danger px-3 py-1 shadow-sm fw-bold d-inline-flex align-items-center gap-1 rounded-pill" title="Review document in viewer & affix signature">
                                                    <i class="mdi mdi-draw fs-6"></i>
                                                    <span>Review &amp; Sign</span>
                                                </a>
                                            @else
                                                <a href="{{ $docUrl }}" class="btn btn-sm btn-light border text-dark px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 rounded-pill" title="Open document in institutional viewer">
                                                    <i class="mdi mdi-eye-outline text-primary fs-6"></i>
                                                    <span>View Document</span>
                                                </a>
                                            @endif

                                            <!-- Direct Comments Drawer shortcut -->
                                            <a href="{{ $docUrl }}#comments" class="btn btn-sm btn-light border p-2 text-muted rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View revision comments &amp; feedback">
                                                <i class="mdi mdi-comment-text-multiple-outline text-secondary fs-6"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-light border text-muted px-2 py-1 rounded-pill" disabled>
                                                Unavailable
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($items->hasPages())
                <div class="px-4 py-3 border-top bg-light d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="extra-small text-muted">
                        Showing <span class="fw-bold font-mono">{{ $items->firstItem() }}</span> to <span class="fw-bold font-mono">{{ $items->lastItem() }}</span> of <span class="fw-bold font-mono">{{ $items->total() }}</span> documents
                    </div>
                    <div>
                        {{ $items->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Copy text helper with toast alert feedback
     */
    function copySignatoryText(text, label) {
        if (!text) return;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyFeedback(label);
            }).catch(() => {
                fallbackCopy(text, label);
            });
        } else {
            fallbackCopy(text, label);
        }
    }

    function fallbackCopy(text, label) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        showCopyFeedback(label);
    }

    function showCopyFeedback(label) {
        if (window.dssToast && typeof window.dssToast.show === 'function') {
            window.dssToast.show({
                title: 'Copied to Clipboard',
                message: `${label || 'Text'} has been copied to your clipboard.`,
                icon: 'success'
            });
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `${label || 'Text'} copied!`,
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
</script>
@endpush
