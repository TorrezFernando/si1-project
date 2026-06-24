<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU23: Modelo de la ficha medica asociada a un estudiante.
class FichaMedica extends Model
{
    // CU23: Tabla real de fichas medicas.
    protected $table = 'ficha_medica';
    // CU23: Llave primaria de la ficha.
    protected $primaryKey = 'id_ficha';
    // CU23: La tabla no usa timestamps.
    public $timestamps = false;

    // CU23: Campos editables de salud y contacto de emergencia.
    protected $fillable = [
        'tipo_sangre',
        'alergias',
        'contacto_emergencia',
        'telf_emerg',
        'id_alumno',
    ];

    // CU23: Estudiante al que pertenece la ficha medica.
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }
}
