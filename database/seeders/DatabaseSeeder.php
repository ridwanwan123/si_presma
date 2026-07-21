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
            'kota' => 'ADM. KOTA JAKARTA UTARA',
            'unit_kerja' => 'KANKEMENAG JAKARTA UTARA',
        ]);

        $wilayahTimur = WilayahPengawas::create([
            'kota' => 'ADM. KOTA JAKARTA TIMUR',
            'unit_kerja' => 'KANKEMENAG JAKARTA TIMUR',
        ]);

        $wilayahBarat = WilayahPengawas::create([
            'kota' => 'ADM. KOTA JAKARTA BARAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA BARAT',
        ]);

        $wilayahSelatan = WilayahPengawas::create([
            'kota' => 'ADM. KOTA JAKARTA SELATAN',
            'unit_kerja' => 'KANKEMENAG JAKARTA SELATAN',
        ]);
        
        $wilayahPusat = WilayahPengawas::create([
            'kota' => 'ADM. KOTA JAKARTA PUSAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA PUSAT',
        ]);

        $wilayahKepSeribu = WilayahPengawas::create([
            'kota' => 'ADM. KOTA KEPULAUAN SERIBU',
            'unit_kerja' => 'KANKEMENAG KEPULAUAN SERIBU',
        ]);

        /*
        |--------------------------------------------------------------------------
        | MADRASAH SEEDER
        |--------------------------------------------------------------------------
        */

        $this->call([
            MadrasahSeeder::class,
            RubrikPenilaianSeeder::class
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
        | USER MADRASAH (5 AKUN)
        |--------------------------------------------------------------------------
        */

        $madrasahList = Madrasah::limit(5)->get();

        foreach ($madrasahList as $index => $madrasah) {

            User::create([
                'role_id' => $madrasahRole->id,
                'madrasah_id' => $madrasah->id,
                'wilayah_pengawas_id' => null,
                'nama' => $madrasah->nama_madrasah,
                'email' => 'madrasah' . ($index + 1) . '@mail.com',
                'username' => 'madrasah' . ($index + 1),
                'password' => Hash::make('penmad123'),
                'no_hp' => '08222222222' . ($index + 1),
                'is_active' => true,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | USER PENGAWAS (2 AKUN)
        |--------------------------------------------------------------------------
        */

        $wilayahList = [
            $wilayahSelatan,
            $wilayahTimur
        ];

        foreach ($wilayahList as $index => $wilayah) {

            User::create([
                'role_id' => $pengawasRole->id,
                'madrasah_id' => null,
                'wilayah_pengawas_id' => $wilayah->id,
                'nama' => 'Pengawas ' . ($index + 1),
                'email' => 'pengawas' . ($index + 1) . '@mail.com',
                'username' => 'pengawas' . ($index + 1),
                'password' => Hash::make('penmad123'),
                'no_hp' => '08333333333' . ($index + 1),
                'is_active' => true,
            ]);
        }
    }
}