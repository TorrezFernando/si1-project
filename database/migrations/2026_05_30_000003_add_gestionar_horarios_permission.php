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

        $idModulo = DB::table('modulos')->where('nombre', 'Academico')->value('id_modulo');

        if (! $idModulo) {
            $idModulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Academico',
                'descripcion' => 'Modulo academico',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.horarios.index')
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => 'Gestionar horarios',
                    'updated_at' => now(),
                ]);
        } else {
            $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                'id_modulo' => $idModulo,
                'nombre' => 'admin.horarios.index',
                'descripcion' => 'Gestionar horarios',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $existe = DB::table('rol_funcionalidad')
            ->where('id_rol', RolEnum::SECRETARIA->value)
            ->where('id_funcionalidad', $idFuncionalidad)
            ->exists();

        if (! $existe) {
            DB::table('rol_funcionalidad')->insert([
                'id_rol' => RolEnum::SECRETARIA->value,
                'id_funcionalidad' => $idFuncionalidad,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.horarios.index')
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
