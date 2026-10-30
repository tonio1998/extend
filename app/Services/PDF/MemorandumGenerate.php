<?php

namespace App\Services\PDF;

use App\Models\Policy\UniversityMemorandum;
use App\Models\SaaS\InstitutionSetting;
use App\Services\DSS\TenantContext;

class MemorandumGenerate extends PolicyTemplate
{
    public UniversityMemorandum $memo;

    public string $fontFamily = 'Arial';

    public float $lineHeight = 5.2;

    public float $paragraphSpacing = 3.0;

    public function __construct(
        UniversityMemorandum $memo,
        ?InstitutionSetting $institution = null,
        string|array|null $paperSize = null,
        ?string $templateType = null
    ) {
        $institution = $institution ?? TenantContext::getInstitution();
        $resolvedPaper = $paperSize ?: ($memo->paper_size ?: 'A4');
        $resolvedTemplate = $templateType ?: ($memo->template_type ?: 'executive');

        parent::__construct('P', 'mm', $resolvedPaper, $institution);
        $this->memo = $memo;

        // Apply template mode
        $this->headerMode = ($resolvedTemplate === 'iso_form' ? self::HEADER_ISO_FORM : self::HEADER_EXECUTIVE);
        if ($this->headerMode === self::HEADER_ISO_FORM) {
            $effectiveDate = $memo->doc_effective_date
                ? $memo->doc_effective_date->format('M d, Y')
                : ($memo->memo_date ? $memo->memo_date->format('M d, Y') : date('M d, Y'));

            $this->setDocumentControl(
                docNo: $memo->doc_control_no ?: 'F-ISO-001',
                effectiveDate: $effectiveDate,
                revNo: ($memo->doc_rev_no !== null && $memo->doc_rev_no !== '') ? $memo->doc_rev_no : '0',
                pageNo: '1 of 1',
                tracking: (string) $memo->tracking_number
            );
        }

        // Use 1D Barcode with tracking number strictly
        $this->trackingCodeType = 'barcode';
        $this->trackingNumber = (string) $memo->tracking_number;
        $this->policyNumber = $memo->memo_number ?: $memo->tracking_number;
        $this->effectivityDate = $memo->memo_date ? $memo->memo_date->format('F d, Y') : date('F d, Y');
        $this->officeName = $memo->signatory_title ? 'Office of the '.$memo->signatory_title : 'Office of the University President';

        $acronym = $this->institution?->school_acronym ?: 'SNSU';
        $this->SetTitle($acronym.' Memorandum: '.$this->policyNumber);
        $this->AddPage();
        $this->generateContent();
    }

    /**
     * Generate structured body content conforming to the official SUC template
     */
    private function generateContent(): void
    {
        $margin = 20.0;
        $labelW = 32.0;
        $colonW = 4.0;
        $valX = $margin + $labelW + $colonW; // 56.0mm
        $valW = $this->contentWidth - ($labelW + $colonW); // 134.0mm
        $lineH = 5.0;
        $rowGap = 2.0;

        // Optional Form Header Banner for ISO/QMS Form template (Image 2 style)
        if ($this->headerMode === self::HEADER_ISO_FORM) {
            $this->SetFont($this->fontFamily, 'B', 11.5);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.3);
            $this->Cell(0, 6.5, 'UNIVERSITY MEMORANDUM', 1, 1, 'C');
            $this->Ln(3.0);
        }

        // Row 1: REFERENCE NO. (SNSU SUC Standard)
        $y = $this->GetY();
        $this->SetXY($margin, $y);
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->Cell($labelW, $lineH, 'REFERENCE NO.', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 10.0);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $this->policyNumber, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 2: FOR / TO
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'FOR / TO', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');

        if ($this->memo->recipient_distribution_mode === 'separate_page') {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $toText = 'SEE ATTACHED LIST';
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, $lineH, $toText, 0, 'L');
            $this->SetLeftMargin($margin);
            $this->SetX($margin);
            $this->Ln($rowGap);
        } elseif ($this->memo->recipient_distribution_mode === 'inline_roster' && ! empty($this->memo->recipient_roster)) {
            $nameW = 85.0;
            $roleW = $valW - $nameW;
            $roster = $this->memo->recipient_roster;

            foreach ($roster as $rec) {
                $rowY = $this->GetY();
                $this->SetXY($valX, $rowY);

                $name = strtoupper(trim($rec['name'] ?: ($rec['office'] ?? '')));
                $role = strtoupper(trim($rec['designation'] ?? ($rec['role'] ?? '')));

                $this->SetFont($this->fontFamily, 'B', 9.0);
                $this->Cell($nameW, $lineH, $name, 0, 0, 'L');

                $this->SetFont($this->fontFamily, 'B', 9.0);
                $this->Cell($roleW, $lineH, $role, 0, 1, 'L');
            }
            $this->Ln($rowGap);
        } else {
            $this->SetFont($this->fontFamily, '', 9.0);
            $toText = $this->memo->recipient_to ?: 'All Concerned';
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, $lineH, $toText, 0, 'L');
            $this->SetLeftMargin($margin);
            $this->SetX($margin);
            $this->Ln($rowGap);
        }

        // Row 3: FROM
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'FROM', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $fromText = strtoupper($this->memo->signatory_from ?: 'The University President');
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $fromText, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 4: SUBJECT
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'SUBJECT', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, strtoupper($this->memo->subject), 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 5: DATE
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'DATE', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', 9.5);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $this->effectivityDate, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 6: LEGAL BASIS (if present)
        if (! empty($this->memo->legal_basis)) {
            $y = $this->GetY();
            $this->SetFont($this->fontFamily, 'B', 9.0);
            $this->SetXY($margin, $y);
            $this->Cell($labelW, $lineH, 'LEGAL BASIS', 0, 0, 'L');
            $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, 4.5, $this->memo->legal_basis, 0, 'L');
            $this->SetLeftMargin($margin);
            $this->SetX($margin);
            $this->Ln($rowGap);
        }

        // Dividing Line
        $this->Ln(1.5);
        $this->SetDrawColor(80, 80, 80);
        $this->SetLineWidth(0.25);
        $this->Line(20, $this->GetY(), $this->GetPageWidth() - 20, $this->GetY());
        $this->Ln(3.5);

        // Cultural / Formal SUC Greeting
        $this->SetFont($this->fontFamily, 'I', 9.5);
        $this->Cell(0, 5.0, 'Madujow nu aedow! Greetings of peace and committed public service.', 0, 1, 'L');
        $this->Ln(2.0);

        // Main Body Preamble / Background
        if (! empty($this->memo->body_content)) {
            $this->WriteParagraph($this->memo->body_content);
        }

        // Directives & Procedural Instructions
        if (! empty($this->memo->directives) && is_array($this->memo->directives)) {
            foreach ($this->memo->directives as $dir) {
                if (is_string($dir)) {
                    $this->WriteParagraph($dir);

                    continue;
                }

                $title = trim($dir['title'] ?? '');
                $body = trim($dir['body'] ?? '');

                if (! empty($title)) {
                    $this->SetFont($this->fontFamily, 'B', 9.5);
                    $this->MultiCell($this->contentWidth, $this->lineHeight, $title, 0, 'L');
                    $this->Ln(1.0);
                }

                if (! empty($body)) {
                    $this->WriteParagraph($body);
                }
            }
        }

        // Dynamic Content Data Table / Schedule
        $this->renderContentTable();

        // Closing mandate
        $this->Ln(1.0);
        $this->WriteParagraph('For strict and immediate compliance.');

        // Signatory Block
        $this->renderSignatory();

        // Annex "A" Recipient Distribution Sheet (Dedicated Next Page)
        $this->renderAnnexPage();
    }

    /**
     * Render dynamic content data table with official SUC borders and formatting
     */
    private function renderContentTable(): void
    {
        $table = $this->memo->content_table;
        if (empty($table) || empty($table['headers']) || empty($table['rows'])) {
            return;
        }

        $headers = $table['headers'];
        $rows = $table['rows'];
        $numCols = count($headers);
        if ($numCols === 0) {
            return;
        }

        // Check page-break before table
        if ($this->GetY() > 220) {
            $this->AddPage();
        }

        $this->Ln(2.0);

        // Optional Table Title / Caption
        if (! empty($table['title'])) {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->Cell($this->contentWidth, 5.5, strtoupper($table['title']), 0, 1, 'L');
            $this->Ln(1.0);
        }

        // Calculate column widths to fit exactly within printable width (170mm)
        $totalWidth = $this->contentWidth; // 170mm
        $colWidths = $this->calculateColumnWidths($headers, $totalWidth);

        // Header Row
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(30, 41, 59);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fontFamily, 'B', 8.5);

        foreach ($headers as $idx => $header) {
            $w = $colWidths[$idx];
            $this->Cell($w, 6.5, $header, 1, 0, 'L', true);
        }
        $this->Ln();

        // Data Rows
        $this->SetFont($this->fontFamily, '', 8.0);
        $this->SetDrawColor(100, 116, 139);
        $this->SetLineWidth(0.2);

        foreach ($rows as $row) {
            $this->SetFont($this->fontFamily, '', 8.0);
            $maxLines = 1;
            $cleanRow = [];
            foreach ($headers as $colIdx => $hdr) {
                $cellVal = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], trim((string) ($row[$colIdx] ?? '')));
                $cleanRow[$colIdx] = $cellVal;
                $w = $colWidths[$colIdx];
                $lines = $this->calculateCellLines($cellVal, $w);
                if ($lines > $maxLines) {
                    $maxLines = $lines;
                }
            }

            $lineH = 3.6;
            $rowH = max(6.5, ($maxLines * $lineH) + 2.4);

            if ($this->GetY() + $rowH > 248) {
                $this->AddPage();
                // Repeat header on next page
                $this->SetFillColor(241, 245, 249);
                $this->SetDrawColor(30, 41, 59);
                $this->SetLineWidth(0.3);
                $this->SetFont($this->fontFamily, 'B', 8.5);
                foreach ($headers as $idx => $header) {
                    $this->Cell($colWidths[$idx], 6.5, $header, 1, 0, 'L', true);
                }
                $this->Ln();
                $this->SetFont($this->fontFamily, '', 8.0);
                $this->SetDrawColor(100, 116, 139);
                $this->SetLineWidth(0.2);
            }

            $y = $this->GetY();
            $x = 20.0;

            // Draw bounding rectangles for each column
            foreach ($colWidths as $w) {
                $this->Rect($x, $y, $w, $rowH);
                $x += $w;
            }

            // Print cell content with wrapping
            $curX = 20.0;
            foreach ($headers as $colIdx => $hdr) {
                $cellVal = $cleanRow[$colIdx];
                $w = $colWidths[$colIdx];
                $this->SetXY($curX + 1.0, $y + 1.2);
                $this->MultiCell($w - 2.0, $lineH, $cellVal, 0, 'L');
                $curX += $w;
            }

            $this->SetY($y + $rowH);
        }

        $this->Ln(3.0);
    }

    /**
     * Helper to compute balanced column widths
     */
    private function calculateColumnWidths(array $headers, float $totalWidth): array
    {
        $count = count($headers);
        if ($count === 2) {
            return [60.0, 110.0];
        } elseif ($count === 3) {
            return [45.0, 65.0, 60.0];
        } elseif ($count === 4) {
            return [45.0, 40.0, 45.0, 40.0];
        } elseif ($count === 5) {
            return [35.0, 35.0, 35.0, 35.0, 30.0];
        }

        // Even distribution fallback
        $w = round($totalWidth / $count, 1);
        $widths = array_fill(0, $count, $w);
        $widths[$count - 1] = $totalWidth - ($w * ($count - 1));

        return $widths;
    }

    /**
     * Render official signatory block
     */
    private function renderSignatory(): void
    {
        if ($this->GetY() > 225) {
            $this->AddPage();
        }

        $this->Ln(10.0);

        // Signatory box placed on the right side
        $boxW = 75.0;
        $boxX = $this->GetPageWidth() - 20.0 - $boxW; // right-aligned

        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->Cell($boxW, 4.5, strtoupper($this->memo->signatory_from ?: 'DR. JOY C. CAPISTRANO'), 0, 1, 'C');

        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, '', 8.5);
        $this->SetTextColor(71, 85, 105);
        $this->Cell($boxW, 4.0, $this->memo->signatory_title ?: 'University President', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    /**
     * Render Annex "A" Recipient Distribution Sheet on a dedicated next page
     */
    private function renderAnnexPage(): void
    {
        if ($this->memo->recipient_distribution_mode !== 'separate_page' || empty($this->memo->recipient_roster)) {
            return;
        }

        // Start fresh page for Annex A
        $this->AddPage();

        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell(0, 5.5, 'ANNEX "A" - MEMORANDUM DISTRIBUTION ROSTER', 0, 1, 'C');

        $this->SetFont($this->fontFamily, 'I', 8.5);
        $this->SetTextColor(51, 65, 85);
        $cleanSubject = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], $this->memo->subject);
        $cleanPolicy = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], $this->policyNumber);
        $refText = 'Relative to: '.$cleanPolicy.' - '.$cleanSubject;
        $this->SetX(20);
        $this->MultiCell($this->contentWidth, 4.2, $refText, 0, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->Ln(2.0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->Line(20, $this->GetY(), $this->GetPageWidth() - 20, $this->GetY());
        $this->Ln(3.0);

        $this->SetFont($this->fontFamily, '', 8.5);
        $intro = 'The designated officials and unit heads listed hereunder are the official recipients of this University Memorandum. Receiving personnel or liaison officers shall acknowledge physical receipt by signing on the acknowledgment column below:';
        $this->MultiCell($this->contentWidth, 4.2, $intro, 0, 'J');
        $this->Ln(3.0);

        // Distribution Table Columns (Total = 170mm)
        // # (8mm), Office (54mm), Official Name (48mm), Campus (22mm), Signature (23mm), Date Received (15mm)
        $colWidths = [8.0, 54.0, 48.0, 22.0, 23.0, 15.0];
        $headers = ['#', 'Recipient Office / College', 'Designated Official / Name', 'Campus', 'Signature', 'Date Rec.'];

        $this->printRosterHeader($colWidths, $headers);

        $this->SetFont($this->fontFamily, '', 8.0);
        $this->SetDrawColor(100, 116, 139);
        $this->SetLineWidth(0.2);

        foreach ($this->memo->recipient_roster as $idx => $rec) {
            $officeName = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], trim($rec['office'] ?? ''));
            $officialName = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], trim($rec['name'] ?? ''));
            if (! empty($rec['designation'])) {
                $officialName .= "\n".str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], trim($rec['designation']));
            }
            $campusName = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], trim($rec['campus'] ?? 'Main Campus'));

            $this->SetFont($this->fontFamily, '', 8.0);
            $officeLines = $this->calculateCellLines($officeName, $colWidths[1]);
            $nameLines = $this->calculateCellLines($officialName, $colWidths[2]);
            $campusLines = $this->calculateCellLines($campusName, $colWidths[3]);

            $maxLines = max(1, $officeLines, $nameLines, $campusLines);
            $lineH = 3.6;
            $rowH = max(7.5, ($maxLines * $lineH) + 2.4);

            if ($this->GetY() + $rowH > 250) {
                $this->AddPage();
                $this->printRosterHeader($colWidths, $headers);
                $this->SetFont($this->fontFamily, '', 8.0);
                $this->SetDrawColor(100, 116, 139);
                $this->SetLineWidth(0.2);
            }

            $y = $this->GetY();
            $x = 20.0;

            // Draw bounding rectangles for each column
            foreach ($colWidths as $w) {
                $this->Rect($x, $y, $w, $rowH);
                $x += $w;
            }

            // Col 0: # (centered vertically)
            $this->SetXY(20.0, $y + (($rowH - 4.0) / 2));
            $this->Cell($colWidths[0], 4.0, (string) ($idx + 1), 0, 0, 'C');

            // Col 1: Recipient Office / College (MultiCell with wrapping)
            $this->SetXY(20.0 + $colWidths[0] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[1] - 2.0, $lineH, $officeName, 0, 'L');

            // Col 2: Designated Official / Name (MultiCell with wrapping)
            $this->SetXY(20.0 + $colWidths[0] + $colWidths[1] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[2] - 2.0, $lineH, $officialName, 0, 'L');

            // Col 3: Campus (MultiCell with wrapping)
            $this->SetXY(20.0 + $colWidths[0] + $colWidths[1] + $colWidths[2] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[3] - 2.0, $lineH, $campusName, 0, 'L');

            $this->SetY($y + $rowH);
        }

        $this->Ln(3.5);
        $this->SetFont($this->fontFamily, 'I', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 3.5, '* All receiving offices shall return a signed/stamped copy to the Records Management Office within 24 hours of receipt.', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    private function printRosterHeader(array $colWidths, array $headers): void
    {
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(30, 41, 59);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fontFamily, 'B', 8.0);

        foreach ($headers as $i => $h) {
            $align = in_array($i, [0, 4, 5]) ? 'C' : 'L';
            $this->Cell($colWidths[$i], 6.0, $h, 1, 0, $align, true);
        }
        $this->Ln();
    }

    /**
     * Write paragraph with justified text and first-line indentation
     */
    private function WriteParagraph(string $text): void
    {
        $indent = '          '; // visual indent
        $this->SetFont($this->fontFamily, '', 9.5);
        $this->MultiCell($this->contentWidth, $this->lineHeight, $indent.trim($text), 0, 'J');
        $this->Ln($this->paragraphSpacing);
    }

    /**
     * Calculate how many lines of text will be needed given a specific width and current font
     */
    private function calculateCellLines(string $text, float $cellWidth): int
    {
        $text = trim($text);
        if ($text === '') {
            return 1;
        }

        $usableWidth = max(5.0, $cellWidth - 2.4); // account for left/right inner cell padding
        $paragraphs = explode("\n", str_replace(["\r\n", "\r"], "\n", $text));
        $totalLines = 0;

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                $totalLines++;

                continue;
            }

            $words = preg_split('/\s+/', $para);
            if (empty($words)) {
                $totalLines++;

                continue;
            }

            $currentLine = '';
            $paraLines = 1;

            foreach ($words as $word) {
                $wordWidth = $this->GetStringWidth($word);

                // If a single word exceeds usable width, account for forced line wrap
                if ($wordWidth > $usableWidth) {
                    if ($currentLine !== '') {
                        $paraLines++;
                        $currentLine = '';
                    }
                    $paraLines += max(1, (int) ceil($wordWidth / $usableWidth)) - 1;

                    continue;
                }

                $testLine = $currentLine === '' ? $word : $currentLine.' '.$word;
                if ($this->GetStringWidth($testLine) <= $usableWidth) {
                    $currentLine = $testLine;
                } else {
                    $paraLines++;
                    $currentLine = $word;
                }
            }

            $totalLines += $paraLines;
        }

        return max(1, $totalLines);
    }
}
