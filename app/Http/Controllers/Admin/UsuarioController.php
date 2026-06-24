<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// CU01: Controlador administrativo para gestionar credenciales de usuarios del sistema.
class UsuarioController extends Controller
{
    // CU01: Lista usuarios y permite buscar por username, id_user o id_rol.
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $usuarios = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('id_user', $search)
                    ->orWhere('id_rol', $search);
            })
            ->orderBy('username')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Rol::cases(),
            'search' => $search,
        ]);
    }

    // CU01: Abre formulario para registrar una credencial de acceso independiente.
    public function create()
    {
        return view('admin.usuarios.create', [
            'roles' => Rol::cases(),
        ]);
    }

    // CU01: Guarda un usuario validando username unico y rol existente.
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:usuario,username'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'id_rol' => ['required', Rule::in(array_column(Rol::cases(), 'value'))],
        ], [
            'username.unique' => 'El nombre de usuario ya existe.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ]);

        User::create([
            'username' => $data['username'],
            'password' => $data['password'],
            'id_rol' => (int) $data['id_rol'],
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario registrado exitosamente.')
            ->with('icono', 'success');
    }

    // CU01: Abre formulario de edicion de credenciales y rol.
    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Rol::cases(),
        ]);
    }

    // CU01: Actualiza username, rol y opcionalmente la contrasena.
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore($usuario->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'id_rol' => ['required', Rule::in(array_column(Rol::cases(), 'value'))],
        ], [
            'username.unique' => 'El nombre de usuario ya existe.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ]);

        $usuario->username = $data['username'];
        $usuario->id_rol = (int) $data['id_rol'];

        if (!empty($data['password'])) {
            $usuario->password = $data['password'];
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU01: Elimina usuarios protegiendo el propio usuario, el ultimo admin y relaciones existentes.
    public function destroy(User $usuario)
    {
        if ((int) Auth::id() === (int) $usuario->id_user) {
            return redirect()->route('admin.usuarios.index')
                ->with('mensaje', 'No puedes eliminar el usuario con el que iniciaste sesion.')
                ->with('icono', 'error');
        }

        if ($usuario->rol()->isAdmin() && User::where('id_rol', Rol::ADMIN->value)->count() <= 1) {
            return redirect()->route('admin.usuarios.index')
                ->with('mensaje', 'No se puede eliminar el unico usuario Administrador.')
                ->with('icono', 'error');
        }

        if ($this->tieneRegistrosRelacionados($usuario)) {
            return redirect()->route('admin.usuarios.index')
                ->with('mensaje', 'No se puede eliminar. El usuario esta vinculado a registros del sistema.')
                ->with('icono', 'error');
        }

        try {
            $usuario->delete();
        } catch (QueryException $e) {
            return redirect()->route('admin.usuarios.index')
                ->with('mensaje', 'No se puede eliminar el usuario porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario eliminado exitosamente.')
            ->with('icono', 'success');
    }

    // CU01: Evita romper vinculos existentes con modulos que ya administran sus propios usuarios.
    private function tieneRegistrosRelacionados(User $usuario): bool
    {
        foreach (['alumno', 'profesor', 'apoderado', 'secretaria', 'bitacora'] as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'id_user')) {
                if (DB::table($tabla)->where('id_user', $usuario->id_user)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }
}
