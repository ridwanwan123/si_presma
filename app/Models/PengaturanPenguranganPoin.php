<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanPenguranganPoin extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_pengurangan_poin';

    protected $fillable = [
        'kode',
        'kategori',
        'label',
        'nilai',
        'tipe',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | AMBIL NILAI BERDASARKAN KODE
    |--------------------------------------------------------------------------
    | Dipakai di PenguranganPoinService supaya kode program tidak pernah
    | hardcode angka persen/poin -- semua ditarik dari sini, yang isinya
    | admin-editable lewat halaman Pengaturan.
    */
    public static function nilai(string $kode): float
    {
        return (float) (static::where('kode', $kode)->value('nilai') ?? 0);
    }
}