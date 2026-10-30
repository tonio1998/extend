<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ExtensionRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.create',
            'rie.plans.manage',
            'rie.plans.delete',
            'rie.proposals.view',
            'rie.proposals.create',
            'rie.proposals.manage',
            'rie.proposals.review',
            'rie.proposals.evaluate',
            'rie.signatures.sign',
            'rie.approvals.manage',
            'rie.implementation.manage',
            'rie.activities.manage',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.reports.accomplishment',
            'rie.impact.evaluate',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.reports.analytics',
            'rie.budgets.view',
            'rie.budgets.manage',
            'rie.tracking.view',
            'rie.tracking.manage',
            'rie.settings.manage',
            'system.users.assign',
            'system.roles.manage',
            'system.hierarchy.manage',
            'system.theme.customize',
            'system.audit.view',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Remove obsolete non-extension permissions and stray roles
        Permission::whereNotIn('name', $permissions)->delete();
        Role::whereIn('name', ['BACMember', 'new'])->delete();

        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $directorPermissions = [
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.manage',
            'rie.proposals.view',
            'rie.proposals.manage',
            'rie.proposals.review',
            'rie.signatures.sign',
            'rie.approvals.manage',
            'rie.reports.accomplishment',
            'rie.impact.evaluate',
            'rie.reports.analytics',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.tracking.view',
            'rie.tracking.manage',
            'rie.settings.manage',
            'rie.budgets.view',
            'rie.budgets.manage',
        ];

        $directorRole = Role::firstOrCreate(['name' => 'Director', 'guard_name' => 'web']);
        $directorRole->syncPermissions($directorPermissions);

        $presidentRole = Role::firstOrCreate(['name' => 'President', 'guard_name' => 'web']);
        $presidentRole->syncPermissions($directorPermissions);

        $coordinatorPermissions = [
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.manage',
            'rie.proposals.view',
            'rie.proposals.review',
            'rie.proposals.evaluate',
            'rie.signatures.sign',
            'rie.implementation.manage',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.reports.accomplishment',
            'rie.impact.evaluate',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.tracking.view',
        ];

        $extensionCoordinator = Role::firstOrCreate(['name' => 'Extension Coordinator', 'guard_name' => 'web']);
        $extensionCoordinator->syncPermissions($coordinatorPermissions);

        $collegeHead = Role::firstOrCreate(['name' => 'College Extension Head', 'guard_name' => 'web']);
        $collegeHead->syncPermissions($coordinatorPermissions);

        $generalStaff = Role::firstOrCreate(['name' => 'Extension Staff', 'guard_name' => 'web']);
        $generalStaff->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.manage',
            'rie.proposals.view',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.activities.manage',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.reports.analytics',
            'rie.tracking.view',
            'rie.tracking.manage',
        ]);

        $trackingOfficer = Role::firstOrCreate(['name' => 'Extension Tracking Officer', 'guard_name' => 'web']);
        $trackingOfficer->syncPermissions([
            'dashboard.view',
            'rie.tracking.view',
            'rie.tracking.manage',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.documents.view',
            'rie.documents.manage',
        ]);

        $budgetOfficer = Role::firstOrCreate(['name' => 'Extension Budget Officer', 'guard_name' => 'web']);
        $budgetOfficer->syncPermissions([
            'dashboard.view',
            'rie.budgets.view',
            'rie.budgets.manage',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.documents.view',
            'rie.documents.manage',
        ]);

        $meOfficer = Role::firstOrCreate(['name' => 'Extension M&E Officer', 'guard_name' => 'web']);
        $meOfficer->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.manage',
            'rie.proposals.view',
            'rie.proposals.review',
            'rie.impact.evaluate',
            'rie.reports.accomplishment',
            'rie.documents.view',
            'rie.documents.manage',
        ]);

        $docCustodian = Role::firstOrCreate(['name' => 'Extension Document Custodian', 'guard_name' => 'web']);
        $docCustodian->syncPermissions([
            'dashboard.view',
            'rie.documents.view',
            'rie.documents.manage',
            'rie.tracking.view',
            'rie.plans.view',
            'rie.proposals.view',
        ]);

        $evaluatorRole = Role::firstOrCreate(['name' => 'Evaluator', 'guard_name' => 'web']);
        $evaluatorRole->syncPermissions([
            'dashboard.view',
            'rie.proposals.view',
            'rie.proposals.review',
            'rie.proposals.evaluate',
            'rie.impact.evaluate',
            'rie.documents.view',
        ]);

        $projectLeader = Role::firstOrCreate(['name' => 'Project Leader', 'guard_name' => 'web']);
        $projectLeader->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.create',
            'rie.proposals.view',
            'rie.proposals.create',
            'rie.implementation.manage',
            'rie.activities.manage',
            'rie.beneficiaries.manage',
            'rie.partners.manage',
            'rie.reports.accomplishment',
            'rie.signatures.sign',
            'rie.documents.view',
            'rie.documents.manage',
        ]);

        $dean = Role::firstOrCreate(['name' => 'Dean', 'guard_name' => 'web']);
        $dean->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.signatures.sign',
            'rie.approvals.manage',
            'rie.documents.view',
            'rie.reports.analytics',
        ]);

        $unitHead = Role::firstOrCreate(['name' => 'UnitHead', 'guard_name' => 'web']);
        $unitHead->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.signatures.sign',
            'rie.approvals.manage',
            'rie.documents.view',
            'rie.reports.analytics',
        ]);

        $auditorRole = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);
        $auditorRole->syncPermissions([
            'dashboard.view',
            'system.audit.view',
            'rie.reports.analytics',
            'rie.documents.view',
        ]);

        $faculty = Role::firstOrCreate(['name' => 'Faculty', 'guard_name' => 'web']);
        $faculty->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.plans.create',
            'rie.proposals.view',
            'rie.proposals.create',
            'rie.signatures.sign',
            'rie.documents.view',
            'rie.documents.manage',
        ]);

        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employee->syncPermissions([
            'dashboard.view',
            'rie.plans.view',
            'rie.proposals.view',
            'rie.documents.view',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
