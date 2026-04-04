<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TandingMatch;
use App\Models\TandingScore;
use App\Models\ValidationRequest;
use App\Models\ValidationVote;
use Illuminate\Http\Request;
use App\Events\KirimPoinSeniTR;
use App\Events\KirimPoinTanding;
use App\Events\ValidationVoteReceived;
use App\Events\ValidationCompleted;
use App\Helpers\MatchResolver; // Add this
use App\Services\TunggalReguService; // Add this
use Illuminate\Support\Facades\Log; // Import Log
use Illuminate\Support\Facades\Cache; // Jangan lupa import ini
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

        // Get side parameter from URL (default to 1)
        $side = request()->get('side', 1);

        // Load players for this match
        $pertandingan->load('players');

        // Get players for current side
        $currentSidePlayers = $pertandingan->players->where('side_number', $side);

        // Get opponent side number
        $opponentSide = $side == 1 ? 2 : 1;

        return view('seni.tunggal_regu.juri', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'maxJurus' => $maxJurus,
            'matchType' => $pertandingan->kelas->jenis_pertandingan,
            'currentSide' => $side,
            'currentSidePlayers' => $currentSidePlayers,
            'opponentSide' => $opponentSide
        ]);
    }

    public function addMoveError(Request $request)
    {
        $validated = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'user_id' => 'required|integer|exists:users,id',
            'jurus_number' => 'required|integer|min:1',
            'side' => 'nullable|in:1,2'
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
            $validated['jurus_number'],
            $validated['side'] ?? '1'
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
            'current_jurus' => 'required|integer',
            'side' => 'nullable|in:1,2'
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
            $maxJurus,
            $validated['side'] ?? '1'
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
        // Validasi: Cek apakah user punya akses ke pertandingan ini
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if (!$user->hasAccessToPertandingan($id)) {
            $pertandingan = \App\Models\Pertandingan::find($id);

            return response()->view('errors.403', [
                'message' => 'Anda tidak memiliki akses ke pertandingan ini.',
                'your_arenas' => $user->arenas->pluck('arena_name')->implode(', '),
                'match_arena' => $pertandingan->arena->arena_name ?? 'Unknown',
            ], 403);
        }

        return view('tanding.juri', ['id' => $id]);
    }

    public function kirimPoin(Request $request)
    {
        $pertandinganId = $request->input('pertandingan_id');
        $filter = $request->input('filter');
        $type = strtoupper($request->input('type')); // Convert to uppercase for consistency
        $juriId = $request->input('juri_id');
        $poinValue = $request->input('poin');

        // Get juri number from user role (juri_1 -> 1, juri_2 -> 2, etc)
        $user = User::find($juriId);
        $juriNumber = $this->getJuriNumber($user->role);

        $cacheKey = "tanding_{$pertandinganId}_{$filter}_{$type}";
        $pendingVote = Cache::get($cacheKey);

        if (!$pendingVote) {
            // --- KASUS A: Orang Pertama ---

            Cache::put($cacheKey, [
                'first_juri_id' => $juriId,
                'poin' => $poinValue
            ], now()->addSeconds(3));

            // DATABASE PERSISTENCE: Get or create tanding match
            $tandingMatch = TandingMatch::firstOrCreate(
                ['pertandingan_id' => $pertandinganId],
                [
                    'current_round' => 1,
                    'match_status' => 'in_progress',
                    'started_at' => now()
                ]
            );

            // Save score to database (status: input)
            TandingScore::create([
                'tanding_match_id' => $tandingMatch->id,
                'judge_id' => $juriId,
                'team' => $filter,
                'technique' => $type,
                'points' => $type === 'TENDANG' ? 2 : 1, // Fixed: uppercase comparison
                'round' => $tandingMatch->current_round,
                'status' => 'input',
            ]);

            // 1. BROADCAST INPUT (Agar lampu juri di layar menyala)
            broadcast(new KirimPoinTanding([
                'type' => $type,
                'poin' => $poinValue,
                'pertandingan_id' => $pertandinganId,
                'filter' => $filter,
                'juri_id' => $juriNumber, // Send juri number (1,2,3) not user_id
                'status' => 'input' // <--- Status Input
            ]));

            return response()->json(['status' => 'waiting']);
        } else {
            // --- KASUS B: Orang Kedua (Validasi) ---

            if ($pendingVote['first_juri_id'] != $juriId) {

                // DATABASE PERSISTENCE: Get match and save second juri score
                $tandingMatch = TandingMatch::where('pertandingan_id', $pertandinganId)->first();

                if ($tandingMatch) {
                    // DELETE all 'input' records for this technique/team combo
                    // (We don't need to keep individual input records)
                    TandingScore::where('tanding_match_id', $tandingMatch->id)
                        ->where('team', $filter)
                        ->where('technique', $type)
                        ->where('status', 'input')
                        ->delete();

                    // CREATE ONLY ONE validated score record
                    TandingScore::create([
                        'tanding_match_id' => $tandingMatch->id,
                        'judge_id' => $juriId, // Can use either juri's ID, or leave null
                        'team' => $filter,
                        'technique' => $type,
                        'points' => $type === 'TENDANG' ? 2 : 1,
                        'round' => $tandingMatch->current_round,
                        'status' => 'sah', // Directly mark as validated
                    ]);

                    // Update total score
                    $points = $type === 'TENDANG' ? 2 : 1; // Fixed: uppercase comparison

                    if ($filter === 'blue') {
                        $tandingMatch->increment('blue_total_score', $points);
                    } else {
                        $tandingMatch->increment('red_total_score', $points);
                    }
                }

                // 1. BROADCAST INPUT JURI KEDUA (Agar lampu juri ini juga menyala)
                broadcast(new KirimPoinTanding([
                    'type' => $type,
                    'poin' => $poinValue,
                    'pertandingan_id' => $pertandinganId,
                    'filter' => $filter,
                    'juri_id' => $juriNumber, // Send juri number (1,2,3) not user_id
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

        // Get side parameter from URL (default to 1)
        $side = request()->get('side', 1);

        // Load players for this match
        $pertandingan->load('players');

        // Get players for current side
        $currentSidePlayers = $pertandingan->players->where('side_number', $side);

        // Get opponent side number
        $opponentSide = $side == 1 ? 2 : 1;

        // Fetch existing score for this user, match, and side
        $existingScore = \App\Models\JudgeScore::where('pertandingan_id', $pertandingan->id)
            ->where('user_id', $user->id)
            ->where('side', $side)
            ->first();

        return view('seni.ganda.juri', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'currentSide' => $side,
            'currentSidePlayers' => $currentSidePlayers,
            'opponentSide' => $opponentSide,
            'existingScore' => $existingScore // Pass existing score
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
            'side' => 'nullable|in:1,2'
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
            \Illuminate\Support\Facades\Log::info('Ganda Juri Submission:', [
                'user_id' => $validatedData['user_id'],
                'side_received' => $validatedData['side'] ?? 'null',
                'side_final' => $validatedData['side'] ?? '1',
                'scores' => $scores
            ]);

            $judgeScore = $realtimeService->addJudgeScore(
                $validatedData['pertandingan_id'],
                $validatedData['user_id'],
                $scores,
                $validatedData['side'] ?? '1'
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

    /**
     * Get juri display number from role (juri_1 -> 1, juri_2 -> 2, etc)
     */
    private function getJuriNumber($role)
    {
        // Extract number from role (juri_1 -> 1, juri_2 -> 2, juri_3 -> 3, juri_4 -> 4)
        if (preg_match('/juri_(\d+)/', $role, $matches)) {
            return (int)$matches[1];
        }
        return 1; // Default to 1 if role doesn't match pattern
    }

    /**
     * Submit validation vote from juri
     */
    public function submitValidationVote(Request $request)
    {
        $validationRequestId = $request->input('validation_request_id');
        $vote = $request->input('vote'); // 'SAH', 'TIDAK SAH', 'NETRAL'
        $juriId = auth()->user()->id;

        // Check if this juri already voted
        $existingVote = ValidationVote::where('validation_request_id', $validationRequestId)
            ->where('juri_id', $juriId)
            ->first();

        if ($existingVote) {
            return response()->json([
                'status' => 'already_voted',
                'message' => 'Anda sudah memberikan vote untuk request ini'
            ], 400);
        }

        // Save vote
        ValidationVote::create([
            'validation_request_id' => $validationRequestId,
            'juri_id' => $juriId,
            'vote' => $vote
        ]);

        // Get validation request
        $validationRequest = ValidationRequest::find($validationRequestId);

        // Broadcast vote received
        broadcast(new ValidationVoteReceived([
            'validation_request_id' => $validationRequestId,
            'juri_id' => $juriId,
            'vote' => $vote,
            'pertandingan_id' => $validationRequest->tandingMatch->pertandingan_id
        ]));

        // Check if we have enough votes (2 or 3)
        $voteCount = $validationRequest->votes()->count();

        if ($voteCount >= 2) {
            $result = $validationRequest->calculateResult();

            $validationRequest->update([
                'result' => $result,
                'status' => 'completed'
            ]);

            // Broadcast completion
            broadcast(new ValidationCompleted([
                'validation_request_id' => $validationRequestId,
                'result' => $result,
                'validation_type' => $validationRequest->validation_type,
                'team' => $validationRequest->team,
                'pertandingan_id' => $validationRequest->tandingMatch->pertandingan_id
            ]));
        }

        return response()->json([
            'status' => 'voted',
            'vote' => $vote
        ]);
    }
}
