<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-blue-900 border border-yellow-400">
                        Tahap 3
                    </span>
                    <span class="text-xs text-slate-500 font-medium">Periode Seleksi {{ $activePeriode->tahun }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-blue-950 leading-tight mt-1">
                    {{ __('Pengelolaan Data Calon Penerima Beasiswa') }}
                </h2>
                <p class="text-xs text-slate-500">
                    Input & Impor Data Mahasiswa Mandiri Beserta Matriks Nilai Multikriteria (C1 - C13)
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

                <a href="{{ route('ahp.index') }}" 
                    class="bg-yellow-400 hover:bg-yellow-500 text-blue-950 font-bold py-2.5 px-4 rounded-lg shadow text-xs flex items-center gap-2 transition transform hover:-translate-y-0.5">
                    <span>Lanjut ke Pembobotan AHP</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
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
                        <div class="font-bold text-sm text-blue-900">Sukses</div>
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
                        <div class="font-bold text-sm text-rose-900">Perhatian</div>
                        <div class="text-xs text-rose-800">{{ session('error') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Statistik Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-blue-600">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendaftar</div>
                <div class="text-2xl font-extrabold text-blue-950 mt-1">{{ count($mahasiswas) }} Mahasiswa</div>
                <div class="text-xs text-slate-500 mt-1">Data tersimpan di sistem</div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-blue-800">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Kriteria</div>
                <div class="text-2xl font-extrabold text-blue-950 mt-1">{{ count($kriterias) }} Kriteria</div>
                <div class="text-xs text-slate-500 mt-1">4 Kelompok (Benefit & Cost)</div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-yellow-400">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuota Penerima</div>
                <form action="{{ route('mahasiswa.update-kuota') }}" method="POST" class="flex items-center gap-2 mt-1">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                    <input type="number" name="kuota_penerima" value="{{ $activePeriode->kuota_penerima }}" min="1" max="10000" class="w-20 text-lg font-bold text-blue-950 py-0.5 px-2 border border-slate-300 rounded focus:ring-yellow-400 focus:border-yellow-400">
                    <button type="submit" class="text-xs bg-yellow-400 hover:bg-yellow-500 text-blue-950 font-bold py-1.5 px-3 rounded shadow transition">
                        Update
                    </button>
                </form>
                <div class="text-xs text-slate-500 mt-1">Batas kuota yang berstatus lolos</div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 border-t-4 border-t-yellow-500 flex flex-col justify-between">
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Seleksi</div>
                    <div class="text-2xl font-extrabold text-blue-950 mt-1">{{ $activePeriode->tahun }}</div>
                </div>
                <div class="text-xs text-blue-800 font-semibold mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Database Terhubung</span>
                </div>
            </div>
        </div>

        <!-- Bagian Input Data: Impor CSV & Input Manual -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 1. Impor File CSV Mandiri -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-extrabold text-blue-950 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <span>Impor Data via Berkas CSV</span>
                        </h3>
                        <a href="{{ route('mahasiswa.template') }}" 
                            class="text-xs bg-yellow-50 text-blue-950 border border-yellow-300 hover:bg-yellow-100 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Template CSV</span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                        Siapkan berkas spreadsheet CSV Anda dengan urutan kolom: <br>
                        <code class="bg-slate-100 text-blue-900 px-1.5 py-0.5 rounded text-[11px] font-mono font-bold">nomor_pendaftaran, nama_inisial, c1, c2, c3, ..., c13</code>.
                    </p>

                    <form action="{{ route('mahasiswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                        
                        <div class="border-2 border-dashed border-blue-200 rounded-xl p-5 text-center hover:border-blue-500 transition bg-blue-50/30">
                            <input type="file" name="file" accept=".csv,.txt" required 
                                class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-700 file:text-white hover:file:bg-blue-800 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-2">Mendukung file .CSV pemisah koma (,) atau titik koma (;)</p>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <button type="submit" 
                                class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-5 rounded-lg text-xs shadow-md flex items-center gap-2 border border-yellow-400 transition transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Unggah & Simpan Data CSV</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Opsi Tambahan / Reset -->
                <div class="border-t border-slate-200 pt-4 mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50 -mx-6 -mb-6 p-4 rounded-b-xl">
                    <span class="text-xs text-slate-500">
                        Total saat ini: <strong>{{ count($mahasiswas) }}</strong> pendaftar
                    </span>

                    <form action="{{ route('system.reset-all') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mereset SELURUH data sistem ke kondisi awal?');" class="inline">
                        @csrf
                        <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">
                        <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Kosongkan / Reset Semua Data</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2. Tambah Data Mahasiswa Manual -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-800 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-blue-950">Tambah Data Pendaftar Manual</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Masukkan identitas satu per satu dan isi nilai setiap kriteria evaluasi (C1 - C13).
                </p>

                <form action="{{ route('mahasiswa.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $activePeriode->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Pendaftaran / NISN</label>
                            <input type="text" name="nomor_pendaftaran" placeholder="Misal: 26010001" required 
                                class="w-full text-xs border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama / Inisial Mahasiswa</label>
                            <input type="text" name="nama_inisial" placeholder="Misal: Ahmad S." required 
                                class="w-full text-xs border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2">
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-blue-950">Nilai Kriteria (C1 s/d C13):</label>
                            <span class="text-[10px] text-slate-400">Benefit (makin besar makin baik) / Cost (makin kecil makin baik)</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 max-h-56 overflow-y-auto p-1.5 border border-slate-200 rounded-lg bg-slate-50">
                            @foreach($kriterias as $k)
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200 hover:border-yellow-400 transition">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="font-bold text-blue-900">{{ $k->kode }}</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ $k->jenis === 'benefit' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800 border border-yellow-300' }}">
                                            {{ $k->jenis }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 truncate mb-1" title="{{ $k->nama }}">{{ $k->nama }}</div>
                                    <input type="number" step="any" name="nilai[{{ $k->id }}]" value="0" required 
                                        class="w-full text-xs text-center border-slate-300 rounded py-1 px-1 focus:ring-blue-500 font-mono font-bold">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" 
                            class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2.5 px-5 rounded-lg text-xs shadow-md flex items-center gap-1.5 border border-yellow-400 transition transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Mahasiswa Baru</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Mahasiswa & Matriks Nilai (Dominan Putih, Header Biru) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-blue-950">Matriks Keputusan Alternatif Calon Penerima KIP-K</h3>
                    <p class="text-xs text-slate-500">Nilai mentah pendaftar yang akan dinormalisasi dan dioptimasi dengan vektor MOORA.</p>
                </div>
                @if(count($mahasiswas) > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-blue-100 text-blue-900 font-bold px-3 py-1 rounded-full border border-blue-300">
                            {{ count($mahasiswas) }} Data Terdaftar
                        </span>
                    </div>
                @endif
            </div>

            @if(count($mahasiswas) > 0)
                <div class="overflow-x-auto max-h-[500px]">
                    <table class="min-w-full text-xs divide-y divide-slate-200 border text-center">
                        <thead class="bg-blue-800 text-white sticky top-0 shadow">
                            <tr>
                                <th class="py-3 px-3">No</th>
                                <th class="py-3 px-3">No. Pendaftaran</th>
                                <th class="py-3 px-3 text-left">Nama / Inisial</th>
                                @foreach($kriterias as $k)
                                    <th class="py-3 px-2" title="{{ $k->nama }} ({{ $k->jenis }})">
                                        <div class="font-bold text-yellow-300">{{ $k->kode }}</div>
                                        <div class="text-[9px] font-medium text-blue-200 uppercase">{{ $k->jenis }}</div>
                                    </th>
                                @endforeach
                                <th class="py-3 px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($mahasiswas as $index => $mhs)
                                @php
                                    $nilaiMap = $mhs->nilais->pluck('nilai', 'kriteria_id')->toArray();
                                @endphp
                                <tr class="hover:bg-yellow-50/30 transition">
                                    <td class="py-2.5 px-3 text-slate-500">{{ $index + 1 }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-blue-950 font-mono">{{ $mhs->nomor_pendaftaran }}</td>
                                    <td class="py-2.5 px-3 text-left font-bold text-slate-800">{{ $mhs->nama_inisial }}</td>
                                    @foreach($kriterias as $k)
                                        <td class="py-2.5 px-2 font-mono text-slate-700">
                                            @if(isset($nilaiMap[$k->id]))
                                                {{ $nilaiMap[$k->id] >= 1000 ? number_format($nilaiMap[$k->id], 0, ',', '.') : $nilaiMap[$k->id] }}
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="py-2.5 px-3">
                                        <form action="{{ route('mahasiswa.destroy', $mhs->id) }}" method="POST" onsubmit="return confirm('Hapus data pendaftar ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs hover:underline" title="Hapus pendaftar">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Tampilan Bersih Ketika Belum Ada Data -->
                <div class="text-center py-14 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
                    <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-blue-950">Belum Ada Data Calon Penerima KIP-K</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Silakan unggah berkas spreadsheet CSV Anda melalui formulir di atas, atau masukkan data mahasiswa secara manual.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
