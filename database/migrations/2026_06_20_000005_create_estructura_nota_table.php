<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('estructura_nota')) {
            Schema::create('estructura_nota', function (Blueprint $table) {
                $table->integer('id_estructura', true);
                $table->integer('id_materia');
                $table->string('componente', 20);
                $table->decimal('porcentaje', 5, 2)->default(25.00);
                $table->boolean('activo')->default(true);
                $table->foreign('id_materia')->references('id_materia')->on('materia')->onDelete('cascade');
            });
        }

        $componentes = [
            ['componente' => 'SER', 'porcentaje' => 25.00],
            ['componente' => 'SABER', 'porcentaje' => 25.00],
            ['componente' => 'HACER', 'porcentaje' => 25.00],
            ['componente' => 'AUTOEVALUACION', 'porcentaje' => 25.00],
        ];

        $materias = DB::table('materia')->pluck('id_materia');

        foreach ($materias as $idMateria) {
            $existe = DB::table('estructura_nota')
                ->where('id_materia', $idMateria)
                ->exists();

            if (! $existe) {
                foreach ($componentes as $comp) {
                    DB::table('estructura_nota')->insert([
                        'id_materia' => $idMateria,
                        'componente' => $comp['componente'],
                        'porcentaje' => $comp['porcentaje'],
                        'activo' => true,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('estructura_nota');
    }
};
