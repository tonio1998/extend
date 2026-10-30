<?php

namespace App\Models;

use App\Models\Dts\Unit;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDesignation extends Model
{
    use HasFactory;

    protected $table = 'employee_designations';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'user_id',
        'campus_id',
        'department_id',
        'designation_id',
        'designation',
        'is_primary',
        'status',
        'effective_date',
        'end_date',
        'remarks',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'department_id');
    }

    public function designationModel(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Alias for department
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'department_id');
    }
}
