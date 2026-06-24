<?php

namespace App\Models;

use App\Enums\Rol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// CU01: Modelo principal de usuarios y credenciales del sistema.
class User extends Authenticatable
{
    // CU01: Habilita factory y notificaciones para el usuario.
    use HasFactory, Notifiable;

    // CU01: Tabla real donde se guardan los usuarios.
    protected $table = 'usuario';
    // CU01: Llave primaria de la tabla usuario.
    protected $primaryKey = 'id_user';
    // CU01: La tabla usuario no usa created_at/updated_at.
    public $timestamps = false;

    // CU01: Campos permitidos para crear o actualizar usuarios.
    protected $fillable = [
        'username',
        'password',
        'id_rol',
    ];

    // CU01: Campos ocultos cuando el usuario se serializa.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // CU01: Convierte password usando hashing automatico de Laravel.
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // CU01: Devuelve el enum del rol del usuario.
    public function rol(): Rol
    {
        return Rol::tryFrom((int) $this->id_rol) ?? Rol::ADMIN;
    }

    // CU01: Devuelve el nombre legible del rol.
    public function getRolNombreAttribute(): string
    {
        return $this->rol()->label();
    }

    // CU01: Verifica si el usuario es administrador.
    public function isAdmin(): bool
    {
        return $this->rol()->isAdmin();
    }

    // CU01 y CU02: Verifica si el usuario es profesor.
    public function isProfesor(): bool
    {
        return $this->rol()->isProfesor();
    }

    // CU01 y CU03: Verifica si el usuario es alumno.
    public function isAlumno(): bool
    {
        return $this->rol()->isAlumno();
    }

    // CU01 y CU04: Verifica si el usuario es apoderado/tutor.
    public function isApoderado(): bool
    {
        return $this->rol()->isApoderado();
    }
}
