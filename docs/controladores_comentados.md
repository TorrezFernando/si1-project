# Controladores Comentados Del Proyecto

Este documento explica los controladores ubicados en `app/Http/Controllers`.

Nota de lectura: se comentan las lineas funcionales y las librerias/imports. Las llaves `{}`, `}`, lineas en blanco y cierres de arreglos se agrupan cuando solo cumplen funcion estructural, para que el documento sirva para estudiar el codigo sin repetir cientos de veces "cierra el bloque".

## Conceptos Base

`namespace ...;`: indica en que espacio de nombres vive la clase. Laravel usa esto para cargar clases automaticamente.

`use ...;`: importa una clase, enum, facade, modelo, servicio o regla para poder usarla con su nombre corto dentro del controlador.

`extends Controller`: hace que el controlador herede de la clase base de controladores de Laravel.

`Request $request`: objeto que contiene datos de la peticion HTTP: formularios, query params, archivos, usuario autenticado, etc.

`view(...)`: devuelve una vista Blade.

`redirect()->route(...)`: redirecciona a una ruta nombrada.

`with('mensaje', ...)` y `with('icono', ...)`: guardan mensajes flash para mostrarlos en la siguiente pantalla.

`validate(...)`: valida los datos que llegan desde el formulario. Si falla, Laravel redirige atras con errores.

`findOrFail(...)`: busca un registro por ID; si no existe, lanza error 404.

`paginate(...)`: pagina resultados para no cargar todo de golpe.

`DB::table(...)`: usa el query builder de Laravel para consultas directas a tablas.

`Auth::user()` / `$request->user()`: obtiene el usuario autenticado.

`Gate` / `can:...`: controla permisos por rol o funcionalidad.

---

## app/Http/Controllers/Controller.php

Controlador base del proyecto.

- `namespace App\Http\Controllers;`: ubica la clase en el namespace comun de controladores.
- `abstract class Controller`: define una clase base abstracta. No se instancia directamente.
- Las demas clases heredan de esta para mantener la estructura Laravel.

---

## app/Http/Controllers/HomeController.php

Controlador del panel principal.

**Librerias**

- `AuthService`: servicio que decide redirecciones segun rol.
- `Controller`: clase base.
- `Request`: acceso a usuario y datos de peticion.
- `Auth`: facade de autenticacion.

**Funcionamiento**

- `__construct(...)`: inyecta `AuthService` y aplica middleware `auth`, por eso solo usuarios logueados entran.
- `index(Request $request)`: punto de entrada del panel.
- Obtiene el usuario autenticado con `$request->user()` o `Auth::user()`.
- Consulta `AuthService` para saber si el usuario debe ir al horario de profesor, consulta de apoderado o panel general.
- Devuelve vista del panel o redireccion segun rol.

**Idea clave:** este controlador no administra datos; decide a donde mandar al usuario despues del login.

---

## app/Http/Controllers/GestionController.php

Controlador de CU22: Gestionar Año Escolar.

**Librerias**

- `Controller`: base Laravel.
- Modelos academicos relacionados con gestion escolar.
- `Request`: datos del formulario.
- `QueryException`: captura errores de integridad referencial.

**Funciones**

- `index()`: lista gestiones registradas.
- Usa consultas ordenadas para mostrar anios escolares.
- Devuelve `admin.gestiones.index`.
- `create()`: abre formulario de nueva gestion.
- `store(Request $request)`: valida nombre, fecha inicio, fecha fin y estado.
- Crea la gestion en base de datos.
- Redirige al listado con mensaje de exito.
- `edit($id)`: busca una gestion por ID y muestra formulario.
- `update(Request $request, $id)`: valida y actualiza datos.
- `activar($id)`: desactiva otras gestiones y activa solo una.
- `destroy($id)`: intenta eliminar; si tiene relaciones, captura error y muestra mensaje.

**Idea clave:** representa el anio escolar y evita que haya varias gestiones activas al mismo tiempo.

---

## app/Http/Controllers/ConfiguracionController.php

Controlador raiz de configuracion institucional. Ojo: aunque esta en carpeta raiz, su namespace apunta a `App\Http\Controllers\Admin`.

**Librerias**

- `Controller`: base.
- `Request`: peticion/formulario.
- Usa `\App\Models\Configuracion` con nombre completo, sin importarlo arriba.

**Funcionamiento linea por linea funcional**

- `index()`: devuelve la vista `admin.configuracion.index`.
- `store(Request $request)`: recibe datos de configuracion institucional.
- `Configuracion::first()`: obtiene la primera configuracion existente.
- `if ($configuracion)`: si ya existe, la actualiza.
- Asigna campos: `nombre`, `descripcion`, `direccion`, `telefono`, `divisa`, `web`, `correo_electronico`.
- `hasFile('logo')`: detecta si se subio un logo nuevo.
- Si habia logo anterior y el archivo existe, usa `unlink(...)` para borrarlo.
- Obtiene el archivo con `$request->file('logo')`.
- Genera nombre con `time()` mas nombre original.
- Mueve archivo a `public/uploads/logos`.
- Guarda ruta relativa en `$configuracion->logo`.
- `save()`: persiste cambios.
- Redirige con mensaje de configuracion actualizada.
- Si no existe configuracion, crea una nueva instancia y repite asignacion de campos.

**Idea clave:** mantiene datos generales del colegio y administra el logo.

---

## app/Http/Controllers/Admin/ConfiguracionController.php

Controlador administrativo de configuracion. Cumple la misma funcion general que el controlador anterior, pero dentro de `Admin`.

**Librerias**

- `Controller`, `Request`.
- Modelo `Configuracion`.

**Funciones**

- `index()`: muestra configuracion actual.
- `store(Request $request)`: crea o actualiza configuracion.
- Valida/recibe campos institucionales.
- Maneja subida de logo.
- Guarda la configuracion.
- Redirige con mensaje flash.

**Detalle importante:** existe duplicidad conceptual con `app/Http/Controllers/ConfiguracionController.php`. Si se refactoriza, convendria dejar solo uno.

---

## app/Http/Controllers/Admin/AlumnoController.php

Controlador de CU03 y parte de CU01: Gestionar Estudiante y usuario de acceso.

**Librerias**

- `AlumnoService`: concentra creacion/actualizacion/eliminacion de alumno junto con usuario.
- `Controller`: base.
- `Alumno`: modelo de estudiantes.
- `QueryException`: captura errores de BD al eliminar.
- `Request`: datos del formulario.
- `Rule`: reglas avanzadas de validacion, por ejemplo `unique` ignorando el registro actual.

**Funciones**

- `__construct(AlumnoService $alumnoService)`: inyecta el servicio y lo guarda como dependencia.
- `index()`: carga alumnos con su relacion `usuario`.
- Ordena por apellido paterno, materno y nombres.
- Pagina de 15 en 15.
- Devuelve `admin.alumnos.index`.
- `create()`: devuelve `admin.alumnos.create`.
- `store(Request $request)`: valida CI, nombres, apellidos, genero, fecha nacimiento, username y password.
- `Rule::in(['F', 'M'])`: limita genero a F o M.
- `unique:usuario,username`: evita username duplicado.
- Llama `crearConUsuario($data)` para crear alumno y credenciales en una transaccion.
- Redirige con exito.
- `edit($id)`: busca alumno con usuario asociado.
- Devuelve vista de edicion.
- `update(Request $request, $id)`: busca alumno, valida datos y username unico ignorando el usuario actual.
- Llama `actualizarConUsuario(...)`.
- `destroy($id)`: busca alumno con usuario.
- Intenta eliminar mediante servicio.
- Si hay registros relacionados, captura `QueryException` y muestra error.

**Idea clave:** el controlador no hace toda la logica; delega la consistencia alumno-usuario al servicio de dominio.

---

## app/Http/Controllers/Admin/ApoderadoController.php

Controlador de CU04 y parte de CU01: Gestionar Tutor/Apoderado y su usuario.

**Librerias**

- `Rol`: enum de roles.
- `Controller`: base.
- `Alumno`, `Apoderado`, `User`: modelos relacionados.
- `DB`: transacciones y consultas directas.
- `Hash`: encripta contrasenas.
- `Request`: datos de formulario.
- `Rule`: validacion avanzada.
- `QueryException`: captura restricciones de BD.

**Funciones principales**

- `index(Request $request)`: lista apoderados.
- Usa `with(['alumnos', 'usuario'])` para traer relaciones.
- Si hay busqueda, filtra por datos personales, username o alumnos vinculados.
- Pagina resultados y devuelve `admin.apoderados.index`.
- `create()`: carga alumnos disponibles para relacionar con el tutor.
- `store(Request $request)`: valida datos personales, username, password y alumnos.
- Usa `DB::transaction(...)` para crear usuario, apoderado y parentescos juntos.
- Crea usuario con rol `Rol::APODERADO`.
- Hashea password con `Hash::make`.
- Crea apoderado y asigna `id_user`.
- Sincroniza relacion con alumnos.
- Redirige con exito.
- `edit(Apoderado $apoderado)`: carga alumnos y usuario asociado.
- `update(...)`: valida datos, actualiza usuario si existe o lo crea si faltaba.
- Mantiene rol de apoderado.
- Actualiza datos personales.
- Sincroniza alumnos vinculados.
- `destroy(...)`: elimina relacion de parentesco, apoderado y usuario si corresponde.

**Idea clave:** usa transacciones para no dejar usuario creado sin apoderado o apoderado sin usuario.

---

## app/Http/Controllers/Admin/BitacoraController.php

Controlador de CU05: consulta de bitacora.

**Librerias**

- `Rol`: enum para listar/filtrar roles.
- `Controller`: base.
- `Bitacora`: modelo de registros de acciones.
- `Request`: filtros de busqueda.

**Funciones**

- `index(Request $request)`: recibe filtros de texto y rol.
- Carga bitacoras con `usuario`.
- Si hay busqueda, filtra por accion, IP o username.
- Si hay rol, filtra por rol del usuario.
- Ordena por fecha descendente.
- Pagina resultados.
- Devuelve vista `admin.bitacora.index`.

**Idea clave:** este controlador solo consulta. La escritura de bitacora ocurre en middleware y `BitacoraLogger`.

---

## app/Http/Controllers/Admin/CambiarPasswordController.php

Controlador de CU08: cambio de contrasena desde configuracion.

**Librerias**

- `Controller`.
- `Request`.
- `Hash`: verifica y encripta passwords.

**Funciones**

- `edit()`: muestra formulario para cambiar contrasena.
- `update(Request $request)`: valida password actual, nueva password y confirmacion.
- Obtiene usuario autenticado con `$request->user()`.
- Verifica password actual con `Hash::check`.
- Si no coincide, vuelve con error.
- Encripta nueva contrasena con `Hash::make`.
- Guarda usuario.
- Redirige con mensaje de exito.

---

## app/Http/Controllers/Admin/CursoController.php

Controlador de CU12: Gestionar Curso.

**Librerias**

- Modelos de curso, nivel, paralelo, turno y gestion academica.
- `Controller`, `Request`.
- `Rule` para validar combinaciones unicas.
- `QueryException` para errores al eliminar.

**Funciones**

- `index(Request $request)`: lista cursos con relaciones utiles.
- Permite busqueda por nombre, nivel, paralelo o turno.
- `create()`: carga niveles, paralelos y turnos para selectores.
- `store(Request $request)`: valida datos del curso.
- Crea curso.
- `edit($id)`: busca curso y carga catalogos.
- `update(Request $request, $id)`: valida y actualiza curso.
- `destroy($id)`: intenta borrar y controla restricciones de relaciones.

**Idea clave:** curso funciona como estructura academica, generalmente asociada a gestion, nivel, paralelo y turno.

---

## app/Http/Controllers/Admin/FichaMedicaController.php

Controlador de CU23: Gestionar Ficha Medica.

**Librerias**

- `Alumno`, `FichaMedica`: modelos.
- `Controller`, `Request`.
- `Rule`, `QueryException`.

**Funciones**

- `index(Request $request)`: lista fichas medicas con alumno.
- Filtra por estudiante o datos medicos.
- `create()`: carga alumnos para seleccionar.
- `store(Request $request)`: valida datos medicos y alumno.
- Guarda ficha.
- `show(FichaMedica $ficha)`: muestra detalle.
- `edit(FichaMedica $ficha)`: muestra formulario de edicion.
- `update(...)`: valida y actualiza ficha.
- `destroy(...)`: elimina si no hay restricciones.

**Idea clave:** vincula informacion medica a estudiantes.

---

## app/Http/Controllers/Admin/FuncionalidadController.php

Controlador de CU09: Gestionar Funcionalidad.

**Librerias**

- `Controller`.
- `Funcionalidad`: modelo de permisos/acciones del sistema.
- `Modulo`: modelo al que pertenece cada funcionalidad.
- `Request`.
- `QueryException`.

**Funciones**

- `index(Request $request)`: lista funcionalidades con su modulo.
- Busca por nombre, descripcion o modulo.
- `create()`: carga modulos para selector.
- `store(Request $request)`: valida nombre, descripcion e `id_modulo`.
- `exists:modulos,id_modulo`: obliga a escoger un modulo real.
- Crea funcionalidad.
- `edit(Funcionalidad $funcionalidad)`: carga funcionalidad y modulos.
- `update(...)`: valida y actualiza.
- `destroy(...)`: intenta eliminar; si esta asignada a roles, muestra error.

**Idea clave:** las funcionalidades son permisos que luego se asignan a roles.

---

## app/Http/Controllers/Admin/HorarioController.php

Controlador de CU14: Gestionar Horario.

**Librerias**

- Modelos academicos: aulas, cursos, gestiones, horarios, materias, profesores, paralelos.
- `HorarioService`: servicio para consultas de horario.
- `Controller`, `Request`, `DB`, `Rule`, `QueryException`.

**Funciones principales**

- `index(Request $request)`: lista asignaciones de horario con filtros.
- Carga relaciones para mostrar materia, curso, gestion, aula, dia y hora.
- `create()`: carga catalogos necesarios: materias, cursos, gestiones, profesores, aulas, paralelos y horarios.
- `store(Request $request)`: valida que existan materia, curso, gestion, profesor, paralelo, aula y horario.
- Usa transaccion o consultas para crear la asignacion.
- Controla duplicados/conflictos.
- `edit(...)`: localiza una asignacion compuesta por materia, gestion, curso y paralelo.
- `update(...)`: valida cambios y actualiza asignacion.
- `destroy(...)`: elimina asignacion de horario y controla relaciones.

**Idea clave:** este controlador maneja claves compuestas; por eso varias rutas tienen muchos parametros.

---

## app/Http/Controllers/Admin/InfraestructuraController.php

Controlador de CU20: Gestionar Infraestructura.

**Librerias**

- Modelo de infraestructura/aula.
- `Controller`, `Request`, `Rule`, `QueryException`.

**Funciones**

- `index(Request $request)`: lista ambientes/aulas.
- Filtra por nombre, tipo o descripcion.
- `create()`: formulario nuevo.
- `store(Request $request)`: valida y crea.
- `edit(...)`: formulario de edicion.
- `update(...)`: valida y guarda cambios.
- `destroy(...)`: elimina si no esta relacionada con horarios u otros registros.

**Idea clave:** gestiona espacios fisicos del colegio.

---

## app/Http/Controllers/Admin/MateriaController.php

Controlador de CU13: Gestionar Materia.

**Librerias**

- `CampoSaberes`, `Materia`: modelos.
- `Controller`, `Request`.
- `Rule`, `QueryException`.

**Funciones**

- `index(Request $request)`: lista materias con campo de saberes.
- Busca por nombre, carga horaria o distintivo.
- `create()`: carga campos de saberes.
- `store(Request $request)`: valida nombre unico, carga horaria, distintivo y campo.
- Crea materia.
- `edit($id)`: busca materia y carga campos.
- `update(...)`: valida nombre unico ignorando actual.
- `destroy($id)`: intenta borrar; si esta en notas/horarios/asignaciones, muestra error.

---

## app/Http/Controllers/Admin/MatriculaController.php

Controlador de CU11: Gestionar Matricula.

**Librerias**

- Modelos de alumno, curso, gestion, matricula, mensualidad, pagos y relaciones academicas.
- `Controller`, `Request`, `DB`, `Rule`, `QueryException`.

**Funciones principales**

- `index(Request $request)`: lista matriculas/inscripciones con filtros.
- Carga estudiante, curso, gestion y estado.
- `create()`: carga alumnos, cursos y gestiones disponibles.
- `store(Request $request)`: valida datos de inscripcion.
- Usa transaccion para registrar matricula, inscripcion y curso/gestion.
- Puede generar datos financieros asociados.
- `edit(...)`: abre formulario de edicion.
- `update(...)`: actualiza curso, gestion, estado u otros datos permitidos.
- `cambiarEstado(...)`: cambia estado de la matricula.
- `destroy(...)`: elimina si no tiene pagos, notas u obligaciones vinculadas.

**Idea clave:** matricula conecta al estudiante con curso y gestion; por eso tiene impacto academico y financiero.

---

## app/Http/Controllers/Admin/MensualidadController.php

Controlador de CU18: Gestionar Mensualidad.

**Librerias**

- Modelos de pago mensual, matricula, alumno, gestion y pagos.
- `Controller`, `Request`, `DB`, `Rule`, `QueryException`.

**Funciones**

- `index(Request $request)`: consulta mensualidades con filtros por alumno, curso, mes, gestion o estado.
- `create()`: prepara datos para generar obligaciones.
- `store(Request $request)`: genera mensualidades para estudiantes matriculados.
- Valida gestion, curso, mes y monto.
- Evita duplicados de mensualidad.
- `registrarPago(...)`: registra pago de mensualidad.
- Cambia estado segun pago.
- Redirige con mensajes de exito/error.

**Idea clave:** genera y controla obligaciones mensuales de pago.

---

## app/Http/Controllers/Admin/ModuloController.php

Controlador de CU10: Gestionar Modulo.

**Librerias**

- `Modulo`: modelo que representa modulos del sistema.
- `QueryException`: errores de BD.
- `Request`: filtros y datos de formulario.

**Funciones**

- `index(Request $request)`: lista modulos.
- `$search = trim(...)`: limpia texto de busqueda.
- `Modulo::withCount('funcionalidades')`: trae cantidad de funcionalidades asociadas.
- `when($search, ...)`: aplica filtro solo si hay busqueda.
- Busca por `nombre` o `descripcion`.
- Ordena por nombre.
- Pagina 15 resultados.
- Devuelve `admin.modulos.index`.
- `create()`: devuelve formulario.
- `store(Request $request)`: valida nombre obligatorio, maximo 255 y unico.
- `descripcion`: opcional, maximo 1000.
- `Modulo::create($data)`: guarda.
- `edit(Modulo $modulo)`: recibe modelo por route model binding.
- `update(...)`: valida nombre unico ignorando el modulo actual.
- `$modulo->update($data)`: actualiza.
- `destroy(Modulo $modulo)`: verifica si tiene funcionalidades.
- Si tiene funcionalidades, no elimina.
- Si no tiene, intenta borrar.
- Captura `QueryException` si la base de datos bloquea por relaciones.

**Idea clave:** un modulo agrupa funcionalidades usadas luego para permisos por rol.

---

## app/Http/Controllers/Admin/NivelController.php

Controlador auxiliar de CU12: niveles academicos.

**Librerias**

- `GestionAcademicaService`: servicio de operaciones academicas.
- `Nivel`: modelo.
- `Controller`, `Request`.

**Funciones**

- `__construct(...)`: inyecta servicio.
- `index()`: lista niveles desde el servicio.
- `store(Request $request)`: valida nombre unico y crea nivel.
- `update(Request $request, $id)`: valida y actualiza.
- `destroy($id)`: elimina si no esta relacionado con cursos.

---

## app/Http/Controllers/Admin/NotaController.php

Controlador de CU15: Gestionar Nota.

**Librerias**

- Modelos academicos: alumno, materia, curso, gestion, trimestre, nota.
- Servicios de notas.
- `Controller`, `Request`, `DB`, `Rule`, `QueryException`.

**Funciones principales**

- `index(Request $request)`: lista notas con filtros.
- Construye consultas por alumno, materia, curso, gestion y trimestre.
- `create()`: carga catalogos necesarios.
- `store(Request $request)`: valida calificaciones.
- Reglas evitan duplicar nota por alumno/materia/curso/gestion/trimestre.
- Guarda nota.
- `edit(...)`: usa clave compuesta para ubicar nota.
- `update(...)`: valida y actualiza calificaciones.
- `destroy(...)`: elimina registro de nota.

**Idea clave:** trabaja con claves compuestas porque una nota depende de varios identificadores.

---

## app/Http/Controllers/Admin/PagoController.php

Controlador de CU17: Gestionar Pago.

**Librerias**

- Modelos/consultas de matricula, mensualidad y pagos.
- `Controller`, `Request`, `DB`, `Rule`, `QueryException`.

**Funciones principales**

- `index(Request $request)`: lista pagos y permite filtros.
- `create()`: muestra formulario para registrar pago.
- `store(Request $request)`: valida monto, tipo y referencia.
- Registra pago.
- Actualiza estado de obligacion relacionada si corresponde.
- `edit($tipo, $referencia)`: ubica pago por tipo/referencia.
- `update(...)`: actualiza datos de pago.
- `anular(...)`: marca pago como anulado o cambia estado.

**Idea clave:** centraliza pagos de distintos conceptos, como matricula o mensualidad.

---

## app/Http/Controllers/Admin/PerfilController.php

Controlador de CU08: Gestionar Perfil.

**Librerias**

- `Rol`: enum de roles.
- Modelos: alumno, apoderado, profesor, personal administrativo, user.
- `Controller`, `Request`, `DB`, `Hash`, `Rule`.

**Funciones**

- `show(Request $request)`: obtiene usuario autenticado.
- `resolverPerfil($usuario)`: busca el registro personal asociado segun rol.
- Calcula si puede editar username.
- Devuelve `admin.perfil.index`.
- `update(Request $request)`: valida datos permitidos.
- Usa transaccion para actualizar usuario y datos personales.
- `updatePassword(Request $request)`: valida contrasena actual y nueva.
- `Hash::check`: comprueba password actual.
- `Hash::make`: encripta nueva.
- Metodos privados resuelven perfil por `id_user` o por username tecnico tipo `profesor_1`.

**Idea clave:** permite al usuario modificar su perfil sin tocar campos sensibles como rol, CI o fecha de nacimiento.

---

## app/Http/Controllers/Admin/PermisoRolController.php

Controlador de permisos por rol. Relacionado con CU09 y CU10.

**Librerias**

- `Rol`: enum.
- `Modulo`: carga modulos y funcionalidades.
- `Controller`, `Request`, `DB`.

**Funciones**

- `index(Request $request)`: muestra matriz de permisos.
- Carga roles del enum.
- Carga modulos con funcionalidades.
- Obtiene funcionalidades asignadas al rol seleccionado.
- `update(Request $request, int $rol)`: valida funcionalidades enviadas.
- Si el rol es administrador, mantiene acceso total.
- Usa tabla pivote `rol_funcionalidad`.
- Borra asignaciones anteriores y registra nuevas.

**Idea clave:** une roles con funcionalidades para el control dinamico de permisos.

---

## app/Http/Controllers/Admin/PersonalAdministrativoController.php

Controlador de CU24 y CU01: Gestionar Personal Administrativo y usuario.

**Librerias**

- `Rol`: enum.
- `PersonalAdministrativo`, `User`: modelos.
- `Controller`, `Request`, `DB`, `Hash`, `Rule`, `QueryException`.

**Funciones**

- `index(Request $request)`: lista personal administrativo con usuario.
- Filtra por CI, nombre, apellido, cargo, area o username.
- `create()`: formulario nuevo.
- `store(Request $request)`: valida datos personales y credenciales.
- Usa transaccion para crear usuario y secretaria/personal juntos.
- Crea usuario con rol secretaria.
- `edit(...)`: carga personal y usuario.
- `update(...)`: actualiza datos personales y credenciales.
- Crea usuario si un registro antiguo no tenia.
- `destroy(...)`: elimina personal y usuario asociado si no hay dependencias.
- Metodo privado centraliza reglas de validacion.

---

## app/Http/Controllers/Admin/ProfesorController.php

Controlador de CU02 y CU01: Gestionar Docente y usuario.

**Librerias**

- `ProfesorService`: logica compleja de docente.
- `Rol`: enum.
- `Profesor`, `User`, modelos de permisos.
- `Controller`, `Request`, `DB`, `Hash`, `Rule`, `QueryException`.

**Funciones principales**

- `__construct(...)`: inyecta servicio.
- `index(Request $request)`: lista docentes con usuario y permiso.
- `create()`: formulario.
- `store(Request $request)`: valida datos personales, username y password.
- Crea usuario con rol profesor.
- Crea profesor vinculado a `id_user`.
- Crea permiso inicial para horario.
- `edit($id)`: edita credenciales/acceso del docente.
- `update(...)`: actualiza username, password opcional y permiso.
- `editInfo($id)`: formulario para informacion personal.
- `updateInfo(...)`: actualiza datos personales.
- `destroy($id)`: elimina docente usando servicio para limpiar relaciones.

**Idea clave:** separa edicion de acceso y edicion de informacion personal.

---

## app/Http/Controllers/Admin/ReporteController.php

Controlador de CU21: Generar Reporte.

**Librerias**

- `Controller`, `Request`, `DB`.
- Modelos/consultas relacionados con reportes.

**Funciones**

- `index()`: muestra pantalla de reportes disponibles.
- `generar(Request $request)`: recibe tipo de reporte y filtros.
- Usa un mapa de tipos para decidir que metodo privado ejecutar.
- Cada metodo privado arma una consulta especifica.
- Reportes incluyen usuarios, bitacora, alumnos, docentes, pagos, notas y otros.
- `exportar(Request $request)`: prepara salida para exportacion o impresion.
- Metodos privados usan `DB::table(...)`, `join(...)`, `select(...)`, `where(...)`, `orderBy(...)`.

**Idea clave:** es un controlador grande porque centraliza varios reportes administrativos.

---

## app/Http/Controllers/Admin/ReporteEstaticoController.php

Controlador de reportes estaticos, principalmente boletines/consulta de notas por rol.

**Librerias**

- `Rol`: enum.
- `Controller`, `Request`, `Auth`, `DB`.

**Funciones**

- `__construct()`: aplica middleware `auth`.
- `index(Request $request)`: obtiene usuario y rol.
- Segun rol, prepara filtros permitidos.
- Admin/secretaria pueden ver mas datos.
- Profesor solo ve cursos/materias asignadas.
- Apoderado solo ve hijos vinculados.
- Construye consulta de notas con joins.
- Agrupa datos para boletin cuando corresponde.
- Devuelve vista `admin.reportes-estaticos.index`.

**Idea clave:** aplica seguridad por rol directamente en las consultas.

---

## app/Http/Controllers/Admin/TurnoController.php

Controlador auxiliar de CU12: turnos.

**Librerias**

- `GestionAcademicaService`: servicio.
- `Turno`: modelo.
- `Controller`, `Request`.

**Funciones**

- `index()`: lista turnos.
- `create()`: formulario nuevo.
- `store(Request $request)`: valida nombre unico y crea turno.
- `edit($id)`: busca turno; si no existe redirige con error.
- `update(...)`: valida y actualiza.
- `destroy($id)`: intenta eliminar y captura `QueryException` si tiene relaciones.

---

## app/Http/Controllers/Admin/UsuarioController.php

Controlador de CU01: Gestionar Usuario. Fue agregado como modulo independiente sin alterar los controladores existentes.

**Librerias**

- `Rol`: enum de roles disponibles.
- `Controller`: base.
- `User`: modelo de tabla `usuario`.
- `QueryException`: errores de BD.
- `Request`: datos de formulario y busqueda.
- `Auth`: usuario autenticado.
- `DB`: consultas directas para revisar relaciones.
- `Schema`: verifica si tablas/columnas existen.
- `Rule`: validacion avanzada.

**Funciones**

- `index(Request $request)`: lista usuarios.
- Lee `search` desde query string.
- Usa `User::query()` para iniciar consulta.
- `when($search, ...)`: filtra solo si hay busqueda.
- Busca por `username`, `id_user` o `id_rol`.
- Ordena por username.
- Pagina 15.
- Devuelve `admin.usuarios.index` con usuarios, roles y busqueda.
- `create()`: muestra formulario con roles.
- `store(Request $request)`: valida username unico, password confirmada y rol valido.
- `Rule::in(array_column(Rol::cases(), 'value'))`: limita rol a valores del enum.
- `User::create(...)`: crea credencial.
- `edit(User $usuario)`: usa route model binding para editar.
- `update(...)`: valida username unico ignorando usuario actual.
- Actualiza username y rol.
- Cambia password solo si se envio una nueva.
- `destroy(User $usuario)`: protege eliminacion.
- No permite borrar el usuario de la sesion actual.
- No permite borrar el ultimo administrador.
- No permite borrar usuarios vinculados a registros del sistema.
- `tieneRegistrosRelacionados(...)`: revisa tablas `alumno`, `profesor`, `apoderado`, `secretaria`, `bitacora`.
- Usa `Schema::hasTable` y `Schema::hasColumn` para evitar errores si la tabla/columna no existe.

---

## app/Http/Controllers/Apoderado/ConsultaController.php

Controlador para consulta de notas por apoderado y tambien por administrador.

**Librerias**

- `ApoderadoService`: resuelve apoderado e hijos.
- `AuthService`: verifica rol.
- `NotaService`: consulta notas.
- `Controller`, `Request`, `Auth`, `Log`.

**Funciones**

- `__construct(...)`: inyecta servicios y exige `auth`.
- `index(Request $request)`: obtiene usuario autenticado.
- Si es admin, llama `indexComoAdmin`.
- Si es apoderado, llama `indexComoApoderado`.
- Si no corresponde, aborta con 403.
- `indexComoApoderado(...)`: busca apoderado por username.
- Si no existe, devuelve vista sin datos.
- Carga hijos del apoderado.
- Lee hijo seleccionado desde request.
- Si el hijo seleccionado no pertenece al apoderado, lo descarta.
- Si no se selecciono hijo, toma el primero.
- Consulta notas filtradas por apoderado.
- Agrupa notas por hijo.
- Calcula resumen: total, promedio, mejor nota y materias.
- Registra metricas con `Log::info`.
- Devuelve vista `apoderado.consulta`.
- `indexComoAdmin(...)`: hace consulta parecida pero sobre todos los alumnos/hijos.

---

## app/Http/Controllers/Profesor/HorarioController.php

Controlador para consulta de horario del profesor.

**Librerias**

- `AuthService`: verifica rol y permisos.
- `HorarioService`: consulta horarios.
- `Controller`, `Request`, `Auth`, `Log`.

**Funciones**

- `__construct(...)`: inyecta servicios y exige autenticacion.
- `index(Request $request)`: obtiene usuario.
- Si es admin, permite escoger profesor por filtro.
- Si es profesor, resuelve su docente vinculado por `id_user` o username tecnico.
- Llama al servicio para obtener horario.
- Agrupa horario por dia.
- Carga docentes para selector si aplica.
- Registra metricas de rendimiento con `Log`.
- Devuelve vista de horario.

---

## app/Http/Controllers/Auth/LoginController.php

Controlador de CU06 y CU07: iniciar y cerrar sesion.

**Librerias**

- `AuthService`: decide destino post-login.
- `BitacoraLogger`: guarda inicio/cierre de sesion.
- `User`: modelo de usuario.
- `Controller`, traits de autenticacion Laravel.
- `Request`, `Auth`, `Hash`, `Log`.

**Funciones**

- Usa trait `AuthenticatesUsers`: Laravel aporta logica base de login.
- `__construct(...)`: middleware `guest` excepto logout y `auth` para logout.
- `username()`: indica que el login usa `username` en vez de email.
- `credentials(Request $request)`: arma credenciales.
- Logica adicional revisa password legacy y rehashea si corresponde.
- `authenticated(Request $request, $user)`: se ejecuta despues del login exitoso.
- Guarda bitacora: `Inicio de sesion`.
- Redirige segun `AuthService`.
- `redirectTo()`: fallback de redireccion.
- `logout(Request $request)`: registra cierre de sesion antes de cerrar.
- Llama `Auth::logout()`.
- Invalida sesion y regenera token CSRF.
- Redirige al login.

---

## app/Http/Controllers/Auth/AdminResetPasswordController.php

Controlador de recuperacion de contrasena para administrador.

**Librerias**

- `AdminPasswordResetMail`: correo con codigo.
- `User`: usuario.
- `Cache`: almacena codigo temporal.
- `Hash`: encripta nueva password.
- `Mail`: envia correo.
- `Password`: reglas fuertes de contrasena.

**Funciones**

- `showForgotForm()`: muestra formulario de solicitud.
- `sendResetCode(Request $request)`: valida username.
- Busca usuario administrador (`id_rol = 1`).
- Si no existe, devuelve error.
- Genera codigo de 6 digitos.
- Guarda codigo en cache por 15 minutos.
- Obtiene correo institucional desde configuracion o usa fallback.
- Envia correo con `Mail::to(...)->send(...)`.
- Si falla SMTP, devuelve error.
- Redirige al formulario para ingresar codigo.
- `showResetForm(Request $request)`: muestra formulario final.
- `resetPassword(Request $request)`: valida usuario, codigo y nueva password.
- Compara codigo enviado con cache.
- Actualiza password hasheado.
- Borra codigo de cache.
- Redirige al login o pantalla correspondiente.

---

## app/Http/Controllers/Auth/ConfirmPasswordController.php

Controlador Laravel para confirmar password en acciones sensibles.

**Librerias**

- `Controller`.
- Trait `ConfirmsPasswords`.

**Funciones**

- Define `$redirectTo = '/home'`.
- `__construct()`: aplica middleware `auth`.
- El trait aporta formulario y verificacion de password.

---

## app/Http/Controllers/Auth/ForgotPasswordController.php

Controlador Laravel de solicitud de recuperacion de password.

**Librerias**

- `Controller`.
- Trait `SendsPasswordResetEmails`.

**Funciones**

- `__construct()`: aplica `guest`.
- El trait maneja enviar enlace/correo de reseteo.

---

## app/Http/Controllers/Auth/RegisterController.php

Controlador Laravel de registro, aunque las rutas de registro estan desactivadas.

**Librerias**

- `User`.
- `Controller`.
- `Hash`.
- `Validator`.
- Trait `RegistersUsers`.

**Funciones**

- `$redirectTo = '/home'`: destino despues de registrar.
- `__construct()`: middleware guest.
- `validator(array $data)`: valida nombre, email y password.
- `create(array $data)`: crea usuario usando hash de password.

**Nota:** en `routes/web.php` se usa `Auth::routes(['register' => false])`, asi que el registro publico esta apagado.

---

## app/Http/Controllers/Auth/ResetPasswordController.php

Controlador Laravel para aplicar reset de password.

**Librerias**

- `Controller`.
- Trait `ResetsPasswords`.

**Funciones**

- `$redirectTo = '/home'`.
- El trait maneja formulario, validacion de token y cambio de password.

---

## app/Http/Controllers/Auth/VerificationController.php

Controlador Laravel para verificacion de email.

**Librerias**

- `Controller`.
- Trait `VerifiesEmails`.

**Funciones**

- `$redirectTo = '/home'`.
- `__construct()`: exige `auth`, `signed` para verificar y throttle para reenviar.
- El trait maneja verificacion de correo.

---

## Resumen Por Caso De Uso

- CU01 Usuario: `Admin/UsuarioController.php`, ademas integrado en alumno, profesor, apoderado y personal administrativo.
- CU02 Docente: `Admin/ProfesorController.php`.
- CU03 Estudiante: `Admin/AlumnoController.php`.
- CU04 Tutor: `Admin/ApoderadoController.php` y `Apoderado/ConsultaController.php`.
- CU05 Bitacora: `Admin/BitacoraController.php`; escritura en middleware y `BitacoraLogger`.
- CU06 Login: `Auth/LoginController.php`.
- CU07 Logout: `Auth/LoginController.php`.
- CU08 Perfil: `Admin/PerfilController.php`, `Admin/CambiarPasswordController.php`.
- CU09 Funcionalidad: `Admin/FuncionalidadController.php`, `Admin/PermisoRolController.php`.
- CU10 Modulo: `Admin/ModuloController.php`.
- CU11 Matricula: `Admin/MatriculaController.php`.
- CU12 Curso: `Admin/CursoController.php`, `Admin/NivelController.php`, `Admin/TurnoController.php`.
- CU13 Materia: `Admin/MateriaController.php`.
- CU14 Horario: `Admin/HorarioController.php`, `Profesor/HorarioController.php`.
- CU15 Nota: `Admin/NotaController.php`.
- CU17 Pago: `Admin/PagoController.php`.
- CU18 Mensualidad: `Admin/MensualidadController.php`.
- CU20 Infraestructura: `Admin/InfraestructuraController.php`.
- CU21 Reporte: `Admin/ReporteController.php`, `Admin/ReporteEstaticoController.php`.
- CU22 Año Escolar: `GestionController.php`.
- CU23 Ficha Medica: `Admin/FichaMedicaController.php`.
- CU24 Personal Administrativo: `Admin/PersonalAdministrativoController.php`.

## Controladores Con Logica Proporcionada Por Traits De Laravel

Estos controladores tienen pocas lineas propias porque Laravel aporta la mayoria del comportamiento mediante traits:

- `ConfirmPasswordController`: trait `ConfirmsPasswords`.
- `ForgotPasswordController`: trait `SendsPasswordResetEmails`.
- `RegisterController`: trait `RegistersUsers`.
- `ResetPasswordController`: trait `ResetsPasswords`.
- `VerificationController`: trait `VerifiesEmails`.

En esos casos, las lineas `use TraitName;` son las mas importantes porque importan metodos completos ya implementados por Laravel.
