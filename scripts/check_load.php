<?php
use Illuminate\Support\Facades\DB;

$load = DB::table('materia_curso_gestion_paralelo as mcgp')
    ->join('materia_curso_gestion as mcg', function($join) {
        $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
             ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
             ->on('mcg.id_curso', '=', 'mcgp.id_curso');
    })
    ->select('mcg.id_profesor', DB::raw('COUNT(*) as total'))
    ->groupBy('mcg.id_profesor')
    ->get();

foreach($load as $l) {
    echo "Profesor {$l->id_profesor}: {$l->total} clases\n";
}

$total_classes = DB::table('materia_curso_gestion_paralelo')->count();
echo "\nTotal de registros de clases: " . $total_classes . "\n";
echo "Slots disponibles en la semana: 30 (5 dias x 6 slots)\n";
