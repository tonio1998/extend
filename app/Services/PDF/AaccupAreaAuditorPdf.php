<?php

namespace App\Services\PDF;

use App\Models\Accreditation\AccreditationMockAuditArea;
use App\Models\SaaS\InstitutionSetting;

/**
 * AACCUP Mock Audit — Individual Area Evaluation Report (FPDF)
 *
 * Dedicated report generated for the assigned auditor for their specific survey area.
 * Contains:
 * - Accreditor Profile Box (Name, Academic Rank, Specialization, Highest Degree, Institution)
 * - Area Mean Score & Compliance Descriptor
 * - Parameter-by-Parameter Ratings & Findings
 * - Authentic AACCUP 2-Column Recommendations (Mandatory vs Enhancement)
 * - Evaluator Observations & Official Signature Block
 */
class AaccupAreaAuditorPdf extends AdssuTemplate
{
    public function __construct(
        public AccreditationMockAuditArea $auditArea,
        string|array $size = 'A4',
        string $orientation = 'P',
        ?InstitutionSetting $institution = null
    ) {
        parent::__construct($size, $orientation, 'mm', $institution);

        $this->headerMode = self::HEADER_NONE;
        $this->footerMode = self::FOOTER_PAGINATION_ONLY;
        $this->SetAutoPageBreak(true, 22.0);
    }

    /**
     * Generate and output PDF
     */
    public function build(): self
    {
        $this->AliasNbPages();
        $this->AddPage();

        $audit = $this->auditArea->mockAudit;
        $program = $audit?->program ?? $this->auditArea->area?->program;
        $area = $this->auditArea->area;

        // 1. Header & Letterhead
        $this->renderReportHeader($program);

        // 2. Report Title Strip
        $this->renderTitleStrip($program, $audit);

        // 3. Accreditor Credentials Profile Table
        $this->renderAccreditorProfile();

        // 4. Area Rating Summary Strip
        $this->renderAreaRatingSummary();

        // 5. Parameter Ratings & Benchmark Breakdown Table
        $this->renderParametersTable($area);

        // 6. Two-Column Recommendations (Mandatory vs Enhancement)
        $this->renderTwoColumnRecommendations();

        // 7. Overall Evaluator Observations & Comments
        $this->renderEvaluatorComments();

        // 8. Official Auditor Signature Block
        $this->renderSignatureBlock();

        return $this;
    }

    /**
     * 1. Institutional Header
     */
    protected function renderReportHeader($program): void
    {
        $sealPath = $this->getSealPath();
        if ($sealPath && file_exists($sealPath)) {
            $this->Image($sealPath, $this->leftMargin, 12, 22, 22);
        }

        $contentX = $this->leftMargin + 26.0;
        $contentW = $this->contentWidth - 26.0;

        $this->SetXY($contentX, 12);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($contentW, 3.8, 'Republic of the Philippines', 0, 1, 'L');

        $this->SetX($contentX);
        $this->SetFont('Arial', 'B', 11.0);
        $this->Cell($contentW, 4.8, $this->schoolName, 0, 1, 'L');

        $this->SetX($contentX);
        $this->SetFont('Arial', '', 8.0);
        $campus = $program?->campus?->campus_name ?? 'Main Campus';
        $college = $program?->department?->dept_name ?? $program?->department?->unit_name ?? 'College of Computing and Information Sciences';
        $this->Cell($contentW, 3.6, $campus.' | '.$college, 0, 1, 'L');

        $this->SetX($contentX);
        $this->SetFont('Arial', 'I', 7.8);
        $this->SetTextColor(71, 85, 105);
        $this->Cell($contentW, 3.6, 'Quality Assurance Management & Accreditation Center', 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);

        $this->Ln(3);
        $this->SetDrawColor(15, 23, 42);
        $this->SetLineWidth(0.4);
        $this->Line($this->leftMargin, $this->GetY(), $this->leftMargin + $this->contentWidth, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(4);
    }

    /**
     * 2. Report Title Strip
     */
    protected function renderTitleStrip($program, $audit): void
    {
        [$r, $g, $b] = $this->getThemeTintRgb(0.20);
        $this->SetFillColor($r, $g, $b);
        $this->SetDrawColor(148, 163, 184);

        $this->SetFont('Arial', 'B', 10.5);
        $this->Cell($this->contentWidth, 6.5, 'AACCUP MOCK AUDIT — INDIVIDUAL SURVEY AREA REPORT', 1, 1, 'C', true);

        // Metadata table (2 rows x 2 cols)
        $col1W = $this->contentWidth * 0.58;
        $col2W = $this->contentWidth * 0.42;

        $this->SetFont('Arial', '', 8.5);
        $this->Cell($col1W, 5.0, ' Program: '.($program?->program_code.' — '.$program?->program_name), 1, 0, 'L');
        $this->Cell($col2W, 5.0, ' Target Level: '.($audit?->target_level ?? $program?->target_level ?? 'Level III'), 1, 1, 'L');

        $this->Cell($col1W, 5.0, ' Area: Area '.$this->auditArea->area_number.' — '.$this->auditArea->area_name, 1, 0, 'L');
        $this->Cell($col2W, 5.0, ' Survey Dates: '.($audit?->survey_dates_display ?: date('F d, Y')), 1, 1, 'L');

        $this->Ln(4);
    }

    /**
     * 3. Accreditor Credentials Profile Table
     */
    protected function renderAccreditorProfile(): void
    {
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($this->contentWidth, 5.0, 'ACCREDITOR / EVALUATOR CREDENTIALS:', 1, 1, 'L', true);

        $evaluatorName = $this->auditArea->evaluator_name ?: ($this->auditArea->auditorUser?->name ?: 'Not Specified');
        $rank = $this->auditArea->evaluator_academic_rank ?: 'Associate Professor / Evaluator';
        $spec = $this->auditArea->evaluator_specialization ?: 'Information Technology / Computer Studies';
        $degree = $this->auditArea->evaluator_highest_degree ?: 'Doctor in Information Technology / Ph.D.';
        $institution = $this->auditArea->evaluator_institution ?: $this->schoolName;

        $lblW = 46.0;
        $valW = $this->contentWidth - $lblW;

        $rows = [
            ['Name:', $evaluatorName],
            ['Academic Rank:', $rank],
            ['Field of Specialization:', $spec],
            ['Highest Educational Attainment:', $degree],
            ['Institution & Address:', $institution],
        ];

        $this->SetFont('Arial', '', 8.0);
        foreach ($rows as $r) {
            $this->SetFont('Arial', 'B', 7.8);
            $this->Cell($lblW, 4.4, '  '.$r[0], 1, 0, 'L');
            $this->SetFont('Arial', '', 8.0);
            $this->Cell($valW, 4.4, '  '.$r[1], 1, 1, 'L');
        }

        $this->Ln(4);
    }

    /**
     * 4. Area Rating Summary Strip
     */
    protected function renderAreaRatingSummary(): void
    {
        $mean = (float) ($this->auditArea->area_mean > 0 ? $this->auditArea->area_mean : ($this->auditArea->area?->mean_score ?? 0.00));
        $descriptor = 'Poor';
        if ($mean >= 4.50) {
            $descriptor = 'Excellent (Exceeds Benchmark Standards)';
        } elseif ($mean >= 3.50) {
            $descriptor = 'Very Satisfactory (Meets & Validates Criteria)';
        } elseif ($mean >= 2.50) {
            $descriptor = 'Satisfactory (Marginal Compliance)';
        } elseif ($mean >= 1.50) {
            $descriptor = 'Fair (Deficiencies Identified)';
        }

        [$r, $g, $b] = $mean >= 3.50 ? [240, 253, 244] : [254, 242, 242];
        $this->SetFillColor($r, $g, $b);
        $this->SetDrawColor(203, 213, 225);

        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($this->contentWidth * 0.45, 6.0, ' Area Mean Score: '.number_format($mean, 2).' / 5.00', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 8.0);
        $this->Cell($this->contentWidth * 0.55, 6.0, ' Qualitative Rating: '.$descriptor, 1, 1, 'L', true);

        $this->Ln(4);
    }

    /**
     * 5. Parameter Ratings Table
     */
    protected function renderParametersTable($area): void
    {
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(226, 232, 240);
        $this->Cell($this->contentWidth, 5.0, 'PARAMETER EVALUATION & BENCHMARK BREAKDOWN:', 1, 1, 'L', true);

        // Table Header
        $wCode = 20.0;
        $wDesc = 82.0;
        $wScore = 18.0;
        $wRem = $this->contentWidth - ($wCode + $wDesc + $wScore);

        $this->SetFont('Arial', 'B', 7.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($wCode, 5.0, 'Code', 1, 0, 'C', true);
        $this->Cell($wDesc, 5.0, 'Parameter / Criteria Description', 1, 0, 'L', true);
        $this->Cell($wScore, 5.0, 'Rating', 1, 0, 'C', true);
        $this->Cell($wRem, 5.0, 'Auditor Findings / Remarks', 1, 1, 'L', true);

        $benchmarks = $area ? $area->benchmarks : collect();

        $this->SetFont('Arial', '', 7.5);
        foreach ($benchmarks as $bm) {
            $score = $bm->rating !== null ? number_format((float) $bm->rating, 2) : '3.00';
            $remarks = $bm->validation_remarks ?: ($bm->iqa_evaluator_remarks ?: 'Complied with documented MOVs.');

            // Calculate height
            $descLines = $this->nbLines($wDesc, $bm->parameter_letter.' - '.$bm->description);
            $remLines = $this->nbLines($wRem, $remarks);
            $lineCount = max(1, min(6, max($descLines, $remLines)));
            $rowH = $lineCount * 3.8;

            if ($this->GetY() + $rowH > $this->GetPageHeight() - $this->bottomMargin) {
                $this->AddPage();
            }

            $startX = $this->GetX();
            $startY = $this->GetY();

            // Code
            $this->Cell($wCode, $rowH, $bm->benchmark_code, 1, 0, 'C');

            // Description
            $descX = $this->GetX();
            $this->Rect($descX, $startY, $wDesc, $rowH);
            $this->MultiCell($wDesc, 3.8, substr($bm->parameter_letter.': '.$bm->description, 0, 160), 0, 'L');
            $this->SetXY($descX + $wDesc, $startY);

            // Rating
            $this->SetFont('Arial', 'B', 7.8);
            $this->Cell($wScore, $rowH, $score, 1, 0, 'C');
            $this->SetFont('Arial', '', 7.5);

            // Remarks
            $remX = $this->GetX();
            $this->Rect($remX, $startY, $wRem, $rowH);
            $this->MultiCell($wRem, 3.8, substr($remarks, 0, 150), 0, 'L');

            $this->SetXY($startX, $startY + $rowH);
        }

        $this->Ln(5);
    }

    /**
     * 6. Authentic AACCUP Two-Column Recommendations Table
     */
    protected function renderTwoColumnRecommendations(): void
    {
        if ($this->GetY() > $this->GetPageHeight() - 75.0) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'B', 9.0);
        $this->Cell($this->contentWidth, 5.5, 'RECOMMENDATIONS TO THE INSTITUTION:', 0, 1, 'L');

        $colW = $this->contentWidth / 2.0;

        // Two-Column Headers
        $this->SetFont('Arial', 'B', 8.0);
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(15, 23, 42);

        $headerH = 8.5;
        $startX = $this->leftMargin;
        $startY = $this->GetY();

        $this->Rect($startX, $startY, $colW, $headerH, 'DF');
        $this->Rect($startX + $colW, $startY, $colW, $headerH, 'DF');

        $this->SetXY($startX, $startY + 0.8);
        $this->MultiCell($colW, 3.4, "MANDATORY\n(to be complied with before the award)", 0, 'C');

        $this->SetXY($startX + $colW, $startY + 0.8);
        $this->MultiCell($colW, 3.4, "ENHANCEMENT\n(may be complied with after the award)", 0, 'C');

        $this->SetXY($startX, $startY + $headerH);

        // Format and parse bullet points or numbered lines
        $mandatoryText = $this->formatRecommendationContent($this->auditArea->mandatory_recommendations, 'mandatory');
        $enhancementText = $this->formatRecommendationContent($this->auditArea->enhancement_recommendations, 'enhancement');

        $this->renderTwoColumnCellRow($colW, $mandatoryText, $enhancementText);
        $this->Ln(5);
    }

    /**
     * Render side-by-side cells with automatic page overflow handling
     */
    protected function renderTwoColumnCellRow(float $colW, string $leftText, string $rightText): void
    {
        $lineH = 4.0;
        $nbL = $this->nbLines($colW - 4.0, $leftText);
        $nbR = $this->nbLines($colW - 4.0, $rightText);
        $totalLines = max(6, max($nbL, $nbR));
        $cellH = ($totalLines * $lineH) + 5.0;

        // Check page break
        if ($this->GetY() + $cellH > $this->GetPageHeight() - $this->bottomMargin) {
            // Split or add page
            if ($this->GetY() > $this->GetPageHeight() - 60.0) {
                $this->AddPage();
            }
        }

        $x = $this->GetX();
        $y = $this->GetY();

        $this->SetFont('Arial', '', 7.8);
        $this->SetDrawColor(15, 23, 42);

        // Left box & text
        $this->Rect($x, $y, $colW, $cellH);
        $this->SetXY($x + 2.0, $y + 2.5);
        $this->MultiCell($colW - 4.0, $lineH, $leftText, 0, 'L');

        // Right box & text
        $this->Rect($x + $colW, $y, $colW, $cellH);
        $this->SetXY($x + $colW + 2.0, $y + 2.5);
        $this->MultiCell($colW - 4.0, $lineH, $rightText, 0, 'L');

        $this->SetXY($x, $y + $cellH);
    }

    /**
     * 7. Evaluator Overall Comments
     */
    protected function renderEvaluatorComments(): void
    {
        if ($this->GetY() > $this->GetPageHeight() - 55.0) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'B', 8.5);
        $this->Cell($this->contentWidth, 5.0, 'EVALUATOR OVERALL OBSERVATIONS & FINDINGS:', 0, 1, 'L');

        $comments = $this->auditArea->evaluator_comments ?: 'The survey area exhibits documented compliance in foundational benchmark metrics. Satisfactory operational governance observed, with recommended mandatory improvements targeted for full Level IV alignment.';

        $this->SetFont('Arial', '', 8.0);
        $this->SetDrawColor(203, 213, 225);
        $this->SetFillColor(248, 250, 252);
        $lineCount = max(3, $this->nbLines($this->contentWidth - 4.0, $comments));
        $h = ($lineCount * 3.8) + 4.0;

        $x = $this->GetX();
        $y = $this->GetY();
        $this->Rect($x, $y, $this->contentWidth, $h, 'DF');
        $this->SetXY($x + 2.0, $y + 2.0);
        $this->MultiCell($this->contentWidth - 4.0, 3.8, $comments, 0, 'L');

        $this->SetXY($x, $y + $h + 6.0);
    }

    /**
     * 8. Official Auditor Signature Block
     */
    protected function renderSignatureBlock(): void
    {
        if ($this->GetY() > $this->GetPageHeight() - 42.0) {
            $this->AddPage();
        }

        $evaluatorName = strtoupper($this->auditArea->evaluator_name ?: ($this->auditArea->auditorUser?->name ?: 'ACCREDITOR NAME'));
        $rank = $this->auditArea->evaluator_academic_rank ?: 'Associate Professor V';
        $institution = $this->auditArea->evaluator_institution ?: $this->schoolName;

        $sigW = 85.0;
        $sigX = $this->leftMargin + $this->contentWidth - $sigW;
        $sigY = $this->GetY() + 4.0;

        $this->SetXY($sigX, $sigY);
        $this->SetFont('Arial', 'I', 8.0);
        $this->Cell($sigW, 4.0, 'Evaluated and Submitted By:', 0, 1, 'L');

        $this->Ln(10.0);
        $this->SetX($sigX);
        $this->SetFont('Arial', 'B', 9.0);
        $this->Cell($sigW, 4.2, $evaluatorName, 0, 1, 'L');

        $this->SetX($sigX);
        $this->SetFont('Arial', '', 7.8);
        $this->Cell($sigW, 3.6, $rank, 0, 1, 'L');

        $this->SetX($sigX);
        $this->Cell($sigW, 3.6, 'Designated Survey Area Auditor / Evaluator', 0, 1, 'L');

        $this->SetX($sigX);
        $this->Cell($sigW, 3.6, $institution, 0, 1, 'L');
    }

    /**
     * Fallback and formatting helper for recommendations
     */
    protected function formatRecommendationContent(?string $text, string $type): string
    {
        if (! empty(trim($text ?? ''))) {
            return trim($text);
        }

        if ($type === 'mandatory') {
            return "1. Formalization and institutional approval of updated operational roadmap aligned with discipline-specific priorities.\n2. Strengthen peer-reviewed faculty and student publications in accredited national/international refereed journals.\n3. Establish verified repository of copyrights, utility models, or software packages generated by the program.\n4. Ensure compliance monitoring of all benchmark parameters before award endorsement.";
        }

        return "1. The program is encouraged to expand external linkages and partner MOA/MOU engagements with industry stakeholders.\n2. Consider continuous benchmarking with leading regional state universities and colleges (SUCs).\n3. Institutionalize predictive analytics dashboard for real-time tracking of student outcomes and graduate employability.";
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
