# PHP per a gent que ja sap Java (i JavaScript)

## Introducció: Per què PHP?

Veniu de Java (primer curs) i JavaScript (client). PHP és el tercer vèrtex del triangle: un llenguatge que s'assembla als dos però no és cap dels dos. La bona notícia és que la sintaxi és tan familiar que en una sessió ja podreu llegir codi PHP sense problemes. La menys bona és que PHP té les seves pròpies manies — i si no les coneixeu d'entrada, us passareu mitja hora buscant un punt i coma que en realitat és una fletxa.

> **Nota**: aquest document cobreix la sintaxi de PHP que necessitareu per treballar amb Laravel. No cobreix coses com PHP pur per generar HTML inline (`<?php echo "hola" ?>` dins d'un `.php` barrejat amb HTML), ni funcions de PHP que Laravel ja abstrau per vosaltres. Si una cosa no surt aquí, o bé no la necessiteu, o bé la trobareu al document de Laravel corresponent.

---

## Variables i tipus

### Declaració de variables

| Concepte | Java | JavaScript | PHP |
|---|---|---|---|
| Declarar variable | `String nom = "Ada";` | `let nom = "Ada";` | `$nom = "Ada";` |
| Constant | `final int MAX = 100;` | `const MAX = 100;` | `const MAX = 100;` o `define('MAX', 100);` |
| Tipus | Explícit obligatori | Dinàmic | Dinàmic (tipat opcional des de PHP 7.4) |
| Prefix | Cap | Cap | `$` sempre |

```php
// PHP: totes les variables comencen amb $
$nom = "Ada Lovelace";      // string
$edat = 36;                  // integer
$altura = 1.65;              // float
$actiu = true;               // boolean
$puntuacions = [90, 85, 92]; // array

// Constant (dins d'una classe)
class Config {
    const MAX_INTENTS = 3;
}
// Accés: Config::MAX_INTENTS
```

> **Atenció Java lovers**: no hi ha `int`, `String`, `boolean` obligatori. PHP és dinàmic com JS. Però a Laravel veureu *type hints* per tot arreu (que semblen Java):
> ```php
> public function buscar(string $titol, int $limit = 10): array
> ```
> Són opcionals al llenguatge, però Laravel els utilitza massivament i nosaltres també ho farem.

---

## Strings

### Cometes simples vs dobles

Això és una de les coses que Java NO té i JS sí (amb template literals):

```php
$nom = "Grace";

// Cometes dobles: interpola variables (com template literals de JS)
echo "Hola, $nom!";           // Hola, Grace!
echo "Té {$nom}s i més";      // Claus per delimitar

// Cometes simples: literal, no interpola (com cometes normals de Java)
echo 'Hola, $nom!';           // Hola, $nom!

// Concatenació: amb punt (.), NO amb + (Java) ni + (JS)
echo "Hola, " . $nom . "!";  // Hola, Grace!
```

| Operació | Java | JavaScript | PHP |
|---|---|---|---|
| Concatenar | `"a" + "b"` | `"a" + "b"` | `"a" . "b"` |
| Interpolar | No té (o `String.format`) | `` `Hola ${nom}` `` | `"Hola $nom"` o `"Hola {$nom}"` |
| Longitud | `str.length()` | `str.length` | `strlen($str)` |

> **El punt (`.`) és l'enemic invisible**: vindreu de dos llenguatges on `+` concatena. En PHP, `+` suma. Si feu `"hola" + "mon"` obtindreu `0`. Benvinguts.

---

## Arrays

PHP té un sol tipus `array` que fa de tot: llista, diccionari (map), cua... Tot és array. No hi ha `ArrayList`, `HashMap`, `Map`, `Set` com a Java.

### Arrays indexats (com llistes)

```php
// Crear
$fruites = ["poma", "plàtan", "kiwi"];  // Sintaxi curta (PHP 5.4+)
$fruites = array("poma", "plàtan", "kiwi"); // Sintaxi antiga (la veureu en docs vells)

// Accedir
$fruites[0]; // "poma" — igual que Java i JS

// Afegir al final
$fruites[] = "maduixa";    // PHP way
array_push($fruites, "maduixa"); // També vàlid
```

### Arrays associatius (com Map/Object)

Aquí ve la diferència gran. En Java necessiteu un `HashMap`. En JS, un objecte `{}`. En PHP, és el mateix array:

```php
$alumne = [
    "nom" => "Linus",
    "cognom" => "Torvalds",
    "edat" => 54,
    "llenguatges" => ["C", "PHP", "Python"]
];

// Accedir
$alumne["nom"];          // "Linus"
$alumne["llenguatges"][0]; // "C"

// Afegir
$alumne["email"] = "linus@linux.org";
```

| Operació | Java (HashMap) | JavaScript (Object) | PHP (Array associatiu) |
|---|---|---|---|
| Crear | `Map.of("k", "v")` | `{k: "v"}` | `["k" => "v"]` |
| Accedir | `.get("k")` | `.k` o `["k"]` | `["k"]` |
| Clau-valor | `→` no existeix | `:` | `=>` |

### Funcions útils d'arrays

```php
// Comptar elements
count($fruites);   // PHP (no .length ni .size())

// Comprovar si existeix una clau
array_key_exists("nom", $alumne); // true

// Comprovar si un valor existeix
in_array("poma", $fruites); // true

// Recórrer
foreach ($fruites as $fruita) {
    echo $fruita;
}

// Recórrer amb clau i valor
foreach ($alumne as $clau => $valor) {
    echo "$clau: $valor";
}

// Filtrar (equivalent a .filter() de JS)
$grans = array_filter($edats, function($edat) {
    return $edat >= 18;
});

// Transformar (equivalent a .map() de JS)
$noms_majuscules = array_map(function($nom) {
    return strtoupper($nom);
}, $noms);

// Ordenar
sort($fruites);           // Ordena l'array directament (modifica l'original!)
$ordenat = collect($fruites)->sort(); // Laravel way (no modifica l'original)
```

> **Compte**: `array_filter` i `array_map` en PHP reben els paràmetres al revés que en JS. En JS feu `array.filter(fn)`. En PHP, `array_filter($array, $fn)`. I `array_map` els rep en l'altre ordre: `array_map($fn, $array)`. Sí, inconsistent dins del propi PHP. Benvinguts (bis).

---

## Estructures de control

### Condicionals

Pràcticament iguals que Java i JS:

```php
if ($edat >= 18) {
    echo "Major d'edat";
} elseif ($edat >= 16) {    // elseif! Tot junt! (else if separat també funciona)
    echo "Quasi";
} else {
    echo "Menor";
}

// Ternari — idèntic a Java i JS
$missatge = $edat >= 18 ? "Major" : "Menor";

// Null coalescing — com JS (??) però molt usat a Laravel
$nom = $request->input('nom') ?? "Anònim";
// Equivalent JS: const nom = request.input('nom') ?? "Anònim";
// Java no tenia ?? fins fa poc (Java té Optional, que és una altra bèstia)

// Elvis operator (no existeix a Java ni JS estàndard)
$nom = $valor ?: "default";  // Si $valor és truthy, l'usa; si no, "default"
```

### Comparació: == vs ===

Funciona EXACTAMENT com JavaScript (i diferent de Java):

```php
// == compara valor (amb conversió de tipus, com JS)
0 == "0"      // true  (com JS)
0 == false    // true  (com JS)
"" == false   // true  (com JS)

// === compara valor I tipus (com JS)
0 === "0"     // false (com JS)
0 === false   // false (com JS)
```

> En Java, `==` compara referències en objectes i valor en primitius. En PHP i JS, `==` fa *type juggling* i `===` és l'estricte. **Regla d'or a PHP (com a JS): useu sempre `===` llevat que tingueu un motiu per no fer-ho.**

### Bucles

```php
// for — idèntic a Java
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// while — idèntic a Java
while ($condicio) {
    // ...
}

// foreach — la versió PHP del enhanced for de Java / for...of de JS
foreach ($fruites as $fruita) {
    echo $fruita;
}

// foreach amb clau (no existeix directe en Java; en JS teniu for...in o entries())
foreach ($alumne as $clau => $valor) {
    echo "$clau: $valor";
}
```

| Bucle | Java | JavaScript | PHP |
|---|---|---|---|
| Per índex | `for (int i=0; ...)` | `for (let i=0; ...)` | `for ($i=0; ...)` |
| Per element | `for (String s : llista)` | `for (const s of llista)` | `foreach ($llista as $s)` |
| Amb clau-valor | No directe | `for (const [k,v] of map)` | `foreach ($map as $k => $v)` |

---

## Funcions

### Funcions normals

```php
// PHP
function sumar(int $a, int $b): int {
    return $a + $b;
}

// Valors per defecte (com JS, no existia a Java fins records)
function saludar(string $nom = "món"): string {
    return "Hola, $nom!";
}
```

| Aspecte | Java | JavaScript | PHP |
|---|---|---|---|
| Keyword | Tipus de retorn (implícit) | `function` | `function` |
| Type hints | Obligatoris | No | Opcionals |
| Return type | Al principi | No | `: tipus` al final |
| Valors defecte | Overloading | `= valor` | `= valor` |

### Funcions anònimes (closures)

```php
// PHP — similar a JS
$doble = function($n) {
    return $n * 2;
};

// Arrow function (PHP 7.4+) — com JS però amb fn
$doble = fn($n) => $n * 2;

// Atenció: les arrow functions de PHP NOMÉS poden tenir UNA expressió
// Per a més línies, heu d'usar function() { }
```

| Sintaxi | Java | JavaScript | PHP |
|---|---|---|---|
| Lambda/Arrow | `(a) -> a * 2` | `(a) => a * 2` | `fn($a) => $a * 2` |
| Fletxa | `->` | `=>` | `=>` |
| Keyword | Cap | Cap | `fn` |
| Multi-línia | `(a) -> { ... }` | `(a) => { ... }` | `function($a) { ... }` |

> **La fletxa `=>` en PHP NO és una arrow function** (que usa `fn =>`). La `=>` sola és el separador clau-valor dels arrays associatius. No confongueu:
> ```php
> // Això és un array
> $x = ["clau" => "valor"];
> 
> // Això és una arrow function
> $x = fn($a) => $a * 2;
> ```

### Scope de variables en closures

Aquí PHP és diferent de Java I de JS:

```php
$factor = 3;

// En JS, la closure captura $factor automàticament. En PHP, NO.
$multiplicar = function($n) {
    return $n * $factor;  // ERROR! $factor no existeix aquí
};

// Cal usar `use` per capturar variables de l'scope exterior
$multiplicar = function($n) use ($factor) {
    return $n * $factor;  // Ara sí
};

// Les arrow functions (fn =>) SÍ capturen automàticament (com JS)
$multiplicar = fn($n) => $n * $factor;  // Funciona sense use
```

---

## Classes i OOP

### Sintaxi bàsica

```php
class Alumne {
    // Propietats amb visibilitat (com Java, no com JS)
    private string $nom;
    private int $edat;
    public bool $actiu = true;
    
    // Constructor
    public function __construct(string $nom, int $edat) {
        $this->nom = $nom;
        $this->edat = $edat;
    }
    
    // Getter
    public function getNom(): string {
        return $this->nom;
    }
    
    // Mètode
    public function esAdult(): bool {
        return $this->edat >= 18;
    }
    
    // Mètode estàtic (com Java)
    public static function crear(string $nom, int $edat): self {
        return new self($nom, $edat);
    }
}

// Instanciar
$alumne = new Alumne("Ada", 25);
$alumne->getNom();       // "Ada"
$alumne->actiu;          // true
Alumne::crear("Bob", 30); // Mètode estàtic
```

### Taula comparativa OOP

| Concepte | Java | JavaScript (ES6) | PHP |
|---|---|---|---|
| Classe | `class Nom { }` | `class Nom { }` | `class Nom { }` |
| Constructor | `Nom(args)` | `constructor(args)` | `__construct(args)` |
| `this` | `this.prop` | `this.prop` | `$this->prop` |
| Estàtic | `Nom.metode()` | `Nom.metode()` | `Nom::metode()` |
| Herència | `extends` | `extends` | `extends` |
| Interfície | `implements` | No nativa | `implements` |
| Accés | `public/private/protected` | `#private` (nou) | `public/private/protected` |
| Propietat | `this.x` | `this.x` | `$this->x` |
| Mètode | `this.m()` | `this.m()` | `$this->m()` |
| Crida estàtica | `Classe.m()` | `Classe.m()` | `Classe::m()` |
| Abstract | `abstract class` | No | `abstract class` |

> **La fletxa `->` vs `::`**: En PHP, `->` és per accedir a propietats i mètodes d'una *instància* (com el `.` de Java i JS). `::` és per mètodes i constants *estàtics* o de classe. 
> ```php
> $alumne->getNom();    // Instància: ->
> Alumne::crear("a", 1); // Estàtic: ::
> ```
> En Java, tot és `.`. En PHP, heu de distingir.

### Constructor simplificat (PHP 8+)

PHP 8 va afegir una sintaxi que us agradarà si veniu de Kotlin o TypeScript:

```php
// Abans (verbose, com Java clàssic)
class Producte {
    private string $nom;
    private float $preu;
    
    public function __construct(string $nom, float $preu) {
        $this->nom = $nom;
        $this->preu = $preu;
    }
}

// PHP 8+ (constructor promotion — la propietat es declara i assigna al constructor)
class Producte {
    public function __construct(
        private string $nom,
        private float $preu
    ) {}
}
// Fa exactament el mateix! Veureu això sovint a codi Laravel modern.
```

### Traits (no existeixen a Java ni JS)

Un trait és com una interfície amb implementació. Java no en té (fins fa poc amb els *default methods* a les interfícies). JS no en té (usa mixins informalment).

```php
trait TeTimestamps {
    public function getCreatedAt(): string {
        return $this->created_at;
    }
}

trait TeSlug {
    public function getSlug(): string {
        return strtolower(str_replace(' ', '-', $this->nom));
    }
}

class Article {
    use TeTimestamps, TeSlug;  // "Importa" els mètodes dels traits
    
    public function __construct(
        public string $nom,
        public string $created_at
    ) {}
}
```

> **Per què importa?** Perquè a Laravel els models utilitzen traits constantment: `HasFactory`, `SoftDeletes`, `HasApiTokens`... Quan veieu `use HasFactory;` dins d'un model, és això.

---

## Namespaces i imports

| Concepte | Java | JavaScript | PHP |
|---|---|---|---|
| Declarar espai | `package com.app;` | (mòduls amb export) | `namespace App\Models;` |
| Importar | `import com.app.Cosa;` | `import { Cosa } from './cosa'` | `use App\Models\Cosa;` |
| Separador | `.` | `/` | `\` |

```php
// Fitxer: app/Models/Llibre.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Prestec;

class Llibre extends Model {
    // ...
}
```

> **La barra inversa `\`**: Java usa `.`, JS usa `/`, PHP usa `\`. No pregunteu per què. Accepteu-ho i seguiu endavant.

---

## Null handling

PHP té diverses maneres de tractar valors nuls, algunes familiars i altres no:

```php
// Null coalescing (??) — idèntic a JS
$nom = $variable ?? "default";

// Nullsafe operator (?->) — com ?. de JS i Kotlin
$ciutat = $user?->adreça?->ciutat;
// Equivalent JS: const ciutat = user?.adreça?.ciutat;
// En Java: no existia fins molt recentment

// isset() — comprova si la variable existeix i NO és null
if (isset($variable)) {
    // existeix i no és null
}

// empty() — comprova si és "buit" (null, "", 0, false, [])
if (empty($variable)) {
    // és buit
}

// Nullable type hints (com Java Optional però integrat)
function buscar(string $nom): ?Alumne {
    // El ? indica que pot retornar null
    return Alumne::where('nom', $nom)->first();
}
```

---

## Type casting i conversions

```php
// Cast explícit (com Java)
$enter = (int) "42";       // 42
$decimal = (float) "3.14"; // 3.14
$text = (string) 42;       // "42"
$array = (array) $objecte; // Converteix propietats a array
$bool = (bool) 1;          // true

// Funcions de conversió
intval("42");     // 42
floatval("3.14"); // 3.14
strval(42);       // "42"

// Comprovar tipus
is_string($x);
is_int($x);
is_array($x);
is_null($x);
is_bool($x);
gettype($x);    // Retorna el nom del tipus com a string
```

| Operació | Java | JavaScript | PHP |
|---|---|---|---|
| Cast | `(int) x` | No directe | `(int) $x` |
| A string | `String.valueOf(x)` | `String(x)` | `(string) $x` o `strval($x)` |
| A enter | `Integer.parseInt(s)` | `parseInt(s)` | `(int) $s` o `intval($s)` |
| Comprovar tipus | `instanceof` | `typeof` | `is_string()`, `instanceof` |

---

## Gestió d'errors

```php
// try-catch — pràcticament idèntic a Java
try {
    $resultat = operacioPerillosa();
} catch (Exception $e) {
    echo $e->getMessage();
} catch (TypeError $e) {
    // Podem fer catch de tipus específics (com Java)
    echo "Error de tipus: " . $e->getMessage();
} finally {
    // S'executa sempre (com Java)
    echo "Fet!";
}

// Llançar excepcions (com Java)
throw new Exception("Alguna cosa ha anat malament");
throw new \InvalidArgumentException("Paràmetre invàlid");
```

> A Laravel rarament fareu try-catch manual. Laravel gestiona les excepcions per vosaltres amb el Handler d'excepcions. Però heu d'entendre la sintaxi perquè la trobareu en documentació i paquets.

---

## Diferències traïdores (cheat sheet de supervivència)

Coses que us faran perdre 30 minuts la primera setmana:

| Trampa | Què passa | Per què |
|---|---|---|
| Oblidar el `$` | `nom = "Ada";` → Error | Totes les variables porten `$` |
| Concatenar amb `+` | `"hola" + " món"` → `0` | En PHP es concatena amb `.` |
| `->` vs `::` | `Classe->metode()` → Error | `->` instància, `::` estàtic |
| `=>` vs `->` | Confusió total | `=>` arrays associatius, `->` accés a objectes |
| `elseif` vs `else if` | Tots dos funcionen | Però `elseif` és l'estàndard PHP |
| Arrays 0-indexed | `$a = []; $a[5] = "x";` funciona | PHP no es queixa, crea forats |
| `echo` vs `return` | `echo` imprimeix, `return` retorna | A Laravel, quasi sempre `return` |
| Punt i coma obligatori | `echo "hola"` → Error | Sempre `;` al final (com Java, no com JS on a vegades és opcional) |
| `==` fa coses estranyes | `"php" == 0` és `true` (PHP < 8) | Useu `===` sempre |
| `$this` és obligatori | `$this->propietat` | No es pot ometre com en Java (`propietat` directe no funciona) |

---

## Funcions habituals de PHP que trobareu a Laravel

No són funcions de Laravel — són PHP pur, però les veureu constantment:

### Strings
```php
strlen($str)              // Longitud
strtolower($str)          // Minúscules
strtoupper($str)          // Majúscules  
trim($str)                // Eliminar espais inici/final
str_replace("a", "b", $str) // Reemplaçar
explode(",", $str)        // Split → array (com .split() de JS/Java)
implode(",", $array)      // Join → string (com .join() de JS)
str_contains($str, "foo") // Conté? (PHP 8+, com .contains() de Java)
str_starts_with($str, "H") // Comença amb? (PHP 8+)
str_ends_with($str, "!")   // Acaba amb? (PHP 8+)
substr($str, 0, 5)        // Substring
sprintf("Hola %s, tens %d anys", $nom, $edat) // Format (com Java)
```

### Arrays
```php
count($arr)               // Longitud (no .length!)
array_merge($a, $b)       // Unir arrays (com spread [...a, ...b] de JS)
array_keys($assoc)        // Claus d'un associatiu
array_values($assoc)      // Valors d'un associatiu
array_unique($arr)        // Eliminar duplicats
array_slice($arr, 1, 3)   // Subarray (com .slice() de JS)
array_splice($arr, 1, 2)  // Tallar i opcionalment inserir
usort($arr, fn($a, $b) => $a - $b); // Ordenar amb funció custom
array_map(fn($x) => $x * 2, $arr)   // Map
array_filter($arr, fn($x) => $x > 0) // Filter
array_reduce($arr, fn($carry, $x) => $carry + $x, 0) // Reduce
```

### Dates i temps
```php
date('Y-m-d')             // Data actual formatada: "2025-09-20"
time()                    // Timestamp Unix
strtotime('+1 week')      // Convertir text a timestamp
// Però a Laravel, usareu Carbon (molt més potent):
// now(), Carbon::parse(), ->addDays(7), etc.
```

### Matemàtiques
```php
abs($n)                   // Valor absolut
ceil($n)                  // Arrodonir amunt
floor($n)                 // Arrodonir avall
round($n, 2)              // Arrodonir a 2 decimals
max($a, $b)               // Màxim
min($a, $b)               // Mínim
rand(1, 100)              // Aleatori entre 1 i 100
```

### JSON
```php
// PHP ↔ JSON (equivalent a JSON.parse/stringify de JS)
$json = json_encode($array);    // Array/Object → JSON string
$array = json_decode($json, true); // JSON string → Array (true = associatiu)
// El segon paràmetre `true` és important: sense ell retorna un stdClass object
```

---

## Resum visual: el triangle PHP-Java-JS

```
                        JAVA
                       /    \
            tipat fort /      \ OOP clàssica
                     /        \
                    /    PHP    \
                   / (el fill    \
                  /   del mig)    \
                 /                 \
         JAVASCRIPT ─────────────── 
              dinàmic, closures,
              ?? i ?., arrays flexibles
```

**PHP ha agafat de Java**: classes amb visibilitat (`public/private/protected`), interfícies, abstract, type hints, try-catch, `instanceof`.

**PHP ha agafat de JS**: tipat dinàmic, `==` vs `===`, null coalescing (`??`), arrow functions, arrays que fan de tot.

**PHP ha inventat de collita pròpia**: `$` a les variables, `.` per concatenar, `->` per accedir a objectes, `::` per estàtics, `=>` per arrays associatius, `use` per capturar scope en closures, traits.

---

## Exercici zero: tradueix mentalment

Abans de tocar Laravel, mireu aquest codi Java i penseu com seria en PHP:

```java
// Java
public class Alumne {
    private String nom;
    private int edat;
    private List<String> assignatures;
    
    public Alumne(String nom, int edat) {
        this.nom = nom;
        this.edat = edat;
        this.assignatures = new ArrayList<>();
    }
    
    public boolean esAdult() {
        return this.edat >= 18;
    }
    
    public String info() {
        return this.nom + " (" + this.edat + " anys)";
    }
}
```

<details>
<summary>Solució en PHP</summary>

```php
class Alumne {
    private string $nom;
    private int $edat;
    private array $assignatures;
    
    public function __construct(string $nom, int $edat) {
        $this->nom = $nom;
        $this->edat = $edat;
        $this->assignatures = [];
    }
    
    public function esAdult(): bool {
        return $this->edat >= 18;
    }
    
    public function info(): string {
        return $this->nom . " (" . $this->edat . " anys)";
        // O amb interpolació:
        // return "{$this->nom} ({$this->edat} anys)";
    }
}

// O amb constructor promotion (PHP 8+):
class Alumne {
    public function __construct(
        private string $nom,
        private int $edat,
        private array $assignatures = []
    ) {}
    
    public function esAdult(): bool {
        return $this->edat >= 18;
    }
    
    public function info(): string {
        return "{$this->nom} ({$this->edat} anys)";
    }
}
```
</details>

---

*Ara ja podeu llegir codi PHP sense que us soni a xinès. Al següent document, veurem com Laravel utilitza tot això per fer màgia amb Eloquent, Blade i companyia.*
