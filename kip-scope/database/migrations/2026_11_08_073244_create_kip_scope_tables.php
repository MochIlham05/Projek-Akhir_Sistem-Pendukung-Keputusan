<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Periode Seleksi
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->unsignedInteger('kuota_penerima');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Kelompok Kriteria (G1 - G4)
        Schema::create('kelompok_kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10);
            $table->string('nama');
            $table->timestamps();
        });

        // 3. Master Kriteria (C1 - C13)
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok_kriterias')->cascadeOnDelete();
            $table->string('kode', 10);
            $table->string('nama');
            $table->enum('jenis', ['benefit', 'cost']);
            $table->timestamps();
        });

        // 4. Mahasiswa
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran')->unique();
            $table->string('nama_inisial');
            $table->timestamps();
        });

        // 5. Matriks Nilai Alternatif per Kriteria
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->decimal('nilai', 14, 2);
            $table->timestamps();
        });

        // 6. Matriks Perbandingan Berpasangan AHP
        Schema::create('perbandingan_ahps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->enum('tingkat', ['kelompok', 'kriteria']);
            $table->foreignId('kelompok_id')->nullable()->constrained('kelompok_kriterias')->cascadeOnDelete();
            $table->string('elemen_i', 10);
            $table->string('elemen_j', 10);
            $table->decimal('nilai_skala', 8, 4);
            $table->timestamps();
        });

        // 7. Hasil Pembobotan AHP
        Schema::create('bobot_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->decimal('bobot_lokal', 8, 6);
            $table->decimal('bobot_global', 8, 6);
            $table->decimal('cr', 8, 4);
            $table->timestamps();
        });

        // 8. Hasil Pemeringkatan MOORA
        Schema::create('hasil_mooras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->decimal('skor_y', 10, 6);
            $table->unsignedInteger('peringkat');
            $table->enum('status', ['lolos', 'tidak_lolos']);
            $table->timestamps();
        });

        // 9. Hasil Skenario Uji Sensitivitas
        Schema::create('skenario_sensitivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->string('nama_skenario', 20);
            $table->json('bobot_perubahan');
            $table->decimal('spearman_rho', 6, 4)->nullable();
            $table->decimal('top_k_overlap', 6, 2)->nullable();
            $table->timestamps();
        });

        // 10. Log Audit Jejak Perhitungan
        Schema::create('log_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi');
            $table->json('detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_audits');
        Schema::dropIfExists('skenario_sensitivitas');
        Schema::dropIfExists('hasil_mooras');
        Schema::dropIfExists('bobot_hasils');
        Schema::dropIfExists('perbandingan_ahps');
        Schema::dropIfExists('nilais');
        Schema::dropIfExists('mahasiswas');
        Schema::dropIfExists('kriterias');
        Schema::dropIfExists('kelompok_kriterias');
        Schema::dropIfExists('periodes');
    }
};