<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveSideChangedSeni implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pertandingan_id;
    public $side;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($pertandingan_id, $side)
    {
        $this->pertandingan_id = $pertandingan_id;
        $this->side = $side;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('seni.' . $this->pertandingan_id);
    }

    public function broadcastAs()
    {
        return 'ActiveSideChanged';
    }
}
