<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visualitzacio extends Model
{
    protected $fillable = ['completada', 'data_inici', 'film_id', 'user_id'];

    // ★ belongsTo = "Una visualització PERTANY A un film"
    //   És l'invers de hasMany. La FK (film_id) està en AQUESTA taula.
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    // ★ Mateixa lògica: una visualització pertany a un usuari.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
