@extends('adminlte::page')

@section('title', 'Reportes Estáticos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="text-dark font-weight-bold">
            <i class="fas fa-file-invoice mr-2 text-info"></i>Reportes Estáticos
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('home-panel') }}"><i class="fas fa-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes Estáticos</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Panel de Filtros -->
        <div class="col-md-12 mb-4">
            <div class="card shadow border-0 card-premium">
                <div class="card-header bg-gradient-info text-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-filter mr-2"></i>Criterios de Selección y Filtros
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reportes_estaticos.index') }}" class="needs-validation">
                        <div class="row">
                            <!-- Gestion Académica (Común para todos) -->
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-secondary">
                                    <i class="fas fa-calendar-alt mr-1"></i>Gestión Académica <span class="text-danger">*</span>
                                </label>
                                <select name="id_gestion" class="form-control select-premium" required>
                                    <option value="">Seleccione Gestión</option>
                                    @foreach($gestiones as $g)
                                        <option value="{{ $g->id_gestion }}" {{ (string)$idGestion === (string)$g->id_gestion ? 'selected' : '' }}>
                                            {{ $g->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($rol === \App\Enums\Rol::ADMIN->value || $rol === \App\Enums\Rol::SECRETARIA->value)
                                <!-- Curso (Admin/Secretaria) -->
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-graduation-cap mr-1"></i>Curso
                                    </label>
                                    <select name="id_curso" class="form-control select-premium">
                                        <option value="">Todos los Cursos</option>
                                        @foreach($cursos as $c)
                                            <option value="{{ $c->id_curso }}" {{ (string)$idCurso === (string)$c->id_curso ? 'selected' : '' }}>
                                                {{ $c->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Materia (Admin/Secretaria) -->
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-book mr-1"></i>Materia
                                    </label>
                                    <select name="id_materia" class="form-control select-premium">
                                        <option value="">Todas las Materias</option>
                                        @foreach($materias as $m)
                                            <option value="{{ $m->id_materia }}" {{ (string)$idMateria === (string)$m->id_materia ? 'selected' : '' }}>
                                                {{ $m->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Estudiante (Admin/Secretaria) -->
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-user mr-1"></i>Estudiante
                                    </label>
                                    <select name="id_alumno" class="form-control select-premium">
                                        <option value="">Todos los Estudiantes</option>
                                        @foreach($alumnos as $a)
                                            <option value="{{ $a->id_alumno }}" {{ (string)$idAlumno === (string)$a->id_alumno ? 'selected' : '' }}>
                                                {{ $a->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($rol === \App\Enums\Rol::PROFESOR->value)
                                <!-- Curso (Docente - Requerido) -->
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-graduation-cap mr-1"></i>Curso <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_curso" class="form-control select-premium" required>
                                        <option value="">Seleccione Curso</option>
                                        @foreach($cursos as $c)
                                            <option value="{{ $c->id_curso }}" {{ (string)$idCurso === (string)$c->id_curso ? 'selected' : '' }}>
                                                {{ $c->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Materia (Docente - Requerido) -->
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-book mr-1"></i>Materia <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_materia" class="form-control select-premium" required>
                                        <option value="">Seleccione Materia</option>
                                        @foreach($materias as $m)
                                            <option value="{{ $m->id_materia }}" {{ (string)$idMateria === (string)$m->id_materia ? 'selected' : '' }}>
                                                {{ $m->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($rol === \App\Enums\Rol::APODERADO->value)
                                <!-- Hijo (Apoderado - Requerido) -->
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-secondary">
                                        <i class="fas fa-user-graduate mr-1"></i>Hijo / Estudiante <span class="text-danger">*</span>
                                    </label>
                                    <select name="id_alumno" class="form-control select-premium" required>
                                        @if($hijos->count() > 1)
                                            <option value="">Seleccione Hijo</option>
                                        @endif
                                        @foreach($hijos as $h)
                                            <option value="{{ $h->id_alumno }}" {{ (string)$idAlumno === (string)$h->id_alumno ? 'selected' : '' }}>
                                                {{ $h->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-gradient-info btn-lg px-5 shadow font-weight-bold">
                                <i class="fas fa-search-plus mr-2"></i>Generar Reporte Estático
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Resultados -->
        <div class="col-md-12">
            @if($mostrarReporte)
                @if($rol === \App\Enums\Rol::APODERADO->value)
                    <!-- ============================================== -->
                    <!-- VISTA ESPECTACULAR: BOLETÍN DE NOTAS DE HIJO -->
                    <!-- ============================================== -->
                    @php
                        $alumnoObj = $hijos->firstWhere('id_alumno', $idAlumno);
                        $firstRow = $reporteData->first();
                    @endphp
                    
                    <div class="card shadow border-0 card-premium printable-section">
                        <div class="card-header bg-gradient-success text-white py-4 d-flex justify-content-between align-items-center flex-wrap no-print">
                            <div>
                                <h3 class="font-weight-bold mb-1"><i class="fas fa-award mr-2"></i>Boletín Oficial de Calificaciones</h3>
                                <p class="mb-0 text-white-50">Consulta académica integral de notas obtenidas por materia</p>
                            </div>
                            <div class="card-tools mt-3 mt-md-0">
                                <button type="button" class="btn btn-light font-weight-bold text-success shadow-sm" onclick="window.print();">
                                    <i class="fas fa-print mr-2"></i>Imprimir Boletín de Notas
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-light">
                            <!-- Encabezado de Impresión/Boletín -->
                            <div class="row border-bottom pb-4 mb-4 text-dark align-items-center">
                                <div class="col-md-3 text-center text-md-left mb-3 mb-md-0">
                                    <img src="{{ asset('img/Colegio.jpg') }}" alt="Colegio Logo" class="img-fluid rounded-circle shadow-sm" style="max-height: 100px;">
                                </div>
                                <div class="col-md-6 text-center">
                                    <h2 class="font-weight-bold text-uppercase mb-1" style="letter-spacing: 1px;">Colegio "Los Angeles"</h2>
                                    <h4 class="text-secondary font-weight-bold mb-0 text-uppercase small" style="letter-spacing: 2px;">Boletín de Notas del Estudiante</h4>
                                </div>
                                <div class="col-md-3 text-center text-md-right mt-3 mt-md-0">
                                    <div class="badge badge-info p-2 font-weight-bold shadow-sm" style="font-size: 0.95rem;">
                                        Gestión: {{ $firstRow->gestion ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Información General del Alumno -->
                            <div class="row bg-white rounded p-4 mb-4 shadow-sm border border-light">
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block small font-weight-bold">Estudiante:</span>
                                    <span class="h5 font-weight-bold text-dark">{{ $alumnoObj->nombre_completo ?? ($firstRow->estudiante ?? '-') }}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <span class="text-muted d-block small font-weight-bold">Cédula de Identidad (CI):</span>
                                    <span class="h6 font-weight-bold text-dark">{{ $firstRow->ci ?? '-' }}</span>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <span class="text-muted d-block small font-weight-bold">Curso:</span>
                                    <span class="h6 font-weight-bold text-dark">{{ $firstRow->curso ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- Tabla del Boletín -->
                            <div class="table-responsive bg-white rounded shadow-sm border border-light">
                                <table class="table table-bordered table-striped table-hover text-center mb-0 boletin-table align-middle">
                                    <thead class="thead-custom-dark text-white text-uppercase">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-left" style="width: 25%;">Materias / Áreas de Aprendizaje</th>
                                            <th colspan="5" class="align-middle bg-primary text-white py-2 small">Primer Trimestre</th>
                                            <th colspan="5" class="align-middle bg-indigo text-white py-2 small">Segundo Trimestre</th>
                                            <th colspan="5" class="align-middle bg-purple text-white py-2 small">Tercer Trimestre</th>
                                            <th rowspan="2" class="align-middle bg-success text-white" style="width: 10%;">Promedio Anual</th>
                                        </tr>
                                        <tr class="subheaders small">
                                            <!-- T1 -->
                                            <th class="py-1">SER</th>
                                            <th class="py-1">SABER</th>
                                            <th class="py-1">HACER</th>
                                            <th class="py-1">AUTO</th>
                                            <th class="py-1 bg-light font-weight-bold text-dark">PROM</th>
                                            <!-- T2 -->
                                            <th class="py-1">SER</th>
                                            <th class="py-1">SABER</th>
                                            <th class="py-1">HACER</th>
                                            <th class="py-1">AUTO</th>
                                            <th class="py-1 bg-light font-weight-bold text-dark">PROM</th>
                                            <!-- T3 -->
                                            <th class="py-1">SER</th>
                                            <th class="py-1">SABER</th>
                                            <th class="py-1">HACER</th>
                                            <th class="py-1">AUTO</th>
                                            <th class="py-1 bg-light font-weight-bold text-dark">PROM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($boletin as $materia)
                                            <tr>
                                                <!-- Nombre Materia -->
                                                <td class="text-left font-weight-bold text-dark pl-3">
                                                    {{ $materia['materia'] }}
                                                </td>
                                                
                                                <!-- Trimestre 1 -->
                                                <td>{{ $materia['trimestres'][1]['ser'] }}</td>
                                                <td>{{ $materia['trimestres'][1]['saber'] }}</td>
                                                <td>{{ $materia['trimestres'][1]['hacer'] }}</td>
                                                <td>{{ $materia['trimestres'][1]['autoevaluacion'] }}</td>
                                                <td class="bg-light-blue font-weight-bold">
                                                    @if($materia['trimestres'][1]['promedio'] !== '-')
                                                        <span class="{{ $materia['trimestres'][1]['promedio'] >= 51 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format((float)$materia['trimestres'][1]['promedio'], 0) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <!-- Trimestre 2 -->
                                                <td>{{ $materia['trimestres'][2]['ser'] }}</td>
                                                <td>{{ $materia['trimestres'][2]['saber'] }}</td>
                                                <td>{{ $materia['trimestres'][2]['hacer'] }}</td>
                                                <td>{{ $materia['trimestres'][2]['autoevaluacion'] }}</td>
                                                <td class="bg-light-blue font-weight-bold">
                                                    @if($materia['trimestres'][2]['promedio'] !== '-')
                                                        <span class="{{ $materia['trimestres'][2]['promedio'] >= 51 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format((float)$materia['trimestres'][2]['promedio'], 0) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <!-- Trimestre 3 -->
                                                <td>{{ $materia['trimestres'][3]['ser'] }}</td>
                                                <td>{{ $materia['trimestres'][3]['saber'] }}</td>
                                                <td>{{ $materia['trimestres'][3]['hacer'] }}</td>
                                                <td>{{ $materia['trimestres'][3]['autoevaluacion'] }}</td>
                                                <td class="bg-light-blue font-weight-bold">
                                                    @if($materia['trimestres'][3]['promedio'] !== '-')
                                                        <span class="{{ $materia['trimestres'][3]['promedio'] >= 51 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format((float)$materia['trimestres'][3]['promedio'], 0) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <!-- Promedio Anual -->
                                                <td class="bg-gradient-success-light font-weight-bold text-center" style="font-size: 1.05rem;">
                                                    @if($materia['promedio_anual'] !== '-')
                                                        <span class="badge {{ $materia['promedio_anual'] >= 51 ? 'badge-success' : 'badge-danger' }} px-3 py-2 shadow-sm font-weight-bold rounded-pill">
                                                            {{ number_format((float)$materia['promedio_anual'], 1) }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary rounded-pill px-3 py-2">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="17" class="text-center py-4 text-muted">
                                                    No se encontraron calificaciones cargadas en esta gestión.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Leyenda del boletín -->
                            <div class="row mt-4 pt-3 border-top text-muted small no-print">
                                <div class="col-md-6">
                                    <strong>Nomenclaturas:</strong> <span class="badge badge-light border">SER (Saber Ser)</span> <span class="badge badge-light border">SABER (Saber Conocer)</span> <span class="badge badge-light border">HACER (Saber Hacer)</span> <span class="badge badge-light border">AUTO (Autoevaluación)</span> <span class="badge badge-light border">PROM (Promedio Trimestral)</span>
                                </div>
                                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                    * La nota mínima de aprobación en cada período académico y anual es de <strong>51 puntos</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- ============================================== -->
                    <!-- VISTA TRADICIONAL PREMIUM: ADMIN/SECRETARIA/DOCENTE -->
                    <!-- ============================================== -->
                    <div class="card shadow border-0 card-premium">
                        <div class="card-header bg-gradient-success text-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h3 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-table mr-2"></i>Calificaciones Reportadas
                                </h3>
                            </div>
                            <div class="card-tools mt-2 mt-md-0">
                                <button type="button" class="btn btn-light btn-sm font-weight-bold shadow-sm" onclick="window.print();">
                                    <i class="fas fa-print mr-1"></i>Imprimir Vista
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            @if($reporteData->isEmpty())
                                <div class="p-5 text-center text-muted">
                                    <i class="fas fa-search fa-3x mb-3 text-secondary"></i>
                                    <h5>No se encontraron datos de calificaciones que coincidan con los filtros.</h5>
                                    <p class="mb-0 small">Pruebe seleccionando otros criterios en los selectores.</p>
                                </div>
                            @else
                                <table class="table table-hover table-striped table-valign-middle mb-0 align-middle text-center">
                                    <thead class="thead-dark text-uppercase small">
                                        <tr>
                                            <th class="text-left pl-4">Estudiante</th>
                                            <th>CI</th>
                                            <th>Curso</th>
                                            <th>Materia</th>
                                            <th>Trimestre</th>
                                            <th class="table-info-header">Ser</th>
                                            <th class="table-info-header">Saber</th>
                                            <th class="table-info-header">Hacer</th>
                                            <th class="table-info-header">Autoevaluación</th>
                                            <th class="table-success-header font-weight-bold">Promedio Final</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reporteData as $row)
                                            <tr>
                                                <td class="text-left font-weight-bold text-dark pl-4">{{ $row->estudiante }}</td>
                                                <td>{{ $row->ci }}</td>
                                                <td><span class="badge badge-secondary px-2 py-1 rounded">{{ $row->curso }}</span></td>
                                                <td>{{ $row->materia }}</td>
                                                <td><span class="badge badge-light border px-2 py-1 rounded">{{ $row->trimestre }}</span></td>
                                                <td>{{ $row->ser }}</td>
                                                <td>{{ $row->saber }}</td>
                                                <td>{{ $row->hacer }}</td>
                                                <td>{{ $row->autoevaluacion }}</td>
                                                <td class="font-weight-bold">
                                                    <span class="badge {{ $row->promediofinal >= 51 ? 'badge-success' : 'badge-danger' }} px-3 py-2 shadow-sm rounded">
                                                        {{ number_format((float)$row->promediofinal, 1) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <!-- Estado Inicial: Sin Búsqueda -->
                <div class="card shadow border-0 card-premium">
                    <div class="card-body p-5 text-center text-muted">
                        <div class="py-4">
                            <i class="fas fa-chart-bar fa-4x mb-4 text-info opacity-50 animate-bounce"></i>
                            <h4 class="font-weight-bold text-dark">Bienvenido al Módulo de Reporte Estático</h4>
                            <p class="lead max-width-600 mx-auto" style="font-size: 1.05rem;">
                                @if($rol === \App\Enums\Rol::APODERADO->value)
                                    Por favor seleccione la <strong>Gestión Académica</strong> para visualizar el boletín oficial de calificaciones de su hijo de manera organizada.
                                @elseif($rol === \App\Enums\Rol::PROFESOR->value)
                                    Por favor seleccione la <strong>Gestión Académica, Curso y Materia</strong> para generar la lista oficial estática de calificaciones de sus estudiantes asignados.
                                @else
                                    Seleccione al menos una <strong>Gestión Académica</strong> y aplique los filtros correspondientes para generar los registros estáticos de notas escolares.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-premium {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #fff;
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        .btn-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient-info:hover {
            background: linear-gradient(135deg, #117a8b 0%, #0c5662 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
        }
        .select-premium {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 0.4rem 0.8rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .select-premium:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.15);
        }
        
        /* Estilos Boletín */
        .thead-custom-dark th {
            background-color: #2c3e50 !important;
            color: #fff !important;
            border-color: #34495e !important;
        }
        .boletin-table th, .boletin-table td {
            vertical-align: middle !important;
            border: 1px solid #dee2e6 !important;
            padding: 8px 6px !important;
        }
        .subheaders th {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: bold;
            font-size: 0.75rem !important;
            letter-spacing: 0.5px;
        }
        .bg-light-blue {
            background-color: #ebf5fb !important;
        }
        .bg-gradient-success-light {
            background-color: #eafaf1 !important;
        }
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Estilos de Impresión */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print, 
            .main-header, 
            .main-sidebar, 
            .main-footer, 
            .content-header, 
            nav, 
            form, 
            .card-header.no-print,
            .btn {
                display: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
                background: white !important;
                padding: 0 !important;
            }
            .card-premium {
                box-shadow: none !important;
                border: none !important;
            }
            .printable-section {
                display: block !important;
                width: 100% !important;
            }
            .boletin-table {
                width: 100% !important;
                font-size: 11px !important;
            }
            .boletin-table th, .boletin-table td {
                padding: 4px 2px !important;
            }
        }
        
        .thead-dark th {
            background-color: #343a40 !important;
            border-color: #454d55 !important;
            color: #fff !important;
        }
        .table-info-header {
            background-color: #e2f0d9 !important;
            color: #385723 !important;
        }
        .table-success-header {
            background-color: #c6e0b4 !important;
            color: #375623 !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Animación elegante de carga al enviar filtros
            $('form').submit(function() {
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Generando Reporte...');
            });
        });
    </script>
@stop
