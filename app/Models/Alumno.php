<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU03 y CU01: Modelo del estudiante y su usuario de acceso.
class Alumno extends Model
{
    // CU03: Tabla real donde se guardan los estudiantes.
    protected $table = 'alumno';
    // CU03: Llave primaria del alumno.
    protected $primaryKey = 'id_alumno';
    // CU03: La tabla alumno no maneja timestamps de Laravel.
    public $timestamps = false;

    // CU03 y CU01: Campos permitidos para registrar o actualizar estudiantes.
    protected $fillable = [
        'id_user',
        'ci',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'genero',
        'fecha_nac',
        'telefono',
        'id_beca',
    ];

    // CU03 y CU01: Usuario vinculado al estudiante para iniciar sesion.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // CU23: Ficha medica registrada para el estudiante.
    public function fichaMedica()
    {
        return $this->hasOne(FichaMedica::class, 'id_alumno', 'id_alumno');
    }

    public function beca()
    {
        return $this->belongsTo(Beca::class, 'id_beca', 'id_beca');
    }
}
