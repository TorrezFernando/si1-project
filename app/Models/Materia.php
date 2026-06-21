<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU13: Modelo de materia o asignatura del plan academico.
class Materia extends Model
{
    // CU13: Tabla real de materias.
    protected $table = 'materia';
    // CU13: Llave primaria de la materia.
    protected $primaryKey = 'id_materia';
    // CU13: La tabla materia no usa timestamps.
    public $timestamps = false;

    // CU13: Campos editables de la materia.
    protected $fillable = [
        'nombre',
        'carga_horaria',
        'distintivo',
        'id_campo',
    ];

    // CU13: Campo de saberes al que pertenece la materia.
    public function campo()
    {
        return $this->belongsTo(CampoSaberes::class, 'id_campo', 'id_campo');
    }

    // CU12 y CU13: Cursos y gestiones donde se dicta la materia.
    public function cursosGestiones()
    {
        return $this->belongsToMany(Curso::class, 'materia_curso_gestion', 'id_materia', 'id_curso')
            ->withPivot('id_gestion', 'id_profesor');
    }

    // CU13: Estructura de evaluacion de la materia (SER/SABER/HACER/AUTOEVALUACION).
    public function estructuraNotas()
    {
        return $this->hasMany(EstructuraNota::class, 'id_materia', 'id_materia');
    }
}
