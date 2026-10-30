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
        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'extension_head_employee_id')) {
                $table->unsignedBigInteger('extension_head_employee_id')->nullable()->after('head_title');
            }
            if (! Schema::hasColumn('units', 'extension_head_name')) {
                $table->string('extension_head_name', 191)->nullable()->after('extension_head_employee_id');
            }
            if (! Schema::hasColumn('units', 'extension_head_title')) {
                $table->string('extension_head_title', 191)->nullable()->after('extension_head_name');
            }
        });

        Schema::table('extension_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('extension_plans', 'reviewed_by_user_id')) {
                $table->unsignedBigInteger('reviewed_by_user_id')->nullable()->after('prepared_at');
            }
            if (! Schema::hasColumn('extension_plans', 'reviewed_by_employee_id')) {
                $table->unsignedBigInteger('reviewed_by_employee_id')->nullable()->after('reviewed_by_user_id');
            }
            if (! Schema::hasColumn('extension_plans', 'reviewed_by_name')) {
                $table->string('reviewed_by_name', 191)->nullable()->after('reviewed_by_employee_id');
            }
            if (! Schema::hasColumn('extension_plans', 'reviewed_by_designation')) {
                $table->string('reviewed_by_designation', 191)->nullable()->after('reviewed_by_name');
            }
            if (! Schema::hasColumn('extension_plans', 'reviewed_at')) {
                $table->dateTime('reviewed_at')->nullable()->after('reviewed_by_designation');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_employee_id')) {
                $table->unsignedBigInteger('concurred_by_employee_id')->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_name')) {
                $table->string('concurred_by_name', 191)->nullable()->after('concurred_by_employee_id');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_designation')) {
                $table->string('concurred_by_designation', 191)->nullable()->after('concurred_by_name');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_at')) {
                $table->dateTime('concurred_at')->nullable()->after('concurred_by_designation');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_2_employee_id')) {
                $table->unsignedBigInteger('concurred_by_2_employee_id')->nullable()->after('concurred_at');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_2_name')) {
                $table->string('concurred_by_2_name', 191)->nullable()->after('concurred_by_2_employee_id');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_2_designation')) {
                $table->string('concurred_by_2_designation', 191)->nullable()->after('concurred_by_2_name');
            }
            if (! Schema::hasColumn('extension_plans', 'concurred_by_2_at')) {
                $table->dateTime('concurred_by_2_at')->nullable()->after('concurred_by_2_designation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['extension_head_employee_id', 'extension_head_name', 'extension_head_title']);
        });

        Schema::table('extension_plans', function (Blueprint $table) {
            $table->dropColumn([
                'reviewed_by_user_id',
                'reviewed_by_employee_id',
                'reviewed_by_name',
                'reviewed_by_designation',
                'reviewed_at',
                'concurred_by_employee_id',
                'concurred_by_name',
                'concurred_by_designation',
                'concurred_at',
                'concurred_by_2_employee_id',
                'concurred_by_2_name',
                'concurred_by_2_designation',
                'concurred_by_2_at',
            ]);
        });
    }
};
