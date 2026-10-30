<?php

namespace App\Services\RIE;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExtensionSignatoryResolver
{
    /**
     * Resolve the 4 dynamic signatories for an Extension Plan:
     * 1. Prepared by: Project Leader (current user / faculty proponent)
     * 2. Reviewed by: College Extension Head (based on college / department)
     * 3. Noted by: College Dean / Unit Head (based on college / department)
     * 4. Concurred by: University Extension Specialist V & University Extension Director
     */
    public static function resolveForPlan(?int $academicProgramId = null, ?int $unitId = null, ?User $proponent = null): array
    {
        $proponent = $proponent ?: Auth::user();
        $tenantId = $proponent?->tenant_id ?: 1;

        // 1. Resolve Target College Unit
        $unit = null;
        if ($unitId) {
            $unit = Unit::find($unitId);
        }

        if (! $unit && $academicProgramId) {
            $prog = AcademicProgram::find($academicProgramId);
            if ($prog && $prog->department_id) {
                $unit = Unit::find($prog->department_id);
            }
        }

        if (! $unit && $proponent) {
            $deptId = $proponent->dept_id ?: ($proponent->employee?->department_id ?? null);
            if ($deptId) {
                $unit = Unit::find($deptId);
            }
        }

        // Fallback: Default to CCIS or first active academic unit
        if (! $unit) {
            $unit = Unit::where('tenant_id', $tenantId)
                ->where('unit_code', 'CCIS')
                ->first()
                ?: Unit::where('tenant_id', $tenantId)->where('unit_type', 'Academic')->first()
                ?: Unit::first();
        }

        // 2. Prepared by (Project Leader)
        $prepName = $proponent ? ($proponent->employee?->full_name ?: $proponent->name) : 'Project Leader';
        $prepDesig = $proponent ? ($proponent->designation ?: 'Project Leader') : 'Project Leader';
        $prepUserId = $proponent?->id;
        $prepEmpId = $proponent?->employee_id;

        // 3. Reviewed by (College Extension Head)
        $revName = $unit?->extension_head_name;
        $revDesig = $unit?->extension_head_title ?: 'College Head for Extension';
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

        // 4. Noted by (College Dean / UnitHead)
        $notedName = $unit?->head_name;
        $notedDesig = $unit?->head_title ?: 'College Dean';
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

        // 5. Concurred by 1 (University Extension Specialist V)
        $specEmp = Employee::where('tenant_id', $tenantId)
            ->where('designation', 'like', '%Extension Specialist%')
            ->first()
            ?: Employee::where('email', 'e.rabadan@adssu.edu.ph')->first();

        $concurred1Name = $specEmp ? $specEmp->full_name : 'EVER JOY G. RABADAN, MAEM, MSc.';
        $concurred1Desig = $specEmp ? ($specEmp->designation ?: 'University Extension Specialist V') : 'University Extension Specialist V';
        $concurred1EmpId = $specEmp?->id;

        // 6. Concurred by 2 (University Extension Director)
        $dirEmp = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('designation', 'like', '%Extension Director%')
                    ->orWhere('designation', 'like', '%Director, Extension%');
            })
            ->first()
            ?: Employee::where('email', 'a.plaza@adssu.edu.ph')->first();

        $concurred2Name = $dirEmp ? $dirEmp->full_name : 'ALVIE ELMA C. PLAZA, MA';
        $concurred2Desig = $dirEmp ? ($dirEmp->designation ?: 'University Extension Director') : 'University Extension Director';
        $concurred2EmpId = $dirEmp?->id;

        return [
            'unit' => $unit,
            'college_name' => $unit?->unit_name,
            'college_code' => $unit?->unit_code,
            'prepared' => [
                'role_label' => 'Prepared by:',
                'name' => $prepName,
                'designation' => $prepDesig,
                'user_id' => $prepUserId,
                'employee_id' => $prepEmpId,
            ],
            'reviewed' => [
                'role_label' => 'Reviewed by:',
                'name' => $revName ?: 'College Head for Extension',
                'designation' => $revDesig,
                'user_id' => $revUserId,
                'employee_id' => $revEmpId,
            ],
            'noted' => [
                'role_label' => 'Noted by:',
                'name' => $notedName ?: 'College Dean',
                'designation' => $notedDesig,
                'user_id' => $notedUserId,
                'employee_id' => $notedEmpId,
            ],
            'concurred_1' => [
                'role_label' => 'Concurred by:',
                'name' => $concurred1Name,
                'designation' => $concurred1Desig,
                'employee_id' => $concurred1EmpId,
            ],
            'concurred_2' => [
                'role_label' => 'Concurred by:',
                'name' => $concurred2Name,
                'designation' => $concurred2Desig,
                'employee_id' => $concurred2EmpId,
            ],
        ];
    }
}
