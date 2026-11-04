<?php

namespace App\Http\Controllers;

use App\Models\Llibre;
use App\Models\Prestec;
use App\Models\User;
use Illuminate\Http\Request;

class PrestecController extends Controller
{
    public function crear($idLlibre,$idUsuari){
        $llibre = Llibre::findOrFail($idLlibre);
        $usuari = User::findOrFail($idUsuari);

        Prestec::create([
            'actiu'=>true,
            'data'=>now(),
            'llibre_id'=>$llibre->id,
            'user_id'=>$usuari->id,
        ]);

        return redirect()->route('llibres.cataleg');

    }

    public function retornar($idPrestec){
        $prestec = Prestec::findOrFail($idPrestec);

        if($prestec->user_id == auth()->id() && $prestec->actiu){
            $prestec->actiu=false;
            $prestec->save();
        }

        return redirect()->route('llibres.cataleg');
    }
}
