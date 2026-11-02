<?php

namespace Database\Seeders;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ExtensionCollegeSignatorySeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;
        $campusId = 1;

        // 1. Ensure Spatie Roles exist
        $roleExtHead = Role::firstOrCreate(['name' => 'College Extension Head', 'guard_name' => 'web']);
        $roleUnitHead = Role::firstOrCreate(['name' => 'UnitHead', 'guard_name' => 'web']);

        // 2. Define the Colleges with their respective Deans and Extension Heads (from official matrix)
        $colleges = [
            [
                'code' => 'CCIS',
                'name' => 'College of Computer and Information Sciences',
                'short' => 'CCIS',
                'dean' => [
                    'first' => 'Virnille',
                    'last' => 'Francisco',
                    'full' => 'VIRNILLE C. FRANCISCO, PhEdD',
                    'title' => 'College of Computer and Information Sciences Dean',
                    'email' => 'v.francisco@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Jessa',
                    'last' => 'Hambre',
                    'full' => 'JESSA G. HAMBRE',
                    'title' => 'College of Computer and Information Sciences Head for Extension',
                    'email' => 'j.hambre@adssu.edu.ph',
                ],
                'programs' => ['BSIT', 'BSCS', 'BSIS', 'MIT', 'MSIT', 'Bachelor of Science in Information Technology', 'Bachelor of Science in Computer Science'],
            ],
            [
                'code' => 'GS',
                'name' => 'Graduate School',
                'short' => 'GS',
                'dean' => [
                    'first' => 'Merlyn',
                    'last' => 'Estoque',
                    'full' => 'MERLYN L. ESTOQUE, PhD',
                    'title' => 'Graduate School Dean',
                    'email' => 'm.estoque@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Louida',
                    'last' => 'Patac',
                    'full' => 'LOUIDA P. PATAC, PhD',
                    'title' => 'Graduate School Head for Extension',
                    'email' => 'l.patac@adssu.edu.ph',
                ],
                'programs' => ['Master', 'Doctor', 'PhD', 'Graduate'],
            ],
            [
                'code' => 'CBT',
                'name' => 'College of Business and Technology',
                'short' => 'CBT',
                'dean' => [
                    'first' => 'Marilou',
                    'last' => 'Carnicer',
                    'full' => 'MARILOU B. CARNICER, PhD',
                    'title' => 'College of Business and Technology Dean',
                    'email' => 'm.carnicer@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Kenneth Charls',
                    'last' => 'Ferol',
                    'full' => 'MR. KENNETH CHARLS E. FEROL',
                    'title' => 'College of Business and Technology Head for Extension',
                    'email' => 'k.ferol@adssu.edu.ph',
                ],
                'programs' => ['Business', 'Technology', 'Accountancy', 'Hospitality', 'Tourism', 'BSBA', 'BSA'],
            ],
            [
                'code' => 'CTE',
                'name' => 'College of Teacher Education',
                'short' => 'CTE',
                'dean' => [
                    'first' => 'Elvis',
                    'last' => 'Patulin',
                    'full' => 'ELVIS P. PATULIN, PhD, EdD',
                    'title' => 'College of Teacher Education Dean',
                    'email' => 'e.patulin@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Marijul',
                    'last' => 'Jovero',
                    'full' => 'MARIJUL B. JOVERO',
                    'title' => 'College of Teacher Education Head for Extension',
                    'email' => 'm.jovero@adssu.edu.ph',
                ],
                'programs' => ['Education', 'BSEd', 'BEEd', 'Teacher'],
            ],
            [
                'code' => 'CAS',
                'name' => 'College of Arts and Sciences',
                'short' => 'CAS',
                'dean' => [
                    'first' => 'Louida',
                    'last' => 'Patac',
                    'full' => 'LOUIDA P. PATAC, PhD',
                    'title' => 'College of Arts and Sciences Dean',
                    'email' => 'l.patac.cas@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Luis',
                    'last' => 'Cuabo',
                    'full' => 'LUIS E. CUABO',
                    'title' => 'College of Arts and Sciences Head for Extension',
                    'email' => 'l.cuabo@adssu.edu.ph',
                ],
                'programs' => ['Arts', 'Sciences', 'AB', 'Biology', 'Mathematics'],
            ],
            [
                'code' => 'COE',
                'name' => 'College of Engineering',
                'short' => 'COE',
                'dean' => [
                    'first' => 'Catherine',
                    'last' => 'Verallo',
                    'full' => 'ENGR. CATHERINE M. VERALLO, MScPe, PCpE',
                    'title' => 'College of Engineering Dean',
                    'email' => 'c.verallo@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Rosanne',
                    'last' => 'Andaluz',
                    'full' => 'ENGR. ROSANNE ANDALUZ, PhD.',
                    'title' => 'College of Engineering Head for Extension',
                    'email' => 'r.andaluz@adssu.edu.ph',
                ],
                'programs' => ['Engineering', 'Civil', 'Electrical', 'Mechanical', 'Computer Engineering', 'BSCE', 'BSEE', 'BSME', 'BSCPE'],
            ],
            [
                'code' => 'MAINIT',
                'name' => 'Mainit Campus',
                'short' => 'Mainit',
                'dean' => [
                    'first' => 'Juliet Ruth',
                    'last' => 'Mozar',
                    'full' => 'JULIET RUTH D. MOZAR, EdD',
                    'title' => 'Director, Mainit Campus',
                    'email' => 'j.mozar@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Jan Darvine',
                    'last' => 'Ojeda',
                    'full' => 'MR. JAN DARVINE C. OJEDA',
                    'title' => 'Mainit Campus Head for Extension',
                    'email' => 'j.ojeda@adssu.edu.ph',
                ],
                'programs' => ['Mainit', 'Agriculture', 'Agro-Forestry', 'Forestry'],
            ],
            [
                'code' => 'MALIMONO',
                'name' => 'Malimono Campus',
                'short' => 'Malimono',
                'dean' => [
                    'first' => 'Jerry',
                    'last' => 'Acero',
                    'full' => 'JERRY B. ACERO, DiSDS',
                    'title' => 'Director, Malimono Campus',
                    'email' => 'j.acero@adssu.edu.ph',
                ],
                'extension_head' => [
                    'first' => 'Criselle',
                    'last' => 'Sarcol-Sabillo',
                    'full' => 'CRISELLE SARCOL-SABILLO, MSc.',
                    'title' => 'Malimono Campus Head for Extension',
                    'email' => 'c.sabillo@adssu.edu.ph',
                ],
                'programs' => ['Malimono', 'Fisheries', 'Marine Biology'],
            ],
        ];

        // 3. Process each College Unit and its Officers
        foreach ($colleges as $cData) {
            $unit = Unit::firstOrNew([
                'tenant_id' => $tenantId,
                'unit_code' => $cData['code'],
            ]);

            $unit->campus_id = $campusId;
            $unit->unit_name = $cData['name'];
            $unit->short_name = $cData['short'];
            $unit->unit_type = 'Academic';
            $unit->is_active = true;
            $unit->save();

            // A. Dean / UnitHead
            $deanInfo = $cData['dean'];
            $deanEmp = Employee::firstOrNew(['email' => $deanInfo['email']]);
            $deanEmp->tenant_id = $tenantId;
            $deanEmp->campus_id = $campusId;
            $deanEmp->department_id = $unit->id;
            $deanEmp->first_name = $deanInfo['first'];
            $deanEmp->last_name = $deanInfo['last'];
            $deanEmp->employee_no = $deanEmp->employee_no ?: 'EMP-'.strtoupper(Str::random(5));
            $deanEmp->designation = $deanInfo['title'];
            $deanEmp->save();

            $deanUser = User::firstOrNew(['email' => $deanInfo['email']]);
            $deanUser->tenant_id = $tenantId;
            $deanUser->campus_id = $campusId;
            $deanUser->dept_id = $unit->id;
            $deanUser->employee_id = $deanEmp->id;
            $deanUser->name = $deanInfo['full'];
            $deanUser->designation = $deanInfo['title'];
            if (! $deanUser->exists) {
                $deanUser->password = Hash::make('password123');
            }
            $deanUser->save();
            if (! $deanUser->hasRole('UnitHead')) {
                $deanUser->assignRole($roleUnitHead);
            }

            // B. College Extension Head
            $extInfo = $cData['extension_head'];
            $extEmp = Employee::firstOrNew(['email' => $extInfo['email']]);
            $extEmp->tenant_id = $tenantId;
            $extEmp->campus_id = $campusId;
            $extEmp->department_id = $unit->id;
            $extEmp->first_name = $extInfo['first'];
            $extEmp->last_name = $extInfo['last'];
            $extEmp->employee_no = $extEmp->employee_no ?: 'EMP-'.strtoupper(Str::random(5));
            $extEmp->designation = $extInfo['title'];
            $extEmp->save();

            $extUser = User::firstOrNew(['email' => $extInfo['email']]);
            $extUser->tenant_id = $tenantId;
            $extUser->campus_id = $campusId;
            $extUser->dept_id = $unit->id;
            $extUser->employee_id = $extEmp->id;
            $extUser->name = $extInfo['full'];
            $extUser->designation = $extInfo['title'];
            if (! $extUser->exists) {
                $extUser->password = Hash::make('password123');
            }
            $extUser->save();
            if (! $extUser->hasRole('College Extension Head')) {
                $extUser->assignRole($roleExtHead);
            }

            // C. Link both officers directly onto the Unit record
            $unit->head_employee_id = $deanEmp->id;
            $unit->head_name = $deanInfo['full'];
            $unit->head_title = $deanInfo['title'];

            $unit->extension_head_employee_id = $extEmp->id;
            $unit->extension_head_name = $extInfo['full'];
            $unit->extension_head_title = $extInfo['title'];
            $unit->save();

            // D. Associate academic programs to this unit
            foreach ($cData['programs'] as $pKeyword) {
                AcademicProgram::where('tenant_id', $tenantId)
                    ->where(function ($q) use ($pKeyword) {
                        $q->where('program_code', 'like', "%{$pKeyword}%")
                            ->orWhere('program_name', 'like', "%{$pKeyword}%");
                    })
                    ->whereNull('department_id')
                    ->update(['department_id' => $unit->id]);
            }
        }

        // 4. Institutional Concurred Signatories (Extension Services Office)
        $extOffice = Unit::firstOrNew([
            'tenant_id' => $tenantId,
            'unit_code' => 'UESO',
        ]);
        $extOffice->campus_id = $campusId;
        $extOffice->unit_name = 'University Extension Services Office';
        $extOffice->short_name = 'UESO';
        $extOffice->unit_type = 'Administrative';
        $extOffice->is_active = true;
        $extOffice->save();

        // Ever Joy G. Rabadan (University Extension Specialist V)
        $rabadanEmp = Employee::firstOrNew(['email' => 'e.rabadan@adssu.edu.ph']);
        $rabadanEmp->tenant_id = $tenantId;
        $rabadanEmp->campus_id = $campusId;
        $rabadanEmp->department_id = $extOffice->id;
        $rabadanEmp->first_name = 'Ever Joy';
        $rabadanEmp->last_name = 'Rabadan';
        $rabadanEmp->employee_no = $rabadanEmp->employee_no ?: 'EMP-'.strtoupper(Str::random(5));
        $rabadanEmp->designation = 'University Extension Specialist V';
        $rabadanEmp->save();

        // Alvie Elma C. Plaza (University Extension Director)
        $plazaEmp = Employee::firstOrNew(['email' => 'a.plaza@adssu.edu.ph']);
        $plazaEmp->tenant_id = $tenantId;
        $plazaEmp->campus_id = $campusId;
        $plazaEmp->department_id = $extOffice->id;
        $plazaEmp->first_name = 'Alvie Elma';
        $plazaEmp->last_name = 'Plaza';
        $plazaEmp->employee_no = $plazaEmp->employee_no ?: 'EMP-'.strtoupper(Str::random(5));
        $plazaEmp->designation = 'University Extension Director';
        $plazaEmp->save();

        $extOffice->head_employee_id = $plazaEmp->id;
        $extOffice->head_name = 'ALVIE ELMA C. PLAZA, MA';
        $extOffice->head_title = 'University Extension Director';
        $extOffice->save();

        // 5. Update existing Extension Plan 1 to demonstrate the dynamic assignment
        $plan = ExtensionPlan::find(1);
        if ($plan) {
            $ccisUnit = Unit::where('unit_code', 'CCIS')->first();
            if ($ccisUnit) {
                $plan->reviewed_by_employee_id = $ccisUnit->extension_head_employee_id;
                $plan->reviewed_by_name = $ccisUnit->extension_head_name;
                $plan->reviewed_by_designation = $ccisUnit->extension_head_title;

                $plan->noted_by_employee_id = $ccisUnit->head_employee_id;
                $plan->noted_by_name = $ccisUnit->head_name;
                $plan->noted_by_designation = $ccisUnit->head_title;

                $plan->concurred_by_employee_id = $rabadanEmp->id;
                $plan->concurred_by_name = 'EVER JOY G. RABADAN, MAEM, MSc.';
                $plan->concurred_by_designation = 'University Extension Specialist V';

                $plan->concurred_by_2_employee_id = $plazaEmp->id;
                $plan->concurred_by_2_name = 'ALVIE ELMA C. PLAZA, MA';
                $plan->concurred_by_2_designation = 'University Extension Director';
                $plan->save();
            }
        }
    }
}
