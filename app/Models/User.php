<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'admin' // Asumo que sigues usando esto para identificar administradores
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Verifica si el usuario es admin (true/false)
    public function isAdmin()
    {
        return (bool) $this->admin;
    }

    // Imagen de perfil en AdminLTE (puedes personalizarla)
    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    // Descripción en AdminLTE (devuelve el nombre o "Usuario" por defecto)
    public function adminlte_desc()
    {
        return $this->name ?? 'Usuario';
    }

    // URL del perfil en AdminLTE
    public function adminlte_profile_url()
    {
        return 'profile/username';
    }
}
