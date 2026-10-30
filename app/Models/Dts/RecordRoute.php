<?php

namespace App\Models\Dts;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordRoute extends Model
{
    use HasFactory;

    protected $table = 'dts_record_routes';

    protected $fillable = [
        'tenant_id',
        'record_id',
        'step_number',
        'unit_id',
        'dept_id',
        'office_name',
        'for_action',
        'sla_hours',
        'sla_duration',
        'sla_unit',
        'processing_time',
        'is_mandatory',
        'status',
        'received_at',
        'completed_at',
        'actual_minutes',
        'received_by',
        'completed_by',
        'courier',
        'remarks',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'unit_id' => 'integer',
        'dept_id' => 'integer',
        'sla_hours' => 'integer',
        'sla_duration' => 'float',
        'is_mandatory' => 'boolean',
        'actual_minutes' => 'integer',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(Records::class, 'record_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isInTransit(): bool
    {
        return $this->status === 'in_transit';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDelayed(): bool
    {
        if ($this->status === 'delayed') {
            return true;
        }

        if ($this->isInProgress() && $this->received_at) {
            $allocatedMin = $this->getAllocatedMinutes();
            $elapsedMin = Carbon::parse($this->received_at)->diffInMinutes(now());

            return $elapsedMin > $allocatedMin;
        }

        return false;
    }

    public function getAllocatedMinutes(): int
    {
        if ($this->sla_duration && $this->sla_unit) {
            $unit = strtolower($this->sla_unit);
            if (str_contains($unit, 'day')) {
                return (int) round($this->sla_duration * 8 * 60); // 8-hour workday
            }
            if (str_contains($unit, 'hour')) {
                return (int) round($this->sla_duration * 60);
            }
            if (str_contains($unit, 'min')) {
                return (int) round($this->sla_duration);
            }
        }

        if ($this->sla_hours) {
            return (int) ($this->sla_hours * 60);
        }

        return 24 * 60; // default 24h
    }

    public function getElapsedMinutes(): ?int
    {
        if ($this->actual_minutes) {
            return $this->actual_minutes;
        }

        if ($this->received_at) {
            $end = $this->completed_at ?: now();

            return (int) Carbon::parse($this->received_at)->diffInMinutes($end);
        }

        return null;
    }

    public function getFormattedActualTimeAttribute(): ?string
    {
        $min = $this->getElapsedMinutes();
        if ($min === null) {
            return null;
        }

        if ($min < 60) {
            return "{$min} min".($min !== 1 ? 's' : '');
        }

        $hours = floor($min / 60);
        $remainderMin = $min % 60;

        if ($hours < 24) {
            return $remainderMin > 0 ? "{$hours}h {$remainderMin}m" : "{$hours} hr".($hours !== 1 ? 's' : '');
        }

        $days = round($hours / 8, 1);

        return "{$days} work day".($days != 1 ? 's' : '');
    }

    public function isForAcceptance(): bool
    {
        return $this->status === 'in_transit';
    }

    public function getTimeRemainingTextAttribute(): ?string
    {
        if ($this->isCompleted()) {
            return 'Completed';
        }

        if (! $this->received_at) {
            return $this->isInTransit() ? 'For Acceptance (Pending Receipt)' : 'Scheduled Station';
        }

        $allocated = $this->getAllocatedMinutes();
        $elapsed = $this->getElapsedMinutes() ?: 0;

        if ($elapsed > $allocated) {
            $overdue = $elapsed - $allocated;
            $overdueHrs = round($overdue / 60, 1);

            return "Overdue by {$overdueHrs}h";
        }

        $rem = $allocated - $elapsed;
        $remHrs = round($rem / 60, 1);

        return "{$remHrs}h remaining";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Done',
            'in_progress' => $this->isDelayed() ? 'SLA Breached' : 'Active Custody',
            'in_transit' => 'For Acceptance',
            'delayed' => 'SLA Breached',
            default => 'Pending',
        };
    }
}
