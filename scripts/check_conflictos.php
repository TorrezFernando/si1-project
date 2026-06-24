<?php
use Illuminate\Support\Facades\DB;

// Buscar conflictos: un profesor con más de un curso en el mismo horario
$conflictos = DB::table('materia_curso_gestion as mcg')
    ->join('materia_curso_gestion_paralelo as mcgp', function($join) {
        $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
             ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
             ->on('mcg.id_curso', '=', 'mcgp.id_curso');
    })
    ->join('horario as h', 'mcgp.id_horario', '=', 'h.id_horario')
    ->select(
        'mcg.id_profesor',
        'mcgp.id_horario',
        'h.dia',
        'h.hora_inicio',
        'h.hora_fin',
        DB::raw('COUNT(DISTINCT mcg.id_curso) as total_cursos'),
        DB::raw('GROUP_CONCAT(DISTINCT mcg.id_curso) as cursos')
    )
    ->groupBy('mcg.id_profesor', 'mcgp.id_horario', 'h.dia', 'h.hora_inicio', 'h.hora_fin')
    ->havingRaw('COUNT(DISTINCT mcg.id_curso) > 1')
    ->get();

if ($conflictos->isEmpty()) {
    echo "✅ No hay conflictos. Cada profesor tiene un solo curso por horario.\n";
} else {
    echo "⚠️ Conflictos encontrados: " . count($conflictos) . "\n\n";
    foreach ($conflictos as $c) {
        echo "Profesor {$c->id_profesor} | {$c->dia} {$c->hora_inicio}-{$c->hora_fin} | Cursos: {$c->cursos}\n";
    }
}
