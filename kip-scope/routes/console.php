<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\HasilMoora;
use App\Models\SkenarioSensitivitas;
use App\Models\BobotHasil;
use App\Models\PerbandinganAhp;
use App\Models\Nilai;
use App\Models\Mahasiswa;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('kip:reset-data', function () {
    HasilMoora::query()->delete();
    SkenarioSensitivitas::query()->delete();
    BobotHasil::query()->delete();
    PerbandinganAhp::query()->delete();
    Nilai::query()->delete();
    Mahasiswa::query()->delete();
    $this->info('Seluruh data dummy mahasiswa, nilai, bobot AHP, dan hasil MOORA berhasil dikosongkan!');
})->purpose('Mengosongkan data simulasi mahasiswa dan hasil perhitungan untuk input data mandiri');
