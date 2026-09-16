<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Administrador extends Model
{
    protected $fillable = ['nom', 'departament'];

    // ★ Costat invers de la relació N:M.
    //   Tant Film com Administrator defineixen belongsToMany;
    //   la taula pivot és la mateixa (administrator_film).
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
