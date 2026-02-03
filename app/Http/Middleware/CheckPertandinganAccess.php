<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Pertandingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPertandinganAccess
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memastikan user (juri/dewan) hanya bisa akses
     * pertandingan yang ada di arena mereka.
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil pertandingan_id dari route parameter
        $pertandinganId = $request->route('id');

        if (!$pertandinganId) {
            return response()->json(['error' => 'Pertandingan ID tidak ditemukan'], 400);
        }

        // Ambil data pertandingan
        $pertandingan = Pertandingan::find($pertandinganId);

        if (!$pertandingan) {
            return response()->json(['error' => 'Pertandingan tidak ditemukan'], 404);
        }

        // Ambil user yang sedang login
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek apakah user memiliki akses ke arena pertandingan ini
        $hasAccess = $user->arenas()
            ->where('arena.id', $pertandingan->arena_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses ke pertandingan ini',
                'message' => 'User tidak di-assign ke arena pertandingan ini',
                'your_arenas' => $user->arenas->pluck('arena_name'),
                'match_arena' => $pertandingan->arena->arena_name ?? 'Unknown',
            ], 403);
        }

        // User memiliki akses, lanjutkan request
        return $next($request);
    }
}
