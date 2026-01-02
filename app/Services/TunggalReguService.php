<?php

namespace App\Services;

use App\Models\TunggalReguScore;
use App\Models\Penalty;
use App\Models\Pertandingan;
use Illuminate\Support\Facades\DB;

class TunggalReguService
{
    /**
     * Add error to a specific jurus
     * 
     * @param int $pertandinganId
     * @param int $userId
     * @param int $jurusNumber
     * @return TunggalReguScore
     */
    public function addMoveError($pertandinganId, $userId, $jurusNumber)
    {
        // Get or create score record
        $score = TunggalReguScore::firstOrCreate(
            [
                'pertandingan_id' => $pertandinganId,
                'user_id' => $userId,
            ],
            [
                'errors_per_jurus' => json_encode([]), // Fix: encode to JSON string
                'total_errors' => 0,
                'category_score' => 0.00,
            ]
        );

        // Get current errors array
        $errorsPerJurus = $score->errors_per_jurus ?? [];

        // Initialize jurus if not exists
        if (!isset($errorsPerJurus[$jurusNumber])) {
            $errorsPerJurus[$jurusNumber] = 0;
        }

        // Increment error for this jurus
        $errorsPerJurus[$jurusNumber]++;

        // Calculate total errors
        $totalErrors = array_sum($errorsPerJurus);

        // Update score
        $score->update([
            'errors_per_jurus' => $errorsPerJurus,
            'total_errors' => $totalErrors,
            // correctness_score and total_score will be auto-calculated by model
        ]);

        // Load user relationship for broadcasting
        $score->load('user');

        // Broadcast the update
        broadcast(new \App\Events\TunggalReguScoreUpdated($score->toArray(), $pertandinganId));

        return $score;
    }

    /**
     * Set category score (Kemantapan/Penghayatan/Stamina)
     * 
     * @param int $pertandinganId
     * @param int $userId
     * @param float $score
     * @param int $maxJurus
     * @return TunggalReguScore
     */
    public function setCategoryScore($pertandinganId, $userId, $score, $maxJurus)
    {
        // Get or create score record
        $scoreRecord = TunggalReguScore::firstOrCreate(
            [
                'pertandingan_id' => $pertandinganId,
                'user_id' => $userId,
            ],
            [
                'errors_per_jurus' => json_encode([]), // Encode to JSON string initially
                'total_errors' => 0,
            ]
        );

        // Update category score
        $scoreRecord->update([
            'category_score' => $score,
            // total_score will be auto-calculated by model
        ]);

        // Load user relationship
        $scoreRecord->load('user');

        // Broadcast the update
        broadcast(new \App\Events\TunggalReguScoreUpdated($scoreRecord->toArray(), $pertandinganId));

        return $scoreRecord;
    }

    /**
     * Get all match data including judges and penalties
     * 
     * @param int $pertandinganId
     * @return array
     */
    public function getMatchData($pertandinganId)
    {
        $pertandingan = Pertandingan::with(['tunggalReguScores.user', 'penalties'])
            ->find($pertandinganId);

        if (!$pertandingan) {
            return [
                'match_id' => $pertandinganId,
                'judges' => [],
                'penalties' => [],
                'total_penalties' => 0,
                'statistics' => [
                    'median' => 0,
                    'std_dev' => 0,
                    'final_score' => 0,
                ]
            ];
        }

        // Format judges data
        $judges = [];
        foreach ($pertandingan->tunggalReguScores as $score) {
            $judges[$score->user_id] = [
                'judge_id' => $score->user_id,
                'judge_name' => $score->user->name ?? "Juri {$score->user_id}",
                'correctness_score' => (float) $score->correctness_score,
                'category_score' => (float) $score->category_score,
                'total_score' => (float) $score->total_score,
                'total_errors' => $score->total_errors,
                'errors_per_jurus' => $score->errors_per_jurus ?? [],
                'last_update' => $score->updated_at->toIso8601String(),
            ];
        }

        // Format penalties data
        $penalties = $pertandingan->penalties->map(function ($penalty) {
            return [
                'penalty_id' => $penalty->penalty_id,
                'type' => $penalty->type,
                'value' => (float) $penalty->value,
                'status' => $penalty->status,
                'timestamp' => $penalty->created_at->toIso8601String(),
            ];
        })->toArray();

        // Calculate statistics
        $statistics = $this->calculateStatistics($pertandinganId);

        return [
            'match_id' => $pertandinganId,
            'judges' => $judges,
            'penalties' => $penalties,
            'total_penalties' => $statistics['total_penalties'],
            'statistics' => $statistics,
        ];
    }

    /**
     * Calculate statistics (median, std dev, final score)
     * 
     * @param int $pertandinganId
     * @param int $totalJudges
     * @return array
     */
    public function calculateStatistics($pertandinganId, $totalJudges = 4)
    {
        $scores = TunggalReguScore::where('pertandingan_id', $pertandinganId)->get();

        $totalScores = $scores->pluck('total_score')->toArray();

        // Fill with default score 9.90 for judges who haven't submitted
        $defaultScore = 9.90;
        $submittedCount = count($totalScores);

        for ($i = $submittedCount; $i < $totalJudges; $i++) {
            $totalScores[] = $defaultScore;
        }

        sort($totalScores);

        // Calculate median
        $median = $this->calculateMedian($totalScores);

        // Calculate mean
        $mean = array_sum($totalScores) / count($totalScores);

        // Calculate standard deviation
        $stdDev = $this->calculateStdDev($totalScores, $mean);

        // Get total penalties
        $totalPenalties = Penalty::where('pertandingan_id', $pertandinganId)
            ->where('status', 'active')
            ->sum('value');

        // Calculate final score
        $finalScore = $median + $totalPenalties; // totalPenalties is already negative

        return [
            'median' => round($median, 2),
            'mean' => round($mean, 2),
            'std_dev' => round($stdDev, 6),
            'total_penalties' => round((float) $totalPenalties, 2),
            'final_score' => round($finalScore, 2),
            'scores' => $totalScores,
        ];
    }

    /**
     * Calculate median from array of scores
     */
    private function calculateMedian(array $scores)
    {
        $count = count($scores);
        if ($count === 0) return 0;

        sort($scores);

        if ($count % 2 === 0) {
            return ($scores[$count / 2 - 1] + $scores[$count / 2]) / 2;
        } else {
            return $scores[floor($count / 2)];
        }
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStdDev(array $scores, $mean)
    {
        if (count($scores) === 0) return 0;

        $squaredDiffs = array_map(function ($score) use ($mean) {
            return pow($score - $mean, 2);
        }, $scores);

        $variance = array_sum($squaredDiffs) / count($scores);

        return sqrt($variance);
    }
}
