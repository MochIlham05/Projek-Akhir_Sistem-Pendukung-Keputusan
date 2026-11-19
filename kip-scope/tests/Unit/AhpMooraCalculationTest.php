<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AhpService;
use App\Services\MooraService;

class AhpMooraCalculationTest extends TestCase
{
    public function test_ahp_calculation_and_consistency(): void
    {
        $ahp = new AhpService();
        $keys = ['G1', 'G2', 'G3'];
        
        // Matriks 3x3 konsisten
        $matrix = [
            'G1' => ['G1' => 1,   'G2' => 3,   'G3' => 5],
            'G2' => ['G1' => 1/3, 'G2' => 1,   'G3' => 3],
            'G3' => ['G1' => 1/5, 'G2' => 1/3, 'G3' => 1],
        ];

        $result = $ahp->calculateWeights($matrix, $keys);

        $this->assertTrue($result['is_consistent']);
        $this->assertLessThanOrEqual(0.1, $result['cr']);
        $this->assertEqualsWithDelta(1.0, array_sum($result['weights']), 0.0001);
    }

    public function test_moora_ranking(): void
    {
        $moora = new MooraService();
        
        $alternatives = [
            ['id' => 1, 'identitas' => 'Mhs A'],
            ['id' => 2, 'identitas' => 'Mhs B'],
        ];

        $criterias = [
            ['id' => 'C1', 'jenis' => 'benefit'],
            ['id' => 'C2', 'jenis' => 'cost'],
        ];

        $matrix = [
            1 => ['C1' => 80, 'C2' => 50],
            2 => ['C1' => 90, 'C2' => 20],
        ];

        $weights = ['C1' => 0.6, 'C2' => 0.4];
        $quota = 1;

        $ranked = $moora->rank($alternatives, $criterias, $matrix, $weights, $quota);

        // Mhs B memiliki nilai benefit lebih tinggi dan cost lebih rendah, harus peringkat 1
        $this->assertEquals(2, $ranked[0]['mahasiswa_id']);
        $this->assertEquals('lolos', $ranked[0]['status']);
        $this->assertEquals('tidak_lolos', $ranked[1]['status']);
    }
}