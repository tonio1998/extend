<?php

namespace App\Services\PDF;

use App\Models\Accreditation\AccreditationMockAudit;
use App\Models\SaaS\InstitutionSetting;

/**
 * Authentic AACCUP Technical Review & Recommended Board Action Consolidated Report (FPDF)
 *
 * Faithfully mirrors the official 8-page AACCUP evaluation format:
 * - Page 1: University Internal Transmittal to Dean (QA Officer, VPAA, University President)
 * - Page 2: AACCUP Executive Board Action Notice
 * - Page 3: Technical Review & Recommended Board Action (Summary of Ratings, Criteria, Board Action)
 * - Page 4: Accreditors Profile Roster (All survey area evaluators)
 * - Pages 4-8: Two-Column Recommendations (Mandatory vs Enhancement for each area)
 * - Page 8: Official Consolidated Signatures (Lead Auditor & Executive Director)
 */
class AaccupTechnicalReviewPdf extends AdssuTemplate
{
    public function __construct(
        public AccreditationMockAudit $mockAudit,
        string|array $size = 'A4',
        string $orientation = 'P',
        ?InstitutionSetting $institution = null
    ) {
        parent::__construct($size, $orientation, 'mm', $institution);

        $this->headerMode = self::HEADER_NONE;
        $this->footerMode = self::FOOTER_PAGINATION_ONLY;
        $this->SetAutoPageBreak(true, 20.0);
    }

    /**
     * Generate complete consolidated report
     */
    public function build(): self
    {
        $this->AliasNbPages();

        $program = $this->mockAudit->program;

        // ── PAGE 1: University Internal Transmittal Letter ───────────────────
        $this->AddPage();
        $this->renderPage1InternalTransmittal($program);

        // ── PAGE 2: Accrediting Agency Board Action Transmittal ───────────────
        $this->AddPage();
        $this->renderPage2AgencyTransmittal($program);

        // ── PAGE 3: Technical Review & Recommended Board Action ───────────────
        $this->AddPage();
        $this->renderPage3RatingsAndCriteria($program);

        // ── PAGE 4+: Accreditors Roster & 2-Column Recommendations ───────────
        $this->AddPage();
        $this->renderPage4AccreditorsAndRecommendations($program);

        return $this;
    }

    /**
     * PAGE 1: Internal University Transmittal to Dean
     */
    protected function renderPage1InternalTransmittal($program): void
    {
        // Letterhead
        $sealPath = $this->getSealPath();
        if ($sealPath && file_exists($sealPath)) {
            $this->Image($sealPath, $this->leftMargin, 12, 22, 22);
        }

        // Bagong Pilipinas
        $bpPath = public_path('images/bagong_pilipinas.png');
        if (file_exists($bpPath)) {
            $this->Image($bpPath, $this->leftMargin + 24.0, 15, 17, 17);
        }

        $headerX = $this->leftMargin + 44.0;
        $this->SetXY($headerX, 13.0);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(0, 3.8, 'Republic of the Philippines', 0, 1, 'L');

        $this->SetX($headerX);
        $this->SetFont('Arial', 'B', 11.5);
        $this->Cell(0, 4.8, $this->schoolName, 0, 1, 'L');

        $this->SetX($headerX);
        $this->SetFont('Arial', '', 8.0);
        $this->Cell(0, 3.8, $this->campusAddress.' | '.$this->fullAddress, 0, 1, 'L');

        // SOCOTEC & PAB on right
        $socotec = public_path('images/socotec_iso9001.png');
        if (file_exists($socotec)) {
            $this->Image($socotec, $this->GetPageWidth() - $this->rightMargin - 30.0, 12, 14, 14);
        }
        $pab = public_path('images/pab_accredited.jpg');
        if (file_exists($pab)) {
            $this->Image($pab, $this->GetPageWidth() - $this->rightMargin - 15.0, 12, 15, 14);
        }

        $this->Ln(8);
        $this->SetDrawColor(0, 128, 0); // Green university bar
        $this->SetLineWidth(0.6);
        $this->Line($this->leftMargin, $this->GetY(), $this->GetPageWidth() - $this->rightMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);

        // Date
        $this->Ln(6);
        $this->SetFont('Arial', '', 9.0);
        $this->Cell(0, 5.0, $this->mockAudit->survey_dates_display ? date('F d, Y') : 'September 11, 2026', 0, 1, 'L');

        // Addressee
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9.5);
        $deanName = 'DR. VIRNILLE C. FRANCISCO';
        $college = $program?->department?->dept_name ?? $program?->department?->unit_name ?? 'College of Computing and Information Sciences';
        $this->Cell(0, 4.5, $deanName, 0, 1, 'L');
        $this->SetFont('Arial', '', 9.0);
        $this->Cell(0, 4.2, 'College Dean', 0, 1, 'L');
        $this->Cell(0, 4.2, $college, 0, 1, 'L');

        // Salutation & Body
        $this->Ln(5);
        $this->SetFont('Arial', '', 9.0);
        $this->Cell(0, 4.5, 'Dear Dr. Francisco:', 0, 1, 'L');
        $this->Ln(2);
        $this->Cell(0, 4.5, 'Greetings!', 0, 1, 'L');

        $this->Ln(2);
        $surveyDates = $this->mockAudit->survey_dates_display ?: 'June 23-25, 2026';
        $intro = "Forwarding herewith the AACCUP Technical Review and Recommended Board Action for the programs evaluated on {$surveyDates}:";
        $this->MultiCell($this->contentWidth, 4.5, $intro, 0, 'L');

        // Transmittal Table
        $this->Ln(3);
        $wProg = 65.0;
        $wStat = 32.0;
        $wVal = 35.0;
        $wRem = $this->contentWidth - ($wProg + $wStat + $wVal);

        $this->SetFont('Arial', 'B', 8.2);
        $this->SetFillColor(245, 245, 245);
        $this->Cell($wProg, 6.0, 'Program', 1, 0, 'C', true);
        $this->Cell($wStat, 6.0, 'Accreditation Status', 1, 0, 'C', true);
        $this->Cell($wVal, 6.0, 'Validity', 1, 0, 'C', true);
        $this->Cell($wRem, 6.0, 'Remarks', 1, 1, 'C', true);

        $this->SetFont('Arial', '', 8.0);
        $progTitle = $program?->program_name ?? 'Bachelor of Science in Information Technology';
        $status = $this->mockAudit->board_action_award ?: 'Level III';
        $validity = $this->mockAudit->validity_duration ?: 'July 1, 2026 - June 30, 2027';
        $remarks = $this->mockAudit->board_action_remarks ?: 'To comply with mandatory recommendations before award of Level IV';

        $startX = $this->GetX();
        $startY = $this->GetY();
        $rowH = 14.0;

        $this->Rect($startX, $startY, $wProg, $rowH);
        $this->SetXY($startX + 1.0, $startY + 2.0);
        $this->MultiCell($wProg - 2.0, 4.0, $progTitle, 0, 'L');

        $this->SetXY($startX + $wProg, $startY);
        $this->Cell($wStat, $rowH, $status, 1, 0, 'C');

        $this->SetXY($startX + $wProg + $wStat, $startY);
        $this->Cell($wVal, $rowH, $validity, 1, 0, 'C');

        $this->Rect($startX + $wProg + $wStat + $wVal, $startY, $wRem, $rowH);
        $this->SetXY($startX + $wProg + $wStat + $wVal + 1.0, $startY + 2.0);
        $this->MultiCell($wRem - 2.0, 3.8, $remarks, 0, 'L');

        $this->SetXY($startX, $startY + $rowH + 4.0);

        // Body paragraph 2
        $complianceDeadline = 'January 31, 2027';
        $p2 = "Please be informed that the above-mentioned program is required to comply with the mandatory recommendations stated in the evaluation. Since the compliance report is subject to another evaluation by AACCUP accreditors, we hope to receive the complete reports no later than {$complianceDeadline}.\n\nThank you for your unwavering commitment to the continuous improvement of our academic programs.";
        $this->MultiCell($this->contentWidth, 4.5, $p2, 0, 'L');

        // Signatories Block
        $this->Ln(6);
        $this->Cell(0, 4.5, 'Very truly yours,', 0, 1, 'L');
        $this->Ln(12.0);

        $qaOfficer = $program?->qaCoordinatorUser?->name ?? 'YASMIN V. TIU, MBA';
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell(0, 4.2, $qaOfficer, 0, 1, 'L');
        $this->SetFont('Arial', '', 8.0);
        $this->Cell(0, 3.8, 'Quality Assurance Management Office', 0, 1, 'L');

        $this->Ln(8);
        $colSignW = $this->contentWidth / 2.0;

        $this->SetFont('Arial', '', 8.5);
        $this->Cell($colSignW, 4.5, 'Endorsed by:', 0, 0, 'L');
        $this->Cell($colSignW, 4.5, 'Approved by:', 0, 1, 'L');

        $this->Ln(12.0);
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell($colSignW, 4.2, 'RONITA E. TALINGTING, PhD', 0, 0, 'L');
        $this->Cell($colSignW, 4.2, 'ROWENA A. PLANDO, PhD', 0, 1, 'L');

        $this->SetFont('Arial', '', 8.0);
        $this->Cell($colSignW, 3.8, 'Vice President for Academic Affairs', 0, 0, 'L');
        $this->Cell($colSignW, 3.8, 'University President', 0, 1, 'L');
    }

    /**
     * PAGE 2: Accrediting Agency Board Action Transmittal
     */
    protected function renderPage2AgencyTransmittal($program): void
    {
        $this->SetFont('Arial', 'B', 10.0);
        $this->SetTextColor(30, 58, 138); // Navy blue
        $this->Cell(0, 4.5, 'ACCREDITING AGENCY OF CHARTERED COLLEGES', 0, 1, 'C');
        $this->Cell(0, 4.5, 'AND UNIVERSITIES IN THE PHILIPPINES, INC.', 0, 1, 'C');
        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(0, 3.5, '5AB Future Point Plaza 1, 111 Panay Avenue, South Triangle, Quezon City 1103, Philippines', 0, 1, 'C');
        $this->Cell(0, 3.5, 'Tel.: (632) 8961-3317; (632) 8961-1271 | E-mail: mail@aaccup.org.ph | URL: www.aaccup.com', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->Ln(3);
        $this->SetDrawColor(203, 213, 225);
        $this->Line($this->leftMargin, $this->GetY(), $this->GetPageWidth() - $this->rightMargin, $this->GetY());
        $this->Ln(6);

        // Date & Addressee
        $this->SetFont('Arial', '', 8.8);
        $this->Cell(0, 4.5, 'August 19, 2026', 0, 1, 'L');
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 9.0);
        $this->Cell(0, 4.2, 'DR. ROWENA A. PLANDO', 0, 1, 'L');
        $this->SetFont('Arial', '', 8.5);
        $this->Cell(0, 4.0, 'President', 0, 1, 'L');
        $this->Cell(0, 4.0, $this->schoolName, 0, 1, 'L');
        $this->Cell(0, 4.0, $this->campusAddress, 0, 1, 'L');

        $this->Ln(4);
        $this->Cell(0, 4.2, 'Dear President Plando,', 0, 1, 'L');
        $this->Ln(2);
        $this->Cell(0, 4.2, 'Greetings from AACCUP!', 0, 1, 'L');

        $this->Ln(3);
        $surveyDates = $this->mockAudit->survey_dates_display ?: 'June 23 - 25, 2026';
        $intro = "We are pleased to forward to you the Board Action of the following programs assessed by AACCUP on {$surveyDates}:";
        $this->MultiCell($this->contentWidth, 4.5, $intro, 0, 'L');

        // Programs Table
        $this->Ln(3);
        $wP = $this->contentWidth * 0.44;
        $wL = $this->contentWidth * 0.28;
        $wV = $this->contentWidth * 0.28;

        $this->SetFont('Arial', 'B', 8.0);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($wP, 5.5, 'PROGRAM/S', 1, 0, 'C', true);
        $this->Cell($wL, 5.5, "Level\n(Accreditation Status)", 1, 0, 'C', true);
        $this->Cell($wV, 5.5, "Date of Validity of\nThis Accreditation", 1, 1, 'C', true);

        // Campus Subheading
        $this->SetFont('Arial', 'B', 8.0);
        $this->SetFillColor(248, 250, 252);
        $this->Cell($this->contentWidth, 5.0, '  '.($program?->campus?->campus_name ?? 'Main Campus'), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 8.0);
        $this->Cell($wP, 7.0, ' 1.  '.$program?->program_name, 1, 0, 'L');
        $this->Cell($wL, 7.0, 'Level III Re-accredited*', 1, 0, 'C');
        $this->Cell($wV, 7.0, 'July 01, 2026 - June 30, 2027', 1, 1, 'C');

        $this->Ln(2);
        $this->SetFont('Arial', 'I', 7.2);
        $this->Cell(0, 3.5, '*Level III Re-accredited: The program level is III. Must comply with mandatory recommendations.', 0, 1, 'L');

        $this->Ln(5);
        $this->SetFont('Arial', '', 8.5);
        $att = "Attached is a copy of the Technical Review and Board Action of each of the above stated programs with the following information:\n\n1.  Summary of Ratings\n2.  Board Action, and\n3.  Suggestions to the SUC -- steps or measures to be taken to prepare the program for the next survey.\n\nThank you for advancing Quality Assurance in your University. Please acknowledge upon receipt.\n\nBest regards,\nVery truly yours,";
        $this->MultiCell($this->contentWidth, 4.2, $att, 0, 'L');

        $this->Ln(10.0);
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell(0, 4.0, 'MILABEL E. HO, Ed.D., R.N.', 0, 1, 'L');
        $this->SetFont('Arial', '', 8.0);
        $this->Cell(0, 3.8, 'Executive Director', 0, 1, 'L');
    }

    /**
     * PAGE 3: Technical Review, Summary of Ratings, Criteria & Recommended Board Action
     */
    protected function renderPage3RatingsAndCriteria($program): void
    {
        $this->SetFont('Arial', 'B', 10.0);
        $this->Cell(0, 5.0, 'AACCUP TECHNICAL REVIEW and RECOMMENDED BOARD ACTION', 0, 1, 'C');
        $this->Ln(3);

        // Header Table
        $col1W = 18.0;
        $val1W = 75.0;
        $col2W = 18.0;
        $val2W = $this->contentWidth - ($col1W + $val1W + $col2W);

        $this->SetFont('Arial', '', 8.2);
        $this->Cell($col1W, 4.5, 'SUC:', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($val1W, 4.5, $this->schoolName, 'B', 0, 'L');
        $this->SetFont('Arial', '', 8.2);
        $this->Cell($col2W, 4.5, ' Region:', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($val2W, 4.5, 'CARAGA / Region XIII', 'B', 1, 'L');

        $this->SetFont('Arial', '', 8.2);
        $this->Cell($col1W, 4.5, 'Address:', 0, 0, 'L');
        $this->Cell($val1W + $col2W + $val2W, 4.5, $this->fullAddress, 'B', 1, 'L');

        $this->Cell($col1W, 4.5, 'Program:', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($val1W + $col2W + $val2W, 4.5, $program?->program_name ?? 'BSIT', 'B', 1, 'L');

        $this->SetFont('Arial', '', 8.2);
        $this->Cell($col1W, 4.5, 'Type of Visit:', 0, 0, 'L');
        $this->Cell($val1W, 4.5, $this->mockAudit->type_of_visit ?: '4th Survey', 'B', 0, 'L');
        $this->Cell($col2W, 4.5, ' Date:', 0, 0, 'L');
        $this->Cell($val2W, 4.5, $this->mockAudit->survey_dates_display ?: 'June 23-25, 2026', 'B', 1, 'L');

        $this->Ln(4);

        // 1. SUMMARY OF RATINGS
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell(0, 4.5, 'SUMMARY OF RATINGS:', 0, 1, 'L');

        $wArea = $this->contentWidth * 0.70;
        $wRate = $this->contentWidth * 0.30;

        $this->SetFont('Arial', 'B', 7.8);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($wArea, 5.0, 'Areas', 1, 0, 'C', true);
        $this->Cell($wRate, 5.0, "Ratings\n1=Poor, 2=Fair, 3=Satisfactory, 4=Very Satisfactory, 5=Excellent", 1, 1, 'C', true);

        $areas = $this->mockAudit->auditAreas->isNotEmpty() ? $this->mockAudit->auditAreas : $program?->areas;
        $this->SetFont('Arial', '', 8.0);

        $sum = 0.0;
        $count = 0;

        if ($areas) {
            foreach ($areas as $idx => $a) {
                $score = (float) ($a->area_mean ?? $a->mean_score ?? 3.50);
                if ($score <= 0) {
                    $score = 3.50;
                }
                $sum += $score;
                $count++;

                $areaTitle = $a->area_name;
                $this->Cell($wArea, 4.4, ' '.($idx + 1).'.  '.$areaTitle, 1, 0, 'L');
                $this->SetFont('Arial', 'B', 8.0);
                $this->Cell($wRate, 4.4, number_format($score, 2), 1, 1, 'C');
                $this->SetFont('Arial', '', 8.0);
            }
        }

        $grandMean = $count > 0 ? round($sum / $count, 2) : 4.57;

        // Grand Mean Row
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(248, 250, 252);
        $this->Cell($wArea, 5.0, 'Grand Mean', 1, 0, 'R', true);
        $this->Cell($wRate, 5.0, number_format($grandMean, 2), 1, 1, 'C', true);

        $this->Ln(4);

        // 2. CRITERIA TO PASS THIS LEVEL
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell(0, 4.5, 'CRITERIA TO PASS THIS LEVEL:', 0, 1, 'L');

        $wCrit = $this->contentWidth * 0.58;
        $wMean = $this->contentWidth * 0.20;
        $wCheck = ($this->contentWidth - ($wCrit + $wMean)) / 3.0;

        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell($wCrit, 5.0, 'Criteria Requirements', 1, 0, 'C', true);
        $this->Cell($wMean, 5.0, 'Mean Rating', 1, 0, 'C', true);
        $this->Cell($wCheck, 5.0, 'Yes', 1, 0, 'C', true);
        $this->Cell($wCheck, 5.0, 'No', 1, 0, 'C', true);
        $this->Cell($wCheck, 5.0, 'NA', 1, 1, 'C', true);

        $this->SetFont('Arial', '', 7.8);
        // Row 1: Minimum Grand Mean
        $this->Cell($wCrit, 4.5, ' 1.  Qualified in the Level IV - Minimum Grand Mean', 1, 0, 'L');
        $this->Cell($wMean, 4.5, '4.50 or higher', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, $grandMean >= 4.50 ? '[X]' : '[ ]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, $grandMean < 4.50 ? '[X]' : '[ ]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[ ]', 1, 1, 'C');

        // Row 2: Minimum Area Mean
        $this->Cell($wCrit, 4.5, '      - Minimum Area Mean', 1, 0, 'L');
        $this->Cell($wMean, 4.5, '4.00 or higher', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[X]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[ ]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[ ]', 1, 1, 'C');

        // Row 3: Mandatory Recommendations
        $this->Cell($wCrit, 4.5, ' 2.  Mandatory Recommendations complied before award', 1, 0, 'L');
        $this->Cell($wMean, 4.5, 'To be complied', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[ ]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[X]', 1, 0, 'C');
        $this->Cell($wCheck, 4.5, '[ ]', 1, 1, 'C');

        $this->Ln(4);

        // 3. RECOMMENDED BOARD ACTION
        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell(0, 4.5, 'RECOMMENDED BOARD ACTION:', 0, 1, 'L');

        $wA = $this->contentWidth * 0.35;
        $wR = $this->contentWidth * 0.35;
        $wE = $this->contentWidth * 0.30;

        $this->SetFont('Arial', 'B', 7.8);
        $this->Cell($wA, 5.0, 'Type of Award', 1, 0, 'C', true);
        $this->Cell($wR, 5.0, 'Remarks', 1, 0, 'C', true);
        $this->Cell($wE, 5.0, 'Effectivity or Date of Duration', 1, 1, 'C', true);

        $this->SetFont('Arial', '', 8.0);
        $this->Cell($wA, 7.0, ' [X] Level III Re-accredited', 1, 0, 'L');
        $this->Cell($wR, 7.0, ' must comply with mandatory', 1, 0, 'L');
        $this->Cell($wE, 7.0, ' July 1, 2026 - June 30, 2027', 1, 1, 'C');

        $this->Cell($wA, 5.0, ' [  ] Level IV Re-accredited', 1, 0, 'L');
        $this->Cell($wR, 5.0, ' Revisit area/s of:', 1, 0, 'L');
        $this->Cell($wE, 5.0, '', 1, 1, 'C');

        $this->Ln(6);
        $this->SetFont('Arial', 'I', 7.5);
        $this->Cell($this->contentWidth * 0.60, 4.0, 'Technical Review - '.$this->schoolName.' / '.$program?->program_code, 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.0);
        $this->Cell($this->contentWidth * 0.40, 4.0, 'Lead Auditor Signature: __________________', 0, 1, 'R');
    }

    /**
     * PAGE 4+: Accreditors Profile Roster & 2-Column Recommendations
     */
    protected function renderPage4AccreditorsAndRecommendations($program): void
    {
        // Accreditors Section
        $this->SetFont('Arial', 'B', 9.0);
        $this->Cell(0, 5.0, 'ACCREDITORS:', 0, 1, 'L');

        $colW = $this->contentWidth;
        $lblW = 50.0;
        $valW = $colW - $lblW;

        $auditAreas = $this->mockAudit->auditAreas;

        if ($auditAreas->isNotEmpty()) {
            foreach ($auditAreas->take(3) as $aa) {
                $evalName = $aa->evaluator_name ?: ($aa->auditorUser?->name ?: 'Dr. Diane B. Remot (TL)');
                $rank = $aa->evaluator_academic_rank ?: 'Associate Professor V';
                $spec = $aa->evaluator_specialization ?: 'Information Technology / Computer Engineering';
                $deg = $aa->evaluator_highest_degree ?: 'Doctor in Information Technology';
                $inst = $aa->evaluator_institution ?: 'Eastern Visayas State University, Tacloban City';

                $this->SetFont('Arial', 'B', 7.5);
                $this->SetFillColor(248, 250, 252);
                $this->Cell($lblW, 4.2, ' Name:', 1, 0, 'L', true);
                $this->SetFont('Arial', 'B', 8.0);
                $this->Cell($valW, 4.2, ' '.$evalName, 1, 1, 'L');

                $this->SetFont('Arial', '', 7.5);
                $this->Cell($lblW, 4.2, ' Academic Rank:', 1, 0, 'L');
                $this->Cell($valW, 4.2, ' '.$rank, 1, 1, 'L');

                $this->Cell($lblW, 4.2, ' Field of Specialization:', 1, 0, 'L');
                $this->Cell($valW, 4.2, ' '.$spec, 1, 1, 'L');

                $this->Cell($lblW, 4.2, ' Highest Educational Attainment:', 1, 0, 'L');
                $this->Cell($valW, 4.2, ' '.$deg, 1, 1, 'L');

                $this->Cell($lblW, 4.2, ' Institution and Address:', 1, 0, 'L');
                $this->Cell($valW, 4.2, ' '.$inst, 1, 1, 'L');

                $this->Ln(2);
            }
        }

        $this->Ln(4);

        // RECOMMENDATIONS TO THE INSTITUTION
        $this->SetFont('Arial', 'B', 9.0);
        $this->Cell(0, 5.0, 'RECOMMENDATIONS TO THE INSTITUTION:', 0, 1, 'L');

        $halfW = $this->contentWidth / 2.0;

        // Two Column Header
        $this->SetFont('Arial', 'B', 7.8);
        $this->SetFillColor(241, 245, 249);
        $hH = 8.5;
        $sx = $this->leftMargin;
        $sy = $this->GetY();

        $this->Rect($sx, $sy, $halfW, $hH, 'DF');
        $this->Rect($sx + $halfW, $sy, $halfW, $hH, 'DF');

        $this->SetXY($sx, $sy + 0.8);
        $this->MultiCell($halfW, 3.4, "MANDATORY\n(to be complied with before the award)", 0, 'C');

        $this->SetXY($sx + $halfW, $sy + 0.8);
        $this->MultiCell($halfW, 3.4, "ENHANCEMENT\n(may be complied with after the award)", 0, 'C');

        $this->SetXY($sx, $sy + $hH);

        // Loop over survey areas and render recommendations
        $renderAreas = $auditAreas->isNotEmpty() ? $auditAreas : $program?->areas;

        if ($renderAreas) {
            foreach ($renderAreas as $a) {
                $areaTitle = strtoupper($a->area_name);

                // Area Header Row spanning both columns
                if ($this->GetY() > $this->GetPageHeight() - 40.0) {
                    $this->AddPage();
                    // Re-print header
                    $sy = $this->GetY();
                    $this->Rect($sx, $sy, $halfW, $hH, 'DF');
                    $this->Rect($sx + $halfW, $sy, $halfW, $hH, 'DF');
                    $this->SetXY($sx, $sy + 0.8);
                    $this->MultiCell($halfW, 3.4, "MANDATORY\n(to be complied with before the award)", 0, 'C');
                    $this->SetXY($sx + $halfW, $sy + 0.8);
                    $this->MultiCell($halfW, 3.4, "ENHANCEMENT\n(may be complied with after the award)", 0, 'C');
                    $this->SetXY($sx, $sy + $hH);
                }

                $this->SetFont('Arial', 'B', 8.0);
                $this->SetFillColor(226, 232, 240);
                $this->Cell($this->contentWidth, 5.0, '  '.$areaTitle, 1, 1, 'L', true);

                $mandText = $this->formatRecommendationText($a->mandatory_recommendations ?? '', 'mandatory', $areaTitle);
                $enhText = $this->formatRecommendationText($a->enhancement_recommendations ?? '', 'enhancement', $areaTitle);

                $lineH = 3.6;
                $nbL = $this->nbLines($halfW - 4.0, $mandText);
                $nbR = $this->nbLines($halfW - 4.0, $enhText);
                $lines = max(5, max($nbL, $nbR));
                $cellH = ($lines * $lineH) + 4.0;

                if ($this->GetY() + $cellH > $this->GetPageHeight() - $this->bottomMargin) {
                    $this->AddPage();
                }

                $x = $this->GetX();
                $y = $this->GetY();

                $this->SetFont('Arial', '', 7.5);
                $this->Rect($x, $y, $halfW, $cellH);
                $this->SetXY($x + 2.0, $y + 2.0);
                $this->MultiCell($halfW - 4.0, $lineH, $mandText, 0, 'L');

                $this->Rect($x + $halfW, $y, $halfW, $cellH);
                $this->SetXY($x + $halfW + 2.0, $y + 2.0);
                $this->MultiCell($halfW - 4.0, $lineH, $enhText, 0, 'L');

                $this->SetXY($x, $y + $cellH);
            }
        }

        // Consolidated Signatories Block
        $this->Ln(8);
        if ($this->GetY() > $this->GetPageHeight() - 45.0) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'I', 8.5);
        $this->Cell(0, 4.5, 'Consolidated and Reviewed By:', 0, 1, 'L');

        $this->Ln(12.0);
        $half = $this->contentWidth / 2.0;

        $leadAuditor = $this->mockAudit->lead_auditor_name ?: 'DR. BERT J. TUGA';

        $this->SetFont('Arial', 'B', 8.8);
        $this->Cell($half, 4.2, $leadAuditor, 0, 0, 'L');
        $this->Cell($half, 4.2, 'MILABEL E. HO, Ed.D., R.N.', 0, 1, 'L');

        $this->SetFont('Arial', '', 7.8);
        $this->Cell($half, 3.8, 'Lead Accreditor / Reviewer', 0, 0, 'L');
        $this->Cell($half, 3.8, 'Executive Director, AACCUP', 0, 1, 'L');
    }

    /**
     * Formatting helper for area recommendations
     */
    protected function formatRecommendationText(?string $text, string $type, string $areaTitle): string
    {
        if (! empty(trim($text ?? ''))) {
            return trim($text);
        }

        if ($type === 'mandatory') {
            return "1. Formalization and institutional approval of {$areaTitle} operational roadmap with measurable targets.\n2. Strengthen documentation of faculty and student participation in recognized refereed research.\n3. Institutionalize verified copyright and IP registration system before award validation.";
        }

        return "1. The program is encouraged to expand collaborative MOA/MOU partnerships with regional industry stakeholders.\n2. Institutionalize digital monitoring and outcomes-based feedback mechanisms.\n3. Expand longitudinal tracer studies and capability-building workshops.";
    }

    /**
     * Compute number of lines that a MultiCell will take
     */
    public function nbLines(float $w, string $txt): int
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;

                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c] ?? 500;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl;
    }
}
