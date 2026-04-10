<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$kelasTanding = DB::table('kelas')->where('nama_kelas', 'A = 30kg - 40kg')->first();

$rows = DB::select("
    SELECT p.id, p.status, p.jenis_pertandingan,
           tm.match_status,
           COUNT(pp.id) as jumlah_player
    FROM pertandingan p
    LEFT JOIN tanding_matches tm ON tm.pertandingan_id = p.id
    LEFT JOIN pertandingan_player pp ON pp.pertandingan_id = p.id
    WHERE p.kelas_id = ?
    GROUP BY p.id, p.status, p.jenis_pertandingan, tm.match_status
    ORDER BY p.id
", [$kelasTanding->id]);

echo "\nPertandingan Tanding (kelas: {$kelasTanding->nama_kelas})\n";
echo str_repeat('-', 75) . "\n";
echo sprintf("%-5s %-12s %-15s %-15s %-10s\n", '#ID', 'Jenis', 'Status', 'Tanding Match', 'Players');
echo str_repeat('-', 75) . "\n";
foreach ($rows as $r) {
    echo sprintf("%-5s %-12s %-15s %-15s %-10s\n",
        $r->id,
        $r->jenis_pertandingan,
        $r->status,
        $r->match_status ?? '(belum ada)',
        $r->jumlah_player . ' player(s)'
    );
}
echo str_repeat('-', 75) . "\n";
