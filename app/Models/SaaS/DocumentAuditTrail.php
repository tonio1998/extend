<?php

namespace App\Models\SaaS;

use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentAuditTrail extends Model
{
    use HasFactory;

    protected $table = 'sys_document_audit_trails';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'module',
        'uploaded_document_id',
        'documentable_type',
        'documentable_id',
        'action',
        'version',
        'previous_version',
        'summary',
        'change_notes',
        'sha256_hash',
        'filename',
        'file_size',
        'user_id',
        'user_name',
        'user_designation',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to the uploaded document record.
     */
    public function uploadedDocument(): BelongsTo
    {
        return $this->belongsTo(UploadedDocument::class, 'uploaded_document_id');
    }

    /**
     * Polymorphic relation to parent entity (ExtensionPlan, Policy, Memo, Record, etc.).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship to the user who triggered the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Centralized static logger for recording document audit trails.
     *
     * @param array{
     *     action: string,
     *     summary: string,
     *     uploaded_document_id?: ?int,
     *     documentable_type?: ?string,
     *     documentable_id?: ?int,
     *     module?: ?string,
     *     version?: ?string,
     *     previous_version?: ?string,
     *     change_notes?: ?string,
     *     sha256_hash?: ?string,
     *     filename?: ?string,
     *     file_size?: ?int,
     *     metadata?: ?array,
     *     request?: ?Request
     * } $data
     */
    public static function record(array $data, ?Request $request = null): self
    {
        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $request = $request ?? request();

        $tenantId = $data['tenant_id'] ?? ($tenant?->id ?? 1);
        $campusId = $data['campus_id'] ?? ($user?->campus_id ?? $tenant?->campuses()->first()?->id);

        return self::create([
            'tenant_id' => $tenantId,
            'campus_id' => $campusId,
            'module' => $data['module'] ?? 'general',
            'uploaded_document_id' => $data['uploaded_document_id'] ?? null,
            'documentable_type' => $data['documentable_type'] ?? null,
            'documentable_id' => $data['documentable_id'] ?? null,
            'action' => $data['action'],
            'version' => $data['version'] ?? '1.0',
            'previous_version' => $data['previous_version'] ?? null,
            'summary' => $data['summary'],
            'change_notes' => $data['change_notes'] ?? null,
            'sha256_hash' => $data['sha256_hash'] ?? null,
            'filename' => $data['filename'] ?? null,
            'file_size' => $data['file_size'] ?? 0,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'user_designation' => $user?->designation ?? $user?->role?->name ?? null,
            'ip_address' => $request?->ip() ?? '127.0.0.1',
            'user_agent' => $request?->userAgent() ?? 'CLI/Unknown',
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Get a human-friendly action badge HTML / color config.
     */
    public function getActionBadgeAttribute(): array
    {
        return match ($this->action) {
            'uploaded' => ['class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'mdi-cloud-upload', 'label' => 'Uploaded'],
            'version_bumped' => ['class' => 'bg-primary-subtle text-primary border border-primary-subtle', 'icon' => 'mdi-source-branch', 'label' => 'New Version'],
            'superseded' => ['class' => 'bg-warning-subtle text-warning border border-warning-subtle', 'icon' => 'mdi-history', 'label' => 'Superseded'],
            'downloaded' => ['class' => 'bg-info-subtle text-info border border-info-subtle', 'icon' => 'mdi-download', 'label' => 'Downloaded'],
            'previewed' => ['class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle', 'icon' => 'mdi-eye', 'label' => 'Previewed'],
            'deleted' => ['class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'mdi-delete', 'label' => 'Archived/Deleted'],
            'physically_signed_tagged' => ['class' => 'bg-danger text-white', 'icon' => 'mdi-draw-pen', 'label' => 'Wet-Signed'],
            default => ['class' => 'bg-light text-dark border', 'icon' => 'mdi-information-outline', 'label' => ucfirst($this->action)],
        };
    }
}
