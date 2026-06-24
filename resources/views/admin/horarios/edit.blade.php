@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')
    <h1><b>Editar Horario</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Modificar d&iacute;a, hora o aula</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.horarios.update', [$horario->id_materia, $horario->id_gestion, $horario->id_curso, $horario->id_paralelo]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.horarios.form', ['modo' => 'editar'])

                        <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Actualizar Horario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
