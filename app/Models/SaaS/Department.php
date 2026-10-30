<?php

namespace App\Models\SaaS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Department extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'dept_id',
        'unit_code',
        'unit_name',
        'short_name',
        'unit_type',
        'head_name',
        'head_title',
        'is_active',
        // Virtual/compatibility fields mapped via mutators
        'dept_code',
        'dept_name',
        'division',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function ($dept) {
            if (DB::getDriverName() === 'sqlite' && Schema::hasTable('sys_departments')) {
                DB::table('sys_departments')->updateOrInsert(
                    ['id' => $dept->id],
                    [
                        'tenant_id' => $dept->tenant_id,
                        'campus_id' => $dept->campus_id,
                        'dept_code' => $dept->unit_code,
                        'dept_name' => $dept->unit_name,
                        'division' => $dept->unit_type,
                        'head_name' => $dept->head_name,
                        'active' => $dept->is_active ?? true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }

    public function getDeptNameAttribute(): string
    {
        return $this->attributes['unit_name'] ?? '';
    }

    public function setDeptNameAttribute($val): void
    {
        $this->attributes['unit_name'] = $val;
    }

    public function getDeptCodeAttribute(): ?string
    {
        return $this->attributes['unit_code'] ?? null;
    }

    public function setDeptCodeAttribute($val): void
    {
        $this->attributes['unit_code'] = $val;
    }

    public function setDivisionAttribute($val): void
    {
        // Ignored for units table compatibility
    }

    public function setStatusAttribute($val): void
    {
        $this->attributes['is_active'] = ($val === 'active' || $val === true || $val === 1);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
}
