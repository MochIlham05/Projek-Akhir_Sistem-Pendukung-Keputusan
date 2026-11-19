<?php

namespace App\Services;

class SensitivityService
{
    /**
     * Hitung Korelasi Peringkat Spearman (Persamaan 6)
     * rho = 1 - (6 * sum(d_i^2)) / (m * (m^2 - 1))
     */
    public function calculateSpearmanRho(array $baseRankings, array $scenarioRankings): float
    {
        $m = count($baseRankings);
        if ($m <= 1) {
            return 1.0;
        }

        // Buat map: id => peringkat
        $baseRankMap = [];
        foreach ($baseRankings as $row) {
            $baseRankMap[$row['mahasiswa_id']] = $row['peringkat'];
        }

        $sumD2 = 0;
        foreach ($scenarioRankings as $row) {
            $id = $row['mahasiswa_id'];
            $rBase = $baseRankMap[$id] ?? 0;
            $rScen = $row['peringkat'];
            $d = $rBase - $rScen;
            $sumD2 += pow($d, 2);
        }

        $denominator = $m * (pow($m, 2) - 1);
        $rho = 1 - ((6 * $sumD2) / $denominator);

        return round($rho, 4);
    }

    /**
     * Hitung Top-k Overlap (Persamaan 7)
     * Overlap = (|Top_k_dasar intersec Top_k_skenario| / k) * 100%
     */
    public function calculateTopKOverlap(array $baseRankings, array $scenarioRankings, int $k): float
    {
        if ($k <= 0) {
            return 0.0;
        }

        $baseTopK = array_slice(array_column($baseRankings, 'mahasiswa_id'), 0, $k);
        $scenTopK = array_slice(array_column($scenarioRankings, 'mahasiswa_id'), 0, $k);

        $intersection = array_intersect($baseTopK, $scenTopK);
        $overlap = (count($intersection) / $k) * 100;

        return round($overlap, 2);
    }

    /**
     * Menghasilkan Skenario Bobot S0 - S7 (Tabel 3.5 proposal)
     */
    public function generateScenarios(array $groupWeights, array $localWeights): array
    {
        $scenarios = [];

        // S0: Bobot dasar AHP
        $scenarios['S0'] = $this->buildGlobalWeights($groupWeights, $localWeights);

        // Definisi variasi grup
        $variations = [
            'S1' => ['group' => 'G1', 'factor' => 1.2], // G1 naik 20%
            'S2' => ['group' => 'G1', 'factor' => 0.8], // G1 turun 20%
            'S3' => ['group' => 'G3', 'factor' => 1.2], // G3 naik 20%
            'S4' => ['group' => 'G3', 'factor' => 0.8], // G3 turun 20%
            'S5' => ['group' => 'G4', 'factor' => 1.2], // G4 naik 20%
            'S6' => ['group' => 'G4', 'factor' => 0.8], // G4 turun 20%
        ];

        foreach ($variations as $code => $cfg) {
            $adjustedGroups = $groupWeights;
            $gKey = $cfg['group'];

            if (isset($adjustedGroups[$gKey])) {
                $adjustedGroups[$gKey] *= $cfg['factor'];
                // Renormalisasi agar total bobot grup tetap 1
                $total = array_sum($adjustedGroups);
                foreach ($adjustedGroups as $k => $val) {
                    $adjustedGroups[$k] = $val / $total;
                }
            }
            $scenarios[$code] = $this->buildGlobalWeights($adjustedGroups, $localWeights);
        }

        // S7: Bobot sama rata (100 / 13)
        $allCritKeys = [];
        foreach ($localWeights as $gId => $crits) {
            foreach ($crits as $cId => $w) {
                $allCritKeys[] = $cId;
            }
        }
        $count = count($allCritKeys);
        $equalWeight = ($count > 0) ? (1.0 / $count) : 0;
        $s7Weights = [];
        foreach ($allCritKeys as $cId) {
            $s7Weights[$cId] = $equalWeight;
        }
        $scenarios['S7'] = $s7Weights;

        return $scenarios;
    }

    protected function buildGlobalWeights(array $groupWeights, array $localWeights): array
    {
        $global = [];
        foreach ($localWeights as $gKey => $crits) {
            $wG = $groupWeights[$gKey] ?? 0;
            foreach ($crits as $cKey => $wL) {
                $global[$cKey] = $wG * $wL;
            }
        }
        return $global;
    }
}