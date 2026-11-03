<?php

namespace App\Models\RIE;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use App\Models\User;
use App\Services\RIE\DocumentCodeService;
use App\Traits\HasCentralizedDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityDesign extends Model
{
    use HasCentralizedDocuments, HasFactory, SoftDeletes;

    protected $table = 'extension_activity_designs';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'extension_proposal_id',
        'extension_plan_id',
        'dts_record_id',
        'design_code',
        'tracking_number',
        'title',
        'banner_program',
        'academic_program_id',
        'unit_id',
        'collaborating_agencies',
        'target_beneficiaries',
        'target_beneficiaries_count',
        'duration_text',
        'start_date',
        'end_date',
        'project_location',
        'project_leader_user_id',
        'project_leader_employee_id',
        'project_leader_name',
        'project_leader_title',
        'context_rationale',
        'general_objective',
        'specific_objectives',
        'program_matrix',
        'budget_total',
        'budget_snsu_share',
        'budget_partner_share',
        'budget_total_display',
        'status',
        'remarks',
        'metadata',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'specific_objectives' => 'array',
        'program_matrix' => 'array',
        'metadata' => 'array',
        'budget_total' => 'decimal:2',
        'budget_snsu_share' => 'decimal:2',
        'budget_partner_share' => 'decimal:2',
        'target_beneficiaries_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function getRouteKey(): string
    {
        return function_exists('dss_encrypt_id') ? dss_encrypt_id($this->id) : (string) $this->id;
    }

    /**
     * Parent Extension Proposal relationship.
     */
    public function extensionProposal(): BelongsTo
    {
        return $this->belongsTo(ExtensionProposal::class, 'extension_proposal_id');
    }

    /**
     * Parent Extension Plan relationship.
     */
    public function extensionPlan(): BelongsTo
    {
        return $this->belongsTo(ExtensionPlan::class, 'extension_plan_id');
    }

    /**
     * Implementing Department/Unit.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Academic Program.
     */
    public function academicProgram(): BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    /**
     * Campus.
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * Tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Project Leader User account.
     */
    public function projectLeaderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_leader_user_id');
    }

    /**
     * Project Leader Employee record.
     */
    public function projectLeaderEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_leader_employee_id');
    }

    /**
     * Formatted budget strings.
     */
    public function getFormattedBudgetTotalAttribute(): string
    {
        return '₱'.number_format((float) ($this->budget_total ?: 0), 2);
    }

    public function getFormattedSnsuShareAttribute(): string
    {
        return '₱'.number_format((float) ($this->budget_snsu_share ?: 0), 2);
    }

    public function getFormattedPartnerShareAttribute(): string
    {
        return '₱'.number_format((float) ($this->budget_partner_share ?: 0), 2);
    }

    // ── DELEGATING ACCESSORS (Directly linked to Extension Proposal — No Duplication) ──

    public function getTitleAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->title;
    }

    public function getBannerProgramAttribute(?string $value): ?string
    {
        return $value ?: ($this->extensionProposal?->banner_program ?: $this->extensionPlan?->program_name);
    }

    public function getUnitIdAttribute(?int $value): ?int
    {
        return $value ?: $this->extensionProposal?->unit_id;
    }

    public function getAcademicProgramIdAttribute(?int $value): ?int
    {
        return $value ?: $this->extensionProposal?->academic_program_id;
    }

    public function getCollaboratingAgenciesAttribute(?string $value): ?string
    {
        if ($value) {
            return $value;
        }
        $collab = $this->extensionProposal?->collaborating_agencies;

        return is_array($collab) ? implode(', ', $collab) : $collab;
    }

    public function getTargetBeneficiariesAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->beneficiaries;
    }

    public function getTargetBeneficiariesCountAttribute(?int $value): ?int
    {
        if ($value !== null && $value > 0) {
            return (int) $value;
        }
        $beneData = $this->extensionProposal?->target_beneficiaries_data;
        if (is_array($beneData) && count($beneData) > 0) {
            return count($beneData);
        }

        return (int) ($this->extensionPlan?->target_beneficiaries_count ?? 0);
    }

    public function getDurationTextAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->duration_text;
    }

    public function getStartDateAttribute($value)
    {
        return $value ?: $this->extensionProposal?->duration_start;
    }

    public function getEndDateAttribute($value)
    {
        return $value ?: $this->extensionProposal?->duration_end;
    }

    public function getProjectLocationAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->project_location;
    }

    public function getProjectLeaderNameAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->project_leader_name;
    }

    public function getProjectLeaderTitleAttribute(?string $value): ?string
    {
        return $value ?: ($this->extensionProposal?->project_leader_title ?: 'Project Leader');
    }

    public function getContextRationaleAttribute(?string $value): ?string
    {
        return $value ?: ($this->extensionProposal?->introduction ?: ($this->extensionProposal?->relevance_narrative ?: $this->extensionProposal?->background));
    }

    public function getGeneralObjectiveAttribute(?string $value): ?string
    {
        return $value ?: $this->extensionProposal?->general_objective;
    }

    public function getSpecificObjectivesAttribute($value): array
    {
        if (! empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        return $this->extensionProposal?->specific_objectives ?? [];
    }

    public function getProgramMatrixAttribute($value): array
    {
        if (! empty($value)) {
            return is_array($value) ? $value : (json_decode($value, true) ?: []);
        }

        return $this->extensionPlan?->milestones_list ?? ($this->extensionProposal?->extensionPlan?->milestones_list ?? []);
    }

    public function getBudgetTotalAttribute($value): float
    {
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        return (float) ($this->extensionProposal?->budget_cash_amount ?: ($this->extensionProposal?->budget_institution_share ?: 0));
    }

    public function getBudgetSnsuShareAttribute($value): float
    {
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        return (float) ($this->extensionProposal?->budget_institution_share ?: $this->budget_total);
    }

    public function getBudgetPartnerShareAttribute($value): float
    {
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        return (float) ($this->extensionProposal?->budget_partner_share ?: 0);
    }

    public function getBudgetTotalDisplayAttribute(?string $value): ?string
    {
        return $value ?: ($this->extensionProposal?->budget_total_display ?: ('PHP '.number_format($this->budget_total, 2)));
    }

    /**
     * Helper to retrieve any unique/custom section exclusive to this Activity Design.
     */
    public function getAdSection(string $key, mixed $default = null): mixed
    {
        return data_get($this->metadata, "ad_sections.{$key}", $default);
    }

    /**
     * Helper to set any unique/custom section exclusive to this Activity Design.
     */
    public function setAdSection(string $key, mixed $value): self
    {
        $meta = $this->metadata ?: [];
        data_set($meta, "ad_sections.{$key}", $value);
        $this->metadata = $meta;

        return $this;
    }

    /**
     * Link directly to an Extension Proposal (no duplicate storage needed).
     */
    public function populateFromProposal(ExtensionProposal $proposal): self
    {
        $this->extension_proposal_id = $proposal->id;
        $this->extension_plan_id = $proposal->extension_plan_id;
        $this->tenant_id = $proposal->tenant_id ?: 1;
        $this->campus_id = $proposal->campus_id;
        $this->academic_program_id = $proposal->academic_program_id;
        $this->unit_id = $proposal->unit_id;

        return $this;
    }

    /**
     * Generates a new unique Activity Design Code from Admin Settings (e.g. SNSU-ACT-2026-0001).
     */
    public static function generateCode(?int $tenantId = 1): string
    {
        return DocumentCodeService::generateActivityDesignCode($tenantId);
    }
}
