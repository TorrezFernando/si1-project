<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    protected $table = 'beca';
    protected $primaryKey = 'id_beca';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'porcentaje',
        'activo',
        'admin_only',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'activo' => 'boolean',
        'admin_only' => 'boolean',
    ];
}
