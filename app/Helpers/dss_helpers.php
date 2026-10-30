<?php

use App\Services\Security\UrlIdEncryptor;

if (! function_exists('dss_encrypt_id')) {
    /**
     * Encrypt an ID into a URL-safe token.
     */
    function dss_encrypt_id(int|string|null $id): string
    {
        return UrlIdEncryptor::encrypt($id);
    }
}

if (! function_exists('dss_decrypt_id')) {
    /**
     * Decrypt a URL-safe token back into an ID.
     */
    function dss_decrypt_id(int|string|null $token): int|string
    {
        return UrlIdEncryptor::decrypt($token);
    }
}

if (! function_exists('dss_image_base64')) {
    /**
     * Convert an image file path to base64 data URI for reliable PDF rendering.
     */
    function dss_image_base64(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $fullPath = public_path(ltrim($path, '/\\'));
        if (! file_exists($fullPath)) {
            $fullPath = $path;
        }

        if (! file_exists($fullPath)) {
            return null;
        }

        $mime = mime_content_type($fullPath) ?: 'image/png';
        $data = file_get_contents($fullPath);

        return 'data:'.$mime.';base64,'.base64_encode($data);
    }
}

if (! function_exists('formatQR12Digits')) {
    /**
     * Standard 12-digit QR Code formatting matching uisfix standard.
     */
    function formatQR12Digits(int|string|null $qr): string
    {
        if (empty($qr) || $qr === '0') {
            return '';
        }
        if (is_numeric($qr) && strlen((string) $qr) < 12) {
            return str_pad((string) $qr, 12, '0', STR_PAD_LEFT);
        }

        return (string) $qr;
    }
}
