<?php

namespace Tests\Feature;

use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\User;
use Tests\TestCase;

class DocumentCodeSettingsTest extends TestCase
{
    public function test_can_access_institution_settings_page(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.settings.institution'));

        $response->assertStatus(200);
    }

    public function test_can_access_document_codes_settings_page(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.settings.document_codes'));

        $response->assertStatus(200)
            ->assertSee('Official Document Code Formats')
            ->assertSee('Extension Plan')
            ->assertSee('Extension Proposal')
            ->assertSee('Activity Design');
    }

    public function test_can_update_document_codes_in_dts_document_types(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('dss.settings.document_codes.update'), [
                'codes' => [
                    'plan' => 'SNSU-PLAN-XXXX',
                    'proposal' => 'SNSU-PROP-XXXX',
                    'activity_design' => 'SNSU-ACT-XXXX',
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('dts_document_types', [
            'id' => 1,
            'code' => 'SNSU-PLAN-XXXX',
        ]);

        $this->assertDatabaseHas('dts_document_types', [
            'id' => 2,
            'code' => 'SNSU-PROP-XXXX',
        ]);

        $this->assertDatabaseHas('dts_document_types', [
            'id' => 3,
            'code' => 'SNSU-ACT-XXXX',
        ]);
    }

    public function test_generators_produce_codes_based_on_dts_document_types(): void
    {
        $year = date('Y');

        // 1. Plan: format + current year + series (e.g. SNSU-PLAN-2026-0001)
        $planCode = ExtensionPlan::generateCode(1);
        $this->assertMatchesRegularExpression('/^SNSU-PLAN-'.$year.'-\d{4}$/', $planCode);

        // 2. Proposal: format + current year + series (e.g. SNSU-PROP-2026-0001)
        $proposalCode = ExtensionProposal::generateCode(1);
        $this->assertMatchesRegularExpression('/^SNSU-PROP-'.$year.'-\d{4}$/', $proposalCode);

        // 3. Activity Design: format + current year + series (e.g. SNSU-ACT-2026-0001)
        $adCode = ActivityDesign::generateCode(1);
        $this->assertMatchesRegularExpression('/^SNSU-ACT-'.$year.'-\d{4}$/', $adCode);
    }
}
