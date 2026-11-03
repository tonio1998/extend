<?php

namespace App\Services\PDF;

use App\Models\Dts\Records;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\PolicySignatory;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionAgenda;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Models\SaaS\InstitutionSetting;
use App\Models\SaaS\SignatoryMatrix;
use App\Services\DSS\TenantContext;
use App\Services\DTS\DtsRoutingService;
use App\Services\RIE\ExtensionSignatoryResolver;
use App\Services\Signatories\CentralizedSignatoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Centralized Institutional Document PDF Generator
 *
 * Single FPDF class that handles ALL document types — policies, memorandums, and
 * DTS routing slips — through one static factory entry point.
 *
 * All header/footer branding, stamp positions, logo slots, core values,
 * address lines, etc. are applied automatically from the Template Studio settings
 * stored in InstitutionSetting::document_settings.
 *
 * Usage:
 *   $pdf = DocumentPdfGenerator::fromPolicy($policy);
 *   $pdf = DocumentPdfGenerator::fromMemo($memo);
 *   $pdf = DocumentPdfGenerator::fromDts($record);
 *   $pdf = DocumentPdfGenerator::fromExtensionPlan($plan);
 *
 *   return response($pdf->Output('S', 'doc.pdf'), 200, [...]);
 */
class DocumentPdfGenerator extends InstitutionalDocumentTemplate
{
    // ──────────────────────────────────────────────────────────────────────────
    // Resolved document payloads (only one will be set at a time)
    // ──────────────────────────────────────────────────────────────────────────

    private ?InstitutionalPolicy $policy = null;

    private ?UniversityMemorandum $memo = null;

    private ?Records $dtsRecord = null;

    private ?ExtensionPlan $extensionPlan = null;

    private ?ExtensionProposal $extensionProposal = null;

    private ?ActivityDesign $activityDesign = null;

    /** @var string 'policy' | 'memo' | 'dts' | 'extension_plan' | 'extension_proposal' | 'activity_design' */
    private string $docKind = 'policy';

    /** RGB tuple from the institution theme */
    private array $themeRgb = [15, 23, 42];

    protected float $lineHeight = 7.2;

    protected float $paragraphSpacing = 3.0;

    // ──────────────────────────────────────────────────────────────────────────
    // Static Factories
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Build a PDF for an InstitutionalPolicy.
     */
    public static function fromPolicy(
        InstitutionalPolicy $policy,
        ?InstitutionSetting $institution = null,
        string|array|null $paper = null,
        ?string $template = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();
        $paper = $paper ?: ($policy->paper_size ?: 'A4');
        $template = $template ?: ($policy->template_type ?: 'executive');

        // Auto-register with DTS if available
        if (class_exists(DtsRoutingService::class) && Schema::hasTable('dts_records')) {
            DtsRoutingService::registerPolicy($policy, Auth::id());
            $policy->refresh()->load('dtsRecord');
        }

        $dts = $policy->dtsRecord;
        $docId = (string) ($dts?->DocumentID ?: ($policy->policy_number ?: $policy->tracking_number));
        $formattedQr = (string) ($dts?->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($policy->tracking_number ?: ($dts?->QRCODE ?: $dts?->id)) : (string) $policy->tracking_number));

        $gen = new static($paper, 'P', 'mm', $institution, $policy->document_type);
        $gen->docKind = 'policy';
        $gen->policy = $policy;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $formattedQr;
        $gen->trackingNumber = $docId;
        $gen->policyNumber = $policy->policy_number ?: $docId;
        $gen->effectivityDate = $policy->effectivity_date ? $policy->effectivity_date->format('F d, Y') : date('F d, Y');
        $gen->officeName = $policy->approving_authority ? 'Office of the '.$policy->approving_authority : 'Office of the University President';
        $gen->qrTargetUrl = $formattedQr;

        $gen->applyThemeColor();
        $gen->applyDocSettings($policy->document_type);
        $gen->applyHeaderMode($template, $policy);

        $acronym = $institution?->school_acronym ?: 'INST';
        $gen->SetTitle($acronym.' '.($policy->document_type ?: 'Policy').': '.$gen->policyNumber);
        $gen->AddPage();
        $gen->renderPolicyBody();

        return $gen;
    }

    /**
     * Build a PDF for a UniversityMemorandum.
     */
    public static function fromMemo(
        UniversityMemorandum $memo,
        ?InstitutionSetting $institution = null,
        string|array|null $paper = null,
        ?string $template = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();
        $paper = $paper ?: ($memo->paper_size ?: 'A4');
        $template = $template ?: ($memo->template_type ?: 'executive');
        $docType = $memo->document_type ?? 'University Memorandum';

        // Auto-register with DTS if available
        if (class_exists(DtsRoutingService::class) && Schema::hasTable('dts_records')) {
            DtsRoutingService::registerMemorandum($memo, Auth::id());
            $memo->refresh()->load('dtsRecord');
        }

        $dts = $memo->dtsRecord;
        $docId = (string) ($dts?->DocumentID ?: ($memo->memo_number ?: $memo->tracking_number));
        $formattedQr = (string) ($dts?->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($memo->tracking_number ?: ($dts?->QRCODE ?: $dts?->id)) : (string) $memo->tracking_number));

        $gen = new static($paper, 'P', 'mm', $institution, $docType);
        $gen->docKind = 'memo';
        $gen->memo = $memo;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $formattedQr;
        $gen->trackingNumber = $docId;
        $gen->policyNumber = $memo->memo_number ?: $docId;
        $gen->effectivityDate = $memo->memo_date ? $memo->memo_date->format('F d, Y') : date('F d, Y');
        $gen->officeName = $memo->signatory_title ? 'Office of the '.$memo->signatory_title : 'Office of the University President';
        $gen->qrTargetUrl = $formattedQr;

        $gen->applyThemeColor();
        $gen->applyDocSettings($docType);
        $gen->applyHeaderMode($template, $memo);

        $acronym = $institution?->school_acronym ?: 'INST';
        $gen->SetTitle($acronym.' Memorandum: '.$gen->policyNumber);
        $gen->AddPage();
        $gen->renderMemoBody();

        return $gen;
    }

    /**
     * Build a PDF for a DTS Record (routing slip).
     */
    public static function fromDts(
        Records $record,
        ?InstitutionSetting $institution = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();

        $docId = (string) $record->DocumentID;
        $formattedQr = (string) ($record->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($record->QRCODE ?: $record->id) : (string) $record->QRCODE));

        $gen = new static('A4', 'P', 'mm', $institution, 'Generic (Default)');
        $gen->docKind = 'dts';
        $gen->dtsRecord = $record;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $formattedQr;
        $gen->trackingNumber = $docId;
        $gen->qrTargetUrl = $formattedQr;
        $gen->applyThemeColor();
        $gen->applyDocSettings('Generic (Default)');

        $gen->headerMode = static::HEADER_ISO_FORM;
        $gen->footerMode = static::FOOTER_STANDARD;

        $acronym = $institution?->school_acronym ?: 'INST';
        $effectiveDate = $record->created_at ? $record->created_at->format('M d, Y') : date('M d, Y');
        $gen->setDocumentControl(
            docNo: "FM-{$acronym}-DTS-01",
            effectiveDate: $effectiveDate,
            revNo: '00',
            pageNo: '1 of 1',
            tracking: $docId,
        );

        $gen->SetTitle("{$acronym} Document Routing Slip - {$record->DocumentID}");
        $gen->AddPage();
        $gen->renderDtsBody();

        return $gen;
    }

    /**
     * Build a PDF for an ExtensionPlan.
     */
    public static function fromExtensionPlan(
        ExtensionPlan $plan,
        ?InstitutionSetting $institution = null,
        string|array|null $paper = null,
        ?string $template = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();
        $paper = $paper ?: 'A4';
        $template = $template ?: 'executive';

        if (Schema::hasTable('dts_records') && ! $plan->relationLoaded('dtsRecord')) {
            $plan->load('dtsRecord.routes');
        }

        $dts = $plan->dtsRecord;
        $trackingNum = (string) ($plan->tracking_number ?: '');
        $docId = $trackingNum !== '' ? $trackingNum : (string) ($dts?->DocumentID ?: $plan->plan_code);
        $formattedQr = $trackingNum !== '' ? $trackingNum : (string) ($dts?->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($dts?->QRCODE ?: $dts?->id) : (string) $plan->plan_code));

        $gen = new static($paper, 'P', 'mm', $institution, 'Extension Plan');
        $gen->docKind = 'extension_plan';
        $gen->extensionPlan = $plan;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $formattedQr;
        $gen->trackingNumber = $docId;
        $gen->policyNumber = $plan->plan_code;
        $gen->effectivityDate = $plan->created_at ? $plan->created_at->format('F d, Y') : date('F d, Y');
        $gen->officeName = 'Extension Services / Office of RIE';
        $gen->showOfficeSubheader = false;
        $gen->qrTargetUrl = $formattedQr;

        $gen->applyThemeColor();
        $gen->applyDocSettings('Extension Plan');
        $gen->applyHeaderMode($template, $plan);

        $acronym = $institution?->school_acronym ?: 'INST';
        $gen->SetTitle($acronym.' Extension Plan: '.$gen->policyNumber);
        $gen->AddPage();
        $gen->renderExtensionPlanBody();

        return $gen;
    }

    /**
     * Factory: Generate PDF for an Extension Proposal (7-Page SNSU Official Format).
     */
    public static function fromExtensionProposal(
        ExtensionProposal $proposal,
        ?InstitutionSetting $institution = null,
        string|array|null $paper = null,
        ?string $template = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();
        $paper = $paper ?: 'A4';
        $template = $template ?: 'executive';

        if (Schema::hasTable('dts_records') && ! $proposal->relationLoaded('dtsRecord')) {
            $proposal->load('dtsRecord.routes');
        }

        $dts = $proposal->dtsRecord;
        $docId = (string) ($dts?->DocumentID ?: $proposal->proposal_code);
        $formattedQr = (string) ($proposal->tracking_number ?: ($dts?->formatted_qr ?: (function_exists('formatQR12Digits') ? formatQR12Digits($dts?->QRCODE ?: $dts?->id) : (string) $proposal->proposal_code)));

        $gen = new static($paper, 'P', 'mm', $institution, 'Extension Proposal');
        $gen->docKind = 'extension_proposal';
        $gen->extensionProposal = $proposal;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $formattedQr;
        $gen->trackingNumber = $proposal->tracking_number ?: $docId;
        $gen->policyNumber = $proposal->proposal_code;
        $gen->effectivityDate = $proposal->created_at ? $proposal->created_at->format('F d, Y') : date('F d, Y');
        $gen->officeName = 'Extension Services / Office of RIE';
        $gen->showOfficeSubheader = false;
        $gen->qrTargetUrl = $formattedQr;

        $gen->applyThemeColor();
        $gen->applyDocSettings('Extension Proposal');
        $gen->applyHeaderMode($template, $proposal);

        $acronym = $institution?->school_acronym ?: 'SNSU';
        $gen->SetTitle($acronym.' Extension Proposal: '.$gen->policyNumber);
        $gen->renderExtensionProposalBody();

        return $gen;
    }

    /**
     * Factory: Generate PDF for an Activity Design (FPDF Official Format).
     */
    public static function fromActivityDesign(
        ActivityDesign $design,
        ?InstitutionSetting $institution = null,
        string|array|null $paper = null,
        ?string $template = null,
    ): static {
        $institution = $institution ?? TenantContext::getInstitution();
        $paper = $paper ?: 'A4';
        $template = $template ?: 'executive';

        $docId = (string) $design->design_code;
        $tracking = (string) ($design->tracking_number ?: $design->design_code);

        $gen = new static($paper, 'P', 'mm', $institution, 'Activity Design');
        $gen->docKind = 'activity_design';
        $gen->activityDesign = $design;
        $gen->trackingCodeType = 'barcode';
        $gen->documentId = $docId;
        $gen->formattedQr = $tracking;
        $gen->trackingNumber = $tracking;
        $gen->policyNumber = $design->design_code;
        $gen->effectivityDate = $design->created_at ? $design->created_at->format('F d, Y') : date('F d, Y');
        $gen->officeName = 'Extension Services / Office of RIE';
        $gen->showOfficeSubheader = false;
        $gen->qrTargetUrl = $tracking;

        $gen->applyThemeColor();
        $gen->applyDocSettings('Activity Design');
        $gen->applyHeaderMode($template, $design);

        $acronym = $institution?->school_acronym ?: 'SNSU';
        $gen->SetTitle($acronym.' Activity Design: '.$gen->policyNumber);
        $gen->renderActivityDesignBody();

        return $gen;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helper: Apply Template Settings from Template Studio
    // ──────────────────────────────────────────────────────────────────────────

    private function applyThemeColor(): void
    {
        $theme = TenantContext::getTheme();
        $hex = ltrim($theme?->primary_color ?? '#0f172a', '#');
        if (strlen($hex) === 6) {
            $this->themeRgb = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        }
    }

    private function applyDocSettings(string $docType): void
    {
        $allSettings = $this->institution?->document_settings ?? [];
        $generic = $allSettings['Generic (Default)'] ?? ($allSettings['Generic'] ?? []);
        $specific = $allSettings[$docType] ?? [];
        $settings = array_merge($generic, $specific);

        if (empty($settings)) {
            return;
        }

        // Typography
        if (! empty($settings['font_family'])) {
            $this->fontFamily = $settings['font_family'];
        }
        $lineHeightVal = (float) ($settings['line_height'] ?? 1.5);
        $this->lineHeight = round(4.8 * $lineHeightVal, 1);
        $this->paragraphSpacing = (float) ($settings['paragraph_spacing'] ?? 3.0);

        // Header & Logo Layout
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
        if (isset($settings['header_left_slot_1'])) {
            $this->headerLeftSlot1 = $settings['header_left_slot_1'];
        }
        if (isset($settings['header_left_slot_2'])) {
            $this->headerLeftSlot2 = $settings['header_left_slot_2'];
        }
        if (isset($settings['header_right_slot_1'])) {
            $this->headerRightSlot1 = $settings['header_right_slot_1'];
        }
        if (! empty($settings['header_school_name'])) {
            $this->schoolName = strtoupper($settings['header_school_name']);
        }
        if (! empty($settings['header_address'])) {
            $this->campusAddress = $settings['header_address'];
            $this->fullAddress = $settings['header_address'];
        }
        if (! empty($settings['office_name'])) {
            $this->officeName = $settings['office_name'];
        }

        // Footer Branding & Slots
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

        // Stamp Placement (Barcode & QR Code)
        if (! empty($settings['barcode_position'])) {
            $this->barcodePosition = $settings['barcode_position'];
        }
        if (! empty($settings['qr_position'])) {
            $this->qrPosition = $settings['qr_position'];
        }

        // Footer Information & Toggles
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
        if (isset($settings['core_values'])) {
            $this->coreValues = $settings['core_values'];
        }
        if (isset($settings['show_distribution'])) {
            $this->showDistribution = (bool) $settings['show_distribution'];
        }
        if (! empty($settings['distribution_text'])) {
            $this->distributionText = $settings['distribution_text'];
        }
        if (! empty($settings['footer_address'])) {
            $this->fullAddress = $settings['footer_address'];
        }
        if (! empty($settings['footer_contact'])) {
            $this->mobileContact = $settings['footer_contact'];
        }
        if (! empty($settings['footer_website'])) {
            $this->websiteAndSocial = $settings['footer_website'];
        }
    }

    /**
     * Apply header/template mode and, for ISO forms, set document control fields.
     */
    private function applyHeaderMode(string $template, Model $document): void
    {
        $this->headerMode = ($template === 'iso_form' || $template === 'iso')
            ? static::HEADER_ISO_FORM
            : static::HEADER_EXECUTIVE;

        if ($this->headerMode !== static::HEADER_ISO_FORM) {
            return;
        }

        $effectiveDate = match (true) {
            $document instanceof InstitutionalPolicy => $document->doc_effective_date?->format('M d, Y')
                ?? $document->effectivity_date?->format('M d, Y')
                ?? date('M d, Y'),
            $document instanceof UniversityMemorandum => $document->doc_effective_date?->format('M d, Y')
                ?? $document->memo_date?->format('M d, Y')
                ?? date('M d, Y'),
            $document instanceof ExtensionPlan => $document->created_at?->format('M d, Y')
                ?? date('M d, Y'),
            $document instanceof ExtensionProposal => $document->created_at?->format('M d, Y')
                ?? date('M d, Y'),
            default => date('M d, Y'),
        };

        $acronym = $this->institution?->school_acronym ?: 'RIE';
        $docNo = match (true) {
            $document instanceof InstitutionalPolicy => $document->doc_control_no ?? 'F-POL-001',
            $document instanceof UniversityMemorandum => $document->doc_control_no ?? 'F-ISO-001',
            $document instanceof ExtensionPlan => "FM-{$acronym}-EXT-01",
            $document instanceof ExtensionProposal => "FM-{$acronym}-PROP-01",
            default => "FM-{$acronym}-DOC-01",
        };

        $revNo = match (true) {
            $document instanceof ExtensionPlan || $document instanceof ExtensionProposal => '00',
            default => (string) ($document->doc_rev_no ?? '0'),
        };

        $tracking = match (true) {
            $document instanceof ExtensionPlan => (string) ($document->dts_document_id ?: $document->plan_code),
            $document instanceof ExtensionProposal => (string) ($document->tracking_number ?: ($document->dts_document_id ?: $document->proposal_code)),
            default => (string) ($document->tracking_number ?? $document->id ?? ''),
        };

        $this->setDocumentControl(
            docNo: $docNo,
            effectiveDate: $effectiveDate,
            revNo: $revNo,
            pageNo: '1 of 1',
            tracking: $tracking,
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Body Renderers (one per document kind)
    // ──────────────────────────────────────────────────────────────────────────

    // ── POLICY ────────────────────────────────────────────────────────────────

    private function renderPolicyBody(): void
    {
        $margin = 20.0;
        $labelW = 32.0;
        $colonW = 4.0;
        $valX = $margin + $labelW + $colonW;
        $valW = $this->contentWidth - ($labelW + $colonW);
        $lineH = 5.2;
        $rowGap = 2.2;

        // ISO banner
        if ($this->headerMode === static::HEADER_ISO_FORM) {
            $this->SetFont($this->fontFamily, 'B', 11.5);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.3);
            $docTitle = strtoupper($this->policy->document_type ?: 'INSTITUTIONAL POLICY');
            $this->Cell(0, 6.5, $docTitle, 1, 1, 'C');
            $this->Ln(3.0);
        }

        // Metadata header block
        $this->metaRow('REFERENCE NO.', $this->policyNumber, $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B', 10.5);
        $this->metaRow('FOR / TO', 'Vice Presidents, Campus Directors, College Deans, Academic Directors, Department Chairs, Unit Heads, Faculty, Administrative Personnel, and All Concerned', $margin, $labelW, $colonW, $valX, $valW, $lineH, '', 9.5);
        $this->metaRow('FROM', strtoupper($this->policy->approving_authority ?: 'The University President'), $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B');
        $this->metaRow('SUBJECT', strtoupper($this->policy->title), $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B');
        if (! empty($this->policy->legal_basis)) {
            $this->metaRow('LEGAL BASIS', $this->policy->legal_basis, $margin, $labelW, $colonW, $valX, $valW, $lineH, '', 9.0, 4.8);
        }
        $this->Ln(1.5);

        // Divider
        $this->SetDrawColor(80, 80, 80);
        $this->SetLineWidth(0.25);
        $this->Line(20, $this->GetY(), $this->GetPageWidth() - 20, $this->GetY());
        $this->Ln(3.5);

        // Greeting
        $docSettings = $this->institution?->document_settings[$this->policy->document_type]
            ?? ($this->institution?->document_settings['Generic (Default)'] ?? []);
        $greeting = $docSettings['greeting'] ?? null;
        if (! empty($greeting)) {
            $this->SetFont($this->fontFamily, 'I', 10.5);
            $this->Cell(0, 5.5, $greeting, 0, 1, 'L');
            $this->Ln(2.0);
        }

        // Rationale
        if (! empty($this->policy->rationale)) {
            $this->SetFont($this->fontFamily, '', 10.0);
            $this->writeParagraph($this->policy->rationale);
        }

        // Scope
        if (! empty($this->policy->scope)) {
            $this->SetFont($this->fontFamily, '', 10.0);
            $this->writeParagraph('This issuance applies to '.$this->policy->scope);
        }

        // Provisions
        if (! empty($this->policy->provisions) && is_array($this->policy->provisions)) {
            foreach ($this->policy->provisions as $art) {
                if (is_string($art)) {
                    $this->SetFont($this->fontFamily, '', 9.5);
                    $this->writeParagraph($art);

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
                        $this->writeParagraph(
                            is_array($clause)
                                ? ($clause['body'] ?? $clause['text'] ?? $clause['clause'] ?? json_encode($clause))
                                : (string) $clause
                        );
                    }
                } elseif (is_string($clauses) && ! empty($clauses)) {
                    $this->writeParagraph($clauses);
                }
            }
        }

        // Signatories
        $this->renderPolicySignatories();
    }

    // ── MEMO ──────────────────────────────────────────────────────────────────

    private function renderMemoBody(): void
    {
        $margin = 20.0;
        $labelW = 32.0;
        $colonW = 4.0;
        $valX = $margin + $labelW + $colonW;
        $valW = $this->contentWidth - ($labelW + $colonW);
        $lineH = 5.0;
        $rowGap = 2.0;

        if ($this->headerMode === static::HEADER_ISO_FORM) {
            $this->SetFont($this->fontFamily, 'B', 11.5);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.3);
            $this->Cell(0, 6.5, 'UNIVERSITY MEMORANDUM', 1, 1, 'C');
            $this->Ln(3.0);
        }

        // Header rows
        $this->metaRow('REFERENCE NO.', $this->policyNumber, $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B', 10.0);

        // TO — supports roster modes
        $this->renderMemoToRow($margin, $labelW, $colonW, $valX, $valW, $lineH);

        $this->metaRow('FROM', strtoupper($this->memo->signatory_from ?: 'The University President'), $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B');
        $this->metaRow('SUBJECT', strtoupper($this->memo->subject), $margin, $labelW, $colonW, $valX, $valW, $lineH, 'B');
        $this->metaRow('DATE', $this->effectivityDate, $margin, $labelW, $colonW, $valX, $valW, $lineH, '');

        if (! empty($this->memo->legal_basis)) {
            $this->metaRow('LEGAL BASIS', $this->memo->legal_basis, $margin, $labelW, $colonW, $valX, $valW, $lineH, '', 9.0, 4.5);
        }

        // Divider
        $this->Ln(1.5);
        $this->SetDrawColor(80, 80, 80);
        $this->SetLineWidth(0.25);
        $this->Line(20, $this->GetY(), $this->GetPageWidth() - 20, $this->GetY());
        $this->Ln(3.5);

        // Greeting
        $docSettings = $this->institution?->document_settings[$this->memo->document_type ?? 'University Memorandum']
            ?? ($this->institution?->document_settings['Generic (Default)'] ?? []);
        $greeting = $docSettings['greeting'] ?? null;
        if (! empty($greeting)) {
            $this->SetFont($this->fontFamily, 'I', 9.5);
            $this->Cell(0, 5.0, $greeting, 0, 1, 'L');
            $this->Ln(2.0);
        }

        // Body
        if (! empty($this->memo->body_content)) {
            $this->writeParagraph($this->memo->body_content);
        }

        // Directives
        if (! empty($this->memo->directives) && is_array($this->memo->directives)) {
            foreach ($this->memo->directives as $dir) {
                if (is_string($dir)) {
                    $this->writeParagraph($dir);

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
                    $this->writeParagraph($body);
                }
            }
        }

        // Content table
        $this->renderMemoContentTable();

        // Closing
        $closing = $docSettings['closing'] ?? 'For strict and immediate compliance.';
        if (! empty($closing)) {
            $this->Ln(1.0);
            $this->writeParagraph($closing);
        }

        // Signatories
        $this->renderMemoSignatories();

        // Annex A (distribution page)
        $this->renderMemoAnnexPage();
    }

    private function renderMemoToRow(float $margin, float $labelW, float $colonW, float $valX, float $valW, float $lineH): void
    {
        $y = $this->GetY();
        $this->SetXY($margin, $y);
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->Cell($labelW, $lineH, 'FOR / TO', 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');

        $mode = $this->memo->recipient_distribution_mode;
        $roster = $this->memo->recipient_roster;

        if ($mode === 'separate_page') {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, $lineH, 'SEE ATTACHED LIST', 0, 'L');
        } elseif ($mode === 'inline_roster' && ! empty($roster)) {
            $nameW = 85.0;
            $roleW = $valW - $nameW;
            foreach ($roster as $rec) {
                $rowY = $this->GetY();
                $this->SetXY($valX, $rowY);
                $this->SetFont($this->fontFamily, 'B', 9.0);
                $this->Cell($nameW, $lineH, strtoupper(trim($rec['name'] ?: ($rec['office'] ?? ''))), 0, 0, 'L');
                $this->Cell($roleW, $lineH, strtoupper(trim($rec['designation'] ?? ($rec['role'] ?? ''))), 0, 1, 'L');
            }
        } else {
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->SetLeftMargin($valX);
            $this->SetXY($valX, $y);
            $this->MultiCell($valW, $lineH, $this->memo->recipient_to ?: 'All Concerned', 0, 'L');
        }

        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln(2.0);
    }

    private function renderMemoSignatories(): void
    {
        $tenant = $this->institution?->tenant_id ?? $this->memo->tenant_id ?? 1;
        $docType = $this->memo->document_type ?? 'University Memorandum';

        $signatories = PolicySignatory::where('tenant_id', $tenant)
            ->where('is_template', true)
            ->where(function ($q) use ($docType) {
                $q->where('document_type', $docType)
                    ->orWhere('document_type', 'like', '%Memorandum%');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($signatories->isNotEmpty()) {
            $this->renderSignatoryGrid($docType, $signatories);

            return;
        }

        $this->renderMemoLegacySignatory();
    }

    private function renderMemoLegacySignatory(): void
    {
        $allSettings = $this->institution?->document_settings ?? [];
        $docSettings = $allSettings[$this->memo->document_type ?? 'University Memorandum']
            ?? ($allSettings['Generic (Default)'] ?? []);
        $signatoryLayout = strtolower($docSettings['signatory_layout'] ?? 'right');
        $sigGap = (float) ($docSettings['signature_gap'] ?? 9.0);

        if ($this->GetY() > 225) {
            $this->AddPage();
        }
        $this->Ln(6.0);
        $boxW = 75.0;
        $margin = $this->leftMargin;
        $usable = $this->contentWidth;

        $boxX = match ($signatoryLayout) {
            'left' => $margin,
            'center' => $margin + ($usable - $boxW) / 2.0,
            default => $margin + $usable - $boxW,
        };

        $sigFrom = $this->memo->signatory_from;
        $sigTitle = $this->memo->signatory_title;

        if (empty($sigFrom)) {
            $tenantId = $this->institution?->tenant_id ?? $this->memo->tenant_id ?? 1;
            $centralSignatories = CentralizedSignatoryService::getSignatoriesFor('Office Memorandum', $tenantId, null);
            if ($centralSignatories->isEmpty()) {
                $centralSignatories = CentralizedSignatoryService::getSignatoriesFor('Memorandum', $tenantId, null);
            }
            $targetSig = $centralSignatories->first(fn ($s) => in_array($s->tier_role, ['approved', 'noted', 'reviewed']));

            if ($targetSig && ! empty($targetSig->resolved_name)) {
                $sigFrom = $targetSig->resolved_name;
                $sigTitle = $sigTitle ?: ($targetSig->resolved_designation ?: 'University President');
            } else {
                $matrixSignatory = SignatoryMatrix::where('tenant_id', $tenantId)
                    ->where(function ($q) {
                        $q->where('role_title', 'like', '%President%')
                            ->orWhere('role_title', 'like', '%Approved by%');
                    })
                    ->first();
                if ($matrixSignatory && ! empty($matrixSignatory->employee_name)) {
                    $sigFrom = $matrixSignatory->employee_name;
                    $sigTitle = $sigTitle ?: ($matrixSignatory->designation ?: 'University President');
                } else {
                    $sigFrom = 'AUTHORIZED SIGNATORY OFFICIAL';
                    $sigTitle = $sigTitle ?: 'Office of the President';
                }
            }
        }

        $this->SetXY($boxX, $this->GetY() + $sigGap);
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->Cell($boxW, 4.5, strtoupper($sigFrom), 0, 1, 'C');
        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, '', 8.5);
        $this->SetTextColor(71, 85, 105);
        $this->Cell($boxW, 4.0, $sigTitle, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    private function renderMemoAnnexPage(): void
    {
        if ($this->memo->recipient_distribution_mode !== 'separate_page' || empty($this->memo->recipient_roster)) {
            return;
        }

        $this->AddPage();
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell(0, 5.5, 'ANNEX "A" - MEMORANDUM DISTRIBUTION ROSTER', 0, 1, 'C');

        $this->SetFont($this->fontFamily, 'I', 8.5);
        $this->SetTextColor(51, 65, 85);
        $refText = 'Relative to: '.$this->sanitizeText($this->policyNumber).' - '.$this->sanitizeText($this->memo->subject);
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

        $colWidths = [8.0, 54.0, 48.0, 22.0, 23.0, 15.0];
        $headers = ['#', 'Recipient Office / College', 'Designated Official / Name', 'Campus', 'Signature', 'Date Rec.'];
        $this->printRosterTableHeader($colWidths, $headers);

        $this->SetFont($this->fontFamily, '', 8.0);
        $this->SetDrawColor(100, 116, 139);
        $this->SetLineWidth(0.2);

        foreach ($this->memo->recipient_roster as $idx => $rec) {
            $officeName = $this->sanitizeText(trim($rec['office'] ?? ''));
            $officialName = $this->sanitizeText(trim($rec['name'] ?? ''));
            if (! empty($rec['designation'])) {
                $officialName .= "\n".$this->sanitizeText(trim($rec['designation']));
            }
            $campusName = $this->sanitizeText(trim($rec['campus'] ?? 'Main Campus'));

            $officeLines = $this->estimateCellLines($officeName, $colWidths[1]);
            $nameLines = $this->estimateCellLines($officialName, $colWidths[2]);
            $campusLines = $this->estimateCellLines($campusName, $colWidths[3]);
            $maxLines = max(1, $officeLines, $nameLines, $campusLines);
            $rowH = max(7.5, ($maxLines * 3.6) + 2.4);

            if ($this->GetY() + $rowH > 250) {
                $this->AddPage();
                $this->printRosterTableHeader($colWidths, $headers);
                $this->SetFont($this->fontFamily, '', 8.0);
                $this->SetDrawColor(100, 116, 139);
                $this->SetLineWidth(0.2);
            }

            $y = $this->GetY();
            $x = 20.0;
            foreach ($colWidths as $w) {
                $this->Rect($x, $y, $w, $rowH);
                $x += $w;
            }

            $this->SetXY(20.0, $y + (($rowH - 4.0) / 2));
            $this->Cell($colWidths[0], 4.0, (string) ($idx + 1), 0, 0, 'C');
            $this->SetXY(20.0 + $colWidths[0] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[1] - 2.0, 3.6, $officeName, 0, 'L');
            $this->SetXY(20.0 + $colWidths[0] + $colWidths[1] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[2] - 2.0, 3.6, $officialName, 0, 'L');
            $this->SetXY(20.0 + $colWidths[0] + $colWidths[1] + $colWidths[2] + 1.0, $y + 1.2);
            $this->MultiCell($colWidths[3] - 2.0, 3.6, $campusName, 0, 'L');
            $this->SetY($y + $rowH);
        }

        $this->Ln(3.5);
        $this->SetFont($this->fontFamily, 'I', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 3.5, '* All receiving offices shall return a signed/stamped copy to the Records Management Office within 24 hours of receipt.', 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
    }

    private function renderMemoContentTable(): void
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

        if ($this->GetY() > 220) {
            $this->AddPage();
        }

        $this->Ln(2.0);
        if (! empty($table['title'])) {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->Cell($this->contentWidth, 5.5, strtoupper($table['title']), 0, 1, 'L');
            $this->Ln(1.0);
        }

        $colWidths = $this->distributeColumnWidths($numCols, $this->contentWidth);

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

        foreach ($rows as $row) {
            $maxLines = 1;
            $cleanRow = [];
            foreach ($headers as $colIdx => $hdr) {
                $cellVal = $this->sanitizeText(trim((string) ($row[$colIdx] ?? '')));
                $cleanRow[$colIdx] = $cellVal;
                $lines = $this->estimateCellLines($cellVal, $colWidths[$colIdx]);
                if ($lines > $maxLines) {
                    $maxLines = $lines;
                }
            }

            $lineH = 3.6;
            $rowH = max(6.5, ($maxLines * $lineH) + 2.4);

            if ($this->GetY() + $rowH > 248) {
                $this->AddPage();
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
            foreach ($colWidths as $w) {
                $this->Rect($x, $y, $w, $rowH);
                $x += $w;
            }
            $curX = 20.0;
            foreach ($headers as $colIdx => $hdr) {
                $this->SetXY($curX + 1.0, $y + 1.2);
                $this->MultiCell($colWidths[$colIdx] - 2.0, $lineH, $cleanRow[$colIdx], 0, 'L');
                $curX += $colWidths[$colIdx];
            }
            $this->SetY($y + $rowH);
        }

        $this->Ln(3.0);
    }

    // ── DTS ───────────────────────────────────────────────────────────────────

    /**
     * Minimal DTS routing slip body.
     * A full-featured DTS slip is available via DtsRoutingSlipPdf;
     * this provides a lightweight summary sheet when accessed from the viewer.
     */
    private function renderDtsBody(): void
    {
        $record = $this->dtsRecord;
        $margin = 20.0;
        $w = $this->contentWidth;

        // Metadata grid
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(30, 41, 59);
        $this->SetLineWidth(0.3);

        $pairs = [
            ['Tracking Number', (string) $record->DocumentID],
            ['Document Type', $record->document_type ?? '—'],
            ['Subject / Title', $record->subject ?? $record->title ?? '—'],
            ['Origin / From', $record->origin_unit ?? $record->source_unit ?? '—'],
            ['Current Office', $record->current_unit ?? '—'],
            ['Status', $record->current_status ?? 'In Transit'],
            ['Date Filed', $record->created_at?->format('F d, Y') ?? '—'],
            ['Priority', $record->priority ?? 'Normal'],
        ];

        $labelW = 50.0;
        $valW = $w - $labelW;
        $rowH = 6.5;
        foreach ($pairs as [$label, $value]) {
            $y = $this->GetY();
            $this->SetXY($margin, $y);
            $this->SetFont($this->fontFamily, 'B', 8.5);
            $this->Cell($labelW, $rowH, $label, 1, 0, 'L', true);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->Cell($valW, $rowH, $this->sanitizeText((string) $value), 1, 1, 'L');
        }

        $this->Ln(4.0);

        // Notes
        if (! empty($record->remarks)) {
            $this->SetFont($this->fontFamily, 'B', 9.0);
            $this->Cell($w, 5.5, 'REMARKS / INSTRUCTIONS', 0, 1, 'L');
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->MultiCell($w, 4.5, $this->sanitizeText($record->remarks), 1, 'L');
            $this->Ln(3.0);
        }
    }

    // ── EXTENSION PLAN ────────────────────────────────────────────────────────

    /**
     * Render the official Extension Plan document body matching the institutional paper format.
     */
    private function renderExtensionPlanBody(): void
    {
        $plan = $this->extensionPlan;
        $margin = $this->leftMargin ?: 20.0;
        $w = $this->contentWidth;
        $lineH = 4.6;

        // 1. Centered Title
        $this->SetFont($this->fontFamily, 'B', 13.0);
        $this->Cell($w, 6.5, 'EXTENSION PLAN', 0, 1, 'C');
        $this->Ln(3.5);

        // 2. Program & Extension Activities
        $labelW = 52.0;
        $valW = $w - $labelW;

        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($labelW, 5.5, 'PROGRAM:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', 11.0);
        $this->MultiCell($valW, 5.5, $this->sanitizeText($plan->program_name), 0, 'L');
        $this->Ln(1.8);

        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($labelW, 5.5, 'EXTENSION ACTIVITIES:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', 11.0);
        $this->MultiCell($valW, 5.5, $this->sanitizeText($plan->extension_activity), 0, 'L');
        $this->Ln(3.0);

        // 3. Objectives
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($w, 5.5, 'Objectives:', 0, 1, 'L');
        $this->Ln(1.2);

        $rawObj = trim($plan->objectives ?: '');
        $lines = preg_split('/\r\n|\r|\n/', $rawObj);
        $this->SetFont($this->fontFamily, '', 11.0);
        $this->SetTextColor(30, 41, 59);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^(\d+[\.\)])\s*(.*)$/', $line, $m)) {
                $num = $m[1];
                $txt = $m[2];
                $numW = 9.0;
                $this->SetX($margin + 3.0);
                $this->SetFont($this->fontFamily, 'B', 11.0);
                $this->Cell($numW, 5.0, $num, 0, 0, 'L');
                $this->SetFont($this->fontFamily, '', 11.0);
                $this->MultiCell($w - 3.0 - $numW, 5.0, $this->sanitizeText($txt), 0, 'L');
                $this->Ln(1.2);
            } else {
                $this->SetX($margin + 2.0);
                $this->SetFont($this->fontFamily, '', 11.0);
                $this->MultiCell($w - 2.0, 5.0, $this->sanitizeText($line), 0, 'L');
                $this->Ln(1.5);
            }
        }
        $this->Ln(3.0);

        // 4. Schedule of Activities: Formal Table with automatic wrap and zero overlap
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($w, 5.5, 'Schedule of Activities:', 0, 1, 'L');
        $this->Ln(1.8);

        // Column widths
        $col1W = $w - 58.0;
        $col2W = 58.0;
        $headerH = 7.0;

        if ($this->GetY() + $headerH + 15.0 > $this->GetPageHeight() - $this->bottomMargin) {
            $this->AddPage();
        }

        // Draw Table Header
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->SetTextColor(30, 41, 59);

        $this->Cell($col1W, $headerH, '  Activity / Milestone Description', 1, 0, 'L', true);
        $this->Cell($col2W, $headerH, 'Target Date / Period', 1, 1, 'C', true);

        // Dynamic Milestones from Plan metadata with fallback to default timeline
        $schedItems = [];
        foreach ($plan->milestones_list as $m) {
            $schedItems[$m['title']] = $m['target_date'] ?? null;
        }

        $itemNo = 1;
        $this->SetLineWidth(0.25);

        foreach ($schedItems as $label => $val) {
            $valStr = $val ?: 'To be scheduled';
            $fullLabel = " {$itemNo}.  {$label}";
            $itemNo++;

            $rowLineH = 4.2;
            $lines1 = $this->calculateCellLines($fullLabel, $col1W, 9.5, '');
            $lines2 = $this->calculateCellLines($valStr, $col2W, 9.5, '');
            $maxLines = max($lines1, $lines2);
            $rowH = max(6.8, ($maxLines * $rowLineH) + 2.5);

            if ($this->GetY() + $rowH > $this->GetPageHeight() - $this->bottomMargin) {
                $this->AddPage();
                $this->SetFillColor(241, 245, 249);
                $this->SetDrawColor(203, 213, 225);
                $this->SetLineWidth(0.3);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(30, 41, 59);
                $this->Cell($col1W, $headerH, '  Activity / Milestone Description (Cont.)', 1, 0, 'L', true);
                $this->Cell($col2W, $headerH, 'Target Date / Period', 1, 1, 'C', true);
                $this->SetLineWidth(0.25);
            }

            $curX = $this->GetX();
            $curY = $this->GetY();

            $this->SetDrawColor(203, 213, 225);
            $this->Rect($curX, $curY, $col1W, $rowH);
            $this->Rect($curX + $col1W, $curY, $col2W, $rowH);

            $padY1 = $curY + (($rowH - ($lines1 * $rowLineH)) / 2.0);
            $this->SetXY($curX + 2.0, $padY1);
            $this->SetFont($this->fontFamily, '', 9.5);
            $this->SetTextColor(30, 41, 59);
            $this->MultiCell($col1W - 4.0, $rowLineH, $this->sanitizeText($fullLabel), 0, 'L');

            $padY2 = $curY + (($rowH - ($lines2 * $rowLineH)) / 2.0);
            $this->SetXY($curX + $col1W + 1.5, $padY2);
            $this->SetFont($this->fontFamily, '', 9.5);
            if ($valStr === 'To be scheduled') {
                $this->SetTextColor(148, 163, 184);
            } else {
                $this->SetTextColor(15, 23, 42);
            }
            $this->MultiCell($col2W - 3.0, $rowLineH, $this->sanitizeText($valStr), 0, 'C');
            $this->SetTextColor(30, 41, 59);

            $this->SetXY($curX, $curY + $rowH);
        }

        $this->Ln(3.5);

        // 5. Project Scope & Resource Summary
        if ($this->GetY() + 32.0 > $this->GetPageHeight() - $this->bottomMargin) {
            $this->AddPage();
        }

        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($w, 5.5, 'Project Scope & Resource Summary:', 0, 1, 'L');
        $this->Ln(1.8);

        $boxColW = $w / 2.0;
        $boxH = 12.0;
        $curY = $this->GetY();
        $this->SetFillColor(250, 252, 255);
        $this->SetDrawColor(203, 213, 225);

        // Box 1: Beneficiaries
        $this->Rect($margin, $curY, $boxColW, $boxH, 'DF');
        $this->SetXY($margin + 3.0, $curY + 1.8);
        $this->SetFont($this->fontFamily, 'B', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($boxColW - 6.0, 3.5, 'TARGET BENEFICIARIES', 0, 1, 'L');
        $this->SetX($margin + 3.0);
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($boxColW - 6.0, 4.5, $this->sanitizeText($plan->beneficiaries ?: 'N/A'), 0, 1, 'L');

        // Box 2: Target Beneficiaries Count
        $this->Rect($margin + $boxColW, $curY, $boxColW, $boxH, 'DF');
        $this->SetXY($margin + $boxColW + 3.0, $curY + 1.8);
        $this->SetFont($this->fontFamily, 'B', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($boxColW - 6.0, 3.5, 'TARGET NUMBER OF BENEFICIARIES', 0, 1, 'L');
        $this->SetX($margin + $boxColW + 3.0);
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($boxColW - 6.0, 4.5, number_format((int) $plan->target_beneficiaries_count).' Participants', 0, 1, 'L');

        $curY += $boxH;

        // Box 3: Proposed Budget
        $this->Rect($margin, $curY, $boxColW, $boxH, 'DF');
        $this->SetXY($margin + 3.0, $curY + 1.8);
        $this->SetFont($this->fontFamily, 'B', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($boxColW - 6.0, 3.5, 'PROPOSED BUDGET', 0, 1, 'L');
        $this->SetX($margin + 3.0);
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($boxColW - 6.0, 4.5, 'PHP '.number_format((float) $plan->proposed_budget, 2), 0, 1, 'L');

        // Box 4: Extensionists Count
        $this->Rect($margin + $boxColW, $curY, $boxColW, $boxH, 'DF');
        $this->SetXY($margin + $boxColW + 3.0, $curY + 1.8);
        $this->SetFont($this->fontFamily, 'B', 7.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($boxColW - 6.0, 3.5, 'NUMBER OF EXTENSIONISTS', 0, 1, 'L');
        $this->SetX($margin + $boxColW + 3.0);
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetTextColor(15, 23, 42);
        $this->Cell($boxColW - 6.0, 4.5, number_format((int) $plan->extensionists_count).' Faculty / Personnel', 0, 1, 'L');

        $this->SetY($curY + $boxH + 5.0);

        // 6. Institutional Signatories (Dynamic 12-Column CSS Grid Engine)
        if ($this->GetY() + 45.0 > $this->GetPageHeight() - $this->bottomMargin) {
            $this->AddPage();
        }

        // Fetch runtime signatories from sys_document_signatories
        $signatories = DocumentSignatory::where('document_type', $plan->getMorphClass())
            ->where('document_id', $plan->getKey())
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($signatories->isEmpty()) {
            ExtensionSignatoryResolver::snapshotToDocument($plan);
            $signatories = DocumentSignatory::where('document_type', $plan->getMorphClass())
                ->where('document_id', $plan->getKey())
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        // Dynamically align unsigned signatories with current central matrix layout
        $matrices = DocumentSignatoryMatrix::where('tenant_id', $plan->tenant_id ?? 1)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('doc_type_id', 1)
                    ->orWhere('process_type', 'Extension Plan');
            })
            ->orderBy('sort_order', 'asc')
            ->get()
            ->keyBy('sort_order');

        if ($matrices->isNotEmpty()) {
            foreach ($signatories as $sig) {
                if (empty($sig->signed_at) && $matrices->has($sig->sort_order)) {
                    $mat = $matrices->get($sig->sort_order);
                    $sig->col_span = $mat->col_span ?: 12;
                    $sig->alignment = $mat->alignment ?: 'left';
                    if (! empty($mat->role_label)) {
                        $sig->role_label = $mat->role_label;
                    }
                    $sig->save();
                }
            }
        }

        $this->render12ColSignatoryBlock($signatories, $w, $margin);
    }

    /**
     * Render the official 6-Page SNSU Activity Proposal.
     * Aligned with official university format, letterhead, and review tiers.
     */
    private function renderExtensionProposalBody(): void
    {
        $proposal = $this->extensionProposal;
        if (! $proposal) {
            return;
        }

        $this->SetAutoPageBreak(false);
        $margin = $this->leftMargin ?: 20.0;
        $w = $this->contentWidth;
        $maxY = 252.0; // Barcode safe boundary

        $lineH11 = 5.8; // 1.5 line-height for 11pt font
        $lineH10 = 5.2;

        $checkPageBreak = function (float $neededHeight) use ($maxY): bool {
            if ($this->GetY() + $neededHeight > $maxY) {
                $this->AddPage();
                $this->SetTextColor(17, 24, 39);

                return true;
            }

            return false;
        };

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 1: BASIC INFORMATION (Sections A to I)
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();
        $this->SetTextColor(17, 24, 39);

        // Document Title
        $this->SetFont($this->fontFamily, 'B', 14.0);
        $this->Cell($w, 6.0, 'ACTIVITY PROPOSAL', 0, 1, 'C');
        $this->Ln(2.0);

        // Section Header
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'PART I. BASIC INFORMATION', 0, 1, 'L');
        $this->Ln(2.0);

        $p1Font = 11.0;
        $p1LineH = 5.2;
        $labelW = 54.0;
        $valW = $w - $labelW;

        // A. Title of Activity
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($labelW, $p1LineH, 'A. Title of the Activity:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->MultiCell($valW, $p1LineH, $this->sanitizeText(strtoupper($proposal->title)), 'B', 'L');
        $this->Ln(1.5);

        // B. Banner Program
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($w, $p1LineH, 'B. Banner Program', 0, 1, 'L');

        $programsList = class_exists(ExtensionBannerProgram::class)
            ? ExtensionBannerProgram::active()->ordered()->pluck('name')->toArray()
            : [];
        if (empty($programsList)) {
            $programsList = ['TECHASSIST', 'FOODPRO', 'AGRIYAMAN', 'MODFARM 360', 'GOVeRN', 'SUSTINAHAN', 'RENEWTECH', 'ReStat-QUIIP', 'STEAMUP', 'TOURGABAY', 'MSMEntri', 'KATUTUBO', 'ECOREVIVE', 'GADLIGHT', 'WELLNESS', 'TUBIG-SIGURADO'];
        }
        $halfP = (int) ceil(count($programsList) / 2);
        $col1P = array_slice($programsList, 0, $halfP);
        $col2P = array_slice($programsList, $halfP);
        $selBanner = $proposal->banner_program;
        $bannerOther = $proposal->banner_program_other;

        $halfW = ($w - 10.0) / 2.0;
        $bpStartY = $this->GetY();

        // Column 1
        $this->SetFont($this->fontFamily, '', 8.5);
        foreach ($col1P as $idx => $bp) {
            $this->SetXY($margin + 6.0, $bpStartY + ($idx * 4.0));
            $checked = ($selBanner === $bp);
            $this->Cell(3.5, 3.5, $checked ? chr(215) : '', 1, 0, 'C');
            $this->SetX($margin + 11.0);
            $this->Cell($halfW - 5.0, 3.5, $bp, 0, 0, 'L');
        }

        // Column 2
        foreach ($col2P as $idx => $bp) {
            $this->SetXY($margin + $halfW + 10.0, $bpStartY + ($idx * 4.0));
            $checked = ($selBanner === $bp);
            $this->Cell(3.5, 3.5, $checked ? chr(215) : '', 1, 0, 'C');
            $this->SetX($margin + $halfW + 15.0);
            $this->Cell($halfW - 5.0, 3.5, $bp, 0, 0, 'L');
        }
        $maxColCount = max(count($col1P), count($col2P));
        $this->SetY($bpStartY + ($maxColCount * 4.0) + 1.0);

        // Others
        $isOther = ($selBanner === 'Others' || (! in_array($selBanner, $programsList) && ! empty($selBanner)));
        $this->SetX($margin + 6.0);
        $this->Cell(3.5, 3.5, $isOther ? chr(215) : '', 1, 0, 'C');
        $this->SetX($margin + 11.0);
        $this->Cell(15.0, 3.5, 'Others:', 0, 0, 'L');
        $otherText = $isOther ? ($bannerOther ?: $selBanner) : '';
        $this->Cell($w - 30.0, 3.5, $this->sanitizeText($otherText), 'B', 1, 'L');
        $this->Ln(1.5);

        // C. Extension Agenda
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($w, $p1LineH, 'C. Extension Agenda:', 0, 1, 'L');
        $agendas = class_exists(ExtensionAgenda::class)
            ? ExtensionAgenda::active()->ordered()->pluck('name')->toArray()
            : [];
        if (empty($agendas)) {
            $agendas = [
                'Transformative Learning',
                'Livelihood Generation',
                'Biodiversity Conservation',
                'Climate Change Adoption & Risk Management',
                'Good Governance',
            ];
        }
        $selectedAgendas = (array) ($proposal->agenda_priorities ?? ['Transformative Learning']);
        $this->SetFont($this->fontFamily, '', 9.0);
        foreach ($agendas as $ag) {
            $isAgChecked = in_array($ag, $selectedAgendas);
            $this->SetX($margin + 6.0);
            $this->Cell(3.5, 3.5, $isAgChecked ? chr(215) : '', 1, 0, 'C');
            $this->SetX($margin + 11.0);
            $this->Cell($w - 11.0, 3.5, $ag, 0, 1, 'L');
            $this->Ln(0.5);
        }
        $this->Ln(1.5);

        // D. SDG Addressed
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($labelW, $p1LineH, 'D. SDG Addressed:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $p1Font);
        $sdgArr = [];
        $selGoals = $proposal->resolved_sdg_alignments['selected'] ?? [4, 9, 17];
        foreach ($selGoals as $sg) {
            $sdgArr[] = "SDG {$sg}";
        }
        $this->MultiCell($valW, $p1LineH, $this->sanitizeText(implode(', ', $sdgArr)), 'B', 'L');
        $this->Ln(1.5);

        // E. Project Leader & Gender
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($labelW, $p1LineH, 'E. Project Leader:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $leaderW = $valW * 0.55;
        $this->Cell($leaderW, $p1LineH, $this->sanitizeText($proposal->project_leader_name), 'B', 0, 'L');
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell(20.0, $p1LineH, '   Gender:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $p1Font);
        $genderW = $valW - $leaderW - 20.0;
        $this->Cell($genderW, $p1LineH, $this->sanitizeText($proposal->project_leader_gender ?: 'N/A'), 'B', 1, 'L');
        $this->Ln(1.5);

        // F. Implementing Colleges
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($labelW, $p1LineH, 'F. Implementing Colleges:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $p1Font);
        $colleges = $proposal->implementing_colleges ?? [];
        if (empty($colleges)) {
            $colleges = [$proposal->unit?->unit_name ?: 'College of Computing and Information Sciences'];
        }
        $collegesStr = is_array($colleges) ? implode('; ', $colleges) : $colleges;
        $this->MultiCell($valW, $p1LineH, $this->sanitizeText($collegesStr), 'B', 'L');
        $this->Ln(1.5);

        // G. Collaborating Agencies (Zero text overlap)
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($w, $p1LineH, 'G. Collaborating Agencies:', 0, 1, 'L');
        $collab = $proposal->resolved_collaborating_agencies;
        if (empty($collab)) {
            $this->SetX($margin + 6.0);
            $this->SetFont($this->fontFamily, 'I', 10.0);
            $this->Cell($w - 6.0, 5.0, '- None indicated -', 0, 1, 'L');
        } else {
            foreach ($collab as $cRow) {
                $agName = trim($cRow['agency'] ?? '');
                $agRole = trim($cRow['role'] ?? 'Collaborating Partner');
                $this->SetX($margin + 6.0);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $strW = $this->GetStringWidth($agName);
                if ($strW > 100.0) {
                    $this->Cell(16.0, 4.8, 'Agency: ', 0, 0, 'L');
                    $this->SetFont($this->fontFamily, '', 10.0);
                    $this->MultiCell($w - 22.0, 4.8, $this->sanitizeText($agName), 'B', 'L');
                    $this->SetX($margin + 22.0);
                    $this->SetFont($this->fontFamily, 'B', 9.5);
                    $this->Cell(12.0, 4.8, 'Role: ', 0, 0, 'L');
                    $this->SetFont($this->fontFamily, '', 9.5);
                    $this->Cell($w - 34.0, 4.8, $this->sanitizeText($agRole), 'B', 1, 'L');
                } else {
                    $this->Cell(16.0, 4.8, 'Agency: ', 0, 0, 'L');
                    $this->SetFont($this->fontFamily, '', 10.0);
                    $this->Cell(95.0, 4.8, $this->sanitizeText($agName), 'B', 0, 'L');
                    $this->SetFont($this->fontFamily, 'B', 10.0);
                    $this->Cell(14.0, 4.8, '  Role: ', 0, 0, 'L');
                    $this->SetFont($this->fontFamily, '', 10.0);
                    $this->Cell($w - 131.0, 4.8, $this->sanitizeText($agRole), 'B', 1, 'L');
                }
            }
        }
        $this->Ln(1.5);

        // H. Target Beneficiaries Table
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($w, $p1LineH, 'H. Target Beneficiaries:', 0, 1, 'L');
        $bens = $proposal->resolved_target_beneficiaries;
        if (empty($bens)) {
            $bens = [['name' => $proposal->beneficiaries ?: 'Partner Beneficiaries', 'male' => 30, 'female' => 20, 'total' => 50]];
        }
        $bColW = round(($w - 60.0), 2);
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bColW, 5.0, 'Name / Group', 1, 0, 'L', true);
        $this->Cell(20.0, 5.0, 'Male', 1, 0, 'C', true);
        $this->Cell(20.0, 5.0, 'Female', 1, 0, 'C', true);
        $this->Cell(20.0, 5.0, 'Total', 1, 1, 'C', true);

        $totM = 0;
        $totF = 0;
        $totAll = 0;
        $this->SetFont($this->fontFamily, '', 9.5);
        foreach ($bens as $b) {
            $m = (int) ($b['male'] ?? 0);
            $f = (int) ($b['female'] ?? 0);
            $t = (int) ($b['total'] ?? ($m + $f));
            $totM += $m;
            $totF += $f;
            $totAll += $t;
            $this->Cell($bColW, 4.8, $this->sanitizeText($b['name'] ?? ''), 1, 0, 'L');
            $this->Cell(20.0, 4.8, (string) $m, 1, 0, 'C');
            $this->Cell(20.0, 4.8, (string) $f, 1, 0, 'C');
            $this->Cell(20.0, 4.8, (string) $t, 1, 1, 'C');
        }
        // Total row
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->Cell($bColW, 4.8, 'Total Beneficiaries:', 1, 0, 'R', true);
        $this->Cell(20.0, 4.8, (string) $totM, 1, 0, 'C', true);
        $this->Cell(20.0, 4.8, (string) $totF, 1, 0, 'C', true);
        $this->Cell(20.0, 4.8, (string) $totAll, 1, 1, 'C', true);
        $this->Ln(1.5);

        // I. Estimated Budget Requirement
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell($w, $p1LineH, 'I. Estimated Budget Requirement:', 0, 1, 'L');
        $this->SetFont($this->fontFamily, '', $p1Font);
        $this->SetX($margin + 6.0);
        $this->Cell(44.0, $p1LineH, 'Internal (Institution):', 0, 0, 'L');
        $internalFund = $proposal->fund_source_internal ?: ($proposal->budget_institution_share ?: 'In-Kind (University Service Vehicle & Faculty Expertise)');
        $this->Cell($w - 50.0, $p1LineH, $this->sanitizeText($internalFund), 'B', 1, 'L');

        $this->SetX($margin + 6.0);
        $this->Cell(44.0, $p1LineH, 'External (Partner):', 0, 0, 'L');
        $externalFund = $proposal->fund_source_external ?: ($proposal->budget_partner_share ?: 'In-Kind (Partner Venue & Provisions)');
        $this->Cell($w - 50.0, $p1LineH, $this->sanitizeText($externalFund), 'B', 1, 'L');

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 2: PART II. TECHNICAL DESCRIPTION (Starts with J. Duration)
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();

        // J. Duration at the top of Page 2
        $this->SetFont($this->fontFamily, 'B', $p1Font);
        $this->Cell(26.0, 5.8, 'J. Duration:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $p1Font);
        $dStart = $proposal->duration_start ? $proposal->duration_start->format('m/d/Y') : '—';
        $dEnd = $proposal->duration_end ? $proposal->duration_end->format('m/d/Y') : '—';
        $dMonths = $proposal->duration_months ?: 1;
        $this->Cell(52.0, 5.8, 'Started Date: '.$dStart, 0, 0, 'L');
        $this->Cell(52.0, 5.8, 'End Date: '.$dEnd, 0, 0, 'L');
        $this->Cell($w - 130.0, 5.8, 'No. of Months: '.$dMonths, 0, 1, 'L');
        $this->Ln(3.0);

        $this->SetFont($this->fontFamily, 'B', 12.0);
        $this->Cell($w, 6.0, 'PART II. TECHNICAL DESCRIPTION', 0, 1, 'L');
        $this->Ln(2.0);

        // A. RATIONALE (not to exceed 500 words)
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.5, 'A. RATIONALE (not to exceed 500 words)', 0, 1, 'L');
        $this->SetFont($this->fontFamily, '', 11.0);
        $introText = $this->htmlToPlainText($proposal->introduction ?: 'The extension activity addresses community capability building and technology transfer.');
        $this->printFlowingParagraphs($introText, $w, $lineH11, $maxY, $checkPageBreak, 11.0);
        $this->Ln(2.5);

        // B. OBJECTIVES
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.5, 'B. OBJECTIVES', 0, 1, 'L');
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell(42.0, $lineH11, 'General Objective:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', 11.0);
        $genObj = $proposal->resolved_general_objective;
        $this->MultiCell($w - 42.0, $lineH11, $this->sanitizeText($genObj), 0, 'L');
        $this->Ln(1.5);

        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.5, 'Specific Objectives:', 0, 1, 'L');
        $sObjs = $proposal->resolved_specific_objectives;
        foreach ($sObjs as $idx => $sObjStr) {
            $this->SetX($margin + 6.0);
            $this->SetFont($this->fontFamily, 'B', 11.0);
            $this->Cell(28.0, $lineH11, 'Objective '.($idx + 1).':', 0, 0, 'L');
            $this->SetFont($this->fontFamily, '', 11.0);
            $this->MultiCell($w - 34.0, $lineH11, $this->sanitizeText($sObjStr), 0, 'L');
            $this->Ln(0.5);
        }
        $this->Ln(2.5);

        // C. EXPECTED OUTPUT (First 4 rows on Page 2)
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.5, 'C. EXPECTED OUTPUT', 0, 1, 'L');
        $outputs = $proposal->resolved_expected_outputs;
        $outputsPage2 = array_slice($outputs, 0, 4);

        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(45.0, 5.2, 'Output Category', 1, 0, 'L', true);
        $this->Cell(25.0, 5.2, 'Target Qty', 1, 0, 'C', true);
        $this->Cell($w - 70.0, 5.2, 'Output Description / Key Indicators', 1, 1, 'L', true);

        $this->SetFont($this->fontFamily, '', 9.5);
        foreach ($outputsPage2 as $out) {
            $cat = $out['category'] ?? 'Category';
            $tgt = (string) ($out['target'] ?? '0');
            $desc = $this->htmlToPlainText((string) ($out['description'] ?? '—'));
            if (trim($desc) === '') {
                $desc = '—';
            }
            $lines = $this->estimateCellLines($desc, $w - 70.0);
            $rowH = max(5.2, $lines * 4.4 + 1.2);
            $curY = $this->GetY();
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->Cell(45.0, $rowH, $cat, 1, 0, 'L');
            $this->SetFont($this->fontFamily, '', 9.5);
            $this->Cell(25.0, $rowH, $tgt, 1, 0, 'C');
            $this->Rect($margin + 70.0, $curY, $w - 70.0, $rowH);
            $this->SetXY($margin + 70.0, $curY + 0.8);
            $this->MultiCell($w - 70.0, 4.4, $this->sanitizeText($desc), 0, 'L');
            $this->SetY($curY + $rowH);
        }

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 3: EXPECTED OUTPUT (Cont.), RELEVANCE, SCHEDULE, PROPONENTS, RISK
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();

        // Continuation of C. Expected Output (Remaining 3 rows)
        $outputsPage3 = array_slice($outputs, 4);
        if (! empty($outputsPage3)) {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->SetFillColor(241, 245, 249);
            $this->Cell(45.0, 5.0, 'Output Category', 1, 0, 'L', true);
            $this->Cell(25.0, 5.0, 'Target Qty', 1, 0, 'C', true);
            $this->Cell($w - 70.0, 5.0, 'Output Description / Key Indicators', 1, 1, 'L', true);

            $this->SetFont($this->fontFamily, '', 9.5);
            foreach ($outputsPage3 as $out) {
                $cat = $out['category'] ?? 'Category';
                $tgt = (string) ($out['target'] ?? '0');
                $desc = $this->htmlToPlainText((string) ($out['description'] ?? '—'));
                if (trim($desc) === '') {
                    $desc = '—';
                }
                $lines = $this->estimateCellLines($desc, $w - 70.0);
                $rowH = max(5.0, $lines * 4.2 + 1.2);
                $curY = $this->GetY();
                $this->SetFont($this->fontFamily, 'B', 9.5);
                $this->Cell(45.0, $rowH, $cat, 1, 0, 'L');
                $this->SetFont($this->fontFamily, '', 9.5);
                $this->Cell(25.0, $rowH, $tgt, 1, 0, 'C');
                $this->Rect($margin + 70.0, $curY, $w - 70.0, $rowH);
                $this->SetXY($margin + 70.0, $curY + 0.6);
                $this->MultiCell($w - 70.0, 4.2, $this->sanitizeText($desc), 0, 'L');
                $this->SetY($curY + $rowH);
            }
            $this->Ln(2.5);
        }

        // D. RELEVANCE OF THE PROGRAM/PROJECTS/ACTIVITY
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.2, 'D. RELEVANCE OF PROGRAM/PROJECTS/ACTIVITY', 0, 1, 'L');
        $this->SetFont($this->fontFamily, '', 11.0);
        $relevText = $this->htmlToPlainText($proposal->relevance_narrative ?: 'The extension activity directly aligns with university research thrusts and regional development.');
        $this->printFlowingParagraphs($relevText, $w, $lineH11, $maxY, $checkPageBreak, 11.0);
        $this->Ln(2.5);

        // E. SCHEDULE OF ACTIVITIES (Jan - Dec)
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.2, 'E. SCHEDULE OF ACTIVITIES', 0, 1, 'L');
        $schedData = $proposal->resolved_schedule_of_activities['rows'] ?? [];
        if (empty($schedData)) {
            $schedData = [
                ['no' => 1, 'activity' => 'Coordination with Partner Community & LGU', 'agency' => 'Project Leader / Team', 'months' => ['Sep' => 1]],
                ['no' => 2, 'activity' => 'Preparation of Training Modules & Logistics', 'agency' => 'Extension Faculty', 'months' => ['Sep' => 1, 'Oct' => 1]],
                ['no' => 3, 'activity' => 'Conduct of Capability Training Workshops', 'agency' => 'Project Proponents', 'months' => ['Oct' => 1]],
                ['no' => 4, 'activity' => 'Post-Activity Evaluation & Terminal Reporting', 'agency' => 'Project Proponents', 'months' => ['Nov' => 1]],
            ];
        }
        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $mWidth = 6.2;
        $actW = $w - 8.0 - 45.0 - ($mWidth * 12);

        $this->SetFont($this->fontFamily, 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(8.0, 5.0, 'No.', 1, 0, 'C', true);
        $this->Cell($actW, 5.0, 'Major Activities', 1, 0, 'L', true);
        $this->Cell(45.0, 5.0, 'Agency / Person Involved', 1, 0, 'L', true);
        foreach ($allMonths as $m) {
            $this->Cell($mWidth, 5.0, substr($m, 0, 1), 1, 0, 'C', true);
        }
        $this->Ln(5.0);

        $this->SetFont($this->fontFamily, '', 8.0);
        foreach ($schedData as $idx => $sRow) {
            $actDesc = $sRow['activity'] ?? ($sRow['name'] ?? '');
            $agency = $sRow['agency'] ?? ($sRow['person'] ?? 'Extension Team');
            $rMonths = $sRow['months'] ?? [];
            $lines = max($this->estimateCellLines($actDesc, $actW), $this->estimateCellLines($agency, 45.0));
            $rowH = max(4.6, $lines * 3.6 + 1.0);
            $curY = $this->GetY();
            $this->Cell(8.0, $rowH, (string) ($sRow['no'] ?? ($idx + 1)), 1, 0, 'C');

            $this->Rect($margin + 8.0, $curY, $actW, $rowH);
            $this->SetXY($margin + 8.0, $curY + 0.5);
            $this->MultiCell($actW, 3.6, $this->sanitizeText($actDesc), 0, 'L');

            $this->SetXY($margin + 8.0 + $actW, $curY);
            $this->Rect($margin + 8.0 + $actW, $curY, 45.0, $rowH);
            $this->SetXY($margin + 8.0 + $actW, $curY + 0.5);
            $this->MultiCell(45.0, 3.6, $this->sanitizeText($agency), 0, 'L');

            $curX = $margin + 8.0 + $actW + 45.0;
            foreach ($allMonths as $m) {
                $isActive = ! empty($rMonths[$m]) || (! empty($sRow['active_months']) && in_array($m, (array) $sRow['active_months']));
                $this->SetXY($curX, $curY);
                if ($isActive) {
                    $this->SetFillColor(30, 41, 59);
                    $this->SetTextColor(255, 255, 255);
                    $this->Cell($mWidth, $rowH, chr(215), 1, 0, 'C', true);
                    $this->SetTextColor(17, 24, 39);
                } else {
                    $this->Cell($mWidth, $rowH, '', 1, 0, 'C');
                }
                $curX += $mWidth;
            }
            $this->SetY($curY + $rowH);
        }
        $this->Ln(2.5);

        // F. PROJECT PROPONENTS (3 MultiCell Columns - ZERO OVERLAP)
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.2, 'F. PROJECT PROPONENTS', 0, 1, 'L');
        $propRows = $proposal->resolved_people_involved['rows'] ?? [];
        if (empty($propRows) && ! empty($proposal->proponents)) {
            $propRows = $proposal->proponents;
        }
        if (empty($propRows)) {
            $propRows = [
                ['name' => $proposal->project_leader_name, 'role' => 'Project Leader', 'responsibilities' => 'Overall project management, institutional coordination, and reporting.'],
            ];
        }

        $pCol1W = 46.0;
        $pCol2W = 46.0;
        $pCol3W = $w - ($pCol1W + $pCol2W);

        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($pCol1W, 5.2, 'Name', 1, 0, 'L', true);
        $this->Cell($pCol2W, 5.2, 'Project Role Title', 1, 0, 'L', true);
        $this->Cell($pCol3W, 5.2, 'Duties & Responsibilities', 1, 1, 'L', true);

        $this->SetFont($this->fontFamily, '', 9.0);
        foreach ($propRows as $p) {
            $pName = $p['name'] ?? '';
            $pRole = $p['role'] ?? 'Member';
            $pDuties = $this->htmlToPlainText((string) ($p['responsibilities'] ?? ($p['duties'] ?? 'Activity facilitation, technical modules, and logistics.')));

            $lines = max(
                $this->estimateCellLines($pName, $pCol1W - 4.0),
                $this->estimateCellLines($pRole, $pCol2W - 4.0),
                $this->estimateCellLines($pDuties, $pCol3W - 4.0)
            );
            $rowH = max(5.0, $lines * 3.8 + 1.4);
            $curY = $this->GetY();

            // Col 1: Name
            $this->Rect($margin, $curY, $pCol1W, $rowH);
            $this->SetXY($margin + 2.0, $curY + 0.6);
            $this->SetFont($this->fontFamily, 'B', 9.0);
            $this->MultiCell($pCol1W - 4.0, 3.8, $this->sanitizeText($pName), 0, 'L');

            // Col 2: Role (MultiCell wrapped - never overlaps into Col 3)
            $this->Rect($margin + $pCol1W, $curY, $pCol2W, $rowH);
            $this->SetXY($margin + $pCol1W + 2.0, $curY + 0.6);
            $this->SetFont($this->fontFamily, '', 9.0);
            $this->MultiCell($pCol2W - 4.0, 3.8, $this->sanitizeText($pRole), 0, 'L');

            // Col 3: Duties
            $this->Rect($margin + $pCol1W + $pCol2W, $curY, $pCol3W, $rowH);
            $this->SetXY($margin + $pCol1W + $pCol2W + 2.0, $curY + 0.6);
            $this->MultiCell($pCol3W - 4.0, 3.8, $this->sanitizeText($pDuties), 0, 'L');

            $this->SetY($curY + $rowH);
        }
        $this->Ln(2.5);

        // G. RISK MANAGEMENT
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.2, 'G. RISK MANAGEMENT', 0, 1, 'L');
        $risks = $proposal->resolved_risk_management;
        if (empty($risks)) {
            $risks = [
                ['objective_no' => '1', 'activity_no' => '1', 'risk' => 'Inclement weather or typhoon disruption', 'risk_level' => 'Low', 'prevention' => 'Coordinate backup indoor venue and monitor PAGASA advisories.'],
                ['objective_no' => '2', 'activity_no' => '3', 'risk' => 'Intermittent internet connectivity during demo', 'risk_level' => 'Medium', 'prevention' => 'Pre-download offline presentation slides and localized tools.'],
                ['objective_no' => '3', 'activity_no' => '4', 'risk' => 'Low participant post-training turnout', 'risk_level' => 'High', 'prevention' => 'Active coordination with LGU leaders and award certificates of completion.'],
            ];
        }
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetFillColor(241, 245, 249);
        $this->Cell(14.0, 5.0, 'Obj No.', 1, 0, 'C', true);
        $this->Cell(14.0, 5.0, 'Act No.', 1, 0, 'C', true);
        $this->Cell(55.0, 5.0, 'Risk', 1, 0, 'L', true);
        $this->Cell(18.0, 5.0, 'Level', 1, 0, 'C', true);
        $this->Cell($w - 101.0, 5.0, 'Prevention / Mitigation Plan', 1, 1, 'L', true);

        $this->SetFont($this->fontFamily, '', 8.5);
        foreach ($risks as $rk) {
            $rRisk = $this->htmlToPlainText((string) ($rk['risk'] ?? ''));
            $rPrev = $this->htmlToPlainText((string) ($rk['prevention'] ?? ''));
            $lines = max($this->estimateCellLines($rRisk, 55.0), $this->estimateCellLines($rPrev, $w - 101.0));
            $rowH = max(4.8, $lines * 3.8 + 1.2);
            $curY = $this->GetY();
            $this->Cell(14.0, $rowH, (string) ($rk['objective_no'] ?? 1), 1, 0, 'C');
            $this->Cell(14.0, $rowH, (string) ($rk['activity_no'] ?? 1), 1, 0, 'C');

            $this->Rect($margin + 28.0, $curY, 55.0, $rowH);
            $this->SetXY($margin + 28.0, $curY + 0.6);
            $this->MultiCell(55.0, 3.8, $this->sanitizeText($rRisk), 0, 'L');

            $this->SetXY($margin + 83.0, $curY);
            $this->Cell(18.0, $rowH, ucfirst(strtolower($rk['risk_level'] ?? 'Low')), 1, 0, 'C');

            $this->Rect($margin + 101.0, $curY, $w - 101.0, $rowH);
            $this->SetXY($margin + 101.0, $curY + 0.6);
            $this->MultiCell($w - 101.0, 3.8, $this->sanitizeText($rPrev), 0, 'L');
            $this->SetY($curY + $rowH);
        }

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 4: BUDGETARY REQUIREMENTS (Part II: Sections A & B - Communication)
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();

        $this->SetFont($this->fontFamily, 'B', 12.0);
        $this->Cell($w, 5.5, 'PART II. BUDGETARY REQUIREMENTS', 0, 1, 'L');
        $this->SetFont($this->fontFamily, 'I', 8.5);
        $this->Cell($w, 4.0, '(put N/A in cells if no required budget requirements; each section is dynamic)', 0, 1, 'L');
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($w, 5.0, 'Line - Item Budget', 0, 1, 'L');

        $budgetSections = $proposal->resolved_line_item_budget;

        $bCodeW = 10.0;
        $bPartW = 67.0;
        $bQtyW = 15.0;
        $bUnitW = 18.0;
        $bCostW = 32.0;
        $bAmtW = 38.0;

        $this->SetFont($this->fontFamily, 'B', 8.0);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bCodeW, 5.0, '', 1, 0, 'C', true);
        $this->Cell($bPartW, 5.0, 'Particulars / Item Description', 1, 0, 'L', true);
        $this->Cell($bQtyW, 5.0, 'Qty', 1, 0, 'C', true);
        $this->Cell($bUnitW, 5.0, 'Unit', 1, 0, 'C', true);
        $this->Cell($bCostW, 5.0, 'Unit Cost (P)', 1, 0, 'R', true);
        $this->Cell($bAmtW, 5.0, 'Total Cost (P)', 1, 1, 'R', true);

        // Helper to render section items
        $renderSectionItems = function (array $items) use ($bCodeW, $bPartW, $bQtyW, $bUnitW, $bCostW, $bAmtW, $margin): void {
            if (empty($items)) {
                $items = [['particulars' => 'N/A', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A']];
            }
            $this->SetFont($this->fontFamily, '', 8.0);
            foreach ($items as $item) {
                $part = $item['particulars'] ?? 'N/A';
                $qty = (string) ($item['qty'] ?? 'N/A');
                $unit = (string) ($item['unit'] ?? 'N/A');
                $cost = is_numeric($item['cost'] ?? null) ? number_format((float) $item['cost'], 2) : (string) ($item['cost'] ?? 'N/A');
                $amtVal = $item['amount'] ?? null;
                $amtStr = is_numeric($amtVal) ? number_format((float) $amtVal, 2) : (string) ($amtVal ?? 'N/A');

                $lines = $this->estimateCellLines($part, $bPartW - 6.0);
                $rowH = max(4.5, $lines * 3.6 + 1.0);
                $curY = $this->GetY();

                $this->Cell($bCodeW, $rowH, '', 1, 0, 'C');
                $this->Rect($margin + $bCodeW, $curY, $bPartW, $rowH);
                $this->SetXY($margin + $bCodeW + 3.0, $curY + 0.5);
                $this->MultiCell($bPartW - 3.0, 3.6, $this->sanitizeText($part), 0, 'L');

                $this->SetXY($margin + $bCodeW + $bPartW, $curY);
                $this->Cell($bQtyW, $rowH, $qty, 1, 0, 'C');
                $this->Cell($bUnitW, $rowH, $this->sanitizeText($unit), 1, 0, 'C');
                $this->Cell($bCostW, $rowH, $cost, 1, 0, 'R');
                $this->Cell($bAmtW, $rowH, $amtStr, 1, 1, 'R');
            }
        };

        // Section A: Personal Services
        $this->SetFont($this->fontFamily, 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bCodeW, 5.0, 'A.', 1, 0, 'C', true);
        $this->Cell($w - $bCodeW, 5.0, ' Personal Services', 1, 1, 'L', true);
        $renderSectionItems($budgetSections['A']['items'] ?? [
            ['particulars' => 'Salary', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
            ['particulars' => 'Honoraria', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
        ]);

        // Section B: MOOE Main Header (spans full width - NO TRUNCATION)
        $this->SetFont($this->fontFamily, 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bCodeW, 5.0, 'B.', 1, 0, 'C', true);
        $this->Cell($w - $bCodeW, 5.0, ' Maintenance & Other Operating Expenses (MOOE)', 1, 1, 'L', true);

        // Subcategory: Communication
        $this->SetFont($this->fontFamily, 'B', 8.0);
        $this->SetFillColor(255, 255, 255);
        $this->Cell($bCodeW, 4.5, '', 1, 0, 'C', true);
        $this->Cell($w - $bCodeW, 4.5, '  Communication', 1, 1, 'L', true);
        $renderSectionItems($budgetSections['B_COMM']['items'] ?? []);

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 5: BUDGET CONTINUATION & TIER 1/2 SIGNATURES
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();

        // Continuation Table Header
        $this->SetFont($this->fontFamily, 'B', 8.0);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bCodeW, 5.0, '', 1, 0, 'C', true);
        $this->Cell($bPartW, 5.0, 'Particulars / Item Description', 1, 0, 'L', true);
        $this->Cell($bQtyW, 5.0, 'Qty', 1, 0, 'C', true);
        $this->Cell($bUnitW, 5.0, 'Unit', 1, 0, 'C', true);
        $this->Cell($bCostW, 5.0, 'Unit Cost (P)', 1, 0, 'R', true);
        $this->Cell($bAmtW, 5.0, 'Total Cost (P)', 1, 1, 'R', true);

        $mooeRemaining = [
            'B_TRANS' => 'Transportation & Travel',
            'B_SUPP' => 'Supplies and Materials',
            'B_TRAIN' => 'Trainings & Seminars',
            'B_OTHER' => 'Other Services',
        ];

        foreach ($mooeRemaining as $subKey => $subTitle) {
            $this->SetFont($this->fontFamily, 'B', 8.0);
            $this->SetFillColor(255, 255, 255);
            $this->Cell($bCodeW, 4.5, '', 1, 0, 'C', true);
            $this->Cell($w - $bCodeW, 4.5, '  '.$subTitle, 1, 1, 'L', true);
            $renderSectionItems($budgetSections[$subKey]['items'] ?? []);
        }

        // Section C: Capital Outlay (CO)
        $this->SetFont($this->fontFamily, 'B', 8.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bCodeW, 5.0, 'C.', 1, 0, 'C', true);
        $this->Cell($w - $bCodeW, 5.0, ' Capital Outlay (CO)', 1, 1, 'L', true);
        $renderSectionItems($budgetSections['C']['items'] ?? []);

        // Optional D: Contingency
        $secDItems = $budgetSections['D']['items'] ?? [];
        if (! empty($secDItems)) {
            $this->SetFont($this->fontFamily, 'B', 8.5);
            $this->SetFillColor(241, 245, 249);
            $this->Cell($bCodeW, 5.0, 'D.', 1, 0, 'C', true);
            $this->Cell($w - $bCodeW, 5.0, ' Contingency or Miscellaneous', 1, 1, 'L', true);
            $renderSectionItems($secDItems);
        }

        // Grand Total row
        $this->SetFont($this->fontFamily, 'B', 8.5);
        $this->SetFillColor(226, 232, 240);
        $this->Cell($bCodeW + $bPartW + $bQtyW + $bUnitW + $bCostW, 5.5, 'GRAND TOTAL: ', 1, 0, 'R', true);
        $grandTotalDisplay = $proposal->budget_cash_amount > 0
            ? 'P'.number_format($proposal->budget_cash_amount, 2)
            : 'P0.00';
        $this->Cell($bAmtW, 5.5, $grandTotalDisplay, 1, 1, 'R', true);
        $this->Ln(6.0);

        // ══════════════════════════════════════════════════════════════════════
        // SIGNATURES: PAGE 5 (Tiers 1 & 2: Prepared, Noted, Reviewed)
        // ══════════════════════════════════════════════════════════════════════
        $signatoriesList = CentralizedSignatoryService::getSignatoriesForDocument($proposal);

        $renderSigImage = function (?string $base64Data, float $x, float $y, float $maxW, float $maxH): void {
            if (empty($base64Data)) {
                return;
            }
            try {
                if (str_contains($base64Data, ',')) {
                    $base64Data = explode(',', $base64Data, 2)[1];
                }
                $bin = base64_decode($base64Data);
                if ($bin) {
                    $tmp = tempnam(sys_get_temp_dir(), 'sig_').'.png';
                    file_put_contents($tmp, $bin);
                    $this->Image($tmp, $x, $y, $maxW, $maxH);
                    @unlink($tmp);
                }
            } catch (\Throwable $e) {
                // Ignore image rendering error gracefully
            }
        };

        $sigW = ($w - 12.0) / 2.0;
        $sigItems = $signatoriesList->values();
        $totalSigs = $sigItems->count();
        $i = 0;

        $resolvePos = function ($sig, $idx) {
            $align = strtolower(trim($sig->alignment ?? ''));
            $col = (int) ($sig->position_column ?? 1);
            $label = strtolower(trim(($sig->role_label ?? '').' '.($sig->tier_role ?? '')));

            // Certified as to availability of funds -> paired right column
            if (str_contains($label, 'funds')) {
                return ['align' => 'right', 'col' => 2];
            }
            // Certified as to availability of allotment -> paired left column
            if (str_contains($label, 'allotment')) {
                return ['align' => 'left', 'col' => 1];
            }

            // Explicit left or right alignment from user/model
            if ($align === 'left' || $align === 'right') {
                return ['align' => $align, 'col' => $col ?: ($align === 'right' ? 2 : 1)];
            }

            // Left-aligned institutional tiers (Proponent, Noted, Concurred, Reviewed, Endorsed)
            if (str_contains($label, 'noted') || str_contains($label, 'concurred') || str_contains($label, 'prepared') || str_contains($label, 'reviewed') || str_contains($label, 'endorsed')) {
                return ['align' => 'left', 'col' => 1];
            }

            // Recommending Approval or Approved by -> centered across page
            if (str_contains($label, 'recommend') || str_contains($label, 'approve')) {
                return ['align' => 'center', 'col' => 2];
            }

            if ($align === 'center') {
                return ['align' => 'center', 'col' => 2];
            }

            return ['align' => 'left', 'col' => 1];
        };

        while ($i < $totalSigs) {
            $sig1 = $sigItems[$i];
            $pos1 = $resolvePos($sig1, $i);

            // Check if this is a paired side-by-side row:
            // Sig1 is 'left' and Sig2 exists and is 'right'
            $isPair = false;
            $sig2 = null;
            if ($pos1['align'] === 'left' && ($i + 1 < $totalSigs)) {
                $candidate2 = $sigItems[$i + 1];
                $pos2 = $resolvePos($candidate2, $i + 1);
                if ($pos2['align'] === 'right') {
                    $isPair = true;
                    $sig2 = $candidate2;
                }
            }

            if ($isPair) {
                // Render Two Columns: Sig1 on Left, Sig2 on Right
                $checkPageBreak(36.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $name2 = $sig2->signatory_name ?: ($sig2->employee?->full_name ?? ($sig2->name ?? '—'));
                $title2 = $sig2->designation ?: ($sig2->employee?->designation ?? ($sig2->title ?? ''));
                $rawLabel2 = trim($sig2->role_label ?? ($sig2->role ?? ''));
                $label2 = ! empty($rawLabel2) ? (rtrim($rawLabel2, ':').':') : '';

                // Left Column
                $leftX = $margin;
                $this->SetXY($leftX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label1), 0, 0, 'L');

                // Right Column Label
                $rightX = $margin + $sigW + 12.0;
                $this->SetXY($rightX, $rowY);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label2), 0, 1, 'L');

                // Signature Image space (approx 11mm)
                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $leftX, $rowY + 4.5, 38.0, 10.0);
                }
                if (! empty($sig2->signature_img)) {
                    $renderSigImage($sig2->signature_img, $rightX, $rowY + 4.5, 38.0, 10.0);
                }

                // Name lines
                $nameY = $rowY + 14.0;
                $this->SetXY($leftX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 0, 'L');

                $this->SetXY($rightX, $nameY);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name2)), 0, 1, 'L');

                // Title lines
                $titleY = $nameY + 4.5;
                $this->SetXY($leftX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title1), 0, 'L');
                $endY1 = $this->GetY();

                $this->SetXY($rightX, $titleY);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title2), 0, 'L');
                $endY2 = $this->GetY();

                $this->SetY(max($endY1, $endY2) + 6.0);
                $i += 2;
            } elseif ($pos1['align'] === 'center') {
                // Render Centered Full Row
                $checkPageBreak(36.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $centerW = min(120.0, $w);
                $centerX = $margin + ($w - $centerW) / 2.0;

                $this->SetXY($centerX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                $this->Cell($centerW, 4.5, $this->sanitizeText($label1), 0, 1, 'C');

                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $centerX + ($centerW - 38.0) / 2.0, $rowY + 4.5, 38.0, 10.0);
                }

                $nameY = $rowY + 14.0;
                $this->SetXY($centerX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($centerW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 1, 'C');

                $titleY = $nameY + 4.5;
                $this->SetXY($centerX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($centerW, 3.8, $this->sanitizeText($title1), 0, 'C');

                $this->SetY($this->GetY() + 6.0);
                $i += 1;
            } else {
                // Render Single Left Column (or Right Column)
                $checkPageBreak(36.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $colX = ($pos1['align'] === 'right') ? ($margin + $sigW + 12.0) : $margin;

                $this->SetXY($colX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label1), 0, 1, 'L');

                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $colX, $rowY + 4.5, 38.0, 10.0);
                }

                $nameY = $rowY + 14.0;
                $this->SetXY($colX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 1, 'L');

                $titleY = $nameY + 4.5;
                $this->SetXY($colX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title1), 0, 'L');

                $this->SetY($this->GetY() + 6.0);
                $i += 1;
            }
        }

        // Restore default auto page break
        $this->SetAutoPageBreak(true, 42.0);
    }

    /**
     * Render the official Activity Design body via FPDF.
     */
    private function renderActivityDesignBody(): void
    {
        $design = $this->activityDesign;
        if (! $design) {
            return;
        }

        $proposal = $design->extensionProposal;
        $plan = $design->extensionPlan ?: $proposal?->extensionPlan;

        $this->SetAutoPageBreak(false);
        $margin = $this->leftMargin ?: 20.0;
        $w = $this->contentWidth;
        $maxY = 252.0;

        $checkPageBreak = function (float $neededHeight) use ($maxY): bool {
            if ($this->GetY() + $neededHeight > $maxY) {
                $this->AddPage();
                $this->SetTextColor(17, 24, 39);

                return true;
            }

            return false;
        };

        // ══════════════════════════════════════════════════════════════════════
        // PAGE 1: TITLE & GENERAL INFORMATION
        // ══════════════════════════════════════════════════════════════════════
        $this->AddPage();
        $this->SetTextColor(17, 24, 39);

        // Document Title
        $this->SetFont($this->fontFamily, 'B', 14.0);
        $this->Cell($w, 6.0, 'ACTIVITY DESIGN', 0, 1, 'C');
        $this->Ln(2.0);

        // Document Control Reference Block
        $this->SetFont($this->fontFamily, '', 8.5);
        $this->SetTextColor(100, 116, 139);
        $year = date('Y');
        $refText = "Doc Ref: SNSU-RIE-AD-{$year}   |   Design Code: {$design->design_code}   |   Status: ".strtoupper($design->status ?: 'Draft');
        $this->Cell($w, 4.0, $refText, 'B', 1, 'C');
        $this->Ln(3.0);
        $this->SetTextColor(17, 24, 39);

        // Section I: General Information
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'I. GENERAL INFORMATION', 0, 1, 'L');
        $this->Ln(2.0);

        $labelW = 54.0;
        $valW = $w - $labelW;
        $pFont = 10.0;
        $lineH = 5.0;

        // Activity Title
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Activity Title:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText(strtoupper($design->title)), 0, 'L');
        $this->Ln(1.5);

        // Program / Banner Project
        $banner = $design->banner_program ?: ($proposal?->banner_program ?: ($plan?->program_name ?: 'Institutional Extension Program'));
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Banner Program:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($banner), 0, 'L');
        $this->Ln(1.5);

        // Implementing Unit / College
        $unitName = $design->unit?->unit_name ?: ($proposal?->unit?->unit_name ?: 'College Extension Unit');
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Implementing Unit:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($unitName), 0, 'L');
        $this->Ln(1.5);

        // Partner / Collaborating Agencies
        $partners = $design->collaborating_agencies ?: 'Community Stakeholders / LGU';
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Partner Agencies:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($partners), 0, 'L');
        $this->Ln(1.5);

        // Target Beneficiaries
        $bene = $design->target_beneficiaries ?: ($proposal?->beneficiaries ?: 'Community Members');
        $pax = $design->target_beneficiaries_count ?: ($plan?->target_beneficiaries_count ?: null);
        if ($pax) {
            $bene .= ' ('.number_format((int) $pax).' Pax)';
        }
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Target Beneficiaries:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($bene), 0, 'L');
        $this->Ln(1.5);

        // Implementation Period / Venue
        $period = $design->duration_text ?: ($proposal?->duration_text ?: 'Scheduled Implementation');
        if ($design->project_location) {
            $period .= ' | Venue: '.$design->project_location;
        }
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Period / Venue:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, '', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($period), 0, 'L');
        $this->Ln(1.5);

        // Project Leader
        $lead = $design->project_leader_name.' ('.($design->project_leader_title ?: 'Project Leader').')';
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->Cell($labelW, $lineH, 'Project Leader:', 0, 0, 'L');
        $this->SetFont($this->fontFamily, 'B', $pFont);
        $this->MultiCell($valW, $lineH, $this->sanitizeText($lead), 0, 'L');
        $this->Ln(4.0);

        // ══════════════════════════════════════════════════════════════════════
        // II. CONTEXT, RATIONALE & OBJECTIVES
        // ══════════════════════════════════════════════════════════════════════
        $checkPageBreak(30.0);
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'II. CONTEXT, RATIONALE & OBJECTIVES', 0, 1, 'L');
        $this->Ln(2.0);

        // A. Context & Significance
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->Cell($w, 4.5, 'A. Context & Significance', 0, 1, 'L');
        $this->Ln(1.0);

        $rationale = $design->context_rationale ?: ($proposal?->introduction ?: ($proposal?->relevance_narrative ?: 'This extension activity is undertaken as part of the university mandate to translate technical and academic expertise into meaningful community capacity building.'));
        $this->printFlowingParagraphs($rationale, $w, 5.0, $maxY, $checkPageBreak, 9.5);
        $this->Ln(2.5);

        // B. Objectives
        $checkPageBreak(25.0);
        $this->SetFont($this->fontFamily, 'B', 10.0);
        $this->Cell($w, 4.5, 'B. Activity Objectives', 0, 1, 'L');
        $this->Ln(1.0);

        if (! empty($design->general_objective)) {
            $this->SetFont($this->fontFamily, 'B', 9.5);
            $this->Cell($w, 4.5, 'General Objective: '.$this->sanitizeText($design->general_objective), 0, 1, 'L');
            $this->Ln(1.5);
        }

        $specificObjs = $design->specific_objectives ?: ($proposal?->specific_objectives ?? []);
        if (! empty($specificObjs) && is_array($specificObjs)) {
            $this->SetFont($this->fontFamily, '', 9.5);
            foreach ($specificObjs as $idx => $obj) {
                $checkPageBreak(6.0);
                $objTxt = is_array($obj) ? ($obj['text'] ?? implode(' ', $obj)) : (string) $obj;
                $this->Cell(6.0, 4.5, ($idx + 1).'.', 0, 0, 'R');
                $this->MultiCell($w - 7.0, 4.5, $this->sanitizeText($objTxt), 0, 'L');
                $this->Ln(1.0);
            }
        } elseif ($plan && ! empty($plan->objectives)) {
            $this->printFlowingParagraphs($plan->objectives, $w, 4.8, $maxY, $checkPageBreak, 9.5);
        }
        $this->Ln(4.0);

        // ══════════════════════════════════════════════════════════════════════
        // III. PROGRAM MATRIX & SCHEDULE OF MILESTONES
        // ══════════════════════════════════════════════════════════════════════
        $checkPageBreak(35.0);
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'III. PROGRAM MATRIX & SCHEDULE OF MILESTONES', 0, 1, 'L');
        $this->Ln(2.0);

        $colNumW = 10.0;
        $colDateW = 45.0;
        $colStatW = 32.0;
        $colTitleW = $w - ($colNumW + $colDateW + $colStatW);
        $tblH = 6.0;

        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fontFamily, 'B', 9.0);
        $this->SetTextColor(30, 41, 59);

        $this->Cell($colNumW, $tblH, '#', 1, 0, 'C', true);
        $this->Cell($colTitleW, $tblH, ' Milestone / Activity Component', 1, 0, 'L', true);
        $this->Cell($colDateW, $tblH, 'Target Date', 1, 0, 'C', true);
        $this->Cell($colStatW, $tblH, 'Verification', 1, 1, 'C', true);

        $activities = $design->program_matrix ?: ($plan?->milestones_list ?? []);
        $itemNum = 1;

        foreach ($activities as $act) {
            $titleTxt = $act['title'] ?? 'Scheduled Milestone';
            $dateTxt = $act['target_date'] ?? 'To be scheduled';
            $statusTxt = ! empty($act['is_completed']) ? 'Verified' : (! empty($act['is_na']) ? 'Exempted' : 'Pending');

            $rowLineH = 4.2;
            $linesTitle = $this->calculateCellLines($titleTxt, $colTitleW - 4.0, 9.0, '');
            $rowH = max(6.0, ($linesTitle * $rowLineH) + 2.0);

            $checkPageBreak($rowH);

            $curX = $this->GetX();
            $curY = $this->GetY();

            $this->Rect($curX, $curY, $colNumW, $rowH);
            $this->Rect($curX + $colNumW, $curY, $colTitleW, $rowH);
            $this->Rect($curX + $colNumW + $colTitleW, $curY, $colDateW, $rowH);
            $this->Rect($curX + $colNumW + $colTitleW + $colDateW, $curY, $colStatW, $rowH);

            $this->SetXY($curX, $curY + 1.0);
            $this->SetFont($this->fontFamily, 'B', 8.5);
            $this->Cell($colNumW, $rowLineH, (string) $itemNum, 0, 0, 'C');

            $this->SetXY($curX + $colNumW + 2.0, $curY + 1.0);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->MultiCell($colTitleW - 4.0, $rowLineH, $this->sanitizeText($titleTxt), 0, 'L');

            $this->SetXY($curX + $colNumW + $colTitleW, $curY + 1.0);
            $this->SetFont($this->fontFamily, '', 8.5);
            $this->Cell($colDateW, $rowLineH, $this->sanitizeText($dateTxt), 0, 0, 'C');

            $this->SetXY($curX + $colNumW + $colTitleW + $colDateW, $curY + 1.0);
            $this->SetFont($this->fontFamily, 'B', 8.0);
            $this->Cell($colStatW, $rowLineH, $statusTxt, 0, 0, 'C');

            $this->SetXY($curX, $curY + $rowH);
            $itemNum++;
        }
        $this->Ln(4.0);

        // ══════════════════════════════════════════════════════════════════════
        // IV. BUDGETARY REQUIREMENTS & ALLOCATION
        // ══════════════════════════════════════════════════════════════════════
        $checkPageBreak(25.0);
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'IV. BUDGETARY REQUIREMENTS & ALLOCATION', 0, 1, 'L');
        $this->Ln(2.0);

        $bLabelW = 75.0;
        $bValW = $w - $bLabelW;

        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->SetFillColor(241, 245, 249);
        $this->Cell($bLabelW, 5.5, ' Total Proposed Budget', 1, 0, 'L', true);
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->Cell($bValW, 5.5, ' '.$design->formatted_budget_total, 1, 1, 'L', true);

        $this->SetFont($this->fontFamily, '', 9.0);
        $this->Cell($bLabelW, 5.0, ' SNSU Institutional Share', 1, 0, 'L');
        $this->Cell($bValW, 5.0, ' '.$design->formatted_snsu_share, 1, 1, 'L');

        $this->Cell($bLabelW, 5.0, ' Partner / Counterpart Share', 1, 0, 'L');
        $this->Cell($bValW, 5.0, ' '.($design->formatted_partner_share ?: 'P0.00 (In-kind / Venue)'), 1, 1, 'L');
        $this->Ln(6.0);

        // ══════════════════════════════════════════════════════════════════════
        // V. REVIEW & APPROVAL MATRIX (OFFICIAL SIGNATORIES)
        // ══════════════════════════════════════════════════════════════════════
        $checkPageBreak(60.0);
        $this->SetFont($this->fontFamily, 'B', 11.5);
        $this->Cell($w, 5.0, 'V. REVIEW & APPROVAL MATRIX', 0, 1, 'L');
        $this->Ln(3.0);

        $signatoriesList = CentralizedSignatoryService::getSignatoriesForDocument($design);
        if ($signatoriesList->isEmpty()) {
            ActivityDesignController::syncSignatoriesAndRoutes($design);
            $signatoriesList = CentralizedSignatoryService::getSignatoriesForDocument($design);
        }

        $renderSigImage = function (?string $base64Data, float $x, float $y, float $maxW, float $maxH): void {
            if (empty($base64Data)) {
                return;
            }
            try {
                if (str_contains($base64Data, ',')) {
                    $base64Data = explode(',', $base64Data, 2)[1];
                }
                $bin = base64_decode($base64Data);
                if ($bin) {
                    $tmp = tempnam(sys_get_temp_dir(), 'sig_').'.png';
                    file_put_contents($tmp, $bin);
                    $this->Image($tmp, $x, $y, $maxW, $maxH);
                    @unlink($tmp);
                }
            } catch (\Throwable $e) {
                // Ignore gracefully
            }
        };

        $sigW = ($w - 12.0) / 2.0;
        $sigItems = $signatoriesList->values();
        $totalSigs = $sigItems->count();
        $i = 0;

        $resolvePos = function ($sig) {
            $align = strtolower(trim($sig->alignment ?? ''));
            $col = (int) ($sig->position_column ?? 1);
            $label = strtolower(trim(($sig->role_label ?? '').' '.($sig->tier_role ?? '')));

            // Certified as to availability of funds -> right column
            if (str_contains($label, 'funds')) {
                return ['align' => 'right', 'col' => 2];
            }
            // Certified as to availability of allotment -> left column
            if (str_contains($label, 'allotment')) {
                return ['align' => 'left', 'col' => 1];
            }

            if ($align === 'left' || $align === 'right') {
                return ['align' => $align, 'col' => $col ?: ($align === 'right' ? 2 : 1)];
            }

            if (str_contains($label, 'noted') || str_contains($label, 'concurred') || str_contains($label, 'prepared') || str_contains($label, 'reviewed') || str_contains($label, 'endorsed')) {
                return ['align' => 'left', 'col' => 1];
            }

            if (str_contains($label, 'recommend') || str_contains($label, 'approve') || str_contains($label, 'cao')) {
                return ['align' => 'center', 'col' => 2];
            }

            if ($align === 'center') {
                return ['align' => 'center', 'col' => 2];
            }

            return ['align' => 'left', 'col' => 1];
        };

        while ($i < $totalSigs) {
            $sig1 = $sigItems[$i];
            $pos1 = $resolvePos($sig1);

            // Check if this is a paired side-by-side row (left + right, like allotment + funds)
            $isPair = false;
            $sig2 = null;
            if ($pos1['align'] === 'left' && ($i + 1 < $totalSigs)) {
                $candidate2 = $sigItems[$i + 1];
                $pos2 = $resolvePos($candidate2);
                if ($pos2['align'] === 'right') {
                    $isPair = true;
                    $sig2 = $candidate2;
                }
            }

            if ($isPair) {
                // Render Two Columns: Sig1 on Left, Sig2 on Right
                $checkPageBreak(34.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $name2 = $sig2->signatory_name ?: ($sig2->employee?->full_name ?? ($sig2->name ?? '—'));
                $title2 = $sig2->designation ?: ($sig2->employee?->designation ?? ($sig2->title ?? ''));
                $rawLabel2 = trim($sig2->role_label ?? ($sig2->role ?? ''));
                $label2 = ! empty($rawLabel2) ? (rtrim($rawLabel2, ':').':') : '';

                $leftX = $margin;
                $this->SetXY($leftX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label1), 0, 0, 'L');

                $rightX = $margin + $sigW + 12.0;
                $this->SetXY($rightX, $rowY);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label2), 0, 1, 'L');

                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $leftX, $rowY + 4.5, 38.0, 10.0);
                }
                if (! empty($sig2->signature_img)) {
                    $renderSigImage($sig2->signature_img, $rightX, $rowY + 4.5, 38.0, 10.0);
                }

                $nameY = $rowY + 14.0;
                $this->SetXY($leftX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 0, 'L');

                $this->SetXY($rightX, $nameY);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name2)), 0, 1, 'L');

                $titleY = $nameY + 4.5;
                $this->SetXY($leftX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title1), 0, 'L');
                $endY1 = $this->GetY();

                $this->SetXY($rightX, $titleY);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title2), 0, 'L');
                $endY2 = $this->GetY();

                $this->SetY(max($endY1, $endY2) + 6.0);
                $i += 2;
            } elseif ($pos1['align'] === 'center') {
                // Render Centered Full Row
                $checkPageBreak(34.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $centerW = min(120.0, $w);
                $centerX = $margin + ($w - $centerW) / 2.0;

                $this->SetXY($centerX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                if (! empty($label1)) {
                    $this->Cell($centerW, 4.5, $this->sanitizeText($label1), 0, 1, 'C');
                } else {
                    $this->Ln(4.5);
                }

                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $centerX + ($centerW - 38.0) / 2.0, $rowY + 4.5, 38.0, 10.0);
                }

                $nameY = $rowY + 14.0;
                $this->SetXY($centerX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($centerW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 1, 'C');

                $titleY = $nameY + 4.5;
                $this->SetXY($centerX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($centerW, 3.8, $this->sanitizeText($title1), 0, 'C');

                $this->SetY($this->GetY() + 6.0);
                $i += 1;
            } else {
                // Render Single Left Column (or Right Column)
                $checkPageBreak(34.0);
                $rowY = $this->GetY();

                $name1 = $sig1->signatory_name ?: ($sig1->employee?->full_name ?? ($sig1->name ?? '—'));
                $title1 = $sig1->designation ?: ($sig1->employee?->designation ?? ($sig1->title ?? ''));
                $rawLabel1 = trim($sig1->role_label ?? ($sig1->role ?? ''));
                $label1 = ! empty($rawLabel1) ? (rtrim($rawLabel1, ':').':') : '';

                $colX = ($pos1['align'] === 'right') ? ($margin + $sigW + 12.0) : $margin;

                $this->SetXY($colX, $rowY);
                $this->SetFont($this->fontFamily, 'I', 9.5);
                $this->SetTextColor(55, 65, 81);
                $this->Cell($sigW, 4.5, $this->sanitizeText($label1), 0, 1, 'L');

                if (! empty($sig1->signature_img)) {
                    $renderSigImage($sig1->signature_img, $colX, $rowY + 4.5, 38.0, 10.0);
                }

                $nameY = $rowY + 14.0;
                $this->SetXY($colX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(17, 24, 39);
                $this->Cell($sigW, 4.5, $this->sanitizeText(strtoupper($name1)), 0, 1, 'L');

                $titleY = $nameY + 4.5;
                $this->SetXY($colX, $titleY);
                $this->SetFont($this->fontFamily, '', 9.0);
                $this->SetTextColor(75, 85, 99);
                $this->MultiCell($sigW, 3.8, $this->sanitizeText($title1), 0, 'L');

                $this->SetY($this->GetY() + 6.0);
                $i += 1;
            }
        }

        $this->SetAutoPageBreak(true, 42.0);
    }

    /**
     * Print flowing narrative paragraphs with proper Justify alignment.
     *
     * - Double newlines (\n\n) = paragraph break → rendered as separate MultiCell('J') blocks.
     * - Single newlines within a paragraph are joined as spaces so FPDF receives the full
     *   paragraph text at once and can properly justify every wrapped line.
     * - Lines starting with "- " are treated as list items: rendered with a bullet glyph and
     *   left-aligned so their short length does not look broken by justify.
     */
    private function printFlowingParagraphs(string $text, float $w, float $lineH, float $maxY, callable $checkPageBreak, float $fontSize = 11.0): void
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Split into logical paragraphs on two or more consecutive newlines
        $paragraphs = preg_split('/\n{2,}/', $text);

        $parseListItem = static function (string $line, ?string &$marker = null, ?string &$itemText = null): bool {
            $trimmed = trim($line);
            if (preg_match('/^([-*•])\s+(.*)$/u', $trimmed, $m)) {
                $marker = chr(149);
                $itemText = $m[2];

                return true;
            }
            if (preg_match('/^(\d+[\.\)]|[a-zA-Z][\.\)]|\(\d+\))\s+(.*)$/u', $trimmed, $m)) {
                $marker = $m[1];
                $itemText = $m[2];

                return true;
            }

            return false;
        };

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                $this->Ln(2.5);

                continue;
            }

            // Split paragraph into individual lines to detect list items
            $lines = explode("\n", $paragraph);
            $hasList = false;
            foreach ($lines as $l) {
                if ($parseListItem($l)) {
                    $hasList = true;
                    break;
                }
            }

            if ($hasList) {
                // ── List block: group non-list preamble text, then render each item ──
                $preamble = '';
                $currentItem = null;
                $currentMarker = null;

                $flushItem = function () use (&$currentItem, &$currentMarker, $w, $lineH, &$checkPageBreak, $fontSize): void {
                    if ($currentItem === null) {
                        return;
                    }
                    $itemText = trim($currentItem);
                    $marker = $currentMarker;
                    $currentItem = null;
                    $currentMarker = null;
                    if ($itemText === '') {
                        return;
                    }

                    $margin = $this->lMargin;
                    $isBulletGlyph = ($marker === chr(149));
                    $markerW = $isBulletGlyph ? 5.0 : max(6.5, $this->GetStringWidth((string) $marker) + 2.5);
                    $textW = $w - $markerW;
                    $startX = $margin + $markerW;

                    $checkPageBreak($lineH + 1.5);

                    $this->SetXY($margin, $this->GetY());
                    $this->SetFont($this->fontFamily, '', $fontSize);
                    $this->Cell($markerW, $lineH, (string) $marker, 0, 0, $isBulletGlyph ? 'C' : 'L');
                    $this->printFormattedText($itemText, $textW, $lineH, 'J', $checkPageBreak, $fontSize, $startX);
                    $this->SetX($margin);
                    $this->Ln(0.8);
                };

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }

                    $matchedMarker = null;
                    $matchedText = null;
                    if ($parseListItem($line, $matchedMarker, $matchedText)) {
                        // Flush preamble (text before the list starts)
                        if ($preamble !== '' && $currentItem === null) {
                            $joinedPreamble = preg_replace('/\s{2,}/', ' ', trim($preamble));
                            $this->printFormattedText($joinedPreamble, $w, $lineH, 'J', $checkPageBreak, $fontSize);
                            $this->Ln(1.5);
                            $preamble = '';
                        }
                        // Flush previous item before starting the next
                        $flushItem();
                        $currentMarker = $matchedMarker;
                        $currentItem = $matchedText;
                    } else {
                        if ($currentItem !== null) {
                            // Continuation text within the same list item (e.g. from wrapped line or <br>)
                            $currentItem .= ' '.$line;
                        } else {
                            // Pre-list preamble text
                            $preamble .= ($preamble !== '' ? ' ' : '').$line;
                        }
                    }
                }

                // Flush the last item
                $flushItem();

                // Flush any trailing preamble
                if ($preamble !== '') {
                    $joinedPreamble = preg_replace('/\s{2,}/', ' ', trim($preamble));
                    $this->printFormattedText($joinedPreamble, $w, $lineH, 'J', $checkPageBreak, $fontSize);
                    $this->Ln(1.5);
                }
            } else {
                // ── Regular paragraph: join single newlines into spaces for full justify ──
                $joined = preg_replace('/\n+/', ' ', $paragraph);
                $joined = preg_replace('/\s{2,}/', ' ', $joined);
                $joined = trim($joined);

                $this->printFormattedText($joined, $w, $lineH, 'J', $checkPageBreak, $fontSize);
                $this->Ln(2.0);
            }
        }
    }

    /**
     * Render a text block that may contain <b>, <strong>, <i>, <em> tags,
     * maintaining line-wrapping and Justify/Left alignment.
     */
    private function printFormattedText(
        string $text,
        float $w,
        float $lineH,
        string $align,
        callable $checkPageBreak,
        float $fontSize = 11.0,
        ?float $startX = null
    ): void {
        $clean = preg_replace('/<(?:strong|b)(\s[^>]*)?>/i', '<b>', $text);
        $clean = preg_replace('/<\/(?:strong|b)>/i', '</b>', $clean);
        $clean = preg_replace('/<(?:em|i)(\s[^>]*)?>/i', '<i>', $clean);
        $clean = preg_replace('/<\/(?:em|i)>/i', '</i>', $clean);

        // If there are no HTML formatting tags, fast path
        if (! str_contains($clean, '<b>') && ! str_contains($clean, '<i>')) {
            $rowLines = $this->estimateCellLines($clean, $w);
            $checkPageBreak($rowLines * $lineH + 1.0);
            if ($startX !== null) {
                $this->SetX($startX);
            }
            $this->SetFont($this->fontFamily, '', $fontSize);
            $this->MultiCell($w, $lineH, $this->sanitizeText($clean), 0, $align);

            return;
        }

        // Split into tags and text pieces
        $parts = preg_split('/(<[^>]+>)/', $clean, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $bold = false;
        $italic = false;
        $words = [];

        foreach ($parts as $part) {
            $lower = strtolower($part);
            if ($lower === '<b>') {
                $bold = true;
            } elseif ($lower === '</b>') {
                $bold = false;
            } elseif ($lower === '<i>') {
                $italic = true;
            } elseif ($lower === '</i>') {
                $italic = false;
            } elseif (str_starts_with($part, '<')) {
                continue;
            } else {
                $style = ($bold ? 'B' : '').($italic ? 'I' : '');
                $decoded = html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decoded = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $decoded);

                preg_match_all('/\S+/', $decoded, $matches);
                if (! empty($matches[0])) {
                    foreach ($matches[0] as $wordStr) {
                        $this->SetFont($this->fontFamily, $style, $fontSize);
                        $sanitized = $this->sanitizeText($wordStr);
                        $wordW = $this->GetStringWidth($sanitized);
                        $spaceW = $this->GetStringWidth(' ');
                        $words[] = [
                            'text' => $sanitized,
                            'style' => $style,
                            'w' => $wordW,
                            'spaceW' => $spaceW,
                        ];
                    }
                }
            }
        }

        if (empty($words)) {
            return;
        }

        $lines = [];
        $currentLine = [];
        $currentLineWidth = 0.0;

        foreach ($words as $word) {
            $needed = $word['w'];
            if (! empty($currentLine)) {
                $prev = end($currentLine);
                $needed += $prev['spaceW'];
            }

            if (empty($currentLine) || ($currentLineWidth + $needed <= $w)) {
                $currentLine[] = $word;
                $currentLineWidth += $needed;
            } else {
                $lines[] = $currentLine;
                $currentLine = [$word];
                $currentLineWidth = $word['w'];
            }
        }
        if (! empty($currentLine)) {
            $lines[] = $currentLine;
        }

        $baseX = $startX ?? $this->GetX();

        foreach ($lines as $lineIdx => $lineWords) {
            $checkPageBreak($lineH + 1.0);
            $isLastLine = ($lineIdx === count($lines) - 1);

            $totalWordW = 0.0;
            $spacesCount = count($lineWords) - 1;
            $regularSpaceW = 0.0;
            foreach ($lineWords as $lw) {
                $totalWordW += $lw['w'];
                $regularSpaceW = $lw['spaceW'];
            }
            $totalWithSpaces = $totalWordW + ($spacesCount * $regularSpaceW);

            $extraPerSpace = 0.0;
            if ($align === 'J' && ! $isLastLine && $spacesCount > 0 && $totalWithSpaces < $w) {
                $extraPerSpace = ($w - $totalWithSpaces) / $spacesCount;
            }

            $curX = $baseX;
            $y = $this->GetY();

            foreach ($lineWords as $wIdx => $lw) {
                $this->SetXY($curX, $y);
                $this->SetFont($this->fontFamily, $lw['style'], $fontSize);
                $this->Cell($lw['w'], $lineH, $lw['text'], 0, 0, 'L');
                $curX += $lw['w'];
                if ($wIdx < $spacesCount) {
                    $curX += $lw['spaceW'] + $extraPerSpace;
                }
            }

            $this->SetXY($baseX, $y + $lineH);
        }
    }

    /**
     * Calculate line count for wrapped text in FPDF cell.
     */
    private function calculateCellLines(string $text, float $width, float $fontSize = 8.5, string $style = ''): int
    {
        $this->SetFont($this->fontFamily, $style, $fontSize);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $rawLines = explode("\n", $text);
        $totalLines = 0;

        foreach ($rawLines as $rawLine) {
            $words = preg_split('/\s+/', trim($rawLine));
            if (empty($words) || $words[0] === '') {
                $totalLines++;

                continue;
            }
            $currentLine = '';
            $lineCount = 1;
            foreach ($words as $word) {
                $test = $currentLine === '' ? $word : $currentLine.' '.$word;
                if ($this->GetStringWidth($test) > ($width - 4.0) && $currentLine !== '') {
                    $lineCount++;
                    $currentLine = $word;
                } else {
                    $currentLine = $test;
                }
            }
            $totalLines += $lineCount;
        }

        return max(1, $totalLines);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Shared Signatory Renderer (Policy)
    // ──────────────────────────────────────────────────────────────────────────

    private function renderPolicySignatories(): void
    {
        $tenant = $this->institution?->tenant_id ?? $this->policy->tenant_id ?? 1;
        $docType = $this->policy->document_type;

        $signatories = $this->policy->signatories;

        if ($signatories->isEmpty()) {
            $signatories = PolicySignatory::where('tenant_id', $tenant)
                ->where('is_template', true)
                ->where(function ($q) use ($docType) {
                    $q->whereNull('document_type')
                        ->orWhere('document_type', $docType)
                        ->orWhere('document_type', 'like', '%Policy%');
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        if ($signatories->isNotEmpty()) {
            $this->renderSignatoryGrid($docType, $signatories);

            return;
        }

        $this->renderPolicyLegacySignatory();
    }

    /**
     * Render signatories in a dynamic 12-column CSS-style grid layout.
     * Supports col_span (1-12), alignment (left, center, right), signature line,
     * digital signature image, uppercase bold names, and designations.
     */
    public function render12ColSignatoryBlock(iterable $signatories, ?float $w = null, ?float $margin = null): void
    {
        $w = $w ?? $this->contentWidth;
        $margin = $margin ?? $this->leftMargin;

        if (empty($signatories)) {
            return;
        }

        // Group signatories into rows based on 12-column capacity
        $rows = [];
        $currentRow = [];
        $currentCols = 0;

        foreach ($signatories as $sig) {
            $span = max(1, min(12, (int) ($sig->col_span ?: 6)));
            if ($currentCols + $span > 12 && ! empty($currentRow)) {
                $rows[] = $currentRow;
                $currentRow = [];
                $currentCols = 0;
            }
            $currentRow[] = [
                'sig' => $sig,
                'span' => $span,
                'align' => strtolower($sig->alignment ?: 'left'),
            ];
            $currentCols += $span;
        }
        if (! empty($currentRow)) {
            $rows[] = $currentRow;
        }

        $renderSigImage = function (?string $base64Data, float $x, float $y, float $maxW, float $maxH): void {
            if (empty($base64Data)) {
                return;
            }
            try {
                if (str_contains($base64Data, ',')) {
                    $base64Data = explode(',', $base64Data, 2)[1];
                }
                $bin = base64_decode($base64Data);
                if ($bin) {
                    $tmp = tempnam(sys_get_temp_dir(), 'sig_').'.png';
                    file_put_contents($tmp, $bin);
                    $this->Image($tmp, $x, $y, $maxW, $maxH);
                    @unlink($tmp);
                }
            } catch (\Throwable $e) {
                // Ignore image rendering error gracefully
            }
        };

        foreach ($rows as $row) {
            if ($this->GetY() + 40.0 > $this->GetPageHeight() - $this->bottomMargin) {
                $this->AddPage();
            }

            $rowY = $this->GetY() + 4.0;
            $currentX = $margin;
            $rowMaxEndY = $rowY + 24.0;

            foreach ($row as $item) {
                $sig = $item['sig'];
                $span = $item['span'];
                $align = $item['align'];
                $colWidth = ($span / 12.0) * $w;
                $pad = 2.5;
                $innerW = $colWidth - ($pad * 2);
                $itemX = $currentX + $pad;

                $fpdfAlign = ($align === 'center') ? 'C' : (($align === 'right') ? 'R' : 'L');

                // A. Role Label
                $this->SetXY($itemX, $rowY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(51, 65, 85);
                $rawLabel = trim($sig->role_label ?? '');
                $roleLabel = ($rawLabel === '.' || $rawLabel === '') ? '' : $this->sanitizeText($rawLabel);
                if (! empty($roleLabel)) {
                    $this->Cell($innerW, 4.2, $roleLabel, 0, 1, $fpdfAlign);
                }

                // B. Signature Space Geometry & Placement (No underline line)
                $lineWidth = min(68.0, $innerW);
                $lineX = match ($align) {
                    'center' => $itemX + (($innerW - $lineWidth) / 2.0),
                    'right' => $itemX + $innerW - $lineWidth,
                    default => $itemX,
                };
                $nameY = $rowY + 16.0;

                // C. Digital Signature Image (if signed)
                if (! empty($sig->signature_img)) {
                    $imgW = min(36.0, $lineWidth);
                    $imgH = 11.0;
                    $imgX = $lineX + (($lineWidth - $imgW) / 2.0);
                    $imgY = $nameY - $imgH - 1.0;
                    $renderSigImage($sig->signature_img, $imgX, $imgY, $imgW, $imgH);
                }

                // D. Signatory Name (Bold, Uppercase, Font Size 11.0)
                $this->SetXY($itemX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 11.0);
                $this->SetTextColor(15, 23, 42);
                $name = $sig->signatory_name ?? ($sig->employee_name ?? 'Authorized Signatory');
                $nameText = strtoupper($this->sanitizeText($name));
                $this->MultiCell($innerW, 4.4, $nameText, 0, $fpdfAlign);
                $nameEndY = $this->GetY();

                // E. Designation (Font Size 9.0)
                $desig = $sig->designation ?? '';
                if (! empty($desig)) {
                    $this->SetXY($itemX, $nameEndY);
                    $this->SetFont($this->fontFamily, '', 9.0);
                    $this->SetTextColor(71, 85, 105);
                    $desigText = $this->sanitizeText($desig);
                    $this->MultiCell($innerW, 3.6, $desigText, 0, $fpdfAlign);
                }

                // F. Office / Unit (if present, Font Size 8.5)
                $office = $sig->office ?? '';
                if (! empty($office)) {
                    $this->SetXY($itemX, $this->GetY());
                    $this->SetFont($this->fontFamily, '', 8.5);
                    $this->SetTextColor(100, 116, 139);
                    $this->MultiCell($innerW, 3.4, $this->sanitizeText($office), 0, $fpdfAlign);
                }

                $rowMaxEndY = max($rowMaxEndY, $this->GetY());
                $currentX += $colWidth;
            }

            $this->SetY($rowMaxEndY + 5.0);
        }
    }

    /**
     * Render signatories in a responsive 12-column grid respecting document settings.
     *
     * @param  Collection<int, PolicySignatory>  $signatories
     */
    private function renderSignatoryGrid(string $docType, $signatories): void
    {
        $allSettings = $this->institution?->document_settings ?? [];
        $generic = $allSettings['Generic (Default)'] ?? ($allSettings['Generic'] ?? []);
        $specific = $allSettings[$docType] ?? [];
        $docSettings = array_merge($generic, $specific);

        $numColumns = max(1, (int) ($docSettings['signatory_columns'] ?? 2));
        $signatoryLayout = strtolower($docSettings['signatory_layout'] ?? 'spread');
        $sigGap = (float) ($docSettings['signature_gap'] ?? 9.0);

        $margin = $this->leftMargin;
        $pageW = $this->GetPageWidth();
        $usable = $this->contentWidth;
        $gridColW = $usable / 12.0;

        // Page break guard before starting signatory section
        $maxAllowedY = $this->GetPageHeight() - 75.0;
        if ($this->GetY() > $maxAllowedY) {
            $this->AddPage();
        }

        $this->Ln(3.0);

        // Group signatories into visual rows based on 12-column capacity
        $rows = [];
        $currentRow = [];
        $currentColSum = 0;

        foreach ($signatories->sortBy('sort_order') as $sig) {
            $span = (int) ($sig->col_span ?: ($numColumns > 1 ? round(12 / $numColumns) : 12));
            $span = max(1, min($span, 12));

            if (! empty($currentRow) && ($currentColSum + $span > 12)) {
                $rows[] = $currentRow;
                $currentRow = [];
                $currentColSum = 0;
            }

            $currentRow[] = $sig;
            $currentColSum += $span;
        }

        if (! empty($currentRow)) {
            $rows[] = $currentRow;
        }

        $rowStartY = $this->GetY();

        foreach ($rows as $rowSigs) {
            // Check if this row fits on the current page
            $maxAllowedRowY = $this->GetPageHeight() - 65.0;
            if ($rowStartY > $maxAllowedRowY) {
                $this->AddPage();
                $rowStartY = $this->GetY();
            }

            $countInRow = count($rowSigs);
            $totalRowSpan = 0;
            foreach ($rowSigs as $s) {
                $span = (int) ($s->col_span ?: ($numColumns > 1 ? round(12 / $numColumns) : 12));
                $totalRowSpan += max(1, min($span, 12));
            }

            $maxRowY = $rowStartY;

            // Compute starting X and spacing based on signatory_layout
            if ($countInRow === 1) {
                $sig = $rowSigs[0];
                $span = max(1, min((int) ($sig->col_span ?: ($numColumns > 1 ? round(12 / $numColumns) : 12)), 12));
                $itemW = round($span * $gridColW, 1);

                $itemX = match ($signatoryLayout) {
                    'right' => $margin + $usable - $itemW,
                    'center' => $margin + ($usable - $itemW) / 2.0,
                    'left' => $margin,
                    default => match (strtolower($sig->alignment ?? 'left')) {
                        'right' => $margin + $usable - $itemW,
                        'center' => $margin + ($usable - $itemW) / 2.0,
                        default => ((int) ($sig->position_column ?? 1) > 1) ? $margin + $usable - $itemW : $margin,
                    },
                };

                $rowItemsWithX = [[$sig, $itemX, $itemW]];
            } elseif ($totalRowSpan < 12 && $signatoryLayout === 'spread' && $countInRow === 2) {
                // Two items spread: Left item on the left margin, right item on the right margin
                $sig1 = $rowSigs[0];
                $sig2 = $rowSigs[1];
                $span1 = max(1, min((int) ($sig1->col_span ?: 6), 12));
                $span2 = max(1, min((int) ($sig2->col_span ?: 6), 12));
                $itemW1 = round($span1 * $gridColW, 1);
                $itemW2 = round($span2 * $gridColW, 1);

                $rowItemsWithX = [
                    [$sig1, $margin, $itemW1],
                    [$sig2, $margin + $usable - $itemW2, $itemW2],
                ];
            } else {
                // Multi-item row: sequential placement across 12 grid columns
                $rowItemsWithX = [];
                $runningX = $margin;
                foreach ($rowSigs as $sig) {
                    $span = max(1, min((int) ($sig->col_span ?: ($numColumns > 1 ? round(12 / $numColumns) : 12)), 12));
                    $itemW = round($span * $gridColW, 1);
                    $rowItemsWithX[] = [$sig, $runningX, $itemW];
                    $runningX += $itemW;
                }
            }

            foreach ($rowItemsWithX as [$sig, $itemX, $itemW]) {
                $alignment = strtolower($sig->alignment ?? 'left');
                $alignCell = match ($alignment) {
                    'center' => 'C',
                    'right' => 'R',
                    default => 'L',
                };

                // Role label
                $this->SetXY($itemX, $rowStartY);
                $this->SetFont($this->fontFamily, 'I', 8.5);
                $this->SetTextColor(80, 80, 80);
                $this->Cell($itemW, 4.0, $sig->role_label, 0, 1, $alignCell);

                // Signature space (no underline)
                $nameY = $rowStartY + 4.0 + $sigGap;

                // Official name in formal solid black
                $this->SetXY($itemX, $nameY);
                $this->SetFont($this->fontFamily, 'B', 10.0);
                $this->SetTextColor(0, 0, 0);
                $this->Cell($itemW, 4.8, strtoupper($sig->employee_name), 0, 1, $alignCell);

                // Designation
                if (! empty($sig->designation)) {
                    $this->SetX($itemX);
                    $this->SetFont($this->fontFamily, '', 9.0);
                    $this->SetTextColor(51, 65, 85);
                    $this->Cell($itemW, 4.0, $sig->designation, 0, 1, $alignCell);
                }

                // Office
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

            $rowStartY = $maxRowY + 2.0;
        }

        $this->SetTextColor(0, 0, 0);
        $this->SetY($rowStartY + 2.0);
    }

    private function renderPolicyLegacySignatory(): void
    {
        $allSettings = $this->institution?->document_settings ?? [];
        $docSettings = $allSettings[$this->policy->document_type]
            ?? ($allSettings['Generic (Default)'] ?? []);
        $signatoryLayout = strtolower($docSettings['signatory_layout'] ?? 'right');
        $sigGap = (float) ($docSettings['signature_gap'] ?? 16.0);

        $maxAllowedY = $this->GetPageHeight() - 75.0;
        if ($this->GetY() > $maxAllowedY) {
            $this->AddPage();
        }

        $boxW = 65.0;
        $margin = $this->leftMargin;
        $usable = $this->contentWidth;

        $boxX = match ($signatoryLayout) {
            'left' => $margin,
            'center' => $margin + ($usable - $boxW) / 2.0,
            default => $margin + $usable - $boxW,
        };

        $currentY = $this->GetY();
        $this->SetXY($boxX, $currentY);
        $this->SetFont($this->fontFamily, '', 10.5);
        $this->Cell($boxW, 5.0, 'Sincerely yours,', 0, 1, 'L');

        $tenantId = $this->institution?->tenant_id ?? $this->policy->tenant_id ?? 1;
        $presName = null;
        $presTitle = 'University President';

        if ($this->policy->approvedByUser?->name) {
            $presName = strtoupper($this->policy->approvedByUser->name);
        } else {
            $centralSignatories = CentralizedSignatoryService::getSignatoriesFor('Institutional Policy', $tenantId, null);
            if ($centralSignatories->isEmpty()) {
                $centralSignatories = CentralizedSignatoryService::getSignatoriesFor('Policy', $tenantId, null);
            }
            $targetSig = $centralSignatories->first(fn ($s) => in_array($s->tier_role, ['approved', 'recommending_approval', 'noted']));

            if ($targetSig && ! empty($targetSig->resolved_name)) {
                $presName = strtoupper($targetSig->resolved_name);
                if (! empty($targetSig->resolved_designation)) {
                    $presTitle = $targetSig->resolved_designation;
                }
            } else {
                $signatory = SignatoryMatrix::where('tenant_id', $tenantId)->where('role_title', 'Approved by')->first();
                if ($signatory && ! empty($signatory->employee_name)) {
                    $presName = strtoupper($signatory->employee_name);
                    if (! empty($signatory->designation)) {
                        $presTitle = $signatory->designation;
                    }
                } else {
                    $presName = 'AUTHORIZED SIGNATORY OFFICIAL';
                }
            }
        }

        $this->SetY($currentY + $sigGap);
        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, 'B', 11.0);
        $this->Cell($boxW, 5.0, $presName, 0, 1, 'L');
        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, '', 9.5);
        $this->Cell($boxW, 4.2, $presTitle, 0, 1, 'L');
        $this->SetX($boxX);
        $this->SetFont($this->fontFamily, '', 8.5);
        $this->Cell($boxW, 3.8, $this->institution?->school_name ?: 'Higher Education Institution', 0, 1, 'L');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Shared Utilities
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Render a standard label : value metadata row used in header blocks.
     */
    private function metaRow(
        string $label,
        string $value,
        float $margin,
        float $labelW,
        float $colonW,
        float $valX,
        float $valW,
        float $lineH,
        string $valueFontStyle = '',
        float $valueFontSize = 9.5,
        float $valueLineH = 0.0,
    ): void {
        $y = $this->GetY();
        $this->SetXY($margin, $y);
        $this->SetFont($this->fontFamily, 'B', 9.5);
        $this->Cell($labelW, $lineH, $label, 0, 0, 'L');
        $this->Cell($colonW, $lineH, ':', 0, 0, 'L');
        $this->SetFont($this->fontFamily, $valueFontStyle, $valueFontSize);
        $this->SetLeftMargin($valX);
        $this->SetXY($valX, $y);
        $this->MultiCell($valW, $valueLineH > 0 ? $valueLineH : $lineH, $this->sanitizeText($value), 0, 'L');
        $this->SetLeftMargin($margin);
        $this->SetX($margin);
        $this->Ln(2.0);
    }

    /**
     * Justified paragraph with first-line indent.
     */
    private function writeParagraph(string $text): void
    {
        $indent = '          ';
        $this->SetFont($this->fontFamily, '', 10.0);
        $this->MultiCell($this->contentWidth, $this->lineHeight, $indent.trim($this->sanitizeText($text)), 0, 'J');
        $this->Ln($this->paragraphSpacing);
    }

    /**
     * Convert HTML from contenteditable editors into clean plain text
     * suitable for FPDF MultiCell rendering (Justify alignment).
     * Preserves paragraph breaks, list bullets, and collapses excess whitespace.
     */
    protected function htmlToPlainText(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        // Normalise line endings
        $text = str_replace(["\r\n", "\r"], "\n", $html);

        // Process <ol> lists: number each item
        $text = preg_replace_callback(
            '#<ol(\s[^>]*)?>(.+?)</ol>#is',
            static function (array $m): string {
                $count = 1;
                $items = preg_replace_callback(
                    '#<li(\s[^>]*)?>(.+?)</li>#is',
                    static function (array $li) use (&$count): string {
                        $inner = preg_replace('/[\r\n]+/', ' ', $li[2]);
                        $inner = preg_replace('/\s{2,}/', ' ', $inner);

                        return "\n  ".($count++).'. '.$inner."\n";
                    },
                    $m[2]
                );

                return "\n".$items."\n";
            },
            $text
        );

        // Process <ul> lists: bullet each item
        $text = preg_replace_callback(
            '#<ul(\s[^>]*)?>(.+?)</ul>#is',
            static function (array $m): string {
                $items = preg_replace_callback(
                    '#<li(\s[^>]*)?>(.+?)</li>#is',
                    static function (array $li): string {
                        $inner = preg_replace('/[\r\n]+/', ' ', $li[2]);
                        $inner = preg_replace('/\s{2,}/', ' ', $inner);

                        return "\n  - ".$inner."\n";
                    },
                    $m[2]
                );

                return "\n".$items."\n";
            },
            $text
        );

        // Block-level tags → paragraph separator
        $text = preg_replace('#<(?:p|div|h[1-6]|blockquote)(\s[^>]*)?>\s*#i', '', $text);
        $text = preg_replace('#</(?:p|div|h[1-6]|blockquote)>\s*#i', "\n\n", $text);

        // <br> variants → single newline
        $text = preg_replace('#<br\s*/?>\s*#i', "\n", $text);

        // Standalone <li>/</li> if any remained outside ul/ol
        $text = preg_replace('#</li>\s*#i', "\n", $text);
        $text = preg_replace('#\s*<li(\s[^>]*)?>\s*#i', "\n  - ", $text);

        // Clean up remaining ul/ol wrapper tags
        $text = preg_replace('#</?(?:ul|ol)(\s[^>]*)?>\s*#i', "\n", $text);

        // Standardize bold and italic tags
        $text = preg_replace('#<(?:strong|b)(\s[^>]*)?>#i', '<b>', $text);
        $text = preg_replace('#</(?:strong|b)>#i', '</b>', $text);
        $text = preg_replace('#<(?:em|i)(\s[^>]*)?>#i', '<i>', $text);
        $text = preg_replace('#</(?:em|i)>#i', '</i>', $text);

        // Strip all remaining HTML tags for FPDF compatibility
        $text = strip_tags($text);

        // Decode HTML entities (e.g. &amp; &nbsp; &lt;)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse runs of more than 2 newlines → double newline (paragraph break)
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Remove leading/trailing blank lines
        $text = trim($text);

        return $this->sanitizeText($text);
    }

    /**
     * Strip non-Latin-1 Unicode characters for FPDF compatibility.
     */
    protected function sanitizeText(string $text): string
    {
        return str_replace(
            ["\xE2\x80\x94", "\xE2\x80\x93", "\xE2\x80\xA2", '\u{2022}', '—', '–', '•', "\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", '\u{201C}', '\u{201D}', '\u{201A}', '\u{201E}', '\u{2026}', '\u{2013}', '\u{2014}', '₱', "\xE2\x80\xB1"],
            ['-', '-', '-', '-', '-', '-', '-', "'", "'", '"', '"', '"', '"', ',', '"', '...', '-', '--', 'PHP ', 'PHP '],
            $text
        );
    }

    /**
     * Print roster table header row.
     */
    private function printRosterTableHeader(array $colWidths, array $headers): void
    {
        $this->SetFillColor(241, 245, 249);
        $this->SetDrawColor(30, 41, 59);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fontFamily, 'B', 8.0);
        foreach ($headers as $i => $h) {
            $this->Cell($colWidths[$i], 6.0, $h, 1, 0, in_array($i, [0, 4, 5]) ? 'C' : 'L', true);
        }
        $this->Ln();
    }

    /**
     * Calculate balanced column widths for a table.
     *
     * @return float[]
     */
    private function distributeColumnWidths(int $count, float $totalWidth): array
    {
        return match ($count) {
            2 => [60.0, 110.0],
            3 => [45.0, 65.0, 60.0],
            4 => [45.0, 40.0, 45.0, 40.0],
            5 => [35.0, 35.0, 35.0, 35.0, 30.0],
            default => array_map(fn ($i) => $i < $count - 1 ? round($totalWidth / $count, 1) : $totalWidth - (round($totalWidth / $count, 1) * ($count - 1)), range(0, $count - 1)),
        };
    }

    /**
     * Estimate how many lines a string will wrap to in a given cell width.
     */
    protected function estimateCellLines(string $text, float $cellWidth): int
    {
        $text = trim($text);
        if ($text === '') {
            return 1;
        }

        $usableWidth = max(5.0, $cellWidth - 2.4);
        $paragraphs = explode("\n", str_replace(["\r\n", "\r"], "\n", $text));
        $totalLines = 0;

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                $totalLines++;

                continue;
            }

            $words = preg_split('/\s+/', $para);
            $currentLine = '';
            $paraLines = 1;

            foreach ($words as $word) {
                $wordWidth = $this->GetStringWidth($word);
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
