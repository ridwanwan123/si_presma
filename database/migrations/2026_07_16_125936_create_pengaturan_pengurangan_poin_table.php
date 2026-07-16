<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_pengurangan_poin', function (Blueprint $table) {
            $table->id();

            // Kode unik dipakai kode program untuk ambil nilai spesifik,
            // BUKAN nilainya -- nilainya (kolom `nilai`) yang admin-editable
            // lewat form, sesuai permintaan supaya tidak hardcode di kode.
            $table->string('kode')->unique();

            $table->enum('kategori', ['aduan_masyarakat', 'keterlambatan']);

            $table->string('label');

            $table->decimal('nilai', 8, 2);

            $table->enum('tipe', ['persen', 'poin']);

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | SEED NILAI AWAL — sesuai dokumen resmi. Admin bisa ubah kapan saja
        | lewat halaman Pengaturan, ini cuma nilai default pertama kali.
        |--------------------------------------------------------------------------
        */
        DB::table('pengaturan_pengurangan_poin')->insert([
            [
                'kode' => 'aduan_1_2_kali',
                'kategori' => 'aduan_masyarakat',
                'label' => '1-2 kali tindak lanjut dalam satu permasalahan',
                'nilai' => 5,
                'tipe' => 'persen',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'aduan_3_kali',
                'kategori' => 'aduan_masyarakat',
                'label' => '3 kali tindak lanjut dalam satu permasalahan',
                'nilai' => 15,
                'tipe' => 'persen',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'aduan_lebih_3_kali',
                'kategori' => 'aduan_masyarakat',
                'label' => 'Lebih dari 3 kali sampai dilakukan investigasi',
                'nilai' => 20,
                'tipe' => 'persen',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'telat_1_hari',
                'kategori' => 'keterlambatan',
                'label' => 'Terlambat 1 hari',
                'nilai' => 25,
                'tipe' => 'poin',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'telat_2_hari',
                'kategori' => 'keterlambatan',
                'label' => 'Terlambat 2 hari',
                'nilai' => 50,
                'tipe' => 'poin',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'telat_3_hari_lebih',
                'kategori' => 'keterlambatan',
                'label' => 'Terlambat 3 hari atau lebih',
                'nilai' => 75,
                'tipe' => 'poin',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pengurangan_poin');
    }
};