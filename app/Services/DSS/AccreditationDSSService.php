<?php

namespace App\Services\DSS;

use App\Models\Accreditation\AccreditationArea;
use App\Models\Accreditation\AccreditationBenchmark;
use App\Models\Accreditation\AccreditationProgram;

class AccreditationDSSService
{
    /**
     * Pre-configured Instrument Templates Catalog across SUC Accreditation Levels & Agencies
     */
    public static function getInstrumentCatalog(): array
    {
        return [
            'AACCUP_STANDARD_10' => [
                'name' => 'AACCUP Standard (10 Survey Areas)',
                'level_scope' => 'Candidate Status, Level I Accredited, Level II Re-Accredited',
                'description' => 'Comprehensive 10-area instrument required for preliminary surveys and formal Levels I & II accreditation.',
                'areas' => [
                    1 => ['name' => 'Area I: Vision, Mission, Goals, and Objectives (VMGO)', 'mandatory' => false, 'mov' => 'BOR Resolution, Student Handbook, Consultation Minutes'],
                    2 => ['name' => 'Area II: Faculty', 'mandatory' => true, 'mov' => 'Faculty Masterlist, Terminal Diplomas, Licensure Board Ratings, NBC 461 Reclassification'],
                    3 => ['name' => 'Area III: Curriculum and Instruction', 'mandatory' => true, 'mov' => 'OBE Course Syllabi, CMO Compliance Matrix, Instructional Materials'],
                    4 => ['name' => 'Area IV: Support to Students', 'mandatory' => false, 'mov' => 'Student Affairs Manual, Scholarship Clearances, Guidance Testing, Alumni Tracer'],
                    5 => ['name' => 'Area V: Research', 'mandatory' => true, 'mov' => 'Scopus/WoS Publications, IPOPHL Patent/Utility Models, University Research Grants'],
                    6 => ['name' => 'Area VI: Extension and Community Involvement', 'mandatory' => true, 'mov' => 'LGU Adopted Barangay MOA, Sustainable Livelihood Transfer Reports, Impact Assessment'],
                    7 => ['name' => 'Area VII: Library', 'mandatory' => false, 'mov' => 'ScienceDirect/IEEE Subscriptions, Book Holdings Inventory, E-Library Analytics'],
                    8 => ['name' => 'Area VIII: Physical Plant and Facilities', 'mandatory' => false, 'mov' => 'Building Safety Permits, BP 344 Accessibility, Smart Classroom Infrastructure'],
                    9 => ['name' => 'Area IX: Laboratories', 'mandatory' => false, 'mov' => 'Equipment Calibration Certificates, MSDS Sheets, DENR Environmental Permits'],
                    10 => ['name' => 'Area X: Administration', 'mandatory' => false, 'mov' => 'IIPM-DSS Automated System, ISO 9001:2015 Audit Report, Budget Utilization Rate'],
                ],
            ],

            'AACCUP_LEVEL_3_PHASE_1' => [
                'name' => 'AACCUP Level III Phase 1 (4 Mandatory Core Areas)',
                'level_scope' => 'Level III Phase 1 Qualifying Assessment',
                'description' => 'Specialized qualifying instrument strictly evaluating the 4 mandatory SUC core mandate areas (Faculty, Curriculum, Research, Extension).',
                'areas' => [
                    1 => ['name' => 'Area II: Faculty Development, Advanced Degrees & Research Track', 'mandatory' => true, 'mov' => 'Doctorate Faculty Ratio, NBC 461 Track, International Certifications'],
                    2 => ['name' => 'Area III: Curriculum & Instruction, Licensure Passing & Graduate Employability', 'mandatory' => true, 'mov' => 'Board Exam Passing Rates vs National Mean, OBE Curricula, Employer Feedback'],
                    3 => ['name' => 'Area V: Research Capability, Scientific Publications & Commercialized Patents', 'mandatory' => true, 'mov' => 'Scopus/WoS Indexed Articles, External Research Grants, Patent Commercialization'],
                    4 => ['name' => 'Area VI: Extension & Community Impact, Sustainable Livelihood & LGU MOAs', 'mandatory' => true, 'mov' => 'Adopted Municipalities Impact Studies, Tech Transfer Agreements, SUC Extension Grants'],
                ],
            ],

            'AACCUP_LEVEL_4_DISTINCTION' => [
                'name' => 'AACCUP Level IV Distinction (5 Institutional Impact Criteria)',
                'level_scope' => 'Level IV Institutional Distinction & Center of Excellence',
                'description' => 'Elite 5-criterion institutional impact instrument measuring academic distinction, global visibility, high-impact research, and institutional governance.',
                'areas' => [
                    1 => ['name' => 'Criterion 1: Higher Education Services & Academic Distinction', 'mandatory' => true, 'mov' => 'National Licensure Topnotchers, CHED COE/COD Accreditation, 100% OBE Alignment'],
                    2 => ['name' => 'Criterion 2: Advanced Research Capability, Scopus/WoS Publications & Patents', 'mandatory' => true, 'mov' => 'High-Impact Scopus Q1/Q2 Papers, IPOPHL Inventions, DOST-Funded Consortia'],
                    3 => ['name' => 'Criterion 3: Extension Services, Socio-Economic Impact & Industry Partnerships', 'mandatory' => true, 'mov' => 'Community Economic uplift metrics, Private Industry MOAs, Regional SUC Impact'],
                    4 => ['name' => 'Criterion 4: Internationalization, Cross-Border Mobility & Global Consortia', 'mandatory' => true, 'mov' => 'Inbound/Outbound Student Mobility, ASEAN AUN-QA Linkages, International Visiting Faculty'],
                    5 => ['name' => 'Criterion 5: Institutional Governance, Resource Management, ISO 9001 & Green Campus', 'mandatory' => true, 'mov' => 'ISO 9001:2015 Surveillance Cert, 98% Budget Execution, Solar Smart Energy Systems, IIPM-DSS ERP'],
                ],
            ],

            'CHED_ISA_5_KRA' => [
                'name' => 'CHED Institutional Sustainability Assessment (5 KRAs)',
                'level_scope' => 'CHED Institutional Accreditation & Autonomous Status',
                'description' => 'Commission on Higher Education (CHED) ISA framework evaluating institutional maturity across 5 Key Result Areas.',
                'areas' => [
                    1 => ['name' => 'KRA 1: Governance and Management', 'mandatory' => true, 'mov' => 'Strategic Plan, Management Information Systems, Financial Sustainability'],
                    2 => ['name' => 'KRA 2: Quality of Teaching and Learning', 'mandatory' => true, 'mov' => 'Curriculum Design, Program Delivery, Assessment Systems, Faculty Qualifications'],
                    3 => ['name' => 'KRA 3: Quality of Professional Exposure, Research, and Creative Work', 'mandatory' => true, 'mov' => 'Industry Internships, Creative Works, Publications, Research Outputs'],
                    4 => ['name' => 'KRA 4: Support for Students', 'mandatory' => false, 'mov' => 'Student Support Services, Guidance, Academic Advising, Co-Curricular Programs'],
                    5 => ['name' => 'KRA 5: Relations with the Community', 'mandatory' => false, 'mov' => 'Community Engagement, Extension Programs, Social Relevance, Partner Feedback'],
                ],
            ],

            'CHED_COE_COD' => [
                'name' => 'CHED Center of Excellence / Development (4 Domains)',
                'level_scope' => 'CHED COE / COD Designation',
                'description' => 'Discipline-specific benchmark standards evaluating program excellence for national Center of Excellence recognition.',
                'areas' => [
                    1 => ['name' => 'Domain 1: Instructional Quality & Faculty Terminal Degrees', 'mandatory' => true, 'mov' => 'PhD Faculty Percentage, Modernized Laboratories, Licensure Performance'],
                    2 => ['name' => 'Domain 2: Research Track Record & Scientific Publications', 'mandatory' => true, 'mov' => 'Refereed/Indexed Journals, Research Funding, Citation Indices'],
                    3 => ['name' => 'Domain 3: Extension Programs & Community Engagement', 'mandatory' => true, 'mov' => 'Extension Services, Institutionalized Outreach, Community Impact'],
                    4 => ['name' => 'Domain 4: Institutional Linkages & National/International Consortia', 'mandatory' => true, 'mov' => 'Active MOAs/MOUs, Industry Collaborations, Global Consortia'],
                ],
            ],
        ];
    }

    /**
     * Standard AACCUP Threshold Rules & Criteria
     */
    public static function getLevelThresholds(): array
    {
        return [
            'Candidate Status' => [
                'min_grand_mean' => 2.00,
                'min_area_score' => 1.50,
                'mandatory_areas' => [],
                'description' => 'Preliminary survey status. Requires baseline compliance across all survey areas with no critical deficiencies.',
            ],
            'Level I Accredited' => [
                'min_grand_mean' => 2.50,
                'min_area_score' => 2.00,
                'mandatory_areas' => [],
                'description' => 'First level formal accreditation. Program meets satisfactory standards with full compliance on curriculum and faculty qualifications.',
            ],
            'Level II Re-Accredited' => [
                'min_grand_mean' => 3.00,
                'min_area_score' => 2.50,
                'mandatory_areas' => [2, 3], // Faculty & Curriculum
                'description' => 'Demonstrated high academic standards. Mandatory Area II (Faculty) and Area III (Curriculum & Instruction) must achieve at least 3.00.',
            ],
            'Level III Phase 1' => [
                'min_grand_mean' => 3.50,
                'min_area_score' => 3.00,
                'mandatory_areas' => [1, 2, 3, 4], // In Level III Phase 1 instrument, all 4 areas are mandatory
                'description' => 'Advanced SUC mandate performance. Mandatory Area II (Faculty), Area III (Curriculum), Area V (Research), and Area VI (Extension) must all achieve >= 3.50.',
            ],
            'Level III Phase 2' => [
                'min_grand_mean' => 3.80,
                'min_area_score' => 3.20,
                'mandatory_areas' => [2, 3, 5, 6],
                'description' => 'Full Level III accreditation. Requires sustained publication track record, funded research, community extensions, and master/doctorate faculty ratio.',
            ],
            'Level IV Re-Accredited' => [
                'min_grand_mean' => 4.00,
                'min_area_score' => 3.50,
                'mandatory_areas' => [1, 2, 3, 4, 5], // All 5 Institutional Criteria in Level IV
                'description' => 'Highest program distinction. All 5 Institutional Impact Criteria (Academic Distinction, Research, Extension, Internationalization, Governance) must achieve >= 4.00.',
            ],
        ];
    }

    /**
     * Apply / Re-template an Instrument to a Program
     */
    public function applyTemplate(AccreditationProgram $program, string $templateKey): void
    {
        $catalog = self::getInstrumentCatalog();
        $template = $catalog[$templateKey] ?? $catalog['AACCUP_STANDARD_10'];

        // Remove existing areas and benchmarks
        $program->areas()->delete();

        // Update program's instrument template title
        $program->update(['instrument_template' => $template['name']]);

        // Generate new areas and parameters from template
        foreach ($template['areas'] as $num => $info) {
            $area = AccreditationArea::create([
                'tenant_id' => $program->tenant_id,
                'program_id' => $program->id,
                'area_number' => $num,
                'area_name' => $info['name'],
                'area_leader' => $program->task_force_chair ?? 'Designated Area Leader',
                'weight' => 1.00,
                'mean_score' => 3.50,
                'is_mandatory_level3' => $info['mandatory'],
                'compliance_status' => 'Compliant',
                'remarks' => "Standard benchmark parameters initialized for {$info['name']}.",
            ]);

            // Parameter A
            AccreditationBenchmark::create([
                'tenant_id' => $program->tenant_id,
                'area_id' => $area->id,
                'parameter_letter' => 'Parameter A: System, Standards & Policy Guidelines',
                'benchmark_code' => "P.{$num}.A",
                'description' => "Standard compliance, institutional policies, and operational processes documented for {$info['name']}.",
                'rating' => 3.50,
                'means_of_verification' => $info['mov'] ?? 'Official BOR Resolutions, Transmittals, Program Portfolios',
                'status' => 'Uploaded',
                'evidence_count' => 3,
            ]);

            // Parameter B
            AccreditationBenchmark::create([
                'tenant_id' => $program->tenant_id,
                'area_id' => $area->id,
                'parameter_letter' => 'Parameter B: Implementation, Quality Control & Monitoring',
                'benchmark_code' => "P.{$num}.B",
                'description' => "Institutional monitoring, stakeholder feedback, and continuous evaluation for {$info['name']}.",
                'rating' => 3.50,
                'means_of_verification' => $info['mov'] ?? 'IQA Mock Audit Reports, Minutes of Task Force Meetings, Faculty Portfolios',
                'status' => 'Uploaded',
                'evidence_count' => 2,
            ]);
        }

        $program->recalculateOverallScore();
    }

    /**
     * Compute full DSS readiness analysis dynamically for ANY instrument structure
     */
    public function evaluateReadiness(AccreditationProgram $program): array
    {
        $areas = $program->areas()->with('benchmarks')->get();
        $targetLevel = $program->target_level ?: 'Level I Accredited';
        $thresholds = self::getLevelThresholds()[$targetLevel] ?? self::getLevelThresholds()['Level I Accredited'];

        $areaScores = [];
        $areaLabels = [];
        $targetScores = [];
        $deficitAreas = [];
        $mandatoryChecks = [];

        $grandMean = $program->recalculateOverallScore();

        foreach ($areas as $area) {
            $score = (float) $area->mean_score;
            $areaScores[] = $score;

            // Extract clean short title for Radar Chart
            $parts = explode(':', $area->area_name);
            $shortTitle = trim($parts[0] ?? "Area {$area->area_number}");
            if (isset($parts[1])) {
                $sub = trim(explode(',', $parts[1])[0]);
                $shortTitle .= ' ('.substr($sub, 0, 14).'..)';
            }
            $areaLabels[] = $shortTitle;
            $targetScores[] = $thresholds['min_grand_mean'];

            $isDeficit = false;
            $deficitReason = '';

            // Check minimum area threshold
            if ($score < $thresholds['min_area_score']) {
                $isDeficit = true;
                $deficitReason = "Score ({$score}) is below the minimum permissible area threshold of {$thresholds['min_area_score']}.";
            }

            // Check mandatory areas
            $isMandatory = $area->is_mandatory_level3 || in_array($area->area_number, $thresholds['mandatory_areas']);
            if ($isMandatory) {
                $mandatoryTarget = $thresholds['min_grand_mean'];
                $passed = $score >= $mandatoryTarget;
                if (! $passed) {
                    $isDeficit = true;
                    $deficitReason = "Mandatory core area requires minimum score of {$mandatoryTarget} (Current: {$score}).";
                }

                $mandatoryChecks[] = [
                    'area_number' => $area->area_number,
                    'area_name' => $area->area_name,
                    'current_score' => $score,
                    'required_score' => $mandatoryTarget,
                    'passed' => $passed,
                ];
            }

            if ($isDeficit) {
                $deficitAreas[] = [
                    'area_number' => $area->area_number,
                    'area_name' => $area->area_name,
                    'current_score' => $score,
                    'target_score' => $thresholds['min_grand_mean'],
                    'gap' => round($thresholds['min_grand_mean'] - $score, 2),
                    'reason' => $deficitReason,
                    'recommendation' => $this->generateAreaRecommendation($area->area_number, $area->area_name, $score, $thresholds['min_grand_mean']),
                ];
            }
        }

        // Overall readiness verdict
        $isGrandMeanPassed = $grandMean >= $thresholds['min_grand_mean'];
        $isMandatoryPassed = empty($deficitAreas);
        $isReady = $isGrandMeanPassed && $isMandatoryPassed;

        $readinessPercentage = min(100, round(($grandMean / max(1, $thresholds['min_grand_mean'])) * 100, 1));
        if (! $isMandatoryPassed) {
            $readinessPercentage = min(88.0, $readinessPercentage);
        }

        return [
            'program_id' => $program->id,
            'program_name' => $program->program_name,
            'instrument_template' => $program->instrument_template ?? 'AACCUP Standard (10 Survey Areas)',
            'current_level' => $program->current_level,
            'target_level' => $targetLevel,
            'grand_mean' => $grandMean,
            'required_grand_mean' => $thresholds['min_grand_mean'],
            'is_ready' => $isReady,
            'readiness_percentage' => $readinessPercentage,
            'is_grand_mean_passed' => $isGrandMeanPassed,
            'mandatory_checks' => $mandatoryChecks,
            'deficit_areas' => $deficitAreas,
            'total_deficit_count' => count($deficitAreas),
            'prescriptive_roadmap' => $this->buildPrescriptiveRoadmap($deficitAreas, $targetLevel),
            'chart_data' => [
                'labels' => $areaLabels,
                'current_scores' => $areaScores,
                'target_scores' => $targetScores,
            ],
        ];
    }

    /**
     * Generate prescriptive recommendations dynamically
     */
    protected function generateAreaRecommendation(int $areaNumber, string $areaName, float $currentScore, float $targetScore): string
    {
        $lowerName = strtolower($areaName);

        if (str_contains($lowerName, 'vmgo') || str_contains($lowerName, 'vision')) {
            return 'Conduct university-wide VMGO dissemination survey and secure updated Board of Regents resolution affirming alignment with regional development goals.';
        }
        if (str_contains($lowerName, 'faculty') || str_contains($lowerName, 'teaching')) {
            return 'Fast-track faculty scholarship completion for doctorate degrees, ensure full-time faculty hold relevant Master’s/Doctorate degrees, and update NBC 461 reclassification files.';
        }
        if (str_contains($lowerName, 'curriculum') || str_contains($lowerName, 'instruction') || str_contains($lowerName, 'academic excellence')) {
            return 'Align 100% of course syllabi to Outcomes-Based Education (OBE) and CHED Policies, Standards, and Guidelines (PSG). Update instructional materials with copyright/ISBN.';
        }
        if (str_contains($lowerName, 'student')) {
            return 'Institutionalize career placement tracking, expand guidance counseling psychological testing battery, and automate student scholarship clearance in IIPM-DSS.';
        }
        if (str_contains($lowerName, 'research') || str_contains($lowerName, 'innovation') || str_contains($lowerName, 'publication')) {
            return 'Increase Scopus/Web of Science indexed faculty publications (+3 papers needed). Allocate seed research grants and file utility model/patent applications with IPOPHL.';
        }
        if (str_contains($lowerName, 'extension') || str_contains($lowerName, 'community')) {
            return 'Establish sustainable community extension MOA with LGU adopted barangays in Agusan del Sur, focusing on agri-technology livelihood transfers and impact assessments.';
        }
        if (str_contains($lowerName, 'internationalization') || str_contains($lowerName, 'global')) {
            return 'Institutionalize cross-border inbound/outbound student & faculty mobility, align curricula with ASEAN University Network-QA (AUN-QA), and formalize global university consortia.';
        }
        if (str_contains($lowerName, 'governance') || str_contains($lowerName, 'administration') || str_contains($lowerName, 'iso')) {
            return 'Automate institutional processes through IIPM-DSS, complete ISO 9001:2015 surveillance audit, and maintain 95%+ financial budget execution rate.';
        }
        if (str_contains($lowerName, 'library')) {
            return 'Subscribe to online academic databases (ScienceDirect, IEEE, Scopus) and increase updated textbook holdings published within the last 5 years.';
        }
        if (str_contains($lowerName, 'laborator')) {
            return 'Calibrate precision laboratory instruments, update Chemical Safety Data Sheets (MSDS), and secure hazardous waste disposal compliance certificates (DENR-EMB).';
        }
        if (str_contains($lowerName, 'physical plant') || str_contains($lowerName, 'facility')) {
            return 'Conduct structural disaster risk and safety audit, install emergency exits and ramp accessibility (BP 344), and expand campus solar-powered smart lighting.';
        }

        return "Review parameter means of verification (MOVs) for {$areaName} and conduct Internal Quality Audit (IQA) mock review to remediate compliance gaps.";
    }

    /**
     * Build high-level action roadmap
     */
    protected function buildPrescriptiveRoadmap(array $deficitAreas, string $targetLevel): array
    {
        $roadmap = [];

        if (empty($deficitAreas)) {
            $roadmap[] = [
                'phase' => 'Phase 1: Validation',
                'action' => 'Endorse program to University Internal Quality Audit (IQA) Directorate for final Pre-Survey Mock Evaluation.',
                'timeline' => 'Immediate (1-2 weeks)',
                'priority' => 'High',
            ];
            $roadmap[] = [
                'phase' => 'Phase 2: Executive Transmittal',
                'action' => 'VPAA and SUC President endorsement to AACCUP Executive Secretariat for formal accreditation schedule.',
                'timeline' => '30 days prior to survey visit',
                'priority' => 'High',
            ];

            return $roadmap;
        }

        foreach ($deficitAreas as $index => $deficit) {
            $roadmap[] = [
                'phase' => 'Intervention '.($index + 1),
                'action' => "Remediate {$deficit['area_name']}: {$deficit['recommendation']}",
                'timeline' => '30-45 Days',
                'priority' => $deficit['gap'] > 0.5 ? 'Critical' : 'Moderate',
            ];
        }

        $roadmap[] = [
            'phase' => 'Mock Re-assessment',
            'action' => "Re-evaluate simulated grand mean after benchmark evidence upload to ensure compliance with {$targetLevel} standards.",
            'timeline' => 'Post-Intervention (60 Days)',
            'priority' => 'High',
        ];

        return $roadmap;
    }
}
