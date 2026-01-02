<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertandingan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pertandingan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kelas_id',
        'arena_id',
        'next_match_id',
        'status',
    ];

    /**
     * Get the kelas that owns the pertandingan.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get the arena that owns the pertandingan.
     */
    public function arena()
    {
        return $this->belongsTo(Arena::class, 'arena_id');
    }

    /**
     * Get the next match.
     */
    public function nextMatch()
    {
        return $this->belongsTo(Pertandingan::class, 'next_match_id');
    }

    /**
     * Get the previous matches that link to this match.
     */
    public function previousMatches()
    {
        return $this->hasMany(Pertandingan::class, 'next_match_id');
    }

    /**
     * Get the players for the pertandingan.
     */
    public function players()
    {
        return $this->hasMany(PertandinganPlayer::class, 'pertandingan_id');
    }

    /**
     * Get the judge scores for the pertandingan.
     */
    public function judgeScores()
    {
        return $this->hasMany(JudgeScore::class, 'pertandingan_id');
    }

    /**
     * Get the penalties for the pertandingan.
     */
    public function penalties()
    {
        return $this->hasMany(Penalty::class, 'pertandingan_id');
    }

    /**
     * Get only active penalties for the pertandingan.
     */
    public function activePenalties()
    {
        return $this->hasMany(Penalty::class, 'pertandingan_id')->where('status', 'active');
    }

    /**
     * Get the tunggal/regu scores for the pertandingan.
     */
    public function tunggalReguScores()
    {
        return $this->hasMany(TunggalReguScore::class, 'pertandingan_id');
    }
}
