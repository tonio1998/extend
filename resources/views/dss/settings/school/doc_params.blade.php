@extends('layouts.dss.app')

@section('title', 'Document Parameters — School Management')

@section('content')
@php
    $docTypes = [
        'University Memorandum', 'Administrative Order', 'Board Resolution',
        'Implementing Rules & Regulations', 'Institutional Policy Code',
    ];
    $docIcons = [
        'University Memorandum'            => 'mdi-file-document-outline',
        'Administrative Order'             => 'mdi-file-sign',
        'Board Resolution'                 => 'mdi-gavel',
        'Implementing Rules & Regulations' => 'mdi-scale-balance',
        'Institutional Policy Code'        => 'mdi-book-open-variant',
    ];
    $savedDocSettings = $institution->document_settings ?? [];
@endphp

<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="extra-small text-muted font-mono">School Management</span>
                <i class="mdi mdi-chevron-right text-muted" style="font-size:12px;"></i>
                <span class="extra-small fw-bold text-dark font-mono">Document Parameters</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-file-cog text-dss-primary fs-5"></i>
                Document Formatting Parameters &amp; Salutations
            </h3>
            <p class="text-muted small mb-0">Configure typography, line spacing, opening greetings, closing salutations, and footer core values per document instrument.</p>
        </div>
    </div>

    {{-- Document Parameters Accordion --}}
    <div class="dss-card p-4">
        <div class="accordion" id="docTemplateAccordion">
            @foreach($docTypes as $dtIdx => $docType)
                @php
                    $slug  = Str::slug($docType, '_');
                    $saved = $savedDocSettings[$docType] ?? [];
                    $tplSigs = \App\Models\Policy\PolicySignatory::where('tenant_id', $institution->tenant_id)
                        ->where('is_template', true)
                        ->where(function($q) use ($docType) {
                            $q->where('document_type', $docType)->orWhereNull('document_type');
                        })
                        ->orderBy('position_column')->orderBy('sort_order')->get();
                @endphp
                <div class="accordion-item border mb-2 rounded shadow-2xs">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $dtIdx > 0 ? 'collapsed' : '' }} fw-semibold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#acc_{{ $slug }}">
                            <i class="mdi {{ $docIcons[$docType] ?? 'mdi-file-outline' }} me-2 text-dss-primary fs-5"></i>
                            {{ $docType }}
                            <span class="badge bg-light text-muted border ms-2 small">{{ $tplSigs->count() }} signatories configured</span>
                        </button>
                    </h2>
                    <div id="acc_{{ $slug }}" class="accordion-collapse collapse {{ $dtIdx === 0 ? 'show' : '' }}">
                        <div class="accordion-body pt-3">
                            <div class="p-3 border rounded bg-white mb-3 shadow-2xs">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold extra-small text-muted text-uppercase mb-0">
                                        <i class="mdi mdi-tune me-1 text-primary"></i>Document Parameters for {{ $docType }}
                                    </h6>
                                    <button class="btn btn-dss-primary btn-sm px-3 shadow-2xs" onclick="saveDocTemplate('{{ addslashes($docType) }}')">
                                        <i class="mdi mdi-content-save me-1"></i> Save Parameters
                                    </button>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Opening Cultural Greeting</label>
                                        <input type="text" class="form-control form-control-sm" id="greeting_{{ $slug }}"
                                            value="{{ $saved['greeting'] ?? '' }}"
                                            placeholder="e.g. Madujow nu aedow! Greetings from our institution!">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Closing Directive Salutation</label>
                                        <input type="text" class="form-control form-control-sm" id="closing_{{ $slug }}"
                                            value="{{ $saved['closing'] ?? '' }}"
                                            placeholder="e.g. For strict compliance and immediate dissemination.">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Core Values Text (Footer)</label>
                                        <input type="text" class="form-control form-control-sm" id="corevals_{{ $slug }}"
                                            value="{{ $saved['core_values'] ?? '' }}"
                                            placeholder="e.g. Accountability | Responsiveness | Innovativeness | Selflessness | Excellence">
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4 col-12">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Official Document Font</label>
                                        <select class="form-select form-select-sm" id="font_{{ $slug }}">
                                            <option value="Arial" {{ ($saved['font_family'] ?? 'Arial') === 'Arial' ? 'selected' : '' }}>Arial (Modern Official Sans-Serif)</option>
                                            <option value="Times" {{ ($saved['font_family'] ?? '') === 'Times' ? 'selected' : '' }}>Times New Roman (Formal Legal Serif)</option>
                                            <option value="Helvetica" {{ ($saved['font_family'] ?? '') === 'Helvetica' ? 'selected' : '' }}>Helvetica (Clean Corporate Sans)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Line Height</label>
                                        <select class="form-select form-select-sm" id="lineheight_{{ $slug }}">
                                            <option value="1.15" {{ ($saved['line_height'] ?? 1.5) == 1.15 ? 'selected' : '' }}>1.15 (Compact)</option>
                                            <option value="1.25" {{ ($saved['line_height'] ?? 1.5) == 1.25 ? 'selected' : '' }}>1.25 (Standard)</option>
                                            <option value="1.5"  {{ ($saved['line_height'] ?? 1.5) == 1.5  ? 'selected' : '' }}>1.5 (Standard Relaxed)</option>
                                            <option value="2.0"  {{ ($saved['line_height'] ?? 1.5) == 2.0  ? 'selected' : '' }}>2.0 (Double Space)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Signatory Columns</label>
                                        <select class="form-select form-select-sm" id="sigcols_{{ $slug }}">
                                            <option value="1" {{ ($saved['signatory_columns'] ?? 2) == 1 ? 'selected' : '' }}>1 Column</option>
                                            <option value="2" {{ ($saved['signatory_columns'] ?? 2) == 2 ? 'selected' : '' }}>2 Columns (Standard)</option>
                                            <option value="3" {{ ($saved['signatory_columns'] ?? 2) == 3 ? 'selected' : '' }}>3 Columns</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="paraspacing_{{ $slug }}" value="{{ $saved['paragraph_spacing'] ?? 3.0 }}">
                                <input type="hidden" id="siggap_{{ $slug }}" value="{{ $saved['signature_gap'] ?? 9.0 }}">
                                <input type="hidden" id="siglayout_{{ $slug }}" value="{{ $saved['signatory_layout'] ?? 'grid12' }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    var csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function saveDocTemplate(docType) {
        const slug = docType.toLowerCase().replace(/[^a-z0-9]/g, '_');
        const payload = {
            document_type: docType,
            greeting:           document.getElementById('greeting_'   + slug)?.value || '',
            closing:            document.getElementById('closing_'    + slug)?.value || '',
            core_values:        document.getElementById('corevals_'   + slug)?.value || '',
            font_family:        document.getElementById('font_'       + slug)?.value || 'Arial',
            line_height:        parseFloat(document.getElementById('lineheight_' + slug)?.value || '1.5'),
            paragraph_spacing:  parseFloat(document.getElementById('paraspacing_' + slug)?.value || '3.0'),
            signature_gap:      parseFloat(document.getElementById('siggap_'     + slug)?.value || '9.0'),
            signatory_columns:  parseInt(document.getElementById('sigcols_'  + slug)?.value || '2', 10),
            signatory_layout:   document.getElementById('siglayout_' + slug)?.value || 'grid12',
        };
        fetch("{{ route('dss.settings.institution.document_settings') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(d => {
            if (d.result) {
                Swal.fire({ icon: 'success', title: 'Saved!', text: d.message, confirmButtonColor: 'var(--dss-primary)', timer: 1600, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save parameters.' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not connect to server.' }));
    }
</script>
@endsection
