<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use Exception;

class MahasiswaCsvImport
{
    public static function import(string $filePath, int $periodeId): int
    {
        $kriteriaMap = Kriteria::pluck('id', 'kode')->mapWithKeys(function ($id, $kode) {
            return [strtolower(trim($kode)) => $id];
        });

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("File CSV tidak dapat dibaca.");
        }

        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            throw new Exception("File CSV kosong.");
        }

        // Hapus UTF-8 BOM jika ada
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        // Deteksi delimiter: cek baris pertama
        $lines = preg_split("/\r\n|\n|\r/", trim($content));
        if (empty($lines)) {
            throw new Exception("Format file CSV tidak valid.");
        }

        $firstLine = $lines[0];
        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $headers = fgetcsv($handle, 2000, $delimiter);
        if (!$headers) {
            fclose($handle);
            throw new Exception("Gagal membaca header CSV.");
        }

        // Normalisasi header (lowercase, trim, hilangkan karakter aneh)
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h)));
        }, $headers);

        $importedCount = 0;

        DB::transaction(function () use ($handle, $delimiter, $headers, $kriteriaMap, $periodeId, &$importedCount) {
            while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
                if (empty($row) || count($row) < 2) continue;

                // Pad atau potong baris agar panjang sama dengan header
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }

                $data = array_combine($headers, $row);

                $noPendaftaran = trim($data['nomor_pendaftaran'] ?? $data['no_pendaftaran'] ?? $data['nisn'] ?? $data['nim'] ?? $row[0] ?? '');
                if ($noPendaftaran === '') continue;

                $namaInisial = trim($data['nama_inisial'] ?? $data['nama'] ?? $data['inisial'] ?? $row[1] ?? 'Mhs');

                // 1. Simpan / Perbarui Mahasiswa
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['nomor_pendaftaran' => $noPendaftaran],
                    ['nama_inisial' => $namaInisial]
                );

                // 2. Simpan Nilai Kriteria C1 - C13
                foreach ($kriteriaMap as $kode => $kriteriaId) {
                    if (isset($data[$kode]) && $data[$kode] !== '') {
                        $val = (float) str_replace(',', '.', trim($data[$kode]));
                        Nilai::updateOrCreate(
                            [
                                'periode_id' => $periodeId,
                                'mahasiswa_id' => $mahasiswa->id,
                                'kriteria_id' => $kriteriaId,
                            ],
                            [
                                'nilai' => $val,
                            ]
                        );
                    }
                }

                $importedCount++;
            }
        });

        fclose($handle);
        return $importedCount;
    }
}
