<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TandingPertandinganSeeder extends Seeder
{
    /**
     * Run the database seeds untuk pertandingan TANDING.
     * 
     * Scenario:
     * - Arena A: Akan digunakan untuk pertandingan Tanding
     * - Arena B: Untuk pertandingan lain
     * - Arena C: Cadangan
     * 
     * User Assignment:
     * - Juri 1, 2, 3 -> Assigned ke Arena A (untuk tanding)
     * - Dewan 1 -> Assigned ke Arena A (untuk tanding)
     * - Juri 4 -> Assigned ke Arena B (untuk seni)
     */
    public function run(): void
    {
        // ========================================
        // 1. TAMBAH USERS UNTUK TANDING
        // ========================================

        // Cek apakah user sudah ada, kalau belum buat baru
        $existingUsers = DB::table('users')->pluck('username')->toArray();

        $newUsers = [];

        // Juri untuk Tanding (jika belum ada)
        if (!in_array('juri_tanding_1', $existingUsers)) {
            $newUsers[] = [
                'username' => 'juri_tanding_1',
                'password' => Hash::make('password'),
                'role' => 'juri_1',
                'name' => 'Juri Tanding 1',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!in_array('juri_tanding_2', $existingUsers)) {
            $newUsers[] = [
                'username' => 'juri_tanding_2',
                'password' => Hash::make('password'),
                'role' => 'juri_2',
                'name' => 'Juri Tanding 2',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!in_array('juri_tanding_3', $existingUsers)) {
            $newUsers[] = [
                'username' => 'juri_tanding_3',
                'password' => Hash::make('password'),
                'role' => 'juri_3',
                'name' => 'Juri Tanding 3',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!in_array('dewan_tanding_1', $existingUsers)) {
            $newUsers[] = [
                'username' => 'dewan_tanding_1',
                'password' => Hash::make('password'),
                'role' => 'dewan',
                'name' => 'Dewan Tanding 1',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($newUsers)) {
            DB::table('users')->insert($newUsers);
        }

        // ========================================
        // 2. AMBIL ID USERS
        // ========================================

        $juriTanding1 = DB::table('users')->where('username', 'juri_tanding_1')->first();
        $juriTanding2 = DB::table('users')->where('username', 'juri_tanding_2')->first();
        $juriTanding3 = DB::table('users')->where('username', 'juri_tanding_3')->first();
        $dewanTanding1 = DB::table('users')->where('username', 'dewan_tanding_1')->first();
        $operator2 = DB::table('users')->where('username', 'operator2')->first();

        // ========================================
        // 3. ASSIGN USERS KE ARENA
        // ========================================

        // Hapus assignment lama (clean slate)
        // DB::table('user_arena')->whereIn('user_id', [
        //     $juriTanding1->id ?? 0,
        //     $juriTanding2->id ?? 0,
        //     $juriTanding3->id ?? 0,
        //     $dewanTanding1->id ?? 0,
        // ])->delete();

        $userArenaAssignments = [];

        if ($operator2) {
            $userArenaAssignments[] = [
                'user_id' => $operator2->id,
                'arena_id' => 2, // Arena B
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Arena B (ID: 1) untuk Tanding
        if ($juriTanding1) {
            $userArenaAssignments[] = [
                'user_id' => $juriTanding1->id,
                'arena_id' => 2, // Arena B
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($juriTanding2) {
            $userArenaAssignments[] = [
                'user_id' => $juriTanding2->id,
                'arena_id' => 2, // Arena B
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($juriTanding3) {
            $userArenaAssignments[] = [
                'user_id' => $juriTanding3->id,
                'arena_id' => 2, // Arena B
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($dewanTanding1) {
            $userArenaAssignments[] = [
                'user_id' => $dewanTanding1->id,
                'arena_id' => 2, // Arena B
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        if (!empty($userArenaAssignments)) {
            DB::table('user_arena')->insert($userArenaAssignments);
        }

        // ========================================
        // 4. BUAT KELAS TANDING (Jika belum ada)
        // ========================================

        $kelasTanding = DB::table('kelas')->where('nama_kelas', 'A = 30kg - 40kg')->first();

        if (!$kelasTanding) {
            DB::table('kelas')->insert([
                'nama_kelas' => 'A = 30kg - 40kg',
                'jenis_pertandingan' => 'Tanding',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kelasTanding = DB::table('kelas')->where('nama_kelas', 'A = 30kg - 40kg')->first();
        }

        // ========================================
        // 5. BUAT PERTANDINGAN TANDING
        // ========================================

        $tandingPertandingan = [
            [
                'kelas_id' => $kelasTanding->id,
                'arena_id' => 2, // Arena B
                'next_match_id' => null,
                'status' => 'berlangsung', // Sedang berlangsung
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => $kelasTanding->id,
                'arena_id' => 2, // Arena B
                'next_match_id' => null,
                'status' => 'belum_dimulai', // Belum dimulai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => $kelasTanding->id,
                'arena_id' => 2, // Arena B
                'next_match_id' => null,
                'status' => 'belum_dimulai', // Belum dimulai
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pertandingan')->insert($tandingPertandingan);

        // Ambil ID pertandingan yang baru dibuat
        $pertandinganIds = DB::table('pertandingan')
            ->where('kelas_id', $kelasTanding->id)
            ->orderBy('id')
            ->pluck('id');

        // ========================================
        // 6. BUAT TANDING MATCHES (untuk pertandingan yang sedang berlangsung)
        // ========================================

        if (count($pertandinganIds) >= 1) {
            // Buat tanding match untuk pertandingan pertama (yang sedang berlangsung)
            $tandingMatches = [
                [
                    'pertandingan_id' => $pertandinganIds[0],
                    'current_round' => 1,
                    'blue_total_score' => 0, // Skor awal untuk simulasi
                    'red_total_score' => 0,  // Skor awal untuk simulasi
                    'blue_disqualified' => false,
                    'red_disqualified' => false,
                    'match_status' => 'in_progress',
                    'started_at' => now()->subMinutes(5), // Dimulai 5 menit yang lalu
                    'finished_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('tanding_matches')->insert($tandingMatches);
        }

        // ========================================
        // 7. TAMBAH PLAYERS UNTUK PERTANDINGAN TANDING
        // ========================================

        if (count($pertandinganIds) >= 3) {
            $players = [
                // Pertandingan 1 (sedang berlangsung)
                [
                    'pertandingan_id' => $pertandinganIds[0],
                    'player_name' => 'Ahmad Tanding Blue',
                    'player_contingent' => 'INDONESIA',
                    'side_number' => 1, // Blue
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'pertandingan_id' => $pertandinganIds[0],
                    'player_name' => 'Budi Tanding Red',
                    'player_contingent' => 'MALAYSIA',
                    'side_number' => 2, // Red
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Pertandingan 2 (belum dimulai)
                [
                    'pertandingan_id' => $pertandinganIds[1],
                    'player_name' => 'Charlie Blue',
                    'player_contingent' => 'THAILAND',
                    'side_number' => 1, // Blue
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'pertandingan_id' => $pertandinganIds[1],
                    'player_name' => 'David Red',
                    'player_contingent' => 'SINGAPORE',
                    'side_number' => 2, // Red
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Pertandingan 3 (belum dimulai)
                [
                    'pertandingan_id' => $pertandinganIds[2],
                    'player_name' => 'Eko Blue',
                    'player_contingent' => 'VIETNAM',
                    'side_number' => 1, // Blue
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'pertandingan_id' => $pertandinganIds[2],
                    'player_name' => 'Fajar Red',
                    'player_contingent' => 'PHILIPPINES',
                    'side_number' => 2, // Red
                    'total_score' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('pertandingan_player')->insert($players);
        }

        echo "\n✅ Tanding Seeder completed successfully!\n";
        echo "   - Created/verified Juri Tanding 1, 2, 3\n";
        echo "   - Created/verified Dewan Tanding 1\n";
        echo "   - Assigned users to Arena B\n";
        echo "   - Created 3 Tanding pertandingan\n";
        echo "   - Created 1 Tanding match (in_progress) with sample scores\n";
        echo "   - Added players to matches\n\n";

        echo "📋 Login Credentials:\n";
        echo "   Juri 1: juri_tanding_1 / password\n";
        echo "   Juri 2: juri_tanding_2 / password\n";
        echo "   Juri 3: juri_tanding_3 / password\n";
        echo "   Dewan:  dewan_tanding_1 / password\n\n";
    }
}
