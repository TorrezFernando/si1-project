@extends('adminlte::page')

@section('title', 'Nuevo Horario')

@section('content_header')
    <h1><b>Nuevo Horario</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registrar asignaci&oacute;n de horario</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.horarios.store') }}" method="POST">
                        @csrf
                        @include('admin.horarios.form', ['modo' => 'crear'])

                        <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Horario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
