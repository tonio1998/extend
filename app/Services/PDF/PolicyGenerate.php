<?php

namespace App\Services\PDF;

use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\PolicySignatory;
use App\Models\SaaS\InstitutionSetting;
use App\Models\SaaS\SignatoryMatrix;
use App\Services\DSS\TenantContext;

class PolicyGenerate extends PolicyTemplate
{
    public InstitutionalPolicy $policy;

    public string $fontFamily = 'Times';

    public float $lineHeight = 6.0;

    public float $paragraphSpacing = 3.0;

    public float $signatureGap = 16.0;

    /** RGB from the institution theme primary color */
    protected array $themeRgb = [15, 23, 42];

    public function __construct(
        InstitutionalPolicy $policy,
        ?InstitutionSetting $institution = null,
        string|array|null $paperSize = null,
        ?string $templateType = null
    ) {
        $institution = $institution ?? TenantContext::getInstitution();
        $resolvedPaper = $paperSize ?: ($policy->paper_size ?: 'A4');
        $resolvedTemplate = $templateType ?: ($policy->template_type ?: 'executive');

        parent::__construct('P', 'mm', $resolvedPaper, $institution);
        $this->policy = $policy;

        // Apply template mode
        $this->headerMode = ($resolvedTemplate === 'iso_form' ? self::HEADER_ISO_FORM : self::HEADER_EXECUTIVE);
        if ($this->headerMode === self::HEADER_ISO_FORM) {
            $effectiveDate = $policy->doc_effective_date
                ? $policy->doc_effective_date->format('M d, Y')
                : ($policy->effectivity_date ? $policy->effectivity_date->format('M d, Y') : date('M d, Y'));

            $this->setDocumentControl(
                docNo: $policy->doc_control_no ?: 'F-POL-001',
                effectiveDate: $effectiveDate,
                revNo: $policy->doc_rev_no ?: '1',
                pageNo: '1 of 1',
                tracking: (string) $policy->tracking_number
            );
        }

        // Resolve theme primary color → RGB for use in signatory section
        $theme = TenantContext::getTheme();
        $hex = ltrim($theme?->primary_color ?? '#0f172a', '#');
        if (strlen($hex) === 6) {
            $this->themeRgb = [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];
        }

        // Use DocumentID for 1D Barcode and 12-digit formatted QR for QR code
        $dts = $policy->dtsRecord;
        $docId = (string) ($dts?->DocumentID ?: ($policy->policy_number ?: $policy->tracking_number));
        $formattedQr = (string) ($dts?->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($policy->tracking_number ?: ($dts?->QRCODE ?: $dts?->id)) : (string) $policy->tracking_number));

        $this->trackingCodeType = 'barcode';
        $this->documentId = $docId;
        $this->formattedQr = $formattedQr;
        $this->trackingNumber = $docId;
        $this->policyNumber = $policy->policy_number ?: $docId;
        $this->qrTargetUrl = $formattedQr;
        $this->effectivityDate = $policy->effectivity_date ? $policy->effectivity_date->format('F d, Y') : date('F d, Y');
        $this->officeName = $policy->approving_authority ? 'Office of the '.$policy->approving_authority : 'Office of the University President';

        $docType = $policy->document_type;
        $docSettings = $institution?->document_settings[$docType]
            ?? ($institution?->document_settings['Generic (Default)']
            ?? ($institution?->document_settings['Generic'] ?? []));

        $this->fontFamily = $docSettings['font_family'] ?? 'Arial';
        $lineHeightVal = (float) ($docSettings['line_height'] ?? 1.5);
        $this->lineHeight = round(4.8 * $lineHeightVal, 1);
        $this->paragraphSpacing = (float) ($docSettings['paragraph_spacing'] ?? 3.0);
        $this->signatureGap = (float) ($docSettings['signature_gap'] ?? 9.0);

        // Apply logo layout & divider styling from tenant settings
        $this->logoLayout = $docSettings['logo_layout'] ?? 'centered';
        $this->logoSize = (float) ($docSettings['logo_size'] ?? 24.0);
        $this->logoTopMargin = (float) ($docSettings['logo_top_margin'] ?? 10.0);
        $this->showSecondaryLogo = (bool) ($docSettings['show_secondary_logo'] ?? false);
        $this->footerLogoPosition = $docSettings['footer_logo_position'] ?? 'left';
        $this->showFooterLogo = (bool) ($docSettings['show_footer_logo'] ?? true);
        if (! empty($docSettings['core_values'])) {
            $this->coreValues = $docSettings['core_values'];
        }
        if (! empty($docSettings['office_name'])) {
            $this->officeName = $docSettings['office_name'];
        }
        if (! empty($docSettings['header_school_name'])) {
            $this->schoolName = strtoupper($docSettings['header_school_name']);
        }
        if (! empty($docSettings['header_address'])) {
            $this->campusAddress = $docSettings['header_address'];
            $this->fullAddress = $docSettings['header_address'];
        }

        $acronym = $this->institution?->school_acronym ?: 'INST';
        $this->SetTitle($acronym.' '.($policy->document_type ?: 'Policy').': '.$this->policyNumber);
        $this->AddPage();
        $this->generateContent();
    }

    /**
     * Generate structured body content conforming to the official template
     */
    private function generateContent(): void
    {
        $w = $this->contentWidth;

        // Optional Form Header Banner for ISO/QMS Form template
        if ($this->headerMode === self::HEADER_ISO_FORM) {
            $this->SetFont($this->fontFamily, 'B', 11.5);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.3);
            $docTitle = strtoupper($this->policy->document_type ?: 'INSTITUTIONAL POLICY');
            $this->Cell(0, 6.5, $docTitle, 1, 1, 'C');
            $this->Ln(3.0);
        }

        // 1. Memorandum Metadata Box
        $margin = 20.0;
        $labelW = 32.0;
        $colonW = 4.0;
        $valX = $margin + $labelW + $colonW;
        $valW = $this->contentWidth - ($labelW + $colonW);
        $lineH = 5.2;
        $rowGap = 2.2;

        // Row 1: REFERENCE NO. (SNSU SUC Standard)
        $y = $this->GetY();
        $this->SetXY($margin, $y);
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->Cell($labelW, $lineH, 'REFERENCE NO.', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 10.5);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $this->policyNumber, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 2: FOR / TO
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'FOR / TO', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', 9.5);
        $toText = 'Vice Presidents, Campus Directors, College Deans, Academic Directors, Department Chairs, Unit Heads, Faculty, Administrative Personnel, and All Concerned';
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $toText, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 3: FROM
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'FROM', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $fromText = strtoupper($this->policy->approving_authority ?: 'The University President');
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, $fromText, 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 4: SUBJECT
        $y = $this->GetY();
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->SetXY($margin, $y);
        $this->Cell($labelW, $lineH, 'SUBJECT', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $lineH, strtoupper($this->policy->title), 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln($rowGap);

        // Row 5: LEGAL BASIS (if present)
        if (! empty($this->policy->legal_basis)) {
            $y = $this->GetY();
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->SetXY($margin, $y);
            $this->Cell($labelW, $lineH, 'LEGAL BASIS', 0, 0, 'L');
            $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, 4.8, $this->policy->legal_basis, 0, 'L');
            $this->SetLeftMargin($margin);
            $this->SetX($margin);
            $this->Ln($rowGap);
        }

        $this->Ln(1.5);
        $this->SetDrawColor(80, 80, 80);
        $this->SetLineWidth(0.25);
        $this->Line(20, $this->GetY(), $this->GetPageWidth() - 20, $this->GetY());
        $this->Ln(3.5);

        $docSettings = $this->institution?->document_settings[$this->policy->document_type] ?? [];

        // 2. Cultural Greeting (configurable per document type)
        $greeting = $docSettings['greeting'] ?? 'Madujow nu aedow! Greetings of peace and academic excellence.';
        $this->SetFont($this->fontFamily, 'I', 10.5);
        $this->Cell(0, 5.5, $greeting, 0, 1, 'L');
        $this->Ln(2.0);

        // 3. Rationale & Policy Statement
        if (! empty($this->policy->rationale)) {
            $this->SetFont($this->fontFamily, '', 10.0);
            $this->WriteParagraph($this->policy->rationale);
        }

        // 4. Scope & Coverage
        if (! empty($this->policy->scope)) {
            $this->SetFont($this->fontFamily, '', 10.0);
            $this->WriteParagraph('This issuance applies to '.$this->policy->scope);
        }

        // 5. Operating Provisions (Structured Articles & Clauses)
        if (! empty($this->policy->provisions) && is_array($this->policy->provisions)) {
            foreach ($this->policy->provisions as $art) {
                if (is_string($art)) {
                    $this->SetFont($this->fontFamily, '', 9.5);
                    $this->WriteParagraph($art);

                    continue;
                }

                $this->Ln(1.5);
                $artPrefix = $art['article'] ?? '';
                $artTitle = $art['title'] ?? '';
                $header = $artPrefix.(! empty($artPrefix) && ! empty($artTitle) ? ': ' : '').$artTitle;
                if (! empty($header)) {
                    $this->SetFont($this->fontFamily, 'B', 10.0);
                    $this->SetX(20);
                    $this->MultiCell($this->contentWidth, 5.0, strtoupper(trim($header, ': ')), 0, 'C');
                    $this->Ln(1.0);
                }

                $this->SetFont($this->fontFamily, '', 9.5);
                $clauses = $art['clauses'] ?? $art['body'] ?? $art['content'] ?? [];
                if (is_array($clauses)) {
                    foreach ($clauses as $clause) {
                        if (is_string($clause)) {
                            $this->WriteParagraph($clause);
                        } elseif (is_array($clause)) {
                            $this->WriteParagraph($clause['body'] ?? $clause['text'] ?? $clause['clause'] ?? json_encode($clause));
                        }
                    }
                } elseif (is_string($clauses) && ! empty($clauses)) {
                    $this->WriteParagraph($clauses);
                }
            }
        }

        // 8. Dynamic multi-column signatory section
        $this->addSignatorySection();
    }

    /**
     * Write paragraph with justified text and first-line indentation
     */
    private function WriteParagraph(string $text): void
    {
        $cleanText = str_replace(["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '—', '–', '•', '’', '‘', '“', '”'], ['-', '-', '-', "'", "'", '"', '"'], $text);
        $indent = '          '; // ~12mm visual indent
        $this->SetFont($this->fontFamily, '', 10.0);
        $this->MultiCell($this->contentWidth, $this->lineHeight, $indent.trim($cleanText), 0, 'J');
        $this->Ln($this->paragraphSpacing);
    }

    /**
     * Dynamic multi-column signatory block.
     * Respects position_column (1 = left, 2 = right) and sort_order row grouping.
     * Falls back to institution-level templates, then legacy single-signatory block.
     */
    private function addSignatorySection(): void
    {
        $tenant = $this->institution?->tenant_id ?? $this->policy->tenant_id ?? 1;
        $docType = $this->policy->document_type;
        $docSettings = $this->institution?->document_settings[$docType] ?? [];
        $numColumns = max(1, (int) ($docSettings['signatory_columns'] ?? 2));

        // Load per-policy signatories
        $signatories = $this->policy->signatories;

        // Fall back to institution templates if none assigned
        if ($signatories->isEmpty()) {
            $signatories = PolicySignatory::where('tenant_id', $tenant)
                ->where('is_template', true)
                ->where(function ($q) use ($docType) {
                    $q->whereNull('document_type')->orWhere('document_type', $docType);
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        // Fall back to legacy single signatory from SignatoryMatrix
        if ($signatories->isEmpty()) {
            $this->addLegacySignatorySection();

            return;
        }

        $maxAllowedY = $this->GetPageHeight() - 75.0;
        $currentY = $this->GetY();
        if ($currentY > $maxAllowedY) {
            $this->AddPage();
            $currentY = $this->GetY();
        }

        $pageW = $this->GetPageWidth();
        $margin = 20.0;
        $usable = $pageW - ($margin * 2);
        $colW = $usable / $numColumns;

        // ── Group signatories into rows by sort_order ──────────────────────
        // Signatories sharing the same sort_order sit side-by-side in one row.
        // position_column (1-based) determines which column slot they occupy.
        $rows = [];
        foreach ($signatories->sortBy('sort_order') as $sig) {
            $rows[$sig->sort_order][] = $sig;
        }
        ksort($rows);

        $rowStartY = $currentY;

        foreach ($rows as $rowSigs) {
            // Page-break guard
            $maxAllowedRowY = $this->GetPageHeight() - 65.0;
            if ($rowStartY > $maxAllowedRowY) {
                $this->AddPage();
                $rowStartY = $this->GetY();
            }

            $maxRowY = $rowStartY;

            foreach ($rowSigs as $sig) {
                // Determine column slot (1-based → 0-based)
                $colIndex = max(0, ((int) ($sig->position_column ?: 1)) - 1);
                $colIndex = min($colIndex, $numColumns - 1);

                $span = (int) ($sig->col_span ?: ($numColumns > 1 ? round(12 / $numColumns) : 12));
                $span = max(1, min($span, 12));

                $itemW = round($span * ($usable / 12.0), 1);
                $itemX = $margin + ($colIndex * $colW);
                $itemY = $rowStartY;

                $alignment = strtolower($sig->alignment ?? 'left');
                $isCenter = ($alignment === 'center');
                $isRight = ($alignment === 'right');
                $alignCell = $isRight ? 'R' : ($isCenter ? 'C' : 'L');

                // 1. Role Label
                $this->SetXY($itemX, $itemY);
                $this->SetFont($this->fontFamily, 'I', 8.5);
                $this->SetTextColor(80, 80, 80);
                $this->Cell($itemW, 4.0, $sig->role_label, 0, 1, $alignCell);

                // 2. Signature gap (no underline)
                $sigGap = $this->signatureGap;
                $nameY = $itemY + 4.0 + $sigGap;

                // 3. Name in formal solid black
                $this->SetXY($itemX, $nameY);
                $this->SetFont('Arial', 'B', 10.0);
                $this->SetTextColor(0, 0, 0);
                $this->Cell($itemW, 4.8, strtoupper($sig->employee_name), 0, 1, $alignCell);

                // 4. Designation
                if (! empty($sig->designation)) {
                    $this->SetX($itemX);
                    $this->SetFont($this->fontFamily, '', 9.0);
                    $this->SetTextColor(51, 65, 85);
                    $this->Cell($itemW, 4.0, $sig->designation, 0, 1, $alignCell);
                }

                // 5. Office
                if (! empty($sig->office)) {
                    $this->SetX($itemX);
                    $this->SetFont($this->fontFamily, '', 8.5);
                    $this->SetTextColor(100, 116, 139);
                    $this->Cell($itemW, 3.8, $sig->office, 0, 1, $alignCell);
                }

                $afterY = $this->GetY() + 4.0;
                if ($afterY > $maxRowY) {
                    $maxRowY = $afterY;
                }
            }

            // Advance past the tallest signatory in this row
            $rowStartY = $maxRowY + 2.0;
        }

        $this->SetY($rowStartY + 2.0);
    }

    /**
     * Legacy fallback: single right-aligned Presidential signatory block
     */
    private function addLegacySignatorySection(): void
    {
        $maxAllowedY = $this->GetPageHeight() - 75.0;
        $currentY = $this->GetY();
        if ($currentY > $maxAllowedY) {
            $this->AddPage();
            $currentY = $this->GetY();
        }

        $boxW = 65.0;
        $boxX = $this->GetPageWidth() - 20.0 - $boxW;
        $this->SetXY($boxX, $currentY);
        $this->SetFont('Arial', '', 10.5);
        $this->Cell($boxW, 5.0, 'Sincerely yours,', 0, 1, 'L');

        $sigPath = public_path('images/presidential_signature.png');
        if (file_exists($sigPath)) {
            $this->Image($sigPath, $boxX - 4, $currentY + 2.5, 46, 15);
        }

        $schoolName = $this->institution?->school_name ?: 'Higher Education Institution';
        $tenantId = $this->institution?->tenant_id ?? $this->policy->tenant_id ?? 1;

        $presName = null;
        $presTitle = 'University President';

        if ($this->policy->approvedByUser?->name) {
            $presName = strtoupper($this->policy->approvedByUser->name);
        } else {
            $signatory = SignatoryMatrix::where('tenant_id', $tenantId)
                ->where('role_title', 'Approved by')
                ->first();
            if ($signatory && ! empty($signatory->employee_name)) {
                $presName = strtoupper($signatory->employee_name);
                if (! empty($signatory->designation)) {
                    $presTitle = $signatory->designation;
                }
            } else {
                $presName = 'AUTHORIZED SIGNATORY OFFICIAL';
            }
        }

        $this->SetY($currentY + 16.0);
        $this->SetX($boxX);
        $this->SetFont('Arial', 'B', 11.0);
        $this->Cell($boxW, 5.0, $presName, 0, 1, 'L');

        $this->SetX($boxX);
        $this->SetFont('Arial', '', 9.5);
        $this->Cell($boxW, 4.2, $presTitle, 0, 1, 'L');

        $this->SetX($boxX);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($boxW, 3.8, $schoolName, 0, 1, 'L');
    }
}
