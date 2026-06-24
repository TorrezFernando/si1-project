@extends('adminlte::page')

@section('title', 'Editar Materia')

@section('content_header')
    <h1><b>Editar Materia</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Modificar datos de la materia</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.materias.update', $materia->id_materia) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Materia <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $materia->nombre) }}" required>
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distintivo">Distintivo</label>
                                    <input type="text" name="distintivo" class="form-control" value="{{ old('distintivo', $materia->distintivo) }}">
                                    @error('distintivo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="carga_horaria">Carga horaria semanal <span class="text-danger">*</span></label>
                                    <input type="number" name="carga_horaria" class="form-control" value="{{ old('carga_horaria', $materia->carga_horaria) }}" min="1" required>
                                    @error('carga_horaria')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="id_campo">Campo de Saberes <span class="text-danger">*</span></label>
                                    <select name="id_campo" class="form-control" required>
                                        <option value="">Seleccione un campo de saberes...</option>
                                        @foreach($campos as $campo)
                                            <option value="{{ $campo->id_campo }}" {{ old('id_campo', $materia->id_campo) == $campo->id_campo ? 'selected' : '' }}>
                                                {{ $campo->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_campo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar Materia</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
