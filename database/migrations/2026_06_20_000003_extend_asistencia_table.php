<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('asistencia')) {
            return;
        }

        Schema::table('asistencia', function (Blueprint $table) {
            if (! Schema::hasColumn('asistencia', 'id_materia')) {
                $table->integer('id_materia')->nullable()->after('id_matricula');
                $table->foreign('id_materia')->references('id_materia')->on('materia')->onDelete('cascade');
            }
            if (! Schema::hasColumn('asistencia', 'id_gestion')) {
                $table->integer('id_gestion')->nullable()->after('id_materia');
                $table->foreign('id_gestion')->references('id_gestion')->on('gestion')->onDelete('cascade');
            }
            if (! Schema::hasColumn('asistencia', 'id_curso')) {
                $table->integer('id_curso')->nullable()->after('id_gestion');
                $table->foreign('id_curso')->references('id_curso')->on('curso')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('asistencia')) {
            return;
        }

        Schema::table('asistencia', function (Blueprint $table) {
            $table->dropForeign(['id_materia']);
            $table->dropForeign(['id_gestion']);
            $table->dropForeign(['id_curso']);
            $table->dropColumn(['id_materia', 'id_gestion', 'id_curso']);
        });
    }
};
