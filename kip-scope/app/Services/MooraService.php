<?php

namespace App\Services;

class MooraService
{
    /**
     * Memproses pemeringkatan MOORA.
     * 
     * @param array $alternatives Daftar alternatif [['id' => 1, 'nama' => '...'], ...]
     * @param array $criterias Daftar kriteria [['id' => 1, 'jenis' => 'benefit'], ...]
     * @param array $matrix Nilai matriks [$altId][$critId] = float
     * @param array $weights Bobot global kriteria [$critId] = float
     * @param int $quota Kuota penerima beasiswa
     */
    public function rank(array $alternatives, array $criterias, array $matrix, array $weights, int $quota): array
    {
        $critIds = array_column($criterias, 'id');
        $critTypes = array_column($criterias, 'jenis', 'id');

        // 1. Hitung pembagi vektor untuk tiap kolom kriteria (Persamaan 4 denominator)
        $divisors = [];
        foreach ($critIds as $cId) {
            $sumSquares = 0;
            foreach ($alternatives as $alt) {
                $val = $matrix[$alt['id']][$cId] ?? 0;
                $sumSquares += pow($val, 2);
            }
            $divisors[$cId] = sqrt($sumSquares);
        }

        // 2. Normalisasi nilai dan hitung skor optimasi y_i (Persamaan 4 dan 5)
        $results = [];
        foreach ($alternatives as $alt) {
            $altId = $alt['id'];
            $benefitSum = 0;
            $costSum = 0;

            foreach ($critIds as $cId) {
                $rawVal = $matrix[$altId][$cId] ?? 0;
                $normalized = ($divisors[$cId] > 0) ? ($rawVal / $divisors[$cId]) : 0;
                $weightedVal = $normalized * ($weights[$cId] ?? 0);

                if ($critTypes[$cId] === 'benefit') {
                    $benefitSum += $weightedVal;
                } else {
                    $costSum += $weightedVal;
                }
            }

            // y_i = sum(benefit) - sum(cost)
            $scoreY = $benefitSum - $costSum;

            $results[] = [
                'mahasiswa_id' => $altId,
                'identitas' => $alt['identitas'] ?? $alt['nomor_pendaftaran'] ?? $altId,
                'skor_y' => $scoreY,
            ];
        }

        // 3. Urutkan hasil dari skor y_i terbesar ke terkecil
        usort($results, fn($a, $b) => $b['skor_y'] <=> $a['skor_y']);

        // 4. Beri peringkat dan tentukan status lolos berdasarkan kuota
        foreach ($results as $index => &$res) {
            $rank = $index + 1;
            $res['peringkat'] = $rank;
            $res['status'] = ($rank <= $quota) ? 'lolos' : 'tidak_lolos';
        }

        return $results;
    }
}