@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1>Mi Perfil</h1>
@stop

@section('content')
    @php
        $registro = $perfil['registro'];
        $nombreCompleto = $registro
            ? trim(($registro->nombre ?? $registro->nombres ?? '') . ' ' . ($registro->ap_paterno ?? '') . ' ' . ($registro->ap_materno ?? ''))
            : $usuario->username;
        $usernameBloqueado = ! $puedeEditarUsername;
    @endphp

    {{-- Profile header --}}
    <div class="welcome-header" style="margin-bottom: 1.5rem;">
        <span class="role-badge">{{ $usuario->rol_nombre }}</span>
        <div class="welcome-title">{{ $nombreCompleto }}</div>
        <div class="welcome-subtitle">
            <i class="fas fa-user mr-1"></i> {{ $usuario->username }}
        </div>
    </div>

    <div class="row">
        {{-- Left: Info cards --}}
        <div class="col-lg-4">
            <div class="card" style="margin-bottom: 1rem;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card mr-1" style="color: #3b82f6;"></i> Datos de la cuenta
                    </h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Usuario</small>
                        <div style="font-weight: 600; color: #1e293b;">{{ $usuario->username }}</div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Rol</small>
                        <div>
                            <span class="badge-chip">{{ $usuario->rol_nombre }}</span>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Tipo de perfil</small>
                        <div style="font-weight: 600; color: #1e293b;">{{ $perfil['titulo'] }}</div>
                    </div>
                    <div>
                        <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Nombre completo</small>
                        <div style="font-weight: 600; color: #1e293b;">{{ $nombreCompleto ?: 'Sin registro vinculado' }}</div>
                    </div>
                </div>
            </div>

            @if($registro)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lock mr-1" style="color: #64748b;"></i> Datos del sistema
                    </h3>
                </div>
                <div class="card-body">
                    @if(isset($registro->ci))
                        <div style="margin-bottom: 0.8rem;">
                            <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">CI</small>
                            <div style="font-weight: 600;">{{ $registro->ci }}</div>
                        </div>
                    @endif
                    @if(isset($registro->fecha_nac))
                        <div>
                            <small style="color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">Fecha de nacimiento</small>
                            <div style="font-weight: 600;">{{ $registro->fecha_nac }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Edit forms --}}
        <div class="col-lg-8">
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit mr-1" style="color: #2563eb;"></i> Editar datos
                    </h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre de usuario</label>
                                    <input type="text" name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username', $usuario->username) }}"
                                           @if($usernameBloqueado) disabled @endif
                                           style="border-radius: 8px;">
                                    @if($usernameBloqueado)
                                        <small class="form-text text-muted">Bloqueado por el tipo de perfil.</small>
                                    @endif
                                    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            @if(in_array($perfil['tipo'], ['profesor', 'secretaria'], true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Correo electronico</label>
                                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
                                               value="{{ old('correo', $registro->correo ?? '') }}" style="border-radius: 8px;">
                                        @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif

                            @if(in_array($perfil['tipo'], ['profesor', 'secretaria', 'apoderado'], true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telefono</label>
                                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                               value="{{ old('telefono', $registro->telefono ?? '') }}" style="border-radius: 8px;">
                                        @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif

                            @if(in_array($perfil['tipo'], ['profesor', 'secretaria'], true))
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Direccion</label>
                                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                               value="{{ old('direccion', $registro->direccion ?? '') }}" style="border-radius: 8px;">
                                        @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif

                            @if($perfil['tipo'] === 'apoderado')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ocupacion</label>
                                        <input type="text" name="ocupacion" class="form-control @error('ocupacion') is-invalid @enderror"
                                               value="{{ old('ocupacion', $registro->ocupacion ?? '') }}" style="border-radius: 8px;">
                                        @error('ocupacion') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-1" style="color: #f59e0b;"></i> Cambiar contraseña
                    </h3>
                </div>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Contraseña actual</label>
                                    <input type="password" name="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           required style="border-radius: 8px;">
                                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nueva contraseña</label>
                                    <input type="password" name="new_password"
                                           class="form-control @error('new_password') is-invalid @enderror"
                                           required style="border-radius: 8px;">
                                    @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirmar contraseña</label>
                                    <input type="password" name="new_password_confirmation"
                                           class="form-control" required style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.
                        </small>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #f59e0b, #d97706) !important;">
                            <i class="fas fa-key mr-1"></i> Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
