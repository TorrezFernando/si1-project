<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU15: Modelo de calificaciones por alumno, materia, gestion, curso y trimestre.
class Nota extends Model
{
    // CU15: Tabla real de notas.
    protected $table = 'nota';
    // CU15: Usa llave compuesta, no id autoincremental.
    public $incrementing = false;
    // CU15: No maneja timestamps.
    public $timestamps = false;
    // CU15: No hay llave primaria simple para Eloquent.
    protected $primaryKey = null;

    // CU15: Campos editables de la calificacion.
    protected $fillable = [
        'id_alumno',
        'id_materia',
        'id_gestion',
        'id_curso',
        'id_trimestre',
        'ser',
        'saber',
        'hacer',
        'autoevaluacion',
        'promediofinal',
        'descripcion',
    ];

    // CU15 y CU03: Alumno evaluado.
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    // CU15 y CU13: Materia evaluada.
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    // CU15 y CU22: Gestion escolar de la nota.
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    // CU15 y CU12: Curso correspondiente a la nota.
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    // CU15: Trimestre al que pertenece la nota.
    public function trimestre()
    {
        return $this->belongsTo(Trimestre::class, 'id_trimestre', 'id_trimestre');
    }
}
