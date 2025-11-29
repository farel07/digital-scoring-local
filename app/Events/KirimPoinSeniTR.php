<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class KirimPoinSeniTR implements ShouldBroadcast
{
    public $poin, $filter, $pertandingan_id, $type, $role;
    public function __construct($poin, $filter, $pertandingan_id, $type, $role)
    {
        $this->poin = $poin;
        $this->filter = $filter;
        $this->pertandingan_id = $pertandingan_id;
        $this->type = $type;
        $this->role = $role;
    }

    public function broadcastOn()
    {
        return new Channel('kirim-poin-seni-tr-' . $this->pertandingan_id);
    }
}
