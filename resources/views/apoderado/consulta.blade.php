@extends('adminlte::page')

@section('title', 'Consulta de Notas')

@section('content_header')
    <h1>{{ $esAdmin ? 'Consulta general de notas' : 'Consulta de notas' }}</h1>
@stop

@section('content')
    {{-- Sin apoderado --}}
    @if (! $esAdmin && ! $apoderado)
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">🔒</div>
                    <h5>Sin acceso</h5>
                    <p>No se encontro un registro de apoderado vinculado a tu usuario.</p>
                </div>
            </div>
        </div>

    {{-- Sin hijos --}}
    @elseif($hijos->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">👪</div>
                    <h5>Sin estudiantes</h5>
                    <p>{{ $esAdmin ? 'No hay alumnos registrados.' : 'No tienes hijos vinculados a tu cuenta.' }}</p>
                </div>
            </div>
        </div>

    @else
        {{-- Info header --}}
        <div class="welcome-header" style="margin-bottom: 1.2rem;">
            @if ($esAdmin)
                <span class="role-badge" style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">Administrador</span>
            @else
                <span class="role-badge">Apoderado</span>
            @endif
            <div class="welcome-title">
                {{ $esAdmin ? 'Consulta general de calificaciones' : trim($apoderado->nombres . ' ' . $apoderado->ap_paterno) }}
            </div>
            <div class="welcome-subtitle">
                {{ $alumnoSeleccionado ? 'Calificaciones del alumno seleccionado.' : 'Selecciona un alumno para ver sus notas.' }}
            </div>
        </div>

        {{-- Student switcher --}}
        @if($esAdmin || $hijos->count() > 1)
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                        <i class="fas fa-users mr-1" style="color: #3b82f6;"></i>
                        {{ $hijos->count() }} {{ $hijos->count() == 1 ? 'alumno disponible' : 'alumnos disponibles' }}
                    </span>
                    <button type="button" class="btn btn-sm"
                            style="border: 1.5px solid #dbeafe; border-radius: 10px; color: #2563eb; font-weight: 600;"
                            onclick="document.getElementById('studentPicker').classList.toggle('d-none')">
                        <i class="fas fa-exchange-alt mr-1"></i> Cambiar alumno
                    </button>
                </div>
            </div>

            {{-- Collapsible student grid --}}
            <div id="studentPicker" class="d-none">
                <div class="card-body" style="border-bottom: 1px solid #e2e8f0;">
                    <input type="text" id="studentSearch" class="form-control"
                           placeholder="Buscar por nombre o apellido..."
                           style="border-radius: 10px; margin-bottom: 0.8rem;"
                           oninput="filterStudents(this.value)">
                    <div id="studentList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.5rem; max-height: 300px; overflow-y: auto;">
                        @foreach($hijos as $hijo)
                            @php
                                $isActive = (string) $hijoSeleccionado === (string) $hijo->id_alumno;
                                $extra = $esAdmin ? ($hijo->apoderados_detalle ?? $hijo->apoderados ?? '') : ($hijo->parentesco ?? '');
                                $initials = strtoupper(substr($hijo->nombre_completo, 0, 1));
                            @endphp
                            <a href="{{ route('apoderado.consulta', ['hijo' => $hijo->id_alumno]) }}"
                               class="text-decoration-none student-item"
                               data-search="{{ strtolower($hijo->nombre_completo . ' ' . $extra) }}">
                                <div style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 0.8rem; border-radius: 10px; border: 1.5px solid {{ $isActive ? '#3b82f6' : '#e2e8f0' }}; background: {{ $isActive ? '#eff6ff' : '#ffffff' }}; transition: all 0.2s; cursor: pointer;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $isActive ? 'linear-gradient(135deg, #2563eb, #3b82f6)' : '#e2e8f0' }}; color: {{ $isActive ? '#fff' : '#94a3b8' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $hijo->nombre_completo }}
                                        </div>
                                        @if($extra)
                                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $extra }}</div>
                                        @endif
                                    </div>
                                    @if($isActive)
                                        <i class="fas fa-check-circle" style="color: #3b82f6; margin-left: auto;"></i>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Current student chip --}}
            @if($alumnoSeleccionado)
            <div class="card-body" style="padding: 0.7rem 1rem;">
                <span style="font-size: 0.82rem; color: #64748b; margin-right: 0.5rem;">Seleccionado:</span>
                <span class="badge-chip" style="font-size: 0.85rem; padding: 0.3rem 0.8rem; background: #eff6ff; color: #1e40af; font-weight: 600;">
                    <i class="fas fa-user-graduate mr-1"></i>
                    {{ $alumnoSeleccionado->nombre_completo }}
                </span>
            </div>
            @endif
        </div>
        @endif

        {{-- Selected student grades --}}
        @if($hijoSeleccionado && $alumnoSeleccionado)
            @php
                $notasAlumno = $notasPorHijo->get($hijoSeleccionado, collect());
                $notasAgrupadas = $notasAlumno->groupBy(function ($nota) {
                    return $nota->gestion . '|' . $nota->curso . '|' . $nota->materia;
                });
            @endphp

            {{-- Stats --}}
            @if($stats)
            <div class="stats-grid" style="margin-bottom: 1.2rem;">
                <div class="stat-card blue">
                    <div class="stat-icon si-blue">⭐</div>
                    <div class="stat-number">{{ $stats->promedio }}</div>
                    <div class="stat-label">Promedio general</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon si-green">📈</div>
                    <div class="stat-number">{{ $stats->mejor }}</div>
                    <div class="stat-label">Mejor nota</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon si-purple">📖</div>
                    <div class="stat-number">{{ $stats->materias }}</div>
                    <div class="stat-label">Materias</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon si-orange">📋</div>
                    <div class="stat-number">{{ $stats->total }}</div>
                    <div class="stat-label">Evaluaciones</div>
                </div>
            </div>
            @endif

            {{-- Grades card --}}
            <div class="card" style="overflow: hidden;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-1" style="color: #2563eb;"></i>
                        Calificaciones de {{ $alumnoSeleccionado->nombre_completo }}
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($notasAlumno->isEmpty())
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <div class="empty-icon">📝</div>
                            <h5>Sin calificaciones</h5>
                            <p>No hay notas registradas para este alumno.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="data-table grades-summary-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 220px;">Materia</th>
                                        <th class="text-center" style="min-width: 220px;">1er Trimestre</th>
                                        <th class="text-center" style="min-width: 220px;">2do Trimestre</th>
                                        <th class="text-center" style="min-width: 220px;">3er Trimestre</th>
                                        <th class="text-center" style="width: 120px;">Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notasAgrupadas as $grupo)
                                        @php
                                            $primeraNota = $grupo->first();
                                            $trimestres = $grupo->keyBy(function ($nota) {
                                                return (int) preg_replace('/\D+/', '', $nota->trimestre);
                                            });
                                            $promedioAnual = $grupo->avg('promediofinal');
                                            $promedioColor = $promedioAnual >= 60 ? '#166534' : ($promedioAnual >= 40 ? '#92400e' : '#991b1b');
                                            $promedioBg = $promedioAnual >= 60 ? '#dcfce7' : ($promedioAnual >= 40 ? '#fef3c7' : '#fef2f2');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="grade-subject">
                                                    <div class="grade-subject-name">{{ $primeraNota->materia }}</div>
                                                    <div class="grade-subject-meta">
                                                        <span><i class="fas fa-layer-group"></i> {{ $primeraNota->curso }}</span>
                                                        <span><i class="fas fa-calendar-alt"></i> {{ $primeraNota->gestion }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            @for($trimestre = 1; $trimestre <= 3; $trimestre++)
                                                @php
                                                    $notaTrimestre = $trimestres->get($trimestre);
                                                @endphp
                                                <td>
                                                    @if($notaTrimestre)
                                                        @php
                                                            $p = (float) $notaTrimestre->promediofinal;
                                                            $color = $p >= 60 ? '#166534' : ($p >= 40 ? '#92400e' : '#991b1b');
                                                            $bg = $p >= 60 ? '#dcfce7' : ($p >= 40 ? '#fef3c7' : '#fef2f2');
                                                        @endphp
                                                        <div class="trimester-grade">
                                                            <div class="trimester-score-grid">
                                                                <span><small>Ser</small>{{ $notaTrimestre->ser }}</span>
                                                                <span><small>Saber</small>{{ $notaTrimestre->saber }}</span>
                                                                <span><small>Hacer</small>{{ $notaTrimestre->hacer }}</span>
                                                                <span><small>Auto</small>{{ $notaTrimestre->autoevaluacion }}</span>
                                                            </div>
                                                            <div class="trimester-bottom">
                                                                <span class="trimester-average" style="background: {{ $bg }}; color: {{ $color }};">
                                                                    {{ number_format($p, 2) }}
                                                                </span>
                                                                <span class="trimester-observation" title="{{ $notaTrimestre->descripcion ?: 'Sin observacion' }}">
                                                                    {{ $notaTrimestre->descripcion ?: 'Sin observacion' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="trimester-grade empty">
                                                            <span>Sin nota</span>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endfor

                                            <td class="text-center">
                                                <span class="annual-average" style="background: {{ $promedioBg }}; color: {{ $promedioColor }};">
                                                    {{ number_format($promedioAnual, 2) }}
                                                </span>
                                                <div class="annual-count">
                                                    {{ $grupo->count() }}/3 trim.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                            <small class="text-muted">
                                {{ $notasAlumno->count() }} {{ $notasAlumno->count() == 1 ? 'calificacion' : 'calificaciones' }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    @endif
@stop

@section('js')
<script>
    function filterStudents(query) {
        var items = document.querySelectorAll('.student-item');
        var q = query.toLowerCase().trim();
        items.forEach(function(item) {
            var search = item.getAttribute('data-search') || '';
            item.style.display = !q || search.indexOf(q) !== -1 ? '' : 'none';
        });
    }

    // Auto-expand picker if no student is selected (shouldn't happen with default, but safety)
    @if(!$hijoSeleccionado)
        document.addEventListener('DOMContentLoaded', function() {
            var picker = document.getElementById('studentPicker');
            if (picker) picker.classList.remove('d-none');
        });
    @endif
</script>
@stop

@section('css')
<style>
    .grades-summary-table thead th {
        vertical-align: middle;
    }

    .grades-summary-table tbody td {
        vertical-align: top;
    }

    .grade-subject {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        min-width: 0;
    }

    .grade-subject-name {
        color: #1e293b;
        font-weight: 750;
        line-height: 1.25;
    }

    .grade-subject-meta {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .grade-subject-meta i {
        width: 14px;
        color: #3b82f6;
        margin-right: 0.25rem;
    }

    .trimester-grade {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.65rem;
        min-height: 112px;
    }

    .trimester-grade.empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 0.82rem;
        font-weight: 650;
        border-style: dashed;
    }

    .trimester-score-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.3rem;
    }

    .trimester-score-grid span {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border-radius: 7px;
        background: #f8fafc;
        color: #1e293b;
        font-size: 0.86rem;
        font-weight: 750;
    }

    .trimester-score-grid small {
        color: #64748b;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .trimester-bottom {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-top: 0.55rem;
        min-width: 0;
    }

    .trimester-average {
        border-radius: 999px;
        padding: 0.22rem 0.55rem;
        font-size: 0.78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .trimester-observation {
        color: #64748b;
        font-size: 0.76rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .annual-average {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 64px;
        min-height: 34px;
        border-radius: 999px;
        padding: 0.35rem 0.7rem;
        font-weight: 850;
    }

    .annual-count {
        margin-top: 0.35rem;
        color: #94a3b8;
        font-size: 0.72rem;
        font-weight: 700;
    }

    @media (max-width: 991px) {
        .grades-summary-table {
            min-width: 980px;
        }
    }
</style>
@stop
