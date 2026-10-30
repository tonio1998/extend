<?php

namespace App\Models;

use App\Models\SaaS\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'designations';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'category',
        'description',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function employeeDesignations(): HasMany
    {
        return $this->hasMany(EmployeeDesignation::class, 'designation_id');
    }

    public function userDesignations(): HasMany
    {
        return $this->employeeDesignations();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    /**
     * Number of active employee holders of this designation
     */
    public function getActiveHoldersCountAttribute(): int
    {
        return EmployeeDesignation::where('designation_id', $this->id)
            ->where('status', 'active')
            ->distinct('employee_id')
            ->count('employee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
