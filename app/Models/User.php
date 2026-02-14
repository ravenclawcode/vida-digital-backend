<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
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
        'is_online',
        'last_seen',
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
            'last_seen' => 'datetime',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo) return null;

        return str_contains($this->profile_photo, 'assets/')
            ? $this->profile_photo
            : asset('storage/' . $this->profile_photo);
    }

    public function messagesSent()
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(PrivateMessage::class, 'receiver_id');
    }
}
