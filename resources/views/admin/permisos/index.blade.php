@extends('adminlte::page')

@section('title', 'Permisos por Rol')

@section('content_header')
    <h1>Permisos por Rol</h1>
@stop

@section('content')
    <div class="list-header" style="margin-bottom: 1.5rem;">
        <div class="list-info">
            <h4>{{ $modulos->sum(fn($m) => $m->funcionalidades->count()) }} funcionalidades en {{ $modulos->count() }} modulos</h4>
            <p>Asigna permisos a cada rol del sistema</p>
        </div>
        <div class="list-toolbar">
            <form action="{{ route('admin.permisos.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="id_rol" class="form-control" onchange="this.form.submit()" style="border-radius: 10px; width: auto; min-width: 180px;">
                    @foreach($roles as $item)
                        <option value="{{ $item->id_rol }}" {{ (int) $rol->id_rol === (int) $item->id_rol ? 'selected' : '' }}>
                            {{ $item->tipo }}
                        </option>
                    @endforeach
                </select>
            </form>
            <span style="font-size: 0.82rem; color: #64748b; font-weight: 500; background: #f1f5f9; padding: 0.35rem 0.8rem; border-radius: 8px;">
                Rol: <strong style="color: #1e293b;">{{ $rol->tipo }}</strong>
            </span>
        </div>
    </div>

    @if ((int) $rol->id_rol === \App\Enums\Rol::ADMIN->value)
        <div class="welcome-header" style="margin-bottom: 1.5rem; background: #fef3c7; border-color: #fde68a;">
            <div class="welcome-title" style="font-size: 1rem;">
                <i class="fas fa-shield-alt mr-1" style="color: #92400e;"></i>
                El Administrador tiene todos los permisos por defecto
            </div>
            <div class="welcome-subtitle" style="color: #92400e;">No necesita asignaciones manuales. Los checkboxes estan deshabilitados.</div>
        </div>
    @endif

    <form action="{{ route('admin.permisos.update', $rol) }}" method="POST">
        @csrf @method('PUT')

        <div class="row">
            @foreach($modulos as $modulo)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100" style="overflow: hidden;">
                        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700;">
                                <i class="fas fa-cube mr-1" style="color: #3b82f6;"></i>
                                {{ $modulo->nombre }}
                            </h3>
                            <span class="badge-chip" style="margin-left: auto;">
                                {{ $modulo->funcionalidades->count() }} func.
                            </span>
                        </div>
                        <div class="card-body" style="padding: 1rem;">
                            @forelse($modulo->funcionalidades as $funcionalidad)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="func_{{ $funcionalidad->id_funcionalidad }}"
                                           name="funcionalidades[]"
                                           value="{{ $funcionalidad->id_funcionalidad }}"
                                           {{ in_array($funcionalidad->id_funcionalidad, $permisosAsignados, true) ? 'checked' : '' }}
                                           {{ (int) $rol->id_rol === \App\Enums\Rol::ADMIN->value ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="func_{{ $funcionalidad->id_funcionalidad }}" style="cursor: pointer; width: 100%;">
                                        <strong style="font-size: 0.88rem;">{{ $funcionalidad->nombre }}</strong>
                                        @if($funcionalidad->descripcion)
                                            <br><small style="color: #94a3b8; font-size: 0.78rem;">{{ $funcionalidad->descripcion }}</small>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <span style="color: #94a3b8; font-size: 0.85rem;">Sin funcionalidades registradas.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body text-right">
                <button type="submit" class="btn btn-primary"
                        {{ (int) $rol->id_rol === \App\Enums\Rol::ADMIN->value ? 'disabled' : '' }}>
                    <i class="fas fa-save mr-1"></i> Guardar permisos
                </button>
            </div>
        </div>
    </form>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
