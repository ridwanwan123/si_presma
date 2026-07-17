<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingArsipDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ranking_arsip_id',
        'madrasah_id',
        'nama_madrasah',
        'npsn',
        'jenjang_madrasah',
        'kota',
        'peringkat',
        'nilai_akademik',
        'nilai_non_akademik',
        'nilai_keagamaan',
        'nilai_gtk',
        'nilai_lembaga',
        'total_nilai_asesor',
        'potongan_aduan',
        'potongan_keterlambatan',
        'total_nilai_akhir',
        'jumlah_prestasi_dinilai',
    ];

    public function rankingArsip()
    {
        return $this->belongsTo(RankingArsip::class);
    }

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }
}