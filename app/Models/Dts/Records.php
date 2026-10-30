<?php

namespace App\Models\Dts;

use App\Models\User;
use App\Services\Dts\RecordWorkflowEngine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Records extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dts_records';

    protected $fillable = [
        'tenant_id',
        'UnitID',
        'DocumentID',
        'QRCODE',
        'ConnectQR',
        'type',
        'doc_type',
        'doc_type_id',
        'Description',
        'Pages',
        'forAction',
        'Remarks',
        'LastAction',
        'Courier',
        'location',
        'Destination',
        'DateTimeReceived',
        'Origin',
        'TransactionType',
        'Priority',
        'TrailStatus',
        'DocStatus',
        'status',
        'archived',
        'locked',
        'created_by',
        'updated_by',
        'Deadline',
        'pdf_path',
        'routing_as',
    ];

    protected $casts = [
        'doc_type_id' => 'integer',
        'archived' => 'integer',
        'locked' => 'integer',
        'DateTimeReceived' => 'datetime',
        'Deadline' => 'datetime',
    ];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(RecordRoute::class, 'record_id')->orderBy('step_number', 'asc');
    }

    public function activeRoute(): ?RecordRoute
    {
        return $this->routes->first(function ($r) {
            return in_array($r->status, ['in_progress', 'in_transit', 'delayed']);
        });
    }

    public function nextRoute(): ?RecordRoute
    {
        return $this->routes->first(function ($r) {
            return $r->status === 'pending';
        });
    }

    public function getRouteProgressAttribute(): array
    {
        $total = $this->routes->count();
        if ($total === 0) {
            return [
                'has_routes' => false,
                'total' => 0,
                'completed' => 0,
                'current_step' => 1,
                'percentage' => 0,
                'percent' => 0,
                'current_office' => null,
                'next_office' => null,
                'is_all_completed' => false,
            ];
        }

        $completed = $this->routes->where('status', 'completed')->count();
        $isDone = ($this->TrailStatus === 'Done');

        if ($isDone || $completed >= $total) {
            return [
                'has_routes' => true,
                'total' => $total,
                'completed' => $total,
                'current_step' => $total,
                'percentage' => 100,
                'percent' => 100,
                'current_office' => null,
                'next_office' => null,
                'is_all_completed' => true,
            ];
        }

        $active = $this->activeRoute();
        $next = $this->nextRoute();

        $currentStepNum = $active ? $active->step_number : ($next ? $next->step_number : ($completed ?: 1));

        $progressScore = $completed + ($active ? 0.5 : 0);
        $percentage = min(99, max(0, (int) round(($progressScore / $total) * 100)));

        if ($active && $percentage < 5) {
            $percentage = (int) round((1 / $total) * 50);
        }

        return [
            'has_routes' => true,
            'total' => $total,
            'completed' => $completed,
            'current_step' => $currentStepNum,
            'percentage' => $percentage,
            'percent' => $percentage,
            'current_office' => $active?->office_name,
            'next_office' => $next?->office_name,
            'is_all_completed' => false,
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TrackLogs::class, 'TransactionID')->orderBy('id', 'asc');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecordFiles::class, 'RecordID')->orderBy('created_at', 'desc');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'UnitID');
    }

    public function destinationUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'Destination');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'UnitID');
    }

    public function destinationDepartment(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'Destination');
    }

    public function getFormattedQrAttribute(): string
    {
        return formatQR12Digits($this->QRCODE ?: $this->id);
    }

    public function getSlaStatusAttribute(): string
    {
        if ($this->TrailStatus === 'Done') {
            return 'Completed';
        }

        if ($this->Deadline) {
            if (now()->greaterThan($this->Deadline)) {
                return 'Overdue';
            }
            if (now()->diffInHours($this->Deadline, false) <= 24) {
                return 'Approaching SLA';
            }
        }

        return 'Within SLA';
    }

    public function getAgingDaysAttribute(): int
    {
        $start = $this->DateTimeReceived ?: $this->created_at;

        return $start ? (int) $start->diffInDays(now()) : 0;
    }

    /**
     * Get procedural steps with station-level turnaround SLA compliance directly from record routes.
     */
    public function getCharterWorkflow(): array
    {
        return app(RecordWorkflowEngine::class)->getRouteWorkflowCompliance($this);
    }

    /**
     * Get non-enforcing intelligent next station recommendation from record routes.
     */
    public function getCharterNextStepPrediction(): ?array
    {
        return app(RecordWorkflowEngine::class)->predictNextStation($this);
    }
}
