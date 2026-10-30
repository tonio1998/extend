<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Services\DSS\TenantContext;
use App\Services\DTS\SnsuTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExtensionTrackingController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        abort_unless(
            auth()->user()?->can('rie.tracking.view') || auth()->user()?->can('rie.tracking.manage'),
            403,
            'Unauthorized to view Extension Tracking Hub.'
        );

        if ($request->ajax()) {
            return $this->data($request);
        }

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $plansQuery = ExtensionPlan::where('tenant_id', $tenantId);
        $totalPlans = (clone $plansQuery)->count();
        $plansConnected = (clone $plansQuery)->whereNotNull('tracking_number')->where('tracking_number', '!=', '')->count();
        $plansMissing = $totalPlans - $plansConnected;

        $proposalsQuery = ExtensionProposal::where('tenant_id', $tenantId);
        $totalProposals = (clone $proposalsQuery)->count();
        $proposalsConnected = (clone $proposalsQuery)->whereNotNull('tracking_number')->where('tracking_number', '!=', '')->count();
        $proposalsMissing = $totalProposals - $proposalsConnected;

        $totalDocs = $totalPlans + $totalProposals;
        $totalConnected = $plansConnected + $proposalsConnected;
        $totalMissing = $plansMissing + $proposalsMissing;
        $completionRate = $totalDocs > 0 ? round(($totalConnected / $totalDocs) * 100) : 0;

        return view('dss.rie.tracking.index', compact(
            'totalDocs',
            'totalConnected',
            'totalMissing',
            'completionRate',
            'totalPlans',
            'plansConnected',
            'plansMissing',
            'totalProposals',
            'proposalsConnected',
            'proposalsMissing'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(
            auth()->user()?->can('rie.tracking.view') || auth()->user()?->can('rie.tracking.manage'),
            403
        );

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $canManageTracking = (bool) auth()->user()?->can('rie.tracking.manage');

        $docType = $request->get('doc_type', 'all');
        $statusFilter = $request->get('tracking_status', 'all');

        $rows = collect();

        if ($docType === 'all' || $docType === 'plans') {
            $plans = ExtensionPlan::where('tenant_id', $tenantId)
                ->with(['preparedByUser', 'academicProgram'])
                ->latest()
                ->get();

            foreach ($plans as $plan) {
                $hasTracking = ! empty($plan->tracking_number);
                if ($statusFilter === 'connected' && ! $hasTracking) {
                    continue;
                }
                if ($statusFilter === 'missing' && $hasTracking) {
                    continue;
                }

                $statusText = is_array($plan->status_badge) ? ($plan->status_badge['label'] ?? strtoupper($plan->status)) : strtoupper($plan->status);
                $statusClass = is_array($plan->status_badge) ? ($plan->status_badge['class'] ?? 'bg-secondary text-white') : 'bg-secondary text-white';

                $rows->push([
                    'type_key' => 'plan',
                    'type_label' => 'Plan',
                    'id' => $plan->getRouteKey(),
                    'raw_id' => $plan->id,
                    'code' => $plan->plan_code,
                    'title' => $plan->extension_activity,
                    'subtitle' => $plan->program_name,
                    'proponent' => $plan->prepared_by_name ?: ($plan->preparedByUser?->name ?? 'Proponent'),
                    'status' => $plan->status,
                    'status_label' => $statusText,
                    'status_class' => $statusClass,
                    'tracking_number' => $plan->tracking_number,
                    'has_tracking' => $hasTracking,
                    'view_url' => route('dss.rie.plans.show', $plan->getRouteKey()),
                    'pdf_url' => route('dss.rie.plans.pdf', ['id' => $plan->getRouteKey(), 'download' => 1]),
                    'track_url' => $hasTracking ? route('document.track', $plan->tracking_number) : null,
                ]);
            }
        }

        if ($docType === 'all' || $docType === 'proposals') {
            $proposals = ExtensionProposal::where('tenant_id', $tenantId)
                ->with(['projectLeaderUser', 'extensionPlan'])
                ->latest()
                ->get();

            foreach ($proposals as $prop) {
                $hasTracking = ! empty($prop->tracking_number);
                if ($statusFilter === 'connected' && ! $hasTracking) {
                    continue;
                }
                if ($statusFilter === 'missing' && $hasTracking) {
                    continue;
                }

                $statusText = is_array($prop->status_badge) ? ($prop->status_badge['label'] ?? strtoupper($prop->status)) : strtoupper($prop->status);
                $statusClass = is_array($prop->status_badge) ? ($prop->status_badge['class'] ?? 'bg-secondary text-white') : 'bg-secondary text-white';

                $rows->push([
                    'type_key' => 'proposal',
                    'type_label' => 'Proposal',
                    'id' => (string) $prop->id,
                    'raw_id' => $prop->id,
                    'code' => $prop->proposal_code ?: ('PROP-'.str_pad((string) $prop->id, 4, '0', STR_PAD_LEFT)),
                    'title' => $prop->title,
                    'subtitle' => $prop->banner_program ?: 'Extension Initiative',
                    'proponent' => $prop->project_leader_name ?: ($prop->projectLeaderUser?->name ?? 'Project Leader'),
                    'status' => $prop->status,
                    'status_label' => $statusText,
                    'status_class' => $statusClass,
                    'tracking_number' => $prop->tracking_number,
                    'has_tracking' => $hasTracking,
                    'view_url' => route('dss.rie.proposals.show', $prop->id),
                    'pdf_url' => route('dss.rie.proposals.pdf', ['id' => $prop->id, 'download' => 1]),
                    'track_url' => $hasTracking ? route('document.track', $prop->tracking_number) : null,
                ]);
            }
        }

        return DataTables::of($rows)
            ->addColumn('doc_info', function (array $row) {
                $isPlan = $row['type_key'] === 'plan';
                $badge = $isPlan
                    ? '<span class="badge bg-light text-success border border-success-subtle font-mono me-1" style="font-size: 10px;">PLAN</span>'
                    : '<span class="badge bg-light text-primary border border-primary-subtle font-mono me-1" style="font-size: 10px;">PROPOSAL</span>';
                $code = e($row['code']);
                $title = e($row['title']);
                $proponent = e($row['proponent']);

                return '
                    <div>
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            '.$badge.'
                            <span class="fw-bold font-mono text-dark" style="font-size: 12.5px;">'.$code.'</span>
                        </div>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 480px; font-size: 13px;" title="'.$title.'">'.$title.'</div>
                        <div class="text-muted extra-small mt-0.5"><i class="mdi mdi-account-outline me-0.5"></i>'.$proponent.'</div>
                    </div>
                ';
            })
            ->addColumn('status_badge', function (array $row) {
                $lbl = e($row['status_label']);
                $cls = e($row['status_class']);

                return '<span class="badge '.$cls.' rounded-pill px-2.5 py-1 extra-small fw-semibold">'.$lbl.'</span>';
            })
            ->addColumn('tracking_info', function (array $row) use ($canManageTracking) {
                $trk = e($row['tracking_number'] ?? '');
                $typeKey = e($row['type_key']);
                $docId = e($row['id']);
                $docCode = e($row['code']);

                if ($row['has_tracking']) {
                    $manageBtn = $canManageTracking
                        ? '<button type="button" class="btn btn-xs btn-light border py-0.5 px-1.5 text-muted hover-text-primary rounded-1"
                                onclick="openConnectModal(\''.$typeKey.'\', \''.$docId.'\', \''.$docCode.'\', \''.$trk.'\')"
                                title="Edit tracking number">
                            <i class="mdi mdi-pencil-outline"></i>
                           </button>'
                        : '';

                    return '
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1.5 fs-7 d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-barcode-scan"></i>
                                <strong>'.$trk.'</strong>
                            </span>
                            '.$manageBtn.'
                        </div>
                    ';
                }

                if (! $canManageTracking) {
                    return '<span class="badge bg-light text-muted border font-mono px-2 py-1 fs-7">Not Connected</span>';
                }

                return '
                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs"
                            onclick="openConnectModal(\''.$typeKey.'\', \''.$docId.'\', \''.$docCode.'\', \'\')">
                        <i class="mdi mdi-link-variant"></i> Connect #
                    </button>
                ';
            })
            ->addColumn('actions', function (array $row) use ($canManageTracking) {
                $viewUrl = e($row['view_url']);
                $pdfUrl = e($row['pdf_url']);
                $typeKey = e($row['type_key']);
                $docId = e($row['id']);
                $docCode = e($row['code']);

                if ($row['has_tracking']) {
                    $pdfBtn = '<a href="'.$pdfUrl.'" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="Download Official PDF"><i class="mdi mdi-file-pdf-box text-danger fs-6"></i></a>';
                } elseif ($canManageTracking) {
                    $pdfBtn = '<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" onclick="openConnectModal(\''.$typeKey.'\', \''.$docId.'\', \''.$docCode.'\', \'\', true, \''.$pdfUrl.'\')" title="Connect tracking # to print PDF"><i class="mdi mdi-file-pdf-box text-muted fs-6"></i></button>';
                } else {
                    $pdfBtn = '<span class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 disabled text-muted opacity-50" title="Tracking number required to print official PDF"><i class="mdi mdi-file-pdf-box text-muted fs-6"></i></span>';
                }

                return '
                    <div class="d-flex align-items-center justify-content-end gap-1.5">
                        <a href="'.$viewUrl.'" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-dark" title="View Document">
                            <i class="mdi mdi-eye-outline fs-6"></i>
                        </a>
                        '.$pdfBtn.'
                    </div>
                ';
            })
            ->rawColumns(['doc_info', 'status_badge', 'tracking_info', 'actions'])
            ->toJson();
    }

    public function connect(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->can('rie.tracking.manage'), 403, 'Unauthorized to manage tracking numbers.');

        $validated = $request->validate([
            'document_type' => 'required|string|in:plan,proposal',
            'document_id' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:60',
        ]);

        $type = $validated['document_type'];
        $rawId = $validated['document_id'];
        $trackingNumber = trim($validated['tracking_number']);

        if ($type === 'plan') {
            $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($rawId) : (int) $rawId;
            $document = ExtensionPlan::findOrFail($planId);
            $documentCode = $document->plan_code;
        } else {
            $document = ExtensionProposal::findOrFail((int) $rawId);
            $documentCode = $document->proposal_code ?: ('Proposal #'.$document->id);
        }

        $document->update([
            'tracking_number' => $trackingNumber,
        ]);

        $res = SnsuTrackingService::attachTrackingNumber($document, $trackingNumber);

        return response()->json([
            'success' => true,
            'message' => "Successfully connected 3rd-party tracking number '{$trackingNumber}' to {$documentCode}.",
            'tracking_number' => $trackingNumber,
            'remote_data' => $res['remote_data'] ?? null,
        ]);
    }

    public function disconnect(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->can('rie.tracking.manage'), 403, 'Unauthorized to manage tracking numbers.');

        $validated = $request->validate([
            'document_type' => 'required|string|in:plan,proposal',
            'document_id' => 'required|string|max:100',
        ]);

        $type = $validated['document_type'];
        $rawId = $validated['document_id'];

        if ($type === 'plan') {
            $planId = function_exists('dss_decrypt_id') ? dss_decrypt_id($rawId) : (int) $rawId;
            $document = ExtensionPlan::findOrFail($planId);
        } else {
            $document = ExtensionProposal::findOrFail((int) $rawId);
        }

        $oldTracking = $document->tracking_number;
        $document->update(['tracking_number' => null]);

        if (method_exists($document, 'recordAuditTrail')) {
            $document->recordAuditTrail('Tracking Disconnected', "Disconnected tracking number '{$oldTracking}'.", auth()->user());
        }

        return response()->json([
            'success' => true,
            'message' => 'Tracking number successfully disconnected.',
        ]);
    }
}
