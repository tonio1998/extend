@extends('layouts.dss.app')

@section('title', 'Create Activity Design — Extension Suite')

@section('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 44px;
        border-radius: 10px;
        border-color: #cbd5e1;
        font-size: 13.5px;
        display: flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-selection:focus {
        border-color: var(--ma-primary, #047857);
        box-shadow: 0 0 0 0.2rem rgba(4, 120, 87, 0.15);
    }
    .proposal-option-item {
        padding: 6px 4px;
    }
    .proposal-option-item .option-code {
        font-family: var(--bs-font-monospace);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .proposal-option-item .option-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-top: 1px;
    }
    .proposal-option-item .option-meta {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }
    .quick-selector-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: border-color 0.2s ease;
    }
    .preview-fact-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb extra-small font-mono m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dss.rie.activity_designs.index') }}" class="text-decoration-none text-muted">Activity Designs</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold">New Activity Design</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-calendar-check-outline text-primary"></i>
                Create Activity Design
            </h4>
            <p class="text-muted small mb-0">Select an approved Extension Proposal to generate its official Activity Design sheet. Project objectives, milestones, and budgets are automatically synchronized.</p>
        </div>
        <div>
            <a href="{{ route('dss.rie.activity_designs.index') }}" class="btn btn-sm btn-outline-secondary px-3 shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Activity Designs
            </a>
        </div>
    </div>

    {{-- 1. Dedicated Quick Selector Card --}}
    <div class="quick-selector-card p-4 mb-4" id="quickSelectorContainer">
        <form action="{{ route('dss.rie.activity_designs.store') }}" method="POST" id="activityDesignForm">
            @csrf

            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-8">
                    <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1 d-flex align-items-center justify-content-between">
                        <span>Select Extension Proposal</span>
                        <span class="text-muted extra-small font-normal">Search by code, title, or project leader</span>
                    </label>
                    <select name="extension_proposal_id" id="proposalSelect" class="form-select" required style="width: 100%;">
                        <option value="">Search and select an extension proposal...</option>
                        @foreach($proposals as $p)
                            @php
                                $existing = $existingDesignMap->get($p->id);
                                $unitName = $p->unit?->unit_name ?: ($p->extensionPlan?->academicProgram?->program_name ?: 'Academic Unit');
                                $leaderDisplay = $p->project_leader_name ?: 'Unassigned';
                                $budgetVal = (float) ($p->budget_cash_amount ?: ($p->budget_institution_share ?: 0));
                            @endphp
                            <option value="{{ $p->id }}" {{ ($selectedProposal && $selectedProposal->id == $p->id) ? 'selected' : '' }}
                                data-code="{{ $p->proposal_code }}"
                                data-title="{{ $p->title }}"
                                data-unit="{{ $unitName }}"
                                data-leader="{{ $leaderDisplay }}"
                                data-location="{{ $p->project_location ?: 'SNSU / Partner Community' }}"
                                data-duration="{{ $p->duration_text ?: 'Scheduled Period' }}"
                                data-budget="₱{{ number_format($budgetVal, 2) }}"
                                data-status="{{ $p->status }}"
                                data-existing-id="{{ $existing ? (function_exists('dss_encrypt_id') ? dss_encrypt_id($existing->id) : $existing->id) : '' }}"
                                data-existing-code="{{ $existing ? $existing->design_code : '' }}">
                                {{ $p->proposal_code }} — {{ $p->title }} ({{ $leaderDisplay }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-4 d-flex align-items-end">
                    <div class="w-100">
                        <span class="extra-small text-muted d-block mb-1">Standard Activity Design Code Format</span>
                        <div class="p-2 px-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <span class="extra-small font-mono text-muted">Format: <code>Prefix-Year-Series</code></span>
                            <span class="badge bg-primary font-mono extra-small" id="badgeNextCode">{{ $nextCode ?? 'SNSU-ACT-'.date('Y').'-XXXX' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dynamic Proposal Details Card (Appears on Selection) --}}
            <div id="proposalPreviewSection" class="mt-4 pt-3 border-top" style="{{ $selectedProposal ? '' : 'display: none;' }}">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary font-mono fs-6" id="previewCode">
                            {{ $selectedProposal?->proposal_code }}
                        </span>
                        <span class="badge bg-success font-mono extra-small" id="badgeExistingLinked" style="{{ ($selectedProposal && $existingDesignMap->has($selectedProposal->id)) ? '' : 'display: none;' }}">
                            <i class="mdi mdi-check-circle-outline me-1"></i>Activity Design Active
                        </span>
                    </div>
                    <div class="text-muted extra-small">
                        <i class="mdi mdi-link-variant me-1"></i>Direct Data Linkage (No re-encoding needed)
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3" id="previewTitle">
                    {{ $selectedProposal?->title }}
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="preview-fact-box">
                            <div class="extra-small text-muted text-uppercase fw-bold mb-1">Project Leader</div>
                            <div class="fw-bold text-dark text-truncate" id="previewLeader">
                                {{ $selectedProposal?->project_leader_name ?: '—' }}
                            </div>
                            <div class="text-muted extra-small text-truncate" id="previewUnit">
                                {{ $selectedProposal?->unit?->unit_name ?: 'Academic Unit' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="preview-fact-box">
                            <div class="extra-small text-muted text-uppercase fw-bold mb-1">Implementation Period</div>
                            <div class="fw-bold text-dark text-truncate" id="previewDuration">
                                {{ $selectedProposal?->duration_text ?: 'Scheduled Period' }}
                            </div>
                            <div class="text-muted extra-small text-truncate" id="previewLocation">
                                {{ $selectedProposal?->project_location ?: 'SNSU / Field' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="preview-fact-box">
                            <div class="extra-small text-muted text-uppercase fw-bold mb-1">Allocated Budget</div>
                            <div class="fw-bold text-success font-mono" id="previewBudget">
                                ₱{{ number_format((float) ($selectedProposal?->budget_cash_amount ?: 0), 2) }}
                            </div>
                            <div class="text-muted extra-small">Approved Proposal Share</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="preview-fact-box">
                            <div class="extra-small text-muted text-uppercase fw-bold mb-1">Target Document Code</div>
                            <div class="fw-bold text-primary font-mono" id="previewDesignCode">
                                @if($selectedProposal && $existingDesignMap->has($selectedProposal->id))
                                    {{ $existingDesignMap->get($selectedProposal->id)->design_code }}
                                @else
                                    {{ $nextCode }}
                                @endif
                            </div>
                            <div class="text-muted extra-small">Document Type #3</div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                    <button type="submit" id="btnGenerate" class="btn btn-primary px-4 py-2 fw-semibold shadow-xs">
                        <i class="mdi mdi-file-document-plus-outline me-1"></i>Generate Activity Design
                    </button>
                    <a href="#" id="btnOpenExisting" class="btn btn-outline-success px-4 py-2 fw-semibold shadow-xs" style="display: none;">
                        <i class="mdi mdi-file-eye-outline me-1"></i>View Activity Design Sheet
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- 2. Proposals Directory Table --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold text-dark m-0">Proposals Ready for Activity Design</h6>
                <div class="text-muted extra-small">Browse all proposals or click "Select Proposal" to generate an Activity Design.</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 280px;">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="tableFilterInput" class="form-control border-start-0" placeholder="Filter proposals...">
                </div>
                <span class="badge bg-light text-dark border font-mono">Total: {{ $proposals->count() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="proposalsTable">
                <thead class="table-light extra-small text-uppercase font-mono">
                    <tr>
                        <th class="ps-4" style="width: 140px;">Action</th>
                        <th style="width: 25%;">Proposal Code &amp; Title</th>
                        <th style="width: 25%;">Project Leader</th>
                        <th style="width: 20%;">Academic Program / Unit</th>
                        <th style="width: 15%;">Budget</th>
                        <th class="pe-4 text-center" style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody class="small" id="proposalsTableBody">
                    @forelse($proposals as $p)
                        @php
                            $existing = $existingDesignMap->get($p->id);
                            $unitName = $p->unit?->unit_name ?: ($p->extensionPlan?->academicProgram?->program_name ?: 'Academic Unit');
                            $budgetVal = (float) ($p->budget_cash_amount ?: ($p->budget_institution_share ?: 0));
                        @endphp
                        <tr class="proposal-row"
                            data-search="{{ strtolower($p->proposal_code . ' ' . $p->title . ' ' . $p->project_leader_name . ' ' . $unitName) }}">
                            
                            {{-- Action Column --}}
                            <td class="ps-4">
                                @if($existing)
                                    <a href="{{ route('dss.rie.activity_designs.show', (function_exists('dss_encrypt_id') ? dss_encrypt_id($existing->id) : $existing->id)) }}"
                                       class="btn btn-outline-success btn-xs px-3 py-1 fw-semibold rounded-pill">
                                        <i class="mdi mdi-eye me-1"></i>View
                                    </a>
                                @else
                                    <button type="button"
                                            class="btn btn-primary btn-xs px-3 py-1 fw-semibold rounded-pill btn-quick-select"
                                            data-id="{{ $p->id }}">
                                        <i class="mdi mdi-check me-1"></i>Select
                                    </button>
                                @endif
                            </td>

                            {{-- Code & Title --}}
                            <td>
                                <div class="fw-bold font-mono text-primary">{{ $p->proposal_code }}</div>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $p->title }}">{{ $p->title }}</div>
                            </td>

                            {{-- Leader --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->project_leader_name ?: '—' }}</div>
                                <div class="text-muted extra-small">{{ $p->project_leader_title ?: 'Project Leader' }}</div>
                            </td>

                            {{-- Academic Unit --}}
                            <td>
                                <div class="text-dark">{{ $unitName }}</div>
                            </td>

                            {{-- Budget --}}
                            <td>
                                <div class="font-mono fw-bold text-success">₱{{ number_format($budgetVal, 2) }}</div>
                            </td>

                            {{-- Activity Design Status --}}
                            <td class="pe-4 text-center">
                                @if($existing)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                                        {{ $existing->design_code }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border font-mono extra-small">
                                        Ready
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No proposals found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const $select = $('#proposalSelect');
    const previewSection = document.getElementById('proposalPreviewSection');
    const previewCode = document.getElementById('previewCode');
    const previewTitle = document.getElementById('previewTitle');
    const previewUnit = document.getElementById('previewUnit');
    const previewLeader = document.getElementById('previewLeader');
    const previewDuration = document.getElementById('previewDuration');
    const previewLocation = document.getElementById('previewLocation');
    const previewBudget = document.getElementById('previewBudget');
    const previewDesignCode = document.getElementById('previewDesignCode');
    const badgeExistingLinked = document.getElementById('badgeExistingLinked');
    const btnGenerate = document.getElementById('btnGenerate');
    const btnOpenExisting = document.getElementById('btnOpenExisting');
    const nextCodeStr = "{{ $nextCode ?? 'SNSU-ACT-' . date('Y') . '-XXXX' }}";

    // Custom Select2 formatting for rich search dropdown
    function formatProposalOption(opt) {
        if (!opt.id) {
            return opt.text;
        }
        const $el = $(opt.element);
        const code = $el.data('code') || '';
        const title = $el.data('title') || opt.text;
        const leader = $el.data('leader') || '';
        const unit = $el.data('unit') || '';
        const existingCode = $el.data('existing-code');

        let statusBadge = existingCode 
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle float-end font-mono extra-small">${existingCode}</span>` 
            : `<span class="badge bg-light text-muted border float-end font-mono extra-small">Ready</span>`;

        return $(`
            <div class="proposal-option-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle option-code">${code}</span>
                    ${statusBadge}
                </div>
                <div class="option-title text-truncate">${title}</div>
                <div class="option-meta"><i class="mdi mdi-account-outline me-1"></i>${leader} &bull; <i class="mdi mdi-office-building me-1"></i>${unit}</div>
            </div>
        `);
    }

    $select.select2({
        theme: 'bootstrap-5',
        placeholder: 'Search proposal by code, title, or project leader...',
        allowClear: true,
        templateResult: formatProposalOption
    });

    function updatePreviewFromSelect() {
        const val = $select.val();
        if (!val) {
            previewSection.style.display = 'none';
            btnOpenExisting.style.display = 'none';
            btnGenerate.style.display = 'inline-block';
            btnGenerate.innerHTML = '<i class="mdi mdi-file-document-plus-outline me-1"></i>Generate Activity Design';
            return;
        }

        const selectedOpt = $select.find(':selected');
        const code = selectedOpt.data('code') || '';
        const title = selectedOpt.data('title') || '';
        const unit = selectedOpt.data('unit') || '—';
        const leader = selectedOpt.data('leader') || '—';
        const duration = selectedOpt.data('duration') || '—';
        const location = selectedOpt.data('location') || '—';
        const budget = selectedOpt.data('budget') || '₱0.00';
        const existingId = selectedOpt.data('existing-id');
        const existingCode = selectedOpt.data('existing-code');

        previewCode.textContent = code;
        previewTitle.textContent = title;
        previewUnit.textContent = unit;
        previewLeader.textContent = leader;
        previewDuration.textContent = duration;
        previewLocation.textContent = location;
        previewBudget.textContent = budget;

        if (existingId && existingCode) {
            badgeExistingLinked.style.display = 'inline-block';
            previewDesignCode.textContent = existingCode;
            btnGenerate.innerHTML = '<i class="mdi mdi-sync me-1"></i>Re-sync &amp; Update Activity Design';
            btnOpenExisting.style.display = 'inline-block';
            btnOpenExisting.href = '/dss/rie/activity-designs/' + existingId;
        } else {
            badgeExistingLinked.style.display = 'none';
            previewDesignCode.textContent = nextCodeStr;
            btnGenerate.innerHTML = '<i class="mdi mdi-file-document-plus-outline me-1"></i>Generate Activity Design';
            btnOpenExisting.style.display = 'none';
        }

        previewSection.style.display = 'block';
    }

    $select.on('change', updatePreviewFromSelect);

    if ($select.val()) {
        updatePreviewFromSelect();
    }

    // "Select" button inside Table
    $('.btn-quick-select').on('click', function () {
        const pId = $(this).data('id');
        $select.val(pId).trigger('change');
        $('html, body').animate({
            scrollTop: $('#quickSelectorContainer').offset().top - 80
        }, 300);
    });

    // Real-time table filter
    $('#tableFilterInput').on('input', function () {
        const q = $(this).val().toLowerCase().trim();
        $('.proposal-row').each(function () {
            const text = $(this).data('search');
            if (!q || text.indexOf(q) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endpush
