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
        Schema::create('ranking_arsips', function (Blueprint $table) {
            $table->id();

            // Satu periode cuma boleh punya SATU arsip -- kalau admin
            // "arsipkan ulang", record yang sama di-UPDATE (lihat
            // RankingArsipController::store()), bukan menambah duplikat.
            $table->unsignedSmallInteger('periode')->unique();

            $table->foreignId('diarsipkan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('diarsipkan_pada');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_arsips');
    }
};