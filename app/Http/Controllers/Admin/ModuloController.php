<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

// CU10: Controlador para gestionar modulos del sistema y su relacion con funcionalidades.
class ModuloController extends Controller
{
    // CU10: Lista modulos con conteo de funcionalidades y busqueda.
    public function index(Request $request)
    {
        // CU10: Buscar modulos por nombre o descripcion, tal como indica el documento.
        $search = trim((string) $request->input('search'));
        // CU10 y CU09: Incluye cantidad de funcionalidades para entender dependencias.
        $modulos = Modulo::withCount('funcionalidades')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(15)->appends(['search' => $search]);

        return view('admin.modulos.index', compact('modulos', 'search'));
    }

    // CU10: Abre formulario para registrar modulo.
    public function create()
    {
        return view('admin.modulos.create');
    }

    // CU10: Guarda un nuevo modulo.
    public function store(Request $request)
    {
        // CU10: Evita nombres duplicados antes de guardar el modulo.
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:modulos,nombre',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'El modulo ya existe.',
        ]);

        // CU10: Registra el modulo que agrupara funcionalidades del sistema.
        Modulo::create($data);

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo registrado exitosamente.')
            ->with('icono', 'success');
    }

    // CU10: Abre formulario de edicion del modulo.
    public function edit(Modulo $modulo)
    {
        return view('admin.modulos.edit', compact('modulo'));
    }

    // CU10: Actualiza nombre y descripcion del modulo.
    public function update(Request $request, Modulo $modulo)
    {
        // CU10: Valida nombre unico ignorando el modulo actual.
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:modulos,nombre,' . $modulo->id_modulo . ',id_modulo',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'nombre.unique' => 'El modulo ya existe.',
        ]);

        // CU10: Guarda los cambios del modulo.
        $modulo->update($data);

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU10: Elimina un modulo si no tiene funcionalidades asociadas.
    public function destroy(Modulo $modulo)
    {
        // CU10: No elimina modulos con funcionalidades asociadas.
        if ($modulo->funcionalidades()->exists()) {
            return redirect()->route('admin.modulos.index')
                ->with('mensaje', 'No se puede eliminar. El modulo tiene funcionalidades registradas.')
                ->with('icono', 'error');
        }

        try {
            // CU10: Intenta borrar el modulo cuando ya no depende de funcionalidades.
            $modulo->delete();
        } catch (QueryException $e) {
            // CU10: Informa restricciones adicionales detectadas por la base de datos.
            return redirect()->route('admin.modulos.index')
                ->with('mensaje', 'No se puede eliminar el modulo porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.modulos.index')
            ->with('mensaje', 'Modulo eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
