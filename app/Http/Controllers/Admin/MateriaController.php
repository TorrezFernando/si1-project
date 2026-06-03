<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\CampoSaberes;
use Illuminate\Http\Request;

// CU13: Controlador para gestionar materias y su campo de saberes.
class MateriaController extends Controller
{
    // CU13: Lista materias con busqueda por nombre, carga horaria o distintivo.
    public function index(Request $request)
    {
        // CU13: Normaliza el texto de busqueda.
        $search = trim((string) $request->input('search'));

        // CU13: Carga la relacion con campo de saberes para mostrar informacion completa.
        $query = Materia::with('campo');

        if ($search) {
            // CU13: Busca materias por nombre o carga horaria.
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre) LIKE LOWER(?)', ['%' . $search . '%'])
                  ->orWhere('carga_horaria', 'like', '%' . $search . '%')
                  ->orWhereRaw('LOWER(distintivo) LIKE LOWER(?)', ['%' . $search . '%']);
            });
        }

        // CU13: Ejecuta la consulta ordenada alfabeticamente.
        $materias = $query->orderBy('nombre', 'asc')->paginate(15)->appends(['search' => $search]);

        return view('admin.materias.index', compact('materias', 'search'));
    }

    // CU13: Abre formulario para registrar una materia.
    public function create()
    {
        // CU13: Carga campos de saberes para clasificar la materia.
        $campos = CampoSaberes::all();
        return view('admin.materias.create', compact('campos'));
    }

    // CU13: Guarda una nueva materia.
    public function store(Request $request)
    {
        // CU13: Valida nombre unico, carga horaria y campo de saberes.
        $request->validate([
            'nombre' => 'required|string|max:255|unique:materia,nombre',
            'carga_horaria' => 'required|integer|min:1|max:80',
            'distintivo' => 'nullable|string|max:255',
            'id_campo' => 'required|exists:campos_saberes_conocimientos,id_campo',
        ], [
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.unique' => 'Ya existe una materia con este nombre.',
            'id_campo.required' => 'El campo de saberes es obligatorio.',
            'id_campo.exists' => 'El campo de saberes seleccionado no es válido.',
        ]);

        // CU13: Registra la materia con sus datos academicos.
        Materia::create([
            'nombre' => $request->nombre,
            'carga_horaria' => $request->carga_horaria,
            'distintivo' => $request->distintivo,
            'id_campo' => $request->id_campo,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'Materia creada con éxito')
            ->with('icono', 'success');
    }

    // CU13: Abre formulario de edicion de materia.
    public function edit($id)
    {
        // CU13: Busca materia y carga campos para el selector.
        $materia = Materia::findOrFail($id);
        $campos = CampoSaberes::all();
        return view('admin.materias.edit', compact('materia', 'campos'));
    }

    // CU13: Actualiza los datos editables de una materia.
    public function update(Request $request, $id)
    {
        // CU13: Valida nombre unico ignorando la materia actual y campo valido.
        $request->validate([
            'nombre' => 'required|string|max:255|unique:materia,nombre,' . $id . ',id_materia',
            /* 'carga_horaria' => 'required|integer|min:1|max:80', */
            'distintivo' => 'nullable|string|max:255',
            'id_campo' => 'required|exists:campos_saberes_conocimientos,id_campo',
        ], [
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.unique' => 'Ya existe una materia con este nombre.',
            'id_campo.required' => 'El campo de saberes es obligatorio.',
            'id_campo.exists' => 'El campo de saberes seleccionado no es válido.',
        ]);

        // CU13: Aplica los cambios a la materia seleccionada.
        $materia = Materia::findOrFail($id);
        $materia->update([
            'nombre' => $request->nombre,
            /* 'carga_horaria' => $request->carga_horaria, */
            'distintivo' => $request->distintivo,
            'id_campo' => $request->id_campo,
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'Materia actualizada con éxito')
            ->with('icono', 'success');
    }

    // CU13: Elimina una materia cuando no participa en cursos, notas u horarios.
    public function destroy($id)
    {
        // CU13: Localiza la materia por su identificador.
        $materia = Materia::findOrFail($id);
        try {
            // CU13: Intenta eliminar y deja que la integridad referencial proteja relaciones.
            $materia->delete();
            return redirect()->route('admin.materias.index')
                ->with('mensaje', 'Materia eliminada con éxito')
                ->with('icono', 'success');
        } catch (\Exception $e) {
            // CU13, CU14 y CU15: Informa si la materia esta relacionada con horario o notas.
            return redirect()->route('admin.materias.index')
                ->with('mensaje', 'No se puede eliminar la materia porque tiene registros relacionados.')
                ->with('icono', 'error');
        }
    }
}
