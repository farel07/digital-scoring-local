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
            // Remove errors_per_jurus casting, will use accessor/mutator instead
            'total_errors' => 'integer',
            'correctness_score' => 'decimal:2',
            'category_score' => 'decimal:2',
            'total_score' => 'decimal:2',
        ];
    }

    /**
     * Get errors_per_jurus attribute (Accessor).
     * Converts JSON string from database to PHP array.
     */
    public function getErrorsPerJurusAttribute($value)
    {
        if ($value === null) {
            return [];
        }

        // If already an array (after setting), return as-is
        if (is_array($value)) {
            return $value;
        }

        // Decode JSON string to array
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Set errors_per_jurus attribute (Mutator).
     * Converts PHP array to JSON string for database storage.
     */
    public function setErrorsPerJurusAttribute($value)
    {
        // If null, set as empty JSON array
        if ($value === null) {
            $this->attributes['errors_per_jurus'] = '[]';
            return;
        }

        // If already a string (JSON), store as-is
        if (is_string($value)) {
            $this->attributes['errors_per_jurus'] = $value;
            return;
        }

        // If array, encode to JSON
        if (is_array($value)) {
            $this->attributes['errors_per_jurus'] = json_encode($value);
            return;
        }

        // Fallback: empty array
        $this->attributes['errors_per_jurus'] = '[]';
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
