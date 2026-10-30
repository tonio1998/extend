<?php

namespace App\Models\SaaS;

use App\Models\DSS\DSSEvaluation;
use App\Models\IPM\ProcessWorkflow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'sys_tenants';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'plan',
        'status',
    ];

    public function institutionSetting(): HasOne
    {
        return $this->hasOne(InstitutionSetting::class, 'tenant_id');
    }

    public function themeSetting(): HasOne
    {
        return $this->hasOne(ThemeSetting::class, 'tenant_id');
    }

    public function campuses(): HasMany
    {
        return $this->hasMany(Campus::class, 'tenant_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'tenant_id');
    }

    public function signatories(): HasMany
    {
        return $this->hasMany(SignatoryMatrix::class, 'tenant_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(ProcessWorkflow::class, 'tenant_id');
    }

    public function dssEvaluations(): HasMany
    {
        return $this->hasMany(DSSEvaluation::class, 'tenant_id');
    }
}
