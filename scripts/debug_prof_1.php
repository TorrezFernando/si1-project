<?php
use Illuminate\Support\Facades\DB;

$idProfesor = 1;
$ultimaGestion = DB::table('materia_curso_gestion')
    ->where('id_profesor', $idProfesor)
    ->max('id_gestion');

echo "id_profesor: $idProfesor\n";
echo "ultimaGestion: $ultimaGestion\n";

$horarios = DB::table('materia_curso_gestion as mcg')
    ->join('materia_curso_gestion_paralelo as mcgp', function ($join) {
        $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
             ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
             ->on('mcg.id_curso', '=', 'mcgp.id_curso');
    })
    ->join('horario as h', 'mcgp.id_horario', '=', 'h.id_horario')
    ->where('mcg.id_profesor', $idProfesor)
    ->where('mcg.id_gestion', $ultimaGestion)
    ->get();

echo "Clases encontradas: " . $horarios->count() . "\n";
foreach($horarios as $h) {
    echo "{$h->dia} | {$h->hora_inicio} - {$h->hora_fin} | {$h->id_materia}\n";
}
