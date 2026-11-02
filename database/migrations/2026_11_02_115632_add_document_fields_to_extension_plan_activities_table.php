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
        Schema::table('extension_plan_activities', function (Blueprint $table) {
            $table->string('file_path', 500)->nullable()->after('remarks');
            $table->string('file_name', 255)->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            $table->string('mime_type', 100)->nullable()->after('file_size');
            $table->timestamp('uploaded_at')->nullable()->after('mime_type');
            $table->foreignId('uploaded_by_user_id')->nullable()->after('uploaded_at')->constrained('users')->nullOnDelete();
            $table->foreignId('uploaded_document_id')->nullable()->after('uploaded_by_user_id')->constrained('sys_uploaded_documents')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('uploaded_document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension_plan_activities', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by_user_id']);
            $table->dropForeign(['uploaded_document_id']);
            $table->dropColumn([
                'file_path',
                'file_name',
                'file_size',
                'mime_type',
                'uploaded_at',
                'uploaded_by_user_id',
                'uploaded_document_id',
                'completed_at',
            ]);
        });
    }
};
