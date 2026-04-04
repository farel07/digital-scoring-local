<?php
// Mark migration as completed since we ran it manually

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Marking migration as completed...\n";

// Get the latest batch number
$latestBatch = DB::table('migrations')->max('batch') ?? 0;
$nextBatch = $latestBatch + 1;

// Insert the migration record
DB::table('migrations')->insert([
    'migration' => '2026_02_12_073522_update_judge_scores_unique_constraint',
    'batch' => $nextBatch
]);

echo "✓ Migration marked as completed (batch $nextBatch)\n";
