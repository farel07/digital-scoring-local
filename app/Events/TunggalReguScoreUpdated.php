<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TunggalReguScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $score;
    public $pertandinganId;

    /**
     * Create a new event instance.
     */
    public function __construct($score, $pertandinganId)
    {
        $this->score = $score;
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
        return 'tunggal.score.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'judge_id' => $this->score['user_id'],
            'judge_name' => $this->score['user']['name'] ?? 'Juri',
            'correctness_score' => $this->score['correctness_score'],
            'category_score' => $this->score['category_score'],
            'total_score' => $this->score['total_score'],
            'total_errors' => $this->score['total_errors'],
            'errors_per_jurus' => $this->score['errors_per_jurus'] ?? [],
            'timestamp' => now()->toIso8601String()
        ];
    }
}
