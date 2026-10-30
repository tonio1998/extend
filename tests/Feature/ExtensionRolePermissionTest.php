<?php

namespace Tests\Feature;

use App\Http\Controllers\DSS\RolePermissionController;
use App\Models\RIE\ExtensionPlan;
use App\Models\User;
use App\Policies\ExtensionPlanPolicy;
use Database\Seeders\ExtensionRolesAndPermissionsSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExtensionRolePermissionTest extends TestCase
{
    public function test_roles_and_permissions_are_seeded_and_assigned(): void
    {
        $this->seed(ExtensionRolesAndPermissionsSeeder::class);

        $trackingOfficerRole = Role::where('name', 'Extension Tracking Officer')->first();
        $this->assertNotNull($trackingOfficerRole);
        $this->assertTrue($trackingOfficerRole->hasPermissionTo('rie.tracking.manage'));
        $this->assertTrue($trackingOfficerRole->hasPermissionTo('rie.tracking.view'));
        $this->assertFalse($trackingOfficerRole->hasPermissionTo('rie.budgets.manage'));

        $budgetOfficerRole = Role::where('name', 'Extension Budget Officer')->first();
        $this->assertNotNull($budgetOfficerRole);
        $this->assertTrue($budgetOfficerRole->hasPermissionTo('rie.budgets.manage'));
        $this->assertFalse($budgetOfficerRole->hasPermissionTo('rie.tracking.manage'));

        $coordinatorRole = Role::where('name', 'Extension Coordinator')->first();
        $this->assertNotNull($coordinatorRole);
        $this->assertTrue($coordinatorRole->hasPermissionTo('rie.proposals.review'));
        $this->assertTrue($coordinatorRole->hasPermissionTo('rie.implementation.manage'));
        $this->assertTrue($coordinatorRole->hasPermissionTo('rie.reports.accomplishment'));

        $evaluatorRole = Role::where('name', 'Evaluator')->first();
        $this->assertNotNull($evaluatorRole);
        $this->assertTrue($evaluatorRole->hasPermissionTo('rie.proposals.evaluate'));
        $this->assertTrue($evaluatorRole->hasPermissionTo('rie.impact.evaluate'));
        $this->assertFalse($evaluatorRole->hasPermissionTo('rie.plans.create'));

        $projectLeaderRole = Role::where('name', 'Project Leader')->first();
        $this->assertNotNull($projectLeaderRole);
        $this->assertTrue($projectLeaderRole->hasPermissionTo('rie.proposals.create'));
        $this->assertTrue($projectLeaderRole->hasPermissionTo('rie.plans.create'));
        $this->assertTrue($projectLeaderRole->hasPermissionTo('rie.activities.manage'));
    }

    public function test_permission_catalog_covers_all_seeded_permissions(): void
    {
        $this->seed(ExtensionRolesAndPermissionsSeeder::class);

        $catalog = RolePermissionController::getPermissionCatalog();
        $catalogKeys = [];
        foreach ($catalog as $domainPermissions) {
            foreach (array_keys($domainPermissions) as $permKey) {
                $catalogKeys[] = $permKey;
            }
        }

        $seededPermissionNames = Permission::pluck('name')->all();

        foreach ($seededPermissionNames as $permissionName) {
            $this->assertContains($permissionName, $catalogKeys);
        }
    }

    public function test_user_role_methods_and_effective_dashboard(): void
    {
        $director = new User(['name' => 'Dr. Director', 'role' => 'Director']);
        $this->assertTrue($director->isExtensionDirector());
        $this->assertFalse($director->isTenantAdmin());
        $this->assertSame('director', $director->getEffectiveDashboardRole());

        $dean = new User(['name' => 'Dean User', 'role' => 'Dean']);
        $this->assertTrue($dean->isDean());
        $this->assertSame('dean', $dean->getEffectiveDashboardRole());

        $coord = new User(['name' => 'Coordinator User', 'role' => 'Extension Coordinator']);
        $this->assertTrue($coord->isCollegeHead());
        $this->assertSame('college_head', $coord->getEffectiveDashboardRole());

        $staff = new User(['name' => 'Staff User', 'role' => 'Extension Staff']);
        $this->assertTrue($staff->isExtensionOfficeStaff());
        $this->assertSame('extension_office', $staff->getEffectiveDashboardRole());

        $trackingOfficer = new User(['name' => 'Tracking Officer', 'role' => 'Extension Tracking Officer']);
        $this->assertTrue($trackingOfficer->isExtensionOfficeStaff());
        $this->assertSame('extension_office', $trackingOfficer->getEffectiveDashboardRole());

        $budgetOfficer = new User(['name' => 'Budget Officer', 'role' => 'Extension Budget Officer']);
        $this->assertTrue($budgetOfficer->isExtensionOfficeStaff());
        $this->assertSame('extension_office', $budgetOfficer->getEffectiveDashboardRole());

        $pl = new User(['name' => 'Faculty Proponent', 'role' => 'Project Leader']);
        $this->assertTrue($pl->isProjectLeader());
        $this->assertSame('project_leader', $pl->getEffectiveDashboardRole());
    }

    public function test_extension_plan_policy_respects_roles_and_signatories(): void
    {
        $policy = new ExtensionPlanPolicy;

        $proponent = new User(['name' => 'Proponent', 'role' => 'Project Leader']);
        $proponent->id = 501;

        $otherFaculty = new User(['name' => 'Other Faculty', 'role' => 'Faculty']);
        $otherFaculty->id = 502;
        $otherFaculty->employee_id = 88;

        $admin = new User(['name' => 'Admin', 'role' => 'Admin']);
        $admin->id = 503;

        $draftPlan = new ExtensionPlan([
            'prepared_by_user_id' => 501,
            'status' => 'draft',
            'reviewed_by_employee_id' => 88,
        ]);

        $submittedPlan = new ExtensionPlan([
            'prepared_by_user_id' => 501,
            'status' => 'submitted',
        ]);

        $this->assertTrue($policy->update($proponent, $draftPlan));
        $this->assertFalse($policy->update($proponent, $submittedPlan));
        $this->assertFalse($policy->update($otherFaculty, $draftPlan));
        $this->assertTrue($policy->view($otherFaculty, $draftPlan));

        $this->assertTrue($policy->delete($proponent, $draftPlan));
        $this->assertFalse($policy->delete($proponent, $submittedPlan));
        $this->assertFalse($policy->delete($otherFaculty, $draftPlan));
        $this->assertTrue($policy->delete($admin, $submittedPlan));
    }
}
