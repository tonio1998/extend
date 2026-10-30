<?php

namespace App\Models\SaaS;

use App\Models\Dts\DocumentType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSignatoryMatrix extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sys_document_signatory_matrices';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'doc_type_id',
        'process_type',
        'tier_role',
        'role_label',
        'assignment_type',
        'employee_id',
        'user_id',
        'signatory_name',
        'designation',
        'office',
        'sort_order',
        'position_column',
        'col_span',
        'alignment',
        'requires_digital_signature',
        'is_mandatory',
        'is_active',
        'signature_instructions',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'position_column' => 'integer',
        'col_span' => 'integer',
        'requires_digital_signature' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForProcess($query, string $processType)
    {
        return $query->where('process_type', $processType);
    }

    public function scopeForCampus($query, ?int $campusId = null)
    {
        if ($campusId) {
            return $query->where(function ($q) use ($campusId) {
                $q->where('campus_id', $campusId)
                    ->orWhereNull('campus_id');
            });
        }

        return $query->whereNull('campus_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Accessors & Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the active display name of the signatory.
     */
    public function getResolvedNameAttribute(): string
    {
        if ($this->assignment_type === 'dynamic_author') {
            return '[Document Author / Proponent]';
        }

        if ($this->assignment_type === 'dynamic_dept_head') {
            return '[Originating Department / Unit Head]';
        }

        if ($this->assignment_type === 'dynamic_campus_director') {
            return '[Campus Executive Director]';
        }

        if ($this->employee) {
            return $this->employee->full_name;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return $this->signatory_name ?: 'Designated Signatory';
    }

    /**
     * Resolve the active designation of the signatory.
     */
    public function getResolvedDesignationAttribute(): string
    {
        if ($this->employee && ! empty($this->employee->current_designation)) {
            return $this->employee->current_designation;
        }

        if ($this->designation) {
            return $this->designation;
        }

        return match ($this->tier_role) {
            'prepared' => 'Project Leader',
            'noted' => 'Extension Head / Unit Head',
            'approved' => 'University President / Director',
            default => 'Authorized Signatory',
        };
    }
}
