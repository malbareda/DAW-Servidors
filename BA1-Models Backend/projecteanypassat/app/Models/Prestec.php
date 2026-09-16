<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestec extends Model
{
    use HasFactory;
    //
    protected $table = 'prestecs';
    protected $fillable = ["actiu","data","llibre_id","user_id"];

    //1-N, NO PONEMOS S
    public function llibre(){
        return $this->belongsTo(Llibre::class);
    }

    //1-N
    public function user(){
        return $this->belongsTo(User::class);
    }
}
