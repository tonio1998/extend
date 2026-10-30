@extends('layouts.dss.app')

@section('title', 'ISO Document Controls — School Management')

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">School Management</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size:12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">ISO Document Controls</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-white font-mono extra-small">QMS CONTROLLED</span>
                <span class="badge bg-light text-dark border font-mono extra-small">OFFICE &amp; DOCUMENT DRIVEN</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-check-decagram text-primary fs-5"></i>
                ISO 9001:2015 Document Controls Matrix
            </h3>
            <p class="text-muted small mb-0">Configure official Doc No., Effective Date, and Rev No. per originating office and document type.</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm px-3 shadow-2xs fw-bold" onclick="openIsoModal()">
                <i class="mdi mdi-plus-circle me-1"></i>Register ISO Form Rule
            </button>
        </div>
    </div>

    {{-- ISO Controls Table --}}
    <div class="dss-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="extra-small text-uppercase text-muted font-mono">
                        <th style="width:25%;">Originating Office</th>
                        <th style="width:22%;">Document Instrument</th>
                        <th style="width:15%;">ISO Doc No.</th>
                        <th style="width:10%;">Rev No.</th>
                        <th style="width:13%;">Effective Date</th>
                        <th style="width:15%;" class="text-end">Revision Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($isoControls as $ctrl)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">{{ $ctrl->office_code }}</span>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size:13px;">{{ $ctrl->office_name }}</div>
                                        @if($ctrl->department && $ctrl->department->campus)
                                            <div class="text-muted extra-small">{{ $ctrl->department->campus->campus_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono" style="font-size:11px;">{{ $ctrl->document_type }}</span>
                                @if($ctrl->notes)
                                    <div class="text-muted extra-small text-truncate" style="max-width:200px;" title="{{ $ctrl->notes }}">
                                        <i class="mdi mdi-information-outline me-0.5"></i>{{ $ctrl->notes }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="iso-doc-badge">{{ $ctrl->doc_no }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-mono fw-bold">Rev {{ $ctrl->rev_no }}</span>
                            </td>
                            <td>
                                <span class="font-mono text-dark fw-semibold" style="font-size:12px;">
                                    {{ $ctrl->effective_date ? $ctrl->effective_date->format('M d, Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary fw-bold"
                                            onclick="openIncrementModal({{ $ctrl->id }}, '{{ $ctrl->rev_no }}', '{{ $ctrl->doc_no }}', '{{ $ctrl->office_code }}')"
                                            title="Increment Revision">
                                        <i class="mdi mdi-plus-box me-0.5"></i>+ Revision
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="editIsoControl({{ json_encode($ctrl) }})"
                                            title="Edit Control">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                            onclick="deleteIsoControl({{ $ctrl->id }}, '{{ $ctrl->doc_no }}')"
                                            title="Delete Rule">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="mdi mdi-file-document-outline fs-3 d-block mb-1 text-secondary"></i>
                                <span>No ISO Document Controls registered yet. Click "Register ISO Form Rule" above to create one.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal 1: Register / Edit ISO Document Control --}}
<div class="modal fade" id="isoControlModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #f0f9ff; border-left: 4px solid #2563eb;">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:34px; height:34px; background: #eff6ff; border: 1px solid #bfdbfe;">
                            <i class="mdi mdi-check-decagram" style="color: #2563eb; font-size: 18px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0 font-mono" id="isoModalTitle">Register ISO Form Rule</h6>
                            <div class="extra-small text-muted">ISO 9001:2015 QMS Document Control Matrix — Office &amp; Document Level</div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="iso_id">
                <div id="iso_status_alert" class="alert alert-light border py-2 px-3 extra-small mb-4 text-muted d-flex align-items-center gap-2">
                    <i class="mdi mdi-information-outline fs-5 text-primary"></i>
                    <div>Select an originating office from <strong>DTS Units</strong> to fetch existing ISO document control specifications or register a new one.</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Originating Office / Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="iso_dept_id" data-no-select2="true" onchange="syncIsoControlDetails()" style="cursor:pointer;">
                            <option value="">-- Select Office / Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" data-code="{{ $dept->unit_code }}" data-name="{{ $dept->unit_name }}">
                                    [{{ $dept->unit_code ?: 'OFFICE' }}] {{ $dept->unit_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Office Code <span class="badge bg-light text-muted border py-0 px-1 font-monospace" style="font-size:9px;">DTS</span></label>
                        <input type="text" class="form-control font-mono fw-bold bg-light text-center" id="iso_office_code" placeholder="OP" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Document Instrument <span class="text-danger">*</span></label>
                        <select class="form-select" id="iso_doc_type" data-no-select2="true" onchange="syncIsoControlDetails()" style="cursor:pointer;">
                            <option value="University Memorandum">University Memorandum</option>
                            <option value="Institutional Policy Code">Institutional Policy Code</option>
                            <option value="Administrative Order">Administrative Order</option>
                            <option value="Special Order">Special Order</option>
                            <option value="Board Resolution">Board Resolution</option>
                            <option value="Implementing Rules &amp; Regulations">Implementing Rules &amp; Regulations</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Office Title <span class="badge bg-light text-muted border py-0 px-1 font-monospace" style="font-size:9px;">DTS Unit</span></label>
                        <input type="text" class="form-control bg-light" id="iso_office_name" placeholder="Office / Department name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">ISO Document No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-mono fw-bold" id="iso_doc_no" placeholder="e.g. FM-{{ $institution->school_acronym ?: 'ADSSU' }}-OP-01-01">
                        <div class="extra-small text-muted mt-1">Standard: <code>FM-[SCHOOL]-[UNIT]-XX-XX</code></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Revision No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-mono text-center fw-bold" id="iso_rev_no" value="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Effectivity Date</label>
                        <input type="date" class="form-control font-mono" id="iso_effective_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Revision / Audit Notes</label>
                        <textarea class="form-control" id="iso_notes" rows="2" placeholder="e.g. Revision 3 approved pursuant to 2026 ISO 9001:2015 QA audit."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="isoSaveBtn" onclick="saveIsoControl()">
                    <i class="mdi mdi-content-save me-1"></i>Save ISO Rule
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Quick Increment Revision --}}
<div class="modal fade" id="incrementRevisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom px-4 py-3" style="background: #eff6ff; border-left: 4px solid #2563eb;">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:34px; height:34px; background: #dbeafe; border: 1px solid #bfdbfe;">
                            <i class="mdi mdi-plus-box-multiple-outline" style="color: #1d4ed8; font-size: 18px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0 font-mono">Increment Document Revision</h6>
                            <div class="extra-small text-muted">Apply a new QMS revision number and effective date</div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="inc_id">
                <div class="p-3 rounded-3 mb-4 text-center" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
                    <div class="extra-small text-muted text-uppercase fw-bold font-mono mb-1">Controlled Document</div>
                    <div class="h4 fw-bold font-mono text-dark mb-1" id="inc_doc_no">F-HUM-002</div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono" id="inc_office_code">OFFICE: HUM</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Current Revision</label>
                        <input type="text" class="form-control font-mono bg-light text-center fw-bold fs-5" id="inc_current_rev" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-uppercase" style="color: #1d4ed8;">New Revision No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-mono fw-bold text-center fs-5" id="inc_new_rev" style="border-color: #2563eb; color: #1d4ed8;">
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">New Effectivity Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-mono" id="inc_effective_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Reason for Revision / QA Audit Notes</label>
                        <textarea class="form-control" id="inc_notes" rows="3" placeholder="e.g. Periodic QA review, updated legal basis, and reformatted provisions."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="submitIncrementRevision()">
                    <i class="mdi mdi-check-circle me-1"></i>Apply Revision
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const allIsoControls = @json($isoControls);
    const defaultSchoolCode = '{{ $institution->school_acronym ?: "ADSSU" }}';
    const isoBaseUrl = "{{ route('dss.settings.institution.iso_controls.store') }}";

    function syncIsoControlDetails() {
        const deptSelect = document.getElementById('iso_dept_id');
        if (!deptSelect) return;
        const deptId = deptSelect.value;
        const docType = document.getElementById('iso_doc_type')?.value || 'University Memorandum';
        const alertEl = document.getElementById('iso_status_alert');
        const saveBtn = document.getElementById('isoSaveBtn');
        const modalTitle = document.getElementById('isoModalTitle');

        if (!deptId) {
            document.getElementById('iso_id').value = '';
            document.getElementById('iso_office_code').value = '';
            document.getElementById('iso_office_name').value = '';
            document.getElementById('iso_doc_no').value = `FM-${defaultSchoolCode}-`;
            document.getElementById('iso_rev_no').value = '0';
            document.getElementById('iso_effective_date').value = '{{ date("Y-m-d") }}';
            document.getElementById('iso_notes').value = '';
            if (alertEl) {
                alertEl.className = 'alert alert-light border py-2 px-3 extra-small mb-3 text-muted d-flex align-items-center gap-2';
                alertEl.innerHTML = '<i class="mdi mdi-information-outline fs-5 text-primary"></i><div>Select an originating office from <strong>DTS Units</strong> to fetch existing ISO document control specifications or register a new one.</div>';
            }
            if (modalTitle) modalTitle.innerHTML = '<i class="mdi mdi-check-decagram text-primary me-1"></i>Register ISO Form Rule';
            if (saveBtn) saveBtn.innerHTML = '<i class="mdi mdi-content-save me-1"></i>Save ISO Rule';
            return;
        }

        const opt = deptSelect.options[deptSelect.selectedIndex];
        const code = opt ? (opt.dataset.code || '') : '';
        const name = opt ? (opt.dataset.name || '') : '';
        document.getElementById('iso_office_code').value = code;
        document.getElementById('iso_office_name').value = name;

        const match = allIsoControls.find(c => String(c.department_id) === String(deptId) && c.document_type === docType);
        if (match) {
            document.getElementById('iso_id').value = match.id;
            document.getElementById('iso_doc_no').value = match.doc_no || `FM-${defaultSchoolCode}-${code}-01-01`;
            document.getElementById('iso_rev_no').value = match.rev_no !== null && match.rev_no !== undefined ? match.rev_no : '0';
            document.getElementById('iso_effective_date').value = match.effective_date ? match.effective_date.split('T')[0] : '{{ date("Y-m-d") }}';
            document.getElementById('iso_notes').value = match.notes || '';
            if (alertEl) {
                alertEl.className = 'alert alert-info py-2 px-3 extra-small mb-3 d-flex align-items-center gap-2';
                alertEl.innerHTML = `<i class="mdi mdi-check-circle text-info fs-5"></i><div><strong>Registered Control Found:</strong> Loaded <code>${match.doc_no}</code> (Rev ${match.rev_no}) for <strong>${name}</strong>. Edits will update this rule.</div>`;
            }
            if (modalTitle) modalTitle.innerHTML = `<i class="mdi mdi-pencil text-primary me-1"></i>Edit ISO Form Rule (${match.doc_no})`;
            if (saveBtn) saveBtn.innerHTML = '<i class="mdi mdi-content-save-edit me-1"></i>Update ISO Rule';
        } else {
            document.getElementById('iso_id').value = '';
            document.getElementById('iso_doc_no').value = `FM-${defaultSchoolCode}-${code || 'UNIT'}-01-01`;
            document.getElementById('iso_rev_no').value = '0';
            document.getElementById('iso_effective_date').value = '{{ date("Y-m-d") }}';
            document.getElementById('iso_notes').value = '';
            if (alertEl) {
                alertEl.className = 'alert alert-warning py-2 px-3 extra-small mb-3 d-flex align-items-center gap-2';
                alertEl.innerHTML = `<i class="mdi mdi-plus-circle-outline text-warning fs-5"></i><div><strong>No Rule Found:</strong> No existing control for <strong>${name}</strong> (${docType}). Suggested: <code>FM-${defaultSchoolCode}-${code || 'UNIT'}-01-01</code>.</div>`;
            }
            if (modalTitle) modalTitle.innerHTML = '<i class="mdi mdi-check-decagram text-primary me-1"></i>Register ISO Form Rule';
            if (saveBtn) saveBtn.innerHTML = '<i class="mdi mdi-plus-circle me-1"></i>Save ISO Rule';
        }
    }

    function openIsoModal() {
        document.getElementById('iso_id').value = '';
        const deptSelect = document.getElementById('iso_dept_id');
        if (deptSelect) deptSelect.value = '';
        const docSelect = document.getElementById('iso_doc_type');
        if (docSelect) docSelect.value = 'University Memorandum';
        syncIsoControlDetails();
        const modalEl = document.getElementById('isoControlModal');
        let modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl, { focus: false });
        modalInstance.show();
    }

    function editIsoControl(ctrl) {
        const deptSelect = document.getElementById('iso_dept_id');
        if (deptSelect) deptSelect.value = ctrl.department_id || '';
        const docSelect = document.getElementById('iso_doc_type');
        if (docSelect) docSelect.value = ctrl.document_type;
        syncIsoControlDetails();
        document.getElementById('iso_id').value = ctrl.id;
        document.getElementById('iso_doc_no').value = ctrl.doc_no;
        document.getElementById('iso_rev_no').value = ctrl.rev_no;
        document.getElementById('iso_effective_date').value = ctrl.effective_date ? ctrl.effective_date.split('T')[0] : '{{ date("Y-m-d") }}';
        document.getElementById('iso_notes').value = ctrl.notes || '';
        const modalEl = document.getElementById('isoControlModal');
        let modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl, { focus: false });
        modalInstance.show();
    }

    function saveIsoControl() {
        const id = document.getElementById('iso_id').value;
        const deptId = document.getElementById('iso_dept_id').value;
        if (!deptId) {
            Swal.fire({ icon: 'warning', title: 'Originating Office Required', text: 'Please select an Originating Office / Unit.' });
            return;
        }
        const payload = {
            department_id: deptId,
            office_code: document.getElementById('iso_office_code').value,
            office_name: document.getElementById('iso_office_name').value,
            document_type: document.getElementById('iso_doc_type').value,
            doc_no: document.getElementById('iso_doc_no').value,
            rev_no: document.getElementById('iso_rev_no').value,
            effective_date: document.getElementById('iso_effective_date').value,
            notes: document.getElementById('iso_notes').value,
        };
        if (!payload.doc_no) {
            Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please specify the ISO Document Number.' });
            return;
        }
        const url = id ? `${isoBaseUrl}/${id}` : isoBaseUrl;
        const method = id ? 'PUT' : 'POST';
        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        })
        .then(async r => {
            const d = await r.json().catch(() => ({ result: false, message: 'Server error: ' + r.statusText }));
            if (!r.ok || !d.result) {
                Swal.fire({ icon: 'error', title: 'Error', html: d.message || 'Failed to save ISO rule.' });
                return;
            }
            Swal.fire({ icon: 'success', title: 'Saved!', text: d.message, confirmButtonColor: 'var(--dss-primary)', timer: 1600, showConfirmButton: false })
                .then(() => location.reload());
        })
        .catch(err => Swal.fire({ icon: 'error', title: 'Network Error', text: err.message || 'Could not connect to server.' }));
    }

    function openIncrementModal(id, currentRev, docNo, officeCode) {
        document.getElementById('inc_id').value = id;
        document.getElementById('inc_doc_no').textContent = docNo;
        document.getElementById('inc_office_code').textContent = 'OFFICE: ' + officeCode;
        document.getElementById('inc_current_rev').value = 'Rev ' + currentRev;
        const currentNum = parseInt(currentRev, 10);
        document.getElementById('inc_new_rev').value = !isNaN(currentNum) ? (currentNum + 1) : (currentRev + '.1');
        document.getElementById('inc_effective_date').value = '{{ date("Y-m-d") }}';
        document.getElementById('inc_notes').value = '';
        new bootstrap.Modal(document.getElementById('incrementRevisionModal')).show();
    }

    function submitIncrementRevision() {
        const id = document.getElementById('inc_id').value;
        const payload = {
            new_rev_no: document.getElementById('inc_new_rev').value,
            effective_date: document.getElementById('inc_effective_date').value,
            notes: document.getElementById('inc_notes').value,
        };
        if (!payload.new_rev_no || !payload.effective_date) {
            Swal.fire({ icon: 'warning', title: 'Fields Required', text: 'Please specify new revision number and effective date.' });
            return;
        }
        fetch(`${isoBaseUrl}/${id}/increment-revision`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        })
        .then(async r => {
            const d = await r.json().catch(() => ({ result: false, message: 'Server error: ' + r.statusText }));
            if (!r.ok || !d.result) {
                Swal.fire({ icon: 'error', title: 'Error', html: d.message || 'Failed to apply revision.' });
                return;
            }
            Swal.fire({ icon: 'success', title: 'Revision Updated!', text: d.message, confirmButtonColor: 'var(--dss-primary)', timer: 1600, showConfirmButton: false })
                .then(() => location.reload());
        })
        .catch(err => Swal.fire({ icon: 'error', title: 'Network Error', text: err.message || 'Could not connect to server.' }));
    }

    function deleteIsoControl(id, docNo) {
        Swal.fire({
            title: `Delete Rule for ${docNo}?`,
            text: 'This will remove this specific ISO Document Control configuration.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${isoBaseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                })
                .then(async r => {
                    const d = await r.json().catch(() => ({ result: false, message: 'Server error: ' + r.statusText }));
                    if (!r.ok || !d.result) {
                        Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to delete.' });
                        return;
                    }
                    Swal.fire({ icon: 'success', title: 'Deleted', text: d.message, timer: 1400, showConfirmButton: false })
                        .then(() => location.reload());
                })
                .catch(err => Swal.fire({ icon: 'error', title: 'Network Error', text: err.message || 'Could not connect to server.' }));
            }
        });
    }
</script>
@endsection
