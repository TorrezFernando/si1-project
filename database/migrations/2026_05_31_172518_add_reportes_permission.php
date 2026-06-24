<?php

use App\Enums\Rol as RolEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('modulos') || ! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        // Buscar o crear el módulo 'Seguridad'
        $idModulo = DB::table('modulos')->where('nombre', 'Seguridad')->value('id_modulo');

        if (! $idModulo) {
            $idModulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Seguridad',
                'descripcion' => 'Modulo de seguridad',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Registrar la funcionalidad 'admin.reportes.index'
        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.reportes.index')
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => 'Generar reportes y consultas',
                    'updated_at' => now(),
                ]);
        } else {
            $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                'id_modulo' => $idModulo,
                'nombre' => 'admin.reportes.index',
                'descripcion' => 'Generar reportes y consultas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Asignar el permiso a los roles SECRETARIA, PROFESOR, APODERADO
        $roles = [
            RolEnum::SECRETARIA->value,
            RolEnum::PROFESOR->value,
            RolEnum::APODERADO->value,
        ];

        foreach ($roles as $idRol) {
            $existe = DB::table('rol_funcionalidad')
                ->where('id_rol', $idRol)
                ->where('id_funcionalidad', $idFuncionalidad)
                ->exists();

            if (! $existe) {
                DB::table('rol_funcionalidad')->insert([
                    'id_rol' => $idRol,
                    'id_funcionalidad' => $idFuncionalidad,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.reportes.index')
            ->value('id_funcionalidad');

        if (! $idFuncionalidad) {
            return;
        }

        DB::table('rol_funcionalidad')
            ->where('id_funcionalidad', $idFuncionalidad)
            ->delete();

        DB::table('funcionalidades')
            ->where('id_funcionalidad', $idFuncionalidad)
            ->delete();
    }
};
