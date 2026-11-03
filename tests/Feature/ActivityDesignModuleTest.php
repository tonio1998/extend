<?php

namespace Tests\Feature;

use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\User;
use Tests\TestCase;

class ActivityDesignModuleTest extends TestCase
{
    public function test_can_access_activity_designs_registry(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.activity_designs.index'));

        $response->assertStatus(200)
            ->assertSee('Activity Designs Registry')
            ->assertSee('MODULE 3');
    }

    public function test_can_access_activity_design_create_page(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.activity_designs.create'));

        $response->assertStatus(200)
            ->assertSee('Create Activity Design');
    }

    public function test_can_create_activity_design_and_view_official_sheet_with_signatories(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::first();

        $response = $this->actingAs($user)
            ->post(route('dss.rie.activity_designs.store'), [
                'extension_proposal_id' => $proposal?->id,
                'title' => 'Test Community Training on AI & Data Literacy',
                'project_leader_name' => 'Engr. Test Leader',
                'project_leader_title' => 'Project Proponent',
                'budget_total' => 50000.00,
                'budget_snsu_share' => 50000.00,
                'budget_partner_share' => 0.00,
            ]);

        $response->assertRedirect();

        $design = ActivityDesign::where('title', 'Test Community Training on AI & Data Literacy')->latest('id')->first();
        $this->assertNotNull($design);

        // View the official sheet
        $viewResponse = $this->actingAs($user)
            ->get(route('dss.rie.activity_designs.show', $design->getRouteKey()));

        $viewResponse->assertStatus(200)
            ->assertSee('ACTIVITY DESIGN')
            ->assertSee('Test Community Training on AI & Data Literacy')
            // Post-concurred signatories verification
            ->assertSee('Recommending Approval:')
            ->assertSee('ROMY JUN A. SUNICO, DTE')
            ->assertSee('Vice President for RIE')
            ->assertSee('Certified as to availability of allotment:')
            ->assertSee('MARY ANN M. NARCISO, MBA')
            ->assertSee('Budget Officer III')
            ->assertSee('Certified as to availability of funds:')
            ->assertSee('IVY JEAN J. TURNO')
            ->assertSee('Accountant III')
            ->assertSee('FELICITY MAE M. ECLEO')
            ->assertSee('CAO-Designate')
            ->assertSee('Approved by:')
            ->assertSee('ROWENA A. PLANDO, Ph.D.')
            ->assertSee('University President');
    }

    public function test_can_access_dedicated_configure_signatories_page(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first() ?? ActivityDesign::create([
            'tenant_id' => 1,
            'design_code' => ActivityDesign::generateCode(1),
            'title' => 'Signatories Test Activity Design',
            'project_leader_name' => 'Sample Leader',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dss.rie.activity_designs.signatories.edit', $design->getRouteKey()));

        $response->assertStatus(200)
            ->assertSee('Configure Activity Design Signatories')
            ->assertSee('Live Signatory Chain')
            ->assertSee('Sequential Approval Chain')
            ->assertSee('Suggest Official Sequence')
            ->assertSee('Save Signatories Workflow');
    }

    public function test_proposal_activity_design_route_redirects_to_dedicated_transaction(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::first();

        if ($proposal) {
            $response = $this->actingAs($user)
                ->get(route('dss.rie.proposals.activity_design', $proposal->getRouteKey()));

            $response->assertRedirect();
            $this->assertDatabaseHas('extension_activity_designs', [
                'extension_proposal_id' => $proposal->id,
            ]);
        }
    }

    public function test_suggested_signatories_returns_full_official_matrix(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first() ?? ActivityDesign::create([
            'tenant_id' => 1,
            'design_code' => ActivityDesign::generateCode(1),
            'title' => 'Sample Activity Design',
            'project_leader_name' => 'Sample Leader',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('dss.rie.activity_designs.signatories.suggest', $design->getRouteKey()));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('signatories');
        $this->assertIsArray($data);

        // Verify the post-concurred entries are present
        $names = collect($data)->pluck('name')->toArray();
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains($n, 'SUNICO')));
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains($n, 'NARCISO')));
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains($n, 'TURNO')));
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains($n, 'ECLEO')));
        $this->assertTrue(collect($names)->contains(fn ($n) => str_contains($n, 'PLANDO')));
    }

    public function test_can_update_dynamic_signatories_via_ajax(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first();

        $response = $this->actingAs($user)
            ->postJson(route('dss.rie.activity_designs.signatories.update', $design->getRouteKey()), [
                'signatories' => [
                    [
                        'role_label' => 'Prepared by:',
                        'tier_role' => 'prepared',
                        'name' => 'Engr. Dynamic Proponent',
                        'designation' => 'Lead Proponent',
                    ],
                    [
                        'role_label' => 'Recommending Approval:',
                        'tier_role' => 'recommending_approval',
                        'name' => 'ROMY JUN A. SUNICO, DTE',
                        'designation' => 'Vice President for RIE',
                    ],
                    [
                        'role_label' => 'Certified as to availability of allotment:',
                        'tier_role' => 'certified_allotment',
                        'name' => 'MARY ANN M. NARCISO, MBA',
                        'designation' => 'Budget Officer III',
                    ],
                    [
                        'role_label' => 'Certified as to availability of funds:',
                        'tier_role' => 'certified_funds',
                        'name' => 'IVY JEAN J. TURNO',
                        'designation' => 'Accountant III',
                    ],
                    [
                        'role_label' => '',
                        'tier_role' => 'cao_designate',
                        'name' => 'FELICITY MAE M. ECLEO',
                        'designation' => 'CAO-Designate',
                    ],
                    [
                        'role_label' => 'Approved by:',
                        'tier_role' => 'approved',
                        'name' => 'ROWENA A. PLANDO, Ph.D.',
                        'designation' => 'University President III',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('sys_document_signatories', [
            'document_type' => $design->getMorphClass(),
            'document_id' => $design->id,
            'signatory_name' => 'IVY JEAN J. TURNO',
            'designation' => 'Accountant III',
        ]);
    }

    public function test_can_generate_activity_design_fpdf_document(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.activity_designs.pdf', $design->getRouteKey()));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_can_sync_activity_design_from_proposal(): void
    {
        $user = User::first() ?? User::factory()->create();
        $plan = ExtensionPlan::first();
        $proposal = ExtensionProposal::create([
            'tenant_id' => 1,
            'extension_plan_id' => $plan?->id ?? 1,
            'proposal_code' => 'SNSU-PROP-TEST-'.uniqid(),
            'title' => 'Community Coastal Ecosystem Assessment',
            'project_leader_name' => 'Dr. Coastal Specialist',
            'status' => 'approved',
        ]);

        // 1-Click store just by sending extension_proposal_id (no manual encoding, no duplicate data)
        $response = $this->actingAs($user)
            ->post(route('dss.rie.activity_designs.store'), [
                'extension_proposal_id' => $proposal->id,
            ]);

        $response->assertRedirect();

        $design = ActivityDesign::where('extension_proposal_id', $proposal->id)->first();
        $this->assertNotNull($design);
        $this->assertEquals($proposal->title, $design->title);

        // Test the sync route
        $syncResponse = $this->actingAs($user)
            ->post(route('dss.rie.activity_designs.sync', $design->getRouteKey()));

        $syncResponse->assertRedirect();
        $syncResponse->assertSessionHas('success');
    }
}
