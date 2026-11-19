<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkenarioSensitivitas extends Model
{
    protected $table = 'skenario_sensitivitas';
    protected $guarded = [];

    protected $casts = [
        'bobot_perubahan' => 'array',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
