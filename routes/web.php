<?php

use Illuminate\Support\Facades\Route;
use App\Events\NewNotificationEvent;
use App\Http\Controllers\juriController;
use App\Http\Controllers\dewanOperatorController;
use App\Http\Controllers\dewanController;
use App\Http\Controllers\penilaianController;
use App\Http\Controllers\timerController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\AuthController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/api/active-match-url', [AuthController::class, 'getActiveMatchUrl']);

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/superadmin', function () {
    return view('superadmin.superadmin');
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

// Route untuk halaman operator dashboard
Route::get('/operator/dashboard', [OperatorController::class, 'index'])->name('operator.dashboard');
Route::post('/operator/update-status', [OperatorController::class, 'updateStatus'])->name('operator.updateStatus');

// route tanding
Route::get('/dewan-tanding/{id}', [dewanController::class, 'tanding_index']);
Route::get('/juri-tanding/score-log', [juriController::class, 'getJuriScoreLog']); // ← must be ABOVE /{id}
Route::get('/juri-tanding/{id}', [juriController::class, 'tanding_index']);
Route::post('/juri-tanding/kirim-poin', [juriController::class, 'kirimPoin']);
Route::post('/dewan/kirim-penalti-tanding', [dewanController::class, 'kirim_penalti_tanding']);

// Validation request routes
Route::post('/dewan-tanding/request-validation', [dewanController::class, 'requestValidation']);
Route::get('/dewan-tanding/last-validation/{id}', [dewanController::class, 'getLastValidation']);
Route::get('/dewan-tanding/penalty-counts/{id}', [dewanController::class, 'getPenaltyCounts']);
Route::get('/dewan-tanding/penalty-counts-per-round/{id}', [dewanController::class, 'getPenaltyCountsPerRound']);
Route::post('/juri-tanding/submit-validation-vote', [juriController::class, 'submitValidationVote']);

// Timer Tanding Routes
Route::get('/timer-tanding/{userId}', [timerController::class, 'index'])->name('timer.index');
Route::post('/timer-tanding/broadcast', [timerController::class, 'broadcastTimer'])->name('timer.broadcast');
Route::post('/timer-tanding/update-round', [timerController::class, 'updateRound'])->name('timer.updateRound');


// route seni ganda 
route::get('/juri-seni-ganda/{id}', [juriController::class, 'index_ganda']);
route::post('/juri-seni-ganda/kirim-poin', [juriController::class, 'kirim_poin_ganda']);

Route::get('/dewan-seni-ganda/{id}', [dewanController::class, 'index_ganda']);
Route::post('/dewan-seni-ganda/kirim-penalti', [dewanController::class, 'kirim_penalti_ganda']);

Route::get('/dewan-operator-seni-ganda/{id}', [dewanOperatorController::class, 'index_ganda']);

// Route for spectators/viewers (penonton) - same pattern as dewanOperator
Route::get('/penonton-seni-ganda/{id}', [dewanOperatorController::class, 'index_ganda_penonton']);

// API route for getting events (realtime simulation)
Route::get('/api/seni/ganda/events/{matchId}', [App\Http\Controllers\SeniGandaApiController::class, 'getEvents']);

// route seni tunggal/regu
Route::get('/juri-seni-tunggal-regu/{user_id}', [juriController::class, 'index_tunggal_regu']);
Route::post('/seni/tunggal-regu/add-error', [juriController::class, 'addMoveError']);
Route::post('/seni/tunggal-regu/set-category', [juriController::class, 'setCategoryScore']);

Route::get('/dewan-seni-tunggal-regu/{user_id}', [dewanController::class, 'index_tunggal_regu']);
Route::post('/dewan-seni-tunggal-regu/kirim-penalti', [dewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);

// API route for tunggal/regu events
Route::get('/api/seni/tunggal-regu/events/{matchId}', [App\Http\Controllers\TunggalReguApiController::class, 'getEvents']);

// Operator view for tunggal/regu
Route::get('/dewan-operator-seni-tunggal-regu/{id}', [dewanOperatorController::class, 'index_tunggal_regu']);

// Route for spectators/viewers (penonton) tunggal-regu - same pattern as dewan operator
Route::get('/penonton-seni-tunggal-regu/{id}', [dewanOperatorController::class, 'index_tunggal_regu_penonton']);

// Penalty submission for tunggal/regu
Route::post('/dewan-seni-tunggal-regu/kirim-penalti', [dewanController::class, 'kirim_pelanggaran_seni_tunggal_regu']);


// ==================== SUPERADMIN ROUTES ====================

// Superadmin Dashboard
Route::get('/superadmin', [SuperadminController::class, 'index']);

// Arena Management API
Route::get('/api/superadmin/arenas', [SuperadminController::class, 'getArenas']);
Route::post('/api/superadmin/arenas', [SuperadminController::class, 'createArena']);
Route::put('/api/superadmin/arenas/{id}', [SuperadminController::class, 'updateArena']);
Route::delete('/api/superadmin/arenas/{id}', [SuperadminController::class, 'deleteArena']);

// User Management API
Route::get('/api/superadmin/users', [SuperadminController::class, 'getUsers']);
Route::post('/api/superadmin/users', [SuperadminController::class, 'createUser']);
Route::put('/api/superadmin/users/{id}', [SuperadminController::class, 'updateUser']);
Route::delete('/api/superadmin/users/{id}', [SuperadminController::class, 'deleteUser']);

// User-Arena Assignment API
Route::get('/api/superadmin/assignments', [SuperadminController::class, 'getAssignments']);
Route::post('/api/superadmin/assignments', [SuperadminController::class, 'createAssignment']);
Route::delete('/api/superadmin/assignments/{arenaId}/{userId}', [SuperadminController::class, 'deleteAssignment']);

// Match Management API
Route::get('/api/superadmin/kelas', [SuperadminController::class, 'getKelas']);
Route::get('/api/superadmin/matches', [SuperadminController::class, 'getMatches']);
Route::post('/api/superadmin/matches', [SuperadminController::class, 'createMatch']);
Route::post('/api/superadmin/import-matches', [SuperadminController::class, 'importMatches']);
Route::get('/api/superadmin/matches/{id}', [SuperadminController::class, 'getMatchDetail']);
Route::put('/api/superadmin/matches/{id}/arena', [SuperadminController::class, 'reassignMatchArena']);
Route::put('/api/superadmin/matches/{id}/status', [SuperadminController::class, 'updateMatchStatus']);


// route testing listen
Route::get('/test-listen', function () {
    return view('testListen');
});
