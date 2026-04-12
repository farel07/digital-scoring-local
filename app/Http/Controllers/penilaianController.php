<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pertandingan;
use App\Models\TandingMatch;

class penilaianController extends Controller
{
    public function index($id)
    {
        $pertandingan = Pertandingan::findOrFail($id);

        // Get or create tanding match
        $tandingMatch = TandingMatch::firstOrCreate(
            ['pertandingan_id' => $id],
            [
                'current_round' => 1,
                'match_status' => 'not_started'
            ]
        );

        // Get all validated scores
        $scores = $tandingMatch->scores()
            ->where('status', 'sah')
            ->with('judge')
            ->orderBy('created_at')
            ->get();

        // Get all penalties
        $penalties = $tandingMatch->penalties()
            ->orderBy('created_at')
            ->get();

        // Calculate technique statistics (count of each technique per team)
        // Filter points > 0 to exclude juri2 confirmation records (points=0)
        $techniqueStats = [
            'blue' => [
                'pukul' => $tandingMatch->scores()
                    ->where('status', 'sah')
                    ->where('points', '>', 0)
                    ->where('team', 'blue')
                    ->where('technique', 'PUKUL')
                    ->count(),
                'tendang' => $tandingMatch->scores()
                    ->where('status', 'sah')
                    ->where('points', '>', 0)
                    ->where('team', 'blue')
                    ->where('technique', 'TENDANG')
                    ->count(),
            ],
            'red' => [
                'pukul' => $tandingMatch->scores()
                    ->where('status', 'sah')
                    ->where('points', '>', 0)
                    ->where('team', 'red')
                    ->where('technique', 'PUKUL')
                    ->count(),
                'tendang' => $tandingMatch->scores()
                    ->where('status', 'sah')
                    ->where('points', '>', 0)
                    ->where('team', 'red')
                    ->where('technique', 'TENDANG')
                    ->count(),
            ],
        ];

        return view('tanding.penilaian', [
            'id'                  => $id,
            'pertandingan'        => $pertandingan,
            'tandingMatch'        => $tandingMatch,
            'scores'              => $scores,
            'penalties'           => $penalties,
            'techniqueStats'      => $techniqueStats,
            'jenis_pertandingan'  => $pertandingan->jenis_pertandingan ?? 'prestasi',
            'max_ronde'           => $pertandingan->maxRonde(),
            'playerBlue'          => $pertandingan->players()->where('side_number', 1)->first(),
            'playerRed'           => $pertandingan->players()->where('side_number', 2)->first(),
        ]);
    }
}
