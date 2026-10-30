<?php

namespace App\Models\Accreditation;

use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\SaaS\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicProgram extends Model
{
    use HasFactory;

    protected $table = 'academic_programs';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'department_id',
        'program_code',
        'program_name',
        'major',
        'degree_level',
        'discipline_cluster',
        'default_instrument_template',
        'standard_years',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'standard_years' => 'integer',
        ];
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
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function accreditationPrograms(): HasMany
    {
        return $this->hasMany(AccreditationProgram::class, 'academic_program_id');
    }

    public function getHashIdAttribute(): string
    {
        return dss_encrypt_id($this->id);
    }

    public function getDisplayNameAttribute(): string
    {
        $name = "[{$this->program_code}] {$this->program_name}";
        if ($this->major) {
            $name .= " — {$this->major}";
        }
        if ($this->relationLoaded('campus') && $this->campus) {
            $name .= " ({$this->campus->campus_name})";
        }

        return $name;
    }
}
