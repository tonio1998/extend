@extends('layouts.dss.app')

@section('title', 'Campuses — School Management')

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">School Management</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size:12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">Campuses</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-map-marker-multiple text-dss-primary fs-5"></i>
                Registered Campuses
            </h3>
            <p class="text-muted small mb-0">Sovereign multi-campus branches, geographic satellite units, and campus directors.</p>
        </div>
        <div>
            <button class="btn btn-dss-primary btn-sm px-3 shadow-xs" onclick="openCampusModal()">
                <i class="mdi mdi-plus me-1"></i>Add Campus Unit
            </button>
        </div>
    </div>

    {{-- Campuses Grid --}}
    <div class="dss-card p-4">
        @if($campuses->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="mdi mdi-map-marker-off-outline fs-1 d-block mb-2 text-secondary"></i>
                <div class="fw-semibold mb-1">No campuses registered yet.</div>
                <div class="small">Click <strong>Add Campus Unit</strong> to register your first campus.</div>
            </div>
        @else
            <div class="row g-3">
                @foreach($campuses as $campus)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 rounded-3 border bg-light h-100 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-dss-primary font-mono">{{ $campus->campus_code }}</span>
                                <span class="badge bg-white text-muted border extra-small">{{ $campus->campus_type }}</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $campus->campus_name }}</h6>
                            <div class="extra-small text-muted mb-2">
                                <i class="mdi mdi-map-marker text-danger me-0.5"></i>{{ $campus->location ?: 'Main Grounds' }}
                            </div>
                            <div class="extra-small text-muted font-mono border-top pt-2 d-flex justify-content-between">
                                <span>Director:</span>
                                <strong>{{ $campus->head_name ?: 'Campus Director' }}</strong>
                            </div>
                            @if($campus->departments->isNotEmpty())
                                <div class="mt-2 pt-2 border-top">
                                    <div class="extra-small text-muted fw-semibold mb-1">Units / Departments ({{ $campus->departments->count() }})</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($campus->departments->take(6) as $dept)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono" style="font-size:10px;">{{ $dept->unit_code ?: $dept->unit_name }}</span>
                                        @endforeach
                                        @if($campus->departments->count() > 6)
                                            <span class="badge bg-secondary-subtle text-secondary border font-mono" style="font-size:10px;">+{{ $campus->departments->count() - 6 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Modal: Add Campus Unit --}}
<div class="modal fade" id="addCampusModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc; border-left: 4px solid var(--dss-primary, #7d2ae8);">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:34px; height:34px; background: var(--dss-primary-soft, #f5f3ff); border: 1px solid var(--dss-primary-border, #e9d5ff);">
                            <i class="mdi mdi-map-marker-plus-outline" style="color: var(--dss-primary, #7d2ae8); font-size: 18px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Add Campus Unit</h6>
                            <div class="extra-small text-muted">Register a new campus, satellite, or extension unit</div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-mono fw-bold" id="c_code" placeholder="e.g. DELCARMEN">
                        <div class="extra-small text-muted mt-1">Short identifier used in document controls</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="c_name" placeholder="e.g. Del Carmen Campus">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Type</label>
                        <select class="form-select" id="c_type">
                            <option value="Main Campus">Main Campus</option>
                            <option value="Satellite Campus">Satellite Campus</option>
                            <option value="Extension Unit">Extension Unit</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Location / Address</label>
                        <input type="text" class="form-control" id="c_location" placeholder="e.g. Del Carmen, Surigao del Norte">
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Director / Head</label>
                        <input type="text" class="form-control" id="c_head" placeholder="e.g. Dr. Juan Dela Cruz">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-dss-primary px-4 fw-semibold" onclick="submitCampus()">
                    <i class="mdi mdi-content-save me-1"></i>Save Campus Unit
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function openCampusModal() {
        new bootstrap.Modal(document.getElementById('addCampusModal')).show();
    }

    function submitCampus() {
        const code = document.getElementById('c_code').value.trim();
        const name = document.getElementById('c_name').value.trim();
        if (!code || !name) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Campus Code and Name are required.' });
            return;
        }
        fetch("{{ route('dss.settings.institution.campus') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                campus_code: code,
                campus_name: name,
                campus_type: document.getElementById('c_type').value,
                location: document.getElementById('c_location').value,
                head_name: document.getElementById('c_head').value,
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({ icon: 'success', title: 'Saved!', text: d.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save campus.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
    }
</script>
@endsection

