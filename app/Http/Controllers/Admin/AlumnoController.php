<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Alumnos\Services\AlumnoService;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    public function __construct(
        protected AlumnoService $alumnoService
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $becados = $request->boolean('becados');

        $query = Alumno::with('usuario', 'beca');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT_WS(' ', nombres, ap_paterno, ap_materno) LIKE ?", ["%{$search}%"])
                  ->orWhere('ci', 'like', "%{$search}%");
            });
        }

        if ($idCurso) {
            $ids = DB::table('inscripcion')
                ->join('inscripcion_curso_gestion', 'inscripcion_curso_gestion.id_inscripcion', '=', 'inscripcion.id_inscripcion')
                ->where('inscripcion_curso_gestion.id_curso', $idCurso)
                ->pluck('inscripcion.id_alumno');
            $query->whereIn('alumno.id_alumno', $ids);
        }

        if ($idMateria) {
            $ids = DB::table('inscripcion')
                ->join('inscripcion_curso_gestion', 'inscripcion_curso_gestion.id_inscripcion', '=', 'inscripcion.id_inscripcion')
                ->join('materia_curso_gestion', function ($j) {
                    $j->on('materia_curso_gestion.id_curso', '=', 'inscripcion_curso_gestion.id_curso')
                      ->on('materia_curso_gestion.id_gestion', '=', 'inscripcion_curso_gestion.id_gestion');
                })
                ->where('materia_curso_gestion.id_materia', $idMateria)
                ->pluck('inscripcion.id_alumno');
            $query->whereIn('alumno.id_alumno', $ids);
        }

        if ($becados) {
            $query->whereNotNull('alumno.id_beca');
        }

        $alumnos = $query->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->paginate(15)
            ->appends($request->except('page'));

        $cursos = DB::table('curso')->orderBy('nombre')->get();
        $materias = DB::table('materia')->orderBy('nombre')->get();

        return view('admin.alumnos.index', compact('alumnos', 'search', 'idCurso', 'idMateria', 'becados', 'cursos', 'materias'));
    }

    public function create()
    {
        $becas = DB::table('beca')->where('activo', true)->orderBy('nombre')->get();
        return view('admin.alumnos.create', compact('becas'));
    }

    public function store(Request $request)
    {
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
            'id_beca' => ['nullable', 'integer', 'exists:beca,id_beca'],
        ]);

        $data['id_beca'] = empty($data['id_beca']) ? null : (int) $data['id_beca'];
        $this->alumnoService->crearConUsuario($data);

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno creado exitosamente.')
            ->with('icono', 'success');
    }

    public function edit($id)
    {
        $alumno = Alumno::with('usuario', 'beca')->findOrFail($id);
        $becas = DB::table('beca')->where('activo', true)->orderBy('nombre')->get();

        return view('admin.alumnos.edit', compact('alumno', 'becas'));
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::with('usuario')->findOrFail($id);

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
            'id_beca' => ['nullable', 'integer', 'exists:beca,id_beca'],
        ]);

        $data['id_beca'] = empty($data['id_beca']) ? null : (int) $data['id_beca'];
        $this->alumnoService->actualizarConUsuario($alumno, $data);

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno actualizado exitosamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $alumno = Alumno::with('usuario')->findOrFail($id);

        try {
            $this->alumnoService->eliminar($alumno);
        } catch (QueryException $e) {
            return redirect()->route('admin.alumnos.index')
                ->with('mensaje', 'No se puede eliminar el alumno porque tiene registros relacionados en el sistema.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.alumnos.index')
            ->with('mensaje', 'Alumno eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
