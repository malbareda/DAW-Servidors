# Seeders i Factories a Laravel

## Índex

1. [Introducció](#introducció)
2. [Factories](#factories)
3. [Seeders](#seeders)
4. [Relacions entre models](#relacions-entre-models)
5. [Estats (States) a les Factories](#estats-states-a-les-factories)
6. [Ordre d'execució](#ordre-dexecució)
7. [Errors comuns](#errors-comuns)

---

## Introducció

Les **Factories** i els **Seeders** són eines de Laravel per generar dades de prova a la base de dades.

- **Factory**: Defineix com crear un model amb dades falses (faker)
- **Seeder**: Executa la creació de múltiples registres utilitzant les factories

Són útils per:
- Desenvolupament local
- Testing
- Demos i presentacions
- Inicialitzar bases de dades amb dades de prova

---

## Factories

### Què és una Factory?

Una factory defineix l'estructura de dades per defecte d'un model. Utilitza la llibreria **Faker** per generar dades realistes.

### Crear una Factory

```bash
php artisan make:factory NomDelModelFactory
```

### Estructura bàsica

```php
<?php

namespace Database\Factories;

use App\Models\Llibre;
use Illuminate\Database\Eloquent\Factories\Factory;

class LlibreFactory extends Factory
{
    protected $model = Llibre::class;

    public function definition(): array
    {
        return [
            'titol' => $this->faker->sentence(),
            'categoria' => $this->faker->randomElement(['FICCIO', 'NOFICCIO']),
        ];
    }
}
```

### Canviar la locale de Faker

Per defecte Faker genera dades en anglès. Pots canviar-ho:

```php
public function definition(): array
{
    $this->faker = \Faker\Factory::create('es_ES'); // Espanyol
    // $this->faker = \Faker\Factory::create('pt_BR'); // Portuguès brasiler
    // $this->faker = \Faker\Factory::create('uk_UK'); // Ucraïnès

    return [
        'nom' => $this->faker->name(),
        'ciutat' => $this->faker->city(),
    ];
}
```

### Exemples de dades Faker comunes

```php
// Textos
$this->faker->name()              // "Joan Garcia"
$this->faker->sentence()          // "Lorem ipsum dolor sit amet."
$this->faker->sentence(10)        // Frase de 10 paraules
$this->faker->text(100)           // Text de màxim 100 caràcters
$this->faker->city()              // "Barcelona"
$this->faker->country()           // "España"

// Números
$this->faker->numberBetween(18, 30)     // Número entre 18 i 30
$this->faker->randomElement(['A', 'B']) // Element aleatori d'un array

// Booleans
$this->faker->boolean()           // true o false (50%)
$this->faker->boolean(70)         // true 70% de les vegades

// Dates
$this->faker->dateTimeBetween('-3 months', 'now')
$this->faker->dateTimeBetween('-30 years', '-18 years')
```

### Factory amb relacions

Quan un model depèn d'un altre (clau forana), tens dues opcions:

#### Opció 1: Models que NO creen bucles

Si la relació NO crea un bucle infinit (per exemple, User no depèn de Prestec), pots crear el model automàticament:

```php
// PrestecFactory.php - Prestec depèn de User i Llibre, però aquests NO depenen de Prestec
public function definition(): array
{
    return [
        'actiu' => $this->faker->boolean(70),
        'data' => $this->faker->dateTimeBetween('-3 months', 'now'),
        'llibre_id' => Llibre::factory(),  // OK: Llibre no depèn de Prestec
        'user_id' => User::factory(),      // OK: User no depèn de Prestec
    ];
}
```

Això funciona perquè NO hi ha bucle: `Prestec` crea `User`, però `User` NO crea `Prestec`.

#### Opció 2: Models que podrien crear bucles

Si hi ha risc de bucle (Femboy depèn de Factory, Factory podria dependre de Femboy), usa `inRandomOrder()->first()?->id`:

```php
// FemboyFactory.php
public function definition(): array
{
    return [
        'nombre' => $this->faker->name(),
        'edad' => $this->faker->numberBetween(18, 30),
        // Agafa una Factory existent aleatòriament
        'factory_id' => \App\Models\Factory::inRandomOrder()->first()?->id,
    ];
}
```

**Important**: Això requereix que ja existeixin registres de `Factory` a la base de dades. Per tant, l'ordre al `DatabaseSeeder` és crucial:

```php
$this->call([
    FactorySeeder::class,  // Primer: crear factories
    FemboySeeder::class,   // Després: els femboys agafen factories existents
]);
```

**Què fa `inRandomOrder()->first()?->id`?**
- `inRandomOrder()`: Ordena els registres aleatòriament
- `first()`: Agafa el primer (que és aleatori)
- `?->`: Operador null-safe (si no hi ha cap registre, retorna null en lloc de petar)
- `id`: Agafa només l'ID

### Exemple complet amb inRandomOrder

```php
// FactoryFactory.php
class FactoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'ubicacion' => $this->faker->city(),
            'cif' => $this->generateCIF(),
        ];
    }
}

// FemboyFactory.php
class FemboyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'edad' => $this->faker->numberBetween(18, 30),
            // Agafa Factory existent
            'factory_id' => \App\Models\Factory::inRandomOrder()->first()?->id,
        ];
    }
}

// DatabaseSeeder.php
public function run(): void
{
    $this->call([
        FactorySeeder::class,  // 1. Crear factories primer
        FemboySeeder::class,   // 2. Els femboys agafen factories existents
    ]);
}

// FemboySeeder.php
public function run(): void
{
    // Simplement crear femboys, la factory s'encarrega de les relacions
    Femboy::factory(10)->create();
}
```
```

---

## Seeders

### Què és un Seeder?

Un seeder crida les factories per crear registres a la base de dades.

### Crear un Seeder

```bash
php artisan make:seeder NomDelSeeder
```

### Estructura bàsica

```php
<?php

namespace Database\Seeders;

use App\Models\Llibre;
use Illuminate\Database\Seeder;

class LlibreSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 15 llibres
        Llibre::factory(15)->create();
    }
}
```

### Dades manuals vs Faker

Pots combinar dades creades manualment amb factories:

```php
public function run(): void
{
    // Dades manuals
    User::factory()->create([
        'name' => 'Administrador',
        'email' => 'admin@biblioteca.cat',
        'password' => Hash::make('password123'),
    ]);

    // Dades amb Faker
    User::factory(20)->create();
}
```

### Assignar relacions al Seeder

Quan hi ha claus foranes, assignar-les al seeder:

```php
public function run(): void
{
    $factories = Factory::all();

    if ($factories->count() === 0) {
        $this->command->error('No hi ha factories disponibles!');
        return;
    }

    // Crear femboys assignant factory_id manualment
    Femboy::factory(10)->create([
        'factory_id' => $factories->random()->id,
    ]);
}
```

### Exemple complet: BibliotecariSeeder

```php
<?php

namespace Database\Seeders;

use App\Models\Bibliotecari;
use Illuminate\Database\Seeder;

class BibliotecariSeeder extends Seeder
{
    public function run(): void
    {
        Bibliotecari::factory(15)->create();
    }
}
```

### Exemple complet: UserSeeder amb manual + Faker

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuari administrador manual
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@biblioteca.cat',
            'password' => Hash::make('password123'),
        ]);

        // 20 usuaris amb Faker
        User::factory(20)->create();
    }
}
```

---

## Relacions entre models

### Relació 1-N

**Exemple**: Un `User` pot tenir molts `Prestec`.

Com que `User` i `Llibre` NO depenen de `Prestec`, podem crear-los directament a la factory:

**Factory**: Crear els models relacionats directament
```php
// PrestecFactory.php
public function definition(): array
{
    return [
        'actiu' => $this->faker->boolean(70),
        'data' => $this->faker->dateTimeBetween('-3 months', 'now'),
        'llibre_id' => Llibre::factory(),  // Crea un llibre automàticament
        'user_id' => User::factory(),      // Crea un user automàticament
    ];
}
```

**Seeder**: Simplement crear els préstecs
```php
// PrestecSeeder.php
public function run(): void
{
    // Simplement crear préstecs, la factory gestiona les relacions
    Prestec::factory(35)->create();
}
```

**Alternativa**: Si vols reutilitzar users i llibres existents en lloc de crear-ne de nous cada vegada:

```php
// PrestecFactory.php
public function definition(): array
{
    return [
        'actiu' => $this->faker->boolean(70),
        'data' => $this->faker->dateTimeBetween('-3 months', 'now'),
        'llibre_id' => Llibre::inRandomOrder()->first()?->id ?? Llibre::factory(),
        'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
    ];
}
```

Això intenta agafar un existent, i si no n'hi ha, en crea un de nou.
```

### Relació N-N

**Exemple**: `Bibliotecari` N-N `Llibre`

Necessites un seeder específic per la taula intermèdia:

```php
<?php

namespace Database\Seeders;

use App\Models\Bibliotecari;
use App\Models\Llibre;
use Illuminate\Database\Seeder;

class BibliotecariLlibreSeeder extends Seeder
{
    public function run(): void
    {
        $bibliotecaris = Bibliotecari::all();
        $llibres = Llibre::all();

        if ($bibliotecaris->count() === 0 || $llibres->count() === 0) {
            $this->command->info('No hi ha bibliotecaris o llibres disponibles!');
            return;
        }

        // Per cada bibliotecari, assignar entre 3 i 8 llibres aleatoris
        foreach ($bibliotecaris as $bibliotecari) {
            $numLlibres = rand(3, min(8, $llibres->count()));
            $llibresAssignats = $llibres->random($numLlibres);

            $bibliotecari->llibres()->attach($llibresAssignats->pluck('id')->toArray());
        }

        // Assegurar que tots els llibres tenen almenys un bibliotecari
        foreach ($llibres as $llibre) {
            if ($llibre->bibliotecaris()->count() === 0) {
                $bibliotecari = $bibliotecaris->random();
                $llibre->bibliotecaris()->attach($bibliotecari->id);
            }
        }
    }
}
```

**Mètodes importants per N-N**:
- `attach($id)` - Afegir una relació
- `detach($id)` - Eliminar una relació
- `sync([$id1, $id2])` - Sincronitzar (elimina velles, afegeix noves)

---

## Estats (States) a les Factories

Els **estats** permeten crear variants d'un model amb la mateixa factory.

### Definir estats

```php
// LlibreFactory.php
class LlibreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titol' => $this->faker->sentence(),
            'categoria' => $this->faker->randomElement(['FICCIO', 'NOFICCIO']),
        ];
    }

    // Estat per llibres de ficció
    public function ficcio(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'categoria' => 'FICCIO',
            ];
        });
    }

    // Estat per llibres de no ficció
    public function noFiccio(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'categoria' => 'NOFICCIO',
            ];
        });
    }
}
```

### Usar estats al Seeder

```php
public function run(): void
{
    // 15 llibres de ficció
    Llibre::factory(15)->ficcio()->create();

    // 15 llibres de no ficció
    Llibre::factory(15)->noFiccio()->create();
}
```

### Estats amb Faker

Quan uses Faker dins d'un estat, utilitza el helper `fake()`:

```php
// ❌ MALAMENT
public function actiu(): static
{
    return $this->state(function (array $attributes) {
        return [
            'actiu' => true,
            'data' => $this->faker->dateTimeBetween('-1 month', 'now'), // NO
        ];
    });
}

// ✅ BÉ
public function actiu(): static
{
    return $this->state(function (array $attributes) {
        return [
            'actiu' => true,
            'data' => fake()->dateTimeBetween('-1 month', 'now'), // SÍ
        ];
    });
}
```

### Exemple complet amb estats

```php
// PrestecFactory.php
class PrestecFactory extends Factory
{
    public function definition(): array
    {
        return [
            'actiu' => $this->faker->boolean(70),
            'data' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'llibre_id' => Llibre::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function actiu(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'actiu' => true,
                'data' => fake()->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    public function retornat(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'actiu' => false,
                'data' => fake()->dateTimeBetween('-6 months', '-1 month'),
            ];
        });
    }
}

// PrestecSeeder.php
public function run(): void
{
    // 15 préstecs actius
    Prestec::factory(15)->actiu()->create();

    // 20 préstecs retornats
    Prestec::factory(20)->retornat()->create();
}
```
```

---

## Ordre d'execució

L'ordre dels seeders és **crucial** quan hi ha relacions.

### DatabaseSeeder.php

Aquest és el seeder principal que crida tots els altres:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,               // 1. Primers els usuaris
            BibliotecariSeeder::class,       // 2. Després els bibliotecaris
            LlibreSeeder::class,             // 3. Després els llibres
            BibliotecariLlibreSeeder::class, // 4. Després les relacions N-N
            PrestecSeeder::class,            // 5. Finalment els préstecs
        ]);
    }
}
```

### Regla general d'ordre

1. **Models sense dependències** (User, Bibliotecari)
2. **Models amb dependències simples** (Llibre)
3. **Relacions N-N** (BibliotecariLlibre)
4. **Models amb múltiples dependències** (Prestec depèn de User i Llibre)

### Executar els seeders

```bash
# Executar tots els seeders
php artisan db:seed

# Executar un seeder específic
php artisan db:seed --class=UserSeeder

# Esborrar BD, tornar a crear i executar seeders
php artisan migrate:fresh --seed
```

---

## Errors comuns

### 1. Non-static method factory() cannot be called statically

**Causa**: El model no té el trait `HasFactory`.

**Solució**:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llibre extends Model
{
    use HasFactory; // Afegir aquesta línia
    
    // ...
}
```

Si el problema persisteix, neteja la cache:
```bash
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### 2. Allowed memory size exhausted

**Causa**: Bucles infinits quan les factories creen altres models que al seu torn creen el primer model, o ús de `realText()`.

**Problema amb realText()**:

```php
// ❌ MALAMENT - Consumeix molta memòria
public function definition(): array
{
    return [
        'descripcion' => $this->faker->realText(30, 2), // NO!
    ];
}

// ✅ BÉ - Alternativa lleugera
public function definition(): array
{
    return [
        'descripcion' => $this->faker->sentence(10), // 10 paraules
        // o
        'descripcion' => $this->faker->text(100), // Màxim 100 caràcters
    ];
}
```

**Problema amb bucles infinits**:

```php
// ❌ MALAMENT - Crea bucle infinit
// FemboyFactory.php
public function definition(): array
{
    return [
        'nombre' => $this->faker->name(),
        'factory_id' => Factory::factory(), // Això crea un Factory
    ];
}

// FactoryFactory.php
public function definition(): array
{
    return [
        'nombre' => $this->faker->company(),
        'femboy_principal_id' => Femboy::factory(), // Això crea un Femboy
    ];
}
// RESULTAT: Femboy crea Factory crea Femboy crea Factory... ♾️
```

**Solució als bucles infinits**:

Usa `inRandomOrder()->first()?->id` per agafar models existents:

```php
// ✅ FemboyFactory.php
public function definition(): array
{
    return [
        'nombre' => $this->faker->name(),
        'edad' => $this->faker->numberBetween(18, 30),
        // Agafa una Factory existent, no en crea una nova
        'factory_id' => \App\Models\Factory::inRandomOrder()->first()?->id,
    ];
}

// ✅ FactoryFactory.php
public function definition(): array
{
    return [
        'nombre' => $this->faker->company(),
        'ubicacion' => $this->faker->city(),
        'cif' => $this->generateCIF(),
        // No crea Femboy aquí!
    ];
}

// ✅ DatabaseSeeder.php - L'ORDRE ÉS CRUCIAL
public function run(): void
{
    $this->call([
        FactorySeeder::class,  // 1. Crear factories primer
        FemboySeeder::class,   // 2. Els femboys agafen factories existents
    ]);
}
```

**Clau**: Quan uses `inRandomOrder()->first()?->id`, el model relacionat JA HA D'EXISTIR a la base de dades. Per això l'ordre al `DatabaseSeeder` és fonamental.

**Quan NO usar inRandomOrder()?**

Quan el model relacionat NO crea bucle (no depèn del model actual):

```php
// ✅ Prestec pot crear User i Llibre directament perquè NO hi ha bucle
// PrestecFactory.php
public function definition(): array
{
    return [
        'actiu' => $this->faker->boolean(70),
        'llibre_id' => Llibre::factory(),  // OK: Llibre no depèn de Prestec
        'user_id' => User::factory(),      // OK: User no depèn de Prestec
    ];
}

### 3. SQLSTATE[23000]: Integrity constraint violation

**Causa**: Intent de crear un registre amb una clau forana que no existeix.

**Solució**: Verifica l'ordre al `DatabaseSeeder`:

```php
// ❌ MALAMENT
$this->call([
    PrestecSeeder::class,  // Necessita User i Llibre
    UserSeeder::class,     // Es crea després!
    LlibreSeeder::class,   // Es crea després!
]);

// ✅ BÉ
$this->call([
    UserSeeder::class,     // Primer
    LlibreSeeder::class,   // Segon
    PrestecSeeder::class,  // Últim (depèn dels anteriors)
]);
```

### 4. Múltiples crides a factory amb estats consumeixen massa memòria

**Problema**:

```php
// Cada crida genera nova instància de Faker
Femboy::factory(10)->joven()->create();
Femboy::factory(8)->reservado()->create();
Femboy::factory(5)->experimentado()->create();
// Si uses realText(), això pot exhaurir la memòria
```

**Solució**:

1. Usa `sentence()` o `text()` en lloc de `realText()`
2. Usa `fake()` dins dels estats en lloc de `$this->faker`

```php
// Factory
public function definition(): array
{
    return [
        'descripcion' => $this->faker->sentence(10), // En lloc de realText()
    ];
}

// Estat
public function joven(): static
{
    return $this->state(function (array $attributes) {
        return [
            'edad' => fake()->numberBetween(18, 22), // fake() en lloc de $this->faker
        ];
    });
}
```

### 5. Faker genera dades en anglès

**Solució**: Canvia la locale al principi del mètode `definition()`:

```php
public function definition(): array
{
    $this->faker = \Faker\Factory::create('es_ES');
    
    return [
        'nom' => $this->faker->name(), // Ara en espanyol
    ];
}
```

Locales comunes:
- `es_ES` - Espanyol
- `ca_ES` - Català
- `pt_BR` - Portuguès brasiler
- `fr_FR` - Francès
- `de_DE` - Alemany
- `uk_UK` - Ucraïnès

---

## Resum de bones pràctiques

1. **Factories sense bucles**: Si el model relacionat NO depèn del model actual, crea'l amb `Model::factory()`
2. **Factories amb bucles**: Si hi ha risc de bucle, usa `Model::inRandomOrder()->first()?->id`
3. **Ordre crucial**: Quan uses `inRandomOrder()`, el model relacionat ha d'existir ABANS
4. **Estats**: Usa `fake()` dins dels estats, no `$this->faker`
5. **Text**: Usa `sentence()` o `text()`, evita `realText()`
6. **Validació**: L'operador `?->` protegeix contra nulls quan no hi ha registres
7. **DatabaseSeeder**: L'ordre dels seeders determina quines relacions funcionen
