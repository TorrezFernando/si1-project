<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfraestructuraController extends Controller
{
    private const ESTADOS = ['Activo', 'Inactivo', 'Mantenimiento'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $estado = $request->input('estado');

        $aulas = Aula::query()
            ->withCount('materiaCursoGestionParalelo as horarios_asignados')
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereRaw('LOWER(nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(tipo) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(ubicacion) LIKE LOWER(?)', [$like])
                        ->orWhere('capacidad', 'like', $like);
                });
            })
            ->when(in_array($estado, self::ESTADOS, true), fn ($query) => $query->where('estado', $estado))
            ->orderBy('nombre')
            ->paginate(15)->appends($request->except('page'));

        $estados = self::ESTADOS;

        return view('admin.infraestructura.index', compact('aulas', 'search', 'estado', 'estados'));
    }

    public function create()
    {
        $estados = self::ESTADOS;

        return view('admin.infraestructura.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        Aula::create($data);

        return redirect()->route('admin.infraestructura.index')
            ->with('mensaje', 'Infraestructura registrada exitosamente')
            ->with('icono', 'success');
    }

    public function edit(int $id)
    {
        $aula = Aula::findOrFail($id);
        $estados = self::ESTADOS;

        return view('admin.infraestructura.edit', compact('aula', 'estados'));
    }

    public function update(Request $request, int $id)
    {
        $aula = Aula::findOrFail($id);
        $data = $this->validar($request, $aula);

        $aula->update($data);

        return redirect()->route('admin.infraestructura.index')
            ->with('mensaje', 'Infraestructura actualizada exitosamente')
            ->with('icono', 'success');
    }

    public function destroy(int $id)
    {
        $aula = Aula::withCount('materiaCursoGestionParalelo as horarios_asignados')->findOrFail($id);

        if ($aula->horarios_asignados > 0) {
            return redirect()->route('admin.infraestructura.index')
                ->with('mensaje', 'No se puede eliminar. El aula tiene horarios asignados')
                ->with('icono', 'error');
        }

        $aula->delete();

        return redirect()->route('admin.infraestructura.index')
            ->with('mensaje', 'Infraestructura eliminada exitosamente')
            ->with('icono', 'success');
    }

    private function validar(Request $request, ?Aula $aula = null): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('aula', 'nombre')->ignore($aula?->id_aula, 'id_aula'),
            ],
            'tipo' => ['required', 'string', 'max:60'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:999'],
            'ubicacion' => ['required', 'string', 'max:100'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ], [
            'nombre.required' => 'El nombre del aula es obligatorio.',
            'nombre.unique' => 'El aula ya existe.',
            'tipo.required' => 'El tipo de ambiente es obligatorio.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un numero entero.',
            'capacidad.min' => 'La capacidad debe ser mayor a cero.',
            'ubicacion.required' => 'La ubicacion es obligatoria.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es valido.',
        ]);
    }
}
