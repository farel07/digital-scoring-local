<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JudgeScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $judgeScore;
    public $pertandinganId;

    /**
     * Create a new event instance.
     */
    public function __construct($judgeScore, $pertandinganId)
    {
        $this->judgeScore = $judgeScore;
        $this->pertandinganId = $pertandinganId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('pertandingan.' . $this->pertandinganId)
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'judge.score.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'judge_id' => $this->judgeScore['user_id'],
            'judge_name' => $this->judgeScore['user']->name ?? 'Juri',
            'scores' => [
                'teknik' => $this->judgeScore['teknik'],
                'kekuatan' => $this->judgeScore['kekuatan'],
                'penampilan' => $this->judgeScore['penampilan'],
            ],
            'total' => $this->judgeScore['total'],
            'timestamp' => now()->toIso8601String()
        ];
    }
}
