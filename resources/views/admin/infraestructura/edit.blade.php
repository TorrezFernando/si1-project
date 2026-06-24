@extends('adminlte::page')

@section('title', 'Editar Infraestructura')

@section('content_header')
    <h1><b>Editar Infraestructura</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Modificar aula o ambiente</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.infraestructura.update', $aula->id_aula) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.infraestructura.form')

                        <a href="{{ route('admin.infraestructura.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Actualizar Infraestructura
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
