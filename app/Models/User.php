<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Accreditation\AccreditationArea;
use App\Models\Accreditation\AccreditationBenchmark;
use App\Models\Dts\Unit;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'employee_id',
        'campus_id',
        'dept_id',
        'name',
        'email',
        'password',
        'role',
        'designation',
        'phone_number',
        'avatar_url',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->role === 'SuperAdmin' && ! $user->isDesignatedSuperAdmin()) {
                $user->role = 'Admin';
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'dept_id');
    }

    public function dtsUnit(): ?Unit
    {
        return Unit::where('tenant_id', $this->tenant_id)
            ->where(function ($q) {
                if ($this->dept_id) {
                    $q->where('id', $this->dept_id)
                        ->orWhere('dept_id', $this->dept_id);
                }
            })
            ->first() ?: Unit::where('tenant_id', $this->tenant_id)->first();
    }

    public function assignedAreas(): BelongsToMany
    {
        return $this->belongsToMany(AccreditationArea::class, 'accreditation_area_users', 'user_id', 'area_id')
            ->withPivot('role_in_team')
            ->withTimestamps();
    }

    public function assignedBenchmarks(): BelongsToMany
    {
        return $this->belongsToMany(AccreditationBenchmark::class, 'accreditation_benchmark_users', 'user_id', 'benchmark_id')
            ->withPivot('role_in_team')
            ->withTimestamps();
    }

    /**
     * Multiple institutional designations and units / offices
     */
    public function employeeDesignations(): HasMany
    {
        return $this->hasMany(EmployeeDesignation::class, 'user_id');
    }

    /**
     * Backward-compatible alias
     */
    public function userDesignations(): HasMany
    {
        return $this->employeeDesignations();
    }

    /**
     * Primary institutional designation
     */
    public function primaryDesignation(): HasOne
    {
        return $this->hasOne(EmployeeDesignation::class, 'user_id')->where('is_primary', true);
    }

    /**
     * Check if this user's email matches the configured SuperAdmin email (apiloton1@snsu.edu.ph)
     */
    public function isDesignatedSuperAdmin(): bool
    {
        $superAdminEmail = strtolower(trim(config('dss.superadmin_email', 'apiloton1@snsu.edu.ph')));
        $userEmail = strtolower(trim($this->email ?? ''));

        return $userEmail !== '' && $userEmail === $superAdminEmail;
    }

    /**
     * Check if user is Platform SuperAdmin (Solely apiloton1@snsu.edu.ph)
     */
    public function isSuperAdmin(): bool
    {
        if ($this->isDesignatedSuperAdmin()) {
            if ($this->role !== 'SuperAdmin' || ! $this->hasRole('SuperAdmin')) {
                $this->ensureSuperAdminState();
            }

            return true;
        }

        return false;
    }

    /**
     * Automatically ensure and assign Spatie SuperAdmin role & permissions for designated user
     */
    public function ensureSuperAdminState(): bool
    {
        if (! $this->isDesignatedSuperAdmin()) {
            return false;
        }

        // 1. Update database role column if needed
        if ($this->role !== 'SuperAdmin') {
            $this->update(['role' => 'SuperAdmin']);
        }

        try {
            // 2. Ensure global Spatie SuperAdmin role exists with all permissions
            $superRole = Role::firstOrCreate([
                'name' => 'SuperAdmin',
                'guard_name' => 'web',
            ]);

            $allPermissions = Permission::all();
            if ($allPermissions->isNotEmpty()) {
                $superRole->syncPermissions($allPermissions);
            }

            // Explicitly assign global Spatie Role to user
            if (! $this->hasRole('SuperAdmin')) {
                $this->assignRole($superRole);
            }

            // Clear Spatie permission cache
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
            Log::warning('Failed to auto-assign Spatie SuperAdmin role: '.$e->getMessage());
        }

        return true;
    }

    public function isImpersonatingTenant(): bool
    {
        return false;
    }

    public function isTenantAdmin(): bool
    {
        return $this->isSuperAdmin()
            || $this->role === 'Admin'
            || $this->hasRole(['SuperAdmin', 'Admin'])
            || in_array(strtolower($this->role ?? ''), ['admin', 'superadmin', 'administrator']);
    }

    public function canTransactTenant(): bool
    {
        return true;
    }

    public function isExtensionDirector(): bool
    {
        return $this->isSuperAdmin()
            || $this->role === 'Director'
            || $this->hasRole('Director')
            || in_array(strtolower($this->role ?? ''), ['director', 'president'])
            || in_array(strtolower($this->employee?->designation ?? ''), ['extension director', 'director for extension', 'director, extension services']);
    }

    public function isDean(): bool
    {
        return $this->role === 'Dean'
            || $this->hasRole(['Dean', 'UnitHead'])
            || in_array(strtolower($this->role ?? ''), ['dean', 'unithead'])
            || str_contains(strtolower($this->employee?->designation ?? ''), 'dean');
    }

    public function isCollegeHead(): bool
    {
        return $this->hasRole(['College Extension Head', 'Extension Coordinator'])
            || in_array($this->role, ['College Extension Head', 'Extension Coordinator', 'Coordinator'])
            || str_contains(strtolower($this->role ?? ''), 'extension head')
            || str_contains(strtolower($this->role ?? ''), 'coordinator')
            || str_contains(strtolower($this->employee?->designation ?? ''), 'extension head')
            || str_contains(strtolower($this->employee?->designation ?? ''), 'coordinator');
    }

    public function isExtensionOfficeStaff(): bool
    {
        return $this->isSuperAdmin()
            || $this->role === 'Admin'
            || $this->hasRole([
                'Admin',
                'Extension Staff',
                'Extension Tracking Officer',
                'Extension Budget Officer',
                'Extension M&E Officer',
                'Extension Document Custodian',
            ])
            || in_array(strtolower($this->role ?? ''), [
                'admin',
                'superadmin',
                'staff',
                'extension staff',
                'extension tracking officer',
                'extension budget officer',
                'extension m&e officer',
                'extension document custodian',
            ]);
    }

    public function isEvaluator(): bool
    {
        return $this->hasRole('Evaluator')
            || strtolower($this->role ?? '') === 'evaluator';
    }

    public function isAuditor(): bool
    {
        return $this->hasRole('Auditor')
            || strtolower($this->role ?? '') === 'auditor';
    }

    public function isProjectLeader(): bool
    {
        return ! $this->isExtensionDirector()
            && ! $this->isExtensionOfficeStaff()
            && ! $this->isDean()
            && ! $this->isCollegeHead();
    }

    public function getEffectiveDashboardRole(): string
    {
        if ($this->isExtensionDirector()) {
            return 'director';
        }
        if ($this->isDean()) {
            return 'dean';
        }
        if ($this->isCollegeHead()) {
            return 'college_head';
        }
        if ($this->isExtensionOfficeStaff()) {
            return 'extension_office';
        }

        return 'project_leader';
    }
}
