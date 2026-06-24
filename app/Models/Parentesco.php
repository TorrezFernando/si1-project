<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU04: Modelo de relacion entre estudiante y tutor/apoderado.
class Parentesco extends Model
{
    // CU04: Tabla real que vincula alumnos con apoderados.
    protected $table = 'parentesco';
    // CU04: La relacion no usa autoincremental.
    public $incrementing = false;
    // CU04: La tabla parentesco no usa timestamps.
    public $timestamps = false;
    // CU04: No hay una llave primaria simple definida.
    protected $primaryKey = null;

    // CU04: Campos editables de la relacion alumno-apoderado.
    protected $fillable = [
        'id_alumno',
        'id_apoderado',
        'descripcion',
    ];

    // CU04 y CU03: Alumno relacionado con este parentesco.
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    // CU04: Apoderado relacionado con este parentesco.
    public function apoderado()
    {
        return $this->belongsTo(Apoderado::class, 'id_apoderado', 'id_apoderado');
    }
}
