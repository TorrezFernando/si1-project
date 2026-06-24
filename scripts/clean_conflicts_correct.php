<?php
use Illuminate\Support\Facades\DB;

// Paso 1: Limpiar la tabla destino (ya lo hice con el restore, pero por si acaso)
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Obtener todas las asignaciones vinculadas a un profesor y horario, PRIORIZANDO la gestión más nueva
$assignments = DB::table('materia_curso_gestion_paralelo as mcgp')
    ->join('materia_curso_gestion as mcg', function($join) {
        $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
             ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
             ->on('mcg.id_curso', '=', 'mcgp.id_curso');
    })
    ->select('mcgp.*', 'mcg.id_profesor', 'mcg.id_gestion')
    ->orderBy('mcg.id_gestion', 'desc') // Importante: Primero lo más nuevo
    ->get();

$kept = []; 
$toDeleteKeys = [];

foreach ($assignments as $a) {
    if ($a->id_gestion != 2) {
        // Si no es la gestión 2, marcamos para borrar inmediatamente (queremos solo la gestión actual que es la 2)
         $toDeleteKeys[] = [
            'id_materia'  => $a->id_materia,
            'id_gestion'  => $a->id_gestion,
            'id_curso'    => $a->id_curso,
            'id_paralelo' => $a->id_paralelo
        ];
        continue;
    }

    $profKey = $a->id_profesor;
    $hourKey = $a->id_horario;
    
    if (isset($kept[$profKey][$hourKey])) {
        // Ya tenemos una clase para este profesor en este horario (mismo año), esta sobra
        $toDeleteKeys[] = [
            'id_materia'  => $a->id_materia,
            'id_gestion'  => $a->id_gestion,
            'id_curso'    => $a->id_curso,
            'id_paralelo' => $a->id_paralelo
        ];
    } else {
        // Reservamos este slot
        $kept[$profKey][$hourKey] = true;
    }
}

echo "Total registros: " . count($assignments) . "\n";
echo "Registros a borrar: " . count($toDeleteKeys) . "\n";

foreach ($toDeleteKeys as $item) {
    DB::table('materia_curso_gestion_paralelo')
        ->where('id_materia', $item['id_materia'])
        ->where('id_gestion', $item['id_gestion'])
        ->where('id_curso', $item['id_curso'])
        ->where('id_paralelo', $item['id_paralelo'])
        ->delete();
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Limpieza completada PRIORIZADA.\n";
