# Comandes Importants de PHP Artisan en Laravel

## Introducció
PHP Artisan és la interfície de línia de comandes (CLI) inclosa amb Laravel. Proporciona un gran nombre de comandes útils que poden ajudar-vos mentre creeu la vostra aplicació. A continuació es detallen les comandes més importants relacionades amb migracions, models, controladors, rutes i seeders.

## Comandes de Migració de Base de Dades

### Migració Bàsica
```bash
php artisan migrate
```
Executa totes les migracions pendents.

### Reset i Rollback
```bash
php artisan migrate:rollback
```
Reverteix l'últim "batch" de migracions.

```bash
php artisan migrate:rollback --step=2
```
Reverteix els últims 2 "batches" de migracions.

```bash
php artisan migrate:reset
```
Reverteix totes les migracions de l'aplicació.

### Fresh i Refresh
```bash
php artisan migrate:fresh
```
Elimina totes les taules i després executa el comando `migrate`. És la més important quan vols començar des de zero amb la base de dades.

```bash
php artisan migrate:fresh --seed
```
Elimina totes les taules, executa totes les migracions i després sembra la base de dades.

```bash
php artisan migrate:refresh
```
Reverteix totes les migracions i després executa el comando `migrate` de nou (conserva les dades).

```bash
php artisan migrate:refresh --seed
```
Reverteix totes les migracions, executa totes les migracions i després sembra la base de dades.

### Status
```bash
php artisan migrate:status
```
Mostra l'estat de cada migració.

## Comandes de Creació (Make)

### Models
```bash
php artisan make:model Nom
```
Crea un nou model.

```bash
php artisan make:model Nom -m
```
Crea un nou model i una migració corresponent.

```bash
php artisan make:model Nom -mc
```
Crea un nou model, una migració i un controlador.

```bash
php artisan make:model Nom -mcr
```
Crea un nou model, una migració, un controlador amb mètodes de recursos.

```bash
php artisan make:model Nom --all
```
Crea un model amb totes les relacions (migració, seeder, factory, controlador, política).

### Migracions
```bash
php artisan make:migration create_nom_taula_table
```
Crea una nova migració per crear una taula.

```bash
php artisan make:migration add_columna_to_nom_taula_table
```
Crea una nova migració per afegir columnes a una taula existent.

### Controladors
```bash
php artisan make:controller NomController
```
Crea un nou controlador.

```bash
php artisan make:controller NomController --resource
```
Crea un nou controlador amb mètodes de recursos (index, create, store, show, edit, update, destroy).

```bash
php artisan make:controller NomController --api
```
Crea un nou controlador API amb mètodes de recursos (index, store, show, update, destroy).

### Seeders
```bash
php artisan make:seeder NomSeeder
```
Crea una nova classe seeder.

## Comandes per a Routes

```bash
php artisan route:list
```
Llista totes les rutes registrades a l'aplicació.

```bash
php artisan route:cache
```
Crea un fitxer de cache per a rutes més ràpides.

```bash
php artisan route:clear
```
Elimina el cache de rutes.

## Comandes d'Autenticació

```bash
php artisan make:auth
```
Genera un scaffold d'autenticació bàsic (en versions antigues de Laravel).

```bash
php artisan ui bootstrap --auth
```
Genera interfícies d'usuari Bootstrap amb autenticació (Laravel 8+).

```bash
php artisan ui vue --auth
```
Genera interfícies d'usuari Vue amb autenticació.

```bash
php artisan ui react --auth
```
Genera interfícies d'usuari React amb autenticació.

```bash
php artisan breeze:install
```
Instal·la Laravel Breeze, una implementació d'autenticació mínima amb Tailwind CSS (Laravel 8+).

```bash
php artisan jetstream:install livewire
```
Instal·la Laravel Jetstream amb Livewire, oferint autenticació, verificació d'email, gestió de sessions, API via Laravel Sanctum.

```bash
php artisan jetstream:install inertia
```
Instal·la Laravel Jetstream amb Inertia.js.

## Exemple d'Ús Comú en Desenvolupament

```bash
# Crear un model amb migració i controlador
php artisan make:model Producte -mc

# Executar les migracions
php artisan migrate

# Si cal recomençar la base de dades des de zero
php artisan migrate:fresh --seed

# Crear un seeder per carregar dades inicials
php artisan make:seeder ProductesSeeder

# Executar els seeders
php artisan db:seed

# Executar un seeder específic
php artisan db:seed --class=ProductesSeeder
```

## Consells

1. Utilitzeu `migrate:fresh` durant el desenvolupament per reiniciar la base de dades, però MAI en producció.
2. La bandera `--help` es pot afegir a qualsevol comanda per veure les opcions disponibles.
3. Per opcions avançades de migració, consulteu la documentació oficial de Laravel.
