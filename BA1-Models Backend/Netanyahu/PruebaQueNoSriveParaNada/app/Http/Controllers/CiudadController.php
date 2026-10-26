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
        //Totes les ciutats
        //$ciudades = Ciudad::all();
        //Totes les ciutats destruides
        //wheres (camp, valor)->get();
        //$ciudades = Ciudad::where('estado','colonizable')->get();
        //Totes les ciutats amigues;
        $ciudades = Ciudad::where('amigo', true)->get();
        //ciutats ordenades per nombre d'habitants
        //$ciudades = Ciudad::orderBy('habitantes','asc')->first();
        //ciutats amb habitants majors que 500000
        $ciudades = Ciudad::where('habitantes','<',500000)->get();
        //ciutats amb habitants entre 100.000 i 1.000.000
        $ciudades = Ciudad::whereBetween('habitantes',[100000,999999])->get();
        //ciutats amb una variable

        //$nom = "Sabadell";
        //$ciudades = Ciudad::where('nombre',$nom)->get();

        //ciutats que tenen com a minim un exercit
        $ciudades = Ciudad::has('ejercitos')->get();
        //ciutats sense exercits (perdo)
        $ciudades = Ciudad::doesntHave('ejercitos')->get();


        //ciutats amb els exercits carregats ja (eager loading)
        $ciudades = Ciudad::with('ejercitos')->get();
        //ciutat amb el nombre d'exercits que té (count)
        $ciudades = Ciudad::with('ejercitos_count')->get();
        //aqui podries accedir al numero d'exercits amb $ciudades


        ///Crec que aixo no entra a la practica
        //ciutat amb mes exercits
        $ciudades = Ciudad::with('ejercitos_count')
            ->orderBy('ejercitos_count','desc')
            ->first();

        //ciudades que tienen ejercito ordenadas por el tamaño del ejercito
        $ciudad = Ciudad::has('ejercitos')
            ->orderBy('ejercitos_soldados')
            ->get();

        //ciudades que empiezan por Sab
        $ciudad = Ciudad::where('nombre', 'like', 'Sab%')->get();
        //ciudades que acaban por Sab
        $ciudad = Ciudad::where('nombre', 'like', '%sab')->get();





        $cosa = "eeeeeehhhhh hola";
        //PROCESO DATOS



        //DEVUELVO LOS DATOS A UNA VISTA (Frontend)
        //return view
        //primero le digo la vista que quiero acceder
        //y en el compact le paso todas las variables que quiero que el frontend pueda accede
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
    public function edit (Ciudad $ciudad)
    {
        return view('ciudades.edit', compact('ciudad'));
    }

    public function update(Request $request, Ciudad $ciudad)
    {
        $ciudad->nombre = $request->nombre;
        $ciudad->habitantes = $request->habitantes;
        $ciudad->pais = $request->pais;
        $ciudad->superficie = $request->superficie;
        $ciudad->alcalde = $request->alcalde;
        $ciudad->estado = $request->estado;
        $ciudad->amigo = $request->has('amigo');
        $ciudad->save();

        return redirect()->route('ciudades.show', $ciudad);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
