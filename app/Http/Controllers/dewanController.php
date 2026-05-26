<?php

namespace App\Http\Controllers;

use App\Events\KirimPenalti;
use App\Events\KirimPenaltiTanding;
use App\Events\ValidationRequestSent;
use App\Models\Pertandingan;
use App\Models\TandingMatch;
use App\Models\TandingPenalty;
use App\Models\User;
use App\Models\ValidationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dewanController extends Controller
{
    //
    public function index($id)
    {
        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();
        return view('seni.tunggal_regu.dewan', ['id' => $id, 'penaltyRules' => $penaltyRules]);
    }

    public function index_tunggal_regu($userId)
    {
        // Get active match for this user (same pattern as juri)
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            $user         = \App\Models\User::find($userId);
            $arena = $user ? $user->arenas()->first() : null;
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.',
                'arena_id' => $arena ? $arena->id : null,
                'arena_name' => $arena ? $arena->arena_name : '-',
            ], 404);
        }

        $user         = \App\Models\User::find($userId);
        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();

        // Jenis kompetisi: prestasi (2 tim) | pemasalan (N peserta)
        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';

        // All players grouped by side_number
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        $cachedSide = \Illuminate\Support\Facades\Cache::get("active_side_seni_tunggal_{$pertandingan->id}") ?? ($allSides->first() ?? 1);

        return view('seni.tunggal_regu.dewan', [
            'id'                => $pertandingan->id,
            'user'              => $user,
            'pertandingan'      => $pertandingan,
            'penaltyRules'      => $penaltyRules,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers'        => $allPlayers,
            'allSides'          => $allSides,
            'cachedSide'        => $cachedSide,
        ]);
    }

    public function tanding_index($id)
    {
        $pertandingan = Pertandingan::findOrFail($id);

        if ($pertandingan->status === 'selesai') {
            return redirect('/waiting-match');
        }

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

        return view('tanding.dewan', [
            'id'                 => $id,
            'pertandingan'       => $pertandingan,
            'jenis_pertandingan' => $pertandingan->jenis_pertandingan ?? 'prestasi',
            'max_ronde'          => $pertandingan->maxRonde(),
            'playerBlue'         => $pertandingan->players()->where('side_number', 1)->first(),
            'playerRed'          => $pertandingan->players()->where('side_number', 2)->first(),
        ]);
    }

    public function kirim_pelanggaran_seni_tunggal_regu(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'penalty_id'      => 'required|string',
            'value'           => 'required|numeric',
            'side'            => 'nullable|integer|min:1',  // pemasalan: bisa > 2
        ]);

        $side = (string) ($validatedData['side'] ?? '1');
        $validatedData['side'] = $side;

        // Persist penalty to database
        if ($validatedData['value'] == 0) {
            // Clear penalty (set status to 'cleared' or delete)
            // Find the most recent active penalty with this type and side
            $penalty = \App\Models\Penalty::where('pertandingan_id', $validatedData['pertandingan_id'])
                ->where('type', $validatedData['penalty_id'])
                ->where('status', 'active')
                ->where('side', $side)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($penalty) {
                $penalty->update(['status' => 'cleared']);
            }
        } else {
            // Add penalty - create new entry with unique ID
            $uniquePenaltyId = $validatedData['penalty_id'] . '_' . time() . '_' . rand(1000, 9999);

            \App\Models\Penalty::create([
                'pertandingan_id' => $validatedData['pertandingan_id'],
                'penalty_id' => $uniquePenaltyId,
                'type' => $validatedData['penalty_id'], // Original type for grouping
                'value' => $validatedData['value'],
                'status' => 'active',
                'side' => $side,
            ]);
        }

        // Broadcast to Dewan Operator
        broadcast(new KirimPenalti($validatedData))->toOthers();

        return response()->json(['status' => 'success', 'data' => $validatedData]);
    }

    public function switch_active_side_tunggal_regu(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'side'            => 'required|string',
        ]);

        $pertandinganId = $validatedData['pertandingan_id'];
        $side = $validatedData['side'];

        // Store in cache for 24 hours so latecomers can get it
        \Illuminate\Support\Facades\Cache::put("active_side_seni_tunggal_{$pertandinganId}", $side, 86400);

        // Broadcast event
        broadcast(new \App\Events\ActiveSideChangedSeni($pertandinganId, $side))->toOthers();

        return response()->json([
            'status' => 'success',
            'side' => $side
        ]);
    }

    function kirim_penalti_tanding(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'penalty_id'      => 'required|',
            'value'           => 'required',
            'filter'          => 'required|string'
        ]);

        // ── BATAS MAKSIMUM PER TIM ───────────────────────────────────────────
        $limits = [
            'bina'       => 2,
            'teguran'    => 2,
            'peringatan' => 3,
        ];

        $penaltyType = $validatedData['penalty_id'];
        $team        = $validatedData['filter'];

        // Cek apakah penalty ini punya batas
        if (isset($limits[$penaltyType])) {
            $tandingMatchCheck = TandingMatch::where('pertandingan_id', $validatedData['pertandingan_id'])->first();

            if ($tandingMatchCheck) {
                $existingCount = TandingPenalty::where('tanding_match_id', $tandingMatchCheck->id)
                    ->where('team', $team)
                    ->where('penalty_type', $penaltyType)
                    ->count();

                if ($existingCount >= $limits[$penaltyType]) {
                    return response()->json([
                        'status'    => 'limit_reached',
                        'message'   => ucfirst($penaltyType) . ' untuk tim ' . strtoupper($team) . ' sudah mencapai batas maksimum (' . $limits[$penaltyType] . 'x).',
                        'count'     => $existingCount,
                        'limit'     => $limits[$penaltyType],
                    ], 422);
                }
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        // Calculate point deduction based on penalty type
        $pointDeduction = 0;
        $isDisqualified = false;

        if ($validatedData['penalty_id'] === 'jatuhan') {
            // Jatuhan: +3 points for the athlete (opponent falls)
            $pointDeduction = 3; // Positive value to ADD points
        } elseif ($validatedData['penalty_id'] === 'teguran') {
            // Teguran 1: -1 point, Teguran 2: -2 points
            if ($validatedData['value'] == 1) {
                $pointDeduction = -1;
            } elseif ($validatedData['value'] == 2) {
                $pointDeduction = -2;
            }
        } elseif ($validatedData['penalty_id'] === 'peringatan') {
            // Peringatan 1: -5, Peringatan 2: -10, Peringatan 3: -15 + DQ
            if ($validatedData['value'] == 1) {
                $pointDeduction = -5;
            } elseif ($validatedData['value'] == 2) {
                $pointDeduction = -10;
            } elseif ($validatedData['value'] == 3) {
                $pointDeduction = -15;
                $isDisqualified = true;
            }
        }

        // DATABASE PERSISTENCE: Get or create tanding match
        $tandingMatch = TandingMatch::firstOrCreate(
            ['pertandingan_id' => $validatedData['pertandingan_id']],
            [
                'current_round' => 1,
                'match_status'  => 'in_progress',
                'started_at'    => now()
            ]
        );

        // Save penalty to database
        TandingPenalty::create([
            'tanding_match_id'        => $tandingMatch->id,
            'team'                    => $validatedData['filter'],
            'penalty_type'            => $validatedData['penalty_id'],
            'penalty_value'           => $validatedData['value'],
            'point_deduction'         => $pointDeduction,
            'round'                   => $tandingMatch->current_round,
            'caused_disqualification' => $isDisqualified,
        ]);

        // Update total score in database
        if ($validatedData['filter'] === 'blue') {
            $tandingMatch->increment('blue_total_score', $pointDeduction);
            if ($isDisqualified) {
                $tandingMatch->update(['blue_disqualified' => true]);
            }
        } else {
            $tandingMatch->increment('red_total_score', $pointDeduction);
            if ($isDisqualified) {
                $tandingMatch->update(['red_disqualified' => true]);
            }
        }

        // Add calculated values to broadcast data
        $validatedData['point_deduction'] = $pointDeduction;
        $validatedData['is_disqualified'] = $isDisqualified;

        broadcast(new KirimPenaltiTanding($validatedData))->toOthers();

        return response()->json([
            'status' => 'success',
            'data'   => $validatedData
        ]);
    }

    function index_ganda($userId)
    {
        // Get active match for this user
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = \App\Models\User::find($userId);

        // Jenis kompetisi: prestasi (2 tim) | pemasalan (N peserta)
        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';

        // All players grouped by side_number
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        $cachedSide = \Illuminate\Support\Facades\Cache::get("active_side_seni_ganda_{$pertandingan->id}") ?? ($allSides->first() ?? 1);

        return view('seni.ganda.dewan', [
            'id'                => $pertandingan->id,
            'user'              => $user,
            'pertandingan'      => $pertandingan,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers'        => $allPlayers,
            'allSides'          => $allSides,
            'cachedSide'        => $cachedSide,
        ]);
    }

    public function kirim_penalti_ganda(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'penalty_id'      => 'required|string',
            'type'            => 'required|string',
            'value'           => 'required|numeric',
            'action'          => 'required|string|in:add,clear',
            'side'            => 'nullable|integer|min:1',  // pemasalan: bisa > 2
        ]);

        try {
            $realtimeService = new \App\Services\RealtimeService();

            if ($validatedData['action'] === 'add') {
                $realtimeService->addPenalty(
                    $validatedData['pertandingan_id'],
                    $validatedData['penalty_id'],
                    $validatedData['type'],
                    $validatedData['value'],
                    $validatedData['side'] ?? '1'
                );
            } else {
                $realtimeService->clearPenalty(
                    $validatedData['pertandingan_id'],
                    $validatedData['penalty_id']
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Penalti berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate penalti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function switch_active_side_ganda(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'side'            => 'required|string',
        ]);

        $pertandinganId = $validatedData['pertandingan_id'];
        $side = $validatedData['side'];

        // Store in cache for 24 hours so latecomers can get it
        \Illuminate\Support\Facades\Cache::put("active_side_seni_ganda_{$pertandinganId}", $side, 86400);

        // Broadcast event reusing the same event used by tunggal/regu
        broadcast(new \App\Events\ActiveSideChangedSeni($pertandinganId, $side))->toOthers();

        return response()->json([
            'status' => 'success',
            'side' => $side
        ]);
    }

    /**
     * Request validation from juris for jatuhan or pelanggaran
     */
    public function requestValidation(Request $request)
    {
        $pertandinganId = $request->input('pertandingan_id');
        $type = $request->input('validation_type'); // 'jatuhan' or 'pelanggaran'
        $team = $request->input('team'); // 'blue' or 'red'

        $tandingMatch = TandingMatch::firstOrCreate(
            ['pertandingan_id' => $pertandinganId],
            [
                'current_round' => 1,
                'match_status' => 'in_progress',
                'started_at' => now()
            ]
        );

        $validationRequest = ValidationRequest::create([
            'tanding_match_id' => $tandingMatch->id,
            'requested_by' => auth()->user()->id,
            'validation_type' => $type,
            'team' => $team,
            'description' => ucfirst($type) . ' - Team ' . ucfirst($team),
            'status' => 'pending'
        ]);

        // Broadcast to all juris
        broadcast(new ValidationRequestSent([
            'validation_request_id' => $validationRequest->id,
            'pertandingan_id' => $pertandinganId,
            'validation_type' => $type,
            'team' => $team,
            'description' => $validationRequest->description
        ]));

        return response()->json([
            'status' => 'sent',
            'request_id' => $validationRequest->id
        ]);
    }

    /**
     * Get last completed validation for a match
     */
    public function getLastValidation($pertandinganId)
    {
        $tandingMatch = TandingMatch::where('pertandingan_id', $pertandinganId)->first();

        if (!$tandingMatch) {
            return response()->json(null);
        }

        $lastValidation = ValidationRequest::where('tanding_match_id', $tandingMatch->id)
            ->where('status', 'completed')
            ->with('votes.juri')
            ->latest()
            ->first();

        return response()->json($lastValidation);
    }

    /**
     * Get current penalty counts for a match (for frontend to restore state on refresh)
     * Returns counts per team: { blue: { bina: 1, teguran: 0, peringatan: 2 }, red: { ... } }
     */
    public function getPenaltyCounts($pertandinganId)
    {
        $tandingMatch = TandingMatch::where('pertandingan_id', $pertandinganId)->first();

        $empty = ['bina' => 0, 'teguran' => 0, 'peringatan' => 0];

        if (!$tandingMatch) {
            return response()->json(['blue' => $empty, 'red' => $empty]);
        }

        $counts = ['blue' => $empty, 'red' => $empty];

        foreach (['blue', 'red'] as $team) {
            foreach (['bina', 'teguran', 'peringatan'] as $type) {
                $counts[$team][$type] = TandingPenalty::where('tanding_match_id', $tandingMatch->id)
                    ->where('team', $team)
                    ->where('penalty_type', $type)
                    ->count();
            }
        }

        return response()->json($counts);
    }

    /**
     * Get penalty counts per round per team for score-value boxes display.
     * GET /dewan-tanding/penalty-counts-per-round/{id}
     *
     * Bina, teguran, jatuhan → per-round (reset each round)
     * Peringatan             → cumulative (running total across rounds)
     *
     * Returns:
     * {
     *   blue: { 1: {bina:1, teguran:0, peringatan:0, jatuhan:2}, 2: {...}, 3: {...} },
     *   red:  { ... }
     * }
     */
    public function getPenaltyCountsPerRound($pertandinganId)
    {
        $tandingMatch = TandingMatch::where('pertandingan_id', $pertandinganId)->first();

        // Determine max rounds based on jenis_pertandingan
        $pertandingan = Pertandingan::find($pertandinganId);
        $maxRonde = $pertandingan ? $pertandingan->maxRonde() : 3;

        $emptyRound  = ['bina' => 0, 'teguran' => 0, 'peringatan' => 0, 'jatuhan' => 0];
        $emptyResult = ['blue' => [], 'red' => []];
        for ($r = 1; $r <= $maxRonde; $r++) {
            $emptyResult['blue'][(string)$r] = $emptyRound;
            $emptyResult['red'][(string)$r]  = $emptyRound;
        }

        if (!$tandingMatch) {
            return response()->json($emptyResult);
        }

        $penalties = TandingPenalty::where('tanding_match_id', $tandingMatch->id)->get();

        $result = $emptyResult;

        // Types that reset per round
        $perRoundTypes = ['bina', 'teguran', 'jatuhan'];

        foreach ($penalties as $p) {
            $team  = $p->team;
            $type  = $p->penalty_type;
            $round = (string)($p->round ?? 1);

            if (!isset($result[$team][$round])) continue;

            if (in_array($type, $perRoundTypes)) {
                // Count only in the round it occurred
                $result[$team][$round][$type]++;
            } else {
                // Peringatan: cumulative — add to current round AND all subsequent rounds
                for ($r = (int)$round; $r <= $maxRonde; $r++) {
                    $result[$team][(string)$r][$type]++;
                }
            }
        }

        return response()->json($result);
    }

    /**
     * Set the winner of the pertandingan and advance them to the next match.
     */
    public function setWinner(Request $request)
    {
        $validated = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'winner_id'       => 'required|integer|exists:pertandingan_player,id',
        ]);

        try {
            DB::beginTransaction();

            $pertandingan = Pertandingan::with('players')->findOrFail($validated['pertandingan_id']);
            
            // Validate that the winner is indeed a player of this match
            $winnerPlayer = $pertandingan->players->firstWhere('id', $validated['winner_id']);
            if (!$winnerPlayer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemain yang dipilih tidak terdaftar di pertandingan ini.'
                ], 400);
            }

            // Save winner and update status
            $pertandingan->update([
                'winner_id' => $validated['winner_id'],
                'status'    => 'selesai'
            ]);

            // Broadcast MatchStatusChanged to notify other clients in the arena
            broadcast(new \App\Events\MatchStatusChanged($pertandingan->arena_id, $pertandingan->id, 'selesai'))->toOthers();


            // If there's a next match, advance the winner
            if ($pertandingan->next_match_id) {
                $nextMatch = Pertandingan::find($pertandingan->next_match_id);
                if ($nextMatch) {
                    // Check if player is already advanced to next match
                    $alreadyExists = $nextMatch->players()
                        ->where('player_name', $winnerPlayer->player_name)
                        ->where('player_contingent', $winnerPlayer->player_contingent)
                        ->exists();

                    if (!$alreadyExists) {
                        $existingCount = $nextMatch->players()->count();
                        
                        // If count is 0, side_number = 1. If 1, side_number = 2.
                        // If somehow >= 2, we fallback to 2 or ignore.
                        if ($existingCount < 2) {
                            $sideNumber = ($existingCount === 0) ? 1 : 2;

                            $nextMatch->players()->create([
                                'player_name'       => $winnerPlayer->player_name,
                                'player_contingent' => $winnerPlayer->player_contingent,
                                'side_number'       => $sideNumber,
                                'total_score'       => 0,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pemenang berhasil ditentukan dan diloloskan ke babak berikutnya.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menentukan pemenang: ' . $e->getMessage()
            ], 500);
        }
    }
}

