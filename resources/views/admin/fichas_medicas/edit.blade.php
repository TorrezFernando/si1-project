@extends('adminlte::page')

@section('title', 'Editar Ficha Medica')

@section('content_header')
    <h1><b>Editar ficha medica</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-notes-medical mr-1"></i>
                Datos actuales de la ficha medica
            </h3>
        </div>
        <form action="{{ route('admin.fichas-medicas.update', $ficha) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('admin.fichas_medicas.form')
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.fichas-medicas.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
@stop
