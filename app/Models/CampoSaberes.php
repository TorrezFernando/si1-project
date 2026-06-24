<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU13: Modelo del campo de saberes y conocimientos al que pertenece una materia.
class CampoSaberes extends Model
{
    // CU13: Tabla real de campos de saberes.
    protected $table = 'campos_saberes_conocimientos';
    // CU13: Llave primaria del campo.
    protected $primaryKey = 'id_campo';
    // CU13: La tabla no usa marcas de tiempo.
    public $timestamps = false;

    // CU13: Descripcion editable del campo de saber.
    protected $fillable = ['descripcion'];

    // CU13: Materias clasificadas dentro de este campo.
    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_campo', 'id_campo');
    }
}
