<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Http\Requests\StoreAdministradorRequest;
use App\Http\Requests\UpdateAdministradorRequest;
use App\Models\Film;

class AdministradorController extends Controller
{
    /*
      |----------------------------------------------------------------------
      | AVANÇAT — /admin/assign/{idFilm}/{idAdministrator}
      |----------------------------------------------------------------------
    */
    public function assign($idFilm, $idAdministrator)
    {
        $film = Film::findOrFail($idFilm);
        $admin = Administrador::findOrFail($idAdministrator);

        //attach() crea un registre a la taula pivot administrator_film
        //   amb film_id i administrator_id.
        //attach es el que es fa serviur casi sempre per N-N
        $film->administrators()->attach($admin->id);

        //tots els with son innecessaris per l'examen, aquesta fuincio pdoria haver estat 40 caracters
        return redirect('/films/create')->with('success', "Administrador {$admin->nom} assignat!");
    }
}
