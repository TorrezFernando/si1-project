<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    const STUDENTS_PER_COURSE = 30;
    const GESTION = 2;
    const MATERIAS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    const TRIMESTRES = [1, 2, 3];
    const CURSOS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
    const MESES = ['Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre'];

    public function run(): void
    {
        $this->command->info('Limpiando datos transaccionales...');
        $this->cleanData();

        $this->command->info('Poblando tablas maestras vacias...');
        $this->seedMasterLookups();

        $this->command->info('Creando usuarios y alumnos...');
        $this->seedAlumnos();

        $this->command->info('Creando apoderados y parentescos...');
        $this->seedApoderados();

        $this->command->info('Creando fichas medicas...');
        $this->seedFichasMedicas();

        $this->command->info('Creando matriculas e inscripciones...');
        $this->seedMatriculas();

        $this->command->info('Creando notas...');
        $this->seedNotas();

        $this->command->info('Asignando becas automaticas (Excelencia y Hermanos)...');
        $this->seedBecas();

        $this->command->info('Creando pagos mensuales...');
        $this->seedPagos();

        $this->command->info('Creando asistencias...');
        $this->seedAsistencias();

        $this->command->info('Seeder completado!');
    }

    private function cleanData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'asistencia', 'pago_mensual', 'nota', 'inscripcion_curso_gestion',
            'inscripcion', 'matricula', 'parentesco', 'ficha_medica',
            'alumno', 'apoderado',
        ];
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }

        DB::table('usuario')->whereIn('id_rol', [3, 4])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedMasterLookups(): void
    {
        if (DB::table('nivels')->count() === 0) {
            DB::table('nivels')->insert([
                ['nombre' => 'Inicial'],
                ['nombre' => 'Primaria'],
                ['nombre' => 'Secundaria'],
            ]);
        }
        if (DB::table('turnos')->count() === 0) {
            DB::table('turnos')->insert([
                ['nombre' => 'Mañana'],
                ['nombre' => 'Tarde'],
            ]);
        }
        if (DB::table('configuraciones')->count() === 0) {
            DB::table('configuraciones')->insert([
                'nombre' => 'Colegio Test',
                'descripcion' => 'Colegio de prueba para testing',
                'direccion' => 'Av. Siempre Viva Nro 742',
                'telefono' => '28000000',
                'divisa' => 'Bolivianos',
                'correo_electronico' => 'info@colegiotest.edu.bo',
            ]);
        }
    }

    private array $familyGroup = [];

    private function seedAlumnos(): void
    {
        $nombres = [
            'Camila', 'Lucas', 'Paula', 'Mateo', 'Valentina', 'Santiago', 'Isabella',
            'Sebastian', 'Gabriela', 'Benjamin', 'Emily', 'Diego', 'Sofia', 'Samuel',
            'Abigail', 'Joaquin', 'Ariana', 'Daniel', 'Victoria', 'Alexander', 'Mariana',
            'Thiago', 'Luciana', 'Jose', 'Valeria', 'Miguel', 'Ximena', 'Ethan', 'Renata',
            'Adrian', 'Catalina', 'Ian', 'Fernanda', 'Leonardo', 'Antonella', 'Maximiliano',
            'Alessandra', 'Bruno', 'Lara', 'Lautaro', 'Julieta', 'Emiliano', 'Bianca',
            'Nicolas', 'Aitana', 'Facundo', 'Alma', 'Kevin', 'Guadalupe', 'Pablo',
        ];
        $apPaterno = [
            'Quispe', 'Vargas', 'Flores', 'Mamani', 'Garcia', 'Rodriguez', 'Martinez',
            'Lopez', 'Hernandez', 'Gonzales', 'Perez', 'Morales', 'Condori', 'Choque',
            'Cruz', 'Torrez', 'Castillo', 'Rojas', 'Gutierrez', 'Alvarez', 'Ortiz',
            'Ramos', 'Cardenas', 'Miranda', 'Salazar', 'Vega', 'Medina', 'Carrasco',
            'Molina', 'Chavez', 'Rivera', 'Aguilar', 'Paredes', 'Roca', 'Zuleta',
        ];
        $apMaterno = [
            'Quispe', 'Vargas', 'Flores', 'Mamani', 'Garcia', 'Rodriguez', 'Martinez',
            'Lopez', 'Hernandez', 'Gonzales', 'Perez', 'Morales', 'Condori', 'Choque',
            'Cruz', 'Jimenez', 'Castillo', 'Rojas', 'Villca', 'Alvarez', 'Ortiz',
            'Ramos', 'Cardenas', 'Miranda', 'Salazar', 'Romero', 'Medina', 'Carrasco',
            'Molina', 'Chavez', 'Rivera', 'Aguilar', 'Paredes', 'Torrico', 'Delgado',
        ];
        $generos = ['F', 'M'];

        $userStartId = DB::table('usuario')->max('id_user') ?? 0;
        $userId = $userStartId;
        $alumnoId = 0;
        $this->familyGroup = [];

        $alumnoUsers = [];
        $alumnos = [];

        foreach (self::CURSOS as $curso) {
            $i = 1;
            while ($i <= self::STUDENTS_PER_COURSE) {
                $groupSize = $this->randomGroupSize();
                $remaining = self::STUDENTS_PER_COURSE - $i + 1;
                if ($groupSize > $remaining) $groupSize = $remaining;

                $paterno = $apPaterno[array_rand($apPaterno)];
                $materno = $apMaterno[array_rand($apMaterno)];
                $groupKey = 'c' . $curso . '_g' . $i;

                for ($j = 0; $j < $groupSize; $j++) {
                    $userId++;
                    $alumnoId++;
                    $genero = $generos[array_rand($generos)];
                    $nombre = $nombres[array_rand($nombres)];
                    $anioNac = 2026 - $this->edadPorCurso($curso);

                    $alumnoUsers[] = [
                        'id_user' => $userId,
                        'username' => 'alumno_' . $alumnoId,
                        'password' => Hash::make('alumno123'),
                        'id_rol' => 3,
                    ];
                    $alumnos[] = [
                        'id_alumno' => $alumnoId,
                        'id_user' => $userId,
                        'ci' => (string) (10000000 + $alumnoId),
                        'nombres' => $nombre,
                        'ap_paterno' => $paterno,
                        'ap_materno' => $materno,
                        'genero' => $genero,
                        'fecha_nac' => sprintf('%d-%02d-%02d', $anioNac, rand(1, 12), rand(1, 28)),
                        'telefono' => '7' . str_pad((string) (8000000 + $alumnoId), 7, '0', STR_PAD_LEFT),
                        'id_beca' => null,
                    ];
                    $this->familyGroup[$alumnoId] = [
                        'key' => $groupKey,
                        'curso' => $curso,
                        'size' => $groupSize,
                        'paterno' => $paterno,
                        'materno' => $materno,
                    ];
                }
                $i += $groupSize;
            }
        }

        foreach (array_chunk($alumnoUsers, 100) as $chunk) {
            DB::table('usuario')->insert($chunk);
        }
        foreach (array_chunk($alumnos, 100) as $chunk) {
            DB::table('alumno')->insert($chunk);
        }
    }

    private function randomGroupSize(): int
    {
        $r = rand(1, 100);
        if ($r <= 60) return 1;
        if ($r <= 85) return 2;
        if ($r <= 95) return 3;
        if ($r <= 98) return 4;
        return 5;
    }

    private function seedApoderados(): void
    {
        $nombres = [
            'Maria', 'Juan', 'Ana', 'Carlos', 'Rosa', 'Pedro', 'Elena', 'Luis',
            'Carmen', 'Jorge', 'Patricia', 'Ricardo', 'Sandra', 'Fernando', 'Martha',
            'Alberto', 'Monica', 'Roberto', 'Teresa', 'Raul', 'Gloria', 'Manuel',
            'Silvia', 'Eduardo', 'Isabel', 'Alvaro', 'Diana', 'Oscar', 'Margarita', 'Hugo',
        ];
        $ocupaciones = ['Profesor', 'Medico', 'Abogado', 'Ingeniero', 'Comerciante', 'Chofer', 'Contador', 'Enfermero', 'Arquitecto', 'Ama de casa'];

        $userStartId = DB::table('usuario')->max('id_user') ?? 0;
        $userId = $userStartId;
        $apoderadoId = 0;

        $apoderados = [];
        $parentescos = [];
        $apoderadoUsers = [];
        $createdApoderados = [];

        foreach (self::CURSOS as $curso) {
            $startId = ($curso - 1) * self::STUDENTS_PER_COURSE + 1;
            $endId = $curso * self::STUDENTS_PER_COURSE;

            $processed = [];
            for ($alumnoId = $startId; $alumnoId <= $endId; $alumnoId++) {
                if (isset($processed[$alumnoId])) continue;

                $groupKey = $this->familyGroup[$alumnoId]['key'] ?? null;
                $groupSize = $this->familyGroup[$alumnoId]['size'] ?? 1;
                $paterno = $this->familyGroup[$alumnoId]['paterno'] ?? '';
                $materno = $this->familyGroup[$alumnoId]['materno'] ?? '';

                if ($groupKey && isset($createdApoderados[$groupKey])) {
                    $apoderadosGrupo = $createdApoderados[$groupKey];
                    for ($j = 0; $j < $groupSize; $j++) {
                        $sid = $startId + ($alumnoId - $startId) + $j;
                        foreach ($apoderadosGrupo as $apIdx => $apid) {
                            $parentescos[] = [
                                'id_alumno' => $sid,
                                'id_apoderado' => $apid,
                                'descripcion' => $apIdx === 0 ? 'Madre' : 'Padre',
                            ];
                        }
                        $processed[$sid] = true;
                    }
                    continue;
                }

                $userId++;
                $apoderadoId++;
                $nombreMadre = $nombres[array_rand($nombres)];
                $apoderadoUsers[] = [
                    'id_user' => $userId,
                    'username' => 'apoderado_' . $apoderadoId,
                    'password' => Hash::make('apoderado123'),
                    'id_rol' => 4,
                ];
                $apoderados[] = [
                    'id_apoderado' => $apoderadoId,
                    'id_user' => $userId,
                    'ci' => (string) (20000000 + $apoderadoId),
                    'nombres' => $nombreMadre,
                    'ap_paterno' => $paterno,
                    'ap_materno' => $materno,
                    'genero' => 'F',
                    'ocupacion' => $ocupaciones[array_rand($ocupaciones)],
                    'fecha_nac' => sprintf('%d-%02d-%02d', rand(1975, 1995), rand(1, 12), rand(1, 28)),
                    'telefono' => '7' . str_pad((string) (6000000 + $apoderadoId), 7, '0', STR_PAD_LEFT),
                ];

                $userId++;
                $apoderadoId++;
                $nombrePadre = $nombres[array_rand($nombres)];
                $apoderadoUsers[] = [
                    'id_user' => $userId,
                    'username' => 'apoderado_' . $apoderadoId,
                    'password' => Hash::make('apoderado123'),
                    'id_rol' => 4,
                ];
                $apoderados[] = [
                    'id_apoderado' => $apoderadoId,
                    'id_user' => $userId,
                    'ci' => (string) (20000000 + $apoderadoId),
                    'nombres' => $nombrePadre,
                    'ap_paterno' => $paterno,
                    'ap_materno' => $materno,
                    'genero' => 'M',
                    'ocupacion' => $ocupaciones[array_rand($ocupaciones)],
                    'fecha_nac' => sprintf('%d-%02d-%02d', rand(1970, 1995), rand(1, 12), rand(1, 28)),
                    'telefono' => '7' . str_pad((string) (6000000 + $apoderadoId), 7, '0', STR_PAD_LEFT),
                ];

                $madreId = $apoderadoId - 1;
                $padreId = $apoderadoId;

                for ($j = 0; $j < $groupSize; $j++) {
                    $sid = ($alumnoId - $startId) + $j + $startId;
                    $parentescos[] = [
                        'id_alumno' => $sid,
                        'id_apoderado' => $madreId,
                        'descripcion' => 'Madre',
                    ];
                    $parentescos[] = [
                        'id_alumno' => $sid,
                        'id_apoderado' => $padreId,
                        'descripcion' => 'Padre',
                    ];
                    $processed[$sid] = true;
                }

                if ($groupKey) {
                    $createdApoderados[$groupKey] = [$madreId, $padreId];
                }
            }
        }

        foreach (array_chunk($apoderadoUsers, 100) as $chunk) {
            DB::table('usuario')->insert($chunk);
        }
        foreach (array_chunk($apoderados, 100) as $chunk) {
            DB::table('apoderado')->insert($chunk);
        }
        foreach (array_chunk($parentescos, 100) as $chunk) {
            DB::table('parentesco')->insert($chunk);
        }
    }

    private function seedFichasMedicas(): void
    {
        $sangres = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $alergias = ['Ninguna', 'Polvo', 'Penicilina', 'Mariscos', 'Lactosa', 'Frutos secos', 'Polen', 'Aspergil', 'Ninguna', 'Ninguna', 'Ninguna'];

        $totalAlumnos = DB::table('alumno')->count();
        $fichas = [];
        for ($id = 1; $id <= $totalAlumnos; $id++) {
            $alumno = DB::table('alumno')->where('id_alumno', $id)->first();
            if (!$alumno) continue;
            $fichas[] = [
                'tipo_sangre' => $sangres[array_rand($sangres)],
                'alergias' => $alergias[array_rand($alergias)],
                'contacto_emergencia' => 'Emergencia ' . $id,
                'telf_emerg' => '7800' . str_pad((string) $id, 4, '0', STR_PAD_LEFT),
                'id_alumno' => $id,
            ];
        }
        foreach (array_chunk($fichas, 100) as $chunk) {
            DB::table('ficha_medica')->insert($chunk);
        }
    }

    private function seedMatriculas(): void
    {
        $totalAlumnos = DB::table('alumno')->count();
        $matriculas = [];
        $inscripciones = [];
        $inscCursoGestion = [];
        $secretarias = DB::table('secretaria')->pluck('id_secretaria')->toArray();

        $matriculaId = 0;

        foreach (self::CURSOS as $curso) {
            $startId = ($curso - 1) * self::STUDENTS_PER_COURSE + 1;
            $endId = $curso * self::STUDENTS_PER_COURSE;

            for ($alumnoId = $startId; $alumnoId <= $endId; $alumnoId++) {
                $alumno = DB::table('alumno')->where('id_alumno', $alumnoId)->first();
                if (!$alumno) continue;

                $matriculaId++;
                $secretariaId = $secretarias ? $secretarias[array_rand($secretarias)] : 1;

                $matriculas[] = [
                    'id_matricula' => $matriculaId,
                    'monto' => 460.00,
                    'monto_pagado' => null,
                    'fecha' => '2026-01-' . str_pad((string) rand(15, 31), 2, '0', STR_PAD_LEFT),
                    'fecha_pago' => null,
                    'estado' => 'Pendiente',
                    'estado_pago' => 'Pendiente',
                    'motivo_anulacion' => null,
                ];
                $inscripciones[] = [
                    'id_inscripcion' => $matriculaId,
                    'fecha' => '2026-01-' . str_pad((string) rand(15, 31), 2, '0', STR_PAD_LEFT),
                    'id_alumno' => $alumnoId,
                    'id_apoderado' => null,
                    'id_secretaria' => $secretariaId,
                    'id_matricula' => $matriculaId,
                ];
                $inscCursoGestion[] = [
                    'id_inscripcion' => $matriculaId,
                    'id_gestion' => self::GESTION,
                    'id_curso' => $curso,
                    'paralelo' => 'A',
                ];
            }
        }

        DB::table('matricula')->insert($matriculas);
        DB::table('inscripcion')->insert($inscripciones);
        DB::table('inscripcion_curso_gestion')->insert($inscCursoGestion);
    }

    private function seedNotas(): void
    {
        $descs = [
            'En proceso de fortalecimiento',
            'Aprobado con buen desempeño',
            'Destacado rendimiento',
            'En proceso de mejora continua',
        ];
        $materiaCurso = DB::table('materia_curso_gestion')
            ->where('id_gestion', self::GESTION)
            ->get();

        $notas = [];
        $totalNotas = 0;

        foreach (self::CURSOS as $curso) {
            $startId = ($curso - 1) * self::STUDENTS_PER_COURSE + 1;
            $endId = $curso * self::STUDENTS_PER_COURSE;

            foreach (self::MATERIAS as $materia) {
                foreach (self::TRIMESTRES as $trimestre) {
                    for ($alumnoId = $startId; $alumnoId <= $endId; $alumnoId++) {
                        $ser = rand(60, 100);
                        $saber = rand(60, 100);
                        $hacer = rand(60, 100);
                        $auto = rand(60, 100);
                        $prom = ($ser + $saber + $hacer + $auto) / 4;
                        $descIdx = $prom >= 80 ? ($prom >= 90 ? 2 : 1) : ($prom >= 70 ? 3 : 0);
                        $desc = (int) round($prom) >= 80 ? 'Aprobado con buen desempeño' : 'En proceso de fortalecimiento';

                        $notas[] = [
                            'id_alumno' => $alumnoId,
                            'id_materia' => $materia,
                            'id_gestion' => self::GESTION,
                            'id_curso' => $curso,
                            'id_trimestre' => $trimestre,
                            'ser' => $ser,
                            'saber' => $saber,
                            'hacer' => $hacer,
                            'autoevaluacion' => $auto,
                            'promediofinal' => round($prom, 2),
                            'descripcion' => $desc,
                        ];
                        $totalNotas++;

                        if ($totalNotas % 500 === 0) {
                            DB::table('nota')->insert($notas);
                            $notas = [];
                        }
                    }
                }
            }
        }

        if (!empty($notas)) {
            DB::table('nota')->insert($notas);
        }
    }

    private function seedBecas(): void
    {
        $becaExcelencia = (int) DB::table('beca')->where('nombre', 'Excelencia')->value('id_beca');
        $becaHermanos = (int) DB::table('beca')->where('nombre', 'Hermanos')->value('id_beca');

        if ($becaExcelencia) {
            DB::update("
                UPDATE alumno a
                INNER JOIN (
                    SELECT id_alumno, id_gestion, id_curso,
                           ROW_NUMBER() OVER (PARTITION BY id_gestion, id_curso ORDER BY promedio DESC, id_alumno ASC) AS rnk
                    FROM (
                        SELECT id_alumno, id_gestion, id_curso,
                               AVG(promediofinal) AS promedio
                        FROM nota
                        WHERE id_gestion = 2
                        GROUP BY id_gestion, id_curso, id_alumno
                    ) promedios
                ) r ON a.id_alumno = r.id_alumno AND r.rnk = 1
                SET a.id_beca = ?
            ", [$becaExcelencia]);
        }

        if ($becaHermanos) {
            DB::statement("
                UPDATE alumno a
                INNER JOIN (
                    SELECT p.id_alumno
                    FROM parentesco p
                    INNER JOIN alumno a1 ON a1.id_alumno = p.id_alumno
                    WHERE (
                        SELECT COUNT(DISTINCT p3.id_alumno)
                        FROM parentesco p3
                        INNER JOIN alumno a3 ON a3.id_alumno = p3.id_alumno
                        WHERE p3.id_apoderado = p.id_apoderado
                          AND a3.ap_paterno = a1.ap_paterno
                          AND a3.ap_materno = a1.ap_materno
                    ) >= 3
                    AND NOT EXISTS (
                        SELECT 1
                        FROM parentesco p4
                        INNER JOIN alumno a4 ON a4.id_alumno = p4.id_alumno
                        WHERE p4.id_apoderado = p.id_apoderado
                          AND a4.ap_paterno = a1.ap_paterno
                          AND a4.ap_materno = a1.ap_materno
                          AND (a4.fecha_nac > a1.fecha_nac
                               OR (a4.fecha_nac = a1.fecha_nac AND a4.id_alumno > a1.id_alumno))
                    )
                ) m ON a.id_alumno = m.id_alumno
                SET a.id_beca = {$becaHermanos}
                WHERE a.id_beca IS NULL
            ");
        }

        $excelenciaCount = DB::table('alumno')->where('id_beca', $becaExcelencia)->count();
        $hermanosCount = DB::table('alumno')->where('id_beca', $becaHermanos)->count();
        $this->command->info("  - Excelencia asignada a {$excelenciaCount} alumnos");
        $this->command->info("  - Hermanos asignada a {$hermanosCount} alumnos");
    }

    private function seedPagos(): void
    {
        $totalAlumnos = DB::table('alumno')->count();
        $pagos = [];
        $totalPagos = 0;
        $montoMensual = 224.00;
        $descuentoBase = 0;

        foreach (self::CURSOS as $curso) {
            $startId = ($curso - 1) * self::STUDENTS_PER_COURSE + 1;
            $endId = $curso * self::STUDENTS_PER_COURSE;

            for ($alumnoId = $startId; $alumnoId <= $endId; $alumnoId++) {
                $alumno = DB::table('alumno')->where('id_alumno', $alumnoId)->first();
                if (!$alumno) continue;

                $mesNum = 2;
                foreach (self::MESES as $mes) {
                    $diaPago = $mes === 'Febrero' ? rand(1, 10) : rand(1, 10);
                    $fechaPago = $mesNum >= 10 ? "2026-{$mesNum}-{$diaPago}" : "2026-0{$mesNum}-{$diaPago}";
                    $totalPagos++;

                    $pagos[] = [
                        'monto' => $montoMensual,
                        'fecha' => $fechaPago,
                        'fecha_pago' => rand(0, 3) > 0 ? $fechaPago : null,
                        'mes' => $mes,
                        'descuento' => $descuentoBase,
                        'estado' => rand(0, 3) > 0 ? 'Pagado' : 'Pendiente',
                        'motivo_anulacion' => null,
                        'id_gestion' => self::GESTION,
                        'id_curso' => $curso,
                        'id_alumno' => $alumnoId,
                        'id_beca' => null,
                    ];
                    $mesNum++;

                    if ($totalPagos % 500 === 0) {
                        DB::table('pago_mensual')->insert($pagos);
                        $pagos = [];
                    }
                }
            }
        }

        if (!empty($pagos)) {
            DB::table('pago_mensual')->insert($pagos);
        }
    }

    private function seedAsistencias(): void
    {
        $totalAlumnos = DB::table('alumno')->count();
        $diasHabiles = $this->getDiasHabiles(2026, 2, 11);
        $asistencias = [];
        $totalAsis = 0;

        foreach (self::CURSOS as $curso) {
            $startId = ($curso - 1) * self::STUDENTS_PER_COURSE + 1;
            $endId = $curso * self::STUDENTS_PER_COURSE;

            foreach ($diasHabiles as $fecha) {
                for ($alumnoId = $startId; $alumnoId <= $endId; $alumnoId++) {
                    $matricula = DB::table('matricula')
                        ->whereExists(function ($q) use ($alumnoId) {
                            $q->select(DB::raw(1))
                              ->from('inscripcion')
                              ->whereColumn('inscripcion.id_matricula', 'matricula.id_matricula')
                              ->where('inscripcion.id_alumno', $alumnoId);
                        })->first();

                    if (!$matricula) continue;

                    $asistencias[] = [
                        'fecha' => $fecha,
                        'estado' => rand(1, 10) > 2 ? 'P' : 'A',
                        'id_matricula' => $matricula->id_matricula,
                        'id_materia' => null,
                        'id_gestion' => null,
                        'id_curso' => null,
                    ];
                    $totalAsis++;

                    if ($totalAsis % 500 === 0) {
                        DB::table('asistencia')->insert($asistencias);
                        $asistencias = [];
                    }

                    if ($totalAsis > 50000) break 3;
                }
            }
        }

        if (!empty($asistencias)) {
            DB::table('asistencia')->insert($asistencias);
        }
    }

    private function getDiasHabiles(int $anio, int $mes, int $cantidad): array
    {
        $dias = [];
        $dia = 1;
        while (count($dias) < $cantidad) {
            $ts = strtotime("{$anio}-{$mes}-{$dia}");
            if ($ts === false) break;
            $numDia = (int) date('N', $ts);
            if ($numDia <= 5) {
                $dias[] = date('Y-m-d', $ts);
            }
            $dia++;
        }
        return $dias;
    }

    private function edadPorCurso(int $cursoId): int
    {
        return match ($cursoId) {
            1 => 4,   // Prekinder
            2 => 5,   // Kinder
            3 => 6,   // 1ro Primaria
            4 => 7,   // 2do Primaria
            5 => 8,   // 3ro Primaria
            6 => 9,   // 4to Primaria
            7 => 10,  // 5to Primaria
            8 => 11,  // 6to Primaria
            9 => 12,  // 1ro Secundaria
            10 => 13, // 2do Secundaria
            11 => 14, // 3ro Secundaria
            12 => 15, // 4to Secundaria
            13 => 16, // 5to Secundaria
            14 => 17, // 6to Secundaria
            default => 10,
        };
    }
}
