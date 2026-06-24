<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aula')) {
            return;
        }

        Schema::table('aula', function (Blueprint $table) {
            if (! Schema::hasColumn('aula', 'nombre')) {
                $table->string('nombre', 100)->nullable()->after('id_aula');
            }

            if (! Schema::hasColumn('aula', 'capacidad')) {
                $table->unsignedInteger('capacidad')->default(30)->after('tipo');
            }

            if (! Schema::hasColumn('aula', 'ubicacion')) {
                $table->string('ubicacion', 100)->default('Por definir')->after('capacidad');
            }

            if (! Schema::hasColumn('aula', 'estado')) {
                $table->string('estado', 20)->default('Activo')->after('ubicacion');
            }
        });

        DB::table('aula')
            ->whereNull('nombre')
            ->update([
                'nombre' => DB::raw('tipo'),
                'estado' => 'Activo',
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('aula')) {
            return;
        }

        Schema::table('aula', function (Blueprint $table) {
            foreach (['estado', 'ubicacion', 'capacidad', 'nombre'] as $column) {
                if (Schema::hasColumn('aula', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
