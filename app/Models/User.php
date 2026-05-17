<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'madrasah_id',
        'nama',
        'email',
        'username',
        'password',
        'no_hp',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class);
    }


    /*
    |--------------------------------------------------------------------------
    | CEK ROLE
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin()
    {
        return $this->role->nama == 'superadmin';
    }

    public function isMadrasah()
    {
        return $this->role->nama == 'madrasah';
    }

    public function isAsesor()
    {
        return $this->role->nama == 'asesor';
    }
}
