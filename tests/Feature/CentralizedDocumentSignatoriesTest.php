<?php

namespace Tests\Feature;

use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\User;
use Tests\TestCase;

class CentralizedDocumentSignatoriesTest extends TestCase
{
    public function test_can_access_centralized_signatories_modifier_for_activity_design(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first() ?? ActivityDesign::create([
            'tenant_id' => 1,
            'design_code' => ActivityDesign::generateCode(1),
            'title' => 'Sample Community AI Seminar',
            'project_leader_name' => 'Engr. Centralized Leader',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dss.documents.signatories.edit', [
                'type' => 'activity_design',
                'id' => $design->getRouteKey(),
            ]));

        $response->assertStatus(200)
            ->assertSee('Configure Activity Design Signatories')
            ->assertSee('Live Signatory Chain')
            ->assertSee('Sequential Approval Chain')
            ->assertSee('Save Signatories Workflow');
    }

    public function test_can_access_centralized_signatories_modifier_for_extension_proposal(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::first();

        if ($proposal) {
            $response = $this->actingAs($user)
                ->get(route('dss.documents.signatories.edit', [
                    'type' => 'extension_proposal',
                    'id' => $proposal->getRouteKey(),
                ]));

            $response->assertStatus(200)
                ->assertSee('Configure Extension Proposal Signatories')
                ->assertSee('Sequential Approval Chain');
        }
    }

    public function test_can_access_centralized_signatories_modifier_for_extension_plan(): void
    {
        $user = User::first() ?? User::factory()->create();
        $plan = ExtensionPlan::first();

        if ($plan) {
            $response = $this->actingAs($user)
                ->get(route('dss.documents.signatories.edit', [
                    'type' => 'extension_plan',
                    'id' => $plan->getRouteKey(),
                ]));

            $response->assertStatus(200)
                ->assertSee('Configure Extension Plan Signatories');
        }
    }

    public function test_can_update_signatories_workflow_via_centralized_endpoint(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first() ?? ActivityDesign::create([
            'tenant_id' => 1,
            'design_code' => ActivityDesign::generateCode(1),
            'title' => 'Centralized Workflow Test Design',
            'project_leader_name' => 'Dr. Lead Researcher',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('dss.documents.signatories.update', [
                'type' => 'activity_design',
                'id' => $design->getRouteKey(),
            ]), [
                'signatories' => [
                    [
                        'role_label' => 'Prepared by:',
                        'tier_role' => 'prepared',
                        'name' => 'Dr. Lead Researcher',
                        'designation' => 'Lead Proponent',
                        'alignment' => 'left',
                        'position_column' => 1,
                    ],
                    [
                        'role_label' => 'Reviewed by:',
                        'tier_role' => 'reviewed',
                        'name' => 'Prof. Extension Chair',
                        'designation' => 'Chair, Extension Unit',
                        'alignment' => 'left',
                        'position_column' => 2,
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
            'signatory_name' => 'Prof. Extension Chair',
        ]);
    }

    public function test_can_suggest_signatories_via_centralized_endpoint(): void
    {
        $user = User::first() ?? User::factory()->create();
        $design = ActivityDesign::first() ?? ActivityDesign::create([
            'tenant_id' => 1,
            'design_code' => ActivityDesign::generateCode(1),
            'title' => 'Centralized Suggest Test Design',
            'project_leader_name' => 'Sample Proponent',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('dss.documents.signatories.suggest', [
                'type' => 'activity_design',
                'id' => $design->getRouteKey(),
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertIsArray($response->json('signatories'));
    }

    public function test_extension_plan_show_page_contains_signatories_configuration_button(): void
    {
        $user = User::first() ?? User::factory()->create();
        $plan = ExtensionPlan::first();

        if ($plan) {
            $response = $this->actingAs($user)
                ->get(route('dss.rie.plans.show', $plan->getRouteKey()));

            $response->assertStatus(200)
                ->assertSee('Configure Signatories')
                ->assertSee('/dss/documents/extension_plan/')
                ->assertSee('/signatories/edit');
        }
    }

    public function test_extension_proposal_show_page_contains_signatories_configuration_button(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::first();

        if ($proposal) {
            $response = $this->actingAs($user)
                ->get(route('dss.rie.proposals.show', $proposal->getRouteKey()));

            $response->assertStatus(200)
                ->assertSee('Configure Signatories')
                ->assertSee('/dss/documents/extension_proposal/')
                ->assertSee('/signatories/edit');
        }
    }

    public function test_extension_plan_datatables_actions_contains_configure_signatories(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.rie.plans.data'));

        $response->assertStatus(200)
            ->assertSee('Configure Signatories');
    }

    public function test_extension_proposal_datatables_actions_contains_configure_signatories(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.rie.proposals.data'));

        $response->assertStatus(200)
            ->assertSee('Configure Signatories');
    }
}
