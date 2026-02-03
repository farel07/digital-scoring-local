<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\KirimPenalti;
use App\Events\KirimPenaltiTanding;
use App\Events\ValidationRequestSent;
use App\Models\TandingMatch;
use App\Models\TandingPenalty;
use App\Models\User;
use App\Models\ValidationRequest;

class dewanController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.dewan', ['id' => $id]);
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

        return view('seni.tunggal_regu.dewan', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan
        ]);
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

        return view('tanding.dewan', ['id' => $id]);
    }

    public function kirim_pelanggaran_seni_tunggal_regu(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'penalty_id' => 'required|string',
            'value' => 'required|numeric',
        ]);

        // Persist penalty to database
        if ($validatedData['value'] == 0) {
            // Clear penalty (set status to 'cleared' or delete)
            // Find the most recent active penalty with this type
            $penalty = \App\Models\Penalty::where('pertandingan_id', $validatedData['pertandingan_id'])
                ->where('type', $validatedData['penalty_id'])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($penalty) {
                $penalty->update(['status' => 'cleared']);
            }
        } else {
            // Add penalty - create new entry with unique ID
            // Append timestamp to penalty_id for uniqueness
            $uniquePenaltyId = $validatedData['penalty_id'] . '_' . time() . '_' . rand(1000, 9999);

            \App\Models\Penalty::create([
                'pertandingan_id' => $validatedData['pertandingan_id'],
                'penalty_id' => $uniquePenaltyId,
                'type' => $validatedData['penalty_id'], // Original type for grouping
                'value' => $validatedData['value'],
                'status' => 'active',
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
            'penalty_id' => 'required|',
            'value' => 'required',
            'filter' => 'required|string'
        ]);

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
                'match_status' => 'in_progress',
                'started_at' => now()
            ]
        );

        // Save penalty to database
        TandingPenalty::create([
            'tanding_match_id' => $tandingMatch->id,
            'team' => $validatedData['filter'],
            'penalty_type' => $validatedData['penalty_id'],
            'penalty_value' => $validatedData['value'],
            'point_deduction' => $pointDeduction,
            'round' => $tandingMatch->current_round,
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
            'data' => $validatedData
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
            'action' => 'required|string|in:add,clear'
        ]);

        try {
            $realtimeService = new \App\Services\RealtimeService();

            if ($validatedData['action'] === 'add') {
                $realtimeService->addPenalty(
                    $validatedData['pertandingan_id'],
                    $validatedData['penalty_id'],
                    $validatedData['type'],
                    $validatedData['value']
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
}
