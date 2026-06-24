<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU22: Modelo de la gestion o anio escolar.
class Gestion extends Model
{
    // CU22: Tabla real de gestiones escolares.
    protected $table='gestion';
    // CU22: Llave primaria de la gestion.
    protected $primaryKey='id_gestion';
    // CU22: Campos editables de la gestion.
    protected $fillable = [
        'nombre',
        'fechainicio',
        'fechafin',
        'activo',
    ];

    // CU22: Convierte fechas y estado activo a tipos utiles en PHP.
    protected $casts = [
        'fechainicio' => 'date',
        'fechafin' => 'date',
        'activo' => 'boolean',
    ];
}
