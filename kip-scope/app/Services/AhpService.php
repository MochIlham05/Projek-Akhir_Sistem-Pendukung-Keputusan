<?php

namespace App\Services;

use Exception;

class AhpService
{
    // Tabel 2.4 Nilai Random Index (RI) Saaty (1 - 15)
    protected array $riTable = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
        11 => 1.51, 12 => 1.48, 13 => 1.56, 14 => 1.57, 15 => 1.59
    ];

    /**
     * Hitung bobot lokal dan konsistensi dari matriks perbandingan berpasangan n x n.
     * $matrix berupa array associative [$elemen_i][$elemen_j] = nilai
     */
    public function calculateWeights(array $matrix, array $keys): array
    {
        $n = count($keys);
        if ($n === 0) {
            throw new Exception("Matriks perbandingan kosong.");
        }
        if ($n === 1) {
            return [
                'weights' => [$keys[0] => 1.0],
                'lambda_max' => 1.0,
                'ci' => 0.0,
                'cr' => 0.0,
                'is_consistent' => true
            ];
        }

        // 1. Hitung total tiap kolom
        $colSums = [];
        foreach ($keys as $j) {
            $colSums[$j] = 0;
            foreach ($keys as $i) {
                $colSums[$j] += $matrix[$i][$j];
            }
        }

        // 2. Normalisasi matriks dan hitung rata-rata baris (bobot w)
        $weights = [];
        foreach ($keys as $i) {
            $rowSum = 0;
            foreach ($keys as $j) {
                $normalizedVal = $matrix[$i][$j] / $colSums[$j];
                $rowSum += $normalizedVal;
            }
            $weights[$i] = $rowSum / $n;
        }

        // 3. Hitung perkalian matriks A dengan vektor bobot (A * w)
        $aw = [];
        foreach ($keys as $i) {
            $aw[$i] = 0;
            foreach ($keys as $j) {
                $aw[$i] += $matrix[$i][$j] * $weights[$j];
            }
        }

        // 4. Hitung Lambda Max (Persamaan 1)
        $lambdaMaxSum = 0;
        foreach ($keys as $i) {
            $lambdaMaxSum += $aw[$i] / $weights[$i];
        }
        $lambdaMax = $lambdaMaxSum / $n;

        // 5. Hitung Consistency Index (CI) (Persamaan 2)
        $ci = ($lambdaMax - $n) / ($n - 1);

        // 6. Hitung Consistency Ratio (CR) (Persamaan 3)
        $ri = $this->riTable[$n] ?? 1.59;
        $cr = ($ri > 0) ? ($ci / $ri) : 0.0;

        return [
            'weights' => $weights,
            'lambda_max' => round($lambdaMax, 4),
            'ci' => round($ci, 4),
            'cr' => round($cr, 4),
            'is_consistent' => ($cr <= 0.1) // CR <= 0.1 wajib terpenuhi
        ];
    }

    /**
     * Hierarki Dua Tingkat: Bobot Global = Bobot Kelompok x Bobot Lokal
     */
    public function computeGlobalWeights(array $groupWeights, array $localWeightsPerGroup): array
    {
        $globalWeights = [];
        foreach ($localWeightsPerGroup as $groupId => $localWeights) {
            $wGroup = $groupWeights[$groupId] ?? 0;
            foreach ($localWeights as $criteriaId => $wLocal) {
                $globalWeights[$criteriaId] = $wGroup * $wLocal;
            }
        }
        return $globalWeights;
    }
}
