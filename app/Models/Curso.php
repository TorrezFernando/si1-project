<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU12: Modelo de cursos/grupos academicos del colegio.
class Curso extends Model
{
    // CU12: Tabla real de cursos.
    protected $table = 'curso';
    // CU12: Llave primaria usada por la tabla curso.
    protected $primaryKey = 'id_curso';
    // CU12: La tabla curso no maneja timestamps de Laravel.
    public $timestamps = false;

    // CU12: Campos editables para registrar o actualizar cursos.
    protected $fillable = ['nombre', 'grado', 'id_nivel', 'id_paralelo', 'id_turno'];

    // CU22 y CU12: Gestiones escolares en las que participa el curso.
    public function gestiones()
    {
        return $this->belongsToMany(Gestion::class, 'curso_gestion', 'id_curso', 'id_gestion');
    }

    // CU13 y CU12: Materias asignadas al curso por gestion y docente.
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'materia_curso_gestion', 'id_curso', 'id_materia')
            ->withPivot('id_gestion', 'id_profesor');
    }

    // CU12 y CU14: Paralelos disponibles para el curso segun asignaciones de materia.
    public function paralelos()
    {
        return $this->belongsToMany(Paralelo::class, 'materia_curso_gestion_paralelo', 'id_curso', 'id_paralelo')->distinct();
    }

    // CU12: Nivel academico al que pertenece el curso.
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }

    // CU12: Paralelo principal relacionado al curso.
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
    }

    // CU12: Turno o jornada asociada al curso.
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno');
    }
}
