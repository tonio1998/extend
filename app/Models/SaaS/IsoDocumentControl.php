<?php

namespace App\Models\SaaS;

use App\Models\Dts\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IsoDocumentControl extends Model
{
    use HasFactory;

    protected $table = 'sys_iso_document_controls';

    protected $fillable = [
        'tenant_id',
        'department_id',
        'document_type',
        'doc_no',
        'rev_no',
        'effective_date',
        'page_no_format',
        'notes',
        'is_active',
    ];

    protected $appends = [
        'office_code',
        'office_name',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'department_id');
    }

    /**
     * Dynamically resolve office_code directly from the units source of truth.
     */
    public function getOfficeCodeAttribute(): ?string
    {
        return $this->department?->unit_code ?: $this->department?->short_name;
    }

    /**
     * Dynamically resolve office_name directly from the units source of truth.
     */
    public function getOfficeNameAttribute(): ?string
    {
        return $this->department?->unit_name ?: 'Institutional Office';
    }

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Dynamic resolver: finds matching ISO Document Control for tenant, office (code/name/id), and document type.
     */
    public static function resolveControl(int $tenantId, string|int|null $office = null, ?string $docType = null, bool $fallbackToFirst = true): ?self
    {
        $query = static::with('department')->where('tenant_id', $tenantId)->where('is_active', true);

        if ($office) {
            $officeQuery = clone $query;
            if (is_numeric($office)) {
                $match = $officeQuery->where('department_id', (int) $office);
            } else {
                $officeStr = (string) $office;
                $match = $officeQuery->whereHas('department', function ($q) use ($officeStr) {
                    $q->where('unit_code', $officeStr)
                        ->orWhere('short_name', $officeStr)
                        ->orWhere('unit_name', 'like', "%{$officeStr}%");
                });
            }

            if ($docType) {
                $exactMatch = (clone $match)->where(function ($q) use ($docType) {
                    $q->where('document_type', $docType)
                        ->orWhere('document_type', 'like', "%{$docType}%");
                })->first();
                if ($exactMatch) {
                    return $exactMatch;
                }
            }

            $firstOffice = $match->first();
            if ($firstOffice) {
                return $firstOffice;
            }

            if (! $fallbackToFirst) {
                return null;
            }
        }

        if ($docType) {
            $docMatch = (clone $query)->where(function ($q) use ($docType) {
                $q->where('document_type', $docType)
                    ->orWhere('document_type', 'like', "%{$docType}%");
            })->first();
            if ($docMatch) {
                return $docMatch;
            }
        }

        return $fallbackToFirst ? $query->first() : null;
    }

    /**
     * Increment the revision number and update effectivity date
     */
    public function incrementRevision(?string $newEffectiveDate = null, ?string $notes = null): self
    {
        $currentRev = is_numeric($this->rev_no) ? (int) $this->rev_no : 0;
        $this->rev_no = (string) ($currentRev + 1);

        if ($newEffectiveDate) {
            $this->effective_date = $newEffectiveDate;
        } else {
            $this->effective_date = now()->format('Y-m-d');
        }

        if ($notes) {
            $this->notes = $notes;
        }

        $this->save();

        return $this;
    }
}
