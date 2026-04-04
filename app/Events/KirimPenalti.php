<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KirimPenalti implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $penalty_id;
    public float $value;
    public string $side;
    private int $pertandingan_id;

    public function __construct(array $data)
    {
        $this->penalty_id = $data['penalty_id'];
        $this->value = $data['value'];
        $this->side = $data['side'] ?? '1';
        $this->pertandingan_id = $data['pertandingan_id'];
    }

    public function broadcastOn(): array
    {
        return [
            // Use same channel pattern as score updates for consistency
            new Channel('pertandingan.' . $this->pertandingan_id),
        ];
    }

    public function broadcastAs()
    {
        // Use event name that DewanOperator listens to
        return 'penalty.updated';
    }
}
