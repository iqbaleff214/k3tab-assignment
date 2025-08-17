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
        Schema::table('modules', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('slug');
            $table->text('equipment_required')->nullable()->after('code');
            $table->text('procedure')->nullable()->after('equipment_required');
            $table->text('reference')->nullable()->after('procedure');
            $table->text('performance')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['code', 'equipment_required', 'procedure', 'reference', 'performance']);
        });
    }
};
