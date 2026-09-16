# Apunts Laravel: Middleware i Policies

## 1. MIDDLEWARE

### Què és un Middleware?

Un middleware és un **filtre** que s'executa **abans o després** que una petició HTTP arribi al controller. Pensa-ho com un porter de discoteca: abans que entris, et comprova l'edat, la vestimenta, etc.

```
Petició HTTP → [Middleware 1] → [Middleware 2] → Controller → Resposta
```

**Equivalència Django:** els middlewares de Laravel són similars als decoradors `@login_required`, `@permission_required`, o als middlewares de Django (`MIDDLEWARE` a `settings.py`), però s'apliquen **per ruta** en lloc de globalment.

---

### Crear un Middleware

Per crear un middleware:

```bash
php artisan make:middleware NomDelMiddleware
```

Això crea un fitxer a `app/Http/Middleware/`.

---

### Exemple 1: Middleware d'Autenticació (UserIsAuth)

Un middleware que comprova si l'usuari està loguejat:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserIsAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Auth::check() retorna true si l'usuari està autenticat
        // Equivalent Django: request.user.is_authenticated
        if (!Auth::check()) {
            // Redirigeix al login amb un missatge flash d'error
            return redirect()->route('login')->with('error', "Logueate majo");
        }

        // next() significa: "tot correcte, segueix endavant cap al controller"
        return $next($request);
    }
}
```

**Anatomia del mètode `handle()`:**

| Element | Què fa |
|---|---|
| `$request` | La petició HTTP entrant (equivalent a `request` de Django) |
| `$next` | Una funció que passa la petició al següent middleware o al controller |
| `return $next($request)` | "Deixa passar" — la petició continua el seu camí |
| `return redirect(...)` | "No passes" — talla la petició i redirigeix |

---

### Exemple 2: Middleware de Permisos (CheckRole)

Un middleware que comprova si l'usuari té un rol concret:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Rep un paràmetre extra: el rol requerit.
     * 
     * @param string $role  El rol que es demana ('admin', 'propietari', etc.)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        // Si no està loguejat, fora
        if (!$user) {
            return redirect()->route('login');
        }

        // Comprovem el rol
        switch ($role) {
            case 'admin':
                if ($user->name !== 'admin') {
                    abort(403, 'Necessites ser admin');
                }
                break;

            case 'propietari':
                if (!$user->customer || !$user->customer->factory_id) {
                    abort(403, 'Necessites ser propietari d\'una factory');
                }
                break;

            case 'customer':
                if (!$user->customer) {
                    abort(403, 'Necessites ser customer');
                }
                break;
        }

        return $next($request);
    }
}
```

**Ús a les rutes amb paràmetre:**

```php
// El text després dels dos punts és el paràmetre $role del middleware
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware(CheckRole::class . ':admin');

Route::get('/factory/manage', [FactoryController::class, 'manage'])
    ->middleware(CheckRole::class . ':propietari');
```

---

### Exemple 3: Middleware per Carregar Recursos (LoadFactory)

Un middleware que carrega un recurs i el posa disponible per a tota la petició:

```php
<?php

namespace App\Http\Middleware;

use App\Models\Factory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadFactory
{
    /**
     * Carrega la factory de l'usuari loguejat i la comparteix 
     * amb tota la petició (controller + vistes).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->customer && $user->customer->factory_id) {
            // Carrega la factory amb els seus femboys (eager loading)
            $factory = Factory::with('femboys')->find($user->customer->factory_id);

            // merge() afegeix dades a la request, accessible al controller
            // Equivalent Django: request.factory = factory (més o menys)
            $request->merge(['user_factory' => $factory]);

            // share() fa la variable accessible a TOTES les vistes Blade
            // Equivalent Django: afegir al context_processor
            view()->share('userFactory', $factory);
        }

        return $next($request);
    }
}
```

**Com accedir a les dades al controller:**

```php
class FactoryController extends Controller
{
    public function dashboard(Request $request)
    {
        // La factory ja ve carregada pel middleware
        $factory = $request->input('user_factory');

        return view('factory.dashboard');
        // A la vista, $userFactory ja està disponible gràcies a view()->share()
    }
}
```

---

### Aplicar Middleware a les Rutes

Hi ha **tres maneres** d'aplicar middleware:

#### Opció A: Directament a la ruta

```php
// Middleware sense paràmetres
Route::get('/femboys/edit/{femboy}', [FemboyController::class, 'edit'])
    ->name('femboys.edit')
    ->middleware(UserIsAuth::class);

// Middleware amb paràmetres
Route::get('/admin/panel', [AdminController::class, 'index'])
    ->middleware(CheckRole::class . ':admin');
```

#### Opció B: A un grup de rutes

```php
// Totes les rutes dins el grup passen pel middleware
Route::middleware([UserIsAuth::class])->group(function () {
    Route::get('/femboys/create', [FemboyController::class, 'create'])
        ->name('femboys.create');
    Route::post('/femboys/store', [FemboyController::class, 'store'])
        ->name('femboys.store');
    Route::get('/femboys/edit/{femboy}', [FemboyController::class, 'edit'])
        ->name('femboys.edit');
});

// Pots combinar prefix + middleware
Route::prefix('admin')
    ->middleware([UserIsAuth::class, CheckRole::class . ':admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index']);
        Route::get('/users', [AdminController::class, 'users']);
    });
```

#### Opció C: Al constructor del controller

```php
class FemboyController extends Controller
{
    public function __construct()
    {
        // Totes les accions d'aquest controller requereixen autenticació
        $this->middleware(UserIsAuth::class);

        // Opcionalment, només per a certs mètodes
        $this->middleware(CheckRole::class . ':admin')->only(['destroy', 'create']);

        // O excloent mètodes
        $this->middleware(UserIsAuth::class)->except(['all', 'show']);
    }
}
```

---

### Resum visual: Flux d'una petició amb Middleware

```
  Usuari fa GET /femboys/edit/5
         │
         ▼
  ┌──────────────────┐
  │   UserIsAuth     │── No loguejat? → redirect('/login')
  └──────┬───────────┘
         │ Loguejat ✓
         ▼
  ┌──────────────────┐
  │   CheckRole      │── No és admin? → abort(403)
  └──────┬───────────┘
         │ És admin ✓
         ▼
  ┌──────────────────┐
  │   LoadFactory    │── Carrega factory → merge a request
  └──────┬───────────┘
         │
         ▼
  ┌──────────────────┐
  │   Controller     │── FemboyController@edit
  └──────────────────┘
```

---

---

## 2. POLICIES

### Què és una Policy?

Una policy és una classe que encapsula **lògica d'autorització** per a un model concret. Mentre que el middleware decideix "**pots accedir a aquesta ruta?**", la policy decideix "**pots fer aquesta acció sobre aquest objecte concret?**".

| Middleware | Policy |
|---|---|
| Comprova rols generals | Comprova permisos sobre objectes concrets |
| "Ets admin?" | "Pots editar AQUEST femboy?" |
| S'aplica a rutes | S'aplica a models |
| `abort(403)` si falla | `abort(403)` si falla |

**Equivalència Django:** les policies són similars a `django-guardian` (permisos per objecte) o als decoradors `@permission_required`, però molt més organitzades.

---

### Crear una Policy

```bash
php artisan make:policy FemboyPolicy --model=Femboy
```

Això crea `app/Policies/FemboyPolicy.php` i la vincula automàticament al model `Femboy`.

---

### Registrar la Policy

A `app/Providers/AuthServiceProvider.php`:

```php
protected $policies = [
    Femboy::class => FemboyPolicy::class,
];
```

> **Nota:** A partir de Laravel 10+, si segueixes la convenció de noms (`FemboyPolicy` per a `Femboy`), Laravel la registra automàticament.

---

### Exemple complet: FemboyPolicy

```php
<?php

namespace App\Policies;

use App\Models\Femboy;
use App\Models\User;

class FemboyPolicy
{
    // =============================================
    // MÈTODES AUXILIARS (helpers privats)
    // =============================================

    /**
     * L'usuari és admin?
     * Comprovem pel nom (en producció seria un camp 'role' o una taula de rols)
     */
    public function esAdmin(User $user): bool
    {
        return $user->name === "admin";
    }

    /**
     * L'usuari és propietari d'una factory?
     * Un propietari té un customer amb factory_id assignat
     */
    public function esPropietari(User $user): bool
    {
        return $user->customer !== null 
            && $user->customer->factory_id !== null;
    }

    /**
     * L'usuari és un customer normal (sense factory)?
     */
    public function esCustomer(User $user): bool
    {
        return $user->customer !== null 
            && $user->customer->factory_id === null;
    }

    // =============================================
    // MÈTODES DE POLICY (accions sobre Femboy)
    // =============================================

    /**
     * Qui pot VEURE LA LLISTA de femboys?
     * 
     * viewAny NO rep cap $femboy perquè no estem mirant un objecte concret,
     * sinó si l'usuari pot accedir a la pàgina de llistat en general.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tothom autenticat pot veure la llista
    }

    /**
     * Qui pot VEURE tots els femboys? (vista admin)
     */
    public function viewAll(User $user): bool
    {
        return $this->esAdmin($user);
    }

    /**
     * Qui pot VEURE els femboys de la seva factory?
     * 
     * Aquí SÍ que rebem un $femboy concret per comparar 
     * si pertany a la factory de l'usuari.
     */
    public function viewFactory(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        if ($this->esPropietari($user)) {
            // Comparem la factory de l'usuari amb la del femboy
            return $user->customer->factory_id === $femboy->factory_id;
        }

        return false;
    }

    /**
     * Qui pot VEURE només els seus femboys assignats?
     */
    public function viewOwn(User $user, Femboy $femboy): bool
    {
        if ($this->esCustomer($user)) {
            // Comprovem si el femboy està a la relació N-N del customer
            return $user->customer->femboys->contains($femboy->id);
        }

        return false;
    }

    /**
     * Qui pot VEURE un femboy concret?
     * Combina totes les regles anteriors en un sol mètode.
     * 
     * ORDRE IMPORTANT: del més permissiu al més restrictiu
     */
    public function view(User $user, Femboy $femboy): bool
    {
        // 1. Admin veu tots
        if ($this->esAdmin($user)) {
            return true;
        }

        // 2. Propietari veu els de la seva factory
        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        // 3. Customer veu només els seus
        if ($this->esCustomer($user)) {
            return $user->customer->femboys->contains($femboy->id);
        }

        // 4. Tothom pot veure els disponibles
        if ($femboy->estado === "Disponible") {
            return true;
        }

        return false;
    }

    /**
     * Qui pot CREAR un femboy?
     * 
     * IMPORTANT: create() NO rep un $femboy perquè encara no existeix!
     * Només rep $user.
     */
    public function create(User $user): bool
    {
        return $this->esAdmin($user) || $this->esPropietari($user);
    }

    /**
     * Qui pot ACTUALITZAR un femboy?
     */
    public function update(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        // Propietari només pot editar femboys de la seva factory
        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        // Customers no poden editar
        return false;
    }

    /**
     * Qui pot ELIMINAR un femboy?
     */
    public function delete(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        return false;
    }
}
```

---

### Resum dels mètodes de Policy

| Mètode | Rep model? | Quan s'usa |
|---|---|---|
| `viewAny($user)` | No | Pot veure la llista? |
| `view($user, $femboy)` | Sí | Pot veure AQUEST femboy? |
| `create($user)` | No | Pot crear-ne un de nou? |
| `update($user, $femboy)` | Sí | Pot editar AQUEST femboy? |
| `delete($user, $femboy)` | Sí | Pot eliminar AQUEST femboy? |

**Regla d'or:** si l'objecte encara no existeix (`create`, `viewAny`), el mètode **no rep** el model. Si l'objecte ja existeix (`view`, `update`, `delete`), el mètode **sí que el rep**.

---

---

## 3. USAR POLICIES AL CONTROLLER

### Mètode `authorize()`

Dins el controller, cridem `$this->authorize('nomAcció', $objecte)`. Si la policy retorna `false`, Laravel llança automàticament un **error 403 Forbidden**.

```php
class FemboyController extends Controller
{
    /**
     * Mostrar el formulari de creació
     */
    public function create()
    {
        // authorize('create', Femboy::class)
        //   → Busca FemboyPolicy::create($user)
        //   → Si retorna false → 403 automàtic
        //   → Si retorna true → continua
        //
        // NOTA: Passem la CLASSE perquè no tenim cap objecte concret encara
        $this->authorize('create', Femboy::class);

        $factories = Factory::all();
        $estados = EstadoFemboy::cases();
        return view('femboys.create', compact('factories', 'estados'));
    }

    /**
     * Mostrar la llista de femboys
     */
    public function all()
    {
        // viewAny tampoc rep un objecte concret
        $this->authorize('viewAny', Femboy::class);

        $femboys = Femboy::all();
        return view('femboys.all', compact('femboys'));
    }

    /**
     * Mostrar un femboy concret
     */
    public function show(Femboy $femboy)
    {
        // authorize('view', $femboy)
        //   → Busca FemboyPolicy::view($user, $femboy)
        //   → Aquí SÍ que passem l'objecte concret
        $this->authorize('view', $femboy);

        return view('femboys.show', compact('femboy'));
    }

    /**
     * Actualitzar un femboy
     */
    public function update(Request $request, Femboy $femboy)
    {
        $this->authorize('update', $femboy);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'edad' => 'required|integer|min:18|max:100',
            'procedencia' => 'required|string|max:255',
            'fecha_cumpleanos' => 'required|date|before:today',
            'descripcion' => 'required|string|max:500',
            'estado' => 'required|in:Disponible,Reservado,No Disponible',
            'factory_id' => 'required|exists:factories,id'
        ]);

        $femboy->update($validated);

        return redirect()->route('femboys.all')
            ->with('success', 'Femboy actualizado correctamente');
    }

    /**
     * Eliminar un femboy
     */
    public function destroy(Femboy $femboy)
    {
        $this->authorize('delete', $femboy);

        $femboy->delete();

        return redirect()->route('femboys.all')
            ->with('success', 'Femboy deleted successfully');
    }
}
```

### Patró `authorize()`: quin mètode crida?

```
$this->authorize('view', $femboy)
                   │        │
                   │        └── Objecte Femboy → Laravel busca FemboyPolicy
                   │
                   └── Nom del mètode dins la policy → FemboyPolicy::view()

Resultat: FemboyPolicy::view(auth()->user(), $femboy)
```

Quan passes la classe en lloc de l'objecte:

```
$this->authorize('create', Femboy::class)
                   │            │
                   │            └── Classe Femboy → Laravel busca FemboyPolicy
                   │
                   └── FemboyPolicy::create(auth()->user())
                       (sense segon paràmetre perquè no hi ha objecte)
```

---

---

## 4. USAR POLICIES A LES VISTES BLADE

### Directives `@can`, `@cannot`, `@canany`

Les directives Blade permeten **mostrar o amagar HTML** segons els permisos de l'usuari. Laravel crida la mateixa policy que `authorize()`, però sense llançar 403 — simplement mostra o no mostra el contingut.

---

### `@can` — Mostra si TÉ permís

```blade
{{-- Amb objecte concret --}}
@can('view', $femboy)
    <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
@endcan

{{-- Amb classe (sense objecte, per a accions com 'create') --}}
@can('create', App\Models\Femboy::class)
    <a href="{{ route('femboys.create') }}" class="btn btn-primary">Crear nou Femboy</a>
@endcan
```

---

### `@cannot` — Mostra si NO té permís

```blade
@cannot('update', $femboy)
    <span style="color: #999;">No editable</span>
@endcannot

@cannot('delete', $femboy)
    <span style="color: #999;">No eliminable</span>
@endcannot
```

---

### `@can` / `@elsecan` / `@endcan` — Cadena condicional

Perfecte per mostrar contingut diferent segons el rol:

```blade
<div class="user-info">
    <strong>Usuari:</strong> {{ auth()->user()->name }}<br>

    @can('esAdmin', App\Models\Femboy::class)
        <strong>Rol:</strong> Administrador

    @elsecan('esPropietari', App\Models\Femboy::class)
        <strong>Rol:</strong> Propietari de Factory<br>
        <strong>Factory:</strong> {{ auth()->user()->customer->factory->nombre }}

    @elsecan('esCustomer', App\Models\Femboy::class)
        <strong>Rol:</strong> Customer
    @endcan
</div>
```

---

### `@canany` — Mostra si té ALGUN dels permisos

```blade
@canany(['update', 'delete'], $femboy)
    <div class="accions">
        @can('update', $femboy)
            <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
        @endcan
        @can('delete', $femboy)
            <button class="btn btn-danger">Eliminar</button>
        @endcan
    </div>
@endcanany
```

---

### Exemple complet: Llista amb permisos per rol

Aquesta vista mostra **files diferents** segons el tipus d'usuari, controlant què veu cadascú:

```blade
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Estado</th>
            <th>Factory</th>
            <th>Accions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($femboys as $femboy)

        {{-- VISTA ADMIN: veu tots els femboys amb totes les accions --}}
        @can('viewAll', App\Models\Femboy::class)
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>
                    @if($femboy->estado === 'Disponible')
                        <span class="badge badge-success">{{ $femboy->estado }}</span>
                    @elseif($femboy->estado === 'Reservado')
                        <span class="badge badge-warning">{{ $femboy->estado }}</span>
                    @else
                        <span class="badge badge-danger">{{ $femboy->estado }}</span>
                    @endif
                </td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>
                    {{-- Admin pot fer de tot --}}
                    <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('femboys.destroy', $femboy) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Estàs segur?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>

        {{-- VISTA PROPIETARI: veu femboys de la seva factory --}}
        @elsecan('viewFactory', $femboy)
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>{{ $femboy->estado }}</td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>
                    @can('view', $femboy)
                        <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    @endcan
                    @can('update', $femboy)
                        <a href="{{ route('femboys.edit', $femboy) }}" class="btn btn-warning">Editar</a>
                    @endcan
                    @can('delete', $femboy)
                        <form action="{{ route('femboys.destroy', $femboy) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Estàs segur?')">Eliminar</button>
                        </form>
                    @endcan
                </td>
            </tr>

        {{-- VISTA CUSTOMER: només els seus, sense editar ni eliminar --}}
        @elsecan('viewOwn', $femboy)
            <tr>
                <td>{{ $femboy->id }}</td>
                <td>{{ $femboy->nombre }}</td>
                <td>{{ $femboy->edad }}</td>
                <td>{{ $femboy->estado }}</td>
                <td>{{ $femboy->factory->nombre }}</td>
                <td>
                    @can('view', $femboy)
                        <a href="{{ route('femboys.show', $femboy) }}" class="btn btn-secondary">Veure</a>
                    @endcan
                    @cannot('update', $femboy)
                        <span style="color: #999;">No editable</span>
                    @endcannot
                </td>
            </tr>
        @endcan

    @empty
        <tr>
            <td colspan="6" class="empty-state">
                No hi ha femboys disponibles per mostrar.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
```

---

---

## 5. MIDDLEWARE vs POLICY — QUAN FER SERVIR CADASCUN?

| Situació | Usa |
|---|---|
| "L'usuari està loguejat?" | **Middleware** |
| "L'usuari és admin?" (a nivell de ruta) | **Middleware** |
| "L'usuari pot editar AQUEST femboy concret?" | **Policy** |
| "Mostro el botó d'eliminar a la vista?" | **Policy** (`@can`) |
| "Carrego dades extra per a la petició?" | **Middleware** |
| "L'usuari pot crear femboys?" | **Policy** (`create`) |

**Regla pràctica:**

- **Middleware** → decisions sobre **rutes** (qui pot accedir a on)
- **Policy** → decisions sobre **objectes** (qui pot fer què amb quin recurs)

La combinació ideal és fer servir **middleware per filtrar l'accés general** i **policies per al control fi** dins del controller i les vistes:

```php
// web.php
// Primer el middleware comprova que estiguis loguejat
Route::get('/femboys/edit/{femboy}', [FemboyController::class, 'edit'])
    ->middleware(UserIsAuth::class);

// Després al controller, la policy comprova que puguis editar AQUEST femboy
public function edit(Femboy $femboy)
{
    $this->authorize('update', $femboy); // ← Policy
    // ...
}
```

---

## 6. TAULA RESUM RÀPIDA

### Middleware

| Acció | Codi |
|---|---|
| Crear | `php artisan make:middleware NomMiddleware` |
| Aplicar a una ruta | `->middleware(NomMiddleware::class)` |
| Aplicar a un grup | `Route::middleware([...])->group(...)` |
| Amb paràmetre | `->middleware(CheckRole::class . ':admin')` |
| Deixar passar | `return $next($request);` |
| Bloquejar | `return redirect(...)` o `abort(403)` |

### Policies

| Acció | Codi |
|---|---|
| Crear | `php artisan make:policy NomPolicy --model=Nom` |
| Autoritzar al controller (amb objecte) | `$this->authorize('view', $femboy)` |
| Autoritzar al controller (sense objecte) | `$this->authorize('create', Femboy::class)` |
| Mostrar a Blade si TÉ permís | `@can('update', $femboy) ... @endcan` |
| Mostrar a Blade si NO té permís | `@cannot('delete', $femboy) ... @endcannot` |
| Cadena condicional | `@can ... @elsecan ... @endcan` |
| Algun permís | `@canany(['update','delete'], $femboy) ... @endcanany` |
