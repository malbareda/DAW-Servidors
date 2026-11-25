<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Poni extends Model
{
    //LES VARIABLES QUE HAS CREAT A LA MIGRACIO (la FK tambe)
    protected $fillable = ['nom','edat','mida','user_id'];

    //PONI TE UN PROPIETARI (soc la N, per aixo tinc la FK). Pot ser null.
    public function user(): BelongsTo
    {
        //belongsTo  1-N (soc la N) o 1-1 (soc el passiu)
        return $this->belongsTo(User::class);
    }

    public function concursos(): BelongsToMany
    {
        //N-N es belongsToMany EN AMBDOS COSTATS
        return $this->belongsToMany(Concurs::class);
    }
}
