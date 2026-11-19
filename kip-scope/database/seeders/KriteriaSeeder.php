<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['id' => 1, 'kode' => 'G1', 'nama' => 'Data Kesejahteraan'],
            ['id' => 2, 'kode' => 'G2', 'nama' => 'Program Bantuan Sosial'],
            ['id' => 3, 'kode' => 'G3', 'nama' => 'Kondisi Ekonomi Orang Tua'],
            ['id' => 4, 'kode' => 'G4', 'nama' => 'Prestasi'],
        ];
        DB::table('kelompok_kriterias')->insert($groups);

        $kriterias = [
            ['kelompok_id' => 1, 'kode' => 'C1', 'nama' => 'DTKS', 'jenis' => 'benefit'],
            ['kelompok_id' => 1, 'kode' => 'C2', 'nama' => 'P3KE', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C3', 'nama' => 'PKH', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C4', 'nama' => 'BPNT', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C5', 'nama' => 'PBI', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C6', 'nama' => 'KIP', 'jenis' => 'benefit'],
            ['kelompok_id' => 4, 'kode' => 'C7', 'nama' => 'Prestasi (Achievement)', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C8', 'nama' => 'BPUM', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C9', 'nama' => 'KIS', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C10', 'nama' => 'Bantuan Sembako', 'jenis' => 'benefit'],
            ['kelompok_id' => 2, 'kode' => 'C11', 'nama' => 'Prakerja', 'jenis' => 'benefit'],
            ['kelompok_id' => 3, 'kode' => 'C12', 'nama' => 'Kondisi ekonomi ayah', 'jenis' => 'cost'],
            ['kelompok_id' => 3, 'kode' => 'C13', 'nama' => 'Kondisi ekonomi ibu', 'jenis' => 'cost'],
        ];
        DB::table('kriterias')->insert($kriterias);
    }
}