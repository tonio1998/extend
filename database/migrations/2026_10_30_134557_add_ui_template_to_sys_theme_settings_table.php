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
        if (! Schema::hasColumn('sys_theme_settings', 'ui_template')) {
            Schema::table('sys_theme_settings', function (Blueprint $table) {
                $table->string('ui_template', 40)->default('flup_minimal')->after('preset_key');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sys_theme_settings', 'ui_template')) {
            Schema::table('sys_theme_settings', function (Blueprint $table) {
                $table->dropColumn('ui_template');
            });
        }
    }
};
