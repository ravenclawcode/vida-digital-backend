<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EducationContent extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'type',
        'category',
        'duration',
        'description',
        'video_url',
        'content',
        'important_note',
        'thumbnail',
        'likes'
    ];
}
