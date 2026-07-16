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
        Schema::create('prestasi_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madrasah_id')
                ->constrained('madrasahs')
                ->cascadeOnDelete();
            $table->enum('bidang_prestasi', [
                    'Akademik',
                    'Non Akademik',
                    'Keagamaan',
                    'GTK',
                    'Lembaga'
            ]);
            $table->string('submitter');
            $table->string('nama_kegiatan');
            $table->enum('tingkat', [
                    'Kabupaten/Kota',
                    'Provinsi',
                    'Nasional',
                    'Internasional'
                ]);
            $table->enum('kategori_kegiatan', [
                        'Individu',
                        'Beregu'
                    ]);
            $table->string('juara');
            $table->string('lembaga_penyelenggara')->nullable();
            $table->string('kategori_penyelenggara')->nullable();
            $table->date('waktu_kegiatan');
            
            $table->enum('metode_pelaksanaan', [
                'Luring',
                'Daring',
            ]);

            $table->decimal('skor', 8, 2);

            $table->string('link_drive_bukti')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedSmallInteger('periode');
            $table->timestamps();
            $table->softDeletes();

             /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */
            $table->index([
                'madrasah_id',
                'bidang_prestasi',
                'waktu_kegiatan'
            ]);

            $table->index('nama_kegiatan');

            /*
            |--------------------------------------------------------------------------
            | INDEX KOMPOSIT UNTUK QUERY BER-PERIODE
            |--------------------------------------------------------------------------
            | Hampir seluruh query prestasi sekarang selalu filter periode
            | (PeriodeAktif::aktif()), tapi kolom ini belum pernah ter-index.
            | Untuk Madrasah (data dibatasi ke madrasah_id sendiri) dampaknya
            | kecil, tapi untuk Administrator yang melihat SELURUH madrasah,
            | full scan tanpa index ini akan terasa begitu total baris
            | mencapai puluhan ribu.
            */
            $table->index(
                ['periode', 'bidang_prestasi', 'madrasah_id'],
                'idx_prestasi_periode_bidang_madrasah'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswas');
    }
};