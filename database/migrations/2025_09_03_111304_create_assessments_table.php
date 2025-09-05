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
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assessee_id')->constrained('users')->cascadeOnDelete();
            $table->string('assessee_name');
            $table->string('assessee_no_id')->nullable();
            $table->string('assessee_school')->nullable();
            $table->foreignUuid('performance_guide_id')->constrained('performance_guides')->cascadeOnDelete();
            $table->jsonb('tasks')->nullable();
            $table->enum('result', \App\Enum\AssessmentResult::values())->nullable()->default(null);
            $table->enum('status', \App\Enum\AssessmentStatus::values())->default(\App\Enum\AssessmentStatus::Pending->value);
            $table->text('comment')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
