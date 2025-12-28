<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MindfulnessAudio extends Model
{
    protected $table = 'mindfulness_audios';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['title', 'category', 'description', 'duration', 'audio_url', 'cover_url'];

    protected static function boot() {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }
}
