<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodLog extends Model
{
    protected $fillable = ['user_id', 'mood_code', 'date'];
}