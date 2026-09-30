<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    //NOMBRE OFICIAL DE LA TABLA
    protected $table = 'ciudades';
    protected $fillable = [
        'nombre',
        'habitantes',
        'pais',
        'superficie',
        'alcalde',
        'amigo',
        'estado',
    ];

    //como tiene varios usamos el plural
    public function ejercitos()
    {
        return $this->hasMany(Ejercito::class);
    }
    //

    

}
