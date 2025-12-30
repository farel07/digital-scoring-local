<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Pertandingan;
use App\Models\Arena;

class MatchResolver
{
    /**
     * Get active match for a user based on their arena assignment
     * 
     * @param int $userId
     * @return Pertandingan|null
     */
    public static function getActiveMatchForUser($userId)
    {
        $user = User::with('arenas')->find($userId);

        if (!$user) {
            return null;
        }

        // Get user's assigned arenas
        $arenaIds = $user->arenas->pluck('id')->toArray();

        if (empty($arenaIds)) {
            return null;
        }

        // Find pertandingan with status 'berlangsung' in user's arena
        $pertandingan = Pertandingan::whereIn('arena_id', $arenaIds)
            ->where('status', 'berlangsung')
            ->first();

        return $pertandingan;
    }

    /**
     * Get user's assigned arena
     * 
     * @param int $userId
     * @return Arena|null
     */
    public static function getUserArena($userId)
    {
        $user = User::with('arenas')->find($userId);

        if (!$user || $user->arenas->isEmpty()) {
            return null;
        }

        // Return first arena (assuming user is assigned to one arena)
        return $user->arenas->first();
    }

    /**
     * Validate if user has access to a specific pertandingan
     * 
     * @param int $userId
     * @param int $pertandinganId
     * @return bool
     */
    public static function validateUserAccess($userId, $pertandinganId)
    {
        $user = User::with('arenas')->find($userId);

        if (!$user) {
            return false;
        }

        $pertandingan = Pertandingan::find($pertandinganId);

        if (!$pertandingan) {
            return false;
        }

        // Check if pertandingan's arena matches user's assigned arena
        $userArenaIds = $user->arenas->pluck('id')->toArray();

        return in_array($pertandingan->arena_id, $userArenaIds);
    }

    /**
     * Get all pertandingan in user's arena with specific status
     * 
     * @param int $userId
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMatchesForUser($userId, $status = null)
    {
        $user = User::with('arenas')->find($userId);

        if (!$user) {
            return collect([]);
        }

        $arenaIds = $user->arenas->pluck('id')->toArray();

        $query = Pertandingan::whereIn('arena_id', $arenaIds);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }
}
