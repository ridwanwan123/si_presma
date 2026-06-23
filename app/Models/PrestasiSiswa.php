<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'madrasah_id',
        'bidang_prestasi',
        'submitter',
        'nama_kegiatan',
        'tingkat',
        'kategori_kegiatan',
        'juara',
        'lembaga_penyelenggara',
        'kategori_penyelenggara',
        'waktu_kegiatan',
        'skor_luring',
        'skor_daring',
        'link_drive_bukti',
        'presentase',
        'nilai_akhir',
        'keterangan',
        'periode',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'waktu_kegiatan' => 'date',
        'periode' => 'datetime',
    ];

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }

     /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeVisible($query)
    {
        $user = auth()->user();

        if ($user->isAdministrator()) {
            return $query;
        }

        if ($user->isOperator()) {
            return $query->where(
                'madrasah_id',
                $user->madrasah_id
            );
        }

        if ($user->isPengawas()) {
            return $query->whereHas('madrasah', function ($q) use ($user) {

                $q->where(
                    'wilayah_pengawas_id',
                    $user->wilayah_pengawas_id
                );

            });
        }

        return $query;
    }
}
