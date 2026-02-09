<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationLog extends Model
{
    use HasUuids;
    protected $fillable = ['medication_id', 'date', 'status'];

    /**
     * Relasi ke model Medication
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medication_id');
    }
}
