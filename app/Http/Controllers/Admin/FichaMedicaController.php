<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\FichaMedica;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// CU23: Controlador para gestionar fichas medicas de estudiantes.
class FichaMedicaController extends Controller
{
    private const TIPOS_SANGRE = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    // CU23: Lista, busca y permite consultar fichas medicas existentes.
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $fichas = FichaMedica::with('alumno')
            ->when($search, function ($query) use ($search) {
                $query->where('tipo_sangre', 'like', "%{$search}%")
                    ->orWhere('alergias', 'like', "%{$search}%")
                    ->orWhere('contacto_emergencia', 'like', "%{$search}%")
                    ->orWhere('telf_emerg', 'like', "%{$search}%")
                    ->orWhereHas('alumno', function ($alumnoQuery) use ($search) {
                        $alumnoQuery->where('ci', 'like', "%{$search}%")
                            ->orWhere('nombres', 'like', "%{$search}%")
                            ->orWhere('ap_paterno', 'like', "%{$search}%")
                            ->orWhere('ap_materno', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('id_ficha')
            ->paginate(15)->appends(['search' => $search]);

        return view('admin.fichas_medicas.index', compact('fichas', 'search'));
    }

    // CU23: Muestra formulario para nueva ficha medica.
    public function create(Request $request)
    {
        $alumnos = $this->alumnosParaSeleccion($request->input('alumno'));
        $tiposSangre = self::TIPOS_SANGRE;

        return view('admin.fichas_medicas.create', compact('alumnos', 'tiposSangre'));
    }

    // CU23: Registra ficha medica validando que el estudiante exista y no tenga ficha previa.
    public function store(Request $request)
    {
        $data = $this->validarFicha($request);

        $fichaExistente = FichaMedica::where('id_alumno', $data['id_alumno'])->first();

        if ($fichaExistente) {
            return redirect()->route('admin.fichas-medicas.edit', $fichaExistente)
                ->with('mensaje', 'El estudiante ya tiene ficha medica. Puede editarla desde esta pantalla.')
                ->with('icono', 'warning');
        }

        FichaMedica::create($data);

        return redirect()->route('admin.fichas-medicas.index')
            ->with('mensaje', 'Ficha medica registrada exitosamente.')
            ->with('icono', 'success');
    }

    // CU23: Consulta el detalle de una ficha medica.
    public function show(FichaMedica $ficha)
    {
        $ficha->load('alumno');

        return view('admin.fichas_medicas.show', compact('ficha'));
    }

    // CU23: Muestra datos actuales para editar ficha medica.
    public function edit(FichaMedica $ficha)
    {
        $ficha->load('alumno');
        $alumnos = $this->alumnosParaSeleccion(null, $ficha->id_alumno);
        $tiposSangre = self::TIPOS_SANGRE;

        return view('admin.fichas_medicas.edit', [
            'ficha' => $ficha,
            'alumnos' => $alumnos,
            'tiposSangre' => $tiposSangre,
        ]);
    }

    // CU23: Guarda cambios de tipo de sangre, alergias y contacto de emergencia.
    public function update(Request $request, FichaMedica $ficha)
    {
        $data = $this->validarFicha($request, $ficha);

        $ficha->update($data);

        return redirect()->route('admin.fichas-medicas.index')
            ->with('mensaje', 'Ficha medica actualizada exitosamente.')
            ->with('icono', 'success');
    }

    // CU23: Elimina ficha medica previa confirmacion desde la vista.
    public function destroy(FichaMedica $ficha)
    {
        try {
            $ficha->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.fichas-medicas.index')
                ->with('mensaje', 'No se puede eliminar la ficha medica porque tiene registros asociados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.fichas-medicas.index')
            ->with('mensaje', 'Ficha medica eliminada exitosamente.')
            ->with('icono', 'success');
    }

    private function validarFicha(Request $request, ?FichaMedica $ficha = null): array
    {
        $data = $request->validate([
            'id_alumno' => [
                'required',
                'integer',
                'exists:alumno,id_alumno',
                Rule::unique('ficha_medica', 'id_alumno')->ignore($ficha?->id_ficha, 'id_ficha'),
            ],
            'tipo_sangre' => ['required', Rule::in(self::TIPOS_SANGRE)],
            'alergias' => ['nullable', 'string', 'max:100'],
            'contacto_emergencia' => ['required', 'string', 'max:100'],
            'telf_emerg' => ['required', 'string', 'max:20'],
        ], [
            'id_alumno.exists' => 'El estudiante seleccionado no existe.',
            'id_alumno.unique' => 'El estudiante ya tiene una ficha medica registrada.',
            'tipo_sangre.in' => 'El tipo de sangre no es valido.',
            'contacto_emergencia.required' => 'El contacto de emergencia debe tener nombre.',
            'telf_emerg.required' => 'El contacto de emergencia debe tener telefono.',
        ]);

        $data['alergias'] = $data['alergias'] ?? '';

        return $data;
    }

    private function alumnosParaSeleccion(?string $search = null, ?int $idAlumnoActual = null)
    {
        $search = trim((string) $search);

        return Alumno::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('ci', 'like', "%{$search}%")
                        ->orWhere('nombres', 'like', "%{$search}%")
                        ->orWhere('ap_paterno', 'like', "%{$search}%")
                        ->orWhere('ap_materno', 'like', "%{$search}%");
                });
            })
            ->when($idAlumnoActual, function ($query) use ($idAlumnoActual) {
                $query->where(function ($subQuery) use ($idAlumnoActual) {
                    $subQuery->where('id_alumno', $idAlumnoActual)
                        ->orWhereDoesntHave('fichaMedica');
                });
            }, function ($query) {
                $query->whereDoesntHave('fichaMedica');
            })
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->limit(80)
            ->get();
    }
}
