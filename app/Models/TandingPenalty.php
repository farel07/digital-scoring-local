<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandingPenalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanding_match_id',
        'team',
        'penalty_type',
        'penalty_value',
        'point_deduction',
        'round',
        'caused_disqualification'
    ];

    protected $casts = [
        'caused_disqualification' => 'boolean',
    ];

    /**
     * Get the tanding match that owns the penalty.
     */
    public function tandingMatch()
    {
        return $this->belongsTo(TandingMatch::class);
    }
}
