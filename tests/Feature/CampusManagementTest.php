<?php

namespace Tests\Feature;

use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\User;
use Tests\TestCase;

class CampusManagementTest extends TestCase
{
    public function test_campuses_table_page_renders_successfully(): void
    {
        $superAdmin = User::where('email', 'apiloton1@snsu.edu.ph')->first()
            ?? User::factory()->create(['role' => 'SuperAdmin', 'email' => 'apiloton1@snsu.edu.ph']);

        $res = $this->actingAs($superAdmin)->get(route('dss.settings.institution.campuses'));
        $res->assertStatus(200);
        $res->assertSee('Registered Campuses Registry');
        $res->assertSee('campusesTable');
    }

    public function test_campus_crud_operations_work_properly(): void
    {
        $superAdmin = User::where('email', 'apiloton1@snsu.edu.ph')->first()
            ?? User::factory()->create(['role' => 'SuperAdmin', 'email' => 'apiloton1@snsu.edu.ph']);

        // 1. Create Campus
        $createRes = $this->actingAs($superAdmin)->postJson(route('dss.settings.institution.campus'), [
            'campus_code' => 'TESTCAMPUS',
            'campus_name' => 'Test Innovation Campus',
            'campus_type' => 'Satellite Campus',
            'location' => 'Siargao Island',
            'head_name' => 'Dr. Test Director',
            'contact_no' => '09123456789',
            'status' => 'active',
        ]);
        $createRes->assertStatus(200)->assertJson(['result' => true]);
        $campusId = $createRes->json('campus.id');
        $this->assertDatabaseHas('sys_campuses', ['id' => $campusId, 'campus_code' => 'TESTCAMPUS']);

        // 2. Update Campus
        $updateRes = $this->actingAs($superAdmin)->putJson(route('dss.settings.institution.campus.update', $campusId), [
            'campus_code' => 'TESTCAMPUS_MOD',
            'campus_name' => 'Updated Innovation Campus',
            'campus_type' => 'Extension Unit',
            'location' => 'Dapa, Siargao',
            'head_name' => 'Dr. Updated Director',
            'contact_no' => '09987654321',
            'status' => 'inactive',
        ]);
        $updateRes->assertStatus(200)->assertJson(['result' => true]);
        $this->assertDatabaseHas('sys_campuses', ['id' => $campusId, 'campus_name' => 'Updated Innovation Campus', 'status' => 'inactive']);

        // 3. Delete Campus
        $deleteRes = $this->actingAs($superAdmin)->deleteJson(route('dss.settings.institution.campus.destroy', $campusId));
        $deleteRes->assertStatus(200)->assertJson(['result' => true]);
        $this->assertDatabaseMissing('sys_campuses', ['id' => $campusId]);
    }

    public function test_cannot_delete_campus_with_attached_departments(): void
    {
        $superAdmin = User::where('email', 'apiloton1@snsu.edu.ph')->first()
            ?? User::factory()->create(['role' => 'SuperAdmin', 'email' => 'apiloton1@snsu.edu.ph']);

        $campus = Campus::first();
        if (! $campus) {
            $campus = Campus::create([
                'tenant_id' => $superAdmin->tenant_id ?? 1,
                'campus_code' => 'CAMPUS_WITH_DEPT',
                'campus_name' => 'Campus With Dept',
                'campus_type' => 'Main Campus',
                'status' => 'active',
            ]);
        }

        // Attach department
        $dept = Department::firstOrCreate(
            ['campus_id' => $campus->id, 'unit_code' => 'TEST_DEPT_BLOCK'],
            ['tenant_id' => $campus->tenant_id, 'unit_name' => 'Test Department Block']
        );

        $deleteRes = $this->actingAs($superAdmin)->deleteJson(route('dss.settings.institution.campus.destroy', $campus->id));
        $deleteRes->assertStatus(422)->assertJson(['result' => false]);
        $this->assertDatabaseHas('sys_campuses', ['id' => $campus->id]);
    }
}
