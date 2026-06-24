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
        if (! Schema::hasColumn('profesor', 'id_user')) {
            Schema::table('profesor', function (Blueprint $table) {
                $table->integer('id_user')->nullable()->after('id_profesor');
                $table->unique('id_user', 'profesor_id_user_unique');
            });
        }

        if (! Schema::hasTable('profesor_permisos')) {
            Schema::create('profesor_permisos', function (Blueprint $table) {
                $table->id();
                $table->integer('id_profesor')->unique();
                $table->boolean('puede_ver_horario')->default(true);
            });
        }

        $profesores = DB::table('profesor')->orderBy('id_profesor')->get();

        foreach ($profesores as $profesor) {
            $username = 'profesor_'.$profesor->id_profesor;
            $defaultPassword = 'prof'.str_pad((string) $profesor->id_profesor, 3, '0', STR_PAD_LEFT);

            $usuario = null;

            if (! empty($profesor->id_user)) {
                $usuario = DB::table('usuario')
                    ->where('id_user', $profesor->id_user)
                    ->where('id_rol', 2)
                    ->first();
            }

            if (! $usuario) {
                $usuario = DB::table('usuario')
                    ->where('username', $username)
                    ->where('id_rol', 2)
                    ->first();
            }

            if (! $usuario) {
                $idUser = DB::table('usuario')->insertGetId([
                    'username' => $username,
                    'password' => Hash::make($defaultPassword),
                    'id_rol' => 2,
                    'remember_token' => null,
                ]);

                $usuario = DB::table('usuario')->where('id_user', $idUser)->first();
            } elseif (! str_starts_with((string) $usuario->password, '$2y$')) {
                DB::table('usuario')
                    ->where('id_user', $usuario->id_user)
                    ->update([
                        'password' => Hash::make($usuario->password),
                    ]);
            }

            DB::table('profesor')
                ->where('id_profesor', $profesor->id_profesor)
                ->update([
                    'id_user' => $usuario->id_user,
                ]);

            $permisoExiste = DB::table('profesor_permisos')
                ->where('id_profesor', $profesor->id_profesor)
                ->exists();

            if (! $permisoExiste) {
                DB::table('profesor_permisos')->insert([
                    'id_profesor' => $profesor->id_profesor,
                    'puede_ver_horario' => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('profesor_permisos')) {
            Schema::dropIfExists('profesor_permisos');
        }

        if (Schema::hasColumn('profesor', 'id_user')) {
            Schema::table('profesor', function (Blueprint $table) {
                $table->dropUnique('profesor_id_user_unique');
                $table->dropColumn('id_user');
            });
        }
    }
};
