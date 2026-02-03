<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandingScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanding_match_id',
        'judge_id',
        'team',
        'technique',
        'points',
        'round',
        'status'
    ];

    /**
     * Get the tanding match that owns the score.
     */
    public function tandingMatch()
    {
        return $this->belongsTo(TandingMatch::class);
    }

    /**
     * Get the judge (user) who gave the score.
     */
    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }
}
