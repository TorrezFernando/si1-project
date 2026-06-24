<?php

namespace App\Domain\Notas\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// CU15 y CU04: Servicio de consultas de notas para alumnos y apoderados.
class NotaService
{
    // CU15: Construye consulta base con alumno, materia, gestion, curso y trimestre.
    public function consultaBaseNotas(): \Illuminate\Database\Query\Builder
    {
        // CU15: Une las tablas necesarias para mostrar historial academico completo.
        return DB::table('nota as n')
            ->join('alumno as a', 'a.id_alumno', '=', 'n.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'n.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'n.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'n.id_curso')
            ->join('trimestre as t', 't.id_trimestre', '=', 'n.id_trimestre')
            ->select(
                'n.id_alumno',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'm.nombre as materia',
                'g.nombre as gestion',
                'c.nombre as curso',
                DB::raw("CONCAT('Trimestre ', t.id_trimestre) as trimestre"),
                'n.ser',
                'n.saber',
                'n.hacer',
                'n.autoevaluacion',
                'n.promediofinal',
                'n.descripcion'
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->orderByDesc('n.id_gestion')
            ->orderBy('n.id_trimestre')
            ->orderBy('m.nombre');
    }

    // CU15 y CU03: Devuelve notas de un alumno especifico.
    public function notasPorAlumno(int $idAlumno): Collection
    {
        return $this->consultaBaseNotas()
            ->where('n.id_alumno', $idAlumno)
            ->get();
    }

    // CU15 y CU04: Devuelve notas solo de hijos vinculados a un apoderado.
    public function notasFiltradasPorApoderado(int $idApoderado, ?int $hijoSeleccionado = null): Collection
    {
        // CU04: Usa parentesco para asegurar que el apoderado solo vea sus alumnos.
        return $this->consultaBaseNotas()
            ->whereExists(function ($query) use ($idApoderado) {
                $query->select(DB::raw(1))
                    ->from('parentesco as p')
                    ->whereColumn('p.id_alumno', 'n.id_alumno')
                    ->where('p.id_apoderado', $idApoderado);
            })
            ->when($hijoSeleccionado, function ($query, $idAlumno) {
                $query->where('n.id_alumno', $idAlumno);
            })
            ->get();
    }
}
