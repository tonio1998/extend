<?php

namespace App\Services\Dts;

use App\Models\Dts\DocumentType;
use App\Models\Dts\RecordRoute;
use App\Models\Dts\Records;
use App\Models\Dts\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RecordWorkflowEngine
{
    /**
     * Instantiate and duplicate configured DocumentType routing steps into the record's dedicated routing table.
     */
    public function instantiateRoutesForRecord(Records $record, ?DocumentType $docType = null): Collection
    {
        // If routes are already instantiated for this record, return them
        $existing = RecordRoute::where('record_id', $record->id)->orderBy('step_number')->get();
        if ($existing->isNotEmpty()) {
            return $existing;
        }

        // Resolve DocumentType if not passed
        if (! $docType) {
            if ($record->doc_type_id) {
                $docType = DocumentType::with(['routingTemplate.steps.unit', 'citizenCharter.steps.unit'])->find($record->doc_type_id);
            }
            if (! $docType && $record->doc_type) {
                $docType = DocumentType::where('tenant_id', $record->tenant_id)
                    ->where(function ($q) use ($record) {
                        $q->where('name', $record->doc_type)->orWhere('code', $record->doc_type);
                    })
                    ->with(['routingTemplate.steps.unit', 'citizenCharter.steps.unit'])
                    ->first();
            }
        }

        $sourceSteps = collect();

        // 1. Prioritize RoutingTemplate configured for this DocumentType
        if ($docType && $docType->routingTemplate && $docType->routingTemplate->steps->isNotEmpty()) {
            $sourceSteps = $docType->routingTemplate->steps->sortBy('step_number')->values()->map(function ($s) {
                return [
                    'step_number' => $s->step_number,
                    'unit_id' => $s->unit_id,
                    'office_name' => $s->unit?->unit_name ?: ($s->unit_name ?: 'Designated Station'),
                    'for_action' => $s->for_action ?: 'For processing & administrative compliance',
                    'sla_hours' => $s->sla_hours ?: 24,
                    'sla_duration' => $s->sla_duration ?: 24,
                    'sla_unit' => $s->sla_unit ?: 'hours',
                    'processing_time' => $s->processing_time ?: ($s->sla_duration ? $s->sla_duration.' '.$s->sla_unit : ($s->sla_hours ? $s->sla_hours.' Hours' : '1 Day')),
                    'is_mandatory' => $s->is_mandatory ?? true,
                ];
            });
        }

        // 2. Fallback to single hop if Destination is set on the record
        if ($sourceSteps->isEmpty() && ! empty($record->Destination)) {
            $destUnit = Unit::find($record->Destination);
            $sourceSteps = collect([[
                'step_number' => 1,
                'unit_id' => $destUnit?->id ?? $record->Destination,
                'dept_id' => $destUnit?->id ?? $record->Destination,
                'office_name' => $destUnit?->unit_name ?: 'Destination Office',
                'for_action' => $record->forAction ?: 'For Appropriate Action & Compliance',
                'sla_hours' => 24,
                'sla_duration' => 24,
                'sla_unit' => 'hours',
                'processing_time' => '1 Working Day',
                'is_mandatory' => true,
            ]]);
        }

        $originUnit = Unit::find($record->UnitID) ?: ($record->creator?->dtsUnit() ?? null);
        $originName = $originUnit?->unit_name ?: ($record->Origin ?: 'Originating Office');

        // By default, Step 1 is the creator / originating station
        $firstSource = $sourceSteps->first();
        $startsFromOrigin = false;
        if ($firstSource) {
            if (! empty($firstSource['unit_id']) && $originUnit && $firstSource['unit_id'] == $originUnit->id) {
                $startsFromOrigin = true;
            } elseif (! empty($firstSource['office_name']) && stripos($firstSource['office_name'], $originName) !== false) {
                $startsFromOrigin = true;
            }
        }

        if (! $startsFromOrigin) {
            $originStep = [
                'unit_id' => $originUnit?->id,
                'office_name' => $originName,
                'for_action' => 'Document Registration & Originating Endorsement',
                'sla_hours' => 2,
                'sla_duration' => 2,
                'sla_unit' => 'hours',
                'processing_time' => 'Origin Preparation',
                'is_mandatory' => true,
                'is_origin' => true,
            ];
            $sourceSteps->prepend($originStep);
        }

        $created = collect();
        $isRecordInTransit = ($record->TrailStatus === 'In-Transit');

        foreach ($sourceSteps->values() as $idx => $s) {
            $stepNumber = $idx + 1; // Always sequential: 1, 2, 3...
            $unitId = $s['unit_id'] ?? null;
            $unit = $unitId ? Unit::find($unitId) : null;
            if (! $unit && ! empty($s['office_name'])) {
                $unit = Unit::where('unit_name', 'LIKE', "%{$s['office_name']}%")
                    ->orWhere('short_name', 'LIKE', "%{$s['office_name']}%")
                    ->first();
                if ($unit) {
                    $unitId = $unit->id;
                }
            }

            $deptId = $unitId ?? ($unit?->id ?? ($s['dept_id'] ?? null));
            $officeName = $unit?->unit_name ?: ($s['office_name'] ?? 'Designated Unit');

            // Determine initial status for step:
            $status = 'pending';
            $receivedAt = null;
            $completedAt = null;
            $completedBy = null;
            $actualMinutes = null;

            if ($idx === 0) {
                // Step 1: Originating Office / Creator
                if ($isRecordInTransit) {
                    // Document immediately dispatched out upon creation
                    $status = 'completed';
                    $receivedAt = $record->created_at ?: now();
                    $completedAt = $record->created_at ?: now();
                    $completedBy = $record->created_by;
                    $actualMinutes = 1;
                } else {
                    // Document kept at origin: active custody at creator's station
                    $status = 'in_progress';
                    $receivedAt = $record->DateTimeReceived ?: ($record->created_at ?: now());
                }
            } elseif ($idx === 1 && $isRecordInTransit) {
                // Step 2: First destination office is en route for acceptance
                $status = 'in_transit';
            }

            $route = RecordRoute::create([
                'tenant_id' => $record->tenant_id ?: 1,
                'record_id' => $record->id,
                'step_number' => $stepNumber,
                'unit_id' => $unitId,
                'dept_id' => $deptId,
                'office_name' => $officeName,
                'for_action' => $s['for_action'] ?? $record->forAction,
                'sla_hours' => $s['sla_hours'] ?? 24,
                'sla_duration' => $s['sla_duration'] ?? 24,
                'sla_unit' => $s['sla_unit'] ?? 'hours',
                'processing_time' => $s['processing_time'] ?? '1 Working Day',
                'is_mandatory' => $s['is_mandatory'] ?? true,
                'status' => $status,
                'received_at' => $receivedAt,
                'completed_at' => $completedAt,
                'completed_by' => $completedBy,
                'actual_minutes' => $actualMinutes,
                'received_by' => $status === 'in_progress' ? $record->created_by : null,
            ]);

            $created->push($route);
        }

        return $created;
    }

    /**
     * Sanitize and self-heal instantiated route states for a record.
     * Guarantees that at most one station has active custody, prior stations are completed,
     * and in-transit destinations show 'in_transit' (For Acceptance).
     */
    public function sanitizeRouteStates(Records $record): void
    {
        $routes = $record->routes()->orderBy('step_number')->orderBy('id')->get();
        if ($routes->isEmpty()) {
            return;
        }

        // 1. Guarantee strict sequential numbering: 1, 2, 3, ... N
        foreach ($routes as $idx => $r) {
            $expected = $idx + 1;
            if ($r->step_number !== $expected) {
                $r->update(['step_number' => $expected]);
                $r->step_number = $expected;
            }
        }

        // 3. Terminal / Archived records: all steps must be completed
        if ($record->TrailStatus === 'Done') {
            foreach ($routes as $r) {
                if ($r->status !== 'completed') {
                    $r->update([
                        'status' => 'completed',
                        'completed_at' => $r->completed_at ?: ($record->updated_at ?: now()),
                        'actual_minutes' => $r->actual_minutes ?: max(1, (int) Carbon::parse($r->received_at ?: $record->created_at)->diffInMinutes($r->completed_at ?: now())),
                    ]);
                }
            }

            return;
        }

        // 4. Freshly created records at origin (TrailStatus is 'Start' or 'Pending')
        if (in_array($record->TrailStatus, ['Start', 'Pending'])) {
            $hasRouteMovements = $record->logs()->whereIn('TrailStatus', ['Received', 'In-Transit'])->exists();
            if (! $hasRouteMovements) {
                // Step 1 (Origin) has active custody at origin
                $first = $routes->first();
                if ($first && $first->status !== 'in_progress') {
                    $first->update([
                        'status' => 'in_progress',
                        'received_at' => $record->DateTimeReceived ?: ($record->created_at ?: now()),
                        'received_by' => $record->created_by,
                    ]);
                }
                // All subsequent steps are pending
                foreach ($routes->slice(1) as $future) {
                    if ($future->status !== 'pending') {
                        $future->update([
                            'status' => 'pending',
                            'received_at' => null,
                            'received_by' => null,
                            'completed_at' => null,
                            'completed_by' => null,
                        ]);
                    }
                }

                return;
            }
        }

        // 5. Document is In-Transit:
        if ($record->TrailStatus === 'In-Transit') {
            // Find highest step reached or completed so far
            $lastCompleted = $routes->where('status', 'completed')->last();
            $lastStepNum = $lastCompleted ? $lastCompleted->step_number : 0;

            // In a strictly sequential routing pipeline, only the IMMEDIATE NEXT step directly following
            // the last completed step can claim the in-transit transmittal.
            // A later step (e.g. Step 4 or last step) must NOT claim it if prior steps are not completed!
            $immediateNextStep = $routes->first(fn ($r) => $r->step_number > $lastStepNum);

            $destStep = null;
            if ($record->Destination && $immediateNextStep) {
                if ($immediateNextStep->unit_id == $record->Destination || $immediateNextStep->dept_id == $record->Destination) {
                    $destStep = $immediateNextStep;
                }
            } elseif (! $record->Destination) {
                $destStep = $routes->firstWhere('status', 'in_transit');
                if ($destStep && $destStep->step_number != ($lastStepNum + 1)) {
                    $destStep = null;
                }
            }

            if ($destStep) {
                // Predefined immediate next route step reached: mark as in_transit (For Acceptance)
                if ($destStep->status !== 'in_transit') {
                    $destStep->update([
                        'status' => 'in_transit',
                        'received_at' => null,
                        'received_by' => null,
                    ]);
                }

                // All future steps remain pending
                foreach ($routes->where('step_number', '>', $destStep->step_number) as $future) {
                    if ($future->status !== 'pending') {
                        $future->update([
                            'status' => 'pending',
                            'received_at' => null,
                            'completed_at' => null,
                        ]);
                    }
                }
            } else {
                // Destination is OFF-ROUTE or jumped ahead while previous steps are still pending.
                // Do NOT mark any future route step as in_transit or completed! All pending steps remain pending.
                $futureSteps = $routes->where('step_number', '>', $lastStepNum);
                foreach ($futureSteps as $future) {
                    if ($future->status !== 'pending') {
                        $future->update([
                            'status' => 'pending',
                            'received_at' => null,
                            'completed_at' => null,
                        ]);
                    }
                }
            }

            return;
        }

        // 6. Document is in custody (Received, Return, or default):
        $lastCompleted = $routes->where('status', 'completed')->last();
        $lastStepNum = $lastCompleted ? $lastCompleted->step_number : 0;
        $immediateNextStep = $routes->first(fn ($r) => $r->step_number > $lastStepNum);

        $matchingStep = null;
        $inTransitStep = $routes->firstWhere('status', 'in_transit');
        if ($inTransitStep && (! $record->UnitID || $inTransitStep->unit_id == $record->UnitID || $inTransitStep->dept_id == $record->UnitID)) {
            $matchingStep = $inTransitStep;
        } elseif ($immediateNextStep && $record->UnitID && ($immediateNextStep->unit_id == $record->UnitID || $immediateNextStep->dept_id == $record->UnitID)) {
            $matchingStep = $immediateNextStep;
        }

        if ($matchingStep) {
            if ($matchingStep->status !== 'in_progress') {
                $matchingStep->update([
                    'status' => 'in_progress',
                    'received_at' => $matchingStep->received_at ?: now(),
                ]);
            }

            // All future steps pending
            foreach ($routes->where('step_number', '>', $matchingStep->step_number) as $future) {
                if ($future->status !== 'pending') {
                    $future->update([
                        'status' => 'pending',
                        'received_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }
        } else {
            // Custody is with an OFF-ROUTE station or non-sequential station while prior steps are still pending.
            // Do NOT force any future route step to become in_progress! All pending steps remain pending.
            $futureSteps = $routes->where('step_number', '>', $lastStepNum);
            foreach ($futureSteps as $future) {
                if ($future->status !== 'pending') {
                    $future->update([
                        'status' => 'pending',
                        'received_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }
        }
    }

    /**
     * Record Incoming receipt at a station and update the matching instantiated route step.
     */
    public function recordIncomingReceipt(Records $record, ?int $stationId = null, ?User $user = null, ?string $remarks = null): ?RecordRoute
    {
        $routes = $record->routes()->orderBy('step_number')->get();
        if ($routes->isEmpty()) {
            return null;
        }

        $lastCompleted = $routes->where('status', 'completed')->last();
        $lastStepNum = $lastCompleted ? $lastCompleted->step_number : 0;
        $immediateNextStep = $routes->first(fn ($r) => $r->step_number > $lastStepNum);

        // 1. First priority: The step currently in 'in_transit' (it was dispatched specifically to this step)
        $targetStep = $routes->firstWhere('status', 'in_transit');
        if ($targetStep && $stationId && $targetStep->unit_id != $stationId && $targetStep->dept_id != $stationId) {
            $targetStep = null;
        }

        // 2. Second priority: If no step was in transit, only the IMMEDIATE NEXT pending step can claim custody
        if (! $targetStep && $stationId && $immediateNextStep) {
            if ($immediateNextStep->unit_id == $stationId || $immediateNextStep->dept_id == $stationId) {
                $targetStep = $immediateNextStep;
            }
        }

        if ($targetStep) {
            $targetStep->update([
                'status' => 'in_progress',
                'received_at' => now(),
                'received_by' => $user?->id,
                'completed_at' => null,
                'completed_by' => null,
                'actual_minutes' => null,
                'remarks' => $remarks ?: $targetStep->remarks,
            ]);

            // Guarantee all future steps are pending
            foreach ($routes->where('step_number', '>', $targetStep->step_number) as $future) {
                if ($future->status !== 'pending') {
                    $future->update([
                        'status' => 'pending',
                        'received_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }

            $record->update([
                'UnitID' => $targetStep->unit_id ?: $record->UnitID,
                'location' => $targetStep->office_name,
            ]);

            return $targetStep;
        }

        // Off-route receipt (e.g. side-transmittal or non-sequential station):
        if ($stationId) {
            $custodyUnit = Unit::find($stationId);
            $record->update([
                'UnitID' => $stationId,
                'location' => $custodyUnit?->unit_name ?: $record->location,
            ]);
        }

        return null;
    }

    /**
     * Record Outgoing / Dispatched / Forward movement and complete current step while advancing next step.
     */
    public function recordDispatch(
        Records $record,
        ?int $fromStationId,
        ?int $toStationId,
        string $action,
        ?User $user = null,
        ?string $courier = null,
        ?string $remarks = null,
        ?Carbon $incomingTime = null
    ): array {
        $routes = $record->routes()->orderBy('step_number')->get();
        if ($routes->isEmpty()) {
            return ['completed_step' => null, 'next_step' => null];
        }

        // 1. Identify the current active step being dispatched
        $currentStep = $routes->first(fn ($r) => in_array($r->status, ['in_progress', 'delayed']));
        if (! $currentStep && $fromStationId) {
            $lastCompleted = $routes->where('status', 'completed')->last();
            $lastStepNum = $lastCompleted ? $lastCompleted->step_number : 0;
            $immediateNext = $routes->first(fn ($r) => $r->step_number > $lastStepNum);
            if ($immediateNext && ($immediateNext->unit_id == $fromStationId || $immediateNext->dept_id == $fromStationId)) {
                $currentStep = $immediateNext;
            }
        }
        if (! $currentStep) {
            $currentStep = $routes->where('status', 'completed')->last() ?: $routes->first();
        }

        if ($currentStep) {
            if (! $currentStep->received_at) {
                $currentStep->received_at = $incomingTime ?: now()->subMinute();
                $currentStep->received_by = $user?->id;
            }
            $receivedTime = $currentStep->received_at;
            $actualMinutes = max(1, (int) Carbon::parse($receivedTime)->diffInMinutes(now()));

            $currentStep->update([
                'status' => 'completed',
                'received_at' => $receivedTime,
                'received_by' => $currentStep->received_by ?: $user?->id,
                'completed_at' => now(),
                'completed_by' => $user?->id,
                'actual_minutes' => $actualMinutes,
                'courier' => $courier,
                'remarks' => $remarks ?: $currentStep->remarks,
            ]);
        }

        // 2. Identify the next step strictly matching the chosen destination
        // Only the IMMEDIATE NEXT step directly following $currentStep can claim the transmittal.
        // A later step (e.g. Step 4 or last step) must NOT claim it if prior steps are still pending!
        $immediateNextStep = $currentStep
            ? $routes->where('step_number', '>', $currentStep->step_number)->first()
            : $routes->where('status', 'pending')->first();

        $nextStep = null;
        if ($toStationId) {
            if ($immediateNextStep && ($immediateNextStep->unit_id == $toStationId || $immediateNextStep->dept_id == $toStationId)) {
                $nextStep = $immediateNextStep;
            }
        } else {
            // Only if NO destination was selected by user, auto-select immediate next sequential route step
            $nextStep = $immediateNextStep;
        }

        if ($nextStep) {
            $nextStep->update([
                'status' => 'in_transit',
                'for_action' => $action ?: $nextStep->for_action,
                'courier' => $courier,
                'received_at' => null,
                'received_by' => null,
            ]);

            if ($nextStep->unit_id && ! $toStationId) {
                $record->update([
                    'Destination' => $nextStep->unit_id,
                    'location' => 'In-Transit to '.$nextStep->office_name,
                ]);
            }

            // Ensure future steps after nextStep remain pending
            foreach ($routes->where('step_number', '>', $nextStep->step_number) as $future) {
                if ($future->status !== 'pending') {
                    $future->update([
                        'status' => 'pending',
                        'received_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }
        } else {
            // Destination is OFF-ROUTE or jumped ahead while intermediate steps are pending.
            // Do NOT mark any future route step as in_transit. All pending steps remain pending until routed properly!
            $futureSteps = $currentStep
                ? $routes->where('step_number', '>', $currentStep->step_number)
                : $routes->where('status', 'pending');

            foreach ($futureSteps as $future) {
                if ($future->status !== 'pending') {
                    $future->update([
                        'status' => 'pending',
                        'received_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }
        }

        return [
            'completed_step' => $currentStep,
            'next_step' => $nextStep,
        ];
    }

    /**
     * Record Return movement back to a prior station or origin.
     */
    public function recordReturnMovement(
        Records $record,
        ?int $fromStationId,
        ?int $toStationId,
        string $action,
        ?User $user = null,
        ?string $remarks = null
    ): ?RecordRoute {
        $routes = $record->routes()->orderBy('step_number')->get();
        if ($routes->isEmpty()) {
            return null;
        }

        $currentStep = $routes->first(fn ($r) => in_array($r->status, ['in_progress', 'delayed', 'in_transit']))
            ?: ($fromStationId ? $routes->firstWhere('unit_id', $fromStationId) : $routes->last());

        // Target return step should be prior to currentStep
        $targetReturnStep = null;
        if ($toStationId && $currentStep) {
            $targetReturnStep = $routes->where('step_number', '<', $currentStep->step_number)
                ->first(fn ($r) => $r->unit_id == $toStationId || $r->dept_id == $toStationId);
        }

        if (! $targetReturnStep && $currentStep) {
            $targetReturnStep = $routes->where('step_number', '<', $currentStep->step_number)->last();
        }

        if (! $targetReturnStep) {
            $targetReturnStep = $routes->first();
        }

        if ($targetReturnStep) {
            $targetReturnStep->update([
                'status' => 'in_progress',
                'remarks' => $remarks ?: "Returned: {$action}",
            ]);

            // All steps after the return step are reset to pending
            foreach ($routes->where('step_number', '>', $targetReturnStep->step_number) as $future) {
                $future->update([
                    'status' => 'pending',
                    'received_at' => null,
                    'received_by' => null,
                    'completed_at' => null,
                    'completed_by' => null,
                ]);
            }

            $record->update([
                'UnitID' => $targetReturnStep->unit_id ?: $record->UnitID,
                'location' => $targetReturnStep->office_name,
            ]);

            return $targetReturnStep;
        }

        return null;
    }

    /**
     * Mark all active/remaining route steps as completed upon terminal archiving.
     */
    public function recordTerminalCompletion(Records $record, ?User $user = null): void
    {
        $routes = $record->routes()->orderBy('step_number')->get();
        foreach ($routes as $r) {
            if ($r->status !== 'completed') {
                $receivedTime = $r->received_at ?: now();
                $actualMinutes = $r->received_at ? max(1, (int) Carbon::parse($receivedTime)->diffInMinutes(now())) : 1;

                $r->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'completed_by' => $user?->id,
                    'actual_minutes' => $actualMinutes,
                ]);
            }
        }
    }

    /**
     * Compute full workflow compliance metrics directly from the record's instantiated routes.
     * Replaces CitizenCharterWorkflowService with document route instances.
     */
    public function getRouteWorkflowCompliance(Records $record): array
    {
        $routes = $record->routes()->with(['receiver', 'completer', 'unit'])->orderBy('step_number')->get();
        if ($routes->isEmpty()) {
            return [
                'has_charter' => false,
                'has_routes' => false,
                'steps' => [],
                'total_allocated_time_formatted' => 'N/A',
                'total_actual_time_formatted' => '0 mins',
                'total_allocated_minutes' => 0,
                'total_actual_minutes' => 0,
                'any_delayed' => false,
                'all_completed' => false,
                'overall_compliance' => 'No Routes Configured',
            ];
        }

        $totalAllocatedMinutes = $routes->sum(fn ($r) => $r->getAllocatedMinutes());
        $totalActualMinutes = $routes->sum(fn ($r) => $r->getElapsedMinutes() ?: 0);
        $anyDelayed = $routes->contains(fn ($r) => $r->isDelayed());
        $allCompleted = $routes->every(fn ($r) => $r->isCompleted());

        $processedSteps = [];
        foreach ($routes as $r) {
            $processedSteps[] = [
                'step_number' => $r->step_number,
                'office_name' => $r->office_name,
                'unit_id' => $r->unit_id,
                'dept_id' => $r->unit_id,
                'person_responsible' => $r->receiver?->name ?: ($r->completer?->name ?: 'Designated Action Officer'),
                'client_step' => 'Submit document for '.($r->for_action ?: 'processing'),
                'agency_action' => $r->for_action ?: 'Process document',
                'allocated_time' => $r->processing_time ?: ($r->sla_hours ? $r->sla_hours.' Hours' : '1 Day'),
                'allocated_minutes' => $r->getAllocatedMinutes(),
                'actual_time_formatted' => $r->formatted_actual_time,
                'actual_minutes' => $r->getElapsedMinutes(),
                'status' => $r->isDelayed() ? 'delayed' : $r->status,
                'time_remaining_text' => $r->time_remaining_text,
                'timestamp' => $r->received_at?->format('M d, Y h:i A') ?: ($r->completed_at?->format('M d, Y h:i A') ?: ''),
            ];
        }

        $allocatedHours = round($totalAllocatedMinutes / 60, 1);
        $formattedAllocated = ($allocatedHours >= 24)
            ? round($allocatedHours / 8, 1).' Work Days ('.$allocatedHours.' hrs)'
            : ($allocatedHours > 0 ? $allocatedHours.' hrs' : 'Standard SLA');

        $actualHours = round($totalActualMinutes / 60, 1);
        $formattedActual = ($totalActualMinutes < 60)
            ? $totalActualMinutes.' mins'
            : $actualHours.' hrs';

        return [
            'has_charter' => true,
            'has_routes' => true,
            'steps' => $processedSteps,
            'total_allocated_time_formatted' => $formattedAllocated,
            'total_actual_time_formatted' => $formattedActual,
            'total_allocated_minutes' => $totalAllocatedMinutes,
            'total_actual_minutes' => $totalActualMinutes,
            'any_delayed' => $anyDelayed,
            'all_completed' => $allCompleted,
            'overall_compliance' => $anyDelayed ? 'Delayed' : ($allCompleted ? 'Complied' : 'Within Target'),
        ];
    }

    /**
     * Predict the intelligent next station directly from the record's instantiated routing steps.
     * When reaching the last step, terminal action is automatically recommended instead of outgoing.
     */
    public function predictNextStation(Records $record): ?array
    {
        $routes = $record->routes()->orderBy('step_number')->get();
        if ($routes->isEmpty()) {
            return null;
        }

        $totalSteps = $routes->count();

        // 1. If record is In-Transit, the receiving station MUST log Incoming receipt first!
        if ($record->TrailStatus === 'In-Transit') {
            $inTransitStep = $routes->firstWhere('status', 'in_transit');
            if (! $inTransitStep && $record->Destination) {
                $lastCompleted = $routes->where('status', 'completed')->last();
                $lastStepNum = $lastCompleted ? $lastCompleted->step_number : 0;
                $immediateNext = $routes->first(fn ($r) => $r->step_number > $lastStepNum);
                if ($immediateNext && ($immediateNext->unit_id == $record->Destination || $immediateNext->dept_id == $record->Destination)) {
                    $inTransitStep = $immediateNext;
                }
            }

            if ($inTransitStep) {
                $nextStationAfterThis = $routes->first(fn ($r) => $r->step_number > $inTransitStep->step_number);

                return [
                    'is_incoming_action' => true,
                    'is_terminal' => false,
                    'action_type' => 'Incoming',
                    'step_number' => $inTransitStep->step_number,
                    'total_steps' => $totalSteps,
                    'office_name' => $inTransitStep->office_name,
                    'unit_id' => $inTransitStep->unit_id,
                    'suggested_dest_id' => $nextStationAfterThis?->unit_id,
                    'suggested_dest_name' => $nextStationAfterThis?->office_name,
                    'suggested_action' => 'Document Received and Custody Acknowledged',
                    'agency_action' => $inTransitStep->for_action ?: 'Acknowledge Incoming Custody',
                    'allocated_time' => $inTransitStep->processing_time ?: ($inTransitStep->sla_hours ? $inTransitStep->sla_hours.'h' : '1 Day'),
                    'recommendation_title' => 'Incoming Receipt Required',
                    'recommendation_reason' => "Document is In-Transit to {$inTransitStep->office_name} (Step {$inTransitStep->step_number} of {$totalSteps}). The office must acknowledge incoming custody.",
                ];
            }

            // Document is In-Transit to an off-route or non-sequential station:
            $destUnit = $record->Destination ? Unit::find($record->Destination) : null;
            $destName = $destUnit?->unit_name ?: ($record->destinationUnit?->unit_name ?: 'Destination Station');
            $nextPendingStep = $routes->firstWhere('status', 'pending');

            return [
                'is_incoming_action' => true,
                'is_terminal' => false,
                'action_type' => 'Incoming',
                'step_number' => null,
                'total_steps' => $totalSteps,
                'office_name' => $destName,
                'unit_id' => $destUnit?->id,
                'suggested_dest_id' => $nextPendingStep?->unit_id,
                'suggested_dest_name' => $nextPendingStep?->office_name,
                'suggested_action' => 'Document Received and Custody Acknowledged',
                'agency_action' => 'Acknowledge Incoming Custody',
                'allocated_time' => 'Off-Route',
                'recommendation_title' => 'Incoming Receipt Required (Off-Route Transmittal)',
                'recommendation_reason' => "Document is In-Transit to [{$destName}]. This station must acknowledge receipt before forwarding back to the roadmap.",
            ];
        }

        // 2. If record is Done, no further hops needed
        if ($record->TrailStatus === 'Done') {
            return null;
        }

        $activeStep = $routes->first(fn ($r) => in_array($r->status, ['in_progress', 'delayed']));
        $lastRoute = $routes->last();

        // 3. CASE: Document has reached or is at the LAST STEP:
        // Wag na automatic na outgoing! Suggest Terminal completion (Done).
        // Must ONLY trigger if all prior steps before the last step are already completed!
        $allPriorToLastCompleted = $routes->where('step_number', '<', $totalSteps)->every(fn ($r) => $r->status === 'completed');
        $isAtLastStep = $allPriorToLastCompleted && (
            ($activeStep && $activeStep->step_number >= $totalSteps)
            || ($record->UnitID && $lastRoute && $record->UnitID == $lastRoute->unit_id && $record->TrailStatus !== 'In-Transit')
            || ($record->location && $lastRoute && stripos($record->location, $lastRoute->office_name) !== false && $record->TrailStatus !== 'In-Transit')
        );

        if ($isAtLastStep) {
            $terminalStep = $activeStep ?: $lastRoute;

            return [
                'is_incoming_action' => false,
                'is_terminal' => true,
                'action_type' => 'Terminal',
                'step_number' => $totalSteps,
                'total_steps' => $totalSteps,
                'office_name' => $terminalStep->office_name,
                'suggested_dest_id' => null,
                'suggested_action' => $terminalStep->for_action ?: 'Final Action Completed & Conclude Document Trail',
                'agency_action' => $terminalStep->for_action ?: 'Conclude routing trail and archive document',
                'allocated_time' => $terminalStep->processing_time ?: ($terminalStep->sla_hours ? $terminalStep->sla_hours.'h' : 'Terminal'),
                'recommendation_title' => 'Final Action / Terminal Completion',
                'recommendation_reason' => "Document has reached the final station ({$terminalStep->office_name}). Complete the required action and mark the document trail as Terminal (Done).",
            ];
        }

        // 4. CASE: Recommend the next pending step
        $nextStep = $routes->firstWhere('status', 'pending');
        if ($nextStep) {
            $isLastStep = ($nextStep->step_number >= $totalSteps);

            return [
                'is_incoming_action' => false,
                'is_terminal' => false,
                'action_type' => 'Outgoing',
                'step_number' => $nextStep->step_number,
                'total_steps' => $totalSteps,
                'office_name' => $nextStep->office_name,
                'suggested_dest_id' => $nextStep->unit_id,
                'suggested_action' => $nextStep->for_action ?: 'For verification & processing',
                'agency_action' => $nextStep->for_action ?: 'For verification & processing',
                'allocated_time' => $nextStep->processing_time ?: ($nextStep->sla_hours ? $nextStep->sla_hours.'h' : '1 Day'),
                'recommendation_title' => $isLastStep ? 'Next Station (Final Stop)' : 'Suggested Next Station',
                'recommendation_reason' => $isLastStep
                    ? "Dispatch to the final routing station ({$nextStep->office_name}) for final approval/completion."
                    : "Sequential Step {$nextStep->step_number} of {$totalSteps} under configured document route.",
            ];
        }

        // 5. CASE: All predefined steps completed; recommend terminal completion
        return [
            'is_incoming_action' => false,
            'is_terminal' => true,
            'action_type' => 'Terminal',
            'step_number' => $totalSteps,
            'total_steps' => $totalSteps,
            'office_name' => $lastRoute?->office_name ?: 'Central Records & Archives',
            'suggested_dest_id' => null,
            'suggested_action' => 'Completed & Terminal Custody Achieved',
            'agency_action' => 'Archive and Conclude Document Tracking Trail',
            'allocated_time' => 'Terminal',
            'recommendation_title' => 'Conclude Document Trail',
            'recommendation_reason' => "All {$totalSteps} configured routing stations have completed handling. Archive file into Central Records.",
        ];
    }
}
