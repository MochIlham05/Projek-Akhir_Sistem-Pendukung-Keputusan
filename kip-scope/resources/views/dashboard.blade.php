<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-blue-900 border border-yellow-400">
                        Periode {{ $activePeriode->tahun }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">Sistem Pendukung Keputusan KIP-K</span>
                </div>
                <h2 class="font-extrabold text-2xl text-blue-950 leading-tight mt-1">
                    {{ __('Dashboard Analitik KIP-SCOPE') }}
                </h2>
                <p class="text-xs text-slate-500">
                    Pemeringkatan Vektor MOORA & Uji Sensitivitas Kestabilan (S0 - S7)
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <form action="{{ route('system.reset-all') }}" method="POST" 
                    onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mereset SELURUH data sistem?\n\nAksi ini akan menghapus:\n- Semua data pendaftar & nilai kriteria\n- Matriks & bobot hasil AHP\n- Hasil pemeringkatan MOORA & uji sensitivitas\n\nSistem akan kembali bersih ke kondisi awal.');">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <button type="submit" 
                        class="bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold py-2.5 px-3.5 rounded-lg border border-rose-200 shadow-sm text-xs flex items-center gap-1.5 transition"
                        title="Kosongkan seluruh data mahasiswa, bobot AHP, dan pemeringkatan MOORA">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Reset Semua Data</span>
                    </button>
                </form>

                <form action="{{ route('moora.calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <button type="submit" 
                        class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg text-sm flex items-center gap-2 border border-yellow-400 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Hitung Pemeringkatan MOORA</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div class="bg-blue-50 border-l-4 border-blue-600 text-blue-950 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                        &#10003;
                    </div>
                    <div>
                        <div class="font-bold text-sm text-blue-900">Operasi Berhasil</div>
                        <div class="text-xs text-blue-800">{{ session('success') }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-950 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-sm">
                        !
                    </div>
                    <div>
                        <div class="font-bold text-sm text-rose-900">Pemberitahuan</div>
                        <div class="text-xs text-rose-800">{{ session('error') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Statistik Ringkasan (Dominan Putih, Biru, Kuning) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Pendaftar -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-blue-600 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendaftar</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-blue-950 mt-2">{{ $totalMahasiswa }} Mahasiswa</div>
                <div class="mt-2 text-xs">
                    <a href="{{ route('mahasiswa.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                        <span>Kelola Data Mandiri</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- 2. Jumlah Kriteria -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-blue-800 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Kriteria</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-900 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-blue-950 mt-2">{{ $totalKriteria }} Kriteria</div>
                <div class="mt-2 text-xs text-slate-500 font-medium">
                    4 Kelompok (C1 s/d C13)
                </div>
            </div>

            <!-- 3. Kuota Beasiswa -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-yellow-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuota Beasiswa</span>
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-800 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                </div>
                <form action="{{ route('mahasiswa.update-kuota') }}" method="POST" class="flex items-center gap-2 mt-2">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <input type="number" name="kuota_penerima" value="{{ $activePeriode->kuota_penerima }}" min="1" max="10000" class="w-20 text-lg font-bold text-blue-950 py-0.5 px-2 border border-slate-300 rounded focus:ring-yellow-400 focus:border-yellow-400">
                    <button type="submit" class="text-xs bg-yellow-400 hover:bg-yellow-500 text-blue-950 font-bold py-1.5 px-3 rounded shadow transition">
                        Update
                    </button>
                </form>
                <div class="mt-1 text-[11px] text-slate-500">
                    Batas atas peringkat penerima lolos
                </div>
            </div>

            <!-- 4. Consistency Ratio (CR) -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-yellow-500 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Consistency Ratio (CR)</span>
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-800 flex items-center justify-center font-bold">
                        &lambda;
                    </div>
                </div>
                <div class="text-2xl font-extrabold {{ $crAhp !== null && $crAhp <= 0.1 ? 'text-blue-900' : 'text-slate-400' }} mt-2">
                    {{ $crAhp !== null ? number_format($crAhp, 3) : 'Belum dihitung' }}
                </div>
                <div class="mt-2 text-xs">
                    <a href="{{ route('ahp.index') }}" class="text-yellow-700 hover:text-yellow-800 font-bold inline-flex items-center gap-1">
                        <span>{{ $crAhp !== null ? 'Lihat Matriks AHP' : 'Atur Matriks AHP' }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Banner Panduan Alur Integrasi (Biru - Kuning - Putih) -->
        <div class="bg-white rounded-xl p-5 border border-yellow-300 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-400 text-blue-950 flex items-center justify-center font-black text-lg shadow-sm">
                        i
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-blue-950">Alur Penerapan Sistem Pendukung Keputusan KIP-SCOPE</h4>
                        <p class="text-xs text-slate-600">Pastikan seluruh langkah berikut terintegrasi sebelum melihat hasil akhir:</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold {{ $totalMahasiswa > 0 ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-slate-100 text-slate-500' }}">
                        <span>1. Data Mahasiswa:</span>
                        <span>{{ $totalMahasiswa > 0 ? $totalMahasiswa . ' Terdaftar' : 'Belum Ada' }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold {{ $crAhp !== null ? 'bg-yellow-100 text-blue-950 border border-yellow-400' : 'bg-slate-100 text-slate-500' }}">
                        <span>2. Pembobotan AHP:</span>
                        <span>{{ $crAhp !== null ? 'CR ' . number_format($crAhp, 3) : 'Belum Dihitung' }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold {{ count($hasilMoora) > 0 ? 'bg-blue-800 text-yellow-300 border border-blue-900' : 'bg-slate-100 text-slate-500' }}">
                        <span>3. MOORA:</span>
                        <span>{{ count($hasilMoora) > 0 ? count($hasilMoora) . ' Peringkat' : 'Siap Dihitung' }}</span>
                    </span>
                </div>
            </div>
        </div>

        @if(count($hasilMoora) > 0)
            <!-- Baris Visualisasi Grafik (Chart.js) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Grafik Distribusi Skor Optimasi y_i -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-sm font-extrabold text-blue-950">Distribusi Skor Kelayakan ($y_i$)</h3>
                            <p class="text-[11px] text-slate-500">15 Pendaftar Peringkat Teratas</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-900">
                            Normalisasi Vektor
                        </span>
                    </div>
                    <div class="h-64">
                        <canvas id="scoreDistChart"></canvas>
                    </div>
                </div>

                <!-- Grafik Kestabilan Sensitivitas Spearman Rho -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-sm font-extrabold text-blue-950">Uji Sensitivitas (Korelasi Spearman $\rho$)</h3>
                            <p class="text-[11px] text-slate-500">Evaluasi S0 s/d S7 terhadap Peringkat Acuan</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-yellow-100 text-yellow-900 border border-yellow-300">
                            Ambang Batas &ge; 0.90
                        </span>
                    </div>
                    <div class="h-64">
                        <canvas id="sensitivityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabel Peringkat Rekomendasi MOORA -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-blue-950">Tabel Rekomendasi Penerima Beasiswa MOORA</h3>
                        <p class="text-xs text-slate-500">
                            Garis tebal kuning memisahkan pendaftar yang masuk kuota ({{ $activePeriode->kuota_penerima }} teratas) dengan yang tidak lolos kuota.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-yellow-100 text-blue-950 font-bold px-3 py-1 rounded-full border border-yellow-400">
                            {{ $hasilMoora->where('status', 'lolos')->count() }} Lolos Kuota
                        </span>
                        <span class="text-xs bg-slate-100 text-slate-700 font-semibold px-3 py-1 rounded-full border border-slate-300">
                            {{ $hasilMoora->where('status', 'tidak_lolos')->count() }} Belum Lolos
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[500px]">
                    <table class="min-w-full text-xs divide-y divide-slate-200 border text-center">
                        <thead class="bg-blue-800 text-white sticky top-0 shadow">
                            <tr>
                                <th class="py-3 px-3">Peringkat</th>
                                <th class="py-3 px-3">Nomor Pendaftaran</th>
                                <th class="py-3 px-3 text-left">Nama / Inisial</th>
                                <th class="py-3 px-3 font-bold text-yellow-300">Skor Optimasi ($y_i$)</th>
                                <th class="py-3 px-3">Status Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($hasilMoora as $h)
                                @php
                                    $isBorder = ($h->peringkat == $activePeriode->kuota_penerima);
                                @endphp
                                <tr class="hover:bg-slate-50 transition {{ $h->status === 'lolos' ? 'bg-yellow-50/20' : '' }} {{ $isBorder ? 'border-b-4 border-yellow-400 font-semibold' : '' }}">
                                    <td class="py-2.5 px-3 font-bold text-blue-950">#{{ $h->peringkat }}</td>
                                    <td class="py-2.5 px-3 font-mono font-medium text-slate-700">{{ $h->mahasiswa->nomor_pendaftaran ?? '-' }}</td>
                                    <td class="py-2.5 px-3 text-left font-bold text-slate-900">{{ $h->mahasiswa->nama_inisial ?? '-' }}</td>
                                    <td class="py-2.5 px-3 font-mono font-extrabold text-blue-800 text-sm">
                                        {{ number_format($h->skor_y, 6) }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($h->status === 'lolos')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-yellow-400 text-blue-950 border border-yellow-500 shadow-sm">
                                                Lolos Kuota
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-300">
                                                Tidak Lolos
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Rincian Skenario Sensitivitas (S0 - S7) -->
            @if(count($skenarios) > 0)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h3 class="text-base font-extrabold text-blue-950">Rincian Hasil Uji Sensitivitas Kestabilan (S0 - S7)</h3>
                            <p class="text-xs text-slate-500">Membuktikan bahwa variasi bobot kebijakan tidak mengubah urutan secara drastis.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-slate-200 border text-center">
                            <thead class="bg-slate-100 text-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3">Skenario</th>
                                    <th class="py-2.5 px-3 text-left">Deskripsi Uji</th>
                                    <th class="py-2.5 px-3 font-bold text-blue-900">Spearman Rho (&rho;)</th>
                                    <th class="py-2.5 px-3 font-bold text-yellow-800">Top-k Overlap (%)</th>
                                    <th class="py-2.5 px-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @php
                                    $scenarioDescriptions = [
                                        'S0' => 'Bobot Dasar Hasil AHP (Acuan Standar)',
                                        'S1' => 'G1 (Kesejahteraan DTKS & P3KE) Naik +20%',
                                        'S2' => 'G1 (Kesejahteraan DTKS & P3KE) Turun -20%',
                                        'S3' => 'G3 (Ekonomi Orang Tua) Naik +20%',
                                        'S4' => 'G3 (Ekonomi Orang Tua) Turun -20%',
                                        'S5' => 'G4 (Prestasi Mahasiswa) Naik +20%',
                                        'S6' => 'G4 (Prestasi Mahasiswa) Turun -20%',
                                        'S7' => 'Bobot Sama Rata (Equal Weights 100/13)',
                                    ];
                                @endphp
                                @foreach($skenarios as $scen)
                                    @php
                                        $isStable = ($scen->spearman_rho >= 0.90 && $scen->top_k_overlap >= 90.0);
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2 px-3 font-extrabold text-blue-900">{{ $scen->nama_skenario }}</td>
                                        <td class="py-2 px-3 text-left font-medium text-slate-700">{{ $scenarioDescriptions[$scen->nama_skenario] ?? '-' }}</td>
                                        <td class="py-2 px-3 font-mono font-bold text-blue-800">{{ $scen->spearman_rho }}</td>
                                        <td class="py-2 px-3 font-mono font-bold text-yellow-800">{{ $scen->top_k_overlap }}%</td>
                                        <td class="py-2 px-3">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $isStable ? 'bg-yellow-100 text-blue-950 border border-yellow-400' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $isStable ? 'Sangat Stabil (>= 0.90)' : 'Cukup Stabil' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <!-- Tampilan Bersih Ketika Belum Ada Data (Data Dummy Sudah Dihapus) -->
            <div class="bg-white p-12 rounded-xl shadow-sm border border-slate-200 text-center max-w-3xl mx-auto">
                <div class="w-20 h-20 rounded-full bg-blue-50 border-4 border-yellow-400 text-blue-700 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-extrabold text-blue-950">Data Mahasiswa Siap Diinputkan Mandiri</h3>
                <p class="mt-2 text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                    Data simulasi/dummy telah berhasil dibersihkan. Anda sekarang dapat menambahkan data calon penerima beasiswa KIP-K sendiri menggunakan berkas CSV atau formulir manual.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('mahasiswa.index') }}" 
                        class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-5 rounded-lg shadow-md text-xs flex items-center justify-center gap-2 border border-yellow-400 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>1. Tambah / Impor Data Mahasiswa</span>
                    </a>
                    
                    <a href="{{ route('ahp.index') }}" 
                        class="bg-yellow-400 hover:bg-yellow-500 text-blue-950 font-bold py-2.5 px-5 rounded-lg shadow text-xs flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>2. Verifikasi Bobot AHP</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Script Chart.js -->
    @if(count($hasilMoora) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // 1. Chart Distribusi Skor Top 15 (Warna Dominan Biru & Aksen Kuning)
            const scoreLabels = @json($hasilMoora->take(15)->map(fn($h) => $h->mahasiswa->nomor_pendaftaran ?? '#' . $h->peringkat));
            const scoreValues = @json($hasilMoora->take(15)->pluck('skor_y'));

            const scoreCtx = document.getElementById('scoreDistChart');
            if (scoreCtx) {
                new Chart(scoreCtx, {
                    type: 'bar',
                    data: {
                        labels: scoreLabels,
                        datasets: [{
                            label: 'Skor Optimasi (y_i)',
                            data: scoreValues,
                            backgroundColor: '#1e40af', // Royal Blue
                            hoverBackgroundColor: '#eab308', // Gold / Yellow
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: false, ticks: { font: { size: 10 } } },
                            x: { ticks: { font: { size: 9 } } }
                        }
                    }
                });
            }

            // 2. Chart Sensitivitas (Spearman Rho)
            const scenLabels = @json($skenarios->pluck('nama_skenario'));
            const rhoValues = @json($skenarios->pluck('spearman_rho'));

            const sensCtx = document.getElementById('sensitivityChart');
            if (sensCtx) {
                new Chart(sensCtx, {
                    type: 'line',
                    data: {
                        labels: scenLabels,
                        datasets: [{
                            label: 'Korelasi Spearman (ρ)',
                            data: rhoValues,
                            borderColor: '#1e40af', // Royal Blue
                            backgroundColor: 'rgba(234, 179, 8, 0.15)', // Light Yellow
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#eab308', // Yellow
                            pointBorderColor: '#1e40af',
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { min: 0.85, max: 1.01, ticks: { font: { size: 10 } } },
                            x: { ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
