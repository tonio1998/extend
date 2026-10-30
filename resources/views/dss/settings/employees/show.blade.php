@extends('layouts.dss.app')

@section('title', $employee->full_name . ' (' . $employee->employee_no . ') — SNSU')

@section('styles')
<style>
    .crm-hero {
        background: radial-gradient(circle at 95% 10%, rgba(37, 99, 235, 0.22) 0%, transparent 45%),
                    radial-gradient(100% 175% at 0% 0%, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 30px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.35);
        position: relative;
        overflow: visible;
        z-index: 10;
    }
    .crm-hero .dropdown-menu {
        z-index: 1060 !important;
        box-shadow: 0 14px 35px -5px rgba(0, 0, 0, 0.35) !important;
        border: 1px solid #cbd5e1;
    }
    .crm-avatar {
        width: 80px;
        height: 80px;
        min-width: 80px;
        font-size: 28px;
        font-weight: 800;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.15);
        position: relative;
    }
    .status-pulse-dot {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid #0f172a;
    }
    .crm-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .crm-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .crm-stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3.5px;
    }
    .crm-stat-box.accent-blue::before { background: #2563eb; }
    .crm-stat-box.accent-green::before { background: #10b981; }
    .crm-stat-box.accent-amber::before { background: #f59e0b; }
    .crm-stat-box.accent-purple::before { background: #8b5cf6; }

    .crm-nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        gap: 8px;
        margin-bottom: 24px;
    }
    .crm-nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 13.5px;
        padding: 10px 18px;
        border-radius: 8px;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .crm-nav-tabs .nav-link:hover {
        color: #0f172a;
        background: #f1f5f9;
    }
    .crm-nav-tabs .nav-link.active {
        color: #0f172a;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }
    .crm-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
    }
    .crm-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 13px;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #64748b;
        font-weight: 500;
    }
    .info-val {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }
    .posting-card-crm {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        transition: all 0.2s ease;
        background: #ffffff;
        position: relative;
    }
    .posting-card-crm:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 16px -4px rgba(0,0,0,0.06);
    }
    .posting-card-crm.primary-appointment {
        border-color: #bfdbfe;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border-left: 4px solid #2563eb;
    }
    .hero-badge-pill {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .hero-badge-pill:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    @media print {
        body { background: #ffffff !important; font-size: 12px; }
        .no-print, .btn, .crm-nav-tabs, .dropdown { display: none !important; }
        .tab-pane { display: block !important; opacity: 1 !important; }
        .crm-hero { background: #ffffff !important; color: #000000 !important; border: 1px solid #ccc !important; box-shadow: none !important; padding: 15px !important; }
        .crm-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumbs & Quick Back -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 no-print">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.settings.employees.index') }}" class="text-decoration-none text-muted">Employee Directory</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $employee->employee_no }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.employees.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1.5">
                <i class="mdi mdi-arrow-left text-muted"></i>
                <span>Employee Directory</span>
            </a>
            <button type="button" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-1.5 text-dark d-flex align-items-center gap-1.5" onclick="window.print()">
                <i class="mdi mdi-printer-outline text-secondary"></i>
                <span>Print Dossier</span>
            </button>
            @if(!$isSuperAdmin)
                <a href="{{ route('dss.settings.employees.edit', $employee->encrypted_id) }}" class="btn btn-sm btn-dark fw-bold px-3 py-1.5 shadow-2xs d-flex align-items-center gap-1.5">
                    <i class="mdi mdi-pencil-outline"></i>
                    <span>Edit Profile</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Executive CRM Hero Dossier -->
    @php
        $initials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1));
        $statusBg = match($employee->employment_status) {
            'Active' => '#10b981',
            'On Leave' => '#f59e0b',
            default => '#64748b',
        };
        $statusTextClass = match($employee->employment_status) {
            'Active' => 'bg-success text-white',
            'On Leave' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    @endphp
    <div class="crm-hero">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-4">
            <div class="d-flex align-items-center gap-3.5 flex-wrap flex-sm-nowrap">
                <div class="crm-avatar">
                    {{ $initials }}
                    <span class="status-pulse-dot" style="background-color: {{ $statusBg }};" title="Status: {{ $employee->employment_status }}"></span>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1.5">
                        <h3 class="fw-bold text-white mb-0" style="letter-spacing: -0.02em;">{{ $employee->full_name }}</h3>
                        <span class="badge bg-white text-dark font-mono px-2.5 py-1 fw-bold" style="font-size: 12px;">{{ $employee->employee_no }}</span>
                        <span class="badge {{ $statusTextClass }} px-2.5 py-1 font-mono text-uppercase" style="font-size: 11px;">{{ $employee->employment_status }}</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5" style="font-size: 11px;">{{ $employee->employment_type }}</span>
                    </div>

                    <div class="text-white-50 small d-flex align-items-center gap-2 flex-wrap mb-3">
                        <span class="text-white fw-bold fs-6">
                            <i class="mdi mdi-briefcase-check-outline text-info me-1"></i>
                            {{ $primaryDesig ? $primaryDesig->designation : ($employee->designation ?: 'Staff Member') }}
                        </span>
                        <span>&bull;</span>
                        <span class="text-white-50">
                            <i class="mdi mdi-office-building text-warning me-1"></i>
                            {{ $primaryDesig?->department?->unit_name ?? $employee->department?->unit_name ?? 'Unassigned Office' }}
                        </span>
                        <span>&bull;</span>
                        <span class="text-white-50">
                            <i class="mdi mdi-map-marker text-danger me-0.5"></i>
                            {{ $primaryDesig?->campus?->campus_name ?? $employee->campus?->campus_name ?? 'Main Campus' }}
                        </span>
                    </div>

                    <!-- Quick Interactive Contact Bar -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="mailto:{{ $employee->email }}" class="hero-badge-pill" title="Click to send email">
                            <i class="mdi mdi-email-outline"></i>
                            <span class="font-mono">{{ $employee->email }}</span>
                        </a>

                        @if($employee->phone_number)
                            <a href="tel:{{ $employee->phone_number }}" class="hero-badge-pill" title="Click to call">
                                <i class="mdi mdi-phone-outline"></i>
                                <span class="font-mono">{{ $employee->phone_number }}</span>
                            </a>
                        @endif

                        @if($employee->user)
                            <span class="hero-badge-pill" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4);">
                                <i class="mdi mdi-shield-account text-success"></i>
                                <span>User Linked: <strong>{{ $employee->user->role }}</strong></span>
                            </span>
                        @else
                            <span class="hero-badge-pill" style="background: rgba(245, 158, 11, 0.2); border-color: rgba(245, 158, 11, 0.4);">
                                <i class="mdi mdi-account-cancel-outline text-warning"></i>
                                <span>Entity Only (0..1)</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Actions Toolbar -->
            <div class="d-flex align-items-center gap-2 flex-wrap no-print">
                @if(!$isSuperAdmin)
                    @if(!$employee->user)
                        <button type="button" class="btn btn-primary fw-bold px-3.5 py-2 shadow-sm d-flex align-items-center gap-1.5" onclick="openGenerateUserModal('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->employee_no }}', '{{ $employee->email }}', '{{ addslashes($employee->designation) }}')">
                            <i class="mdi mdi-key-plus"></i>
                            <span>Generate User Access</span>
                        </button>
                    @endif

                    <a href="{{ route('dss.settings.employees.edit', $employee->encrypted_id) }}" class="btn btn-white border px-3.5 py-2 fw-semibold d-flex align-items-center gap-1.5 text-dark shadow-2xs">
                        <i class="mdi mdi-pencil-outline text-primary"></i>
                        <span>Edit Personnel</span>
                    </a>

                    <!-- Dropdown for Status Switch & Governance -->
                    <div class="dropdown">
                        <button class="btn btn-white border px-2.5 py-2 text-dark shadow-2xs" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="More Operations">
                            <i class="mdi mdi-dots-vertical fs-6"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px;">
                            <li class="dropdown-header text-uppercase extra-small fw-bold">Employment Status</li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $employee->employment_status === 'Active' ? 'active' : '' }}" href="javascript:void(0)" onclick="quickToggleStatus('{{ $employee->encrypted_id }}', 'Active')">
                                    <span class="status-dot status-dot-active"></span>
                                    <span>Mark Active</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $employee->employment_status === 'On Leave' ? 'active' : '' }}" href="javascript:void(0)" onclick="quickToggleStatus('{{ $employee->encrypted_id }}', 'On Leave')">
                                    <span class="status-dot status-dot-leave"></span>
                                    <span>Mark On Leave</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $employee->employment_status === 'Inactive' ? 'active' : '' }}" href="javascript:void(0)" onclick="quickToggleStatus('{{ $employee->encrypted_id }}', 'Inactive')">
                                    <span class="status-dot status-dot-inactive"></span>
                                    <span>Mark Inactive</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @if($employee->user)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-primary" href="javascript:void(0)" onclick="openGenerateUserModal('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->employee_no }}', '{{ $employee->user->email }}', '{{ addslashes($employee->designation) }}')">
                                        <i class="mdi mdi-key-change"></i>
                                        <span>Re-issue Credentials &amp; Slip</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-warning" href="javascript:void(0)" onclick="revokeUserAccess('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->user->email }}')">
                                        <i class="mdi mdi-link-variant-off"></i>
                                        <span>Unlink User Account</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="javascript:void(0)" onclick="confirmDeleteEmployee('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', {{ $employee->user ? 'true' : 'false' }})">
                                    <i class="mdi mdi-archive-arrow-down-outline"></i>
                                    <span>Archive Personnel Record</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 4 Key Executive CRM Metric Cards Strip -->
    <div class="row g-3 mb-4">
        <!-- 1. Tenure in Service -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="crm-stat-box accent-blue">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Tenure in Service</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $tenure ?: 'Newly Appointed' }}</div>
                        <div class="extra-small text-muted mt-1">
                            <i class="mdi mdi-calendar-check text-primary me-0.5"></i>
                            {{ $employee->date_hired ? 'Since ' . $employee->date_hired->format('M d, Y') : 'Date not specified' }}
                        </div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-2.5 text-primary">
                        <i class="mdi mdi-clock-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Active Appointments -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="crm-stat-box accent-green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Total Appointments</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $employee->designations->count() ?: 1 }} Position{{ $employee->designations->count() === 1 ? '' : 's' }}</div>
                        <div class="extra-small text-muted mt-1">
                            <i class="mdi mdi-shield-star text-success me-0.5"></i>
                            1 Mother Unit &bull; {{ max(0, $employee->designations->count() - 1) }} Concurrent
                        </div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-2.5 text-success">
                        <i class="mdi mdi-briefcase-account-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. System Access Status -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="crm-stat-box accent-purple">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">System Access (0..1)</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">
                            {{ $employee->user ? 'SSO Enabled' : 'Entity Only' }}
                        </div>
                        <div class="extra-small text-muted mt-1">
                            @if($employee->user)
                                <i class="mdi mdi-check-circle text-success me-0.5"></i> Role: <strong>{{ $employee->user->role }}</strong>
                            @else
                                <i class="mdi mdi-information-outline text-muted me-0.5"></i> No active user account
                            @endif
                        </div>
                    </div>
                    <div class="rounded-3 bg-purple-subtle p-2.5 text-purple" style="background-color: #f3e8ff; color: #7c3aed;">
                        <i class="mdi mdi-account-key-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Institutional Standing -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="crm-stat-box accent-amber">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Standing &amp; Cadre</div>
                        <div class="fs-4 fw-bold text-dark mb-0 font-mono">{{ $employee->employment_status }}</div>
                        <div class="extra-small text-muted mt-1">
                            <i class="mdi mdi-account-tie text-warning me-0.5"></i> {{ $employee->employment_type }} Personnel
                        </div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-2.5 text-warning">
                        <i class="mdi mdi-badge-account fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive CRM Tabs -->
    <ul class="nav crm-nav-tabs no-print" id="crmDossierTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
                <i class="mdi mdi-account-box-outline fs-5"></i>
                <span>360° Profile &amp; Identification</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-appointments-btn" data-bs-toggle="tab" data-bs-target="#tab-appointments" type="button" role="tab">
                <i class="mdi mdi-briefcase-account-outline fs-5"></i>
                <span>Institutional Appointments &amp; Mother Unit</span>
                <span class="badge bg-light text-dark border font-mono ms-1">{{ $employee->designations->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-security-btn" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab">
                <i class="mdi mdi-shield-key-outline fs-5"></i>
                <span>System Credentials &amp; Google SSO</span>
                @if($employee->user)
                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 10px;">Active</span>
                @else
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1" style="font-size: 10px;">0..1 Entity</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-governance-btn" data-bs-toggle="tab" data-bs-target="#tab-governance" type="button" role="tab">
                <i class="mdi mdi-history fs-5"></i>
                <span>Governance &amp; Audit Trail</span>
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="crmDossierTabsContent">
        <!-- ================= TAB 1: 360° PROFILE OVERVIEW ================= -->
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
            <div class="row g-4">
                <!-- Left: Identity & Contact Card -->
                <div class="col-12 col-lg-6">
                    <div class="crm-card">
                        <div class="crm-card-title">
                            <span class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-card-account-details-outline text-primary fs-5"></i>
                                <span>Personal Identification &amp; Official Bio</span>
                            </span>
                            @if(!$isSuperAdmin)
                                <a href="{{ route('dss.settings.employees.edit', $employee->encrypted_id) }}" class="btn btn-sm btn-light border py-0.5 px-2 font-mono extra-small">
                                    Edit
                                </a>
                            @endif
                        </div>

                        <div class="info-row">
                            <span class="info-label">Full Legal Name:</span>
                            <span class="info-val fs-6">{{ $employee->full_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employee ID No.:</span>
                            <span class="info-val font-mono">
                                <span class="badge bg-light text-dark border">{{ $employee->employee_no }}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Gender:</span>
                            <span class="info-val">{{ $employee->gender ?: 'Not Specified' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Official / Institutional Email:</span>
                            <span class="info-val font-mono">
                                <a href="mailto:{{ $employee->email }}" class="text-primary text-decoration-none">
                                    {{ $employee->email }}
                                </a>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Official Contact Number:</span>
                            <span class="info-val font-mono">{{ $employee->phone_number ?: 'None on record' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sovereign Institution:</span>
                            <span class="info-val">{{ $tenant->school_name ?? $tenant->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Service & Mother Unit Station Card -->
                <div class="col-12 col-lg-6">
                    <div class="crm-card">
                        <div class="crm-card-title">
                            <span class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-office-building-cog-outline text-success fs-5"></i>
                                <span>Institutional Cadre &amp; Mother Station</span>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                Verified
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Mother Unit Designation:</span>
                            <span class="info-val text-primary fw-bold">
                                {{ $primaryDesig ? $primaryDesig->designation : ($employee->designation ?: 'Staff Member') }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Parent Department / Office:</span>
                            <span class="info-val">
                                {{ $primaryDesig?->department?->unit_name ?? $employee->department?->unit_name ?? 'Unassigned Office' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Home Campus:</span>
                            <span class="info-val">
                                <i class="mdi mdi-map-marker-outline text-secondary me-0.5"></i>
                                {{ $primaryDesig?->campus?->campus_name ?? $employee->campus?->campus_name ?? 'Main Campus' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employment Classification:</span>
                            <span class="info-val">
                                <span class="badge bg-light text-dark border">{{ $employee->employment_type }}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date of Official Assumption:</span>
                            <span class="info-val font-mono">
                                {{ $employee->date_hired ? $employee->date_hired->format('F d, Y') : 'Not Specified' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Calculated Tenure:</span>
                            <span class="info-val text-success font-mono fw-bold">
                                {{ $tenure ?: 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: APPOINTMENTS & DESIGNATIONS ================= -->
        <div class="tab-pane fade" id="tab-appointments" role="tabpanel">
            <div class="crm-card">
                <div class="crm-card-title">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-briefcase-account-outline text-primary fs-5"></i>
                        <span>Institutional Appointments &amp; Designation History</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="extra-small text-muted">Total: <strong>{{ $employee->designations->count() }}</strong></span>
                        @if(!$isSuperAdmin)
                            <a href="{{ route('dss.settings.employees.edit', $employee->encrypted_id) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2.5 py-1 extra-small">
                                <i class="mdi mdi-plus-circle me-1"></i>Add / Modify Appointment
                            </a>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($employee->designations as $d)
                        @php
                            $isPri = (bool) $d->is_primary;
                        @endphp
                        <div class="posting-card-crm {{ $isPri ? 'primary-appointment' : '' }}">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h5 class="fw-bold text-dark mb-0 fs-6">{{ $d->designation }}</h5>
                                        @if($isPri)
                                            <span class="badge bg-primary text-white" style="font-size: 9.5px; letter-spacing: 0.05em;">
                                                <i class="mdi mdi-star me-0.5"></i>MOTHER UNIT / PRIMARY
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 9.5px;">
                                                CONCURRENT APPOINTMENT
                                            </span>
                                        @endif

                                        @if($d->designationModel)
                                            <span class="badge bg-light text-secondary border font-mono extra-small">
                                                {{ $d->designationModel->category }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-muted small d-flex align-items-center gap-3 flex-wrap">
                                        <div>
                                            <i class="mdi mdi-office-building text-primary me-1"></i>
                                            <span class="fw-semibold text-dark">{{ $d->department?->unit_name ?? 'Unassigned Office' }}</span>
                                        </div>
                                        <div>
                                            <i class="mdi mdi-map-marker-outline text-secondary me-0.5"></i>
                                            <span>{{ $d->campus?->campus_name ?? 'Main Campus' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">
                                        {{ $d->status ?? 'Active' }}
                                    </span>
                                </div>
                            </div>

                            @if($d->effective_date || $d->end_date || $d->remarks)
                                <div class="pt-2 mt-2 border-top extra-small text-muted d-flex align-items-center gap-3 flex-wrap font-mono">
                                    @if($d->effective_date)
                                        <div><strong>Effective:</strong> {{ $d->effective_date->format('M d, Y') }}</div>
                                    @endif
                                    @if($d->end_date)
                                        <div><strong>End Date:</strong> {{ $d->end_date->format('M d, Y') }}</div>
                                    @endif
                                    @if($d->remarks)
                                        <div class="text-muted"><em>{{ $d->remarks }}</em></div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="mdi mdi-briefcase-off-outline fs-1 text-muted d-block mb-2"></i>
                            <div class="fw-bold text-dark fs-6">No Institutional Appointments Recorded</div>
                            <div class="extra-small text-muted mt-1">Assign primary mother unit or concurrent appointments by clicking Edit Profile.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ================= TAB 3: SYSTEM USER CREDENTIALS & GOOGLE SSO ================= -->
        <div class="tab-pane fade" id="tab-security" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="crm-card">
                        <div class="crm-card-title">
                            <span class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-shield-account text-success fs-5"></i>
                                <span>Sovereign Identity &amp; Login Credential (0..1)</span>
                            </span>
                            @if($employee->user)
                                <span class="badge bg-success text-white font-mono extra-small">Account Active</span>
                            @else
                                <span class="badge bg-warning text-dark font-mono extra-small">Entity Only</span>
                            @endif
                        </div>

                        @if($employee->user)
                            <div class="p-3.5 rounded-3 bg-light border mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="mdi mdi-account-circle fs-4 text-primary"></i>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $employee->user->name }}</div>
                                            <div class="font-mono text-muted extra-small">{{ $employee->user->email }}</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary text-white font-mono px-2.5 py-1">{{ $employee->user->role }}</span>
                                </div>

                                <div class="p-2.5 bg-white rounded border extra-small font-mono text-muted d-flex align-items-center justify-content-between">
                                    <span>Google Workspace SSO:</span>
                                    <span class="text-success fw-bold d-flex align-items-center gap-1">
                                        <i class="mdi mdi-google text-danger"></i> 1-Click OAuth Enabled
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Account Creation Date:</span>
                                <span class="info-val font-mono">{{ $employee->user->created_at ? $employee->user->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Last Profile Synchronized:</span>
                                <span class="info-val font-mono">{{ $employee->user->updated_at ? $employee->user->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Account Verification:</span>
                                <span class="info-val text-success">
                                    <i class="mdi mdi-check-decagram text-success me-1"></i>Verified Institutional Identity
                                </span>
                            </div>

                            @if(!$isSuperAdmin)
                                <div class="pt-3 mt-3 border-top d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold py-2 px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" onclick="openGenerateUserModal('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->employee_no }}', '{{ $employee->user->email }}', '{{ addslashes($employee->designation) }}')">
                                        <i class="mdi mdi-key-change"></i>
                                        <span>Re-issue Credentials &amp; Print Slip</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning fw-semibold py-2 px-3" onclick="revokeUserAccess('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->user->email }}')">
                                        <i class="mdi mdi-link-variant-off me-1"></i>Unlink Login
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="p-4 rounded-3 bg-light border text-center mb-3">
                                <i class="mdi mdi-account-off-outline fs-1 text-muted d-block mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">Institutional Personnel Entity (0..1)</h6>
                                <p class="text-muted extra-small mb-3" style="max-width: 420px; margin: 0 auto;">
                                    This employee is registered in the institutional master directory without a system login credential. They are safe to participate in document routing workflows and institutional records.
                                </p>
                                @if(!$isSuperAdmin)
                                    <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-2xs" onclick="openGenerateUserModal('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', '{{ $employee->employee_no }}', '{{ $employee->email }}', '{{ addslashes($employee->designation) }}')">
                                        <i class="mdi mdi-key-plus me-1"></i>Generate System Login Access Now
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="crm-card">
                        <div class="crm-card-title">
                            <span class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-shield-check-outline text-primary fs-5"></i>
                                <span>Security Policy &amp; OAuth SSO</span>
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="fw-bold text-dark small mb-1">
                                <i class="mdi mdi-google text-danger me-1"></i>Google Workspace Integration
                            </div>
                            <div class="extra-small text-muted" style="line-height: 1.5;">
                                When enabled, personnel log in directly using their institutional email without needing to remember an extra application password.
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <div class="fw-bold text-dark small mb-1">
                                <i class="mdi mdi-lock-outline text-secondary me-1"></i>Sovereign Boundaries
                            </div>
                            <div class="extra-small text-muted" style="line-height: 1.5;">
                                Credentials and session permissions are strictly isolated within the <strong>{{ $tenant->school_name ?? $tenant->name }}</strong> tenant partition.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: GOVERNANCE & AUDIT TRAIL ================= -->
        <div class="tab-pane fade" id="tab-governance" role="tabpanel">
            <div class="crm-card">
                <div class="crm-card-title">
                    <span class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-history text-primary fs-5"></i>
                        <span>Institutional Audit Trail &amp; Governance Metadata</span>
                    </span>
                    <span class="badge bg-light text-muted border font-mono">Immutable Log</span>
                </div>

                <div class="timeline p-2">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-primary-subtle p-2 text-primary flex-shrink-0">
                            <i class="mdi mdi-account-plus fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">Personnel Profile Registered in Master Entity Directory</div>
                            <div class="extra-small text-muted mt-0.5">
                                Created at: <span class="font-mono fw-semibold text-dark">{{ $employee->created_at ? $employee->created_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle bg-info-subtle p-2 text-info flex-shrink-0">
                            <i class="mdi mdi-update fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small">Last Profile Dossier Synchronization</div>
                            <div class="extra-small text-muted mt-0.5">
                                Updated at: <span class="font-mono fw-semibold text-dark">{{ $employee->updated_at ? $employee->updated_at->format('M d, Y h:i:s A') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($employee->user)
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <div class="rounded-circle bg-success-subtle p-2 text-success flex-shrink-0">
                                <i class="mdi mdi-shield-account fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">System User Account Generated &amp; Bound</div>
                                <div class="extra-small text-muted mt-0.5">
                                    Bound to: <span class="font-mono fw-semibold text-dark">{{ $employee->user->email }}</span> with role [{{ $employee->user->role }}]
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-3 bg-light rounded-3 border mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="extra-small text-muted">
                        <i class="mdi mdi-shield-lock-outline text-primary me-1"></i>
                        Governed under ISO 9001:2015 institutional records management policies.
                    </div>
                    @if(!$isSuperAdmin)
                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold px-3 py-1.5" onclick="confirmDeleteEmployee('{{ $employee->encrypted_id }}', '{{ addslashes($employee->full_name) }}', {{ $employee->user ? 'true' : 'false' }})">
                            <i class="mdi mdi-archive-arrow-down-outline me-1"></i>Archive Personnel Record
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('dss.settings.employees.partials.generate-user-modal')
@endsection

@section('scripts')
<script>
    function quickToggleStatus(empId, newStatus) {
        fetch('/dss/settings/employees/' + empId + '/toggle-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Status update failed.' });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    function revokeUserAccess(empId, empName, userEmail) {
        Swal.fire({
            title: 'Unlink User Account?',
            html: `Are you sure you want to unlink and deactivate the system login account (<strong>${userEmail}</strong>) for <strong>${empName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Unlink Access',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/dss/settings/employees/' + empId + '/revoke-user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Access Revoked',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: data.message });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error revoking user access.' });
                });
            }
        });
    }

    function confirmDeleteEmployee(empId, empName, hasUser) {
        let warning = `Are you sure you want to archive <strong>${empName}</strong> from the Master Institutional Directory?`;
        if (hasUser) {
            warning = `<strong>${empName}</strong> has an active system user login account.<br><br>Archiving will also unlink and deactivate their user access. Continue?`;
        }

        Swal.fire({
            title: 'Archive Personnel Record?',
            html: warning,
            icon: hasUser ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Archive Record',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/dss/settings/employees/' + empId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ force_detach_user: 1 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Archived',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        }).then(() => {
                            window.location.href = '{{ route('dss.settings.employees.index') }}';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error archiving employee record.' });
                });
            }
        });
    }
</script>
@endsection
