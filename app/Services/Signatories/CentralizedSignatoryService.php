<?php

namespace App\Services\Signatories;

use App\Models\Dts\DocumentType;
use App\Models\Employee;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Models\User;
use App\Services\DTS\DtsRoutingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * CANONICAL CENTRALIZED SIGNATORY ENGINE
 *
 * Single Source of Truth for ALL institutional document signatories across the entire DSS suite
 * (Extension Plans, Institutional Policies, University Memorandums, DTS Routing Slips, Activity Designs, etc.).
 *
 * Scoped dynamically per:
 *  - Tenant Institution (tenant_id)
 *  - Campus Branch (campus_id)
 *  - Document Process Type (doc_type_id referencing dts_document_types)
 *
 * STANDING ARCHITECTURAL RULE:
 *  Do NOT create separate or redundant signatory tables/matrices.
 *  All modules must resolve approval workflows through this service via:
 *      CentralizedSignatoryService::getSignatoriesFor(...)
 *      CentralizedSignatoryService::recordDigitalSignature(...)
 */
class CentralizedSignatoryService
{
    /**
     * Resolve signatories for a given document type, institution, and optional campus.
     */
    public static function getSignatoriesFor(
        DocumentType|string|int $docType,
        int $tenantId,
        ?int $campusId = null,
        ?User $author = null
    ): Collection {
        $docTypeId = null;
        $processTypeName = null;

        if ($docType instanceof DocumentType) {
            $docTypeId = $docType->id;
            $processTypeName = $docType->name;
        } elseif (is_numeric($docType)) {
            $dt = DocumentType::find($docType);
            $docTypeId = $dt?->id;
            $processTypeName = $dt?->name;
        } else {
            $processTypeName = (string) $docType;
            $dt = DocumentType::where('tenant_id', $tenantId)
                ->where(function ($q) use ($processTypeName) {
                    $q->where('name', $processTypeName)
                        ->orWhere('code', $processTypeName);
                })->first();
            $docTypeId = $dt?->id;
        }

        // 1. Campus-specific query
        $query = DocumentSignatoryMatrix::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) use ($docTypeId, $processTypeName) {
                if ($docTypeId) {
                    $q->where('doc_type_id', $docTypeId);
                }
                if ($processTypeName) {
                    $q->orWhere('process_type', $processTypeName);
                }
            });

        if ($campusId) {
            $campusQuery = (clone $query)->where('campus_id', $campusId);
            $signatories = $campusQuery->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

            if ($signatories->isNotEmpty()) {
                return self::hydrateDynamicAssignees($signatories, $author, $campusId);
            }
        }

        // 2. Fallback to Global / Tenant-wide (campus_id is null)
        $globalQuery = (clone $query)->whereNull('campus_id');
        $signatories = $globalQuery->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        if ($signatories->isNotEmpty()) {
            return self::hydrateDynamicAssignees($signatories, $author, $campusId);
        }

        // 3. Fallback to Generic Default if none exists for this specific document type
        $genericQuery = DocumentSignatoryMatrix::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('process_type', 'Generic (Default)')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($genericQuery->isNotEmpty()) {
            return self::hydrateDynamicAssignees($genericQuery, $author, $campusId);
        }

        return collect();
    }

    /**
     * Hydrate dynamic assignees (e.g. Current Author, Dept Head, Campus Director).
     */
    protected static function hydrateDynamicAssignees(Collection $matrices, ?User $author, ?int $campusId): Collection
    {
        return $matrices->map(function (DocumentSignatoryMatrix $item) use ($author) {
            $clone = clone $item;

            if ($item->assignment_type === 'dynamic_author' && $author) {
                $clone->employee_id = $author->employee_id;
                $clone->user_id = $author->id;
                $clone->signatory_name = $author->employee?->full_name ?: $author->name;
                $clone->designation = $author->primaryDesignation?->designation_title ?: ($author->designation ?: 'Project Leader');
                $clone->office = $author->dtsUnit()?->name;
            } elseif ($item->employee) {
                $clone->signatory_name = $item->employee->full_name;
                $clone->designation = $item->employee->designation ?: $item->designation;
            } elseif ($item->user) {
                $clone->signatory_name = $item->user->name;
                $clone->designation = $item->designation ?: $item->user->designation;
            }

            return $clone;
        });
    }

    /**
     * Record a verified digital signature onto a document model.
     */
    public static function recordDigitalSignature(
        Model $document,
        string $tierRole,
        User $signer,
        array $data = []
    ): array {
        $metadata = $document->metadata ?? [];
        $signatures = $metadata['signatures'] ?? [];

        $signerName = $signer->employee?->full_name ?: $signer->name;
        $designation = $data['designation'] ?? ($signer->primaryDesignation?->designation_title ?: ($signer->designation ?: 'Authorized Signatory'));
        $signatureImg = $data['signature_img'] ?? null; // base64 PNG data URL
        $signatureType = $data['signature_type'] ?? 'drawn'; // drawn, font, upload
        $remarks = $data['remarks'] ?? 'Verified and electronically signed.';

        $docCode = $document->plan_code ?? ($document->tracking_number ?? ($document->DocumentID ?? 'DOC'));
        $salt = (string) Str::uuid();
        $hash = strtoupper(substr(hash('sha256', "{$docCode}-{$tierRole}-{$signer->id}-".now()->timestamp."-{$salt}"), 0, 16));
        $fullSigRef = "SIG-{$docCode}-{$hash}";

        $sigRecord = [
            'signed' => true,
            'role_label' => $data['role_label'] ?? ucfirst($tierRole).' by',
            'signed_by_user_id' => $signer->id,
            'signed_by_employee_id' => $signer->employee_id,
            'signer_name' => $signerName,
            'designation' => $designation,
            'signature_img' => $signatureImg,
            'signature_type' => $signatureType,
            'signature_hash' => $fullSigRef,
            'signed_at' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 150),
            'remarks' => $remarks,
        ];

        $signatures[$tierRole] = $sigRecord;
        $metadata['signatures'] = $signatures;
        $document->metadata = $metadata;

        // Sync timestamp column if exists
        $timestampColumn = "{$tierRole}_at";
        if (in_array($timestampColumn, $document->getFillable())) {
            $document->{$timestampColumn} = now();
        }

        $document->save();

        // Sync with runtime document instance table (sys_document_signatories)
        $instance = DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->where('tier_role', $tierRole)
            ->first();

        if ($instance) {
            $instance->update([
                'status' => 'signed',
                'signed_at' => now(),
                'signed_by_user_id' => $signer->id,
                'signature_img' => $signatureImg,
                'signature_type' => $signatureType,
                'signature_hash' => $fullSigRef,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 150),
                'remarks' => $remarks,
            ]);
        }

        // Integrate with DTS record if linked
        if (isset($document->dtsRecord) && $document->dtsRecord) {
            $trailStatus = ($tierRole === 'approved') ? 'Done' : 'In-Transit';
            DtsRoutingService::recordTrailLog(
                $document->dtsRecord,
                "Digitally Signed: {$sigRecord['role_label']}",
                $trailStatus,
                "Verified digital endorsement by {$signerName} ({$designation}) [Ref: {$fullSigRef}]",
                $signer->id
            );
        }

        return $sigRecord;
    }

    /**
     * Instantiate standard matrix templates into the runtime sys_document_signatories table.
     * This snapshot links the signatories to the specific document ID and powers the "For Signature" queue.
     */
    public static function instantiateForDocument(
        Model $document,
        DocumentType|string|int $docType,
        int $tenantId,
        ?int $campusId = null,
        ?User $author = null,
        array $overrides = [],
        bool $forceSync = false
    ): Collection {
        $dtsRecordId = $document->dts_record_id ?? ($document->dtsRecord?->id ?? null);
        $docTypeId = null;
        if ($docType instanceof DocumentType) {
            $docTypeId = $docType->id;
        } elseif (is_numeric($docType)) {
            $docTypeId = (int) $docType;
        }

        // Auto-extract document-level creator inputs if not in $overrides
        $creatorInputs = $overrides;

        // 1. Prepared by (Author / Creator)
        if (! isset($creatorInputs['prepared'])) {
            $prepUserId = $document->prepared_by_user_id ?? ($author?->id ?? null);
            $prepEmpId = $document->prepared_by_employee_id ?? ($author?->employee_id ?? null);
            $prepName = $document->prepared_by_name ?? ($author?->employee?->full_name ?? ($author?->name ?? null));
            $prepDesig = $document->prepared_by_designation ?? ($author?->designation ?? 'Project Leader');
            if ($prepName || $prepUserId || $prepEmpId) {
                $creatorInputs['prepared'] = [
                    'role_label' => 'Prepared by:',
                    'user_id' => $prepUserId,
                    'employee_id' => $prepEmpId,
                    'signatory_name' => $prepName,
                    'designation' => $prepDesig,
                    'sort_order' => 1,
                    'position_column' => 1,
                ];
            }
        }

        // 2. Reviewed by (College Extension Head)
        if (! isset($creatorInputs['reviewed']) && ! empty($document->reviewed_by_employee_id)) {
            $revEmp = $document->reviewedByEmployee ?? Employee::find($document->reviewed_by_employee_id);
            $revUserId = $document->reviewed_by_user_id ?? ($revEmp ? User::where('employee_id', $revEmp->id)->value('id') : null);
            $creatorInputs['reviewed'] = [
                'role_label' => 'Reviewed by:',
                'employee_id' => $document->reviewed_by_employee_id,
                'user_id' => $revUserId,
                'signatory_name' => $document->reviewed_by_name ?? $revEmp?->full_name,
                'designation' => $document->reviewed_by_designation ?? ($revEmp?->designation ?? 'College Head for Extension'),
                'office' => $revEmp?->department?->unit_name ?: 'College Extension Office',
                'sort_order' => 1,
                'position_column' => 2,
            ];
        }

        // 3. Noted by (Creator selected employee / Dean)
        if (! isset($creatorInputs['noted']) && ! empty($document->noted_by_employee_id)) {
            $notedEmp = $document->notedByEmployee ?? Employee::find($document->noted_by_employee_id);
            $notedUserId = $notedEmp ? User::where('employee_id', $notedEmp->id)->value('id') : null;
            $creatorInputs['noted'] = [
                'role_label' => 'Noted by:',
                'employee_id' => $document->noted_by_employee_id,
                'user_id' => $notedUserId,
                'signatory_name' => $document->noted_by_name ?? $notedEmp?->full_name,
                'designation' => $document->noted_by_designation ?? ($notedEmp?->designation ?? 'College Dean'),
                'office' => $notedEmp?->department?->unit_name ?: 'Office of the Dean',
                'sort_order' => 1,
                'position_column' => 3,
            ];
        }

        // 4. Approved by (Legacy or direct Dean approval)
        if (! isset($creatorInputs['approved']) && ! empty($document->approved_by_employee_id) && empty($document->reviewed_by_employee_id)) {
            $apprEmp = $document->approvedByEmployee ?? Employee::find($document->approved_by_employee_id);
            $apprUserId = $apprEmp ? User::where('employee_id', $apprEmp->id)->value('id') : null;
            $creatorInputs['approved'] = [
                'role_label' => 'Approved by:',
                'employee_id' => $document->approved_by_employee_id,
                'user_id' => $apprUserId,
                'signatory_name' => $document->approved_by_name ?? $apprEmp?->full_name,
                'designation' => $document->approved_by_designation ?? ($apprEmp?->designation ?? 'Dean'),
                'office' => $apprEmp?->department?->unit_name ?: 'Office of the Dean',
                'sort_order' => 1,
                'position_column' => 3,
            ];
        }

        // 5. Concurred by (Institutional Extension Specialists / Director)
        if (! isset($creatorInputs['concurred']) && ! empty($document->concurred_by_employee_id)) {
            $concEmp = $document->concurredByEmployee ?? Employee::find($document->concurred_by_employee_id);
            $concUserId = $concEmp ? User::where('employee_id', $concEmp->id)->value('id') : null;
            $creatorInputs['concurred'] = [
                'role_label' => 'Concurred by:',
                'employee_id' => $document->concurred_by_employee_id,
                'user_id' => $concUserId,
                'signatory_name' => $document->concurred_by_name ?? $concEmp?->full_name,
                'designation' => $document->concurred_by_designation ?? ($concEmp?->designation ?? 'University Extension Specialist V'),
                'office' => 'University Extension Services Office',
                'sort_order' => 1,
                'position_column' => 4,
            ];
        }

        // Check existing runtime records in sys_document_signatories
        $existing = DocumentSignatory::where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->orderBy('position_column')
            ->orderBy('sort_order')
            ->get();

        if ($existing->isNotEmpty()) {
            if (! $forceSync) {
                return $existing;
            }

            // If forceSync is true: update pending (unsigned) rows to reflect latest creator inputs
            foreach ($existing as $row) {
                if ($row->status !== 'signed') {
                    $tier = $row->tier_role;
                    $cData = $creatorInputs[$tier] ?? null;
                    if (! $cData && $tier === 'reviewed' && isset($creatorInputs['noted'])) {
                        $cData = $creatorInputs['noted'];
                    }

                    if ($cData) {
                        $empId = $cData['employee_id'] ?? $row->employee_id;
                        $userId = $cData['user_id'] ?? ($empId ? User::where('employee_id', $empId)->value('id') : $row->user_id);
                        $row->update([
                            'employee_id' => $empId,
                            'user_id' => $userId,
                            'signatory_name' => $cData['signatory_name'] ?? $row->signatory_name,
                            'designation' => $cData['designation'] ?? $row->designation,
                            'office' => $cData['office'] ?? $row->office,
                            'role_label' => $cData['role_label'] ?? $row->role_label,
                            'tier_role' => (isset($creatorInputs['noted']) && $tier === 'reviewed') ? 'noted' : $row->tier_role,
                            'assignment_type' => 'creator_selected',
                        ]);
                    }
                }
            }

            // Check if any creator tier is missing completely from existing rows
            $existingTiers = $existing->pluck('tier_role')->toArray();
            foreach ($creatorInputs as $tierRole => $data) {
                if (! in_array($tierRole, $existingTiers) && ! ($tierRole === 'noted' && in_array('reviewed', $existingTiers))) {
                    $empId = $data['employee_id'] ?? null;
                    $userId = $data['user_id'] ?? ($empId ? User::where('employee_id', $empId)->value('id') : null);

                    DocumentSignatory::create([
                        'tenant_id' => $tenantId,
                        'campus_id' => $campusId,
                        'matrix_id' => null,
                        'doc_type_id' => $docTypeId,
                        'document_type' => $document->getMorphClass(),
                        'document_id' => $document->getKey(),
                        'dts_record_id' => $dtsRecordId,
                        'tier_role' => $tierRole,
                        'role_label' => $data['role_label'] ?? (ucfirst($tierRole).' by:'),
                        'assignment_type' => 'creator_selected',
                        'employee_id' => $empId,
                        'user_id' => $userId,
                        'signatory_name' => $data['signatory_name'] ?? null,
                        'designation' => $data['designation'] ?? null,
                        'office' => $data['office'] ?? null,
                        'sort_order' => $data['sort_order'] ?? 1,
                        'position_column' => $data['position_column'] ?? 1,
                        'col_span' => 1,
                        'alignment' => 'left',
                        'status' => 'pending',
                        'requires_digital_signature' => true,
                        'is_mandatory' => true,
                    ]);
                }
            }

            return DocumentSignatory::where('document_type', $document->getMorphClass())
                ->where('document_id', $document->getKey())
                ->orderBy('position_column')
                ->orderBy('sort_order')
                ->get();
        }

        $templates = self::getSignatoriesFor($docType, $tenantId, $campusId, $author);
        $created = collect();
        $handledCreatorTiers = [];

        // If templates exist in matrix, map over templates and override with creator-selected personnel
        if ($templates->isNotEmpty()) {
            foreach ($templates as $tmpl) {
                $tierRole = $tmpl->tier_role;
                $creatorData = $creatorInputs[$tierRole] ?? null;

                if (! $creatorData) {
                    if ($tierRole === 'reviewed' && isset($creatorInputs['noted'])) {
                        $creatorData = $creatorInputs['noted'];
                        $handledCreatorTiers['noted'] = true;
                    } elseif ($tierRole === 'noted' && isset($creatorInputs['reviewed'])) {
                        $creatorData = $creatorInputs['reviewed'];
                        $handledCreatorTiers['reviewed'] = true;
                    }
                } else {
                    $handledCreatorTiers[$tierRole] = true;
                }

                // If template assignment_type is creator_selected OR if document has explicit creator selection for this tier
                $useCreator = ($tmpl->assignment_type === 'creator_selected')
                    || ($tmpl->assignment_type === 'dynamic_author' && $tierRole === 'prepared')
                    || (! empty($creatorData['employee_id']));

                $empId = $useCreator ? ($creatorData['employee_id'] ?? $tmpl->employee_id) : ($tmpl->employee_id ?? ($creatorData['employee_id'] ?? null));
                $userId = $useCreator ? ($creatorData['user_id'] ?? $tmpl->user_id) : ($tmpl->user_id ?? ($creatorData['user_id'] ?? null));
                if (! $userId && $empId) {
                    $userId = User::where('employee_id', $empId)->value('id');
                }

                $sigName = $useCreator ? ($creatorData['signatory_name'] ?? ($tmpl->signatory_name ?: $tmpl->resolved_name)) : ($tmpl->signatory_name ?: ($creatorData['signatory_name'] ?? $tmpl->resolved_name));
                $sigDesig = $useCreator ? ($creatorData['designation'] ?? ($tmpl->designation ?: $tmpl->resolved_designation)) : ($tmpl->designation ?: ($creatorData['designation'] ?? $tmpl->resolved_designation));
                $office = $useCreator ? ($creatorData['office'] ?? $tmpl->office) : ($tmpl->office ?? ($creatorData['office'] ?? null));
                $roleLabel = ($useCreator && ! empty($creatorData['role_label'])) ? $creatorData['role_label'] : $tmpl->role_label;

                $created->push(DocumentSignatory::create([
                    'tenant_id' => $tenantId,
                    'campus_id' => $campusId,
                    'matrix_id' => $tmpl->id,
                    'doc_type_id' => $tmpl->doc_type_id ?: $docTypeId,
                    'document_type' => $document->getMorphClass(),
                    'document_id' => $document->getKey(),
                    'dts_record_id' => $dtsRecordId,
                    'tier_role' => $tierRole,
                    'role_label' => $roleLabel,
                    'assignment_type' => $useCreator ? 'creator_selected' : $tmpl->assignment_type,
                    'employee_id' => $empId,
                    'user_id' => $userId,
                    'signatory_name' => $sigName,
                    'designation' => $sigDesig,
                    'office' => $office,
                    'sort_order' => $tmpl->sort_order,
                    'position_column' => $tmpl->position_column,
                    'col_span' => $tmpl->col_span ?? 1,
                    'alignment' => $tmpl->alignment ?? 'left',
                    'status' => 'pending',
                    'requires_digital_signature' => $tmpl->requires_digital_signature,
                    'is_mandatory' => $tmpl->is_mandatory,
                ]));
            }

            // Insert any creator-selected tiers that were NOT matched by any template
            foreach ($creatorInputs as $tierRole => $data) {
                if (! isset($handledCreatorTiers[$tierRole])) {
                    $empId = $data['employee_id'] ?? null;
                    $userId = $data['user_id'] ?? ($empId ? User::where('employee_id', $empId)->value('id') : null);

                    $created->push(DocumentSignatory::create([
                        'tenant_id' => $tenantId,
                        'campus_id' => $campusId,
                        'matrix_id' => null,
                        'doc_type_id' => $docTypeId,
                        'document_type' => $document->getMorphClass(),
                        'document_id' => $document->getKey(),
                        'dts_record_id' => $dtsRecordId,
                        'tier_role' => $tierRole,
                        'role_label' => $data['role_label'] ?? (ucfirst($tierRole).' by:'),
                        'assignment_type' => 'creator_selected',
                        'employee_id' => $empId,
                        'user_id' => $userId,
                        'signatory_name' => $data['signatory_name'] ?? null,
                        'designation' => $data['designation'] ?? null,
                        'office' => $data['office'] ?? null,
                        'sort_order' => $data['sort_order'] ?? 1,
                        'position_column' => $data['position_column'] ?? 1,
                        'col_span' => 1,
                        'alignment' => 'left',
                        'status' => 'pending',
                        'requires_digital_signature' => true,
                        'is_mandatory' => true,
                    ]));
                    $handledCreatorTiers[$tierRole] = true;
                }
            }
        } elseif (! empty($creatorInputs)) {
            // Fallback: Matrix template not seeded yet, create directly from creator form inputs!
            foreach ($creatorInputs as $tierRole => $data) {
                $empId = $data['employee_id'] ?? null;
                $userId = $data['user_id'] ?? ($empId ? User::where('employee_id', $empId)->value('id') : null);

                $created->push(DocumentSignatory::create([
                    'tenant_id' => $tenantId,
                    'campus_id' => $campusId,
                    'matrix_id' => null,
                    'doc_type_id' => $docTypeId,
                    'document_type' => $document->getMorphClass(),
                    'document_id' => $document->getKey(),
                    'dts_record_id' => $dtsRecordId,
                    'tier_role' => $tierRole,
                    'role_label' => $data['role_label'] ?? (ucfirst($tierRole).' by:'),
                    'assignment_type' => 'creator_selected',
                    'employee_id' => $empId,
                    'user_id' => $userId,
                    'signatory_name' => $data['signatory_name'] ?? null,
                    'designation' => $data['designation'] ?? null,
                    'office' => $data['office'] ?? null,
                    'sort_order' => $data['sort_order'] ?? 1,
                    'position_column' => $data['position_column'] ?? 1,
                    'col_span' => 1,
                    'alignment' => 'left',
                    'status' => 'pending',
                    'requires_digital_signature' => true,
                    'is_mandatory' => true,
                ]));
            }
        }

        return $created;
    }

    /**
     * Get runtime signatories for a specific document instance.
     */
    public static function getSignatoriesForDocument(Model $document): Collection
    {
        return DocumentSignatory::with(['employee', 'signedByUser'])
            ->where('document_type', $document->getMorphClass())
            ->where('document_id', $document->getKey())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get all documents pending signature for a specific user.
     * Powers the "For Signature" queue and notification badges.
     */
    public static function getPendingForUser(User $user, ?int $tenantId = null): Collection
    {
        $query = DocumentSignatory::with(['document', 'dtsRecord', 'campus'])
            ->pending()
            ->forSignatoryUser($user)
            ->orderBy('created_at', 'desc');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get();
    }
}
