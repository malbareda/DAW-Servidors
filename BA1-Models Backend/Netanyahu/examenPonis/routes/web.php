<?php

use App\Http\Controllers\ConcursController;
use App\Http\Controllers\PoniController;
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
});

//Les rutes de l'examen van FORA del grup auth: /ponis/meus ha de funcionar sense ningu loguejat (llista buida)
Route::get('/ponis/nou', [PoniController::class, 'nou'])->name('ponis.nou');
Route::post('/ponis/nou', [PoniController::class, 'store'])->name('ponis.store');
Route::get('/ponis/propietari/{idPoni}/{idUsuari}', [PoniController::class, 'propietari'])->name('ponis.propietari');
Route::get('/ponis', [PoniController::class, 'index'])->name('ponis.index');
Route::get('/ponis/fitxa/{idPoni}', [PoniController::class, 'fitxa'])->name('ponis.fitxa');

Route::get('/ponis/aniversari/{idPoni}', [PoniController::class, 'aniversari'])->name('ponis.aniversari');
Route::get('/concursos/inscriure/{idConcurs}/{idPoni}', [ConcursController::class, 'inscriure'])->name('concursos.inscriure');
Route::get('/ponis/ranking', [PoniController::class, 'ranking'])->name('ponis.ranking');
Route::get('/ponis/meus', [PoniController::class, 'meus'])->name('ponis.meus');

require __DIR__.'/auth.php';
