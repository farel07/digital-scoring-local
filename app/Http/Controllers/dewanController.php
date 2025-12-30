<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\KirimPenalti;
use App\Events\KirimPenaltiTanding;

class dewanController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.dewan', ['id' => $id]);
    }

    public function tanding_index($id)
    {
        return view('tanding.dewan', ['id' => $id]);
    }

    public function kirim_pelanggaran_seni_tunggal_regu(Request $request)
    {
        $validatedData = $request->validate([
            'pertandingan_id' => 'required|integer',
            'penalty_id' => 'required|string',
            'value' => 'required|numeric',
        ]);

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

        broadcast(new KirimPenaltiTanding($validatedData))->toOthers();

        return response()->json(['status' => 'success', 'data' => $validatedData]);
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
}
