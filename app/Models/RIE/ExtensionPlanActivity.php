<?php

namespace App\Models\RIE;

use App\Models\SaaS\Tenant;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class ExtensionPlanActivity extends Model
{
    use HasFactory;

    protected $table = 'extension_plan_activities';

    protected $fillable = [
        'tenant_id',
        'extension_plan_id',
        'sort_order',
        'title',
        'target_date',
        'start_date',
        'end_date',
        'is_na',
        'status',
        'remarks',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_at',
        'uploaded_by_user_id',
        'uploaded_document_id',
        'completed_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_na' => 'boolean',
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $appends = [
        'is_completed',
        'file_url',
        'formatted_file_size',
        'file_icon',
    ];

    public function extensionPlan(): BelongsTo
    {
        return $this->belongsTo(ExtensionPlan::class, 'extension_plan_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function uploadedDocument(): BelongsTo
    {
        return $this->belongsTo(UploadedDocument::class, 'uploaded_document_id');
    }

    public function uploadedDocuments(): MorphMany
    {
        return $this->morphMany(UploadedDocument::class, 'documentable')->latest('id');
    }

    /**
     * Get list of all documents/links attached to this activity.
     */
    public function getDocumentsListAttribute(): array
    {
        $docs = $this->relationLoaded('uploadedDocuments')
            ? $this->uploadedDocuments
            : $this->uploadedDocuments()->get();

        if ($docs->isNotEmpty()) {
            return $docs->map(function (UploadedDocument $doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'file_name' => $doc->original_filename,
                    'file_url' => $doc->download_url,
                    'preview_url' => $doc->preview_url,
                    'formatted_file_size' => $doc->formatted_file_size,
                    'file_icon' => $doc->file_icon,
                    'is_gdrive' => $doc->disk === 'external' || str_contains((string) $doc->file_path, 'drive.google.com'),
                    'uploaded_at' => $doc->created_at?->format('M d, Y h:i A'),
                    'uploaded_by' => $doc->uploader_name ?: ($doc->uploader?->name ?? 'System'),
                ];
            })->all();
        }

        // Fallback to legacy single file fields if no morphMany records yet
        if (! empty($this->file_path)) {
            $isGdrive = str_contains($this->file_path, 'drive.google.com');

            return [[
                'id' => $this->uploaded_document_id ?: 0,
                'title' => $this->file_name ?: ($isGdrive ? 'Google Drive Evidence' : 'Milestone Proof'),
                'file_name' => $this->file_name ?: ($isGdrive ? 'Google Drive Link' : 'Uploaded File'),
                'file_url' => $this->file_url,
                'preview_url' => $this->preview_url,
                'formatted_file_size' => $this->formatted_file_size,
                'file_icon' => $this->file_icon,
                'is_gdrive' => $isGdrive,
                'uploaded_at' => $this->uploaded_at?->format('M d, Y h:i A'),
                'uploaded_by' => $this->uploadedByUser?->name,
            ]];
        }

        return [];
    }

    /**
     * Determine if this milestone activity is completed (proof uploaded or marked completed).
     */
    public function getIsCompletedAttribute(): bool
    {
        return ! empty($this->file_path)
            || ! empty($this->uploaded_document_id)
            || $this->status === 'completed'
            || ! is_null($this->completed_at)
            || (! empty($this->documents_list));
    }

    /**
     * Get direct download/access URL for the attached wet-signed proof.
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://'))) {
            return $this->file_path;
        }

        if ($this->uploaded_document_id && $this->relationLoaded('uploadedDocument') && $this->uploadedDocument) {
            return $this->uploadedDocument->download_url;
        }

        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }

        return null;
    }

    /**
     * Get preview URL if viewable in browser.
     */
    public function getPreviewUrlAttribute(): ?string
    {
        if ($this->file_path && (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://'))) {
            return $this->file_path;
        }

        if ($this->uploaded_document_id && $this->relationLoaded('uploadedDocument') && $this->uploadedDocument) {
            return $this->uploadedDocument->preview_url;
        }

        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }

        return null;
    }

    /**
     * Formatted human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if ($this->file_path && (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://'))) {
            return str_contains($this->file_path, 'drive.google.com') ? 'Google Drive Link' : 'External Link';
        }

        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 1).' '.$units[$pow];
    }

    /**
     * File icon class based on file extension / mime.
     */
    public function getFileIconAttribute(): string
    {
        if ($this->file_path && (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://'))) {
            if (str_contains($this->file_path, 'drive.google.com') || str_contains((string) $this->file_name, 'Google Drive')) {
                return 'mdi-google-drive text-success';
            }

            return 'mdi-link-variant text-primary';
        }

        $ext = strtolower(pathinfo($this->file_name ?? $this->file_path ?? '', PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'mdi-file-pdf-box text-danger',
            'doc', 'docx' => 'mdi-file-word-box text-primary',
            'xls', 'xlsx' => 'mdi-file-excel-box text-success',
            'png', 'jpg', 'jpeg', 'webp' => 'mdi-file-image text-info',
            default => 'mdi-file-document-outline text-secondary',
        };
    }

    /**
     * Parse target date string into structured start_date, end_date, and is_na.
     * Supports single dates ("March 15, 2026"), ranges ("May 10 – May 12, 2026"), and N/A.
     *
     * @return array{start_date: ?string, end_date: ?string, is_na: bool}
     */
    public static function parseDateRange(?string $text): array
    {
        if (! $text || strtolower(trim($text)) === 'n/a') {
            return [
                'start_date' => null,
                'end_date' => null,
                'is_na' => strtolower(trim((string) $text)) === 'n/a',
            ];
        }

        $text = trim($text);

        // Check for range separators: " – ", " — ", " to ", " - "
        $parts = preg_split('/\s*(?:–|—|-|\bto\b)\s*/u', $text);
        if (count($parts) >= 2) {
            $p1 = trim($parts[0]);
            $p2 = trim($parts[1]);

            try {
                $d2 = Carbon::parse($p2);
                $d1 = null;
                try {
                    $d1 = Carbon::parse($p1);
                } catch (\Throwable) {
                    $d1 = Carbon::parse($p1.', '.$d2->year);
                }

                return [
                    'start_date' => $d1?->toDateString(),
                    'end_date' => $d2->toDateString(),
                    'is_na' => false,
                ];
            } catch (\Throwable) {
                // Not standard dates, maybe text range like "Q1 – Q2"
            }
        }

        try {
            $d = Carbon::parse($text);

            return [
                'start_date' => $d->toDateString(),
                'end_date' => $d->toDateString(),
                'is_na' => false,
            ];
        } catch (\Throwable) {
            return [
                'start_date' => null,
                'end_date' => null,
                'is_na' => false,
            ];
        }
    }
}
