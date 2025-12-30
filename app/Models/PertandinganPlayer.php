<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertandinganPlayer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pertandingan_player';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pertandingan_id',
        'player_name',
        'player_contingent',
        'side_number',
        'total_score',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
        ];
    }

    /**
     * Get the pertandingan that owns the player.
     */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class, 'pertandingan_id');
    }
}
