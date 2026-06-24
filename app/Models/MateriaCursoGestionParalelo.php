<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU14: Modelo pivote que ubica materia/curso/gestion en paralelo, horario y aula.
class MateriaCursoGestionParalelo extends Model
{
    // CU14: Tabla de detalle para horarios por paralelo.
    protected $table = 'materia_curso_gestion_paralelo';
    // CU14: Usa llave compuesta, no autoincremental.
    public $incrementing = false;
    // CU14: No usa timestamps.
    public $timestamps = false;
    // CU14: No existe llave primaria simple para Eloquent.
    protected $primaryKey = null;

    // CU14: Campos editables de la asignacion de horario.
    protected $fillable = [
        'id_materia',
        'id_gestion',
        'id_curso',
        'id_paralelo',
        'id_horario',
        'id_aula',
    ];

    // CU14: Bloque horario asignado.
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }

    // CU20 y CU14: Aula donde se dicta la clase.
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    // CU12 y CU14: Paralelo del curso.
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
    }
}
