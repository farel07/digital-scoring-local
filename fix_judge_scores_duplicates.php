&lt;?php

// Script to fix duplicate judge_scores entries before migration
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking for duplicate judge_scores entries...\n";

// Find duplicate entries
$duplicates = DB::table('judge_scores')
->select('pertandingan_id', 'user_id', DB::raw('COUNT(*) as count'))
->groupBy('pertandingan_id', 'user_id')
->having('count', '>', 1)
->get();

echo "Found " . $duplicates->count() . " duplicate entries\n";

foreach ($duplicates as $dup) {
echo "Processing pertandingan_id={$dup->pertandingan_id}, user_id={$dup->user_id}\n";

// Get all records for this combination
$records = DB::table('judge_scores')
->where('pertandingan_id', $dup->pertandingan_id)
->where('user_id', $dup->user_id)
->orderBy('id', 'asc')
->get();

// Keep the first one, update it with side=1 if null
if ($records->count() > 0) {
$first = $records->first();
if (is_null($first->side)) {
DB::table('judge_scores')
->where('id', $first->id)
->update(['side' => '1']);
echo " - Kept record {$first->id} and set side=1\n";
}

// If there's a second record, update it to side=2 (or delete if side is already set)
if ($records->count() > 1) {
$second = $records->skip(1)->first();

// If the second record doesn't have side set or has same side, update to side=2
if (is_null($second->side) || $second->side == $first->side) {
DB::table('judge_scores')
->where('id', $second->id)
->update(['side' => '2']);
echo " - Kept record {$second->id} and set side=2\n";
}
}

// Delete any additional duplicates (keep only 2: side 1 and side 2)
if ($records->count() > 2) {
$recordsToDelete = $records->skip(2);
foreach ($recordsToDelete as $record) {
DB::table('judge_scores')->where('id', $record->id)->delete();
echo " - Deleted extra record {$record->id}\n";
}
}
}
}

echo "\nDone! You can now run: php artisan migrate\n";