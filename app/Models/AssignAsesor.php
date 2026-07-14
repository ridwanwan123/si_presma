<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignAsesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'asesor_id',
        'madrasah_id',
        'periode',
        'assigned_by',
        'assigned_at',
        'status',
        'catatan'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function penilaianPrestasis()
    {
        return $this->hasMany(PenilaianPrestasi::class);
    }
}