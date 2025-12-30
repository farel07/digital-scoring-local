<?php

namespace App\Services;

use App\Models\JudgeScore;
use App\Models\Penalty;
use App\Models\Pertandingan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RealtimeService
{
    /**
     * Add or update judge score
     * 
     * @param int $pertandinganId
     * @param int $userId
     * @param array $scores ['teknik', 'kekuatan', 'penampilan']
     * @return JudgeScore
     */
    public function addJudgeScore($pertandinganId, $userId, array $scores)
    {
        // Use updateOrCreate to handle duplicate submissions
        $judgeScore = JudgeScore::updateOrCreate(
            [
                'pertandingan_id' => $pertandinganId,
                'user_id' => $userId,
            ],
            [
                'teknik' => $scores['teknik'] ?? 0,
                'kekuatan' => $scores['kekuatan'] ?? 0,
                'penampilan' => $scores['penampilan'] ?? 0,
                // total will be auto-calculated by model
            ]
        );

        // Load user relationship for broadcasting
        $judgeScore->load('user');

        // Broadcast the update to WebSocket
        broadcast(new \App\Events\JudgeScoreUpdated($judgeScore->toArray(), $pertandinganId));

        return $judgeScore;
    }

    /**
     * Add penalty
     * 
     * @param int $pertandinganId
     * @param string $penaltyId
     * @param string $type
     * @param float $value
     * @return Penalty
     */
    public function addPenalty($pertandinganId, $penaltyId, $type, $value)
    {
        // Check if penalty already exists
        $penalty = Penalty::where('penalty_id', $penaltyId)->first();

        if ($penalty) {
            // Reactivate if it was cleared
            $penalty->update(['status' => 'active']);
        } else {
            // Create new penalty
            $penalty = Penalty::create([
                'pertandingan_id' => $pertandinganId,
                'penalty_id' => $penaltyId,
                'type' => $type,
                'value' => $value,
                'status' => 'active',
            ]);
        }

        // Broadcast the penalty update
        broadcast(new \App\Events\PenaltyUpdated($penalty->toArray(), $pertandinganId, 'add'));

        return $penalty;
    }

    /**
     * Clear penalty (set status to cleared)
     * 
     * @param int $pertandinganId
     * @param string $penaltyId
     * @return bool
     */
    public function clearPenalty($pertandinganId, $penaltyId)
    {
        $penalty = Penalty::where('penalty_id', $penaltyId)
            ->where('pertandingan_id', $pertandinganId)
            ->first();

        if ($penalty) {
            $penalty->update(['status' => 'cleared']);

            // Broadcast the penalty clear
            broadcast(new \App\Events\PenaltyUpdated($penalty->toArray(), $pertandinganId, 'clear'));

            return true;
        }

        return false;
    }

    /**
     * Get all match data including judges and penalties
     * 
     * @param int $pertandinganId
     * @return array
     */
    public function getMatchData($pertandinganId)
    {
        $pertandingan = Pertandingan::with(['judgeScores.user', 'penalties'])
            ->find($pertandinganId);

        if (!$pertandingan) {
            return [
                'match_id' => $pertandinganId,
                'last_update' => Carbon::now()->toIso8601String(),
                'judges' => new \stdClass(),
                'penalties' => [],
                'total_penalties' => 0,
            ];
        }

        // Format judges data
        $judges = [];
        foreach ($pertandingan->judgeScores as $judgeScore) {
            $judges[$judgeScore->user_id] = [
                'judge_id' => $judgeScore->user_id,
                'judge_name' => $judgeScore->user->name ?? "Juri {$judgeScore->user_id}",
                'scores' => [
                    'teknik' => (float) $judgeScore->teknik,
                    'kekuatan' => (float) $judgeScore->kekuatan,
                    'penampilan' => (float) $judgeScore->penampilan,
                ],
                'total' => (float) $judgeScore->total,
                'last_update' => $judgeScore->updated_at->toIso8601String(),
            ];
        }

        // Format penalties data
        $penalties = $pertandingan->penalties->map(function ($penalty) {
            return [
                'penalty_id' => $penalty->penalty_id,
                'type' => $penalty->type,
                'value' => (float) $penalty->value,
                'timestamp' => $penalty->created_at->toIso8601String(),
                'status' => $penalty->status,
            ];
        })->toArray();

        // Calculate total active penalties
        $totalPenalties = $this->getTotalPenalties($pertandinganId);

        // Get last update time
        $lastUpdate = $pertandingan->updated_at ?? Carbon::now();
        if ($pertandingan->judgeScores->isNotEmpty()) {
            $lastJudgeUpdate = $pertandingan->judgeScores->max('updated_at');
            $lastUpdate = $lastJudgeUpdate > $lastUpdate ? $lastJudgeUpdate : $lastUpdate;
        }
        if ($pertandingan->penalties->isNotEmpty()) {
            $lastPenaltyUpdate = $pertandingan->penalties->max('updated_at');
            $lastUpdate = $lastPenaltyUpdate > $lastUpdate ? $lastPenaltyUpdate : $lastUpdate;
        }

        return [
            'match_id' => $pertandinganId,
            'last_update' => $lastUpdate->toIso8601String(),
            'judges' => empty($judges) ? new \stdClass() : $judges,
            'penalties' => $penalties,
            'total_penalties' => $totalPenalties,
        ];
    }

    /**
     * Get total active penalties for a match
     * 
     * @param int $pertandinganId
     * @return float
     */
    public function getTotalPenalties($pertandinganId)
    {
        $total = Penalty::where('pertandingan_id', $pertandinganId)
            ->where('status', 'active')
            ->sum('value');

        return round((float) $total, 2);
    }

    /**
     * Calculate statistics (median, standard deviation, final score)
     * 
     * @param int $pertandinganId
     * @param int $totalJudges Total number of judges expected
     * @return array
     */
    public function calculateStatistics($pertandinganId, $totalJudges = 4)
    {
        $judgeScores = JudgeScore::where('pertandingan_id', $pertandinganId)->get();

        $scores = $judgeScores->pluck('total')->toArray();

        // Fill with default score 9.10 for judges who haven't submitted
        $defaultScore = 9.10;
        $submittedCount = count($scores);

        for ($i = $submittedCount; $i < $totalJudges; $i++) {
            $scores[] = $defaultScore;
        }

        sort($scores);

        // Calculate median
        $median = $this->calculateMedian($scores);

        // Calculate mean
        $mean = array_sum($scores) / count($scores);

        // Calculate standard deviation
        $stdDev = $this->calculateStdDev($scores, $mean);

        // Get total penalties
        $totalPenalties = $this->getTotalPenalties($pertandinganId);

        // Calculate final score
        $finalScore = $median + $totalPenalties; // totalPenalties is already negative

        return [
            'median' => round($median, 3),
            'mean' => round($mean, 3),
            'std_dev' => round($stdDev, 6),
            'total_penalties' => $totalPenalties,
            'final_score' => round($finalScore, 3),
            'scores' => $scores,
        ];
    }

    /**
     * Calculate median from array of scores
     * 
     * @param array $scores
     * @return float
     */
    private function calculateMedian(array $scores)
    {
        $count = count($scores);

        if ($count === 0) {
            return 0;
        }

        sort($scores);

        if ($count % 2 === 0) {
            return ($scores[$count / 2 - 1] + $scores[$count / 2]) / 2;
        } else {
            return $scores[floor($count / 2)];
        }
    }

    /**
     * Calculate standard deviation
     * 
     * @param array $scores
     * @param float $mean
     * @return float
     */
    private function calculateStdDev(array $scores, $mean)
    {
        if (count($scores) === 0) {
            return 0;
        }

        $squaredDiffs = array_map(function ($score) use ($mean) {
            return pow($score - $mean, 2);
        }, $scores);

        $variance = array_sum($squaredDiffs) / count($scores);

        return sqrt($variance);
    }
}
