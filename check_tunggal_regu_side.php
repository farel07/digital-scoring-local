<?php

use Illuminate\Support\Facades\DB;

// Check if side column exists in tunggal_regu_scores
$columns = DB::select("SHOW COLUMNS FROM tunggal_regu_scores LIKE 'side'");

if (count($columns) > 0) {
    echo "✅ Kolom 'side' SUDAH ADA di tabel tunggal_regu_scores\n\n";
    print_r($columns);
} else {
    echo "❌ Kolom 'side' TIDAK ADA di tabel tunggal_regu_scores\n";
    echo "\nJalankan query ini untuk menambahkan:\n";
    echo "ALTER TABLE tunggal_regu_scores ADD COLUMN side ENUM('1', '2') NULL AFTER user_id;\n";
}

echo "\n\n=== Struktur lengkap tabel tunggal_regu_scores ===\n";
$allColumns = DB::select("SHOW COLUMNS FROM tunggal_regu_scores");
foreach ($allColumns as $column) {
    echo "- {$column->Field} ({$column->Type}) {$column->Null} {$column->Key}\n";
}
