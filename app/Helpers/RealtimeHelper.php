<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class RealtimeHelper
{
    protected static $filePath = 'data/events.json';

    /**
     * Get the full path to the events JSON file
     */
    protected static function getFilePath($matchId = 1)
    {
        return public_path(self::$filePath);
    }

    /**
     * Read events from JSON file
     */
    public static function readEvents($matchId = 1)
    {
        $filePath = self::getFilePath($matchId);

        if (!File::exists($filePath)) {
            // Create default structure if file doesn't exist
            $defaultData = [
                'match_id' => $matchId,
                'last_update' => Carbon::now()->toIso8601String(),
                'judges' => new \stdClass(),
                'penalties' => [],
                'total_penalties' => 0
            ];
            self::writeEvents($defaultData, $matchId);
            return $defaultData;
        }

        $content = File::get($filePath);
        return json_decode($content, true);
    }

    /**
     * Write events to JSON file
     */
    public static function writeEvents($data, $matchId = 1)
    {
        $filePath = self::getFilePath($matchId);
        $data['last_update'] = Carbon::now()->toIso8601String();

        // Ensure directory exists
        $directory = dirname($filePath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }

    /**
     * Add or update judge score
     */
    public static function addJudgeScore($judgeId, $scores, $matchId = 1)
    {
        $events = self::readEvents($matchId);

        // Calculate total (base score 9.10 + additional scores)
        $total = 9.10 + ($scores['teknik'] ?? 0) + ($scores['kekuatan'] ?? 0) + ($scores['penampilan'] ?? 0);

        // Update or add judge data
        $judgeData = [
            'judge_id' => $judgeId,
            'judge_name' => "Juri $judgeId",
            'scores' => [
                'teknik' => $scores['teknik'] ?? 0,
                'kekuatan' => $scores['kekuatan'] ?? 0,
                'penampilan' => $scores['penampilan'] ?? 0
            ],
            'total' => round($total, 2),
            'last_update' => Carbon::now()->toIso8601String()
        ];

        $events['judges'][$judgeId] = $judgeData;

        self::writeEvents($events, $matchId);
        return $judgeData;
    }

    /**
     * Add penalty
     */
    public static function addPenalty($penaltyId, $type, $value, $matchId = 1)
    {
        $events = self::readEvents($matchId);

        // Check if penalty already exists
        $penaltyExists = false;
        foreach ($events['penalties'] as &$penalty) {
            if ($penalty['penalty_id'] === $penaltyId) {
                $penalty['status'] = 'active';
                $penalty['timestamp'] = Carbon::now()->toIso8601String();
                $penaltyExists = true;
                break;
            }
        }

        // Add new penalty if it doesn't exist
        if (!$penaltyExists) {
            $events['penalties'][] = [
                'penalty_id' => $penaltyId,
                'type' => $type,
                'value' => $value,
                'timestamp' => Carbon::now()->toIso8601String(),
                'status' => 'active'
            ];
        }

        // Recalculate total penalties
        $events['total_penalties'] = self::calculateTotalPenalties($events['penalties']);

        self::writeEvents($events, $matchId);
        return true;
    }

    /**
     * Clear penalty (set status to cleared)
     */
    public static function clearPenalty($penaltyId, $matchId = 1)
    {
        $events = self::readEvents($matchId);

        foreach ($events['penalties'] as &$penalty) {
            if ($penalty['penalty_id'] === $penaltyId) {
                $penalty['status'] = 'cleared';
                $penalty['timestamp'] = Carbon::now()->toIso8601String();
                break;
            }
        }

        // Recalculate total penalties
        $events['total_penalties'] = self::calculateTotalPenalties($events['penalties']);

        self::writeEvents($events, $matchId);
        return true;
    }

    /**
     * Calculate total active penalties
     */
    protected static function calculateTotalPenalties($penalties)
    {
        $total = 0;
        foreach ($penalties as $penalty) {
            if ($penalty['status'] === 'active') {
                $total += $penalty['value'];
            }
        }
        return round($total, 2);
    }

    /**
     * Get active judges count
     */
    public static function getActiveJudges($matchId = 1)
    {
        $events = self::readEvents($matchId);
        return count($events['judges'] ?? []);
    }

    /**
     * Get all active judges
     */
    public static function getJudges($matchId = 1)
    {
        $events = self::readEvents($matchId);
        return $events['judges'] ?? [];
    }

    /**
     * Get all penalties
     */
    public static function getPenalties($matchId = 1)
    {
        $events = self::readEvents($matchId);
        return $events['penalties'] ?? [];
    }
}
