@extends('adminlte::page')

@section('title', 'Panel de Control')

@section('content_header')
    <h1>Datos del sistema </h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Bienvenido a la seccion de configuracion de sistema</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/configuracion/create') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">

                                <div class="form-group">
                                    <label for="">logo de la institucion</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                                        </div>
                                        <input type="file" name="logo" onchange="mostrarImagen(event)"
                                            accept="image/*">
                                        <br>
                                        <center>
                                            <img id="preview" src="{{ url($configuracion->logo ?? '') }}"
                                                style="max-width: 300px; margin-top: 10px;">
                                        </center>
                                        <script>
                                            const mostrarImagen = e =>
                                                document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
                                        </script>
                                    </div>
                                    @error('logo')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nombre</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                    value="{{ old('nombre', $configuracion->nombre ?? '') }}" name="nombre"
                                                    placeholder="Escriba un nombre">
                                            </div>
                                            @error('nombre')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Descripción</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                    value="{{ old('descripcion', $configuracion->descripcion ?? '') }}"
                                                    name="descripcion" placeholder="Escriba una descripción">
                                            </div>
                                            @error('descripcion')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Dirección</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-map-marker-alt"></i></span>
                                                </div>
                                                <input class="form-control"
                                                    value="{{ old('direccion', $configuracion->direccion ?? '') }}"
                                                    name="direccion" placeholder="Escriba aquí..." required>
                                            </div>
                                            @error('direccion')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Teléfono</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                    value="{{ old('telefono', $configuracion->telefono ?? '') }}"
                                                    name="telefono" placeholder="Escriba aquí..." required>
                                            </div>
                                            @error('telefono')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Divisa</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-money-bill-wave"></i></span>
                                                </div>

                                                <select name="divisa" class="form-control" required>
                                                    <option value="">Seleccione una divisa</option>
                                                    @foreach ($divisas as $divisa)
                                                        <option value="{{ $divisa['symbol'] }}"
                                                            {{ old('divisa', $configuracion->divisa ?? '') == $divisa['symbol'] ? 'selected' : '' }}>
                                                            {{ $divisa['name'] . ' (' . $divisa['symbol'] . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            @error('divisa')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">correo_electronico</label><b> (*)</b>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control"
                                                    value="{{ old('correo_electronico', $configuracion->correo_electronico ?? '') }}"
                                                    name="correo_electronico" placeholder="Escriba aquí...">
                                            </div>
                                            @error('correo_electronico')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Sitio Web</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                    value="{{ old('web', $configuracion->web ?? '') }}" name="web"
                                                    placeholder="Escriba aquí...">
                                            </div>
                                            @error('web')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('/admin') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i>
                                    cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                    Guardar</button>
                            </div>
                        </div>
                </div>
           
            </form>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    </div>  
@stop

@if (Session::has('mensaje'))
    <script>
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 4000
        });
    </script>
@endif
@section('css')
    {{-- Aquí pondremos estilos más adelante si lo necesitas --}}
@stop

@section('js')
    <script>
        console.log("AdminLTE cargado correctamente");
    </script>
@stop
