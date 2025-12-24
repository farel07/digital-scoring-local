<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class penilaianController extends Controller
{
    public function index($id)
    {
        return view('tanding.penilaian', ['id' => $id]);
    }
}
