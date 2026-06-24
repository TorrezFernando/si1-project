<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

// CU04: Modelo del tutor/apoderado.
class Apoderado extends Model
{
    // CU04: Tabla real de apoderados.
    protected $table = 'apoderado';
    // CU04: Llave primaria del apoderado.
    protected $primaryKey = 'id_apoderado';
    // CU04: La tabla apoderado no usa timestamps.
    public $timestamps = false;

    // CU04: Campos editables del tutor/apoderado.
    protected $fillable = [
        'id_user',
        'ci',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'genero',
        'ocupacion',
        'fecha_nac',
        'telefono',
    ];

    // CU04: Relaciona el apoderado con sus alumnos mediante parentesco.
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'parentesco', 'id_apoderado', 'id_alumno')
            ->withPivot('descripcion');
    }

    // CU04 y CU01: Usuario de consulta asociado directamente al tutor.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // CU04 y CU01: Devuelve el usuario asociado o cae al patron antiguo apoderado_ID.
    public function usuarioConsulta(): ?User
    {
        if ($this->id_user) {
            $usuario = User::where('id_user', $this->id_user)
                ->where('id_rol', 4)
                ->first();

            if ($usuario) {
                return $usuario;
            }
        }

        return User::where('username', 'apoderado_' . $this->id_apoderado)
            ->where('id_rol', 4)
            ->first();
    }
}
