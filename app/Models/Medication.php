<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Medication extends Model
{
    use HasUuids;
    protected $fillable = ['user_id', 'name', 'reminder_time'];

    public function logs()
    {
        return $this->hasMany(MedicationLog::class);
    }
}