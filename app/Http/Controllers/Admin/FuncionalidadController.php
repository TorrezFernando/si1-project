<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcionalidad;
use App\Models\Modulo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// CU09: Controlador para gestionar funcionalidades que luego se asignan a roles.
class FuncionalidadController extends Controller
{
    // CU09: Lista funcionalidades con su modulo y filtro de busqueda.
    public function index(Request $request)
    {
        // CU09: Busca por nombre, descripcion o modulo.
        $search = trim((string) $request->input('search'));
        // CU09 y CU10: Carga modulo para ubicar cada funcionalidad dentro del sistema.
        $funcionalidades = Funcionalidad::with('modulo')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhereHas('modulo', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
            })
            ->orderBy('nombre')
            ->paginate(15)->appends(['search' => $search]);

        return view('admin.funcionalidades.index', compact('funcionalidades', 'search'));
    }

    // CU09: Abre formulario para registrar funcionalidad.
    public function create()
    {
        // CU10: Carga modulos disponibles para asociar la funcionalidad.
        $modulos = Modulo::orderBy('nombre')->get();
        return view('admin.funcionalidades.create', compact('modulos'));
    }

    // CU09: Guarda una nueva funcionalidad dentro de un modulo.
    public function store(Request $request)
    {
        // CU09: El nombre solo debe ser unico dentro del mismo modulo.
        $data = $request->validate([
            'id_modulo' => 'required|exists:modulos,id_modulo',
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('funcionalidades', 'nombre')->where('id_modulo', $request->input('id_modulo')),
            ],
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'La funcionalidad ya existe en este modulo.',
        ]);

        // CU09: Crea la funcionalidad que podra asignarse a roles.
        Funcionalidad::create($data);

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad registrada exitosamente.')
            ->with('icono', 'success');
    }

    // CU09: Abre formulario de edicion de funcionalidad.
    public function edit(Funcionalidad $funcionalidad)
    {
        // CU10: Carga modulos para permitir cambiar la agrupacion.
        $modulos = Modulo::orderBy('nombre')->get();
        return view('admin.funcionalidades.edit', compact('funcionalidad', 'modulos'));
    }

    // CU09: Actualiza datos de una funcionalidad.
    public function update(Request $request, Funcionalidad $funcionalidad)
    {
        // CU09: Valida nombre unico dentro del modulo seleccionado.
        $data = $request->validate([
            'id_modulo' => 'required|exists:modulos,id_modulo',
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('funcionalidades', 'nombre')
                    ->where('id_modulo', $request->input('id_modulo'))
                    ->ignore($funcionalidad->id_funcionalidad, 'id_funcionalidad'),
            ],
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'La funcionalidad ya existe en este modulo.',
        ]);

        // CU09: Guarda los cambios de nombre, descripcion o modulo.
        $funcionalidad->update($data);

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad actualizada exitosamente.')
            ->with('icono', 'success');
    }

    // CU09: Elimina una funcionalidad si no esta asignada a roles.
    public function destroy(Funcionalidad $funcionalidad)
    {
        // CU09: La BD bloquea eliminaciones si en el futuro se vincula a roles.
        try {
            // CU09: Intenta eliminar la funcionalidad seleccionada.
            $funcionalidad->delete();
        } catch (QueryException $e) {
            // CU09: Informa cuando la funcionalidad ya controla permisos de algun rol.
            return redirect()->route('admin.funcionalidades.index')
                ->with('mensaje', 'No se puede eliminar. La funcionalidad esta asignada a uno o mas roles.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.funcionalidades.index')
            ->with('mensaje', 'Funcionalidad eliminada exitosamente.')
            ->with('icono', 'success');
    }
}
