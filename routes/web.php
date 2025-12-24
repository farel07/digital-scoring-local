<?php

use Illuminate\Support\Facades\Route;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\juriController;
use App\Http\Controllers\dewanOperatorController;
use App\Http\Controllers\dewanController;
use App\Http\Controllers\penilaianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-notif', function () {
    broadcast(new NewNotificationEvent("Hallo! Ada notifikasi baru!"));
    return "Notifikasi berhasil dikirim!";
});

Route::get('/juri/{id}', [juriController::class, 'index']);
Route::post('/juri/{id}', [juriController::class, 'kirim_poin_seni_tunggal_regu']);

Route::get('/dewan/{id}', [dewanController::class, 'index']);
Route::post('/dewan/kirim-penalti', [DewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);
// Route::post('/dewan/{id}', [dewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);

Route::get('/penilaian/{id}', [penilaianController::class, 'index']);

Route::get('/dewan-operator/{id}', [dewanOperatorController::class, 'index']);