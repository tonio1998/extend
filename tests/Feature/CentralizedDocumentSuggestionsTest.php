<?php

namespace Tests\Feature;

use App\Models\Dts\Unit;
use App\Models\User;
use Tests\TestCase;

class CentralizedDocumentSuggestionsTest extends TestCase
{
    public function test_suggestions_endpoint_returns_matching_units_and_leaders(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.documents.suggestions', ['q' => 'College']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'units' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'campus',
                        'head',
                        'projects_count',
                        'documents_count',
                        'total_budget',
                        'overview_url',
                        'transactions_url',
                    ],
                ],
                'leaders',
                'plans',
                'proposals',
            ]);
    }

    public function test_repository_can_filter_by_unit_id_and_project_leader(): void
    {
        $user = User::first() ?? User::factory()->create();
        $unit = Unit::first();

        $response = $this->actingAs($user)
            ->get(route('dss.documents.repository', [
                'unit_id' => $unit?->id,
                'project_leader' => 'Antonio Jr. L. Piloton',
            ]));

        $response->assertStatus(200)
            ->assertSee('Active Filters');
    }
}
