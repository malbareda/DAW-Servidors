# Guia de Relacions Recursives amb Entity Framework Core

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
    [DatabaseGenerated(DatabaseGeneratedOption.None)]  // No autonumèric
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

**Important:** Són la **mateixa relació** vista des de diferents perspectives. EF Core manté la sincronització automàticament.

## 3. Configuració amb Fluent API (OBLIGATORI)

### Configuració al Context

**Important:** En EF Core, les relacions recursives N-N **sempre** s'han de configurar amb Fluent API. No es poden inferir automàticament.

```csharp
protected override void OnModelCreating(ModelBuilder modelBuilder)
{
    modelBuilder.Entity<Modul>()
        .HasMany(m => m.Prerequisits)           // Un mòdul TÉ MOLTS prerequisits
        .WithMany(m => m.EsPrerequisitDe)       // La navegació inversa
        .UsingEntity<Dictionary<string, object>>(
            "ModulPrerrequisits",               // Nom de la taula intermèdia
            // Configura PrerequisitID (costat dret)
            r => r
                .HasOne<Modul>()
                .WithMany()
                .HasForeignKey("PrerequisitID")
                .OnDelete(DeleteBehavior.NoAction),
            // Configura ModulID (costat esquerre)
            l => l
                .HasOne<Modul>()
                .WithMany()
                .HasForeignKey("ModulID")
                .OnDelete(DeleteBehavior.NoAction),
            // Configura la taula intermèdia. Aquesta en concret no és necessària i si no la poses ho infereix
            j =>
            {
                j.HasKey("ModulID", "PrerequisitID");  //clau primaria de la taula, en aquest cas, una clau composta
                j.ToTable("ModulPrerrequisits");  //nom de la taula
            }
        );
        
    base.OnModelCreating(modelBuilder);
}
```


### Estructura de la Taula Intermèdia

EF Core crea automàticament aquesta taula:

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
- `DeleteBehavior.NoAction` evita cicles en cascada

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

### Per què NoAction en Delete?

```csharp
.OnDelete(DeleteBehavior.NoAction)
```

En relacions recursives, el delete en cascada pot crear **cicles infinits**:
- Si esborres M3 → esborra M6 (perquè és prerequisit)
- Si esborres M6 → esborra M3 (si M6 fos prerequisit de M3)
- **Cicle infinit!**

`DeleteBehavior.NoAction` evita aquest problema.

## 4. Creació de Relacions Recursives

### Inserció de Dades

```csharp
using (var ctx = new ProvaContext())
{
    // Crear els mòduls
    var m6 = new Modul() { Codi = 6, nom = "Accés a Dades", horesSetmana = 4 };
    var m3 = new Modul() { Codi = 3, nom = "Programació", horesSetmana = 7 };
    var m2 = new Modul() { Codi = 2, nom = "Bases de Dades", horesSetmana = 4 };
    var m7 = new Modul() { Codi = 7, nom = "Interfícies", horesSetmana = 4 };

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
}
```

### Sincronització Automàtica

Quan fem `m6.Prerequisits.Add(m3)`, EF Core automàticament:
1. Afegeix M3 a la col·lecció `M6.Prerequisits`
2. Afegeix M6 a la col·lecció `M3.EsPrerequisitDe`
3. Crea un registre a `ModulPrerrequisits` (6, 3)

**No cal fer res manualment per mantenir la sincronització!**

## 5. Navegació de Relacions Recursives

### Amb Lazy Loading (Recomanat per Desenvolupament)

```csharp
using (var ctx = new ProvaContext())
{
    // Obtenir un mòdul
    Modul m6 = ctx.Modul.Where(m => m.nom == "Accés a Dades").First();

    // Lazy Loading carrega automàticament els prerequisits
    Console.WriteLine("ELS PREREQUISITS D'ACCÉS A DADES SÓN:");
    foreach(Modul preq in m6.Prerequisits)
    {
        Console.WriteLine($"{preq.Codi}: {preq.nom}");
    }
    
    // Sortida:
    // 3: Programació
    // 2: Bases de Dades
}
```

### Navegació Inversa

```csharp
using (var ctx = new ProvaContext())
{
    // Obtenir un mòdul
    Modul m3 = ctx.Modul.Where(m => m.nom == "Programació").First();

    // Veure de quins mòduls és prerequisit
    Console.WriteLine("PROGRAMACIÓ ÉS PREREQUISIT DE:");
    foreach (Modul dep in m3.EsPrerequisitDe)
    {
        Console.WriteLine($"{dep.Codi}: {dep.nom}");
    }
    
    // Sortida:
    // 6: Accés a Dades
    // 7: Interfícies
}
```

### Amb Eager Loading (Include)

```csharp
using (var ctx = new ProvaContext())
{
    // Carregar mòdul amb els seus prerequisits
    var m6 = ctx.Modul
        .Include(m => m.Prerequisits)
        .Where(m => m.Codi == 6)
        .First();

    // Els prerequisits ja estan carregats
    foreach(var preq in m6.Prerequisits)
    {
        Console.WriteLine(preq.nom);
    }
}
```

### Include de Múltiples Nivells

```csharp
using (var ctx = new ProvaContext())
{
    // Carregar mòdul, prerequisits, i prerequisits dels prerequisits
    var m6 = ctx.Modul
        .Include(m => m.Prerequisits)
            .ThenInclude(p => p.Prerequisits)
        .Where(m => m.Codi == 6)
        .First();

    // Ara podem navegar dos nivells
    foreach(var preq in m6.Prerequisits)
    {
        Console.WriteLine(preq.nom);
        foreach(var preqPreq in preq.Prerequisits)
        {
            Console.WriteLine($"  - {preqPreq.nom}");
        }
    }
}
```

### Include Bidireccional

```csharp
using (var ctx = new ProvaContext())
{
    // Carregar ambdós costats de la relació
    var m3 = ctx.Modul
        .Include(m => m.Prerequisits)
        .Include(m => m.EsPrerequisitDe)
        .Where(m => m.Codi == 3)
        .First();

    // Ara podem navegar en ambdues direccions
    Console.WriteLine("M3 necessita:");
    foreach(var p in m3.Prerequisits) 
    { 
        Console.WriteLine($"  {p.nom}");
    }

    Console.WriteLine("M3 és necessari per:");
    foreach(var d in m3.EsPrerequisitDe) 
    { 
        Console.WriteLine($"  {d.nom}");
    }
}
```


## 7. Casos d'Ús Pràctics

### 1. Validar si es pot Cursar un Mòdul

```csharp
public bool PotCursarModul(int modulID, List<int> modulsFets)
{
    using (var ctx = new ProvaContext())
    {
        var modul = ctx.Modul
            .Include(m => m.Prerequisits)
            .FirstOrDefault(m => m.Codi == modulID);
        
        if (modul == null) return false;
        
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



## 9. Errors Comuns i Solucions

### Error 1: Cicle Infinit en Lazy Loading

```csharp
//  PROBLEMA: Cicle infinit si hi ha dependencies circulars
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
// Usar HashSet per tracking
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

// 
MostrarPrerequisitsRecursiu(m6, new HashSet<int>());
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
// Comentaris clars
public virtual ICollection<Modul> Prerequisits { get; set; }         // Aquest necessita...
public virtual ICollection<Modul> EsPrerequisitDe { get; set; }      // ...és necessari per
```



### 5. Noms Descriptius per les Propietats

```csharp
//  BO: Noms que indiquen la direcció
public virtual ICollection<Modul> Prerequisits { get; set; }
public virtual ICollection<Modul> EsPrerequisitDe { get; set; }

// DOLENT: Noms ambigus
public virtual ICollection<Modul> Moduls1 { get; set; }
public virtual ICollection<Modul> Moduls2 { get; set; }
```



## 8. Resum

Les relacions recursives N-N en EF Core són potents però requereixen atenció especial:

| Aspecte | Recomanació |
|---------|-------------|
| **Definició** | Dues propietats ICollection<T> de la mateixa entitat |
| **Fluent API** | **OBLIGATORI** amb `.UsingEntity()` per configurar la taula intermèdia |
| **Virtual** | Recomanat per flexibilitat, però usar Include en producció |
| **Sincronització** | EF Core manté automàticament ambdós costats de la relació |
| **Delete Behavior** | Usar `DeleteBehavior.NoAction` per evitar cicles |
| **Cicles** | Validar en lògica de negoci abans d'afegir relacions |
| **Rendiment** | Usar `Include()` per evitar N+1, considerar `AsNoTracking()` |
| **Navegació** | Utilitzar les dues propietats segons necessitat |
| **Testing** | Include explícit facilita el testing |

La clau és entendre que les dues propietats representen **la mateixa relació** des de perspectives diferents, i que la configuració amb Fluent API és **obligatòria** per a relacions recursives en EF Core.