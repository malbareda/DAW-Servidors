<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Film extends Model
{
    protected $fillable = ['titol', 'genere', 'durada'];

    // ★ hasMany = "Un film TÉ MOLTES visualitzacions"
    //   Laravel buscarà automàticament la columna film_id a la taula views.
    public function views(): HasMany
    {
        return $this->hasMany(Visualitzacio::class);
    }

    // ★ belongsToMany = relació N:M
    //   Laravel busca la taula pivot administrator_film (ordre alfabètic!)
    //   i fa el JOIN automàticament.
    public function administradors(): BelongsToMany
    {
        return $this->belongsToMany(Administrador::class);
    }
}
