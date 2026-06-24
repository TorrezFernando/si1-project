<?php
use Illuminate\Support\Facades\DB;

// Nuevos 6 bloques por día (orden correcto)
$slots = [
    ['hora_inicio' => '08:00:00', 'hora_fin' => '08:45:00'],
    ['hora_inicio' => '08:45:00', 'hora_fin' => '09:30:00'],
    ['hora_inicio' => '10:30:00', 'hora_fin' => '11:15:00'],
    ['hora_inicio' => '11:15:00', 'hora_fin' => '12:00:00'],
    ['hora_inicio' => '12:00:00', 'hora_fin' => '12:45:00'],
    ['hora_inicio' => '12:45:00', 'hora_fin' => '13:30:00'],
];

$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

// Construir mapa de IDs viejos → nuevos
// Los viejos IDs siguen el mismo patrón: 6 por día, ordenados
// Viejo slot 3 (09:45-10:30) = recreo → se reasigna al slot 3 nuevo (10:30-11:15)
// Viejo slot 5 (13:30-14:15) → se reasigna al slot 5 nuevo (12:00-12:45)
// Viejo slot 6 (14:15-15:00) → se reasigna al slot 6 nuevo (12:45-13:30)
// Los slots 1,2,4 quedan igual en su posición

// Para cada día, el viejo ID base es (dia_index * 6) + slot_offset (1-based)
// Nuevo ID sería (nuevo_dia_index * 6) + nuevo_slot_offset (1-based)

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Eliminar horarios viejos e insertar los nuevos con los mismo IDs
DB::table('horario')->truncate();

$nuevoId = 1;
$mapaViejoNuevo = [];

foreach ($dias as $diaIndex => $dia) {
    $baseViejoId = $diaIndex * 6 + 1; // IDs viejos: 1-6 Lunes, 7-12 Martes, etc.
    
    foreach ($slots as $slotIndex => $slot) {
        DB::table('horario')->insert([
            'id_horario'  => $nuevoId,
            'dia'         => $dia,
            'hora_inicio' => $slot['hora_inicio'],
            'hora_fin'    => $slot['hora_fin'],
        ]);
        
        // Mapear viejo → nuevo
        // slot 0 (8:00) viejo ID base+0 → nuevo id
        // slot 1 (8:45) viejo ID base+1 → nuevo id
        // slot 2 (10:30) viejo ID base+3 (era el 10:30) → nuevo id  
        // slot 3 (11:15) viejo ID base+? 
        // Los viejos eran: [0]=8:00, [1]=8:45, [2]=9:45(recreo), [3]=10:30, [4]=13:30, [5]=14:15
        // Los nuevos son:  [0]=8:00, [1]=8:45, [2]=10:30,        [3]=11:15, [4]=12:00, [5]=12:45
        
        if ($slotIndex == 0) $viejoOffset = 0; // 8:00 → 8:00
        if ($slotIndex == 1) $viejoOffset = 1; // 8:45 → 8:45
        if ($slotIndex == 2) $viejoOffset = 3; // 10:30 → (viejo era slot 3, 10:30)
        if ($slotIndex == 3) $viejoOffset = 2; // 11:15 → (viejo era slot 2, el recreo 9:45-10:30, reasignamos)
        if ($slotIndex == 4) $viejoOffset = 4; // 12:00 → (viejo era slot 4, 13:30)
        if ($slotIndex == 5) $viejoOffset = 5; // 12:45 → (viejo era slot 5, 14:15)
        
        $viejoId = $baseViejoId + $viejoOffset;
        $mapaViejoNuevo[$viejoId] = $nuevoId;
        $nuevoId++;
    }
}

// Reasignar materia_curso_gestion_paralelo
foreach ($mapaViejoNuevo as $viejo => $nuevo) {
    if ($viejo !== $nuevo) {
        DB::table('materia_curso_gestion_paralelo')
            ->where('id_horario', $viejo)
            ->update(['id_horario' => $nuevo]);
        echo "Reasignado horario $viejo → $nuevo\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Verificar resultado
echo "\n=== Tabla horario actualizada ===\n";
$nuevos = DB::table('horario')->orderBy('id_horario')->get();
foreach ($nuevos as $h) {
    echo "{$h->id_horario} | {$h->dia} | {$h->hora_inicio} - {$h->hora_fin}\n";
}
