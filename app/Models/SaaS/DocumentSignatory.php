<?php

namespace App\Models\SaaS;

use App\Models\Dts\DocumentType;
use App\Models\Dts\Records;
use App\Models\Employee;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSignatory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sys_document_signatories';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'matrix_id',
        'doc_type_id',
        'document_type',
        'document_id',
        'dts_record_id',
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
        'status',
        'requires_digital_signature',
        'is_mandatory',
        'signed_at',
        'signed_by_user_id',
        'signature_img',
        'signature_type',
        'signature_hash',
        'ip_address',
        'user_agent',
        'remarks',
        'metadata',
    ];

    protected $casts = [
        'requires_digital_signature' => 'boolean',
        'is_mandatory' => 'boolean',
        'signed_at' => 'datetime',
        'metadata' => 'array',
        'sort_order' => 'integer',
        'position_column' => 'integer',
        'col_span' => 'integer',
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

    public function matrix(): BelongsTo
    {
        return $this->belongsTo(DocumentSignatoryMatrix::class, 'matrix_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function signedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }

    public function dtsRecord(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id')->whereRaw('1 = 0');
    }

    /**
     * Polymorphic parent document (ExtensionPlan, Memorandum, Policy, etc.)
     */
    public function document(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'document_type', 'document_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Scopes for "For Signature" Queues
    // ──────────────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    /**
     * Scope for filtering all documents pending signature for a specific user / employee.
     */
    public function scopeForSignatoryUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if (! empty($user->employee_id)) {
                $q->orWhere('employee_id', $user->employee_id);
            }
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if a given user account is authorized to sign this instance.
     */
    public function canBeSignedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        if ($this->user_id && $this->user_id === $user->id) {
            return true;
        }

        if ($this->employee_id && $user->employee_id && $this->employee_id === $user->employee_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if this signatory's turn has arrived in sequential order.
     * All prior signatories with a lower sort_order that are mandatory must be signed first.
     */
    public function isReadyToSign(): bool
    {
        if ($this->isSigned()) {
            return false;
        }

        // Check if any mandatory signatory with earlier sort_order is still pending
        return ! static::where('document_type', $this->document_type)
            ->where('document_id', $this->document_id)
            ->where('sort_order', '<', $this->sort_order)
            ->where('is_mandatory', true)
            ->where('status', '!=', 'signed')
            ->exists();
    }

    /**
     * Check if the given user can sign this step right now (user is authorized AND sequential turn has arrived).
     */
    public function canSignNow(?User $user): bool
    {
        return $this->canBeSignedBy($user) && $this->isReadyToSign();
    }

    public function getDocumentTitleAttribute(): string
    {
        $doc = $this->document;
        if (! $doc) {
            return 'Document #'.$this->document_id;
        }

        return $doc->title ?? ($doc->extension_activity ?? ($doc->subject ?? 'Document #'.$this->document_id));
    }

    public function getDocumentCodeAttribute(): string
    {
        $doc = $this->document;
        if (! $doc) {
            return 'DOC-'.$this->document_id;
        }

        return $doc->proposal_code ?? ($doc->plan_code ?? ($doc->memo_number ?? ($doc->policy_number ?? ($doc->tracking_number ?? 'DOC-'.$this->document_id))));
    }

    public function getDocumentUrlAttribute(): ?string
    {
        $doc = $this->document;
        if (! $doc) {
            return null;
        }

        if ($doc instanceof ExtensionProposal) {
            return route('dss.documents.view', ['type' => 'extension_proposal', 'id' => $doc->id]);
        }

        if ($doc instanceof ExtensionPlan) {
            return route('dss.documents.view', ['type' => 'extension_plan', 'id' => $doc->id]);
        }

        if ($doc instanceof UniversityMemorandum) {
            return route('dss.documents.view', ['type' => 'memo', 'id' => $doc->id]);
        }

        if ($doc instanceof InstitutionalPolicy) {
            return route('dss.documents.view', ['type' => 'policy', 'id' => $doc->id]);
        }

        if ($doc instanceof Records) {
            return route('dss.documents.view', ['type' => 'dts', 'id' => $doc->id]);
        }

        return null;
    }

    public function getWorkbenchUrlAttribute(): ?string
    {
        $doc = $this->document;
        if (! $doc) {
            return null;
        }

        if ($doc instanceof ExtensionProposal) {
            return route('dss.rie.proposals.show', $doc->getRouteKey());
        }

        if ($doc instanceof ExtensionPlan) {
            return route('dss.rie.plans.show', $doc->getRouteKey());
        }

        if ($doc instanceof UniversityMemorandum) {
            return route('dss.memos.show', $doc->id);
        }

        if ($doc instanceof InstitutionalPolicy) {
            return route('dss.policies.show', $doc->id);
        }

        return null;
    }

    public function getDocumentTypeBadgeAttribute(): string
    {
        $type = $this->document_type;
        if (str_contains($type, 'ExtensionProposal')) {
            return 'Extension Proposal';
        }
        if (str_contains($type, 'ExtensionPlan')) {
            return 'Extension Plan';
        }
        if (str_contains($type, 'UniversityMemorandum')) {
            return 'University Memorandum';
        }
        if (str_contains($type, 'InstitutionalPolicy')) {
            return 'Institutional Policy';
        }

        return class_basename($type);
    }
}
