<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitizenCharter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_citizen_charters';

    protected $fillable = [
        'tenant_id',
        'service_code',
        'service_name',
        'category',
        'classification',
        'transaction_type',
        'who_may_avail',
        'checklist_requirements',
        'fees',
        'standard_sla_days',
        'standard_sla_hours',
        'unit_id',
        'doc_type_id',
        'legal_basis',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'checklist_requirements' => 'array',
        'standard_sla_days' => 'integer',
        'standard_sla_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(CitizenCharterStep::class, 'charter_id')->orderBy('step_number');
    }
}
