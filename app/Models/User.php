<?php

namespace App\Models;

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
        'wilayah_pengawas_id',
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
    | Relationships
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

    public function wilayahPengawas()
    {
        return $this->belongsTo(WilayahPengawas::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdministrator(): bool
    {
        return $this->role?->nama === 'Administrator';
    }

    public function isOperator(): bool
    {
        return $this->role?->nama === 'Madrasah';
    }

    public function isPengawas(): bool
    {
        return $this->role?->nama === 'Pengawas';
    }

    public function hasRole(array|string $roles): bool
    {
        $roles = (array) $roles;

        return in_array($this->role?->nama, $roles);
    }
}