<?php

namespace App\Domain\Apoderados\Services;

use App\Models\Apoderado;
use App\Models\Parentesco;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// CU04: Servicio relacionado con tutor/apoderado y sus hijos.
class ApoderadoService
{
    // CU04 y CU01: Resuelve un apoderado desde el username del usuario.
    public function resolverPorUsername(string $username): ?object
    {
        // CU04 y CU01: Primero intenta resolver por el usuario vinculado directamente.
        $usuario = DB::table('usuario')
            ->where('username', $username)
            ->where('id_rol', 4)
            ->first();

        if ($usuario && DB::getSchemaBuilder()->hasColumn('apoderado', 'id_user')) {
            $apoderado = DB::table('apoderado')
                ->where('id_user', $usuario->id_user)
                ->first();

            if ($apoderado) {
                return $apoderado;
            }
        }

        // CU04: Busca apoderado cuyo username siga el formato apoderado_id.
        $apoderado = DB::table('apoderado')
            ->whereRaw("CONCAT('apoderado_', id_apoderado) = ?", [$username])
            ->first();

        // CU04: Devuelve apoderado encontrado por concatenacion.
        if ($apoderado) {
            return $apoderado;
        }

        // CU04: Extrae id desde username apoderado_### si aplica.
        if (preg_match('/^apoderado_(\d+)$/', $username, $matches)) {
            // CU04: Busca apoderado directamente por id.
            return DB::table('apoderado')
                ->where('id_apoderado', (int) $matches[1])
                ->first();
        }

        // CU04: Retorna null si el usuario no corresponde a un apoderado.
        return null;
    }

    // CU04: Obtiene los hijos vinculados a un apoderado.
    public function obtenerHijosDeApoderado(int $idApoderado): Collection
    {
        // CU04: Consulta relacion parentesco -> alumno para este apoderado.
        return DB::table('parentesco as p')
            ->join('alumno as a', 'a.id_alumno', '=', 'p.id_alumno')
            ->where('p.id_apoderado', $idApoderado)
            ->select(
                'a.id_alumno',
                'a.nombres',
                'a.ap_paterno',
                'a.ap_materno',
                'p.descripcion as parentesco'
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get()
            ->map(function ($hijo) {
                // CU04: Agrega nombre completo para mostrar en la vista.
                $hijo->nombre_completo = trim(
                    $hijo->nombres.' '.$hijo->ap_paterno.' '.$hijo->ap_materno
                );
                return $hijo;
            });
    }

    // CU04: Obtiene todos los alumnos con sus apoderados para vista administrativa.
    public function obtenerTodosLosHijosConApoderados(): Collection
    {
        // CU04: Consulta alumnos y concatena apoderados vinculados.
        return DB::table('alumno as a')
            ->leftJoin('parentesco as p', 'p.id_alumno', '=', 'a.id_alumno')
            ->leftJoin('apoderado as ap', 'ap.id_apoderado', '=', 'p.id_apoderado')
            ->select(
                'a.id_alumno',
                'a.nombres',
                'a.ap_paterno',
                'a.ap_materno',
                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno)
                        ORDER BY ap.ap_paterno, ap.ap_materno, ap.nombres
                        SEPARATOR ' | '
                    ) as apoderados
                "),
                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT CONCAT_WS(': ', CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno), p.descripcion)
                        ORDER BY ap.ap_paterno, ap.ap_materno, ap.nombres
                        SEPARATOR ' | '
                    ) as apoderados_detalle
                ")
            )
            ->groupBy('a.id_alumno', 'a.nombres', 'a.ap_paterno', 'a.ap_materno')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get()
            ->map(function ($hijo) {
                // CU04: Agrega nombre completo para mostrar en la vista.
                $hijo->nombre_completo = trim(
                    $hijo->nombres.' '.$hijo->ap_paterno.' '.$hijo->ap_materno
                );
                return $hijo;
            });
    }
}
