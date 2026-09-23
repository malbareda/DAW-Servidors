<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //RECIBO INFORMACION DE LA WEB

        //COJO DATOS
        $ciudades = Ciudad::all();
        $cosa = "eeeeeehhhhh hola";
        //PROCESO DATOS

        //DEVUELVO LOS DATOS A UNA VISTA (Frontend)
        //return view
        //primero le digo la vista que quiero acceder
        //y en el compact le paso todas las variables que quiero que el frontend pueda acceder
        return view('ciudades.index', compact('cosa','ciudades'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
