<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracion que crea la tabla pasarela_transacciones.
// Almacena el seguimiento de cada pago enviado a la pasarela Libelula.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasarela_transacciones', function (Blueprint $table) {
            $table->id();                                         // Identificador unico de la transaccion
            $table->string('tipo', 20);                           // Tipo: 'matricula' | 'mensualidad'
            $table->integer('id_referencia');                     // ID de la obligacion en su tabla origen
            $table->string('identificador_deuda', 100);           // Identificador unico enviado a Libelula
            $table->text('url_pasarela')->nullable();             // URL de la pasarela para redirigir al cliente
            $table->decimal('monto', 10, 2);                      // Monto de la deuda
            $table->string('estado', 20)->default('pendiente');   // Estado: pendiente | pagado | fallido
            $table->string('transaccion_id', 100)->nullable();    // ID de transaccion devuelto por Libelula (callback)
            $table->dateTime('fecha_pago_pasarela')->nullable();  // Fecha en que se confirmo el pago
            $table->timestamps();                                 // created_at, updated_at

            $table->index(['tipo', 'id_referencia']);             // Indice compuesto para busquedas por obligacion
            $table->index('estado');                              // Indice para filtrar por estado
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasarela_transacciones');
    }
};
