<?php

use App\Http\Controllers\ArtistaController;
use App\Http\Controllers\ConcertController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//TODO LO QUE ESTA DENTRO DE ESTE GRUPO NECESITA ESTAR LOGUEADO
//si no lo estas, el middleware 'auth' te manda al login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //ROUTE:get o post, depende, (URL, CONTROLLER, 'FUNCION DEL CONTROLER')->(NOMBRE PARA REFERENCIARLO MAS TARDE DESDE UNA VISTA)
    //ARTISTES (CRUD)
    //OJO: /artistes/create tiene que ir ANTES que /artistes/{artista}, si no laravel pensaria que 'create' es un artista
    Route::get('/artistes', [ArtistaController::class, 'index'])->name('artistes.index');
    Route::get('/artistes/create', [ArtistaController::class, 'create'])->name('artistes.create');
    Route::post('/artistes', [ArtistaController::class, 'store'])->name('artistes.store');
    Route::get('/artistes/{artista}', [ArtistaController::class, 'show'])->name('artistes.show');
    Route::get('/artistes/{artista}/edit', [ArtistaController::class, 'edit'])->name('artistes.edit');
    Route::put('/artistes/{artista}', [ArtistaController::class, 'update'])->name('artistes.update');
    Route::delete('/artistes/{artista}', [ArtistaController::class, 'destroy'])->name('artistes.destroy');

    //CONCERTS (solo index, create, store y show)
    Route::get('/concerts', [ConcertController::class, 'index'])->name('concerts.index');
    Route::get('/concerts/create', [ConcertController::class, 'create'])->name('concerts.create');
    Route::post('/concerts', [ConcertController::class, 'store'])->name('concerts.store');
    Route::get('/concerts/{concert}', [ConcertController::class, 'show'])->name('concerts.show');
});

require __DIR__.'/auth.php';
