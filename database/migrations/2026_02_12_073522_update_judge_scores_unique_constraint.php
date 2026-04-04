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
        // Get the actual index name from the database
        $indexes = DB::select("SHOW INDEX FROM judge_scores WHERE Key_name LIKE '%pertandingan%user%' AND Non_unique = 0");

        $uniqueIndexName = null;
        foreach ($indexes as $index) {
            if (stripos($index->Key_name, 'pertandingan') !== false && stripos($index->Key_name, 'user') !== false) {
                $uniqueIndexName = $index->Key_name;
                break;
            }
        }

        // STEP 1: Clean up duplicate records before changing constraint
        // Find and delete duplicates, keeping only the latest one
        DB::statement('
            DELETE t1 FROM judge_scores t1
            INNER JOIN judge_scores t2 
            WHERE t1.pertandingan_id = t2.pertandingan_id 
            AND t1.user_id = t2.user_id 
            AND t1.id < t2.id
        ');

        // STEP 2: Drop the old unique constraint using actual index name  
        if ($uniqueIndexName) {
            Schema::table('judge_scores', function (Blueprint $table) use ($uniqueIndexName) {
                $table->dropIndex($uniqueIndexName);
            });
        } else {
            // Fallback: try to drop by column names
            try {
                Schema::table('judge_scores', function (Blueprint $table) {
                    $table->dropUnique(['pertandingan_id', 'user_id']);
                });
            } catch (\Exception $e) {
                // If it fails, the constraint might not exist or have a different name
                echo "Warning: Could not drop unique constraint: " . $e->getMessage() . "\n";
            }
        }

        // STEP 3: Add new unique constraint that includes side
        Schema::table('judge_scores', function (Blueprint $table) {
            $table->unique(['pertandingan_id', 'user_id', 'side'], 'judge_scores_match_user_side_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judge_scores', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('judge_scores_match_user_side_unique');

            // Restore the old constraint
            $table->unique(['pertandingan_id', 'user_id']);
        });
    }
};
