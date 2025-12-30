<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'arena';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'arena_name',
    ];

    /**
     * Get the users associated with the arena.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_arena', 'arena_id', 'user_id');
    }

    /**
     * Get the pertandingan for the arena.
     */
    public function pertandingan()
    {
        return $this->hasMany(Pertandingan::class, 'arena_id');
    }
}
