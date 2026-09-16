# Guia Fluent API amb Entity Framework Core

## 1. Introducció a Fluent API

Fluent API és una manera de configurar models d'Entity Framework Core utilitzant codi en lloc d'atributs (Data Annotations). Es configura al mètode `OnModelCreating` del context:

```csharp
protected override void OnModelCreating(ModelBuilder modelBuilder)
{
    // Aquí van les configuracions
    base.OnModelCreating(modelBuilder);
}
```

**Per què utilitzar Fluent API?**
- Més potent i flexible que Data Annotations
- Permet configuracions que no són possibles amb atributs
- Separa la configuració de la base de dades dels models
- **Obligatori** per a relacions 1-1 i recursives
- Millor per a configuracions complexes

## 2. Configuració de Taules

### Nom de Taula i Schema
```csharp
modelBuilder.Entity<Professor>()
    .ToTable("Professors", "dbo");
```
Aquesta configuració:
- Defineix el nom de la taula a la base de dades
- Especifica l'schema (per defecte és "dbo")
- És equivalent a l'atribut `[Table("Professors", Schema = "dbo")]`

### Exemple amb múltiples entitats
```csharp
modelBuilder.Entity<Professor>().ToTable("Professors");
modelBuilder.Entity<Modul>().ToTable("Moduls");
modelBuilder.Entity<Curs>().ToTable("Cursos");
```

### Configuració de Clau Primària
```csharp
// Clau primària simple
modelBuilder.Entity<Modul>()
    .HasKey(m => m.Codi);

// Clau primària composta
modelBuilder.Entity<Inscripcio>()
    .HasKey(i => new { i.EstudiantID, i.ModulID });
```
Permet:
- Definir quina propietat és la clau primària
- Crear claus primàries compostes
- És equivalent a l'atribut `[Key]`

### Desactivar Autonumèric
```csharp
modelBuilder.Entity<Modul>()
    .Property(m => m.Codi)
    .ValueGeneratedNever();  // No és autonumèric
```

## 3. Configuració de Propietats

### Propietats Requerides i Opcionals
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.nom)
    .IsRequired()           // NOT NULL
    .HasMaxLength(50);      // nvarchar(50)

modelBuilder.Entity<Modul>()
    .Property(m => m.dataInici)
    .IsRequired(false);     // Permet NULL (opcional)
```
Configura:
- Si una propietat pot ser NULL
- Longitud màxima per strings
- És equivalent a `[Required]` i `[MaxLength]`

### Tipus de Dades i Precisió
```csharp
// Tipus específic SQL Server
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasColumnType("int");

// Configuració avançada de string
modelBuilder.Entity<Modul>()
    .Property(m => m.nom)
    .HasMaxLength(100)
    .IsUnicode(true);       // nvarchar en lloc de varchar

// Decimals amb precisió
modelBuilder.Entity<Nota>()
    .Property(n => n.valor)
    .HasPrecision(5, 2);    // decimal(5,2)
```
Permet configurar:
- Tipus de dada SQL específic
- Precisió i escala per decimals
- Suport Unicode per strings

### Noms de Columna
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasColumnName("hores_assignades");

modelBuilder.Entity<Modul>()
    .Property(m => m.horesSetmana)
    .HasColumnName("hores_setmana");
```
Equivalent a `[Column("nom_columna")]`, permet:
- Definir el nom de la columna a la BD
- Seguir convencions específiques de nomenclatura

### Valors per Defecte
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasDefaultValue(0);

modelBuilder.Entity<Curs>()
    .Property(c => c.Actiu)
    .HasDefaultValue(true);

// Valor per defecte amb SQL
modelBuilder.Entity<Modul>()
    .Property(m => m.dataCreacio)
    .HasDefaultValueSql("GETDATE()");
```

### Ignorar Propietats
```csharp
modelBuilder.Entity<Professor>()
    .Ignore(p => p.nomComplet);  // No es mapeja a la BD
```
Equivalent a `[NotMapped]`.

## 4. Configuració de Relacions

### Relació Un a Un (1-1) - OBLIGATORI amb Fluent API

**Important:** Les relacions 1-1 **sempre** s'han de configurar amb Fluent API perquè EF Core necessita saber quin és el costat dominant (on va la Foreign Key).

```csharp
// Relació Professor-Curs (1-1)
modelBuilder.Entity<Professor>()
    .HasOne(p => p.Curs)                // Professor té un Curs
    .WithOne(c => c.Professor)          // Curs té un Professor
    .HasForeignKey<Curs>("ProfessorID") // La FK està a Curs (costat dependent)
    .OnDelete(DeleteBehavior.NoAction); // No delete en cascada
```

**Opcions de Delete Behavior:**
- `DeleteBehavior.Cascade`: Esborra en cascada (per defecte en EF Core)
- `DeleteBehavior.NoAction`: No esborra automàticament
- `DeleteBehavior.SetNull`: Posa NULL a la FK
- `DeleteBehavior.Restrict`: Impedeix l'esborrat si hi ha dades relacionades
- `DeleteBehavior.ClientSetNull`: Posa NULL només a nivell de client

**Exemple amb relació opcional:**
```csharp
// Professor pot no tenir curs assignat
modelBuilder.Entity<Professor>()
    .HasOne(p => p.Curs)
    .WithOne(c => c.Professor)
    .HasForeignKey<Curs>("ProfessorID")
    .IsRequired(false);  // La FK pot ser NULL
```

### Relació Un a Molts (1-N)

EF Core pot inferir aquesta relació automàticament, però podem configurar-la explícitament:

```csharp
// Relació Curs-Modul (1-N)
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)         // Curs té molts Moduls
    .WithOne(m => m.Curs)           // Modul té un Curs
    .HasForeignKey(m => m.CursID)   // Especifica la FK (opcional)
    .OnDelete(DeleteBehavior.Cascade);  // Delete en cascada

// Amb FK nullable (relació opcional)
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)
    .WithOne(m => m.Curs)
    .HasForeignKey(m => m.CursID)
    .IsRequired(false);  // El mòdul pot no tenir curs assignat
```

**Configuració avançada:**
```csharp
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)
    .WithOne(m => m.Curs)
    .HasForeignKey(m => m.CursID)
    .OnDelete(DeleteBehavior.Restrict)  // Impedeix esborrar curs si té mòduls
    .HasConstraintName("FK_Modul_Curs");  // Nom de la constraint
```

### Relació Molts a Molts (N-N)

EF Core pot inferir relacions N-N simples, però podem configurar-les per personalitzar la taula intermèdia:

**Configuració Simple (EF Core crea la taula automàticament):**
```csharp
modelBuilder.Entity<Professor>()
    .HasMany(p => p.Moduls)
    .WithMany(m => m.Professors);
```
Crea automàticament una taula `ModulProfessor` amb les columnes `ModulsID` i `ProfessorsID`.

**Configuració Personalitzada:**
```csharp
modelBuilder.Entity<Professor>()
    .HasMany(p => p.Moduls)
    .WithMany(m => m.Professors)
    .UsingEntity<Dictionary<string, object>>(
        "ProfessorModul",  // Nom de la taula intermèdia
        j => j
            .HasOne<Modul>()
            .WithMany()
            .HasForeignKey("ModulID")
            .HasConstraintName("FK_ProfessorModul_Modul")
            .OnDelete(DeleteBehavior.Cascade),
        j => j
            .HasOne<Professor>()
            .WithMany()
            .HasForeignKey("ProfessorID")
            .HasConstraintName("FK_ProfessorModul_Professor")
            .OnDelete(DeleteBehavior.Cascade),
        j =>
        {
            j.HasKey("ProfessorID", "ModulID");
            j.ToTable("ProfessorModul");
        }
    );
```

### Relació N-N Recursiva - OBLIGATORI amb Fluent API

**Important:** Les relacions recursives **sempre** s'han de configurar amb Fluent API.

```csharp
// Relació Modul-Prerequisits (N-N recursiva)
modelBuilder.Entity<Modul>()
    .HasMany(m => m.Prerequisits)           // Modul té molts Prerequisits
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
        // Configura la taula intermèdia
        j =>
        {
            j.HasKey("ModulID", "PrerequisitID");
            j.ToTable("ModulPrerrequisits");
        }
    );
```

**Estructura de la taula generada:**
```
ModulPrerrequisits
├─ ModulID (FK → Modul.Codi)          // L'assignatura que necessita prerequisits
└─ PrerequisitID (FK → Modul.Codi)    // L'assignatura que és prerequisit
```

**Exemple de dades:**
| ModulID | PrerequisitID | Significat |
|---------|---------------|------------|
| 6 | 3 | M6 (Accés a Dades) necessita M3 (Programació) |
| 6 | 2 | M6 (Accés a Dades) necessita M2 (Bases de Dades) |
| 7 | 3 | M7 (Interfícies) necessita M3 (Programació) |

**Per què NoAction en Delete?**
En relacions recursives, el delete en cascada pot crear cicles infinits. Per això es recomana `DeleteBehavior.NoAction`.

## 5. Configuracions Avançades

### Índexs

```csharp
// Índex simple
modelBuilder.Entity<Professor>()
    .HasIndex(p => p.cognom);

// Índex únic
modelBuilder.Entity<Professor>()
    .HasIndex(p => p.email)
    .IsUnique();

// Índex compost
modelBuilder.Entity<Professor>()
    .HasIndex(p => new { p.cognom, p.nom })
    .HasDatabaseName("IX_Professor_NomComplet");

// Índex amb filtre
modelBuilder.Entity<Curs>()
    .HasIndex(c => c.Any)
    .HasFilter("[Actiu] = 1");
```

### Restriccions CHECK

```csharp
modelBuilder.Entity<Professor>()
    .ToTable(t => t.HasCheckConstraint(
        "CK_HoresPositives",
        "[hores_assignades] >= 0"
    ));

modelBuilder.Entity<Modul>()
    .ToTable(t => t.HasCheckConstraint(
        "CK_HoresSetmanaValid",
        "[hores_setmana] BETWEEN 1 AND 10"
    ));
```

### Sequences (Seqüències)

```csharp
modelBuilder.HasSequence<int>("OrderNumbers")
    .StartsAt(1000)
    .IncrementsBy(1);

modelBuilder.Entity<Comanda>()
    .Property(c => c.NumeroComanda)
    .HasDefaultValueSql("NEXT VALUE FOR OrderNumbers");
```

### Vistes (Views)

```csharp
modelBuilder.Entity<ProfessorView>()
    .ToView("VistaModulsPerProfessor")
    .HasNoKey();  // Les vistes sovint no tenen clau primària
```

### Dades d'Inicialització (Seeding)

```csharp
modelBuilder.Entity<CicleFormatiu>().HasData(
    new CicleFormatiu { CicleID = 1, Nom = "DAM", NomComplet = "Desenvolupament d'Aplicacions Multiplataforma" },
    new CicleFormatiu { CicleID = 2, Nom = "ASIX", NomComplet = "Administració de Sistemes Informàtics en Xarxa" },
    new CicleFormatiu { CicleID = 3, Nom = "DAW", NomComplet = "Desenvolupament d'Aplicacions Web" }
);
```

## 6. Organització del Codi

### Separar Configuracions en Classes

És una bona pràctica crear classes de configuració separades per a cada entitat:

```csharp
public class ProfessorConfiguration : IEntityTypeConfiguration<Professor>
{
    public void Configure(EntityTypeBuilder<Professor> builder)
    {
        builder.ToTable("Professors");
        
        builder.HasKey(p => p.ProfessorID);
        
        builder.Property(p => p.nom)
            .IsRequired()
            .HasMaxLength(50);
        
        builder.Property(p => p.cognom)
            .IsRequired()
            .HasMaxLength(100);
        
        builder.Property(p => p.horesAssignades)
            .HasColumnName("hores_assignades")
            .HasDefaultValue(0);
        
        builder.HasOne(p => p.Curs)
            .WithOne(c => c.Professor)
            .HasForeignKey<Curs>("ProfessorID")
            .OnDelete(DeleteBehavior.NoAction);
        
        builder.HasMany(p => p.Moduls)
            .WithMany(m => m.Professors);
    }
}
```

**Aplicar la configuració al context:**
```csharp
protected override void OnModelCreating(ModelBuilder modelBuilder)
{
    modelBuilder.ApplyConfiguration(new ProfessorConfiguration());
    modelBuilder.ApplyConfiguration(new ModulConfiguration());
    modelBuilder.ApplyConfiguration(new CursConfiguration());
    
    // O aplicar totes les configuracions de l'assembly automàticament
    modelBuilder.ApplyConfigurationsFromAssembly(typeof(ProvaContext).Assembly);
    
    base.OnModelCreating(modelBuilder);
}
```

## 7. Exemple Complet

```csharp
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

public class ProvaContext : DbContext
{
    public DbSet<Professor> Professor { get; set; }
    public DbSet<Modul> Modul { get; set; }
    public DbSet<Curs> Curs { get; set; }

    public ProvaContext()
    {
    }

    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
    {
        if (!optionsBuilder.IsConfigured)
        {
            optionsBuilder
                .UseSqlServer(@"Server=(localdb)\mssqllocaldb;Database=EFCoreTest;Trusted_Connection=True;")
                .UseLazyLoadingProxies();
        }
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Configuració de Professor
        modelBuilder.Entity<Professor>(entity =>
        {
            entity.ToTable("Professors");
            entity.HasKey(p => p.ProfessorID);
            
            entity.Property(p => p.nom)
                .IsRequired()
                .HasMaxLength(50);
            
            entity.Property(p => p.cognom)
                .IsRequired()
                .HasMaxLength(100);
            
            entity.Property(p => p.horesAssignades)
                .HasColumnName("hores_assignades")
                .HasDefaultValue(0);
            
            entity.HasIndex(p => p.cognom);
        });

        // Configuració de Modul
        modelBuilder.Entity<Modul>(entity =>
        {
            entity.ToTable("Moduls");
            entity.HasKey(m => m.Codi);
            
            entity.Property(m => m.Codi)
                .ValueGeneratedNever();  // No autonumèric
            
            entity.Property(m => m.nom)
                .IsRequired()
                .HasMaxLength(100);
            
            entity.Property(m => m.horesSetmana)
                .HasColumnName("hores_setmana");
            
            entity.ToTable(t => t.HasCheckConstraint(
                "CK_HoresSetmanaValid",
                "[hores_setmana] BETWEEN 1 AND 10"
            ));
        });

        // Configuració de Curs
        modelBuilder.Entity<Curs>(entity =>
        {
            entity.ToTable("Cursos");
            entity.HasKey(c => c.CursID);
            
            entity.Property(c => c.Actiu)
                .HasDefaultValue(true);
        });

        // RELACIÓ 1-1: Professor-Curs (OBLIGATORI Fluent API)
        modelBuilder.Entity<Professor>()
            .HasOne(p => p.Curs)
            .WithOne(c => c.Professor)
            .HasForeignKey<Curs>("ProfessorID")
            .OnDelete(DeleteBehavior.NoAction);

        // RELACIÓ 1-N: Curs-Modul (Opcional, però recomanat per claredat)
        modelBuilder.Entity<Curs>()
            .HasMany(c => c.Moduls)
            .WithOne(m => m.Curs)
            .HasForeignKey("CursID")
            .OnDelete(DeleteBehavior.Cascade);

        // RELACIÓ N-N: Professor-Modul (Opcional)
        modelBuilder.Entity<Professor>()
            .HasMany(p => p.Moduls)
            .WithMany(m => m.Professors);

        // RELACIÓ N-N RECURSIVA: Modul-Prerequisits (OBLIGATORI Fluent API)
        modelBuilder.Entity<Modul>()
            .HasMany(m => m.Prerequisits)
            .WithMany(m => m.EsPrerequisitDe)
            .UsingEntity<Dictionary<string, object>>(
                "ModulPrerrequisits",
                r => r
                    .HasOne<Modul>()
                    .WithMany()
                    .HasForeignKey("PrerequisitID")
                    .OnDelete(DeleteBehavior.NoAction),
                l => l
                    .HasOne<Modul>()
                    .WithMany()
                    .HasForeignKey("ModulID")
                    .OnDelete(DeleteBehavior.NoAction),
                j =>
                {
                    j.HasKey("ModulID", "PrerequisitID");
                    j.ToTable("ModulPrerrequisits");
                }
            );

        base.OnModelCreating(modelBuilder);
    }
}
```

## 8. Bones Pràctiques

### 1. Quan Utilitzar Fluent API vs Data Annotations

**Utilitza SEMPRE Fluent API per:**
- Relacions 1-1 (obligatori)
- Relacions recursives (obligatori)
- Configuracions de relacions complexes
- Índexs compostos
- Restriccions CHECK
- Configuracions que no es poden expressar amb atributs

**Pots utilitzar Data Annotations per:**
- Validacions simples (`[Required]`, `[MaxLength]`)
- Noms de columnes (`[Column]`)
- Claus primàries simples (`[Key]`)
- Propietats que no es mapegen (`[NotMapped]`)

### 2. Organització del Codi

- **Projectes petits**: Configuracions al `OnModelCreating`
- **Projectes mitjans/grans**: Classes de configuració separades (`IEntityTypeConfiguration<T>`)
- **Projectes empresarials**: Carpeta `Configurations/` amb una classe per entitat

### 3. Nomenament Consistent

```csharp
// Bones pràctiques de nomenament
modelBuilder.Entity<Modul>()
    .ToTable("Moduls");  // Nom en plural per taules

modelBuilder.Entity<Professor>()
    .HasIndex(p => p.email)
    .HasDatabaseName("IX_Professor_Email");  // Prefix IX_ per índexs
```

### 4. Delete Behavior

```csharp
// Per defecte, EF Core usa Cascade
// Canvia-ho només quan sigui necessari:

// Relacions principals: Cascade (comportament normal)
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)
    .WithOne(m => m.Curs)
    .OnDelete(DeleteBehavior.Cascade);

// Relacions que poden crear cicles: NoAction
modelBuilder.Entity<Professor>()
    .HasOne(p => p.Curs)
    .WithOne(c => c.Professor)
    .OnDelete(DeleteBehavior.NoAction);

// Relacions opcionals on vols mantenir els fills: SetNull
modelBuilder.Entity<Modul>()
    .HasOne(m => m.ProfessorTutor)
    .WithMany()
    .OnDelete(DeleteBehavior.SetNull);
```

### 5. Optimització de Consultes

```csharp
// Índexs per camps que es busquen sovint
modelBuilder.Entity<Professor>()
    .HasIndex(p => p.cognom);

// Índexs compostos per consultes complexes
modelBuilder.Entity<Modul>()
    .HasIndex(m => new { m.CursID, m.Actiu });

// Índexs amb filtre per estalviar espai
modelBuilder.Entity<Curs>()
    .HasIndex(c => c.Any)
    .HasFilter("[Actiu] = 1");  // Només indexa cursos actius
```

### 6. Validacions a Nivell de BD

```csharp
// Combina validacions de client (Data Annotations) amb validacions de BD
modelBuilder.Entity<Professor>()
    .ToTable(t => t.HasCheckConstraint(
        "CK_HoresPositives",
        "[hores_assignades] >= 0 AND [hores_assignades] <= 40"
    ));
```

### 7. Documentació

```csharp
// Documenta decisions de disseny complexes
protected override void OnModelCreating(ModelBuilder modelBuilder)
{
    // IMPORTANT: Relació 1-1 Professor-Curs
    // La FK està a Curs perquè un professor pot existir sense curs assignat
    // però un curs sempre ha de tenir un professor responsable
    modelBuilder.Entity<Professor>()
        .HasOne(p => p.Curs)
        .WithOne(c => c.Professor)
        .HasForeignKey<Curs>("ProfessorID")
        .OnDelete(DeleteBehavior.NoAction);  // NoAction per evitar cicles
}
```

### 8. Testing de Configuracions

```csharp
// Comprova que les configuracions són correctes abans de crear la BD
using (var ctx = new ProvaContext())
{
    var model = ctx.Model;
    var entityTypes = model.GetEntityTypes();
    
    foreach (var entityType in entityTypes)
    {
        Console.WriteLine($"Entitat: {entityType.Name}");
        foreach (var property in entityType.GetProperties())
        {
            Console.WriteLine($"  - {property.Name}: {property.GetColumnType()}");
        }
    }
}
```

## 9. Errors Comuns i Solucions

### Error: "Unable to determine the relationship"
**Problema:** EF Core no pot inferir una relació complexa.
**Solució:** Configura-la explícitament amb Fluent API.

### Error: "Introducing FOREIGN KEY constraint may cause cycles"
**Problema:** Multiple delete paths en SQL Server.
**Solució:** Usa `OnDelete(DeleteBehavior.NoAction)` en alguna de les relacions.

### Error: "The property cannot be configured as a navigation"
**Problema:** Has intentat configurar una propietat escalar com a navegació.
**Solució:** Revisa que estàs configurant la propietat correcta.

### Error: "The entity type requires a primary key"
**Problema:** No hi ha clau primària definida.
**Solució:** Defineix la clau amb `.HasKey()` o segueix les convencions (propietat ID o [NomClasse]ID).

## 10. Recursos Addicionals

- **Documentació oficial**: https://learn.microsoft.com/ef/core/modeling/
- **Comparativa EF6 vs EF Core**: https://learn.microsoft.com/ef/efcore-and-ef6/
- **Patrons de configuració**: https://learn.microsoft.com/ef/core/modeling/entity-type-configuration