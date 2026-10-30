<?php

namespace App\Models\RIE;

use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtensionBudget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'extension_budgets';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'fiscal_year',
        'budget_code',
        'title',
        'fund_cluster',
        'total_allocation',
        'allocated_gaa',
        'allocated_stf',
        'allocated_external',
        'realigned_amount',
        'status',
        'board_resolution_no',
        'approved_by',
        'approved_at',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_allocation' => 'decimal:2',
        'allocated_gaa' => 'decimal:2',
        'allocated_stf' => 'decimal:2',
        'allocated_external' => 'decimal:2',
        'realigned_amount' => 'decimal:2',
        'approved_at' => 'date',
    ];

    public function getRouteKey(): string
    {
        return function_exists('dss_encrypt_id') ? dss_encrypt_id($this->id) : (string) $this->id;
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ExtensionBudgetAllocation::class, 'extension_budget_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ExtensionBudgetTransaction::class, 'extension_budget_id')->latest();
    }

    public function getEffectiveTotalAttribute(): float
    {
        return (float) ($this->total_allocation + $this->realigned_amount);
    }

    public function proposalsQuery()
    {
        $fy = $this->fiscal_year;

        $query = ExtensionProposal::where('tenant_id', $this->tenant_id)
            ->where(function ($q) use ($fy) {
                $q->whereYear('duration_start', $fy)
                    ->orWhere(function ($sub) use ($fy) {
                        $sub->whereNull('duration_start')->whereYear('created_at', $fy);
                    });
            });

        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }

        return $query;
    }

    public function getUtilizedAmountAttribute(): float
    {
        return (float) $this->proposalsQuery()
            ->whereIn('status', ['Approved', 'approved'])
            ->sum('budget_cash_amount');
    }

    public function getCommittedAmountAttribute(): float
    {
        return (float) $this->proposalsQuery()
            ->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'In-Review', 'in-review'])
            ->sum('budget_cash_amount');
    }

    public function getDraftAmountAttribute(): float
    {
        return (float) $this->proposalsQuery()
            ->whereIn('status', ['Draft', 'draft', 'pending', 'returned'])
            ->sum('budget_cash_amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) ($this->effective_total - $this->utilized_amount);
    }

    public function getProjectedBalanceAttribute(): float
    {
        return (float) ($this->effective_total - ($this->utilized_amount + $this->committed_amount));
    }

    public function getBurnRateAttribute(): float
    {
        if ($this->effective_total <= 0) {
            return 0.0;
        }

        return round(($this->utilized_amount / $this->effective_total) * 100, 1);
    }

    public function getCommitmentRateAttribute(): float
    {
        if ($this->effective_total <= 0) {
            return 0.0;
        }

        return round((($this->utilized_amount + $this->committed_amount) / $this->effective_total) * 100, 1);
    }

    public function getHealthStatusAttribute(): array
    {
        $rate = $this->commitment_rate;

        if ($rate >= 100) {
            return [
                'status' => 'depleted',
                'label' => 'FULLY COMMITTED / OVER BUDGET',
                'badge' => 'bg-danger text-white',
                'progress_class' => 'bg-danger',
                'icon' => 'mdi-alert-octagon',
            ];
        }

        if ($rate >= 85) {
            return [
                'status' => 'warning',
                'label' => 'CRITICAL ALLOCATION (>85%)',
                'badge' => 'bg-warning text-dark',
                'progress_class' => 'bg-warning',
                'icon' => 'mdi-alert',
            ];
        }

        if ($rate >= 60) {
            return [
                'status' => 'moderate',
                'label' => 'HEALTHY UTILIZATION (60-85%)',
                'badge' => 'bg-info text-dark',
                'progress_class' => 'bg-info',
                'icon' => 'mdi-chart-line',
            ];
        }

        return [
            'status' => 'optimal',
            'label' => 'PLENTIFUL LIQUIDITY (<60%)',
            'badge' => 'bg-success text-white',
            'progress_class' => 'bg-success',
            'icon' => 'mdi-check-decagram',
        ];
    }
}
