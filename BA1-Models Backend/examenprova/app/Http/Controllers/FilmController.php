<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | BÀSIC — /films/create (GET) — Mostra el formulari de creació
    |----------------------------------------------------------------------
    Simplement retorna una vista Blade. No necessita dades addicionals
    perquè el formulari és estàtic (els gèneres estan hardcodejats al HTML).
    Es podria passar l'enum per mostrar el formulari com al projecte, pero aixo es mes rapid

    */
    public function create()
    {
        return view('films.create');
    }

    /*
    |----------------------------------------------------------------------
    | BÀSIC — /films/create (POST) — Desa una nova pel·lícula
    |----------------------------------------------------------------------
    $request->validate() valida les dades ABANS de tocar la BD.
    Aixo no es necessari i no ho demanava l'enunciat però així teniu un validate d'exemple

    */
    public function store(Request $request)
    {
        $request->validate([
            'titol'  => 'required|string|max:255',
            'genere' => 'required|in:ACCIO,COMEDIA,DRAMA', // ★ in: restringeix als valors vàlids
            'durada' => 'required|integer|min:1',
        ]);

        $film = new Film();
        $film->titol = $request->titol;
        $film->genere = $request->genere;
        $film->durada = $request->durada;
        $film->save();


        //Redirigim a la creació amb un missatge flash de confirmació.
        return redirect('/films/create')->with('success', 'Pel·lícula creada correctament!');
    }

    /*
    |----------------------------------------------------------------------
    | BÀSIC — /films/delete/{idFilm} — Elimina una pel·lícula
    |----------------------------------------------------------------------
    findOrFail() busca per PK. Si no existeix, llança una excepció 404
    automàticament (molt més net que comprovar manualment amb if/else).

    no obstant un find normal amb comprovacio de nulitat ja em val
    */
    public function destroy($idFilm)
    {
        $film = Film::findOrFail($idFilm);
        $film->delete();

        return redirect('/films/create')->with('success', 'Pel·lícula eliminada.');
    }

    /*
    |----------------------------------------------------------------------
    | AVANÇAT — /films/edit/{idFilm} (GET) — Mostra formulari d'edició
    |----------------------------------------------------------------------
    Passem l'objecte $film a la vista perquè el formulari pugui
    mostrar els valors actuals com a defaults (value="{{ $film->titol }}").
    */
    public function edit($idFilm)
    {
        $film = Film::findOrFail($idFilm);
        return view('films.edit', compact('film'));
    }

    /*
    |----------------------------------------------------------------------
    | AVANÇAT — /films/edit/{idFilm} (POST) — Actualitza la pel·lícula
    |----------------------------------------------------------------------
    update() fa un UPDATE SQL només dels camps que han canviat.
    */
    public function update(Request $request, $idFilm)
    {
        $request->validate([
            'titol'  => 'required|string|max:255',
            'genere' => 'required|in:ACCIO,COMEDIA,DRAMA',
            'durada' => 'required|integer|min:1',
        ]);

        $film = Film::findOrFail($idFilm);
        $film->titol = $request->titol;
        $film->genere = $request->genere;
        $film->durada = $request->durada;
        $film->update();

        return redirect('/films/edit/' . $idFilm)->with('success', 'Pel·lícula actualitzada!');
    }

    /*
    |----------------------------------------------------------------------
    | AVANÇAT — /films/noAdmin — Pel·lícules sense administrador
    |----------------------------------------------------------------------
    doesntHave() filtra els models que NO tenen cap relació associada.
    És l'oposat de has() que filtra els que SÍ en tenen.
    */
    public function noAdmin()
    {
        $films = Film::doesntHave('administradors')->get();
        //podem reutilitzar la vista de films.index, simplement ara en films li passem els que no tenen admin
        return view('films.index', compact('films'));
    }
}
