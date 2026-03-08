@extends('adminlte::page')

@section('content_header')
    <h1><b>Edición de un Turno</b></h1>
    <hr>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos del formulario </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/turnos/'.$turno->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nombre del turno</label><b> (*)</b>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ old('nombre', $turno->nombre) }}"
                                            name="nombre" placeholder="Escriba aqui..." required>
                                    </div>
                                    @error('nombre')
                                        <small style="color: red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                        </div>


                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('/admin/turnos') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i>
                                    cancelar</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                    Actualizar</button>
                            </div>
                        </div>
                </div>
            </div>
            </form>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    </div>
    </div>
@stop

@section('css')
    {{-- Aquí pondremos estilos más adelante si lo necesitas --}}
@stop

@section('js')
   
@stop
