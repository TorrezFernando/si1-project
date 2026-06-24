<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beca', function (Blueprint $table) {
            if (!Schema::hasColumn('beca', 'nombre')) {
                $table->string('nombre', 100)->after('id_beca');
            }
            if (!Schema::hasColumn('beca', 'porcentaje')) {
                $table->decimal('porcentaje', 5, 2)->after('nombre');
            }
            if (!Schema::hasColumn('beca', 'activo')) {
                $table->boolean('activo')->default(true)->after('porcentaje');
            }
        });

        DB::table('beca')->whereNull('nombre')->update(['nombre' => DB::raw('descripcion')]);
        DB::table('beca')->whereNull('porcentaje')->update(['porcentaje' => 0]);
    }

    public function down(): void
    {
        Schema::table('beca', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'porcentaje', 'activo']);
        });
    }
};
