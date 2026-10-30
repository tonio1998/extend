<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutingTemplateStep extends Model
{
    use HasFactory;

    protected $table = 'dts_routing_template_steps';

    protected $fillable = [
        'template_id',
        'step_number',
        'unit_id',
        'unit_name',
        'for_action',
        'sla_hours',
        'sla_duration',
        'sla_unit',
        'processing_time',
        'is_mandatory',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'sla_hours' => 'integer',
        'sla_duration' => 'float',
        'is_mandatory' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(RoutingTemplate::class, 'template_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
