@extends('layouts.dss.app')

@section('title', 'School Profile — School Management')

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">School Management</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size:12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">School Profile</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-white extra-small">SOVEREIGN SUC ENGINE</span>
                <span class="badge bg-light text-dark border extra-small">ISO 9001:2015 QMS ARCHITECTURE</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-school text-dss-primary fs-5"></i>
                Institutional Identification &amp; Legal Charter Profile
            </h3>
            <p class="text-muted small mb-0">Fundamental university metadata, charter basis, contact channels, and official fiscal identity.</p>
        </div>
        <div>
            <button class="btn btn-dss-primary btn-sm px-3 shadow-xs" onclick="saveSchoolProfile()">
                <i class="mdi mdi-content-save me-1"></i>Save Institutional Profile
            </button>
        </div>
    </div>

    {{-- Profile Form --}}
    <div class="dss-card p-4">
        <form id="profileForm">
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Official Institution Name</label>
                    <input type="text" class="form-control" id="school_name" name="school_name" value="{{ $institution->school_name }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Acronym</label>
                    <input type="text" class="form-control font-mono" id="school_acronym" name="school_acronym" value="{{ $institution->school_acronym }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Institution Type</label>
                    <input type="text" class="form-control" id="school_type" name="school_type" value="{{ $institution->school_type }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Charter / Enabling Law</label>
                    <input type="text" class="form-control" id="charter_basis" name="charter_basis" value="{{ $institution->charter_basis }}">
                </div>
                <div class="col-12">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Official Campus Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ $institution->address }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Official Email Address</label>
                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $institution->contact_email }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Official Contact / Telephone</label>
                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ $institution->contact_phone }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase">Active Fiscal Year</label>
                    <input type="text" class="form-control font-mono" id="current_fy" name="current_fy" value="{{ $institution->current_fy }}">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-dss-primary px-4" onclick="saveSchoolProfile()">
                    <i class="mdi mdi-content-save me-1"></i>Save Profile
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    var csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function saveSchoolProfile() {
        const formData = {
            school_name:    document.getElementById('school_name').value,
            school_acronym: document.getElementById('school_acronym').value,
            school_type:    document.getElementById('school_type').value,
            charter_basis:  document.getElementById('charter_basis').value,
            address:        document.getElementById('address').value,
            contact_email:  document.getElementById('contact_email').value,
            contact_phone:  document.getElementById('contact_phone').value,
            current_fy:     document.getElementById('current_fy').value,
        };

        fetch("{{ route('dss.settings.institution.profile') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 1800,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to save profile.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
    }
</script>
@endsection
