<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('secretaria') && ! Schema::hasColumn('secretaria', 'id_user')) {
            Schema::table('secretaria', function (Blueprint $table) {
                $table->integer('id_user')->nullable()->after('id_secretaria');
                $table->unique('id_user', 'secretaria_id_user_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('secretaria') && Schema::hasColumn('secretaria', 'id_user')) {
            Schema::table('secretaria', function (Blueprint $table) {
                $table->dropUnique('secretaria_id_user_unique');
                $table->dropColumn('id_user');
            });
        }
    }
};
