<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BecaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $becas = Beca::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(nombre) LIKE LOWER(?)', ["%{$search}%"])
                        ->orWhereRaw('LOWER(descripcion) LIKE LOWER(?)', ["%{$search}%"])
                        ->orWhere('porcentaje', 'like', "%{$search}%");
                });
            })
            ->addSelect(['alumnos_asignados' => DB::table('pago_mensual')
                ->whereColumn('pago_mensual.id_beca', 'beca.id_beca')
                ->selectRaw('COUNT(DISTINCT id_alumno)')
            ])
            ->orderBy('nombre')
            ->paginate(15)->appends($request->except('page'));

        return view('admin.becas.index', compact('becas', 'search'));
    }

    public function create()
    {
        return view('admin.becas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        Beca::create($data);

        return redirect()->route('admin.becas.index')
            ->with('mensaje', 'Beca registrada exitosamente')
            ->with('icono', 'success');
    }

    public function edit(int $id)
    {
        $beca = Beca::findOrFail($id);

        return view('admin.becas.edit', compact('beca'));
    }

    public function update(Request $request, int $id)
    {
        $beca = Beca::findOrFail($id);
        $data = $this->validar($request, $beca);

        $beca->update($data);

        return redirect()->route('admin.becas.index')
            ->with('mensaje', 'Beca actualizada exitosamente')
            ->with('icono', 'success');
    }

    public function destroy(int $id)
    {
        $beca = Beca::findOrFail($id);
        $alumnosAsignados = DB::table('pago_mensual')
            ->where('id_beca', $id)
            ->distinct('id_alumno')
            ->count('id_alumno');

        if ($alumnosAsignados > 0) {
            return redirect()->route('admin.becas.index')
                ->with('mensaje', 'No se puede eliminar. La beca esta asignada a uno o mas estudiantes activos')
                ->with('icono', 'error');
        }

        $beca->delete();

        return redirect()->route('admin.becas.index')
            ->with('mensaje', 'Beca eliminada exitosamente')
            ->with('icono', 'success');
    }

    private function validar(Request $request, ?Beca $beca = null): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('beca', 'nombre')->ignore($beca?->id_beca, 'id_beca'),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'activo' => ['nullable', 'boolean'],
            'admin_only' => ['nullable', 'boolean'],
        ], [
            'nombre.required' => 'El nombre de la beca es obligatorio.',
            'nombre.unique' => 'Ya existe una beca con ese nombre.',
            'porcentaje.required' => 'El porcentaje de descuento es obligatorio.',
            'porcentaje.numeric' => 'El porcentaje debe ser un valor numerico.',
            'porcentaje.min' => 'El porcentaje no puede ser menor a 0.',
            'porcentaje.max' => 'El porcentaje no puede ser mayor a 100.',
        ]);
    }
}
