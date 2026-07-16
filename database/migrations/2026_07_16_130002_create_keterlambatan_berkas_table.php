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
        Schema::create('keterlambatan_berkas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('madrasah_id')
                ->constrained('madrasahs')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('periode');

            $table->unsignedSmallInteger('jumlah_hari_terlambat');

            $table->text('keterangan')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE: satu madrasah cuma bisa punya SATU catatan keterlambatan
            | per periode -- keterlambatan itu sifatnya kejadian tunggal per
            | siklus pengumpulan, bukan berulang seperti Aduan Masyarakat.
            |--------------------------------------------------------------------------
            */
            $table->unique(['madrasah_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterlambatan_berkas');
    }
};