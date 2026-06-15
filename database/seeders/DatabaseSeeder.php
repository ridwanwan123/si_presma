<?php

namespace Database\Seeders;

use App\Models\Madrasah;
use App\Models\Role;
use App\Models\User;
use App\Models\WilayahPengawas;
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
            'nama' => 'Operator Madrasah',
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

        $wilayahPengawas = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA UTARA',
            'unit_kerja' => 'KANKEMENAG JAKARTA UTARA',
        ]);

        $wilayahPengawas = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA TIMUR',
            'unit_kerja' => 'KANKEMENAG JAKARTA TIMUR',
        ]);

        $wilayahPengawas = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA BARAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA BARAT',
        ]);

        $wilayahPengawas = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA JAKARTA PUSAT',
            'unit_kerja' => 'KANKEMENAG JAKARTA PUSAT',
        ]);
        
        $wilayahPengawas = WilayahPengawas::create([
            'kota' => 'ADMINISTRASI KOTA KEPULAUAN SERIBU',
            'unit_kerja' => 'KANKEMENAG KEPULAUAN SERIBU',
        ]);



        
        /*
        |--------------------------------------------------------------------------
        | MADRASAH
        |--------------------------------------------------------------------------
        */

        $madrasah = Madrasah::create([
            'jenjang_madrasah' => 'MA',
            'nama_madrasah' => 'MAN 1 Jakarta',
            'npsn' => '12345678',
            'kota' => 'Jakarta Utara',
            'provinsi' => 'DKI Jakarta',
            'akreditasi' => 'A',
            'alamat_sekolah' => 'Jl. Pendidikan No. 1',
            'nama_kepala_madrasah' => 'Ahmad Fauzi',
            'nip_kepala_madrasah' => '1987654321',
            'nama_kepala_urusan_tata_usaha' => 'Budi Santoso',
            'nip_kepala_urusan_tata_usaha' => '1976543210',
        ]);

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
            'madrasah_id' => $madrasah->id,
            'wilayah_pengawas_id' => null,
            'nama' => 'MAN 1 Jakarta',
            'email' => 'madrasah@mail.com',
            'username' => 'madrasah',
            'password' => Hash::make('penmad123'),
            'no_hp' => '082222222222',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER ASESOR
        |--------------------------------------------------------------------------
        */

        User::create([
            'role_id' => $pengawasRole->id,
            'madrasah_id' => null,
            'wilayah_pengawas_id' => $wilayahPengawas->id,
            'nama' => 'Pengawas',
            'email' => 'pengawas@mail.com',
            'username' => 'pengawas',
            'password' => Hash::make('penmad123'),
            'no_hp' => '083333333333',
            'is_active' => true,
        ]);
    }
}