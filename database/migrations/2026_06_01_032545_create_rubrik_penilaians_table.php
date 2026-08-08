<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rubrik_penilaians', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | KLASIFIKASI
            |--------------------------------------------------------------------------
            */
            $table->string('bidang_prestasi'); // Akademik, Non Akademik, Keagamaan, GTK, Lembaga

            // Sub-tipe DI DALAM satu bidang -- soalnya GTK sendiri punya 2 pola beda
            // (Lomba vs Karya Tulis), begitu juga Lembaga punya banyak jenis kriteria.
            $table->enum('jenis_rubrik', ['Lomba', 'Karya', 'Kelembagaan', 'Hafalan']);

            /*
            |--------------------------------------------------------------------------
            | KRITERIA TERSTRUKTUR -- dipakai kategori "Lomba" (siswa & GTK-lomba).
            | SEMUA nullable, karena kategori lain (Karya/Kelembagaan/Hafalan) tidak
            | memakai kolom-kolom ini sama sekali.
            |--------------------------------------------------------------------------
            */
            $table->string('tingkat')->nullable();       // Kabupaten/Kota, Provinsi, Nasional, Internasional
            $table->string('juara')->nullable();          // Juara 1, Juara 2, Juara 3, Harapan 1, Harapan 2, Harapan 3
            $table->string('kategori_kegiatan')->nullable(); // Individu, Beregu
            $table->string('metode_pelaksanaan')->nullable(); // Luring, Daring (GTK-lomba TIDAK punya dimensi ini)
            $table->string('kategori_penyelenggara')->nullable(); // Pemerintah, Non Pemerintah

            /*
            |--------------------------------------------------------------------------
            | KRITERIA FLEKSIBEL -- dipakai kategori "Karya" & "Kelembagaan", yang
            | tidak punya struktur baku (namanya beda-beda, bukan kombinasi
            | tingkat x juara). Contoh isi kriteria_khusus: "Penulis Jurnal ISSN
            | Terindeks Scopus", "Adiwiyata", "Zona Integritas - Lolos Tim Nasional".
            |--------------------------------------------------------------------------
            */
            $table->string('kriteria_khusus')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RENTANG ANGKA -- dipakai untuk kriteria berbasis rentang/hitungan,
            | BUKAN pencocokan teks persis. Dua contoh nyata di Juknis:
            | - Hafalan Qur'an: nilai_min = nilai_max = jumlah juz (1-30)
            | - Lembaga "Serapan Lulusan MA ke PTN": nilai_min/nilai_max = rentang %
            |   (misal 80.00 - 89.99 = skor 300)
            |--------------------------------------------------------------------------
            */
            $table->decimal('nilai_min', 8, 2)->nullable();
            $table->decimal('nilai_max', 8, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | HASIL
            |--------------------------------------------------------------------------
            */
            $table->decimal('skor', 8, 2);

            $table->text('keterangan')->nullable();

            // Tahun berlaku Juknis -- skor/aturan bisa direvisi tiap tahun JMA,
            // jangan sampai revisi tahun depan menimpa histori tahun ini.
            $table->unsignedSmallInteger('tahun_berlaku');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | PENTING: nama index diberi MANUAL ('rubrik_lookup_index'), bukan
            | dibiarkan Laravel bikin otomatis -- kalau otomatis, nama yang
            | dihasilkan ('rubrik_penilaians_bidang_prestasi_jenis_rubrik_tahun_
            | berlaku_index') kepanjangan, MySQL cuma izinkan maksimal 64
            | karakter untuk nama index/identifier apapun.
            |--------------------------------------------------------------------------
            */
            $table->index(
                ['bidang_prestasi', 'jenis_rubrik', 'tahun_berlaku'],
                'rubrik_lookup_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrik_penilaians');
    }
};  