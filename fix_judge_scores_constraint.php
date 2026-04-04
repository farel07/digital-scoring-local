<?php
// Direct SQL fix for judge_scores table

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing judge_scores table ===\n\n";

// Step 1: Show current indexes
echo "Step 1: Current indexes on judge_scores table:\n";
$indexes = DB::select("SHOW INDEX FROM judge_scores");
foreach ($indexes as $index) {
    echo "  - {$index->Key_name} (columns: {$index->Column_name}, unique: " . ($index->Non_unique ? 'no' : 'yes') . ")\n";
}
echo "\n";

// Step 2: Check for duplicates
echo "Step 2: Checking for duplicate records...\n";
$duplicates = DB::select("
    SELECT pertandingan_id, user_id, COUNT(*) as count 
    FROM judge_scores 
    GROUP BY pertandingan_id, user_id 
    HAVING count > 1
");
echo "Found " . count($duplicates) . " duplicate combinations\n";
foreach ($duplicates as $dup) {
    echo "  - pertandingan_id={$dup->pertandingan_id}, user_id={$dup->user_id}, count={$dup->count}\n";
}
echo "\n";

// Step 3: Delete duplicates (keep the latest one)
if (count($duplicates) > 0) {
    echo "Step 3: Deleting duplicate records (keeping latest)...\n";
    $deleted = DB::delete("
        DELETE t1 FROM judge_scores t1
        INNER JOIN judge_scores t2 
        WHERE t1.pertandingan_id = t2.pertandingan_id 
        AND t1.user_id = t2.user_id 
        AND t1.id < t2.id
    ");
    echo "Deleted $deleted duplicate records\n\n";
} else {
    echo "Step 3: No duplicates to delete\n\n";
}

// Step 4: Drop old unique constraint
echo "Step 4: Dropping old unique constraint...\n";
try {
    // Find the actual constraint name
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'judge_scores'
        AND CONSTRAINT_TYPE = 'UNIQUE'
    ");

    foreach ($constraints as $constraint) {
        echo "  Found constraint: {$constraint->CONSTRAINT_NAME}\n";
        // Only drop if it's not the new one we're about to create
        if ($constraint->CONSTRAINT_NAME != 'judge_scores_match_user_side_unique') {
            DB::statement("ALTER TABLE judge_scores DROP INDEX `{$constraint->CONSTRAINT_NAME}`");
            echo "  Dropped constraint: {$constraint->CONSTRAINT_NAME}\n";
        }
    }
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Step 5: Create new unique constraint with side
echo "Step 5: Creating new unique constraint with side column...\n";
try {
    DB::statement("
        ALTER TABLE judge_scores 
        ADD UNIQUE INDEX judge_scores_match_user_side_unique (pertandingan_id, user_id, side)
    ");
    echo "  Successfully created new unique constraint!\n";
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Step 6: Verify final state
echo "Step 6: Final indexes on judge_scores table:\n";
$finalIndexes = DB::select("SHOW INDEX FROM judge_scores");
foreach ($finalIndexes as $index) {
    echo "  - {$index->Key_name} (column: {$index->Column_name}, unique: " . ($index->Non_unique ? 'no' : 'yes') . ")\n";
}

echo "\n=== Done! ===\n";
