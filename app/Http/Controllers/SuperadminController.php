<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\User;
use App\Models\Pertandingan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SuperadminController extends Controller
{
    /**
     * Display the superadmin dashboard
     */
    public function index()
    {
        return view('superadmin.superadmin');
    }

    // ==================== ARENA CRUD ====================

    /**
     * Get all arenas
     */
    public function getArenas()
    {
        $arenas = Arena::withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($arenas);
    }

    /**
     * Create new arena
     */
    public function createArena(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arena_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $arena = Arena::create([
            'arena_name' => $request->arena_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Arena berhasil ditambahkan',
            'data' => $arena
        ], 201);
    }

    /**
     * Update arena
     */
    public function updateArena(Request $request, $id)
    {
        $arena = Arena::find($id);

        if (!$arena) {
            return response()->json([
                'success' => false,
                'message' => 'Arena tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'arena_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $arena->update([
            'arena_name' => $request->arena_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Arena berhasil diupdate',
            'data' => $arena
        ]);
    }

    /**
     * Delete arena
     */
    public function deleteArena($id)
    {
        $arena = Arena::find($id);

        if (!$arena) {
            return response()->json([
                'success' => false,
                'message' => 'Arena tidak ditemukan'
            ], 404);
        }

        $arena->delete();

        return response()->json([
            'success' => true,
            'message' => 'Arena berhasil dihapus'
        ]);
    }

    // ==================== USER CRUD ====================

    /**
     * Get all users
     */
    public function getUsers()
    {
        $users = User::with('arenas')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,juri_1,juri_2,juri_3,juri_4,juri_5,juri_6,juri_7,juri_8,juri_9,juri_10,dewan,operator',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,juri_1,juri_2,juri_3,juri_4,juri_5,juri_6,juri_7,juri_8,juri_9,juri_10,dewan,operator',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'username' => $request->username,
            'name' => $request->name,
            'role' => $request->role,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    // ==================== USER-ARENA ASSIGNMENT ====================

    /**
     * Get all assignments grouped by arena
     */
    public function getAssignments()
    {
        $arenas = Arena::with(['users' => function ($query) {
            $query->select('users.id', 'users.username', 'users.name', 'users.role');
        }])->get();

        return response()->json($arenas);
    }

    /**
     * Assign user to arena
     */
    public function createAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'arena_id' => 'required|exists:arena,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        // Check if user already has any arena assignment
        $existingArenas = $user->arenas()->get();
        if ($existingArenas->count() > 0) {
            $arenaNames = $existingArenas->pluck('arena_name')->join(', ');
            return response()->json([
                'success' => false,
                'message' => "User sudah ditempatkan di arena: {$arenaNames}. Hapus penempatan tersebut terlebih dahulu."
            ], 422);
        }

        // Assign user to arena
        $user->arenas()->attach($request->arena_id);

        return response()->json([
            'success' => true,
            'message' => 'Penempatan berhasil ditambahkan'
        ]);
    }

    /**
     * Remove user from arena
     */
    public function deleteAssignment($arenaId, $userId)
    {
        $user = User::find($userId);
        $arena = Arena::find($arenaId);

        if (!$user || !$arena) {
            return response()->json([
                'success' => false,
                'message' => 'User atau Arena tidak ditemukan'
            ], 404);
        }

        $user->arenas()->detach($arenaId);

        return response()->json([
            'success' => true,
            'message' => 'Penempatan berhasil dihapus'
        ]);
    }

    // ==================== MATCH MANAGEMENT ====================

    /**
     * Get all matches with players and contingent
     */
    public function getMatches()
    {
        $matches = Pertandingan::with([
            'kelas',
            'arena',
            'players' => function ($query) {
                $query->select('id', 'pertandingan_id', 'player_name', 'player_contingent', 'side_number');
            }
        ])->orderBy('created_at', 'desc')
            ->get();

        return response()->json($matches);
    }

    /**
     * Get all kelas for dropdown
     */
    public function getKelas()
    {
        $kelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        return response()->json($kelas);
    }

    /**
     * Create new match with players
     */
    public function createMatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'arena_id' => 'required|exists:arena,id',
            'status' => 'required|in:belum_dimulai,berlangsung,selesai',
            'players' => 'required|array|min:1',
            'players.*.player_name' => 'required|string|max:255',
            'players.*.player_contingent' => 'required|string|max:255',
            'players.*.side_number' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create pertandingan
            $match = Pertandingan::create([
                'kelas_id' => $request->kelas_id,
                'arena_id' => $request->arena_id,
                'status' => $request->status,
            ]);

            // Create players
            foreach ($request->players as $playerData) {
                $match->players()->create([
                    'player_name' => $playerData['player_name'],
                    'player_contingent' => $playerData['player_contingent'],
                    'side_number' => $playerData['side_number'],
                ]);
            }

            DB::commit();

            // Load relationships for response
            $match->load(['kelas', 'arena', 'players']);

            return response()->json([
                'success' => true,
                'message' => 'Pertandingan berhasil ditambahkan',
                'data' => $match
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get match detail
     */
    public function getMatchDetail($id)
    {
        $match = Pertandingan::with([
            'kelas',
            'arena',
            'players'
        ])->find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $match
        ]);
    }

    /**
     * Reassign match to different arena
     */
    public function reassignMatchArena(Request $request, $id)
    {
        $match = Pertandingan::find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'arena_id' => 'required|exists:arena,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $match->update([
            'arena_id' => $request->arena_id
        ]);

        $match->load('arena');

        return response()->json([
            'success' => true,
            'message' => 'Pertandingan berhasil dipindahkan',
            'data' => $match
        ]);
    }

    /**
     * Update match status
     */
    public function updateMatchStatus(Request $request, $id)
    {
        $match = Pertandingan::find($id);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:belum_dimulai,berlangsung,selesai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $match->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pertandingan berhasil diupdate',
            'data' => $match
        ]);
    }

    /**
     * Import matches from CSV file
     */
    public function importMatches(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            // Remove header row
            $header = array_shift($csvData);

            $created = 0;
            $errors = [];
            $partaiToMatchId = []; // Map Partai number to created match ID

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Parse CSV columns
                    $partai = $row[0] ?? null;
                    $kategori = $row[1] ?? null;
                    $jenisPertandingan = $row[2] ?? 'Tanding';
                    $namaKelas = $row[3] ?? null;
                    $unit1 = $row[4] ?? null;
                    $kontingen1 = $row[5] ?? null;
                    $unit2 = $row[6] ?? null;
                    $kontingen2 = $row[7] ?? null;
                    $arenaName = $row[8] ?? 'Belum Ditentukan';
                    $nextMatch = $row[9] ?? null;

                    // Skip if kelas name is empty
                    if (empty($namaKelas)) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Kelas tidak boleh kosong";
                        continue;
                    }

                    // Find or create Kelas
                    $kelas = Kelas::where('nama_kelas', $namaKelas)
                        ->where('jenis_pertandingan', $jenisPertandingan)
                        ->first();

                    if (!$kelas) {
                        $kelas = Kelas::create([
                            'nama_kelas' => $namaKelas,
                            'jenis_pertandingan' => $jenisPertandingan
                        ]);
                    }

                    // Find Arena (default to first arena if "Belum Ditentukan")
                    $arena = null;
                    if ($arenaName !== 'Belum Ditentukan') {
                        $arena = Arena::where('arena_name', $arenaName)->first();
                    }

                    // Use first arena as default if no arena found
                    if (!$arena) {
                        $arena = Arena::first();
                    }

                    // Create Pertandingan
                    $pertandingan = Pertandingan::create([
                        'kelas_id' => $kelas->id,
                        'arena_id' => $arena ? $arena->id : null,
                        'status' => 'belum_dimulai',
                        'next_match_id' => null // Will be set later
                    ]);

                    // Store mapping for next_match_id
                    if ($partai) {
                        $partaiToMatchId[$partai] = [
                            'match_id' => $pertandingan->id,
                            'next_match' => $nextMatch
                        ];
                    }

                    // Create players
                    // Unit 1 (Blue/Side 1)
                    if (!empty($unit1) && $unit1 !== '-') {
                        $pertandingan->players()->create([
                            'player_name' => $unit1,
                            'player_contingent' => $kontingen1 ?? '-',
                            'side_number' => 1
                        ]);
                    }

                    // Unit 2 (Red/Side 2)
                    if (!empty($unit2) && $unit2 !== '-') {
                        $pertandingan->players()->create([
                            'player_name' => $unit2,
                            'player_contingent' => $kontingen2 ?? '-',
                            'side_number' => 2
                        ]);
                    }

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            // Update next_match_id based on Partai mapping
            foreach ($partaiToMatchId as $partai => $data) {
                if (!empty($data['next_match'])) {
                    $nextMatchData = $partaiToMatchId[$data['next_match']] ?? null;
                    if ($nextMatchData) {
                        Pertandingan::where('id', $data['match_id'])
                            ->update(['next_match_id' => $nextMatchData['match_id']]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$created} pertandingan",
                'data' => [
                    'created' => $created,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
