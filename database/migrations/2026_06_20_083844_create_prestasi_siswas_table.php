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
            $table->decimal('skor_luring', 8, 2)->default(0)->nullable();
            $table->decimal('skor_daring', 8, 2)->default(0)->nullable();
            $table->string('link_drive_bukti')->nullable();
            $table->decimal('presentase', 5, 2)->default(0)->nullable();
            $table->decimal('nilai_akhir', 8, 2)->default(0)->nullable();
            $table->text('keterangan')->nullable();
            $table->date('periode');
            $table->enum('status_verifikasi', [
                'pending',
                'verified',
                'rejected'
            ])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
