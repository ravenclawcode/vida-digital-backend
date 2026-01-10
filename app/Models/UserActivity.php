<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserActivity extends Model
{
    use HasUuids;
    protected $fillable = ['user_id', 'type', 'description'];

    public static function log($type, $description)
    {
        self::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'description' => $description
        ]);
    }
}