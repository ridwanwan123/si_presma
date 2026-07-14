<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAktif extends Model
{
    use HasFactory;

    protected $table = 'periode_aktifs';

    protected $fillable = [
        'periode',
        'is_active',
        'diaktifkan_oleh',
        'diaktifkan_pada',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'diaktifkan_pada' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function diaktifkanOleh()
    {
        return $this->belongsTo(User::class, 'diaktifkan_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | PERIODE AKTIF (SATU-SATUNYA SUMBER KEBENARAN)
    |--------------------------------------------------------------------------
    | Dipakai menggantikan now()->year yang sebelumnya hardcode tersebar di
    | banyak controller/model. Kalau belum pernah ada periode yang diaktifkan
    | sama sekali (mis. sebelum data awal diisi), fallback ke tahun berjalan
    | supaya sistem tetap bisa jalan alih-alih error.
    */
    public static function aktif(): int
    {
        return static::where('is_active', true)->value('periode') ?? now()->year;
    }
}