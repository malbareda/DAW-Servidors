<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Concurs extends Model
{
    //el plural de concurs no es regular: li dic el nom de la taula
    protected $table = 'concursos';

    protected $fillable = ['nom','data'];

    protected $casts = [
        'data' => 'date',
    ];

    public function ponis(): BelongsToMany
    {
        //N-N es belongsToMany EN AMBDOS COSTATS
        return $this->belongsToMany(Poni::class);
    }
}
