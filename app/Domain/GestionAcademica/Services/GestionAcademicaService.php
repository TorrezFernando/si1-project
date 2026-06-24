<?php

namespace App\Domain\GestionAcademica\Services;

use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Collection;

// CU22, CU12 y CU14: Servicio de reglas para gestiones, niveles y turnos academicos.
class GestionAcademicaService
{
    // CU22: Devuelve todas las gestiones ordenadas por anio descendente.
    public function todasLasGestiones(): Collection
    {
        // CU22: Ordena los anios escolares mas recientes primero.
        return Gestion::orderByDesc('nombre')->get();
    }

    public function crearGestion(string $nombre, string $fechaInicio, string $fechaFin): Gestion
    {
        // CU22: El documento indica que una gestion nueva inicia inactiva.
        return Gestion::create([
            'nombre' => $nombre,
            'fechainicio' => $fechaInicio,
            'fechafin' => $fechaFin,
            'activo' => false,
        ]);
    }

    // CU22: Actualiza nombre y rango de fechas de una gestion.
    public function actualizarGestion(Gestion $gestion, string $nombre, string $fechaInicio, string $fechaFin): void
    {
        $gestion->nombre = $nombre;
        $gestion->fechainicio = $fechaInicio;
        $gestion->fechafin = $fechaFin;
        $gestion->save();
    }

    // CU22: Activa una gestion y desactiva el resto.
    public function activarGestion(Gestion $gestion): void
    {
        // CU22: Solo puede existir una gestion activa a la vez.
        Gestion::where('id_gestion', '!=', $gestion->id_gestion)->update(['activo' => false]);
        $gestion->activo = true;
        $gestion->save();
    }

    // CU22: Elimina una gestion escolar.
    public function eliminarGestion(Gestion $gestion): void
    {
        $gestion->delete();
    }

    // CU12: Devuelve niveles academicos.
    public function todosLosNiveles(): Collection
    {
        return Nivel::all();
    }

    // CU12: Crea un nivel academico.
    public function crearNivel(string $nombre): Nivel
    {
        return Nivel::create(['nombre' => $nombre]);
    }

    // CU12: Actualiza nombre del nivel.
    public function actualizarNivel(Nivel $nivel, string $nombre): void
    {
        $nivel->nombre = $nombre;
        $nivel->save();
    }

    // CU12: Elimina un nivel academico.
    public function eliminarNivel(Nivel $nivel): void
    {
        $nivel->delete();
    }

    // CU12 y CU14: Devuelve turnos o jornadas.
    public function todosLosTurnos(): Collection
    {
        return Turno::all();
    }

    // CU12: Crea un turno.
    public function crearTurno(string $nombre): Turno
    {
        return Turno::create(['nombre' => $nombre]);
    }

    // CU12: Actualiza nombre del turno.
    public function actualizarTurno(Turno $turno, string $nombre): void
    {
        $turno->nombre = $nombre;
        $turno->save();
    }

    // CU12: Elimina un turno.
    public function eliminarTurno(Turno $turno): void
    {
        $turno->delete();
    }
}
