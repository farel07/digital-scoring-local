<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Dropping OLD constraint...\n";

try {
    DB::statement('ALTER TABLE tunggal_regu_scores DROP INDEX tunggal_regu_scores_pertandingan_id_user_id_unique');
    echo "✅ Successfully dropped: tunggal_regu_scores_pertandingan_id_user_id_unique\n\n";
} catch (\Exception $e) {
    echo "⚠️  Error: " . $e->getMessage() . "\n\n";
}

echo "Remaining indexes:\n";
$indexes = DB::select('SHOW INDEX FROM tunggal_regu_scores WHERE Key_name != "PRIMARY"');

$grouped = [];
foreach ($indexes as $index) {
    $grouped[$index->Key_name][] = $index->Column_name;
}

foreach ($grouped as $keyName => $columns) {
    echo "  - {$keyName}: (" . implode(', ', $columns) . ")\n";
}
