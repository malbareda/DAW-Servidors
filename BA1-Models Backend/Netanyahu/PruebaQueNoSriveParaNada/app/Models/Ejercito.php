<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejercito extends Model
{
    //LAS FK SUELEN SER FILLABLES
    protected $fillable = ['nombre', 'soldados', 'ciudad_id'];

    //PONES EL NOMBRE DEL OTRO MODELO
    public function ciudad()
    {
        //belongsTo  1-N (soy la N) o 1-1 (soy el pasivo)
        //belongsToMany N-N
        //hasMany 1-N (soy el 1)
        //hasOne 1-1 (soy el dominante)
        return $this->belongsTo(Ciudad::class);

    }

    public function genocidios()
    {
        return $this->belongsToMany(Genocidio::class);

    }
}
