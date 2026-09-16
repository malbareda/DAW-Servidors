<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserIsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //si NO estas loguejat
        if(!Auth::check()){
            //redirect
            return redirect()->route('login')->with('error',"Logueate majo");
        }
        //next significa que segueix endavant cap a on et duia la ruta
        return $next($request);
    }
}
