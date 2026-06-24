<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU02 y CU14: Modelo que controla si un docente puede consultar su horario.
class ProfesorPermiso extends Model
{
    // CU02: Tabla donde se guardan permisos individuales del docente.
    protected $table = 'profesor_permisos';
    // CU02: Esta tabla no registra created_at ni updated_at.
    public $timestamps = false;

    // CU02: Campos que pueden asignarse masivamente.
    protected $fillable = [
        'id_profesor',
        'puede_ver_horario',
    ];

    // CU02: Convierte el permiso a booleano para usarlo como verdadero/falso.
    protected $casts = [
        'puede_ver_horario' => 'boolean',
    ];

    // CU02: Docente al que pertenece esta configuracion de permiso.
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }
}
