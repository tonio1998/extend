@extends('layouts.dss.app')

@section('title', 'Institutional Document Repository — Centralized Uploads')

@section('content')
<div class="container-fluid px-4 py-3">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0 extra-small">
            <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dss.rie.proposals.index') }}" class="text-decoration-none text-muted">RIE Extensions</a></li>
            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Document Repository</li>
        </ol>
    </nav>

    <div class="canva-hero-header p-4 mb-4 shadow-2xs">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="canva-theme-badge">
                        <i class="mdi mdi-folder-multiple-outline me-1"></i> INSTITUTIONAL REPOSITORY &bull; UPLOAD ENGINE
                    </span>
                    <span class="text-secondary extra-small">Cross-Module Digital Asset Management</span>
                </div>
                <h2 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.02em;">
                    Centralized Document &amp; Attachment Repository
                </h2>
                <p class="text-secondary small mb-0" style="max-width: 820px;">
                    Unified institutional archive storing all uploaded attachments, scanned wet-ink signed documents, activity designs, resolutions, and supporting exhibits across all university modules.
                </p>
            </div>

            <div>
                <button type="button" class="btn btn-dss-primary rounded-pill px-4 py-2 fw-bold shadow-xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#quickUploadModal">
                    <i class="mdi mdi-cloud-upload"></i>
                    <span>Upload Document</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="repo-bento-card h-100" style="border-top: 3.5px solid #2563eb;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Repository Files</span>
                        <div class="fs-2 fw-extrabold font-mono text-dark mt-1 lh-1">{{ $totalCount }}</div>
                    </div>
                    <div class="repo-icon-bubble bg-primary-subtle text-primary">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                    </div>
                </div>
                <div class="pt-2 border-top mt-2 extra-small text-secondary fw-semibold">
                    <i class="mdi mdi-database-outline"></i> Total archived digital assets
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('dss.documents.repository', ['physically_signed' => 1]) }}" class="text-decoration-none">
                <div class="repo-bento-card h-100 {{ $physicallySignedOnly ? 'bg-danger-subtle border-danger' : '' }}" style="border-top: 3.5px solid #dc2626;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Wet-Signed Scans</span>
                            <div class="fs-2 fw-extrabold font-mono text-danger mt-1 lh-1">{{ $signedCount }}</div>
                        </div>
                        <div class="repo-icon-bubble bg-danger-subtle text-danger">
                            <i class="mdi mdi-draw-pen"></i>
                        </div>
                    </div>
                    <div class="pt-2 border-top mt-2 extra-small text-danger fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="mdi mdi-shield-check-outline"></i> Physically signed &amp; verified</span>
                        @if($physicallySignedOnly)
                            <span class="badge bg-danger text-white extra-small">Filtered</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="repo-bento-card h-100" style="border-top: 3.5px solid #059669;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Module Coverage</span>
                        <div class="fs-4 fw-extrabold font-mono text-success mt-1 lh-1">All SUC Modules</div>
                    </div>
                    <div class="repo-icon-bubble bg-success-subtle text-success">
                        <i class="mdi mdi-layers-triple-outline"></i>
                    </div>
                </div>
                <div class="pt-2 border-top mt-2 extra-small text-success fw-semibold">
                    <i class="mdi mdi-check-decagram-outline"></i> RIE, DTS, Policies, Memos, QA
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="repo-bento-card h-100" style="border-top: 3.5px solid #7c3aed;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-uppercase extra-small text-muted fw-bold" style="letter-spacing: 0.05em; font-size: 10.5px;">Audit Integrity</span>
                        <div class="fs-5 fw-extrabold font-mono text-purple mt-1 lh-1">SHA-256 Hashed</div>
                    </div>
                    <div class="repo-icon-bubble bg-purple-subtle text-purple" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                        <i class="mdi mdi-shield-lock-outline"></i>
                    </div>
                </div>
                <div class="pt-2 border-top mt-2 extra-small text-muted fw-semibold">
                    <i class="mdi mdi-lock-check-outline text-success"></i> Tamper-evident checksums
                </div>
            </div>
        </div>
    </div>

    <div class="canva-card p-3 mb-4 shadow-sm">
        <form action="{{ route('dss.documents.repository') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search filename, title, hash, notes..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="module" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Modules</option>
                    <option value="rie" {{ $moduleFilter === 'rie' ? 'selected' : '' }}>RIE (Extension Plans &amp; Proposals)</option>
                    <option value="policy" {{ $moduleFilter === 'policy' ? 'selected' : '' }}>Institutional Policies</option>
                    <option value="memo" {{ $moduleFilter === 'memo' ? 'selected' : '' }}>University Memorandums</option>
                    <option value="accreditation" {{ $moduleFilter === 'accreditation' ? 'selected' : '' }}>Accreditation QA</option>
                    <option value="general" {{ $moduleFilter === 'general' ? 'selected' : '' }}>General / Standalone</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="physically_signed_plan" {{ $categoryFilter === 'physically_signed_plan' ? 'selected' : '' }}>Physically Signed Plans</option>
                    <option value="activity_design" {{ $categoryFilter === 'activity_design' ? 'selected' : '' }}>Activity Designs</option>
                    <option value="moa_mou" {{ $categoryFilter === 'moa_mou' ? 'selected' : '' }}>MOA / MOU Copies</option>
                    <option value="budget_proposal" {{ $categoryFilter === 'budget_proposal' ? 'selected' : '' }}>Budget Proposals</option>
                    <option value="annex" {{ $categoryFilter === 'annex' ? 'selected' : '' }}>Annexes</option>
                    <option value="general" {{ $categoryFilter === 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="campus_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Campuses</option>
                    @foreach($campuses as $camp)
                        <option value="{{ $camp->id }}" {{ (string)$campusId === (string)$camp->id ? 'selected' : '' }}>
                            {{ $camp->campus_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Wet-Signed Only Checkbox -->
            <div class="col-lg-2 col-md-6">
                <div class="form-check form-switch m-0 pt-1">
                    <input class="form-check-input" type="checkbox" role="switch" id="signedOnlySwitch" name="physically_signed" value="1" {{ $physicallySignedOnly ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label extra-small fw-bold text-danger" for="signedOnlySwitch">
                        <i class="mdi mdi-draw-pen me-0.5"></i> Wet-Signed Only
                    </label>
                </div>
            </div>

            <div class="col-lg-1 col-md-6 text-end">
                @if($search || $moduleFilter || $categoryFilter || $campusId || $physicallySignedOnly)
                    <a href="{{ route('dss.documents.repository') }}" class="btn btn-sm btn-light border text-danger w-100" title="Reset all filters">
                        <i class="mdi mdi-close"></i>
                    </a>
                @else
                    <button type="submit" class="btn btn-sm btn-dss-primary w-100 fw-bold">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="canva-card p-0 mb-4 shadow-sm overflow-hidden">
        @if($documents->isEmpty())
            <div class="text-center py-5 px-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light mb-3 shadow-2xs" style="width: 72px; height: 72px;">
                    <i class="mdi mdi-folder-open-outline text-muted fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Documents Found</h5>
                <p class="text-muted small mb-3">No uploaded documents match your filter criteria.</p>
                <a href="{{ route('dss.documents.repository') }}" class="btn btn-sm btn-light border fw-semibold px-3 py-1.5 rounded-pill">
                    Clear Filters
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4" style="width: 280px;">Document &amp; File</th>
                            <th style="width: 170px;">Category</th>
                            <th style="width: 220px;">Linked Module Record</th>
                            <th style="width: 140px;">File Size</th>
                            <th style="width: 180px;">Uploaded By / Date</th>
                            <th class="pe-4 text-end" style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                            <tr>
                                <!-- Document & File Details -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-inline-flex align-items-center justify-content-center bg-light border p-2" style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="mdi {{ $doc->file_icon }} fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 230px;" title="{{ $doc->title }}">
                                                {{ $doc->title }}
                                            </div>
                                            <div class="extra-small text-muted text-truncate font-mono" style="max-width: 230px;" title="{{ $doc->original_filename }}">
                                                {{ $doc->original_filename }}
                                            </div>
                                            @if($doc->sha256_hash)
                                                <div class="mt-0.5">
                                                    <span class="hash-badge-repo" onclick="copyRepoHash('{{ $doc->sha256_hash }}')" title="Click to copy SHA-256 Checksum: {{ $doc->sha256_hash }}">
                                                        <i class="mdi mdi-shield-check-outline text-success"></i>
                                                        <span>{{ Str::limit($doc->sha256_hash, 12) }}</span>
                                                        <i class="mdi mdi-content-copy text-muted ms-auto"></i>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Wet-Signed Flag -->
                                <td>
                                    @if($doc->is_physically_signed)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small px-2.5 py-1 mb-1 d-inline-flex align-items-center gap-1 font-mono">
                                            <i class="mdi mdi-draw-pen"></i> Wet-Signed Copy
                                        </span>
                                    @endif
                                    <div>
                                        <span class="badge bg-light text-secondary border extra-small px-2 py-0.5">
                                            {{ $doc->category_label }}
                                        </span>
                                    </div>
                                    @if($doc->signatory_notes)
                                        <div class="extra-small text-muted fst-italic mt-1 text-truncate" style="max-width: 170px;" title="{{ $doc->signatory_notes }}">
                                            "{{ $doc->signatory_notes }}"
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if($doc->documentable)
                                        @php
                                            $parent = $doc->documentable;
                                            $linkUrl = null;
                                            $recordLabel = 'Record #' . $doc->documentable_id;

                                            if ($parent instanceof \App\Models\RIE\ExtensionPlan) {
                                                $linkUrl = route('dss.rie.plans.show', $parent->getRouteKey());
                                                $recordLabel = $parent->plan_code . ' — ' . Str::limit($parent->extension_activity, 25);
                                            } elseif ($parent instanceof \App\Models\RIE\ExtensionProposal) {
                                                $linkUrl = route('dss.rie.proposals.show', $parent->getRouteKey());
                                                $recordLabel = $parent->proposal_code . ' — ' . Str::limit($parent->title, 25);
                                            } elseif ($parent instanceof \App\Models\Policy\InstitutionalPolicy) {
                                                $linkUrl = route('dss.policies.show', $parent->id);
                                                $recordLabel = $parent->policy_number . ' — ' . Str::limit($parent->title, 25);
                                            } elseif ($parent instanceof \App\Models\Policy\UniversityMemorandum) {
                                                $linkUrl = route('dss.memos.show', $parent->id);
                                                $recordLabel = $parent->memo_number . ' — ' . Str::limit($parent->subject, 25);
                                            }
                                        @endphp

                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small text-uppercase mb-1 font-mono">
                                            {{ $doc->module }}
                                        </span>
                                        @if($linkUrl)
                                            <div>
                                                <a href="{{ $linkUrl }}" class="text-dark fw-bold text-decoration-none hover-primary extra-small text-truncate d-inline-block" style="max-width: 210px;" title="{{ $recordLabel }}" target="_blank">
                                                    {{ $recordLabel }} <i class="mdi mdi-open-in-new extra-small text-primary"></i>
                                                </a>
                                            </div>
                                        @else
                                            <div class="extra-small text-muted">{{ $recordLabel }}</div>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border extra-small">Standalone Document</span>
                                    @endif
                                </td>

                                <!-- File Size -->
                                <td>
                                    <span class="font-mono fw-semibold text-dark extra-small">
                                        {{ $doc->formatted_file_size }}
                                    </span>
                                    <div class="extra-small text-muted text-uppercase">
                                        {{ $doc->extension ?: 'FILE' }}
                                    </div>
                                </td>

                                <!-- Uploader & Date -->
                                <td>
                                    <div class="fw-semibold text-dark extra-small text-truncate" style="max-width: 170px;" title="{{ $doc->uploader_name }}">
                                        {{ $doc->uploader_name ?: ($doc->uploader?->name ?: 'System') }}
                                    </div>
                                    <div class="extra-small text-muted font-mono">
                                        {{ $doc->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($doc->is_previewable)
                                            <a href="{{ $doc->preview_url }}" target="_blank" class="btn btn-sm btn-light border px-2.5 py-1 text-primary" title="Preview document in browser">
                                                <i class="mdi mdi-eye-outline fs-6"></i>
                                            </a>
                                        @endif
                                        <a href="{{ $doc->download_url }}" class="btn btn-sm btn-light border px-2.5 py-1 text-dark" title="Download file">
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

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="px-4 py-3 border-top bg-light d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="extra-small text-muted">
                        Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }} documents
                    </div>
                    <div>
                        {{ $documents->links() }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<div class="modal fade" id="quickUploadModal" tabindex="-1" aria-labelledby="quickUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="quickUploadModalLabel">
                    <i class="mdi mdi-cloud-upload text-primary"></i>
                    Upload to Institutional Repository
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <x-dss.document-uploader :allowPhysicalSign="true" category="general" title="Upload Document" />
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyRepoHash(hash) {
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
}
</script>
@endpush
