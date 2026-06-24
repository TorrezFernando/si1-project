<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU15: Modelo de trimestres usados para organizar calificaciones.
class Trimestre extends Model
{
    // CU15: Tabla real de trimestres.
    protected $table = 'trimestre';
    // CU15: Llave primaria del trimestre.
    protected $primaryKey = 'id_trimestre';
    // CU15: La tabla trimestre no usa timestamps.
    public $timestamps = false;

    // CU15: Notas registradas en este trimestre.
    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_trimestre', 'id_trimestre');
    }
}
