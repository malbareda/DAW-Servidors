# Guia Fluent API amb Entity Framework

## 1. Introducció a Fluent API

Fluent API és una manera de configurar models d'Entity Framework utilitzant codi en lloc d'atributs. Es configura al mètode `OnModelCreating` del context:

```csharp
protected override void OnModelCreating(DbModelBuilder modelBuilder)
{
    // Aquí van les configuracions
    base.OnModelCreating(modelBuilder);
}
```

## 2. Configuració de Taules

### Nom de Taula i Schema
```csharp
modelBuilder.Entity<Professor>()
    .ToTable("Professors", "dbo");
```
Aquesta configuració:
- Defineix el nom de la taula a la base de dades
- Especifica l'schema (per defecte és "dbo")
- És equivalent a l'atribut `[Table("Professors")]`

### Configuració de Clau Primària
```csharp
modelBuilder.Entity<Modul>()
    .HasKey(m => m.Codi);

// Clau primària composta (si fos necessari)
modelBuilder.Entity<Modul>()
    .HasKey(m => new { m.Codi, m.Any });
```
Permet:
- Definir quina propietat és la clau primària
- Crear claus primàries compostes
- És equivalent a l'atribut `[Key]`

## 3. Configuració de Propietats

### Propietats Requerides i Opcionals
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.nom)
    .IsRequired()  // NOT NULL
    .HasMaxLength(50);  // nvarchar(50)

modelBuilder.Entity<Modul>()
    .Property(m => m.dataInici)
    .IsOptional();  // Permet NULL
```
Configura:
- Si una propietat pot ser NULL
- Longitud màxima per strings
- És equivalent a `[Required]` i `[MaxLength]`

### Tipus de Dades i Precisió
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasColumnType("int");

modelBuilder.Entity<Modul>()
    .Property(m => m.nom)
    .HasColumnType("nvarchar")
    .HasMaxLength(100)
    .IsUnicode(true);
```
Permet configurar:
- Tipus de dada SQL específic
- Precisió i escala per decimals
- Suport Unicode per strings

### Noms de Columna i Convencions
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasColumnName("hores_assignades");

modelBuilder.Entity<Modul>()
    .Property(m => m.horesSetmana)
    .HasColumnName("hores_setmana");
```
Equivalent a `[Column]`, permet:
- Definir el nom de la columna a la BD
- Seguir convencions específiques de nomenclatura

## 4. Configuració de Relacions

### Relació Un a Un (1-1)
```csharp
// Relació Professor-Curs (1-1)
modelBuilder.Entity<Professor>()
    .HasOptional(p => p.Curs)    // Professor pot tenir 0 o 1 Curs
    .WithRequired(c => c.Professor);  // Curs ha de tenir 1 Professor

// Alternativa amb clau forana explícita
modelBuilder.Entity<Professor>()
    .HasOptional(p => p.Curs)
    .WithRequired(c => c.Professor)
    .Map(m => m.MapKey("ProfessorID"));
```
Aquesta configuració:
- Defineix una relació 1-1 opcional-requerida
- Especifica quina entitat és la principal
- Permet configurar la clau forana

### Relació Un a Molts (1-N)
```csharp
// Relació Curs-Modul (1-N)
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)
    .WithRequired(m => m.Curs)
    .WillCascadeOnDelete(true);

// Amb clau forana explícita
modelBuilder.Entity<Curs>()
    .HasMany(c => c.Moduls)
    .WithRequired(m => m.Curs)
    .HasForeignKey(m => m.CursID);
```
Permet configurar:
- Multiplicitat de la relació
- Comportament en eliminació en cascada
- Clau forana explícita

### Relació Molts a Molts (N-N)
```csharp
// Relació Professor-Modul (N-N)
modelBuilder.Entity<Professor>()
    .HasMany(p => p.Moduls)
    .WithMany(m => m.Professors)
    .Map(m =>
    {
        m.ToTable("ProfessorModul");
        m.MapLeftKey("ProfessorID");
        m.MapRightKey("ModulID");
    });
```
Permet:
- Definir la taula intermèdia
- Configurar les claus de la relació
- Personalitzar el mapping

## 5. Configuracions Avançades

### Índexs
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.cognom)
    .HasColumnAnnotation("Index", new IndexAnnotation(new IndexAttribute()));

// Índex compost
modelBuilder.Entity<Professor>()
    .HasIndex(p => new { p.cognom, p.nom });
```
Permet crear:
- Índexs simples
- Índexs compostos
- Índexs únics

### Herència
```csharp
// Si tinguéssim una classe PersonaBase
modelBuilder.Entity<PersonaBase>()
    .Map<Professor>(m => m.Requires("TipusPersona").HasValue("Professor"))
    .Map<Alumne>(m => m.Requires("TipusPersona").HasValue("Alumne"));
```
Configura:
- Estratègia d'herència (TPH, TPT, TPC)
- Discriminador per tipus
- Mapping específic per cada tipus

### Propietats de Navegació
```csharp
modelBuilder.Entity<Professor>()
    .HasMany(p => p.Moduls)
    .WithMany(m => m.Professors)
    .Map(m =>
    {
        m.ToTable("ProfessorModul");
        m.MapLeftKey("ProfessorID");
        m.MapRightKey("ModulID");
    });
```
Configura:
- Lazy loading
- Eager loading per defecte
- Propietats de navegació

### Validacions i Restriccions
```csharp
modelBuilder.Entity<Professor>()
    .Property(p => p.horesAssignades)
    .HasPrecision(5, 2)
    .IsRequired()
    .HasColumnAnnotation(
        "Check",
        new CheckConstraintAnnotation("CK_HoresPositives", "hores_assignades >= 0"));
```
Permet definir:
- Restriccions CHECK
- Valors per defecte
- Regles de validació

## 6. Configuracions Complexes

### Exemple Complet
```csharp
protected override void OnModelCreating(DbModelBuilder modelBuilder)
{
    // Configuració de Professor
    modelBuilder.Entity<Professor>()
        .ToTable("Professors")
        .HasKey(p => p.ProfessorID)
        .Property(p => p.nom)
            .IsRequired()
            .HasMaxLength(50)
            .HasColumnName("nom_professor");

    // Configuració de Modul
    modelBuilder.Entity<Modul>()
        .ToTable("Moduls")
        .HasKey(m => m.Codi)
        .Property(m => m.horesSetmana)
            .IsRequired()
            .HasColumnName("hores_setmana");

    // Configuració de relacions
    modelBuilder.Entity<Professor>()
        .HasOptional(p => p.Curs)
        .WithRequired(c => c.Professor);

    modelBuilder.Entity<Professor>()
        .HasMany(p => p.Moduls)
        .WithMany(m => m.Professors)
        .Map(m =>
        {
            m.ToTable("ProfessorModul");
            m.MapLeftKey("ProfessorID");
            m.MapRightKey("ModulID");
        });

    base.OnModelCreating(modelBuilder);
}
```

## 7. Bones Pràctiques

1. **Centralitzar Configuracions**:
   - Crear classes de configuració separades per cada entitat
   - Utilitzar l'extensió `EntityTypeConfiguration<T>`

2. **Mantenir Consistència**:
   - Usar el mateix estil de configuració
   - Documentar decisions de disseny

3. **Optimitzar Rendiment**:
   - Configurar índexs apropiadament
   - Gestionar lazy loading adequadament

4. **Validacions**:
   - Combinar validacions de client i servidor
   - Utilitzar restriccions de base de dades quan sigui possible

5. **Mantenibilitat**:
   - Agrupar configuracions relacionades
   - Seguir convencions de nomenclatura consistents
