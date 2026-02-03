<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation_request_id',
        'juri_id',
        'vote'
    ];

    public function validationRequest()
    {
        return $this->belongsTo(ValidationRequest::class);
    }

    public function juri()
    {
        return $this->belongsTo(User::class, 'juri_id');
    }
}
