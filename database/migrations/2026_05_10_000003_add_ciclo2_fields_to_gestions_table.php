<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gestions', function (Blueprint $table) {
            $table->date('fechainicio')->nullable()->after('nombre');
            $table->date('fechafin')->nullable()->after('fechainicio');
            $table->boolean('activo')->default(false)->after('fechafin');
        });
    }

    public function down(): void
    {
        Schema::table('gestions', function (Blueprint $table) {
            $table->dropColumn(['fechainicio', 'fechafin', 'activo']);
        });
    }
};
