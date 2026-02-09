<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhqResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_score', 'category', 'answers'];

    protected $casts = [
        'answers' => 'array',
        'user_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
