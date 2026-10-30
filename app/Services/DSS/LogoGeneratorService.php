<?php

namespace App\Services\DSS;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LogoGeneratorService
{
    /**
     * Generate 7 multi-resolution white-label logo versions from an uploaded image.
     *
     * @return array<string, array>
     */
    public function generateVariants(UploadedFile $file, int|string $tenantId): array
    {
        $tenantDir = "tenants/{$tenantId}/branding";
        Storage::disk('public')->makeDirectory($tenantDir);

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $sourcePath = $file->getPathname();

        // Clean up any existing original files to ensure strictly 1 single clean set
        $existingFiles = Storage::disk('public')->files($tenantDir);
        foreach ($existingFiles as $ef) {
            if (str_starts_with(basename($ef), 'original')) {
                Storage::disk('public')->delete($ef);
            }
        }

        // 1. Save original file with fixed name so it never accumulates duplicates
        $originalFileName = 'original.'.$extension;
        $originalPath = $file->storeAs($tenantDir, $originalFileName, 'public');

        // Check if SVG
        if ($extension === 'svg' || str_contains($mime, 'svg')) {
            return $this->handleSvgVariants($file, $tenantDir, $originalPath, $originalFileName);
        }

        // Load image resource via GD
        $sourceGd = $this->createGdResource($sourcePath, $extension);
        if (! $sourceGd) {
            // Fallback: return original file for all slots if GD cannot parse
            return $this->handleFallbackVariants($originalPath, $originalFileName);
        }

        // Standard specifications for the 7 institutional versions
        $specs = [
            'favicon' => [
                'name' => 'Browser Favicon',
                'file_name' => 'favicon-32x32.png',
                'width' => 32,
                'height' => 32,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '32 × 32',
                'purpose' => 'Browser tab icon, bookmark shortcut, and mobile address bar icon.',
            ],
            'app_icon' => [
                'name' => 'PWA / Mobile App Icon',
                'file_name' => 'app-icon-192x192.png',
                'width' => 192,
                'height' => 192,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '192 × 192',
                'purpose' => 'Apple Touch icon, Android manifest launcher, and mobile home screen shortcut.',
            ],
            'sidebar' => [
                'name' => 'Compact Sidebar Mark',
                'file_name' => 'sidebar-compact-120x120.png',
                'width' => 120,
                'height' => 120,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '120 × 120',
                'purpose' => 'Compact sidebar brand badge, collapsed menu icon, and small header mark.',
            ],
            'navbar' => [
                'name' => 'Horizontal Navbar Header',
                'file_name' => 'navbar-header-280x80.png',
                'width' => 280,
                'height' => 80,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '280 × 80',
                'purpose' => 'Primary brand logo for top application navigation bar and header banners.',
            ],
            'master' => [
                'name' => 'Master High-Resolution Logo',
                'file_name' => 'master-512x512.png',
                'width' => 512,
                'height' => 512,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '512 × 512',
                'purpose' => 'High-resolution logo for institutional profile, portal landing, and login screen.',
            ],
            'print_seal' => [
                'name' => 'Official SSR Print Seal (300 DPI)',
                'file_name' => 'print-seal-1024x1024.png',
                'width' => 1024,
                'height' => 1024,
                'aspect' => true,
                'opacity' => 1.0,
                'badge' => '1024 × 1024',
                'purpose' => 'Ultra-crisp seal for A4 & Long SSR binder covers, certificates, and institutional reports.',
            ],
            'watermark' => [
                'name' => 'Security Document Watermark',
                'file_name' => 'watermark-translucent-600x600.png',
                'width' => 600,
                'height' => 600,
                'aspect' => true,
                'opacity' => 0.22,
                'badge' => '600 × 600 (22% Opacity)',
                'purpose' => 'Security watermark stamp for background printing across official documents and PDF exports.',
            ],
        ];

        $variants = [];
        $absoluteDir = Storage::disk('public')->path($tenantDir);

        foreach ($specs as $key => $spec) {
            $destFile = $spec['file_name'];
            $destRelPath = "{$tenantDir}/{$destFile}";
            $destAbsPath = "{$absoluteDir}/{$destFile}";

            $resized = $this->renderResizedCanvas(
                $sourceGd,
                $spec['width'],
                $spec['height'],
                $spec['aspect'],
                $spec['opacity']
            );

            imagepng($resized, $destAbsPath, 9);
            imagedestroy($resized);

            $fileSize = file_exists($destAbsPath) ? filesize($destAbsPath) : 0;

            $variants[$key] = [
                'key' => $key,
                'name' => $spec['name'],
                'file_name' => $destFile,
                'relative_path' => "storage/{$destRelPath}",
                'storage_path' => $destRelPath,
                'url' => asset("storage/{$destRelPath}").'?v='.time(),
                'width' => $spec['width'],
                'height' => $spec['height'],
                'badge' => $spec['badge'],
                'purpose' => $spec['purpose'],
                'size_formatted' => $this->formatBytes($fileSize),
            ];
        }

        imagedestroy($sourceGd);

        // Also add original reference
        $variants['original'] = [
            'key' => 'original',
            'name' => 'Original Uploaded Master',
            'file_name' => $originalFileName,
            'relative_path' => "storage/{$originalPath}",
            'storage_path' => $originalPath,
            'url' => asset("storage/{$originalPath}"),
            'width' => null,
            'height' => null,
            'badge' => 'Original Master',
            'purpose' => 'Raw unprocessed master file uploaded by administrator.',
            'size_formatted' => $this->formatBytes($file->getSize()),
        ];

        return $variants;
    }

    /**
     * Render resized canvas with truecolor alpha channel and optional opacity blend.
     */
    protected function renderResizedCanvas($sourceGd, int $targetW, int $targetH, bool $preserveAspect = true, float $opacity = 1.0)
    {
        $canvas = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        // Fill with complete transparency
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetW, $targetH, $transparent);

        $srcW = imagesx($sourceGd);
        $srcH = imagesy($sourceGd);

        if ($preserveAspect) {
            $ratio = min($targetW / $srcW, $targetH / $srcH);
            $newW = max(1, (int) round($srcW * $ratio));
            $newH = max(1, (int) round($srcH * $ratio));
            $dstX = (int) round(($targetW - $newW) / 2);
            $dstY = (int) round(($targetH - $newH) / 2);
        } else {
            $newW = $targetW;
            $newH = $targetH;
            $dstX = 0;
            $dstY = 0;
        }

        imagecopyresampled($canvas, $sourceGd, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

        // Apply alpha opacity for watermark if specified
        if ($opacity < 1.0) {
            imagealphablending($canvas, false);
            for ($x = 0; $x < $targetW; $x++) {
                for ($y = 0; $y < $targetH; $y++) {
                    $rgba = imagecolorat($canvas, $x, $y);
                    $alpha = ($rgba >> 24) & 0x7F;
                    if ($alpha < 127) {
                        $newAlpha = (int) round(127 - ((127 - $alpha) * $opacity));
                        $newAlpha = max(0, min(127, $newAlpha));
                        $r = ($rgba >> 16) & 0xFF;
                        $g = ($rgba >> 8) & 0xFF;
                        $b = $rgba & 0xFF;
                        $color = imagecolorallocatealpha($canvas, $r, $g, $b, $newAlpha);
                        imagesetpixel($canvas, $x, $y, $color);
                    }
                }
            }
        }

        return $canvas;
    }

    /**
     * Create GD resource from supported image extensions.
     */
    protected function createGdResource(string $path, string $ext)
    {
        return match ($ext) {
            'png' => @imagecreatefrompng($path),
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'webp' => @imagecreatefromwebp($path),
            'gif' => @imagecreatefromgif($path),
            default => @imagecreatefromstring((string) file_get_contents($path)),
        };
    }

    /**
     * Handle SVG uploads (vector graphics).
     */
    protected function handleSvgVariants(UploadedFile $file, string $tenantDir, string $path, string $fileName): array
    {
        $url = asset("storage/{$path}");
        $size = $this->formatBytes($file->getSize());

        $slots = [
            'favicon' => ['name' => 'Browser Favicon', 'badge' => '32 × 32 (SVG)'],
            'app_icon' => ['name' => 'PWA / Mobile App Icon', 'badge' => '192 × 192 (SVG)'],
            'sidebar' => ['name' => 'Compact Sidebar Mark', 'badge' => '120 × 120 (SVG)'],
            'navbar' => ['name' => 'Horizontal Navbar Header', 'badge' => '280 × 80 (SVG)'],
            'master' => ['name' => 'Master High-Resolution Logo', 'badge' => 'Vector SVG'],
            'print_seal' => ['name' => 'Official SSR Print Seal (300 DPI)', 'badge' => 'Vector Scalable'],
            'watermark' => ['name' => 'Security Document Watermark', 'badge' => 'Vector Translucent'],
        ];

        $variants = [];
        foreach ($slots as $key => $s) {
            $variants[$key] = [
                'key' => $key,
                'name' => $s['name'],
                'file_name' => $fileName,
                'relative_path' => "storage/{$path}",
                'storage_path' => $path,
                'url' => $url,
                'width' => null,
                'height' => null,
                'badge' => $s['badge'],
                'purpose' => 'Vector scalable SVG white-label institutional asset.',
                'size_formatted' => $size,
            ];
        }

        $variants['original'] = [
            'key' => 'original',
            'name' => 'Original Vector SVG Master',
            'file_name' => $fileName,
            'relative_path' => "storage/{$path}",
            'storage_path' => $path,
            'url' => $url,
            'width' => null,
            'height' => null,
            'badge' => 'Original SVG',
            'purpose' => 'Raw unprocessed vector master file.',
            'size_formatted' => $size,
        ];

        return $variants;
    }

    /**
     * Fallback variants if image processing is unavailable.
     */
    protected function handleFallbackVariants(string $path, string $fileName): array
    {
        $url = asset("storage/{$path}");
        $slots = ['favicon', 'app_icon', 'sidebar', 'navbar', 'master', 'print_seal', 'watermark', 'original'];
        $variants = [];

        foreach ($slots as $slot) {
            $variants[$slot] = [
                'key' => $slot,
                'name' => ucfirst(str_replace('_', ' ', $slot)),
                'file_name' => $fileName,
                'relative_path' => "storage/{$path}",
                'storage_path' => $path,
                'url' => $url,
                'width' => null,
                'height' => null,
                'badge' => 'Standard',
                'purpose' => 'Uploaded institutional brand asset.',
                'size_formatted' => 'Standard',
            ];
        }

        return $variants;
    }

    /**
     * Format bytes into readable format.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 KB';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / pow(1024, $power), 1).' '.$units[$power];
    }
}
