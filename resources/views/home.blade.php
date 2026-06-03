@extends('adminlte::page')

@section('title', 'Panel de Control')

@section('content_header')
    <h1>Panel principal</h1>
@stop

@section('content')
    @php
        $user = auth()->user();
        $rol = $user->id_rol;
        $rolNombre = match((int)$rol) {
            1 => 'Administrador',
            2 => 'Profesor',
            3 => 'Alumno',
            4 => 'Apoderado',
            5 => 'Director',
            6 => 'Secretaria',
            default => 'Usuario'
        };
        $nombre = $user->username ?? 'Usuario';
    @endphp

    {{-- Welcome header --}}
    <div class="welcome-header">
        <span class="role-badge">{{ $rolNombre }}</span>
        <div class="welcome-title">Bienvenido, {{ $nombre }}</div>
        <div class="welcome-subtitle">
            @switch($rol)
                @case(1) Panel de administracion general del sistema. @break
                @case(2) Gestion de tus horarios y asignaturas. @break
                @case(3) Consulta de tus calificaciones. @break
                @case(4) Consulta de notas de tus hijos. @break
                @case(5) Supervision academica y administrativa. @break
                @case(6) Gestion administrativa del colegio. @break
            @endswitch
        </div>
    </div>

    {{-- ===================== ADMIN / DIRECTOR / SECRETARIA ===================== --}}
    @if (in_array($rol, [1, 5, 6]))
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon si-blue">👨‍🎓</div>
                <div class="stat-number">{{ $stats['total_alumnos'] ?? 0 }}</div>
                <div class="stat-label">Alumnos registrados</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon si-green">👩‍🏫</div>
                <div class="stat-number">{{ $stats['total_profesores'] ?? 0 }}</div>
                <div class="stat-label">Profesores</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon si-purple">📚</div>
                <div class="stat-number">{{ $stats['total_cursos'] ?? 0 }}</div>
                <div class="stat-label">Cursos</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon si-orange">📖</div>
                <div class="stat-number">{{ $stats['total_materias'] ?? 0 }}</div>
                <div class="stat-label">Materias</div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="{{ route('apoderado.consulta') }}" class="quick-card">
                <div class="qc-icon qc-blue">📊</div>
                <h5>Consulta Academica</h5>
                <p>Consulta las notas de todos los alumnos registrados en el sistema.</p>
                <span class="qc-arrow">Consultar notas →</span>
            </a>
            <a href="{{ route('admin.alumnos.index') }}" class="quick-card">
                <div class="qc-icon qc-green">👨‍🎓</div>
                <h5>Gestion de Alumnos</h5>
                <p>Crea, modifica y administra los datos de los estudiantes.</p>
                <span class="qc-arrow">Ir a Alumnos →</span>
            </a>
            <a href="{{ route('admin.profesores.index') }}" class="quick-card">
                <div class="qc-icon qc-purple">👩‍🏫</div>
                <h5>Gestion de Profesores</h5>
                <p>Administra docentes, permisos y asignaciones de materias.</p>
                <span class="qc-arrow">Ir a Profesores →</span>
            </a>
            <a href="{{ route('admin.cursos.index') }}" class="quick-card">
                <div class="qc-icon qc-orange">📚</div>
                <h5>Gestion de Cursos</h5>
                <p>Crea y administra los cursos disponibles en la institucion.</p>
                <span class="qc-arrow">Ir a Cursos →</span>
            </a>
            <a href="{{ route('profesor.horario') }}" class="quick-card">
                <div class="qc-icon qc-sky">📅</div>
                <h5>Supervisar Horarios</h5>
                <p>Revisa los horarios de cualquier profesor del colegio.</p>
                <span class="qc-arrow">Ver horarios →</span>
            </a>
            @if ($rol == 1)
            <a href="{{ url('/admin/configuracion') }}" class="quick-card">
                <div class="qc-icon qc-blue">⚙️</div>
                <h5>Configuracion del Sistema</h5>
                <p>Ajusta los parametros generales y la informacion institucional.</p>
                <span class="qc-arrow">Configurar →</span>
            </a>
            @endif
        </div>
    @endif

    {{-- ===================== PROFESOR ===================== --}}
    @if ($rol == 2)
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon si-blue">📅</div>
                <div class="stat-number">{{ $stats['total_horarios'] ?? 0 }}</div>
                <div class="stat-label">Clases en tu horario</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon si-purple">📖</div>
                <div class="stat-number">{{ $stats['materias_count'] ?? 0 }}</div>
                <div class="stat-label">Materias asignadas</div>
            </div>
        </div>

        @if (isset($stats['profesor']) && $stats['profesor'])
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Perfil de profesor</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong>{{ $stats['profesor']->nombre }} {{ $stats['profesor']->ap_paterno }} {{ $stats['profesor']->ap_materno }}</strong>
                </p>
                @if (($stats['total_horarios'] ?? 0) > 0)
                    <p>Tu horario ya esta configurado. Puedes consultarlo desde el enlace de abajo o desde el menu lateral.</p>
                    <a href="{{ route('profesor.horario') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-week mr-1"></i> Ver mi horario
                    </a>
                @else
                    <p>Por ahora no tienes horarios asignados. Cuando el administrador te asigne clases, apareceran aqui.</p>
                @endif
            </div>
        </div>
        @else
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Perfil de profesor</h3>
            </div>
            <div class="card-body">
                <p>Tu usuario puede iniciar y cerrar sesion correctamente.</p>
                <p class="mb-0">Por ahora no tienes modulos habilitados por el administrador. Cuando te autoricen, aqui aparecera tu acceso al horario.</p>
            </div>
        </div>
        @endif
    @endif

    {{-- ===================== ALUMNO ===================== --}}
    @if ($rol == 3)
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon si-blue">📊</div>
                <div class="stat-number">{{ $stats['total_notas'] ?? 0 }}</div>
                <div class="stat-label">Calificaciones registradas</div>
            </div>
        </div>

        @if (isset($stats['alumno']) && $stats['alumno'])
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Perfil de alumno</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong>{{ $stats['alumno']->nombre }} {{ $stats['alumno']->ap_paterno }} {{ $stats['alumno']->ap_materno }}</strong>
                </p>
                <p>Solicita a tu apoderado que consulte tus calificaciones desde su cuenta.</p>
            </div>
        </div>
        @else
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Perfil de alumno</h3>
            </div>
            <div class="card-body">
                <p>Tu usuario esta activo pero aun no se ha vinculado un registro de alumno.</p>
            </div>
        </div>
        @endif
    @endif

    {{-- ===================== APODERADO ===================== --}}
    @if ($rol == 4)
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon si-blue">👪</div>
                <div class="stat-number">{{ $stats['total_hijos'] ?? 0 }}</div>
                <div class="stat-label">Hijos vinculados</div>
            </div>
        </div>

        @if (isset($stats['apoderado']) && $stats['apoderado'])
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Perfil de apoderado</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong>{{ trim($stats['apoderado']->nombres . ' ' . $stats['apoderado']->ap_paterno . ' ' . $stats['apoderado']->ap_materno) }}</strong>
                </p>
                <p>Desde aqui puedes consultar las notas registradas de tus hijos.</p>
                <a href="{{ route('apoderado.consulta') }}" class="btn btn-primary">
                    <i class="fas fa-file-alt mr-1"></i> Consultar notas
                </a>
            </div>
        </div>
        @else
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Perfil de apoderado</h3>
            </div>
            <div class="card-body">
                <p>Tu usuario esta activo pero aun no se ha vinculado un registro de apoderado.</p>
            </div>
        </div>
        @endif
    @endif

    {{-- ===================== CONFIGURACION (todos los roles) ===================== --}}
    @if ($configuracion)
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Informacion del Sistema</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card indigo" style="box-shadow: none; margin-bottom: 0;">
                        <div class="stat-icon si-indigo">🏫</div>
                        <div class="stat-number" style="font-size: 1.2rem;">{{ $configuracion->nombre }}</div>
                        <div class="stat-label">Institucion</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card blue" style="box-shadow: none; margin-bottom: 0;">
                        <div class="stat-icon si-blue">📝</div>
                        <div class="stat-number" style="font-size: 1.2rem;">{{ $configuracion->version ?? '1.0' }}</div>
                        <div class="stat-label">Version</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card green" style="box-shadow: none; margin-bottom: 0;">
                        <div class="stat-icon si-green">📅</div>
                        <div class="stat-number" style="font-size: 1rem;">{{ $configuracion->created_at ? $configuracion->created_at->format('d/m/Y') : '—' }}</div>
                        <div class="stat-label">Fecha de creacion</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Configuracion del sistema</h3>
        </div>
        <div class="card-body">
            <p>Aun no has configurado los datos del sistema.</p>
            @if ($rol == 1)
                <a href="{{ url('/admin/configuracion') }}" class="btn btn-primary">Ir a Configuracion</a>
            @endif
        </div>
    </div>
    @endif
@stop

@section('css')
@stop

@section('js')
    <script>
        console.log('Panel de control — Colegio Los Angeles');
    </script>
@stop
