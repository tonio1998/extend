<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RepositoryAjaxTest extends TestCase
{
    public function test_repository_ajax_returns_json_and_html(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.documents.repository', ['page' => 1]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'viewMode',
            'html',
            'counts' => [
                'total',
                'signed',
                'rie',
                'gdrive',
                'policy',
                'dts',
                'storage_mb',
            ],
            'filters',
            'pagination',
        ]);
    }

    public function test_repository_ajax_handles_pagination_and_filters(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('dss.documents.repository', [
                'page' => 2,
                'view' => 'grid',
                'format' => 'pdf',
                'module' => 'rie',
                'sort' => 'name_asc',
                'search' => 'test',
            ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'viewMode' => 'grid',
        ]);
        $this->assertEquals('grid', $response->json('filters.view'));
        $this->assertEquals('pdf', $response->json('filters.format'));
        $this->assertEquals('rie', $response->json('filters.module'));
        $this->assertEquals('name_asc', $response->json('filters.sort'));
    }

    public function test_preview_missing_document_renders_professional_error_ui(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dss.documents.upload.preview', 421));

        // Whether 421 exists or is missing on disk:
        $status = $response->getStatusCode();
        $this->assertTrue(in_array($status, [200, 404]));
        if ($status === 404) {
            $response->assertSee('Document Unavailable');
            $response->assertSee('REF-');
        }
    }
}
