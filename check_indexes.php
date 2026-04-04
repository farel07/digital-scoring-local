<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking all indexes on tunggal_regu_scores table:\n\n";

$indexes = DB::select('SHOW INDEX FROM tunggal_regu_scores');

foreach ($indexes as $index) {
    echo "Key name: {$index->Key_name}\n";
    echo "  Column: {$index->Column_name}\n";
    echo "  Unique: {$index->Non_unique}\n";
    echo "  Seq: {$index->Seq_in_index}\n\n";
}
