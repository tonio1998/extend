<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExtensionCalendarDedicatedViewTest extends TestCase
{
    public function test_extension_calendar_dedicated_ui_renders_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.rie.calendar.index'));

        $response->assertStatus(200);
        $response->assertSee('cal-crm-shell');
        $response->assertSee('cal-topbar');
        $response->assertSee('cal-sidebar');
        $response->assertSee('cal-canvas');
        $response->assertSee('cal-mini-picker');
        $response->assertSee('TIMELINE FOOTPRINT');
        $response->assertSee('Extension Calendar');
    }
}
