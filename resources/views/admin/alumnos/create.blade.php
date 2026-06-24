@extends('adminlte::page')
{{-- Diagrama de secuencia: esta vista es la pantalla que usa el Administrador para iniciar "Crear alumno". --}}

@section('title', 'Crear Alumno')
{{-- Titulo de la pagina dentro de AdminLTE. --}}

@section('content_header')
    {{-- Paso vista: encabezado que se muestra antes de capturar los datos. --}}
    <h1><b>Creacion de un Nuevo Alumno</b></h1>
    <hr>
@stop

@section('content')
    {{-- Paso vista: tarjeta principal del formulario. --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Complete los datos del alumno</h3>
        </div>
        <div class="card-body">
            {{-- Paso vista -> ruta: al presionar Guardar se envia POST a routes/web.php mediante admin.alumnos.store. --}}
            {{-- Luego la secuencia continua en AlumnoController@store -> AlumnoService -> modelos Alumno/User -> base de datos. --}}
            <form action="{{ route('admin.alumnos.store') }}" method="POST">
                {{-- Token CSRF: Laravel lo exige para aceptar formularios POST. --}}
                @csrf

                {{-- Datos personales: estos campos llegan al Request del controlador. --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CI</label><b> (*)</b>
                            {{-- old('ci') conserva el dato si la validacion del controlador falla. --}}
                            <input type="text" class="form-control" name="ci" value="{{ old('ci') }}" required>
                            {{-- Si el controlador detecta error en ci, Laravel muestra el mensaje aqui. --}}
                            @error('ci')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombres</label><b> (*)</b>
                            {{-- Campo enviado al backend como nombres. --}}
                            <input type="text" class="form-control" name="nombres" value="{{ old('nombres') }}" required>
                            @error('nombres')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Paterno</label><b> (*)</b>
                            {{-- Campo enviado al backend como ap_paterno. --}}
                            <input type="text" class="form-control" name="ap_paterno" value="{{ old('ap_paterno') }}" required>
                            @error('ap_paterno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apellido Materno</label><b> (*)</b>
                            {{-- Campo enviado al backend como ap_materno. --}}
                            <input type="text" class="form-control" name="ap_materno" value="{{ old('ap_materno') }}" required>
                            @error('ap_materno')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Genero</label><b> (*)</b>
                            {{-- Campo enviado al backend como genero; el controlador espera F o M. --}}
                            <select name="genero" class="form-control" required>
                                <option value="">Seleccione</option>
                                <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino</option>
                            </select>
                            @error('genero')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label><b> (*)</b>
                            {{-- Campo enviado como fecha_nac para guardarse en el modelo Alumno. --}}
                            <input type="date" class="form-control" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                            @error('fecha_nac')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Telefono</label><b> (*)</b>
                            {{-- Campo enviado al backend como telefono. --}}
                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}" required>
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
                                    <option value="{{ $beca->id_beca }}" {{ old('id_beca') == $beca->id_beca ? 'selected' : '' }}>
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
                {{-- Datos de acceso: AlumnoService crea un User relacionado al Alumno. --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario</label><b> (*)</b>
                            {{-- username se guarda en app/Models/User.php. --}}
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contraseña</label><b> (*)</b>
                            {{-- password se envia al controlador y el servicio lo guarda cifrado. --}}
                            <input type="password" class="form-control" name="password" required>
                            <small class="form-text text-muted">Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.</small>
                            @error('password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar Contraseña</label><b> (*)</b>
                            {{-- Laravel usa password_confirmation para validar que coincida con password. --}}
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <hr>
                {{-- Cancelar vuelve al listado sin ejecutar AlumnoController@store. --}}
                <a href="{{ route('admin.alumnos.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                {{-- Guardar dispara el formulario y continua la secuencia hacia Controller -> Service -> Models -> BD. --}}
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </form>
        </div>
    </div>
@stop
