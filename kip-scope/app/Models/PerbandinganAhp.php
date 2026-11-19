<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerbandinganAhp extends Model
{
    protected $table = 'perbandingan_ahps';
    protected $guarded = [];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(KelompokKriteria::class, 'kelompok_id');
    }
}
