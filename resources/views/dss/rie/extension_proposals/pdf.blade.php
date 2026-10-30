<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ACTIVITY PROPOSAL — {{ $proposal->proposal_code }}</title>
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
            font-size: 11pt;
            line-height: 1.4;
            color: #111827;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .page {
            width: 100%;
            min-height: 270mm;
            position: relative;
            page-break-after: always;
            padding-bottom: 25px;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-title {
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.25;
        }
        .header-title .univ-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }
        .green-bar {
            height: 3.5px;
            background-color: #15803d;
            width: 100%;
            margin-bottom: 12px;
        }
        .footer-line {
            margin-top: 15px;
            border-top: 3px solid #15803d;
            padding-top: 4px;
            font-size: 8pt;
            color: #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-left {
            text-align: left;
        }
        .footer-right {
            text-align: right;
        }
        .footer-right a {
            color: #0284c7;
            text-decoration: none;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 8px 0 14px 0;
        }
        .part-header {
            font-weight: bold;
            font-size: 12pt;
            margin: 10px 0 6px 0;
            text-transform: uppercase;
        }
        .item-label {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
        }
        .field-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .field-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 11pt;
        }
        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 14px;
            font-size: 9.5pt;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.25;
            margin-bottom: 2px;
        }
        .checkbox-box {
            width: 12px;
            height: 12px;
            border: 1.2px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .checkbox-box.checked {
            background-color: #000;
            color: #fff;
        }
        .text-underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-height: 18px;
            padding: 0 4px;
        }
        .bordered-box {
            border: 1px solid #000;
            min-height: 100px;
            padding: 8px;
            font-size: 11pt;
            line-height: 1.45;
            text-align: justify;
            margin-bottom: 8px;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            font-size: 9.5pt;
        }
        table.grid-table th, table.grid-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        table.grid-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
        }
        .badge-risk-low {
            background-color: #ffedd5;
            color: #9a3412;
            font-weight: bold;
            text-align: center;
        }
        .badge-risk-medium {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
            text-align: center;
        }
        .badge-risk-high {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            text-align: center;
        }
        .sig-block-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .sig-block-table td {
            vertical-align: top;
            padding: 6px 10px;
            font-size: 9.5pt;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            min-height: 38px;
            margin-bottom: 4px;
            position: relative;
        }
        .sig-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5pt;
        }
        .sig-role {
            font-size: 8.5pt;
            color: #334155;
        }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar for Screen / Browser View -->
    <div class="no-print" style="position: fixed; top: 15px; right: 20px; z-index: 9999; background: white; padding: 8px 18px; border-radius: 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #cbd5e1; display: flex; gap: 10px; align-items: center;">
        <span style="font-size: 11px; font-weight: bold; color: #1e293b;">ACTIVITY PROPOSAL &bull; {{ $proposal->proposal_code }}</span>
        <button onclick="window.print()" style="background: #15803d; color: white; border: none; padding: 6px 16px; border-radius: 20px; font-weight: bold; cursor: pointer; font-size: 12px;">Print / Save as PDF</button>
        <button onclick="window.close()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 12px;">Close</button>
    </div>

    @php
        $renderHeader = function() {
            return '
            <table class="header-table">
                <tr>
                    <td style="width: 75px; text-align: left;">
                        <img src="' . asset('assets/images/snsu-logo.png') . '" style="height: 60px;" onerror="this.style.display=\'none\'">
                    </td>
                    <td class="header-title">
                        <div>Republic of the Philippines</div>
                        <div class="univ-name">SURIGAO DEL NORTE STATE UNIVERSITY</div>
                        <div>Narciso Street, Surigao City 8400, Philippines</div>
                        <div style="font-size: 8pt; font-style: italic; margin-top: 1px;">
                            "For Nation\'s Greater Heights" &bull; <strong>BAGONG PILIPINAS</strong>
                        </div>
                    </td>
                    <td style="width: 75px; text-align: right;">
                        <img src="' . asset('assets/images/socotec-logo.png') . '" style="height: 50px;" onerror="this.style.display=\'none\'">
                    </td>
                </tr>
            </table>
            <div class="green-bar"></div>
            ';
        };

        $renderFooter = function() {
            return '
            <div class="footer-line">
                <div class="footer-left">Tel. Nos.: (086) 827-3741</div>
                <div class="footer-right">
                    Email: <a href="mailto:info@snsu.edu.ph">info@snsu.edu.ph</a> &nbsp;&bull;&nbsp; URL: <a href="https://snsu.edu.ph">snsu.edu.ph</a>
                </div>
            </div>
            ';
        };

        // Banner Programs from DB or fallback
        $programsList = isset($bannerPrograms) && $bannerPrograms instanceof \Illuminate\Support\Collection
            ? $bannerPrograms->pluck('name')->toArray()
            : (is_array($bannerPrograms ?? null) ? $bannerPrograms : \App\Models\RIE\ExtensionBannerProgram::active()->ordered()->pluck('name')->toArray());
        if (empty($programsList)) {
            $programsList = [
                'TECHASSIST', 'FOODPRO', 'AGRIYAMAN', 'MODFARM 360', 'GOVeRN', 'SUSTINAHAN', 'RENEWTECH', 'ReStat-QUIIP',
                'STEAMUP', 'TOURGABAY', 'MSMEntri', 'KATUTUBO', 'ECOREVIVE', 'GADLIGHT', 'WELLNESS', 'TUBIG-SIGURADO',
            ];
        }
        $halfP = (int) ceil(count($programsList) / 2);
        $col1 = array_slice($programsList, 0, $halfP);
        $col2 = array_slice($programsList, $halfP);

        $selectedBanner = $proposal->banner_program;
        $bannerOther = $proposal->banner_program_other;
        $agendas = $proposal->agenda_priorities ?? [];

        // Beneficiaries
        $beneficiariesList = $proposal->resolved_target_beneficiaries;

        // Collaborating agencies
        $collabAgencies = $proposal->resolved_collaborating_agencies;
        if (empty($collabAgencies) && !empty($proposal->collaborating_agencies)) {
            $collabAgencies = [['agency' => $proposal->collaborating_agencies, 'role' => 'Partner Agency']];
        }

        // Expected Outputs
        $outputs = $proposal->resolved_expected_outputs;

        // Line-item budget
        $budgetSections = $proposal->resolved_line_item_budget;

        // Signatories list
        $signatories = $proposal->documentSignatories()->orderBy('sort_order')->get();
        $prepSig = $signatories->firstWhere('tier_role', 'prepared') ?: $signatories->first();
        $revFocalSig = $signatories->firstWhere('tier_role', 'reviewed_focal');
        $revCoordSig = $signatories->firstWhere('tier_role', 'reviewed_coordinator');
        $notedDeanSig = $signatories->firstWhere('tier_role', 'noted_dean') ?: $signatories->firstWhere('tier_role', 'noted');
        $notedDirSig = $signatories->firstWhere('tier_role', 'noted_director');
        $recDirSig = $signatories->firstWhere('tier_role', 'recommended_director') ?: $signatories->firstWhere('tier_role', 'concurred');
        $appVpieSig = $signatories->firstWhere('tier_role', 'approved_vpie') ?: ($signatories->firstWhere('tier_role', 'approved') ?: $signatories->firstWhere('tier_role', 'recommended_vpie'));
    @endphp

    <!-- ==================== PAGE 1 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <div class="doc-title">ACTIVITY PROPOSAL</div>

        <div class="part-header">PART I. BASIC INFORMATION</div>

        <table class="field-table" style="margin-bottom: 6px;">
            <tr>
                <td style="width: 26%;" class="item-label">A. Title of the Activity:</td>
                <td style="border-bottom: 1px solid #000; font-weight: bold; text-transform: uppercase;">
                    {{ $proposal->title }}
                </td>
            </tr>
        </table>

        <div style="margin-bottom: 8px;">
            <div class="item-label" style="margin-bottom: 4px;">B. Banner Program</div>
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    @foreach($col1 as $prog)
                        <div class="checkbox-item">
                            <span class="checkbox-box {{ $selectedBanner === $prog ? 'checked' : '' }}">{!! $selectedBanner === $prog ? '&#10003;' : '' !!}</span>
                            <span>{{ $prog }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="flex: 1;">
                    @foreach($col2 as $prog)
                        <div class="checkbox-item">
                            <span class="checkbox-box {{ $selectedBanner === $prog ? 'checked' : '' }}">{!! $selectedBanner === $prog ? '&#10003;' : '' !!}</span>
                            <span>{{ $prog }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="margin-top: 4px; font-size: 9pt;">
                Others: <span class="text-underline" style="min-width: 320px;">{{ $selectedBanner === 'Others' || !in_array($selectedBanner, $programsList) ? ($bannerOther ?: $selectedBanner) : '' }}</span>
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <div class="item-label" style="margin-bottom: 4px;">C. Extension Agenda:</div>
            @php
                $agendaList = isset($extensionAgendas) && $extensionAgendas instanceof \Illuminate\Support\Collection
                    ? $extensionAgendas->pluck('name')->toArray()
                    : (is_array($extensionAgendas ?? null) ? $extensionAgendas : \App\Models\RIE\ExtensionAgenda::active()->ordered()->pluck('name')->toArray());
                if (empty($agendaList)) {
                    $agendaList = [
                        'Transformative Learning',
                        'Livelihood Generation',
                        'Biodiversity Conservation',
                        'Climate Change Adoption & Risk Management',
                        'Good Governance',
                    ];
                }
            @endphp
            @foreach($agendaList as $agenda)
                <div class="checkbox-item" style="margin-bottom: 3px;">
                    <span class="checkbox-box {{ in_array($agenda, $agendas) ? 'checked' : '' }}">{!! in_array($agenda, $agendas) ? '&#10003;' : '' !!}</span>
                    <span>{{ $agenda }}</span>
                </div>
            @endforeach
        </div>

        <table class="field-table" style="margin-bottom: 6px;">
            <tr>
                <td style="width: 26%;" class="item-label">D. SDG Addressed:</td>
                <td style="border-bottom: 1px solid #000; font-weight: 500;">
                    @php
                        $sdgArr = [];
                        if ($proposal->sdgs && $proposal->sdgs->isNotEmpty()) {
                            foreach ($proposal->sdgs as $sdg) {
                                $sdgArr[] = "SDG {$sdg->goal_number}: {$sdg->title}";
                            }
                        } elseif (!empty($proposal->sdg_alignments)) {
                            $sel = $proposal->sdg_alignments['selected'] ?? $proposal->sdg_alignments;
                            if (is_array($sel)) {
                                foreach ($sel as $s) {
                                    $sdgArr[] = "SDG {$s}";
                                }
                            }
                        }
                    @endphp
                    {{ !empty($sdgArr) ? implode(', ', $sdgArr) : 'SDG 4: Quality Education, SDG 9: Industry & Innovation, SDG 17: Partnerships' }}
                </td>
            </tr>
            <tr>
                <td class="item-label">E. Project Leader:</td>
                <td style="border-bottom: 1px solid #000;">
                    <strong>{{ $proposal->project_leader_name }}</strong>
                    <span style="margin-left: 20px; font-weight: normal;">Gender:</span>
                    <strong>{{ $proposal->project_leader_gender ?: 'Auto fetch from employee profile' }}</strong>
                </td>
            </tr>
            <tr>
                <td class="item-label">F. Implementing Colleges:</td>
                <td style="border-bottom: 1px solid #000;">
                    @php
                        $colleges = $proposal->implementing_colleges ?? [];
                        if (empty($colleges)) {
                            $colleges = [$proposal->unit?->unit_name ?: ($proposal->academicProgram?->name ?: 'College of Computing and Information Sciences')];
                        }
                    @endphp
                    {{ is_array($colleges) ? implode('; ', $colleges) : $colleges }}
                </td>
            </tr>
        </table>

        <div style="margin-bottom: 8px;">
            <div class="item-label" style="margin-bottom: 4px;">G. Collaborating Agencies:</div>
            @forelse($collabAgencies as $idx => $agency)
                <div style="margin-bottom: 3px; font-size: 9pt;">
                    <span style="font-weight: 600;">{{ $agency['agency'] ?? 'Agency #'.($idx+1) }}</span>
                    &nbsp;&bull;&nbsp; Role: <span class="text-underline" style="min-width: 250px;">{{ $agency['role'] ?? 'Partner' }}</span>
                </div>
            @empty
                <div style="font-size: 9pt; color: #64748b;">— No external collaborating agencies indicated —</div>
            @endforelse
        </div>

        <div style="margin-bottom: 8px;">
            <div class="item-label" style="margin-bottom: 4px;">H. Target Beneficiaries:</div>
            <table class="grid-table" style="margin-bottom: 4px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Name</th>
                        <th style="width: 80px;">Male</th>
                        <th style="width: 80px;">Female</th>
                        <th style="width: 80px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumM = 0; $sumF = 0; $sumT = 0; @endphp
                    @forelse($beneficiariesList as $ben)
                        @php
                            $m = (int) ($ben['male'] ?? 0);
                            $f = (int) ($ben['female'] ?? 0);
                            $t = (int) ($ben['total'] ?? ($m + $f));
                            $sumM += $m; $sumF += $f; $sumT += $t;
                        @endphp
                        <tr>
                            <td>{{ $ben['name'] ?? 'Beneficiaries' }}</td>
                            <td style="text-align: center;">{{ $m }}</td>
                            <td style="text-align: center;">{{ $f }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $t }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ $proposal->beneficiaries ?: 'Partner Community Beneficiaries' }}</td>
                            <td style="text-align: center;">30</td>
                            <td style="text-align: center;">20</td>
                            <td style="text-align: center; font-weight: bold;">50</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            <div class="item-label" style="margin-bottom: 4px;">I. Estimated Budget Requirement: <span style="font-weight: normal;">{{ $proposal->budget_total_display ?: 'Grand Total: No Cash Requirement' }}</span></div>
            <table class="field-table">
                <tr>
                    <td style="width: 15%; padding-left: 15px;">Fund Source:</td>
                    <td style="width: 15%;">Internal:</td>
                    <td style="border-bottom: 1px solid #000;">
                        {{ $proposal->fund_source_internal ?: ($proposal->budget_institution_share ?: ($proposal->budget_snsu_share ?: 'In-Kind (University Service Vehicle & Faculty Expertise)')) }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>External:</td>
                    <td style="border-bottom: 1px solid #000;">
                        {{ $proposal->fund_source_external ?: ($proposal->budget_partner_share ?: 'In-Kind (Partner Venue & Provisions)') }}
                    </td>
                </tr>
            </table>
        </div>

        {!! $renderFooter() !!}
    </div>

    <!-- ==================== PAGE 2 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <table class="field-table" style="margin-bottom: 12px;">
            <tr>
                <td style="width: 15%;" class="item-label">J. Duration:</td>
                <td style="width: 28%;">
                    Started Date: <span class="text-underline">{{ $proposal->duration_start ? $proposal->duration_start->format('m/d/y') : '—' }}</span>
                </td>
                <td style="width: 28%;">
                    End Date: <span class="text-underline">{{ $proposal->duration_end ? $proposal->duration_end->format('m/d/y') : '—' }}</span>
                </td>
                <td style="width: 29%;">
                    No of Months: <strong style="font-size: 11pt;">{{ $proposal->duration_months ?: 1 }}</strong>
                </td>
            </tr>
        </table>

        <div class="part-header" style="margin-top: 14px;">PART II. TECHNICAL DESCRIPTION</div>

        <div style="margin-bottom: 12px;">
            <div class="item-label" style="margin-bottom: 4px;">A. Rationale (not to exceed 500 words)</div>
            <div class="bordered-box" style="min-height: 140px;">
                {!! $proposal->introduction_html ?: nl2br(e($proposal->introduction ?: 'The extension activity addresses targeted capacity building and institutional knowledge transfer for partner community beneficiaries in Surigao Del Norte.')) !!}
            </div>
        </div>

        <div style="margin-bottom: 12px;">
            <div class="item-label" style="margin-bottom: 4px;">B. Objectives</div>
            <div style="margin-bottom: 6px; font-size: 11pt;">
                <strong>General Objective:</strong>
                <span class="text-underline" style="width: 80%;">
                    {{ strip_tags($proposal->general_objective ?: ($proposal->objectives_text ?: 'To provide transformative technical skills training and community capacity building.')) }}
                </span>
            </div>
            <div style="margin-left: 10px; font-size: 11pt;">
                <strong>Specific Objectives:</strong>
                @php
                    $specificObjs = $proposal->specific_objectives ?? [];
                    if (empty($specificObjs) && is_array($proposal->objectives)) {
                        $specificObjs = array_values(array_filter($proposal->objectives, fn($o) => is_string($o) && !empty(trim($o))));
                    }
                    if (empty($specificObjs)) {
                        $specificObjs = [
                            '1. Deliver interactive technical training modules for beneficiaries.',
                            '2. Facilitate hands-on workshops and practical demonstrations.',
                            '3. Evaluate community learning outcomes and project sustainability.',
                        ];
                    }
                @endphp
                @foreach($specificObjs as $idx => $sObj)
                    <div style="margin: 4px 0;">
                        <strong>Objective {{ $idx + 1 }}:</strong>
                        <span class="text-underline" style="width: 82%;">{{ strip_tags(is_array($sObj) ? ($sObj['description'] ?? $sObj['title'] ?? '') : (string) $sObj) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <div class="item-label" style="margin-bottom: 4px;">C. Expected Output</div>
            <table class="grid-table">
                <thead>
                    <tr>
                        <th style="width: 25%; text-align: left;">Category</th>
                        <th style="width: 15%;">Target Quantity</th>
                        <th style="text-align: left;">Description / Output Details</th>
                    </tr>
                </thead>
                <tbody>
                    @php $first4 = array_slice($outputs, 0, 4); @endphp
                    @foreach($first4 as $out)
                        <tr>
                            <td style="font-weight: bold;">{{ $out['category'] }}</td>
                            <td style="text-align: center;">{{ $out['target'] }}</td>
                            <td>{{ $out['description'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {!! $renderFooter() !!}
    </div>

    <!-- ==================== PAGE 3 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <!-- Continuation of C. Expected Output -->
        <table class="grid-table" style="margin-top: 4px; margin-bottom: 12px;">
            <thead>
                <tr>
                    <th style="width: 25%; text-align: left;">Category</th>
                    <th style="width: 15%;">Target Quantity</th>
                    <th style="text-align: left;">Description / Output Details</th>
                </tr>
            </thead>
            <tbody>
                @php $last3 = array_slice($outputs, 4, 3); @endphp
                @foreach($last3 as $out)
                    <tr>
                        <td style="font-weight: bold;">{{ $out['category'] }}</td>
                        <td style="text-align: center;">{{ $out['target'] }}</td>
                        <td>{{ $out['description'] ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-bottom: 12px;">
            <div class="item-label" style="margin-bottom: 4px;">D. Relevance of the Program/Projects/Activity</div>
            <div class="bordered-box" style="min-height: 100px;">
                {!! $proposal->relevance_narrative_html ?: nl2br(e($proposal->relevance_narrative ?: 'The extension activity directly aligns with university research thrusts, regional development agenda, and UN Sustainable Development Goals.')) !!}
            </div>
        </div>

        <div style="margin-bottom: 12px;">
            <div class="item-label" style="margin-bottom: 4px;">E. Schedule of Activities</div>
            <table class="grid-table" style="font-size: 8pt;">
                <thead>
                    <tr>
                        <th style="width: 30px;">No.</th>
                        <th style="width: 38%; text-align: left;">Major Activities</th>
                        <th style="width: 22%; text-align: left;">Agency / Person Involved</th>
                        <th style="width: 30px;">Jan</th>
                        <th style="width: 30px;">Feb</th>
                        <th style="width: 30px;">Mar</th>
                        <th style="width: 30px;">Apr</th>
                        <th style="width: 30px;">May</th>
                        <th style="width: 30px;">Jun</th>
                        <th style="width: 30px;">Jul</th>
                        <th style="width: 30px;">Aug</th>
                        <th style="width: 30px;">Sep</th>
                        <th style="width: 30px;">Oct</th>
                        <th style="width: 30px;">Nov</th>
                        <th style="width: 30px;">Dec</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sched = $proposal->resolved_schedule_of_activities['rows'] ?? [];
                        if (empty($sched)) {
                            $sched = [
                                ['no' => 1, 'activity' => 'Coordination with Partner Community & LGU', 'agency' => 'Project Leader / Team', 'months' => ['Sep' => 1]],
                                ['no' => 2, 'activity' => 'Preparation of Training Materials & Logistics', 'agency' => 'Extension Team', 'months' => ['Sep' => 1, 'Oct' => 1]],
                                ['no' => 3, 'activity' => 'Conduct of Capability Training Workshops', 'agency' => 'Faculty Extensionists', 'months' => ['Oct' => 1]],
                                ['no' => 4, 'activity' => 'Post-Activity Evaluation & Terminal Reporting', 'agency' => 'Project Proponents', 'months' => ['Nov' => 1]],
                            ];
                        }
                        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    @endphp
                    @foreach($sched as $idx => $sRow)
                        @php
                            $rowMonths = $sRow['months'] ?? [];
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $sRow['no'] ?? ($idx + 1) }}</td>
                            <td>{{ $sRow['activity'] ?? ($sRow['name'] ?? '') }}</td>
                            <td>{{ $sRow['agency'] ?? ($sRow['person'] ?? 'Extension Team') }}</td>
                            @foreach($allMonths as $m)
                                @php $active = !empty($rowMonths[$m]) || (!empty($sRow['active_months']) && in_array($m, (array)$sRow['active_months'])); @endphp
                                <td style="text-align: center; {{ $active ? 'background-color: #000; color: #fff;' : '' }}">
                                    {!! $active ? '&#10003;' : '' !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-bottom: 12px;">
            <div class="item-label" style="margin-bottom: 4px;">F. Project Proponents</div>
            <table class="grid-table">
                <thead>
                    <tr>
                        <th style="width: 30%; text-align: left;">Name</th>
                        <th style="width: 25%; text-align: left;">Project Role Title</th>
                        <th style="text-align: left;">Duties &amp; Responsibilities</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $propRows = $proposal->resolved_people_involved['rows'] ?? [];
                        if (empty($propRows) && !empty($proposal->proponents)) {
                            $propRows = $proposal->proponents;
                        }
                    @endphp
                    @forelse($propRows as $p)
                        <tr>
                            <td style="font-weight: bold;">{{ $p['name'] ?? '' }}</td>
                            <td>{{ $p['role'] ?? 'Member' }}</td>
                            <td>{{ $p['responsibilities'] ?? ($p['duties'] ?? 'Activity facilitation, technical modules, and logistics.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="font-weight: bold;">{{ $proposal->project_leader_name }}</td>
                            <td>Project Leader</td>
                            <td>Overall project management, institutional coordination, and reporting.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            <div class="item-label" style="margin-bottom: 4px;">G. Risk Management</div>
            <table class="grid-table" style="font-size: 8.5pt;">
                <thead>
                    <tr>
                        <th style="width: 65px;">Objective No.</th>
                        <th style="width: 65px;">Activity No.</th>
                        <th>Risk</th>
                        <th style="width: 80px;">Risk Level</th>
                        <th>Prevention / Mitigation</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $risks = $proposal->resolved_risk_management;
                        $firstRiskBatch = array_slice($risks, 0, 2);
                        if (empty($firstRiskBatch)) {
                            $firstRiskBatch = [
                                ['objective_no' => '1', 'activity_no' => '1', 'risk' => 'Inclement weather or typhoon disruption', 'risk_level' => 'Low', 'prevention' => 'Coordinate backup indoor venue and monitor PAGASA advisories.'],
                                ['objective_no' => '2', 'activity_no' => '3', 'risk' => 'Intermittent internet connectivity during demo', 'risk_level' => 'Medium', 'prevention' => 'Pre-download offline training slides, software, and localized tools.'],
                            ];
                        }
                    @endphp
                    @foreach($firstRiskBatch as $rk)
                        @php
                            $lvl = ucfirst(strtolower($rk['risk_level'] ?? 'Low'));
                            $badgeClass = match($lvl) {
                                'High' => 'badge-risk-high',
                                'Medium' => 'badge-risk-medium',
                                default => 'badge-risk-low',
                            };
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $rk['objective_no'] ?? 1 }}</td>
                            <td style="text-align: center;">{{ $rk['activity_no'] ?? 1 }}</td>
                            <td>{{ $rk['risk'] ?? '' }}</td>
                            <td class="{{ $badgeClass }}">{{ $lvl }}</td>
                            <td>{{ $rk['prevention'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {!! $renderFooter() !!}
    </div>

    <!-- ==================== PAGE 4 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <!-- G. Risk Management Continuation -->
        <table class="grid-table" style="font-size: 8.5pt; margin-top: 4px; margin-bottom: 12px;">
            <thead>
                <tr>
                    <th style="width: 65px;">Objective No.</th>
                    <th style="width: 65px;">Activity No.</th>
                    <th>Risk</th>
                    <th style="width: 80px;">Risk Level</th>
                    <th>Prevention / Mitigation</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $secondRiskBatch = array_slice($risks, 2);
                    if (empty($secondRiskBatch)) {
                        $secondRiskBatch = [
                            ['objective_no' => '3', 'activity_no' => '4', 'risk' => 'Low participant post-training turnout', 'risk_level' => 'High', 'prevention' => 'Active coordination with LGU leaders and award certificates of completion.'],
                            ['objective_no' => '1', 'activity_no' => '2', 'risk' => 'Equipment unavailability', 'risk_level' => 'Low', 'prevention' => 'Advance reservation of university multimedia equipment.'],
                        ];
                    }
                @endphp
                @foreach($secondRiskBatch as $rk)
                    @php
                        $lvl = ucfirst(strtolower($rk['risk_level'] ?? 'Low'));
                        $badgeClass = match($lvl) {
                            'High' => 'badge-risk-high',
                            'Medium' => 'badge-risk-medium',
                            default => 'badge-risk-low',
                        };
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $rk['objective_no'] ?? 1 }}</td>
                        <td style="text-align: center;">{{ $rk['activity_no'] ?? 1 }}</td>
                        <td>{{ $rk['risk'] ?? '' }}</td>
                        <td class="{{ $badgeClass }}">{{ $lvl }}</td>
                        <td>{{ $rk['prevention'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="part-header" style="margin-top: 14px;">
            <div style="font-weight: bold; margin-bottom: 2px; font-size: 10pt;">PART II. BUDGETARY REQUIREMENTS</div>
            <div style="font-style: italic; font-size: 8pt; margin-bottom: 4px; color: #475569;">(put N/A in cells if no required budget requirements; each section is dynamic)</div>
        </div>

        <div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">Line - Item Budget</div>

        <table class="grid-table" style="font-size: 8pt; margin-top: 2px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;"></th>
                    <th style="text-align: left; width: 45%;">Particulars / Item Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 10%; text-align: center;">Unit</th>
                    <th style="width: 15%; text-align: right;">Unit Cost</th>
                    <th style="width: 15%; text-align: right;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                <!-- Section A: Personal Services -->
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td style="text-align: center; font-weight: bold;">A.</td>
                    <td colspan="5">Personal Services</td>
                </tr>
                @php
                    $secA = $budgetSections['A']['items'] ?? [];
                    if (empty($secA)) {
                        $secA = [
                            ['particulars' => 'Salary', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
                            ['particulars' => 'Honoraria', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
                        ];
                    }
                @endphp
                @foreach($secA as $it)
                    <tr>
                        <td></td>
                        <td style="padding-left: 15px;">{{ $it['particulars'] ?? 'Salary' }}</td>
                        <td style="text-align: center;">{{ $it['qty'] ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $it['unit'] ?? 'N/A' }}</td>
                        <td style="text-align: right;">{{ is_numeric($it['cost'] ?? null) ? number_format($it['cost'], 2) : ($it['cost'] ?? 'N/A') }}</td>
                        <td style="text-align: right; font-weight: 500;">{{ is_numeric($it['amount'] ?? null) ? number_format($it['amount'], 2) : ($it['amount'] ?? 'N/A') }}</td>
                    </tr>
                @endforeach

                <!-- Section B: MOOE Main Header -->
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td style="text-align: center; font-weight: bold;">B.</td>
                    <td colspan="5">Maintenance &amp; Other Operating Expenses (MOOE)</td>
                </tr>

                <!-- B Subcategory: Communication -->
                <tr style="background-color: #ffffff; font-weight: bold;">
                    <td></td>
                    <td colspan="5" style="padding-left: 12px; color: #1e293b;">Communication</td>
                </tr>
                @php $secComm = $budgetSections['B_COMM']['items'] ?? ($budgetSections['C']['items'] ?? []); @endphp
                @forelse($secComm as $it)
                    <tr>
                        <td></td>
                        <td style="padding-left: 24px;">{{ $it['particulars'] }}</td>
                        <td style="text-align: center;">{{ $it['qty'] }}</td>
                        <td style="text-align: center;">{{ $it['unit'] }}</td>
                        <td style="text-align: right;">{{ is_numeric($it['cost']) ? number_format($it['cost'], 2) : $it['cost'] }}</td>
                        <td style="text-align: right; font-weight: 500;">{{ is_numeric($it['amount']) ? number_format($it['amount'], 2) : $it['amount'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td></td>
                        <td style="padding-left: 24px; color: #64748b;">N/A</td>
                        <td style="text-align: center;">N/A</td>
                        <td style="text-align: center;">N/A</td>
                        <td style="text-align: right;">N/A</td>
                        <td style="text-align: right;">N/A</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {!! $renderFooter() !!}
    </div>

    <!-- ==================== PAGE 5 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <!-- Continuation of Line-Item Budget: MOOE Subcategories, Capital Outlay, Contingency -->
        <table class="grid-table" style="font-size: 8pt; margin-top: 4px; margin-bottom: 12px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;"></th>
                    <th style="text-align: left; width: 45%;">Particulars / Item Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 10%; text-align: center;">Unit</th>
                    <th style="width: 15%; text-align: right;">Unit Cost</th>
                    <th style="width: 15%; text-align: right;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mooeContinuation = [
                        'B_TRANS' => 'Transportation & Travel',
                        'B_SUPP'  => 'Supplies and Materials',
                        'B_TRAIN' => 'Trainings & Seminars',
                        'B_OTHER' => 'Other Services',
                    ];
                @endphp
                @foreach($mooeContinuation as $subKey => $subTitle)
                    <tr style="background-color: #ffffff; font-weight: bold;">
                        <td></td>
                        <td colspan="5" style="padding-left: 12px; color: #1e293b;">{{ $subTitle }}</td>
                    </tr>
                    @php $subItems = $budgetSections[$subKey]['items'] ?? []; @endphp
                    @forelse($subItems as $it)
                        <tr>
                            <td></td>
                            <td style="padding-left: 24px;">{{ $it['particulars'] }}</td>
                            <td style="text-align: center;">{{ $it['qty'] }}</td>
                            <td style="text-align: center;">{{ $it['unit'] }}</td>
                            <td style="text-align: right;">{{ is_numeric($it['cost']) ? number_format($it['cost'], 2) : $it['cost'] }}</td>
                            <td style="text-align: right; font-weight: 500;">{{ is_numeric($it['amount']) ? number_format($it['amount'], 2) : $it['amount'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td></td>
                            <td style="padding-left: 24px; color: #64748b;">N/A</td>
                            <td style="text-align: center;">N/A</td>
                            <td style="text-align: center;">N/A</td>
                            <td style="text-align: right;">N/A</td>
                            <td style="text-align: right;">N/A</td>
                        </tr>
                    @endforelse
                @endforeach

                <!-- C. Capital Outlay (CO) -->
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td style="text-align: center; font-weight: bold;">C.</td>
                    <td colspan="5">Capital Outlay (CO)</td>
                </tr>
                @php $secCO = $budgetSections['C']['items'] ?? []; @endphp
                @forelse($secCO as $it)
                    <tr>
                        <td></td>
                        <td style="padding-left: 24px;">{{ $it['particulars'] }}</td>
                        <td style="text-align: center;">{{ $it['qty'] }}</td>
                        <td style="text-align: center;">{{ $it['unit'] }}</td>
                        <td style="text-align: right;">{{ is_numeric($it['cost']) ? number_format($it['cost'], 2) : $it['cost'] }}</td>
                        <td style="text-align: right; font-weight: 500;">{{ is_numeric($it['amount']) ? number_format($it['amount'], 2) : $it['amount'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td></td>
                        <td style="padding-left: 24px; color: #64748b;">N/A</td>
                        <td style="text-align: center;">N/A</td>
                        <td style="text-align: center;">N/A</td>
                        <td style="text-align: right;">N/A</td>
                        <td style="text-align: right;">N/A</td>
                    </tr>
                @endforelse

                <!-- D. Contingency or Miscellaneous (if items exist) -->
                @php $secD = $budgetSections['D']['items'] ?? []; @endphp
                @if(!empty($secD))
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td style="text-align: center; font-weight: bold;">D.</td>
                        <td colspan="5">Contingency or Miscellaneous</td>
                    </tr>
                    @foreach($secD as $it)
                        <tr>
                            <td></td>
                            <td style="padding-left: 24px;">{{ $it['particulars'] }}</td>
                            <td style="text-align: center;">{{ $it['qty'] }}</td>
                            <td style="text-align: center;">{{ $it['unit'] }}</td>
                            <td style="text-align: right;">{{ is_numeric($it['cost']) ? number_format($it['cost'], 2) : $it['cost'] }}</td>
                            <td style="text-align: right; font-weight: 500;">{{ is_numeric($it['amount']) ? number_format($it['amount'], 2) : $it['amount'] }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Grand Total Row -->
                <tr style="background-color: #e2e8f0; font-weight: bold; font-size: 9pt;">
                    <td colspan="5" style="text-align: right; text-transform: uppercase;">Grand Total:</td>
                    <td style="text-align: right; color: #0f172a;">
                        {{ $proposal->budget_cash_amount > 0 ? 'P' . number_format($proposal->budget_cash_amount, 2) : ($proposal->budget_total_display ?: 'P0.00') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures (Part 1): Prepared by & Reviewed by -->
        <div style="margin-top: 20px;">
            <table class="sig-block-table">
                <tr>
                    <td colspan="2" style="padding-bottom: 2px;">Prepared by:</td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $prepSig?->signatory_name ?: $proposal->project_leader_name }}</div>
                        <div class="sig-role">{{ $prepSig?->designation ?: 'Project Leader' }}</div>
                    </td>
                    <td style="width: 50%;"></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 20px; padding-bottom: 2px;">Reviewed by:</td>
                </tr>
                <tr>
                    <td style="width: 50%; padding-right: 15px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $revFocalSig?->signatory_name ?: '—' }}</div>
                        <div class="sig-role">{{ $revFocalSig?->designation ?: 'Focal Person' }}</div>
                    </td>
                    <td style="width: 50%; padding-left: 15px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $revCoordSig?->signatory_name ?: '—' }}</div>
                        <div class="sig-role">{{ $revCoordSig?->designation ?: 'College/Campus Research/Extension Coordinator' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {!! $renderFooter() !!}
    </div>

    <!-- ==================== PAGE 6 ==================== -->
    <div class="page">
        {!! $renderHeader() !!}

        <!-- Signatures (Part 2): Noted by, Recommending Approval, Approved -->
        <table class="sig-block-table" style="margin-top: 15px;">
            <tr>
                <td colspan="2" style="padding-bottom: 2px;">Noted:</td>
            </tr>
            <tr>
                <td style="width: 50%; padding-right: 15px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $notedDeanSig?->signatory_name ?: '—' }}</div>
                    <div class="sig-role">{{ $notedDeanSig?->designation ?: 'College Dean/Program Chair' }}</div>
                </td>
                <td style="width: 50%; padding-left: 15px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $notedDirSig?->signatory_name ?: '—' }}</div>
                    <div class="sig-role">{{ $notedDirSig?->designation ?: 'Campus Director' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 28px; padding-bottom: 2px;">Recommending Approval:</td>
            </tr>
            <tr>
                <td style="width: 50%; padding-right: 15px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $recDirSig?->signatory_name ?: 'ALVIE ELMA C. PLAZA, MA' }}</div>
                    <div class="sig-role">{{ $recDirSig?->designation ?: 'Extension Director' }}</div>
                </td>
                <td style="width: 50%;"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 28px; padding-bottom: 2px;">Approved:</td>
            </tr>
            <tr>
                <td style="width: 50%; padding-right: 15px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $appVpieSig?->signatory_name ?: 'ROMY JUN A. SUNICO, DTE' }}</div>
                    <div class="sig-role">{{ $appVpieSig?->designation ?: 'Vice President for Research, Innovation and Extension' }}</div>
                </td>
                <td style="width: 50%;"></td>
            </tr>
        </table>

        {!! $renderFooter() !!}
    </div>

</body>
</html>
