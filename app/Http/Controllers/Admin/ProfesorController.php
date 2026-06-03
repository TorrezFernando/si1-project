<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Profesores\Services\ProfesorService;
use App\Http\Controllers\Controller;
use App\Models\Profesor;
use App\Models\ProfesorPermiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// CU02 y CU01: Controlador para gestionar docentes y sus usuarios de acceso.
class ProfesorController extends Controller
{
    // CU02: Limite maximo de docentes permitidos por el sistema.
    const MAX_PROFESORES = 20;

    // CU02 y CU01: Inyecta servicios usados para docente, usuario y permisos.
    public function __construct(
        protected ProfesorService $profesorService,
        protected AuthService $authService,
    ) {}

    // CU02: Lista todos los docentes registrados.
    public function index()
    {
        // CU02 y CU01: Carga docentes con usuario y permiso de horario.
        $profesores = Profesor::with(['usuario', 'permiso'])
            ->orderBy('ap_paterno')
            ->orderBy('ap_materno')
            ->orderBy('nombre')
            ->paginate(15);

        // CU02: Calcula total para validar capacidad maxima.
        $totalProfesores = Profesor::count();

        // CU02: Muestra la vista de listado de docentes.
        return view('admin.profesores.index', compact('profesores', 'totalProfesores'));
    }

    // CU02: Abre el formulario para registrar docente.
    public function create()
    {
        // CU02: Cuenta docentes registrados antes de permitir crear otro.
        $total = Profesor::count();

        // CU02: Bloquea la creacion si se alcanzo la capacidad maxima.
        if ($total >= self::MAX_PROFESORES) {
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'Capacidad al tope: no se pueden registrar más de ' . self::MAX_PROFESORES . ' profesores.')
                ->with('icono', 'warning');
        }

        // CU02: Devuelve la vista del formulario de nuevo docente.
        return view('admin.profesores.create');
    }

    // CU02 y CU01: Guarda un nuevo docente y crea su usuario.
    public function store(Request $request)
    {
        // CU02: Revalida el limite antes de guardar.
        if (Profesor::count() >= self::MAX_PROFESORES) {
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'Capacidad al tope: no se pueden registrar más de ' . self::MAX_PROFESORES . ' profesores.')
                ->with('icono', 'warning');
        }

        // CU02 y CU01: Valida datos personales, credenciales y confirmacion de password.
        $data = $request->validate([
            'nombre'     => ['required', 'string', 'max:50'],
            'ap_paterno' => ['required', 'string', 'max:50'],
            'ap_materno' => ['required', 'string', 'max:50'],
            'ci'         => ['required', 'string', 'max:20'],
            'genero'     => ['required', 'in:M,F'],
            'fecha_nac'  => ['required', 'date'],
            'direccion'  => ['required', 'string', 'max:100'],
            'telefono'   => ['required', 'string', 'max:20'],
            'correo'     => ['required', 'email', 'max:100'],
            'rda'        => ['nullable', 'string', 'max:20'],
            'username'   => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // CU02 y CU01: Usa transaccion para crear usuario y docente juntos.
        DB::transaction(function () use ($data) {
            // CU01: Crea el usuario con rol docente.
            $usuario = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_rol'   => 2,
            ]);

            // CU02: Crea el registro del docente vinculado al usuario.
            $profesor = Profesor::create([
                'nombre'     => $data['nombre'],
                'ap_paterno' => $data['ap_paterno'],
                'ap_materno' => $data['ap_materno'],
                'ci'         => $data['ci'],
                'genero'     => $data['genero'],
                'fecha_nac'  => $data['fecha_nac'],
                'direccion'  => $data['direccion'],
                'telefono'   => $data['telefono'],
                'correo'     => $data['correo'],
                'rda'        => $data['rda'] ?? '',
                'id_user'    => $usuario->id_user,
            ]);

            // CU02: Crea permiso inicial del docente para controlar acceso al horario.
            $this->profesorService->crearPermisoDefecto($profesor);
        });

        // CU02: Redirige al listado con mensaje de exito.
        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor registrado exitosamente.')
            ->with('icono', 'success');
    }

    // CU02 y CU01: Abre formulario para editar usuario/acceso del docente.
    public function edit($id)
    {
        // CU02 y CU01: Busca docente junto con usuario y permiso.
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        // CU02 y CU01: Muestra formulario de configuracion de acceso.
        return view('admin.profesores.edit', compact('profesor'));
    }

    // CU02 y CU01: Actualiza usuario, password y permiso de horario del docente.
    public function update(Request $request, $id)
    {
        // CU02 y CU01: Obtiene el docente a modificar.
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        // CU02 y CU01: Valida username, password opcional y permiso.
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore(optional($profesor->usuario)->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'puede_ver_horario' => ['nullable', 'boolean'],
        ]);

        // CU02 y CU01: Actualiza acceso dentro de una transaccion.
        DB::transaction(function () use ($profesor, $data, $request) {
            // CU01: Obtiene el usuario asociado al docente.
            $usuario = $profesor->usuario;

            // CU01: Si no existe usuario, lo crea para este docente.
            if (!$usuario) {
                $usuario = User::create([
                    'username' => $data['username'],
                    'password' => Hash::make($data['password'] ?: $this->profesorService->generarPasswordDefault($profesor)),
                    'id_rol' => 2,
                ]);

                // CU02 y CU01: Vincula el nuevo usuario al docente.
                $profesor->id_user = $usuario->id_user;
                $profesor->save();
            } else {
                // CU01: Actualiza credenciales del usuario existente.
                $usuario->username = $data['username'];
                $usuario->id_rol = 2;

                // CU01: Cambia password solo si se envio uno nuevo.
                if (!empty($data['password'])) {
                    $usuario->password = Hash::make($data['password']);
                }

                $usuario->save();
            }

            // CU02: Actualiza el permiso para ver horario.
            ProfesorPermiso::updateOrCreate(
                ['id_profesor' => $profesor->id_profesor],
                ['puede_ver_horario' => $request->boolean('puede_ver_horario')]
            );
        });

        // CU02: Redirige al listado con mensaje de exito.
        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU02: Abre formulario para editar informacion personal del docente.
    public function editInfo($id)
    {
        // CU02: Busca el docente por su identificador.
        $profesor = Profesor::findOrFail($id);

        // CU02: Muestra vista de edicion de datos personales.
        return view('admin.profesores.editInfo', compact('profesor'));
    }

    // CU02: Actualiza informacion personal del docente.
    public function updateInfo(Request $request, $id)
    {
        // CU02: Busca el docente a modificar.
        $profesor = Profesor::findOrFail($id);

        // CU02: Valida los campos personales del docente.
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'ap_paterno'  => ['required', 'string', 'max:100'],
            'ap_materno'  => ['nullable', 'string', 'max:100'],
            'ci'          => ['nullable', 'string', 'max:20'],
            'correo'      => ['nullable', 'email', 'max:150'],
            'telefono'    => ['nullable', 'string', 'max:20'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'genero'      => ['nullable', 'in:M,F'],
            'fecha_nac'   => ['nullable', 'date'],
        ]);

        // CU02: Guarda los datos personales actualizados.
        $profesor->fill($data)->save();

        // CU02: Redirige al listado con mensaje de exito.
        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Datos del profesor actualizados correctamente.')
            ->with('icono', 'success');
    }

    // CU02 y CU01: Elimina docente y relaciones de usuario/permisos.
    public function destroy($id)
    {
        // CU02 y CU01: Carga docente con usuario y permiso antes de eliminar.
        $profesor = Profesor::with(['usuario', 'permiso'])->findOrFail($id);

        // CU02: Intenta eliminar usando el servicio para limpiar relaciones.
        try {
            $this->profesorService->eliminarProfesor($profesor);
        } catch (\Exception $e) {
            // CU02: Informa cuando existen relaciones que impiden eliminar.
            return redirect()->route('admin.profesores.index')
                ->with('mensaje', 'No se puede eliminar el profesor: ' . $e->getMessage())
                ->with('icono', 'error');
        }

        // CU02: Redirige al listado con mensaje de eliminacion correcta.
        return redirect()->route('admin.profesores.index')
            ->with('mensaje', 'Profesor eliminado correctamente.')
            ->with('icono', 'success');
    }
}
