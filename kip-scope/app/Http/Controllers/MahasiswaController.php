<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\Nilai;
use App\Models\HasilMoora;
use App\Models\SkenarioSensitivitas;
use App\Imports\MahasiswaCsvImport;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil atau inisialisasi periode aktif
        $activePeriode = Periode::where('is_active', true)->first();
        if (!$activePeriode) {
            $activePeriode = Periode::create([
                'tahun' => (int) date('Y'),
                'kuota_penerima' => 50,
                'is_active' => true,
            ]);
        }

        $kriterias = Kriteria::with('kelompok')->orderBy('id')->get();
        $mahasiswas = Mahasiswa::with(['nilais' => function ($query) use ($activePeriode) {
            $query->where('periode_id', $activePeriode->id);
        }])->orderBy('id', 'desc')->get();

        return view('mahasiswa.index', compact('activePeriode', 'kriterias', 'mahasiswas'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'periode_id' => 'required|exists:periodes,id',
        ]);

        try {
            $count = MahasiswaCsvImport::import($request->file('file')->getRealPath(), (int) $request->periode_id);

            AuditService::log('IMPORT_CSV_MAHASISWA', [
                'periode_id' => $request->periode_id,
                'total_diimpor' => $count,
                'file_name' => $request->file('file')->getClientOriginalName(),
            ]);

            return redirect()->back()->with('success', "Berhasil mengimpor {$count} data calon penerima beasiswa!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor file CSV: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pendaftaran' => 'required|string|max:50',
            'nama_inisial' => 'required|string|max:100',
            'periode_id' => 'required|exists:periodes,id',
            'nilai' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $mhs = Mahasiswa::updateOrCreate(
                ['nomor_pendaftaran' => trim($request->nomor_pendaftaran)],
                ['nama_inisial' => trim($request->nama_inisial)]
            );

            foreach ($request->nilai as $kriteriaId => $val) {
                Nilai::updateOrCreate(
                    [
                        'periode_id' => $request->periode_id,
                        'mahasiswa_id' => $mhs->id,
                        'kriteria_id' => $kriteriaId,
                    ],
                    [
                        'nilai' => (float) $val,
                    ]
                );
            }

            AuditService::log('TAMBAH_DATA_MAHASISWA_MANUAL', [
                'nomor_pendaftaran' => $mhs->nomor_pendaftaran,
                'periode_id' => $request->periode_id,
            ]);
        });

        return redirect()->back()->with('success', 'Data pendaftar berhasil disimpan!');
    }

    public function destroy($id)
    {
        $mhs = Mahasiswa::findOrFail($id);
        $no = $mhs->nomor_pendaftaran;
        $mhs->delete();

        AuditService::log('HAPUS_DATA_MAHASISWA', ['nomor_pendaftaran' => $no]);

        return redirect()->back()->with('success', "Data mahasiswa ({$no}) berhasil dihapus.");
    }

    public function reset(Request $request)
    {
        $periodeId = $request->input('periode_id');
        DB::transaction(function () use ($periodeId) {
            HasilMoora::query()->delete();
            SkenarioSensitivitas::query()->delete();
            Nilai::query()->delete();
            Mahasiswa::query()->delete();
        });

        AuditService::log('RESET_DATA_MAHASISWA', ['periode_id' => $periodeId]);

        return redirect()->back()->with('success', 'Seluruh data mahasiswa, nilai kriteria, dan hasil pemeringkatan berhasil dikosongkan!');
    }

    public function updateKuota(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'kuota_penerima' => 'required|integer|min:1|max:10000',
        ]);

        $periode = Periode::findOrFail($request->periode_id);
        $periode->update(['kuota_penerima' => (int) $request->kuota_penerima]);

        AuditService::log('UPDATE_KUOTA_BEASISWA', [
            'periode_id' => $periode->id,
            'kuota_baru' => $periode->kuota_penerima,
        ]);

        return redirect()->back()->with('success', "Kuota beasiswa berhasil diperbarui menjadi {$periode->kuota_penerima} penerima.");
    }

    public function template(): StreamedResponse
    {
        $kriterias = Kriteria::orderBy('id')->get();
        $headers = array_merge(['nomor_pendaftaran', 'nama_inisial'], $kriterias->pluck('kode')->map(fn($k) => strtolower($k))->toArray());

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            // Contoh baris 1
            fputcsv($handle, ['26010001', 'Ahmad S.', '4', '1', '1', '1', '1', '1', '85', '0', '1', '1', '0', '1500000', '1200000']);
            // Contoh baris 2
            fputcsv($handle, ['26010002', 'Budi P.', '3', '2', '0', '1', '1', '0', '78', '1', '1', '0', '1', '2000000', '1800000']);
            // Contoh baris 3
            fputcsv($handle, ['26010003', 'Citra D.', '1', '4', '0', '0', '0', '0', '92', '0', '0', '0', '0', '3500000', '2500000']);
            fclose($handle);
        }, 'template_mahasiswa_kip_scope.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function seedDummy(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periode = $periodeId ? Periode::findOrFail($periodeId) : Periode::where('is_active', true)->firstOrFail();

        $kriterias = Kriteria::orderBy('id')->get();

        $names = [
            'Andi Pratama', 'Budi Santoso', 'Citra Dewi', 'Dimas Anggara', 'Eka Lestari',
            'Fajar Ramadhan', 'Gita Permata', 'Hadi Wijaya', 'Indah Kusuma', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Mega Utami', 'Nugroho Saputra', 'Olivia Putri',
            'Panji Gemilang', 'Qori Amelia', 'Rian Hidayat', 'Siti Rahma', 'Teguh Prasetyo',
            'Utari Wulandari', 'Vina Panduwinata', 'Wahyu Setiawan', 'Xenia Febrianti', 'Yusuf Mansur',
            'Zahra Nurhaliza', 'Agus Firmansyah', 'Bella Septiana', 'Candra Gunawan', 'Dian Sastro',
            'Erik Estrada', 'Fitri Handayani', 'Galih Pangestu', 'Hesti Purwadinata', 'Irfan Bachdim',
            'Julia Perez', 'Kiki Fatmala', 'Lesti Kejora', 'Maulana Malik', 'Nina Tamam',
            'Oki Setiana', 'Putri Marino', 'Qibil Changcut', 'Raffi Ahmad', 'Syahrini Fatimah',
            'Tara Basro', 'Uus Rizky', 'Vicky Prasetyo', 'Wulan Guritno', 'Yayan Ruhian'
        ];

        DB::transaction(function () use ($periode, $kriterias, $names) {
            foreach ($names as $idx => $fullName) {
                $noReg = sprintf('26%06d', $idx + 101);
                $parts = explode(' ', $fullName);
                $initial = count($parts) > 1 ? $parts[0] . ' ' . substr($parts[1], 0, 1) . '.' : $fullName;

                $mhs = Mahasiswa::updateOrCreate(
                    ['nomor_pendaftaran' => $noReg],
                    ['nama_inisial' => $initial]
                );

                // Simulasi profil (ada yang sangat miskin & berprestasi tinggi, menengah, dan mampu)
                $isDeserving = ($idx % 3 === 0);
                $isMiddle = ($idx % 3 === 1);

                foreach ($kriterias as $k) {
                    $val = 0;
                    switch ($k->kode) {
                        case 'C1': // DTKS (1-4)
                            $val = $isDeserving ? rand(3, 4) : ($isMiddle ? rand(2, 3) : rand(1, 2));
                            break;
                        case 'C2': // P3KE (1-5)
                            $val = $isDeserving ? rand(1, 2) : ($isMiddle ? rand(2, 4) : rand(4, 5));
                            break;
                        case 'C3': // PKH (0/1)
                        case 'C4': // BPNT (0/1)
                        case 'C5': // PBI (0/1)
                        case 'C6': // KIP (0/1)
                        case 'C8': // BPUM (0/1)
                        case 'C9': // KIS (0/1)
                        case 'C10': // Sembako (0/1)
                        case 'C11': // Prakerja (0/1)
                            $val = $isDeserving ? (rand(1, 10) > 3 ? 1 : 0) : ($isMiddle ? (rand(1, 10) > 6 ? 1 : 0) : 0);
                            break;
                        case 'C7': // Prestasi (0-100)
                            $val = $isDeserving ? rand(75, 98) : ($isMiddle ? rand(65, 88) : rand(50, 75));
                            break;
                        case 'C12': // Ekonomi Ayah (Cost - misal penghasilan dalam rupiah ratusan ribu)
                            $val = $isDeserving ? rand(500000, 1500000) : ($isMiddle ? rand(1500000, 3000000) : rand(3000000, 6000000));
                            break;
                        case 'C13': // Ekonomi Ibu (Cost)
                            $val = $isDeserving ? rand(0, 800000) : ($isMiddle ? rand(500000, 1500000) : rand(1500000, 3000000));
                            break;
                        default:
                            $val = rand(1, 10);
                    }

                    Nilai::updateOrCreate(
                        [
                            'periode_id' => $periode->id,
                            'mahasiswa_id' => $mhs->id,
                            'kriteria_id' => $k->id,
                        ],
                        [
                            'nilai' => $val,
                        ]
                    );
                }
            }

            AuditService::log('SEED_DUMMY_MAHASISWA', [
                'periode_id' => $periode->id,
                'total_generated' => count($names),
            ]);
        });

        return redirect()->back()->with('success', 'Berhasil membangkitkan 50 data simulasi pendaftar beasiswa lengkap dengan nilai kriteria!');
    }
}
