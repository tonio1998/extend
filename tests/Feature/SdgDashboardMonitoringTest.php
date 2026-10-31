<?php

namespace Tests\Feature;

use App\Http\Controllers\DSS\RIE\ExtensionProposalController;
use App\Models\RIE\ExtensionProposal;
use App\Models\RIE\SustainableDevelopmentGoal;
use App\Models\User;
use Tests\TestCase;

class SdgDashboardMonitoringTest extends TestCase
{
    public function test_dashboard_displays_sdg_monitoring_with_extension_counts(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get(route('dss.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('sdgMonitoring');
        $response->assertViewHas('sdgCoveredCount');
        $response->assertViewHas('sdgUncoveredCount');
        $response->assertViewHas('totalSdgTagsCount');
        $response->assertViewHas('bannerProgramMonitoring');
        $response->assertViewHas('agendaMonitoring');
        $response->assertViewHas('expectedOutputsMonitoring');
        $response->assertViewHas('gadMonitoring');
        $response->assertViewHas('collegesMonitoring');
        $response->assertViewHas('partnerAgenciesMonitoring');
        $response->assertViewHas('fundingStructureMonitoring');
        $response->assertViewHas('availableYears');

        $sdgMonitoring = $response->viewData('sdgMonitoring');
        $this->assertCount(17, $sdgMonitoring);

        foreach (range(1, 17) as $goalNum) {
            $this->assertArrayHasKey($goalNum, $sdgMonitoring);
            $this->assertSame($goalNum, $sdgMonitoring[$goalNum]['goal_number']);
            $this->assertArrayHasKey('extensions_count', $sdgMonitoring[$goalNum]);
            $this->assertArrayHasKey('proposals', $sdgMonitoring[$goalNum]);
        }

        $response->assertSee('UN SDG MONITORING');
        $response->assertSee('Sustainable Development Goals (SDG) Extension Alignment');
        $response->assertSee('Institutional Banner Programs Monitoring');
        $response->assertSee('Extension Agenda Priorities');
        $response->assertSee('Expected Outputs &amp; Deliverables Monitoring', false);
        $response->assertSee('GAD Beneficiaries &amp; Proponents', false);
        $response->assertSee('Implementing Colleges &amp; Units', false);
        $response->assertSee('Collaborating Partner Agencies');
        $response->assertSee('Proposal Funding Structure');
    }

    public function test_dashboard_supports_whole_all_time_and_per_year_filtering(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $allResponse = $this->actingAs($user)->get(route('dss.dashboard', ['period' => 'all']));
        $allResponse->assertStatus(200);
        $this->assertTrue($allResponse->viewData('isAllTime'));
        $this->assertSame('Whole / All-Time', $allResponse->viewData('periodLabel'));

        $yearResponse = $this->actingAs($user)->get(route('dss.dashboard', ['period' => 2026]));
        $yearResponse->assertStatus(200);
        $this->assertFalse($yearResponse->viewData('isAllTime'));
        $this->assertSame('FY 2026', $yearResponse->viewData('periodLabel'));

        $futureYearResponse = $this->actingAs($user)->get(route('dss.dashboard', ['period' => 2030]));
        $futureYearResponse->assertStatus(200);
        $this->assertSame(0, $futureYearResponse->viewData('totalProposals'));
        $this->assertSame(0, $futureYearResponse->viewData('sdgCoveredCount'));
    }

    public function test_sync_proposal_sdgs_handles_direct_and_indirect_alignments(): void
    {
        $proposal = ExtensionProposal::first();
        if (! $proposal || SustainableDevelopmentGoal::count() === 0) {
            $this->markTestSkipped('Seeded proposal or SDGs not present.');
        }

        $originalAlignments = $proposal->sdg_alignments;
        $originalSdgIds = $proposal->sdgs()->pluck('sustainable_development_goals.id')->all();

        try {
            $payload = [
                'selected' => [4, 9],
                'indirect' => [11, 13],
                'narrative' => 'Test alignment narrative',
            ];

            $proposal->sdg_alignments = $payload;
            $proposal->save();

            ExtensionProposalController::syncProposalSdgs($proposal, $payload);
            $proposal->load('sdgs');

            $resolved = $proposal->resolved_sdg_alignments;
            $this->assertEqualsCanonicalizing([4, 9], $resolved['selected']);
            $this->assertEqualsCanonicalizing([11, 13], $resolved['indirect']);
        } finally {
            $proposal->sdg_alignments = $originalAlignments;
            $proposal->save();
            $proposal->sdgs()->sync($originalSdgIds);
        }
    }

    public function test_proposals_datatable_filters_by_sdg_number(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)
            ->getJson(route('dss.rie.proposals.data', ['sdg' => 4]));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    public function test_extension_calendar_page_displays_commencement_and_duration_monitoring(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get(route('dss.rie.calendar.index', ['period' => 'all']));

        $response->assertStatus(200);
        $response->assertViewHas('calendarEvents');
        $response->assertViewHas('durationRoadmapItems');
        $response->assertViewHas('ongoingExtensionsCount');
        $response->assertViewHas('upcomingCommencementsCount');
        $response->assertViewHas('completedExtensionsCount');
        $response->assertViewHas('totalScheduledEventsCount');
        $response->assertViewHas('availableYears');
        $response->assertSee('Extension Calendar');
    }

    public function test_extension_calendar_events_json_endpoint_filters_properly(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->getJson(route('dss.rie.calendar.events', [
            'period' => 'all',
            'category' => 'proposal_duration',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'events',
            'roadmap',
            'stats' => [
                'ongoing',
                'upcoming',
                'completed',
                'total_events',
            ],
        ]);
    }

    public function test_landing_and_authentication_pages_reflect_selected_ui_template_and_theme(): void
    {
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('data-ui-template=', false);
        $landingResponse->assertSee('--dss-primary:', false);

        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('data-ui-template=', false);
        $loginResponse->assertSee('--dss-primary:', false);

        $forgotResponse = $this->get(route('password.request'));
        $forgotResponse->assertStatus(200);
        $forgotResponse->assertSee('data-ui-template=', false);
        $forgotResponse->assertSee('--dss-primary:', false);
    }
}
