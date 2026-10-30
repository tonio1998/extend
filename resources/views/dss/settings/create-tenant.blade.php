@extends('layouts.dss.app')

@section('title', 'Register New Institutional School — SuperAdmin Control')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('dss.settings.tenants.index') }}" class="btn btn-sm btn-light border text-muted">
                    <i class="mdi mdi-arrow-left me-1"></i>Back
                </a>
                <h3 class="fw-bold text-dark mb-0">Register New Institutional School</h3>
                <span class="badge bg-danger font-mono">SuperAdmin Privilege</span>
            </div>
            <p class="text-muted small mb-0">Provision a new sovereign university/college database tenant, initial campus unit, and administrator credentials.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="dss-card p-4 p-md-5">
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-xs mb-4 py-2.5 px-3 small">
                        <i class="mdi mdi-alert-circle me-1.5"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('dss.settings.tenants.store') }}">
                    @csrf

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="mdi mdi-school text-dss-primary me-1.5"></i>1. Institution Profile & Identity
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="institution_name">Official University / College Name</label>
                            <input type="text" class="form-control" id="institution_name" name="institution_name" value="{{ old('institution_name') }}" required placeholder="e.g. Surigao del Norte State University">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="school_acronym">School Acronym</label>
                            <input type="text" class="form-control text-uppercase font-mono" id="school_acronym" name="school_acronym" value="{{ old('school_acronym') }}" required placeholder="e.g. SNSU">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="school_type">Institution Classification</label>
                            <select class="form-select" id="school_type" name="school_type">
                                <option value="State University and College (SUC)" selected>State University and College (SUC)</option>
                                <option value="Local University and College (LUC)">Local University and College (LUC)</option>
                                <option value="Private Higher Education Institution (PHEI)">Private Higher Education Institution (PHEI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="campus_name">Main / Primary Campus Name</label>
                            <input type="text" class="form-control" id="campus_name" name="campus_name" value="{{ old('campus_name', 'Main Campus') }}" required placeholder="e.g. Main Campus">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="mdi mdi-account-shield text-dss-primary me-1.5"></i>2. Initial Institution Administrator
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="admin_name">Administrator Full Name</label>
                            <input type="text" class="form-control" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required placeholder="e.g. Engr. Juan Dela Cruz">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="admin_email">Institutional Email Address</label>
                            <input type="email" class="form-control font-mono" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required placeholder="admin@school.edu.ph">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted" for="admin_password">Temporary Initial Password</label>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" required placeholder="Minimum 8 characters">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('dss.settings.tenants.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-dss-primary px-4 py-2.5">
                            <i class="mdi mdi-check-circle me-1.5"></i>Provision Institutional Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
