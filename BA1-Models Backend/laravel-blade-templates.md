# Plantilles Blade a Laravel

## Introducció

Blade és el motor de plantilles de Laravel. Permet escriure HTML barrejat amb lògica PHP d'una manera neta i llegible, sense els `<?php ?>` per tot arreu. Si veniu de JSP (Java) o EJS (JavaScript), el concepte és el mateix: un fitxer que barreja HTML amb lògica del servidor. Blade ho fa amb directives que comencen amb `@`.

**On viuen les vistes?** Sempre a `resources/views/`. Un fitxer `resources/views/concerts/index.blade.php` es renderitza amb `view('concerts.index')` — els punts substitueixen les barres.

## Layouts: Herència de Plantilles

### El concepte

En una app web, totes les pàgines comparteixen estructura: el `<head>`, la navbar, el footer. En lloc de copiar-enganxar tot això a cada vista, Blade permet definir un **layout** (plantilla base) i fer que cada pàgina l'hereti.

> **Analogia**: penseu en un layout com una plantilla de Word amb capçalera i peu de pàgina. Cada document nou que crees amb aquesta plantilla ja té l'estructura — tu només escrius el contingut del mig.

### Definir un Layout

Breeze ja us genera un layout a `resources/views/layouts/app.blade.php`. L'estructura bàsica és:

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SonaFest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Navbar (compartida a totes les pàgines) -->
    <nav>
        @include('layouts.navigation')
    </nav>

    <!-- Contingut que canvia a cada pàgina -->
    <main class="container mx-auto p-4">
        @yield('content')
    </main>

    <!-- Footer (compartit) -->
    <footer>
        <p>&copy; {{ date('Y') }} SonaFest</p>
    </footer>
</body>
</html>
```

**Directives clau**:
- `@yield('nom')` — Defineix un "forat" que les pàgines filles ompliran. El segon paràmetre és el valor per defecte.
- `@include('nom.vista')` — Inclou un altre fitxer Blade (com un `include` de PHP, però més net).
- `@vite(...)` — Carrega els assets compilats (CSS i JS de Tailwind).

### Heretar el Layout

Cada pàgina filla diu de quin layout hereta i omple els forats:

```html
<!-- resources/views/concerts/index.blade.php -->
@extends('layouts.app')

@section('title', 'Concerts - SonaFest')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Llista de Concerts</h1>
    
    <!-- Aquí va el contingut específic d'aquesta pàgina -->
    <p>Benvinguts al llistat de concerts!</p>
@endsection
```

**Directives clau**:
- `@extends('layouts.app')` — Diu quina plantilla base fa servir. Ha de ser la **primera línia** del fitxer.
- `@section('nom')` — Omple el `@yield('nom')` del layout. Dues formes:
  - Curta (una línia): `@section('title', 'Text')` — sense `@endsection`
  - Llarga (bloc): `@section('content') ... @endsection`

### Comparació ràpida

| Concepte | Java (JSP/Thymeleaf) | JavaScript (EJS) | Laravel (Blade) |
|---|---|---|---|
| Plantilla base | `<tiles:insertDefinition>` | Layout parcials manuals | `@yield('content')` |
| Heretar | `<tiles:putAttribute>` | — | `@extends('layout')` |
| Omplir secció | `<tiles:putAttribute>` | — | `@section ... @endsection` |
| Incloure parcial | `<jsp:include>` | `<%- include('partial') %>` | `@include('partial')` |

## Mostrar Dades: {{ }} i {!! !!}

### Escapar dades (segur per defecte)

```html
<!-- Escapa HTML automàticament (evita XSS) -->
<p>Artista: {{ $artista->nom }}</p>

<!-- SENSE escapar (PERILL — només si confies al 100% en el contingut) -->
<p>{!! $artista->bio_html !!}</p>
```

`{{ $variable }}` és equivalent a `<?php echo htmlspecialchars($variable) ?>`. La versió segura. Feu-la servir **sempre** tret que tingueu una raó molt bona per no fer-ho.

### Valors per defecte

```html
<!-- Si $artista->web és null, mostra 'Sense web' -->
<p>Web: {{ $artista->web ?? 'Sense web' }}</p>
```

## Directives de Control

### Condicionals

```html
<!-- @if / @elseif / @else -->
@if($artista->actiu)
    <span class="badge bg-green-500 text-white px-2 py-1 rounded">Actiu</span>
@else
    <span class="badge bg-red-500 text-white px-2 py-1 rounded">Inactiu</span>
@endif

<!-- @unless (el contrari de @if) -->
@unless($concert->esSoldOut())
    <a href="{{ route('concerts.comprar', $concert) }}">Compra entrades</a>
@endunless

<!-- Comprovar autenticació -->
@auth
    <p>Benvingut, {{ auth()->user()->name }}!</p>
@endauth

@guest
    <a href="{{ route('login') }}">Inicia sessió</a>
@endguest
```

### Bucles

```html
<!-- @foreach — el que fareu servir el 99% del temps -->
@foreach($concerts as $concert)
    <div class="concert-card border p-4 mb-2 rounded">
        <h3>{{ $concert->nom }}</h3>
        <p>Artista: {{ $concert->artista->nom }}</p>
        <p>Data: {{ $concert->data_hora->format('d/m/Y H:i') }}</p>
    </div>
@endforeach

<!-- @forelse — com @foreach però amb un bloc per quan la col·lecció és buida -->
@forelse($concerts as $concert)
    <div class="concert-card border p-4 mb-2 rounded">
        <h3>{{ $concert->nom }}</h3>
    </div>
@empty
    <p class="text-gray-500">No hi ha concerts programats.</p>
@endforelse
```

> **Consell pro**: `@forelse` és molt millor que fer un `@if(count($concerts) > 0)` seguit d'un `@foreach`. Menys codi, més clar.

### La variable $loop

Dins de qualsevol `@foreach` o `@forelse`, Blade proporciona automàticament la variable `$loop`:

```html
@foreach($artistes as $artista)
    <tr class="{{ $loop->even ? 'bg-gray-100' : '' }}">
        <td>{{ $loop->iteration }}</td>  <!-- 1, 2, 3... (compta des d'1) -->
        <td>{{ $artista->nom }}</td>
    </tr>
@endforeach
```

| Propietat | Què fa |
|---|---|
| `$loop->index` | Índex actual (comença a 0) |
| `$loop->iteration` | Iteració actual (comença a 1) |
| `$loop->first` | `true` si és el primer element |
| `$loop->last` | `true` si és l'últim element |
| `$loop->even` / `$loop->odd` | Files parells/senars (útil per zebra-striping) |
| `$loop->count` | Total d'elements |

## Formularis

Els formularis HTML són el cor de qualsevol CRUD. A Laravel, hi ha unes quantes coses que **sempre** cal recordar.

### El bàsic: @csrf i @method

```html
<!-- CREAR (POST) -->
<form action="{{ route('artistes.store') }}" method="POST">
    @csrf
    <!-- camps del formulari -->
    <button type="submit">Crear Artista</button>
</form>

<!-- EDITAR (PUT) — els formularis HTML només suporten GET i POST -->
<form action="{{ route('artistes.update', $artista) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- camps del formulari -->
    <button type="submit">Actualitzar</button>
</form>

<!-- ELIMINAR (DELETE) -->
<form action="{{ route('artistes.destroy', $artista) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600"
            onclick="return confirm('Segur que vols eliminar?')">
        Eliminar
    </button>
</form>
```

**Per què `@csrf`?** Protecció contra atacs Cross-Site Request Forgery. Sense el token CSRF, Laravel rebutjarà el formulari amb un error 419. **Poseu `@csrf` a TOTS els formularis POST/PUT/DELETE**.

**Per què `@method('PUT')`?** Els formularis HTML estàndard només suporten GET i POST. `@method('PUT')` (o `'DELETE'`, `'PATCH'`) afegeix un camp ocult `_method` que Laravel interpreta per enrutar correctament la petició.

### Camps de text

```html
<div class="mb-4">
    <label for="nom" class="block font-medium mb-1">Nom de l'artista</label>
    <input type="text" 
           id="nom" 
           name="nom" 
           value="{{ old('nom', $artista->nom ?? '') }}"
           class="w-full border rounded p-2 @error('nom') border-red-500 @enderror"
           required>
    @error('nom')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

**Desglossem això**:

1. **`name="nom"`** — El nom del camp. A PHP arribarà com `$request->input('nom')`.
2. **`old('nom', $artista->nom ?? '')`** — Funció clau! Retorna:
   - El valor que l'usuari va escriure si el formulari ha fallat la validació (repopulació)
   - El valor actual del model si estem editant
   - Cadena buida si és un formulari nou
3. **`@error('nom') ... @enderror`** — Mostra el missatge d'error de validació per a aquest camp.
4. **`$message`** — Variable automàtica dins de `@error` amb el text de l'error.

### Select (desplegable)

```html
<div class="mb-4">
    <label for="tipus" class="block font-medium mb-1">Tipus de concert</label>
    <select name="tipus" id="tipus" class="w-full border rounded p-2">
        <option value="">-- Selecciona --</option>
        @foreach(['CONCERT', 'DJ_SET', 'SHOWCASE', 'ACUSTIC'] as $tipus)
            <option value="{{ $tipus }}" 
                    {{ old('tipus', $concert->tipus ?? '') == $tipus ? 'selected' : '' }}>
                {{ $tipus }}
            </option>
        @endforeach
    </select>
    @error('tipus')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

### Select amb relacions (FK)

```html
<!-- Seleccionar l'escenari d'un concert -->
<div class="mb-4">
    <label for="escenari_id" class="block font-medium mb-1">Escenari</label>
    <select name="escenari_id" id="escenari_id" class="w-full border rounded p-2">
        <option value="">-- Selecciona escenari --</option>
        @foreach($escenaris as $escenari)
            <option value="{{ $escenari->id }}"
                    {{ old('escenari_id', $concert->escenari_id ?? '') == $escenari->id ? 'selected' : '' }}>
                {{ $escenari->nom }} (Aforament: {{ $escenari->aforament_maxim }})
            </option>
        @endforeach
    </select>
    @error('escenari_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

> **Important**: perquè això funcioni, el controller ha de passar `$escenaris` a la vista:
> ```php
> public function create()
> {
>     $escenaris = Escenari::all();
>     return view('concerts.create', compact('escenaris'));
> }
> ```

### Checkboxes per a relacions N:M

Quan tenim una relació Molts a Molts (com artista-gènere), necessitem múltiples checkboxes:

```html
<!-- Seleccionar gèneres d'un artista (relació N:M) -->
<div class="mb-4">
    <label class="block font-medium mb-1">Gèneres</label>
    @foreach($generes as $genere)
        <label class="inline-flex items-center mr-4">
            <input type="checkbox" 
                   name="generes[]" 
                   value="{{ $genere->id }}"
                   {{ in_array($genere->id, old('generes', $artista->generes->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                   class="mr-1">
            {{ $genere->nom }}
        </label>
    @endforeach
    @error('generes')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

**Punts clau**:
- **`name="generes[]"`** — Els claudàtors `[]` fan que PHP rebi un array amb tots els valors marcats.
- **`value="{{ $genere->id }}"`** — El valor que s'envia és l'ID del gènere.
- **`in_array(...)`** — Comprova si aquest gènere ja està seleccionat (per a edició o repopulació).
- **`pluck('id')->toArray()`** — Extreu els IDs de la col·lecció de gèneres de l'artista.

Al controller, per desar la relació N:M:
```php
// Al mètode store() o update()
$artista->generes()->sync($request->input('generes', []));
```

### Checkbox booleà individual

```html
<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="hidden" name="actiu" value="0">
        <input type="checkbox" 
               name="actiu" 
               value="1"
               {{ old('actiu', $artista->actiu ?? true) ? 'checked' : '' }}
               class="mr-2">
        Artista actiu
    </label>
</div>
```

> **Truc del hidden**: Si el checkbox no està marcat, el navegador NO envia res. El `<input type="hidden" name="actiu" value="0">` garanteix que sempre s'envia un valor (0 si no marcat, 1 si marcat, perquè el checkbox sobreescriu el hidden).

### Textarea

```html
<div class="mb-4">
    <label for="bio" class="block font-medium mb-1">Biografia</label>
    <textarea name="bio" 
              id="bio" 
              rows="4"
              class="w-full border rounded p-2">{{ old('bio', $artista->bio ?? '') }}</textarea>
    @error('bio')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

> **Atenció**: A diferència dels inputs, al textarea el valor va **entre les etiquetes**, no en un atribut `value`. I NO deixeu espais ni salts de línia entre `>` i `{{ old(...) }}`, o apareixeran espais en blanc al text.

### Camps de data i hora

```html
<div class="mb-4">
    <label for="data_hora" class="block font-medium mb-1">Data i hora del concert</label>
    <input type="datetime-local" 
           name="data_hora" 
           id="data_hora"
           value="{{ old('data_hora', isset($concert) ? $concert->data_hora->format('Y-m-d\TH:i') : '') }}"
           class="w-full border rounded p-2">
    @error('data_hora')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

> **Nota**: el format del `datetime-local` HTML és `YYYY-MM-DDTHH:MM`. Per això fem servir `->format('Y-m-d\TH:i')` amb la `T` escapada.

### Reutilitzar formularis amb @include

No repetiu el formulari sencer a `create.blade.php` i `edit.blade.php`. Extraieu-lo a un parcial:

```html
<!-- resources/views/artistes/_form.blade.php -->
<div class="mb-4">
    <label for="nom" class="block font-medium mb-1">Nom</label>
    <input type="text" name="nom" id="nom"
           value="{{ old('nom', $artista->nom ?? '') }}"
           class="w-full border rounded p-2">
    @error('nom')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="pais" class="block font-medium mb-1">País</label>
    <input type="text" name="pais" id="pais"
           value="{{ old('pais', $artista->pais ?? '') }}"
           class="w-full border rounded p-2">
    @error('pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- ... més camps ... -->
```

```html
<!-- resources/views/artistes/create.blade.php -->
@extends('layouts.app')
@section('content')
    <h1>Crear Artista</h1>
    <form action="{{ route('artistes.store') }}" method="POST">
        @csrf
        @include('artistes._form')
        <button type="submit">Crear</button>
    </form>
@endsection

<!-- resources/views/artistes/edit.blade.php -->
@extends('layouts.app')
@section('content')
    <h1>Editar {{ $artista->nom }}</h1>
    <form action="{{ route('artistes.update', $artista) }}" method="POST">
        @csrf
        @method('PUT')
        @include('artistes._form')
        <button type="submit">Actualitzar</button>
    </form>
@endsection
```

> **Convenció**: els parcials (fitxers inclosos amb `@include`) solen començar amb `_` al nom: `_form.blade.php`, `_card.blade.php`. No és obligatori, però ajuda a distingir-los de les vistes completes.

## Flash Messages (Missatges de Sessió)

Després d'una acció (crear, editar, eliminar), volem mostrar un missatge d'èxit a l'usuari.

### Al Controller

```php
public function store(Request $request)
{
    // ... crear l'artista ...
    
    return redirect()->route('artistes.index')
                     ->with('success', 'Artista creat correctament!');
}

public function destroy(Artista $artista)
{
    $artista->delete();
    
    return redirect()->route('artistes.index')
                     ->with('success', 'Artista eliminat correctament.');
}
```

### A la Vista (normalment al layout)

```html
<!-- Poseu-ho al layout per tenir-ho disponible a totes les pàgines -->
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif
```

> **Com funciona?** `->with('success', 'text')` guarda `'text'` a la sessió **només per a la següent petició** (flash data). A la pàgina següent, `session('success')` el llegeix i després desapareix automàticament.

## Errors de Validació

### Mostrar tots els errors al principi del formulari

```html
@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Hi ha errors al formulari:</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

### Mostrar errors camp per camp

Ja ho hem vist als exemples de formularis:

```html
@error('nom')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
```

**Consell**: combinar les dues aproximacions funciona bé — un resum al principi del formulari i errors inline a cada camp.

### Afegir classe CSS condicional als camps amb error

```html
<input type="text" name="nom"
       class="w-full border rounded p-2 @error('nom') border-red-500 bg-red-50 @enderror">
```

### Errors propis des del controller: `withErrors` + `@error`

`$request->validate()` comprova el format de les dades (que hi siguin, que siguin un número, que l'id existeixi...), però no pot comprovar **regles de negoci** com "no pots envair una ciutat amiga" o "aquest artista ja té un concert a aquesta hora". Per a això fas la comprovació tu mateix i, si falla, tornes al formulari amb un error propi:

**Controller** (`EjercitoController@invadir`):

```php
public function invadir(Request $request, Ejercito $ejercito)
{
    // 1. Validació normal: si falla, Laravel fa el back() automàticament
    $request->validate([
        'ciudad_id' => 'required|exists:ciudades,id',
    ]);

    $ciudad = Ciudad::find($request->ciudad_id);

    // 2. Validació de negoci: la fem nosaltres
    if ($ciudad->amigo) {
        return back()->withErrors([
            'ciudad_id' => 'No pots envair una ciutat amiga.',
        ])->withInput();
    }

    // Clau inventada: no correspon a cap camp del formulari
    if ($ejercito->soldados < 100) {
        return back()->withErrors([
            'soldados' => 'L\'exèrcit necessita 100 soldats com a mínim.',
        ])->withInput();
    }

    // ... si arribem aquí, tot és correcte
}
```

**Vista** (`ejercitos/invadir.blade.php`):

```html
<form action="{{ route('ejercitos.invadido', $ejercito) }}" method="POST">
    @csrf

    {{-- Error amb clau inventada: el mostrem on vulguem --}}
    @error('soldados')
        <p class="text-red-500">{{ $message }}</p>
    @enderror

    <select name="ciudad_id">
        @foreach($ciudades as $c)
            <option value="{{ $c->id }}" {{ old('ciudad_id') == $c->id ? 'selected' : '' }}>
                {{ $c->nombre }}
            </option>
        @endforeach
    </select>
    @error('ciudad_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror

    <button type="submit">Envair!</button>
</form>
```

**Com es connecten les dues parts**:

| Controller | Vista |
|---|---|
| `withErrors(['ciudad_id' => 'text'])` | `@error('ciudad_id')` → `{{ $message }}` val `'text'` |
| `withErrors(['soldados' => 'text'])` | `@error('soldados')` |
| `->withInput()` | `old('ciudad_id')` recupera el valor triat |
| `back()` | torna a la pàgina del formulari |

**Punts clau**:

1. **La clau del `withErrors` ha de ser exactament la mateixa que la de l'`@error`**. Si poses `withErrors(['ciutat' => ...])` i a la vista `@error('ciudad_id')`, l'error no es veurà.
2. **La clau pot ser inventada**. No cal que sigui el `name` d'un input: `'soldados'` no és cap camp del formulari, però `@error('soldados')` el mostra igualment. Útil per a errors generals que no són d'un camp concret.
3. **Els errors de `validate()` i els de `withErrors` arriben al mateix lloc**. `@error('ciudad_id')` mostra tant el `required` de la validació com el "ciutat amiga" del controller. També surten tots a `$errors->all()`.
4. **Sense `->withInput()`, `old()` torna buit** i l'usuari ha de tornar a omplir tot el formulari. `validate()` ja ho fa sol, però amb `withErrors` ho has de posar tu.
5. **Sempre `return`**: `return back()->withErrors(...)`. Si oblides el `return`, el codi continua executant-se i l'acció es fa igualment.

> Exemples reals: `PruebaQueNoSriveParaNada` (`EjercitoController@invadir`) i `minipractica` (`ConcertController@store`, artista amb dos concerts a la mateixa hora). Al controller, mira també la secció *Redirects amb dades flash* de `laravel-controllers-requests.md`.

## Badges i Rendering Condicional

### Badges amb classes dinàmiques

```html
<!-- Badge per tipus de concert -->
@php
    $tipusColors = [
        'CONCERT' => 'bg-blue-500',
        'DJ_SET' => 'bg-purple-500',
        'SHOWCASE' => 'bg-yellow-500',
        'ACUSTIC' => 'bg-green-500',
    ];
@endphp
<span class="text-white text-xs px-2 py-1 rounded {{ $tipusColors[$concert->tipus] ?? 'bg-gray-500' }}">
    {{ $concert->tipus }}
</span>

<!-- Badge actiu/inactiu -->
<span class="{{ $artista->actiu ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} 
             text-xs px-2 py-1 rounded">
    {{ $artista->actiu ? 'Actiu' : 'Inactiu' }}
</span>
```

### Mostrar relacions

```html
<!-- Mostrar gèneres d'un artista (relació N:M) -->
<div class="flex flex-wrap gap-1">
    @forelse($artista->generes as $genere)
        <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">
            {{ $genere->nom }}
        </span>
    @empty
        <span class="text-gray-400 text-sm">Sense gèneres assignats</span>
    @endforelse
</div>
```

## Taula Completa amb Accions CRUD

Exemple complet d'una vista `index` amb totes les funcionalitats:

```html
<!-- resources/views/artistes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Artistes - SonaFest')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Artistes</h1>
        <a href="{{ route('artistes.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Nou Artista
        </a>
    </div>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Taula -->
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">#</th>
                <th class="border p-2 text-left">Nom</th>
                <th class="border p-2 text-left">País</th>
                <th class="border p-2 text-left">Estat</th>
                <th class="border p-2 text-left">Concerts</th>
                <th class="border p-2 text-left">Accions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($artistes as $artista)
                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                    <td class="border p-2">{{ $loop->iteration }}</td>
                    <td class="border p-2">{{ $artista->nom }}</td>
                    <td class="border p-2">{{ $artista->pais }}</td>
                    <td class="border p-2">
                        <span class="{{ $artista->actiu ? 'text-green-600' : 'text-red-600' }}">
                            {{ $artista->actiu ? '✓ Actiu' : '✗ Inactiu' }}
                        </span>
                    </td>
                    <td class="border p-2">{{ $artista->concerts_count ?? $artista->concerts->count() }}</td>
                    <td class="border p-2">
                        <a href="{{ route('artistes.show', $artista) }}" 
                           class="text-blue-500 hover:underline mr-2">Veure</a>
                        <a href="{{ route('artistes.edit', $artista) }}" 
                           class="text-yellow-600 hover:underline mr-2">Editar</a>
                        <form action="{{ route('artistes.destroy', $artista) }}" 
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline"
                                    onclick="return confirm('Eliminar {{ $artista->nom }}?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border p-4 text-center text-gray-500">
                        No hi ha artistes. <a href="{{ route('artistes.create') }}" class="text-blue-500">Crea'n un!</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
```

## Components Blade (Breu Introducció)

A partir de Laravel 7, Blade ofereix components que funcionen com components HTML reutilitzables:

```html
<!-- resources/views/components/alert.blade.php -->
@props(['type' => 'info', 'message'])

@php
    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error'   => 'bg-red-100 border-red-400 text-red-700',
        'info'    => 'bg-blue-100 border-blue-400 text-blue-700',
    ];
@endphp

<div class="{{ $colors[$type] ?? $colors['info'] }} border px-4 py-3 rounded mb-4">
    {{ $message }}
</div>
```

Ús:
```html
<x-alert type="success" message="Artista creat correctament!" />
<x-alert type="error" :message="$errorMessage" />
```

> **Nota**: els components són opcionals per a la pràctica. Són útils per evitar repetir HTML, però podeu aconseguir el mateix amb `@include` i parcials.

## Xuleta Ràpida de Directives Blade

| Directiva | Què fa | Exemple |
|---|---|---|
| `{{ $var }}` | Mostra valor escapant HTML | `{{ $artista->nom }}` |
| `{!! $var !!}` | Mostra valor SENSE escapar | `{!! $bio_html !!}` |
| `@extends('layout')` | Hereta un layout | `@extends('layouts.app')` |
| `@section / @endsection` | Defineix contingut per al layout | `@section('content') ... @endsection` |
| `@yield('nom')` | Forat al layout que les filles omplen | `@yield('content')` |
| `@include('vista')` | Inclou un parcial | `@include('artistes._form')` |
| `@if / @elseif / @else / @endif` | Condicional | `@if($actiu) ... @endif` |
| `@unless / @endunless` | Contrari de @if | `@unless($soldOut) ... @endunless` |
| `@foreach / @endforeach` | Bucle | `@foreach($items as $item)` |
| `@forelse / @empty / @endforelse` | Bucle amb cas buit | `@forelse($items as $item) ... @empty Buit @endforelse` |
| `@csrf` | Token CSRF (obligatori a forms) | `<form> @csrf ...` |
| `@method('PUT')` | Mètode HTTP (PUT/DELETE/PATCH) | `@method('DELETE')` |
| `@error('camp') / @enderror` | Mostra error de validació | `@error('nom') {{ $message }} @enderror` |
| `@auth / @endauth` | Si l'usuari està autenticat | `@auth Hola! @endauth` |
| `@guest / @endguest` | Si l'usuari NO està autenticat | `@guest Login @endguest` |
| `@props([...])` | Defineix propietats d'un component | `@props(['type' => 'info'])` |
| `@php / @endphp` | Bloc de PHP pur | `@php $x = 1; @endphp` |

## Errors Comuns i Solucions

1. **Error 419 Page Expired** — T'has oblidat el `@csrf` al formulari.

2. **MethodNotAllowedHttpException** — Vols fer PUT o DELETE però no tens `@method('PUT')`.

3. **El formulari no conserva els valors** — No fas servir `old('camp')` als `value` dels inputs.

4. **L'error de validació no es mostra** — No tens `@error('camp')` sota l'input, o el `name` de l'input no coincideix amb el nom del camp a les regles de validació.

5. **La vista no existeix** — El nom de la vista no coincideix amb la ruta del fitxer. Recorda: `view('concerts.index')` → `resources/views/concerts/index.blade.php`.

6. **Variables undefined** — El controller no passa les dades a la vista. Has de fer `return view('vista', compact('variable'))` o `return view('vista', ['variable' => $valor])`.

7. **HTML apareix com a text** — Fas servir `{{ }}` amb contingut HTML. Si vols renderitzar HTML, usa `{!! !!}` (amb compte).

8. **Espais en blanc al textarea** — Hi ha espais entre les etiquetes del textarea i `{{ old() }}`. Posa-ho tot junt: `<textarea>{{ old('bio') }}</textarea>`.
