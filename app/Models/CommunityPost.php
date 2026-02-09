<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunityPost extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['user_id', 'category', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(CommunityLike::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class);
    }
}
