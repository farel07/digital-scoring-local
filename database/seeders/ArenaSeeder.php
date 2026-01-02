<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arenas = [
            [
                'arena_name' => 'Arena A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arena_name' => 'Arena B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'arena_name' => 'Arena C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('arena')->insert($arenas);

        // Assign users to arenas
        // Arena A: user_id 1-5 (4 juri + 1 dewan)
        // Arena B: bisa ditambahkan nanti jika perlu
        $userArenas = [
            ['user_id' => 1, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 4, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 5, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()], // dewan
            ['user_id' => 6, 'arena_id' => 1, 'created_at' => now(), 'updated_at' => now()], // operator
        ];

        DB::table('user_arena')->insert($userArenas);
    }
}
