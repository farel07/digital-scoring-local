<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penalties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pertandingan_id',
        'penalty_id',
        'type',
        'value',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
        ];
    }

    /**
     * Get the pertandingan that owns the penalty.
     */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class, 'pertandingan_id');
    }

    /**
     * Scope a query to only include active penalties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include cleared penalties.
     */
    public function scopeCleared($query)
    {
        return $query->where('status', 'cleared');
    }
}
