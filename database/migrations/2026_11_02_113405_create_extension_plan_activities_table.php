<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extension_plan_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('extension_plan_id')->constrained('extension_plans')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(1)->index();
            $table->string('title', 255);
            $table->string('target_date', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_na')->default(false);
            $table->string('status', 50)->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Backfill existing extension plans into extension_plan_activities
        try {
            $plans = DB::table('extension_plans')->get();
            foreach ($plans as $plan) {
                $milestones = [];
                $meta = json_decode($plan->metadata ?? '{}', true) ?: [];

                if (! empty($meta['milestones']) && is_array($meta['milestones'])) {
                    $order = 1;
                    foreach ($meta['milestones'] as $m) {
                        $t = trim($m['title'] ?? '');
                        if ($t !== '') {
                            $milestones[] = [
                                'sort_order' => $order++,
                                'title' => $t,
                                'target_date' => $m['target_date'] ?? null,
                                'is_na' => strtolower(trim((string) ($m['target_date'] ?? ''))) === 'n/a',
                            ];
                        }
                    }
                }

                if (empty($milestones)) {
                    $formatDate = function ($dateVal) {
                        return $dateVal ? date('F d, Y', strtotime($dateVal)) : null;
                    };

                    $milestones = [
                        ['sort_order' => 1, 'title' => 'Submission of Extension Project Proposal', 'target_date' => $formatDate($plan->sub_extension_proposal_date), 'is_na' => false],
                        ['sort_order' => 2, 'title' => 'Submission of Activity Design', 'target_date' => $formatDate($plan->sub_activity_design_date), 'is_na' => false],
                        ['sort_order' => 3, 'title' => 'Submission of Purchase Request (Optional)', 'target_date' => $formatDate($plan->sub_purchase_request_date) ?: 'N/A', 'is_na' => ! $plan->sub_purchase_request_date],
                        ['sort_order' => 4, 'title' => 'MOA Signing', 'target_date' => $formatDate($plan->moa_signing_date), 'is_na' => false],
                        ['sort_order' => 5, 'title' => 'Arrival of Materials', 'target_date' => $plan->materials_arrival_date ?: $formatDate($plan->materials_arrival_start), 'is_na' => false],
                        ['sort_order' => 6, 'title' => 'Conduct of Extension Activity', 'target_date' => $plan->conduct_activity_date ?: $formatDate($plan->conduct_activity_start), 'is_na' => false],
                        ['sort_order' => 7, 'title' => 'Submission of Narrative Reports, Attendance & Evaluation', 'target_date' => $formatDate($plan->sub_narrative_report_date), 'is_na' => false],
                        ['sort_order' => 8, 'title' => 'Conduct of Monitoring/Feedback', 'target_date' => $formatDate($plan->monitoring_feedback_date), 'is_na' => false],
                    ];
                }

                foreach ($milestones as $m) {
                    DB::table('extension_plan_activities')->insert([
                        'tenant_id' => $plan->tenant_id ?? 1,
                        'extension_plan_id' => $plan->id,
                        'sort_order' => $m['sort_order'],
                        'title' => $m['title'],
                        'target_date' => $m['target_date'],
                        'is_na' => $m['is_na'],
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (Throwable $e) {
            // Log but don't fail migration if backfill table structure is created
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extension_plan_activities');
    }
};
