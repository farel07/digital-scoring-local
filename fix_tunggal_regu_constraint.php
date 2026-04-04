<?php

/**
 * Fix Tunggal/Regu Scores Unique Constraint
 * 
 * This script updates the unique constraint on tunggal_regu_scores table
 * from (pertandingan_id, user_id) to (pertandingan_id, user_id, side)
 * 
 * Run this directly: php fix_tunggal_regu_constraint.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "🔍 Checking tunggal_regu_scores table...\n";

    // 1. Clean up potential duplicates by keeping only side='1' for duplicate (pertandingan_id, user_id)
    echo "🧹 Cleaning up duplicates...\n";

    // Find duplicates
    $duplicates = DB::select("
        SELECT pertandingan_id, user_id, COUNT(*) as count
        FROM tunggal_regu_scores
        GROUP BY pertandingan_id, user_id
        HAVING count > 1
    ");

    if (count($duplicates) > 0) {
        echo "   Found " . count($duplicates) . " duplicate groups\n";

        foreach ($duplicates as $dup) {
            // Keep the first record (side='1'), delete others
            $ids = DB::table('tunggal_regu_scores')
                ->where('pertandingan_id', $dup->pertandingan_id)
                ->where('user_id', $dup->user_id)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Delete all except the first one
            if (count($ids) > 1) {
                $toDelete = array_slice($ids, 1);
                DB::table('tunggal_regu_scores')
                    ->whereIn('id', $toDelete)
                    ->delete();
                echo "   Deleted " . count($toDelete) . " duplicate records for pertandingan {$dup->pertandingan_id}, user {$dup->user_id}\n";
            }
        }
    } else {
        echo "   ✅ No duplicates found\n";
    }

    // 2. Drop old unique constraint
    echo "\n🔧 Dropping old unique constraint...\n";

    // Get constraint name
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'tunggal_regu_scores'
        AND CONSTRAINT_TYPE = 'UNIQUE'
        AND CONSTRAINT_NAME LIKE '%pertandingan_id%'
    ");

    if (count($constraints) > 0) {
        foreach ($constraints as $constraint) {
            // Check if this constraint includes 'side' already
            $columns = DB::select("
                SELECT COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'tunggal_regu_scores'
                AND CONSTRAINT_NAME = ?
            ", [$constraint->CONSTRAINT_NAME]);

            $columnNames = array_map(fn($c) => $c->COLUMN_NAME, $columns);

            // If constraint already includes side, skip
            if (in_array('side', $columnNames)) {
                echo "   ℹ️  Constraint {$constraint->CONSTRAINT_NAME} already includes 'side' column - skipping\n";
                continue;
            }

            // Drop old constraint
            DB::statement("ALTER TABLE tunggal_regu_scores DROP INDEX {$constraint->CONSTRAINT_NAME}");
            echo "   ✅ Dropped constraint: {$constraint->CONSTRAINT_NAME}\n";
        }
    } else {
        echo "   ℹ️  No unique constraint found\n";
    }

    // 3. Create new unique constraint with side
    echo "\n🔨 Creating new unique constraint (pertandingan_id, user_id, side)...\n";

    // Check if new constraint already exists
    $newConstraints = DB::select("
        SELECT CONSTRAINT_NAME
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'tunggal_regu_scores'
        AND CONSTRAINT_TYPE = 'UNIQUE'
    ");

    $hasCorrectConstraint = false;
    foreach ($newConstraints as $constraint) {
        $columns = DB::select("
            SELECT COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'tunggal_regu_scores'
            AND CONSTRAINT_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$constraint->CONSTRAINT_NAME]);

        $columnNames = array_map(fn($c) => $c->COLUMN_NAME, $columns);

        if ($columnNames === ['pertandingan_id', 'user_id', 'side']) {
            $hasCorrectConstraint = true;
            echo "   ✅ Correct constraint already exists: {$constraint->CONSTRAINT_NAME}\n";
            break;
        }
    }

    if (!$hasCorrectConstraint) {
        DB::statement("
            ALTER TABLE tunggal_regu_scores
            ADD UNIQUE KEY unique_tunggal_regu_judge_side (pertandingan_id, user_id, side)
        ");
        echo "   ✅ Created new unique constraint: unique_tunggal_regu_judge_side\n";
    }

    echo "\n✅ SUCCESS! Tunggal/Regu constraint fix completed!\n";
    echo "   Judges can now submit scores for both Side 1 and Side 2\n\n";
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
