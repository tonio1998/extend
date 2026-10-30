<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Config;

class UrlIdEncryptor
{
    protected static function getCipherKey(): string
    {
        $appKey = (string) Config::get('app.key', 'base64:defaultDSSKeyForAccreditation2026');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        return hash('sha256', $appKey.':snsu_rie_secure_url_ids', true);
    }

    public static function encrypt(int|string|null $id): string
    {
        if ($id === null || $id === '') {
            return '';
        }

        $key = self::getCipherKey();
        $idStr = (string) $id;

        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($idStr, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);

        if ($ciphertext === false) {
            return (string) $id;
        }

        $signature = hash_hmac('sha256', $iv.$ciphertext, $key, true);
        $shortSig = substr($signature, 0, 4);

        $payload = $iv.$shortSig.$ciphertext;

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    public static function decrypt(int|string|null $token): int|string
    {
        if ($token === null || $token === '') {
            return 0;
        }

        if (is_numeric($token) && (int) $token > 0 && strlen((string) $token) <= 6) {
            return (int) $token;
        }

        $rawBase64 = strtr((string) $token, '-_', '+/');
        $remainder = strlen($rawBase64) % 4;
        if ($remainder) {
            $rawBase64 .= str_repeat('=', 4 - $remainder);
        }

        $payload = base64_decode($rawBase64, true);
        if ($payload === false || strlen($payload) < 21) {
            return is_numeric($token) ? (int) $token : (string) $token;
        }

        $key = self::getCipherKey();
        $iv = substr($payload, 0, 16);
        $shortSig = substr($payload, 16, 4);
        $ciphertext = substr($payload, 20);

        $expectedSig = substr(hash_hmac('sha256', $iv.$ciphertext, $key, true), 0, 4);
        if (! hash_equals($expectedSig, $shortSig)) {
            return is_numeric($token) ? (int) $token : 0;
        }

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            return is_numeric($token) ? (int) $token : 0;
        }

        return is_numeric($decrypted) ? (int) $decrypted : $decrypted;
    }
}
