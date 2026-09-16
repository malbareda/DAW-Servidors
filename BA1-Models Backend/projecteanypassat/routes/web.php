<?php

use App\Http\Controllers\LlibreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PrestecController;
use App\Http\Controllers\BibliotecariController;




Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get("/llibres/create", [LlibreController::class, 'create'])->name('llibres.create');
    Route::post("/llibres/create", [LlibreController::class, 'store'])->name('llibres.store');

    Route::get("/prestecs/{idLlibre}/{idUsuari}", [\App\Http\Controllers\PrestecController::class, 'create'])->name('prestecs.create');

    Route::get("/llibres/cataleg", [LlibreController::class, 'cataleg'])->name('llibres.cataleg');

    Route::get("/llibres/eliminar/{idLlibre}", [LlibreController::class, 'eliminar'])->name('llibres.eliminar');

    Route::get("/llibres/modificar/{idLlibre}", [LlibreController::class, 'modificar'])->name('llibres.modificar');
    Route::post("/llibres/modificar/{idLlibre}", [LlibreController::class, 'update'])->name('llibres.update');

    Route::get("/llibres/senseprestecs", [LlibreController::class, 'sensePrestecs'])->name('llibres.senseprestecs');

    Route::get("retornar/{idPrestec}", [PrestecController::class, 'retornar'])->name('prestecs.retornar');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
