@extends('adminlte::page')

@section('title', 'Editar Pago')

@section('content_header')
    <h1><b>Editar Pago</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">CU17: Editar pago registrado</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-light border">
                <strong>{{ ucfirst($pago->tipo) }} - {{ $pago->concepto }}</strong><br>
                {{ $pago->alumno }} | {{ $pago->curso }} | {{ $pago->gestion }}
            </div>

            <form action="{{ route('admin.pagos.update', [$pago->tipo, $pago->id_referencia]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Monto recibido <span class="text-danger">*</span></label>
                            <input type="number" name="monto_recibido" step="0.01" min="0.01" class="form-control" value="{{ old('monto_recibido', number_format((float) $pago->monto_pagado, 2, '.', '')) }}" required>
                            @error('monto_recibido') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de pago <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_pago" class="form-control" value="{{ old('fecha_pago', $pago->fecha_pago) }}" required>
                            @error('fecha_pago') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
@stop
