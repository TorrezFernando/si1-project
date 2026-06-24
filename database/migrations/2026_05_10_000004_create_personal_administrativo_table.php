<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_administrativo', function (Blueprint $table) {
            $table->id('id_personal_administrativo');
            $table->string('ci')->unique();
            $table->string('nombre', 50);
            $table->string('ap_paterno', 50);
            $table->string('ap_materno', 50)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('cargo', 50);
            $table->string('area', 50);
            $table->date('fecha_ingreso');
            $table->unsignedInteger('id_user')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_administrativo');
    }
};
