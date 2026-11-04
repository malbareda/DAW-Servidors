<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Llibre extends Model
{
    //EMPEZADO CON LOS CAMPO FILLABLE QUE SERAN LAS VARIABLES QUE HAS CERADO EN LA MIGRACION
    protected $fillable = ['titol','categoria' ];

    //LIBRO TIENE MUCHOS PRESTAMOS
    public function prestecs(): HasMany
    {
        //hasMany 1-N (soy el 1)
        return $this->hasMany(Prestec::class);
    }

    public function bibliotecaris(): BelongsToMany
    {
        //N-N es belongsToMany EN AMBOS LADOS
        return $this->belongsToMany(Bibliotecari::class);
    }

}
