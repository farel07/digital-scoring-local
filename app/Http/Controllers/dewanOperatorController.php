<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MatchResolver;
use App\Models\User;

class dewanOperatorController extends Controller
{
    /**
     * Show the dewan operator page for seni ganda
     * Can be accessed by match_id or user_id
     * 
     * @param int $id (can be match_id or user_id depending on context)
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index_ganda($id)
    {
        // $id is ALWAYS user_id, resolve to active match
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($id);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        // Get match_id from resolved pertandingan
        $matchId = $pertandingan->id;

        // Get jumlah juri from query parameter (default 4)
        $jumlahJuri = request()->query('jumlah', 4);

        return view('seni.ganda.dewanOperator', [
            'id' => $matchId,
            'jumlahJuri' => $jumlahJuri
        ]);
    }

    public function index_tunggal_regu($userId)
    {
        // Get active match for this user (same pattern as juri)
        $pertandingan = MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = User::find($userId);
        $jumlahJuri = request()->query('jumlah', 4);

        // Determine max jurus based on match type
        $maxJurus = $pertandingan->kelas->jenis_pertandingan === 'tunggal' ? 14 : 12;

        return view('seni.tunggal_regu.dewanOperator', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'jumlahJuri' => $jumlahJuri,
            'maxJurus' => $maxJurus,
            'matchType' => $pertandingan->kelas->jenis_pertandingan
        ]);
    }
}
