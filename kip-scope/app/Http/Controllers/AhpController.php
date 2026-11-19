<?php

namespace App\Http\Controllers;

use App\Models\KelompokKriteria;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\PerbandinganAhp;
use App\Models\BobotHasil;
use App\Services\AhpService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AhpController extends Controller
{
    protected AhpService $ahpService;

    public function __construct(AhpService $ahpService)
    {
        $this->ahpService = $ahpService;
    }

    public function index()
    {
        $activePeriode = Periode::where('is_active', true)->first();
        if (!$activePeriode) {
            $activePeriode = Periode::create([
                'tahun' => (int) date('Y'),
                'kuota_penerima' => 50,
                'is_active' => true,
            ]);
        }

        $groups = KelompokKriteria::with('kriterias')->orderBy('id')->get();

        // Ambil data nilai perbandingan yang pernah disimpan jika ada
        $savedComparisons = PerbandinganAhp::where('periode_id', $activePeriode->id)->get();
        $compMap = [];
        foreach ($savedComparisons as $c) {
            $compMap[$c->elemen_i][$c->elemen_j] = (float) $c->nilai_skala;
        }

        // Ambil bobot hasil tersimpan
        $bobotHasil = BobotHasil::with('kriteria')->where('periode_id', $activePeriode->id)->get();

        return view('ahp.index', compact('activePeriode', 'groups', 'compMap', 'bobotHasil'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'group_matrix' => 'required|array',
            'local_matrix' => 'required|array',
        ]);

        $periodeId = (int) $request->periode_id;
        $groups = KelompokKriteria::with('kriterias')->orderBy('id')->get();
        $groupKeys = $groups->pluck('kode')->toArray();

        // 1. Hitung AHP untuk Antar-Kelompok (G1 - G4)
        $groupMatrix = $request->group_matrix;
        $groupAhp = $this->ahpService->calculateWeights($groupMatrix, $groupKeys);

        if (!$groupAhp['is_consistent']) {
            return redirect()->back()->withInput()->with('error', "Matriks perbandingan kelompok tidak konsisten! Nilai CR: {$groupAhp['cr']} (wajib <= 0.1). Silakan revisi perbandingan antar-kelompok.");
        }

        // 2. Hitung AHP Lokal untuk Tiap Kelompok
        $localWeightsPerGroup = [];
        $localAhpResults = [];

        foreach ($groups as $group) {
            $critKeys = $group->kriterias->pluck('kode')->toArray();
            if (count($critKeys) <= 1) {
                // Jika hanya ada 1 kriteria dalam kelompok (contoh G4 hanya C7)
                $localWeightsPerGroup[$group->id] = [$critKeys[0] => 1.0];
                continue;
            }

            $matrixLokal = $request->local_matrix[$group->kode] ?? [];
            if (empty($matrixLokal)) {
                return redirect()->back()->withInput()->with('error', "Matriks lokal untuk kelompok {$group->nama} ({$group->kode}) belum diisi.");
            }

            $res = $this->ahpService->calculateWeights($matrixLokal, $critKeys);

            if (!$res['is_consistent']) {
                return redirect()->back()->withInput()->with('error', "Matriks kriteria pada {$group->nama} ({$group->kode}) tidak konsisten! CR: {$res['cr']} (wajib <= 0.1). Silakan revisi.");
            }

            $localAhpResults[$group->kode] = $res;
            $localWeightsPerGroup[$group->id] = $res['weights'];
        }

        // 3. Simpan Bobot Global & Riwayat Matriks dalam Transaction
        $allKriterias = Kriteria::all()->keyBy('kode');

        DB::transaction(function () use ($periodeId, $request, $groups, $groupAhp, $localWeightsPerGroup, $allKriterias) {
            // Hapus data perbandingan & bobot lama untuk periode ini
            PerbandinganAhp::where('periode_id', $periodeId)->delete();
            BobotHasil::where('periode_id', $periodeId)->delete();

            // Simpan perbandingan kelompok
            foreach ($request->group_matrix as $i => $row) {
                foreach ($row as $j => $val) {
                    PerbandinganAhp::create([
                        'periode_id' => $periodeId,
                        'tingkat' => 'kelompok',
                        'elemen_i' => $i,
                        'elemen_j' => $j,
                        'nilai_skala' => (float) $val,
                    ]);
                }
            }

            // Simpan perbandingan lokal & bobot hasil
            foreach ($groups as $group) {
                $wGroup = $groupAhp['weights'][$group->kode] ?? 0;
                $localWeights = $localWeightsPerGroup[$group->id] ?? [];

                // Simpan matriks lokal jika ada input perbandingan
                if (isset($request->local_matrix[$group->kode])) {
                    foreach ($request->local_matrix[$group->kode] as $i => $row) {
                        foreach ($row as $j => $val) {
                            PerbandinganAhp::create([
                                'periode_id' => $periodeId,
                                'tingkat' => 'kriteria',
                                'kelompok_id' => $group->id,
                                'elemen_i' => $i,
                                'elemen_j' => $j,
                                'nilai_skala' => (float) $val,
                            ]);
                        }
                    }
                }

                // Simpan bobot kriteria ke database
                foreach ($localWeights as $critCode => $wLocal) {
                    $crit = $allKriterias[$critCode] ?? null;
                    if ($crit) {
                        BobotHasil::create([
                            'periode_id' => $periodeId,
                            'kriteria_id' => $crit->id,
                            'bobot_lokal' => (float) $wLocal,
                            'bobot_global' => (float) ($wGroup * $wLocal),
                            'cr' => (float) $groupAhp['cr'],
                        ]);
                    }
                }
            }

            AuditService::log('HITUNG_BOBOT_AHP', [
                'periode_id' => $periodeId,
                'cr_kelompok' => $groupAhp['cr'],
                'lambda_max' => $groupAhp['lambda_max'],
            ]);
        });

        return redirect()->route('ahp.index')->with('success', "Pembobotan AHP dua tingkat berhasil diverifikasi! Nilai Consistency Ratio (CR) kelompok: {$groupAhp['cr']} (Konsisten <= 0.1). Bobot global siap digunakan untuk MOORA.");
    }

    public function setPreset(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periode = $periodeId ? Periode::findOrFail($periodeId) : Periode::where('is_active', true)->firstOrFail();

        // Preset perbandingan konsisten sesuai proposal
        // G1 (Kesejahteraan), G2 (Bansos), G3 (Ekonomi Ortu), G4 (Prestasi)
        $groupMatrix = [
            'G1' => ['G1' => 1.0, 'G2' => 2.0, 'G3' => 3.0, 'G4' => 4.0],
            'G2' => ['G1' => 0.5, 'G2' => 1.0, 'G3' => 2.0, 'G4' => 2.0],
            'G3' => ['G1' => 0.3333, 'G2' => 0.5, 'G3' => 1.0, 'G4' => 2.0],
            'G4' => ['G1' => 0.25, 'G2' => 0.5, 'G3' => 0.5, 'G4' => 1.0],
        ];

        // G1 lokal: C1 (DTKS), C2 (P3KE) -> DTKS sedikit lebih penting dari P3KE (skala 2)
        $localG1 = [
            'C1' => ['C1' => 1.0, 'C2' => 2.0],
            'C2' => ['C1' => 0.5, 'C2' => 1.0],
        ];

        // G2 lokal: C3, C4, C5, C6, C8, C9, C10, C11 -> Program Bansos
        $g2Keys = ['C3', 'C4', 'C5', 'C6', 'C8', 'C9', 'C10', 'C11'];
        $localG2 = [];
        foreach ($g2Keys as $i) {
            foreach ($g2Keys as $j) {
                $localG2[$i][$j] = ($i === $j) ? 1.0 : 1.0;
            }
        }
        // KIP (C6) & PKH (C3) diberi sedikit prioritas
        $localG2['C6']['C8'] = 2.0; $localG2['C8']['C6'] = 0.5;
        $localG2['C6']['C11'] = 2.0; $localG2['C11']['C6'] = 0.5;
        $localG2['C3']['C8'] = 2.0; $localG2['C8']['C3'] = 0.5;
        $localG2['C3']['C11'] = 2.0; $localG2['C11']['C3'] = 0.5;

        // G3 lokal: C12 (Ayah), C13 (Ibu)
        $localG3 = [
            'C12' => ['C12' => 1.0, 'C13' => 1.5],
            'C13' => ['C12' => 0.6667, 'C13' => 1.0],
        ];

        $request->merge([
            'periode_id' => $periode->id,
            'group_matrix' => $groupMatrix,
            'local_matrix' => [
                'G1' => $localG1,
                'G2' => $localG2,
                'G3' => $localG3,
            ],
        ]);

        return $this->calculate($request);
    }
}
