<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU12: Modelo de paralelo escolar, por ejemplo A, B, C o D.
class Paralelo extends Model
{
    // CU12: Tabla real de paralelos.
    protected $table = 'paralelo';
    // CU12: Llave primaria del paralelo.
    protected $primaryKey = 'id_paralelo';
    // CU12: La tabla paralelo no usa timestamps.
    public $timestamps = false;

    // CU12: Descripcion editable del paralelo.
    protected $fillable = ['descripcion'];

    // CU14: Asignaciones de horario que usan este paralelo.
    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_paralelo', 'id_paralelo');
    }
}
