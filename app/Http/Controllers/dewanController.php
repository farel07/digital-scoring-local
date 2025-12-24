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
}
