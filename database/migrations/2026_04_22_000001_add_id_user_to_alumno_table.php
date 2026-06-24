<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('alumno', 'id_user')) {
            Schema::table('alumno', function (Blueprint $table) {
                $table->integer('id_user')->nullable()->after('id_alumno');
                $table->unique('id_user', 'alumno_id_user_unique');
            });
        }

        $usuarios = DB::table('usuario')
            ->where('id_rol', 3)
            ->select('id_user', 'username', 'password')
            ->get();

        foreach ($usuarios as $usuario) {
            if (! str_starts_with((string) $usuario->password, '$2y$')) {
                DB::table('usuario')
                    ->where('id_user', $usuario->id_user)
                    ->update([
                        'password' => Hash::make($usuario->password),
                    ]);
            }

            if (preg_match('/^alumno_(\d+)$/', $usuario->username, $matches)) {
                DB::table('alumno')
                    ->where('id_alumno', (int) $matches[1])
                    ->whereNull('id_user')
                    ->update([
                        'id_user' => $usuario->id_user,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('alumno', 'id_user')) {
            Schema::table('alumno', function (Blueprint $table) {
                $table->dropUnique('alumno_id_user_unique');
                $table->dropColumn('id_user');
            });
        }
    }
};
