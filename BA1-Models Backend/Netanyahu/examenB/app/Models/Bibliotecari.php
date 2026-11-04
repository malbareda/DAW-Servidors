<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bibliotecari extends Model
{
    protected $fillable = [
        'nom',
    ];

    public function llibres(): BelongsToMany
    {
        //N-N es belongsToMany EN AMBOS LADOS
        return $this->belongsToMany(Llibre::class);
    }
}
