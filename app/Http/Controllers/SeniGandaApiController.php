<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RealtimeService;

class SeniGandaApiController extends Controller
{
    protected $realtimeService;

    public function __construct()
    {
        $this->realtimeService = new RealtimeService();
    }

    /**
     * Get events for a match (judges scores and penalties)
     * 
     * @param int $matchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvents($matchId)
    {
        try {
            $data = $this->realtimeService->getMatchData($matchId);

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pertandingan: ' . $e->getMessage()
            ], 500);
        }
    }
}
