@extends('adminlte::page')

@section('title', 'Profesores')

@section('content_header')
    <h1>Profesores</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $totalProfesores }} {{ $totalProfesores == 1 ? 'profesor' : 'profesores' }}</h4>
            <p>Gestion de docentes y sus accesos al sistema</p>
        </div>
        <div class="list-toolbar">
            <span class="capacity-badge {{ $totalProfesores >= 20 ? 'full' : '' }}">
                <i class="fas {{ $totalProfesores >= 20 ? 'fa-exclamation-triangle' : 'fa-users' }}"></i>
                {{ $totalProfesores }}/20
            </span>
            <a href="{{ route('admin.profesores.create') }}"
               class="btn-add {{ $totalProfesores >= 20 ? 'disabled' : '' }}"
               @if($totalProfesores >= 20) style="opacity: 0.5; pointer-events: none;" @endif>
                <i class="fas fa-plus mr-1"></i> Añadir Profesor
            </a>
        </div>
    </div>

    @if($totalProfesores >= 20)
    <div class="alert alert-warning" style="border-radius: 10px; border-left: 4px solid #f59e0b;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        Capacidad maxima alcanzada (20 profesores). Elimina registros para añadir nuevos.
    </div>
    @endif

    <div class="card" style="overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Profesor</th>
                            <th>Contacto</th>
                            <th>Usuario</th>
                            <th style="width: 100px;">Horario</th>
                            <th style="width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profesores as $index => $profesor)
                        @php
                            $fullName = trim($profesor->nombre . ' ' . $profesor->ap_paterno . ' ' . ($profesor->ap_materno ?? ''));
                            $initials = strtoupper(substr($profesor->nombre, 0, 1) . substr($profesor->ap_paterno, 0, 1));
                            $horarioHabilitado = $profesor->permiso && $profesor->permiso->puede_ver_horario;
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $profesores->firstItem() + $index }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #7c3aed, #a78bfa);">{{ $initials }}</div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $fullName }}</div>
                                        <div class="user-detail">CI: {{ $profesor->ci }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem; color: #64748b;">
                                    @if($profesor->correo)
                                        <i class="fas fa-envelope mr-1" style="font-size: 0.7rem;"></i> {{ $profesor->correo }}<br>
                                    @endif
                                    @if($profesor->telefono)
                                        <i class="fas fa-phone-alt mr-1" style="font-size: 0.7rem;"></i> {{ $profesor->telefono }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ $profesor->usuario->username ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $horarioHabilitado ? 'on' : 'off' }}">
                                    <span class="status-dot"></span>
                                    {{ $horarioHabilitado ? 'Si' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.profesores.editInfo', $profesor->id_profesor) }}"
                                       class="btn-icon btn-icon-edit" title="Editar datos">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('admin.profesores.edit', $profesor->id_profesor) }}"
                                       class="btn-icon btn-icon-key" title="Editar acceso">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <button type="button"
                                            class="btn-icon btn-icon-delete"
                                            title="Eliminar"
                                            onclick="confirmarEliminar('{{ $fullName }}', {{ $profesor->id_profesor }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="form-eliminar-{{ $profesor->id_profesor }}"
                                          action="{{ route('admin.profesores.destroy', $profesor->id_profesor) }}"
                                          method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">👩‍🏫</div>
                                    <h5>Sin profesores</h5>
                                    <p>No hay docentes registrados en el sistema.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($profesores->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $profesores->firstItem() }}-{{ $profesores->lastItem() }} de {{ $profesores->total() }}
                </small>
                {{ $profesores->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        function confirmarEliminar(nombre, id) {
            Swal.fire({
                title: 'Eliminar profesor',
                text: '¿Desea eliminar a ' + nombre + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-eliminar-' + id).submit();
                }
            });
        }

        @if(session('mensaje'))
        Swal.fire({
            position: 'top-end',
            icon: '{{ session('icono', 'success') }}',
            title: '{{ session('mensaje') }}',
            showConfirmButton: false,
            timer: 3500
        });
        @endif
    </script>
@stop
