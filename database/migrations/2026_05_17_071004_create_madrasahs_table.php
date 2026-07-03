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
        Schema::create('madrasahs', function (Blueprint $table) {
            $table->id();

            $table->string('jenjang_madrasah');
            $table->string('logo')->nullable();
            $table->string('status_madrasah')->nullable();
            $table->string('nama_madrasah');
            $table->string('npsn')->unique();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->text('alamat_sekolah')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('akreditasi')->nullable();
            $table->string('nama_kepala_madrasah');
            $table->string('nip_kepala_madrasah')->nullable();
            $table->string('no_telepon_kamad')->nullable();
            $table->string('foto_kamad')->nullable();
            $table->string('nama_kepala_urusan_tata_usaha')->nullable();
            $table->string('nip_kepala_urusan_tata_usaha')->nullable();
            $table->string('no_telepon_katu')->nullable();
            $table->string('foto_katu')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('madrasahs');
    }
};
