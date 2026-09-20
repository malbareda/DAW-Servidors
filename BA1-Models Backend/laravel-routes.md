# Rutes a Laravel

## Introducció

Les rutes connecten les URLs de la teva aplicació amb el codi que les gestiona. Quan un usuari visita `http://sonafest.test/artistes`, Laravel mira les rutes definides, troba quina coincideix, i executa el controller o funció associada.

**On es defineixen?** Al fitxer `routes/web.php` (per a pàgines web amb sessions i cookies). Hi ha altres fitxers de rutes (`api.php`, `console.php`), però per ara ens centrem en `web.php`.

## Rutes Bàsiques

### Ruta simple amb funció

```php
// routes/web.php

// Ruta GET que retorna una vista directament
Route::get('/benvingut', function () {
    return view('benvingut');
});

// Ruta GET amb paràmetre
Route::get('/artistes/{id}', function ($id) {
    return "Artista amb ID: " . $id;
});
```

Però gairebé sempre volem que les rutes apuntin a un controller, no a una funció anònima.

### Ruta que apunta a un Controller

```php
// Apunta al mètode index() del ArtistaController
Route::get('/artistes', [ArtistaController::class, 'index']);
```

**Sintaxi**: `Route::verb('/url', [Controller::class, 'mètode'])`.

### Verbs HTTP disponibles

```php
Route::get('/artistes', [ArtistaController::class, 'index']);      // Llistar / Mostrar
Route::post('/artistes', [ArtistaController::class, 'store']);     // Crear (desar)
Route::get('/artistes/{artista}', [ArtistaController::class, 'show']);   // Veure un
Route::put('/artistes/{artista}', [ArtistaController::class, 'update']); // Actualitzar
Route::delete('/artistes/{artista}', [ArtistaController::class, 'destroy']); // Eliminar
```

## Route::resource — El Drecera Màgica

Definir les 7 rutes CRUD una per una és repetitiu. `Route::resource` ho fa tot amb una sola línia:

```php
Route::resource('artistes', ArtistaController::class);
```

Aquesta línia genera **7 rutes automàticament**:

| Verb HTTP | URI | Mètode del Controller | Nom de la Ruta | Què fa |
|---|---|---|---|---|
| GET | `/artistes` | `index()` | `artistes.index` | Llistar tots |
| GET | `/artistes/create` | `create()` | `artistes.create` | Formulari de crear |
| POST | `/artistes` | `store()` | `artistes.store` | Desar un nou |
| GET | `/artistes/{artista}` | `show()` | `artistes.show` | Mostrar un |
| GET | `/artistes/{artista}/edit` | `edit()` | `artistes.edit` | Formulari d'editar |
| PUT/PATCH | `/artistes/{artista}` | `update()` | `artistes.update` | Actualitzar |
| DELETE | `/artistes/{artista}` | `destroy()` | `artistes.destroy` | Eliminar |

> **Per veure totes les rutes**: `php artisan route:list`. Aquesta comanda és la vostra millor amiga — si alguna cosa no funciona, comproveu que la ruta existeix.

### Crear el Controller per a resource

```bash
php artisan make:controller ArtistaController --resource
```

El flag `--resource` genera el controller amb els 7 mètodes buits (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`).

### Limitar les rutes d'un resource

Si no necessites totes les 7 rutes:

```php
// Només index, show i store
Route::resource('artistes', ArtistaController::class)
    ->only(['index', 'show', 'store']);

// Totes EXCEPTE destroy
Route::resource('artistes', ArtistaController::class)
    ->except(['destroy']);
```

## Noms de Rutes (Named Routes)

Cada ruta pot tenir un nom. Amb `Route::resource`, els noms es generen automàticament (`artistes.index`, `artistes.store`, etc.). Per a rutes manuals:

```php
Route::get('/sobre-nosaltres', function () {
    return view('about');
})->name('about');
```

### El helper route()

Els noms de ruta serveixen per generar URLs sense hardcodejar els paths. Això és **fonamental** a les vistes Blade:

```php
// Al controller
return redirect()->route('artistes.index');

// A les vistes Blade
<a href="{{ route('artistes.index') }}">Llista d'artistes</a>
<a href="{{ route('artistes.show', $artista) }}">{{ $artista->nom }}</a>
<a href="{{ route('artistes.edit', $artista) }}">Editar</a>

// Al formulari
<form action="{{ route('artistes.store') }}" method="POST">
<form action="{{ route('artistes.update', $artista) }}" method="POST">
```

> **Per què noms i no URLs directes?** Si un dia canvies `/artistes` per `/artistas` o `/artists`, només canvies la definició de la ruta. Totes les vistes que fan servir `route('artistes.index')` seguiran funcionant sense tocar res. Si haguéssiu hardcodejat `/artistes` per tot arreu, hauríeu de canviar-ho fitxer per fitxer.

### Passar paràmetres a route()

```php
// Ruta amb un paràmetre
route('artistes.show', $artista)           // /artistes/5 (passa l'ID de l'objecte)
route('artistes.show', ['artista' => 5])   // /artistes/5 (passa l'ID explícitament)

// Ruta amb múltiples paràmetres (rutes niuades)
route('concerts.entrades.store', [$concert])  // /concerts/3/entrades
```

## Grups de Rutes amb Middleware

El middleware és codi que s'executa ABANS (o DESPRÉS) d'una ruta. L'ús més comú: restringir l'accés a usuaris autenticats.

### Protegir rutes amb auth

```php
// Totes aquestes rutes requereixen login
Route::middleware(['auth'])->group(function () {
    Route::resource('artistes', ArtistaController::class);
    Route::resource('concerts', ConcertController::class);
    Route::resource('escenaris', EscenariController::class);
});

// Rutes públiques (sense auth)
Route::get('/', function () {
    return view('welcome');
});
```

Qualsevol usuari no autenticat que intenti accedir a una ruta dins del grup serà redirigit automàticament a la pàgina de login.

### Múltiples middlewares

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Només usuaris autenticats I amb email verificat
});
```

### Prefix de URL

```php
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('artistes', ArtistaController::class);
    // Genera URLs com /admin/artistes, /admin/artistes/create, etc.
});
```

## Rutes Niuades (Nested Routes)

Quan un recurs "pertany" a un altre, podem crear rutes niuades. Per exemple, comprar una entrada per a un concert concret:

### Opció 1: Ruta manual niuada

```php
// POST /concerts/{concert}/comprar
Route::post('/concerts/{concert}/comprar', [EntradaController::class, 'comprar'])
    ->name('concerts.comprar')
    ->middleware('auth');
```

Al controller:

```php
public function comprar(Concert $concert)
{
    // $concert ja conté el concert correcte gràcies al Route Model Binding
    // (Laravel busca automàticament el Concert amb l'ID de la URL)
    
    $entrada = Entrada::create([
        'user_id' => auth()->id(),
        'concert_id' => $concert->id,
        'preu_pagat' => $concert->preu,
    ]);
    
    return redirect()->route('concerts.show', $concert)
                     ->with('success', 'Entrada comprada!');
}
```

### Opció 2: Resource niuat

```php
// Genera rutes com /concerts/{concert}/entrades, /concerts/{concert}/entrades/{entrada}
Route::resource('concerts.entrades', EntradaController::class);
```

Això genera rutes amb dos paràmetres. Al controller:

```php
public function index(Concert $concert)
{
    $entrades = $concert->entrades;
    return view('entrades.index', compact('concert', 'entrades'));
}
```

## Route Model Binding

Quan una ruta té un paràmetre com `{artista}`, Laravel pot buscar automàticament el model:

```php
// routes/web.php
Route::get('/artistes/{artista}', [ArtistaController::class, 'show']);

// ArtistaController.php
public function show(Artista $artista)  // ← Type-hint amb el model
{
    // $artista ja és l'objecte Artista amb l'ID de la URL
    // Si no existeix, Laravel retorna un 404 automàticament
    return view('artistes.show', compact('artista'));
}
```

**Com funciona?** Si la URL és `/artistes/5`, Laravel fa `Artista::findOrFail(5)` automàticament. Si l'artista 5 no existeix → 404.

> **Requisit**: el nom del paràmetre a la ruta (`{artista}`) ha de coincidir amb el nom de la variable al controller (`$artista`).

## Exemple Complet: routes/web.php del SonaFest

```php
<?php

use App\Http\Controllers\ArtistaController;
use App\Http\Controllers\ConcertController;
use App\Http\Controllers\EscenariController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\EstadistiquesController;
use Illuminate\Support\Facades\Route;

// Pàgina principal (pública)
Route::get('/', function () {
    return view('welcome');
});

// Rutes protegides (requereixen login)
Route::middleware(['auth'])->group(function () {
    
    // CRUD complet per a cada recurs
    Route::resource('artistes', ArtistaController::class);
    Route::resource('concerts', ConcertController::class);
    Route::resource('escenaris', EscenariController::class);
    Route::resource('generes', GenereController::class);
    
    // Comprar entrada per a un concert
    Route::post('/concerts/{concert}/comprar', [EntradaController::class, 'comprar'])
        ->name('concerts.comprar');
    
    // Estadístiques (pàgina custom, no és CRUD)
    Route::get('/estadistiques', [EstadistiquesController::class, 'index'])
        ->name('estadistiques');
});

// Rutes d'autenticació (generades per Breeze)
require __DIR__ . '/auth.php';
```

## Comandes Útils

```bash
# Veure totes les rutes definides
php artisan route:list

# Filtrar rutes per nom
php artisan route:list --name=artistes

# Netejar la cache de rutes
php artisan route:clear

# Cachear les rutes (per a producció)
php artisan route:cache
```

## Errors Comuns i Solucions

1. **404 Not Found** — La ruta no existeix. Comprova amb `php artisan route:list`. Has importat el controller amb `use`?

2. **405 Method Not Allowed** — El verb HTTP no coincideix. Per exemple, envies un POST a una ruta GET. Comprova el `method` del formulari i que tinguis `@method('PUT')` si cal.

3. **419 Page Expired** — Falta `@csrf` al formulari.

4. **L'ordre de les rutes importa** — Laravel busca la primera ruta que coincideix. Si poses una ruta amb `{id}` abans d'una amb un text fix, la `{id}` la capturarà:

   ```php
   // MAL — /artistes/create serà capturat com {artista} = "create"
   Route::get('/artistes/{artista}', [ArtistaController::class, 'show']);
   Route::get('/artistes/create', [ArtistaController::class, 'create']);
   
   // BÉ — la ruta fixa va primer
   Route::get('/artistes/create', [ArtistaController::class, 'create']);
   Route::get('/artistes/{artista}', [ArtistaController::class, 'show']);
   ```
   
   > Amb `Route::resource` això no és problema perquè Laravel ordena les rutes correctament.

5. **Route Model Binding no funciona** — El nom del paràmetre `{artista}` no coincideix amb la variable `$artista` al controller. O el type-hint no és el model correcte.

## Xuleta de Rutes

| Codi | Resultat |
|---|---|
| `Route::get('/url', [Controller::class, 'mètode'])` | Ruta GET manual |
| `Route::resource('nom', Controller::class)` | 7 rutes CRUD automàtiques |
| `->name('nom')` | Assignar nom a una ruta |
| `route('nom', $model)` | Generar URL a partir del nom |
| `Route::middleware(['auth'])->group(...)` | Protegir rutes amb middleware |
| `Route::prefix('admin')->group(...)` | Afegir prefix a les URLs |
| `{model}` a la ruta + type-hint al controller | Route Model Binding automàtic |
| `php artisan route:list` | Veure totes les rutes |
