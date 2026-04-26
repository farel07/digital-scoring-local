<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertandingan;
use App\Models\Arena;
use App\Events\MatchStatusChanged;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    /**
     * Show the operator dashboard
     */
    public function index($user_id = null)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || $user->role !== 'operator') {
            return redirect('/')->with('error', 'Akses ditolak. Anda bukan operator.');
        }

        // Get the arena assigned to this operator
        $arena = $user->arenas()->first();
        
        if (!$arena) {
            return redirect('/')->with('error', 'Anda belum ditugaskan ke arena manapun.');
        }

        // Get filter inputs (default to 'all' for initial view)
        $filterType = request()->query('type', 'all'); // all, tanding, seni, jurus baku
        $filterStatus = request()->query('status', 'all'); // all, belum_dimulai, berlangsung, selesai
        $filterClass = request()->query('class', 'all'); // all or kelas_id
        $filterGender = request()->query('gender', 'all'); // all, putra, putri
        $search = request()->query('search', ''); // Search query

        // Base Query
        $query = Pertandingan::where('arena_id', $arena->id)
            ->with([
                'kelas',
                'players'
            ]);

        // Apply Search Filter (Partai ID or Player Name)
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('players', function($qPlayer) use ($search) {
                      $qPlayer->where('player_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Match Status
        if ($filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        // Filter by Match Type (jenis_pertandingan)
        if ($filterType === 'tanding') {
            $query->whereHas('kelas', function($q) {
                $q->where('jenis_pertandingan', 'tanding');
            });
        } elseif ($filterType === 'artistics') {
            $query->whereHas('kelas', function($q) {
                $q->whereIn('jenis_pertandingan', ['tunggal', 'ganda', 'regu']);
            });
        } elseif ($filterType === 'jurus baku') {
             $query->whereHas('kelas', function($q) {
                $q->where('jenis_pertandingan', 'jurus_baku');
            });
        }
        
        // Filter by Class
        if ($filterClass !== 'all') {
            $query->where('kelas_id', $filterClass);
        }
        
        // Filter by Gender (based on nama_kelas containing 'putra' or 'putri')
        if ($filterGender === 'putra') {
            $query->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%putra%')->orWhere('nama_kelas', 'like', '%pa%');
            });
        } elseif ($filterGender === 'putri') {
            $query->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%putri%')->orWhere('nama_kelas', 'like', '%pi%');
            });
        }

        // Apply Pagination
        $daftar_pertandingan = $query->orderBy('id')->paginate(10)->withQueryString();
        
        // Get unique classes for the dropdown
        $availableClasses = \App\Models\Kelas::whereIn('id', Pertandingan::where('arena_id', $arena->id)->pluck('kelas_id'))
                                             ->get();

        return view('operator.dashboard', [
            'daftar_pertandingan' => $daftar_pertandingan,
            'arena' => $arena,
            'filterType' => $filterType,
            'filterStatus' => $filterStatus,
            'filterClass' => $filterClass,
            'filterGender' => $filterGender,
            'searchQuery' => $search,
            'availableClasses' => $availableClasses
        ]);
    }

    /**
     * Update Match Status via AJAX
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'pertandingan_id' => 'required|exists:pertandingan,id',
            'status' => 'required|in:belum_dimulai,berlangsung,selesai',
        ]);

        $pertandingan = Pertandingan::findOrFail($request->pertandingan_id);
        $newStatus = $request->status;

        // If setting to berlangsung, make sure no other match in this arena is berlangsung
        if ($newStatus === 'berlangsung') {
            $activeMatches = Pertandingan::where('arena_id', $pertandingan->arena_id)
                ->where('status', 'berlangsung')
                ->where('id', '!=', $pertandingan->id)
                ->get();

            foreach ($activeMatches as $activeMatch) {
                $activeMatch->status = 'selesai'; // Or 'belum_dimulai' depending on rules
                $activeMatch->save();
            }
        }

        $pertandingan->status = $newStatus;
        $pertandingan->save();

        // If changed to berlangsung, broadcast event to reload clients
        if ($newStatus === 'berlangsung') {
            broadcast(new MatchStatusChanged($pertandingan->arena_id, $pertandingan->id, $newStatus))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'new_status' => $newStatus
        ]);
    }
}
