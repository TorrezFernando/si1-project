<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU12, CU13 y CU14: Modelo pivote que asigna materia a curso, gestion y profesor.
class MateriaCursoGestion extends Model
{
    // CU12/CU13: Tabla de asignacion academica principal.
    protected $table = 'materia_curso_gestion';
    // CU12: La tabla usa llave compuesta, no id autoincremental.
    public $incrementing = false;
    // CU12: No maneja timestamps.
    public $timestamps = false;
    // CU12: No hay llave primaria simple para Eloquent.
    protected $primaryKey = null;

    // CU12, CU13 y CU02: Campos que forman la asignacion.
    protected $fillable = [
        'id_materia',
        'id_gestion',
        'id_curso',
        'id_profesor',
    ];

    // CU13: Materia asignada.
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    // CU22: Gestion escolar de la asignacion.
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    // CU12: Curso donde se dicta la materia.
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    // CU02: Profesor responsable de la materia en este curso/gestion.
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }

    // CU14: Paralelos, horarios y aulas relacionados con esta asignacion.
    public function paralelos()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_materia', 'id_materia')
            ->whereColumn('materia_curso_gestion_paralelo.id_gestion', 'materia_curso_gestion.id_gestion')
            ->whereColumn('materia_curso_gestion_paralelo.id_curso', 'materia_curso_gestion.id_curso');
    }
}
