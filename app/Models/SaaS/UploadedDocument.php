<?php

namespace App\Models\SaaS;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sys_uploaded_documents';

    protected $guarded = ['id'];

    protected $casts = [
        'is_physically_signed' => 'boolean',
        'file_size' => 'integer',
        'signed_date' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_file_size',
        'file_icon',
        'download_url',
        'preview_url',
        'is_previewable',
        'category_label',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(DocumentAuditTrail::class, 'uploaded_document_id')->latest();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────────────────────────────────

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    public function getFileIconAttribute(): string
    {
        $ext = strtolower($this->extension ?: pathinfo($this->file_path ?? '', PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => 'mdi-file-pdf-box text-danger',
            'doc', 'docx' => 'mdi-file-word-box text-primary',
            'xls', 'xlsx', 'csv' => 'mdi-file-excel-box text-success',
            'ppt', 'pptx' => 'mdi-file-powerpoint-box text-warning',
            'png', 'jpg', 'jpeg', 'webp', 'gif' => 'mdi-file-image-outline text-info',
            'zip', 'rar', '7z' => 'mdi-folder-zip-outline text-secondary',
            'txt' => 'mdi-file-document-outline text-muted',
            default => 'mdi-file-outline text-secondary',
        };
    }

    public function getIsPreviewableAttribute(): bool
    {
        $ext = strtolower($this->extension ?: pathinfo($this->file_path ?? '', PATHINFO_EXTENSION));

        return in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'txt']);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('dss.documents.upload.download', $this->id);
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('dss.documents.upload.preview', $this->id);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'physically_signed_plan' => 'Physically Signed Copy',
            'activity_design' => 'Activity Design',
            'moa_mou' => 'MOA / MOU Copy',
            'budget_proposal' => 'Budget Proposal Breakdown',
            'narrative_report' => 'Narrative Accomplishment Report',
            'attendance_sheet' => 'Attendance Sheet',
            'resolution' => 'Board / Council Resolution',
            'annex' => 'Annex / Attachment',
            'supporting_evidence' => 'Supporting Verification Evidence',
            default => ucwords(str_replace('_', ' ', $this->category ?: 'General Attachment')),
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────────────────────────────

    public function scopePhysicallySigned(Builder $query): Builder
    {
        return $query->where('is_physically_signed', true);
    }

    public function scopeForModel(Builder $query, Model $model): Builder
    {
        return $query->where('documentable_type', $model->getMorphClass())
            ->where('documentable_id', $model->getKey());
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }
}
