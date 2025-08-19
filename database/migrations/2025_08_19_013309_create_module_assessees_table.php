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
        Schema::create('module_assessees', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->jsonb('downloaded_files')->nullable();
            $table->boolean('is_doing_test')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_assessees');
    }
};
