<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstructuraNota extends Model
{
    protected $table = 'estructura_nota';
    protected $primaryKey = 'id_estructura';
    public $timestamps = false;

    protected $fillable = [
        'id_materia',
        'componente',
        'porcentaje',
        'activo',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }
}
