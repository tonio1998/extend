<?php

namespace App\Models\RIE;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SustainableDevelopmentGoal extends Model
{
    use HasFactory;

    protected $table = 'sustainable_development_goals';

    protected $fillable = [
        'goal_number',
        'code',
        'name',
        'short_name',
        'description',
        'color_hex',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'goal_number' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Extension proposals aligned to this SDG.
     */
    public function extensionProposals(): BelongsToMany
    {
        return $this->belongsToMany(
            ExtensionProposal::class,
            'extension_proposal_sdgs',
            'sdg_id',
            'extension_proposal_id'
        )->withPivot(['is_primary', 'relevance_notes'])->withTimestamps();
    }

    /**
     * Scope for active goals.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by goal number / sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('goal_number');
    }
}
