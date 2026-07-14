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
        Schema::create('prestasi_siklus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('madrasah_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->year('periode');

            $table->enum('status', [
                'OPEN',
                'SUBMITTED',
                'LOCKED',
                'ASSESSMENT',
                'FINISHED',
            ])->default('OPEN');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('assessment_started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('locked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

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
        Schema::dropIfExists('prestasi_siklus');
    }
};
