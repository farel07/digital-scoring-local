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
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = \App\Models\User::find($userId);
        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();

        return view('seni.tunggal_regu.dewan', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'penaltyRules' => $penaltyRules
        ]);
    }

    public function tanding_index($id)
    {
        $pertandingan = Pertandingan::findOrFail($id);

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
            'id' => $id,
            'playerBlue'     => $pertandingan->players()->where('side_number', 1)->first(), // side_number 1 = Blue
            'playerRed'      => $pertandingan->players()->where('side_number', 2)->first(), // side_number 2 = Red 
        ]);
    }

    public function kirim_pelanggaran_seni_tunggal_regu(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'penalty_id' => 'required|string',
            'value' => 'required|numeric',
            'side' => 'nullable|in:1,2'
        ]);

        $side = $validatedData['side'] ?? '1';
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

        return view('seni.ganda.dewan', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan
        ]);
    }

    public function kirim_penalti_ganda(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer|exists:pertandingan,id',
            'penalty_id' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric',
            'action' => 'required|string|in:add,clear',
            'side' => 'nullable|in:1,2'
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

        $emptyRound   = ['bina' => 0, 'teguran' => 0, 'peringatan' => 0, 'jatuhan' => 0];
        $emptyResult  = [
            'blue' => ['1' => $emptyRound, '2' => $emptyRound, '3' => $emptyRound],
            'red'  => ['1' => $emptyRound, '2' => $emptyRound, '3' => $emptyRound],
        ];

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
                for ($r = (int)$round; $r <= 3; $r++) {
                    $result[$team][(string)$r][$type]++;
                }
            }
        }

        return response()->json($result);
    }
}

