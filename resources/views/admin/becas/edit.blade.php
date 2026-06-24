@extends('adminlte::page')

@section('title', 'Editar Beca')

@section('content_header')
    <h1><b>Editar Beca</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Modificar tipo de beca</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.becas.update', $beca->id_beca) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.becas.form')

                        <a href="{{ route('admin.becas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Actualizar Beca
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
