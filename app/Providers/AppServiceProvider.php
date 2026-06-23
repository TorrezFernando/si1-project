<?php

namespace App\Providers;

use App\Enums\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

// Proveedor principal: registra reglas globales de permisos y configuracion de Laravel.
class AppServiceProvider extends ServiceProvider
{
    // Registra servicios en el contenedor de Laravel.
    public function register(): void
    {
        //
    }

    // Configura reglas que se cargan al iniciar la aplicacion.
    public function boot(): void
    {
        // Compatibilidad con indices antiguos de MySQL.
        Schema::defaultStringLength(191);

        // CU09: Permiso dinamico global basado en rol_funcionalidad.
        Gate::before(function ($user, string $ability) {
            // CU01: El administrador puede ejecutar cualquier accion.
            if ((int) $user->id_rol === Rol::ADMIN->value) {
                return true;
            }

            // CU09: Si las tablas aun no existen, deja que otras reglas decidan.
            if (! Schema::hasTable('rol_funcionalidad') || ! Schema::hasTable('funcionalidades')) {
                return null;
            }

            // CU09: Verifica si el rol tiene asignada la funcionalidad solicitada.
            return DB::table('rol_funcionalidad as rf')
                ->join('funcionalidades as f', 'f.id_funcionalidad', '=', 'rf.id_funcionalidad')
                ->where('rf.id_rol', (int) $user->id_rol)
                ->where('f.nombre', $ability)
                ->exists() ?: null;
        });

        // Regla unica de contrasena: minimo 8 caracteres, mayuscula, minuscula, numero y simbolo.
        Password::defaults(function () {
            return Password::min(8)->letters()->mixedCase()->numbers()->symbols();
        });

        // CU01: Gate simple para administrador.
        Gate::define('is-admin', function ($user) {
            return (int) $user->id_rol === Rol::ADMIN->value;
        });

        // CU02: Gate simple para profesor.
        Gate::define('is-profesor', function ($user) {
            return (int) $user->id_rol === Rol::PROFESOR->value;
        });

        // CU04: Gate simple para apoderado.
        Gate::define('is-apoderado', function ($user) {
            return (int) $user->id_rol === Rol::APODERADO->value;
        });

        // CU23: Permiso de acceso a fichas medicas.
        Gate::define('fichas-medicas', function ($user) {
            return $this->tienePermiso($user, 'admin.fichas-medicas.index');
        });

        // CU09: Gate parametrico para permisos dinamicos.
        Gate::define('permiso-dinamico', function ($user, string $permiso) {
            return $this->tienePermiso($user, $permiso);
        });

        // Permiso para el nuevo modulo de reportes estaticos.
        Gate::define('admin.reportes_estaticos.index', function ($user) {
            return in_array((int) $user->id_rol, [
                Rol::ADMIN->value,
                Rol::SECRETARIA->value,
                Rol::PROFESOR->value,
                Rol::APODERADO->value
            ], true);
        });

        // CU14 y CU02: Permiso especifico para que docentes vean horario.
        Gate::define('profesor-horario', function ($user) {
            // CU09: Si el rol tiene permiso dinamico, permite acceso.
            if ($this->tienePermiso($user, 'profesor.horario')) {
                return true;
            }

            $inicio = microtime(true);

            // CU02: Solo profesores llegan a la comprobacion individual.
            if ((int) $user->id_rol !== Rol::PROFESOR->value) {
                Log::info('[PERF] Gate profesor-horario', [
                    'ms' => round((microtime(true) - $inicio) * 1000, 2),
                    'resultado' => false,
                    'motivo' => 'no_profesor',
                    'id_user' => $user->id_user ?? null,
                ]);

                return false;
            }

            $inicioSchema = microtime(true);
            // CU14: Si faltan tablas durante instalacion/migracion, no bloquea al profesor.
            if (!Schema::hasTable('profesor_permisos') || !Schema::hasTable('profesor') || !Schema::hasColumn('profesor', 'id_user')) {
                Log::info('[PERF] Gate profesor-horario schema', [
                    'ms' => round((microtime(true) - $inicioSchema) * 1000, 2),
                    'id_user' => $user->id_user ?? null,
                ]);

                Log::info('[PERF] Gate profesor-horario', [
                    'ms' => round((microtime(true) - $inicio) * 1000, 2),
                    'resultado' => true,
                    'motivo' => 'schema_incompleto',
                    'id_user' => $user->id_user ?? null,
                ]);

                return true;
            }

            Log::info('[PERF] Gate profesor-horario schema', [
                'ms' => round((microtime(true) - $inicioSchema) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);

            $inicioConsulta = microtime(true);
            // CU14: Consulta el permiso individual del profesor.
            $resultado = (bool) DB::table('profesor as p')
                ->join('profesor_permisos as pp', 'pp.id_profesor', '=', 'p.id_profesor')
                ->where('p.id_user', $user->id_user)
                ->value('pp.puede_ver_horario');

            Log::info('[PERF] Gate profesor-horario consulta', [
                'ms' => round((microtime(true) - $inicioConsulta) * 1000, 2),
                'resultado' => $resultado,
                'id_user' => $user->id_user ?? null,
            ]);

            Log::info('[PERF] Gate profesor-horario', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => $resultado,
                'motivo' => 'consulta_permiso',
                'id_user' => $user->id_user ?? null,
            ]);

            return $resultado;
        });
    }

    // CU09: Verifica si un usuario tiene una funcionalidad asignada por su rol.
    private function tienePermiso($user, string $permiso): bool
    {
        // CU01: Administrador siempre tiene permiso.
        if ((int) $user->id_rol === Rol::ADMIN->value) {
            return true;
        }

        // CU09: Sin tablas de permisos, no concede permisos dinamicos.
        if (! Schema::hasTable('rol_funcionalidad') || ! Schema::hasTable('funcionalidades')) {
            return false;
        }

        // CU09: Busca permiso en la relacion rol_funcionalidad.
        return DB::table('rol_funcionalidad as rf')
            ->join('funcionalidades as f', 'f.id_funcionalidad', '=', 'rf.id_funcionalidad')
            ->where('rf.id_rol', (int) $user->id_rol)
            ->where('f.nombre', $permiso)
            ->exists();
    }
}
