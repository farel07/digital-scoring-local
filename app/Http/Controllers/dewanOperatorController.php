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
        // Check if mode=user is passed
        $mode = request()->query('mode');

        if ($mode === 'user') {
            // $id is user_id, get their active match
            $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($id);

            if (!$pertandingan) {
                return response()->view('errors.no-active-match', [
                    'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
                ], 404);
            }

            $id = $pertandingan->id; // Use match_id for the rest
        }

        // $id is now match_id
        $jumlahJuri = request()->query('jumlah', 4);

        return view('seni.ganda.dewanOperator', [
            'id' => $id,
            'jumlahJuri' => $jumlahJuri
        ]);
    }

    public function index_tunggal_regu($id)
    {
        // Check if mode=user is passed
        $mode = request()->query('mode');

        if ($mode === 'user') {
            // $id is user_id, get their active match
            $pertandingan = \App\Helpers\MatchResolver::getActiveMatchForUser($id);

            if (!$pertandingan) {
                return response()->view('errors.no-active-match', [
                    'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
                ], 404);
            }

            $id = $pertandingan->id; // Use match_id for the rest
        }

        // $id is now match_id
        $pertandingan = \App\Models\Pertandingan::with('kelas')->find($id);
        $jumlahJuri = request()->query('jumlah', 4);
        $maxJurus = $pertandingan->kelas->jenis_pertandingan === 'tunggal' ? 14 : 12;

        return view('seni.tunggal_regu.dewanOperator', [
            'id' => $id,
            'jumlahJuri' => $jumlahJuri,
            'maxJurus' => $maxJurus,
            'matchType' => $pertandingan->kelas->jenis_pertandingan
        ]);
    }
}
