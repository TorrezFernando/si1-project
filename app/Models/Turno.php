<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU12: Modelo de turno o jornada academica.
class Turno extends Model
{
    // CU12: Tabla real de turnos.
    protected $table = 'turnos';
    // CU12: Nombre editable del turno.
    protected $fillable = [
        'nombre', 
         ];

}
