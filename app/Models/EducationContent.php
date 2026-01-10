<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EducationContent extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'title',
        'type',
        'category',
        'duration',
        'description',
        'video_url',
        'important_note',
        'thumbnail',
        'likes'
    ];

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'education_likes', 'education_content_id', 'user_id');
    }
}
