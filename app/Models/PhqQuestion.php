<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhqQuestion extends Model
{
    protected $fillable = ['question_text', 'options'];
    protected $casts = [
        'options' => 'array',
    ];
}
