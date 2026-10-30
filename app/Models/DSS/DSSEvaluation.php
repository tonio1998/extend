<?php

namespace App\Models\DSS;

use App\Models\SaaS\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DSSEvaluation extends Model
{
    use HasFactory;

    protected $table = 'dss_evaluations';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'evaluation_code',
        'title',
        'category',
        'model_type',
        'criteria_data',
        'pairwise_matrix',
        'calculated_weights',
        'consistency_ratio',
        'is_consistent',
        'alternatives_data',
        'decision_matrix',
        'topsis_scores',
        'recommended_alternative',
        'executive_summary',
    ];

    protected $casts = [
        'criteria_data' => 'array',
        'pairwise_matrix' => 'array',
        'calculated_weights' => 'array',
        'consistency_ratio' => 'float',
        'is_consistent' => 'boolean',
        'alternatives_data' => 'array',
        'decision_matrix' => 'array',
        'topsis_scores' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
