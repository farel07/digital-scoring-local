<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dewanOperatorController extends Controller
{
    //
    public function index($id)
    {
        return view('seni.tunggal_regu.dewanOperator', ['id' => $id, 'jumlahJuri' => 4]);
    }
}
