<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExtensionPlansAndProposalsViewTest extends TestCase
{
    public function test_extension_plans_index_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.plans.index'));

        $response->assertStatus(200);
        $response->assertSee('Extension Plans Masterlist');
        $response->assertSee('Gatekeeper Protocol');
        $response->assertSee('plansTable');
    }

    public function test_my_extension_plans_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.plans.my'));

        $response->assertStatus(200);
        $response->assertSee('My Extension Plans');
        $response->assertSee('plansTable');
    }

    public function test_extension_proposals_index_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.proposals.index'));

        $response->assertStatus(200);
        $response->assertSee('Extension Proposals Masterlist');
        $response->assertSee('Gatekeeper Pipeline');
        $response->assertSee('proposalsTable');
    }

    public function test_my_extension_proposals_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.proposals.my'));

        $response->assertStatus(200);
        $response->assertSee('My Extension Proposals');
        $response->assertSee('proposalsTable');
    }

    public function test_extension_proposals_data_returns_actions_column(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.rie.proposals.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
    }
}
