<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\WilayahPengawas;
use App\Models\Madrasah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE
        |--------------------------------------------------------------------------
        */

        $superadminRole = Role::create([
            'nama' => 'Administrator',
            'keterangan' => 'Super Administrator',
        ]);

        $madrasahRole = Role::create([
            'nama' => 'Madrasah',
            'keterangan' => 'Admin Madrasah',
        ]);

        $pengawasRole = Role::create([
            'nama' => 'Pengawas',
            'keterangan' => 'Pengawas/Asseor Penilai Madrasah',
        ]);

        /*
        |--------------------------------------------------------------------------
        | WILAYAH PENGAWAS
        |--------------------------------------------------------------------------
        */

        $wilayahUtara = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA UTARA',
            'unit_kerja' => 'KANKEMENAG JAKARTA UTARA',
        ]);

        $wilayahTimur = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA TIMUR',
            'unit_kerja' => 'KANKEMENAG JAKARTA TIMUR',
        ]);

        $wilayahBarat = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA BARAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA BARAT',
        ]);

        $wilayahPusat = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA PUSAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA PUSAT',
        ]);

        $wilayahKepSeribu = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA KEPULAUAN SERIBU',
            'unit_kerja' => 'KANKEMENAG KEPULAUAN SERIBU',
        ]);

        /*
        |--------------------------------------------------------------------------
        | MADRASAH SEEDER
        |--------------------------------------------------------------------------
        */

        $this->call([
            MadrasahSeeder::class
        ]);

        /*
        |--------------------------------------------------------------------------
        | AMBIL 1 MADRASAH UNTUK USER MADRASAH
        |--------------------------------------------------------------------------
        */

        $madrasah = \App\Models\Madrasah::where('npsn', '20177932')->first();

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */

        User::create([
            'role_id' => $superadminRole->id,
            'madrasah_id' => null,
            'wilayah_pengawas_id' => null,
            'nama' => 'Kanwil Kemenag',
            'email' => 'superadmin@mail.com',
            'username' => 'superadmin',
            'password' => Hash::make('penmad123'),
            'no_hp' => '081111111111',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER MADRASAH
        |--------------------------------------------------------------------------
        */

        User::create([
            'role_id' => $madrasahRole->id,
            'madrasah_id' => $madrasah?->id, // SAFE NULL HANDLING
            'wilayah_pengawas_id' => null,
            'nama' => $madrasah?->nama_madrasah ?? 'Madrasah Demo',
            'email' => 'madrasah@mail.com',
            'username' => 'madrasah',
            'password' => Hash::make('penmad123'),
            'no_hp' => '082222222222',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER PENGAWAS
        |--------------------------------------------------------------------------
        */

        User::create([
            'role_id' => $pengawasRole->id,
            'madrasah_id' => null,
            'wilayah_pengawas_id' => $wilayahUtara->id,
            'nama' => 'Pengawas',
            'email' => 'pengawas@mail.com',
            'username' => 'pengawas',
            'password' => Hash::make('penmad123'),
            'no_hp' => '083333333333',
            'is_active' => true,
        ]);
    }
}