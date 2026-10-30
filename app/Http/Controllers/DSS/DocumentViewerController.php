<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Records;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\SystemComment;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use App\Notifications\DocumentWorkflowNotification;
use App\Services\DSS\TenantContext;
use App\Services\DTS\DtsRoutingService;
use App\Services\PDF\DocumentPdfGenerator;
use App\Services\Signatories\CentralizedSignatoryService;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Milon\Barcode\DNS2D;

/**
 * Centralized Document PDF Viewer
 *
 * A single controller entry point for viewing any institutional document as a PDF.
 * All rendering decisions — template mode, header/footer layout, logo slots, barcode
 * and QR positions, core values, address lines — are delegated to the single
 * DocumentPdfGenerator class, which applies Template Studio settings automatically.
 *
 * Routes:
 *   GET /dss/documents/{type}/{id}/view  → viewer blade (iframe shell)
 *   GET /dss/documents/{type}/{id}/pdf   → raw PDF stream (inline or download)
 *
 * Supported {type} values: policy | memo | dts | extension_plan
 */
class DocumentViewerController extends Controller
{
    /**
     * Supported document type configuration.
     *
     * @return array<string, array{label: string, model: class-string, with: string[]}>
     */
    protected function supportedTypes(): array
    {
        return [
            'policy' => [
                'label' => 'Institutional Policy',
                'model' => InstitutionalPolicy::class,
                'with' => ['category', 'leadOffice', 'approvalBody'],
            ],
            'memo' => [
                'label' => 'University Memorandum',
                'model' => UniversityMemorandum::class,
                'with' => [],
            ],
            'dts' => [
                'label' => 'DTS Record',
                'model' => Records::class,
                'with' => [],
            ],
            'extension_plan' => [
                'label' => 'Extension Plan',
                'model' => ExtensionPlan::class,
                'with' => ['campus', 'academicProgram', 'preparedByUser', 'preparedByEmployee', 'notedByEmployee', 'approvedByEmployee'],
            ],
            'extension_proposal' => [
                'label' => 'Extension Proposal',
                'model' => ExtensionProposal::class,
                'with' => ['campus', 'department', 'extensionPlan', 'preparedByUser', 'preparedByEmployee'],
            ],
        ];
    }

    public function view(Request $request, string $type, int|string $id): Response|View
    {
        $types = $this->supportedTypes();
        abort_unless(array_key_exists($type, $types), 404, 'Unknown document type.');

        $config = $types[$type];
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $with = $config['with'];
        if (! Schema::hasTable('dts_records')) {
            $with = array_values(array_filter($with, fn ($rel) => ! str_starts_with($rel, 'dtsRecord')));
        }

        $document = $config['model']::with($with)->findOrFail($id);

        $qs = collect(['paper', 'template'])
            ->filter(fn ($k) => $request->has($k))
            ->mapWithKeys(fn ($k) => [$k => $request->query($k)])
            ->toArray();

        $pdfUrl = route('dss.documents.pdf', ['type' => $type, 'id' => $id, 'download' => '0', ...$qs]);
        $downloadUrl = route('dss.documents.pdf', ['type' => $type, 'id' => $id, 'download' => '1', ...$qs]);
        $barcodeStampUrl = route('dss.documents.barcode', ['type' => $type, 'id' => $id]);
        $meta = $this->resolveDocumentMeta($type, $document);

        $commentsCount = SystemComment::forTransaction($type, $id)
            ->where('tenant_id', $tenantId)
            ->count();

        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($document);

        $user = Auth::user();
        $myPendingSignatory = null;
        $mySignedSignatory = null;
        $savedSignature = null;

        if ($user) {
            foreach ($signatories as $sig) {
                if ($sig->canBeSignedBy($user)) {
                    if ($sig->status === 'pending' && ! $myPendingSignatory) {
                        $myPendingSignatory = $sig;
                    } elseif ($sig->status === 'signed' && ! $mySignedSignatory) {
                        $mySignedSignatory = $sig;
                    }
                }
            }

            $savedSignature = DocumentSignatory::where('signed_by_user_id', $user->id)
                ->whereNotNull('signature_img')
                ->latest('signed_at')
                ->value('signature_img');
        }

        $modelClass = $config['model'];
        $uploadedDocs = UploadedDocument::with(['uploader'])
            ->where('tenant_id', $tenantId)
            ->where(function ($q) use ($modelClass, $type, $id) {
                $q->where(function ($sub) use ($modelClass, $id) {
                    $sub->where('documentable_type', $modelClass)
                        ->where('documentable_id', $id);
                })->orWhere(function ($sub) use ($type, $id) {
                    $sub->where('documentable_type', $type)
                        ->where('documentable_id', $id);
                });
            })
            ->orderBy('is_physically_signed', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $attachmentsCount = $uploadedDocs->count();
        $physicallySignedCount = $uploadedDocs->where('is_physically_signed', true)->count();

        return view('dss.documents.viewer', compact(
            'institution', 'theme', 'type', 'id', 'document', 'meta', 'pdfUrl', 'downloadUrl', 'barcodeStampUrl',
            'commentsCount', 'signatories', 'myPendingSignatory', 'mySignedSignatory', 'savedSignature',
            'uploadedDocs', 'attachmentsCount', 'physicallySignedCount'
        ));
    }

    /**
     * Alias for view() to satisfy /{type}/{id}/view or show route calls.
     */
    public function show(Request $request, string $type, int|string $id): Response|View
    {
        return $this->view($request, $type, $id);
    }

    /**
     * Retrieve all chronological comments and feedback notes for a document from sys_comments.
     */
    public function getComments(string $type, int|string $id): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $comments = SystemComment::with(['user.employee'])
            ->where('tenant_id', $tenantId)
            ->forTransaction($type, $id)
            ->chronological()
            ->get()
            ->map(function (SystemComment $c) {
                return [
                    'id' => $c->id,
                    'user_id' => $c->user_id,
                    'user_name' => $c->author_name,
                    'user_role' => $c->author_designation,
                    'initial' => $c->author_initials,
                    'action_type' => $c->action_type,
                    'comment' => $c->comment,
                    'rating_at_time' => $c->rating_at_time,
                    'stage_at_time' => $c->stage_at_time,
                    'created_at_human' => $c->created_at_human,
                    'created_at_formatted' => $c->created_at_formatted,
                    'is_self' => Auth::check() && Auth::id() === $c->user_id,
                    'is_current_user' => Auth::check() && Auth::id() === $c->user_id,
                ];
            });

        return response()->json([
            'status' => 'success',
            'result' => true,
            'comments' => $comments,
            'count' => $comments->count(),
        ]);
    }

    /**
     * Store a new audit feedback note or review comment for the document in sys_comments.
     */
    public function storeComment(Request $request, string $type, int|string $id): JsonResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:3000'],
            'action_type' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $comment = SystemComment::create([
            'tenant_id' => $tenantId,
            'commentable_type' => $type,
            'commentable_id' => $id,
            'user_id' => $user?->id,
            'author_name' => $user?->employee?->full_name ?: $user?->name,
            'author_designation' => $user?->employee?->designation ?: ($user?->designation ?: 'Staff'),
            'action_type' => $validated['action_type'] ?? 'comment',
            'comment' => trim($validated['comment']),
        ]);

        return response()->json([
            'status' => 'success',
            'result' => true,
            'message' => 'Note recorded successfully.',
            'comment' => $comment,
        ]);
    }

    /**
     * Electronically sign and endorse the document by an authorized signatory.
     */
    public function signDocument(Request $request, string $type, int|string $id): JsonResponse
    {
        $types = $this->supportedTypes();
        abort_unless(array_key_exists($type, $types), 404, 'Unknown document type.');

        $validated = $request->validate([
            'tier_role' => ['nullable', 'string', 'max:50'],
            'signature_img' => ['required', 'string'],
            'signature_type' => ['nullable', 'string', 'in:draw,drawn,type,typed,saved,upload'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Authentication required.'], 401);
        }

        $config = $types[$type];
        $document = $config['model']::findOrFail($id);
        $signatories = CentralizedSignatoryService::getSignatoriesForDocument($document);

        // Find target signatory record
        $targetSig = null;
        if (! empty($validated['tier_role'])) {
            $targetSig = $signatories->firstWhere('tier_role', $validated['tier_role']);
        } else {
            $targetSig = $signatories->first(fn ($s) => $s->canBeSignedBy($user) && $s->status === 'pending');
        }

        if (! $targetSig) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active pending signatory assignment found for your user on this document.',
            ], 403);
        }

        $canSign = $user->isSuperAdmin() || $user->isTenantAdmin() || $targetSig->canBeSignedBy($user);
        if (! $canSign) {
            return response()->json([
                'status' => 'error',
                'message' => "You are not authorized to affix signature for the '{$targetSig->role_label}' tier.",
            ], 403);
        }

        $tierRole = $targetSig->tier_role;
        $roleLabel = $targetSig->role_label ?: (ucfirst($tierRole).' by');

        // Record the digital signature
        $sigRecord = CentralizedSignatoryService::recordDigitalSignature(
            $document,
            $tierRole,
            $user,
            [
                'role_label' => $roleLabel,
                'signature_img' => $validated['signature_img'],
                'signature_type' => $validated['signature_type'] ?? 'drawn',
                'remarks' => $validated['remarks'] ?? 'Verified and electronically endorsed.',
                'designation' => $targetSig->designation ?: ($user->employee?->designation ?: ($user->designation ?: 'Authorized Signatory')),
            ]
        );

        // Record automatic audit note in sys_comments
        $remarkText = ! empty($validated['remarks']) ? "\nRemarks: ".trim($validated['remarks']) : '';
        SystemComment::create([
            'tenant_id' => $tenantId,
            'commentable_type' => $type,
            'commentable_id' => $id,
            'user_id' => $user->id,
            'author_name' => $user->employee?->full_name ?: $user->name,
            'author_designation' => $targetSig->designation ?: ($user->employee?->designation ?: 'Signatory'),
            'action_type' => 'endorsed',
            'comment' => "Affixed digital signature & seal as {$roleLabel} [{$sigRecord['signature_hash']}].{$remarkText}",
        ]);

        // Update overall document status if applicable
        if ($document instanceof ExtensionPlan) {
            if ($tierRole === 'noted' && $document->status === 'submitted') {
                $document->status = 'noted';
                $document->save();
            } elseif ($tierRole === 'approved') {
                $document->status = 'approved';
                $document->save();
            }
        } elseif ($document instanceof InstitutionalPolicy) {
            if ($tierRole === 'approved') {
                $document->status = 'approved';
                $document->save();
            }
        } elseif ($document instanceof ExtensionProposal) {
            if ($tierRole === 'reviewed' && in_array($document->status, ['submitted', 'draft'])) {
                $document->status = 'reviewed';
            } elseif ($tierRole === 'noted') {
                $document->status = 'noted';
            } elseif ($tierRole === 'concurred') {
                $document->status = 'concurred';
            } elseif ($tierRole === 'recommended') {
                $document->status = 'recommended';
            } elseif ($tierRole === 'approved') {
                $document->status = 'approved';
            }
            $document->save();

            if ($document->dtsRecord && class_exists(DtsRoutingService::class)) {
                $forAction = match ($tierRole) {
                    'prepared' => 'Proposal Prepared & Submitted for Technical Review',
                    'reviewed' => 'Reviewed by Extension Head - Endorsed for Dean Note',
                    'noted' => 'Noted by Dean - Endorsed for Extension Director Concurrence',
                    'concurred' => 'Concurred by Extension Director - Endorsed for VP RIE Recommendation',
                    'recommended' => 'Recommending Approval by VP RIE - Endorsed for Presidential Approval',
                    'approved' => 'Officially Approved by University President',
                    default => 'Digitally Signed Tier',
                };
                $trailStatus = ($tierRole === 'approved') ? 'Done' : 'In-Transit';
                DtsRoutingService::recordTrailLog(
                    $document->dtsRecord,
                    $forAction,
                    $trailStatus,
                    $validated['remarks'] ?? "Digitally signed as {$roleLabel}.",
                    $user?->id
                );
            }
        }

        // Live notification dispatch
        $meta = $this->resolveDocumentMeta($type, $document);
        $docUrl = route('dss.documents.view', ['type' => $type, 'id' => $id]);
        $creator = $this->resolveDocumentCreator($document);

        if ($creator && $creator->id !== $user->id) {
            $creator->notify(new DocumentWorkflowNotification(
                title: "Document Endorsed: {$meta['title']}",
                message: "{$user->name} electronically signed and verified this document as {$roleLabel}.",
                actionType: 'endorsed',
                url: $docUrl,
                documentType: $type,
                documentId: $id,
                referenceCode: $meta['reference'] ?? null,
                senderName: $user->name,
                senderId: $user->id
            ));
        }

        // Notify next pending signatory in queue
        $nextSig = DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->where('status', 'pending')
            ->orderBy('position_column')
            ->orderBy('sort_order')
            ->first();

        if ($nextSig && $nextSig->user_id && $nextSig->user_id !== $user->id) {
            $nextUser = User::find($nextSig->user_id);
            if ($nextUser) {
                $nextUser->notify(new DocumentWorkflowNotification(
                    title: "Ready For Your Signature: {$meta['title']}",
                    message: "Document has advanced in routing and is ready for your endorsement as {$nextSig->role_label}.",
                    actionType: 'signature_required',
                    url: $docUrl,
                    documentType: $type,
                    documentId: $id,
                    referenceCode: $meta['reference'] ?? null,
                    senderName: $user->name,
                    senderId: $user->id
                ));
            }
        }

        return response()->json([
            'status' => 'success',
            'result' => true,
            'message' => "Document successfully signed and sealed as '{$roleLabel}'.",
            'signature_hash' => $sigRecord['signature_hash'],
            'signed_at' => $sigRecord['signed_at'],
        ]);
    }

    /**
     * Request revisions on the document with formal instructions and creator notification.
     */
    public function requestRevision(Request $request, string $type, int|string $id): JsonResponse
    {
        $types = $this->supportedTypes();
        abort_unless(array_key_exists($type, $types), 404, 'Unknown document type.');

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:1000'],
        ]);

        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $user = Auth::user();

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Authentication required.'], 401);
        }

        $config = $types[$type];
        $document = $config['model']::findOrFail($id);

        // Record revision note in sys_comments
        $comment = SystemComment::create([
            'tenant_id' => $tenantId,
            'commentable_type' => $type,
            'commentable_id' => $id,
            'user_id' => $user->id,
            'author_name' => $user->employee?->full_name ?: $user->name,
            'author_designation' => $user->employee?->designation ?: ($user->designation ?: 'Reviewer'),
            'action_type' => 'revision_requested',
            'comment' => 'Revision Requested: '.trim($validated['remarks']),
        ]);

        // Update document status if status column exists
        if (in_array('status', $document->getFillable()) || isset($document->status)) {
            $document->status = 'revision_requested';
            $document->save();
        }

        // Notify document creator
        $meta = $this->resolveDocumentMeta($type, $document);
        $docUrl = route('dss.documents.view', ['type' => $type, 'id' => $id]).'#comments';
        $creator = $this->resolveDocumentCreator($document);

        if ($creator && $creator->id !== $user->id) {
            $creator->notify(new DocumentWorkflowNotification(
                title: "Revision Requested: {$meta['title']}",
                message: "{$user->name} requested revisions: \"".Str::limit($validated['remarks'], 120).'"',
                actionType: 'revision_requested',
                url: $docUrl,
                documentType: $type,
                documentId: $id,
                referenceCode: $meta['reference'] ?? null,
                senderName: $user->name,
                senderId: $user->id
            ));
        }

        return response()->json([
            'status' => 'success',
            'result' => true,
            'message' => 'Revision request logged and document author notified.',
            'comment' => $comment,
        ]);
    }

    /**
     * Helper to resolve the creator / author user of a document across modules.
     */
    protected function resolveDocumentCreator(Model $document): ?User
    {
        $creatorId = $document->prepared_by_user_id
            ?? ($document->created_by
            ?? ($document->user_id
            ?? ($document->creator_id ?? null)));

        if ($creatorId) {
            return User::find($creatorId);
        }

        return null;
    }

    /**
     * Stream the PDF using DocumentPdfGenerator — one class decides everything.
     */
    public function pdf(Request $request, string $type, int|string $id): Response
    {
        $types = $this->supportedTypes();
        abort_unless(array_key_exists($type, $types), 404, 'Unknown document type.');

        $config = $types[$type];
        $institution = TenantContext::getInstitution();
        $with = $config['with'];
        if (! Schema::hasTable('dts_records')) {
            $with = array_values(array_filter($with, fn ($rel) => ! str_starts_with($rel, 'dtsRecord')));
        }
        $document = $config['model']::with($with)->findOrFail($id);

        $paper = $request->query('paper');
        $template = $request->query('template');
        $dest = $request->query('download') === '1' ? 'D' : 'I';

        // Single unified generator — all template settings applied inside
        $pdf = match ($type) {
            'policy' => DocumentPdfGenerator::fromPolicy($document, $institution, $paper, $template),
            'memo' => DocumentPdfGenerator::fromMemo($document, $institution, $paper, $template),
            'dts' => DocumentPdfGenerator::fromDts($document, $institution),
            'extension_plan' => DocumentPdfGenerator::fromExtensionPlan($document, $institution, $paper, $template),
            'extension_proposal' => DocumentPdfGenerator::fromExtensionProposal($document, $institution, $paper, $template),
            default => abort(404),
        };

        $prefix = $institution?->school_acronym ?: 'DOC';
        $ref = match ($type) {
            'policy' => $document->policy_number ?: $document->tracking_number,
            'memo' => $document->memo_number ?: $document->tracking_number ?: $document->id,
            'dts' => $document->DocumentID,
            'extension_plan' => $document->plan_code ?: ('EXT_'.$document->id),
            'extension_proposal' => $document->proposal_code ?: ('PROP_'.$document->id),
        };
        $fileName = $prefix.'_'.str_replace(['/', '\\', ' '], '_', (string) $ref).'.pdf';

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($dest === 'D' ? 'attachment' : 'inline')."; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Alias for pdf() to satisfy preview route calls.
     */
    public function preview(Request $request, string $type, int|string $id): Response
    {
        return $this->pdf($request, $type, $id);
    }

    /**
     * Generate a barcode-stamp PDF — minimal PDF417 tracking label, no full letterhead.
     * Useful for printing and physically attaching to a document.
     */
    public function barcodeStamp(Request $request, string $type, int|string $id): Response
    {
        $types = $this->supportedTypes();
        abort_unless(array_key_exists($type, $types), 404, 'Unknown document type.');

        $config = $types[$type];
        $with = $config['with'];
        if (! Schema::hasTable('dts_records')) {
            $with = array_values(array_filter($with, fn ($rel) => ! str_starts_with($rel, 'dtsRecord')));
        }
        $document = $config['model']::with($with)->findOrFail($id);
        $meta = $this->resolveDocumentMeta($type, $document);
        $institution = TenantContext::getInstitution();

        $prefix = $institution?->school_acronym ?: 'DOC';
        $tracking = $meta['tracking'] ?: $meta['reference'] ?: (string) $id;
        $formattedTracking = is_numeric($tracking)
            ? str_pad($tracking, 12, '0', STR_PAD_LEFT)
            : $tracking;
        $grouped = implode(' ', str_split($formattedTracking, 4));

        // Build a minimal A4 PDF — barcode stamp block only
        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(false);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $greenR = 21;
        $greenG = 128;
        $greenB = 61;

        // Top green accent line
        $pdf->SetDrawColor($greenR, $greenG, $greenB);
        $pdf->SetLineWidth(1.5);
        $pdf->Line(20, 18, 190, 18);
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);

        // Institution name
        $pdf->SetY(22);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(15, 23, 42);
        $pdf->Cell(170, 6, $institution?->school_name ?: 'SURIGAO DEL NORTE STATE UNIVERSITY', 0, 1, 'C');

        // Document type + title
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->Cell(170, 5, $meta['subtitle'].' — '.mb_strimwidth($meta['title'], 0, 90, '…'), 0, 1, 'C');

        // Reference badge
        if (! empty($meta['reference'])) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor($greenR, $greenG, $greenB);
            $pdf->Cell(170, 5, 'Ref: '.$meta['reference'], 0, 1, 'C');
        }

        // PDF417 Barcode — large, centered
        $bcW = 120.0;
        $bcH = 28.0;
        $bcX = (210 - $bcW) / 2;
        $bcY = $pdf->GetY() + 10;

        try {
            $d2 = new DNS2D;
            $pngBase64 = $d2->getBarcodePNG($formattedTracking, 'PDF417', 4, 4);
            if ($pngBase64) {
                $tmp = tempnam(sys_get_temp_dir(), 'snsu_stamp_').'.png';
                file_put_contents($tmp, base64_decode($pngBase64));
                $pdf->Image($tmp, $bcX, $bcY, $bcW, $bcH);
                @unlink($tmp);
            }
        } catch (\Throwable) {
            // skip if barcode fails
        }

        // Tracking number in Courier below barcode
        $pdf->SetXY($bcX, $bcY + $bcH + 2.5);
        $pdf->SetFont('Courier', 'B', 12);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell($bcW, 6, $grouped, 0, 1, 'C');

        // Date stamp
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->Cell($bcW, 5, 'Generated: '.now()->format('F d, Y h:i A'), 0, 1, 'C');

        // Bottom green accent line
        $pdf->SetDrawColor($greenR, $greenG, $greenB);
        $pdf->SetLineWidth(1.5);
        $pdf->Line(20, 277, 190, 277);

        $fileName = $prefix.'_BARCODE_'.str_replace(['/', '\\', ' '], '_', $formattedTracking).'.pdf';

        return response($pdf->Output('S', $fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Meta Resolver (for viewer header display only)
    // ──────────────────────────────────────────────────────────────────────────

    private function resolveDocumentMeta(string $type, mixed $document): array
    {
        return match ($type) {
            'policy' => [
                'title' => $document->title ?? 'Institutional Policy',
                'subtitle' => $document->document_type ?? 'Policy',
                'reference' => $document->policy_number ?? $document->tracking_number,
                'tracking' => $document->tracking_number,
                'date' => $document->effectivity_date?->format('F d, Y'),
                'status' => $document->status ?? 'Draft',
                'status_color' => match (strtolower($document->status ?? 'draft')) {
                    'approved', 'published' => 'success',
                    'pending', 'review' => 'warning',
                    'returned', 'rejected' => 'danger',
                    default => 'secondary',
                },
                'icon' => 'mdi-file-document-outline',
            ],
            'memo' => [
                'title' => $document->subject ?? $document->title ?? 'University Memorandum',
                'subtitle' => $document->document_type ?? 'Memorandum',
                'reference' => $document->memo_number ?? $document->tracking_number,
                'tracking' => $document->tracking_number,
                'date' => $document->memo_date?->format('F d, Y'),
                'status' => $document->status ?? 'Draft',
                'status_color' => match (strtolower($document->status ?? 'draft')) {
                    'released', 'published' => 'success',
                    'pending' => 'warning',
                    default => 'secondary',
                },
                'icon' => 'mdi-email-outline',
            ],
            'dts' => [
                'title' => $document->subject ?? $document->title ?? 'DTS Record',
                'subtitle' => $document->document_type ?? 'Document',
                'reference' => $document->DocumentID,
                'tracking' => $document->DocumentID,
                'date' => $document->created_at?->format('F d, Y'),
                'status' => $document->current_status ?? 'In Transit',
                'status_color' => 'info',
                'icon' => 'mdi-file-move-outline',
            ],
            'extension_plan' => [
                'title' => $document->extension_activity,
                'subtitle' => 'Extension Plan - '.$document->program_name,
                'reference' => $document->plan_code,
                'tracking' => $document->tracking_number ?: ($document->dts_document_id ?: $document->plan_code),
                'date' => $document->created_at?->format('F d, Y'),
                'status' => ucfirst($document->status ?? 'Draft'),
                'status_color' => match (strtolower($document->status ?? 'draft')) {
                    'approved' => 'success',
                    'submitted', 'under_review' => 'warning',
                    'rejected' => 'danger',
                    default => 'info',
                },
                'icon' => 'mdi-calendar-multiselect',
            ],
            'extension_proposal' => [
                'title' => $document->title,
                'subtitle' => 'Extension Proposal - '.($document->proposal_code ?: 'RIE'),
                'reference' => $document->proposal_code,
                'tracking' => $document->tracking_number ?: ($document->dts_document_id ?: $document->proposal_code),
                'date' => $document->created_at?->format('F d, Y'),
                'status' => ucfirst($document->status ?? 'Draft'),
                'status_color' => match (strtolower($document->status ?? 'draft')) {
                    'approved' => 'success',
                    'submitted', 'under_review', 'reviewed', 'noted', 'concurred', 'recommended' => 'warning',
                    'rejected' => 'danger',
                    default => 'info',
                },
                'icon' => 'mdi-file-document-outline',
            ],
            default => [
                'title' => 'Document', 'subtitle' => 'Record', 'reference' => '—',
                'tracking' => null, 'date' => null, 'status' => null,
                'status_color' => 'secondary', 'icon' => 'mdi-file-outline',
            ],
        };
    }
}
