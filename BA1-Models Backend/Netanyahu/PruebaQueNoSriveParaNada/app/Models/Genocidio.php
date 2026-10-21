<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genocidio extends Model
{
    //
    protected $fillable = ['nombre','muertos','descripcion','ejercito_id'];

    public function ejercitos()
    {
        //N-N es belongsToMany EN AMBOS LADOS
        return $this->belongsToMany(Ejercito::class);
    }
}
