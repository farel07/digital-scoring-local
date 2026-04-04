<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertandingan;
use App\Models\TandingMatch;
use App\Events\TimerUpdated;
use App\Helpers\MatchResolver;

class timerController extends Controller
{
    /**
     * Display timer page for a specific pertandingan
     */
    public function index($userId)
    {
        // Get active match for this timer user
        $pertandingan = MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return view('tanding.timer', [
                'error' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.',
                'pertandingan' => null,
                'arena' => null,
                'peserta1' => null,
                'peserta2' => null,
                'kelasInfo' => null,
                'roundName' => null
            ]);
        }

        // Get user's arena
        $user = \App\Models\User::find($userId);
        $arena = $user->arenas()->first();

        // Get match participants
        // side_number: 1 = Blue (Biru), 2 = Red (Merah)
        $peserta1 = $pertandingan->players()
            ->where('side_number', 2) // Red/Merah
            ->first();

        $peserta2 = $pertandingan->players()
            ->where('side_number', 1) // Blue/Biru
            ->first();

        // Get match class info
        $kelasInfo = $pertandingan->kelasPertandingan;

        // Determine round name based on babak
        $roundNames = [
            'babak_1' => '1/128',
            'babak_2' => '1/64',
            'babak_3' => '1/32',
            'babak_4' => '1/16',
            'babak_5' => '1/8',
            'perempat_final' => '1/4',
            'semi_final' => '1/2',
            'final' => 'Final'
        ];

        $roundName = $roundNames[$pertandingan->babak] ?? strtoupper(str_replace('_', ' ', $pertandingan->babak));

        return view('tanding.timer', [
            'pertandingan' => $pertandingan,
            'arena' => $arena,
            'peserta1' => $peserta1,
            'peserta2' => $peserta2,
            'kelasInfo' => $kelasInfo,
            'roundName' => $roundName,
            'user' => $user
        ]);
    }

    /**
     * Broadcast timer state to all connected clients
     */
    public function broadcastTimer(Request $request)
    {
        $pertandinganId = $request->input('pertandingan_id');
        $state = $request->input('state'); // 'playing', 'paused', 'reset'
        $currentTime = $request->input('current_time');
        $totalDuration = $request->input('total_duration');

        // Broadcast timer update
        broadcast(new TimerUpdated([
            'pertandingan_id' => $pertandinganId,
            'state' => $state,
            'current_time' => $currentTime,
            'total_duration' => $totalDuration,
            'timestamp' => now()->timestamp
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Timer state broadcasted'
        ]);
    }

    /**
     * Update current round and broadcast to all clients
     */
    public function updateRound(Request $request)
    {
        $pertandinganId = $request->input('pertandingan_id');
        $roundNumber = $request->input('round_number');

        // Find the tanding match
        $tandingMatch = TandingMatch::where('pertandingan_id', $pertandinganId)->first();

        if (!$tandingMatch) {
            // Create if not exists
            $tandingMatch = TandingMatch::create([
                'pertandingan_id' => $pertandinganId,
                'current_round' => $roundNumber,
                'match_status' => 'in_progress',
                'started_at' => now()
            ]);
        } else {
            // Update round
            $tandingMatch->update([
                'current_round' => $roundNumber
            ]);
        }

        // Broadcast round change
        broadcast(new TimerUpdated([
            'pertandingan_id' => $pertandinganId,
            'state' => 'round_changed',
            'current_round' => $roundNumber,
            'timestamp' => now()->timestamp
        ]));

        return response()->json([
            'status' => 'success',
            'message' => "Round updated to {$roundNumber}",
            'current_round' => $roundNumber
        ]);
    }
}
