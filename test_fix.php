<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing After Fix ===\n\n";

// Test 1: Check raw database value
echo "TEST 1: Raw Database Value\n";
$raw = DB::table('tunggal_regu_scores')->first();
if ($raw) {
    echo "errors_per_jurus (raw DB): ";
    var_dump($raw->errors_per_jurus);
    echo "Type: " . gettype($raw->errors_per_jurus) . "\n\n";
}

// Test 2: Check Eloquent model casting
echo "TEST 2: Eloquent Model Casting\n";
$score = App\Models\TunggalReguScore::first();
if ($score) {
    echo "errors_per_jurus (after cast): ";
    var_dump($score->errors_per_jurus);
    echo "Type: " . gettype($score->errors_per_jurus) . "\n";
    echo "Is Array: " . (is_array($score->errors_per_jurus) ? 'YES' : 'NO') . "\n\n";
}

// Test 3: Simulate the service operation
echo "TEST 3: Simulating addMoveError Operation\n";
if ($score) {
    $errorsPerJurus = $score->errors_per_jurus;

    // Safety guard from our fix
    if (!is_array($errorsPerJurus)) {
        echo "Not an array! Converting...\n";
        $errorsPerJurus = [];
    }

    echo "Initial errors: ";
    var_dump($errorsPerJurus);

    // Test increment (this is where error occurred)
    $jurusNumber = 1;
    if (!isset($errorsPerJurus[$jurusNumber])) {
        $errorsPerJurus[$jurusNumber] = 0;
    }
    $errorsPerJurus[$jurusNumber]++;

    echo "After increment jurus 1: ";
    var_dump($errorsPerJurus);
    echo "\nSUCCESS: Can increment without error!\n";
}

echo "\n=== All Tests Completed ===\n";
