<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoutingTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_routing_templates';

    protected $fillable = [
        'tenant_id',
        'template_name',
        'description',
        'doc_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RoutingTemplateStep::class, 'template_id')->orderBy('step_number', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
