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
        Schema::create('aduan_masyarakats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('madrasah_id')
                ->constrained('madrasahs')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('periode');

            $table->enum('tingkat_aduan', [
                'Pusat',
                'Badan Pemeriksa Keuangan',
                'Ombudsman',
                'Inspektorat Jenderal',
                'Kantor Wilayah',
                'Kota/Kabupaten',
            ]);

            // Nama/identitas permasalahan -- 1 permasalahan bisa punya
            // beberapa kali tindak lanjut sebelum "selesai".
            $table->string('permasalahan');

            $table->unsignedTinyInteger('jumlah_tindak_lanjut');

            $table->date('tanggal_aduan');

            $table->text('catatan')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Satu madrasah bisa punya banyak aduan berbeda dalam satu
            // periode (makanya TIDAK unique), tapi tetap perlu index biar
            // agregasi per madrasah+periode cepat.
            $table->index(['madrasah_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduan_masyarakats');
    }
};