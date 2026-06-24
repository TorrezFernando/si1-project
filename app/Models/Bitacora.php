<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU05: Modelo de registros de bitacora.
class Bitacora extends Model
{
    // CU05: Tabla real donde se guarda la bitacora.
    protected $table = 'bitacora';
    // CU05: Llave primaria de la bitacora.
    protected $primaryKey = 'id_bitacora';
    // CU05: La tabla bitacora maneja fecha_hora manualmente.
    public $timestamps = false;

    // CU05: Campos permitidos al crear registros de bitacora.
    protected $fillable = [
        'id_user',
        'fecha_hora',
        'accion',
        'ip',
    ];

    // CU05: Convierte fecha_hora a objeto fecha.
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    // CU05 y CU01: Usuario que genero la accion registrada.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
