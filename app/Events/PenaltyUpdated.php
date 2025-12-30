<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PenaltyUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $penalty;
    public $pertandinganId;
    public $action; // 'add' or 'clear'

    /**
     * Create a new event instance.
     */
    public function __construct($penalty, $pertandinganId, $action)
    {
        $this->penalty = $penalty;
        $this->pertandinganId = $pertandinganId;
        $this->action = $action;
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
        return 'penalty.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'penalty_id' => $this->penalty['penalty_id'],
            'type' => $this->penalty['type'],
            'value' => $this->penalty['value'],
            'status' => $this->penalty['status'],
            'action' => $this->action,
            'timestamp' => $this->penalty['created_at'] ?? now()->toIso8601String()
        ];
    }
}
