<?php

namespace App\Models\SaaS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatoryMatrix extends Model
{
    use HasFactory;

    protected $table = 'sys_signatory_matrices';

    protected $fillable = [
        'tenant_id',
        'process_type',
        'min_amount',
        'max_amount',
        'step_order',
        'role_title',
        'employee_name',
        'designation',
        'is_mandatory',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'step_order' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
