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
        Schema::table('tunggal_regu_scores', function (Blueprint $table) {
            // Add side column if it doesn't exist
            if (!Schema::hasColumn('tunggal_regu_scores', 'side')) {
                $table->enum('side', ['1', '2'])->default('1')->after('user_id');
            }
        });

        // Update unique constraint to include 'side'
        // First, drop existing unique index if exists (usually matches table_column_unique)
        try {
            Schema::table('tunggal_regu_scores', function (Blueprint $table) {
                $table->dropUnique(['pertandingan_id', 'user_id']); // Default unique name
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist or name is different
        }

        // Add new unique index including side
        Schema::table('tunggal_regu_scores', function (Blueprint $table) {
            $table->unique(['pertandingan_id', 'user_id', 'side'], 'unique_score_per_side');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunggal_regu_scores', function (Blueprint $table) {
            $table->dropUnique('unique_score_per_side');
            $table->unique(['pertandingan_id', 'user_id']); // Restore old constraint
        });
    }
};
