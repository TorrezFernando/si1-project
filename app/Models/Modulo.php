<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU10: Representa los modulos que agrupan funcionalidades del sistema.
class Modulo extends Model
{
    // CU10: Tabla real de modulos.
    protected $table = 'modulos';
    // CU10: Llave primaria del modulo.
    protected $primaryKey = 'id_modulo';

    // CU10: Campos editables del modulo.
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // CU09 y CU10: Funcionalidades agrupadas dentro del modulo.
    public function funcionalidades()
    {
        return $this->hasMany(Funcionalidad::class, 'id_modulo', 'id_modulo');
    }
}
