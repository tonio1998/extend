<?php

namespace App\Models\RIE;

use App\Models\SaaS\Campus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtensionBudgetAllocation extends Model
{
    use HasFactory;

    protected $table = 'extension_budget_allocations';

    protected $fillable = [
        'extension_budget_id',
        'campus_id',
        'category_name',
        'allocated_amount',
        'remarks',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(ExtensionBudget::class, 'extension_budget_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
}
