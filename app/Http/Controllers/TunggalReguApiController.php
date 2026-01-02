<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TunggalReguService;

class TunggalReguApiController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new TunggalReguService();
    }

    /**
     * Get events for a tunggal/regu match
     * 
     * @param int $matchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvents($matchId)
    {
        try {
            $data = $this->service->getMatchData($matchId);

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
