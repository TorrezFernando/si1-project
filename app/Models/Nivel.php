<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU12: Modelo de nivel academico, por ejemplo inicial, primaria o secundaria.
class Nivel extends Model
{
    // CU12: Tabla real de niveles.
    protected $table = 'nivels';

    // CU12: Nombre editable del nivel.
    protected $fillable = [ 'nombre' ];
   
}
