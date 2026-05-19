<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NomorPartaiSeeder extends Seeder
{
    /**
     * Isi kolom nomor_partai untuk semua pertandingan yang sudah ada,
     * diurutkan per-kelas (kelas_id) berdasarkan ID pertandingan.
     *
     * Logika:
     * - Ambil semua pertandingan, kelompokkan per kelas_id
     * - Urutkan berdasarkan id ASC dalam setiap kelompok kelas
     * - Assign nomor_partai = 1, 2, 3, ... untuk setiap kelas
     */
    public function run(): void
    {
        $this->command->info('⏳ Mengisi nomor_partai untuk pertandingan yang sudah ada...');

        // Ambil semua pertandingan, kelompokkan per kelas_id, urut berdasarkan id
        $pertandinganPerKelas = DB::table('pertandingan')
            ->orderBy('kelas_id')
            ->orderBy('id')
            ->get(['id', 'kelas_id', 'nomor_partai'])
            ->groupBy('kelas_id');

        $totalUpdated = 0;

        foreach ($pertandinganPerKelas as $kelasId => $pertandinganList) {
            $nomorPartai = 1;

            foreach ($pertandinganList as $pertandingan) {
                DB::table('pertandingan')
                    ->where('id', $pertandingan->id)
                    ->update(['nomor_partai' => $nomorPartai]);

                $nomorPartai++;
                $totalUpdated++;
            }

            $namaKelas = DB::table('kelas')->where('id', $kelasId)->value('nama_kelas');
            $this->command->info("   ✅ Kelas \"{$namaKelas}\" (ID: {$kelasId}): {$pertandinganList->count()} partai");
        }

        $this->command->info("✅ Selesai! Total {$totalUpdated} pertandingan diperbarui.");
    }
}
