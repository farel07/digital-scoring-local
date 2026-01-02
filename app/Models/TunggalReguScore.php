<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunggalReguScore extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'tunggal_regu_scores';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pertandingan_id',
        'user_id',
        'total_errors',
        'correctness_score',
        'category_score',
        'total_score',
        'errors_per_jurus',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'errors_per_jurus' => 'array',
            'total_errors' => 'integer',
            'correctness_score' => 'decimal:2',
            'category_score' => 'decimal:2',
            'total_score' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate scores before saving
        static::saving(function ($score) {
            // Calculate correctness score: 9.90 - (total_errors × 0.01)
            $score->correctness_score = 9.90 - ($score->total_errors * 0.01);

            // Calculate total score: correctness + category
            $score->total_score = $score->correctness_score + $score->category_score;
        });
    }

    /**
     * Get the pertandingan that owns the score.
     */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class, 'pertandingan_id');
    }

    /**
     * Get the user (juri) that owns the score.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
