<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AhpController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Grup Route yang membutuhkan Login
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard & MOORA (Tahap 5)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/moora/calculate', [DashboardController::class, 'calculateMoora'])->name('moora.calculate');
    Route::post('/system/reset-all', [DashboardController::class, 'resetAll'])->name('system.reset-all');

    // Route Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul Data Mahasiswa (Tahap 3)
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::post('/mahasiswa/store', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::get('/mahasiswa/template', [MahasiswaController::class, 'template'])->name('mahasiswa.template');
    Route::post('/mahasiswa/seed-dummy', [MahasiswaController::class, 'seedDummy'])->name('mahasiswa.seed-dummy');
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::post('/mahasiswa/reset', [MahasiswaController::class, 'reset'])->name('mahasiswa.reset');
    Route::post('/mahasiswa/kuota', [MahasiswaController::class, 'updateKuota'])->name('mahasiswa.update-kuota');

    // Modul AHP (Tahap 4)
    Route::get('/ahp', [AhpController::class, 'index'])->name('ahp.index');
    Route::post('/ahp/calculate', [AhpController::class, 'calculate'])->name('ahp.calculate');
    Route::post('/ahp/preset', [AhpController::class, 'setPreset'])->name('ahp.preset');
});

require __DIR__.'/auth.php';
