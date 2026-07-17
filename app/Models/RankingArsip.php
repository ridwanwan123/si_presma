<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingArsip extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode',
        'diarsipkan_oleh',
        'diarsipkan_pada',
        'catatan',
    ];

    protected $casts = [
        'diarsipkan_pada' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(RankingArsipDetail::class);
    }

    public function diarsipkanOleh()
    {
        return $this->belongsTo(User::class, 'diarsipkan_oleh');
    }
}