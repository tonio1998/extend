<?php

namespace Tests\Feature;

use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionPlanActivity;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExtensionPlanMilestoneProgressTest extends TestCase
{
    public function test_milestone_progress_calculation(): void
    {
        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database for testing.');
        }

        // Clean previous activities for predictable test assertions
        ExtensionPlanActivity::where('extension_plan_id', $plan->id)->delete();

        // Create 4 activities: 3 active, 1 N/A
        $act1 = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 1,
            'title' => 'Submission of Proposal',
            'target_date' => 'January 15, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $act2 = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 2,
            'title' => 'Submission of Activity Design',
            'target_date' => 'February 10, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $act3 = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 3,
            'title' => 'Purchase Request (Exempted)',
            'target_date' => 'N/A',
            'is_na' => true,
            'status' => 'pending',
        ]);

        $act4 = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 4,
            'title' => 'Conduct of Extension Activity',
            'target_date' => 'March 01, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $plan->load('activities');

        // Total active: 3 (act1, act2, act4). act3 is exempted/N/A
        $this->assertEquals(3, $plan->active_milestones_count);
        $this->assertEquals(0, $plan->completed_milestones_count);
        $this->assertEquals(0, $plan->progress_percentage);
        $this->assertEquals('Not Started', $plan->progress_status);

        // Complete 1 activity with proof
        $act1->update([
            'file_path' => 'extension_plans/test/proof1.pdf',
            'file_name' => 'Proof_Act1.pdf',
            'status' => 'completed',
        ]);

        $fresh = $plan->fresh();
        $this->assertEquals(1, $fresh->completed_milestones_count);
        $this->assertEquals(33, $fresh->progress_percentage); // 1 / 3 = 33%
        $this->assertEquals('In Progress', $fresh->progress_status);

        // Complete 2nd activity
        $act2->update([
            'file_path' => 'extension_plans/test/proof2.pdf',
            'file_name' => 'Proof_Act2.pdf',
            'status' => 'completed',
        ]);

        $fresh2 = $plan->fresh();
        $this->assertEquals(2, $fresh2->completed_milestones_count);
        $this->assertEquals(67, $fresh2->progress_percentage); // 2 / 3 = 67%

        // Complete 3rd activity (all active completed)
        $act4->update([
            'file_path' => 'extension_plans/test/proof4.pdf',
            'file_name' => 'Proof_Act4.pdf',
            'status' => 'completed',
        ]);

        $fresh3 = $plan->fresh();
        $this->assertEquals(3, $fresh3->completed_milestones_count);
        $this->assertEquals(100, $fresh3->progress_percentage); // 3 / 3 = 100%
        $this->assertEquals('Completed', $fresh3->progress_status);
    }

    public function test_milestone_document_upload_and_delete_endpoints(): void
    {
        Storage::fake('public');

        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found in database.');
        }

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        // Set user as proponent for authorization
        $plan->update(['prepared_by_user_id' => $user->id]);

        $activity = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 1,
            'title' => 'Submission of Activity Design',
            'target_date' => 'February 20, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('Signed_Activity_Design.pdf', 1024, 'application/pdf');

        // Test Upload
        $response = $this->actingAs($user)
            ->postJson(route('dss.rie.plans.activity.upload_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]), [
                'file' => $file,
                'remarks' => 'Wet-signed by Dean and Director',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'activity' => [
                'id' => $activity->id,
                'status' => 'completed',
                'is_completed' => true,
            ],
        ]);

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertNotNull($activity->file_path);
        $this->assertNotNull($activity->uploaded_document_id);
        Storage::disk('public')->assertExists($activity->file_path);

        // Test Delete
        $deleteResponse = $this->actingAs($user)
            ->deleteJson(route('dss.rie.plans.activity.delete_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]));

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);

        $activity->refresh();
        $this->assertEquals('pending', $activity->status);
        $this->assertNull($activity->file_path);
        $this->assertNull($activity->uploaded_document_id);
    }

    public function test_superadmin_and_admin_can_upload_even_if_not_plan_preparer(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'SuperAdmin')->first() ?: User::where('role', 'Admin')->first();
        if (! $admin) {
            $this->markTestSkipped('No admin found.');
        }

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        // Set prepared_by_user_id to a different ID to test non-preparer authorization
        $plan->update(['prepared_by_user_id' => 999999]);

        $activity = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 4,
            'title' => 'Admin Upload Test Stage',
            'target_date' => 'May 20, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('Admin_Endorsement.pdf', 512, 'application/pdf');

        $response = $this->actingAs($admin)
            ->postJson(route('dss.rie.plans.activity.upload_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]), [
                'file' => $file,
                'remarks' => 'Uploaded by SuperAdmin',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
    }

    public function test_google_drive_milestone_evidence_upload(): void
    {
        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found in database.');
        }

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        $plan->update(['prepared_by_user_id' => $user->id]);

        $activity = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 2,
            'title' => 'Conduct of Extension Activity',
            'target_date' => 'March 15, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $gdriveLink = 'https://drive.google.com/file/d/1A2B3C4D5E6F7G8H9I0J/view?usp=sharing';

        // Upload Google Drive Link
        $response = $this->actingAs($user)
            ->postJson(route('dss.rie.plans.activity.upload_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]), [
                'gdrive_url' => $gdriveLink,
                'remarks' => 'Public Google Drive link verified',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'activity' => [
                'id' => $activity->id,
                'status' => 'completed',
                'is_completed' => true,
            ],
        ]);

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertEquals($gdriveLink, $activity->file_path);
        $this->assertNotNull($activity->uploaded_document_id);

        // Check UploadedDocument in repository
        $doc = UploadedDocument::find($activity->uploaded_document_id);
        $this->assertNotNull($doc);
        $this->assertEquals('external', $doc->disk);
        $this->assertEquals($gdriveLink, $doc->file_path);
        $this->assertEquals('wet_signed_milestone', $doc->category);
        $this->assertEquals('Wet-Signed Milestone Evidence', $doc->category_label);
    }

    public function test_centralized_repository_upload_with_activity_linking(): void
    {
        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found in database.');
        }

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        $activity = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 3,
            'title' => 'MOA Signing with Partner LGU',
            'target_date' => 'April 10, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $gdriveUrl = 'https://drive.google.com/file/d/9Z8Y7X6W5V4U3T2S1R/view?usp=sharing';

        // Upload through Centralized Repository route: dss.documents.upload.store
        $response = $this->actingAs($user)
            ->postJson(route('dss.documents.upload.store'), [
                'gdrive_url' => $gdriveUrl,
                'documentable_type' => 'extension_plan_activity',
                'documentable_id' => $activity->id,
                'category' => 'wet_signed_milestone',
                'title' => 'Wet-Signed MOA with LGU',
                'is_physically_signed' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertEquals($gdriveUrl, $activity->file_path);
        $this->assertNotNull($activity->uploaded_document_id);

        // Verify repository page lists the document and renders without errors
        $repoResponse = $this->actingAs($user)
            ->get(route('dss.documents.repository', ['category' => 'wet_signed_milestone']));

        $repoResponse->assertStatus(200);
        $repoResponse->assertSee('Wet-Signed Milestone Evidence');
        $repoResponse->assertSee('Wet-Signed MOA with LGU');
    }

    public function test_centralized_repository_simultaneous_multi_file_and_multi_gdrive_upload(): void
    {
        Storage::fake('public');

        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found.');
        }

        $file1 = UploadedFile::fake()->create('Repo_Document_A.pdf', 350, 'application/pdf');
        $file2 = UploadedFile::fake()->create('Repo_Document_B.pdf', 200, 'application/pdf');
        $gdrive1 = 'https://drive.google.com/file/d/RepoGDriveAlpha123/view?usp=sharing';
        $gdrive2 = 'https://drive.google.com/file/d/RepoGDriveBeta456/view?usp=sharing';

        $response = $this->actingAs($user)
            ->postJson(route('dss.documents.upload.store'), [
                'files' => [$file1, $file2],
                'gdrive_urls' => [$gdrive1, $gdrive2],
                'category' => 'general',
                'title' => 'Simultaneous Repo Assets',
                'is_physically_signed' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 4,
        ]);

        // Verify Repository page renders in both Grid and Table view without errors
        $gridView = $this->actingAs($user)->get(route('dss.documents.repository', ['view' => 'grid']));
        $gridView->assertStatus(200);
        $gridView->assertSee('Institutional Document Repository');
        $gridView->assertSee('DIGITAL ASSET VAULT');

        $tableView = $this->actingAs($user)->get(route('dss.documents.repository', ['view' => 'table']));
        $tableView->assertStatus(200);
        $tableView->assertSee('Document Title &amp; Filename', false);

        // Verify Uploaded Document Preview and Download routes match CentralizedDocumentUploadController (not 404)
        $latestDoc = UploadedDocument::latest('id')->first();
        if ($latestDoc) {
            $previewResponse = $this->actingAs($user)->get(route('dss.documents.upload.preview', $latestDoc->id));
            $this->assertTrue(in_array($previewResponse->status(), [200, 302]));

            $downloadResponse = $this->actingAs($user)->get(route('dss.documents.upload.download', $latestDoc->id));
            $this->assertTrue(in_array($downloadResponse->status(), [200, 302]));
        }
    }

    public function test_proposal_activity_design_view_and_cross_level_milestones(): void
    {
        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found.');
        }

        $proposal = ExtensionProposal::first();
        if (! $proposal) {
            $this->markTestSkipped('No extension proposal found.');
        }

        // Test Activity Design View route
        $response = $this->actingAs($user)
            ->get(route('dss.rie.proposals.activity_design', $proposal->getRouteKey()));

        $response->assertStatus(200);
        $response->assertSee('ACTIVITY DESIGN');
        $response->assertSee('Republic of the Philippines');

        // Test Proposal Workbench with Activities Tab
        $showResponse = $this->actingAs($user)
            ->get(route('dss.rie.proposals.show', [
                'id' => $proposal->getRouteKey(),
                'tab' => 'activities',
            ]));

        $showResponse->assertStatus(200);
        $showResponse->assertSee('Institutional Extension Lifecycle Pipeline');
        $showResponse->assertSee('Stage 1: Extension Plan');
        $showResponse->assertSee('Stage 2: Extension Proposal');
        $showResponse->assertSee('Stage 3: Activity Design');
    }

    public function test_simultaneous_multi_file_and_multi_gdrive_upload_and_individual_delete(): void
    {
        Storage::fake('public');

        $user = User::first();
        if (! $user) {
            $this->markTestSkipped('No user found.');
        }

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        $plan->update(['prepared_by_user_id' => $user->id]);

        $activity = ExtensionPlanActivity::create([
            'extension_plan_id' => $plan->id,
            'sort_order' => 5,
            'title' => 'Simultaneous Multi-Evidence Milestone Stage',
            'target_date' => 'May 20, 2026',
            'is_na' => false,
            'status' => 'pending',
        ]);

        $file1 = UploadedFile::fake()->create('Proof_Document_1.pdf', 500, 'application/pdf');
        $file2 = UploadedFile::fake()->create('Proof_Document_2.pdf', 300, 'application/pdf');
        $gdrive1 = 'https://drive.google.com/file/d/GDriveFileOne1111/view?usp=sharing';
        $gdrive2 = 'https://drive.google.com/file/d/GDriveFileTwo2222/view?usp=sharing';

        // Simultaneous upload: 2 physical files + 2 Google Drive URLs
        $response = $this->actingAs($user)
            ->postJson(route('dss.rie.plans.activity.upload_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]), [
                'files' => [$file1, $file2],
                'gdrive_urls' => [$gdrive1, $gdrive2],
                'remarks' => 'Combined physical scans and Google Drive repository records',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'activity' => [
                'id' => $activity->id,
                'status' => 'completed',
                'is_completed' => true,
                'documents_count' => 4,
            ],
        ]);

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertCount(4, $activity->uploadedDocuments);
        $this->assertCount(4, $activity->documents_list);

        // Delete only the 1st document
        $firstDoc = $activity->uploadedDocuments()->first();
        $this->assertNotNull($firstDoc);

        $deleteSingleResponse = $this->actingAs($user)
            ->deleteJson(route('dss.rie.plans.activity.delete_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
                'docId' => $firstDoc->id,
            ]));

        $deleteSingleResponse->assertStatus(200);
        $deleteSingleResponse->assertJson(['success' => true]);

        $activity->refresh();
        $this->assertCount(3, $activity->uploadedDocuments);
        $this->assertEquals('completed', $activity->status);

        // Remove all remaining documents
        $delAllResponse = $this->actingAs($user)
            ->deleteJson(route('dss.rie.plans.activity.delete_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]));

        $delAllResponse->assertStatus(200);
        $activity->refresh();
        $this->assertCount(0, $activity->uploadedDocuments);
        $this->assertEquals('pending', $activity->status);
    }

    public function test_authenticated_project_leader_and_employee_can_upload_milestone_proof_without_unauthorized_error(): void
    {
        Storage::fake('public');

        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in database.');
        }

        // Find a user who is NOT the creator of this plan
        $otherUser = User::where('id', '!=', $plan->prepared_by_user_id)->first();
        if (! $otherUser) {
            $otherUser = User::first();
        }

        $activity = ExtensionPlanActivity::create([
            'tenant_id' => $plan->tenant_id ?? 1,
            'extension_plan_id' => $plan->id,
            'sort_order' => 99,
            'title' => 'Submission of Activity Design Auth Test',
            'status' => 'pending',
        ]);

        $dummyFile = UploadedFile::fake()->create('Extension-Proposal-V3.pdf', 300, 'application/pdf');

        // Test that an authenticated collaborator can upload without 403 Unauthorized
        $response = $this->actingAs($otherUser)
            ->postJson(route('dss.rie.plans.activity.upload_document', [
                'id' => $plan->getRouteKey(),
                'activityId' => $activity->id,
            ]), [
                'files' => [$dummyFile],
                'remarks' => 'Uploaded by project collaborator',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertEquals($otherUser->id, $activity->uploaded_by_user_id);

        // Verify non-PDF files are strictly rejected (PDF only allowed)
        $pngFile = UploadedFile::fake()->create('Image_Scan.png', 300, 'image/png');
        $nonPdfResponse = $this->actingAs($otherUser)->postJson(route('dss.rie.plans.activity.upload_document', [
            'id' => $plan->getRouteKey(),
            'activityId' => $activity->id,
        ]), [
            'file' => $pngFile,
        ]);
        $nonPdfResponse->assertStatus(422);
        $nonPdfResponse->assertJsonValidationErrors(['file']);
    }
}
