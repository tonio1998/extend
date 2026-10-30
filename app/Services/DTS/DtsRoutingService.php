<?php

namespace App\Services\DTS;

use App\Models\Dts\Records;
use App\Models\Dts\TrackLogs;
use App\Models\IPM\ProcessWorkflow;
use App\Models\IPM\ProcessWorkflowStep;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionProposal;
use App\Services\Signatories\CentralizedSignatoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DtsRoutingService
{
    /**
     * Register or retrieve the centralized DTS tracking record for a University Memorandum.
     * Automatically creates dts_records, assigns the unique QRCODE, and links it as tracking_number.
     */
    public static function registerMemorandum(UniversityMemorandum $memo, ?int $userId = null): ?Records
    {
        if (! Schema::hasTable('dts_records')) {
            return null;
        }
        $userId = $userId ?? Auth::id() ?? $memo->created_by ?? 1;

        // 1. If already linked and exists in dts_records, return it
        if ($memo->dts_record_id) {
            $existing = Records::find($memo->dts_record_id);
            if ($existing) {
                return $existing;
            }
        }

        // 2. Check if a DTS record matches this memo's existing QR code / tracking number
        // DO NOT search by DocumentID/memo_number alone, as seeded or draft memo numbers can repeat across batches
        if (! empty($memo->tracking_number) && ! str_starts_with($memo->tracking_number, 'DTS-TMP-')) {
            $cleanQr = ltrim($memo->tracking_number, '0');
            $existing = Records::where(function ($q) use ($memo, $cleanQr) {
                $q->where('QRCODE', $memo->tracking_number);
                if (is_numeric($cleanQr) && $cleanQr !== '') {
                    $q->orWhere('id', (int) $cleanQr)
                        ->orWhere('QRCODE', (string) $cleanQr);
                }
            })->first();

            if ($existing) {
                // Verify this record is not already claimed by a different memorandum
                $claimedByOther = UniversityMemorandum::where('dts_record_id', $existing->id)
                    ->where('id', '!=', $memo->id)
                    ->exists();

                if (! $claimedByOther) {
                    $memo->update(['dts_record_id' => $existing->id]);

                    return $existing;
                }
            }
        }

        return DB::transaction(function () use ($memo, $userId) {
            $originOffice = $memo->department?->unit_name ?: 'Office of the University President';

            // Ensure unique DocumentID for DTS
            $docId = $memo->memo_number;
            if (empty($docId) || Records::where('DocumentID', $docId)->exists()) {
                $docId = 'MEMO-'.($memo->series_year ?? date('Y')).'-'.str_pad($memo->id, 4, '0', STR_PAD_LEFT);
            }

            $record = Records::create([
                'tenant_id' => $memo->tenant_id ?? 1,
                'UnitID' => $memo->dept_id ?? 1,
                'Destination' => 0,
                'DocumentID' => $docId,
                'QRCODE' => 'DTS-TMP-'.Str::uuid(),
                'ConnectQR' => $memo->tracking_number,
                'type' => 'Internal',
                'doc_type' => 'University Memorandum',
                'Description' => 'Memorandum: '.$memo->subject,
                'Pages' => $memo->recipient_distribution_mode === 'separate_page' ? '2' : '1',
                'forAction' => $memo->action_required ?: 'For Compliance',
                'Remarks' => 'Official University Memorandum generated from Institutional Policy & Governance System.',
                'LastAction' => 1,
                'location' => $originOffice,
                'DateTimeReceived' => now(),
                'Origin' => $originOffice,
                'TransactionType' => 'Single',
                'Priority' => $memo->priority_level === 'Urgent' ? 1 : 2,
                'TrailStatus' => 'Start',
                'DocStatus' => 'active',
                'status' => 'active',
                'archived' => 0,
                'locked' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if ($record->QRCODE !== (string) $record->id) {
                $record->update(['QRCODE' => (string) $record->id]);
            }

            TrackLogs::create([
                'tenant_id' => $record->tenant_id,
                'RecordID' => $record->id,
                'TransactionID' => $record->id,
                'QRID' => (string) $record->id,
                'courier' => $userId,
                'UnitID' => $record->UnitID,
                'Destination' => 0,
                'forAction' => $memo->action_required ?: 'For Compliance',
                'TrailStatus' => 'Start',
                'comments' => "University Memorandum [{$record->DocumentID}] registered into Centralized DTS.",
                'location' => $originOffice,
                'status' => 'active',
                'archived' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $memoUpdates = ['dts_record_id' => $record->id];
            $currentTracking = $memo->tracking_number;
            $needsTracking = empty($currentTracking)
                || str_starts_with($currentTracking, 'DTS-TMP-')
                || UniversityMemorandum::where('tracking_number', $currentTracking)->where('id', '!=', $memo->id)->exists();

            if ($needsTracking) {
                $candidateQr = formatQR12Digits($record->id);
                if (UniversityMemorandum::where('tracking_number', $candidateQr)->where('id', '!=', $memo->id)->exists()) {
                    $candidateQr = 'MEMO-'.($memo->series_year ?? date('Y')).'-'.str_pad($memo->id, 5, '0', STR_PAD_LEFT);
                }
                $memoUpdates['tracking_number'] = $candidateQr;
            }

            $memo->update($memoUpdates);

            // Snapshot template signatories into runtime sys_document_signatories
            try {
                CentralizedSignatoryService::instantiateForDocument(
                    $memo,
                    'University Memorandum',
                    $memo->tenant_id ?? 1,
                    $memo->campus_id,
                    $memo->creator
                );
            } catch (\Throwable $sigEx) {
                report($sigEx);
            }

            return $record;
        });
    }

    /**
     * Register or retrieve the centralized DTS tracking record for an institutional policy/memorandum.
     * Automatically creates dts_records and initial dts_logs routing trail.
     */
    public static function registerPolicy(InstitutionalPolicy $policy, ?int $userId = null): ?Records
    {
        if (! Schema::hasTable('dts_records')) {
            return null;
        }
        $userId = $userId ?? Auth::id() ?? $policy->approved_by ?? $policy->proponent_id ?? 1;

        // 1. If already linked and exists in dts_records, return it
        if ($policy->dts_record_id) {
            $existing = Records::find($policy->dts_record_id);
            if ($existing) {
                return $existing;
            }
        }

        // 2. Check by ConnectQR or QRCODE, ensuring not claimed by another policy
        if (! empty($policy->tracking_number) && ! str_starts_with($policy->tracking_number, 'DTS-TMP-')) {
            $existing = Records::where(function ($q) use ($policy) {
                $q->where('ConnectQR', $policy->tracking_number)
                    ->orWhere('QRCODE', $policy->tracking_number);
            })->first();

            if ($existing) {
                $claimed = InstitutionalPolicy::where('dts_record_id', $existing->id)
                    ->where('id', '!=', $policy->id)
                    ->exists();

                if (! $claimed) {
                    $policy->update(['dts_record_id' => $existing->id]);

                    return $existing;
                }
            }
        }

        return DB::transaction(function () use ($policy, $userId) {
            // 4. Create row in centralized dts_records
            $record = Records::create([
                'tenant_id' => $policy->tenant_id ?? 1,
                'UnitID' => $policy->dept_id ?? 1,
                'DocumentID' => $policy->policy_number ?: ('POL-'.date('Y').'-'.str_pad($policy->id, 4, '0', STR_PAD_LEFT)),
                'QRCODE' => 'DTS-TMP-'.Str::uuid(),
                'ConnectQR' => $policy->tracking_number,
                'type' => 'Internal',
                'doc_type' => $policy->document_type ?: 'University Memorandum',
                'Description' => ($policy->document_type ?: 'Memorandum').': '.$policy->title,
                'Pages' => '1',
                'forAction' => 'For University-Wide Routing, Execution & Compliance',
                'Remarks' => 'Official institutional policy instrument promulgated for administrative routing.',
                'LastAction' => 1,
                'location' => 'Office of the University President',
                'Destination' => 0,
                'DateTimeReceived' => now(),
                'Origin' => 'Office of the University President',
                'TransactionType' => 'Single',
                'Priority' => 1,
                'TrailStatus' => 'Start',
                'DocStatus' => 'active',
                'status' => 'active',
                'archived' => 0,
                'locked' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Ensure QRCODE is strictly its actual database ID
            if ($record->QRCODE !== (string) $record->id) {
                $record->update(['QRCODE' => (string) $record->id]);
            }

            // 5. Create initial routing trail log in centralized dts_logs
            TrackLogs::create([
                'tenant_id' => $record->tenant_id,
                'RecordID' => $record->id,
                'TransactionID' => $record->id,
                'QRID' => (string) $record->id,
                'courier' => $userId,
                'UnitID' => $record->UnitID,
                'Destination' => 0,
                'forAction' => 'Official Document Generation & Routing Initiation',
                'TrailStatus' => 'Start',
                'comments' => "Official {$policy->document_type} [{$record->DocumentID}] generated via FPDF. Registered into Centralized Document Tracking System (DTS) for administrative routing.",
                'location' => 'Office of the University President',
                'status' => 'active',
                'archived' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 6. Update Policy record with linked DTS record ID and QRCODE as tracking_number
            $policy->update([
                'dts_record_id' => $record->id,
                'tracking_number' => formatQR12Digits($record->QRCODE ?: $record->id),
            ]);

            // 7. Sync with ProcessWorkflow for unified tracking view across IIPM-DSS
            $workflow = ProcessWorkflow::firstOrCreate(
                ['tracking_number' => (string) $record->id],
                [
                    'tenant_id' => $record->tenant_id,
                    'campus_id' => $policy->campus_id,
                    'dept_id' => $policy->dept_id,
                    'user_id' => $policy->proponent_id ?? $userId,
                    'process_type' => $policy->document_type ?: 'University Memorandum',
                    'title' => $policy->title,
                    'amount' => 0.00,
                    'fund_cluster' => '01 - Regular Agency Fund',
                    'current_stage' => 'Official Promulgation & University Routing',
                    'stage_number' => 6,
                    'total_stages' => 6,
                    'status' => 'Approved',
                    'qr_code' => (string) $record->id,
                    'submitted_at' => $policy->created_at ?? now(),
                    'completed_at' => now(),
                ]
            );

            ProcessWorkflowStep::firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'step_number' => 1,
                ],
                [
                    'stage_name' => 'Formulation & Official Generation',
                    'assigned_role' => 'University President / Secretariat',
                    'acted_by_user_id' => $userId,
                    'action' => 'Initiated',
                    'remarks' => "Generated official FPDF issuance with DTS Tracking # {$record->QRCODE}",
                    'acted_at' => now(),
                    'turnaround_minutes' => 0,
                ]
            );

            // Snapshot template signatories into runtime sys_document_signatories
            try {
                CentralizedSignatoryService::instantiateForDocument(
                    $policy,
                    $policy->document_type ?: 'Institutional Policy Code',
                    $policy->tenant_id ?? 1,
                    $policy->campus_id,
                    $policy->proponent
                );
            } catch (\Throwable $sigEx) {
                report($sigEx);
            }

            return $record;
        });
    }

    /**
     * Register or retrieve the centralized DTS tracking record for an Extension Proposal.
     * Automatically creates dts_records, assigns the unique 12-digit QRCODE/tracking_number,
     * logs initial route trail, and syncs unified ProcessWorkflow.
     */
    public static function registerExtensionProposal(ExtensionProposal $proposal, ?int $userId = null): ?Records
    {
        if (! Schema::hasTable('dts_records')) {
            return null;
        }
        $userId = $userId ?? Auth::id() ?? $proposal->project_leader_user_id ?? $proposal->prepared_by_user_id ?? 1;

        $proponentEmp = $proposal->projectLeaderEmployee ?? ($proposal->project_leader_employee_id ? Employee::find($proposal->project_leader_employee_id) : null);
        $proposalUnitId = $proposal->unit_id
            ?: ($proposal->academicProgram?->department_id
            ?: ($proposal->extensionPlan?->academicProgram?->department_id
            ?: ($proponentEmp?->department_id ?: 1)));

        if (empty($proposal->unit_id) && $proposalUnitId) {
            $proposal->update(['unit_id' => $proposalUnitId]);
        }

        // 1. If already linked and exists in dts_records, ensure tracking_number is synchronized and return
        if ($proposal->dts_record_id) {
            $existing = Records::find($proposal->dts_record_id);
            if ($existing) {
                $tracking = formatQR12Digits($existing->QRCODE ?: $existing->id);
                if ($proposal->tracking_number !== $tracking) {
                    $proposal->update(['tracking_number' => $tracking]);
                }

                // Ensure unified ProcessWorkflow exists for tracker
                ProcessWorkflow::firstOrCreate(
                    ['tracking_number' => (string) $existing->id],
                    [
                        'tenant_id' => $existing->tenant_id,
                        'campus_id' => $proposal->campus_id,
                        'dept_id' => $proposalUnitId,
                        'user_id' => $proposal->project_leader_user_id ?? $userId,
                        'process_type' => 'Extension Proposal',
                        'title' => $proposal->title,
                        'amount' => $proposal->budget_cash_amount ?? 0.00,
                        'fund_cluster' => '01 - Regular Agency Fund',
                        'current_stage' => '6-Tier Approval Routing',
                        'stage_number' => 1,
                        'total_stages' => 6,
                        'status' => 'In-Review',
                        'qr_code' => (string) $existing->id,
                        'submitted_at' => $proposal->created_at ?? now(),
                    ]
                );

                return $existing;
            }
        }

        // 2. Check by tracking_number or proposal_code in DTS records
        if (! empty($proposal->tracking_number) && ! str_starts_with($proposal->tracking_number, 'DTS-TMP-')) {
            $existing = Records::where(function ($q) use ($proposal) {
                $q->where('ConnectQR', $proposal->tracking_number)
                    ->orWhere('QRCODE', $proposal->tracking_number)
                    ->orWhere('DocumentID', $proposal->proposal_code);
            })->first();

            if ($existing) {
                $claimed = ExtensionProposal::where('dts_record_id', $existing->id)
                    ->where('id', '!=', $proposal->id)
                    ->exists();

                if (! $claimed) {
                    $proposal->update([
                        'dts_record_id' => $existing->id,
                        'tracking_number' => formatQR12Digits($existing->QRCODE ?: $existing->id),
                    ]);

                    return $existing;
                }
            }
        }

        return DB::transaction(function () use ($proposal, $userId, $proposalUnitId) {
            $docId = $proposal->proposal_code ?: ('PROP-'.date('Y').'-'.str_pad($proposal->id, 4, '0', STR_PAD_LEFT));
            $originOffice = 'College Extension Program / Office of RIE';

            // Create record in dts_records
            $record = Records::create([
                'tenant_id' => $proposal->tenant_id ?? 1,
                'UnitID' => $proposalUnitId,
                'DocumentID' => $docId,
                'QRCODE' => 'DTS-TMP-'.Str::uuid(),
                'ConnectQR' => $proposal->proposal_code,
                'type' => 'Internal',
                'doc_type' => 'Extension Proposal',
                'Description' => 'Extension Proposal: '.$proposal->title,
                'Pages' => '7',
                'forAction' => 'For 6-Tier Extension Proposal Evaluation, Endorsement & University Approval',
                'Remarks' => 'Official Extension Proposal generated from RIE System.',
                'LastAction' => 1,
                'location' => $originOffice,
                'Destination' => 0,
                'DateTimeReceived' => now(),
                'Origin' => $originOffice,
                'TransactionType' => 'Single',
                'Priority' => 1,
                'TrailStatus' => 'Start',
                'DocStatus' => 'active',
                'status' => 'active',
                'archived' => 0,
                'locked' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Set QRCODE to its database ID
            if ($record->QRCODE !== (string) $record->id) {
                $record->update(['QRCODE' => (string) $record->id]);
            }

            // Create initial routing trail log in centralized dts_logs
            TrackLogs::create([
                'tenant_id' => $record->tenant_id,
                'RecordID' => $record->id,
                'TransactionID' => $record->id,
                'QRID' => (string) $record->id,
                'courier' => $userId,
                'UnitID' => $record->UnitID,
                'Destination' => 0,
                'forAction' => 'Official Proposal Creation & 6-Tier Approval Routing Initiation',
                'TrailStatus' => 'Start',
                'comments' => "Extension Proposal [{$record->DocumentID}] registered into Centralized Document Tracking System (DTS). Initiated by Project Leader {$proposal->project_leader_name}.",
                'location' => $originOffice,
                'status' => 'active',
                'archived' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Formatted 12-digit tracking number
            $trackingNumber = formatQR12Digits($record->QRCODE ?: $record->id);

            // Update Proposal record
            $proposal->update([
                'dts_record_id' => $record->id,
                'tracking_number' => $trackingNumber,
            ]);

            // Sync with ProcessWorkflow for unified cross-system tracking
            $workflow = ProcessWorkflow::firstOrCreate(
                ['tracking_number' => (string) $record->id],
                [
                    'tenant_id' => $record->tenant_id,
                    'campus_id' => $proposal->campus_id,
                    'dept_id' => $proposal->academicProgram?->dept_id ?? 1,
                    'user_id' => $proposal->project_leader_user_id ?? $userId,
                    'process_type' => 'Extension Proposal',
                    'title' => $proposal->title,
                    'amount' => $proposal->budget_cash_amount ?? 0.00,
                    'fund_cluster' => '01 - Regular Agency Fund',
                    'current_stage' => '6-Tier Approval Routing',
                    'stage_number' => 1,
                    'total_stages' => 6,
                    'status' => 'In-Review',
                    'qr_code' => (string) $record->id,
                    'submitted_at' => $proposal->created_at ?? now(),
                ]
            );

            ProcessWorkflowStep::firstOrCreate(
                [
                    'workflow_id' => $workflow->id,
                    'step_number' => 1,
                ],
                [
                    'stage_name' => 'Proposal Formulation & Submission',
                    'assigned_role' => 'Project Leader / Extensionist',
                    'acted_by_user_id' => $userId,
                    'action' => 'Initiated',
                    'remarks' => "Created Extension Proposal [{$proposal->proposal_code}] with DTS Tracking # {$trackingNumber}",
                    'acted_at' => now(),
                    'turnaround_minutes' => 0,
                ]
            );

            return $record;
        });
    }

    /**
     * Append a routing step into dts_logs and update dts_records TrailStatus.
     */
    public static function recordTrailLog(
        Records $record,
        string $forAction,
        string $trailStatus = 'In-Transit',
        string $comments = '',
        ?int $userId = null
    ): ?TrackLogs {
        if (! Schema::hasTable('dts_logs')) {
            return null;
        }
        $userId = $userId ?? Auth::id() ?? 1;

        $validStatuses = ['Start', 'In-Transit', 'Pending', 'Received', 'Done', 'Return', 'Incoming', 'Outgoing', 'DFR', 'DNR', 'Open', 'forwarded'];
        $normalizedStatus = match (strtolower(str_replace([' ', '_'], '-', $trailStatus))) {
            'approved', 'promulgated', 'completed', 'released', 'done' => 'Done',
            'signed', 'in-transit', 'transit' => 'In-Transit',
            default => in_array($trailStatus, $validStatuses, true) ? $trailStatus : 'In-Transit',
        };

        $record->update([
            'TrailStatus' => $normalizedStatus,
            'updated_by' => $userId,
        ]);

        return TrackLogs::create([
            'tenant_id' => $record->tenant_id,
            'RecordID' => $record->id,
            'TransactionID' => $record->id,
            'QRID' => $record->QRCODE,
            'courier' => $userId,
            'UnitID' => $record->UnitID,
            'Destination' => 0,
            'forAction' => $forAction,
            'TrailStatus' => $normalizedStatus,
            'comments' => $comments,
            'location' => $record->location ?: 'Office of the University President',
            'status' => 'active',
            'archived' => 0,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    /**
     * Synchronize DocumentID and Description in dts_records when a memorandum or policy is updated.
     */
    public static function syncDocumentId(int $dtsRecordId, string $newDocumentId, ?string $newDescription = null): void
    {
        if (! Schema::hasTable('dts_records')) {
            return;
        }
        $dts = Records::find($dtsRecordId);
        if ($dts) {
            $updates = ['DocumentID' => $newDocumentId];
            if ($newDescription !== null) {
                $updates['Description'] = $newDescription;
            }
            $dts->update($updates);
        }
    }
}
