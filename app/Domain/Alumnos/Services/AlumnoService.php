<?php

namespace App\Domain\Alumnos\Services;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// CU03 y CU01: Servicio que sincroniza estudiante con usuario de acceso.
class AlumnoService
{
    // CU03 y CU01: Crea un estudiante junto con su usuario.
    public function crearConUsuario(array $data): void
    {
        // CU03 y CU01: Asegura que usuario y estudiante se creen juntos.
        DB::transaction(function () use ($data) {
            // CU01: Crea credenciales del estudiante con rol alumno.
            $usuario = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_rol' => 3,
            ]);

            // CU03: Crea el estudiante vinculado al usuario.
            Alumno::create([
                'id_user' => $usuario->id_user,
                'ci' => $data['ci'],
                'nombres' => $data['nombres'],
                'ap_paterno' => $data['ap_paterno'],
                'ap_materno' => $data['ap_materno'],
                'genero' => $data['genero'],
                'fecha_nac' => $data['fecha_nac'],
                'telefono' => $data['telefono'],
                'id_beca' => $data['id_beca'] ?? null,
            ]);
        });
    }

    // CU03 y CU01: Actualiza datos del estudiante y su usuario.
    public function actualizarConUsuario(Alumno $alumno, array $data): void
    {
        // CU03 y CU01: Asegura consistencia entre estudiante y usuario.
        DB::transaction(function () use ($alumno, $data) {
            // CU01: Recupera el usuario asociado al estudiante.
            $usuario = $alumno->usuario;

            // CU01: Si el estudiante no tiene usuario, lo crea.
            if (!$usuario) {
                $usuario = User::create([
                    'username' => $data['username'],
                    'password' => Hash::make($data['password'] ?: 'alumno123'),
                    'id_rol' => 3,
                ]);

                // CU03 y CU01: Vincula el nuevo usuario al estudiante.
                $alumno->id_user = $usuario->id_user;
            } else {
                // CU01: Actualiza username del usuario existente.
                $usuario->username = $data['username'];

                // CU01: Cambia password solo si se envio una nueva.
                if (!empty($data['password'])) {
                    $usuario->password = Hash::make($data['password']);
                }

                // CU01: Mantiene rol alumno para este usuario.
                $usuario->id_rol = 3;
                $usuario->save();
            }

            // CU03: Actualiza datos personales del estudiante.
            $alumno->ci = $data['ci'];
            $alumno->nombres = $data['nombres'];
            $alumno->ap_paterno = $data['ap_paterno'];
            $alumno->ap_materno = $data['ap_materno'];
            $alumno->genero = $data['genero'];
            $alumno->fecha_nac = $data['fecha_nac'];
            $alumno->telefono = $data['telefono'];
            $alumno->id_beca = $data['id_beca'] ?? null;
            // CU03: Guarda cambios del estudiante.
            $alumno->save();
        });
    }

    // CU03 y CU01: Elimina estudiante y usuario relacionado.
    public function eliminar(Alumno $alumno): void
    {
        // CU03 y CU01: Asegura que ambos registros se eliminen juntos.
        DB::transaction(function () use ($alumno) {
            // CU01: Guarda referencia al usuario antes de eliminar alumno.
            $usuario = $alumno->usuario;
            // CU03: Elimina el registro del estudiante.
            $alumno->delete();

            // CU01: Elimina usuario vinculado si existe.
            if ($usuario) {
                $usuario->delete();
            }
        });
    }
}
