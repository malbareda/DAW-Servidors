# Creació de Models i Migracions a Laravel

## Introducció

Els models i les migracions són components fonamentals en el desenvolupament d'aplicacions amb Laravel. Aquest document explica com crear-los correctament, basant-se en els exemples proporcionats.

## Models a Laravel

Els models en Laravel representen les taules de la base de dades i permeten interactuar amb elles de manera orientada a objectes.

### Creació d'un Model

Per crear un model, utilitzem la comanda artisan:

```bash
php artisan make:model NomModel
```

Per crear un model amb la seva migració associada:

```bash
php artisan make:model NomModel -m
```

### Estructura Bàsica d'un Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomModel extends Model
{
    // Configuració i relacions
}
```

### Elements Importants en un Model

1. **Definició de la taula**
   ```php
   protected $table = 'nom_taula';
   ```
   Si no s'especifica, Laravel assumeix que és el nom del model en plural (i en minúscules).

2. **Clau primària**
   ```php
   protected $primaryKey = 'id';
   ```
   Per defecte és 'id'. Només cal especificar-ho si és diferent.

3. **Camps assignables massivament**
   ```php
   protected $fillable = ['camp1', 'camp2', 'camp3'];
   ```
   Defineix quins camps poden ser assignats massivament (per exemple, amb el mètode `create()`).

4. **Camps protegits**
   ```php
   protected $guarded = ['id'];
   ```
   Defineix quins camps NO poden ser assignats massivament. No es pot utilitzar juntament amb `$fillable`.

5. **Timestamps**
   ```php
   public $timestamps = false;
   ```
   Per defecte és `true`. Si és `false`, Laravel no gestionarà automàticament els camps `created_at` i `updated_at`.

6. **Format de dates**
   ```php
   protected $dateFormat = 'U';
   ```
   Defineix el format de les dates (per defecte és Y-m-d H:i:s).

7. **Casts (conversió automàtica de tipus)**
   ```php
   protected $casts = [
       'actiu' => 'boolean',
       'data_hora' => 'datetime',
       'preu' => 'decimal:2',
   ];
   ```
   `$casts` diu a Laravel com tractar cada camp quan el llegeix de la base de dades. Sense casts, tot arriba com a string. Amb casts, Laravel fa la conversió automàticament:
   - `'boolean'` → el camp arriba com a `true`/`false` (no com `0`/`1`)
   - `'datetime'` → el camp arriba com un objecte Carbon, que permet fer `$model->data_hora->format('d/m/Y')` o `$model->data_hora->addDays(7)`
   - `'decimal:2'` → el camp arriba com a string amb 2 decimals (útil per a preus)
   - `'array'` → si guardes JSON a la BD, arriba com a array PHP
   - `'integer'`, `'float'` → conversions numèriques

   **Casts amb enums de PHP** (recomanat a partir de PHP 8.1):
   
   En lloc de tractar els enums com a strings, podeu crear un enum PHP i fer que Laravel el gestioni automàticament:
   
   ```php
   // Fitxer: app/Enums/TipusConcert.php
   namespace App\Enums;
   
   enum TipusConcert: string
   {
       case CONCERT = 'CONCERT';
       case DJ_SET = 'DJ_SET';
       case SHOWCASE = 'SHOWCASE';
       case ACUSTIC = 'ACUSTIC';
   }
   ```
   
   Al model:
   ```php
   use App\Enums\TipusConcert;
   
   protected $casts = [
       'tipus' => TipusConcert::class,
   ];
   ```
   
   Ara `$concert->tipus` retorna un `TipusConcert::CONCERT` (un objecte enum) en lloc d'un string `"CONCERT"`. Això dóna autocompletat a l'IDE, evita errors tipogràfics, i permet fer `$concert->tipus === TipusConcert::DJ_SET`.
   
   > **Nota**: la columna `enum` a la migració (`$table->enum('tipus', [...])`) i el PHP Enum al model són coses separades. La migració defineix les restriccions a la base de dades. El `$casts` al model defineix com PHP tracta el valor. Podeu usar l'un sense l'altre, però junts donen la màxima seguretat.

### Definició de Relacions

1. **Relació Un a Molts (1:N)**

   Des del costat "1":
   ```php
   public function nomRelacioEnPlural()
   {
       return $this->hasMany(ModelRelacionat::class);
   }
   ```

   Des del costat "N":
   ```php
   public function nomRelacioEnSingular()
   {
       return $this->belongsTo(ModelRelacionat::class);
   }
   ```

   Exemple del codi proporcionat:
   ```php
   // A Llibre.php (costat "1")
   public function prestecs()
   {
       return $this->hasMany(Prestec::class);
   }

   // A Prestec.php (costat "N")
   public function llibre()
   {
       return $this->belongsTo(Llibre::class);
   }
   ```

2. **Relació Molts a Molts (N:M)**

   ```php
   public function nomRelacioEnPlural()
   {
       return $this->belongsToMany(ModelRelacionat::class);
   }
   ```

   Exemple del codi proporcionat:
   ```php
   // A Bibliotecari.php
   public function llibres()
   {
       return $this->belongsToMany(Llibre::class);
   }

   // A Llibre.php
   public function bibliotecaris()
   {
       return $this->belongsToMany(Bibliotecari::class);
   }
   ```

3. **Relació Un a Un (1:1)**

   ```php
   // Des del costat propietari
   public function nomRelacioEnSingular()
   {
       return $this->hasOne(ModelRelacionat::class);
   }

   // Des de l'altre costat
   public function nomRelacioEnSingular()
   {
       return $this->belongsTo(ModelRelacionat::class);
   }
   ```

### Model Events (Events del Cicle de Vida)

Laravel dispara events automàticament quan es creen, actualitzen o eliminen registres. Podem "enganxar-nos" a aquests events per executar lògica automàtica.

#### Exemple Pràctic: Generar un codi únic automàticament

Imaginem que cada `Entrada` (tiquet) necessita un codi únic generat automàticament quan es crea:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entrada extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'preu_pagat', 'codi_entrada'];

    /**
     * El mètode boot() s'executa quan Laravel "arrenca" el model.
     * Aquí podem registrar events.
     */
    protected static function boot()
    {
        parent::boot();  // IMPORTANT: sempre cridar el parent

        // Abans de crear un registre nou...
        static::creating(function ($entrada) {
            // Genera un codi únic de 10 caràcters
            $entrada->codi_entrada = 'ENT-' . strtoupper(Str::random(10));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }
}
```

Ara, quan fem `Entrada::create([...])`, el camp `codi_entrada` s'omplirà automàticament sense que l'haguem de passar manualment.

#### Events disponibles

| Event | Quan s'executa | Ús típic |
|---|---|---|
| `creating` | Abans de crear (INSERT) | Generar codis, assignar valors per defecte |
| `created` | Després de crear | Enviar notificacions, registrar log |
| `updating` | Abans d'actualitzar (UPDATE) | Validar canvis, calcular valors derivats |
| `updated` | Després d'actualitzar | Sincronitzar dades, notificar |
| `deleting` | Abans d'eliminar (DELETE) | Comprovar dependències, netejar fitxers |
| `deleted` | Després d'eliminar | Registrar log d'eliminació |

#### Altre exemple: Calcular un valor derivat

```php
protected static function boot()
{
    parent::boot();

    // Abans d'actualitzar, recalcular el preu total
    static::updating(function ($comanda) {
        $comanda->preu_total = $comanda->quantitat * $comanda->preu_unitari;
    });
}
```

> **Nota**: els events `creating`/`updating` permeten modificar l'objecte ABANS que s'escrigui a la base de dades. Els events `created`/`updated` s'executen DESPRÉS — l'objecte ja s'ha guardat.

## Migracions a Laravel

Les migracions són com el control de versions per a la base de dades, permetent definir i modificar l'esquema de manera programàtica.

### Creació d'una Migració

```bash
php artisan make:migration create_nom_taula_table
```

O juntament amb el model:

```bash
php artisan make:model NomModel -m
```

### Estructura Bàsica d'una Migració

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nom_taula', function (Blueprint $table) {
            // Definició de l'esquema
        });
    }

    public function down()
    {
        Schema::dropIfExists('nom_taula');
    }
};
```

### Elements Importants en una Migració

1. **Camps Bàsics**
   ```php
   $table->id();                         // Clau primària autoincremental
   $table->timestamps();                 // created_at i updated_at
   $table->string('nom', 100);           // VARCHAR amb longitud opcional
   $table->text('descripcio');           // TEXT
   $table->integer('quantitat');         // INTEGER
   $table->decimal('preu', 8, 2);        // DECIMAL amb precisió i escala
   $table->boolean('actiu');             // BOOLEAN
   $table->date('data_naixement');       // DATE
   $table->datetime('data_hora');        // DATETIME
   $table->time('hora');                 // TIME
   $table->enum('estat', ['A', 'B', 'C']); // ENUM amb valors possibles
   $table->json('propietats');           // JSON
   ```

2. **Modificadors de Camps**
   ```php
   $table->string('email')->unique();    // Valor únic
   $table->integer('edat')->nullable();  // Permet valors NULL
   $table->string('nom')->default('Anònim'); // Valor per defecte
   $table->integer('vots')->unsigned();  // Només valors positius
   ```

3. **Claus Foranes (Relacions)**
   ```php
   // Forma bàsica
   $table->foreignId('user_id')->constrained();

   // Especificant taula
   $table->foreignId('user_id')->constrained('users');

   // Amb accions en cascada
   $table->foreignId('user_id')
         ->constrained()
         ->onUpdate('cascade')
         ->onDelete('cascade');
   ```

4. **Índexs**
   ```php
   $table->index('columna');             // Índex simple
   $table->unique(['columna1', 'columna2']); // Índex únic compost
   ```

### Modificar Taules Existents (Schema::table)

Fins ara hem vist `Schema::create()` per crear taules noves. Però què passa quan la taula ja existeix i volem afegir-hi columnes? Per exemple, afegir un camp `escenari_id` a la taula `concerts` que ja existeix.

#### Crear la migració

```bash
php artisan make:migration add_escenari_id_to_concerts_table
```

> **Convenció de noms**: `add_X_to_Y_table` per afegir columnes, `remove_X_from_Y_table` per treure-les. Laravel llegeix aquest nom i dedueix quina taula modificar.

#### Estructura de la migració

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table (no Schema::create!) — modifica una taula existent
        Schema::table('concerts', function (Blueprint $table) {
            $table->foreignId('escenari_id')
                  ->nullable()            // nullable perquè els concerts existents no en tenen
                  ->constrained()
                  ->onDelete('cascade')
                  ->after('artista_id');   // posiciona la columna després d'artista_id
        });
    }

    public function down(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropForeign(['escenari_id']);  // primer eliminar la FK
            $table->dropColumn('escenari_id');      // després la columna
        });
    }
};
```

**Diferències clau amb `Schema::create()`**:

| | `Schema::create()` | `Schema::table()` |
|---|---|---|
| **Quan** | Taula nova | Taula que ja existeix |
| **Què fa** | Crea la taula sencera | Modifica (afegeix/treu/canvia columnes) |
| **`down()`** | `Schema::dropIfExists('taula')` | Desfà els canvis concrets |
| **`->nullable()`** | Opcional | Sovint necessari (els registres existents necessiten un valor) |

#### Modificadors útils per a `Schema::table()`

```php
Schema::table('concerts', function (Blueprint $table) {
    // Afegir columna en una posició concreta
    $table->boolean('cobert')->default(false)->after('ubicacio');

    // Canviar el tipus d'una columna (requereix doctrine/dbal)
    $table->string('nom', 200)->change();

    // Renombrar una columna
    $table->renameColumn('nom_antic', 'nom_nou');

    // Eliminar una columna
    $table->dropColumn('columna_obsoleta');
});
```

> **Alerta**: si feu `migrate:fresh`, l'ordre de les migracions importa. La migració que fa `Schema::table('concerts')` ha d'executar-se DESPRÉS de la que fa `Schema::create('concerts')`. Laravel les executa per ordre cronològic (pel timestamp del nom del fitxer), així que si les creeu en l'ordre correcte, tot anirà bé.

### Taules Pivot per a Relacions N:M

Per a relacions Molts a Molts, necessitem crear una taula pivot:

```php
Schema::create('model1_model2', function (Blueprint $table) {
    $table->id();
    $table->foreignId('model1_id')->constrained()->onDelete('cascade');
    $table->foreignId('model2_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

Exemple del codi proporcionat:
```php
Schema::create('bibliotecari_llibre', function (Blueprint $table) {
    $table->id();
    $table->foreignId("bibliotecari_id")->constrained()->onDelete('cascade');
    $table->foreignId("llibre_id")->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

## Exemples Complets

### Model: Llibre

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Llibre extends Model
{
    // Nom de la taula (opcional si segueix la convenció)
    protected $table = 'llibres';

    // Clau primària (opcional si és 'id')
    protected $primaryKey = 'id';

    // Camps assignables massivament
    protected $fillable = ["titol", "categoria"];

    // Relació 1:N (un llibre té molts préstecs)
    public function prestecs()
    {
        return $this->hasMany(Prestec::class);
    }

    // Relació N:M (un llibre pot estar gestionat per molts bibliotecaris)
    public function bibliotecaris()
    {
        return $this->belongsToMany(Bibliotecari::class);
    }
}
```

### Migració: Llibres

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llibres', function (Blueprint $table) {
            $table->id();
            $table->string("titol");                                  // Camp VARCHAR
            $table->enum("categoria", ["FICCIO", "NOFICCIO"]);        // Camp ENUM
            $table->timestamps();                                     // created_at i updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llibres');
    }
};
```

### Model: Prestec

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestec extends Model
{
    protected $table = 'prestecs';
    protected $fillable = ["actiu", "data", "llibre_id", "user_id"];

    // Relació N:1 (molts préstecs pertanyen a un llibre)
    public function llibre()
    {
        return $this->belongsTo(Llibre::class);
    }

    // Relació N:1 (molts préstecs pertanyen a un usuari)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Migració: Prestecs

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestecs', function (Blueprint $table) {
            $table->id();
            $table->boolean("actiu");                 // Camp Boolean
            $table->datetime("data");                 // Camp Datetime
            
            // Claus foranes amb eliminació en cascada
            $table->foreignId("llibre_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestecs');
    }
};
```

## Convencions de Nomenclatura

1. **Models**: Singular, primera lletra majúscula (CamelCase), ex: `User`, `ProductCategory`
2. **Taules**: Plural, minúscules, separades per guions baixos (snake_case), ex: `users`, `product_categories`
3. **Claus primàries**: Normalment `id`
4. **Claus foranes**: NomModel en singular + `_id`, ex: `user_id`, `category_id`
5. **Taules pivot**: Noms dels models en singular, en ordre alfabètic, separats per guió baix, ex: `role_user`

## Bones Pràctiques

1. **Definir els fills**: Sempre definir els camps `$fillable` o `$guarded` per evitar assignacions massives no desitjades.
2. **Convencions de noms**: Seguir les convencions de Laravel per aprofitar millor les funcionalitats automàtiques.
3. **Noms de relacions**: Singular per a relacions `belongsTo`, plural per a `hasMany` i `belongsToMany`.
4. **Seguretat en migracions**: Utilitzar restriccions i validacions a nivell de base de dades.
5. **Gestió en cascada**: Definir què passa amb els registres relacionats quan s'elimina un registre principal.

## Comandes Útils

```bash
# Executar migracions
php artisan migrate

# Revertir migracions
php artisan migrate:rollback

# Revertir i tornar a executar migracions
php artisan migrate:refresh

# Eliminar totes les taules i tornar a executar migracions
php artisan migrate:fresh

# Estat de les migracions
php artisan migrate:status
```
