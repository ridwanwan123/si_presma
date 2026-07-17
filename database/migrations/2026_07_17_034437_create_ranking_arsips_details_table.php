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
        Schema::create('ranking_arsip_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ranking_arsip_id')
                ->constrained('ranking_arsips')
                ->cascadeOnDelete();

            $table->foreignId('madrasah_id')
                ->nullable()
                ->constrained('madrasahs')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT IDENTITAS MADRASAH
            |--------------------------------------------------------------------------
            | Disimpan apa adanya di sini (bukan cuma foreignId), supaya arsip
            | tetap utuh walau data madrasah aslinya berubah (ganti nama,
            | reklasifikasi jenjang, dll) atau bahkan dihapus di kemudian hari.
            | Arsip = potret keadaan SAAT diarsipkan, bukan link ke data live.
            */
            $table->string('nama_madrasah');
            $table->string('npsn')->nullable();
            $table->string('jenjang_madrasah')->nullable();
            $table->string('kota')->nullable();

            $table->unsignedInteger('peringkat');

            /*
            |--------------------------------------------------------------------------
            | NILAI MENTAH PER BIDANG (murni hasil asesor, BELUM ada potongan)
            |--------------------------------------------------------------------------
            */
            $table->decimal('nilai_akademik', 10, 2)->default(0);
            $table->decimal('nilai_non_akademik', 10, 2)->default(0);
            $table->decimal('nilai_keagamaan', 10, 2)->default(0);
            $table->decimal('nilai_gtk', 10, 2)->default(0);
            $table->decimal('nilai_lembaga', 10, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | TOTAL & POTONGAN
            |--------------------------------------------------------------------------
            */
            // Total murni dari asesor (jumlah 5 bidang di atas), SEBELUM potongan
            $table->decimal('total_nilai_asesor', 10, 2)->default(0);

            $table->decimal('potongan_aduan', 10, 2)->default(0);
            $table->decimal('potongan_keterlambatan', 10, 2)->default(0);

            // Nilai akhir SETELAH potongan -- ini dasar peringkat
            $table->decimal('total_nilai_akhir', 10, 2)->default(0);

            $table->unsignedInteger('jumlah_prestasi_dinilai')->default(0);

            $table->timestamps();

            $table->index(['ranking_arsip_id', 'peringkat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_arsip_details');
    }
};