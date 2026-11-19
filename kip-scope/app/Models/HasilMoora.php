<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilMoora extends Model
{
    protected $table = 'hasil_mooras';
    protected $guarded = [];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
