<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking tunggal_regu_scores table ===\n\n";

$records = DB::table('tunggal_regu_scores')->get();

foreach ($records as $record) {
    echo "ID: {$record->id}\n";
    echo "pertandingan_id: {$record->pertandingan_id}\n";
    echo "user_id: {$record->user_id}\n";
    echo "errors_per_jurus (raw): ";
    var_dump($record->errors_per_jurus);
    echo "Type: " . gettype($record->errors_per_jurus) . "\n";
    echo "---\n";
}

echo "\n=== Using Eloquent Model ===\n\n";

$scores = App\Models\TunggalReguScore::all();

foreach ($scores as $score) {
    echo "ID: {$score->id}\n";
    echo "errors_per_jurus (after cast): ";
    var_dump($score->errors_per_jurus);
    echo "Type: " . gettype($score->errors_per_jurus) . "\n";
    echo "---\n";
}
