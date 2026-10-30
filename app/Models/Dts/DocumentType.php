<?php

namespace App\Models\Dts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_document_types';

    protected $fillable = [
        'tenant_id',
        'code',
        'citizen_charter_code',
        'name',
        'category',
        'standard_sla_days',
        'complexity',
        'client_type',
        'who_may_avail',
        'checklist_requirements',
        'fees',
        'legal_basis',
        'primary_unit_id',
        'default_priority',
        'requires_approval',
        'template_code',
        'description',
        'icon',
        'badge_color',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'standard_sla_days' => 'integer',
        'default_priority' => 'integer',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function primaryUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'primary_unit_id');
    }

    public function citizenCharter(): HasOne
    {
        return $this->hasOne(CitizenCharter::class, 'doc_type_id');
    }

    public function routingTemplate(): HasOne
    {
        return $this->hasOne(RoutingTemplate::class, 'doc_type_id');
    }

    /**
     * Get configured flow steps (either from routing template or citizen charter).
     */
    public function getFlowSteps()
    {
        if ($this->relationLoaded('routingTemplate') && $this->routingTemplate?->steps?->isNotEmpty()) {
            return $this->routingTemplate->steps;
        }

        if ($this->relationLoaded('citizenCharter') && $this->citizenCharter?->steps?->isNotEmpty()) {
            return $this->citizenCharter->steps;
        }

        // Direct query fallback
        $tpl = $this->routingTemplate()->with('steps.unit')->first();
        if ($tpl && $tpl->steps->isNotEmpty()) {
            return $tpl->steps;
        }

        $charter = $this->citizenCharter()->with('steps.unit')->first();

        return $charter ? $charter->steps : collect();
    }

    /**
     * Get checklist requirements as clean array.
     */
    public function getRequirementsArray(): array
    {
        if (empty($this->checklist_requirements)) {
            return [];
        }

        $decoded = json_decode($this->checklist_requirements, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Split line by line if text format
        $lines = preg_split('/\r\n|\r|\n/', $this->checklist_requirements);

        return array_values(array_filter(array_map('trim', $lines)));
    }
}
