<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandingMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'pertandingan_id',
        'current_round',
        'blue_total_score',
        'red_total_score',
        'blue_disqualified',
        'red_disqualified',
        'match_status',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'blue_disqualified' => 'boolean',
        'red_disqualified' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Get the pertandingan that owns the tanding match.
     */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class);
    }

    /**
     * Get all scores for this tanding match.
     */
    public function scores()
    {
        return $this->hasMany(TandingScore::class);
    }

    /**
     * Get all penalties for this tanding match.
     */
    public function penalties()
    {
        return $this->hasMany(TandingPenalty::class);
    }

    /**
     * Get team score breakdown.
     */
    public function getTeamScores($team)
    {
        return [
            'total' => $team === 'blue' ? $this->blue_total_score : $this->red_total_score,
            'disqualified' => $team === 'blue' ? $this->blue_disqualified : $this->red_disqualified,
        ];
    }
}
