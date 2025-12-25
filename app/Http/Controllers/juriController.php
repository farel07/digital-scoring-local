<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\KirimPoinSeniTR;
use App\Events\KirimPoinTanding;
use Illuminate\Support\Facades\Cache; // Jangan lupa import ini
use Illuminate\Support\Facades\Log; // Import Log



class juriController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.juri', ['id' => $id]);
    }

    public function kirim_poin_seni_tunggal_regu(Request $request)
    {
        $poin = $request->input('poin');
        $filter = $request->input('filter');
        $pertandingan_id = $request->input('pertandingan_id');
        $type = $request->input('type');
        $role = $request->input('role');

       broadcast(new KirimPoinSeniTR($poin, $filter, $pertandingan_id, $type, $role));
        return response()->json(['status' => 'success']);
    }

    public function tanding_index($id)
    {
        return view('tanding.juri', ['id' => $id]);
    }

    public function kirimPoin(Request $request)
{
    $pertandinganId = $request->input('pertandingan_id');
    $filter = $request->input('filter'); 
    $type = $request->input('type');
    $juriId = $request->input('juri_id'); 
    $poinValue = $request->input('poin');

    $cacheKey = "tanding_{$pertandinganId}_{$filter}_{$type}";
    $pendingVote = Cache::get($cacheKey);

    if (!$pendingVote) {
        // --- KASUS A: Orang Pertama ---
        
        Cache::put($cacheKey, [
            'first_juri_id' => $juriId,
            'poin' => $poinValue
        ], now()->addSeconds(3));

        // 1. BROADCAST INPUT (Agar lampu juri di layar menyala)
        broadcast(new KirimPoinTanding([
            'type' => $type,
            'poin' => $poinValue,
            'pertandingan_id' => $pertandinganId,
            'filter' => $filter,
            'juri_id' => $juriId,
            'status' => 'input' // <--- Status Input
        ]));

        return response()->json(['status' => 'waiting']);

    } else {
        // --- KASUS B: Orang Kedua (Validasi) ---
        
        if ($pendingVote['first_juri_id'] != $juriId) {
            
            // 1. BROADCAST INPUT JURI KEDUA (Agar lampu juri ini juga menyala)
            broadcast(new KirimPoinTanding([
                'type' => $type,
                'poin' => $poinValue,
                'pertandingan_id' => $pertandinganId,
                'filter' => $filter,
                'juri_id' => $juriId,
                'status' => 'input' // <--- Status Input
            ]));

            // 2. BROADCAST POIN SAH (Agar angka skor bertambah)
            broadcast(new KirimPoinTanding([
                'type' => $type,
                'poin' => $pendingVote['poin'], // Pakai poin dari cache agar konsisten
                'pertandingan_id' => $pertandinganId,
                'filter' => $filter,
                'juri_id' => $juriId, // Bisa dikosongkan atau diisi salah satu
                'status' => 'sah' // <--- Status Sah
            ]));

            Cache::forget($cacheKey);
            return response()->json(['status' => 'valid']);
        } 
        
        return response()->json(['status' => 'ignored']);
    }
}

}
