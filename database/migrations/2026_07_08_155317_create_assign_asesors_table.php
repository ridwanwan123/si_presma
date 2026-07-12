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

            $table->unique([
                'asesor_id',
                'madrasah_id'
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
