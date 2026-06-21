<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beca', function (Blueprint $table) {
            $table->boolean('admin_only')->default(false)->after('activo');
        });

        DB::table('beca')->where('id_beca', 1)->update([
            'nombre' => 'Excelencia',
            'descripcion' => 'Beca Excelencia',
            'porcentaje' => 100.00,
            'admin_only' => false,
        ]);

        DB::table('beca')->where('id_beca', 2)->update([
            'nombre' => 'Deportiva',
            'descripcion' => 'Beca Deportiva',
            'porcentaje' => 50.00,
            'admin_only' => true,
        ]);

        DB::table('beca')->where('id_beca', 3)->update([
            'nombre' => 'Cultural',
            'descripcion' => 'Beca Cultural',
            'porcentaje' => 50.00,
            'admin_only' => true,
        ]);

        DB::table('beca')->where('id_beca', 4)->update([
            'nombre' => 'Convenio',
            'descripcion' => 'Beca Convenio',
            'porcentaje' => 50.00,
            'admin_only' => true,
        ]);

        DB::table('beca')->where('id_beca', 5)->update([
            'nombre' => 'Social',
            'descripcion' => 'Beca Social',
            'porcentaje' => 100.00,
            'admin_only' => true,
        ]);

        DB::table('beca')->insert([
            'nombre' => 'Hermanos',
            'descripcion' => 'Beca Hermanos',
            'porcentaje' => 100.00,
            'activo' => true,
            'admin_only' => false,
        ]);
    }

    public function down(): void
    {
        DB::table('beca')->where('nombre', 'Hermanos')->delete();

        Schema::table('beca', function (Blueprint $table) {
            $table->dropColumn('admin_only');
        });
    }
};
