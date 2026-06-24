@extends('adminlte::page')

@section('title', 'Generar Mensualidades')

@section('content_header')
    <h1><b>Generar Mensualidades</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU18: Generar obligaciones mensuales</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mensualidades.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Matricula activa <span class="text-danger">*</span></label>
                            <select name="id_inscripcion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($matriculasActivas as $matricula)
                                    <option value="{{ $matricula->id_inscripcion }}" {{ (int) old('id_inscripcion') === (int) $matricula->id_inscripcion ? 'selected' : '' }}>
                                        {{ $matricula->alumno }} - {{ $matricula->curso }} - {{ $matricula->gestion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_inscripcion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Monto base <span class="text-danger">*</span></label>
                            <input type="number" name="monto" step="0.01" min="0.01" class="form-control" value="{{ old('monto') }}" required>
                            @error('monto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Descuento</label>
                            <input type="number" name="descuento" step="0.01" min="0" class="form-control" value="{{ old('descuento', '0.00') }}">
                            @error('descuento') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Beca</label>
                            <select name="id_beca" id="id_beca" class="form-control">
                                <option value="">Sin beca</option>
                                @foreach($becas as $beca)
                                    <option value="{{ $beca->id_beca }}" data-porcentaje="{{ $beca->porcentaje }}" {{ (int) old('id_beca') === (int) $beca->id_beca ? 'selected' : '' }}>
                                        {{ $beca->nombre }} ({{ $beca->porcentaje }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('id_beca') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cogs"></i> Generar Mensualidades
                </button>
                <a href="{{ route('admin.mensualidades.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 3500
        });
    @endif

    document.getElementById('id_beca').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const porcentaje = selected ? parseFloat(selected.dataset.porcentaje) || 0 : 0;
        const monto = parseFloat(document.querySelector('input[name="monto"]').value) || 0;
        const descuentoInput = document.querySelector('input[name="descuento"]');

        if (porcentaje > 0 && monto > 0) {
            const descuento = Math.round(monto * (porcentaje / 100) * 100) / 100;
            descuentoInput.value = descuento.toFixed(2);
        } else if (!this.value) {
            descuentoInput.value = '0.00';
        }
    });

    document.querySelector('input[name="monto"]').addEventListener('input', function() {
        const event = new Event('change');
        document.getElementById('id_beca').dispatchEvent(event);
    });
</script>
@stop
