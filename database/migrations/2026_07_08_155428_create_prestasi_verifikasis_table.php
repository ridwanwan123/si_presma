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
        Schema::create('prestasi_verifikasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prestasi_siswa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('verifikator_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('status',[
                'pending',
                'verified',
                'rejected'
            ]);

            $table->decimal('persentase',5,2)->nullable();

            $table->decimal('nilai_akhir',8,2)->nullable();

            $table->text('catatan')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_verifikasis');
    }
};
