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
        if (Schema::hasTable('pago_mensual')) {
            Schema::table('pago_mensual', function (Blueprint $table) {
                if (! Schema::hasColumn('pago_mensual', 'estado')) {
                    $table->string('estado', 20)->default('Pendiente')->after('descuento');
                }

                if (! Schema::hasColumn('pago_mensual', 'fecha_pago')) {
                    $table->date('fecha_pago')->nullable()->after('fecha');
                }
            });

            DB::table('pago_mensual')
                ->whereNull('fecha_pago')
                ->update([
                    'estado' => 'Pagado',
                    'fecha_pago' => DB::raw('fecha'),
                ]);
        }

        if (! Schema::hasTable('modulos') || ! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $idModulo = DB::table('modulos')->where('nombre', 'Financiero')->value('id_modulo')
            ?: DB::table('modulos')->insertGetId([
                'nombre' => 'Financiero',
                'descripcion' => 'Modulo financiero',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', 'admin.mensualidades.index')
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => 'Gestionar mensualidades',
                    'updated_at' => now(),
                ]);
        } else {
            $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                'id_modulo' => $idModulo,
                'nombre' => 'admin.mensualidades.index',
                'descripcion' => 'Gestionar mensualidades',
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
                ->where('nombre', 'admin.mensualidades.index')
                ->value('id_funcionalidad');

            if ($idFuncionalidad) {
                DB::table('rol_funcionalidad')->where('id_funcionalidad', $idFuncionalidad)->delete();
                DB::table('funcionalidades')->where('id_funcionalidad', $idFuncionalidad)->delete();
            }
        }

        if (Schema::hasTable('pago_mensual')) {
            Schema::table('pago_mensual', function (Blueprint $table) {
                if (Schema::hasColumn('pago_mensual', 'fecha_pago')) {
                    $table->dropColumn('fecha_pago');
                }

                if (Schema::hasColumn('pago_mensual', 'estado')) {
                    $table->dropColumn('estado');
                }
            });
        }
    }
};
