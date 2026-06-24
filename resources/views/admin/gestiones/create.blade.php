@extends('adminlte::page')

@section('content_header')
    <h1><b>Nuevo Anio Escolar</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title">Datos del año escolar</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.gestiones.store') }}" method="POST">
                @csrf
                @include('admin.gestiones.form')
                <a href="{{ route('admin.gestiones.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            </form>
        </div>
    </div>
@stop
