@extends('layouts.dss.app')

@section('title', 'Campuses — School Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/responsive.bootstrap5.min.css') }}">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    {{-- Breadcrumb & Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">School Management</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size: 12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">Campuses</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-map-marker-multiple text-primary fs-5"></i>
                Registered Campuses Registry
            </h3>
            <p class="text-muted small mb-0">Sovereign multi-campus branches, geographic satellite units, and campus directors.</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm px-3 rounded-pill shadow-xs d-flex align-items-center gap-1 fw-bold" onclick="openCampusModal()">
                <i class="mdi mdi-plus"></i>
                <span>Add Campus Unit</span>
            </button>
        </div>
    </div>

    {{-- Campuses Table Card --}}
    <div class="card border rounded-3 p-4 bg-white shadow-2xs mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutional Campus Branches</h6>
                <div class="extra-small text-muted">
                    Official campuses and extension branches governing academic colleges and research centers.
                </div>
            </div>
            <span class="badge bg-light text-dark border font-mono px-3 py-1">
                {{ $campuses->count() }} Registered {{ Str::plural('Campus', $campuses->count()) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="campusesTable" style="font-size: 13px;">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th style="width: 100px;">Actions</th>
                        <th style="width: 130px;">Campus Code</th>
                        <th style="width: 250px;">Campus Name &amp; Location</th>
                        <th style="width: 150px;">Type</th>
                        <th style="width: 200px;">Director / Head</th>
                        <th>Academic Units / Departments</th>
                        <th class="pe-3 text-end" style="width: 110px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campuses as $campus)
                        @php
                            $typeBadgeClass = match($campus->campus_type) {
                                'Main Campus' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'Satellite Campus' => 'bg-info-subtle text-info border border-info-subtle',
                                'Extension Unit' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                default => 'bg-secondary-subtle text-secondary border'
                            };
                            $deptCount = $campus->departments->count();
                        @endphp
                        <tr id="campus-row-{{ $campus->id }}">
                            <td>
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 extra-small fw-semibold"
                                            onclick="openEditCampusModal({{ $campus->id }}, '{{ addslashes($campus->campus_code) }}', '{{ addslashes($campus->campus_name) }}', '{{ addslashes($campus->campus_type) }}', '{{ addslashes($campus->location ?? '') }}', '{{ addslashes($campus->head_name ?? '') }}', '{{ addslashes($campus->contact_no ?? '') }}', '{{ $campus->status ?? 'active' }}')"
                                            title="Edit Campus">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-light border text-danger rounded-pill px-2 py-1 extra-small fw-semibold"
                                            onclick="deleteCampus({{ $campus->id }}, '{{ addslashes($campus->campus_name) }}', {{ $deptCount }})"
                                            title="Delete Campus">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <code class="fw-bold text-dark px-2 py-1 rounded bg-light border font-mono" style="font-size: 11.5px;">
                                    {{ $campus->campus_code }}
                                </code>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $campus->campus_name }}</div>
                                <div class="extra-small text-muted">
                                    <i class="mdi mdi-map-marker text-danger me-1"></i>{{ $campus->location ?: 'Main Grounds' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $typeBadgeClass }} font-mono px-2 py-1 extra-small">
                                    {{ $campus->campus_type }}
                                </span>
                            </td>
                            <td>
                                @if($campus->head_name)
                                    <div class="fw-semibold text-dark extra-small">
                                        <i class="mdi mdi-account-tie-outline text-muted me-1"></i>{{ $campus->head_name }}
                                    </div>
                                @else
                                    <span class="text-muted extra-small font-mono">Unassigned</span>
                                @endif
                                @if($campus->contact_no)
                                    <div class="extra-small text-muted font-mono">
                                        <i class="mdi mdi-phone-outline me-1"></i>{{ $campus->contact_no }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($deptCount > 0)
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        <span class="badge bg-light text-dark border font-mono extra-small">{{ $deptCount }} {{ Str::plural('Unit', $deptCount) }}</span>
                                        @foreach($campus->departments->take(4) as $dept)
                                            <span class="badge bg-light text-secondary border font-mono" style="font-size: 10px;">{{ $dept->unit_code ?: $dept->unit_name }}</span>
                                        @endforeach
                                        @if($deptCount > 4)
                                            <span class="text-muted extra-small font-mono">+{{ $deptCount - 4 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted extra-small font-mono">0 departments</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                @if(($campus->status ?? 'active') === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 extra-small">
                                        <i class="mdi mdi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border font-mono px-2 py-1 extra-small">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL: ADD CAMPUS UNIT                                                    --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="addCampusModal" tabindex="-1" aria-labelledby="addCampusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 d-flex align-items-center justify-content-center bg-primary-subtle text-primary border border-primary-subtle" style="width: 34px; height: 34px;">
                        <i class="mdi mdi-map-marker-plus-outline fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="addCampusModalLabel">Add Campus Unit</h6>
                        <div class="extra-small text-muted">Register a new campus, satellite, or extension unit</div>
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
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Type</label>
                        <select class="form-select" id="c_type">
                            <option value="Main Campus">Main Campus</option>
                            <option value="Satellite Campus" selected>Satellite Campus</option>
                            <option value="Extension Unit">Extension Unit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Location / Address</label>
                        <input type="text" class="form-control" id="c_location" placeholder="e.g. Del Carmen, Surigao del Norte">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Status</label>
                        <select class="form-select" id="c_status">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Director / Head</label>
                        <input type="text" class="form-control" id="c_head" placeholder="e.g. Dr. Juan Dela Cruz">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Contact Details</label>
                        <input type="text" class="form-control font-mono" id="c_contact" placeholder="e.g. (086) 826-1234 / info@campus.edu.ph">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2 bg-light">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" onclick="submitCampus()">
                    <i class="mdi mdi-content-save me-1"></i>Save Campus Unit
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL: EDIT CAMPUS UNIT                                                   --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="editCampusModal" tabindex="-1" aria-labelledby="editCampusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 d-flex align-items-center justify-content-center bg-primary-subtle text-primary border border-primary-subtle" style="width: 34px; height: 34px;">
                        <i class="mdi mdi-pencil-outline fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="editCampusModalLabel">Edit Campus Unit</h6>
                        <div class="extra-small text-muted">Update campus specifications and director designations</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_campus_id">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-mono fw-bold" id="edit_c_code">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_c_name">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Type</label>
                        <select class="form-select" id="edit_c_type">
                            <option value="Main Campus">Main Campus</option>
                            <option value="Satellite Campus">Satellite Campus</option>
                            <option value="Extension Unit">Extension Unit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Location / Address</label>
                        <input type="text" class="form-control" id="edit_c_location">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Status</label>
                        <select class="form-select" id="edit_c_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Campus Director / Head</label>
                        <input type="text" class="form-control" id="edit_c_head">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Contact Details</label>
                        <input type="text" class="form-control font-mono" id="edit_c_contact">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2 bg-light">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-2xs" onclick="submitEditCampus()">
                    <i class="mdi mdi-check-circle-outline me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/responsive.bootstrap5.min.js') }}"></script>

<script>
    let campusesDt;
    const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    $(document).ready(function() {
        campusesDt = $('#campusesTable').DataTable({
            responsive: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search campuses, codes, locations...",
                lengthMenu: "Show _MENU_ campuses"
            },
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: 0 }
            ]
        });
    });

    function openCampusModal() {
        document.getElementById('c_code').value = '';
        document.getElementById('c_name').value = '';
        document.getElementById('c_type').value = 'Satellite Campus';
        document.getElementById('c_location').value = '';
        document.getElementById('c_status').value = 'active';
        document.getElementById('c_head').value = '';
        document.getElementById('c_contact').value = '';
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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                campus_code: code,
                campus_name: name,
                campus_type: document.getElementById('c_type').value,
                location: document.getElementById('c_location').value.trim(),
                status: document.getElementById('c_status').value,
                head_name: document.getElementById('c_head').value.trim(),
                contact_no: document.getElementById('c_contact').value.trim()
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: d.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save campus.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
    }

    function openEditCampusModal(id, code, name, type, location, head, contact, status) {
        document.getElementById('edit_campus_id').value = id;
        document.getElementById('edit_c_code').value = code;
        document.getElementById('edit_c_name').value = name;
        document.getElementById('edit_c_type').value = type;
        document.getElementById('edit_c_location').value = location;
        document.getElementById('edit_c_head').value = head;
        document.getElementById('edit_c_contact').value = contact;
        document.getElementById('edit_c_status').value = status || 'active';
        new bootstrap.Modal(document.getElementById('editCampusModal')).show();
    }

    function submitEditCampus() {
        const id = document.getElementById('edit_campus_id').value;
        const code = document.getElementById('edit_c_code').value.trim();
        const name = document.getElementById('edit_c_name').value.trim();

        if (!code || !name) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Campus Code and Name cannot be empty.' });
            return;
        }

        fetch("{{ url('dss/settings/institution/campus') }}/" + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                campus_code: code,
                campus_name: name,
                campus_type: document.getElementById('edit_c_type').value,
                location: document.getElementById('edit_c_location').value.trim(),
                status: document.getElementById('edit_c_status').value,
                head_name: document.getElementById('edit_c_head').value.trim(),
                contact_no: document.getElementById('edit_c_contact').value.trim()
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: d.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to update campus.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
    }

    function deleteCampus(id, name, deptCount) {
        let warningText = `Are you sure you want to permanently delete "${name}"?`;
        if (deptCount > 0) {
            warningText += ` This campus currently has ${deptCount} academic department(s) attached and cannot be deleted until those units are reassigned or removed.`;
        }

        Swal.fire({
            title: 'Delete Campus Unit?',
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                fetch("{{ url('dss/settings/institution/campus') }}/" + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: d.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            const row = document.getElementById('campus-row-' + id);
                            if (campusesDt && row) {
                                campusesDt.row(row).remove().draw();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Cannot Delete', text: d.message || 'Failed to delete campus.' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
            }
        });
    }
</script>
@endsection
