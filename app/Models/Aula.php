<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo',
        'capacidad',
        'ubicacion',
        'estado',
    ];

    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_aula', 'id_aula');
    }
}
