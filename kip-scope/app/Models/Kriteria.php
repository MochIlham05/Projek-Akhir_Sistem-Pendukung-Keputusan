<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $table = 'kriterias';
    protected $guarded = [];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(KelompokKriteria::class, 'kelompok_id');
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function bobotHasils(): HasMany
    {
        return $this->hasMany(BobotHasil::class);
    }
}
