<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Records;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionPlanActivity;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentAuditTrail;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use App\Services\DSS\TenantContext;
use App\Services\PDF\DocumentWatermarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CentralizedDocumentUploadController extends Controller
{
    /**
     * Map friendly type aliases to full Model class names.
     */
    public static function resolveModelClass(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        return match (strtolower(trim($type))) {
            'extension_plan', 'extensionplan', 'rie_plan', 'plan' => ExtensionPlan::class,
            'extension_proposal', 'extensionproposal', 'rie_proposal', 'proposal' => ExtensionProposal::class,
            'extension_plan_activity', 'extensionplanactivity', 'activity', 'milestone', 'extension_activity' => ExtensionPlanActivity::class,
            'policy', 'institutional_policy', 'institutionalpolicy' => InstitutionalPolicy::class,
            'memo', 'memorandum', 'university_memorandum' => UniversityMemorandum::class,
            'dts', 'record', 'records' => Records::class,
            default => class_exists($type) ? $type : null,
        };
    }

    /**
     * Determine module name from model class or alias.
     */
    public static function resolveModule(?string $type): string
    {
        $resolved = self::resolveModelClass($type);

        return match ($resolved) {
            ExtensionPlan::class,
            ExtensionProposal::class,
            ExtensionPlanActivity::class => 'rie',
            InstitutionalPolicy::class => 'policy',
            UniversityMemorandum::class => 'memo',
            Records::class => 'dts',
            default => 'general',
        };
    }

    /**
     * Centralized Institutional Document Repository Masterlist.
     */
    public function index(Request $request): View|JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $moduleFilter = $request->query('module');
        $categoryFilter = $request->query('category');
        $physicallySignedOnly = $request->boolean('physically_signed');
        $formatFilter = $request->query('format');
        $storageFilter = $request->query('storage');
        $search = trim((string) $request->query('search', ''));
        $campusId = $request->query('campus_id');
        $sort = $request->query('sort', 'latest');
        $viewMode = $request->query('view', 'table');
        if (! in_array($viewMode, ['grid', 'table'], true)) {
            $viewMode = 'table';
        }

        $query = UploadedDocument::with(['uploader', 'campus', 'documentable'])
            ->where('tenant_id', $tenantId);

        if ($moduleFilter) {
            $query->where('module', $moduleFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($physicallySignedOnly) {
            $query->where('is_physically_signed', true);
        }

        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        if ($storageFilter === 'gdrive' || $storageFilter === 'cloud') {
            $query->where('disk', 'external');
        } elseif ($storageFilter === 'local') {
            $query->where('disk', '!=', 'external');
        }

        if ($formatFilter) {
            match ($formatFilter) {
                'pdf' => $query->where('extension', 'pdf'),
                'image' => $query->whereIn('extension', ['png', 'jpg', 'jpeg', 'webp', 'gif']),
                'sheet' => $query->whereIn('extension', ['xls', 'xlsx', 'csv']),
                'doc' => $query->whereIn('extension', ['doc', 'docx', 'txt', 'rtf']),
                'gdrive' => $query->where('disk', 'external'),
                'zip' => $query->where('extension', 'zip'),
                default => null,
            };
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%")
                    ->orWhere('sha256_hash', 'like', "%{$search}%")
                    ->orWhere('uploader_name', 'like', "%{$search}%")
                    ->orWhere('signatory_notes', 'like', "%{$search}%");
            });
        }

        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc')->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('title', 'asc'),
            'name_desc' => $query->orderBy('title', 'desc'),
            'size_desc' => $query->orderByDesc('file_size'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'), // Default: sorted by latest
        };

        $documents = $query->paginate(24)->onEachSide(1)->withQueryString();

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();
        $totalCount = UploadedDocument::where('tenant_id', $tenantId)->count();
        $signedCount = UploadedDocument::where('tenant_id', $tenantId)->where('is_physically_signed', true)->count();
        $rieCount = UploadedDocument::where('tenant_id', $tenantId)->where('module', 'rie')->count();
        $gdriveCount = UploadedDocument::where('tenant_id', $tenantId)->where('disk', 'external')->count();
        $policyCount = UploadedDocument::where('tenant_id', $tenantId)->whereIn('module', ['policy', 'memo'])->count();
        $dtsCount = UploadedDocument::where('tenant_id', $tenantId)->where('module', 'dts')->count();
        $totalStorageBytes = (int) UploadedDocument::where('tenant_id', $tenantId)->where('disk', '!=', 'external')->sum('file_size');
        $systemStorage = $this->getSystemStorageMetrics($totalStorageBytes);
        $recentAuditTrails = DocumentAuditTrail::where('tenant_id', $tenantId)->with(['user'])->latest()->take(15)->get();

        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'viewMode' => $viewMode,
                'html' => view('dss.documents.partials.repository-canvas', compact(
                    'documents',
                    'campuses',
                    'totalCount',
                    'signedCount',
                    'rieCount',
                    'gdriveCount',
                    'policyCount',
                    'dtsCount',
                    'totalStorageBytes',
                    'systemStorage',
                    'sort',
                    'moduleFilter',
                    'categoryFilter',
                    'physicallySignedOnly',
                    'formatFilter',
                    'storageFilter',
                    'search',
                    'campusId',
                    'viewMode'
                ))->render(),
                'counts' => [
                    'total' => $totalCount,
                    'signed' => $signedCount,
                    'rie' => $rieCount,
                    'gdrive' => $gdriveCount,
                    'policy' => $policyCount,
                    'dts' => $dtsCount,
                    'storage_mb' => number_format($totalStorageBytes / (1024 * 1024), 1),
                    'gdrive_count' => $gdriveCount,
                    'system_storage' => $systemStorage,
                ],
                'filters' => [
                    'module' => $moduleFilter,
                    'category' => $categoryFilter,
                    'physically_signed' => $physicallySignedOnly,
                    'format' => $formatFilter,
                    'storage' => $storageFilter,
                    'search' => $search,
                    'campus_id' => $campusId,
                    'sort' => $sort,
                    'view' => $viewMode,
                ],
                'pagination' => [
                    'total' => $documents->total(),
                    'first_item' => $documents->firstItem(),
                    'last_item' => $documents->lastItem(),
                    'current_page' => $documents->currentPage(),
                    'last_page' => $documents->lastPage(),
                ],
            ]);
        }

        return view('dss.documents.repository', compact(
            'documents',
            'campuses',
            'totalCount',
            'signedCount',
            'rieCount',
            'gdriveCount',
            'policyCount',
            'dtsCount',
            'totalStorageBytes',
            'systemStorage',
            'recentAuditTrails',
            'institution',
            'theme',
            'sort',
            'moduleFilter',
            'categoryFilter',
            'physicallySignedOnly',
            'formatFilter',
            'storageFilter',
            'search',
            'campusId',
            'viewMode'
        ));
    }

    /**
     * Upload and attach files/links to repository (Simultaneous Multi-File & Multi-GDrive support).
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        // Sanitize and filter Google Drive links before validation
        if ($request->has('gdrive_urls') && is_array($request->input('gdrive_urls'))) {
            $cleaned = [];
            foreach ($request->input('gdrive_urls') as $u) {
                $trimmed = trim((string) $u);
                if ($trimmed !== '') {
                    if (! preg_match('#^https?://#i', $trimmed)) {
                        $trimmed = 'https://'.$trimmed;
                    }
                    $cleaned[] = $trimmed;
                }
            }
            $request->merge(['gdrive_urls' => $cleaned]);
        }
        if ($request->filled('gdrive_url')) {
            $trimmed = trim((string) $request->input('gdrive_url'));
            if (! preg_match('#^https?://#i', $trimmed)) {
                $trimmed = 'https://'.$trimmed;
            }
            $request->merge(['gdrive_url' => $trimmed]);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|max:51200|mimes:pdf',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|max:51200|mimes:pdf',
            'gdrive_url' => 'nullable|string|max:1000',
            'gdrive_urls' => 'nullable|array',
            'gdrive_urls.*' => 'nullable|string|max:1000',
            'external_url' => 'nullable|string|max:1000',
            'documentable_type' => 'nullable|string|max:150',
            'documentable_id' => 'nullable|integer',
            'activity_id' => 'nullable|integer',
            'category' => 'nullable|string|max:80',
            'title' => 'nullable|string|max:255',
            'is_physically_signed' => 'nullable',
            'signed_date' => 'nullable|date',
            'signatory_notes' => 'nullable|string|max:1000',
            'module' => 'nullable|string|max:60',
        ], [
            'file.mimes' => 'Only PDF documents (.pdf) are accepted.',
            'files.*.mimes' => 'All uploaded files must be in PDF format (.pdf).',
        ]);

        // Collect all physical files
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = array_filter((array) $request->file('files'));
        }
        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        // Collect all Google Drive links
        $gdriveUrls = [];
        if ($request->has('gdrive_urls') && is_array($request->input('gdrive_urls'))) {
            foreach ($request->input('gdrive_urls') as $url) {
                $trimmed = trim((string) $url);
                if ($trimmed !== '') {
                    $gdriveUrls[] = $trimmed;
                }
            }
        }
        if (! empty($validated['gdrive_url'])) {
            $trimmed = trim((string) $validated['gdrive_url']);
            if ($trimmed !== '' && ! in_array($trimmed, $gdriveUrls, true)) {
                $gdriveUrls[] = $trimmed;
            }
        }
        if (! empty($validated['external_url'])) {
            $trimmed = trim((string) $validated['external_url']);
            if ($trimmed !== '' && ! in_array($trimmed, $gdriveUrls, true)) {
                $gdriveUrls[] = $trimmed;
            }
        }

        if (empty($uploadedFiles) && empty($gdriveUrls)) {
            $msg = 'Please upload at least one document file or provide a valid Google Drive link.';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['file' => $msg]);
        }

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $campusId = $user?->campus_id ?? $tenant?->campuses()->first()?->id;

        $modelClass = self::resolveModelClass($validated['documentable_type'] ?? null);
        $targetId = $validated['documentable_id'] ?? null;
        $module = $validated['module'] ?? self::resolveModule($validated['documentable_type'] ?? null);
        $category = $validated['category'] ?: ($request->boolean('is_physically_signed') ? 'physically_signed_plan' : 'general');
        $isPhysicallySigned = $request->boolean('is_physically_signed') || $category === 'physically_signed_plan';
        $targetActivityId = $request->input('activity_id') ?: ($modelClass === ExtensionPlanActivity::class ? $targetId : null);

        $createdDocs = [];

        // 1. Process Physical Files
        foreach ($uploadedFiles as $file) {
            $originalFilename = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $sha256Hash = hash_file('sha256', $file->getRealPath());

            $docTitle = ! empty($validated['title']) && count($uploadedFiles) === 1 && empty($gdriveUrls)
                ? trim($validated['title'])
                : ($isPhysicallySigned ? 'Physically Signed Copy — '.pathinfo($originalFilename, PATHINFO_FILENAME) : pathinfo($originalFilename, PATHINFO_FILENAME));

            $year = date('Y');
            $month = date('m');
            $safeHash = Str::random(24);
            $fileName = "{$safeHash}.{$extension}";
            $storageDirectory = "documents/{$module}/{$year}/{$month}";
            $storedPath = $file->storeAs($storageDirectory, $fileName, 'public');

            $doc = UploadedDocument::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'category' => $category,
                'title' => $docTitle,
                'original_filename' => $originalFilename,
                'file_path' => $storedPath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'disk' => 'public',
                'sha256_hash' => $sha256Hash,
                'is_physically_signed' => $isPhysicallySigned,
                'signed_date' => $validated['signed_date'] ?? ($isPhysicallySigned ? now()->toDateString() : null),
                'signatory_notes' => $validated['signatory_notes'] ?? null,
                'version' => '1.0',
                'status' => 'active',
                'uploaded_by_user_id' => $user?->id,
                'uploader_name' => $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Personnel'),
                'metadata' => [
                    'client_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'uploaded_at' => now()->toIso8601String(),
                    'is_external' => false,
                ],
            ]);

            DocumentAuditTrail::record([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'action' => $isPhysicallySigned ? 'physically_signed_tagged' : 'uploaded',
                'version' => '1.0',
                'summary' => "Uploaded file {$originalFilename} to institutional repository",
                'sha256_hash' => $sha256Hash,
                'filename' => $originalFilename,
                'file_size' => $fileSize,
            ], $request);

            if ($targetActivityId) {
                $this->linkDocumentToMilestoneActivity($doc, $targetActivityId, $user, $validated['signatory_notes'] ?? null);
            }

            $createdDocs[] = $doc;
        }

        // 2. Process Google Drive URLs
        foreach ($gdriveUrls as $url) {
            $isGdrive = str_contains($url, 'drive.google.com');
            $originalFilename = count($gdriveUrls) === 1 && ! empty($validated['title'])
                ? trim($validated['title'])
                : ($isGdrive ? 'Google Drive Document' : 'Cloud Document Link');
            $sha256Hash = hash('sha256', $url);

            $doc = UploadedDocument::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'category' => $category,
                'title' => $originalFilename,
                'original_filename' => $originalFilename,
                'file_path' => $url,
                'file_size' => 0,
                'mime_type' => 'application/vnd.google-apps.document',
                'extension' => $isGdrive ? 'gdrive' : 'url',
                'disk' => 'external',
                'sha256_hash' => $sha256Hash,
                'is_physically_signed' => $isPhysicallySigned,
                'signed_date' => $validated['signed_date'] ?? ($isPhysicallySigned ? now()->toDateString() : null),
                'signatory_notes' => $validated['signatory_notes'] ?? null,
                'version' => '1.0',
                'status' => 'active',
                'uploaded_by_user_id' => $user?->id,
                'uploader_name' => $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Personnel'),
                'metadata' => [
                    'client_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'uploaded_at' => now()->toIso8601String(),
                    'is_external' => true,
                    'is_gdrive' => $isGdrive,
                    'external_url' => $url,
                ],
            ]);

            DocumentAuditTrail::record([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'action' => 'gdrive_link_attached',
                'version' => '1.0',
                'summary' => "Registered Google Drive shareable link to repository: {$url}",
                'sha256_hash' => $sha256Hash,
                'filename' => $originalFilename,
                'file_size' => 0,
            ], $request);

            if ($targetActivityId) {
                $this->linkDocumentToMilestoneActivity($doc, $targetActivityId, $user, $validated['signatory_notes'] ?? null);
            }

            $createdDocs[] = $doc;
        }

        $count = count($createdDocs);
        $successMsg = $count > 1
            ? "Successfully registered {$count} documents/links to the institutional repository."
            : 'Document successfully registered to the institutional repository.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data' => $createdDocs[0] ?? null,
                'documents' => $createdDocs,
                'count' => $count,
            ]);
        }

        return redirect()->route('dss.documents.repository')->with('success', $successMsg);
    }

    /**
     * Link uploaded document to an extension plan milestone activity.
     */
    protected function linkDocumentToMilestoneActivity(UploadedDocument $doc, int $activityId, ?User $user, ?string $notes = null): void
    {
        $act = ExtensionPlanActivity::find($activityId);
        if (! $act) {
            return;
        }

        $act->update([
            'uploaded_document_id' => $doc->id,
            'document_url' => $doc->file_path,
            'document_filename' => $doc->original_filename,
            'document_disk' => $doc->disk,
            'document_mime_type' => $doc->mime_type,
            'document_size' => $doc->file_size,
            'document_hash' => $doc->sha256_hash,
            'uploaded_at' => now(),
            'uploaded_by_user_id' => $user?->id,
            'file_path' => $doc->file_path,
            'file_name' => $doc->original_filename,
            'status' => 'completed',
            'completed_at' => now(),
            'remarks' => $notes ?: $act->remarks,
        ]);

        if ($act->extensionPlan && method_exists($act->extensionPlan, 'recordAuditTrail')) {
            $act->extensionPlan->recordAuditTrail(
                'Milestone Proof Uploaded via Repository',
                "Document proof for milestone '{$act->title}' was uploaded ({$doc->original_filename}) by ".($user?->name ?: 'Authorized User').'.',
                $user,
                $doc->id
            );
        }
    }

    /**
     * Secure file download.
     */
    public function download(int $id): StreamedResponse|BinaryFileResponse|Response|RedirectResponse
    {
        $doc = UploadedDocument::find($id);

        if (! $doc) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => null,
                'requestedId' => $id,
                'expectedPath' => null,
                'disk' => null,
                'inst' => $inst,
                'errorMessage' => "Document record #{$id} was not found in the institutional vault registry.",
            ], 404);
        }

        if ($doc->disk === 'external' || str_starts_with((string) $doc->file_path, 'http')) {
            DocumentAuditTrail::record([
                'tenant_id' => $doc->tenant_id,
                'campus_id' => $doc->campus_id,
                'module' => $doc->module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $doc->documentable_type,
                'documentable_id' => $doc->documentable_id,
                'action' => 'downloaded',
                'version' => $doc->version,
                'summary' => "Accessed external document link: {$doc->original_filename} (v{$doc->version})",
                'sha256_hash' => $doc->sha256_hash,
                'filename' => $doc->original_filename,
                'file_size' => $doc->file_size,
            ], request());

            return redirect()->away($doc->file_path);
        }

        $exists = false;
        $expectedPath = null;
        try {
            $exists = Storage::disk($doc->disk)->exists($doc->file_path);
            $expectedPath = Storage::disk($doc->disk)->path($doc->file_path);
        } catch (\Throwable $e) {
            $expectedPath = $doc->file_path;
        }

        if (! $exists) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => $doc,
                'requestedId' => $id,
                'expectedPath' => $expectedPath,
                'disk' => $doc->disk,
                'inst' => $inst,
                'errorMessage' => "Physical file \"{$doc->original_filename}\" is missing on server storage volume.",
            ], 404);
        }

        // Record Centralized Download Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'downloaded',
            'version' => $doc->version,
            'summary' => "Downloaded document: {$doc->original_filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $doc->original_filename,
            'file_size' => $doc->file_size,
            'file_size' => $doc->file_size,
        ], request());

        return DocumentWatermarkService::stream($doc, Auth::user(), true);
    }

    /**
     * Inline file stream / preview (PDF or image).
     */
    public function preview(int $id): BinaryFileResponse|StreamedResponse|Response|RedirectResponse
    {
        $doc = UploadedDocument::find($id);

        if (! $doc) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => null,
                'requestedId' => $id,
                'expectedPath' => null,
                'disk' => null,
                'inst' => $inst,
                'errorMessage' => "Document record #{$id} was not found in the institutional vault registry.",
            ], 404);
        }

        if ($doc->disk === 'external' || str_starts_with((string) $doc->file_path, 'http')) {
            DocumentAuditTrail::record([
                'tenant_id' => $doc->tenant_id,
                'campus_id' => $doc->campus_id,
                'module' => $doc->module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $doc->documentable_type,
                'documentable_id' => $doc->documentable_id,
                'action' => 'previewed',
                'version' => $doc->version,
                'summary' => "Opened external document link preview: {$doc->original_filename} (v{$doc->version})",
                'sha256_hash' => $doc->sha256_hash,
                'filename' => $doc->original_filename,
                'file_size' => $doc->file_size,
            ], request());

            return redirect()->away($doc->file_path);
        }

        $exists = false;
        $expectedPath = null;
        try {
            $exists = Storage::disk($doc->disk)->exists($doc->file_path);
            $expectedPath = Storage::disk($doc->disk)->path($doc->file_path);
        } catch (\Throwable $e) {
            $expectedPath = $doc->file_path;
        }

        if (! $exists) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => $doc,
                'requestedId' => $id,
                'expectedPath' => $expectedPath,
                'disk' => $doc->disk,
                'inst' => $inst,
                'errorMessage' => "Physical file \"{$doc->original_filename}\" is missing on server storage volume.",
            ], 404);
        }

        // Record Centralized Preview Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'previewed',
            'version' => $doc->version,
            'summary' => "Previewed/Streamed document: {$doc->original_filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $doc->original_filename,
            'file_size' => $doc->file_size,
        ], request());

        return DocumentWatermarkService::stream($doc, Auth::user(), false);
    }

    /**
     * Soft delete uploaded document.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $doc = UploadedDocument::findOrFail($id);
        $filename = $doc->original_filename;

        // Record Centralized Deletion Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'deleted',
            'version' => $doc->version,
            'summary' => "Archived/Deleted document: {$filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $filename,
            'file_size' => $doc->file_size,
        ], request());

        // Find any linked activity before deleting
        $linkedActivity = ExtensionPlanActivity::where('uploaded_document_id', $doc->id)
            ->orWhere(function ($q) use ($doc) {
                $q->where('id', $doc->documentable_id)
                    ->where('documentable_type', ExtensionPlanActivity::class);
            })->first();

        $doc->delete();

        if ($linkedActivity) {
            $remaining = UploadedDocument::where('documentable_type', ExtensionPlanActivity::class)
                ->where('documentable_id', $linkedActivity->id)
                ->where('id', '!=', $doc->id)
                ->count();
            if ($remaining === 0) {
                $linkedActivity->update([
                    'uploaded_document_id' => null,
                    'document_url' => null,
                    'document_filename' => null,
                    'document_disk' => null,
                    'document_mime_type' => null,
                    'document_size' => null,
                    'document_hash' => null,
                    'uploaded_at' => null,
                    'uploaded_by_user_id' => null,
                    'file_path' => null,
                    'file_name' => null,
                    'status' => 'pending',
                    'completed_at' => null,
                ]);
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Attachment \"{$filename}\" removed successfully.",
            ]);
        }

        return back()->with('success', "Attachment \"{$filename}\" removed successfully.");
    }

    /**
     * Fetch list of attachments for a specific model (JSON).
     */
    public function list(Request $request): JsonResponse
    {
        $modelClass = self::resolveModelClass($request->query('documentable_type'));
        $modelId = (int) $request->query('documentable_id');

        abort_unless($modelClass && $modelId, 400, 'Invalid documentable parameters.');

        $documents = UploadedDocument::with(['uploader'])
            ->where('documentable_type', $modelClass)
            ->where('documentable_id', $modelId)
            ->orderBy('is_physically_signed', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $documents->count(),
            'physically_signed_count' => $documents->where('is_physically_signed', true)->count(),
            'documents' => $documents,
        ]);
    }

    /**
     * Compute actual server and system disk metrics.
     *
     * @return array<string, mixed>
     */
    protected function getSystemStorageMetrics(int $vaultBytes): array
    {
        $storageDir = storage_path();
        $diskTotal = (float) (@disk_total_space($storageDir) ?: 0);
        $diskFree = (float) (@disk_free_space($storageDir) ?: 0);
        $diskUsed = max(0, $diskTotal - $diskFree);
        $percent = $diskTotal > 0 ? (int) round(($diskUsed / $diskTotal) * 100) : 0;

        $status = $percent >= 90 ? 'danger' : ($percent >= 75 ? 'warning' : 'success');
        $statusLabel = $percent >= 90 ? 'CRITICAL' : ($percent >= 75 ? 'WARNING' : 'HEALTHY');

        return [
            'total_bytes' => $diskTotal,
            'free_bytes' => $diskFree,
            'used_bytes' => $diskUsed,
            'percent' => $percent,
            'status' => $status,
            'status_label' => $statusLabel,
            'total_formatted' => $this->formatBytes($diskTotal),
            'free_formatted' => $this->formatBytes($diskFree),
            'used_formatted' => $this->formatBytes($diskUsed),
            'vault_formatted' => $this->formatBytes($vaultBytes),
        ];
    }

    /**
     * Format byte values into human-readable strings.
     */
    protected function formatBytes(float|int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        $floor = (int) floor($base);
        $index = min($floor, count($units) - 1);

        return round(pow(1024, $base - $floor), $precision).' '.$units[$index];
    }
}
