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
            $table->string('nama_madrasah');

            $table->string('npsn')->unique();

            $table->string('kota');
            $table->string('provinsi');

            $table->string('akreditasi')->nullable();

            $table->text('alamat_sekolah');

            $table->string('nama_kepala_madrasah');

            $table->string('nip_kepala_madrasah')->nullable();

            $table->string('nama_kepala_urusan_tata_usaha')->nullable();

            $table->string('nip_kepala_urusan_tata_usaha')->nullable();

            $table->timestamps();
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
