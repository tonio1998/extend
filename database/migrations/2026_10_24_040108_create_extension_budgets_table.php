<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extension_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1)->index();
            $table->unsignedBigInteger('campus_id')->nullable()->index();
            $table->unsignedSmallInteger('fiscal_year')->index();
            $table->string('budget_code', 50)->unique();
            $table->string('title');
            $table->string('fund_cluster')->default('01 - Regular Agency Fund (GAA)');
            $table->decimal('total_allocation', 15, 2)->default(0.00);
            $table->decimal('allocated_gaa', 15, 2)->default(0.00);
            $table->decimal('allocated_stf', 15, 2)->default(0.00);
            $table->decimal('allocated_external', 15, 2)->default(0.00);
            $table->decimal('realigned_amount', 15, 2)->default(0.00);
            $table->string('status', 30)->default('active')->index();
            $table->string('board_resolution_no', 100)->nullable();
            $table->string('approved_by', 150)->nullable();
            $table->date('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('extension_budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('extension_budget_id')->index();
            $table->unsignedBigInteger('campus_id')->nullable()->index();
            $table->string('category_name');
            $table->decimal('allocated_amount', 15, 2)->default(0.00);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('extension_budget_id')
                ->references('id')
                ->on('extension_budgets')
                ->onDelete('cascade');
        });

        Schema::create('extension_budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('extension_budget_id')->index();
            $table->string('transaction_type', 40);
            $table->decimal('amount', 15, 2);
            $table->string('reference_no', 120)->nullable();
            $table->text('reason')->nullable();
            $table->string('authorized_by', 150)->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('extension_budget_id')
                ->references('id')
                ->on('extension_budgets')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extension_budget_transactions');
        Schema::dropIfExists('extension_budget_allocations');
        Schema::dropIfExists('extension_budgets');
    }
};
