<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoapNote extends Model
{
    protected $fillable = ['counselor_id', 'patient_id', 'subjective', 'objective', 'assessment', 'plan'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
