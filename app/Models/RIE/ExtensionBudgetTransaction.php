<?php

namespace App\Models\RIE;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtensionBudgetTransaction extends Model
{
    use HasFactory;

    protected $table = 'extension_budget_transactions';

    protected $fillable = [
        'extension_budget_id',
        'transaction_type',
        'amount',
        'reference_no',
        'reason',
        'authorized_by',
        'created_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(ExtensionBudget::class, 'extension_budget_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
