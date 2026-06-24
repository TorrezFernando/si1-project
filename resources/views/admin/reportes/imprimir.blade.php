<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            margin: 24px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #198754;
            padding-bottom: 14px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #146c43;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 7px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #111;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 36px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $nombresReportes = [
            'admin_usuarios' => 'Usuarios del Sistema',
            'admin_bitacora' => 'Accesos y Sesiones',
            'admin_estudiantes_curso' => 'Estudiantes por Curso',
            'admin_matriculas_gestion' => 'Matriculas por Gestion',
            'admin_pagos_estado' => 'Pagos y Estado de Cuenta',
            'admin_mora' => 'Reporte de Mora',
            'docente_notas' => 'Notas por Curso y Materia',
            'docente_asistencia' => 'Asistencia por Curso y Materia',
            'tutor_calificaciones' => 'Calificaciones del Estudiante',
            'tutor_asistencia' => 'Asistencia del Estudiante',
            'tutor_estado_cuenta' => 'Estado de Cuenta del Tutor',
        ];

        $labels = [
            'id_user' => 'ID',
            'id_bitacora' => 'ID',
            'id_alumno' => 'ID Alumno',
            'id_inscripcion' => 'ID Inscripcion',
            'username' => 'Usuario',
            'rol' => 'Rol',
            'fecha_hora' => 'Fecha y Hora',
            'accion' => 'Accion',
            'ip' => 'IP',
            'ci' => 'CI',
            'estudiante' => 'Estudiante',
            'genero' => 'Genero',
            'curso' => 'Curso',
            'paralelo' => 'Paralelo',
            'gestion' => 'Gestion',
            'fecha' => 'Fecha',
            'monto' => 'Monto',
            'estado' => 'Estado',
            'concepto' => 'Concepto',
            'descuento' => 'Descuento',
            'fecha_vencimiento' => 'Vencimiento',
            'estado_pago' => 'Estado de Pago',
            'materia' => 'Materia',
            'trimestre' => 'Trimestre',
            'ser' => 'Ser',
            'saber' => 'Saber',
            'hacer' => 'Hacer',
            'autoevaluacion' => 'Autoevaluacion',
            'promediofinal' => 'Promedio Final',
        ];

        $primerDato = $datos->first();
        $campos = $primerDato ? array_keys((array) $primerDato) : [];
    @endphp

    <div class="header">
        <h1>SISTEMA DE GESTION ESCOLAR</h1>
        <p>Reporte Oficial del Establecimiento Educativo</p>
    </div>

    <div class="meta-info">
        <div><strong>Tipo de Reporte:</strong> {{ $nombresReportes[$tipo] ?? 'Reporte General' }}</div>
        <div><strong>Fecha de Emision:</strong> {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($campos as $campo)
                    <th>{{ $labels[$campo] ?? strtoupper(str_replace('_', ' ', $campo)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $fila)
                <tr>
                    @foreach($campos as $campo)
                        <td>{{ ((array) $fila)[$campo] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte generado automaticamente por el Sistema de Gestion Escolar.
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
