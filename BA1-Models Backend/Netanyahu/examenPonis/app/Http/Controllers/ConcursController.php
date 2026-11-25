<?php

namespace App\Http\Controllers;

use App\Models\Concurs;
use App\Models\Poni;
use Illuminate\Http\Request;

class ConcursController extends Controller
{
    public function inscriure($idConcurs, $idPoni){
        $concurs = Concurs::findOrFail($idConcurs);
        $poni = Poni::findOrFail($idPoni);

        //nomes si te 3 anys o mes i no hi estava inscrit; si no, no fa res
        if($poni->edat >= 3 && !$concurs->ponis->contains($poni->id)){
            //attach serveix per linkar dos elements relacionats amb una N-N
            //(es a dir, posar una fila a la taula intermitja concurs_poni)
            $concurs->ponis()->attach($poni->id);
        }

        return redirect()->route('ponis.ranking');
    }
}
