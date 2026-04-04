<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenaltyRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Tunggal / Regu
            [
                'type' => 'waktu',
                'name' => 'WAKTU',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            [
                'type' => 'keluar_garis',
                'name' => 'SETIAP KALI KELUAR GARIS',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            [
                'type' => 'senjata_jatuh_1',
                'name' => 'SENJATA JATUH TIDAK SESUAI DESKRIPSI',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            [
                'type' => 'senjata_jatuh_2',
                'name' => 'SENJATA TIDAK JATUH SESUAI DESKRIPSI',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            [
                'type' => 'salam_suara',
                'name' => 'TIDAK ADA SALAM & MENGELUARKAN SUARA',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            [
                'type' => 'atribut',
                'name' => 'BAJU / SENJATA TIDAK SESUAI (PATAH)',
                'value' => -0.50,
                'category' => 'tunggal_regu',
            ],
            // Ganda can be added here later with category 'ganda'
        ];

        foreach ($rules as $rule) {
            \App\Models\PenaltyRule::updateOrCreate(
                ['type' => $rule['type']],
                $rule
            );
        }
    }
}
