@extends('adminlte::page')

@section('content_header')
    <h1><b>Editar Anio Escolar</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title">CU22: Datos del anio escolar</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.gestiones.update', $gestion->id_gestion) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.gestiones.form')
                <a href="{{ route('admin.gestiones.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
            </form>
        </div>
    </div>
@stop
