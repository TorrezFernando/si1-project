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
        if (Schema::hasTable('matricula')) {
            Schema::table('matricula', function (Blueprint $table) {
                if (! Schema::hasColumn('matricula', 'estado_pago')) {
                    $table->string('estado_pago', 20)->default('Pendiente')->after('estado');
                }

                if (! Schema::hasColumn('matricula', 'fecha_pago')) {
                    $table->date('fecha_pago')->nullable()->after('fecha');
                }

                if (! Schema::hasColumn('matricula', 'monto_pagado')) {
                    $table->decimal('monto_pagado', 10, 2)->nullable()->after('monto');
                }

                if (! Schema::hasColumn('matricula', 'motivo_anulacion')) {
                    $table->string('motivo_anulacion', 255)->nullable()->after('estado_pago');
                }
            });
        }

        if (Schema::hasTable('pago_mensual') && ! Schema::hasColumn('pago_mensual', 'motivo_anulacion')) {
            Schema::table('pago_mensual', function (Blueprint $table) {
                $table->string('motivo_anulacion', 255)->nullable()->after('estado');
            });
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
            ->where('nombre', 'admin.pagos.index')
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => 'Gestionar pagos',
                    'updated_at' => now(),
                ]);
        } else {
            $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                'id_modulo' => $idModulo,
                'nombre' => 'admin.pagos.index',
                'descripcion' => 'Gestionar pagos',
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
        if (Schema::hasTable('pago_mensual') && Schema::hasColumn('pago_mensual', 'motivo_anulacion')) {
            Schema::table('pago_mensual', function (Blueprint $table) {
                $table->dropColumn('motivo_anulacion');
            });
        }

        if (Schema::hasTable('matricula')) {
            Schema::table('matricula', function (Blueprint $table) {
                foreach (['motivo_anulacion', 'monto_pagado', 'fecha_pago', 'estado_pago'] as $column) {
                    if (Schema::hasColumn('matricula', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
