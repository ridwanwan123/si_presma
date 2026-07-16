<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AduanMasyarakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'madrasah_id',
        'periode',
        'tingkat_aduan',
        'permasalahan',
        'jumlah_tindak_lanjut',
        'tanggal_aduan',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_aduan' => 'date',
    ];

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}