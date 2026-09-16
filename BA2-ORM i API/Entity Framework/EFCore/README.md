# Entity Framework Core - Conceptes Bàsics

## 1. Instal·lació de Paquets NuGet

Abans de començar a treballar amb Entity Framework Core, necessites instal·lar els paquets NuGet necessaris al teu projecte. Obre el fitxer `.csproj` i afegeix les següents referències:

```xml
<ItemGroup>
  <PackageReference Include="Microsoft.EntityFrameworkCore" Version="8.0.22" />
  <PackageReference Include="Microsoft.EntityFrameworkCore.SqlServer" Version="8.0.22" />
  <PackageReference Include="Microsoft.EntityFrameworkCore.Tools" Version="8.0.22">
    <PrivateAssets>all</PrivateAssets>
    <IncludeAssets>runtime; build; native; contentfiles; analyzers; buildtransitive</IncludeAssets>
  </PackageReference>
  <PackageReference Include="Microsoft.EntityFrameworkCore.Proxies" Version="8.0.22" />
</ItemGroup>
```

**Explicació dels paquets:**
- **Microsoft.EntityFrameworkCore**: El paquet principal d'EF Core amb tota la funcionalitat bàsica
- **Microsoft.EntityFrameworkCore.SqlServer**: Provider específic per a SQL Server
- **Microsoft.EntityFrameworkCore.Tools**: Eines per a migracions i gestió de la base de dades
- **Microsoft.EntityFrameworkCore.Proxies**: Permet el Lazy Loading automàtic (carrega dades relacionades sota demanda)

## 2. Què és Entity Framework Core?

Entity Framework Core (EF Core) és un Object-Relational Mapper (ORM) modern i lleuger desenvolupat per Microsoft. Permet als desenvolupadors treballar amb bases de dades relacionals utilitzant objectes .NET, eliminant la necessitat d'escriure SQL manualment per a la majoria d'operacions.

## 3. Models i Classes POCO

### Què són els models?
Els models són classes C# que representen les taules de la base de dades. En EF Core, utilitzem classes POCO (Plain Old CLR Objects) per definir els nostres models.

Exemple bàsic d'un model:
```csharp
public class Professor
{
    public int ProfessorID { get; set; }
    public string nom { get; set; }
    public string cognom { get; set; }
}
```

### Convencions de Models
Entity Framework Core utilitza convencions per inferir l'estructura de la base de dades:

#### 1. Claus Primàries
Una propietat s'identifica automàticament com a clau primària si es diu:
- "ID" 
- "[NomClasse]ID"

```csharp
public int ProfessorID { get; set; }  // Automàticament clau primària i autonumèric
```

#### 2. Tipus de Dades
Els tipus de C# es mapegen automàticament a tipus SQL:
- `string` → `nvarchar(max)`
- `int` → `int`
- `DateTime` → `datetime2`
- `bool` → `bit`
- `decimal` → `decimal(18,2)`

#### 3. Propietats Requerides vs Opcionals
- Tipus **no nullable** (int, bool, DateTime) → columna `NOT NULL`
- Tipus **nullable** (int?, bool?, DateTime?, string) → columna `NULL`

```csharp
public int Any { get; set; }        // NOT NULL
public int? AnyCaducitat { get; set; }  // NULL permès
public string nom { get; set; }     // NULL permès (string és nullable per defecte)
```

## 4. Data Annotations

Les Data Annotations són atributs que apliquem a les nostres classes i propietats per modificar com EF Core mapeja el model a la base de dades.

### Annotations Bàsiques

#### [Key] - Definir Clau Primària
```csharp
[Key]
public int Codi { get; set; }
```

#### [Required] - Camp Obligatori
```csharp
[Required]
public string nom { get; set; }  // NOT NULL a la base de dades
```

#### [Column] - Personalitzar Nom de Columna
```csharp
[Column("hores_setmana")]
public int horesSetmana { get; set; }

[Column(Order = 1)]  // Ordre en què apareixerà a la BD
public int Codi { get; set; }
```

#### [Table] - Personalitzar Nom de Taula
```csharp
[Table("Modul")]
public class Modul { }

[Table("Estudiants", Schema = "dbo")]  // Amb schema
public class Estudiant { }
```

#### [NotMapped] - Excloure Propietat
```csharp
[NotMapped]  // No es crea aquesta columna a la BD
public string nomComplet 
{ 
    get { return $"{nom} {cognom}"; } 
}
```

#### [DatabaseGenerated] - Control de Generació
```csharp
// Per defecte, les claus primàries són autonumèriques
public int ProfessorID { get; set; }

// Per deshabilitar l'autoincrement (útil per a codis manuals)
[Key]
[DatabaseGenerated(DatabaseGeneratedOption.None)]
public int Codi { get; set; }
```

### Validacions de Text

#### [StringLength] - Longitud de String
```csharp
[StringLength(100, MinimumLength = 3)]
public string cognom { get; set; }

[StringLength(50, MinimumLength = 2, ErrorMessage = "El nom ha de tenir entre 2 i 50 caràcters")]
public string segonCognom { get; set; }
```

#### [MaxLength] - Longitud Màxima
```csharp
[MaxLength(1000)]  // Funciona per Strings i Arrays
public byte[] document { get; set; }
```

#### [RegularExpression] - Patrons
```csharp
[RegularExpression(@"^[a-zA-Z\s]+$", 
    ErrorMessage = "El nom només pot contenir lletres")]
public string nom { get; set; }

[RegularExpression(@"^\d{9}$", 
    ErrorMessage = "El telèfon ha de tenir 9 dígits")]
public string telefon { get; set; }

[RegularExpression(@"^[A-Z]{1}\d{7}[A-Z]{1}$")]
public string DNI { get; set; }
```

#### Validacions Específiques
```csharp
[EmailAddress]  // Ha de ser un correu vàlid
public string email { get; set; }

[Phone]  // Ha de ser un telèfon vàlid
public string telefon { get; set; }

[Url]  // Ha de ser una URL vàlida
public string web { get; set; }
```

### Validacions Numèriques

#### [Range] - Rang de Valors
```csharp
// Rang complet
[Range(0, 100)]
public int credits { get; set; }

// Només mínim
[Range(0, int.MaxValue)]
public int horesSetmana { get; set; }

// Amb missatge personalitzat
[Range(1, 12, ErrorMessage = "El mes ha d'estar entre 1 i 12")]
public int mes { get; set; }

// Amb decimals
[Range(0.0, 10.0)]
public double nota { get; set; }

// Amb tipus específic
[Range(typeof(decimal), "0.01", "999999.99")]
public decimal preu { get; set; }
```

### Altres Validacions

#### [Compare] - Comparar Camps
```csharp
public string Password { get; set; }

[Compare("Password", ErrorMessage = "Les contrasenyes no coincideixen")]
public string ConfirmarPassword { get; set; }
```

#### [Editable] i [DefaultValue]
```csharp
[Editable(false)]  // Camp no editable
public int ProfessorID { get; set; }

[DefaultValue(0)]  // Valor per defecte a la BD
public int HoresExtres { get; set; }
```

## 5. Relacions entre Entitats

### Propietats de Navegació amb `virtual`

En EF Core, utilitzem la paraula clau `virtual` en les propietats de navegació per permetre el **Lazy Loading**. Això significa que les dades relacionades es carreguen automàticament quan s'accedeix a la propietat.

```csharp
// AMB virtual: Lazy Loading activat
public virtual ICollection<Modul> Moduls { get; set; }
public virtual Professor Professor { get; set; }

// SENSE virtual: Cal carregar explícitament amb Include()
public ICollection<Modul> Moduls { get; set; }
```

### 1. Relació Un a Un (1-1)

Una relació 1-1 és quan una entitat està associada amb exactament una altra entitat.

**Exemple: Curs ↔ Professor**
```csharp
public class Curs
{
    public int CursID { get; set; }
    public virtual Professor Professor { get; set; }
}

public class Professor
{
    public int ProfessorID { get; set; }
    public virtual Curs Curs { get; set; }
}
```

**Important:** Les relacions 1-1 **sempre s'han de configurar amb Fluent API** perquè EF Core necessita saber quin és el costat dominant (on va la Foreign Key).

### 2. Relació Un a Molts (1-N)

Una relació 1-N és quan una entitat pot estar associada amb múltiples instàncies d'una altra entitat.

**Exemple: Curs ↔ Moduls**
```csharp
public class Curs
{
    public int CursID { get; set; }
    public virtual ICollection<Modul> Moduls { get; set; }
    
    public Curs()
    {
        Moduls = new HashSet<Modul>();
    }
}

public class Modul
{
    public int Codi { get; set; }
    public virtual Curs Curs { get; set; }
}
```

**Nota:** EF Core pot inferir aquesta relació automàticament gràcies a les propietats de navegació recíproques.

### 3. Relació Molts a Molts (N-N)

Una relació N-N és quan múltiples instàncies d'una entitat estan associades amb múltiples instàncies d'una altra.

**Exemple: Professor ↔ Modul**
```csharp
public class Professor
{
    public int ProfessorID { get; set; }
    public virtual ICollection<Modul> Moduls { get; set; }
    
    public Professor()
    {
        Moduls = new HashSet<Modul>();
    }
}

public class Modul
{
    public int Codi { get; set; }
    public virtual ICollection<Professor> Professors { get; set; }
    
    public Modul()
    {
        Professors = new HashSet<Professor>();
    }
}
```

**Nota:** EF Core crea automàticament la taula intermèdia sense necessitat de definir-la explícitament.

### 4. Relació Recursiva (N-N amb la mateixa entitat)

Una relació recursiva és quan una entitat es relaciona amb ella mateixa.

**Exemple: Modul ↔ Prerequisits**
```csharp
public class Modul
{
    public int Codi { get; set; }
    
    // Prerequisits: Mòduls que cal haver aprovat abans d'aquest
    // M6.Prerequisits → [M3, M2]
    public virtual ICollection<Modul> Prerequisits { get; set; }
    
    // EsPrerequisitDe: Mòduls que tenen aquest com a prerequisit
    // M3.EsPrerequisitDe → [M6, M7]
    public virtual ICollection<Modul> EsPrerequisitDe { get; set; }
    
    public Modul()
    {
        Prerequisits = new HashSet<Modul>();
        EsPrerequisitDe = new HashSet<Modul>();
    }
}
```

**Important:** Les relacions recursives **sempre s'han de configurar amb Fluent API**.

## 6. Data Annotations per a Relacions

### [ForeignKey] - Especificar Clau Forana

Pots definir una Foreign Key de dues maneres:

**Opció 1: A la propietat del valor**
```csharp
public class Modul
{
    public int ModulID { get; set; }
    
    [ForeignKey("Curs")]
    public int CursID { get; set; }
    public virtual Curs Curs { get; set; }
}
```

**Opció 2: A la propietat de navegació**
```csharp
public class Modul
{
    public int ModulID { get; set; }
    public int CursID { get; set; }
    
    [ForeignKey("CursID")]
    public virtual Curs Curs { get; set; }
}
```

### [InverseProperty] - Múltiples Relacions

S'utilitza quan hi ha més d'una relació entre dues entitats.

```csharp
public class Professor
{
    public int ProfessorID { get; set; }
    
    [InverseProperty("ProfessorTitular")]
    public virtual ICollection<Modul> ModulsTitular { get; set; }
    
    [InverseProperty("ProfessorSubstitut")]
    public virtual ICollection<Modul> ModulsSubstitut { get; set; }
}

public class Modul
{
    public int ModulID { get; set; }
    public virtual Professor ProfessorTitular { get; set; }
    public virtual Professor ProfessorSubstitut { get; set; }
}
```

## 7. Context de Base de Dades (DbContext)

El DbContext és la classe principal que coordina tota la funcionalitat d'Entity Framework Core. És el pont entre els teus models i la base de dades.

### Estructura Bàsica

```csharp
using Microsoft.EntityFrameworkCore;

public class ProvaContext : DbContext
{
    // DbSet per a cada entitat
    public DbSet<Professor> Professor { get; set; }
    public DbSet<Modul> Modul { get; set; }
    public DbSet<Curs> Curs { get; set; }
    
    // Constructor buit
    public ProvaContext()
    {
    }
    
    // Configuració de la connexió
    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
    {
        if (!optionsBuilder.IsConfigured)
        {
            optionsBuilder
                .UseSqlServer(@"Server=(localdb)\mssqllocaldb;Database=EFCoreTest;Trusted_Connection=True;")
                .UseLazyLoadingProxies();  // Activa el Lazy Loading automàtic
        }
    }
    
    // Configuració del model (Fluent API)
    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Configuracions amb Fluent API aquí
        
        base.OnModelCreating(modelBuilder);
    }
}
```

### OnConfiguring

El mètode `OnConfiguring` s'utilitza per configurar la connexió a la base de dades.

```csharp
protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
{
    if (!optionsBuilder.IsConfigured)
    {
        optionsBuilder
            .UseSqlServer(@"Server=(localdb)\mssqllocaldb;Database=NomBD;Trusted_Connection=True;")
            .UseLazyLoadingProxies();  // IMPORTANT: Activa Lazy Loading
    }
}
```

**Lazy Loading:** Permet que les propietats `virtual` es carreguin automàticament quan s'accedeix a elles, sense necessitat d'utilitzar `Include()`.

## 8. Configuració amb Fluent API

Fluent API és una manera més potent i flexible de configurar el model que les Data Annotations. Totes les configuracions es fan al mètode `OnModelCreating`.

**Important:** Les relacions **1-1** i les relacions **recursives** són **obligatòries** de configurar amb Fluent API. Per a més detalls sobre Fluent API, consulta la guia específica de Fluent API.

## 8. Gestió de la Base de Dades

### Crear i Gestionar la Base de Dades

EF Core ofereix diversos mètodes per gestionar la creació i actualització de la base de dades:

```csharp
using (var ctx = new ProvaContext())
{
    // Elimina la BD si existeix
    ctx.Database.EnsureDeleted();
    
    // Crea la BD des de zero segons els models
    ctx.Database.EnsureCreated();
}
```

**Mètodes disponibles:**
- `EnsureCreated()`: Crea la BD si no existeix (NO usa migracions)
- `EnsureDeleted()`: Elimina la BD si existeix
- `CanConnect()`: Comprova si es pot connectar a la BD
- `Migrate()`: Aplica migracions pendents (recomanat per a producció)

**Important:** `EnsureCreated()` és útil per a prototips i testing, però per a producció hauries d'utilitzar **Migracions**.

## 9. Operacions CRUD

### CREATE - Crear Entitats

```csharp
using (var ctx = new ProvaContext())
{
    // Crear objectes
    var marc = new Professor() { nom = "Marc", cognom = "Albareda" };
    var m6 = new Modul() { Codi = 6, nom = "Accés a Dades", horesSetmana = 4 };
    
    // Afegir al context
    ctx.Professor.Add(marc);
    ctx.Modul.Add(m6);
    
    // Guardar a la base de dades
    ctx.SaveChanges();
}
```

**Relacions en Creació:**
```csharp
var segonDvi = new Curs()
{
    Cicle = CicleFormatiu.DAMVI,
    Actiu = true,
    Any = 2024,
    Professor = marc  // Assigna directament l'objecte
};

// Afegir a col·leccions
segonDvi.Moduls.Add(m6);
marc.Moduls.Add(m6);

ctx.Curs.Add(segonDvi);
ctx.SaveChanges();  // EF Core gestiona totes les relacions automàticament
```

**Relacions Recursives:**
```csharp
m6.Prerequisits.Add(m3);  // M6 necessita M3
m6.Prerequisits.Add(m2);  // M6 necessita M2
m7.Prerequisits.Add(m3);  // M7 necessita M3

ctx.SaveChanges();
```

### READ - Llegir Entitats

#### Obtenir Totes les Entitats
```csharp
List<Professor> professors = ctx.Professor.ToList();
```

#### Comptar Entitats
```csharp
int total = ctx.Professor.Count();
```

#### Cercar amb WHERE
```csharp
Professor prof = ctx.Professor
    .Where(p => p.cognom == "Albareda")
    .FirstOrDefault();
```

#### Cercar per ID amb FIND
```csharp
Professor prof = ctx.Professor.Find(2);
```

#### Comprovar Condicions
```csharp
// Comprovar si tots compleixen una condició
bool tots = ctx.Professor.All(p => p.nom.StartsWith("G"));

// Comprovar si algun compleix una condició
bool algun = ctx.Professor.Any(p => p.nom == "Marc");
```

### UPDATE - Actualitzar Entitats

```csharp
// 1. Obtenir l'entitat
Professor prof = ctx.Professor.Find(1);

// 2. Modificar propietats
prof.nom = "Nou Nom";

// 3. Guardar canvis
ctx.SaveChanges();
```

### DELETE - Esborrar Entitats

```csharp
// 1. Obtenir l'entitat
Professor prof = ctx.Professor.Find(2);

// 2. Eliminar del context
ctx.Professor.Remove(prof);

// 3. Guardar canvis
ctx.SaveChanges();
```

## 10. Navegació de Relacions amb Lazy Loading

Amb Lazy Loading activat (`.UseLazyLoadingProxies()` i propietats `virtual`), pots navegar per les relacions sense necessitat d'utilitzar `Include()`.

### Navegació 1-1
```csharp
Curs c = ctx.Curs.Find(1);
Console.WriteLine(c.Professor.nom);  // Lazy Loading carrega automàticament el Professor
```

### Navegació a través de Múltiples Relacions
```csharp
Professor m = ctx.Professor.Where(p => p.nom == "Marc").FirstOrDefault();
Console.WriteLine(m.Curs.Cicle);  // Lazy Loading navega Professor → Curs → Cicle
```

### Navegació de Relacions Recursives

**Prerequisits (cap endavant):**
```csharp
Modul m6 = ctx.Modul.Where(p => p.nom == "Accés a Dades").First();

foreach (Modul preq in m6.Prerequisits)
{
    Console.WriteLine(preq.nom);  // Programació, Bases de Dades
}
```

**Navegació Inversa (cap enrere):**
```csharp
Modul m3 = ctx.Modul.Where(p => p.nom == "Programació").First();

foreach (Modul modul in m3.EsPrerequisitDe)
{
    Console.WriteLine(modul.nom);  // Accés a Dades, Interfícies
}
```

## 11. Exemple Complet

```csharp
using System;
using System.Linq;

class Program
{
    static void Main(string[] args)
    {
        using (var ctx = new ProvaContext())
        {
            // Recrear la base de dades
            ctx.Database.EnsureDeleted();
            ctx.Database.EnsureCreated();
            
            // CREATE
            var marc = new Professor() { nom = "Marc", cognom = "Albareda" };
            var m6 = new Modul() { Codi = 6, nom = "Accés a Dades", horesSetmana = 4 };
            var m3 = new Modul() { Codi = 3, nom = "Programació", horesSetmana = 7 };
            
            var segonDvi = new Curs()
            {
                Cicle = CicleFormatiu.DAMVI,
                Actiu = true,
                Any = 2024,
                Professor = marc
            };
            
            segonDvi.Moduls.Add(m6);
            segonDvi.Moduls.Add(m3);
            marc.Moduls.Add(m6);
            marc.Moduls.Add(m3);
            
            m6.Prerequisits.Add(m3);
            
            ctx.Modul.Add(m6);
            ctx.Modul.Add(m3);
            ctx.Professor.Add(marc);
            ctx.Curs.Add(segonDvi);
            ctx.SaveChanges();
            
            // READ
            Professor prof = ctx.Professor
                .Where(p => p.cognom == "Albareda")
                .FirstOrDefault();
            Console.WriteLine($"Professor trobat: {prof.nom}");
            
            // UPDATE
            prof.nom = "Marc Joan";
            ctx.SaveChanges();
            
            // NAVEGACIÓ amb Lazy Loading
            Console.WriteLine($"Cicle on ensenya: {prof.Curs.Cicle}");
            
            // Prerequisits
            Modul modul = ctx.Modul.Find(6);
            foreach (Modul preq in modul.Prerequisits)
            {
                Console.WriteLine($"Prerequisit: {preq.nom}");
            }
        }
    }
}
```

## 12. Bones Pràctiques

### 1. Utilitzar `virtual` per Lazy Loading
```csharp
public virtual ICollection<Modul> Moduls { get; set; }
public virtual Professor Professor { get; set; }
```

### 2. Inicialitzar Col·leccions al Constructor
```csharp
public Professor()
{
    Moduls = new HashSet<Modul>();
}
```

### 3. Utilitzar `using` amb el Context
```csharp
using (var ctx = new ProvaContext())
{
    // Operacions amb la base de dades
}  // El context es tanca i es neteja automàticament
```

### 4. Preferir Fluent API per Configuracions Complexes
- Relacions 1-1: **Obligatori** Fluent API
- Relacions recursives: **Obligatori** Fluent API
- Configuracions simples: Data Annotations està bé
- Per a més detalls, consulta la guia específica de Fluent API

### 5. Mantenir els Models Simples (POCO)
- Només propietats i constructors
- Configuracions complexes a Fluent API
- Lògica de negoci a serveis separats

### 6. Activar Lazy Loading
```csharp
optionsBuilder.UseLazyLoadingProxies();
```
Això simplifica molt la navegació de relacions.

### 7. Gestionar Delete Behavior Adequadament
En estructures complexes amb múltiples relacions, pot ser necessari configurar el comportament d'esborrat. Consulta la guia de Fluent API per a més detalls sobre `DeleteBehavior`.

## 13. Capes de Servei

És una bona pràctica separar la lògica d'accés a dades en classes de servei:

```csharp
public class ProfessorService
{
    private ProvaContext _context;

    public ProfessorService()
    {
        _context = new ProvaContext();
    }

    public Professor CercaPerCognom(string cognom)
    {
        return _context.Professor
            .Where(p => p.cognom == cognom)
            .FirstOrDefault();
    }

    public List<Professor> CercaPerPrimeraLletra(string lletra)
    {
        return _context.Professor
            .Where(p => p.nom.StartsWith(lletra))
            .ToList();
    }
}
```

Això permet:
- Separar responsabilitats
- Reutilitzar codi
- Facilitar el testing
- Mantenir el codi organitzat