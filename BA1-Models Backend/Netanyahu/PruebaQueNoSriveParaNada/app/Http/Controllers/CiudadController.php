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

    public function create()
    {
        return view('ciudades.create');
    }

    public function store(Request $request)
    {
        //OPCIONAL, LA VALIDACION, NO HACE FALTA QUE LA HAGAIS EN LA MINIPRACTICA
        //PERO SI EN LA PRACTICA
        $request->validate([
            'nombre' => 'required|string|max:255',
            'habitantes' => 'required|integer',
            'pais' => 'required|string|max:255',
            'superficie' => 'required|numeric',
            'alcalde' => 'required|string|max:255',
            'estado' => 'required|string',
            'amigo' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['amigo'] = $request->has('amigo');

        //CREATE HACE UN INSERT DE ESO EN LA BD
        Ciudad::create($data);

        //hago un redirect a una ruta conocida
        return redirect()->route('ciudades.index');
    }


    public function bombardear(Request $request)
    {
        //RECIBO INFORMACION DE LA WEB
        $nombreCiudad = $request->nombre;

        //COJO DATOS
        $ciudad = Ciudad::where('nombre',$nombreCiudad)->first();
        $respuesta = "";

        if($ciudad==null){
            $respuesta = "No hay una ciudad con este nombre";
        }

        else if($ciudad->estado == 'destruido' || $ciudad->estado == 'abandonado'){
            $respuesta = "Deja de gastar bombas";
        }

        else if($ciudad->amigo == true){
            $respuesta = "¿No bombarderias a un amigo, verdad?";
        }
        else{
            $ciudad->habitantes = $ciudad->habitantes-1000;
            $ciudad-> estado = 'destruido';
            $respuesta = "Ciudad bombardeada satisfactoriamente";
            $ciudad->save(); //ESTO LO GUARDARA EN LA BASE DE DATOS
        }

        return view ('ciudades.bombardear', compact('ciudad','respuesta'));



    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ciudad = Ciudad::findOrFail($id);
        return view('ciudades.show', compact('ciudad'));
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
