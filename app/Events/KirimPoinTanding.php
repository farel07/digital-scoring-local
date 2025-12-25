<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KirimPoinTanding implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $poin;
    public $pertandingan_id;
    public $filter;
    public $juri_id;
    public $status; // <--- Tambah ini ('input' atau 'sah')

    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->poin = $data['poin'];
        $this->pertandingan_id = $data['pertandingan_id'];
        $this->filter = $data['filter'];
        $this->juri_id = $data['juri_id'];
        $this->status = $data['status']; // <--- Assign ini
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('kirim-poin-tanding-' . $this->pertandingan_id),
        ];
    }
    
    public function broadcastAs()
    {
        return 'KirimPoinTanding';
    }
}