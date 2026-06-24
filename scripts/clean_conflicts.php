<?php
use Illuminate\Support\Facades\DB;

// Respaldo preventivo solo de esta tabla por si acaso
DB::statement('CREATE TABLE IF NOT EXISTS materia_curso_gestion_paralelo_backup AS SELECT * FROM materia_curso_gestion_paralelo');

// Obtener todas las asignaciones vinculadas a un profesor y horario
$assignments = DB::table('materia_curso_gestion_paralelo as mcgp')
    ->join('materia_curso_gestion as mcg', function($join) {
        $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
             ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
             ->on('mcg.id_curso', '=', 'mcgp.id_curso');
    })
    ->select('mcgp.*', 'mcg.id_profesor')
    ->get();

$kept = []; // Guardaremos [id_profesor][id_horario] = true
$toDelete = [];

foreach ($assignments as $a) {
    $profKey = $a->id_profesor;
    $hourKey = $a->id_horario;
    
    if (isset($kept[$profKey][$hourKey])) {
        // Ya tenemos una clase para este profesor en este horario, esta sobra
        $toDelete[] = [
            'id_materia'  => $a->id_materia,
            'id_gestion'  => $a->id_gestion,
            'id_curso'    => $a->id_curso,
            'id_paralelo' => $a->id_paralelo
        ];
    } else {
        // Reservamos este slot para este profesor
        $kept[$profKey][$hourKey] = true;
    }
}

echo "Total registros encontrados: " . count($assignments) . "\n";
echo "Registros para eliminar (choques): " . count($toDelete) . "\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($toDelete as $item) {
    DB::table('materia_curso_gestion_paralelo')
        ->where('id_materia', $item['id_materia'])
        ->where('id_gestion', $item['id_gestion'])
        ->where('id_curso', $item['id_curso'])
        ->where('id_paralelo', $item['id_paralelo'])
        ->delete();
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Limpieza completada.\n";
