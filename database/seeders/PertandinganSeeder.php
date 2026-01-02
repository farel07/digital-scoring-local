<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PertandinganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pertandingan = [
            [
                'kelas_id' => 1, // Seni Ganda Putra
                'arena_id' => 1, // Arena A
                'next_match_id' => null,
                'status' => 'berlangsung', // ACTIVE MATCH!
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 2, // Seni Ganda Putri
                'arena_id' => 2, // Arena B
                'next_match_id' => null,
                'status' => 'belum_dimulai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 3, // Seni Tunggal Putra (14 jurus)
                'arena_id' => 1, // Arena A
                'next_match_id' => null,
                'status' => 'berlangsung', // ACTIVE TUNGGAL MATCH!
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 5, // Seni Regu Putra (12 jurus)
                'arena_id' => 2, // Arena B
                'next_match_id' => null,
                'status' => 'berlangsung', // ACTIVE REGU MATCH!
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pertandingan')->insert($pertandingan);

        // Add sample players for pertandingan 1
        $players = [
            [
                'pertandingan_id' => 1,
                'player_name' => 'Ahmad Faizal',
                'player_contingent' => 'MALAYSIA',
                'side_number' => 1, // Blue
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => 1,
                'player_name' => 'Ahmad Rizal',
                'player_contingent' => 'MALAYSIA',
                'side_number' => 1, // Blue (partner)
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pertandingan_player')->insert($players);
    }
}
