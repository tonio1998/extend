<?php

namespace App\Services\PDF;

use App\Models\Dts\Records;
use App\Models\SaaS\InstitutionSetting;
use App\Services\DSS\TenantContext;
use Milon\Barcode\DNS2D;

/**
 * ADSSU Official Document Action Slip & Routing Transmittal Generator
 *
 * Implemented using FPDF and extends the official SUC template (AdssuTemplate)
 * Featuring:
 * - ISO 9001:2015 Form Header (Official Seal, University details, ISO Document Control Box)
 * - Official Document Tracking Hero Box (Code 128 1D Barcode & 12-Digit QR Code matrix)
 * - Document Profile Metadata Table Grid (Subject, Type, Origin, Transaction Complexity, Priority)
 * - Standard 6-Checklist Action Requested matrix (For Appropriate Action, Approval, Study, etc.)
 * - Chain of Custody & Document Movement Matrix with audit trail and physical signature rows
 * - Republic Act No. 11032 Ease of Doing Business Zero Red-Tape compliance notice
 * - Official SUC Footer (Tracking Barcode, Bagong Pilipinas, Pangalantaw 2030, SOCOTEC & PAB badges)
 */
class DtsRoutingSlipPdf extends AdssuTemplate
{
    public Records $record;

    public function __construct(
        Records $record,
        ?InstitutionSetting $institution = null,
        string|array $paperSize = 'A4'
    ) {
        $institution = $institution ?? TenantContext::getInstitution();
        parent::__construct($paperSize, 'P', 'mm', $institution);

        $this->record = $record;

        // Configure SUC Official Template Modes
        $this->headerMode = self::HEADER_ISO_FORM;
        $this->footerMode = self::FOOTER_STANDARD;
        $this->trackingCodeType = 'barcode';
        $this->trackingNumber = (string) $this->record->DocumentID;

        $acronym = $this->institution?->school_acronym ?: 'SNSU';
        $effectiveDate = $this->record->created_at ? $this->record->created_at->format('M d, Y') : date('M d, Y');

        // ISO Document Control Box
        $this->setDocumentControl(
            docNo: "FM-{$acronym}-DTS-01",
            effectiveDate: $effectiveDate,
            revNo: '00',
            pageNo: '1 of 1',
            tracking: (string) $this->record->DocumentID
        );

        $this->SetTitle("{$acronym} Document Routing Slip - {$this->record->DocumentID}");
        $this->SetAuthor($this->schoolName);
        $this->SetCreator('SNSU RIE Extension Management System');
        $this->SetSubject('Official Document Action Slip & Routing Transmittal');

        // Set safe auto page break to strictly prevent accidental second page before footer
        $this->SetAutoPageBreak(true, 36.0);

        $this->AddPage();
        $this->renderSlipBody();
    }

    /**
     * Render the complete Document Action Slip body onto the official canvas
     */
    protected function renderSlipBody(): void
    {
        $w = $this->contentWidth; // 170.0mm
        [$r, $g, $b] = $this->getThemePrimaryRgb();

        // ─────────────────────────────────────────────────────────────────
        // 1. OFFICIAL DOCUMENT ACTION SLIP TITLE BANNER
        // ─────────────────────────────────────────────────────────────────
        $bannerY = $this->GetY() + 0.5;
        $this->SetFillColor($r, $g, $b);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10.0);
        $this->SetXY($this->leftMargin, $bannerY);
        $this->Cell($w, 5.6, $this->safeText('Official Document Action Slip & Routing Transmittal'), 0, 1, 'C', true);

        $this->SetFillColor(241, 245, 249);
        $this->SetTextColor(71, 85, 105);
        $this->SetFont('Arial', 'B', 6.8);
        $this->Cell($w, 3.6, $this->safeText('DOCUMENT TRACKING & RECORDS MANAGEMENT SYSTEM (DTS)'), 'B', 1, 'C', true);

        // ─────────────────────────────────────────────────────────────────
        // 2. OFFICIAL TRACKING HERO BOX (1D BARCODE & 2D QR CODE)
        // ─────────────────────────────────────────────────────────────────
        $heroY = $this->GetY() + 1.2;
        $heroH = 24.5;
        $this->SetDrawColor(203, 213, 225);
        $this->SetFillColor(250, 250, 249);
        $this->Rect($this->leftMargin, $heroY, $w, $heroH, 'FD');

        $divX = $this->leftMargin + 134.0;
        $this->Line($divX, $heroY, $divX, $heroY + $heroH);

        // Left Side: 1D Barcode & Metadata
        $this->SetXY($this->leftMargin + 3.0, $heroY + 1.5);
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor($r, $g, $b);
        $this->Cell(80.0, 3.0, $this->safeText('OFFICIAL DOCUMENT TRACKING IDENTIFICATION'), 0, 1, 'L');

        // Draw 1D Barcode (Code 128)
        $this->drawBarcode((string) $this->record->DocumentID, $this->leftMargin + 3.0, $heroY + 5.0, 54.0, 7.8, false);

        // Barcode Readable Label
        $this->SetXY($this->leftMargin + 3.0, $heroY + 13.2);
        $this->SetFont('Courier', 'B', 8.2);
        $this->SetTextColor(15, 23, 42);
        $this->Cell(54.0, 3.0, '* '.$this->record->DocumentID.' *', 0, 0, 'C');

        // Meta Column Next to Barcode
        $metaX = $this->leftMargin + 61.0;
        $formattedQr = $this->record->formatted_qr ?: formatQR12Digits($this->record->QRCODE ?: $this->record->id);

        $this->SetXY($metaX, $heroY + 4.8);
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(21.0, 3.4, '12-Digit QR:', 0, 0, 'L');
        $this->SetFont('Courier', 'B', 8.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell(48.0, 3.4, $formattedQr, 0, 1, 'L');

        $this->SetX($metaX);
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(21.0, 3.4, 'SLA Target:', 0, 0, 'L');
        $this->SetFont('Arial', '', 7.0);
        $this->SetTextColor(15, 23, 42);
        $deadlineText = $this->record->Deadline ? $this->record->Deadline->format('M d, Y') : 'Within 3 Days (RA 11032 Simple)';
        $this->Cell(48.0, 3.4, $this->safeText($deadlineText), 0, 1, 'L');

        $this->SetX($metaX);
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(21.0, 3.4, 'Routing Status:', 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor($r, $g, $b);
        $this->Cell(48.0, 3.4, $this->safeText($this->record->TrailStatus ?: 'Active / In Routing'), 0, 1, 'L');

        $this->SetX($metaX);
        $this->SetFont('Arial', 'B', 7.0);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(21.0, 3.4, 'Doc Nature:', 0, 0, 'L');
        $this->SetFont('Arial', '', 7.0);
        $this->SetTextColor(15, 23, 42);
        $nature = ($this->record->type ?: 'Internal').' - '.($this->record->TransactionType ?: 'Simple');
        $this->Cell(48.0, 3.4, $this->safeText($nature), 0, 1, 'L');

        // Right Side: 2D QR Code Matrix
        $rightW = $w - 134.0;
        $qrSize = 14.0;
        $qrX = $divX + (($rightW - $qrSize) / 2);

        $this->SetXY($divX, $heroY + 1.5);
        $this->SetFont('Arial', 'B', 6.5);
        $this->SetTextColor($r, $g, $b);
        $this->Cell($rightW, 2.8, $this->safeText('SCAN TO TRACK LIVE'), 0, 1, 'C');

        $this->drawQrCode($formattedQr, $qrX, $heroY + 4.5, $qrSize, false);

        $this->SetXY($divX, $heroY + 19.4);
        $this->SetFont('Courier', 'B', 7.5);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($rightW, 3.0, $formattedQr, 0, 1, 'C');

        // ─────────────────────────────────────────────────────────────────
        // 3. DOCUMENT PROFILE METADATA GRID
        // ─────────────────────────────────────────────────────────────────
        $gridY = $heroY + $heroH + 1.2;
        $this->SetXY($this->leftMargin, $gridY);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.25);

        // Row 1: Document Subject
        $subjLblW = 30.0;
        $subjValW = $w - $subjLblW;
        $subjText = $this->safeText((string) $this->record->Description);

        $subjHeight = 7.5;
        if ($this->GetStringWidth($subjText) > ($subjValW - 4.0)) {
            $subjHeight = 10.0;
        }

        $this->SetFillColor(248, 250, 252);
        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($subjLblW, $subjHeight, $this->safeText('DOCUMENT SUBJECT'), 1, 0, 'L', true);

        $this->SetFillColor(241, 245, 249);
        $this->SetTextColor(15, 23, 42);
        $currX = $this->GetX();
        $currY = $this->GetY();
        $this->Rect($currX, $currY, $subjValW, $subjHeight, 'FD');
        $this->SetXY($currX + 2.0, $currY + 1.2);
        $this->SetFont('Arial', 'B', 7.8);
        $this->MultiCell($subjValW - 4.0, 3.6, $subjText, 0, 'L');
        $this->SetXY($this->leftMargin, $currY + $subjHeight);

        // Standard 4-column layout metrics
        $rowH = 5.2;
        $col1W = 30.0;
        $col2W = 55.0;
        $col3W = 35.0;
        $col4W = 50.0;

        // Row 2: Document Type & Date Encoded
        $this->SetFillColor(248, 250, 252);
        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col1W, $rowH, $this->safeText('DOCUMENT TYPE'), 1, 0, 'L', true);

        $this->SetTextColor(15, 23, 42);
        $docTypeVal = $this->safeText($this->record->doc_type ?: 'General Document');
        $this->printFitCell($col2W, $rowH, $docTypeVal, 'B', 7.4);

        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col3W, $rowH, $this->safeText('DATE / TIME ENCODED'), 1, 0, 'L', true);

        $this->SetTextColor(15, 23, 42);
        $encodedDate = $this->record->created_at ? $this->record->created_at->format('M d, Y - h:i A') : date('M d, Y - h:i A');
        $this->printFitCell($col4W, $rowH, $encodedDate, '', 7.2, 1);

        // Row 3: Originating Office & Originator / Encoder
        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col1W, $rowH, $this->safeText('ORIGINATING OFFICE'), 1, 0, 'L', true);

        $this->SetTextColor(15, 23, 42);
        $originName = $this->record->Origin ?: ($this->record->department?->dept_name ?? 'Institutional Office');
        $this->printFitCell($col2W, $rowH, $originName, 'B', 7.2);

        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col3W, $rowH, $this->safeText('ORIGINATOR / ENCODER'), 1, 0, 'L', true);

        $this->SetTextColor(15, 23, 42);
        $encoderName = $this->record->creator?->name ?? 'Authorized University Personnel';
        $this->printFitCell($col4W, $rowH, $encoderName, '', 7.2, 1);

        // Row 4: Transaction Complexity & Priority Urgency
        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col1W, $rowH, $this->safeText('TRANSACTION (ARTA)'), 1, 0, 'L', true);

        $this->SetTextColor(15, 23, 42);
        $transType = ($this->record->TransactionType ?: 'Simple (3 Days)').' Compliant';
        $this->printFitCell($col2W, $rowH, $transType, '', 7.2);

        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col3W, $rowH, $this->safeText('PRIORITY URGENCY'), 1, 0, 'L', true);

        $p = (int) $this->record->Priority;
        if ($p === 3) {
            $this->SetTextColor(185, 28, 28);
            $pText = 'RUSH / IMMEDIATE (Level 3)';
        } elseif ($p === 2) {
            $this->SetTextColor(180, 83, 9);
            $pText = 'URGENT (Level 2)';
        } else {
            $this->SetTextColor(4, 120, 87);
            $pText = 'STANDARD ROUTING (Level 1)';
        }
        $this->printFitCell($col4W, $rowH, $pText, 'B', 7.2, 1);

        // Row 5: Action Requested & Checklist Grid
        $hasRemarks = ! empty($this->record->Remarks);
        $actH = $hasRemarks ? 17.5 : 14.5;

        $this->SetTextColor(51, 65, 85);
        $this->SetFont('Arial', 'B', 7.0);
        $this->Cell($col1W, $actH, $this->safeText('ACTION REQUESTED'), 1, 0, 'L', true);

        $actValX = $this->GetX();
        $actValY = $this->GetY();
        $actValW = $w - $col1W; // 140.0mm
        $this->Rect($actValX, $actValY, $actValW, $actH, 'D');

        // Primary requested action text
        $this->SetXY($actValX + 2.5, $actValY + 1.2);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor($r, $g, $b);
        $actionPrimary = $this->record->forAction ?: 'For Appropriate Action & Disposition';
        $this->Cell($actValW - 5.0, 3.2, $this->safeText('> '.$actionPrimary), 0, 1, 'L');

        // Checkbox status logic
        $currentActLower = strtolower($actionPrimary);
        $chk1 = str_contains($currentActLower, 'appropriate') || str_contains($currentActLower, 'action');
        $chk2 = str_contains($currentActLower, 'approv') || str_contains($currentActLower, 'sign');
        $chk3 = str_contains($currentActLower, 'study') || str_contains($currentActLower, 'comment') || str_contains($currentActLower, 'recommend');
        $chk4 = str_contains($currentActLower, 'info') || str_contains($currentActLower, 'reference') || str_contains($currentActLower, 'not');
        $chk5 = str_contains($currentActLower, 'complian') || str_contains($currentActLower, 'implement') || str_contains($currentActLower, 'follow');
        $chk6 = str_contains($currentActLower, 'file') || str_contains($currentActLower, 'record') || str_contains($currentActLower, 'safe');

        $cbY1 = $actValY + 4.8;
        $cbY2 = $cbY1 + 3.8;
        $cbColW = ($actValW - 4.0) / 3.0; // ~45.3mm

        $this->drawCheckbox($actValX + 2.5, $cbY1, $chk1, 'For Appropriate Action', $cbColW - 5.5);
        $this->drawCheckbox($actValX + 2.5 + $cbColW, $cbY1, $chk2, 'For Approval / Signature', $cbColW - 5.5);
        $this->drawCheckbox($actValX + 2.5 + ($cbColW * 2), $cbY1, $chk3, 'For Study / Recommendation', $cbColW - 5.5);

        $this->drawCheckbox($actValX + 2.5, $cbY2, $chk4, 'For Info / Reference', $cbColW - 5.5);
        $this->drawCheckbox($actValX + 2.5 + $cbColW, $cbY2, $chk5, 'For Immediate Compliance', $cbColW - 5.5);
        $this->drawCheckbox($actValX + 2.5 + ($cbColW * 2), $cbY2, $chk6, 'For Safe Keeping / Filing', $cbColW - 5.5);

        if ($hasRemarks) {
            $this->SetXY($actValX + 2.5, $cbY2 + 4.2);
            $this->SetFont('Arial', 'I', 6.8);
            $this->SetTextColor(71, 85, 105);
            $this->Cell($actValW - 5.0, 3.0, $this->safeText('Remarks: '.$this->record->Remarks), 0, 0, 'L');
        }

        $this->SetXY($this->leftMargin, $actValY + $actH);

        // Matrix Sub-Header Bar (Reflecting automated DTS system timestamps)
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetTextColor(15, 23, 42);
        $this->Cell(85.0, 3.8, $this->safeText('CHAIN OF CUSTODY & DOCUMENT MOVEMENT MATRIX'), 0, 0, 'L');
        $this->SetFont('Arial', 'I', 6.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($w - 85.0, 3.8, $this->safeText('System-generated audit trail & custody verification (Track via QR Code)'), 0, 1, 'R');

        // Column widths strictly matching exact $w = 170.0mm (Streamlined 5-column digital workflow)
        // Col 1: # (7mm)
        // Col 2: Office / Department Station (48mm)
        // Col 3: Date & Time In (25mm)
        // Col 4: Action Taken / Endorsement (55mm)
        // Col 5: Received By (Signature) (35mm)
        $cwIdx = 7.0;
        $cwStation = 48.0;
        $cwTimeIn = 25.0;
        $cwAction = 55.0;
        $cwSign = 35.0;

        // Table Column Header
        $this->SetFillColor(15, 23, 42);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(15, 23, 42);
        $this->SetLineWidth(0.25);
        $this->SetFont('Arial', 'B', 6.8);

        $thH = 5.0;
        $this->Cell($cwIdx, $thH, '#', 1, 0, 'C', true);
        $this->Cell($cwStation, $thH, $this->safeText('Office / Department Station'), 1, 0, 'L', true);
        $this->Cell($cwTimeIn, $thH, $this->safeText('Date & Time Received'), 1, 0, 'C', true);
        $this->Cell($cwAction, $thH, $this->safeText('Action Taken / Endorsement'), 1, 0, 'L', true);
        $this->Cell($cwSign, $thH, $this->safeText('Received By (Signature)'), 1, 1, 'C', true);

        // Matrix Data Rows: 6 rows with generous 18.5mm height to fill page perfectly
        $logs = $this->record->logs ?? collect();
        $maxRows = 6;
        $rowHeight = 18.5;

        for ($i = 0; $i < $maxRows; $i++) {
            $log = $logs->get($i);
            $rowY = $this->GetY();
            $this->SetDrawColor(203, 213, 225);

            // Row cell borders
            $this->Rect($this->leftMargin, $rowY, $cwIdx, $rowHeight);
            $this->Rect($this->leftMargin + $cwIdx, $rowY, $cwStation, $rowHeight);
            $this->Rect($this->leftMargin + $cwIdx + $cwStation, $rowY, $cwTimeIn, $rowHeight);
            $this->Rect($this->leftMargin + $cwIdx + $cwStation + $cwTimeIn, $rowY, $cwAction, $rowHeight);
            $this->Rect($this->leftMargin + $cwIdx + $cwStation + $cwTimeIn + $cwAction, $rowY, $cwSign, $rowHeight);

            if ($log) {
                // ── Col 1: # ──
                $this->SetXY($this->leftMargin, $rowY + 2.5);
                $this->SetFont('Arial', 'B', 7.5);
                $this->SetTextColor(15, 23, 42);
                $this->Cell($cwIdx, 4.0, (string) ($i + 1), 0, 0, 'C');

                // ── Col 2: Office / Department Station (Bounded Multi-Line) ──
                $stX = $this->leftMargin + $cwIdx + 2.0;
                $stW = $cwStation - 4.0; // 44.0mm
                $stationName = $log->location ?: ($log->department?->dept_name ?? ($log->unit?->unit_name ?? 'Station Office'));
                $this->SetTextColor(15, 23, 42);
                $this->printBoundedText($stX, $rowY + 2.2, $stW, $stationName, 'B', 7.2, 3.2, 'L', 2);

                // Status tag below station name
                $this->SetXY($stX, $rowY + 10.5);
                $this->SetFont('Arial', 'I', 6.2);
                $this->SetTextColor(71, 85, 105);
                $this->Cell($stW, 3.0, $this->safeText('Status: '.($log->TrailStatus ?: 'Transited')), 0, 0, 'L');

                // ── Col 3: Date & Time Received (System Automated Timestamp) ──
                $tinX = $this->leftMargin + $cwIdx + $cwStation;
                $dateStr = $log->created_at ? $log->created_at->format('m/d/Y') : '--';
                $timeStr = $log->created_at ? $log->created_at->format('h:i A') : '';

                $this->SetXY($tinX, $rowY + 3.0);
                $this->SetFont('Courier', 'B', 7.2);
                $this->SetTextColor(15, 23, 42);
                $this->Cell($cwTimeIn, 3.4, $dateStr, 0, 1, 'C');

                if ($timeStr) {
                    $this->SetX($tinX);
                    $this->SetFont('Courier', '', 6.8);
                    $this->SetTextColor(71, 85, 105);
                    $this->Cell($cwTimeIn, 3.2, $timeStr, 0, 0, 'C');
                }

                // ── Col 4: Action Taken / Endorsement (Bounded Multi-Line) ──
                $actX = $this->leftMargin + $cwIdx + $cwStation + $cwTimeIn + 2.0;
                $actW = $cwAction - 4.0; // 51.0mm
                $actionText = (string) ($log->forAction ?: 'Transited');
                $this->SetTextColor(15, 23, 42);
                $this->printBoundedText($actX, $rowY + 2.2, $actW, $actionText, 'B', 7.2, 3.2, 'L', 2);

                if ($log->comments) {
                    $this->SetTextColor(71, 85, 105);
                    $this->printBoundedText($actX, $rowY + 9.5, $actW, $log->comments, '', 6.2, 2.8, 'L', 2);
                }

                // ── Col 5: Received By & Signature Line ──
                $sigX = $this->leftMargin + $cwIdx + $cwStation + $cwTimeIn + $cwAction + 2.0;
                $sigW = $cwSign - 4.0; // 31.0mm
                $courierName = $log->cour?->name ?? 'Authorized Staff';
                $this->SetTextColor(15, 23, 42);
                $this->printBoundedText($sigX, $rowY + 2.0, $sigW, $courierName, 'B', 7.0, 3.0, 'C', 2);

                // Dotted signature line positioned with generous signing room
                $this->SetDrawColor(148, 163, 184);
                $this->Line($sigX, $rowY + 13.8, $sigX + $sigW, $rowY + 13.8);
                $this->SetXY($sigX, $rowY + 14.3);
                $this->SetFont('Arial', 'I', 5.5);
                $this->SetTextColor(148, 163, 184);
                $this->Cell($sigW, 2.5, 'Recipient Signature', 0, 0, 'C');
            } else {
                // Blank slot for physical on-site routing
                $this->SetXY($this->leftMargin, $rowY + 3.0);
                $this->SetFont('Arial', 'B', 7.0);
                $this->SetTextColor(148, 163, 184);
                $this->Cell($cwIdx, 4.0, (string) ($i + 1), 0, 0, 'C');

                // Signature line guide in blank slot
                $sigX = $this->leftMargin + $cwIdx + $cwStation + $cwTimeIn + $cwAction + 2.0;
                $sigW = $cwSign - 4.0;
                $this->SetDrawColor(203, 213, 225);
                $this->Line($sigX, $rowY + 13.8, $sigX + $sigW, $rowY + 13.8);
                $this->SetXY($sigX, $rowY + 14.3);
                $this->SetFont('Arial', 'I', 5.5);
                $this->SetTextColor(148, 163, 184);
                $this->Cell($sigW, 2.5, 'Signature over printed name', 0, 0, 'C');
            }

            $this->SetY($rowY + $rowHeight);
        }

        // ─────────────────────────────────────────────────────────────────
        // 5. COMPLIANCE & LEGAL NOTICE BOX (Bottom-aligned, perfectly fitted)
        // ─────────────────────────────────────────────────────────────────
        $noticeY = $this->GetY() + 2.0;
        $noticeH = 8.5;
        $this->SetXY($this->leftMargin, $noticeY);
        $this->SetFillColor(248, 250, 252);
        $this->SetDrawColor(203, 213, 225);
        $this->Rect($this->leftMargin, $noticeY, $w, $noticeH, 'FD');

        $this->SetXY($this->leftMargin + 2.5, $noticeY + 1.2);
        $this->SetFont('Arial', 'B', 6.5);
        $this->SetTextColor(15, 23, 42);
        $notice1 = 'IMPORTANT NOTICE: Official transmittal of '.$this->schoolName.'. Must remain attached to the document folder throughout the routing cycle.';
        $this->Cell($w - 5.0, 2.8, $this->safeText($notice1), 0, 1, 'L');

        $this->SetX($this->leftMargin + 2.5);
        $this->SetFont('Arial', '', 6.0);
        $this->SetTextColor(100, 116, 139);
        $notice2 = 'In compliance with Republic Act No. 11032 (Ease of Doing Business & Efficient Government Service Delivery Act of 2018). Zero Red-Tape Policy enforced.';
        $this->Cell($w - 5.0, 2.5, $this->safeText($notice2), 0, 0, 'L');
    }

    /**
     * Print cell with auto-scaled font size to strictly prevent horizontal overflow
     */
    protected function printFitCell(float $w, float $h, string $text, string $style = '', float $baseSize = 7.2, int $ln = 0): void
    {
        $text = $this->safeText($text);
        $size = $baseSize;
        $this->SetFont('Arial', $style, $size);
        $maxW = $w - 3.0;

        while ($this->GetStringWidth($text) > $maxW && $size > 5.2) {
            $size -= 0.2;
            $this->SetFont('Arial', $style, $size);
        }

        $this->Cell($w, $h, $text, 1, $ln, 'L', false);
    }

    /**
     * Print wrapped text strictly bounded within a width and max lines.
     * Word-wraps cleanly and truncates with ellipsis if exceeding maxLines.
     * Guaranteed never to overflow horizontally into adjacent cells.
     */
    protected function printBoundedText(
        float $x,
        float $y,
        float $w,
        string $text,
        string $style = '',
        float $fontSize = 7.0,
        float $lineHeight = 3.2,
        string $align = 'L',
        int $maxLines = 2
    ): void {
        $text = trim($this->safeText($text));
        if ($text === '') {
            return;
        }

        $this->SetFont('Arial', $style, $fontSize);

        if ($this->GetStringWidth($text) <= $w) {
            $this->SetXY($x, $y);
            $this->Cell($w, $lineHeight, $text, 0, 0, $align);

            return;
        }

        $words = preg_split('/\s+/', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine === '' ? $word : $currentLine.' '.$word;
            if ($this->GetStringWidth($testLine) <= $w) {
                $currentLine = $testLine;
            } else {
                if ($currentLine !== '') {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
                if (count($lines) >= $maxLines) {
                    break;
                }
            }
        }
        if ($currentLine !== '' && count($lines) < $maxLines) {
            $lines[] = $currentLine;
        }

        if (count($lines) > 0) {
            $lastIdx = count($lines) - 1;
            if ($this->GetStringWidth($lines[$lastIdx]) > $w) {
                while ($this->GetStringWidth($lines[$lastIdx].'...') > $w && strlen($lines[$lastIdx]) > 3) {
                    $lines[$lastIdx] = substr($lines[$lastIdx], 0, -1);
                }
                $lines[$lastIdx] .= '...';
            }
        }

        foreach ($lines as $i => $lineStr) {
            $this->SetXY($x, $y + ($i * $lineHeight));
            $this->Cell($w, $lineHeight, $lineStr, 0, 0, $align);
        }
    }

    /**
     * Draw 2D QR Code Matrix
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
            $qrBase64 = $qrGenerator->getBarcodePNG($code, 'QRCODE');

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
            $this->SetDrawColor(203, 213, 225);
            $this->Rect($x, $y, $size, $size);
            $this->SetFont('Arial', '', 6.0);
            $this->SetXY($x, $y + ($size / 2) - 2.0);
            $this->Cell($size, 4.0, 'QR CODE', 0, 0, 'C');
        }
    }

    /**
     * Reusable Standalone Checkbox Renderer
     */
    public function drawCheckbox(float $x, float $y, bool $checked, string $label, float $maxLabelWidth = 42.0): void
    {
        $boxSize = 3.2;
        $this->SetDrawColor(15, 23, 42);
        $this->SetLineWidth(0.25);

        if ($checked) {
            [$r, $g, $b] = $this->getThemePrimaryRgb();
            $this->SetFillColor($r, $g, $b);
            $this->Rect($x, $y, $boxSize, $boxSize, 'FD');
            $this->SetFont('Arial', 'B', 7.0);
            $this->SetTextColor(255, 255, 255);
            $this->SetXY($x, $y - 0.2);
            $this->Cell($boxSize, $boxSize, 'X', 0, 0, 'C');
        } else {
            $this->SetFillColor(255, 255, 255);
            $this->Rect($x, $y, $boxSize, $boxSize, 'FD');
        }

        $this->SetTextColor(15, 23, 42);
        $fontSize = 6.8;
        $labelClean = $this->safeText($label);
        $this->SetFont('Arial', $checked ? 'B' : '', $fontSize);

        while ($this->GetStringWidth($labelClean) > $maxLabelWidth && $fontSize > 5.2) {
            $fontSize -= 0.2;
            $this->SetFont('Arial', $checked ? 'B' : '', $fontSize);
        }

        $this->SetXY($x + $boxSize + 1.8, $y - 0.2);
        $this->Cell($maxLabelWidth, $boxSize, $labelClean, 0, 0, 'L');
    }

    /**
     * Clean and encode strings for reliable FPDF Latin-1 rendering
     */
    protected function safeText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $replacements = [
            "\xE2\x80\x94" => '--',  // em dash
            "\xE2\x80\x93" => '-',   // en dash
            "\xE2\x80\x98" => "'",   // left single quote
            "\xE2\x80\x99" => "'",   // right single quote
            "\xE2\x80\x9C" => '"',   // left double quote
            "\xE2\x80\x9D" => '"',   // right double quote
            "\xE2\x80\xA2" => '*',   // bullet
            "\xE2\x80\xA6" => '...', // ellipsis
            "\xC2\xA0" => ' ',   // non-breaking space
        ];
        $text = strtr($text, $replacements);

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                return $converted;
            }
        }

        return utf8_decode($text);
    }
}
