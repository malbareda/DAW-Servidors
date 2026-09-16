<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Prestec;
use Illuminate\Http\Request;

class PrestecController extends Controller
{
    //
    public function create($idLlibre, $idUsuari){

        $this->authorize('create', Prestec::class);
        Prestec::create([
            'llibre_id' => $idLlibre,
            'user_id' => $idUsuari,
            'actiu' => true,
            'data' => now()
        ]);

        $prestecQueAcaboDafegir = Prestec::where('llibre_id', $idLlibre)->where('user_id', $idUsuari)->first();

        return response()->json($prestecQueAcaboDafegir);
    }

    public function retornar($idPrestec){
        $prestec = Prestec::find($idPrestec);


        if($prestec->actiu && Auth::id() == $prestec->user_id ){
            $prestec->actiu = false;
            $prestec->save();

        }
    }
}
