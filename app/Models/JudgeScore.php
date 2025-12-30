<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgeScore extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'judge_scores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pertandingan_id',
        'user_id',
        'teknik',
        'kekuatan',
        'penampilan',
        'total',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'teknik' => 'decimal:2',
            'kekuatan' => 'decimal:2',
            'penampilan' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate total before saving
        static::saving(function ($judgeScore) {
            $judgeScore->total = 9.10 + $judgeScore->teknik + $judgeScore->kekuatan + $judgeScore->penampilan;
        });
    }

    /**
     * Get the pertandingan that owns the judge score.
     */
    public function pertandingan()
    {
        return $this->belongsTo(Pertandingan::class, 'pertandingan_id');
    }

    /**
     * Get the user (juri) that owns the judge score.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
