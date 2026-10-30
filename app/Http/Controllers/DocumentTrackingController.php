<?php

namespace App\Http\Controllers;

use App\Models\Dts\Records;
use App\Models\IPM\ProcessWorkflow;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;

class DocumentTrackingController extends Controller
{
    /**
     * Show Document Tracker page (supports search by tracking number or QR)
     */
    public function showTracker(Request $request, ?string $trackingNumber = null)
    {
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        $searchQuery = $trackingNumber ?: $request->get('tracking_no');
        $workflow = null;
        $dtsRecord = null;

        if ($searchQuery) {
            $formattedSearch = formatQR12Digits($searchQuery) ?: $searchQuery;
            $rawId = is_numeric($searchQuery) ? (string) (int) $searchQuery : (string) (int) ltrim($searchQuery, '0');

            $dtsRecord = Records::where('QRCODE', $searchQuery)
                ->orWhere('QRCODE', $formattedSearch)
                ->orWhere('QRCODE', $rawId)
                ->orWhere('id', $rawId)
                ->orWhere('DocumentID', $searchQuery)
                ->orWhere('DocumentID', 'like', "%{$searchQuery}%")
                ->orWhere('ConnectQR', $searchQuery)
                ->with(['logs.cour', 'creator', 'department'])
                ->first();

            $workflow = ProcessWorkflow::where('tracking_number', $searchQuery)
                ->orWhere('tracking_number', $formattedSearch)
                ->orWhere('tracking_number', $rawId)
                ->orWhere('tracking_number', 'like', "%{$searchQuery}%")
                ->orWhere('qr_code', $searchQuery)
                ->orWhere('qr_code', $formattedSearch)
                ->orWhere('qr_code', $rawId)
                ->with(['campus', 'department', 'user', 'steps.actedByUser'])
                ->first();
        }

        // Quick sample documents for instant demo clicking
        $recentTrackableDocs = ProcessWorkflow::latest()->take(4)->get();
        $recentDtsRecords = Records::latest()->take(4)->get();

        return view('tracking.index', compact('institution', 'theme', 'searchQuery', 'workflow', 'dtsRecord', 'recentTrackableDocs', 'recentDtsRecords'));
    }

    /**
     * API lookup for QR scanners & AJAX
     */
    public function trackApi(string $trackingNumber)
    {
        $formattedSearch = formatQR12Digits($trackingNumber) ?: $trackingNumber;
        $rawId = is_numeric($trackingNumber) ? (string) (int) $trackingNumber : (string) (int) ltrim($trackingNumber, '0');

        $dtsRecord = Records::where('QRCODE', $trackingNumber)
            ->orWhere('QRCODE', $formattedSearch)
            ->orWhere('QRCODE', $rawId)
            ->orWhere('id', $rawId)
            ->orWhere('DocumentID', $trackingNumber)
            ->orWhere('DocumentID', 'like', "%{$trackingNumber}%")
            ->orWhere('ConnectQR', $trackingNumber)
            ->with(['logs.cour', 'creator', 'department'])
            ->first();

        $workflow = ProcessWorkflow::where('tracking_number', $trackingNumber)
            ->orWhere('tracking_number', $formattedSearch)
            ->orWhere('tracking_number', $rawId)
            ->orWhere('qr_code', $trackingNumber)
            ->orWhere('qr_code', $formattedSearch)
            ->orWhere('qr_code', $rawId)
            ->with(['campus', 'department', 'user', 'steps.actedByUser'])
            ->first();

        if (! $workflow && ! $dtsRecord) {
            return response()->json([
                'result' => false,
                'message' => "Document with tracking number '{$trackingNumber}' was not found in institutional records.",
            ], 404);
        }

        return response()->json([
            'result' => true,
            'data' => $workflow,
            'dts_record' => $dtsRecord,
        ]);
    }
}
