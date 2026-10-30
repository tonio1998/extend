<?php

namespace App\Models\SaaS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionSetting extends Model
{
    use HasFactory;

    protected $table = 'sys_institution_settings';

    protected $fillable = [
        'tenant_id',
        'school_name',
        'school_acronym',
        'school_type',
        'address',
        'contact_email',
        'contact_phone',
        'website_url',
        'current_fy',
        'charter_basis',
        'logo_path',
        'seal_path',
        'watermark_path',
        'logo_variants',
        'vision_statement',
        'mission_statement',
        'document_settings',
    ];

    protected function casts(): array
    {
        return [
            'logo_variants' => 'array',
            'document_settings' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
