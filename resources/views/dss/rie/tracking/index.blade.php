@extends('layouts.dss.app')

@section('title', 'Document Tracking Hub — RIE System')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-light text-primary border font-mono extra-small">RIE SYSTEM</span>
                <span class="text-muted extra-small">Document Tracking</span>
            </div>
            <h4 class="fw-bold text-dark m-0">Document Tracking Hub</h4>
            <div class="text-muted extra-small mt-0.5">
                Link official 3rd-party DTS tracking numbers to authorize document printing.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('rie.plans.view')
            <a href="{{ route('dss.rie.plans.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="mdi mdi-arrow-left me-1"></i> Extension Plans
            </a>
            @endcan
            @can('rie.proposals.view')
            <a href="{{ route('dss.rie.proposals.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Extension Proposals
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="tracking-stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted extra-small fw-bold font-mono">Total Documents</div>
                    <div class="fs-4 fw-bold text-dark font-mono mt-1">{{ $totalDocs }}</div>
                    <div class="extra-small text-muted">{{ $totalPlans }} Plans • {{ $totalProposals }} Proposals</div>
                </div>
                <div class="rounded-circle p-2 bg-light text-primary d-inline-flex">
                    <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tracking-stat-card d-flex align-items-center justify-content-between border-start border-success border-3">
                <div>
                    <div class="text-uppercase text-muted extra-small fw-bold font-mono">Connected Tracking #</div>
                    <div class="fs-4 fw-bold text-success font-mono mt-1">{{ $totalConnected }}</div>
                    <div class="extra-small text-muted">Ready for official PDF print</div>
                </div>
                <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success d-inline-flex">
                    <i class="mdi mdi-barcode-scan fs-4"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="tracking-stat-card d-flex align-items-center justify-content-between border-start border-warning border-3">
                <div>
                    <div class="text-uppercase text-muted extra-small fw-bold font-mono">Pending Connection</div>
                    <div class="fs-4 fw-bold text-warning-emphasis font-mono mt-1">{{ $totalMissing }}</div>
                    <div class="extra-small text-muted">Tracking number needed for print</div>
                </div>
                <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning d-inline-flex">
                    <i class="mdi mdi-link-variant-off fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border rounded-3 p-3 mb-3 shadow-2xs bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="trackingSearchInput" class="form-control form-control-sm border-start-0"
                           placeholder="Search code, title, proponent, or tracking #...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterDocType" class="form-select form-select-sm">
                    <option value="all">All Document Types</option>
                    <option value="plans">Extension Plans</option>
                    <option value="proposals">Extension Proposals</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterTrackingStatus" class="form-select form-select-sm">
                    <option value="all">All Statuses</option>
                    <option value="missing">Pending Tracking #</option>
                    <option value="connected">Connected Tracking #</option>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-light border w-100" id="btnResetFilters" title="Reset Filters">
                    <i class="mdi mdi-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card border rounded-3 overflow-hidden shadow-2xs bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="trackingTable" style="width: 100%;">
                <thead class="bg-light text-muted text-uppercase fw-bold font-mono">
                    <tr>
                        <th class="ps-3">Document</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 220px;">3rd-Party Tracking #</th>
                        <th class="pe-3 text-end" style="width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('rie.tracking.manage')
<div class="modal fade" id="modalUniversalConnectTracking" tabindex="-1" aria-labelledby="modalUniversalConnectTrackingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 420px;">
        <div class="modal-content shadow-lg border rounded-3">
            <div class="modal-header py-2.5 px-3 border-bottom bg-light">
                <h6 class="modal-title fw-bold text-dark m-0" id="modalUniversalConnectTrackingLabel" style="font-size: 13.5px;">
                    <i class="mdi mdi-barcode-scan text-primary me-1"></i> Connect 3rd-Party DTS Tracking #
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-2">
                    <span class="badge bg-light text-muted border font-mono extra-small" id="modalDocTypeBadge">Plan</span>
                    <span class="fw-bold font-mono text-dark ms-1" style="font-size: 13px;" id="modalDocCodeDisplay">CODE</span>
                </div>

                <form id="formUniversalConnectTracking" onsubmit="return false;">
                    <input type="hidden" id="modalDocTypeInput" value="plan">
                    <input type="hidden" id="modalDocIdInput" value="">
                    <input type="hidden" id="modalRedirectPdfUrl" value="">

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Tracking Number
                        </label>
                        <input type="text" class="form-control font-mono fw-bold fs-6" id="modalTrackingNumberInput"
                               placeholder="e.g. 12345 or SNSU-2026-001" required autocomplete="off">
                        <div class="form-text extra-small text-muted">
                            Enter the tracking number from the 3rd-party tracking system.
                        </div>
                    </div>

                    <div id="modalTrackingFeedback" class="alert d-none extra-small py-1.5 px-2 mb-2"></div>
                </form>
            </div>
            <div class="modal-footer py-2 px-3 border-top bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-xs btn-outline-danger d-none" id="btnDisconnectTrackingModal" title="Unlink tracking number">
                    <i class="mdi mdi-link-variant-off me-0.5"></i> Clear #
                </button>
                <div class="d-flex gap-1.5 ms-auto">
                    <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-xs btn-primary fw-bold" id="btnSubmitUniversalConnect">
                        <i class="mdi mdi-check me-0.5"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    const table = $('#trackingTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        ajax: {
            url: "{{ route('dss.rie.tracking.data') }}",
            data: function (d) {
                d.doc_type = $('#filterDocType').val();
                d.tracking_status = $('#filterTrackingStatus').val();
                d.search = $('#trackingSearchInput').val();
            }
        },
        columns: [
            { data: 'doc_info', name: 'title', className: 'ps-3' },
            { data: 'status_badge', name: 'status' },
            { data: 'tracking_info', name: 'tracking_number' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'pe-3 text-end' }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="ms-2 extra-small text-muted">Loading...</span>',
            emptyTable: 'No documents found.',
            zeroRecords: 'No matching documents.'
        },
        dom: "<'row'<'col-sm-12'tr>><'row align-items-center p-3'<'col-sm-6 text-muted extra-small'i><'col-sm-6 d-flex justify-content-end'p>>"
    });

    $('#filterDocType, #filterTrackingStatus').on('change', function () {
        table.draw();
    });

    $('#trackingSearchInput').on('keyup', function () {
        table.draw();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filterDocType').val('all');
        $('#filterTrackingStatus').val('all');
        $('#trackingSearchInput').val('');
        table.draw();
    });

    @can('rie.tracking.manage')
    window.openConnectModal = function(type, id, code, tracking, redirectAfter = false, pdfUrl = '') {
        $('#modalDocTypeInput').val(type);
        $('#modalDocIdInput').val(id);
        $('#modalDocCodeDisplay').text(code);
        $('#modalDocTypeBadge').text(type === 'plan' ? 'Plan' : 'Proposal');
        $('#modalTrackingNumberInput').val(tracking || '');
        $('#modalRedirectPdfUrl').val(redirectAfter && pdfUrl ? pdfUrl : '');

        const feedback = $('#modalTrackingFeedback');
        feedback.addClass('d-none').removeClass('alert-success alert-danger').text('');

        if (tracking) {
            $('#btnDisconnectTrackingModal').removeClass('d-none');
        } else {
            $('#btnDisconnectTrackingModal').addClass('d-none');
        }

        const modal = new bootstrap.Modal(document.getElementById('modalUniversalConnectTracking'));
        modal.show();
        setTimeout(() => $('#modalTrackingNumberInput').focus(), 350);
    };

    $('#btnSubmitUniversalConnect').on('click', function () {
        const btn = $(this);
        const type = $('#modalDocTypeInput').val();
        const id = $('#modalDocIdInput').val();
        const tracking = $('#modalTrackingNumberInput').val().trim();
        const feedback = $('#modalTrackingFeedback');
        const pdfRedirect = $('#modalRedirectPdfUrl').val();

        if (!tracking) {
            feedback.removeClass('d-none').addClass('alert-danger').text('Please enter a tracking number.');
            return;
        }

        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');
        feedback.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: "{{ route('dss.rie.tracking.connect') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                document_type: type,
                document_id: id,
                tracking_number: tracking
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="mdi mdi-check me-0.5"></i> Save');
                feedback.removeClass('d-none').addClass('alert-success').text('Tracking number saved.');

                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('modalUniversalConnectTracking')).hide();
                    table.ajax.reload(null, false);
                    if (pdfRedirect) {
                        window.open(pdfRedirect, '_blank');
                    }
                }, 600);
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="mdi mdi-check me-0.5"></i> Save');
                const msg = xhr.responseJSON?.message || 'Failed to connect. Please try again.';
                feedback.removeClass('d-none').addClass('alert-danger').text(msg);
            }
        });
    });

    $('#btnDisconnectTrackingModal').on('click', function () {
        if (!confirm('Clear tracking number from this document?')) {
            return;
        }

        const type = $('#modalDocTypeInput').val();
        const id = $('#modalDocIdInput').val();
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('dss.rie.tracking.disconnect') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                document_type: type,
                document_id: id
            },
            success: function () {
                btn.prop('disabled', false);
                bootstrap.Modal.getInstance(document.getElementById('modalUniversalConnectTracking')).hide();
                table.ajax.reload(null, false);
            },
            error: function () {
                btn.prop('disabled', false);
                alert('Failed to clear tracking number.');
            }
        });
    });
    @else
    window.openConnectModal = function() {};
    @endcan
});
</script>
@endsection
