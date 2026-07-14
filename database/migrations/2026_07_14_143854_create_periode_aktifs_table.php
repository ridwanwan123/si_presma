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
        Schema::create('periode_aktifs', function (Blueprint $table) {
            $table->id();

            // Satu baris per tahun periode. unique() supaya "aktifkan periode
            // yang sama dua kali" otomatis jadi UPDATE (lewat updateOrCreate),
            // bukan bikin baris duplikat.
            $table->year('periode')->unique();

            // Cuma SATU baris yang boleh true di satu waktu -- itu yang
            // menentukan "periode berjalan" di seluruh sistem, menggantikan
            // now()->year yang sebelumnya hardcode di banyak tempat.
            $table->boolean('is_active')->default(false);

            $table->foreignId('diaktifkan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('diaktifkan_pada')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_aktifs');
    }
};