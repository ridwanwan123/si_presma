<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madrasah extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenjang_madrasah',
        'status_madrasah',
        'logo',
        'nama_madrasah',
        'npsn',
        'akreditasi',

        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'alamat_sekolah',
        'latitude',
        'longitude',

        'nama_kepala_madrasah',
        'nip_kepala_madrasah',
        'no_telepon_kamad',
        'foto_kamad',

        'nama_kepala_urusan_tata_usaha',
        'nip_kepala_urusan_tata_usaha',
        'no_telepon_katu',
        'foto_katu',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function prestasis()
    {
        return $this->hasMany(PrestasiSiswa::class);
    }

    public function assignAsesor()
    {
        // Satu madrasah bisa punya banyak baris assign_asesors seiring waktu
        // (satu per periode). hasOne() ini dibatasi ke periode aktif supaya
        // "assignment saat ini" tidak pernah kebaca dari periode lain.
        return $this->hasOne(AssignAsesor::class)->where('periode', PeriodeAktif::aktif());
    }

    public function prestasiSiklus()
    {
        return $this->hasMany(PrestasiSiklus::class);
    }

    public function prestasiSiklusAktif()
    {
        if (!auth()->check() || !auth()->user()->hasRole('Madrasah')) {
            return null;
        }

        return $this->prestasiSiklus()->firstOrCreate(
            [
                'periode' => PeriodeAktif::aktif(),
            ],
            [
                'status' => PrestasiSiklus::OPEN,
            ]
        );
    }
}