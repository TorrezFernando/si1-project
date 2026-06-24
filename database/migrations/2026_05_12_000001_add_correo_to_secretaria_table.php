<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('secretaria') && ! Schema::hasColumn('secretaria', 'correo')) {
            Schema::table('secretaria', function (Blueprint $table) {
                $table->string('correo', 100)->nullable()->after('telefono');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('secretaria') && Schema::hasColumn('secretaria', 'correo')) {
            Schema::table('secretaria', function (Blueprint $table) {
                $table->dropColumn('correo');
            });
        }
    }
};
