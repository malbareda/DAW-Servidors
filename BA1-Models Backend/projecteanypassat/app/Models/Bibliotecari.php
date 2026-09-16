<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bibliotecari extends Model
{
    use HasFactory;
    //NOMBRE DE LA TABLA
    protected $table = 'bibliotecaris';

    //LLAVE PRIMARIA
    //POR DEFECTO LAS LLAVES PRIMARIAS SON AUTONUMERICOS
    protected $primaryKey = 'id';

    protected $fillable = ["nom"];

    //RECORDAD QUE LOS N-N SE HACEN DESDE LAS DOS BANDAS
    //DE HECHO TODAS LAS RELACIONES SE HACEN DESDE LAS DOS BANDAS
    public function llibres()
    {
        return $this->belongsToMany(Llibre::class);
    }



}
