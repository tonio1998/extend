@extends('layouts.dss.app')

@section('title', 'SaaS Platform Master Executive Dashboard — SNSU RIE System')

@section('styles')
<style>
    .saas-hero-card {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%) !important;
        border: 1px solid #10b981 !important;
        border-radius: 20px !important;
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }
    .saas-hero-card::after {
        content: '';
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
        pointer-events: none;
    }
    .saas-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .saas-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -4px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }
    .saas-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: transparent;
    }
    .saas-stat-card.border-amber::before { background: #d97706; }
    .saas-stat-card.border-indigo::before { background: #4f46e5; }
    .saas-stat-card.border-emerald::before { background: #059669; }
    .saas-stat-card.border-sky::before { background: #0284c7; }
    .saas-stat-card.border-purple::before { background: #9333ea; }
    .saas-stat-card.border-rose::before { background: #e11d48; }

    .health-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        transition: all 0.2s ease;
    }
    .health-pill:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- 1. SaaS Platform Master Hero Banner -->
    <div class="saas-hero-card p-4 p-md-5 mb-4 shadow-sm">
        <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-1.5 font-mono fw-bold" style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); font-size: 11px;">
                        <i class="mdi mdi-shield-crown me-1 text-warning"></i>SaaS Platform Master Dashboard
                    </span>
                    <span class="badge rounded-pill px-2.5 py-1.5 font-mono" style="background: rgba(16, 185, 129, 0.25); color: #a7f3d0; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 11px;">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Cloud Operational
                    </span>
                </div>
                <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">
                    SNSU RIE System — Cloud Architecture Console
                </h2>
                <p class="text-white-50 mb-3" style="font-size: 14px; max-width: 680px; line-height: 1.6;">
                    Central sovereign administration platform for Research, Innovation &amp; Extension (RIE). Oversee multi-campus partitions, extension agendas, banner programs, SDG targets, and state university workloads.
                </p>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-2xs"
                             style="width: 32px; height: 32px; background: #fbbf24; color: #064e3b; font-size: 12px;">
                            AP
                        </div>
                        <div class="extra-small font-mono text-white-50">
                            SuperAdmin: <strong class="text-white">{{ auth()->user()->name ?? 'Engr. Antonio Piloton Jr.' }}</strong> (<code>{{ config('dss.superadmin_email', 'apiloton1@snsu.edu.ph') }}</code>)
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                    <a href="{{ route('dss.settings.tenants.create') }}" class="btn btn-warning fw-bold px-3 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="mdi mdi-plus-circle-outline fs-6"></i>
                        <span>Provision New School</span>
                    </a>
                    <a href="{{ route('dss.settings.tenants.index') }}" class="btn btn-outline-light fw-semibold px-3 py-2 d-flex align-items-center justify-content-center gap-2" style="border-color: rgba(255, 255, 255, 0.3);">
                        <i class="mdi mdi-domain fs-6 text-warning"></i>
                        <span>Manage School Registry</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SaaS Master Bento KPIs Grid -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Registered SUCs -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-amber">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Registered Institutions</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalTenants }}</div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $activeTenantsCount }} Active</span>
                            <span class="ms-1">Sovereign cloud partitions</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle p-3 text-warning-emphasis">
                        <i class="mdi mdi-school fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Managed Personnel -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-indigo">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Managed Employees</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalEmployees }}</div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $withUserEmployees }} Linked Users</span>
                            <span class="badge bg-light text-secondary border font-mono px-1.5 py-0.5 ms-1" style="font-size: 10px;">{{ $entityOnlyEmployees }} Faculty/Staff</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-3 text-primary">
                        <i class="mdi mdi-badge-account-horizontal fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Platform Users -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-emerald">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Total Platform Users</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalPlatformUsers }}</div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">Google SSO Ready</span>
                            <span class="ms-1">Across all campuses</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-3 text-success">
                        <i class="mdi mdi-account-group fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Campus Network -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-sky">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Campuses &amp; Units</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalCampuses }}</div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-info-subtle text-info border border-info-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $totalUnits }} Offices/Units</span>
                            <span class="ms-1">Distributed network</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-info-subtle p-3 text-info">
                        <i class="mdi mdi-bank-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Extension Proposals & Plans -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-purple">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">Extension Workloads</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalProposals }} <span class="fs-6 fw-normal text-muted">Proposals</span></div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-purple-subtle text-purple border border-purple-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $totalPlans }} Extension Plans</span>
                            <span class="ms-1">RIE Project Pipeline</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-purple-subtle p-3 text-purple">
                        <i class="mdi mdi-handshake-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 6: RIE Framework & RBAC -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="saas-stat-card border-rose">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-bold tracking-wider mb-1">RIE Framework &amp; RBAC</div>
                        <div class="fs-2 fw-bold text-dark font-mono mb-0">{{ $totalRoles }} Roles / {{ $totalPermissions }} Perms</div>
                        <div class="extra-small text-muted mt-1">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-1.5 py-0.5" style="font-size: 10px;">{{ $totalBannerPrograms }} Banner Programs</span>
                            <span class="badge bg-light text-dark border font-mono px-1.5 py-0.5 ms-1" style="font-size: 10px;">{{ $totalSdgs }} SDGs</span>
                        </div>
                    </div>
                    <div class="rounded-3 bg-danger-subtle p-3 text-danger">
                        <i class="mdi mdi-shield-lock-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SaaS Infrastructure Health Matrix -->
    <div class="dss-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Platform Infrastructure &amp; RIE Workload Health</h6>
                <div class="extra-small text-muted">Real-time status of multi-tenant cloud subsystems and institutional partitions.</div>
            </div>
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1">
                    <i class="mdi mdi-check-circle me-1"></i>All Systems Healthy
                </span>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="health-pill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="mdi mdi-handshake text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 13px;">RIE Pipeline Engine</span>
                    </div>
                    <div class="extra-small text-muted mb-2">Operational (Active Workflows)</div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="health-pill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="mdi mdi-database text-success fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 13px;">Multi-Tenant DB Sharding</span>
                    </div>
                    <div class="extra-small text-muted mb-2">Partitioned MySQL / MariaDB (Isolated)</div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="health-pill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="mdi mdi-cloud-check text-info fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 13px;">Cloud Storage &amp; PDF Engine</span>
                    </div>
                    <div class="extra-small text-muted mb-2">DomPDF / TCPDF Engine Active</div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Tenants Detail Registry -->
    <div class="dss-card p-0 mb-4 overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutions &amp; Universities Registry</h6>
                <div class="extra-small text-muted">Partitioned institutions configured on this SNSU RIE instance.</div>
            </div>
            <a href="{{ route('dss.settings.tenants.create') }}" class="btn btn-sm btn-dss-primary">
                <i class="mdi mdi-plus me-1"></i>New Institution
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Institution Name</th>
                        <th>Acronym</th>
                        <th>Domain / Code</th>
                        <th>Campuses</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $t->name }}</div>
                                <div class="extra-small text-muted">{{ $t->uuid }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                                    {{ $t->institutionSetting?->school_acronym ?: 'SNSU' }}
                                </span>
                            </td>
                            <td><code>{{ $t->slug ?: 'snsu' }}</code></td>
                            <td>{{ $t->campuses_count }}</td>
                            <td>{{ $t->users_count }}</td>
                            <td>
                                @if($t->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border font-mono">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('dss.settings.tenants.index') }}" class="btn btn-xs btn-outline-secondary">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No registered institutions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
