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
        Schema::create('assessment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->timestamp('proposed_date');
            $table->tinyInteger('status')->nullable()->comment('null => WAITING, 1 => ACCEPTED, 0 => REJECTED');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_schedules');
    }
};
