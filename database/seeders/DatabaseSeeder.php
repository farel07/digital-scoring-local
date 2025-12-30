<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order (due to foreign key constraints)
        $this->call([
            UserSeeder::class,
            ArenaSeeder::class,
            KelasSeeder::class,
            PertandinganSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Users Created:');
        $this->command->info('- juri1 (ID: 1, Role: juri_1, Password: password)');
        $this->command->info('- juri2 (ID: 2, Role: juri_2, Password: password)');
        $this->command->info('- juri3 (ID: 3, Role: juri_3, Password: password)');
        $this->command->info('- juri4 (ID: 4, Role: juri_4, Password: password)');
        $this->command->info('- dewan1 (ID: 5, Role: dewan, Password: password)');
        $this->command->info('- operator1 (ID: 6, Role: operator, Password: password)');
        $this->command->info('');
        $this->command->info('Active Match:');
        $this->command->info('- Pertandingan ID: 1 (Seni Ganda Putra, Arena A, Status: berlangsung)');
        $this->command->info('');
        $this->command->info('Test URLs:');
        $this->command->info('- Juri: /juri-seni-ganda/1 (user_id=1)');
        $this->command->info('- Dewan: /dewan-seni-ganda/5 (user_id=5)');
        $this->command->info('- Operator: /dewan-operator-seni-ganda/1?jumlah=4');
    }
}
