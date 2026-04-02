<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pertandingan_id;
    public $state;
    public $current_time;
    public $total_duration;
    public $current_round;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct($data)
    {
        $this->pertandingan_id = $data['pertandingan_id'];
        $this->state = $data['state'] ?? null;
        $this->current_time = $data['current_time'] ?? null;
        $this->total_duration = $data['total_duration'] ?? null;
        $this->current_round = $data['current_round'] ?? null;
        $this->timestamp = $data['timestamp'] ?? now()->timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('timer-' . $this->pertandingan_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'TimerUpdated';
    }
}
