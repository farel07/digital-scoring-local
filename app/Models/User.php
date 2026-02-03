<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Arena;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the arenas associated with the user.
     */
    public function arenas()
    {
        return $this->belongsToMany(Arena::class, 'user_arena', 'user_id', 'arena_id');
    }

    /**
     * Check if user has access to a specific arena.
     */
    public function hasAccessToArena($arenaId)
    {
        return $this->arenas()->where('arena.id', $arenaId)->exists();
    }

    /**
     * Check if user has access to a specific pertandingan.
     */
    public function hasAccessToPertandingan($pertandinganId)
    {
        $pertandingan = \App\Models\Pertandingan::find($pertandinganId);

        if (!$pertandingan) {
            return false;
        }

        return $this->hasAccessToArena($pertandingan->arena_id);
    }

    /**
     * Get all pertandingan that the user can access.
     */
    public function accessiblePertandingan()
    {
        $arenaIds = $this->arenas()->pluck('arena.id');

        return \App\Models\Pertandingan::whereIn('arena_id', $arenaIds)->get();
    }

    /**
     * Get active match in user's assigned arenas.
     */
    public function getActiveMatch()
    {
        $arenaIds = $this->arenas()->pluck('arena.id');

        return \App\Models\Pertandingan::whereIn('arena_id', $arenaIds)
            ->where('status', 'berlangsung')
            ->with('kelas')
            ->first();
    }
}
