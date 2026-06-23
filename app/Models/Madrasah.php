<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madrasah extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenjang_madrasah',
        'nama_madrasah',
        'npsn',
        'kota',
        'provinsi',
        'akreditasi',
        'alamat_sekolah',
        'nama_kepala_madrasah',
        'nip_kepala_madrasah',
        'nama_kepala_urusan_tata_usaha',
        'nip_kepala_urusan_tata_usaha',
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
}
