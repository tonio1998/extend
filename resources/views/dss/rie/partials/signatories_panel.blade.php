{{-- ==============================================================================
     REUSABLE 4-TIER DOCUMENT SIGNATORIES PANEL (RIE EXTENSION GOVERNANCE)
     Compatible with: Extension Plans, Extension Proposals, Activity Designs
     ============================================================================== --}}
@php
    $sigs = $signatories ?? [];
    $prep = $sigs['prepared'] ?? [];
    $rev = $sigs['reviewed'] ?? [];
    $noted = $sigs['noted'] ?? [];
    $conc1 = $sigs['concurred_1'] ?? [];
    $conc2 = $sigs['concurred_2'] ?? [];
    $collegeName = $sigs['college_name'] ?? ($sigs['unit']?->unit_name ?? 'Academic / Campus Unit');
@endphp

<div class="card border rounded-3 shadow-2xs mb-4">
    <div class="card-header bg-white py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-2 bg-primary-subtle text-primary">
                <i class="mdi mdi-draw-pen fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark m-0" style="font-size: 14.5px;">
                    Official Document Signatories Matrix
                </h6>
                <div class="text-muted extra-small">
                    Unit Governance: <span class="fw-semibold text-primary">{{ $collegeName }}</span> &bull; 4-Tier Institutional Endorsement
                </div>
            </div>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono">
            <i class="mdi mdi-shield-check me-1"></i>AUTO-RESOLVED
        </span>
    </div>

    <div class="card-body p-3.5">
        <div class="row g-3">
            <!-- 1. Prepared by (Project Leader) -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-3 border bg-white h-100 shadow-2xs d-flex flex-column justify-content-between position-relative overflow-hidden" style="border-top: 3px solid #3b82f6 !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-primary text-white extra-small" style="font-size: 10px;">
                                <i class="mdi mdi-account-star me-0.5"></i> 1. Prepared by
                            </span>
                            <span class="text-muted extra-small font-mono">Proponent</span>
                        </div>
                        <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $prep['name'] ?? 'Project Leader' }}">
                            {{ $prep['name'] ?? 'Project Leader' }}
                        </div>
                        <div class="text-muted extra-small fw-medium mt-1">
                            {{ $prep['designation'] ?? 'Project Leader' }}
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-top extra-small text-primary d-flex align-items-center gap-1">
                        <i class="mdi mdi-check-circle"></i> Faculty Project Lead
                    </div>
                </div>
            </div>

            <!-- 2. Reviewed by (College / Campus Head for Extension) -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-3 border bg-white h-100 shadow-2xs d-flex flex-column justify-content-between position-relative overflow-hidden" style="border-top: 3px solid #06b6d4 !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle extra-small" style="font-size: 10px;">
                                <i class="mdi mdi-file-check-outline me-0.5"></i> 2. Reviewed by
                            </span>
                            <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Unit Dependent</span>
                        </div>
                        <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $rev['name'] ?? 'College Head for Extension' }}">
                            {{ $rev['name'] ?? 'College Head for Extension' }}
                        </div>
                        <div class="text-muted extra-small fw-medium mt-1 text-truncate" title="{{ $rev['designation'] ?? 'College Head for Extension' }}">
                            {{ $rev['designation'] ?? 'College Head for Extension' }}
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-top extra-small text-info d-flex align-items-center gap-1">
                        <i class="mdi mdi-account-tie"></i> Head for Extension
                    </div>
                </div>
            </div>

            <!-- 3. Noted by (College Dean / Campus Director) -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-3 border bg-white h-100 shadow-2xs d-flex flex-column justify-content-between position-relative overflow-hidden" style="border-top: 3px solid #f59e0b !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small" style="font-size: 10px;">
                                <i class="mdi mdi-school-outline me-0.5"></i> 3. Noted by
                            </span>
                            <span class="badge bg-success-subtle text-success extra-small" style="font-size: 9.5px;">Unit Dependent</span>
                        </div>
                        <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $noted['name'] ?? 'College Dean' }}">
                            {{ $noted['name'] ?? 'College Dean' }}
                        </div>
                        <div class="text-muted extra-small fw-medium mt-1 text-truncate" title="{{ $noted['designation'] ?? 'College Dean' }}">
                            {{ $noted['designation'] ?? 'College Dean' }}
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-top extra-small text-warning-emphasis d-flex align-items-center gap-1">
                        <i class="mdi mdi-shield-account"></i> Dean / Campus Director
                    </div>
                </div>
            </div>

            <!-- 4. Concurred by (University Extension Services Office) -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-3 border bg-white h-100 shadow-2xs d-flex flex-column justify-content-between position-relative overflow-hidden" style="border-top: 3px solid #10b981 !important;">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small" style="font-size: 10px;">
                                <i class="mdi mdi-certificate-outline me-0.5"></i> 4. Concurred by
                            </span>
                            <span class="text-muted extra-small">University Level</span>
                        </div>
                        <!-- Specialist V -->
                        <div class="fw-bold text-dark extra-small text-truncate" title="{{ $conc1['name'] ?? 'EVER JOY G. RABADAN, MAEM, MSc.' }}">
                            {{ $conc1['name'] ?? 'EVER JOY G. RABADAN, MAEM, MSc.' }}
                        </div>
                        <div class="text-muted" style="font-size: 10.5px;">
                            {{ $conc1['designation'] ?? 'University Extension Specialist V' }}
                        </div>

                        <!-- Director -->
                        <div class="border-top my-1.5 pt-1.5">
                            <div class="fw-bold text-dark extra-small text-truncate" title="{{ $conc2['name'] ?? 'ALVIE ELMA C. PLAZA, MA' }}">
                                {{ $conc2['name'] ?? 'ALVIE ELMA C. PLAZA, MA' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px;">
                                {{ $conc2['designation'] ?? 'University Extension Director' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-top extra-small text-success d-flex align-items-center gap-1">
                        <i class="mdi mdi-office-building"></i> University Extension Office
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

