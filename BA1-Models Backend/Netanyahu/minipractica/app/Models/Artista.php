<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artista extends Model
{
    //NOMBRE OFICIAL DE LA TABLA
    //si no lo pongo, laravel buscaria 'artistas'
    protected $table = 'artistes';

    //LOS CAMPOS QUE SE PUEDEN RELLENAR CON create() O update() DE GOLPE
    protected $fillable = [
        'nom',
        'pais',
        'bio',
        'imatge_url',
        'actiu',
    ];

    //CASTS: le digo a laravel de que tipo es el campo
    //en mysql un boolean es un 0 o un 1, asi me lo devuelve como true/false
    protected $casts = [
        'actiu' => 'boolean',
    ];

    //como tiene varios usamos el plural
    public function concerts(): HasMany
    {
        //hasMany 1-N (soy el 1)
        return $this->hasMany(Concert::class);
    }
}
