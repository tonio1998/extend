<?php

namespace App\Services\PDF\SUCs;

use App\Services\PDF\InstitutionalDocumentTemplate;
use Milon\Barcode\DNS2D;

/**
 * Dedicated PDF Layout and Branding Handler for Surigao del Norte State University (SNSU).
 *
 * Implements the official SNSU document specifications:
 * - Generic: Left SNSU Seal + Tagline + Bagong Pilipinas, Center University Title, Full-width Green Bar.
 * - ISO: Generic layout + Top-Right SOCOTEC ISO 9001 and PAB Accredited QMS certification badges.
 * - Running Footer: DTS QR Code with 12-digit tracking number, Green Bar, Official Tel/Email/URL.
 */
class SnsuDocumentHandler
{
    /** SNSU Brand Green RGB tuple (#15803d) */
    public const GREEN_RGB = [21, 128, 61];

    /**
     * Render the official SNSU Page 1 Header.
     */
    /**
     * Resolve a tenant branding asset from the school's storage folder.
     * Looks in storage/app/public/tenants/{tenant_id}/branding/ first,
     * then public/storage/tenants/{tenant_id}/branding/, then optional fallback.
     */
    public static function resolveTenantImage(InstitutionalDocumentTemplate $pdf, string $filename, ?string $fallbackPublic = null): ?string
    {
        $tenantId = $pdf->institution?->id ?? $pdf->institution?->tenant_id ?? 2;

        $tenantStoragePath = storage_path("app/public/tenants/{$tenantId}/branding/{$filename}");
        if (file_exists($tenantStoragePath)) {
            return $tenantStoragePath;
        }

        $publicStoragePath = public_path("storage/tenants/{$tenantId}/branding/{$filename}");
        if (file_exists($publicStoragePath)) {
            return $publicStoragePath;
        }

        if ($fallbackPublic && file_exists(public_path($fallbackPublic))) {
            return public_path($fallbackPublic);
        }

        return null;
    }

    /**
     * Render the official SNSU Page 1 Header.
     */
    /**
     * Ensure the Mistral tagline PNG exists in tenant branding storage.
     * Generates a high-res transparent PNG with authentic Mistral font if needed.
     */
    public static function ensureMistralTagline(InstitutionalDocumentTemplate $pdf): ?string
    {
        $existing = self::resolveTenantImage($pdf, 'tagline-mistral.png');
        if ($existing && file_exists($existing)) {
            return $existing;
        }

        $tenantId = $pdf->institution?->id ?? $pdf->institution?->tenant_id ?? 2;
        $dest = storage_path("app/public/tenants/{$tenantId}/branding/tagline-mistral.png");
        $font = 'C:/Windows/Fonts/MISTRAL.TTF';

        if (function_exists('imagettftext') && file_exists($font)) {
            try {
                $text = '"For Nation\'s Greater Heights"';
                $fontSize = 42;
                $bbox = imagettfbbox($fontSize, 0, $font, $text);
                $minX = min([$bbox[0], $bbox[2], $bbox[4], $bbox[6]]);
                $maxX = max([$bbox[0], $bbox[2], $bbox[4], $bbox[6]]);
                $minY = min([$bbox[1], $bbox[3], $bbox[5], $bbox[7]]);
                $maxY = max([$bbox[1], $bbox[3], $bbox[5], $bbox[7]]);

                $textW = $maxX - $minX;
                $textH = $maxY - $minY;
                $padding = 20;
                $im = imagecreatetruecolor($textW + ($padding * 2), $textH + ($padding * 2));
                imagesavealpha($im, true);
                $trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagefill($im, 0, 0, $trans);

                [$r, $g, $b] = self::GREEN_RGB;
                $green = imagecolorallocate($im, $r, $g, $b);
                imagettftext($im, $fontSize, 0, $padding - $minX, $padding - $minY, $green, $font, $text);

                @mkdir(dirname($dest), 0755, true);
                imagepng($im, $dest);
                imagedestroy($im);

                return $dest;
            } catch (\Throwable $e) {
                // Silently fallback if generation fails
            }
        }

        return null;
    }

    /**
     * Render the official SNSU Page 1 Header.
     */
    public static function renderHeader(InstitutionalDocumentTemplate $pdf): void
    {
        $leftMargin = 12.0;
        $rightMargin = 12.0;
        $pageW = $pdf->GetPageWidth();
        $isLandscape = $pageW > $pdf->GetPageHeight();
        $startY = 8.0;

        $isIso = in_array(strtolower((string) $pdf->headerMode), [
            'iso',
            'iso_form',
        ], true);

        // 1. Official SNSU Seal (23.5mm standard)
        $sealW = 23.5;
        $sealH = 23.5;
        $sealPath = self::resolveTenantImage($pdf, 'print-seal-1024x1024.png')
            ?: ($pdf->getSealPath() ?: self::resolveTenantImage($pdf, 'master-512x512.png'));
        if ($sealPath && file_exists($sealPath)) {
            $pdf->Image($sealPath, $leftMargin, $startY, $sealW, $sealH);
        }

        // Tagline under seal: "For Nation's Greater Heights" (Authentic Mistral font)
        $tagY = $startY + $sealH + 0.8;
        $taglineImg = self::ensureMistralTagline($pdf);
        if ($taglineImg && file_exists($taglineImg)) {
            $tagW = 25.5;
            $tagH = 4.0;
            $pdf->Image($taglineImg, $leftMargin - 1.0, $tagY, $tagW, $tagH);
        } else {
            $pdf->SetXY($leftMargin - 1.0, $tagY);
            $pdf->SetFont($pdf->fontFamily, 'I', 7.0);
            [$r, $g, $b] = self::GREEN_RGB;
            $pdf->SetTextColor($r, $g, $b);
            $pdf->Cell(25.5, 3.8, '"For Nation\'s Greater Heights"', 0, 0, 'C');
        }

        // 2. Bagong Pilipinas Logo (24.5mm)
        $bpW = 24.5;
        $bpH = 24.5;
        $bpX = $leftMargin + $sealW + 2.0;
        $bpPath = self::resolveTenantImage($pdf, 'bagong_pilipinas.png', 'images/bagong_pilipinas.png');
        if ($bpPath && file_exists($bpPath)) {
            $pdf->Image($bpPath, $bpX, $startY + 1.2, $bpW, $bpH);
        }

        // 3. Top-Right Badges (Combined SOCOTEC ISO 9001 & PAB Accredited QMS)
        $rightOffset = 0.0;
        if ($isIso) {
            $isoBoxW = 44.0;
            $isoStartX = $pageW - $rightMargin - $isoBoxW;
            $rightOffset = $isoBoxW;

            $tblW = $isoBoxW;
            $tblX = $isoStartX;
            $tblY = $startY + 4.5;
            $rowH = 3.5;
            $labelW = 22.0;
            $valueW = $tblW - $labelW;

            [$gr, $gg, $gb] = self::GREEN_RGB;
            $pdf->SetDrawColor(80, 80, 80);
            $pdf->SetLineWidth(0.25);

            $rows = [
                ['Document No.', $pdf->docNo ?: '—'],
                ['Revision No.', $pdf->revNo ?: '0'],
                ['Effectivity',  $pdf->docEffectiveDate ?: ($pdf->effectivityDate ?: '—')],
                ['Page',         $pdf->PageNo().' of {nb}'],
            ];

            foreach ($rows as $row) {
                [$label, $value] = $row;
                $pdf->SetXY($tblX, $tblY);
                $pdf->SetFillColor($gr, $gg, $gb);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont($pdf->fontFamily, 'B', 6.0);
                $pdf->Cell($labelW, $rowH, $label, 1, 0, 'L', true);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(30, 30, 30);
                $pdf->SetFont($pdf->fontFamily, '', 6.0);
                $pdf->Cell($valueW, $rowH, $value, 1, 1, 'L', true);
                $tblY += $rowH;
            }
            $pdf->SetFillColor(255, 255, 255);
        } else {
            // Combined Certification Badges: SOCOTEC ISO 9001 + PAB Accredited QMS (32.0mm x 21.0mm)
            $badgePath = self::resolveTenantImage($pdf, 'pab_accredited.jpg', 'images/pab_accredited.jpg')
                ?: self::resolveTenantImage($pdf, 'pab_accredited.png', 'images/pab_accredited.png');

            $badgeW = 32.0;
            $badgeH = round($badgeW * (1181 / 1799), 2); // = 21.0mm
            $badgeX = $pageW - $rightMargin - $badgeW;
            $badgeY = $startY + 4.0;

            if ($badgePath && file_exists($badgePath)) {
                $pdf->Image($badgePath, $badgeX, $badgeY, $badgeW, $badgeH);
                $rightOffset += $badgeW;
            }
        }

        // 4. Center University Header Text (Prominent, cleanly separated from logos & badges)
        $textX = $bpX + $bpW + 2.8;
        $badgeStartX = $pageW - $rightMargin - $rightOffset;
        $textW = max(80.0, $badgeStartX - $textX - 2.0);

        $textY = $startY + 7.2;
        $pdf->SetXY($textX, $textY);
        $pdf->SetFont($pdf->fontFamily, '', 8.5);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell($textW, 4.2, 'Republic of the Philippines', 0, 1, 'L');

        $pdf->SetX($textX);
        $pdf->SetFont($pdf->fontFamily, 'B', 11.0);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell($textW, 5.2, 'SURIGAO DEL NORTE STATE UNIVERSITY', 0, 1, 'L');

        $pdf->SetX($textX);
        $pdf->SetFont($pdf->fontFamily, '', 8.0);
        $pdf->SetTextColor(100, 116, 139);
        $address = $pdf->institution?->address ?: 'Narciso Street, Surigao City, Surigao del Norte 8400';
        $pdf->Cell($textW, 3.8, $address, 0, 1, 'L');

        // 5. Full-width SNSU Green Divider (Thick horizontal bar)
        $dividerY = $tagY + 4.0 + 2.2;
        [$r, $g, $b] = self::GREEN_RGB;
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetLineWidth(1.4);
        $pdf->Line($leftMargin, $dividerY, $pageW - $rightMargin, $dividerY);

        // Reset state for document body
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetY($dividerY + 5.0);
    }

    /**
     * Render SNSU Continuation Page Header — same full template as page 1.
     */
    public static function renderContinuationHeader(InstitutionalDocumentTemplate $pdf): void
    {
        self::renderHeader($pdf);
    }

    /**
     * Render the official SNSU Footer.
     */
    public static function renderFooter(InstitutionalDocumentTemplate $pdf): void
    {
        $pageW = $pdf->GetPageWidth();
        $pageH = $pdf->GetPageHeight();
        $leftMargin = 12.0;
        $rightMargin = 12.0;
        $footerY = $pageH - 18.0;

        // DTS PDF417 Barcode (Bottom-right, above divider — compact 2D stacked format)
        $bcW = 70.0;   // wider PDF417 barcode
        $bcH = 14.0;
        $bcX = $pageW - $rightMargin - $bcW;
        $bcY = $footerY - $bcH - 4.0;

        $rawQr = $pdf->formattedQr ?: ($pdf->trackingNumber ?: ($pdf->documentId ?: '0'));
        $formattedQrText = function_exists('formatQR12Digits')
            ? formatQR12Digits($rawQr)
            : (is_numeric($rawQr) ? str_pad((string) $rawQr, 12, '0', STR_PAD_LEFT) : (string) $rawQr);

        try {
            $d2 = new DNS2D;
            $pngBase64 = $d2->getBarcodePNG($formattedQrText, 'PDF417', 3, 3);
            if ($pngBase64) {
                $tmpBc = tempnam(sys_get_temp_dir(), 'snsu_bc_').'.png';
                file_put_contents($tmpBc, base64_decode($pngBase64));
                $pdf->Image($tmpBc, $bcX, $bcY, $bcW, $bcH);
                @unlink($tmpBc);

                // Tracking number below — Courier monospace, grouped 4-4-4
                $grouped = implode(' ', str_split($formattedQrText, 4));
                $pdf->SetXY($bcX, $bcY + $bcH + 0.8);
                $pdf->SetFont($pdf->fontFamily, '', 7.0);
                $pdf->SetTextColor(30, 41, 59);
                $pdf->Cell($bcW, 3.0, $grouped, 0, 0, 'C');
            }
        } catch (\Throwable $e) {
            // Silently skip if barcode engine fails
        }

        // SNSU Green Horizontal Line (Thick bar matching header)
        [$r, $g, $b] = self::GREEN_RGB;
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetLineWidth(1.4);
        $pdf->Line($leftMargin, $footerY, $pageW - $rightMargin, $footerY);

        // Footer Contact Details (Below Green Line)
        $pdf->SetXY($leftMargin, $footerY + 2.6);
        $pdf->SetFont($pdf->fontFamily, 'I', 8.2);
        $pdf->SetTextColor(71, 85, 105);
        $phone = $pdf->institution?->contact_phone ?: '(086) 827-3741';
        $pdf->Cell(80.0, 4.2, 'Tel. Nos.: '.$phone, 0, 0, 'L');

        // Right Contact Info (Email & URL)
        $email = $pdf->institution?->contact_email ?: 'info@snsu.edu.ph';
        $url = $pdf->institution?->website_url ? preg_replace('#^https?://#', '', $pdf->institution->website_url) : 'snsu.edu.ph';

        $pdf->SetXY($pageW - $rightMargin - 85.0, $footerY + 2.2);
        $pdf->SetFont($pdf->fontFamily, '', 7.6);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell(85.0, 3.6, 'Email: '.$email, 0, 1, 'R');

        $pdf->SetX($pageW - $rightMargin - 85.0);
        $pdf->Cell(85.0, 3.6, 'URL: '.$url, 0, 1, 'R');

        // Reset
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
    }
}
