<?php
use Illuminate\Support\Facades\DB;

$horarios = DB::table('horario')->orderByRaw("FIELD(dia, 'Lunes','Martes','Miércoles','Jueves','Viernes')")->orderBy('hora_inicio')->get();
foreach ($horarios as $h) {
    echo "{$h->id_horario} | {$h->dia} | {$h->hora_inicio} - {$h->hora_fin}\n";
}
echo "\nTotal: " . count($horarios) . "\n";
