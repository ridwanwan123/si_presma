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
        Schema::create('penilaian_prestasis', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | ASSIGNMENT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('assign_asesor_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PRESTASI
            |--------------------------------------------------------------------------
            */

            $table->foreignId('prestasi_siswa_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | HASIL PENILAIAN
            |--------------------------------------------------------------------------
            */

            $table->decimal('persentase', 5, 2)
                ->default(100);

            $table->decimal('nilai_akhir', 8, 2)
                ->default(0);

            $table->text('catatan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'draft',
                'completed'
            ])->default('draft');

            /*
            |--------------------------------------------------------------------------
            | WAKTU
            |--------------------------------------------------------------------------
            */

            $table->timestamp('dinilai_pada')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'assign_asesor_id',
                'prestasi_siswa_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_prestasis');
    }
};
