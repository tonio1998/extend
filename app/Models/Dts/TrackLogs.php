<?php

namespace App\Models\Dts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackLogs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_logs';

    protected $fillable = [
        'tenant_id',
        'RecordID',
        'TransactionID',
        'QRID',
        'courier',
        'UnitID',
        'Destination',
        'forAction',
        'TrailStatus',
        'comments',
        'location',
        'Longitude',
        'Latitude',
        'status',
        'archived',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'archived' => 'integer',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(Records::class, 'TransactionID');
    }

    public function cour(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'UnitID');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'Destination');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'UnitID');
    }

    public function destinationDepartment(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'Destination');
    }
}
