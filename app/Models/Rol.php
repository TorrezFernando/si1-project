<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU01 y CU09: Modelo de roles del sistema y sus permisos.
class Rol extends Model
{
    // CU01: Tabla real de roles.
    protected $table = 'rol';
    // CU01: Llave primaria del rol.
    protected $primaryKey = 'id_rol';
    // CU01: La tabla rol no usa timestamps.
    public $timestamps = false;

    // CU01: Tipo o nombre del rol.
    protected $fillable = [
        'tipo',
    ];

    // CU09: Funcionalidades permitidas para este rol.
    public function funcionalidades()
    {
        return $this->belongsToMany(Funcionalidad::class, 'rol_funcionalidad', 'id_rol', 'id_funcionalidad');
    }
}
