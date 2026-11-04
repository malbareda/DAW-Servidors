<?php

use App\Http\Controllers\LlibreController;
use App\Http\Controllers\PrestecController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/llibres/create', [LlibreController::class, 'create'])->name('llibres.create');
    Route::post('/llibres/create', [LlibreController::class, 'store'])->name('llibres.store');
    Route::get('/llibres/modificar/{idLlibre}', [LlibreController::class, 'edit'])->name('llibres.edit');
    Route::post('/llibres/modificar/{idLlibre}', [LlibreController::class, 'update'])->name('llibres.update');

    Route::get('/llibres/cataleg', [LlibreController::class, 'cataleg'])->name('llibres.cataleg');
    Route::get('/llibres/eliminar/{idLlibre}', [LlibreController::class, 'eliminar'])->name('llibres.eliminar');

    Route::get('/llibres/senseprestecs', [LlibreController::class, 'senseprestecs'])->name('llibres.senseprestecs');

    Route::get('/prestecs/{idLlibre}/{idUsuari}', [PrestecController::class, 'crear'])->name('prestecs.crear');
    Route::get('/retornar/{idPrestec}', [PrestecController::class, 'retornar'])->name('prestecs.retornar');

    Route::get('/bibliotecari/assignar/{idLlibre}/{idBibliotecari}', [LlibreController::class, 'assignar'])->name('llibres.assignar');


});

require __DIR__.'/auth.php';
