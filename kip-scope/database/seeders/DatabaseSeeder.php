<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Periode;
use App\Models\KelompokKriteria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna Default untuk Login Sistem
        User::firstOrCreate(
            ['email' => 'moch.ilham1801@gmail.com'],
            [
                'name' => 'Moch. Ilham Abdul Aziz',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ilham@pens.ac.id'],
            [
                'name' => 'Moch. Ilham Abdul Aziz',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@kip-scope.test'],
            [
                'name' => 'Administrator KIP-SCOPE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Master Kelompok & Kriteria jika belum ada
        if (KelompokKriteria::count() === 0) {
            $this->call(KriteriaSeeder::class);
        }

        // 3. Periode Seleksi Aktif Default
        Periode::firstOrCreate(
            ['tahun' => 2026],
            [
                'kuota_penerima' => 50,
                'is_active' => true,
            ]
        );
    }
}
