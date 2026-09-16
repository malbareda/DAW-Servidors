# Guia LINQ amb Entity Framework

## 1. Comandes LINQ del Program.cs

### Obtenir tots els elements
```csharp
List<Professor> listg = ctx.Professor.ToList();
```
Aquesta és la forma més bàsica de consulta LINQ. El mètode `ToList()` executa la consulta i converteix els resultats en una llista de professors. És útil quan necessitem tots els registres d'una taula, però s'ha d'utilitzar amb precaució en taules grans ja que carrega totes les dades a memòria.

### Comptar elements
```csharp
int total = ctx.Professor.Count();
```
`Count()` retorna el número total de registres. És més eficient que fer `ToList().Count` perquè executa un `SELECT COUNT(*)` a la base de dades en lloc de carregar tots els registres.

### Cerca amb Where i FirstOrDefault
```csharp
Professor prof = ctx.Professor
    .Where(p => p.cognom == "Albareda")
    .FirstOrDefault();
```
Aquesta consulta combina dos mètodes importants:
- `Where()` filtra els registres basant-se en una condició
- `FirstOrDefault()` retorna el primer resultat o null si no en troba cap
És més segur que usar `First()` ja que no llença excepcions si no troba resultats.

### Cerca per clau primària amb Find
```csharp
Professor prof = ctx.Professor.Find(2);
```
`Find()` és un mètode optimitzat per buscar per clau primària. Primer mira en la memòria cache del context i només va a la base de dades si no troba l'entitat. És més eficient que fer un `Where` per ID.

### Comprovar una condició amb All
```csharp
bool totsAmbG = ctx.Professor.All(p => p.nom.StartsWith("G"));
```
`All()` verifica si tots els elements compleixen una condició. Retorna true només si tots els professors tenen un nom que comença per "G". És útil per validacions de dades.

## 2. Operacions Bàsiques de LINQ

### Select
```csharp
// Obtenir només noms i cognoms
var noms = ctx.Professor
    .Select(p => new { p.nom, p.cognom })
    .ToList();

// Crear un nou objecte amb dades formatades
var profInfo = ctx.Professor
    .Select(p => new { 
        NomComplet = p.nom + " " + p.cognom,
        ModulsAssignats = p.Moduls.Count()
    })
    .ToList();
```
`Select` permet projectar dades a un nou format. És útil per:
- Seleccionar només les columnes que necessitem, millorant el rendiment
- Transformar les dades en un nou format (com combinar nom i cognom)
- Crear objectes anònims amb les dades que ens interessen
El primer exemple selecciona només nom i cognom, mentre que el segon crea un nou objecte amb el nom complet i el número de mòduls.

### Where amb múltiples condicions
```csharp
// Professors que tenen més de 10 hores i donen algun mòdul
var professors = ctx.Professor
    .Where(p => p.horesAssignades > 10 && p.Moduls.Any())
    .ToList();

// Mòduls actius amb més de 3 hores setmanals
var moduls = ctx.Modul
    .Where(m => m.Curs.Actiu && m.horesSetmana > 3)
    .ToList();
```
`Where` permet filtrar registres amb condicions complexes:
- Podem combinar múltiples condicions amb && (AND) i || (OR)
- Podem navegar a través de relacions (com m.Curs.Actiu)
- Podem usar mètodes com Any() per verificar col·leccions
Aquestes consultes són traduïdes a SQL WHERE clauses eficients.

### OrderBy i ThenBy
```csharp
// Ordenar professors per cognom i després per nom
var profsOrdenats = ctx.Professor
    .OrderBy(p => p.cognom)
    .ThenBy(p => p.nom)
    .ToList();

// Ordenar mòduls per hores descendents
var modulsOrdenats = ctx.Modul
    .OrderByDescending(m => m.horesSetmana)
    .ToList();
```
Els mètodes d'ordenació permeten:
- `OrderBy`: Definir el primer criteri d'ordenació
- `ThenBy`: Afegir criteris secundaris d'ordenació
- `OrderByDescending/ThenByDescending`: Ordenar de major a menor
Es tradueixen a SQL ORDER BY clauses mantenint l'eficiència.

## 3. Operacions amb Conjunts

### Take i Skip (Paginació)
```csharp
// Obtenir els 5 primers professors
var primers = ctx.Professor
    .OrderBy(p => p.ProfessorID)
    .Take(5)
    .ToList();

// Paginació: salt de 10 en 10
var pagina2 = ctx.Professor
    .OrderBy(p => p.ProfessorID)
    .Skip(10)
    .Take(10)
    .ToList();
```
Aquests mètodes són essencials per la paginació:
- `Take(n)`: Selecciona els primers n registres
- `Skip(n)`: Salta els primers n registres
- Combinats permeten implementar paginació de manera eficient
És important sempre incloure un OrderBy per garantir un ordre consistent.

### Distinct i GroupBy
```csharp
// Obtenir tots els anys únics dels cursos
var anysUnics = ctx.Curs
    .Select(c => c.Any)
    .Distinct()
    .ToList();

// Agrupar mòduls per hores setmanals
var modulsPorHores = ctx.Modul
    .GroupBy(m => m.horesSetmana)
    .Select(g => new {
        Hores = g.Key,
        NumModuls = g.Count(),
        Moduls = g.ToList()
    })
    .ToList();
```
- `Distinct()` elimina duplicats, útil per obtenir valors únics
- `GroupBy` permet agrupar registres per una clau i realitzar càlculs per grup
- La combinació amb Select permet crear resums estadístics dels grups

## 4. Operacions amb Relacions

### Include (Eager Loading)
```csharp
// Carregar professors amb els seus mòduls
var profsAmbModuls = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();

// Carregar cursos amb professor i mòduls
var cursosComplets = ctx.Curs
    .Include(c => c.Professor)
    .Include(c => c.Moduls)
    .ToList();
```
`Include` permet carregar relacions de manera anticipada:
- Evita el problema del "N+1" en carregar relacions
- Permet múltiples nivells de càrrega
- És més eficient que carregar les relacions separadament
És important no abusar-ne i només incloure les relacions necessàries.

### Navegació de Relacions
```csharp
// Trobar tots els professors d'un cicle específic
var profsDam = ctx.Professor
    .Where(p => p.Curs.Cicle == CicleFormatiu.DAM)
    .ToList();

// Mòduls amb més d'un professor
var modulsCompartits = ctx.Modul
    .Where(m => m.Professors.Count() > 1)
    .Select(m => new {
        NomModul = m.nom,
        NumProfessors = m.Professors.Count(),
        Professors = m.Professors.Select(p => p.nom)
    })
    .ToList();
```
La navegació de relacions permet:
- Accedir a propietats d'entitats relacionades en consultes
- Realitzar filtres basats en relacions
- Crear projeccions que incloguin dades de múltiples entitats relacionades

## 5. Agregacions i Càlculs

### Funcions d'Agregació
```csharp
// Total d'hores setmanals de tots els mòduls
var totalHores = ctx.Modul.Sum(m => m.horesSetmana);

// Mitjana d'hores assignades per professor
var mitjanaHores = ctx.Professor
    .Average(p => p.horesAssignades);

// Resum estadístic de mòduls
var estatistiques = new {
    TotalModuls = ctx.Modul.Count(),
    MinHores = ctx.Modul.Min(m => m.horesSetmana),
    MaxHores = ctx.Modul.Max(m => m.horesSetmana),
    MitjanaHores = ctx.Modul.Average(m => m.horesSetmana)
};
```
Les funcions d'agregació permeten:
- Calcular totals amb `Sum()`
- Obtenir mitjanes amb `Average()`
- Trobar valors mínims i màxims
- Crear resums estadístics combinant múltiples agregacions
Totes aquestes operacions es realitzen a nivell de base de dades.

### Agregacions Agrupades
```csharp
// Número de mòduls per cicle
var modulsPorCicle = ctx.Modul
    .GroupBy(m => m.Curs.Cicle)
    .Select(g => new {
        Cicle = g.Key,
        NumModuls = g.Count(),
        TotalHores = g.Sum(m => m.horesSetmana)
    })
    .ToList();

// Càrrega de treball per professor
var carregaProfessors = ctx.Professor
    .Select(p => new {
        Professor = p.nom + " " + p.cognom,
        NumModuls = p.Moduls.Count(),
        TotalHores = p.Moduls.Sum(m => m.horesSetmana)
    })
    .OrderByDescending(x => x.TotalHores)
    .ToList();
```
Les agregacions agrupades permeten:
- Calcular estadístiques per grups
- Combinar múltiples càlculs per grup
- Crear informes resumits
Són molt útils per anàlisi de dades i informes.

## 6. Consultes Complexes

### Subconsultes
```csharp
// Professors que donen tots els mòduls d'un curs
var profsDedicats = ctx.Professor
    .Where(p => p.Moduls.Count() == 
        p.Moduls.First().Curs.Moduls.Count())
    .ToList();

// Mòduls amb més professors que la mitjana
var mitjanaProfs = ctx.Modul.Average(m => m.Professors.Count());
var modulsPopulars = ctx.Modul
    .Where(m => m.Professors.Count() > mitjanaProfs)
    .ToList();
```
Les subconsultes permeten:
- Realitzar comparacions amb resultats d'altres consultes
- Filtrar basant-se en càlculs complexos
- Crear condicions dinàmiques
Cal tenir cura amb el rendiment ja que poden generar consultes SQL complexes.

### Joins Complexos
```csharp
// Professors i mòduls del mateix cicle
var assignacions = ctx.Professor
    .SelectMany(p => p.Moduls
        .Where(m => m.Curs.Cicle == p.Curs.Cicle)
        .Select(m => new {
            Professor = p.nom,
            Modul = m.nom,
            Cicle = m.Curs.Cicle
        }))
    .ToList();

// Anàlisi de solapament d'horaris
var solapaments = ctx.Professor
    .SelectMany(p => p.Moduls
        .SelectMany(m1 => p.Moduls
            .Where(m2 => m1.Codi < m2.Codi)
            .Select(m2 => new {
                Professor = p.nom,
                Modul1 = m1.nom,
                Modul2 = m2.nom,
                TotalHores = m1.horesSetmana + m2.horesSetmana
            })))
    .Where(x => x.TotalHores > 8)
    .ToList();
```
Els joins complexos permeten:
- Combinar dades de múltiples entitats de manera complexa
- Crear projeccions personalitzades amb dades relacionades
- Analitzar relacions entre registres de la mateixa taula
Són útils per anàlisis complexos però cal vigilar el rendiment.

## 7. Bones Pràctiques amb LINQ

1. **Usar FirstOrDefault en lloc de First**: Evita excepcions quan no hi ha resultats
2. **Incloure només les relacions necessàries**: Millora el rendiment evitant càrrega innecessària
3. **Filtrar abans de projectar**: Redueix la quantitat de dades processades
4. **Mantenir IQueryable**: Permet que EF optimizi la consulta SQL final
5. **Evitar múltiples enumeracions**: Guarda resultats en variables si s'han de reutilitzar
6. **Escriure expressions lambda llegibles**: Facilita el manteniment del codi

Aquestes pràctiques ajuden a mantenir el codi eficient i mantenible.
