<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCOMPLISHMENT REPORT — {{ $proposal->proposal_code }} — SNSU RIE</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #111827;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
        }
        .report-page-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 16mm 18mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            position: relative;
        }
        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }
        .univ-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .univ-header-table td {
            vertical-align: middle;
        }
        .univ-header-text {
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #334155;
        }
        .univ-name {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 2px 0;
        }
        .green-rule {
            height: 3.5px;
            background-color: #15803d;
            width: 100%;
            margin: 10px 0 16px 0;
        }
        .report-title-box {
            text-align: center;
            margin-bottom: 18px;
        }
        .report-supertitle {
            font-size: 8.5pt;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #047857;
            margin-bottom: 2px;
        }
        .report-main-title {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.2px;
            margin: 0;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 9.5pt;
            color: #64748b;
            font-style: italic;
            margin-top: 3px;
        }
        .section-header {
            background: #f8fafc;
            border-left: 4px solid #15803d;
            padding: 5px 10px;
            font-weight: 700;
            font-size: 10.5pt;
            color: #0f172a;
            margin: 16px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }
        .table-data th, .table-data td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: top;
        }
        .table-data th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
        }
        .metric-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 9pt;
        }
        .pill-success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .pill-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .narrative-block {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #1e293b;
            margin-bottom: 10px;
            text-align: justify;
        }
        .sig-block {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px 16px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 4px;
        }
        .sig-name {
            font-weight: 700;
            font-size: 10pt;
            text-transform: uppercase;
            color: #000;
        }
        .sig-title {
            font-size: 8.5pt;
            color: #334155;
        }
        .photo-evidence-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .evidence-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            text-align: center;
        }
        .evidence-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        .evidence-card .caption {
            font-size: 8pt;
            color: #475569;
            padding: 4px 6px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        @media print {
            body {
                background: #ffffff;
            }
            .print-toolbar {
                display: none !important;
            }
            .report-page-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
                max-width: 100%;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Print Navigation Toolbar (Hidden during print) -->
    <div class="print-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success font-mono px-2.5 py-1">SNSU RIE DSS</span>
            <span class="fw-bold fs-6">Official Extension Accomplishment &amp; Terminal Narrative Report</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.rie.proposals.show', ['id' => $proposal->getRouteKey(), 'tab' => 'accomplishment']) }}" class="btn btn-sm btn-outline-light px-3">
                <i class="mdi mdi-arrow-left me-1"></i>Back to Proposal
            </a>
            <button type="button" class="btn btn-sm btn-success px-3 fw-bold" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i>Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Main Report Document Sheet -->
    <div class="report-page-container">
        <!-- University Institutional Header -->
        <table class="univ-header-table">
            <tr>
                <td style="width: 75px; text-align: left;">
                    @if(!empty($institution->logo_path))
                        <img src="{{ asset($institution->logo_path) }}" alt="Logo" style="height: 68px; width: 68px; object-fit: contain;">
                    @else
                        <img src="{{ asset('assets/images/snsu_logo.png') }}" alt="SNSU" style="height: 68px; width: 68px; object-fit: contain;" onerror="this.style.display='none'">
                    @endif
                </td>
                <td class="univ-header-text">
                    <div>Republic of the Philippines</div>
                    <div class="univ-name">{{ strtoupper($institution->institution_name ?? 'SURIGAO DEL NORTE STATE UNIVERSITY') }}</div>
                    <div>{{ $institution->tagline ?? 'Caraga Regional Knowledge & Innovation Hub' }} &bull; Surigao City, Philippines</div>
                    <div style="font-weight: bold; color: #15803d; margin-top: 1px;">DIRECTORATE OF EXTENSION SERVICES &amp; COMMUNITY ENGAGEMENT</div>
                </td>
                <td style="width: 75px; text-align: right;">
                    @if(!empty($proposal->tracking_number))
                        <div style="text-align: center; border: 1px dashed #94a3b8; padding: 4px; border-radius: 4px;">
                            <div style="font-family: monospace; font-size: 7.5pt; font-weight: bold; color: #047857;">SNSU DTS #</div>
                            <div style="font-family: monospace; font-size: 8.5pt; font-weight: bold; color: #0f172a;">{{ $proposal->tracking_number }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="green-rule"></div>

        <!-- Document Title -->
        <div class="report-title-box">
            <div class="report-supertitle">OFFICIAL EXTENSION SERVICES MONITORING &amp; EVALUATION</div>
            <h1 class="report-main-title">PROJECT TERMINAL ACCOMPLISHMENT &amp; NARRATIVE REPORT</h1>
            <div class="report-subtitle">Documentation of Actual Beneficiary Reach, Deliverables Produced, and Fund Liquidation</div>
        </div>

        <!-- I. Executive Project Profile -->
        <div class="section-header">I. Executive Project Profile</div>
        <table class="table-data">
            <tr>
                <th style="width: 25%;">Project Title</th>
                <td colspan="3" style="font-weight: bold; color: #0f172a;">{{ $proposal->title }}</td>
            </tr>
            <tr>
                <th>Proposal Reference Code</th>
                <td style="width: 25%; font-family: monospace; font-weight: bold;">{{ $proposal->proposal_code }}</td>
                <th style="width: 25%;">Implementation Status</th>
                <td style="width: 25%;">
                    @php $st = $acc['implementation_status'] ?? 'Completed'; @endphp
                    <span class="metric-pill {{ $st === 'Completed' ? 'pill-success' : ($st === 'Ongoing' ? 'pill-primary' : 'pill-warning') }}">
                        {{ $st }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Project Leader</th>
                <td>{{ $proposal->project_leader_name }}</td>
                <th>Proponent Designation</th>
                <td>{{ $proposal->project_leader_title ?: 'Project Leader' }}</td>
            </tr>
            <tr>
                <th>Implementing College / Dept</th>
                <td>{{ $proposal->academicProgram?->name ?: ($proposal->implementing_agency ?: 'SNSU Academic Unit') }}</td>
                <th>Campus / Location</th>
                <td>{{ $proposal->campus?->name ?: 'Surigao City Main Campus' }} &bull; {{ $proposal->project_location ?: 'Caraga Region' }}</td>
            </tr>
            <tr>
                <th>Implementation Period</th>
                <td>{{ $proposal->duration_text ?: ($proposal->duration_start?->format('M d, Y') . ' – ' . $proposal->duration_end?->format('M d, Y')) }}</td>
                <th>Date of Actual Completion</th>
                <td style="font-weight: bold; color: #047857;">
                    {{ !empty($acc['date_completed']) ? \Carbon\Carbon::parse($acc['date_completed'])->format('F d, Y') : ($proposal->duration_end?->format('F d, Y') ?: 'Completed as Scheduled') }}
                </td>
            </tr>
        </table>

        <!-- II. Community Beneficiaries Reached -->
        <div class="section-header">II. Actual Community Beneficiaries Reached</div>
        <table class="table-data text-center">
            <thead>
                <tr>
                    <th class="text-start">Target Community Category</th>
                    <th style="width: 18%;">Planned Target</th>
                    <th style="width: 18%;">Male Reached</th>
                    <th style="width: 18%;">Female Reached</th>
                    <th style="width: 20%; background-color: #ecfdf5; color: #065f46;">Total Beneficiaries</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start">
                        <strong>{{ $proposal->beneficiaries ?: 'General Community Beneficiaries' }}</strong>
                        @if(!empty($acc['actual_beneficiaries_sectors']))
                            <div class="text-muted" style="font-size: 8.5pt;">Sectors: {{ $acc['actual_beneficiaries_sectors'] }}</div>
                        @endif
                    </td>
                    <td style="font-family: monospace;">{{ $proposal->beneficiaries ? Str::limit($proposal->beneficiaries, 20) : '—' }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #2563eb;">{{ number_format((int) ($acc['actual_beneficiaries_male'] ?? 0)) }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #dc2626;">{{ number_format((int) ($acc['actual_beneficiaries_female'] ?? 0)) }}</td>
                    <td style="font-family: monospace; font-weight: 800; font-size: 11pt; background-color: #ecfdf5; color: #047857;">
                        {{ number_format((int) ($acc['actual_beneficiaries_total'] ?? 0)) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- III. Financial Liquidation & Utilization -->
        <div class="section-header">III. Financial Liquidation &amp; Fund Utilization Summary</div>
        <table class="table-data text-center">
            <thead>
                <tr>
                    <th class="text-start">Funding Source</th>
                    <th style="width: 22%;">Approved Allocation</th>
                    <th style="width: 22%;">Actual Disbursed</th>
                    <th style="width: 22%;">Unexpended Balance</th>
                    <th style="width: 16%; background-color: #ecfdf5; color: #065f46;">Utilization Rate</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-start">
                        <strong>{{ $proposal->fund_source_internal ?: 'SNSU Institutional Extension Fund' }}</strong>
                        @if($proposal->fund_source_external)
                            <div class="text-muted" style="font-size: 8.5pt;">Partner Share: {{ $proposal->fund_source_external }}</div>
                        @endif
                    </td>
                    <td style="font-family: monospace;">₱{{ number_format($plannedBudget, 2) }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #0f172a;">₱{{ number_format($actualSpent, 2) }}</td>
                    <td style="font-family: monospace; color: #64748b;">₱{{ number_format($remainingBudget, 2) }}</td>
                    <td style="font-family: monospace; font-weight: 800; background-color: #ecfdf5; color: #047857;">
                        {{ $utilizationRate }}%
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- IV. Key Accomplishments & Deliverables Produced -->
        <div class="section-header">IV. Summary of Physical Outputs &amp; Accomplishments</div>
        <div class="narrative-block">
            @if(!empty($acc['summary_accomplishments']))
                {!! nl2br(e($acc['summary_accomplishments'])) !!}
            @else
                <p class="mb-0 text-muted fst-italic">
                    The extension project was completed in full alignment with the approved project proposal and institutional work plan. Modular instruction, technical coaching, and hands-on skill development were successfully transferred to the designated participants. Deliverables, manuals, and technical outputs were turned over to the partner community stakeholders.
                </p>
            @endif
        </div>

        <!-- V. Problems Encountered & Solutions Adopted -->
        <div class="section-header">V. Field Challenges &amp; Remedial Solutions</div>
        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 50%;">Constraints &amp; Challenges Encountered</th>
                    <th style="width: 50%;">Remedial Solutions Adopted</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if(!empty($acc['problems_encountered']))
                            {!! nl2br(e($acc['problems_encountered'])) !!}
                        @else
                            <span class="text-muted fst-italic">Field execution proceeded smoothly with minor weather and logistical scheduling adjustments handled proactively by the project team.</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($acc['solutions_adopted']))
                            {!! nl2br(e($acc['solutions_adopted'])) !!}
                        @else
                            <span class="text-muted fst-italic">Close coordination with local barangay unit officials ensured venue availability and flexible participation hours.</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- VI. Community Feedback & Impact Narrative -->
        <div class="section-header">VI. Community Testimonials &amp; Qualitative Impact</div>
        <div class="narrative-block">
            @if(!empty($acc['impact_feedback']))
                {!! nl2br(e($acc['impact_feedback'])) !!}
            @else
                <p class="mb-0 text-muted fst-italic">
                    Participants expressed sincere appreciation for SNSU's community extension outreach, noting substantial enhancements in practical competence and immediate relevance to their local livelihood and administrative practices.
                </p>
            @endif
        </div>

        <!-- VII. Photographic Documentation Gallery -->
        @if($proposal->uploadedDocuments && $proposal->uploadedDocuments->count() > 0)
            <div class="section-header">VII. Terminal Photographic Evidence &amp; Attachments</div>
            <div class="photo-evidence-grid">
                @foreach($proposal->uploadedDocuments->take(4) as $doc)
                    <div class="evidence-card">
                        @if(in_array(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                            <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->document_title }}">
                        @else
                            <div class="p-3 bg-light text-center" style="height: 150px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                <i class="mdi mdi-file-document-check text-primary" style="font-size: 38px;"></i>
                                <span class="fw-bold mt-1" style="font-size: 8.5pt;">{{ Str::limit($doc->document_title, 25) }}</span>
                            </div>
                        @endif
                        <div class="caption">
                            <strong>{{ Str::limit($doc->document_title, 32) }}</strong>
                            <div class="text-muted" style="font-size: 7.5pt;">{{ $doc->category ?: 'Official Attachment' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- VIII. Signatures & Institutional Endorsement -->
        <div class="sig-block">
            <div class="section-header">VIII. Verification &amp; Institutional Endorsement</div>
            <table class="sig-table">
                <tr>
                    <td>
                        <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #475569;">Prepared &amp; Submitted By:</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $proposal->project_leader_name }}</div>
                        <div class="sig-title">{{ $proposal->project_leader_title ?: 'Project Leader / Proponent' }}</div>
                        <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: {{ !empty($acc['updated_at']) ? \Carbon\Carbon::parse($acc['updated_at'])->format('M d, Y') : now()->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #475569;">Verified &amp; Noted By:</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">COLLEGE DEAN / PROGRAM CHAIR</div>
                        <div class="sig-title">Office of the College Dean</div>
                        <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #475569;">Concurred &amp; Accepted By:</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">ALVIE ELMA C. PLAZA, MA</div>
                        <div class="sig-title">Director, University Extension Services</div>
                        <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                    </td>
                    <td>
                        <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #475569;">Approved &amp; Institutionalized:</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">ROMY JUN A. SUNICO, DTE</div>
                        <div class="sig-title">Vice President for Research, Innovation &amp; Extension</div>
                        <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Document Security Barcode Stamp Footer -->
        <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 7.5pt; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
            <div>
                Generated via <strong>SNSU RIE Decision Support System</strong> &bull; Tracking Ref: <span style="font-family: monospace; font-weight: bold; color: #047857;">{{ $proposal->tracking_number ?: $proposal->proposal_code }}</span>
            </div>
            <div>
                Official Institutional Document &bull; Page 1 of 1
            </div>
        </div>
    </div>

</body>
</html>
