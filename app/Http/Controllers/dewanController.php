<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\KirimPenalti;

class dewanController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.dewan', ['id' => $id]);
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
}
