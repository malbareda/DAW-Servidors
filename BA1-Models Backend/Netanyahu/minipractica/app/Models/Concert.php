<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Concert extends Model
{
    //LAS FK SUELEN SER FILLABLES
    protected $fillable = [
        'artista_id',
        'nom',
        'data_hora',
        'durada_minuts',
        'tipus',
    ];

    //asi data_hora es un objeto fecha (Carbon) y puedo hacer ->format('d/m/Y H:i')
    //si no, seria un string normal y corriente
    protected $casts = [
        'data_hora' => 'datetime',
    ];

    //PONES EL NOMBRE DEL OTRO MODELO, en singular porque solo es uno
    public function artista(): BelongsTo
    {
        //belongsTo  1-N (soy la N) o 1-1 (soy el pasivo)
        //belongsToMany N-N
        //hasMany 1-N (soy el 1)
        //hasOne 1-1 (soy el dominante)
        return $this->belongsTo(Artista::class);
    }
}
