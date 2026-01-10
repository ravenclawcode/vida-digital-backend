<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationToken extends Model
{
    protected $fillable = [
        'token_code',
        'is_used'
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'token_id');
    }
}