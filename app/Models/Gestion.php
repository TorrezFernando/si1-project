<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table='gestions';
    protected $fillable = [
        'nombre',
    ];
}
