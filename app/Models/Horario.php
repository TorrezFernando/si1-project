<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU14: Modelo de bloques de horario escolar.
class Horario extends Model
{
    // CU14: Tabla real de horarios.
    protected $table = 'horario';
    // CU14: Llave primaria del horario.
    protected $primaryKey = 'id_horario';
    // CU14: La tabla horario no usa timestamps.
    public $timestamps = false;

    // CU14: Campos editables del bloque horario.
    protected $fillable = [
        'dia',
        'hora_inicio',
        'hora_fin',
    ];

    // CU14: Asignaciones de materia/curso/paralelo que usan este bloque horario.
    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_horario', 'id_horario');
    }
}
