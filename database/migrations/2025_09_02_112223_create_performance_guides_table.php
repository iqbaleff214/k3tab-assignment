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
        Schema::create('performance_guides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('skill_number')->unique();
            $table->string('title');
            $table->longText('performance_task')->nullable();
            $table->jsonb('tasks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_guides');
    }
};
