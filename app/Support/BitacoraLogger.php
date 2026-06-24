<?php

namespace App\Support;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// CU05: Clase auxiliar para guardar registros en la bitacora.
class BitacoraLogger
{
    // CU05: Registra una accion realizada por un usuario.
    public static function log(Request $request, string $accion, ?User $user = null): void
    {
        // CU05: Marca inicio para medir rendimiento del registro.
        $inicio = microtime(true);
        // CU05: Usa el usuario recibido o el usuario autenticado de la peticion.
        $user ??= $request->user();

        // CU05: Omite registro si no hay usuario o accion.
        if (! $user || trim($accion) === '') {
            Log::info('[PERF] BitacoraLogger::log omitido', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'accion' => $accion,
                'tiene_usuario' => (bool) $user,
            ]);

            return;
        }

        // CU05: Crea el registro de bitacora en base de datos.
        Bitacora::create([
            'id_user' => $user->id_user,
            'fecha_hora' => now(),
            'accion' => $accion,
            'ip' => $request->ip(),
        ]);

        // CU05: Registra metrica tecnica de confirmacion.
        Log::info('[PERF] BitacoraLogger::log create', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'accion' => $accion,
            'id_user' => $user->id_user,
            'route' => $request->route()?->getName(),
        ]);
    }
}
