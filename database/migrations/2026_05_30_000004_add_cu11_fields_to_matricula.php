<?php

use App\Enums\Rol as RolEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('matricula') && ! Schema::hasColumn('matricula', 'estado')) {
            Schema::table('matricula', function (Blueprint $table) {
                $table->string('estado', 20)->default('Pendiente')->after('fecha');
            });
        }

        if (Schema::hasTable('inscripcion') && Schema::hasTable('apoderado') && ! Schema::hasColumn('inscripcion', 'id_apoderado')) {
            Schema::table('inscripcion', function (Blueprint $table) {
                $table->integer('id_apoderado')->nullable()->after('id_alumno');
            });
        }

        if (! Schema::hasTable('modulos') || ! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $idModulo = DB::table('modulos')->where('nombre', 'Academico')->value('id_modulo')
            ?: DB::table('modulos')->insertGetId([
                'nombre' => 'Academico',
                'descripcion' => 'Modulo academico',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.matriculas.index')
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => 'Gestionar matriculas',
                    'updated_at' => now(),
                ]);
        } else {
            $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                'id_modulo' => $idModulo,
                'nombre' => 'admin.matriculas.index',
                'descripcion' => 'Gestionar matriculas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ([RolEnum::SECRETARIA->value] as $idRol) {
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
        if (Schema::hasTable('funcionalidades') && Schema::hasTable('rol_funcionalidad')) {
            $idFuncionalidad = DB::table('funcionalidades')
                ->where('nombre', 'admin.matriculas.index')
                ->value('id_funcionalidad');

            if ($idFuncionalidad) {
                DB::table('rol_funcionalidad')->where('id_funcionalidad', $idFuncionalidad)->delete();
                DB::table('funcionalidades')->where('id_funcionalidad', $idFuncionalidad)->delete();
            }
        }

        if (Schema::hasTable('inscripcion') && Schema::hasColumn('inscripcion', 'id_apoderado')) {
            Schema::table('inscripcion', function (Blueprint $table) {
                $table->dropColumn('id_apoderado');
            });
        }

        if (Schema::hasTable('matricula') && Schema::hasColumn('matricula', 'estado')) {
            Schema::table('matricula', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};
