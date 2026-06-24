<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Apoderado;
use App\Models\PersonalAdministrativo;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// CU08: Controlador para gestionar el perfil del usuario autenticado.
class PerfilController extends Controller
{
    // CU08: Muestra los datos actuales del usuario logueado.
    public function show(Request $request)
    {
        $usuario = $request->user();
        $perfil = $this->resolverPerfil($usuario);
        $puedeEditarUsername = $this->puedeEditarUsername($usuario, $perfil);

        return view('admin.perfil.index', compact('usuario', 'perfil', 'puedeEditarUsername'));
    }

    // CU08: Actualiza datos personales permitidos y datos de acceso editables.
    public function update(Request $request)
    {
        $usuario = $request->user();
        $perfil = $this->resolverPerfil($usuario);

        $data = $request->validate($this->reglasPerfil($usuario, $perfil));

        DB::transaction(function () use ($usuario, $perfil, $data) {
            if ($this->puedeEditarUsername($usuario, $perfil) && array_key_exists('username', $data)) {
                $usuario->username = $data['username'];
                $usuario->save();
            }

            if ($perfil['registro']) {
                $perfil['registro']->fill($this->datosPersonalesPermitidos($data, $perfil['tipo']))->save();
            }
        });

        return redirect()->route('profile')
            ->with('mensaje', 'Perfil actualizado exitosamente')
            ->with('icono', 'success');
    }

    // CU08: Cambia la contrasena verificando la contrasena actual.
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.current_password' => 'Contrasena actual incorrecta',
            'new_password.confirmed' => 'Las contrasenas no coinciden',
        ]);

        $usuario = $request->user();
        $usuario->password = Hash::make($data['new_password']);
        $usuario->save();

        return redirect()->route('profile')
            ->with('mensaje', 'Contrasena actualizada exitosamente')
            ->with('icono', 'success');
    }

    // CU08: Ubica el registro personal asociado al usuario segun su rol.
    private function resolverPerfil($usuario): array
    {
        $rol = Rol::tryFrom((int) $usuario->id_rol);

        return match ($rol) {
            Rol::PROFESOR => [
                'tipo' => 'profesor',
                'titulo' => 'Docente',
                'registro' => $this->resolverPorUsuarioOPatron(Profesor::class, 'profesor', 'id_profesor', $usuario),
            ],
            Rol::SECRETARIA => [
                'tipo' => 'secretaria',
                'titulo' => 'Personal administrativo',
                'registro' => $this->resolverPorUsuarioOPatron(PersonalAdministrativo::class, 'secretaria', 'id_secretaria', $usuario),
            ],
            Rol::ALUMNO => [
                'tipo' => 'alumno',
                'titulo' => 'Estudiante',
                'registro' => $this->resolverPorUsuarioOPatron(Alumno::class, 'alumno', 'id_alumno', $usuario),
            ],
            Rol::APODERADO => [
                'tipo' => 'apoderado',
                'titulo' => 'Tutor',
                'registro' => $this->resolverApoderado($usuario->username),
            ],
            default => [
                'tipo' => 'usuario',
                'titulo' => $rol?->label() ?? 'Usuario',
                'registro' => null,
            ],
        };
    }

    // CU08: Resuelve perfiles por id_user y cae al username tecnico tipo rol_ID.
    private function resolverPorUsuarioOPatron(string $modelo, string $prefijo, string $llave, $usuario): ?object
    {
        $instancia = new $modelo();

        if (Schema::hasColumn($instancia->getTable(), 'id_user')) {
            $registro = $modelo::where('id_user', $usuario->id_user)->first();

            if ($registro) {
                return $registro;
            }
        }

        if (preg_match('/^' . preg_quote($prefijo, '/') . '_(\d+)$/', $usuario->username, $matches)) {
            return $modelo::where($llave, (int) $matches[1])->first();
        }

        return null;
    }

    // CU08: Los tutores se vinculan por el username apoderado_ID.
    private function resolverApoderado(string $username): ?Apoderado
    {
        $usuario = \App\Models\User::where('username', $username)
            ->where('id_rol', \App\Enums\Rol::APODERADO->value)
            ->first();

        if ($usuario) {
            $apoderado = Apoderado::where('id_user', $usuario->id_user)->first();

            if ($apoderado) {
                return $apoderado;
            }
        }

        if (preg_match('/^apoderado_(\d+)$/', $username, $matches)) {
            return Apoderado::find((int) $matches[1]);
        }

        return null;
    }

    // CU08: Define validaciones sin permitir rol, CI ni fecha de nacimiento.
    private function reglasPerfil($usuario, array $perfil): array
    {
        $reglas = [];

        if ($this->puedeEditarUsername($usuario, $perfil)) {
            $reglas['username'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('usuario', 'username')->ignore($usuario->id_user, 'id_user'),
            ];
        }

        return $reglas + match ($perfil['tipo']) {
            'profesor' => [
                'direccion' => ['nullable', 'string', 'max:100'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'correo' => ['nullable', 'email', 'max:100'],
            ],
            'secretaria' => [
                'direccion' => ['nullable', 'string', 'max:100'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'correo' => ['nullable', 'email', 'max:100'],
            ],
            'apoderado' => [
                'ocupacion' => ['nullable', 'string', 'max:50'],
                'telefono' => ['nullable', 'string', 'max:20'],
            ],
            default => [],
        };
    }

    // CU08: Evita romper perfiles que dependen de usernames tecnicos.
    private function puedeEditarUsername($usuario, array $perfil): bool
    {
        if ($perfil['tipo'] === 'usuario') {
            return true;
        }

        return $perfil['tipo'] !== 'apoderado'
            && $perfil['registro']
            && isset($perfil['registro']->id_user)
            && (int) $perfil['registro']->id_user === (int) $usuario->id_user;
    }

    // CU08: Filtra solo campos personales permitidos por el caso de uso.
    private function datosPersonalesPermitidos(array $data, string $tipo): array
    {
        $campos = match ($tipo) {
            'profesor' => ['direccion', 'telefono', 'correo'],
            'secretaria' => ['direccion', 'telefono', 'correo'],
            'apoderado' => ['ocupacion', 'telefono'],
            default => [],
        };

        return collect($data)->only($campos)->all();
    }
}
