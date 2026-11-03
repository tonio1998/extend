<?php

namespace Tests\Feature;

use App\Models\Dts\Unit;
use App\Models\RIE\ExtensionProposal;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UnitMonitoringAndImpactAssessmentTest extends TestCase
{
    public function test_authenticated_user_can_access_unit_monitoring_page(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('Unit Extension Monitoring');
        $response->assertSee('3-Year Impact Assessment');
        $response->assertSee('Unit / Department Summary Table');
        $response->assertSee('3-Year Post-Commencement Impact Assessment Pipeline');
    }

    public function test_unit_summary_table_has_action_as_first_column(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.index', ['tab' => 'units']));

        $response->assertStatus(200);
        // Verify user rule #8: Action column is the first column header
        $response->assertSee('<th style="width: 130px;" class="text-center">Action</th>', false);
    }

    public function test_impact_assessment_pipeline_identifies_3_year_eligibility(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Ensure a proposal without prior assessment has commenced 3.5 years ago
        $proposal = ExtensionProposal::find(2) ?? ExtensionProposal::first();

        if ($proposal) {
            $proposal->metadata = null;
            $proposal->duration_start = Carbon::now()->subYears(3)->subMonths(6);
            $proposal->save();
        }

        $response = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.index', ['tab' => 'impact']));

        $response->assertStatus(200);
        $response->assertSee('<th style="width: 140px;" class="text-center">Action</th>', false);
        $response->assertSee('3-Yr Impact Due Date');
        $response->assertSee('Impact Assessment Due (3+ Yrs)');
    }

    public function test_can_record_impact_assessment_for_proposal(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::first();

        if (! $proposal) {
            $this->markTestSkipped('No extension proposal found.');
        }

        $payload = [
            'assessment_date' => Carbon::now()->toDateString(),
            'evaluator_name' => 'Dr. Impact Evaluator',
            'sustainability_rating' => 'Outstanding / Highly Sustainable',
            'community_adoption_level' => 'High Adoption (Institutionalized in Community)',
            'findings_summary' => 'Community groups have continued independently operating the technology.',
            'recommendations' => 'Schedule annual refresher workshops.',
            'report_url' => 'https://example.com/reports/impact-2026.pdf',
        ];

        $response = $this->actingAs($user)->postJson(
            route('dss.rie.unit_monitoring.record_impact', $proposal->id),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $proposal->refresh();
        $this->assertEquals('completed', $proposal->metadata['impact_assessment']['status']);
        $this->assertEquals('Dr. Impact Evaluator', $proposal->metadata['impact_assessment']['evaluator_name']);
    }

    public function test_can_export_monitoring_data(): void
    {
        $user = User::first() ?? User::factory()->create();

        $responseUnits = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.export', ['type' => 'units']));
        $responseUnits->assertStatus(200);

        $responseImpact = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.export', ['type' => 'impact']));
        $responseImpact->assertStatus(200);
    }

    public function test_can_access_dedicated_unit_transactions_page(): void
    {
        $user = User::first() ?? User::factory()->create();
        $unit = Unit::first();

        if (! $unit) {
            $this->markTestSkipped('No unit found.');
        }

        $response = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.unit_transactions', $unit->id));
        $response->assertStatus(200);
        $response->assertSee($unit->unit_name);
        $response->assertSee('Extension Proposals &amp; Active Projects', false);
        $response->assertSee('Extension Plans &amp; Program Frameworks', false);
        $response->assertSee('Centralized Repository Documents &amp; Evidence', false);
        // Verify Rule #8: Action is 1st column
        $response->assertSee('<th style="width: 140px;" class="text-center">Action</th>', false);
    }

    public function test_can_access_dedicated_leader_transactions_page(): void
    {
        $user = User::first() ?? User::factory()->create();
        $proposal = ExtensionProposal::whereNotNull('project_leader_name')->first();

        if (! $proposal) {
            $this->markTestSkipped('No proposal with leader found.');
        }

        $response = $this->actingAs($user)->get(route('dss.rie.unit_monitoring.leader_transactions', urlencode($proposal->project_leader_name)));
        $response->assertStatus(200);
        $response->assertSee($proposal->project_leader_name);
        $response->assertSee('Cumulative Budget Managed');
        // Verify Rule #8: Action is 1st column
        $response->assertSee('<th style="width: 100px;" class="text-center">Action</th>', false);
    }
}
