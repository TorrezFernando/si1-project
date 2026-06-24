<?php

namespace App\Domain\Auth\Services;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// CU06: Servicio que decide el destino del usuario despues del inicio de sesion.
class AuthService
{
    // CU06: Redirige al usuario segun rol y permisos despues de hacer login.
    public function redirectAfterLogin(User $user): string
    {
        // CU06: Si el docente tiene permiso de horario, entra directo a su horario.
        if ($this->esProfesorConHorario($user)) {
            return route('profesor.horario');
        }

        // CU06 y CU04: Si es apoderado/tutor, entra a la consulta de sus hijos.
        if ($this->esApoderado($user)) {
            return route('apoderado.consulta');
        }

        // CU06: El resto de usuarios entra al panel principal.
        return route('home-panel');
    }

    // CU06: Redireccion auxiliar cuando el usuario ya esta autenticado.
    public function redirectAfterHome(User $user): string
    {
        // CU06: Mantiene al profesor autorizado dentro del modulo de horario.
        if ($this->esProfesorConHorario($user)) {
            return route('profesor.horario');
        }

        // CU06: Ruta general del panel.
        return route('home-panel');
    }

    // CU01: Verifica si el usuario tiene rol Administrador.
    public function esAdmin(User $user): bool
    {
        return (int) $user->id_rol === Rol::ADMIN->value;
    }

    // CU02 y CU01: Verifica si el usuario pertenece al rol Profesor.
    public function esProfesor(User $user): bool
    {
        return (int) $user->id_rol === Rol::PROFESOR->value;
    }

    // CU03 y CU01: Verifica si el usuario pertenece al rol Alumno.
    public function esAlumno(User $user): bool
    {
        return (int) $user->id_rol === Rol::ALUMNO->value;
    }

    // CU04 y CU01: Verifica si el usuario pertenece al rol Apoderado/Tutor.
    public function esApoderado(User $user): bool
    {
        return (int) $user->id_rol === Rol::APODERADO->value;
    }

    // CU02 y CU06: Verifica si el profesor puede ingresar al modulo de horario.
    public function esProfesorConHorario(User $user): bool
    {
        return $this->esProfesor($user) && Gate::allows('profesor.horario');
    }

    // CU01: Devuelve la etiqueta legible del rol del usuario.
    public function labelDelRol(User $user): string
    {
        $rol = Rol::tryFrom((int) $user->id_rol);
        return $rol ? $rol->label() : 'Usuario';
    }
}
