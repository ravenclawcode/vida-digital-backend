<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class UserActivity extends Model
{
    use HasUuids;
    protected $fillable = ['user_id', 'type', 'description'];

    public static function log($type, $description)
    {
        self::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'description' => $description
        ]);
    }
}
