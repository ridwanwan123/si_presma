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
        Schema::create('assign_asesors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asesor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('madrasah_id')
                ->constrained('madrasahs')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PERIODE
            |--------------------------------------------------------------------------
            | Satu madrasah bisa di-assign ulang setiap periode (tahun) berjalan.
            | Tipe year() disamakan dengan prestasi_siklus.periode, karena satu
            | baris assign_asesors konsepnya menempel ke satu siklus penilaian
            | (madrasah_id + periode) yang sama.
            */
            $table->year('periode');

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'assigned',
                'not_assigned',
                'in_progress',
                'completed'
            ])->default('assigned');

            $table->text('catatan')->nullable();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            | Sebelumnya: unique(['asesor_id', 'madrasah_id']) -- ini tidak selaras
            | dengan relasi hasOne() di model Madrasah, karena masih mengizinkan satu
            | madrasah punya banyak baris assignment (selama asesor_id-nya beda).
            | Diganti jadi unique(['madrasah_id', 'periode']): satu madrasah cuma
            | boleh punya SATU baris assignment per periode, sesuai asumsi hasOne()
            | untuk periode berjalan, sekaligus otomatis membuka riwayat baru begitu
            | periode berganti.
            */
            $table->unique([
                'madrasah_id',
                'periode'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_asesors');
    }
};