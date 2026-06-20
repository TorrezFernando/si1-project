<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChatIaController extends Controller
{
    /**
     * Procesa la consulta por voz/texto de la IA, genera el SQL correspondiente con Gemini,
     * valida la seguridad y ejecuta la consulta de forma segura en la base de datos.
     */
    public function preguntar(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
        ]);

        $apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'explicacion' => 'La API Key de Gemini no está configurada. Por favor, agregue GEMINI_API_KEY en su archivo .env.',
            ], 400);
        }

        $user = $request->user();
        $idRol = (int) $user->id_rol;

        // Obtener IDs vinculados al usuario según su rol
        $idVinculado = null;
        $detallesRol = '';

        if ($idRol === 2) { // Profesor
            $idVinculado = DB::table('profesor')->where('id_user', $user->id_user)->value('id_profesor');
            $detallesRol = "Eres el Profesor ID: {$idVinculado}. Solo puedes consultar información de tus materias, tus cursos y tus alumnos asignados.";
        } elseif ($idRol === 3) { // Alumno
            $idVinculado = DB::table('alumno')->where('id_user', $user->id_user)->value('id_alumno');
            $detallesRol = "Eres el Alumno ID: {$idVinculado}. Solo puedes consultar información sobre tus propias notas, matrícula, pagos y asistencia.";
        } elseif ($idRol === 4) { // Apoderado
            $idVinculado = DB::table('apoderado')->where('id_user', $user->id_user)->value('id_apoderado');
            if (!$idVinculado && str_starts_with($user->username, 'apoderado_')) {
                $idVinculado = (int) substr($user->username, 10);
            }
            $detallesRol = "Eres el Apoderado ID: {$idVinculado}. Solo puedes consultar información de alumnos que sean tus hijos (vinculados a través de la tabla `parentesco` donde id_apoderado = {$idVinculado}).";
        } else {
            $detallesRol = "Tienes un rol administrativo (Administrador/Director/Secretaria). Tienes acceso a toda la base de datos.";
        }

        // Esquema de la base de datos detallado para alimentar a Gemini
        $esquemaDB = "
Esquema de tablas y campos disponibles en la base de datos (SQL):

1. `alumno`:
   - id_alumno (int, PK)
   - ci (string)
   - nombres (string)
   - ap_paterno (string)
   - ap_materno (string)
   - genero (string: 'M', 'F')
   - fecha_nacimiento (date)
   - id_user (int, FK a users)

2. `profesor`:
   - id_profesor (int, PK)
   - ci (string)
   - nombre (string)
   - ap_paterno (string)
   - ap_materno (string)
   - id_user (int, FK a users)

3. `apoderado`:
   - id_apoderado (int, PK)
   - ci (string)
   - nombres (string)
   - ap_paterno (string)
   - ap_materno (string)
   - id_user (int, FK a users)

4. `parentesco`:
   - id_parentesco (int, PK)
   - id_apoderado (int, FK a apoderado)
   - id_alumno (int, FK a alumno)
   - parentesco (string, ej: 'Padre', 'Madre', 'Tutor')

5. `curso`:
   - id_curso (int, PK)
   - nombre (string, ej: '1ro Primaria', '2do Secundaria', '3ro Secundaria')

6. `materia`:
   - id_materia (int, PK)
   - nombre (string, ej: 'Matemáticas', 'Lenguaje')

7. `gestion`:
   - id_gestion (int, PK)
   - nombre (string, ej: '2026', '2025')
   - fechainicio (date)
   - fechafin (date)

8. `matricula`:
   - id_matricula (int, PK)
   - fecha (date)
   - monto (decimal)
   - estado (string, ej: 'Registrada', 'Pendiente')

9. `inscripcion`:
   - id_inscripcion (int, PK)
   - id_matricula (int, FK a matricula)
   - id_alumno (int, FK a alumno)

10. `inscripcion_curso_gestion`:
    - id_inscripcion (int, FK a inscripcion)
    - id_curso (int, FK a curso)
    - id_gestion (int, FK a gestion)
    - paralelo (string, ej: 'A', 'B')

11. `nota`:
    - id_alumno (int, FK a alumno)
    - id_materia (int, FK a materia)
    - id_gestion (int, FK a gestion)
    - id_curso (int, FK a curso)
    - id_trimestre (int, ej: 1, 2, 3)
    - ser (int, nota 0-100)
    - saber (int, nota 0-100)
    - hacer (int, nota 0-100)
    - autoevaluacion (int, nota 0-100)
    - promediofinal (int, nota 0-100)

12. `pago_mensual`:
    - id_pago_mensual (int, PK)
    - id_alumno (int, FK a alumno)
    - id_gestion (int, FK a gestion)
    - id_curso (int, FK a curso)
    - mes (string, ej: 'Febrero', 'Marzo')
    - monto (decimal)
    - descuento (decimal)
    - estado (string, ej: 'Pagado', 'Pendiente')
    - fecha_pago / fecha (date)

13. `asistencia`:
    - id_asistencia (int, PK)
    - id_matricula (int, FK a matricula)
    - fecha (date)
    - estado (string: 'P' = Presente, 'A' = Ausente, 'F' = Falta, 'R' = Retraso)

14. `materia_curso_gestion`:
    - id_materia (int, FK a materia)
    - id_curso (int, FK a curso)
    - id_gestion (int, FK a gestion)
    - id_profesor (int, FK a profesor)
";

        // Prompt del sistema con instrucciones detalladas
        $systemPrompt = "
Eres un asistente de inteligencia artificial del Colegio Los Ángeles experto en el análisis y generación de reportes escolares.
Tu tarea es traducir la petición en lenguaje natural del usuario a una consulta SQL de tipo SELECT para consultar la base de datos, y proveer una explicación amigable del reporte.

Toma en cuenta las siguientes reglas obligatorias:
1. Genera únicamente consultas SELECT. Está ABSOLUTAMENTE PROHIBIDO generar consultas de modificación como INSERT, UPDATE, DELETE, DROP, ALTER, etc.
2. La consulta SQL debe ser compatible con la base de datos del proyecto (utiliza nombres de campos y tablas exactos del esquema proveído).
3. Restricciones del Rol del Usuario:
   {$detallesRol}
   Si el usuario pide ver datos fuera de su alcance permitido, responde amigablemente indicando que no tiene permisos y NO generes ninguna consulta SQL (deja el campo 'sql' vacío o nulo).
4. El formato de respuesta debe ser estrictamente un JSON válido con la siguiente estructura:
   {
     \"explicacion\": \"Una breve introducción o respuesta amigable en español sobre lo que mostrará el reporte.\",
     \"sql\": \"Consulta SELECT generada o null si no se puede realizar o no hay permisos.\",
     \"cabeceras\": [\"Título Columna 1\", \"Título Columna 2\", ...]
   }
No respondas con código markdown (sin ```json) en el cuerpo del mensaje, solo devuelve el objeto JSON de forma pura.
5. IMPORTANTE: Los nombres en la tabla `curso` NO llevan la preposición 'de'. Están guardados con el formato '[Grado] [Nivel]' (ej: '1ro Primaria', '3ro Secundaria'). Si el usuario escribe o dice 'de' (como '3ro de Secundaria'), debes omitir la palabra 'de' al generar la comparación en la cláusula WHERE (ej: usar `c.nombre = '3ro Secundaria'`).

{$esquemaDB}
";

        $userMessage = $request->input('mensaje');

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1/models/gemini-3.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\nPetición del usuario: \"" . $userMessage . "\""]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Error llamando a Gemini API', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'explicacion' => 'Hubo un problema al comunicarse con el asistente de Inteligencia Artificial.',
                ], 500);
            }

            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Sanitizar la respuesta de Gemini (a veces incluye ```json o espacios)
            $textResponse = trim($textResponse);
            if (str_starts_with($textResponse, '```json')) {
                $textResponse = substr($textResponse, 7);
            }
            if (str_ends_with($textResponse, '```')) {
                $textResponse = substr($textResponse, 0, -3);
            }
            $textResponse = trim($textResponse);

            $iaResult = json_decode($textResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($iaResult['explicacion'])) {
                Log::error('Respuesta de Gemini no es un JSON válido o carece de campos obligatorios', ['response' => $textResponse]);
                return response()->json([
                    'success' => false,
                    'explicacion' => 'El asistente de IA no pudo estructurar la respuesta correctamente.',
                ], 500);
            }

            $sql = isset($iaResult['sql']) ? trim($iaResult['sql']) : null;
            $cabeceras = $iaResult['cabeceras'] ?? [];
            $datos = [];

            if ($sql) {
                // Validación estricta de seguridad SQL antes de ejecutar
                // 1. Debe iniciar estrictamente con SELECT o WITH
                if (!preg_match('/^(select|with)\s/i', $sql)) {
                    return response()->json([
                        'success' => false,
                        'explicacion' => 'Consulta rechazada por seguridad: Sólo se admiten consultas de lectura (SELECT).',
                    ], 403);
                }

                // 2. Bloquear múltiples sentencias (evitar inyección por punto y coma)
                if (str_contains($sql, ';')) {
                    $sqlSinPuntoYComaFinal = rtrim($sql, ';');
                    if (str_contains($sqlSinPuntoYComaFinal, ';')) {
                        return response()->json([
                            'success' => false,
                            'explicacion' => 'Consulta rechazada por seguridad: No se permiten múltiples sentencias SQL.',
                        ], 403);
                    }
                    $sql = $sqlSinPuntoYComaFinal;
                }

                // 3. Palabras clave prohibidas (bloquear inserciones, actualizaciones, borrados y cambios de estructura)
                $forbiddenKeywords = ['insert', 'update', 'delete', 'drop', 'alter', 'create', 'truncate', 'replace', 'grant', 'revoke', 'schema', 'db_name', 'load_file', 'intofile', 'dumpfile'];
                foreach ($forbiddenKeywords as $keyword) {
                    if (preg_match('/\b' . $keyword . '\b/i', $sql)) {
                        return response()->json([
                            'success' => false,
                            'explicacion' => 'Consulta rechazada por seguridad: Contiene una acción de base de datos no permitida.',
                        ], 403);
                    }
                }

                // Ejecutar la consulta de forma segura
                try {
                    $resultados = DB::select($sql);
                    $datos = array_map(function ($item) {
                        return (array) $item;
                    }, $resultados);

                    // Guardar en la sesión para exportación segura
                    session([
                        'chat_ia_last_sql' => $sql,
                        'chat_ia_last_cabeceras' => $cabeceras,
                    ]);

                } catch (\Exception $dbEx) {
                    Log::error('Error ejecutando SQL generado por IA', ['sql' => $sql, 'error' => $dbEx->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'explicacion' => 'Se generó un error al consultar la base de datos. Por favor, reformule su pregunta.',
                        'error_db' => $dbEx->getMessage()
                    ], 500);
                }
            }

            Log::info('Chat IA Debug Output', [
                'user_message' => $userMessage,
                'gemini_raw_text' => $textResponse,
                'sql_generated' => $sql,
                'cabeceras' => $cabeceras,
                'datos_count' => count($datos),
                'datos_sample' => count($datos) > 0 ? array_slice($datos, 0, 2) : []
            ]);

            return response()->json([
                'success' => true,
                'explicacion' => $iaResult['explicacion'],
                'cabeceras' => $cabeceras,
                'datos' => $datos,
                'sql' => $sql,
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción general en ChatIaController', ['msg' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'explicacion' => 'Ocurrió un error inesperado al procesar su solicitud.',
            ], 500);
        }
    }

    /**
     * Exporta el último reporte generado por la IA en formato CSV, Excel o PDF/Impresión.
     */
    public function exportar(Request $request)
    {
        $formato = $request->query('formato');
        if (!in_array($formato, ['csv', 'excel', 'print', 'pdf'])) {
            abort(400, 'Formato no soportado.');
        }

        $sql = session('chat_ia_last_sql');
        $cabeceras = session('chat_ia_last_cabeceras') ?? [];

        if (!$sql) {
            return redirect()->route('admin.reportes.index')
                ->with('mensaje', 'No hay datos de IA disponibles para exportar.')
                ->with('icono', 'error');
        }

        try {
            $resultados = DB::select($sql);
            $datos = collect($resultados);
        } catch (\Exception $e) {
            Log::error('Error ejecutando SQL para exportar reporte de IA', ['sql' => $sql, 'error' => $e->getMessage()]);
            return redirect()->route('admin.reportes.index')
                ->with('mensaje', 'Error al regenerar los datos del reporte.')
                ->with('icono', 'error');
        }

        if ($datos->isEmpty()) {
            return redirect()->route('admin.reportes.index')
                ->with('mensaje', 'No se encontraron datos para exportar.')
                ->with('icono', 'error');
        }

        $filename = 'reporte_ia_' . date('Ymd_His');

        if ($formato === 'print' || $formato === 'pdf') {
            return view('admin.reportes.imprimir', [
                'datos' => $datos,
                'tipo' => 'chat_ia_reporte', // Esto resultará en "Reporte General"
                'formato' => $formato,
            ]);
        }

        if ($formato === 'csv') {
            $headers = [
                'Content-type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.csv",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($datos, $cabeceras) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

                $primerDato = (array) $datos->first();
                $campos = array_keys($primerDato);

                // Escribir cabeceras amigables
                $columnHeaders = (count($cabeceras) === count($campos)) ? $cabeceras : array_map(fn($f) => strtoupper(str_replace('_', ' ', $f)), $campos);
                fputcsv($file, $columnHeaders);

                foreach ($datos as $fila) {
                    fputcsv($file, (array) $fila);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($formato === 'excel') {
            $headers = [
                'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.xls",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($datos, $cabeceras) {
                echo "\xEF\xBB\xBF"; // BOM UTF-8
                echo "<table border='1'>";
                
                $primerDato = (array) $datos->first();
                $campos = array_keys($primerDato);
                
                echo '<tr>';
                $columnHeaders = (count($cabeceras) === count($campos)) ? $cabeceras : array_map(fn($f) => strtoupper(str_replace('_', ' ', $f)), $campos);
                foreach ($columnHeaders as $cabecera) {
                    echo '<th style="background-color:#198754;color:white;">' . e($cabecera) . '</th>';
                }
                echo '</tr>';

                foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ((array) $fila as $valor) {
                        echo '<td>' . e((string) ($valor ?? '-')) . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        abort(400);
    }
}
