<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahPengawas extends Model
{
    protected $table = 'wilayah_pengawas';

    protected $fillable = [
        'kota',
        'unit_kerja',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}