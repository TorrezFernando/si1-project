<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('alumno', 'id_beca')) {
            Schema::table('alumno', function (Blueprint $table) {
                $table->integer('id_beca')->nullable()->after('fecha_nac');
                $table->foreign('id_beca')->references('id_beca')->on('beca')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('alumno', 'id_beca')) {
            Schema::table('alumno', function (Blueprint $table) {
                $table->dropForeign(['id_beca']);
                $table->dropColumn('id_beca');
            });
        }
    }
};
