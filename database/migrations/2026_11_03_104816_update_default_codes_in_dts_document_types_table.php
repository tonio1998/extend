<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('dts_document_types')->where('id', 1)->update(['code' => 'SNSU-PLAN-XXXX']);
        DB::table('dts_document_types')->where('id', 2)->update(['code' => 'SNSU-PROP-XXXX']);
        DB::table('dts_document_types')->where('id', 3)->update(['code' => 'SNSU-ACT-XXXX']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('dts_document_types')->where('id', 1)->update(['code' => 'EXT-PLAN']);
        DB::table('dts_document_types')->where('id', 2)->update(['code' => 'EXT-PROP']);
        DB::table('dts_document_types')->where('id', 3)->update(['code' => 'ACT-DES']);
    }
};
