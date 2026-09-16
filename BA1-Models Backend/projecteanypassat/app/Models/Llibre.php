<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llibre extends Model
{
    use HasFactory;

    //NOMBRE DE LA TABLA
    protected $table = 'llibres';

    //LLAVE PRIMARIA
    protected $primaryKey = 'id';

    //CAMPOS DE LA BD (campos fillable, por tanto que se pueden editar)
    protected $fillable = ["titol", "categoria"];

    //LAS FUNCIONES N-1 SE LLAMAN COMO EL NOMBRE DE LA CLASE + 'S'
    public function prestecs()
    {
        return $this->hasMany(Prestec::class);
    }

    //N-N
    public function bibliotecaris(){
        return $this->belongsToMany(Bibliotecari::class);
    }


}
