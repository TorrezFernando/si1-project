@extends('adminlte::page')

@section('title', 'Nueva Beca')

@section('content_header')
    <h1><b>Nueva Beca</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Registrar tipo de beca</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.becas.store') }}" method="POST">
                        @csrf
                        @include('admin.becas.form')

                        <a href="{{ route('admin.becas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Beca
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
