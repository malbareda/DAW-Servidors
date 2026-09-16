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
