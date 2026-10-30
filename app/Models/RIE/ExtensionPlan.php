<?php

namespace App\Models\RIE;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Employee;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use App\Models\User;
use App\Traits\HasCentralizedDocuments;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtensionPlan extends Model
{
    use HasCentralizedDocuments, HasFactory, SoftDeletes;

    protected $table = 'extension_plans';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'academic_program_id',
        'dts_record_id',
        'plan_code',
        'tracking_number',
        'program_name',
        'extension_activity',
        'objectives',
        'sub_extension_proposal_date',
        'sub_activity_design_date',
        'sub_purchase_request_date',
        'moa_signing_date',
        'materials_arrival_mode',
        'materials_arrival_start',
        'materials_arrival_end',
        'materials_arrival_dates',
        'materials_arrival_date',
        'conduct_activity_mode',
        'conduct_activity_start',
        'conduct_activity_end',
        'conduct_activity_dates',
        'conduct_activity_date',
        'sub_narrative_report_date',
        'monitoring_feedback_date',
        'monitoring_feedback_end_date',
        'beneficiaries',
        'target_beneficiaries_count',
        'proposed_budget',
        'extensionists_count',
        'prepared_by_user_id',
        'prepared_by_employee_id',
        'prepared_by_name',
        'prepared_by_designation',
        'prepared_at',
        'reviewed_by_user_id',
        'reviewed_by_employee_id',
        'reviewed_by_name',
        'reviewed_by_designation',
        'reviewed_at',
        'noted_by_employee_id',
        'noted_by_name',
        'noted_by_designation',
        'noted_at',
        'approved_by_employee_id',
        'approved_by_name',
        'approved_by_designation',
        'approved_at',
        'concurred_by_employee_id',
        'concurred_by_name',
        'concurred_by_designation',
        'concurred_at',
        'concurred_by_2_employee_id',
        'concurred_by_2_name',
        'concurred_by_2_designation',
        'concurred_by_2_at',
        'status',
        'remarks',
        'metadata',
    ];

    protected $casts = [
        'sub_extension_proposal_date' => 'date',
        'sub_activity_design_date' => 'date',
        'sub_purchase_request_date' => 'date',
        'moa_signing_date' => 'date',
        'materials_arrival_start' => 'date',
        'materials_arrival_end' => 'date',
        'materials_arrival_dates' => 'array',
        'conduct_activity_start' => 'date',
        'conduct_activity_end' => 'date',
        'conduct_activity_dates' => 'array',
        'sub_narrative_report_date' => 'date',
        'monitoring_feedback_date' => 'date',
        'monitoring_feedback_end_date' => 'date',
        'prepared_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'noted_at' => 'datetime',
        'approved_at' => 'datetime',
        'concurred_at' => 'datetime',
        'concurred_by_2_at' => 'datetime',
        'target_beneficiaries_count' => 'integer',
        'extensionists_count' => 'integer',
        'proposed_budget' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function academicProgram(): BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    public function dtsRecord(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id')->whereRaw('1 = 0');
    }

    public function preparedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function preparedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prepared_by_employee_id');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function reviewedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewed_by_employee_id');
    }

    public function notedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'noted_by_employee_id');
    }

    public function approvedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by_employee_id');
    }

    public function concurredByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'concurred_by_employee_id');
    }

    public function concurredBy2Employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'concurred_by_2_employee_id');
    }

    public function getFormattedBudgetAttribute(): string
    {
        return '₱'.number_format((float) $this->proposed_budget, 2);
    }

    public function getEncryptedIdAttribute(): string
    {
        return dss_encrypt_id($this->id);
    }

    public function getRouteKey(): string
    {
        return dss_encrypt_id($this->id);
    }

    public function getFormattedMaterialsArrivalAttribute(): string
    {
        if ($this->materials_arrival_mode === 'range' && $this->materials_arrival_start) {
            if ($this->materials_arrival_end && $this->materials_arrival_end->ne($this->materials_arrival_start)) {
                return $this->materials_arrival_start->format('F d, Y').' – '.$this->materials_arrival_end->format('F d, Y');
            }

            return $this->materials_arrival_start->format('F d, Y');
        }

        if ($this->materials_arrival_mode === 'multiple' && ! empty($this->materials_arrival_dates)) {
            $formattedDates = array_map(function ($d) {
                try {
                    return Carbon::parse($d)->format('M d, Y');
                } catch (\Throwable) {
                    return $d;
                }
            }, (array) $this->materials_arrival_dates);

            return implode(', ', $formattedDates);
        }

        if ($this->materials_arrival_start) {
            return $this->materials_arrival_start->format('F d, Y');
        }

        return (string) ($this->materials_arrival_date ?: '____________');
    }

    public function getFormattedConductActivityAttribute(): string
    {
        if ($this->conduct_activity_mode === 'range' && $this->conduct_activity_start) {
            if ($this->conduct_activity_end && $this->conduct_activity_end->ne($this->conduct_activity_start)) {
                return $this->conduct_activity_start->format('F d, Y').' – '.$this->conduct_activity_end->format('F d, Y');
            }

            return $this->conduct_activity_start->format('F d, Y');
        }

        if ($this->conduct_activity_mode === 'multiple' && ! empty($this->conduct_activity_dates)) {
            $formattedDates = array_map(function ($d) {
                try {
                    return Carbon::parse($d)->format('M d, Y');
                } catch (\Throwable) {
                    return $d;
                }
            }, (array) $this->conduct_activity_dates);

            return implode(', ', $formattedDates);
        }

        if ($this->conduct_activity_start) {
            return $this->conduct_activity_start->format('F d, Y');
        }

        return (string) ($this->conduct_activity_date ?: '____________');
    }

    public function getCalendarEventsAttribute(): array
    {
        $events = [];

        if ($this->conduct_activity_start) {
            $events[] = [
                'id' => 'conduct_'.$this->id,
                'title' => 'Conduct: '.$this->extension_activity,
                'start' => $this->conduct_activity_start->toDateString(),
                'end' => $this->conduct_activity_end ? $this->conduct_activity_end->addDay()->toDateString() : $this->conduct_activity_start->toDateString(),
                'allDay' => true,
                'color' => '#059669',
                'category' => 'conduct_activity',
                'url' => route('dss.rie.plans.show', $this->getRouteKey()),
            ];
        } elseif (! empty($this->conduct_activity_dates)) {
            foreach ($this->conduct_activity_dates as $i => $d) {
                $events[] = [
                    'id' => 'conduct_'.$this->id.'_'.$i,
                    'title' => 'Conduct: '.$this->extension_activity,
                    'start' => Carbon::parse($d)->toDateString(),
                    'allDay' => true,
                    'color' => '#059669',
                    'category' => 'conduct_activity',
                    'url' => route('dss.rie.plans.show', $this->getRouteKey()),
                ];
            }
        }

        if ($this->materials_arrival_start) {
            $events[] = [
                'id' => 'materials_'.$this->id,
                'title' => 'Materials: '.$this->extension_activity,
                'start' => $this->materials_arrival_start->toDateString(),
                'end' => $this->materials_arrival_end ? $this->materials_arrival_end->addDay()->toDateString() : $this->materials_arrival_start->toDateString(),
                'allDay' => true,
                'color' => '#d97706',
                'category' => 'arrival_materials',
                'url' => route('dss.rie.plans.show', $this->getRouteKey()),
            ];
        }

        if ($this->moa_signing_date) {
            $events[] = [
                'id' => 'moa_'.$this->id,
                'title' => 'MOA Signing: '.$this->extension_activity,
                'start' => $this->moa_signing_date->toDateString(),
                'allDay' => true,
                'color' => '#4f46e5',
                'category' => 'moa_signing',
                'url' => route('dss.rie.plans.show', $this->getRouteKey()),
            ];
        }

        if ($this->monitoring_feedback_date) {
            $events[] = [
                'id' => 'monitoring_'.$this->id,
                'title' => 'Monitoring/Feedback: '.$this->extension_activity,
                'start' => $this->monitoring_feedback_date->toDateString(),
                'end' => $this->monitoring_feedback_end_date ? $this->monitoring_feedback_end_date->addDay()->toDateString() : $this->monitoring_feedback_date->toDateString(),
                'allDay' => true,
                'color' => '#0284c7',
                'category' => 'monitoring',
                'url' => route('dss.rie.plans.show', $this->getRouteKey()),
            ];
        }

        return $events;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'submitted' => [
                'label' => 'SUBMITTED (IN ROUTE)',
                'class' => 'bg-info text-dark',
                'icon' => 'mdi-send-check',
            ],
            'noted' => [
                'label' => 'NOTED BY EXTENSION HEAD',
                'class' => 'bg-primary text-white',
                'icon' => 'mdi-check',
            ],
            'approved' => [
                'label' => 'APPROVED BY DEAN',
                'class' => 'bg-success text-white',
                'icon' => 'mdi-check-decagram',
            ],
            'returned' => [
                'label' => 'RETURNED FOR REVISION',
                'class' => 'bg-warning text-dark',
                'icon' => 'mdi-alert-circle',
            ],
            default => [
                'label' => 'DRAFT PLAN',
                'class' => 'bg-secondary text-white',
                'icon' => 'mdi-file-document-edit-outline',
            ],
        };
    }

    public function extensionProposal(): HasOne
    {
        return $this->hasOne(ExtensionProposal::class, 'extension_plan_id');
    }

    public function canCreateProposal(): bool
    {
        return $this->status === 'approved';
    }

    public function getTrackingNumberAttribute(): ?string
    {
        return $this->attributes['tracking_number'] ?? ($this->metadata['tracking_number'] ?? null);
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        if (in_array($this->status, ['draft', 'revision_requested'], true)) {
            return $this->prepared_by_user_id === $user->id;
        }

        return false;
    }

    public function canBeDeletedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        return $this->status === 'draft' && $this->prepared_by_user_id === $user->id;
    }
}
