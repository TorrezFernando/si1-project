<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curso', function (Blueprint $table) {
            $table->string('grado')->nullable()->after('nombre');
            $table->unsignedBigInteger('id_nivel')->nullable()->after('grado');
            $table->unsignedInteger('id_paralelo')->nullable()->after('id_nivel');
            $table->unsignedBigInteger('id_turno')->nullable()->after('id_paralelo');
        });

        Schema::table('materia', function (Blueprint $table) {
            $table->unsignedInteger('carga_horaria')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('curso', function (Blueprint $table) {
            $table->dropColumn(['grado', 'id_nivel', 'id_paralelo', 'id_turno']);
        });

        Schema::table('materia', function (Blueprint $table) {
            $table->dropColumn('carga_horaria');
        });
    }
};
