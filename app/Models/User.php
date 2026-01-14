<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory\HasFactoryFactory */
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $appends = ['profile_photo_url'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'role_id',
        'token_id',
        'username',
        'email',
        'password',
        'gender',
        'otp_code',
        'otp_expires_at',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'education_likes',
            'education_content_id',
            'user_id'
        );
    }

    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo)
            return null;

        if (str_contains($this->profile_photo, 'assets/')) {
            return $this->profile_photo;
        }

        return asset('storage/' . $this->profile_photo);
    }
}