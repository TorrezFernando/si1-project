<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU09: Guarda los permisos o acciones especificas disponibles en cada modulo.
class Funcionalidad extends Model
{
    // CU09: Tabla real de funcionalidades.
    protected $table = 'funcionalidades';
    // CU09: Llave primaria de la funcionalidad.
    protected $primaryKey = 'id_funcionalidad';

    // CU09 y CU10: Campos editables de la funcionalidad.
    protected $fillable = [
        'id_modulo',
        'nombre',
        'descripcion',
    ];

    // CU10: Modulo al que pertenece la funcionalidad.
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }

    // CU09: Roles que tienen permitido usar esta funcionalidad.
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_funcionalidad', 'id_funcionalidad', 'id_rol');
    }
}
