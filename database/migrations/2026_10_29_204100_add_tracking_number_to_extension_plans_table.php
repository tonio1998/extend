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
        Schema::table('extension_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('extension_plans', 'tracking_number')) {
                $table->string('tracking_number', 50)->nullable()->after('plan_code');
                $table->index('tracking_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension_plans', function (Blueprint $table) {
            if (Schema::hasColumn('extension_plans', 'tracking_number')) {
                $table->dropIndex(['tracking_number']);
                $table->dropColumn('tracking_number');
            }
        });
    }
};
