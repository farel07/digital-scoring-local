<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertandingan;
use App\Models\Arena;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    /**
     * Show the static operator page
     * Displays match schedule in a static (read-only) view
     */
    public function index($user_id = null)
    {
        // Get user arena assignment if user_id is provided
        $arena = null;
        $daftar_pertandingan = collect();

        if ($user_id) {
            $user = \App\Models\User::find($user_id);

            if ($user && $user->arena) {
                $arena = $user->arena;

                // Get all matches for this arena
                $daftar_pertandingan = Pertandingan::where('arena_id', $arena->id)
                    ->with([
                        'kelasPertandingan.kelas',
                        'kelasPertandingan.kategoriPertandingan',
                        'kelasPertandingan.jenisPertandingan',
                        'pemain_unit_1.player.contingent',
                        'pemain_unit_2.player.contingent'
                    ])
                    ->orderBy('round_number')
                    ->orderBy('match_number')
                    ->get();
            }
        } else {
            // If no user_id provided, get all matches
            $daftar_pertandingan = Pertandingan::with([
                'kelasPertandingan.kelas',
                'kelasPertandingan.kategoriPertandingan',
                'kelasPertandingan.jenisPertandingan',
                'pemain_unit_1.player.contingent',
                'pemain_unit_2.player.contingent'
            ])
                ->orderBy('round_number')
                ->orderBy('match_number')
                ->get();
        }

        return view('operator-static', [
            'daftar_pertandingan' => $daftar_pertandingan,
            'arena' => $arena,
            'user_id' => $user_id
        ]);
    }
}
