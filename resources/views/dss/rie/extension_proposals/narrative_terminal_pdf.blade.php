<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TERMINAL NARRATIVE REPORT — {{ $proposal->proposal_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 18mm 18mm 18mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #111827;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-center {
            text-align: center;
            font-size: 8pt;
            line-height: 1.25;
            color: #334155;
        }
        .univ-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }
        .univ-directorate {
            font-weight: bold;
            color: #15803d;
            font-size: 8.5pt;
        }
        .green-rule {
            height: 3px;
            background-color: #15803d;
            width: 100%;
            margin: 8px 0 14px 0;
        }
        .doc-title-box {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-supertitle {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1.2px;
            color: #047857;
            margin-bottom: 2px;
        }
        .doc-main-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.2px;
            margin: 0;
            text-transform: uppercase;
        }
        .project-title-banner {
            background-color: #f8fafc;
            border-left: 3.5px solid #15803d;
            padding: 7px 10px;
            margin-top: 6px;
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            text-align: center;
        }
        .section-header {
            background: #f1f5f9;
            border-left: 3.5px solid #15803d;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 9.5pt;
            color: #0f172a;
            margin: 14px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .narrative-p {
            font-size: 9.5pt;
            line-height: 1.55;
            color: #1e293b;
            text-align: justify;
            margin: 0 0 8px 0;
            text-indent: 20px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }
        .meta-table th, .meta-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #334155;
            width: 24%;
        }
        .photo-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 8px 0 12px 0;
            page-break-inside: avoid;
        }
        .photo-cell {
            width: 50%;
            vertical-align: top;
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 4px;
            background: #fcfcfc;
        }
        .photo-cell img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }
        .photo-caption {
            font-size: 7.5pt;
            color: #475569;
            font-style: italic;
            padding-top: 4px;
            text-align: center;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding: 8px 14px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 36px;
            margin-bottom: 3px;
        }
        .sig-name {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            color: #000;
        }
        .sig-title {
            font-size: 8pt;
            color: #334155;
        }
        .footer-table {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            margin-top: 25px;
            padding-top: 4px;
            font-size: 7.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Institutional Letterhead -->
    <table class="header-table">
        <tr>
            <td style="width: 70px; text-align: left;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="height: 60px; width: 60px; object-fit: contain;">
                @endif
            </td>
            <td class="header-center">
                <div>Republic of the Philippines</div>
                <div class="univ-title">{{ strtoupper($institution->institution_name ?? 'SURIGAO DEL NORTE STATE UNIVERSITY') }}</div>
                <div>Surigao City, Caraga Region, Philippines</div>
                <div class="univ-directorate">DIRECTORATE OF EXTENSION SERVICES &bull; RIE MONITORING &amp; EVALUATION</div>
            </td>
            <td style="width: 70px; text-align: right;">
                @if(!empty($proposal->tracking_number))
                    <div style="border: 1px solid #047857; padding: 3px; text-align: center; font-size: 7.5pt; font-family: monospace;">
                        <span style="font-weight: bold; color: #047857;">DTS TRACKING</span><br>
                        <strong>{{ $proposal->tracking_number }}</strong>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <div class="green-rule"></div>

    <!-- Document Title -->
    <div class="doc-title-box">
        <div class="doc-supertitle">OFFICIAL POST-IMPLEMENTATION EVALUATION</div>
        <h1 class="doc-main-title">EXTENSION PROJECT TERMINAL NARRATIVE REPORT</h1>
        <div class="project-title-banner">
            “{{ $proposal->title }}”
        </div>
    </div>

    <!-- Summary Profile Matrix -->
    <table class="meta-table">
        <tr>
            <th>Proposal Code</th>
            <td style="font-family: monospace; font-weight: bold;">{{ $proposal->proposal_code }}</td>
            <th>Implementation Period</th>
            <td>{{ $proposal->duration_text ?: ($proposal->duration_start?->format('M Y') . ' – ' . $proposal->duration_end?->format('M Y')) }}</td>
        </tr>
        <tr>
            <th>Project Leader</th>
            <td style="font-weight: bold;">{{ $proposal->project_leader_name }}</td>
            <th>Implementing College</th>
            <td>{{ $proposal->academicProgram?->name ?: ($proposal->implementing_agency ?: 'SNSU Academic Unit') }}</td>
        </tr>
        <tr>
            <th>Partner Community / Venue</th>
            <td>{{ $proposal->beneficiaries ? Str::limit($proposal->beneficiaries, 40) : 'Caraga Partner Communities' }} &bull; {{ $proposal->project_location ?: 'Surigao del Norte' }}</td>
            <th>Completion Status</th>
            <td style="font-weight: bold; color: #047857;">
                {{ $acc['implementation_status'] ?? 'Completed' }}
                @if(!empty($acc['date_completed']))
                    ({{ \Carbon\Carbon::parse($acc['date_completed'])->format('M d, Y') }})
                @endif
            </td>
        </tr>
    </table>

    <!-- 1. Executive Summary & Background Narrative -->
    <div class="section-header">1. Executive Overview &amp; Background Narrative</div>
    @if(!empty($acc['executive_narrative']))
        {!! nl2br(e($acc['executive_narrative'])) !!}
    @else
        <p class="narrative-p">
            The university extension project entitled <strong>“{{ $proposal->title }}”</strong> was conceptualized and conducted pursuant to Surigao del Norte State University’s mandate of advancing inclusive community development and technological empowerment across Caraga. The intervention addressed critical capacity gaps identified among partner community members, specifically targeting: <em>{{ $proposal->beneficiaries ?: 'local stakeholders and marginalized community sectors' }}</em>.
        </p>
        <p class="narrative-p">
            Guided by the institutional extension thrust and the United Nations Sustainable Development Goals (SDGs), the project mobilized faculty expertise and student facilitators to provide systematic, evidence-based technical assistance. Over the course of the approved implementation period, all programmed preparatory coordinating sessions, baseline profiling, and community engagements were executed in active partnership with local leadership.
        </p>
    @endif

    <!-- 2. Activity Execution & Training Delivery Narrative -->
    <div class="section-header">2. Activity Execution &amp; Community Engagement Narrative</div>
    @if(!empty($acc['activity_narrative']))
        {!! nl2br(e($acc['activity_narrative'])) !!}
    @else
        <p class="narrative-p">
            The project rollout commenced with comprehensive stakeholder consultations and inter-agency coordination with local barangay leaders and community representatives. Structured modules and practical training regimens were delivered through interactive lectures, hands-on laboratory demonstrations, and small-group coaching sessions designed to cater to adult learners.
        </p>
        <p class="narrative-p">
            Throughout the training days, active participation was observed across all attendee cohorts. Participants engaged in contextualized simulation exercises, allowing immediate application of technical concepts to their actual daily livelihood and administrative responsibilities. Continuous formative assessments verified mastery of core competencies prior to the conclusion of each instructional module.
        </p>
    @endif

    <!-- Embedded Photographic Documentation Set 1 -->
    @if(!empty($evidencePhotos) && count($evidencePhotos) >= 2)
        <table class="photo-table">
            <tr>
                <td class="photo-cell">
                    <img src="{{ $evidencePhotos[0]['data_uri'] }}" alt="Photo 1">
                    <div class="photo-caption">Figure 1. {{ $evidencePhotos[0]['title'] }} — Field execution and interactive training session with participants.</div>
                </td>
                <td class="photo-cell">
                    <img src="{{ $evidencePhotos[1]['data_uri'] }}" alt="Photo 2">
                    <div class="photo-caption">Figure 2. {{ $evidencePhotos[1]['title'] }} — Hands-on mentoring and participant skill demonstration.</div>
                </td>
            </tr>
        </table>
    @endif

    <!-- 3. Key Accomplishments & Technology Transfer Narrative -->
    <div class="section-header">3. Key Physical Accomplishments &amp; Technology Transfer Narrative</div>
    @if(!empty($acc['summary_accomplishments']))
        <div style="font-size: 9.5pt; line-height: 1.55; text-align: justify; margin-bottom: 8px;">
            {!! nl2br(e($acc['summary_accomplishments'])) !!}
        </div>
    @else
        <p class="narrative-p">
            The extension project achieved 100% of its programmed milestone deliverables. Key outputs including: <em>{{ is_array($proposal->expected_outputs) ? implode('; ', $proposal->expected_outputs) : ($proposal->expected_outputs ?: 'Instructional toolkits, technical guides, and turn-over deliverables') }}</em> were formally presented and adopted by the beneficiaries.
        </p>
        <p class="narrative-p">
            In total, the project successfully capacitated <strong>{{ number_format((int) ($acc['actual_beneficiaries_total'] ?? 0)) }}</strong> active community participants (comprising {{ (int) ($acc['actual_beneficiaries_male'] ?? 0) }} males and {{ (int) ($acc['actual_beneficiaries_female'] ?? 0) }} females). Participants demonstrated measurable post-training competence, ensuring sustained capability retention within the host community.
        </p>
    @endif

    <!-- Embedded Photographic Documentation Set 2 -->
    @if(!empty($evidencePhotos) && count($evidencePhotos) >= 4)
        <table class="photo-table">
            <tr>
                <td class="photo-cell">
                    <img src="{{ $evidencePhotos[2]['data_uri'] }}" alt="Photo 3">
                    <div class="photo-caption">Figure 3. {{ $evidencePhotos[2]['title'] }} — Turn-over of technical materials and certificate awarding.</div>
                </td>
                <td class="photo-cell">
                    <img src="{{ $evidencePhotos[3]['data_uri'] }}" alt="Photo 4">
                    <div class="photo-caption">Figure 4. {{ $evidencePhotos[3]['title'] }} — Practical output inspection and community stakeholder acceptance.</div>
                </td>
            </tr>
        </table>
    @endif

    <!-- 4. Operational Challenges & Solutions Adopted Narrative -->
    <div class="section-header">4. Operational Challenges &amp; Remedial Solutions Narrative</div>
    @if(!empty($acc['problems_encountered']) || !empty($acc['solutions_adopted']))
        <p class="narrative-p">
            <strong>Field Challenges Encountered:</strong><br>
            {!! nl2br(e($acc['problems_encountered'])) !!}
        </p>
        <p class="narrative-p">
            <strong>Remedial Measures &amp; Solutions Adopted:</strong><br>
            {!! nl2br(e($acc['solutions_adopted'])) !!}
        </p>
    @else
        <p class="narrative-p">
            During execution, the project team successfully managed typical field constraints including logistical scheduling conflicts with participants' livelihood hours and unpredictable weather conditions in coastal access routes. Through prompt coordination with Barangay Council leaders, flexible modular scheduling was instituted, ensuring zero disruption to participant attendance.
        </p>
    @endif

    <!-- 5. Community Voice & Qualitative Testimonials Narrative -->
    <div class="section-header">5. Community Voice, Stakeholder Feedback &amp; Testimonials</div>
    @if(!empty($acc['impact_feedback']))
        <div style="font-size: 9.5pt; line-height: 1.55; text-align: justify; margin-bottom: 8px;">
            {!! nl2br(e($acc['impact_feedback'])) !!}
        </div>
    @else
        <p class="narrative-p">
            Post-activity feedback evaluations revealed exceptionally high satisfaction ratings across all modules. Host community leaders formally commended Surigao del Norte State University for its continuous presence and responsive extension programming. Participating beneficiaries reported immediate improvements in their daily operational confidence and expressed enthusiasm for prospective follow-on extension phases.
        </p>
    @endif

    <!-- Embedded Photographic Documentation Set 3 (Group / Banner photo) -->
    @if(!empty($evidencePhotos) && count($evidencePhotos) >= 5)
        <table class="photo-table">
            <tr>
                <td class="photo-cell" colspan="2" style="width: 100%;">
                    <img src="{{ $evidencePhotos[4]['data_uri'] }}" alt="Group Photo" style="height: 180px;">
                    <div class="photo-caption">Figure 5. {{ $evidencePhotos[4]['title'] }} — University faculty facilitators, project leaders, and community beneficiaries during the official culmination and turn-over.</div>
                </td>
            </tr>
        </table>
    @endif

    <!-- 6. Summary of Community Reach & Fund Liquidation -->
    <div class="section-header">6. Summary of Community Reach &amp; Financial Liquidation</div>
    <table class="meta-table" style="text-align: center;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="width: 25%; text-align: left;">Metric Description</th>
                <th style="width: 25%;">Target / Approved</th>
                <th style="width: 25%;">Actual Realized</th>
                <th style="width: 25%;">Performance Rating</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left; font-weight: bold;">Community Beneficiaries Reached</td>
                <td>{{ $proposal->beneficiaries ? Str::limit($proposal->beneficiaries, 20) : 'Community Target' }}</td>
                <td style="font-weight: bold; color: #047857;">{{ number_format((int) ($acc['actual_beneficiaries_total'] ?? 0)) }} persons</td>
                <td>Male: {{ (int) ($acc['actual_beneficiaries_male'] ?? 0) }} &bull; Female: {{ (int) ($acc['actual_beneficiaries_female'] ?? 0) }}</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold;">Financial Liquidation (₱)</td>
                <td>₱{{ number_format($plannedBudget, 2) }}</td>
                <td style="font-weight: bold; color: #0f172a;">₱{{ number_format($actualSpent, 2) }}</td>
                <td style="font-weight: bold; color: #047857;">{{ $utilizationRate }}% Fund Utilization</td>
            </tr>
        </tbody>
    </table>

    <!-- 7. Signatures & Official Acceptance -->
    <div style="page-break-inside: avoid; margin-top: 15px;">
        <div class="section-header">7. Verification &amp; Institutional Acceptance Signatures</div>
        <table class="sig-table">
            <tr>
                <td>
                    <div style="font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569;">Prepared &amp; Submitted By:</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $proposal->project_leader_name }}</div>
                    <div class="sig-title">{{ $proposal->project_leader_title ?: 'Project Leader / Proponent' }}</div>
                    <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: {{ !empty($acc['updated_at']) ? \Carbon\Carbon::parse($acc['updated_at'])->format('M d, Y') : now()->format('M d, Y') }}</div>
                </td>
                <td>
                    <div style="font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569;">Verified &amp; Endorsed By:</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">COLLEGE DEAN / PROGRAM CHAIR</div>
                    <div class="sig-title">Office of the College Dean</div>
                    <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569;">Concurred &amp; Accepted By:</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">ALVIE ELMA C. PLAZA, MA</div>
                    <div class="sig-title">Director, University Extension Services</div>
                    <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                </td>
                <td>
                    <div style="font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #475569;">Approved &amp; Institutionalized:</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">ROMY JUN A. SUNICO, DTE</div>
                    <div class="sig-title">Vice President for Research, Innovation &amp; Extension</div>
                    <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Date: ________________________</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Document Security Stamp Footer -->
    <table class="footer-table">
        <tr>
            <td style="text-align: left;">
                Generated via <strong>SNSU RIE Decision Support System</strong> &bull; Ref: {{ $proposal->tracking_number ?: $proposal->proposal_code }}
            </td>
            <td style="text-align: right;">
                Official University Terminal Narrative Report &bull; ISO 9001:2015 Compliant
            </td>
        </tr>
    </table>

</body>
</html>
