<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extension_activity_designs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('campus_id')->nullable()->index();
            $table->unsignedBigInteger('extension_proposal_id')->nullable()->index();
            $table->unsignedBigInteger('extension_plan_id')->nullable()->index();
            $table->unsignedBigInteger('dts_record_id')->nullable()->index();

            $table->string('design_code', 60)->unique();
            $table->string('tracking_number', 60)->nullable()->index();
            $table->string('title', 255);
            $table->string('banner_program', 150)->nullable();
            $table->unsignedBigInteger('academic_program_id')->nullable()->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();

            $table->text('collaborating_agencies')->nullable();
            $table->text('target_beneficiaries')->nullable();
            $table->integer('target_beneficiaries_count')->nullable();

            $table->text('duration_text')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('project_location', 255)->nullable();

            $table->unsignedBigInteger('project_leader_user_id')->nullable()->index();
            $table->unsignedBigInteger('project_leader_employee_id')->nullable()->index();
            $table->string('project_leader_name', 191)->nullable();
            $table->string('project_leader_title', 191)->default('Project Leader');

            $table->longText('context_rationale')->nullable();
            $table->text('general_objective')->nullable();
            $table->json('specific_objectives')->nullable();
            $table->json('program_matrix')->nullable();

            $table->decimal('budget_total', 15, 2)->default(0);
            $table->decimal('budget_snsu_share', 15, 2)->default(0);
            $table->decimal('budget_partner_share', 15, 2)->default(0);
            $table->string('budget_total_display', 100)->nullable();

            $table->string('status', 50)->default('draft')->index();
            $table->text('remarks')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extension_activity_designs');
    }
};
