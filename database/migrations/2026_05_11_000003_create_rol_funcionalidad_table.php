<?php

use App\Enums\Rol as RolEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rol_funcionalidad')) {
            Schema::create('rol_funcionalidad', function (Blueprint $table) {
                $table->unsignedInteger('id_rol');
                $table->unsignedBigInteger('id_funcionalidad');
                $table->primary(['id_rol', 'id_funcionalidad']);
            });
        }

        $this->registrarPermisosBase();
        $this->asignarPermisosIniciales();
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_funcionalidad');
    }

    private function registrarPermisosBase(): void
    {
        $modulos = [
            'Panel' => [
                'home-panel' => 'Acceder al panel principal',
                'profile' => 'Acceder a mi perfil',
            ],
            'Usuarios' => [
                'admin.personal-administrativo.index' => 'Gestionar personal administrativo',
                'admin.profesores.index' => 'Gestionar docentes',
                'admin.apoderados.index' => 'Gestionar tutores',
            ],
            'Academico' => [
                'admin.alumnos.index' => 'Gestionar estudiantes',
                'admin.cursos.index' => 'Gestionar cursos',
                'admin.materias.index' => 'Gestionar materias',
                'admin.notas.index' => 'Gestionar notas',
                'admin.fichas-medicas.index' => 'Gestionar fichas medicas',
                'admin.infraestructura.index' => 'Gestionar infraestructura',
                'admin.horarios.index' => 'Gestionar horarios',
                'admin.gestiones.index' => 'Gestionar anios escolares',
                'profesor.horario' => 'Consultar horario de profesores',
                'apoderado.consulta' => 'Consultar notas de hijos',
            ],
            'Financiero' => [
                'admin.pagos.index' => 'Gestionar pagos',
                'admin.reportes-financieros.index' => 'Consultar reportes financieros',
            ],
            'Seguridad' => [
                'admin.bitacora.index' => 'Consultar bitacora',
                'admin.modulos.index' => 'Gestionar modulos',
                'admin.funcionalidades.index' => 'Gestionar funcionalidades',
                'admin.permisos.index' => 'Asignar permisos por rol',
            ],
        ];

        foreach ($modulos as $moduloNombre => $funcionalidades) {
            $idModulo = DB::table('modulos')->where('nombre', $moduloNombre)->value('id_modulo');

            if (! $idModulo) {
                $idModulo = DB::table('modulos')->insertGetId([
                    'nombre' => $moduloNombre,
                    'descripcion' => 'Modulo ' . strtolower($moduloNombre),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($funcionalidades as $permiso => $descripcion) {
                $funcionalidad = DB::table('funcionalidades')
                    ->where('id_modulo', $idModulo)
                    ->where('nombre', $permiso)
                    ->first();

                if ($funcionalidad) {
                    DB::table('funcionalidades')
                        ->where('id_funcionalidad', $funcionalidad->id_funcionalidad)
                        ->update([
                            'descripcion' => $descripcion,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('funcionalidades')->insert([
                        'id_modulo' => $idModulo,
                        'nombre' => $permiso,
                        'descripcion' => $descripcion,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function asignarPermisosIniciales(): void
    {
        $asignaciones = [
            RolEnum::PROFESOR->value => ['home-panel', 'profile', 'profesor.horario', 'admin.notas.index'],
            RolEnum::APODERADO->value => ['home-panel', 'profile', 'apoderado.consulta'],
            RolEnum::SECRETARIA->value => [
                'home-panel',
                'profile',
                'admin.alumnos.index',
                'admin.apoderados.index',
                'admin.notas.index',
                'admin.fichas-medicas.index',
                'admin.infraestructura.index',
                'admin.horarios.index',
            ],
        ];

        foreach ($asignaciones as $idRol => $permisos) {
            foreach ($permisos as $permiso) {
                $idFuncionalidad = DB::table('funcionalidades')->where('nombre', $permiso)->value('id_funcionalidad');

                if ($idFuncionalidad) {
                    $existePermiso = DB::table('rol_funcionalidad')
                        ->where('id_rol', $idRol)
                        ->where('id_funcionalidad', $idFuncionalidad)
                        ->exists();

                    if (! $existePermiso) {
                        DB::table('rol_funcionalidad')->insert([
                            'id_rol' => $idRol,
                            'id_funcionalidad' => $idFuncionalidad,
                        ]);
                    }
                }
            }
        }
    }
};
