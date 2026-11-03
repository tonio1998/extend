@extends('layouts.dss.app')

@section('title', 'Document Code Formats — System Settings')

@section('content')
<div class="container-fluid p-0">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">System Settings</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size:12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">Document Codes</span>
            </div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-barcode-scan text-primary"></i>
                Official Document Code Formats
            </h4>
            <p class="text-muted small mb-0">Set the standardized numbering formats for extension documents. Each created document automatically generates with its format prefix, current year, and 4-digit series.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.institution') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Settings
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-xs fw-semibold" onclick="saveDocumentCodeSettings()">
                <i class="mdi mdi-content-save me-1"></i>Save Formats
            </button>
        </div>
    </div>

    {{-- Settings Form --}}
    <form id="documentCodesForm">
        @csrf

        <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">RIE Document Numbering Standards</h6>
                        <span class="text-muted extra-small">Format structure: <code>[PREFIX]-[YEAR]-[SERIES]</code> (e.g. <code>SNSU-PLAN-2026-0001</code>)</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted extra-small text-uppercase fw-bold" style="width: 32%;">Document Type</th>
                                <th class="py-3 text-muted extra-small text-uppercase fw-bold" style="width: 38%;">Code Format Prefix</th>
                                <th class="pe-4 py-3 text-muted extra-small text-uppercase fw-bold text-end" style="width: 30%;">Live Sample Output</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 1. Extension Plan --}}
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="mdi mdi-clipboard-text-outline fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $docTypes['plan']['name'] }}</div>
                                            <div class="text-muted extra-small">Annual & Academic Program Work & Financial Plan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="input-group input-group-sm" style="max-width: 320px;">
                                        <input type="text"
                                            class="form-control font-mono text-uppercase doc-code-input"
                                            id="code_plan"
                                            name="codes[plan]"
                                            value="{{ $docTypes['plan']['code'] }}"
                                            placeholder="SNSU-PLAN-XXXX"
                                            data-target="plan"
                                            required>
                                    </div>
                                    <div class="text-muted extra-small mt-1">Use <code>XXXX</code> for incremental series numbering.</div>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <span class="badge bg-light text-primary border font-mono fs-6 px-3 py-2" id="preview_plan">
                                        {{ $docTypes['plan']['preview'] }}
                                    </span>
                                </td>
                            </tr>

                            {{-- 2. Extension Proposal --}}
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="mdi mdi-file-table-outline fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $docTypes['proposal']['name'] }}</div>
                                            <div class="text-muted extra-small">Comprehensive Extension Project & Program Proposal</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="input-group input-group-sm" style="max-width: 320px;">
                                        <input type="text"
                                            class="form-control font-mono text-uppercase doc-code-input"
                                            id="code_proposal"
                                            name="codes[proposal]"
                                            value="{{ $docTypes['proposal']['code'] }}"
                                            placeholder="SNSU-PROP-XXXX"
                                            data-target="proposal"
                                            required>
                                    </div>
                                    <div class="text-muted extra-small mt-1">Use <code>XXXX</code> for incremental series numbering.</div>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <span class="badge bg-light text-info border font-mono fs-6 px-3 py-2" id="preview_proposal">
                                        {{ $docTypes['proposal']['preview'] }}
                                    </span>
                                </td>
                            </tr>

                            {{-- 3. Activity Design --}}
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="mdi mdi-calendar-check-outline fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $docTypes['activity_design']['name'] }}</div>
                                            <div class="text-muted extra-small">Detailed Activity Execution Design & Certification</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="input-group input-group-sm" style="max-width: 320px;">
                                        <input type="text"
                                            class="form-control font-mono text-uppercase doc-code-input"
                                            id="code_activity_design"
                                            name="codes[activity_design]"
                                            value="{{ $docTypes['activity_design']['code'] }}"
                                            placeholder="SNSU-ACT-XXXX"
                                            data-target="activity_design"
                                            required>
                                    </div>
                                    <div class="text-muted extra-small mt-1">Use <code>XXXX</code> for incremental series numbering.</div>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <span class="badge bg-light text-success border font-mono fs-6 px-3 py-2" id="preview_activity_design">
                                        {{ $docTypes['activity_design']['preview'] }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted extra-small">Changes apply immediately to newly generated documents across all campuses.</span>
                <button type="button" class="btn btn-primary btn-sm px-4 shadow-xs fw-semibold" onclick="saveDocumentCodeSettings()">
                    <i class="mdi mdi-content-save me-1"></i>Save Formats
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    const currentYear = "{{ date('Y') }}";

    function updatePreview(type) {
        const input = document.getElementById('code_' + type);
        const previewEl = document.getElementById('preview_' + type);

        if (!input || !previewEl) return;

        let pattern = input.value.trim().toUpperCase();
        input.value = pattern;

        if (!pattern) {
            previewEl.textContent = '—';
            return;
        }

        let preview = '';
        if (pattern.includes('XXXX')) {
            if (!pattern.includes(currentYear) && !pattern.includes('YYYY')) {
                preview = pattern.replace('-XXXX', '-' + currentYear + '-0001');
            } else {
                preview = pattern.replace('YYYY', currentYear).replace('XXXX', '0001');
            }
        } else {
            preview = pattern + '-' + currentYear + '-0001';
        }

        previewEl.textContent = preview.replace(/-+/g, '-');
    }

    document.querySelectorAll('.doc-code-input').forEach(el => {
        el.addEventListener('input', function() {
            updatePreview(this.dataset.target);
        });
    });

    function saveDocumentCodeSettings() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        const payload = {
            codes: {
                plan: document.getElementById('code_plan').value.trim(),
                proposal: document.getElementById('code_proposal').value.trim(),
                activity_design: document.getElementById('code_activity_design').value.trim()
            }
        };

        fetch("{{ route('dss.settings.document_codes.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.result || data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved Successfully',
                        text: data.message || 'Document code formats updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(data.message || 'Document code formats updated successfully.');
                }
            } else {
                alert(data.message || 'Failed to save document code formats.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An unexpected error occurred while saving.');
        });
    }
</script>
@endpush
