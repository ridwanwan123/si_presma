<?php

namespace Database\Seeders;

use App\Models\Madrasah;
use App\Models\Role;
use App\Models\User;
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
            'nama' => 'Kanwil Kemenag',
            'keterangan' => 'Super Administrator',
        ]);

        $madrasahRole = Role::create([
            'nama' => 'Madrasah',
            'keterangan' => 'Admin Madrasah',
        ]);

        $asesorRole = Role::create([
            'nama' => 'Asesor',
            'keterangan' => 'Asesor Penilai',
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
            'kota' => 'Jakarta',
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
            'nama' => 'Super Admin',
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
            'nama' => 'MAN 01 Jakarta',
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
            'role_id' => $asesorRole->id,
            'madrasah_id' => null,
            'nama' => 'Asesor',
            'email' => 'asesor@mail.com',
            'username' => 'asesor',
            'password' => Hash::make('penmad123'),
            'no_hp' => '083333333333',
            'is_active' => true,
        ]);
    }
}