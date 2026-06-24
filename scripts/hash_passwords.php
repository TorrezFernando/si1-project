<?php

/**
 * Script para hashear las contraseñas en texto plano de la BD.
 * Ejecutar: php hash_passwords.php
 *
 * Detecta qué registros tienen contraseña sin hash ($2y$ = bcrypt)
 * y los actualiza con Hash::make().
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$usuarios = DB::table('usuario')->get();

$actualizados = 0;
$omitidos    = 0;

foreach ($usuarios as $u) {
    // Si ya es un hash bcrypt/argon lo saltamos
    if (str_starts_with($u->password, '$2y$') || str_starts_with($u->password, '$argon')) {
        $omitidos++;
        continue;
    }

    // Guardamos la contraseña original y la hasheamos
    $plain = $u->password;

    DB::table('usuario')
        ->where('id_user', $u->id_user)
        ->update(['password' => Hash::make($plain)]);

    $actualizados++;
    echo "Actualizado: {$u->username} (id_user={$u->id_user})\n";
}

echo "\n✅ Proceso completado.\n";
echo "   Actualizados : {$actualizados}\n";
echo "   Ya hasheados : {$omitidos}\n";
