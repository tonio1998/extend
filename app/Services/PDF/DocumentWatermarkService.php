<?php

namespace App\Services\PDF;

use App\Library\FPDI\src\Fpdi;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentWatermarkService
{
    /**
     * Generate and stream a watermarked PDF for download or inline preview.
     * Features an authentic Institutional Archival Document Control Security Strip
     * applied across every single page.
     */
    public static function stream(UploadedDocument $doc, ?User $user = null, bool $isDownload = true): Response|StreamedResponse|BinaryFileResponse
    {
        $filename = $doc->original_filename;
        $inst = TenantContext::getInstitution();
        $schoolName = $inst?->school_name ?: 'Surigao del Norte State University';
        $fullPath = Storage::disk($doc->disk)->path($doc->file_path);

        // If not PDF, stream original file directly
        if (strtolower($doc->extension ?: pathinfo($filename, PATHINFO_EXTENSION)) !== 'pdf') {
            return $isDownload
                ? Storage::disk($doc->disk)->download($doc->file_path, $filename)
                : response()->file($fullPath, ['Content-Type' => $doc->mime_type ?: 'application/octet-stream']);
        }

        try {
            $pdf = new Fpdi;
            // CRITICAL: Disable auto page breaks so drawing within the bottom margin
            // does NOT trigger phantom page breaks across imported pages.
            $pdf->SetAutoPageBreak(false);

            $pageCount = $pdf->setSourceFile($fullPath);

            $userName = $user?->name ?: ($doc->uploader_name ?: 'Authorized Personnel');
            $hash = $doc->sha256_hash ?: (file_exists($fullPath) ? hash_file('sha256', $fullPath) : null);
            $actionLabel = $isDownload ? 'ARCHIVAL DOWNLOAD COPY' : 'OFFICIAL ARCHIVAL PREVIEW';
            $tz = config('app.timezone', 'Asia/Manila');
            $timestamp = now()->timezone($tz)->format('M d, Y h:i:s A');

            // Apply institutional security stamp on EVERY page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                $pageW = $size['width'];
                $pageH = $size['height'];

                // ─────────────────────────────────────────────────────────────
                // INSTITUTIONAL ARCHIVAL SECURITY STRIP (Authentic SUC / ISO Layout)
                // ─────────────────────────────────────────────────────────────
                $margin = 7.0; // mm from page edges
                $stripW = $pageW - ($margin * 2);
                $stripH = 10.0; // Prominent, highly legible 10.0mm height
                $stripX = $margin;
                $stripY = $pageH - $stripH - 3.5; // 3.5mm above bottom edge

                // 1. Opaque White Background (ensures legibility over any underlying document bleed)
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetDrawColor(203, 213, 225); // Slate 300 hairline
                $pdf->SetLineWidth(0.2);
                $pdf->Rect($stripX, $stripY, $stripW, $stripH, 'DF');

                // 2. Institutional University Green Indicator Pip on Left Edge
                $pdf->SetFillColor(0, 103, 79); // SNSU Deep Emerald #00674f
                $pdf->Rect($stripX, $stripY, 2.0, $stripH, 'F');

                // 3. Row 1: Institutional Authority & Page Sequence (Prominent 8.5pt / 8.0pt)
                $pdf->SetXY($stripX + 3.2, $stripY + 0.8);
                $pdf->SetFont('Helvetica', 'B', 8.5);
                $pdf->SetTextColor(15, 23, 42); // Dark Slate #0f172a
                $leftHeader = self::safeText(mb_strtoupper($schoolName));
                $pdf->Cell($stripW * 0.60, 4.2, $leftHeader, 0, 0, 'L');

                $pdf->SetFont('Helvetica', 'B', 8.0);
                $pdf->SetTextColor(0, 103, 79); // Emerald #00674f
                $rightHeader = self::safeText("PAGE {$pageNo} OF {$pageCount}  |  {$actionLabel}");
                $pdf->Cell($stripW * 0.40 - 4.0, 4.2, $rightHeader, 0, 0, 'R');

                // 4. Subtle Hairline Divider between Row 1 and Row 2
                $pdf->SetDrawColor(226, 232, 240); // Slate 200
                $pdf->SetLineWidth(0.12);
                $pdf->Line($stripX + 3.2, $stripY + 5.2, $stripX + $stripW - 1.5, $stripY + 5.2);

                // 5. Row 2: Cryptographic Checksum & Access Audit Trail (Legible 7.0pt)
                $pdf->SetXY($stripX + 3.2, $stripY + 5.5);
                $pdf->SetFont('Courier', 'B', 7.0);
                $pdf->SetTextColor(30, 41, 59); // Slate 800 - crisp, high-contrast monospace
                $hashDisplay = self::safeText($hash ? 'SHA-256: '.$hash : 'INTEGRITY: TAMPER-EVIDENT ARCHIVAL COPY');
                $pdf->Cell($stripW * 0.58, 3.8, $hashDisplay, 0, 0, 'L');

                $pdf->SetFont('Helvetica', '', 7.0);
                $pdf->SetTextColor(71, 85, 105); // Slate 600
                $auditTrail = self::safeText("RETRIEVED: {$timestamp}  |  USER: ".mb_strtoupper($userName));
                $pdf->Cell($stripW * 0.42 - 4.0, 3.8, $auditTrail, 0, 0, 'R');
            }

            $output = $pdf->Output('S');
            $disposition = $isDownload ? 'attachment' : 'inline';

            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "{$disposition}; filename=\"".addslashes($filename).'"',
                'Cache-Control' => 'private, no-transform, no-store, must-revalidate',
            ]);
        } catch (Exception $e) {
            Log::warning('PDF Watermark failed, falling back to original: '.$e->getMessage(), [
                'doc_id' => $doc->id,
            ]);

            return $isDownload
                ? Storage::disk($doc->disk)->download($doc->file_path, $filename)
                : response()->file($fullPath, ['Content-Type' => 'application/pdf']);
        }
    }

    /**
     * Clean and encode strings for reliable FPDF Latin-1 rendering.
     */
    protected static function safeText(?string $text): string
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
            "\xE2\x80\xA2" => '|',   // bullet to clean pipe
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
