<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Visualitzacio;
use App\Http\Requests\StoreVisualitzacioRequest;
use App\Http\Requests\UpdateVisualitzacioRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VisualitzacioController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | BÀSIC — /visualitzacions — Llista visualitzacions de l'usuari logejat
    |----------------------------------------------------------------------
    | ★ Auth::id() retorna l'ID de l'usuari autenticat.
    */
    public function index()
    {
        $films = Visualitzacio::where('user_id', Auth::id())
            ->get();

        return view('films.index', compact('films'));
    }

    /*
    |----------------------------------------------------------------------
    | BÀSIC — /visualitzacions/{idFilm}/{idUser} — Crea una visualització
    |----------------------------------------------------------------------
    */
    public function store($idFilm, $idUser)
    {
        Film::findOrFail($idFilm);

        $vi = new Visualitzacio();
        $vi->film_id = $idFilm;
        $vi->user_id = $idUser;
        $vi->completada = false;
        $vi->data_inici = Carbon::now();
        $vi->save();

        return redirect('/visualitzacions')->with('success', 'Visualització creada!');
    }

    /*
    |----------------------------------------------------------------------
    | AVANÇAT — /finish/{idView} — Marca una visualització com a completada
    |----------------------------------------------------------------------
    */
    public function finish($idView)
    {
        $view = Visualitzacio::findOrFail($idView);

        // ★ Doble condició: és el meu view I encara no està completat
        if ($view->user_id === Auth::id() && !$view->completada) {
            $view->update(['completada' => true]);
        }

        return redirect('/visualitzacions');
    }

}
