@extends('adminlte::page')
{{-- Diagrama de secuencia: esta vista es la pantalla que usa el Administrador para actualizar un alumno. --}}
{{-- Antes de mostrarse, admin.alumnos.edit llamo a AlumnoController@edit y cargo el modelo Alumno seleccionado. --}}

@section('title', 'Editar Alumno')
{{-- Titulo de la pagina dentro de AdminLTE. --}}

@section('content_header')
    {{-- Paso vista: encabezado del formulario de edicion. --}}
    <h1><b>Edicion de Alumno</b></h1>
    <hr>
@stop

@section('content')
    {{-- Paso vista: tarjeta que agrupa los datos actuales del alumno. --}}
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Actualice los datos del alumno</h3>
        </div>
        <div class="card-body">
            {{-- Paso vista -> ruta: al presionar Actualizar se envia a admin.alumnos.update con el id del alumno. --}}
            {{-- Luego la secuencia continua en AlumnoController@update -> AlumnoService -> Alumno/User -> base de datos. --}}
            <form action="{{ route('admin.alumnos.update', $alumno->id_alumno) }}" method="POST">
                {{-- Token CSRF requerido por Laravel. --}}
                @csrf
                {{-- Laravel usa este campo oculto para tratar el formulario como una peticion PUT. --}}
                @method('PUT')

                {{-- Datos personales: se llenan con old(...) o con los datos actuales del modelo $alumno. --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CI</label><b> (*)</b>
                            {{-- Si falla la validacion, old conserva lo escrito; si no, muestra $alumno->ci. --}}
                            <input type="text" class="form-control" name="ci" value="{{ old('ci', $alumno->ci) }}" required>
                            @error('ci')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombres</label><b> (*)</b>
                            {{-- Campo enviado al controlador como nombres. --}}
                            <input type="text" class="form-control" name="nombres" value="{{ old('nombres', $alumno->nombres) }}" required>
                            @error('nombres')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Paterno</label><b> (*)</b>
                            {{-- Campo enviado al controlador como ap_paterno. --}}
                            <input type="text" class="form-control" name="ap_paterno" value="{{ old('ap_paterno', $alumno->ap_paterno) }}" required>
                            @error('ap_paterno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Materno</label><b> (*)</b>
                            {{-- Campo enviado al controlador como ap_materno. --}}
                            <input type="text" class="form-control" name="ap_materno" value="{{ old('ap_materno', $alumno->ap_materno) }}" required>
                            @error('ap_materno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Genero</label><b> (*)</b>
                            {{-- Select enviado como genero; conserva el valor actual del alumno. --}}
                            <select name="genero" class="form-control" required>
                                <option value="F" {{ old('genero', $alumno->genero) === 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="M" {{ old('genero', $alumno->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                            </select>
                            @error('genero')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label><b> (*)</b>
                            {{-- Campo enviado como fecha_nac. --}}
                            <input type="date" class="form-control" name="fecha_nac" value="{{ old('fecha_nac', $alumno->fecha_nac) }}" required>
                            @error('fecha_nac')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telefono</label><b> (*)</b>
                            {{-- Campo enviado como telefono. --}}
                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono', $alumno->telefono) }}" required>
                            @error('telefono')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Beca asignada</label>
                            <select name="id_beca" class="form-control">
                                <option value="">Sin beca</option>
                                @foreach($becas as $beca)
                                    <option value="{{ $beca->id_beca }}" {{ old('id_beca', $alumno->id_beca) == $beca->id_beca ? 'selected' : '' }}>
                                        {{ $beca->nombre }} ({{ $beca->porcentaje }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('id_beca')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Datos de acceso</h5>
                {{-- Datos de acceso: vienen de la relacion $alumno->usuario y se actualizan junto al alumno. --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario</label><b> (*)</b>
                            {{-- optional evita error si el alumno todavia no tiene usuario relacionado. --}}
                            <input type="text" class="form-control" name="username" value="{{ old('username', optional($alumno->usuario)->username) }}" required>
                            @error('username')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nueva Contraseña</label>
                            {{-- Si se deja vacio, el servicio mantiene la contrasena actual. --}}
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Deje este campo vacio si no desea cambiar la contraseña.</small>
                            @error('password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar Nueva Contraseña</label>
                            {{-- Campo de confirmacion usado por la validacion del controlador. --}}
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Cancelar vuelve al listado sin ejecutar AlumnoController@update. --}}
                <a href="{{ route('admin.alumnos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                {{-- Actualizar envia los datos y continua la secuencia hacia Controller -> Service -> Models -> BD. --}}
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </form>
        </div>
    </div>
@stop
