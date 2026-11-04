<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestec extends Model
{
    protected $fillable = [
        'actiu',
        'data',
        //LAS FK SON FILLABLES? GENERALMENTE SI
        'llibre_id',
        'user_id',
    ];

    //EN ESTE EXAMEN NO ES ESTRICTAMENTE ENCESARIO PERO QUIZAS EN EL DE VERDAD SI
    //EL CASTS SIRVE PARA FORZAR UN TIPO
    protected $casts = [
        'actiu' => 'boolean',
        'data' => 'datetime',
    ];


    public function llibre(): BelongsTo
    {
        return $this->belongsTo(Llibre::class);
    }

    public function user(): BelongsTo
    {
        //belongsTo  1-N (soy la N) o 1-1 (soy el pasivo)
        //belongsToMany N-N
        //hasMany 1-N (soy el 1)
        //hasOne 1-1 (soy el dominante)
        return $this->belongsTo(User::class);
    }


}
