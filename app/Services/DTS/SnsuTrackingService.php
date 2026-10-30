<?php

namespace App\Services\DTS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SnsuTrackingService
{
    /**
     * Get the configured SNSU DTS API Base URL.
     */
    public static function getBaseUrl(): string
    {
        return rtrim((string) config('services.snsu_dts.base_url', env('SNSU_DTS_API_BASE_URL', 'http://uis.dev.com')), '/');
    }

    /**
     * Fetch document live status & routing trail from SNSU DTS API.
     *
     * @return array{tracking_number: string, document_id: ?string, title: ?string, trail_status: string, doc_status: string, current_location: string, destination: string, trail: array}|null
     */
    public static function fetchTracking(?string $trackingNumber): ?array
    {
        if (empty($trackingNumber)) {
            return null;
        }

        $clean = trim((string) $trackingNumber);
        if ($clean === '' || str_starts_with($clean, 'DTS-TMP-')) {
            return null;
        }

        try {
            $baseUrl = self::getBaseUrl();
            $apiKey = config('services.snsu_dts.api_key');

            $request = Http::timeout(6)->withoutVerifying();
            if (! empty($apiKey)) {
                $request = $request->withHeaders([
                    'X-API-KEY' => $apiKey,
                    'Authorization' => "Bearer {$apiKey}",
                ]);
            }

            // Call the public tracking gateway endpoint
            $response = $request->get("{$baseUrl}/api/gateway/dts/track/{$clean}");

            if ($response->successful()) {
                $json = $response->json();
                if (! empty($json['success']) && isset($json['data'])) {
                    return $json['data'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("SnsuTrackingService: Failed to fetch tracking for '{$clean}': {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Associate an official SNSU Tracking Number with an Extension Proposal or Plan.
     */
    public static function attachTrackingNumber(Model $document, string $trackingNumber): array
    {
        $clean = trim($trackingNumber);
        if ($clean === '') {
            return ['success' => false, 'message' => 'Tracking number cannot be empty.'];
        }

        // Verify tracking number against live SNSU DTS
        $remoteData = self::fetchTracking($clean);
        $officialTracking = $remoteData['tracking_number'] ?? $clean;

        if (Schema::hasColumn($document->getTable(), 'tracking_number')) {
            $document->update([
                'tracking_number' => $officialTracking,
            ]);
        } else {
            $meta = $document->metadata ?? [];
            $meta['tracking_number'] = $officialTracking;
            $document->metadata = $meta;
            $document->save();
        }

        if (method_exists($document, 'recordAuditTrail')) {
            $document->recordAuditTrail(
                'SNSU Tracking Connected',
                "Linked official SNSU Central DTS Tracking Number: {$officialTracking}".($remoteData ? " (Current Status: {$remoteData['trail_status']})" : ''),
                auth()->user()
            );
        }

        return [
            'success' => true,
            'tracking_number' => $officialTracking,
            'remote_data' => $remoteData,
            'message' => "Successfully connected tracking number {$officialTracking}.",
        ];
    }
}
