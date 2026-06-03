<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Nivel;
use App\Models\Paralelo;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// CU12: Controlador para gestionar cursos/grupos dentro de la gestion academica.
class CursoController extends Controller
{
    // CU12: Lista cursos con busqueda y filtros por curso/paralelo.
    public function index(Request $request)
    {
        // CU12: Lee filtros enviados desde la pantalla administrativa.
        $search = trim((string) $request->input('search'));
        $curso_id = $request->input('curso_id');
        $paralelo_id = $request->input('paralelo_id');

        // CU12: Lista y busca cursos por grado, nivel, paralelo o turno.
        $query = Curso::with(['nivel', 'paralelo', 'turno']);

        if ($search) {
            // CU12: Busca cursos por nombre, grado, nivel, paralelo o turno.
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('grado', 'like', "%{$search}%")
                ->orWhereHas('nivel', fn ($q) => $q->where('nombre', 'like', "%{$search}%"))
                ->orWhereHas('paralelo', fn ($q) => $q->where('descripcion', 'like', "%{$search}%"))
                ->orWhereHas('turno', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
        }

        if ($curso_id) {
            // CU12: Filtra un curso especifico seleccionado en la vista.
            $query->where('id_curso', $curso_id);
        }

        if ($paralelo_id) {
            // CU12 y CU14: Filtra cursos que tienen materias asignadas al paralelo indicado.
            $query->whereIn('id_curso', function ($q) use ($paralelo_id) {
                $q->select('id_curso')
                  ->from('materia_curso_gestion_paralelo')
                  ->where('id_paralelo', $paralelo_id);
            });
        }

        if ($paralelo_id) {
            // CU12: Carga solo el paralelo filtrado para mostrar datos consistentes.
            $query->with(['paralelos' => function($q) use ($paralelo_id) {
                $q->where('paralelo.id_paralelo', $paralelo_id);
            }]);
        } else {
            // CU12: Sin filtro, carga todos los paralelos relacionados al curso.
            $query->with('paralelos');
        }

        // CU12: Ejecuta la consulta final ordenada para el listado.
        $cursos = $query->orderBy('nombre', 'asc')->paginate(15)->appends($request->except('page'));

        // CU12: Catalogos usados por los filtros del listado.
        $all_cursos = Curso::all();
        $all_paralelos = \App\Models\Paralelo::all();
        
        return view('admin.cursos.index', compact('cursos', 'search', 'all_cursos', 'all_paralelos', 'curso_id', 'paralelo_id'));
    }

    // CU12: Abre formulario para crear curso con catalogos academicos.
    public function create()
    {
        return view('admin.cursos.create', $this->catalogos());
    }

    // CU12: Guarda un nuevo curso.
    public function store(Request $request)
    {
        // CU12: Valida datos minimos del curso antes de registrarlo.
        $data = $this->validarCurso($request);
        //$data['nombre'] = $this->nombreCurso($data);

        // CU12: Crea el curso en la tabla principal.
        Curso::create($data);

        return redirect()->route('admin.cursos.index')
            ->with('mensaje', 'Curso creado con éxito')
            ->with('icono', 'success');
    }

    // CU12: Abre formulario de edicion del curso.
    public function edit($id)
    {
        // CU12: Busca curso y carga catalogos para los selectores.
        $curso = Curso::findOrFail($id);
        //dd($curso);
        return view('admin.cursos.edit', ['curso' => $curso] + $this->catalogos());
    }

    // CU12: Actualiza datos del curso.
    public function update(Request $request, $id)
    {
        // CU12: Busca el curso, valida cambios y los persiste.
        $curso = Curso::findOrFail($id);
        $data = $this->validarCurso($request, $curso->id_curso);
        //$data['nombre'] = $this->nombreCurso($data);
        $curso->update($data);

        return redirect()->route('admin.cursos.index')
            ->with('mensaje', 'Curso actualizado con éxito')
            ->with('icono', 'success');
    }

    // CU12: Elimina un curso si no esta vinculado con materias, inscripciones u otros registros.
    public function destroy($id)
    {
        // CU12: Localiza el curso por su identificador.
        $curso = Curso::findOrFail($id);
        try {
            // CU12: Intenta borrar y deja que la BD proteja relaciones existentes.
            $curso->delete();
            return redirect()->route('admin.cursos.index')
                ->with('mensaje', 'Curso eliminado con éxito')
                ->with('icono', 'success');
        } catch (\Exception $e) {
            // CU12 y CU11: Informa cuando el curso participa en registros academicos.
            return redirect()->route('admin.cursos.index')
                ->with('mensaje', 'No se puede eliminar el curso porque tiene registros relacionados.')
                ->with('icono', 'error');
        }
    }

    // CU12: Centraliza reglas de validacion para crear o editar cursos.
    private function validarCurso(Request $request, ?int $id = null): array
    {
        // CU12: No permite duplicar grado, nivel, paralelo y turno.
        return $request->validate([
            'nombre' => [ //MODIFIED era grado
                'required',
                'string',
                'max:50',
                /* Rule::unique('curso', 'grado')
                    ->where('id_nivel', $request->input('id_nivel'))
                    ->where('id_paralelo', $request->input('id_paralelo'))
                    ->where('id_turno', $request->input('id_turno'))
                    ->ignore($id, 'id_curso'), */
            ],
            /* 'id_paralelo' => 'required|exists:paralelo,id_paralelo',
            'id_nivel' => 'required|exists:nivels,id',
            'id_turno' => 'required|exists:turnos,id', */
        ], [
            //'grado.unique' => 'El curso ya existe.',
            'nombre.unique' => 'El curso ya existe.',
        ]);
    }

    // CU12: Carga niveles, paralelos y turnos para formularios de curso.
    private function catalogos(): array
    {
        return [
            'niveles' => Nivel::orderBy('nombre')->get(),
            'paralelos' => Paralelo::orderBy('descripcion')->get(),
            'turnos' => Turno::orderBy('nombre')->get(),
        ];
    }

    // CU12: Construye un nombre compuesto a partir de grado, nivel, paralelo y turno.
    private function nombreCurso(array $data): string
    {
        // CU12: Obtiene las etiquetas relacionadas para armar el nombre visible del curso.
        $nivel = Nivel::find($data['id_nivel'])?->nombre;
        $paralelo = Paralelo::find($data['id_paralelo'])?->descripcion;
        $turno = Turno::find($data['id_turno'])?->nombre;

        return trim($data['grado'] . ' ' . $nivel . ' ' . $paralelo . ' ' . $turno);
    }
}
