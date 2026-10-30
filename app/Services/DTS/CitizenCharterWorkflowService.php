<?php

namespace App\Services\Dts;

use App\Models\Dts\CitizenCharter;
use App\Models\Dts\CitizenCharterStep;
use App\Models\Dts\DocumentType;
use App\Models\Dts\Records;
use App\Models\Dts\Unit;
use Carbon\Carbon;

class CitizenCharterWorkflowService
{
    /**
     * Resolve the Citizen's Charter associated with a given record or document type name.
     * Prioritizes the DocumentType master configuration and its configured Routing Flow.
     */
    public function resolveCharter(Records|string|null $target): ?CitizenCharter
    {
        $docTypeName = $target instanceof Records ? $target->doc_type : $target;
        $tenantId = $target instanceof Records ? $target->tenant_id : null;

        if (empty($docTypeName)) {
            return null;
        }

        // 0. If record has explicit doc_type_id foreign key, resolve directly!
        if ($target instanceof Records && ! empty($target->doc_type_id)) {
            $docType = DocumentType::with(['routingTemplate.steps.unit', 'citizenCharter.steps.unit'])->find($target->doc_type_id);
            if ($docType) {
                if ($docType->citizenCharter && $docType->citizenCharter->steps->isNotEmpty()) {
                    return $docType->citizenCharter;
                }
                if ($docType->routingTemplate && $docType->routingTemplate->steps->isNotEmpty()) {
                    $charter = CitizenCharter::firstOrCreate(
                        ['doc_type_id' => $docType->id],
                        [
                            'tenant_id' => $docType->tenant_id,
                            'service_code' => $docType->citizen_charter_code ?: ($docType->code ?: 'CC-'.$docType->id),
                            'service_name' => $docType->name,
                            'service_type' => $docType->category === 'simple' ? 'Simple' : 'Complex',
                            'standard_turnaround_days' => $docType->standard_sla_days ?: 3,
                            'is_active' => true,
                        ]
                    );

                    if ($charter->steps()->count() === 0) {
                        foreach ($docType->routingTemplate->steps as $s) {
                            CitizenCharterStep::create([
                                'charter_id' => $charter->id,
                                'step_number' => $s->step_number ?? $s->step_order ?? 1,
                                'unit_id' => $s->unit_id,
                                'office_name' => $s->unit?->unit_name ?: ($s->unit_name ?: 'Designated Unit'),
                                'client_step' => 'Submit document / transmittal for '.($s->for_action ?: ($s->action_required ?: 'processing')),
                                'agency_action' => $s->for_action ?: ($s->instructions ?: ($s->action_required ?: 'Process document')),
                                'person_responsible' => 'Designated Action Officer',
                                'processing_time' => $s->processing_time ?: ($s->sla_hours ? $s->sla_hours.' Hours' : '1 Day'),
                                'fees' => 'None',
                            ]);
                        }
                    }

                    return $charter->load('steps.unit');
                }
            }
        }

        // 1. Prioritize DocumentType by name or code (master source of truth for DTS)
        $docTypeQuery = DocumentType::with(['routingTemplate.steps.unit', 'citizenCharter.steps.unit']);
        if ($tenantId) {
            $docTypeQuery->where('tenant_id', $tenantId);
        }

        $docType = $docTypeQuery->where(function ($q) use ($docTypeName) {
            $q->where('name', $docTypeName)
                ->orWhere('code', $docTypeName)
                ->orWhere('name', 'LIKE', $docTypeName);
        })->first();

        // Fallback without tenant constraint
        if (! $docType) {
            $docType = DocumentType::with(['routingTemplate.steps.unit', 'citizenCharter.steps.unit'])
                ->where(function ($q) use ($docTypeName) {
                    $q->where('name', $docTypeName)
                        ->orWhere('code', $docTypeName)
                        ->orWhere('name', 'LIKE', "%{$docTypeName}%");
                })->first();
        }

        if ($docType) {
            // A. If citizenCharter already has configured steps
            if ($docType->citizenCharter && $docType->citizenCharter->steps->isNotEmpty()) {
                return $docType->citizenCharter;
            }

            // B. If routingTemplate has steps, ensure CitizenCharter is synchronized
            if ($docType->routingTemplate && $docType->routingTemplate->steps->isNotEmpty()) {
                $charter = CitizenCharter::firstOrCreate(
                    ['doc_type_id' => $docType->id],
                    [
                        'tenant_id' => $docType->tenant_id,
                        'service_code' => $docType->citizen_charter_code ?: ($docType->code ?: 'CC-'.$docType->id),
                        'service_name' => $docType->name,
                        'service_type' => $docType->category === 'simple' ? 'Simple' : 'Complex',
                        'standard_turnaround_days' => $docType->standard_sla_days ?: 3,
                        'is_active' => true,
                    ]
                );

                if ($charter->steps()->count() === 0) {
                    foreach ($docType->routingTemplate->steps as $s) {
                        CitizenCharterStep::create([
                            'charter_id' => $charter->id,
                            'step_number' => $s->step_order,
                            'unit_id' => $s->unit_id,
                            'office_name' => $s->unit?->unit_name ?: 'Designated Unit',
                            'client_step' => 'Submit document / transmittal for '.($s->action_required ?: 'processing'),
                            'agency_action' => $s->instructions ?: ($s->action_required ?: 'Process document'),
                            'person_responsible' => 'Designated Action Officer',
                            'processing_time' => $s->processing_time ?: ($s->sla_hours ? $s->sla_hours.' Hours' : '1 Day'),
                            'fees' => 'None',
                        ]);
                    }
                }

                return $charter->load('steps.unit');
            }

            // C. Try via citizen_charter_code link
            if ($docType->citizen_charter_code) {
                $charter = CitizenCharter::with('steps.unit')
                    ->where('service_code', $docType->citizen_charter_code)
                    ->where('is_active', true)
                    ->first();

                if ($charter && $charter->steps->isNotEmpty()) {
                    return $charter;
                }
            }
        }

        // 2. Direct match in CitizenCharter by service_name or service_code
        $charterQuery = CitizenCharter::with('steps.unit')->where('is_active', true);
        if ($tenantId) {
            $charterQuery->where('tenant_id', $tenantId);
        }

        $charter = $charterQuery->where(function ($q) use ($docTypeName) {
            $q->where('service_name', 'LIKE', "%{$docTypeName}%")
                ->orWhere('service_code', $docTypeName);
        })->first();

        if ($charter && $charter->steps->isNotEmpty()) {
            return $charter;
        }

        // Final fallback across all tenants
        return CitizenCharter::with('steps.unit')
            ->where('is_active', true)
            ->where(function ($q) use ($docTypeName) {
                $q->where('service_name', 'LIKE', "%{$docTypeName}%")
                    ->orWhere('service_code', $docTypeName);
            })
            ->first();
    }

    /**
     * Parse human-readable SLA strings into total working minutes.
     * Supports:
     * - Minutes: "15m", "15 mins", "30 Minutes"
     * - Hours: "1h", "4 hrs", "4 Hours"
     * - Days: "1d", "2 Days", "1.5 Working Days" (8 hours/day under ARTA CSC standard)
     */
    public function parseProcessingTimeToMinutes(string|int|float|null $processingTime): int
    {
        if (empty($processingTime)) {
            return 480; // default 1 working day (8 hours)
        }

        if (is_numeric($processingTime)) {
            return (int) $processingTime;
        }

        $str = strtolower(trim($processingTime));

        // Days: "1d", "2 days", "3 working days", "1.5 days"
        if (preg_match('/([\d\.]+)\s*(?:d|day|days|working day|working days)\b/', $str, $matches)) {
            return (int) round((float) $matches[1] * 8 * 60);
        }

        // Hours: "1h", "2 hrs", "4 hours"
        if (preg_match('/([\d\.]+)\s*(?:h|hr|hrs|hour|hours)\b/', $str, $matches)) {
            return (int) round((float) $matches[1] * 60);
        }

        // Minutes: "15m", "30 mins", "45 minutes"
        if (preg_match('/([\d\.]+)\s*(?:m|min|mins|minute|minutes)\b/', $str, $matches)) {
            return (int) round((float) $matches[1]);
        }

        return 480;
    }

    /**
     * Format minutes into readable duration.
     */
    public function formatMinutes(int $totalMinutes): string
    {
        if ($totalMinutes < 60) {
            return "{$totalMinutes} min".($totalMinutes === 1 ? '' : 's');
        }

        $hours = floor($totalMinutes / 60);
        $mins = $totalMinutes % 60;

        if ($hours < 8) {
            return "{$hours} hr".($hours === 1 ? '' : 's').($mins > 0 ? " {$mins}m" : '');
        }

        $days = floor($hours / 8);
        $remHours = $hours % 8;

        $parts = ["{$days} day".($days === 1 ? '' : 's')];
        if ($remHours > 0) {
            $parts[] = "{$remHours} hr".($remHours === 1 ? '' : 's');
        }
        if ($mins > 0 && $days < 3) {
            $parts[] = "{$mins}m";
        }

        return implode(' ', $parts);
    }

    /**
     * Predict the intelligent next-hop recommendation based on Citizen's Charter.
     * Non-enforcing suggestion for personnel.
     */
    public function predictNextStep(Records $record): ?array
    {
        if ($record->routes()->exists()) {
            return app(RecordWorkflowEngine::class)->predictNextStation($record);
        }

        $charter = $this->resolveCharter($record);
        if (! $charter || $charter->steps->isEmpty()) {
            return null;
        }

        $steps = $charter->steps->sortBy('step_number')->values();
        $totalSteps = $steps->count();

        // Case 1: If document is currently In-Transit
        // The receiving station MUST log Incoming / Received first before sending it anywhere!
        if ($record->TrailStatus === 'In-Transit') {
            $destDept = $record->destinationDepartment;
            $destName = $destDept?->dept_name ?: ($record->location ?: 'Receiving Station');

            $compliance = $this->getCharterStepsWithCompliance($record);
            $matchedStep = null;

            // 1. Check which step is currently identified as in_transit
            foreach ($compliance['steps'] as $cs) {
                if ($cs['status'] === 'in_transit') {
                    $matchedStep = $steps->firstWhere('step_number', $cs['step_number']);
                    break;
                }
            }

            // 2. If not found, look for first non-completed step matching destination
            if (! $matchedStep) {
                foreach ($compliance['steps'] as $cs) {
                    if ($cs['status'] !== 'completed') {
                        if (($cs['dept_id'] && $cs['dept_id'] == $record->Destination) ||
                            ($destDept && stripos($cs['office_name'], $destDept->dept_name) !== false)) {
                            $matchedStep = $steps->firstWhere('step_number', $cs['step_number']);
                            break;
                        }
                    }
                }
            }

            // 3. Fallback to any step matching destination
            if (! $matchedStep) {
                foreach ($steps as $st) {
                    $stUnit = $this->resolveStepUnit($st);
                    if (($stUnit && $stUnit['dept_id'] == $record->Destination) ||
                        ($destDept && stripos($st->office_name, $destDept->dept_name) !== false)) {
                        $matchedStep = $st;
                        break;
                    }
                }
            }

            $stepNumber = $matchedStep ? $matchedStep->step_number : $totalSteps;

            return [
                'is_incoming_action' => true,
                'action_type' => 'Incoming',
                'step_number' => $stepNumber,
                'total_steps' => $totalSteps,
                'is_terminal' => false,
                'office_name' => $destName,
                'suggested_dest_id' => null,
                'suggested_action' => 'Document Received and Custody Acknowledged',
                'allocated_time' => $matchedStep?->processing_time ?: 'Standard SLA',
                'person_responsible' => $matchedStep?->person_responsible ?: 'Receiving Officer',
                'client_step' => 'Present physical document / files for receiving',
                'agency_action' => 'Acknowledge custody & verify attachments to start SLA handling timer',
                'recommendation_title' => 'Incoming Receipt Required',
                'recommendation_reason' => "Document was dispatched and is In-Transit to {$destName}. The receiving station must log as Incoming to acknowledge physical custody and begin the Citizen's Charter turnaround SLA clock.",
            ];
        }

        // Case 2: Document is terminal/done
        if ($record->TrailStatus === 'Done') {
            return null;
        }

        // Case 3: Document is in custody (Received, Start, Return)
        // Get all movement logs
        $allLogs = $record->logs()->orderBy('id', 'asc')->get();

        // Check if the final step of the charter was already reached and completed
        $lastStep = $steps->last();
        $lastStepUnit = $this->resolveStepUnit($lastStep);

        foreach ($allLogs as $logIdx => $log) {
            if ($logIdx === 0 && $log->TrailStatus === 'Start') {
                continue;
            }

            $isLastStepMatch = false;
            if ($lastStepUnit && $lastStepUnit['dept_id']) {
                if ($log->UnitID == $lastStepUnit['dept_id'] || $log->Destination == $lastStepUnit['dept_id']) {
                    $isLastStepMatch = true;
                }
            } elseif ($lastStep->office_name) {
                if (($log->department && stripos($log->department->dept_name, $lastStep->office_name) !== false) ||
                    ($log->destinationDepartment && stripos($log->destinationDepartment->dept_name, $lastStep->office_name) !== false)) {
                    $isLastStepMatch = true;
                }
            }

            if ($isLastStepMatch) {
                // If there are movements after the last step, or if last step dispatched out, it is completed!
                if ($logIdx < $allLogs->count() - 1 || $log->TrailStatus === 'Done' || ($log->UnitID == ($lastStepUnit['dept_id'] ?? 0) && $log->TrailStatus !== 'Received')) {
                    return null; // All Citizen's Charter steps have concluded!
                }
            }
        }

        // If newly created / only origin log exists, start at Step 1
        if ($allLogs->count() <= 1) {
            $firstStep = $steps->first();

            return $this->formatStepSuggestion($firstStep, $record, 1, $totalSteps);
        }

        // Determine sequential progression
        $completedStepsCount = 0;

        foreach ($steps as $idx => $step) {
            $stepUnit = $this->resolveStepUnit($step);
            $reached = false;
            $dispatchedOut = false;

            foreach ($allLogs as $logIdx => $log) {
                if ($logIdx === 0 && $log->TrailStatus === 'Start') {
                    continue;
                }

                $isMatch = false;
                if ($stepUnit && $stepUnit['dept_id']) {
                    if ($log->Destination == $stepUnit['dept_id'] || ($log->UnitID == $stepUnit['dept_id'] && $log->TrailStatus === 'Received')) {
                        $isMatch = true;
                    }
                } elseif ($step->office_name) {
                    $destDeptName = $log->destinationDepartment?->dept_name;
                    $currDeptName = $log->department?->dept_name;
                    if (($destDeptName && stripos($destDeptName, $step->office_name) !== false) ||
                        ($currDeptName && stripos($currDeptName, $step->office_name) !== false && $log->TrailStatus === 'Received')) {
                        $isMatch = true;
                    }
                }

                if ($isMatch) {
                    $reached = true;
                    for ($k = $logIdx + 1; $k < $allLogs->count(); $k++) {
                        $subsequentLog = $allLogs[$k];
                        if ($subsequentLog->UnitID == ($stepUnit['dept_id'] ?? null) && $subsequentLog->Destination != ($stepUnit['dept_id'] ?? null)) {
                            $dispatchedOut = true;
                            break;
                        }
                    }
                    break;
                }
            }

            if ($reached && $dispatchedOut) {
                $completedStepsCount = $idx + 1;
            } elseif ($reached && ! $dispatchedOut) {
                $completedStepsCount = $idx + 1;
                break;
            } else {
                $completedStepsCount = $idx;
                break;
            }
        }

        if ($completedStepsCount >= $totalSteps) {
            return null; // All Citizen's Charter steps have been fulfilled!
        }

        $targetStep = $steps->get($completedStepsCount);

        return $this->formatStepSuggestion($targetStep, $record, $completedStepsCount + 1, $totalSteps);
    }

    /**
     * Compute full workflow steps with per-station SLA turnaround compliance.
     */
    public function getCharterStepsWithCompliance(Records $record): array
    {
        $routes = $record->routes()->with(['receiver', 'completer', 'unit'])->orderBy('step_number')->get();
        if ($routes->isNotEmpty()) {
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
                    'dept_id' => $r->dept_id,
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

            return [
                'has_charter' => true,
                'charter' => $this->resolveCharter($record),
                'steps' => $processedSteps,
                'total_allocated_time_formatted' => $this->formatMinutes(max(1, $totalAllocatedMinutes)),
                'total_actual_time_formatted' => $this->formatMinutes(max(0, $totalActualMinutes)),
                'total_allocated_minutes' => $totalAllocatedMinutes,
                'total_actual_minutes' => $totalActualMinutes,
                'any_delayed' => $anyDelayed,
                'all_completed' => $allCompleted,
                'overall_compliance' => $anyDelayed ? 'Delayed' : ($allCompleted ? 'Complied' : 'Within Target'),
            ];
        }

        $charter = $this->resolveCharter($record);
        if (! $charter || $charter->steps->isEmpty()) {
            return [
                'has_charter' => false,
                'charter' => null,
                'steps' => [],
                'overall_compliance' => 'N/A',
            ];
        }

        $steps = $charter->steps->sortBy('step_number')->values();
        $allLogs = $record->logs()->orderBy('created_at', 'asc')->get();

        $processedSteps = [];
        $totalAllocatedMinutes = 0;
        $totalActualMinutes = 0;
        $anyDelayed = false;
        $allCompleted = true;

        $hasReachedPrevious = true;

        $logCursor = 0;

        foreach ($steps as $index => $step) {
            $stepUnit = $this->resolveStepUnit($step);
            $allocatedMinutes = $this->parseProcessingTimeToMinutes($step->processing_time);
            $totalAllocatedMinutes += $allocatedMinutes;

            $status = 'pending';
            $complianceStatus = 'Pending';
            $actualMinutes = null;
            $timeRemainingText = null;
            $matchedLogDate = null;

            $deptId = $stepUnit['dept_id'] ?? null;
            $officeName = $step->office_name;

            // Search logs strictly from the current chronological cursor (supports multiple visits to the same office)
            $logsToSearch = $allLogs->slice($logCursor);

            // 1. Did a movement dispatch towards this office?
            $dispatchedToLog = null;
            foreach ($logsToSearch as $log) {
                if ($log->TrailStatus === 'In-Transit') {
                    if ($deptId && $log->Destination == $deptId) {
                        $dispatchedToLog = $log;
                        break;
                    } elseif ($officeName && $log->destinationDepartment && stripos($log->destinationDepartment->dept_name, $officeName) !== false) {
                        $dispatchedToLog = $log;
                        break;
                    }
                }
            }

            // If this step is the origin office where document was created
            $isOriginStep = ($index === 0 && $deptId && $record->UnitID == $deptId);

            // 2. Did this office officially log Received?
            $receivedLog = null;
            foreach ($logsToSearch as $log) {
                if ($log->TrailStatus === 'Received') {
                    if ($deptId && $log->UnitID == $deptId) {
                        $receivedLog = $log;
                        break;
                    } elseif ($officeName && $log->department && stripos($log->department->dept_name, $officeName) !== false) {
                        $receivedLog = $log;
                        break;
                    }
                }
            }

            // If origin step and no separate Received log, origin creation log counts as receipt/start
            if (! $receivedLog && $isOriginStep && $allLogs->isNotEmpty()) {
                $receivedLog = $allLogs->first();
            }

            // 3. Did this office subsequently exit / dispatch out?
            $exitLog = null;
            $refLog = $receivedLog ?: $dispatchedToLog;
            if ($refLog) {
                $passedRef = false;
                foreach ($logsToSearch as $log) {
                    if ($log->id == $refLog->id) {
                        $passedRef = true;
                        if (! $receivedLog && $log->TrailStatus !== 'Received') {
                            // If this log itself is an outgoing action from this unit
                            if (($deptId && $log->UnitID == $deptId) || ($officeName && $log->department && stripos($log->department->dept_name, $officeName) !== false)) {
                                $exitLog = $log;
                                break;
                            }
                        }

                        continue;
                    }
                    if ($passedRef && $log->TrailStatus !== 'Received') {
                        $isOutgoingFromHere = false;
                        if ($deptId && $log->UnitID == $deptId) {
                            $isOutgoingFromHere = true;
                        } elseif ($officeName && $log->department && stripos($log->department->dept_name, $officeName) !== false) {
                            $isOutgoingFromHere = true;
                        }

                        if ($isOutgoingFromHere) {
                            $exitLog = $log;
                            break;
                        }
                    }
                }
            } elseif ($logsToSearch->isNotEmpty()) {
                // Check if any log from this unit dispatched out
                foreach ($logsToSearch as $log) {
                    if ($log->TrailStatus !== 'Received') {
                        if (($deptId && $log->UnitID == $deptId) || ($officeName && $log->department && stripos($log->department->dept_name, $officeName) !== false)) {
                            $exitLog = $log;
                            break;
                        }
                    }
                }
            }

            // If exitLog was found but receivedLog was omitted (direct dispatch), infer receipt from dispatch/exit
            if (! $receivedLog && $exitLog) {
                $receivedLog = $dispatchedToLog ?: $exitLog;
            }

            // Advance the chronological log cursor when this station exits or receives
            if ($exitLog) {
                $exitPos = $allLogs->search(fn ($l) => $l->id === $exitLog->id);
                if ($exitPos !== false) {
                    $logCursor = $exitPos + 1;
                }
            } elseif ($receivedLog) {
                $recvPos = $allLogs->search(fn ($l) => $l->id === $receivedLog->id);
                if ($recvPos !== false) {
                    $logCursor = $recvPos;
                }
            }

            // 4. Check if any SUBSEQUENT step in the charter was already reached or completed
            $subsequentStepReached = false;
            for ($sIdx = $index + 1; $sIdx < $steps->count(); $sIdx++) {
                $futureStep = $steps->get($sIdx);
                $futureUnit = $this->resolveStepUnit($futureStep);
                $fDeptId = $futureUnit['dept_id'] ?? null;
                $fOfficeName = $futureStep->office_name;

                foreach ($allLogs->slice($logCursor) as $log) {
                    if (($fDeptId && ($log->UnitID == $fDeptId || $log->Destination == $fDeptId)) ||
                        ($fOfficeName && (($log->department && stripos($log->department->dept_name, $fOfficeName) !== false) ||
                                          ($log->destinationDepartment && stripos($log->destinationDepartment->dept_name, $fOfficeName) !== false)))) {
                        $subsequentStepReached = true;
                        break 2;
                    }
                }
            }

            // 5. Now evaluate state:
            if ($subsequentStepReached && (! $receivedLog || ! $exitLog)) {
                // If a later step in the charter was already reached or finished, this prior step is completed
                $status = 'completed';
                $actualMinutes = 1;
                $totalActualMinutes += $actualMinutes;
                $complianceStatus = 'Complied (On-Time)';
                $matchedLogDate = $exitLog?->created_at?->format('M d, Y h:i A') ?? $dispatchedToLog?->created_at?->format('M d, Y h:i A');
            } elseif ($receivedLog && $exitLog) {
                // Completed handling at this station
                $status = 'completed';
                $startTime = Carbon::parse($receivedLog->created_at);
                $endTime = Carbon::parse($exitLog->created_at);
                $actualMinutes = max(1, $startTime->diffInMinutes($endTime));
                $totalActualMinutes += $actualMinutes;
                $matchedLogDate = $exitLog->created_at->format('M d, Y h:i A');

                if ($actualMinutes <= $allocatedMinutes) {
                    $complianceStatus = 'Complied (On-Time)';
                } else {
                    $complianceStatus = 'Delayed / Breached SLA';
                    $status = 'delayed';
                    $anyDelayed = true;
                }
            } elseif ($receivedLog && ! $exitLog) {
                // Currently in custody at this station — TIMER IS RUNNING FROM RECEIPT!
                $allCompleted = false;
                $status = 'in_progress';
                $startTime = Carbon::parse($receivedLog->created_at);
                $actualMinutes = max(1, $startTime->diffInMinutes(now()));
                $totalActualMinutes += $actualMinutes;
                $matchedLogDate = $receivedLog->created_at->format('M d, Y h:i A');

                if ($actualMinutes <= $allocatedMinutes) {
                    $remaining = $allocatedMinutes - $actualMinutes;
                    $complianceStatus = 'In-Progress (Within SLA)';
                    $timeRemainingText = $this->formatMinutes($remaining).' remaining';
                } else {
                    $overdue = $actualMinutes - $allocatedMinutes;
                    $complianceStatus = 'Delayed / SLA Breached';
                    $timeRemainingText = 'Overdue by '.$this->formatMinutes($overdue);
                    $status = 'delayed';
                    $anyDelayed = true;
                }
                $hasReachedPrevious = false;
            } elseif ($dispatchedToLog && ! $receivedLog) {
                // Dispatched by previous office, but NOT received by this office yet!
                // Clock has NOT started yet!
                $allCompleted = false;
                $status = 'in_transit';
                $complianceStatus = 'In-Transit (Awaiting Receipt)';
                $matchedLogDate = 'Dispatched: '.$dispatchedToLog->created_at->format('M d, Y h:i A');
                $actualMinutes = null;
                $timeRemainingText = 'Timer starts upon Receipt';
                $hasReachedPrevious = false;
            } elseif ($index === 0 && $allLogs->count() <= 1) {
                // Initial step waiting for origin to dispatch
                $allCompleted = false;
                $status = 'pending';
                $complianceStatus = 'Pending (Awaiting Initial Dispatch)';
                $timeRemainingText = 'Awaiting dispatch from origin';
                $hasReachedPrevious = false;
            } else {
                $allCompleted = false;
                $status = 'pending';
                $complianceStatus = 'Pending';
                $timeRemainingText = 'Awaiting prior steps';
                $hasReachedPrevious = false;
            }

            $processedSteps[] = [
                'step_number' => $step->step_number,
                'office_name' => $step->office_name ?: ($stepUnit['name'] ?? 'Assigned Station'),
                'unit_id' => $step->unit_id,
                'dept_id' => $stepUnit['dept_id'] ?? null,
                'client_step' => $step->client_step,
                'agency_action' => $step->agency_action,
                'person_responsible' => $step->person_responsible ?: 'Designated Staff',
                'fees' => $step->fees ?: 'None',
                'allocated_time' => $step->processing_time,
                'allocated_minutes' => $allocatedMinutes,
                'actual_minutes' => $actualMinutes,
                'actual_time_formatted' => $actualMinutes !== null ? $this->formatMinutes($actualMinutes) : null,
                'time_remaining_text' => $timeRemainingText,
                'status' => $status,
                'compliance_status' => $complianceStatus,
                'matched_log_date' => $matchedLogDate,
            ];
        }

        $overallCompliance = 'Within SLA Target';
        if ($anyDelayed) {
            $overallCompliance = 'SLA Bottleneck Detected';
        } elseif ($allCompleted) {
            $overallCompliance = 'Fully Complied with ARTA SLA';
        }

        return [
            'has_charter' => true,
            'charter' => $charter,
            'steps' => $processedSteps,
            'total_allocated_time_formatted' => $this->formatMinutes($totalAllocatedMinutes),
            'total_actual_time_formatted' => $this->formatMinutes($totalActualMinutes),
            'any_delayed' => $anyDelayed,
            'all_completed' => $allCompleted,
            'overall_compliance' => $overallCompliance,
        ];
    }

    /**
     * Resolve unit for a step using dts_units.
     */
    protected function resolveStepUnit(CitizenCharterStep $step): ?array
    {
        if ($step->unit_id) {
            $unit = Unit::find($step->unit_id);
            if ($unit) {
                return [
                    'id' => $unit->id,
                    'unit_id' => $unit->id,
                    'name' => $unit->unit_name,
                    'dept_id' => $unit->id,
                ];
            }
        }

        if ($step->office_name) {
            $unit = Unit::where('unit_name', 'LIKE', "%{$step->office_name}%")
                ->orWhere('unit_code', 'LIKE', "%{$step->office_name}%")
                ->orWhere('short_name', 'LIKE', "%{$step->office_name}%")
                ->first();

            if ($unit) {
                return [
                    'id' => $unit->id,
                    'unit_id' => $unit->id,
                    'name' => $unit->unit_name,
                    'dept_id' => $unit->id,
                ];
            }
        }

        return null;
    }

    /**
     * Format standard prediction array.
     */
    protected function formatStepSuggestion(CitizenCharterStep $step, Records $record, int $stepNum, int $totalSteps): array
    {
        $stepUnit = $this->resolveStepUnit($step);

        return [
            'step_number' => $step->step_number,
            'total_steps' => $totalSteps,
            'is_terminal' => false,
            'office_name' => $step->office_name ?: ($stepUnit['name'] ?? 'Next Station'),
            'suggested_dest_id' => $stepUnit['dept_id'] ?? null,
            'suggested_action' => $step->agency_action ?: 'For verification & processing pursuant to Document Routing Flow',
            'allocated_time' => $step->processing_time,
            'person_responsible' => $step->person_responsible ?: 'Office Action Officer',
            'client_step' => $step->client_step,
            'agency_action' => $step->agency_action,
            'recommendation_reason' => "Sequential Step {$stepNum} of {$totalSteps} under configured Document Routing Flow.",
        ];
    }
}
