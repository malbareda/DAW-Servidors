# Controllers, Requests i Validació a Laravel

## Introducció

Un **controller** és la classe PHP que rep les peticions HTTP i decideix què fer: validar les dades, interactuar amb els models, i retornar una resposta (normalment una vista Blade o un redirect). Si les rutes són el "mapa" de la teva app, els controllers són els "operaris" que fan la feina.

## Crear un Controller

```bash
# Controller buit
php artisan make:controller ArtistaController

# Controller amb els 7 mètodes CRUD (recomanat per a resources)
php artisan make:controller ArtistaController --resource

# Controller amb model (injecta el model automàticament)
php artisan make:controller ArtistaController --resource --model=Artista
```

## Estructura d'un Resource Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use Illuminate\Http\Request;

class ArtistaController extends Controller
{
    // GET /artistes — Llistar tots els artistes
    public function index()
    {
        $artistes = Artista::all();
        return view('artistes.index', compact('artistes'));
    }

    // GET /artistes/create — Mostrar formulari de creació
    public function create()
    {
        return view('artistes.create');
    }

    // POST /artistes — Desar un nou artista
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'pais' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'actiu' => 'boolean',
        ]);

        Artista::create($validated);

        return redirect()->route('artistes.index')
                         ->with('success', 'Artista creat correctament!');
    }

    // GET /artistes/{artista} — Mostrar un artista
    public function show(Artista $artista)
    {
        return view('artistes.show', compact('artista'));
    }

    // GET /artistes/{artista}/edit — Mostrar formulari d'edició
    public function edit(Artista $artista)
    {
        return view('artistes.edit', compact('artista'));
    }

    // PUT /artistes/{artista} — Actualitzar un artista
    public function update(Request $request, Artista $artista)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'pais' => 'required|string|max:100',
            'bio' => 'nullable|string',
            'actiu' => 'boolean',
        ]);

        $artista->update($validated);

        return redirect()->route('artistes.index')
                         ->with('success', 'Artista actualitzat correctament!');
    }

    // DELETE /artistes/{artista} — Eliminar un artista
    public function destroy(Artista $artista)
    {
        $artista->delete();

        return redirect()->route('artistes.index')
                         ->with('success', 'Artista eliminat correctament.');
    }
}
```

> **Nota**: Gràcies al Route Model Binding, quan el mètode rep `Artista $artista`, Laravel automàticament busca l'artista amb l'ID de la URL. Si no existeix, retorna 404.

## L'Objecte Request

El `Request` conté tota la informació de la petició HTTP: les dades del formulari, els paràmetres de la URL, els headers, els fitxers pujats, etc.

### Accedir a les dades

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    // Obtenir un camp concret
    $nom = $request->input('nom');
    
    // Amb valor per defecte si no existeix
    $actiu = $request->input('actiu', true);
    
    // Obtenir TOTES les dades del formulari
    $totes = $request->all();
    
    // Obtenir només alguns camps (útil per seguretat)
    $dades = $request->only(['nom', 'pais', 'bio']);
    
    // Obtenir tot EXCEPTE alguns camps
    $dades = $request->except(['_token', '_method']);
}
```

### Comprovar si un camp existeix

```php
// Existeix al request? (fins i tot si és buit)
if ($request->has('nom')) {
    // ...
}

// Existeix I no és buit? (string no buit, no null)
if ($request->filled('nom')) {
    // ...
}

// Comprovar múltiples camps alhora
if ($request->has(['nom', 'pais'])) {
    // Els DOS existeixen
}
```

> **`has()` vs `filled()`**: `has()` retorna `true` fins i tot si el valor és una cadena buida `""`. `filled()` retorna `true` només si el valor realment conté alguna cosa. Per a filtres i cerques, `filled()` és gairebé sempre el que voleu.

### Obtenir l'usuari autenticat

```php
public function store(Request $request)
{
    // L'usuari que ha enviat el formulari
    $user = $request->user();
    // o bé
    $user = auth()->user();
    
    // L'ID de l'usuari
    $userId = auth()->id();
}
```

## Validació

La validació és **imprescindible**. Mai us fieu de les dades que arriben d'un formulari — sempre valideu al controller.

### Validació inline (la més ràpida)

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'nom'           => 'required|string|max:255',
        'pais'          => 'required|string|max:100',
        'bio'           => 'nullable|string',
        'imatge_url'    => 'nullable|url',
        'actiu'         => 'boolean',
        'data_hora'     => 'required|date|after:now',
        'durada_minuts' => 'required|integer|min:15|max:480',
        'tipus'         => 'required|in:CONCERT,DJ_SET,SHOWCASE,ACUSTIC',
        'escenari_id'   => 'required|exists:escenaris,id',
        'preu'          => 'required|numeric|min:0|max:999.99',
        'generes'       => 'required|array|min:1',
        'generes.*'     => 'exists:generes,id',
    ]);

    // Si la validació falla, Laravel automàticament:
    // 1. Redirigeix de tornada al formulari
    // 2. Envia els errors a la vista (accessibles amb @error)
    // 3. Envia les dades antigues (accessibles amb old())

    // Si la validació passa, $validated conté NOMÉS els camps validats
    Artista::create($validated);
}
```

### Regles de validació habituals

| Regla | Què valida | Exemple |
|---|---|---|
| `required` | El camp no pot ser buit | `'nom' => 'required'` |
| `nullable` | Permet valors null | `'bio' => 'nullable\|string'` |
| `string` | Ha de ser un string | `'nom' => 'string'` |
| `integer` | Ha de ser un enter | `'edat' => 'integer'` |
| `numeric` | Ha de ser numèric (enter o decimal) | `'preu' => 'numeric'` |
| `boolean` | Ha de ser true/false (o 1/0) | `'actiu' => 'boolean'` |
| `date` | Ha de ser una data vàlida | `'data' => 'date'` |
| `after:data` | La data ha de ser posterior a... | `'data' => 'after:now'` |
| `before:data` | La data ha de ser anterior a... | `'data' => 'before:2025-12-31'` |
| `min:N` | Valor mínim (numèric) o longitud mínima (string) | `'preu' => 'min:0'` |
| `max:N` | Valor màxim (numèric) o longitud màxima (string) | `'nom' => 'max:255'` |
| `in:a,b,c` | Ha de ser un dels valors llistats | `'tipus' => 'in:A,B,C'` |
| `exists:taula,columna` | Ha d'existir a la base de dades | `'user_id' => 'exists:users,id'` |
| `unique:taula,columna` | No ha d'existir encara a la BD | `'email' => 'unique:users,email'` |
| `email` | Ha de ser un email vàlid | `'email' => 'email'` |
| `url` | Ha de ser una URL vàlida | `'web' => 'url'` |
| `array` | Ha de ser un array | `'generes' => 'array'` |
| `confirmed` | Ha de tenir un camp `_confirmation` igual | `'password' => 'confirmed'` |

### Validació d'arrays (relacions N:M)

Quan envies checkboxes amb `name="generes[]"`:

```php
$validated = $request->validate([
    'generes'   => 'required|array|min:1',        // L'array és obligatori i ha de tenir almenys 1 element
    'generes.*' => 'exists:generes,id',            // Cada element ha d'existir a la taula generes
]);
```

El `.*` significa "cada element de l'array".

### Unique amb excepció (per a editar)

Quan edites un registre, la regla `unique` fallarà perquè trobarà el registre actual. Cal excloure'l:

```php
public function update(Request $request, Artista $artista)
{
    $validated = $request->validate([
        'email' => 'required|email|unique:artistes,email,' . $artista->id,
        // Diu: unique a la taula artistes, columna email, EXCEPTE l'ID actual
    ]);
}
```

### Missatges d'error en català

```php
$validated = $request->validate([
    'nom' => 'required|string|max:255',
    'pais' => 'required|string',
], [
    'nom.required' => 'El nom és obligatori.',
    'nom.max' => 'El nom no pot tenir més de 255 caràcters.',
    'pais.required' => 'El país és obligatori.',
]);
```

> **Alternativa global**: podeu traduir els missatges editant `lang/ca/validation.php`. Així no cal repetir-los a cada controller.

## Redirects (Redireccions)

Després de processar un formulari (store/update/delete), gairebé sempre volem redirigir l'usuari a una altra pàgina, no retornar una vista directament.

### Tipus de redirects

```php
// Redirigir a una ruta amb nom
return redirect()->route('artistes.index');

// Redirigir a una ruta amb paràmetre
return redirect()->route('artistes.show', $artista);

// Redirigir enrere (a la pàgina anterior — útil quan la validació falla)
return back();

// Redirigir a una URL concreta
return redirect('/artistes');
```

### Redirects amb dades flash

```php
// Enviar un missatge d'èxit
return redirect()->route('artistes.index')
                 ->with('success', 'Artista creat correctament!');

// Enviar un missatge d'error
return redirect()->route('artistes.index')
                 ->with('error', 'No s\'ha pogut crear l\'artista.');

// Redirigir enrere amb errors de validació (això ho fa Laravel automàticament,
// però si vols fer-ho manualment):
return back()->withErrors(['nom' => 'Aquest nom ja existeix.'])
             ->withInput();  // conserva les dades del formulari
```

**`->withInput()`** fa que `old('camp')` funcioni a la vista — conserva el que l'usuari havia escrit perquè no ho hagi de tornar a omplir tot.

## Passar Dades a les Vistes

### Formes equivalents

```php
// Amb compact() — la més comuna
return view('artistes.create', compact('generes', 'escenaris'));

// Amb array associatiu
return view('artistes.create', [
    'generes' => $generes,
    'escenaris' => $escenaris,
]);

// Amb ->with()
return view('artistes.create')
    ->with('generes', $generes)
    ->with('escenaris', $escenaris);
```

`compact('generes')` és equivalent a `['generes' => $generes]`. Més curt.

## Exemple Complet: ConcertController amb Validació

```php
<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\Artista;
use App\Models\Escenari;
use Illuminate\Http\Request;

class ConcertController extends Controller
{
    public function index(Request $request)
    {
        // Consulta base
        $query = Concert::with(['artista', 'escenari']);

        // Filtratge condicional (Part avançada de la Pràctica)
        if ($request->filled('artista_id')) {
            $query->where('artista_id', $request->input('artista_id'));
        }

        if ($request->filled('tipus')) {
            $query->where('tipus', $request->input('tipus'));
        }

        if ($request->filled('data_inici') && $request->filled('data_fi')) {
            $query->whereBetween('data_hora', [
                $request->input('data_inici'),
                $request->input('data_fi'),
            ]);
        }

        $concerts = $query->orderBy('data_hora')->get();
        $artistes = Artista::orderBy('nom')->get();

        return view('concerts.index', compact('concerts', 'artistes'));
    }

    public function create()
    {
        $artistes = Artista::where('actiu', true)->orderBy('nom')->get();
        $escenaris = Escenari::orderBy('nom')->get();

        return view('concerts.create', compact('artistes', 'escenaris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'           => 'required|string|max:255',
            'artista_id'    => 'required|exists:artistes,id',
            'escenari_id'   => 'required|exists:escenaris,id',
            'data_hora'     => 'required|date|after:now',
            'durada_minuts' => 'required|integer|min:15|max:480',
            'tipus'         => 'required|in:CONCERT,DJ_SET,SHOWCASE,ACUSTIC',
        ]);

        // Validació de negoci: comprovar aforament (Part avançada)
        $escenari = Escenari::find($validated['escenari_id']);
        $concertsAlMateixTemps = Concert::where('escenari_id', $escenari->id)
            ->where('data_hora', $validated['data_hora'])
            ->count();

        if ($concertsAlMateixTemps > 0) {
            return back()->withErrors([
                'escenari_id' => 'Ja hi ha un concert a aquest escenari a la mateixa hora.',
            ])->withInput();
        }

        Concert::create($validated);

        return redirect()->route('concerts.index')
                         ->with('success', 'Concert creat correctament!');
    }

    public function show(Concert $concert)
    {
        // Carregar relacions per evitar N+1
        $concert->load(['artista', 'escenari', 'entrades.user']);

        return view('concerts.show', compact('concert'));
    }

    public function edit(Concert $concert)
    {
        $artistes = Artista::where('actiu', true)->orderBy('nom')->get();
        $escenaris = Escenari::orderBy('nom')->get();

        return view('concerts.edit', compact('concert', 'artistes', 'escenaris'));
    }

    public function update(Request $request, Concert $concert)
    {
        $validated = $request->validate([
            'nom'           => 'required|string|max:255',
            'artista_id'    => 'required|exists:artistes,id',
            'escenari_id'   => 'required|exists:escenaris,id',
            'data_hora'     => 'required|date',
            'durada_minuts' => 'required|integer|min:15|max:480',
            'tipus'         => 'required|in:CONCERT,DJ_SET,SHOWCASE,ACUSTIC',
        ]);

        $concert->update($validated);

        return redirect()->route('concerts.index')
                         ->with('success', 'Concert actualitzat correctament!');
    }

    public function destroy(Concert $concert)
    {
        $concert->delete();

        return redirect()->route('concerts.index')
                         ->with('success', 'Concert eliminat correctament.');
    }
}
```

## Filtratge Combinat amb $request->filled()

Un patró molt útil per a la part avançada de la pràctica: construir una consulta dinàmicament segons els filtres que l'usuari ha omplert.

```php
public function index(Request $request)
{
    $query = Concert::with(['artista', 'escenari']);

    // Cada filtre s'aplica NOMÉS si l'usuari ha omplert el camp
    if ($request->filled('artista_id')) {
        $query->where('artista_id', $request->input('artista_id'));
    }

    if ($request->filled('tipus')) {
        $query->where('tipus', $request->input('tipus'));
    }

    if ($request->filled('escenari_id')) {
        $query->where('escenari_id', $request->input('escenari_id'));
    }

    if ($request->filled('cerca')) {
        $query->where('nom', 'like', '%' . $request->input('cerca') . '%');
    }

    $concerts = $query->orderBy('data_hora')->paginate(20);

    return view('concerts.index', compact('concerts'));
}
```

> **Per què `filled()` i no `has()`?** Si l'usuari envia el formulari de filtres sense omplir un camp, `has()` retornaria `true` (el camp existeix com a cadena buida), i filtraríem amb un valor buit. `filled()` retorna `true` només si realment hi ha un valor.

### El formulari de filtres a la vista

```html
<form action="{{ route('concerts.index') }}" method="GET" class="flex gap-4 mb-6">
    <!-- Filtre per artista -->
    <select name="artista_id" class="border rounded p-2">
        <option value="">Tots els artistes</option>
        @foreach($artistes as $artista)
            <option value="{{ $artista->id }}" 
                    {{ request('artista_id') == $artista->id ? 'selected' : '' }}>
                {{ $artista->nom }}
            </option>
        @endforeach
    </select>

    <!-- Filtre per tipus -->
    <select name="tipus" class="border rounded p-2">
        <option value="">Tots els tipus</option>
        @foreach(['CONCERT', 'DJ_SET', 'SHOWCASE', 'ACUSTIC'] as $tipus)
            <option value="{{ $tipus }}" {{ request('tipus') == $tipus ? 'selected' : '' }}>
                {{ $tipus }}
            </option>
        @endforeach
    </select>

    <!-- Cerca per nom -->
    <input type="text" name="cerca" placeholder="Cercar concert..."
           value="{{ request('cerca') }}" class="border rounded p-2">

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filtrar</button>
    <a href="{{ route('concerts.index') }}" class="text-gray-500 px-4 py-2">Netejar</a>
</form>
```

> **`request('camp')`** a Blade: retorna el valor del query string actual. Serveix per mantenir els filtres seleccionats després d'enviar el formulari (com `old()` però per a GET).

## Sync per a Relacions N:M

Quan deses o actualitzes gèneres d'un artista:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'generes' => 'required|array|min:1',
        'generes.*' => 'exists:generes,id',
    ]);

    // Crear l'artista (sense els gèneres, que van a la taula pivot)
    $artista = Artista::create($request->except('generes'));

    // Sincronitzar la relació N:M
    $artista->generes()->sync($request->input('generes'));

    return redirect()->route('artistes.index')
                     ->with('success', 'Artista creat correctament!');
}

public function update(Request $request, Artista $artista)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'generes' => 'required|array|min:1',
        'generes.*' => 'exists:generes,id',
    ]);

    $artista->update($request->except('generes'));

    // sync() actualitza la taula pivot:
    // - Afegeix els nous
    // - Elimina els que ja no hi són
    // - Manté els que no canvien
    $artista->generes()->sync($request->input('generes'));

    return redirect()->route('artistes.index')
                     ->with('success', 'Artista actualitzat!');
}
```

**`sync()` vs `attach()` vs `detach()`**:

| Mètode | Què fa | Quan usar-lo |
|---|---|---|
| `sync([1, 2, 3])` | Fa que la relació sigui EXACTAMENT [1,2,3] | Actualitzar checkboxes — el més comú |
| `attach([4, 5])` | Afegeix 4 i 5 als existents | Afegir sense tocar els existents |
| `detach([2])` | Treu el 2 dels existents | Treure un element concret |
| `detach()` | Treu TOTS | Netejar tota la relació |

## Errors Comuns

1. **"Add [nom] to fillable property to allow mass assignment"** — El camp que intentes guardar no està a `$fillable` del model.

2. **Validació passa però les dades no es guarden** — Comprova que fas servir `$validated` (el resultat de `validate()`) i no `$request->all()`, o bé que el camp està a `$fillable`.

3. **El formulari d'edició no mostra les dades actuals** — No passes el model a la vista o no fas servir `old('camp', $model->camp)`.

4. **redirect() no funciona (pàgina en blanc)** — T'has oblidat el `return` davant del `redirect()`.

5. **Les dades del formulari es perden després d'un error de validació** — Laravel ho fa automàticament amb `$request->validate()`, però si fas la validació manualment, has d'afegir `->withInput()` al redirect.

6. **Error "Attempt to read property on null"** — Intentes accedir a una relació que no existeix (ex: `$concert->artista->nom` quan `artista_id` és null). Solució: comprova amb `$concert->artista?->nom` (nullsafe operator de PHP 8).
