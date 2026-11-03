<?php

namespace Tests\Feature;

use App\Models\RIE\ExtensionAgenda;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\User;
use Tests\TestCase;

class ExtensionPriorityTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('email', 'apiloton1@snsu.edu.ph')->first()
            ?? User::factory()->create(['role' => 'SuperAdmin', 'email' => 'apiloton1@snsu.edu.ph']);
    }

    public function test_priorities_studio_renders_with_compact_kpis_and_involved_counts(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('dss.settings.rie_priorities.index'));

        $response->assertStatus(200);
        $response->assertSee('Extension Agendas &amp; Banner Programs', false);
        $response->assertSee('Involved Extensions');
        $response->assertSee('Active Coverage');
        $response->assertSee('bannerProgramsTable');
        $response->assertSee('agendasTable');
        $response->assertViewHas('bannerProposalCounts');
        $response->assertViewHas('agendaProposalCounts');
    }

    public function test_banner_program_lifecycle_and_status_toggle(): void
    {
        $user = $this->getAdminUser();

        // 1. Store
        $createRes = $this->actingAs($user)->postJson(route('dss.settings.rie_priorities.banner_programs.store'), [
            'code' => 'BP-TEST-INIT',
            'name' => 'Community Technology Test Banner',
            'description' => 'Test banner description for evaluation',
            'sort_order' => 99,
            'is_active' => 1,
        ]);

        $createRes->assertStatus(200);
        $createRes->assertJson(['success' => true]);

        $banner = ExtensionBannerProgram::where('code', 'BP-TEST-INIT')->first();
        $this->assertNotNull($banner);

        // 2. Toggle Status
        $toggleRes = $this->actingAs($user)->postJson(route('dss.settings.rie_priorities.banner_programs.toggle_status', $banner->id));
        $toggleRes->assertStatus(200)->assertJson(['success' => true, 'is_active' => false]);
        $this->assertFalse((bool) $banner->fresh()->is_active);

        // 3. Update
        $updateRes = $this->actingAs($user)->putJson(route('dss.settings.rie_priorities.banner_programs.update', $banner->id), [
            'code' => 'BP-TEST-UPDATED',
            'name' => 'Updated Community Banner',
            'description' => 'Updated description',
            'sort_order' => 100,
            'is_active' => 1,
        ]);
        $updateRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('BP-TEST-UPDATED', $banner->fresh()->code);

        // 4. Delete
        $deleteRes = $this->actingAs($user)->deleteJson(route('dss.settings.rie_priorities.banner_programs.destroy', $banner->id));
        $deleteRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertNull(ExtensionBannerProgram::where('code', 'BP-TEST-UPDATED')->first());
    }

    public function test_extension_agenda_lifecycle_and_status_toggle(): void
    {
        $user = $this->getAdminUser();

        // 1. Store
        $createRes = $this->actingAs($user)->postJson(route('dss.settings.rie_priorities.agendas.store'), [
            'code' => 'EA-TEST-THRUST',
            'name' => 'Smart Marine Ecosystems & Blue Economy',
            'description' => 'Marine conservation test agenda',
            'sort_order' => 88,
            'is_active' => 1,
        ]);

        $createRes->assertStatus(200);
        $createRes->assertJson(['success' => true]);

        $agenda = ExtensionAgenda::where('code', 'EA-TEST-THRUST')->first();
        $this->assertNotNull($agenda);

        // 2. Toggle Status
        $toggleRes = $this->actingAs($user)->postJson(route('dss.settings.rie_priorities.agendas.toggle_status', $agenda->id));
        $toggleRes->assertStatus(200)->assertJson(['success' => true, 'is_active' => false]);
        $this->assertFalse((bool) $agenda->fresh()->is_active);

        // 3. Update
        $updateRes = $this->actingAs($user)->putJson(route('dss.settings.rie_priorities.agendas.update', $agenda->id), [
            'code' => 'EA-TEST-UPDATED',
            'name' => 'Updated Marine Agenda',
            'description' => 'Updated description',
            'sort_order' => 89,
            'is_active' => 1,
        ]);
        $updateRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('EA-TEST-UPDATED', $agenda->fresh()->code);

        // 4. Delete
        $deleteRes = $this->actingAs($user)->deleteJson(route('dss.settings.rie_priorities.agendas.destroy', $agenda->id));
        $deleteRes->assertStatus(200)->assertJson(['success' => true]);
        $this->assertNull(ExtensionAgenda::where('code', 'EA-TEST-UPDATED')->first());
    }
}
