<!-- Filter & Keyword Search Bar -->
<div class="card border rounded-3 p-3 mb-4 bg-white shadow-2xs">
    <form id="repoCanvasFilterForm" action="{{ route('dss.documents.repository') }}" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="view" value="{{ $viewMode }}" id="hiddenViewMode">
        <input type="hidden" name="format" value="{{ $formatFilter }}" id="hiddenFormatFilter">
        <input type="hidden" name="storage" value="{{ $storageFilter }}" id="hiddenStorageFilter">
        <input type="hidden" name="campus_id" value="{{ $campusId }}" id="hiddenCampusId">
        <input type="hidden" name="physically_signed" value="{{ $physicallySignedOnly ? '1' : '' }}" id="hiddenPhysicallySigned">

        <!-- Module Select -->
        <div class="col-md-2">
            <select name="module" id="canvasModuleSelect" class="form-select form-select-sm">
                <option value="">All Institutional Modules</option>
                <option value="rie" {{ $moduleFilter === 'rie' ? 'selected' : '' }}>RIE (Plans &amp; Proposals)</option>
                <option value="policy" {{ $moduleFilter === 'policy' ? 'selected' : '' }}>Institutional Policies</option>
                <option value="memo" {{ $moduleFilter === 'memo' ? 'selected' : '' }}>Memorandums</option>
                <option value="dts" {{ $moduleFilter === 'dts' ? 'selected' : '' }}>DTS Tracking Attachments</option>
                <option value="general" {{ $moduleFilter === 'general' ? 'selected' : '' }}>General / Standalone</option>
            </select>
        </div>

        <!-- Category Select -->
        <div class="col-md-2">
            <select name="category" id="canvasCategorySelect" class="form-select form-select-sm">
                <option value="">All Document Categories</option>
                <option value="wet_signed_milestone" {{ $categoryFilter === 'wet_signed_milestone' ? 'selected' : '' }}>Wet-Signed Milestones</option>
                <option value="physically_signed_plan" {{ $categoryFilter === 'physically_signed_plan' ? 'selected' : '' }}>Physically Signed Plans</option>
                <option value="activity_design" {{ $categoryFilter === 'activity_design' ? 'selected' : '' }}>Activity Designs</option>
                <option value="milestone_evidence" {{ $categoryFilter === 'milestone_evidence' ? 'selected' : '' }}>Milestone Evidence</option>
                <option value="moa_mou" {{ $categoryFilter === 'moa_mou' ? 'selected' : '' }}>MOA / MOU Copies</option>
                <option value="budget_proposal" {{ $categoryFilter === 'budget_proposal' ? 'selected' : '' }}>Budget Proposals</option>
                <option value="general" {{ $categoryFilter === 'general' ? 'selected' : '' }}>General Attachments</option>
            </select>
        </div>

        <!-- Sort Order Select -->
        <div class="col-md-2">
            <select name="sort" id="canvasSortSelect" class="form-select form-select-sm font-mono extra-small">
                <option value="latest" {{ empty($sort) || $sort === 'latest' ? 'selected' : '' }}>Sort: Latest Uploads</option>
                <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Sort: Oldest Uploads</option>
                <option value="name_asc" {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}>Sort: Title (A-Z)</option>
                <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Sort: Title (Z-A)</option>
                <option value="size_desc" {{ ($sort ?? '') === 'size_desc' ? 'selected' : '' }}>Sort: File Size (Largest)</option>
            </select>
        </div>

        <!-- Keyword Search -->
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                <input type="text" name="search" id="canvasSearchInput" class="form-control form-control-sm border-start-0" placeholder="Search filename, title, checksum, or author..." value="{{ $search }}" autocomplete="off">
                @if($search)
                    <button type="button" class="input-group-text bg-light border-start-0 text-danger" id="btnClearCanvasSearch" title="Clear Search">
                        <i class="mdi mdi-close"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="col-md-2 text-end">
            @if($search || $moduleFilter || $categoryFilter || $campusId || $physicallySignedOnly || $formatFilter || $storageFilter || (!empty($sort) && $sort !== 'latest'))
                <button type="button" class="btn btn-sm btn-light border text-danger rounded-pill w-100 fw-medium" id="btnResetRepoFilters" title="Reset all filters">
                    <i class="mdi mdi-refresh me-1"></i> Clear Filters
                </button>
            @else
                <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100 fw-medium">
                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                </button>
            @endif
        </div>
    </form>

    <!-- Active Filter Tags -->
    @if($search || $moduleFilter || $categoryFilter || $campusId || $physicallySignedOnly || $formatFilter || $storageFilter || (!empty($sort) && $sort !== 'latest'))
        <div class="d-flex align-items-center gap-1 flex-wrap pt-2 mt-2 border-top extra-small">
            <span class="text-muted fw-bold font-mono">Active Filters:</span>
            @if($search)
                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                    Search: "{{ $search }}"
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('search')"></i>
                </span>
            @endif
            @if($moduleFilter)
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1">
                    Module: {{ strtoupper($moduleFilter) }}
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('module')"></i>
                </span>
            @endif
            @if($categoryFilter)
                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                    Category: {{ $categoryFilter }}
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('category')"></i>
                </span>
            @endif
            @if($physicallySignedOnly)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                    <i class="mdi mdi-draw-pen me-1"></i> Wet-Signed
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('physically_signed')"></i>
                </span>
            @endif
            @if($formatFilter)
                <span class="badge bg-info-subtle text-info border border-info-subtle d-inline-flex align-items-center gap-1">
                    Format: {{ strtoupper($formatFilter) }}
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('format')"></i>
                </span>
            @endif
            @if($storageFilter)
                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                    Storage: {{ strtoupper($storageFilter) }}
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('storage')"></i>
                </span>
            @endif
            @if(!empty($sort) && $sort !== 'latest')
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle d-inline-flex align-items-center gap-1">
                    Sort: {{ str_replace('_', ' ', strtoupper($sort)) }}
                    <i class="mdi mdi-close ms-1 cursor-pointer" onclick="removeRepoFilter('sort')"></i>
                </span>
            @endif
        </div>
    @endif
</div>

<!-- Top Pagination Bar -->
@if(!$documents->isEmpty() && $documents->hasPages())
    <div class="mb-3">
        {{ $documents->links('components.dss.pagination') }}
    </div>
@endif

<!-- Documents Display: Grid vs Table Explorer -->
@if($documents->isEmpty())
    <div class="card border rounded-3 p-5 text-center bg-white shadow-2xs mb-4">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light mb-3 shadow-2xs" style="width: 64px; height: 64px;">
            <i class="mdi mdi-folder-open-outline text-muted fs-2"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">No Documents Found</h5>
        <p class="text-muted small mb-3">No uploaded files or Google Drive links match your search and filter parameters.</p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-sm btn-light border px-3 rounded-pill fw-semibold" onclick="resetRepoFilters()">
                Reset Filters
            </button>
            <button type="button" class="btn btn-sm btn-primary px-3 rounded-pill fw-bold" onclick="openUploadModalWithMode('both')">
                <i class="mdi mdi-cloud-upload me-1"></i> Upload Document Now
            </button>
        </div>
    </div>
@elseif($viewMode === 'grid')
    <!-- GRID CARDS VIEW -->
    <div class="row g-3 mb-4">
        @foreach($documents as $doc)
            @php
                $isExt = $doc->disk === 'external';
                $isGdrive = $isExt && str_contains((string)$doc->file_path, 'drive.google.com');

                $typeColor = '#64748b';
                $ext = strtolower(pathinfo((string)$doc->original_filename, PATHINFO_EXTENSION));

                if ($isGdrive) {
                    $typeColor = '#16a34a';
                } elseif (in_array($ext, ['pdf'])) {
                    $typeColor = '#dc2626';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    $typeColor = '#2563eb';
                } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                    $typeColor = '#059669';
                } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                    $typeColor = '#7c3aed';
                } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                    $typeColor = '#d97706';
                }
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-xl-3" id="docCard_{{ $doc->id }}">
                <div class="repo-doc-card">
                    <!-- Card Header Banner -->
                    <div class="repo-doc-card-thumb">
                        <div class="p-2 text-center">
                            <i class="mdi {{ $doc->file_icon }} fs-2" style="color: {{ $typeColor }};"></i>
                        </div>

                        <!-- Top Right Badges -->
                        <div class="position-absolute top-0 end-0 m-1 d-flex flex-column align-items-end gap-1">
                            @if($doc->is_physically_signed)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle shadow-2xs" style="font-size: 10px; padding: 2px 6px;" title="Official Wet-Signed Copy">
                                    <i class="mdi mdi-draw-pen me-1"></i> Wet-Signed
                                </span>
                            @endif
                            @if($isGdrive)
                                <span class="badge bg-success-subtle text-success border border-success-subtle shadow-2xs" style="font-size: 10px; padding: 2px 6px;" title="Google Drive Link">
                                    <i class="mdi mdi-google-drive me-1"></i> Drive
                                </span>
                            @endif
                        </div>

                        <!-- Top Left Category Badge -->
                        <div class="position-absolute top-0 start-0 m-1">
                            <span class="badge bg-white text-secondary border" style="font-size: 10px; padding: 2px 6px;">
                                {{ $doc->category_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-2 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <a href="{{ $doc->preview_url }}" target="_blank" class="fw-semibold text-dark text-decoration-none d-block text-truncate mb-1" style="font-size: 13px;" title="{{ $doc->title }}">
                                {{ $doc->title }}
                            </a>

                            <div class="extra-small text-muted font-mono text-truncate mb-1" style="font-size: 11px;" title="{{ $doc->original_filename }}">
                                {{ $doc->original_filename }}
                            </div>

                            <!-- Linked Parent Record Badge -->
                            @if($doc->documentable)
                                @php
                                    $parent = $doc->documentable;
                                    $linkUrl = null;
                                    $recordLabel = 'Record #' . $doc->documentable_id;

                                    if ($parent instanceof \App\Models\RIE\ExtensionPlan) {
                                        $linkUrl = route('dss.rie.plans.show', $parent->getRouteKey());
                                        $recordLabel = 'Plan: ' . $parent->plan_code;
                                    } elseif ($parent instanceof \App\Models\RIE\ExtensionProposal) {
                                        $linkUrl = route('dss.rie.proposals.show', $parent->getRouteKey());
                                        $recordLabel = 'Proposal: ' . ($parent->proposal_code ?: 'P-' . $parent->id);
                                    } elseif ($parent instanceof \App\Models\RIE\ExtensionPlanActivity) {
                                        $plan = $parent->extensionPlan;
                                        $linkUrl = $plan ? route('dss.rie.plans.show', $plan->getRouteKey()) : null;
                                        $recordLabel = 'Milestone: ' . Str::limit($parent->title, 18);
                                    }
                                @endphp
                                <div class="mb-1">
                                    @if($linkUrl)
                                        <a href="{{ $linkUrl }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none extra-small text-truncate d-inline-block" style="max-width: 100%; font-size: 10px;" target="_blank" title="Open linked record">
                                            <i class="mdi mdi-link-variant me-1"></i> {{ $recordLabel }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border extra-small text-truncate d-inline-block" style="font-size: 10px;">
                                            {{ $recordLabel }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- SHA-256 Checksum Pill -->
                            @if($doc->sha256_hash)
                                <div class="mb-2">
                                    <span class="repo-hash-badge" style="font-size: 10px; padding: 2px 6px;" onclick="copyRepoHash('{{ $doc->sha256_hash }}')" title="Click to copy SHA-256 Checksum">
                                        <i class="mdi mdi-shield-check text-success"></i>
                                        <span>{{ substr($doc->sha256_hash, 0, 12) }}...</span>
                                        <i class="mdi mdi-content-copy text-muted ms-1" style="font-size: 9px;"></i>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer: Author, Size, Date, Actions -->
                        <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                            <div class="overflow-hidden pe-1">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 11px; max-width: 110px;" title="{{ $doc->uploader_name }}">
                                    {{ $doc->uploader_name ?: 'System' }}
                                </div>
                                <div class="extra-small text-muted font-mono text-truncate" style="font-size: 10px;">
                                    {{ $doc->formatted_file_size }} &bull; {{ $doc->created_at->format('M d') }}
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <a href="{{ $doc->preview_url }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" title="View Document">
                                    <i class="mdi mdi-eye" style="font-size: 12px;"></i>
                                </a>
                                <a href="{{ $doc->download_url }}" class="btn btn-xs btn-outline-secondary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" title="Download Document">
                                    <i class="mdi mdi-download" style="font-size: 12px;"></i>
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-info rounded-circle p-1 d-inline-flex align-items-center justify-content-center btn-inspect-asset" style="width: 24px; height: 24px;"
                                        data-id="{{ $doc->id }}"
                                        data-title="{{ $doc->title }}"
                                        data-filename="{{ $doc->original_filename }}"
                                        data-hash="{{ $doc->sha256_hash }}"
                                        data-size="{{ $doc->formatted_file_size }}"
                                        data-uploader="{{ $doc->uploader_name ?: 'System' }}"
                                        data-date="{{ $doc->created_at->format('M d, Y h:i A') }}"
                                        data-category="{{ $doc->category_label }}"
                                        data-signed="{{ $doc->is_physically_signed ? 'Yes (Wet-Signed)' : 'Digital' }}"
                                        data-notes="{{ $doc->signatory_notes }}"
                                        data-preview="{{ $doc->preview_url }}"
                                        data-download="{{ $doc->download_url }}"
                                        title="Inspect Details">
                                    <i class="mdi mdi-information-outline"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center btn-delete-document" style="width: 24px; height: 24px;"
                                        data-doc-id="{{ $doc->id }}"
                                        data-filename="{{ $doc->original_filename }}"
                                        data-delete-url="{{ route('dss.documents.upload.destroy', $doc->id) }}"
                                        title="Remove Document">
                                    <i class="mdi mdi-trash-can-outline" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- TABLE EXPLORER VIEW (ACTIONS IN 1ST COLUMN WITH DROPDOWN) -->
    <div class="card border rounded-3 p-0 shadow-2xs overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light extra-small text-uppercase text-muted font-mono">
                    <tr>
                        <th class="ps-3" style="width: 85px;">Action</th>
                        <th style="width: 45px;">#</th>
                        <th style="min-width: 280px;">Document Title &amp; Filename</th>
                        <th style="min-width: 160px;">Category &amp; Source</th>
                        <th style="min-width: 170px;">Linked Record</th>
                        <th style="min-width: 180px;">SHA-256 Checksum</th>
                        <th style="min-width: 160px;">Uploader &amp; Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        @php
                            $isExt = $doc->disk === 'external';
                            $isGdrive = $isExt && str_contains((string)$doc->file_path, 'drive.google.com');
                        @endphp
                        <tr id="docRow_{{ $doc->id }}">
                            <!-- 1ST COLUMN: ACTIONS DROPDOWN -->
                            <td class="ps-3">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 extra-small fw-semibold d-inline-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="actionDrop_{{ $doc->id }}">
                                        <i class="mdi mdi-dots-horizontal fs-6"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border p-1 rounded-3" style="font-size: 12px; min-width: 175px;" aria-labelledby="actionDrop_{{ $doc->id }}">
                                        <li>
                                            <a class="dropdown-item rounded-2 py-1 d-flex align-items-center gap-2" href="{{ $doc->preview_url }}" target="_blank">
                                                <i class="mdi mdi-eye text-primary fs-6"></i>
                                                <span>Preview Document</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 py-1 d-flex align-items-center gap-2" href="{{ $doc->download_url }}">
                                                <i class="mdi mdi-download text-secondary fs-6"></i>
                                                <span>Download Copy</span>
                                            </a>
                                        </li>
                                        @if($doc->sha256_hash)
                                            <li>
                                                <a class="dropdown-item rounded-2 py-1 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="copyRepoHash('{{ $doc->sha256_hash }}')">
                                                    <i class="mdi mdi-shield-check text-success fs-6"></i>
                                                    <span>Copy SHA-256</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item rounded-2 py-1 d-flex align-items-center gap-2 btn-inspect-asset" href="javascript:void(0)"
                                               data-id="{{ $doc->id }}"
                                               data-title="{{ $doc->title }}"
                                               data-filename="{{ $doc->original_filename }}"
                                               data-hash="{{ $doc->sha256_hash }}"
                                               data-size="{{ $doc->formatted_file_size }}"
                                               data-uploader="{{ $doc->uploader_name ?: 'System' }}"
                                               data-date="{{ $doc->created_at->format('M d, Y h:i A') }}"
                                               data-category="{{ $doc->category_label }}"
                                               data-signed="{{ $doc->is_physically_signed ? 'Yes (Wet-Signed)' : 'Digital' }}"
                                               data-notes="{{ $doc->signatory_notes }}"
                                               data-preview="{{ $doc->preview_url }}"
                                               data-download="{{ $doc->download_url }}">
                                                <i class="mdi mdi-information-outline text-info fs-6"></i>
                                                <span>Document Details</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1 text-danger d-flex align-items-center gap-2 btn-delete-document"
                                                    data-doc-id="{{ $doc->id }}"
                                                    data-filename="{{ $doc->original_filename }}"
                                                    data-delete-url="{{ route('dss.documents.upload.destroy', $doc->id) }}">
                                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                                                <span>Remove Document</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <!-- 2ND COLUMN: INDEX -->
                            <td class="text-muted font-mono extra-small">{{ $loop->iteration + ($documents->currentPage() - 1) * $documents->perPage() }}</td>

                            <!-- 3RD COLUMN: TITLE & FILENAME -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi {{ $doc->file_icon }} fs-4 flex-shrink-0"></i>
                                    <div class="overflow-hidden">
                                        <a href="{{ $doc->preview_url }}" target="_blank" class="fw-bold text-dark text-truncate d-block text-decoration-none" title="{{ $doc->title }}">
                                            {{ $doc->title }}
                                        </a>
                                        <div class="extra-small text-muted font-mono text-truncate" style="max-width: 260px;" title="{{ $doc->original_filename }}">
                                            {{ $doc->original_filename }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 4TH COLUMN: CATEGORY & BADGES -->
                            <td>
                                <span class="badge bg-light text-secondary border font-mono extra-small">
                                    {{ $doc->category_label }}
                                </span>
                                @if($doc->is_physically_signed)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small ms-1">
                                        <i class="mdi mdi-draw-pen me-1"></i> Wet-Signed
                                    </span>
                                @endif
                                @if($isGdrive)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small ms-1">
                                        <i class="mdi mdi-google-drive me-1"></i> Drive
                                    </span>
                                @endif
                            </td>

                            <!-- 5TH COLUMN: LINKED RECORD -->
                            <td>
                                @if($doc->documentable)
                                    @php
                                        $parent = $doc->documentable;
                                        $linkUrl = null;
                                        $recordLabel = 'Record #' . $doc->documentable_id;

                                        if ($parent instanceof \App\Models\RIE\ExtensionPlan) {
                                            $linkUrl = route('dss.rie.plans.show', $parent->getRouteKey());
                                            $recordLabel = 'Plan: ' . $parent->plan_code;
                                        } elseif ($parent instanceof \App\Models\RIE\ExtensionProposal) {
                                            $linkUrl = route('dss.rie.proposals.show', $parent->getRouteKey());
                                            $recordLabel = 'Proposal: ' . ($parent->proposal_code ?: 'P-' . $parent->id);
                                        } elseif ($parent instanceof \App\Models\RIE\ExtensionPlanActivity) {
                                            $plan = $parent->extensionPlan;
                                            $linkUrl = $plan ? route('dss.rie.plans.show', $plan->getRouteKey()) : null;
                                            $recordLabel = 'Milestone: ' . Str::limit($parent->title, 20);
                                        }
                                    @endphp
                                    @if($linkUrl)
                                        <a href="{{ $linkUrl }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none extra-small" target="_blank">
                                            <i class="mdi mdi-link-variant me-1"></i> {{ $recordLabel }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border extra-small">
                                            {{ $recordLabel }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted extra-small fst-italic">Standalone File</span>
                                @endif
                            </td>

                            <!-- 6TH COLUMN: SHA-256 CHECKSUM -->
                            <td>
                                @if($doc->sha256_hash)
                                    <span class="repo-hash-badge" onclick="copyRepoHash('{{ $doc->sha256_hash }}')" title="Click to copy SHA-256 Checksum">
                                        <i class="mdi mdi-shield-check text-success"></i>
                                        <span>{{ substr($doc->sha256_hash, 0, 14) }}...</span>
                                        <i class="mdi mdi-content-copy text-muted ms-1" style="font-size: 10px;"></i>
                                    </span>
                                @else
                                    <span class="text-muted extra-small">—</span>
                                @endif
                            </td>

                            <!-- 7TH COLUMN: UPLOADER & DATE -->
                            <td>
                                <div class="fw-semibold text-dark extra-small text-truncate" style="max-width: 150px;">
                                    {{ $doc->uploader_name ?: 'System' }}
                                </div>
                                <div class="extra-small text-muted font-mono">
                                    {{ $doc->formatted_file_size }} &bull; {{ $doc->created_at->format('M d, Y') }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Pagination -->
@if($documents->hasPages())
    <div class="mb-4">
        {{ $documents->links('components.dss.pagination') }}
    </div>
@endif

