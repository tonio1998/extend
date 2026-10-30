<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenCharterStep extends Model
{
    use HasFactory;

    protected $table = 'dts_citizen_charter_steps';

    protected $fillable = [
        'charter_id',
        'step_number',
        'client_step',
        'agency_action',
        'office_name',
        'unit_id',
        'processing_time',
        'person_responsible',
        'fees',
    ];

    public function charter(): BelongsTo
    {
        return $this->belongsTo(CitizenCharter::class, 'charter_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
