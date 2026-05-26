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
        $timerTanding1 = DB::table('users')->where('username', 'timer_tanding_1')->first();

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

        if ($timerTanding1) {
            $userArenaAssignments[] = [
                'user_id' => $timerTanding1->id,
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
                'jenis_pertandingan' => 'tanding',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kelasTanding = DB::table('kelas')->where('nama_kelas', 'A = 30kg - 40kg')->first();
        }

        // ========================================
        // 5. BUAT PERTANDINGAN TANDING
        // ========================================

        // ========================================
        // 5. BUAT PERTANDINGAN TANDING DENGAN BRACKET TERINTEGRASI
        // ========================================

        // Buat pertandingan final/selanjutnya (Match C / Match 4) terlebih dahulu
        $match4Id = DB::table('pertandingan')->insertGetId([
            'kelas_id' => $kelasTanding->id,
            'arena_id' => 2, // Arena B
            'next_match_id' => null,
            'status' => 'belum_dimulai', // Belum dimulai
            'jenis_pertandingan' => 'prestasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat pertandingan asal A (Match 1) yang menunjuk ke Match 4
        $match1Id = DB::table('pertandingan')->insertGetId([
            'kelas_id' => $kelasTanding->id,
            'arena_id' => 2, // Arena B
            'next_match_id' => $match4Id,
            'status' => 'berlangsung', // Sedang berlangsung
            'jenis_pertandingan' => 'prestasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat pertandingan asal B (Match 5) yang menunjuk ke Match 4
        $match5Id = DB::table('pertandingan')->insertGetId([
            'kelas_id' => $kelasTanding->id,
            'arena_id' => 2, // Arena B
            'next_match_id' => $match4Id,
            'status' => 'berlangsung', // Sedang berlangsung
            'jenis_pertandingan' => 'prestasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Pertandingan Pemasalan lainnya
        $match2Id = DB::table('pertandingan')->insertGetId([
            'kelas_id' => $kelasTanding->id,
            'arena_id' => 2, // Arena B
            'next_match_id' => null,
            'status' => 'berlangsung', // Sedang berlangsung
            'jenis_pertandingan' => 'pemasalan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $match3Id = DB::table('pertandingan')->insertGetId([
            'kelas_id' => $kelasTanding->id,
            'arena_id' => 2, // Arena B
            'next_match_id' => null,
            'status' => 'berlangsung', // Sedang berlangsung
            'jenis_pertandingan' => 'pemasalan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========================================
        // 6. BUAT TANDING MATCHES (untuk semua pertandingan yang sedang berlangsung)
        // ========================================

        $tandingMatches = [
            [
                'pertandingan_id' => $match1Id,
                'current_round' => 1,
                'blue_total_score' => 0,
                'red_total_score' => 0,
                'blue_disqualified' => false,
                'red_disqualified' => false,
                'match_status' => 'in_progress',
                'started_at' => now()->subMinutes(5),
                'finished_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match5Id,
                'current_round' => 1,
                'blue_total_score' => 0,
                'red_total_score' => 0,
                'blue_disqualified' => false,
                'red_disqualified' => false,
                'match_status' => 'in_progress',
                'started_at' => now()->subMinutes(3),
                'finished_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match2Id,
                'current_round' => 1,
                'blue_total_score' => 0,
                'red_total_score' => 0,
                'blue_disqualified' => false,
                'red_disqualified' => false,
                'match_status' => 'in_progress',
                'started_at' => now()->subMinutes(10),
                'finished_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match3Id,
                'current_round' => 1,
                'blue_total_score' => 0,
                'red_total_score' => 0,
                'blue_disqualified' => false,
                'red_disqualified' => false,
                'match_status' => 'in_progress',
                'started_at' => now()->subMinutes(1),
                'finished_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tanding_matches')->insert($tandingMatches);

        // ========================================
        // 7. TAMBAH PLAYERS UNTUK PERTANDINGAN TANDING
        // ========================================

        $players = [
            // Players Match 1 (DKI Jakarta vs Jawa Barat)
            [
                'pertandingan_id' => $match1Id,
                'player_name' => 'Ahmad Fauzi',
                'player_contingent' => 'DKI JAKARTA',
                'side_number' => 1, // Blue
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match1Id,
                'player_name' => 'Budi Santoso',
                'player_contingent' => 'JAWA BARAT',
                'side_number' => 2, // Red
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Players Match 5 (Riau vs NTB)
            [
                'pertandingan_id' => $match5Id,
                'player_name' => 'Irfan Maulana',
                'player_contingent' => 'RIAU',
                'side_number' => 1, // Blue
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match5Id,
                'player_name' => 'Joko Susilo',
                'player_contingent' => 'NUSA TENGGARA BARAT',
                'side_number' => 2, // Red
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Players Match 2 (Jawa Tengah vs Jawa Timur)
            [
                'pertandingan_id' => $match2Id,
                'player_name' => 'Cahyo Prabowo',
                'player_contingent' => 'JAWA TENGAH',
                'side_number' => 1, // Blue
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match2Id,
                'player_name' => 'Dedi Kurniawan',
                'player_contingent' => 'JAWA TIMUR',
                'side_number' => 2, // Red
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Players Match 3 (Sumatera Utara vs Sulawesi Selatan)
            [
                'pertandingan_id' => $match3Id,
                'player_name' => 'Eko Prasetyo',
                'player_contingent' => 'SUMATERA UTARA',
                'side_number' => 1, // Blue
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pertandingan_id' => $match3Id,
                'player_name' => 'Fajar Hidayat',
                'player_contingent' => 'SULAWESI SELATAN',
                'side_number' => 2, // Red
                'total_score' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pertandingan_player')->insert($players);

        echo "\n✅ Tanding Seeder completed successfully!\n";
        echo "   - Created/verified Juri Tanding 1, 2, 3\n";
        echo "   - Created/verified Dewan Tanding 1\n";
        echo "   - Assigned users to Arena B\n";
        echo "   - Created 5 Tanding pertandingan (Match 1 & 5 terhubung ke Match 4 sebagai final)\n";
        echo "   - Created 4 Tanding matches (in_progress)\n";
        echo "   - Added 8 players for active matches (Match 4 kosong menunggu pemenang)\n\n";

        echo "📋 Login Credentials:\n";
        echo "   Juri 1: juri_tanding_1 / password\n";
        echo "   Juri 2: juri_tanding_2 / password\n";
        echo "   Juri 3: juri_tanding_3 / password\n";
        echo "   Dewan:  dewan_tanding_1 / password\n\n";

        echo "🔗 Test URLs:\n";
        echo "   Dewan Match 1: /dewan-tanding/{$match1Id} (Pemenang akan lolos ke Match {$match4Id})\n";
        echo "   Dewan Match 5: /dewan-tanding/{$match5Id} (Pemenang akan lolos ke Match {$match4Id})\n\n";
    }
}
