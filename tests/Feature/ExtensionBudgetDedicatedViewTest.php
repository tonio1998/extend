<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExtensionBudgetDedicatedViewTest extends TestCase
{
    public function test_extension_budget_view_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.budgets.index'));

        $response->assertStatus(200);
        $response->assertSee('Annual Extension Budgets');
        $response->assertSee('Overall Burn Rate');
        $response->assertSee('Approved Annual Budget');
        $response->assertSee('Allocations Table');
        $response->assertSee('Campus Distribution');
        $response->assertSee('Fund Clusters Matrix');
        $response->assertSee('viewPaneCampuses');
        $response->assertSee('viewPaneClusters');
        $response->assertSee('realignModal');
    }
}
