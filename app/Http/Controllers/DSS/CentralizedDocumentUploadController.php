<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Records;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentAuditTrail;
use App\Models\SaaS\UploadedDocument;
use App\Services\DSS\TenantContext;
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
            ExtensionPlan::class => 'rie',
            InstitutionalPolicy::class => 'policy',
            UniversityMemorandum::class => 'memo',
            Records::class => 'dts',
            default => 'general',
        };
    }

    /**
     * Centralized Institutional Document Repository Masterlist.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $moduleFilter = $request->query('module');
        $categoryFilter = $request->query('category');
        $physicallySignedOnly = $request->boolean('physically_signed');
        $search = trim((string) $request->query('search', ''));
        $campusId = $request->query('campus_id');

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

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%")
                    ->orWhere('sha256_hash', 'like', "%{$search}%")
                    ->orWhere('uploader_name', 'like', "%{$search}%")
                    ->orWhere('signatory_notes', 'like', "%{$search}%");
            });
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();
        $totalCount = UploadedDocument::where('tenant_id', $tenantId)->count();
        $signedCount = UploadedDocument::where('tenant_id', $tenantId)->where('is_physically_signed', true)->count();
        $recentAuditTrails = DocumentAuditTrail::where('tenant_id', $tenantId)->with(['user'])->latest()->take(15)->get();

        return view('dss.documents.repository', compact(
            'documents',
            'campuses',
            'totalCount',
            'signedCount',
            'recentAuditTrails',
            'moduleFilter',
            'categoryFilter',
            'physicallySignedOnly',
            'search',
            'campusId'
        ));
    }

    /**
     * Upload and attach a file to a record (or standalone).
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,png,jpg,jpeg,webp,gif,txt,zip',
            'documentable_type' => 'nullable|string|max:150',
            'documentable_id' => 'nullable|integer',
            'category' => 'nullable|string|max:80',
            'title' => 'nullable|string|max:255',
            'is_physically_signed' => 'nullable',
            'signed_date' => 'nullable|date',
            'signatory_notes' => 'nullable|string|max:1000',
            'module' => 'nullable|string|max:60',
        ]);

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $campusId = $user?->campus_id ?? $tenant?->campuses()->first()?->id;

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        // Compute SHA-256 for audit & authenticity verification
        $sha256Hash = hash_file('sha256', $file->getRealPath());

        // Derive target model & module
        $modelClass = self::resolveModelClass($validated['documentable_type'] ?? null);
        $targetId = $validated['documentable_id'] ?? null;
        $module = $validated['module'] ?? self::resolveModule($validated['documentable_type'] ?? null);

        // Category default
        $category = $validated['category'] ?: ($request->boolean('is_physically_signed') ? 'physically_signed_plan' : 'general');
        $isPhysicallySigned = $request->boolean('is_physically_signed') || $category === 'physically_signed_plan';

        // 1. SMART VERSIONING: Match by identical filename, SHA-256 hash, or explicit document ID
        // If a DIFFERENT file is uploaded (e.g. "FINAL TERM INTEG.pdf" vs "ITE 315 Plan"), it is a NEW separate attachment (v1.0)
        // If the SAME file / filename is re-uploaded, it auto-increments version (v1.0 -> v1.1 -> v1.2)
        $existingDocsQuery = UploadedDocument::where('tenant_id', $tenantId);
        $replacesDocId = $request->input('replaces_document_id');

        if ($replacesDocId) {
            $existingDocsQuery->where('id', $replacesDocId);
        } elseif ($modelClass && $targetId) {
            $existingDocsQuery->where('documentable_type', $modelClass)
                ->where('documentable_id', $targetId)
                ->where(function ($q) use ($sha256Hash, $originalFilename) {
                    $q->where('sha256_hash', $sha256Hash)
                        ->orWhere('original_filename', $originalFilename);
                });
        } else {
            $existingDocsQuery->where('sha256_hash', $sha256Hash);
        }

        // Find current active document for this record
        $existingActiveDoc = (clone $existingDocsQuery)->where('status', 'active')->latest('id')->first();

        // Calculate highest version recorded so far
        $allVersions = (clone $existingDocsQuery)->pluck('version')->toArray();
        $highestVersion = 1.0;
        foreach ($allVersions as $v) {
            if (is_numeric($v) && (float) $v > $highestVersion) {
                $highestVersion = (float) $v;
            }
        }

        $previousVersion = null;
        $version = '1.0';

        if ($existingActiveDoc || ! empty($allVersions)) {
            $previousVersion = $existingActiveDoc?->version ?: number_format($highestVersion, 1, '.', '');
            $nextVerNum = ((float) $previousVersion) + 0.1;
            if ($nextVerNum <= $highestVersion) {
                $nextVerNum = $highestVersion + 0.1;
            }
            $version = number_format($nextVerNum, 1, '.', '');
        }

        // Allow manual version override if explicitly provided
        if (! empty($request->input('version'))) {
            $version = trim($request->input('version'));
        }

        $changeNotes = $request->input('change_notes') ?? $request->input('signatory_notes');
        if (empty($changeNotes) && $previousVersion) {
            $changeNotes = "Re-uploaded document (auto-incremented from v{$previousVersion})";
        }

        // Archive/Supersede previous active document into audit trail
        if ($existingActiveDoc) {
            $existingActiveDoc->update(['status' => 'superseded']);

            // Log Superseded Audit Trail
            DocumentAuditTrail::record([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'uploaded_document_id' => $existingActiveDoc->id,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'action' => 'superseded',
                'version' => $existingActiveDoc->version,
                'previous_version' => null,
                'summary' => "Version {$existingActiveDoc->version} superseded by newly uploaded Version {$version}",
                'change_notes' => $changeNotes,
                'sha256_hash' => $existingActiveDoc->sha256_hash,
                'filename' => $existingActiveDoc->original_filename,
                'file_size' => $existingActiveDoc->file_size,
            ], $request);
        }

        // Title fallback
        $title = ! empty($validated['title'])
            ? trim($validated['title'])
            : ($isPhysicallySigned ? 'Physically Signed Copy — '.pathinfo($originalFilename, PATHINFO_FILENAME) : pathinfo($originalFilename, PATHINFO_FILENAME));

        // Generate sanitized unique storage path
        $year = date('Y');
        $month = date('m');
        $safeHash = Str::random(24);
        $fileName = "{$safeHash}.{$extension}";
        $storageDirectory = "documents/{$module}/{$year}/{$month}";

        $storedPath = $file->storeAs($storageDirectory, $fileName, 'public');

        // Create database record
        $uploadedDoc = UploadedDocument::create([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'module' => $module,
            'documentable_type' => $modelClass,
            'documentable_id' => $validated['documentable_id'] ?? null,
            'category' => $category,
            'title' => $title,
            'original_filename' => $originalFilename,
            'file_path' => $storedPath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'disk' => 'public',
            'sha256_hash' => $sha256Hash,
            'is_physically_signed' => $isPhysicallySigned,
            'signed_date' => $validated['signed_date'] ?? ($isPhysicallySigned ? now()->toDateString() : null),
            'signatory_notes' => $changeNotes,
            'version' => $version,
            'status' => 'active',
            'uploaded_by_user_id' => $user?->id,
            'uploader_name' => $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Signatory'),
            'metadata' => [
                'client_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'uploaded_at' => now()->toIso8601String(),
                'version' => $version,
                'previous_version' => $previousVersion,
            ],
        ]);

        // Record Centralized Audit Trail for Upload / Version Bump
        DocumentAuditTrail::record([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'module' => $module,
            'uploaded_document_id' => $uploadedDoc->id,
            'documentable_type' => $modelClass,
            'documentable_id' => $targetId,
            'action' => $previousVersion ? 'version_bumped' : ($isPhysicallySigned ? 'physically_signed_tagged' : 'uploaded'),
            'version' => $version,
            'previous_version' => $previousVersion,
            'summary' => $previousVersion
                ? "Uploaded revised Version {$version} (Supersedes v{$previousVersion}) of {$originalFilename}"
                : ($isPhysicallySigned ? "Uploaded official physically signed plan scan ({$originalFilename})" : "Uploaded new document ({$originalFilename})"),
            'change_notes' => $changeNotes,
            'sha256_hash' => $sha256Hash,
            'filename' => $originalFilename,
            'file_size' => $fileSize,
        ], $request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $previousVersion
                    ? "File re-uploaded. Version automatically incremented to v{$version} (previous v{$previousVersion} archived in audit trail)."
                    : "File \"{$originalFilename}\" uploaded successfully as Version 1.0.",
                'document' => $uploadedDoc->load('uploader'),
            ]);
        }

        return back()->with('success', $previousVersion
            ? "File re-uploaded. Version automatically incremented to v{$version} (v{$previousVersion} archived in audit trail)."
            : "File \"{$originalFilename}\" (v{$version}) attached successfully.");
    }

    /**
     * Secure file download.
     */
    public function download(int $id): StreamedResponse|BinaryFileResponse|Response
    {
        $doc = UploadedDocument::findOrFail($id);

        abort_unless(Storage::disk($doc->disk)->exists($doc->file_path), 404, 'File not found on storage.');

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
        ], request());

        return Storage::disk($doc->disk)->download($doc->file_path, $doc->original_filename);
    }

    /**
     * Inline file stream / preview (PDF or image).
     */
    public function preview(int $id): BinaryFileResponse|StreamedResponse|Response
    {
        $doc = UploadedDocument::findOrFail($id);

        abort_unless(Storage::disk($doc->disk)->exists($doc->file_path), 404, 'File not found on storage.');

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

        $fullPath = Storage::disk($doc->disk)->path($doc->file_path);

        return response()->file($fullPath, [
            'Content-Type' => $doc->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($doc->original_filename).'"',
        ]);
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

        $doc->delete();

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
}
