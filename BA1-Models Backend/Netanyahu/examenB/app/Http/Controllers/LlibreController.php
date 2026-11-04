<?php

namespace App\Http\Controllers;

use App\Models\Bibliotecari;
use App\Models\Llibre;
use Illuminate\Http\Request;

class LlibreController extends Controller
{
    public function create(){
        return view('llibres.create');//EL FORMULARIO
    }

    public function store(Request $request)///EL RETORNO DEL FORMULARIO, POST
    {
        $data = $request->validate([
            //VALIDACIONES, NO SON NECESARIAS A NO SER QUE LO PIDA ESPECIFICAMENTE EN EL ENUNCIADO
            'titol' => 'required|string|max:255',
            'categoria' => 'required|in:FICCIO,NOFICCIO',
        ]);

        Llibre::create($data);
        return redirect()->route('llibres.cataleg');
    }


    public function edit($idLlibre){
        $llibre = Llibre::findOrFail($idLlibre);

        return view('llibres.edit',compact('llibre'));//EL FORMULARIO
    }

    public function update(Request $request, $idLlibre)///EL RETORNO DEL FORMULARIO, POST
    {
        $llibre = Llibre::findOrFail($idLlibre);

        $llibre->titol = $request->titol;
        $llibre->categoria = $request->categoria;

        $llibre->save();
        return redirect()->route('llibres.cataleg');
    }

    public function cataleg(){
        $llibres = Llibre::all();

        return view('llibres.cataleg',compact('llibres'));

    }

    public function eliminar($idLlibre){
        $llibre = Llibre::findOrFail($idLlibre);
        $llibre->delete();
        return redirect()->route('llibres.cataleg');
    }


    public function senseprestecs()
    {
        //SIEMPRE HAY UNA PREGUNTA DE ALGUNA FUNCION GRACIOSA DEL WHERE
        //QUE ESTARA EN LOS APUNTES CON ALGUN EJEMPLO, NO FALLA.
        $llibres = Llibre::doesntHave('prestecs')->get();
        return view('llibres.cataleg', compact('llibres'));
    }

    public function assignar($idLlibre, $idBibliotecari){
        $llibre = Llibre::findOrFail($idLlibre);
        $bibliotecari = Bibliotecari::findOrFail($idBibliotecari);

        //attach sirve para linkar dos elementos relacionados entre si con una N-N
        //(es decir, meter una fila en la tabla intermedia {bibliotecari_llibreÇ)
        //en una N-N haces attach en un solo lado y ya te lo mete en ambos lados
        $llibre->bibliotecaris()->attach($bibliotecari->id);

        return redirect()->route('llibres.cataleg');
    }
}
