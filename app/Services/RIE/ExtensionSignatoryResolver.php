<?php

namespace App\Services\RIE;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * CANONICAL EXTENSION & RIE DOCUMENT SIGNATORY RESOLVER
 *
 * Universal 4-Tier Document Signatory Resolver across the RIE Suite:
 * - Extension Plans
 * - Extension Proposals
 * - Activity Designs / Sub-Activity Designs
 * - Institutional Endorsement Slips & RIE Memorandums
 *
 * Matrix Structure (per Institutional Governance):
 * 1. Prepared by:  Project Leader (Faculty Proponent / Lead Author)
 * 2. Reviewed by:  College / Campus Extension Head (employees.department_id = units.id)
 * 3. Noted by:     College Dean / Campus Director (employees.department_id = units.id)
 * 4. Concurred by: University Extension Specialist V & University Extension Director
 */
class ExtensionSignatoryResolver
{
    /**
     * Resolve the dynamic 4-column signatories for a project leader, unit, or program.
     *
     * @param  User|Employee|int|null  $projectLeader  User instance, Employee instance, or ID
     * @param  int|null  $unitId  Explicit Unit ID (optional override)
     * @param  int|null  $academicProgramId  Academic Program ID (optional fallback)
     * @return array<string, mixed>
     */
    public static function resolve(
        User|Employee|int|null $projectLeader = null,
        ?int $unitId = null,
        ?int $academicProgramId = null
    ): array {
        // 1. Resolve Project Leader Employee and User instances
        $proponentUser = null;
        $proponentEmp = null;

        if ($projectLeader instanceof Employee) {
            $proponentEmp = $projectLeader;
            $proponentUser = $projectLeader->user ?? User::where('employee_id', $projectLeader->id)->first();
        } elseif ($projectLeader instanceof User) {
            $proponentUser = $projectLeader;
            $proponentEmp = $projectLeader->employee
                ?? ($projectLeader->employee_id ? Employee::find($projectLeader->employee_id) : null);
        } elseif (is_numeric($projectLeader) && $projectLeader > 0) {
            $proponentEmp = Employee::find($projectLeader);
            if ($proponentEmp) {
                $proponentUser = $proponentEmp->user ?? User::where('employee_id', $proponentEmp->id)->first();
            } else {
                $proponentUser = User::find($projectLeader);
                $proponentEmp = $proponentUser?->employee
                    ?? ($proponentUser?->employee_id ? Employee::find($proponentUser->employee_id) : null);
            }
        } else {
            $proponentUser = Auth::user();
            $proponentEmp = $proponentUser?->employee
                ?? ($proponentUser?->employee_id ? Employee::find($proponentUser->employee_id) : null);
        }

        $tenantId = $proponentUser?->tenant_id ?: ($proponentEmp?->tenant_id ?: 1);

        // 2. Resolve Target College / Campus Unit
        // PRIMARY RULE: employees.department_id = units.id (where the Project Leader belongs)
        // If Project Leader is in an admin unit without extension head, check academicProgramId
        $unit = null;

        if ($unitId) {
            $unit = Unit::find($unitId);
        }

        if (! $unit && $proponentEmp && $proponentEmp->department_id) {
            $candidate = Unit::find($proponentEmp->department_id);
            if ($candidate && (! empty($candidate->extension_head_name) || $candidate->unit_type === 'Academic')) {
                $unit = $candidate;
            }
        }

        if (! $unit && $academicProgramId) {
            $prog = AcademicProgram::find($academicProgramId);
            if ($prog && $prog->department_id) {
                $unit = Unit::find($prog->department_id);
            }
        }

        if (! $unit && $proponentEmp && $proponentEmp->department_id) {
            $unit = Unit::find($proponentEmp->department_id);
        }

        if (! $unit && $proponentUser && $proponentUser->dept_id) {
            $unit = Unit::find($proponentUser->dept_id);
        }

        // Fallback: Default to CCIS or first active academic unit
        if (! $unit) {
            $unit = Unit::where('tenant_id', $tenantId)
                ->where('unit_code', 'CCIS')
                ->first()
                ?: Unit::where('tenant_id', $tenantId)->where('unit_type', 'Academic')->first()
                ?: Unit::first();
        }

        // 3. Tier 1: Prepared by (Project Leader / Proponent)
        $prepName = $proponentEmp?->full_name
            ?: ($proponentUser?->name ?: 'Project Leader');
        $prepDesig = $proponentEmp?->designation
            ?: ($proponentUser?->designation ?: 'Project Leader');
        $prepUserId = $proponentUser?->id;
        $prepEmpId = $proponentEmp?->id ?: $proponentUser?->employee_id;

        // 4. Tier 2: Reviewed by (College / Campus Extension Head)
        $revName = $unit?->extension_head_name;
        $revDesig = $unit?->extension_head_title ?: ($unit?->short_name ? $unit->short_name.' Head for Extension' : 'College Head for Extension');
        $revEmpId = $unit?->extension_head_employee_id;
        $revUserId = null;

        if (! $revName && $unit) {
            $extUser = User::role('College Extension Head')
                ->where('dept_id', $unit->id)
                ->first();
            if ($extUser) {
                $revName = $extUser->employee?->full_name ?: $extUser->name;
                $revDesig = $extUser->designation ?: ($unit->short_name.' Head for Extension');
                $revEmpId = $extUser->employee_id;
                $revUserId = $extUser->id;
            }
        }

        if (! $revUserId && $revEmpId) {
            $revUserId = User::where('employee_id', $revEmpId)->value('id');
        }

        // 5. Tier 3: Noted by (College Dean / Campus Director)
        $notedName = $unit?->head_name;
        $notedDesig = $unit?->head_title ?: ($unit?->short_name ? $unit->short_name.' Dean' : 'College Dean');
        $notedEmpId = $unit?->head_employee_id;
        $notedUserId = null;

        if (! $notedName && $unit) {
            $deanUser = User::role('UnitHead')
                ->where('dept_id', $unit->id)
                ->first();
            if ($deanUser) {
                $notedName = $deanUser->employee?->full_name ?: $deanUser->name;
                $notedDesig = $deanUser->designation ?: ($unit->short_name.' Dean');
                $notedEmpId = $deanUser->employee_id;
                $notedUserId = $deanUser->id;
            }
        }

        if (! $notedUserId && $notedEmpId) {
            $notedUserId = User::where('employee_id', $notedEmpId)->value('id');
        }

        // 6. Tier 4: Concurred by (Institutional Extension Officials)
        // Concurred 1: University Extension Specialist V
        $specEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%Extension Specialist%')
                    ->orWhere('last_name', 'like', '%Rabadan%');
            })
            ->first()
            ?: Employee::where('email', 'e.rabadan@adssu.edu.ph')->first();

        $concurred1Name = $specEmp && str_contains($specEmp->full_name, ',') ? $specEmp->full_name : 'EVER JOY G. RABADAN, MAEM, MSc.';
        $concurred1Desig = $specEmp ? ($specEmp->designation ?: 'University Extension Specialist V') : 'University Extension Specialist V';
        $concurred1EmpId = $specEmp?->id ?: 21;
        $concurred1UserId = $specEmp ? User::where('employee_id', $specEmp->id)->value('id') : null;

        // Concurred 2: University Extension Director
        $dirEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%Extension Director%')
                    ->orWhere('designation', 'like', '%Director, Extension%')
                    ->orWhere('last_name', 'like', '%Plaza%');
            })
            ->first()
            ?: Employee::where('email', 'a.plaza@adssu.edu.ph')->first();

        $concurred2Name = $dirEmp && str_contains($dirEmp->full_name, ',') ? $dirEmp->full_name : 'ALVIE ELMA C. PLAZA, MA';
        $concurred2Desig = $dirEmp ? ($dirEmp->designation ?: 'University Extension Director') : 'University Extension Director';
        $concurred2EmpId = $dirEmp?->id ?: 22;
        $concurred2UserId = $dirEmp ? User::where('employee_id', $dirEmp->id)->value('id') : null;

        // 7. Look up matrix configurations to inherit col_span, alignment, and role_label
        $matrixMap = DocumentSignatoryMatrix::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('doc_type_id', 1)
                    ->orWhere('process_type', 'Extension Plan');
            })
            ->orderBy('sort_order', 'asc')
            ->get();

        $m1 = $matrixMap->firstWhere('sort_order', 1) ?? $matrixMap->get(0);
        $m2 = $matrixMap->firstWhere('sort_order', 2) ?? $matrixMap->get(1);
        $m3 = $matrixMap->firstWhere('sort_order', 3) ?? $matrixMap->get(2);
        $m4 = $matrixMap->firstWhere('sort_order', 4) ?? $matrixMap->get(3);
        $m5 = $matrixMap->firstWhere('sort_order', 5) ?? $matrixMap->get(4);

        return [
            'unit' => $unit,
            'unit_id' => $unit?->id,
            'college_name' => $unit?->unit_name,
            'college_code' => $unit?->unit_code,
            'prepared' => [
                'role_label' => $m1?->role_label ?: 'Prepared by:',
                'tier_role' => 'prepared',
                'name' => $prepName,
                'designation' => $prepDesig,
                'user_id' => $prepUserId,
                'employee_id' => $prepEmpId,
                'position_column' => 1,
                'col_span' => $m1?->col_span ?: 12,
                'alignment' => $m1?->alignment ?: 'left',
            ],
            'reviewed' => [
                'role_label' => $m2?->role_label ?: 'Reviewed by:',
                'tier_role' => 'reviewed',
                'name' => $revName ?: 'College Head for Extension',
                'designation' => $revDesig,
                'user_id' => $revUserId,
                'employee_id' => $revEmpId,
                'position_column' => 2,
                'col_span' => $m2?->col_span ?: 6,
                'alignment' => $m2?->alignment ?: 'left',
            ],
            'noted' => [
                'role_label' => $m3?->role_label ?: 'Noted by:',
                'tier_role' => 'noted',
                'name' => $notedName ?: 'College Dean',
                'designation' => $notedDesig,
                'user_id' => $notedUserId,
                'employee_id' => $notedEmpId,
                'position_column' => 3,
                'col_span' => $m3?->col_span ?: 6,
                'alignment' => $m3?->alignment ?: 'left',
            ],
            'concurred_1' => [
                'role_label' => $m4?->role_label ?: 'Concurred by:',
                'tier_role' => 'concurred',
                'name' => $concurred1Name,
                'designation' => $concurred1Desig,
                'user_id' => $concurred1UserId,
                'employee_id' => $concurred1EmpId,
                'position_column' => 4,
                'col_span' => $m4?->col_span ?: 6,
                'alignment' => $m4?->alignment ?: 'left',
            ],
            'concurred_2' => [
                'role_label' => $m5?->role_label ?: 'Concurred by:',
                'tier_role' => 'concurred_2',
                'name' => $concurred2Name,
                'designation' => $concurred2Desig,
                'user_id' => $concurred2UserId,
                'employee_id' => $concurred2EmpId,
                'position_column' => 4,
                'col_span' => $m5?->col_span ?: 6,
                'alignment' => $m5?->alignment ?: 'left',
            ],
        ];
    }

    /**
     * Resolve signatories directly from any document model.
     * Supports: ExtensionPlan, ExtensionProposal, ActivityDesign, or custom models.
     *
     * @return array<string, mixed>
     */
    public static function resolveForDocument(Model $document): array
    {
        $projectLeader = null;
        $unitId = null;
        $programId = null;

        if ($document instanceof ExtensionPlan) {
            $projectLeader = $document->prepared_by_employee_id
                ?: ($document->prepared_by_user_id ?: ($document->preparedByUser ?? null));
            $programId = $document->academic_program_id;
        } elseif ($document instanceof ExtensionProposal) {
            $projectLeader = $document->project_leader_employee_id
                ?: ($document->project_leader_user_id ?: ($document->prepared_by_employee_id ?: null));
            $unitId = $document->unit_id;
            $programId = $document->extension_plan_id ? $document->extensionPlan?->academic_program_id : null;
        } else {
            $projectLeader = $document->project_leader_employee_id
                ?? ($document->prepared_by_employee_id
                ?? ($document->user_id ?? null));
            $unitId = $document->unit_id ?? ($document->department_id ?? null);
        }

        return self::resolve($projectLeader, $unitId, $programId);
    }

    /**
     * Backward-compatible alias for Extension Plans.
     */
    public static function resolveForPlan(?int $academicProgramId = null, ?int $unitId = null, ?User $proponent = null): array
    {
        return self::resolve($proponent, $unitId, $academicProgramId);
    }

    /**
     * Resolve signatories specifically for an Extension Proposal.
     */
    public static function resolveForProposal(ExtensionProposal $proposal): array
    {
        return self::resolveForDocument($proposal);
    }

    /**
     * Snapshot signatories to the dedicated `sys_document_signatories` table.
     * This decouples the document from hardcoded columns and enables adding
     * any number of arbitrary signatories in the future without schema migrations.
     *
     * @param  Model  $document  Any document instance (ExtensionPlan, ExtensionProposal, etc.)
     * @param  array<string, mixed>  $signatories  Optional custom signatories or creator form overrides
     * @return Collection<int, DocumentSignatory>
     */
    public static function snapshotToDocument(Model $document, array $signatories = []): Collection
    {
        if (empty($signatories)) {
            $signatories = self::resolveForDocument($document);
        }

        $tenantId = $document->tenant_id ?? 1;
        $campusId = $document->campus_id ?? null;
        $docMorph = $document->getMorphClass();
        $docId = $document->getKey();
        $dtsRecordId = $document->dts_record_id ?? ($document->dtsRecord?->id ?? null);

        // Normalize structured dictionary into sequential list
        $list = [];
        if (isset($signatories['prepared']) || isset($signatories['reviewed'])) {
            $order = 1;
            foreach (['prepared', 'reviewed', 'noted', 'concurred_1', 'concurred_2'] as $tier) {
                if (! empty($signatories[$tier])) {
                    $item = $signatories[$tier];
                    $col = str_starts_with($tier, 'concurred') ? 4 : $order;
                    $list[] = array_merge($item, [
                        'tier_role' => $item['tier_role'] ?? $tier,
                        'sort_order' => $order++,
                        'position_column' => $item['position_column'] ?? $col,
                        'col_span' => $item['col_span'] ?? ($tier === 'prepared' ? 12 : 6),
                        'alignment' => $item['alignment'] ?? 'left',
                    ]);
                }
            }
        } elseif (is_array($signatories)) {
            $list = $signatories;
        }

        $records = collect();
        foreach ($list as $sig) {
            $tierRole = $sig['tier_role'] ?? 'signatory';
            $empId = $sig['employee_id'] ?? null;
            $userId = $sig['user_id'] ?? ($empId ? User::where('employee_id', $empId)->value('id') : null);

            $record = DocumentSignatory::updateOrCreate(
                [
                    'document_type' => $docMorph,
                    'document_id' => $docId,
                    'tier_role' => $tierRole,
                ],
                [
                    'tenant_id' => $tenantId,
                    'campus_id' => $campusId,
                    'dts_record_id' => $dtsRecordId,
                    'role_label' => $sig['role_label'] ?? ucfirst($tierRole).' by:',
                    'employee_id' => $empId,
                    'user_id' => $userId,
                    'signatory_name' => $sig['name'] ?? ($sig['signatory_name'] ?? null),
                    'designation' => $sig['designation'] ?? null,
                    'office' => $sig['office'] ?? null,
                    'position_column' => $sig['position_column'] ?? 1,
                    'col_span' => $sig['col_span'] ?? ($tierRole === 'prepared' ? 12 : 6),
                    'alignment' => $sig['alignment'] ?? 'left',
                    'sort_order' => $sig['sort_order'] ?? 1,
                    'status' => $sig['status'] ?? 'pending',
                    'requires_digital_signature' => true,
                    'is_mandatory' => true,
                ]
            );

            $records->push($record);
        }

        return $records;
    }

    /**
     * Get a collection of all academic and campus units with their assigned deans/directors and extension heads.
     *
     * @return Collection<int, Unit>
     */
    public static function getAllUnitsWithSignatories(): Collection
    {
        return Unit::where('is_active', true)
            ->where(function ($q) {
                $q->where('unit_type', 'Academic')
                    ->orWhereNotNull('extension_head_name')
                    ->orWhereIn('unit_code', ['CCIS', 'GS', 'CBT', 'CTE', 'CAS', 'COE', 'MAINIT', 'MALIMONO']);
            })
            ->orderBy('unit_name', 'asc')
            ->get();
    }
}
