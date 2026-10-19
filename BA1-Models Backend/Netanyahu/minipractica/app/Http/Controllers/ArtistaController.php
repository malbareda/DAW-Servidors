<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use Illuminate\Http\Request;

class ArtistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //COJO DATOS
        //orderBy para ordenarlos por nombre, y get() para ejecutar la consulta
        $artistes = Artista::orderBy('nom')->get();

        //DEVUELVO LOS DATOS A UNA VISTA (Frontend)
        //en el compact le paso todas las variables que quiero que el frontend pueda acceder
        return view('artistes.index', compact('artistes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //USO EL MISMO FORMULARIO PARA CREATE Y EDIT
        //aqui no le paso ningun artista, asi el formulario sabe que es para crear
        return view('artistes.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //VALIDACION, si algo falla vuelve al formulario el solo con los errores
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'imatge_url' => 'nullable|url',
            'actiu' => 'sometimes|boolean',
        ]);

        //validate() me devuelve solo los campos validados, los guardo en $data
        //CREATE HACE UN INSERT DE ESO EN LA BD
        Artista::create($data);

        //hago un redirect a una ruta conocida
        return redirect()->route('artistes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Artista $artista)
    {
        //como en la ruta pone {artista} y aqui pido un Artista, laravel me lo busca solo
        //es lo mismo que hacer Artista::findOrFail($id)
        return view('artistes.show', compact('artista'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artista $artista)
    {
        //el mismo formulario que en create, pero ahora SI le paso el artista
        return view('artistes.form', compact('artista'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artista $artista)
    {
        //LA MISMA VALIDACION QUE EN EL STORE
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'imatge_url' => 'nullable|url',
            'actiu' => 'sometimes|boolean',
        ]);

        //update() es como el create() pero sobre un artista que ya existe
        $artista->update($data);

        return redirect()->route('artistes.show', $artista);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artista $artista)
    {
        //NO LO PIDEN EN LA MINIPRACTICA
    }
}
