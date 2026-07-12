<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPrestasi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_prestasis';

    protected $fillable = [
        'assign_asesor_id',
        'prestasi_siswa_id',
        'persentase',
        'nilai_akhir',
        'catatan',
        'status',
        'dinilai_pada',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'dinilai_pada' => 'datetime',
    ];

    /**
     * Assignment asesor.
     */
    public function assignAsesor()
    {
        return $this->belongsTo(AssignAsesor::class);
    }

    /**
     * Prestasi siswa yang dinilai.
     */
    public function prestasiSiswa()
    {
        return $this->belongsTo(PrestasiSiswa::class);
    }
}