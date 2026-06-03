<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Alumnos\Services\AlumnoService;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// CU03 y CU01: Controlador para gestionar estudiantes y sus usuarios de acceso.
class AlumnoController extends Controller
{
    // CU03: Inyecta el servicio que crea, actualiza y elimina estudiantes con usuario.
    public function __construct(
        protected AlumnoService $alumnoService
    ) {}

    // CU03: Lista todos los estudiantes registrados.
    public function index()
    {
        // CU03 y CU01: Carga estudiantes con su usuario asociado.
        $alumnos = Alumno::with('usuario')
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->paginate(15);

        // CU03: Muestra la vista de listado de estudiantes.
        return view('admin.alumnos.index', compact('alumnos'));
    }

    // CU03: Abre formulario para registrar estudiante.
    public function create()
    {
        // CU03: Devuelve la vista de creacion de estudiante.
        return view('admin.alumnos.create');
    }

    // CU03 y CU01: Guarda estudiante y crea usuario vinculado.
    public function store(Request $request)
    {
        // CU03 y CU01: Valida datos personales y credenciales del estudiante.
        $data = $request->validate([
            'ci' => ['required', 'string', 'max:20', 'unique:alumno,ci'],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['F', 'M'])],
            'fecha_nac' => ['required', 'date'],
            'telefono' => ['required', 'string', 'max:20'],
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // CU03 y CU01: Crea alumno y usuario en el servicio.
        $this->alumnoService->crearConUsuario($data);

        // CU03: Redirige al listado con mensaje de exito.
        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno creado exitosamente.')
            ->with('icono', 'success');
    }

    // CU03: Abre formulario para editar estudiante.
    public function edit($id)
    {
        // CU03 y CU01: Busca estudiante con su usuario.
        $alumno = Alumno::with('usuario')->findOrFail($id);

        // CU03: Muestra vista de edicion.
        return view('admin.alumnos.edit', compact('alumno'));
    }

    // CU03 y CU01: Actualiza estudiante y usuario vinculado.
    public function update(Request $request, $id)
    {
        // CU03 y CU01: Obtiene estudiante con usuario asociado.
        $alumno = Alumno::with('usuario')->findOrFail($id);

        // CU03 y CU01: Valida datos personales y credenciales editables.
        $data = $request->validate([
            'ci' => ['required', 'string', 'max:20', Rule::unique('alumno', 'ci')->ignore($alumno->id_alumno, 'id_alumno')],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['F', 'M'])],
            'fecha_nac' => ['required', 'date'],
            'telefono' => ['required', 'string', 'max:20'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($alumno->usuario)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // CU03 y CU01: Actualiza alumno y usuario en el servicio.
        $this->alumnoService->actualizarConUsuario($alumno, $data);

        // CU03: Redirige al listado con mensaje de exito.
        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU03 y CU01: Elimina estudiante y usuario vinculado.
    public function destroy($id)
    {
        // CU03 y CU01: Busca estudiante con usuario antes de eliminar.
        $alumno = Alumno::with('usuario')->findOrFail($id);

        // CU03 y CU01: Intenta eliminar alumno y usuario asociado.
        try {
            $this->alumnoService->eliminar($alumno);
        } catch (QueryException $e) {
            // CU03: Informa si el estudiante tiene registros relacionados.
            return redirect()->route('admin.alumnos.index')
                ->with('mensaje', 'No se puede eliminar el alumno porque tiene registros relacionados en el sistema.')
                ->with('icono', 'error');
        }

        // CU03: Redirige al listado con mensaje de eliminacion correcta.
        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
