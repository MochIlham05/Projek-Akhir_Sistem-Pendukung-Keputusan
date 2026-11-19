<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokKriteria extends Model
{
    protected $table = 'kelompok_kriterias';
    protected $guarded = [];

    public function kriterias(): HasMany
    {
        return $this->hasMany(Kriteria::class, 'kelompok_id');
    }
}
