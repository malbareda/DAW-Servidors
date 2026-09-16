<?php

use App\Http\Controllers\FilmController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/films/create',        [FilmController::class, 'create']);
Route::post('/films/create',       [FilmController::class, 'store']);
Route::get('/films/delete/{idFilm}', [FilmController::class, 'destroy']);
Route::get('/films/edit/{idFilm}', [FilmController::class, 'edit']);
Route::post('/films/edit/{idFilm}', [FilmController::class, 'update']);
Route::get('/films/noAdmin',       [FilmController::class, 'noAdmin']);

// --- VISUALITZACIONS ---
Route::get('/visualitzacions',                    [\App\Http\Controllers\VisualitzacioController::class, 'index']);
Route::get('/visualitzacions/{idFilm}/{idUser}',  [\App\Http\Controllers\VisualitzacioController::class, 'store']);
Route::get('/finish/{idView}',                    [\App\Http\Controllers\VisualitzacioController::class, 'finish']);

// --- ADMINISTRADORS ---
Route::get('/admin/assign/{idFilm}/{idAdministrator}', [\App\Http\Controllers\AdministradorController::class, 'assign']);
