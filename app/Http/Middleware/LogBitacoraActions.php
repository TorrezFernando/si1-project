<?php

namespace App\Http\Middleware;

use App\Enums\Rol;
use App\Models\User;
use App\Support\BitacoraLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

// CU05: Middleware que registra acciones importantes en la bitacora.
class LogBitacoraActions
{
    // CU05: Intercepta la peticion para registrar la accion despues de ejecutarla.
    public function handle(Request $request, Closure $next): Response
    {
        // CU05: Marca inicio para medir rendimiento del registro de bitacora.
        $inicio = microtime(true);
        // CU05: Continua con la peticion original antes de registrar.
        $response = $next($request);

        // CU05: Omite bitacora si no hay usuario o la respuesta fallo.
        if (!$request->user() || $response->getStatusCode() >= 400) {
            Log::info('[PERF] LogBitacoraActions handle', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'route' => $request->route()?->getName(),
                'status' => $response->getStatusCode(),
                'resultado' => 'omitido',
            ]);

            return $response;
        }

        // CU05: Marca inicio para resolver el nombre de la accion.
        $inicioResolver = microtime(true);
        // CU05: Traduce la ruta actual a una accion legible.
        $accion = $this->resolveAction($request);

        // CU05: Registra metrica tecnica de resolucion de accion.
        Log::info('[PERF] LogBitacoraActions resolveAction', [
            'ms' => round((microtime(true) - $inicioResolver) * 1000, 2),
            'route' => $request->route()?->getName(),
            'tiene_accion' => (bool) $accion,
        ]);

        // CU05: Si la ruta tiene accion definida, se guarda en bitacora.
        if ($accion) {
            // CU05: Marca inicio de escritura en bitacora.
            $inicioBitacora = microtime(true);
            // CU05: Guarda accion del usuario autenticado.
            BitacoraLogger::log($request, $accion, $request->user());

            // CU05: Registra metrica tecnica de escritura de bitacora.
            Log::info('[PERF] LogBitacoraActions bitacora', [
                'ms' => round((microtime(true) - $inicioBitacora) * 1000, 2),
                'route' => $request->route()?->getName(),
                'accion' => $accion,
            ]);
        }

        // CU05: Registra metrica tecnica total del middleware.
        Log::info('[PERF] LogBitacoraActions handle', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'route' => $request->route()?->getName(),
            'status' => $response->getStatusCode(),
            'resultado' => $accion ? 'registrado' : 'sin_accion',
        ]);

        return $response;
    }

    // CU05: Relaciona nombres de rutas con descripciones de acciones o genera una por defecto.
    private function resolveAction(Request $request): ?string
    {
        // CU05: Obtiene el usuario autenticado.
        $user = $request->user();

        // CU05: Si no hay usuario, no se registra accion.
        if (!$user) {
            return null;
        }

        // CU05: Obtiene el nombre de la ruta actual.
        $routeName = $request->route()?->getName();
        
        // CU05 y CU01: Obtiene etiqueta del rol para describir la accion.
        $rolLabel = Rol::tryFrom((int) $user->id_rol)?->label() ?? 'Usuario';
        // CU05 y CU01: Verifica si el usuario es administrador.
        $esAdmin = (int) $user->id_rol === Rol::ADMIN->value;

        // CU05: Mapa de rutas del sistema hacia mensajes de bitacora.
        $accionEspecifica = null;
        if ($routeName) {
            $accionEspecifica = match ($routeName) {
                'home' => $rolLabel . ' accedio al panel principal',

                'admin.configuracion.index' => 'Administrador consulto la configuracion del sistema',
                'admin.configuracion.store' => 'Administrador actualizo la configuracion del sistema',
                'admin.password.edit' => 'Administrador abrio la pantalla de cambio de contrasena',
                'admin.password.update' => 'Administrador cambio su contrasena',

                'profile' => $rolLabel . ' consulto su perfil',
                'profile.update' => $rolLabel . ' actualizo su perfil',
                'profile.password' => $rolLabel . ' cambio su contrasena desde Mi Perfil',

                'admin.alumnos.index' => 'Administrador consulto el listado de alumnos',
                'admin.alumnos.create' => 'Administrador abrio el formulario de registro de alumnos',
                'admin.alumnos.store' => 'Administrador registro un alumno',
                'admin.alumnos.edit' => 'Administrador abrio la edicion de un alumno',
                'admin.alumnos.update' => 'Administrador actualizo los datos de un alumno',
                'admin.alumnos.destroy' => 'Administrador elimino un alumno',

                'admin.apoderados.index' => 'Administrador consulto el listado de tutores',
                'admin.apoderados.create' => 'Administrador abrio el registro de tutor',
                'admin.apoderados.store' => 'Administrador registro un tutor',
                'admin.apoderados.edit' => 'Administrador abrio la edicion de un tutor',
                'admin.apoderados.update' => 'Administrador actualizo un tutor',
                'admin.apoderados.destroy' => 'Administrador elimino un tutor',

                'admin.profesores.index' => 'Administrador consulto el listado de profesores',
                'admin.profesores.edit' => 'Administrador abrio la configuracion de acceso de un profesor',
                'admin.profesores.update' => 'Administrador actualizo el acceso de un profesor',

                'admin.gestiones.index' => 'Administrador consulto las gestiones',
                'admin.gestiones.create' => 'Administrador abrio el formulario de gestiones',
                'admin.gestiones.store' => 'Administrador registro una gestion',
                'admin.gestiones.edit' => 'Administrador abrio la edicion de una gestion',
                'admin.gestiones.update' => 'Administrador actualizo una gestion',
                'admin.gestiones.destroy' => 'Administrador elimino una gestion',

                'admin.nivels.index' => 'Administrador consulto los niveles',
                'admin.nivels.store' => 'Administrador registro un nivel',
                'admin.nivels.update' => 'Administrador actualizo un nivel',
                'admin.nivels.destroy' => 'Administrador elimino un nivel',

                'admin.turnos.index' => 'Administrador consulto los turnos',
                'admin.turnos.create' => 'Administrador abrio el formulario de turnos',
                'admin.turnos.store' => 'Administrador registro un turno',
                'admin.turnos.edit' => 'Administrador abrio la edicion de un turno',
                'admin.turnos.update' => 'Administrador actualizo un turno',
                'admin.turnos.destroy' => 'Administrador elimino un turno',

                'admin.bitacora.index' => 'Administrador consulto la bitacora',
                'admin.permisos.index' => 'Administrador consulto permisos por rol',
                'admin.permisos.update' => 'Administrador actualizo permisos por rol',

                'admin.fichas-medicas.index' => $rolLabel . ' consulto fichas medicas',
                'admin.fichas-medicas.create' => $rolLabel . ' abrio el registro de ficha medica',
                'admin.fichas-medicas.store' => $rolLabel . ' registro una ficha medica',
                'admin.fichas-medicas.show' => $rolLabel . ' consulto el detalle de una ficha medica',
                'admin.fichas-medicas.edit' => $rolLabel . ' abrio la edicion de una ficha medica',
                'admin.fichas-medicas.update' => $rolLabel . ' actualizo una ficha medica',
                'admin.fichas-medicas.destroy' => $rolLabel . ' elimino una ficha medica',

                'admin.notas.index' => $rolLabel . ' consulto la gestion de notas',
                'admin.notas.create' => $rolLabel . ' abrio el registro de nota',
                'admin.notas.store' => $rolLabel . ' registro una nota',
                'admin.notas.edit' => $rolLabel . ' abrio la edicion de una nota',
                'admin.notas.update' => $rolLabel . ' actualizo una nota',
                'admin.notas.destroy' => $rolLabel . ' elimino una nota',

                'admin.infraestructura.index' => $rolLabel . ' consulto la infraestructura',
                'admin.infraestructura.create' => $rolLabel . ' abrio el registro de infraestructura',
                'admin.infraestructura.store' => $rolLabel . ' registro infraestructura',
                'admin.infraestructura.edit' => $rolLabel . ' abrio la edicion de infraestructura',
                'admin.infraestructura.update' => $rolLabel . ' actualizo infraestructura',
                'admin.infraestructura.destroy' => $rolLabel . ' elimino infraestructura',

                'admin.horarios.index' => $rolLabel . ' consulto la gestion de horarios',
                'admin.horarios.create' => $rolLabel . ' abrio el registro de horario',
                'admin.horarios.store' => $rolLabel . ' registro un horario',
                'admin.horarios.edit' => $rolLabel . ' abrio la edicion de un horario',
                'admin.horarios.update' => $rolLabel . ' actualizo un horario',
                'admin.horarios.destroy' => $rolLabel . ' elimino un horario',

                'profesor.horario' => $esAdmin
                    ? 'Administrador consulto el horario de profesores'
                    : 'Profesor consulto su horario',

                'apoderado.consulta' => $esAdmin
                    ? 'Administrador consulto las notas de los alumnos'
                    : 'Apoderado consulto las notas de sus hijos',

                'admin.reportes.index' => $rolLabel . ' consulto el modulo de reportes',
                'admin.reportes.generar' => $rolLabel . ' genero un reporte en el sistema',
                'admin.reportes.exportar' => $rolLabel . ' exporto un reporte en formato ' . strtoupper($request->input('formato', 'impreso')),

                default => null,
            };
        }

        // CU05: Retorna la accion si esta en el mapa de rutas.
        if ($accionEspecifica) {
            return $accionEspecifica;
        }

        // CU05: Si no es una ruta conocida, interpretamos la URL para generar un mensaje en lenguaje sencillo.
        $metodo = $request->method();
        $segmentos = $request->segments();
        
        // Filtramos segmentos que no aportan al lenguaje sencillo (ej: admin, api, ids numericos, o acciones tipicas de CRUD)
        $segmentosUtiles = array_filter($segmentos, function($seg) {
            return !is_numeric($seg) && !in_array(strtolower($seg), ['admin', 'api', 'create', 'edit', 'store', 'update', 'destroy', 'show']);
        });
        
        $seccion = 'una sección del sistema';
        if (!empty($segmentosUtiles)) {
            // Tomamos el primer segmento útil como el nombre de la sección (ej: "alumnos")
            $seccion = str_replace('-', ' ', reset($segmentosUtiles));
        }

        // Generamos el mensaje basado en el metodo HTTP
        $accionGenerada = match ($metodo) {
            'GET' => "{$rolLabel} consultó la sección de {$seccion}",
            'POST' => "{$rolLabel} registró un nuevo elemento en {$seccion}",
            'PUT', 'PATCH' => "{$rolLabel} actualizó un registro en {$seccion}",
            'DELETE' => "{$rolLabel} eliminó un registro en {$seccion}",
            default => "{$rolLabel} interactuó con la sección de {$seccion}"
        };

        // Refinamos si detectamos que abrio un formulario especifico por GET
        if ($metodo === 'GET') {
            if (in_array('create', $segmentos)) {
                $accionGenerada = "{$rolLabel} abrió el formulario de registro en {$seccion}";
            } elseif (in_array('edit', $segmentos)) {
                $accionGenerada = "{$rolLabel} abrió la edición de un registro en {$seccion}";
            }
        }

        // Limitamos a 255 caracteres para asegurar que entre en el campo de base de datos
        if (mb_strlen($accionGenerada) > 255) {
            $accionGenerada = mb_substr($accionGenerada, 0, 252) . '...';
        }

        return $accionGenerada;
    }
}
