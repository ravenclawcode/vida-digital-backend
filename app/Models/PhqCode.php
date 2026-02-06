<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhqCode extends Model
{
    protected $fillable = ['token_code', 'user_id', 'is_used'];
}
