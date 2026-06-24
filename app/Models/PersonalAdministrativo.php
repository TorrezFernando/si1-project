<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU24: Personal administrativo con sus datos personales, laborales y usuario de acceso.
class PersonalAdministrativo extends Model
{
    // CU24: Tabla heredada donde se guarda el personal administrativo.
    protected $table = 'secretaria';
    // CU24: Llave primaria del personal administrativo.
    protected $primaryKey = 'id_secretaria';

    // CU24: La tabla secretaria no usa timestamps.
    public $timestamps = false;

    // CU24 y CU01: Campos editables del personal y su usuario vinculado.
    protected $fillable = [
        'id_secretaria',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'direccion',
        'telefono',
        'correo',
        //'cargo',
        //'area',
        'fecha_ingreso',
        'id_user',
    ];

    // CU24: Convierte fecha de ingreso a objeto fecha.
    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    // CU24 y CU01: Usuario de acceso asociado al personal administrativo.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
