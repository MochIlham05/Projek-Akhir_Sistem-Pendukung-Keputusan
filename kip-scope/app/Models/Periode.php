<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'periodes';
    protected $guarded = [];

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function hasilMooras(): HasMany
    {
        return $this->hasMany(HasilMoora::class);
    }

    public function bobotHasils(): HasMany
    {
        return $this->hasMany(BobotHasil::class);
    }

    public function skenarioSensitivitas(): HasMany
    {
        return $this->hasMany(SkenarioSensitivitas::class);
    }

    public function perbandinganAhps(): HasMany
    {
        return $this->hasMany(PerbandinganAhp::class);
    }
}
