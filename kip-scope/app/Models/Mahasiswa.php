<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $guarded = [];

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function hasilMooras(): HasMany
    {
        return $this->hasMany(HasilMoora::class);
    }
}