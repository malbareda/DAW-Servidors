# Entity Framework - Conceptes Bàsics

## Què és Entity Framework?

Entity Framework (EF) és un Object-Relational Mapper (ORM) desenvolupat per Microsoft que permet als desenvolupadors treballar amb bases de dades relacionals utilitzant objectes .NET. Elimina la necessitat d'escriure la majoria del codi d'accés a dades que normalment necessiten els desenvolupadors.

## Models i Classes POCO

### Què són els models?
Els models són classes que representen les taules de la base de dades. En EF, utilitzem classes POCO (Plain Old CLR Objects) per definir els nostres models.

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
Entity Framework utilitza convencions per inferir l'estructura de la base de dades:

1. **Claus Primàries**: 
   - Una propietat s'identifica com a clau primària si es diu:
     - "ID" 
     - "[NomClasse]ID"
   ```csharp
   public int ProfessorID { get; set; }  // Automàticament clau primària
   ```

2. **Tipus de Dades**:
   - Els tipus de C# es mapegen automàticament a tipus SQL
   - string → nvarchar
   - int → int
   - DateTime → datetime
   - bool → bit

## Data Annotations

Les Data Annotations són atributs que podem aplicar a les nostres classes i propietats per modificar com EF mapeja el model a la base de dades.

```csharp
public class Modul
{
    [Key]                           // Defineix la clau primària
    public int Codi { get; set; }

    [Required]                      // NOT NULL
    public string nom { get; set; }

    [Column("hores_setmana")]       // Nom de la columna a la BD
    public int horesSetmana { get; set; }

    [Column(Order = 1)]  //Ordre en el que apareixera a la BD
    public int Codi { get; set; }

    [NotMapped]  //no és una propietat de la BD, es una variable auxiliar que no ha d'apareixer a la BD
    public string nomComplet 
    { 
        get { return $"{nom} {cognom}"; } 
    }


    [StringLength(100, MinimumLength = 3)]  //Determina maxim (i opcionalment, minim, d'una String)
    public string cognom { get; set; }

    
    [StringLength(50, MinimumLength = 2, ErrorMessage = "El nom ha de tenir entre 2 i 50 caràcters")] // Amb missatge d'error personalitzat
    public string segonCognom { get; set; }

    [MaxLength(1000)]  //maxLenght i minLenght funcionen tant per Strings com per Arrays
    public byte[] document { get; set; }


/////////ANOTACIONS DE RANG

    // Rang complet
    [Range(0, 100)]
    public int credits { get; set; }
    
    // Només mínim (màxim és int.MaxValue, si no tingues mínim seria posar a minim int.MinValue)
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


////////////////REGULAR EXPRESSIONS I SIMILARS

    [RegularExpression(@"^[a-zA-Z\s]+$", 
        ErrorMessage = "El nom només pot contenir lletres")]
    public string nom { get; set; }
    
    [RegularExpression(@"^\d{9}$", 
        ErrorMessage = "El telèfon ha de tenir 9 dígits")]
    public string telefon { get; set; }
    
    [RegularExpression(@"^[A-Z]{1}\d{7}[A-Z]{1}$")]
    public string DNI { get; set; }

    [EmailAddress] //ha de ser un correu vàlid
    public string email { get; set; }

    [Phone] //ha de ser un telefon vàlid
    public string telefon { get; set; }

    [Url] //ha de ser una URL valida
    public string web { get; set; }

    
    public string Password { get; set; }
    [Compare("Password", ErrorMessage = "Les contrasenyes no coincideixen")] //Compare, serveix per comparar uqe un camp sigui igual a un altre
    public string ConfirmarPassword { get; set; }


///// EDICIONS
    [Editable(false)]  //si el camp es pot editar
    public int ProfessorID { get; set; }

    [DefaultValue(0)]  //Aixo posa el valor per defecte a la BD. Igualment teniu el constructor i haurieu de posar el valor tant alla com al constructor
    public int HoresExtres { get; set; }

}
```
## Data Annotations (En Relacions)

Les Data Annotations també es poden fer servir per especificar camps de les relacions

```csharp
[Table("Estudiants", Schema = "dbo")] //es posa a dalt de la CLASSE, no del nom. Defineix el nom i també el Schema, opcionalment
public class Estudiant { }



[Key] //defineix una clau com a clau primarioa. No autonumèric, clau primaria
public int Codi { get; set; }
public string nom { get; set; }

```
En una ForeignKey ho pots fer de dues maneres
```csharp
public class Modul
{
    public int ModulID { get; set; }
    
    [ForeignKey("Curs")] //Pots posar-ho en el int que defineix el valor de la taula
    public int CursID { get; set; }
    public virtual Curs Curs { get; set; }
}


public class Modul
{
    public int ModulID { get; set; }
    public int CursID { get; set; }
    
    [ForeignKey("CursID")] // O també es pot posar a la propietat de navegació:
    public virtual Curs Curs { get; set; }
}
```
[InverseProperty]
Especifica la propietat de navegació inversa quan hi ha múltiples relacions entre dues entitats.
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

## Relacions entre Entitats

### 1. Relació Un a Un (1-1)
Entre `Curs` i `Professor`:
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

### 2. Relació Un a Molts (1-N)
Entre `Curs` i `Modul`:
```csharp
public class Curs
{
    public int CursID { get; set; }
    public ICollection<Modul> Moduls { get; set; }
}

public class Modul
{
    public int ModulID { get; set; }
    public Curs Curs { get; set; }
}
```

### 3. Relació Molts a Molts (N-N)
Entre `Professor` i `Modul`:
```csharp
public class Professor
{
    public int ProfessorID { get; set; }
    public virtual ICollection<Modul> Moduls { get; set; }
}

public class Modul
{
    public int ModulID { get; set; }
    public virtual ICollection<Professor> Professors { get; set; }
}
```

## Configuració de Relacions

Hi ha dues maneres principals de configurar les relacions:

### 1. Per Inferència (Convention)
EF pot inferir moltes relacions basant-se en les propietats i convencions de nomenament:
- Propietats de navegació recíproques
- Col·leccions amb ICollection<T>
- Claus foranes seguint el patró [PropietatNavegació]ID

### 2. Fluent API
Permet una configuració més detallada i explícita al mètode `OnModelCreating`:

```csharp
protected override void OnModelCreating(DbModelBuilder modelBuilder)
{
    modelBuilder.Entity<Professor>()
        .HasOptional(p => p.Curs)    // Professor pot tenir 0 o 1 Curs
        .WithRequired(c => c.Professor);  // Curs ha de tenir 1 Professor
}
```

Altres exemples de Fluent API:
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.nom)
    .IsRequired()
    .HasMaxLength(50);

modelBuilder.Entity<Modul>()
    .HasMany(m => m.Professors)
    .WithMany(p => p.Moduls);
```

## Context de Base de Dades

El context és la classe principal que coordina la funcionalitat d'Entity Framework per un model:

```csharp
public class ProvaContext : DbContext
{
    public ProvaContext() : base("Test")
    {
    }

    public DbSet<Professor> Professor { get; set; }
    public DbSet<Modul> Modul { get; set; }
    public DbSet<Curs> Curs { get; set; }
}
```

### Estratègies d'Inicialització

Entity Framework ofereix diverses estratègies per inicialitzar la base de dades:

```csharp
// Crea la BD només si no existeix
Database.SetInitializer(new CreateDatabaseIfNotExists<ProvaContext>());

// Elimina i recrea la BD si canvia el model
Database.SetInitializer(new DropCreateDatabaseIfModelChanges<ProvaContext>());

// Elimina i recrea la BD sempre
Database.SetInitializer(new DropCreateDatabaseAlways<ProvaContext>());
```

## Bones Pràctiques

1. **Utilitzar `virtual` per Lazy Loading**:
   ```csharp
   public virtual ICollection<Modul> Moduls { get; set; }
   ```

2. **Inicialitzar col·leccions al constructor**:
   ```csharp
   public Professor()
   {
       this.Moduls = new HashSet<Modul>();
   }
   ```

3. **Utilitzar `using` amb el context**:
   ```csharp
   using (var ctx = new ProvaContext())
   {
       // Operacions amb la base de dades
   }
   ```

4. **Preferir Fluent API sobre Data Annotations** per configuracions complexes

5. **Mantenir els models simples** (POCO) i utilitzar configuracions externes
