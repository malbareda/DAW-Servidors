<?php

use App\Http\Controllers\FemboyController;
use App\Http\Middleware\UserIsAuth;
use Illuminate\Support\Facades\Route;

//Diferencia clau: Model Binding
//si el nom del paràmetre coincideix amb el nom de tipus al controller:
//laravel busca el model amb aquella id: si existeix el retorna com a objecte, si no, 404

Route::get('/', function () {
    return view('welcome');
});

//Rutes de Customer
Route::get('/customer/link/{customer}/{user}', [\App\Http\Controllers\CustomerController::class, 'linkUserToCustomer'])->name('customer.link');
//si vols passar una ruta per el middleware, posa al final ->middleware(ClasseMiddleware::class)
Route::get('/customer/myFemboys', [\App\Http\Controllers\CustomerController::class, 'myFemboys'])->name('customer.myFemboys')->middleware(UserIsAuth::class);

//Rutes de Femboy
Route::get('/femboys', [FemboyController::class, 'all'])->name('femboys.all');
Route::get('/femboys/create', [FemboyController::class, 'create'])->name('femboys.create');
Route::post('/femboys/store', [FemboyController::class, 'store'])->name('femboys.store');
//SI POSEU {nomDeLaClasse} i passeu la id, podreu carregar directament l'objecte de la calsse al controller sense fer el get
Route::get('/femboys/delete/{femboy}', [FemboyController::class, 'destroy'])->name('femboys.destroy');
Route::get('/femboys/edit/{femboy}', [FemboyController::class, 'edit'])->name('femboys.edit')->middleware(UserIsAuth::class);;
//el update és put segons estandar HTTP. Ja que estem ho fem bé.
Route::put('/femboys/update/{femboy}', [FemboyController::class, 'update'])->name('femboys.update');
Route::get('/femboys/estado/{estado}', [FemboyController::class, 'porEstado'])->name('femboys.porEstado');
//recordeu de que si teniu un metode que accepta variable ha d'anar al final, sino es pensaria que el "create" es la variable
Route::get('/femboys/{femboy}', [FemboyController::class, 'show'])->name('femboys.show');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');





/**
 * ==========================================
 * ALTRES TIPUS DE RUTES ÚTILS
 * ==========================================
 *
 * // GRUP DE RUTES AMB PREFIX
 * Route::prefix('admin')->group(function () {
 *     Route::get('/users', ...);     // /admin/users
 *     Route::get('/posts', ...);     // /admin/posts
 * });
 *
 * // GRUP DE RUTES AMB MIDDLEWARE
 * Route::middleware(['auth', 'admin'])->group(function () {
 *     Route::get('/dashboard', ...);
 *     Route::get('/settings', ...);
 * });
 *
 * // RESOURCE ROUTES (CRUD automàtic)
 * Route::resource('femboys', FemboyController::class);
 * // Crea automàticament:
 * // GET    /femboys           → index
 * // GET    /femboys/create    → create
 * // POST   /femboys           → store
 * // GET    /femboys/{id}      → show
 * // GET    /femboys/{id}/edit → edit
 * // PUT    /femboys/{id}      → update
 * // DELETE /femboys/{id}      → destroy
 *
 * // API ROUTES (sense middleware web, amb middleware api)
 * // A routes/api.php:
 * Route::get('/users', [UserController::class, 'index']);
 * // URL real: /api/users (prefix automàtic)
 *
 * // RUTA AMB VALIDACIÓ DE PARÀMETRES
 * Route::get('/user/{id}', ...)->where('id', '[0-9]+');
 * // Només accepta números
 *
 * // RUTA AMB PARÀMETRE OPCIONAL
 * Route::get('/user/{name?}', function ($name = 'Guest') {
 *     return "Hello, $name";
 * });
 *
 * // REDIRECT
 * Route::redirect('/old-url', '/new-url', 301);
 *
 * // VIEW DIRECTA (sense controller)
 * Route::view('/about', 'about', ['title' => 'About Us']);
 */
