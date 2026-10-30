@props([
    'model' => null,
    'type' => null,
    'category' => 'physically_signed_plan',
    'allowPhysicalSign' => true,
    'title' => 'Upload Physically Signed Document / Attachments',
    'uploaderId' => 'docUploader_' . Str::random(8),
])

@php
    $modelType = $type ?: ($model ? get_class($model) : null);
    $modelId = $model ? $model->getKey() : null;
@endphp

<div id="{{ $uploaderId }}" class="centralized-uploader-wrapper">
    <div class="card border-0 shadow-2xs rounded-3 overflow-hidden mb-4" style="background: #ffffff; border: 1.5px dashed #cbd5e1 !important;">
        <div class="card-body p-4">
            <form id="{{ $uploaderId }}_form" action="{{ route('dss.documents.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="documentable_type" value="{{ $modelType }}">
                <input type="hidden" name="documentable_id" value="{{ $modelId }}">

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="uploader-dropzone rounded-3 p-4 text-center d-flex flex-column align-items-center justify-content-center h-100"
                             id="{{ $uploaderId }}_dropzone"
                             style="background: #f8fafc; border: 2px dashed #94a3b8; cursor: pointer; min-height: 180px; transition: all 0.2s ease;">
                            <input type="file" name="file" id="{{ $uploaderId }}_fileInput" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.png,.jpg,.jpeg,.webp,.zip">
                            <div class="rounded-circle bg-white p-3 shadow-2xs mb-2 text-primary" style="width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="mdi mdi-cloud-upload-outline" style="font-size: 28px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Click to browse or drag &amp; drop file here</h6>
                            <p class="text-muted extra-small mb-2">
                                Accepts <strong>PDF, Word, Excel, JPG, PNG, ZIP</strong> up to <strong>50MB</strong>
                            </p>
                            <div id="{{ $uploaderId }}_filePreview" class="d-none badge bg-primary-subtle text-primary border border-primary-subtle py-1.5 px-3 font-mono extra-small">
                                <i class="mdi mdi-file-check me-1"></i> <span class="file-name-text"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        @if($allowPhysicalSign)
                            <div class="p-3 rounded-3 mb-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" role="switch" id="{{ $uploaderId }}_physicallySignedToggle" name="is_physically_signed" value="1" {{ $category === 'physically_signed_plan' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger extra-small" for="{{ $uploaderId }}_physicallySignedToggle">
                                        <i class="mdi mdi-draw-pen me-1"></i> Physically / Wet-Signed Document Scan
                                    </label>
                                </div>
                                <p class="text-muted extra-small m-0" style="font-size: 11px;">
                                    Marking this as a physically signed copy will tag this file as the official endorsed wet-ink scan for audit trails.
                                </p>
                            </div>
                        @endif

                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Document Category</label>
                                <select name="category" id="{{ $uploaderId }}_category" class="form-select form-select-sm">
                                    <option value="general" {{ $category === 'general' || empty($category) ? 'selected' : '' }}>General Document / Attachment</option>
                                    <option value="physically_signed_plan" {{ $category === 'physically_signed_plan' ? 'selected' : '' }}>Physically Signed Plan</option>
                                    <option value="activity_design">Activity Design Proposal</option>
                                    <option value="moa_mou">MOA / MOU Copy</option>
                                    <option value="budget_proposal">Budget Proposal Breakdown</option>
                                    <option value="narrative_report">Narrative Report</option>
                                    <option value="attendance_sheet">Attendance Sheet</option>
                                    <option value="annex">Annex / Attachment</option>
                                    <option value="supporting_evidence">Supporting Verification Evidence</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Signed / Effective Date</label>
                                <input type="date" name="signed_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Document Title (Optional)</label>
                                <input type="text" name="title" class="form-control form-control-sm" placeholder="Leave empty to use original file name">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Signatory Remarks</label>
                                <input type="text" name="signatory_notes" class="form-control form-control-sm" placeholder="e.g. Signed by Dean &amp; VPAA">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">
                                    Revision / Changelog <span class="badge bg-light text-secondary">v1.1+</span>
                                </label>
                                <input type="text" name="change_notes" class="form-control form-control-sm" placeholder="e.g. Revised page 3 signatures">
                            </div>
                        </div>

                        <div id="{{ $uploaderId }}_progressWrapper" class="d-none mt-3">
                            <div class="progress" style="height: 6px;">
                                <div id="{{ $uploaderId }}_progressBar" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="extra-small text-muted text-end mt-1 progress-text">Uploading: 0%</div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" id="{{ $uploaderId }}_submitBtn" class="btn btn-sm btn-dss-primary px-4 fw-bold shadow-2xs rounded-pill" disabled>
                                <i class="mdi mdi-upload me-1"></i> Upload &amp; Attach File
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const uploaderId = '{{ $uploaderId }}';
    const form = document.getElementById(uploaderId + '_form');
    const dropzone = document.getElementById(uploaderId + '_dropzone');
    const fileInput = document.getElementById(uploaderId + '_fileInput');
    const filePreview = document.getElementById(uploaderId + '_filePreview');
    const submitBtn = document.getElementById(uploaderId + '_submitBtn');
    const progressWrapper = document.getElementById(uploaderId + '_progressWrapper');
    const progressBar = document.getElementById(uploaderId + '_progressBar');
    const physicallySignedToggle = document.getElementById(uploaderId + '_physicallySignedToggle');
    const categorySelect = document.getElementById(uploaderId + '_category');

    // Clicking dropzone triggers file input
    dropzone.addEventListener('click', (e) => {
        if (e.target !== fileInput) {
            fileInput.click();
        }
    });

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = 'var(--dss-primary, #002b49)';
            dropzone.style.backgroundColor = '#f1f5f9';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = '#94a3b8';
            dropzone.style.backgroundColor = '#f8fafc';
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelected(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileSelected(fileInput.files[0]);
        }
    });

    function handleFileSelected(file) {
        if (!file) return;
        filePreview.classList.remove('d-none');
        filePreview.querySelector('.file-name-text').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
        submitBtn.disabled = false;
        dropzone.style.borderColor = '#10b981';
    }

    if (physicallySignedToggle) {
        physicallySignedToggle.addEventListener('change', () => {
            if (physicallySignedToggle.checked) {
                categorySelect.value = 'physically_signed_plan';
            }
        });
    }

    // Handle AJAX form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please select a file to upload.');
            return;
        }

        const formData = new FormData(form);
        submitBtn.disabled = true;
        progressWrapper.classList.remove('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressBar.textContent = percent + '%';
                progressWrapper.querySelector('.progress-text').textContent = 'Uploading: ' + percent + '%';
            }
        });

        xhr.onload = function() {
            submitBtn.disabled = false;
            progressWrapper.classList.add('d-none');

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.success) {
                        if (window.dssToast) {
                            window.dssToast.show({
                                title: 'Upload Successful',
                                message: resp.message || 'File uploaded successfully.',
                                icon: 'success'
                            });
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Uploaded!',
                                text: resp.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }

                        // Reset form
                        form.reset();
                        filePreview.classList.add('d-none');
                        dropzone.style.borderColor = '#94a3b8';
                        submitBtn.disabled = true;

                        // Close parent modal if opened in a modal
                        const parentModal = form.closest('.modal');
                        if (parentModal && typeof bootstrap !== 'undefined') {
                            const modalInstance = bootstrap.Modal.getInstance(parentModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }

                        // Trigger custom event or reload attachments
                        document.dispatchEvent(new CustomEvent('dss:document-uploaded', { detail: resp.document }));

                        // Reload page or refresh attachments table if reload flag
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: resp.is_duplicate ? 'warning' : 'error',
                                title: resp.is_duplicate ? 'Duplicate Document Detected' : 'Upload Failed',
                                text: resp.message || 'Unknown error occurred.',
                                confirmButtonColor: '#0f172a'
                            });
                        } else {
                            alert('Upload failed: ' + (resp.message || 'Unknown error.'));
                        }
                    }
                } catch (err) {
                    window.location.reload();
                }
            } else {
                let errorMsg = 'Error during upload.';
                let isDuplicate = false;
                try {
                    const errResp = JSON.parse(xhr.responseText);
                    errorMsg = errResp.message || errorMsg;
                    isDuplicate = !!errResp.is_duplicate;
                } catch(e) {}

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: isDuplicate ? 'warning' : 'error',
                        title: isDuplicate ? 'Duplicate Document Detected' : 'Upload Error',
                        text: errorMsg,
                        confirmButtonColor: '#0f172a'
                    });
                } else {
                    alert(errorMsg);
                }
            }
        };

        xhr.onerror = function() {
            submitBtn.disabled = false;
            progressWrapper.classList.add('d-none');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'A network error occurred during upload. Please check your connection.',
                    confirmButtonColor: '#0f172a'
                });
            } else {
                alert('Network error occurred during upload.');
            }
        };

        xhr.send(formData);
    });
})();
</script>
@endpush
