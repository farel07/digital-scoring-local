<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\KirimPoinSeniTR;
use App\Events\KirimPoinTanding;
use Illuminate\Support\Facades\Cache; // Jangan lupa import ini
use Illuminate\Support\Facades\Log; // Import Log
use App\Helpers\MatchResolver; // Add this
use App\Services\TunggalReguService; // Add this
use App\Services\RealtimeService; // Add this if not already present



class juriController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.juri', ['id' => $id]);
    }

    public function index_tunggal_regu($userId)
    {
        // Get active match for this user
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = \App\Models\User::find($userId);

        // Determine max jurus based on match type
        $maxJurus = $pertandingan->kelas->jenis_pertandingan === 'tunggal' ? 14 : 12;

        return view('seni.tunggal_regu.juri', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'maxJurus' => $maxJurus,
            'matchType' => $pertandingan->kelas->jenis_pertandingan
        ]);
    }

    public function addMoveError(Request $request)
    {
        $validated = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'user_id' => 'required|integer|exists:users,id',
            'jurus_number' => 'required|integer|min:1'
        ]);

        // Validate user has access to this match
        if (!\App\Helpers\MatchResolver::validateUserAccess($validated['user_id'], $validated['pertandingan_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke pertandingan ini'
            ], 403);
        }

        $service = new \App\Services\TunggalReguService();
        $score = $service->addMoveError(
            $validated['pertandingan_id'],
            $validated['user_id'],
            $validated['jurus_number']
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_errors' => $score->total_errors,
                'correctness_score' => $score->correctness_score,
                'total_score' => $score->total_score,
                'errors_per_jurus' => $score->errors_per_jurus
            ]
        ]);
    }

    public function setCategoryScore(Request $request)
    {
        $validated = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'user_id' => 'required|integer|exists:users,id',
            'score' => 'required|numeric|min:0.01|max:0.10',
            'current_jurus' => 'required|integer'
        ]);

        // Validate user has access
        if (!\App\Helpers\MatchResolver::validateUserAccess($validated['user_id'], $validated['pertandingan_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke pertandingan ini'
            ], 403);
        }

        // Get max jurus for this match
        $pertandingan = \App\Models\Pertandingan::find($validated['pertandingan_id']);
        $maxJurus = $pertandingan->kelas->jenis_pertandingan === 'tunggal' ? 14 : 12;

        if ($validated['current_jurus'] > $maxJurus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category score only valid for first ' . $maxJurus . ' jurus'
            ], 400);
        }

        $service = new \App\Services\TunggalReguService();
        $score = $service->setCategoryScore(
            $validated['pertandingan_id'],
            $validated['user_id'],
            $validated['score'],
            $maxJurus
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'category_score' => $score->category_score,
                'total_score' => $score->total_score
            ]
        ]);
    }

    public function kirim_poin_seni_tunggal_regu(Request $request)
    {
        $poin = $request->input('poin');
        $filter = $request->input('filter');
        $pertandingan_id = $request->input('pertandingan_id');
        $type = $request->input('type');
        $role = $request->input('role');

        broadcast(new KirimPoinSeniTR($poin, $filter, $pertandingan_id, $type, $role));
        return response()->json(['status' => 'success']);
    }

    public function tanding_index($id)
    {
        return view('tanding.juri', ['id' => $id]);
    }

    public function kirimPoin(Request $request)
    {
        $pertandinganId = $request->input('pertandingan_id');
        $filter = $request->input('filter');
        $type = $request->input('type');
        $juriId = $request->input('juri_id');
        $poinValue = $request->input('poin');

        $cacheKey = "tanding_{$pertandinganId}_{$filter}_{$type}";
        $pendingVote = Cache::get($cacheKey);

        if (!$pendingVote) {
            // --- KASUS A: Orang Pertama ---

            Cache::put($cacheKey, [
                'first_juri_id' => $juriId,
                'poin' => $poinValue
            ], now()->addSeconds(3));

            // 1. BROADCAST INPUT (Agar lampu juri di layar menyala)
            broadcast(new KirimPoinTanding([
                'type' => $type,
                'poin' => $poinValue,
                'pertandingan_id' => $pertandinganId,
                'filter' => $filter,
                'juri_id' => $juriId,
                'status' => 'input' // <--- Status Input
            ]));

            return response()->json(['status' => 'waiting']);
        } else {
            // --- KASUS B: Orang Kedua (Validasi) ---

            if ($pendingVote['first_juri_id'] != $juriId) {

                // 1. BROADCAST INPUT JURI KEDUA (Agar lampu juri ini juga menyala)
                broadcast(new KirimPoinTanding([
                    'type' => $type,
                    'poin' => $poinValue,
                    'pertandingan_id' => $pertandinganId,
                    'filter' => $filter,
                    'juri_id' => $juriId,
                    'status' => 'input' // <--- Status Input
                ]));

                // 2. BROADCAST POIN SAH (Agar angka skor bertambah)
                broadcast(new KirimPoinTanding([
                    'type' => $type,
                    'poin' => $pendingVote['poin'], // Pakai poin dari cache agar konsisten
                    'pertandingan_id' => $pertandinganId,
                    'filter' => $filter,
                    'juri_id' => $juriId, // Bisa dikosongkan atau diisi salah satu
                    'status' => 'sah' // <--- Status Sah
                ]));

                Cache::forget($cacheKey);
                return response()->json(['status' => 'valid']);
            }

            return response()->json(['status' => 'ignored']);
        }
    }


    public function index_ganda($userId)
    {
        // Get active match for this user
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = \App\Models\User::find($userId);

        return view('seni.ganda.juri', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan
        ]);
    }

    public function kirim_poin_ganda(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'teknik' => 'required|numeric|min:0|max:0.30',
            'kekuatan' => 'required|numeric|min:0|max:0.30',
            'penampilan' => 'required|numeric|min:0|max:0.30',
        ]);

        try {
            // Validate user has access to this match
            if (!\App\Helpers\MatchResolver::validateUserAccess($validatedData['user_id'], $validatedData['pertandingan_id'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke pertandingan ini'
                ], 403);
            }

            $scores = [
                'teknik' => $validatedData['teknik'],
                'kekuatan' => $validatedData['kekuatan'],
                'penampilan' => $validatedData['penampilan']
            ];

            $realtimeService = new \App\Services\RealtimeService();
            $judgeScore = $realtimeService->addJudgeScore(
                $validatedData['pertandingan_id'],
                $validatedData['user_id'],
                $scores
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Poin berhasil dikirim',
                'data' => [
                    'judge_id' => $judgeScore->user_id,
                    'scores' => $scores,
                    'total' => $judgeScore->total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim poin: ' . $e->getMessage()
            ], 500);
        }
    }
}
