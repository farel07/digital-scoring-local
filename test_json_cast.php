<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing JSON Cast Fix ===\n\n";

// Clean up test data first
DB::table('tunggal_regu_scores')->where('pertandingan_id', 3)->delete();
echo "Cleaned up test data\n\n";

// Test 1: Create new record (this was failing before)
echo "TEST 1: Insert New Record\n";
try {
    $score = App\Models\TunggalReguScore::create([
        'pertandingan_id' => 3,
        'user_id' => 1,
        'errors_per_jurus' => [], // Send as array
        'total_errors' => 0,
        'category_score' => 0.00,
    ]);
    echo "✓ SUCCESS: Record created with ID {$score->id}\n";
    echo "  errors_per_jurus type: " . gettype($score->errors_per_jurus) . "\n";
    echo "  errors_per_jurus value: ";
    var_dump($score->errors_per_jurus);
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Read the record back
echo "TEST 2: Read Record Back\n";
$score = App\Models\TunggalReguScore::where('pertandingan_id', 3)->first();
if ($score) {
    echo "✓ Record found\n";
    echo "  errors_per_jurus type: " . gettype($score->errors_per_jurus) . "\n";
    echo "  Is array: " . (is_array($score->errors_per_jurus) ? 'YES' : 'NO') . "\n";
}

echo "\n";

// Test 3: Update with array data (simulate service operation)
echo "TEST 3: Update Record (Simulate addMoveError)\n";
try {
    $errorsPerJurus = $score->errors_per_jurus;

    // Safety guard
    if (!is_array($errorsPerJurus)) {
        $errorsPerJurus = [];
    }

    // Add error to jurus 1
    if (!isset($errorsPerJurus[1])) {
        $errorsPerJurus[1] = 0;
    }
    $errorsPerJurus[1]++;

    // Update
    $score->update([
        'errors_per_jurus' => $errorsPerJurus,
        'total_errors' => array_sum($errorsPerJurus),
    ]);

    echo "✓ SUCCESS: Updated record\n";
    echo "  errors_per_jurus: ";
    var_dump($score->fresh()->errors_per_jurus);
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Check raw database value
echo "TEST 4: Verify Database Storage\n";
$raw = DB::table('tunggal_regu_scores')->where('pertandingan_id', 3)->first();
echo "  Raw DB value: " . $raw->errors_per_jurus . "\n";
echo "  Type: " . gettype($raw->errors_per_jurus) . "\n";

// Cleanup
DB::table('tunggal_regu_scores')->where('pertandingan_id', 3)->delete();

echo "\n=== All Tests Completed ===\n";
