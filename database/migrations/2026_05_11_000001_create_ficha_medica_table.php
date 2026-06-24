<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ficha_medica')) {
            Schema::create('ficha_medica', function (Blueprint $table) {
                $table->id('id_ficha');
                $table->string('tipo_sangre', 5);
                $table->string('alergias', 100)->nullable();
                $table->string('contacto_emergencia', 100);
                $table->string('telf_emerg', 20);
                $table->unsignedInteger('id_alumno')->unique();
            });

            return;
        }

        Schema::table('ficha_medica', function (Blueprint $table) {
            if (! Schema::hasColumn('ficha_medica', 'contacto_emergencia')) {
                $table->string('contacto_emergencia', 100)->nullable()->after('alergias');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('ficha_medica') && Schema::hasColumn('ficha_medica', 'contacto_emergencia')) {
            Schema::table('ficha_medica', function (Blueprint $table) {
                $table->dropColumn('contacto_emergencia');
            });
        }
    }
};
