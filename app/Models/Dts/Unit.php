<?php

namespace App\Models\Dts;

use App\Models\Employee;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'dept_id',
        'unit_code',
        'unit_name',
        'short_name',
        'unit_type',
        'building',
        'room_number',
        'location_description',
        'latitude',
        'longitude',
        'head_employee_id',
        'head_name',
        'head_title',
        'extension_head_employee_id',
        'extension_head_name',
        'extension_head_title',
        'email',
        'contact_number',
        'is_receiving_station',
        'is_originating_station',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_receiving_station' => 'boolean',
        'is_originating_station' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }

    public function extensionHeadEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'extension_head_employee_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function getDeptNameAttribute(): string
    {
        return $this->unit_name;
    }

    public function getDeptCodeAttribute(): ?string
    {
        return $this->unit_code;
    }

    public function getNameAttribute(): string
    {
        return $this->unit_name;
    }

    public function getDepartmentNameAttribute(): string
    {
        return $this->unit_name;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dept_id');
    }

    public function parentUnit(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dept_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Records::class, 'UnitID');
    }

    public function extensionProposals(): HasMany
    {
        return $this->hasMany(ExtensionProposal::class, 'unit_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeReceivingStations($query)
    {
        return $query->where('is_receiving_station', true);
    }
}
