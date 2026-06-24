<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU02 y CU01: Modelo del docente y su usuario de acceso.
class Profesor extends Model
{
    // CU02: Tabla real de docentes.
    protected $table = 'profesor';
    // CU02: Llave primaria del docente.
    protected $primaryKey = 'id_profesor';
    // CU02: La tabla profesor no usa timestamps.
    public $timestamps = false;

    // CU02 y CU01: Campos permitidos para registrar o actualizar docentes.
    protected $fillable = [
        'id_user',
        'ci',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'direccion',
        'genero',
        'fecha_nac',
        'rda',
        'telefono',
        'correo',
    ];

    // CU02 y CU01: Usuario vinculado al docente para iniciar sesion.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // CU02 y CU14: Permiso individual para consultar horario.
    public function permiso()
    {
        return $this->hasOne(ProfesorPermiso::class, 'id_profesor', 'id_profesor');
    }
}
