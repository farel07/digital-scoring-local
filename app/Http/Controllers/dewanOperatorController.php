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
    public function index_ganda($id, Request $request)
    {
        $jumlahJuri = $request->query('jumlah', 4);

        // Check if this is a user_id or match_id
        // If there's a 'mode' query param set to 'user', treat as user_id
        if ($request->query('mode') === 'user') {
            $pertandingan = MatchResolver::getActiveMatchForUser($id);

            if (!$pertandingan) {
                return response()->view('errors.no-active-match', [
                    'message' => 'Tidak ada pertandingan yang sedang berlangsung di arena Anda.'
                ], 404);
            }

            $matchId = $pertandingan->id;
        } else {
            // Treat as match_id (default behavior for backward compatibility)
            $matchId = $id;
        }

        return view('seni.ganda.dewanOperator', [
            'id' => $matchId,
            'jumlahJuri' => $jumlahJuri
        ]);
    }
}
