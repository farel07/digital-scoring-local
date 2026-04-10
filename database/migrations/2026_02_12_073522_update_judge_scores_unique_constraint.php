<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // STEP 1: Clean up duplicate records before changing constraint
        // Find and delete duplicates, keeping only the latest one
        DB::statement('
            DELETE t1 FROM judge_scores t1
            INNER JOIN judge_scores t2 
            WHERE t1.pertandingan_id = t2.pertandingan_id 
            AND t1.user_id = t2.user_id 
            AND t1.id < t2.id
        ');

        // STEP 2: Drop ALL existing unique indexes (except PRIMARY) safely
        $indexes = DB::select("SHOW INDEX FROM judge_scores WHERE Non_unique = 0");

        foreach ($indexes as $index) {
            $keyName = $index->Key_name;
            if ($keyName === 'PRIMARY' || $keyName === 'judge_scores_match_user_side_unique') {
                continue;
            }
            try {
                Schema::table('judge_scores', function (Blueprint $table) use ($keyName) {
                    $table->dropIndex($keyName);
                });
            } catch (\Exception $e) {
                // Ignore if already gone
            }
        }

        // STEP 3: Add new unique constraint that includes side (if not already exists)
        $newIndexExists = collect(DB::select("SHOW INDEX FROM judge_scores WHERE Key_name = 'judge_scores_match_user_side_unique'"))->isNotEmpty();

        if (!$newIndexExists) {
            Schema::table('judge_scores', function (Blueprint $table) {
                $table->unique(['pertandingan_id', 'user_id', 'side'], 'judge_scores_match_user_side_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('judge_scores', function (Blueprint $table) {
                $table->dropUnique('judge_scores_match_user_side_unique');
            });
        } catch (\Exception $e) {
            // Ignore if not exists
        }

        try {
            Schema::table('judge_scores', function (Blueprint $table) {
                $table->unique(['pertandingan_id', 'user_id']);
            });
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }
};
