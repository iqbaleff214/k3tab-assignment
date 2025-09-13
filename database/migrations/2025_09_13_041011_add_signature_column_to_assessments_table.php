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
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('assessor_name')->nullable();
            $table->longText('assessor_signature')->nullable();
            $table->timestamp('assessor_signature_date')->nullable();
            $table->longText('assessee_signature')->nullable();
            $table->timestamp('assessee_signature_date')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->longText('supervisor_signature')->nullable();
            $table->timestamp('supervisor_signature_date')->nullable();
            $table->string('data_recorder_name')->nullable();
            $table->longText('data_recorder_signature')->nullable();
            $table->timestamp('data_recorder_signature_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'assessor_signature', 'assessor_signature_date',
                'assessee_signature', 'assessee_signature_date',
                'supervisor_signature', 'supervisor_signature_date',
                'data_recorder_signature', 'data_recorder_signature_date',
                'assessor_name', 'supervisor_name', 'data_recorder_name',
            ]);
        });
    }
};
