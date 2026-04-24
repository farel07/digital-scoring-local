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

        $matchId    = $pertandingan->id;
        $jumlahJuri = request()->query('jumlah', 4);

        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        return view('seni.ganda.dewanOperator', [
            'id'                => $matchId,
            'jumlahJuri'        => $jumlahJuri,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers'        => $allPlayers,
            'allSides'          => $allSides,
        ]);
    }

    public function index_ganda_penonton($id)
    {
        // Same pattern as index_ganda: $id is user_id, resolve to active match
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($id);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $matchId    = $pertandingan->id;
        $jumlahJuri = request()->query('jumlah', 4);

        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        return view('seni.ganda.penonton', [
            'id'                => $matchId,
            'jumlahJuri'        => $jumlahJuri,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers'        => $allPlayers,
            'allSides'          => $allSides,
        ]);
    }

    /**
     * Halaman hasil dual-panel Seni Ganda
     * Menampilkan skor Sudut Biru vs Sudut Merah secara berdampingan
     * dengan indikator pemenang otomatis
     */
    public function index_ganda_hasil($id)
    {
        $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($id);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $matchId    = $pertandingan->id;
        $jumlahJuri = request()->query('jumlah', 4);

        return view('seni.ganda.hasil', [
            'id'           => $matchId,
            'pertandingan' => $pertandingan,
            'jumlahJuri'   => $jumlahJuri,
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
        $matchType = $pertandingan->kelas->jenis_pertandingan;
        $maxJurus = $matchType === 'tunggal' ? 14 : 12;

        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();

        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        return view('seni.tunggal_regu.dewanOperator', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'jumlahJuri' => $jumlahJuri,
            'maxJurus' => $maxJurus,
            'matchType' => $matchType,
            'penaltyRules' => $penaltyRules,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers' => $allPlayers,
            'allSides' => $allSides
        ]);
    }

    public function index_tunggal_regu_penonton($userId)
    {
        // Same pattern as dewan operator: resolve user_id to active match
        $pertandingan = MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = User::find($userId);
        $jumlahJuri = request()->query('jumlah', 4);

        // Determine max jurus based on match type
        $matchType = $pertandingan->kelas->jenis_pertandingan;
        $maxJurus = $matchType === 'tunggal' ? 14 : 12;

        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();

        $jenisPertandingan = $pertandingan->jenis_pertandingan ?? 'prestasi';
        $pertandingan->load('players');
        $allPlayers = $pertandingan->players->groupBy('side_number');
        $allSides   = $allPlayers->keys()->sort()->values();

        return view('seni.tunggal_regu.penonton', [
            'id' => $pertandingan->id,
            'user' => $user,
            'pertandingan' => $pertandingan,
            'jumlahJuri' => $jumlahJuri,
            'maxJurus' => $maxJurus,
            'matchType' => $matchType,
            'penaltyRules' => $penaltyRules,
            'jenisPertandingan' => $jenisPertandingan,
            'allPlayers' => $allPlayers,
            'allSides' => $allSides
        ]);
    }

    /**
     * Halaman hasil dual-panel Seni Tunggal/Regu
     * Menampilkan skor Sudut Biru vs Sudut Merah secara berdampingan
     * dengan indikator pemenang otomatis
     */
    public function index_tunggal_regu_hasil($userId)
    {
        $pertandingan = MatchResolver::getActiveMatchForUser($userId);

        if (!$pertandingan) {
            return response()->view('errors.no-active-match', [
                'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
            ], 404);
        }

        $user = User::find($userId);
        $jumlahJuri = request()->query('jumlah', 4);

        $matchType = $pertandingan->kelas->jenis_pertandingan;
        $maxJurus  = $matchType === 'tunggal' ? 14 : 12;

        $penaltyRules = \App\Models\PenaltyRule::where('category', 'tunggal_regu')->get();

        return view('seni.tunggal_regu.hasil', [
            'id'           => $pertandingan->id,
            'user'         => $user,
            'pertandingan' => $pertandingan,
            'jumlahJuri'   => $jumlahJuri,
            'maxJurus'     => $maxJurus,
            'matchType'    => $matchType,
            'penaltyRules' => $penaltyRules,
        ]);
    }
}
