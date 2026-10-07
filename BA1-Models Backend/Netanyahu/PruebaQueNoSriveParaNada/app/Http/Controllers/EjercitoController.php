<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Ejercito;
use Illuminate\Http\Request;

class EjercitoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //RECIBO INFORMACION DE LA WEB

        //COJO DATOS
        $ejercitos = Ejercito::all();
        //PROCESO DATO
        //DEVUELVO LOS DATOS A UNA VISTA (Frontend)
        //return view
        //primero le digo la vista que quiero acceder
        //y en el compact le paso todas las variables que quiero que el frontend pueda acceder
        return view('ejercitos.index', compact('ejercitos'));

    }


    public function invadirFormulario(Ejercito $ejercito){
        $ciudades = Ciudad::all();
        return view('ejercitos.invadir',compact('ejercito','ciudades'));
    }

    public function invadir(Request $request, Ejercito $ejercito){
        //dd DUMP AND DIE, mata el programa y muestra esto por pantalla
        //dd($request->ciudad_id);
        $ciudad = Ciudad::find($request->ciudad_id);
        $ciudad->estado = 'destruido';
        $ejercito->soldados -= 100;
        $ciudad->habitantes -=1000;
        //haciendo la relacion en un solo lado ya vale.
        //si es el de la FK, claro.
        $ejercito->ciudad_id = $ciudad->id;

        $ejercito->save();
        $ciudad->save();

        return redirect()->route('ejercitos.index');
    }

}
