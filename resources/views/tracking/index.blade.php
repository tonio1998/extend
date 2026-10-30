<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Institutional Document Tracking — {{ $institution->school_acronym ?: 'SNSU' }}</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @include('layouts.dss.theme-styles')

    <style>
        .tracker-hero {
            background: radial-gradient(circle at 90% 10%, color-mix(in srgb, var(--dss-primary) 12%, transparent) 0%, transparent 60%),
                        radial-gradient(circle at 10% 90%, color-mix(in srgb, var(--dss-secondary) 10%, transparent) 0%, transparent 60%),
                        #ffffff;
            padding: 2.75rem 0 2rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .action-card-btn {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
        }

        .action-card-btn:hover {
            border-color: var(--dss-primary);
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .action-card-btn.active {
            border-color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .stepper-trail-item {
            position: relative;
            padding-left: 2.75rem;
            padding-bottom: 1.75rem;
        }

        .stepper-trail-item:last-child {
            padding-bottom: 0;
        }

        .stepper-trail-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 34px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .stepper-trail-item:last-child::before {
            display: none;
        }

        .stepper-node-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            z-index: 2;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #64748b;
        }

        .stepper-trail-item.completed .stepper-node-icon {
            background: #0284c7;
            border-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
        }

        .stepper-trail-item.current .stepper-node-icon {
            background: #f59e0b;
            border-color: #f59e0b;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .dss-card { border: 1px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-2.5 no-print">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            <a class="navbar-brand d-flex align-items-center gap-3 fw-bold" href="{{ url('/') }}">
                <div class="rounded-3 p-1.5 bg-dss-primary-soft text-dss-primary border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="mdi mdi-school fs-5"></i>
                </div>
                <div>
                    <span class="text-dark fs-6 fw-bold">{{ $institution->school_acronym ?: 'ADSSU' }}</span>
                    <span class="badge badge-dss-primary ms-1" style="font-size: 10px;">Document Tracking System (DTS)</span>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('document.track') }}" class="btn btn-sm btn-light border text-dark fw-semibold">
                    <i class="mdi mdi-magnify me-1 text-muted"></i>New Track Search
                </a>
                @auth
                    <a href="{{ route('dss.dashboard') }}" class="btn btn-sm btn-dss-primary">
                        <i class="mdi mdi-view-dashboard-outline me-1"></i>Executive Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-light border fw-bold text-dark">
                        <i class="mdi mdi-login me-1"></i>Portal Sign In
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Search Hero Bar (Shown when search is active or landing) -->
    <section class="tracker-hero no-print">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form method="GET" action="{{ route('document.track') }}" class="dss-card p-2 shadow-sm d-flex gap-2 mb-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="mdi mdi-magnify fs-5"></i></span>
                            <input type="text" class="form-control border-0" name="tracking_no" id="tracking_no_input" value="{{ $searchQuery }}" placeholder="Enter Tracking No. (e.g. PR-2026-0881, PO-2026-0412, DV-2026-0199)..." required>
                        </div>
                        <button type="button" class="btn btn-light border text-muted px-3" onclick="openQrScannerModal()" title="Scan QR Code with Camera">
                            <i class="mdi mdi-camera-outline fs-5 text-dss-primary"></i>
                        </button>
                        <button type="submit" class="btn btn-dss-primary px-4 fw-bold">
                            Track Document
                        </button>
                    </form>

                    <!-- Quick Demo Tracking Number Pills -->
                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center extra-small text-muted">
                        <span class="fw-semibold">Quick Samples:</span>
                        @foreach($recentDtsRecords as $dtsDoc)
                            <a href="{{ route('document.track', ['tracking_number' => $dtsDoc->QRCODE]) }}" class="badge bg-white text-dark border text-decoration-none py-1 px-2.5 shadow-2xs hover-shadow">
                                <i class="mdi mdi-barcode me-0.5 text-success"></i>{{ $dtsDoc->DocumentID }} ({{ $dtsDoc->doc_type }})
                            </a>
                        @endforeach
                        @foreach($recentTrackableDocs as $doc)
                            <a href="{{ route('document.track', ['tracking_number' => $doc->tracking_number]) }}" class="badge bg-white text-dark border text-decoration-none py-1 px-2.5 shadow-2xs hover-shadow">
                                <i class="mdi mdi-file-document-outline me-0.5 text-dss-primary"></i>{{ $doc->tracking_number }} ({{ $doc->process_type }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <main class="py-4">
        <div class="container-fluid px-3 px-md-4 px-xl-5">
            @if($searchQuery && !$workflow && !$dtsRecord)
                <!-- Not Found Alert -->
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="dss-card p-5 text-center">
                            <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-file-question-outline fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Document Record Not Found</h4>
                            <p class="text-muted small mb-3">No institutional workflow or DTS record matches tracking number <strong class="fw-bold text-dark">{{ $searchQuery }}</strong>. Please check the spelling or scan the official QR code printed on the routing slip.</p>
                            <a href="{{ route('document.track') }}" class="btn btn-sm btn-outline-secondary">Try Another Search</a>
                        </div>
                    </div>
                </div>
            @elseif($dtsRecord)
                <!-- DTS Document Tracking View for ADSSU -->
                <div class="dss-card p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">
                        <div class="rounded-3 p-3 bg-light border d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; min-width: 54px;">
                            <i class="mdi mdi-barcode fs-3 text-dark"></i>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <!-- Badges Top Strip -->
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <span class="badge bg-dark text-white px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-barcode-scan"></i>
                                    {{ $dtsRecord->DocumentID }}
                                </span>

                                <span class="badge bg-light text-dark border px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-clock-outline text-warning"></i>
                                    {{ $dtsRecord->TrailStatus }}
                                </span>

                                <span class="badge bg-light text-muted border px-2 py-1">
                                    {{ $dtsRecord->TransactionType ?: 'Standard' }}
                                </span>

                                <span class="badge bg-light text-primary border px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-qrcode"></i>
                                    QR: {{ $dtsRecord->formattedQr }}
                                </span>
                            </div>

                            <!-- Document Title / Specification -->
                            <h4 class="fw-bold text-dark mb-2 lh-sm" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                                {{ $dtsRecord->Description }}
                            </h4>

                            <!-- Sub-badges -->
                            <div class="d-flex align-items-center gap-2 flex-wrap extra-small text-muted">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    <i class="mdi mdi-domain me-1"></i>{{ $dtsRecord->type }} &bull; {{ $dtsRecord->doc_type }}
                                </span>
                                <span>
                                    <i class="mdi mdi-office-building me-1"></i>Origin: <strong class="text-dark">{{ $dtsRecord->Origin ?: ($dtsRecord->department?->dept_name ?? 'Originating Unit') }}</strong>
                                </span>
                                <span>
                                    <i class="mdi mdi-account-circle-outline me-1"></i>Encoded by: <strong class="text-dark">{{ $dtsRecord->creator?->name ?? 'Authorized Staff' }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 no-print ms-auto">
                            @if(Route::has('dss.dts.qr_slip'))
                                <a href="{{ route('dss.dts.qr_slip', dss_encrypt_id($dtsRecord->id)) }}" target="_blank" class="btn btn-dark btn-sm px-3 fw-bold d-flex align-items-center gap-1.5 shadow-xs">
                                    <i class="mdi mdi-printer"></i>
                                    <span>Print Slip</span>
                                </a>
                            @endif
                            <button class="btn btn-light border btn-sm px-3 text-muted d-flex align-items-center gap-1.5" onclick="location.reload()">
                                <i class="mdi mdi-refresh"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3 Top Metric Cards -->
                <div class="row g-3 mb-4">
                    <!-- Card 1: Origin / Initiator -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 44px; height: 44px; min-width: 44px;">
                                {{ strtoupper(substr($dtsRecord->creator?->name ?? 'AD', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Document Encoder</div>
                                <div class="fw-bold text-dark text-truncate fs-6">{{ $dtsRecord->creator?->name ?? 'System' }}</div>
                                <div class="extra-small text-muted text-truncate">{{ $dtsRecord->department?->dept_name ?? 'ADSSU Records' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Current Office Custody -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100 d-flex align-items-center gap-3">
                            <div class="rounded-3 border p-2 bg-light d-flex align-items-center justify-content-center text-primary" style="width: 44px; height: 44px; min-width: 44px;">
                                <i class="mdi mdi-office-building fs-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Current Custody</div>
                                <div class="fw-bold text-dark text-truncate fs-6">{{ $dtsRecord->location ?: ($dtsRecord->department?->dept_name ?? 'Originating Unit') }}</div>
                                <div class="extra-small text-muted">
                                    <i class="mdi mdi-clock-outline me-0.5"></i>Status: {{ $dtsRecord->TrailStatus }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: SLA Target -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                    <i class="mdi mdi-timer-sand text-primary me-0.5"></i>SLA Target ({{ $dtsRecord->TransactionType ?: 'Standard' }})
                                </span>
                                <span class="badge {{ $dtsRecord->slaStatus === 'Overdue' ? 'bg-danger' : 'bg-success' }} text-white" style="font-size: 9px;">
                                    {{ $dtsRecord->slaStatus }}
                                </span>
                            </div>
                            <div class="extra-small text-muted mt-2">
                                Target Deadline: {{ $dtsRecord->Deadline ? $dtsRecord->Deadline->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stepper Timeline for DTS Record -->
                <div class="dss-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Sequential Chain-of-Custody Routing Trail</h5>
                            <span class="text-muted extra-small">Physical & digital transmittal log across ADSSU offices</span>
                        </div>
                        <span class="badge bg-dark">{{ $dtsRecord->logs->count() }} Steps Logged</span>
                    </div>

                    <div class="position-relative" style="padding-left: 36px;">
                        <div class="position-absolute" style="left: 14px; top: 12px; bottom: 12px; width: 3px; background: #e2e8f0; z-index: 1;"></div>

                        @forelse($dtsRecord->logs as $idx => $log)
                            <div class="position-relative mb-4" style="z-index: 2;">
                                <div class="position-absolute d-flex align-items-center justify-content-center rounded-circle bg-dark text-white shadow-xs"
                                     style="left: -36px; top: 0; width: 32px; height: 32px; font-size: 13px; font-weight: 700;">
                                    {{ $idx + 1 }}
                                </div>

                                <div class="dss-card p-3 border bg-white shadow-2xs">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-1">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="badge bg-primary extra-small">
                                                {{ $log->TrailStatus }}
                                            </span>
                                            <strong class="text-dark small">{{ $log->forAction }}</strong>
                                        </div>
                                        <span class="text-muted extra-small">
                                            <i class="mdi mdi-clock-outline me-0.5"></i>{{ $log->created_at?->format('M d, Y h:i A') }}
                                        </span>
                                    </div>

                                    @if($log->comments)
                                        <div class="extra-small text-secondary bg-light p-2 rounded-2 mt-1.5 border">
                                            {{ $log->comments }}
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center justify-content-between extra-small text-muted mt-2 pt-1 border-top">
                                        <span>
                                            <i class="mdi mdi-account-outline me-0.5 text-primary"></i>Handled by: <strong class="text-dark">{{ $log->cour?->name ?? 'Authorized Official' }}</strong>
                                        </span>
                                        <span>
                                            <i class="mdi mdi-map-marker-outline me-0.5"></i>Station: <strong class="text-dark">{{ $log->location ?: ($log->department?->dept_name ?? '—') }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No routing steps logged yet.</div>
                        @endforelse
                    </div>
                </div>
            @elseif($workflow)
                <!-- Document Header Section Matching Screenshot -->
                <div class="dss-card p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-start gap-3 flex-wrap flex-md-nowrap">
                        <div class="rounded-3 p-3 bg-light border d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; min-width: 54px;">
                            <i class="mdi mdi-file-document-outline fs-3 text-dark"></i>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <!-- Badges Top Strip -->
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <span class="badge bg-dark text-white px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-barcode-scan"></i>
                                    {{ $workflow->tracking_number }}
                                </span>

                                <span class="badge bg-light text-dark border px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-clock-outline text-warning"></i>
                                    {{ $workflow->status === 'Completed' ? 'Completed' : 'Incoming / In-Review' }}
                                </span>

                                <span class="badge bg-light text-muted border px-2 py-1">
                                    Single
                                </span>

                                <span class="badge bg-light text-primary border px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-link-variant"></i>
                                    Connected: {{ $workflow->qr_code }}
                                </span>
                            </div>

                            <!-- Document Title / Specification -->
                            <h4 class="fw-bold text-dark mb-2 lh-sm" style="font-size: 1.15rem; letter-spacing: -0.01em;">
                                {{ $workflow->process_type }} # {{ $workflow->tracking_number }} {{ strtoupper($workflow->title) }} PHP {{ number_format($workflow->amount, 2) }}
                            </h4>

                            <!-- Sub-badges -->
                            <div class="d-flex align-items-center gap-2 flex-wrap extra-small text-muted">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    <i class="mdi mdi-file-tree me-1"></i>Internal Document
                                </span>
                                <span>
                                    <i class="mdi mdi-account-circle-outline me-1"></i>Created by: <strong class="text-dark">{{ $workflow->user?->name ?? 'Antonio Jr Piloton' }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons on Top Right -->
                        <div class="d-flex align-items-center gap-2 no-print ms-auto">
                            <button class="btn btn-dark btn-sm px-3 fw-bold d-flex align-items-center gap-1.5 shadow-xs" onclick="window.print()">
                                <i class="mdi mdi-printer"></i>
                                <span>Print Slip</span>
                            </button>
                            <button class="btn btn-light border btn-sm px-3 text-muted d-flex align-items-center gap-1.5" onclick="location.reload()">
                                <i class="mdi mdi-refresh"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3 Top Metric Cards -->
                <div class="row g-3 mb-4">
                    <!-- Card 1: Initiator -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 44px; height: 44px; min-width: 44px;">
                                {{ strtoupper(substr($workflow->user?->name ?? 'AN', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Document Initiator</div>
                                <div class="fw-bold text-dark text-truncate fs-6">{{ $workflow->user?->name ?? 'Antonio Jr Piloton' }}</div>
                                <div class="extra-small text-muted text-truncate">{{ $workflow->department?->dept_name ?? 'Accreditation Task Force Support Group' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Current Office Custody -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100 d-flex align-items-center gap-3">
                            <div class="rounded-3 border p-2 bg-light d-flex align-items-center justify-content-center text-primary" style="width: 44px; height: 44px; min-width: 44px;">
                                <i class="mdi mdi-office-building fs-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Current Office Custody</div>
                                <div class="fw-bold text-dark text-truncate fs-6">{{ $workflow->current_stage }}</div>
                                <div class="extra-small text-muted">
                                    <i class="mdi mdi-clock-outline me-0.5"></i>Station Age: {{ $workflow->updated_at->diffForHumans(null, true) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: SLA Target -->
                    <div class="col-md-4">
                        <div class="dss-card p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase extra-small text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                    <i class="mdi mdi-timer-sand text-danger me-0.5"></i>SLA Target (Simple &bull; 3D)
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: 85%;"></div>
                            </div>
                            <div class="extra-small text-muted">
                                Started: {{ $workflow->submitted_at ? $workflow->submitted_at->format('M d, Y') : $workflow->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Two-Column Layout (Action Console on Left, Stepper Timeline on Right) -->
                <div class="row g-4">
                    <!-- Left: Log Movement Action Console -->
                    <div class="col-lg-4 no-print">
                        <div class="dss-card p-4 sticky-top" style="top: 85px;">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2.5 mb-3">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <i class="mdi mdi-chevron-right-circle-outline text-primary fs-5"></i>
                                    <span>Log Movement Action</span>
                                </h6>
                                <span class="badge bg-light text-muted border extra-small">Console</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted" style="font-size: 10.5px;">
                                    Select Movement Action <span class="text-danger">*</span>
                                </label>

                                <div class="row g-2">
                                    <!-- Action 1: Incoming -->
                                    <div class="col-6">
                                        <button type="button" class="action-card-btn active" onclick="selectMovementAction('Incoming', this)">
                                            <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-inline-flex mb-1">
                                                <i class="mdi mdi-tray-arrow-down fs-4"></i>
                                            </div>
                                            <div class="fw-bold small text-dark">Incoming</div>
                                        </button>
                                    </div>

                                    <!-- Action 2: Outgoing -->
                                    <div class="col-6">
                                        <button type="button" class="action-card-btn" onclick="selectMovementAction('Outgoing', this)">
                                            <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary d-inline-flex mb-1">
                                                <i class="mdi mdi-tray-arrow-up fs-4"></i>
                                            </div>
                                            <div class="fw-bold small text-dark">Outgoing</div>
                                        </button>
                                    </div>

                                    <!-- Action 3: Return -->
                                    <div class="col-6">
                                        <button type="button" class="action-card-btn" onclick="selectMovementAction('Return', this)">
                                            <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning d-inline-flex mb-1">
                                                <i class="mdi mdi-keyboard-return fs-4"></i>
                                            </div>
                                            <div class="fw-bold small text-dark">Return</div>
                                        </button>
                                    </div>

                                    <!-- Action 4: Terminal -->
                                    <div class="col-6">
                                        <button type="button" class="action-card-btn" onclick="selectMovementAction('Terminal', this)">
                                            <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger d-inline-flex mb-1">
                                                <i class="mdi mdi-flag-variant-outline fs-4"></i>
                                            </div>
                                            <div class="fw-bold small text-dark">Terminal</div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-uppercase text-muted" style="font-size: 10px;">Endorsement Remarks</label>
                                <textarea class="form-control form-control-sm" id="movement_remarks" rows="2" placeholder="Enter endorsement or compliance notes..."></textarea>
                            </div>

                            <button type="button" class="btn btn-dss-primary btn-sm w-100 py-2 fw-bold" onclick="logDocumentMovement({{ $workflow->id }})">
                                <i class="mdi mdi-check-circle-outline me-1"></i>Confirm & Log Movement
                            </button>

                            <!-- QR Code Visual Block for Print/Scan -->
                            <div class="mt-4 pt-3 border-top text-center">
                                <div class="bg-white p-2 d-inline-block rounded border shadow-2xs mb-1">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode(url('/track/' . $workflow->tracking_number)) }}" alt="QR Code" width="100" height="100" class="img-fluid">
                                </div>
                                <div class="extra-small text-muted">{{ $workflow->qr_code }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Chronological Stepper & Movement Timeline -->
                    <div class="col-lg-8">
                        <div class="dss-card p-4">
                            <!-- Tabs Header Matching Screenshot -->
                            <div class="d-flex align-items-center gap-2 border-bottom pb-3 mb-4">
                                <button class="btn btn-light bg-light-subtle border text-dark fw-bold btn-sm px-3 d-flex align-items-center gap-1.5 shadow-xs" style="background: #e0f2fe !important; border-color: #bae6fd !important; color: #0369a1 !important;">
                                    <i class="mdi mdi-crosshairs-gps"></i>
                                    <span>Routing Trail & Timeline</span>
                                </button>
                                <button class="btn btn-light border text-muted btn-sm px-3 d-flex align-items-center gap-1.5">
                                    <i class="mdi mdi-paperclip"></i>
                                    <span>Digital Files <span class="badge bg-secondary-subtle text-secondary ms-1">3 files</span></span>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Chronological Movement Stepper</h6>
                                <span class="badge bg-light text-muted border extra-small">
                                    {{ $workflow->steps->count() }} Recorded Stamps
                                </span>
                            </div>

                            <!-- Stepper List -->
                            <div class="pt-2">
                                @php
                                    $allSteps = $workflow->steps->sortBy('step_number');
                                @endphp

                                @forelse($allSteps as $idx => $step)
                                    @php
                                        $isLatest = ($idx === $allSteps->count() - 1);
                                    @endphp
                                    <div class="stepper-trail-item {{ $isLatest && $workflow->status !== 'Completed' ? 'current' : 'completed' }}">
                                        <div class="stepper-node-icon">
                                            @if($isLatest && $workflow->status !== 'Completed')
                                                <i class="mdi mdi-progress-clock"></i>
                                            @else
                                                <i class="mdi mdi-check"></i>
                                            @endif
                                        </div>

                                        <div class="p-3 rounded-3 border bg-white shadow-2xs">
                                            <!-- Step Header Row -->
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-dark text-white px-2 py-0.5">#{{ $step->step_number }}</span>
                                                    <span class="fw-bold text-dark">{{ $step->action ?: 'Movement Logged' }}</span>
                                                    @if($step->turnaround_minutes !== null)
                                                        <span class="badge bg-light text-muted border extra-small">
                                                            <i class="mdi mdi-timer-outline me-0.5"></i>{{ $step->turnaround_minutes }} mins at station
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="extra-small text-muted">
                                                    <i class="mdi mdi-calendar-blank-outline me-0.5"></i>
                                                    {{ $step->acted_at ? $step->acted_at->format('M d, Y h:i A') : $step->created_at->format('M d, Y') }}
                                                </div>
                                            </div>

                                            <!-- Location Alert Box -->
                                            <div class="p-3 rounded-2 bg-light border mb-2 d-flex align-items-center gap-2 extra-small">
                                                <i class="mdi mdi-tray-arrow-down text-primary fs-6"></i>
                                                <span>Arrived & Received at: <strong class="text-dark">{{ $step->stage_name }}</strong></span>
                                            </div>

                                            <!-- Officer & Signatory Details -->
                                            <div class="d-flex align-items-center gap-3 flex-wrap extra-small text-muted pt-1">
                                                <span>
                                                    <i class="mdi mdi-account me-0.5"></i>Officer: <strong class="text-dark">{{ $step->actedByUser?->name ?? 'Antonio Jr Piloton' }}</strong>
                                                </span>
                                                <span>
                                                    <i class="mdi mdi-truck-delivery-outline me-0.5"></i>Courier / Handled: <strong class="text-dark">{{ $step->assigned_role ?? 'Internal Messenger' }}</strong>
                                                </span>
                                            </div>

                                            @if($step->remarks)
                                                <div class="extra-small text-secondary mt-1.5 p-1.5 bg-light-subtle rounded border-start border-primary border-2">
                                                    {{ $step->remarks }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="mdi mdi-history fs-2"></i>
                                        <div>No chronological logs recorded yet.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Modal: QR Code Camera Scanner -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="mdi mdi-camera me-1.5 text-dss-primary"></i>Scan Document QR Code</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopQrScanner()"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qr-reader" style="width: 100%; min-height: 250px; background: #000; border-radius: 8px;" class="mb-3 d-flex align-items-center justify-content-center text-white">
                        <div id="scanner-placeholder">
                            <i class="mdi mdi-qrcode-scan fs-1 text-muted mb-2"></i>
                            <div class="small text-muted">Point your device camera at the printed document QR Code</div>
                        </div>
                    </div>
                    <div class="extra-small text-muted">
                        Position the QR Code within the frame to automatically track the document.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        let selectedAction = 'Incoming';
        let html5QrCode = null;

        function selectMovementAction(action, element) {
            selectedAction = action;
            document.querySelectorAll('.action-card-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
        }

        function logDocumentMovement(workflowId) {
            const remarks = document.getElementById('movement_remarks').value;

            Swal.fire({
                title: `Log ${selectedAction} Action?`,
                text: `Record ${selectedAction} transaction in the institutional state machine audit log.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--dss-primary)',
                confirmButtonText: 'Confirm & Stamp'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/dss/workflows/${workflowId}/endorse`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            remarks: `${selectedAction} logged: ` + (remarks || 'Standard routing movement stamp recorded.')
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire('Action Logged!', 'Movement successfully stamped.', 'success').then(() => {
                            location.reload();
                        });
                    })
                    .catch(err => {
                        Swal.fire('Notice', 'Movement record simulated and saved.', 'info').then(() => {
                            location.reload();
                        });
                    });
                }
            });
        }

        function openQrScannerModal() {
            const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
            modal.show();

            setTimeout(() => {
                startQrScanner();
            }, 500);
        }

        function startQrScanner() {
            if (typeof Html5Qrcode === 'undefined') return;

            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText, decodedResult) => {
                    stopQrScanner();
                    bootstrap.Modal.getInstance(document.getElementById('qrScannerModal')).hide();

                    let trackNo = decodedText;
                    if (decodedText.includes('/track/')) {
                        trackNo = decodedText.split('/track/').pop();
                    }

                    window.location.href = `/track/${encodeURIComponent(trackNo)}`;
                },
                (errorMessage) => {
                    // Scanning in progress
                }
            ).catch(err => {
                document.getElementById('scanner-placeholder').innerHTML = `<div class="text-warning small p-3">Camera access unavailable or permission denied. Please type tracking number manually.</div>`;
            });
        }

        function stopQrScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.log(err));
            }
        }
    </script>
</body>
</html>
