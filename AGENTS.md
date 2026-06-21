# AGENTS.md — Project Progress

## Goal
Implement missing use cases CU16 (Gestionar Asistencia) and CU19 (Gestionar Beca), plus verify/complete any other gaps.

## Status

### ✅ Completed
- **CU19 (Gestionar Beca)**: Full CRUD implemented
  - Migration: `2026_06_20_000001_add_fields_to_beca_table.php` (adds `nombre`, `porcentaje`, `activo`)
  - Migration: `2026_06_21_000001_update_becas_types.php` (sets percentages, adds `admin_only` column, inserts Beca Hermanos)
  - Model: `App\Models\Beca` (table `beca`, PK `id_beca`, no timestamps; `admin_only` added)
  - Controller: `App\Http\Controllers\Admin\BecaController` (index with search/pagination, create, store, edit, update, destroy with delete protection; `admin_only` validation)
  - Views: `resources/views/admin/becas/{index,create,edit,form}.blade.php` (index shows Tipo column; form has admin_only select)
  - Routes: `Route::resource('/admin/becas', ...)` in `routes/web.php`
  - Permission migration: `2026_06_20_000002_add_becas_permission.php` (grants admin.becas.index to ADMIN and SECRETARIA)

- **CU19 Beca data**:
  - Excelencia: 100%, auto-assigned to best student per course
  - Deportiva: 50%, admin-only
  - Cultural: 50%, admin-only
  - Convenio: 50%, admin-only
  - Social: 100%, admin-only
  - Hermanos: 100%, auto-assigned to youngest of 3+ siblings

- **CU16 (Gestionar Asistencia)**: Full CRUD implemented
  - Migration: `2026_06_20_000003_extend_asistencia_table.php` (adds `id_materia`, `id_gestion`, `id_curso`)
  - Model: `App\Models\Asistencia` (table `asistencia`, PK `id_asistencia`)
  - Controller: `App\Http\Controllers\Admin\AsistenciaController` (index with filters, create with bulk student load via AJAX, store, edit by date/materia/curso/gestion, update bulk, destroy single)
  - Views: `resources/views/admin/asistencias/{index,create,edit,form}.blade.php`
  - Routes: 7 explicit routes in `routes/web.php`
  - Permission migration: `2026_06_20_000004_add_asistencias_permission.php` (grants admin.asistencias.index to ADMIN, SECRETARIA, PROFESOR)

- **CU13**: Auto-assignment of SER/SABER/HACER/AUTOEVALUACION evaluation structure
  - Migration: `2026_06_20_000005_create_estructura_nota_table.php` (creates table + backfills existing materias)
  - Model: `App\Models\EstructuraNota`
  - Auto-creates 4 records (SER/SABER/HACER/AUTOEVALUACION) when a materia is created in MateriaController::store()
  - NotaController::calcularPromedio() uses configurable weights from estructura_nota instead of hardcoded 1/4

- **CU17**: Auto-beca-check on payment registration
  - MensualidadController::store() auto-calculates `descuento` from `beca.porcentaje` when `id_beca` is provided and no manual descuento is entered
  - View updated with JS to auto-fill descuento when beca is selected + shows `nombre (porcentaje%)`
  - Only active becas shown in dropdown
  - **Enhancement**: Auto-assigns Beca Excelencia (best student per curso+gestion) or Beca Hermanos (youngest of 3+ siblings) when no manual id_beca is provided
  - **Enhancement**: Filters becas by `admin_only` for non-ADMIN users; server-side guard prevents bypass

- **Double-encoding fix**: Fixed 5,332 rows across 21 columns in 14 tables (`alumno`, `apoderado`, `aula`, `materia`, `especialidad`, `nota`, `profesor`, `curso`, `gestion`, `horario`, `ficha_medica`, `secretaria`, `campos_saberes_conocimientos`) using `CONVERT(CAST(CONVERT(column USING latin1) AS BINARY) USING utf8mb4)`.

- **Alumno filters + beca assignment**:
  - Migration: `2026_06_21_162131_add_id_beca_to_alumno_table.php` (adds `id_beca` FK to `alumno`)
  - Model: `Alumno` has `belongsTo(Beca)` relationship; `id_beca` in fillable
  - Controller: `AlumnoController::index()` accepts `search` (name/CI), `id_curso`, `id_materia`, `becados` params; `edit()` passes `$becas` dropdown; `store()`/`update()` validate `id_beca`
  - View `alumnos/index.blade.php`: filter bar with curso/materia dropdowns, search input, "Solo becados" checkbox, beca column in table
  - Views `alumnos/{create,edit}.blade.php`: beca dropdown added
  - `AlumnoService`: passes `id_beca` through to model
  - `MensualidadController::autoAsignarBeca()`: falls back to `alumno.id_beca` if no auto-detected beca

## Key Notes
- No `PagoMensual` model exists; controllers use `DB::table('pago_mensual')` directly.
- Beca delete protection counts distinct `id_alumno` from `pago_mensual` table.
- All permissions follow the pattern: funcionalidad `admin.{resource}.index` + rol_funcionalidad entries.
- AsistenciaController uses AJAX (`alumnosPorAsignacion`) to load students dynamically when an asignacion (materia|gestion|curso) is selected.
- EstructuraNota weights default to 25% each and can be customized per materia.
- Admin-only becas filtered server-side in `MensualidadController::catalogos()` (dropdown) and guarded in `store()` (direct POST).
- Auto-asignacion runs only when no manual `id_beca` is provided; checks Excelencia first, then Hermanos, then `alumno.id_beca` as fallback.
