<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Ejercito;
use App\Models\Genocidio;
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
        $genocidio = Genocidio::create([
            'nombre' => "genocidio de ".$ciudad->nombre,
            'victimas' => 1000,
            'descripcion' => "Lorem ipsum dolor sit amet consectetur adipiscing elit parturient, facilisi torquent vestibulum vitae fermentum sed in aptent, leo inceptos laoreet ad natoque nunc montes. Tristique aliquam molestie faucibus duis morbi ante quam, viverra nam himenaeos cum taciti ligula id, vehicula sociosqu porta vivamus porttitor hendrerit. Ornare convallis scelerisque odio taciti class nisi pulvinar mauris nunc, aliquet inceptos metus vitae vivamus porttitor suscipit hac etiam penatibus, tincidunt fringilla dictum molestie varius magna dis malesuada.",
        ]);

        //linkamos el genocidio con el ejercto
        $genocidio->ejercitos()->attach($ejercito->id);

        $ejercito->ciudad_id = $ciudad->id;

        $ejercito->save();
        $ciudad->save();
        $genocidio->save();

        return redirect()->route('ejercitos.index');
    }

}
