<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeterlambatanBerkas extends Model
{
    use HasFactory;

    protected $table = 'keterlambatan_berkas';

    protected $fillable = [
        'madrasah_id',
        'periode',
        'jumlah_hari_terlambat',
        'keterangan',
        'created_by',
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