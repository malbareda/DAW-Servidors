# Guia Lazy Loading vs Eager Loading amb Entity Framework

## 1. Introducció als Tipus de Càrrega

Entity Framework ofereix diferents estratègies per carregar dades relacionades. Entendre aquestes estratègies és fonamental per escriure aplicacions eficients.

### Tipus de Càrrega
- **Lazy Loading**: Les dades relacionades es carreguen automàticament quan hi accedim
- **Eager Loading**: Les dades relacionades es carreguen explícitament amb la consulta inicial
- **Explicit Loading**: Carreguem manualment les dades relacionades quan ho necessitem

## 2. Lazy Loading amb Virtual

### Què és el Lazy Loading?

El Lazy Loading permet que EF carregui automàticament les propietats de navegació quan hi accedim. Per activar-lo, cal marcar les propietats com a `virtual`.

```csharp
public class Protocolo
{
    public int ProtocoloID { get; set; }
    public string ProtocoloName { get; set; }
    
    // VIRTUAL activa el Lazy Loading
    public virtual ICollection<Alarma> Alarmas { get; set; }
    
    public Protocolo() 
    {
        Alarmas = new HashSet<Alarma>();
    }
}
```

### Com Funciona?

Quan una propietat és `virtual`, EF crea un proxy (classe derivada) que intercepta l'accés a la propietat:

```csharp
using (var ctx = new FuegoContext())
{
    // Carreguem només el Protocolo
    Protocolo prot = ctx.Protocolo.Find(1);
    
    // Quan accedim a Alarmas, EF automàticament:
    // 1. Detecta que la col·lecció no està carregada
    // 2. Fa una consulta SELECT a la BD
    // 3. Omple la col·lecció amb els resultats
    Console.WriteLine(prot.Alarmas.First().Name);  // ✓ Funciona!
}
```

### Avantatges del Lazy Loading

1. **Còmode i intuïtiu**: No cal pensar en què carregar
2. **Estalvia memòria inicial**: Només carrega el que necessites
3. **Codi més net**: Menys Include() explícits

```csharp
// Sense Lazy Loading necessitaries:
var prot = ctx.Protocolo.Include(p => p.Alarmas).Find(1);

// Amb Lazy Loading pots fer:
var prot = ctx.Protocolo.Find(1);
// I automàticament carrega Alarmas quan hi accedim
```

### Inconvenients del Lazy Loading

1. **Problema N+1**: Pot generar moltes consultes a la BD

```csharp
// PROBLEMA: Això genera 1 + N consultes!
var protocols = ctx.Protocolo.ToList();  // 1 consulta
foreach (var p in protocols)
{
    // Cada iteració fa una consulta extra! (N consultes)
    Console.WriteLine(p.Alarmas.Count());
}

// SOLUCIÓ: Usar Eager Loading
var protocols = ctx.Protocolo.Include(p => p.Alarmas).ToList();  // 1 sola consulta
```

2. **Requereix context obert**: Si el context es tanca, peta

```csharp
Protocolo prot;
using (var ctx = new FuegoContext())
{
    prot = ctx.Protocolo.Find(1);
}  // Context tancat aquí!

// AIXÒ PETA: El context està tancat, no pot carregar Alarmas
Console.WriteLine(prot.Alarmas.First().Name);  // ✗ Exception!
```

3. **Menys predictible**: Difícil saber quantes consultes es faran

## 3. Eager Loading amb Include

### Què és l'Eager Loading?

L'Eager Loading carrega explícitament les dades relacionades en la consulta inicial. S'utilitza el mètode `Include()`.

```csharp
using (var ctx = new FuegoContext())
{
    // Carreguem Protocolo amb les seves Alarmas en una sola consulta
    Protocolo prot = ctx.Protocolo
        .Include(p => p.Alarmas)
        .First();
    
    // Alarmas ja està carregat, no fa més consultes
    Console.WriteLine(prot.Alarmas.First().Name);  // ✓ Funciona!
}
```

### Include amb Múltiples Nivells

Pots carregar relacions en profunditat:

```csharp
// Carregar Protocolo → Alarmas → Tono (tres nivells)
var prot = ctx.Protocolo
    .Include(p => p.Alarmas.Select(a => a.Tono))
    .First();

// O amb sintaxi de cadena (menys type-safe)
var prot = ctx.Protocolo
    .Include("Alarmas.Tono")
    .First();
```

### Múltiples Include

Pots fer diversos Include en la mateixa consulta:

```csharp
// Carregar múltiples relacions
var alarma = ctx.Alarma
    .Include(a => a.Protocolo)
    .Include(a => a.Tono)
    .First();
```

### Avantatges de l'Eager Loading

1. **Rendiment predictible**: Saps exactament quantes consultes es fan
2. **Funciona amb context tancat**: Pots retornar l'objecte fora del using
3. **Evita el problema N+1**: Una sola consulta per tot

```csharp
Protocolo prot;
using (var ctx = new FuegoContext())
{
    // Carreguem tot el que necessitem
    prot = ctx.Protocolo
        .Include(p => p.Alarmas)
        .First();
}  // Context tancat

// Funciona perquè tot està carregat
Console.WriteLine(prot.Alarmas.First().Name);  // ✓ Funciona!
```

### Inconvenients de l'Eager Loading

1. **Menys flexible**: Cal saber d'antuvi què necessites
2. **Pot carregar massa dades**: Si no totes les propietats es necessiten
3. **Codi més verbós**: Molts Include() poden fer el codi menys llegible

## 4. Comparació Pràctica

### Exemple: Propietat NO Virtual (Eager obligatori)

```csharp
public class Protocolo
{
    public int ProtocoloID { get; set; }
    
    // NO és virtual → Eager Loading obligatori
    public ICollection<Alarma> Alarmas { get; set; }
}

// Això deixa Alarmas com a NULL
var prot = ctx.Protocolo.Find(1);
Console.WriteLine(prot.Alarmas);  // NULL!

// Cal fer Include explícitament
var prot = ctx.Protocolo
    .Include(p => p.Alarmas)
    .First();
Console.WriteLine(prot.Alarmas.Count());  // ✓ Funciona!
```

### Exemple: Propietat Virtual (Lazy disponible)

```csharp
public class Protocolo
{
    public int ProtocoloID { get; set; }
    
    // SÍ és virtual → Lazy Loading disponible
    public virtual ICollection<Alarma> Alarmas { get; set; }
}

// Funciona amb Lazy Loading (si context obert)
var prot = ctx.Protocolo.Find(1);
Console.WriteLine(prot.Alarmas.Count());  // ✓ Funciona!

// Però també podem fer Eager Loading si volem
var prot2 = ctx.Protocolo
    .Include(p => p.Alarmas)
    .First();
```

## 5. Explicit Loading

### Càrrega Manual de Relacions

Explicit Loading permet carregar relacions manualment quan ho necessitem:

```csharp
using (var ctx = new FuegoContext())
{
    // Carreguem només el Protocolo
    var prot = ctx.Protocolo.Find(1);
    
    // Després decidim carregar les Alarmas
    ctx.Entry(prot)
        .Collection(p => p.Alarmas)
        .Load();
    
    // Ara podem accedir a Alarmas
    Console.WriteLine(prot.Alarmas.Count());
}
```

### Carregar Propietats Individuals

Per relacions 1-1, s'usa `Reference` en lloc de `Collection`:

```csharp
using (var ctx = new FuegoContext())
{
    var tono = ctx.Tono.Find(1);
    
    // Carregar l'Alarma relacionada
    ctx.Entry(tono)
        .Reference(t => t.Alarma)
        .Load();
    
    Console.WriteLine(tono.Alarma.Name);
}
```

### Consultes sobre Relacions

Pots fer consultes sobre les relacions sense carregar-les completament:

```csharp
using (var ctx = new FuegoContext())
{
    var prot = ctx.Protocolo.Find(1);
    
    // Comptar sense carregar totes les alarmes
    int numAlarmas = ctx.Entry(prot)
        .Collection(p => p.Alarmas)
        .Query()
        .Count();
    
    // Filtrar la càrrega
    var alarmasUrgents = ctx.Entry(prot)
        .Collection(p => p.Alarmas)
        .Query()
        .Where(a => a.tonos > 3)
        .ToList();
}
```

## 6. Desactivar Lazy Loading Globalment

### Configuració Global

Pots desactivar el Lazy Loading per tot el context:

```csharp
public class FuegoContext : DbContext
{
    public FuegoContext() : base("Fuego")
    {
        // Desactivar Lazy Loading globalment
        this.Configuration.LazyLoadingEnabled = false;
    }
    
    public DbSet<Protocolo> Protocolo { get; set; }
    public DbSet<Alarma> Alarma { get; set; }
}
```

Amb això:
- Les propietats `virtual` deixen de carregar automàticament
- Cal fer Include() sempre
- Més control sobre el rendiment
- Evita consultes inesperades

## 7. Bones Pràctiques

### Quan Usar Lazy Loading (virtual)

✓ **Usa Lazy Loading quan:**
- Treballes amb entitats individuals
- No sempre necessites les relacions
- El context està sempre obert durant l'ús
- Desenvolupament ràpid / prototipatge

```csharp
// Exemple: Mostrar detalls d'un protocol
public void MostrarProtocol(int id)
{
    using (var ctx = new FuegoContext())
    {
        var prot = ctx.Protocolo.Find(id);
        Console.WriteLine(prot.ProtocoloName);
        
        // Només carrega Alarmas si l'usuari ho demana
        if (usuariVolVeureAlarmas)
        {
            foreach (var a in prot.Alarmas)  // Lazy loading aquí
            {
                Console.WriteLine(a.Name);
            }
        }
    }
}
```

### Quan Usar Eager Loading (Include)

✓ **Usa Eager Loading quan:**
- Iteres sobre col·leccions
- Saps que necessitaràs les relacions
- Vols retornar dades fora del context
- Necessites rendiment predictible

```csharp
// Exemple: Llistat de protocols amb les seves alarmes
public List<Protocolo> ObtenirTotsProtocols()
{
    using (var ctx = new FuegoContext())
    {
        // Include per evitar N+1
        return ctx.Protocolo
            .Include(p => p.Alarmas)
            .ToList();
    }  // Context tancat, però les dades estan carregades
}
```

### Quan Usar Explicit Loading

✓ **Usa Explicit Loading quan:**
- Vols control precís sobre què i quan carregar
- Les relacions són opcionals segons la lògica de negoci
- Vols fer consultes filtrades sobre relacions

```csharp
// Exemple: Carregar només alarmes actives
public void ProcessarProtocol(int id)
{
    using (var ctx = new FuegoContext())
    {
        var prot = ctx.Protocolo.Find(id);
        
        // Carregar només alarmes amb més de 2 tonos
        var alarmes = ctx.Entry(prot)
            .Collection(p => p.Alarmas)
            .Query()
            .Where(a => a.tonos > 2)
            .ToList();
    }
}
```

### Checklist de Decisions

1. **Fas un bucle sobre entitats?** → Usa Include (Eager)
2. **Retornes dades fora del context?** → Usa Include (Eager)
3. **No sempre necessites les relacions?** → Usa virtual (Lazy)
4. **Vols màxim control?** → Desactiva Lazy + Explicit Loading
5. **Projecte gran / producció?** → Prefereix Eager (més predictible)
6. **Prototipatge ràpid?** → Lazy Loading (més còmode)

### Errors Comuns a Evitar

❌ **Error 1: Lazy Loading amb context tancat**
```csharp
Protocolo prot;
using (var ctx = new FuegoContext())
{
    prot = ctx.Protocolo.Find(1);
}
// Peta aquí: context tancat
Console.WriteLine(prot.Alarmas.Count());
```

❌ **Error 2: N+1 en bucles sense Include**
```csharp
var prots = ctx.Protocolo.ToList();
foreach (var p in prots)
{
    // Cada iteració fa una consulta extra!
    Console.WriteLine(p.Alarmas.Count());
}
```

❌ **Error 3: Include innecessaris**
```csharp
// Carrega totes les alarmes però no les uses
var prot = ctx.Protocolo
    .Include(p => p.Alarmas)
    .First();
Console.WriteLine(prot.ProtocoloName);  // No usa Alarmas!
```

✓ **Solucions:**
```csharp
// Solució Error 1: Include abans de tancar context
Protocolo prot;
using (var ctx = new FuegoContext())
{
    prot = ctx.Protocolo
        .Include(p => p.Alarmas)
        .First();
}
Console.WriteLine(prot.Alarmas.Count());  // ✓

// Solució Error 2: Include abans del bucle
var prots = ctx.Protocolo
    .Include(p => p.Alarmas)
    .ToList();

// Solució Error 3: No incloure si no es necessita
var prot = ctx.Protocolo.First();
Console.WriteLine(prot.ProtocoloName);
```

## 8. Resum Comparatiu

| Característica | Lazy Loading | Eager Loading | Explicit Loading |
|----------------|--------------|---------------|------------------|
| **Sintaxi** | `virtual` | `Include()` | `Entry().Load()` |
| **Quan carrega** | Al accedir | Amb la consulta | Manual |
| **Context obert** | Requerit | No requerit | Requerit |
| **Consultes BD** | Múltiples | Una sola | Controlades |
| **Problema N+1** | Sí | No | No |
| **Flexibilitat** | Alta | Baixa | Molt alta |
| **Rendiment** | Menys predictible | Predictible | Molt predictible |
| **Ús recomanat** | Dev ràpid | Producció | Control precís |

### Exemple Complet Comparat

```csharp
// LAZY LOADING
public class Protocolo
{
    public virtual ICollection<Alarma> Alarmas { get; set; }
}

using (var ctx = new FuegoContext())
{
    var p = ctx.Protocolo.Find(1);
    Console.WriteLine(p.Alarmas.Count());  // Query automàtic aquí
}

// EAGER LOADING
public class Protocolo
{
    public ICollection<Alarma> Alarmas { get; set; }  // No virtual
}

using (var ctx = new FuegoContext())
{
    var p = ctx.Protocolo
        .Include(x => x.Alarmas)
        .First();
    Console.WriteLine(p.Alarmas.Count());  // Ja carregat
}

// EXPLICIT LOADING
using (var ctx = new FuegoContext())
{
    var p = ctx.Protocolo.Find(1);
    ctx.Entry(p).Collection(x => x.Alarmas).Load();
    Console.WriteLine(p.Alarmas.Count());  // Carregat manualment
}
```

La clau és entendre el trade-off entre comoditat i control, i escollir l'estratègia adequada per cada situació.
