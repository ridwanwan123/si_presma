<?php

namespace Database\Seeders;

use App\Models\PeriodeAktif;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeriodeAktifSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();

        PeriodeAktif::create([
            'periode' => 2026,
            'is_active' => true,
            'diaktifkan_oleh' => $superadmin?->id,
            'diaktifkan_pada' => now(),
            'keterangan' => 'Prestasi 2026 / Jakarta Madrasah Awards 2027',
        ]);
    }
}