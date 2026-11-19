<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BobotHasil extends Model
{
    protected $table = 'bobot_hasils';
    protected $guarded = [];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }
}
