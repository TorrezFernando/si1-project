<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Apoderado;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// CU04: Controlador para gestionar tutores/apoderados y sus estudiantes vinculados.
class ApoderadoController extends Controller
{
    // CU04: Lista tutores y permite buscar por datos del tutor, usuario o estudiante vinculado.
    public function index(Request $request)
    {
        // CU04: Normaliza el texto buscado para evitar espacios innecesarios.
        $search = trim((string) $request->input('search'));

        // CU04 y CU01: Carga tutores con estudiantes y usuario de acceso.
        $apoderados = Apoderado::with(['alumnos', 'usuario'])
            ->when($search, function ($query) use ($search) {
                // CU04: Busca coincidencias en datos personales del tutor.
                $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%")
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($search) {
                        // CU01: Tambien permite encontrar al tutor por su usuario.
                        $usuarioQuery->where('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('alumnos', function ($alumnoQuery) use ($search) {
                        // CU04 y CU03: Busca tutores a partir de los estudiantes bajo su responsabilidad.
                        $alumnoQuery->where('ci', 'like', "%{$search}%")
                            ->orWhere('nombres', 'like', "%{$search}%")
                            ->orWhere('ap_paterno', 'like', "%{$search}%")
                            ->orWhere('ap_materno', 'like', "%{$search}%");
                    });
            })
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->paginate(15)
            ->appends(['search' => $search]);

        // CU04: Entrega el listado filtrado a la vista administrativa.
        return view('admin.apoderados.index', compact('apoderados', 'search'));
    }

    // CU04: Abre el formulario para registrar un nuevo tutor.
    public function create(Request $request)
    {
        // CU04 y CU03: Prepara estudiantes disponibles para vincularlos al tutor.
        $alumnos = $this->alumnosParaSeleccion($request->input('alumno'));

        return view('admin.apoderados.create', compact('alumnos'));
    }

    // CU04 y CU01: Guarda tutor, usuario de acceso y relacion con estudiantes.
    public function store(Request $request)
    {
        // CU04: Valida datos personales y parentesco del tutor.
        $data = $this->validarApoderado($request);
        // CU01: Valida las credenciales que usara el tutor para iniciar sesion.
        $credenciales = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        // CU04 y CU03: Valida los estudiantes que quedaran bajo este tutor.
        $alumnos = $this->validarAlumnos($request);
        $descripcionParentesco = $data['descripcion_parentesco'];
        unset($data['descripcion_parentesco']);

        // CU04 y CU01: Usa transaccion para mantener juntos usuario, tutor y parentescos.
        DB::transaction(function () use ($data, $credenciales, $alumnos, $descripcionParentesco) {
            // CU01: Crea usuario con rol de apoderado.
            $usuario = User::create([
                'username' => $credenciales['username'],
                'password' => Hash::make($credenciales['password']),
                'id_rol' => Rol::APODERADO->value,
            ]);

            $data['id_user'] = $usuario->id_user;
            // CU04: Registra datos personales del tutor.
            $apoderado = Apoderado::create($data);
            // CU04: Vincula el tutor con sus estudiantes y parentesco.
            $this->sincronizarAlumnos($apoderado, $alumnos, $descripcionParentesco);
        });

        $mensaje = 'Tutor registrado exitosamente.';

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', $mensaje)
            ->with('icono', 'success');
    }

    // CU04 y CU01: Abre formulario de edicion con datos del tutor, usuario y estudiantes.
    public function edit(Apoderado $apoderado)
    {
        // CU04 y CU01: Precarga relaciones para mostrarlas en el formulario.
        $apoderado->load(['alumnos', 'usuario']);
        $alumnos = $this->alumnosParaSeleccion(null);
        $usuario = $apoderado->usuarioConsulta();

        return view('admin.apoderados.edit', compact('apoderado', 'alumnos', 'usuario'));
    }

    // CU04 y CU01: Actualiza tutor, credenciales y estudiantes vinculados.
    public function update(Request $request, Apoderado $apoderado)
    {
        // CU04: Valida datos personales sin duplicar el CI del mismo registro.
        $data = $this->validarApoderado($request, $apoderado);
        $usuarioActual = $apoderado->usuarioConsulta();
        // CU01: Valida usuario unico y password opcional.
        $credenciales = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($usuarioActual)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);
        // CU04 y CU03: Revalida estudiantes seleccionados.
        $alumnos = $this->validarAlumnos($request);
        $descripcionParentesco = $data['descripcion_parentesco'];
        unset($data['descripcion_parentesco']);

        // CU04 y CU01: Agrupa cambios para evitar registros incompletos si algo falla.
        DB::transaction(function () use ($apoderado, $data, $credenciales, $alumnos, $descripcionParentesco) {
            $usuario = $apoderado->usuarioConsulta();

            // CU01: Si el tutor antiguo no tiene usuario, lo crea.
            if (!$usuario) {
                $usuario = User::create([
                    'username' => $credenciales['username'],
                    'password' => Hash::make($credenciales['password'] ?: 'apoderado' . $apoderado->id_apoderado),
                    'id_rol' => Rol::APODERADO->value,
                ]);

                $data['id_user'] = $usuario->id_user;
            } else {
                // CU01: Actualiza el usuario existente y mantiene el rol de apoderado.
                $usuario->username = $credenciales['username'];
                $usuario->id_rol = Rol::APODERADO->value;

                // CU01: Cambia password solo cuando el formulario envia una nueva.
                if (!empty($credenciales['password'])) {
                    $usuario->password = Hash::make($credenciales['password']);
                }

                $usuario->save();
                $data['id_user'] = $usuario->id_user;
            }

            // CU04: Actualiza datos del tutor y rehace sus vinculos con estudiantes.
            $apoderado->update($data);
            $this->sincronizarAlumnos($apoderado, $alumnos, $descripcionParentesco);
        });

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', 'Tutor actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU04 y CU01: Elimina tutor si no tiene estudiantes matriculados dependientes.
    public function destroy(Apoderado $apoderado)
    {
        // CU04: Protege la informacion academica evitando borrar tutores con matriculas.
        if ($this->tieneEstudiantesMatriculados($apoderado)) {
            return redirect()->route('admin.apoderados.index')
                ->with('mensaje', 'No se puede eliminar. El tutor tiene estudiantes matriculados.')
                ->with('icono', 'error');
        }

        try {
            // CU04 y CU01: Quita parentescos, tutor y usuario dentro de una transaccion.
            DB::transaction(function () use ($apoderado) {
                $usuario = $apoderado->usuarioConsulta();
                $apoderado->alumnos()->detach();
                $apoderado->delete();
                $usuario?->delete();
            });
        } catch (QueryException $e) {
            // CU04: Muestra error cuando la BD detecta relaciones no eliminables.
            return redirect()->route('admin.apoderados.index')
                ->with('mensaje', 'No se puede eliminar el tutor porque tiene registros asociados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.apoderados.index')
            ->with('mensaje', 'Tutor eliminado exitosamente.')
            ->with('icono', 'success');
    }

    // CU04: Centraliza reglas de validacion para datos personales del tutor.
    private function validarApoderado(Request $request, ?Apoderado $apoderado = null): array
    {
        return $request->validate([
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('apoderado', 'ci')->ignore($apoderado?->id_apoderado, 'id_apoderado'),
            ],
            'nombres' => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'genero' => ['required', Rule::in(['M', 'F'])],
            'ocupacion' => ['required', 'string', 'max:50'],
            'fecha_nac' => ['required', 'date'],
            'telefono' => ['required', 'string', 'max:20'],
            'descripcion_parentesco' => ['required', 'string', 'max:30'],
        ], [
            'ci.unique' => 'Ya existe una persona con ese CI.',
        ]);
    }

    // CU04 y CU03: Valida que el tutor tenga al menos un estudiante existente.
    private function validarAlumnos(Request $request): array
    {
        $data = $request->validate([
            'alumnos' => ['required', 'array', 'min:1'],
            'alumnos.*' => ['integer', 'exists:alumno,id_alumno'],
        ], [
            'alumnos.required' => 'Debe vincular al menos un estudiante existente.',
            'alumnos.*.exists' => 'Uno de los estudiantes seleccionados no existe.',
        ]);

        // CU04: Evita duplicados cuando el formulario envia el mismo estudiante mas de una vez.
        return array_unique($data['alumnos']);
    }

    // CU04: Sincroniza la tabla parentesco con la descripcion del vinculo familiar.
    private function sincronizarAlumnos(Apoderado $apoderado, array $alumnos, string $descripcion): void
    {
        $syncData = [];

        // CU04: Prepara el formato requerido por sync para guardar datos extra en la relacion.
        foreach ($alumnos as $idAlumno) {
            $syncData[$idAlumno] = ['descripcion' => $descripcion];
        }

        $apoderado->alumnos()->sync($syncData);
    }

    // CU03 y CU04: Obtiene estudiantes para el selector del formulario de tutores.
    private function alumnosParaSeleccion(?string $search = null)
    {
        $search = trim((string) $search);

        return Alumno::query()
            ->when($search, function ($query) use ($search) {
                // CU03: Filtra estudiantes por CI o nombres para vincularlos rapidamente.
                $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('ap_paterno', 'like', "%{$search}%")
                    ->orWhere('ap_materno', 'like', "%{$search}%");
            })
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombres')
            ->limit(120)
            ->get();
    }

    // CU04 y CU11: Verifica si el tutor participa en inscripciones ya registradas.
    private function tieneEstudiantesMatriculados(Apoderado $apoderado): bool
    {
        return DB::table('parentesco as p')
            ->join('inscripcion as i', 'i.id_alumno', '=', 'p.id_alumno')
            ->where('p.id_apoderado', $apoderado->id_apoderado)
            ->exists();
    }
}
