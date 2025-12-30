<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            [
                'nama_kelas' => 'Seni Ganda Putra',
                'jenis_pertandingan' => 'ganda',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Seni Ganda Putri',
                'jenis_pertandingan' => 'ganda',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Seni Tunggal Putra',
                'jenis_pertandingan' => 'tunggal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'Seni Tunggal Putri',
                'jenis_pertandingan' => 'tunggal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kelas')->insert($kelas);
    }
}
