<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanding_match_id',
        'requested_by',
        'validation_type',
        'team',
        'description',
        'result',
        'status'
    ];

    public function tandingMatch()
    {
        return $this->belongsTo(TandingMatch::class, 'tanding_match_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function votes()
    {
        return $this->hasMany(ValidationVote::class);
    }

    /**
     * Calculate validation result based on votes
     * - If 2+ juris vote the same: return that vote
     * - If all 3 juris vote differently: return 'INVALID'
     */
    public function calculateResult()
    {
        $votes = $this->votes()->pluck('vote')->toArray();

        // Need at least 2 votes to calculate
        if (count($votes) < 2) {
            return null;
        }

        // Count votes for each option
        $voteCounts = array_count_values($votes);
        arsort($voteCounts); // Sort by count descending

        $topVote = array_key_first($voteCounts);
        $topCount = $voteCounts[$topVote];

        // At least 2 juris voted the same
        if ($topCount >= 2) {
            return $topVote;
        }

        // All different votes (only possible with 3 votes)
        return 'INVALID';
    }
}
