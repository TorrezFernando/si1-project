<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionalidades', function (Blueprint $table) {
            $table->id('id_funcionalidad');
            $table->foreignId('id_modulo')->constrained('modulos', 'id_modulo')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->unique(['id_modulo', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionalidades');
    }
};
