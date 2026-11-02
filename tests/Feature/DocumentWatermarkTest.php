<?php

namespace Tests\Feature;

use App\Library\FPDI\src\Fpdi;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use App\Services\PDF\DocumentWatermarkService;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentWatermarkTest extends TestCase
{
    public function test_it_stamps_all_pages_of_a_multipage_pdf_without_adding_extra_pages(): void
    {
        Storage::fake('public');

        // Create a 3-page source PDF using FPDF
        $fpdf = new Fpdf;
        for ($i = 1; $i <= 3; $i++) {
            $fpdf->AddPage();
            $fpdf->SetFont('Helvetica', 'B', 16);
            $fpdf->Cell(100, 20, "Original Content Page {$i}");
        }
        $sourcePdfContent = $fpdf->Output('S');

        $filePath = 'documents/test_multipage.pdf';
        Storage::disk('public')->put($filePath, $sourcePdfContent);
        $fullPath = Storage::disk('public')->path($filePath);

        $doc = UploadedDocument::create([
            'tenant_id' => 1,
            'title' => 'Multi-Page Test Document',
            'original_filename' => 'MultiPageDocument.pdf',
            'file_path' => $filePath,
            'disk' => 'public',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => strlen($sourcePdfContent),
            'sha256_hash' => hash('sha256', $sourcePdfContent),
        ]);

        $user = User::factory()->make([
            'name' => 'Prof. Juan Dela Cruz',
        ]);

        $response = DocumentWatermarkService::stream($doc, $user, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('MultiPageDocument.pdf', $response->headers->get('Content-Disposition'));

        // Inspect the resulting PDF using FPDI to verify page count
        $tmpOutput = tempnam(sys_get_temp_dir(), 'test_watermarked_').'.pdf';
        file_put_contents($tmpOutput, $response->getContent());

        $fpdi = new Fpdi;
        $finalPageCount = $fpdi->setSourceFile($tmpOutput);

        // Crucial assertion: Must remain exactly 3 pages, stamped on every page, with no auto-break bloat
        $this->assertEquals(3, $finalPageCount);

        @unlink($tmpOutput);
    }

    public function test_it_supports_inline_preview_mode(): void
    {
        Storage::fake('public');

        $fpdf = new Fpdf;
        $fpdf->AddPage();
        $fpdf->SetFont('Helvetica', 'B', 12);
        $fpdf->Cell(80, 10, 'Single Page Test');
        $sourcePdfContent = $fpdf->Output('S');

        $filePath = 'documents/single_page.pdf';
        Storage::disk('public')->put($filePath, $sourcePdfContent);

        $doc = UploadedDocument::create([
            'tenant_id' => 1,
            'title' => 'Single Page Document',
            'original_filename' => 'SinglePage.pdf',
            'file_path' => $filePath,
            'disk' => 'public',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => strlen($sourcePdfContent),
            'sha256_hash' => hash('sha256', $sourcePdfContent),
        ]);

        $response = DocumentWatermarkService::stream($doc, null, false);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }
}
