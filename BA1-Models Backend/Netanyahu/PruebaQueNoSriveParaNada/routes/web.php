<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\EjercitoController;

Route::get('/', function () {
});

//ROUTE:get o post, depende, (URL, CONTROLLER, 'FUNCION DEL CONTROLER')->(NOMBRE PARA REFERENCIARLO MAS TARDE DESDE UNA VISTA)
Route::get('/ciudades', [CiudadController::class, 'index'])->name('ciudades.index');
Route::get('/bombardear/{nombre}', [CiudadController::class, 'bombardear'])->name('ciudades.bombardear');
Route::get('/ciudades/create', [CiudadController::class, 'create'])->name('ciudades.create');
Route::post('/ciudades', [CiudadController::class, 'store'])->name('ciudades.store');
Route::get('/ciudades/{ciudad}', [CiudadController::class, 'show'])->name('ciudades.show');
Route::get('/ciudades/{ciudad}/edit', [CiudadController::class, 'edit'])->name('ciudades.edit');
Route::put('/ciudades/{ciudad}', [CiudadController::class, 'update'])->name('ciudades.update');
Route::delete('/ciudades/{ciudad}', [CiudadController::class, 'destroy'])->name('ciudades.destroy');

Route::get('/ejercitos', [EjercitoController::class, 'index'])->name('ejercitos.index');
Route::get('ejercitos/{ejercito}/invadir', [EjercitoController::class, 'invadirFormulario'])->name('ejercitos.invadir');
Route::post('ejercitos/{ejercito}/invadir', [EjercitoController::class, 'invadir'])->name('ejercitos.invadido');
