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

    /**
     * Konfigurasi UUID sebagai Primary Key
     */
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable)
     */
    protected $fillable = [
    'role_id',
    'token_id',
    'username',
    'email',
    'password',
    'otp_code',
    'otp_expires_at',
    'profile_photo', 
];

    /**
     * Atribut yang disembunyikan saat serialisasi (JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut ke tipe data tertentu
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}