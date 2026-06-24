@extends('adminlte::page')

@section('title', 'Nueva Infraestructura')

@section('content_header')
    <h1><b>Nueva Infraestructura</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registrar aula o ambiente</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.infraestructura.store') }}" method="POST">
                        @csrf
                        @include('admin.infraestructura.form')

                        <a href="{{ route('admin.infraestructura.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Infraestructura
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
