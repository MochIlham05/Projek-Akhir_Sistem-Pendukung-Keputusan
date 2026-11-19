<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Models\Kriteria;
use App\Models\KelompokKriteria;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\BobotHasil;
use App\Models\HasilMoora;
use App\Models\SkenarioSensitivitas;
use App\Models\PerbandinganAhp;
use App\Services\MooraService;
use App\Services\SensitivityService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected MooraService $mooraService;
    protected SensitivityService $sensitivityService;

    public function __construct(MooraService $mooraService, SensitivityService $sensitivityService)
    {
        $this->mooraService = $mooraService;
        $this->sensitivityService = $sensitivityService;
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

        $totalMahasiswa = Mahasiswa::count();
        $totalKriteria = Kriteria::count();

        // Ambil CR dari bobot AHP yang tersimpan
        $firstBobot = BobotHasil::where('periode_id', $activePeriode->id)->first();
        $crAhp = $firstBobot ? (float) $firstBobot->cr : null;

        // Ambil hasil pemeringkatan MOORA jika sudah dihitung
        $hasilMoora = HasilMoora::with('mahasiswa')
            ->where('periode_id', $activePeriode->id)
            ->orderBy('peringkat', 'asc')
            ->get();

        // Ambil hasil uji sensitivitas jika ada
        $skenarios = SkenarioSensitivitas::where('periode_id', $activePeriode->id)->get();

        return view('dashboard', compact(
            'activePeriode',
            'totalMahasiswa',
            'totalKriteria',
            'crAhp',
            'hasilMoora',
            'skenarios'
        ));
    }

    public function calculateMoora(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $periodeId = (int) $request->periode_id;
        $periode = Periode::findOrFail($periodeId);

        // 1. Ambil Bobot Global AHP
        $bobots = BobotHasil::where('periode_id', $periodeId)->get();
        if ($bobots->isEmpty()) {
            return redirect()->route('ahp.index')->with('error', 'Bobot AHP belum dihitung untuk periode ini. Silakan lakukan perhitungan AHP terlebih dahulu.');
        }

        $weights = $bobots->pluck('bobot_global', 'kriteria_id')->toArray();

        // 2. Ambil Kriteria & Mahasiswa beserta matriks Nilai
        $criterias = Kriteria::all()->toArray();
        $mahasiswaModels = Mahasiswa::all();

        if ($mahasiswaModels->isEmpty()) {
            return redirect()->route('mahasiswa.index')->with('error', 'Data mahasiswa pendaftar masih kosong. Silakan unggah CSV atau input manual terlebih dahulu.');
        }

        $mahasiswas = $mahasiswaModels->map(function ($m) {
            return [
                'id' => $m->id,
                'nomor_pendaftaran' => $m->nomor_pendaftaran,
                'identitas' => $m->nomor_pendaftaran . ' (' . $m->nama_inisial . ')'
            ];
        })->toArray();

        $nilais = Nilai::where('periode_id', $periodeId)->get();
        if ($nilais->isEmpty()) {
            return redirect()->route('mahasiswa.index')->with('error', 'Nilai pendaftar pada periode ini masih kosong. Silakan lengkapi nilai atau impor data.');
        }

        $matrix = [];
        foreach ($nilais as $n) {
            $matrix[$n->mahasiswa_id][$n->kriteria_id] = (float) $n->nilai;
        }

        // 3. Eksekusi MOORA (Ratio System)
        $rankedResults = $this->mooraService->rank($mahasiswas, $criterias, $matrix, $weights, $periode->kuota_penerima);

        // 4. Eksekusi Uji Sensitivitas (S0 - S7)
        $groups = KelompokKriteria::with('kriterias')->get();
        $groupWeights = [];
        $localWeights = [];

        foreach ($groups as $g) {
            $firstCritInGroup = $g->kriterias->first();
            $b = $bobots->where('kriteria_id', $firstCritInGroup?->id)->first();
            $wGroup = ($b && (float) $b->bobot_lokal > 0) ? ((float) $b->bobot_global / (float) $b->bobot_lokal) : 0.25;
            $groupWeights[$g->kode] = $wGroup;

            foreach ($g->kriterias as $c) {
                $bLoc = $bobots->where('kriteria_id', $c->id)->first();
                $localWeights[$g->kode][$c->id] = $bLoc ? (float) $bLoc->bobot_lokal : 0;
            }
        }

        $generatedScenarios = $this->sensitivityService->generateScenarios($groupWeights, $localWeights);
        $sensitivitySummary = [];

        foreach ($generatedScenarios as $scenName => $scenWeights) {
            $scenRanked = $this->mooraService->rank($mahasiswas, $criterias, $matrix, $scenWeights, $periode->kuota_penerima);
            $rho = $this->sensitivityService->calculateSpearmanRho($rankedResults, $scenRanked);
            $overlap = $this->sensitivityService->calculateTopKOverlap($rankedResults, $scenRanked, $periode->kuota_penerima);

            $sensitivitySummary[$scenName] = [
                'weights' => $scenWeights,
                'spearman_rho' => $rho,
                'top_k_overlap' => $overlap
            ];
        }

        // 5. Simpan Hasil ke Database dalam Transaction
        DB::transaction(function () use ($periodeId, $rankedResults, $sensitivitySummary) {
            HasilMoora::where('periode_id', $periodeId)->delete();
            SkenarioSensitivitas::where('periode_id', $periodeId)->delete();

            foreach ($rankedResults as $row) {
                HasilMoora::create([
                    'periode_id' => $periodeId,
                    'mahasiswa_id' => $row['mahasiswa_id'],
                    'skor_y' => $row['skor_y'],
                    'peringkat' => $row['peringkat'],
                    'status' => $row['status'],
                ]);
            }

            foreach ($sensitivitySummary as $name => $data) {
                SkenarioSensitivitas::create([
                    'periode_id' => $periodeId,
                    'nama_skenario' => $name,
                    'bobot_perubahan' => $data['weights'],
                    'spearman_rho' => $data['spearman_rho'],
                    'top_k_overlap' => $data['top_k_overlap'],
                ]);
            }

            AuditService::log('HITUNG_MOORA_DAN_SENSITIVITAS', [
                'periode_id' => $periodeId,
                'total_peserta' => count($rankedResults),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Perhitungan MOORA dan 8 Skenario Uji Sensitivitas (S0 - S7) berhasil dijalankan secara lengkap!');
    }

    public function resetAll(Request $request)
    {
        $periodeId = $request->input('periode_id');

        DB::transaction(function () use ($periodeId) {
            HasilMoora::query()->delete();
            SkenarioSensitivitas::query()->delete();
            BobotHasil::query()->delete();
            PerbandinganAhp::query()->delete();
            Nilai::query()->delete();
            Mahasiswa::query()->delete();

            AuditService::log('RESET_TOTAL_SELURUH_DATA', [
                'periode_id' => $periodeId,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Seluruh data sistem (pendaftar mahasiswa, nilai kriteria, matriks & bobot AHP, serta hasil pemeringkatan MOORA) berhasil direset ke kondisi awal yang bersih!');
    }
}
