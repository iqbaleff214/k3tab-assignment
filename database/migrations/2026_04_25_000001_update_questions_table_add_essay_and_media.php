<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type')->default('multiple_choice')->after('title');
            $table->string('question_image')->nullable()->after('question');
            $table->json('choices_images')->nullable()->after('choices');
            $table->json('choices')->nullable()->change();
            $table->unsignedTinyInteger('correct_answer_index')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'question_image', 'choices_images']);
            $table->json('choices')->nullable(false)->change();
            $table->unsignedTinyInteger('correct_answer_index')->default(0)->nullable(false)->change();
        });
    }
};
