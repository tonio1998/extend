<?php

namespace App\Services\PDF;

use App\Library\FPDI\src\Fpdi;
use App\Models\SaaS\InstitutionSetting;
use App\Services\DSS\TenantContext;
use App\Services\PDF\SUCs\SnsuDocumentHandler;
use Carbon\Carbon;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

/**
 * Universal Reusable Sovereign Document Template
 *
 * Dynamically resolves institutional branding, logos, letterheads,
 * and header/footer configurations entirely from saved template settings
 * and active tenant metadata. Zero hardcoded school names or static assets.
 */
class InstitutionalDocumentTemplate extends Fpdi
{
    // --- Paper Size Constants ---
    public const PAPER_A4 = 'A4';

    public const PAPER_FOLIO = [215.9, 330.2];

    public const PAPER_8_5X13 = [215.9, 330.2];

    public const PAPER_LONG = [215.9, 330.2];

    // --- Header Modes ---
    public const HEADER_EXECUTIVE = 'executive';

    public const HEADER_ISO_FORM = 'iso_form';

    public const HEADER_NONE = 'none';

    // --- Footer Modes ---
    public const FOOTER_STANDARD = 'standard';

    public const FOOTER_PAGINATION_ONLY = 'pagination_only';

    public const FOOTER_NONE = 'none';

    // --- Core Properties ---
    public ?InstitutionSetting $institution = null;

    public string|array $paperSize = 'A4';

    public string $headerMode = self::HEADER_EXECUTIVE;

    public string $footerMode = self::FOOTER_STANDARD;

    public string $fontFamily = 'Arial';

    public float $leftMargin = 20.0;

    public float $rightMargin = 20.0;

    public float $topMargin = 12.0;

    public float $bottomMargin = 42.0;

    public float $contentWidth = 170.0;

    // --- Executive Header & Tracking Metadata ---
    public string $officeName = 'Office of the University President';

    public ?string $documentDate = null;

    public ?string $effectivityDate = null;

    public string $trackingNumber = '';

    public string $policyNumber = '';

    public ?string $documentId = null;

    public ?string $formattedQr = null;

    public string $trackingCodeType = 'barcode';

    // --- ISO Form / Document Control Box ---
    public ?string $docNo = null;

    public ?string $docEffectiveDate = null;

    public ?string $revNo = null;

    public ?string $docPageNo = null;

    public ?string $docTracking = null;

    // --- Institutional Metadata (Dynamically Populated) ---
    public string $schoolName = '';

    public string $formerName = '';

    public string $campusAddress = '';

    public string $fullAddress = '';

    public string $mobileContact = '';

    public string $websiteAndSocial = '';

    public string $coreValues = '';

    // --- Layout & Logo Placement Configurations ---
    public string $logoLayout = 'centered'; // 'centered', 'split_dual', 'left', 'right', 'iso_box'

    public float $logoSize = 24.0;          // mm

    public float $logoTopMargin = 10.0;     // mm

    public bool $showSecondaryLogo = false;

    public string $dividerStyle = 'single_black'; // 'single_black', 'double_rule', 'theme_accent', 'none'

    public ?string $secondaryLogoPath = null;

    // --- Footer Logo & Placement Configurations ---
    public string $footerLogoPosition = 'left'; // 'left', 'center', 'right', 'split_dual', 'dual_left', 'dual_left_and_right', 'dual_right', 'none'

    public bool $showFooterLogo = true;

    // --- Multi-Slot Dynamic Logos ---
    public ?string $footerLeftSlot1 = 'seal';

    public ?string $footerLeftSlot2 = null;

    public ?string $footerRightSlot1 = null;

    public ?string $footerRightSlot2 = null;

    public ?string $headerLeftSlot1 = 'seal';

    public ?string $headerLeftSlot2 = null;

    public ?string $headerRightSlot1 = null;

    // --- Footer Content Toggles (Admin-Configurable) ---
    public string $barcodePosition = 'above_right'; // above_left|above_center|above_right|footer_left|footer_center|footer_right|none

    public string $qrPosition = 'above_right';      // same options

    public ?string $qrTargetUrl = null;

    public bool $showFooterAddress = true;

    public bool $showFooterContact = true;

    public bool $showFooterWebsite = true;

    public bool $showCoreValues = true;

    public bool $showDistribution = true;

    public string $distributionText = '';

    public bool $showOfficeSubheader = true;

    /**
     * Constructor
     */
    public function __construct(
        string|array $size = 'A4',
        string $orientation = 'P',
        string $unit = 'mm',
        ?InstitutionSetting $institution = null,
        ?string $documentType = null
    ) {
        $resolvedSize = self::resolvePaperSize($size);
        parent::__construct($orientation, $unit, $resolvedSize);

        $this->paperSize = $resolvedSize;
        $this->institution = $institution ?? TenantContext::getInstitution() ?? InstitutionSetting::first();

        // 1. Resolve Institutional Metadata dynamically from Database
        if ($this->institution) {
            $this->schoolName = strtoupper($this->institution->school_name ?: 'INSTITUTIONAL HEADER');
            $this->campusAddress = $this->institution->address ?: '';
            $this->fullAddress = $this->institution->address ?: '';

            $contacts = [];
            if (! empty($this->institution->contact_phone)) {
                $contacts[] = 'tel: '.$this->institution->contact_phone;
            }
            if (! empty($this->institution->contact_email)) {
                $contacts[] = 'email: '.$this->institution->contact_email;
            }
            $this->mobileContact = implode(' | ', $contacts);

            $online = [];
            if (! empty($this->institution->website_url)) {
                $online[] = 'website: '.$this->institution->website_url;
            }
            $this->websiteAndSocial = implode(' | ', $online);

            $this->coreValues = $this->institution->document_settings['Generic (Default)']['core_values']
                ?? ($this->institution->document_settings['Generic']['core_values'] ?? '');
        }

        // 2. Cascade Saved Template Settings for Document Type
        if ($this->institution && ! empty($this->institution->document_settings)) {
            $allSettings = $this->institution->document_settings;
            $generic = $allSettings['Generic (Default)'] ?? ($allSettings['Generic'] ?? []);
            $specific = $documentType ? ($allSettings[$documentType] ?? []) : [];
            $settings = array_merge($generic, $specific);

            if (! empty($settings['logo_layout'])) {
                $this->logoLayout = $settings['logo_layout'];
            }
            if (isset($settings['logo_size'])) {
                $this->logoSize = (float) $settings['logo_size'];
            }
            if (isset($settings['logo_top_margin'])) {
                $this->logoTopMargin = (float) $settings['logo_top_margin'];
            }
            if (isset($settings['show_secondary_logo'])) {
                $this->showSecondaryLogo = (bool) $settings['show_secondary_logo'];
            }
            if (! empty($settings['divider_style'])) {
                $this->dividerStyle = $settings['divider_style'];
            }
            if (! empty($settings['footer_logo_position'])) {
                $this->footerLogoPosition = $settings['footer_logo_position'];
            }
            if (isset($settings['show_footer_logo'])) {
                $this->showFooterLogo = (bool) $settings['show_footer_logo'];
            }
            if (isset($settings['footer_left_slot_1'])) {
                $this->footerLeftSlot1 = $settings['footer_left_slot_1'];
            }
            if (isset($settings['footer_left_slot_2'])) {
                $this->footerLeftSlot2 = $settings['footer_left_slot_2'];
            }
            if (isset($settings['footer_right_slot_1'])) {
                $this->footerRightSlot1 = $settings['footer_right_slot_1'];
            }
            if (isset($settings['footer_right_slot_2'])) {
                $this->footerRightSlot2 = $settings['footer_right_slot_2'];
            }
            if (isset($settings['header_left_slot_1'])) {
                $this->headerLeftSlot1 = $settings['header_left_slot_1'];
            }
            if (isset($settings['header_left_slot_2'])) {
                $this->headerLeftSlot2 = $settings['header_left_slot_2'];
            }
            if (isset($settings['header_right_slot_1'])) {
                $this->headerRightSlot1 = $settings['header_right_slot_1'];
            }
            if (! empty($settings['core_values'])) {
                $this->coreValues = $settings['core_values'];
            }
            if (! empty($settings['font_family'])) {
                $this->fontFamily = $settings['font_family'];
            }
            // Footer stamp positions
            if (! empty($settings['barcode_position'])) {
                $this->barcodePosition = $settings['barcode_position'];
            }
            if (! empty($settings['qr_position'])) {
                $this->qrPosition = $settings['qr_position'];
            }
            if (isset($settings['show_footer_address'])) {
                $this->showFooterAddress = (bool) $settings['show_footer_address'];
            }
            if (isset($settings['show_footer_contact'])) {
                $this->showFooterContact = (bool) $settings['show_footer_contact'];
            }
            if (isset($settings['show_footer_website'])) {
                $this->showFooterWebsite = (bool) $settings['show_footer_website'];
            }
            if (isset($settings['show_core_values'])) {
                $this->showCoreValues = (bool) $settings['show_core_values'];
            }
            if (isset($settings['show_distribution'])) {
                $this->showDistribution = (bool) $settings['show_distribution'];
            }
            if (! empty($settings['distribution_text'])) {
                $this->distributionText = $settings['distribution_text'];
            }
        }

        $this->SetLeftMargin($this->leftMargin);
        $this->SetRightMargin($this->rightMargin);
        $this->SetTopMargin($this->topMargin);
        $this->SetAutoPageBreak(true, $this->bottomMargin);

        $this->contentWidth = $this->GetPageWidth() - ($this->leftMargin + $this->rightMargin);

        // Enable {nb} alias for total page count in all documents
        $this->AliasNbPages();
    }

    /**
     * Resolve string paper size to FPDF format
     */
    public static function resolvePaperSize(string|array $size): string|array
    {
        if (is_array($size)) {
            return $size;
        }

        $normalized = strtolower(str_replace([' ', '-', '_', '.'], '', $size));

        if (in_array($normalized, ['85x13', 'folio', 'long', 'phlegal', 'legalph', 'f4', 'longbond'])) {
            return self::PAPER_FOLIO;
        }

        return self::PAPER_A4;
    }

    /**
     * Check if active institution is Surigao del Norte State University (SNSU)
     */
    public function isSnsu(): bool
    {
        $acronym = strtoupper(trim((string) ($this->institution?->school_acronym ?? '')));
        if ($acronym === 'SNSU') {
            return true;
        }

        $id = (int) ($this->institution?->id ?? 0);
        if ($id === 2) {
            return true;
        }

        $name = strtoupper((string) ($this->institution?->school_name ?? ''));

        return str_contains($name, 'SURIGAO DEL NORTE');
    }

    /**
     * Automatic FPDF Header
     */
    public function Header(): void
    {
        if ($this->headerMode === self::HEADER_NONE) {
            return;
        }

        if ($this->isSnsu()) {
            if ($this->PageNo() === 1) {
                SnsuDocumentHandler::renderHeader($this);
            } else {
                SnsuDocumentHandler::renderContinuationHeader($this);
            }

            return;
        }

        if ($this->PageNo() === 1) {
            if ($this->headerMode === self::HEADER_ISO_FORM) {
                $this->renderIsoFormHeader();
            } else {
                $this->printExecutiveHeader();
            }
        } else {
            $this->printContinuationHeader();
        }
    }

    /**
     * Executive Header with dynamic logo layout and positioning
     */
    public function printExecutiveHeader(?string $office = null, ?string $date = null): void
    {
        $pageW = $this->GetPageWidth();
        $sealPath = $this->getSealPath();
        $sealWidth = $this->logoSize > 0 ? $this->logoSize : 24.0;
        $topMargin = $this->logoTopMargin > 0 ? $this->logoTopMargin : 10.0;
        $layout = $this->logoLayout ?: 'centered';

        if ($layout === 'split_dual') {
            $leftSeal = $this->resolveLogoFile($this->headerLeftSlot1 ?: 'seal') ?: $sealPath;
            if ($leftSeal && file_exists($leftSeal)) {
                $this->Image($leftSeal, $this->leftMargin, $topMargin, $sealWidth, $sealWidth);
            }

            $rightSeal = $this->resolveLogoFile($this->headerRightSlot1 ?: 'secondary') ?: $this->getSecondaryLogoPath();
            if ($rightSeal && file_exists($rightSeal)) {
                $secX = $pageW - $this->rightMargin - $sealWidth;
                $this->Image($rightSeal, $secX, $topMargin, $sealWidth, $sealWidth);
            }

            $this->SetY($topMargin + 1.0);
            $this->SetFont($this->fontFamily, '', 9.5);
            $this->Cell(0, 4.2, 'Republic of the Philippines', 0, 1, 'C');

            $this->SetFont($this->fontFamily, 'B', 12.0);
            $this->Cell(0, 5.0, $this->schoolName, 0, 1, 'C');

            if (! empty($this->formerName)) {
                $this->SetFont($this->fontFamily, 'I', 8.2);
                $this->Cell(0, 3.8, $this->formerName, 0, 1, 'C');
            }

            $this->SetFont($this->fontFamily, '', 8.8);
            $this->Cell(0, 3.8, $this->campusAddress, 0, 1, 'C');

            $minY = $topMargin + $sealWidth + 2.0;
            if ($this->GetY() < $minY) {
                $this->SetY($minY);
            }
        } elseif ($layout === 'dual_left') {
            // Dual Logos Side-by-Side on the Left (magkatabi)
            $seal1 = $this->resolveLogoFile($this->headerLeftSlot1 ?: 'seal');
            $seal2 = $this->resolveLogoFile($this->headerLeftSlot2 ?: 'secondary');

            $xPos = $this->leftMargin;
            if ($seal1 && file_exists($seal1)) {
                $this->Image($seal1, $xPos, $topMargin, $sealWidth, $sealWidth);
                $xPos += $sealWidth + 2.5;
            }
            if ($seal2 && file_exists($seal2)) {
                $this->Image($seal2, $xPos, $topMargin, $sealWidth, $sealWidth);
                $xPos += $sealWidth + 2.5;
            }

            $this->SetY($topMargin + 1.0);
            $textX = $xPos + 2.0;
            $textW = $pageW - $this->rightMargin - $textX;

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->Cell($textW, 4.0, 'Republic of the Philippines', 0, 1, 'L');

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, 'B', 12.0);
            $this->Cell($textW, 5.0, $this->schoolName, 0, 1, 'L');

            if (! empty($this->formerName)) {
                $this->SetX($textX);
                $this->SetFont($this->fontFamily, 'I', 8.0);
                $this->Cell($textW, 3.8, $this->formerName, 0, 1, 'L');
            }

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->Cell($textW, 3.8, $this->campusAddress, 0, 1, 'L');

            $minY = $topMargin + $sealWidth + 2.0;
            if ($this->GetY() < $minY) {
                $this->SetY($minY);
            }
        } elseif ($layout === 'left') {
            // Left-Aligned Seal: Logo on Left, School Details alongside
            if ($sealPath && file_exists($sealPath)) {
                $this->Image($sealPath, $this->leftMargin, $topMargin, $sealWidth, $sealWidth);
            }

            $this->SetY($topMargin + 1.0);
            $textX = $this->leftMargin + $sealWidth + 4.0;
            $textW = $pageW - $this->rightMargin - $textX;

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->Cell($textW, 4.0, 'Republic of the Philippines', 0, 1, 'L');

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, 'B', 12.0);
            $this->Cell($textW, 5.0, $this->schoolName, 0, 1, 'L');

            if (! empty($this->formerName)) {
                $this->SetX($textX);
                $this->SetFont($this->fontFamily, 'I', 8.0);
                $this->Cell($textW, 3.8, $this->formerName, 0, 1, 'L');
            }

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->Cell($textW, 3.8, $this->campusAddress, 0, 1, 'L');

            $minY = $topMargin + $sealWidth + 2.0;
            if ($this->GetY() < $minY) {
                $this->SetY($minY);
            }
        } elseif ($layout === 'right') {
            // Right-Aligned Seal: Logo on Right Margin, School Details on Left
            $sealX = $pageW - $this->rightMargin - $sealWidth;
            if ($sealPath && file_exists($sealPath)) {
                $this->Image($sealPath, $sealX, $topMargin, $sealWidth, $sealWidth);
            }

            $this->SetY($topMargin + 1.0);
            $textX = $this->leftMargin;
            $textW = $sealX - $textX - 4.0;

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->Cell($textW, 4.0, 'Republic of the Philippines', 0, 1, 'L');

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, 'B', 12.0);
            $this->Cell($textW, 5.0, $this->schoolName, 0, 1, 'L');

            if (! empty($this->formerName)) {
                $this->SetX($textX);
                $this->SetFont($this->fontFamily, 'I', 8.0);
                $this->Cell($textW, 3.8, $this->formerName, 0, 1, 'L');
            }

            $this->SetX($textX);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->Cell($textW, 3.8, $this->campusAddress, 0, 1, 'L');

            $minY = $topMargin + $sealWidth + 2.0;
            if ($this->GetY() < $minY) {
                $this->SetY($minY);
            }
        } else {
            // Centered Executive Seal (Default)
            $sealX = ($pageW - $sealWidth) / 2.0;
            if ($sealPath && file_exists($sealPath)) {
                $this->Image($sealPath, $sealX, $topMargin, $sealWidth, $sealWidth);
            }

            $this->SetY($topMargin + $sealWidth + 1.5);

            $this->SetFont($this->fontFamily, '', 9.5);
            $this->Cell(0, 4.2, 'Republic of the Philippines', 0, 1, 'C');

            $this->SetFont($this->fontFamily, 'B', 12.0);
            $this->Cell(0, 5.0, $this->schoolName, 0, 1, 'C');

            if (! empty($this->formerName)) {
                $this->SetFont($this->fontFamily, 'I', 8.2);
                $this->Cell(0, 3.8, $this->formerName, 0, 1, 'C');
            }

            $this->SetFont($this->fontFamily, '', 8.8);
            $this->Cell(0, 3.8, $this->campusAddress, 0, 1, 'C');
        }

        // Draw Divider Line based on dividerStyle
        $divider = $this->dividerStyle ?: 'single_black';
        if ($divider === 'double_rule') {
            $this->Ln(1.5);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.3);
            $this->Line($this->leftMargin, $this->GetY(), $pageW - $this->rightMargin, $this->GetY());
            $this->Ln(0.8);
            $this->SetLineWidth(0.15);
            $this->Line($this->leftMargin, $this->GetY(), $pageW - $this->rightMargin, $this->GetY());
            $this->SetLineWidth(0.2);
        } elseif ($divider === 'theme_accent') {
            $this->Ln(1.8);
            [$r, $g, $b] = $this->getThemePrimaryRgb();
            $this->SetDrawColor($r, $g, $b);
            $this->SetLineWidth(0.6);
            $this->Line($this->leftMargin, $this->GetY(), $pageW - $this->rightMargin, $this->GetY());
            $this->SetLineWidth(0.2);
            $this->SetDrawColor(0, 0, 0);
        } elseif ($divider === 'none') {
            $this->Ln(1.8);
        } else {
            // single_black (default)
            $this->Ln(1.8);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.4);
            $this->Line($this->leftMargin, $this->GetY(), $pageW - $this->rightMargin, $this->GetY());
            $this->SetLineWidth(0.2);
        }

        // Office Sub-Header & Date Strip
        if ($this->showOfficeSubheader) {
            $this->Ln(2.2);
            $resolvedOffice = $office ?: $this->officeName;
            $resolvedDate = $date ?: ($this->documentDate ?: ($this->effectivityDate ?: date('F d, Y')));

            $this->SetFont($this->fontFamily, 'I', 10.0);
            $this->Cell($this->contentWidth * 0.60, 5.0, $resolvedOffice, 0, 0, 'L');

            $this->SetFont($this->fontFamily, '', 10.0);
            $this->Cell($this->contentWidth * 0.40, 5.0, $resolvedDate, 0, 1, 'R');

            $this->Ln(3.5);
        } else {
            $this->Ln(2.0);
        }
    }

    /**
     * ISO / QMS Document Header with Control Box
     */
    public function renderIsoFormHeader(?array $docControl = null, ?string $tracking = null): void
    {
        $pageW = $this->GetPageWidth();
        $sealPath = $this->getSealPath();
        $startY = 10.0;
        $sealW = 23.0;

        if ($sealPath && file_exists($sealPath)) {
            $this->Image($sealPath, $this->leftMargin, $startY, $sealW, $sealW);
        }

        $boxW = 62.0;
        $boxX = $pageW - $this->rightMargin - $boxW;
        $rowH = 4.2;
        $lblW = 20.0;
        $valW = $boxW - $lblW;

        $docNo = $docControl['doc_no'] ?? ($this->docNo ?: 'F-OP-01-01');
        $rawEffective = $docControl['effective_date'] ?? ($this->docEffectiveDate ?: date('Y-m-d'));
        try {
            $effectiveDate = Carbon::parse($rawEffective)->format('M d, Y');
        } catch (\Throwable $e) {
            $effectiveDate = (string) $rawEffective;
        }

        $revNo = $docControl['rev_no'] ?? ($this->revNo ?: '1');
        $pageNo = $docControl['page_no'] ?? ($this->docPageNo ?: $this->PageNo().' of {nb}');

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.25);

        [$fillR, $fillG, $fillB] = $this->getThemePrimaryRgb();
        $this->SetFillColor($fillR, $fillG, $fillB);

        // Box Rows
        $this->SetXY($boxX, $startY);
        $this->SetFont('Arial', 'B', 7.2);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($lblW, $rowH, 'Doc No.:', 1, 0, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($valW, $rowH, $docNo, 1, 1, 'L', false);

        $this->SetX($boxX);
        $this->SetFont('Arial', 'B', 7.2);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($lblW, $rowH, 'Effective Date:', 1, 0, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($valW, $rowH, $effectiveDate, 1, 1, 'L', false);

        $this->SetX($boxX);
        $this->SetFont('Arial', 'B', 7.2);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($lblW, $rowH, 'Rev No.:', 1, 0, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($valW, $rowH, $revNo, 1, 1, 'L', false);

        $this->SetX($boxX);
        $this->SetFont('Arial', 'B', 7.2);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($lblW, $rowH, 'Page No.:', 1, 0, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($valW, $rowH, $pageNo, 1, 1, 'L', false);

        // Center Details
        $centerX = $this->leftMargin + $sealW + 3.0;
        $centerW = $boxX - $centerX - 2.0;

        $this->SetXY($centerX, $startY + 0.5);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($centerW, 3.8, 'Republic of the Philippines', 0, 1, 'C');

        $this->SetX($centerX);
        $this->SetFont('Arial', 'B', 10.5);
        $this->Cell($centerW, 4.4, $this->schoolName, 0, 1, 'C');

        $this->SetX($centerX);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell($centerW, 3.5, $this->campusAddress, 0, 1, 'C');

        if (! empty($this->mobileContact)) {
            $this->SetX($centerX);
            $this->SetFont('Arial', 'I', 7.0);
            $this->Cell($centerW, 3.2, $this->mobileContact, 0, 1, 'C');
        }

        $this->SetY($startY + max($sealW, 17.0) + 2.0);
        $this->SetLineWidth(0.4);
        $this->Line($this->leftMargin, $this->GetY(), $pageW - $this->rightMargin, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->Ln(3.0);
    }

    /**
     * Continuation header on page 2+
     */
    public function printContinuationHeader(): void
    {
        $this->SetY(10.0);
        $this->SetFont($this->fontFamily, 'I', 8.5);
        $this->SetTextColor(100, 116, 139);

        $docTitle = $this->policyNumber ?: $this->schoolName;
        $this->Cell($this->contentWidth * 0.70, 4.5, $docTitle, 0, 0, 'L');
        $this->Cell($this->contentWidth * 0.30, 4.5, 'Page '.$this->PageNo().' of {nb}', 0, 1, 'R');

        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.2);
        $this->Line($this->leftMargin, $this->GetY() + 0.5, $this->GetPageWidth() - $this->rightMargin, $this->GetY() + 0.5);
        $this->SetDrawColor(0, 0, 0);

        $this->SetY(18.0);
    }

    /**
     * Dynamic Official Footer with configurable placement
     */
    public function Footer(): void
    {
        if ($this->footerMode === self::FOOTER_NONE) {
            return;
        }

        if ($this->footerMode === self::FOOTER_PAGINATION_ONLY) {
            $this->renderPaginationFooter();

            return;
        }

        if ($this->isSnsu()) {
            SnsuDocumentHandler::renderFooter($this);

            return;
        }

        $pageW = $this->GetPageWidth();
        $pageH = $this->GetPageHeight();
        $rightLimit = $pageW - $this->rightMargin;
        $footerY = $pageH - 22.0;

        // 1. Tracking Stamp: render barcode and QR in their configured zones
        // Barcode encodes and displays DocumentID
        $barcodeCode = (string) ($this->documentId ?: ($this->trackingNumber ?: ($this->policyNumber ?: 'DTS-VERIFIED')));

        // QR Code encodes and displays the formatted 12-digit QRCODE in DTS
        $rawQr = $this->formattedQr ?: ($this->trackingNumber ?: ($this->documentId ?: '0'));
        $formattedQrText = function_exists('formatQR12Digits')
            ? formatQR12Digits($rawQr)
            : (is_numeric($rawQr) ? str_pad((string) $rawQr, 12, '0', STR_PAD_LEFT) : (string) $rawQr);

        $qrData = $this->qrTargetUrl ?: ($formattedQrText ?: $barcodeCode);

        $bcW = 48.0;
        $bcH = 8.5;
        $qrSize = 15.5;
        $stampGap = 3.5;
        $aboveY = $footerY - 19.5;

        // --- Above-rule rendering (above_left | above_center | above_right) ---
        foreach (['above_left', 'above_center', 'above_right'] as $zone) {
            $hasBc = ($this->barcodePosition === $zone && ! empty($barcodeCode));
            $hasQr = ($this->qrPosition === $zone && ! empty($qrData));

            if (! $hasBc && ! $hasQr) {
                continue;
            }

            if ($hasBc && $hasQr) {
                $totalW = $bcW + $stampGap + $qrSize;
                $startX = match ($zone) {
                    'above_left' => $this->leftMargin,
                    'above_center' => ($pageW - $totalW) / 2.0,
                    'above_right' => $rightLimit - $totalW,
                };
                $this->drawBarcode($barcodeCode, $startX, $aboveY + 6.5, $bcW, $bcH, true);
                $this->drawQrCode($qrData, $startX + $bcW + $stampGap, $aboveY, $qrSize, true, $formattedQrText);
            } elseif ($hasBc) {
                $bcX = match ($zone) {
                    'above_left' => $this->leftMargin,
                    'above_center' => ($pageW - $bcW) / 2.0,
                    'above_right' => $rightLimit - $bcW,
                };
                $this->drawBarcode($barcodeCode, $bcX, $aboveY + 6.5, $bcW, $bcH, true);
            } elseif ($hasQr) {
                $qrX = match ($zone) {
                    'above_left' => $this->leftMargin,
                    'above_center' => ($pageW - $qrSize) / 2.0,
                    'above_right' => $rightLimit - $qrSize,
                };
                $this->drawQrCode($qrData, $qrX, $aboveY, $qrSize, true, $formattedQrText);
            }
        }

        // 2. Horizontal Rule
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.4);
        $this->Line($this->leftMargin, $footerY, $rightLimit, $footerY);
        $this->SetLineWidth(0.2);

        $logoY = $footerY + 1.5;
        $footerPos = $this->footerLogoPosition ?: 'left';
        $showFoot = $this->showFooterLogo;

        $leftLogosWidth = 0.0;
        $rightLogosWidth = 0.0;

        $sealPath = $this->getSealPath();
        $secLogo = $this->getSecondaryLogoPath();

        if ($showFoot && $footerPos !== 'none') {
            if ($footerPos === 'center') {
                // CENTER: Single Quality / University Seal Centered in Footer
                $centerKey = $this->footerLeftSlot1 ?: 'seal';
                $footLogo = $this->resolveLogoFile($centerKey) ?: ($sealPath ?: $secLogo);
                if ($footLogo && file_exists($footLogo)) {
                    $centerLogoW = 12.0;
                    $centerLogoX = ($pageW - $centerLogoW) / 2.0;
                    $this->Image($footLogo, $centerLogoX, $logoY, $centerLogoW, $centerLogoW);
                }
            } else {
                $footLogoSize = 13.5;
                $gap = 2.5;

                // Determine left and right logo keys based on footer position & configured slots
                $leftKeys = [];
                $rightKeys = [];

                if ($footerPos === 'dual_left') {
                    $leftKeys[] = $this->footerLeftSlot1 ?: 'seal';
                    $leftKeys[] = $this->footerLeftSlot2 ?: 'secondary';
                } elseif ($footerPos === 'dual_left_and_right') {
                    $leftKeys[] = $this->footerLeftSlot1 ?: 'seal';
                    $leftKeys[] = $this->footerLeftSlot2 ?: 'secondary';
                    $rightKeys[] = $this->footerRightSlot1 ?: 'iso';
                    if (! empty($this->footerRightSlot2)) {
                        $rightKeys[] = $this->footerRightSlot2;
                    }
                } elseif ($footerPos === 'split_dual') {
                    $leftKeys[] = $this->footerLeftSlot1 ?: 'seal';
                    $rightKeys[] = $this->footerRightSlot1 ?: ($this->footerLeftSlot2 ?: 'secondary');
                } elseif ($footerPos === 'dual_right') {
                    $rightKeys[] = $this->footerRightSlot1 ?: 'iso';
                    $rightKeys[] = $this->footerRightSlot2 ?: 'secondary';
                } elseif ($footerPos === 'left') {
                    $leftKeys[] = $this->footerLeftSlot1 ?: 'seal';
                    if (! empty($this->footerLeftSlot2)) {
                        $leftKeys[] = $this->footerLeftSlot2;
                    }
                } elseif ($footerPos === 'right') {
                    $rightKeys[] = $this->footerRightSlot1 ?: ($this->footerLeftSlot1 ?: 'seal');
                    if (! empty($this->footerRightSlot2)) {
                        $rightKeys[] = $this->footerRightSlot2;
                    }
                }

                // Resolve and filter actual existing files
                $validLeftLogos = [];
                foreach ($leftKeys as $lk) {
                    $resolved = $this->resolveLogoFile($lk);
                    if ($resolved && file_exists($resolved)) {
                        $validLeftLogos[] = $resolved;
                    }
                }

                $validRightLogos = [];
                foreach ($rightKeys as $rk) {
                    $resolved = $this->resolveLogoFile($rk);
                    if ($resolved && file_exists($resolved)) {
                        $validRightLogos[] = $resolved;
                    }
                }

                // Render Left Logos side-by-side (magkatabi)
                if (! empty($validLeftLogos)) {
                    $curX = $this->leftMargin;
                    foreach ($validLeftLogos as $logoPath) {
                        $this->Image($logoPath, $curX, $logoY, $footLogoSize, $footLogoSize);
                        $curX += $footLogoSize + $gap;
                    }
                    $leftLogosWidth = $curX - $this->leftMargin;
                }

                // Render Right Logos side-by-side
                if (! empty($validRightLogos)) {
                    $totalRightW = (count($validRightLogos) * $footLogoSize) + ((count($validRightLogos) - 1) * $gap);
                    $curX = $rightLimit - $totalRightW;
                    foreach ($validRightLogos as $logoPath) {
                        $this->Image($logoPath, $curX, $logoY, $footLogoSize, $footLogoSize);
                        $curX += $footLogoSize + $gap;
                    }
                    $rightLogosWidth = $totalRightW + 2.0;
                }
            }
        }

        // In-footer stamp placements (footer_left | footer_right)
        $footBcW = 36.0;
        $footBcH = 6.5;
        $footQrSize = 13.0;
        $inFootY = $footerY + 1.2;

        $hasFootLeftBc = ($this->barcodePosition === 'footer_left' && ! empty($barcodeCode));
        $hasFootLeftQr = ($this->qrPosition === 'footer_left' && ! empty($qrData));
        if ($hasFootLeftBc || $hasFootLeftQr) {
            $footLeftX = $this->leftMargin + ($leftLogosWidth > 0 ? $leftLogosWidth + 2.0 : 0.0);
            $addedW = 0.0;
            if ($hasFootLeftBc) {
                $this->drawBarcode($barcodeCode, $footLeftX, $inFootY + 0.5, $footBcW, $footBcH, true);
                $footLeftX += $footBcW + 2.5;
                $addedW += $footBcW + 2.5;
            }
            if ($hasFootLeftQr) {
                $this->drawQrCode($qrData, $footLeftX, $inFootY, $footQrSize, true, $formattedQrText);
                $addedW += $footQrSize + 2.5;
            }
            $leftLogosWidth += $addedW;
        }

        $hasFootRightBc = ($this->barcodePosition === 'footer_right' && ! empty($barcodeCode));
        $hasFootRightQr = ($this->qrPosition === 'footer_right' && ! empty($qrData));
        if ($hasFootRightBc || $hasFootRightQr) {
            $neededW = 0.0;
            if ($hasFootRightBc) {
                $neededW += $footBcW + 2.5;
            }
            if ($hasFootRightQr) {
                $neededW += $footQrSize + 2.5;
            }
            $footRightX = $rightLimit - $rightLogosWidth - $neededW;
            if ($hasFootRightBc) {
                $this->drawBarcode($barcodeCode, $footRightX, $inFootY + 0.5, $footBcW, $footBcH, true);
                $footRightX += $footBcW + 2.5;
            }
            if ($hasFootRightQr) {
                $this->drawQrCode($qrData, $footRightX, $inFootY, $footQrSize, true, $formattedQrText);
            }
            $rightLogosWidth += $neededW;
        }

        // 3. CENTER: Institutional Information (each line respects admin toggles & custom settings)
        $centerX = $this->leftMargin + $leftLogosWidth + 2.0;
        $centerW = $rightLimit - $rightLogosWidth - $centerX;

        // In-footer center stamp (footer_center)
        $hasFootCenterBc = ($this->barcodePosition === 'footer_center' && ! empty($barcodeCode));
        $hasFootCenterQr = ($this->qrPosition === 'footer_center' && ! empty($qrData));
        $footerTextY = $footerY + 1.5;

        if ($hasFootCenterBc || $hasFootCenterQr) {
            $totalCenterStampW = 0.0;
            if ($hasFootCenterBc) {
                $totalCenterStampW += $footBcW;
            }
            if ($hasFootCenterQr) {
                $totalCenterStampW += ($totalCenterStampW > 0 ? 3.0 : 0) + $footQrSize;
            }
            $csX = $centerX + ($centerW - $totalCenterStampW) / 2.0;
            if ($hasFootCenterBc) {
                $this->drawBarcode($barcodeCode, $csX, $inFootY + 0.5, $footBcW, $footBcH, false);
                $csX += $footBcW + 3.0;
            }
            if ($hasFootCenterQr) {
                $this->drawQrCode($qrData, $csX, $inFootY, $footQrSize, true, $formattedQrText);
            }
            $footerTextY = $footerY + max($footBcH + 2.5, $footQrSize + 2.5);
        }

        $this->SetFont('Arial', '', 6.2);
        $this->SetTextColor(30, 41, 59);
        $this->SetXY($centerX, $footerTextY);

        if ($this->showFooterAddress && ! empty($this->fullAddress)) {
            $this->Cell($centerW, 2.7, $this->fullAddress, 0, 1, 'C');
        }

        if ($this->showFooterContact && ! empty($this->mobileContact)) {
            $this->SetX($centerX);
            $this->Cell($centerW, 2.7, $this->mobileContact, 0, 1, 'C');
        }

        if ($this->showFooterWebsite && ! empty($this->websiteAndSocial)) {
            $this->SetX($centerX);
            $this->Cell($centerW, 2.7, $this->websiteAndSocial, 0, 1, 'C');
        }

        if ($this->showCoreValues && ! empty($this->coreValues)) {
            $this->SetFont('Arial', 'B', 6.4);
            $this->SetTextColor(0, 0, 0);
            $this->SetX($centerX);
            $this->Cell($centerW, 3.0, $this->coreValues, 0, 1, 'C');
        }

        // 4. Distribution list (admin-configurable text)
        if ($this->showDistribution && ! empty($this->distributionText)) {
            $distY = $this->GetY() + 0.8;
            if ($distY < $pageH - 2.0) {
                $this->SetFont('Arial', '', 5.5);
                $this->SetTextColor(100, 116, 139);
                $this->SetX($this->leftMargin);
                $label = 'Distribution: ';
                $this->SetFont('Arial', 'B', 5.5);
                $this->Cell($this->GetStringWidth($label), 2.5, $label, 0, 0, 'L');
                $this->SetFont('Arial', '', 5.5);
                $this->MultiCell($centerW + $rightLogosWidth + $leftLogosWidth - $this->GetStringWidth($label), 2.5, $this->distributionText, 0, 'L');
            }
        }

        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
    }

    /**
     * Simple Pagination Footer
     */
    public function renderPaginationFooter(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8.0);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 10, 'Page '.$this->PageNo().' of {nb}', 0, 0, 'C');
    }

    /**
     * Helper to set ISO Document Control box values
     */
    public function setDocumentControl(string $docNo, string $effectiveDate, string $revNo, ?string $pageNo = null, ?string $tracking = null): self
    {
        $this->docNo = $docNo;
        $this->docEffectiveDate = $effectiveDate;
        $this->revNo = $revNo;
        $this->docPageNo = $pageNo;
        $this->docTracking = $tracking;
        if (! empty($tracking) && empty($this->trackingNumber)) {
            $this->trackingNumber = $tracking;
        }

        return $this;
    }

    /**
     * Reusable Standalone Barcode Generator
     */
    public function drawBarcode(string $code, float $x, float $y, float $width = 48.0, float $height = 8.5, bool $showText = true): void
    {
        try {
            $barcodeGenerator = new DNS1D;
            $barcodeBase64 = $barcodeGenerator->getBarcodePNG($code, 'C128', 2, 40);

            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $barcodeFile = $tempDir.'/temp_bc_'.uniqid().'.png';
            file_put_contents($barcodeFile, base64_decode($barcodeBase64));

            if (file_exists($barcodeFile)) {
                $this->Image($barcodeFile, $x, $y, $width, $height);
                @unlink($barcodeFile);
            }

            if ($showText) {
                $this->SetXY($x, $y + $height + 0.4);
                $this->SetFont('Arial', 'B', 7.2);
                $this->SetTextColor(15, 23, 42);
                $this->Cell($width, 2.8, $code, 0, 0, 'C');
            }
        } catch (\Throwable $e) {
            // Fail-safe
        }
    }

    /**
     * Reusable Standalone QR Code Generator
     */
    public function drawQrCode(
        string $code,
        float $x,
        float $y,
        float $size = 15.5,
        bool|string $showLabel = true,
        ?string $labelText = null
    ): void {
        try {
            $qrGenerator = new DNS2D;
            $qrBase64 = $qrGenerator->getBarcodePNG($code, 'QRCODE', 4, 4);

            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $qrFile = $tempDir.'/temp_qr_'.uniqid().'.png';
            file_put_contents($qrFile, base64_decode($qrBase64));

            if (file_exists($qrFile)) {
                $this->Image($qrFile, $x, $y, $size, $size);
                @unlink($qrFile);
            }

            if ($showLabel) {
                $displayText = (string) (is_string($showLabel) ? $showLabel : ($labelText ?: $code));
                $fontSize = 6.8;
                $this->SetFont('Arial', 'B', $fontSize);
                $cellW = $size + 4.0;
                while ($this->GetStringWidth($displayText) > $cellW && $fontSize > 5.0) {
                    $fontSize -= 0.2;
                    $this->SetFont('Arial', 'B', $fontSize);
                }
                $this->SetXY($x - 2.0, $y + $size + 0.3);
                $this->SetTextColor(15, 23, 42);
                $this->Cell($cellW, 2.4, $displayText, 0, 0, 'C');
            }
        } catch (\Throwable $e) {
            // Fail-safe
        }
    }

    /**
     * Resolve High-Resolution University Seal
     */
    public function getSealPath(): ?string
    {
        if (! empty($this->institution?->seal_path) && file_exists(public_path($this->institution->seal_path))) {
            return public_path($this->institution->seal_path);
        }

        if (! empty($this->institution?->logo_variants['print_seal']['relative_path']) && file_exists(public_path($this->institution->logo_variants['print_seal']['relative_path']))) {
            return public_path($this->institution->logo_variants['print_seal']['relative_path']);
        }

        if (! empty($this->institution?->logo_variants['master']['relative_path']) && file_exists(public_path($this->institution->logo_variants['master']['relative_path']))) {
            return public_path($this->institution->logo_variants['master']['relative_path']);
        }

        if (! empty($this->institution?->logo_path) && file_exists(public_path($this->institution->logo_path))) {
            return public_path($this->institution->logo_path);
        }

        $tenantId = $this->institution?->tenant_id ?? 1;
        $candidate1 = public_path("storage/tenants/{$tenantId}/branding/print-seal-1024x1024.png");
        if (file_exists($candidate1)) {
            return $candidate1;
        }

        $candidate2 = public_path("storage/tenants/{$tenantId}/branding/master-512x512.png");
        if (file_exists($candidate2)) {
            return $candidate2;
        }

        return null;
    }

    /**
     * Resolve Auxiliary / Secondary Logo
     */
    public function getSecondaryLogoPath(): ?string
    {
        if (! empty($this->secondaryLogoPath) && file_exists($this->secondaryLogoPath)) {
            return $this->secondaryLogoPath;
        }

        $tenantId = $this->institution?->tenant_id ?? 1;
        $candidate1 = public_path("storage/tenants/{$tenantId}/branding/secondary-logo.png");
        if (file_exists($candidate1)) {
            return $candidate1;
        }

        $candidate2 = public_path('images/bagong_pilipinas.png');
        if (file_exists($candidate2)) {
            return $candidate2;
        }

        $candidate3 = public_path('images/bagong-pilipinas-logo.png');
        if (file_exists($candidate3)) {
            return $candidate3;
        }

        return $this->getSealPath();
    }

    /**
     * Resolve ISO / Quality Certification Badge
     */
    public function getIsoLogoPath(): ?string
    {
        $tenantId = $this->institution?->tenant_id ?? 1;
        $candidate1 = public_path("storage/tenants/{$tenantId}/branding/iso-logo.png");
        if (file_exists($candidate1)) {
            return $candidate1;
        }

        $candidate2 = public_path('images/socotec_iso9001.png');
        if (file_exists($candidate2)) {
            return $candidate2;
        }

        return null;
    }

    /**
     * Dynamically resolve any logo key or uploaded file path
     */
    public function resolveLogoFile(?string $keyOrPath): ?string
    {
        if (empty($keyOrPath) || $keyOrPath === 'none') {
            return null;
        }

        if ($keyOrPath === 'seal') {
            return $this->getSealPath();
        }

        if ($keyOrPath === 'secondary') {
            return $this->getSecondaryLogoPath();
        }

        if ($keyOrPath === 'iso') {
            return $this->getIsoLogoPath();
        }

        if ($keyOrPath === 'pab') {
            $pab = public_path('images/pab_accredited.jpg');

            return file_exists($pab) ? $pab : null;
        }

        // Direct public relative path
        if (file_exists(public_path($keyOrPath))) {
            return public_path($keyOrPath);
        }

        if (file_exists($keyOrPath)) {
            return $keyOrPath;
        }

        // Check custom_logos by id in institution->logo_variants
        $customLogos = $this->institution?->logo_variants['custom_logos'] ?? [];
        foreach ($customLogos as $cl) {
            if (($cl['id'] ?? '') === $keyOrPath) {
                if (! empty($cl['relative_path']) && file_exists(public_path($cl['relative_path']))) {
                    return public_path($cl['relative_path']);
                }
            }
        }

        return null;
    }

    /**
     * Dynamic theme primary RGB
     */
    public function getThemePrimaryRgb(): array
    {
        try {
            $theme = TenantContext::getTheme();
            $hex = ltrim($theme?->primary_color ?? '#047857', '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) === 6) {
                return [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2)),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback dark green
        }

        return [4, 120, 87];
    }
}
