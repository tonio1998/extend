@props([
    'model' => null,
    'type' => null,
    'category' => 'general',
    'allowPhysicalSign' => true,
    'title' => 'Upload Institutional Documents / Attachments',
    'uploaderId' => 'docUploader_' . Str::random(8),
])

@php
    $modelType = $type ?: ($model ? get_class($model) : null);
    $modelId = $model ? $model->getKey() : null;
    $cleanId = preg_replace('/[^a-zA-Z0-9_]/', '_', $uploaderId);
@endphp

<div id="{{ $uploaderId }}" class="centralized-uploader-wrapper">
    <div class="canva-card p-4 border shadow-2xs bg-white rounded-3">
        
        <!-- ─────────────────────────────────────────────────────────────
             1. METHOD SEGMENTED CONTROLLER (PDF vs Google Drive vs Both)
             ───────────────────────────────────────────────────────────── -->
        <div class="d-flex align-items-center justify-content-between mb-3.5 pb-2.5 border-bottom flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted extra-small fw-bold font-mono text-uppercase">Method:</span>
                <div class="btn-group btn-group-sm p-1 bg-light border rounded-pill" role="tablist">
                    <button type="button" 
                            class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold tab-nav-btn active bg-white shadow-2xs text-dark" 
                            id="{{ $uploaderId }}_tabBtnPdf" 
                            onclick="switchUploaderTab_{{ $cleanId }}('pdf')">
                        <i class="mdi mdi-file-pdf-box text-danger me-1"></i> Upload PDF File(s)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill font-mono extra-small ms-1 d-none" id="{{ $uploaderId }}_tabBadgePdf">0</span>
                    </button>
                    <button type="button" 
                            class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold tab-nav-btn text-secondary" 
                            id="{{ $uploaderId }}_tabBtnGdrive" 
                            onclick="switchUploaderTab_{{ $cleanId }}('gdrive')">
                        <i class="mdi mdi-google-drive text-success me-1"></i> Google Drive Link(s)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono extra-small ms-1 d-none" id="{{ $uploaderId }}_tabBadgeGdrive">0</span>
                    </button>
                    <button type="button" 
                            class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold tab-nav-btn text-secondary" 
                            id="{{ $uploaderId }}_tabBtnBoth" 
                            onclick="switchUploaderTab_{{ $cleanId }}('both')">
                        <i class="mdi mdi-view-split-vertical text-primary me-1"></i> Both (Split View)
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border font-mono extra-small">
                    <i class="mdi mdi-shield-check text-success me-1"></i> SHA-256 Vault Verified
                </span>
            </div>
        </div>

        <!-- ─────────────────────────────────────────────────────────────
             UNIFIED FORM: PROCESSES PDFS & LINKS TOGETHER
             ───────────────────────────────────────────────────────────── -->
        <form id="{{ $uploaderId }}_form" action="{{ route('dss.documents.upload.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="documentable_type" value="{{ $modelType }}">
            <input type="hidden" name="documentable_id" value="{{ $modelId }}">

            <!-- Split / Switchable Row Container -->
            <div class="row g-3 mb-3" id="{{ $uploaderId }}_viewsRow">
                
                <!-- ── VIEW 1: PDF DROPZONE & QUEUE LIST ── -->
                <div class="col-12" id="{{ $uploaderId }}_colPdf">
                    <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label extra-small fw-bold text-dark font-mono m-0">
                                    <i class="mdi mdi-file-pdf-box text-danger me-1"></i> Upload PDF Document(s)
                                </label>
                                <span class="badge bg-white text-secondary border font-mono extra-small">Max 50MB per file</span>
                            </div>

                            <!-- Dropzone Area -->
                            <div class="uploader-dropzone rounded-3 p-3 text-center d-flex flex-column align-items-center justify-content-center position-relative"
                                 id="{{ $uploaderId }}_dropzone"
                                 style="background: #ffffff; border: 2px dashed #94a3b8; cursor: pointer; min-height: 130px; transition: all 0.2s ease;">
                                <input type="file" id="{{ $uploaderId }}_fileInput" class="d-none" multiple accept=".pdf,application/pdf">
                                <div class="rounded-circle bg-white p-2.5 shadow-2xs mb-1.5 text-danger d-inline-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                    <i class="mdi mdi-file-pdf-box fs-3"></i>
                                </div>
                                <div class="fw-bold text-dark extra-small mb-0.5">Click to browse or drag &amp; drop PDF files</div>
                                <div class="text-muted extra-small">
                                    <strong class="text-danger">PDF format only (.pdf)</strong> &bull; Multiple files supported
                                </div>
                            </div>

                            <!-- Selected PDF Files Queue (Naka-lista sa ibaba bago i-upload) -->
                            <div id="{{ $uploaderId }}_queueWrapper" class="d-none mt-3">
                                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="extra-small fw-bold text-uppercase font-mono text-dark">
                                            <i class="mdi mdi-format-list-bulleted text-primary me-1"></i> Selected PDF Queue (<span id="{{ $uploaderId }}_queueCount">0</span>)
                                        </span>
                                        <span class="badge bg-white text-muted border font-mono extra-small" id="{{ $uploaderId }}_queueTotalSize">0 MB</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 extra-small fw-semibold" onclick="document.getElementById('{{ $uploaderId }}_fileInput').click();">
                                            <i class="mdi mdi-plus me-0.5"></i> Add More
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 extra-small fw-semibold" onclick="clearQueue_{{ $cleanId }}()">
                                            <i class="mdi mdi-trash-can-outline me-0.5"></i> Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Dynamic Queue Container with Real-time Progress Bar each file -->
                                <div id="{{ $uploaderId }}_queueList" class="d-flex flex-column gap-2" style="max-height: 240px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── VIEW 2: GOOGLE DRIVE SHAREABLE LINKS ── -->
                <div class="col-12 d-none" id="{{ $uploaderId }}_colGdrive">
                    <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label extra-small fw-bold text-dark font-mono m-0">
                                    <i class="mdi mdi-google-drive text-success me-1"></i> Google Drive Shareable Link(s)
                                </label>
                                <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-3 py-1 extra-small fw-bold" id="{{ $uploaderId }}_btnAddLink" onclick="addGdriveRow_{{ $cleanId }}()">
                                    <i class="mdi mdi-plus me-0.5"></i> Add Link
                                </button>
                            </div>

                            <!-- Dynamic Link Rows Container -->
                            <div id="{{ $uploaderId }}_gdriveRowsWrapper" class="d-flex flex-column gap-2 mb-2.5">
                                <div class="input-group input-group-sm gdrive-entry-row">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
                                    <input type="url" name="gdrive_urls[]" class="form-control gdrive-url-input" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" oninput="updateAllCounts_{{ $cleanId }}()">
                                </div>
                            </div>

                            <!-- Sharing Guidance Callout -->
                            <div class="p-2.5 rounded-2 extra-small text-muted d-flex align-items-center gap-2" style="background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.2);">
                                <i class="mdi mdi-information-outline text-primary fs-5 flex-shrink-0"></i>
                                <span>
                                    Ensure link sharing in Google Drive is set to <strong>"Anyone with the link can view"</strong> for institutional review.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Physically Signed Scan Toggle (if enabled) -->
            @if($allowPhysicalSign)
                <div class="p-2.5 rounded-3 mb-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                    <div class="form-check form-switch mb-0.5">
                        <input class="form-check-input" type="checkbox" role="switch" id="{{ $uploaderId }}_physicallySignedToggle" name="is_physically_signed" value="1" {{ $category === 'physically_signed_plan' ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-danger extra-small" for="{{ $uploaderId }}_physicallySignedToggle">
                            <i class="mdi mdi-draw-pen me-1"></i> Physically / Wet-Signed Document Scan
                        </label>
                    </div>
                    <div class="text-muted extra-small ms-4">
                        Marking this tags the uploaded file / attached link as the official endorsed wet-ink scan for audit trails.
                    </div>
                </div>
            @endif

            <!-- Metadata Inputs: Category, Custom Title, Notes -->
            <div class="p-3 rounded-3 bg-light border mb-3">
                <div class="row g-2.5">
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-muted font-mono mb-1">Document Category</label>
                        <select name="category" id="{{ $uploaderId }}_category" class="form-select form-select-sm">
                            <option value="general" {{ $category === 'general' || empty($category) ? 'selected' : '' }}>General Document / Attachment</option>
                            <option value="wet_signed_milestone" {{ $category === 'wet_signed_milestone' ? 'selected' : '' }}>Wet-Signed Milestone Evidence</option>
                            <option value="milestone_evidence" {{ $category === 'milestone_evidence' ? 'selected' : '' }}>Milestone Supporting Evidence</option>
                            <option value="physically_signed_plan" {{ $category === 'physically_signed_plan' ? 'selected' : '' }}>Physically Signed Plan</option>
                            <option value="activity_design" {{ $category === 'activity_design' ? 'selected' : '' }}>Activity Design Proposal</option>
                            <option value="moa_mou" {{ $category === 'moa_mou' ? 'selected' : '' }}>MOA / MOU Copy</option>
                            <option value="budget_proposal" {{ $category === 'budget_proposal' ? 'selected' : '' }}>Budget Proposal Breakdown</option>
                            <option value="narrative_report" {{ $category === 'narrative_report' ? 'selected' : '' }}>Narrative Report</option>
                            <option value="attendance_sheet" {{ $category === 'attendance_sheet' ? 'selected' : '' }}>Attendance Sheet</option>
                            <option value="annex" {{ $category === 'annex' ? 'selected' : '' }}>Annex / Attachment</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-muted font-mono mb-1">Custom Title / Label <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="title" id="{{ $uploaderId }}_title" class="form-control form-control-sm" placeholder="e.g. Official Approved Activity Design">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label extra-small fw-bold text-muted font-mono mb-1">Notes / Remarks <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="signatory_notes" id="{{ $uploaderId }}_notes" class="form-control form-control-sm" placeholder="e.g. Endorsed with wet ink signatures">
                    </div>
                </div>
            </div>

            <!-- Unified Actions Footer -->
            <div class="d-flex align-items-center justify-content-between pt-1">
                <span class="text-muted extra-small">
                    <i class="mdi mdi-shield-check text-success me-1"></i> SHA-256 Checksum computed automatically on upload.
                </span>
                <button type="submit" id="{{ $uploaderId }}_submitBtn" class="btn btn-sm btn-dss-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-1.5 shadow-xs">
                    <i class="mdi mdi-cloud-upload"></i>
                    <span id="{{ $uploaderId }}_submitBtnLabel">Upload Documents</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
(function() {
    const uploaderId = '{{ $uploaderId }}';
    const cleanId = '{{ $cleanId }}';

    // State for selected PDF files
    let selectedFiles = [];

    // Elements
    const form = document.getElementById(uploaderId + '_form');
    const colPdf = document.getElementById(uploaderId + '_colPdf');
    const colGdrive = document.getElementById(uploaderId + '_colGdrive');
    const tabBtnPdf = document.getElementById(uploaderId + '_tabBtnPdf');
    const tabBtnGdrive = document.getElementById(uploaderId + '_tabBtnGdrive');
    const tabBtnBoth = document.getElementById(uploaderId + '_tabBtnBoth');
    const tabBadgePdf = document.getElementById(uploaderId + '_tabBadgePdf');
    const tabBadgeGdrive = document.getElementById(uploaderId + '_tabBadgeGdrive');

    const dropzone = document.getElementById(uploaderId + '_dropzone');
    const fileInput = document.getElementById(uploaderId + '_fileInput');
    const queueWrapper = document.getElementById(uploaderId + '_queueWrapper');
    const queueList = document.getElementById(uploaderId + '_queueList');
    const queueCount = document.getElementById(uploaderId + '_queueCount');
    const queueTotalSize = document.getElementById(uploaderId + '_queueTotalSize');

    const gdriveRowsWrapper = document.getElementById(uploaderId + '_gdriveRowsWrapper');
    const submitBtn = document.getElementById(uploaderId + '_submitBtn');
    const submitBtnLabel = document.getElementById(uploaderId + '_submitBtnLabel');

    const physicallySignedToggle = document.getElementById(uploaderId + '_physicallySignedToggle');
    const categorySelect = document.getElementById(uploaderId + '_category');

    // ─────────────────────────────────────────────────────────────────────────
    // TAB SWITCHER FUNCTION (PDF vs GDrive vs Both)
    // ─────────────────────────────────────────────────────────────────────────
    window['switchUploaderTab_' + cleanId] = function(mode) {
        [tabBtnPdf, tabBtnGdrive, tabBtnBoth].forEach(btn => {
            if (btn) {
                btn.classList.remove('active', 'bg-white', 'shadow-2xs', 'text-dark');
                btn.classList.add('text-secondary');
            }
        });

        if (mode === 'pdf') {
            colPdf.className = 'col-12';
            colGdrive.className = 'col-12 d-none';
            tabBtnPdf.classList.add('active', 'bg-white', 'shadow-2xs', 'text-dark');
            tabBtnPdf.classList.remove('text-secondary');
        } else if (mode === 'gdrive') {
            colPdf.className = 'col-12 d-none';
            colGdrive.className = 'col-12';
            tabBtnGdrive.classList.add('active', 'bg-white', 'shadow-2xs', 'text-dark');
            tabBtnGdrive.classList.remove('text-secondary');

            const firstInp = colGdrive.querySelector('.gdrive-url-input');
            if (firstInp) setTimeout(() => firstInp.focus(), 100);
        } else {
            // Both / split view
            colPdf.className = 'col-lg-6';
            colGdrive.className = 'col-lg-6';
            tabBtnBoth.classList.add('active', 'bg-white', 'shadow-2xs', 'text-dark');
            tabBtnBoth.classList.remove('text-secondary');
        }
    };

    // Global Hook for External Buttons (e.g. Repository Sidebar "+ New Upload ▾")
    window.setGlobalUploadMode = function(mode) {
        if (typeof window['switchUploaderTab_' + cleanId] === 'function') {
            window['switchUploaderTab_' + cleanId](mode);
        }
    };

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE COUNTS & SUBMIT BUTTON LABEL
    // ─────────────────────────────────────────────────────────────────────────
    window['updateAllCounts_' + cleanId] = function() {
        const pdfCount = selectedFiles.length;
        let gdriveCount = 0;
        if (gdriveRowsWrapper) {
            gdriveRowsWrapper.querySelectorAll('.gdrive-url-input').forEach(inp => {
                if (inp.value.trim() !== '') gdriveCount++;
            });
        }

        // Update Tab Badges
        if (tabBadgePdf) {
            if (pdfCount > 0) {
                tabBadgePdf.textContent = pdfCount;
                tabBadgePdf.classList.remove('d-none');
            } else {
                tabBadgePdf.classList.add('d-none');
            }
        }
        if (tabBadgeGdrive) {
            if (gdriveCount > 0) {
                tabBadgeGdrive.textContent = gdriveCount;
                tabBadgeGdrive.classList.remove('d-none');
            } else {
                tabBadgeGdrive.classList.add('d-none');
            }
        }

        // Update Submit Button Label
        if (submitBtnLabel) {
            if (pdfCount > 0 && gdriveCount > 0) {
                submitBtnLabel.innerHTML = `Upload ${pdfCount} PDF &amp; Attach ${gdriveCount} Link(s)`;
            } else if (pdfCount > 0) {
                submitBtnLabel.innerHTML = `Upload ${pdfCount} PDF Document(s)`;
            } else if (gdriveCount > 0) {
                submitBtnLabel.innerHTML = `Attach ${gdriveCount} Google Drive Link(s)`;
            } else {
                submitBtnLabel.innerHTML = 'Upload Documents';
            }
        }
    };

    // ─────────────────────────────────────────────────────────────────────────
    // PDF DROPZONE & QUEUE LIST HANDLING
    // ─────────────────────────────────────────────────────────────────────────
    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#00674f';
            dropzone.style.background = '#f0fdf4';
        });

        dropzone.addEventListener('dragleave', function() {
            dropzone.style.borderColor = '#94a3b8';
            dropzone.style.background = '#ffffff';
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#94a3b8';
            dropzone.style.background = '#ffffff';
            if (e.dataTransfer && e.dataTransfer.files) {
                appendFilesToQueue(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                appendFilesToQueue(this.files);
                this.value = '';
            }
        });
    }

    function appendFilesToQueue(files) {
        const fileArr = Array.from(files);
        const nonPdfs = [];

        fileArr.forEach(file => {
            const isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
            if (!isPdf) {
                nonPdfs.push(file.name);
                return;
            }

            const alreadyExists = selectedFiles.some(f => f.file.name === file.name && f.file.size === file.size);
            if (!alreadyExists) {
                selectedFiles.push({
                    file: file,
                    status: 'ready',
                    progress: 0,
                    errorMessage: null
                });
            }
        });

        if (nonPdfs.length > 0) {
            const alertMsg = 'Only PDF documents (.pdf) are allowed. The following non-PDF file(s) were ignored: ' + nonPdfs.join(', ');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Non-PDF Files Ignored',
                    text: alertMsg,
                    confirmButtonColor: '#00674f'
                });
            } else {
                alert(alertMsg);
            }
        }

        renderQueue();
        window['updateAllCounts_' + cleanId]();
    }

    function renderQueue() {
        if (!queueWrapper || !queueList) return;

        if (selectedFiles.length === 0) {
            queueWrapper.classList.add('d-none');
            dropzone.style.borderColor = '#94a3b8';
            return;
        }

        queueWrapper.classList.remove('d-none');
        dropzone.style.borderColor = '#00674f';
        queueCount.textContent = selectedFiles.length;

        const totalBytes = selectedFiles.reduce((acc, f) => acc + f.file.size, 0);
        queueTotalSize.textContent = (totalBytes / (1024 * 1024)).toFixed(2) + ' MB';

        queueList.innerHTML = '';

        selectedFiles.forEach((item, index) => {
            const sizeStr = item.file.size > 1024 * 1024 
                ? (item.file.size / (1024 * 1024)).toFixed(2) + ' MB'
                : (item.file.size / 1024).toFixed(0) + ' KB';

            const row = document.createElement('div');
            row.className = 'p-2.5 rounded-2 bg-white border d-flex flex-column gap-1.5 shadow-2xs';
            row.id = uploaderId + '_fileItem_' + index;

            let badgeHtml = '';
            if (item.status === 'ready') {
                badgeHtml = '<span class="badge bg-light text-secondary border font-mono extra-small">Ready</span>';
            } else if (item.status === 'uploading') {
                badgeHtml = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small"><i class="mdi mdi-loading mdi-spin me-1"></i>Uploading ${item.progress}%</span>`;
            } else if (item.status === 'done') {
                badgeHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small"><i class="mdi mdi-check-circle me-1"></i>Uploaded ✓</span>';
            } else if (item.status === 'error') {
                badgeHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono extra-small" title="${item.errorMessage || 'Failed'}"><i class="mdi mdi-alert-circle me-1"></i>Failed</span>`;
            }

            const deleteBtnHtml = item.status === 'uploading' || item.status === 'done'
                ? ''
                : `<button type="button" class="btn btn-xs btn-outline-danger rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" onclick="removeQueueFile_${cleanId}(${index})" title="Remove this file"><i class="mdi mdi-close"></i></button>`;

            row.innerHTML = `
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                        <div class="rounded-2 p-1.5 bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-file-pdf-box fs-5"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark extra-small text-truncate" title="${item.file.name}">${item.file.name}</div>
                            <div class="text-muted extra-small font-mono">${sizeStr}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <div id="${uploaderId}_badge_${index}">${badgeHtml}</div>
                        ${deleteBtnHtml}
                    </div>
                </div>
                <!-- Real-time Progress Bar each file -->
                <div class="progress ${item.status === 'ready' ? 'd-none' : ''}" id="${uploaderId}_progressWrap_${index}" style="height: 5px;">
                    <div class="progress-bar ${item.status === 'done' ? 'bg-success' : 'bg-primary progress-bar-striped progress-bar-animated'}" 
                         id="${uploaderId}_progressBar_${index}" 
                         role="progressbar" 
                         style="width: ${item.progress}%"></div>
                </div>
            `;

            queueList.appendChild(row);
        });
    }

    window['removeQueueFile_' + cleanId] = function(index) {
        if (selectedFiles[index]) {
            selectedFiles.splice(index, 1);
            renderQueue();
            window['updateAllCounts_' + cleanId]();
        }
    };

    window['clearQueue_' + cleanId] = function() {
        selectedFiles = [];
        renderQueue();
        window['updateAllCounts_' + cleanId]();
    };

    // Category auto-sync on physically signed switch
    if (physicallySignedToggle && categorySelect) {
        physicallySignedToggle.addEventListener('change', function() {
            if (this.checked && categorySelect.value === 'general') {
                categorySelect.value = 'physically_signed_plan';
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GOOGLE DRIVE DYNAMIC LINK ROWS
    // ─────────────────────────────────────────────────────────────────────────
    window['addGdriveRow_' + cleanId] = function() {
        if (!gdriveRowsWrapper) return;
        const row = document.createElement('div');
        row.className = 'input-group input-group-sm gdrive-entry-row mb-2';
        row.innerHTML = `
            <span class="input-group-text bg-white"><i class="mdi mdi-google-drive text-success"></i></span>
            <input type="url" name="gdrive_urls[]" class="form-control gdrive-url-input" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" oninput="updateAllCounts_${cleanId}()">
            <button type="button" class="btn btn-outline-danger btn-remove-gdrive" onclick="removeGdriveRow_${cleanId}(this)" title="Remove link row"><i class="mdi mdi-trash-can-outline"></i></button>
        `;
        gdriveRowsWrapper.appendChild(row);

        const newInp = row.querySelector('input');
        if (newInp) newInp.focus();
        window['updateAllCounts_' + cleanId]();
    };

    window['removeGdriveRow_' + cleanId] = function(btn) {
        const row = btn.closest('.gdrive-entry-row');
        if (row) {
            row.remove();
            window['updateAllCounts_' + cleanId]();
        }
    };

    // ─────────────────────────────────────────────────────────────────────────
    // UNIFIED SUBMIT: PROCESSES BOTH PDFS (WITH LIVE PROGRESS) AND LINKS
    // ─────────────────────────────────────────────────────────────────────────
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const pendingPdfs = selectedFiles.filter(f => f.status !== 'done');
            const gdriveUrls = [];
            if (gdriveRowsWrapper) {
                gdriveRowsWrapper.querySelectorAll('.gdrive-url-input').forEach(inp => {
                    const val = inp.value.trim();
                    if (val !== '') gdriveUrls.push(val);
                });
            }

            if (selectedFiles.length === 0 && gdriveUrls.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Documents Selected',
                        text: 'Please select at least one PDF file or enter a valid Google Drive link.',
                        confirmButtonColor: '#00674f'
                    });
                } else {
                    alert('Please select at least one PDF file or enter a valid Google Drive link.');
                }
                return;
            }

            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;

            const categoryVal = categorySelect ? categorySelect.value : 'general';
            const titleVal = document.getElementById(uploaderId + '_title')?.value?.trim() || '';
            const notesVal = document.getElementById(uploaderId + '_notes')?.value?.trim() || '';
            const isPhysicallySignedVal = physicallySignedToggle && physicallySignedToggle.checked ? '1' : '0';
            const docTypeVal = form.querySelector('input[name="documentable_type"]')?.value || '';
            const docIdVal = form.querySelector('input[name="documentable_id"]')?.value || '';
            const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

            let uploadedPdfSuccess = 0;
            let uploadedPdfFail = 0;
            let gdriveSuccess = false;

            // 1. Sequentially upload each PDF with real-time per-file progress bar
            if (selectedFiles.length > 0) {
                for (let i = 0; i < selectedFiles.length; i++) {
                    const item = selectedFiles[i];
                    if (item.status === 'done') {
                        uploadedPdfSuccess++;
                        continue;
                    }

                    item.status = 'uploading';
                    submitBtn.innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i> Uploading PDF ${i + 1} of ${selectedFiles.length}...`;

                    const progWrap = document.getElementById(uploaderId + '_progressWrap_' + i);
                    const progBar = document.getElementById(uploaderId + '_progressBar_' + i);
                    const badgeEl = document.getElementById(uploaderId + '_badge_' + i);
                    if (progWrap) progWrap.classList.remove('d-none');

                    await new Promise((resolve) => {
                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('file', item.file);
                        formData.append('category', categoryVal);
                        formData.append('documentable_type', docTypeVal);
                        formData.append('documentable_id', docIdVal);
                        formData.append('is_physically_signed', isPhysicallySignedVal);
                        if (titleVal && selectedFiles.length === 1 && gdriveUrls.length === 0) {
                            formData.append('title', titleVal);
                        }
                        if (notesVal) formData.append('signatory_notes', notesVal);

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', form.action, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                        xhr.upload.onprogress = function(evt) {
                            if (evt.lengthComputable) {
                                const percent = Math.round((evt.loaded / evt.total) * 100);
                                item.progress = percent;
                                if (progBar) progBar.style.width = percent + '%';
                                if (badgeEl) {
                                    badgeEl.innerHTML = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small"><i class="mdi mdi-loading mdi-spin me-1"></i>Uploading ${percent}%</span>`;
                                }
                            }
                        };

                        xhr.onload = function() {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                try {
                                    const resp = JSON.parse(xhr.responseText);
                                    if (resp.success) {
                                        item.status = 'done';
                                        item.progress = 100;
                                        if (progBar) {
                                            progBar.style.width = '100%';
                                            progBar.className = 'progress-bar bg-success';
                                        }
                                        if (badgeEl) {
                                            badgeEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small"><i class="mdi mdi-check-circle me-1"></i>Uploaded ✓</span>';
                                        }
                                        uploadedPdfSuccess++;
                                        resolve(true);
                                        return;
                                    }
                                } catch(e) {}
                            }
                            item.status = 'error';
                            uploadedPdfFail++;
                            if (progBar) progBar.className = 'progress-bar bg-danger';
                            if (badgeEl) {
                                badgeEl.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono extra-small"><i class="mdi mdi-alert-circle me-1"></i>Failed</span>';
                            }
                            resolve(false);
                        };

                        xhr.onerror = function() {
                            item.status = 'error';
                            uploadedPdfFail++;
                            if (progBar) progBar.className = 'progress-bar bg-danger';
                            if (badgeEl) {
                                badgeEl.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono extra-small"><i class="mdi mdi-alert-circle me-1"></i>Network Error</span>';
                            }
                            resolve(false);
                        };

                        xhr.send(formData);
                    });
                }
            }

            // 2. Attach Google Drive Links (if any were provided)
            if (gdriveUrls.length > 0) {
                submitBtn.innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i> Attaching ${gdriveUrls.length} Google Drive link(s)...`;

                await new Promise((resolve) => {
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('category', categoryVal);
                    formData.append('documentable_type', docTypeVal);
                    formData.append('documentable_id', docIdVal);
                    formData.append('is_physically_signed', isPhysicallySignedVal);
                    if (titleVal) formData.append('title', titleVal);
                    if (notesVal) formData.append('signatory_notes', notesVal);

                    gdriveUrls.forEach(url => {
                        formData.append('gdrive_urls[]', url);
                    });

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const resp = JSON.parse(xhr.responseText);
                                if (resp.success) {
                                    gdriveSuccess = true;
                                    resolve(true);
                                    return;
                                }
                            } catch(e) {}
                        }
                        resolve(false);
                    };

                    xhr.onerror = function() {
                        resolve(false);
                    };

                    xhr.send(formData);
                });
            }

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;

            // 3. Process Feedback & Result
            const totalSuccess = uploadedPdfSuccess + (gdriveSuccess ? gdriveUrls.length : 0);
            if (totalSuccess > 0) {
                const parts = [];
                if (uploadedPdfSuccess > 0) parts.push(`${uploadedPdfSuccess} PDF document(s)`);
                if (gdriveSuccess && gdriveUrls.length > 0) parts.push(`${gdriveUrls.length} Google Drive link(s)`);
                const summaryMsg = 'Successfully registered ' + parts.join(' and ') + ' to the institutional vault.';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved to Repository',
                        text: summaryMsg,
                        timer: 1900,
                        showConfirmButton: false
                    });
                } else {
                    alert(summaryMsg);
                }

                const parentModal = form.closest('.modal');
                if (parentModal && typeof bootstrap !== 'undefined') {
                    const inst = bootstrap.Modal.getInstance(parentModal);
                    if (inst) inst.hide();
                }

                setTimeout(() => {
                    if (typeof window.refreshRepoAjax === 'function') {
                        window.refreshRepoAjax();
                    } else {
                        window.location.reload();
                    }
                }, 600);
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'Could not upload PDF files or attach links. Please verify file sizes and URLs.',
                        confirmButtonColor: '#00674f'
                    });
                } else {
                    alert('Could not save documents. Please verify your files/links and try again.');
                }
            }
        });
    }

    // Initialize counts
    window['updateAllCounts_' + cleanId]();

})();
</script>
