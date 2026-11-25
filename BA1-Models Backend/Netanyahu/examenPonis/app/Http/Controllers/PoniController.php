<?php

namespace App\Http\Controllers;

use App\Models\Poni;
use App\Models\User;
use Illuminate\Http\Request;

class PoniController extends Controller
{
    public function nou(){
        return view('ponis.nou');//EL FORMULARI
    }

    public function store(Request $request)///EL RETORN DEL FORMULARI, POST
    {
        $data = $request->validate([
            //VALIDACIONS, NO SON NECESSARIES A NO SER QUE HO DEMANI L'ENUNCIAT
            'nom' => 'required|string|max:255',
            'edat' => 'required|integer|min:0',
            'mida' => 'required|in:PETIT,MITJA,GRAN',
        ]);

        //sense propietari: user_id no es posa i queda a null
        Poni::create($data);
        return redirect()->route('ponis.index');
    }

    public function propietari($idPoni, $idUsuari){
        $poni = Poni::findOrFail($idPoni);
        $usuari = User::findOrFail($idUsuari);

        $poni->user_id = $usuari->id;
        $poni->save();

        return redirect()->route('ponis.index');
    }

    public function index(){
        //where amb un operador: nomes els de 3 anys o mes
        $ponis = Poni::where('edat','>=',3)->get();

        return view('ponis.index',compact('ponis'));
    }

    public function fitxa($idPoni){
        $poni = Poni::findOrFail($idPoni);

        return view('ponis.fitxa',compact('poni'));
    }

    public function aniversari($idPoni){
        $poni = Poni::findOrFail($idPoni);

        $poni->edat = $poni->edat + 1;
        $poni->save();

        return redirect()->route('ponis.fitxa',$poni->id);
    }

    public function ranking(){
        //withCount compta les relacions i crea el camp concursos_count
        $ponis = Poni::withCount('concursos')->orderByDesc('concursos_count')->get();

        return view('ponis.ranking',compact('ponis'));
    }

    public function meus(){
        //si no hi ha ningu loguejat: llista buida (si no, where user_id = null duria els de l'hipica!)
        if(auth()->check()){
            $ponis = Poni::where('user_id',auth()->id())->where('mida','PETIT')->get();
        }else{
            $ponis = collect();
        }

        //reutilitzo la vista index
        return view('ponis.index',compact('ponis'));
    }
}
