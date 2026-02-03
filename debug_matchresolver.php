<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debugging MatchResolver ===\n\n";

// Check user 5
echo "USER 5:\n";
$user5 = App\Models\User::with('arenas')->find(5);
if ($user5) {
    echo "  User exists: {$user5->name}\n";
    echo "  Arenas assigned: " . $user5->arenas->count() . "\n";
    foreach ($user5->arenas as $arena) {
        echo "    - Arena ID: {$arena->id}\n";
    }
    $arenaIds = $user5->arenas->pluck('id')->toArray();
    echo "  Arena IDs array: " . json_encode($arenaIds) . "\n";
} else {
    echo "  User 5 not found!\n";
}

echo "\n";

// Check user 6
echo "USER 6:\n";
$user6 = App\Models\User::with('arenas')->find(6);
if ($user6) {
    echo "  User exists: {$user6->name}\n";
    echo "  Arenas assigned: " . $user6->arenas->count() . "\n";
    foreach ($user6->arenas as $arena) {
        echo "    - Arena ID: {$arena->id}\n";
    }
    $arenaIds = $user6->arenas->pluck('id')->toArray();
    echo "  Arena IDs array: " . json_encode($arenaIds) . "\n";
} else {
    echo "  User 6 not found!\n";
}

echo "\n";

// Check all pertandingan with status berlangsung
echo "PERTANDINGAN (status=berlangsung):\n";
$matches = App\Models\Pertandingan::where('status', 'berlangsung')->get();
foreach ($matches as $match) {
    echo "  ID: {$match->id}, Arena: {$match->arena_id}, Status: {$match->status}\n";
}

echo "\n";

// Test MatchResolver for user 5
echo "MATCHRESOLVER TEST - User 5:\n";
$match5 = App\Helpers\MatchResolver::getActiveMatchForUser(5);
if ($match5) {
    echo "  Found match ID: {$match5->id}\n";
    echo "  Arena ID: {$match5->arena_id}\n";
    echo "  Status: {$match5->status}\n";
} else {
    echo "  No active match found!\n";
}

echo "\n";

// Test MatchResolver for user 6  
echo "MATCHRESOLVER TEST - User 6:\n";
$match6 = App\Helpers\MatchResolver::getActiveMatchForUser(6);
if ($match6) {
    echo "  Found match ID: {$match6->id}\n";
    echo "  Arena ID: {$match6->arena_id}\n";
    echo "  Status: {$match6->status}\n";
} else {
    echo "  No active match found!\n";
}

echo "\n=== Debug Complete ===\n";
