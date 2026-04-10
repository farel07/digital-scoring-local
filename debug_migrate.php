<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DEBUG MIGRATE FRESH ===\n\n";

// Step 1: Drop all tables
DB::statement('SET FOREIGN_KEY_CHECKS=0');
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    DB::statement("DROP TABLE IF EXISTS `$tableName`");
    echo "Dropped: $tableName\n";
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "\n";

// Step 2: Run migrations one by one
$migrationDir = __DIR__ . '/database/migrations';
$files = glob($migrationDir . '/*.php');
sort($files);

foreach ($files as $file) {
    $filename = basename($file);
    echo "Running: $filename ... ";
    
    try {
        $migration = require $file;
        $migration->up();
        echo "OK\n";
    } catch (\Exception $e) {
        echo "FAILED!\n";
        echo "  ERROR: " . $e->getMessage() . "\n";
        echo "  FILE: $filename\n";
        break;
    }
}

echo "\n=== DONE ===\n";
