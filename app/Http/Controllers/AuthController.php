<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // If user is already authenticated, redirect to home
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();

            // Smart redirect based on role and arena
            $redirectUrl = $this->getRedirectUrl($user);

            return redirect($redirectUrl)->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Authentication failed
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Get redirect URL based on user role and assigned arena.
     */
    private function getRedirectUrl($user)
    {
        // Get user's arenas
        $arenaIds = $user->arenas()->pluck('arena.id');

        if ($arenaIds->isEmpty()) {
            // User has no arena assignment, go to welcome page
            return '/';
        }

        // Find active match in user's arena
        $activeMatch = \App\Models\Pertandingan::whereIn('arena_id', $arenaIds)
            ->where('status', 'berlangsung')
            ->with('kelas')
            ->first();

        if (!$activeMatch) {
            // No active match, go to welcome page
            return '/';
        }

        // Route based on role and match type
        return $this->buildRouteForRole($user->role, $activeMatch, $user->id);
    }

    /**
     * Build the appropriate route based on user role and match type.
     */
    private function buildRouteForRole($role, $activeMatch, $userId)
    {
        $pertandinganId = $activeMatch->id;
        $matchType = strtolower($activeMatch->kelas->jenis_pertandingan);

        // Route for Juri roles (juri_1, juri_2, juri_3, juri_4)
        if (in_array($role, ['juri_1', 'juri_2', 'juri_3', 'juri_4'])) {
            return $this->getJuriRoute($matchType, $userId, $pertandinganId);
        }

        // Route for Dewan role
        if ($role === 'dewan') {
            return $this->getDewanRoute($matchType, $userId, $pertandinganId);
        }

        // Route for Operator role
        if ($role === 'operator') {
            return $this->getOperatorRoute($matchType, $pertandinganId);
        }

        // Route for Timer Tanding role
        if ($role === 'timer_tanding') {
            return "/timer-tanding/{$userId}";
        }

        // Default fallback
        return '/';
    }

    /**
     * Get Juri route based on match type.
     */
    private function getJuriRoute($matchType, $userId, $pertandinganId)
    {
        if ($matchType === 'tanding') {
            return "/juri-tanding/{$pertandinganId}";
        }

        if (in_array($matchType, ['tunggal', 'regu'])) {
            return "/juri-seni-tunggal-regu/{$userId}";
        }

        // Seni Ganda or other types
        return "/juri-seni-ganda/{$userId}";
    }

    /**
     * Get Dewan route based on match type.
     */
    private function getDewanRoute($matchType, $userId, $pertandinganId)
    {
        if ($matchType === 'tanding') {
            return "/dewan-tanding/{$pertandinganId}";
        }

        if (in_array($matchType, ['tunggal', 'regu'])) {
            return "/dewan-seni-tunggal-regu/{$userId}";
        }

        // Seni Ganda or other types
        return "/dewan-seni-ganda/{$userId}";
    }

    /**
     * Get Operator route based on match type.
     */
    private function getOperatorRoute($matchType, $pertandinganId)
    {
        if ($matchType === 'tanding') {
            // For tanding, redirect to penilaian page
            return "/penilaian/{$pertandinganId}";
        }

        if (in_array($matchType, ['tunggal', 'regu'])) {
            return "/dewan-operator-seni-tunggal-regu/{$pertandinganId}";
        }

        // Seni Ganda or other types
        return "/dewan-operator-seni-ganda/{$pertandinganId}";
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }
}
