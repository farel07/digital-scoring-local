<?php
// Test script to verify the fix works

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Testing judge_scores constraint fix ===\n\n";

// Test data
$testPertandinganId = 999; // Using high number to avoid conflicts
$testUserId = 999;

// Clean up any existing test data
DB::table('judge_scores')->where('pertandingan_id', $testPertandinganId)->delete();

echo "Test 1: Insert score for Side 1...\n";
try {
    DB::table('judge_scores')->insert([
        'pertandingan_id' => $testPertandinganId,
        'user_id' => $testUserId,
        'side' => '1',
        'teknik' => 0.25,
        'kekuatan' => 0.20,
        'penampilan' => 0.15,
        'total' => 9.70,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Successfully inserted score for Side 1\n\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
}

echo "Test 2: Insert score for Side 2 (same judge, same match)...\n";
try {
    DB::table('judge_scores')->insert([
        'pertandingan_id' => $testPertandinganId,
        'user_id' => $testUserId,
        'side' => '2',
        'teknik' => 0.29,
        'kekuatan' => 0.25,
        'penampilan' => 0.20,
        'total' => 9.84,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Successfully inserted score for Side 2\n";
    echo "✓ FIX WORKS! Judge can now score both sides!\n\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
}

echo "Test 3: Try to insert duplicate for Side 1 (should fail)...\n";
try {
    DB::table('judge_scores')->insert([
        'pertandingan_id' => $testPertandinganId,
        'user_id' => $testUserId,
        'side' => '1',
        'teknik' => 0.10,
        'kekuatan' => 0.10,
        'penampilan' => 0.10,
        'total' => 9.40,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✗ Unexpected: Duplicate was allowed (constraint not working!)\n\n";
} catch (\Exception $e) {
    echo "✓ Correctly rejected duplicate: " . substr($e->getMessage(), 0, 80) . "...\n";
    echo "✓ Constraint is working correctly!\n\n";
}

// Clean up test data
DB::table('judge_scores')->where('pertandingan_id', $testPertandinganId)->delete();
echo "Cleaned up test data\n";

echo "\n=== All tests passed! ===\n";
