# Guia Lazy Loading vs Eager Loading amb Entity Framework Core

## 1. Introducció als Tipus de Càrrega

Entity Framework Core ofereix tres estratègies principals per carregar dades relacionades. Entendre aquestes estratègies és fonamental per escriure aplicacions eficients.

### Tipus de Càrrega
- **Lazy Loading**: Les dades relacionades es carreguen automàticament quan hi accedim
- **Eager Loading**: Les dades relacionades es carreguen explícitament amb la consulta inicial
- **Explicit Loading**: Carreguem manualment les dades relacionades quan ho necessitem

**Important:** En EF Core, el Lazy Loading **NO està activat per defecte**. Cal configurar-lo explícitament.

## 2. Eager Loading amb Include (Recomanat)

### Què és l'Eager Loading?

L'Eager Loading carrega explícitament les dades relacionades en la consulta inicial utilitzant el mètode `Include()`. És l'estratègia **recomanada** en EF Core.

```csharp
using (var ctx = new ProvaContext())
{
    // Carreguem Professor amb els seus Mòduls en una sola consulta
    var professor = ctx.Professor
        .Include(p => p.Moduls)
        .FirstOrDefault(p => p.ProfessorID == 1);
    
    // Moduls ja està carregat, no fa més consultes
    Console.WriteLine($"Té {professor.Moduls.Count()} mòduls");
}
```

### SQL Generat

```sql
SELECT [p].[ProfessorID], [p].[nom], [p].[cognom], ...
       [m].[ModulID], [m].[nom], [m].[horesSetmana], ...
FROM [Professors] AS [p]
LEFT JOIN [ProfessorModul] AS [pm] ON [p].[ProfessorID] = [pm].[ProfessorsID]
LEFT JOIN [Moduls] AS [m] ON [pm].[ModulsID] = [m].[ModulID]
WHERE [p].[ProfessorID] = 1
```

### Include amb Múltiples Relacions

Pots carregar diverses relacions en la mateixa consulta:

```csharp
// Múltiples includes al mateix nivell
var curs = ctx.Curs
    .Include(c => c.Professor)
    .Include(c => c.Moduls)
    .FirstOrDefault(c => c.CursID == 1);
```

### ThenInclude - Navegació en Profunditat

Per carregar relacions niades, utilitza `ThenInclude()`:

```csharp
// Carregar Curs → Moduls → Professors (tres nivells)
var curs = ctx.Curs
    .Include(c => c.Moduls)
        .ThenInclude(m => m.Professors)
    .FirstOrDefault(c => c.CursID == 1);

// Ara podem accedir sense més consultes
foreach (var modul in curs.Moduls)
{
    Console.WriteLine($"Mòdul: {modul.nom}");
    foreach (var prof in modul.Professors)
    {
        Console.WriteLine($"  Professor: {prof.nom}");
    }
}
```

### Include amb Col·leccions Múltiples

```csharp
// Dos camins diferents des de Curs
var curs = ctx.Curs
    .Include(c => c.Moduls)
        .ThenInclude(m => m.Professors)
    .Include(c => c.Moduls)
        .ThenInclude(m => m.Prerequisits)
    .FirstOrDefault();
```

### Include amb Filtres (EF Core 5.0+)

Des d'EF Core 5.0, pots filtrar dins d'un Include:

```csharp
// Només carregar mòduls amb més de 3 hores
var professor = ctx.Professor
    .Include(p => p.Moduls.Where(m => m.horesSetmana > 3))
    .FirstOrDefault();

// Només els 3 primers mòduls ordenats per nom
var professor = ctx.Professor
    .Include(p => p.Moduls.OrderBy(m => m.nom).Take(3))
    .FirstOrDefault();
```

### Avantatges d'Eager Loading

1. **Rendiment predictible**: Saps exactament quantes consultes es fan
2. **Funciona amb context tancat**: Pots retornar objectes fora del `using`
3. **Evita el problema N+1**: Una sola consulta (o poques) per tot
4. **No requereix configuració extra**: Funciona "out of the box"
5. **Més fàcil de fer testing**: El comportament és explícit

```csharp
Professor professor;
using (var ctx = new ProvaContext())
{
    // Carreguem tot el que necessitem
    professor = ctx.Professor
        .Include(p => p.Moduls)
        .FirstOrDefault();
}  // Context tancat

// Funciona perquè tot està carregat
Console.WriteLine($"Mòduls: {professor.Moduls.Count}");  // ✓ Funciona!
```

### Inconvenients d'Eager Loading

1. **Menys flexible**: Cal saber d'antuvi què necessites
2. **Pot carregar massa dades**: Si no totes les propietats es necessiten
3. **Cartesian explosion**: Múltiples Includes poden generar resultats duplicats

## 3. AsSplitQuery vs AsSingleQuery

### El Problema: Cartesian Explosion

Quan fas múltiples Includes de col·leccions, EF Core genera un JOIN que pot crear **cartesian explosion**:

```csharp
// Això pot generar moltes files duplicades
var curs = ctx.Curs
    .Include(c => c.Moduls)          // 10 mòduls
    .Include(c => c.AltresRelacio)   // 5 elements
    .FirstOrDefault();
// Resultat: 10 × 5 = 50 files retornades (amb duplicats)
```

### Solució: AsSplitQuery

`AsSplitQuery()` divideix la consulta en múltiples SELECTs separats:

```csharp
var curs = ctx.Curs
    .Include(c => c.Moduls)
    .Include(c => c.Professor)
    .AsSplitQuery()  // Divideix en múltiples queries
    .FirstOrDefault();
```

**SQL Generat:**
```sql
-- Query 1: Curs principal
SELECT [c].[CursID], [c].[Any], [c].[Cicle] ...
FROM [Cursos] AS [c]

-- Query 2: Moduls
SELECT [m].[ModulID], [m].[nom], [m].[CursID] ...
FROM [Moduls] AS [m]
WHERE [m].[CursID] = @curs_id

-- Query 3: Professor
SELECT [p].[ProfessorID], [p].[nom] ...
FROM [Professors] AS [p]
WHERE [p].[CursID] = @curs_id
```

### Quan Utilitzar Cada Un?

| Situació | Recomanació |
|----------|-------------|
| Un sol Include | `AsSingleQuery` (per defecte) |
| Múltiples Includes de col·leccions | `AsSplitQuery` |
| Includes de referències (1-1) | `AsSingleQuery` |
| Dades molt relacionades | `AsSingleQuery` |
| Moltes dades no relacionades | `AsSplitQuery` |

### Configurar Globalment

Pots fer que tots els Includes facin split per defecte:

```csharp
protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    optionsBuilder
        .UseSqlServer(connectionString)
        .UseQuerySplittingBehavior(QuerySplittingBehavior.SplitQuery);
}
```

## 4. Lazy Loading amb EF Core

### Configuració Necessària

**Important:** El Lazy Loading NO està activat per defecte en EF Core. Cal:

1. **Instal·lar el paquet NuGet:**
```xml
<PackageReference Include="Microsoft.EntityFrameworkCore.Proxies" Version="8.0.22" />
```

2. **Activar-lo al Context:**
```csharp
protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    optionsBuilder
        .UseSqlServer(connectionString)
        .UseLazyLoadingProxies();  // ← IMPORTANT!
}
```

3. **Marcar propietats com `virtual`:**
```csharp
public class Professor
{
    public int ProfessorID { get; set; }
    public string nom { get; set; }
    
    // VIRTUAL és necessari per Lazy Loading
    public virtual ICollection<Modul> Moduls { get; set; }
    public virtual Curs Curs { get; set; }
    
    public Professor()
    {
        Moduls = new HashSet<Modul>();
    }
}
```

### Com Funciona el Lazy Loading?

Quan una propietat és `virtual` i el Lazy Loading està activat, EF Core crea un **proxy dinàmic** (classe derivada) que intercepta l'accés:

```csharp
using (var ctx = new ProvaContext())
{
    // Carreguem només el Professor
    var professor = ctx.Professor.Find(1);
    
    // Quan accedim a Moduls, EF Core automàticament:
    // 1. Detecta que la col·lecció no està carregada
    // 2. Fa una consulta SELECT a la BD
    // 3. Omple la col·lecció amb els resultats
    
    Console.WriteLine($"Té {professor.Moduls.Count()} mòduls");  // ✓ Query automàtic aquí
}
```

### Avantatges del Lazy Loading

1. **Còmode i intuïtiu**: No cal pensar en què carregar
2. **Estalvia memòria inicial**: Només carrega el que necessites
3. **Codi més net**: Menys Include() explícits
4. **Útil per navegació exploratòria**: Quan no saps què necessitaràs

```csharp
// Sense Lazy Loading necessitaries:
var prof = ctx.Professor.Include(p => p.Moduls).Find(1);

// Amb Lazy Loading pots fer:
var prof = ctx.Professor.Find(1);
// I automàticament carrega Moduls quan hi accedim
```

### Inconvenients del Lazy Loading

#### 1. Problema N+1

**El problema més greu del Lazy Loading:**

```csharp
// ❌ PROBLEMA: Això genera 1 + N consultes!
var professors = ctx.Professor.ToList();  // 1 consulta

foreach (var p in professors)  // Si hi ha 100 professors...
{
    // Cada iteració fa una consulta extra! (100 consultes més)
    Console.WriteLine($"{p.nom}: {p.Moduls.Count()} mòduls");
}
// Total: 101 consultes a la base de dades!
```

**Solució: Usar Eager Loading**

```csharp
// ✓ SOLUCIÓ: Include abans del bucle
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();  // 1 o 2 consultes (depenent d'AsSplitQuery)

foreach (var p in professors)
{
    Console.WriteLine($"{p.nom}: {p.Moduls.Count()} mòduls");
}
```

#### 2. Requereix Context Obert

```csharp
// ❌ PROBLEMA: Context tancat
Professor professor;
using (var ctx = new ProvaContext())
{
    professor = ctx.Professor.Find(1);
}  // Context es tanca aquí!

// ✗ EXCEPCIÓ: "Cannot access a disposed context"
Console.WriteLine(professor.Moduls.Count());
```

**Solució: Include abans de tancar**

```csharp
// ✓ SOLUCIÓ
Professor professor;
using (var ctx = new ProvaContext())
{
    professor = ctx.Professor
        .Include(p => p.Moduls)
        .FirstOrDefault(p => p.ProfessorID == 1);
}

// Funciona: dades ja carregades
Console.WriteLine(professor.Moduls.Count());
```

#### 3. Menys Predictible

És difícil saber quantes consultes es faran:

```csharp
var professor = ctx.Professor.Find(1);

// Quantes consultes fa això?
foreach (var modul in professor.Moduls)
{
    foreach (var prereq in modul.Prerequisits)
    {
        Console.WriteLine(prereq.nom);
    }
}
// Resposta: Depèn! Podrien ser dotzenes de consultes.
```

#### 4. Problemes amb Serialització

```csharp
// ❌ PROBLEMA: JSON infinit amb relacions circulars
var professor = ctx.Professor.Find(1);
var json = JsonSerializer.Serialize(professor);
// Professor → Moduls → Professors → Moduls → ... (cicle infinit!)
```

### Lazy Loading amb ILazyLoader (Alternativa sense virtual)

EF Core ofereix una alternativa sense `virtual` utilitzant `ILazyLoader`:

```csharp
public class Professor
{
    private ILazyLoader _lazyLoader;
    
    public Professor()
    {
    }
    
    private Professor(ILazyLoader lazyLoader)
    {
        _lazyLoader = lazyLoader;
    }
    
    public int ProfessorID { get; set; }
    public string nom { get; set; }
    
    private ICollection<Modul> _moduls;
    public ICollection<Modul> Moduls
    {
        get => _lazyLoader.Load(this, ref _moduls);
        set => _moduls = value;
    }
}
```

**Avantatges:**
- No requereix `virtual`
- Més control sobre la càrrega
- Funciona millor amb alguns frameworks

**Inconvenients:**
- Molt més verbós
- Més difícil de mantenir

## 5. Explicit Loading

### Càrrega Manual de Relacions

Explicit Loading permet carregar relacions manualment després d'haver carregat l'entitat principal:

```csharp
using (var ctx = new ProvaContext())
{
    // Carreguem només el Professor
    var professor = ctx.Professor.Find(1);
    
    // Després decidim carregar els Mòduls
    ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Load();
    
    // Ara podem accedir a Moduls
    Console.WriteLine($"Té {professor.Moduls.Count()} mòduls");
}
```

### Collection vs Reference

```csharp
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor.Find(1);
    
    // Per col·leccions (1-N, N-N)
    ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Load();
    
    // Per referències (1-1, N-1)
    ctx.Entry(professor)
        .Reference(p => p.Curs)
        .Load();
}
```

### Query sobre Relacions

Pots fer consultes sobre les relacions sense carregar-les completament:

```csharp
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor.Find(1);
    
    // Comptar sense carregar tots els mòduls
    int numModuls = ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Query()
        .Count();
    
    // Filtrar la càrrega
    var modulsDificils = ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Query()
        .Where(m => m.horesSetmana > 5)
        .ToList();
    
    // Comprovar si hi ha algun mòdul amb una condició
    bool teModulsLlargs = ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Query()
        .Any(m => m.horesSetmana > 7);
}
```

### IsLoaded - Comprovar si està Carregat

```csharp
var professor = ctx.Professor.Find(1);

// Comprovar si ja està carregat
bool carregat = ctx.Entry(professor)
    .Collection(p => p.Moduls)
    .IsLoaded;

if (!carregat)
{
    ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Load();
}
```

### Avantatges d'Explicit Loading

1. **Control precís**: Decideixeseixes exactament què i quan carregar
2. **Consultes filtrades**: Pots carregar només part de la relació
3. **Estalvi de memòria**: Només carregues el que necessites
4. **Útil per lògica condicional**: Carregar segons condicions de negoci

```csharp
public void MostrarProfessor(int id, bool mostrarModuls)
{
    using (var ctx = new ProvaContext())
    {
        var professor = ctx.Professor.Find(id);
        Console.WriteLine(professor.nom);
        
        // Només carregar si cal
        if (mostrarModuls)
        {
            ctx.Entry(professor)
                .Collection(p => p.Moduls)
                .Load();
            
            foreach (var m in professor.Moduls)
            {
                Console.WriteLine($"  - {m.nom}");
            }
        }
    }
}
```

### Inconvenients d'Explicit Loading

1. **Més verbós**: Més codi que Lazy o Eager
2. **Fàcil oblidar**: Pots oblidar carregar alguna relació
3. **Context obert**: Requereix que el context estigui obert

## 6. AsNoTracking - Consultes de Només Lectura

### Què és AsNoTracking?

Per defecte, EF Core fa **tracking** de totes les entitats carregades per detectar canvis. Si només vols **llegir** dades (sense modificar-les), pots desactivar el tracking:

```csharp
var professors = ctx.Professor
    .AsNoTracking()
    .Include(p => p.Moduls)
    .ToList();
```

### Avantatges d'AsNoTracking

1. **Més ràpid**: No cal fer tracking dels canvis
2. **Menys memòria**: No guarda l'estat original
3. **Ideal per consultes**: Reports, visualitzacions, APIs de lectura

```csharp
// Per un report amb milers de registres
var resum = ctx.Modul
    .AsNoTracking()
    .Select(m => new {
        m.nom,
        m.horesSetmana,
        NumProfessors = m.Professors.Count()
    })
    .ToList();
```

### Quan Utilitzar AsNoTracking

✓ **Usa AsNoTracking quan:**
- Només llegeixes dades (no modificaràs res)
- Consultes per reports o dashboards
- APIs que només retornen dades
- Consultes amb molts registres
- Projeccions amb Select

```csharp
// API endpoint de només lectura
[HttpGet]
public IActionResult GetProfessors()
{
    var professors = _context.Professor
        .AsNoTracking()
        .Include(p => p.Moduls)
        .ToList();
    
    return Ok(professors);
}
```

### AsNoTrackingWithIdentityResolution

Si necessites que EF Core combini entitats duplicades però sense tracking complet:

```csharp
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .AsNoTrackingWithIdentityResolution()
    .ToList();
```

## 7. Comparació de Totes les Estratègies

### Taula Comparativa

| Característica | Eager Loading | Lazy Loading | Explicit Loading | AsNoTracking |
|----------------|---------------|--------------|------------------|--------------|
| **Configuració** | Cap | Proxies + virtual | Cap | Cap |
| **Sintaxi** | `Include()` | Automàtic | `Entry().Load()` | `AsNoTracking()` |
| **Quan carrega** | Amb la consulta | Al accedir | Manual | Amb la consulta |
| **Context obert** | No requerit | Requerit | Requerit | No requerit |
| **Consultes BD** | 1-2 (controlat) | Múltiples | Controlades | 1-2 |
| **Problema N+1** | No | Sí | No | No |
| **Predictibilitat** | Alta | Baixa | Alta | Alta |
| **Rendiment** | Bo | Variable | Bo | Excel·lent |
| **Modificar dades** | Sí | Sí | Sí | No |
| **Recomanat per** | General | Dev ràpid | Control precís | Només lectura |

### Exemple Complet Comparat

```csharp
// ============================================================
// EAGER LOADING (Recomanat per defecte)
// ============================================================
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor
        .Include(p => p.Moduls)
        .FirstOrDefault(p => p.ProfessorID == 1);
    
    Console.WriteLine($"Mòduls: {professor.Moduls.Count}");
}
// SQL: 1 o 2 consultes (depenent de split)

// ============================================================
// LAZY LOADING (Requereix configuració)
// ============================================================
// Al model: public virtual ICollection<Modul> Moduls { get; set; }
// Al context: .UseLazyLoadingProxies()
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor.Find(1);
    
    // Query automàtic aquí quan accedim
    Console.WriteLine($"Mòduls: {professor.Moduls.Count}");
}
// SQL: 2 consultes (1 per Professor, 1 per Moduls)

// ============================================================
// EXPLICIT LOADING
// ============================================================
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor.Find(1);
    
    // Càrrega manual
    ctx.Entry(professor)
        .Collection(p => p.Moduls)
        .Load();
    
    Console.WriteLine($"Mòduls: {professor.Moduls.Count}");
}
// SQL: 2 consultes controlades

// ============================================================
// AS NO TRACKING (Només lectura)
// ============================================================
using (var ctx = new ProvaContext())
{
    var professor = ctx.Professor
        .AsNoTracking()
        .Include(p => p.Moduls)
        .FirstOrDefault(p => p.ProfessorID == 1);
    
    Console.WriteLine($"Mòduls: {professor.Moduls.Count}");
    // No pots fer SaveChanges() amb aquestes entitats
}
// SQL: 1 consulta, sense tracking overhead
```

## 8. Bones Pràctiques i Recomanacions

### Estratègia Recomanada per Defecte

**En EF Core, l'estratègia recomanada és Eager Loading amb Include:**

```csharp
// ✓ RECOMANAT: Eager Loading amb Include
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();
```

**Per què?**
1. Més predictible i controlable
2. No requereix configuració extra
3. Evita el problema N+1
4. Funciona amb context tancat
5. Més fàcil de fer testing

### Quan Usar Cada Estratègia

#### Eager Loading (Include) - Usa per defecte

✓ **Usa Eager Loading quan:**
- Treballes amb col·leccions (bucles)
- Saps que necessitaràs les relacions
- Vols retornar dades fora del context
- Necessites rendiment predictible
- En producció i APIs

```csharp
// Exemple: API endpoint
public List<ProfessorDTO> GetProfessorsAmbModuls()
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Professor
            .Include(p => p.Moduls)
            .Select(p => new ProfessorDTO
            {
                Nom = p.nom,
                NumModuls = p.Moduls.Count()
            })
            .ToList();
    }
}
```

#### Lazy Loading - Usa amb precaució

✓ **Usa Lazy Loading quan:**
- Desenvolupament ràpid / prototipatge
- Navegació exploratòria d'entitats individuals
- No sempre necessites les relacions
- Context sempre obert

❌ **NO usis Lazy Loading quan:**
- Iteres sobre col·leccions
- En APIs de producció
- Quan el rendiment és crític
- Amb serialització JSON

```csharp
// ✓ ÚS ACCEPTABLE: Entitat individual
public void MostrarDetallsProfessor(int id)
{
    using (var ctx = new ProvaContext())
    {
        var prof = ctx.Professor.Find(id);
        Console.WriteLine($"{prof.nom}");
        
        // Lazy loading aquí està bé (un sol professor)
        if (prof.Moduls.Any())
        {
            Console.WriteLine($"Té {prof.Moduls.Count()} mòduls");
        }
    }
}

// ❌ ÚS INCORRECTE: Col·leccions
public void LlistarTots()
{
    using (var ctx = new ProvaContext())
    {
        var professors = ctx.Professor.ToList();
        
        // ❌ N+1 problem!
        foreach (var p in professors)
        {
            Console.WriteLine($"{p.nom}: {p.Moduls.Count()}");
        }
    }
}
```

#### Explicit Loading - Control màxim

✓ **Usa Explicit Loading quan:**
- Vols control precís sobre què i quan carregar
- Les relacions són opcionals segons lògica de negoci
- Vols fer consultes filtrades sobre relacions
- Necessites carregar només part de la relació

```csharp
// Exemple: Carregar només el necessari
public void ProcessarProfessor(int id, ProcessOptions options)
{
    using (var ctx = new ProvaContext())
    {
        var professor = ctx.Professor.Find(id);
        
        if (options.IncloureModuls)
        {
            ctx.Entry(professor)
                .Collection(p => p.Moduls)
                .Query()
                .Where(m => m.horesSetmana > options.MinHores)
                .Load();
        }
        
        if (options.IncloureCurs)
        {
            ctx.Entry(professor)
                .Reference(p => p.Curs)
                .Load();
        }
    }
}
```

#### AsNoTracking - Només lectura

✓ **Usa SEMPRE AsNoTracking quan:**
- Consultes de només lectura
- Reports i dashboards
- APIs que només retornen dades
- Consultes amb molts registres
- No necessites modificar les dades

```csharp
// ✓ PERFECTE per reports
public List<ModulResum> GetResumModuls()
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Modul
            .AsNoTracking()
            .Select(m => new ModulResum
            {
                Nom = m.nom,
                Hores = m.horesSetmana,
                NumProfessors = m.Professors.Count()
            })
            .ToList();
    }
}
```

### Checklist de Decisions

```
1. És una consulta de només lectura?
   └─ SÍ → AsNoTracking + Include
   └─ NO → Continua

2. Fas un bucle sobre entitats?
   └─ SÍ → Include (Eager Loading)
   └─ NO → Continua

3. Retornes dades fora del context?
   └─ SÍ → Include (Eager Loading)
   └─ NO → Continua

4. Necessites control precís sobre què carregar?
   └─ SÍ → Explicit Loading
   └─ NO → Continua

5. És una navegació exploratòria d'una sola entitat?
   └─ SÍ → Lazy Loading (si està configurat)
   └─ NO → Include (per defecte)
```

## 9. Errors Comuns i Solucions

### Error 1: N+1 amb Lazy Loading

```csharp
// ❌ PROBLEMA: Genera 101 consultes
var professors = ctx.Professor.ToList();  // 1 query
foreach (var p in professors)  // 100 iteracions
{
    Console.WriteLine(p.Moduls.Count());  // 100 queries més!
}
```

**Solució:**

```csharp
// ✓ SOLUCIÓ: Include abans del bucle
var professors = ctx.Professor
    .Include(p => p.Moduls)
    .ToList();  // 1-2 queries total

foreach (var p in professors)
{
    Console.WriteLine(p.Moduls.Count());  // No genera queries
}
```

### Error 2: Context Tancat

```csharp
// ❌ PROBLEMA: Context tancat amb Lazy Loading
Professor GetProfessor(int id)
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Professor.Find(id);
    }  // Context es tanca
}

var prof = GetProfessor(1);
Console.WriteLine(prof.Moduls.Count());  // ✗ Excepció!
```

**Solució:**

```csharp
// ✓ SOLUCIÓ: Include abans de tancar
Professor GetProfessor(int id)
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Professor
            .Include(p => p.Moduls)
            .FirstOrDefault(p => p.ProfessorID == id);
    }
}

var prof = GetProfessor(1);
Console.WriteLine(prof.Moduls.Count());  // ✓ Funciona
```

### Error 3: Include Innecessari

```csharp
// ❌ PROBLEMA: Carrega dades que no usa
var professor = ctx.Professor
    .Include(p => p.Moduls)  // Carrega mòduls
    .FirstOrDefault();

Console.WriteLine(professor.nom);  // No usa Moduls!
```

**Solució:**

```csharp
// ✓ SOLUCIÓ: No incloure si no es necessita
var professor = ctx.Professor.FirstOrDefault();
Console.WriteLine(professor.nom);

// O projectar només el que cal
var nom = ctx.Professor
    .Where(p => p.ProfessorID == 1)
    .Select(p => p.nom)
    .FirstOrDefault();
```

### Error 4: Cartesian Explosion

```csharp
// ❌ PROBLEMA: Múltiples includes generen producte cartesià
var curs = ctx.Curs
    .Include(c => c.Moduls)         // 20 mòduls
    .Include(c => c.AltresRelacio)  // 10 elements
    .FirstOrDefault();
// Resultat: 200 files amb duplicats
```

**Solució:**

```csharp
// ✓ SOLUCIÓ: AsSplitQuery
var curs = ctx.Curs
    .Include(c => c.Moduls)
    .Include(c => c.AltresRelacio)
    .AsSplitQuery()  // Divideix en queries separades
    .FirstOrDefault();
```

### Error 5: Oblidar AsNoTracking en Consultes de Lectura

```csharp
// ❌ SUBÒPTIM: Tracking innecessari
var resum = ctx.Modul
    .Include(m => m.Professors)
    .ToList();  // Fa tracking de tot
```

**Solució:**

```csharp
// ✓ MILLOR: AsNoTracking per només lectura
var resum = ctx.Modul
    .AsNoTracking()
    .Include(m => m.Professors)
    .ToList();

// O millor encara: projectar
var resum = ctx.Modul
    .Select(m => new {
        m.nom,
        NumProfessors = m.Professors.Count()
    })
    .ToList();
```

### Error 6: Lazy Loading sense Configuració

```csharp
// ❌ PROBLEMA: Virtual sense UseLazyLoadingProxies
public class Professor
{
    public virtual ICollection<Modul> Moduls { get; set; }
}

// Al context NO hi ha .UseLazyLoadingProxies()
var prof = ctx.Professor.Find(1);
Console.WriteLine(prof.Moduls.Count());  // Col·lecció buida!
```

**Solució:**

```csharp
// ✓ SOLUCIÓ 1: Activar Lazy Loading
protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    optionsBuilder
        .UseSqlServer(connectionString)
        .UseLazyLoadingProxies();  // ← Afegir això
}

// ✓ SOLUCIÓ 2: Usar Include
var prof = ctx.Professor
    .Include(p => p.Moduls)
    .FirstOrDefault(p => p.ProfessorID == 1);
```

## 10. Optimització i Monitorització

### Veure les SQL Generades

```csharp
// Configurar logging al OnConfiguring
protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    optionsBuilder
        .UseSqlServer(connectionString)
        .LogTo(Console.WriteLine, Microsoft.Extensions.Logging.LogLevel.Information)
        .EnableSensitiveDataLogging();  // Mostra valors de paràmetres
}

// O per una consulta específica
var query = ctx.Professor.Include(p => p.Moduls);
var sql = query.ToQueryString();
Console.WriteLine(sql);
```

### Comptar Consultes

```csharp
int queryCount = 0;

protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    optionsBuilder
        .UseSqlServer(connectionString)
        .LogTo(message => {
            if (message.Contains("Executed DbCommand"))
            {
                queryCount++;
                Console.WriteLine($"Query #{queryCount}: {message}");
            }
        });
}
```

### Profiling amb EF Core

```csharp
// Usar un profiler com MiniProfiler
services.AddMiniProfiler(options => {
    options.RouteBasePath = "/profiler";
}).AddEntityFramework();

// O usar el built-in diagnostic source
var listener = new DiagnosticListener("EFCore");
listener.Subscribe(new EFCoreEventListener());
```

## 11. Resum i Recomanacions Finals

### Estratègia Recomanada per EF Core

1. **Per defecte: Eager Loading amb Include**
   - Predictible i eficient
   - Funciona amb context tancat
   - Evita N+1

2. **Per consultes de lectura: AsNoTracking + Include**
   - Màxim rendiment
   - Mínim overhead

3. **Per control precís: Explicit Loading**
   - Càrrega condicional
   - Filtres sobre relacions

4. **Lazy Loading: Només desenvolupament**
   - Prototipatge ràpid
   - Navegació exploratòria
   - **MAI en producció sense cura**

### Patró Recomanat

```csharp
// ✓ PATRÓ RECOMANAT per APIs
public async Task<List<ProfessorDTO>> GetProfessorsAsync()
{
    return await _context.Professor
        .AsNoTracking()                    // Només lectura
        .Include(p => p.Moduls)           // Carregar relacions
        .AsSplitQuery()                   // Si múltiples includes
        .Select(p => new ProfessorDTO     // Projectar a DTO
        {
            Nom = p.nom,
            Cognom = p.cognom,
            Moduls = p.Moduls.Select(m => new ModulDTO
            {
                Nom = m.nom,
                Hores = m.horesSetmana
            }).ToList()
        })
        .ToListAsync();
}
```

### Checklist Final

- [ ] Usar Include per defecte en lloc de Lazy Loading
- [ ] AsNoTracking per totes les consultes de només lectura
- [ ] AsSplitQuery quan fas múltiples Includes de col·leccions
- [ ] Projectar amb Select quan només necessites algunes propietats
- [ ] Evitar Lazy Loading en bucles (problema N+1)
- [ ] Include abans de tancar el context si retornes entitats
- [ ] Monitoritzar les SQL generades durant el desenvolupament
- [ ] Considerar Explicit Loading per càrrega condicional

**La clau:** En EF Core, **Eager Loading és el rei**. Lazy Loading és còmode però perillós. Usa Include per defecte i AsNoTracking quan només llegeixes.