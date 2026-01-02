<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kelas',
        'jenis_pertandingan',
    ];

    /**
     * Get the pertandingan for the kelas.
     */
    public function pertandingan()
    {
        return $this->hasMany(Pertandingan::class, 'kelas_id');
    }
}
