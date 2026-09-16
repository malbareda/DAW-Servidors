<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Llibre;
use Illuminate\Http\Request;

class LlibreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Autorizacion, lo veremos mas tarde
        $this->authorize('create', Llibre::class);

        return view("llibres.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //ESTO LO HARE CUANDO HAGA EL FORMUALRIO
        Llibre::create([
           "titol" => $request->input('titol'),
            "categoria" => $request->input('categoria')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function cataleg()
    {
        $llibres = Llibre::all(); //cojo todos los libros
        return response()->json($llibres); //coger Llibre:all() y transformarlo a json y devolverlo
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modificar(string $id)
    {
        $llibre = Llibre::find($id);
        return view("llibres.modificar", compact("llibre"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $llibre = Llibre::find($id);
        $llibre->update($request->all());

        $llibreModificat = Llibre::find($id);
        return redirect()->route('llibres.cataleg',);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $llibre = Llibre::find($id);
        $llibre->delete();
    }

    public function sensePrestecs(){

        $llibres = Llibre::doesntHave('prestecs')->get();
        return response()->json($llibres);  //se podria devolver un html
    }

}
