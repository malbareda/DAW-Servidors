# Guia d'Instal·lació de Laravel

## Introducció
Laravel és un framework PHP modern, d'alt rendiment i elegant, ideal per a desenvolupar aplicacions web. Aquesta guia us mostrarà com instal·lar Laravel correctament utilitzant PHPStorm.

## Requisits previs
- PHP >= 8.2
- Composer
- PHPStorm
- Extensió de MySQL activada a PHP
- Node.js i npm (per al frontend)

## Passos per a la instal·lació

### 1. Obrir PHPStorm
Primer de tot, obriu l'IDE PHPStorm a l'ordinador.

### 2. Crear un projecte Laravel amb Composer
- Aneu a "File" > "New Project"
- Seleccioneu "Composer Project" a l'esquerra
- Al camp de "Package", escriviu `laravel/laravel`
- A la versió, seleccioneu la versió 11.6
- Escolliu la ubicació on voleu crear el projecte
- Feu clic a "Create"

### 3. Instal·lar dependències
Un cop creat el projecte, necessitem instal·lar totes les dependències:
- Feu clic al botó "Install vendor" que apareix a la notificació
- Alternativament, obriu una terminal dins de PHPStorm i executeu:
  ```
  composer install
  ```

### 4. Verificar la instal·lació amb Artisan
Per comprovar que la instal·lació bàsica ha funcionat correctament:
- Obriu una terminal dins de PHPStorm
- Executeu:
  ```
  php artisan serve
  ```

### 5. Generar clau d'encriptació
Si veieu un error indicant que falten claus d'encriptació, genereu-les amb:
```
php artisan key:generate
```
Aquest comandament afegirà automàticament la clau al fitxer `.env`.

### 6. Configurar la base de dades
Editeu el fitxer `.env` del vostre projecte:
- Canvieu `DB_CONNECTION` a `mysql`
- Configureu els paràmetres de la base de dades:
  ```
  DB_DATABASE=nom_de_la_vostra_base_de_dades
  DB_USERNAME=el_vostre_usuari
  DB_PASSWORD=la_vostra_contrasenya
  ```

### 7. Instal·lar Laravel UI i configurar Bootstrap amb Autenticació
Per afegir una interfície d'usuari amb Bootstrap i el sistema d'autenticació de Laravel:

1. Instal·leu el paquet Laravel UI:
```
composer require laravel/ui
```

2. Genereu l'scaffolding de Bootstrap amb autenticació:
```
php artisan ui bootstrap --auth
```

3. Instal·leu les dependències de frontend:
```
npm install
```

4. Compileu els assets:
```
npm run dev
```

Això instal·larà Bootstrap i generarà les vistes, controladors i rutes necessàries per al sistema d'autenticació (login, registre, recuperació de contrasenya, etc.).

### 8. Executar migracions per a l'autenticació
Després d'instal·lar l'autenticació, necessiteu migrar les taules d'usuaris a la base de dades:
```
php artisan migrate
```

### 9. Iniciar el servidor de desenvolupament
Finalment, torneu a iniciar el servidor de desenvolupament:
```
php artisan serve
```
Aquest comandament iniciarà un servidor local a `http://127.0.0.1:8000`. Obriu aquesta URL al vostre navegador i hauríeu de veure la pàgina de benvinguda de Laravel amb els enllaços d'autenticació.

## Solució de problemes comuns

### No troba composer.json quan fas composer install
En la finestra de crear projecte de PHPStorm, treu el field --no-install dels command line parameters

### Error de permisos
Si trobeu errors de permisos amb el directori `storage`:
```
php artisan storage:link
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Problemes amb la base de dades
Si teniu problemes de connexió amb la base de dades:
1. Comproveu que MySQL està en execució
2. Verifiqueu les credencials d'accés
3. Assegureu-vos que la base de dades existeix:
   ```
   php artisan db:create
   ```

### Problemes amb npm
Si teniu errors en compilar els assets:
1. Actualitzeu npm: `npm install -g npm`
2. Esborreu la carpeta node_modules i torneu a instal·lar: 
   ```
   rm -rf node_modules
   npm install
   ```
3. Proveu amb: `npm run dev -- --force`

## Conclusions
Ara teniu un entorn de Laravel funcionant correctament amb Bootstrap i autenticació, i esteu preparats per començar a desenvolupar la vostra aplicació web. Recordeu que Laravel disposa d'una excel·lent documentació oficial a [laravel.com](https://laravel.com/docs) i una gran comunitat que us pot ajudar amb qualsevol problema que trobeu.