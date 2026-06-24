<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('apoderado') && ! Schema::hasColumn('apoderado', 'id_user')) {
            Schema::table('apoderado', function (Blueprint $table) {
                $table->integer('id_user')->nullable()->after('id_apoderado');
                $table->unique('id_user', 'apoderado_id_user_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('apoderado') && Schema::hasColumn('apoderado', 'id_user')) {
            Schema::table('apoderado', function (Blueprint $table) {
                $table->dropUnique('apoderado_id_user_unique');
                $table->dropColumn('id_user');
            });
        }
    }
};
