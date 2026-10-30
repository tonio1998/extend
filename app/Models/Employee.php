<?php

namespace App\Models;

use App\Models\Dts\Unit;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'tenant_id',
        'employee_no',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'phone_number',
        'gender',
        'campus_id',
        'department_id',
        'designation_id',
        'designation',
        'employment_type',
        'employment_status',
        'date_hired',
    ];

    protected $casts = [
        'date_hired' => 'date',
    ];

    /**
     * Institutional Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Assigned Campus
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * Assigned Office / Department / Unit
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'department_id');
    }

    /**
     * Master Designation
     */
    public function designationModel(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Optional linked system user account (0..1 relationship)
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    /**
     * Multiple designations and offices / units
     */
    public function designations(): HasMany
    {
        return $this->hasMany(EmployeeDesignation::class, 'employee_id');
    }

    /**
     * Alias for designations
     */
    public function employeeDesignations(): HasMany
    {
        return $this->hasMany(EmployeeDesignation::class, 'employee_id');
    }

    /**
     * Primary designation / mother unit
     */
    public function primaryDesignation(): HasOne
    {
        return $this->hasOne(EmployeeDesignation::class, 'employee_id')->where('is_primary', true);
    }

    /**
     * All unique designations string (e.g. "Programmer, Faculty, Extension Head")
     */
    public function getAllDesignationsListAttribute(): array
    {
        $list = $this->designations->pluck('designation')->filter()->unique()->values()->toArray();
        if (empty($list) && $this->designation) {
            $list = [$this->designation];
        }

        return $list;
    }

    /**
     * Compute full name
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if (! empty($this->middle_name)) {
            $name .= ' '.strtoupper(substr($this->middle_name, 0, 1)).'.';
        }
        $name .= ' '.$this->last_name;
        if (! empty($this->suffix)) {
            $name .= ' '.$this->suffix;
        }

        return trim($name);
    }

    /**
     * Check if employee has an active system login account
     */
    public function getHasUserAccountAttribute(): bool
    {
        return $this->user()->exists();
    }

    /**
     * Get the route key for implicit model binding and URL generation.
     */
    public function getRouteKey(): string
    {
        return dss_encrypt_id($this->id);
    }

    /**
     * URL-safe encrypted identifier.
     */
    public function getEncryptedIdAttribute(): string
    {
        return dss_encrypt_id($this->id);
    }
}
