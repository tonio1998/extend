@props([
    'model',
    'type' => null,
    'title' => 'Attached Files & Physical Scans',
    'allowPhysicalSign' => true,
    'defaultCategory' => 'general',
    'auditTrails' => null,
])

@php
    $componentId = 'doc_att_' . substr(md5(get_class($model) . '_' . $model->getKey()), 0, 10);
    $uploadModalId = $componentId . '_uploadModal';
    $auditModalId = $componentId . '_auditModal';

    $uploadedDocs = $model->uploadedDocuments ?? collect();
    $physicallySigned = $model->physicallySignedDocument ?? $uploadedDocs->firstWhere('is_physically_signed', true);

    // Resolve audit trails if not passed
    $trails = $auditTrails ?? ($model->documentAuditTrails ? $model->documentAuditTrails()->with(['user', 'uploadedDocument'])->latest()->get() : collect());
@endphp

<div id="{{ $componentId }}_wrapper" class="dss-document-attachments-component">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold text-dark m-0 fs-6">{{ $title }}</h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small rounded-pill px-2.5 py-1">
                {{ $uploadedDocs->count() }} {{ Str::plural('File', $uploadedDocs->count()) }}
            </span>
            @if($physicallySigned)
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2.5 py-1 font-mono">
                    <i class="mdi mdi-draw-pen me-1"></i> Wet-Signed Plan Verified
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold d-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#{{ $auditModalId }}">
                <i class="mdi mdi-history fs-6"></i>
                <span>Audit Trail</span>
                <span class="badge bg-secondary text-white font-mono extra-small ms-1">{{ $trails->count() }}</span>
            </button>

            <button type="button" class="btn btn-sm btn-dss-primary rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#{{ $uploadModalId }}">
                <i class="mdi mdi-upload fs-6"></i>
                <span>Upload Attachment</span>
            </button>
        </div>
    </div>

    @if($physicallySigned)
        <div class="p-2.5 px-3 mb-3 rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
            <div class="d-flex align-items-center gap-2.5">
                <span class="badge bg-success rounded-circle p-1.5 d-inline-flex text-white">
                    <i class="mdi mdi-check-bold" style="font-size: 11px;"></i>
                </span>
                <div class="extra-small">
                    <span class="fw-bold text-success-emphasis">Official Wet-Signed Copy:</span>
                    <strong class="text-dark font-mono ms-1">{{ $physicallySigned->original_filename }}</strong>
                    <span class="text-muted ms-2">({{ $physicallySigned->formatted_file_size }} • Signed {{ $physicallySigned->signed_date ? $physicallySigned->signed_date->format('M d, Y') : $physicallySigned->created_at->format('M d, Y') }})</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                @if($physicallySigned->is_previewable)
                    <a href="{{ $physicallySigned->preview_url }}" target="_blank" class="btn btn-xs btn-success rounded-pill px-2.5 py-1 extra-small fw-semibold d-inline-flex align-items-center gap-1 shadow-none">
                        <i class="mdi mdi-eye-outline"></i> View Wet-Signed Scan
                    </a>
                @endif
                <a href="{{ $physicallySigned->download_url }}" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 extra-small" title="Download">
                    <i class="mdi mdi-download"></i>
                </a>
            </div>
        </div>
    @endif

    <div class="canva-card p-0 border shadow-2xs overflow-hidden">
        @if($uploadedDocs->isEmpty())
            <div class="text-center py-5 text-muted small">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light mb-2" style="width: 52px; height: 52px;">
                    <i class="mdi mdi-folder-open-outline fs-3 text-secondary opacity-50"></i>
                </div>
                <div class="fw-semibold text-dark">No Attachments Yet</div>
                <div class="extra-small text-muted mt-1 mb-3">Upload physically signed plans, activity proposals, or supporting evidence.</div>
                <button type="button" class="btn btn-sm btn-dss-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#{{ $uploadModalId }}">
                    <i class="mdi mdi-upload me-1"></i> Upload First File
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4" style="width: 280px;">Document / File</th>
                            <th style="width: 170px;">Category</th>
                            <th style="width: 130px;">Size</th>
                            <th style="width: 170px;">Uploaded By &amp; Date</th>
                            <th class="pe-4 text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploadedDocs as $doc)
                            <tr class="{{ $doc->status === 'superseded' ? 'opacity-75 bg-light-subtle' : '' }}">
                                <!-- File & Name -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 d-inline-flex align-items-center justify-content-center bg-light border p-2" style="width: 38px; height: 38px; min-width: 38px;">
                                            <i class="mdi {{ $doc->file_icon }} fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="{{ $doc->title }}">
                                                    {{ $doc->title }}
                                                </span>
                                                @if($doc->status === 'superseded')
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-mono" style="font-size: 10px; padding: 1px 5px;">
                                                        v{{ $doc->version }} (Archived)
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 10px; padding: 1px 5px;">
                                                        v{{ $doc->version }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="extra-small text-muted text-truncate font-mono" style="max-width: 220px;" title="{{ $doc->original_filename }}">
                                                {{ $doc->original_filename }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td>
                                    @if($doc->is_physically_signed)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small px-2 py-0.5 font-mono mb-1 d-inline-block">
                                            <i class="mdi mdi-draw-pen me-0.5"></i> Wet-Signed Plan
                                        </span>
                                    @endif
                                    <div>
                                        <span class="badge bg-light text-secondary border extra-small px-2 py-0.5">
                                            {{ $doc->category_label }}
                                        </span>
                                    </div>
                                    @if($doc->signatory_notes)
                                        <div class="extra-small text-muted fst-italic mt-0.5 text-truncate" style="max-width: 160px;" title="{{ $doc->signatory_notes }}">
                                            "{{ $doc->signatory_notes }}"
                                        </div>
                                    @endif
                                </td>

                                <!-- File Size -->
                                <td>
                                    <span class="font-mono text-dark fw-semibold extra-small">{{ $doc->formatted_file_size }}</span>
                                    <div class="extra-small text-muted text-uppercase">{{ $doc->extension ?: 'FILE' }}</div>
                                </td>

                                <!-- Uploaded By & Date -->
                                <td>
                                    <div class="fw-semibold text-dark extra-small text-truncate" style="max-width: 160px;" title="{{ $doc->uploader_name }}">
                                        {{ $doc->uploader_name ?: ($doc->uploader?->name ?: 'User') }}
                                    </div>
                                    <div class="extra-small text-muted font-mono">
                                        {{ $doc->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($doc->is_previewable)
                                            <a href="{{ $doc->preview_url }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1 text-primary" title="Preview file">
                                                <i class="mdi mdi-eye-outline fs-6"></i>
                                            </a>
                                        @endif
                                        <a href="{{ $doc->download_url }}" class="btn btn-sm btn-light border px-2 py-1 text-dark" title="Download file">
                                            <i class="mdi mdi-download fs-6"></i>
                                        </a>
                                        <form action="{{ route('dss.documents.upload.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this attached file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1" title="Delete attachment">
                                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="modal fade" id="{{ $uploadModalId }}" tabindex="-1" aria-labelledby="{{ $uploadModalId }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-inline-flex">
                            <i class="mdi mdi-cloud-upload fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6" id="{{ $uploadModalId }}Label">Upload Document Attachment</h5>
                            <p class="text-muted extra-small m-0">Attach supporting files or an official physically signed plan scan.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <x-dss.document-uploader :model="$model" :type="$type" :category="$defaultCategory" :allowPhysicalSign="$allowPhysicalSign" title="Select or Drag Document Here" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $auditModalId }}" tabindex="-1" aria-labelledby="{{ $auditModalId }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-2 d-inline-flex">
                            <i class="mdi mdi-history fs-5 text-indigo"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6" id="{{ $auditModalId }}Label">Centralized Document Audit Trail &amp; Movement History</h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono">
                                <i class="mdi mdi-shield-lock-outline me-1"></i> ISO 9001:2015 &amp; COA Verified
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($trails->isEmpty())
                        <div class="text-center py-5 text-muted extra-small">
                            <i class="mdi mdi-file-clock-outline fs-2 text-secondary opacity-50 d-block mb-1"></i>
                            No movement or download trail logged yet. Every upload, revision, download, and delete is recorded automatically.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                <thead class="table-light extra-small text-uppercase text-muted font-mono">
                                    <tr>
                                        <th class="ps-4" style="width: 170px;">Timestamp</th>
                                        <th style="width: 140px;">Action</th>
                                        <th style="width: 90px;">Version</th>
                                        <th>Summary &amp; Changelog Notes</th>
                                        <th style="width: 180px;">Actor / User</th>
                                        <th class="pe-4 text-end" style="width: 140px;">Integrity Seal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trails as $trail)
                                        @php
                                            $badge = $trail->action_badge;
                                        @endphp
                                        <tr>
                                            <!-- Timestamp -->
                                            <td class="ps-4 font-mono text-muted extra-small">
                                                <div class="fw-semibold text-dark">{{ $trail->created_at->format('M d, Y') }}</div>
                                                <div>{{ $trail->created_at->format('h:i:s A') }}</div>
                                            </td>

                                            <!-- Action Badge -->
                                            <td>
                                                <span class="badge {{ $badge['class'] }} rounded-pill extra-small px-2 py-0.5 font-mono d-inline-flex align-items-center gap-1">
                                                    <i class="mdi {{ $badge['icon'] }}"></i>
                                                    <span>{{ $badge['label'] }}</span>
                                                </span>
                                            </td>

                                            <!-- Version -->
                                            <td>
                                                <span class="badge bg-light text-dark border font-mono extra-small px-2 py-0.5">
                                                    v{{ $trail->version }}
                                                </span>
                                            </td>

                                            <!-- Summary & Changelog Notes -->
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $trail->summary }}</div>
                                                @if($trail->change_notes)
                                                    <div class="extra-small text-muted fst-italic mt-0.5">
                                                        <i class="mdi mdi-comment-text-outline text-primary me-0.5"></i> "{{ $trail->change_notes }}"
                                                    </div>
                                                @endif
                                                @if($trail->filename && $trail->filename !== $trail->summary)
                                                    <div class="extra-small text-muted font-mono mt-0.5">
                                                        File: {{ $trail->filename }} ({{ number_format($trail->file_size / 1024, 1) }} KB)
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- Actor / User & IP -->
                                            <td>
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;">
                                                    {{ $trail->user_name ?: ($trail->user?->name ?: 'System') }}
                                                </div>
                                                <div class="extra-small text-muted font-mono">
                                                    {{ $trail->ip_address ?: '127.0.0.1' }}
                                                </div>
                                            </td>

                                            <!-- Cryptographic Hash Seal -->
                                            <td class="pe-4 text-end">
                                                @if($trail->sha256_hash)
                                                    <span class="badge bg-light text-muted border font-mono extra-small cursor-pointer" onclick="copyRepoHash('{{ $trail->sha256_hash }}')" title="Cryptographic SHA-256 Hash: {{ $trail->sha256_hash }}" style="cursor: pointer;">
                                                        <i class="mdi mdi-shield-check text-success"></i>
                                                        <span>{{ Str::limit($trail->sha256_hash, 8) }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted extra-small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer py-2 px-4 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
<script>
if (typeof window.copyRepoHash !== 'function') {
    window.copyRepoHash = function(hash) {
        if (!hash) return;
        if (typeof window.dssCopyToClipboard === 'function') {
            window.dssCopyToClipboard(hash, 'SHA-256 hash copied to clipboard.');
        } else {
            const ta = document.createElement('textarea');
            ta.value = hash;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            if (window.dssToast) {
                window.dssToast.show({ title: 'Checksum Copied', message: 'SHA-256 hash copied to clipboard.', icon: 'success' });
            } else {
                alert('SHA-256 Hash copied: ' + hash);
            }
        }
    };
}
</script>
@endonce

