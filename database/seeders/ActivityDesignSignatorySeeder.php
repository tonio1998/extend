<?php

namespace Database\Seeders;

use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\SaaS\DocumentSignatoryMatrix;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ActivityDesignSignatorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $campusId = 1;

        // 1. Ensure required departments exist
        $budgetUnit = Unit::firstOrCreate(
            ['tenant_id' => $tenantId, 'short_name' => 'Budget Office'],
            ['unit_name' => 'Budget Management Office', 'unit_type' => 'Financial', 'is_active' => true]
        );

        $accountingUnit = Unit::firstOrCreate(
            ['tenant_id' => $tenantId, 'short_name' => 'Accounting'],
            ['unit_name' => 'Accounting Office', 'unit_type' => 'Financial', 'is_active' => true]
        );

        $vpeUnit = Unit::firstOrCreate(
            ['tenant_id' => $tenantId, 'unit_code' => 'OVPIE'],
            ['unit_name' => 'Office of the Vice President for Research, Innovation, and Extension', 'short_name' => 'OVPRIE', 'unit_type' => 'Executive', 'is_active' => true]
        );

        $vpaUnit = Unit::firstOrCreate(
            ['tenant_id' => $tenantId, 'short_name' => 'OVPAF'],
            ['unit_name' => 'Office of the Vice President for Admin & Finance', 'unit_type' => 'Executive', 'is_active' => true]
        );

        $presUnit = Unit::firstOrCreate(
            ['tenant_id' => $tenantId, 'short_name' => 'OP'],
            ['unit_name' => 'Office of the University President', 'unit_type' => 'Executive', 'is_active' => true]
        );

        // 2. Institutional Signatories
        $officers = [
            [
                'email' => 'rsunico@snsu.edu.ph',
                'first' => 'ROMY JUN A.',
                'last' => 'SUNICO, DTE',
                'designation' => 'Vice President for RIE',
                'department_id' => $vpeUnit->id,
            ],
            [
                'email' => 'm.narciso@snsu.edu.ph',
                'first' => 'MARY ANN M.',
                'last' => 'NARCISO, MBA',
                'designation' => 'Budget Officer III',
                'department_id' => $budgetUnit->id,
            ],
            [
                'email' => 'i.turno@snsu.edu.ph',
                'first' => 'IVY JEAN J.',
                'last' => 'TURNO',
                'designation' => 'Accountant III',
                'department_id' => $accountingUnit->id,
            ],
            [
                'email' => 'f.ecleo@snsu.edu.ph',
                'first' => 'FELICITY MAE M.',
                'last' => 'ECLEO',
                'designation' => 'CAO-Designate',
                'department_id' => $vpaUnit->id,
            ],
            [
                'email' => 'r.plando@snsu.edu.ph',
                'first' => 'ROWENA A.',
                'last' => 'PLANDO, Ph.D.',
                'designation' => 'University President III',
                'department_id' => $presUnit->id,
            ],
        ];

        $employeeMap = [];

        foreach ($officers as $off) {
            $emp = Employee::where('email', $off['email'])->first()
                ?: Employee::where('last_name', 'like', '%'.explode(' ', $off['last'])[0].'%')->first();

            if (! $emp) {
                $emp = new Employee;
                $emp->employee_no = 'EMP-'.strtoupper(Str::random(6));
            }

            $emp->tenant_id = $tenantId;
            $emp->campus_id = $campusId;
            $emp->department_id = $off['department_id'];
            $emp->first_name = $off['first'];
            $emp->last_name = $off['last'];
            $emp->email = $off['email'];
            $emp->designation = $off['designation'];
            $emp->save();

            $employeeMap[$off['designation']] = $emp;

            // Optional user account for digital signing
            $user = User::firstOrNew(['email' => $off['email']]);
            $user->tenant_id = $tenantId;
            $user->campus_id = $campusId;
            $user->dept_id = $off['department_id'];
            $user->employee_id = $emp->id;
            $user->name = "{$off['first']} {$off['last']}";
            $user->designation = $off['designation'];
            if (! $user->exists) {
                $user->password = Hash::make('password123');
            }
            $user->save();
        }

        // Also ensure Extension Director is resolved
        $directorEmp = Employee::where('designation', 'like', '%Extension Director%')->first();

        // 3. Seed sys_document_signatory_matrices for doc_type_id: 3 (Activity Design)
        DocumentSignatoryMatrix::where('doc_type_id', 3)->delete();
        DocumentSignatoryMatrix::where('process_type', 'Activity Design')->delete();

        $matrixRows = [
            [
                'sort_order' => 1,
                'tier_role' => 'prepared',
                'role_label' => 'Prepared by:',
                'position_column' => 1,
                'col_span' => 3,
                'alignment' => 'left',
                'assignment_type' => 'dynamic_author',
                'employee_id' => null,
                'signatory_name' => 'Project Leader',
                'designation' => 'Project Leader',
                'office' => 'Proponent Unit',
            ],
            [
                'sort_order' => 2,
                'tier_role' => 'reviewed',
                'role_label' => 'Reviewed by:',
                'position_column' => 2,
                'col_span' => 3,
                'alignment' => 'left',
                'assignment_type' => 'dynamic_extension_head',
                'employee_id' => null,
                'signatory_name' => 'College Head for Extension',
                'designation' => 'Head, College Extension Unit',
                'office' => 'College Extension Unit',
            ],
            [
                'sort_order' => 3,
                'tier_role' => 'noted',
                'role_label' => 'Noted by:',
                'position_column' => 3,
                'col_span' => 3,
                'alignment' => 'left',
                'assignment_type' => 'dynamic_college_dean',
                'employee_id' => null,
                'signatory_name' => 'College Dean',
                'designation' => 'College Dean',
                'office' => 'Office of the Dean',
            ],
            [
                'sort_order' => 4,
                'tier_role' => 'concurred',
                'role_label' => 'Concurred by:',
                'position_column' => 4,
                'col_span' => 3,
                'alignment' => 'left',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $directorEmp?->id,
                'signatory_name' => $directorEmp?->full_name ?: 'ALVIE ELMA C. PLAZA, MA',
                'designation' => $directorEmp?->designation ?: 'Director, Extension Services',
                'office' => 'University Extension Services Office',
            ],
            [
                'sort_order' => 5,
                'tier_role' => 'recommending_approval',
                'role_label' => 'Recommending Approval:',
                'position_column' => 2,
                'col_span' => 12,
                'alignment' => 'center',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $employeeMap['Vice President for RIE']?->id ?? null,
                'signatory_name' => 'ROMY JUN A. SUNICO, DTE',
                'designation' => 'Vice President for RIE',
                'office' => 'Office of the Vice President for RIE',
            ],
            [
                'sort_order' => 6,
                'tier_role' => 'certified_allotment',
                'role_label' => 'Certified as to availability of allotment:',
                'position_column' => 1,
                'col_span' => 6,
                'alignment' => 'left',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $employeeMap['Budget Officer III']?->id ?? null,
                'signatory_name' => 'MARY ANN M. NARCISO, MBA',
                'designation' => 'Budget Officer III',
                'office' => 'Budget Management Office',
            ],
            [
                'sort_order' => 7,
                'tier_role' => 'certified_funds',
                'role_label' => 'Certified as to availability of funds:',
                'position_column' => 2,
                'col_span' => 6,
                'alignment' => 'right',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $employeeMap['Accountant III']?->id ?? null,
                'signatory_name' => 'IVY JEAN J. TURNO',
                'designation' => 'Accountant III',
                'office' => 'Accounting Office',
            ],
            [
                'sort_order' => 8,
                'tier_role' => 'cao_designate',
                'role_label' => '',
                'position_column' => 2,
                'col_span' => 12,
                'alignment' => 'center',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $employeeMap['CAO-Designate']?->id ?? null,
                'signatory_name' => 'FELICITY MAE M. ECLEO',
                'designation' => 'CAO-Designate',
                'office' => 'Office of the Chief Administrative Officer',
            ],
            [
                'sort_order' => 9,
                'tier_role' => 'approved',
                'role_label' => 'Approved by:',
                'position_column' => 2,
                'col_span' => 12,
                'alignment' => 'center',
                'assignment_type' => 'fixed_employee',
                'employee_id' => $employeeMap['University President III']?->id ?? null,
                'signatory_name' => 'ROWENA A. PLANDO, Ph.D.',
                'designation' => 'University President III',
                'office' => 'Office of the University President',
            ],
        ];

        foreach ($matrixRows as $row) {
            DocumentSignatoryMatrix::create([
                'tenant_id' => $tenantId,
                'campus_id' => null,
                'doc_type_id' => 3,
                'process_type' => 'Activity Design',
                'tier_role' => $row['tier_role'],
                'role_label' => $row['role_label'],
                'assignment_type' => $row['assignment_type'],
                'employee_id' => $row['employee_id'],
                'signatory_name' => $row['signatory_name'],
                'designation' => $row['designation'],
                'office' => $row['office'],
                'sort_order' => $row['sort_order'],
                'position_column' => $row['position_column'],
                'col_span' => $row['col_span'],
                'alignment' => $row['alignment'],
                'is_active' => true,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
            ]);
        }
    }
}
