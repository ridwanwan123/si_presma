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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('madrasah_id')
                ->nullable()
                ->constrained('madrasahs')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('wilayah_pengawas_id')
                ->nullable()
                ->constrained('wilayah_pengawas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('nama');

            $table->string('email')->unique();

            $table->string('username')->unique();

            $table->string('password');

            $table->string('no_hp')->nullable();

            $table->boolean('is_active')->default(true);

            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
