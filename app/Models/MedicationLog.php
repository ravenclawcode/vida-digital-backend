<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MedicationLog extends Model {
    use HasUuids;
    protected $fillable = ['medication_id', 'date', 'status'];
}
