<?php

namespace App\Enums;

// CU01: Enum central de roles del sistema definidos en el documento.
enum Rol: int
{
    // CU01: Administrador con acceso total.
    case ADMIN = 1;
    // CU02: Docente.
    case PROFESOR = 2;
    // CU03: Estudiante.
    case ALUMNO = 3;
    // CU04: Tutor o apoderado.
    case APODERADO = 4;
    // CU01: Director.
    case DIRECTOR = 5;
    // CU24: Secretaria o personal administrativo.
    case SECRETARIA = 6;

    // CU01: Nombre legible para mostrar el rol en vistas y bitacora.
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PROFESOR => 'Profesor',
            self::ALUMNO => 'Alumno',
            self::APODERADO => 'Apoderado',
            self::DIRECTOR => 'Director',
            self::SECRETARIA => 'Secretaria',
        };
    }

    // CU01: Verifica rol administrador.
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    // CU02: Verifica rol profesor.
    public function isProfesor(): bool
    {
        return $this === self::PROFESOR;
    }

    // CU03: Verifica rol alumno.
    public function isAlumno(): bool
    {
        return $this === self::ALUMNO;
    }

    // CU04: Verifica rol apoderado.
    public function isApoderado(): bool
    {
        return $this === self::APODERADO;
    }
}
