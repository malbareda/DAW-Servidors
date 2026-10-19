<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Concert;
use Illuminate\Http\Request;

class ConcertController extends Controller
{
    public function index()
    {
        //COJO DATOS
        //with('artista') es el EAGER LOADING: me trae los conciertos Y sus artistas en 2 consultas
        //si hiciera Concert::all() y luego $concert->artista->nom en la vista,
        //haria una consulta POR CADA concierto (problema N+1)
        $concerts = Concert::with('artista')->orderBy('data_hora')->get();

        return view('concerts.index', compact('concerts'));
    }

    public function create()
    {
        //SOLO LOS ARTISTAS ACTIVOS, para el desplegable
        $artistes = Artista::where('actiu', true)->orderBy('nom')->get();

        return view('concerts.create', compact('artistes'));
    }

    public function store(Request $request)
    {
        //si no sabeis que os llega del formulario descomentad esto
        //dd DUMP AND DIE, mata el programa y muestra esto por pantalla
        //dd($request->all());

        $data = $request->validate([
            //exists mira que el artista exista en la tabla artistes (OJO con el nombre de la tabla)
            'artista_id' => 'required|exists:artistes,id',
            'nom' => 'required|string|max:255',
            'data_hora' => 'required|date',
            'durada_minuts' => 'required|integer|min:15|max:300',
            //in: solo deja pasar estos valores, los mismos que el enum de la migracion
            'tipus' => 'required|in:CONCERT,DJ_SET,SHOWCASE,ACUSTIC',
        ]);

        Concert::create($data);

        return redirect()->route('concerts.index');
    }

    public function show(Concert $concert)
    {
        //aqui no hace falta with(), es un solo concierto
        //con $concert->artista en la vista ya me trae su artista
        return view('concerts.show', compact('concert'));
    }
}
