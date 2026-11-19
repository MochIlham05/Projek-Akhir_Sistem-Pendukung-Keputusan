<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-blue-900 border border-yellow-400">
                        Tahap 4
                    </span>
                    <span class="text-xs text-slate-500 font-medium">Hierarki Dua Tingkat AHP</span>
                </div>
                <h2 class="font-extrabold text-2xl text-blue-950 leading-tight mt-1">
                    {{ __('Matriks Perbandingan Berpasangan AHP') }}
                </h2>
                <p class="text-xs text-slate-500">
                    Pembobotan Kriteria dengan Uji Rasio Konsistensi (CR &le; 0.1) & Bobot Global ($w_j$)
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('system.reset-all') }}" method="POST" 
                    onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mereset SELURUH data sistem?\n\nAksi ini akan menghapus:\n- Semua data pendaftar & nilai kriteria\n- Matriks & bobot hasil AHP\n- Hasil pemeringkatan MOORA & uji sensitivitas\n\nSistem akan kembali bersih ke kondisi awal.');">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <button type="submit" 
                        class="bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold py-2 px-3 rounded-lg border border-rose-200 shadow-sm text-xs flex items-center gap-1.5 transition"
                        title="Kosongkan seluruh data sistem">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Reset Data</span>
                    </button>
                </form>

                <form action="{{ route('ahp.preset') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <button type="submit" 
                        class="bg-yellow-400 hover:bg-yellow-500 text-blue-950 font-bold py-2 px-3.5 rounded-lg shadow text-xs flex items-center gap-1.5 transition transform hover:-translate-y-0.5" 
                        title="Isi otomatis dengan matriks perbandingan konsisten acuan dokumen proposal">
                        <svg class="w-4 h-4 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Gunakan Bobot Konsisten Standar</span>
                    </button>
                </form>

                @if(count($bobotHasil) > 0)
                    <a href="{{ route('dashboard') }}" 
                        class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-3.5 rounded-lg shadow text-xs flex items-center gap-1.5 border border-yellow-400 transition transform hover:-translate-y-0.5">
                        <span>Lanjut ke Dashboard & MOORA</span>
                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endif
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
                        <div class="font-bold text-sm text-blue-900">Perhitungan Konsisten</div>
                        <div class="text-xs text-blue-800">{{ session('success') }}</div>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="text-xs bg-yellow-400 hover:bg-yellow-500 text-blue-950 px-3.5 py-1.5 rounded-lg font-bold shadow transition">
                    Ke Dashboard MOORA &rarr;
                </a>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-950 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-sm">
                        !
                    </div>
                    <div>
                        <div class="font-bold text-sm text-rose-900">Matriks Belum Konsisten</div>
                        <div class="text-xs text-rose-800">{{ session('error') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Informasi Skala Saaty (Dominan Putih & Kuning) -->
        <div class="bg-white border-2 border-yellow-300 rounded-xl p-5 text-xs text-slate-700 shadow-sm">
            <div class="font-bold mb-1 flex items-center gap-2 text-blue-950 text-sm">
                <div class="w-6 h-6 rounded-full bg-yellow-400 text-blue-950 flex items-center justify-center font-extrabold text-xs">
                    i
                </div>
                <span>Panduan Skala Perbandingan Berpasangan Saaty (1 - 9)</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-3">
                <div class="bg-yellow-50/60 p-2 rounded-lg border border-yellow-200"><strong class="text-blue-950">1:</strong> Sama penting (Equal)</div>
                <div class="bg-yellow-50/60 p-2 rounded-lg border border-yellow-200"><strong class="text-blue-950">3:</strong> Sedikit lebih penting</div>
                <div class="bg-yellow-50/60 p-2 rounded-lg border border-yellow-200"><strong class="text-blue-950">5:</strong> Lebih penting (Strong)</div>
                <div class="bg-yellow-50/60 p-2 rounded-lg border border-yellow-200"><strong class="text-blue-950">7:</strong> Sangat penting</div>
                <div class="bg-yellow-50/60 p-2 rounded-lg border border-yellow-200"><strong class="text-blue-950">9:</strong> Mutlak lebih penting</div>
            </div>
            <p class="mt-3 text-slate-500 text-[11px]">
                *Nilai resiprokal (1/x) pada posisi diagonal terbalik otomatis dihitung dan diisi oleh sistem secara instan.
            </p>
        </div>

        <form action="{{ route('ahp.calculate') }}" method="POST">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">

            <!-- 1. Perbandingan Antar-Kelompok (G1 - G4) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-extrabold text-blue-950">Tingkat 1: Perbandingan Antar-Kelompok Kriteria (G1 - G4)</h3>
                    <span class="text-xs bg-blue-100 text-blue-900 font-bold px-3 py-1 rounded-full border border-blue-300">
                        4 Kelompok Kriteria
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    G1: Kesejahteraan | G2: Bantuan Sosial | G3: Kondisi Ekonomi Orang Tua | G4: Prestasi
                </p>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full text-center text-xs divide-y divide-slate-200 border">
                        <thead class="bg-blue-800 text-white">
                            <tr>
                                <th class="py-3 px-3">Kelompok</th>
                                @foreach($groups as $g)
                                    <th class="py-3 px-3 font-bold">{{ $g->kode }} ({{ $g->nama }})</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($groups as $gi)
                                <tr class="hover:bg-yellow-50/20">
                                    <td class="font-extrabold bg-slate-50 py-2.5 px-3 text-blue-950">{{ $gi->kode }}</td>
                                    @foreach($groups as $gj)
                                        <td class="p-2">
                                            @if($gi->kode === $gj->kode)
                                                <input type="text" name="group_matrix[{{ $gi->kode }}][{{ $gj->kode }}]" value="1" readonly 
                                                    class="w-16 text-center text-xs bg-slate-100 border-slate-300 rounded font-bold text-slate-500">
                                            @else
                                                <input type="number" step="any" min="0.01" max="9" 
                                                    id="grp_{{ $gi->kode }}_{{ $gj->kode }}"
                                                    name="group_matrix[{{ $gi->kode }}][{{ $gj->kode }}]" 
                                                    value="{{ old("group_matrix.{$gi->kode}.{$gj->kode}", $compMap[$gi->kode][$gj->kode] ?? 1) }}"
                                                    onchange="updateReciprocal('grp', '{{ $gi->kode }}', '{{ $gj->kode }}')"
                                                    class="w-20 text-center text-xs border-slate-300 rounded shadow-sm focus:ring-yellow-400 focus:border-yellow-400 font-mono font-bold text-blue-950">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Perbandingan Kriteria di Dalam Tiap Kelompok -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <h3 class="text-base font-extrabold text-blue-950 mb-1">Tingkat 2: Perbandingan Lokal Antar-Kriteria dalam Kelompok</h3>
                <p class="text-xs text-slate-500 mb-4">
                    Kelompok dengan hanya 1 kriteria (G4: Prestasi) memiliki bobot lokal otomatis 1.0.
                </p>
                
                @foreach($groups as $g)
                    @if(count($g->kriterias) > 1)
                        <div class="mt-5 border-t border-slate-200 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                    <span>{{ $g->kode }}: {{ $g->nama }} ({{ count($g->kriterias) }} Kriteria)</span>
                                </h4>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-center text-xs divide-y divide-slate-200 border">
                                    <thead class="bg-slate-100 text-slate-800">
                                        <tr>
                                            <th class="py-2.5 px-3">Kriteria</th>
                                            @foreach($g->kriterias as $ck)
                                                <th class="py-2.5 px-3 font-bold text-blue-950" title="{{ $ck->nama }}">{{ $ck->kode }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach($g->kriterias as $ci)
                                            <tr class="hover:bg-yellow-50/20">
                                                <td class="font-extrabold bg-slate-50 py-2 px-3 text-blue-950" title="{{ $ci->nama }}">{{ $ci->kode }}</td>
                                                @foreach($g->kriterias as $cj)
                                                    <td class="p-2">
                                                        @if($ci->kode === $cj->kode)
                                                            <input type="text" name="local_matrix[{{ $g->kode }}][{{ $ci->kode }}][{{ $cj->kode }}]" value="1" readonly 
                                                                class="w-16 text-center text-xs bg-slate-100 border-slate-300 rounded font-bold text-slate-500">
                                                        @else
                                                            <input type="number" step="any" min="0.01" max="9"
                                                                id="loc_{{ $g->kode }}_{{ $ci->kode }}_{{ $cj->kode }}"
                                                                name="local_matrix[{{ $g->kode }}][{{ $ci->kode }}][{{ $cj->kode }}]"
                                                                value="{{ old("local_matrix.{$g->kode}.{$ci->kode}.{$cj->kode}", $compMap[$ci->kode][$cj->kode] ?? 1) }}"
                                                                onchange="updateLocalReciprocal('{{ $g->kode }}', '{{ $ci->kode }}', '{{ $cj->kode }}')"
                                                                class="w-20 text-center text-xs border-slate-300 rounded shadow-sm focus:ring-yellow-400 focus:border-yellow-400 font-mono font-bold text-blue-950">
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                        class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-md text-xs flex items-center gap-2 border border-yellow-400 transition transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Hitung Bobot AHP & Validasi Konsistensi (CR)</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Hasil Bobot Tersimpan (Dominan Putih, Biru, Kuning) -->
        @if(count($bobotHasil) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-blue-950">Hasil Bobot Global Tersimpan (AHP Terverifikasi)</h3>
                        <p class="text-xs text-slate-500">Bobot global ($w_j$) diperoleh dari perkalian bobot kelompok dengan bobot lokal kriteria.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-blue-950 border border-yellow-400">
                            CR Kelompok: {{ $bobotHasil->first()->cr }} (Konsisten &le; 0.1)
                        </span>
                        <a href="{{ route('dashboard') }}" 
                            class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-1.5 px-3.5 rounded-lg text-xs shadow flex items-center gap-1 transition">
                            <span>Lanjut ke MOORA</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs divide-y divide-slate-200 border text-center">
                        <thead class="bg-blue-800 text-white">
                            <tr>
                                <th class="py-3 px-3">Kode Kriteria</th>
                                <th class="py-3 px-3 text-left">Nama Kriteria</th>
                                <th class="py-3 px-3">Kelompok</th>
                                <th class="py-3 px-3">Sifat Kriteria</th>
                                <th class="py-3 px-3 font-semibold">Bobot Lokal</th>
                                <th class="py-3 px-3 font-bold text-yellow-300">Bobot Global ($w_j$)</th>
                                <th class="py-3 px-3">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($bobotHasil as $b)
                                <tr class="hover:bg-yellow-50/20">
                                    <td class="py-2.5 px-3 font-bold text-blue-950">{{ $b->kriteria->kode }}</td>
                                    <td class="py-2.5 px-3 text-left font-medium text-slate-800">{{ $b->kriteria->nama }}</td>
                                    <td class="py-2.5 px-3 text-slate-600">{{ $b->kriteria->kelompok->nama ?? '-' }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $b->kriteria->jenis === 'benefit' ? 'bg-blue-100 text-blue-900' : 'bg-yellow-100 text-yellow-900 border border-yellow-300' }}">
                                            {{ $b->kriteria->jenis }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono font-medium text-slate-700">{{ number_format($b->bobot_lokal, 4) }}</td>
                                    <td class="py-2.5 px-3 font-mono font-extrabold text-blue-800 text-sm">{{ number_format($b->bobot_global, 4) }}</td>
                                    <td class="py-2.5 px-3 font-mono text-slate-500 font-semibold">{{ number_format($b->bobot_global * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300">
                            <tr>
                                <td colspan="5" class="py-3 px-3 text-right text-slate-700">Total Bobot Global ($\sum w_j$):</td>
                                <td class="py-3 px-3 font-mono text-blue-900 text-sm font-extrabold">{{ number_format($bobotHasil->sum('bobot_global'), 4) }}</td>
                                <td class="py-3 px-3 font-mono text-yellow-800 text-sm font-extrabold">{{ number_format($bobotHasil->sum('bobot_global') * 100, 1) }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        function updateReciprocal(prefix, i, j) {
            const valInput = document.getElementById(`${prefix}_${i}_${j}`);
            const recipInput = document.getElementById(`${prefix}_${j}_${i}`);
            const val = parseFloat(valInput.value);
            if (val && val > 0 && recipInput) {
                recipInput.value = (1 / val).toFixed(4);
            }
        }

        function updateLocalReciprocal(group, i, j) {
            const valInput = document.getElementById(`loc_${group}_${i}_${j}`);
            const recipInput = document.getElementById(`loc_${group}_${j}_${i}`);
            const val = parseFloat(valInput.value);
            if (val && val > 0 && recipInput) {
                recipInput.value = (1 / val).toFixed(4);
            }
        }
    </script>
</x-app-layout>
