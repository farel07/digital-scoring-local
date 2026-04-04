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
        Schema::table('penalties', function (Blueprint $table) {
            $table->enum('side', ['1', '2'])->nullable()->after('pertandingan_id')->comment('Side 1 (Blue) or Side 2 (Red)');

            // Add index for faster queries by side
            $table->index(['pertandingan_id', 'side', 'status'], 'idx_pertandingan_side_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            $table->dropIndex('idx_pertandingan_side_status');
            $table->dropColumn('side');
        });
    }
};
