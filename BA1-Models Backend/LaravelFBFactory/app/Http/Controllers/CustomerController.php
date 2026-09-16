<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{
    public function linkUserToCustomer(Customer $customer, User $user)
    {



        $customer->user_id = $user->id;
        //també podries assignar l'objecte: $customer->user()->associate($user);
        $customer->save();
        //redirect amb nom de ruta, com a django
        return redirect()->route('customer.myFemboys');
    }

    public function myFemboys()
    {
        //el user actual que esta logueado en formato objeto User
        $user = Auth::user();
        dd($user->customer);
        $customer = $user->customer;

        if(!$customer){
            return redirect()->route('home')->with('error','no eres un customer oficial');
        }

        $femboys = $customer->femboys;

        return view('customer.myFemboys', compact('femboys'));

    }
}
