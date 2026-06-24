<?php

namespace App\Domain\Horarios\Services;

use App\Models\Profesor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// CU14: Servicio de consultas para horarios de docentes.
class HorarioService
{
    // CU14 y CU02: Obtiene el horario de un profesor en su ultima gestion asignada.
    public function obtenerHorarioProfesor(int $idProfesor): Collection
    {
        $ultimaGestion = DB::table('materia_curso_gestion')
            ->where('id_profesor', $idProfesor)
            ->max('id_gestion');

        // CU14: Si no hay asignaciones, devuelve una coleccion vacia.
        if (!$ultimaGestion) {
            return collect();
        }

        // CU14: Une materia, curso, paralelo, aula, horario y gestion para armar la grilla.
        return DB::table('materia_curso_gestion as mcg')
            ->join('materia_curso_gestion_paralelo as mcgp', function ($join) {
                $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
                     ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
                     ->on('mcg.id_curso', '=', 'mcgp.id_curso');
            })
            ->join('horario as h', 'mcgp.id_horario', '=', 'h.id_horario')
            ->join('materia as m', 'mcg.id_materia', '=', 'm.id_materia')
            ->join('curso_gestion as cg', function ($join) {
                $join->on('mcg.id_gestion', '=', 'cg.id_gestion')
                     ->on('mcg.id_curso', '=', 'cg.id_curso');
            })
            ->join('curso as c', 'cg.id_curso', '=', 'c.id_curso')
            ->join('paralelo as p', 'mcgp.id_paralelo', '=', 'p.id_paralelo')
            ->join('aula as a', 'mcgp.id_aula', '=', 'a.id_aula')
            ->join('gestion as g', 'mcg.id_gestion', '=', 'g.id_gestion')
            ->where('mcg.id_profesor', $idProfesor)
            ->where('mcg.id_gestion', $ultimaGestion)
            ->select(
                'h.id_horario',
                'h.dia',
                'h.hora_inicio',
                'h.hora_fin',
                'm.nombre as materia',
                'c.nombre as curso',
                DB::raw('COALESCE(a.nombre, a.tipo) as aula'),
                'g.nombre as gestion',
                'p.descripcion as paralelo'
            )
            ->orderByRaw("FIELD(h.dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')")
            ->orderBy('h.hora_inicio')
            ->get();
    }

    // CU14: Agrupa registros de horario en dias laborales.
    public function agruparPorDia(Collection $horarios): array
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $horariosPorDia = [];

        foreach ($dias as $dia) {
            $horariosPorDia[$dia] = $horarios->where('dia', $dia)->values();
        }

        return $horariosPorDia;
    }

    // CU02: Devuelve docentes para selector administrativo.
    public function obtenerTodosLosProfesores(): Collection
    {
        return DB::table('profesor')->orderBy('nombre')->get();
    }

    // CU02 y CU01: Busca docente por usuario vinculado.
    public function obtenerProfesorPorUserId(int $idUser): ?object
    {
        return DB::table('profesor')->where('id_user', $idUser)->first();
    }

    // CU02: Busca docente por su id primario.
    public function obtenerProfesorPorId(int $idProfesor): ?object
    {
        return DB::table('profesor')->where('id_profesor', $idProfesor)->first();
    }

    // CU02 y CU01: Obtiene id de docente desde username tecnico profesor_ID.
    public function extraerIdProfesorDesdeUsername(string $username): ?int
    {
        if (preg_match('/profesor_(\d+)/', $username, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
