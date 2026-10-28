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

        //VALIDACION NORMAL: si falla, laravel ya hace el back() con los errores el solito
        $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
        ]);

        $ciudad = Ciudad::find($request->ciudad_id);

        //VALIDACIONES DE NEGOCIO: esto validate() no lo puede comprobar
        //back() vuelve al formulario
        //withErrors: la clave ('ciudad_id') es la que luego pillo con @error('ciudad_id') en la vista
        //withInput: para que old('ciudad_id') deje seleccionada la ciudad que habia elegido
        if ($ciudad->estado == 'destruido' || $ciudad->estado == 'abandonado') {
            return back()->withErrors([
                'ciudad_id' => 'Aquesta ciutat ja està '.$ciudad->estado.', no cal envair-la.',
            ])->withInput();
        }

        if ($ciudad->amigo) {
            return back()->withErrors([
                'ciudad_id' => 'No pots envair una ciutat amiga.',
            ])->withInput();
        }

        //el error no tiene por que ser de un campo del formulario, me invento la clave 'soldados'
        if ($ejercito->soldados < 100) {
            return back()->withErrors([
                'soldados' => 'L\'exèrcit només té '.$ejercito->soldados.' soldats, en necessita 100 com a mínim.',
            ])->withInput();
        }

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
