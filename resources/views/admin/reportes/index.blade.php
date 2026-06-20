@extends('adminlte::page')

@section('title', 'Generar Reportes')

@section('content_header')
    <h1 class="text-dark font-weight-bold"><i class="fas fa-chart-line mr-2"></i>Modulo de Reportes</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow border-0 card-outline card-success card-premium">
                <div class="card-header bg-gradient-success text-white py-3">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-file-invoice mr-2"></i>Seleccione un Reporte
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="report-selector">
                        @if($rol === \App\Enums\Rol::ADMIN->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="admin_usuarios">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-users-cog mr-2 text-success"></i>Usuarios del Sistema</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Lista de todos los usuarios registrados y sus roles.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_bitacora">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-history mr-2 text-success"></i>Accesos y Sesiones</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Registro detallado de acciones y sesiones de usuarios.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::SECRETARIA->value || $rol === \App\Enums\Rol::ADMIN->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option @if($rol !== \App\Enums\Rol::ADMIN->value) active @endif" data-report="admin_estudiantes_curso">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-user-graduate mr-2 text-success"></i>Estudiantes por Curso</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de alumnos inscritos por curso.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_matriculas_gestion">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-id-card mr-2 text-success"></i>Matriculas por Gestion</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Reporte de matriculas anuales.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_pagos_estado">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-money-check-alt mr-2 text-success"></i>Pagos y Estado de Cuenta</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Detalle de mensualidades, matriculas y estados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_mora">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-exclamation-circle mr-2 text-success"></i>Reporte de Mora</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de obligaciones pendientes vencidas.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::PROFESOR->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="docente_notas">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-file-signature mr-2 text-success"></i>Notas por Curso y Materia</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Calificaciones de tus alumnos asignados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="docente_asistencia">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-calendar-check mr-2 text-success"></i>Asistencia por Curso y Materia</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Asistencia de estudiantes en tus cursos asignados.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="admin_estudiantes_curso">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-user-graduate mr-2 text-success"></i>Estudiantes por Curso</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Listado de alumnos de tus cursos asignados.</small>
                            </a>
                        @endif

                        @if($rol === \App\Enums\Rol::APODERADO->value)
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option active" data-report="tutor_calificaciones">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-award mr-2 text-success"></i>Calificaciones del Estudiante</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Boleta de calificaciones de tus hijos.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="tutor_asistencia">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-calendar-check mr-2 text-success"></i>Asistencia del Estudiante</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Registro de asistencia de tus hijos.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 border-bottom report-option" data-report="tutor_estado_cuenta">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 font-weight-bold"><i class="fas fa-receipt mr-2 text-success"></i>Estado de Cuenta</h6>
                                    <i class="fas fa-chevron-right text-muted icon-arrow"></i>
                                </div>
                                <small class="text-muted">Control de mensualidades y pagos de tus hijos.</small>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Pestañas de Selección de Modo -->
            <ul class="nav nav-pills mb-3 shadow-sm p-1 bg-white rounded-pill d-inline-flex" id="reportes-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active rounded-pill px-4 font-weight-bold" id="manual-tab" data-toggle="pill" href="#manual-pane" role="tab" aria-controls="manual-pane" aria-selected="true">
                        <i class="fas fa-filter mr-2"></i>Reporte Clásico
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link rounded-pill px-4 font-weight-bold" id="ia-tab" data-toggle="pill" href="#ia-pane" role="tab" aria-controls="ia-pane" aria-selected="false">
                        <i class="fas fa-robot mr-2 animate-pulse"></i>Asistente IA (Voz)
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="reportes-tabContent">
                <!-- PANEL CLÁSICO (FILTROS) -->
                <div class="tab-pane fade show active" id="manual-pane" role="tabpanel" aria-labelledby="manual-tab">
                    <div class="card shadow border-0 card-outline card-success card-premium">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title text-success font-weight-bold mb-0">
                                <i class="fas fa-filter mr-2"></i>Filtros y Busqueda
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="filter-form">
                                @csrf
                                <input type="hidden" name="tipo_reporte" id="tipo_reporte_input">

                                <div class="row">
                                    <div class="col-md-6 form-group filter-item" id="filter-gestion">
                                        <label class="font-weight-bold"><i class="fas fa-calendar-alt mr-1 text-secondary"></i>Gestion Academica</label>
                                        <select name="id_gestion" class="form-control">
                                            <option value="">Seleccione Gestion</option>
                                            @foreach($catalogos['gestiones'] as $g)
                                                <option value="{{ $g->id_gestion }}">{{ $g->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group filter-item" id="filter-curso">
                                        <label class="font-weight-bold"><i class="fas fa-graduation-cap mr-1 text-secondary"></i>Curso</label>
                                        <select name="id_curso" class="form-control">
                                            <option value="">Seleccione Curso</option>
                                            @foreach($catalogos['cursos'] as $c)
                                                <option value="{{ $c->id_curso }}">{{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group filter-item" id="filter-materia">
                                        <label class="font-weight-bold"><i class="fas fa-book mr-1 text-secondary"></i>Materia</label>
                                        <select name="id_materia" class="form-control">
                                            <option value="">Seleccione Materia</option>
                                            @foreach($catalogos['materias'] as $m)
                                                <option value="{{ $m->id_materia }}">{{ $m->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group filter-item" id="filter-alumno">
                                        <label class="font-weight-bold"><i class="fas fa-user-graduate mr-1 text-secondary"></i>Hijo / Estudiante</label>
                                        <select name="id_alumno" class="form-control">
                                            <option value="">Seleccione Estudiante</option>
                                            @foreach($catalogos['hijos'] as $h)
                                                <option value="{{ $h->id_alumno }}">{{ $h->nombre_completo }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group filter-item" id="filter-search">
                                        <label class="font-weight-bold"><i class="fas fa-search mr-1 text-secondary"></i>Buscar en Bitacora</label>
                                        <input type="text" name="search" class="form-control" placeholder="Usuario, accion o IP">
                                    </div>

                                    <div class="col-md-6 form-group filter-item" id="filter-fecha-inicio">
                                        <label class="font-weight-bold"><i class="fas fa-calendar-plus mr-1 text-secondary"></i>Fecha de Inicio</label>
                                        <input type="date" name="fecha_inicio" class="form-control">
                                    </div>
                                    <div class="col-md-6 form-group filter-item" id="filter-fecha-fin">
                                        <label class="font-weight-bold"><i class="fas fa-calendar-minus mr-1 text-secondary"></i>Fecha de Fin</label>
                                        <input type="date" name="fecha_fin" class="form-control">
                                    </div>
                                </div>

                                <div class="mt-3 text-right">
                                    <button type="submit" class="btn btn-gradient-success btn-lg px-5 shadow">
                                        <i class="fas fa-sync-alt mr-2 spinner-icon"></i>Generar Reporte
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow border-0 card-premium" id="results-card" style="display: none;">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title text-success font-weight-bold mb-0">
                                <i class="fas fa-table mr-2"></i>Resultados Generados
                            </h3>
                            <div class="card-tools d-flex flex-wrap">
                                <button type="button" class="btn btn-outline-danger btn-sm mr-2 mb-1 export-btn" data-format="print">
                                    <i class="fas fa-print mr-1"></i>Imprimir
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm mr-2 mb-1 export-btn" data-format="pdf">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm mb-1 export-btn" data-format="excel">
                                    <i class="fas fa-file-excel mr-1"></i>Excel
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0 table-responsive" style="max-height: 500px;">
                            <table class="table table-hover table-striped table-valign-middle mb-0" id="results-table">
                                <thead class="thead-dark text-uppercase small"></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL ASISTENTE IA (VOZ) -->
                <div class="tab-pane fade" id="ia-pane" role="tabpanel" aria-labelledby="ia-tab">
                    <div class="card shadow border-0 card-outline card-success card-premium">
                        <div class="card-header bg-gradient-success text-white py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold mb-0">
                                <i class="fas fa-robot mr-2"></i>Asistente de Voz IA
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="toggle-tts-btn" title="Activar/Desactivar lectura de voz">
                                    <i class="fas fa-volume-up mr-1"></i>Voz: <span id="tts-status-text">Activado</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body chat-panel-body" style="background-color: #f4f6f9 !important;">
                            <!-- Historial del Chat -->
                            <div class="chat-history-container mb-3 p-3 bg-white border rounded" id="chat-history" style="height: 380px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; border-radius: 12px !important;">
                                <div class="chat-bubble ia-msg d-flex flex-column p-3 rounded" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <span class="font-weight-bold text-success mb-1 small"><i class="fas fa-robot mr-1"></i>Asistente IA</span>
                                    <span class="chat-text">¡Hola! Soy tu asistente inteligente del Colegio Los Ángeles. ¿En qué reporte te puedo ayudar hoy? Puedes hablarme haciendo clic en el micrófono. Por ejemplo: <i>"Muestra los alumnos de 1ro de secundaria"</i> o <i>"¿Quiénes tienen mensualidades pendientes de pago?"</i></span>
                                </div>
                            </div>

                            <!-- Retroalimentación de Voz -->
                            <div id="voice-feedback" class="text-muted mb-2 px-2 small font-italic" style="display: none; transition: all 0.3s ease;">
                                <i class="fas fa-microphone text-danger animate-pulse mr-1"></i> <span class="feedback-status">Escuchando: </span>"<span class="transcription-preview"></span>"
                            </div>

                            <!-- Formulario de Entrada -->
                            <form id="chat-form" class="d-flex align-items-center">
                                <button type="button" class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center mr-2 position-relative" id="mic-btn" style="width: 50px; height: 50px; flex-shrink: 0; box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2); transition: all 0.3s;" title="Hablar">
                                    <i class="fas fa-microphone" style="font-size: 1.3rem;"></i>
                                    <div class="pulse-ring" style="display: none;"></div>
                                </button>
                                
                                <input type="text" id="chat-input" class="form-control rounded-pill py-3 px-4 mr-2" placeholder="Escribe tu consulta o haz clic en el micrófono..." style="height: 50px; border: 1px solid #ced4da; box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);">
                                
                                <button type="submit" class="btn btn-success rounded-circle d-flex align-items-center justify-content-center" id="send-btn" style="width: 50px; height: 50px; flex-shrink: 0; background: linear-gradient(135deg, #28a745 0%, #218838 100%); border: none; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);" title="Enviar mensaje">
                                    <i class="fas fa-paper-plane" style="font-size: 1.1rem; color: white;"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de Resultados IA -->
                    <div class="card shadow border-0 card-premium" id="ia-results-card" style="display: none;">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title text-success font-weight-bold mb-0">
                                <i class="fas fa-table mr-2"></i>Reporte Generado por IA
                            </h3>
                            <div class="card-tools d-flex flex-wrap">
                                <button type="button" class="btn btn-outline-danger btn-sm mr-2 mb-1 ia-export-btn" data-format="print">
                                    <i class="fas fa-print mr-1"></i>Imprimir
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm mr-2 mb-1 ia-export-btn" data-format="pdf">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm mb-1 ia-export-btn" data-format="excel">
                                    <i class="fas fa-file-excel mr-1"></i>Excel
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0 table-responsive" style="max-height: 500px;">
                            <table class="table table-hover table-striped table-valign-middle mb-0" id="ia-results-table">
                                <thead class="thead-dark text-uppercase small"></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <style>
        .card-premium {
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .report-option {
            transition: all 0.25s ease;
            border-left: 4px solid transparent;
        }
        .report-option.active {
            border-left-color: #28a745;
            background-color: #f4faf6 !important;
            color: #218838 !important;
        }
        .report-option:hover:not(.active) {
            background-color: #f8f9fa;
            border-left-color: #ddd;
            transform: translateX(4px);
        }
        .btn-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            border: none;
            color: white;
        }
        .btn-gradient-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            color: white;
        }
        .icon-arrow {
            transition: transform 0.2s ease;
        }
        .report-option.active .icon-arrow {
            transform: rotate(90deg);
            color: #28a745 !important;
        }
        .thead-dark th {
            background-color: #343a40 !important;
            border-color: #454d55 !important;
            color: #fff !important;
            white-space: nowrap;
        }
        #results-table td {
            white-space: nowrap;
        }

        /* Estilos del Asistente de Voz IA */
        .chat-history-container::-webkit-scrollbar {
            width: 6px;
        }
        .chat-history-container::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 3px;
        }
        .chat-bubble {
            box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.04);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-msg {
            background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
            align-self: flex-end;
            max-width: 80%;
            border-radius: 15px 15px 0px 15px !important;
        }
        .ia-msg {
            background-color: #ffffff;
            align-self: flex-start;
            max-width: 80%;
            border-radius: 15px 15px 15px 0px !important;
            border-left: 4px solid #28a745 !important;
        }
        .pulse-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border: 3px solid #dc3545;
            border-radius: 50%;
            animation: pulse-ring-anim 1.5s infinite;
        }
        @keyframes pulse-ring-anim {
            0% { transform: scale(0.95); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }
        .animate-pulse {
            animation: pulse-anim 2s infinite;
        }
        @keyframes pulse-anim {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        #mic-btn.recording {
            background-color: #28a745 !important;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.4);
            border-color: #28a745 !important;
        }
        #mic-btn.recording .pulse-ring {
            border-color: #28a745 !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const reportFilters = {
                admin_usuarios: [],
                admin_bitacora: ['search', 'fecha-inicio', 'fecha-fin'],
                admin_estudiantes_curso: ['gestion', 'curso'],
                admin_matriculas_gestion: ['gestion'],
                admin_pagos_estado: ['gestion', 'curso'],
                admin_mora: ['gestion', 'curso'],
                docente_notas: ['gestion', 'curso', 'materia'],
                docente_asistencia: ['gestion', 'curso', 'materia'],
                tutor_calificaciones: ['alumno'],
                tutor_asistencia: ['alumno'],
                tutor_estado_cuenta: ['alumno']
            };

            const labelsMap = {
                id_user: 'ID',
                id_bitacora: 'ID',
                id_alumno: 'ID Alumno',
                id_inscripcion: 'ID Inscripcion',
                username: 'Usuario',
                rol: 'Rol',
                fecha_hora: 'Fecha y Hora',
                accion: 'Accion',
                ip: 'IP',
                ci: 'CI',
                estudiante: 'Estudiante',
                genero: 'Genero',
                curso: 'Curso',
                paralelo: 'Paralelo',
                gestion: 'Gestion',
                fecha: 'Fecha',
                monto: 'Monto',
                estado: 'Estado',
                concepto: 'Concepto',
                descuento: 'Descuento',
                fecha_vencimiento: 'Vencimiento',
                estado_pago: 'Estado de Pago',
                materia: 'Materia',
                trimestre: 'Trimestre',
                ser: 'Ser',
                saber: 'Saber',
                hacer: 'Hacer',
                autoevaluacion: 'Autoevaluacion',
                promediofinal: 'Promedio Final'
            };

            function updateFilters(reportType) {
                $('.filter-item').hide();
                $('.filter-item :input').prop('required', false);

                const activeFilters = reportFilters[reportType] || [];
                activeFilters.forEach(filter => {
                    $(`#filter-${filter}`).show();
                    if (filter === 'alumno' || reportType === 'docente_notas' || reportType === 'docente_asistencia') {
                        $(`#filter-${filter} :input`).prop('required', true);
                    }
                });

                $('#tipo_reporte_input').val(reportType);
                $('#results-card').hide();
            }

            $('.report-option').click(function(e) {
                e.preventDefault();
                $('.report-option').removeClass('active');
                $(this).addClass('active');
                updateFilters($(this).data('report'));
            });

            const initialReport = $('.report-option.active').data('report');
            if (initialReport) {
                updateFilters(initialReport);
            }

            $('#filter-form').submit(function(e) {
                e.preventDefault();

                const reportType = $('#tipo_reporte_input').val();
                const spinner = $('.spinner-icon');
                spinner.addClass('fa-spin');

                $.ajax({
                    url: "{{ route('admin.reportes.generar') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        spinner.removeClass('fa-spin');

                        if (!response.success) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Reporte Vacio',
                                text: response.mensaje,
                                confirmButtonColor: '#28a745'
                            });
                            $('#results-card').hide();
                            return;
                        }

                        renderTable(response.datos);
                    },
                    error: function(xhr) {
                        spinner.removeClass('fa-spin');
                        const errors = xhr.responseJSON && xhr.responseJSON.errors;
                        const firstError = errors ? Object.values(errors)[0][0] : null;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: firstError || 'No se pudo generar el reporte. Intentelo de nuevo.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            function renderTable(data) {
                const thead = $('#results-table thead');
                const tbody = $('#results-table tbody');

                thead.empty();
                tbody.empty();

                if (!data.length) {
                    $('#results-card').hide();
                    return;
                }

                const fields = Object.keys(data[0]);
                const trHead = $('<tr>');
                fields.forEach(field => {
                    trHead.append($('<th>').text(labelsMap[field] || field.replaceAll('_', ' ').toUpperCase()));
                });
                thead.append(trHead);

                data.forEach(item => {
                    const trRow = $('<tr>');
                    fields.forEach(field => {
                        const val = item[field];
                        trRow.append($('<td>').text(val === null || val === undefined ? '-' : val));
                    });
                    tbody.append(trRow);
                });

                $('#results-card').fadeIn();
            }

            $('.export-btn').click(function() {
                const formato = $(this).data('format');
                const filters = $('#filter-form').serialize();
                const url = `{{ route('admin.reportes.exportar') }}?formato=${formato}&${filters}`;

                if (formato === 'print' || formato === 'pdf') {
                    window.open(url, '_blank');
                    return;
                }

                window.location.href = url;
            });

            // ==========================================
            // LÓGICA DE ASISTENTE DE IA POR VOZ / CHAT
            // ==========================================
            let ttsEnabled = true;
            let recognition = null;
            let isRecording = false;
            let currentIaSql = null;

            // Compatibilidad del SpeechRecognition
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                recognition = new SpeechRecognition();
                recognition.lang = 'es-ES';
                recognition.interimResults = true;
                recognition.continuous = false;

                recognition.onstart = function() {
                    isRecording = true;
                    $('#mic-btn').addClass('recording');
                    $('.pulse-ring').show();
                    $('#voice-feedback').fadeIn();
                    $('.feedback-status').text('Escuchando: ');
                    $('.transcription-preview').text('Habla ahora...');
                    window.speechSynthesis.cancel();
                };

                recognition.onresult = function(event) {
                    let interimTranscript = '';
                    let finalTranscript = '';

                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        if (event.results[i].isFinal) {
                            finalTranscript += event.results[i][0].transcript;
                        } else {
                            interimTranscript += event.results[i][0].transcript;
                        }
                    }

                    const preview = finalTranscript || interimTranscript;
                    $('.transcription-preview').text(preview);
                    $('#chat-input').val(preview);
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error', event.error);
                    stopRecording();
                    if (event.error === 'not-allowed') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Permiso Denegado',
                            text: 'No se pudo acceder al micrófono. Habilite los permisos en su navegador.',
                            confirmButtonColor: '#28a745'
                        });
                    }
                };

                recognition.onend = function() {
                    stopRecording();
                    const text = $('#chat-input').val().trim();
                    if (text.length > 3) {
                        $('#chat-form').submit();
                    }
                };
            } else {
                $('#mic-btn').prop('disabled', true).attr('title', 'El reconocimiento de voz no está soportado en este navegador');
            }

            function stopRecording() {
                isRecording = false;
                $('#mic-btn').removeClass('recording');
                $('.pulse-ring').hide();
                $('#voice-feedback').fadeOut();
                if (recognition) {
                    recognition.stop();
                }
            }

            $('#mic-btn').click(function() {
                if (!recognition) return;
                if (isRecording) {
                    stopRecording();
                } else {
                    $('#chat-input').val('');
                    recognition.start();
                }
            });

            // Parlante / TTS Toggle
            $('#toggle-tts-btn').click(function() {
                ttsEnabled = !ttsEnabled;
                if (ttsEnabled) {
                    $(this).removeClass('btn-outline-secondary').addClass('btn-outline-light');
                    $('#tts-status-text').text('Activado');
                    $(this).find('i').removeClass('fa-volume-mute').addClass('fa-volume-up');
                } else {
                    $(this).removeClass('btn-outline-light').addClass('btn-outline-secondary');
                    $('#tts-status-text').text('Silenciado');
                    $(this).find('i').removeClass('fa-volume-up').addClass('fa-volume-mute');
                    window.speechSynthesis.cancel();
                }
            });

            // Envío del Formulario Chat IA
            $('#chat-form').submit(function(e) {
                e.preventDefault();
                const mensaje = $('#chat-input').val().trim();
                if (!mensaje) return;

                // Bloquear entrada
                $('#chat-input').val('').prop('disabled', true);
                $('#send-btn').prop('disabled', true);
                stopRecording();

                // Agregar burbuja de usuario
                const userBubble = $(`
                    <div class="chat-bubble user-msg d-flex flex-column p-3 rounded" style="align-self: flex-end; max-width: 80%; border-radius: 15px 15px 0px 15px !important; margin-top: 5px;">
                        <span class="font-weight-bold text-primary mb-1 small"><i class="fas fa-user mr-1"></i>Tú</span>
                        <span class="chat-text">${escapeHtml(mensaje)}</span>
                    </div>
                `);
                $('#chat-history').append(userBubble);
                scrollToBottom();

                // Agregar burbuja pensando
                const loadingBubble = $(`
                    <div class="chat-bubble ia-msg d-flex flex-column p-3 rounded" id="ia-loading-bubble" style="align-self: flex-start; max-width: 80%; border-radius: 15px 15px 15px 0px !important;">
                        <span class="font-weight-bold text-success mb-1 small"><i class="fas fa-robot mr-1"></i>Asistente IA</span>
                        <span class="chat-text"><i class="fas fa-spinner fa-spin mr-1"></i>Pensando y consultando base de datos...</span>
                    </div>
                `);
                $('#chat-history').append(loadingBubble);
                scrollToBottom();

                $.ajax({
                    url: "{{ route('admin.chat_ia.preguntar') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mensaje: mensaje
                    },
                    success: function(response) {
                        $('#ia-loading-bubble').remove();

                        // Burbuja de IA
                        const iaBubble = $(`
                            <div class="chat-bubble ia-msg d-flex flex-column p-3 rounded" style="align-self: flex-start; max-width: 80%; border-radius: 15px 15px 15px 0px !important; margin-top: 5px;">
                                <span class="font-weight-bold text-success mb-1 small"><i class="fas fa-robot mr-1"></i>Asistente IA</span>
                                <span class="chat-text">${escapeHtml(response.explicacion)}</span>
                            </div>
                        `);
                        $('#chat-history').append(iaBubble);
                        scrollToBottom();

                        // Lectura de voz (TTS)
                        if (ttsEnabled && response.explicacion) {
                            window.speechSynthesis.cancel();
                            const utterance = new SpeechSynthesisUtterance(response.explicacion);
                            utterance.lang = 'es-ES';
                            window.speechSynthesis.speak(utterance);
                        }

                        $('#chat-input').prop('disabled', false).focus();
                        $('#send-btn').prop('disabled', false);

                        // Cargar datos en la tabla de la IA
                        if (response.datos && response.datos.length > 0) {
                            currentIaSql = response.sql;
                            renderIaTable(response.datos, response.cabeceras);
                        } else {
                            $('#ia-results-card').hide();
                            currentIaSql = null;
                        }
                    },
                    error: function(xhr) {
                        $('#ia-loading-bubble').remove();
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.explicacion 
                            ? xhr.responseJSON.explicacion 
                            : 'Lo siento, ocurrió un problema al procesar tu solicitud.';

                        const errorBubble = $(`
                            <div class="chat-bubble ia-msg d-flex flex-column p-3 rounded text-danger" style="align-self: flex-start; max-width: 80%; border-radius: 15px 15px 15px 0px !important; border-left: 4px solid #dc3545 !important; margin-top: 5px;">
                                <span class="font-weight-bold text-danger mb-1 small"><i class="fas fa-exclamation-triangle mr-1"></i>Error</span>
                                <span class="chat-text">${escapeHtml(errorMsg)}</span>
                            </div>
                        `);
                        $('#chat-history').append(errorBubble);
                        scrollToBottom();

                        if (ttsEnabled) {
                            window.speechSynthesis.cancel();
                            const utterance = new SpeechSynthesisUtterance("Ha ocurrido un error al procesar la solicitud.");
                            utterance.lang = 'es-ES';
                            window.speechSynthesis.speak(utterance);
                        }

                        $('#chat-input').prop('disabled', false).focus();
                        $('#send-btn').prop('disabled', false);
                        $('#ia-results-card').hide();
                        currentIaSql = null;
                    }
                });
            });

            function scrollToBottom() {
                const container = document.getElementById('chat-history');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

            function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function renderIaTable(data, headers) {
                const thead = $('#ia-results-table thead');
                const tbody = $('#ia-results-table tbody');

                thead.empty();
                tbody.empty();

                if (!data.length) {
                    $('#ia-results-card').hide();
                    return;
                }

                const fields = Object.keys(data[0]);
                const trHead = $('<tr>');
                
                const actualHeaders = (headers && headers.length === fields.length) 
                    ? headers 
                    : fields.map(f => labelsMap[f] || f.replaceAll('_', ' ').toUpperCase());

                actualHeaders.forEach(header => {
                    trHead.append($('<th>').text(header));
                });
                thead.append(trHead);

                data.forEach(item => {
                    const trRow = $('<tr>');
                    fields.forEach(field => {
                        const val = item[field];
                        trRow.append($('<td>').text(val === null || val === undefined ? '-' : val));
                    });
                    tbody.append(trRow);
                });

                $('#ia-results-card').fadeIn();
            }

            // Exportaciones IA
            $('.ia-export-btn').click(function() {
                if (!currentIaSql) return;
                const formato = $(this).data('format');
                const url = `{{ route('admin.chat_ia.exportar') }}?formato=${formato}`;

                if (formato === 'print' || formato === 'pdf') {
                    window.open(url, '_blank');
                    return;
                }

                window.location.href = url;
            });
        });
    </script>
@stop
