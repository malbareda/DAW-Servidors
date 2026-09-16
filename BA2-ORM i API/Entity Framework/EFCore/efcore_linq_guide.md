# Guia LINQ amb Entity Framework Core

## 1. Introducció a LINQ amb EF Core

LINQ (Language Integrated Query) és la manera principal d'interactuar amb dades en Entity Framework Core. Les consultes LINQ es tradueixen automàticament a SQL, permetent escriure consultes de bases de dades directament en C#.

**Avantatges de LINQ amb EF Core:**
- Seguretat de tipus en temps de compilació
- IntelliSense i autocompletat
- Traducció automàtica a SQL optimitzat
- Sintaxi elegant i llegible
- Reutilització de codi

## 2. Operacions Bàsiques de Lectura

### Obtenir tots els elements
```csharp
List<Professor> professors = ctx.Professor.ToList();
```
Aquesta és la forma més bàsica de consulta LINQ. El mètode `ToList()` executa la consulta i converteix els resultats en una llista de professors. 

**Important:** `ToList()` carrega tots els registres a memòria. En taules grans, considera utilitzar paginació amb `Take()` i `Skip()`.

### Execució Diferida (Deferred Execution)
```csharp
// Aquesta consulta NO s'executa immediatament
IQueryable<Professor> query = ctx.Professor.Where(p => p.nom.StartsWith("M"));

// S'executa quan es crida ToList(), Count(), First(), etc.
List<Professor> resultats = query.ToList();
```
LINQ utilitza execució diferida: les consultes no s'executen fins que es demana un resultat concret.

### Comptar elements
```csharp
// Compte de tots els professors
int total = ctx.Professor.Count();

// Compte amb filtre
int totalActius = ctx.Professor.Count(p => p.horesAssignades > 0);

// LongCount per a taules molt grans (retorna long)
long totalGran = ctx.Professor.LongCount();
```
`Count()` és eficient perquè executa un `SELECT COUNT(*)` a la base de dades en lloc de carregar tots els registres.

### Cerca amb Where
```csharp
// Filtre simple
var professors = ctx.Professor
    .Where(p => p.cognom == "Albareda")
    .ToList();

// Múltiples filtres encadenats (es combinen amb AND)
var professors = ctx.Professor
    .Where(p => p.nom.StartsWith("M"))
    .Where(p => p.horesAssignades > 10)
    .ToList();

// Filtre amb condicions complexes
var professors = ctx.Professor
    .Where(p => p.cognom == "Albareda" || p.cognom == "Santamaria")
    .ToList();
```

### Obtenir un únic resultat

```csharp
// FirstOrDefault - Primer element o null
Professor prof = ctx.Professor
    .Where(p => p.cognom == "Albareda")
    .FirstOrDefault();

// First - Primer element o excepció si no n'hi ha
Professor prof = ctx.Professor
    .Where(p => p.cognom == "Albareda")
    .First();

// SingleOrDefault - Un únic element o null (excepció si n'hi ha més d'un)
Professor prof = ctx.Professor
    .Where(p => p.ProfessorID == 1)
    .SingleOrDefault();

// LastOrDefault - Últim element (requereix OrderBy)
Professor prof = ctx.Professor
    .OrderBy(p => p.ProfessorID)
    .LastOrDefault();
```

**Bones pràctiques:**
- Usa `FirstOrDefault()` quan pugui no haver-hi resultats
- Usa `SingleOrDefault()` quan esperes exactament 0 o 1 resultat
- Usa `First()` només quan estàs segur que hi ha resultats

### Cerca per clau primària amb Find
```csharp
// Find per clau primària simple
Professor prof = ctx.Professor.Find(2);

// Find per clau primària composta
var inscripcio = ctx.Inscripcio.Find(estudiantId, modulId);

// Find amb async
Professor prof = await ctx.Professor.FindAsync(2);
```
`Find()` és optimitzat per a claus primàries:
1. Primer cerca a la memòria cache del context
2. Si no ho troba, va a la base de dades
3. És més eficient que `Where(p => p.ID == 2).FirstOrDefault()`

### Comprovar condicions

```csharp
// All - Comprova si tots compleixen la condició
bool totsAmbG = ctx.Professor.All(p => p.nom.StartsWith("G"));

// Any - Comprova si almenys un compleix la condició
bool hiHaAlgunActiu = ctx.Professor.Any(p => p.horesAssignades > 0);

// Any sense paràmetres - Comprova si hi ha algun element
bool hiHaProfessors = ctx.Professor.Any();

// Contains - Comprova si un valor està en una llista
var cognoms = new[] { "Albareda", "Santamaria" };
var professors = ctx.Professor
    .Where(p => cognoms.Contains(p.cognom))
    .ToList();
```

## 3. Projecció i Transformació

### Select - Projectar dades
```csharp
// Seleccionar només algunes propietats (tipus anònim)
var noms = ctx.Professor
    .Select(p => new { p.nom, p.cognom })
    .ToList();

// Crear un nou objecte amb dades transformades
var profInfo = ctx.Professor
    .Select(p => new { 
        NomComplet = p.nom + " " + p.cognom,
        ModulsAssignats = p.Moduls.Count(),
        EsActiu = p.horesAssignades > 0
    })
    .ToList();

// Projecció a un tipus definit (DTO)
public class ProfessorDTO
{
    public string NomComplet { get; set; }
    public int NumModuls { get; set; }
}

var dtos = ctx.Professor
    .Select(p => new ProfessorDTO
    {
        NomComplet = p.nom + " " + p.cognom,
        NumModuls = p.Moduls.Count()
    })
    .ToList();
```

**Avantatges de Select:**
- Millora el rendiment (només selecciona les columnes necessàries)
- Redueix el tràfic de xarxa
- Permet transformacions de dades
- Crea objectes més simples per a la interfície d'usuari

### SelectMany - Aplanar col·leccions
```csharp
// Obtenir tots els mòduls de tots els professors (aplanat)
var totsElsModuls = ctx.Professor
    .SelectMany(p => p.Moduls)
    .ToList();

// SelectMany amb projecció
var assignacions = ctx.Professor
    .SelectMany(p => p.Moduls, (prof, modul) => new
    {
        Professor = prof.nom,
        Modul = modul.nom
    })
    .ToList();
```

## 4. Filtres amb Where

### Condicions simples
```csharp
// Igualtat
var profs = ctx.Professor.Where(p => p.cognom == "Albareda").ToList();

// Comparacions numèriques
var profs = ctx.Professor.Where(p => p.horesAssignades > 10).ToList();
var profs = ctx.Professor.Where(p => p.horesAssignades >= 10 && p.horesAssignades <= 20).ToList();

// Operadors lògics
var profs = ctx.Professor
    .Where(p => p.nom.StartsWith("M") || p.cognom.StartsWith("M"))
    .ToList();

// Negació
var profs = ctx.Professor.Where(p => !p.nom.StartsWith("M")).ToList();
```

### Mètodes de String
```csharp
// StartsWith, EndsWith, Contains
var profs = ctx.Professor.Where(p => p.nom.StartsWith("Ma")).ToList();
var profs = ctx.Professor.Where(p => p.cognom.EndsWith("ez")).ToList();
var profs = ctx.Professor.Where(p => p.nom.Contains("arc")).ToList();

// ToLower/ToUpper per a comparacions case-insensitive
var profs = ctx.Professor
    .Where(p => p.nom.ToLower() == "marc")
    .ToList();

// EF.Functions per a funcions específiques de SQL
var profs = ctx.Professor
    .Where(p => EF.Functions.Like(p.nom, "M%"))
    .ToList();
```

**Nota important:** En EF Core, pots utilitzar `EF.Functions` per accedir a funcions SQL específiques com `Like`, `Collate`, etc.

### Navegació de Relacions en Where
```csharp
// Filtrar per propietats d'entitats relacionades
var profsDam = ctx.Professor
    .Where(p => p.Curs.Cicle == CicleFormatiu.DAM)
    .ToList();

// Filtrar per col·leccions relacionades
var profsAmbModuls = ctx.Professor
    .Where(p => p.Moduls.Any())
    .ToList();

var profsAmbMoltsModuls = ctx.Professor
    .Where(p => p.Moduls.Count() > 3)
    .ToList();

// Condicions complexes amb relacions
var moduls = ctx.Modul
    .Where(m => m.Curs.Actiu && m.Professors.Any(p => p.horesAssignades > 10))
    .ToList();
```

## 5. Ordenació

### OrderBy i ThenBy
```csharp
// Ordenar per un camp (ascendent)
var profs = ctx.Professor
    .OrderBy(p => p.cognom)
    .ToList();

// Ordenar descendent
var profs = ctx.Professor
    .OrderByDescending(p => p.horesAssignades)
    .ToList();

// Ordenació múltiple
var profs = ctx.Professor
    .OrderBy(p => p.cognom)
    .ThenBy(p => p.nom)
    .ToList();

// Combinació ascendent i descendent
var profs = ctx.Professor
    .OrderBy(p => p.cognom)
    .ThenByDescending(p => p.horesAssignades)
    .ToList();
```

**Important:** Sempre has d'incloure `OrderBy` abans d'utilitzar `Take`, `Skip`, `First`, o `Last` per garantir resultats consistents.

## 6. Paginació

### Take i Skip
```csharp
// Obtenir els 5 primers professors
var primers = ctx.Professor
    .OrderBy(p => p.ProfessorID)
    .Take(5)
    .ToList();

// Saltar els primers 10 i agafar els següents 10 (pàgina 2)
var pagina2 = ctx.Professor
    .OrderBy(p => p.ProfessorID)
    .Skip(10)
    .Take(10)
    .ToList();

// Funció de paginació reutilitzable
public List<Professor> ObtenirPagina(int numeroPagina, int mesuraPagina)
{
    return ctx.Professor
        .OrderBy(p => p.ProfessorID)
        .Skip((numeroPagina - 1) * mesuraPagina)
        .Take(mesuraPagina)
        .ToList();
}
```

### Exemple complet de paginació
```csharp
public class ResultatPaginat<T>
{
    public List<T> Dades { get; set; }
    public int PaginaActual { get; set; }
    public int TotalPagines { get; set; }
    public int TotalElements { get; set; }
}

public ResultatPaginat<Professor> ObtenirProfessorsPaginats(int pagina, int mida)
{
    var total = ctx.Professor.Count();
    var dades = ctx.Professor
        .OrderBy(p => p.cognom)
        .Skip((pagina - 1) * mida)
        .Take(mida)
        .ToList();
    
    return new ResultatPaginat<Professor>
    {
        Dades = dades,
        PaginaActual = pagina,
        TotalPagines = (int)Math.Ceiling(total / (double)mida),
        TotalElements = total
    };
}
```

## 7. Agrupació i Conjunts

### Distinct - Eliminar duplicats
```csharp
// Obtenir tots els anys únics dels cursos
var anysUnics = ctx.Curs
    .Select(c => c.Any)
    .Distinct()
    .ToList();

// Cognoms únics de professors
var cognomsUnics = ctx.Professor
    .Select(p => p.cognom)
    .Distinct()
    .OrderBy(c => c)
    .ToList();
```

### GroupBy - Agrupar dades
```csharp
// Agrupar mòduls per hores setmanals
var modulsPorHores = ctx.Modul
    .GroupBy(m => m.horesSetmana)
    .Select(g => new {
        Hores = g.Key,
        NumModuls = g.Count(),
        NomModuls = g.Select(m => m.nom).ToList()
    })
    .ToList();

// Agrupar amb múltiples agregacions
var estadistiquesPerCicle = ctx.Curs
    .GroupBy(c => c.Cicle)
    .Select(g => new {
        Cicle = g.Key,
        NumCursos = g.Count(),
        CursosActius = g.Count(c => c.Actiu),
        TotalModuls = g.Sum(c => c.Moduls.Count())
    })
    .ToList();

// GroupBy amb navegació de relacions
var profsPorCicle = ctx.Professor
    .GroupBy(p => p.Curs.Cicle)
    .Select(g => new {
        Cicle = g.Key,
        Professors = g.Select(p => p.nom + " " + p.cognom).ToList(),
        TotalHores = g.Sum(p => p.horesAssignades)
    })
    .ToList();
```

## 8. Càrrega de Dades Relacionades

### Lazy Loading (Càrrega Diferida)
```csharp
// AMB UseLazyLoadingProxies() i propietats virtual
var curs = ctx.Curs.Find(1);
// Quan accedim a Professor, es carrega automàticament
var nomProf = curs.Professor.nom;
```

### Eager Loading amb Include (Càrrega Anticipada)
```csharp
// Carregar professors amb els seus mòduls
var profsAmbModuls = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();

// Múltiples includes
var cursosComplets = ctx.Curs
    .Include(c => c.Professor)
    .Include(c => c.Moduls)
    .ToList();

// Include anidat (ThenInclude)
var cursos = ctx.Curs
    .Include(c => c.Moduls)
        .ThenInclude(m => m.Professors)
    .ToList();

// Include amb filtre (EF Core 5.0+)
var professors = ctx.Professor
    .Include(p => p.Moduls.Where(m => m.horesSetmana > 3))
    .ToList();
```

**Quan utilitzar Include:**
- Quan saps que necessitaràs les dades relacionades
- Per evitar el problema N+1 (múltiples consultes)
- Quan vols optimitzar el nombre de consultes a la BD

### Explicit Loading (Càrrega Explícita)
```csharp
var professor = ctx.Professor.Find(1);

// Carregar una col·lecció relacionada
ctx.Entry(professor)
    .Collection(p => p.Moduls)
    .Load();

// Carregar una referència relacionada
var modul = ctx.Modul.Find(6);
ctx.Entry(modul)
    .Reference(m => m.Curs)
    .Load();

// Carregar amb filtre
ctx.Entry(professor)
    .Collection(p => p.Moduls)
    .Query()
    .Where(m => m.horesSetmana > 3)
    .Load();
```

### Select vs Include
```csharp
// Include: Carrega totes les propietats dels mòduls
var profs1 = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();

// Select: Només carrega les propietats especificades (més eficient)
var profs2 = ctx.Professor
    .Select(p => new {
        p.nom,
        Moduls = p.Moduls.Select(m => m.nom)
    })
    .ToList();
```

**Select és més eficient** quan només necessites algunes propietats de les entitats relacionades.

## 9. Agregacions i Càlculs

### Funcions d'Agregació
```csharp
// Count - Comptar elements
int totalModuls = ctx.Modul.Count();
int modulsDam = ctx.Modul.Count(m => m.Curs.Cicle == CicleFormatiu.DAM);

// Sum - Sumar valors
int totalHores = ctx.Modul.Sum(m => m.horesSetmana);
int horesProfessor = ctx.Professor
    .Where(p => p.ProfessorID == 1)
    .SelectMany(p => p.Moduls)
    .Sum(m => m.horesSetmana);

// Average - Mitjana
double mitjanaHores = ctx.Professor.Average(p => p.horesAssignades);

// Min i Max
int minHores = ctx.Modul.Min(m => m.horesSetmana);
int maxHores = ctx.Modul.Max(m => m.horesSetmana);

// Múltiples agregacions
var estadistiques = new {
    TotalModuls = ctx.Modul.Count(),
    MinHores = ctx.Modul.Min(m => m.horesSetmana),
    MaxHores = ctx.Modul.Max(m => m.horesSetmana),
    MitjanaHores = ctx.Modul.Average(m => m.horesSetmana),
    TotalHores = ctx.Modul.Sum(m => m.horesSetmana)
};
```

### Agregacions amb GroupBy
```csharp
// Resum per cicle formatiu
var resumPerCicle = ctx.Modul
    .GroupBy(m => m.Curs.Cicle)
    .Select(g => new {
        Cicle = g.Key,
        NumModuls = g.Count(),
        TotalHores = g.Sum(m => m.horesSetmana),
        MitjanaHores = g.Average(m => m.horesSetmana),
        MinHores = g.Min(m => m.horesSetmana),
        MaxHores = g.Max(m => m.horesSetmana)
    })
    .OrderByDescending(x => x.TotalHores)
    .ToList();

// Càrrega de treball per professor
var carregaProfessors = ctx.Professor
    .Select(p => new {
        Professor = p.nom + " " + p.cognom,
        NumModuls = p.Moduls.Count(),
        TotalHores = p.Moduls.Sum(m => m.horesSetmana),
        HoresDeclarades = p.horesAssignades
    })
    .OrderByDescending(x => x.TotalHores)
    .ToList();
```

## 10. Consultes Avançades

### Subconsultes
```csharp
// Professors amb més mòduls que la mitjana
var mitjanaModuls = ctx.Professor.Average(p => p.Moduls.Count());
var profsActivs = ctx.Professor
    .Where(p => p.Moduls.Count() > mitjanaModuls)
    .ToList();

// Mòduls dels cursos més recents
var anyMaxim = ctx.Curs.Max(c => c.Any);
var modulsRecents = ctx.Modul
    .Where(m => m.Curs.Any == anyMaxim)
    .ToList();
```

### Unions i Interseccions
```csharp
// Union - Combinar dos conjunts (sense duplicats)
var profsDam = ctx.Professor.Where(p => p.Curs.Cicle == CicleFormatiu.DAM);
var profsAsix = ctx.Professor.Where(p => p.Curs.Cicle == CicleFormatiu.ASIX);
var totsProfs = profsDam.Union(profsAsix).ToList();

// Concat - Combinar dos conjunts (amb duplicats)
var totsProfsDuplicats = profsDam.Concat(profsAsix).ToList();

// Intersect - Elements comuns
var profsComuns = profsDam.Intersect(profsAsix).ToList();

// Except - Elements del primer conjunt que no estan al segon
var nomesDam = profsDam.Except(profsAsix).ToList();
```

### Consultes amb Let (variable intermèdia)
```csharp
// Sintaxi de consulta amb let
var resultats = from p in ctx.Professor
                let nomComplet = p.nom + " " + p.cognom
                let numModuls = p.Moduls.Count()
                where numModuls > 2
                orderby nomComplet
                select new {
                    NomComplet = nomComplet,
                    NumeroModuls = numModuls
                };
```

## 11. Operacions amb Relacions Recursives

```csharp
// Obtenir prerequisits d'un mòdul
var modul = ctx.Modul
    .Include(m => m.Prerequisits)
    .FirstOrDefault(m => m.nom == "Accés a Dades");

foreach (var prereq in modul.Prerequisits)
{
    Console.WriteLine($"Prerequisit: {prereq.nom}");
}

// Obtenir mòduls que tenen aquest com a prerequisit
var modulBase = ctx.Modul
    .Include(m => m.EsPrerequisitDe)
    .FirstOrDefault(m => m.nom == "Programació");

foreach (var dependent in modulBase.EsPrerequisitDe)
{
    Console.WriteLine($"Depèn d'aquest: {dependent.nom}");
}

// Trobar mòduls sense prerequisits
var modulsSensePrerequisits = ctx.Modul
    .Where(m => !m.Prerequisits.Any())
    .ToList();

// Trobar mòduls amb més prerequisits
var modulComplexe = ctx.Modul
    .OrderByDescending(m => m.Prerequisits.Count())
    .FirstOrDefault();
```

## 12. Sintaxi de Consulta vs Sintaxi de Mètodes

LINQ ofereix dues sintaxis: consulta (SQL-like) i mètodes (cadena de mètodes).

### Sintaxi de Consulta
```csharp
var resultats = from p in ctx.Professor
                where p.horesAssignades > 10
                orderby p.cognom, p.nom
                select new { p.nom, p.cognom };
```

### Sintaxi de Mètodes (Recomanada)
```csharp
var resultats = ctx.Professor
    .Where(p => p.horesAssignades > 10)
    .OrderBy(p => p.cognom)
    .ThenBy(p => p.nom)
    .Select(p => new { p.nom, p.cognom });
```

**Recomanació:** Utilitza la sintaxi de mètodes ja que:
- És més flexible
- Permet accedir a tots els mètodes LINQ
- És més comú en el codi modern
- És més fàcil d'encadenar amb altres operacions

## 13. Funcions Específiques d'EF Core

### EF.Functions
```csharp
// Like (equivalent a SQL LIKE)
var profs = ctx.Professor
    .Where(p => EF.Functions.Like(p.nom, "M%"))
    .ToList();

// Collate (per a comparacions case-sensitive)
var profs = ctx.Professor
    .Where(p => EF.Functions.Collate(p.nom, "SQL_Latin1_General_CP1_CS_AS") == "Marc")
    .ToList();

// DataLength (longitud en bytes)
var profs = ctx.Professor
    .Where(p => EF.Functions.DataLength(p.nom) > 10)
    .ToList();
```

### Consultes SQL Raw
```csharp
// Consulta SQL directa
var professors = ctx.Professor
    .FromSqlRaw("SELECT * FROM Professors WHERE hores_assignades > {0}", 10)
    .ToList();

// Interpolació segura (protegeix contra SQL Injection)
var minim = 10;
var professors = ctx.Professor
    .FromSqlInterpolated($"SELECT * FROM Professors WHERE hores_assignades > {minim}")
    .ToList();

// Es pot combinar amb LINQ
var professors = ctx.Professor
    .FromSqlRaw("SELECT * FROM Professors WHERE hores_assignades > 10")
    .Where(p => p.nom.StartsWith("M"))
    .OrderBy(p => p.cognom)
    .ToList();
```

## 14. Optimització i Rendiment

### AsNoTracking - Consultes de només lectura
```csharp
// Més ràpid per a consultes de només lectura
var professors = ctx.Professor
    .AsNoTracking()
    .ToList();

// Útil per a dades que no modificaràs
var resum = ctx.Modul
    .AsNoTracking()
    .Select(m => new { m.nom, m.horesSetmana })
    .ToList();
```

**Quan utilitzar AsNoTracking:**
- Consultes de només lectura (reports, visualitzacions)
- Millora el rendiment (no fa tracking dels canvis)
- Redueix la memòria utilitzada

### AsSplitQuery - Divisions de consultes
```csharp
// En lloc d'un JOIN gran, fa múltiples consultes
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .Include(p => p.Curs)
    .AsSplitQuery()  // Divideix en múltiples SELECT
    .ToList();
```

**Quan utilitzar AsSplitQuery:**
- Quan `Include` genera consultes SQL molt grans
- Quan hi ha múltiples col·leccions relacionades
- Pot ser més ràpid en alguns casos

### Compiled Queries - Consultes compilades
```csharp
// Definir una consulta compilada (es compila una sola vegada)
private static readonly Func<ProvaContext, int, Professor> _getByIdCompiled =
    EF.CompileQuery((ProvaContext ctx, int id) =>
        ctx.Professor.FirstOrDefault(p => p.ProfessorID == id));

// Utilitzar la consulta compilada
public Professor GetProfessorById(int id)
{
    return _getByIdCompiled(ctx, id);
}
```

**Avantatges:**
- Millor rendiment per a consultes repetitives
- La traducció de LINQ a SQL es fa una sola vegada

## 15. Bones Pràctiques amb LINQ

### 1. Usar FirstOrDefault en lloc de First
```csharp
// ❌ Pot llançar excepcions
var prof = ctx.Professor.First(p => p.ProfessorID == 999);

// ✅ Retorna null si no troba res
var prof = ctx.Professor.FirstOrDefault(p => p.ProfessorID == 999);
if (prof != null)
{
    // Processar
}
```

### 2. Filtrar abans de projectar
```csharp
// ❌ Menys eficient
var resultats = ctx.Professor
    .Select(p => new { p.nom, p.cognom, p.horesAssignades })
    .Where(x => x.horesAssignades > 10)
    .ToList();

// ✅ Més eficient
var resultats = ctx.Professor
    .Where(p => p.horesAssignades > 10)
    .Select(p => new { p.nom, p.cognom, p.horesAssignades })
    .ToList();
```

### 3. Utilitzar IQueryable fins al final
```csharp
// ❌ ToList() massa aviat
var professors = ctx.Professor.ToList();
var filtrats = professors.Where(p => p.horesAssignades > 10).ToList();

// ✅ Manté IQueryable, filtra a la BD
var filtrats = ctx.Professor
    .Where(p => p.horesAssignades > 10)
    .ToList();
```

### 4. Evitar múltiples enumeracions
```csharp
// ❌ Executa la consulta 3 vegades
var query = ctx.Professor.Where(p => p.horesAssignades > 10);
int count = query.Count();
var first = query.FirstOrDefault();
var list = query.ToList();

// ✅ Executa una sola vegada
var list = ctx.Professor
    .Where(p => p.horesAssignades > 10)
    .ToList();
int count = list.Count;
var first = list.FirstOrDefault();
```

### 5. Usar Include només quan sigui necessari
```csharp
// ❌ Carrega dades innecessàries
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .Include(p => p.Curs)
    .Select(p => new { p.nom })  // Només necessita el nom
    .ToList();

// ✅ No carrega dades innecessàries
var professors = ctx.Professor
    .Select(p => new { p.nom })
    .ToList();
```

### 6. Utilitzar AsNoTracking per consultes de lectura
```csharp
// ✅ Més ràpid per a dades que no modificaràs
var resum = ctx.Modul
    .AsNoTracking()
    .Select(m => new { m.nom, m.horesSetmana })
    .ToList();
```

### 7. Escriure expressions lambda llegibles
```csharp
// ❌ Difícil de llegir
var r = ctx.Professor.Where(p => p.Moduls.Count(m => m.horesSetmana > 3 && m.Curs.Actiu) > 2).ToList();

// ✅ Més llegible
var professors = ctx.Professor
    .Where(p => p.Moduls
        .Count(m => m.horesSetmana > 3 && m.Curs.Actiu) > 2)
    .ToList();
```

## 16. Depuració de Consultes LINQ

### Veure la SQL generada
```csharp
var query = ctx.Professor
    .Where(p => p.horesAssignades > 10)
    .OrderBy(p => p.cognom);

// Obtenir la SQL generada
var sql = query.ToQueryString();
Console.WriteLine(sql);

// Amb logging (al OnConfiguring)
optionsBuilder
    .UseSqlServer(connectionString)
    .LogTo(Console.WriteLine, Microsoft.Extensions.Logging.LogLevel.Information);
```

### Exemple de sortida SQL
```sql
SELECT [p].[ProfessorID], [p].[nom], [p].[cognom], [p].[hores_assignades]
FROM [Professors] AS [p]
WHERE [p].[hores_assignades] > 10
ORDER BY [p].[cognom]
```

## 17. Errors Comuns i Solucions

### Error: "Cannot translate lambda expression"
**Problema:** EF Core no pot traduir certa lògica C# a SQL.
```csharp
// ❌ No es pot traduir
var profs = ctx.Professor
    .Where(p => MiMetodeCustom(p.nom))
    .ToList();

// ✅ Filtra després de carregar (menys eficient)
var profs = ctx.Professor
    .ToList()
    .Where(p => MiMetodeCustom(p.nom))
    .ToList();

// ✅ Millor: usa SQL/LINQ que es pugui traduir
var profs = ctx.Professor
    .Where(p => p.nom.StartsWith("M"))
    .ToList();
```

### Error: "A tracking query projects owned entity without corresponding owner"
**Problema:** Estàs projectant només una entitat de propietat sense el propietari.
```csharp
// ✅ Solució: usa AsNoTracking
var resultats = ctx.Professor
    .AsNoTracking()
    .Select(p => new { ... })
    .ToList();
```

### Problema: N+1 Queries
```csharp
// ❌ Problema N+1: una consulta per cada professor
var professors = ctx.Professor.ToList();
foreach (var prof in professors)
{
    Console.WriteLine(prof.Moduls.Count());  // Consulta per cada iteració!
}

// ✅ Solució: Eager loading amb Include
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();
foreach (var prof in professors)
{
    Console.WriteLine(prof.Moduls.Count());  // Ja està carregat
}
```

## 18. Resum de Mètodes LINQ

| Mètode | Descripció | Retorna |
|--------|------------|---------|
| `Where` | Filtra elements | IQueryable |
| `Select` | Projecta/transforma | IQueryable |
| `SelectMany` | Aplana col·leccions | IQueryable |
| `OrderBy` | Ordena ascendent | IQueryable |
| `OrderByDescending` | Ordena descendent | IQueryable |
| `ThenBy` | Ordenació secundària | IQueryable |
| `Take` | Pren els primers n | IQueryable |
| `Skip` | Salta els primers n | IQueryable |
| `First` | Primer element (excepció si no n'hi ha) | Entitat |
| `FirstOrDefault` | Primer element o null | Entitat? |
| `Single` | Un únic element | Entitat |
| `SingleOrDefault` | Un únic o null | Entitat? |
| `Last` | Últim element | Entitat |
| `Any` | Existeix algun? | bool |
| `All` | Tots compleixen? | bool |
| `Count` | Nombre d'elements | int |
| `Sum` | Suma de valors | numèric |
| `Average` | Mitjana | numèric |
| `Min` | Valor mínim | numèric |
| `Max` | Valor màxim | numèric |
| `GroupBy` | Agrupa elements | IQueryable |
| `Distinct` | Elimina duplicats | IQueryable |
| `Include` | Eager loading | IQueryable |
| `ToList` | Executa i converteix | List |
| `ToArray` | Executa i converteix | Array |
| `AsNoTracking` | Sense tracking | IQueryable |

Aquestes pràctiques i mètodes t'ajudaran a escriure consultes LINQ eficients i mantenibles amb Entity Framework Core.