<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ValidationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $validation_request_id;
    public $pertandingan_id;
    public $validation_type;
    public $team;
    public $result;

    /**
     * Create a new event instance.
     */
    public function __construct($data)
    {
        $this->validation_request_id = $data['validation_request_id'];
        $this->pertandingan_id = $data['pertandingan_id'];
        $this->validation_type = $data['validation_type'];
        $this->team = $data['team'];
        $this->result = $data['result'];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('validation-completed-' . $this->pertandingan_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ValidationCompleted';
    }
}
