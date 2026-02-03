<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KirimPenaltiTanding implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $penalty_id;
    public float $value;
    public int $point_deduction;
    public bool $is_disqualified;
    private string $pertandingan_id;
    public string $filter;

    public function __construct(array $data)
    {
        $this->penalty_id = $data['penalty_id'];
        $this->value = $data['value'];
        $this->pertandingan_id = $data['pertandingan_id'];
        $this->filter = $data['filter'];
        $this->point_deduction = $data['point_deduction'] ?? 0;
        $this->is_disqualified = $data['is_disqualified'] ?? false;
    }

    public function broadcastOn(): array
    {
        return [
            // Channel baru khusus untuk penalti
            new Channel('kirim-penalti-tanding-' . $this->pertandingan_id),
        ];
    }

    public function broadcastAs()
    {
        return 'KirimPenaltiTanding';
    }
}
