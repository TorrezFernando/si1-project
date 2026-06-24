<?php

namespace App\Domain\Profesores\Services;

use App\Models\Profesor;
use App\Models\ProfesorPermiso;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// CU02 y CU01: Servicio para reglas de docente, usuario y permisos.
class ProfesorService
{
    // CU02 y CU01: Elimina docente y limpia datos academicos relacionados.
    public function eliminarProfesor(Profesor $profesor): void
    {
        // CU02 y CU01: Ejecuta la eliminacion completa en una transaccion.
        DB::transaction(function () use ($profesor) {
            // CU02: Guarda el id del docente para limpiar relaciones.
            $pid = $profesor->id_profesor;

            // CU02: Busca materias/curso/gestion asignadas al docente.
            $mcg = DB::table('materia_curso_gestion')
                ->where('id_profesor', $pid)
                ->get(['id_materia', 'id_gestion', 'id_curso']);

            // CU02: Recorre asignaciones para borrar dependencias.
            foreach ($mcg as $row) {
                // CU02: Elimina notas relacionadas con la asignacion del docente.
                DB::table('nota')
                    ->where('id_materia', $row->id_materia)
                    ->where('id_gestion', $row->id_gestion)
                    ->where('id_curso', $row->id_curso)
                    ->delete();

                // CU02: Elimina paralelos relacionados con la asignacion del docente.
                DB::table('materia_curso_gestion_paralelo')
                    ->where('id_materia', $row->id_materia)
                    ->where('id_gestion', $row->id_gestion)
                    ->where('id_curso', $row->id_curso)
                    ->delete();
            }

            // CU02: Elimina asignaciones academicas del docente.
            DB::table('materia_curso_gestion')
                ->where('id_profesor', $pid)
                ->delete();

            // CU02: Elimina especialidades del docente.
            DB::table('profesor_especialidad')
                ->where('id_profesor', $pid)
                ->delete();

            // CU02: Elimina permiso de horario si existe.
            if ($profesor->permiso) {
                $profesor->permiso->delete();
            }

            // CU01: Desvincula usuario antes de eliminar docente.
            $profesor->id_user = null;
            $profesor->save();

            // CU02: Elimina el docente.
            $profesor->delete();
        });
    }

    // CU02: Crea permiso inicial para controlar acceso del docente a horario.
    public function crearPermisoDefecto(Profesor $profesor): void
    {
        ProfesorPermiso::create([
            'id_profesor' => $profesor->id_profesor,
            'puede_ver_horario' => false,
        ]);
    }

    // CU01 y CU02: Genera username por defecto para docente sin usuario.
    public function generarUsernameDefault(Profesor $profesor): string
    {
        return 'profesor_' . str_pad((string) $profesor->id_profesor, 3, '0', STR_PAD_LEFT);
    }

    // CU01 y CU02: Genera password por defecto para docente sin usuario.
    public function generarPasswordDefault(Profesor $profesor): string
    {
        return 'prof' . str_pad((string) $profesor->id_profesor, 3, '0', STR_PAD_LEFT);
    }
}
