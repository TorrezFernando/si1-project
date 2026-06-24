@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <h1><b>Registrar Pago</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU17: Registrar pago</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pagos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo" id="tipo" value="{{ old('tipo', $seleccionada->tipo ?? '') }}">
                <input type="hidden" name="id_referencia" id="id_referencia" value="{{ old('id_referencia', $seleccionada->id_referencia ?? '') }}">

                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Obligacion pendiente <span class="text-danger">*</span></label>
                            <select id="obligacion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($obligaciones as $obligacion)
                                    @php $value = $obligacion->tipo . '|' . $obligacion->id_referencia; @endphp
                                    <option
                                        value="{{ $value }}"
                                        data-tipo="{{ $obligacion->tipo }}"
                                        data-referencia="{{ $obligacion->id_referencia }}"
                                        data-monto="{{ number_format((float) $obligacion->monto_pendiente, 2, '.', '') }}"
                                        {{ ($seleccionada && $seleccionada->tipo === $obligacion->tipo && (int) $seleccionada->id_referencia === (int) $obligacion->id_referencia) ? 'selected' : '' }}>
                                        {{ ucfirst($obligacion->tipo) }} - {{ $obligacion->concepto }} - {{ $obligacion->alumno }} - {{ $obligacion->curso }} - {{ $obligacion->gestion }} - {{ number_format((float) $obligacion->monto_pendiente, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_referencia') <small class="text-danger">{{ $message }}</small> @enderror
                            @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Monto recibido <span class="text-danger">*</span></label>
                            <input type="number" name="monto_recibido" id="monto_recibido" step="0.01" min="0.01" class="form-control" value="{{ old('monto_recibido', $seleccionada ? number_format((float) $seleccionada->monto_pendiente, 2, '.', '') : '') }}" required>
                            @error('monto_recibido') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de pago <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_pago" class="form-control" value="{{ old('fecha_pago', now()->toDateString()) }}" required>
                            @error('fecha_pago') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Pago
                </button>
                <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    function actualizarSeleccion() {
        const option = document.getElementById('obligacion').selectedOptions[0];
        document.getElementById('tipo').value = option?.dataset.tipo || '';
        document.getElementById('id_referencia').value = option?.dataset.referencia || '';
        if (option?.dataset.monto) {
            document.getElementById('monto_recibido').value = option.dataset.monto;
        }
    }

    document.getElementById('obligacion').addEventListener('change', actualizarSeleccion);
    actualizarSeleccion();
</script>
@stop
