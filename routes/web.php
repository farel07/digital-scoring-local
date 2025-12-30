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

// Route::post('/dewan/{id}', [dewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);


Route::get('/penilaian/{id}', [penilaianController::class, 'index']);
Route::get('/dewan-operator/{id}', [dewanOperatorController::class, 'index']);
Route::post('/dewan/kirim-penalti', [dewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);

// route tanding
Route::get('/dewan-tanding/{id}', [dewanController::class, 'tanding_index']);
Route::get('/juri-tanding/{id}', [juriController::class, 'tanding_index']);
Route::post('/juri-tanding/kirim-poin', [juriController::class, 'kirimPoin']);
Route::post('/dewan/kirim-penalti-tanding', [DewanController::class, 'kirim_penalti_tanding']);


// route seni ganda 
route::get('/juri-seni-ganda/{id}', [juriController::class, 'index_ganda']);
route::post('/juri-seni-ganda/kirim-poin', [juriController::class, 'kirim_poin_ganda']);

Route::get('/dewan-seni-ganda/{id}', [dewanController::class, 'index_ganda']);
Route::post('/dewan-seni-ganda/kirim-penalti', [dewanController::class, 'kirim_penalti_ganda']);

Route::get('/dewan-operator-seni-ganda/{id}', [dewanOperatorController::class, 'index_ganda']);

// API route for getting events (realtime simulation)
Route::get('/api/seni/ganda/events/{matchId}', [App\Http\Controllers\SeniGandaApiController::class, 'getEvents']);


// route testing listen
Route::get('/test-listen', function () {
    return view('testListen');
});
