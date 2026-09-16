# Guia de Relacions Recursives amb Entity Framework

## 1. Què és una Relació Recursiva?

Una relació recursiva és quan una entitat es relaciona amb si mateixa. És a dir, una taula té una clau forana que apunta a la seva pròpia clau primària.

### Exemple del Món Real: Prerequisites de Mòduls

En el nostre cas, tenim mòduls formatius on:
- **M6 (Accés a Dades)** necessita haver fet abans **M3 (Programació)** i **M2 (Bases de Dades)**
- **M7 (Interfícies)** necessita haver fet abans **M3 (Programació)**

Aquesta és una relació **Molts a Molts (N-N) recursiva** perquè:
- Un mòdul pot tenir **molts** prerequisits
- Un mòdul pot ser prerequisit de **molts** altres mòduls
- Tots els elements són de la mateixa taula (Modul)

## 2. Estructura de les Classes

### Definició del Model

```csharp
public class Modul
{
    [Key]
    public int Codi { get; set; }
    
    [Required]
    public string nom { get; set; }
    
    [Column("hores_setmana")]
    public int horesSetmana { get; set; }

    // RELACIÓ RECURSIVA N-N: Dos costats de la mateixa relació
    
    // Prerequisits: Mòduls que cal fer abans d'aquest
    public virtual ICollection<Modul> Prerequisits { get; set; }
    
    // EsPrerequisitDe: Mòduls que tenen aquest com a prerequisit
    public virtual ICollection<Modul> EsPrerequisitDe { get; set; }

    public Modul()
    {
        this.Prerequisits = new HashSet<Modul>();
        this.EsPrerequisitDe = new HashSet<Modul>();
    }
}
```

### Per què Dues Propietats?

Les dues propietats representen els **dos costats de la mateixa relació**:

```csharp
// COSTAT ACTIU: "Aquest mòdul necessita..."
public virtual ICollection<Modul> Prerequisits { get; set; }

// COSTAT PASSIU: "Aquest mòdul és necessari per..."
public virtual ICollection<Modul> EsPrerequisitDe { get; set; }
```

**Exemple pràctic:**
- `M6.Prerequisits` → [M3, M2] (M6 necessita M3 i M2)
- `M3.EsPrerequisitDe` → [M6, M7] (M3 és necessari per M6 i M7)

**Important:** Són la **mateixa relació** vista des de diferents perspectives. EF manté la sincronització automàticament.

## 3. Configuració amb Fluent API

### Configuració al Context

```csharp
protected override void OnModelCreating(DbModelBuilder modelBuilder)
{
    modelBuilder.Entity<Modul>()
        // HasMany: Un mòdul TÉ MOLTS prerequisits
        .HasMany(m => m.Prerequisits)
        
        // WithMany: Aquests prerequisits SÓN PREREQUISITS DE MOLTS
        .WithMany(m => m.EsPrerequisitDe)
        
        // Map: Configura la taula intermèdia. AIXO HO POSO NOMES PERQUE ES UNA N-N, SI FOS UNA 1-N NO TINDRIA TAULA INTEMEDIA OBVIAMENT
        .Map(m =>
        {
            m.ToTable("ModulPrerrequisits");
            m.MapLeftKey("ModulID");        // Mòdul que necessita prerequisits
            m.MapRightKey("PrerequisitID"); // Mòdul que és prerequisit
        });
        
    base.OnModelCreating(modelBuilder);
}
```

### Estructura de la Taula Intermèdia

EF crea automàticament aquesta taula:

```sql
CREATE TABLE ModulPrerrequisits (
    ModulID INT NOT NULL,         -- FK → Modul.Codi (l'assignatura)
    PrerequisitID INT NOT NULL,   -- FK → Modul.Codi (el prerequisit)
    PRIMARY KEY (ModulID, PrerequisitID),
    FOREIGN KEY (ModulID) REFERENCES Modul(Codi),
    FOREIGN KEY (PrerequisitID) REFERENCES Modul(Codi)
)
```

**Característiques importants:**
- Ambdues claus foranes apunten a la **mateixa taula** (Modul)
- Clau primària composta evita duplicats
- Permet múltiples prerequisits per mòdul

### Exemple de Dades

Si tenim aquesta configuració:
- M6 necessita M3 i M2
- M7 necessita M3

La taula `ModulPrerrequisits` conté:

| ModulID | PrerequisitID | Significat                           |
|---------|---------------|--------------------------------------|
| 6       | 3             | M6 (Accés a Dades) necessita M3      |
| 6       | 2             | M6 (Accés a Dades) necessita M2      |
| 7       | 3             | M7 (Interfícies) necessita M3        |

## 4. Creació de Relacions Recursives

### Inserció de Dades

```csharp
// Crear els mòduls
var m6 = new Modul() { Codi = 6, nom = "Acces a Dades", horesSetmana = 4 };
var m3 = new Modul() { Codi = 3, nom = "Programacio", horesSetmana = 7 };
var m2 = new Modul() { Codi = 2, nom = "Bases de Dades", horesSetmana = 4 };
var m7 = new Modul() { Codi = 7, nom = "Interficies", horesSetmana = 4 };

// Definir prerequisits
m6.Prerequisits.Add(m3);  // M6 necessita M3
m6.Prerequisits.Add(m2);  // M6 necessita M2
m7.Prerequisits.Add(m3);  // M7 necessita M3

// Guardar a la BD
ctx.Modul.Add(m6);
ctx.Modul.Add(m7);
ctx.Modul.Add(m3);
ctx.Modul.Add(m2);
ctx.SaveChanges();
```

### Sincronització Automàtica

Quan fem `m6.Prerequisits.Add(m3)`, EF automàticament:
1. Afegeix M3 a la col·lecció `M6.Prerequisits`
2. Afegeix M6 a la col·lecció `M3.EsPrerequisitDe`
3. Crea un registre a `ModulPrerrequisits` (6, 3)

**No cal fer res manualment per mantenir la sincronització!**

## 5. Navegació de Relacions Recursives

### Navegació Directa: Obtenir Prerequisites

```csharp
// Obtenir un mòdul
Modul m6 = ctx.Modul.Where(m => m.nom == "Acces a Dades").First();

// Accedir als seus prerequisits
Console.WriteLine("ELS PRERREQUISITS D'ACCES A DADES SON:");
foreach(Modul preq in m6.Prerequisits)
{
    Console.WriteLine(preq.Codi + ": " + preq.nom);
}

// Sortida:
// 3: Programacio
// 2: Bases de Dades
```

### Navegació Inversa: Obtenir Dependents

```csharp
// Obtenir un mòdul
Modul m3 = ctx.Modul.Where(m => m.nom == "Programacio").First();

// Veure de quins mòduls és prerequisit
Console.WriteLine("PROGRAMACIO ES PREREQUISIT DE:");
foreach (Modul dep in m3.EsPrerequisitDe)
{
    Console.WriteLine(dep.Codi + ": " + dep.nom);
}

// Sortida:
// 6: Acces a Dades
// 7: Interficies
```

## 6. Virtual vs No Virtual en Recursives

### Amb Virtual (Lazy Loading)

```csharp
public class Modul
{
    // SÍ és VIRTUAL → Lazy Loading activat
    public virtual ICollection<Modul> Prerequisits { get; set; }
    public virtual ICollection<Modul> EsPrerequisitDe { get; set; }
}
```

**Comportament amb Virtual:**

```csharp
using (var ctx = new ProvaContext())
{
    // Carreguem només el mòdul M6
    Modul m6 = ctx.Modul.Find(6);
    
    // LAZY LOADING: Quan accedim a Prerequisits, EF automàticament:
    // 1. Detecta que no està carregat
    // 2. Fa SELECT a ModulPrerrequisits WHERE ModulID = 6
    // 3. Obté [3, 2]
    // 4. Fa SELECT a Modul WHERE Codi IN (3, 2)
    // 5. Omple la col·lecció
    
    foreach(var p in m6.Prerequisits)  // ✓ Funciona!
    {
        Console.WriteLine(p.nom);
    }
}
```

**Avantatges:**
- Més còmode: no cal recordar fer Include()
- Carrega només el que necessites
- Codi més net

**Inconvenients:**
- Problema N+1 en bucles
- Requereix context obert
- Menys predictible

### Sense Virtual (Eager Loading Obligatori)

```csharp
public class Modul
{
    // NO és virtual → Eager Loading obligatori
    public ICollection<Modul> Prerequisits { get; set; }
    public ICollection<Modul> EsPrerequisitDe { get; set; }
}
```

**Comportament sense Virtual:**

```csharp
using (var ctx = new ProvaContext())
{
    // Sense Include, Prerequisits serà NULL
    Modul m6 = ctx.Modul.Find(6);
    
    // ✗ PETA: NullReferenceException
    foreach(var p in m6.Prerequisits)
    {
        Console.WriteLine(p.nom);
    }
}
```

**Solució: Usar Include()**

```csharp
using (var ctx = new ProvaContext())
{
    // Cal fer Include explícitament
    Modul m6 = ctx.Modul
        .Include(m => m.Prerequisits)
        .Where(m => m.Codi == 6)
        .First();
    
    // ✓ Ara funciona
    foreach(var p in m6.Prerequisits)
    {
        Console.WriteLine(p.nom);
    }
}
```

**Avantatges:**
- Més explícit i controlat
- Millor rendiment en bucles
- No depèn del context obert

**Inconvenients:**
- Menys còmode
- Cal recordar sempre fer Include()
- Més codi verbós

### Comparació Pràctica

| Aspecte | Amb Virtual | Sense Virtual |
|---------|-------------|---------------|
| **Sintaxi bàsica** | `var m = ctx.Modul.Find(6);` | `var m = ctx.Modul.Include(x => x.Prerequisits).Find(6);` |
| **Accés a relació** | Automàtic | Cal Include() |
| **Context tancat** | Peta | Funciona si s'ha fet Include() |
| **Bucles** | Problema N+1 | Més eficient amb Include() |
| **Predictibilitat** | Baixa | Alta |
| **Comoditat** | Alta | Baixa |

## 7. Include amb Relacions Recursives

### Include Simple

```csharp
// Carregar mòdul amb els seus prerequisits
var m6 = ctx.Modul
    .Include(m => m.Prerequisits)
    .Where(m => m.Codi == 6)
    .First();
```

### Include de Múltiples Nivells

```csharp
// Carregar mòdul, prerequisits, i prerequisits dels prerequisits
var m6 = ctx.Modul
    .Include(m => m.Prerequisits.Select(p => p.Prerequisits))
    .Where(m => m.Codi == 6)
    .First();

// Ara podem navegar dos nivells:
foreach(var preq in m6.Prerequisits)
{
    Console.WriteLine(preq.nom);
    foreach(var preqPreq in preq.Prerequisits)
    {
        Console.WriteLine("  - " + preqPreq.nom);
    }
}
```

### Include Bidireccional

```csharp
// Carregar ambdós costats de la relació
var m3 = ctx.Modul
    .Include(m => m.Prerequisits)
    .Include(m => m.EsPrerequisitDe)
    .Where(m => m.Codi == 3)
    .First();

// Ara podem navegar en ambdues direccions
Console.WriteLine("M3 necessita:");
foreach(var p in m3.Prerequisits) { /* ... */ }

Console.WriteLine("M3 és necessari per:");
foreach(var d in m3.EsPrerequisitDe) { /* ... */ }
```

## 8. Casos d'Ús Pràctics

### 1. Validar si es pot Cursar un Mòdul

```csharp
public bool PotCursarModul(int modulID, List<int> modulsFets)
{
    using (var ctx = new ProvaContext())
    {
        var modul = ctx.Modul
            .Include(m => m.Prerequisits)
            .Find(modulID);
        
        // Comprovar si tots els prerequisits estan fets
        foreach(var preq in modul.Prerequisits)
        {
            if (!modulsFets.Contains(preq.Codi))
            {
                return false;  // Falta un prerequisit
            }
        }
        
        return true;  // Pot cursar-lo
    }
}

// Exemple d'ús
bool potFerM6 = PotCursarModul(6, new List<int> { 3, 2 });  // true
bool potFerM6_2 = PotCursarModul(6, new List<int> { 3 });    // false (falta M2)
```

### 2. Obtenir Ordre de Cursada

```csharp
public List<int> ObtenirOrdreOptim(List<int> modulsPendents)
{
    using (var ctx = new ProvaContext())
    {
        var ordre = new List<int>();
        var fets = new List<int>();
        
        while (modulsPendents.Count > 0)
        {
            foreach(int modulID in modulsPendents.ToList())
            {
                var modul = ctx.Modul
                    .Include(m => m.Prerequisits)
                    .Find(modulID);
                
                // Si tots els prerequisits estan fets
                if (modul.Prerequisits.All(p => fets.Contains(p.Codi)))
                {
                    ordre.Add(modulID);
                    fets.Add(modulID);
                    modulsPendents.Remove(modulID);
                }
            }
        }
        
        return ordre;
    }
}

// Exemple: Ordre òptim per fer M2, M3, M6, M7
var ordre = ObtenirOrdreOptim(new List<int> { 2, 3, 6, 7 });
// Resultat possible: [2, 3, 6, 7] o [3, 2, 6, 7]
```

### 3. Impacte d'Eliminar un Mòdul

```csharp
public void MostrarImpacteEliminacio(int modulID)
{
    using (var ctx = new ProvaContext())
    {
        var modul = ctx.Modul
            .Include(m => m.EsPrerequisitDe)
            .Find(modulID);
        
        Console.WriteLine($"Si elimines {modul.nom}:");
        
        if (modul.EsPrerequisitDe.Count == 0)
        {
            Console.WriteLine("  Cap mòdul es veu afectat");
        }
        else
        {
            Console.WriteLine("  Afecta a:");
            foreach(var dep in modul.EsPrerequisitDe)
            {
                Console.WriteLine($"    - {dep.nom} (perd un prerequisit)");
            }
        }
    }
}

// Exemple
MostrarImpacteEliminacio(3);
// Sortida:
// Si elimines Programacio:
//   Afecta a:
//     - Acces a Dades (perd un prerequisit)
//     - Interficies (perd un prerequisit)
```

### 4. Graella de Dependencies

```csharp
public void MostrarGrafDependencies()
{
    using (var ctx = new ProvaContext())
    {
        var moduls = ctx.Modul
            .Include(m => m.Prerequisits)
            .Include(m => m.EsPrerequisitDe)
            .ToList();
        
        foreach(var m in moduls)
        {
            Console.WriteLine($"\n{m.nom} (M{m.Codi}):");
            
            Console.WriteLine("  Prerequisits:");
            if (m.Prerequisits.Count == 0)
            {
                Console.WriteLine("    (Cap)");
            }
            else
            {
                foreach(var p in m.Prerequisits)
                {
                    Console.WriteLine($"    ← M{p.Codi}: {p.nom}");
                }
            }
            
            Console.WriteLine("  És prerequisit de:");
            if (m.EsPrerequisitDe.Count == 0)
            {
                Console.WriteLine("    (Cap)");
            }
            else
            {
                foreach(var d in m.EsPrerequisitDe)
                {
                    Console.WriteLine($"    → M{d.Codi}: {d.nom}");
                }
            }
        }
    }
}

// Sortida exemple:
// Programacio (M3):
//   Prerequisits:
//     (Cap)
//   És prerequisit de:
//     → M6: Acces a Dades
//     → M7: Interficies
//
// Acces a Dades (M6):
//   Prerequisits:
//     ← M3: Programacio
//     ← M2: Bases de Dades
//   És prerequisit de:
//     (Cap)
```

## 9. Errors Comuns i Solucions

### Error 1: Cicle Infinit en Lazy Loading

```csharp
// ❌ PROBLEMA: Cicle infinit si hi ha dependencies circulars
var m = ctx.Modul.Find(1);
foreach(var p in m.Prerequisits)
{
    foreach(var pp in p.Prerequisits)  // Si p apunta a m, bucle infinit!
    {
        // ...
    }
}
```

**Solució:** Controlar profunditat o usar HashSet per evitar repeticions

```csharp
// ✓ SOLUCIÓ: Usar HashSet per tracking
public void MostrarPrerequisitsRecursiu(Modul m, HashSet<int> visitats, int nivell = 0)
{
    if (visitats.Contains(m.Codi)) return;  // Ja visitat
    
    visitats.Add(m.Codi);
    Console.WriteLine(new string(' ', nivell * 2) + m.nom);
    
    foreach(var p in m.Prerequisits)
    {
        MostrarPrerequisitsRecursiu(p, visitats, nivell + 1);
    }
}

// Ús
MostrarPrerequisitsRecursiu(m6, new HashSet<int>());
```

### Error 2: Oblidar Include en Producció

```csharp
// ❌ PROBLEMA: Funciona en dev (virtual) però peta en prod (no virtual)
public List<Modul> ObtenirModulsAmbPrerequisits()
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Modul.ToList();  // Prerequisits serà NULL si no és virtual!
    }
}
```

**Solució:** Sempre fer Include, independentment de virtual

```csharp
// ✓ SOLUCIÓ: Include explícit
public List<Modul> ObtenirModulsAmbPrerequisits()
{
    using (var ctx = new ProvaContext())
    {
        return ctx.Modul
            .Include(m => m.Prerequisits)
            .ToList();
    }
}
```

### Error 3: N+1 en Navegació Recursiva

```csharp
// ❌ PROBLEMA: Genera moltes queries
var moduls = ctx.Modul.ToList();
foreach(var m in moduls)
{
    // Cada iteració genera una query per Prerequisits!
    Console.WriteLine($"{m.nom}: {m.Prerequisits.Count} prerequisits");
}
```

**Solució:** Include abans del bucle

```csharp
// ✓ SOLUCIÓ: Una sola query
var moduls = ctx.Modul
    .Include(m => m.Prerequisits)
    .ToList();

foreach(var m in moduls)
{
    Console.WriteLine($"{m.nom}: {m.Prerequisits.Count} prerequisits");
}
```

## 10. Bones Pràctiques

### 1. Usar Virtual per Desenvolupament, Include per Producció

```csharp
// En desenvolupament: virtual per comoditat
public virtual ICollection<Modul> Prerequisits { get; set; }

// En codi: sempre Include per seguretat
var moduls = ctx.Modul
    .Include(m => m.Prerequisits)
    .ToList();
```

### 2. Inicialitzar Sempre les Col·leccions

```csharp
public Modul()
{
    // Fins i tot amb virtual, evita problemes
    this.Prerequisits = new HashSet<Modul>();
    this.EsPrerequisitDe = new HashSet<Modul>();
}
```

### 3. Documentar la Direcció de la Relació

```csharp
// ✓ BO: Comentaris clars
public virtual ICollection<Modul> Prerequisits { get; set; }         // Aquest necessita...
public virtual ICollection<Modul> EsPrerequisitDe { get; set; }      // ...és necessari per
```

### 4. Validar Cicles en Lògica de Negoci

```csharp
public bool CreariaCicle(int modulID, int prerequisitID)
{
    // Comprovar si afegir prerequisit crearia un cicle
    // M6 → M3 → M2 → M6 (cicle!)
    using (var ctx = new ProvaContext())
    {
        var visitats = new HashSet<int>();
        return ComprovarCicleRecursiu(prerequisitID, modulID, visitats, ctx);
    }
}
```

### 5. Noms Descriptius per les Propietats

```csharp
// ✓ BO: Noms que indiquen la direcció
public virtual ICollection<Modul> Prerequisits { get; set; }
public virtual ICollection<Modul> EsPrerequisitDe { get; set; }

// ❌ DOLENT: Noms ambigus
public virtual ICollection<Modul> Moduls1 { get; set; }
public virtual ICollection<Modul> Moduls2 { get; set; }
```

## 11. Resum

Les relacions recursives N-N són potents però cal tenir en compte:

| Aspecte | Recomanació |
|---------|-------------|
| **Definició** | Dues propietats ICollection<T> de la mateixa entitat |
| **Fluent API** | Sempre necessària per configurar la taula intermèdia |
| **Virtual** | Recomanat per flexibilitat, però usar Include en producció |
| **Sincronització** | EF manté automàticament ambdós costats de la relació |
| **Cicles** | Validar en lògica de negoci |
| **Rendiment** | Usar Include() per evitar N+1 |
| **Navegació** | Utilitzar les dues propietats segons necessitat |

La clau és entendre que les dues propietats representen **la mateixa relació** des de perspectives diferents, i que `virtual` només afecta **com** es carreguen les dades, no la seva estructura.
