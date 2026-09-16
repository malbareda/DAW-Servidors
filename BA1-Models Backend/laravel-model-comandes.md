# Comandes típiques de Laravel relatives al Model (amb exemples de Biblioteca)

## Recuperació de registres

### find() - Trobar un registre per la seva clau primària
```php
// Troba un llibre amb ID 1
$llibre = Llibre::find(1);

// Exemple real del LlibreController
public function modificar(string $id)
{
    $llibre = Llibre::find($id);
    return view("llibres.modificar", compact("llibre"));
}
```

### findOrFail() - Trobar un registre o llançar excepció
```php
// Troba un préstec amb ID 1 o llança una excepció ModelNotFoundException
$prestec = Prestec::findOrFail(1);

// Exemple adaptat de retornar() en PrestecController
public function retornar($idPrestec){
    $prestec = Prestec::findOrFail($idPrestec);
    // Si no es troba el préstec, es llança excepció automàticament
    
    if($prestec->actiu && Auth::id() == $prestec->user_id ){
        $prestec->actiu = false;
        $prestec->save();
    }
}
```

### first() - Obtenir el primer registre que coincideix
```php
// Obtenir el primer llibre de categoria 'novel·la'
$llibre = Llibre::where('categoria', 'novel·la')->first();

// Exemple real de PrestecController
$prestecQueAcaboDafegir = Prestec::where('llibre_id', $idLlibre)
                                 ->where('user_id', $idUsuari)
                                 ->first();
```

### all() - Obtenir tots els registres
```php
// Obtenir tots els llibres
$llibres = Llibre::all();

// Exemple real de LlibreController
public function cataleg()
{
    $llibres = Llibre::all(); // Agafa tots els llibres
    return response()->json($llibres);
}
```

## Consultes amb condicions

### where() - Filtrar registres
```php
// Llibres de categoria 'novel·la'
$llibres = Llibre::where('categoria', 'novel·la')->get();

// Múltiples condicions (com a PrestecController)
$prestec = Prestec::where('llibre_id', $idLlibre)
                 ->where('user_id', $idUsuari)
                 ->first();

// Operadors de comparació
$prestecs = Prestec::where('data', '>=', now()->subDays(30))->get();
```

### orWhere() - Afegir condició amb OR
```php
// Llibres que són novel·la O infantil
$llibres = Llibre::where('categoria', 'novel·la')
                ->orWhere('categoria', 'infantil')
                ->get();
```

### whereIn() - Condició amb múltiples valors possibles
```php
// Llibres amb certes categories
$llibres = Llibre::whereIn('categoria', ['novel·la', 'història', 'ciència'])->get();
```

### whereNotIn() - Condició amb exclusió de valors
```php
// Llibres que NO són de categoria infantil o juvenil
$llibres = Llibre::whereNotIn('categoria', ['infantil', 'juvenil'])->get();
```

### whereBetween() - Condició amb rang de valors
```php
// Préstecs realitzats entre dues dates
$prestecs = Prestec::whereBetween('data', [
    now()->subDays(30),
    now()
])->get();
```

### whereNull() / whereNotNull() - Condició amb valors nuls
```php
// Préstecs sense data de retorn (encara actius)
$prestecs = Prestec::whereNull('data_retorn')->get();

// Alternativa utilitzant actiu (com al model existent)
$prestecsActius = Prestec::where('actiu', true)->get();
```

## Relacions

### has() - Filtrar models que tenen relacions
```php
// Llibres que tenen préstecs
$llibres = Llibre::has('prestecs')->get();

// Llibres amb més de 3 préstecs
$llibresPopulars = Llibre::has('prestecs', '>=', 3)->get();
```

### whereHas() - Filtrar models amb condicions en les relacions
```php
// Llibres que tenen préstecs actius
$llibresEnPrestec = Llibre::whereHas('prestecs', function ($query) {
    $query->where('actiu', true);
})->get();
```

### doesntHave() - Filtrar models que no tenen relacions
```php
// Llibres que no tenen préstecs
$llibresSensePrestecs = Llibre::doesntHave('prestecs')->get();

// Exemple real de LlibreController
public function sensePrestecs(){
    $llibres = Llibre::doesntHave('prestecs')->get();
    return response()->json($llibres);
}
```

### whereDoesntHave() - Filtrar models que no compleixen condicions en relacions
```php
// Llibres que no tenen préstecs actius
$llibresDisponibles = Llibre::whereDoesntHave('prestecs', function ($query) {
    $query->where('actiu', true);
})->get();
```

### withCount() - Comptar relacions
```php
// Obtenir llibres amb el nombre de préstecs
$llibres = Llibre::withCount('prestecs')->get();
// Resultat accessible com $llibre->prestecs_count

// Obtenir llibres amb el nombre de préstecs actius
$llibres = Llibre::withCount(['prestecs' => function ($query) {
    $query->where('actiu', true);
}])->get();
```

### with() - Carregar relacions eager loading
```php
// Obtenir llibres amb els seus préstecs (eager loading)
$llibres = Llibre::with('prestecs')->get();

// Carregar múltiples relacions
$llibres = Llibre::with(['prestecs', 'bibliotecaris'])->get();

// Carregar relacions amb condicions
$llibres = Llibre::with(['prestecs' => function ($query) {
    $query->where('actiu', true);
}])->get();
```

## Creació i actualització

### create() - Crear un nou registre
```php
// Crear un nou llibre
$llibre = Llibre::create([
    'titol' => 'El Quixot',
    'categoria' => 'novel·la'
]);

// Exemple real de PrestecController
Prestec::create([
    'llibre_id' => $idLlibre,
    'user_id' => $idUsuari,
    'actiu' => true,
    'data' => now()
]);

// Exemple real de LlibreController
public function store(Request $request)
{
    Llibre::create([
       "titol" => $request->input('titol'),
        "categoria" => $request->input('categoria')
    ]);
}
```

### save() - Guardar un model existent o nou
```php
// Crear i guardar un nou llibre
$llibre = new Llibre;
$llibre->titol = 'El Quixot';
$llibre->categoria = 'novel·la';
$llibre->save();

// Actualitzar un llibre existent
$llibre = Llibre::find(1);
$llibre->titol = 'Don Quixot de la Manxa';
$llibre->save();

// Exemple real de PrestecController
public function retornar($idPrestec){
    $prestec = Prestec::find($idPrestec);
    
    if($prestec->actiu && Auth::id() == $prestec->user_id ){
        $prestec->actiu = false;
        $prestec->save();
    }
}
```

### updateOrCreate() - Actualitzar un registre o crear-lo si no existeix
```php
// Actualitzar bibliotecari si existeix, o crear-lo si no
$bibliotecari = Bibliotecari::updateOrCreate(
    ['id' => 1],
    ['nom' => 'Joan Pere']
);
```

### update() - Actualitzar múltiples registres
```php
// Actualitzar la categoria de tots els llibres d'aventures a 'novel·la d'aventures'
Llibre::where('categoria', 'aventures')->update(['categoria' => 'novel·la d\'aventures']);

// Exemple adaptat de LlibreController
public function update(Request $request, string $id)
{
    $llibre = Llibre::find($id);
    $llibre->update($request->all());

    return redirect()->route('llibres.cataleg');
}
```

## Eliminació

### delete() - Eliminar un model
```php
// Eliminar un llibre específic
$llibre = Llibre::find(1);
$llibre->delete();

// Exemple real de LlibreController
public function destroy(string $id)
{
    $llibre = Llibre::find($id);
    $llibre->delete();
}
```

### destroy() - Eliminar models per la seva clau primària
```php
// Eliminar llibre amb ID 1
Llibre::destroy(1);

// Eliminar múltiples llibres
Llibre::destroy([1, 2, 3]);
```

## Agregacions i funcions auxiliars

### count() - Comptar registres
```php
// Comptar tots els llibres
$count = Llibre::count();

// Comptar préstecs actius
$count = Prestec::where('actiu', true)->count();
```

### max() / min() / avg() / sum() - Funcions d'agregació
```php
// Obtenir la data del préstec més recent
$ultimData = Prestec::max('data');

// Comptar quants préstecs té cada usuari
$prestecsPerUsuari = Prestec::select('user_id', DB::raw('count(*) as total'))
                            ->groupBy('user_id')
                            ->get();
```

### chunk() - Processar registres en blocs
```php
// Processar préstecs en blocs de 100
Prestec::chunk(100, function ($prestecs) {
    foreach ($prestecs as $prestec) {
        // Processa cada préstec
    }
});
```

### paginate() - Paginar resultats
```php
// Paginar llibres (per defecte 15 per pàgina)
$llibres = Llibre::paginate();

// Exemple adaptat a un controlador
public function catalegPaginat()
{
    $llibres = Llibre::paginate(10);
    return view('llibres.cataleg', compact('llibres'));
}
```

## Ordre i agrupament

### orderBy() - Ordenar resultats
```php
// Ordenar llibres per títol (ascendent per defecte)
$llibres = Llibre::orderBy('titol')->get();

// Ordenar préstecs per data descendent
$prestecs = Prestec::orderBy('data', 'desc')->get();

// Exemple que es podria aplicar a LlibreController
public function catalegOrdenat()
{
    $llibres = Llibre::orderBy('titol')->get();
    return response()->json($llibres);
}
```

### latest() / oldest() - Ordenar per data
```php
// Ordenar préstecs per data descendent (més recents primer)
$prestecs = Prestec::latest()->get();

// Ordenar préstecs per data ascendent (més antics primer)
$prestecs = Prestec::oldest()->get();
```

### inRandomOrder() - Ordenar aleatòriament
```php
// Obtenir un llibre aleatori
$llibreAleatori = Llibre::inRandomOrder()->first();

// Exemple que es podria aplicar a un mètode recomanacions
public function llibreRecomanat()
{
    $llibre = Llibre::inRandomOrder()->first();
    return view('llibres.recomanacio', compact('llibre'));
}
```

## Relacions i navegació entre models

### belongsTo() - Relació inversa (de molts a un o N:1)
```php
// Definit a Prestec.php
public function llibre(){
    return $this->belongsTo(Llibre::class);
}

public function user(){
    return $this->belongsTo(User::class);
}

// Exemple d'ús
$prestec = Prestec::find(1);
$llibrePrestec = $prestec->llibre; // Accedeix al llibre d'aquest préstec
$usuariPrestec = $prestec->user; // Accedeix a l'usuari d'aquest préstec
```

### hasMany() - Relació d'un a molts (1:N)
```php
// Definit a Llibre.php
public function prestecs()
{
    return $this->hasMany(Prestec::class);
}

// Exemple d'ús
$llibre = Llibre::find(1);
$prestecsDaaquestLlibre = $llibre->prestecs; // Col·lecció de préstecs d'aquest llibre
```

### belongsToMany() - Relació de molts a molts (N:M)
```php
// Definit a Llibre.php
public function bibliotecaris(){
    return $this->belongsToMany(Bibliotecari::class);
}

// Definit a Bibliotecari.php
public function llibres()
{
    return $this->belongsToMany(Llibre::class);
}

// Exemple d'ús
$llibre = Llibre::find(1);
$bibliotecarisDaaquestLlibre = $llibre->bibliotecaris;

$bibliotecari = Bibliotecari::find(1);
$llibresDaaquestBibliotecari = $bibliotecari->llibres;
```

### Manipulació de relacions N:M
```php
// Afegir un bibliotecari a un llibre
$llibre = Llibre::find(1);
$llibre->bibliotecaris()->attach(2); // Afegeix el bibliotecari amb ID 2

// Treure un bibliotecari d'un llibre
$llibre->bibliotecaris()->detach(2); // Treu el bibliotecari amb ID 2

// Sincronitzar bibliotecaris (elimina els anteriors i afegeix només els especificats)
$llibre->bibliotecaris()->sync([1, 3, 5]); // Només tindrem els bibliotecaris 1, 3 i 5
```

## Consultes i accessos habituals

### Accedir als llibres que tenen préstecs actius
```php
$llibresEnPrestec = Llibre::whereHas('prestecs', function($query) {
    $query->where('actiu', true);
})->get();
```

### Obtenir els préstecs d'un usuari específic
```php
$prestecsUsuari = Prestec::where('user_id', Auth::id())->get();

// O més avançat, amb les dades del llibre
$prestecsUsuari = Prestec::with('llibre')
                         ->where('user_id', Auth::id())
                         ->get();
```

### Accedir a un préstec amb la informació completa de llibre i usuari
```php
$prestec = Prestec::with(['llibre', 'user'])
                  ->find(1);

// Accedir a les dades
$titolLlibre = $prestec->llibre->titol;
$nomUsuari = $prestec->user->name;
```

### Comprovar si un llibre està disponible
```php
$llibre = Llibre::find(1);
$disponible = !$llibre->prestecs()->where('actiu', true)->exists();

// O des del controlador
public function esDisponible($idLlibre)
{
    $disponible = !Prestec::where('llibre_id', $idLlibre)
                          ->where('actiu', true)
                          ->exists();
    
    return response()->json(['disponible' => $disponible]);
}
```

## Altres funcions útils

### pluck() - Obtenir una columna específica
```php
// Obtenir tots els títols de llibres
$titols = Llibre::pluck('titol');

// Obtenir títols indexats per ID
$titols = Llibre::pluck('titol', 'id');
```

### value() - Obtenir un valor específic
```php
// Obtenir el títol del llibre amb ID 1
$titol = Llibre::where('id', 1)->value('titol');
```

### exists() / doesntExist() - Comprovar si existeix un registre
```php
// Comprovar si existeix un llibre amb títol específic
$existeix = Llibre::where('titol', 'El Quixot')->exists();

// Comprovar si un usuari té préstecs actius
$tePrestecs = Prestec::where('user_id', Auth::id())
                     ->where('actiu', true)
                     ->exists();
```

### toSql() - Obtenir consulta SQL (útil per depuració)
```php
// Veure la consulta SQL generada (sense executar-la)
$sql = Llibre::whereHas('prestecs', function($q) {
    $q->where('actiu', true);
})->toSql();
```

## Consells addicionals

### Utilitzar models amb JSON API
```php
// Exemple de LlibreController
public function cataleg()
{
    $llibres = Llibre::all();
    return response()->json($llibres);
}

// Amb relacions
public function prestecsUsuari($userId)
{
    $prestecs = Prestec::with('llibre')
                       ->where('user_id', $userId)
                       ->get();
    return response()->json($prestecs);
}
```

### Optimitzar consultes amb select
```php
// Seleccionar només les columnes necessàries
$titolsLlibres = Llibre::select('id', 'titol')->get();

// Útil per a grans quantitats de dades
$prestecsBasics = Prestec::select('id', 'llibre_id', 'user_id', 'data', 'actiu')
                         ->where('actiu', true)
                         ->get();
```
