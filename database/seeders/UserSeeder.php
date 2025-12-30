<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'juri1',
                'password' => Hash::make('password'),
                'role' => 'juri_1',
                'name' => 'Juri 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'juri2',
                'password' => Hash::make('password'),
                'role' => 'juri_2',
                'name' => 'Juri 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'juri3',
                'password' => Hash::make('password'),
                'role' => 'juri_3',
                'name' => 'Juri 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'juri4',
                'password' => Hash::make('password'),
                'role' => 'juri_4',
                'name' => 'Juri 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'dewan1',
                'password' => Hash::make('password'),
                'role' => 'dewan',
                'name' => 'Dewan Juri 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'operator1',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'name' => 'Operator 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
