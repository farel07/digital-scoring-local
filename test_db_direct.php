<?php

// Simple standalone test - no Laravel framework loading
$pdo = new PDO('mysql:host=127.0.0.1;dbname=digital_scoring', 'root', '');

echo "=== Direct Database Test ===\n\n";

// Delete test record
$pdo->exec("DELETE FROM tunggal_regu_scores WHERE pertandingan_id = 999");

// Test 1: Insert with JSON array
echo "TEST 1: Insert JSON array\n";
$stmt = $pdo->prepare("INSERT INTO tunggal_regu_scores (pertandingan_id, user_id, errors_per_jurus, total_errors, correctness_score, category_score, total_score) VALUES (999, 1, ?, 0, 9.90, 0.00, 9.90)");
$result = $stmt->execute(['[]']); // Insert JSON string
echo "Insert status: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 2: Read it back
echo "TEST 2: Read back\n";
$stmt = $pdo->query("SELECT errors_per_jurus FROM tunggal_regu_scores WHERE pertandingan_id = 999");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Value: " . $row['errors_per_jurus'] . "\n";
echo "Type: " . gettype($row['errors_per_jurus']) . "\n\n";

// Test 3: Update with JSON object
echo "TEST 3: Update with JSON object\n";
$jsonData = json_encode(['1' => 1, '2' => 2]);
echo "Updating with: $jsonData\n";
$stmt = $pdo->prepare("UPDATE tunggal_regu_scores SET errors_per_jurus = ? WHERE pertandingan_id = 999");
$result = $stmt->execute([$jsonData]);
echo "Update status: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 4: Read updated value
echo "TEST 4: Read updated value\n";
$stmt = $pdo->query("SELECT errors_per_jurus FROM tunggal_regu_scores WHERE pertandingan_id = 999");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Value: " . $row['errors_per_jurus'] . "\n";
$decoded = json_decode($row['errors_per_jurus'], true);
echo "Decoded: ";
var_dump($decoded);

// Cleanup
$pdo->exec("DELETE FROM tunggal_regu_scores WHERE pertandingan_id = 999");

echo "\n=== Database Test Complete ===\n";
